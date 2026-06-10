<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "placeorder_status".
 *
 * @property int $id
 * @property int $cart_id
 * @property int|null $cancelled_by
 * @property string|null $cancelled_date
 * @property int|null $deleted_by
 * @property string|null $deleted_date
 */
class PlaceorderStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'placeorder_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cart_id'], 'required'],
            [['cart_id', 'cancelled_by', 'deleted_by'], 'integer'],
            [['cancelled_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cart_id' => 'Cart ID',
            'cancelled_by' => 'Cancelled By',
            'cancelled_date' => 'Cancelled Date',
            'deleted_by' => 'Deleted By',
            'deleted_date' => 'Deleted Date',
        ];
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
