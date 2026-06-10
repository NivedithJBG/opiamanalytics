<?php

/**
 * This is the model class for table "iowproducts".
 *
 * The followings are the available columns in table 'iowproducts':
 * @property integer $IOWProducts_Id
 * @property integer $IOW_Id
 * @property string $type
 * @property integer $Item_Id
 * @property double $Quantity
 * @property double $Price
 * @property double $specific_rate
 * @property string $Added_On
 * @property string $Updated_On
 * @property integer $Added_By
 * @property integer $Status
 * @property integer $resource_id
 * @property string $restype
 * @property integer $Resource_group_Id
 */
class Iowproducts extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'iowproducts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('IOW_Id, type, Item_Id, Quantity, Price, specific_rate, Added_On, Added_By, Status, resource_id, restype, Resource_group_Id', 'required'),
			array('IOW_Id, Item_Id, Added_By, Status, resource_id, Resource_group_Id', 'numerical', 'integerOnly'=>true),
			array('Quantity, Price, specific_rate', 'numerical'),
			array('type, restype', 'length', 'max'=>150),
			array('Updated_On', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('IOWProducts_Id, IOW_Id, type, Item_Id, Quantity, Price, specific_rate, Added_On, Updated_On, Added_By, Status, resource_id, restype, Resource_group_Id', 'safe', 'on'=>'search'),
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
			'IOWProducts_Id' => 'Iowproducts',
			'IOW_Id' => 'Iow',
			'type' => 'Type',
			'Item_Id' => 'Item',
			'Quantity' => 'Quantity',
			'Price' => 'Price',
			'specific_rate' => 'Specific Rate',
			'Added_On' => 'Added On',
			'Updated_On' => 'Updated On',
			'Added_By' => 'Added By',
			'Status' => 'Status',
			'resource_id' => 'Resource',
			'restype' => 'Restype',
			'Resource_group_Id' => 'Resource Group',
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

		$criteria->compare('IOWProducts_Id',$this->IOWProducts_Id);
		$criteria->compare('IOW_Id',$this->IOW_Id);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('Item_Id',$this->Item_Id);
		$criteria->compare('Quantity',$this->Quantity);
		$criteria->compare('Price',$this->Price);
		$criteria->compare('specific_rate',$this->specific_rate);
		$criteria->compare('Added_On',$this->Added_On,true);
		$criteria->compare('Updated_On',$this->Updated_On,true);
		$criteria->compare('Added_By',$this->Added_By);
		$criteria->compare('Status',$this->Status);
		$criteria->compare('resource_id',$this->resource_id);
		$criteria->compare('restype',$this->restype,true);
		$criteria->compare('Resource_group_Id',$this->Resource_group_Id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Iowproducts the static model class
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
