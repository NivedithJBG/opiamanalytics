<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "projuser_selection".
 *
 * @property int $id
 * @property int $userid
 * @property int $projectid
 */
class ProjuserSelection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'projuser_selection';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'projectid'], 'required'],
            [['userid', 'projectid'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'projectid' => 'Projectid',
        ];
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
