<?php

namespace app\models;
use Yii;
/**
 * This is the model class for table "cart".
 *
 * The followings are the available columns in table 'cart':
 * @property integer $cartID
 * @property integer $Job_Id
 * @property integer $groupid
 * @property integer $Vendor_Id
 * @property integer $ResourceType_Id
 * @property integer $Resource_Id
 * @property string $unit
 * @property double $rate
 * @property double $Qnty
 * @property double $amount
 * @property integer $status
 * @property integer $Project
 * @property integer $no_workers
 * @property integer $no_days
 * @property double $ot_rate
 */
class Cart extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'cart';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Vendor_Id, ResourceType_Id, Resource_Id, Qnty', 'required'),
			array('Vendor_Id, ResourceType_Id, Resource_Id', 'numerical', 'integerOnly'=>true),
			array('Qnty', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('cartID, Vendor_Id, ResourceType_Id, Resource_Id, Qnty', 'safe', 'on'=>'search'),
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
			'cartID' => 'Cart',
			'Vendor_Id' => 'Vendor',
			'ResourceType_Id' => 'Resource Type',
			'Resource_Id' => 'Resource',
			'Qnty' => 'Qnty',
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

		$criteria->compare('cartID',$this->cartID);
		$criteria->compare('Vendor_Id',$this->Vendor_Id);
		$criteria->compare('ResourceType_Id',$this->ResourceType_Id);
		$criteria->compare('Resource_Id',$this->Resource_Id);
		$criteria->compare('Qnty',$this->Qnty);

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

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cart the static model class
	 */
	//public static function model($className=__CLASS__)
	//{
	//	return parent::model($className);
	//}
}
