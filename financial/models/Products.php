<?php

namespace app\models;
use Yii;

/**
 * This is the model class for table "Products".
 *
 * The followings are the available columns in table 'Products':
 * @property integer $Product_Id
 * @property integer $Project_Id
 * @property string $Name
 * @property string $Unit
 * @property integer $Price
 * @property string $Added_On
 * @property string $Updated_On
 * @property integer $Added_By
 * @property integer $Status
 * @property integer $quantity
 * @property integer $amount
 * @property integer $worktype
 * @property integer $estimate
 * @property integer $enggsortorder
 * @property integer $pricing_status
 */
class Products extends \yii\db\ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Products the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'products';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Name, Unit, Added_On', 'required'),
			//array('Price, Added_By, Status', 'numerical', 'integerOnly'=>true),
			array('Name', 'length', 'max'=>250),
			array('Unit', 'length', 'max'=>50),
			array('Updated_On,Price,quantity,amount', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Product_Id, Project_Id,Name, Unit, Price, Added_On, Updated_On, Added_By, Status,quantity,amount', 'safe', 'on'=>'search'),
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
			'Product_Id' => 'Product',
			'Project_Id' => 'Project_Id',
			'Name' => 'Name',
			'Unit' => 'Unit',
			'Price' => 'Price',
			'Added_On' => 'Added On',
			'Updated_On' => 'Updated On',
			'Added_By' => 'Added By',
			'Status' => 'Status',
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

		$criteria->compare('Product_Id',$this->Product_Id);
		$criteria->compare('Name',$this->Name,true);
		$criteria->compare('Unit',$this->Unit,true);
		$criteria->compare('Price',$this->Price);
		$criteria->compare('Added_On',$this->Added_On,true);
		$criteria->compare('Updated_On',$this->Updated_On,true);
		$criteria->compare('Added_By',$this->Added_By);
		$criteria->compare('Status',$this->Status);
		$criteria->compare('Status',$this->Project_Id);

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