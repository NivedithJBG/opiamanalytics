<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "activity_relations".
 *
 * The followings are the available columns in table 'activity_relations':
 * @property integer $id
 * @property integer $precedent_schedule_item
 * @property integer $precedent_activity
 * @property integer $dependent_schedule_item
 * @property integer $dependent_activity
 * @property integer $relation_type
 */
class ActivityRelations extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'activity_relations';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, precedent_schedule_item, precedent_activity, dependent_schedule_item, dependent_activity, relation_type', 'required'),
			array('id, precedent_schedule_item, precedent_activity, dependent_schedule_item, dependent_activity, relation_type', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, precedent_schedule_item, precedent_activity, dependent_schedule_item, dependent_activity, relation_type', 'safe', 'on'=>'search'),
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
			'precedent_schedule_item' => 'Precedent Schedule Item',
			'precedent_activity' => 'Precedent Activity',
			'dependent_schedule_item' => 'Dependent Schedule Item',
			'dependent_activity' => 'Dependent Activity',
			'relation_type' => 'Relation Type',
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
		$criteria->compare('precedent_schedule_item',$this->precedent_schedule_item);
		$criteria->compare('precedent_activity',$this->precedent_activity);
		$criteria->compare('dependent_schedule_item',$this->dependent_schedule_item);
		$criteria->compare('dependent_activity',$this->dependent_activity);
		$criteria->compare('relation_type',$this->relation_type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActivityRelations the static model class
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
