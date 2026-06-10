<?php

/**
 * This is the model class for table "site_report".
 *
 * The followings are the available columns in table 'site_report':
 * @property integer $report_id
 * @property integer $activityid
 * @property string $unit
 * @property double $quantity_total
 * @property double $quantity_produced
 * @property integer $resourcegroup_id
 * @property integer $reportdate
 */
class SiteReport extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'site_report';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('activityid, unit, quantity_total, quantity_produced, resourcegroup_id', 'required'),
			array('activityid, resourcegroup_id', 'numerical', 'integerOnly'=>true),
			array('quantity_total, quantity_produced', 'numerical'),
			array('unit', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('report_id, activityid, unit, quantity_total, quantity_produced, resourcegroup_id', 'safe', 'on'=>'search'),
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
			'report_id' => 'Report',
			'activityid' => 'Activityid',
			'unit' => 'Unit',
			'quantity_total' => 'Quantity Total',
			'quantity_produced' => 'Quantity Produced',
			'resourcegroup_id' => 'Resourcegroup',
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

		$criteria->compare('report_id',$this->report_id);
		$criteria->compare('activityid',$this->activityid);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('quantity_total',$this->quantity_total);
		$criteria->compare('quantity_produced',$this->quantity_produced);
		$criteria->compare('resourcegroup_id',$this->resourcegroup_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SiteReport the static model class
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
