<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_accounts".
 *
 * The followings are the available columns in table 'user_accounts':
 * @property integer $user_acnt_id
 * @property integer $user_id
 * @property integer $account_id
 * @property integer $account_type
 * @property integer $schedule
 */
class UserAccounts extends \yii\db\ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserAccounts the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'user_accounts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, account_id, account_type, schedule', 'required'),
			array('user_id, account_id, account_type, schedule', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_acnt_id, user_id, account_id, account_type, schedule', 'safe', 'on'=>'search'),
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
			'user_acnt_id' => 'User Acnt',
			'user_id' => 'User',
			'account_id' => 'Account',
			'account_type' => 'Account Type',
			'schedule' => 'Schedule',
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

		$criteria->compare('user_acnt_id',$this->user_acnt_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('account_id',$this->account_id);
		$criteria->compare('account_type',$this->account_type);
		$criteria->compare('schedule',$this->schedule);

		return new CActiveDataProvider(get_class($this), array(
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