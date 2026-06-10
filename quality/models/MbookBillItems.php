<?php

/**
 * This is the model class for table "mbook_bill_items".
 *
 * The followings are the available columns in table 'mbook_bill_items':
 * @property integer $id
 * @property integer $bill_id
 * @property integer $boq_id
 * @property double $qty_executed
 * @property double $qty_previous
 * @property double $rate
 * @property double $upto_date
 * @property double $previous_bill
 * @property double $since_previous_bill
 * @property integer $status
 * @property double $bill_per
 * @property double $net_amount
 */
class MbookBillItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'mbook_bill_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bill_id, boq_id, qty_executed, qty_previous, rate, upto_date, previous_bill, since_previous_bill, status', 'required'),
			array('bill_id, boq_id, status', 'numerical', 'integerOnly'=>true),
			array('qty_executed, qty_previous, rate, upto_date, previous_bill, since_previous_bill', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, bill_id, boq_id, qty_executed, qty_previous, rate, upto_date, previous_bill, since_previous_bill, status', 'safe', 'on'=>'search'),
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
			'bill_id' => 'Bill',
			'boq_id' => 'Boq',
			'qty_executed' => 'Qty Executed',
			'qty_previous' => 'Qty Previous',
			'rate' => 'Rate',
			'upto_date' => 'Upto Date',
			'previous_bill' => 'Previous Bill',
			'since_previous_bill' => 'Since Previous Bill',
			'status' => 'Status',
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
		$criteria->compare('bill_id',$this->bill_id);
		$criteria->compare('boq_id',$this->boq_id);
		$criteria->compare('qty_executed',$this->qty_executed);
		$criteria->compare('qty_previous',$this->qty_previous);
		$criteria->compare('rate',$this->rate);
		$criteria->compare('upto_date',$this->upto_date);
		$criteria->compare('previous_bill',$this->previous_bill);
		$criteria->compare('since_previous_bill',$this->since_previous_bill);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MbookBillItems the static model class
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
