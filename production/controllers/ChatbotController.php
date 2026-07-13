<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

/**
 * ChatbotController — Dashboard Chatbot v4
 *
 * Architecture: one tool per dashboard panel, each running the exact same
 * queries as the live cost dashboard. No cache tables. Always live data.
 *
 * Core computation functions:
 *   computeActivityCosts()     — mirrors actionCostdashboardbatch()
 *   computeActivityResources() — mirrors actionCostdashboardactivity()
 *
 * Panel tools (direct DB → answer, no intermediate tables):
 *   get_project_costs          — project-level est vs actual
 *   get_group_costs            — cost by group
 *   get_iow_costs              — cost by IOW
 *   get_activity_costs         — cost by activity (the table)
 *   get_activity_unit_cost     — unit cost of one activity
 *   get_resource_unit_cost     — est vs actual unit cost per resource
 *   get_resource_consumption   — planned vs actual consumption per resource
 *   get_resource_cost_by_type  — cost grouped by resource type
 */
class ChatbotController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors() { return []; }

    // -----------------------------------------------------------------------
    // API key
    // -----------------------------------------------------------------------
    private function getApiKey()
    {
        $secrets = @include(Yii::getAlias('@app') . '/config/secrets.php');
        return is_array($secrets) ? ($secrets['anthropicApiKey'] ?? '') : '';
    }

    // -----------------------------------------------------------------------
    // Logging
    // -----------------------------------------------------------------------
    private function logToolCall($sessionId, $toolName, $toolInput, $toolOutput)
    {
        try {
            Yii::$app->db->createCommand()->insert('chatbot_tool_logs', [
                'session_id'  => $sessionId,
                'tool_name'   => $toolName,
                'tool_input'  => json_encode($toolInput,  JSON_UNESCAPED_UNICODE),
                'tool_output' => json_encode($toolOutput, JSON_UNESCAPED_UNICODE),
            ])->execute();
        } catch (\Exception $e) {}
    }

    private function updateLogReply($sessionId, $reply)
    {
        try {
            Yii::$app->db->createCommand(
                "UPDATE chatbot_tool_logs SET claude_reply = :r WHERE session_id = :s AND claude_reply IS NULL ORDER BY id DESC LIMIT 1",
                [':r' => $reply, ':s' => $sessionId]
            )->execute();
        } catch (\Exception $e) {}
    }

    // -----------------------------------------------------------------------
    // SELECT-only validator
    // -----------------------------------------------------------------------
    private function validateSelectOnly($sql)
    {
        $normalized = preg_replace('/\s+/', ' ', trim($sql));
        if (!preg_match('/^SELECT\s/i', $normalized)) {
            return 'Only SELECT queries are permitted.';
        }
        $blocked = ['INSERT','UPDATE','DELETE','DROP','TRUNCATE','ALTER','CREATE',
                    'REPLACE','CALL','EXEC','GRANT','REVOKE','LOAD','OUTFILE','DUMPFILE'];
        foreach ($blocked as $kw) {
            if (preg_match('/\b' . $kw . '\b/i', $normalized)) {
                return "Keyword '{$kw}' is not allowed in a read-only query.";
            }
        }
        return null;
    }

    // -----------------------------------------------------------------------
    // Resolve project by partial name
    // -----------------------------------------------------------------------
    private function resolveProject($db, $projectName)
    {
        if (trim($projectName) === '') {
            return ['status' => 'no_data', 'reason' => 'No project name provided.'];
        }
        $row = $db->createCommand(
            "SELECT Project_Id, Name, client_name, location, project_value
             FROM projects WHERE Status = 0 AND LOWER(Name) LIKE :n LIMIT 1",
            [':n' => '%' . strtolower(trim($projectName)) . '%']
        )->queryOne();

        if (!$row) {
            return ['status' => 'no_data', 'reason' => "No active project found matching \"{$projectName}\"."];
        }
        return $row;
    }

    // -----------------------------------------------------------------------
    // Fuzzy activity name matcher — returns scheduleactivities rows
    // -----------------------------------------------------------------------
    private function resolveActivity($db, $pid, $activityName)
    {
        $raw    = preg_replace('/[^a-z0-9 ]/i', ' ', $activityName);
        $words  = array_values(array_filter(explode(' ', strtolower($raw)), fn($w) => strlen($w) >= 3));
        $where  = 'projectId = :pid AND status = 0';
        $params = [':pid' => $pid];

        if (!empty($words)) {
            foreach ($words as $i => $w) {
                $key = ':w' . $i;
                $where .= " AND LOWER(name) LIKE {$key}";
                $params[$key] = '%' . rtrim($w, 's') . '%';
            }
        } else {
            $where .= ' AND LOWER(name) LIKE :wfull';
            $params[':wfull'] = '%' . strtolower($activityName) . '%';
        }

        return $db->createCommand(
            "SELECT id, name, activity_id AS wan_id, quantity AS sched_qty, unit
             FROM scheduleactivities WHERE {$where} ORDER BY name LIMIT 10",
            $params
        )->queryAll();
    }

    // =======================================================================
    // CORE COMPUTATION 1 — mirrors actionCostdashboardbatch()
    // Returns array keyed by scheduleactivities.id with full per-activity costs
    // and per-resource detail captured in a single pass.
    // =======================================================================
    private function computeActivityCosts($db, $pid)
    {
        $schedActs = $db->createCommand(
            "SELECT sa.id, sa.activity_id, sa.quantity, sa.name
             FROM scheduleactivities sa WHERE sa.projectId=:pid AND sa.status=0",
            [':pid' => $pid]
        )->queryAll();

        if (empty($schedActs)) return [];

        $actIds = array_column($schedActs, 'id');
        $wbIds  = array_unique(array_filter(array_map('intval', array_column($schedActs, 'activity_id'))));

        if (empty($actIds) || empty($wbIds)) return [];

        $actIdsStr = implode(',', array_map('intval', $actIds));
        $wbIdsStr  = implode(',', $wbIds);

        // Last reported qty per schedule activity
        $lqRows = $db->createCommand(
            "SELECT activity_id, MAX(cumulated_qty) AS lq
             FROM schedule_progress_report
             WHERE activity_id IN ({$actIdsStr}) GROUP BY activity_id"
        )->queryAll();
        $lastQtyMap = [];
        foreach ($lqRows as $r) { $lastQtyMap[(int)$r['activity_id']] = (float)$r['lq']; }

        // Estimate qty per WBS id
        $penRows = $db->createCommand(
            "SELECT activity_Id, activity_qty FROM pricing_estimate_new
             WHERE activity_Id IN ({$wbIdsStr}) AND project_Id=:pid AND pricing_status=0",
            [':pid' => $pid]
        )->queryAll();
        $estQtyMap = [];
        foreach ($penRows as $r) { $estQtyMap[(int)$r['activity_Id']] = (float)$r['activity_qty']; }

        // Resources per WBS id with GRN weighted avg and store indent data
        $resRows = $db->createCommand(
            "SELECT pern.activity_id, pern.pricing_resourceid, pern.resourcetype_Id AS type_id,
                    pern.rate, pern.quantity AS res_qty, pern.task_ids, pern.resource_Id,
                    COALESCE(pern.display_name, r.Name, rt.Name) AS resource_name,
                    COALESCE(r.Unit, '') AS resource_unit,
                    rt.Name AS type_name,
                    grn.actual_unit_cost, grn.grn_qty, si.stock_at_site
             FROM pricing_estimate_resources_new pern
             LEFT JOIN resources r ON r.Resource_Id = pern.resource_Id
             LEFT JOIN resourcetype rt ON rt.ResourceType_Id = pern.resourcetype_Id
             LEFT JOIN (
                 SELECT por.allocation_id,
                        SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))*por.rate)
                            /NULLIF(SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))),0) AS actual_unit_cost,
                        SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))) AS grn_qty
                 FROM purchase_order_resources por
                 JOIN goods_received_note g ON g.GRN_Purchase_Order=por.order_id AND g.GRN_Item=por.resource_id
                 JOIN purchase_orders po ON po.order_id=por.order_id
                 WHERE po.project_id=:pid2 AND po.delete_status=0 AND por.delete_status=0
                 GROUP BY por.allocation_id
             ) grn ON grn.allocation_id=pern.pricing_resourceid
             LEFT JOIN (
                 SELECT si2.resource_id, si2.activity_id AS si_activity_id, si2.stock_at_site
                 FROM store_indents si2
                 INNER JOIN (
                     SELECT resource_id, activity_id, MAX(id) AS mx
                     FROM store_indents WHERE project_id=:pid3 GROUP BY resource_id, activity_id
                 ) lsi ON lsi.resource_id=si2.resource_id AND lsi.activity_id=si2.activity_id AND si2.id=lsi.mx
             ) si ON si.resource_id=pern.resource_Id AND si.si_activity_id=pern.activity_id
             WHERE pern.activity_id IN ({$wbIdsStr}) AND pern.project_id=:pid AND pern.pricing_status=0",
            [':pid' => $pid, ':pid2' => $pid, ':pid3' => $pid]
        )->queryAll();

        $resByWbs = [];
        foreach ($resRows as $r) { $resByWbs[(int)$r['activity_id']][] = $r; }

        // MB data — task amounts and work done across all sent MBs
        $taskAmountById   = [];
        $taskWorkDoneById = [];
        $mbs = $db->createCommand(
            "SELECT entries FROM wo_measurement_book
             WHERE project_id=:pid AND sent_status=1 AND delete_status=0",
            [':pid' => $pid]
        )->queryAll();
        foreach ($mbs as $mb) {
            foreach (json_decode($mb['entries'] ?? '[]', true) ?: [] as $entry) {
                foreach ($entry['tasks'] ?? [] as $task) {
                    $wd  = (float)($task['work_done'] ?? 0);
                    $rt  = (float)($task['rate']      ?? 0);
                    $tid = (int)($task['task_id']     ?? 0);
                    if ($tid) {
                        $taskAmountById[$tid]   = ($taskAmountById[$tid]   ?? 0.0) + $rt * $wd;
                        $taskWorkDoneById[$tid] = ($taskWorkDoneById[$tid] ?? 0.0) + $wd;
                    }
                }
            }
        }

        // Single pass per activity: compute rollup totals and capture per-resource detail
        $data = [];
        foreach ($schedActs as $sa) {
            $schedId   = (int)$sa['id'];
            $wbId      = (int)$sa['activity_id'];
            $schedQty  = (float)$sa['quantity'];
            $estQty    = $estQtyMap[$wbId] ?? 0;
            $lastQty   = $lastQtyMap[$schedId] ?? 0;
            $resources = $resByWbs[$wbId] ?? [];
            $ratio     = ($schedQty > 0) ? $estQty / $schedQty : 0.0;

            $unitCost    = 0.0;
            $actualTotal = 0.0;
            $resDetail   = [];

            foreach ($resources as $r) {
                $typeId             = (int)$r['type_id'];
                $resQty             = (float)$r['res_qty'];
                $estRate            = (float)$r['rate'];
                $plannedConsumption = round($resQty * $ratio, 3);

                $unitCost += $resQty * $estRate;

                $indentRaised = false;
                $hasMb        = false;

                if ($typeId === 4) {
                    $taskIds = array_filter(array_map('intval', explode(',', $r['task_ids'] ?? '')));
                    $wdVal = 0.0; $wdQty = 0.0;
                    foreach ($taskIds as $tid) {
                        $wdVal += $taskAmountById[$tid]   ?? 0.0;
                        $wdQty += $taskWorkDoneById[$tid] ?? 0.0;
                    }
                    $actUnit           = $wdQty > 0 ? $wdVal / $wdQty : null;
                    $hasMb             = $wdQty > 0;
                    $actualConsumption = ($lastQty > 0 && $wdQty > 0)
                        ? round($wdQty / $lastQty, 3) : $plannedConsumption;
                } elseif (in_array($typeId, [2, 6, 7, 8])) {
                    $actUnit      = ($r['actual_unit_cost'] !== null) ? (float)$r['actual_unit_cost'] : null;
                    $indentRaised = ($r['stock_at_site'] !== null);
                    if ($indentRaised && $lastQty > 0) {
                        $actualConsumption = round(
                            max(0, (float)($r['grn_qty'] ?? 0) - (float)($r['stock_at_site'] ?? 0)) / $lastQty,
                            3
                        );
                    } else {
                        $actualConsumption = $plannedConsumption;
                    }
                } else {
                    $actUnit           = null;
                    $actualConsumption = $plannedConsumption;
                }

                // Dashboard uses est as fallback when no actual
                $effectiveActUnit = $actUnit !== null ? $actUnit : $estRate;
                $hasActual = ($indentRaised && in_array($typeId, [2, 6, 7, 8])) || ($hasMb && $typeId === 4);

                if ($hasActual) {
                    $actualTotal += $effectiveActUnit * $actualConsumption;
                } else {
                    $actualTotal += $estRate * $plannedConsumption;
                }

                $resName = $r['resource_name'] ?? '';
                if ($resName === '') continue;

                $resDetail[] = [
                    'resource_name'       => $resName,
                    'resource_unit'       => $r['resource_unit'] ?? '',
                    'resource_type_name'  => $r['type_name'] ?? '',
                    'qty_per_unit'        => $resQty,
                    'est_unit_cost'       => $estRate,
                    'act_unit_cost'       => $actUnit,
                    'effective_act_unit'  => $effectiveActUnit,
                    'planned_consumption' => $plannedConsumption,
                    'actual_consumption'  => $actualConsumption,
                    'indent_raised'       => $indentRaised,
                    'has_mb'              => $hasMb,
                    'has_actual'          => $hasActual,
                    'est_cost'            => round($estRate * $plannedConsumption, 4),
                    'act_cost'            => round($effectiveActUnit * $actualConsumption, 4),
                ];
            }

            $estUCTotal = $unitCost * $ratio;
            // has_actual = true if ANY resource has real site data (indent_raised or has_mb)
            // mirrors JS: if (acoa > 0) hasReal = true
            $hasActual = false;
            foreach ($resDetail as $rd) {
                if ($rd['has_actual']) { $hasActual = true; break; }
            }

            // acoa = actualTotal × schedQty (actualTotal always uses est fallback, matches dashboard)
            // actwd: dashboard passes actwd from PHP; PHP: actualTotal > 0 ? actualTotal×lastQty : estwd
            // Since we always compute actualTotal (with fallback), check hasActual for the same gate
            $acoa  = round($actualTotal * $schedQty, 2);
            $estwd = round($estUCTotal * $lastQty, 2);
            $actwd = $hasActual ? round($actualTotal * $lastQty, 2) : $estwd;

            $data[$schedId] = [
                'wbId'       => $wbId,
                'name'       => $sa['name'],
                'schedQty'   => $schedQty,
                'estQty'     => $estQty,
                'lastQty'    => $lastQty,
                'unitCost'   => round($unitCost,    4),
                'estUCTotal' => round($estUCTotal,  4),
                'est'        => round($unitCost * $estQty, 2),
                'acoa'       => $acoa,
                'estwd'      => $estwd,
                'actwd'      => $actwd,
                'has_actual' => $hasActual,
                'resources'  => $resDetail,
            ];
        }
        return $data;
    }

    // =======================================================================
    // CORE COMPUTATION 2 — mirrors actionCostdashboardactivity()
    // Per-activity resource detail for a single schedule activity.
    // Used by resource unit cost, consumption, and cost-by-type tools.
    // =======================================================================
    private function computeActivityResources($db, $pid, $schedId, $wbId, $schedQty)
    {
        // Estimate qty
        $estQty = (float)($db->createCommand(
            "SELECT COALESCE(activity_qty,0) FROM pricing_estimate_new
             WHERE activity_Id=:wid AND project_Id=:pid AND pricing_status=0 LIMIT 1",
            [':wid' => $wbId, ':pid' => $pid]
        )->queryScalar() ?: 0);

        // Last reported qty — matches actionCostdashboardactivity: ORDER BY updated_at DESC LIMIT 1
        $lastQty = (float)($db->createCommand(
            "SELECT COALESCE(cumulated_qty,0) FROM schedule_progress_report
             WHERE activity_id=:sid ORDER BY updated_at DESC LIMIT 1",
            [':sid' => $schedId]
        )->queryScalar() ?: 0);

        $ratio = ($schedQty > 0) ? $estQty / $schedQty : 0.0;

        // Resources with GRN weighted avg and store indent
        $rows = $db->createCommand(
            "SELECT pern.pricing_resourceid,
                    COALESCE(pern.display_name, r.Name, rt.Name) AS resource_name,
                    COALESCE(rt.Name, '') AS type_name,
                    pern.rate, pern.quantity AS res_qty,
                    pern.resourcetype_Id AS type_id,
                    COALESCE(r.Unit, '') AS unit,
                    grn_cost.actual_unit_cost, grn_cost.grn_qty,
                    si.stock_at_site, pern.task_ids
             FROM pricing_estimate_resources_new pern
             LEFT JOIN resources r     ON r.Resource_Id      = pern.resource_Id
             LEFT JOIN resourcetype rt ON rt.ResourceType_Id = pern.resourcetype_Id
             LEFT JOIN (
                 SELECT por.allocation_id,
                        SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4)) * por.rate)
                            / NULLIF(SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))), 0) AS actual_unit_cost,
                        SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))) AS grn_qty
                 FROM purchase_order_resources por
                 JOIN goods_received_note g ON g.GRN_Purchase_Order=por.order_id AND g.GRN_Item=por.resource_id
                 JOIN purchase_orders po    ON po.order_id=por.order_id
                 WHERE po.project_id=:pid2 AND po.delete_status=0 AND por.delete_status=0
                 GROUP BY por.allocation_id
             ) grn_cost ON grn_cost.allocation_id=pern.pricing_resourceid
             LEFT JOIN (
                 SELECT si2.resource_id, si2.stock_at_site
                 FROM store_indents si2
                 INNER JOIN (
                     SELECT resource_id, MAX(id) AS max_id
                     FROM store_indents WHERE project_id=:pid3 AND activity_id=:wid2
                     GROUP BY resource_id
                 ) latest ON latest.resource_id=si2.resource_id AND si2.id=latest.max_id
             ) si ON si.resource_id=pern.resource_Id
             WHERE pern.activity_id=:wid AND pern.project_id=:pid AND pern.pricing_status=0
             ORDER BY pern.rate DESC",
            [':wid' => $wbId, ':pid' => $pid, ':pid2' => $pid, ':pid3' => $pid, ':wid2' => $wbId]
        )->queryAll();

        if (empty($rows)) return [];

        // MB data scoped to this activity's wbId
        $taskWorkDoneById = [];
        $taskAmountById   = [];
        $allMbs = $db->createCommand(
            "SELECT entries FROM wo_measurement_book
             WHERE project_id=:pid AND sent_status=1 AND delete_status=0",
            [':pid' => $pid]
        )->queryAll();
        foreach ($allMbs as $mb) {
            foreach (json_decode($mb['entries'] ?? '[]', true) ?: [] as $entry) {
                if ((int)($entry['activity_id'] ?? 0) !== $wbId) continue;
                foreach ($entry['tasks'] ?? [] as $task) {
                    $wd  = (float)($task['work_done'] ?? 0);
                    $rt  = (float)($task['rate']      ?? 0);
                    $tid = (int)($task['task_id']     ?? 0);
                    if ($tid) {
                        $taskWorkDoneById[$tid] = ($taskWorkDoneById[$tid] ?? 0.0) + $wd;
                        $taskAmountById[$tid]   = ($taskAmountById[$tid]   ?? 0.0) + $rt * $wd;
                    }
                }
            }
        }

        $items = [];
        foreach ($rows as $r) {
            $resQty             = (float)$r['res_qty'];
            $typeId             = (int)$r['type_id'];
            $estRate            = (float)$r['rate'];
            // Round to 3 — matches actionCostdashboardactivity
            $plannedConsumption = round($resQty * $ratio, 3);

            $indentRaised = false;
            $hasMb        = false;

            if ($typeId === 4) {
                $taskIds = array_filter(array_map('intval', explode(',', $r['task_ids'] ?? '')));
                $wdVal = 0.0; $wdQty = 0.0;
                foreach ($taskIds as $tid) {
                    $wdVal += $taskAmountById[$tid]   ?? 0.0;
                    $wdQty += $taskWorkDoneById[$tid] ?? 0.0;
                }
                $actUnit           = $wdQty > 0 ? $wdVal / $wdQty : null;
                $hasMb             = $wdQty > 0;
                $actualConsumption = ($lastQty > 0 && $wdQty > 0)
                    ? round($wdQty / $lastQty, 3) : $plannedConsumption;
            } elseif (in_array($typeId, [2, 6, 7, 8])) {
                $actUnit      = ($r['actual_unit_cost'] !== null) ? (float)$r['actual_unit_cost'] : null;
                $indentRaised = ($r['stock_at_site'] !== null);
                if ($indentRaised && $lastQty > 0) {
                    $actualConsumption = round(
                        max(0, (float)($r['grn_qty'] ?? 0) - (float)($r['stock_at_site'] ?? 0)) / $lastQty,
                        3
                    );
                } else {
                    $actualConsumption = $plannedConsumption;
                }
            } else {
                $actUnit           = null;
                $actualConsumption = $plannedConsumption;
            }

            // has_actual mirrors JS: (isMaterial && indent_raised) || (isSC && has_mb)
            $hasActual = ($indentRaised && in_array($typeId, [2, 6, 7, 8])) || ($hasMb && $typeId === 4);

            // For cost totals, dashboard falls back to estimated when no actual (actUC = hasAct ? actual : est)
            $effectiveActUnit = $actUnit !== null ? $actUnit : $estRate;

            $items[] = [
                'resource_name'       => $r['resource_name'],
                'resource_type'       => $r['type_name'],
                'unit'                => $r['unit'],
                'qty_per_unit'        => $resQty,
                'est_unit_cost'       => $estRate,
                'act_unit_cost'       => $actUnit,
                'effective_act_unit'  => $effectiveActUnit,
                'planned_consumption' => $plannedConsumption,
                'actual_consumption'  => $actualConsumption,
                'indent_raised'       => $indentRaised,
                'has_mb'              => $hasMb,
                'has_actual'          => $hasActual,
                'est_cost'            => round($estRate * $plannedConsumption, 4),
                // act_cost uses dashboard fallback: effectiveActUnit × actualConsumption
                'act_cost'            => round($effectiveActUnit * $actualConsumption, 4),
            ];
        }

        return [
            'last_qty' => $lastQty,
            'est_qty'  => $estQty,
            'items'    => $items,
        ];
    }

    // =======================================================================
    // PANEL TOOLS — one per dashboard panel
    // =======================================================================

    // ── Tool 1: Project-level cost summary ──────────────────────────────────
    private function toolGetProjectCosts($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid     = (int)$project['Project_Id'];
        $actData = $this->computeActivityCosts($db, $pid);

        if (empty($actData)) {
            return ['status' => 'no_data', 'reason' => "No cost data found for \"{$project['Name']}\"."];
        }

        // Dashboard JS: totAcoa += acoa > 0 ? acoa : est
        // Since acoa now uses est fallback, acoa always >= est when no actuals (= est)
        // Use has_actual flag to decide which to show, matching JS hasReal logic
        $totEst = 0.0; $totAcoa = 0.0; $totEstWD = 0.0; $totActWD = 0.0; $hasAnyActual = false;
        foreach ($actData as $c) {
            $totEst   += $c['est'];
            $totEstWD += $c['estwd'];
            // acoa already = est when no actuals (fallback in computeActivityCosts)
            $totAcoa  += $c['acoa'];
            $totActWD += $c['actwd'];
            if ($c['has_actual']) $hasAnyActual = true;
        }

        return [
            'status'          => 'ok',
            'project'         => $project['Name'],
            'client'          => $project['client_name'],
            'location'        => $project['location'],
            'contract_value'  => (float)$project['project_value'],
            'estimated_cost'  => round($totEst,   2),
            'actual_cost'     => $hasAnyActual ? round($totAcoa, 2) : null,
            'has_actual'      => $hasAnyActual,
            'difference'      => $hasAnyActual ? round($totEst - $totAcoa, 2) : null,
            'ecwd'            => round($totEstWD, 2),
            'acwd'            => round($totActWD, 2),
            'ecwd_acwd_diff'  => round($totEstWD - $totActWD, 2),
            'as_of'           => date('Y-m-d H:i:s'),
        ];
    }

    // ── Tool 2: Cost by Group ────────────────────────────────────────────────
    private function toolGetGroupCosts($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid     = (int)$project['Project_Id'];
        $actData = $this->computeActivityCosts($db, $pid);

        if (empty($actData)) {
            return ['status' => 'no_data', 'reason' => "No cost data found for \"{$project['Name']}\"."];
        }

        // Build hierarchy maps
        $iowRows = $db->createCommand(
            "SELECT wg.id AS iow_id, wg.iowGroupid AS group_id FROM workgroups_new wg
             WHERE wg.project_Id=:pid AND wg.pricing_status=0",
            [':pid' => $pid]
        )->queryAll();
        $iowToGroup = [];
        foreach ($iowRows as $r) { $iowToGroup[(int)$r['iow_id']] = (int)$r['group_id']; }

        $groupRows = $db->createCommand(
            "SELECT DISTINCT ig.id AS group_id, ig.name AS group_name
             FROM workgroups_new wg JOIN iow_groups ig ON ig.id=wg.iowGroupid
             WHERE wg.project_Id=:pid AND wg.pricing_status=0 ORDER BY ig.name",
            [':pid' => $pid]
        )->queryAll();
        $groupNames = [];
        foreach ($groupRows as $r) { $groupNames[(int)$r['group_id']] = $r['group_name']; }

        $schedWan = $db->createCommand(
            "SELECT id AS sched_id, activity_id AS wan_id FROM scheduleactivities WHERE projectId=:pid AND status=0",
            [':pid' => $pid]
        )->queryAll();
        $schedToWan = [];
        foreach ($schedWan as $r) { $schedToWan[(int)$r['sched_id']] = (int)$r['wan_id']; }

        $wanToIow = $db->createCommand(
            "SELECT wan.id AS wan_id, wg.id AS iow_id FROM workgroup_activities_new wan
             JOIN workgroups_new wg ON wg.id=wan.WorkGroup_Id AND wg.project_Id=:pid
             WHERE wan.project_Id=:pid2 AND wan.pricing_status=0",
            [':pid' => $pid, ':pid2' => $pid]
        )->queryAll();
        $wanIowMap = [];
        foreach ($wanToIow as $r) { $wanIowMap[(int)$r['wan_id']] = (int)$r['iow_id']; }

        $grpAgg = [];
        foreach ($actData as $schedId => $c) {
            $wanId = $schedToWan[$schedId] ?? null;
            if (!$wanId) continue;
            $iowId = $wanIowMap[$wanId] ?? null;
            if (!$iowId) continue;
            $gid = $iowToGroup[$iowId] ?? null;
            if (!$gid) continue;
            if (!isset($grpAgg[$gid])) {
                $grpAgg[$gid] = ['est' => 0.0, 'acoa' => 0.0, 'estwd' => 0.0, 'actwd' => 0.0, 'has_actual' => false];
            }
            $grpAgg[$gid]['est']   += $c['est'];
            $grpAgg[$gid]['estwd'] += $c['estwd'];
            // acoa already uses est fallback when no actuals
            $grpAgg[$gid]['acoa']  += $c['acoa'];
            $grpAgg[$gid]['actwd'] += $c['actwd'];
            if ($c['has_actual']) $grpAgg[$gid]['has_actual'] = true;
        }

        $filter = strtolower(trim($input['group_name'] ?? ''));
        $result = [];
        foreach ($groupRows as $g) {
            $gid  = (int)$g['group_id'];
            $name = $g['group_name'];
            if ($filter !== '' && strpos(strtolower($name), $filter) === false) continue;
            $agg  = $grpAgg[$gid] ?? ['est' => 0, 'acoa' => 0, 'estwd' => 0, 'actwd' => 0, 'has_actual' => false];
            $result[] = [
                'group_name'     => $name,
                'has_actual'     => $agg['has_actual'],
                'estimated_cost' => round($agg['est'],  2),
                'actual_cost'    => $agg['has_actual'] ? round($agg['acoa'], 2) : null,
                'difference'     => $agg['has_actual'] ? round($agg['est'] - $agg['acoa'], 2) : null,
                'ecwd'           => round($agg['estwd'], 2),
                'acwd'           => round($agg['actwd'], 2),
                'ecwd_acwd_diff' => round($agg['estwd'] - $agg['actwd'], 2),
            ];
        }

        if (empty($result)) {
            return ['status' => 'no_data', 'reason' => "No groups found" . ($filter ? " matching \"{$filter}\"" : '') . "."];
        }

        return [
            'status'  => 'ok',
            'project' => $project['Name'],
            'as_of'   => date('Y-m-d H:i:s'),
            'groups'  => $result,
        ];
    }

    // ── Tool 3: Cost by IOW ──────────────────────────────────────────────────
    private function toolGetIowCosts($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid     = (int)$project['Project_Id'];
        $actData = $this->computeActivityCosts($db, $pid);

        if (empty($actData)) {
            return ['status' => 'no_data', 'reason' => "No cost data found for \"{$project['Name']}\"."];
        }

        $iowRows = $db->createCommand(
            "SELECT wg.id AS iow_id, wg.WorkGroup_Name AS iow_name, wg.iowGroupid AS group_id,
                    ig.name AS group_name
             FROM workgroups_new wg JOIN iow_groups ig ON ig.id=wg.iowGroupid
             WHERE wg.project_Id=:pid AND wg.pricing_status=0 ORDER BY ig.name, wg.WorkGroup_Name",
            [':pid' => $pid]
        )->queryAll();

        $wanToIow = $db->createCommand(
            "SELECT wan.id AS wan_id, wg.id AS iow_id FROM workgroup_activities_new wan
             JOIN workgroups_new wg ON wg.id=wan.WorkGroup_Id AND wg.project_Id=:pid
             WHERE wan.project_Id=:pid2 AND wan.pricing_status=0",
            [':pid' => $pid, ':pid2' => $pid]
        )->queryAll();
        $wanIowMap = [];
        foreach ($wanToIow as $r) { $wanIowMap[(int)$r['wan_id']] = (int)$r['iow_id']; }

        $schedWan = $db->createCommand(
            "SELECT id AS sched_id, activity_id AS wan_id FROM scheduleactivities WHERE projectId=:pid AND status=0",
            [':pid' => $pid]
        )->queryAll();
        $schedToWan = [];
        foreach ($schedWan as $r) { $schedToWan[(int)$r['sched_id']] = (int)$r['wan_id']; }

        $iowAgg = [];
        foreach ($actData as $schedId => $c) {
            $wanId = $schedToWan[$schedId] ?? null;
            if (!$wanId) continue;
            $iowId = $wanIowMap[$wanId] ?? null;
            if (!$iowId) continue;
            if (!isset($iowAgg[$iowId])) {
                $iowAgg[$iowId] = ['est' => 0.0, 'acoa' => 0.0, 'estwd' => 0.0, 'actwd' => 0.0, 'has_actual' => false];
            }
            $iowAgg[$iowId]['est']   += $c['est'];
            $iowAgg[$iowId]['estwd'] += $c['estwd'];
            $iowAgg[$iowId]['acoa']  += $c['acoa'];
            $iowAgg[$iowId]['actwd'] += $c['actwd'];
            if ($c['has_actual']) $iowAgg[$iowId]['has_actual'] = true;
        }

        $filter = strtolower(trim($input['iow_name'] ?? ''));
        $result = [];
        foreach ($iowRows as $iow) {
            $iid  = (int)$iow['iow_id'];
            $name = $iow['iow_name'];
            if ($filter !== '' && strpos(strtolower($name), $filter) === false) continue;
            $agg  = $iowAgg[$iid] ?? ['est' => 0, 'acoa' => 0, 'estwd' => 0, 'actwd' => 0, 'has_actual' => false];
            $result[] = [
                'iow_name'       => $name,
                'group_name'     => $iow['group_name'],
                'has_actual'     => $agg['has_actual'],
                'estimated_cost' => round($agg['est'],  2),
                'actual_cost'    => $agg['has_actual'] ? round($agg['acoa'], 2) : null,
                'difference'     => $agg['has_actual'] ? round($agg['est'] - $agg['acoa'], 2) : null,
                'ecwd'           => round($agg['estwd'], 2),
                'acwd'           => round($agg['actwd'], 2),
                'ecwd_acwd_diff' => round($agg['estwd'] - $agg['actwd'], 2),
            ];
        }

        if (empty($result)) {
            return ['status' => 'no_data', 'reason' => "No IOW items found" . ($filter ? " matching \"{$filter}\"" : '') . "."];
        }

        return [
            'status'  => 'ok',
            'project' => $project['Name'],
            'as_of'   => date('Y-m-d H:i:s'),
            'iow_items' => $result,
        ];
    }

    // ── Tool 4: Cost by Activity (the full table) ────────────────────────────
    private function toolGetActivityCosts($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid     = (int)$project['Project_Id'];
        $actData = $this->computeActivityCosts($db, $pid);

        if (empty($actData)) {
            return ['status' => 'no_data', 'reason' => "No activity cost data found for \"{$project['Name']}\"."];
        }

        $filter = strtolower(trim($input['activity_name'] ?? ''));

        $rows = [];
        foreach ($actData as $schedId => $c) {
            if ($filter !== '' && strpos(strtolower($c['name']), $filter) === false) continue;
            $rows[] = [
                'activity_name'          => $c['name'],
                'estimated_unit_cost'    => $c['estUCTotal'],
                'quantity_of_work_done'  => $c['lastQty'],
                'estimated_cost'         => $c['est'],
                'actual_cost'            => $c['has_actual'] ? $c['acoa'] : null,
                'difference'             => $c['has_actual'] ? round($c['est'] - $c['acoa'], 2) : null,
                'has_actual'             => $c['has_actual'],
                'ecwd'                   => $c['estwd'],
                'acwd'                   => $c['has_actual'] ? $c['actwd'] : null,
            ];
        }

        if (empty($rows)) {
            return ['status' => 'no_data', 'reason' => "No activities found" . ($filter ? " matching \"{$filter}\"" : '') . "."];
        }

        return [
            'status'     => 'ok',
            'project'    => $project['Name'],
            'as_of'      => date('Y-m-d H:i:s'),
            'count'      => count($rows),
            'activities' => $rows,
        ];
    }

    // ── Tool 5: Unit cost of a single activity ───────────────────────────────
    private function toolGetActivityUnitCost($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid          = (int)$project['Project_Id'];
        $activityName = trim($input['activity_name'] ?? '');

        if ($activityName === '') {
            return ['status' => 'no_data', 'reason' => 'activity_name is required.'];
        }

        $matches = $this->resolveActivity($db, $pid, $activityName);

        if (empty($matches)) {
            return ['status' => 'no_data', 'reason' => "No activity matching \"{$activityName}\" found in \"{$project['Name']}\"."];
        }
        if (count($matches) > 1) {
            return [
                'status'     => 'ambiguous',
                'reason'     => 'Multiple activities matched. Ask the user to clarify.',
                'candidates' => array_column($matches, 'name'),
            ];
        }

        $act     = $matches[0];
        $schedId = (int)$act['id'];
        $wbId    = (int)$act['wan_id'];

        $res = $this->computeActivityResources($db, $pid, $schedId, $wbId, (float)$act['sched_qty']);
        if (empty($res)) {
            return ['status' => 'no_data', 'reason' => "No resource data found for \"{$act['name']}\"."];
        }

        // Unit cost of activity = sum of (rate × planned_consumption) across all resources
        // mirrors renderCdUnitCostOfActivity() in _performancedashboard.js:
        //   estUC * estCons for estimated
        //   actUC = hasAct ? actual_unit_cost : estUC (fallback) for actual
        $estUCA = 0.0; $actUCA = 0.0; $hasActual = false;
        foreach ($res['items'] as $r) {
            $estUCA += $r['est_unit_cost'] * $r['planned_consumption'];
            // effective_act_unit already applies dashboard fallback (est when no actual)
            $actUCA += $r['effective_act_unit'] * $r['actual_consumption'];
            if ($r['has_actual']) $hasActual = true;
        }

        return [
            'status'                  => 'ok',
            'project'                 => $project['Name'],
            'activity'                => $act['name'],
            'unit'                    => $act['unit'],
            'schedule_qty'            => (float)$act['sched_qty'],
            'qty_done'                => $res['last_qty'],
            'estimated_unit_cost'     => round($estUCA, 4),
            'actual_unit_cost'        => $hasActual ? round($actUCA, 4) : null,
            'has_actual'              => $hasActual,
            'as_of'                   => date('Y-m-d H:i:s'),
        ];
    }

    // ── Tool 6: Unit cost of resource (est vs actual) ────────────────────────
    private function toolGetResourceUnitCost($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid          = (int)$project['Project_Id'];
        $activityName = trim($input['activity_name'] ?? '');
        $resourceName = strtolower(trim($input['resource_name'] ?? ''));

        if ($activityName === '') {
            return ['status' => 'no_data', 'reason' => 'activity_name is required for resource unit cost.'];
        }

        $matches = $this->resolveActivity($db, $pid, $activityName);
        if (empty($matches)) {
            return ['status' => 'no_data', 'reason' => "No activity matching \"{$activityName}\" found in \"{$project['Name']}\"."];
        }
        if (count($matches) > 1) {
            return ['status' => 'ambiguous', 'reason' => 'Multiple activities matched.', 'candidates' => array_column($matches, 'name')];
        }

        $act = $matches[0];
        $res = $this->computeActivityResources($db, $pid, (int)$act['id'], (int)$act['wan_id'], (float)$act['sched_qty']);
        if (empty($res)) {
            return ['status' => 'no_data', 'reason' => "No resource data found for \"{$act['name']}\"."];
        }

        $items = $res['items'];
        if ($resourceName !== '') {
            $items = array_values(array_filter($items, fn($r) => strpos(strtolower($r['resource_name']), $resourceName) !== false));
        }
        if (empty($items)) {
            return ['status' => 'no_data', 'reason' => "No resource matching \"{$resourceName}\" found in activity \"{$act['name']}\"."];
        }

        return [
            'status'    => 'ok',
            'project'   => $project['Name'],
            'activity'  => $act['name'],
            'unit'      => $act['unit'],
            'as_of'     => date('Y-m-d H:i:s'),
            'resources' => array_map(fn($r) => [
                'resource_name'   => $r['resource_name'],
                'resource_type'   => $r['resource_type'],
                'unit'            => $r['unit'],
                'est_unit_cost'   => $r['est_unit_cost'],
                'act_unit_cost'   => $r['act_unit_cost'],
                // has_actual: material needs indent raised, SC needs MB entry (mirrors JS dashboard)
                'has_actual'      => $r['has_actual'],
            ], $items),
        ];
    }

    // ── Tool 7: Resource consumption (planned vs actual) ─────────────────────
    private function toolGetResourceConsumption($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid          = (int)$project['Project_Id'];
        $activityName = trim($input['activity_name'] ?? '');
        $resourceName = strtolower(trim($input['resource_name'] ?? ''));

        if ($activityName === '') {
            return ['status' => 'no_data', 'reason' => 'activity_name is required for resource consumption.'];
        }

        $matches = $this->resolveActivity($db, $pid, $activityName);
        if (empty($matches)) {
            return ['status' => 'no_data', 'reason' => "No activity matching \"{$activityName}\" found in \"{$project['Name']}\"."];
        }
        if (count($matches) > 1) {
            return ['status' => 'ambiguous', 'reason' => 'Multiple activities matched.', 'candidates' => array_column($matches, 'name')];
        }

        $act = $matches[0];
        $res = $this->computeActivityResources($db, $pid, (int)$act['id'], (int)$act['wan_id'], (float)$act['sched_qty']);
        if (empty($res)) {
            return ['status' => 'no_data', 'reason' => "No resource data found for \"{$act['name']}\"."];
        }

        $items = $res['items'];
        if ($resourceName !== '') {
            $items = array_values(array_filter($items, fn($r) => strpos(strtolower($r['resource_name']), $resourceName) !== false));
        }
        if (empty($items)) {
            return ['status' => 'no_data', 'reason' => "No resource matching \"{$resourceName}\" found in activity \"{$act['name']}\"."];
        }

        return [
            'status'    => 'ok',
            'project'   => $project['Name'],
            'activity'  => $act['name'],
            'unit'      => $act['unit'],
            'qty_done'  => $res['last_qty'],
            'as_of'     => date('Y-m-d H:i:s'),
            'resources' => array_map(fn($r) => [
                'resource_name'       => $r['resource_name'],
                'resource_type'       => $r['resource_type'],
                'unit'                => $r['unit'],
                'planned_consumption' => $r['planned_consumption'],
                // Dashboard shows actual_consumption only when indent_raised (material) or has_mb (SC)
                'actual_consumption'  => $r['has_actual'] ? $r['actual_consumption'] : $r['planned_consumption'],
                'has_actual'          => $r['has_actual'],
            ], $items),
        ];
    }

    // ── Tool 8: Resource cost by type (Material / Labour / Plant / SC) ───────
    private function toolGetResourceCostByType($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid          = (int)$project['Project_Id'];
        $activityName = trim($input['activity_name'] ?? '');

        if ($activityName === '') {
            return ['status' => 'no_data', 'reason' => 'activity_name is required for resource cost by type.'];
        }

        $matches = $this->resolveActivity($db, $pid, $activityName);
        if (empty($matches)) {
            return ['status' => 'no_data', 'reason' => "No activity matching \"{$activityName}\" found in \"{$project['Name']}\"."];
        }
        if (count($matches) > 1) {
            return ['status' => 'ambiguous', 'reason' => 'Multiple activities matched.', 'candidates' => array_column($matches, 'name')];
        }

        $act = $matches[0];
        $res = $this->computeActivityResources($db, $pid, (int)$act['id'], (int)$act['wan_id'], (float)$act['sched_qty']);
        if (empty($res)) {
            return ['status' => 'no_data', 'reason' => "No resource data found for \"{$act['name']}\"."];
        }

        // Group by resource_type — mirrors renderCdResourceCost() in JS dashboard:
        //   actUC = hasActual ? actual_unit_cost : estUC  (fallback to est when no actual)
        //   groups[key].act += actUC * actCons  (always, for ALL rows)
        $byType = [];
        foreach ($res['items'] as $r) {
            $tn = $r['resource_type'] ?: 'Other';
            if (!isset($byType[$tn])) {
                $byType[$tn] = ['est_cost' => 0.0, 'act_cost' => 0.0, 'has_actual' => false];
            }
            $byType[$tn]['est_cost'] += $r['est_cost'];
            // act_cost already uses effectiveActUnit (dashboard fallback), always add it
            $byType[$tn]['act_cost'] += $r['act_cost'];
            if ($r['has_actual']) $byType[$tn]['has_actual'] = true;
        }

        $result = [];
        foreach ($byType as $tn => $vals) {
            $result[] = [
                'resource_type' => $tn,
                'estimated_cost'=> round($vals['est_cost'], 2),
                'actual_cost'   => $vals['has_actual'] ? round($vals['act_cost'], 2) : null,
                'has_actual'    => $vals['has_actual'],
            ];
        }

        return [
            'status'         => 'ok',
            'project'        => $project['Name'],
            'activity'       => $act['name'],
            'as_of'          => date('Y-m-d H:i:s'),
            'by_resource_type' => $result,
        ];
    }

    // =======================================================================
    // SUPPORTING TOOLS (unchanged logic, no cache dependency)
    // =======================================================================

    private function toolGetProjects($db)
    {
        $rows = $db->createCommand(
            "SELECT Project_Id, Name, client_name, location, start_date, end_date, project_value
             FROM projects WHERE Status=0 ORDER BY Name ASC LIMIT 50"
        )->queryAll();

        if (empty($rows)) return ['status' => 'no_data', 'reason' => 'No active projects found.'];

        return [
            'status'   => 'ok',
            'count'    => count($rows),
            'projects' => array_map(fn($p) => [
                'name'           => $p['Name'],
                'client'         => $p['client_name'],
                'location'       => $p['location'],
                'start_date'     => $p['start_date'],
                'end_date'       => $p['end_date'],
                'contract_value' => (float)$p['project_value'],
            ], $rows),
        ];
    }

    private function toolGetActivityKpi($db, $input)
    {
        $activityName = trim($input['activity_name'] ?? '');
        $projectName  = trim($input['project_name']  ?? '');

        if ($activityName === '') return ['status' => 'no_data', 'reason' => 'activity_name is required.'];

        $where  = "sa.status=0";
        $params = [];
        if ($projectName !== '') {
            $where .= " AND LOWER(p.Name) LIKE :pname";
            $params[':pname'] = '%' . strtolower($projectName) . '%';
        }
        $raw   = preg_replace('/[^a-z0-9 ]/i', ' ', $activityName);
        $words = array_values(array_filter(explode(' ', $raw), fn($w) => strlen($w) >= 4));
        foreach ($words as $i => $word) {
            $key = ':aw' . $i;
            $where .= " AND LOWER(sa.name) LIKE {$key}";
            $params[$key] = '%' . strtolower(rtrim($word, 's')) . '%';
        }

        $acts = $db->createCommand(
            "SELECT sa.id, sa.name, sa.duration, sa.old_duration, sa.unit,
                    sa.quantity, sa.completed_status, sa.start_date,
                    sa.actual_start_date, sa.actual_end_date, sa.end_date,
                    sa.resource_units, sa.critical_status,
                    sa.projectId AS pid, p.Name AS project_name
             FROM scheduleactivities sa
             JOIN projects p ON p.Project_Id=sa.projectId AND p.Status=0
             WHERE {$where} ORDER BY sa.start_date DESC LIMIT 5",
            $params
        )->queryAll();

        if (empty($acts)) return ['status' => 'no_data', 'reason' => "No activity found matching \"{$activityName}\"."];
        if (count($acts) > 1) {
            return [
                'status'     => 'ambiguous',
                'reason'     => 'Multiple activities matched. Ask the user to specify which one.',
                'candidates' => array_map(fn($a) => ['name' => $a['name'], 'project' => $a['project_name']], $acts),
            ];
        }

        $act   = $acts[0];
        $actid = (int)$act['id'];
        $pid   = (int)$act['pid'];

        // Exact port of _buildKpi() from ProjectsmainController.php
        $san = $db->createCommand(
            "SELECT progress, cycle_qty, Workhours, Cycles, Resourceunits, cycle_unit_type
             FROM schedule_activity_new WHERE actvity_id=$actid"
        )->queryOne();

        $target_qty     = (float)($act['quantity'] ?? 0);
        $unit           = $act['unit'] ?? '';
        $wh             = $san ? (int)$san['Workhours']  : 8;
        $progress       = $san ? (float)$san['progress'] : 0;
        $resource_units = max(1, (float)($act['resource_units'] ?? 1));

        $pr = $db->createCommand(
            "SELECT cumulated_qty FROM schedule_progress_report
             WHERE activity_id=$actid ORDER BY updated_at DESC LIMIT 1"
        )->queryOne();
        $actual_qty    = $pr ? (float)$pr['cumulated_qty'] : 0;
        $work_done_pct = ($target_qty > 0) ? round($actual_qty / $target_qty * 100, 1) : $progress;

        $duration   = (float)($act['duration']     ?? 0);
        $b_duration = (float)($act['old_duration'] ?? 0);

        $target_cycle = ($target_qty > 0)
            ? round(($b_duration / $target_qty) * $wh, 3) : 0;
        $actual_cycle = 0;
        $actual_prod  = 0;

        $brk = $db->createCommand(
            "SELECT SUM(break_hour) AS total_break FROM schedule_progress_report_log
             WHERE activity_id=$actid"
        )->queryOne();
        $cum_break = $brk ? (float)$brk['total_break'] : 0;
        $cap_max   = 0;
        $cap_used  = 0;

        $cod_rows = $db->createCommand(
            "SELECT cd.title, COUNT(strc.id) AS cnt
             FROM schedule_task_report_cause_of_delays strc
             JOIN cause_of_delays cd ON cd.id=strc.cause_of_delay_id
             WHERE strc.activity_id=$actid GROUP BY cd.id, cd.title"
        )->queryAll();
        $total_cod      = array_sum(array_column($cod_rows, 'cnt'));
        $cause_of_delay = array_map(fn($r) => [
            'name'  => $r['title'],
            'count' => (int)$r['cnt'],
            'pct'   => $total_cod > 0 ? round($r['cnt'] / $total_cod * 100) : 0,
        ], $cod_rows);

        $res_rows = $db->createCommand(
            "SELECT r.Name AS name, strr.count AS cnt
             FROM schedule_task_report_resources strr
             JOIN resources r ON r.Resource_Id=strr.res_id
             WHERE strr.activity_id=$actid
             ORDER BY strr.count DESC LIMIT 8"
        )->queryAll();

        $lrd = $db->createCommand(
            "SELECT MAX(report_date) AS last_date FROM schedule_progress_report_log
             WHERE activity_id=$actid AND currentqty>0"
        )->queryOne();
        $last_reported_date = ($lrd && !empty($lrd['last_date'])) ? $lrd['last_date'] : '';

        $spr = $db->createCommand(
            "SELECT start_date FROM schedule_progress_report WHERE activity_id=$actid LIMIT 1"
        )->queryOne();
        $reported_start = ($spr && !empty($spr['start_date']) && $spr['start_date'] != '0000-00-00') ? $spr['start_date'] : '';
        $planned_start  = (!empty($act['start_date']) && $act['start_date'] != '0000-00-00') ? $act['start_date'] : '';
        $act_start_date = ($planned_start && $reported_start)
            ? min($planned_start, $reported_start)
            : ($reported_start ?: $planned_start);

        $planned_per_day = ($b_duration > 0 && $target_qty > 0) ? round($target_qty / $b_duration, 3) : 0;
        $target_prod     = $planned_per_day;
        $elapsed         = 0;
        $start_delay     = 0;

        if ($act_start_date && $last_reported_date && $actual_qty > 0) {
            $elapsed      = max(1, (strtotime($last_reported_date) - strtotime($act_start_date)) / 86400 + 1);
            $actual_prod  = round($actual_qty / $elapsed, 3);
            $actual_cycle = round(($elapsed / $actual_qty) * $wh, 3);
            $cap_max      = round($elapsed * $wh, 2);
            $cap_used     = round(max(0, $cap_max - $cum_break), 2);
        } elseif ($planned_start && $actual_qty == 0 && strtotime($planned_start) < strtotime(date('Y-m-d'))) {
            $start_delay = (int)floor((strtotime(date('Y-m-d')) - strtotime($planned_start)) / 86400);
            $elapsed     = $start_delay;
        }

        $projected_duration = ($actual_qty > 0 && $elapsed > 0)
            ? (int)round($elapsed / $actual_qty * $target_qty)
            : ($start_delay > 0 ? $b_duration + $start_delay : 0);

        $sa_act      = $db->createCommand("SELECT activity_id FROM scheduleactivities WHERE id=$actid")->queryOne();
        $wbn_id      = $sa_act ? (int)$sa_act['activity_id'] : 0;
        $wbn_row     = $wbn_id ? $db->createCommand("SELECT activity_Id FROM workgroup_activities_new WHERE id=$wbn_id")->queryOne() : null;
        $masterActId = ($wbn_row && $wbn_row['activity_Id']) ? (int)$wbn_row['activity_Id'] : $wbn_id;

        $task_rows = $masterActId ? $db->createCommand(
            "SELECT at.id AS task_id, at.task_name, at.task_unit,
                    COALESCE(stn.task_productivity, at.productivity, 0) AS productivity,
                    COALESCE(stn.task_resource_units, 1) AS resource_units,
                    COALESCE(stn.task_qty, 0) AS task_qty,
                    COALESCE(stn.Budgeted_Duration, 0) AS planned_duration
             FROM activity_tasks at
             LEFT JOIN schedule_task_new stn ON stn.task_Id=at.id AND stn.activity_Id=$actid
             WHERE at.activity_id=$masterActId ORDER BY at.sort_order ASC"
        )->queryAll() : [];

        $taskMbQty = [];
        $mbRows    = $db->createCommand(
            "SELECT entries FROM wo_measurement_book WHERE project_id=$pid AND sent_status=1"
        )->queryAll();
        foreach ($mbRows as $mbRow) {
            foreach (json_decode($mbRow['entries'] ?? '[]', true) ?: [] as $entry) {
                if ((string)($entry['activity_id'] ?? '') !== (string)$wbn_id) continue;
                foreach ($entry['tasks'] ?? [] as $t) {
                    $tid = (int)($t['task_id'] ?? 0);
                    if (!$tid) continue;
                    $taskMbQty[$tid] = ($taskMbQty[$tid] ?? 0) + (float)($t['work_done'] ?? 0);
                }
            }
        }

        // Task map: exact copy of _buildKpi() task closure
        $tasks = array_map(function($t) use ($target_qty, $actual_qty, $elapsed, $taskMbQty) {
            $tqu    = (float)$t['task_qty'];
            $actual = ($elapsed > 0 && $actual_qty > 0 && $tqu > 0)
                ? round($actual_qty * $tqu / $elapsed, 3) : 0;
            $mbQty  = $taskMbQty[(int)$t['task_id']] ?? 0;
            return [
                'name'             => $t['task_name'],
                'unit'             => $t['task_unit'],
                'val'              => round((float)$t['productivity'] * max(1, (float)$t['resource_units']), 3),
                'actual'           => $actual,
                'qty'              => round($tqu * $target_qty, 3),
                'planned_duration' => (float)$t['planned_duration'],
                'actual_duration'  => ($elapsed > 0 && $mbQty > 0) ? round($elapsed / $mbQty, 3) : 0,
            ];
        }, $task_rows);

        // Return: exact field names and values from _buildKpi() + status wrapper
        return [
            'status'              => 'ok',
            'activity_id'         => $actid,
            'activity_name'       => $act['name'],
            'work_done_pct'       => $work_done_pct,
            'target_qty'          => $target_qty,
            'actual_qty'          => $actual_qty,
            'unit'                => $unit,
            'duration'            => $duration,
            'b_duration'          => $b_duration,
            'act_start_date'      => $act_start_date,
            'last_reported_date'  => $last_reported_date ?: null,
            'planned_per_day'     => $planned_per_day,
            'resource_units'      => $resource_units,
            'target_productivity' => $target_prod,
            'actual_productivity' => $actual_prod,
            'wh'                  => $wh,
            'target_cycle_time'   => $target_cycle,
            'actual_cycle_time'   => $actual_cycle,
            'cap_max'             => $cap_max,
            'cap_used'            => $cap_used,
            'cause_of_delay'      => $cause_of_delay,
            'resources'           => $res_rows,
            'tasks'               => $tasks,
            'elapsed'             => (int)round($elapsed),
            'start_delay'         => $start_delay,
            'projected_duration'  => $projected_duration,
            'planned_end_date'    => $act['end_date'] ?? '',
            'reported_start_date' => $reported_start ?: '',
            'adj_start_date'      => $planned_start ?: $act_start_date,
            'adj_end_date'        => $planned_start
                ? date('Y-m-d', strtotime($planned_start . ' +' . (max(1, $b_duration) - 1) . ' days'))
                : ($act['end_date'] ?? ''),
            'critical'            => (($act['critical_status'] ?? '') === 'Yes'),
            'project_name'        => $act['project_name'],
        ];
    }

    private function toolGetScheduleActivities($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid    = (int)$project['Project_Id'];
        $filter = $input['filter'] ?? 'all';
        $extra  = '';
        switch ($filter) {
            case 'critical':  $extra = "AND sa.critical_status='Yes'"; break;
            case 'completed': $extra = "AND sa.completed_status=1"; break;
            case 'ongoing':   $extra = "AND sa.completed_status=0 AND pr.report_count>0"; break;
        }

        // Mirror actionPerformancedashboard(): compute projected_duration the same way the dashboard does.
        // Delayed = not completed AND:
        //   - ongoing (has progress): projected_duration > old_duration
        //   - upcoming (no progress): start_date < today
        $rows = $db->createCommand("
            SELECT sa.name, sa.start_date, sa.end_date,
                   sa.old_duration AS planned_duration, sa.quantity, sa.unit,
                   sa.completed_status, sa.critical_status,
                   COALESCE(pr.report_count, 0) AS report_count,
                   COALESCE(pr.cumulated_qty, 0) AS qty_done,
                   pr.spr_start_date,
                   CASE WHEN sa.quantity>0 AND COALESCE(pr.cumulated_qty,0)>0
                        THEN ROUND(COALESCE(pr.cumulated_qty,0)/sa.quantity*100,1)
                        WHEN sa.completed_status=1 THEN 100 ELSE 0 END AS progress_pct,
                   CASE
                       WHEN pr.cumulated_qty>0 AND sa.quantity>0
                            AND pr.spr_start_date IS NOT NULL AND pr.spr_start_date!='0000-00-00'
                       THEN ROUND(
                              (DATEDIFF(pr.last_report_date, pr.spr_start_date) + 1)
                              / pr.cumulated_qty * sa.quantity
                            )
                       ELSE NULL
                   END AS projected_duration
            FROM scheduleactivities sa
            LEFT JOIN (
                SELECT spr.activity_id,
                       COUNT(*) AS report_count,
                       MAX(spr.cumulated_qty) AS cumulated_qty,
                       MIN(spr.start_date) AS spr_start_date,
                       COALESCE(MAX(sprl.report_date), MAX(spr.updated_at)) AS last_report_date
                FROM schedule_progress_report spr
                LEFT JOIN schedule_progress_report_log sprl
                       ON sprl.activity_id=spr.activity_id AND sprl.currentqty>0
                GROUP BY spr.activity_id
            ) pr ON pr.activity_id=sa.id
            WHERE sa.projectId=:pid AND sa.status=0 {$extra}
            ORDER BY sa.start_date ASC LIMIT 200
        ", [':pid' => $pid])->queryAll();

        $today = date('Y-m-d');

        // Apply delayed filter in PHP (same logic as dashboard JS toBarItems())
        if ($filter === 'delayed') {
            $rows = array_values(array_filter($rows, function($a) use ($today) {
                if ((int)$a['completed_status'] === 1) return false;
                $hasProgress = (int)$a['report_count'] > 0;
                if ($hasProgress) {
                    // Ongoing: delayed if projected_duration > planned_duration
                    $proj = $a['projected_duration'];
                    return $proj !== null && (float)$proj > (float)$a['planned_duration'];
                } else {
                    // Upcoming: delayed if planned start has already passed
                    return !empty($a['start_date']) && $a['start_date'] !== '0000-00-00'
                        && $a['start_date'] < $today;
                }
            }));
        }

        if (empty($rows)) return ['status' => 'no_data', 'reason' => "No {$filter} activities found for \"{$project['Name']}\"."];

        return [
            'status'     => 'ok',
            'project'    => $project['Name'],
            'filter'     => $filter,
            'count'      => count($rows),
            'activities' => array_map(function($a) use ($today) {
                $hasProgress = (int)$a['report_count'] > 0;
                $proj        = $a['projected_duration'] !== null ? (int)$a['projected_duration'] : null;
                $planned     = (int)$a['planned_duration'];
                // Days overdue: ongoing → proj - planned; upcoming → today - start_date
                if ($hasProgress) {
                    $days_overdue = ($proj !== null && $proj > $planned) ? $proj - $planned : 0;
                } else {
                    $days_overdue = (!empty($a['start_date']) && $a['start_date'] < $today)
                        ? (int)floor((strtotime($today) - strtotime($a['start_date'])) / 86400)
                        : 0;
                }
                return [
                    'name'                => $a['name'],
                    'start_date'          => $a['start_date'],
                    'end_date'            => $a['end_date'],
                    'planned_duration'    => $planned,
                    'projected_duration'  => $proj,
                    'planned_qty'         => (float)$a['quantity'],
                    'qty_done'            => (float)$a['qty_done'],
                    'unit'                => $a['unit'],
                    'progress_percent'    => (float)$a['progress_pct'],
                    'is_completed'        => (int)$a['completed_status'] === 1,
                    'is_critical'         => $a['critical_status'] === 'Yes',
                    'has_progress'        => $hasProgress,
                    'days_overdue'        => $days_overdue,
                ];
            }, $rows),
        ];
    }

    private function toolGetMaterials($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid          = (int)$project['Project_Id'];
        $materialName = trim($input['material_name'] ?? '');
        $matFilter    = '';
        $params       = [':pid' => $pid];
        if ($materialName !== '') {
            $matFilter = "AND r.Name LIKE :mname";
            $params[':mname'] = '%' . $materialName . '%';
        }

        $grns = $db->createCommand("
            SELECT r.Name AS material, r.Unit,
                   SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))) AS total_qty,
                   SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))*g.GRN_Rate) AS total_value,
                   MAX(g.GRN_Date) AS last_grn_date
            FROM goods_received_note g
            JOIN resources r ON r.Resource_Id=g.GRN_Item
            JOIN purchase_orders po ON po.order_id=g.GRN_Purchase_Order
            WHERE po.project_id=:pid AND g.delete_status=0 AND po.delete_status=0 {$matFilter}
            GROUP BY r.Resource_Id, r.Name, r.Unit ORDER BY total_value DESC LIMIT 100
        ", $params)->queryAll();

        $pos = $db->createCommand("
            SELECT por.resource_name AS material, por.unit,
                   SUM(por.qnty) AS ordered_qty, SUM(por.amount) AS ordered_value,
                   COUNT(DISTINCT po.order_id) AS po_count
            FROM purchase_order_resources por
            JOIN purchase_orders po ON po.order_id=por.order_id
            WHERE po.project_id=:pid AND po.delete_status=0 AND por.delete_status=0
            GROUP BY por.resource_name, por.unit ORDER BY ordered_value DESC LIMIT 100
        ", [':pid' => $pid])->queryAll();

        if (empty($grns) && empty($pos)) {
            return ['status' => 'no_data', 'reason' => "No material data found for \"{$project['Name']}\"."];
        }
        return ['status' => 'ok', 'project' => $project['Name'], 'grn_received' => $grns ?: [], 'purchase_orders' => $pos ?: []];
    }

    private function toolGetStock($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid          = (int)$project['Project_Id'];
        $materialName = trim($input['material_name'] ?? '');
        $matFilter    = '';
        $params       = [':pid' => $pid];
        if ($materialName !== '') {
            $matFilter = "AND LOWER(r.Name) LIKE :mname";
            $params[':mname'] = '%' . strtolower($materialName) . '%';
        }

        $rows = $db->createCommand("
            SELECT r.Name AS material, r.Unit,
                   SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))) AS total_received,
                   MAX(g.GRN_Date) AS last_grn_date
            FROM goods_received_note g
            JOIN resources r ON r.Resource_Id=g.GRN_Item
            WHERE g.GRN_Project=:pid AND g.delete_status=0 {$matFilter}
            GROUP BY r.Resource_Id, r.Name, r.Unit ORDER BY r.Name ASC LIMIT 100
        ", $params)->queryAll();

        if (empty($rows)) {
            return ['status' => 'no_data', 'reason' => "No GRN receipts found for \"{$project['Name']}\"" . ($materialName ? " matching \"{$materialName}\"" : '') . '.'];
        }
        return [
            'status'  => 'ok',
            'project' => $project['Name'],
            'count'   => count($rows),
            'stock'   => array_map(fn($r) => [
                'material'       => $r['material'],
                'unit'           => $r['Unit'],
                'total_received' => round((float)$r['total_received'], 3),
                'last_grn_date'  => $r['last_grn_date'],
            ], $rows),
        ];
    }

    private function toolGetProjectEstimate($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid  = (int)$project['Project_Id'];
        $rows = $db->createCommand(
            "SELECT pen.activity_qty, COALESCE(SUM(pern.rate*pern.quantity),0) AS unit_cost
             FROM pricing_estimate_new pen
             LEFT JOIN pricing_estimate_resources_new pern
               ON pern.activity_id=pen.activity_Id AND pern.project_id=:pid2 AND pern.pricing_status=0
             WHERE pen.project_Id=:pid AND pen.pricing_status=0
             GROUP BY pen.pricing_estimate_Id, pen.activity_qty",
            [':pid' => $pid, ':pid2' => $pid]
        )->queryAll();

        if (empty($rows)) return ['status' => 'no_data', 'reason' => "No pricing estimate found for \"{$project['Name']}\"."];

        $totalEstimate = 0.0;
        foreach ($rows as $r) { $totalEstimate += (float)$r['activity_qty'] * (float)$r['unit_cost']; }

        $contractValue = (float)$project['project_value'];
        $profitability = $contractValue > 0 ? round($contractValue - $totalEstimate, 2) : null;
        $marginPct     = ($contractValue > 0 && $totalEstimate > 0)
            ? round(($profitability / $contractValue) * 100, 1) : null;

        return [
            'status'                  => 'ok',
            'project'                 => $project['Name'],
            'total_estimate_cost'     => round($totalEstimate, 2),
            'contract_value'          => $contractValue,
            'estimated_profitability' => $profitability,
            'margin_percent'          => $marginPct,
        ];
    }

    private function toolGetWorkOrdersAndMb($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid          = (int)$project['Project_Id'];
        $activityName = trim($input['activity_name'] ?? '');

        $wos = $db->createCommand("
            SELECT wo.WO_Number, wo.Date_requested, wo.Scope,
                   wo.Quantity, wo.Unit, wo.Rate, wo.Total, wo.Duration, wo.start_date,
                   v.Name AS vendor_name, wo.WO_Subject
            FROM work_order wo
            LEFT JOIN vendors v ON v.Vendor_Id=wo.WO_Vendor
            WHERE wo.Project_Id=:pid ORDER BY wo.Date_requested DESC LIMIT 100
        ", [':pid' => $pid])->queryAll();

        $mbs = $db->createCommand("
            SELECT mb.mb_number, mb.mb_date, mb.wo_number, mb.entries
            FROM wo_measurement_book mb
            WHERE mb.project_id=:pid AND mb.delete_status=0 AND mb.sent_status=1
            ORDER BY mb.mb_date DESC LIMIT 100
        ", [':pid' => $pid])->queryAll();

        if (empty($wos) && empty($mbs)) {
            return ['status' => 'no_data', 'reason' => "No work orders or MBs found for \"{$project['Name']}\"."];
        }

        $woResult = array_map(function($wo) {
            $items    = json_decode($wo['WO_Subject'] ?? '[]', true);
            $actNames = is_array($items) ? array_filter(array_column($items, 'activity_name')) : [];
            return [
                'wo_number'  => $wo['WO_Number'],
                'date'       => $wo['Date_requested'],
                'vendor'     => $wo['vendor_name'],
                'scope'      => $wo['Scope'],
                'activities' => array_values($actNames),
                'total'      => (float)$wo['Total'],
            ];
        }, $wos);

        $mbResult = [];
        foreach ($mbs as $mb) {
            $entries = json_decode($mb['entries'] ?? '[]', true) ?: [];
            foreach ($entries as $entry) {
                $entryActName = $entry['activity_name'] ?? '';
                if ($activityName !== '' && stripos($entryActName, $activityName) === false) continue;
                foreach ($entry['tasks'] ?? [] as $task) {
                    $mbResult[] = [
                        'mb_number' => $mb['mb_number'],
                        'date'      => $mb['mb_date'],
                        'wo_number' => $mb['wo_number'],
                        'activity'  => $entryActName,
                        'task'      => $task['task_name'] ?? '',
                        'work_done' => (float)($task['work_done'] ?? 0),
                        'rate'      => (float)($task['rate'] ?? 0),
                        'value'     => round((float)($task['work_done'] ?? 0) * (float)($task['rate'] ?? 0), 2),
                    ];
                }
            }
        }

        return ['status' => 'ok', 'project' => $project['Name'], 'work_orders' => $woResult, 'measurement_books' => $mbResult];
    }

    private function toolGetProjectScheduleSummary($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid = (int)$project['Project_Id'];

        $schedRow = $db->createCommand(
            "SELECT MIN(start_date) AS plan_start, MAX(end_date) AS plan_end,
                    COUNT(*) AS total_acts, SUM(completed_status) AS completed_acts,
                    SUM(CASE WHEN completed_status=0 AND end_date<CURDATE() THEN 1 ELSE 0 END) AS overdue_acts,
                    SUM(CASE WHEN critical_status='Yes' THEN 1 ELSE 0 END) AS critical_acts,
                    SUM(CASE WHEN critical_status='Yes' AND completed_status=0 AND end_date<CURDATE() THEN 1 ELSE 0 END) AS critical_overdue
             FROM scheduleactivities WHERE projectId=:pid AND status=0 AND old_duration>0",
            [':pid' => $pid]
        )->queryOne();

        $progressRow = $db->createCommand(
            "SELECT AVG(CASE WHEN sa.quantity>0 AND rpt.cumulated_qty>0
                              THEN LEAST(rpt.cumulated_qty/sa.quantity*100,100)
                              WHEN sa.completed_status=1 THEN 100 ELSE 0 END) AS actual_pct,
                    AVG(CASE WHEN sa.old_duration>0 AND sa.start_date<=CURDATE()
                              THEN LEAST(DATEDIFF(CURDATE(),sa.start_date)/sa.old_duration*100,100)
                              WHEN sa.completed_status=1 THEN 100 ELSE 0 END) AS planned_pct
             FROM scheduleactivities sa
             LEFT JOIN (SELECT activity_id, MAX(cumulated_qty) AS cumulated_qty
                        FROM schedule_progress_report GROUP BY activity_id) rpt ON rpt.activity_id=sa.id
             WHERE sa.projectId=:pid AND sa.status=0",
            [':pid' => $pid]
        )->queryOne();

        $critDelay = $db->createCommand(
            "SELECT MAX(DATEDIFF(CURDATE(),end_date)) AS max_delay
             FROM scheduleactivities
             WHERE projectId=:pid AND status=0 AND critical_status='Yes' AND completed_status=0 AND end_date<CURDATE()",
            [':pid' => $pid]
        )->queryScalar();

        $actualPct  = round((float)($progressRow['actual_pct']  ?? 0), 1);
        $plannedPct = round((float)($progressRow['planned_pct'] ?? 0), 1);
        $planEnd    = $schedRow['plan_end'] ?? null;
        $critDelay  = max(0, (int)($critDelay ?? 0));
        $forecastEnd = ($planEnd && $critDelay > 0)
            ? date('Y-m-d', strtotime($planEnd . ' +' . $critDelay . ' days'))
            : $planEnd;

        return [
            'status'                => 'ok',
            'project'               => $project['Name'],
            'plan_start'            => $schedRow['plan_start'] ?? null,
            'plan_end'              => $planEnd,
            'forecast_end'          => $forecastEnd,
            'critical_delay_days'   => $critDelay,
            'total_activities'      => (int)($schedRow['total_acts']      ?? 0),
            'completed_activities'  => (int)($schedRow['completed_acts']  ?? 0),
            'overdue_activities'    => (int)($schedRow['overdue_acts']    ?? 0),
            'critical_activities'   => (int)($schedRow['critical_acts']   ?? 0),
            'critical_overdue'      => (int)($schedRow['critical_overdue']?? 0),
            'actual_progress_pct'   => $actualPct,
            'planned_progress_pct'  => $plannedPct,
            'schedule_variance_pct' => round($actualPct - $plannedPct, 1),
        ];
    }

    private function toolQueryDatabase($db, $input)
    {
        $sql    = trim($input['sql']    ?? '');
        $reason = trim($input['reason'] ?? '');
        if ($sql === '') return ['status' => 'no_data', 'reason' => 'No SQL provided.'];

        $err = $this->validateSelectOnly($sql);
        if ($err) return ['status' => 'rejected', 'reason' => $err];

        if (!preg_match('/\bLIMIT\s+\d+/i', $sql)) {
            $sql = rtrim($sql, '; ') . ' LIMIT 50';
        } else {
            $sql = preg_replace_callback('/\bLIMIT\s+(\d+)/i', fn($m) => 'LIMIT ' . min((int)$m[1], 50), $sql);
        }

        try {
            $rows = $db->createCommand($sql)->queryAll();
        } catch (\Exception $e) {
            return ['status' => 'error', 'reason' => 'Query failed: ' . $e->getMessage()];
        }

        if (empty($rows)) return ['status' => 'no_data', 'reason' => 'Query returned no rows.', 'query_used' => $sql];

        return ['status' => 'ok', 'row_count' => count($rows), 'rows' => $rows, 'query_used' => $sql, 'query_reason' => $reason];
    }

    // =======================================================================
    // Tool definitions
    // =======================================================================
    private function toolDefinitions()
    {
        return [
            [
                'name'        => 'get_project_costs',
                'description' => 'Returns project-level cost summary: estimated cost, actual cost, difference, ECWD, ACWD. Use for "how much has been spent on X project", "what is the total cost of Y project", overall budget questions.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => ['project_name' => ['type' => 'string']],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_group_costs',
                'description' => 'Returns cost breakdown by group (top-level WBS grouping) for a project. Optionally filter to a specific group. Use when the user asks about cost by group or wants a cost breakdown at group level.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string'],
                        'group_name'   => ['type' => 'string', 'description' => 'Optional: partial group name to filter to one group.'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_iow_costs',
                'description' => 'Returns cost breakdown by IOW (Item of Work) for a project. Optionally filter to a specific IOW. Use when the user asks about cost by IOW or work package.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string'],
                        'iow_name'     => ['type' => 'string', 'description' => 'Optional: partial IOW name to filter.'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_activity_costs',
                'description' => 'Returns the cost dashboard activity table: estimated unit cost, quantity done, estimated cost, actual cost, difference for each activity. Optionally filter to one activity by partial name. Use for "show me cost of all activities" or "what is the estimated cost of drain clearing".',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'activity_name' => ['type' => 'string', 'description' => 'Optional: partial activity name to filter to one activity.'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_activity_unit_cost',
                'description' => 'Returns the estimated and actual unit cost of a single activity (cost per one unit of output, e.g. cost per metre of drain). Use when the user asks "what is the unit cost of [activity]" or "cost per metre/cum/nos of [activity]".',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'activity_name' => ['type' => 'string'],
                    ],
                    'required' => ['project_name', 'activity_name'],
                ],
            ],
            [
                'name'        => 'get_resource_unit_cost',
                'description' => 'Returns the estimated unit cost vs actual unit cost for each resource within an activity. Use when the user asks "what is the rate of reinforcement in drain clearing", "actual vs estimated rate of cement", "unit cost of [resource] in [activity]".',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'activity_name' => ['type' => 'string'],
                        'resource_name' => ['type' => 'string', 'description' => 'Optional: partial resource name to filter to specific resources.'],
                    ],
                    'required' => ['project_name', 'activity_name'],
                ],
            ],
            [
                'name'        => 'get_resource_consumption',
                'description' => 'Returns planned vs actual consumption per resource for an activity (quantity of resource used per unit of activity output). Use when the user asks "how much cement per metre", "actual consumption of reinforcement", "planned vs actual usage of [resource]".',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'activity_name' => ['type' => 'string'],
                        'resource_name' => ['type' => 'string', 'description' => 'Optional: filter to a specific resource.'],
                    ],
                    'required' => ['project_name', 'activity_name'],
                ],
            ],
            [
                'name'        => 'get_resource_cost_by_type',
                'description' => 'Returns estimated vs actual cost grouped by resource type (Material, Labour, Plant, Subcontractor etc.) for an activity. Use when the user asks "how much of the cost is material vs labour", "cost breakdown by type for [activity]", "what is the material cost of [activity]".',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'activity_name' => ['type' => 'string'],
                    ],
                    'required' => ['project_name', 'activity_name'],
                ],
            ],
            [
                'name'        => 'get_projects',
                'description' => 'Returns the list of all active projects with contract value, client, location, start and end dates.',
                'input_schema' => ['type' => 'object', 'properties' => (object)[], 'required' => []],
            ],
            [
                'name'        => 'get_activity_kpi',
                'description' => 'Returns production KPIs for a single schedule activity: planned vs actual quantity, progress %, production rates, cycle times, tasks, cause-of-delay breakdown.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'activity_name' => ['type' => 'string'],
                    ],
                    'required' => ['activity_name'],
                ],
            ],
            [
                'name'        => 'get_schedule_activities',
                'description' => 'Returns list of schedule activities with dates, quantities, progress %, delay status. Filter: all, critical, delayed, completed, ongoing.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string'],
                        'filter'       => ['type' => 'string', 'enum' => ['all','critical','delayed','completed','ongoing']],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_materials',
                'description' => 'Returns materials received (GRN) and purchase orders for a project.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'material_name' => ['type' => 'string', 'description' => 'Optional filter by material name.'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_stock',
                'description' => 'Returns material stock position at site: total quantity received via GRN.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'material_name' => ['type' => 'string', 'description' => 'Optional filter.'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_project_estimate',
                'description' => 'Returns the total estimated cost (full BOQ/budget) and contract margin. Use when the user asks about total budget or profitability.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => ['project_name' => ['type' => 'string']],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_work_orders_and_mb',
                'description' => 'Returns work orders and measurement book entries for a project, showing subcontractor scope, quantities, and amounts certified.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'activity_name' => ['type' => 'string', 'description' => 'Optional filter by activity.'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_project_schedule_summary',
                'description' => 'Returns schedule health: progress %, planned vs actual, critical activities, delays, forecast completion.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => ['project_name' => ['type' => 'string']],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'query_database',
                'description' => 'Executes a raw SELECT query. Use ONLY when no other tool covers the question.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'sql'    => ['type' => 'string'],
                        'reason' => ['type' => 'string', 'description' => 'Why no other tool covers this.'],
                    ],
                    'required' => ['sql', 'reason'],
                ],
            ],
        ];
    }

    // =======================================================================
    // Tool dispatcher
    // =======================================================================
    private function executeTool($name, $input)
    {
        $db = Yii::$app->db;
        switch ($name) {
            case 'get_project_costs':         return $this->toolGetProjectCosts($db, $input);
            case 'get_group_costs':           return $this->toolGetGroupCosts($db, $input);
            case 'get_iow_costs':             return $this->toolGetIowCosts($db, $input);
            case 'get_activity_costs':        return $this->toolGetActivityCosts($db, $input);
            case 'get_activity_unit_cost':    return $this->toolGetActivityUnitCost($db, $input);
            case 'get_resource_unit_cost':    return $this->toolGetResourceUnitCost($db, $input);
            case 'get_resource_consumption':  return $this->toolGetResourceConsumption($db, $input);
            case 'get_resource_cost_by_type': return $this->toolGetResourceCostByType($db, $input);
            case 'get_projects':              return $this->toolGetProjects($db);
            case 'get_activity_kpi':          return $this->toolGetActivityKpi($db, $input);
            case 'get_schedule_activities':   return $this->toolGetScheduleActivities($db, $input);
            case 'get_materials':             return $this->toolGetMaterials($db, $input);
            case 'get_stock':                 return $this->toolGetStock($db, $input);
            case 'get_project_estimate':      return $this->toolGetProjectEstimate($db, $input);
            case 'get_work_orders_and_mb':    return $this->toolGetWorkOrdersAndMb($db, $input);
            case 'get_project_schedule_summary': return $this->toolGetProjectScheduleSummary($db, $input);
            case 'query_database':            return $this->toolQueryDatabase($db, $input);
            default:
                return ['status' => 'no_data', 'reason' => "Unknown tool: {$name}"];
        }
    }

    // =======================================================================
    // Main action — tool-calling loop
    // =======================================================================
    public function actionChat()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $message    = trim(Yii::$app->request->post('message', ''));
        $historyRaw = Yii::$app->request->post('history', '[]');
        $history    = is_array($historyRaw) ? $historyRaw : (json_decode($historyRaw, true) ?: []);

        if (!$message) return ['error' => 'No message'];

        $apiKey = $this->getApiKey();
        if (!$apiKey) return ['error' => 'API key not configured'];

        $requestSessionId = substr(md5(uniqid('', true)), 0, 16);

        $systemPrompt =
            "You are a project assistant here to provide key project information to support decision making. " .
            "Do not mention or identify yourself as part of any software, ERP, or platform. " .
            "If asked who you are or what you do, say: 'I am a project assistant. I provide you with key project information to support your decisions.'\n\n" .

            "## HOW TO ANSWER COST QUESTIONS\n\n" .
            "You have one tool per dashboard panel. Each tool runs the exact same calculation as the live cost dashboard. " .
            "Pick the right tool for the question and read the result directly — never do arithmetic yourself.\n\n" .

            "Cost panel tools (use these for all cost/estimate/actual questions):\n" .
            "- get_project_costs → total project estimated cost, actual cost, ECWD, ACWD\n" .
            "- get_group_costs → cost broken down by group (pass group_name to filter to one group)\n" .
            "- get_iow_costs → cost broken down by IOW/work package\n" .
            "- get_activity_costs → the activity cost table: est cost, actual cost, difference per activity\n" .
            "- get_activity_unit_cost → estimated and actual unit cost of one activity (cost per metre / cum / nos etc.)\n" .
            "- get_resource_unit_cost → estimated vs actual unit cost per resource within an activity\n" .
            "- get_resource_consumption → planned vs actual consumption per resource within an activity\n" .
            "- get_resource_cost_by_type → cost grouped by resource type (Material, Labour, Plant, Subcontractor)\n\n" .

            "KPI panel tools (use these for all schedule/performance questions):\n" .
            "- get_activity_kpi → all production KPIs for one activity: work done %, productivity, cycle times, capacity, elapsed, projected duration, tasks, cause-of-delay\n\n" .

            "Other tools:\n" .
            "- get_projects → list of active projects\n" .
            "- get_schedule_activities → activity list with dates, progress, delay status\n" .
            "- get_project_schedule_summary → schedule health, overall progress %, forecast completion\n" .
            "- get_materials / get_stock → GRN receipts, purchase orders, site stock\n" .
            "- get_work_orders_and_mb → subcontractor work orders and measurement books\n" .
            "- get_project_estimate → total BOQ budget and profit margin\n" .
            "- query_database → raw SELECT only when no other tool covers the question\n\n" .

            "## ARITHMETIC RULE\n" .
            "By default, never compute or derive numbers yourself — every figure must come directly from a tool result field. " .
            "If a value is not in the tool result, say it is not available.\n" .
            "EXCEPTION: if the user explicitly asks you to compute an average (e.g. 'what is the average cost per activity', " .
            "'average unit cost across these activities', 'compute the average for me'), you may calculate a simple average " .
            "from values already returned by tools in the same conversation. State clearly that you computed this average " .
            "from the tool data and show the inputs used (e.g. 'average of X, Y, Z = ...'). " .
            "Do NOT perform any other arithmetic (totals, differences, percentages, ratios) — those must still come from tool results.\n\n" .

            "## ACTUALS AVAILABILITY\n" .
            "Tool results include a has_actual flag. When has_actual is false, actual cost is not yet recorded — " .
            "tell the user 'no actuals recorded yet' rather than showing the estimated value as if it were actual.\n\n" .

            "## ANSWER FORMAT\n" .
            "Answer in a natural, conversational tone — like a knowledgeable colleague briefly explaining the numbers. " .
            "Give the figure asked for, add one or two sentences of relevant context if it helps (e.g. whether the activity is on track, over budget, behind schedule), then stop. " .
            "Do not write long paragraphs or bullet lists unless the user asks for a breakdown.\n" .
            "- Never mention tool names, field names, or JSON keys in your answer.\n" .
            "- For ambiguous matches (multiple activities/groups), list candidates and ask the user to clarify.\n" .
            "- Do not use emojis.\n" .
            "- Do not use the phrase 'for context' or 'it is worth noting' — just say it naturally.\n" .
            "- If the tool returns an as_of timestamp, you may mention the data date naturally (e.g. 'as of 12 July') only when freshness is relevant to the answer.\n\n" .

            "## COST TERMINOLOGY — read this carefully before answering any cost question\n\n" .

            "### ESTIMATED vs ACTUAL — the fundamental distinction\n" .
            "ESTIMATED (also called planned, budgeted) = what was planned BEFORE work started. Set during project estimation/BOQ.\n" .
            "ACTUAL = what has really happened based on recorded site data (GRN receipts, measurement books, progress reports).\n" .
            "These are NEVER interchangeable. If the user asks for actual and you only have estimated, say so explicitly.\n\n" .

            "### Activity-level fields (from get_project_costs, get_group_costs, get_iow_costs, get_activity_costs)\n" .
            "- estimated_cost → the PLANNED total cost for that item (set in BOQ/estimate). Answer questions like 'what is the budget for X'.\n" .
            "- actual_cost → what has ACTUALLY been spent on that item so far. Answer 'how much has been spent on X'.\n" .
            "- difference → estimated_cost minus actual_cost. Positive = under budget. Negative = over budget.\n" .
            "- ecwd (Estimated Cost of Work Done) → the PLANNED cost of only the portion of work completed so far. This is the budget value of work done, NOT the total budget.\n" .
            "- acwd (Actual Cost of Work Done) → what was ACTUALLY spent on the work completed so far.\n" .
            "- ecwd_acwd_diff → ecwd minus acwd. Shows whether work done so far cost more or less than planned.\n\n" .

            "### Activity unit cost fields (from get_activity_costs, get_activity_unit_cost)\n" .
            "- estimated_unit_cost → PLANNED cost to complete ONE unit of activity output (e.g. ₹ per metre of drain, ₹ per cum of concrete). Derived from the estimate.\n" .
            "- actual_unit_cost (from get_activity_unit_cost) → what it ACTUALLY cost per unit of output based on site data. Only present when has_actual=true.\n" .
            "Do NOT confuse estimated_unit_cost with actual_unit_cost. If the user asks 'what is the unit cost' without specifying, return BOTH and label each clearly.\n\n" .

            "### Resource-level fields (from get_resource_unit_cost, get_resource_consumption, get_resource_cost_by_type)\n" .
            "- est_unit_cost → PLANNED rate per resource unit (e.g. ₹/kg of steel, ₹/hour of labour). From the pricing estimate.\n" .
            "- act_unit_cost → ACTUAL rate paid per resource unit. For materials: weighted average GRN purchase price. For subcontractors: weighted average MB rate. NULL means no actual data recorded yet.\n" .
            "- planned_consumption → ESTIMATED quantity of this resource consumed per unit of activity output (e.g. 0.05 kg of steel per metre of drain). From the estimate.\n" .
            "- actual_consumption → ACTUAL quantity of this resource consumed per unit of activity output, derived from GRN receipts or MB entries.\n" .
            "- est_cost → PLANNED cost contribution of this resource per unit of activity output (est_unit_cost × planned_consumption).\n" .
            "- act_cost → ACTUAL cost contribution of this resource per unit of activity output (act_unit_cost × actual_consumption). NULL if no actuals.\n\n" .

            "### PLANNED vs ACTUAL — summary cheat sheet\n" .
            "| User asks for | Field to use | Source tool |\n" .
            "| budget / estimated cost of activity | estimated_cost | get_activity_costs |\n" .
            "| actual spend on activity | actual_cost | get_activity_costs |\n" .
            "| planned unit cost of activity | estimated_unit_cost | get_activity_costs or get_activity_unit_cost |\n" .
            "| actual unit cost of activity | actual_unit_cost | get_activity_unit_cost |\n" .
            "| planned rate of a resource | est_unit_cost | get_resource_unit_cost |\n" .
            "| actual rate / actual price of a resource | act_unit_cost | get_resource_unit_cost |\n" .
            "| planned quantity of resource used | planned_consumption | get_resource_consumption |\n" .
            "| actual quantity of resource used | actual_consumption | get_resource_consumption |\n" .
            "| cost of work done so far (budget side) | ecwd | get_activity_costs or get_project_costs |\n" .
            "| cost of work done so far (actual side) | acwd | get_activity_costs or get_project_costs |\n\n" .

            "## KPI TERMINOLOGY — read this carefully before answering any schedule/performance question\n\n" .

            "All KPI values come from get_activity_kpi. Field names match the live KPI dashboard exactly.\n\n" .

            "### Progress & Quantity (KPI dashboard: Work Done panel)\n" .
            "- work_done_pct → % of scheduled quantity completed. Dashboard label: work done %.\n" .
            "- target_qty → total scheduled quantity for the activity (e.g. 500 cum, 1200 m).\n" .
            "- actual_qty → cumulative quantity completed to date (latest progress report).\n" .
            "- unit → unit of measurement (cum, m, nos, etc.).\n\n" .

            "### Duration & Schedule (KPI dashboard: Activity Duration panel)\n" .
            "- b_duration → planned/budgeted duration in days (from schedule). Dashboard label: planned duration.\n" .
            "- duration → current schedule duration in days (may differ from b_duration if revised).\n" .
            "- elapsed → days elapsed from act_start_date to last_reported_date + 1. This is what PHP uses for all KPI computations. NOT the same as today minus start date.\n" .
            "- start_delay → days overdue before any progress was recorded (only set when actual_qty = 0 but start date has passed).\n" .
            "- projected_duration → forecast total duration = round(elapsed / actual_qty × target_qty). Dashboard label: projected duration.\n" .
            "- act_start_date → earlier of planned_start and reported_start. The activity's effective start anchor.\n" .
            "- planned_end_date → originally scheduled completion date.\n" .
            "- adj_start_date → adjusted start date (= planned_start if available).\n" .
            "- adj_end_date → adjusted end date computed from adj_start_date + b_duration - 1 days.\n\n" .

            "### Productivity (KPI dashboard: Target Production / Productivity panels)\n" .
            "CRITICAL DISTINCTION — these two terms are completely different:\n" .
            "- 'target production' or 'production' of an activity = the TOTAL QUANTITY completed from start to today = actual_qty. e.g. '63 metres of drain cleared'. This is what the user means when they ask 'what is the target production' or 'what is the production of this activity'.\n" .
            "- 'productivity' or 'production rate' or 'production per day' = how fast work is progressing = target_productivity (planned) or actual_productivity (actual). e.g. '13.33 m/day'. Only use these when the user specifically asks for rate, speed, or per-day figures.\n" .
            "NEVER answer a 'target production' question with the per-day rate. NEVER answer a 'productivity' question with the total quantity.\n" .
            "- target_productivity (= planned_per_day) → target_qty / b_duration. The planned daily rate. Use only when user asks for productivity, rate, or per-day output.\n" .
            "- actual_productivity → actual_qty / elapsed. The actual daily rate achieved so far.\n" .
            "- target_qty → the planned total quantity for the full activity. Use when user asks 'what is the target' or 'what is the planned quantity'.\n" .
            "- actual_qty → the quantity completed to date. Use when user asks 'what is the production so far', 'how much has been done', or 'target production'.\n" .
            "- resource_units → number of resource crews/gangs deployed.\n" .
            "- wh → work hours per day.\n\n" .

            "### Cycle Time (KPI dashboard: Cycle Time panel)\n" .
            "- target_cycle_time → (b_duration / target_qty) × wh. Time in hours to produce one unit at planned rate.\n" .
            "- actual_cycle_time → (elapsed / actual_qty) × wh. Actual hours per unit based on progress to date.\n" .
            "  IMPORTANT: When actual_cycle_time = 0 (no progress yet), the dashboard shows target_cycle_time as the current cycle time. Follow this same fallback: if actual_cycle_time is 0, report target_cycle_time as the current cycle time.\n\n" .

            "### Capacity Utilisation (KPI dashboard: Capacity panel)\n" .
            "- cap_max → elapsed × wh. Total available work-hours over elapsed period.\n" .
            "- cap_used → max(0, cap_max − cumulative_break_hours). Productive hours used.\n" .
            "- capacity % = cap_used / cap_max × 100. Only meaningful when progress has been reported.\n\n" .

            "### Tasks (KPI dashboard: Resource Productivity panel)\n" .
            "Each task in the 'tasks' array has:\n" .
            "- name → task name.\n" .
            "- unit → task unit.\n" .
            "- val → target production rate = productivity × max(1, resource_units). Dashboard label: target.\n" .
            "- actual → actual task production rate = actual_qty × task_qty / elapsed. Dashboard label: actual.\n" .
            "- qty → total planned task quantity = task_qty × target_qty.\n" .
            "- planned_duration → budgeted duration for this task in days.\n" .
            "- actual_duration → elapsed / mb_work_done (from measurement book). 0 if no MB entries.\n\n" .

            "### Cause of Delay (KPI dashboard: Cause of Delay panel)\n" .
            "Each entry in 'cause_of_delay' has: name (reason), count (number of occurrences), pct (% of total delay reports).\n\n" .

            "### KPI CHEAT SHEET\n" .
            "| User asks for | Field | Notes |\n" .
            "| work done / progress % | work_done_pct | |\n" .
            "| quantity completed | actual_qty + unit | |\n" .
            "| planned quantity / target | target_qty + unit | |\n" .
            "| planned duration | b_duration | in days |\n" .
            "| projected / forecast duration | projected_duration | in days |\n" .
            "| days elapsed | elapsed | last_reported_date − act_start_date + 1 |\n" .
            "| start delay | start_delay | days overdue before first progress |\n" .
            "| planned start | adj_start_date | |\n" .
            "| planned end / scheduled completion | adj_end_date | |\n" .
            "| target production per day | target_productivity | |\n" .
            "| actual production per day | actual_productivity | |\n" .
            "| target cycle time | target_cycle_time | hours per unit |\n" .
            "| actual cycle time | actual_cycle_time (fallback: target_cycle_time when 0) | hours per unit |\n" .
            "| capacity available | cap_max | hours |\n" .
            "| capacity used / productive hours | cap_used | hours |\n" .
            "| task target production rate | tasks[].val | |\n" .
            "| task actual production rate | tasks[].actual | |\n\n" .

            "## COST REASONING STYLE\n" .
            "When explaining cost, use this vocabulary:\n" .
            "- Every resource has two cost levers: (1) the rate/price paid for it, and (2) how much of it was used relative to plan.\n" .
            "- A price issue points to procurement (sourcing, vendor, rate negotiation). A usage issue points to execution: for materials/consumables this means yield and wastage; for equipment/labour this means productivity and duration.\n" .
            "- Activity cost = resources consumed × their rates, scaled by quantity of work done. Group/IOW/project cost is the roll-up of its activities. A high-level overrun always traces back to specific activities and specific resources.\n\n" .
            "Rules:\n" .
            "- Only explain WHY using this vocabulary if you have actual figures from a tool result to point to. If you don't have the specific breakdown, say so plainly — never present a specific cause for this project unless the data actually shows it.\n" .
            "- Never invent or calculate a number yourself. Always report figures exactly as retrieved.\n" .
            "- Keep general explanation clearly separate in wording from confirmed data (e.g. 'in general, this pattern often relates to...' vs. 'the data shows...').\n\n" .

            "## GROUNDING RULE\n" .
            "Only state facts from tool results. If a tool returns no_data, say that information is not available. " .
            "Never guess or fill gaps from general knowledge.\n\n" .

            "TODAY'S DATE: " . date('d F Y') . ".";

        // Build message history — plain text turns only
        $messages = [];
        foreach ((array)$history as $h) {
            if (empty($h['role']) || !isset($h['content'])) continue;
            $content = $h['content'];
            if (!is_string($content)) continue;
            if (trim($content) === '') continue;
            $messages[] = ['role' => $h['role'], 'content' => $content];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        $tools = $this->toolDefinitions();

        for ($round = 0; $round < 8; $round++) {
            $payload = json_encode([
                'model'       => 'claude-sonnet-4-6',
                'max_tokens'  => 1500,
                'temperature' => 0.1,
                'system'      => $systemPrompt,
                'tools'       => $tools,
                'messages'    => $messages,
            ]);

            $ch = curl_init('https://api.anthropic.com/v1/messages');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: '         . $apiKey,
                    'anthropic-version: 2023-06-01',
                    'content-type: application/json',
                ],
                CURLOPT_TIMEOUT => 60,
            ]);
            $response = curl_exec($ch);
            $err      = curl_error($ch);
            curl_close($ch);

            if ($err) return ['error' => 'API error: ' . $err];

            $data = json_decode($response, true);
            if (isset($data['error'])) {
                return ['error' => 'API: ' . ($data['error']['message'] ?? substr($response, 0, 300))];
            }

            $stopReason = $data['stop_reason'] ?? '';
            $content    = $data['content']     ?? [];

            if ($stopReason === 'end_turn') {
                foreach ($content as $block) {
                    if ($block['type'] === 'text') {
                        $this->updateLogReply($requestSessionId, $block['text']);
                        return ['reply' => $block['text']];
                    }
                }
                return ['reply' => 'No response.'];
            }

            if ($stopReason === 'tool_use') {
                // Re-encode: empty tool inputs must be {} (object) not [] (array) for the API
                $contentForHistory = array_map(function($block) {
                    if (($block['type'] ?? '') === 'tool_use' && isset($block['input']) && $block['input'] === []) {
                        $block['input'] = (object)[];
                    }
                    return $block;
                }, $content);
                $messages[] = ['role' => 'assistant', 'content' => $contentForHistory];

                $toolResults = [];
                foreach ($content as $block) {
                    if ($block['type'] !== 'tool_use') continue;

                    $toolName  = $block['name'];
                    $toolInput = is_array($block['input'] ?? null) ? $block['input'] : [];
                    $toolUseId = $block['id'];

                    $result = $this->executeTool($toolName, $toolInput);
                    $this->logToolCall($requestSessionId, $toolName, $toolInput, $result);

                    $toolResults[] = [
                        'type'        => 'tool_result',
                        'tool_use_id' => $toolUseId,
                        'content'     => json_encode($result),
                    ];
                }

                $messages[] = ['role' => 'user', 'content' => $toolResults];
                continue;
            }

            foreach ($content as $block) {
                if ($block['type'] === 'text') return ['reply' => $block['text']];
            }
            return ['reply' => 'Unexpected response from API.'];
        }

        return ['reply' => 'Could not complete request after multiple attempts.'];
    }
}
