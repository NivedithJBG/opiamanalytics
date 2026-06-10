<?php

namespace app\models;
use Yii;


/**
 * This is the model class for table "notifications".
 *
 * The followings are the available columns in table 'notifications':
 * @property integer $notf_id
 * @property integer $itemid
 * @property string $type
 * @property integer $user_id
 * @property integer $assigned_user
 * @property integer $status
 */
class Notifications extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	
	public static function tableName()
	{
		return 'notifications';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('itemid, type, user_id, assigned_user, status', 'required'),
			array('itemid, user_id, assigned_user, status', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>155),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('notf_id, itemid, type, user_id, assigned_user, status', 'safe', 'on'=>'search'),
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
			'notf_id' => 'Notf',
			'itemid' => 'Itemid',
			'type' => 'Type',
			'user_id' => 'User',
			'assigned_user' => 'Assigned User',
			'status' => 'Status',
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

		$criteria->compare('notf_id',$this->notf_id);
		$criteria->compare('itemid',$this->itemid);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('assigned_user',$this->assigned_user);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Notifications the static model class
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
