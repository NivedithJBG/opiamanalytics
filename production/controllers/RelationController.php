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

        /* WBS/IOW groups that have scheduled activities. scheduleactivities.
           scheduleitem_id references wbsscheduleitems.scheduleitem_id, NOT
           workgroups_new.Workgroup_Id directly — wbsscheduleitems.wbsid is
           the actual FK to workgroups_new. Skipping that hop (the previous
           bug here) meant this EXISTS check could never match, so newly
           added IOWs/activities never appeared as selectable in the
           Relationships window despite being live on the Gantt chart. */
        $groups = $db->createCommand("
            SELECT wn.Workgroup_Id, wn.Name AS iow_name, ig.name AS iow_group_name
            FROM workgroups_new wn
            LEFT JOIN iow_groups ig ON ig.id = wn.iowGroupid AND ig.status = 0
            WHERE wn.Project_Id = :pid AND wn.Status = 0
              AND EXISTS (
                  SELECT 1 FROM wbsscheduleitems wsi
                  JOIN scheduleactivities sa ON sa.scheduleitem_id = wsi.scheduleitem_id
                  WHERE wsi.wbsid = wn.Workgroup_Id
                    AND wsi.status = 0
                    AND sa.status = 0
                    AND sa.actual_start_date IS NOT NULL
                    AND sa.actual_start_date != '0000-00-00'
              )
            ORDER BY wn.sortorder ASC, wn.Workgroup_Id ASC
        ", [':pid' => $projectId])->queryAll();

        /* Activities per group — scheduleitem_id here is remapped to the
           workgroups_new.Workgroup_Id (via wbsscheduleitems.wbsid) so the
           frontend can match each activity to a group from the list above
           using the same key. */
        $activities = $db->createCommand("
            SELECT sa.id, sa.name, wsi.wbsid AS scheduleitem_id
            FROM scheduleactivities sa
            JOIN wbsscheduleitems wsi ON wsi.scheduleitem_id = sa.scheduleitem_id AND wsi.status = 0
            WHERE sa.projectId = :pid AND sa.status = 0
              AND sa.actual_start_date IS NOT NULL
              AND sa.actual_start_date != '0000-00-00'
            ORDER BY wsi.wbsid ASC, sa.id ASC
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

        $inserted = $this->saveRelationRows($projectId, $rows);
        return json_encode(['error' => 'No', 'inserted' => $inserted]);
    }

    /* Shared row-insert logic used by actionSave() (batch, from the Add
       form) and actionCreatefromtemplate() (single row, from the
       historical-template reuse flow) — same duplicate-check, date-seed,
       and CPM-recalc behavior regardless of caller. */
    private function saveRelationRows($projectId, $rows)
    {
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

            /* Seed the precedent's baseline if it has none yet — otherwise
               RecalculatePlannedDates() has no anchor for it. Planned only;
               Actual is untouched (GetRelationcorrect owns that). */
            if (!$precedentAct->act_start_date || $precedentAct->act_start_date == '0000-00-00') {
                $afterHoliday = Yii::$app->helper->getDateAfterHoliday(date('d-m-Y'), $projectId);
                $precedentAct->act_start_date = date('Y-m-d', strtotime($afterHoliday));
                $precedentAct->old_duration = $precedentAct->old_duration ?: 1;
                $precedentAct->save(false);
            }

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

        /* Adding a relationship is an explicit user action, so Planned
           recalculates (RecalculatePlannedDates) — every affected
           activity's act_start_date moves to reflect the new network.
           GetRelationcorrect() also runs so the page's Actual-date display
           is fresh immediately, without waiting for the next page load. */
        if ($inserted > 0) {
            Yii::$app->helper->RecalculatePlannedDates($projectId);
            Yii::$app->helper->GetRelationcorrect($projectId);
        }

        return $inserted;
    }

    /* List all active relationships for a project with names */
    public function actionList()
    {
        $projectId = (int)$_POST['projectid'];
        if (!$projectId) return json_encode(['error' => 'Yes', 'errortext' => 'No project.']);

        $rows = \Yii::$app->db->createCommand("
            SELECT ar.id,
                   ar.precedent_activity, sa_p.name AS prec_name,
                   wn_p.Name AS prec_iow, wsi_p.wbsid AS precedent_schedule_item,
                   ar.dependent_activity, sa_d.name AS dep_name,
                   wn_d.Name AS dep_iow, wsi_d.wbsid AS dependent_schedule_item,
                   ar.relation_type, ar.lag_days
            FROM activity_relations ar
            JOIN scheduleactivities sa_p ON sa_p.id = ar.precedent_activity
            JOIN scheduleactivities sa_d ON sa_d.id = ar.dependent_activity
            LEFT JOIN wbsscheduleitems wsi_p ON wsi_p.scheduleitem_id = sa_p.scheduleitem_id AND wsi_p.status = 0
            LEFT JOIN workgroups_new wn_p ON wn_p.Workgroup_Id = wsi_p.wbsid
            LEFT JOIN wbsscheduleitems wsi_d ON wsi_d.scheduleitem_id = sa_d.scheduleitem_id AND wsi_d.status = 0
            LEFT JOIN workgroups_new wn_d ON wn_d.Workgroup_Id = wsi_d.wbsid
            WHERE ar.projectId = :pid AND ar.status = 0
            ORDER BY ar.id ASC
        ", [':pid' => $projectId])->queryAll();

        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    /* Lists distinct historical relationship "templates" — every unique
       (precedent library activity, dependent library activity, type)
       combination ever created, across ALL projects, not just the
       current one. Matched via workgroup_activities_new.activity_Id,
       the stable library activity id (estimateactivities.activity_id),
       since scheduleactivities/workgroup_activities_new rows themselves
       are per-project instances and never repeat across projects.
       Feeds the Relationships List popup's search-and-tick reuse flow:
       the user searches this catalog and ticks a row to recreate that
       same pairing for the CURRENT project (see actionCreatefromtemplate). */
    public function actionListtemplates()
    {
        $search = trim($_POST['search'] ?? '');

        $sql = "
            SELECT MIN(ar.id) AS id,
                   wa_p.activity_Id AS prec_lib_id, ea_p.activity_name AS prec_name,
                   wa_d.activity_Id AS dep_lib_id,  ea_d.activity_name AS dep_name,
                   ar.relation_type, ar.lag_days
            FROM activity_relations ar
            JOIN scheduleactivities sa_p ON sa_p.id = ar.precedent_activity
            JOIN scheduleactivities sa_d ON sa_d.id = ar.dependent_activity
            JOIN workgroup_activities_new wa_p ON wa_p.id = sa_p.activity_id
            JOIN workgroup_activities_new wa_d ON wa_d.id = sa_d.activity_id
            JOIN estimateactivities ea_p ON ea_p.activity_id = wa_p.activity_Id
            JOIN estimateactivities ea_d ON ea_d.activity_id = wa_d.activity_Id
            WHERE ar.status = 0
        ";
        $params = [];
        if ($search !== '') {
            $sql .= " AND (ea_p.activity_name LIKE :s OR ea_d.activity_name LIKE :s)";
            $params[':s'] = '%' . $search . '%';
        }
        $sql .= " GROUP BY wa_p.activity_Id, wa_d.activity_Id, ar.relation_type
                  ORDER BY ea_p.activity_name ASC
                  LIMIT 200";

        $rows = \Yii::$app->db->createCommand($sql, $params)->queryAll();
        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    /* Recreates a historical template relationship for the CURRENT
       project: resolves both library activity ids to THEIR live
       scheduleactivities row in this project (via workgroup_activities_new
       .activity_Id), then saves exactly like actionSave()'s single-row
       path. Blocks with a clear message if either activity isn't
       scheduled in this project yet, rather than silently failing. */
    public function actionCreatefromtemplate()
    {
        $projectId = (int)$_POST['projectid'];
        $precLibId = (int)$_POST['prec_lib_id'];
        $depLibId  = (int)$_POST['dep_lib_id'];
        $relType   = (int)$_POST['relation_type'];
        $lagDays   = (int)($_POST['lag_days'] ?? 0);
        if (!$projectId || !$precLibId || !$depLibId) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Missing data.']);
        }

        $db = \Yii::$app->db;
        $findSa = function ($libId) use ($db, $projectId) {
            return $db->createCommand("
                SELECT sa.id, sa.scheduleitem_id, wa.activity_Name AS name
                FROM scheduleactivities sa
                JOIN workgroup_activities_new wa ON wa.id = sa.activity_id
                WHERE wa.activity_Id = :lid AND sa.projectId = :pid AND sa.status = 0
                LIMIT 1
            ", [':lid' => $libId, ':pid' => $projectId])->queryOne();
        };

        $precSa = $findSa($precLibId);
        $depSa  = $findSa($depLibId);
        if (!$precSa) {
            return json_encode(['error' => 'Yes', 'errortext' => 'The Precedent activity isn\'t scheduled in this project yet.']);
        }
        if (!$depSa) {
            return json_encode(['error' => 'Yes', 'errortext' => 'The Dependent activity isn\'t scheduled in this project yet.']);
        }

        $precWbsId = $db->createCommand(
            "SELECT wbsid FROM wbsscheduleitems WHERE scheduleitem_id=:si AND status=0 LIMIT 1",
            [':si' => $precSa['scheduleitem_id']]
        )->queryScalar();
        $depWbsId = $db->createCommand(
            "SELECT wbsid FROM wbsscheduleitems WHERE scheduleitem_id=:si AND status=0 LIMIT 1",
            [':si' => $depSa['scheduleitem_id']]
        )->queryScalar();

        $row = [
            'precedent_activity'      => (int)$precSa['id'],
            'dependent_activity'      => (int)$depSa['id'],
            'precedent_schedule_item' => (int)$precWbsId,
            'dependent_schedule_item' => (int)$depWbsId,
            'relation_type'           => $relType ?: 2,
            'lag_days'                => $lagDays,
        ];

        $inserted = $this->saveRelationRows($projectId, [$row]);
        if ($inserted > 0) {
            return json_encode(['error' => 'No', 'inserted' => $inserted]);
        }
        return json_encode(['error' => 'Yes', 'errortext' => 'This relationship already exists in this project.']);
    }

    /* Edits an existing relationship in place — which two activities are
       linked, the relation type, and the lag/lead. Same date-shifting
       logic actionSave() applies to a newly-created row, re-applied here
       since changing the precedent/type/lag all affect the dependent's
       computed start/end date; then the CPM network is recalculated the
       same way actionSave()/actionDelete() already do. */
    public function actionUpdate()
    {
        $id        = (int)$_POST['id'];
        $projectId = (int)$_POST['projectid'];
        if (!$id) return json_encode(['error' => 'Yes', 'errortext' => 'No id.']);

        $model = \app\models\ActivityRelations::findOne($id);
        if (!$model || (int)$model->projectId !== $projectId) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Not found.']);
        }

        $precAct  = (int)($_POST['precedent_activity'] ?? 0);
        $depAct   = (int)($_POST['dependent_activity'] ?? 0);
        $precItem = (int)($_POST['precedent_schedule_item'] ?? 0);
        $depItem  = (int)($_POST['dependent_schedule_item'] ?? 0);
        $relType  = (int)($_POST['relation_type'] ?? 0);
        $lagDays  = (int)($_POST['lag_days'] ?? 0);
        if (!$precAct || !$depAct || $precAct === $depAct) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Select both a Precedent and a Dependent activity.']);
        }

        $precedentAct = \app\models\Scheduleactivities::findOne($precAct);
        $dependentAct = \app\models\Scheduleactivities::findOne($depAct);
        if (!$precedentAct || !$dependentAct) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Activity not found.']);
        }

        /* Seed the precedent's Planned baseline if it has none yet —
           RecalculatePlannedDates() below needs an anchor for it. */
        if (!$precedentAct->act_start_date || $precedentAct->act_start_date == '0000-00-00') {
            $afterHoliday = Yii::$app->helper->getDateAfterHoliday(date('d-m-Y'), $projectId);
            $precedentAct->act_start_date = date('Y-m-d', strtotime($afterHoliday));
            $precedentAct->old_duration = $precedentAct->old_duration ?: 1;
            $precedentAct->save(false);
        }

        $model->precedent_activity      = $precAct;
        $model->dependent_activity      = $depAct;
        $model->precedent_schedule_item = $precItem;
        $model->dependent_schedule_item = $depItem;
        $model->relation_type           = $relType;
        $model->lag_days                = $lagDays;
        if (!$model->save(false)) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Some error occurred while saving.']);
        }

        /* Editing a relationship is an explicit user action — Planned
           recalculates for every activity the change affects. Actual also
           refreshes immediately for the current page view. */
        Yii::$app->helper->RecalculatePlannedDates($projectId);
        Yii::$app->helper->GetRelationcorrect($projectId);

        return json_encode(['error' => 'No']);
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

        /* Deleting a relationship is an explicit user action — Planned
           recalculates so the dependent's baseline is no longer derived
           from a relation that no longer exists. */
        Yii::$app->helper->RecalculatePlannedDates($model->projectId);
        Yii::$app->helper->GetRelationcorrect($model->projectId);

        return json_encode(['error' => 'No']);
    }
}
