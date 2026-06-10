<?php

/**
 * This is the model class for table "pro_activities".
 *
 * The followings are the available columns in table 'pro_activities':
 * @property integer $Id
 * @property integer $wg_Id
 * @property integer $activity_Id
 * @property integer $process_Id
 * @property integer $pro_estimate_id
 * @property integer $project_Id
 * @property double $quantity
 * @property integer $sortorder
 */
class ProActivities extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pro_activities';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('wg_Id, activity_Id, process_Id, pro_estimate_id, project_Id, quantity, sortorder', 'required'),
			array('wg_Id, activity_Id, process_Id, pro_estimate_id, project_Id, sortorder', 'numerical', 'integerOnly'=>true),
			array('quantity', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Id, wg_Id, activity_Id, process_Id, pro_estimate_id, project_Id, quantity, sortorder', 'safe', 'on'=>'search'),
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
			'Id' => 'ID',
			'wg_Id' => 'Wg',
			'activity_Id' => 'Activity',
			'process_Id' => 'Process',
			'pro_estimate_id' => 'Pro Estimate',
			'project_Id' => 'Project',
			'quantity' => 'Quantity',
			'sortorder' => 'Sortorder',
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

		$criteria->compare('Id',$this->Id);
		$criteria->compare('wg_Id',$this->wg_Id);
		$criteria->compare('activity_Id',$this->activity_Id);
		$criteria->compare('process_Id',$this->process_Id);
		$criteria->compare('pro_estimate_id',$this->pro_estimate_id);
		$criteria->compare('project_Id',$this->project_Id);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('sortorder',$this->sortorder);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ProActivities the static model class
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
