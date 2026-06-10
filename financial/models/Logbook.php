<?php

namespace app\models;
use Yii;


/**
 * This is the model class for table "logbook".
 *
 * The followings are the available columns in table 'logbook':
 * @property integer $log_id
 * @property integer $activity_id
 * @property string $date
 * @property integer $equipment_id
 * @property double $start_time_km
 * @property double $end_time_km
 * @property double $worked_hours
 * @property double $diesel
 * @property integer $project_id
 * @property integer $userid
 * @property integer $groupid
 * @property integer $process
 * @property integer $trips
 * @property integer $Updated_On
 * @property integer $unit
 * @property integer $order_id
 */
class Logbook extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'logbook';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('activity_id, date, equipment_id, start_time_km, end_time_km, worked_hours, diesel, project_id', 'required'),
			array('activity_id, equipment_id, project_id', 'numerical', 'integerOnly'=>true),
			array('start_time_km, end_time_km, worked_hours, diesel', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('log_id, activity_id, date, equipment_id, start_time_km, end_time_km, worked_hours, diesel, project_id,userid,groupid,process', 'safe', 'on'=>'search'),
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
			'log_id' => 'Log',
			'activity_id' => 'Activity',
			'date' => 'Date',
			'equipment_id' => 'Equipment',
			'start_time_km' => 'Start Time Km',
			'end_time_km' => 'End Time Km',
			'worked_hours' => 'Worked Hours',
			'diesel' => 'Diesel',
			'project_id' => 'Project',
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

		$criteria->compare('log_id',$this->log_id);
		$criteria->compare('activity_id',$this->activity_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('equipment_id',$this->equipment_id);
		$criteria->compare('start_time_km',$this->start_time_km);
		$criteria->compare('end_time_km',$this->end_time_km);
		$criteria->compare('worked_hours',$this->worked_hours);
		$criteria->compare('diesel',$this->diesel);
		$criteria->compare('project_id',$this->project_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Logbook the static model class
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
