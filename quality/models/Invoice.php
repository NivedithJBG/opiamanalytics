<?php

namespace app\models;

use Yii;
/**

/**
 * This is the model class for table "invoice".
 *
 * The followings are the available columns in table 'invoice':
 * @property integer $invoice_id
 * @property string $invoice_no
 * @property integer $order_id
 * @property string $Specification
 * @property double $Advance
 * @property string $Contact
 * @property string $DeliveryDate
 * @property string $Place
 * @property string $Payment
 * @property double $GST
 * @property double $IGST
 * @property integer $status
 * @property integer $order_type
 * @property integer $project_id
 * @property string $invoice_date
 * @property string $vendor
 * @property integer $transportation_cost
 * @property integer $total_wages
 * @property integer $musterroll_group
 */
class Invoice extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static  function tableName()
	{
		return 'invoice';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('invoice_no, order_id, Specification, Advance, Contact, DeliveryDate, Place, Payment, GST, IGST', 'required'),
			array('order_id', 'numerical', 'integerOnly'=>true),
			array('Advance, GST, IGST', 'numerical'),
			array('invoice_no, Specification, Contact, Place, Payment', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('invoice_id, invoice_no, order_id, Specification, Advance, Contact, DeliveryDate, Place, Payment, GST, IGST', 'safe', 'on'=>'search'),
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
			'invoice_id' => 'Invoice',
			'invoice_no' => 'Invoice No',
			'order_id' => 'Order',
			'Specification' => 'Specification',
			'Advance' => 'Advance',
			'Contact' => 'Contact',
			'DeliveryDate' => 'Delivery Date',
			'Place' => 'Place',
			'Payment' => 'Payment',
			'GST' => 'Gst',
			'IGST' => 'Igst',
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

		$criteria->compare('invoice_id',$this->invoice_id);
		$criteria->compare('invoice_no',$this->invoice_no,true);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('Specification',$this->Specification,true);
		$criteria->compare('Advance',$this->Advance);
		$criteria->compare('Contact',$this->Contact,true);
		$criteria->compare('DeliveryDate',$this->DeliveryDate,true);
		$criteria->compare('Place',$this->Place,true);
		$criteria->compare('Payment',$this->Payment,true);
		$criteria->compare('GST',$this->GST);
		$criteria->compare('IGST',$this->IGST);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Invoice the static model class
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
