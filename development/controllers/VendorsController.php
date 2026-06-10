<?php

namespace app\controllers;  

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter; 
use yii\web\CHttpException;
use app\models\Vendortypes;  
use app\models\VendorGroup;  
use app\models\AccountsItem;  
use app\models\Vendors; 
use app\models\Resources; 

class VendorsController extends Controller
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
				'actions'=>array('index','view','create','Search','update','deletevendor','Checkname'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('CreateVendortype','SearchVendortypes','Vendortypeupdate','DeleteVendortype','SearchVendorgroups','CreateVendorgroup','Vendorgroupupdate','Vendorgroups','DeleteVendorgroup'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    public function actionIndex()
    {
        $connection = CActiveRecord::getDbConnection();
        $sql="SELECT Name FROM vendors  WHERE status='0' ORDER BY Vendor_Id ASC, Name ASC";
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

    public function actionDeletevendor()
    {
        if(isset($_POST['vendor'])):
            $model = $this->findModel($_POST['vendor']);
            $model->Status = 1;
            $model->save(false);

            $resources = Resources::find()->where(['Vendor_Id' => $_POST['vendor']])->all();
            foreach($resources as $resource):
                $resource->pricing_status = 1;
                $resource->save(false);
            endforeach;
            $arr = array('Id' => $_POST['vendor'],'error'=>'No');
            return json_encode($arr);
        else:
            $arr = array('Id' => $_POST['vendor'],'error'=>'Yes','errortext'=>'Can not Delete Resource Type with ID '.$_POST['resid'].'.');
            return json_encode($arr);
        endif;
    }

    public function actionCreate()
    {
        $model=new Vendors;
        if(isset($_POST['vendorname'])):
            $model->Name=$_POST['vendorname'];
            $model->Brand=$_POST['brandname'];
            $model->Address=$_POST['vendoraddress'];
            $model->City=$_POST['vendorcity'];
            $model->Phone=$_POST['vendorphone'];
            $model->Email=$_POST['vendoremail'];
            $model->vendor_type=$_POST['choosevvendortype'];
            $model->Added_On=date('y-m-d h:i:s');
            $model->vendor_groupid=$_POST['choosevvendorgroup'];
           // $model->Added_By=Yii::app()->user->id;
            if($model->save()):
                //$this->redirect(array('/AccountsItem/create?id='.$model->Vendor_Id.'&vendor='.$model->Name.''));
                $arr = array('Id' => $model->Vendor_Id, 'Name' => $model->Name,'error'=>'No');
                echo json_encode($arr);
            else:
                $arr = array('id' => '', 'Name' => '','error'=>'Yes','errortext'=>'Cannot add now. Please try again later');
                echo json_encode($arr);
            endif;

        endif;

    }

    public function actionSearch()
    {
        $connection = \Yii::$app->db; 
        $sql="SELECT Vendor_Id,Name,Brand,Address,City,Phone,Email,vendor_type,account_id,vendor_groupid FROM vendors WHERE status='0' ";
        if($_POST['vendorname']!='')
            $sql.=" AND Name LIKE '%".$_POST['vendorname']."%'";
        $sql.=" ORDER BY Vendor_Id ASC, Name ASC";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $datarows='';
        $vendor='';
        $vendordata='';
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):
                $account=AccountsItem::find($data['account_id'])->one()->name; 

                $email=str_replace(",","<br>",$data['Email']);
                $st=array(",","/");
                $phone=str_replace($st,"<br>",$data['Phone']);
                $brand=$data['Brand'];
                                                        
                

                    $vendor.="<tr id='vendorrow".$data['Vendor_Id']."' class='vendorrow".$data['Vendor_Id']."'>
                                <td>".($key+1)."</td>
                                <td>
                                    <span>".$data['Name']."</span>
                                    <input type='hidden' id='vendorname".$data['Vendor_Id']."' value='".$data['Name']."'>
                                   <span class='error'></span>
                                </td>

                                <td>
                                    <span class='emaildata'>".$email."</span>
                                      <input type='hidden' id='vendoremail".$data['Vendor_Id']."' value='".$email."'>   
                                  
                                </td>

                                <td>
                                    <span>".$phone."</span>
                                     <input type='hidden' id='vendorphone".$data['Vendor_Id']."' value='".$phone."'>


                                </td>
                                <td>
                                    <span>".$data['City']."</span>
                                     <input type='hidden' id='vendorcity".$data['Vendor_Id']."'  value='".$data['City']."'>  
                                  
                                </td>
                                 <td>
                                    <span>".$data['Address']."</span>
                                    <input type='hidden' id='vendoraddress".$data['Vendor_Id']."' value='".$data['Address']."'>
                                   
                                </td>
                              
                                <td class='icon-groups' style='display:flex;'>  <button type='button' class='editvendorbutton' value='".$data['Vendor_Id']."' id='editvendorbutton".$data['Vendor_Id']."' title='Edit Vendor'><a href='#' class='icon-pencil'></a></button> &nbsp;

                                   <button type='button' class='deletevendorbutton' value='".$data['Vendor_Id']."' id='deletevendorbutton".$data['Vendor_Id']."' title='Delete Vendor'><a href='#'' class='icon-trash1'></a></button> 
                                </td>

                             
                            </tr>";

            endforeach;
            
        else:
            $vendor='<tr id="nodata"><td colspan="8" style="text-align: center;">No Vendors Found</td></tr>';
        endif;
        $arr = array('result' => $vendor,'vendorid'=>$vendordata,'error'=>'No');
        return json_encode($arr);
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
    public function actionCheckname()
    {
        if(isset($_POST['name'])):
            $connection = CActiveRecord::getDbConnection();
            $sql="SELECT COUNT(*) AS total FROM vendors WHERE Name='".$_POST['name']."'";
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
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
    public function actionUpdate()
    {
        if(isset($_POST['vendorid']))
        {
            $model=$this->findModel($_POST['vendorid']);
            if(isset($_POST['name'])):
                $model->Name=$_POST['name'];
            endif;
            if(isset($_POST['address'])):
                $model->Address=$_POST['address'];
            endif;
            if(isset($_POST['city'])):
                $model->City=$_POST['city'];
            endif;
            if(isset($_POST['phone'])):
                $model->Phone=$_POST['phone'];
            endif;
            if(isset($_POST['email'])):
                $model->Email=$_POST['email'];
            endif;
            $model->Updated_On=date('y-m-d h:i:s');

            if($model->save(false)):
                $arr = array('Id' => $_POST['vendorid'], 'error' => 'No');
                return json_encode($arr); 
            endif;
        }

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
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Vendors('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Vendors']))
			$model->attributes=$_GET['Vendors'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Vendors the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Vendors::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

    public function findModel($id)
    {
         $model=Vendors::findOne($id);

        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

	/**
	 * Performs the AJAX validation.
	 * @param Vendors $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='vendors-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}


}
