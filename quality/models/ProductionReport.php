<?php

/**
 * This is the model class for table "production_report".
 *
 * The followings are the available columns in table 'production_report':
 * @property integer $prod_id
 * @property string $date
 * @property integer $project_id
 * @property integer $wbs_id
 * @property integer $iow_id
 * @property integer $activity_id
 * @property string $activity_unit
 * @property double $activity_qnty
 * @property integer $activity_nos
 * @property string $activity_remarks
 * @property integer $group_id
 * @property integer $reported_by
 * @property string $reported_on
 * @property integer $delete_status
 * @property string $activity_name
 */
class ProductionReport extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'production_report';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, project_id, wbs_id, iow_id, activity_id, activity_unit, activity_qnty, activity_nos, activity_remarks, group_id, reported_by, reported_on, delete_status', 'required'),
			array('project_id, wbs_id, iow_id, activity_id, activity_nos, group_id, reported_by, delete_status', 'numerical', 'integerOnly'=>true),
			array('activity_qnty', 'numerical'),
			array('activity_unit, activity_remarks', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('prod_id, date, project_id, wbs_id, iow_id, activity_id, activity_unit, activity_qnty, activity_nos, activity_remarks, group_id, reported_by, reported_on, delete_status', 'safe', 'on'=>'search'),
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
			'prod_id' => 'Prod',
			'date' => 'Date',
			'project_id' => 'Project',
			'wbs_id' => 'Wbs',
			'iow_id' => 'Iow',
			'activity_id' => 'Activity',
			'activity_unit' => 'Activity Unit',
			'activity_qnty' => 'Activity Qnty',
			'activity_nos' => 'Activity Nos',
			'activity_remarks' => 'Activity Remarks',
			'group_id' => 'Group',
			'reported_by' => 'Reported By',
			'reported_on' => 'Reported On',
			'delete_status' => 'Delete Status',
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

		$criteria->compare('prod_id',$this->prod_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('wbs_id',$this->wbs_id);
		$criteria->compare('iow_id',$this->iow_id);
		$criteria->compare('activity_id',$this->activity_id);
		$criteria->compare('activity_unit',$this->activity_unit,true);
		$criteria->compare('activity_qnty',$this->activity_qnty);
		$criteria->compare('activity_nos',$this->activity_nos);
		$criteria->compare('activity_remarks',$this->activity_remarks,true);
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('reported_by',$this->reported_by);
		$criteria->compare('reported_on',$this->reported_on,true);
		$criteria->compare('delete_status',$this->delete_status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ProductionReport the static model class
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
