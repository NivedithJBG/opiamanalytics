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

        $context = $this->buildContext();

        if ($context === '') {
            return ['reply' => self::NO_DATA_REPLY];
        }

        $systemPrompt =
            "You are a read-only data assistant for Opiam Analytics ERP — a construction project management system.\n" .
            "You ONLY answer questions using the exact data in the CONTEXT block below.\n\n" .
            "STRICT RULES — violating any rule is forbidden:\n" .
            "1. If the specific answer is not explicitly present in CONTEXT, reply ONLY with: \"" . self::NO_DATA_REPLY . "\" — nothing else.\n" .
            "2. Never use outside knowledge. Never calculate, estimate, or infer values not in CONTEXT.\n" .
            "3. Never mention percentages, amounts, dates, names, or quantities that are not in CONTEXT.\n" .
            "4. Answer only what was asked — no extra info, no suggestions, no closing remarks, no emojis.\n" .
            "5. One or two sentences maximum unless a list is truly needed.\n" .
            "6. Money values: always show ₹ symbol with 2 decimal places.\n" .
            "7. Quantity values: show with the unit (Ton, m3, Bag, etc.) as given in CONTEXT.\n\n" .
            "CONTEXT:\n" . $context;

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
            $apiErr = $data['error']['message'] ?? $data['type'] ?? substr($response, 0, 300);
            return ['error' => 'API: ' . $apiErr];
        }

        return ['reply' => $data['content'][0]['text']];
    }

    private function buildContext()
    {
        $db      = Yii::$app->db;
        $context = '';

        // ================================================================
        // 1. PROJECTS
        // ================================================================
        $projects = $db->createCommand("
            SELECT Project_Id, Name, client_name, location, start_date, end_date, project_value
            FROM projects
            WHERE Status = 0
            ORDER BY Name ASC
            LIMIT 50
        ")->queryAll();

        if (!$projects) return '';

        $projectIds = array_column($projects, 'Project_Id');
        $pidList    = implode(',', array_map('intval', $projectIds));

        $context .= "=== PROJECTS ===\n";
        foreach ($projects as $p) {
            $context .= "- {$p['Name']} (ID:{$p['Project_Id']})"
                . " | Client: {$p['client_name']}"
                . " | Location: {$p['location']}"
                . " | Start: {$p['start_date']} | End: {$p['end_date']}"
                . " | Contract Value: ₹" . number_format((float)$p['project_value'], 2) . "\n";
        }
        $context .= "\n";

        // ================================================================
        // 2. COST OF WORK DONE (from cache — set by cost dashboard)
        // ================================================================
        $namedParams  = [];
        $placeholders = [];
        foreach ($projectIds as $i => $pid) {
            $key = ':pid' . $i;
            $namedParams[$key] = $pid;
            $placeholders[] = $key;
        }
        $cacheRows = $db->createCommand(
            "SELECT project_id, metric_name, metric_value, updated_at
             FROM chatbot_metrics_cache
             WHERE project_id IN (" . implode(',', $placeholders) . ")",
            $namedParams
        )->queryAll();

        $cache = [];
        foreach ($cacheRows as $r) {
            $cache[$r['project_id']][$r['metric_name']] = [
                'value'      => $r['metric_value'],
                'updated_at' => $r['updated_at'],
            ];
        }
        $cv = function($pid, $metric) use ($cache) {
            return $cache[$pid][$metric]['value'] ?? null;
        };
        $cu = function($pid) use ($cache) {
            return !empty($cache[$pid]) ? reset($cache[$pid])['updated_at'] : null;
        };

        $context .= "=== COST OF WORK DONE ===\n";
        $hasCost = false;
        foreach ($projects as $p) {
            $pid   = $p['Project_Id'];
            $estWd = $cv($pid, 'estimated_cost_work_done');
            $actWd = $cv($pid, 'actual_cost_work_done');
            if ($estWd === null && $actWd === null) continue;
            $estWd    = (float)($estWd ?? 0);
            $actWd    = (float)($actWd ?? 0);
            $variance = $estWd - $actWd;
            $vLabel   = $variance >= 0 ? 'Under Budget' : 'Over Budget';
            $context .= "- {$p['Name']}"
                . " | Estimated Cost of Work Done: ₹" . number_format($estWd, 2)
                . " | Actual Cost of Work Done: ₹" . number_format($actWd, 2)
                . " | Variance: ₹" . number_format(abs($variance), 2) . " ({$vLabel})"
                . " | As of: " . ($cu($pid) ?? 'unknown') . "\n";
            $hasCost = true;
        }
        if (!$hasCost) $context .= "[Cost data not yet cached — open the cost dashboard to populate]\n";
        $context .= "\n";

        // ================================================================
        // 3. PHYSICAL PROGRESS (from cache)
        // ================================================================
        $context .= "=== PHYSICAL PROGRESS ===\n";
        $hasProgress = false;
        foreach ($projects as $p) {
            $pid = $p['Project_Id'];
            $pct = $cv($pid, 'physical_progress_pct');
            if ($pct === null) continue;
            $total     = (int)($cv($pid, 'total_activities') ?? 0);
            $completed = (int)($cv($pid, 'completed_activities') ?? 0);
            $context .= "- {$p['Name']}"
                . " | Physical Progress: {$pct}%"
                . " | Activities: {$completed} completed of {$total} total"
                . " | As of: " . ($cu($pid) ?? 'unknown') . "\n";
            $hasProgress = true;
        }
        if (!$hasProgress) $context .= "[No progress data cached yet]\n";
        $context .= "\n";

        // ================================================================
        // 4. SCHEDULE — all activities with dates and progress
        // ================================================================
        $activities = $db->createCommand("
            SELECT
                sa.name, sa.start_date, sa.end_date,
                sa.old_duration AS planned_duration,
                sa.completed_status, sa.critical_status,
                sa.quantity, sa.unit,
                COALESCE(rpt.cumulated_qty, 0) AS cumulated_qty,
                CASE
                    WHEN sa.quantity > 0 AND COALESCE(rpt.cumulated_qty,0) > 0
                    THEN ROUND(COALESCE(rpt.cumulated_qty,0) / sa.quantity * 100, 1)
                    WHEN sa.completed_status = 1 THEN 100
                    ELSE 0
                END AS progress_pct,
                p.Name AS project_name,
                CASE
                    WHEN sa.completed_status = 0 AND sa.end_date < CURDATE()
                    THEN DATEDIFF(CURDATE(), sa.end_date)
                    ELSE 0
                END AS days_overdue
            FROM scheduleactivities sa
            JOIN projects p ON p.Project_Id = sa.projectId AND p.Status = 0
            LEFT JOIN (
                SELECT activity_id, MAX(cumulated_qty) AS cumulated_qty
                FROM schedule_progress_report
                GROUP BY activity_id
            ) rpt ON rpt.activity_id = sa.id
            WHERE sa.status = 0
            ORDER BY p.Name, sa.start_date
            LIMIT 500
        ")->queryAll();

        if ($activities) {
            $context .= "=== SCHEDULE ACTIVITIES ===\n";
            foreach ($activities as $a) {
                $critical = strtolower($a['critical_status']) === 'yes' ? ' [CRITICAL]' : '';
                $status   = $a['completed_status'] == 1 ? 'Completed' : 'Ongoing';
                $overdue  = $a['days_overdue'] > 0 ? " [DELAYED {$a['days_overdue']} days]" : '';
                $context .= "- [{$a['project_name']}] {$a['name']}{$critical}{$overdue}"
                    . " | Start: {$a['start_date']} | End: {$a['end_date']}"
                    . " | Planned Duration: {$a['planned_duration']}d"
                    . " | Target Qty: {$a['quantity']} {$a['unit']}"
                    . " | Qty Done: {$a['cumulated_qty']} {$a['unit']}"
                    . " | Progress: {$a['progress_pct']}%"
                    . " | Status: {$status}\n";
            }
            $context .= "\n";
        }

        // ================================================================
        // 5. MATERIALS RECEIVED (GRN) — every line item
        // ================================================================
        $grns = $db->createCommand("
            SELECT
                g.GRN_Date, g.grn_number,
                r.Name AS resource_name, r.Unit,
                CAST(g.GRN_Quantity AS DECIMAL(15,4)) AS qty,
                g.GRN_Rate AS rate,
                CAST(g.GRN_Quantity AS DECIMAL(15,4)) * g.GRN_Rate AS value,
                v.Vendor_Name,
                p.Name AS project_name
            FROM goods_received_note g
            JOIN resources r ON r.Resource_Id = g.GRN_Item
            JOIN purchase_orders po ON po.order_id = g.GRN_Purchase_Order
            JOIN projects p ON p.Project_Id = po.project_id AND p.Status = 0
            LEFT JOIN vendor v ON v.Vendor_Id = g.GRN_Vendor
            WHERE g.delete_status = 0
              AND po.delete_status = 0
              AND po.project_id IN ($pidList)
            ORDER BY p.Name, g.GRN_Date DESC
            LIMIT 500
        ")->queryAll();

        if ($grns) {
            $context .= "=== MATERIALS RECEIVED (GRN) ===\n";
            foreach ($grns as $g) {
                $context .= "- [{$g['project_name']}] {$g['resource_name']}"
                    . " | GRN#: {$g['grn_number']}"
                    . " | Date: {$g['GRN_Date']}"
                    . " | Qty: " . number_format((float)$g['qty'], 3) . " {$g['Unit']}"
                    . " | Rate: ₹" . number_format((float)$g['rate'], 2)
                    . " | Value: ₹" . number_format((float)$g['value'], 2)
                    . " | Vendor: {$g['Vendor_Name']}\n";
            }
            $context .= "\n";

            // Also add aggregated summary per material per project
            $context .= "=== MATERIALS RECEIVED — TOTALS PER MATERIAL ===\n";
            $totals = [];
            foreach ($grns as $g) {
                $key = $g['project_name'] . '||' . $g['resource_name'] . '||' . $g['Unit'];
                if (!isset($totals[$key])) {
                    $totals[$key] = ['project' => $g['project_name'], 'name' => $g['resource_name'], 'unit' => $g['Unit'], 'qty' => 0.0, 'value' => 0.0];
                }
                $totals[$key]['qty']   += (float)$g['qty'];
                $totals[$key]['value'] += (float)$g['value'];
            }
            foreach ($totals as $t) {
                $context .= "- [{$t['project']}] {$t['name']}"
                    . " | Total Received: " . number_format($t['qty'], 3) . " {$t['unit']}"
                    . " | Total Value: ₹" . number_format($t['value'], 2) . "\n";
            }
            $context .= "\n";
        } else {
            $context .= "=== MATERIALS RECEIVED (GRN) ===\n[No GRN records found]\n\n";
        }

        // ================================================================
        // 6. PURCHASE ORDERS — line item detail
        // ================================================================
        $pos = $db->createCommand("
            SELECT
                po.order_id, po.order_date, po.total_amount,
                v.Vendor_Name,
                p.Name AS project_name,
                r.Name AS item_name, r.Unit AS item_unit,
                por.quantity AS ordered_qty, por.rate, por.amount
            FROM purchase_orders po
            JOIN purchase_order_resources por ON por.order_id = po.order_id AND por.delete_status = 0
            LEFT JOIN resources r  ON r.Resource_Id  = por.resource_id
            LEFT JOIN vendor v     ON v.Vendor_Id    = po.vendor_id
            JOIN projects p        ON p.Project_Id   = po.project_id AND p.Status = 0
            WHERE po.delete_status = 0
              AND po.project_id IN ($pidList)
            ORDER BY po.order_date DESC, po.order_id DESC
            LIMIT 500
        ")->queryAll();

        if ($pos) {
            $context .= "=== PURCHASE ORDER LINE ITEMS ===\n";
            foreach ($pos as $po) {
                $context .= "- [{$po['project_name']}] PO#{$po['order_id']}"
                    . " | Date: {$po['order_date']}"
                    . " | Vendor: {$po['Vendor_Name']}"
                    . " | Item: {$po['item_name']} ({$po['item_unit']})"
                    . " | Ordered Qty: " . number_format((float)$po['ordered_qty'], 3)
                    . " | Rate: ₹" . number_format((float)$po['rate'], 2)
                    . " | Amount: ₹" . number_format((float)$po['amount'], 2) . "\n";
            }
            $context .= "\n";
        } else {
            $context .= "=== PURCHASE ORDER LINE ITEMS ===\n[No purchase orders found]\n\n";
        }

        // ================================================================
        // 7. DOCUMENTS
        // ================================================================
        $docs = $db->createCommand("
            SELECT pf.original_name, pf.uploaded_at, p.Name AS project_name
            FROM project_files pf
            JOIN projects p ON p.Project_Id = pf.project_id AND p.Status = 0
            WHERE pf.file_type = 'documents'
              AND p.Project_Id IN ($pidList)
            ORDER BY pf.uploaded_at DESC
            LIMIT 100
        ")->queryAll();

        if ($docs) {
            $context .= "=== UPLOADED DOCUMENTS ===\n";
            foreach ($docs as $d) {
                $context .= "- [{$d['project_name']}] {$d['original_name']} | Uploaded: {$d['uploaded_at']}\n";
            }
            $context .= "\n";
        }

        return $context;
    }
}
