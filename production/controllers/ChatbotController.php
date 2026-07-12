<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

/**
 * ChatbotController — Dashboard Chatbot (v3)
 *
 * Architecture (per spec):
 *   load_project_dashboard  — runs once per project per session, writes 4 cache tables
 *   get_dashboard_metric    — pure SELECT from cache tables, never computes
 *
 * All cost values come from the SAME queries as actionCostdashboardbatch() in
 * ProjectsmainController, ensuring numbers match the live dashboard exactly.
 *
 * Session = one open browser conversation.  60-min idle → treated as new session.
 * computed_at is surfaced on every answer so users know data freshness.
 */
class ChatbotController extends Controller
{
    public $enableCsrfValidation = false;

    // Session idle timeout in seconds (60 minutes)
    const SESSION_TIMEOUT = 3600;

    public function behaviors()
    {
        return [];
    }

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
    // SELECT-only validator (for query_database tool)
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
    // Core computation — mirrors actionCostdashboardbatch() exactly
    // Returns array keyed by scheduleactivities.id:
    //   est, acoa, estwd, actwd, resources (array of per-resource rows)
    // -----------------------------------------------------------------------
    private function computeActivityCosts($db, $pid)
    {
        $schedActs = $db->createCommand(
            "SELECT sa.id, sa.activity_id, sa.quantity, sa.name
             FROM scheduleactivities sa WHERE sa.projectId=:pid AND sa.status=0",
            [':pid' => $pid]
        )->queryAll();

        if (empty($schedActs)) return [];

        $actIds = array_column($schedActs, 'id');
        $wbIds  = array_unique(array_column($schedActs, 'activity_id'));
        $wbIds  = array_filter(array_map('intval', $wbIds));

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

        // Resources per WBS id — same JOIN as actionCostdashboardbatch, plus names for cache
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

        // MB data — task amounts and work done
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

        // Compute per activity — mirrors actionCostdashboardbatch exactly.
        // Single pass per resource: accumulates activity-level totals AND captures
        // per-resource detail (actUnit, actualConsumption, typeName) for cache tables.
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
            $resCache    = [];

            foreach ($resources as $r) {
                $typeId             = (int)$r['type_id'];
                $resQty             = (float)$r['res_qty'];
                $estRate            = (float)$r['rate'];
                $plannedConsumption = $resQty * $ratio;

                $unitCost += $resQty * $estRate;

                if ($typeId === 4) {
                    $taskIds = array_filter(array_map('intval', explode(',', $r['task_ids'] ?? '')));
                    $wdVal = 0.0; $wdQty = 0.0;
                    foreach ($taskIds as $tid) {
                        $wdVal += $taskAmountById[$tid]   ?? 0.0;
                        $wdQty += $taskWorkDoneById[$tid] ?? 0.0;
                    }
                    $actUnit  = $wdQty > 0 ? $wdVal / $wdQty : null;
                    $scWd     = $wdQty; // same variable, already summed above
                    $actualConsumption = ($lastQty > 0 && $scWd > 0)
                        ? $scWd / $lastQty : $plannedConsumption;
                } elseif (in_array($typeId, [2, 6, 7, 8])) {
                    $actUnit      = ($r['actual_unit_cost'] !== null) ? (float)$r['actual_unit_cost'] : null;
                    $indentRaised = ($r['stock_at_site'] !== null);
                    if ($indentRaised && $lastQty > 0) {
                        $actualConsumption = max(0, (float)($r['grn_qty'] ?? 0) - (float)($r['stock_at_site'] ?? 0)) / $lastQty;
                    } else {
                        $actualConsumption = $plannedConsumption;
                    }
                } else {
                    $actUnit           = null;
                    $actualConsumption = $plannedConsumption;
                }

                if ($actUnit !== null) {
                    $actualTotal += $actUnit * $actualConsumption;
                }

                $resName = $r['resource_name'] ?? '';
                if ($resName === '') continue;

                $resCache[] = [
                    'resource_name'      => $resName,
                    'resource_unit'      => $r['resource_unit'] ?? '',
                    'resource_type_name' => $r['type_name'] ?? '',
                    'qty_per_unit'       => $resQty,
                    'est_unit_cost'      => $estRate,
                    'act_unit_cost'      => $actUnit,
                    'planned_consumption'=> $plannedConsumption,
                    'actual_consumption' => $actualConsumption,
                    'est_cost'           => round($estRate * $plannedConsumption, 4),
                    'act_cost'           => $actUnit !== null ? round($actUnit * $actualConsumption, 4) : null,
                ];
            }

            $estUCTotal = $unitCost * $ratio;

            $data[$schedId] = [
                'wbId'      => $wbId,
                'name'      => $sa['name'],
                'schedQty'  => $schedQty,
                'lastQty'   => $lastQty,
                'unitCost'  => round($unitCost, 4),
                'est'       => round($unitCost * $estQty, 2),
                'acoa'      => round($actualTotal * $schedQty, 2),
                'estwd'     => round($estUCTotal * $lastQty, 2),
                'actwd'     => $actualTotal > 0 ? round($actualTotal * $lastQty, 2) : round($estUCTotal * $lastQty, 2),
                'resources' => $resCache,
            ];
        }
        return $data;
    }

    // -----------------------------------------------------------------------
    // Tool definitions — two core tools + supporting tools
    // -----------------------------------------------------------------------
    private function toolDefinitions()
    {
        return [
            // ── Primary: load once per session ──────────────────────────────
            [
                'name'        => 'load_project_dashboard',
                'description' =>
                    'Selects a project and computes all cost dashboard metrics at every level ' .
                    '(project, groups, IOW, activities) using the same calculations as the live dashboard. ' .
                    'Call this ONCE when a project is first mentioned in a session, or when the user switches ' .
                    'to a different project not yet loaded this session. ' .
                    'Do NOT call again for a project already loaded this session — check session state first. ' .
                    'After this completes, use get_dashboard_metric to answer all cost questions.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string', 'description' => 'Full or partial project name'],
                    ],
                    'required' => ['project_name'],
                ],
            ],

            // ── Primary: read from cache ────────────────────────────────────
            [
                'name'        => 'get_dashboard_metric',
                'description' =>
                    'Reads cost dashboard metrics from the session cache tables. ' .
                    'Pure read — never computes. ' .
                    'Use for ALL cost/estimate/actual questions once the project is loaded. ' .
                    'If it returns reason="project_not_loaded", call load_project_dashboard first then retry. ' .
                    'Always include the computed_at timestamp in your answer.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string'],
                        'level'        => [
                            'type' => 'string',
                            'enum' => ['project', 'group', 'iow', 'activity'],
                            'description' => 'Hierarchy level to read from',
                        ],
                        'item_name'    => [
                            'type'        => 'string',
                            'description' => 'Name of the group/IOW/activity. Omit if level=project.',
                        ],
                        'field'        => [
                            'type'        => 'string',
                            'description' => 'Optional specific metric. If omitted, all metrics for that row are returned.',
                        ],
                    ],
                    'required' => ['project_name', 'level'],
                ],
            ],

            // ── Supporting tools (unchanged) ────────────────────────────────
            [
                'name'        => 'get_projects',
                'description' => 'Returns the list of all active projects with contract value, client, location, start date and end date.',
                'input_schema' => ['type' => 'object', 'properties' => (object)[], 'required' => []],
            ],
            [
                'name'        => 'get_activity_kpi',
                'description' => 'Returns KPI data for a single schedule activity: planned vs actual quantity, progress %, production rates, cycle times, tasks, cause-of-delay breakdown. Use for specific activity performance questions.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string', 'description' => 'Full or partial project name (optional if unambiguous)'],
                        'activity_name' => ['type' => 'string', 'description' => 'Full or partial activity name'],
                    ],
                    'required' => ['activity_name'],
                ],
            ],
            [
                'name'        => 'get_schedule_activities',
                'description' => 'Returns list of schedule activities for a project with dates, quantities, progress %, delay status.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string'],
                        'filter'       => ['type' => 'string', 'enum' => ['all','critical','delayed','completed','ongoing']],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_materials',
                'description' => 'Returns materials received (GRN), purchase orders, and store indent stock for a project.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'material_name' => ['type' => 'string', 'description' => 'Optional filter by material name'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_stock',
                'description' => 'Returns material stock position at site: total quantity received via GRN per material.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'material_name' => ['type' => 'string', 'description' => 'Optional filter'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_project_estimate',
                'description' => 'Returns the total estimated cost (full BOQ/budget) of a project and contract margin. Use when user asks about total budget or profitability, not cost of work done.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_resource_metric',
                'description' =>
                    'Reads per-resource detail from the session cache (cb_cache_resources). ' .
                    'Use when the user asks about: resource unit costs/rates ("unit cost of reinforcement", "rate of steel"), ' .
                    'actual vs estimated unit cost of a resource, resource consumption (planned vs actual), ' .
                    'resource cost by type (Material/Labour/Plant/Subcontractor), or the full resource breakdown of an activity. ' .
                    'Returns: est_unit_cost, act_unit_cost, planned_consumption, actual_consumption, est_cost, act_cost, resource_type_name per resource row. ' .
                    'Requires the project to be loaded via load_project_dashboard first. ' .
                    'If it returns reason="project_not_loaded", call load_project_dashboard first then retry.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'resource_name' => [
                            'type'        => 'string',
                            'description' => 'Full or partial resource name (e.g. "reinforcement", "cement", "clearing drain"). Omit to get all resources for the activity.',
                        ],
                        'activity_name' => [
                            'type'        => 'string',
                            'description' => 'Optional: filter by activity name. If omitted, searches across all activities.',
                        ],
                    ],
                    'required' => ['project_name', 'resource_name'],
                ],
            ],
            [
                'name'        => 'get_activity_resources',
                'description' => 'LEGACY: Use get_resource_metric instead (reads from cache). ' .
                    'This tool does a live query. Only use if get_resource_metric is unavailable or project is not loaded.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'activity_name' => ['type' => 'string'],
                    ],
                    'required' => ['project_name', 'activity_name'],
                ],
            ],
            [
                'name'        => 'get_work_orders_and_mb',
                'description' => 'Returns work orders and measurement book entries for a project, showing subcontractor scope, quantities, and amounts certified.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string'],
                        'activity_name' => ['type' => 'string', 'description' => 'Optional filter by activity'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_project_schedule_summary',
                'description' => 'Returns schedule health summary: progress %, planned vs actual, critical activities, delays, forecast completion. Use for schedule/progress questions not already in cache.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'query_database',
                'description' => 'Executes a raw SELECT query against the live database. Use ONLY for questions no other tool can answer. Query MUST be SELECT-only.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'sql'    => ['type' => 'string', 'description' => 'A valid MySQL SELECT statement.'],
                        'reason' => ['type' => 'string', 'description' => 'Why no other tool covers this question.'],
                    ],
                    'required' => ['sql', 'reason'],
                ],
            ],
        ];
    }

    // -----------------------------------------------------------------------
    // Tool dispatcher
    // -----------------------------------------------------------------------
    private function executeTool($name, $input, &$sessionLoadedProjects, $chatSessionId)
    {
        $db = Yii::$app->db;
        switch ($name) {
            case 'load_project_dashboard':
                return $this->toolLoadProjectDashboard($db, $input, $sessionLoadedProjects, $chatSessionId);
            case 'get_dashboard_metric':
                return $this->toolGetDashboardMetric($db, $input);
            case 'get_projects':
                return $this->toolGetProjects($db);
            case 'get_activity_kpi':
                return $this->toolGetActivityKpi($db, $input);
            case 'get_schedule_activities':
                return $this->toolGetScheduleActivities($db, $input);
            case 'get_materials':
                return $this->toolGetMaterials($db, $input);
            case 'get_stock':
                return $this->toolGetStock($db, $input);
            case 'get_project_estimate':
                return $this->toolGetProjectEstimate($db, $input);
            case 'get_resource_metric':
                return $this->toolGetResourceMetric($db, $input);
            case 'get_activity_resources':
                return $this->toolGetActivityResources($db, $input);
            case 'get_work_orders_and_mb':
                return $this->toolGetWorkOrdersAndMb($db, $input);
            case 'get_project_schedule_summary':
                return $this->toolGetProjectScheduleSummary($db, $input);
            case 'query_database':
                return $this->toolQueryDatabase($db, $input);
            default:
                return ['status' => 'no_data', 'reason' => "Unknown tool: {$name}"];
        }
    }

    // =======================================================================
    // TOOL: load_project_dashboard
    // Computes all 4 cache tables in one pass using the same logic as the
    // live dashboard (actionCostdashboardbatch).
    // Session guard: skip if this project was loaded in this session and
    // last_active_at is within SESSION_TIMEOUT seconds.
    // =======================================================================
    private function toolLoadProjectDashboard($db, $input, &$sessionLoadedProjects, $chatSessionId)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid         = (int)$project['Project_Id'];
        $projectName = $project['Name'];
        $now         = date('Y-m-d H:i:s');

        // ── Session guard (PHP-side, not model-side) ─────────────────────────
        // Check if already loaded in this chat session (in-memory for this request)
        if (isset($sessionLoadedProjects[$pid])) {
            $loadedAt = $sessionLoadedProjects[$pid];
            if ((time() - $loadedAt) < self::SESSION_TIMEOUT) {
                // Already loaded and not stale — read computed_at from cache
                $ca = $db->createCommand(
                    "SELECT computed_at FROM cb_cache_project WHERE project_id=:pid LIMIT 1",
                    [':pid' => $pid]
                )->queryScalar();
                return [
                    'status'       => 'already_loaded',
                    'project'      => $projectName,
                    'project_id'   => $pid,
                    'computed_at'  => $ca ?: $now,
                    'message'      => "Dashboard for \"{$projectName}\" is already loaded this session. Use get_dashboard_metric to answer questions.",
                ];
            }
        }

        // ── Also check DB-side session cache (handles page reloads within timeout)
        $dbCache = $db->createCommand(
            "SELECT computed_at, last_active_at, chat_session_id FROM cb_cache_project
             WHERE project_id=:pid LIMIT 1",
            [':pid' => $pid]
        )->queryOne();

        if ($dbCache && $dbCache['chat_session_id'] === $chatSessionId) {
            $lastActive = strtotime($dbCache['last_active_at'] ?? '1970-01-01');
            if ((time() - $lastActive) < self::SESSION_TIMEOUT) {
                // Touch last_active_at so session stays alive
                $db->createCommand(
                    "UPDATE cb_cache_project SET last_active_at=:now WHERE project_id=:pid",
                    [':now' => $now, ':pid' => $pid]
                )->execute();
                $sessionLoadedProjects[$pid] = time();
                return [
                    'status'      => 'already_loaded',
                    'project'     => $projectName,
                    'project_id'  => $pid,
                    'computed_at' => $dbCache['computed_at'],
                    'message'     => "Dashboard for \"{$projectName}\" already loaded this session (computed at {$dbCache['computed_at']}). Use get_dashboard_metric.",
                ];
            }
        }

        // ── Compute activity costs (authoritative source) ────────────────────
        $actCosts = $this->computeActivityCosts($db, $pid);

        if (empty($actCosts)) {
            return ['status' => 'no_data', 'reason' => "No schedule activities with cost data found for \"{$projectName}\"."];
        }

        // ── Build WBS (workgroup) hierarchy: group → IOW → activities ────────
        // Groups
        $groupRows = $db->createCommand(
            "SELECT DISTINCT wg.iowGroupid AS group_id, ig.name AS group_name
             FROM workgroups_new wg
             JOIN iow_groups ig ON ig.id = wg.iowGroupid
             WHERE wg.project_Id = :pid AND wg.pricing_status = 0
             ORDER BY ig.name",
            [':pid' => $pid]
        )->queryAll();

        // IOW items (workgroups_new)
        $iowRows = $db->createCommand(
            "SELECT wg.id AS iow_id, wg.WorkGroup_Name AS iow_name, wg.iowGroupid AS group_id
             FROM workgroups_new wg
             WHERE wg.project_Id = :pid AND wg.pricing_status = 0
             ORDER BY wg.iowGroupid, wg.WorkGroup_Name",
            [':pid' => $pid]
        )->queryAll();

        // Activities with their IOW link
        $activityRows = $db->createCommand(
            "SELECT wan.id AS wan_id, wan.activity_Name AS activity_name,
                    wan.activity_Unit AS unit,
                    wg.id AS iow_id, wg.iowGroupid AS group_id,
                    pen.activity_qty
             FROM workgroup_activities_new wan
             JOIN workgroups_new wg ON wg.id = wan.WorkGroup_Id AND wg.project_Id = :pid
             JOIN pricing_estimate_new pen
               ON pen.activity_Id = wan.id AND pen.project_Id = :pid2 AND pen.pricing_status = 0
             WHERE wan.project_Id = :pid3 AND wan.pricing_status = 0",
            [':pid' => $pid, ':pid2' => $pid, ':pid3' => $pid]
        )->queryAll();

        // Map wan_id → {iow_id, group_id, activity_name, unit, activity_qty}
        $wanMap = [];
        foreach ($activityRows as $a) { $wanMap[(int)$a['wan_id']] = $a; }

        // Map scheduleactivities.id → wan_id via activity_id column
        $schedWanMap = $db->createCommand(
            "SELECT id AS sched_id, activity_id AS wan_id FROM scheduleactivities
             WHERE projectId=:pid AND status=0",
            [':pid' => $pid]
        )->queryAll();
        $schedToWan = [];
        foreach ($schedWanMap as $r) { $schedToWan[(int)$r['sched_id']] = (int)$r['wan_id']; }

        // ── Aggregate per IOW ────────────────────────────────────────────────
        // iow_id => {est, acoa, estwd, actwd}
        $iowAgg = [];
        foreach ($actCosts as $schedId => $c) {
            $wanId = $schedToWan[$schedId] ?? null;
            if (!$wanId || !isset($wanMap[$wanId])) continue;
            $iowId = (int)$wanMap[$wanId]['iow_id'];
            if (!isset($iowAgg[$iowId])) {
                $iowAgg[$iowId] = ['est' => 0.0, 'acoa' => 0.0, 'estwd' => 0.0, 'actwd' => 0.0, 'has_actual' => false];
            }
            $iowAgg[$iowId]['est']   += $c['est'];
            $iowAgg[$iowId]['estwd'] += $c['estwd'];
            if ($c['acoa'] > 0) {
                $iowAgg[$iowId]['acoa']      += $c['acoa'];
                $iowAgg[$iowId]['actwd']     += $c['actwd'];
                $iowAgg[$iowId]['has_actual'] = true;
            } else {
                // Use est as proxy when no actual (mirrors JS dashboard)
                $iowAgg[$iowId]['acoa']  += $c['est'];
                $iowAgg[$iowId]['actwd'] += $c['estwd'];
            }
        }

        // ── Aggregate per Group ──────────────────────────────────────────────
        // group_id => {est, acoa, estwd, actwd}
        $iowToGroup = [];
        foreach ($iowRows as $iow) { $iowToGroup[(int)$iow['iow_id']] = (int)$iow['group_id']; }

        $grpAgg = [];
        foreach ($iowAgg as $iowId => $vals) {
            $gid = $iowToGroup[$iowId] ?? null;
            if ($gid === null) continue;
            if (!isset($grpAgg[$gid])) {
                $grpAgg[$gid] = ['est' => 0.0, 'acoa' => 0.0, 'estwd' => 0.0, 'actwd' => 0.0];
            }
            $grpAgg[$gid]['est']   += $vals['est'];
            $grpAgg[$gid]['acoa']  += $vals['acoa'];
            $grpAgg[$gid]['estwd'] += $vals['estwd'];
            $grpAgg[$gid]['actwd'] += $vals['actwd'];
        }

        // ── Project totals ───────────────────────────────────────────────────
        $totEst = 0.0; $totAcoa = 0.0; $totEstWD = 0.0; $totActWD = 0.0;
        foreach ($actCosts as $c) {
            $totEst   += $c['est'];
            $totEstWD += $c['estwd'];
            if ($c['acoa'] > 0) {
                $totAcoa  += $c['acoa'];
                $totActWD += $c['actwd'];
            } else {
                $totAcoa  += $c['est'];
                $totActWD += $c['estwd'];
            }
        }

        // ── Write to cache tables (REPLACE INTO = upsert) ───────────────────
        // 1. Project
        $db->createCommand()->delete('cb_cache_project', ['project_id' => $pid])->execute();
        $db->createCommand()->insert('cb_cache_project', [
            'project_id'       => $pid,
            'project_name'     => $projectName,
            'client_name'      => $project['client_name'],
            'location'         => $project['location'],
            'contract_value'   => (float)$project['project_value'],
            'estimated_cost'   => round($totEst,   2),
            'actual_cost'      => round($totAcoa,  2),
            'difference'       => round($totEst - $totAcoa, 2),
            'ecwd'             => round($totEstWD, 2),
            'acwd'             => round($totActWD, 2),
            'ecwd_acwd_diff'   => round($totEstWD - $totActWD, 2),
            'computed_at'      => $now,
            'chat_session_id'  => $chatSessionId,
            'last_active_at'   => $now,
        ])->execute();

        // 2. Groups
        $db->createCommand()->delete('cb_cache_groups', ['project_id' => $pid])->execute();
        foreach ($groupRows as $g) {
            $gid  = (int)$g['group_id'];
            $agg  = $grpAgg[$gid] ?? ['est' => 0, 'acoa' => 0, 'estwd' => 0, 'actwd' => 0];
            $db->createCommand()->insert('cb_cache_groups', [
                'project_id'     => $pid,
                'group_id'       => $gid,
                'group_name'     => $g['group_name'],
                'estimated_cost' => round($agg['est'],  2),
                'actual_cost'    => round($agg['acoa'], 2),
                'difference'     => round($agg['est'] - $agg['acoa'], 2),
                'ecwd'           => round($agg['estwd'], 2),
                'acwd'           => round($agg['actwd'], 2),
                'ecwd_acwd_diff' => round($agg['estwd'] - $agg['actwd'], 2),
                'computed_at'    => $now,
            ])->execute();
        }

        // 3. IOW
        $db->createCommand()->delete('cb_cache_iow', ['project_id' => $pid])->execute();
        foreach ($iowRows as $iow) {
            $iid = (int)$iow['iow_id'];
            $agg = $iowAgg[$iid] ?? ['est' => 0, 'acoa' => 0, 'estwd' => 0, 'actwd' => 0];
            $db->createCommand()->insert('cb_cache_iow', [
                'project_id'     => $pid,
                'iow_id'         => $iid,
                'group_id'       => (int)$iow['group_id'],
                'iow_name'       => $iow['iow_name'],
                'estimated_cost' => round($agg['est'],  2),
                'actual_cost'    => round($agg['acoa'], 2),
                'difference'     => round($agg['est'] - $agg['acoa'], 2),
                'ecwd'           => round($agg['estwd'], 2),
                'acwd'           => round($agg['actwd'], 2),
                'ecwd_acwd_diff' => round($agg['estwd'] - $agg['actwd'], 2),
                'computed_at'    => $now,
            ])->execute();
        }

        // 4. Activities
        $db->createCommand()->delete('cb_cache_activities', ['project_id' => $pid])->execute();
        foreach ($actCosts as $schedId => $c) {
            $wanId = $schedToWan[$schedId] ?? null;
            $wan   = ($wanId && isset($wanMap[$wanId])) ? $wanMap[$wanId] : null;
            $db->createCommand()->insert('cb_cache_activities', [
                'project_id'          => $pid,
                'sched_activity_id'   => $schedId,
                'wan_id'              => $wanId ?? 0,
                'iow_id'              => $wan ? (int)$wan['iow_id']   : 0,
                'group_id'            => $wan ? (int)$wan['group_id'] : 0,
                'activity_name'       => $c['name'],
                'unit'                => $wan ? $wan['unit'] : '',
                'activity_qty'        => $wan ? (float)$wan['activity_qty'] : 0,
                'qty_done'            => $c['lastQty'],
                'est_cost_activity'   => $c['est'],
                'unit_cost'           => $c['unitCost'],
                'actual_cost_activity'=> $c['acoa'],
                'difference'          => round($c['est'] - $c['acoa'], 2),
                'ecwd'                => $c['estwd'],
                'acwd'                => $c['actwd'],
                'ecwd_acwd_diff'      => round($c['estwd'] - $c['actwd'], 2),
                'computed_at'         => $now,
            ])->execute();
        }

        // 5. cb_cache_resources — per-resource per activity (estimated + actual unit cost + consumption)
        $db->createCommand()->delete('cb_cache_resources', ['project_id' => $pid])->execute();
        foreach ($actCosts as $schedId => $c) {
            $wanId = $schedToWan[$schedId] ?? null;
            foreach ($c['resources'] as $res) {
                $db->createCommand()->insert('cb_cache_resources', [
                    'project_id'         => $pid,
                    'sched_activity_id'  => $schedId,
                    'wan_id'             => $wanId ?? 0,
                    'activity_name'      => $c['name'],
                    'resource_name'      => $res['resource_name'],
                    'resource_unit'      => $res['resource_unit'],
                    'resource_type_name' => $res['resource_type_name'],
                    'qty_per_unit'       => $res['qty_per_unit'],
                    'est_unit_cost'      => $res['est_unit_cost'],
                    'act_unit_cost'      => $res['act_unit_cost'],
                    'planned_consumption'=> $res['planned_consumption'],
                    'actual_consumption' => $res['actual_consumption'],
                    'est_cost'           => $res['est_cost'],
                    'act_cost'           => $res['act_cost'],
                    'computed_at'        => $now,
                ])->execute();
            }
        }

        // Mark loaded in this request's memory
        $sessionLoadedProjects[$pid] = time();

        // Summary counts
        $groupCount    = count($groupRows);
        $iowCount      = count($iowRows);
        $activityCount = count($actCosts);

        return [
            'status'         => 'ok',
            'project'        => $projectName,
            'project_id'     => $pid,
            'computed_at'    => $now,
            'loaded_counts'  => [
                'groups'     => $groupCount,
                'iow_items'  => $iowCount,
                'activities' => $activityCount,
            ],
            'project_totals' => [
                'estimated_cost' => round($totEst,   2),
                'actual_cost'    => round($totAcoa,  2),
                'difference'     => round($totEst - $totAcoa, 2),
                'ecwd'           => round($totEstWD, 2),
                'acwd'           => round($totActWD, 2),
                'ecwd_acwd_diff' => round($totEstWD - $totActWD, 2),
            ],
            'message' => "Dashboard loaded for \"{$projectName}\" ({$groupCount} groups, {$iowCount} IOWs, {$activityCount} activities). Now use get_dashboard_metric to answer questions.",
        ];
    }

    // =======================================================================
    // TOOL: get_dashboard_metric
    // Pure read from cache tables. Never computes.
    // =======================================================================
    private function toolGetDashboardMetric($db, $input)
    {
        $projectName = trim($input['project_name'] ?? '');
        $level       = $input['level']     ?? 'project';
        $itemName    = trim($input['item_name'] ?? '');
        $field       = trim($input['field']     ?? '');

        if ($projectName === '') {
            return ['matched' => false, 'reason' => 'project_name is required.'];
        }

        // Resolve project
        $project = $this->resolveProject($db, $projectName);
        if (isset($project['status'])) {
            return ['matched' => false, 'reason' => $project['reason']];
        }
        $pid = (int)$project['Project_Id'];

        // Check project is loaded
        $cacheCheck = $db->createCommand(
            "SELECT computed_at FROM cb_cache_project WHERE project_id=:pid LIMIT 1",
            [':pid' => $pid]
        )->queryScalar();

        if (!$cacheCheck) {
            return [
                'matched' => false,
                'reason'  => 'project_not_loaded',
                'message' => "Dashboard for \"{$project['Name']}\" has not been loaded this session. Call load_project_dashboard first.",
            ];
        }

        // Update last_active_at to keep session alive
        $db->createCommand(
            "UPDATE cb_cache_project SET last_active_at=NOW() WHERE project_id=:pid",
            [':pid' => $pid]
        )->execute();

        switch ($level) {
            case 'project':
                return $this->readProjectLevel($db, $pid, $project['Name'], $field);
            case 'group':
                return $this->readGroupLevel($db, $pid, $project['Name'], $itemName, $field);
            case 'iow':
                return $this->readIowLevel($db, $pid, $project['Name'], $itemName, $field);
            case 'activity':
                return $this->readActivityLevel($db, $pid, $project['Name'], $itemName, $field);
            default:
                return ['matched' => false, 'reason' => "Unknown level \"{$level}\". Use: project, group, iow, activity."];
        }
    }

    private function readProjectLevel($db, $pid, $projectName, $field)
    {
        $row = $db->createCommand(
            "SELECT * FROM cb_cache_project WHERE project_id=:pid LIMIT 1",
            [':pid' => $pid]
        )->queryOne();

        if (!$row) return ['matched' => false, 'reason' => 'project_not_loaded'];

        $values = [
            'contract_value'  => (float)$row['contract_value'],
            'estimated_cost'  => (float)$row['estimated_cost'],
            'actual_cost'     => (float)$row['actual_cost'],
            'difference'      => (float)$row['difference'],
            'ecwd'            => (float)$row['ecwd'],
            'acwd'            => (float)$row['acwd'],
            'ecwd_acwd_diff'  => (float)$row['ecwd_acwd_diff'],
        ];

        return [
            'matched'     => true,
            'project'     => $projectName,
            'level'       => 'project',
            'computed_at' => $row['computed_at'],
            'values'      => $field !== '' && isset($values[$field])
                ? [$field => $values[$field]]
                : $values,
        ];
    }

    private function readGroupLevel($db, $pid, $projectName, $itemName, $field)
    {
        if ($itemName === '') {
            // Return all groups
            $rows = $db->createCommand(
                "SELECT * FROM cb_cache_groups WHERE project_id=:pid ORDER BY group_name",
                [':pid' => $pid]
            )->queryAll();
            if (empty($rows)) return ['matched' => false, 'reason' => 'No groups found for this project.'];
            return [
                'matched'     => true,
                'project'     => $projectName,
                'level'       => 'group',
                'computed_at' => $rows[0]['computed_at'],
                'rows'        => array_map(fn($r) => $this->formatCacheRow($r, 'group_name'), $rows),
            ];
        }

        $rows = $db->createCommand(
            "SELECT * FROM cb_cache_groups WHERE project_id=:pid AND LOWER(group_name) LIKE :n",
            [':pid' => $pid, ':n' => '%' . strtolower($itemName) . '%']
        )->queryAll();

        return $this->returnMatchedRows($rows, 'group', $projectName, $itemName, 'group_name', $field);
    }

    private function readIowLevel($db, $pid, $projectName, $itemName, $field)
    {
        if ($itemName === '') {
            $rows = $db->createCommand(
                "SELECT ci.*, cg.group_name FROM cb_cache_iow ci
                 LEFT JOIN cb_cache_groups cg ON cg.project_id=ci.project_id AND cg.group_id=ci.group_id
                 WHERE ci.project_id=:pid ORDER BY cg.group_name, ci.iow_name",
                [':pid' => $pid]
            )->queryAll();
            if (empty($rows)) return ['matched' => false, 'reason' => 'No IOW items found for this project.'];
            return [
                'matched'     => true,
                'project'     => $projectName,
                'level'       => 'iow',
                'computed_at' => $rows[0]['computed_at'],
                'rows'        => array_map(fn($r) => $this->formatCacheRow($r, 'iow_name', 'group_name'), $rows),
            ];
        }

        $rows = $db->createCommand(
            "SELECT ci.*, cg.group_name FROM cb_cache_iow ci
             LEFT JOIN cb_cache_groups cg ON cg.project_id=ci.project_id AND cg.group_id=ci.group_id
             WHERE ci.project_id=:pid AND LOWER(ci.iow_name) LIKE :n",
            [':pid' => $pid, ':n' => '%' . strtolower($itemName) . '%']
        )->queryAll();

        return $this->returnMatchedRows($rows, 'iow', $projectName, $itemName, 'iow_name', $field);
    }

    private function readActivityLevel($db, $pid, $projectName, $itemName, $field)
    {
        if ($itemName === '') {
            $rows = $db->createCommand(
                "SELECT ca.*, ci.iow_name, cg.group_name
                 FROM cb_cache_activities ca
                 LEFT JOIN cb_cache_iow ci ON ci.project_id=ca.project_id AND ci.iow_id=ca.iow_id
                 LEFT JOIN cb_cache_groups cg ON cg.project_id=ca.project_id AND cg.group_id=ca.group_id
                 WHERE ca.project_id=:pid ORDER BY cg.group_name, ci.iow_name, ca.activity_name",
                [':pid' => $pid]
            )->queryAll();
            if (empty($rows)) return ['matched' => false, 'reason' => 'No activities found for this project.'];
            return [
                'matched'     => true,
                'project'     => $projectName,
                'level'       => 'activity',
                'computed_at' => $rows[0]['computed_at'],
                'rows'        => array_map(fn($r) => $this->formatActivityRow($r), $rows),
            ];
        }

        // Fuzzy match on activity name
        $raw   = preg_replace('/[^a-z0-9 ]/i', ' ', $itemName);
        $words = array_values(array_filter(explode(' ', strtolower($raw)), fn($w) => strlen($w) >= 3));
        $where = 'ca.project_id=:pid';
        $params = [':pid' => $pid];
        foreach ($words as $i => $w) {
            $key = ':w' . $i;
            $where .= " AND LOWER(ca.activity_name) LIKE {$key}";
            $params[$key] = '%' . rtrim($w, 's') . '%';
        }
        if (empty($words)) {
            $where .= ' AND LOWER(ca.activity_name) LIKE :wfull';
            $params[':wfull'] = '%' . strtolower($itemName) . '%';
        }

        $rows = $db->createCommand(
            "SELECT ca.*, ci.iow_name, cg.group_name
             FROM cb_cache_activities ca
             LEFT JOIN cb_cache_iow ci ON ci.project_id=ca.project_id AND ci.iow_id=ca.iow_id
             LEFT JOIN cb_cache_groups cg ON cg.project_id=ca.project_id AND cg.group_id=ca.group_id
             WHERE {$where}
             ORDER BY ca.activity_name LIMIT 20",
            $params
        )->queryAll();

        if (empty($rows)) {
            return ['matched' => false, 'candidates' => [], 'reason' => "No activity matching \"{$itemName}\" found in \"{$projectName}\"."];
        }
        if (count($rows) > 1) {
            return [
                'matched'    => false,
                'candidates' => array_map(fn($r) => [
                    'activity_name' => $r['activity_name'],
                    'iow_name'      => $r['iow_name'] ?? '',
                    'group_name'    => $r['group_name'] ?? '',
                ], $rows),
                'reason' => 'Multiple activities matched. Ask the user to clarify which one.',
            ];
        }

        $r      = $rows[0];
        $values = [
            'activity_qty'         => (float)$r['activity_qty'],
            'qty_done'             => (float)$r['qty_done'],
            'unit'                 => $r['unit'],
            'est_cost_activity'    => (float)$r['est_cost_activity'],
            'unit_cost'            => (float)$r['unit_cost'],
            'actual_cost_activity' => (float)$r['actual_cost_activity'],
            'difference'           => (float)$r['difference'],
            'ecwd'                 => (float)$r['ecwd'],
            'acwd'                 => (float)$r['acwd'],
            'ecwd_acwd_diff'       => (float)$r['ecwd_acwd_diff'],
        ];

        return [
            'matched'      => true,
            'project'      => $projectName,
            'level'        => 'activity',
            'item'         => $r['activity_name'],
            'group'        => $r['group_name'] ?? '',
            'iow'          => $r['iow_name']   ?? '',
            'computed_at'  => $r['computed_at'],
            'values'       => $field !== '' && isset($values[$field])
                ? [$field => $values[$field]]
                : $values,
        ];
    }

    private function formatCacheRow($r, $nameKey, $parentKey = null)
    {
        $out = [
            'name'           => $r[$nameKey],
            'estimated_cost' => (float)$r['estimated_cost'],
            'actual_cost'    => (float)$r['actual_cost'],
            'difference'     => (float)$r['difference'],
            'ecwd'           => (float)$r['ecwd'],
            'acwd'           => (float)$r['acwd'],
            'ecwd_acwd_diff' => (float)$r['ecwd_acwd_diff'],
        ];
        if ($parentKey && isset($r[$parentKey])) $out['parent'] = $r[$parentKey];
        return $out;
    }

    private function formatActivityRow($r)
    {
        return [
            'activity_name'        => $r['activity_name'],
            'group'                => $r['group_name'] ?? '',
            'iow'                  => $r['iow_name']   ?? '',
            'unit'                 => $r['unit'],
            'activity_qty'         => (float)$r['activity_qty'],
            'qty_done'             => (float)$r['qty_done'],
            'est_cost_activity'    => (float)$r['est_cost_activity'],
            'unit_cost'            => (float)$r['unit_cost'],
            'actual_cost_activity' => (float)$r['actual_cost_activity'],
            'difference'           => (float)$r['difference'],
            'ecwd'                 => (float)$r['ecwd'],
            'acwd'                 => (float)$r['acwd'],
            'ecwd_acwd_diff'       => (float)$r['ecwd_acwd_diff'],
        ];
    }

    private function returnMatchedRows($rows, $level, $projectName, $itemName, $nameKey, $field)
    {
        if (empty($rows)) {
            return ['matched' => false, 'candidates' => [], 'reason' => "No {$level} matching \"{$itemName}\" found in \"{$projectName}\"."];
        }
        if (count($rows) > 1) {
            return [
                'matched'    => false,
                'candidates' => array_column($rows, $nameKey),
                'reason'     => 'Multiple matches. Ask the user to clarify which one.',
            ];
        }

        $r      = $rows[0];
        $values = [
            'estimated_cost' => (float)$r['estimated_cost'],
            'actual_cost'    => (float)$r['actual_cost'],
            'difference'     => (float)$r['difference'],
            'ecwd'           => (float)$r['ecwd'],
            'acwd'           => (float)$r['acwd'],
            'ecwd_acwd_diff' => (float)$r['ecwd_acwd_diff'],
        ];

        return [
            'matched'     => true,
            'project'     => $projectName,
            'level'       => $level,
            'item'        => $r[$nameKey],
            'computed_at' => $r['computed_at'],
            'values'      => $field !== '' && isset($values[$field])
                ? [$field => $values[$field]]
                : $values,
        ];
    }

    // =======================================================================
    // Supporting tools (unchanged from v2)
    // =======================================================================

    private function toolGetProjects($db)
    {
        $rows = $db->createCommand(
            "SELECT Project_Id, Name, client_name, location, start_date, end_date, project_value
             FROM projects WHERE Status = 0 ORDER BY Name ASC LIMIT 50"
        )->queryAll();

        if (empty($rows)) {
            return ['status' => 'no_data', 'reason' => 'No active projects found.'];
        }
        return [
            'status'   => 'ok',
            'count'    => count($rows),
            'projects' => array_map(fn($p) => [
                'id'             => (int)$p['Project_Id'],
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

        if ($activityName === '') {
            return ['status' => 'no_data', 'reason' => 'activity_name is required.'];
        }

        $where  = "sa.status = 0";
        $params = [];

        if ($projectName !== '') {
            $where .= " AND LOWER(p.Name) LIKE :pname";
            $params[':pname'] = '%' . strtolower($projectName) . '%';
        }

        $raw   = preg_replace('/[^a-z0-9 ]/i', ' ', $activityName);
        $words = array_values(array_filter(explode(' ', $raw), fn($w) => strlen($w) >= 4));
        foreach ($words as $i => $word) {
            $key  = ':aw' . $i;
            $where .= " AND LOWER(sa.name) LIKE {$key}";
            $params[$key] = '%' . strtolower(rtrim($word, 's')) . '%';
        }

        $acts = $db->createCommand("
            SELECT sa.id, sa.name, sa.duration, sa.old_duration, sa.unit,
                   sa.quantity, sa.completed_status, sa.start_date,
                   sa.actual_start_date, sa.actual_end_date, sa.end_date,
                   sa.resource_units, sa.critical_status,
                   sa.projectId AS pid, p.Name AS project_name
            FROM scheduleactivities sa
            JOIN projects p ON p.Project_Id = sa.projectId AND p.Status = 0
            WHERE {$where}
            ORDER BY sa.start_date DESC LIMIT 5
        ", $params)->queryAll();

        if (empty($acts)) {
            return ['status' => 'no_data', 'reason' => "No schedule activity found matching \"{$activityName}\"" . ($projectName ? " in \"{$projectName}\"" : '') . '.'];
        }
        if (count($acts) > 1) {
            return [
                'status'             => 'ambiguous',
                'reason'             => 'Multiple activities matched. Ask the user to specify which one.',
                'matched_activities' => array_map(fn($a) => [
                    'id'         => (int)$a['id'],
                    'name'       => $a['name'],
                    'project'    => $a['project_name'],
                    'start_date' => $a['start_date'],
                    'end_date'   => $a['end_date'],
                ], $acts),
            ];
        }

        $act   = $acts[0];
        $actid = (int)$act['id'];
        $pid   = (int)$act['pid'];

        $san = $db->createCommand(
            "SELECT progress, Workhours, Cycles, Resourceunits FROM schedule_activity_new WHERE actvity_id=$actid"
        )->queryOne();

        $target_qty = (float)($act['quantity'] ?? 0);
        $unit       = $act['unit'] ?? '';
        $wh         = $san ? (int)$san['Workhours'] : 8;
        $progress   = $san ? (float)$san['progress'] : 0;

        $pr = $db->createCommand(
            "SELECT cumulated_qty FROM schedule_progress_report WHERE activity_id=$actid ORDER BY updated_at DESC LIMIT 1"
        )->queryOne();
        $actual_qty     = $pr ? (float)$pr['cumulated_qty'] : 0;
        $work_done_pct  = ($target_qty > 0) ? round($actual_qty / $target_qty * 100, 1) : $progress;
        $b_duration     = (float)($act['old_duration'] ?? 0);
        $planned_per_day = ($b_duration > 0 && $target_qty > 0) ? round($target_qty / $b_duration, 3) : 0;

        $brk = $db->createCommand(
            "SELECT SUM(break_hour) AS total_break FROM schedule_progress_report_log WHERE activity_id=$actid"
        )->queryOne();
        $cum_break = $brk ? (float)$brk['total_break'] : 0;

        $lrd = $db->createCommand(
            "SELECT MAX(report_date) AS last_date FROM schedule_progress_report_log WHERE activity_id=$actid AND currentqty > 0"
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

        $actual_prod = 0; $elapsed = 0; $start_delay = 0; $cap_max = 0; $cap_used = 0; $actual_cycle = 0;

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

        $cod_rows = $db->createCommand(
            "SELECT cd.title, COUNT(strc.id) AS cnt
             FROM schedule_task_report_cause_of_delays strc
             JOIN cause_of_delays cd ON cd.id = strc.cause_of_delay_id
             WHERE strc.activity_id=$actid GROUP BY cd.id, cd.title"
        )->queryAll();
        $total_cod = array_sum(array_column($cod_rows, 'cnt'));
        $cause_of_delay = array_map(fn($r) => [
            'reason'  => $r['title'],
            'count'   => (int)$r['cnt'],
            'percent' => $total_cod > 0 ? round($r['cnt'] / $total_cod * 100) : 0,
        ], $cod_rows);

        $sa_act    = $db->createCommand("SELECT activity_id FROM scheduleactivities WHERE id=$actid")->queryOne();
        $wbn_id    = $sa_act ? (int)$sa_act['activity_id'] : 0;
        $wbn_row   = $wbn_id ? $db->createCommand("SELECT activity_Id FROM workgroup_activities_new WHERE id=$wbn_id")->queryOne() : null;
        $masterActId = ($wbn_row && $wbn_row['activity_Id']) ? (int)$wbn_row['activity_Id'] : $wbn_id;

        $task_rows = $masterActId ? $db->createCommand(
            "SELECT at.id AS task_id, at.task_name, at.task_unit,
                    COALESCE(stn.task_productivity, at.productivity, 0) AS productivity,
                    COALESCE(stn.task_resource_units, 1) AS resource_units,
                    COALESCE(stn.task_qty, 0) AS task_qty,
                    COALESCE(stn.Budgeted_Duration, 0) AS planned_duration
             FROM activity_tasks at
             LEFT JOIN schedule_task_new stn ON stn.task_Id = at.id AND stn.activity_Id = $actid
             WHERE at.activity_id = $masterActId ORDER BY at.sort_order ASC"
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

        $tasks = array_map(function($t) use ($target_qty, $actual_qty, $elapsed, $taskMbQty) {
            $tqu   = (float)$t['task_qty'];
            $mbQty = $taskMbQty[(int)$t['task_id']] ?? 0;
            return [
                'task_name'                 => $t['task_name'],
                'unit'                      => $t['task_unit'],
                'target_production_per_day' => round((float)$t['productivity'] * max(1, (float)$t['resource_units']), 3),
                'productivity_per_resource' => (float)$t['productivity'],
                'resource_units'            => (float)$t['resource_units'],
                'task_planned_qty'          => round($tqu * $target_qty, 3),
                'planned_duration_days'     => (float)$t['planned_duration'],
                'actual_duration_days'      => ($elapsed > 0 && $mbQty > 0) ? round($elapsed / $mbQty, 3) : 0,
            ];
        }, $task_rows);

        $target_cycle = ($target_qty > 0 && $b_duration > 0) ? round(($b_duration / $target_qty) * $wh, 3) : 0;

        return [
            'status'                   => 'ok',
            'project'                  => $act['project_name'],
            'activity'                 => $act['name'],
            'unit'                     => $unit,
            'target_qty'               => $target_qty,
            'actual_qty_done'          => $actual_qty,
            'progress_percent'         => $work_done_pct,
            'planned_duration_days'    => $b_duration,
            'elapsed_days'             => (int)round($elapsed),
            'start_delay_days'         => $start_delay,
            'projected_total_duration' => $projected_duration,
            'planned_start_date'       => $planned_start,
            'planned_end_date'         => $act['end_date'] ?? '',
            'reported_start_date'      => $reported_start ?: null,
            'last_reported_date'       => $last_reported_date ?: null,
            'target_production_per_day'=> $planned_per_day,
            'actual_production_per_day'=> $actual_prod,
            'work_hours_per_day'       => $wh,
            'target_cycle_time_hrs'    => $target_cycle,
            'actual_cycle_time_hrs'    => $actual_cycle,
            'capacity_max_hrs'         => $cap_max,
            'capacity_used_hrs'        => $cap_used,
            'is_critical'              => ($act['critical_status'] === 'Yes'),
            'is_completed'             => ((int)$act['completed_status'] === 1),
            'cause_of_delay'           => empty($cause_of_delay)
                ? ['status' => 'no_data', 'reason' => 'No cause-of-delay entries recorded.']
                : $cause_of_delay,
            'tasks'                    => empty($tasks)
                ? ['status' => 'no_data', 'reason' => 'No tasks defined for this activity.']
                : $tasks,
        ];
    }

    private function toolGetScheduleActivities($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid    = (int)$project['Project_Id'];
        $filter = $input['filter'] ?? 'all';
        $having = '';
        switch ($filter) {
            case 'critical':  $having = "AND sa.critical_status = 'Yes'"; break;
            case 'delayed':   $having = "AND sa.completed_status = 0 AND sa.end_date < CURDATE()"; break;
            case 'completed': $having = "AND sa.completed_status = 1"; break;
            case 'ongoing':   $having = "AND sa.completed_status = 0"; break;
        }

        $rows = $db->createCommand("
            SELECT sa.name, sa.start_date, sa.end_date,
                   sa.old_duration AS planned_duration, sa.quantity, sa.unit,
                   sa.completed_status, sa.critical_status,
                   COALESCE(rpt.cumulated_qty, 0) AS qty_done,
                   CASE
                     WHEN sa.quantity > 0 AND COALESCE(rpt.cumulated_qty,0) > 0
                     THEN ROUND(COALESCE(rpt.cumulated_qty,0)/sa.quantity*100,1)
                     WHEN sa.completed_status = 1 THEN 100 ELSE 0
                   END AS progress_pct,
                   CASE
                     WHEN sa.completed_status = 0 AND sa.end_date < CURDATE()
                     THEN DATEDIFF(CURDATE(), sa.end_date) ELSE 0
                   END AS days_overdue
            FROM scheduleactivities sa
            LEFT JOIN (
                SELECT activity_id, MAX(cumulated_qty) AS cumulated_qty
                FROM schedule_progress_report GROUP BY activity_id
            ) rpt ON rpt.activity_id = sa.id
            WHERE sa.projectId = :pid AND sa.status = 0 {$having}
            ORDER BY sa.start_date ASC LIMIT 200
        ", [':pid' => $pid])->queryAll();

        if (empty($rows)) {
            return ['status' => 'no_data', 'reason' => "No {$filter} activities found for project \"{$project['Name']}\"."];
        }
        return [
            'status'     => 'ok',
            'project'    => $project['Name'],
            'filter'     => $filter,
            'count'      => count($rows),
            'activities' => array_map(fn($a) => [
                'name'             => $a['name'],
                'start_date'       => $a['start_date'],
                'end_date'         => $a['end_date'],
                'planned_duration' => (int)$a['planned_duration'],
                'planned_qty'      => (float)$a['quantity'],
                'qty_done'         => (float)$a['qty_done'],
                'unit'             => $a['unit'],
                'progress_percent' => (float)$a['progress_pct'],
                'is_completed'     => (int)$a['completed_status'] === 1,
                'is_critical'      => $a['critical_status'] === 'Yes',
                'days_overdue'     => (int)$a['days_overdue'],
            ], $rows),
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
            $matFilter        = "AND r.Name LIKE :mname";
            $params[':mname'] = '%' . $materialName . '%';
        }

        $grns = $db->createCommand("
            SELECT r.Name AS material, r.Unit,
                   SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))) AS total_qty,
                   SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4)) * g.GRN_Rate) AS total_value,
                   MAX(g.GRN_Date) AS last_grn_date
            FROM goods_received_note g
            JOIN resources r ON r.Resource_Id = g.GRN_Item
            JOIN purchase_orders po ON po.order_id = g.GRN_Purchase_Order
            WHERE po.project_id = :pid AND g.delete_status = 0 AND po.delete_status = 0 {$matFilter}
            GROUP BY r.Resource_Id, r.Name, r.Unit ORDER BY total_value DESC LIMIT 100
        ", $params)->queryAll();

        $pos = $db->createCommand("
            SELECT por.resource_name AS material, por.unit,
                   SUM(por.qnty) AS ordered_qty, SUM(por.amount) AS ordered_value,
                   COUNT(DISTINCT po.order_id) AS po_count
            FROM purchase_order_resources por
            JOIN purchase_orders po ON po.order_id = por.order_id
            WHERE po.project_id = :pid AND po.delete_status = 0 AND por.delete_status = 0
            GROUP BY por.resource_name, por.unit ORDER BY ordered_value DESC LIMIT 100
        ", [':pid' => $pid])->queryAll();

        if (empty($grns) && empty($pos)) {
            return ['status' => 'no_data', 'reason' => "No material data found for project \"{$project['Name']}\"."];
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
            $matFilter        = "AND LOWER(r.Name) LIKE :mname";
            $params[':mname'] = '%' . strtolower($materialName) . '%';
        }

        $rows = $db->createCommand("
            SELECT r.Name AS material, r.Unit,
                   SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))) AS total_received,
                   MAX(g.GRN_Date) AS last_grn_date
            FROM goods_received_note g
            JOIN resources r ON r.Resource_Id = g.GRN_Item
            WHERE g.GRN_Project = :pid AND g.delete_status = 0 {$matFilter}
            GROUP BY r.Resource_Id, r.Name, r.Unit ORDER BY r.Name ASC LIMIT 100
        ", $params)->queryAll();

        if (empty($rows)) {
            return ['status' => 'no_data', 'reason' => 'No GRN receipts found for project "' . $project['Name'] . '"' . ($materialName ? " matching \"{$materialName}\"" : '') . '.'];
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
            "SELECT pen.activity_qty, COALESCE(SUM(pern.rate * pern.quantity), 0) AS unit_cost
             FROM pricing_estimate_new pen
             LEFT JOIN pricing_estimate_resources_new pern
               ON pern.activity_id = pen.activity_Id AND pern.project_id = :pid2 AND pern.pricing_status = 0
             WHERE pen.project_Id = :pid AND pen.pricing_status = 0
             GROUP BY pen.pricing_estimate_Id, pen.activity_qty",
            [':pid' => $pid, ':pid2' => $pid]
        )->queryAll();

        if (empty($rows)) {
            return ['status' => 'no_data', 'reason' => "No pricing estimate found for project \"{$project['Name']}\"."];
        }

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

    // =======================================================================
    // TOOL: get_resource_metric
    // Pure read from cb_cache_resources — returns per-resource unit rates.
    // =======================================================================
    private function toolGetResourceMetric($db, $input)
    {
        $projectName  = trim($input['project_name']  ?? '');
        $resourceName = trim($input['resource_name'] ?? '');
        $activityName = trim($input['activity_name'] ?? '');

        if ($projectName === '') return ['matched' => false, 'reason' => 'project_name is required.'];
        if ($resourceName === '') return ['matched' => false, 'reason' => 'resource_name is required.'];

        $project = $this->resolveProject($db, $projectName);
        if (isset($project['status'])) return ['matched' => false, 'reason' => $project['reason']];
        $pid = (int)$project['Project_Id'];

        // Check project is loaded
        $cacheCheck = $db->createCommand(
            "SELECT computed_at FROM cb_cache_project WHERE project_id=:pid LIMIT 1",
            [':pid' => $pid]
        )->queryScalar();
        if (!$cacheCheck) {
            return [
                'matched' => false,
                'reason'  => 'project_not_loaded',
                'message' => "Dashboard for \"{$project['Name']}\" has not been loaded this session. Call load_project_dashboard first.",
            ];
        }

        // Build fuzzy match on resource_name
        $raw   = preg_replace('/[^a-z0-9 ]/i', ' ', $resourceName);
        $words = array_values(array_filter(explode(' ', strtolower($raw)), fn($w) => strlen($w) >= 3));
        $where = 'project_id = :pid';
        $params = [':pid' => $pid];
        foreach ($words as $i => $w) {
            $key = ':rw' . $i;
            $where .= " AND LOWER(resource_name) LIKE {$key}";
            $params[$key] = '%' . rtrim($w, 's') . '%';
        }
        if (empty($words)) {
            $where .= ' AND LOWER(resource_name) LIKE :rfull';
            $params[':rfull'] = '%' . strtolower($resourceName) . '%';
        }

        // Optional activity filter
        if ($activityName !== '') {
            $actRaw   = preg_replace('/[^a-z0-9 ]/i', ' ', $activityName);
            $actWords = array_values(array_filter(explode(' ', strtolower($actRaw)), fn($w) => strlen($w) >= 3));
            foreach ($actWords as $i => $w) {
                $key = ':aw' . $i;
                $where .= " AND LOWER(activity_name) LIKE {$key}";
                $params[$key] = '%' . rtrim($w, 's') . '%';
            }
        }

        $rows = $db->createCommand(
            "SELECT resource_name, resource_unit, resource_type_name, activity_name,
                    qty_per_unit, est_unit_cost, act_unit_cost,
                    planned_consumption, actual_consumption,
                    est_cost, act_cost, computed_at
             FROM cb_cache_resources
             WHERE {$where}
             ORDER BY resource_name, activity_name LIMIT 50",
            $params
        )->queryAll();

        if (empty($rows)) {
            return [
                'matched' => false,
                'reason'  => "No resource matching \"{$resourceName}\" found in \"{$project['Name']}\"" .
                    ($activityName ? " for activity \"{$activityName}\"" : '') . '.',
            ];
        }

        // Build resource-type cost grouping (Table 4 equivalent — for type-level questions)
        $byType = [];
        foreach ($rows as $r) {
            $tn = $r['resource_type_name'] ?: 'Other';
            if (!isset($byType[$tn])) {
                $byType[$tn] = ['est_cost' => 0.0, 'act_cost' => 0.0];
            }
            $byType[$tn]['est_cost'] += (float)($r['est_cost'] ?? 0);
            if ($r['act_cost'] !== null) {
                $byType[$tn]['act_cost'] += (float)$r['act_cost'];
            }
        }
        $byTypeOut = [];
        foreach ($byType as $tn => $vals) {
            $byTypeOut[] = [
                'resource_type'  => $tn,
                'est_cost'       => round($vals['est_cost'], 2),
                'act_cost'       => round($vals['act_cost'], 2),
            ];
        }

        $computedAt = $rows[0]['computed_at'];
        return [
            'matched'         => true,
            'project'         => $project['Name'],
            'resource_filter' => $resourceName,
            'activity_filter' => $activityName ?: null,
            'computed_at'     => $computedAt,
            'count'           => count($rows),
            'resources'       => array_map(fn($r) => [
                'resource_name'       => $r['resource_name'],
                'resource_type'       => $r['resource_type_name'],
                'unit'                => $r['resource_unit'],
                'activity_name'       => $r['activity_name'],
                'qty_per_unit'        => (float)$r['qty_per_unit'],
                'est_unit_cost'       => (float)$r['est_unit_cost'],
                'act_unit_cost'       => $r['act_unit_cost'] !== null ? (float)$r['act_unit_cost'] : null,
                'planned_consumption' => round((float)$r['planned_consumption'], 4),
                'actual_consumption'  => round((float)$r['actual_consumption'], 4),
                'est_cost'            => (float)$r['est_cost'],
                'act_cost'            => $r['act_cost'] !== null ? (float)$r['act_cost'] : null,
            ], $rows),
            'by_resource_type' => $byTypeOut,
        ];
    }

    private function toolGetActivityResources($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid          = (int)$project['Project_Id'];
        $activityName = trim($input['activity_name'] ?? '');
        if ($activityName === '') {
            return ['status' => 'no_data', 'reason' => 'activity_name is required.'];
        }

        $actFilter = '';
        $params    = [':pid' => $pid, ':pid2' => $pid];
        $raw       = preg_replace('/[^a-z0-9 ]/i', ' ', $activityName);
        $words     = array_values(array_filter(explode(' ', $raw), fn($w) => strlen($w) >= 4));
        foreach ($words as $i => $word) {
            $key = ':aw' . $i;
            $actFilter .= " AND LOWER(wan.activity_Name) LIKE {$key}";
            $params[$key] = '%' . strtolower(rtrim($word, 's')) . '%';
        }

        $act = $db->createCommand(
            "SELECT wan.id AS wan_id, wan.activity_Name, wan.activity_Unit, pen.activity_qty
             FROM workgroup_activities_new wan
             JOIN pricing_estimate_new pen
               ON pen.activity_Id = wan.id AND pen.project_Id = :pid AND pen.pricing_status = 0
             WHERE wan.project_Id = :pid2 AND wan.pricing_status = 0 {$actFilter} LIMIT 1",
            $params
        )->queryOne();

        if (!$act) {
            return ['status' => 'no_data', 'reason' => "Activity \"{$activityName}\" not found in estimate for \"{$project['Name']}\"."];
        }

        $wanId  = (int)$act['wan_id'];
        $actQty = (float)$act['activity_qty'];

        $rows = $db->createCommand(
            "SELECT COALESCE(pern.display_name, r.Name, rt.Name) AS resource_name,
                    rt.Name AS resource_type,
                    COALESCE(r.Unit, '') AS resource_unit,
                    pern.quantity AS qty_per_unit, pern.rate AS unit_rate,
                    pern.quantity * pern.rate AS amount_per_unit
             FROM pricing_estimate_resources_new pern
             LEFT JOIN resources r     ON r.Resource_Id      = pern.resource_Id
             LEFT JOIN resourcetype rt ON rt.ResourceType_Id = pern.resourcetype_Id
             WHERE pern.activity_id = :wid AND pern.project_id = :pid AND pern.pricing_status = 0
             ORDER BY pern.rate DESC",
            [':wid' => $wanId, ':pid' => $pid]
        )->queryAll();

        if (empty($rows)) {
            return ['status' => 'no_data', 'reason' => "No resources allocated for activity \"{$act['activity_Name']}\"."];
        }

        $totalUnitCost = 0.0;
        $resources     = [];
        foreach ($rows as $r) {
            $amt = round((float)$r['amount_per_unit'], 2);
            $totalUnitCost += $amt;
            $resources[] = [
                'resource_name'   => $r['resource_name'],
                'resource_type'   => $r['resource_type'],
                'unit'            => $r['resource_unit'],
                'qty_per_unit'    => (float)$r['qty_per_unit'],
                'unit_rate'       => round((float)$r['unit_rate'], 2),
                'amount_per_unit' => $amt,
            ];
        }

        return [
            'status'              => 'ok',
            'project'             => $project['Name'],
            'activity'            => $act['activity_Name'],
            'activity_unit'       => $act['activity_Unit'],
            'activity_qty'        => $actQty,
            'resources'           => $resources,
            'total_unit_cost'     => round($totalUnitCost, 2),
            'total_activity_cost' => round($totalUnitCost * $actQty, 2),
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
            LEFT JOIN vendors v ON v.Vendor_Id = wo.WO_Vendor
            WHERE wo.Project_Id = :pid ORDER BY wo.Date_requested DESC LIMIT 100
        ", [':pid' => $pid])->queryAll();

        $mbs = $db->createCommand("
            SELECT mb.mb_number, mb.mb_date, mb.wo_number, mb.entries
            FROM wo_measurement_book mb
            WHERE mb.project_id = :pid AND mb.delete_status = 0 AND mb.sent_status = 1
            ORDER BY mb.mb_date DESC LIMIT 100
        ", [':pid' => $pid])->queryAll();

        if (empty($wos) && empty($mbs)) {
            return ['status' => 'no_data', 'reason' => "No work orders or measurement books found for project \"{$project['Name']}\"."];
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
                        'unit'      => $task['unit'] ?? '',
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

        $pid   = (int)$project['Project_Id'];
        $today = date('Y-m-d');

        $schedRow = $db->createCommand(
            "SELECT MIN(start_date) AS plan_start, MAX(end_date) AS plan_end,
                    COUNT(*) AS total_acts, SUM(completed_status) AS completed_acts,
                    SUM(CASE WHEN completed_status=0 AND end_date < CURDATE() THEN 1 ELSE 0 END) AS overdue_acts,
                    SUM(CASE WHEN critical_status='Yes' THEN 1 ELSE 0 END) AS critical_acts,
                    SUM(CASE WHEN critical_status='Yes' AND completed_status=0 AND end_date < CURDATE() THEN 1 ELSE 0 END) AS critical_overdue
             FROM scheduleactivities WHERE projectId=:pid AND status=0 AND old_duration > 0",
            [':pid' => $pid]
        )->queryOne();

        $progressRow = $db->createCommand(
            "SELECT AVG(CASE
                WHEN sa.quantity > 0 AND rpt.cumulated_qty > 0
                     THEN LEAST(rpt.cumulated_qty/sa.quantity*100,100)
                WHEN sa.completed_status = 1 THEN 100 ELSE 0
             END) AS actual_pct,
             AVG(CASE
                WHEN sa.old_duration > 0 AND sa.start_date <= CURDATE()
                     THEN LEAST(DATEDIFF(CURDATE(),sa.start_date)/sa.old_duration*100,100)
                WHEN sa.completed_status = 1 THEN 100 ELSE 0
             END) AS planned_pct
             FROM scheduleactivities sa
             LEFT JOIN (SELECT activity_id, MAX(cumulated_qty) AS cumulated_qty
                        FROM schedule_progress_report GROUP BY activity_id) rpt ON rpt.activity_id=sa.id
             WHERE sa.projectId=:pid AND sa.status=0",
            [':pid' => $pid]
        )->queryOne();

        $critDelay = $db->createCommand(
            "SELECT MAX(DATEDIFF(CURDATE(), end_date)) AS max_delay
             FROM scheduleactivities
             WHERE projectId=:pid AND status=0 AND critical_status='Yes'
               AND completed_status=0 AND end_date < CURDATE()",
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
            'status'               => 'ok',
            'project'              => $project['Name'],
            'plan_start'           => $schedRow['plan_start'] ?? null,
            'plan_end'             => $planEnd,
            'forecast_end'         => $forecastEnd,
            'critical_delay_days'  => $critDelay,
            'total_activities'     => (int)($schedRow['total_acts']      ?? 0),
            'completed_activities' => (int)($schedRow['completed_acts']  ?? 0),
            'overdue_activities'   => (int)($schedRow['overdue_acts']    ?? 0),
            'critical_activities'  => (int)($schedRow['critical_acts']   ?? 0),
            'critical_overdue'     => (int)($schedRow['critical_overdue']?? 0),
            'actual_progress_pct'  => $actualPct,
            'planned_progress_pct' => $plannedPct,
            'schedule_variance_pct'=> round($actualPct - $plannedPct, 1),
        ];
    }

    private function toolQueryDatabase($db, $input)
    {
        $sql    = trim($input['sql']    ?? '');
        $reason = trim($input['reason'] ?? '');
        if ($sql === '') return ['status' => 'no_data', 'reason' => 'No SQL provided.'];

        $validationError = $this->validateSelectOnly($sql);
        if ($validationError) return ['status' => 'rejected', 'reason' => $validationError];

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

        // Per-request session tracking (in-memory)
        // Passed by reference into executeTool so it persists across tool calls within one HTTP request
        $sessionLoadedProjects = [];

        // Chat session id — passed from browser, identifies the open conversation window
        $chatSessionId = trim(Yii::$app->request->post('session_id', ''));
        if ($chatSessionId === '') {
            $chatSessionId = substr(md5(uniqid('', true)), 0, 16);
        }

        // Log session id (reuse for all tool calls in this request)
        $requestSessionId = substr(md5(uniqid('', true)), 0, 16);

        $systemPrompt =
            "You are a read-only project dashboard assistant for Opiam Analytics ERP, a construction project management system.\n\n" .

            "## CORE WORKFLOW — follow this every time a project is mentioned\n\n" .

            "STEP 1 — LOAD (once per project per session):\n" .
            "When the user asks about a project for the first time in a conversation, call load_project_dashboard with the project name. " .
            "This computes all cost metrics at every level (project, group, IOW, activity) from the live database. " .
            "If load_project_dashboard returns status='already_loaded', skip to STEP 2 immediately — do NOT load again.\n\n" .

            "STEP 2 — READ (all subsequent questions):\n" .
            "After the project is loaded, answer ALL cost and metric questions by calling get_dashboard_metric. " .
            "Never recompute or recalculate — only read from the cache. " .
            "If get_dashboard_metric returns reason='project_not_loaded', call load_project_dashboard first, then retry.\n\n" .

            "## NO-RECOMPUTE RULE (ABSOLUTE)\n" .
            "You MUST NOT do arithmetic yourself. All numbers come from tool results. " .
            "You MUST NOT query GRN tables, measurement books, PO tables, or any raw data to verify or recalculate cost figures. " .
            "The cache tables ARE the answer — they were computed from the same queries the live dashboard uses.\n\n" .

            "## AS-OF RULE (MANDATORY)\n" .
            "Every answer that includes a cost or metric figure MUST state the computed_at timestamp from the tool result. " .
            "Format: '(as of [time], loaded at session start)' — e.g. '(as of 09:41 AM, loaded at session start)'. " .
            "This tells the user the data was not recomputed mid-session.\n\n" .

            "## SESSION RULES\n" .
            "- Same project mentioned again in the same conversation → no reload, use existing cache.\n" .
            "- User switches to a different project → call load_project_dashboard for that project.\n" .
            "- No mid-session refresh, even if the user asks to 'check latest' — tell them to close and reopen the chatbot if they need fresh data.\n\n" .

            "## ANSWER FORMAT\n" .
            "1. State the metric asked for, copied exactly from the tool result — no paraphrasing of numbers.\n" .
            "2. Include the computed_at as-of note.\n" .
            "3. For ambiguous matches (multiple groups/IOWs/activities), list the candidates and ask the user to clarify.\n" .
            "4. Never mention internal field names, JSON keys, or tool names in your answer.\n" .
            "5. Do not add commentary, suggestions, or emojis.\n\n" .

            "## COST TERMINOLOGY\n" .
            "Activity-level (from get_dashboard_metric):\n" .
            "- 'Estimated cost' = estimated_cost / est_cost_activity (full budget for that item)\n" .
            "- 'Unit cost' / 'estimated unit cost of activity' = unit_cost (estimated cost per one unit of activity output)\n" .
            "- 'Actual cost' = actual_cost / actual_cost_activity (actual spend on that activity)\n" .
            "- 'ECWD' / 'estimated cost of work done' = ecwd (budget value of work completed so far)\n" .
            "- 'ACWD' / 'actual cost of work done' = acwd (actual spend on work completed so far)\n" .
            "- 'Difference' = difference (estimated minus actual; positive = under budget, negative = over budget)\n\n" .
            "Resource-level (from get_resource_metric — per resource row):\n" .
            "- 'Estimated unit cost of resource' = est_unit_cost (estimated rate per resource unit, e.g. ₹/kg, ₹/hour)\n" .
            "- 'Actual unit cost of resource' = act_unit_cost (GRN weighted avg for materials, MB weighted avg for subcontractors; null if no actuals)\n" .
            "- 'Planned consumption' = planned_consumption (estimated quantity of this resource used per unit of activity output)\n" .
            "- 'Actual consumption' = actual_consumption (actual quantity used per unit of activity output, derived from GRN/MB)\n" .
            "- 'Est cost' (resource level) = est_cost (est_unit_cost × planned_consumption)\n" .
            "- 'Act cost' (resource level) = act_cost (act_unit_cost × actual_consumption; null if no actuals)\n" .
            "- 'By resource type' = by_resource_type (aggregated est/act cost grouped by Material, Labour, Plant, Subcontractor etc.)\n\n" .

            "## WHEN TO USE OTHER TOOLS\n" .
            "- get_schedule_activities → list of activities with dates, progress, delay status\n" .
            "- get_activity_kpi → production rates, cycle times, tasks, cause-of-delay for one specific activity\n" .
            "- get_resource_metric → use for any question about individual resources: estimated or actual unit cost of a resource, " .
            "planned vs actual consumption, cost by resource type (Material/Labour/Plant/Subcontractor). " .
            "Examples: 'unit cost of reinforcement', 'actual rate of steel', 'cement consumption', 'resource breakdown of clearing drain', " .
            "'how much did we spend on materials vs labour'. " .
            "Pass project_name + resource_name (partial ok). Optionally pass activity_name to narrow scope. " .
            "Returns per-resource: est_unit_cost, act_unit_cost (null if no actuals), planned_consumption, actual_consumption, est_cost, act_cost. " .
            "Also returns by_resource_type array with aggregated est_cost/act_cost per type. " .
            "Requires project to be loaded. If reason='project_not_loaded', call load_project_dashboard first.\n" .
            "- get_materials / get_stock → materials received, GRN, stock position\n" .
            "- get_work_orders_and_mb → subcontractor work orders and measurement books\n" .
            "- get_project_estimate → total BOQ budget and profit margin\n" .
            "- get_project_schedule_summary → schedule health, progress %, delays\n" .
            "- query_database → ONLY when no other tool covers the question\n\n" .

            "## GROUNDING RULE\n" .
            "Only state facts from tool results. If a tool returns no_data, say 'That information is not available in the system.' " .
            "Never guess, estimate, or fill gaps from general knowledge.\n\n" .

            "TODAY'S DATE: " . date('d F Y') . ".";

        // Only pass plain user/assistant text turns as history.
        // Never pass tool_use or tool_result blocks — those are internal API rounds
        // that the browser should not hold, and re-sending them causes
        // "Input should be an object" from the Anthropic API.
        $messages = [];
        foreach ((array)$history as $h) {
            if (empty($h['role']) || !isset($h['content'])) continue;
            $content = $h['content'];
            // Skip any entry whose content is not a plain string (e.g. serialised tool blocks)
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
            $t0       = microtime(true);
            $response = curl_exec($ch);
            $ms       = (int)round((microtime(true) - $t0) * 1000);
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
                $messages[] = ['role' => 'assistant', 'content' => $content];

                $toolResults = [];
                foreach ($content as $block) {
                    if ($block['type'] !== 'tool_use') continue;

                    $toolName  = $block['name'];
                    $toolInput = is_array($block['input'] ?? null) ? $block['input'] : [];
                    $toolUseId = $block['id'];

                    $result = $this->executeTool($toolName, $toolInput, $sessionLoadedProjects, $chatSessionId);

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
