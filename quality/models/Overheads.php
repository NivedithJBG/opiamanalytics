<?php

namespace app\models;
use Yii;

/**
 * This is the model class for table "overheads".
 *
 * The followings are the available columns in table 'overheads':
 * @property integer $Overhead_id
 * @property string $Name
 * @property string $Unit
 * @property double $Price
 * @property integer $worktype
 * @property integer $estimate
 * @property integer $enggsortorder
 * @property integer $pricing_status
 */
class Overheads extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'overheads';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Name, Unit', 'required'),
			array('Price', 'numerical'),
			array('Name', 'length', 'max'=>250),
			array('Unit', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Overhead_id, Name, Unit, Price', 'safe', 'on'=>'search'),
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
			'Overhead_id' => 'Overhead',
			'Name' => 'Name',
			'Unit' => 'Unit',
			'Price' => 'Price',
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

		$criteria->compare('Overhead_id',$this->Overhead_id);
		$criteria->compare('Name',$this->Name,true);
		$criteria->compare('Unit',$this->Unit,true);
		$criteria->compare('Price',$this->Price);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Overheads the static model class
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
