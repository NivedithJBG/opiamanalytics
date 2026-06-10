<?php

/**
 * This is the model class for table "bills".
 *
 * The followings are the available columns in table 'bills':
 * @property integer $bill_id
 * @property string $billno
 * @property string $party
 * @property string $purpose
 * @property string $amount
 * @property string $unit
 * @property string $rate
 * @property string $quantity
 * @property string $date
 * @property string $duedate
 * @property string $projectid
 * @property integer $User_Id
 * @property integer $Status
 * @property integer $groupid
 * @property integer $Approved_By
 * @property string $Requested_On
 * @property string $Approved_On
 * @property integer $payment
 * @property integer $place
 * @property integer $bill_type
 * @property string $bill_Invoice_Number
 * @property string $bill_Tin_No
 * @property string $bill_CST_Reg_No
 * @property string $bill_WO_No
 * @property string $bill_Period
 * @property string $bill_Total_amount
 * @property string $bill_Tax_Percentage
 * @property string $bill_TDS_Percentage
 * @property string $bill_Retention_Percentage
 * @property string $bill_Other_Deductions
 * @property string $Bill_period_start
 * @property string $Bill_period_end
 * @property string $bill_Deduction
 * @property string $bill_Purchase_Order
 * @property string $bill_search_status
 * @property string $bill_purpose
 * @property string $bill_travel_amount
 * @property string $bill_Boarding_amount
 * @property string $bill_Lodging_amount
 * @property string $bill_party
 */
class Bills extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'bills';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('billno, party, purpose, amount, date, duedate, User_Id, Status, Approved_By, Requested_On, payment,bill_type', 'required'),
			array('User_Id, Status, Approved_By, payment,bill_type', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('billno, party', 'length', 'max'=>250),
			array('Approved_On', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('bill_id, billno, party,date, duedate, User_Id, Status, Approved_By, Requested_On, Approved_On, payment,projectid,groupid,place,bill_type,bill_Invoice_Number,bill_Tin_No,bill_CST_Reg_No,bill_Period,bill_WO_No,bill_Total_amount,bill_Tax_Percentage,bill_TDS_Percentage,bill_Retention_Percentage,bill_Other_Deductions,Bill_period_start,Bill_period_end,bill_Deduction', 'safe', 'on'=>'search'),
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
			'bill_id' => 'Bill',
			'billno' => 'Billno',
			'party' => 'Party',
			'purpose' => 'Purpose',
			'amount' => 'Amount',
			'date' => 'Date',
			'duedate' => 'Duedate',
			'User_Id' => 'User',
			'Status' => 'Status',
			'Approved_By' => 'Approved By',
			'Requested_On' => 'Requested On',
			'Approved_On' => 'Approved On',
			'payment' => 'Payment',
			'bill_type' => 'Bill Type',
			'bill_Invoice_Number' => 'Bill Invoce Number',
			'bill_Tin_No' => 'Bill Tin NO',
			'bill_CST_Reg_No' => 'Bill CST RegNo',
			'bill_Period' => 'Bill Period',
			'bill_WO_No' => 'Bill WO No',
			'bill_Total_amount' => 'Bill Total Amount',
			'bill_Tax_Percentage' => 'Tax Percentage',
			'bill_Retention_Percentage' => 'Retention Percentage',
			'bill_Other_Deductions' => 'Other Deductions',
			'Bill_period_start' => 'start',
			'Bill_period_start' => 'end',
			'bill_Deduction' => 'Deduction Account',
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

		$criteria->compare('bill_id',$this->bill_id);
		$criteria->compare('billno',$this->billno,true);
		$criteria->compare('party',$this->party,true);
		$criteria->compare('purpose',$this->purpose,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('duedate',$this->duedate,true);
		$criteria->compare('User_Id',$this->User_Id);
		$criteria->compare('Status',$this->Status);
		$criteria->compare('Approved_By',$this->Approved_By);
		$criteria->compare('Requested_On',$this->Requested_On,true);
		$criteria->compare('Approved_On',$this->Approved_On,true);
		$criteria->compare('payment',$this->payment);
		$criteria->compare('bill_type',$this->bill_type);
		$criteria->compare('bill_Invoice_Number',$this->bill_Invoice_Number);
		$criteria->compare('bill_Tin_No',$this->bill_Tin_No);
		$criteria->compare('bill_CST_Reg_No',$this->bill_CST_Reg_No);
		$criteria->compare('bill_Period',$this->bill_Period);
		$criteria->compare('bill_WO_No',$this->bill_WO_No);
		$criteria->compare('bill_Total_amount',$this->bill_Total_amount);
		$criteria->compare('bill_Tax_Percentage',$this->bill_Tax_Percentage);
		$criteria->compare('bill_Retention_Percentage',$this->bill_Retention_Percentage);
		$criteria->compare('bill_Other_Deductions',$this->bill_Other_Deductions);
		$criteria->compare('Bill_period_start',$this->Bill_period_start);
		$criteria->compare('Bill_period_start',$this->Bill_period_start);
		$criteria->compare('bill_Deduction',$this->bill_Deduction);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Bills the static model class
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
