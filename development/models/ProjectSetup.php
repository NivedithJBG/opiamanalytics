<?php

namespace app\models;
use Yii;
/**
 * This is the model class for table "project_setup".
 *
 * The followings are the available columns in table 'project_setup':
 * @property integer $PS_Id
 * @property integer $PS_Project_Id
 * @property string $PS_Name
 * @property string $PS_Unit
 * @property string $PS_Price
 * @property string $PS_Added_On
 * @property string $PS_Updated_On
 * @property integer $PS_Added_By
 * @property integer $PS_Status
 * @property string $PS_Quantity
 * @property string $PS_Amount
 * @property integer $PS_Worktype
 * @property integer $estimate
 * @property integer $enggsortorder
 * @property integer $pricing_status
 */
class ProjectSetup extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'project_setup';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('PS_Project_Id, PS_Name, PS_Unit, PS_Price, PS_Added_On, PS_Updated_On, PS_Added_By, PS_Status, PS_Quantity, PS_Amount', 'required'),
			array('PS_Project_Id, PS_Added_By, PS_Status', 'numerical', 'integerOnly'=>true),
			array('PS_Name, PS_Amount', 'length', 'max'=>50),
			array('PS_Unit, PS_Price, PS_Quantity', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('PS_Id, PS_Project_Id, PS_Name, PS_Unit, PS_Price, PS_Added_On, PS_Updated_On, PS_Added_By, PS_Status, PS_Quantity, PS_Amount', 'safe', 'on'=>'search'),
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
			'PS_Id' => 'Ps',
			'PS_Project_Id' => 'Ps Project',
			'PS_Name' => 'Ps Name',
			'PS_Unit' => 'Ps Unit',
			'PS_Price' => 'Ps Price',
			'PS_Added_On' => 'Ps Added On',
			'PS_Updated_On' => 'Ps Updated On',
			'PS_Added_By' => 'Ps Added By',
			'PS_Status' => 'Ps Status',
			'PS_Quantity' => 'Ps Quantity',
			'PS_Amount' => 'Ps Amount',
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

		$criteria->compare('PS_Id',$this->PS_Id);
		$criteria->compare('PS_Project_Id',$this->PS_Project_Id);
		$criteria->compare('PS_Name',$this->PS_Name,true);
		$criteria->compare('PS_Unit',$this->PS_Unit,true);
		$criteria->compare('PS_Price',$this->PS_Price,true);
		$criteria->compare('PS_Added_On',$this->PS_Added_On,true);
		$criteria->compare('PS_Updated_On',$this->PS_Updated_On,true);
		$criteria->compare('PS_Added_By',$this->PS_Added_By);
		$criteria->compare('PS_Status',$this->PS_Status);
		$criteria->compare('PS_Quantity',$this->PS_Quantity,true);
		$criteria->compare('PS_Amount',$this->PS_Amount,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ProjectSetup the static model class
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
