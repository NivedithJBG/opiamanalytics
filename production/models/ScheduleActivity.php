<?php

/**
 * This is the model class for table "schedule_activity".
 *
 * The followings are the available columns in table 'schedule_activity':
 * @property integer $id
 * @property integer $Project_Id
 * @property integer $actvity_id
 * @property integer $process_id
 * @property integer $wbs_id
 * @property integer $Resourceunits
 * @property integer $Workhours
 * @property integer $Cycles
 * @property string $start_date
 * @property string $end_date
 * @property double $duration
 * @property double $progress
 * @property integer $sortorder
 * @property integer $open
 * @property integer $parent
 * @property string $itemtype
 * @property integer $wbs_activity_Id
 * @property string $activity_name
 */
class ScheduleActivity extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'schedule_activity';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Project_Id, actvity_id, process_id, wbs_id, Resourceunits, Workhours, Cycles, start_date, end_date, duration, progress, sortorder, open, parent, itemtype, wbs_activity_Id, activity_name', 'required'),
			array('Project_Id, actvity_id, process_id, wbs_id, Resourceunits, Workhours, Cycles, sortorder, open, parent, wbs_activity_Id', 'numerical', 'integerOnly'=>true),
			array('duration, progress', 'numerical'),
			array('itemtype', 'length', 'max'=>50),
			array('activity_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, Project_Id, actvity_id, process_id, wbs_id, Resourceunits, Workhours, Cycles, start_date, end_date, duration, progress, sortorder, open, parent, itemtype, wbs_activity_Id, activity_name', 'safe', 'on'=>'search'),
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
			'Project_Id' => 'Project',
			'actvity_id' => 'Actvity',
			'process_id' => 'Process',
			'wbs_id' => 'Wbs',
			'Resourceunits' => 'Resourceunits',
			'Workhours' => 'Workhours',
			'Cycles' => 'Cycles',
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
			'duration' => 'Duration',
			'progress' => 'Progress',
			'sortorder' => 'Sortorder',
			'open' => 'Open',
			'parent' => 'Parent',
			'itemtype' => 'Itemtype',
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
		$criteria->compare('Project_Id',$this->Project_Id);
		$criteria->compare('actvity_id',$this->actvity_id);
		$criteria->compare('process_id',$this->process_id);
		$criteria->compare('wbs_id',$this->wbs_id);
		$criteria->compare('Resourceunits',$this->Resourceunits);
		$criteria->compare('Workhours',$this->Workhours);
		$criteria->compare('Cycles',$this->Cycles);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('duration',$this->duration);
		$criteria->compare('progress',$this->progress);
		$criteria->compare('sortorder',$this->sortorder);
		$criteria->compare('open',$this->open);
		$criteria->compare('parent',$this->parent);
		$criteria->compare('itemtype',$this->itemtype,true);
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
	 * @return ScheduleActivity the static model class
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
