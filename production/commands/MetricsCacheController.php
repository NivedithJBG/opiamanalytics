<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\components\MetricsCacheHelper;

/**
 * Refreshes the chatbot metrics cache for all active projects.
 * Run via cron: php yii metrics-cache/refresh-all
 */
class MetricsCacheController extends Controller
{
    public function actionRefreshAll()
    {
        echo date('Y-m-d H:i:s') . " Refreshing chatbot metrics cache...\n";
        MetricsCacheHelper::refreshAll();
        echo date('Y-m-d H:i:s') . " Done.\n";
        return ExitCode::OK;
    }
}
