<?php

/**
 * This is the model class for table "iowschmethedology".
 *
 * The followings are the available columns in table 'iowschmethedology':
 * @property integer $methedology_Id
 * @property integer $IOW_Id
 * @property string $methedologies
 * @property integer $pro_estimate_id
 */
class Iowschmethedology extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'iowschmethedology';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('IOW_Id, methedologies, pro_estimate_id', 'required'),
			array('IOW_Id, pro_estimate_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('methedology_Id, IOW_Id, methedologies, pro_estimate_id', 'safe', 'on'=>'search'),
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
			'methedology_Id' => 'Methedology',
			'IOW_Id' => 'Iow',
			'methedologies' => 'Methedologies',
			'pro_estimate_id' => 'Pro Estimate',
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

		$criteria->compare('methedology_Id',$this->methedology_Id);
		$criteria->compare('IOW_Id',$this->IOW_Id);
		$criteria->compare('methedologies',$this->methedologies,true);
		$criteria->compare('pro_estimate_id',$this->pro_estimate_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Iowschmethedology the static model class
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
