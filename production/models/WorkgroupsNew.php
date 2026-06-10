<?php


namespace app\models;

use Yii;
/**
 * This is the model class for table "workgroups_new".
 *
 * The followings are the available columns in table 'workgroups_new':
 * @property integer $Workgroup_Id
 * @property integer $Project_Id
 * @property string $Name
 * @property string $Added_On
 * @property string $Updated_On
 * @property integer $Added_By
 * @property integer $Status
 * @property string $start_date
 * @property string $end_date
 * @property integer $duration
 * @property integer $progress
 * @property integer $open
 * @property integer $sortorder
 * @property integer $parent
 * @property string $itemtype
 * @property integer $worktype
 * @property integer $worktypegroup
 * @property integer $wbs_estimate_id
 */
class WorkgroupsNew extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'workgroups_new';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Project_Id, Name, Added_On, Added_By, Status, start_date, end_date, duration, progress, open, sortorder, parent, itemtype, worktype, worktypegroup, wbs_estimate_id', 'required'),
			array('Project_Id, Added_By, Status, duration, progress, open, sortorder, parent, worktype, worktypegroup, wbs_estimate_id', 'numerical', 'integerOnly'=>true),
			array('Name', 'length', 'max'=>250),
			array('itemtype', 'length', 'max'=>20),
			array('Updated_On', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Workgroup_Id, Project_Id, Name, Added_On, Updated_On, Added_By, Status, start_date, end_date, duration, progress, open, sortorder, parent, itemtype, worktype, worktypegroup, wbs_estimate_id', 'safe', 'on'=>'search'),
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
			'Workgroup_Id' => 'Workgroup',
			'Project_Id' => 'Project',
			'Name' => 'Name',
			'Added_On' => 'Added On',
			'Updated_On' => 'Updated On',
			'Added_By' => 'Added By',
			'Status' => 'Status',
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
			'duration' => 'Duration',
			'progress' => 'Progress',
			'open' => 'Open',
			'sortorder' => 'Sortorder',
			'parent' => 'Parent',
			'itemtype' => 'Itemtype',
			'worktype' => 'Worktype',
			'worktypegroup' => 'Worktypegroup',
			'wbs_estimate_id' => 'Wbs Estimate',
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

		$criteria->compare('Workgroup_Id',$this->Workgroup_Id);
		$criteria->compare('Project_Id',$this->Project_Id);
		$criteria->compare('Name',$this->Name,true);
		$criteria->compare('Added_On',$this->Added_On,true);
		$criteria->compare('Updated_On',$this->Updated_On,true);
		$criteria->compare('Added_By',$this->Added_By);
		$criteria->compare('Status',$this->Status);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('duration',$this->duration);
		$criteria->compare('progress',$this->progress);
		$criteria->compare('open',$this->open);
		$criteria->compare('sortorder',$this->sortorder);
		$criteria->compare('parent',$this->parent);
		$criteria->compare('itemtype',$this->itemtype,true);
		$criteria->compare('worktype',$this->worktype);
		$criteria->compare('worktypegroup',$this->worktypegroup);
		$criteria->compare('wbs_estimate_id',$this->wbs_estimate_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WorkgroupsNew the static model class
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
