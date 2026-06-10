<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "terms_condtns_content".
 *
 * @property int $id
 * @property int $termsid primary key of terms_condtns table
 * @property string $content
 * @property int $status
 */
class TermsCondtnsContent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'terms_condtns_content';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['termsid', 'content', 'status'], 'required'],
            [['termsid', 'status'], 'integer'],
            [['content'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'termsid' => 'Termsid',
            'content' => 'Content',
            'status' => 'Status',
        ];
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
