<?php

/**
 * This is the model class for table "work_order_report".
 *
 * The followings are the available columns in table 'work_order_report':
 * @property integer $WOR_Id
 * @property integer $WOR_Project_Id
 * @property integer $WOR_Contractor_Id
 * @property string $WOR_WO_No
 * @property integer $WOR_Cycle_No
 * @property integer $WOR_Item_id
 * @property string $WOR_Unit
 * @property string $WOR_Rate
 * @property string $WOR_Quantity
 * @property string $WOR_Amount
 * @property integer $WOR_Status
 * @property integer $WOR_User_Id
 * @property integer $WOR_Date
 */
class WorkOrderReport extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'work_order_report';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('WOR_Project_Id, WOR_Contractor_Id, WOR_WO_No, WOR_Cycle_No, WOR_Item_id, WOR_Unit, WOR_Rate, WOR_Quantity, WOR_Amount, WOR_Status', 'required'),
			array('WOR_Project_Id, WOR_Contractor_Id, WOR_Cycle_No, WOR_Item_id, WOR_Status', 'numerical', 'integerOnly'=>true),
			array('WOR_WO_No, WOR_Quantity', 'length', 'max'=>20),
			array('WOR_Unit, WOR_Rate, WOR_Amount', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('WOR_Id, WOR_Project_Id, WOR_Contractor_Id, WOR_WO_No, WOR_Cycle_No, WOR_Item_id, WOR_Unit, WOR_Rate, WOR_Quantity, WOR_Amount, WOR_Status', 'safe', 'on'=>'search'),
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
			'WOR_Id' => 'Wor',
			'WOR_Project_Id' => 'Wor Project',
			'WOR_Contractor_Id' => 'Wor Contractor',
			'WOR_WO_No' => 'Wor Wo No',
			'WOR_Cycle_No' => 'Wor Cycle No',
			'WOR_Item_id' => 'Wor Item',
			'WOR_Unit' => 'Wor Unit',
			'WOR_Rate' => 'Wor Rate',
			'WOR_Quantity' => 'Wor Quantity',
			'WOR_Amount' => 'Wor Amount',
			'WOR_Status' => 'Wor Status',
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

		$criteria->compare('WOR_Id',$this->WOR_Id);
		$criteria->compare('WOR_Project_Id',$this->WOR_Project_Id);
		$criteria->compare('WOR_Contractor_Id',$this->WOR_Contractor_Id);
		$criteria->compare('WOR_WO_No',$this->WOR_WO_No,true);
		$criteria->compare('WOR_Cycle_No',$this->WOR_Cycle_No);
		$criteria->compare('WOR_Item_id',$this->WOR_Item_id);
		$criteria->compare('WOR_Unit',$this->WOR_Unit,true);
		$criteria->compare('WOR_Rate',$this->WOR_Rate,true);
		$criteria->compare('WOR_Quantity',$this->WOR_Quantity,true);
		$criteria->compare('WOR_Amount',$this->WOR_Amount,true);
		$criteria->compare('WOR_Status',$this->WOR_Status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WorkOrderReport the static model class
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
