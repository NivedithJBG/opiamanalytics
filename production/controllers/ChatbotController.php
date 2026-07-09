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

        if ($context === '') {
            return ['reply' => self::NO_DATA_REPLY];
        }

        $systemPrompt =
            "You are a read-only data assistant for Opiam Analytics ERP.\n" .
            "You ONLY answer questions using the exact data in the CONTEXT block below.\n\n" .
            "STRICT RULES — violating any rule is forbidden:\n" .
            "1. If the specific answer is not explicitly present in CONTEXT, reply ONLY with: \"" . self::NO_DATA_REPLY . "\" — nothing else.\n" .
            "2. Never use outside knowledge. Never calculate, estimate, or infer values not in CONTEXT.\n" .
            "3. Never mention percentages, amounts, dates, or names that are not in CONTEXT.\n" .
            "4. Answer only what was asked — no extra info, no suggestions, no closing remarks, no emojis.\n" .
            "5. One or two sentences maximum unless a list is truly needed.\n" .
            "6. Money values: show with 2 decimal places and currency symbol (₹ or as provided).\n\n" .
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

    private function buildContext($message)
    {
        $db  = Yii::$app->db;
        $msg = strtolower($message);
        $context = '';

        $wantsProcure  = $this->matches($msg, ['purchas', 'procure', 'grn', 'received', 'material', 'quantity', 'qty', 'bought', 'buy', 'order', 'po', 'vendor', 'supplier', 'invoice', 'reinforcement', 'cement', 'steel', 'fe ', 'tmt', 'how much', 'ton', 'kg', 'litre', 'bag', 'nos']);
        $wantsCost     = $this->matches($msg, ['cost', 'budget', 'estimat', 'actual', 'spend', 'amount', 'price', 'rate', 'variance', 'overrun', 'financial', 'expenditure', 'money', 'work done', 'total cost', 'spent', 'paid']);
        $wantsProgress = $this->matches($msg, ['progress', 'physical', 'percent', '%', 'complete', 'done', 'kpi', 'performance', 'how much work']);
        $wantsSchedule = $this->matches($msg, ['delay', 'critical', 'behind', 'on time', 'late', 'schedule', 'gantt', 'when', 'duration', 'start', 'end', 'activit', 'task', 'float']);
        $wantsDocs     = $this->matches($msg, ['document', 'file', 'pdf', 'report', 'letter', 'contract', 'invoice', 'drawing', 'upload']);
        $wantsGeneral  = !$wantsCost && !$wantsProgress && !$wantsSchedule && !$wantsDocs && !$wantsProcure;

        // --- Project list with cached metrics ---
        $projects = $db->createCommand("
            SELECT Project_Id, Name, client_name, location, start_date, end_date, project_value
            FROM projects
            WHERE Status = 0
            ORDER BY Name ASC
            LIMIT 50
        ")->queryAll();

        if (!$projects) return '';

        // Load all cached metrics for all projects in one query
        $projectIds = array_column($projects, 'Project_Id');
        $namedParams = [];
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

        // Index cache by project_id => metric_name => {value, updated_at}
        $cache = [];
        foreach ($cacheRows as $r) {
            $cache[$r['project_id']][$r['metric_name']] = [
                'value'      => $r['metric_value'],
                'updated_at' => $r['updated_at'],
            ];
        }

        // Helper: get cached value or null
        $cv = function($pid, $metric) use ($cache) {
            return isset($cache[$pid][$metric]) ? $cache[$pid][$metric]['value'] : null;
        };
        $cu = function($pid) use ($cache) {
            // updated_at of any metric for this project
            if (!empty($cache[$pid])) {
                return reset($cache[$pid])['updated_at'];
            }
            return null;
        };

        // --- PROJECTS section ---
        $context .= "=== PROJECTS ===\n";
        foreach ($projects as $p) {
            $context .= "- {$p['Name']} (ID:{$p['Project_Id']}) | Client: {$p['client_name']} | Location: {$p['location']} | Start: {$p['start_date']} | End: {$p['end_date']}\n";
        }
        $context .= "\n";

        // --- COST (from cache) ---
        if ($wantsCost) {
            $hasCost = false;
            $costBlock = "=== COST OF WORK DONE (from cost dashboard) ===\n";
            foreach ($projects as $p) {
                $pid = $p['Project_Id'];
                // Use the cost-dashboard computed values (actual_cost_work_done set by actionCostdashboardbatch)
                $estWd  = $cv($pid, 'estimated_cost_work_done');
                $actWd  = $cv($pid, 'actual_cost_work_done');
                if ($estWd === null && $actWd === null) continue;
                $estWd  = (float)($estWd ?? 0);
                $actWd  = (float)($actWd ?? 0);
                $variance = $estWd - $actWd;
                $vLabel   = $variance >= 0 ? 'Under Budget' : 'Over Budget';
                $updated  = $cu($pid) ?? 'unknown';
                $costBlock .= "- {$p['Name']}"
                    . " | Estimated Cost of Work Done: ₹" . number_format($estWd, 2)
                    . " | Actual Cost of Work Done: ₹" . number_format($actWd, 2)
                    . " | Budget Variance: ₹" . number_format(abs($variance), 2) . " ({$vLabel})"
                    . " | As of: {$updated}\n";
                $hasCost = true;
            }
            $context .= $hasCost ? $costBlock . "\n" : "=== COST OF WORK DONE ===\n[NO COST DATA IN CACHE — OPEN THE COST DASHBOARD FOR THIS PROJECT FIRST]\n\n";
        }

        // --- PROGRESS (from cache) ---
        if ($wantsProgress) {
            $hasProgress = false;
            $progBlock = "=== KPI — PHYSICAL PROGRESS ===\n";
            foreach ($projects as $p) {
                $pid  = $p['Project_Id'];
                $pct  = $cv($pid, 'physical_progress_pct');
                if ($pct === null) continue;
                $total     = (int)($cv($pid, 'total_activities') ?? 0);
                $completed = (int)($cv($pid, 'completed_activities') ?? 0);
                $updated   = $cu($pid) ?? 'unknown';
                $progBlock .= "- {$p['Name']}"
                    . " | Physical Progress: {$pct}%"
                    . " | Completed: {$completed} of {$total} activities"
                    . " | As of: {$updated}\n";
                $hasProgress = true;
            }
            $context .= $hasProgress ? $progBlock . "\n" : "=== KPI — PHYSICAL PROGRESS ===\n[NO PROGRESS DATA IN CACHE]\n\n";
        }

        // --- SCHEDULE (cache for summary + live query for activity list) ---
        if ($wantsSchedule) {
            $hasDelay = false;
            $schedBlock = "=== SCHEDULE STATUS (Critical Path) ===\n";
            foreach ($projects as $p) {
                $pid      = $p['Project_Id'];
                $critical = $cv($pid, 'critical_count');
                if ($critical === null) continue;
                $delayed  = (int)($cv($pid, 'delayed_critical') ?? 0);
                $maxDays  = (int)($cv($pid, 'max_delay_days')   ?? 0);
                $endTs    = (int)($cv($pid, 'planned_end_date') ?? 0);
                $endStr   = $endTs ? date('Y-m-d', $endTs) : 'N/A';
                $updated  = $cu($pid) ?? 'unknown';
                $status   = $delayed > 0
                    ? "DELAYED — {$delayed} critical activities overdue by up to {$maxDays} days"
                    : "On Schedule";
                $schedBlock .= "- {$p['Name']}"
                    . " | Critical Activities: {$critical}"
                    . " | Planned End: {$endStr}"
                    . " | Status: {$status}"
                    . " | As of: {$updated}\n";
                $hasDelay = true;
            }
            $context .= $hasDelay ? $schedBlock . "\n" : "=== SCHEDULE STATUS ===\n[NO SCHEDULE DATA IN CACHE]\n\n";

            // Live activity list (small, fast query — only name/dates/progress)
            $activities = $db->createCommand("
                SELECT
                    sa.name, sa.start_date, sa.end_date, sa.old_duration AS planned_duration,
                    sa.completed_status, sa.critical_status,
                    CASE
                        WHEN sa.quantity > 0 AND COALESCE(rpt.cumulated_qty,0) > 0
                        THEN ROUND(COALESCE(rpt.cumulated_qty,0) / sa.quantity * 100, 1)
                        WHEN sa.completed_status = 1 THEN 100
                        ELSE 0
                    END AS progress_pct,
                    p.Name AS project_name
                FROM scheduleactivities sa
                JOIN projects p ON p.Project_Id = sa.projectId AND p.Status = 0
                LEFT JOIN (
                    SELECT activity_id, SUM(currentqty) AS cumulated_qty
                    FROM schedule_progress_report_log
                    GROUP BY activity_id
                ) rpt ON rpt.activity_id = sa.id
                WHERE sa.status = 0
                ORDER BY p.Name, (sa.critical_status = 'Yes') DESC, sa.start_date
                LIMIT 300
            ")->queryAll();

            if ($activities) {
                $context .= "=== ACTIVITIES (Gantt) ===\n";
                foreach ($activities as $a) {
                    $critical = strtolower($a['critical_status']) === 'yes' ? ' [CRITICAL]' : '';
                    $status   = $a['completed_status'] == 1 ? 'Completed' : 'Ongoing';
                    $context .= "- [{$a['project_name']}] {$a['name']}{$critical}"
                        . " | {$a['start_date']} to {$a['end_date']}"
                        . " | Duration: {$a['planned_duration']}d"
                        . " | Progress: {$a['progress_pct']}%"
                        . " | {$status}\n";
                }
                $context .= "\n";
            }
        }

        // --- DOCUMENTS ---
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

        // --- PROCUREMENT (GRN received quantities, PO orders, vendors) ---
        if ($wantsProcure) {
            // GRN — materials received, grouped by resource and project
            $grns = $db->createCommand("
                SELECT r.Name AS resource_name, r.Unit,
                       p.Name AS project_name,
                       SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))) AS total_qty,
                       SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4)) * g.GRN_Rate) AS total_value,
                       MAX(g.GRN_Date) AS last_received
                FROM goods_received_note g
                JOIN resources r ON r.Resource_Id = g.GRN_Item
                JOIN purchase_orders po ON po.order_id = g.GRN_Purchase_Order
                JOIN projects p ON p.Project_Id = po.project_id
                WHERE g.delete_status = 0
                  AND po.delete_status = 0
                GROUP BY r.Resource_Id, r.Name, r.Unit, p.Project_Id, p.Name
                ORDER BY p.Name, r.Name
                LIMIT 200
            ")->queryAll();

            if ($grns) {
                $context .= "=== MATERIALS RECEIVED (GRN) ===\n";
                foreach ($grns as $g) {
                    $context .= "- {$g['resource_name']} ({$g['Unit']})"
                        . " | Project: {$g['project_name']}"
                        . " | Qty Received: " . number_format((float)$g['total_qty'], 3)
                        . " {$g['Unit']}"
                        . " | Total Value: ₹" . number_format((float)$g['total_value'], 2)
                        . " | Last Received: {$g['last_received']}\n";
                }
                $context .= "\n";
            } else {
                $context .= "=== MATERIALS RECEIVED (GRN) ===\n[NO GRN RECORDS FOUND]\n\n";
            }

            // Purchase Orders — with vendor and line items
            $pos = $db->createCommand("
                SELECT po.order_id, po.order_date, po.total_amount,
                       v.Vendor_Name, p.Name AS project_name,
                       r.Name AS item_name, r.Unit AS item_unit,
                       por.quantity AS ordered_qty, por.rate, por.amount
                FROM purchase_orders po
                JOIN purchase_order_resources por ON por.order_id = po.order_id AND por.delete_status = 0
                LEFT JOIN resources r ON r.Resource_Id = por.resource_id
                LEFT JOIN vendor v ON v.Vendor_Id = po.vendor_id
                LEFT JOIN projects p ON p.Project_Id = po.project_id
                WHERE po.delete_status = 0
                ORDER BY po.order_date DESC, po.order_id DESC
                LIMIT 300
            ")->queryAll();

            if ($pos) {
                $context .= "=== PURCHASE ORDER LINE ITEMS ===\n";
                foreach ($pos as $po) {
                    $context .= "- PO#{$po['order_id']} | {$po['project_name']} | Vendor: {$po['Vendor_Name']}"
                        . " | Date: {$po['order_date']}"
                        . " | Item: {$po['item_name']} ({$po['item_unit']})"
                        . " | Ordered Qty: " . number_format((float)$po['ordered_qty'], 3)
                        . " | Rate: ₹" . number_format((float)$po['rate'], 2)
                        . " | Amount: ₹" . number_format((float)$po['amount'], 2) . "\n";
                }
                $context .= "\n";
            }
        }

        // --- GENERAL (resources list) ---
        if ($wantsGeneral) {
            if ($this->matches($msg, ['resource', 'equipment', 'labour', 'labor', 'manpower', 'worker', 'machine'])) {
                $resources = $db->createCommand("
                    SELECT r.Name, rt.Name AS type, r.Unit
                    FROM resources r
                    LEFT JOIN resourcetype rt ON rt.Resourcetype_Id = r.Resourcetype_Id
                    WHERE r.Status = 0
                    ORDER BY rt.Name, r.Name
                    LIMIT 100
                ")->queryAll();

                if ($resources) {
                    $context .= "=== RESOURCES ===\n";
                    foreach ($resources as $r) {
                        $context .= "- {$r['Name']} | Type: {$r['type']} | Unit: {$r['Unit']}\n";
                    }
                    $context .= "\n";
                }
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
