<?php

namespace app\controllers;

use Yii;
//use yii\rest\Controller;
use yii\web\Response;
use yii\web\Request;
use yii\web\JsonParser;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\models\Scheduleactivities;
use app\models\ScheduleTaskNew;
use app\models\ScheduleTaskReport;
use app\models\ScheduleProgressReport;
use app\models\ScheduleProgressReportLog;
use app\models\ActivityReportWrkactvty;
use app\models\ScheduleActivityNew;


class ApiController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
        ];
        return $behaviors;
    }   

    /*public function behaviors()
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }*/

    public function beforeAction($action) 
    { 
        /*// Get the request object
        $request = \Yii::$app->getRequest();

        // Set a new request parser (e.g., JSON parser)
        $jsonParser = new JsonParser();
        $request->parsers = [
            'application/json' => $jsonParser,
        ];
*/

        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }


    public function actionReportprogress()
    {

        $jsonParser = new JsonParser();
        $jsonData = $jsonParser->parse(\Yii::$app->request->rawBody, 'application/json');

        $connection     = \Yii::$app->db;

        $uid            = 0;
        $activityid     = $jsonData['activityid'];
        $schActvty      = Scheduleactivities::findOne($activityid);
        $projectid      = $schActvty->projectId;

        $schtasknew     = ScheduleTaskNew::find()->where(['activity_Id'=>$activityid])->one();
        $taskid         = $schtasknew->task_Id;

        $schactnew      = ScheduleActivityNew::find()->where(['actvity_id' => $activityid])->one();
        $workhours      = $schactnew->Workhours;

        $curr_cycle     = 1;
        $currentqnty    = $jsonData['currentqnty'];

        $dateselected   = date("Y-m-d");

        $break_hour     = Yii::$app->helper->timeToSeconds($jsonData['break_hour']);
        $wastehour      = Yii::$app->helper->timeToSeconds($jsonData['waste_hour']);
        $stoppage_time  = Yii::$app->helper->timeToSeconds('0:0');

        $start_time     = strtotime($jsonData['start_date']." ".$jsonData['start_time']); 
        $end_time       = strtotime($jsonData['end_date']." ".$jsonData['end_time']); 

        $tot_duration   = (round(abs($end_time - $start_time),2) - $break_hour) / (60*60);//hours
        $act_days       = floor(round(abs($end_time - $start_time),2)/((60*60*24)));//Seconds
        $act_duration   = ($tot_duration - ($act_days * (24 - $workhours))) * (60*60);


        //--- Task Reporting --------------------------------

        $taskreprt  = ScheduleTaskReport::find()
                        ->where(['taskid' => $taskid])
                        ->andWhere(['activityid' => $activityid])
                        ->andWhere(['current_cycle' => $curr_cycle])
                        ->one();

        if(!$taskreprt)
            $taskreprt = new ScheduleTaskReport();

        $taskreprt->cycleno         = $curr_cycle;
        $taskreprt->current_cycle   = $curr_cycle;
        //$model->task_resource_id = (($_POST['task_resource_id']) ? $_POST['task_resource_id'] : 0);
        //$model->resources_at_site = $resources_at_site;
        $taskreprt->task_description = '';
        $taskreprt->taskid          = $taskid;
        $taskreprt->activityid      = $activityid;
        $taskreprt->projectid       = $projectid;
        $taskreprt->date            = $dateselected;
        $taskreprt->act_duration    = $act_duration;
        $taskreprt->waste_hour      = $wastehour;
        $taskreprt->break_hour      = $break_hour;
        $taskreprt->stoppage_time   = $stoppage_time;
        $taskreprt->task_date       = date("Y-m-d", strtotime($jsonData['start_date']));
        $taskreprt->task_enddate    = date("Y-m-d", strtotime($jsonData['end_date']));
        $taskreprt->start_time      = $jsonData['start_time'];
        $taskreprt->end_time        = $jsonData['end_time'];
        $taskreprt->status          = 1;
        $taskreprt->save(false);
        //$scheduleTaskReportIds[]= $taskreprt->id;
        //---------------------------------------------------


        //----Progress Report-----------------------------------------------------------------------------

            //if($is_schedule_activity = $_POST['is_schedule_activity']){
            if($is_schedule_activity = 1){
                $progReportWhereArr = ['activity_id' => $activityid];
                $progReportWhereCond = " AND activity_id='".$activityid."'";
            }
            else{
                $progReportWhereArr = ['wrk_grp_act_id' => $activityid];
                $progReportWhereCond = " AND wrk_grp_act_id='".$activityid."'";
            }

            $startDateQuery =   ScheduleTaskReport::find()
                                ->where(['activityid' => $activityid])
                                ->orderBy(['task_date'=>SORT_ASC, 'start_time'=>SORT_ASC])
                                ->one();
            $endDateQuery   =   ScheduleTaskReport::find()
                                ->where(['activityid' => $activityid])
                                ->orderBy(['task_enddate'=>SORT_DESC, 'end_time'=>SORT_DESC])
                                ->one();
            $curEndDateQry  =   ScheduleTaskReport::find()
                                ->where(['activityid' => $activityid])
                                ->andWhere(['current_cycle' => $curr_cycle])
                                ->orderBy(['task_enddate'=>SORT_DESC, 'end_time'=>SORT_DESC])
                                ->one();

            $exist  = ScheduleProgressReport::find()->where($progReportWhereArr)->one();
            if(!$exist){
                $actreport= new ScheduleProgressReport;
                $actreport->activity_id     = ($is_schedule_activity) ? $activityid : 0;
                $actreport->wrk_grp_act_id  = (!$is_schedule_activity) ? $activityid : 0;
                $actreport->start_date      = date('Y-m-d', strtotime($startDateQuery->task_date));
                $actreport->cumulated_qty   = $currentqnty;
                $actreport->updated_at      = date('Y-m-d');
                if($actreport->save(false)):
                    $actreportlog                   = new ScheduleProgressReportLog;
                    $actreportlog->activity_id      = ($is_schedule_activity) ? $activityid : 0;
                    $actreportlog->wrk_grp_act_id   = (!$is_schedule_activity) ? $activityid : 0;
                    $actreportlog->report_date      = $endDateQuery->task_enddate;
                    $actreportlog->current_cycle    = $curr_cycle;
                    $actreportlog->currentqty       = $currentqnty;
                    $actreportlog->reported_by      = 0;
                    $actreportlog->break_hour       = round($break_hour / 3600, 4);
                    $actreportlog->save(false);
                endif;
            }
            else{
                $logexist = ScheduleProgressReportLog::find()
                            ->where($progReportWhereArr)
                            ->andWhere(['current_cycle' => $curr_cycle])
                            ->one();
                if(!empty($logexist)){
                    $logexist->currentqty       = $currentqnty;
                    $logexist->reported_by      = $uid;
                    $logexist->report_date      = $curEndDateQry->task_enddate;
                    $logexist->break_hour       = round($break_hour / 3600, 4);
                    $logexist->save(false);
                }
                else{
                    if($currentqnty != ''):
                        $actreportlog                   = new ScheduleProgressReportLog;
                        $actreportlog->activity_id      = ($is_schedule_activity) ? $activityid : 0;
                        $actreportlog->wrk_grp_act_id   = (!$is_schedule_activity) ? $activityid : 0;
                        $actreportlog->report_date      = $endDateQuery->task_enddate;
                        $actreportlog->current_cycle    = $curr_cycle;
                        $actreportlog->currentqty       = $currentqnty;
                        $actreportlog->reported_by      = $uid;
                        $actreportlog->break_hour       = round($break_hour / 3600, 4);
                        $actreportlog->save(false);
                    endif;
                }

                $sql1       = "SELECT SUM(currentqty) AS totalqty FROM schedule_progress_report_log WHERE 1".$progReportWhereCond;
                $command    = $connection->createCommand($sql1);
                $dataReader = $command->query();
                $totalQty   = $dataReader->read();

                $exist->start_date    = date('Y-m-d', strtotime($startDateQuery->task_date));
                $exist->cumulated_qty = $totalQty['totalqty'];
                $exist->updated_at    = date('Y-m-d');
                $exist->save(false);
            }


            $actreport= new ActivityReportWrkactvty;
            if(isset($activityid))
                $actreport->activity_id= $activityid;
            $actreport->quantity = 0;

            if(isset($endDateQuery->task_enddate))
                 $actreport->date = date('Y-m-d', strtotime($endDateQuery->task_enddate));
            $actreport->projectid = $projectid;
            $actreport->status = 0;
            $actreport->save(false);



        //--- NEW ACTUAL DURATION CALCULATION BASED ON TASK REPORTING --------------
            $totCumDuration     =   Yii::$app->helper->secondsToTime(
                                        Yii::$app->helper->getDataFromQuery("   
                                            SELECT (SUM( (act_duration)) + 
                                                    SUM( (waste_hour)) + 
                                                    SUM( (break_hour)) 
                                                  ) as tot_duration_sum  
                                            FROM schedule_task_report 
                                            WHERE status = 1
                                            AND activityid=".$activityid
                                        )['tot_duration_sum']
                                    );

            $totStoppageTime    =   Yii::$app->helper->secondsToTime(
                                        Yii::$app->helper->getDataFromQuery("   
                                            SELECT SUM(stoppage_time) as tot_stoppage_time  
                                            FROM schedule_task_report 
                                            WHERE status = 1
                                            AND activityid=".$activityid
                                        )['tot_stoppage_time']
                                    );

            $actualDuration = 0;
            if($totCumDuration && $totCumDuration != '00:00'){
                $cumqty         = Yii::$app->helper->getDataFromQuery(" SELECT SUM(currentqty) AS totalqty 
                                                            FROM schedule_progress_report_log 
                                                            WHERE  activity_id=".$activityid
                                                         )['totalqty'];
                $cumqty = round($cumqty,3);

                $Cycles     = $schactnew->Cycles;
                $workhours  = $schactnew->Workhours;
                $resunit    = $schactnew->Resourceunits;
                $activityQty = $schActvty->quantity;

                $totStoppageDays = 0;
                if($totStoppageTime)
                    $totStoppageDays = Yii::$app->helper->hoursToMinutes($totStoppageTime)/($workhours*60);


                $from_time      = strtotime($startDateQuery->task_date." ".$startDateQuery->start_time); 
                $to_time        = strtotime($endDateQuery->task_enddate." ".$endDateQuery->end_time); 
                $diff_minutes   = round(abs($from_time - $to_time) / 60,2);
                $holidayCount   = Yii::$app->helper->getHolidayCount($startDateQuery->task_date, $endDateQuery->task_enddate, $schActvty->projectId);

                $stoppageDaysDeduct = 0;
                if($totStoppageDays)
                    $stoppageDaysDeduct = ($totStoppageDays > 1) ? floor($totStoppageDays) : 1;

                $totCumDays     = ($diff_minutes/(24*60)) - $stoppageDaysDeduct;
                $totCumDuration = (floor($totCumDays) * $workhours) + ceil($diff_minutes%(24*60)/60);
                $reptdDays      = ($totCumDuration/$workhours) - $holidayCount;

                $actualDuration = round((($reptdDays/$cumqty)*$activityQty) + $totStoppageDays);

                if(round($reptdDays) > $actualDuration){
                    $task_start_date    = date_create($startDateQuery->task_date);
                    $task_end_date      = date_create($endDateQuery->task_enddate);
                    $diff               = date_diff($task_start_date, $task_end_date);
                    $duration           = $diff->format("%a");
                    $schActvty->duration = $duration - ($holidayCount*$workhours);
                    $schActvty->end_date = date('Y-m-d', strtotime($endDateQuery->task_enddate));
                }
                else{
                    $schActvty->duration = $actualDuration;
                    $schActvty->end_date = date('Y-m-d', strtotime($schActvty->start_date. ' + '.$actualDuration.' days'));
                }
            }

            $schActvty->act_start_date = date('Y-m-d', strtotime($startDateQuery->task_date));
            $schActvty->task_updated = 0;
            $schActvty->save(false);

            if($actualDuration){
                $schProgRepLog = ScheduleProgressReportLog::find()
                                ->where($progReportWhereArr)
                                ->andWhere(['current_cycle' => $curr_cycle])
                                ->orderBy(['id'=>SORT_DESC])
                                ->one();
                $schProgRepLog->activity_duration    = $actualDuration;
                $schProgRepLog->save(false);
            }
            //----END-----------------------------------------------------------------------------

                  

        return ['res' => 'success'];
    }




  
}
