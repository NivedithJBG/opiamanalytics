<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "schedule_task_report_resources".
 *
 * @property int $id
 * @property int $cycleno
 * @property int $taskid tasks_new table primary key
 * @property int $activityid scheduleactivities table primary key
 * @property string $date
 * @property float $act_duration
 */
class ScheduleTaskReportResources extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schedule_task_report_resources';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['schedule_task_report_id', 'res_id',], 'integer'],
            [['count', 'no_of_equipment', 'no_of_labour'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID'
        ];
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
