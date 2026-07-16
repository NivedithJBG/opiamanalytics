<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use amnah\yii2\user\models\User;
use app\models\Userrole;
use app\models\UserProfile;
use app\models\Departments;
use app\models\UserTabs;
use app\models\DepartmentTab;
use app\models\ProjuserSelection;
use app\models\Projects;

AppAsset::register($this);

$userVariable = Yii::$app->session->get('Saml');
 
$ProjectId = '';
$ProjectName = '';
if($projuser = ProjuserSelection::find()->where(['userid' => Yii::$app->user->Id])->one()){
    $Project = Projects::findOne($projuser->projectid);
    $ProjectName = $Project->Name;
    $ProjectId = $projuser->projectid;
}

$user_id = Yii::$app->user->id;
$user = User::find()->where(['id'=>Yii::$app->user->id])->one();
$userprof = UserProfile::find()->where(['user_id'=>Yii::$app->user->id])->one();


$HTTP_HOST = explode('.', $_SERVER['HTTP_HOST']);
$subDomain = array_shift(($HTTP_HOST));


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="shortcut icon" href="../../web/favicon.ico">
    <title>Opiam -  Organised Project Information And Management</title>
    <!-- Bootstrap core CSS -->
    <!-- <link href="<?php //echo Yii::$app->request->baseUrl; ?>/cssnew/bootstrap.min.css?v=2" rel="stylesheet"> -->
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/bootstrap.css" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jumbotron-narrow.css" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jquery.autocomplete.css" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jquery-ui.css" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/ipad.css?v=1.38" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,500,700' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="<?php echo Yii::$app->request->baseUrl; ?>/css/codejqry.css">
    <link rel="stylesheet" href="<?php echo Yii::$app->request->baseUrl; ?>/css/card.css" media="all"  />
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/icons.css" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/css/animate.css" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/bootstrap-tagsinput.css" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/typeahead.css" rel="stylesheet">

    <?php if(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'report') { ?> <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/style11.css?v=1.19" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer11.css" rel="stylesheet">
             
               <?php }else{ ?>
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/style.css?v=1.19" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer1.css" rel="stylesheet">
    <?php } ?>

    <!-- <link href="<//?php echo Yii::$app->request->baseUrl; ?>/cssnew/style.css?v=1.18" rel="stylesheet"> -->
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer.css?v=1.18" rel="stylesheet">
  <!--   <link href="<//?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer1.css" rel="stylesheet"> -->
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer2.css?v=20260716c" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer3.css" rel="stylesheet">

    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer4.css" rel="stylesheet">

    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/dashboard.css" rel="stylesheet">
     <!--<link href="<?php //echo Yii::$app->request->baseUrl; ?>/cssnew/style.css?v=2.11" rel="stylesheet"> 
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
     -->


    <link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jsgantt.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jsgantt-extra.css" />

    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/custom.css?v=20260716d" rel="stylesheet">

     
    <!--<script type="text/javascript" src="<?php //echo Yii::$app->request->baseUrl; ?>/jsnew/jquery-1.9.1.min.js"></script>-->

    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/jquery.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/jquery.min.js"></script>
    

    <!--<script type="text/javascript" src="<?php //echo Yii::$app->request->baseUrl; ?>/jsnew/jquery-ui.js"></script>-->
    <!--<script type="text/javascript" src="<?php //echo Yii::$app->request->baseUrl; ?>/js/magicsuggest.js"></script>
    
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>-->
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/common.js?v=1.6"></script>
    
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/maxcdn.min.js"></script>
    <script src="//cdn.ckeditor.com/4.15.0/full/ckeditor.js"></script>
     
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/custom.js"></script>
    
    <style type="text/css">
        input, select, textarea,
        input.form-control, select.form-control, textarea.form-control {
            color: #000 !important;
        }
        span#clearnotif:hover {
            text-decoration: underline;
        }
        /* Nav icon size */
        .round-icons li a {
            height: 26px !important;
            width: 26px !important;
            font-size: 12px !important;
            margin: 0 2px !important;
        }
        /* Nav icon colours */
        .round-icons .icon-chart3,
        .round-icons .icon-calendar,
        .round-icons .icon-copy.duplicateProject,
        .round-icons .icon-tools.overNow4,
        .round-icons .icon-wrench.overNow,
        .round-icons .icon-document.overNow8,
        .round-icons .perf-dashboard-btn,
        .round-icons .cost-dashboard-btn,
        .round-icons .qe-btn { color: #fff !important; }

        .round-icons .icon-chart3            { background-color: #003580 !important; }
        .round-icons .icon-calendar          { background-color: #B8860B !important; }
        .round-icons .icon-copy.duplicateProject { background-color: #E65C00 !important; }
        .round-icons .icon-tools.overNow4    { background-color: #CC0000 !important; }
        .round-icons .icon-wrench.overNow    { background-color: #555555 !important; }
        .round-icons .icon-document.overNow8 { background-color: #000000 !important; }
        .round-icons .perf-dashboard-btn     { background-color: #2e7d32 !important; height: 26px !important; width: 26px !important; font-size: 12px !important; }
        .round-icons .cost-dashboard-btn     { background-color: #7b1fa2 !important; height: 26px !important; width: 26px !important; font-size: 9px !important; padding: 0 !important; box-sizing: content-box !important; }
        .round-icons .qe-btn                 { background-color: #00838f !important; height: 26px !important; width: 26px !important; font-size: 12px !important; }
        /* Keep coloured backgrounds on hover/focus */
        .round-icons .icon-chart3:hover,            .round-icons .icon-chart3:focus            { background: #002060 !important; }
        .round-icons .icon-calendar:hover,          .round-icons .icon-calendar:focus          { background: #8a6200 !important; }
        .round-icons .icon-copy.duplicateProject:hover, .round-icons .icon-copy.duplicateProject:focus { background: #b84400 !important; }
        .round-icons .icon-tools.overNow4:hover,    .round-icons .icon-tools.overNow4:focus    { background: #990000 !important; }
        .round-icons .icon-wrench.overNow:hover,    .round-icons .icon-wrench.overNow:focus    { background: #333333 !important; }
        .round-icons .icon-document.overNow8:hover, .round-icons .icon-document.overNow8:focus { background: #222222 !important; }
        .round-icons .perf-dashboard-btn:hover,     .round-icons .perf-dashboard-btn:focus     { background: #1b5e20 !important; }
        .round-icons .cost-dashboard-btn:hover,     .round-icons .cost-dashboard-btn:focus     { background: #4a148c !important; }
        .round-icons .qe-btn:hover,                 .round-icons .qe-btn:focus                 { background: #005f6b !important; }
    </style>

    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    
    
    <script>
       tableauDashboardVisible = true;
    </script>

</head>

<?php
$action = Yii::$app->controller->action->id;
if($action=='login')
{ ?>
<body class="login">
<!--<php $this->beginBody() ?>-->

<div class="wrap">

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<?php } else { ?>
<body class="procurement">

    <aside class="leftside">
        <nav class="navbar navbar-default">
          <div class="container-fluid">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>                        
              </button>
              <a class="navbar-brand opiam-logo" href="<?php echo Yii::$app->request->baseUrl; ?>/projectsmain/index" style="margin-top: 11px; margin-left: -105px;">
                  <!--<img src="images/logo.jpg" width="" height="" alt />-->
                  <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/performance-pad-logo.png" width="75px;" height="" alt />
              </a>
            </div>
            <div class="collapse navbar-collapse headernav"  id="myNavbar">

                    <input type="hidden" id="selectedProject" name="selectedProject" value="<?php echo $ProjectName; ?>">
                    <input type="hidden" id="workBookName" name="workBookName">
                    <input type="hidden" id="selectedDashboardType" name="selectedDashboardType">
                    <input type="hidden" id="environment" name="environment" value="<?php echo $subDomain ?>">

               <?php if(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'masters') { ?>
               <h1>Finance Masters</h1>
            
              <?php }
               elseif(Yii::$app->controller->id == 'accountssub' && Yii::$app->controller->action->id == 'edit') { ?>
               <h1>Finance Masters</h1>
            
              <?php }
               elseif(Yii::$app->controller->id == 'accountsitem' && Yii::$app->controller->action->id == 'create') { ?>
               <h1>Finance Masters</h1>
            
              <?php }
               elseif(Yii::$app->controller->id == 'accountsitem' && Yii::$app->controller->action->id == 'updateaccount') { ?>
               <h1>Finance Masters</h1>
                <?php }
                 elseif(Yii::$app->controller->id == 'financerequests' && Yii::$app->controller->action->id == 'index') { ?>
               <h1 id="finance-title-head">Finance</h1>
                <?php }
                 elseif(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'projectmasters') { ?>
               <h1>Project Masters</h1>
                <?php }
                elseif(
                    (Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'index') ||
                    (Yii::$app->controller->id == 'projects' && (Yii::$app->controller->action->id == 'assetregister' || Yii::$app->controller->action->id == 'assetlibrary' ))
                )
                { ?>
                <!-- <h1 id="project-title-head">Projects</h1> -->
                <a class="performance-pad-logo"  href="<?php echo Yii::$app->request->baseUrl; ?>/projectsmain/index"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/performance-pad-text.png" width="175px;" height="" style="margin-top: 18px;" /></a>

                <?php } 
                elseif(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'projectperformance') { ?>
                    <h1 id="project-title-head">Project Performance</h1>

                <?php } 
                elseif(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'dashboards') { ?>
                <h1 id="project-title-head">Dashboard</h1>
            <?php } elseif(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'report') { ?>
                <h1 id="prjct_head">Project Managers Desk</h1>
            <?php } elseif(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'userrole') { ?>
                <h1 id="userprjct_head">User Role</h1>
            <?php } elseif(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'templates') { ?>
                <h1>Templates</h1>
            <?php } elseif(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'newganttchart') { ?>
                <h1>Gantt Chart</h1>
            <?php } elseif(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'dashboard') { ?>
                <h1 id="prj_name">Project Dashboard</h1>
            <?php } elseif(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'reports') { ?>
                <h1>Project Reports</h1>
                 <?php } elseif(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'reporting') { ?>
                <h1>Reporting</h1>

                <?php } elseif(Yii::$app->controller->id == 'procurement') { ?>
                <h1>Procurement</h1>

                <?php } elseif(Yii::$app->controller->id == 'storekeeper') { ?>
                <h1>Site Office</h1>

                <?php }
                ?>


              
                
              <ul class="nav navbar-nav navbar-right round-icons" style="display:none;">
                <li><a href="#" class="icon-shopping_cart"><span class="count">5</span></a></li>
                <li><a href="#" class="icon-bell2 dropdown-toggle" data-toggle="dropdown"><span class="count">5</span></a>
                    <ul class="dropdown-menu">
                      <li><a href="#">Notification One</a></li>
                      <li><a href="#">Notification Two</a></li>
                     
                    </ul>
                </li>
                <li><a href="#" class="icon-user3 dropdown-toggle" data-toggle="dropdown">
                    
                </a>
                <ul class="dropdown-menu">
                  <li><a href="#">Solminds</a></li>
                  <li><a href="#">Profile</a></li>
                 
                </ul>
                </li>
                <?php if($user->account_type != 'perfm_pad_access_only'){ ?>
                <li><a href="#" class="icon-menu1"></a></li>
                <?php } ?>
              </ul>
              
              
              
              <ul class="nav navbar-nav navbar-right round-icons">
                <!-- <li><a class="icon-pie-chart" title="Dashboards" href="<?php //echo Yii::$app->urlManager->createUrl('projectsmain/dashboards')?>"> 
                    </a>
                </li> -->
                <li style="padding: 10px; padding-right:20px; font-weight: normal;">
                    <span class="glyphicon glyphicon-user"></span> &nbsp; <?php echo ucwords(Yii::$app->user->displayName) ;?>
                </li>

                <?php 

                if(Yii::$app->controller->action->id != 'projectperformance') {


                //if($user->account_type == 'perfm_pad_reporting_only'){ 
                ?>

                <!-- <li>
                    <a href="#dashboardPopup" title="Report Dashboard" class="dropdown-toggle resourceTypeTab icon-chart1" data-toggle="modal" data-target="#dashboardPopup"   id="resourceReport" data-type="Report"></a>
                </li> -->

                <?php 
                //}
                ?>

                <?php //if($user->account_type != 'perfm_pad_reporting_only'){ ?>
                <?php if(1){ ?>
                <li>

                </li>

                <?php

                }

                if($ProjectId && Yii::$app->controller->id != 'procurement') {  ?>
                <li>
                    <a href="#ganttchartPopup" class="dropdown-toggle icon-chart3" data-toggle="modal" data-projectid="<?php echo $ProjectId; ?>" id="ganttchartPopupLink" data-target="#ganttchartPopup" title="Gantt Chart"></a>
                    <!-- <a class="icon-chart3" title="Gantt Chart" href="<?php //echo Yii::$app->urlManager->createUrl('projectsmain/newganttchart?id='.$ProjectId)?>"> 
                        </a> -->
                </li>
                

                <?php if(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'index') {  ?>
                <li>
                    <a href="#holidayPopup" class="dropdown-toggle icon-calendar" data-toggle="modal" data-projectid="<?php echo $ProjectId; ?>" id="holidayPopupLink" data-target="#holidayPopup" title="Holidays"></a>
                </li> 

                <li>
                    <a href="javascript:;" class=" icon-copy duplicateProject"  data-projectid="<?php echo $ProjectId; ?>" id="duplicateProject_<?php echo $ProjectId; ?>"  title="Duplicate Project"></a>
                </li> 

                <?php } } ?>

                <?php if(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'index') {

                    $user=User::find()->where(['id'=>Yii::$app->user->id])->one();

                    if($user->role_id !=0){

                        $functabs=UserTabs::find()->where(['function_id'=>1])->andWhere(['role_id'=>$user->role_id])->all();

                            $proj_master='display:none';
                            $res_master='display:none';
                            $proj_report='display:none';
                            $dashboard='display:none';

                            foreach($functabs as $functab){

                                if($functab->tab_id==32){
                                    $proj_master='';
                                }elseif($functab->tab_id==33){
                                    $res_master='';
                                }elseif($functab->tab_id==34){
                                    $proj_report='';
                                }elseif($functab->tab_id==40){
                                    $dashboard='';
                                }  

                                  
                            }
                            echo '<li style="'.$proj_master.'"><a class="icon-tools overNow4" title="Activity Library" href="'.Yii::$app->urlManager->createUrl("projects/projectmasters").'">
                                    </a>
                                </li>
                                <li><a class="icon-wrench overNow" title="Resource Library" href="#"> </a></li>
                                <li><a class="icon-document overNow8" title="Project Documents" href="#"> </a></li>
                                            <li style="'.$dashboard.'"><a class="icon-dashboard prjcet-dashboard" title="Dashboard" href="'.Yii::$app->urlManager->createUrl("projectsmain/dashboard").'">
                                    </a>
                                </li>';

                            if(!$user->account_type  || $user->account_type == 'normal'){ 
                               // echo '<li style="'.$proj_report.'"><a class="icon-flickr overNow1" title="Project Report" href="'.Yii::$app->urlManager->createUrl("projectsmain/reports").'"> </a></li>';
                            }

                    }elseif($user->account_type != 'perfm_pad_reporting_only'){

                 ?>
                    <li><a class="icon-tools overNow4" title="Activity Library" href="<?php echo Yii::$app->urlManager->createUrl('projects/projectmasters')?>">
                        </a>
                    </li>
                    <li><a class="icon-wrench overNow" title="Resource Library" href="#"> </a></li>
                    <li><a class="icon-document overNow8" title="Project Documents" href="#"> </a></li>
                    <li><a class="icon-stats perf-dashboard-btn" title="KPI" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-stats cost-dashboard-btn" title="Cost Dashboard" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-pencil qe-btn" title="Quick Entry" href="#" style="cursor:pointer;"> </a></li>
                    <?php if(!$user->account_type || $user->account_type == 'normal'){ ?>
                    <!-- <li><a class="icon-flickr overNow1" title="Project Report" href="<?php //echo Yii::$app->urlManager->createUrl('projectsmain/reports')?>">
                        </a>
                    </li> -->
                    <?php } ?>

                <?php } }
                elseif(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'reports') { ?>
                    <li><a class="icon-tools overNow4" title="Activity Library" href="<?php echo Yii::$app->urlManager->createUrl('projects/projectmasters')?>">
                        </a>
                    </li>
                    <li><a class="icon-wrench overNow" title="Resource Library" href="#"> </a></li>
                    <li><a class="icon-document overNow8" title="Project Documents" href="#"> </a></li>
                    <li><a class="icon-stats perf-dashboard-btn" title="KPI" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-stats cost-dashboard-btn" title="Cost Dashboard" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-pencil qe-btn" title="Quick Entry" href="#" style="cursor:pointer;"> </a></li>
                    <?php if(!$user->account_type || $user->account_type == 'normal'){ ?>
                    <!-- <li><a class="icon-flickr overNow1555" title="Project Report" href="<?php //echo Yii::$app->urlManager->createUrl('projectsmain/reports')?>">
                        </a>
                    </li> -->
                    <?php } ?>
                <?php }


                 elseif(Yii::$app->controller->id == 'financerequests' && Yii::$app->controller->action->id == 'index') { 
                    $user=User::find()->where(['id'=>Yii::$app->user->id])->one();

                    if($user->role_id !=0){

                        $functabs=UserTabs::find()->where(['function_id'=>3])->andWhere(['role_id'=>$user->role_id])->all();
                        foreach ($functabs as $key => $functab) {
                           
                            
                                if($functab->tab_id==37){
                                    $proj_master='';
                                }else{
                                    $proj_master='display:none;';
                                }

                                 
                                 echo '<li style="'.$proj_master.'"><a class="icon-tools overNow2" title="Finance Masters" href="'.Yii::$app->urlManager->createUrl("projects/masters").'"></a></li>';
                                 echo '<li style="'.$proj_master.'"><a class="icon-flickr overFinreport" title="Finance Reports" href="'.Yii::$app->urlManager->createUrl("financerequests/finreports").'"></a></li>';
                             }
                            
                            
                        }else{
                    ?>

                    <li><a class="icon-tools overNow2" title="Finance Masters" href="<?php echo Yii::$app->urlManager->createUrl('projects/masters')?>"> 
                        </a>
                    </li>

                    <li><a class="icon-flickr overFinreport" title="Finance Reports" href="<?php echo Yii::$app->urlManager->createUrl('financerequests/finreports')?>"> 
                        </a>
                    </li>
                    <?php 
                    
                        if($userVariable!=''){
                            $dash = '';
                        }
                        else{
                            $dash = 'style="pointer-events: none;"';
                        }
                    
                    ?>
                    
                    <li><a class="icon-dashboard finance-dashboard" title="Dashboard" style="display:none;" href="<?php echo Yii::$app->urlManager->createUrl('financerequests/findashboard')?>" <?php echo $dash; ?>> 
                        </a>
                    </li>

                    <?php }  }

                 elseif(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'userrole') { ?>

                    <li><a class="icon-tools overNow6" title="User Masters" href="<?php echo Yii::$app->urlManager->createUrl('projects/masters')?>"> 
                        </a>
                    </li>

                 <?php }   
                 elseif(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'projectmasters') { ?>

                    <li><a class="icon-tools overNow4" title="Activity Library" href="<?php echo Yii::$app->urlManager->createUrl('projects/projectmasters')?>">
                        </a>
                    </li>
                    <li><a class="icon-wrench overNow" title="Resource Library" href="#"> </a></li>
                    <li><a class="icon-document overNow8" title="Project Documents" href="#"> </a></li>
                    <li><a class="icon-stats perf-dashboard-btn" title="KPI" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-stats cost-dashboard-btn" title="Cost Dashboard" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-pencil qe-btn" title="Quick Entry" href="#" style="cursor:pointer;"> </a></li>
                    <?php if(!$user->account_type || $user->account_type == 'normal'){ ?>
                    <!-- <li><a class="icon-flickr overNow1" title="Project Report" href="<?php //echo Yii::$app->urlManager->createUrl('projectsmain/reports')?>">
                        </a>
                    </li> -->
                    <?php } ?>


                <?php } elseif(Yii::$app->controller->id == 'procurement') { ?>
                    <li><a class="icon-shop overNowVendorLib" title="Vendor Library" href="#"> </a></li>
                <?php } else {
                            $user=User::find()->where(['id'=>Yii::$app->user->id])->one();

                            if($user->role_id !=0){

                                $functabs=UserTabs::find()->where(['function_id'=>4])->andWhere(['role_id'=>$user->role_id])->all();

                                    $proj_master='display:none';
                                    $oper_report='display:none';
                                    $oper_dashboard='display:none';

                                    foreach($functabs as $functab){

                                        if($functab->tab_id==38){
                                            $proj_master='';
                                        }elseif($functab->tab_id==39){
                                            $oper_report='';
                                        }elseif($functab->tab_id==42){
                                            $oper_dashboard='';
                                        }

                                          
                                    }

                                echo '<li style="'.$proj_master.'"><a class="icon-tools overNow4" title="Activity Library" href="'.Yii::$app->urlManager->createUrl("projects/projectmasters").'"> 
                                        </a>
                                    </li>
                                    <li style="'.$oper_dashboard.'"><a class="icon-dashboard prjcet-dashboard" title="Dashboard"  href="'.Yii::$app->urlManager->createUrl("projectsmain/dashboard").'"> 
                                        </a>
                                    </li>
                                    <li style="'.$oper_report.'"><a class="icon-flickr overNow5" title="Operation Report" href="'.Yii::$app->urlManager->createUrl("projects/operation_report").'"> 
                                        </a>
                                    </li>';


                                }else{


                 ?>
                    <li><a class="icon-tools overNow4" title="Activity Library" href="<?php echo Yii::$app->urlManager->createUrl('projects/projectmasters')?>">
                        </a>
                    </li>
                    <li><a class="icon-stats perf-dashboard-btn" title="KPI" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-stats cost-dashboard-btn" title="Cost Dashboard" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-pencil qe-btn" title="Quick Entry" href="#" style="cursor:pointer;"> </a></li>
                <?php  } } } ?>


                <?php 
                if(Yii::$app->controller->id == 'projects' ){
                if($projuser->projectid == 12){//Office_coperate 
                ?>    
                    <!-- <li><a class="icon-flickr overNow5" title="Operation Reports" href="<?php //echo Yii::$app->urlManager->createUrl('projects/operation_report')?>"></a>
                    </li> 
                    <li><a class="icon-moneybag overNow5" title="Asset Register" href="<?php //echo Yii::$app->urlManager->createUrl('projects/operation_report')?>">  </a> </li>-->
                    <li><a class="icon-moneybag" title="Asset Library" href="<?php echo Yii::$app->urlManager->createUrl('projects/assetlibrary')?>"> </a></li>
                <?php } else{ ?>
                    <!-- <li><a class="icon-moneybag overNow7" title="Asset Library" href="javascript:;"> </a></li> -->
                    <li><a class="icon-moneybag" title="Asset Register" href="<?php echo Yii::$app->urlManager->createUrl('projects/assetregister')?>"> </a></li>
                <?php }  } ?>





                    <li><a href="#" title="My Account" class="MyAccountNAV icon-user3 dropdown-toggle" data-toggle="dropdown">
                        </a>
                        <ul class="dropdown-menu" style="height: auto;margin-left: -55px;">
                            <li>
                                <a href="#updateProfilePopup" class="dropdown-toggle" data-toggle="modal" id="updateProfilePopupLink" data-target="#updateProfilePopup" title="Upate Profile"><span class="glyphicon glyphicon-user"></span> &nbsp; <?php echo Yii::$app->user->displayName ;?></a></li>


                            <!--<li class="dropdownli"><a href="<?php //echo Yii::$app->urlManager->createUrl('ProjectPricing/index')?>">Project Pricing</a></li>
                            <li class="dropdownli"><a href="<?php //echo Yii::$app->urlManager->createUrl('Legal/index')?>">Legal</a></li>
                            <li class="dropdownli"><a href="<?php //echo Yii::$app->urlManager->createUrl('doccumentManager/index')?>">Document Manager</a></li>
                            <li class="dropdownli"><a href="<?php //echo Yii::$app->urlManager->createUrl('projects/masters')?>">Masters</a></li>
                             <li class="dropdownli"><a href="<?php //echo Yii::$app->urlManager->createUrl('projects/masters')?>">Project Masters</a></li>
                            <li class="dropdownli"><a href="<?php //echo Yii::$app->urlManager->createUrl('user/user/admin')?>">Human Resources</a></li>-->
                            <li class="dropdownli"><a data-method='POST' href="<?php echo Yii::$app->urlManager->createUrl('site/logout')?>">Logout</a></li>
                        </ul>
                    </li>
                    <?php if($user->account_type != 'perfm_pad_reporting_only' && $user->account_type != 'project_performance_only'){ ?>
                    <li><a href="#" class="icon-menu1 menuNAV"></a></li>
                    <?php } ?>
                </ul>
                         
            </div>
          </div>
        </nav>

        <?= $content ?>

    </aside>

    <aside class="rightside">
        <div class="col-md-12">
                 <div class="rightside-logo">
                     <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/logo3.jpg" width="" height="" alt />
                     <!-- <img src="<?php //echo Yii::app()->request->baseUrl; ?>/images/logo3.jpg?v=1" id="logoimage"> -->
                     </div> 
              
              
              <ul class="nav nav-pills nav-stacked">
                <?php 
                    
                

                 if(Yii::$app->user->isGuest){ ?>
                    <li><a href="<?php echo Yii::$app->urlManager->createUrl('user/login')?>">Login</a></li>

                <?php  } 
                 
                else{ 
                    $user=User::find()->where(['id'=>Yii::$app->user->id])->one();
                    $funcnames=Departments::find()->all();
                    $functiontabs=DepartmentTab::find()->all();
                    
                     if($user->role_id !=0){


                            foreach($funcnames AS $funcname){
                                
                                
                                $functions=UserTabs::find()->where(['function_id'=>$funcname->dep_id])->andWhere(['role_id'=>$user->role_id])->one();
                                if($functions){
                                    $modules=''; 
                                }
                                else
                                {
                                    $modules='display:none';
                                }
                                if($funcname->dep_id == 1){
                                echo '<li style="'.$modules.'"><a href="'.Yii::$app->urlManager->createUrl('projectsmain/index').'">'.$funcname->name.'</li>';
                                }
                                elseif($funcname->dep_id == 4){
                                    //echo '<li style="'.$modules.'"><a href="'.Yii::$app->urlManager->createUrl('projects/report').'">'.$funcname->name.'</li>';
                                }
                                elseif($funcname->dep_id == 3){
                                    //echo '<li style="'.$modules.'"><a href="'.Yii::$app->urlManager->createUrl('financerequests/index').'">'.$funcname->name.'</li>';
                                }
                                elseif($funcname->dep_id == 5){
                                    echo '<li style="'.$modules.'"><a href="'.Yii::$app->urlManager->createUrl('projects/userrole').'">'.$funcname->name.'</li>';
                                }



                            }

                         
                    }else{

                        if($user->account_type == 'perfm_pad_access_only'){
                    ?>

                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('projectsmain/index')?>">Projects</a></li>

                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('projectsmain/projectperformance')?>">Project Performance</a></li>


                    <?php } else { ?>

                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('projectsmain/index')?>">Projects</a></li>

                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('procurement/index')?>">Procurement</a></li>

                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('storekeeper/index')?>">Site Office</a></li>

                        <?php if($user->account_type != 'client_user' && $user->account_type != 'client_admin'){ ?>
                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('projects/userrole')?>">User Management</a></li>
                        <?php } ?>

                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('projects/templates')?>">Templates</a></li>


                <?php } } } ?>
            </ul>
              
            </div>

    </aside>
    


<?php } ?>

<!--<php $this->endBody() ?>-->

    <div id="emailorderModel" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Email Order</h4>
                </div>
                <form action="" id="orderemail" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="email">Email address:</label>
                            <input type="email" class="form-control" id="orderemailid" required>
                            <span class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="cc">Cc:</label>
                            <input type="email" class="form-control" id="orderccemail" required>
                            <span class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject:</label>
                            <input type="text" class="form-control" id="ordersubject" required>
                            <span class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="body">Body:</label>
                            <textarea rows="8" cols="25" class="form-control" id="orderbody" required></textarea>
                            <span class="error"></span>
                            <!--<input type="text" class="form-control" id="body" required>-->
                        </div>
                        <div class="TandC">
                        <span class="icon-paperclip"></span>attachment : <br>
                        1 . <a href="" target="_blank" id="Orderw3s">Order Receipt</a><br>
                        2 . <a href="" target="_blank" id="Termsw3s">Terms and Conditions</a>
                        
                        </div>
                        <div class="mailloader" style="display: none">
                            <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/mail.gif" align="middle">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="orderid">
                        <div class="alert alert-success" id="ordersuccesinfo" style="display: none">

                        </div>
                        <div class="alert alert-warning" id="ordererrorinfo" style="display: none">

                        </div>
                        <button type="button" class="btn btn-default" id="emailorder1">Send</button>
                    </div>
                </form>
            </div>

        </div>
    </div>


    <?php
        /* Project dashboard start */
        if($action!='login')
        { 

            echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/projectsmain/dashboard.php');
        }
        /* Project dashboard end */

        /* Finance dashboard start */
        if($action!='login')
        { 

            //echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/financerequests/findashboard.php');
        }
        /* Project dashboard end */
        

        //Finance Master Overrelay start
        if(Yii::$app->controller->id == 'financerequests' && Yii::$app->controller->action->id == 'index') { 
            
            echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/projects/costing.php');
            echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/financerequests/finreports.php');
        }
        //Finance Master Overrelay end
        
        //Project Master Overrelay start
        if(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'report' || Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'index' || Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'reports' || Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'projectmasters') {

            echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/projects/projectmasters.php');
        }
        //Project Master Overrelay end

        //Resource Library Overrelay start
        if(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'index' || Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'reports' || Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'projectmasters') {
            echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/projectsmain/overrelay-resourcemaster.php');
        }
        //Resource Library Overrelay end

        //Vendor Library Overrelay start
        if(Yii::$app->controller->id == 'procurement') {
            echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/procurement/overrelay-vendorlibrary.php');
        }
        //Vendor Library Overrelay end

    ?>
    <div id="emailvendorModel" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Email Vendor</h4>
                </div>
                <form action="" id="vendoremail" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="email">Email address:</label>
                            <input type="email" class="form-control" id="vendoremailid" required>
                            <span class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject:</label>
                            <input type="text" class="form-control" id="subject" required>
                            <span class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="body">Body:</label>
                            <textarea rows="8" cols="25" class="form-control" id="body" required></textarea>
                            <span class="error"></span>
                            <!--<input type="text" class="form-control" id="body" required>-->
                        </div>
                        <div class="mailloader" style="display: none">
                            <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/mail.gif" align="middle">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="alert alert-success" id="succesinfo" style="display: none">

                        </div>
                        <div class="alert alert-warning" id="errorinfo" style="display: none">

                        </div>
                        <button type="button" class="btn btn-default" id="emailvendor">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="phonemodal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Contact</h4>
                </div>
                <div class="modal-body" id="vendcontno">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>            
            </div>
        </div>
    </div>

        <!-- Choose vendor popup start -->

        <div class="modal fade changeVendorpopou" id="changeVendorpopou">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Change Vendor</h4>
                        <button type="button" class="close changevendorpopup" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form>
                            <table class="table table-bordered vendor-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vendor Name</th>
                                        <th>Location</th>
                                        <th>Brand<br> 
                                        <th>Unit</th>
                                        <th>Rate</th>
                                        <th>Split Quantity</th>
                                        <th></th>
                                    </tr>
                                    <tr class="preloader" style="display:none;"><td colspan="12" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody  id="newaddedresources">
                                </tbody>
                            </table>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                        <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
                
                    </div>

                </div>
            </div>
        </div>

        <!-- Choose vendor popup end -->



        <!-- Workorder Choose vendor popup start -->

        <div class="modal fade changewrkVendorpopou" id="changewrkVendorpopou">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Change Vendor</h4>
                        <button type="button" class="close changewrkvendorpopup" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form>
                            <table class="table table-bordered vendor-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vendor Name</th>
                                        <th>Location</th>
                                        <th>Brand<br> 
                                        <th>Unit</th>
                                        <th>Rate</th>
                                        <th>Split Quantity</th>
                                        <th></th>
                                    </tr>
                                    <tr class="preloader" style="display:none;"><td colspan="12" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody  id="newworkaddedresources">
                                </tbody>
                            </table>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                        <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
                
                    </div>

                </div>
            </div>
        </div>

        <!-- Workorder Choose vendor popup end -->

        <!-- DirectWorkorder Choose vendor popup start -->

        <div class="modal fade changedirectwrkVendorpopou" id="changedirectwrkVendorpopou">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Change Vendor</h4>
                        <button type="button" class="close changedirwrkvendorpopup" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form>
                            <table class="table table-bordered vendor-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vendor Name</th>
                                        <th>Location</th>
                                        <th>Brand<br> 
                                        <th>Unit</th>
                                        <th>Rate</th>
                                        <th>Split Quantity</th>
                                        <th></th>
                                    </tr>
                                    <tr class="preloader" style="display:none;"><td colspan="12" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody  id="newdirworkaddedresources">
                                </tbody>
                            </table>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                        <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
                
                    </div>

                </div>
            </div>
        </div>

        <!-- DirectWorkorder Choose vendor popup end -->

        <!-- Leaseorder Choose vendor popup start -->

        <div class="modal fade changeleaseVendorpopou" id="changeleaseVendorpopou">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Change Vendor</h4>
                        <button type="button" class="close changeleasevendorpopup" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form>
                            <table class="table table-bordered vendor-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vendor Name</th>
                                        <th>Location</th>
                                        <th>Brand<br> 
                                        <th>Unit</th>
                                        <th>Rate</th>
                                        <th>Split Quantity</th>
                                        <th></th>
                                    </tr>
                                    <tr class="preloader" style="display:none;"><td colspan="12" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody  id="newleaseaddedresources">
                                </tbody>
                            </table>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                        <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
                
                    </div>

                </div>
            </div>
        </div>

        <!-- Leaseorder Choose vendor popup end -->

        <!-- Despatch Choose vendor popup start -->

        <div class="modal fade changedespVendorpopou" id="changedespVendorpopou">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Change Vendor</h4>
                        <button type="button" class="close changedespvendorpopup" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form>
                            <table class="table table-bordered vendor-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vendor Name</th>
                                        <th>Location</th>
                                        <th>Brand<br> 
                                        <th>Unit</th>
                                        <th>Rate</th>
                                        <th>Split Quantity</th>
                                        <th></th>
                                    </tr>
                                    <tr class="preloader" style="display:none;"><td colspan="12" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody  id="newdespaddedresources">
                                </tbody>
                            </table>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                        <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
                
                    </div>

                </div>
            </div>
        </div>

        <!-- Despatch Choose vendor popup end -->




        <!-- tableau Dashboard popup  -->

        <div class="modal fade dashboardPopup" id="dashboardPopup" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <div>
                            <h4 class="modal-title dashboardPopupTitle" style="float:left;">Dashboard</h4>
                            <div style="float: right;">
                                <div class="row">
                                    <div class="col-md-4 refreshTableauBtnContainer" style=" padding-top: 4px; padding-right: 40px;">
                                        <a href="javascript:;" title="Refresh Dashboard Data" class="icon-refresh3 refreshTableau" style="font-size: 22px; display:none;"></a>
                                    </div>
                                    <div class="col-md-4 " style=" padding-top: 4px; padding-right: 40px;">
                                        <a href="javascript:;" title="See Dashboard in Full screen" class="icon-enlarge enlargeDashboard" style="font-size: 18px;"></a>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="close dashboardPopup dashboardPopupCloseBtn" data-dismiss="modal">×</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear: both;text-align: center;">
                            <div class="dashboardPopupErrormessage"></div>
                            <div class="dashboardPopupSuccessmessage"></div>
                            <div class="dashboardPopupSuccessProgress" align="center">
                                <div id="myProgress" >
                                  <div id="myBar"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                       
                        <div class="dashboardView" id="dashPerformance" style="height:100%; width:100%; display: none;" ></div>
                        <div class="dashboardView" id="dashReport" style="height:100%; width:100%; display: none;" ></div>
                        <div class="dashboardView" id="dashSchedule" style="height:100%; width:100%; display: none;" ></div>
                        <div class="dashboardView" id="dashPlant" style="height:100%; width:100%; display: none;" ></div>
                        <div class="dashboardView" id="dashCost" style="height:100%; width:100%; display: none;" ></div>
                        <div class="dashboardView" id="dashLabour" style="height:100%; width:100%; display: none;" ></div>
                        <div class="dashboardView" id="dashSubcontractor" style="height:100%; width:100%; display: none;" ></div>
                        <div class="dashboardView" id="dashMaterials" style="height:100%; width:100%; display: none;" ></div>
                        <!-- <div class="dashboardView" id="dashEstimate" style="height:100%; width:100%; display: none;" ></div> -->
                        <div class="dashboardView" id="dashSupport" style="height:100%; width:100%; display: none;" ></div>
                        <div class="dashboardView" id="dashResource" style="height:100%; width:100%; display: none;" ></div>
                        <div class="dashboardView" id="dashOrganisation" style="height:100%; width:100%; display: none;" ></div>

                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                        <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
                
                    </div>

                </div>
            </div>
        </div>
        <!-- tableau Dashboard popup end -->



        <!-- Gantt Chart popup  -->
        <div class="modal fade ganttchartPopup" id="ganttchartPopup" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <div>
                            <h4 class="modal-title " style="float:left;">Gantt Chart</h4>
                            <div style="float: right;">


                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-4 " style=" padding-top: 4px; padding-right: 40px;">
                                        <a id="ganttchartPopupForm" data-link="<?php echo Yii::$app->urlManager->createUrl('projectsmain/newganttchart?id=')?>" href="" title="View Gantt Chart" class="  icon-enlarge"  target="_blank" style="font-size: 18px;"></a>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="close ganttchartPopup ganttchartPopupCloseBtn" data-dismiss="modal">×</button>
                                    </div>
                                </div>




                            </div>
                        </div>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div id="ganttchartPopupBody" style="height:100%; width:100%; " >
                            <div style="padding: 50px; text-align: center; " >
                                    LOADING....
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                    </div>

                </div>
            </div>
        </div>
        <!-- Gantt Chart popup end -->



        <!-- Holiday popup  -->
        <div class="modal fade holidayPopup" id="holidayPopup" >
            <div class="modal-dialog modal-lg" style="width:450px;">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <div>
                            <h4 class="modal-title " style="float:left;">Holidays</h4>
                            <div style="float: right;">
                                <div class="row">
                                        <button type="button" class="close holidayPopup popupCloseBtn" data-dismiss="modal">×</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div id="holidayPopupBody" style="height:100%; width:100%; " >
                                LOADING....
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                    </div>

                </div>
            </div>
        </div>
        <!-- Holiday popup end -->



        <!---------------- POPUP - TASK REPORT ------------------->
        <div class="modal fade taskReportPopup" id="taskReportPopup" >
            <div class="modal-dialog modal-lg" style="width:65%;">
                <div class="modal-content">
                    <!-- <form id="addAssetLibItemform" > -->

                      <!-- Modal Header -->
                      <div class="modal-header" style="padding: 15px 25px;">
                          <h4 class="modal-title"  style="float: left;">Progress Report</h4>
                          <button type="button" class="close taskReportPopup taskReportPopupCloseBtn" data-dismiss="modal" style="float:right;">×</button>
                      </div>

                      <!-- Modal body -->
                      <div class="modal-body" style="padding: 20px 25px; min-height:560px; max-height:80vh; overflow-y:auto;">
                          <div class="taskReportData" id="taskReportData">
                          </div>
                      </div>

                      <!-- Modal footer -->
                      <div class="modal-footer" style="padding: 15px 25px;">

                            <div style="clear:both;float:left;padding-top:5px;">
                                <button class="btn btn-report taskProgressRptSaveDraft" id="taskProgressRptSaveDraft" data-id="" value="" title="Save as Draft" style="background:#1a6fc4;color:#fff;border:none;">Save as Draft</button>
                            </div>

                            <div style="width: 65%; float: left; text-align: center;">
                                <span id="task-success-messages" style="color:green;display: none;"><h5>Task Reported Successfully</h5></span>
                                <span id="task-error-messages" style="color:red;display: none;"><h5>Task Reported not Successfully</h5></span>
                            </div>


                            <div class="text-right submit-report-cntnr">
                                <button class="btn btn-primary btn-report savetaskprogressrpt" id="savetaskprogressrpt" data-id="" value="" title="Save">Report</button>  
                                <button class="btn btn-danger btn-report canceltaskprogressrpt" id="canceltaskprogressrpt" data-dismiss="modal" data-id="" value="" title="Cancel">Cancel</button>
                            </div>
                  
                      </div>
                    <!-- </form>   -->

                </div>
            </div>
        </div>
        <!------------------------------------------->







        <!---------------- POPUP - Profile Update ------------------->
        <?php 
        if($userprof){
        ?>
        <div class="modal fade updateProfilePopup" id="updateProfilePopup" >
            <div class="modal-dialog modal-lg" >
                <div class="modal-content">
                    <!-- <form id="addAssetLibItemform" > -->

                      <!-- Modal Header -->
                      <div class="modal-header" style="padding: 15px 25px;">
                          <h4 class="modal-title"  style="float: left;">Update Profile</h4>
                          <button type="button" class="close updateProfilePopup popupCloseBtn" data-dismiss="modal" style="float:right;">×</button>
                      </div>

                      <!-- Modal body -->
                      <div class="modal-body" style="padding: 20px 25px;">
                          <div>
                                <form id="updateusers" method="POST">
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input class="form-control" id="edit_usrfname" name="edit_usrfname" value="<?php echo $userprof->firstname; ?>" type="text" placeholder="Enter First Name">
                                            <input type="hidden" name="edit_userid" id="edit_userid" value="<?php echo $user_id ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <div class="form-group">
                                            <label>Last Name</label>
                                            <input class="form-control" id="edit_usrlname" name="edit_usrlname" value="<?php echo $userprof->lastname ?>" type="text" placeholder="Enter Last Name">
                                        </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input class="form-control" id="edit_usremail" name="edit_usremail" value="<?php echo $user->email ?>" type="text" placeholder="Enter Email">
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label>New Password</label>
                                            <input class="form-control" id="edit_usrpswd" name="edit_usrpswd" value="" type="password" placeholder="Enter Password">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <div class="form-group">
                                            <label>Confirm Password</label>
                                            <input class="form-control" id="edit_confirmpswd" name="edit_confirmpswd" value="" type="password" placeholder="Confirm Password">
                                        </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        &nbsp;
                                    </div>                                
                                </form>
                          </div>
                      </div>

                      <!-- Modal footer -->
                      <div class="modal-footer" style="padding: 15px 25px; clear: both; ">
                          
                            <div style="clear: both; float: left; padding-top:5px;">
                            </div>

                            <div style="width: 70%; float: left; text-align: center;">
                                <span id="editprof-success-messages" style="color:green;display: none;"><h5>Profile Updated Successfully</h5></span>
                                <span id="editprof-error-messages" style="color:red;display: none;"><h5>Profile Updated not Successfully</h5></span>
                            </div>

                            <div class="text-right submit-report-cntnr">
                                <button class="btn btn-primary btn-report updateProfile" id="updateProfile" data-id="" value="" title="Save">Update</button>  
                                <button class="btn btn-danger btn-report cancelUpdateProfile" id="cancelUpdateProfile" data-dismiss="modal" data-id="" value="" title="Cancel" style="font-family: Montserrat;min-width: 103px;background: #ed1b2d;border-color: #ed1b2d;    border-radius: 23px;">Cancel</button>
                            </div>
                  
                      </div>
                    <!-- </form>   -->

                </div>
            </div>
        </div>
        <?php 
        }
        ?>
        <!------------------------------------------->


        <!---  RESOURCE GROUP POPUP ---->
        <div class="modal fade resourceGroupPopup" id="resourceGroupPopup" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title"  style="float: left;">Manage Resource Groups</h4>
                        <button type="button" class="close resourceGroupPopup" data-dismiss="modal" style="float:right; font-size: 30px;">×</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">


                            <div class="row">
                                    
                                    <div class="col-md-12">
                                        <div class="preloader" id="Promain-preloader-Listwbs" style="display: none;" align="center">
                                            <img src="/sreejith/opiam_analytics/web/images/loader.gif" align="middle">
                                        </div>


                                        <div class="row">
                                            <form id="estworktypeform">
                                                <div class="col-md-3"></div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>Resource Group</label>
                                                        <input type="text" class="form-control" id="resgroupname" placeholder="Resource Group">
                                                        <span class="error" style="display: none;"></span>
                                                    </div>  
                                                </div>
                                                <div class="col-md-4 text-left" style="padding-top: 5px;">
                                                    <label style="width: 100%;"></label>
                                                    
                                                    <button type="button" class="btn btn-primary save-btn" id="saveResGroup"><span class="icon-check"></span> Add</button>
                                                </div>
                                            </form>
                                        </div>

                                        <hr>

                                        <div id="resGroupListContainer" class="row ">
                                            
                                        </div>


                                    </div>
                                    
                            </div>

                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                        <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
                
                    </div>

                </div>
            </div>
        </div>
        <!-------------->




        <div style="font-size: 13px;font-weight: normal; width: 100%; text-align: center; padding-top:20px;">
            Powered by <a href="https://www.opiamanalytics.com" target="_blank" style="color: #000;font-weight: 500;">Opiam Analytics</a>, 
            Copyright © 2022
            <?php //echo date('Y'); ?>. 
            <!-- <a href="https://www.opiamanalytics.com" target="_blank"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/opiam-analytics-logo-trans.png" width="10%" height="" alt /></a> -->
        </div>

<!-- ── Chatbot ─────────────────────────────────────────────────────────── -->
<style>
/* FAB button */
#cb-btn{position:fixed;bottom:24px;right:24px;z-index:99999;width:44px;height:44px;border-radius:50%;background:#1a2540;color:#fff;border:none;font-size:20px;cursor:pointer;box-shadow:0 4px 16px rgba(0,0,0,.35);display:flex;align-items:center;justify-content:center;transition:background .2s}
#cb-btn:hover{background:#2d3f6e}

/* Desktop floating panel */
#cb-win{position:fixed;bottom:80px;right:24px;z-index:99999;width:330px;height:440px;background:#fff;border-radius:14px;box-shadow:0 8px 40px rgba(0,0,0,.28);display:none;flex-direction:column;font-family:'Times New Roman',Times,serif;overflow:hidden}

/* Mobile: full-screen overlay */
@media(max-width:600px){
  #cb-btn{width:42px;height:42px;font-size:19px;bottom:16px;right:16px}
  #cb-win{bottom:0;right:0;left:0;top:0;width:100%;height:100%;border-radius:0;box-shadow:none}
}

/* Header */
#cb-hdr{background:#1a2540;color:#fff;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;font-family:'Times New Roman',Times,serif;flex-shrink:0}
#cb-hdr-title{font-size:17px;font-weight:700;line-height:1.2}
#cb-hdr-sub{font-size:13px;opacity:.65;font-weight:400;margin-top:2px}
#cb-hdr-actions{display:flex;align-items:center;gap:10px}
#cb-clear{background:none;border:1px solid rgba(255,255,255,.3);color:rgba(255,255,255,.7);font-size:12px;cursor:pointer;padding:3px 8px;border-radius:4px;white-space:nowrap;font-family:'Times New Roman',Times,serif}
#cb-clear:hover{color:#fff;border-color:#fff}
#cb-close{background:none;border:none;color:#fff;font-size:26px;cursor:pointer;line-height:1;padding:0 2px;opacity:.8}
#cb-close:hover{opacity:1}

/* Message area */
#cb-msgs{flex:1;overflow-y:auto;padding:14px 12px;display:flex;flex-direction:column;gap:10px}
.cb-msg{max-width:86%;padding:10px 14px;border-radius:12px;font-size:17px;line-height:1.65;word-wrap:break-word;font-family:'Times New Roman',Times,serif;white-space:pre-wrap}
.cb-msg.user{background:#1a2540;color:#fff;align-self:flex-end;border-bottom-right-radius:3px}
.cb-msg.bot{background:#f0f3fa;color:#111;align-self:flex-start;border-bottom-left-radius:3px}
.cb-msg.typing{color:#888;font-style:italic;background:#f0f3fa;align-self:flex-start}

/* Input footer */
#cb-foot{display:flex;gap:6px;padding:8px 10px;border-top:1px solid #e8ecf4;flex-shrink:0;background:#fff;align-items:flex-end}
#cb-input{flex:1;border:1px solid #cbd5e1;border-radius:20px;padding:7px 13px;font-size:15px;outline:none;font-family:'Times New Roman',Times,serif;resize:none;max-height:100px;overflow-y:auto;line-height:1.5}
#cb-input:focus{border-color:#1a2540}
#cb-send{background:#1a2540;color:#fff;border:none;border-radius:50%;width:30px;height:30px;font-size:13px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .2s}
#cb-send:hover{background:#2d3f6e}
#cb-send:disabled{background:#b0b8cc;cursor:not-allowed}
#cb-mic{background:#f0f3fa;color:#1a2540;border:1px solid #cbd5e1;border-radius:50%;width:30px;height:30px;font-size:13px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .2s,color .2s,border-color .2s}
#cb-mic:hover{background:#e2e8f0}
#cb-mic.listening{background:#e53935;color:#fff;border-color:#e53935;animation:cb-pulse 1s infinite}
@keyframes cb-pulse{0%,100%{box-shadow:0 0 0 0 rgba(229,57,53,.4)}50%{box-shadow:0 0 0 7px rgba(229,57,53,0)}}

@media(max-width:600px){
  .cb-msg{font-size:17px;max-width:90%}
  #cb-input{font-size:17px}
  #cb-foot{padding:10px 10px 18px}
  #cb-send,#cb-mic{width:40px;height:40px;font-size:17px}
}
</style>

<button id="cb-btn" title="Ask Project Assistant"><span class="icon-bubbles4"></span></button>

<div id="cb-win" role="dialog" aria-label="Project Assistant">
    <div id="cb-hdr">
        <div>
            <div id="cb-hdr-title">Project Assistant</div>
            <div id="cb-hdr-sub">Ask anything about your projects</div>
        </div>
        <div id="cb-hdr-actions">
            <button id="cb-clear">Clear</button>
            <button id="cb-close" aria-label="Close">&times;</button>
        </div>
    </div>
    <div id="cb-msgs" role="log" aria-live="polite">
        <div class="cb-msg bot">Hi! I can answer questions about your projects, costs, activities, schedules and documents. What would you like to know?</div>
    </div>
    <div id="cb-foot">
        <textarea id="cb-input" placeholder="Ask a question…" autocomplete="off" rows="1" aria-label="Message"></textarea>
        <button id="cb-mic" title="Speak" aria-label="Voice input">&#127908;</button>
        <button id="cb-send" title="Send" aria-label="Send">&#10148;</button>
    </div>
</div>

<script>
(function(){
    var cbHistory = [];
    var win     = document.getElementById('cb-win');
    var msgs    = document.getElementById('cb-msgs');
    var inp     = document.getElementById('cb-input');
    var sendBtn = document.getElementById('cb-send');
    var chatUrl = '<?php echo Yii::$app->urlManager->createAbsoluteUrl(["/chatbot/chat"]); ?>';

    /* Auto-grow textarea */
    inp.addEventListener('input', function(){
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    /* Open / close */
    function openChat(){
        win.style.display = 'flex';
        if(window.innerWidth <= 600) document.body.style.overflow = 'hidden';
        setTimeout(function(){ inp.focus(); }, 50);
    }
    function closeChat(){
        win.style.display = 'none';
        if(window.innerWidth <= 600) document.body.style.overflow = '';
    }

    document.getElementById('cb-btn').addEventListener('click', function(){
        win.style.display === 'flex' ? closeChat() : openChat();
    });
    document.getElementById('cb-close').addEventListener('click', closeChat);

    document.getElementById('cb-clear').addEventListener('click', function(){
        cbHistory = [];
        msgs.innerHTML = '<div class="cb-msg bot">Chat cleared. How can I help you?</div>';
    });

    /* Voice input */
    var micBtn = document.getElementById('cb-mic');
    var recognition = null;
    var listening = false;
    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if(SpeechRecognition){
        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = true;
        recognition.lang = 'en-IN';
        var interim = '';
        recognition.onstart = function(){ listening = true; micBtn.classList.add('listening'); micBtn.title = 'Stop'; };
        recognition.onend   = function(){ listening = false; micBtn.classList.remove('listening'); micBtn.title = 'Speak'; interim = ''; };
        recognition.onerror = function(e){ listening = false; micBtn.classList.remove('listening'); micBtn.title = 'Speak'; };
        recognition.onresult = function(e){
            var final = '', intr = '';
            for(var i = e.resultIndex; i < e.results.length; i++){
                if(e.results[i].isFinal) final += e.results[i][0].transcript;
                else intr += e.results[i][0].transcript;
            }
            if(final){ inp.value = (inp.value + ' ' + final).trim(); inp.style.height='auto'; inp.style.height=Math.min(inp.scrollHeight,120)+'px'; }
        };
        micBtn.addEventListener('click', function(){
            if(listening){ recognition.stop(); }
            else { inp.focus(); try{ recognition.start(); } catch(ex){} }
        });
    } else {
        micBtn.title = 'Voice not supported in this browser';
        micBtn.style.opacity = '0.4';
        micBtn.style.cursor = 'not-allowed';
    }

    sendBtn.addEventListener('click', sendMsg);
    inp.addEventListener('keydown', function(e){
        if(e.key === 'Enter' && !e.shiftKey){ e.preventDefault(); sendMsg(); }
    });

    function sendMsg(){
        var text = inp.value.trim();
        if(!text || sendBtn.disabled) return;
        inp.value = '';
        inp.style.height = 'auto';
        addMsg(text, 'user');
        cbHistory.push({role:'user', content:text});
        var typing = addMsg('Thinking…', 'bot typing');
        sendBtn.disabled = true;

        var priorHistory = cbHistory.slice(0, -1);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', chatUrl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            typing.remove();
            sendBtn.disabled = false;
            if(xhr.status !== 200){
                addMsg('Server error (' + xhr.status + '). Please try again.', 'bot');
                return;
            }
            try {
                var d = JSON.parse(xhr.responseText);
                if(d.error){ addMsg('Error: ' + d.error, 'bot'); return; }
                var reply = d.reply || 'Sorry, something went wrong.';
                addMsg(reply, 'bot');
                cbHistory.push({role:'assistant', content:reply});
                if(cbHistory.length > 20) cbHistory = cbHistory.slice(-20);
            } catch(ex){
                addMsg('Parse error: ' + xhr.responseText.substring(0, 200), 'bot');
            }
        };
        xhr.onerror = function(){
            typing.remove();
            sendBtn.disabled = false;
            addMsg('Network error. Please try again.', 'bot');
        };
        xhr.send('message=' + encodeURIComponent(text) + '&history=' + encodeURIComponent(JSON.stringify(priorHistory)));
    }

    function addMsg(text, cls){
        var d = document.createElement('div');
        d.className = 'cb-msg ' + cls;
        d.textContent = text;
        msgs.appendChild(d);
        msgs.scrollTop = msgs.scrollHeight;
        return d;
    }
})();
</script>
<!-- ── End Chatbot ─────────────────────────────────────────────────────── -->

</body>
</html>

<script>
    //=----Third party cookie Checking for Tableau-----
     window.onload = function (){
        var receiveMessage = function (evt) {
          if (evt.data === 'tableau.completed') {
              $("#dashboardPopup").find('.modal-body').html('<div style="text-align:center; padding:50px;"><div ><img style="max-width: 20%;" src="/images/warning-sign.png"></div> <div style="padding-top:20px;font-size: 15px;color: #000;">Please enable cookies and clear the browser cache!</div> </div>');
                tableauDashboardVisible = false;
                $('.refreshTableau').hide();
          }
        };

        window.addEventListener("message", receiveMessage, false);
    };
    //-----------------------------------------------------
</script>


 <!-- src="embedding.3.0.js" -->
  <script type="module" src="https://prod-apnortheast-a.online.tableau.com/javascripts/api/tableau.embedding.3.latest.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js" integrity="sha512-E8QSvWZ0eCLGk4km3hxSsNmGWbLtSCSUcewDQPQWZF6pEU8GlT8a5fF32wOl1i8ftdMhssTrF/OhyGWwonTcXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>



<style>
    

</style>


<script type="text/javascript">
    $(function(){
        var type = window.location.hash.substr(1);
        if(type!=''){
            setTimeout(function() {
                $('#'+type).trigger('click');
            },1000);
        }
    });
</script>
<script type="text/javascript" >
    $(document).ready(function()
    {
        $("#notificationLink").click(function()
        {
            $("#notificationContainer").fadeToggle(300);
            $("#notification_count").fadeOut("slow");
            var item='all';
            var userid='<?php echo Yii::$app->user->Id;?>';
            $.ajax({
                    type: 'POST',
                    url: '../Task/changestatus',
                    data: { item: item,userid:userid}
            });
            return false;

        });

        $("#settings").click(function()
        {
            $("#settingsContainer").fadeToggle(300);
        });

        //Document Click hiding the popup
        /*$(document).click(function()
        {
            $("#notificationContainer").hide();
            //$("#settingsContainer").toggle();
        });*/
        $('#clearnotif').click(function() {
            //$("#notificationContainer").fadeOut(300);
            var userid='<?php echo Yii::$app->user->Id;?>';
            $.ajax({
                type: 'POST',
                url: '../site/clearnotifications',
                data: { userid:userid},
                dataType: "json",
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#notificationsBody').html('');
                    }

                }
            });
        });

        //Popup on click
        /*$("#notificationContainer").click(function()
        {
            return false;
        });*/

    });
    $(document).on('click','.readnotification',function(){
        var item=$(this).data('id');
        var userid='<?php echo Yii::$app->user->Id;?>';
        $.ajax({
                type: 'POST',
                url: '../Task/changestatus',
                data: { item: item,userid:userid}
        })
    });
    
    
    function closeFrame(){
             setTimeout(function(){
                   $('.approveOrder-cntnt').removeClass('active');
            },500);
        }
    function closeFrame2(){
         setTimeout(function(){
              //$('.acco-cart input[type=radio]').trigger('click');
              $('.placeOrderPop-cntnt .icon-close').trigger('click');
              $('#cartsearch').trigger('click');
               
        },500);
    }
    /*function refreshParentWindow(){
            alert('sdfsdf');
             setTimeout(function(){
                   $('.acco-cart input[type=radio]').trigger('click');
                   $('#cartsearch').trigger('click');
            },1000);
        }*/
    
    
</script>

    <script>
        CKEDITOR.replace( 'orderbody', {
            // Define the toolbar: http://docs.ckeditor.com/#!/guide/dev_toolbar
            // The full preset from CDN which we used as a base provides more features than we need.
            // Also by default it comes with a 3-line toolbar. Here we put all buttons in a single row.
            toolbar: [
                { name: 'clipboard', items: [ 'Undo', 'Redo' ] },
                { name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'CopyFormatting' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                { name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                { name: 'links', items: [ 'Link', 'Unlink' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
                { name: 'insert', items: [ 'Image', 'Table' ] },
                { name: 'tools', items: [ 'Maximize' ] },
                { name: 'editing', items: [ 'Scayt' ] }
            ],

            // Since we define all configuration options here, let's instruct CKEditor to not load config.js which it does by default.
            // One HTTP request less will result in a faster startup time.
            // For more information check http://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-customConfig
            customConfig: '',

            // Sometimes applications that convert HTML to PDF prefer setting image width through attributes instead of CSS styles.
            // For more information check:
            //  - About Advanced Content Filter: http://docs.ckeditor.com/#!/guide/dev_advanced_content_filter
            //  - About Disallowed Content: http://docs.ckeditor.com/#!/guide/dev_disallowed_content
            //  - About Allowed Content: http://docs.ckeditor.com/#!/guide/dev_allowed_content_rules
            disallowedContent: 'img{width,height,float}',
            extraAllowedContent: 'img[width,height,align]',

            // Enabling extra plugins, available in the full-all preset: http://ckeditor.com/presets-all
            //extraPlugins: 'tableresize,uploadimage,uploadfile',

            /*********************** File management support ***********************/
            // In order to turn on support for file uploads, CKEditor has to be configured to use some server side
            // solution with file upload/management capabilities, like for example CKFinder.
            // For more information see http://docs.ckeditor.com/#!/guide/dev_ckfinder_integration

            // Uncomment and correct these lines after you setup your local CKFinder instance.
            // filebrowserBrowseUrl: 'http://example.com/ckfinder/ckfinder.html',
            // filebrowserUploadUrl: 'http://example.com/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
            /*********************** File management support ***********************/

            // Make the editing area bigger than default.
            width:555,height:542,

            // An array of stylesheets to style the WYSIWYG area.
            // Note: it is recommended to keep your own styles in a separate file in order to make future updates painless.
            contentsCss: [ 'https://cdn.ckeditor.com/4.6.0-441b33b/full-all/ckeditor/contents.css'],

            // This is optional, but will let us define multiple different styles for multiple editors using the same CSS file.
            bodyClass: 'document-editor',

            // Reduce the list of block elements listed in the Format dropdown to the most commonly used.
            format_tags: 'p;h1;h2;h3;pre',

            // Simplify the Image and Link dialog windows. The "Advanced" tab is not needed in most cases.
            removeDialogTabs: 'image:advanced;link:advanced',

            // Define the list of styles which should be available in the Styles dropdown list.
            // If the "class" attribute is used to style an element, make sure to define the style for the class in "mystyles.css"
            // (and on your website so that it rendered in the same way).
            // Note: by default CKEditor looks for styles.js file. Defining stylesSet inline (as below) stops CKEditor from loading
            // that file, which means one HTTP request less (and a faster startup).
            // For more information see http://docs.ckeditor.com/#!/guide/dev_styles
            stylesSet: [
                /* Inline Styles */
                { name: 'Marker', element: 'span', attributes: { 'class': 'marker' } },
                { name: 'Cited Work', element: 'cite' },
                { name: 'Inline Quotation', element: 'q' },

                /* Object Styles */
                {
                    name: 'Special Container',
                    element: 'div',
                    styles: {
                        padding: '5px 10px',
                        background: '#eee',
                        border: '1px solid #ccc'
                    }
                },
                {
                    name: 'Compact table',
                    element: 'table',
                    attributes: {
                        cellpadding: '5',
                        cellspacing: '0',
                        border: '1',
                        bordercolor: '#ccc'
                    },
                    styles: {
                        'border-collapse': 'collapse',
                        'width':'100%'
                    }
                },
                { name: 'Borderless Table', element: 'table', styles: { 'border-style': 'hidden', 'background-color': '#E6E6FA' } },
                { name: 'Square Bulleted List', element: 'ul', styles: { 'list-style-type': 'square' } }
            ]
        } );
    </script>
    
    

<!-- Performance Dashboard Modal -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@700;800&family=Barlow+Condensed:wght@400;500;600;700;800&family=Barlow:wght@300;400;500;600&display=swap');
/* ── Modal shell ──────────────────────────────────────────────────────────── */
#pd-bk{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9998}
#pd-bk.pd-open{display:block}
#pd-modal{display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:78vw;max-width:1180px;height:88vh;z-index:9999;border-radius:6px;overflow:hidden;background:#f0f2f7;box-shadow:0 8px 32px rgba(0,0,0,.7);flex-direction:column}
#pd-modal.pd-open{display:flex}
#pd-hdr{background:linear-gradient(180deg,#0c1535 0%,#05091c 100%);padding:5px 16px;display:flex;align-items:center;border-bottom:1px solid #2a4a8a;flex-shrink:0}
#pd-close{background:none;border:none;color:#fff;opacity:.85;font-size:18px;line-height:1;cursor:pointer;padding:0 3px;margin-left:6px}
#pd-close:hover{opacity:1}
#pd-body{flex:1;min-height:0;overflow:hidden;padding:6px 10px 0;display:flex;flex-direction:column;background:#e2e4e8}
/* ── Grid ─────────────────────────────────────────────────────────────────── */
#pd-grid{flex:1;min-height:0;display:flex;gap:8px;}
#pd-left{flex:1.8;display:flex;flex-direction:column;min-height:0;gap:2px;}
#pd-left .panel{flex:1;min-height:0;}
#pd-left .pb{padding-right:4px;overflow-x:hidden !important;overflow-y:auto !important;}
#pd-left .pb::-webkit-scrollbar{width:4px;}
#pd-left .pb::-webkit-scrollbar-thumb{background:#c0c8d8;border-radius:2px;}
#pd-left .pb::-webkit-scrollbar-track{background:transparent;}
#pd-right{flex:2.2;display:grid;grid-template-columns:1fr 1fr;grid-template-rows:repeat(3,1fr);gap:8px;}
#pd-tip{position:fixed;z-index:10002;background:#0c1535;color:#e8ecf4;font-family:'Barlow Condensed',sans-serif;font-size:12px;line-height:1.4;padding:6px 18px;border-radius:4px;pointer-events:none;display:none;white-space:pre;box-shadow:0 3px 12px rgba(0,0,0,.4);min-width:320px;max-width:420px;}
/* ── Panel ────────────────────────────────────────────────────────────────── */
.dash-modal .panel{background:#fff;border:none;border-right:1px solid #c8d0e0;overflow:hidden;display:flex;flex-direction:column;min-height:0}
.dash-modal .ph{background:linear-gradient(180deg,#0c1535 0%,#05091c 100%);padding:3px 6px;font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;letter-spacing:.7px;text-align:center;text-transform:uppercase;color:#fff;flex-shrink:0}
.dash-modal .pb{flex:1;min-height:0;overflow:hidden;padding:4px 7px 3px}
#pd-c2{overflow:auto !important;}
/* ── Legend ───────────────────────────────────────────────────────────────── */
.dash-modal .leg{display:flex;gap:6px;align-items:center;font-family:'Barlow Condensed',sans-serif;font-size:13px;color:#4a5a72;margin-bottom:3px}
.dash-modal .leg span{display:flex;align-items:center;gap:3px}
.dash-modal .ld{width:7px;height:7px;border-radius:50%;flex-shrink:0}
/* ── Bar rows ─────────────────────────────────────────────────────────────── */
.dash-modal .brow{display:flex;align-items:center;margin-bottom:3px}
.dash-modal .brow.brow-active{background:#e8f0fe;border-radius:3px}
.dash-modal .brow.brow-active .blbl{color:#0d1f6e;font-weight:700}
.dash-modal .blbl{font-family:'Barlow Condensed',sans-serif;font-size:13px;color:#4a5a72;width:160px;min-width:160px;text-align:left;padding-left:4px;padding-right:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.dash-modal .btrk{flex:1;min-width:40px;max-width:60%;height:17px;background:transparent;border-radius:2px;display:flex;overflow:visible}
.dash-modal .bs{height:100%;display:flex;align-items:center;justify-content:center;font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;min-width:14px}
/* bar colors set inline by renderBars */
.dash-modal .baxis{display:flex;justify-content:space-between;padding-left:166px;margin-top:2px;font-family:'Barlow Condensed',sans-serif;font-size:11px;color:#5a6e8c}
/* ── Gauge panels ─────────────────────────────────────────────────────────── */
.dash-modal .hist{font-family:'Barlow Condensed',sans-serif;font-size:11px;color:#5a6e8c;text-align:right;padding:1px 5px 0;flex-shrink:0}
.dash-modal .gp{flex:1;min-height:0;display:flex;flex-direction:column;align-items:center;padding:2px 4px 2px;overflow:hidden;position:relative;background:#fff}
.dash-modal .gp svg{display:block;width:100%;max-width:500px;max-height:500px;flex:1;min-height:40px}
.dash-modal .gvals{position:absolute;bottom:28px;left:0;right:0;display:flex;justify-content:space-between;padding:0 8px}
.dash-modal .gvl{font-family:'Barlow Condensed',sans-serif;text-align:left;line-height:1.3}
.dash-modal .gvr{font-family:'Barlow Condensed',sans-serif;text-align:right;line-height:1.3}
.dash-modal .glbl{font-size:13px;color:#5a6e8c;display:inline}
.dash-modal .gnum{font-size:17px;font-weight:700;display:inline;margin-left:2px}
.dash-modal .gsub{position:absolute;bottom:8px;left:0;right:0;font-family:'Barlow Condensed',sans-serif;font-size:13px;color:#5a6e8c;text-align:center;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;padding:0 4px}
/* ── Cause of Delay ───────────────────────────────────────────────────────── */
.dash-modal .dbody{flex:1;min-height:0;display:flex;flex-direction:column;align-items:center;padding:2px 5px 3px}
.dash-modal .dbody canvas{width:100%!important;flex:1;min-height:0}
/* ── Resource Capacity ────────────────────────────────────────────────────── */
.dash-modal .resbody{flex:1;min-height:0;padding:4px 7px 3px;display:flex;flex-direction:column}
.dash-modal .resbars{flex:1;min-height:0;display:flex;align-items:flex-end;gap:4px;border-bottom:1px solid #c8d0e0}
.dash-modal .rescol{display:flex;flex-direction:column;align-items:center;flex:1;gap:1px}
.dash-modal .resn{font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:700;color:#1a2540}
.dash-modal .resb{width:100%;border-radius:2px 2px 0 0}
.dash-modal .reslabels{display:flex;gap:3px;margin-top:3px;flex-shrink:0}
.dash-modal .reslbl{flex:1;font-family:'Barlow Condensed',sans-serif;font-size:10px;color:#4a5a72;text-align:center;line-height:1.2;overflow:hidden}
.dash-modal .resfoot{text-align:center;font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:400;color:#1a2540;margin-top:2px;flex-shrink:0}
/* ── Tasks chip & tooltip ──────────────────────────────────────────────────── */
.pd-tasks-chip{position:absolute;left:6px;top:6px;font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;letter-spacing:.6px;color:#1a2540;border:1px solid #b0bec5;border-radius:3px;padding:2px 8px;cursor:pointer;text-transform:uppercase;background:#fff;z-index:10;}
.pd-tasks-chip:hover{background:#f0f4ff;border-color:#3461b8;color:#3461b8;}
#pd-tasks-tip{position:fixed;z-index:10000;background:#0c1535;border:1px solid #263d6e;border-radius:8px;box-shadow:0 8px 28px rgba(0,0,0,.6);padding:14px 16px 12px;display:none;pointer-events:auto;box-sizing:border-box;overflow-y:auto;}
#pd-tasks-tip .tip-title{font-family:'Barlow Condensed',sans-serif;font-size:17px;font-weight:700;color:#a8d4f5;text-transform:uppercase;letter-spacing:.7px;margin-bottom:10px;border-bottom:1px solid rgba(255,255,255,.15);padding-bottom:7px;}
#pd-tasks-tip table{width:100%;border-collapse:collapse;}
#pd-tasks-tip table th{font-family:'Barlow Condensed',sans-serif;font-size:12px;color:#7aafd4 !important;text-transform:uppercase;letter-spacing:.5px;font-weight:600;padding:0 10px 8px 0;}
#pd-tasks-tip table td{font-family:'Barlow Condensed',sans-serif;font-size:16px;color:#e8f0fc !important;padding:6px 10px 6px 0;border-top:1px solid rgba(255,255,255,.07);}
#pd-tasks-tip table td:not(:first-child){text-align:right;padding-right:0;}
</style>
<style>
#cd-bk{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9998}
#cd-bk.cd-open{display:block}
#cd-modal{display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:78vw;max-width:1180px;height:88vh;z-index:9999;border-radius:6px;overflow:hidden;background:#f0f2f7;box-shadow:0 8px 32px rgba(0,0,0,.7);flex-direction:column}
#cd-modal.cd-open{display:flex}
#cd-hdr{background:linear-gradient(180deg,#0c1535 0%,#05091c 100%);padding:5px 16px;display:flex;align-items:center;border-bottom:1px solid #2a4a8a;flex-shrink:0}
#cd-close{background:none;border:none;color:#fff;opacity:.85;font-size:18px;line-height:1;cursor:pointer;padding:0 3px;margin-left:6px}
#cd-close:hover{opacity:1}
#cd-body{flex:1;min-height:0;overflow:hidden;padding:6px 10px 0;display:flex;flex-direction:column;background:#e2e4e8}
#cd-grid{flex:1;min-height:0;display:grid;grid-template-columns:1.8fr 1.1fr 1.1fr;grid-template-rows:repeat(12,1fr);row-gap:0;column-gap:0}
#cd-modal .ph{background:#222222;}
#cd-modal .panel{border-right:3px solid #a0aec0}
#cd-c2,#cd-c1,#cd-c3,#cd-c4{overflow-y:auto !important;}
#cd-c4 .brow, #cd-c3 .brow, #cd-c1 .brow{margin-bottom:0 !important;}

</style>
<div id="cd-bk"></div>
<div id="cd-modal" class="dash-modal">
  <div id="cd-hdr">
    <span id="cd-title" style="flex:1;color:#ffffff;font-family:'Nunito',sans-serif;font-size:16px;font-weight:700;letter-spacing:.5px;text-align:center"><?php echo htmlspecialchars($ProjectName); ?> — Cost Dashboard</span>
    <button id="cd-close">&times;</button>
  </div>
  <div id="cd-body">
    <div id="cd-grid">
      <div class="panel" style="grid-column:1;grid-row:1/span 3"><div class="ph">Project Cost</div><div class="pb" id="cd-c2"></div></div>
      <div class="panel" style="grid-column:1;grid-row:4/span 2"><div class="ph">Group Cost</div><div class="pb" id="cd-c1"></div></div>
      <div class="panel" style="grid-column:1;grid-row:6/span 3"><div class="ph">IOW Cost</div><div class="pb" id="cd-c3"></div></div>
      <div class="panel" style="grid-column:1;grid-row:9/span 4"><div class="ph">Activity Costs</div><div class="pb" id="cd-c4"></div></div>
      <div class="panel" style="grid-column:2;grid-row:1/span 4"><div class="ph">Cost of Activity</div><div class="gp" id="cd-g2"></div></div>
      <div class="panel" style="grid-column:3;grid-row:1/span 4"><div class="ph">Quantity of Work Done</div><div class="gp" id="cd-g4"></div></div>
      <div class="panel" style="grid-column:2;grid-row:5/span 4"><div class="ph">Unit Cost of Activity</div><div class="gp" id="cd-g5"></div></div>
      <div class="panel" style="grid-column:3;grid-row:5/span 4"><div class="ph">Resource Cost</div><div class="resbody" id="cd-rcost"></div></div>
      <div class="panel" style="grid-column:2;grid-row:9/span 4"><div class="ph">Unit Cost of Resource</div><div class="resbody" id="cd-c6"></div></div>
      <div class="panel" style="grid-column:3;grid-row:9/span 4"><div class="ph">Resource Consumption</div><div class="resbody" id="cd-c7"></div></div>
    </div>
  </div>
</div>
<div id="pd-bk"></div>
<div id="pd-modal" class="dash-modal">
  <div id="pd-hdr">
    <span id="pd-title" style="flex:1;color:#ffffff;font-family:'Nunito',sans-serif;font-size:16px;font-weight:700;letter-spacing:.5px;text-align:center">Performance Dashboard</span>
    <button id="pd-close">&times;</button>
  </div>
  <div id="pd-body">
    <div id="pd-grid">
      <div id="pd-left">
        <div class="panel"><div class="ph">MAJOR GROUPS</div><div class="pb" id="pd-c1"></div></div>
        <div class="panel"><div class="ph">IOW</div><div class="pb" id="pd-c3"></div></div>
        <div class="panel"><div class="ph">ONGOING ACTIVITIES</div><div class="pb" id="pd-c4"></div></div>
        <div class="panel"><div class="ph">UPCOMING ACTIVITIES</div><div class="pb" id="pd-c5"></div></div>
      </div>
      <div id="pd-right">
        <div class="panel"><div class="ph">Project Duration</div><div class="pb" id="pd-c2"></div></div>
        <div class="panel"><div class="ph">Activity Duration</div><div class="pb" id="pd-g6"><span style="font-size:9px;color:#aaa">Loading&hellip;</span></div></div>
        <div class="panel"><div class="ph">Target Production</div><div class="gp" id="pd-g2"><span style="font-size:9px;color:#aaa">Loading&hellip;</span></div></div>
        <div class="panel"><div class="ph">Activity Productivity</div><div class="gp" id="pd-g3"><span style="font-size:9px;color:#aaa">Loading&hellip;</span></div></div>
        <div class="panel"><div class="ph">Capacity Utilisation</div><div class="gp" id="pd-g5"><span style="font-size:9px;color:#aaa">Loading&hellip;</span></div></div>
        <div class="panel"><div class="ph">Cycle Time</div><div class="gp" id="pd-g4"><span style="font-size:9px;color:#aaa">Loading&hellip;</span></div></div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/_performancedashboard.js?v=<?php echo time();?>"></script>

<!-- ══════════════════════════════════════════════════════════════════════
     WBS / IOW ENTRY MODAL
════════════════════════════════════════════════════════════════════════ -->
<style>
#qe-bk{display:none;position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:10000}
#qe-bk.qe-open{display:block}
#qe-modal{
  display:none;position:fixed;top:50%;left:50%;
  transform:translate(-50%,-50%);
  width:860px;max-width:95vw;max-height:88vh;
  z-index:10001;border-radius:10px;overflow:hidden;
  background:#edf0f4;box-shadow:0 12px 40px rgba(0,0,0,.75);
  flex-direction:column;
  font-family:'Barlow',sans-serif;
}
#qe-modal.qe-open{display:flex}
#qe-hdr{display:none}
#qe-close{display:none}
#qe-body{flex:1;overflow-y:auto;padding:16px 20px 20px;display:flex;flex-direction:column;gap:0}

/* Section wrapper */
.qe-section{background:#fff;border-radius:4px;border:1px solid #a0aab8;margin-bottom:0;overflow:hidden}
.qe-section+.qe-section{margin-top:14px}
.qe-sec-hdr{display:none}
.qe-sec-body{padding:14px 16px}

/* Label + field */
.qe-row{display:flex;flex-wrap:wrap;gap:14px 16px;align-items:flex-end}
.qe-field{display:flex;flex-direction:column;min-width:80px}
.qe-field.wide{flex:1 1 180px}
.qe-field.med{flex:1 1 120px}
.qe-field.sm{flex:0 0 100px}
.qe-field.xs{flex:0 0 80px}
.qe-label{font-size:11px;font-weight:900;color:#000;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px}
.qe-input,.qe-select{
  width:100%;padding:8px 10px;font-size:13px;color:#2d3748;
  border:1px solid #a0aab8;border-radius:3px;background:#fff;
  outline:none;box-sizing:border-box;font-family:'Barlow',sans-serif;
  transition:border-color .15s;height:38px;
}
.qe-input:focus,.qe-select:focus{border-color:#4a5568;background:#fff}
.qe-input[readonly]{background:#edf0f4;color:#666;cursor:default}
.qe-persist-note{font-size:9px;color:#888;font-style:italic;margin-top:2px}

/* Divider */
.qe-divider{border:none;border-top:1px solid #a0aab8;margin:12px 0}

/* Repeating rows (tasks / resources) */
.qe-repeat-tbl{width:100%;border-collapse:collapse}
.qe-repeat-tbl th{
  font-size:11px;font-weight:900;color:#000;text-transform:uppercase;
  letter-spacing:.4px;padding:0 6px 8px 0;border-bottom:1px solid #a0aab8;
  white-space:nowrap;
}
.qe-repeat-tbl td{padding:6px 6px 6px 0;vertical-align:middle}
.qe-repeat-tbl td:last-child{padding-right:0}
.qe-repeat-tbl input,.qe-repeat-tbl select{
  width:100%;padding:7px 9px;font-size:13px;color:#2d3748;
  border:1px solid #a0aab8;border-radius:3px;background:#fff;
  outline:none;font-family:'Barlow',sans-serif;transition:border-color .15s;
  box-sizing:border-box;height:36px;
}
.qe-repeat-tbl input:focus,.qe-repeat-tbl select:focus{border-color:#4a5568}
.qe-repeat-tbl input[readonly]{background:#edf0f4;color:#666;cursor:default}
.qe-add-btn{
  display:inline-flex;align-items:center;justify-content:center;
  width:22px;height:22px;border-radius:50%;border:none;cursor:pointer;
  background:#6b7a93;color:#fff;font-size:16px;line-height:1;
  transition:background .15s;flex-shrink:0;
}
.qe-add-btn:hover{background:#4a5568}
.qe-del-btn{
  display:inline-flex;align-items:center;justify-content:center;
  width:18px;height:18px;border-radius:50%;border:none;cursor:pointer;
  background:#e67e22;color:#fff;font-size:13px;line-height:1;
  flex-shrink:0;opacity:.9;
}
.qe-del-btn:hover{opacity:1;background:#ca6f1e}

/* Footer bar */
#qe-footer{
  background:#dce1ea;border-top:1px solid #a0aab8;
  padding:8px 16px;display:flex;align-items:center;gap:12px;flex-shrink:0;
}
#qe-duration-display{
  flex:1;font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:600;color:#2d3748;
}
#qe-duration-display span{color:#4a5568;margin-left:4px}
#qe-save-msg{font-size:11px;color:#27ae60;display:none}
#qe-btn-add{
  background:#00838f;color:#fff;border:none;border-radius:4px;
  padding:6px 22px;font-size:12px;font-weight:700;font-family:'Nunito',sans-serif;
  cursor:pointer;letter-spacing:.3px;text-transform:uppercase;
}
#qe-btn-add:hover{background:#006b75}
#qe-btn-add:disabled{background:#aaa;cursor:default}
</style>

<div id="qe-bk"></div>
<div id="qe-modal">
  <div id="qe-hdr">
    <span>Add Activity to Schedule</span>
    <button id="qe-close">&times;</button>
  </div>
  <div id="qe-body">

    <!-- ── SECTION 1 : Project Type + Group (persists) ─────────────── -->
    <div class="qe-section">
      <div class="qe-sec-hdr">Project Type &amp; Group <span style="font-weight:400;opacity:.7">&mdash; stays selected until you change it</span></div>
      <div class="qe-sec-body">
        <div class="qe-row">
          <div class="qe-field wide">
            <span class="qe-label">Project Type</span>
            <select id="qe-proj-type" class="qe-select">
              <option value="">— Select Project Type —</option>
            </select>
          </div>
          <div class="qe-field wide">
            <span class="qe-label">Group</span>
            <select id="qe-group" class="qe-select">
              <option value="">— Select Group —</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- ── SECTION 2 : IOW + Activity (cascading) ────────────────────── -->
    <div class="qe-section">
      <div class="qe-sec-hdr">IOW &amp; Activity</div>
      <div class="qe-sec-body">
        <div class="qe-row">
          <div class="qe-field wide">
            <span class="qe-label">IOW</span>
            <select id="qe-iow" class="qe-select">
              <option value="">— Select IOW —</option>
            </select>
          </div>
          <div class="qe-field wide">
            <span class="qe-label">Activity</span>
            <select id="qe-activity" class="qe-select">
              <option value="">— Select Activity —</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- ── SECTION 3 : Activity Details ──────────────────────────────── -->
    <div class="qe-section">
      <div class="qe-sec-hdr">Activity Details</div>
      <div class="qe-sec-body">
        <div class="qe-row">
          <div style="font-size:9px;font-weight:700;color:#4a5568;text-transform:uppercase;letter-spacing:.5px;width:100%;margin-bottom:2px">Estimate</div>
          <div class="qe-field sm">
            <span class="qe-label">Unit</span>
            <input id="qe-unit" type="text" class="qe-input" placeholder="e.g. m³">
          </div>
          <div class="qe-field xs">
            <span class="qe-label">Quantity</span>
            <input id="qe-qty" type="number" class="qe-input" placeholder="0" step="0.001">
          </div>
          <div class="qe-field xs">
            <span class="qe-label">Rate</span>
            <input id="qe-rate" type="number" class="qe-input" placeholder="0.00" step="0.01">
          </div>
          <div class="qe-field xs">
            <span class="qe-label">Amount</span>
            <input id="qe-amount" type="number" class="qe-input" placeholder="0.00" readonly>
          </div>
          <div style="width:30px"></div>
          <div style="font-size:9px;font-weight:700;color:#4a5568;text-transform:uppercase;letter-spacing:.5px;align-self:flex-end;padding-bottom:5px">Schedule</div>
          <div class="qe-field sm">
            <span class="qe-label">Schedule Unit</span>
            <input id="qe-sch-unit" type="text" class="qe-input" placeholder="e.g. Nos">
          </div>
          <div class="qe-field xs">
            <span class="qe-label">Schedule Qty</span>
            <input id="qe-sch-qty" type="number" class="qe-input" placeholder="0" step="0.001">
          </div>
        </div>
      </div>
    </div>

    <!-- ── SECTION 4 : Tasks ─────────────────────────────────────────── -->
    <div class="qe-section">
      <div class="qe-sec-hdr">Tasks <span style="font-weight:400;opacity:.7">&mdash; duration is computed from these</span></div>
      <div class="qe-sec-body">
        <table class="qe-repeat-tbl" id="qe-task-tbl">
          <thead>
            <tr>
              <th style="width:36%">Task Name</th>
              <th style="width:14%">Unit</th>
              <th style="width:18%">Productivity / Day</th>
              <th style="width:16%">Resource Units</th>
              <th style="width:16%;text-align:right">
                <button class="qe-add-btn" id="qe-task-add" title="Add task row">+</button>
              </th>
            </tr>
          </thead>
          <tbody id="qe-task-body">
            <!-- rows injected by JS -->
          </tbody>
        </table>
      </div>
    </div>

    <!-- ── SECTION 5 : Resources ──────────────────────────────────────── -->
    <div class="qe-section">
      <div class="qe-sec-hdr">Resources</div>
      <div class="qe-sec-body">
        <table class="qe-repeat-tbl" id="qe-res-tbl">
          <thead>
            <tr>
              <th style="width:20%">Resource Type</th>
              <th style="width:28%">Resource Name</th>
              <th style="width:12%">Quantity</th>
              <th style="width:14%">Rate</th>
              <th style="width:14%">Amount</th>
              <th style="width:12%;text-align:right">
                <button class="qe-add-btn" id="qe-res-add" title="Add resource row">+</button>
              </th>
            </tr>
          </thead>
          <tbody id="qe-res-body">
            <!-- rows injected by JS -->
          </tbody>
        </table>
      </div>
    </div>

  </div><!-- /qe-body -->

  <div id="qe-footer">
    <div id="qe-duration-display">Computed Duration: <span id="qe-dur-val">&mdash;</span> days</div>
    <div id="qe-save-msg">&#10003; Activity added to Gantt!</div>
    <button id="qe-btn-add">&#43; Add to Gantt</button>
    <button id="qe-close" style="background:#e67e22;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;">&#10005; Close</button>
  </div>
</div><!-- /qe-modal -->

<script>
(function(){
'use strict';

/* ── open / close ── */
function openModal(){
  document.getElementById('qe-bk').classList.add('qe-open');
  document.getElementById('qe-modal').classList.add('qe-open');
  loadProjTypes();
  if(!document.querySelector('#qe-task-body tr')) addTaskRow();
  if(!document.querySelector('#qe-res-body tr'))  { loadResTypes(function(){ addResRow(); }); }
  recalcDuration();
}
window.openQeModal  = openModal;
window.loadIows     = loadIows;
window.loadActivities = loadActivities;
function closeModal(){
  document.getElementById('qe-bk').classList.remove('qe-open');
  document.getElementById('qe-modal').classList.remove('qe-open');
}

/* ── cascading dropdowns ── */

function loadProjTypes(){
  var sel = document.getElementById('qe-proj-type');
  if(sel.options.length > 1) return;
  $.ajax({
    type:'POST', url:'../projectsmain/getwbtypelist', dataType:'json',
    success: function(d){
      (d.items||[]).forEach(function(item){
        var o = document.createElement('option');
        o.value = item.id; o.textContent = item.name;
        sel.appendChild(o);
      });
    }
  });
}

function loadGroups(typeId){
  var sel = document.getElementById('qe-group');
  sel.innerHTML = '<option value="">— Select Group —</option>';
  document.getElementById('qe-iow').innerHTML = '<option value="">— Select IOW —</option>';
  document.getElementById('qe-activity').innerHTML = '<option value="">— Select Activity —</option>';
  if(!typeId) return;
  $.ajax({
    type:'POST', url:'../projectsmain/getwbgrouplist', dataType:'json',
    data:{typeId: typeId},
    success: function(d){
      (d.items||[]).forEach(function(item){
        var o = document.createElement('option');
        o.value = item.id; o.textContent = item.name;
        sel.appendChild(o);
      });
    }
  });
}

function loadIows(groupId){
  var sel = document.getElementById('qe-iow');
  sel.innerHTML = '<option value="">— Select IOW —</option>';
  document.getElementById('qe-activity').innerHTML = '<option value="">— Select Activity —</option>';
  if(!groupId) return;
  $.ajax({
    type:'POST', url:'../projectsmain/getiowbygroup', dataType:'json',
    data:{groupId: groupId},
    success: function(d){
      (d.items||[]).forEach(function(item){
        var o = document.createElement('option');
        o.value = item.id; o.textContent = item.name;
        sel.appendChild(o);
      });
    }
  });
}

function loadActivities(iowId){
  var sel = document.getElementById('qe-activity');
  sel.innerHTML = '<option value="">— Select Activity —</option>';
  if(!iowId) return;
  $.ajax({
    type:'POST', url:'../projectsmain/getiowactivities', dataType:'json',
    data:{iowId: iowId},
    success: function(d){
      (d.items||[]).forEach(function(item){
        var o = document.createElement('option');
        o.value = item.id; o.textContent = item.name;
        sel.appendChild(o);
      });
    }
  });
}

/* ── duration calculation ── */
function recalcDuration(){
  var estQty = parseFloat(document.getElementById('qe-qty').value) || 0;
  var schQty = parseFloat(document.getElementById('qe-sch-qty').value) || 0;
  var qtyPerUnit = (schQty > 0) ? estQty / schQty : 0;

  var cycleDays = 0;
  document.querySelectorAll('#qe-task-body tr').forEach(function(tr){
    var prod     = parseFloat(tr.querySelector('.qe-task-prod')     ? tr.querySelector('.qe-task-prod').value     : 0) || 0;
    var resUnits = parseFloat(tr.querySelector('.qe-task-resunits') ? tr.querySelector('.qe-task-resunits').value : 0) || 0;
    if(prod > 0 && resUnits > 0 && qtyPerUnit > 0){
      cycleDays += (qtyPerUnit / prod) / resUnits;
    }
  });

  var duration = (schQty > 0 && cycleDays > 0) ? Math.ceil(cycleDays * schQty) : 0;
  document.getElementById('qe-dur-val').textContent = duration > 0 ? duration : '—';
  return duration;
}

/* ── Resource Types ── */
var _resTypes = [];
function loadResTypes(cb){
  if(_resTypes.length){ if(cb) cb(); return; }
  $.ajax({
    type:'POST', url:'../projectsmain/getrestypelist', dataType:'json',
    success: function(d){
      _resTypes = d.items || [];
      if(cb) cb();
    }
  });
}

/* ── Task rows ── */
function addTaskRow(){
  var tbody = document.getElementById('qe-task-body');
  var tr = document.createElement('tr');
  tr.innerHTML =
    '<td><input type="text" class="qe-task-name" placeholder="Task name"></td>'+
    '<td><input type="text" class="qe-task-unit" placeholder="Unit"></td>'+
    '<td><input type="number" class="qe-task-prod" placeholder="0.00" step="0.001" min="0"></td>'+
    '<td><input type="number" class="qe-task-resunits" placeholder="1" step="1" min="1"></td>'+
    '<td style="text-align:right"><button class="qe-del-btn qe-task-del" title="Remove">&times;</button></td>';
  tbody.appendChild(tr);
  tr.querySelector('.qe-task-prod').addEventListener('input', recalcDuration);
  tr.querySelector('.qe-task-resunits').addEventListener('input', recalcDuration);
  tr.querySelector('.qe-task-del').addEventListener('click', function(){
    if(document.querySelectorAll('#qe-task-body tr').length > 1){ tr.remove(); recalcDuration(); }
  });
}

/* ── Resource rows ── */
function makeResTypeOptions(){
  var html = '<option value="">— Type —</option>';
  _resTypes.forEach(function(t){ html += '<option value="'+t.id+'">'+t.name+'</option>'; });
  return html;
}

function addResRow(){
  var tbody = document.getElementById('qe-res-body');
  var tr = document.createElement('tr');
  tr.innerHTML =
    '<td><select class="qe-res-type">'+makeResTypeOptions()+'</select></td>'+
    '<td><input type="text" class="qe-res-name" placeholder="Resource name"></td>'+
    '<td><input type="number" class="qe-res-qty" placeholder="0" step="0.001"></td>'+
    '<td><input type="number" class="qe-res-rate" placeholder="0.00" step="0.01"></td>'+
    '<td><input type="number" class="qe-res-amt" placeholder="0.00" readonly></td>'+
    '<td style="text-align:right"><button class="qe-del-btn qe-res-del" title="Remove">&times;</button></td>';
  tbody.appendChild(tr);

  var qtyEl  = tr.querySelector('.qe-res-qty');
  var rateEl = tr.querySelector('.qe-res-rate');
  var amtEl  = tr.querySelector('.qe-res-amt');
  function calcAmt(){
    var q = parseFloat(qtyEl.value)||0, r = parseFloat(rateEl.value)||0;
    amtEl.value = (q*r).toFixed(2);
  }
  qtyEl.addEventListener('input', calcAmt);
  rateEl.addEventListener('input', calcAmt);
  tr.querySelector('.qe-res-del').addEventListener('click', function(){
    if(document.querySelectorAll('#qe-res-body tr').length > 1) tr.remove();
  });
}

/* ── clear activity fields (keeps Project Type, Group, IOW, Activity selections) ── */
function clearActivityFields(){
  document.getElementById('qe-actname').value  = '';
  document.getElementById('qe-unit').value     = '';
  document.getElementById('qe-qty').value      = '';
  document.getElementById('qe-rate').value     = '';
  document.getElementById('qe-amount').value   = '';
  document.getElementById('qe-sch-unit').value = '';
  document.getElementById('qe-sch-qty').value  = '';
  document.getElementById('qe-task-body').innerHTML = '';
  document.getElementById('qe-res-body').innerHTML  = '';
  addTaskRow();
  loadResTypes(function(){ addResRow(); });
  recalcDuration();
}

/* ── collect form payload ── */
function collectPayload(){
  var tasks = [];
  document.querySelectorAll('#qe-task-body tr').forEach(function(tr){
    var name = tr.querySelector('.qe-task-name') ? tr.querySelector('.qe-task-name').value.trim() : '';
    if(!name) return;
    tasks.push({
      name:     name,
      unit:     tr.querySelector('.qe-task-unit').value.trim(),
      prod:     parseFloat(tr.querySelector('.qe-task-prod').value)     || 0,
      resunits: parseFloat(tr.querySelector('.qe-task-resunits').value) || 1
    });
  });
  var resources = [];
  document.querySelectorAll('#qe-res-body tr').forEach(function(tr){
    var name = tr.querySelector('.qe-res-name') ? tr.querySelector('.qe-res-name').value.trim() : '';
    if(!name) return;
    resources.push({
      type_id: tr.querySelector('.qe-res-type').value,
      name:    name,
      qty:     parseFloat(tr.querySelector('.qe-res-qty').value)  || 0,
      rate:    parseFloat(tr.querySelector('.qe-res-rate').value) || 0
    });
  });

  var iowSel      = document.getElementById('qe-iow');
  var actSel      = document.getElementById('qe-activity');
  var projTypeSel = document.getElementById('qe-proj-type');
  var groupSel    = document.getElementById('qe-group');

  return {
    proj_type_id: projTypeSel.value,
    group_id:     groupSel.value,
    iow_group_id: iowSel.value,
    iow_group:    iowSel.options[iowSel.selectedIndex] ? iowSel.options[iowSel.selectedIndex].text : '',
    iow_act_id:   actSel.value,
    iow_name:     actSel.options[actSel.selectedIndex] ? actSel.options[actSel.selectedIndex].text : '',
    act_name:     document.getElementById('qe-actname').value.trim(),
    unit:         document.getElementById('qe-unit').value.trim(),
    qty:          parseFloat(document.getElementById('qe-qty').value)     || 0,
    rate:         parseFloat(document.getElementById('qe-rate').value)    || 0,
    sch_unit:     document.getElementById('qe-sch-unit').value.trim(),
    sch_qty:      parseFloat(document.getElementById('qe-sch-qty').value) || 0,
    duration:     recalcDuration(),
    tasks:        tasks,
    resources:    resources
  };
}

/* ── Bind ── */
document.addEventListener('DOMContentLoaded', function(){

  /* open via .qe-btn class anywhere on page */
  document.addEventListener('click', function(e){
    if(e.target.closest('.qe-btn')){
      e.preventDefault();
      loadResTypes(function(){
        document.querySelectorAll('#qe-res-body .qe-res-type').forEach(function(sel){
          var cur = sel.value;
          sel.innerHTML = makeResTypeOptions();
          sel.value = cur;
        });
      });
      openModal();
    }
  });

  /* close */
  document.getElementById('qe-close').addEventListener('click', closeModal);
  document.getElementById('qe-bk').addEventListener('click', closeModal);

  /* cascade: Project Type → Groups */
  document.getElementById('qe-proj-type').addEventListener('change', function(){
    loadGroups(this.value);
  });

  /* cascade: Group → IOWs */
  document.getElementById('qe-group').addEventListener('change', function(){
    loadIows(this.value);
  });

  /* cascade: IOW → Activities */
  document.getElementById('qe-iow').addEventListener('change', function(){
    loadActivities(this.value);
  });

  /* estimate amount = qty × rate */
  function calcActivityAmount(){
    var q = parseFloat(document.getElementById('qe-qty').value)  || 0;
    var r = parseFloat(document.getElementById('qe-rate').value) || 0;
    document.getElementById('qe-amount').value = (q * r).toFixed(2);
  }
  document.getElementById('qe-qty').addEventListener('input', function(){ calcActivityAmount(); recalcDuration(); });
  document.getElementById('qe-rate').addEventListener('input', calcActivityAmount);
  document.getElementById('qe-sch-qty').addEventListener('input', recalcDuration);

  /* add task row */
  document.getElementById('qe-task-add').addEventListener('click', addTaskRow);

  /* add resource row */
  document.getElementById('qe-res-add').addEventListener('click', function(){
    loadResTypes(function(){ addResRow(); });
    if(_resTypes.length) addResRow();
  });

  /* ── Add to Gantt ── */
  document.getElementById('qe-btn-add').addEventListener('click', function(){
    var payload = collectPayload();
    if(!payload.iow_group_id){ alert('Please select an IOW.'); return; }
    if(!payload.act_name)    { alert('Please enter an Activity name.'); return; }
    if(payload.duration < 1) { alert('Duration is 0. Please fill in task productivity and resource units.'); return; }

    var btn = document.getElementById('qe-btn-add');
    btn.disabled = true; btn.textContent = 'Saving…';

    $.ajax({
      type:'POST', url:'../projectsmain/wbsadd',
      data:{ payload: JSON.stringify(payload) }, dataType:'json',
      success: function(d){
        btn.disabled = false; btn.textContent = '+ Add to Gantt';
        if(d.error && d.error !== 'No'){ alert('Error: ' + d.error); return; }
        var msg = document.getElementById('qe-save-msg');
        msg.style.display = 'block';
        setTimeout(function(){ msg.style.display = 'none'; }, 3000);
        clearActivityFields();
        if(typeof window.loadGantt === 'function') window.loadGantt();
      },
      error: function(){
        btn.disabled = false; btn.textContent = '+ Add to Gantt';
        alert('Server error — please try again.');
      }
    });
  });

});

})();
</script>
<?php $this->endPage() ?>
