<?php

namespace app\models;
use Yii;


/**
 * This is the model class for table "cashadvance".
 *
 * The followings are the available columns in table 'cashadvance':
 * @property integer $cashadvance_id
 * @property string $date
 * @property double $amount
 * @property string $purpose
 * @property integer $created_by
 * @property integer $status
 * @property integer $activity
 * @property integer $accounthead
 * @property integer $project_id
 * @property integer $voucher
 * @property integer $group_id
 * @property integer $paymenttype
 * @property integer $delete_status
 * @property integer $expense_status
 * @property integer $app_group_id
 */
class Cashadvance extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $total;

	public static function tableName()
	{
		return 'cashadvance';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, amount, purpose, created_by, status, activity, accounthead, project_id', 'required'),
			array('created_by, status, activity, accounthead, project_id', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('purpose', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('cashadvance_id, date, amount, purpose, created_by, status, activity, accounthead, project_id', 'safe', 'on'=>'search'),
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
			'cashadvance_id' => 'Cashadvance',
			'date' => 'Date',
			'amount' => 'Amount',
			'purpose' => 'Purpose',
			'created_by' => 'Created By',
			'status' => 'Status',
			'activity' => 'Activity',
			'accounthead' => 'Accounthead',
			'project_id' => 'Project',
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

		$criteria->compare('cashadvance_id',$this->cashadvance_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('purpose',$this->purpose,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('status',$this->status);
		$criteria->compare('activity',$this->activity);
		$criteria->compare('accounthead',$this->accounthead);
		$criteria->compare('project_id',$this->project_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cashadvance the static model class
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
