<?php

/**
 * This is the model class for table "expense_statement".
 *
 * The followings are the available columns in table 'expense_statement':
 * @property integer $expense_id
 * @property string $date
 * @property double $amount
 * @property string $purpose
 * @property integer $created_by
 * @property integer $status
 * @property integer $accounthead
 * @property integer $project_id
 * @property integer $voucher
 * @property integer $group_id
 * @property integer $paymenttype
 * @property integer $delete_status
 * @property integer $advance_group
 */
class ExpenseStatement extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ExpenseStatement the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'expense_statement';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, amount, purpose, created_by, status, accounthead, project_id, voucher, group_id, paymenttype', 'required'),
			array('created_by, status, accounthead, project_id, voucher, group_id, paymenttype', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('purpose', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('expense_id, date, amount, purpose, created_by, status, accounthead, project_id, voucher, group_id, paymenttype', 'safe', 'on'=>'search'),
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
			'expense_id' => 'Expense',
			'date' => 'Date',
			'amount' => 'Amount',
			'purpose' => 'Purpose',
			'created_by' => 'Created By',
			'status' => 'Status',
			'accounthead' => 'Accounthead',
			'project_id' => 'Project',
			'voucher' => 'Voucher',
			'group_id' => 'Group',
			'paymenttype' => 'Paymenttype',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('expense_id',$this->expense_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('purpose',$this->purpose,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('status',$this->status);
		$criteria->compare('accounthead',$this->accounthead);
		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('voucher',$this->voucher);
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('paymenttype',$this->paymenttype);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}