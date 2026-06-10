<?php

namespace app\models;
use Yii;
/**
 * This is the model class for table "issue_slips".
 *
 * The followings are the available columns in table 'issue_slips':
 * @property integer $slip_id
 * @property string $date
 * @property integer $project_id
 * @property integer $iow_id
 * @property integer $activity_id
 * @property string $activity_unit
 * @property double $activity_qty
 * @property integer $resource_id
 * @property string $resource_unit
 * @property double $resource_qty
 * @property integer $user_id
 * @property string $slip_date
 * @property integer $group_id
 * @property integer $delete_status
 */
class IssueSlips extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'issue_slips';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, project_id, iow_id, activity_id, activity_unit, activity_qty, resource_id, resource_unit, resource_qty, user_id, slip_date, group_id', 'required'),
			array('project_id, iow_id, activity_id, resource_id, user_id, group_id', 'numerical', 'integerOnly'=>true),
			array('activity_qty, resource_qty', 'numerical'),
			array('activity_unit, resource_unit', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('slip_id, date, project_id, iow_id, activity_id, activity_unit, activity_qty, resource_id, resource_unit, resource_qty, user_id, slip_date, group_id', 'safe', 'on'=>'search'),
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
			'slip_id' => 'Slip',
			'date' => 'Date',
			'project_id' => 'Project',
			'iow_id' => 'Iow',
			'activity_id' => 'Activity',
			'activity_unit' => 'Activity Unit',
			'activity_qty' => 'Activity Qty',
			'resource_id' => 'Resource',
			'resource_unit' => 'Resource Unit',
			'resource_qty' => 'Resource Qty',
			'user_id' => 'User',
			'slip_date' => 'Slip Date',
			'group_id' => 'Group',
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

		$criteria->compare('slip_id',$this->slip_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('iow_id',$this->iow_id);
		$criteria->compare('activity_id',$this->activity_id);
		$criteria->compare('activity_unit',$this->activity_unit,true);
		$criteria->compare('activity_qty',$this->activity_qty);
		$criteria->compare('resource_id',$this->resource_id);
		$criteria->compare('resource_unit',$this->resource_unit,true);
		$criteria->compare('resource_qty',$this->resource_qty);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('slip_date',$this->slip_date,true);
		$criteria->compare('group_id',$this->group_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return IssueSlips the static model class
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
