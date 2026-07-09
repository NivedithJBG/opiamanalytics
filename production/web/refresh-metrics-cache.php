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

    foreach ($metrics as $name => $val) {
        $upsert->execute([':pid' => $pid, ':name' => $name, ':val' => $val]);
    }

    echo "  Project {$pid}: estimated=" . number_format($metrics['estimated_cost'], 0)
        . " po=" . number_format($metrics['actual_cost_po'], 0)
        . " progress=" . $metrics['physical_progress_pct'] . "%\n";
}

echo date('Y-m-d H:i:s') . " Done.\n";
