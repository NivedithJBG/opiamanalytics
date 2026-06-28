<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl; 
use yii\data\ActiveDataProvider;
use app\models\Projects;
use app\models\Resources;
use app\models\Boq;
use app\models\ResourceGroup;
use app\models\WorkgroupActivitiesNew;
use app\models\WorkgroupsNew;
use app\models\AccountsItem;
use app\models\OrderedResource;
use app\models\Resourcetype;
use app\models\ActivityReportWrkactvty;
use app\models\ProjuserSelection;
use app\models\PricingEstimateNew;
use app\models\PricingEstimateResourcesNew;
use app\models\ScheduleResource;
use app\models\ClientBill;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\AccountsSub;  
use app\models\AccountTypes;   
use app\models\Bsitems;
use yii\filters\VerbFilter;

/**
 * TermscondtnsController implements the CRUD actions for TermsCondtns model.
 */
class DashboardController extends Controller
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

    public function actionActivitycost()
    {
        //$projectid = 63;
        $connection = Yii::$app->db;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if($projuser){
            $projectid = $projuser->projectid;

            /* Estimate cost calculation start*/

            $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
            $command = $connection->createCommand($sqltemvals1);
            $dataReader1 = $command->query();
            $structures = $dataReader1->readAll();
            $pricesum=0;
            if($structures){
                foreach($structures AS $structure):
                     $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                     $command = $connection->createCommand($sql);
                     $dataReader = $command->query();
                     $wbsactivities = $dataReader->readAll();
                     if($wbsactivities){
                       //$datarows.="<tr id='tempprodrow' style='background: white;'><th colspan='9' style='font-size: 16px;background: white;'>".$structure['Name']."</th></tr>";  
                        $subtot=0;
                        foreach($wbsactivities as $key=> $wbsactivity):
                            $sqltemvals="SELECT est_resource_amount AS Price FROM estactivity_resources WHERE estactivity_id='".$wbsactivity['activity_Id']."' ";
                            $command=$connection->createCommand($sqltemvals);
                            $dataReader=$command->query();
                            $resourcesadded=$dataReader->readAll();
                            $price=0;
                            foreach($resourcesadded AS $key=>$data):
                                $price=$price+$data['Price'];
                            endforeach;
                            $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->one();

                            $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->all();

                            if($pricingestimateres):
                                $specrate=0;
                                foreach($pricingestimateres AS $pricingestimatere):
                                    $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                                endforeach;
                                //$specrate=$pricingestimate['specific_rate'];
                            else:
                                $specrate=$price;
                                //$specrate=0;
                            endif;
                            if($pricingestimate){
                            $amount=$pricingestimate->activity_qty * $specrate;
                            $subtot = $subtot + $amount;
                            }
                        endforeach;
                         $pricesum=$pricesum + $subtot; 
                    }
                 endforeach;
            }

            /* Estimate cost calculation end*/

            $project = Projects::findOne($projectid);

            if($project->project_value > $pricesum){
                $profitability = $project->project_value - $pricesum;  
            }
            else{
                $profitability = 0;
            }   

            $datarows = "data.addRows([ 
                            ['Estimate Cost', ".$pricesum."],
                            ['Profitability', ".$profitability."]
                        ]);";

            $arr=array('result'=>$datarows,'project'=>$project->Name,'error'=>'No');
            return json_encode($arr);

        }
        else{
            $datarows = '<div style="text-align: center;">No Project Selected</div>';

            $arr = array('result' => $datarows, 'error' => 'Yes');
            return json_encode($arr);
        }

    }

    public function actionEstimateiowcost()
    {
        //$projectid = 63;
        $connection = Yii::$app->db;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if($projuser){
            $projectid = $projuser->projectid;

            $datarows = 'data.addRows([';

            $iowlist = '';

            $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
            $command = $connection->createCommand($sqltemvals1);
            $dataReader1 = $command->query();
            $structures = $dataReader1->readAll();
            $pricesum=0;
            if($structures){
                foreach($structures AS $structure):
                    $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                    $command = $connection->createCommand($sql);
                    $dataReader = $command->query();
                    $wbsactivities = $dataReader->readAll();
                    if($wbsactivities){
                       //$datarows.="<tr id='tempprodrow' style='background: white;'><th colspan='9' style='font-size: 16px;background: white;'>".$structure['Name']."</th></tr>";  
                        $subtot=0;
                        foreach($wbsactivities as $key=> $wbsactivity):
                            $sqltemvals="SELECT est_resource_amount AS Price FROM estactivity_resources WHERE estactivity_id='".$wbsactivity['activity_Id']."' ";
                            $command=$connection->createCommand($sqltemvals);
                            $dataReader=$command->query();
                            $resourcesadded=$dataReader->readAll();
                            $price=0;
                            foreach($resourcesadded AS $key=>$data):
                                $price=$price+$data['Price'];
                            endforeach;
                            $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->one();

                            $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->all();

                            if($pricingestimateres):
                                $specrate=0;
                                foreach($pricingestimateres AS $pricingestimatere):
                                    $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                                endforeach;
                                //$specrate=$pricingestimate['specific_rate'];
                            else:
                                $specrate=$price;
                                //$specrate=0;
                            endif;
                            $amount=$pricingestimate->activity_qty * $specrate;
                            $subtot = $subtot + $amount;
                        endforeach;
                        $pricesum=$pricesum + $subtot; 

                        if($subtot!=0){

                            $datarows.= "['".$structure['Name']."', ".$subtot."],";

                            $iowlist.= '<tr>
                                        <td>'.$structure['Name'].'</td>
                                        <td>'.number_format($subtot,2).'</td>
                                    </tr>';

                        }
                    }
                endforeach;

                $iowlist.= '<tr>
                                <td>Total</td>
                                <td>'.number_format($pricesum,2).'</td>
                            </tr>';

            }  

            $datarows.= "]);";

            $arr=array('result'=>$datarows,'iowlist'=> $iowlist, 'error'=>'No');
            return json_encode($arr);

        }
        else{
            $datarows = '<div style="text-align: center;">No Project Selected</div>';

            $arr = array('result' => $datarows, 'error' => 'Yes');
            return json_encode($arr);
        }

    }

    public function actionEstimateactivitycost()
    {
        //$projectid = 63;
        $connection = Yii::$app->db;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if($projuser){
            $projectid = $projuser->projectid;

            $datarows = 'drilldata.addRows([';

            $activitylist = '';

            $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Name LIKE '%".$_POST['iowname']."%' AND Project_Id='".$projectid."' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
            $command = $connection->createCommand($sqltemvals1);
            $dataReader1 = $command->query();
            $structure = $dataReader1->read();
            $pricesum=0;

            $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $wbsactivities = $dataReader->readAll();
            if($wbsactivities){
               //$datarows.="<tr id='tempprodrow' style='background: white;'><th colspan='9' style='font-size: 16px;background: white;'>".$structure['Name']."</th></tr>";  
                $subtot=0;
                foreach($wbsactivities as $key=> $wbsactivity):
                    $sqltemvals="SELECT est_resource_amount AS Price FROM estactivity_resources WHERE estactivity_id='".$wbsactivity['activity_Id']."' ";
                    $command=$connection->createCommand($sqltemvals);
                    $dataReader=$command->query();
                    $resourcesadded=$dataReader->readAll();
                    $price=0;
                    foreach($resourcesadded AS $key=>$data):
                        $price=$price+$data['Price'];
                    endforeach;
                    $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->one();

                    $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->all();

                    if($pricingestimateres):
                        $specrate=0;
                        foreach($pricingestimateres AS $pricingestimatere):
                            $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                        endforeach;
                        //$specrate=$pricingestimate['specific_rate'];
                    else:
                        $specrate=$price;
                        //$specrate=0;
                    endif;
                    $amount=$pricingestimate->activity_qty * $specrate;
                    $subtot = $subtot + $amount;
                    $datarows.= "['".$wbsactivity['activity_Name']."', ".$amount."],";

                    if($amount!=0){

                        $activitylist.= '<tr>
                                        <td>'.$wbsactivity['activity_Name'].'</td>
                                        <td>'.number_format($amount,2).'</td>
                                    </tr>';

                    }

                endforeach;
                $pricesum=$pricesum + $subtot; 

                $activitylist.= '<tr>
                                    <td>Total</td>
                                    <td>'.number_format($pricesum,2).'</td>
                                </tr>';
            }

            $datarows.= "]);";

            $iowhead = 'Activity Cost - '.$structure['Name'].' <span class="icon-arrow-left-thick firstlistback"></span>';

            $arr=array('result'=>$datarows,'activitylist'=> $activitylist, 'iowhead'=>$iowhead, 'error'=>'No');
            return json_encode($arr);

        }
        else{
            $datarows = '<div style="text-align: center;">No Project Selected</div>';

            $arr = array('result' => $datarows, 'error' => 'Yes');
            return json_encode($arr);
        }

    }

    public function actionProjectresourcecost()
    {
        $connection = Yii::$app->db;
        //$projectid=36;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid= $projuser->projectid;

        $datarows = 'data.addRows([';

        $resourcelist = '';

        $resourcetypes = Resourcetype::find()->all();

        $pricesum=0;

        if($resourcetypes):

            foreach($resourcetypes AS $resourcetype):

                $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
                $command = $connection->createCommand($sqltemvals1);
                $dataReader1 = $command->query();
                $structures = $dataReader1->readAll();
                if($structures){
                    $restypecost = 0;
                    foreach($structures AS $structure):
                        $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                        $command = $connection->createCommand($sql);
                        $dataReader = $command->query();
                        $wbsactivities = $dataReader->readAll();
                        if($wbsactivities){
                            $subtot=0;
                            foreach($wbsactivities as $key=> $wbsactivity):
                                $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->one();

                                $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['resourcetype_Id' =>$resourcetype->ResourceType_Id])->andWhere(['pricing_status' =>0])->all();

                                $specrate=0;

                                if($pricingestimateres):
                                    foreach($pricingestimateres AS $pricingestimatere):
                                        $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                                    endforeach;
                                endif;
                                if($pricingestimate){
                                $amount=$pricingestimate->activity_qty * $specrate;
                                $subtot = $subtot + $amount;
                                $restypecost = $restypecost + $amount;
                            }
                            endforeach;
                            $pricesum=$pricesum + $subtot; 
                        }
                    endforeach;

                    if($restypecost!=0){
                        $datarows.= "['".$resourcetype->Name."', ".$restypecost."],";
                        $resourcelist.= '<tr>
                                        <td>'.$resourcetype->Name.'</td>
                                        <td>'.number_format($restypecost,2).'</td>
                                    </tr>';
                    }
                }  

            endforeach;

        endif;

        $resourcelist.= '<tr>
                            <td>Total</td>
                            <td>'.number_format($pricesum,2).'</td>
                        </tr>';

        //echo $pricesum; exit;

        $rescost = "data.addRows([['Resource Cost', ".$pricesum."],]);";

        $datarows.= "]);";
        
        //echo $pricesum.'<br>'; echo $datarows; exit;
        $arr = array('result'=>$datarows,'rescost'=>$rescost,'resourcelist'=>$resourcelist,'error'=>'No');
        // echo $arr;
        return json_encode($arr);
    }

    public function actionProjectresourcetypecost()
    {
        $connection = Yii::$app->db;
        //$projectid=36;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid= $projuser->projectid;

        $datarows = 'drilldata.addRows([';

        $resourcelist = '';

        $sql = "SELECT * FROM resourcetype WHERE Name LIKE '%".$_POST['resourcetype']."%' AND Status=0 ORDER BY sortorder ASC";
        $command = $connection->createCommand($sql);
        $dataReader1 = $command->query();
        $resourcetypes = $dataReader1->readAll();

        $pricesum=0;

        if($resourcetypes):

            foreach($resourcetypes AS $resourcetype):

                $resnamearr=array();

                $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
                $command = $connection->createCommand($sqltemvals1);
                $dataReader1 = $command->query();
                $structures = $dataReader1->readAll();
                if($structures){
                    $restypecost = 0;
                    foreach($structures AS $structure):
                        $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                        $command = $connection->createCommand($sql);
                        $dataReader = $command->query();
                        $wbsactivities = $dataReader->readAll();
                        if($wbsactivities){
                            $subtot=0;
                            foreach($wbsactivities as $key=> $wbsactivity):
                                $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->one();

                                $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['resourcetype_Id' =>$resourcetype['ResourceType_Id']])->andWhere(['pricing_status' =>0])->all();

                                $specrate=0;

                                if($pricingestimateres):
                                    foreach($pricingestimateres AS $pricingestimatere):
                                        $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);

                                        $specrate1 =$pricingestimatere['rate'] * $pricingestimatere['quantity'];

                                        $amount1=$pricingestimate->activity_qty * $specrate1;

                                        $resource = Resources::findOne($pricingestimatere['resource_Id']);

                                        array_push($resnamearr, array("resname" => $resource->Name, "amount" => $amount1));

                                        //$datarows.= "['".$resource->Name."', ".$amount1."],";
                                    endforeach;
                                endif;
                                if($pricingestimate){
                                $amount=$pricingestimate->activity_qty * $specrate;
                                $subtot = $subtot + $amount;
                                $restypecost = $restypecost + $amount;
                            }
                            endforeach;
                            $pricesum=$pricesum + $subtot; 
                        }
                    endforeach;

                    $restotals = array_reduce($resnamearr, function ($a, $b) {
                        isset($a[$b['resname']]) ? $a[$b['resname']]['amount'] += $b['amount'] : $a[$b['resname']] = $b;  
                        return $a;
                    });

                    $allresources = array_values($restotals);

                    foreach($allresources as $allresource):

                        $datarows.= "['".$allresource['resname']."', ".$allresource['amount']."],";

                        $sql1 = "SELECT *  FROM `resources` WHERE `Name` LIKE '".$allresource['resname']."' AND pricing_status=0";
                        $command = $connection->createCommand($sql1);
                        $dataReader = $command->query();
                        $resources = $dataReader->readAll();
                        $resids='';
                        foreach ($resources as $key => $resource) {  
                            if($key==0):
                                $resids.=$resource['Resource_Id'];
                                $resunit = $resource['Unit'];
                            else:
                                $resids.=','.$resource['Resource_Id'];
                            endif;
                        }

                        $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere('resource_Id IN('.$resids.')')->andWhere(['pricing_status' =>0])->all();

                        $specrate=0;

                        $specqty=0;

                        $count=0;

                        if($pricingestimateres):
                            foreach($pricingestimateres AS $pricingestimatere):
                                $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$pricingestimatere->activity_id])->andWhere(['pricing_status' =>0])->one();
                                if($pricingestimate){
                                $specrate=$specrate + $pricingestimatere['rate'];
                                $specqty = $specqty + ($pricingestimate->activity_qty*$pricingestimatere['quantity']);
                            }
                                $count++;
                            endforeach;
                        endif;

                        $resourceavgrate = $specrate/$count;

                        $resourceqty = $specqty;

                        $resourcelist.= '<tr>
                                        <td>'.$allresource['resname'].'</td>
                                        <td>'.$resourceavgrate.'</td>
                                        <td>'.$resourceqty.'</td>
                                        <td>'.number_format($allresource['amount'],2).'</td>
                                    </tr>';

                    endforeach;

                    $resourcelist.= '<tr>
                                        <td>Total</td>
                                        <td></td>
                                        <td></td>
                                        <td>'.number_format($pricesum,2).'</td>
                                    </tr>';    

                }  

            endforeach;

        endif;

        $datarows.= "]);";

        $restypename = 'Resource Cost - '.$_POST['resourcetype'].' <span class="icon-arrow-left-thick firstlistbackchart2"></span>';
        
        //echo $datarows; exit;
        //print_r(array_values($restotals)); exit;

        $arr = array('result'=>$datarows, 'restypename'=>$restypename, 'resourcelist'=>$resourcelist, 'error'=>'No');
        // echo $arr;
        return json_encode($arr);
    }

    public function actionProjectresourcedial()
    {

        $connection = Yii::$app->db;
        //$projectid=36;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid= $projuser->projectid;

        $datarows = 'drilldata.addRows([';

        $resourcelist = '';

        $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
        $command = $connection->createCommand($sqltemvals1);
        $dataReader1 = $command->query();
        $structures = $dataReader1->readAll();

        $specrate=0;

        $specqty=0;

        $count=0;

        $resunit ='';

        if($structures){
            $restypecost = 0;
            foreach($structures AS $structure):
                $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                $command = $connection->createCommand($sql);
                $dataReader = $command->query();
                $wbsactivities = $dataReader->readAll();
                if($wbsactivities){
                    $subtot=0;
                    foreach($wbsactivities as $key=> $wbsactivity):
                        $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->one();

                        $sql1 = "SELECT *  FROM `resources` WHERE `Name` LIKE '".$_POST['resourcename']."' AND pricing_status=0";
                        $command = $connection->createCommand($sql1);
                        $dataReader = $command->query();
                        $resources = $dataReader->readAll();
                        $resids='';
                        foreach ($resources as $key => $resource) {  
                            if($key==0):
                                $resids.=$resource['Resource_Id'];
                                $resunit = $resource['Unit'];
                            else:
                                $resids.=','.$resource['Resource_Id'];
                            endif;
                        }

                        $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere('resource_Id IN('.$resids.')')->andWhere(['pricing_status' =>0])->all();

                        if($pricingestimateres):
                            foreach($pricingestimateres AS $pricingestimatere):
                                //$specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                                $specrate=$specrate + $pricingestimatere['rate'];
                                $specqty = $specqty + ($pricingestimate->activity_qty*$pricingestimatere['quantity']);
                                $count++;
                            endforeach;
                        endif;
                    endforeach;
                }
            endforeach;

        }  

        $datarows.= "]);";

        $resourceavgrate = $specrate/$count;

        $number_amt= $this->no_to_words(round($resourceavgrate));

        $resavg_rate = $number_amt['number'];

        $resourceqty = $specqty;

        $number_amt2= $this->no_to_words(round($resourceqty));

        $res_qty = $number_amt2['number'];

        $arr = array('result'=>$datarows, 'resourceavgrate'=>round($resourceavgrate,2), 'resavg_rate'=>$resavg_rate, 'resourceqty'=>round($resourceqty,2), 'res_qty'=>$res_qty, 'resunit'=>$resunit, 'error'=>'No');
        // echo $arr;
        return json_encode($arr);

    }

    function no_to_words($no)
    {
        //echo "no: ".$no;exit;
        //$no=125393353;
        $numlength = strlen((string)$no);
        //echo "numlength: ".$numlength;exit;
        if($numlength == 4 || $numlength == 5):
            $number = $no / 1000;
            $numstr = "";
        elseif($numlength == 6 || $numlength == 7):
            $number = $no / 100000;
            $numstr = "lac";
        elseif($numlength == 8 || $numlength == 9):
            $number = $no / 10000000;
            $numstr = "cr";
        else:
            $number = $no;
            $numstr = "";
        endif;
        //echo "number: ".floor($number*100)/100;exit;
        $arr= array('number'=>(floor($number*100)/100),'numstr'=>$numstr);
        return $arr;

    }


    public function actionProjectdrivercost()
    {
        $connection = Yii::$app->db;
        //$projectid=36;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid= $projuser->projectid;

        $datarows = 'data.addRows([';

        $resourcetypes = Resourcetype::find()->all();

        $pricesum=0;
        $timescost=0;
        $yieldcost=0;
        $capitalcost=0;

        if($resourcetypes):

            foreach($resourcetypes AS $resourcetype):

                $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
                $command = $connection->createCommand($sqltemvals1);
                $dataReader1 = $command->query();
                $structures = $dataReader1->readAll();
                if($structures){
                    $restypecost = 0;
                    foreach($structures AS $structure):
                        $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                        $command = $connection->createCommand($sql);
                        $dataReader = $command->query();
                        $wbsactivities = $dataReader->readAll();
                        if($wbsactivities){
                            $subtot=0;
                            foreach($wbsactivities as $key=> $wbsactivity):
                                $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->one();

                                $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['resourcetype_Id' =>$resourcetype->ResourceType_Id])->andWhere(['pricing_status' =>0])->all();

                                $specrate=0;

                                if($pricingestimateres):
                                    foreach($pricingestimateres AS $pricingestimatere):
                                        $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                                    endforeach;
                                endif;
                                if($pricingestimate){
                                $amount=$pricingestimate->activity_qty * $specrate;
                                $subtot = $subtot + $amount;
                                $restypecost = $restypecost + $amount;
                            }
                            endforeach;
                            $pricesum=$pricesum + $subtot; 
                        }
                    endforeach;
                    if($resourcetype->ResourceType_Id==19 || $resourcetype->ResourceType_Id==33 || $resourcetype->ResourceType_Id==26 || $resourcetype->ResourceType_Id==25){
                        $timescost+=$restypecost;
                    }
                    if($resourcetype->ResourceType_Id==16 || $resourcetype->ResourceType_Id==20 || $resourcetype->ResourceType_Id==21){
                        $yieldcost+=$restypecost;
                    }
                    if($resourcetype->ResourceType_Id==37 || $resourcetype->ResourceType_Id==23 || $resourcetype->ResourceType_Id==24){
                        $capitalcost+=$restypecost;
                    }

                    
                }  

            endforeach;
            $timerows= 'data.addRows([["Time", '.$timescost.'],["Yield", '.$yieldcost.'],["Capital", '.$capitalcost.']])';

        endif;

        //echo $pricesum; exit;

        $datarows.= "]);";
        
        //echo $pricesum.'<br>'; echo $datarows; exit;
        $arr = array('result'=>$timerows,'error'=>'No');
        // echo $arr;
        return json_encode($arr);
    }
    public function actionProjectrestypedrivercost(){
        $connection = Yii::$app->db;
        //$projectid=36;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid= $projuser->projectid;

        $datarows = 'drilldata.addRows([';
        $resourcelists = '';
        if($_POST['resourcetypename']=="Time"){
        $sql = "SELECT * FROM resourcetype WHERE ResourceType_Id IN(19,33,26,25) AND Status=0 ORDER BY sortorder ASC";
        }
        if($_POST['resourcetypename']=="Yield"){
            $sql = "SELECT * FROM resourcetype WHERE ResourceType_Id IN(16,20,21) AND Status=0 ORDER BY sortorder ASC";
        }
        if($_POST['resourcetypename']=="Capital"){
            $sql = "SELECT * FROM resourcetype WHERE ResourceType_Id IN(37,23,24) AND Status=0 ORDER BY sortorder ASC";
        }
        $command = $connection->createCommand($sql);
        $dataReader1 = $command->query();
        $resourcetypes = $dataReader1->readAll();
        //print_r($resourcetypes);exit;
        $pricesum=0;
        $amounttotal=0;

        if($resourcetypes):

            foreach($resourcetypes AS $resourcetype):

                $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
                $command = $connection->createCommand($sqltemvals1);
                $dataReader1 = $command->query();
                $structures = $dataReader1->readAll();
                if($structures){
                    $restypecost = 0;
                    foreach($structures AS $structure):
                        $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                        $command = $connection->createCommand($sql);
                        $dataReader = $command->query();
                        $wbsactivities = $dataReader->readAll();
                        if($wbsactivities){
                            $subtot=0;
                            foreach($wbsactivities as $key=> $wbsactivity):
                                $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->one();

                                $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['resourcetype_Id' =>$resourcetype['ResourceType_Id']])->andWhere(['pricing_status' =>0])->all();

                                $specrate=0;

                                if($pricingestimateres):
                                    foreach($pricingestimateres AS $pricingestimatere):
                                        $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                                    endforeach;
                                endif;
                                if($pricingestimate){
                                $amount=$pricingestimate->activity_qty * $specrate;
                                $subtot = $subtot + $amount;
                                $restypecost = $restypecost + $amount;
                            }
                            endforeach;
                            $pricesum=$pricesum + $subtot; 
                            $amounttotal+=$restypecost;
                        }
                    endforeach;
                    $datarows.= "['".$resourcetype['Name']."', ".$restypecost."],";
                   $resourcelists.= '<tr>
                                        <td>'.$resourcetype['Name'].'</td>
                                        <td>'.number_format($restypecost,2).'</td>
                                    </tr>';
                    
                }  

            endforeach;
            $resourcelists.= '<tr>
                                        <td>Total</td>
                                        <td>'.number_format($pricesum,2).'</td>
                                    </tr>';

        endif;

        //echo $pricesum; exit;

        $datarows.= "]);";
        $driversname = 'Cost Drivers - '.$_POST['resourcetypename'].' <span class="icon-arrow-left-thick mainlistback"></span>';
        
        $arr = array('result'=>$datarows,'driversname'=>$driversname,'resourcelists'=>$resourcelists,'error'=>'No');
        return json_encode($arr);
    }

    public function actionProjectcompletedate()
    {
        $connection = Yii::$app->db;
        //$projectid=36;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid= $projuser->projectid;

        $sql = "SELECT * FROM scheduleactivities AS a INNER JOIN wbsscheduleitems AS b ON  a.scheduleitem_id=b.scheduleitem_id WHERE a.projectId='".$projectid."' AND a.status=0 AND b.status=0 ORDER BY actual_start_date ASC";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $activitystart = $dataReader->read();

        $activitystartdate = $activitystart['actual_start_date'];

        if($activitystartdate!='' && $activitystartdate!='0000-00-00'){

            $sql = "SELECT * FROM scheduleactivities AS a INNER JOIN wbsscheduleitems AS b ON  a.scheduleitem_id=b.scheduleitem_id WHERE a.projectId='".$projectid."' AND a.status=0 AND b.status=0 ORDER BY actual_end_date DESC";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $activityend = $dataReader->read();

            $activityenddate = $activityend['actual_end_date'];

        }
        else{

            $sql = "SELECT * FROM scheduleactivities AS a INNER JOIN wbsscheduleitems AS b ON  a.scheduleitem_id=b.scheduleitem_id WHERE a.projectId='".$projectid."' AND a.status=0 AND b.status=0 ORDER BY start_date ASC";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $activitystart = $dataReader->read();

            $activitystartdate = $activitystart['start_date'];

            $sql = "SELECT * FROM scheduleactivities AS a INNER JOIN wbsscheduleitems AS b ON  a.scheduleitem_id=b.scheduleitem_id WHERE a.projectId='".$projectid."' AND a.status=0 AND b.status=0 ORDER BY end_date DESC";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $activityend = $dataReader->read();

            $activityenddate = $activityend['end_date'];

        }

        $barpercentage = 100;

        $barstartdate = date('M jS Y', strtotime($activitystartdate));
        $barstartdate1 = date('M Y', strtotime($activitystartdate));

        $barenddate = date('M jS Y', strtotime($activityenddate));
        $barenddate1 = date('M Y', strtotime($activityenddate));

        $datarows = "<div class='custom-bar-chart-wrpr'>    

                    <div class='custom-bar-chart'>
                        <div class='bottom-label-wrpr'>
                            <label class='labl-1'>Scheduled end date ".$barenddate."</label>
                            <label class='labl-2' style='left: calc(".$barpercentage."% - 118px);'></label>
                            <label class='labl-3'></label>
                            <label class='labl-4'>Scheduled start date ".$barstartdate."</label>
                        </div>
                        <div class='date-wrpr'>
                            <label class='datelabl-1'>".$barstartdate1."</label>
                            <label class='datelabl-2' style='left:calc(".$barpercentage."% - 42px)'></label>
                            <label class='datelabl-3'>".$barenddate1."</label>
                            
                        </div>
                        
                        <div class='bar-wrpr' style='background: #008000;'>
                            <div class='chart-bar' style='width:".$barpercentage."%;background: #1982ad;'></div>
                        </div>                   

                    </div>
                </div>";


        $arr = array('result'=>$datarows, 'error' => 'No');
        return json_encode($arr);


    }

    public function actionCapacityutiztn()
    {

        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid= $projuser->projectid;

        $schresources=ScheduleResource::find()->where(['projectid' => $projectid])->andWhere(['status' =>0])->all();

        $subcontr_cap =0;
        $subcontr_utz =0;
        $labour_cap =0;
        $labour_utz =0;
        $plant_cap =0;
        $plant_utz =0;
        $leased_cap =0;
        $leased_utz =0;

        if(count($schresources)>0):

            foreach($schresources as $schresource):

                $resource=Resources::findOne($schresource->resourceid);

                if($resource->ResourceType_Id==19){
                    $subcontr_cap+= $schresource->capacity;
                    $subcontr_utz+= $schresource->utilised;
                }
                elseif($resource->ResourceType_Id==33){
                    $labour_cap+= $schresource->capacity;
                    $labour_utz+= $schresource->utilised;
                }
                elseif($resource->ResourceType_Id==26){
                    $plant_cap+= $schresource->capacity;
                    $plant_utz+= $schresource->utilised;    
                }
                else{
                    $leased_cap+= $schresource->capacity;
                    $leased_utz+= $schresource->utilised;
                }

            endforeach;

        endif;

        if($subcontr_cap!=0 && $subcontr_utz!=0){
            $subper = ($subcontr_cap/$subcontr_utz)*100;
        }
        else{
            $subper = 0;
        }

        if($labour_cap!=0 && $labour_utz!=0){
            $labourper = ($labour_cap/$labour_utz)*100;
        }
        else{
            $labourper = 0;
        }

        if($plant_cap!=0 && $plant_utz!=0){
            $plantper = ($plant_cap/$plant_utz)*100;
        }
        else{
            $plantper = 0;
        }

        if($leased_cap!=0 && $leased_utz!=0){
            $leasedper = ($leased_cap/$leased_utz)*100;
        }
        else{
            $leasedper = 0;
        }

        if($subper>100){
            $substyle = 'red';
        }
        else{
            $substyle = 'green';
        }

        if($labourper>100){
            $labstyle = 'red';
        }
        else{
            $labstyle = 'green';
        }

        if($plantper>100){
            $plantstyle = 'red';
        }
        else{
            $plantstyle = 'green';
        }

        if($leasedper>100){
            $leasedstyle = 'red';
        }
        else{
            $leasedstyle = 'green';
        }

        $datarows= "data.addRows([
                        ['Sub:Contractors', 100, ".round($subper, 2).", '".$substyle."'],
                        ['Direct Labour', 100, ".round($labourper, 2).", '".$labstyle."'],
                        ['Plant & Equipment Usage', 100, ".round($plantper, 2).", '".$plantstyle."'],
                        ['Leased Equipment/Premises', 100, ".round($leasedper, 2).", '".$leasedstyle."']
                    ]);
                ";

        $arr = array('result'=>$datarows,'error'=>'No');
        // echo $arr;
        return json_encode($arr);
        
    }

    public function actionCapacityutiztnresource()
    {
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid= $projuser->projectid;

        $schresources=ScheduleResource::find()->where(['projectid' => $projectid])->andWhere(['status' =>0])->groupBy(['resourceid'])->all();

        $datarows = array();

        $subcontr= "data.addRows([";

        $labour= "data.addRows([";

        $plant= "data.addRows([";

        $leased= "data.addRows([";

        $subcontrcount = 0;
        $labourcount = 0;
        $plantcount = 0;
        $leasedcount = 0;

        if(count($schresources)>0):

            foreach($schresources as $schresource):

                $resource=Resources::findOne($schresource->resourceid);

                $schtyperesources=ScheduleResource::find()->where(['projectid' => $projectid])->andWhere(['status' =>0])->andWhere(['resourceid' => $schresource->resourceid])->all();

                $resourcecapcty = 0;
                $resourceutilizd = 0;

                foreach($schtyperesources as $schtyperesource):

                    $resourcecapcty+= $schtyperesource->capacity;
                    $resourceutilizd+= $schtyperesource->utilised;

                endforeach; 

                if($resourcecapcty!=0 && $resourceutilizd!=0){
                    $resper = ($resourcecapcty/$resourceutilizd)*100;
                }
                else{
                    $resper = 0;
                }

                if($resper>100){
                    $resstyle = 'red';
                }
                else{
                    $resstyle = 'green';
                }

                if($resource->ResourceType_Id==19){
                    $subcontrcount++;
                    $subcontr.= "['".$resource->Name."', 100, ".round($resper, 2).", '".$resstyle."'],";
                }
                elseif($resource->ResourceType_Id==33){
                    $labourcount++;
                    $labour.= "['".$resource->Name."', 100, ".round($resper, 2).", '".$resstyle."'],";
                }
                elseif($resource->ResourceType_Id==26){
                    $plantcount++;
                    $plant.= "['".$resource->Name."', 100, ".round($resper, 2).", '".$resstyle."'],";   
                }
                else{
                    $leasedcount++;
                    $leased.= "['".$resource->Name."', 100, ".round($resper, 2).", '".$resstyle."'],";
                }

            endforeach;

        endif;

        $subcontr.= "]);";

        $labour.= "]);";

        $plant.= "]);";

        $leased.= "]);";

        array_push($datarows, $subcontr);

        array_push($datarows, $labour);

        array_push($datarows, $plant);

        array_push($datarows, $leased);

        $arr = array('result'=>$datarows,'count'=>4,'error'=>'No');
        // echo $arr;
        return json_encode($arr);
        
    }

    public function actionRoichart()
    {
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid= $projuser->projectid;

        $project = Projects::findOne($projectid);

        $prjctvalue = $project->project_value;

        $connection = Yii::$app->db;

        $sql = "SELECT * FROM resourcetype WHERE ResourceType_Id=37 AND Status=0 ORDER BY sortorder ASC";

        $command = $connection->createCommand($sql);
        $dataReader1 = $command->query();
        $resourcetypes = $dataReader1->readAll();

        $restypecost=0;
        if($resourcetypes):

            foreach($resourcetypes AS $resourcetype):

                $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
                $command = $connection->createCommand($sqltemvals1);
                $dataReader1 = $command->query();
                $structures = $dataReader1->readAll();
                if($structures){
                    $restypecost = 0;
                    foreach($structures AS $structure):
                        $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                        $command = $connection->createCommand($sql);
                        $dataReader = $command->query();
                        $wbsactivities = $dataReader->readAll();
                        if($wbsactivities){
                            $subtot=0;
                            foreach($wbsactivities as $key=> $wbsactivity):
                                $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->one();

                                $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['resourcetype_Id' =>$resourcetype['ResourceType_Id']])->andWhere(['pricing_status' =>0])->all();

                                $specrate=0;

                                if($pricingestimateres):
                                    foreach($pricingestimateres AS $pricingestimatere):
                                        $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                                    endforeach;
                                endif;
                                if($pricingestimate){
                                    $amount=$pricingestimate->activity_qty * $specrate;
                                    $subtot = $subtot + $amount;
                                    //$restypecost = $restypecost + $amount;
                                }
                            endforeach;
                            $restypecost=$restypecost + $subtot; 
                            //$amounttotal+=$restypecost;
                        }
                    endforeach;
                    
                }  

            endforeach;

        endif;

        $estinvstmnts = $restypecost;

        /* Estimate cost calculation start*/

        $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
        $command = $connection->createCommand($sqltemvals1);
        $dataReader1 = $command->query();
        $structures = $dataReader1->readAll();
        $pricesum=0;
        if($structures){
            foreach($structures AS $structure):
                 $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                 $command = $connection->createCommand($sql);
                 $dataReader = $command->query();
                 $wbsactivities = $dataReader->readAll();
                 if($wbsactivities){
                   //$datarows.="<tr id='tempprodrow' style='background: white;'><th colspan='9' style='font-size: 16px;background: white;'>".$structure['Name']."</th></tr>";  
                    $subtot=0;
                    foreach($wbsactivities as $key=> $wbsactivity):
                        $sqltemvals="SELECT est_resource_amount AS Price FROM estactivity_resources WHERE estactivity_id='".$wbsactivity['activity_Id']."' ";
                        $command=$connection->createCommand($sqltemvals);
                        $dataReader=$command->query();
                        $resourcesadded=$dataReader->readAll();
                        $price=0;
                        foreach($resourcesadded AS $key=>$data):
                            $price=$price+$data['Price'];
                        endforeach;
                        $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->one();

                        $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->all();

                        if($pricingestimateres):
                            $specrate=0;
                            foreach($pricingestimateres AS $pricingestimatere):
                                $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                            endforeach;
                            //$specrate=$pricingestimate['specific_rate'];
                        else:
                            $specrate=$price;
                            //$specrate=0;
                        endif;
                        if($pricingestimate){
                            $amount=$pricingestimate->activity_qty * $specrate;
                            $subtot = $subtot + $amount;
                        }
                    endforeach;
                     $pricesum=$pricesum + $subtot; 
                }
             endforeach;
        }

        $estimatecost = $pricesum;

        /* Estimate cost calculation end*/

        $estprftblty = $prjctvalue - $estimatecost;

        $prvalue = (10*$prjctvalue)/100;

        $estprjctnvestments = $estinvstmnts + $prvalue;

        $expectedroi = ($estprjctnvestments/$estprftblty)*100;

        $datarows= "data.addRows([
                        ['Expected ROI', ".round($expectedroi,1).",".round($expectedroi,1).", 'green']
                    ]);
                ";

        $arr = array('result'=>$datarows,'prjctvalue'=>$prjctvalue,'estimatecost'=>$estimatecost,'estinvestments'=>$estinvstmnts,'estprftblty'=>$estprftblty,'estprjctnvestments'=>$estprjctnvestments,'error'=>'No');
        return json_encode($arr);


    }

    public function materialsamount($activityid,$resourceid,$projectid)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT *  FROM `resources` WHERE `Resource_Id` = ".$resourceid." AND pricing_status=0";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $prestimateitem=$dataReader->read();
        $resourcename=$prestimateitem['Name'];
        $sql = "SELECT *  FROM `resources` WHERE `Name` LIKE '".$resourcename."' AND pricing_status=0";
        //$sql = "SELECT *  FROM `resources` WHERE `Name` LIKE 'Cement Bulker-OPC' AND pricing_status=0";
        //echo $sql;exit;
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $purchaseresources = $dataReader->readAll();
        $resids='';
        if($purchaseresources):
            foreach ($purchaseresources as $key => $purchaseresource) {
                if($key==0):
                    $resids.=$purchaseresource['Resource_Id'];
                else:
                    $resids.=','.$purchaseresource['Resource_Id'];
                endif;
            }
            //echo $resids;exit;
            $sql = "SELECT *  FROM `issue_slips` WHERE activity_id=".$activityid." AND `resource_id` IN (".$resids.") AND project_id=".$projectid." AND delete_status=0";
            //echo $sql;exit;
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $issueslipitems=$dataReader->readAll();
            //echo "issitems: ".count($issueslipitems);exit;
            $issuqty=0;
            if($issueslipitems):
                foreach ($issueslipitems as $value) {
                    $issuqty+=$value['resource_qty'];
                }
            endif;
            $stocktotal= 0;
            //echo "stocktotal: ".$stocktotal;exit;
            $sql="select a.invoice_date,a.vendor,b.resource_qty,b.invoice_id,b.order_id from invoice as a inner join invoice_resources as b on a.invoice_id=b.invoice_id where a.status!=3 AND b.resource_id IN (".$resids.") AND a.project_id=".$projectid." ";
            //echo $sql;exit;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $dataresources = $dataReader->readAll();
            $totqnty=0;
            $total_amount=0;
            //$scheduleitem = $estimateresource->resource_Id;
            if($dataresources):
                foreach($dataresources AS $key=>$dataresource):
                    $orderdetails=OrderedResource::find()->where(['order_id' => $dataresource['order_id']])->andWhere('resource_id IN('.$resids.')')->one();
                    $procamount=$orderdetails['rate'] * $dataresource['resource_qty'];
                    //echo "procamount: ".$procamount."<br>";
                    $total_amount+=$procamount;
                    $totqnty+=$dataresource['resource_qty'];
                    //echo "totqnty: ".$dataresource['resource_qty']."<br>";
                endforeach;
            endif;
            //echo $totqnty; exit;
            if($total_amount!=0 && $totqnty!=0){
                $avg_rate = $total_amount / $totqnty;
            }
            else{
                $avg_rate = 0;
            }
            //echo "avg_rate: ".$avg_rate;exit;
        else:
            $avg_rate=0;
        endif;
        $dataitems = array('issuqty'=>$issuqty,'avg_rate'=>$avg_rate,'stocktotal'=>abs($stocktotal));
        return $dataitems;
        //return $avg_rate;
    }

    public function equipmentamount($activityid,$resourceid,$projectid)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT *  FROM `resources` WHERE `Resource_Id` = ".$resourceid." AND pricing_status=0";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $prestimateitem=$dataReader->read();

        if($prestimateitem){
            //$sql ="SELECT sum(diesel) as dieselissued, sum(worked_hours) as workedhours FROM logbook WHERE equipment_id =".$resourceid." ";
            $sql ="SELECT sum(diesel) as dieselissued, sum(worked_hours) as workedhours FROM logbook WHERE equipment_id =".$resourceid." AND activity_id =".$activityid."";
            //echo $sql;exit;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $equipment = $dataReader->read();

            if($equipment['dieselissued']!=''){
                $dieselissued = $equipment['dieselissued'];
            }
            else{
                $dieselissued = 0;               
            }

            if($equipment['workedhours']!=''){
                $dieselwrkdhrs = $equipment['workedhours'];
            }
            else{               
                $dieselwrkdhrs = 0; 
            }
        }
        else{
            $dieselissued = 0;
            $dieselwrkdhrs = 0;
        }

        //echo $dieselwrkdhrs; exit;

        //$resourcename=$prestimateitem['Name'];
        $sql = "SELECT *  FROM `resources` WHERE `Name` LIKE 'Diesel' AND pricing_status=0";
        //echo $sql;exit;
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $dieselpurchase = $dataReader->readAll();
        $deslresids='';
        if($dieselpurchase):
            foreach ($dieselpurchase as $key => $diesel) {  
                if($key==0):
                    $deslresids.=$diesel['Resource_Id'];
                else:
                    $deslresids.=','.$diesel['Resource_Id'];  
                endif;
            }
            //echo $deslresids;exit;  
            $sql="select a.invoice_date,a.vendor,b.resource_qty,b.invoice_id,b.order_id from invoice as a inner join invoice_resources as b on a.invoice_id=b.invoice_id where a.status!=3 AND b.resource_id IN (".$deslresids.") AND a.project_id=".$projectid." ";
            //echo $sql;exit;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $dataresources = $dataReader->readAll();
            $totqnty=0;
            $total_amount=0;
            //$scheduleitem = $estimateresource->resource_Id;
            if($dataresources):
                foreach($dataresources AS $key=>$dataresource):
                    $orderdetails=OrderedResource::find()->where(['order_id' => $dataresource['order_id']])->andWhere('resource_id IN('.$deslresids.')')->one();
                    $procamount=$orderdetails['rate'] * $dataresource['resource_qty'];
                    //echo "procamount: ".$procamount."<br>";
                    $total_amount+=$procamount;
                    $totqnty+=$dataresource['resource_qty'];
                    //echo "totqnty: ".$dataresource['resource_qty']."<br>";
                endforeach;
            endif;
            //echo "total_amount: ".$total_amount;exit;
            //echo "totqnty: ".$totqnty;exit;
            if($total_amount!=0 && $totqnty!=0){
                $avg_rate1 = $total_amount / $totqnty;
            }
            else{
                $avg_rate1 = 0;
            }
            //echo "avg_rate: ".$avg_rate;exit;

            // Diesel issued and worked hours

            //echo $equipment['dieselissued']; exit;

        else:
            $avg_rate1=0;
            //$dieselperhour = 0;
            //$dieselcost = 0;
        endif;

        //echo $dieselcost; exit;

        if($dieselissued!=0 && $dieselwrkdhrs!=0){
            $dieselperhour = $dieselissued / $dieselwrkdhrs;
        }
        else{
            $dieselperhour = 0;
        }

        $dieselcost = $dieselperhour * $avg_rate1;

        //echo $dieselcost; exit;

        //repair cost

        $sql="SELECT sum(amount) as totalrepairamt FROM voucher WHERE (account_id=15 OR creditacnt=15) AND project_id='".$projectid."' AND Resource_Id='".$resourceid."'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $voucher=$dataReader->read();

        if($voucher['totalrepairamt']!=''){
            $repaircost = $voucher['totalrepairamt'] / $dieselwrkdhrs;
        }
        else{
            $repaircost = 0;
        }  

        $costperhour = $dieselcost + $repaircost;

        $avg_rate = $costperhour * $dieselwrkdhrs;

        $issuqty=1;

        $dataitems = array('issuqty'=>$issuqty,'avg_rate'=>$avg_rate);  
        return $dataitems;
    }

    public function subcontractoramount($activityid,$resourceid,$projectid)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT *  FROM `resources` WHERE `Resource_Id` = ".$resourceid." AND pricing_status=0";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $prestimateitem=$dataReader->read();
        $resourcename=$prestimateitem['Name'];
        $sql = "SELECT *  FROM `resources` WHERE `Name` LIKE '".$resourcename."' AND pricing_status=0";
        //$sql = "SELECT *  FROM `resources` WHERE `Name` LIKE 'Cement Bulker-OPC' AND pricing_status=0";
        //echo $sql;exit;
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $purchaseresources = $dataReader->readAll();
        $resids='';
        if($purchaseresources):
            foreach ($purchaseresources as $key => $purchaseresource) {  
                if($key==0):
                    $resids.=$purchaseresource['Resource_Id'];
                else:
                    $resids.=','.$purchaseresource['Resource_Id'];  //print_r($resids); exit;
                endif;
            }
            //echo $resids;exit;
            //$sql="select a.place,b.resource_id,b.resource_qty from workorder_items as b inner join workorder_bills as a on a.bill_id=b.bill_id where a.delete_status=0 AND b.resource_id IN (".$resids.") AND a.place=".$projectid." ";

            $sql="select b.place,a.resource_id,b.order_id,a.resource_qty from workorder_items as a inner join workorder_bills as b on a.bill_id=b.bill_id where b.delete_status=0 AND b.status!=3 AND a.resource_id IN (".$resids.") AND a.activity_id=".$activityid." AND b.place=".$projectid."";
            //echo $sql;exit;
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $issueslipitems=$dataReader->readAll();
            $culumatedqty=0;
            $totalbillamount=0;
            if($issueslipitems):
                foreach ($issueslipitems as $value) {  
                    $culumatedqty+=$value['resource_qty'];
                    //echo "culumatedqty: ".$culumatedqty;
                    $orderdetails=OrderedResource::find()->where(['order_id' => $value['order_id']])->andWhere('resource_id IN('.$resids.')')->one();
                    $billamount=$orderdetails['rate'] * $value['resource_qty'];
                    //echo "billamount: ".$billamount;exit;
                    $totalbillamount+=$billamount;
                }
            endif;
            //echo "culumatedqty: ".$culumatedqty;exit;
            //echo "totalbillamount: ".$totalbillamount;exit;
            if($totalbillamount!=0 && $culumatedqty!=0){
                $avg_rate = $totalbillamount / $culumatedqty;  //  print_r($avg_rate); exit;
            }
            else{
                $avg_rate = 0;
            }
            //echo "avg_rate: ".$avg_rate;exit;
        else:
            $avg_rate=0;
        endif;
        $dataitems = array('issuqty'=>$culumatedqty,'avg_rate'=>$avg_rate);  
        return $dataitems;
        //return $avg_rate;
    }
    public function supportamount($resourceid,$projectid)
    {
        //echo "resourceid: ".$resourceid;exit;
        $connection = Yii::$app->db;
        $resource=Resources::findOne($resourceid);
         $sql = "SELECT *  FROM `resources` WHERE `Name` LIKE '".$resource->Name."' AND pricing_status=0";
        //$sql = "SELECT *  FROM `resources` WHERE `Name` LIKE 'Contractor for supply of concrete' AND pricing_status=0";
        //echo $sql;exit;
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $purchaseresources = $dataReader->readAll();
        $resids='';
        if($purchaseresources):
            foreach ($purchaseresources as $key => $purchaseresource) {
                if($key==0):
                    $resids.=$purchaseresource['Resource_Id'];
                else:
                    $resids.=','.$purchaseresource['Resource_Id'];
                endif;
            }
            //echo $resids;exit;
            $sql = "SELECT * FROM accounts_item where resource_group='" . $resource->Resource_group_Id . "' ";
            //echo $sql;exit;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $acctitems = $dataReader->readAll();
            //echo "acctitems: ".count($acctitems);exit;
            $ledgeramount = 0;
            foreach($acctitems as $acctitem):
                $accntid = $acctitem['id']; 
                //$accntid = 98; 
                $sql="(SELECT id,voucher_no,DATE_FORMAT(date,'%d/%m/%y') AS date,narration,amount,type,creditacnt,bank_id,contra,account_id,'Voucher' AS rawtype FROM voucher WHERE (account_id='".$accntid."' OR creditacnt='".$accntid."') AND (place='".$projectid."' OR project_id='".$projectid."') )
                    UNION
                    (SELECT id,voucher_no,DATE_FORMAT(date,'%d/%m/%y') AS date,narration,amount,type,creditacnt,bank_id,contra,debitacnt AS account_id,'Journal' AS rawtype FROM journalvoucher WHERE (debitacnt='".$accntid."' OR creditacnt='".$accntid."') AND (project_id='".$projectid."' OR place='".$projectid."')) ORDER BY DATE_FORMAT(date,'%d/%m/%y') ASC,id DESC";
                //echo $sql;exit;
                //$command=$connection->createCommand($sql);
                //$dataReader=$command->query();
                //$dataProvider=$dataReader->readAll();
                //echo "items: ".count($dataProvider);exit;
                $initialdate='2014-03-31';
                $enddate=date('Y-m-d');
                $command = Yii::$app->db->createCommand("CALL GetLedgerBalance (:startdate, :currentdate,:place,:accounthead,:project,@outval)");
                $command->bindParam(":startdate",$initialdate);
                $command->bindParam(":currentdate", $enddate);
                $place=12;
                $command->bindParam(":place", $place);
                $command->bindParam(":accounthead", $accntid);
                $command->bindParam(":project", $projectid);
                $command->query();
                //print_r($command);exit;
                $datavouchers=Yii::$app->db->createCommand("select @outval as result;")->queryScalar();
                //$datavouchers=711551.91;
                if($datavouchers!=0):
                    $sql1="SELECT balance FROM ledger_openingbalance WHERE accountid='".$accntid."'";
                    //echo $sql1;exit;
                    $command=$connection->createCommand($sql1);
                    $dataReader=$command->query();
                    $balance=$dataReader->read();
                    $openingbal=$balance['balance'];
                else:
                    $openingbal=0;
                endif;
                //echo "openingbal: ".$openingbal;exit;
                $openbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                //$openbalance = $openingbal + $datavouchers;
                //echo "openbalance: ".$openbalance;exit;
                $ledgeramount+=abs($openbalance);
            endforeach;
            $current_date = date('Y-m-d');
            $project = Projects::findOne($projectid);
            $projectdate = $project->start_date;
            $ts1 = strtotime($current_date);
            $ts2 = strtotime($projectdate);

            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);

            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);

            $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
            $monthdiff = abs($diff);
            //echo "monthdiff: ".$monthdiff;exit;
            if($ledgeramount!=0 && $monthdiff!=0){
                $avg_rate = $ledgeramount / $monthdiff;
            }
            else{
                $avg_rate = 0;
            }
            //echo "avg_rate: ".$avg_rate;exit;
        else:
            $avg_rate=0;
        endif;
        $dataitems = array('issuqty'=>$monthdiff,'avg_rate'=>$avg_rate);  
        return $dataitems;
    }

    public function directworkersamount($activityid,$resourceid,$projectid)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT *  FROM `resources` WHERE `Resource_Id` = ".$resourceid." AND pricing_status=0";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $prestimateitem=$dataReader->read();
        $resourcename=$prestimateitem['Name'];
        $sql = "SELECT *  FROM `resources` WHERE `Name` LIKE '".$resourcename."' AND pricing_status=0";
        //$sql = "SELECT *  FROM `resources` WHERE `Name` LIKE 'Cement Bulker-OPC' AND pricing_status=0";
        //echo $sql;exit;
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $purchaseresources = $dataReader->readAll();
        $resids='';
        if($purchaseresources):
            foreach ($purchaseresources as $key => $purchaseresource) {  
                if($key==0):
                    $resids.=$purchaseresource['Resource_Id'];
                else:
                    $resids.=','.$purchaseresource['Resource_Id'];  
                endif;
            }
            //echo $resids;exit;            
            $sql = "SELECT *  FROM `musterroll` WHERE activity_id=".$activityid." AND `trade_id` IN (".$resids.") AND `project_id` = ".$projectid." ";
            //echo $sql;exit;
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $directworkitems=$dataReader->readAll();
            $noofdays=0;
            $totalwagesamount=0;
            if($directworkitems):
                foreach ($directworkitems as $value) {  
                    $noofdays+=$value['hours'];
                    //echo "noofdays: ".$noofdays;
                    $totalwagesamount+=$value['wages'];
                }
            endif;
            //echo "noofdays: ".$noofdays;exit;
            //echo "totalwagesamount: ".$totalwagesamount;exit;
            if($totalwagesamount!=0 && $noofdays!=0){
                $avg_rate = $totalwagesamount / $noofdays;
            }
            else{
                $avg_rate = 0;
            }
            //echo "avg_rate: ".$avg_rate;exit;
        else:
            $avg_rate=0;
        endif;
        $dataitems = array('issuqty'=>$noofdays,'avg_rate'=>$avg_rate);  
        return $dataitems;
    }

    public function investmentamount($resourceid,$projectid)
    {
        //echo "resourceid: ".$resourceid;exit;
        $connection = Yii::$app->db;
        $resource=Resources::findOne($resourceid);
         $sql = "SELECT *  FROM `resources` WHERE `Name` LIKE '".$resource->Name."' AND pricing_status=0";
        //$sql = "SELECT *  FROM `resources` WHERE `Name` LIKE 'Contractor for supply of concrete' AND pricing_status=0";
        //echo $sql;exit;
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $purchaseresources = $dataReader->readAll();
        $resids='';
        if($purchaseresources):
            //echo $resids;exit;
            $sql = "SELECT * FROM accounts_item where resource_group='" . $resource->Resource_group_Id . "' ";
            //echo $sql;exit;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $acctitems = $dataReader->readAll();
            //echo "acctitems: ".count($acctitems);exit;
            $ledgeramount = 0;
            foreach($acctitems as $acctitem):
                $accntid = $acctitem['id']; 
                //$accntid = 98; 
                $sql="(SELECT id,voucher_no,DATE_FORMAT(date,'%d/%m/%y') AS date,narration,amount,type,creditacnt,bank_id,contra,account_id,'Voucher' AS rawtype FROM voucher WHERE (account_id='".$accntid."' OR creditacnt='".$accntid."') AND (place='".$projectid."' OR project_id='".$projectid."') )
                    UNION
                    (SELECT id,voucher_no,DATE_FORMAT(date,'%d/%m/%y') AS date,narration,amount,type,creditacnt,bank_id,contra,debitacnt AS account_id,'Journal' AS rawtype FROM journalvoucher WHERE (debitacnt='".$accntid."' OR creditacnt='".$accntid."') AND (project_id='".$projectid."' OR place='".$projectid."')) ORDER BY DATE_FORMAT(date,'%d/%m/%y') ASC,id DESC";
                //echo $sql;exit;
                //$command=$connection->createCommand($sql);
                //$dataReader=$command->query();
                //$dataProvider=$dataReader->readAll();
                //echo "items: ".count($dataProvider);exit;
                $initialdate='2014-03-31';
                $enddate=date('Y-m-d');
                $command = Yii::$app->db->createCommand("CALL GetLedgerBalance (:startdate, :currentdate,:place,:accounthead,:project,@outval)");
                $command->bindParam(":startdate",$initialdate);
                $command->bindParam(":currentdate", $enddate);
                $place=12;
                $command->bindParam(":place", $place);
                $command->bindParam(":accounthead", $accntid);
                $command->bindParam(":project", $projectid);
                $command->query();
                //print_r($command);exit;
                $datavouchers=Yii::$app->db->createCommand("select @outval as result;")->queryScalar();
                //$datavouchers=711551.91;
                if($datavouchers!=0):
                    $sql1="SELECT balance FROM ledger_openingbalance WHERE accountid='".$accntid."'";
                    //echo $sql1;exit;
                    $command=$connection->createCommand($sql1);
                    $dataReader=$command->query();
                    $balance=$dataReader->read();
                    $openingbal=$balance['balance'];
                else:
                    $openingbal=0;
                endif;
                //echo "openingbal: ".$openingbal;exit;
                $openbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                //$openbalance = $openingbal + $datavouchers;
                //echo "openbalance: ".$openbalance;exit;
                $ledgeramount+=abs($openbalance);
            endforeach;
            if($ledgeramount!=0){
                $avg_rate = $ledgeramount;
            }
            else{
                $avg_rate = 0;
            }
        else:
            $avg_rate=0;
        endif;
        $monthdiff = 1;
        //echo $ledgeramount; exit;
        $dataitems = array('issuqty'=>$monthdiff,'avg_rate'=>$avg_rate); 
        return $dataitems;
    }


    public function actionProcresourcedrill()
    {

        $connection = Yii::$app->db;
        //$projectid=36;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid= $projuser->projectid;

        /* Resource estimate qty start */

        $sql1 = "SELECT *  FROM `resources` WHERE `Name` LIKE '".$_POST['resourcename']."' AND pricing_status=0";
        //$sql1 = "SELECT *  FROM `resources` WHERE `Name` LIKE 'Cement Bulker-OPC' AND pricing_status=0";
        $command = $connection->createCommand($sql1);
        $dataReader = $command->query();
        $resources = $dataReader->readAll();
        $resids='';
        foreach ($resources as $key => $resource) {  
            if($key==0):
                $resids.=$resource['Resource_Id'];
                $resunit = $resource['Unit'];
            else:
                $resids.=','.$resource['Resource_Id'];
            endif;
        }

        $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere('resource_Id IN('.$resids.')')->andWhere(['pricing_status' =>0])->all();

        $specrate=0;

        $specqty=0;

        $count=0;

        $totrepqty=0;

        $clintbillqty = 0;

        $activityids = array();

        if($pricingestimateres):
            foreach($pricingestimateres AS $pricingestimatere):
                $item = WorkgroupActivitiesNew::findOne($pricingestimatere->activity_id);
                $iow = WorkgroupsNew::findOne($item->wbs_id);
                if($iow->Status==0 && $item->pricing_status==0){
                    $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$pricingestimatere->activity_id])->andWhere(['pricing_status' =>0])->one();
                    if($pricingestimate){
                        $specrate=$specrate + $pricingestimatere['rate'];
                        $specqty = $specqty + ($pricingestimate->activity_qty*$pricingestimatere['quantity']);
                    }
                    $reportedacts=ActivityReportWrkactvty::find()->where(['iow_id' => $iow->Workgroup_Id])->andWhere(['activity_id' => $pricingestimatere->activity_id])->andWhere(['projectid' => $projectid])->andWhere(['status' => 0])->all();

                    $client_bills=ClientBill::find()->where(['projectid' => $projectid])->andWhere(['boq_id' => $item->boq_slno])->andWhere(['status' => 0])->all();

                    if (!in_array($pricingestimatere->activity_id, $activityids))
                    {
                        array_push($activityids, $pricingestimatere->activity_id);

                        foreach($reportedacts AS $reportedact):
                            $totrepqty+=$reportedact['quantity'];
                        endforeach;

                        foreach($client_bills as $client_bill):
                            $clintbillqty+= $client_bill->current_qty;
                        endforeach;

                    }
                    
                    $count++;
                }
            endforeach;
        endif;

        if($count!=0){
            $resourceavgrate = $specrate/$count;
        }
        else{
            $resourceavgrate = 0;
        }

        $estimateqty = $specqty;

        /* Resource estimate qty end */

        /* Procured total and stock total start */

        $_POST['project'] = $projectid;

        $condition='AND a.project_id='.$_POST['project'].' ';
        $condition1=' AND b.project_id='.$_POST['project'].'';
        $condition2=' AND a.project_id='.$_POST['project'].'';
        $condition3='?project_id='.$_POST['project'].'';
        $condition4=' AND project_id='.$_POST['project'].' ';

        $sql2 = "SELECT SUM(a.resource_qty) as usedqnty FROM invoice_resources as a INNER JOIN invoice as b on a.invoice_id=b.invoice_id WHERE a.resource_id IN (".$resids.") AND b.status!=3 ".$condition1." ";
        //echo $sql2;exit;
        $command = $connection->createCommand($sql2);
        $dataReader = $command->query();
        $dataused = $dataReader->read();

        $sql3 = "SELECT SUM(resource_qty) as usedqnty  FROM issue_slips WHERE resource_id IN (".$resids.") ".$condition4." AND delete_status=0 ";
        //echo $sql3;exit;
        $command = $connection->createCommand($sql3);
        $dataReader = $command->query();
        $activityusage = $dataReader->read();
        $stockqnty=$dataused['usedqnty'] - $activityusage['usedqnty'];

        $sql1="select b.invoice_date,b.vendor,a.resource_qty,a.invoice_id,a.order_id from invoice_resources as a inner join invoice as b on a.invoice_id=b.invoice_id where b.status!=3 AND a.resource_id IN(".$resids.") ".$condition1." ";
        $command = $connection->createCommand($sql1);
        $dataReader = $command->query();
        $dataresources = $dataReader->readAll();
        $totrate=0;
        $amountincgst=0;
        $count=0;
        $gstamount=0;
        foreach($dataresources AS $dataresource):
            //$totrate+=$dataresource['rate'];
            //$orderdetails=OrderedResource::find()->where(['order_id'=>$dataresource['order_id']])->andwhere([ 'IN','resource_id',$resids ])->one();

            $orderdetails=OrderedResource::find()->where(['order_id' => $dataresource['order_id']])->andWhere('resource_id IN('.$resids.')')->one();

            if($orderdetails){
                $totrate+=$orderdetails['rate'] * $dataresource['resource_qty'];
            
                if($orderdetails['cgst']!=0):
                    $gstamount=($totrate * ($orderdetails['cgst'] + $orderdetails['sgst']))/100;
                else:
                    $gstamount=($totrate * $orderdetails['igst'])/100;
                endif;
            }
            $amountincgst=$totrate + $gstamount;
            $count++;
        endforeach;
        //$rate=$totrate/$count;
        if($dataused['usedqnty']!=0){
            $rate=$totrate/$dataused['usedqnty'];
        }
        else{
            $rate = 0;
        }
        
        $amount=$stockqnty * $rate;
        $purchamount=$dataused['usedqnty'] * $rate;
        $usedamount=$activityusage['usedqnty'] * $rate;

        $procuredtotal = $purchamount;

        $stocktotal = $amount;

        /* Procured total and stock total end */

        /* To be procured qty start */

        if($totrepqty!=0){
            $actualqty = $activityusage['usedqnty'] / $totrepqty;
        }
        else{
            $actualqty = 0;
        }

        /* To be procured qty end */

        $purchaseqty = $dataused['usedqnty'];

        $usedqty = $activityusage['usedqnty'];

        $datarows_barchart = 'data.addRows([';

        $datarows_barchart.= " 
                            ['Estimated Qty', ".round($estimateqty,2).", 'green', ".round($estimateqty,2)."],
                            ['Actual Qty', ".round($actualqty,2).", 'green', ".round($actualqty,2)."],
                            ['Purchased Qty', ".round($purchaseqty,2).", 'green', ".round($purchaseqty,2)."],
                            ['Issued Qty', ".round($usedqty,2).", 'green', ".round($usedqty,2)."],
                            ['Stock Qty', ".round($stockqnty,2).", 'green', ".round($stockqnty,2)."], ";

        $datarows_barchart.= "]);";

        /* Dial gauge chart data start */

        //$activityrows = array();

        $activitynames = array();

        $activityids = array();

        $est_consumptn = array();

        $act_consumptn = array();

        $activitycount = 0;

        $workgroups=WorkgroupsNew::find()->where(['Project_Id' => $projectid])->andWhere(['Status' => 0])->all();
        if($workgroups):

            foreach($workgroups AS $workgroup):

                $wbsactivities=WorkgroupActivitiesNew::find()->where(['wbs_id' => $workgroup->Workgroup_Id])->andWhere(['pricing_status' =>0])->all();

                if($wbsactivities):

                    foreach($wbsactivities AS $wbsactivity):

                        $priestimateresrces=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_id' => $wbsactivity->id])->andWhere('resource_Id IN('.$resids.')')->andWhere(['pricing_status' =>0])->all();

                        $specrate1=0;

                        $specqty1=0;

                        if($priestimateresrces):
                            foreach($priestimateresrces AS $priestimateresrce):
                                $pricingestimatecal=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$priestimateresrce->activity_id])->andWhere(['pricing_status' =>0])->one();
                                if($pricingestimatecal){
                                    $specrate1=$specrate1 + $priestimateresrce['rate'];
                                    $specqty1 = $specqty1 + ($pricingestimatecal->activity_qty*$priestimateresrce['quantity']);
                                }
                            endforeach;

                            $sql3 = "SELECT SUM(resource_qty) as usedqnty  FROM issue_slips WHERE resource_id IN (".$resids.") ".$condition4." AND activity_id=".$wbsactivity->id." AND delete_status=0 ";
                            //echo $sql3;exit;
                            $command = $connection->createCommand($sql3);
                            $dataReader = $command->query();
                            $activityusage = $dataReader->read();
                            $issuedqnty= $activityusage['usedqnty'];

                            if($issuedqnty==''){
                                $issuedqnty = 0;
                            }

                            $reportedacts=ActivityReportWrkactvty::find()->where(['iow_id' => $workgroup->Workgroup_Id])->andWhere(['activity_id' => $wbsactivity->id])->andWhere(['projectid' => $projectid])->andWhere(['status' => 0])->all();

                            $totrepqty = 0;

                            if($reportedacts):

                                foreach($reportedacts AS $reportedact):
                                    $totrepqty+=$reportedact['quantity'];
                                endforeach;

                            endif;

                            array_push($activityids, $wbsactivity->id);

                            array_push($activitynames, $wbsactivity->activity_Name);

                            array_push($est_consumptn, round($specqty1));

                            if($issuedqnty!=0 && $totrepqty!=0){
                                $avg_cons = $issuedqnty/$totrepqty;
                            }
                            else{
                                $avg_cons = 0;
                            }

                            array_push($act_consumptn, round($avg_cons,1));

                            $activitycount++;

                            //$activityrows.= 'Activity Name: '.$wbsactivity->activity_Name.'<br> Estimate Qty:'.$specqty1.'<br> Issued Qty:'.$issuedqnty.'<br>Reported Qty:'.$totrepqty.'<br>';
                        endif;

                    endforeach;

                endif; 

            endforeach;

        endif;    

        /* Dial gauge chart data end */

        
        //echo $activityrows; exit;
        $arr = array('result'=>$datarows_barchart,'activityids'=> $activityids, 'activitynames'=> $activitynames, 'est_consumptn'=> $est_consumptn, 'act_consumptn'=> $act_consumptn, 'activitycount'=> $activitycount, 'error'=>'No');
        // echo $arr;
        return json_encode($arr);

    }

    /* Procurement dashboard end */


    public function actionDashcalculation()
    {
        $connection = Yii::$app->db;

        $sql="SELECT * FROM projects WHERE Status=0 AND Project_Delete_Status=0 AND favourite=1";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $projects=$dataReader->readAll();

        $deletesql="DELETE FROM dashproject";
        $command=$connection->createCommand($deletesql);
        $dataReader=$command->query();
        //$dashdata=$dataReader->delete();

        foreach($projects as $project):

            $projectid=$project['Project_Id'];

            $startdate = '';

            $enddate = '';

            $stocktotal = $this->Getstockamount($projectid);

            $contractincome=$this->closingbalance(140,$projectid,$startdate,$enddate);

            $sql="SELECT id,name FROM accounts_sub WHERE master_id='1' ORDER BY sortorder ASC,id DESC";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $subgroups=$dataReader->readAll();
            $datarows='';
            $projexptot=0;
            foreach($subgroups AS $subgroup):
                $sql="SELECT COUNT(account_id) as count FROM subgroup_accounts AS a INNER JOIN accounts_item AS b ON a.account_id=b.id WHERE a.subgrp_id='".$subgroup['id']."' AND b.account_type IN (5,8,6)";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $dataval=$dataReader->read();
                $datasubgroupows='';
                
                $sql1="SELECT a.subgrp_id,b.id,b.name FROM subgroup_accounts AS a INNER JOIN accounts_item AS b ON a.account_id=b.id WHERE a.subgrp_id='".$subgroup['id']."' AND accountschedule_id=0 AND b.account_type IN (5,8,6)";
                $command=$connection->createCommand($sql1);
                $dataReader=$command->query();
                $accounts=$dataReader->readAll();
                $grptotal=0;
                $dataaccountrows='';
                if(count($accounts)>0):
                    foreach($accounts AS $accounthead):
                        $closingbal=$this->closingbalance($accounthead['id'],$projectid,$startdate,$enddate);
                        $grptotal=$grptotal + abs($closingbal);
                    endforeach;
                $projexptot=$projexptot + $grptotal;
                endif;
            endforeach;
                    // echo $projexptot;exit;


            $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
            $command = $connection->createCommand($sqltemvals1);
            $dataReader1 = $command->query();
            $structures = $dataReader1->readAll();
            $pricesum=0;
            $estimateValue=0;
            $estimateValuelastbill=0;
            if($structures){
                foreach($structures AS $structure):
                    $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                    $command = $connection->createCommand($sql);
                    $dataReader = $command->query();
                    $wbsactivities = $dataReader->readAll();
                    if(count($wbsactivities)>0)
                    { 
                        $subtot=0;
                        foreach($wbsactivities as $key=> $wbsactivity):
                            $sqltemvals="SELECT est_resource_amount AS Price FROM estactivity_resources WHERE estactivity_id='".$wbsactivity['activity_Id']."' ";
                            $command=$connection->createCommand($sqltemvals);
                            $dataReader=$command->query();
                            $resourcesadded=$dataReader->readAll();
                            $price=0;
                            foreach($resourcesadded AS $key=>$data):
                                $price=$price+$data['Price'];
                            endforeach;
                            $pricingestimate=PricingEstimateNew::find()->where(['project_Id'=>$projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status'=>0])->one();
                             $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id'=>$projectid])->andWhere(['activity_id'=>$wbsactivity['id']])->andWhere(['pricing_status'=>0])->all();
                            if($pricingestimateres):
                                $specrate=0;
                                foreach($pricingestimateres AS $pricingestimatere):
                                    $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                                endforeach;
                                //$specrate=$pricingestimate['specific_rate'];
                            else:
                                $specrate=$price;
                                //$specrate=0;
                            endif;

                            $sqlreprt="SELECT sum(quantity) as qty FROM activity_report_wrkactvty WHERE activity_id='".$wbsactivity['id']."' ";
                            $command=$connection->createCommand($sqlreprt);
                            $dataReader=$command->query();
                            $reprtqty=$dataReader->read();

                            if($wbsactivity['boq_slno']!=0 && $wbsactivity['boq_slno']!=''){
                                $boq=Boq::findOne($wbsactivity['boq_slno']);

                                if($boq){
                                    $boqrate = $boq->rate;
                                }
                                else{
                                    $boqrate = 0;
                                }
                                
                            }
                            else{
                                $boqrate = 0;
                            }

                            $estamt = $specrate * $reprtqty['qty'];

                            $estVal = $boqrate * $reprtqty['qty'];

                            $pricesum= $pricesum + $estamt;

                            $estimateValue = $estimateValue + number_format($estVal,2);

                            $lastclient_bill = ClientBill::find()->where(['projectid' => $projectid])->andWhere(['status' => 0])->orderBy(['raise_date' => SORT_DESC])->one();

                            if($lastclient_bill){

                                $sqlreprt="SELECT sum(quantity) as qty FROM activity_report_wrkactvty WHERE activity_id='".$wbsactivity['id']."' AND date<='".$lastclient_bill->raise_date."' ";
                                $command=$connection->createCommand($sqlreprt);
                                $dataReader=$command->query();
                                $reprtqty=$dataReader->read();

                                if($wbsactivity['boq_slno']!=0 && $wbsactivity['boq_slno']!=''){
                                    $boq=Boq::findOne($wbsactivity['boq_slno']);

                                    if($boq){
                                        $boqrate = $boq->rate;
                                    }
                                    else{
                                        $boqrate = 0;
                                    }
                                    
                                }
                                else{
                                    $boqrate = 0;
                                }

                                $estVal_lastbill = $boqrate * $reprtqty['qty'];

                            }
                            else{

                                $estVal_lastbill = 0;

                            }


                            $estimateValuelastbill = $estimateValuelastbill + number_format($estVal_lastbill,2);


                        endforeach;
                    }
                 endforeach;

            }

            $client_bills = ClientBill::find()->where(['projectid' => $projectid])->andWhere(['status' => 0])->all();

            $cum_amount_clientbill = 0;

            if(!empty($client_bills)):

                foreach($client_bills as $key => $client_bill):

                    $boq=Boq::findOne($client_bill->boq_id);

                    if($boq){

                        $cum_amount_clientbill+= $boq->rate * $client_bill->current_qty;

                    }

                endforeach;    

            endif;


            $insertsql = "INSERT INTO dashproject (projectid,expenditure,stockvalue,estimated,estimatedValue,contractIncome,cum_amount_clientbill,est_value_clientbill) VALUES('" . $projectid . "','" . $projexptot . "','" . $stocktotal . "','" . $pricesum . "','" . $estimateValue . "','" . $contractincome . "','" . $cum_amount_clientbill . "','" . $estimateValuelastbill . "')";
            //echo $sql;exit;
            $command = $connection->createCommand($insertsql);
            $dataReader = $command->query();

        endforeach;

    }

    public function actionDashaccountcalculation()
    {
        $connection = Yii::$app->db;

        //set_time_limit(500);

        //$sql="SELECT * FROM projects WHERE Project_Id=56 AND Status=0 AND Project_Delete_Status=0 AND favourite=1";
        $sql="SELECT * FROM projects WHERE Status=0 AND Project_Delete_Status=0 AND favourite=1";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $projects=$dataReader->readAll();

        $deletesql="DELETE FROM dashboardaccount";
        $command=$connection->createCommand($deletesql);
        $dataReader=$command->query();
        //$dashdata=$dataReader->delete();

        foreach($projects as $project):

            $projectid=$project['Project_Id'];

            $startdate = '';

            $enddate = '';

            $sql="SELECT id,name FROM accounts_sub WHERE master_id='1' ORDER BY sortorder ASC,id DESC";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $subgroups=$dataReader->readAll();

            foreach($subgroups as $subgroup):

                $subgrpid = $subgroup['id'];

                $sql1="SELECT a.subgrp_id,b.id,b.name FROM subgroup_accounts AS a INNER JOIN accounts_item AS b ON a.account_id=b.id WHERE a.subgrp_id='".$subgrpid."' AND a.accountschedule_id=0 AND b.account_type IN (5,8,6)";
                $command=$connection->createCommand($sql1);
                $dataReader=$command->query();
                $accounts=$dataReader->readAll();
                if($accounts):
                    foreach($accounts AS $key=>$accounthead):
                        $closingbal=$this->closingbalance($accounthead['id'],$projectid,$startdate,$enddate);
                        $resgrpid=AccountsItem::findOne($accounthead['id']);
                        //$sql = "SELECT *  FROM `resources` WHERE `Resource_group_Id` = ".$resgrpid->resource_group." AND pricing_status=0";
                        $sql = "SELECT *  FROM `resources` WHERE `Resource_Acc_Id` = ".$accounthead['id']." AND pricing_status=0";
                        $command=$connection->createCommand($sql);
                        $dataReader=$command->query();
                        $resdataitems=$dataReader->readAll();
                        $resids='';
                        if ($resdataitems) {
                            foreach ($resdataitems as $key1 => $resdataitem) {
                                if ($key1==0) {
                                    $resids.=$resdataitem['Resource_Id'];
                                }
                                else {
                                    $resids.=",".$resdataitem['Resource_Id'];
                                }
                            }
                            $stocktotal= $this->Getresstockamount($projectid,$resids);
                        }
                        else {
                            $stocktotal= 0;
                        }
                        if($subgrpid==57):
                            $netamount=abs($closingbal) - $stocktotal;
                        else:
                            $netamount=abs($closingbal);
                        endif;
                        
                        //$datarows.="<tr><td colspan='1'><span style='cursor: pointer;' class='expensesubacnthd' data-id='".$accounthead['id']."'>".$accounthead['name']."</span></td><td style='text-align: right;'>".number_format((float)abs($netamount), 2)."</td></tr>";
                        //$grpper=(abs($netamount) / $totalicome)*100;

                        $res_amt=0;

                        if($resids){

                            $pricingestresources=PricingEstimateResourcesNew::find()->where(['project_Id'=>$projectid])->andWhere('resource_Id IN('.$resids.')')->andWhere(['pricing_status'=>0])->all();

                            $resnamearr=array();

                            foreach($pricingestresources as $estimateresource):

                                $sqlreprt="SELECT sum(quantity) as qty FROM activity_report_wrkactvty WHERE activity_id='".$estimateresource->activity_id."'";
                                $command=$connection->createCommand($sqlreprt);
                                $dataReader=$command->query();
                                $reprtqty=$dataReader->read();

                                $res_amt= $res_amt + ($estimateresource['rate'] * $estimateresource['quantity'] * $reprtqty['qty']);

                                $wbsactivity['id'] = $estimateresource->activity_id;

                                $resource=Resources::findOne($estimateresource->resource_Id);
                                $resources=Resources::find()->where(['LIKE','Name',$resource->Name])->andWhere(['Resource_Acc_Id'=>$accounthead['id']])->all();
                                $resourceids='';
                                foreach($resources AS $key=>$resourceitem):
                                    if($key==0):
                                        $resourceids.=$resourceitem->Resource_Id;
                                    else:
                                        $resourceids.=','.$resourceitem->Resource_Id;
                                    endif;
                                endforeach;

                                $pricingestimateresources=PricingEstimateResourcesNew::find()->where(['project_Id'=>$projectid])->andWhere('resource_Id IN('.$resourceids.')')->andWhere(['pricing_status'=>0])->all();

                                $estrate = 0;

                                $estratecount = 0;

                                $estconsumptn = 0;

                                foreach($pricingestimateresources AS $key=>$pricingestimateresource):


                                    $estrate+= $pricingestimateresource->rate;

                                    $estconsumptn+= $pricingestimateresource->quantity;

                                    $estratecount++;


                                endforeach;

                                if($estrate!=0 && $estratecount!=0){

                                    $est_avg_rate = $estrate/$estratecount;

                                }
                                else{

                                    $est_avg_rate = 0;

                                }


                                if($estconsumptn!=0 && $estratecount!=0){

                                    $est_avg_consum = $estconsumptn/$estratecount;

                                }
                                else{

                                    $est_avg_consum = 0;

                                }

                                $actual_proj=$projectid;

                                if($estimateresource->resourcetype_Id==16 or $estimateresource->resourcetype_Id==20 or $estimateresource->resourcetype_Id==21 or $estimateresource->resourcetype_Id==23)
                                {
                                    
                                    $resdata=$this->materialsamount($wbsactivity['id'],$estimateresource->resource_Id,$actual_proj);  
                                    $type="materialsamount";
                                }
                                else if($estimateresource->resourcetype_Id==26)
                                {    
                                    $resdata=$this->equipmentamount($wbsactivity['id'],$estimateresource->resource_Id,$actual_proj);
                                    //$amount=$resdata['issuqty'] * $resdata['avg_rate'];
                                    //echo $amount; exit;
                                    $type="equipmentamount";
                                } 
                                else if($estimateresource->resourcetype_Id==19 || $estimateresource->resourcetype_Id==24)
                                { 
                                    $resdata=$this->subcontractoramount($wbsactivity['id'],$estimateresource->resource_Id,$actual_proj);
                                    $type="subcontractoramount";
                                }
                                else if($estimateresource->resourcetype_Id==25 or $estimateresource->resourcetype_Id==50)
                                {
                                        
                                    $resdata=$this->supportamount($estimateresource->resource_Id,$actual_proj);
                                    $type="supportamount";
                                }
                                else if($estimateresource->resourcetype_Id==33)
                                {
                                        
                                    $resdata=$this->directworkersamount($wbsactivity['id'],$estimateresource->resource_Id,$actual_proj);
                                    $type="directworkersamount";
                                }
                                else if($estimateresource->resourcetype_Id==37)
                                {
                                        
                                    $resdata=$this->investmentamount($estimateresource->resource_Id,$actual_proj);
                                    $type="investmentamount";
                                }
                                else {
                                    $resdata=array();
                                    $type="";
                                }
                                
                                if (!in_array($resource->Name, $resnamearr))
                                {
                                    array_push($resnamearr, $resource->Name);
                                    if(isset($resdata['issuqty'])){
                                        if($reprtqty['qty']==0){
                                            $reprtqty['qty'] = 1;
                                        }
                                        $avg_consum=$resdata['issuqty'] / $reprtqty['qty'];
                                    }
                                    else{
                                        $avg_consum= 0;
                                        $resdata['issuqty'] = 0;
                                    }

                                    if(isset($resdata['avg_rate']) && $resdata['avg_rate']!=0){

                                        $insertsql = "INSERT INTO dashboardaccount_res (projectid,account_item,resource,avg_rate,est_avg_rate,avg_consumptn,est_avg_consumptn,issued_qty,budgeted_qty) VALUES('" . $projectid . "','" . $accounthead['name'] . "','" . $resource->Name . "','" . $resdata['avg_rate'] . "','" . $est_avg_rate . "','" . $avg_consum . "','" . $est_avg_consum . "','" . $resdata['issuqty'] . "','" . $estconsumptn . "')";
                                        //echo $sql;exit;
                                        $command = $connection->createCommand($insertsql);
                                        $dataReader = $command->query();

                                    }
                                }

                            endforeach;   

                        } 

                        if($netamount!=0 && $res_amt!=0):

                            $insertsql = "INSERT INTO dashboardaccount (projectid,account_sub,account_item,account_item_amount,est_res_amt) VALUES('" . $projectid . "','" . $subgroup['name'] . "','" . $accounthead['name'] . "','" . abs($netamount) . "','" . $res_amt . "')";
                            //echo $sql;exit;
                            $command = $connection->createCommand($insertsql);
                            $dataReader = $command->query();

                            //$count++;
                        endif;
                        //$grptotal=$grptotal + abs($netamount);
                    endforeach;
                endif;


            endforeach;


        endforeach;

    }

    public function Getresstockamount($projectid,$resourceid)
    {
        //echo "resid: ".$resourceid;exit;
        $connection = Yii::$app->db;
        $condition1 =' AND b.project_id='.$projectid.'';
        $sql1="select a.resource_id,b.invoice_date,b.vendor,a.resource_qty,a.invoice_id,a.order_id from invoice_resources as a inner join invoice as b on a.invoice_id=b.invoice_id where b.status!=3 AND a.resource_id IN (".$resourceid.") ".$condition1." GROUP BY a.resource_id";
        //echo $sql1;exit;
        $command = $connection->createCommand($sql1);
        $dataReader = $command->query();
        $dataresources = $dataReader->readAll();
        $totrate=0;
        $count=0;
        if(count($dataresources)>0):
            foreach($dataresources AS $key=>$dataresource):
                $sql2 = "SELECT SUM(a.resource_qty) as usedqnty FROM invoice_resources as a INNER JOIN invoice as b on a.invoice_id=b.invoice_id WHERE a.resource_id=".$dataresource['resource_id']." AND b.status!=3 ".$condition1." ";
                //echo $sql2;exit;
                $command = $connection->createCommand($sql2);
                $dataReader = $command->query();
                $dataused = $dataReader->read();
                //echo $dataused['usedqnty'];exit;
                $sql3 = "SELECT SUM(resource_qty) as usedqnty  FROM issue_slips WHERE resource_id=".$dataresource['resource_id']." AND delete_status=0 AND project_id=".$projectid." ";
                //echo $sql3;exit;
                $command = $connection->createCommand($sql3);
                $dataReader = $command->query();
                $activityusage = $dataReader->read();
                //echo $activityusage['usedqnty'];exit;
                $stockqnty=$dataused['usedqnty'] - $activityusage['usedqnty'];
                //echo $stockqnty;exit;
                $sql1="select a.resource_id,b.invoice_date,b.vendor,a.resource_qty,a.invoice_id,a.order_id from invoice_resources as a inner join invoice as b on a.invoice_id=b.invoice_id where b.status!=3 AND a.resource_id=".$dataresource['resource_id']." ".$condition1."";
                //echo $sql1;exit;
                $command = $connection->createCommand($sql1);
                $dataReader = $command->query();
                $itemsresources = $dataReader->readAll();
                //echo count($itemsresources);exit;
                $amount = 0;
                foreach ($itemsresources as $key1 => $value) {
                    $orderdetails=OrderedResource::find()->where(['order_id' => $value['order_id']])->andWhere(['resource_id' => $value['resource_id']])->one();
                    $amount += $orderdetails['rate'] * $value['resource_qty'];
                    //echo $orderdetails['rate'] * $value['resource_qty']."<br>";
                }
                $avgrate = $amount / $dataused['usedqnty'];
                $stockamount = $stockqnty * $avgrate;
                //echo $stockamount;exit;
                $totrate+=$stockamount;
                //echo $orderdetails['rate'];exit;
                //$totrate+=$orderdetails['rate'] * $dataresource['resource_qty'];
            endforeach;
        endif;
        //echo "totrate".$totrate;exit;
        //$rate=$totrate/$dataused['usedqnty'];
        //echo $rate;exit;
        if (is_nan($totrate)) {
            $ratelast=0;
        }
        else {
            $ratelast=$totrate;
        }
        //$amount=$stockqnty * $ratelast;
        //echo "ratelast: ".$ratelast;exit;
        return $ratelast;
    }

    public function actionDashcalculation123()
    {
        $connection = Yii::$app->db;

        $sql="SELECT * FROM projects WHERE Project_Id=63";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $projects=$dataReader->readAll();

        //$deletesql="DELETE FROM dashproject";
        //$command=$connection->createCommand($deletesql);
        //$dataReader=$command->query();
        //$dashdata=$dataReader->delete();

        foreach($projects as $project):

            $projectid=$project['Project_Id'];

            $startdate = '';

            $enddate = '';

            $stocktotal = $this->Getstockamount($projectid);

            $contractincome=$this->closingbalance(140,$projectid,$startdate,$enddate);

            $sql="SELECT id,name FROM accounts_sub WHERE master_id='1' ORDER BY sortorder ASC,id DESC";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $subgroups=$dataReader->readAll();
            $datarows='';
            $projexptot=0;
            foreach($subgroups AS $subgroup):
                $sql="SELECT COUNT(account_id) as count FROM subgroup_accounts AS a INNER JOIN accounts_item AS b ON a.account_id=b.id WHERE a.subgrp_id='".$subgroup['id']."' AND b.account_type IN (5,8,6)";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $dataval=$dataReader->read();
                $datasubgroupows='';
                
                $sql1="SELECT a.subgrp_id,b.id,b.name FROM subgroup_accounts AS a INNER JOIN accounts_item AS b ON a.account_id=b.id WHERE a.subgrp_id='".$subgroup['id']."' AND accountschedule_id=0 AND b.account_type IN (5,8,6)";
                $command=$connection->createCommand($sql1);
                $dataReader=$command->query();
                $accounts=$dataReader->readAll();
                $grptotal=0;
                $dataaccountrows='';
                if(count($accounts)>0):
                    foreach($accounts AS $accounthead):
                        $closingbal=$this->closingbalance($accounthead['id'],$projectid,$startdate,$enddate);
                        $grptotal=$grptotal + abs($closingbal);
                    endforeach;
                $projexptot=$projexptot + $grptotal;
                endif;
            endforeach;
                    // echo $projexptot;exit;


            $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
            $command = $connection->createCommand($sqltemvals1);
            $dataReader1 = $command->query();
            $structures = $dataReader1->readAll();
            $pricesum=0;
            $estimateValue=0;
            $estimateValuelastbill=0;
            if($structures){
                foreach($structures AS $structure):
                    $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                    $command = $connection->createCommand($sql);
                    $dataReader = $command->query();
                    $wbsactivities = $dataReader->readAll();
                    if(count($wbsactivities)>0)
                    { 
                        $subtot=0;
                        foreach($wbsactivities as $key=> $wbsactivity):
                            $sqltemvals="SELECT est_resource_amount AS Price FROM estactivity_resources WHERE estactivity_id='".$wbsactivity['activity_Id']."' ";
                            $command=$connection->createCommand($sqltemvals);
                            $dataReader=$command->query();
                            $resourcesadded=$dataReader->readAll();
                            $price=0;
                            foreach($resourcesadded AS $key=>$data):
                                $price=$price+$data['Price'];
                            endforeach;
                            $pricingestimate=PricingEstimateNew::find()->where(['project_Id'=>$projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status'=>0])->one();
                             $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id'=>$projectid])->andWhere(['activity_id'=>$wbsactivity['id']])->andWhere(['pricing_status'=>0])->all();
                            if($pricingestimateres):
                                $specrate=0;
                                foreach($pricingestimateres AS $pricingestimatere):
                                    $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                                endforeach;
                                //$specrate=$pricingestimate['specific_rate'];
                            else:
                                $specrate=$price;
                                //$specrate=0;
                            endif;

                            $sqlreprt="SELECT sum(quantity) as qty FROM activity_report_wrkactvty WHERE activity_id='".$wbsactivity['id']."' ";
                            $command=$connection->createCommand($sqlreprt);
                            $dataReader=$command->query();
                            $reprtqty=$dataReader->read();

                            if($wbsactivity['boq_slno']!=0 && $wbsactivity['boq_slno']!=''){
                                $boq=Boq::findOne($wbsactivity['boq_slno']);

                                if($boq){
                                    $boqrate = $boq->rate;
                                }
                                else{
                                    $boqrate = 0;
                                }
                                
                            }
                            else{
                                $boqrate = 0;
                            }

                            $estamt = $specrate * $reprtqty['qty'];

                            $estVal = $boqrate * $reprtqty['qty'];

                            $pricesum= $pricesum + $estamt;

                            $estimateValue = $estimateValue + number_format($estVal,2);

                            $lastclient_bill = ClientBill::find()->where(['projectid' => $projectid])->andWhere(['status' => 0])->orderBy(['raise_date' => SORT_DESC])->one();

                            $sqlreprt="SELECT sum(quantity) as qty FROM activity_report_wrkactvty WHERE activity_id='".$wbsactivity['id']."' AND date<='".$lastclient_bill->raise_date."' ";
                            $command=$connection->createCommand($sqlreprt);
                            $dataReader=$command->query();
                            $reprtqty=$dataReader->read();

                            if($wbsactivity['boq_slno']!=0 && $wbsactivity['boq_slno']!=''){
                                $boq=Boq::findOne($wbsactivity['boq_slno']);

                                if($boq){
                                    $boqrate = $boq->rate;
                                }
                                else{
                                    $boqrate = 0;
                                }
                                
                            }
                            else{
                                $boqrate = 0;
                            }

                            $estVal_lastbill = $boqrate * $reprtqty['qty'];

                            $estimateValuelastbill = $estimateValuelastbill + number_format($estVal_lastbill,2);

                        endforeach;
                    }
                 endforeach;

            }

            $client_bills = ClientBill::find()->where(['projectid' => $projectid])->andWhere(['status' => 0])->all();

            $cum_amount_clientbill = 0;

            if(!empty($client_bills)):

                foreach($client_bills as $key => $client_bill):

                    $boq=Boq::findOne($client_bill->boq_id);

                    $cum_amount_clientbill+= $boq->rate * $client_bill->current_qty;

                endforeach;    

            endif;

        endforeach;

        echo $estimateValuelastbill; exit;

    }

    function closingbalance($acntid,$projid,$startdate,$enddate)
    {
        $connection = Yii::$app->db;
        $initialdate='2013-04-01';
        if($startdate!='' && $enddate!=''):
            $condition="AND  date BETWEEN '$startdate' AND '$enddate'";
        else:
            $condition="AND date >= '$initialdate'";
        endif;
        if($projid!=''):
            if($projid==64){
                $condition1=" AND (project_id='".$projid."' AND place='".$projid."')";
                $condition2=" AND (place='".$projid."' AND project_id='".$projid."')";
            }
            else{
                $condition1=" AND (project_id='".$projid."' OR place='".$projid."')";
                $condition2=" AND (place='".$projid."' OR project_id='".$projid."')";
            }
        else:
            $condition1="";
            $condition2="";
        endif;
        //echo $condition1;exit;
        $sql="(SELECT amount,type,creditacnt,bank_id,contra,account_id FROM voucher WHERE (account_id='".$acntid."' OR creditacnt='".$acntid."') $condition1 $condition)
              UNION ALL
              (SELECT amount,type,creditacnt,bank_id,contra,debitacnt AS account_id FROM journalvoucher WHERE (debitacnt='".$acntid."' OR creditacnt='".$acntid."') $condition2 $condition)";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        //print_r($dataProvider);
        $debitamount=0;
        $creditamount=0;
        $credittotal=0;
        $debittotal=0;
        foreach($dataProvider AS $key=>$data):
            if($data['contra']==0):
                if($data['creditacnt']==$acntid):
                    $creditamount=number_format((float)$data['amount'], 2);
                    $debitamount='';
                    $credittotal=$credittotal + $data['amount'];
                elseif($data['account_id']==$acntid):
                    $debitamount=number_format((float)$data['amount'], 2);
                    $creditamount='';
                    //echo $acntid.','.$data['amount'].','.$projid.'<br>';
                    $debittotal=$debittotal + (float)$data['amount'];
                endif;
            else:
                if($data['type']=='Payment' && $data['creditacnt']==$acntid ):
                    $creditamount=number_format((float)$data['amount'], 2);
                    $debitamount='';
                    $credittotal=$credittotal + $data['amount'];
                elseif($data['type']=='Receipt' && $data['account_id']==$acntid):
                    $debitamount=number_format((float)$data['amount'], 2);
                    $creditamount='';
                    $debittotal=$debittotal + $data['amount'];

                endif;
            endif;
            //echo $creditamount.' - '.$debitamount.' - '.$credittotal.' - '.$debittotal.'<br>';
            //echo $datarows;exit;
        endforeach;
        $acountbal=$credittotal - $debittotal;
        return $acountbal;
        //$closingbal=$this->getbalance($openbalance,$acountbal);
    }

    function Getstockamount($projectid)
    {
        $connection = Yii::$app->db;
        $sql="select count(a.resource_id) as rescount,a.resource_id, a.resource_qty,a.order_id from invoice_resources AS a INNER JOIN resources AS b ON a.resource_id=b.Resource_Id WHERE b.ResourceType_Id=16 AND a.resource_qty!=0 group by a.resource_id ";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $dataProvider = $dataReader->readAll();
        $condition='';
        $condition1='';
        $condition2='';
        $condition3='';
        if($projectid!=''):
            $condition.='AND a.project_id='.$projectid.' ';
            $condition1.=' AND b.project_id='.$projectid.'';
            $condition2.=' AND a.project_id='.$projectid.'';
            $condition3.='?project_id='.$projectid.'';
        endif;
        $sql1="select a.project_id,a.invoice_id,b.resource_id,c.Resource_group_Id from invoice AS a INNER JOIN invoice_resources AS b ON a.invoice_id=b.invoice_id INNER JOIN resources AS c ON b.resource_id=c.resource_id WHERE a.status!=3 AND c.ResourceType_Id=16 ".$condition." group by c.Resource_group_Id ORDER BY c.Resource_group_Id ASC";
        //echo $sql1;exit;
        $command = $connection->createCommand($sql1);
        $dataReader = $command->query();
        $dataProvider = $dataReader->readAll();
        $totalamount=0;
        if($dataProvider):
            foreach($dataProvider AS $key=>$data):
                $ResgroupName=ResourceGroup::findOne($data['Resource_group_Id'])->Resource_group_Name;

                $sql1="select a.project_id,a.invoice_id,b.resource_id,c.Name,c.Resource_group_Id from invoice AS a INNER JOIN invoice_resources AS b ON a.invoice_id=b.invoice_id INNER JOIN resources AS c ON b.resource_id=c.resource_id WHERE a.status!=3 AND c.ResourceType_Id=16 AND c.Resource_group_Id=".$data['Resource_group_Id']." ".$condition." group by c.Name ORDER BY b.resource_id ASC";
                //echo $sql1;exit;
                $command = $connection->createCommand($sql1);
                $dataReader = $command->query();
                $childresitems = $dataReader->readAll();
                $stocktot=0;
                $stockamounttot=0;
                foreach($childresitems AS $key1=>$data1):
                    $resourcename=Resources::findOne($data1['resource_id'])->Name;
                    $resourceunit=Resources::findOne($data1['resource_id'])->Unit;
                    $sql1="select a.project_id,a.invoice_id,b.resource_id,c.Resource_group_Id from invoice AS a INNER JOIN invoice_resources AS b ON a.invoice_id=b.invoice_id INNER JOIN resources AS c ON b.resource_id=c.resource_id WHERE a.status!=3 AND c.ResourceType_Id=16 AND c.Name LIKE '".$data1['Name']."' AND c.Resource_group_Id=".$data['Resource_group_Id']." ".$condition." group by b.resource_id ORDER BY b.resource_id ASC";
                    //echo $sql1;exit;
                    $command = $connection->createCommand($sql1);
                    $dataReader = $command->query();
                    $stockresitems = $dataReader->readAll();
                    //echo count($stockresitems);exit;
                    $stresids='';
                    if($stockresitems):
                        foreach ($stockresitems as $key2 => $stockresitem) {
                            if($key2==0):
                                $stresids.=$stockresitem['resource_id'];
                            else:
                                $stresids.=",".$stockresitem['resource_id'];
                            endif;
                        }
                    endif;
                    //echo $stresids;exit;
                    //$sql2 = "SELECT SUM(resource_qty) as usedqnty  FROM invoice_resources WHERE resource_id =".$data['resource_id']." ";
                    $sql2 = "SELECT SUM(a.resource_qty) as usedqnty FROM invoice_resources as a INNER JOIN invoice as b on a.invoice_id=b.invoice_id WHERE a.resource_id IN (".$stresids.") AND b.status!=3 ".$condition1." ";
                    //echo $sql2;exit;
                    $command = $connection->createCommand($sql2);
                    $dataReader = $command->query();
                    $dataused = $dataReader->read();
                    //$sql3 = "SELECT SUM(quantity_used) as usedqnty  FROM activity_report_resources WHERE resource_id =".$data1['resource_id']." ".$condition2." ";
                    //$sql3 = "SELECT SUM(a.quantity_used) as usedqnty  FROM activity_report_resources AS a INNER JOIN workgroup_activities_new AS b ON a.activityid=b.id WHERE a.resource_id =".$data1['resource_id']." AND b.pricing_status=0 ".$condition2." ";
                    $sql3 = "SELECT SUM(resource_qty) as usedqnty  FROM issue_slips WHERE project_id=".$projectid." AND resource_id IN (".$stresids.") AND delete_status=0 ";
                    //echo $sql3;exit;
                    $command = $connection->createCommand($sql3);
                    $dataReader = $command->query();
                    $activityusage = $dataReader->read();
                    $stockqnty=$dataused['usedqnty'] - $activityusage['usedqnty'];
                    //echo $stockqnty;exit;
                    //$sql1="select a.unit,a.rate from ordered_resource AS a INNER JOIN orders AS b ON a.order_id=b.order_id WHERE resource_id=".$data1['resource_id']." ".$condition1." AND qnty!=0";
                    $sql1="select b.invoice_date,b.vendor,a.resource_qty,a.invoice_id,a.order_id from invoice_resources as a inner join invoice as b on a.invoice_id=b.invoice_id where b.status!=3 AND a.resource_id IN (".$stresids.") ".$condition1." ";
                    //echo $sql1;exit;
                    $command = $connection->createCommand($sql1);
                    $dataReader = $command->query();
                    $dataresources = $dataReader->readAll();
                    $totrate=0;
                    $amountincgst=0;
                    $count=0;
                    foreach($dataresources AS $dataresource):
                        //$totrate+=$dataresource['rate'];
                        $orderdetails=OrderedResource::find()->where(['order_id' => $dataresource['order_id']])->andWhere('resource_id IN('.$stresids.')')->one();
                        $totrate+=$orderdetails['rate'] * $dataresource['resource_qty'];
                        if($orderdetails['cgst']!=0):
                            $gstamount=($totrate * ($orderdetails['cgst'] + $orderdetails['sgst']))/100;
                        else:
                            $gstamount=($totrate * $orderdetails['igst'])/100;
                        endif;
                        $amountincgst=$totrate + $gstamount;
                        $count++;
                    endforeach;
                    //$rate=$totrate/$count;
                    $rate=$totrate/$dataused['usedqnty'];
                    $amount=$stockqnty * $rate;
                    $stocktot+=$stockqnty;
                    $stockamounttot+=$amount;
                endforeach;
                $totalamount+= $stockamounttot;
            endforeach;
        endif;
        //echo $totalamount;exit;
        return $totalamount;
    }


    /* Balance sheet report data inserting in to table for tableau start */

    public function actionDashbalancesheetreport()
    {
        $connection = \Yii::$app->db; 

        $enddate=date('Y-m-d');

        $ar=array("enddate"=>$enddate);
        $str=http_build_query($ar);
        $print="<a href='".Yii::$app->request->baseUrl."/FinanceRequests/printbalancesheet?".$str."' target='_blank'><i class='glyphicon glyphicon-print'></i>Print</a>";
        $peinfo="<p>Balance Sheet as of ".$enddate."</p>";
        $sql="SELECT id,name FROM accounts_sub WHERE master_id='6' ORDER BY sortorder ASC,id DESC";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $subgroups=$dataReader->readAll();
        $liabilities=array_slice($subgroups, 0, 3);
        $assets=array_slice($subgroups, 3, 4);

        $datarows='';
        $subgrouptotal=0;
        $liabilitiesrow="<tr><td colspan='4'><span style='font-weight: bold;'>A. EQUITY AND LIABILITIES</span></td></tr>";
        foreach($liabilities AS $key=>$liability):
            if($key==1):
                $liabilitiesrow.="<tr><td colspan='4'><span style='font-weight: bold;'>Share application money pending allotment</span></td></tr>";
            endif;
            $liabilitiesrow.="<tr><td colspan='4'><span style='font-weight: bold;'>".($key + 1).".".$liability['name']."</span></td></tr>";
            $sql2="SELECT item_id,acntsubgrp_id,itemname,noteno FROM bsitems WHERE acntsubgrp_id=".$liability['id']." ";
            $command=$connection->createCommand($sql2);
            $dataReader=$command->query();
            $bsitems=$dataReader->readAll();
            $bsitemsrow="";
            $bsitemtotal=0;
            if(!empty($bsitems)):
                foreach($bsitems AS $key=>$bsitem):
                    $alphabets=array(0=>'a',1=>'b',2=>'c',3=>'d',4=>'e',5=>'f');
                    $sql1="SELECT a.subgrp_id,b.id,b.name FROM subgroup_accounts AS a INNER JOIN accounts_item AS b ON a.account_id=b.id WHERE a.subgrp_id='".$liability['id']."' AND a.bsitem_id='".$bsitem['item_id']."' AND b.account_type IN (7)";
                    //echo $sql1;exit;
                    $command=$connection->createCommand($sql1);
                    $dataReader=$command->query();
                    $accounts=$dataReader->readAll();
                    $grptotal=0;
                    $dataaccountrows='';
                    if(count($accounts)>0):
                        foreach($accounts AS $accounthead):

                            $accnt_head=AccountsItem::findOne($accounthead['id']);

                            $accnt_sub=AccountsSub::findOne($accounthead['subgrp_id']);

                            $closingbal=$this->balancesheetclosing(7,$accounthead['id'],$liability['id'],$bsitem['item_id']);
                            $grptotal=$grptotal + abs($closingbal);
                        endforeach;
                    endif;
                    $bsitemsrow.="<tr><td></td><td>(".$alphabets[$key].").".$bsitem['itemname']."</td><td>".$bsitem['noteno']."</td><td style='text-align: right;'>".number_format((float)abs($grptotal), 2)."</td></tr>";
                    $bsitemtotal=$bsitemtotal + $grptotal;
                endforeach;
                $liabilitiesrow.=$bsitemsrow;
            endif;
            $subgrouptotal=$subgrouptotal + $bsitemtotal;
        endforeach;
        $liabilitiesrow.="<tr><td></td><td style='text-align: ;left: ;font-weight: bold;'>Total</td><td></td><td style='text-align: right;'>".number_format((float)abs($subgrouptotal), 2)."</td></tr>";
        $assetsrow="<tr><td colspan='4'><span style='font-weight: bold;'>B. ASSETS</span></td></tr>";
        $assettotal=0;
        foreach($assets AS $key=>$asset):
            $assetsrow.="<tr><td colspan='4'><span style='font-weight: bold;'>".($key + 1).".".$asset['name']."</span></td></tr>";
            $sql2="SELECT item_id,acntsubgrp_id,itemname,noteno FROM bsitems WHERE acntsubgrp_id=".$asset['id']." ";
            $command=$connection->createCommand($sql2);
            $dataReader=$command->query();
            $bsitems=$dataReader->readAll();
            $bsitemsrow="";
            if(count($bsitems)>0):
                $bsitemtotal=0;
                foreach($bsitems AS $key=>$bsitem):
                    $alphabets=array(0=>'a',1=>'b',2=>'c',3=>'d',4=>'e',5=>'f');
                    $sql1="SELECT a.subgrp_id,b.id,b.name FROM subgroup_accounts AS a INNER JOIN accounts_item AS b ON a.account_id=b.id WHERE a.subgrp_id='".$asset['id']."' AND a.bsitem_id='".$bsitem['item_id']."' AND b.account_type IN (6)";
                    //echo $sql1;exit;
                    $command=$connection->createCommand($sql1);
                    $dataReader=$command->query();
                    $accounts=$dataReader->readAll();
                    $grptotal=0;
                    $dataaccountrows='';
                    if(count($accounts)>0):
                        foreach($accounts AS $accounthead):

                            $accnt_head=AccountsItem::findOne($accounthead['id']);

                            $accnt_sub=AccountsSub::findOne($accounthead['subgrp_id']);

                            $closingbal=$this->balancesheetclosing(6,$accounthead['id'],$asset['id'],$bsitem['item_id']);
                            $grptotal=$grptotal + abs($closingbal);
                        endforeach;
                    endif;
                    $bsitemsrow.="<tr><td></td><td>(".$alphabets[$key].").".$bsitem['itemname']."</td><td>".$bsitem['noteno']."</td><td style='text-align: right;'>".number_format((float)abs($grptotal), 2)."</td></tr>";
                    $bsitemtotal=$bsitemtotal + $grptotal;
                endforeach;
                $assetsrow.=$bsitemsrow;
            endif;
            $assettotal=$assettotal + $bsitemtotal;
        endforeach;
        $assetsrow.="<tr><td></td><td style='text-align: left;font-weight: bold;'>Total</td><td></td><td style='text-align: right;'>".number_format((float)abs($assettotal), 2)."</td></tr>";
        $datarows.=$liabilitiesrow.$assetsrow;

        echo $datarows; exit;
        $arr = array('result'=>$datarows,'peinfo' => $peinfo,'print'=>$print,'error'=>'No');
        return json_encode($arr);  
    }

    function balancesheetclosing($acnttypeid,$acntid,$acntsubid,$bsitemid)
    {
        //$connection = CActiveRecord::getDbConnection();
        $connection = \Yii::$app->db;
        $month=date('m');
        if($month=='01' || $month=='02' || $month=='03'):
            $initialdate=date("Y",strtotime("-1 year")).'-04-01';
        else:
            $initialdate=date('Y').'-04-01';
        endif;
        $sql="(SELECT amount,type,creditacnt,bank_id,contra,date,account_id FROM voucher WHERE (account_id='".$acntid."' OR creditacnt='".$acntid."'))
              UNION ALL
              (SELECT amount,type,creditacnt,bank_id,contra,date,debitacnt AS account_id FROM journalvoucher WHERE (debitacnt='".$acntid."' OR creditacnt='".$acntid."'))";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        //print_r($dataProvider);
        $debitamount=0;
        $credittotal=0;
        $debittotal=0;

        $accnttype=AccountTypes::findOne($acnttypeid);

        $accnthead=AccountsItem::findOne($acntid);

        $accntsub=AccountsSub::findOne($acntsubid);

        $bsitem=Bsitems::findOne($bsitemid);
        
        if($dataProvider){

            foreach($dataProvider AS $key=>$data):
                if($data['contra']==0):
                    if($data['creditacnt']==$acntid):
                        $creditamount=number_format((float)$data['amount'], 2);
                        $debitamount='';
                        $credittotal=$credittotal + $data['amount'];
                        $cred_amt = $data['amount'];
                        $debit_amt = 0;
                        $date = $data['date'];
                    elseif($data['account_id']==$acntid):
                        $debitamount=number_format((float)$data['amount'], 2);
                        $creditamount='';
                        $debittotal=$debittotal + $data['amount'];
                        $cred_amt = 0;
                        $debit_amt = $data['amount'];
                        $date = $data['date'];
                    endif;
                else:
                    if($data['type']=='Payment' && $data['creditacnt']==$acntid ):
                        $creditamount=number_format((float)$data['amount'], 2);
                        $debitamount='';
                        $credittotal=$credittotal + $data['amount'];
                    elseif($data['type']=='Receipt' && $data['account_id']==$acntid):
                        $debitamount=number_format((float)$data['amount'], 2);
                        $creditamount='';
                        $debittotal=$debittotal + $data['amount'];

                    endif;
                endif;

                $sql1 = 
                    'INSERT INTO tableau_balancesheet_account (account_type_id, account_type_name, account_sub_id,account_sub_name,bsitem_id,bsitem_name,account_id,account_name,credit_amount,debit_amount,date)
                    VALUES
                    ("'.$acnttypeid.'","'.$accnttype->name.'","'.$acntsubid.'","'.$accntsub->name.'","'.$bsitemid.'","'.$bsitem->itemname.'","'.$acntid.'","'.$accnthead->name.'","'.$cred_amt.'","'.$debit_amt.'","'.$date.'")';
                //echo $sql1;exit;
                $command=$connection->createCommand($sql1);
                $dataReader=$command->query();

                //echo $datarows;exit;
            endforeach;

        }

        else{

            $sql1 = 
                    'INSERT INTO tableau_balancesheet_account (account_type_id, account_type_name, account_sub_id,account_sub_name,bsitem_id,bsitem_name,account_id,account_name,credit_amount,debit_amount)
                    VALUES
                    ("'.$acnttypeid.'","'.$accnttype->name.'","'.$acntsubid.'","'.$accntsub->name.'","'.$bsitemid.'","'.$bsitem->itemname.'","'.$acntid.'","'.$accnthead->name.'",0,0)';
                //echo $sql1;exit;
                $command=$connection->createCommand($sql1);
                $dataReader=$command->query();
        }
        
        $acountbal=$credittotal - $debittotal;
        return $acountbal;
        //$closingbal=$this->getbalance($openbalance,$acountbal);
    }

    /* Balance sheet report data inserting in to table for tableau end */


}

