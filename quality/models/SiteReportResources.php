<?php

namespace app\models;
use Yii;

/**
 * This is the model class for table "site_report_resources".
 *
 * The followings are the available columns in table 'site_report_resources':
 * @property integer $report_id
 * @property integer $activityid
 * @property integer $resource_id
 * @property string $unit
 * @property double $quantity_used
 */
class SiteReportResources extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'site_report_resources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('activityid, resource_id, unit, quantity_used', 'required'),
			array('activityid, resource_id', 'numerical', 'integerOnly'=>true),
			array('quantity_used', 'numerical'),
			array('unit', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('report_id, activityid, resource_id, unit, quantity_used', 'safe', 'on'=>'search'),
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
			'report_id' => 'Report',
			'activityid' => 'Activityid',
			'resource_id' => 'Resource',
			'unit' => 'Unit',
			'quantity_used' => 'Quantity Used',
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

		$criteria->compare('report_id',$this->report_id);
		$criteria->compare('activityid',$this->activityid);
		$criteria->compare('resource_id',$this->resource_id);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('quantity_used',$this->quantity_used);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SiteReportResources the static model class
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
