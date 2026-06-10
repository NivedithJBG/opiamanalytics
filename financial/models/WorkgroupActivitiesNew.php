<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "workgroup_activities_new".
 *
 * The followings are the available columns in table 'workgroup_activities_new':
 * @property integer $id
 * @property integer $wbs_id
 * @property integer $worktype_Id
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
 * @property integer $pr_status
 * @property integer $pricing_status
 * @property integer $operations_status
 * @property integer $wbs_estimate_id
 */
class WorkgroupActivitiesNew extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'workgroup_activities_new';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('wbs_id, worktype_Id, activity_Id, activity_Name, activity_Unit, process_Id, estimate, project_Id, duration, amount, cycle_Unit, cycle_Quantity, sortorder, type, pr_status, pricing_status, operations_status, wbs_estimate_id', 'required'),
			array('wbs_id, worktype_Id, activity_Id, process_Id, estimate, project_Id, sortorder, type, pr_status, pricing_status, operations_status, wbs_estimate_id', 'numerical', 'integerOnly'=>true),
			array('duration, amount, cycle_Quantity', 'numerical'),
			array('activity_Name, activity_Unit, cycle_Unit', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, wbs_id, worktype_Id, activity_Id, activity_Name, activity_Unit, process_Id, estimate, project_Id, duration, amount, cycle_Unit, cycle_Quantity, sortorder, type, pr_status, pricing_status, operations_status, wbs_estimate_id', 'safe', 'on'=>'search'),
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
			'worktype_Id' => 'Worktype',
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
			'pr_status' => 'Pr Status',
			'pricing_status' => 'Pricing Status',
			'operations_status' => 'Operations Status',
			'wbs_estimate_id' => 'Wbs Estimate',
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
		$criteria->compare('worktype_Id',$this->worktype_Id);
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
		$criteria->compare('pr_status',$this->pr_status);
		$criteria->compare('pricing_status',$this->pricing_status);
		$criteria->compare('operations_status',$this->operations_status);
		$criteria->compare('wbs_estimate_id',$this->wbs_estimate_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WorkgroupActivitiesNew the static model class
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
