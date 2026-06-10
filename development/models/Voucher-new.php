<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "voucher".
 *
 * @property int $id
 * @property int $place
 * @property int $project_id
 * @property string $voucher_no
 * @property string $date
 * @property string $amount
 * @property int $bank_id
 * @property int $account_id
 * @property int $creditacnt
 * @property string $type
 * @property string $narration
 * @property int $payment
 * @property string $cheque_no
 * @property int $contra
 * @property int $sub_schedule
 * @property int $import
 * @property int $IOW_Id
 * @property int $Resource_Id
 * @property string $contragroup
 */
class Voucher extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'voucher';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'place', 'project_id', 'voucher_no', 'date', 'amount', 'bank_id', 'account_id', 'creditacnt', 'type', 'narration', 'payment', 'cheque_no', 'contra', 'sub_schedule', 'import', 'IOW_Id', 'Resource_Id', 'contragroup'], 'required'],
            [['id', 'place', 'project_id', 'bank_id', 'account_id', 'creditacnt', 'payment', 'contra', 'sub_schedule', 'import', 'IOW_Id', 'Resource_Id'], 'integer'],
            [['date'], 'safe'],
            [['voucher_no', 'type'], 'string', 'max' => 50],
            [['amount'], 'string', 'max' => 20],
            [['narration'], 'string', 'max' => 250],
            [['cheque_no'], 'string', 'max' => 100],
            [['contragroup'], 'string', 'max' => 155],
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
            'place' => 'Place',
            'project_id' => 'Project ID',
            'voucher_no' => 'Voucher No',
            'date' => 'Date',
            'amount' => 'Amount',
            'bank_id' => 'Bank ID',
            'account_id' => 'Account ID',
            'creditacnt' => 'Creditacnt',
            'type' => 'Type',
            'narration' => 'Narration',
            'payment' => 'Payment',
            'cheque_no' => 'Cheque No',
            'contra' => 'Contra',
            'sub_schedule' => 'Sub Schedule',
            'import' => 'Import',
            'IOW_Id' => 'Iow ID',
            'Resource_Id' => 'Resource ID',
            'contragroup' => 'Contragroup',
        ];
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
