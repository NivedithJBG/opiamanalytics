<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "placeorder_res".
 *
 * @property int $id
 * @property int $cartID
 * @property int $Job_Id
 * @property int $Vendor_Id
 * @property int $Resource_Id
 * @property int $Project
 * @property float $req_qty
 * @property float $reorderqty
 * @property float $reorder_level
 */
class PlaceorderRes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'placeorder_res';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cartID', 'Job_Id', 'Vendor_Id', 'Resource_Id', 'Project', 'req_qty', 'reorderqty', 'reorder_level'], 'required'],
            [['cartID', 'Job_Id', 'Vendor_Id', 'Resource_Id', 'Project'], 'integer'],
            [['req_qty', 'reorderqty', 'reorder_level'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cartID' => 'Cart ID',
            'Job_Id' => 'Job ID',
            'Vendor_Id' => 'Vendor ID',
            'Resource_Id' => 'Resource ID',
            'Project' => 'Project',
            'req_qty' => 'Req Qty',
            'reorderqty' => 'Reorderqty',
            'reorder_level' => 'Reorder Level',
        ];
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
