<?php

/**
 * This is the model class for table "project_cashflow".
 *
 * The followings are the available columns in table 'project_cashflow':
 * @property integer $cashflow_id
 * @property integer $subgroup_id
 * @property double $amount
 * @property string $month
 * @property integer $project_id
 * @property integer $type
 * @property double $opening_balance
 */
class ProjectCashflow extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'project_cashflow';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('subgroup_id, amount, month, project_id, type', 'required'),
			array('subgroup_id, project_id, type', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('cashflow_id, subgroup_id, amount, month, project_id, type', 'safe', 'on'=>'search'),
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
			'cashflow_id' => 'Cashflow',
			'subgroup_id' => 'Subgroup',
			'amount' => 'Amount',
			'month' => 'Month',
			'project_id' => 'Project',
			'type' => 'Type',
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

		$criteria->compare('cashflow_id',$this->cashflow_id);
		$criteria->compare('subgroup_id',$this->subgroup_id);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('month',$this->month,true);
		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('type',$this->type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ProjectCashflow the static model class
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
