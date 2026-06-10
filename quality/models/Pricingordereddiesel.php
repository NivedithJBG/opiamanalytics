<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pricing_ordered_diesel".
 *
 * @property int $id
 * @property int $cartid
 * @property int $pricing_res_id
 */
class Pricingordereddiesel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pricing_ordered_diesel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cartid', 'pricing_res_id'], 'required'],
            [['cartid', 'pricing_res_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cartid' => 'Cartid',
            'pricing_res_id' => 'Pricing Res ID',
        ];
    }
}
