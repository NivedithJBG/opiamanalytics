<?php

/**
 * This is the model class for table "new_report".
 *
 * The followings are the available columns in table 'new_report':
 * @property integer $reportid
 * @property integer $projid
 * @property integer $wbsid
 * @property integer $activity_Id
 * @property integer $process_Id
 * @property double $totalduration
 * @property integer $status
 * @property string $startdate
 * @property string $enddate
 * @property integer $cycle
 * @property string $report_date
 * @property integer $owner
 * @property integer $updatedby
 * @property integer $hours
 * @property integer $wbs_activity_Id
 */
class NewReport extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'new_report';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('projid, wbsid, activity_Id, process_Id, totalduration, status, startdate, enddate, cycle, report_date, owner, updatedby', 'required'),
			array('projid, wbsid, activity_Id, process_Id, status, cycle, owner, updatedby', 'numerical', 'integerOnly'=>true),
			array('totalduration', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('reportid, projid, wbsid, activity_Id, process_Id, totalduration, status, startdate, enddate, cycle, report_date, owner, updatedby', 'safe', 'on'=>'search'),
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
			'reportid' => 'Reportid',
			'projid' => 'Projid',
			'wbsid' => 'Wbsid',
			'activity_Id' => 'Activity',
			'process_Id' => 'Process',
			'totalduration' => 'Totalduration',
			'status' => 'Status',
			'startdate' => 'Startdate',
			'enddate' => 'Enddate',
			'cycle' => 'Cycle',
			'report_date' => 'Report Date',
			'owner' => 'Owner',
			'updatedby' => 'Updatedby',
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

		$criteria->compare('reportid',$this->reportid);
		$criteria->compare('projid',$this->projid);
		$criteria->compare('wbsid',$this->wbsid);
		$criteria->compare('activity_Id',$this->activity_Id);
		$criteria->compare('process_Id',$this->process_Id);
		$criteria->compare('totalduration',$this->totalduration);
		$criteria->compare('status',$this->status);
		$criteria->compare('startdate',$this->startdate,true);
		$criteria->compare('enddate',$this->enddate,true);
		$criteria->compare('cycle',$this->cycle);
		$criteria->compare('report_date',$this->report_date,true);
		$criteria->compare('owner',$this->owner);
		$criteria->compare('updatedby',$this->updatedby);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NewReport the static model class
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
