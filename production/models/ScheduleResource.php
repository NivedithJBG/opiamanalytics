<?php

namespace app\models;
use Yii;

/**
 * This is the model class for table "schedule_resource".
 *
 * The followings are the available columns in table 'schedule_resource':
 * @property integer $id
 * @property integer $activityid
 * @property integer $projectid
 * @property integer $resourceid
 * @property double $capacity
 * @property double $utilised
 * @property integer $status
 */
class ScheduleResource extends \yii\db\ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ScheduleResource the static model class
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
		return 'schedule_resource';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('activityid, projectid, resourceid, capacity, utilised, status', 'required'),
			array('activityid, projectid, resourceid, status', 'numerical', 'integerOnly'=>true),
			array('capacity, utilised', 'numerical'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, activityid, projectid, resourceid, capacity, utilised, status', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'activityid' => 'Activityid',
			'projectid' => 'Projectid',
			'resourceid' => 'Resourceid',
			'capacity' => 'Capacity',
			'utilised' => 'Utilised',
			'status' => 'Status',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('activityid',$this->activityid);
		$criteria->compare('projectid',$this->projectid);
		$criteria->compare('resourceid',$this->resourceid);
		$criteria->compare('capacity',$this->capacity);
		$criteria->compare('utilised',$this->utilised);
		$criteria->compare('status',$this->status);

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