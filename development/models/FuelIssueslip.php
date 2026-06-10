<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fuel_issueslip".
 *
 * @property int $id
 * @property int $equipment_id resources pk
 * @property int $project_id
 * @property string $fuel_type
 * @property float $quantity
 * @property string $unit
 * @property string $slip_date
 * @property int $group_id
 * @property int $created_by
 * @property int $delete_status
 */
class FuelIssueslip extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fuel_issueslip';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['equipment_id', 'project_id', 'fuel_type', 'quantity', 'unit', 'slip_date', 'group_id', 'created_by', 'delete_status'], 'required'],
            [['equipment_id', 'project_id', 'group_id', 'created_by', 'delete_status'], 'integer'],
            [['quantity'], 'number'],
            [['slip_date'], 'safe'],
            [['fuel_type'], 'string', 'max' => 20],
            [['unit'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equipment_id' => 'Equipment ID',
            'project_id' => 'Project ID',
            'fuel_type' => 'Fuel Type',
            'quantity' => 'Quantity',
            'unit' => 'Unit',
            'slip_date' => 'Slip Date',
            'group_id' => 'Group ID',
            'created_by' => 'Created By',
            'delete_status' => 'Delete Status',
        ];
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
