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

       An activity only qualifies for the estimate if BOTH:
       1. It is actually scheduled on the Gantt chart (a live scheduleactivities
          row, status = 0, under the same IOW) — not merely present in the WBS
          estimate table.
       2. It has a real cost value (quantity, rate and amount all > 0) — a
          placeholder row with no rate/quantity entered yet does not qualify. */
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
                sa.id                                          AS sched_id,
                wa.activity_Name                              AS activity_name,
                wa.activity_Unit                               AS unit,
                wa.sortorder                                  AS act_sortorder,
                COALESCE(pe.activity_qty, 0)                  AS quantity,
                COALESCE(
                    pe.specific_rate,
                    (SELECT ea.activity_rate FROM estimateactivities ea WHERE ea.activity_id = wa.activity_Id),
                    0
                )                                              AS rate
            FROM workgroups_new wg
            JOIN iow_groups ig ON ig.id = wg.iowGroupid
            JOIN workgroup_activities_new wa ON wa.wbs_id = wg.Workgroup_Id AND wa.pr_status = 0
            JOIN scheduleactivities sa ON sa.activity_id = wa.id AND sa.projectId = wg.Project_Id AND sa.status = 0
            LEFT JOIN pricing_estimate_new pe
                   ON pe.activity_Id = wa.id AND pe.project_Id = wg.Project_Id AND pe.pricing_status = 0
            WHERE wg.Project_Id = :pid AND wg.Status = 0
            GROUP BY wa.id
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
        foreach ($rows as $r) {
            $qty    = (float)$r['quantity'];
            $rate   = (float)$r['rate'];
            $amount = $qty * $rate;
            if ($qty <= 0 || $rate <= 0 || $amount <= 0) continue; /* no real cost value — excluded per spec */

            $iowKey = $r['iow_name'];
            if (!isset($byIow[$iowKey])) {
                $byIow[$iowKey] = [
                    'iow_name'  => $r['iow_name'],
                    'items'     => [],
                    'subtotal'  => 0.0,
                ];
                $groups[] = $iowKey;
            }
            // Cost of work done — same formula as the Cost Dashboard's
            // "Cost of Activity" panel: Σ(unit-rate × consumption) × qty done.
            $_POST = ['actid' => (int)$r['sched_id']];
            $cd = json_decode($costCtrl->actionCostdashboardactivity(), true) ?: [];
            $workDoneQty = (float)($cd['last_report_qty'] ?? 0);
            $estUCTotal = 0.0; $actUCTotal = 0.0; $hasActual = false;
            $resources = [];
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
            $eAmount = $estUCTotal * $workDoneQty;
            $aAmount = $hasActual ? ($actUCTotal * $workDoneQty) : $eAmount;
            $actualRate = $workDoneQty > 0 ? ($aAmount / $workDoneQty) : $rate;

            $byIow[$iowKey]['items'][] = [
                'sched_id'        => (int)$r['sched_id'],
                'activity_name'   => $r['activity_name'],
                'unit'            => $r['unit'],
                'quantity'        => (float)$r['quantity'],
                'rate'            => (float)$r['rate'],
                'amount'          => $amount,
                'work_done_qty'   => $workDoneQty,
                'e_amount'        => $eAmount,
                'a_amount'        => $aAmount,
                'actual_rate'     => $actualRate,
                'resources'       => $resources,
            ];
            $byIow[$iowKey]['subtotal'] += $amount;
            $grandTotal += $amount;
        }
        $_POST = $origPost;

        $sections = [];
        foreach ($groups as $iowKey) {
            $sections[] = $byIow[$iowKey];
        }

        return json_encode([
            'error'        => 'No',
            'project_name' => $project ? $project['Name'] : '',
            'sections'     => $sections,
            'grand_total'  => $grandTotal,
        ]);
    }
}
