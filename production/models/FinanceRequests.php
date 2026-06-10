<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "finance_requests".
 *
 * The followings are the available columns in table 'finance_requests':
 * @property integer $Id
 * @property integer $User_Id
 * @property string $Amount
 * @property string $alloted_amount
 * @property string $project_id
 * @property string $Purpose
 * @property integer $Status
 * @property integer $Approved_By
 * @property string $Requested_On
 * @property string $Approved_On
 * @property string $group_id
 * @property string $payment
 * @property string $date
 * @property string $place
 * @property string $WBS_Id
 * @property string $IOW_Id
 * @property string $payment_type
 * @property string $bill_no
 * @property integer $advance_id
 * @property integer $igst_tax
 * @property integer $purchase_advance
 * @property integer $credit_account
 * @property integer $Contratype

 *
 */
class FinanceRequests extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'finance_requests';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('User_Id, Amount, Purpose, Status, Approved_By, Requested_On', 'required'),
			array('User_Id, Status, Approved_By', 'numerical', 'integerOnly'=>true),
			array('Amount', 'length', 'max'=>20),
			array('Approved_On', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Id, User_Id, Amount,alloted_amount,project_id,Purpose, Status, Approved_By, Requested_On, Approved_On,group_id,payment,date,place,IOW_Id,WBS_Id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'ID',
			'User_Id' => 'User',
			'Amount' => 'Amount',
			'Purpose' => 'Purpose',
			'Status' => 'Status',
			'Approved_By' => 'Approved By',
			'Requested_On' => 'Requested On',
			'Approved_On' => 'Approved On',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('Id',$this->Id);
		$criteria->compare('User_Id',$this->User_Id);
		$criteria->compare('Amount',$this->Amount,true);
		$criteria->compare('Purpose',$this->Purpose,true);
		$criteria->compare('Status',$this->Status);
		$criteria->compare('Approved_By',$this->Approved_By);
		$criteria->compare('Requested_On',$this->Requested_On,true);
		$criteria->compare('Approved_On',$this->Approved_On,true);
		$criteria->compare('IOW_Id',$this->IOW_Id,true);
		$criteria->compare('WBS_Id',$this->WBS_Id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return FinanceRequests the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
