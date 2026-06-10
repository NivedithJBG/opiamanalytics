<?php

/**
 * This is the model class for table "estimate_product_resources".
 *
 * The followings are the available columns in table 'estimate_product_resources':
 * @property integer $Est_Prod_Res_Id
 * @property integer $Product_Id
 * @property integer $Resource_Id
 * @property double $Quantity
 * @property double $Price
 * @property integer $IOW_Product_Id
 * @property string $Updated_On
 * @property integer $Added_By
 * @property integer $Status
 * @property double $actual_price
 * @property integer $IOW_Id
 * @property integer $Type
 */
class EstimateProductResources extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'estimate_product_resources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Product_Id, Resource_Id, Quantity, Price, IOW_Product_Id, Updated_On, Added_By, Status, actual_price, IOW_Id, Type', 'required'),
			array('Product_Id, Resource_Id, IOW_Product_Id, Added_By, Status, IOW_Id, Type', 'numerical', 'integerOnly'=>true),
			array('Quantity, Price, actual_price', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Est_Prod_Res_Id, Product_Id, Resource_Id, Quantity, Price, IOW_Product_Id, Updated_On, Added_By, Status, actual_price, IOW_Id, Type', 'safe', 'on'=>'search'),
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
			'Est_Prod_Res_Id' => 'Est Prod Res',
			'Product_Id' => 'Product',
			'Resource_Id' => 'Resource',
			'Quantity' => 'Quantity',
			'Price' => 'Price',
			'IOW_Product_Id' => 'Iow Product',
			'Updated_On' => 'Updated On',
			'Added_By' => 'Added By',
			'Status' => 'Status',
			'actual_price' => 'Actual Price',
			'IOW_Id' => 'Iow',
			'Type' => 'Type',
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

		$criteria->compare('Est_Prod_Res_Id',$this->Est_Prod_Res_Id);
		$criteria->compare('Product_Id',$this->Product_Id);
		$criteria->compare('Resource_Id',$this->Resource_Id);
		$criteria->compare('Quantity',$this->Quantity);
		$criteria->compare('Price',$this->Price);
		$criteria->compare('IOW_Product_Id',$this->IOW_Product_Id);
		$criteria->compare('Updated_On',$this->Updated_On,true);
		$criteria->compare('Added_By',$this->Added_By);
		$criteria->compare('Status',$this->Status);
		$criteria->compare('actual_price',$this->actual_price);
		$criteria->compare('IOW_Id',$this->IOW_Id);
		$criteria->compare('Type',$this->Type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EstimateProductResources the static model class
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
