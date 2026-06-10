<?php

/**
 * This is the model class for table "activity_report_tasks".
 *
 * The followings are the available columns in table 'activity_report_tasks':
 * @property integer $id
 * @property integer $activity_Id
 * @property integer $task_Id
 * @property string $unit
 * @property integer $quantity
 * @property string $report_date
 * @property string $time_taken
 * @property string $remarks
 * @property integer $status
 * @property integer $draft_status
 */
class ActivityReportTasks extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'activity_report_tasks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('activity_Id, task_Id, unit, quantity, report_date, time_taken, remarks, status, draft_status', 'required'),
			array('activity_Id, task_Id, quantity, status, draft_status', 'numerical', 'integerOnly'=>true),
			array('unit, time_taken, remarks', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, activity_Id, task_Id, unit, quantity, report_date, time_taken, remarks, status, draft_status', 'safe', 'on'=>'search'),
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
			'activity_Id' => 'Activity',
			'task_Id' => 'Task',
			'unit' => 'Unit',
			'quantity' => 'Quantity',
			'report_date' => 'Report Date',
			'time_taken' => 'Time Taken',
			'remarks' => 'Remarks',
			'status' => 'Status',
			'draft_status' => 'Draft Status',
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
		$criteria->compare('activity_Id',$this->activity_Id);
		$criteria->compare('task_Id',$this->task_Id);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('report_date',$this->report_date,true);
		$criteria->compare('time_taken',$this->time_taken,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('draft_status',$this->draft_status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActivityReportTasks the static model class
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
