<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "resources".
 *
 * The followings are the available columns in table 'resources':
 * @property integer $Resource_Id
 * @property string $Name
 * @property integer $Unit
 * @property integer $Price
 * @property integer $ResourceType_Id
 * @property integer $Vendor_Id
 * @property integer $Project_Id
 * @property integer $Status
 * @property string $Added_On
 * @property string $Updated_On
 * @property integer $Added_By
 * @property integer $Resource_Acc_Id
 * @property integer $Resource_Location
 * @property integer $Resource_group_Id
 * @property integer $pricing_status
 * @property integer $Quantity
 */
class Resources extends \yii\db\ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Resources the static model class
	 */
	/*public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}*/

	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'resources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Name, Unit, Price, ResourceType_Id,Vendor_Id, Added_On,Resource_Acc_Id', 'required','on'=>'create'),
			array('Name, Unit, Price, Updated_On,Resource_Acc_Id', 'safe','on'=>'update'),
			array('Project_Id', 'safe'),
			//array('Price, ResourceType_Id, Added_By', 'numerical', 'integerOnly'=>true),
			array('Name', 'length', 'max'=>250),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Resource_Id, Name, Unit, Price, ResourceType_Id, Status, Added_On, Added_By,Resource_Acc_Id', 'safe', 'on'=>'search'),
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
            'ResourceType_Id' => array(self::BELONGS_TO, 'Resourcetype', 'ResourceType_Id'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Resource_Id' => 'Resource',
			'Name' => 'Name',
			'Unit' => 'Unit',
			'Price' => 'Price',
			'ResourceType_Id' => 'Resource Type',
			'Status' => 'Status',
			'Added_On' => 'Added On',
			'Added_By' => 'Added By',
			'Resource_Acc_Id' => 'Resource Acc Id',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('Resource_Id',$this->Resource_Id);
		$criteria->compare('Name',$this->Name,true);
		$criteria->compare('Unit',$this->Unit);
		$criteria->compare('Price',$this->Price);
		$criteria->compare('ResourceType_Id',$this->ResourceType_Id);
		$criteria->compare('Status',$this->Status);
		$criteria->compare('Added_On',$this->Added_On,true);
		$criteria->compare('Added_By',$this->Added_By);
		$criteria->compare('Resource_Acc_Id',$this->Resource_Acc_Id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}