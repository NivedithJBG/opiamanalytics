<?php


namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use app\models\Scheduleactivities;
use app\models\ScheduleActivityNew;
use app\models\ScheduleProgressReport;
use app\models\ScheduleTaskReport;
use app\models\ScheduleProgressReportLog;
use app\models\ProgressReport;
use app\models\ActivityRelations;
use app\models\Holidays;

class HelperComponent extends Component
{

    public function floatToTimeFormat($value=''){
        if($value > 0){
          $valArr = explode('.', $value);
          if(count($valArr) > 1){
            $value = number_format($value, 2,'.','');
            $res = '';
            if(strlen($valArr[0]) == 1) $res .= '0';
            $res .= str_replace('.', ':', $value).':00';
            return $res;
          }
          else return '0'.$value.':00:00';
        }
        else
          return '00:00';
    }

    public function formatTime($duration){
        return $duration;
        $duration = number_format((float)$duration,2,'.','');
        $totMinutes = (floor($duration)*60)+(($duration - floor($duration))*100);
        $hours = (int)(floor($totMinutes/60));
          $minutes = round($totMinutes-($hours*60));

          //$hours = floor($totMinutes/60);
          //$minutes = $totMinutes - ($hours * 60);
        
        return number_format((float)$hours.'.'.$minutes,2,'.','');

        //return $newTime = $hours.'.'.$minutes;
    }

    public function hoursToMinutes($duration){
        $duration = number_format((float)$duration,2,'.','');
        return $totMinutes = (floor($duration)*60)+(($duration - floor($duration))*100);
    }

    public function timeToSeconds(string $time){
      $arr = explode(':', $time);
      if (count($arr) === 3) {
          return ($arr[0] * 3600) +( $arr[1] * 60) + $arr[2];
      }
      return ($arr[0] * 3600) + ($arr[1] * 60);
    }

    public function secondsToTime($seconds){
        $secs   = $seconds % 60;
        $hrs  = $seconds / 60;
        $mins   = (int)($hrs % 60);
        $hrs  = (int)($hrs / 60);

        $mins   = ($mins < 10) ? '0'.$mins : $mins;
        $hrs  = ($hrs < 10) ? '0'.$hrs : $hrs;

        return $hrs . ":" . $mins;
    }

    public function getDataFromQuery($query){
        $connection = \Yii::$app->db;
        $command = $connection->createCommand($query);
        $dataReader = $command->query();
        return $dataReader->read();
    }
    public function getAllDataFromQuery($query){
        $connection = \Yii::$app->db;
        $command = $connection->createCommand($query);
        $dataReader = $command->query();
        return $dataReader->readAll();
    }

    public function timeToDuration($time){
      if($timeArr = explode(":", $time))
        return $timeArr[0].':'.$timeArr[1];
      else
        return '00:00';
    }

    public function getLongestActivity($projectId){
        $latActQuery = Scheduleactivities::find()->where(['projectId'=>$projectId])->andWhere(['status'=> 0])->orderBy(['end_date'=>SORT_DESC])->one();
        if($latActQuery)
          return ['id' => $latActQuery->id, 'end_date' => $latActQuery->end_date];
        else
          return ['id' => 0, 'end_date' => ''];
    }

    public function tableauRefresh($dataArr){
        $error = 0;

        $server = 'https://prod-apnortheast-a.online.tableau.com';
        $api_version = '3.18';
        $personal_access_token = 'WhoWabFUQKeU5EStW6he7g==:4WWUBfzm6tzq9nxLe6hoKD9Qlb1WTeJd';//Expire on 29-May-2025
        $data['credentials'] = ["personalAccessTokenName" => "OpiamToken", 
                                "personalAccessTokenSecret" => $personal_access_token,
                                "site" => ["contentUrl" => "opiam"]
                                ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $server . '/api/'.$api_version.'/auth/signin');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if ($response === false) {
            $error = 1;
        }
        curl_close($ch);
        $response_data = json_decode($response, true);
        if(!$response_data) $error = 1;
        //----------------------------------

        if(!$error){
            $token = $response_data['credentials']['token'];
            $site_id = $response_data['credentials']['site']['id'];

            $work_book_name = $dataArr['workBookName'];
            $url = $server . '/api/' . $api_version . '/sites/' . $site_id . '/workbooks?filter=contentUrl:eq:'. $work_book_name;
 
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-Tableau-Auth: ' . $token,
                'Content-Type: application/json',
                'Accept: application/json'

            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, false);

            $response = curl_exec($ch);

            if (!curl_errno($ch)) {
              $info = curl_getinfo($ch);
            }

            if ($response === false) {
                $error = 1;
            }
            curl_close($ch);
            $response_data = json_decode($response, true);
            //echo "<pre>"; print_r($response_data); exit;
            if (count($response_data['workbooks']) === 0) {
                $error = 1;
            }
        }
        //----------------------------------


