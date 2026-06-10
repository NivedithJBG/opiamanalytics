<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "schedule_task_report".
 *
 * @property int $id
 * @property int $cycleno
 * @property int $taskid tasks_new table primary key
 * @property int $activityid scheduleactivities table primary key
 * @property string $date
 * @property float $act_duration
 */
class ScheduleTaskReport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schedule_task_report';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cycleno', 'taskid', 'activityid', 'date', 'act_duration'], 'required'],
            [['cycleno', 'taskid', 'activityid'], 'integer'],
            [['date'], 'safe'],
            [['act_duration'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cycleno' => 'Cycleno',
            'taskid' => 'Taskid',
            'activityid' => 'Activityid',
            'date' => 'Date',
            'act_duration' => 'Act Duration',
        ];
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
