<?php

/**
 * This is the model class for table "purchase_order".
 *
 * The followings are the available columns in table 'purchase_order':
 * @property integer $PO_Id
 * @property integer $PO_Project_Id
 * @property string $PO_Date
 * @property integer $PO_Status
 * @property string $PO_Letter_Body
 * @property string $PO_taxes
 * @property string $PO_Delivery
 * @property string $PO_Lead_Time
 * @property string $PO_Payment
 * @property string $PO_Contact
 * @property string $PO_Terms
 * @property string $PO_Item
 * @property string $PO_Quantity
 * @property string $PO_Rate
 * @property integer $PO_User_Id
 * @property integer $PO_Resource_Type
 * @property integer $PO_Resource_Id
 * @property string $PO_Unit
 * @property string $PO_Total
 * @property string $PO_Address
 * @property integer $PO_Vendor
 * @property string $PO_Approved_Name
 * @property string $PO_Designation
 * @property string $PO_Number
 * @property integer $PO_Payment_Type
 * @property integer $PO_Subject
 * @property integer $PO_Function
 * @property integer $PO_Folder
 * @property integer $PO_assigned
 * @property integer $PO_des_no
 * @property integer $PO_des_date

 */
class PurchaseOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'purchase_order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('PO_Project_Id, PO_Date, PO_Status, PO_Letter_Body, PO_taxes, PO_Delivery, PO_Lead_Time, PO_Payment, PO_Contact, PO_Terms, PO_Item, PO_Quantity, PO_Rate, PO_User_Id, PO_Resource_Type, PO_Resource_Id, PO_Unit, PO_Total, PO_Address, PO_Vendor, PO_Approved_Name, PO_Designation, PO_Number, PO_Payment_Type', 'required'),
			array('PO_Project_Id, PO_Status, PO_User_Id, PO_Resource_Type, PO_Resource_Id, PO_Vendor, PO_Payment_Type', 'numerical', 'integerOnly'=>true),
			array('PO_taxes, PO_Item', 'length', 'max'=>20),
			array('PO_Delivery, PO_Lead_Time, PO_Payment, PO_Contact, PO_Unit, PO_Total, PO_Approved_Name, PO_Designation, PO_Number', 'length', 'max'=>50),
			array('PO_Quantity, PO_Rate', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('PO_Id, PO_Project_Id, PO_Date, PO_Status, PO_Letter_Body, PO_taxes, PO_Delivery, PO_Lead_Time, PO_Payment, PO_Contact, PO_Terms, PO_Item, PO_Quantity, PO_Rate, PO_User_Id, PO_Resource_Type, PO_Resource_Id, PO_Unit, PO_Total, PO_Address, PO_Vendor, PO_Approved_Name, PO_Designation, PO_Number, PO_Payment_Type', 'safe', 'on'=>'search'),
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
			'PO_Id' => 'Po',
			'PO_Project_Id' => 'Po Project',
			'PO_Date' => 'Po Date',
			'PO_Status' => 'Po Status',
			'PO_Letter_Body' => 'Po Letter Body',
			'PO_taxes' => 'Po Taxes',
			'PO_Delivery' => 'Po Delivery',
			'PO_Lead_Time' => 'Po Lead Time',
			'PO_Payment' => 'Po Payment',
			'PO_Contact' => 'Po Contact',
			'PO_Terms' => 'Po Terms',
			'PO_Item' => 'Po Item',
			'PO_Quantity' => 'Po Quantity',
			'PO_Rate' => 'Po Rate',
			'PO_User_Id' => 'Po User',
			'PO_Resource_Type' => 'Po Resource Type',
			'PO_Resource_Id' => 'Po Resource',
			'PO_Unit' => 'Po Unit',
			'PO_Total' => 'Po Total',
			'PO_Address' => 'Po Address',
			'PO_Vendor' => 'Po Vendor',
			'PO_Approved_Name' => 'Po Approved Name',
			'PO_Designation' => 'Po Designation',
			'PO_Number' => 'Po Number',
			'PO_Payment_Type' => 'Po Payment Type',
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

		$criteria->compare('PO_Id',$this->PO_Id);
		$criteria->compare('PO_Project_Id',$this->PO_Project_Id);
		$criteria->compare('PO_Date',$this->PO_Date,true);
		$criteria->compare('PO_Status',$this->PO_Status);
		$criteria->compare('PO_Letter_Body',$this->PO_Letter_Body,true);
		$criteria->compare('PO_taxes',$this->PO_taxes,true);
		$criteria->compare('PO_Delivery',$this->PO_Delivery,true);
		$criteria->compare('PO_Lead_Time',$this->PO_Lead_Time,true);
		$criteria->compare('PO_Payment',$this->PO_Payment,true);
		$criteria->compare('PO_Contact',$this->PO_Contact,true);
		$criteria->compare('PO_Terms',$this->PO_Terms,true);
		$criteria->compare('PO_Item',$this->PO_Item,true);
		$criteria->compare('PO_Quantity',$this->PO_Quantity,true);
		$criteria->compare('PO_Rate',$this->PO_Rate,true);
		$criteria->compare('PO_User_Id',$this->PO_User_Id);
		$criteria->compare('PO_Resource_Type',$this->PO_Resource_Type);
		$criteria->compare('PO_Resource_Id',$this->PO_Resource_Id);
		$criteria->compare('PO_Unit',$this->PO_Unit,true);
		$criteria->compare('PO_Total',$this->PO_Total,true);
		$criteria->compare('PO_Address',$this->PO_Address,true);
		$criteria->compare('PO_Vendor',$this->PO_Vendor);
		$criteria->compare('PO_Approved_Name',$this->PO_Approved_Name,true);
		$criteria->compare('PO_Designation',$this->PO_Designation,true);
		$criteria->compare('PO_Number',$this->PO_Number,true);
		$criteria->compare('PO_Payment_Type',$this->PO_Payment_Type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PurchaseOrder the static model class
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