        if(!$error){
            $extract_id = $response_data['workbooks']['workbook'][0]['id'];
            $ch = curl_init();
            $data['tsRequest'] = '';
            $url = $server . '/api/' . $api_version . '/sites/' . $site_id . '/workbooks/' . $extract_id . '/refresh';
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-Tableau-Auth: ' . $token,
                'Content-Type: text/plain',
                'Accept: application/json'
            ));
            curl_setopt($ch, CURLOPT_POSTFIELDS, '<tsRequest></tsRequest>');


            $response = curl_exec($ch);
            if ($response === false) {
                $error = 1;
            }
            $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            //echo '<br>status - '.$status;
            //echo $response;

            if($status == 202){
                $response_data = json_decode($response, true);
                $jobid = $response_data['job']['id'];
                $arr = array('jobid' => $jobid,'message' => 'Dashboard data refresh submitted successfully, please wait till the refresh is completed..', 'error' => 'No');
                return json_encode($arr);
            }
            elseif($status == 409){
                $arr = array('message' => 'Refresh job is already in queue, please check the dashboard after few minutes..', 'error' => 'Yes');
                return json_encode($arr);
            }
            /*$response_data = json_decode($response, true);
            if ($response_data['refreshJob']['status'] === 'Failed') {
                $error = 1;
            }*/
        }

        $arr = array('message' => 'Error occurred, please try after some time..', 'error' => 'Yes');
        return json_encode($arr);
    }


    public function tableauRefreshProgress($dataArr){
        $error = 0;

        $server = 'https://prod-apnortheast-a.online.tableau.com';
        $api_version = '3.18';
        $personal_access_token = 'WhoWabFUQKeU5EStW6he7g==:4WWUBfzm6tzq9nxLe6hoKD9Qlb1WTeJd';//Expire on 29-May-2025
        $data['credentials'] = ["personalAccessTokenName" => "OpiamToken", 
                                "personalAccessTokenSecret" => $personal_access_token,
                                "site" => ["contentUrl" => "opiam"]
                                ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $server . '/api/'.$api_version.'/auth/signin');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if ($response === false) {
            $error = 1;
        }
        curl_close($ch);
        $response_data = json_decode($response, true);
        if(!$response_data) $error = 1;
        //----------------------------------

        if(!$error){
            $token = $response_data['credentials']['token'];
            $site_id = $response_data['credentials']['site']['id'];

            $jobid = $dataArr['jobid'];
            $url = $server . '/api/' . $api_version . '/sites/' . $site_id . '/jobs/'. $jobid;
 
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-Tableau-Auth: ' . $token,
                'Content-Type: application/json',
                'Accept: application/json'

            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, false);
            $response = curl_exec($ch);

            if (!curl_errno($ch)) {
              $info = curl_getinfo($ch);
            }

            if ($response === false) {
                $error = 1;
            }
            curl_close($ch);
            $response_data = json_decode($response, true);
            //echo "<pre>"; print_r($response_data); exit;
            if (count($response_data['job']) === 0) {
                $error = 1;
            }
            else{

                $progress = (isset($response_data['job']['progress']) ? $response_data['job']['progress'] : 0);
                $arr = array('progress' => $progress, 'error' => 'No');
                return json_encode($arr);
            }

        }
        //----------------------------------

        $arr = array('message' => 'Error occurred, please try after some time..', 'error' => 'Yes');
        return json_encode($arr);
    }


    public function GetRelationcorrect($projectid)
    {

        $scheduleactivities = Scheduleactivities::find()->where(['projectId'=>$projectid])->andWhere(['status'=> 0])->orderBy(['scheduleitem_id'=> SORT_ASC,'sortorder'=>SORT_ASC])->all();
        $connection = Yii::$app->db;
        //if(count($scheduleactivities) > 0)
        if(!empty($scheduleactivities))
        {
            foreach ($scheduleactivities as $scheduleactivity) 
            {
                $today = date('Y-m-d');
                $activityid = $scheduleactivity->id;
                $schedulereport = ScheduleProgressReport::find()->where(['activity_id'=> $activityid])->one(); 

                $sql1       = "SELECT SUM(currentqty) AS totalqty FROM schedule_progress_report_log WHERE activity_id='".$activityid."'";
                $command    = $connection->createCommand($sql1);
                $dataReader = $command->query();
                $totalQty   = $dataReader->read();

                if($schedulereport && $totalQty['totalqty']>0){
                    $start_date1 = date('Y-m-d', strtotime($schedulereport->start_date));
                    $start_date  = $schedulereport->start_date;
                }
                else{
                    $start_date1 = date('Y-m-d', strtotime($scheduleactivity->actual_start_date));
                    if($today>$scheduleactivity->actual_start_date){
                        $start_date = $today;
                    }
                    else{
                        $start_date = $scheduleactivity->actual_start_date;
                    }
                }
                
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
                $holidayCount   = 0;
                if($totCumDuration && $totCumDuration != '00:00'){
                    $cumqty         = Yii::$app->helper->getDataFromQuery(" SELECT SUM(currentqty) AS totalqty 
                                                                FROM schedule_progress_report_log 
                                                                WHERE  activity_id=".$activityid
                                             )['totalqty'];
                    $cumqty = round($cumqty,3);

                    $schactnew  = ScheduleActivityNew::find()->where(['actvity_id' => $activityid])->one();
                    $Cycles     = $schactnew->Cycles;
                    $workhours  = $schactnew->Workhours;
                    $resunit    = $schactnew->Resourceunits;
                    
                    $activityQty = $scheduleactivity->quantity;
                    $actualDuration = 0;
                    if($cumqty){
                        //$workhours = 24;//Consider only 24
                        $totStoppageDays = 0;
                        if($totStoppageTime)
                            $totStoppageDays = Yii::$app->helper->hoursToMinutes($totStoppageTime)/($workhours*60);

                        //$actualDuration   = round((($totCumDuration / $cumqty) * $activityQty)  / $workhours);
                        //$actualDuration   = round((((Yii::$app->helper->hoursToMinutes($totCumDuration)/($workhours*60))/$cumqty)*$activityQty)+ $totStoppageDays);


                        $startDateQuery =   ScheduleTaskReport::find()
                                            ->where(['activityid' => $activityid])
                                            ->orderBy(['task_date'=>SORT_ASC, 'start_time'=>SORT_ASC])
                                            ->one();
                        $endDateQuery   =   ScheduleTaskReport::find()
                                            ->where(['activityid' => $activityid])
                                            ->orderBy(['task_enddate'=>SORT_DESC, 'end_time'=>SORT_DESC])
                                            ->one();

                        $from_time      = strtotime($startDateQuery->task_date." ".$startDateQuery->start_time); 
                        $to_time        = strtotime($endDateQuery->task_enddate." ".$endDateQuery->end_time); 
                        $diff_minutes   = round(abs($from_time - $to_time) / 60,2);
                        $holidayCount   = Yii::$app->helper->getHolidayCount($startDateQuery->task_date, $endDateQuery->task_enddate, $projectid);

                        /*$totCumDuration = Yii::$app->helper->secondsToTime(($diff_minutes - Yii::$app->helper->hoursToMinutes($totStoppageTime))*60);
                        //$reptdDays      = (Yii::$app->helper->hoursToMinutes($totCumDuration)/($workhours*60));
                        $reptdDays      = (
                                            (  
                                                (floor(Yii::$app->helper->hoursToMinutes($totCumDuration)/(24*60)) * $workhours) + 
                                                (((Yii::$app->helper->hoursToMinutes($totCumDuration)%($workhours*60))/($workhours*60)) * $workhours)
                                            )
                                            - (floor(Yii::$app->helper->hoursToMinutes($totStoppageTime)/(24*60)) * $workhours)
                                          ) / $workhours;*/

                        $stoppageDaysDeduct = 0;
                        if($totStoppageDays)
                            $stoppageDaysDeduct = ($totStoppageDays > 1) ? floor($totStoppageDays) : 1;


                        /*if($activityid == 1877){
                            echo "holidayCount - ".$holidayCount;
                            echo "<br>";
                            echo "stoppageDaysDeduct - ".$stoppageDaysDeduct;
                            echo "<br>";
                            echo "diff_minutes - ".$diff_minutes;
                            echo "<br>";
                            echo "totCumDays - ".$totCumDays     = ($diff_minutes/(24*60)) - $stoppageDaysDeduct;
                            echo "<br>";
                            echo "totCumDuration 1 - ".(ceil($diff_minutes%(24*60)/60));
                            echo "<br>";
                            echo "totCumDuration 2 - ".(floor($totCumDays) * $workhours);
                            echo "<br>";
                            echo "totCumDuration - ".$totCumDuration = ((floor($totCumDays) * $workhours) + (ceil($diff_minutes%(24*60)/60)));
                            echo "<br>";
                            echo "reptdDays - ".$reptdDays      = ($totCumDuration/$workhours) - $holidayCount;
                            echo "<br>";
                            echo "actualDuration - ".$actualDuration      =  ((($reptdDays/$cumqty)*$activityQty));
                            echo "<br>";
                            echo "totStoppageDays - ".$totStoppageDays;
                            echo "<br>";
                            echo "actualDuration - ".$actualDuration      =  round((($reptdDays/$cumqty)*$activityQty)+ $stoppageDaysDeduct);
                            echo "<br>";
                        }*/

                        $totCumDays     = ($diff_minutes/(24*60)) - $stoppageDaysDeduct;
                        $totCumDuration = (floor($totCumDays) * $workhours) + ceil($diff_minutes%(24*60)/60);
                        $reptdDays      = ($totCumDuration/$workhours) - $holidayCount;


                        $actualDuration = round((($reptdDays/$cumqty)*$activityQty)+ $stoppageDaysDeduct);

                        if(round($reptdDays) > $actualDuration){
                            $task_start_date = date_create($startDateQuery->task_date);
                            $task_end_date   = date_create($endDateQuery->task_enddate);
                            $diff            = date_diff($task_start_date, $task_end_date);
                            $duration        = $diff->format("%a");

                            $scheduleactivity->duration =  $duration - ($holidayCount*$workhours);
                            $scheduleactivity->end_date = date('Y-m-d', strtotime($endDateQuery->task_enddate));
                        }
                        else{
                            $scheduleactivity->duration = $actualDuration;
                            $scheduleactivity->end_date = date('Y-m-d', strtotime($scheduleactivity->start_date. ' + '.$actualDuration.' days'));
                            /*$scheduleactivity->end_date   = date('Y-m-d', 
                                                strtotime(
                                                    Yii::$app->helper->getDateAfterHoliday($scheduleactivity->start_date, $projectid, $actualDuration)
                                                )
                                              );*/
                        }
                    }
                }
                if($actualDuration)
                    $no_of_days = $actualDuration;
                else
                    $no_of_days = $scheduleactivity->old_duration;

                //----END-----------------------------------------------------------------------------


                if($start_date!='' && $start_date!='0000-00-00'){
                    $duration = $no_of_days - 1;
                    $scheduleactivity->duration = $no_of_days;
                    $scheduleactivity->start_date = $start_date;
                    $scheduleactivity->end_date = date('Y-m-d', strtotime($start_date. ' + '.$duration.' days'));
                    //$scheduleactivity->end_date = date('Y-m-d',strtotime(Yii::$app->helper->getDateAfterHoliday($start_date, $projectid, $no_of_days)));
                }
                else{

                    $duration = $no_of_days;
                    $scheduleactivity->duration = $no_of_days;
                    $scheduleactivity->start_date = $start_date;
                    $scheduleactivity->end_date = date('Y-m-d', strtotime($start_date. ' + '.$duration.' days'));
                    //$scheduleactivity->end_date  = date('Y-m-d',strtotime(Yii::$app->helper->getDateAfterHoliday($start_date, $projectid, $duration)));
                }
                $scheduleactivity->save(false);

                if($actualDuration){
                    $schProgRepLog = ScheduleProgressReportLog::find()
                                    ->where(['activity_id' => $activityid])
                                    ->orderBy(['id'=>SORT_DESC])
                                    ->one();
                    $schProgRepLog->activity_duration      = $actualDuration;
                    $schProgRepLog->holiday_cnt            = $holidayCount;
                    //$schProgRepLog->last_activity        = Yii::$app->helper->getLongestActivity($scheduleactivity->projectId)['id'];
                    //$schProgRepLog->last_activity_date   = Yii::$app->helper->getLongestActivity($scheduleactivity->projectId)['end_date'];
                    $schProgRepLog->save(false);
                }
            }
        }


        $query="SELECT a.scheduleitem_id,a.wbsid,a.name,a.projectId FROM wbsscheduleitems AS a
                INNER JOIN workgroups_new AS b ON  a.wbsid=b.Workgroup_Id
                WHERE  a.projectId='".$projectid."' AND a.status=0  ORDER BY b.sortorder  ASC,b.Workgroup_Id ASC";
        $command = $connection->createCommand($query);
        $dataReader = $command->query();
        $items = $dataReader->readAll();

        if($items){
            foreach ($items as $item) {
                $sql="  SELECT s.*,w.act_report_complete_status FROM workgroup_activities_new as w 
                        INNER JOIN wbsscheduleitems as b on w.wbs_id=b.wbsid 
                        INNER JOIN scheduleactivities as s on s.scheduleitem_id=b.scheduleitem_id 
                        AND w.activity_Name=s.name 
                        WHERE w.project_Id = '".$item['projectId']."'
                        AND w.pricing_status=0 AND s.status=0 AND s.scheduleitem_id='".$item['scheduleitem_id']."' GROUP BY w.id ORDER BY w.sortorder ASC";
                $command = $connection->createCommand($sql);
                $dataReader = $command->query();
                $availableActivites = $dataReader->readAll();


                if(!empty($availableActivites))
                {       
                    foreach ($availableActivites as $activity) 
                    {
                        $acId = $activity['id'];
                        $today = date('Y-m-d');
                        $allRelations = ActivityRelations::find()->where(['precedent_activity' => $acId])->andWhere(['status'=>0])->all();
                        if(!empty($allRelations))
                        {
                            foreach ($allRelations AS $relation)
                            {
                                $precAct = Scheduleactivities::findOne($acId);
                                $dependAc = Scheduleactivities::findOne($relation->dependent_activity);

                                $actreported = ScheduleProgressReport::find()->where(['activity_id' => $dependAc->id])->one();

                                $reportlog  = "SELECT SUM(currentqty) AS totalqty 
                                                FROM schedule_progress_report_log 
                                                WHERE activity_id='".$dependAc->id."'";
                                $dataReader = $connection->createCommand($reportlog)->query();
                                $totalQty   = $dataReader->read();

                                $lastreported   =   "SELECT report_date 
                                                    FROM schedule_progress_report_log 
                                                    WHERE activity_id='".$dependAc->id."'
                                                    AND currentqty!=0 
                                                    ORDER BY report_date DESC LIMIT 0,1";
                                $dataReader     = $connection->createCommand($lastreported)->query();
                                $lastreportedate= $dataReader->read();

                                $reported = ProgressReport::find()->where(['activity_id'=> $relation->dependent_activity])->andWhere(['status' => 1])->one();
                                $precedentActual = ProgressReport::find()->where(['activity_id'=>$acId])->one();

                                if(!$precedentActual){  
                                    $precedentActual = $precAct;
                                    $start_date = strtotime($precedentActual->start_date);
                                    $end_date = strtotime($precedentActual->end_date);
                                    $enDateWillBe =  date('Y-m-d', $end_date);
                                }

                                
                                if($relation->relation_type == 1)//SS
                                    {

                                        $depRelations = ActivityRelations::find()->where(['dependent_activity' => $dependAc->id])->andWhere(['status'=>0])->all();

                                        $scheduleID = '';
                                        $scheduleEnddate = '';
                                        foreach($depRelations as $depRelation):
                                            $thisAct = Scheduleactivities::findOne($depRelation->precedent_activity);
                                            $thisdepAct = Scheduleactivities::findOne($depRelation->dependent_activity);
                                            
                                            if($depRelation->relation_type == 1){//SS
                                                $deplag = $thisdepAct->lag;
                                                $depduration = $thisdepAct->duration + $deplag - 1;
                                                $depend = date('Y-m-d', strtotime($thisAct->start_date. ' + '.$depduration.' days'));

                                            }
                                            elseif($depRelation->relation_type == 2)//FS
                                            {
                                                $deplag = $dependAc->lag + 1;
                                                $depstart = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                                $depduration = $thisdepAct->duration - 1;
                                                $depend = date('Y-m-d', strtotime($depstart. ' + '.$depduration.' days'));

                                            }
                                            elseif($depRelation->relation_type == 3)//FF
                                            {
                                                $deplag = $dependAc->lag;
                                                $depend = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));

                                            }

                                            if($scheduleEnddate=='' || $depend > $scheduleEnddate){
                                                //if($depRelation->relation_type==2){
                                                    $scheduleID = $thisAct->id;
                                                    //$scheduleEnddate = $thisAct->end_date;
                                                    $scheduleEnddate = $depend;
                                                //}
                                            }
                                        endforeach;

                                        if($scheduleID == $precAct->id){

                                            $lag = $dependAc->lag;
                                            $depstart = date('Y-m-d', strtotime($precAct->start_date. ' + '.$lag.' days'));
                                            if($actreported && $totalQty['totalqty']>0){

                                                //if($actreported->start_date > $depstart){
                                                //---Removed condition for early reporting--------------
                                                if($actreported->start_date){
                                                    $dependAc->start_date = $actreported->start_date;
                                                    $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));

                                                }
                                                else{
                                                    $dependAc->start_date = date('Y-m-d', strtotime($precAct->start_date. ' + '.$lag.' days'));
                                                    $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));
                                                }
                                            }
                                            else{
                                                /*if($today > $depstart){
                                                    $dependAc->start_date = $today;
                                                    $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));
                                                }
                                                else{
                                                    $dependAc->start_date = date('Y-m-d', strtotime($precAct->start_date. ' + '.$lag.' days'));
                                                    $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));
                                                }*/

                                                //-----/\/\COMMENTED BY SREEJITH - To avoid the today restrictions-----/\/\------
                                                $dependAc->start_date = date('Y-m-d', strtotime($precAct->start_date. ' + '.$lag.' days'));
                                                $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date.' + '.$lag.' days'));

                                            }
                                            //$duration = $dependAc->duration + $lag - 1;
                                            $duration = $dependAc->duration - 1;
                                            $oduration = $dependAc->old_duration - 1;
                                            
                                            $dependAc->end_date = date('Y-m-d', strtotime($dependAc->start_date. ' + '.$duration.' days'));
                                            $dependAc->actual_end_date = date('Y-m-d', strtotime($dependAc->actual_start_date. ' + '.$oduration.' days'));
                                            $dependAc->save(false);
                                        }

                                       // }
                                        $lag = $dependAc->lag;
                                        
                                    }
                                elseif($relation->relation_type == 2)//FS
                                    {

                                        $depRelations = ActivityRelations::find()->where(['dependent_activity' => $dependAc->id])->andWhere(['status' => 0])->all();

                                        $scheduleID = '';
                                        $scheduleEnddate = '';
                                        foreach($depRelations as $depRelation):
                                            $thisAct = Scheduleactivities::findOne($depRelation->precedent_activity);
                                            $thisdepAct = Scheduleactivities::findOne($depRelation->dependent_activity);
                                            if($depRelation->relation_type == 1){
                                                $deplag = $thisdepAct->lag;
                                                $depduration = $thisdepAct->duration + $deplag - 1;
                                                $depend = date('Y-m-d', strtotime($thisAct->start_date. ' + '.$depduration.' days'));
                                            }
                                            elseif($depRelation->relation_type == 2)
                                            {
                                                $deplag = $dependAc->lag + 1;
                                                
                                                $depstart = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                                $depduration = $thisdepAct->duration - 1;
                                                $depend = date('Y-m-d', strtotime($depstart. ' + '.$depduration.' days'));
                                            }
                                            elseif($depRelation->relation_type == 3)
                                            {
                                                $deplag = $dependAc->lag;
                                                $depend = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                            }
                                            if($scheduleEnddate=='' || $depend > $scheduleEnddate){
                                                //if($depRelation->relation_type==2){
                                                    $scheduleID = $thisAct->id;
                                                    //$scheduleEnddate = $thisAct->end_date;
                                                    $scheduleEnddate = $depend;
                                                //}
                                            }
                                        endforeach;

                                        if($scheduleID == $precAct->id){

                                            //if(date('Y-m-d', strtotime($precAct->end_date.' + 1 days')) != $dependAc->start_date)
                                            //{
                                                $lead = $dependAc->lag + 1;
                                                $depstart = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                                if($actreported && $totalQty['totalqty']>0){

                                                    //if($actreported->start_date > $depstart){
                                                    //---Removed condition for early reporting--------------
                                                    if($actreported->start_date){
                                                        $dependAc->start_date = $actreported->start_date;
                                                        //$dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                                    }
                                                    else{
                                                        $dependAc->start_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                                        $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                                    }
                                                }
                                                else{
                                                    /*if($today > $depstart){
                                                        $dependAc->start_date = $today;
                                                        $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                                    }
                                                    else{
                                                        $dependAc->start_date = $depstart;
                                                        $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                                    }*/
                                                    //-----/\/\COMMENTED BY SREEJITH - To avoid the today restrictions-----/\/\------
                                                    $dependAc->start_date = $depstart;
                                                    $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                                }
                                                $start = date('Y-m-d', strtotime($precAct->end_date));
                                                $duration = $dependAc->duration - 1;
                                                $oduration = $dependAc->old_duration - 1;
                                                $dependAc->end_date = date('Y-m-d', strtotime($dependAc->start_date. ' + '.$duration.' days'));
                                                $dependAc->actual_end_date = date('Y-m-d', strtotime($dependAc->actual_start_date. ' + '.$oduration.' days'));

                                                $dependAc->save(false);
                                            //}  

                                        }
                                    }
                                elseif($relation->relation_type == 3)//FF
                                    {

                                        //$depRelations = ActivityRelations::model()->findAll(array('condition'=>'dependent_activity ='.$dependAc->id.' AND relation_type=3 AND status =0'));

                                        $depRelations = ActivityRelations::find()->where(['dependent_activity' => $dependAc->id])->andWhere(['status'=> 0])->all();

                                        $scheduleID = '';
                                        $scheduleEnddate = '';
                                        foreach($depRelations as $depRelation):
                                            $thisAct = Scheduleactivities::findOne($depRelation->precedent_activity);
                                            $thisdepAct = Scheduleactivities::findOne($depRelation->dependent_activity);
                                            if($depRelation->relation_type == 1){
                                                $deplag = $thisdepAct->lag;
                                                $depduration = $thisdepAct->duration + $deplag - 1;
                                                $depend = date('Y-m-d', strtotime($thisAct->start_date. ' + '.$depduration.' days'));
                                            }
                                            elseif($depRelation->relation_type == 2)
                                            {
                                                $deplag = $dependAc->lag + 1;
                                                $depstart = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                                $depduration = $thisdepAct->duration - 1;
                                                $depend = date('Y-m-d', strtotime($depstart. ' + '.$depduration.' days'));
                                            }
                                            elseif($depRelation->relation_type == 3)
                                            {

                                                $deplag = $dependAc->lag;
                                                $depend = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                            }
                                            if($scheduleEnddate=='' || $depend > $scheduleEnddate){
                                                //if($depRelation->relation_type==2){
                                                    $scheduleID = $thisAct->id;
                                                    //$scheduleEnddate = $thisAct->end_date;
                                                    $scheduleEnddate = $depend;
                                                //}
                                            }
                                        endforeach;

                                        if($scheduleID == $precAct->id){

                                            $lead = $dependAc->lag;
                                            $today = date('Y-m-d');

                                            if($actreported && $totalQty['totalqty']>0){

                                                $duration       = $dependAc->duration - 1;
                                                $oduration      = $dependAc->old_duration - 1;
                                                $end_date       = date('Y-m-d', strtotime($actreported->start_date. ' + '.$duration.' days'));
                                                $prec_end_date  = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));

                                                $dependAc->start_date = $actreported->start_date;
                                                
                                                if($end_date>$prec_end_date){
                                                    $dependAc->end_date = $end_date;
                                                    $actual_end_date    = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                                    
                                                    $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                                    $dependAc->actual_end_date   = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                                }
                                                else{
                                                    $datediff = strtotime($prec_end_date) - strtotime($actreported->start_date);
                                                    $bduration = round($datediff / (60 * 60 * 24)) + 1;
                                                    $dependAc->duration = $bduration;
                                                    $dependAc->end_date = $prec_end_date;
                                                    $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                                    $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                                    $dependAc->actual_end_date = date('Y-m-d',strtotime($precAct->actual_end_date.' + '.$lead.' days'));
                                                }
                                                
                                            }
                                            else{

                                                $duration = $dependAc->duration - 1;
                                                $oduration = $dependAc->old_duration - 1;
                                                
                                                $end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                                $dependAc->start_date = date('Y-m-d', strtotime($end_date. ' - '.$duration.' days'));
                                                $dependAc->end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                                $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                                $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                                $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            }

                                            //--
                                            if($actreported && $totalQty['totalqty'] >= $dependAc['quantity']){
                                                $dependAc->end_date   =  date('Y-m-d', strtotime($lastreportedate['report_date']));
                                            }
                                            $dependAc->save(false);

                                        }
                                    }
                                //}
                                // var_dump($dependAc);
                            }

                        }

                        
                        
                    }


                    //--- Holidays => Activity date changes ------
                    $projectId = $item['projectId'];

                    $totSchHolidayCnt = 0;
                    $totActlHolidayCnt = 0;
                    foreach ($availableActivites as $activity){

                        if($relations = ActivityRelations::find()->where(['dependent_activity' => $activity['id']])->andWhere(['status' => 0])->all()){
                            
                            foreach ($relations as $key => $relation) {
                                $actualEndNew       =   '';
                                $actualEndNewTemp   =   '';

                                $precedentActivity  =   Scheduleactivities::findOne($relation->precedent_activity);
                                $preSchStartDate    =   $precedentActivity->actual_start_date;
                                $preSchEndDate      =   $precedentActivity->actual_end_date;

                                $preActlStartDate   =   ($precedentActivity->act_start_date !='' && $precedentActivity->act_start_date != '0000-00-00')
                                                        ? $precedentActivity->act_start_date : $precedentActivity->start_date;
                                $preActlEndDate     =   $precedentActivity->end_date;

                                //$preSchHolidayCnt   =   Yii::$app->helper->getHolidayCount($preSchStartDate, $preSchEndDate, $projectId);
                                //$preActlHolidayCnt  =   Yii::$app->helper->getHolidayCount($preActlStartDate, $preActlEndDate, $projectId);

                                $act_old_dur        =   ($activity['old_duration'] - 1);
                                $act_duration       =   ($activity['duration'] - 1);
                                
                                //---SS---
                                if($relation->relation_type == 1){
                                    $act_start_new      =   date('Y-m-d',strtotime(
                                                                Yii::$app->helper->getDateAfterHoliday($preSchStartDate,$projectid,$activity['lag'])
                                                            ));
                                    $act_end_new        =   date('Y-m-d',strtotime(
                                                                Yii::$app->helper->getDateAfterHoliday($act_start_new,$projectid,$act_old_dur)
                                                            ));

                                    $actualStartNew     =   date('Y-m-d',strtotime(
                                                                Yii::$app->helper->getDateAfterHoliday($preActlStartDate,$projectid,$activity['lag'])
                                                            ));
                                    $actualEndNew       =   date('Y-m-d',strtotime(
                                                                Yii::$app->helper->getDateAfterHoliday($actualStartNew,$projectid,$act_duration)
                                                            ));
                                    //---Reported----                        
                                    if($activity['act_start_date'] !='' && $activity['act_start_date'] != '0000-00-00'){
                                        $actualStartNew     =   $activity['act_start_date'];
                                        $actualEndTemp      =   date('Y-m-d',strtotime(
                                                                    Yii::$app->helper->getDateAfterHoliday($actualStartNew,$projectid,$act_duration)
                                                                ));
                                        $actualEndNew       =   (strtotime($actualEndTemp) > strtotime($actualEndNew)) ? $actualEndTemp : $actualEndNew;
                                    }
                                }
                                //---FS ----
                                elseif($relation->relation_type == 2){
                                    //$lag                =   ($activity['lag']) ? $activity['lag'] : 1;
                                    $lag                =   $activity['lag'] + 1;
                                    $act_start_new      =   date('Y-m-d',strtotime(
                                                                Yii::$app->helper->getDateAfterHoliday($preSchEndDate,$projectid,$lag)
                                                            ));
                                    $act_end_new        =   date('Y-m-d',strtotime(
                                                                Yii::$app->helper->getDateAfterHoliday($act_start_new,$projectid,$act_old_dur)
                                                            ));

                                    $actualStartNew     =   date('Y-m-d',strtotime(
                                                                Yii::$app->helper->getDateAfterHoliday($preActlEndDate,$projectid,$lag)
                                                            ));
                                    $actualEndNew       =   date('Y-m-d',strtotime(
                                                                Yii::$app->helper->getDateAfterHoliday($actualStartNew,$projectid,$act_duration)
                                                            ));
                                    //---Reported----                        
                                    if($activity['act_start_date'] !='' && $activity['act_start_date'] != '0000-00-00'){
                                        $actualStartNew     =   $activity['act_start_date'];
                                        $actualEndNew      =   date('Y-m-d',strtotime(
                                                                    Yii::$app->helper->getDateAfterHoliday($actualStartNew,$projectid,$act_duration)
                                                                ));
                                    }
                                }
                                //---FF---
                                elseif($relation->relation_type == 3){
                                    $act_end_new        =   date('Y-m-d',strtotime(
                                                                Yii::$app->helper->getDateAfterHoliday($preSchEndDate,$projectid,$activity['lag'])
                                                            ));
                                    $act_start_new      =   date('Y-m-d',strtotime(
                                                                Yii::$app->helper->getDateAfterHoliday($act_end_new,$projectid,$act_old_dur,'previous')
                                                            ));
                                    $actualEndNew       =   date('Y-m-d',strtotime(
                                                                Yii::$app->helper->getDateAfterHoliday($preActlEndDate,$projectid,$activity['lag'])
                                                            ));
                                    $actualStartNew     =   date('Y-m-d',strtotime(
                                                                Yii::$app->helper->getDateAfterHoliday($actualEndNew,$projectid,$act_duration,'previous')
                                                            ));
                                    //---Reported----                        
                                    if($activity['act_start_date'] !='' && $activity['act_start_date'] != '0000-00-00'){
                                        $actualStartNew     =   $activity['act_start_date'];
                                        $actualEndTemp      =   date('Y-m-d',strtotime(
                                                                    Yii::$app->helper->getDateAfterHoliday($actualStartNew,$projectid,$act_duration)
                                                                ));
                                        $actualEndNew       =   (strtotime($actualEndTemp) > strtotime($actualEndNew)) ? $actualEndTemp : $actualEndNew;
                                    }
                                }

                                if(!$actualEndNew || (strtotime($actualEndNew) > strtotime($actualEndNewTemp)) ){
                                    $schActivity                    = Scheduleactivities::findOne($activity['id']);
                                    $schActivity->actual_start_date = $act_start_new;
                                    $schActivity->actual_end_date   = $act_end_new;
                                    $schActivity->start_date        = $actualStartNew;
                                    $schActivity->end_date          = $actualEndNew;
                                    $schActivity->save(false);
                                    $actualEndNewTemp               = $actualEndNew;
                                }

                            }


                        }
                        else{
                            $act_old_dur        =   ($activity['old_duration'] - 1);
                            $act_duration       =   ($activity['duration'] - 1);

                            $act_start_new      =   date('Y-m-d',strtotime(
                                                        Yii::$app->helper->getDateAfterHoliday($activity['actual_start_date'],$projectid)
                                                    ));
                            $act_end_new        =   date('Y-m-d',strtotime(
                                                        Yii::$app->helper->getDateAfterHoliday($act_start_new,$projectid,$act_old_dur)
                                                    ));
                            $actualStartNew     =   ($activity['act_start_date'] !='' && $activity['act_start_date'] != '0000-00-00') 
                                                    ? $activity['act_start_date'] : (
                                                        date('Y-m-d',strtotime(
                                                            Yii::$app->helper->getDateAfterHoliday($activity['start_date'],$projectid)
                                                        ))
                                                    );
                            $actualEndNew       =   date('Y-m-d',strtotime(
                                                        Yii::$app->helper->getDateAfterHoliday($actualStartNew,$projectid,$act_duration)
                                                    ));

                            $schActivity                    = Scheduleactivities::findOne($activity['id']);
                            if(!$schActivity->primavera_id){
                                $schActivity->actual_start_date = $act_start_new;
                                $schActivity->actual_end_date   = $act_end_new;
                            }
                            $schActivity->start_date        = $actualStartNew;
                            $schActivity->end_date          = $actualEndNew;
                            $schActivity->save(false);
                        }

                        /*if($activity['id'] == 1799){
                            echo 'actualEndTemp - '.$actualEndTemp;
                            echo "<br>";
                            echo 'actualEndNew - '.$actualEndNew;
                            echo "<br>";
                        }*/

                    }
                    //---------------------------------------------------


                }

            }
        }
        //$this->GetRelationcorrectfinal($projectid);
    }

   

    public $finalDate = '';
    public $durationCnt = 0;

    public function getDateAfterHoliday($date, $projectId, $duration = 0, $direction = 'next'){
        $date = date('j-n-Y', strtotime($date));
        $holiday_dates = [];
        $holiday_weeks = [];
        if($holiday = Holidays::find()->where(['project_id'=>$projectId])->one()){
           $holiday_dates = explode(",", $holiday->dates); 
           $holiday_weeks = explode(",", $holiday->weeks); 
        }
        $this->durationCnt = 0;
        return $this->getWorkingDay($date, $holiday_dates, $holiday_weeks, $duration, $direction);
    }

    function getWorkingDay($dateToCheck, $datesArr, $weeksArr, $duration, $direction){
        if($direction == 'next')         $nextDate = date('j-n-Y', strtotime($dateToCheck. ' +1 day'));
        elseif($direction == 'previous') $nextDate = date('j-n-Y', strtotime($dateToCheck. ' -1 day'));
        
        if(($datesArr && in_array($dateToCheck, $datesArr)) || ($weeksArr && in_array(date('w', strtotime($dateToCheck)), $weeksArr))){
            return $this->getWorkingDay($nextDate, $datesArr, $weeksArr, $duration, $direction);
        }
        else if($duration > $this->durationCnt){
            $this->durationCnt++;
            return $this->getWorkingDay($nextDate, $datesArr, $weeksArr, $duration, $direction);
        }
        else{
            $this->finalDate = $dateToCheck;
        }
        return $this->finalDate;
    }

    public function getHolidayCount($startDate, $endDate, $projectId){
        $holiday_dates = [];
        $holiday_weeks = [];
        $holidayCnt = 0;
        if($holiday = Holidays::find()->where(['project_id'=>$projectId])->one()){
           $holiday_dates = explode(",", $holiday->dates); 
           $holiday_weeks = explode(",", $holiday->weeks); 

            $nextDate   = date('j-n-Y', strtotime($startDate. ' +1 day'));

            /*echo "startDate - ".$startDate;
            echo "<br>";
            echo "endDate - ".$endDate;*/
            while (strtotime($endDate) > strtotime($nextDate)) {
                if( ($holiday_dates && in_array($nextDate,$holiday_dates)) || 
                    ($holiday_weeks && in_array(date('w', strtotime($nextDate)),$holiday_weeks))
                  )
                    $holidayCnt++;
                $nextDate = date('j-n-Y', strtotime($nextDate. ' +1 day'));
            }
        }
        return $holidayCnt;
    }

    public function isMobile(){
        // Check if the "mobile" word exists in User-Agent 
        $isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile")); 
        if($isMob) return 1;
        else       return 0;
    }

    public function changeStrCase($str, $upper = 0){
        return ($upper == 0) ? strtolower($str) : strtoupper($str);
    }

    public function vendorledgerbalance($place,$accountid)
    {
        //echo " acnt: ".$accountid;exit;
        $connection = Yii::$app->db;
        $initialdate='2013-04-01';
        $enddate=date('Y-m-d');
        /*$sql1="(SELECT id,account_id,creditacnt,amount,narration,type,contra FROM voucher WHERE account_id='".$accountid."' AND place='12' AND project_id='".$place."' AND type='Payment' AND date BETWEEN '$initialdate' AND '$enddate')
                UNION
                (SELECT id,debitacnt AS account_id,creditacnt,amount,narration,type,contra FROM journalvoucher WHERE debitacnt='".$accountid."' AND project_id='12' AND place='".$place."' AND date BETWEEN '$initialdate' AND '$enddate')";*/
        $sql1="(SELECT id,account_id,creditacnt,amount,narration,type,contra FROM voucher WHERE account_id='".$accountid."' AND type='Payment' AND date BETWEEN '$initialdate' AND '$enddate')
                UNION
                (SELECT id,debitacnt AS account_id,creditacnt,amount,narration,type,contra FROM journalvoucher WHERE debitacnt='".$accountid."' AND date BETWEEN '$initialdate' AND '$enddate')";
        //echo $sql1;exit;
        $command=$connection->createCommand($sql1);
        $dataReader=$command->query();
        $databalance=$dataReader->readAll();
        if(count($databalance)>0):
            $debittotal=0;
            foreach($databalance AS $databal):
                if($databal['contra']==0):
                    $debittotal=$debittotal + $databal['amount'];
                else:
                    $debittotal=$debittotal + $databal['amount'];
                endif;
            endforeach;
            $acountbal=$debittotal;
        else:
            $acountbal=0;
        endif;
        return $acountbal;
    }

    

}



?>