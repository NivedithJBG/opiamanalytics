<?php

/**
 * This is the model class for table "purchase_items".
 *
 * The followings are the available columns in table 'purchase_items':
 * @property integer $PI_ID
 * @property integer $PI_Resource_Type
 * @property integer $PI_Resource_Id
 * @property string $PI_Unit
 * @property string $PI_Rate
 * @property string $PI_Quantity
 * @property string $PI_Total
 * @property integer $PI_PO_Id
 * @property integer $PI_Status
 * @property integer $PO_ItemId
 * @property string $PO_Details
 * @property string $PI_bill_quantity
 * @property string $PI_Tax
 */
class PurchaseItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'purchase_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('PI_Resource_Type, PI_Resource_Id, PI_Unit, PI_Rate, PI_Quantity, PI_Total, PI_PO_Id, PI_Status, PO_ItemId, PO_Details', 'required'),
			array('PI_Resource_Type, PI_Resource_Id, PI_PO_Id, PI_Status, PO_ItemId', 'numerical', 'integerOnly'=>true),
			array('PI_Unit, PI_Rate, PI_Quantity, PI_Total', 'length', 'max'=>20),
			array('PO_Details', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('PI_ID, PI_Resource_Type, PI_Resource_Id, PI_Unit, PI_Rate, PI_Quantity, PI_Total, PI_PO_Id, PI_Status, PO_ItemId, PO_Details', 'safe', 'on'=>'search'),
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
			'PI_ID' => 'Pi',
			'PI_Resource_Type' => 'Pi Resource Type',
			'PI_Resource_Id' => 'Pi Resource',
			'PI_Unit' => 'Pi Unit',
			'PI_Rate' => 'Pi Rate',
			'PI_Quantity' => 'Pi Quantity',
			'PI_Total' => 'Pi Total',
			'PI_PO_Id' => 'Pi Po',
			'PI_Status' => 'Pi Status',
			'PO_ItemId' => 'Po Item',
			'PO_Details' => 'Po Details',
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

		$criteria->compare('PI_ID',$this->PI_ID);
		$criteria->compare('PI_Resource_Type',$this->PI_Resource_Type);
		$criteria->compare('PI_Resource_Id',$this->PI_Resource_Id);
		$criteria->compare('PI_Unit',$this->PI_Unit,true);
		$criteria->compare('PI_Rate',$this->PI_Rate,true);
		$criteria->compare('PI_Quantity',$this->PI_Quantity,true);
		$criteria->compare('PI_Total',$this->PI_Total,true);
		$criteria->compare('PI_PO_Id',$this->PI_PO_Id);
		$criteria->compare('PI_Status',$this->PI_Status);
		$criteria->compare('PO_ItemId',$this->PO_ItemId);
		$criteria->compare('PO_Details',$this->PO_Details,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PurchaseItems the static model class
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
