<?php


namespace app\models;
use Yii;

/**
 * This is the model class for table "cashbills".
 *
 * The followings are the available columns in table 'cashbills':
 * @property integer $cashbill_id
 * @property string $date
 * @property string $bill_no
 * @property string $vendor
 * @property string $item
 * @property string $unit
 * @property double $quantity
 * @property double $amount
 * @property string $purpose
 * @property integer $created_by
 * @property integer $status
 * @property integer $activity
 * @property integer $resource
 * @property integer $accounthead
 * @property integer $project_id
 * @property integer $advance_id
 * @property double $rate
 * @property double $sgst
 * @property double $igst
 * @property double $balance
 */
class Cashbills extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'cashbills';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, bill_no, vendor, item, unit, quantity, amount, purpose, created_by, status', 'required'),
			array('created_by, status', 'numerical', 'integerOnly'=>true),
			array('quantity, amount', 'numerical'),
			array('bill_no, vendor, item, unit, purpose', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('cashbill_id, date, bill_no, vendor, item, unit, quantity, amount, purpose, created_by, status', 'safe', 'on'=>'search'),
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
			'cashbill_id' => 'Cashbill',
			'date' => 'Date',
			'bill_no' => 'Bill No',
			'vendor' => 'Vendor',
			'item' => 'Item',
			'unit' => 'Unit',
			'quantity' => 'Quantity',
			'amount' => 'Amount',
			'purpose' => 'Purpose',
			'created_by' => 'Created By',
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

		$criteria->compare('cashbill_id',$this->cashbill_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('bill_no',$this->bill_no,true);
		$criteria->compare('vendor',$this->vendor,true);
		$criteria->compare('item',$this->item,true);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('purpose',$this->purpose,true);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cashbills the static model class
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
