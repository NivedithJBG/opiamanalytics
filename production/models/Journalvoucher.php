<?php

namespace app\models;
use Yii;

/**
 * This is the model class for table "journalvoucher".
 *
 * The followings are the available columns in table 'journalvoucher':
 * @property integer $id
 * @property string $place
 * @property integer $project_id
 * @property string $voucher_no
 * @property string $narration
 * @property string $date
 * @property string $duedate
 * @property integer $debitacnt
 * @property integer $creditacnt
 * @property double $amount
 * @property integer $payment
 * @property integer $type
 * @property integer $group_id
 */
class Journalvoucher extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'journalvoucher';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('place, project_id, voucher_no, date, duedate, debitacnt, creditacnt, amount, payment', 'required'),
			array('project_id, debitacnt, creditacnt, payment', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('place', 'length', 'max'=>250),
			array('voucher_no', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, place, project_id, voucher_no, date, duedate, debitacnt, creditacnt, amount, payment,narration,type', 'safe', 'on'=>'search'),
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
			'duedate' => 'Duedate',
			'debitacnt' => 'Debitacnt',
			'creditacnt' => 'Creditacnt',
			'amount' => 'Amount',
			'payment' => 'Payment',
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
		$criteria->compare('voucher_no',$this->voucher_no,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('duedate',$this->duedate,true);
		$criteria->compare('debitacnt',$this->debitacnt);
		$criteria->compare('creditacnt',$this->creditacnt);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('payment',$this->payment);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Journalvoucher the static model class
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
