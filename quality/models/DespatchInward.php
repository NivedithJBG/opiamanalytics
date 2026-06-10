<?php

/**
 * This is the model class for table "despatch_inward".
 *
 * The followings are the available columns in table 'despatch_inward':
 * @property integer $Id
 * @property string $SlNo
 * @property string $Date
 * @property string $Department
 * @property string $DraftedBy
 * @property string $Subject
 * @property string $Time
 * @property string $Addressto
 * @property string $Status
 * @property string $Document
 * @property string $Addressee
 */
class DespatchInward extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'despatch_inward';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Subject', 'required'),
			array('SlNo', 'length', 'max'=>50),
			array('Department, DraftedBy, Subject, Addressto, Document', 'length', 'max'=>250),
			array('Status', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Id, SlNo, Date, Department, DraftedBy, Subject, Time, Addressto, Status, Document,Addressee', 'safe', 'on'=>'search'),
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
			'SlNo' => 'Sl No',
			'Date' => 'Date',
			'Department' => 'Department',
			'DraftedBy' => 'Drafted By',
			'Subject' => 'Subject',
			'Time' => 'Time',
			'Addressto' => 'Addressto',
			'Status' => 'Status',
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
		$criteria->compare('SlNo',$this->SlNo,true);
		$criteria->compare('Date',$this->Date,true);
		$criteria->compare('Department',$this->Department,true);
		$criteria->compare('DraftedBy',$this->DraftedBy,true);
		$criteria->compare('Subject',$this->Subject,true);
		$criteria->compare('Time',$this->Time,true);
		$criteria->compare('Addressto',$this->Addressto,true);
		$criteria->compare('Status',$this->Status,true);
		$criteria->compare('Document',$this->Document,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DespatchInward the static model class
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
