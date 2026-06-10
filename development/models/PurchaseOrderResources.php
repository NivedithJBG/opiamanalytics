<?php


namespace app\models;

use Yii;
/**
 * This is the model class for table "ordered_resource".
 *
 * The followings are the available columns in table 'ordered_resource':
 * @property integer $order_res_id
 * @property integer $order_id
 * @property integer $jobcard_id
 * @property integer $resource_id
 * @property string $unit
 * @property double $rate
 * @property double $qnty
 * @property double $amount
 * @property string $resource_name
 * @property integer $status
 * @property integer $no_workers
 * @property integer $no_days
 * @property double $ot_rate
 * @property string $date
 * @property string $received_date
 * @property string $movefrom
 * @property string $moveto
 * @property string $vehicle_no
 * @property double $cgst
 * @property double $sgst
 * @property double $igst
 * @property double $delete_status
 * @property double $working_hours
 * @property integer $despatch_status
 */
class PurchaseOrderResources extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'purchase_order_resources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, resource_id, unit, rate, qnty, amount', 'required'),
			array('order_id, resource_id', 'numerical', 'integerOnly'=>true),
			array('rate, qnty, amount', 'numerical'),
			array('unit', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('order_res_id, order_id, resource_id, unit, rate, qnty, amount', 'safe', 'on'=>'search'),
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
			'order_res_id' => 'Order Res',
			'order_id' => 'Order',
			'resource_id' => 'Resource',
			'unit' => 'Unit',
			'rate' => 'Rate',
			'qnty' => 'Qnty',
			'amount' => 'Amount',
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

		$criteria->compare('order_res_id',$this->order_res_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('resource_id',$this->resource_id);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('rate',$this->rate);
		$criteria->compare('qnty',$this->qnty);
		$criteria->compare('amount',$this->amount);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrderedResource the static model class
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
