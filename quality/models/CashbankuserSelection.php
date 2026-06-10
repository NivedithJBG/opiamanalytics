<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cashbankuser_selection".
 *
 * @property int $id
 * @property int $userid
 * @property int $account_typeid
 * @property int $accountid
 */
class CashbankuserSelection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cashbankuser_selection';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'account_typeid', 'accountid'], 'required'],
            [['userid', 'account_typeid', 'accountid'], 'integer'],
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
            'account_typeid' => 'Account Typeid',
            'accountid' => 'Accountid',
        ];
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
