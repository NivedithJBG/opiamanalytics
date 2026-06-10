<?php

/**
 * This is the model class for table "billitems".
 *
 * The followings are the available columns in table 'billitems':
 * @property integer $itemid
 * @property integer $billid
 * @property string $item
 * @property string $unit
 * @property double $rate
 * @property double $quantity
 * @property integer $amount
 * @property integer $item_Project_Id
 * @property integer $item_Tax_Amount
 * @property integer $item_Party
 * @property integer $startdate
 * @property integer $enddate
 * @property integer $travelmode
 */
class Billitems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'billitems';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('billid, item, unit, rate, quantity, amount,item_Project_Id', 'required'),
			array('billid, amount', 'numerical', 'integerOnly'=>true),
			array('rate, quantity', 'numerical'),
			array('item, unit', 'length', 'max'=>250),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('itemid, billid, item, unit, rate, quantity, amount,item_Project_Id,item_Tax_Amount,item_Party', 'safe', 'on'=>'search'),
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
			'itemid' => 'Itemid',
			'billid' => 'Billid',
			'item' => 'Item',
			'unit' => 'Unit',
			'rate' => 'Rate',
			'quantity' => 'Quantity',
			'amount' => 'Amount',
			'Project' => 'item_Project_Id',
			'Tax Amount' => 'item_Tax_Amount',
			'Party' => 'item_Party',
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

		$criteria->compare('itemid',$this->itemid);
		$criteria->compare('billid',$this->billid);
		$criteria->compare('item',$this->item,true);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('rate',$this->rate);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('item_Project_Id',$this->item_Project_Id);
		$criteria->compare('item_Tax_Amount',$this->item_Tax_Amount);
		$criteria->compare('item_Party',$this->item_Party);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Billitems the static model class
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
