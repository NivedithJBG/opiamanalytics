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
            $model->Project_Type=$_POST['projecttype'];
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
        $sql="SELECT ResourceType_Id,accountsub_id,Name,sortorder,Resource_group_Id,Source_unit,Project_Type FROM resourcetype WHERE Status='0' ";
        if($_POST['restypename']!='')
            $sql.="AND Name LIKE '%".$_POST['restypename']."%'";
        $sql.=" ORDER BY sortorder ASC,ResourceType_Id ASC, Name ASC";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        //$list=ResourceGroup::find()->all();
        $datarows='';
        $restype='';
        $projtypevaluesimg='';
        if(count($dataProvider)>0):

            $subgeoups=AccountsSub::find()->Where(['master_id'=>1])->orderBy(['sortorder'=> 'SORT_ASC'])->orderBy(['id'=> 'SORT_DESC'])->all();

            foreach($dataProvider AS $key=>$data):
                $typesval='';
                /*foreach ($list AS $type):
                    if ($data['Resource_group_Id'] == $type->Resource_group_Id):
                        $seletedtype = 'Selected="selected"';
                    else:
                        $seletedtype = '';
                    endif;
                    $typesval .= "<option value='" . $type->Resource_group_Id . "' " . $seletedtype . ">" . $type->Resource_group_Name . "</option>";
                endforeach;*/
				$subvalues='<option value="">Select Account Sub Group</option>';
		        $selectedacc_sub ='';
				foreach($subgeoups AS $key1=>$geoup):
                    $acc_subgrp=AccountsSub::findOne($geoup->id); 
					if($data['accountsub_id']==$acc_subgrp->id):    
		                $selectedacc_sub = $acc_subgrp->id;
						$seletedsub = 'Selected="selected"';
					else:
						$seletedsub = '';
					endif;
					$subvalues .= "<option value='" . $geoup->id . "' " . $seletedsub . ">" . $geoup->name . "</option>";
				endforeach;

                if($data['Source_unit']==1):
                    $sourceunit='Purchase Bills';
                elseif($data['Source_unit']==2):
                    $sourceunit='Pay Roll';
                elseif($data['Source_unit']==3):
                    $sourceunit='Muster Roll';
                elseif($data['Source_unit']==4):
                    $sourceunit='Work Bill';
                elseif($data['Source_unit']==5):
                    $sourceunit='Lease Order';
                elseif($data['Source_unit']==6):
                    $sourceunit='Log Book and Books of Accounts';
                elseif($data['Source_unit']==7):
                    $sourceunit='Books of Accounts';
                elseif($data['Source_unit']==8):
                    $sourceunit='Book of Accounts - OT';
                endif;
                if($data['Project_Type']==1):
                    $projecttype='Purchase Order';
                    $projtypevaluesimg='<span class="ordertype-icon">
                                    <img src='.Yii::$app->request->getBaseUrl(true).'/images/carlabel-icon-white.png>
                                     </span>';
                    $ot='<h6>Purchase Order</h6>';                 

                elseif($data['Project_Type']==2):
                    $projecttype='Work Order';
                     $projtypevaluesimg='<span class="ordertype-icon">
                                    <img src='.Yii::$app->request->getBaseUrl(true).'/images/workorderl-icon-white.png>
                                     </span>';
                     $ot='<h6>Work Order</h6>';

                elseif($data['Project_Type']==3):
                    $projecttype='Muster Roll';
                     $projtypevaluesimg='<span class="ordertype-icon">
                                    <img src='.Yii::$app->request->getBaseUrl(true).'/images/musterrolllabel-icon-white.png>
                                     </span>';
                    $ot='<h6>Muster Roll</h6>';

                elseif($data['Project_Type']==4):
                    $projecttype='Lease Order';
                     $projtypevaluesimg='<span class="ordertype-icon">
                                    <img src='.Yii::$app->request->getBaseUrl(true).'/images/leaselabel-icon-white.png>
                                     </span>';
                     $ot='<h6>Lease Order</h6>';           
                else:
                    $projecttype='';
                endif;
                $sourcevalues="<option value='1' " .($data['Source_unit']=='1'?'selected':'') . ">Purchase Bills</option>
                <option value='2' " . ($data['Source_unit']=='2'?'selected':'') . ">Pay Roll</option>
                <option value='3' " . ($data['Source_unit']=='3'?'selected':'') . ">Muster Roll</option>
                <option value='4' " . ($data['Source_unit']=='4'?'selected':'') . ">Work Bill</option>
                <option value='5' " . ($data['Source_unit']=='5'?'selected':'') . ">Lease Order</option>
                <option value='6' " . ($data['Source_unit']=='6'?'selected':'') . ">Log Book and Books of Accounts</option>
                <option value='7' " . ($data['Source_unit']=='7'?'selected':'') . ">Books of Accounts</option>
                <option value='8' " . ($data['Source_unit']=='8'?'selected':'') . ">Book of Accounts - OT</option>
                ";
                $projtypevalues="<option value='1' " .($data['Project_Type']=='1'?'selected':'') . ">Purchase Order</option>
                <option value='2' " . ($data['Project_Type']=='2'?'selected':'') . ">Work Order</option>
                <option value='3' " . ($data['Project_Type']=='3'?'selected':'') . ">Muster Roll</option>
                <option value='4' " . ($data['Project_Type']=='4'?'selected':'') . ">Lease Order</option>
                ";

                if($data['Name']=='Products'):
                    $link="<button type='button' title='Resource Groups' class='btn btn-primary childresourcegroups' value='".$data['ResourceType_Id']."' id='childresourcegroups".$data['ResourceType_Id']."'>
                            <span class='glyphicon glyphicon-list'></span></button>";
                else:
                    $link="<button type='button' title='Resource Groups' class='btn btn-primary childresourcegroups' value='".$data['ResourceType_Id']."' id='childresourcegroups".$data['ResourceType_Id']."'>
                            <span class='glyphicon glyphicon-list'></span></button>";
                endif;
                

                if($data['Name']=='Products'):
                    
                    $links="<button type='' title='Resources' class='childresourcegroups' value='".$data['ResourceType_Id']."' id='childresourcegroups".$data['ResourceType_Id']."'>
                     <a href='#' class='icon-list'></a>";
                else:
                  
                    $links="<button type='' title='Resources' class='childresourcegroups' value='".$data['ResourceType_Id']."' id='childresourcegroups".$data['ResourceType_Id']."'>
                        <a href='#' class='icon-list'></a>";
                endif;
 
                $ressubgrps=AccountsSub::findOne($selectedacc_sub); 
                //$ressubgrp=$ressubgrps['name'];          

                $current_accntgrp=AccountsSub::find()->Where(['id'=>$data['accountsub_id']])->one();
                if($current_accntgrp){
                    $accnt_grp = $current_accntgrp->master_id;
                } 
                else{
                    $accnt_grp = '';
                }                
                         
                $restype.=' 
                <div class="resource-type-cntnt-wrpr row restypesort resourcetype'.$data['ResourceType_Id'].'" data-id="'.$data['ResourceType_Id'].'" id="resourcetyperow'.$data['ResourceType_Id'].'" ">
                    <div class="col-md-10 col-sm-10 resourcess">
                        <div class="row">
                            <div class="col-md-8 col-sm-1">
                                <div class="row">
                                    <div class="col-md-3">
                            				<span>'.($key+1).'</span>	
                            		</div>
                                    <div class="col-md-9 col-sm-3 type">
                           
                                       <span>'.$data["Name"].'</span>
                                       <input type="hidden" id="resordtypenames'.$data['ResourceType_Id'].'" value="'.$data['Name'].'">
                                    </div>
                        
                                </div>
                            </div>
                            <div class="col-md-3 ordertyps">
                                '.$projtypevaluesimg.$ot.'
                        
                                <input type="hidden" id="resordtypeidd'.$data['ResourceType_Id'].'" value="'.$data['Project_Type'].'">
                                <input type="hidden" id="resaccntgroup'.$data['ResourceType_Id'].'" value="'.$accnt_grp.'">
                            </div>
                       
                            <div class="col-md-1 col-sm-4"></div>
                        </div>
                    </div>
                    <div class="col-md-2  col-sm-2 icon-groups">
                        <button type="button" class="editresourtypebutton" value="'.$data['ResourceType_Id'].'" id="editresourtypebutton'.$data['ResourceType_Id'].'" title="Edit Resource Type">
                        <a href="#" class="icon-pencil"></a></button>
                    
                        '.$links.'

                        <button type=""  title="Delete Resource Type" class="deletresourcetypebutton" value="'.$data['ResourceType_Id'].'" id="deletresourcetypebutton'.$data['ResourceType_Id'].'"><a href="#" class="icon-trash1"></a>
                        
                    </div>
                </div>';
                           
            endforeach;

        else:
            $restype='<div><div colspan="9" style="text-align: center">No Resource Types Found</div></div>';
        endif;

        $arr = array('result' => $restype,'restype'=>$restype,'error'=>'No');
        return json_encode($arr);

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
            $model->Project_Type=$_POST['projtype'];

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
}
