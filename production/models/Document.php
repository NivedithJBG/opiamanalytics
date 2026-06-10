<?php

/**
 * This is the model class for table "document".
 *
 * The followings are the available columns in table 'document':
 * @property integer $doc_id
 * @property integer $department
 * @property integer $createdby
 * @property integer $addresse
 * @property string $subject
 * @property string $date
 * @property string $content
 * @property string $status
 * @property string $project_id
 * @property string $ref
 * @property string $doctype
 * @property string $despatch
 * @property string $despatch_number
 * @property string $folder
 * @property string $despatch_date
 * @property string $mode
 * @property string $remarks
 * @property integer $corrections
 * @property integer $forward
 */
class Document extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'document';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('department, createdby, addresse, subject, date, content', 'required'),
			array('department, createdby, addresse', 'numerical', 'integerOnly'=>true),
			array('subject', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('doc_id, department, createdby, addresse, subject, date, content', 'safe', 'on'=>'search'),
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
			'doc_id' => 'Doc',
			'department' => 'Department',
			'createdby' => 'Createdby',
			'addresse' => 'Addresse',
			'subject' => 'Subject',
			'date' => 'Date',
			'content' => 'Content',
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

		$criteria->compare('doc_id',$this->doc_id);
		$criteria->compare('department',$this->department);
		$criteria->compare('createdby',$this->createdby);
		$criteria->compare('addresse',$this->addresse);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('content',$this->content,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Document the static model class
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
