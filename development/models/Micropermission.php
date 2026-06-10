<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "micro_permission".
 *
 * @property int $id
 * @property int $tab_id
 * @property string $name
 */
class Micropermission extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'micro_permission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tab_id', 'name'], 'required'],
            [['tab_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tab_id' => 'Tab ID',
            'name' => 'Name',
        ];
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
