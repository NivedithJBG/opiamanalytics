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
use app\models\Departments;
use app\models\UserTabs;
use app\models\DepartmentTab;

AppAsset::register($this);

$userVariable = Yii::$app->session->get('Saml');
 
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
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/custom.css" rel="stylesheet">
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
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/style.css?v=1.18" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer1.css" rel="stylesheet">
    <?php } ?>

    <!-- <link href="<//?php echo Yii::$app->request->baseUrl; ?>/cssnew/style.css?v=1.18" rel="stylesheet"> -->
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer.css?v=1.18" rel="stylesheet">
  <!--   <link href="<//?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer1.css" rel="stylesheet"> -->
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer2.css" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer3.css" rel="stylesheet">

    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer4.css" rel="stylesheet">

    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/dashboard.css" rel="stylesheet">
     <!--<link href="<?php //echo Yii::$app->request->baseUrl; ?>/cssnew/style.css?v=2.11" rel="stylesheet"> 
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
     -->
     
    <!--<script type="text/javascript" src="<?php //echo Yii::$app->request->baseUrl; ?>/jsnew/jquery-1.9.1.min.js"></script>-->

    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/jquery.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/jquery.min.js"></script>
    

    <!--<script type="text/javascript" src="<?php //echo Yii::$app->request->baseUrl; ?>/jsnew/jquery-ui.js"></script>-->
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/custom.js"></script>
    <!--<script type="text/javascript" src="<?php //echo Yii::$app->request->baseUrl; ?>/js/magicsuggest.js"></script>
    
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>-->
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/common.js?v=1.6"></script>
    
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/js/maxcdn.min.js"></script>
    <script src="//cdn.ckeditor.com/4.15.0/full/ckeditor.js"></script>
     
    <style type="text/css">
        span#clearnotif:hover {
            text-decoration: underline;
        }
    </style>

    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
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

<?php } elseif(Yii::$app->controller->id == 'procurement' && Yii::$app->controller->action->id == 'despatchorder') { ?>
<body class="procurement">

    <aside class="leftside">

        <?= $content ?>

    </aside>
<?php }

 elseif(Yii::$app->controller->id == 'procurement' && Yii::$app->controller->action->id == 'view') { ?>
<body class="procurement">

    <aside class="leftside">

        <?= $content ?>

    </aside>
<?php }


