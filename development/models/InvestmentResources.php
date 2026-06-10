<?php

/**
 * This is the model class for table "investment_resources".
 *
 * The followings are the available columns in table 'investment_resources':
 * @property integer $IR_ID
 * @property integer $IR_Investments_Id
 * @property integer $IR_Resource_Id
 * @property string $IR_Quantity
 * @property string $IR_Price
 * @property string $IR_Added_On
 * @property string $IR_Updated_On
 * @property integer $IR_Added_By
 * @property integer $IR_Status
 * @property string $IR_actual_price
 */
class InvestmentResources extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'investment_resources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('IR_ID, IR_Investments_Id, IR_Resource_Id, IR_Quantity, IR_Price, IR_Added_On, IR_Updated_On, IR_Added_By, IR_Status, IR_actual_price', 'required'),
			array('IR_ID, IR_Investments_Id, IR_Resource_Id, IR_Added_By, IR_Status', 'numerical', 'integerOnly'=>true),
			array('IR_Quantity, IR_Price, IR_actual_price', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('IR_ID, IR_Investments_Id, IR_Resource_Id, IR_Quantity, IR_Price, IR_Added_On, IR_Updated_On, IR_Added_By, IR_Status, IR_actual_price', 'safe', 'on'=>'search'),
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
			'IR_ID' => 'Ir',
			'IR_Investments_Id' => 'Ir Investments',
			'IR_Resource_Id' => 'Ir Resource',
			'IR_Quantity' => 'Ir Quantity',
			'IR_Price' => 'Ir Price',
			'IR_Added_On' => 'Ir Added On',
			'IR_Updated_On' => 'Ir Updated On',
			'IR_Added_By' => 'Ir Added By',
			'IR_Status' => 'Ir Status',
			'IR_actual_price' => 'Ir Actual Price',
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

		$criteria->compare('IR_ID',$this->IR_ID);
		$criteria->compare('IR_Investments_Id',$this->IR_Investments_Id);
		$criteria->compare('IR_Resource_Id',$this->IR_Resource_Id);
		$criteria->compare('IR_Quantity',$this->IR_Quantity,true);
		$criteria->compare('IR_Price',$this->IR_Price,true);
		$criteria->compare('IR_Added_On',$this->IR_Added_On,true);
		$criteria->compare('IR_Updated_On',$this->IR_Updated_On,true);
		$criteria->compare('IR_Added_By',$this->IR_Added_By);
		$criteria->compare('IR_Status',$this->IR_Status);
		$criteria->compare('IR_actual_price',$this->IR_actual_price,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InvestmentResources the static model class
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
