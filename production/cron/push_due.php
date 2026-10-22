<?php
// Daily job (Hostinger hPanel > Advanced > Cron Jobs, 8:00 AM IST = 02:30 UTC):
//   php /home/u161084710/domains/gospl.opiamanalytics.com/public_html/production/cron/push_due.php
// Pushes every open action plan that is due today or overdue to its responsible
// person's phone (see ProjectsmainController::cfPushDuePlans).
chdir(dirname(__DIR__));
$_SERVER['HTTP_HOST'] = 'gospl.opiamanalytics.com';
$_SERVER['SERVER_NAME'] = 'gospl.opiamanalytics.com';
$_SERVER['SCRIPT_FILENAME'] = getcwd() . '/web/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REQUEST_URI'] = '/';
defined('YII_DEBUG') or define('YII_DEBUG', false);
require 'vendor/autoload.php';
require 'vendor/yiisoft/yii2/Yii.php';
$config = require 'config/web.php';
new yii\web\Application($config);
$ist = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
@file_put_contents(Yii::getAlias('@runtime') . '/push_due_date.txt', $ist->format('Y-m-d'));
echo $ist->format('Y-m-d H:i'), ' pushed ', app\controllers\ProjectsmainController::cfPushDuePlans(), " notification(s)\n";
