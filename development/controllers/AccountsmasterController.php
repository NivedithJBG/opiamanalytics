<?php


namespace app\controllers;  

use Yii;
use yii\filters\AccessControl; 
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;   
use app\models\Accountsmaster;


class AccountsmasterController extends Controller
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
    public function actionCreate()
    {
        $model=new Accountsmaster();
        if(isset($_POST['acntgrpsname'])):
            $model->name=$_POST['acntgrpsname'];
            /*$model->Added_By=Yii::app()->user->id;*/
            if($model->save(false)):
                $arr = array('Id' => $model->id, 'Name' => $model->name,'error'=>'No');
                return json_encode($arr);
            else:
                $arr = array('id' => '', 'name' => '','error'=>'Yes','errortext'=>'Cannot add now. Please try again later');
                return json_encode($arr);
            endif;

        endif;

    }
    public function actionSearch()
    {
        $connection = \Yii::$app->db; 
        $sql="SELECT id,name FROM accountsmaster WHERE Status=0";
        if($_POST['acntgrpsname']!='')
            $sql.=" AND Name LIKE '%".$_POST['acntgrpsname']."%'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $datarows='';
        $accsubgrp='';
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):

                  $accsubgrp.=' 
                  			<div class="account-group-cntnt-wrpr row acntgrps'.$data['id'].'" id="acntgrpsrow'.$data['id'].'">
                                <div class="col-md-10 col-sm-10">
                                    <div class="row">
                                        <div class="col-md-1 col-sm-1 finance-list-number">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <span class="number indent-number">'.($key + 1).'</span> 
                                                 <input type="hidden" value="'.$data['id'].'"> 
                                                </div>
                                                
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-5 col-sm-5 type finance-type">
                                            
                                            <span id="acntgrpstext'.$data['id'].'">'.$data['name'].'</span>
                                         <input class="form-control editacntgrpsname" style="display:none" type="text" id="editacntgrpsname'.$data['id'].'" value="'.$data['name'].'"><span class="error"></span>  

                                        </div>
                                        
                                        <div class="col-md-6 col-sm-6"></div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-2 icon-groups">

                             		<button type="" class="editacntgrpsbutton" value="'.$data['id'].'" id="editacntgrpsbutton'.$data['id'].'" title="Edit Account Groups"> <a href="#" class="icon-pencil"></a></button>
                               

                            		<button type="" class="saveacntgrpsbutton" style="display:none" value="'.$data['id'].'" id="saveacntgrpsbutton'.$data['id'].'"><a title="Save Account Group" href="#" class="icon-save"></a></button>
                                  

                                 
                             		<button type=""  class="childacntgrps" value="'.$data['id'].'" id="childacntgrps'.$data['id'].'"><a href="#" class="account-subgroup-btn" title="Account Sub Groups">Account Sub Groups</a></button>
                               
                                   
                                	<button type="" class="deletecntgrpsbutton" value="'.$data['id'].'" id="deletecntgrpsbutton'.$data['id'].'"><a title="Delete Account Group" href="#" class="icon-trash1"></a> </button>

                              	</div>
                            </div>';

            endforeach;
        else:
            $accsubgrp='<div class="account-group-cntnt-wrpr row acntgrps"><div colspan="9" style="text-align: center">No Account Groups Found</div></div>';

          endif;
        $arr = array('result' => $accsubgrp,'error'=>'No');
        return json_encode($arr);
    }
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
    public function actionUpdate()
    {
        if(isset($_POST['acntgrpsid']))
        {
            $model=$this->findModel($_POST['acntgrpsid']);
            $model->name=$_POST['name'];
            if($model->save(false)):
                $arr = array('Id' => $_POST['acntgrpsid'], 'Name' => $_POST['name'],'error'=>'No');
                return json_encode($arr);
            else:
                $arr = array('Id' => $_POST['restypeid'],'error'=>'Yes','errortext'=>'Can not update now.');
                return json_encode($arr);
            endif;
        }

    }
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
    public function actionDeleteitem()
    {
        if(isset($_POST['acntgrpsid'])):
        	$model = $this->findModel($_POST['acntgrpsid']);
        	$model->Status = 1;
        	$model->save(false);
            $arr = array('Id' => $_POST['acntgrpsid'],'error'=>'No');
            return json_encode($arr);
        else:
            $arr = array('Id' => $_POST['restypeid'],'error'=>'Yes','errortext'=>'Can not Delete Resource Type with ID '.$_POST['resid'].'.');
           return json_encode($arr);
        endif;
    }
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Accountsmaster');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Accountsmaster('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Accountsmaster']))
			$model->attributes=$_GET['Accountsmaster'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Accountsmaster the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Accountsmaster::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

   public function findModel($id)
	{
		 $model=Accountsmaster::findOne($id);

		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}



	/**
	 * Performs the AJAX validation.
	 * @param Accountsmaster $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='accountsmaster-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
