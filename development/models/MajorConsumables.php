<?php

/**
 * This is the model class for table "major_consumables".
 *
 * The followings are the available columns in table 'major_consumables':
 * @property integer $MC_Id
 * @property integer $MC_Project_Id
 * @property string $MC_Name
 * @property string $MC_Unit
 * @property string $MC_Quantity
 * @property string $MC_Price
 * @property string $MC_Added_On
 * @property integer $MC_Added_By
 * @property string $MC_Updated_On
 * @property integer $MC_Status
 * @property string $MC_amount
 */
class MajorConsumables extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'major_consumables';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('MC_Project_Id, MC_Name, MC_Unit, MC_Quantity, MC_Price, MC_Added_On, MC_Added_By, MC_Updated_On, MC_Status, MC_amount', 'required'),
			array('MC_Project_Id, MC_Added_By, MC_Status', 'numerical', 'integerOnly'=>true),
			array('MC_Name', 'length', 'max'=>50),
			array('MC_Unit, MC_Quantity, MC_Price, MC_amount', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('MC_Id, MC_Project_Id, MC_Name, MC_Unit, MC_Quantity, MC_Price, MC_Added_On, MC_Added_By, MC_Updated_On, MC_Status, MC_amount', 'safe', 'on'=>'search'),
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
			'MC_Id' => 'Mc',
			'MC_Project_Id' => 'Mc Project',
			'MC_Name' => 'Mc Name',
			'MC_Unit' => 'Mc Unit',
			'MC_Quantity' => 'Mc Quantity',
			'MC_Price' => 'Mc Price',
			'MC_Added_On' => 'Mc Added On',
			'MC_Added_By' => 'Mc Added By',
			'MC_Updated_On' => 'Mc Updated On',
			'MC_Status' => 'Mc Status',
			'MC_amount' => 'Mc Amount',
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

		$criteria->compare('MC_Id',$this->MC_Id);
		$criteria->compare('MC_Project_Id',$this->MC_Project_Id);
		$criteria->compare('MC_Name',$this->MC_Name,true);
		$criteria->compare('MC_Unit',$this->MC_Unit,true);
		$criteria->compare('MC_Quantity',$this->MC_Quantity,true);
		$criteria->compare('MC_Price',$this->MC_Price,true);
		$criteria->compare('MC_Added_On',$this->MC_Added_On,true);
		$criteria->compare('MC_Added_By',$this->MC_Added_By);
		$criteria->compare('MC_Updated_On',$this->MC_Updated_On,true);
		$criteria->compare('MC_Status',$this->MC_Status);
		$criteria->compare('MC_amount',$this->MC_amount,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MajorConsumables the static model class
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
