<?php

/**
 * This is the model class for table "work_order".
 *
 * The followings are the available columns in table 'work_order':
 * @property integer $WO_Id
 * @property integer $Project_Id
 * @property integer $WBS_Id
 * @property integer $IOW_Id
 * @property string $Scope
 * @property integer $Item_id
 * @property string $Unit
 * @property string $Quantity
 * @property string $Rate
 * @property string $Total
 * @property string $Taxes
 * @property string $Duration
 * @property string $Payment
 * @property string $Terms
 * @property string $Date_requested
 * @property integer $User_Id
 * @property integer $WO_Status
 * @property string $WO_Address
 * @property double $WO_tax
 * @property integer $WO_Vendor
 * @property string $WO_Approved_Name
 * @property string $WO_Designation
 * @property string $WO_Subject
 * @property string $WO_Function
 * @property string $WO_Folder
 * @property string $WO_assigned
 * @property string $WO_desp_no
 * @property string $WO_desp_date
 */
class WorkOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'work_order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Project_Id, WBS_Id, IOW_Id, Scope, Item_id, Unit, Quantity, Rate, Total, Taxes, Duration, Payment, Terms, Date_requested, User_Id, WO_Status, WO_Address, WO_tax, WO_Vendor, WO_Approved_Name, WO_Designation', 'required'),
			array('Project_Id, WBS_Id, IOW_Id, Item_id, User_Id, WO_Status, WO_Vendor', 'numerical', 'integerOnly'=>true),
			array('WO_tax', 'numerical'),
			array('Scope, Quantity, Rate, Total', 'length', 'max'=>250),
			array('Unit, WO_Approved_Name, WO_Designation', 'length', 'max'=>50),
			array('Taxes, Duration, Payment', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('WO_Id, Project_Id, WBS_Id, IOW_Id, Scope, Item_id, Unit, Quantity, Rate, Total, Taxes, Duration, Payment, Terms, Date_requested, User_Id, WO_Status, WO_Address, WO_tax, WO_Vendor, WO_Approved_Name, WO_Designation', 'safe', 'on'=>'search'),
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
			'WO_Id' => 'Wo',
			'Project_Id' => 'Project',
			'WBS_Id' => 'Wbs',
			'IOW_Id' => 'Iow',
			'Scope' => 'Scope',
			'Item_id' => 'Item',
			'Unit' => 'Unit',
			'Quantity' => 'Quantity',
			'Rate' => 'Rate',
			'Total' => 'Total',
			'Taxes' => 'Taxes',
			'Duration' => 'Duration',
			'Payment' => 'Payment',
			'Terms' => 'Terms',
			'Date_requested' => 'Date Requested',
			'User_Id' => 'User',
			'WO_Status' => 'Wo Status',
			'WO_Address' => 'Wo Address',
			'WO_tax' => 'Wo Tax',
			'WO_Vendor' => 'Wo Vendor',
			'WO_Approved_Name' => 'Wo Approved Name',
			'WO_Designation' => 'Wo Designation',
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

		$criteria->compare('WO_Id',$this->WO_Id);
		$criteria->compare('Project_Id',$this->Project_Id);
		$criteria->compare('WBS_Id',$this->WBS_Id);
		$criteria->compare('IOW_Id',$this->IOW_Id);
		$criteria->compare('Scope',$this->Scope,true);
		$criteria->compare('Item_id',$this->Item_id);
		$criteria->compare('Unit',$this->Unit,true);
		$criteria->compare('Quantity',$this->Quantity,true);
		$criteria->compare('Rate',$this->Rate,true);
		$criteria->compare('Total',$this->Total,true);
		$criteria->compare('Taxes',$this->Taxes,true);
		$criteria->compare('Duration',$this->Duration,true);
		$criteria->compare('Payment',$this->Payment,true);
		$criteria->compare('Terms',$this->Terms,true);
		$criteria->compare('Date_requested',$this->Date_requested,true);
		$criteria->compare('User_Id',$this->User_Id);
		$criteria->compare('WO_Status',$this->WO_Status);
		$criteria->compare('WO_Address',$this->WO_Address,true);
		$criteria->compare('WO_tax',$this->WO_tax);
		$criteria->compare('WO_Vendor',$this->WO_Vendor);
		$criteria->compare('WO_Approved_Name',$this->WO_Approved_Name,true);
		$criteria->compare('WO_Designation',$this->WO_Designation,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WorkOrder the static model class
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
