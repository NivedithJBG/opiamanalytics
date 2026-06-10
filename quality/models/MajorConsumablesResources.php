<?php

/**
 * This is the model class for table "major_consumables_resources".
 *
 * The followings are the available columns in table 'major_consumables_resources':
 * @property integer $MCR_Id
 * @property integer $MCR_MC_Id
 * @property integer $MCR_Resource_Id
 * @property integer $MCR_Quantity
 * @property integer $MCR_Price
 * @property integer $MCR_Unit
 * @property integer $MCR_Added_On
 * @property integer $MCR_Updated_On
 * @property integer $MCR_Status
 * @property integer $MCR_Actual_price
 */
class MajorConsumablesResources extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'major_consumables_resources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('MCR_MC_Id, MCR_Resource_Id, MCR_Quantity, MCR_Price, MCR_Unit, MCR_Added_On, MCR_Updated_On, MCR_Status, MCR_Actual_price', 'required'),
			array('MCR_MC_Id, MCR_Resource_Id, MCR_Quantity, MCR_Price, MCR_Unit, MCR_Added_On, MCR_Updated_On, MCR_Status, MCR_Actual_price', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('MCR_Id, MCR_MC_Id, MCR_Resource_Id, MCR_Quantity, MCR_Price, MCR_Unit, MCR_Added_On, MCR_Updated_On, MCR_Status, MCR_Actual_price', 'safe', 'on'=>'search'),
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
			'MCR_Id' => 'Mcr',
			'MCR_MC_Id' => 'Mcr Mc',
			'MCR_Resource_Id' => 'Mcr Resource',
			'MCR_Quantity' => 'Mcr Quantity',
			'MCR_Price' => 'Mcr Price',
			'MCR_Unit' => 'Mcr Unit',
			'MCR_Added_On' => 'Mcr Added On',
			'MCR_Updated_On' => 'Mcr Updated On',
			'MCR_Status' => 'Mcr Status',
			'MCR_Actual_price' => 'Mcr Actual Price',
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

		$criteria->compare('MCR_Id',$this->MCR_Id);
		$criteria->compare('MCR_MC_Id',$this->MCR_MC_Id);
		$criteria->compare('MCR_Resource_Id',$this->MCR_Resource_Id);
		$criteria->compare('MCR_Quantity',$this->MCR_Quantity);
		$criteria->compare('MCR_Price',$this->MCR_Price);
		$criteria->compare('MCR_Unit',$this->MCR_Unit);
		$criteria->compare('MCR_Added_On',$this->MCR_Added_On);
		$criteria->compare('MCR_Updated_On',$this->MCR_Updated_On);
		$criteria->compare('MCR_Status',$this->MCR_Status);
		$criteria->compare('MCR_Actual_price',$this->MCR_Actual_price);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MajorConsumablesResources the static model class
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
