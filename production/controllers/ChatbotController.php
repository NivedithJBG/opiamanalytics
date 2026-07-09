<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

class ChatbotController extends Controller
{
    public $enableCsrfValidation = false;

    const NO_DATA_REPLY = 'That information is not available in the system.';

    public function behaviors()
    {
        return [];
    }

    private function getApiKey()
    {
        $secrets = @include(Yii::getAlias('@app') . '/config/secrets.php');
        return is_array($secrets) ? ($secrets['anthropicApiKey'] ?? '') : '';
    }

    public function actionChat()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $message = trim(Yii::$app->request->post('message', ''));
        $history = Yii::$app->request->post('history', []);
        if (!$message) return ['error' => 'No message'];

        $context = $this->buildContext($message);

        // If no data found at all, skip the API call entirely
        if ($context === '') {
            return ['reply' => self::NO_DATA_REPLY];
        }

        $systemPrompt =
            "You are a data assistant for Opiam Analytics ERP.\n" .
            "You ONLY answer using the CONTEXT block below. Rules:\n" .
            "- If the answer is not present in the CONTEXT, reply exactly: \"" . self::NO_DATA_REPLY . "\"\n" .
            "- Never use outside knowledge, never guess, never infer beyond what is written.\n" .
            "- Answer only what was asked. No extra information, no suggestions, no closing remarks, no emojis.\n" .
            "- Be direct. One or two sentences is enough if that answers the question.\n\n" .
            "CONTEXT:\n" . $context;

        // Build messages array from history + current message
        // History comes from the frontend and contains all prior turns
        $messages = [];
        foreach ((array)$history as $h) {
            if (!empty($h['role']) && !empty($h['content'])) {
                $messages[] = ['role' => $h['role'], 'content' => (string)$h['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        $payload = json_encode([
            'model'       => 'claude-sonnet-4-6',
            'max_tokens'  => 512,
            'temperature' => 0.1,
            'system'      => $systemPrompt,
            'messages'    => $messages,
        ]);

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'x-api-key: ' . $this->getApiKey(),
                'anthropic-version: 2023-06-01',
                'content-type: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err) return ['error' => 'API error: ' . $err];

        $data = json_decode($response, true);
        if (!isset($data['content'][0]['text'])) {
            return ['error' => 'Unexpected API response'];
        }

        return ['reply' => $data['content'][0]['text']];
    }

    private function buildContext($message)
    {
        $db = Yii::$app->db;
        $context = '';
        $msg = strtolower($message);

        // Keywords to decide what sections to fetch
        $wantsActivities = $this->matches($msg, ['activit', 'task', 'schedule', 'work', 'critical', 'progress', 'ongoing', 'complete', 'duration', 'start', 'end', 'delay']);
        $wantsCosts      = $this->matches($msg, ['cost', 'budget', 'estimate', 'actual', 'spend', 'amount', 'value', 'rate', 'price']);
        $wantsDocs       = $this->matches($msg, ['document', 'file', 'pdf', 'report', 'letter', 'contract', 'invoice', 'drawing', 'upload']);
        $wantsProjects   = $this->matches($msg, ['project', 'client', 'location', 'site', 'status', 'when', 'where', 'who', 'how many']);

        // Always load projects as base context
        $projects = $db->createCommand("
            SELECT p.Project_Id, p.Name, p.start_date, p.end_date, p.Status,
                   p.client_name, p.location, p.project_value
            FROM projects p
            WHERE p.Status = 0
            ORDER BY p.Name ASC
            LIMIT 50
        ")->queryAll();

        if ($projects) {
            $context .= "=== PROJECTS ===\n";
            foreach ($projects as $p) {
                $context .= "- {$p['Name']} (ID:{$p['Project_Id']}) | Client: {$p['client_name']} | Location: {$p['location']} | Start: {$p['start_date']} | End: {$p['end_date']} | Value: {$p['project_value']}\n";
            }
            $context .= "\n";
        }

        if ($wantsActivities || (!$wantsCosts && !$wantsDocs && !$wantsProjects)) {
            $activities = $db->createCommand("
                SELECT sa.name, sa.start_date, sa.end_date, sa.duration,
                       sa.completed_status, sa.critical_status,
                       p.Name AS project_name
                FROM scheduleactivities sa
                JOIN projects p ON p.Project_Id = sa.projectId
                WHERE sa.status = 0
                ORDER BY p.Name, sa.start_date
                LIMIT 200
            ")->queryAll();

            if ($activities) {
                $context .= "=== ACTIVITIES ===\n";
                foreach ($activities as $a) {
                    $status   = $a['completed_status'] == 1 ? 'Completed' : 'Ongoing';
                    $critical = $a['critical_status'] == 'yes' ? ' [CRITICAL]' : '';
                    $context .= "- [{$a['project_name']}] {$a['name']} | {$a['start_date']} to {$a['end_date']} | {$a['duration']}d | {$status}{$critical}\n";
                }
                $context .= "\n";
            } else {
                $context .= "=== ACTIVITIES ===\n[NO RECORDS FOUND]\n\n";
            }
        }

        if ($wantsCosts) {
            $costs = $db->createCommand("
                SELECT p.Name AS project_name,
                       COALESCE(SUM(pen.specific_rate * pen.activity_qty), 0) AS estimated_cost,
                       COALESCE(SUM(pern.actual_amount), 0) AS actual_cost
                FROM projects p
                LEFT JOIN pricing_estimate_new pen ON pen.project_Id = p.Project_Id
                LEFT JOIN pricing_estimate_resources_new pern ON pern.project_id = p.Project_Id
                WHERE p.Status = 0
                GROUP BY p.Project_Id, p.Name
                ORDER BY p.Name
                LIMIT 50
            ")->queryAll();

            if ($costs) {
                $context .= "=== COST SUMMARY ===\n";
                foreach ($costs as $c) {
                    $context .= "- {$c['project_name']} | Estimated: {$c['estimated_cost']} | Actual: {$c['actual_cost']}\n";
                }
                $context .= "\n";
            } else {
                $context .= "=== COST SUMMARY ===\n[NO RECORDS FOUND]\n\n";
            }
        }

        if ($wantsDocs) {
            $docs = $db->createCommand("
                SELECT pf.original_name, pf.uploaded_at, p.Name AS project_name
                FROM project_files pf
                LEFT JOIN projects p ON p.Project_Id = pf.project_id
                WHERE pf.file_type = 'documents'
                ORDER BY pf.uploaded_at DESC
                LIMIT 50
            ")->queryAll();

            if ($docs) {
                $context .= "=== UPLOADED DOCUMENTS ===\n";
                foreach ($docs as $d) {
                    $context .= "- {$d['original_name']} | Project: {$d['project_name']} | Uploaded: {$d['uploaded_at']}\n";
                }
                $context .= "\n";
            } else {
                $context .= "=== UPLOADED DOCUMENTS ===\n[NO RECORDS FOUND]\n\n";
            }
        }

        return $context;
    }

    private function matches($msg, array $keywords)
    {
        foreach ($keywords as $kw) {
            if (strpos($msg, $kw) !== false) return true;
        }
        return false;
    }
}
