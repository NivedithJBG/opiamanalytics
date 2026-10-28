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

        /* IOW Groups that have scheduled activities, for this project —
           scheduleactivities.iow_group_id is the direct FK (no WBS-item
           tier/bridge table in between). */
        $groups = $db->createCommand("
            SELECT go.iow_group_id AS Workgroup_Id, ig.name AS iow_name, ig.name AS iow_group_name
            FROM iow_group_order go
            LEFT JOIN iow_groups ig ON ig.id = go.iow_group_id AND ig.status = 0
            WHERE go.project_id = :pid
              AND EXISTS (
                  SELECT 1 FROM scheduleactivities sa
                  WHERE sa.iow_group_id = go.iow_group_id
                    AND sa.projectId = go.project_id
                    AND sa.status = 0
                    AND sa.actual_start_date IS NOT NULL
                    AND sa.actual_start_date != '0000-00-00'
              )
            ORDER BY COALESCE(go.sortorder, 999999) ASC, go.iow_group_id ASC
        ", [':pid' => $projectId])->queryAll();

        /* Activities per group — scheduleitem_id here (frontend-facing key
           name, unchanged) now holds the activity's iow_group_id directly,
           matching the Workgroup_Id key in the groups list above. */
        // Same order as the Gantt: activities directly under the IOW Group, then
        // each Sub Group in its order; within those, the Gantt's activity order.
        (new \app\controllers\ProjectsmainController('projectsmain', \Yii::$app))->_sgEnsure();
        $activities = $db->createCommand("
            SELECT sa.id, sa.name, sa.iow_group_id AS scheduleitem_id
            FROM scheduleactivities sa
            LEFT JOIN workgroup_activities_new wa ON wa.id = sa.activity_id
            LEFT JOIN wbs_subgroup_activities m ON m.wan_id = sa.activity_id
            LEFT JOIN wbs_subgroups sg ON sg.id = m.subgroup_id AND sg.status = 0
            WHERE sa.projectId = :pid AND sa.status = 0
              AND sa.actual_start_date IS NOT NULL
              AND sa.actual_start_date != '0000-00-00'
            ORDER BY sa.iow_group_id ASC, (sg.id IS NOT NULL) ASC, COALESCE(sg.sortorder, 0) ASC, COALESCE(sg.id, 0) ASC,
                     COALESCE(wa.sortorder, 999999) ASC, sa.id ASC
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
        if ($inserted === 0) {
            return json_encode(['error' => 'Yes', 'errortext' => 'A relationship between these two activities already exists.']);
        }
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

            /* One active relationship per precedent→dependent pair, whatever its type or lag */
            $exists = $db->createCommand("
                SELECT COUNT(*) FROM activity_relations
                WHERE precedent_activity = :pa AND dependent_activity = :da
                  AND projectId = :pid AND status = 0
            ", [':pa'=>$precAct,':da'=>$depAct,':pid'=>$projectId])->queryScalar();
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

    /**
     * Relationship Agent — Task 1 (see docs/relationship-agent.md).
     * Purely mechanical copy, no inference: for every activity currently
     * scheduled in this project, look up its library-level established
     * relationships (activity_library_relations, matched by
     * estimateactivities.activity_id — "library id", not name), and copy
     * a match into activity_relations for THIS project only when both the
     * precedent and dependent activity are actually present in the
     * current Gantt. No new relationship types or lags are invented here
     * — only exact copies of what's already recorded as an established
     * pattern. Matches that don't resolve on both sides are silently
     * skipped (left for Task 2, not yet built).
     */
    public function actionRelationshipagentTask1()
    {
        $projectId = (int)$_POST['projectid'];
        if (!$projectId) return json_encode(['error' => 'Yes', 'errortext' => 'No project.']);

        $db = \Yii::$app->db;

        // Every scheduled activity in this project, with its library id —
        // the join chain already used throughout this controller/
        // ProjectsmainController (sa.activity_id -> workgroup_activities_new.id
        // -> workgroup_activities_new.activity_Id -> estimateactivities.activity_id).
        $rows = $db->createCommand("
            SELECT sa.id AS sa_id, sa.iow_group_id, wa.activity_Id AS lib_id
            FROM scheduleactivities sa
            JOIN workgroup_activities_new wa ON wa.id = sa.activity_id
            WHERE sa.projectId = :pid AND sa.status = 0
        ", [':pid' => $projectId])->queryAll();

        if (empty($rows)) {
            return json_encode(['error' => 'No', 'copied' => 0, 'checked' => 0]);
        }

        // libId -> [sa_id, iow_group_id] — a library activity could in
        // theory appear more than once in the same project; first match
        // wins, same as every other "one bar per activity" assumption
        // this controller already makes elsewhere.
        $saByLibId = [];
        foreach ($rows as $r) {
            $libId = (int)$r['lib_id'];
            if ($libId && !isset($saByLibId[$libId])) {
                $saByLibId[$libId] = ['sa_id' => (int)$r['sa_id'], 'iow_group_id' => (int)$r['iow_group_id']];
            }
        }
        $libIds = array_keys($saByLibId);
        if (empty($libIds)) {
            return json_encode(['error' => 'No', 'copied' => 0, 'checked' => count($rows)]);
        }

        // Every library-level established relationship touching at least
        // one activity in this project, on either side.
        $libIdList = implode(',', array_map('intval', $libIds));
        $libRels = $db->createCommand("
            SELECT precedent_activity_id, dependent_activity_id, relation_type, lag_days
            FROM activity_library_relations
            WHERE status = 0
              AND (precedent_activity_id IN ($libIdList) OR dependent_activity_id IN ($libIdList))
        ")->queryAll();

        $toCopy = [];
        foreach ($libRels as $lr) {
            $precLib = (int)$lr['precedent_activity_id'];
            $depLib  = (int)$lr['dependent_activity_id'];
            // Both sides must be present in THIS project's Gantt — skip otherwise.
            if (!isset($saByLibId[$precLib]) || !isset($saByLibId[$depLib])) continue;

            $prec = $saByLibId[$precLib];
            $dep  = $saByLibId[$depLib];
            $toCopy[] = [
                'precedent_activity'      => $prec['sa_id'],
                'dependent_activity'      => $dep['sa_id'],
                'precedent_schedule_item' => $prec['iow_group_id'],
                'dependent_schedule_item' => $dep['iow_group_id'],
                'relation_type'           => (int)$lr['relation_type'],
                'lag_days'                => (int)$lr['lag_days'],
            ];
        }

        $copied = empty($toCopy) ? 0 : $this->saveRelationRows($projectId, $toCopy);

        return json_encode(['error' => 'No', 'copied' => $copied, 'checked' => count($rows)]);
    }

    /* List all active relationships for a project with names */
    public function actionList()
    {
        $projectId = (int)$_POST['projectid'];
        if (!$projectId) return json_encode(['error' => 'Yes', 'errortext' => 'No project.']);

        $rows = \Yii::$app->db->createCommand("
            SELECT ar.id,
                   ar.precedent_activity, sa_p.name AS prec_name,
                   ig_p.name AS prec_iow, sa_p.iow_group_id AS precedent_schedule_item,
                   ar.dependent_activity, sa_d.name AS dep_name,
                   ig_d.name AS dep_iow, sa_d.iow_group_id AS dependent_schedule_item,
                   ar.relation_type, ar.lag_days
            FROM activity_relations ar
            JOIN scheduleactivities sa_p ON sa_p.id = ar.precedent_activity
            JOIN scheduleactivities sa_d ON sa_d.id = ar.dependent_activity
            LEFT JOIN iow_groups ig_p ON ig_p.id = sa_p.iow_group_id AND ig_p.status = 0
            LEFT JOIN iow_groups ig_d ON ig_d.id = sa_d.iow_group_id AND ig_d.status = 0
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
                SELECT sa.id, sa.iow_group_id, wa.activity_Name AS name
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

        $row = [
            'precedent_activity'      => (int)$precSa['id'],
            'dependent_activity'      => (int)$depSa['id'],
            'precedent_schedule_item' => (int)$precSa['iow_group_id'],
            'dependent_schedule_item' => (int)$depSa['iow_group_id'],
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

        $duplicate = \app\models\ActivityRelations::find()
            ->where(['projectId' => $projectId, 'status' => 0, 'precedent_activity' => $precAct, 'dependent_activity' => $depAct])
            ->andWhere(['<>', 'id', $id])
            ->exists();
        if ($duplicate) {
            return json_encode(['error' => 'Yes', 'errortext' => 'A relationship between these two activities already exists.']);
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
        Yii::$app->helper->ResetUnlinkedDependentToToday($model->projectId, $model->dependent_activity);
        Yii::$app->helper->RecalculatePlannedDates($model->projectId);
        Yii::$app->helper->GetRelationcorrect($model->projectId);

        return json_encode(['error' => 'No']);
    }
}
