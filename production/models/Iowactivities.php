<?php

/**
 * This is the model class for table "iowactivities".
 *
 * The followings are the available columns in table 'iowactivities':
 * @property integer $Activity_Id
 * @property integer $IOW_Id
 * @property string $Name
 * @property string $Budgeted_Duration
 * @property string $End_Duration
 */
class Iowactivities extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Iowactivities the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'iowactivities';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(/*
			array('IOW_Id, Name, Budgeted_Duration, End_Duration', 'required'),
			array('IOW_Id', 'numerical', 'integerOnly'=>true),
			array('Name', 'length', 'max'=>250),
			array('Budgeted_Duration, End_Duration', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Activity_Id, IOW_Id, Name, Budgeted_Duration, End_Duration', 'safe', 'on'=>'search'),
		*/);
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
			'Activity_Id' => 'Activity',
			'IOW_Id' => 'Iow',
			'Name' => 'Name',
			'Budgeted_Duration' => 'Budgeted Duration',
			'End_Duration' => 'End Duration',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('Activity_Id',$this->Activity_Id);
		$criteria->compare('IOW_Id',$this->IOW_Id);
		$criteria->compare('Name',$this->Name,true);
		$criteria->compare('Budgeted_Duration',$this->Budgeted_Duration,true);
		$criteria->compare('End_Duration',$this->End_Duration,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}