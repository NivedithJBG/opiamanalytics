<?php

namespace app\models;
use Yii;


/**
 * This is the model class for table "pricing_estimate".
 *
 * The followings are the available columns in table 'pricing_estimate':
 * @property integer $pricing_estimate_Id
 * @property integer $project_Id
 * @property integer $activity_Id
 * @property double $activity_qty
 * @property integer $process_Id
 * @property double $specific_rate
 * @property double $est_activity_Id
 * @property double $pricing_status
 * @property double $operations_status
 */
class PricingEstimate extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'pricing_estimate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_Id, activity_Id, activity_qty, process_Id', 'required'),
			array('project_Id, activity_Id, process_Id', 'numerical', 'integerOnly'=>true),
			array('activity_qty', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('pricing_estimate_Id, project_Id, activity_Id, activity_qty, process_Id', 'safe', 'on'=>'search'),
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
			'pricing_estimate_Id' => 'Pricing Estimate',
			'project_Id' => 'Project',
			'activity_Id' => 'Activity',
			'activity_qty' => 'Activity Qty',
			'process_Id' => 'Process',
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

		$criteria->compare('pricing_estimate_Id',$this->pricing_estimate_Id);
		$criteria->compare('project_Id',$this->project_Id);
		$criteria->compare('activity_Id',$this->activity_Id);
		$criteria->compare('activity_qty',$this->activity_qty);
		$criteria->compare('process_Id',$this->process_Id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PricingEstimate the static model class
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
