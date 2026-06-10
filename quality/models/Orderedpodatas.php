<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ordered_podatas".
 *
 * @property int $id
 * @property int $jobid
 * @property int $resourceid
 * @property int $order_id
 */
class Orderedpodatas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ordered_podatas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jobid', 'resourceid', 'order_id'], 'required'],
            [['jobid', 'resourceid', 'order_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jobid' => 'Jobid',
            'resourceid' => 'Resourceid',
            'order_id' => 'Order ID',
        ];
    }
}
