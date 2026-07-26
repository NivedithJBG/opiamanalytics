<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

class RelationController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /* Returns all IOW groups + their activities for the active project's Gantt.
       Only activities that are scheduled (have actual_start_date) are included. */
    public function actionGetganttactivities()
    {
        $projectId = (int)$_POST['projectid'];
        if (!$projectId) return json_encode(['error' => 'Yes', 'errortext' => 'No project.']);

        $db = \Yii::$app->db;

        /* WBS/IOW groups that have scheduled activities */
        $groups = $db->createCommand("
            SELECT wn.Workgroup_Id, wn.Name AS iow_name, ig.name AS iow_group_name
            FROM workgroups_new wn
            LEFT JOIN iow_groups ig ON ig.id = wn.iowGroupid AND ig.status = 0
            WHERE wn.Project_Id = :pid AND wn.Status = 0
              AND EXISTS (
                  SELECT 1 FROM scheduleactivities sa
                  WHERE sa.scheduleitem_id = wn.Workgroup_Id
                    AND sa.status = 0
                    AND sa.actual_start_date IS NOT NULL
                    AND sa.actual_start_date != '0000-00-00'
              )
            ORDER BY wn.sortorder ASC, wn.Workgroup_Id ASC
        ", [':pid' => $projectId])->queryAll();

        /* Activities per group */
        $activities = $db->createCommand("
            SELECT sa.id, sa.name, sa.scheduleitem_id
            FROM scheduleactivities sa
            WHERE sa.projectId = :pid AND sa.status = 0
              AND sa.actual_start_date IS NOT NULL
              AND sa.actual_start_date != '0000-00-00'
            ORDER BY sa.scheduleitem_id ASC, sa.id ASC
        ", [':pid' => $projectId])->queryAll();

        return json_encode(['error' => 'No', 'groups' => $groups, 'activities' => $activities]);
    }

    /* Batch-insert relationships.
       Ported from ProjectsmainController::actionSaverelation() — same seed-date
       logic before GetRelationcorrect(), because the CPM engine anchors off
       act_start_date/start_date and silently skips activities that have neither. */
    public function actionSave()
    {
        $projectId = (int)$_POST['projectid'];
        $rows      = isset($_POST['rows']) ? $_POST['rows'] : [];
        if (!$projectId || empty($rows)) {
            return json_encode(['error' => 'Yes', 'errortext' => 'No data.']);
        }

        $db = \Yii::$app->db;
        $inserted = 0;
        foreach ($rows as $r) {
            $precAct  = (int)$r['precedent_activity'];
            $depAct   = (int)$r['dependent_activity'];
            $precItem = (int)$r['precedent_schedule_item'];
            $depItem  = (int)$r['dependent_schedule_item'];
            $relType  = (int)$r['relation_type'];   /* 1=SS 2=FS 3=FF */
            $lagDays  = (int)$r['lag_days'];
            if (!$precAct || !$depAct || $precAct === $depAct) continue;

            /* Avoid exact duplicates */
            $exists = $db->createCommand("
                SELECT COUNT(*) FROM activity_relations
                WHERE precedent_activity = :pa AND dependent_activity = :da
                  AND relation_type = :rt AND projectId = :pid AND status = 0
            ", [':pa'=>$precAct,':da'=>$depAct,':rt'=>$relType,':pid'=>$projectId])->queryScalar();
            if ($exists) continue;

            $precedentAct = \app\models\Scheduleactivities::findOne($precAct);
            $dependentAct = \app\models\Scheduleactivities::findOne($depAct);
            if (!$precedentAct || !$dependentAct) continue;

            /* Seed the precedent's dates if it has none yet — otherwise the CPM
               engine has no anchor and drops it from the network. */
            if ($precedentAct->start_date == '' && $precedentAct->end_date == '') {
                $afterHoliday = Yii::$app->helper->getDateAfterHoliday(date('d-m-Y'), $projectId);
                $today = date('Y-m-d', strtotime($afterHoliday));
                $precedentAct->start_date        = $today;
                $precedentAct->end_date          = $today;
                $precedentAct->actual_start_date = $today;
                $precedentAct->actual_end_date   = $today;
                $precedentAct->duration          = 1;
                $precedentAct->save(false);
            }

            /* Preserve original baseline before the relation shifts the start date */
            if (!$dependentAct->act_start_date || $dependentAct->act_start_date == '0000-00-00') {
                $dependentAct->act_start_date = $dependentAct->start_date ?: date('Y-m-d');
            }

            $dependentAct->start_date = date('Y-m-d', strtotime(
                Yii::$app->helper->getDateAfterHoliday($precedentAct->start_date, $projectId, $lagDays)
            ));
            $dependentAct->end_date = date('Y-m-d', strtotime(
                Yii::$app->helper->getDateAfterHoliday($precedentAct->start_date, $projectId, ($dependentAct->duration + $lagDays))
            ));
            $dependentAct->lag = $lagDays;
            $dependentAct->save(false);

            if ($precedentAct->start_date == '' || $precedentAct->end_date == '') continue;

            $db->createCommand()->insert('activity_relations', [
                'precedent_activity'     => $precAct,
                'dependent_activity'     => $depAct,
                'precedent_schedule_item'=> $precItem,
                'dependent_schedule_item'=> $depItem,
                'relation_type'          => $relType,
                'lag_days'               => $lagDays,
                'projectId'              => $projectId,
                'status'                 => 0,
                'wbs_structure_id'       => 0,
                'wbs_schedule_status'    => 0,
                'sortorder'              => 0,
            ])->execute();
            $inserted++;
        }

        /* Recalculate the full CPM network once after all rows are saved,
           same engine the legacy Project Schedule tab uses. */
        if ($inserted > 0) {
            Yii::$app->helper->GetRelationcorrect($projectId);
        }

        return json_encode(['error' => 'No', 'inserted' => $inserted]);
    }

    /* List all active relationships for a project with names */
    public function actionList()
    {
        $projectId = (int)$_POST['projectid'];
        if (!$projectId) return json_encode(['error' => 'Yes', 'errortext' => 'No project.']);

        $rows = \Yii::$app->db->createCommand("
            SELECT ar.id,
                   ar.precedent_activity, sa_p.name AS prec_name,
                   wn_p.Name AS prec_iow,
                   ar.dependent_activity, sa_d.name AS dep_name,
                   wn_d.Name AS dep_iow,
                   ar.relation_type, ar.lag_days
            FROM activity_relations ar
            JOIN scheduleactivities sa_p ON sa_p.id = ar.precedent_activity
            JOIN scheduleactivities sa_d ON sa_d.id = ar.dependent_activity
            LEFT JOIN workgroups_new wn_p ON wn_p.Workgroup_Id = sa_p.scheduleitem_id
            LEFT JOIN workgroups_new wn_d ON wn_d.Workgroup_Id = sa_d.scheduleitem_id
            WHERE ar.projectId = :pid AND ar.status = 0
            ORDER BY ar.id ASC
        ", [':pid' => $projectId])->queryAll();

        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    /* Soft-delete a relationship.
       Ported from ProjectsmainController::actionDeleterelation() — same
       critical-flag reset + lag reset before recalculating. */
    public function actionDelete()
    {
        $id        = (int)$_POST['id'];
        $projectId = (int)$_POST['projectid'];
        if (!$id) return json_encode(['error' => 'Yes', 'errortext' => 'No id.']);

        $model = \app\models\ActivityRelations::findOne($id);
        if (!$model) return json_encode(['error' => 'Yes', 'errortext' => 'Not found.']);

        $model->status = 1;
        if (!$model->save(false)) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Some error occurred while saving.']);
        }

        $precedentAct = \app\models\Scheduleactivities::findOne($model->precedent_activity);
        if ($precedentAct) {
            $precedentAct->critical_start = '0000-00-00';
            $precedentAct->critical_end   = '0000-00-00';
            $precedentAct->save(false);
        }

        $dependentAct = \app\models\Scheduleactivities::findOne($model->dependent_activity);
        if ($dependentAct) {
            $dependentAct->lag = 0;
            $dependentAct->save(false);
        }

        Yii::$app->helper->GetRelationcorrect($model->projectId);

        return json_encode(['error' => 'No']);
    }
}
