<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "overheads_resources".
 *
 * The followings are the available columns in table 'overheads_resources':
 * @property integer $OR_Id
 * @property integer $Overhead_id
 * @property integer $Resource_Id
 * @property double $Quantity
 * @property double $Price
 * @property double $actual_price
 * @property integer $pricing_status
 */
class OverheadsResources extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public  static function tableName()
	{
		return 'overheads_resources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Overhead_id, Resource_Id, Quantity, Price, actual_price', 'required'),
			array('Overhead_id, Resource_Id', 'numerical', 'integerOnly'=>true),
			array('Quantity, Price, actual_price', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('OR_Id, Overhead_id, Resource_Id, Quantity, Price, actual_price', 'safe', 'on'=>'search'),
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
			'OR_Id' => 'Or',
			'Overhead_id' => 'Overhead',
			'Resource_Id' => 'Resource',
			'Quantity' => 'Quantity',
			'Price' => 'Price',
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

		$criteria->compare('OR_Id',$this->OR_Id);
		$criteria->compare('Overhead_id',$this->Overhead_id);
		$criteria->compare('Resource_Id',$this->Resource_Id);
		$criteria->compare('Quantity',$this->Quantity);
		$criteria->compare('Price',$this->Price);
		$criteria->compare('actual_price',$this->actual_price);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OverheadsResources the static model class
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
