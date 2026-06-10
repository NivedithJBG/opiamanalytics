<?php

namespace app\models;
use Yii;

/**
 * This is the model class for table "construction".
 *
 * The followings are the available columns in table 'construction':
 * @property integer $Construction_Id
 * @property integer $CO_Project_Id
 * @property string $CO_Name
 * @property string $CO_Unit
 * @property string $CO_Price
 * @property string $CO_Added_On
 * @property integer $CO_Added_By
 * @property string $CO_Updated_On
 * @property integer $CO_Status
 * @property string $CO_Amount
 * @property string $CO_Quantity
 * @property integer $CO_Worktype
 * @property integer $estimate
 * @property integer $enggsortorder
 * @property integer $pricing_status
 */
class Construction extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'construction';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('CO_Project_Id, CO_Name, CO_Unit, CO_Price, CO_Added_On, CO_Added_By, CO_Updated_On, CO_Status, CO_Amount, CO_Quantity', 'required'),
			array('CO_Project_Id, CO_Added_By, CO_Status', 'numerical', 'integerOnly'=>true),
			array('CO_Name', 'length', 'max'=>50),
			array('CO_Unit, CO_Price, CO_Amount, CO_Quantity', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Construction_Id, CO_Project_Id, CO_Name, CO_Unit, CO_Price, CO_Added_On, CO_Added_By, CO_Updated_On, CO_Status, CO_Amount, CO_Quantity', 'safe', 'on'=>'search'),
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
			'Construction_Id' => 'Construction',
			'CO_Project_Id' => 'Co Project',
			'CO_Name' => 'Co Name',
			'CO_Unit' => 'Co Unit',
			'CO_Price' => 'Co Price',
			'CO_Added_On' => 'Co Added On',
			'CO_Added_By' => 'Co Added By',
			'CO_Updated_On' => 'Co Updated On',
			'CO_Status' => 'Co Status',
			'CO_Amount' => 'Co Amount',
			'CO_Quantity' => 'Co Quantity',
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

		$criteria->compare('Construction_Id',$this->Construction_Id);
		$criteria->compare('CO_Project_Id',$this->CO_Project_Id);
		$criteria->compare('CO_Name',$this->CO_Name,true);
		$criteria->compare('CO_Unit',$this->CO_Unit,true);
		$criteria->compare('CO_Price',$this->CO_Price,true);
		$criteria->compare('CO_Added_On',$this->CO_Added_On,true);
		$criteria->compare('CO_Added_By',$this->CO_Added_By);
		$criteria->compare('CO_Updated_On',$this->CO_Updated_On,true);
		$criteria->compare('CO_Status',$this->CO_Status);
		$criteria->compare('CO_Amount',$this->CO_Amount,true);
		$criteria->compare('CO_Quantity',$this->CO_Quantity,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Construction the static model class
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
