<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "logistics_resources".
 *
 * The followings are the available columns in table 'logistics_resources':
 * @property integer $LR_Id
 * @property integer $Logistics_Id
 * @property integer $Resource_Id
 * @property double $Quantity
 * @property double $Price
 * @property string $Added_On
 * @property string $Updated_On
 * @property integer $Added_By
 * @property integer $Status
 * @property double $actual_price
 * @property double $pricing_status
 */
class LogisticsResources extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'logistics_resources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Logistics_Id, Resource_Id, Quantity, Price, Added_On, Updated_On, Added_By, Status, actual_price', 'required'),
			array('Logistics_Id, Resource_Id, Added_By, Status', 'numerical', 'integerOnly'=>true),
			array('Quantity, Price, actual_price', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('LR_Id, Logistics_Id, Resource_Id, Quantity, Price, Added_On, Updated_On, Added_By, Status, actual_price', 'safe', 'on'=>'search'),
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
			'LR_Id' => 'Lr',
			'Logistics_Id' => 'Logistics',
			'Resource_Id' => 'Resource',
			'Quantity' => 'Quantity',
			'Price' => 'Price',
			'Added_On' => 'Added On',
			'Updated_On' => 'Updated On',
			'Added_By' => 'Added By',
			'Status' => 'Status',
			'actual_price' => 'Actual Price',
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

		$criteria->compare('LR_Id',$this->LR_Id);
		$criteria->compare('Logistics_Id',$this->Logistics_Id);
		$criteria->compare('Resource_Id',$this->Resource_Id);
		$criteria->compare('Quantity',$this->Quantity);
		$criteria->compare('Price',$this->Price);
		$criteria->compare('Added_On',$this->Added_On,true);
		$criteria->compare('Updated_On',$this->Updated_On,true);
		$criteria->compare('Added_By',$this->Added_By);
		$criteria->compare('Status',$this->Status);
		$criteria->compare('actual_price',$this->actual_price);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return LogisticsResources the static model class
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
