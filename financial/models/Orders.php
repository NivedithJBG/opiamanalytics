<?php


namespace app\models;

use Yii;
/**
/**
 * This is the model class for table "orders".
 *
 * The followings are the available columns in table 'orders':
 * @property integer $order_id
 * @property integer $project_id
 * @property integer $vendor_id
 * @property string $specification
 * @property double $advance
 * @property string $payment
 * @property string $contact
 * @property string $deliverydate
 * @property string $date
 * @property string $place
 * @property integer $order_type
 * @property integer $status
 * @property integer $emailstatus
 * @property integer $terms
 * @property string $Retention
 * @property string $Period
 * @property string $Leaseperiod
 * @property string $fromdate
 * @property string $todate
 * @property double $sgst
 * @property double $cgst
 * @property double $igst
 * @property string $from
 * @property string $to
 * @property string $vehicle_no
 * @property string $gstn_no
 * @property string $musterroll
 * @property string $delete_status
 * @property double $transportation
 * @property double $tdsper
 * @property double $despatch_status
 * @property string $billing_address
 */
class Orders extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'orders';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('specification, advance, payment, contact, date, place, order_type, status', 'required'),
			array('order_type, status', 'numerical', 'integerOnly'=>true),
			array('advance', 'numerical'),
			array('specification, payment, contact, place', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('order_id, specification, advance, payment, contact, date, place, order_type, status', 'safe', 'on'=>'search'),
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
			'order_id' => 'Order',
			'specification' => 'Specification',
			'advance' => 'Advance',
			'payment' => 'Payment',
			'contact' => 'Contact',
			'date' => 'Date',
			'place' => 'Place',
			'order_type' => 'Order Type',
			'status' => 'Status',
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

		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('specification',$this->specification,true);
		$criteria->compare('advance',$this->advance);
		$criteria->compare('payment',$this->payment,true);
		$criteria->compare('contact',$this->contact,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('place',$this->place,true);
		$criteria->compare('order_type',$this->order_type);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Orders the static model class
	 */
	//public static function model($className=__CLASS__)
	//{
	//	return parent::model($className);
	//}
	public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}
