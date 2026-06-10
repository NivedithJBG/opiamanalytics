<?php

/**
 * This is the model class for table "activity_report_resources_log".
 *
 * The followings are the available columns in table 'activity_report_resources_log':
 * @property integer $activity_report_id
 * @property integer $activityid
 * @property integer $resource_id
 * @property string $unit
 * @property double $quantity_used
 * @property integer $project_id
 * @property string $date
 * @property integer $status
 * @property integer $save_draft_quantity
 * @property integer $schedule_activity_Id
 * @property integer $draft_status
 */
class ActivityReportResourcesLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'activity_report_resources_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('activityid, resource_id, unit, quantity_used, project_id, date, status, save_draft_quantity, schedule_activity_Id, draft_status', 'required'),
			array('activityid, resource_id, project_id, status, save_draft_quantity, schedule_activity_Id, draft_status', 'numerical', 'integerOnly'=>true),
			array('quantity_used', 'numerical'),
			array('unit', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('activity_report_id, activityid, resource_id, unit, quantity_used, project_id, date, status, save_draft_quantity, schedule_activity_Id, draft_status', 'safe', 'on'=>'search'),
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
			'activity_report_id' => 'Activity Report',
			'activityid' => 'Activityid',
			'resource_id' => 'Resource',
			'unit' => 'Unit',
			'quantity_used' => 'Quantity Used',
			'project_id' => 'Project',
			'date' => 'Date',
			'status' => 'Status',
			'save_draft_quantity' => 'Save Draft Quantity',
			'schedule_activity_Id' => 'Schedule Activity',
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

		$criteria->compare('activity_report_id',$this->activity_report_id);
		$criteria->compare('activityid',$this->activityid);
		$criteria->compare('resource_id',$this->resource_id);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('quantity_used',$this->quantity_used);
		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('save_draft_quantity',$this->save_draft_quantity);
		$criteria->compare('schedule_activity_Id',$this->schedule_activity_Id);
		$criteria->compare('draft_status',$this->draft_status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActivityReportResourcesLog the static model class
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
