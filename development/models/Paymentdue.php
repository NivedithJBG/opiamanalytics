<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "paymentdue".
 *
 * @property int $id
 * @property int $order_id
 * @property int $order_type
 * @property string $order_number
 * @property int $vendor_id
 * @property string $amount
 * @property string $duedate
 * @property string $order_date
 */
class Paymentdue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paymentdue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_type', 'order_number', 'vendor_id', 'amount', 'duedate', 'order_date'], 'required'],
            [['order_id', 'order_type', 'vendor_id'], 'integer'],
            [['duedate', 'order_date'], 'safe'],
            [['order_number', 'amount'], 'string', 'max' => 11],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'order_type' => 'Order Type',
            'order_number' => 'Order Number',
            'vendor_id' => 'Vendor ID',
            'amount' => 'Amount',
            'duedate' => 'Duedate',
            'order_date' => 'Order Date',
        ];
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
