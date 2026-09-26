<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

/**
 * Standalone Resource Allocation controller for the Project Estimate
 * popup's "Resource Allocation" button (views/projectsmain/
 * overrelay-estimatereport.php). Reads/writes the SAME table the real
 * Activity Library screen does (estactivity_resources, keyed by the
 * library estimateactivities.activity_id) — so resources allocated here
 * show up in the Activity Library and vice versa, and estimateactivities
 * .activity_rate is kept in sync exactly the same way (SUM of
 * est_resource_amount for the activity) — but via a clean, parameterized,
 * self-contained popup instead of reusing the Activity Library's own
 * deeply-embedded in-page DOM swap (see the earlier attempt/why it was
 * reverted).
 *
 * Scope: the generic Resource Type + Resource + Unit + Qty + Rate +
 * Amount case only — the legacy screen's special Machinery/Labor/
 * Manpower fields (fuel, hours, man-days) reference resource type IDs
 * (24, 26, 33) that do not exist in the live resourcetype table, so
 * those branches are unreachable with real data and were not ported.
 */
class EstresourceallocController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /* Lists the resources already allocated to a library activity, plus
       the activity's own name/unit/rate header. */
    public function actionList()
    {
        $activityId = (int)($_POST['activity_id'] ?? 0);
        // -1 means "header/activity info only" (the popup's own header
        // bar load) — no resource type to filter by yet at that point.
        $restypeId  = isset($_POST['restype_id']) ? (int)$_POST['restype_id'] : 0;
        $groupId    = (int)($_POST['group_id'] ?? 0);
        if (!$activityId) return json_encode(['error' => 'Yes', 'errortext' => 'No activity id.']);

        $db = Yii::$app->db;
        $activity = $db->createCommand(
            "SELECT activity_id, activity_name, activity_unit, activity_rate FROM estimateactivities WHERE activity_id=:id",
            [':id' => $activityId]
        )->queryOne();
        if (!$activity) return json_encode(['error' => 'Yes', 'errortext' => 'Activity not found.']);

        if ($restypeId === -1) {
            return json_encode(['error' => 'No', 'activity' => $activity, 'rows' => []]);
        }

        $sql = "SELECT ear.estactres_id, ear.est_resource_id, ear.est_resource_unit, ear.est_resource_quantity,
                       ear.est_resource_rate, ear.est_resource_amount,
                       r.Name AS resource_name, rt.Name AS resource_type_name, r.ResourceType_Id, r.Resource_group_Id
                FROM estactivity_resources ear
                JOIN resources r ON r.Resource_Id = ear.est_resource_id
                LEFT JOIN resourcetype rt ON rt.ResourceType_Id = r.ResourceType_Id
                WHERE ear.estactivity_id = :aid AND ear.est_resource_status = 0";
        $params = [':aid' => $activityId];
        if ($restypeId > 0) {
            $sql .= " AND r.ResourceType_Id = :restypeid";
            $params[':restypeid'] = $restypeId;
        }
        if ($groupId > 0) {
            $sql .= " AND r.Resource_group_Id = :groupid";
            $params[':groupid'] = $groupId;
        }
        $sql .= " ORDER BY ear.estactres_id ASC";

        $rows = $db->createCommand($sql, $params)->queryAll();

        return json_encode([
            'error'    => 'No',
            'activity' => $activity,
            'rows'     => $rows,
        ]);
    }

    /* Active resource types — populates the Resource Allocation popup's
       left sidebar tabs. */
    public function actionResourcetypes()
    {
        $rows = Yii::$app->db->createCommand(
            "SELECT ResourceType_Id, Name FROM resourcetype WHERE Status=0 ORDER BY sortorder ASC"
        )->queryAll();
        return json_encode(['error' => 'No', 'items' => $rows]);
    }

    /* Resource groups under a resource type — populates the "piano bars"
       shown on the right when a type tab is active (same resource_group
       table the Activity Library's own type→group→resource drilldown
       uses, e.g. ProjectsmainController::actionGetresgrouplist()). */
    public function actionResourcegroups()
    {
        $restypeId = (int)($_POST['restype_id'] ?? 0);
        if (!$restypeId) return json_encode(['error' => 'No', 'items' => []]);

        $rows = Yii::$app->db->createCommand(
            "SELECT Resource_group_Id, Resource_group_Name FROM resource_group
             WHERE status=0 AND ResourceType_Id=:tid ORDER BY RG_sortorder ASC, Resource_group_Name ASC",
            [':tid' => $restypeId]
        )->queryAll();
        return json_encode(['error' => 'No', 'items' => $rows]);
    }

    /* Searches the resource master, optionally filtered by type/group/name,
       excluding resources already allocated to this activity — same
       exclusion actionAllocationsearch() (the legacy screen) applies. */
    public function actionResources()
    {
        $activityId = (int)($_POST['activity_id'] ?? 0);
        $restypeId  = (int)($_POST['restype_id'] ?? 0);
        $groupId    = (int)($_POST['group_id'] ?? 0);
        $search     = trim($_POST['search'] ?? '');

        $sql = "SELECT r.Resource_Id, r.Name, r.Unit, r.Price, r.ResourceType_Id, r.Resource_group_Id, rt.Name AS TypeName
                FROM resources r
                LEFT JOIN resourcetype rt ON rt.ResourceType_Id = r.ResourceType_Id
                WHERE r.Status = 0";
        $params = [];
        if ($restypeId > 0) {
            $sql .= " AND r.ResourceType_Id = :restypeid";
            $params[':restypeid'] = $restypeId;
        }
        if ($groupId > 0) {
            $sql .= " AND r.Resource_group_Id = :groupid";
            $params[':groupid'] = $groupId;
        }
        if ($search !== '') {
            $sql .= " AND r.Name LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }
        if ($activityId > 0) {
            $sql .= " AND r.Resource_Id NOT IN (
                        SELECT est_resource_id FROM estactivity_resources
                        WHERE estactivity_id = :activityid AND est_resource_status = 0
                      )";
            $params[':activityid'] = $activityId;
        }
        $sql .= " ORDER BY rt.Name ASC, r.Name ASC LIMIT 200";

        $rows = Yii::$app->db->createCommand($sql, $params)->queryAll();
        return json_encode(['error' => 'No', 'items' => $rows]);
    }

    /* Allocates one resource to the activity, then recalculates
       estimateactivities.activity_rate as the SUM of every allocated
       resource's amount — same convention actionAddactivityresource()/
       actionUpdateallocaterrated()/actionDeleteactivityresource() (the
       legacy screen) already use, kept identical here so the Rate shown
       in the Activity Library and this popup never disagree. */
    public function actionAdd()
    {
        $activityId = (int)($_POST['activity_id'] ?? 0);
        $resourceId = (int)($_POST['resource_id'] ?? 0);
        $unit       = trim($_POST['unit'] ?? '');
        $qty        = (float)($_POST['qty'] ?? 0);
        $rate       = (float)($_POST['rate'] ?? 0);
        if (!$activityId || !$resourceId) return json_encode(['error' => 'Yes', 'errortext' => 'Missing data.']);
        if (!$qty) return json_encode(['error' => 'Yes', 'errortext' => 'Qty is required.']);

        $db = Yii::$app->db;
        $amount = $qty * $rate;

        $db->createCommand()->insert('estactivity_resources', [
            'estactivity_id'        => $activityId,
            'est_resource_id'       => $resourceId,
            'est_resource_unit'     => $unit,
            'est_resource_quantity' => $qty,
            'est_resource_rate'     => $rate,
            'est_resource_amount'   => $amount,
            'est_resource_status'   => 0,
            'fuel_vendor_id'        => 0,
            'powerstatus'           => 0,
        ])->execute();

        $this->recalcActivityRate($activityId);
        return json_encode(['error' => 'No']);
    }

    /* Edits an already-allocated resource's Qty/Rate (Unit is fixed to
       the resource master's own unit, matching the legacy screen). */
    public function actionUpdate()
    {
        $estactresId = (int)($_POST['estactres_id'] ?? 0);
        $qty         = (float)($_POST['qty'] ?? 0);
        $rate        = (float)($_POST['rate'] ?? 0);
        if (!$estactresId) return json_encode(['error' => 'Yes', 'errortext' => 'No row id.']);
        if (!$qty) return json_encode(['error' => 'Yes', 'errortext' => 'Qty is required.']);

        $db = Yii::$app->db;
        $row = $db->createCommand(
            "SELECT estactivity_id FROM estactivity_resources WHERE estactres_id=:id",
            [':id' => $estactresId]
        )->queryOne();
        if (!$row) return json_encode(['error' => 'Yes', 'errortext' => 'Row not found.']);

        $amount = $qty * $rate;
        $db->createCommand(
            "UPDATE estactivity_resources SET est_resource_quantity=:q, est_resource_rate=:r, est_resource_amount=:a WHERE estactres_id=:id",
            [':q' => $qty, ':r' => $rate, ':a' => $amount, ':id' => $estactresId]
        )->execute();

        $this->recalcActivityRate((int)$row['estactivity_id']);
        return json_encode(['error' => 'No']);
    }

    /* Removes an allocated resource from the activity. */
    public function actionDelete()
    {
        $estactresId = (int)($_POST['estactres_id'] ?? 0);
        if (!$estactresId) return json_encode(['error' => 'Yes', 'errortext' => 'No row id.']);

        $db = Yii::$app->db;
        $row = $db->createCommand(
            "SELECT estactivity_id FROM estactivity_resources WHERE estactres_id=:id",
            [':id' => $estactresId]
        )->queryOne();
        if (!$row) return json_encode(['error' => 'Yes', 'errortext' => 'Row not found.']);

        $db->createCommand()->delete('estactivity_resources', ['estactres_id' => $estactresId])->execute();

        $this->recalcActivityRate((int)$row['estactivity_id']);
        return json_encode(['error' => 'No']);
    }

    private function recalcActivityRate($activityId)
    {
        $db = Yii::$app->db;
        $total = $db->createCommand(
            "SELECT COALESCE(SUM(est_resource_amount),0) FROM estactivity_resources WHERE estactivity_id=:id AND est_resource_status=0",
            [':id' => $activityId]
        )->queryScalar();
        $db->createCommand(
            "UPDATE estimateactivities SET activity_rate=:r WHERE activity_id=:id",
            [':r' => (float)$total, ':id' => $activityId]
        )->execute();
    }
}
