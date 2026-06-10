<?php

namespace app\models;
use Yii;


/**
 * This is the model class for table "muster".
 *
 * The followings are the available columns in table 'muster':
 * @property integer $muster_id
 * @property integer $activity_id
 * @property string $date
 * @property string $name
 * @property integer $trade_id
 * @property double $hours
 * @property double $othours
 * @property double $wages
 * @property double $project_id
 * @property double $userid
 * @property double $groupid
 * @property double $process
 * @property double $status
 * @property double $order_id
 */
class Muster extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'muster';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('activity_id, date, name, trade_id, hours, othours, wages', 'required'),
			array('activity_id, trade_id', 'numerical', 'integerOnly'=>true),
			array('hours, othours, wages', 'numerical'),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('muster_id, activity_id, date, name, trade_id, hours, othours, wages,project_id,userid,groupid,process', 'safe', 'on'=>'search'),
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
			'muster_id' => 'Muster',
			'activity_id' => 'Activity',
			'date' => 'Date',
			'name' => 'Name',
			'trade_id' => 'Trade',
			'hours' => 'Hours',
			'othours' => 'Othours',
			'wages' => 'Wages',
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

		$criteria->compare('muster_id',$this->muster_id);
		$criteria->compare('activity_id',$this->activity_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('trade_id',$this->trade_id);
		$criteria->compare('hours',$this->hours);
		$criteria->compare('othours',$this->othours);
		$criteria->compare('wages',$this->wages);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Muster the static model class
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
