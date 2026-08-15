<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\ProjuserSelection;
use app\components\ResourceTypeCategories;

class ReportController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    private function getCurrentProjectId()
    {
        $uid = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        return $projuser ? $projuser->projectid : null;
    }

    private function parseDate($str)
    {
        if (empty($str) || $str === '0000-00-00') return '';
        $parts = explode('-', $str);
        if (count($parts) === 3 && strlen($parts[0]) === 2) {
            return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        return $str;
    }

    private function formatDateDisplay($dbDate)
    {
        if (empty($dbDate) || $dbDate === '0000-00-00') return '';
        return date('d-m-Y', strtotime($dbDate));
    }

    // ─── Mobile progress reporting page ─────────────────────────────────────

    public function actionIndex($layout = 'true')
    {
        if ($layout === 'false') {
            $this->layout = false;
        }
        return $this->render('index');
    }

    public function actionMobile()
    {
        $this->layout = '@app/views/layouts/mobile';
        $projectid = $this->getCurrentProjectId();
        $db = Yii::$app->db;
        $project = $projectid ? $db->createCommand("SELECT Name FROM projects WHERE Project_Id=:pid", [':pid'=>$projectid])->queryOne() : null;
        $projectName = $project ? $project['Name'] : 'No Project Selected';
        $initialTab = (Yii::$app->request->get('tab') === 'mbook') ? 'mbook' : 'progress';
        return $this->render('mobile', ['projectName' => $projectName, 'initialTab' => $initialTab]);
    }

    // ─── Mobile: clean JSON activity list ───────────────────────────────────

    public function actionMobileactivities()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $projectid = $this->getCurrentProjectId();
        if (!$projectid) return ['activities' => []];

        $db = Yii::$app->db;

        $rows = $db->createCommand("
            SELECT
                sa.id,
                wa.activity_Name AS name,
                COALESCE(sa.unit, wa.activity_Unit, '') AS unit,
                sa.quantity AS b_qty,
                sa.completed_status AS completed,
                spr.start_date,
                spr.cumulated_qty AS cum_qty,
                COALESCE(MAX(sprl.report_date), spr.updated_at) AS last_date,
                wn.Name AS iow
            FROM workgroup_activities_new wa
            JOIN scheduleactivities sa ON sa.activity_id = wa.id AND sa.projectId = :pid
            JOIN workgroups_new wn ON wn.Workgroup_Id = wa.wbs_id AND wn.Project_Id = :pid2
            LEFT JOIN schedule_progress_report spr ON spr.activity_id = sa.id
            LEFT JOIN schedule_progress_report_log sprl ON sprl.activity_id = sa.id
            WHERE wa.project_Id = :pid3 AND wn.Status = 0 AND sa.status = 0 AND sa.completed_status = 0
            GROUP BY sa.id, wa.activity_Name, sa.unit, wa.activity_Unit, sa.quantity, sa.completed_status, spr.start_date, spr.cumulated_qty, spr.updated_at, wn.Name
            ORDER BY wn.sortorder ASC, wa.sortorder ASC
        ", [':pid' => $projectid, ':pid2' => $projectid, ':pid3' => $projectid])->queryAll();

        foreach ($rows as &$r) {
            $r['cum_qty']    = $r['cum_qty']    ? round((float)$r['cum_qty'], 3)    : null;
            $r['b_qty']      = $r['b_qty']      ? round((float)$r['b_qty'], 3)      : null;
            $r['completed']  = (int)$r['completed'];
            $r['start_date'] = ($r['start_date'] && $r['start_date'] !== '0000-00-00')
                ? date('Y-m-d', strtotime($r['start_date'])) : '';
            $r['last_date']  = ($r['last_date'] && $r['last_date'] !== '0000-00-00')
                ? date('d-m-Y', strtotime($r['last_date'])) : null;
        }

        return ['activities' => $rows];
    }

    // ─── Main activity listing (IOW-grouped) ────────────────────────────────

    public function actionScheduleprogressactivities()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $projectid = $this->getCurrentProjectId();
        if (!$projectid) {
            return ['error' => 'No', 'proid' => '', 'result' => '<div style="text-align:center;padding:30px;color:#999;">No project selected. Please select a project first.</div>'];
        }

        $db = Yii::$app->db;

        // Get all workgroups for this project
        $workgroups = $db->createCommand(
            "SELECT Workgroup_Id, Name FROM workgroups_new WHERE Project_Id = :pid AND Status = 0 ORDER BY sortorder ASC",
            [':pid' => $projectid]
        )->queryAll();

        if (empty($workgroups)) {
            // Fallback: flat list from scheduleactivities
            return $this->_flatActivityList($projectid, $db);
        }

        $html = '<style>
            .iow-group-header{background:#e0e0e0;color:#333;padding:8px 14px;font-weight:600;font-size:13px;border-radius:3px;margin:12px 0 0;}
            .iow-act-table{width:100%;border-collapse:collapse;font-size:13px;margin-top:8px;margin-bottom:4px;}
            .iow-act-table thead th{background:#555;color:#fff;padding:8px 10px;border:1px solid #444;white-space:nowrap;}
            .iow-act-table tbody td{padding:7px 10px;border:1px solid #ddd;vertical-align:middle;}
            .iow-act-table tbody tr:hover{background:#f9f9f9;}
            .completed-row{background:#f0fff0;}
            .btn-xs{padding:3px 7px;font-size:11px;}
        </style>';

        $anyActivity = false;

        foreach ($workgroups as $wg) {
            $wgId   = $wg['Workgroup_Id'];
            $wgName = htmlspecialchars($wg['Name']);

            $activities = $db->createCommand("
                SELECT wa.id AS wga_id, wa.activity_Name, wa.activity_Unit, sa.unit AS sa_unit,
                       sa.id AS sched_id, sa.quantity, sa.completed_status,
                       spr.start_date AS report_start_date,
                       spr.cumulated_qty,
                       spr.updated_at AS last_updated
                FROM workgroup_activities_new wa
                JOIN scheduleactivities sa ON sa.activity_id = wa.id AND sa.projectId = :pid AND sa.status = 0
                LEFT JOIN schedule_progress_report spr ON spr.activity_id = sa.id
                WHERE wa.wbs_id = :wgid AND wa.project_Id = :pid2
                ORDER BY wa.sortorder ASC
            ", [':pid' => $projectid, ':wgid' => $wgId, ':pid2' => $projectid])->queryAll();

            if (empty($activities)) continue;

            $anyActivity = true;

            $html .= '<div class="iow-group-header">' . $wgName . '</div>';
            $html .= '<table class="iow-act-table">
            <thead><tr>
                <th>#</th>
                <th>Activity</th>
                <th>Unit</th>
                <th>B.Qty</th>
                <th>Reported Qty</th>
                <th>Last Updated</th>
                <th>Status</th>
                <th>Action</th>
            </tr></thead><tbody>';

            foreach ($activities as $k => $act) {
                $schedId     = (int)$act['sched_id'];
                $cumQty      = (float)($act['cumulated_qty'] ?? 0);
                $bQty        = (float)($act['quantity'] ?? 0);
                $lastUpdated = $this->formatDateDisplay($act['last_updated']);
                $completed   = (int)($act['completed_status'] ?? 0);
                $rowClass    = $completed ? 'completed-row' : '';
                $status      = $completed ? '<span style="color:green;font-weight:600;">Completed</span>' : '<span style="color:#888;">Active</span>';

                $html .= '<tr class="' . $rowClass . '">';
                $html .= '<td>' . ($k + 1) . '</td>';
                $html .= '<td>' . htmlspecialchars($act['activity_Name']) . '</td>';
                $html .= '<td>' . htmlspecialchars($act['sa_unit'] ?: $act['activity_Unit']) . '</td>';
                $html .= '<td>' . $bQty . '</td>';
                $html .= '<td>' . $cumQty . '</td>';
                $html .= '<td>' . ($lastUpdated ?: '-') . '</td>';
                $html .= '<td>' . $status . '</td>';
                $html .= '<td style="white-space:nowrap;">';

                if (!$completed) {
                    $html .= '<button type="button" class="btn btn-sm taskreport" '
                           . 'style="background:#1b5e20;border-color:#0d3d10;color:#fff;" '
                           . 'data-id="' . $schedId . '" '
                           . 'data-toggle="modal" data-target="#taskReportPopup" '
                           . 'title="Report Progress">Report Progress</button> ';
                    $html .= '<button type="button" class="btn btn-xs btn-default activityreportcomplete" '
                           . 'data-v="' . $schedId . '" title="Mark Complete">&#10003;</button> ';
                    $html .= '<button type="button" class="btn btn-xs btn-warning reportclear" '
                           . 'data-id="' . $schedId . '" title="Clear">&#10005;</button>';
                } else {
                    $html .= '<button type="button" class="btn btn-xs btn-info activityreportreactivate" '
                           . 'data-v="' . $schedId . '" title="Activate">&#8635; Activate</button>';
                }

                $html .= '</td></tr>';
            }

            $html .= '</tbody></table>';
        }

        if (!$anyActivity) {
            return $this->_flatActivityList($projectid, $db);
        }

        $html .= '<div class="text-right" style="margin-top:10px;"><button type="button" id="savescheduleprogressrpt" class="btn btn-primary" style="display:none;"><span class="icon-check"></span> Save Report</button></div>';
        $html .= '<div id="success-messages" style="display:none;color:green;padding:10px;">Reported Successfully!</div>';

        return ['error' => 'No', 'proid' => (string)$projectid, 'result' => $html];
    }

    private function _flatActivityList($projectid, $db)
    {
        $activities = $db->createCommand("
            SELECT sa.id, sa.name, sa.unit, sa.quantity, sa.completed_status,
                   spr.start_date AS report_start_date, spr.cumulated_qty, spr.updated_at AS last_updated
            FROM scheduleactivities sa
            LEFT JOIN schedule_progress_report spr ON spr.activity_id = sa.id
            WHERE sa.projectId = :pid AND sa.status = 0
            ORDER BY sa.sortorder ASC
        ", [':pid' => $projectid])->queryAll();

        if (empty($activities)) {
            return ['error' => 'No', 'proid' => '', 'result' => '<div style="text-align:center;padding:30px;color:#999;">No activities found for this project.</div>'];
        }

        $html = '<table class="table table-bordered" style="font-size:13px;">
        <thead><tr style="background:#3c3c3c;color:#fff;">
            <th>#</th><th>Activity</th><th>Unit</th><th>B.Qty</th>
            <th>Reported Qty</th><th>Last Updated</th><th>Action</th>
        </tr></thead><tbody>';

        foreach ($activities as $k => $act) {
            $id        = (int)$act['id'];
            $cumQty    = (float)($act['cumulated_qty'] ?? 0);
            $bQty      = (float)($act['quantity'] ?? 0);
            $completed = (int)($act['completed_status'] ?? 0);

            $html .= '<tr>';
            $html .= '<td>' . ($k + 1) . '</td>';
            $html .= '<td>' . htmlspecialchars($act['name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($act['unit']) . '</td>';
            $html .= '<td>' . $bQty . '</td>';
            $html .= '<td>' . $cumQty . '</td>';
            $html .= '<td>' . ($this->formatDateDisplay($act['last_updated']) ?: '-') . '</td>';
            $html .= '<td>';
            if (!$completed) {
                $html .= '<button type="button" class="btn btn-sm taskreport" '
                       . 'style="background:#1b5e20;border-color:#0d3d10;color:#fff;" '
                       . 'data-id="' . $id . '" '
                       . 'data-toggle="modal" data-target="#taskReportPopup" '
                       . 'title="Report Progress">Report Progress</button> ';
                $html .= '<button type="button" class="btn btn-xs btn-default activityreportcomplete" data-v="' . $id . '" title="Mark Complete">&#10003;</button> ';
                $html .= '<button type="button" class="btn btn-xs btn-warning reportclear" data-id="' . $id . '" title="Clear">&#10005;</button>';
            } else {
                $html .= '<button type="button" class="btn btn-xs btn-info activityreportreactivate" data-v="' . $id . '" title="Reactivate">&#8635;</button>';
            }
            $html .= '</td></tr>';
        }

        $html .= '</tbody></table>';
        $html .= '<div id="success-messages" style="display:none;color:green;padding:10px;">Reported Successfully!</div>';

        return ['error' => 'No', 'proid' => (string)$projectid, 'result' => $html];
    }

    // ─── Popup: task/progress report form for one activity ──────────────────

    public function actionScheduleprogresstasklist()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $actid     = (int)Yii::$app->request->post('actid');
        $reportDate = Yii::$app->request->post('date', date('d-m-Y'));
        $projectid = $this->getCurrentProjectId();

        if (!$actid || !$projectid) return ['error' => 'Yes'];

        $db = Yii::$app->db;

        // Get activity info
        $act = $db->createCommand("
            SELECT sa.id, sa.name, sa.unit, sa.quantity,
                   wa.activity_Name, wa.activity_Unit,
                   spr.start_date AS report_start_date, spr.cumulated_qty,
                   rptlog.last_report_date
            FROM scheduleactivities sa
            LEFT JOIN workgroup_activities_new wa ON wa.id = sa.activity_id
            LEFT JOIN schedule_progress_report spr ON spr.activity_id = sa.id
            LEFT JOIN (
                SELECT activity_id, MAX(report_date) AS last_report_date
                FROM schedule_progress_report_log GROUP BY activity_id
            ) rptlog ON rptlog.activity_id = sa.id
            WHERE sa.id = :aid AND sa.projectId = :pid
            LIMIT 1
        ", [':aid' => $actid, ':pid' => $projectid])->queryOne();

        if (!$act) return ['error' => 'Yes'];

        $actName  = htmlspecialchars($act['activity_Name'] ?: $act['name']);
        $unit     = htmlspecialchars($act['unit'] ?: $act['activity_Unit']);
        $bQty     = (float)($act['quantity'] ?? 0);
        $cumQty   = (float)($act['cumulated_qty'] ?? 0);
        $startDate      = $this->formatDateDisplay($act['report_start_date']);
        $lastReportDate = $this->formatDateDisplay($act['last_report_date'] ?? '');
        $todayDisp = date('d-m-Y');

        // Get tasks for this activity — same source as the Tasks screen (schedule_task_new + activity_tasks)
        $tasks = $db->createCommand("
            SELECT stn.id AS task_Id, at.task_name AS Name
            FROM schedule_task_new stn
            JOIN activity_tasks at ON at.id = stn.task_Id
            WHERE stn.activity_Id = :aid AND stn.status = 0
            ORDER BY stn.id ASC
        ", [':aid' => $actid])->queryAll();

        $html = '<form id="schedule-task-reporting' . $actid . '">';
        $html .= '<input type="hidden" name="actid" value="' . $actid . '">';
        $html .= '<input type="hidden" id="activityid" name="activity_id" value="' . $actid . '">';
        $html .= '<input type="hidden" id="sel_date" value="' . htmlspecialchars($reportDate) . '">';
        $html .= '<input type="hidden" id="report_holiday_arr" value="">';
        $html .= '<input type="hidden" id="report_holiday_week_arr" value="">';

        // Row 1: Activity header — light grey background
        $html .= '<div style="background:#d8d8d8;padding:7px 14px;border-radius:4px 4px 0 0;display:flex;align-items:center;justify-content:space-between;">';
        $html .= '<strong style="font-size:13px;">' . $actName . '</strong>';
        $html .= '<div style="display:flex;align-items:center;gap:5px;font-size:12px;">'
            . '<span style="color:#000;font-weight:600;">Working Hours/Day:</span>'
            . '<select id="workhours" name="workhours" style="border:1px solid #ccc;border-radius:3px;padding:1px 4px;font-size:12px;color:#000;background:#fff;">'
            . '<option value="8">8</option>'
            . '<option value="10">10</option>'
            . '<option value="12">12</option>'
            . '<option value="24">24</option>'
            . '</select></div>';
        $html .= '</div>';

        // 4px spacer
        $html .= '<div style="height:4px;"></div>';

        // Row 2: Data row — dark navy background (sleek/compact)
        $inputStyle = 'background:#fff;color:#001033;border:none;padding:3px 6px;height:26px;font-size:12px;border-radius:3px;';
        $labelStyle = 'font-size:12px;color:#ffffff !important;font-weight:700;display:block;margin-bottom:4px;line-height:1.3;letter-spacing:0.2px;';
        $dispStyle  = 'font-size:13px;font-weight:600;padding:3px 6px;height:26px;color:#001033;background:#f0f4f8;border-radius:3px;display:flex;align-items:center;';

        $html .= '<div style="background:#001033;color:#fff;padding:8px 14px;border-radius:0 0 4px 4px;">';

        // Line 1: Activity Start Date | Last Reported Date | Last Reported Qty
        $html .= '<div style="display:flex;gap:8px;align-items:flex-start;margin-bottom:8px;">';

        $html .= '<div style="flex:0 0 145px;">';
        $html .= '<label style="' . $labelStyle . '">Activity Start Date <span style="color:#ff8888;">*</span></label>';
        $html .= '<input type="text" name="start_date" id="start_date_' . $actid . '"'
            . ' class="form-control edit_start_date datepicker" data-id="' . $actid . '"'
            . ' value="' . $startDate . '" placeholder="dd-mm-yyyy" autocomplete="off"'
            . ' style="' . $inputStyle . 'width:100%;">';
        $html .= '</div>';

        $html .= '<div style="flex:0 0 140px;">';
        $html .= '<label style="' . $labelStyle . '">Last Reported Date</label>';
        $html .= '<div style="' . $dispStyle . 'width:100%;">' . ($lastReportDate ?: '-') . '</div>';
        $html .= '</div>';

        $html .= '<div style="flex:0 0 130px;">';
        $html .= '<label style="' . $labelStyle . '">Last Reported Qty</label>';
        $html .= '<div style="' . $dispStyle . 'width:100%;" id="reportqty' . $actid . '">' . $cumQty . '</div>';
        $html .= '<input type="hidden" name="prev_cumqty" value="' . $cumQty . '">';
        $html .= '</div>';

        // Pushed to the extreme right of the row via margin-left:auto.
        $html .= '<div style="margin-left:auto;flex-shrink:0;">';
        $html .= '<label style="' . $labelStyle . 'text-align:right;">Report Resource</label>';
        $html .= '<button type="button" class="btn btn-xs btn-report-resource-usage" data-id="' . $actid . '"'
            . ' title="Report Resources at Site" style="flex-shrink:0;background:#f0f4f8;color:#001033;border:none;'
            . 'border-radius:3px;height:26px;width:26px;font-size:13px;float:right;">&#128101;</button>';
        $html .= '</div>';

        $html .= '</div>'; // Line 1

        // Line 2: Report Date | Break Days | Unit | Current Quantity | Up-To-Date Qty
        $html .= '<div style="display:flex;gap:8px;align-items:flex-start;">';

        $html .= '<div style="flex:0 0 110px;">';
        $html .= '<label style="' . $labelStyle . '">Report Date</label>';
        $html .= '<input type="text" name="reportdate" class="form-control datepicker" value="' . $todayDisp . '" autocomplete="off"'
            . ' style="' . $inputStyle . 'width:100%;">';
        $html .= '</div>';

        $html .= '<div style="flex:0 0 85px;">';
        $html .= '<label style="' . $labelStyle . '">Break Days</label>';
        $html .= '<input type="number" name="break_days" id="break_days_' . $actid . '"'
            . ' class="form-control" step="0.5" min="0" placeholder="0"'
            . ' style="' . $inputStyle . 'width:100%;">';
        $html .= '</div>';

        $html .= '<div style="flex:0 0 60px;">';
        $html .= '<label style="' . $labelStyle . '">Unit</label>';
        $html .= '<div title="' . $unit . '" style="background:#fff;border-radius:3px;height:26px;display:flex;align-items:center;padding:0 6px;overflow:hidden;">'
            . '<span style="font-size:12px;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' . $unit . '</span>'
            . '</div>';
        $html .= '</div>';

        $html .= '<div style="flex:1;min-width:80px;">';
        $html .= '<label style="' . $labelStyle . '">Current Quantity <span style="color:#ff8888;">*</span></label>';
        $html .= '<input type="number" name="currentqnty" id="currentqnty' . $actid . '"'
            . ' class="form-control currentqnty" data-id="' . $actid . '"'
            . ' value="" step="any" min="0" placeholder="0"'
            . ' style="' . $inputStyle . 'width:100%;">';
        $html .= '</div>';

        $html .= '<div style="flex:1;min-width:80px;">';
        $html .= '<label style="' . $labelStyle . '">Up-To-Date Qty</label>';
        $html .= '<div style="' . $dispStyle . 'width:100%;" id="cumqty' . $actid . '">' . $cumQty . '</div>';
        $html .= '</div>';

        $html .= '</div>'; // Line 2
        $html .= '</div>'; // navy div

        // Live up-to-date qty update
        $html .= '<script>
        (function(){
            var aid = ' . $actid . ';
            $(document).on("input","#currentqnty" + aid, function(){
                var cur = parseFloat($(this).val()) || 0;
                var prev = parseFloat($("#reportqty" + aid).text()) || 0;
                $("#cumqty" + aid).text((prev + cur).toFixed(2));
            });
        })();
        </script>';

        // Tasks section (collapsible)
        $html .= '<div style="margin-top:16px;">';
        $html .= '<div class="tasks-section-toggle" data-target="tasks-body-' . $actid . '"'
            . ' style="background:#e8e8e8;padding:8px 14px;border-radius:3px;cursor:pointer;'
            . 'display:flex;justify-content:center;align-items:center;gap:8px;user-select:none;">';
        $html .= '<span style="font-weight:600;font-size:13px;">Tasks</span>';
        $html .= '<span class="toggle-arrow" style="font-size:11px;">&#9654;</span>';
        $html .= '</div>';

        $html .= '<div id="tasks-body-' . $actid . '" style="display:none;margin-top:8px;">';

        if (!empty($tasks)) {
            $firstTask = $tasks[0]['task_Id'];
            $lastTask  = end($tasks)['task_Id'];
            $html .= '<input type="hidden" id="first_task" value="' . $firstTask . '">';
            $html .= '<input type="hidden" id="last_task" value="' . $lastTask . '">';
            $html .= '<style>
                #tasks-body-' . $actid . ' table{border-collapse:collapse;border:none;}
                #tasks-body-' . $actid . ' td,#tasks-body-' . $actid . ' th{border-top:none;border-left:none;border-right:1px solid #e8e8e8;border-bottom:1px solid #e8e8e8;}
                #tasks-body-' . $actid . ' td:last-child,#tasks-body-' . $actid . ' th:last-child{border-right:none;}
            </style>';

            $html .= '<table class="table" style="font-size:12px;border:none;">
            <thead><tr style="background:#555;color:#fff;">
                <th style="width:4%;">#</th>
                <th style="width:20%;">Task</th>
                <th style="width:13%;">Start Date</th>
                <th style="width:11%;">Start Time</th>
                <th style="width:10%;">Break Hrs</th>
                <th style="width:13%;">End Date</th>
                <th style="width:11%;">End Time</th>
                <th style="width:11%;">Duration/Hrs</th>
            </tr></thead><tbody>';

            foreach ($tasks as $tk => $task) {
                $tid = (int)$task['task_Id'];
                $html .= '<tr>';
                $html .= '<td>' . ($tk + 1) . '</td>';
                $html .= '<td>' . htmlspecialchars($task['Name']) . '</td>';
                $html .= '<td><input type="text" id="task_date' . $tid . '" name="task_date[' . $tid . ']" class="form-control task_dates holidayAppliedDatepicker" data-id="' . $tid . '" data-taskkey="' . ($tk + 1) . '" value="" autocomplete="off" style="width:100px;"></td>';
                $html .= '<td><input type="text" id="taskstart_time' . $tid . '" name="taskstart_time[' . $tid . ']" class="form-control taskstart_time" data-id="' . $tid . '" data-taskkey="' . ($tk + 1) . '" value="" placeholder="HH:MM" style="width:75px;"></td>';
                $html .= '<td><input type="number" id="break_hour' . $tid . '" name="break_hour[' . $tid . ']" class="form-control break_hour" data-id="' . $tid . '" value="" step="0.5" min="0" placeholder="0" style="width:65px;"></td>';
                $html .= '<td><input type="text" id="task_enddate' . $tid . '" name="task_enddate[' . $tid . ']" class="form-control task_enddates holidayAppliedDatepicker" data-id="' . $tid . '" data-taskkey="' . ($tk + 1) . '" value="" autocomplete="off" style="width:100px;"></td>';
                $html .= '<td><input type="text" id="taskend_time' . $tid . '" name="taskend_time[' . $tid . ']" class="form-control taskend_time" data-id="' . $tid . '" data-taskkey="' . ($tk + 1) . '" value="" placeholder="HH:MM" style="width:75px;"></td>';
                $html .= '<td><input type="text" id="taskactdur' . $tid . '" name="taskactdur[' . $tid . ']" class="form-control taskactdur" data-id="' . $tid . '" value="" readonly style="width:75px;background:#f5f5f5;"></td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
            $html .= '<div style="text-align:right;font-size:12px;padding-top:4px;">';
            $html .= 'Actual Cycle Time: <strong><span id="totvdurr">0</span> Hrs</strong>';
            $html .= ' &nbsp;|&nbsp; <strong><span id="act_cycle_days_' . $actid . '">0</span> Days</strong>';
            $html .= ' <span style="color:#999;font-size:11px;">(&#247; WH)</span>';
            $html .= '</div>';
        } else {
            $html .= '<div style="padding:10px;color:#999;font-size:12px;">No tasks defined for this activity.</div>';
        }

        $html .= '</div>'; // tasks-body
        $html .= '</div>'; // tasks section wrapper

        $html .= '</form>';

        // Resource usage popup — one per activity render, opened by the
        // small orange button next to Last Reported Qty above. Lists only
        // allocated Direct Labour / Sub Contractor / Plant & Equipment
        // resources (see actionResourceusagelist), each with a plain
        // Nos/Qty input; Save posts to actionSaveresourceusage.
        $html .= '<div class="resusage-bk" id="resusage-bk' . $actid . '" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200600;"></div>';
        $html .= '<div class="resusage-popup" id="resusage-popup' . $actid . '" style="display:none;position:fixed;top:50%;left:50%;'
            . 'transform:translate(-50%,-50%);width:520px;max-height:80vh;background:#fff;border-radius:6px;'
            . 'box-shadow:0 8px 32px rgba(0,0,0,.4);z-index:200601;overflow:hidden;flex-direction:column;">';
        $html .= '<div style="background:#001033;color:#fff;padding:10px 16px;display:flex;justify-content:space-between;align-items:center;">'
            . '<strong style="font-size:13px;">Report Resources at Site</strong>'
            . '<button type="button" class="resusage-close" data-id="' . $actid . '" style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;line-height:1;">&times;</button>'
            . '</div>';
        $html .= '<div style="padding:14px 16px;overflow-y:auto;flex:1;">';
        $html .= '<table class="table" style="font-size:12px;margin-bottom:0;border-collapse:collapse;">'
            . '<thead><tr style="background:#555;color:#fff;"><th style="width:110px;border:1px solid #ccc;padding:6px 8px;">Resource Type</th><th style="border:1px solid #ccc;padding:6px 8px;">Resource Name</th><th style="width:90px;text-align:right;border:1px solid #ccc;padding:6px 8px;">Nos/Qty</th></tr></thead>'
            . '<tbody id="resusage-body' . $actid . '"><tr><td colspan="3" style="color:#999;padding:10px;border:1px solid #ccc;">Loading&hellip;</td></tr></tbody>'
            . '</table>';
        $html .= '</div>';
        $html .= '<div style="padding:10px 16px;border-top:1px solid #eee;text-align:right;">'
            . '<button type="button" class="btn btn-default resusage-close" data-id="' . $actid . '" style="margin-right:6px;">Cancel</button>'
            . '<button type="button" class="btn btn-primary resusage-save" data-id="' . $actid . '">Save</button>'
            . '</div>';
        $html .= '</div>';

        $html .= '<script>
        (function(){
            var aid = ' . $actid . ';
            function reportDate(){ return $("#sel_date").val() || ""; }

            $(document).off("click.resusageopen' . $actid . '").on("click.resusageopen' . $actid . '", ".btn-report-resource-usage[data-id=" + aid + "]", function(){
                $("#resusage-bk" + aid + ", #resusage-popup" + aid).css("display","block");
                $("#resusage-popup" + aid).css("display","flex");
                $("#resusage-body" + aid).html("<tr><td colspan=\"3\" style=\"color:#999;padding:10px;border:1px solid #ccc;\">Loading&hellip;</td></tr>");
                $.ajax({
                    type: "POST", url: "../report/resourceusagelist", dataType: "json",
                    data: { actid: aid, date: reportDate() },
                    success: function(resp){
                        if(!resp || resp.error !== "No" || !resp.resources || !resp.resources.length){
                            $("#resusage-body" + aid).html("<tr><td colspan=\"3\" style=\"color:#999;padding:10px;border:1px solid #ccc;\">No Direct Labour / Sub Contractor / Plant &amp; Equipment resources allocated to this activity.</td></tr>");
                            return;
                        }
                        var rows = "";
                        resp.resources.forEach(function(r){
                            rows += "<tr>"
                                + "<td data-resource-id=\"" + r.resource_id + "\" data-resource-type-id=\"" + r.resource_type_id + "\" data-name=\"" + $("<div>").text(r.name).html() + "\" style=\"color:#666;border:1px solid #ccc;padding:6px 8px;\">" + $("<div>").text(r.type_name || "").html() + "</td>"
                                + "<td style=\"border:1px solid #ccc;padding:6px 8px;\">" + $("<div>").text(r.name).html() + (r.unit ? " <span style=\"color:#999;\">(" + $("<div>").text(r.unit).html() + ")</span>" : "") + "</td>"
                                + "<td style=\"text-align:right;border:1px solid #ccc;padding:6px 8px;\"><input type=\"number\" class=\"form-control resusage-qty\" min=\"0\" step=\"any\" style=\"width:80px;text-align:right;display:inline-block;\" value=\"" + (r.reported_qty > 0 ? r.reported_qty : "") + "\" placeholder=\"0\"></td>"
                                + "</tr>";
                        });
                        $("#resusage-body" + aid).html(rows);
                    },
                    error: function(){
                        $("#resusage-body" + aid).html("<tr><td colspan=\"3\" style=\"color:#c00;padding:10px;border:1px solid #ccc;\">Could not load resources. Please try again.</td></tr>");
                    }
                });
            });

            $(document).off("click.resusageclose" + aid).on("click.resusageclose" + aid, ".resusage-close[data-id=" + aid + "]", function(){
                $("#resusage-bk" + aid + ", #resusage-popup" + aid).hide();
            });

            $(document).off("click.resusagesave" + aid).on("click.resusagesave" + aid, ".resusage-save[data-id=" + aid + "]", function(){
                var $btn = $(this);
                var rows = [];
                $("#resusage-body" + aid + " tr").each(function(){
                    var $cell = $(this).find("td").first();
                    var resourceId = $cell.data("resource-id");
                    if(!resourceId) return;
                    rows.push({
                        resource_id: resourceId,
                        resource_type_id: $cell.data("resource-type-id"),
                        name: $cell.data("name"),
                        qty: $(this).find(".resusage-qty").val() || 0
                    });
                });
                $btn.prop("disabled", true).text("Saving…");
                $.ajax({
                    type: "POST", url: "../report/saveresourceusage", dataType: "json",
                    data: { actid: aid, date: reportDate(), rows: rows },
                    success: function(resp){
                        $btn.prop("disabled", false).text("Save");
                        if(!resp || resp.error !== "No"){
                            alert("Could not save resource usage. Please try again.");
                            return;
                        }
                        $("#resusage-bk" + aid + ", #resusage-popup" + aid).hide();
                    },
                    error: function(){
                        $btn.prop("disabled", false).text("Save");
                        alert("Network error — could not save resource usage.");
                    }
                });
            });
        })();
        </script>';

        return [
            'error'                  => 'No',
            'result'                 => $html,
            'holiday_arr'            => [],
            'holiday_week_arr'       => [],
        ];
    }

    // ─── Save single-activity progress from popup ────────────────────────────

    public function actionSimplereportprogress()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $projectid = $this->getCurrentProjectId();
        if (!$projectid) return ['error' => 'Yes'];

        $actid      = (int)Yii::$app->request->post('actid');
        $currentQty = (float)Yii::$app->request->post('currentqnty', 0);
        $startDate  = Yii::$app->request->post('start_date', '');
        $reportDate = Yii::$app->request->post('reportdate', date('d-m-Y'));
        $breakHours = (float)Yii::$app->request->post('break_hours', 0);
        $uid        = Yii::$app->user->id;
        $db         = Yii::$app->db;

        if (!$actid) return ['error' => 'Yes'];

        $reportDateDb = $this->parseDate($reportDate) ?: date('Y-m-d');
        $startDateDb  = $this->parseDate($startDate);
        if (empty($startDateDb)) $startDateDb = $reportDateDb;

        $existing = $db->createCommand(
            "SELECT id, cumulated_qty FROM schedule_progress_report WHERE activity_id = :aid LIMIT 1",
            [':aid' => $actid]
        )->queryOne();

        $newCumQty = ($existing ? (float)$existing['cumulated_qty'] : 0) + $currentQty;

        if ($existing) {
            $updateSql = "UPDATE schedule_progress_report SET cumulated_qty = :cq, updated_at = :ud";
            $params = [':cq' => $newCumQty, ':ud' => $reportDateDb, ':aid' => $actid];
            if (!empty($startDateDb)) {
                $updateSql .= ", start_date = IF(start_date IS NULL OR start_date = '0000-00-00', :sd, start_date)";
                $params[':sd'] = $startDateDb;
            }
            $updateSql .= " WHERE activity_id = :aid";
            $db->createCommand($updateSql, $params)->execute();
        } else {
            $db->createCommand(
                "INSERT INTO schedule_progress_report (activity_id, wrk_grp_act_id, start_date, cumulated_qty, updated_at) VALUES (:aid, 0, :sd, :cq, :ud)",
                [':aid' => $actid, ':sd' => $startDateDb, ':cq' => $currentQty, ':ud' => $reportDateDb]
            )->execute();
        }

        if ($currentQty > 0) {
            $db->createCommand(
                "INSERT INTO schedule_progress_report_log (activity_id, wrk_grp_act_id, current_cycle, report_date, reported_by, currentqty, activity_duration, last_activity, holiday_cnt, break_hour, updated_at)
                 VALUES (:aid, 0, 1, :rd, :uid, :qty, 0, 0, 0, :bh, NOW())",
                [':aid' => $actid, ':rd' => $reportDateDb, ':uid' => $uid, ':qty' => $currentQty, ':bh' => $breakHours]
            )->execute();
        }

        return ['error' => 'No'];
    }

    // ─── Mark activity complete ──────────────────────────────────────────────

    public function actionCompleteactivtiyreport()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $projectid = $this->getCurrentProjectId();
        if (!$projectid) return ['error' => 'Yes'];

        $id = (int)Yii::$app->request->post('id');
        if (!$id) return ['error' => 'Yes'];

        Yii::$app->db->createCommand(
            "UPDATE scheduleactivities SET completed_status = 1 WHERE id = :id AND projectId = :pid",
            [':id' => $id, ':pid' => $projectid]
        )->execute();

        return ['error' => 'No'];
    }

    // ─── Reactivate activity ─────────────────────────────────────────────────

    public function actionReactivateactivtiyreport()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $projectid = $this->getCurrentProjectId();
        if (!$projectid) return ['error' => 'Yes'];

        $id = (int)Yii::$app->request->post('id');
        if (!$id) return ['error' => 'Yes'];

        Yii::$app->db->createCommand(
            "UPDATE scheduleactivities SET completed_status = 0 WHERE id = :id AND projectId = :pid",
            [':id' => $id, ':pid' => $projectid]
        )->execute();

        return ['error' => 'No'];
    }

    // ─── Clear activity progress ─────────────────────────────────────────────

    public function actionSchedulereportclearactivity()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $projectid = $this->getCurrentProjectId();
        if (!$projectid) return ['error' => 'Yes'];

        $actid = (int)Yii::$app->request->post('actvtyid');
        if (!$actid) return ['error' => 'Yes'];

        $db = Yii::$app->db;
        $db->createCommand("DELETE FROM schedule_progress_report WHERE activity_id = :aid", [':aid' => $actid])->execute();
        $db->createCommand("DELETE FROM schedule_progress_report_log WHERE activity_id = :aid", [':aid' => $actid])->execute();

        return ['error' => 'No'];
    }

    // ─── History ─────────────────────────────────────────────────────────────

    public function actionActreportcompletedhistory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $projectid = $this->getCurrentProjectId();
        if (!$projectid) return ['error' => 'Yes'];

        $rows = Yii::$app->db->createCommand("
            SELECT sa.name, sprl.report_date, sprl.currentqty, sa.unit
            FROM schedule_progress_report_log sprl
            JOIN scheduleactivities sa ON sa.id = sprl.activity_id
            WHERE sa.projectId = :pid AND sprl.currentqty > 0
            ORDER BY sprl.report_date DESC, sa.sortorder ASC
            LIMIT 100
        ", [':pid' => $projectid])->queryAll();

        if (empty($rows)) {
            return ['error' => 'No', 'result' => '<div style="text-align:center;padding:30px;color:#999;">No history records found.</div>'];
        }

        $html = '<table class="table table-bordered" style="font-size:13px;">
        <thead><tr style="background:#3c3c3c;color:#fff;">
            <th>Activity</th><th>Report Date</th><th>Qty Reported</th><th>Unit</th>
        </tr></thead><tbody>';

        foreach ($rows as $r) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($r['name']) . '</td>';
            $html .= '<td>' . ($r['report_date'] ? date('d-m-Y', strtotime($r['report_date'])) : '-') . '</td>';
            $html .= '<td>' . $r['currentqty'] . '</td>';
            $html .= '<td>' . htmlspecialchars($r['unit']) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return ['error' => 'No', 'result' => $html];
    }

    // ─── Save bulk progress (main form, kept for compatibility) ─────────────

    public function actionScheduleprogressreport()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $projectid = $this->getCurrentProjectId();
        if (!$projectid) return ['error' => 'Yes'];

        $startDates  = Yii::$app->request->post('start_date', []);
        $currentQtys = Yii::$app->request->post('currentqnty', []);
        $reportDate  = date('Y-m-d');
        $uid         = Yii::$app->user->id;
        $db          = Yii::$app->db;

        foreach ($currentQtys as $actid => $qty) {
            $actid = (int)$actid;
            $qty   = (float)$qty;
            $sDate = !empty($startDates[$actid]) ? $startDates[$actid] : '';

            if ($qty <= 0 && empty($sDate)) continue;

            $startDateDb = $this->parseDate($sDate);
            if (empty($startDateDb)) $startDateDb = $reportDate;

            $existing = $db->createCommand(
                "SELECT id, cumulated_qty FROM schedule_progress_report WHERE activity_id = :aid LIMIT 1",
                [':aid' => $actid]
            )->queryOne();

            $newCumQty = ($existing ? (float)$existing['cumulated_qty'] : 0) + $qty;

            if ($existing) {
                $params = [':cq' => $newCumQty, ':ud' => $reportDate, ':aid' => $actid];
                $sql = "UPDATE schedule_progress_report SET cumulated_qty = :cq, updated_at = :ud";
                if (!empty($startDateDb)) {
                    $sql .= ", start_date = IF(start_date IS NULL OR start_date = '0000-00-00', :sd, start_date)";
                    $params[':sd'] = $startDateDb;
                }
                $sql .= " WHERE activity_id = :aid";
                $db->createCommand($sql, $params)->execute();
            } else {
                $db->createCommand(
                    "INSERT INTO schedule_progress_report (activity_id, wrk_grp_act_id, start_date, cumulated_qty, updated_at) VALUES (:aid, 0, :sd, :cq, :ud)",
                    [':aid' => $actid, ':sd' => $startDateDb, ':cq' => $qty, ':ud' => $reportDate]
                )->execute();
            }

            if ($qty > 0) {
                $db->createCommand(
                    "INSERT INTO schedule_progress_report_log (activity_id, wrk_grp_act_id, current_cycle, report_date, reported_by, currentqty, activity_duration, last_activity, holiday_cnt, updated_at)
                     VALUES (:aid, 0, 1, :rd, :uid, :qty, 0, 0, 0, NOW())",
                    [':aid' => $actid, ':rd' => $reportDate, ':uid' => $uid, ':qty' => $qty]
                )->execute();
            }
        }

        return ['error' => 'No'];
    }

    // ─── Stubs for task-level time reporting ─────────────────────────────────

    public function actionScheduletaskreporting()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['error' => 'No'];
    }

    public function actionReportdatechange()
    {
        return $this->actionScheduleprogressactivities();
    }

    public function actionActivityreportlisting()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['error' => 'Yes'];
    }

    // Return sum of all log entries for a specific activity + date (for pre-filling edit form)
    public function actionGetreportbydate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $actid      = (int)Yii::$app->request->post('actid');
        $reportDate = Yii::$app->request->post('report_date', '');
        if (!$actid || !$reportDate) return ['found' => false];

        $db  = Yii::$app->db;

        // Sum all entries for this date — multiple submissions on same day treated as one
        $row = $db->createCommand(
            "SELECT SUM(currentqty) AS totalqty, SUM(break_hour) AS total_break, COUNT(*) AS cnt
             FROM schedule_progress_report_log
             WHERE activity_id = :aid AND report_date = :rd",
            [':aid' => $actid, ':rd' => $reportDate]
        )->queryOne();

        if (!$row || (int)$row['cnt'] === 0) return ['found' => false];

        $spr = $db->createCommand(
            "SELECT start_date FROM schedule_progress_report WHERE activity_id = :aid LIMIT 1",
            [':aid' => $actid]
        )->queryOne();

        return [
            'found'      => true,
            'currentqty' => round((float)$row['totalqty'], 3),
            'break_hour' => round((float)$row['total_break'], 4),
            'start_date' => ($spr && $spr['start_date'] && $spr['start_date'] !== '0000-00-00')
                            ? date('Y-m-d', strtotime($spr['start_date'])) : '',
        ];
    }

    // Replace all log entries for a date with one consolidated row, recalculate totals
    public function actionProgressreportedit()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $projectid = $this->getCurrentProjectId();
        if (!$projectid) return ['error' => 'Yes'];

        $actid      = (int)Yii::$app->request->post('actid');
        $currentQty = (float)Yii::$app->request->post('currentqnty', 0);
        $breakHours = (float)Yii::$app->request->post('break_hours', 0);
        $startDate  = Yii::$app->request->post('start_date', '');
        $reportDate = Yii::$app->request->post('reportdate', '');
        $uid        = Yii::$app->user->id;
        $db         = Yii::$app->db;

        if (!$actid || !$reportDate) return ['error' => 'Yes'];

        $reportDateDb = $this->parseDate($reportDate) ?: date('Y-m-d');

        // Delete all existing entries for this activity + date, then insert one clean row
        $db->createCommand(
            "DELETE FROM schedule_progress_report_log WHERE activity_id = :aid AND report_date = :rd",
            [':aid' => $actid, ':rd' => $reportDateDb]
        )->execute();

        $db->createCommand(
            "INSERT INTO schedule_progress_report_log
             (activity_id, wrk_grp_act_id, current_cycle, report_date, reported_by, currentqty, activity_duration, last_activity, holiday_cnt, break_hour, updated_at)
             VALUES (:aid, 0, 1, :rd, :uid, :qty, 0, 0, 0, :bh, NOW())",
            [':aid' => $actid, ':rd' => $reportDateDb, ':uid' => $uid, ':qty' => $currentQty, ':bh' => $breakHours]
        )->execute();

        // Recalculate cumulated_qty from all log entries for this activity
        $totals = $db->createCommand(
            "SELECT SUM(currentqty) AS totalqty FROM schedule_progress_report_log WHERE activity_id = :aid",
            [':aid' => $actid]
        )->queryOne();

        $newCumQty   = (float)($totals['totalqty'] ?? 0);
        $startDateDb = $this->parseDate($startDate);

        $existing = $db->createCommand(
            "SELECT id FROM schedule_progress_report WHERE activity_id = :aid LIMIT 1",
            [':aid' => $actid]
        )->queryOne();

        if ($existing) {
            $updateSql = "UPDATE schedule_progress_report SET cumulated_qty = :cq, updated_at = :ud";
            $params    = [':cq' => $newCumQty, ':ud' => $reportDateDb, ':aid' => $actid];
            if (!empty($startDateDb)) {
                $updateSql .= ", start_date = :sd";
                $params[':sd'] = $startDateDb;
            }
            $updateSql .= " WHERE activity_id = :aid";
            $db->createCommand($updateSql, $params)->execute();
        }

        return ['error' => 'No', 'new_cumqty' => round($newCumQty, 3)];
    }

    public function actionReporttasknewlog()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['error' => 'Yes'];
    }

    public function actionScheduleresourcelist()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['error' => 'Yes'];
    }

    public function actionScheduleresourcereporting()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['error' => 'Yes'];
    }

    public function actionGettasktime()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['error' => 'Yes'];
    }

    public function actionSchedulereportcleartask()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['error' => 'Yes'];
    }

    /* Resource-usage-at-site popup (Progress Report screen, next to Last
       Reported Qty). Lists only allocated Direct Labour / Sub Contractor /
       Plant & Equipment resources for this activity — pure display of the
       plan (pricing_estimate_resources_new, keyed by wan_id per the WBS
       form's own convention), plus whatever quantity has already been
       reported today, if any. Resource-type ids resolved via
       ResourceTypeCategories (previously a stale hardcoded IN (1,3,4) —
       same bug class as the Work Order tab's hardcoded id 4, and equally
       wrong on this database, where resourcetype ids start at 10). Labour/
       Sub Contractor comes from the "wo" category; Owned/Leased equipment
       needs both "po" (Owned equipments lives there per the routing spec)
       and "lo" (Leased equipments). */
    public function actionResourceusagelist()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $actid = (int)Yii::$app->request->post('actid');
        $date  = $this->parseDate(Yii::$app->request->post('date', '')) ?: date('Y-m-d');
        if (!$actid) return ['error' => 'Yes'];

        $db = Yii::$app->db;

        $usageTypeIds = array_unique(array_merge(
            ResourceTypeCategories::ids('wo'),
            ResourceTypeCategories::ids('lo'),
            array_intersect(ResourceTypeCategories::ids('po'), $db->createCommand(
                "SELECT ResourceType_Id FROM resourcetype WHERE Name LIKE '%Owned%equipment%'"
            )->queryColumn())
        ));
        if (!$usageTypeIds) $usageTypeIds = [0]; // no matching types today — return empty rather than erroring

        $sa = $db->createCommand(
            "SELECT activity_id AS wan_id, projectId AS project_id FROM scheduleactivities WHERE id = :id LIMIT 1",
            [':id' => $actid]
        )->queryOne();
        if (!$sa) return ['error' => 'Yes'];

        $rows = $db->createCommand("
            SELECT pern.resource_Id AS resource_id, pern.resourcetype_Id AS resource_type_id,
                   r.Name AS name, r.Unit AS unit, rt.Name AS type_name,
                   COALESCE(log.quantity, 0) AS reported_qty
            FROM pricing_estimate_resources_new pern
            JOIN resources r ON r.Resource_Id = pern.resource_Id
            LEFT JOIN resourcetype rt ON rt.ResourceType_Id = pern.resourcetype_Id
            LEFT JOIN schedule_resource_report_log log
                   ON log.activity_id = :aid AND log.resource_id = pern.resource_Id AND log.date = :d
            WHERE pern.activity_id = :wid AND pern.project_id = :pid AND pern.pricing_status = 0
              AND pern.resourcetype_Id IN (" . implode(',', array_map('intval', $usageTypeIds)) . ")
            ORDER BY r.Name ASC
        ", [':aid' => $actid, ':d' => $date, ':wid' => $sa['wan_id'], ':pid' => $sa['project_id']])->queryAll();

        return ['error' => 'No', 'resources' => $rows];
    }

    public function actionSaveresourceusage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $actid = (int)Yii::$app->request->post('actid');
        $date  = $this->parseDate(Yii::$app->request->post('date', '')) ?: date('Y-m-d');
        $rows  = Yii::$app->request->post('rows', []);
        if (!$actid || !is_array($rows)) return ['error' => 'Yes'];

        $db = Yii::$app->db;

        $sa = $db->createCommand(
            "SELECT projectId AS project_id FROM scheduleactivities WHERE id = :id LIMIT 1",
            [':id' => $actid]
        )->queryOne();
        if (!$sa) return ['error' => 'Yes'];
        $projectId = (int)$sa['project_id'];

        foreach ($rows as $row) {
            $resourceId = (int)($row['resource_id'] ?? 0);
            $resourceTypeId = (int)($row['resource_type_id'] ?? 0);
            $name = trim($row['name'] ?? '');
            $qty  = (float)($row['qty'] ?? 0);
            if (!$resourceId) continue;

            $existing = $db->createCommand(
                "SELECT id FROM schedule_resource_report_log WHERE activity_id = :aid AND resource_id = :rid AND date = :d LIMIT 1",
                [':aid' => $actid, ':rid' => $resourceId, ':d' => $date]
            )->queryScalar();

            if ($qty <= 0) {
                if ($existing) {
                    $db->createCommand("DELETE FROM schedule_resource_report_log WHERE id = :id", [':id' => $existing])->execute();
                }
                continue;
            }

            if ($existing) {
                $db->createCommand(
                    "UPDATE schedule_resource_report_log SET quantity = :q, updated_at = NOW() WHERE id = :id",
                    [':q' => $qty, ':id' => $existing]
                )->execute();
            } else {
                $db->createCommand()->insert('schedule_resource_report_log', [
                    'activity_id'        => $actid,
                    'project_id'         => $projectId,
                    'resource_id'        => $resourceId,
                    'resource_type_id'   => $resourceTypeId,
                    'pricing_resourceid' => 0,
                    'date'               => $date,
                    'quantity'           => $qty,
                    'name'               => $name,
                    'hours'              => 0,
                    'ot_hours'           => 0,
                    'duration'           => 0,
                ])->execute();
            }
        }

        return ['error' => 'No'];
    }
}
