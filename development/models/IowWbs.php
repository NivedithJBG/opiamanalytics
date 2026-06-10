<?php

/**
 * This is the model class for table "iow_wbs".
 *
 * The followings are the available columns in table 'iow_wbs':
 * @property integer $iow_wbs_id
 * @property integer $Project_Id
 * @property string $Name
 * @property integer $parent
 * @property string $itemtype
 */
class IowWbs extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'iow_wbs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Project_Id, Name, parent, itemtype', 'required'),
			array('Project_Id, parent', 'numerical', 'integerOnly'=>true),
			array('Name, itemtype', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('iow_wbs_id, Project_Id, Name, parent, itemtype', 'safe', 'on'=>'search'),
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
			'iow_wbs_id' => 'Iow Wbs',
			'Project_Id' => 'Project',
			'Name' => 'Name',
			'parent' => 'Parent',
			'itemtype' => 'Itemtype',
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

		$criteria->compare('iow_wbs_id',$this->iow_wbs_id);
		$criteria->compare('Project_Id',$this->Project_Id);
		$criteria->compare('Name',$this->Name,true);
		$criteria->compare('parent',$this->parent);
		$criteria->compare('itemtype',$this->itemtype,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return IowWbs the static model class
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
