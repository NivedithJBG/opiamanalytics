<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "placeorder_res_audit".
 *
 * @property int $id
 * @property int $pricing_resourceid primary key of pricing_estimate_resources_new
 * @property int $edited_by
 * @property string $edited_date
 * @property int $deleted_by
 * @property string $deleted_date
 */
class PlaceorderResAudit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'placeorder_res_audit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pricing_resourceid', 'edited_by', 'deleted_by'], 'required'],
            [['pricing_resourceid', 'edited_by', 'deleted_by'], 'integer'],
            [['edited_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pricing_resourceid' => 'Pricing Resourceid',
            'edited_by' => 'Edited By',
            'edited_date' => 'Edited Date',
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
