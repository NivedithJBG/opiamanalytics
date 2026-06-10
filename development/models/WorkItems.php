<?php

/**
 * This is the model class for table "work_items".
 *
 * The followings are the available columns in table 'work_items':
 * @property integer $WI_Id
 * @property integer $WI_Item
 * @property integer $WI_WBS
 * @property string $WI_Unit
 * @property string $WI_Rate
 * @property string $WI_Quantity
 * @property string $WI_Amount
 * @property integer $WI_Status
 * @property integer $WI_WO_Id
 * @property integer $Resource_Id
 * @property integer $WO_Details
 * @property integer $WI_bill_Quantity
 */
class WorkItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'work_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('WI_Item, WI_WBS, WI_Unit, WI_Rate, WI_Quantity, WI_Amount, WI_Status, WI_WO_Id, Resource_Id', 'required'),
			array('WI_Item, WI_WBS, WI_Status, WI_WO_Id, Resource_Id', 'numerical', 'integerOnly'=>true),
			array('WI_Unit, WI_Rate, WI_Quantity, WI_Amount', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('WI_Id, WI_Item, WI_WBS, WI_Unit, WI_Rate, WI_Quantity, WI_Amount, WI_Status, WI_WO_Id, Resource_Id', 'safe', 'on'=>'search'),
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
			'WI_Id' => 'Wi',
			'WI_Item' => 'Wi Item',
			'WI_WBS' => 'Wi Wbs',
			'WI_Unit' => 'Wi Unit',
			'WI_Rate' => 'Wi Rate',
			'WI_Quantity' => 'Wi Quantity',
			'WI_Amount' => 'Wi Amount',
			'WI_Status' => 'Wi Status',
			'WI_WO_Id' => 'Wi Wo',
			'Resource_Id' => 'Resource',
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

		$criteria->compare('WI_Id',$this->WI_Id);
		$criteria->compare('WI_Item',$this->WI_Item);
		$criteria->compare('WI_WBS',$this->WI_WBS);
		$criteria->compare('WI_Unit',$this->WI_Unit,true);
		$criteria->compare('WI_Rate',$this->WI_Rate,true);
		$criteria->compare('WI_Quantity',$this->WI_Quantity,true);
		$criteria->compare('WI_Amount',$this->WI_Amount,true);
		$criteria->compare('WI_Status',$this->WI_Status);
		$criteria->compare('WI_WO_Id',$this->WI_WO_Id);
		$criteria->compare('Resource_Id',$this->Resource_Id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WorkItems the static model class
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
