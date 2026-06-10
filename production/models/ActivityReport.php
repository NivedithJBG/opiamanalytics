<?php

/**
 * This is the model class for table "activity_report".
 *
 * The followings are the available columns in table 'activity_report':
 * @property integer $id
 * @property integer $activity_id
 * @property integer $item_id
 * @property integer $total_quantity
 * @property integer $projectid
 * @property string $updated_at
 * @property integer $status
 * @property integer $draft_status
 */
class ActivityReport extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'activity_report';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('activity_id, item_id, projectid, updated_at, status, draft_status', 'required'),
			array('activity_id, item_id, total_quantity, projectid, status, draft_status', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, activity_id, item_id, total_quantity, projectid, updated_at, status, draft_status', 'safe', 'on'=>'search'),
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
			'activity_id' => 'Activity',
			'item_id' => 'Item',
			'total_quantity' => 'Total Quantity',
			'projectid' => 'Projectid',
			'updated_at' => 'Updated At',
			'status' => 'Status',
			'draft_status' => 'Draft Status',
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
		$criteria->compare('activity_id',$this->activity_id);
		$criteria->compare('item_id',$this->item_id);
		$criteria->compare('total_quantity',$this->total_quantity);
		$criteria->compare('projectid',$this->projectid);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('draft_status',$this->draft_status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActivityReport the static model class
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
