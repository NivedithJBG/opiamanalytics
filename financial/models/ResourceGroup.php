<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "resource_group".
 *
 * The followings are the available columns in table 'resource_group':
 * @property integer $Resource_group_Id
 * @property string $Resource_group_Name
 * @property string $RG_Added_On
 * @property string $RG_Updated_On
 * @property integer $RG_Added_By
 * @property integer $RG_sortorder
 */
class ResourceGroup extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'resource_group';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Resource_group_Name, RG_Added_On, RG_Updated_On, RG_Added_By, RG_sortorder', 'required'),
			array('RG_Added_By, RG_sortorder', 'numerical', 'integerOnly'=>true),
			array('Resource_group_Name', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Resource_group_Id, Resource_group_Name, RG_Added_On, RG_Updated_On, RG_Added_By, RG_sortorder', 'safe', 'on'=>'search'),
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
			'Resource_group_Id' => 'Resource Group',
			'Resource_group_Name' => 'Resource Group Name',
			'RG_Added_On' => 'Rg Added On',
			'RG_Updated_On' => 'Rg Updated On',
			'RG_Added_By' => 'Rg Added By',
			'RG_sortorder' => 'Rg Sortorder',
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

		$criteria->compare('Resource_group_Id',$this->Resource_group_Id);
		$criteria->compare('Resource_group_Name',$this->Resource_group_Name,true);
		$criteria->compare('RG_Added_On',$this->RG_Added_On,true);
		$criteria->compare('RG_Updated_On',$this->RG_Updated_On,true);
		$criteria->compare('RG_Added_By',$this->RG_Added_By);
		$criteria->compare('RG_sortorder',$this->RG_sortorder);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ResourceGroup the static model class
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
