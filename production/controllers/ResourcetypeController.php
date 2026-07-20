<?php

namespace app\controllers;  

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\ResourceGroup;  
use app\models\AccountsSub;  
use app\models\Resourcetype;
class ResourcetypeController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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

    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','create','Search','Update','DeleteItem','checkname','Testaction'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('Updatesort'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
                'actions'=>array('admin','view','delete'),
				'users'=>array('*'),
			),
		);
	}

    public function actionIndex()
    {
        $connection = CActiveRecord::getDbConnection();
        $sql="SELECT Name FROM resourcetype  WHERE status='0' ORDER BY ResourceType_Id ASC, Name ASC";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $resname=$dataReader->readAll();
        $dataProvider='';
        foreach($resname AS $key=>$res):
            if($key==0):
                $dataProvider="'".$res['Name']."'";
            else:
                $dataProvider.=",'".$res['Name']."'";
            endif;
        endforeach;

        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));
    }

    public function actionCreate()
    {
        $model=new Resourcetype();
        if(isset($_POST['restypename'])):
            $model->Name=$_POST['restypename'];
            if(isset($_POST['resgroup'])):
            $model->Resource_group_Id=$_POST['resgroup'];
            endif;
            $model->Added_On=date('y-m-d h:i:s');
            $model->Added_By=Yii::$app->user->id;
            if(isset($_POST['sourceunit'])):
            $model->Source_unit=$_POST['sourceunit'];
            endif;
            $model->Project_Type = isset($_POST['projecttype']) ? $_POST['projecttype'] : 0;
            if($model->save()):

                $model->sortorder=$model->ResourceType_Id;
                $model->save();

                $accmodel=new AccountsSub();
                //$accmodel->master_id=1;
                $accmodel->master_id=$_POST['addacntgrp'];
                $accmodel->sortorder= 0;
                $accmodel->name=$_POST['restypename'];
                $accmodel->ResourceType_Id=$model->ResourceType_Id;
                $accmodel->save(false);
                $accmodel->sortorder= $accmodel->id;
                $accmodel->save(false);

                $model->accountsub_id=$accmodel->id;
                $model->save();

                $arr = array('Id' => $model->ResourceType_Id, 'Name' => $model->Name,'error'=>'No');
                return json_encode($arr);
            else:
                $arr = array('id' => '', 'Name' => '','error'=>'Yes','errortext'=>'Cannot add now. Please try again later');
                return json_encode($arr);
            endif;

        endif;

    }
    public function actionSearch()
    {
        $connection = \Yii::$app->db;
        $search = isset($_POST['restypename']) ? trim($_POST['restypename']) : '';
        $sql = "SELECT ResourceType_Id, accountsub_id, Name, sortorder, Project_Type FROM resourcetype WHERE Status='0'";
        if ($search !== '') {
            $sql .= " AND Name LIKE '%" . addslashes($search) . "%'";
        }
        $sql .= " ORDER BY sortorder ASC, ResourceType_Id ASC, Name ASC";
        $dataProvider = $connection->createCommand($sql)->queryAll();

        if (empty($dataProvider)) {
            $restype = '<p style="color:#aaa;font-size:13px;text-align:center;padding:30px 0;">No resource types found.</p>';
            return json_encode(['error' => 'No', 'restype' => $restype]);
        }

        $restype = '<table style="width:100%;border-collapse:collapse;font-size:13px;">'
                 . '<thead><tr>'
                 . '<th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;text-align:center;width:36px;">#</th>'
                 . '<th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;">Resource Type</th>'
                 . '<th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;text-align:center;width:90px;"></th>'
                 . '</tr></thead><tbody>';

        foreach ($dataProvider as $i => $data) {
            $safeName = htmlspecialchars($data['Name'], ENT_QUOTES);

            $accntgrp = '';
            $accSub = AccountsSub::find()->where(['id' => $data['accountsub_id']])->one();
            if ($accSub) {
                $accntgrp = $accSub->master_id;
            }

            $restype .= '<tr class="restypesort resourcetype' . $data['ResourceType_Id'] . '" '
                      .     'data-id="' . $data['ResourceType_Id'] . '" '
                      .     'id="resourcetyperow' . $data['ResourceType_Id'] . '">'
                      . '<td style="padding:6px 10px;border:1px solid #ddd;text-align:center;color:#999;">' . ($i + 1) . '</td>'
                      . '<td style="padding:6px 10px;border:1px solid #ddd;">' . $safeName
                      .     '<input type="hidden" id="resordtypenames' . $data['ResourceType_Id'] . '" value="' . $safeName . '">'
                      .     '<input type="hidden" id="resordtypeidd' . $data['ResourceType_Id'] . '" value="' . $data['Project_Type'] . '">'
                      .     '<input type="hidden" id="resaccntgroup' . $data['ResourceType_Id'] . '" value="' . $accntgrp . '">'
                      . '</td>'
                      . '<td style="padding:4px 8px;border:1px solid #ddd;text-align:center;white-space:nowrap;">'
                      .     '<button type="button" class="btn btn-default btn-xs" '
                      .         'data-id="' . (int)$data['ResourceType_Id'] . '" '
                      .         'data-name="' . $safeName . '" '
                      .         'onclick="openEditResType(this)" '
                      .         'style="border-radius:50%;width:28px;height:28px;padding:0;margin-right:4px;">'
                      .         '<span class="icon-pencil"></span></button>'
                      .     '<button type="button" class="deletresourcetypebutton btn btn-danger btn-xs" '
                      .         'value="' . $data['ResourceType_Id'] . '" '
                      .         'id="deletresourcetypebutton' . $data['ResourceType_Id'] . '" '
                      .         'style="border-radius:50%;width:28px;height:28px;padding:0;background-color:#d9534f!important;border-color:#d43f3a!important;color:#fff!important;">'
                      .         '<span class="icon-trash1"></span></button>'
                      . '</td></tr>';
        }

        $restype .= '</tbody></table>';
        return json_encode(['error' => 'No', 'restype' => $restype]);
    }
     public function actionUpdatesort()
    {
        if(isset($_POST['datavalue']))
        {
            foreach($_POST['datavalue'] AS $data)
            {
                $work=Resourcetype::findOne($data['rowid']);
                $work->sortorder=$data['rowindex'] + 1;
                $work->save(false);
            }
        }
        $arr = array('error'=>'No');
        echo json_encode($arr);
    }
    /*public function actionUpdatesort()
    {
        if(isset($_POST['datavalue']))
        {
            foreach($_POST['datavalue'] AS $data)
            {
                $work=Resourcetype::model()->findByPk($data['rowid']);  
                $work->sortorder=$data['rowindex'] + 1;
                $work->save(false);
            }
        }
        $arr = array('error'=>'No');
        echo json_encode($arr);
    }*/
    public function actionUpdate()
    {
        if(isset($_POST['restypeid']))
        {
            $model=$this->findModel($_POST['restypeid']);
            $model->Name=$_POST['name'];
            if(isset($_POST['groupname'])):
            $model->Resource_group_Id=$_POST['groupname'];
            endif;
            $model->Updated_On=date('y-m-d h:i:s');
            if(isset($_POST['sourceunit'])):
            $model->Source_unit=$_POST['sourceunit'];
            endif;
            $model->Project_Type = isset($_POST['projtype']) ? $_POST['projtype'] : $model->Project_Type;

            if($model->save(false)):
                $subgeoups=AccountsSub::findOne($model->accountsub_id);
                $subgeoups->master_id = $_POST['accntgrp'];
                $subgeoups->save(false);
                //$grpnames=ResourceGroup::findOne($model->Resource_group_Id); 
                //$groupname=$grpnames['Resource_group_Name'];
                $groupname='';
                $arr = array('Id' => $_POST['restypeid'], 'Name' => $_POST['name'], 'Group' => $groupname,'error'=>'No');
                return json_encode($arr);
            else:
                $arr = array('Id' => $_POST['restypeid'],'error'=>'Yes','errortext'=>'Can not update now.');
                return json_encode($arr);
            endif;
        }

    }


    public function actionDeleteitem()
    {
        if(isset($_POST['restypeid'])):
            $model = $this->findModel($_POST['restypeid']);
            if($model):
                $model->Status = 1;
                //$model->save(false);

                if($model->save(false)):

                    $accntsubs = AccountsSub::find()->where(['Status'=>0])->andWhere(['ResourceType_Id'=>$_POST['restypeid']])->all();

                    foreach($accntsubs as $accntsub):

                        $accntsub->Status = 1;
                        $accntsub->save(false);

                    endforeach;

                endif;

                $arr = array('Id' => $_POST['restypeid'],'error'=>'No');
                return json_encode($arr);
            else:
                $arr = array('Id' => $_POST['restypeid'],'error'=>'Yes','errortext'=>'Can not Delete Resource Type with ID '.$_POST['resid'].'.');
                return json_encode($arr);
            endif;
        endif;
    }

    public function actionCheckname()
    {
        if(isset($_POST['name'])):
            //$connection = CActiveRecord::getDbConnection();
            $connection = \Yii::$app->db; 
            $sql="SELECT COUNT(*) AS total FROM resourcetype WHERE Name LIKE '%".$_POST['name']."%'";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $data=$dataReader->read();
            if($data['total']>0):
                echo 'Yes';
            else:
                echo 'No';
            endif;

        endif;
    }
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
/*	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}*/

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */


	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
/*	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}*/

	/**
	 * Lists all models.
	 */


	/**
	 * Manages all models.
	 */
/*	public function actionAdmin()
	{
		$model=new Resourcetype('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Resourcetype']))
			$model->attributes=$_GET['Resourcetype'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}*/

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Resourcetype the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Resourcetype::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}



   public function findModel($id)
     {
        if (($model = Resourcetype::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }




	/**
	 * Performs the AJAX validation.
	 * @param Resourcetype $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='resourcetype-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    /* Returns <option> HTML for all active resource types — used to refresh
       dropdowns after a new type is added without a page reload */
    public function actionGetoptions()
    {
        $rows = \Yii::$app->db->createCommand(
            "SELECT ResourceType_Id, Name FROM resourcetype WHERE Status='0' ORDER BY sortorder ASC, Name ASC"
        )->queryAll();
        $opts = '';
        foreach ($rows as $r) {
            $name = htmlspecialchars($r['Name'], ENT_QUOTES);
            $opts .= '<option value="' . $r['ResourceType_Id'] . '">' . $name . '</option>';
        }
        return json_encode(['error' => 'No', 'options' => $opts]);
    }
}
