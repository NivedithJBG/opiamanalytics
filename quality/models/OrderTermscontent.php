<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_termscontent".
 *
 * @property int $id
 * @property int $orderid
 * @property int $termsid
 * @property string $termscontent
 */
class OrderTermscontent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_termscontent';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['orderid', 'termsid', 'termscontent'], 'required'],
            [['orderid', 'termsid'], 'integer'],
            [['termscontent'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orderid' => 'Orderid',
            'termsid' => 'Termsid',
            'termscontent' => 'Termscontent',
        ];
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
