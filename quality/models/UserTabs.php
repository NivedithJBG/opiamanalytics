<?php


namespace app\models;
//namespace app\models;

use Yii;
use yii\db\ActiveRecord;
//use yii\db\ActiveRecord;
//use app\models\CActiveRecord;
//use yii\base\Model;
/**
 * This is the model class for table "user_tabs".
 *
 * The followings are the available columns in table 'user_tabs':
 * @property integer $usr_tab_id
 * @property integer $user_id
 * @property integer $function_id
 * @property integer $tab_id
 */
//class UserTabs extends CActiveRecord   
class UserTabs extends ActiveRecord 
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserTabs the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'user_tabs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, function_id, tab_id', 'required'),
			array('user_id, function_id, tab_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('usr_tab_id, user_id, function_id, tab_id', 'safe', 'on'=>'search'),
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
			'usr_tab_id' => 'Usr Tab',
			'user_id' => 'User',
			'function_id' => 'Function',
			'tab_id' => 'Tab',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('usr_tab_id',$this->usr_tab_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('function_id',$this->function_id);
		$criteria->compare('tab_id',$this->tab_id);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
}