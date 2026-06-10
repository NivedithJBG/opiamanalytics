<?php

namespace app\models;
use Yii;


/**
 * This is the model class for table "journels".
 *
 * The followings are the available columns in table 'journels':
 * @property integer $id
 * @property string $date
 * @property integer $projectid
 * @property integer $creditacnt
 * @property string $narration
 * @property double $total
 * @property double $groupid
 * @property double $User_Id
 * @property double $Status
 * @property double $place
 * @property double $bill_no
 * @property double $bill_id
 * @property double $order_id
 */
class Journels extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'journels';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, projectid, creditacnt, narration, total', 'required'),
			array('projectid, creditacnt', 'numerical', 'integerOnly'=>true),
			array('total', 'numerical'),
			array('narration', 'length', 'max'=>250),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, date, projectid, creditacnt, narration, total,groupid,User_Id,Status,place', 'safe', 'on'=>'search'),
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
			'date' => 'Date',
			'projectid' => 'Projectid',
			'creditacnt' => 'Creditacnt',
			'narration' => 'Narration',
			'total' => 'Total',
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
		$criteria->compare('date',$this->date,true);
		$criteria->compare('projectid',$this->projectid);
		$criteria->compare('creditacnt',$this->creditacnt);
		$criteria->compare('narration',$this->narration,true);
		$criteria->compare('total',$this->total);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Journels the static model class
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
