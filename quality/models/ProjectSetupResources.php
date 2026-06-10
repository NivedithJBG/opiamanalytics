<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project_setup_resources".
 *
 * The followings are the available columns in table 'project_setup_resources':
 * @property integer $PSR_Id
 * @property integer $PSR_PS_Id
 * @property integer $PSR_Resource_Id
 * @property string $PSR_Quantity
 * @property string $PSR_Price
 * @property string $PSR_Added_on
 * @property string $PSR_Updated_On
 * @property integer $PSR_Added_By
 * @property integer $PSR_Status
 * @property string $PSR_Actual_Price
 * @property string $pricing_status
 */
class ProjectSetupResources extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'project_setup_resources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('PSR_PS_Id, PSR_Resource_Id, PSR_Quantity, PSR_Price, PSR_Added_on, PSR_Updated_On, PSR_Added_By, PSR_Status, PSR_Actual_Price', 'required'),
			array('PSR_PS_Id, PSR_Resource_Id, PSR_Added_By, PSR_Status', 'numerical', 'integerOnly'=>true),
			array('PSR_Quantity, PSR_Price, PSR_Actual_Price', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('PSR_Id, PSR_PS_Id, PSR_Resource_Id, PSR_Quantity, PSR_Price, PSR_Added_on, PSR_Updated_On, PSR_Added_By, PSR_Status, PSR_Actual_Price', 'safe', 'on'=>'search'),
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
			'PSR_Id' => 'Psr',
			'PSR_PS_Id' => 'Psr Ps',
			'PSR_Resource_Id' => 'Psr Resource',
			'PSR_Quantity' => 'Psr Quantity',
			'PSR_Price' => 'Psr Price',
			'PSR_Added_on' => 'Psr Added On',
			'PSR_Updated_On' => 'Psr Updated On',
			'PSR_Added_By' => 'Psr Added By',
			'PSR_Status' => 'Psr Status',
			'PSR_Actual_Price' => 'Psr Actual Price',
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

		$criteria->compare('PSR_Id',$this->PSR_Id);
		$criteria->compare('PSR_PS_Id',$this->PSR_PS_Id);
		$criteria->compare('PSR_Resource_Id',$this->PSR_Resource_Id);
		$criteria->compare('PSR_Quantity',$this->PSR_Quantity,true);
		$criteria->compare('PSR_Price',$this->PSR_Price,true);
		$criteria->compare('PSR_Added_on',$this->PSR_Added_on,true);
		$criteria->compare('PSR_Updated_On',$this->PSR_Updated_On,true);
		$criteria->compare('PSR_Added_By',$this->PSR_Added_By);
		$criteria->compare('PSR_Status',$this->PSR_Status);
		$criteria->compare('PSR_Actual_Price',$this->PSR_Actual_Price,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ProjectSetupResources the static model class
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
