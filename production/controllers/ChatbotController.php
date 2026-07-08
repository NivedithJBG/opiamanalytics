<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\ProjuserSelection;

class ChatbotController extends Controller
{
    public $enableCsrfValidation = false;

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

        $systemPrompt = "You are a construction project management assistant for Opiam Analytics ERP. " .
            "You have access to live project data provided below. Answer questions clearly and concisely. " .
            "When showing numbers, format them nicely. If data is not available, say so honestly.\n\n" .
            $context;

        $messages = [];
        foreach ((array)$history as $h) {
            if (!empty($h['role']) && !empty($h['content'])) {
                $messages[] = ['role' => $h['role'], 'content' => $h['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        $payload = json_encode([
            'model'      => 'claude-sonnet-4-6',
            'max_tokens' => 1024,
            'system'     => $systemPrompt,
            'messages'   => $messages,
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

        // All projects summary
        $projects = $db->createCommand("
            SELECT p.Project_Id, p.Name, p.start_date, p.end_date, p.Status,
                   p.client_name, p.location
            FROM projects p
            WHERE p.Status = 0
            ORDER BY p.Name ASC
            LIMIT 50
        ")->queryAll();

        if ($projects) {
            $context .= "=== PROJECTS ===\n";
            foreach ($projects as $p) {
                $context .= "- {$p['Name']} (ID:{$p['Project_Id']}) | Start: {$p['start_date']} | End: {$p['end_date']} | Client: {$p['client_name']} | Location: {$p['location']}\n";
            }
            $context .= "\n";
        }

        // Activities summary per project
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
                $status = $a['completed_status'] == 1 ? 'Completed' : 'Ongoing';
                $critical = $a['critical_status'] == 'yes' ? ' [CRITICAL]' : '';
                $context .= "- [{$a['project_name']}] {$a['name']} | {$a['start_date']} to {$a['end_date']} | {$a['duration']}d | {$status}{$critical}\n";
            }
            $context .= "\n";
        }

        // Cost summary per project (estimated vs actual)
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
        }

        // Documents
        $docs = $db->createCommand("
            SELECT pf.original_name, pf.filename, pf.uploaded_at,
                   p.Name AS project_name
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
                // Extract PDF text if question seems document-related
                if ($this->isDocumentQuery($message)) {
                    $pdfText = $this->extractPdfText($d['filename']);
                    if ($pdfText) {
                        $context .= "  Content preview: " . substr($pdfText, 0, 500) . "\n";
                    }
                }
            }
            $context .= "\n";
        }

        return $context;
    }

    private function isDocumentQuery($message)
    {
        $keywords = ['document', 'file', 'pdf', 'report', 'letter', 'contract', 'invoice', 'drawing'];
        $msg = strtolower($message);
        foreach ($keywords as $kw) {
            if (strpos($msg, $kw) !== false) return true;
        }
        return false;
    }

    private function extractPdfText($filename)
    {
        $path = Yii::getAlias('@webroot') . '/uploads/projects/' . $filename;
        if (!file_exists($path)) return '';

        // Use pdftotext if available, otherwise read raw
        $output = [];
        exec('pdftotext ' . escapeshellarg($path) . ' -', $output, $code);
        if ($code === 0 && $output) {
            return implode(' ', array_slice($output, 0, 50));
        }

        // Fallback: extract readable strings from PDF binary
        $content = file_get_contents($path);
        preg_match_all('/\(([^\)]{3,})\)/', $content, $matches);
        $text = implode(' ', array_slice($matches[1], 0, 100));
        return substr($text, 0, 1000);
    }
}
