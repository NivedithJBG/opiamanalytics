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
            "CONTEXT TRACKING — very important:\n" .
            "- The conversation history shows prior questions and answers. Use it to understand follow-up questions.\n" .
            "- If the user refers to a project or activity without naming it fully, look back at the conversation history to identify which one they mean.\n" .
            "- If the user's latest question is a follow-up, apply the same project/activity from history.\n\n" .
            "FIELD DEFINITIONS — use these to map user questions to the correct CONTEXT field:\n" .
            "- 'target qty' / 'target quantity' / 'planned quantity' / 'how many piles planned' → use 'Target Qty' from SCHEDULE ACTIVITIES\n" .
            "- 'target production' / 'production target' / 'daily target' / 'planned production rate' → use 'Target Production' from ACTIVITY TASKS (this is productivity × resource_units per day)\n" .
            "- 'actual production' / 'actual productivity' / 'production achieved' → use 'Actual Production' or 'Qty Done' fields\n" .
            "- 'productivity' alone without 'target' or 'actual' → use 'Productivity' field from ACTIVITY TASKS\n" .
            "- 'qty done' / 'work done' / 'completed qty' / 'how many done' / 'progress' → use 'Qty Done' from SCHEDULE ACTIVITIES\n" .
            "- 'progress %' / 'percent complete' → use 'Progress' from SCHEDULE ACTIVITIES or PHYSICAL PROGRESS\n" .
            "- 'planned duration' / 'budgeted duration' → use 'Planned Duration' or 'Budgeted Duration'\n" .
            "- 'cost of work done' / 'value of work done' → use COST OF WORK DONE section\n" .
            "- 'materials received' / 'qty received' / 'purchased' / 'GRN' → use MATERIALS RECEIVED section\n" .
            "- 'purchase order' / 'PO' / 'ordered qty' → use PURCHASE ORDER LINE ITEMS section\n" .
            "- 'work order' / 'WO' → use WORK ORDERS section\n" .
            "- 'measurement book' / 'MB' / 'subcontractor work done' → use MEASUREMENT BOOKS section\n" .
            "- 'resource allocation' / 'estimate resource' → use RESOURCE ALLOCATIONS section\n" .
            "- 'stock at site' / 'store indent' / 'material at site' → use STORE INDENTS section\n\n" .
            "STRICT RULES — violating any rule is forbidden:\n" .
            "1. If the specific answer is not explicitly present in CONTEXT (after checking conversation history for context), reply ONLY with: \"" . self::NO_DATA_REPLY . "\" — nothing else.\n" .
            "2. Never use outside knowledge. Never calculate, estimate, or infer values not in CONTEXT.\n" .
            "3. Never mention percentages, amounts, dates, names, or quantities that are not in CONTEXT.\n" .
            "4. Answer only what was asked — no extra info, no suggestions, no closing remarks, no emojis.\n" .
            "5. One or two sentences maximum unless a list is truly needed.\n" .
            "6. Money values: always show ₹ symbol with 2 decimal places.\n" .
            "7. Quantity values: show with the unit (Ton, m3, Bag, etc.) as given in CONTEXT.\n" .
            "8. NEVER ask the user to 'complete their question' or 'clarify' — always attempt to answer using conversation history for context. Only reply with rule 1's message if the data truly does not exist.\n\n" .
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
                v.Name AS vendor_name,
                p.Name AS project_name
            FROM goods_received_note g
            JOIN resources r ON r.Resource_Id = g.GRN_Item
            JOIN purchase_orders po ON po.order_id = g.GRN_Purchase_Order
            JOIN projects p ON p.Project_Id = po.project_id AND p.Status = 0
            LEFT JOIN vendors v ON v.Vendor_Id = g.GRN_Vendor
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
                    . " | Vendor: {$g['vendor_name']}\n";
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
                po.order_id, po.orderdate, po.ordernumber, po.total_amount,
                v.Name AS vendor_name,
                p.Name AS project_name,
                por.resource_name AS item_name, por.unit AS item_unit,
                por.qnty AS ordered_qty, por.rate, por.amount
            FROM purchase_orders po
            JOIN purchase_order_resources por ON por.order_id = po.order_id
            LEFT JOIN vendors v  ON v.Vendor_Id  = po.vendor_id
            JOIN projects p      ON p.Project_Id = po.project_id AND p.Status = 0
            WHERE po.delete_status = 0
              AND po.project_id IN ($pidList)
            ORDER BY po.orderdate DESC, po.order_id DESC
            LIMIT 500
        ")->queryAll();

        if ($pos) {
            $context .= "=== PURCHASE ORDER LINE ITEMS ===\n";
            foreach ($pos as $po) {
                $context .= "- [{$po['project_name']}] PO#{$po['order_id']} ({$po['ordernumber']})"
                    . " | Date: {$po['orderdate']}"
                    . " | Vendor: {$po['vendor_name']}"
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

        // ================================================================
        // 8. MEASUREMENT BOOKS (Work Orders + MB entries)
        // ================================================================
        $wos = $db->createCommand("
            SELECT wo.WO_Number, wo.Date_requested, wo.Scope, wo.Quantity, wo.Unit,
                   wo.Rate, wo.Total, wo.Duration, wo.start_date,
                   v.Name AS vendor_name, p.Name AS project_name, wo.WO_Subject
            FROM work_order wo
            LEFT JOIN vendors v  ON v.Vendor_Id  = wo.WO_Vendor
            JOIN projects p      ON p.Project_Id = wo.Project_Id AND p.Status = 0
            WHERE wo.Project_Id IN ($pidList)
            ORDER BY wo.Date_requested DESC
            LIMIT 200
        ")->queryAll();

        if ($wos) {
            $context .= "=== WORK ORDERS ===\n";
            foreach ($wos as $wo) {
                // WO_Subject is JSON with activity/task detail — extract summary
                $subject = '';
                $woItems = json_decode($wo['WO_Subject'] ?? '[]', true);
                if (is_array($woItems)) {
                    $names = array_column($woItems, 'activity_name');
                    $subject = implode(', ', array_filter($names));
                }
                $context .= "- [{$wo['project_name']}] {$wo['WO_Number']}"
                    . " | Date: {$wo['Date_requested']}"
                    . " | Vendor: {$wo['vendor_name']}"
                    . " | Scope: {$wo['Scope']}"
                    . ($subject ? " | Activities: {$subject}" : '')
                    . " | Qty: {$wo['Quantity']} {$wo['Unit']}"
                    . " | Rate: ₹{$wo['Rate']}"
                    . " | Total: ₹{$wo['Total']}"
                    . " | Duration: {$wo['Duration']} days"
                    . " | Start: {$wo['start_date']}\n";
            }
            $context .= "\n";
        }

        $mbs = $db->createCommand("
            SELECT mb.mb_number, mb.mb_date, mb.wo_number, mb.entries,
                   p.Name AS project_name
            FROM wo_measurement_book mb
            JOIN projects p ON p.Project_Id = mb.project_id AND p.Status = 0
            WHERE mb.delete_status = 0
              AND mb.sent_status = 1
              AND mb.project_id IN ($pidList)
            ORDER BY mb.mb_date DESC
            LIMIT 200
        ")->queryAll();

        if ($mbs) {
            $context .= "=== MEASUREMENT BOOKS ===\n";
            foreach ($mbs as $mb) {
                $entries = json_decode($mb['entries'] ?? '[]', true) ?: [];
                foreach ($entries as $entry) {
                    $actName = $entry['activity_name'] ?? '';
                    $actUnit = $entry['unit'] ?? '';
                    $actQty  = $entry['qty']  ?? 0;
                    foreach ($entry['tasks'] ?? [] as $task) {
                        $context .= "- [{$mb['project_name']}] {$mb['mb_number']}"
                            . " | WO: {$mb['wo_number']}"
                            . " | Date: {$mb['mb_date']}"
                            . " | Activity: {$actName} ({$actQty} {$actUnit})"
                            . " | Task: {$task['task_name']}"
                            . " | Work Done: {$task['work_done']} {$task['unit']}"
                            . " | Rate: ₹{$task['rate']}"
                            . " | Value: ₹" . number_format((float)$task['work_done'] * (float)$task['rate'], 2) . "\n";
                    }
                }
            }
            $context .= "\n";
        }

        // ================================================================
        // 9. ACTIVITY TASKS (planned tasks per schedule activity)
        // ================================================================
        $tasks = $db->createCommand("
            SELECT
                sa.name AS activity_name,
                at.task_name, at.task_unit,
                stn.task_qty, stn.task_productivity, stn.task_resource_units,
                stn.Budgeted_Duration,
                p.Name AS project_name
            FROM schedule_task_new stn
            JOIN scheduleactivities sa ON sa.id = stn.activity_Id
            JOIN projects p            ON p.Project_Id = sa.projectId AND p.Status = 0
            LEFT JOIN activity_tasks at ON at.id = stn.task_Id
            WHERE sa.projectId IN ($pidList)
              AND sa.status = 0
            ORDER BY p.Name, sa.name, at.task_name
            LIMIT 500
        ")->queryAll();

        if ($tasks) {
            $context .= "=== ACTIVITY TASKS (Planned) ===\n";
            foreach ($tasks as $t) {
                $targetProd = round((float)$t['task_productivity'] * max(1, (float)$t['task_resource_units']), 3);
                $context .= "- [{$t['project_name']}] Activity: {$t['activity_name']}"
                    . " | Task: {$t['task_name']}"
                    . " | Unit: {$t['task_unit']}"
                    . " | Planned Qty: {$t['task_qty']}"
                    . " | Target Production: {$targetProd} {$t['task_unit']}/day"
                    . " | Productivity: {$t['task_productivity']} {$t['task_unit']}/day per unit"
                    . " | Resource Units: {$t['task_resource_units']}"
                    . " | Budgeted Duration: {$t['Budgeted_Duration']} days\n";
            }
            $context .= "\n";
        }

        // ================================================================
        // 10. RESOURCE ALLOCATIONS (estimate resources per activity)
        // ================================================================
        $allocs = $db->createCommand("
            SELECT
                wa.activity_Name AS activity_name,
                r.Name  AS resource_name,
                rt.Name AS resource_type,
                r.Unit,
                pern.quantity, pern.rate,
                pern.quantity * pern.rate AS unit_cost,
                p.Name AS project_name
            FROM pricing_estimate_resources_new pern
            JOIN workgroup_activities_new wa ON wa.id = pern.activity_id
            JOIN projects p                  ON p.Project_Id = pern.project_id AND p.Status = 0
            LEFT JOIN resources r            ON r.Resource_Id = pern.resource_Id
            LEFT JOIN resourcetype rt        ON rt.ResourceType_Id = pern.resourcetype_Id
            WHERE pern.project_id IN ($pidList)
              AND pern.pricing_status = 0
            ORDER BY p.Name, wa.activity_Name, rt.Name, r.Name
            LIMIT 500
        ")->queryAll();

        if ($allocs) {
            $context .= "=== RESOURCE ALLOCATIONS (Estimate) ===\n";
            foreach ($allocs as $a) {
                $context .= "- [{$a['project_name']}] Activity: {$a['activity_name']}"
                    . " | Resource: {$a['resource_name']} ({$a['resource_type']})"
                    . " | Unit: {$a['Unit']}"
                    . " | Qty per Unit: {$a['quantity']}"
                    . " | Rate: ₹" . number_format((float)$a['rate'], 2)
                    . " | Unit Cost: ₹" . number_format((float)$a['unit_cost'], 2) . "\n";
            }
            $context .= "\n";
        }

        // ================================================================
        // 11. STORE INDENTS (material issue from store)
        // ================================================================
        $indents = $db->createCommand("
            SELECT
                si.id, si.raised_at, si.stock_at_site, si.avg_consumption,
                si.resource_name, si.resource_type, si.unit, si.task_name,
                p.Name AS project_name
            FROM store_indents si
            JOIN projects p ON p.Project_Id = si.project_id AND p.Status = 0
            WHERE si.project_id IN ($pidList)
            ORDER BY si.raised_at DESC
            LIMIT 300
        ")->queryAll();

        if ($indents) {
            $context .= "=== STORE INDENTS (Material at Site) ===\n";
            foreach ($indents as $s) {
                $context .= "- [{$s['project_name']}] Material: {$s['resource_name']} ({$s['resource_type']})"
                    . " | Unit: {$s['unit']}"
                    . " | Task: {$s['task_name']}"
                    . " | Stock at Site: {$s['stock_at_site']}"
                    . " | Avg Consumption: {$s['avg_consumption']}"
                    . " | Raised: {$s['raised_at']}\n";
            }
            $context .= "\n";
        }

        return $context;
    }
}
