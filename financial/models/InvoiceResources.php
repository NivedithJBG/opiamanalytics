<?php

namespace app\models;
use Yii;


/**
 * This is the model class for table "invoice_resources".
 *
 * The followings are the available columns in table 'invoice_resources':
 * @property integer $invres_id
 * @property integer $invoice_id
 * @property integer $order_id
 * @property integer $resource_id
 * @property double $resource_qty
 * @property integer $resource_acnt
 */
class InvoiceResources extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'invoice_resources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('invoice_id, resource_id, resource_qty', 'required'),
			array('invoice_id, resource_id', 'numerical', 'integerOnly'=>true),
			array('resource_qty', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('invres_id, invoice_id, resource_id, resource_qty', 'safe', 'on'=>'search'),
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
			'invres_id' => 'Invres',
			'invoice_id' => 'Invoice',
			'resource_id' => 'Resource',
			'resource_qty' => 'Resource Qty',
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

		$criteria->compare('invres_id',$this->invres_id);
		$criteria->compare('invoice_id',$this->invoice_id);
		$criteria->compare('resource_id',$this->resource_id);
		$criteria->compare('resource_qty',$this->resource_qty);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InvoiceResources the static model class
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
