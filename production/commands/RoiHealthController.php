<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\components\RoiHealthCronHelper;

/**
 * Runs check_roi_health for all active projects and queues alerts on
 * status transitions / red status. Run via cron: php yii roi-health/run-all
 */
class RoiHealthController extends Controller
{
    public function actionRunAll()
    {
        echo date('Y-m-d H:i:s') . " Running ROI health check for all active projects...\n";
        $summary = RoiHealthCronHelper::runAll();
        echo date('Y-m-d H:i:s') . " Done. Checked {$summary['checked']}, "
            . "alerted {$summary['alerted']}, skipped (no baseline) {$summary['skipped_no_baseline']}.\n";
        return ExitCode::OK;
    }
}
