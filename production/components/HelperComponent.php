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


    private function getCpmProjDays($activityId, $quantity)
    {
        $connection = Yii::$app->db;
        $row = $connection->createCommand("
            SELECT spr.cumulated_qty,
                   spr.start_date AS act_start,
                   COALESCE(MAX(sprl.report_date), spr.updated_at) AS last_report
            FROM schedule_progress_report spr
            LEFT JOIN schedule_progress_report_log sprl
                   ON sprl.activity_id = spr.activity_id AND sprl.currentqty > 0
            WHERE spr.activity_id = " . (int)$activityId . "
            GROUP BY spr.id
        ")->queryOne();

        if (!$row || !$row['cumulated_qty'] || !$row['act_start']) {
            $planned = $connection->createCommand(
                "SELECT old_duration FROM scheduleactivities WHERE id = :aid", [':aid' => (int)$activityId]
            )->queryOne();
            return ($planned && $planned['old_duration'] > 0) ? (float)$planned['old_duration'] : 0;
        }

        $elapsed = max(1, (strtotime($row['last_report']) - strtotime($row['act_start'])) / 86400);
        return ($elapsed / (float)$row['cumulated_qty']) * (float)$quantity;
    }

    public function GetRelationcorrect($projectid)
    {
        $connection = Yii::$app->db;

        // ─── PASS A: Seed projected durations and initial start dates ────────
        $activities = Scheduleactivities::find()
            ->where(['projectId' => $projectid, 'status' => 0])
            ->orderBy(['scheduleitem_id' => SORT_ASC, 'sortorder' => SORT_ASC])
            ->all();

        if (empty($activities)) return;

        foreach ($activities as $act) {
            $projDays = $this->getCpmProjDays($act->id, $act->quantity);

            $sprRow = $connection->createCommand(
                "SELECT start_date FROM schedule_progress_report WHERE activity_id = " . (int)$act->id . " LIMIT 1"
            )->queryOne();

            if ($sprRow && $sprRow['start_date'] && $sprRow['start_date'] != '0000-00-00') {
                $cpmStart = $sprRow['start_date'];
            } elseif ($act->act_start_date && $act->act_start_date != '0000-00-00') {
                // act_start_date is the immutable original baseline — never overwritten by CPM
                $cpmStart = $act->act_start_date;
            } elseif ($act->start_date && $act->start_date != '0000-00-00') {
                $cpmStart = $act->start_date;
                // Capture baseline on first CPM run so future runs always use original date
                $connection->createCommand(
                    "UPDATE scheduleactivities SET act_start_date = :d WHERE id = :id AND (act_start_date IS NULL OR act_start_date = '0000-00-00')",
                    [':d' => $cpmStart, ':id' => $act->id]
                )->execute();
            } else {
                $cpmStart = date('Y-m-d');
            }

            $connection->createCommand("
                INSERT INTO activity_cpm_dates (activity_id, project_id, proj_duration, cpm_start, cpm_end)
                VALUES (:aid, :pid, :dur, :start, NULL)
                ON DUPLICATE KEY UPDATE
                    proj_duration = VALUES(proj_duration),
                    cpm_start     = VALUES(cpm_start),
                    cpm_end       = NULL
            ", [':aid' => $act->id, ':pid' => $projectid, ':dur' => $projDays, ':start' => $cpmStart])->execute();
        }

        // ─── PASS B: Iterative topological scheduling ─────────────────────────
        $allRelations = ActivityRelations::find()
            ->where(['projectId' => $projectid, 'status' => 0])
            ->all();

        $dependsOn = [];
        foreach ($allRelations as $rel) {
            $dependsOn[$rel->dependent_activity][] = $rel;
        }

        $activityIds = array_map(function($a) { return $a->id; }, $activities);
        $maxPasses   = count($activityIds) + 1;

        for ($pass = 0; $pass < $maxPasses; $pass++) {
            $scheduled = 0;

            foreach ($activityIds as $actId) {
                $cpmRow = $connection->createCommand(
                    "SELECT proj_duration, cpm_start, cpm_end FROM activity_cpm_dates WHERE activity_id = :aid AND project_id = :pid",
                    [':aid' => $actId, ':pid' => $projectid]
                )->queryOne();

                if (!$cpmRow || $cpmRow['cpm_end'] !== null) continue;

                $projDays  = (float)$cpmRow['proj_duration'];
                $baseStart = $cpmRow['cpm_start'] ?: date('Y-m-d');
                $effDur    = max(0, round($projDays) - 1);

                if (empty($dependsOn[$actId])) {
                    $cpmEnd = date('Y-m-d', strtotime(
                        $this->getDateAfterHoliday($baseStart, $projectid, $effDur)
                    ));
                    $connection->createCommand(
                        "UPDATE activity_cpm_dates SET cpm_start = :s, cpm_end = :e WHERE activity_id = :aid AND project_id = :pid",
                        [':s' => $baseStart, ':e' => $cpmEnd, ':aid' => $actId, ':pid' => $projectid]
                    )->execute();
                    $scheduled++;
                } else {
                    $latestFS = null;
                    $latestSS = null;
                    $latestFF = null;
                    $allDone  = true;

                    // Lag is stored on the dependent activity in scheduleactivities, not on the relation
                    $actLagRow = $connection->createCommand(
                        "SELECT lag FROM scheduleactivities WHERE id = :aid", [':aid' => $actId]
                    )->queryOne();
                    $actLag = $actLagRow ? (int)$actLagRow['lag'] : 0;

                    foreach ($dependsOn[$actId] as $rel) {
                        $precRow = $connection->createCommand(
                            "SELECT cpm_start, cpm_end FROM activity_cpm_dates WHERE activity_id = :aid AND project_id = :pid",
                            [':aid' => $rel->precedent_activity, ':pid' => $projectid]
                        )->queryOne();

                        if (!$precRow || $precRow['cpm_end'] === null) {
                            $allDone = false;
                            break;
                        }

                        $lag = $actLag;

                        if ($rel->relation_type == 2) { // FS
                            $candidate = date('Y-m-d', strtotime(
                                $this->getDateAfterHoliday($precRow['cpm_end'], $projectid, $lag + 1)
                            ));
                            if ($latestFS === null || $candidate > $latestFS) $latestFS = $candidate;
                        } elseif ($rel->relation_type == 1) { // SS
                            $candidate = date('Y-m-d', strtotime(
                                $this->getDateAfterHoliday($precRow['cpm_start'], $projectid, $lag)
                            ));
                            if ($latestSS === null || $candidate > $latestSS) $latestSS = $candidate;
                        } elseif ($rel->relation_type == 3) { // FF
                            $candidate = date('Y-m-d', strtotime(
                                $this->getDateAfterHoliday($precRow['cpm_end'], $projectid, $lag)
                            ));
                            if ($latestFF === null || $candidate > $latestFF) $latestFF = $candidate;
                        }
                    }

                    if (!$allDone) continue;

                    $cpmStart = $baseStart;
                    if ($latestFS !== null && $latestFS > $cpmStart) $cpmStart = $latestFS;
                    if ($latestSS !== null && $latestSS > $cpmStart) $cpmStart = $latestSS;

                    if ($latestFF !== null) {
                        $cpmEnd   = $latestFF;
                        $cpmStart = date('Y-m-d', strtotime(
                            $this->getDateAfterHoliday($cpmEnd, $projectid, $effDur, 'previous')
                        ));
                    } else {
                        $cpmEnd = date('Y-m-d', strtotime(
                            $this->getDateAfterHoliday($cpmStart, $projectid, $effDur)
                        ));
                    }

                    $connection->createCommand(
                        "UPDATE activity_cpm_dates SET cpm_start = :s, cpm_end = :e WHERE activity_id = :aid AND project_id = :pid",
                        [':s' => $cpmStart, ':e' => $cpmEnd, ':aid' => $actId, ':pid' => $projectid]
                    )->execute();
                    $scheduled++;
                }
            }

            if ($scheduled === 0) break;
        }

        // ─── PASS C: Sync back to scheduleactivities ─────────────────────────
        $connection->createCommand("
            UPDATE scheduleactivities sa
            JOIN activity_cpm_dates acd ON acd.activity_id = sa.id AND acd.project_id = sa.projectId
            SET sa.start_date = acd.cpm_start,
                sa.end_date   = acd.cpm_end
            WHERE acd.cpm_end IS NOT NULL AND acd.project_id = :pid
        ", [':pid' => $projectid])->execute();

        // Update actual_start_date/actual_end_date for activities that have no real progress
        // reports yet. Activities with actual reports use spr_start_date for bar positioning
        // so those bars are unaffected even if actual_start_date changes here.
        $connection->createCommand("
            UPDATE scheduleactivities sa
            JOIN activity_cpm_dates acd ON acd.activity_id = sa.id AND acd.project_id = sa.projectId
            LEFT JOIN schedule_progress_report_log sprl ON sprl.activity_id = sa.id
            SET sa.actual_start_date = acd.cpm_start,
                sa.actual_end_date   = acd.cpm_end
            WHERE acd.cpm_end IS NOT NULL AND acd.project_id = :pid
              AND sprl.activity_id IS NULL
        ", [':pid' => $projectid])->execute();
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