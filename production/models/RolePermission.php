<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "role_permissions".
 *
 * @property int $role_perm_id
 * @property int $role_id
 * @property int $action_id
 * @property string $effect
 * @property int $created_by
 * @property string $created_at
 */
class RolePermission extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'role_permissions';
    }

    public function rules()
    {
        return [
            [['role_id', 'action_id'], 'required'],
            [['role_id', 'action_id', 'created_by'], 'integer'],
            [['effect'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'role_perm_id' => 'Role Perm ID',
            'role_id'      => 'Role ID',
            'action_id'    => 'Action ID',
            'effect'       => 'Effect',
            'created_by'   => 'Created By',
            'created_at'   => 'Created At',
        ];
    }

    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
