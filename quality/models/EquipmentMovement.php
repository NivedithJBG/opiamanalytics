<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "user_projects".
 *
 * The followings are the available columns in table 'user_projects':
 * @property integer $id
 * @property integer $userid
 * @property integer $projectid
 */
class EquipmentMovement extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'equipment_movement';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, movefrom, moveto', 'required'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
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


	public function getFromproject()
	{
	    return $this->hasOne(Projects::className(), ['Project_Id' => 'movefrom']);
	}

	public function getToproject()
	{
	    return $this->hasOne(Projects::className(), ['Project_Id' => 'moveto']);
	}

	public function getEquipment()
	{
	    return $this->hasOne(Resources::className(), ['Resource_Id' => 'equipment_id']);
	}

	
}
