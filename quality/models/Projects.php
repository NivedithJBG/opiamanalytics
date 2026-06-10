<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "projects".
 *
 * The followings are the available columns in table 'projects':
 * @property integer $Project_Id
 * @property string $Name
 * @property string $Added_On
 * @property string $Updated_On
 * @property integer $Added_By
 * @property integer $Status
 * @property integer $Gantt_status
 * @property integer $sector
 * @property integer $Project_Delete_Status
 */
class Projects extends \yii\db\ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return projects the static model class
	 */
	/*public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}*/

	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'projects';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Name', 'required'),
			array('Added_By, Status', 'numerical', 'integerOnly'=>true),
			array('Name', 'length', 'max'=>250),
			array('Updated_On,Added_On,Added_By,Status,Gantt_status', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Project_Id, Name, Added_On, Updated_On, Added_By, Status,sector,Project_Delete_Status', 'safe', 'on'=>'search'),
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
			'Project_Id' => 'Project',
			'Name' => 'Name',
			'Added_On' => 'Added On',
			'Updated_On' => 'Updated On',
			'Added_By' => 'Added By',
			'Status' => 'Status',
			'ProjectDeleteStatus' => 'Project_Delete_Status',
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

		$criteria->compare('Project_Id',$this->Project_Id);
		$criteria->compare('Name',$this->Name,true);
		$criteria->compare('Added_On',$this->Added_On,true);
		$criteria->compare('Updated_On',$this->Updated_On,true);
		$criteria->compare('Added_By',$this->Added_By);
		$criteria->compare('Status',$this->Status);
		$criteria->compare('ProjectDeleteStatus',$this->Project_Delete_Status);

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