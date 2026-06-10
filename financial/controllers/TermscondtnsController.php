<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl; 
use app\models\TermsCondtns;
use app\models\TermsCondtnsContent;
use app\models\OrderTermscontent;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TermscondtnsController implements the CRUD actions for TermsCondtns model.
 */
class TermscondtnsController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Lists all TermsCondtns models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TermsCondtns::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TermsCondtns model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TermsCondtns model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        //echo 'hai'; exit;
        $model = new TermsCondtns();

        $model->title = $_POST['termstitle'].' terms and conditions';
        $model->content = '';
        $model->status = 0;
        if($model->save(false)):

          for($i=0;$i<count($_POST['termscontent']);$i++)
          {
            $model1 = new TermsCondtnsContent();
            $model1->termsid = $model->id;
            $model1->content = $_POST['termscontent'][$i];
            $model1->status = 0;
            if($_POST['termscontent'][$i]!=''){
              $model1->save(false);
            }
          }

        endif;

        $arr=array('error'=>'No');
        return json_encode($arr);

        /*if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);*/
    }

    /**
     * Updates an existing TermsCondtns model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        /*$model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);*/

        //echo 'hai'; exit;

        $model = TermsCondtns::findOne($_POST['termid']); 

        if(isset($_POST['edittermstitle'])):
          $model->title = $_POST['edittermstitle'];
        endif;
        if($model->save(false)):

          $allcontents=TermsCondtnsContent::find()->Where(['termsid'=> $model->id])->all();

          foreach($allcontents as $allcontent):

            $allcontent->delete();

          endforeach;

          for($i=0;$i<count($_POST['termscontent']);$i++)
          {
            $model1 = new TermsCondtnsContent();
            $model1->termsid = $model->id;
            $model1->content = $_POST['termscontent'][$i];
            $model1->status = 0;
            if($_POST['termscontent'][$i]!=''){
              $model1->save(false);
            }
          }

        endif;       

        $arr=array('error'=>'No');
        return json_encode($arr);
    }

     public function actionUpdateapprove()
    {
       

        $model = TermsCondtns::findOne($_POST['termid']); 

         if(isset($_POST['title'])):
        $model->title = $_POST['title'];
      endif;
        $model->content = $_POST['content'];
        $model->status = 0;
        $model->save();

        $arr=array('error'=>'No');
        return json_encode($arr);
    }


    /**
     * Deletes an existing TermsCondtns model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete()
    {
        //$this->findModel($id)->delete();

        $model= TermsCondtns::findOne($_POST['termsid']); 
        $model->status = 1;
        $model->save();

        $arr=array('error'=>'No');
        return json_encode($arr);

        //return $this->redirect(['index']);
    }

    /**
     * Finds the TermsCondtns model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TermsCondtns the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TermsCondtns::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAddterms()
    {
        $datarows='<form id="addtermsform">  

                        <div class="col-md-12">
                          <div class="form-title">Add Terms & Conditions</div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-2"></div>

                            <div class="col-md-8">                             
                              <div class="add-new-form-wrpr">                            
                                <div class="form-group">
                                  <label>Title</label>
                                   <input class="form-control" id="termstitle" name="termstitle" placeholder="Enter title" type="text" />
                                   <span class="error" style="display: none;"></span>
                                </div>
                              </div>                             
                            </div>

                            <div class="col-md-2"></div>

                        </div>

                        <div class="termscontentlist">

                          <div class="col-md-12"><div class="row">
                              <div class="col-md-1"></div>
                              <div class="col-md-1" style="text-align: right;height: 67px;padding-top: 23px;">1</div>

                              <div class="col-md-8">                              
                                <div class="add-new-form-wrpr">                               
                                  <div class="form-group">
                                    <label>Terms and Conditions</label>
                                     <input type="text" class="form-control termscontent" placeholder="" name="termscontent[]">
                                     <span class="ck_validation error" style="display: none;">Enter Content</span>
                                  </div>
                                </div>                             
                              </div>

                              <div class="col-md-1 icon-groups">
                                  <a class="btn btn-primary icon-add termsaddmore" name="termsaddmore" id="termsaddmore" title="Add more" href="javascript:void(0)"></a>
                              </div>

                              <div class="col-md-1"></div>

                          </div></div>

                        </div>

                        <div class="col-md-12">
                            <div class="col-md-6"></div>

                            <div class="col-md-4">                              
                              <label>&nbsp;</label>
                              <button type="button" class="btn btn-danger cancel" id="cancelterms"><span class="icon-close"></span> Cancel</button>
                              <button type="button" class="btn btn-primary" id="saveterms"><span class="icon-check"></span> Add</button>                             
                            </div>

                            <div class="col-md-2"></div>

                        </div>

                      </form>';

        $arr=array('error'=>'No','result'=>$datarows);
        return json_encode($arr);
    }

    public function actionEditterms()
    {
        $model = TermsCondtns::findOne($_POST['termid']);
        $datarows='<form id="edittermsform">  

                        <div class="col-md-12">
                          <div class="form-title">Add Terms & Conditions</div>
                        </div>
                        <div class="col-md-12"><div class="row">
                            <div class="col-md-2"></div>

                            <div class="col-md-8">                             
                              <div class="add-new-form-wrpr">                            
                                <div class="form-group">
                                  <label>Title</label>
                                   <input type="hidden" id="termid" name="termid" value="'.$_POST['termid'].'">
                                   <input class="form-control" id="edittermstitle" name="edittermstitle" placeholder="Enter title" type="text" value="'.$model->title.'"/>
                                   <span class="error" style="display: none;"></span>
                                </div>
                              </div>                             
                            </div>

                            <div class="col-md-2"></div>

                        </div></div>';

                        $allcontents=TermsCondtnsContent::find()->Where(['termsid'=> $model->id])->orderBy(['id'=> SORT_ASC])->all();

                        $datarows.='
                                <div class="edittermscontentlist">';

                        $count = 1;

                        foreach($allcontents as $key=> $allcontent):

                          if($key==0){

                            $datarows.='
                                  <div class="col-md-12">
                                  <div class="row"><div class="col-md-1"></div>
                                      <div class="col-md-1" style="text-align: right;padding-top: 12px;"><label></label><span>'.($key+1).'</span></div>

                                      <div class="col-md-8">                              
                                        <div class="add-new-form-wrpr">                               
                                          <div class="form-group">
                                            <label>Terms and Conditions</label>
                                             <input type="text" class="form-control termscontent" placeholder="" name="termscontent[]" value="'.$allcontent->content.'">
                                             <span class="ck_validation error" style="display: none;">Enter Content</span>
                                          </div>
                                        </div>                             
                                      </div>

                                      <div class="col-md-1 icon-groups">
                                          <a class="btn btn-primary icon-add etermsaddmore" name="etermsaddmore" id="etermsaddmore" title="Add more" href="javascript:void(0)"></a>
                                      </div>

                                      </div>

                                  </div>';

                          }
                          else{

                            $datarows.='
                                <div class="col-md-12 termsrows'.$count.'"><div class="row">
                                <div class="col-md-1"></div>
                                    <div class="col-md-1" style="text-align: right;padding-top: 12px;"><span>'.($key+1).'</span></div>

                                    <div class="col-md-8">                              
                                      <div class="add-new-form-wrpr">                               
                                        <div class="form-group">
                                          
                                           <input type="text" class="form-control termscontent" placeholder="" value="'.$allcontent->content.'" name="termscontent[]">
                                           <span class="ck_validation error" style="display: none;">Enter Content</span>
                                        </div>
                                      </div>                             
                                    </div>

                                    <div class="col-md-1 icon-groups">
                                        <a class="btn btn-primary icon-remove emremoveterms" name="emremoveterms" data-id="'.$count.'" id="emremoveterms" title="Remove" href="javascript:void(0)"></a>
                                    </div>

                                    </div>

                                </div>';

                                $count++;

                          }


                      endforeach;

                      $datarows.='
                                </div>';

                        $datarows.='

                        <div class="col-md-12">
                            <div class="col-md-6"></div>

                            <div class="col-md-4">                              
                              <label>&nbsp;</label>
                              <button type="button" class="btn btn-danger cancel" id="editcancelterms"><span class="icon-close"></span> Cancel</button>
                              <button type="button" class="btn btn-primary" id="editsaveterms"><span class="icon-check"></span> Edit</button>                             
                            </div>

                            <div class="col-md-2"></div>

                        </div>

                      </form>';

        $arr=array('error'=>'No','result'=>$datarows);
        return json_encode($arr);
    }

    public function actionEdittermss()
    {
        $model = TermsCondtns::findOne($_POST['termid']);

        $allcontents=TermsCondtnsContent::find()->Where(['termsid'=> $model->id])->orderBy(['id' => SORT_ASC])->all();
        $datarows ='
                      <div class="modal-dialog">

                      <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" style="text-align:center;">Terms and Conditions</h5>
                           
                          </div>';

        $datarows .='<form id="leaseterms" method="POST"><div class="modal-body" style="display: grid;">
                <div class="termscontentlist">';

        $count = 1;

        foreach($allcontents as $key=> $allcontent):

          if($key==0){

            $datarows.='
                  <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-1"><span>'.($key+1).'</span></div>
                      <div class="col-md-8">                              
                        <div class="add-new-form-wrpr">                               
                          <div class="form-group">
                            
                             <input type="text" class="form-control termscontent" placeholder="" name="termscontent[]" value="'.$allcontent->content.'">
                             <span class="ck_validation error" style="display: none;">Enter Content</span>
                          </div>
                        </div>                             
                      </div>

                      <div class="col-md-1 icon-groups" style="bottom: 13px;">
                          <a class="btn btn-primary icon-add termsaddmoreorder" name="termsaddmore" id="termsaddmore" title="Add more" href="javascript:void(0)"></a>
                      </div>

                      
                        </div>
                  </div>';

          }
          else{

            $datarows.='
                <div class="col-md-12 termsrows'.$count.'">
                <div class="row">
                    <div class="col-md-1"><span>'.($key+1).'</span></div>
                    <div class="col-md-8">                              
                      <div class="add-new-form-wrpr">                               
                        <div class="form-group">
                         
                           <input type="text" class="form-control termscontent" placeholder="" value="'.$allcontent->content.'" name="termscontent[]">
                           <span class="ck_validation error" style="display: none;">Enter Content</span>
                        </div>
                      </div>                             
                    </div>

                    <div class="col-md-1 icon-groups" style="min-height:unset;">
                        <a class="btn btn-primary icon-remove removeterms" name="removeterms" data-id="'.$count.'" id="removeterms" title="Remove" href="javascript:void(0)"></a>
                    </div>

                   </div>

                </div>';

                $count++;

          }


      endforeach;
      $datarows.='</form></div></div>
                    <div class="modal-footer">
                    <button type="button" data-dismiss="modal"  class="btn btn-default termssbmt">Ok</button>
                    </div>
                
                </div>
            ';

      $datarows.='
                </div>';

        $arr=array('error'=>'No','result'=>$datarows);
        return json_encode($arr);
    }
    public function actionSavetermscntenttemporary()
    {
      $connection =  \Yii::$app->db;

      $sql1='DROP TABLE IF EXISTS temp_tercontents';
      $command=$connection->createCommand($sql1);
      $dataReader=$command->query();

      $sql='CREATE TABLE temp_tercontents (content text)';
      $command=$connection->createCommand($sql);
      $dataReader=$command->query();

      if(isset($_POST['termscontent'])){
        for($i=0;$i<count($_POST['termscontent']);$i++)
          {

            $sql="INSERT INTO temp_tercontents(content) values ('".$_POST['termscontent'][$i]."')";
                          
            $command=Yii::$app->db->createCommand($sql);
            $dataReader=$command->query();
          }
      }


    }

    public function actionEdittermpurchase()
    {
        $model = TermsCondtns::findOne($_POST['termid']);

        $allcontents=TermsCondtnsContent::find()->Where(['termsid'=> $model->id])->orderBy(['id' => SORT_ASC])->all();

        $datarows='
                      <div class="modal-dialog">

                      <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" style="text-align:center;">Terms and Conditions</h5>
                           
                          </div>';

        $datarows.='<form id="purchasesss" method="POST"><div class="modal-body" style="display: grid;">
                <div class="termscontentlist">
                <div class="col-md-12">                             
                  <div class="add-new-form-wrpr">                            
                    <div class="form-group">
                      <label></label>
                       <input type="hidden" id="termid" name="termid" value="'.$_POST['termid'].'">
                    </div>
                  </div>                             
                </div>';

        $count = 1;

        foreach($allcontents as $key=> $allcontent):

          if($key==0){

            $datarows.='
                  <div class="col-md-12">
                  <div class="row">

                      
                      <div class="col-md-1"><span>'.($key+1).'.</span></div>
                      <div class="col-md-8">                              
                        <div class="add-new-form-wrpr">                               
                          <div class="form-group">
                            
                             <input type="text" class="form-control termscontent" placeholder="" name="termscontent[]" value="'.$allcontent->content.'">
                             <span class="ck_validation error" style="display: none;">Enter Content</span>
                          </div>
                        </div>                             
                      </div>
                      

                      <div class="col-md-1 icon-groups" style="min-height:unset;">
                          <a class="btn btn-primary icon-add termsaddmorepurchase" name="termsaddmore" id="termsaddmore" title="Add more" href="javascript:void(0)"></a>
                      </div></div>

                  </div>';

          }
          else{

            $datarows.='
                <div class="col-md-12 termsrows'.$count.'">

                   <div class="row">
                    <div class="col-md-1"><span>'.($key+1).'.</span></div>
                    <div class="col-md-8">                              
                      <div class="add-new-form-wrpr">                               
                        <div class="form-group">
                          
                           <input type="text" class="form-control termscontent" placeholder="" value="'.$allcontent->content.'" name="termscontent[]">
                           <span class="ck_validation error" style="display: none;">Enter Content</span>
                        </div>
                      </div>                             
                    </div>

                    <div class="col-md-1 icon-groups" style="min-height:unset;">
                        <a class="btn btn-primary icon-remove removeterms" name="removeterms" data-id="'.$count.'" id="removeterms" title="Remove" href="javascript:void(0)"></a>
                    </div></div>

                </div>';

                $count++;

          }


      endforeach;

       $datarows.='</form></div></div>
                    <div class="modal-footer">
                    <button type="button" data-dismiss="modal"  class="btn btn-default ptermssbmt">Ok</button>
                    </div>
                
                </div>
            ';

      $datarows.='
                </div>';

        $arr=array('error'=>'No','result'=>$datarows);
        return json_encode($arr);
    }

    public function actionEdittermsapprove()
    {
        $model = TermsCondtns::findOne($_POST['termid']);
        /*$datarows='<form id="edittermsform">  
                            <div class="col-md-12">                             
                              <div class="add-new-form-wrpr">                            
                                <div class="form-group">
                                  <label style="font-size:13px;">Edit Terms and Conditions</label>
                                   <input type="hidden" id="termid" name="termid" value="'.$_POST['termid'].'">
                                </div>
                              </div>                             
                            </div>

                            <div class="col-md-12">                              
                              <div class="add-new-form-wrpr">                               
                                <div class="form-group">
                                  <label style="font-size:13px;">Content</label>
                                   <textarea id="edittermsscontent" name="edittermsscontent">'.$model->content.'</textarea>
                                   <script>
                                          CKEDITOR.replace( "edittermsscontent" );
                                  </script>
                                   <span class="ck_validation error" style="display: none;">Enter Content</span>
                                </div>
                              </div>                             
                            </div>

                        <div class="col-md-12">
                            <div class="col-md-9"></div>

                            <div class="col-md-3">                              
                              <label>&nbsp;</label>
                              <button type="button" class="btn btn-danger cancel" id="editcanceltermss"><span class="icon-close"></span> Cancel</button>
                              <button type="button" class="btn btn-primary" id="editsavetermss"><span class="icon-check"></span> Update</button>                             
                            </div>

                        </div>

                      </form>';*/

          $allcontents=OrderTermscontent::find()->Where(['termsid'=> $model->id])->andWhere(['orderid'=> $_POST['orderid']])->orderby(['id' => SORT_ASC])->all();

          if($allcontents){
              $datarows='<div class="modal-dialog">

                            <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" style="text-align:center;">Terms and Conditions</h5>
                                 
                                </div>';

              $datarows .='<form id="leaseterms" method="POST"><div class="modal-body" style="display: grid;">
                      <div class="termscontentlist">';

              $count = 1;

              foreach($allcontents as $key=> $allcontent):

                if($key==0){

                  $datarows.='
                        <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-1"><span>'.($key+1).'</span></div>
                            <div class="col-md-8">                              
                              <div class="add-new-form-wrpr">                               
                                <div class="form-group">
                                  <label>Terms and Conditions</label>
                                   <input type="text" class="form-control termscontent" placeholder="" name="termscontent[]" value="'.$allcontent->termscontent.'">
                                   <span class="ck_validation error" style="display: none;">Enter Content</span>
                                </div>
                              </div>                             
                            </div>

                            <div class="col-md-1 icon-groups" style="min-height:unset;">
                                <a class="btn btn-primary icon-add termsaddmore" name="termsaddmore" id="termsaddmore" title="Add more" href="javascript:void(0)"></a>
                            </div>

                           </div>

                        </div>';

                }
                else{

                  $datarows.='
                      <div class="col-md-12 termsrows'.$count.'">
                      <div class="row">
                          <div class="col-md-1"><span>'.($key+1).'</span></div>
                          <div class="col-md-8">                              
                            <div class="add-new-form-wrpr">                               
                              <div class="form-group">

                                 <input type="text" class="form-control termscontent" placeholder="" value="'.$allcontent->termscontent.'" name="termscontent[]">
                                 <span class="ck_validation error" style="display: none;">Enter Content</span>
                              </div>
                            </div>                             
                          </div>

                          <div class="col-md-1 icon-groups">
                              <a class="btn btn-primary icon-remove removeterms" name="removeterms" data-id="'.$count.'" id="removeterms" title="Remove" href="javascript:void(0)"></a>
                          </div>

                          </div>

                      </div>';

                      $count++;

                }


            endforeach;
            $datarows.='</div></form>
                          <div class="modal-footer">
                          <button type="button" data-dismiss="modal"  class="btn btn-default termssbmt">Ok</button>
                          </div>
                      </div>
                      </div>
                  ';

            $datarows.='
                      </div>';
        }elseif(empty($allcontents)){


            $allnewcontents=TermsCondtnsContent::find()->Where(['termsid'=> $model->id])->orderBy(['id' => SORT_ASC])->all();

        $datarows='
                      <div class="modal-dialog">

                      <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" style="text-align:center;">Terms and Conditions</h5>
                           
                          </div>';

        $datarows.='<form id="purchasesss" method="POST"><div class="modal-body" style="display: grid;">
                <div class="termscontentlist">
                <div class="col-md-12">                             
                  <div class="add-new-form-wrpr">                            
                    <div class="form-group">
                      <label></label>
                       <input type="hidden" id="termid" name="termid" value="'.$_POST['termid'].'">
                    </div>
                  </div>                             
                </div>';

        $count = 1;

        foreach($allnewcontents as $key=> $allnewcontent):

          if($key==0){

            $datarows.='
                  <div class="col-md-12">

                      <div class="row">
                        <div class="col-md-1"><span>'.($key+1).'</span></div>
                      <div class="col-md-8">                              
                        <div class="add-new-form-wrpr">                               
                          <div class="form-group">
                            
                             <input type="text" class="form-control termscontent" placeholder="" name="termscontent[]" value="'.$allnewcontent->content.'">
                             <span class="ck_validation error" style="display: none;">Enter Content</span>
                          </div>
                        </div>                             
                      </div>

                      <div class="col-md-1 icon-groups" >
                          <a class="btn btn-primary icon-add termsaddmorepurchase" name="termsaddmore" id="termsaddmore" title="Add more" href="javascript:void(0)"></a>
                      </div>
                        </div>
                  </div>';

          }
          else{

            $datarows.='
                <div class="col-md-12 termsrows'.$count.'">

                   <div class="row">
                    <div class="col-md-1"><span>'.($key+1).'</span></div>
                    <div class="col-md-8">                              
                      <div class="add-new-form-wrpr">                               
                        <div class="form-group">
                          
                           <input type="text" class="form-control termscontent" placeholder="" value="'.$allnewcontent->content.'" name="termscontent[]">
                           <span class="ck_validation error" style="display: none;">Enter Content</span>
                        </div>
                      </div>                             
                    </div>

                    <div class="col-md-1 icon-groups" style="min-height:unset;">
                        <a class="btn btn-primary icon-remove removeterms" name="removeterms" data-id="'.$count.'" id="removeterms" title="Remove" href="javascript:void(0)"></a>
                    </div>
                        </div>
                </div>';

                $count++;

          }


      endforeach;

       $datarows.='</form></div></div>
                    <div class="modal-footer">
                    <button type="button" data-dismiss="modal"  class="btn btn-default ptermssbmt">Ok</button>
                    </div>
                
                </div>
            ';

      $datarows.='
                </div>';
        }

        $arr=array('error'=>'No','result'=>$datarows);
        return json_encode($arr);
    }
}
