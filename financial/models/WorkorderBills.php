<?php

namespace app\models;
use Yii;


/**
 * This is the model class for table "workorder_bills".
 *
 * The followings are the available columns in table 'workorder_bills':
 * @property integer $bill_id
 * @property integer $order_id
 * @property string $bill_no
 * @property string $date
 * @property integer $place
 * @property integer $party
 * @property integer $sgst
 * @property integer $cgst
 * @property integer $igst
 * @property integer $status
 * @property integer $order_type
 * @property integer $other_deductions
 * @property string $Specification
 * @property integer $amount_pay
 * @property integer $delete_status
 */
class WorkorderBills extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'workorder_bills';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, bill_no, date, place, party', 'required'),
			array('order_id, place, party', 'numerical', 'integerOnly'=>true),
			array('bill_no', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('bill_id, order_id, bill_no, date, place, party', 'safe', 'on'=>'search'),
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
			'bill_id' => 'Bill',
			'order_id' => 'Order',
			'bill_no' => 'Bill No',
			'date' => 'Date',
			'place' => 'Place',
			'party' => 'Party',
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

		$criteria->compare('bill_id',$this->bill_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('bill_no',$this->bill_no,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('place',$this->place);
		$criteria->compare('party',$this->party);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WorkorderBills the static model class
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
