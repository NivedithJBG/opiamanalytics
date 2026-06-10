<?php

namespace app\models;
use Yii;


/**
 * This is the model class for table "voucher".
 *
 * The followings are the available columns in table 'voucher':
 * @property integer $id
 * @property string $place
 * @property integer $project_id
 * @property integer $voucher_no
 * @property integer $cheque_no
 * @property string $date
 * @property string $amount
 * @property string $bank_id
 * @property integer $account_id
 * @property integer $creditacnt
 * @property integer $type
 * @property integer $narration
 * @property integer $payment
 * @property integer $contra
 * @property integer $sub_schedule
 * @property integer $IOW_Id
 * @property integer $Resource_Id
 * @property integer $contragroup
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
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('account_id','required'),
			//array('place, project_id, voucher_no, date, amount, account_type, account_id', 'required'),
			//array('project_id, voucher_no, account_id', 'numerical', 'integerOnly'=>true),
			array('place', 'length', 'max'=>250),
			array('amount, account_type', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, place, project_id, voucher_no, date, amount, bank_id, account_id,creditacnt,type,narration,payment,cheque_no,contra,sub_schedule,Resource_Id,IOW_Id', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'place' => 'Place',
			'project_id' => 'Project',
			'voucher_no' => 'Voucher No',
			'date' => 'Date',
			'amount' => 'Amount',
			'account_type' => 'Account Type',
			'account_id' => 'Account',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('place',$this->place,true);
		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('voucher_no',$this->voucher_no);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('account_type',$this->account_type,true);
		$criteria->compare('account_id',$this->account_id);
		$criteria->compare('Resource_Id',$this->Resource_Id);
		$criteria->compare('IOW_Id',$this->IOW_Id);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Voucher the static model class
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
