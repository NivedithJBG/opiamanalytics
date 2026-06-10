<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "jobcard".
 *
 * The followings are the available columns in table 'jobcard':
 * @property integer $job_id
 * @property string $name
 * @property string $date
 * @property integer $process
 * @property string $activity
 * @property string $startdate
 * @property string $enddate
 * @property integer $groupid
 * @property string $resource
 * @property string $unit
 * @property double $act_quantity
 * @property double $res_quantity
 * @property double $project_id
 * @property double $user_id
 * @property double $status
 * @property double $app_activity
 * @property double $app_resource
 * @property double $Approved_By
 * @property double $Approved_On
 * @property integer $cart_status
 * @property integer $ar_status
 * @property integer $iow
 * @property integer $delete_status
 */
class Jobcard extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'jobcard';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, process, activity, startdate, enddate, groupid, resource, unit, quantity', 'required'),
			array('process, groupid', 'numerical', 'integerOnly'=>true),
			array('quantity', 'numerical'),
			array('activity, resource, unit', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('job_id, date, process, activity, startdate, enddate, groupid, resource, unit, quantity,project_id,user_id,status,app_activity,app_resource,Approved_On,Approved_By', 'safe', 'on'=>'search'),
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
			'job_id' => 'Job',
			'date' => 'Date',
			'process' => 'Process',
			'activity' => 'Activity',
			'startdate' => 'startdate',
			'enddate' => 'enddate',
			'groupid' => 'Groupid',
			'resource' => 'Resource',
			'unit' => 'Unit',
			'quantity' => 'Quantity',
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

		$criteria->compare('job_id',$this->job_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('process',$this->process);
		$criteria->compare('activity',$this->activity,true);
		$criteria->compare('startdate',$this->startdate,true);
		$criteria->compare('enddate',$this->enddate,true);
		$criteria->compare('groupid',$this->groupid);
		$criteria->compare('resource',$this->resource,true);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('quantity',$this->quantity);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Jobcard the static model class
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
