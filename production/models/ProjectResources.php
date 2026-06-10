<?php

/**
 * This is the model class for table "project_resources".
 *
 * The followings are the available columns in table 'project_resources':
 * @property integer $id
 * @property integer $projectid
 * @property integer $itemid
 * @property integer $resourceid
 * @property double $rate
 * @property double $quantity
 * @property double $specrate
 * @property double $amount
 */
class ProjectResources extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'project_resources';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('projectid, itemid, resourceid, quantity, specrate, amount', 'required'),
			array('projectid, itemid, resourceid', 'numerical', 'integerOnly'=>true),
			array('rate, quantity, specrate, amount', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, projectid, itemid, resourceid, rate, quantity, specrate, amount', 'safe', 'on'=>'search'),
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
			'projectid' => 'Projectid',
			'itemid' => 'Productid',
			'resourceid' => 'Resourceid',
			'rate' => 'Rate',
			'quantity' => 'Quantity',
			'specrate' => 'Specrate',
			'amount' => 'Amount',
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
		$criteria->compare('projectid',$this->projectid);
		$criteria->compare('itemid',$this->itemid);
		$criteria->compare('resourceid',$this->resourceid);
		$criteria->compare('rate',$this->rate);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('specrate',$this->specrate);
		$criteria->compare('amount',$this->amount);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ProjectResources the static model class
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
