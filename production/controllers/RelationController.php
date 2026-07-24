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

    /* Batch-insert relationships */
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

            /* The CPM engine (HelperComponent::GetRelationcorrect) reads lag
               from scheduleactivities.lag on the dependent activity, not from
               activity_relations — keep it in sync so the lag entered here
               actually shifts the calculated schedule dates. */
            if ($lagDays > 0) {
                $db->createCommand()->update('scheduleactivities', ['lag' => $lagDays], ['id' => $depAct])->execute();
            }
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

    /* Soft-delete a relationship */
    public function actionDelete()
    {
        $id        = (int)$_POST['id'];
        $projectId = (int)$_POST['projectid'];
        if (!$id) return json_encode(['error' => 'Yes', 'errortext' => 'No id.']);

        \Yii::$app->db->createCommand()->update('activity_relations',
            ['status' => 1],
            ['id' => $id, 'projectId' => $projectId]
        )->execute();

        return json_encode(['error' => 'No']);
    }
}
