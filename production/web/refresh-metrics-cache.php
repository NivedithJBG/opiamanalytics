<?php
/**
 * Standalone metrics cache refresh — no Yii bootstrap needed.
 * Cron: php /home/.../public_html/production/web/refresh-metrics-cache.php
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('CLI only'); }

$dbConf = require __DIR__ . '/../config/db.php';
preg_match('/host=([^;]+)/', $dbConf['dsn'], $m1);
preg_match('/dbname=([^;]+)/', $dbConf['dsn'], $m2);
$host   = $m1[1] ?? 'localhost';
$dbname = $m2[1] ?? '';
$user   = $dbConf['username'];
$pass   = $dbConf['password'];

$pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$projects = $pdo->query("SELECT Project_Id FROM projects WHERE Status = 0")->fetchAll(PDO::FETCH_COLUMN);
echo date('Y-m-d H:i:s') . " Refreshing " . count($projects) . " projects...\n";

$upsert = $pdo->prepare("
    INSERT INTO chatbot_metrics_cache (project_id, metric_name, metric_value, updated_at)
    VALUES (:pid, :name, :val, NOW())
    ON DUPLICATE KEY UPDATE metric_value = :val, updated_at = NOW()
");

foreach ($projects as $pid) {
    $pid = (int)$pid;
    $metrics = [];

    // Estimated cost
    $r = $pdo->prepare("
        SELECT SUM(pern.rate * pern.quantity * pen.activity_qty) AS v
        FROM workgroup_activities_new wa
        JOIN pricing_estimate_new pen ON pen.activity_Id=wa.id AND pen.project_Id=wa.project_Id AND pen.pricing_status=0
        JOIN pricing_estimate_resources_new pern ON pern.activity_id=wa.id AND pern.project_id=wa.project_Id AND pern.pricing_status=0
        WHERE wa.estimate=1 AND wa.pricing_status=0 AND wa.project_Id=?
    ");
    $r->execute([$pid]);
    $metrics['estimated_cost'] = (float)($r->fetchColumn() ?? 0);

    // Actual cost — PO committed
    $r = $pdo->prepare("
        SELECT SUM(por.amount) FROM purchase_order_resources por
        JOIN purchase_orders po ON po.order_id=por.order_id
        WHERE po.project_id=? AND po.delete_status=0 AND por.delete_status=0
    ");
    $r->execute([$pid]);
    $metrics['actual_cost_po'] = (float)($r->fetchColumn() ?? 0);

    // Actual cost — GRN received
    $r = $pdo->prepare("
        SELECT SUM(g.GRN_Quantity * por.rate)
        FROM pricing_estimate_resources_new pern
        JOIN purchase_order_resources por ON por.allocation_id=pern.pricing_resourceid
        JOIN goods_received_note g ON g.GRN_Purchase_Order=por.order_id AND g.GRN_Item=pern.resource_Id
        JOIN purchase_orders po ON po.order_id=por.order_id
        WHERE pern.project_id=? AND po.project_id=? AND po.delete_status=0 AND por.delete_status=0
    ");
    $r->execute([$pid, $pid]);
    $metrics['actual_cost_grn'] = (float)($r->fetchColumn() ?? 0);

    // Physical progress
    $r = $pdo->prepare("
        SELECT
            COUNT(sa.id),
            SUM(CASE WHEN sa.completed_status=1 THEN 1 ELSE 0 END),
            ROUND(SUM(CASE WHEN sa.quantity>0 THEN LEAST(COALESCE(rpt.cumulated_qty,0)/sa.quantity,1.0) ELSE (CASE WHEN sa.completed_status=1 THEN 1.0 ELSE 0.0 END) END)/NULLIF(COUNT(sa.id),0)*100,1)
        FROM scheduleactivities sa
        LEFT JOIN (SELECT activity_id, SUM(currentqty) AS cumulated_qty FROM schedule_progress_report_log GROUP BY activity_id) rpt ON rpt.activity_id=sa.id
        WHERE sa.projectId=? AND sa.status=0
    ");
    $r->execute([$pid]);
    $row = $r->fetch(PDO::FETCH_NUM);
    $metrics['total_activities']      = (int)($row[0] ?? 0);
    $metrics['completed_activities']  = (int)($row[1] ?? 0);
    $metrics['physical_progress_pct'] = (float)($row[2] ?? 0);

    // Schedule / critical path
    $r = $pdo->prepare("
        SELECT COUNT(sa.id),
               MAX(sa.end_date),
               SUM(CASE WHEN sa.completed_status=0 AND sa.end_date<CURDATE() AND COALESCE(rpt.cumulated_qty,0)<sa.quantity THEN 1 ELSE 0 END),
               MAX(CASE WHEN sa.completed_status=0 AND sa.end_date<CURDATE() THEN DATEDIFF(CURDATE(),sa.end_date) ELSE 0 END)
        FROM scheduleactivities sa
        LEFT JOIN (SELECT activity_id, SUM(currentqty) AS cumulated_qty FROM schedule_progress_report_log GROUP BY activity_id) rpt ON rpt.activity_id=sa.id
        WHERE sa.projectId=? AND sa.status=0 AND sa.critical_status='Yes'
    ");
    $r->execute([$pid]);
    $row = $r->fetch(PDO::FETCH_NUM);
    $metrics['critical_count']   = (int)($row[0] ?? 0);
    $metrics['planned_end_date'] = $row[1] ? strtotime($row[1]) : 0;
    $metrics['delayed_critical'] = (int)($row[2] ?? 0);
    $metrics['max_delay_days']   = (int)($row[3] ?? 0);

    // -------------------------------------------------------------------
    // COST OF WORK DONE — replicates actionCostdashboardbatch logic
    // -------------------------------------------------------------------

    // Schedule activities
    $r = $pdo->prepare("SELECT id, activity_id, quantity FROM scheduleactivities WHERE projectId=? AND status=0");
    $r->execute([$pid]);
    $schedActs = $r->fetchAll(PDO::FETCH_ASSOC);

    if ($schedActs) {
        $actIds = array_column($schedActs, 'id');
        $wbIds  = array_unique(array_column($schedActs, 'activity_id'));
        $inActs = implode(',', array_map('intval', $actIds));
        $inWbs  = implode(',', array_map('intval', $wbIds));

        // Last reported qty per schedule activity
        $r = $pdo->query("SELECT activity_id, MAX(cumulated_qty) AS lq FROM schedule_progress_report WHERE activity_id IN ($inActs) GROUP BY activity_id");
        $lastQtyMap = [];
        foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $row) { $lastQtyMap[(int)$row['activity_id']] = (float)$row['lq']; }

        // Estimate activity qty per WBS id
        $r = $pdo->prepare("SELECT activity_Id, activity_qty FROM pricing_estimate_new WHERE activity_Id IN ($inWbs) AND project_Id=? AND pricing_status=0");
        $r->execute([$pid]);
        $estQtyMap = [];
        foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $row) { $estQtyMap[(int)$row['activity_Id']] = (float)$row['activity_qty']; }

        // Resources per WBS id with GRN actual unit cost and store_indent stock
        $r = $pdo->prepare("
            SELECT pern.activity_id, pern.pricing_resourceid, pern.resourcetype_Id AS type_id,
                   pern.rate, pern.quantity AS res_qty, pern.task_ids, pern.resource_Id,
                   grn.actual_unit_cost, grn.grn_qty, si.stock_at_site
            FROM pricing_estimate_resources_new pern
            LEFT JOIN (
                SELECT por.allocation_id,
                       SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))*por.rate)/NULLIF(SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))),0) AS actual_unit_cost,
                       SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))) AS grn_qty
                FROM purchase_order_resources por
                JOIN goods_received_note g ON g.GRN_Purchase_Order=por.order_id AND g.GRN_Item=por.resource_id
                JOIN purchase_orders po ON po.order_id=por.order_id
                WHERE po.project_id=? AND po.delete_status=0 AND por.delete_status=0
                GROUP BY por.allocation_id
            ) grn ON grn.allocation_id=pern.pricing_resourceid
            LEFT JOIN (
                SELECT si2.resource_id, si2.activity_id AS si_act, si2.stock_at_site
                FROM store_indents si2
                INNER JOIN (SELECT resource_id, activity_id, MAX(id) AS mx FROM store_indents WHERE project_id=? GROUP BY resource_id, activity_id) lsi
                ON lsi.resource_id=si2.resource_id AND lsi.activity_id=si2.activity_id AND si2.id=lsi.mx
            ) si ON si.resource_id=pern.resource_Id AND si.si_act=pern.activity_id
            WHERE pern.activity_id IN ($inWbs) AND pern.project_id=? AND pern.pricing_status=0
        ");
        $r->execute([$pid, $pid, $pid]);
        $resByWbs = [];
        foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $row) { $resByWbs[(int)$row['activity_id']][] = $row; }

        // MB data: task amounts and work done by task_id
        $taskAmountById = [];
        $taskWorkDoneById = [];
        $r = $pdo->prepare("SELECT entries FROM wo_measurement_book WHERE project_id=? AND sent_status=1 AND delete_status=0");
        $r->execute([$pid]);
        foreach ($r->fetchAll(PDO::FETCH_COLUMN) as $entriesJson) {
            foreach (json_decode($entriesJson ?? '[]', true) ?: [] as $entry) {
                foreach ($entry['tasks'] ?? [] as $task) {
                    $wd  = (float)($task['work_done'] ?? 0);
                    $rt  = (float)($task['rate']      ?? 0);
                    $tid = (int)($task['task_id']     ?? 0);
                    if ($tid) {
                        $taskAmountById[$tid]   = ($taskAmountById[$tid]   ?? 0.0) + $rt * $wd;
                        $taskWorkDoneById[$tid] = ($taskWorkDoneById[$tid] ?? 0.0) + $wd;
                    }
                }
            }
        }

        // Compute estwd and actwd per schedule activity — same formula as actionCostdashboardbatch
        $totalEstWd = 0.0;
        $totalActWd = 0.0;
        foreach ($schedActs as $sa) {
            $schedId  = (int)$sa['id'];
            $wbId     = (int)$sa['activity_id'];
            $schedQty = (float)$sa['quantity'];
            $estQty   = $estQtyMap[$wbId] ?? 0;
            $lastQty  = $lastQtyMap[$schedId] ?? 0;
            $resources = $resByWbs[$wbId] ?? [];
            $ratio    = ($schedQty > 0) ? $estQty / $schedQty : 0.0;

            $unitCost    = 0.0;
            $actualTotal = 0.0;
            foreach ($resources as $res) {
                $typeId  = (int)$res['type_id'];
                $resQty  = (float)$res['res_qty'];
                $unitCost += $resQty * (float)$res['rate'];
                $plannedConsumption = $resQty * $ratio;

                if ($typeId === 4) {
                    $taskIds = array_filter(array_map('intval', explode(',', $res['task_ids'] ?? '')));
                    $wdVal = 0.0; $wdQty = 0.0;
                    foreach ($taskIds as $tid) {
                        $wdVal += $taskAmountById[$tid]   ?? 0.0;
                        $wdQty += $taskWorkDoneById[$tid] ?? 0.0;
                    }
                    $actUnit = $wdQty > 0 ? $wdVal / $wdQty : null;
                    $scWd = 0.0;
                    foreach ($taskIds as $tid) { $scWd += $taskWorkDoneById[$tid] ?? 0.0; }
                    $actualConsumption = ($lastQty > 0 && $scWd > 0) ? $scWd / $lastQty : $plannedConsumption;
                } elseif (in_array($typeId, [2, 6, 7, 8])) {
                    $actUnit = ($res['actual_unit_cost'] !== null) ? (float)$res['actual_unit_cost'] : null;
                    $indentRaised = ($res['stock_at_site'] !== null);
                    if ($indentRaised && $lastQty > 0) {
                        $actualConsumption = max(0, (float)($res['grn_qty'] ?? 0) - (float)($res['stock_at_site'] ?? 0)) / $lastQty;
                    } else {
                        $actualConsumption = $plannedConsumption;
                    }
                } else {
                    $actUnit = null;
                    $actualConsumption = $plannedConsumption;
                }

                if ($actUnit !== null) { $actualTotal += $actUnit * $actualConsumption; }
            }

            $estUCTotal = $unitCost * $ratio;
            $estwd = $estUCTotal * $lastQty;
            $actwd = $actualTotal > 0 ? $actualTotal * $lastQty : $estwd;
            $totalEstWd += $estwd;
            $totalActWd += $actwd;
        }

        $metrics['estimated_cost_work_done'] = round($totalEstWd, 2);
        $metrics['actual_cost_work_done']    = round($totalActWd, 2);
    }

    foreach ($metrics as $name => $val) {
        $upsert->execute([':pid' => $pid, ':name' => $name, ':val' => $val]);
    }

    echo "  Project {$pid}: estimated_cost_work_done=" . number_format($metrics['estimated_cost_work_done'] ?? 0, 0)
        . " actual_cost_work_done=" . number_format($metrics['actual_cost_work_done'] ?? 0, 0)
        . " progress=" . $metrics['physical_progress_pct'] . "%\n";
}

echo date('Y-m-d H:i:s') . " Done.\n";
