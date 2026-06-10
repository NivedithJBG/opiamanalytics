<?php

/**
 * This is the model class for table "reporttasks".
 *
 * The followings are the available columns in table 'reporttasks':
 * @property integer $reporttaskid
 * @property integer $reportid
 * @property string $taskid
 * @property string $name
 * @property integer $actualduration
 * @property integer $sortorder
 * @property integer $cycle
 * @property string $report_date
 */
class Reporttasks extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'reporttasks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('reportid, taskid, name, actualduration, sortorder, cycle, report_date', 'required'),
			array('reportid, actualduration, sortorder, cycle', 'numerical', 'integerOnly'=>true),
			array('taskid', 'length', 'max'=>50),
			array('name', 'length', 'max'=>250),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('reporttaskid, reportid, taskid, name, actualduration, sortorder, cycle, report_date', 'safe', 'on'=>'search'),
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
			'reporttaskid' => 'Reporttaskid',
			'reportid' => 'Reportid',
			'taskid' => 'Taskid',
			'name' => 'Name',
			'actualduration' => 'Actualduration',
			'sortorder' => 'Sortorder',
			'cycle' => 'Cycle',
			'report_date' => 'Report Date',
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

		$criteria->compare('reporttaskid',$this->reporttaskid);
		$criteria->compare('reportid',$this->reportid);
		$criteria->compare('taskid',$this->taskid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('actualduration',$this->actualduration);
		$criteria->compare('sortorder',$this->sortorder);
		$criteria->compare('cycle',$this->cycle);
		$criteria->compare('report_date',$this->report_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Reporttasks the static model class
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
