<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_permission_overrides".
 *
 * @property int $override_id
 * @property int $user_id
 * @property int $action_id
 * @property int $project_id
 * @property string $effect
 * @property int $created_by
 * @property string $created_at
 * @property string $notes
 */
class UserPermissionOverride extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'user_permission_overrides';
    }

    public function rules()
    {
        return [
            [['user_id', 'action_id', 'effect', 'created_by'], 'required'],
            [['user_id', 'action_id', 'project_id', 'created_by'], 'integer'],
            [['effect'], 'string'],
            [['created_at'], 'safe'],
            [['notes'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'override_id' => 'Override ID',
            'user_id'     => 'User ID',
            'action_id'   => 'Action ID',
            'project_id'  => 'Project ID',
            'effect'      => 'Effect',
            'created_by'  => 'Created By',
            'created_at'  => 'Created At',
            'notes'       => 'Notes',
        ];
    }

    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
