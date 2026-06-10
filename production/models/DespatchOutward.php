<?php

/**
 * This is the model class for table "despatch_outward".
 *
 * The followings are the available columns in table 'despatch_outward':
 * @property integer $Id
 * @property string $Reference_no
 * @property string $Subject
 * @property string $Function
 * @property string $Filed_with
 * @property string $Date
 * @property string $File
 * @property string $Document
 */
class DespatchOutward extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'despatch_outward';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Reference_no, Subject,Filed_with, Date, File, Document', 'required'),
			array('Reference_no, Function', 'length', 'max'=>50),
			array('Subject, Filed_with, File', 'length', 'max'=>100),
			array('Document', 'length', 'max'=>250),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Id, Reference_no, Subject, Function, Filed_with, Date, File, Document', 'safe', 'on'=>'search'),
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
			'Reference_no' => 'Reference No',
			'Subject' => 'Subject',
			'Function' => 'Function',
			'Filed_with' => 'Filed With',
			'Date' => 'Date',
			'File' => 'File',
			'Document' => 'Document',
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
		$criteria->compare('Reference_no',$this->Reference_no,true);
		$criteria->compare('Subject',$this->Subject,true);
		$criteria->compare('Function',$this->Function,true);
		$criteria->compare('Filed_with',$this->Filed_with,true);
		$criteria->compare('Date',$this->Date,true);
		$criteria->compare('File',$this->File,true);
		$criteria->compare('Document',$this->Document,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DespatchOutward the static model class
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
