<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "construction_resources".
 *
 * The followings are the available columns in table 'construction_resources':
 * @property integer $COR_Id
 * @property integer $COR_CO_Id
 * @property integer $COR_Resource_Id
 * @property string $COR_Unit
 * @property string $COR_Price
 * @property string $COR_Qunatity
 * @property integer $COR_Added_By
 * @property string $COR_Added_On
 * @property integer $COR_Status
 * @property string $COR_Actual_Price
 * @property integer $pricing_status
 */
class ConstructionResources extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'construction_resources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('COR_CO_Id, COR_Resource_Id, COR_Unit, COR_Price, COR_Qunatity, COR_Added_By, COR_Added_On, COR_Status, COR_Actual_Price', 'required'),
			array('COR_CO_Id, COR_Resource_Id, COR_Added_By, COR_Status', 'numerical', 'integerOnly'=>true),
			array('COR_Unit, COR_Qunatity, COR_Actual_Price', 'length', 'max'=>20),
			array('COR_Price', 'length', 'max'=>29),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('COR_Id, COR_CO_Id, COR_Resource_Id, COR_Unit, COR_Price, COR_Qunatity, COR_Added_By, COR_Added_On, COR_Status, COR_Actual_Price', 'safe', 'on'=>'search'),
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
			'COR_Id' => 'Cor',
			'COR_CO_Id' => 'Cor Co',
			'COR_Resource_Id' => 'Cor Resource',
			'COR_Unit' => 'Cor Unit',
			'COR_Price' => 'Cor Price',
			'COR_Qunatity' => 'Cor Qunatity',
			'COR_Added_By' => 'Cor Added By',
			'COR_Added_On' => 'Cor Added On',
			'COR_Status' => 'Cor Status',
			'COR_Actual_Price' => 'Cor Actual Price',
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

		$criteria->compare('COR_Id',$this->COR_Id);
		$criteria->compare('COR_CO_Id',$this->COR_CO_Id);
		$criteria->compare('COR_Resource_Id',$this->COR_Resource_Id);
		$criteria->compare('COR_Unit',$this->COR_Unit,true);
		$criteria->compare('COR_Price',$this->COR_Price,true);
		$criteria->compare('COR_Qunatity',$this->COR_Qunatity,true);
		$criteria->compare('COR_Added_By',$this->COR_Added_By);
		$criteria->compare('COR_Added_On',$this->COR_Added_On,true);
		$criteria->compare('COR_Status',$this->COR_Status);
		$criteria->compare('COR_Actual_Price',$this->COR_Actual_Price,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ConstructionResources the static model class
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
