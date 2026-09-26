<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

class EstimatereportController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /* IOW-grouped estimate (BOQ-style) for a project: each activity's Unit,
       Quantity, Rate and Amount, grouped under its IOW (workgroups_new.Name —
       the per-project schedule item, e.g. "Excavation"), not the broader IOW
       Group library category (iow_groups.name, e.g. "Civil Works").

       Every IOW and activity created for this project shows here immediately
       — an IOW with no activities yet still gets its own (empty) section, and
       an activity with no schedule bar / no rate yet still shows as a row
       with Quantity/Rate/Amount at 0, rather than being silently excluded
       until pricing is entered. All three joins below are LEFT JOINs for
       exactly this reason (previously INNER JOINs, which is why newly
       created IOWs/activities via the "+" popups never appeared here). */
    public function actionGet()
    {
        $projectId = (int)$_POST['projectid'];
        if (!$projectId) return json_encode(['error' => 'Yes', 'errortext' => 'No project.']);

        $db = Yii::$app->db;

        $project = $db->createCommand(
            "SELECT Name FROM projects WHERE Project_Id = :pid",
            [':pid' => $projectId]
        )->queryOne();

        $rows = $db->createCommand("
            SELECT
                wg.Workgroup_Id                              AS iow_id,
                wg.Name                                      AS iow_name,
                wg.sortorder                                 AS iow_sortorder,
                wa.id                                        AS activity_row_id,
                wa.activity_Id                                AS lib_activity_id,
                sa.id                                          AS sched_id,
                wa.activity_Name                              AS activity_name,
                wa.activity_Unit                               AS unit,
                wa.sortorder                                  AS act_sortorder,
                COALESCE(pe.activity_qty, 0)                  AS quantity,
                COALESCE(
                    (SELECT ea.activity_rate FROM estimateactivities ea WHERE ea.activity_id = wa.activity_Id),
                    0
                )                                              AS rate
            FROM workgroups_new wg
            LEFT JOIN iow_groups ig ON ig.id = wg.iowGroupid
            LEFT JOIN workgroup_activities_new wa ON wa.wbs_id = wg.Workgroup_Id AND wa.pr_status = 0
            LEFT JOIN scheduleactivities sa ON sa.activity_id = wa.id AND sa.projectId = wg.Project_Id AND sa.status = 0
            LEFT JOIN pricing_estimate_new pe
                   ON pe.activity_Id = wa.id AND pe.project_Id = wg.Project_Id AND pe.pricing_status = 0
            WHERE wg.Project_Id = :pid AND wg.Status = 0
            GROUP BY wg.Workgroup_Id, wa.id
            ORDER BY wg.sortorder ASC, wg.Workgroup_Id ASC, wa.sortorder ASC, wa.id ASC
        ", [':pid' => $projectId])->queryAll();

        // Cost-of-work-done figures reuse the exact same formula as the Cost
        // Dashboard's "Cost of Activity" panel (ProjectsmainController::
        // actionCostdashboardactivity + newganttview.php's _renderCostOfActivity)
        // rather than recomputing a different approximation here.
        $costCtrl = new \app\controllers\ProjectsmainController('projectsmain', Yii::$app);
        $origPost = $_POST;

        /* Group into IOW sections with subtotal, plus a grand total.
           Multiple workgroups_new rows can share the same IOW Name (the WBS
           allows the same IOW to be split across rows), so sections are
           keyed by name here — not Workgroup_Id — to avoid showing the same
           IOW heading more than once with only some of its activities. */
        $groups   = [];
        $byIow    = [];
        $grandTotal = 0.0;
        $grandTotalActual = 0.0;
        foreach ($rows as $r) {
            $iowKey = $r['iow_name'];
            if (!isset($byIow[$iowKey])) {
                $byIow[$iowKey] = [
                    'iow_id'           => (int)$r['iow_id'],
                    'iow_name'         => $r['iow_name'],
                    'items'            => [],
                    'subtotal'         => 0.0,
                    'subtotal_actual'  => 0.0,
                ];
                $groups[] = $iowKey;
            }

            // Empty IOW (no activity attached yet) — the LEFT JOIN chain
            // still produces one row per IOW with wa.id NULL so the
            // section above gets created; nothing else to add here.
            if ($r['activity_row_id'] === null) continue;

            $qty    = (float)$r['quantity'];
            $rate   = (float)$r['rate'];
            $amount = $qty * $rate;

            // Cost of work done — same formula as the Cost Dashboard's
            // "Cost of Activity" panel: Σ(unit-rate × consumption) × qty done.
            // Scheduled activities (sched_id) use actid; estimate-only
            // activities like Project Overhead (see ProjectsController::
            // _seedProjectOverheadActivity — no Gantt bar, so no work-done
            // qty either) use wan_id instead — same actionCostdashboardactivity
            // call, matching the fallback path the Manage Cost popup already
            // uses for these ("Cost Per Day" dial).
            $workDoneQty = 0.0; $eAmount = 0.0; $aAmount = 0.0; $actualRate = $rate;
            $resources = [];
            // Estimate-only activities (Project Overhead etc. — no
            // scheduleactivities row) are identified specifically by
            // lib_activity_id being one of the two auto-seeded ones, NOT
            // by "no sched_id", to avoid running this per-row DB call for
            // every ordinary activity that simply hasn't been scheduled
            // yet — that would multiply this table's query count by its
            // full activity count for no benefit.
            $isOverheadRow = !$r['sched_id'] && in_array((int)$r['lib_activity_id'], [87, 89], true);
            if ($r['sched_id'] || $isOverheadRow) {
                $_POST = $r['sched_id'] ? ['actid' => (int)$r['sched_id']] : ['wan_id' => (int)$r['activity_row_id']];
                $cd = json_decode($costCtrl->actionCostdashboardactivity(), true) ?: [];
                $workDoneQty = (float)($cd['last_report_qty'] ?? 0);
                $estUCTotal = 0.0; $actUCTotal = 0.0; $hasActual = false;
                foreach (($cd['items'] ?? []) as $it) {
                    $estUC  = (float)($it['rate'] ?? 0);
                    $plannedCons = (float)($it['planned_consumption'] ?? 0);
                    $estUCTotal += $estUC * $plannedCons;
                    $hasAct = isset($it['actual_unit_cost']) && $it['actual_unit_cost'] !== null;
                    $actUC  = $hasAct ? (float)$it['actual_unit_cost'] : $estUC;
                    $actUCTotal += $actUC * (float)($it['actual_consumption'] ?? 0);
                    if ($hasAct) $hasActual = true;

                    // Per-resource breakdown for the activity's hover tooltip —
                    // quantity consumed per unit of this activity, at estimated rate,
                    // plus its actual cost (same fields the Cost Dashboard's "Cost of
                    // Resources" panel bar-tooltip uses) so the A.Amount/Rate columns
                    // can show the identical Est-vs-Act resource bar chart on hover.
                    $actualCons = (float)($it['actual_consumption'] ?? 0);
                    $resources[] = [
                        'name'        => $it['name'] ?? '',
                        'unit'        => $it['unit'] ?? '',
                        'qty'         => $plannedCons,
                        'rate'        => $estUC,
                        'amount'      => $estUC * $plannedCons,
                        'act_rate'    => $actUC,
                        'act_qty'     => $actualCons,
                        'act_amount'  => $actUC * $actualCons,
                        'has_actual'  => $hasAct,
                    ];
                }
                if ($r['sched_id']) {
                    // Scheduled activity: Amount scales with work actually
                    // done, so Actual Rate = Actual Amount ÷ Qty Done.
                    $eAmount = $estUCTotal * $workDoneQty;
                    $aAmount = $hasActual ? ($actUCTotal * $workDoneQty) : $eAmount;
                    $actualRate = $workDoneQty > 0 ? ($aAmount / $workDoneQty) : $rate;
                } else {
                    // Estimate-only activity (Project Overhead etc.): there is
                    // no Gantt work-done qty to divide by — the resource-rate
                    // sum itself IS the per-unit (e.g. per-Day) rate, exactly
                    // like the "Cost Per Day" dial's Est/Act figures.
                    $eAmount = $estUCTotal * $qty;
                    $aAmount = $hasActual ? ($actUCTotal * $qty) : $eAmount;
                    $actualRate = $hasActual ? $actUCTotal : $rate;
                }
            }

            $byIow[$iowKey]['items'][] = [
                'sched_id'        => (int)$r['sched_id'],
                'wan_id'          => (int)$r['activity_row_id'],
                'lib_activity_id' => (int)$r['lib_activity_id'],
                'activity_name'   => $r['activity_name'],
                'unit'            => $r['unit'],
                'quantity'        => $qty,
                'rate'            => $rate,
                'amount'          => $amount,
                'work_done_qty'   => $workDoneQty,
                'e_amount'        => $eAmount,
                'a_amount'        => $aAmount,
                'actual_rate'     => $actualRate,
                'resources'       => $resources,
            ];
            $byIow[$iowKey]['subtotal'] += $amount;
            $byIow[$iowKey]['subtotal_actual'] += $aAmount;
            $grandTotal += $amount;
            $grandTotalActual += $aAmount;
        }
        $_POST = $origPost;

        $sections = [];
        foreach ($groups as $iowKey) {
            $sections[] = $byIow[$iowKey];
        }

        return json_encode([
            'error'              => 'No',
            'project_name'       => $project ? $project['Name'] : '',
            'sections'           => $sections,
            'grand_total'        => $grandTotal,
            'grand_total_actual' => $grandTotalActual,
        ]);
    }

    /* Edits Unit/Quantity from the Project Estimate popup's IOW row-list
       (the "Edit" pencil there). Writes to every place that value is
       read from: pricing_estimate_new.activity_qty (what actionGet()
       above actually selects as "quantity"), workgroup_activities_new
       (the project-instance record, cycle_Unit/cycle_Quantity — same
       columns actionWbsactivityupdate's sibling logic keeps in sync),
       and scheduleactivities (the live Gantt bar, if this activity has
       one yet) so the Gantt and this report never disagree. */
    public function actionUpdateunitqty()
    {
        $wanId  = (int)($_POST['wan_id'] ?? 0);
        $schedId = (int)($_POST['sched_id'] ?? 0);
        $unit   = trim($_POST['unit'] ?? '');
        $qty    = (float)($_POST['quantity'] ?? 0);
        if (!$wanId) return json_encode(['error' => 'Yes', 'errortext' => 'No activity id.']);

        $db = Yii::$app->db;

        $wan = $db->createCommand(
            "SELECT id, project_Id FROM workgroup_activities_new WHERE id=:id LIMIT 1",
            [':id' => $wanId]
        )->queryOne();
        if (!$wan) return json_encode(['error' => 'Yes', 'errortext' => 'Activity not found.']);
        $projectId = (int)$wan['project_Id'];

        $db->createCommand(
            "UPDATE workgroup_activities_new SET activity_Unit=:u, cycle_Unit=:u, cycle_Quantity=:q WHERE id=:id",
            [':u' => $unit, ':q' => $qty, ':id' => $wanId]
        )->execute();

        $peExists = $db->createCommand(
            "SELECT pricing_estimate_Id FROM pricing_estimate_new WHERE project_Id=:p AND activity_Id=:a LIMIT 1",
            [':p' => $projectId, ':a' => $wanId]
        )->queryScalar();
        if ($peExists) {
            $db->createCommand(
                "UPDATE pricing_estimate_new SET activity_qty=:q WHERE pricing_estimate_Id=:id",
                [':q' => $qty, ':id' => $peExists]
            )->execute();
        } else {
            $db->createCommand()->insert('pricing_estimate_new', [
                'project_Id'         => $projectId,
                'activity_Id'        => $wanId,
                'activity_qty'       => $qty,
                'specific_rate'      => 0,
                'est_activity_Id'    => 0,
                'pricing_status'     => 0,
                'operations_status'  => 0,
                'primavera_id'       => 0,
            ])->execute();
        }

        if ($schedId) {
            $db->createCommand(
                "UPDATE scheduleactivities SET unit=:u, quantity=:q WHERE id=:id AND projectId=:p AND status=0",
                [':u' => $unit, ':q' => $qty, ':id' => $schedId, ':p' => $projectId]
            )->execute();
        }

        return json_encode(['error' => 'No']);
    }
}
