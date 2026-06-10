<?php

/**
 * This is the model class for table "boqbillitems".
 *
 * The followings are the available columns in table 'boqbillitems':
 * @property integer $bill_id
 * @property integer $boq_bill_id
 * @property integer $projectid
 * @property integer $itemid
 * @property string $unit
 * @property double $quantity
 * @property double $rate
 * @property double $actual_quantity
 * @property double $status
 */
class Boqbillitems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'boqbillitems';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('boq_bill_id, projectid, itemid, unit, quantity, rate, actual_quantity', 'required'),
			array('boq_bill_id, projectid, itemid', 'numerical', 'integerOnly'=>true),
			array('quantity, rate, actual_quantity', 'numerical'),
			array('unit', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('bill_id, boq_bill_id, projectid, itemid, unit, quantity, rate, actual_quantity', 'safe', 'on'=>'search'),
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
			'boq_bill_id' => 'Boq Bill',
			'projectid' => 'Projectid',
			'itemid' => 'Itemid',
			'unit' => 'Unit',
			'quantity' => 'Quantity',
			'rate' => 'Rate',
			'actual_quantity' => 'Actual Quantity',
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
		$criteria->compare('boq_bill_id',$this->boq_bill_id);
		$criteria->compare('projectid',$this->projectid);
		$criteria->compare('itemid',$this->itemid);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('rate',$this->rate);
		$criteria->compare('actual_quantity',$this->actual_quantity);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Boqbillitems the static model class
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
