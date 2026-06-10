<?php

/**
 * This is the model class for table "investments".
 *
 * The followings are the available columns in table 'investments':
 * @property integer $Investments_Id
 * @property integer $IN_Project_Id
 * @property string $IN_Name
 * @property string $IN_Unit
 * @property string $IN_Price
 * @property string $IN_Added_On
 * @property string $IN_Updated_On
 * @property integer $IN_Status
 * @property string $IN_Quantity
 * @property string $IN_Amount
 * @property integer $IN_Added_By
 * @property integer $worktype
 * @property integer $estimate
 * @property integer $schedule
 * @property integer $enggsortorder
 * @property integer $process_id
 * @property integer $pricing_status
 */
class Investments extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'investments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('IN_Project_Id, IN_Name, IN_Unit, IN_Price, IN_Added_On, IN_Updated_On, IN_Status, IN_Quantity, IN_Amount, IN_Added_By', 'required'),
			array('IN_Project_Id, IN_Status, IN_Added_By', 'numerical', 'integerOnly'=>true),
			array('IN_Name, IN_Unit', 'length', 'max'=>50),
			array('IN_Price, IN_Quantity, IN_Amount', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Investments_Id, IN_Project_Id, IN_Name, IN_Unit, IN_Price, IN_Added_On, IN_Updated_On, IN_Status, IN_Quantity, IN_Amount, IN_Added_By', 'safe', 'on'=>'search'),
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
			'Investments_Id' => 'Investments',
			'IN_Project_Id' => 'In Project',
			'IN_Name' => 'In Name',
			'IN_Unit' => 'In Unit',
			'IN_Price' => 'In Price',
			'IN_Added_On' => 'In Added On',
			'IN_Updated_On' => 'In Updated On',
			'IN_Status' => 'In Status',
			'IN_Quantity' => 'In Quantity',
			'IN_Amount' => 'In Amount',
			'IN_Added_By' => 'In Added By',
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

		$criteria->compare('Investments_Id',$this->Investments_Id);
		$criteria->compare('IN_Project_Id',$this->IN_Project_Id);
		$criteria->compare('IN_Name',$this->IN_Name,true);
		$criteria->compare('IN_Unit',$this->IN_Unit,true);
		$criteria->compare('IN_Price',$this->IN_Price,true);
		$criteria->compare('IN_Added_On',$this->IN_Added_On,true);
		$criteria->compare('IN_Updated_On',$this->IN_Updated_On,true);
		$criteria->compare('IN_Status',$this->IN_Status);
		$criteria->compare('IN_Quantity',$this->IN_Quantity,true);
		$criteria->compare('IN_Amount',$this->IN_Amount,true);
		$criteria->compare('IN_Added_By',$this->IN_Added_By);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Investments the static model class
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
