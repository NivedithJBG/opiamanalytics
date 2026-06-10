<?php

/**
 * This is the model class for table "iow_wbs_activities".
 *
 * The followings are the available columns in table 'iow_wbs_activities':
 * @property integer $id
 * @property integer $wbs_id
 * @property integer $activity_Id
 * @property string $activity_Name
 * @property string $activity_Unit
 * @property integer $process_Id
 * @property integer $estimate
 * @property integer $project_Id
 * @property double $duration
 * @property double $amount
 * @property string $cycle_Unit
 * @property double $cycle_Quantity
 * @property integer $sortorder
 * @property integer $type
 */
class IowWbsActivities extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'iow_wbs_activities';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('wbs_id, activity_Id, activity_Name, activity_Unit, process_Id, estimate, project_Id, duration, amount, cycle_Unit, cycle_Quantity, sortorder, type', 'required'),
			array('wbs_id, activity_Id, process_Id, estimate, project_Id, sortorder, type', 'numerical', 'integerOnly'=>true),
			array('duration, amount, cycle_Quantity', 'numerical'),
			array('activity_Name, activity_Unit, cycle_Unit', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, wbs_id, activity_Id, activity_Name, activity_Unit, process_Id, estimate, project_Id, duration, amount, cycle_Unit, cycle_Quantity, sortorder, type', 'safe', 'on'=>'search'),
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
			'activity_Name' => 'Activity Name',
			'activity_Unit' => 'Activity Unit',
			'process_Id' => 'Process',
			'estimate' => 'Estimate',
			'project_Id' => 'Project',
			'duration' => 'Duration',
			'amount' => 'Amount',
			'cycle_Unit' => 'Cycle Unit',
			'cycle_Quantity' => 'Cycle Quantity',
			'sortorder' => 'Sortorder',
			'type' => 'Type',
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
		$criteria->compare('activity_Name',$this->activity_Name,true);
		$criteria->compare('activity_Unit',$this->activity_Unit,true);
		$criteria->compare('process_Id',$this->process_Id);
		$criteria->compare('estimate',$this->estimate);
		$criteria->compare('project_Id',$this->project_Id);
		$criteria->compare('duration',$this->duration);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('cycle_Unit',$this->cycle_Unit,true);
		$criteria->compare('cycle_Quantity',$this->cycle_Quantity);
		$criteria->compare('sortorder',$this->sortorder);
		$criteria->compare('type',$this->type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return IowWbsActivities the static model class
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
