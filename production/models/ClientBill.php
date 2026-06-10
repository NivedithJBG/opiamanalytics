<?php

namespace app\models;
use Yii;

/**
 * This is the model class for table "client_bill".
 *
 * The followings are the available columns in table 'client_bill':
 * @property integer $bill_id
 * @property string $bill_no
 * @property integer $iow_id
 * @property integer $boq_id
 * @property double $current_qty
 * @property string $raise_date
 * @property integer $status
 * @property string $updated_date
 */
class ClientBill extends \yii\db\ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ClientBill the static model class
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
		return 'client_bill';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bill_no, iow_id, boq_id, current_qty, raise_date, status', 'required'),
			array('iow_id, boq_id, status', 'numerical', 'integerOnly'=>true),
			array('current_qty', 'numerical'),
			array('bill_no', 'length', 'max'=>255),
			array('updated_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('bill_id, bill_no, iow_id, boq_id, current_qty, raise_date, status, updated_date', 'safe', 'on'=>'search'),
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
			'bill_no' => 'Bill No',
			'iow_id' => 'Iow',
			'boq_id' => 'Boq',
			'current_qty' => 'Current Qty',
			'raise_date' => 'Raise Date',
			'status' => 'Status',
			'updated_date' => 'Updated Date',
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

		$criteria->compare('bill_id',$this->bill_id);
		$criteria->compare('bill_no',$this->bill_no,true);
		$criteria->compare('iow_id',$this->iow_id);
		$criteria->compare('boq_id',$this->boq_id);
		$criteria->compare('current_qty',$this->current_qty);
		$criteria->compare('raise_date',$this->raise_date,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('updated_date',$this->updated_date,true);

		return new CActiveDataProvider(get_class($this), array(
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