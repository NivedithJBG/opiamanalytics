<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ledger_openingbalance".
 *
 * @property int $id
 * @property int $projectid
 * @property int $accountid
 * @property float $balance
 * @property string $type
 */
class LedgerOpeningbalance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ledger_openingbalance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'projectid', 'accountid', 'balance', 'type'], 'required'],
            [['id', 'projectid', 'accountid'], 'integer'],
            [['balance'], 'number'],
            [['type'], 'string', 'max' => 250],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'projectid' => 'Projectid',
            'accountid' => 'Accountid',
            'balance' => 'Balance',
            'type' => 'Type',
        ];
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
