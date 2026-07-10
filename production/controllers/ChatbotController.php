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
    // Tool definitions sent to Claude
    // -----------------------------------------------------------------------
    private function toolDefinitions()
    {
        return [
            [
                'name'        => 'get_projects',
                'description' => 'Returns the list of all active projects with their contract value, client, location, start date and end date.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => (object)[],
                ],
            ],
            [
                'name'        => 'get_activity_kpi',
                'description' => 'Returns the full KPI data for a single schedule activity: planned vs actual quantity, progress %, target production rate, actual production rate, planned duration, elapsed days, projected duration, task-level productivity, and cause-of-delay breakdown. Use this whenever the user asks about production rate, target production, productivity, progress, duration, or delays for a specific activity.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string', 'description' => 'Full or partial project name'],
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
                'description' => 'Returns a list of schedule activities for a project with start/end dates, planned quantity, qty done, progress %, and delay status. Use when the user wants to list activities, find overdue tasks, or check schedule status.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name' => ['type' => 'string', 'description' => 'Full or partial project name'],
                        'filter'       => ['type' => 'string', 'enum' => ['all', 'critical', 'delayed', 'completed', 'ongoing'], 'description' => 'Which activities to return'],
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
                'description' => 'Returns cost of work done (ECWD = estimated cost, ACWD = actual cost) and variance for a project.',
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
                'description' => 'Returns the TOTAL estimated cost of the project (the full BOQ/pricing estimate — what the project is budgeted to cost in total). Use this when the user asks "what is the estimated cost of the project", "total project budget", "how much does the project cost to build", "project estimate", or "profitability". This is DIFFERENT from ECWD (estimated cost of work done) which is only the cost of units completed so far.',
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
                'description' => 'Returns planned estimated cost per activity: unit_cost (cost per one unit of the activity) and total_cost (unit_cost × activity_qty). Use when the user asks which activity has the highest/lowest cost, the total or unit estimated cost of a specific activity, or cost per Cum/Sqm/unit of work.',
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
                'description' => 'Returns the planned resource allocation for an activity: each resource (labour, material, equipment, subcontractor) with its planned quantity per unit, unit rate, and amount per unit. Use when the user asks "what resources are needed for an activity", "what is the rate of RMC/steel/labour for an activity", "how much concrete is needed per unit", "what is the unit rate breakdown", or "resource-wise cost breakdown".',
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
                'description' => 'Returns the ACTUAL cost incurred per activity from certified measurement books (MB), i.e. subcontract work done billed so far. Use when the user asks "how much has been spent on an activity", "actual cost of concrete laying", "what is the actual vs estimated cost for an activity", or "are we over/under budget on an activity".',
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
                'description' => 'Returns material stock position at site: total quantity received via GRN per material. Use when the user asks about stock, inventory, materials received, "how much cement/steel/material is at site", or "what materials do we have".',
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
                'description' => 'Returns the list of uploaded project documents (contracts, correspondence, drawings, reports, etc.) for a project, including file name, type, and upload date. If the user asks about the content of a specific document, also returns the text content of that file. Use when the user asks about documents, files, contracts, letters, reports, or any uploaded content for a project.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'project_name'  => ['type' => 'string', 'description' => 'Full or partial project name'],
                        'file_name'     => ['type' => 'string', 'description' => 'Optional: partial name of a specific file to read the content of'],
                    ],
                    'required' => ['project_name'],
                ],
            ],
        ];
    }

    // -----------------------------------------------------------------------
    // Tool execution — called when Claude requests a tool
    // -----------------------------------------------------------------------
    private function executeTool($name, $input)
    {
        $db = Yii::$app->db;

        switch ($name) {

            case 'get_projects':
                return $this->toolGetProjects($db);

            case 'get_activity_kpi':
                return $this->toolGetActivityKpi($db, $input);

            case 'get_project_progress':
                return $this->toolGetProjectProgress($db, $input);

            case 'get_schedule_activities':
                return $this->toolGetScheduleActivities($db, $input);

            case 'get_materials':
                return $this->toolGetMaterials($db, $input);

            case 'get_cost_dashboard':
                return $this->toolGetCostDashboard($db, $input);

            case 'get_work_orders_and_mb':
                return $this->toolGetWorkOrdersAndMb($db, $input);

            case 'get_project_estimate':
                return $this->toolGetProjectEstimate($db, $input);

            case 'get_activity_costs':
                return $this->toolGetActivityCosts($db, $input);

            case 'get_activity_resources':
                return $this->toolGetActivityResources($db, $input);

            case 'get_activity_actual_cost':
                return $this->toolGetActivityActualCost($db, $input);

            case 'get_stock':
                return $this->toolGetStock($db, $input);

            case 'get_project_documents':
                return $this->toolGetProjectDocuments($db, $input);

            default:
                return ['error' => "Unknown tool: {$name}"];
        }
    }

    // -----------------------------------------------------------------------
    // Tool: get_projects
    // -----------------------------------------------------------------------
    private function toolGetProjects($db)
    {
        $rows = $db->createCommand("
            SELECT Project_Id, Name, client_name, location,
                   start_date, end_date, project_value
            FROM projects WHERE Status = 0 ORDER BY Name ASC LIMIT 50
        ")->queryAll();

        return array_map(function($p) {
            return [
                'id'             => (int)$p['Project_Id'],
                'name'           => $p['Name'],
                'client'         => $p['client_name'],
                'location'       => $p['location'],
                'start_date'     => $p['start_date'],
                'end_date'       => $p['end_date'],
                'contract_value' => (float)$p['project_value'],
            ];
        }, $rows);
    }

    // -----------------------------------------------------------------------
    // Tool: get_activity_kpi
    // Replicates _buildKpi() from ProjectsmainController exactly,
    // so chatbot answers always match the KPI dashboard.
    // -----------------------------------------------------------------------
    private function toolGetActivityKpi($db, $input)
    {
        $activityName = trim($input['activity_name'] ?? '');
        $projectName  = trim($input['project_name']  ?? '');

        // Find the schedule activity — case-insensitive, word-by-word matching
        $where = "sa.status = 0";
        $params = [];

        if ($projectName !== '') {
            $where .= " AND LOWER(p.Name) LIKE :pname";
            $params[':pname'] = '%' . strtolower($projectName) . '%';
        }

        // Split activity name into meaningful words (4+ chars), strip trailing 's', match each
        if ($activityName !== '') {
            $raw = preg_replace('/[^a-z0-9 ]/i', ' ', $activityName);
            $words = array_values(array_filter(explode(' ', $raw), fn($w) => strlen($w) >= 4));
            foreach ($words as $i => $word) {
                $word = strtolower(rtrim($word, 's')); // "piles"->"pile", "beams"->"beam"
                $key  = ':aw' . $i;
                $where .= " AND LOWER(sa.name) LIKE {$key}";
                $params[$key] = '%' . $word . '%';
            }
        }

        $act = $db->createCommand("
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
            LIMIT 1
        ", $params)->queryOne();

        if (!$act) {
            return ['error' => 'Activity not found. Check the activity name or project name.'];
        }

        $actid = (int)$act['id'];
        $pid   = (int)$act['pid'];

        // schedule_activity_new
        $san = $db->createCommand(
            "SELECT progress, Workhours, Cycles, Resourceunits
             FROM schedule_activity_new WHERE actvity_id=$actid"
        )->queryOne();

        $target_qty = (float)($act['quantity'] ?? 0);
        $unit       = $act['unit'] ?? '';
        $wh         = $san ? (int)$san['Workhours']  : 8;
        $cycles     = $san ? (float)$san['Cycles']   : 1;
        $progress   = $san ? (float)$san['progress'] : 0;

        // Actual qty
        $pr = $db->createCommand(
            "SELECT cumulated_qty FROM schedule_progress_report
             WHERE activity_id=$actid ORDER BY updated_at DESC LIMIT 1"
        )->queryOne();
        $actual_qty = $pr ? (float)$pr['cumulated_qty'] : 0;

        $work_done_pct = ($target_qty > 0)
            ? round($actual_qty / $target_qty * 100, 1) : $progress;

        $b_duration     = (float)($act['old_duration'] ?? 0);
        $duration       = (float)($act['duration']     ?? 0);
        $resource_units = max(1, (float)($act['resource_units'] ?? 1));

        $target_cycle    = ($target_qty > 0)
            ? round(($b_duration / $target_qty) * $wh, 3) : 0;
        $planned_per_day = ($b_duration > 0 && $target_qty > 0)
            ? round($target_qty / $b_duration, 3) : 0;

        // Cumulative break hours
        $brk = $db->createCommand(
            "SELECT SUM(break_hour) AS total_break FROM schedule_progress_report_log
             WHERE activity_id=$actid"
        )->queryOne();
        $cum_break = $brk ? (float)$brk['total_break'] : 0;

        // Date anchors (same logic as _buildKpi)
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
            $elapsed      = max(1, (strtotime($last_reported_date) - strtotime($act_start_date)) / 86400);
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

        // Cause of delay
        $cod_rows = $db->createCommand(
            "SELECT cd.title, COUNT(strc.id) AS cnt
             FROM schedule_task_report_cause_of_delays strc
             JOIN cause_of_delays cd ON cd.id = strc.cause_of_delay_id
             WHERE strc.activity_id=$actid GROUP BY cd.id, cd.title"
        )->queryAll();
        $total_cod = array_sum(array_column($cod_rows, 'cnt'));
        $cause_of_delay = array_map(function($r) use ($total_cod) {
            return [
                'reason'  => $r['title'],
                'count'   => (int)$r['cnt'],
                'percent' => $total_cod > 0 ? round($r['cnt'] / $total_cod * 100) : 0,
            ];
        }, $cod_rows);

        // Tasks (same join as _buildKpi)
        $sa_act = $db->createCommand(
            "SELECT activity_id FROM scheduleactivities WHERE id=$actid"
        )->queryOne();
        $wbn_id = $sa_act ? (int)$sa_act['activity_id'] : 0;
        $wbn_row = $wbn_id ? $db->createCommand(
            "SELECT activity_Id FROM workgroup_activities_new WHERE id=$wbn_id"
        )->queryOne() : null;
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

        // MB qty per task
        $taskMbQty = [];
        $mbRows = $db->createCommand(
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
                'task_name'              => $t['task_name'],
                'unit'                   => $t['task_unit'],
                'target_production_per_day' => round((float)$t['productivity'] * max(1, (float)$t['resource_units']), 3),
                'productivity_per_resource' => (float)$t['productivity'],
                'resource_units'         => (float)$t['resource_units'],
                'actual_production_per_day' => $actual,
                'task_planned_qty'       => round($tqu * $target_qty, 3),
                'planned_duration_days'  => (float)$t['planned_duration'],
                'actual_duration_days'   => ($elapsed > 0 && $mbQty > 0) ? round($elapsed / $mbQty, 3) : 0,
            ];
        }, $task_rows);

        return [
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
                : '',
            'reported_start_date'        => $reported_start,
            'last_reported_date'         => $last_reported_date,
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
            'cause_of_delay'             => $cause_of_delay,
            'tasks'                      => $tasks,
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_project_progress
    // -----------------------------------------------------------------------
    private function toolGetProjectProgress($db, $input)
    {
        $projectName = trim($input['project_name'] ?? '');

        $project = $db->createCommand(
            "SELECT Project_Id, Name, client_name, location, project_value
             FROM projects WHERE Status = 0 AND LOWER(Name) LIKE :n LIMIT 1",
            [':n' => '%' . strtolower($projectName) . '%']
        )->queryOne();

        if (!$project) return ['error' => 'Project not found.'];

        $pid = (int)$project['Project_Id'];

        // Schedule-derived dates and progress — authoritative source, same as the Schedule screen.
        // End date is computed as start_date + old_duration - 1 (matching the Schedule UI formula),
        // NOT from the end_date column which can be stale.
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

        // Physical progress: average of per-activity progress
        // = SUM(actual_qty/target_qty) / total activities, weighted equally
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

        // Critical path — use computed end date (start + old_duration - 1) to match UI
        $critRow = $db->createCommand(
            "SELECT COUNT(sa.id) AS crit_cnt,
                    SUM(CASE WHEN sa.completed_status=0 AND DATE_ADD(sa.start_date, INTERVAL sa.old_duration-1 DAY) < CURDATE() THEN 1 ELSE 0 END) AS delayed_cnt,
                    MAX(CASE WHEN sa.completed_status=0 AND DATE_ADD(sa.start_date, INTERVAL sa.old_duration-1 DAY) < CURDATE() THEN DATEDIFF(CURDATE(), DATE_ADD(sa.start_date, INTERVAL sa.old_duration-1 DAY)) ELSE 0 END) AS max_delay_days,
                    MAX(DATE_ADD(sa.start_date, INTERVAL sa.old_duration-1 DAY)) AS critical_planned_end
             FROM scheduleactivities sa
             WHERE sa.projectId=:pid AND sa.status=0 AND sa.critical_status='Yes' AND sa.old_duration > 0",
            [':pid' => $pid]
        )->queryOne();

        // ECWD / ACWD from cache (these are computed by cost dashboard, not derivable here)
        $cacheRows = $db->createCommand(
            "SELECT metric_name, metric_value FROM chatbot_metrics_cache WHERE project_id=:pid",
            [':pid' => $pid]
        )->queryAll();
        $cache = [];
        foreach ($cacheRows as $r) { $cache[$r['metric_name']] = $r['metric_value']; }

        $progress = $progressRow ? round((float)$progressRow['avg_progress'], 1) : 0;

        return [
            'project'                    => $project['Name'],
            'client'                     => $project['client_name'],
            'contract_value'             => (float)$project['project_value'],
            'schedule_start_date'        => $schedRow['sched_start'] ?? null,
            'schedule_end_date'          => $schedRow['sched_end']   ?? null,
            'planned_duration_days'      => $schedRow ? (int)$schedRow['planned_duration_days'] : null,
            'physical_progress_percent'  => $progress,
            'total_activities'           => $schedRow ? (int)$schedRow['total_acts']      : 0,
            'completed_activities'       => $schedRow ? (int)$schedRow['completed_acts']  : 0,
            'delayed_activities'         => $schedRow ? (int)$schedRow['delayed_acts']    : 0,
            'critical_activities_count'  => $critRow  ? (int)$critRow['crit_cnt']         : 0,
            'delayed_critical_count'     => $critRow  ? (int)$critRow['delayed_cnt']      : 0,
            'max_delay_days'             => $critRow  ? (int)$critRow['max_delay_days']   : 0,
            'critical_planned_end'       => $critRow  ? $critRow['critical_planned_end']  : null,
            'estimated_cost_work_done'   => isset($cache['estimated_cost_work_done']) ? (float)$cache['estimated_cost_work_done'] : null,
            'actual_cost_work_done'      => isset($cache['actual_cost_work_done'])    ? (float)$cache['actual_cost_work_done']    : null,
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_schedule_activities
    // -----------------------------------------------------------------------
    private function toolGetScheduleActivities($db, $input)
    {
        $projectName = trim($input['project_name'] ?? '');
        $filter      = $input['filter'] ?? 'all';

        $project = $db->createCommand(
            "SELECT Project_Id, Name FROM projects WHERE Status = 0 AND LOWER(Name) LIKE :n LIMIT 1",
            [':n' => '%' . strtolower($projectName) . '%']
        )->queryOne();

        if (!$project) return ['error' => 'Project not found.'];

        $pid = (int)$project['Project_Id'];

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

        return [
            'project'    => $project['Name'],
            'filter'     => $filter,
            'count'      => count($rows),
            'activities' => array_map(function($a) {
                return [
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
                ];
            }, $rows),
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_materials
    // -----------------------------------------------------------------------
    private function toolGetMaterials($db, $input)
    {
        $projectName  = trim($input['project_name']  ?? '');
        $materialName = trim($input['material_name'] ?? '');

        $project = $db->createCommand(
            "SELECT Project_Id, Name FROM projects WHERE Status = 0 AND LOWER(Name) LIKE :n LIMIT 1",
            [':n' => '%' . strtolower($projectName) . '%']
        )->queryOne();

        if (!$project) return ['error' => 'Project not found.'];

        $pid = (int)$project['Project_Id'];

        $matFilter = '';
        $params = [':pid' => $pid];
        if ($materialName !== '') {
            $matFilter = "AND r.Name LIKE :mname";
            $params[':mname'] = '%' . $materialName . '%';
        }

        // GRN totals per material
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
            ORDER BY total_value DESC
            LIMIT 100
        ", $params)->queryAll();

        // PO totals per material
        $pos = $db->createCommand("
            SELECT por.resource_name AS material, por.unit,
                   SUM(por.qnty) AS ordered_qty,
                   SUM(por.amount) AS ordered_value,
                   COUNT(DISTINCT po.order_id) AS po_count
            FROM purchase_order_resources por
            JOIN purchase_orders po ON po.order_id = por.order_id
            WHERE po.project_id = :pid AND po.delete_status = 0 AND por.delete_status = 0
            GROUP BY por.resource_name, por.unit
            ORDER BY ordered_value DESC
            LIMIT 100
        ", [':pid' => $pid])->queryAll();

        // Store indents
        $indents = $db->createCommand("
            SELECT si.resource_name AS material, si.unit,
                   si.stock_at_site, si.avg_consumption,
                   si.task_name, si.raised_at
            FROM store_indents si
            WHERE si.project_id = :pid
            ORDER BY si.raised_at DESC LIMIT 100
        ", [':pid' => $pid])->queryAll();

        return [
            'project'         => $project['Name'],
            'grn_received'    => $grns,
            'purchase_orders' => $pos,
            'store_indents'   => $indents,
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_cost_dashboard
    // -----------------------------------------------------------------------
    private function toolGetCostDashboard($db, $input)
    {
        $projectName = trim($input['project_name'] ?? '');

        $project = $db->createCommand(
            "SELECT Project_Id, Name FROM projects WHERE Status = 0 AND LOWER(Name) LIKE :n LIMIT 1",
            [':n' => '%' . strtolower($projectName) . '%']
        )->queryOne();

        if (!$project) return ['error' => 'Project not found.'];

        $pid = (int)$project['Project_Id'];

        $cacheRows = $db->createCommand(
            "SELECT metric_name, metric_value, updated_at
             FROM chatbot_metrics_cache WHERE project_id = :pid",
            [':pid' => $pid]
        )->queryAll();
        $cache = [];
        foreach ($cacheRows as $r) {
            $cache[$r['metric_name']] = ['value' => $r['metric_value'], 'updated_at' => $r['updated_at']];
        }
        $cv = fn($k) => $cache[$k]['value'] ?? null;
        $cu = !empty($cache) ? reset($cache)['updated_at'] : null;

        $ecwd = $cv('estimated_cost_work_done');
        $acwd = $cv('actual_cost_work_done');
        $variance = ($ecwd !== null && $acwd !== null) ? (float)$ecwd - (float)$acwd : null;

        return [
            'project'                   => $project['Name'],
            'estimated_cost_work_done'  => $ecwd !== null ? (float)$ecwd : null,
            'actual_cost_work_done'     => $acwd !== null ? (float)$acwd : null,
            'variance'                  => $variance,
            'status'                    => ($variance !== null ? ($variance >= 0 ? 'Under Budget' : 'Over Budget') : 'No data'),
            'cache_as_of'               => $cu,
            'note'                      => $ecwd === null ? 'Cost data not yet cached — open the cost dashboard to populate.' : null,
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_work_orders_and_mb
    // -----------------------------------------------------------------------
    private function toolGetWorkOrdersAndMb($db, $input)
    {
        $projectName  = trim($input['project_name']  ?? '');
        $activityName = trim($input['activity_name'] ?? '');

        $project = $db->createCommand(
            "SELECT Project_Id, Name FROM projects WHERE Status = 0 AND LOWER(Name) LIKE :n LIMIT 1",
            [':n' => '%' . strtolower($projectName) . '%']
        )->queryOne();

        if (!$project) return ['error' => 'Project not found.'];

        $pid = (int)$project['Project_Id'];

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

        $woResult = array_map(function($wo) {
            $items = json_decode($wo['WO_Subject'] ?? '[]', true);
            $actNames = is_array($items) ? array_filter(array_column($items, 'activity_name')) : [];
            return [
                'wo_number'    => $wo['WO_Number'],
                'date'         => $wo['Date_requested'],
                'vendor'       => $wo['vendor_name'],
                'scope'        => $wo['Scope'],
                'activities'   => array_values($actNames),
                'qty'          => (float)$wo['Quantity'],
                'unit'         => $wo['Unit'],
                'rate'         => (float)$wo['Rate'],
                'total'        => (float)$wo['Total'],
                'duration_days' => (int)$wo['Duration'],
                'start_date'   => $wo['start_date'],
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
                        'mb_number'    => $mb['mb_number'],
                        'date'         => $mb['mb_date'],
                        'wo_number'    => $mb['wo_number'],
                        'activity'     => $entryActName,
                        'task'         => $task['task_name'] ?? '',
                        'work_done'    => (float)($task['work_done'] ?? 0),
                        'unit'         => $task['unit'] ?? '',
                        'rate'         => (float)($task['rate'] ?? 0),
                        'value'        => round((float)($task['work_done'] ?? 0) * (float)($task['rate'] ?? 0), 2),
                    ];
                }
            }
        }

        return [
            'project'      => $project['Name'],
            'work_orders'  => $woResult,
            'measurement_books' => $mbResult,
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_project_estimate
    // Total project BOQ estimate — sum of (activity_qty × unit_cost) for all
    // priced activities. This is what the user means by "estimated cost of
    // the project". NOT the same as ECWD (which is cost of work done so far).
    // -----------------------------------------------------------------------
    private function toolGetProjectEstimate($db, $input)
    {
        $projectName = trim($input['project_name'] ?? '');

        $project = $db->createCommand(
            "SELECT Project_Id, Name, project_value FROM projects
             WHERE Status = 0 AND LOWER(Name) LIKE :n LIMIT 1",
            [':n' => '%' . strtolower($projectName) . '%']
        )->queryOne();

        if (!$project) return ['error' => 'Project not found.'];

        $pid = (int)$project['Project_Id'];

        // Sum activity_qty × unit_cost across all priced activities
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

        $totalEstimate = 0.0;
        foreach ($rows as $r) {
            $totalEstimate += (float)$r['activity_qty'] * (float)$r['unit_cost'];
        }

        $contractValue   = (float)$project['project_value'];
        $profitability   = $contractValue > 0 ? $contractValue - $totalEstimate : null;
        $marginPct       = ($contractValue > 0 && $totalEstimate > 0)
            ? round(($profitability / $contractValue) * 100, 1) : null;

        return [
            'project'               => $project['Name'],
            'total_estimate_cost'   => round($totalEstimate, 2),
            'contract_value'        => $contractValue,
            'estimated_profitability' => $profitability !== null ? round($profitability, 2) : null,
            'margin_percent'        => $marginPct,
            'note'                  => 'total_estimate_cost is the full project budget (BOQ). It is NOT the cost of work done so far — use get_cost_dashboard for that.',
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_activity_costs
    // Per-activity planned estimated cost from the pricing estimate (BOQ).
    // Uses pricing_estimate_new → workgroup_activities_new → pricing_estimate_resources_new.
    // Cost = activity_qty × Σ(resource rate × resource quantity).
    // -----------------------------------------------------------------------
    private function toolGetActivityCosts($db, $input)
    {
        $projectName  = trim($input['project_name']  ?? '');
        $activityName = trim($input['activity_name'] ?? '');

        $project = $db->createCommand(
            "SELECT Project_Id, Name FROM projects
             WHERE Status = 0 AND LOWER(Name) LIKE :n LIMIT 1",
            [':n' => '%' . strtolower($projectName) . '%']
        )->queryOne();

        if (!$project) return ['error' => 'Project not found.'];

        $pid = (int)$project['Project_Id'];

        $actFilter = '';
        $params = [':pid' => $pid, ':pid2' => $pid, ':pid3' => $pid];
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
               ON wan.id = pen.activity_Id
              AND wan.project_Id = :pid
              AND wan.pricing_status = 0
             LEFT JOIN pricing_estimate_resources_new pern
               ON pern.activity_id = pen.activity_Id
              AND pern.project_id = :pid2
              AND pern.pricing_status = 0
             LEFT JOIN scheduleactivities sa
               ON sa.activity_id = pen.activity_Id
              AND sa.projectId = :pid3
              AND sa.status = 0
             WHERE pen.project_Id = :pid4 AND pen.pricing_status = 0
             {$actFilter}
             GROUP BY pen.activity_Id, wan.activity_Name, pen.activity_qty, wan.activity_Unit,
                      sa.quantity, sa.unit
             ORDER BY total_cost DESC
             LIMIT 100",
            array_merge($params, [':pid4' => $pid])
        )->queryAll();

        if (!$rows) {
            return ['error' => 'No priced activities found for this project.'];
        }

        $grandTotal = 0.0;
        $activities = [];
        foreach ($rows as $r) {
            $total             = round((float)$r['total_cost'], 2);
            $grandTotal       += $total;
            $unitCostPerEst    = (float)$r['unit_cost_per_est_unit'];
            $estQty            = (float)$r['est_qty'];
            $schedQty          = (float)($r['sched_qty'] ?? 0);
            $schedUnit         = $r['sched_unit'] ?? $r['est_unit'];
            // Unit cost per schedule unit = unitCost × (estQty / schedQty)
            // e.g. ₹68,000/Ton × (45 Ton / 45 Piles) = ₹68,000/Pile
            $ratio             = ($schedQty > 0) ? $estQty / $schedQty : 1.0;
            $unitCostPerSched  = round($unitCostPerEst * $ratio, 2);

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
            'project'     => $project['Name'],
            'activities'  => $activities,
            'grand_total' => round($grandTotal, 2),
            'count'       => count($activities),
            'note'        => 'unit_cost_per_sched_unit matches the cost dashboard. If units_same=false, the estimate and schedule use different units — explain the conversion using est_qty_per_sched_unit (e.g. 1 Ton per Pile).',
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_activity_resources
    // Returns planned resource breakdown per unit of activity from the
    // resource allocation page (pricing_estimate_resources_new).
    // Shows each resource: type, qty per unit, rate, amount per unit.
    // -----------------------------------------------------------------------
    private function toolGetActivityResources($db, $input)
    {
        $projectName  = trim($input['project_name']  ?? '');
        $activityName = trim($input['activity_name'] ?? '');

        $project = $db->createCommand(
            "SELECT Project_Id, Name FROM projects
             WHERE Status = 0 AND LOWER(Name) LIKE :n LIMIT 1",
            [':n' => '%' . strtolower($projectName) . '%']
        )->queryOne();

        if (!$project) return ['error' => 'Project not found.'];
        $pid = (int)$project['Project_Id'];

        // Find the WBS activity
        $actFilter = '';
        $params = [':pid' => $pid, ':pid2' => $pid];
        if ($activityName !== '') {
            $raw   = preg_replace('/[^a-z0-9 ]/i', ' ', $activityName);
            $words = array_values(array_filter(explode(' ', $raw), fn($w) => strlen($w) >= 4));
            foreach ($words as $i => $word) {
                $key = ':aw' . $i;
                $actFilter .= " AND LOWER(wan.activity_Name) LIKE {$key}";
                $params[$key] = '%' . strtolower(rtrim($word, 's')) . '%';
            }
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

        if (!$act) return ['error' => 'Activity not found in the estimate for this project.'];

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
             LEFT JOIN resources r    ON r.Resource_Id       = pern.resource_Id
             LEFT JOIN resourcetype rt ON rt.ResourceType_Id = pern.resourcetype_Id
             WHERE pern.activity_id = :wid AND pern.project_id = :pid AND pern.pricing_status = 0
             ORDER BY pern.rate DESC",
            [':wid' => $wanId, ':pid' => $pid]
        )->queryAll();

        if (!$rows) return ['error' => 'No resources allocated for this activity.'];

        $totalUnitCost = 0.0;
        $resources = [];
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
            'project'          => $project['Name'],
            'activity'         => $act['activity_Name'],
            'activity_unit'    => $act['activity_Unit'],
            'activity_qty'     => $actQty,
            'resources'        => $resources,
            'total_unit_cost'  => round($totalUnitCost, 2),
            'total_activity_cost' => round($totalUnitCost * $actQty, 2),
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_activity_actual_cost
    // Actual cost per activity = GRN cost (materials received against POs
    // allocated to that activity) + MB cost (subcontract work done × rate).
    // Mirrors the exact logic in ProjectsmainController cost dashboard.
    // -----------------------------------------------------------------------
    private function toolGetActivityActualCost($db, $input)
    {
        $projectName  = trim($input['project_name']  ?? '');
        $activityName = trim($input['activity_name'] ?? '');

        $project = $db->createCommand(
            "SELECT Project_Id, Name FROM projects
             WHERE Status = 0 AND LOWER(Name) LIKE :n LIMIT 1",
            [':n' => '%' . strtolower($projectName) . '%']
        )->queryOne();

        if (!$project) return ['error' => 'Project not found.'];

        $pid = (int)$project['Project_Id'];

        // --- Planned cost per WBS activity id ---
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

        if (!$planRows) return ['error' => 'No estimate data found for this project.'];

        // Index by wan_id
        $plan = [];
        foreach ($planRows as $r) {
            $plan[(int)$r['wan_id']] = $r;
        }

        // --- GRN actual cost: materials received against POs allocated to each activity ---
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
        foreach ($grnRows as $r) {
            $grnCost[(int)$r['wan_id']] = (float)$r['grn_cost'];
        }

        // --- MB actual cost: subcontract work billed ---
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

        // Optional activity name filter
        $actWords = [];
        if ($activityName !== '') {
            $raw = preg_replace('/[^a-z0-9 ]/i', ' ', $activityName);
            $actWords = array_values(array_filter(explode(' ', strtolower($raw)), fn($w) => strlen($w) >= 4));
        }

        $activities     = [];
        $grandEstimated = 0.0;
        $grandActual    = 0.0;

        foreach ($plan as $wanId => $r) {
            $name = $r['activity_name'];

            // Apply activity name filter if provided
            if ($actWords) {
                $nameLower = strtolower($name);
                $match = true;
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

        // Sort by actual cost descending
        usort($activities, fn($a, $b) => $b['actual_cost'] <=> $a['actual_cost']);

        if (empty($activities)) {
            return ['error' => 'No matching activities found.'];
        }

        return [
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
    // Net material stock at site = GRN received − issue slips issued.
    // -----------------------------------------------------------------------
    private function toolGetStock($db, $input)
    {
        $projectName  = trim($input['project_name']  ?? '');
        $materialName = trim($input['material_name'] ?? '');

        $project = $db->createCommand(
            "SELECT Project_Id, Name FROM projects
             WHERE Status = 0 AND LOWER(Name) LIKE :n LIMIT 1",
            [':n' => '%' . strtolower($projectName) . '%']
        )->queryOne();

        if (!$project) return ['error' => 'Project not found.'];

        $pid = (int)$project['Project_Id'];

        $matFilter = '';
        $params    = [':pid' => $pid];
        if ($materialName !== '') {
            $matFilter         = "AND LOWER(r.Name) LIKE :mname";
            $params[':mname']  = '%' . strtolower($materialName) . '%';
        }

        // Total received per material from GRN
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
            return ['error' => 'No GRN receipts found for this project' . ($materialName ? " matching \"$materialName\"" : '') . '.'];
        }

        $stock = [];
        foreach ($grnRows as $r) {
            $received = (float)$r['total_received'];
            $stock[]  = [
                'material'       => $r['material'],
                'unit'           => $r['Unit'],
                'total_received' => round($received, 3),
                'last_grn_date'  => $r['last_grn_date'],
            ];
        }

        return [
            'project' => $project['Name'],
            'stock'   => $stock,
            'count'   => count($stock),
            'note'    => 'Stock figures show total received via GRN. Material consumption/issue tracking is not yet active in this project.',
        ];
    }

    // -----------------------------------------------------------------------
    // Tool: get_project_documents
    // -----------------------------------------------------------------------
    private function toolGetProjectDocuments($db, $input)
    {
        $projectName = trim($input['project_name'] ?? '');
        $fileName    = trim($input['file_name']    ?? '');

        $project = $db->createCommand(
            "SELECT Project_Id, Name FROM projects
             WHERE Status = 0 AND LOWER(Name) LIKE :n LIMIT 1",
            [':n' => '%' . strtolower($projectName) . '%']
        )->queryOne();

        if (!$project) return ['error' => 'Project not found.'];

        $pid = (int)$project['Project_Id'];

        $params = [':pid' => $pid];
        $fileFilter = '';
        if ($fileName !== '') {
            $fileFilter = "AND LOWER(pf.original_name) LIKE :fn";
            $params[':fn'] = '%' . strtolower($fileName) . '%';
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
            return ['project' => $project['Name'], 'documents' => [], 'note' => 'No documents uploaded for this project yet.'];
        }

        $uploadDir = Yii::getAlias('@webroot') . '/uploads/projects/';
        $result = [];
        foreach ($files as $f) {
            $entry = [
                'id'           => (int)$f['id'],
                'type'         => $f['file_type'],
                'name'         => $f['original_name'],
                'uploaded_at'  => $f['uploaded_at'],
            ];

            // If a specific file was requested, try to read its text content
            if ($fileName !== '') {
                $path = $uploadDir . $f['filename'];
                $ext  = strtolower(pathinfo($f['original_name'], PATHINFO_EXTENSION));
                if (file_exists($path)) {
                    if (in_array($ext, ['txt', 'csv'])) {
                        $entry['content'] = mb_substr(file_get_contents($path), 0, 8000);
                    } elseif ($ext === 'pdf') {
                        // Extract text from PDF using pdftotext if available, else note it
                        $tmp = tempnam(sys_get_temp_dir(), 'pdf_');
                        $out = shell_exec('pdftotext ' . escapeshellarg($path) . ' ' . escapeshellarg($tmp) . ' 2>/dev/null && cat ' . escapeshellarg($tmp));
                        @unlink($tmp);
                        $entry['content'] = $out ? mb_substr($out, 0, 8000) : '[PDF — text extraction not available on this server. File is uploaded and accessible.]';
                    } else {
                        $entry['content'] = '[Binary file — ' . strtoupper($ext) . ' document. Cannot extract text for chatbot. File is uploaded and accessible by users.]';
                    }
                } else {
                    $entry['content'] = '[File not found on server]';
                }
            }

            $result[] = $entry;
        }

        return [
            'project'   => $project['Name'],
            'documents' => $result,
            'count'     => count($result),
        ];
    }

    // -----------------------------------------------------------------------
    // Main action — tool-calling loop
    // -----------------------------------------------------------------------
    public function actionChat()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $message = trim(Yii::$app->request->post('message', ''));
        $historyRaw = Yii::$app->request->post('history', '[]');
        $history = is_array($historyRaw) ? $historyRaw : (json_decode($historyRaw, true) ?: []);
        if (!$message) return ['error' => 'No message'];

        $apiKey = $this->getApiKey();
        if (!$apiKey) return ['error' => 'API key not configured'];

        $systemPrompt =
            "You are a read-only data assistant for Opiam Analytics ERP, a construction project management system.\n\n" .
            "You have access to tools that query live data from the system. " .
            "ALWAYS use a tool to answer questions — never make up numbers or use prior knowledge.\n\n" .

            "TOOL SELECTION RULES:\n" .
            "1. Always call the most specific tool available for the question.\n" .
            "2. Use get_activity_kpi for any question about a specific activity's production, progress, or duration.\n" .
            "3. Use get_project_estimate when the user asks about the TOTAL cost to build the project (budget, estimated cost, project estimate, profitability, contract margin). This is the full BOQ sum.\n" .
            "4. Use get_cost_dashboard when the user asks about ECWD / ACWD — i.e., the cost of work completed SO FAR.\n" .
            "5. Use get_project_progress for overall project status, physical progress %, or schedule health.\n" .
            "6. Use get_activity_costs when the user asks about planned/estimated cost of a specific activity or wants activities ranked by cost.\n" .
            "7. Use get_activity_actual_cost when the user asks how much has actually been spent on an activity, or wants actual vs estimated comparison per activity.\n" .
            "8. Use get_stock when the user asks about materials at site, inventory, stock of cement/steel/any material received.\n" .
            "9. Use get_activity_resources when the user asks about resources needed for an activity, unit rate breakdown, what materials/labour/equipment are planned, or the rate of a specific resource in an activity.\n\n" .

            "COST UNIT RULE (applies to ALL cost questions):\n" .
            "All activity costs are expressed in SCHEDULE units. The schedule unit (e.g. Pile, Cum, RMT) is the primary unit for every cost answer.\n" .
            "- unit_cost_per_sched_unit = cost to do ONE schedule unit of the activity (e.g. ₹68,000 per Pile)\n" .
            "- total_cost = unit_cost_per_sched_unit × schedule_qty (total estimated cost of that activity)\n" .
            "- NEVER report unit_cost_per_est_unit as the answer — it only exists internally for conversion.\n" .
            "- When schedule_unit and estimate_unit are DIFFERENT (units_same=false), explain the derivation: '₹X per Pile (schedule unit), based on ₹Y per Ton × Z Ton per Pile'. When they are the SAME unit, just state the cost directly.\n\n" .

            "CRITICAL TERMINOLOGY — NEVER CONFUSE THESE:\n" .
            "- 'estimated cost of the project' / 'project budget' / 'total estimate' / 'how much does the project cost' → get_project_estimate → total_estimate_cost\n" .
            "- 'estimated cost of work done' / 'ECWD' / 'earned cost' → get_cost_dashboard → estimated_cost_work_done (only the cost of units completed so far)\n" .
            "- 'actual cost of work done' / 'ACWD' / 'actual spend' → get_cost_dashboard → actual_cost_work_done\n" .
            "- 'contract value' / 'project value' / 'client contract' → get_projects or get_project_estimate → contract_value (what the client pays, not what it costs)\n" .
            "- 'cost of an activity' / 'how much will activity cost' / 'activity budget' → get_activity_costs → total_cost (= unit_cost_per_sched_unit × schedule_qty)\n" .
            "- 'unit cost' / 'cost per unit' / 'cost per Pile' / 'cost per Cum' / 'rate of activity' → get_activity_costs → unit_cost_per_sched_unit. Always express in schedule unit.\n" .
            "- 'resource breakdown' / 'what resources are needed' / 'rate of RMC or labour or subcontractor' → get_activity_resources → resources with qty_per_unit, unit_rate, amount_per_unit\n" .
            "- 'actual cost of an activity' / 'how much spent on an activity' → get_activity_actual_cost → actual_cost per activity\n" .
            "- 'target production' / 'daily production target' → get_activity_kpi → target_production_per_day (rate per day)\n" .
            "- 'target to date' / 'planned quantity to date' / 'how much should have been done' → get_activity_kpi → target_to_date (cumulative = rate × elapsed days)\n" .
            "- 'actual production' → get_activity_kpi → actual_production_per_day\n" .
            "- 'profitability' → get_project_estimate → estimated_profitability (contract_value − total_estimate_cost)\n" .
            "- 'stock' / 'inventory' / 'materials at site' → get_stock → net_stock per material\n" .
            "\n" .

            "ANSWER FORMAT RULES:\n" .
            "1. If the user mentions a project by name or refers to 'the project' from prior context, always pass that name to the tool.\n" .
            "2. Use conversation history to infer activity/project names from follow-up questions. Never ask the user to clarify.\n" .
            "3. Answer in 1–2 sentences. Show quantities with their units. Show money as ₹ with 2 decimal places.\n" .
            "4. If the tool returns an error or null values, say: 'That information is not available in the system.'\n" .
            "5. Never mention internal field names or JSON keys — translate them to plain English.\n" .
            "6. Do not add extra commentary, suggestions, or emojis.\n\n" .
            "TODAY'S DATE: " . date('d F Y') . " (use this as the current date for all elapsed-day and date calculations).";

        // Build message list from history
        $messages = [];
        foreach ((array)$history as $h) {
            if (empty($h['role']) || !isset($h['content'])) continue;
            $content = $h['content'];
            // History from the JS widget is always plain text — ensure it stays a string
            if (!is_string($content)) $content = is_array($content) ? json_encode($content) : (string)$content;
            if ($content === '') continue;
            $messages[] = ['role' => $h['role'], 'content' => $content];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        $tools = $this->toolDefinitions();

        // Tool-calling loop — max 5 rounds to prevent runaway
        for ($round = 0; $round < 5; $round++) {

            $payload = json_encode([
                'model'      => 'claude-sonnet-4-6',
                'max_tokens' => 1024,
                'temperature' => 0.1,
                'system'     => $systemPrompt,
                'tools'      => $tools,
                'messages'   => $messages,
            ]);

            $ch = curl_init('https://api.anthropic.com/v1/messages');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: '          . $apiKey,
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
            if (isset($data['error'])) {
                return ['error' => 'API: ' . ($data['error']['message'] ?? substr($response, 0, 300))];
            }

            $stopReason = $data['stop_reason'] ?? '';
            $content    = $data['content']     ?? [];

            // If Claude is done — return the text reply
            if ($stopReason === 'end_turn') {
                foreach ($content as $block) {
                    if ($block['type'] === 'text') {
                        return ['reply' => $block['text']];
                    }
                }
                return ['reply' => 'No response.'];
            }

            // If Claude wants to call tools
            if ($stopReason === 'tool_use') {
                // Add Claude's response (which contains tool_use blocks) to messages
                $messages[] = ['role' => 'assistant', 'content' => $content];

                // Execute each requested tool and collect results
                $toolResults = [];
                foreach ($content as $block) {
                    if ($block['type'] !== 'tool_use') continue;

                    $toolName   = $block['name'];
                    $toolInput  = (is_array($block['input'] ?? null)) ? $block['input'] : [];
                    $toolUseId  = $block['id'];

                    $result = $this->executeTool($toolName, $toolInput);

                    $toolResults[] = [
                        'type'        => 'tool_result',
                        'tool_use_id' => $toolUseId,
                        'content'     => json_encode($result),
                    ];
                }

                // Add tool results back as user message
                $messages[] = ['role' => 'user', 'content' => $toolResults];

                // Loop — Claude will now process the results and reply
                continue;
            }

            // Unexpected stop reason — return whatever text we have
            foreach ($content as $block) {
                if ($block['type'] === 'text') {
                    return ['reply' => $block['text']];
                }
            }
            return ['reply' => 'Unexpected response from API.'];
        }

        return ['reply' => 'Could not complete request after multiple attempts.'];
    }
}
