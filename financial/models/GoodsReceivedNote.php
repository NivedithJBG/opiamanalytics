<?php

/**
 * This is the model class for table "goods_received_note".
 *
 * The followings are the available columns in table 'goods_received_note':
 * @property integer $GRN_Id
 * @property string $GRN_Date
 * @property string $GRN_Vehicle
 * @property integer $GRN_Project
 * @property integer $GRN_Vendor
 * @property integer $GRN_Purchase_Order
 * @property integer $GRN_Item
 * @property string $GRN_Quantity
 */
class GoodsReceivedNote extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'goods_received_note';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('GRN_Date, GRN_Vehicle, GRN_Project, GRN_Vendor, GRN_Purchase_Order, GRN_Item, GRN_Quantity', 'required'),
			array('GRN_Project, GRN_Vendor, GRN_Purchase_Order, GRN_Item', 'numerical', 'integerOnly'=>true),
			array('GRN_Vehicle, GRN_Quantity', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('GRN_Id, GRN_Date, GRN_Vehicle, GRN_Project, GRN_Vendor, GRN_Purchase_Order, GRN_Item, GRN_Quantity', 'safe', 'on'=>'search'),
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
			'GRN_Id' => 'Grn',
			'GRN_Date' => 'Grn Date',
			'GRN_Vehicle' => 'Grn Vehicle',
			'GRN_Project' => 'Grn Project',
			'GRN_Vendor' => 'Grn Vendor',
			'GRN_Purchase_Order' => 'Grn Purchase Order',
			'GRN_Item' => 'Grn Item',
			'GRN_Quantity' => 'Grn Quantity',
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

		$criteria->compare('GRN_Id',$this->GRN_Id);
		$criteria->compare('GRN_Date',$this->GRN_Date,true);
		$criteria->compare('GRN_Vehicle',$this->GRN_Vehicle,true);
		$criteria->compare('GRN_Project',$this->GRN_Project);
		$criteria->compare('GRN_Vendor',$this->GRN_Vendor);
		$criteria->compare('GRN_Purchase_Order',$this->GRN_Purchase_Order);
		$criteria->compare('GRN_Item',$this->GRN_Item);
		$criteria->compare('GRN_Quantity',$this->GRN_Quantity,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GoodsReceivedNote the static model class
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
