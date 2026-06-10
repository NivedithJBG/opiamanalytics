<?php

namespace app\controllers;	

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\Html;  
use yii\helpers\ArrayHelper;  
use app\models\AccountsSub;    
use app\models\Accountschedule; 

class AccountscheduleController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Accountschedule']))
		{
			$model->attributes=$_POST['Accountschedule'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Accountschedule');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Accountschedule('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Accountschedule']))
			$model->attributes=$_GET['Accountschedule'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
    /**
     *Custom function by karthik 
     **/
     public function actionGetsubgroups()
     {
        if($_POST['accountgroup']!='none'):

            $code = AccountsSub::find()->where(['master_id'=>$_POST['accountgroup']])->andWhere(['Status'=> 0])->all();
            $data = ArrayHelper::map($code, 'id', 'name');

            echo "<option value=''>Select Account Sub-Group</option>"; 
            
            foreach($data as $value=>$name)
            {    

             echo "<option value='".$value."'>".$name."</option>";                 

            }
        else:
            echo Html::tag('option',
                           array('value'=>''),'Select Account Sub-Group',true);                                    
        endif;
    }

    public function actionGetschedules()
    {
        if($_POST['accountgroup']!='' & $_POST['accountsubgroup']!=''):



             $code = Accountschedule::find()->where(['master_id'=>$_POST['accountgroup']])->andwhere(['sub_id'=>$_POST['accountsubgroup']])->all();
            $data = ArrayHelper::map($code, 'id', 'name');

            

            echo "<option value=''>Select Account Schedule</option>"; 
            
            foreach($data as $value=>$name)
            {    

             echo "<option value='".$value."'>".$name."</option>";                 
              // echo Html::tag('option',
                       //array('value'=>$value),Html::encode($name),true);
            }
        else:
            //echo "<option value="">Select Account Sub-Group</option>"; 
            echo Html::tag('option',
                           array('value'=>''),'Select Account Schedule',true);                                    
        endif;


           // $data=CHtml::listData(Accountschedule::model()->findAll(array('condition'=>'master_id='.$_POST['accountgroup'].' AND sub_id='.$_POST['accountsubgroup'],'order' => 'sortorder')),'id','name');            
        //     echo CHtml::tag('option',
        //            array('value'=>''),'Select Account Schedule',true);
        //     foreach($data as $value=>$name)
        //     {                     
        //         echo CHtml::tag('option',
        //                array('value'=>$value),CHtml::encode($name),true);
        //     }
        // else:
        //     echo CHtml::tag('option',
        //                    array('value'=>''),'Select Account Schedule',true);                                    
        // endif;                
    }     	
    public function actionCreate()
    {
        $model=new Accountschedule();
        if(isset($_POST['accountschedule'])):
            $model->master_id=$_POST['accountgroup'];
            $model->sub_id=$_POST['accountsubgroup'];
            $model->name=$_POST['accountschedule'];
            $model->sortorder=Accountschedule::model()->find(array('condition'=>'master_id='.$_POST['accountgroup'].' AND sub_id='.$_POST['accountsubgroup'],'order'=>'sortorder DESC'))->sortorder+1;            
            if($model->save()):
                $arr = array('Id' => $model->id, 'Name' => $model->name,'Account group'=>$model->master_id, 'Account subgroup'=>$model->sub_id, 'error'=>'No');
                echo json_encode($arr);
            else:
                $arr = array('id' => '', 'name' => '','error'=>'Yes','errortext'=>'Cannot add now. Please try again later');
                echo json_encode($arr);
            endif;
    
        endif;
    
    } 
    public function actionSearch()
    {
        $connection = CActiveRecord::getDbConnection();
        $sql="SELECT a.id,a.name,a.master_id,a.sub_id,b.name as accountgroup,c.name AS accountsubgroup 
                FROM accounts_schedule as a 
                inner join accountsmaster as b on a.master_id=b.id
                inner join accounts_sub as c on a.sub_id=c.id                
                ";
        if($_POST['acntgrp']!='none')
            $sql.=" WHERE a.master_id='".$_POST['acntgrp']."'";
        if($_POST['acntsubgrp']!='')
            $sql.=" AND a.sub_id='".$_POST['acntsubgrp']."'";            
        if($_POST['acntschedulesname']!='')
            $sql.=" AND a.name LIKE '%".$_POST['acntschedulesname']."%'";
        $sql.=" ORDER BY a.sortorder ASC,id DESC";        
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $datarows='';
        $accounts=Accountsmaster::model()->findAll();
        
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):
                $acntgrp='';
                foreach($accounts AS $acnts):
                    if($data['master_id']==$acnts->id):
                        $seletedtype='Selected="selected"';
                    else:
                        $seletedtype='';
                    endif;
                    $acntgrp.= "<option value='".$acnts->id."' ".$seletedtype.">".$acnts->name."</option>";
                endforeach;
                $datarows.="<tr id='acntschedulesrow".$data['id']."' class='ui-state-default no acntschedule".$data['id']."' data-id='".$data['id']."'>
                            <td  class='small75'>".$data['id']."</td>
                            <td>".$data['accountgroup']."</td>
                            <td>".$data['accountsubgroup']."</td>
                            <td>".$data['name']."</td>
                            <td class='small75'><a href='".Yii::app()->request->baseUrl."/accountschedule/edit?id=".$data['id']."'>
                            <button type='button' title='Edit Account Schedule' class='btn btn-primary' > <span class='glyphicon glyphicon-pencil'></span></button></a></td>
                            <td class='small75'><button type='button' title='Delete Account Schedule' class='btn btn-primary deleteacntschedulesbutton' value='".$data['id']."' id='deleteacntschedulesbutton".$data['id']."'> <span class='glyphicon glyphicon-trash'></span></button></td>
                            </tr>";
            endforeach;
        else:
            $datarows='<tr id="nodata"><td colspan="6" style="text-align: center;">No Account Schedules Found</td></tr>';
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        echo json_encode($arr);
    }  
    public function actionDeleteitem()
    {
        if(isset($_POST['acntschedulesid'])):
            if($this->loadModel($_POST['acntschedulesid'])->delete()):
                $arr = array('Id' => $_POST['acntschedulesid'],'error'=>'No');
                echo json_encode($arr);
            else:
                $arr = array('Id' => $_POST['acntschedulesid'],'error'=>'Yes','errortext'=>'Can not Delete Account Schedule with ID '.$_POST['acntschedulesid'].'.');
                echo json_encode($arr);
            endif;
        endif;
    } 
    public function actionEdit($id)
    {
        $connection = CActiveRecord::getDbConnection();
        $model=$this->loadModel($id);        
        if(isset($_POST['groupid']))
        {
            $model->master_id=$_POST['groupid'];
            $model->sub_id=$_POST['subgroupid'];
            $model->name=$_POST['schedulename'];
            if($model->save()):
                $this->redirect(array('projects/masters'));
            endif;
        }
        $this->render('update',array(
            'model'=>$model,
        ));
    }             
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Accountschedule the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Accountschedule::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Accountschedule $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='accountschedule-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