elseif(Yii::$app->controller->id == 'procurement' && Yii::$app->controller->action->id == 'order') { ?>
<body class="procurement">

    <aside class="leftside">

        <?= $content ?>

    </aside>

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
              <a class="navbar-brand opiam-logo" href="<?php echo Yii::$app->request->baseUrl; ?>/projectsmain/index" style="margin-top: 11px;">
                  <!--<img src="images/logo.jpg" width="" height="" alt />-->
                  <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/opiam-analytics-logo-trans.png" width="" height="" alt />
              </a>
            </div>
            <div class="collapse navbar-collapse headernav"  id="myNavbar">
               <?php if(Yii::$app->controller->id == 'procurement' && Yii::$app->controller->action->id == 'index') { ?>
             <h1 id="procurement-title-head">Procurement Process</h1>
               <?php }
               elseif(Yii::$app->controller->id == 'procurement' && Yii::$app->controller->action->id == 'masterindex') { ?>
               <h1>Procurement Masters</h1>
            
              <?php }
               elseif(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'masters') { ?>
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
                elseif(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'index') { ?>
                <h1 id="project-title-head">Projects</h1>
                <?php } elseif(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'tableau') { ?>
                <h1 id="project-title-head">Dashboard</h1>
                <?php } elseif(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'report') { ?>
                <h1 id="prjct_head">Project Managers Desk</h1>
            <?php } elseif(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'userrole') { ?>
                <h1 id="userprjct_head">User Role</h1>
                <?php } elseif(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'newganttchart') { ?>
                <h1>Gantt Chart</h1>
            <?php } elseif(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'dashboard') { ?>
                <h1 id="prj_name">Project Dashboard</h1>
            <?php } elseif(Yii::$app->controller->id == 'procurement' && Yii::$app->controller->action->id == 'reports') { ?>
                <h1>Procurement Reports</h1>
            <?php } elseif(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'reports') { ?>
                <h1>Project Reports</h1>
                 <?php } elseif(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'reporting') { ?>
                <h1>Reporting</h1>

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
                <li><a href="#" class="icon-menu1"></a></li>
              </ul>
              
              
              
              <ul class="nav navbar-nav navbar-right round-icons">
                <li><a class="icon-pie-chart" title="Tableau" href="<?php echo Yii::$app->urlManager->createUrl('projectsmain/tableau')?>"> 
                    </a>
                </li>

                <?php if(Yii::$app->controller->id == 'procurement' && Yii::$app->controller->action->id == 'index') { 

                    $user=User::find()->where(['id'=>Yii::$app->user->id])->one();

                    if($user->role_id !=0){

                        $functabs=UserTabs::find()->where(['function_id'=>2])->andWhere(['role_id'=>$user->role_id])->all();

                            $procu_master='display:none';
                           
                            $procu_report='display:none';
                            $procu_dashboard='display:none';

                            foreach($functabs as $functab){

                                if($functab->tab_id==35){
                                    $procu_master='';
                                }elseif($functab->tab_id==36){
                                    $procu_report='';
                                }elseif($functab->tab_id==41){
                                    $procu_dashboard='';
                                }

                                
                            }

                            echo '<li style="'.$procu_master.'"><a class="icon-tools overNow" title="Resource Library" href="'.Yii::$app->urlManager->createUrl("procurement/masterindex").'"></a></li>
                                <li style="'.$procu_dashboard.'"><a class="icon-dashboard proc-dashboard" title="Dashboard"  href="'.Yii::$app->urlManager->createUrl("procurement/dashboard").'"></a></li>
                                <li style="'.$procu_report.'"><a class="icon-flickr" title="Report" href="'.Yii::$app->urlManager->createUrl("procurement/reports").'"></a></li>';
                        
                        }else{


                    ?>

                  <li><a class="icon-tools overNow" title="Resource Library" href="<?php echo Yii::$app->urlManager->createUrl('procurement/masterindex')?>"> 
                        </a>
                    </li>
                    <li><a class="icon-dashboard proc-dashboard" title="Dashboard" style="display:none;" href="<?php echo Yii::$app->urlManager->createUrl('procurement/dashboard')?>"> 
                        </a>
                    </li>
                    <li><a class="icon-flickr" title="Report" href="<?php echo Yii::$app->urlManager->createUrl('procurement/reports')?>"> 
                        </a>
                    </li>

                <?php } }
                elseif(Yii::$app->controller->id == 'procurement' && Yii::$app->controller->action->id == 'reports') { ?>
                    <li><a class="icon-tools overNow" title="Procurement Masters" href="<?php echo Yii::$app->urlManager->createUrl('procurement/masterindex')?>"> 
                        </a>
                    </li>
                    <li><a class="icon-dashboard proc-dashboard" title="Dashboard" style="display:none;" href="<?php echo Yii::$app->urlManager->createUrl('procurement/dashboard')?>"> 
                        </a>
                    </li>
                    <li><a class="icon-flickr" title="Report" href="<?php echo Yii::$app->urlManager->createUrl('procurement/reports')?>"> 
                        </a>
                    </li>
                <?php }
                elseif(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'index') {

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
                                <li style="'.$res_master.'"><a class="icon-wrench overNow" title="Resource Library" href="'.Yii::$app->urlManager->createUrl("procurement/masterindex").'"> 
                                    </a>
                                </li>
                                <li style="'.$dashboard.'"><a class="icon-dashboard prjcet-dashboard" title="Dashboard" href="'.Yii::$app->urlManager->createUrl("projectsmain/dashboard").'"> 
                                    </a>
                                </li>

                                <li style="'.$proj_report.'"><a class="icon-flickr overNow1" title="Project Report" href="'.Yii::$app->urlManager->createUrl("projectsmain/reports").'"> 
                                    </a>
                                </li>';

                    }
                    else{

                 ?>
                    <li><a class="icon-tools overNow4" title="Activity Library" href="<?php echo Yii::$app->urlManager->createUrl('projects/projectmasters')?>"> 
                        </a>
                    </li>
                    <li><a class="icon-wrench overNow" title="Resource Library" href="<?php echo Yii::$app->urlManager->createUrl('procurement/masterindex')?>"> 
                        </a>
                    </li>
                    <li><a class="icon-dashboard prjcet-dashboard" title="Dashboard" style="display:none;" href="<?php echo Yii::$app->urlManager->createUrl('projectsmain/dashboard')?>"> 
                        </a>
                    </li>

                    <li><a class="icon-flickr overNow1" title="Project Report" href="<?php echo Yii::$app->urlManager->createUrl('projectsmain/reports')?>"> 
                        </a>
                    </li>
                <?php } }
                elseif(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'reports') { ?>
                    <li><a class="icon-tools overNow4" title="Activity Library" href="<?php echo Yii::$app->urlManager->createUrl('projects/projectmasters')?>">
                        </a>
                    </li>
                    <li><a class="icon-wrench overNow" title="Resource Library" href="<?php echo Yii::$app->urlManager->createUrl('procurement/masterindex')?>"> 
                        </a>
                    </li>
                    <li><a class="icon-dashboard prjcet-dashboard" title="Dashboard" style="display:none;" href="<?php echo Yii::$app->urlManager->createUrl('projectsmain/dashboard')?>"> 
                        </a>
                    </li>
                    <li><a class="icon-flickr overNow1555" title="Project Report" href="<?php echo Yii::$app->urlManager->createUrl('projectsmain/reports')?>"> 
                        </a>
                    </li>
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
                    <li><a class="icon-wrench overNow" title="Resource Library" href="<?php echo Yii::$app->urlManager->createUrl('procurement/masterindex')?>"> 
                        </a>
                    </li>
                    <li><a class="icon-dashboard prjcet-dashboard" title="Dashboard" style="display:none;" href="<?php echo Yii::$app->urlManager->createUrl('projectsmain/dashboard')?>"> 
                        </a>
                    </li>
                    <li><a class="icon-flickr overNow1" title="Project Report" href="<?php echo Yii::$app->urlManager->createUrl('projectsmain/reports')?>"> 
                        </a>
                    </li>
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
                    <li><a class="icon-dashboard prjcet-dashboard" title="Dashboard" style="display:none;" href="<?php echo Yii::$app->urlManager->createUrl('projectsmain/dashboard')?>"> 
                        </a>
                    </li>
                    <li><a class="icon-flickr overNow5" title="Operation Report" href="<?php echo Yii::$app->urlManager->createUrl('projects/operation_report')?>"> 
                        </a>
                    </li>
                <?php } } ?>
                    <li><a href="#" title="My Account" class="MyAccountNAV icon-user3 dropdown-toggle" data-toggle="dropdown">
                            
                        </a>
                        <ul class="dropdown-menu" style="height: auto;margin-left: -55px;">
                            <li><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?php echo Yii::$app->user->displayName ;?></a></li>
                            <!--<li class="dropdownli"><a href="<?php //echo Yii::$app->urlManager->createUrl('ProjectPricing/index')?>">Project Pricing</a></li>
                            <li class="dropdownli"><a href="<?php //echo Yii::$app->urlManager->createUrl('Legal/index')?>">Legal</a></li>
                            <li class="dropdownli"><a href="<?php //echo Yii::$app->urlManager->createUrl('doccumentManager/index')?>">Document Manager</a></li>
                            <li class="dropdownli"><a href="<?php //echo Yii::$app->urlManager->createUrl('projects/masters')?>">Masters</a></li>
                             <li class="dropdownli"><a href="<?php //echo Yii::$app->urlManager->createUrl('projects/masters')?>">Project Masters</a></li>
                            <li class="dropdownli"><a href="<?php //echo Yii::$app->urlManager->createUrl('user/user/admin')?>">Human Resources</a></li>-->
                            <li class="dropdownli"><a data-method='POST' href="<?php echo Yii::$app->urlManager->createUrl('site/logout')?>">Logout</a></li>
                        </ul>
                    </li>
                    <li><a href="#" class="icon-menu1 menuNAV"></a></li>
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
                                elseif($funcname->dep_id == 2){
                                     echo '<li style="'.$modules.'"><a href="'.Yii::$app->urlManager->createUrl('procurement/index').'">'.$funcname->name.'</li>';
                                }                               
                                elseif($funcname->dep_id == 4){
                                    echo '<li style="'.$modules.'"><a href="'.Yii::$app->urlManager->createUrl('projects/report').'">'.$funcname->name.'</li>';
                                }
                                elseif($funcname->dep_id == 3){
                                    echo '<li style="'.$modules.'"><a href="'.Yii::$app->urlManager->createUrl('financerequests/index').'">'.$funcname->name.'</li>';
                                }
                                elseif($funcname->dep_id == 5){
                                    echo '<li style="'.$modules.'"><a href="'.Yii::$app->urlManager->createUrl('projects/userrole').'">'.$funcname->name.'</li>';
                                }



                            }

                         
                    }else{

                    ?>

                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('projectsmain/index')?>">Projects</a></li>
                        
                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('procurement/index')?>">Procurement</a></li>

                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('projects/report')?>">Project Managers Desk</a></li>
                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('financerequests/index')?>">Finance</a></li>
                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('projects/userrole')?>">User Management</a></li>
                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('projects/reporting')?>">Reporting</a></li>
                <?php } }?>
            </ul>
              
            </div>

    </aside>
    


