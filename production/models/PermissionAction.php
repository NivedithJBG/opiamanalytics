<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "permission_action".
 *
 * @property int $action_id
 * @property int $tab_id
 * @property string $controller_id
 * @property string $action_id_str
 * @property string $micro_key
 * @property string $label
 * @property int $is_project_scopable
 * @property int $enforced
 * @property int $Delete_status
 */
class PermissionAction extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'permission_action';
    }

    public function rules()
    {
        return [
            [['tab_id', 'controller_id', 'action_id_str', 'label'], 'required'],
            [['tab_id', 'is_project_scopable', 'enforced', 'Delete_status'], 'integer'],
            [['controller_id', 'action_id_str', 'micro_key'], 'string', 'max' => 64],
            [['label'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'action_id'           => 'Action ID',
            'tab_id'               => 'Tab ID',
            'controller_id'        => 'Controller',
            'action_id_str'        => 'Action',
            'micro_key'            => 'Micro Key',
            'label'                => 'Label',
            'is_project_scopable'  => 'Project Scopable',
            'enforced'             => 'Enforced',
            'Delete_status'        => 'Delete Status',
        ];
    }

    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
