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

        // --- Route question to the right data source ---
        $wantsCost     = $this->matches($msg, ['cost', 'budget', 'estimat', 'actual', 'spend', 'amount', 'price', 'rate', 'variance', 'overrun', 'financial', 'expenditure', 'money']);
        $wantsProgress = $this->matches($msg, ['progress', 'physical', 'percent', '%', 'complete', 'done', 'kpi', 'performance', 'how much work', 'work done']);
        $wantsSchedule = $this->matches($msg, ['delay', 'critical', 'overrun', 'behind', 'on time', 'late', 'cpm', 'float', 'schedule', 'gantt', 'when', 'duration', 'start', 'end', 'activit', 'task']);
        $wantsDocs     = $this->matches($msg, ['document', 'file', 'pdf', 'report', 'letter', 'contract', 'invoice', 'drawing', 'upload']);
        $wantsGeneral  = !$wantsCost && !$wantsProgress && !$wantsSchedule && !$wantsDocs;

        // --- Always include project list as base ---
        $projects = $db->createCommand("
            SELECT Project_Id, Name, client_name, location, start_date, end_date, project_value
            FROM projects
            WHERE Status = 0
            ORDER BY Name ASC
            LIMIT 50
        ")->queryAll();

        if ($projects) {
            $context .= "=== PROJECTS ===\n";
            foreach ($projects as $p) {
                $context .= "- {$p['Name']} (ID:{$p['Project_Id']}) | Client: {$p['client_name']} | Location: {$p['location']} | Start: {$p['start_date']} | End: {$p['end_date']}\n";
            }
            $context .= "\n";
        }

        // -------------------------------------------------------------------
        // COST DASHBOARD QUERIES
        // Source: KPI dashboard loop (ProjectsmainController lines 1741-1758)
        // Estimated = SUM(round(pern.rate,2) * round(pern.quantity * pen.activity_qty,2))
        //             for all activities WHERE wa.estimate=1 AND pricing_status=0
        //             NO process_Id filter — matches dashboard exactly
        // Actual    = SUM(GRN_Quantity * po_rate) via GRN + purchase orders
        // -------------------------------------------------------------------
        if ($wantsCost) {
            $costs = $db->createCommand("
                SELECT
                    p.Name AS project_name,
                    COALESCE(est.estimated_cost, 0) AS estimated_cost,
                    COALESCE(act.actual_cost, 0)    AS actual_cost,
                    COALESCE(est.estimated_cost, 0) - COALESCE(act.actual_cost, 0) AS cost_variance
                FROM projects p
                LEFT JOIN (
                    /* Estimated cost: exact formula from KPI dashboard
                       SUM(round(rate,2) * round(quantity * activity_qty, 2))
                       Only estimate=1, pricing_status=0 activities */
                    SELECT wa.project_Id,
                           SUM(ROUND(pern.rate, 2) * ROUND(pern.quantity * pen.activity_qty, 2)) AS estimated_cost
                    FROM workgroup_activities_new wa
                    JOIN pricing_estimate_new pen
                        ON pen.activity_Id = wa.id
                       AND pen.project_Id  = wa.project_Id
                       AND pen.pricing_status = 0
                    JOIN pricing_estimate_resources_new pern
                        ON pern.activity_id = wa.id
                       AND pern.project_id  = wa.project_Id
                       AND pern.pricing_status = 0
                    WHERE wa.estimate = 1
                      AND wa.pricing_status = 0
                    GROUP BY wa.project_Id
                ) est ON est.project_Id = p.Project_Id
                LEFT JOIN (
                    /* Actual cost: GRN_Quantity × PO rate (same as cost dashboard) */
                    SELECT pern.project_id,
                           SUM(g.GRN_Quantity * por.rate) AS actual_cost
                    FROM pricing_estimate_resources_new pern
                    JOIN purchase_order_resources por ON por.allocation_id = pern.pricing_resourceid
                    JOIN goods_received_note g         ON g.GRN_Purchase_Order = por.order_id
                                                      AND g.GRN_Item = pern.resource_Id
                    JOIN purchase_orders po            ON po.order_id = por.order_id
                    WHERE po.delete_status  = 0
                      AND por.delete_status = 0
                    GROUP BY pern.project_id
                ) act ON act.project_id = p.Project_Id
                WHERE p.Status = 0
                ORDER BY p.Name
            ")->queryAll();

            if ($costs) {
                $context .= "=== COST DASHBOARD ===\n";
                foreach ($costs as $c) {
                    $variance_label = $c['cost_variance'] >= 0 ? 'Under Budget' : 'Over Budget';
                    $context .= "- {$c['project_name']}"
                        . " | Estimated: " . number_format($c['estimated_cost'], 2)
                        . " | Actual: "    . number_format($c['actual_cost'], 2)
                        . " | Variance: "  . number_format(abs($c['cost_variance']), 2) . " ({$variance_label})\n";
                }
                $context .= "\n";
            } else {
                $context .= "=== COST DASHBOARD ===\n[NO COST DATA FOUND]\n\n";
            }
        }

        // -------------------------------------------------------------------
        // KPI DASHBOARD — PHYSICAL PROGRESS
        // Source: ProjectsmainController::actionPerformancedashboard() lines 207-241
        // Formula: progress% = SUM(cumulated_qty / quantity) / count(activities) * 100
        //          cumulated_qty from schedule_progress_report_log (SUM of currentqty)
        // -------------------------------------------------------------------
        if ($wantsProgress) {
            $progress = $db->createCommand("
                SELECT
                    p.Name AS project_name,
                    COUNT(sa.id)  AS total_activities,
                    SUM(CASE WHEN sa.completed_status = 1 THEN 1 ELSE 0 END) AS completed_activities,
                    ROUND(
                        SUM(
                            CASE
                                WHEN sa.quantity > 0
                                THEN LEAST(COALESCE(rpt.cumulated_qty, 0) / sa.quantity, 1.0)
                                ELSE (CASE WHEN sa.completed_status = 1 THEN 1.0 ELSE 0.0 END)
                            END
                        ) / NULLIF(COUNT(sa.id), 0) * 100, 1
                    ) AS physical_progress_pct
                FROM projects p
                JOIN scheduleactivities sa ON sa.projectId = p.Project_Id AND sa.status = 0
                LEFT JOIN (
                    SELECT activity_id, SUM(currentqty) AS cumulated_qty
                    FROM schedule_progress_report_log
                    GROUP BY activity_id
                ) rpt ON rpt.activity_id = sa.id
                WHERE p.Status = 0
                GROUP BY p.Project_Id, p.Name
                ORDER BY p.Name
            ")->queryAll();

            if ($progress) {
                $context .= "=== KPI — PHYSICAL PROGRESS ===\n";
                foreach ($progress as $r) {
                    $context .= "- {$r['project_name']}"
                        . " | Physical Progress: {$r['physical_progress_pct']}%"
                        . " | Completed: {$r['completed_activities']} of {$r['total_activities']} activities\n";
                }
                $context .= "\n";
            } else {
                $context .= "=== KPI — PHYSICAL PROGRESS ===\n[NO PROGRESS DATA FOUND]\n\n";
            }
        }

        // -------------------------------------------------------------------
        // GANTT / SCHEDULE STATUS — CRITICAL PATH & DELAY
        // Source: ProjectsmainController::actionPerformancedashboard() lines 421-463
        // Formula: delay = projected_end (planned_end + overrun) - planned_end
        //          overrun = (elapsed_days / cumulated_qty) * quantity - old_duration
        //          Project delay uses critical_status = 'Yes' activities only
        // -------------------------------------------------------------------
        if ($wantsSchedule) {
            // Activity list with critical status and progress
            $activities = $db->createCommand("
                SELECT
                    sa.name, sa.start_date, sa.end_date, sa.old_duration AS planned_duration,
                    sa.completed_status, sa.critical_status, sa.quantity, sa.unit,
                    COALESCE(rpt.cumulated_qty, 0) AS reported_qty,
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

            // Project-level delay (critical path only)
            $delays = $db->createCommand("
                SELECT
                    p.Name AS project_name,
                    COUNT(sa.id) AS critical_count,
                    MAX(sa.end_date) AS planned_end,
                    SUM(CASE
                        WHEN sa.completed_status = 0
                         AND sa.end_date < CURDATE()
                         AND COALESCE(rpt.cumulated_qty, 0) < sa.quantity
                        THEN 1 ELSE 0
                    END) AS delayed_count,
                    MAX(CASE
                        WHEN sa.completed_status = 0 AND sa.end_date < CURDATE()
                        THEN DATEDIFF(CURDATE(), sa.end_date) ELSE 0
                    END) AS max_delay_days
                FROM projects p
                JOIN scheduleactivities sa ON sa.projectId = p.Project_Id
                    AND sa.status = 0 AND sa.critical_status = 'Yes'
                LEFT JOIN (
                    SELECT activity_id, SUM(currentqty) AS cumulated_qty
                    FROM schedule_progress_report_log
                    GROUP BY activity_id
                ) rpt ON rpt.activity_id = sa.id
                WHERE p.Status = 0
                GROUP BY p.Project_Id, p.Name
                ORDER BY p.Name
            ")->queryAll();

            if ($delays) {
                $context .= "=== SCHEDULE STATUS (Critical Path) ===\n";
                foreach ($delays as $r) {
                    $status = $r['delayed_count'] > 0
                        ? "DELAYED — {$r['delayed_count']} critical activities overdue by up to {$r['max_delay_days']} days"
                        : "On Schedule";
                    $context .= "- {$r['project_name']}"
                        . " | Critical Activities: {$r['critical_count']}"
                        . " | Planned End: {$r['planned_end']}"
                        . " | Status: {$status}\n";
                }
                $context .= "\n";
            }

            if ($activities) {
                $context .= "=== ACTIVITIES (Gantt) ===\n";
                foreach ($activities as $a) {
                    $critical = (strtolower($a['critical_status']) === 'yes') ? ' [CRITICAL]' : '';
                    $status   = $a['completed_status'] == 1 ? 'Completed' : 'Ongoing';
                    $context .= "- [{$a['project_name']}] {$a['name']}{$critical}"
                        . " | {$a['start_date']} to {$a['end_date']}"
                        . " | Duration: {$a['planned_duration']}d"
                        . " | Progress: {$a['progress_pct']}%"
                        . " | {$status}\n";
                }
                $context .= "\n";
            } else {
                $context .= "=== ACTIVITIES (Gantt) ===\n[NO RECORDS FOUND]\n\n";
            }
        }

        // -------------------------------------------------------------------
        // DOCUMENTS
        // -------------------------------------------------------------------
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

        // -------------------------------------------------------------------
        // GENERAL DB DATA (procurement, vendors, resources, etc.)
        // -------------------------------------------------------------------
        if ($wantsGeneral) {
            // Purchase orders summary
            if ($this->matches($msg, ['purchase', 'order', 'po', 'vendor', 'supplier', 'procurement'])) {
                $pos = $db->createCommand("
                    SELECT po.order_id, po.order_date, po.total_amount,
                           v.Vendor_Name, p.Name AS project_name
                    FROM purchase_orders po
                    LEFT JOIN vendor v ON v.Vendor_Id = po.vendor_id
                    LEFT JOIN projects p ON p.Project_Id = po.project_id
                    WHERE po.delete_status = 0
                    ORDER BY po.order_date DESC
                    LIMIT 50
                ")->queryAll();

                if ($pos) {
                    $context .= "=== PURCHASE ORDERS ===\n";
                    foreach ($pos as $po) {
                        $context .= "- PO#{$po['order_id']} | {$po['project_name']} | Vendor: {$po['Vendor_Name']} | Date: {$po['order_date']} | Amount: " . number_format($po['total_amount'], 2) . "\n";
                    }
                    $context .= "\n";
                }
            }

            // Resources
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
