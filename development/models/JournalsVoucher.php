<?php

/**
 * This is the model class for table "journals_voucher".
 *
 * The followings are the available columns in table 'journals_voucher':
 * @property integer $id
 * @property string $place
 * @property integer $project_id
 * @property string $voucher_no
 * @property string $date
 * @property integer $debitacnt
 * @property integer $creditacnt
 * @property double $amount
 * @property string $narration
 */
class JournalsVoucher extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'journals_voucher';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('place, project_id, voucher_no, date, debitacnt, creditacnt, amount, narration', 'required'),
			array('project_id, debitacnt, creditacnt', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('place, voucher_no, narration', 'length', 'max'=>250),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, place, project_id, voucher_no, date, debitacnt, creditacnt, amount, narration', 'safe', 'on'=>'search'),
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
			'place' => 'Place',
			'project_id' => 'Project',
			'voucher_no' => 'Voucher No',
			'date' => 'Date',
			'debitacnt' => 'Debitacnt',
			'creditacnt' => 'Creditacnt',
			'amount' => 'Amount',
			'narration' => 'Narration',
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
		$criteria->compare('place',$this->place,true);
		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('voucher_no',$this->voucher_no,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('debitacnt',$this->debitacnt);
		$criteria->compare('creditacnt',$this->creditacnt);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('narration',$this->narration,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return JournalsVoucher the static model class
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
