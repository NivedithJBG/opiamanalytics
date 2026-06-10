<?php

/**
 * This is the model class for table "schedule_task".
 *
 * The followings are the available columns in table 'schedule_task':
 * @property integer $id
 * @property integer $wbs_id
 * @property integer $activity_Id
 * @property integer $process_Id
 * @property integer $task_Id
 * @property double $Budgeted_Duration
 * @property double $End_Duration
 * @property integer $status
 * @property integer $wbs_activity_Id
 * @property string $activity_name
 */
class ScheduleTask extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'schedule_task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('wbs_id, activity_Id, process_Id, task_Id, Budgeted_Duration, End_Duration, status, wbs_activity_Id, activity_name', 'required'),
			array('wbs_id, activity_Id, process_Id, task_Id, status, wbs_activity_Id', 'numerical', 'integerOnly'=>true),
			array('Budgeted_Duration, End_Duration', 'numerical'),
			array('activity_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, wbs_id, activity_Id, process_Id, task_Id, Budgeted_Duration, End_Duration, status, wbs_activity_Id, activity_name', 'safe', 'on'=>'search'),
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
			'wbs_id' => 'Wbs',
			'activity_Id' => 'Activity',
			'process_Id' => 'Process',
			'task_Id' => 'Task',
			'Budgeted_Duration' => 'Budgeted Duration',
			'End_Duration' => 'End Duration',
			'status' => 'Status',
			'wbs_activity_Id' => 'Wbs Activity',
			'activity_name' => 'Activity Name',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('wbs_id',$this->wbs_id);
		$criteria->compare('activity_Id',$this->activity_Id);
		$criteria->compare('process_Id',$this->process_Id);
		$criteria->compare('task_Id',$this->task_Id);
		$criteria->compare('Budgeted_Duration',$this->Budgeted_Duration);
		$criteria->compare('End_Duration',$this->End_Duration);
		$criteria->compare('status',$this->status);
		$criteria->compare('wbs_activity_Id',$this->wbs_activity_Id);
		$criteria->compare('activity_name',$this->activity_name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ScheduleTask the static model class
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
