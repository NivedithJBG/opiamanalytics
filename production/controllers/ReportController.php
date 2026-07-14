<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\ProjuserSelection;

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

    public function actionMobile()
    {
        $this->layout = '@app/views/layouts/mobile';
        $projectid = $this->getCurrentProjectId();
        $db = Yii::$app->db;
        $project = $projectid ? $db->createCommand("SELECT Name FROM projects WHERE Project_Id=:pid", [':pid'=>$projectid])->queryOne() : null;
        $projectName = $project ? $project['Name'] : 'No Project Selected';
        return $this->render('mobile', ['projectName' => $projectName]);
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
            WHERE wa.project_Id = :pid3 AND wn.Status = 0
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
                JOIN scheduleactivities sa ON sa.activity_id = wa.id AND sa.projectId = :pid
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
                $status      = $completed ? '<span style="color:green;font-weight:600;">Complete</span>' : '<span style="color:#888;">Active</span>';

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
                    $html .= '<button type="button" class="btn btn-sm btn-primary taskreport" '
                           . 'data-id="' . $schedId . '" '
                           . 'data-toggle="modal" data-target="#taskReportPopup" '
                           . 'title="Report Progress">Report Progress</button> ';
                    $html .= '<button type="button" class="btn btn-xs btn-default activityreportcomplete" '
                           . 'data-v="' . $schedId . '" title="Mark Complete">&#10003;</button> ';
                    $html .= '<button type="button" class="btn btn-xs btn-warning reportclear" '
                           . 'data-id="' . $schedId . '" title="Clear">&#10005;</button>';
                } else {
                    $html .= '<button type="button" class="btn btn-xs btn-info activityreportreactivate" '
                           . 'data-v="' . $schedId . '" title="Reactivate">&#8635; Reactivate</button>';
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
                $html .= '<button type="button" class="btn btn-sm btn-primary taskreport" '
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

    // Return existing log entry for a specific activity + date (for pre-filling edit form)
    public function actionGetreportbydate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $actid      = (int)Yii::$app->request->post('actid');
        $reportDate = Yii::$app->request->post('report_date', '');
        if (!$actid || !$reportDate) return ['found' => false];

        $db  = Yii::$app->db;
        $row = $db->createCommand(
            "SELECT id, currentqty, break_hour, report_date
             FROM schedule_progress_report_log
             WHERE activity_id = :aid AND report_date = :rd
             ORDER BY id DESC LIMIT 1",
            [':aid' => $actid, ':rd' => $reportDate]
        )->queryOne();

        if (!$row) return ['found' => false];

        $spr = $db->createCommand(
            "SELECT start_date FROM schedule_progress_report WHERE activity_id = :aid LIMIT 1",
            [':aid' => $actid]
        )->queryOne();

        return [
            'found'      => true,
            'log_id'     => (int)$row['id'],
            'currentqty' => (float)$row['currentqty'],
            'break_hour' => (float)$row['break_hour'],
            'start_date' => ($spr && $spr['start_date'] && $spr['start_date'] !== '0000-00-00')
                            ? date('Y-m-d', strtotime($spr['start_date'])) : '',
        ];
    }

    // Update an existing log entry by date and recalculate cumulated totals
    public function actionProgressreportedit()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $projectid = $this->getCurrentProjectId();
        if (!$projectid) return ['error' => 'Yes'];

        $actid      = (int)Yii::$app->request->post('actid');
        $logId      = (int)Yii::$app->request->post('log_id');
        $currentQty = (float)Yii::$app->request->post('currentqnty', 0);
        $breakHours = (float)Yii::$app->request->post('break_hours', 0);
        $startDate  = Yii::$app->request->post('start_date', '');
        $reportDate = Yii::$app->request->post('reportdate', '');
        $uid        = Yii::$app->user->id;
        $db         = Yii::$app->db;

        if (!$actid || !$logId) return ['error' => 'Yes'];

        $reportDateDb = $this->parseDate($reportDate) ?: date('Y-m-d');

        // Update the specific log row
        $db->createCommand(
            "UPDATE schedule_progress_report_log
             SET currentqty = :qty, break_hour = :bh, reported_by = :uid
             WHERE id = :lid AND activity_id = :aid",
            [':qty' => $currentQty, ':bh' => $breakHours, ':uid' => $uid, ':lid' => $logId, ':aid' => $actid]
        )->execute();

        // Recalculate cumulated_qty from all log entries for this activity
        $totals = $db->createCommand(
            "SELECT SUM(currentqty) AS totalqty, MIN(report_date) AS first_date
             FROM schedule_progress_report_log WHERE activity_id = :aid",
            [':aid' => $actid]
        )->queryOne();

        $newCumQty  = (float)($totals['totalqty'] ?? 0);
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
}
