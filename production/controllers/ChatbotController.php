<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

class ChatbotController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [];
    }

    private function getApiKey()
    {
        $secrets = @include(Yii::getAlias('@app') . '/config/secrets.php');
        return is_array($secrets) ? ($secrets['anthropicApiKey'] ?? '') : '';
    }

    // -----------------------------------------------------------------------
    // Logging — Rule 7
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
        } catch (\Exception $e) {
            // Never let logging break the response
        }
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
    // Rule 5 — SELECT-only query validator
    // -----------------------------------------------------------------------
    private function validateSelectOnly($sql)
    {
        $normalized = preg_replace('/\s+/', ' ', trim($sql));
        // Must start with SELECT (allow leading comments stripped above)
        if (!preg_match('/^SELECT\s/i', $normalized)) {
            return 'Only SELECT queries are permitted.';
        }
        // Block any DML / DDL keywords even inside subqueries
        $blocked = ['INSERT', 'UPDATE', 'DELETE', 'DROP', 'TRUNCATE', 'ALTER', 'CREATE',
                    'REPLACE', 'CALL', 'EXEC', 'GRANT', 'REVOKE', 'LOAD', 'OUTFILE', 'DUMPFILE'];
        foreach ($blocked as $kw) {
            if (preg_match('/\b' . $kw . '\b/i', $normalized)) {
                return "Keyword '{$kw}' is not allowed in a read-only query.";
            }
        }
        return null; // valid
    }

    // -----------------------------------------------------------------------
    // Helper — find project by name, return structured no_data on miss
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
    // Tool definitions — Rule 3: strict schemas, required/optional explicit
    // -----------------------------------------------------------------------
    private function toolDefinitions()
    {
        return [
            [
                'name'        => 'get_projects',
                'description' => 'Returns the list of all active projects with contract value, client, location, start date and end date. Returns {status:"no_data"} if there are no active projects.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => (object)[],
                    'required'   => [],
                ],
            ],
            [
                'name'        => 'get_activity_kpi',
                'description' => 'Returns the full KPI data for a single schedule activity: planned vs actual quantity, progress %, target production rate, actual production rate, planned duration, elapsed days, projected duration, task-level productivity, and cause-of-delay breakdown. Use this whenever the user asks about production rate, target production, productivity, progress, duration, or delays for a specific activity. If more than one activity matches, returns all matches so the user can be asked to clarify.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string', 'description' => 'Full or partial project name (optional — omit only if context is unambiguous)'],
                        'activity_name' => ['type' => 'string', 'description' => 'Full or partial schedule activity name'],
                    ],
                    'required' => ['activity_name'],
                ],
            ],
            [
                'name'        => 'get_project_progress',
                'description' => 'Returns physical progress %, total activities, completed activities, cost of work done (estimated and actual), and schedule health (critical activities, delays) for a project.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string', 'description' => 'Full or partial project name'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_schedule_activities',
                'description' => 'Returns a list of schedule activities for a project with start/end dates, planned quantity, qty done, progress %, and delay status.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string', 'description' => 'Full or partial project name'],
                        'filter'       => [
                            'type'        => 'string',
                            'enum'        => ['all', 'critical', 'delayed', 'completed', 'ongoing'],
                            'description' => 'Which activities to return. Default: all',
                        ],
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
                        'project_name'  => ['type' => 'string', 'description' => 'Full or partial project name'],
                        'material_name' => ['type' => 'string', 'description' => 'Optional: filter by material/resource name'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_cost_dashboard',
                'description' => 'Returns cached ECWD and ACWD totals for a project. DO NOT call this for overall project cost questions — those are already answered in the get_project_briefing cost_sentence. Only call this if the user explicitly asks for a cached cost dashboard refresh.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string', 'description' => 'Full or partial project name'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_work_orders_and_mb',
                'description' => 'Returns work orders and measurement book entries for a project, showing subcontractor scope, quantities, and amounts certified.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string', 'description' => 'Full or partial project name'],
                        'activity_name' => ['type' => 'string', 'description' => 'Optional: filter by activity name'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_project_estimate',
                'description' => 'Returns the TOTAL estimated cost of the project (the full BOQ/pricing estimate). Use when the user asks "what is the estimated cost", "total project budget", "profitability", or "contract margin". DIFFERENT from ECWD.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string', 'description' => 'Full or partial project name'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_activity_costs',
                'description' => 'Returns planned estimated cost per activity: unit_cost (cost per one schedule unit) and total_cost. Use when the user asks about cost of a specific activity or wants activities ranked by cost.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string', 'description' => 'Full or partial project name'],
                        'activity_name' => ['type' => 'string', 'description' => 'Optional: filter to a specific activity'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_activity_resources',
                'description' => 'Returns the planned resource allocation for an activity: each resource with planned quantity per unit, unit rate, and amount per unit.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string', 'description' => 'Full or partial project name'],
                        'activity_name' => ['type' => 'string', 'description' => 'Full or partial activity name'],
                    ],
                    'required' => ['project_name', 'activity_name'],
                ],
            ],
            [
                'name'        => 'get_activity_actual_cost',
                'description' => 'Returns the actual cost per activity from GRN receipts and certified measurement books. Use ONLY when the user asks about cost of a SPECIFIC activity by name — never for overall project cost totals, which are already in the briefing cost_sentence.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string', 'description' => 'Full or partial project name'],
                        'activity_name' => ['type' => 'string', 'description' => 'Optional: filter to a specific activity'],
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
                        'project_name'  => ['type' => 'string', 'description' => 'Full or partial project name'],
                        'material_name' => ['type' => 'string', 'description' => 'Optional: filter by material name'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_project_documents',
                'description' => 'Returns the list of uploaded project documents. If a specific file_name is given, also returns text content of that file.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string', 'description' => 'Full or partial project name'],
                        'file_name'    => ['type' => 'string', 'description' => 'Optional: partial name of a specific file to read'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'get_project_briefing',
                'description' => 'Returns the complete project briefing: identity, time position (schedule progress), physical progress, cost position (ECWD/ACWD), procurement health, and a pre-computed alerts list of red flags. ALWAYS call this first before answering any question about a specific project. Do not call other tools until you have called this one.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string', 'description' => 'Full or partial project name'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
            [
                'name'        => 'query_database',
                'description' => 'Executes a raw SELECT query against the live database and returns up to 50 rows. Use ONLY for questions that no other tool can answer. The query MUST be a SELECT statement — any INSERT/UPDATE/DELETE/DROP will be rejected.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'sql'    => ['type' => 'string', 'description' => 'A valid MySQL SELECT statement. Must not contain INSERT, UPDATE, DELETE, DROP, ALTER, TRUNCATE, CREATE, REPLACE, CALL, EXEC, GRANT, or REVOKE.'],
                        'reason' => ['type' => 'string', 'description' => 'One sentence explaining why this query is needed and no other tool covers it.'],
                    ],
                    'required' => ['sql', 'reason'],
                ],
            ],
        ];
    }

    // -----------------------------------------------------------------------
    // Tool execution dispatcher
    // -----------------------------------------------------------------------
    private function executeTool($name, $input)
    {
        $db = Yii::$app->db;

        switch ($name) {
            case 'get_project_briefing':    return $this->toolGetProjectBriefing($db, $input);
            case 'get_projects':           return $this->toolGetProjects($db);
            case 'get_activity_kpi':       return $this->toolGetActivityKpi($db, $input);
            case 'get_project_progress':   return $this->toolGetProjectProgress($db, $input);
            case 'get_schedule_activities':return $this->toolGetScheduleActivities($db, $input);
            case 'get_materials':          return $this->toolGetMaterials($db, $input);
            case 'get_cost_dashboard':     return $this->toolGetCostDashboard($db, $input);
            case 'get_work_orders_and_mb': return $this->toolGetWorkOrdersAndMb($db, $input);
            case 'get_project_estimate':   return $this->toolGetProjectEstimate($db, $input);
            case 'get_activity_costs':     return $this->toolGetActivityCosts($db, $input);
            case 'get_activity_resources': return $this->toolGetActivityResources($db, $input);
            case 'get_activity_actual_cost':return $this->toolGetActivityActualCost($db, $input);
            case 'get_stock':              return $this->toolGetStock($db, $input);
            case 'get_project_documents':  return $this->toolGetProjectDocuments($db, $input);
            case 'query_database':         return $this->toolQueryDatabase($db, $input);
            default:
                return ['status' => 'no_data', 'reason' => "Unknown tool: {$name}"];
        }
    }

    // -----------------------------------------------------------------------
    // Tool: get_projects
    // -----------------------------------------------------------------------
    private function toolGetProjects($db)
    {
        $rows = $db->createCommand(
            "SELECT Project_Id, Name, client_name, location, start_date, end_date, project_value
             FROM projects WHERE Status = 0 ORDER BY Name ASC LIMIT 50"
        )->queryAll();

        if (empty($rows)) {
            return ['status' => 'no_data', 'reason' => 'No active projects found in the system.'];
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

    // -----------------------------------------------------------------------
    // Tool: get_activity_kpi
    // Rule 2: return ALL matches when ambiguous so system prompt can clarify
    // -----------------------------------------------------------------------
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
            $word = strtolower(rtrim($word, 's'));
            $key  = ':aw' . $i;
            $where .= " AND LOWER(sa.name) LIKE {$key}";
            $params[$key] = '%' . $word . '%';
        }

        // Rule 2: fetch up to 5 matches
        $acts = $db->createCommand("
            SELECT sa.id, sa.name, sa.duration, sa.old_duration, sa.unit,
                   sa.quantity, sa.completed_status, sa.start_date,
                   sa.actual_start_date, sa.actual_end_date, sa.end_date,
                   sa.resource_units, sa.critical_status,
                   sa.projectId AS pid,
                   p.Name AS project_name
            FROM scheduleactivities sa
            JOIN projects p ON p.Project_Id = sa.projectId AND p.Status = 0
            WHERE {$where}
            ORDER BY sa.start_date DESC
            LIMIT 5
        ", $params)->queryAll();

        if (empty($acts)) {
            return ['status' => 'no_data', 'reason' => "No schedule activity found matching \"{$activityName}\"" . ($projectName ? " in project \"{$projectName}\"" : '') . '.'];
        }

        // Rule 2: if multiple matches, return summary list and ask Claude to clarify
        if (count($acts) > 1) {
            return [
                'status'           => 'ambiguous',
                'reason'           => 'Multiple activities matched. Ask the user to specify which one.',
                'matched_activities' => array_map(fn($a) => [
                    'id'           => (int)$a['id'],
                    'name'         => $a['name'],
                    'project'      => $a['project_name'],
                    'start_date'   => $a['start_date'],
                    'end_date'     => $a['end_date'],
                    'is_completed' => (int)$a['completed_status'] === 1,
                ], $acts),
            ];
        }

        $act   = $acts[0];
        $actid = (int)$act['id'];
        $pid   = (int)$act['pid'];

        $san = $db->createCommand(
            "SELECT progress, Workhours, Cycles, Resourceunits
             FROM schedule_activity_new WHERE actvity_id=$actid"
        )->queryOne();

        $target_qty     = (float)($act['quantity'] ?? 0);
        $unit           = $act['unit'] ?? '';
        $wh             = $san ? (int)$san['Workhours']  : 8;
        $cycles         = $san ? (float)$san['Cycles']   : 1;
        $progress       = $san ? (float)$san['progress'] : 0;

        $pr = $db->createCommand(
            "SELECT cumulated_qty FROM schedule_progress_report
             WHERE activity_id=$actid ORDER BY updated_at DESC LIMIT 1"
        )->queryOne();
        $actual_qty = $pr ? (float)$pr['cumulated_qty'] : 0;

        $work_done_pct  = ($target_qty > 0)
            ? round($actual_qty / $target_qty * 100, 1) : $progress;

        $b_duration     = (float)($act['old_duration'] ?? 0);
        $duration       = (float)($act['duration']     ?? 0);
        $resource_units = max(1, (float)($act['resource_units'] ?? 1));

        $target_cycle    = ($target_qty > 0)
            ? round(($b_duration / $target_qty) * $wh, 3) : 0;
        $planned_per_day = ($b_duration > 0 && $target_qty > 0)
            ? round($target_qty / $b_duration, 3) : 0;

        $brk = $db->createCommand(
            "SELECT SUM(break_hour) AS total_break FROM schedule_progress_report_log
             WHERE activity_id=$actid"
        )->queryOne();
        $cum_break = $brk ? (float)$brk['total_break'] : 0;

        $lrd = $db->createCommand(
            "SELECT MAX(report_date) AS last_date FROM schedule_progress_report_log
             WHERE activity_id=$actid AND currentqty > 0"
        )->queryOne();
        $last_reported_date = ($lrd && !empty($lrd['last_date'])) ? $lrd['last_date'] : '';

        $spr = $db->createCommand(
            "SELECT start_date FROM schedule_progress_report
             WHERE activity_id=$actid LIMIT 1"
        )->queryOne();
        $reported_start = ($spr && !empty($spr['start_date']) && $spr['start_date'] != '0000-00-00')
            ? $spr['start_date'] : '';
        $planned_start = (!empty($act['start_date']) && $act['start_date'] != '0000-00-00')
            ? $act['start_date'] : '';
        $act_start_date = ($planned_start && $reported_start)
            ? min($planned_start, $reported_start)
            : ($reported_start ?: $planned_start);

        $actual_prod  = 0;
        $actual_cycle = 0;
        $elapsed      = 0;
        $start_delay  = 0;
        $cap_max      = 0;
        $cap_used     = 0;

        if ($act_start_date && $last_reported_date && $actual_qty > 0) {
            // Inclusive elapsed: 6th to 10th = 5 days
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
             WHERE at.activity_id = $masterActId
             ORDER BY at.sort_order ASC"
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
            $tqu    = (float)$t['task_qty'];
            $actual = ($elapsed > 0 && $actual_qty > 0 && $tqu > 0)
                ? round($actual_qty * $tqu / $elapsed, 3) : 0;
            $mbQty  = $taskMbQty[(int)$t['task_id']] ?? 0;
            return [
                'task_name'                 => $t['task_name'],
                'unit'                      => $t['task_unit'],
                'target_production_per_day' => round((float)$t['productivity'] * max(1, (float)$t['resource_units']), 3),
                'productivity_per_resource' => (float)$t['productivity'],
                'resource_units'            => (float)$t['resource_units'],
                'actual_production_per_day' => $actual,
                'task_planned_qty'          => round($tqu * $target_qty, 3),
                'planned_duration_days'     => (float)$t['planned_duration'],
                'actual_duration_days'      => ($elapsed > 0 && $mbQty > 0) ? round($elapsed / $mbQty, 3) : 0,
            ];
        }, $task_rows);

        return [
            'status'                     => 'ok',
            'project'                    => $act['project_name'],
            'activity'                   => $act['name'],
            'unit'                       => $unit,
            'target_qty'                 => $target_qty,
            'actual_qty_done'            => $actual_qty,
            'progress_percent'           => $work_done_pct,
            'planned_duration_days'      => $b_duration,
            'current_duration_days'      => $duration,
            'elapsed_days'               => (int)round($elapsed),
            'start_delay_days'           => $start_delay,
            'projected_total_duration'   => $projected_duration,
            'planned_start_date'         => $planned_start,
            'planned_end_date'           => $planned_start
                ? date('Y-m-d', strtotime($planned_start . ' +' . (max(1, (int)$b_duration) - 1) . ' days'))
                : ($act['end_date'] ?? ''),
            'projected_actual_end_date'  => ($actual_qty > 0 && $elapsed > 0 && $planned_start)
                ? date('Y-m-d', strtotime($planned_start . ' +' . (max(1, (int)ceil($elapsed / $actual_qty * $target_qty)) - 1) . ' days'))
                : null,
            'reported_start_date'        => $reported_start ?: null,
            'last_reported_date'         => $last_reported_date ?: null,
            'target_production_per_day'  => $planned_per_day,
            'target_to_date'             => round($planned_per_day * $elapsed, 2),
            'actual_production_per_day'  => $actual_prod,
            'work_hours_per_day'         => $wh,
            'target_cycle_time_hrs'      => $target_cycle,
            'actual_cycle_time_hrs'      => $actual_cycle,
            'capacity_max_hrs'           => $cap_max,
            'capacity_used_hrs'          => $cap_used,
            'is_critical'                => ($act['critical_status'] === 'Yes'),
            'is_completed'               => ((int)$act['completed_status'] === 1),
            'cause_of_delay'             => empty($cause_of_delay)
                ? ['status' => 'no_data', 'reason' => 'No cause-of-delay entries recorded for this activity.']
                : $cause_of_delay,
            'tasks'                      => empty($tasks)
                ? ['status' => 'no_data', 'reason' => 'No tasks defined for this activity.']
                : $tasks,
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_project_progress
    // -----------------------------------------------------------------------
    private function toolGetProjectProgress($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid = (int)$project['Project_Id'];

        $schedRow = $db->createCommand(
            "SELECT MIN(start_date) AS sched_start,
                    MAX(DATE_ADD(start_date, INTERVAL old_duration - 1 DAY)) AS sched_end,
                    DATEDIFF(MAX(DATE_ADD(start_date, INTERVAL old_duration - 1 DAY)), MIN(start_date)) AS planned_duration_days,
                    COUNT(*) AS total_acts,
                    SUM(completed_status) AS completed_acts,
                    SUM(CASE WHEN completed_status=0 AND end_date < CURDATE() THEN 1 ELSE 0 END) AS delayed_acts
             FROM scheduleactivities
             WHERE projectId=:pid AND status=0 AND old_duration > 0",
            [':pid' => $pid]
        )->queryOne();

        if (!$schedRow || $schedRow['total_acts'] == 0) {
            return ['status' => 'no_data', 'reason' => "No schedule activities found for project \"{$project['Name']}\"."];
        }

        $progressRow = $db->createCommand(
            "SELECT AVG(
                CASE WHEN sa.quantity > 0 AND rpt.cumulated_qty > 0
                     THEN LEAST(rpt.cumulated_qty / sa.quantity * 100, 100)
                     WHEN sa.completed_status = 1 THEN 100
                     ELSE 0 END
             ) AS avg_progress
             FROM scheduleactivities sa
             LEFT JOIN (
                 SELECT activity_id, MAX(cumulated_qty) AS cumulated_qty
                 FROM schedule_progress_report GROUP BY activity_id
             ) rpt ON rpt.activity_id = sa.id
             WHERE sa.projectId=:pid AND sa.status=0",
            [':pid' => $pid]
        )->queryOne();

        $critRow = $db->createCommand(
            "SELECT COUNT(sa.id) AS crit_cnt,
                    SUM(CASE WHEN sa.completed_status=0 AND DATE_ADD(sa.start_date, INTERVAL sa.old_duration-1 DAY) < CURDATE() THEN 1 ELSE 0 END) AS delayed_cnt,
                    MAX(CASE WHEN sa.completed_status=0 AND DATE_ADD(sa.start_date, INTERVAL sa.old_duration-1 DAY) < CURDATE() THEN DATEDIFF(CURDATE(), DATE_ADD(sa.start_date, INTERVAL sa.old_duration-1 DAY)) ELSE 0 END) AS max_delay_days,
                    MAX(DATE_ADD(sa.start_date, INTERVAL sa.old_duration-1 DAY)) AS critical_planned_end
             FROM scheduleactivities sa
             WHERE sa.projectId=:pid AND sa.status=0 AND sa.critical_status='Yes' AND sa.old_duration > 0",
            [':pid' => $pid]
        )->queryOne();

        $cacheRows = $db->createCommand(
            "SELECT metric_name, metric_value FROM chatbot_metrics_cache WHERE project_id=:pid",
            [':pid' => $pid]
        )->queryAll();
        $cache = [];
        foreach ($cacheRows as $r) { $cache[$r['metric_name']] = $r['metric_value']; }

        return [
            'status'                    => 'ok',
            'project'                   => $project['Name'],
            'client'                    => $project['client_name'],
            'contract_value'            => (float)$project['project_value'],
            'schedule_start_date'       => $schedRow['sched_start']          ?? null,
            'schedule_end_date'         => $schedRow['sched_end']            ?? null,
            'planned_duration_days'     => (int)($schedRow['planned_duration_days'] ?? 0),
            'physical_progress_percent' => $progressRow ? round((float)$progressRow['avg_progress'], 1) : 0,
            'total_activities'          => (int)$schedRow['total_acts'],
            'completed_activities'      => (int)$schedRow['completed_acts'],
            'delayed_activities'        => (int)$schedRow['delayed_acts'],
            'critical_activities_count' => $critRow ? (int)$critRow['crit_cnt']       : 0,
            'delayed_critical_count'    => $critRow ? (int)$critRow['delayed_cnt']     : 0,
            'max_delay_days'            => $critRow ? (int)$critRow['max_delay_days']  : 0,
            'critical_planned_end'      => $critRow ? $critRow['critical_planned_end'] : null,
            'estimated_cost_work_done'  => isset($cache['estimated_cost_work_done']) ? (float)$cache['estimated_cost_work_done'] : null,
            'actual_cost_work_done'     => isset($cache['actual_cost_work_done'])    ? (float)$cache['actual_cost_work_done']    : null,
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_schedule_activities
    // -----------------------------------------------------------------------
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
                   sa.old_duration AS planned_duration,
                   sa.quantity, sa.unit,
                   sa.completed_status, sa.critical_status,
                   COALESCE(rpt.cumulated_qty, 0) AS qty_done,
                   CASE
                     WHEN sa.quantity > 0 AND COALESCE(rpt.cumulated_qty,0) > 0
                     THEN ROUND(COALESCE(rpt.cumulated_qty,0) / sa.quantity * 100, 1)
                     WHEN sa.completed_status = 1 THEN 100
                     ELSE 0
                   END AS progress_pct,
                   CASE
                     WHEN sa.completed_status = 0 AND sa.end_date < CURDATE()
                     THEN DATEDIFF(CURDATE(), sa.end_date)
                     ELSE 0
                   END AS days_overdue
            FROM scheduleactivities sa
            LEFT JOIN (
                SELECT activity_id, MAX(cumulated_qty) AS cumulated_qty
                FROM schedule_progress_report GROUP BY activity_id
            ) rpt ON rpt.activity_id = sa.id
            WHERE sa.projectId = :pid AND sa.status = 0 {$having}
            ORDER BY sa.start_date ASC
            LIMIT 200
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

    // -----------------------------------------------------------------------
    // Tool: get_materials
    // -----------------------------------------------------------------------
    private function toolGetMaterials($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid          = (int)$project['Project_Id'];
        $materialName = trim($input['material_name'] ?? '');

        $matFilter = '';
        $params    = [':pid' => $pid];
        if ($materialName !== '') {
            $matFilter     = "AND r.Name LIKE :mname";
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
            WHERE po.project_id = :pid AND g.delete_status = 0 AND po.delete_status = 0
            {$matFilter}
            GROUP BY r.Resource_Id, r.Name, r.Unit
            ORDER BY total_value DESC LIMIT 100
        ", $params)->queryAll();

        $pos = $db->createCommand("
            SELECT por.resource_name AS material, por.unit,
                   SUM(por.qnty) AS ordered_qty,
                   SUM(por.amount) AS ordered_value,
                   COUNT(DISTINCT po.order_id) AS po_count
            FROM purchase_order_resources por
            JOIN purchase_orders po ON po.order_id = por.order_id
            WHERE po.project_id = :pid AND po.delete_status = 0 AND por.delete_status = 0
            GROUP BY por.resource_name, por.unit
            ORDER BY ordered_value DESC LIMIT 100
        ", [':pid' => $pid])->queryAll();

        $indents = $db->createCommand("
            SELECT si.resource_name AS material, si.unit,
                   si.stock_at_site, si.avg_consumption, si.task_name, si.raised_at
            FROM store_indents si
            WHERE si.project_id = :pid
            ORDER BY si.raised_at DESC LIMIT 100
        ", [':pid' => $pid])->queryAll();

        if (empty($grns) && empty($pos) && empty($indents)) {
            return ['status' => 'no_data', 'reason' => "No material data found for project \"{$project['Name']}\"."];
        }

        return [
            'status'          => 'ok',
            'project'         => $project['Name'],
            'grn_received'    => $grns    ?: [],
            'purchase_orders' => $pos     ?: [],
            'store_indents'   => $indents ?: [],
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_cost_dashboard
    // -----------------------------------------------------------------------
    private function toolGetCostDashboard($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid = (int)$project['Project_Id'];

        $cacheRows = $db->createCommand(
            "SELECT metric_name, metric_value, updated_at
             FROM chatbot_metrics_cache WHERE project_id = :pid",
            [':pid' => $pid]
        )->queryAll();

        if (empty($cacheRows)) {
            return [
                'status'  => 'no_data',
                'reason'  => "Cost dashboard data has not been cached yet for project \"{$project['Name']}\". Open the cost dashboard in the ERP to populate it.",
                'project' => $project['Name'],
            ];
        }

        $cache = [];
        foreach ($cacheRows as $r) {
            $cache[$r['metric_name']] = ['value' => $r['metric_value'], 'updated_at' => $r['updated_at']];
        }
        $cv = fn($k) => $cache[$k]['value'] ?? null;
        $cu = !empty($cache) ? reset($cache)['updated_at'] : null;

        $ecwd     = $cv('estimated_cost_work_done');
        $acwd     = $cv('actual_cost_work_done');
        $variance = ($ecwd !== null && $acwd !== null) ? (float)$ecwd - (float)$acwd : null;

        return [
            'status'                    => 'ok',
            'project'                   => $project['Name'],
            'estimated_cost_work_done'  => $ecwd !== null ? (float)$ecwd : null,
            'actual_cost_work_done'     => $acwd !== null ? (float)$acwd : null,
            'variance'                  => $variance,
            'budget_status'             => $variance !== null ? ($variance >= 0 ? 'Under Budget' : 'Over Budget') : null,
            'cache_as_of'               => $cu,
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_work_orders_and_mb
    // -----------------------------------------------------------------------
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
            WHERE wo.Project_Id = :pid
            ORDER BY wo.Date_requested DESC LIMIT 100
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
                'wo_number'     => $wo['WO_Number'],
                'date'          => $wo['Date_requested'],
                'vendor'        => $wo['vendor_name'],
                'scope'         => $wo['Scope'],
                'activities'    => array_values($actNames),
                'qty'           => (float)$wo['Quantity'],
                'unit'          => $wo['Unit'],
                'rate'          => (float)$wo['Rate'],
                'total'         => (float)$wo['Total'],
                'duration_days' => (int)$wo['Duration'],
                'start_date'    => $wo['start_date'],
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
                        'mb_number'  => $mb['mb_number'],
                        'date'       => $mb['mb_date'],
                        'wo_number'  => $mb['wo_number'],
                        'activity'   => $entryActName,
                        'task'       => $task['task_name'] ?? '',
                        'work_done'  => (float)($task['work_done'] ?? 0),
                        'unit'       => $task['unit'] ?? '',
                        'rate'       => (float)($task['rate'] ?? 0),
                        'value'      => round((float)($task['work_done'] ?? 0) * (float)($task['rate'] ?? 0), 2),
                    ];
                }
            }
        }

        return [
            'status'            => 'ok',
            'project'           => $project['Name'],
            'work_orders'       => $woResult,
            'measurement_books' => $mbResult,
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_project_estimate
    // -----------------------------------------------------------------------
    private function toolGetProjectEstimate($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid = (int)$project['Project_Id'];

        $rows = $db->createCommand(
            "SELECT pen.activity_qty,
                    COALESCE(SUM(pern.rate * pern.quantity), 0) AS unit_cost
             FROM pricing_estimate_new pen
             LEFT JOIN pricing_estimate_resources_new pern
               ON pern.activity_id = pen.activity_Id
              AND pern.project_id = :pid2
              AND pern.pricing_status = 0
             WHERE pen.project_Id = :pid AND pen.pricing_status = 0
             GROUP BY pen.pricing_estimate_Id, pen.activity_qty",
            [':pid' => $pid, ':pid2' => $pid]
        )->queryAll();

        if (empty($rows)) {
            return ['status' => 'no_data', 'reason' => "No pricing estimate found for project \"{$project['Name']}\"."];
        }

        $totalEstimate = 0.0;
        foreach ($rows as $r) {
            $totalEstimate += (float)$r['activity_qty'] * (float)$r['unit_cost'];
        }

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
            'note'                    => 'total_estimate_cost is the full project budget (BOQ). It is NOT the cost of work done so far.',
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_activity_costs
    // -----------------------------------------------------------------------
    private function toolGetActivityCosts($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid          = (int)$project['Project_Id'];
        $activityName = trim($input['activity_name'] ?? '');

        $actFilter = '';
        $params    = [':pid' => $pid, ':pid2' => $pid, ':pid3' => $pid, ':pid4' => $pid];
        if ($activityName !== '') {
            $raw   = preg_replace('/[^a-z0-9 ]/i', ' ', $activityName);
            $words = array_values(array_filter(explode(' ', $raw), fn($w) => strlen($w) >= 4));
            foreach ($words as $i => $word) {
                $key  = ':aw' . $i;
                $actFilter .= " AND LOWER(wan.activity_Name) LIKE {$key}";
                $params[$key] = '%' . strtolower(rtrim($word, 's')) . '%';
            }
        }

        $rows = $db->createCommand(
            "SELECT wan.activity_Name AS activity_name,
                    pen.activity_qty   AS est_qty,
                    wan.activity_Unit  AS est_unit,
                    COALESCE(SUM(pern.rate * pern.quantity), 0) AS unit_cost_per_est_unit,
                    pen.activity_qty * COALESCE(SUM(pern.rate * pern.quantity), 0) AS total_cost,
                    sa.quantity        AS sched_qty,
                    sa.unit            AS sched_unit
             FROM pricing_estimate_new pen
             JOIN workgroup_activities_new wan
               ON wan.id = pen.activity_Id AND wan.project_Id = :pid AND wan.pricing_status = 0
             LEFT JOIN pricing_estimate_resources_new pern
               ON pern.activity_id = pen.activity_Id AND pern.project_id = :pid2 AND pern.pricing_status = 0
             LEFT JOIN scheduleactivities sa
               ON sa.activity_id = pen.activity_Id AND sa.projectId = :pid3 AND sa.status = 0
             WHERE pen.project_Id = :pid4 AND pen.pricing_status = 0
             {$actFilter}
             GROUP BY pen.activity_Id, wan.activity_Name, pen.activity_qty, wan.activity_Unit, sa.quantity, sa.unit
             ORDER BY total_cost DESC
             LIMIT 100",
            $params
        )->queryAll();

        if (empty($rows)) {
            return ['status' => 'no_data', 'reason' => 'No priced activities found' . ($activityName ? " matching \"{$activityName}\"" : '') . " for project \"{$project['Name']}\"."];
        }

        $grandTotal  = 0.0;
        $activities  = [];
        foreach ($rows as $r) {
            $total            = round((float)$r['total_cost'], 2);
            $grandTotal      += $total;
            $unitCostPerEst   = (float)$r['unit_cost_per_est_unit'];
            $estQty           = (float)$r['est_qty'];
            $schedQty         = (float)($r['sched_qty'] ?? 0);
            $schedUnit        = $r['sched_unit'] ?? $r['est_unit'];
            $ratio            = ($schedQty > 0) ? $estQty / $schedQty : 1.0;
            $unitCostPerSched = round($unitCostPerEst * $ratio, 2);

            $activities[] = [
                'activity_name'            => $r['activity_name'],
                'estimate_qty'             => $estQty,
                'estimate_unit'            => $r['est_unit'],
                'unit_cost_per_est_unit'   => round($unitCostPerEst, 2),
                'schedule_qty'             => $schedQty,
                'schedule_unit'            => $schedUnit,
                'est_qty_per_sched_unit'   => round($ratio, 4),
                'unit_cost_per_sched_unit' => $unitCostPerSched,
                'units_same'               => (strtolower(trim($r['est_unit'])) === strtolower(trim($schedUnit))),
                'total_cost'               => $total,
            ];
        }

        return [
            'status'      => 'ok',
            'project'     => $project['Name'],
            'activities'  => $activities,
            'grand_total' => round($grandTotal, 2),
            'count'       => count($activities),
            'note'        => 'unit_cost_per_sched_unit matches the cost dashboard. If units_same=false, explain the conversion using est_qty_per_sched_unit.',
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_activity_resources
    // -----------------------------------------------------------------------
    private function toolGetActivityResources($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid          = (int)$project['Project_Id'];
        $activityName = trim($input['activity_name'] ?? '');

        if ($activityName === '') {
            return ['status' => 'no_data', 'reason' => 'activity_name is required for get_activity_resources.'];
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
             WHERE wan.project_Id = :pid2 AND wan.pricing_status = 0
             {$actFilter}
             LIMIT 1",
            $params
        )->queryOne();

        if (!$act) {
            return ['status' => 'no_data', 'reason' => "Activity \"{$activityName}\" not found in the estimate for project \"{$project['Name']}\"."];
        }

        $wanId  = (int)$act['wan_id'];
        $actQty = (float)$act['activity_qty'];

        $rows = $db->createCommand(
            "SELECT COALESCE(pern.display_name, r.Name, rt.Name) AS resource_name,
                    rt.Name AS resource_type,
                    COALESCE(r.Unit, '') AS resource_unit,
                    pern.quantity AS qty_per_unit,
                    pern.rate AS unit_rate,
                    pern.quantity * pern.rate AS amount_per_unit
             FROM pricing_estimate_resources_new pern
             LEFT JOIN resources r     ON r.Resource_Id       = pern.resource_Id
             LEFT JOIN resourcetype rt ON rt.ResourceType_Id  = pern.resourcetype_Id
             WHERE pern.activity_id = :wid AND pern.project_id = :pid AND pern.pricing_status = 0
             ORDER BY pern.rate DESC",
            [':wid' => $wanId, ':pid' => $pid]
        )->queryAll();

        if (empty($rows)) {
            return ['status' => 'no_data', 'reason' => "No resources allocated for activity \"{$act['activity_Name']}\" in the estimate."];
        }

        $totalUnitCost = 0.0;
        $resources     = [];
        foreach ($rows as $r) {
            $amtPerUnit     = round((float)$r['amount_per_unit'], 2);
            $totalUnitCost += $amtPerUnit;
            $resources[] = [
                'resource_name'   => $r['resource_name'],
                'resource_type'   => $r['resource_type'],
                'unit'            => $r['resource_unit'],
                'qty_per_unit'    => (float)$r['qty_per_unit'],
                'unit_rate'       => round((float)$r['unit_rate'], 2),
                'amount_per_unit' => $amtPerUnit,
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

    // -----------------------------------------------------------------------
    // Tool: get_activity_actual_cost
    // -----------------------------------------------------------------------
    private function toolGetActivityActualCost($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid          = (int)$project['Project_Id'];
        $activityName = trim($input['activity_name'] ?? '');

        $planRows = $db->createCommand(
            "SELECT pen.activity_Id AS wan_id,
                    wan.activity_Name AS activity_name,
                    wan.activity_Unit AS unit,
                    pen.activity_qty,
                    pen.activity_qty * COALESCE(SUM(pern.rate * pern.quantity), 0) AS estimated_cost
             FROM pricing_estimate_new pen
             JOIN workgroup_activities_new wan
               ON wan.id = pen.activity_Id AND wan.project_Id = :pid AND wan.pricing_status = 0
             LEFT JOIN pricing_estimate_resources_new pern
               ON pern.activity_id = pen.activity_Id AND pern.project_id = :pid2 AND pern.pricing_status = 0
             WHERE pen.project_Id = :pid3 AND pen.pricing_status = 0
             GROUP BY pen.activity_Id, wan.activity_Name, wan.activity_Unit, pen.activity_qty",
            [':pid' => $pid, ':pid2' => $pid, ':pid3' => $pid]
        )->queryAll();

        if (empty($planRows)) {
            return ['status' => 'no_data', 'reason' => "No estimate data found for project \"{$project['Name']}\"."];
        }

        $plan = [];
        foreach ($planRows as $r) { $plan[(int)$r['wan_id']] = $r; }

        $grnRows = $db->createCommand(
            "SELECT pern.activity_id AS wan_id,
                    SUM(g.GRN_Quantity * por.rate) AS grn_cost
             FROM pricing_estimate_resources_new pern
             JOIN purchase_order_resources por ON por.allocation_id = pern.pricing_resourceid
             JOIN goods_received_note g ON g.GRN_Purchase_Order = por.order_id AND g.GRN_Item = pern.resource_Id
             JOIN purchase_orders po ON po.order_id = por.order_id
             WHERE pern.project_id = :pid AND po.project_id = :pid2
               AND po.delete_status = 0 AND por.delete_status = 0 AND g.delete_status = 0
             GROUP BY pern.activity_id",
            [':pid' => $pid, ':pid2' => $pid]
        )->queryAll();

        $grnCost = [];
        foreach ($grnRows as $r) { $grnCost[(int)$r['wan_id']] = (float)$r['grn_cost']; }

        $mbRows = $db->createCommand(
            "SELECT entries FROM wo_measurement_book
             WHERE project_id = :pid AND sent_status = 1 AND delete_status = 0",
            [':pid' => $pid]
        )->queryAll();

        $mbCost = [];
        foreach ($mbRows as $mb) {
            foreach (json_decode($mb['entries'] ?? '[]', true) ?: [] as $entry) {
                $wanId = (int)($entry['activity_id'] ?? 0);
                if (!$wanId) continue;
                foreach ($entry['tasks'] ?? [] as $task) {
                    $mbCost[$wanId] = ($mbCost[$wanId] ?? 0.0)
                        + (float)($task['work_done'] ?? 0) * (float)($task['rate'] ?? 0);
                }
            }
        }

        $actWords = [];
        if ($activityName !== '') {
            $raw      = preg_replace('/[^a-z0-9 ]/i', ' ', $activityName);
            $actWords = array_values(array_filter(explode(' ', strtolower($raw)), fn($w) => strlen($w) >= 4));
        }

        $activities     = [];
        $grandEstimated = 0.0;
        $grandActual    = 0.0;

        foreach ($plan as $wanId => $r) {
            $name = $r['activity_name'];
            if ($actWords) {
                $nameLower = strtolower($name);
                $match     = true;
                foreach ($actWords as $w) {
                    if (strpos($nameLower, rtrim($w, 's')) === false) { $match = false; break; }
                }
                if (!$match) continue;
            }

            $estimated = round((float)$r['estimated_cost'], 2);
            $actual    = round(($grnCost[$wanId] ?? 0.0) + ($mbCost[$wanId] ?? 0.0), 2);
            $variance  = round($estimated - $actual, 2);

            $grandEstimated += $estimated;
            $grandActual    += $actual;

            $activities[] = [
                'activity_name'  => $name,
                'qty'            => (float)$r['activity_qty'],
                'unit'           => $r['unit'],
                'estimated_cost' => $estimated,
                'actual_cost'    => $actual,
                'variance'       => $variance,
                'status'         => $actual > 0 ? ($variance >= 0 ? 'Under budget' : 'Over budget') : 'Not started',
            ];
        }

        usort($activities, fn($a, $b) => $b['actual_cost'] <=> $a['actual_cost']);

        if (empty($activities)) {
            return ['status' => 'no_data', 'reason' => "No activities found matching \"{$activityName}\" in project \"{$project['Name']}\"."];
        }

        return [
            'status'               => 'ok',
            'project'              => $project['Name'],
            'activities'           => $activities,
            'total_estimated_cost' => round($grandEstimated, 2),
            'total_actual_cost'    => round($grandActual, 2),
            'total_variance'       => round($grandEstimated - $grandActual, 2),
            'note'                 => 'Actual cost = GRN receipts against allocated POs + certified MB subcontract costs.',
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_stock
    // -----------------------------------------------------------------------
    private function toolGetStock($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid          = (int)$project['Project_Id'];
        $materialName = trim($input['material_name'] ?? '');

        $matFilter = '';
        $params    = [':pid' => $pid];
        if ($materialName !== '') {
            $matFilter        = "AND LOWER(r.Name) LIKE :mname";
            $params[':mname'] = '%' . strtolower($materialName) . '%';
        }

        $grnRows = $db->createCommand("
            SELECT r.Resource_Id, r.Name AS material, r.Unit,
                   SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))) AS total_received,
                   MAX(g.GRN_Date) AS last_grn_date
            FROM goods_received_note g
            JOIN resources r ON r.Resource_Id = g.GRN_Item
            WHERE g.GRN_Project = :pid AND g.delete_status = 0
            {$matFilter}
            GROUP BY r.Resource_Id, r.Name, r.Unit
            ORDER BY r.Name ASC
            LIMIT 100
        ", $params)->queryAll();

        if (empty($grnRows)) {
            return ['status' => 'no_data', 'reason' => 'No GRN receipts found for project "' . $project['Name'] . '"' . ($materialName ? " matching \"{$materialName}\"" : '') . '.'];
        }

        return [
            'status'  => 'ok',
            'project' => $project['Name'],
            'count'   => count($grnRows),
            'stock'   => array_map(fn($r) => [
                'material'       => $r['material'],
                'unit'           => $r['Unit'],
                'total_received' => round((float)$r['total_received'], 3),
                'last_grn_date'  => $r['last_grn_date'],
            ], $grnRows),
            'note' => 'Stock figures show total received via GRN. Material consumption/issue tracking may not be active.',
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_project_documents
    // -----------------------------------------------------------------------
    private function toolGetProjectDocuments($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid      = (int)$project['Project_Id'];
        $fileName = trim($input['file_name'] ?? '');

        $params     = [':pid' => $pid];
        $fileFilter = '';
        if ($fileName !== '') {
            $fileFilter      = "AND LOWER(pf.original_name) LIKE :fn";
            $params[':fn']   = '%' . strtolower($fileName) . '%';
        }

        $files = $db->createCommand("
            SELECT pf.id, pf.file_type, pf.filename, pf.original_name,
                   DATE_FORMAT(pf.uploaded_at, '%d %b %Y') AS uploaded_at
            FROM project_files pf
            WHERE pf.project_id = :pid {$fileFilter}
            ORDER BY pf.uploaded_at DESC
            LIMIT 50
        ", $params)->queryAll();

        if (empty($files)) {
            return ['status' => 'no_data', 'reason' => 'No documents found for project "' . $project['Name'] . '"' . ($fileName ? " matching \"{$fileName}\"" : '') . '.'];
        }

        $uploadDir = Yii::getAlias('@webroot') . '/uploads/projects/';
        $result    = [];
        foreach ($files as $f) {
            $entry = [
                'id'          => (int)$f['id'],
                'type'        => $f['file_type'],
                'name'        => $f['original_name'],
                'uploaded_at' => $f['uploaded_at'],
            ];

            if ($fileName !== '') {
                $path = $uploadDir . $f['filename'];
                $ext  = strtolower(pathinfo($f['original_name'], PATHINFO_EXTENSION));
                if (file_exists($path)) {
                    if (in_array($ext, ['txt', 'csv'])) {
                        $entry['content'] = mb_substr(file_get_contents($path), 0, 8000);
                    } elseif ($ext === 'pdf') {
                        $tmp = tempnam(sys_get_temp_dir(), 'pdf_');
                        $out = shell_exec('pdftotext ' . escapeshellarg($path) . ' ' . escapeshellarg($tmp) . ' 2>/dev/null && cat ' . escapeshellarg($tmp));
                        @unlink($tmp);
                        $entry['content'] = $out ? mb_substr($out, 0, 8000) : '[PDF — text extraction not available on this server.]';
                    } else {
                        $entry['content'] = '[Binary file — ' . strtoupper($ext) . ' document. Cannot extract text for chatbot.]';
                    }
                } else {
                    $entry['content'] = '[File not found on server]';
                }
            }

            $result[] = $entry;
        }

        return [
            'status'    => 'ok',
            'project'   => $project['Name'],
            'count'     => count($result),
            'documents' => $result,
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_project_briefing
    // The "dashboard story" — complete context Claude reads before answering
    // any project question. PHP computes every metric; Claude only narrates.
    // -----------------------------------------------------------------------
    private function toolGetProjectBriefing($db, $input)
    {
        $project = $this->resolveProject($db, $input['project_name'] ?? '');
        if (isset($project['status'])) return $project;

        $pid  = (int)$project['Project_Id'];
        $today = date('Y-m-d');

        // ── 1. Schedule span ────────────────────────────────────────────────
        $schedRow = $db->createCommand(
            "SELECT MIN(start_date)    AS plan_start,
                    MAX(end_date)      AS plan_end,
                    DATEDIFF(MAX(end_date), MIN(start_date)) + 1 AS planned_duration_days,
                    COUNT(*)           AS total_acts,
                    SUM(completed_status) AS completed_acts,
                    SUM(CASE WHEN completed_status=0 AND end_date < CURDATE() THEN 1 ELSE 0 END) AS overdue_acts,
                    SUM(CASE WHEN critical_status='Yes' THEN 1 ELSE 0 END) AS critical_acts,
                    SUM(CASE WHEN critical_status='Yes' AND completed_status=0 AND end_date < CURDATE() THEN 1 ELSE 0 END) AS critical_overdue
             FROM scheduleactivities
             WHERE projectId=:pid AND status=0 AND old_duration > 0",
            [':pid' => $pid]
        )->queryOne();

        // ── 2. Physical progress ─────────────────────────────────────────────
        $progressRow = $db->createCommand(
            "SELECT
                AVG(CASE
                    WHEN sa.quantity > 0 AND rpt.cumulated_qty > 0
                         THEN LEAST(rpt.cumulated_qty / sa.quantity * 100, 100)
                    WHEN sa.completed_status = 1 THEN 100
                    ELSE 0
                END) AS actual_progress_pct,
                AVG(CASE
                    WHEN sa.old_duration > 0 AND sa.start_date <= CURDATE()
                         THEN LEAST(DATEDIFF(CURDATE(), sa.start_date) / sa.old_duration * 100, 100)
                    WHEN sa.completed_status = 1 THEN 100
                    ELSE 0
                END) AS planned_progress_pct
             FROM scheduleactivities sa
             LEFT JOIN (
                 SELECT activity_id, MAX(cumulated_qty) AS cumulated_qty
                 FROM schedule_progress_report GROUP BY activity_id
             ) rpt ON rpt.activity_id = sa.id
             WHERE sa.projectId=:pid AND sa.status=0",
            [':pid' => $pid]
        )->queryOne();

        $actualProg  = round((float)($progressRow['actual_progress_pct']  ?? 0), 1);
        $plannedProg = round((float)($progressRow['planned_progress_pct'] ?? 0), 1);
        $schedVariance = round($actualProg - $plannedProg, 1);

        // ── 3. Time position ─────────────────────────────────────────────────
        $planStart = $schedRow['plan_start'] ?? null;
        $planEnd   = $schedRow['plan_end']   ?? null;
        $plannedDays = (int)($schedRow['planned_duration_days'] ?? 0);
        $elapsedDays = ($planStart && $planStart <= $today)
            ? (int)((strtotime($today) - strtotime($planStart)) / 86400) + 1
            : 0;
        $daysRemaining = $planEnd ? max(0, (int)((strtotime($planEnd) - strtotime($today)) / 86400)) : null;
        $pctTimeElapsed = ($plannedDays > 0) ? round($elapsedDays / $plannedDays * 100, 1) : 0;

        // Forecast end from critical path delay
        $critDelayRow = $db->createCommand(
            "SELECT MAX(DATEDIFF(CURDATE(), end_date)) AS max_delay_days,
                    MAX(end_date) AS critical_plan_end
             FROM scheduleactivities
             WHERE projectId=:pid AND status=0 AND critical_status='Yes'
               AND completed_status=0 AND end_date < CURDATE()",
            [':pid' => $pid]
        )->queryOne();
        $critDelay   = max(0, (int)($critDelayRow['max_delay_days'] ?? 0));
        $forecastEnd = ($planEnd && $critDelay > 0)
            ? date('Y-m-d', strtotime($planEnd . ' +' . $critDelay . ' days'))
            : $planEnd;

        $scheduleStatus = 'on_track';
        if ($schedVariance < -5)       $scheduleStatus = 'delayed';
        elseif ($schedVariance > 5)    $scheduleStatus = 'ahead';

        // ── 4. Cost position ─────────────────────────────────────────────────
        // Total estimated project cost (BOQ)
        $estRow = $db->createCommand(
            "SELECT SUM(pen.activity_qty * COALESCE(res.unit_cost,0)) AS total_estimate
             FROM pricing_estimate_new pen
             LEFT JOIN (
                 SELECT activity_id, SUM(rate * quantity) AS unit_cost
                 FROM pricing_estimate_resources_new
                 WHERE project_id=:pid AND pricing_status=0
                 GROUP BY activity_id
             ) res ON res.activity_id = pen.activity_Id
             WHERE pen.project_Id=:pid2 AND pen.pricing_status=0",
            [':pid' => $pid, ':pid2' => $pid]
        )->queryOne();
        $totalEstimate = round((float)($estRow['total_estimate'] ?? 0), 2);

        // ECWD — estimated cost of work done (progress × estimated cost per activity summed)
        $ecwdRow = $db->createCommand(
            "SELECT SUM(
                 LEAST(COALESCE(rpt.cumulated_qty,0) / NULLIF(sa.quantity,0), 1)
                 * COALESCE(actcost.activity_cost, 0)
             ) AS ecwd
             FROM scheduleactivities sa
             LEFT JOIN (
                 SELECT activity_id, MAX(cumulated_qty) AS cumulated_qty
                 FROM schedule_progress_report GROUP BY activity_id
             ) rpt ON rpt.activity_id = sa.id
             LEFT JOIN (
                 SELECT pen.activity_Id,
                        pen.activity_qty * COALESCE(SUM(pern.rate * pern.quantity), 0) AS activity_cost
                 FROM pricing_estimate_new pen
                 LEFT JOIN pricing_estimate_resources_new pern
                   ON pern.activity_id=pen.activity_Id AND pern.project_id=:pid AND pern.pricing_status=0
                 WHERE pen.project_Id=:pid2 AND pen.pricing_status=0
                 GROUP BY pen.activity_Id, pen.activity_qty
             ) actcost ON actcost.activity_Id = sa.activity_id
             WHERE sa.projectId=:pid3 AND sa.status=0",
            [':pid' => $pid, ':pid2' => $pid, ':pid3' => $pid]
        )->queryOne();
        $ecwd = round((float)($ecwdRow['ecwd'] ?? 0), 2);

        // ACWD — actual cost of work done (GRN + certified MB)
        $grnCostRow = $db->createCommand(
            "SELECT SUM(g.GRN_Quantity * por.rate) AS grn_total
             FROM goods_received_note g
             JOIN purchase_order_resources por ON por.order_id = g.GRN_Purchase_Order
             JOIN purchase_orders po ON po.order_id = por.order_id
             WHERE po.project_id=:pid AND po.delete_status=0
               AND por.delete_status=0 AND g.delete_status=0",
            [':pid' => $pid]
        )->queryOne();
        $grnCost = (float)($grnCostRow['grn_total'] ?? 0);

        $mbRows = $db->createCommand(
            "SELECT entries FROM wo_measurement_book
             WHERE project_id=:pid AND sent_status=1 AND delete_status=0",
            [':pid' => $pid]
        )->queryAll();
        $mbCost = 0.0;
        foreach ($mbRows as $mb) {
            foreach (json_decode($mb['entries'] ?? '[]', true) ?: [] as $entry) {
                foreach ($entry['tasks'] ?? [] as $task) {
                    $mbCost += (float)($task['work_done'] ?? 0) * (float)($task['rate'] ?? 0);
                }
            }
        }
        $acwd = round($grnCost + $mbCost, 2);

        $costVariance = round($ecwd - $acwd, 2);
        $cpi = ($acwd > 0) ? round($ecwd / $acwd, 3) : null;
        $costStatus = $cpi === null ? 'no_actuals' : ($cpi >= 1 ? 'under_budget' : 'over_budget');

        // ── 5. Procurement health ────────────────────────────────────────────
        $poRow = $db->createCommand(
            "SELECT COUNT(*) AS total_pos,
                    SUM(CASE WHEN po.delete_status=0 THEN 1 ELSE 0 END) AS active_pos
             FROM purchase_orders po
             WHERE po.project_id=:pid",
            [':pid' => $pid]
        )->queryOne();

        $grnCountRow = $db->createCommand(
            "SELECT COUNT(DISTINCT po.order_id) AS pos_with_grn
             FROM purchase_orders po
             JOIN goods_received_note g ON g.GRN_Purchase_Order = po.order_id
             WHERE po.project_id=:pid AND po.delete_status=0 AND g.delete_status=0",
            [':pid' => $pid]
        )->queryOne();

        $totalPos     = (int)($poRow['active_pos'] ?? 0);
        $posWithGrn   = (int)($grnCountRow['pos_with_grn'] ?? 0);
        $posPending   = max(0, $totalPos - $posWithGrn);

        // ── 6. Top overdue activities (up to 5 for alerts) ──────────────────
        $overdueActs = $db->createCommand(
            "SELECT sa.name, sa.end_date,
                    DATEDIFF(CURDATE(), sa.end_date) AS days_overdue,
                    sa.critical_status
             FROM scheduleactivities sa
             WHERE sa.projectId=:pid AND sa.status=0
               AND sa.completed_status=0 AND sa.end_date < CURDATE()
             ORDER BY days_overdue DESC LIMIT 5",
            [':pid' => $pid]
        )->queryAll();

        // ── 7. Activities not yet started but past planned start ─────────────
        $notStartedLate = (int)$db->createCommand(
            "SELECT COUNT(*) FROM scheduleactivities sa
             LEFT JOIN schedule_progress_report spr ON spr.activity_id = sa.id
             WHERE sa.projectId=:pid AND sa.status=0 AND sa.completed_status=0
               AND sa.start_date < CURDATE()
               AND (spr.activity_id IS NULL OR spr.cumulated_qty IS NULL OR spr.cumulated_qty=0)",
            [':pid' => $pid]
        )->queryScalar();

        // ── 8. Build alerts list ─────────────────────────────────────────────
        $alerts = [];

        if ($schedVariance < -10) {
            $alerts[] = "Schedule is behind by " . abs($schedVariance) . "% — actual progress {$actualProg}% vs planned {$plannedProg}%.";
        }
        if ((int)($schedRow['critical_overdue'] ?? 0) > 0) {
            $alerts[] = (int)$schedRow['critical_overdue'] . " critical path activit" .
                ((int)$schedRow['critical_overdue'] === 1 ? 'y is' : 'ies are') .
                " overdue — project completion date is at risk.";
        }
        if ($critDelay > 0) {
            $alerts[] = "Forecast project end is {$forecastEnd} — {$critDelay} days later than planned ({$planEnd}).";
        }
        if ($cpi !== null && $cpi < 0.9) {
            $alerts[] = "Cost overrun: CPI = {$cpi} (spending ₹" . number_format(1 / $cpi, 2) . " for every ₹1 of work done). ACWD ₹" . number_format($acwd, 2) . " vs ECWD ₹" . number_format($ecwd, 2) . ".";
        }
        if ($notStartedLate > 0) {
            $alerts[] = "{$notStartedLate} activit" . ($notStartedLate === 1 ? 'y has' : 'ies have') .
                " not started but past their planned start date.";
        }
        foreach ($overdueActs as $oa) {
            if ((string)$oa['critical_status'] === 'Yes') {
                $alerts[] = "Critical activity \"{$oa['name']}\" is {$oa['days_overdue']} days overdue (planned end: {$oa['end_date']}).";
            }
        }
        if ($posPending > 0) {
            $alerts[] = "{$posPending} purchase order" . ($posPending === 1 ? '' : 's') .
                " raised but no goods received yet.";
        }
        if (empty($alerts)) {
            $alerts[] = "No critical alerts at this time.";
        }

        // ── 9. Pre-narrate every metric as a complete sentence ───────────────
        // Claude must copy these sentences directly. No arithmetic allowed.

        $fmt = fn($v) => '₹' . number_format((float)$v, 2);

        // Project identity sentence
        $projectSentence = "{$project['Name']} is a project for {$project['client_name']}"
            . ($project['location'] ? " located at {$project['location']}" : '')
            . ". Contract value is " . $fmt($project['project_value']) . ".";

        // Time position sentence
        $timeSentence = "{$elapsedDays} of {$plannedDays} planned days have elapsed ({$pctTimeElapsed}% of timeline consumed).";
        if ($planEnd) {
            $timeSentence .= " Planned completion date is " . date('d M Y', strtotime($planEnd)) . ".";
        }
        if ($critDelay > 0 && $forecastEnd) {
            $timeSentence .= " Current forecast completion is " . date('d M Y', strtotime($forecastEnd))
                . " — {$critDelay} day" . ($critDelay === 1 ? '' : 's') . " behind plan.";
        } elseif ($planEnd && $planEnd > $today) {
            $timeSentence .= " Project is on schedule.";
        }

        // Physical progress sentence
        $totalActs     = (int)($schedRow['total_acts']      ?? 0);
        $completedActs = (int)($schedRow['completed_acts']  ?? 0);
        $overdueActs2  = (int)($schedRow['overdue_acts']    ?? 0);
        $critOverdue   = (int)($schedRow['critical_overdue']?? 0);

        $progressSentence = "Physical progress is {$actualProg}% against a planned {$plannedProg}%"
            . " — schedule variance is " . ($schedVariance >= 0 ? "+{$schedVariance}%" : "{$schedVariance}%")
            . " (" . ($schedVariance >= 0 ? "ahead of plan" : "behind plan") . ").";
        $progressSentence .= " {$completedActs} of {$totalActs} activities are complete.";
        if ($overdueActs2 > 0) {
            $progressSentence .= " {$overdueActs2} activit" . ($overdueActs2 === 1 ? 'y is' : 'ies are')
                . " overdue, of which {$critOverdue} " . ($critOverdue === 1 ? 'is' : 'are') . " on the critical path.";
        }
        if ($notStartedLate > 0) {
            $progressSentence .= " {$notStartedLate} activit" . ($notStartedLate === 1 ? 'y has' : 'ies have')
                . " not started yet but are past their planned start date.";
        }

        // Cost position sentence
        if ($acwd > 0 && $ecwd > 0) {
            $cpiDisp = $cpi !== null ? $cpi : 'N/A';
            $varianceWord = $costVariance >= 0 ? 'saving' : 'overrun';
            $costSentence = "Estimated cost of work done (ECWD) is " . $fmt($ecwd)
                . ". Actual cost of work done (ACWD) is " . $fmt($acwd)
                . ". Cost variance is " . $fmt(abs($costVariance)) . " " . $varianceWord
                . " (CPI = {$cpiDisp}).";
            if ($totalEstimate > 0) {
                $costSentence .= " Total project estimate (BOQ) is " . $fmt($totalEstimate) . ".";
            }
        } elseif ($totalEstimate > 0) {
            $costSentence = "Total project estimate (BOQ) is " . $fmt($totalEstimate)
                . ". No actual cost data recorded yet.";
        } else {
            $costSentence = "No cost data available for this project.";
        }

        // Procurement sentence
        if ($totalPos > 0) {
            $procSentence = "{$totalPos} purchase order" . ($totalPos === 1 ? '' : 's') . " raised."
                . " {$posWithGrn} " . ($posWithGrn === 1 ? 'has' : 'have') . " goods received."
                . " {$posPending} " . ($posPending === 1 ? 'is' : 'are') . " pending delivery.";
        } else {
            $procSentence = "No purchase orders raised for this project yet.";
        }

        // ── 10. Return narrated briefing ──────────────────────────────────────
        return [
            'status'               => 'ok',
            'INSTRUCTION'          => 'Copy these sentences directly into your answer. Do NOT recalculate any number. Do NOT rephrase the figures.',
            'project_sentence'     => $projectSentence,
            'time_sentence'        => $timeSentence,
            'progress_sentence'    => $progressSentence,
            'cost_sentence'        => $costSentence,
            'procurement_sentence' => $procSentence,
            'alerts'               => $alerts,
            'alert_instruction'    => 'Start your answer by stating the most important alert(s) from the alerts list using the exact wording provided.',
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: query_database — Rule 5: SELECT-only, 50-row cap
    // -----------------------------------------------------------------------
    private function toolQueryDatabase($db, $input)
    {
        $sql    = trim($input['sql']    ?? '');
        $reason = trim($input['reason'] ?? '');

        if ($sql === '') {
            return ['status' => 'no_data', 'reason' => 'No SQL query provided.'];
        }

        $validationError = $this->validateSelectOnly($sql);
        if ($validationError) {
            return ['status' => 'rejected', 'reason' => $validationError];
        }

        // Enforce row limit — append LIMIT if not already present
        if (!preg_match('/\bLIMIT\s+\d+/i', $sql)) {
            $sql = rtrim($sql, '; ') . ' LIMIT 50';
        } else {
            // Cap any existing LIMIT at 50
            $sql = preg_replace_callback('/\bLIMIT\s+(\d+)/i', function($m) {
                return 'LIMIT ' . min((int)$m[1], 50);
            }, $sql);
        }

        try {
            $rows = $db->createCommand($sql)->queryAll();
        } catch (\Exception $e) {
            return ['status' => 'error', 'reason' => 'Query failed: ' . $e->getMessage()];
        }

        if (empty($rows)) {
            return ['status' => 'no_data', 'reason' => 'Query returned no rows.', 'query_used' => $sql, 'query_reason' => $reason];
        }

        return [
            'status'       => 'ok',
            'row_count'    => count($rows),
            'rows'         => $rows,
            'query_used'   => $sql,
            'query_reason' => $reason,
        ];
    }

    // -----------------------------------------------------------------------
    // Main action — tool-calling loop with logging (Rule 7) and grounding (Rule 6)
    // -----------------------------------------------------------------------
    public function actionChat()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $message    = trim(Yii::$app->request->post('message', ''));
        $historyRaw = Yii::$app->request->post('history', '[]');
        $history    = is_array($historyRaw) ? $historyRaw : (json_decode($historyRaw, true) ?: []);
        if (!$message) return ['error' => 'No message'];

        $apiKey = $this->getApiKey();
        if (!$apiKey) return ['error' => 'API key not configured'];

        // Rule 7: unique session id per request for grouping log rows
        $sessionId = substr(md5(uniqid('', true)), 0, 16);

        // Rule 6: grounding rule in system prompt
        $systemPrompt =
            "You are a read-only data assistant for Opiam Analytics ERP, a construction project management system.\n\n" .

            "GROUNDING RULE (HIGHEST PRIORITY): Only state facts that come directly from a tool result. " .
            "If a tool returns {\"status\":\"no_data\"}, say explicitly: 'That information is not available in the system.' " .
            "If a tool returns {\"status\":\"ambiguous\"}, list the matched options and ask the user to clarify which one. " .
            "Never guess, estimate, interpolate, or fill gaps from general knowledge. " .
            "Never perform arithmetic yourself — all computed metrics (costs, variances, productivity, durations, cycle times, progress %) are pre-calculated in the tool result. Read the number; do not recalculate it.\n\n" .

            "PROJECT BRIEFING RULE (MANDATORY):\n" .
            "When the user asks any question about a specific project — its status, progress, cost, delays, activities, procurement, or performance — " .
            "you MUST call get_project_briefing FIRST before calling any other tool.\n\n" .

            "COPY RULE (ABSOLUTE — NO EXCEPTIONS):\n" .
            "The briefing returns pre-written sentences: project_sentence, time_sentence, progress_sentence, cost_sentence, procurement_sentence, and alerts. " .
            "These sentences are written by the system in PHP with all numbers already computed and formatted correctly. " .
            "You MUST copy these sentences EXACTLY as written into your answer. " .
            "You MUST NOT rephrase them, recalculate any figure, look up GRN records, measurement books, PO tables, or any other data source to verify or replace the numbers in these sentences. " .
            "The numbers in the sentences ARE the answer. Treat them as final facts. " .
            "If the briefing sentences fully answer the question, stop there — do not call any other tool.\n\n" .

            "WHEN TO CALL ADDITIONAL TOOLS:\n" .
            "Only call a second tool if the user asks for specific detail the briefing sentences do not cover:\n" .
            "- get_activity_kpi → specific activity production rate, cycle time, task breakdown, cause of delay.\n" .
            "- get_schedule_activities → list of activities filtered by status (delayed, critical, ongoing, completed).\n" .
            "- get_activity_costs → estimated cost of a specific activity.\n" .
            "- get_activity_actual_cost → actual spend on a specific activity.\n" .
            "- get_stock → materials at site, inventory levels.\n" .
            "- get_activity_resources → planned resource breakdown for an activity.\n" .
            "- get_project_estimate → total BOQ budget and profitability margin.\n" .
            "- query_database → ONLY when no other tool covers the question.\n\n" .

            "AMBIGUITY HANDLING:\n" .
            "If get_activity_kpi returns status=ambiguous, present the matched_activities list to the user and ask them to specify which activity they mean. Do not pick one yourself.\n\n" .

            "COST UNIT RULE:\n" .
            "All activity costs use SCHEDULE units. Report unit_cost_per_sched_unit as the unit cost. " .
            "Never report unit_cost_per_est_unit. When units_same=false, explain the conversion.\n\n" .

            "CRITICAL TERMINOLOGY:\n" .
            "- 'ECWD' / 'estimated cost of work done' → in the briefing cost_sentence, already written out.\n" .
            "- 'ACWD' / 'actual cost' / 'actual spend' → in the briefing cost_sentence, already written out.\n" .
            "- 'CPI' → in the briefing cost_sentence, already written out.\n" .
            "- 'contract value' → in the briefing project_sentence, already written out.\n" .
            "- 'schedule variance' → in the briefing progress_sentence, already written out.\n" .
            "- 'unit cost of activity' → call get_activity_costs → use unit_cost_per_sched_unit.\n" .
            "- 'actual cost of activity' → call get_activity_actual_cost → use actual_cost field.\n" .
            "- 'target production' → call get_activity_kpi → use target_production_per_day.\n" .
            "- 'actual production' → call get_activity_kpi → use actual_production_per_day.\n" .
            "- 'stock / inventory' → call get_stock → use total_received.\n\n" .

            "ANSWER FORMAT:\n" .
            "1. Start with the most important alert from the briefing alerts list — use the exact wording.\n" .
            "2. Follow with the relevant briefing sentences copied exactly.\n" .
            "3. Only add detail from a second tool if the user asked for it specifically.\n" .
            "4. Never mention internal field names, JSON keys, or tool names in your answer.\n" .
            "5. Do not add commentary, suggestions, or emojis.\n\n" .
            "TODAY'S DATE: " . date('d F Y') . ".";

        $messages = [];
        foreach ((array)$history as $h) {
            if (empty($h['role']) || !isset($h['content'])) continue;
            $content = $h['content'];
            if (!is_string($content)) $content = is_array($content) ? json_encode($content) : (string)$content;
            if ($content === '') continue;
            $messages[] = ['role' => $h['role'], 'content' => $content];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        $tools = $this->toolDefinitions();

        for ($round = 0; $round < 5; $round++) {

            $payload = json_encode([
                'model'       => 'claude-sonnet-4-6',
                'max_tokens'  => 1024,
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
                CURLOPT_TIMEOUT => 30,
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
                        // Rule 7: stamp the final reply onto all log rows from this session
                        $this->updateLogReply($sessionId, $block['text']);
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

                    $result = $this->executeTool($toolName, $toolInput);

                    // Rule 7: log every tool call
                    $this->logToolCall($sessionId, $toolName, $toolInput, $result);

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
