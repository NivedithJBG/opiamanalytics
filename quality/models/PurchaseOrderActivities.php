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
class PurchaseOrderActivities extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'purchase_order_activities';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, resource_id', 'required'),
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

	
}
