<?php


namespace app\models;

use Yii;
/**
 * This is the model class for table "subgroup_accounts".
 *
 * The followings are the available columns in table 'subgroup_accounts':
 * @property integer $id
 * @property integer $group_id
 * @property integer $subgrp_id
 * @property integer $accountschedule_id
 * @property integer $account_id
 * @property integer $bsitem_id
 */
class SubgroupAccounts extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'subgroup_accounts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('group_id, subgrp_id, accountschedule_id, account_id, bsitem_id', 'required'),
			array('group_id, subgrp_id, accountschedule_id, account_id, bsitem_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, group_id, subgrp_id, accountschedule_id, account_id, bsitem_id', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'group_id' => 'Group',
			'subgrp_id' => 'Subgrp',
			'accountschedule_id' => 'Accountschedule',
			'account_id' => 'Account',
			'bsitem_id' => 'Bsitem',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('subgrp_id',$this->subgrp_id);
		$criteria->compare('accountschedule_id',$this->accountschedule_id);
		$criteria->compare('account_id',$this->account_id);
		$criteria->compare('bsitem_id',$this->bsitem_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SubgroupAccounts the static model class
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
