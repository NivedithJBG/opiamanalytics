<?php

/**
 * This is the model class for table "pricing_estimate_resources".
 *
 * The followings are the available columns in table 'pricing_estimate_resources':
 * @property integer $pricing_resourceid
 * @property integer $activity_id
 * @property integer $resource_Id
 * @property double $quantity
 * @property double $rate
 * @property integer $project_id
 * @property integer $resourcetype_Id
 * @property integer $est_activity_Id
 * @property integer $process_Id
 * @property integer $pricing_status
 * @property integer $operations_status
 */
class PricingEstimateResources extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pricing_estimate_resources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('activity_id, resource_Id, quantity, rate, project_id, resourcetype_Id', 'required'),
			array('activity_id, resource_Id, project_id, resourcetype_Id', 'numerical', 'integerOnly'=>true),
			array('quantity, rate', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('pricing_resourceid, activity_id, resource_Id, quantity, rate, project_id, resourcetype_Id', 'safe', 'on'=>'search'),
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
			'pricing_resourceid' => 'Pricing Resourceid',
			'activity_id' => 'Activity',
			'resource_Id' => 'Resource',
			'quantity' => 'Quantity',
			'rate' => 'Rate',
			'project_id' => 'Project',
			'resourcetype_Id' => 'Resourcetype',
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

		$criteria->compare('pricing_resourceid',$this->pricing_resourceid);
		$criteria->compare('activity_id',$this->activity_id);
		$criteria->compare('resource_Id',$this->resource_Id);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('rate',$this->rate);
		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('resourcetype_Id',$this->resourcetype_Id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PricingEstimateResources the static model class
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