<?php } ?>

<!--<php $this->endBody() ?>-->

    <?php if(Yii::$app->controller->id == 'procurement' && Yii::$app->controller->action->id == 'index') { ?>

        <div class="placeOrderPop-cntnt">
            <div class="row">
                <div class="col-md-12 approveHdr">
                    <h3 id="orderpoptitle">Purchase Order</h3>
                    <span class="icon-close"></span>
                </div>
                <iframe id="placeOrderiframe" src="" style="width:100%; height:540px; border:0px; " ></iframe>         
            </div>
            
        </div>

        <div class="approveOrder-cntnt">
            <div class="row">
                <div class="col-md-12 approveHdr">
                    <h3 id="apprpoptitle">Approve Purchase Order</h3>
                    <span class="icon-close"></span>
                </div>
                <iframe id="approveOrder" src="" style="width:100%; height:520px; border:0px; " ></iframe>
                
            </div>
        
        </div>


    <?php } ?>
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

<!-- procurement order modals -->

 <div class="termsbody modal fade" id="myModallease" role="dialog">
 </div>
 <div class="termsbodys modal fade" id="myModalleaseconfirm" role="dialog">
 </div>
 <div class="termsbodyp modal fade" id="myModalpurchase" role="dialog">
 </div>
 

 <!-- procurement order modals End-->

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
        
        //Project Overrelay start
        if(Yii::$app->controller->id == 'procurement' && Yii::$app->controller->action->id == 'reports' || Yii::$app->controller->id == 'procurement' && Yii::$app->controller->action->id == 'index' || Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'index' || Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'reports' || Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'projectmasters') { 
            
            echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/procurement/masterindex.php');
            echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/projectsmain/reports.php');
            
        }
        //Project Overrelay end

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

        //Procurement dashboard start

        if(Yii::$app->controller->id == 'procurement' && Yii::$app->controller->action->id == 'index')
        { 
            
            echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/procurement/dashboard.php');
            
        }

        //Procurement dashboard end
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

</body>
</html>

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

<?php $this->endPage() ?>
