<?php
/*
 * One-time migration: IOW groups per project type.
 *
 * Creates iow_group_worktypes (which project types each IOW group belongs to) and
 * fills it from the active activities already tagged to each group. Safe to run
 * more than once.
 *
 * Run on the server from the production folder:
 *   php migrations/iow_group_worktypes.php
 */
$config = require __DIR__ . '/../config/db.php';
$pdo = new PDO($config['dsn'], $config['username'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$pdo->exec("CREATE TABLE IF NOT EXISTS iow_group_worktypes (
    iow_group_id INT NOT NULL,
    worktype_id  INT NOT NULL,
    PRIMARY KEY (iow_group_id, worktype_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("INSERT IGNORE INTO iow_group_worktypes (iow_group_id, worktype_id)
    SELECT DISTINCT iow_group_id, work_type
    FROM estimateactivities
    WHERE iow_group_id > 0 AND work_type > 0 AND activity_status = 0");

echo "database: " . $pdo->query("SELECT DATABASE()")->fetchColumn() . PHP_EOL;
echo "iow_group_worktypes rows: " . $pdo->query("SELECT COUNT(*) FROM iow_group_worktypes")->fetchColumn() . PHP_EOL;
