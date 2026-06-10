<?php

/**
 * This is the model class for table "measurement_book".
 *
 * The followings are the available columns in table 'measurement_book':
 * @property integer $id
 * @property integer $boq_id
 * @property string $Notes
 * @property string $Itemhead
 * @property string $Description
 * @property integer $Number1
 * @property integer $Number2
 * @property integer $Number3
 * @property double $Length
 * @property double $Width
 * @property double $Depth
 * @property double $Quantity
 * @property string $Date
 * @property integer $Status
 */
class MeasurementBook extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'measurement_book';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('boq_id, Notes, Itemhead, Description, Number1, Number2, Number3, Length, Width, Depth, Quantity, Date, Status', 'required'),
			array('boq_id, Number1, Number2, Number3, Status', 'numerical', 'integerOnly'=>true),
			array('Length, Width, Depth, Quantity', 'numerical'),
			array('Notes, Itemhead, Description', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, boq_id, Notes, Itemhead, Description, Number1, Number2, Number3, Length, Width, Depth, Quantity, Date, Status', 'safe', 'on'=>'search'),
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
			'boq_id' => 'Boq',
			'Notes' => 'Notes',
			'Itemhead' => 'Itemhead',
			'Description' => 'Description',
			'Number1' => 'Number1',
			'Number2' => 'Number2',
			'Number3' => 'Number3',
			'Length' => 'Length',
			'Width' => 'Width',
			'Depth' => 'Depth',
			'Quantity' => 'Quantity',
			'Date' => 'Date',
			'Status' => 'Status',
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
		$criteria->compare('boq_id',$this->boq_id);
		$criteria->compare('Notes',$this->Notes,true);
		$criteria->compare('Itemhead',$this->Itemhead,true);
		$criteria->compare('Description',$this->Description,true);
		$criteria->compare('Number1',$this->Number1);
		$criteria->compare('Number2',$this->Number2);
		$criteria->compare('Number3',$this->Number3);
		$criteria->compare('Length',$this->Length);
		$criteria->compare('Width',$this->Width);
		$criteria->compare('Depth',$this->Depth);
		$criteria->compare('Quantity',$this->Quantity);
		$criteria->compare('Date',$this->Date,true);
		$criteria->compare('Status',$this->Status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MeasurementBook the static model class
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
