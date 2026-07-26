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
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer2.css?v=20260724a" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer3.css" rel="stylesheet">

    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer4.css" rel="stylesheet">

    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/dashboard.css" rel="stylesheet">
     <!--<link href="<?php //echo Yii::$app->request->baseUrl; ?>/cssnew/style.css?v=2.11" rel="stylesheet"> 
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
     -->


    <link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jsgantt.css?v=20260720c" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jsgantt-extra.css?v=20260726c" />
    <script language="javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/jsgantt.js"></script>

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
        .round-icons .icon-wrench.reslib-btn,
        .round-icons .icon-cart.procurementlib-btn,
        .round-icons .icon-office.storeofficelib-btn,
        .round-icons .rel-nav-btn,
        .round-icons .pdoc-btn,

        .round-icons .perf-dashboard-btn,
        .round-icons .cost-dashboard-btn,
        .round-icons .qe-btn { color: #fff !important; }

        .round-icons .icon-chart3            { background-color: #003580 !important; }
        .round-icons .rel-nav-btn            { background-color: #5c3d8f !important; }
        .round-icons .icon-calendar          { background-color: #B8860B !important; }
        .round-icons .icon-copy.duplicateProject { background-color: #E65C00 !important; }
        .round-icons .icon-tools.overNow4    { background-color: #CC0000 !important; }
        .round-icons .icon-wrench.reslib-btn { background-color: #555555 !important; }
        .round-icons .icon-cart.procurementlib-btn { background-color: #00695C !important; }
        .round-icons .icon-office.storeofficelib-btn { background-color: #2c4a4a !important; }
        .round-icons .perf-dashboard-btn     { background-color: #2e7d32 !important; height: 26px !important; width: 26px !important; font-size: 12px !important; }
        .round-icons .cost-dashboard-btn     { background-color: #7b1fa2 !important; height: 26px !important; width: 26px !important; font-size: 9px !important; padding: 0 !important; box-sizing: content-box !important; }
        .round-icons .qe-btn                 { background-color: #00838f !important; height: 26px !important; width: 26px !important; font-size: 12px !important; }
        /* Keep coloured backgrounds on hover/focus */
        .round-icons .icon-chart3:hover,            .round-icons .icon-chart3:focus            { background: #002060 !important; }
        .round-icons .rel-nav-btn:hover,            .round-icons .rel-nav-btn:focus            { background: #4a2f75 !important; }
        .round-icons .icon-calendar:hover,          .round-icons .icon-calendar:focus          { background: #8a6200 !important; }
        .round-icons .icon-copy.duplicateProject:hover, .round-icons .icon-copy.duplicateProject:focus { background: #b84400 !important; }
        .round-icons .icon-tools.overNow4:hover,    .round-icons .icon-tools.overNow4:focus    { background: #990000 !important; }
        .round-icons .icon-wrench.reslib-btn:hover, .round-icons .icon-wrench.reslib-btn:focus { background: #333333 !important; }
        .round-icons .icon-cart.procurementlib-btn:hover, .round-icons .icon-cart.procurementlib-btn:focus { background: #004D40 !important; }
        .round-icons .icon-office.storeofficelib-btn:hover, .round-icons .icon-office.storeofficelib-btn:focus { background: #1c3232 !important; }
        .round-icons .perf-dashboard-btn:hover,     .round-icons .perf-dashboard-btn:focus     { background: #1b5e20 !important; }
        .round-icons .cost-dashboard-btn:hover,     .round-icons .cost-dashboard-btn:focus     { background: #4a148c !important; }
        .round-icons .qe-btn:hover,                 .round-icons .qe-btn:focus                 { background: #005f6b !important; }
        .round-icons .pdoc-btn                      { background-color: #1565C0 !important; }
        .round-icons .pdoc-btn:hover,               .round-icons .pdoc-btn:focus               { background: #0d47a1 !important; }

        /* ── Project Documents Floating Popup ── */
        #pdoc-popup {
            display:none; flex-direction:column;
            position:fixed;
            width:1100px; height:600px; min-width:500px; min-height:280px;
            z-index:100000;
            background:#fff; border-radius:10px; overflow:hidden;
            box-shadow:0 8px 40px rgba(0,0,0,0.32), 0 2px 8px rgba(0,0,0,0.12);
        }
        #pdoc-popup.pdoc-open { display:flex; }
        #pdoc-header {
            background:linear-gradient(135deg,#1565C0,#0d47a1);
            color:#fff; padding:10px 16px; display:flex; align-items:center; gap:8px;
            cursor:move; user-select:none; flex-shrink:0;
        }
        #pdoc-header h4 { margin:0; font-size:14px; font-weight:700; flex:1; }
        #pdoc-close-x {
            background:rgba(255,255,255,0.2); border:none; color:#fff; border-radius:50%;
            width:24px; height:24px; font-size:15px; cursor:pointer; line-height:24px; text-align:center; flex-shrink:0;
        }
        #pdoc-close-x:hover { background:rgba(255,255,255,0.35); }
        #pdoc-search-bar {
            background:#f5f7fa; border-bottom:1px solid #e0e0e0;
            padding:10px 14px; display:flex; gap:8px; flex-wrap:wrap; flex-shrink:0; align-items:flex-end;
        }
        #pdoc-search-bar .pdoc-sf { display:flex; flex-direction:column; gap:2px; }
        #pdoc-search-bar .pdoc-sf label { font-size:10px; font-weight:600; color:#555; margin:0; }
        #pdoc-search-bar select, #pdoc-search-bar input[type=text], #pdoc-search-bar input[type=date] {
            border:1px solid #ccc; border-radius:5px; padding:4px 8px; font-size:12px;
            height:28px; background:#fff; outline:none;
        }
        #pdoc-search-bar select:focus, #pdoc-search-bar input:focus { border-color:#1565C0; }
        #pdoc-search-btn {
            background:#1565C0; color:#fff; border:none; border-radius:5px;
            padding:0 14px; height:28px; font-size:12px; font-weight:600; cursor:pointer; align-self:flex-end;
        }
        #pdoc-search-btn:hover { background:#0d47a1; }
        #pdoc-clear-btn {
            background:#eee; color:#333; border:none; border-radius:5px;
            padding:0 10px; height:28px; font-size:12px; cursor:pointer; align-self:flex-end;
        }
        #pdoc-clear-btn:hover { background:#ddd; }
        #pdoc-table-wrap { flex:1; overflow-y:auto; padding:10px 14px; min-height:0; height:0; }
        #pdoc-table-wrap table { width:100%; border-collapse:collapse; font-size:12px; }
        #pdoc-table-wrap thead th {
            background:#1565C0; color:#fff; padding:8px 10px; text-align:left;
            font-weight:600; font-size:11px; position:sticky; top:0; z-index:1;
        }
        #pdoc-table-wrap tbody tr { border-bottom:1px solid #f0f0f0; transition:background .15s; }
        #pdoc-table-wrap tbody tr:hover { background:#f0f6ff; }
        #pdoc-table-wrap tbody td { padding:7px 10px; color:#1a1a1a; vertical-align:middle; }
        .pdoc-type-badge {
            display:inline-block; padding:2px 7px; border-radius:20px; font-size:10px; font-weight:600;
        }
        .pdoc-badge-doc  { background:#e3f2fd; color:#1565C0; }
        .pdoc-badge-corr { background:#f3e5f5; color:#7b1fa2; }
        .pdoc-view-btn {
            background:#1565C0; color:#fff; border:none; border-radius:4px;
            padding:3px 10px; font-size:11px; font-weight:600; cursor:pointer; white-space:nowrap;
        }
        .pdoc-view-btn:hover { background:#0d47a1; }
        .pdoc-remove-btn {
            background:#fdecea; color:#c0392b; border:1px solid #f5c6c2; border-radius:4px;
            padding:3px 10px; font-size:11px; font-weight:600; cursor:pointer; white-space:nowrap;
        }
        .pdoc-remove-btn:hover { background:#f9b3ae; }
        #pdoc-empty { display:none; text-align:center; color:#888; padding:30px; font-size:13px; }
        #pdoc-loading { display:none; text-align:center; color:#888; padding:30px; font-size:13px; }
        /* resize handle hint */
        #pdoc-popup .ui-resizable-se { background:#1565C0; opacity:.4; border-radius:0 0 8px 0; }
        #pdoc-popup .ui-resizable-se:hover { opacity:.7; }

        /* ── File Viewer Floating Popup ── */
        #pdoc-viewer-popup {
            display:none; flex-direction:column;
            position:fixed;
            width:90vw; height:90vh; min-width:400px; min-height:320px;
            z-index:100100;
            background:#fff; border-radius:10px; overflow:hidden;
            box-shadow:0 12px 48px rgba(0,0,0,0.4);
        }
        #pdoc-viewer-popup.pdoc-open { display:flex; }
        #pdoc-viewer-header {
            background:#1565C0; color:#fff; padding:9px 14px;
            display:flex; align-items:center; gap:8px; cursor:move; user-select:none; flex-shrink:0;
        }
        #pdoc-viewer-title { flex:1; font-size:12px; font-weight:600; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        #pdoc-viewer-close {
            background:rgba(255,255,255,0.2); border:none; color:#fff; border-radius:50%;
            width:22px; height:22px; font-size:14px; cursor:pointer; line-height:22px; text-align:center; flex-shrink:0;
        }
        #pdoc-viewer-close:hover { background:rgba(255,255,255,0.35); }
        #pdoc-viewer-body { flex:1; overflow:hidden; min-height:200px; height:0; }
        #pdoc-viewer-body iframe { width:100%; height:100%; border:none; display:block; }
        #pdoc-viewer-body img { max-width:100%; max-height:100%; object-fit:contain; display:block; margin:auto; }
        #pdoc-viewer-body .pdoc-no-preview {
            display:flex; align-items:center; justify-content:center; height:100%;
            flex-direction:column; gap:12px; color:#555; padding:30px;
        }
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

                ?>
                <li>
                    <a class="icon-add" id="add-project-nav-btn" title="Projects" href="#" style="font-size:18px;"></a>
                </li>
                <li>
                    <a class="icon-document pdoc-btn" id="pdoc-nav-btn" title="Project Documents" href="#"></a>
                </li>
                <li>
                    <a class="icon-chart3" id="gantt-win-open" title="Gantt Chart" href="#" data-projectid="<?php echo $ProjectId?>"></a>
                </li>
                <li>
                    <a class="icon-sitemap rel-nav-btn" id="btn-gantt-relations" title="Activity Relationships" href="#" data-projectid="<?php echo $ProjectId?>"></a>
                </li>
                <?php if($ProjectId && Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'newganttchart') { ?>
                <li><a class="icon-tools overNow4" title="Activity Library" href="<?php echo Yii::$app->urlManager->createUrl('projects/projectmasters')?>"></a></li>
                <?php } ?>
                

                <?php if(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'index') {  ?>
                <li style="display:none;">
                    <a href="#holidayPopup" class="dropdown-toggle icon-calendar" data-toggle="modal" data-projectid="<?php echo $ProjectId; ?>" id="holidayPopupLink" data-target="#holidayPopup" title="Holidays"></a>
                </li>

                <li style="display:none;">
                    <a href="javascript:;" class=" icon-copy duplicateProject"  data-projectid="<?php echo $ProjectId; ?>" id="duplicateProject_<?php echo $ProjectId; ?>"  title="Duplicate Project"></a>
                </li>

                <?php } ?>

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
                                <li><a class="icon-wrench reslib-btn" title="Resource Library" href="#"> </a></li>
                                <li><a class="icon-cart procurementlib-btn" title="Procurement" href="#"> </a></li>
                                <li><a class="icon-office storeofficelib-btn" title="Site Office" href="#"> </a></li>
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
                    <li><a class="icon-wrench reslib-btn" title="Resource Library" href="#"> </a></li>
                                <li><a class="icon-cart procurementlib-btn" title="Procurement" href="#"> </a></li>
                                <li><a class="icon-office storeofficelib-btn" title="Site Office" href="#"> </a></li>
                    <li><a class="icon-stats perf-dashboard-btn" title="KPI" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-stats cost-dashboard-btn" title="Cost Dashboard" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-pencil qe-btn" title="WBS" href="#" style="cursor:pointer;"> </a></li>
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
                    <li><a class="icon-wrench reslib-btn" title="Resource Library" href="#"> </a></li>
                                <li><a class="icon-cart procurementlib-btn" title="Procurement" href="#"> </a></li>
                                <li><a class="icon-office storeofficelib-btn" title="Site Office" href="#"> </a></li>
                    <li><a class="icon-stats perf-dashboard-btn" title="KPI" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-stats cost-dashboard-btn" title="Cost Dashboard" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-pencil qe-btn" title="WBS" href="#" style="cursor:pointer;"> </a></li>
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
                    <li><a class="icon-wrench reslib-btn" title="Resource Library" href="#"> </a></li>
                                <li><a class="icon-cart procurementlib-btn" title="Procurement" href="#"> </a></li>
                                <li><a class="icon-office storeofficelib-btn" title="Site Office" href="#"> </a></li>
                    <li><a class="icon-stats perf-dashboard-btn" title="KPI" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-stats cost-dashboard-btn" title="Cost Dashboard" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-pencil qe-btn" title="WBS" href="#" style="cursor:pointer;"> </a></li>
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
                    <li><a class="icon-pencil qe-btn" title="WBS" href="#" style="cursor:pointer;"> </a></li>
                <?php  } } } ?>


                <?php
                /* Asset Register / Asset Library icons removed — their target
                   views (_assetregister_new.php, _assetlibrary_new.php) don't
                   exist on disk, so clicking either one threw a fatal error.
                   actionAssetregister()/actionAssetlibrary() are left in place
                   in ProjectsController.php in case these screens get rebuilt. */
                ?>





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
        
        //Project Master Overrelay — included on all pages except projectmasters itself (which renders it as page content)
        if(!(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'projectmasters')) {
            echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/projects/projectmasters.php');
        }

        //Resource Library Overrelay start
        if(Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'index' || Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'reports' || Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'projectmasters') {
            echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/projectsmain/overrelay-resourcemaster.php');
        }
        //Resource Library Overrelay end

        //Activity Relations Overrelay — always present when gantt-win is available
        echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/projectsmain/overrelay-relations.php');

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
        <div class="modal fade ganttchartPopup" id="ganttchartPopup" style="display:none !important;">
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
#cb-btn{position:fixed;bottom:24px;right:24px;z-index:100000;width:44px;height:44px;border-radius:50%;background:#1a2540;color:#fff;border:none;font-size:20px;cursor:pointer;box-shadow:0 4px 16px rgba(0,0,0,.35);display:flex;align-items:center;justify-content:center;transition:background .2s}
#cb-btn:hover{background:#2d3f6e}

/* Desktop floating panel */
#cb-win{position:fixed;bottom:80px;right:24px;z-index:100000;width:330px;height:440px;background:#fff;border-radius:14px;box-shadow:0 8px 40px rgba(0,0,0,.28);display:none;flex-direction:column;font-family:'Times New Roman',Times,serif;overflow:hidden}

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
        document.dispatchEvent(new Event('chat:open'));
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

<!-- ── Projects Manager Overlay ───────────────────────────────────────── -->
<style>
#gpm-overlay { display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.55);z-index:100000; }
#gpm-dialog  { position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border-radius:8px;width:900px;max-width:96vw;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 8px 40px rgba(0,0,0,0.4); }
#gpm-header  { padding:14px 20px;border-bottom:1px solid #e5e5e5;display:flex;align-items:center;justify-content:space-between;flex-shrink:0; }
#gpm-header h4 { margin:0;font-size:17px;font-weight:700;color:#333; }
#gpm-close-x { background:none;border:none;font-size:28px;line-height:1;cursor:pointer;color:#999;padding:0; }
#gpm-body    { flex:1;overflow-y:auto;padding:0; }

/* ── Panel: Project List ── */
#gpm-list-panel { padding:16px 20px; }
#gpm-list-panel .gpm-toolbar { display:flex;align-items:center;justify-content:space-between;margin-bottom:14px; }
#gpm-list-panel .gpm-toolbar h5 { margin:0;font-size:14px;color:#555; }
#gpm-cards { display:flex;flex-wrap:wrap;gap:14px; }
.gpm-card {
    border:1px solid #e0e0e0;border-radius:10px;padding:0;width:calc(33.33% - 10px);
    box-sizing:border-box;position:relative;background:#fff;overflow:hidden;
    box-shadow:0 2px 6px rgba(0,0,0,0.08), 0 6px 16px rgba(0,0,0,0.06);
    transition:box-shadow .2s, transform .2s, background .2s, border-color .2s;
    cursor:pointer;
}
.gpm-card:hover { box-shadow:0 8px 24px rgba(0,0,0,0.15), 0 2px 6px rgba(0,0,0,0.08); transform:translateY(-2px); }
.gpm-card.gpm-card-selected {
    background:#e8ecf0;border-color:#a0adb8;
    box-shadow:0 0 0 2px #7090a8, 0 4px 14px rgba(70,100,130,0.18);
    transform:none;
}
.gpm-card.gpm-card-selected .gpm-card-top { background:#e8ecf0; }
.gpm-card.gpm-card-selected .gpm-card-body { background:#e8ecf0; }
.gpm-card.gpm-card-selected .gpm-card-actions { background:#dde3ea;border-top-color:#c0c8d0; }
.gpm-card.gpm-card-selected .gpm-card-name { color:#1a3a52; }
.gpm-selected-badge {
    display:inline-block;font-size:10px;font-weight:700;color:#fff;
    background:#4a7090;border-radius:10px;padding:1px 8px;margin-left:6px;
    vertical-align:middle;letter-spacing:.3px;
}
.gpm-card-top { background:#fff;padding:12px 14px 8px;border-bottom:1px solid #f0f0f0; }
.gpm-card .gpm-card-name { font-size:13px;font-weight:700;color:#1a1a1a;margin:0; }
.gpm-card-body { padding:10px 14px 4px;background:#fff; }
.gpm-card .gpm-card-row { font-size:12px;color:#555;margin-bottom:4px;display:flex;gap:4px; }
.gpm-card .gpm-card-row label { font-weight:600;color:#333;margin:0;min-width:76px;flex-shrink:0; }
.gpm-card .gpm-card-actions { padding:8px 14px 10px;display:flex;gap:6px;border-top:1px solid #f0f0f0;background:#fafafa;margin-top:6px; }
.gpm-no-projects { color:#999;padding:30px 0;text-align:center;font-size:14px;width:100%; }
.gpm-file-list { margin-top:6px; }
.gpm-file-item { display:flex;align-items:center;justify-content:space-between;padding:4px 8px;border-radius:4px;background:#f0f0f0;margin-bottom:4px;font-size:12px; }
.gpm-file-item a { color:#337ab7;text-decoration:none;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.gpm-file-item .gpm-del-file { color:#c00;cursor:pointer;margin-left:8px;font-size:14px;line-height:1;background:none;border:none;padding:0; }

/* ── Panel: Add / Edit Form ── */
#gpm-form-panel { display:none;padding:16px 20px;border-top:1px solid #eee; }
#gpm-form-panel .gpm-form-header { display:flex;align-items:center;gap:10px;margin-bottom:14px; }
#gpm-form-panel .gpm-form-header h5 { margin:0;font-size:15px;font-weight:700;color:#333; }
.gpm-back-btn { background:none;border:1px solid #ccc;border-radius:20px;padding:3px 12px;font-size:12px;cursor:pointer;color:#555; }
.gpm-back-btn:hover { background:#f0f0f0; }
</style>

<div id="gpm-overlay">
  <div id="gpm-dialog">
    <div id="gpm-header">
      <h4 id="gpm-title"><span class="icon-copy"></span> Projects</h4>
      <button id="gpm-close-x" type="button">&times;</button>
    </div>
    <div id="gpm-body">

      <!-- LIST PANEL -->
      <div id="gpm-list-panel">
        <div class="gpm-toolbar">
          <h5 id="gpm-count-label">Loading projects…</h5>
          <button id="gpm-open-add-form" class="btn btn-primary btn-sm" style="border-radius:20px;">
            <span class="icon-add"></span> Add Project
          </button>
        </div>
        <div id="gpm-cards"></div>
      </div>

      <!-- ADD / EDIT FORM PANEL -->
      <div id="gpm-form-panel">
        <div class="gpm-form-header">
          <button class="gpm-back-btn" id="gpm-back-to-list"><span class="icon-arrow-left"></span> Back to list</button>
          <h5 id="gpm-form-title">Add New Project</h5>
        </div>
        <form id="gpm-form" autocomplete="off">
          <input type="hidden" id="gpm_editing_id" value="">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Project Name <span style="color:red">*</span></label>
                <input id="gpm_name" type="text" class="form-control" placeholder="Project Name">
                <span class="text-danger gpm-err" id="gpm_name_err" style="display:none;font-size:12px;"></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Duration (days) <span style="color:red">*</span></label>
                <input id="gpm_duration" type="number" min="1" class="form-control" placeholder="e.g. 180">
                <span class="text-danger gpm-err" id="gpm_duration_err" style="display:none;font-size:12px;"></span>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Start Date <span style="color:red">*</span></label>
                <input id="gpm_startdate" type="date" class="form-control">
                <span class="text-danger gpm-err" id="gpm_startdate_err" style="display:none;font-size:12px;"></span>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>End Date</label>
                <input id="gpm_enddate" type="date" class="form-control" readonly style="background:#f5f5f5;">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Project Value <span style="color:red">*</span></label>
                <input id="gpm_value" type="text" class="form-control" placeholder="e.g. 5000000">
                <span class="text-danger gpm-err" id="gpm_value_err" style="display:none;font-size:12px;"></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Client Name <span style="color:red">*</span></label>
                <input id="gpm_client" type="text" class="form-control" placeholder="Client Name">
                <span class="text-danger gpm-err" id="gpm_client_err" style="display:none;font-size:12px;"></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Location</label>
                <input id="gpm_location" type="text" class="form-control" placeholder="Site Location">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Work Hours <span style="color:red">*</span></label>
                <select id="gpm_wrkhrs" class="form-control">
                  <option value="">-- Select --</option>
                  <option value="8">8 hrs</option>
                  <option value="10">10 hrs</option>
                  <option value="12">12 hrs</option>
                  <option value="24">24 hrs</option>
                </select>
                <span class="text-danger gpm-err" id="gpm_wrkhrs_err" style="display:none;font-size:12px;"></span>
              </div>
            </div>
          </div>
          <div style="text-align:right;margin-top:12px;">
            <button type="button" id="gpm-form-cancel" class="btn btn-default" style="border-radius:20px;margin-right:6px;"><span class="icon-close"></span> Cancel</button>
            <button type="button" id="gpm-form-save" class="btn btn-primary" style="border-radius:20px;"><span class="icon-check"></span> Save Project</button>
          </div>
        </form>
      </div>

    </div><!-- /gpm-body -->
  </div><!-- /gpm-dialog -->
</div><!-- /gpm-overlay -->

<script>
(function(){
    var URL_LIST   = '<?php echo \yii\helpers\Url::to(["/projects/projectsearch"]); ?>';
    var URL_CREATE = '<?php echo \yii\helpers\Url::to(["/projects/create"]); ?>';
    var URL_GET    = '<?php echo \yii\helpers\Url::to(["/projects/globalgetproject"]); ?>';
    var URL_UPDATE = '<?php echo \yii\helpers\Url::to(["/projects/globalupdateproject"]); ?>';
    var URL_DELETE = '<?php echo \yii\helpers\Url::to(["/projects/globaldeleteproject"]); ?>';

    /* ── open / close overlay ── */
    function gpmShow(){
        $('#gpm-overlay').show();
        gpmShowList();
    }
    function gpmHide(){
        $('#gpm-overlay').hide();
        gpmShowListPanel();
    }
    $(document).on('click','#add-project-nav-btn',function(e){ e.preventDefault(); gpmShow(); });
    $(document).on('click','#gpm-close-x',function(){ gpmHide(); });
    $(document).on('click','#gpm-overlay',function(e){ if(e.target===this) gpmHide(); });
    $(document).on('click','#gpm-dialog',function(e){ e.stopPropagation(); });

    /* ── panel switching ── */
    function gpmShowListPanel(){
        $('#gpm-list-panel').show();
        $('#gpm-form-panel').hide();
        $('#gpm-title').html('<span class="icon-copy"></span> Projects');
    }
    function gpmShowFormPanel(isEdit){
        $('#gpm-list-panel').hide();
        $('#gpm-form-panel').show();
        $('#gpm-title').html(isEdit ? '<span class="icon-edit"></span> Edit Project' : '<span class="icon-add"></span> Add Project');
        $('#gpm-form-title').text(isEdit ? 'Edit Project' : 'Add New Project');
        $('#gpm-form-save').html('<span class="icon-check"></span> ' + (isEdit ? 'Update Project' : 'Save Project'));
    }
    $(document).on('click','#gpm-back-to-list, #gpm-form-cancel',function(){ gpmShowListPanel(); });
    $(document).on('click','#gpm-open-add-form',function(){
        $('#gpm_editing_id').val('');
        $('#gpm-form')[0].reset();
        $('.gpm-err').hide();
        gpmShowFormPanel(false);
    });

    /* ── auto-calculate end date ── */
    function gpmCalcEnd(){
        var sd  = $('#gpm_startdate').val();
        var dur = parseInt($('#gpm_duration').val(),10);
        if(sd && dur>0){
            var d=new Date(sd); d.setDate(d.getDate()+dur-1);
            var y=d.getFullYear(), m=String(d.getMonth()+1).padStart(2,'0'), day=String(d.getDate()).padStart(2,'0');
            $('#gpm_enddate').val(y+'-'+m+'-'+day);
        } else { $('#gpm_enddate').val(''); }
    }
    $(document).on('change input','#gpm_startdate,#gpm_duration',gpmCalcEnd);


    /* ── load project list ── */
    function gpmShowList(){
        gpmShowListPanel();
        $('#gpm-cards').html('<div style="padding:20px;color:#999;font-size:13px;">Loading…</div>');
        $('#gpm-count-label').text('Loading projects…');
        $.ajax({
            type:'POST', url:URL_LIST, dataType:'json',
            success:function(data){
                if(data.error==='No'){
                    gpmRenderCards(data.result);
                } else {
                    $('#gpm-cards').html('<div class="gpm-no-projects">Could not load projects.</div>');
                }
            },
            error:function(){ $('#gpm-cards').html('<div class="gpm-no-projects">Server error loading projects.</div>'); }
        });
    }

    var URL_FILES     = '<?php echo \yii\helpers\Url::to(["/projects/globalprojectfiles"]); ?>';
    var URL_DELFILE   = '<?php echo \yii\helpers\Url::to(["/projects/deleteprojectfile"]); ?>';
    var BASE_UPLOAD   = '<?php echo \yii\helpers\Url::base(true); ?>/uploads/projects/';
    var URL_SELECT    = '<?php echo \yii\helpers\Url::to(["/projectsmain/userprojectmain"]); ?>';
    var CURRENT_PID   = '<?php echo $ProjectId; ?>';

    function gpmRenderCards(html){
        var $tmp = $('<div>').html(html);
        var cards = $tmp.find('.fav-project-wrpr');
        if(!cards.length){
            $('#gpm-cards').html('<div class="gpm-no-projects">No projects found. Click <strong>Add Project</strong> to create one.</div>');
            $('#gpm-count-label').text('No projects');
            return;
        }
        var out = '';
        cards.each(function(){
            var pid    = $(this).data('id');
            var name   = $(this).find('.card-body a').text().replace(/^\s*[✓✓]\s*/,'').trim();
            var client = $(this).find('.project-client-name span').text().trim();
            var dur    = $(this).find('.type:not(.project-client-name):not(.text-right) span').first().text().trim();
            var val    = $(this).find('.type.text-right span').text().trim();
            var isSel  = (String(pid) === String(CURRENT_PID));
            var selCls = isSel ? ' gpm-card-selected' : '';
            var badge  = isSel ? '<span class="gpm-selected-badge">Active</span>' : '';
            out += '<div class="gpm-card'+selCls+'" id="gpm-card-'+pid+'" data-id="'+pid+'" data-name="'+name.replace(/"/g,'&quot;')+'">'
                 + '<div class="gpm-card-top"><div class="gpm-card-name">'+name+badge+'</div></div>'
                 + '<div class="gpm-card-body">'
                 + '<div class="gpm-card-row"><label>Client</label><span>'+client+'</span></div>'
                 + '<div class="gpm-card-row"><label>Duration</label><span>'+dur+'</span></div>'
                 + '<div class="gpm-card-row"><label>Value</label><span>'+val+'</span></div>'
                 + '</div>'
                 + '<div class="gpm-card-actions">'
                 + '<button class="btn btn-xs btn-info gpm-edit-btn" data-id="'+pid+'"><span class="icon-edit"></span> Edit</button>'
                 + '<button class="btn btn-xs btn-danger gpm-delete-btn" data-id="'+pid+'" data-name="'+name.replace(/"/g,'&quot;')+'"><span class="icon-trash"></span> Delete</button>'
                 + '</div>'
                 + '</div>';
        });
        $('#gpm-cards').html(out);
        $('#gpm-count-label').text(cards.length + ' project' + (cards.length===1?'':'s'));
    }

    /* ── load existing files for a project ── */
    function gpmLoadFiles(pid){
        $('#gpm-existing-files').hide();
        $('#gpm-file-list-inner').html('<span style="color:#999;font-size:12px;">Loading…</span>');
        $.ajax({
            type:'POST', url:URL_FILES, dataType:'json', data:{project_id:pid},
            success:function(d){
                if(!d.files || !d.files.length){ $('#gpm-existing-files').hide(); return; }
                var html='';
                $.each(d.files,function(i,f){
                    var icon = /\.(jpg|jpeg|png)$/i.test(f.original_name) ? 'icon-image' : 'icon-file-pdf';
                    html += '<div class="gpm-file-item" id="gpm-file-'+f.id+'">'
                          + '<span class="'+icon+'" style="margin-right:6px;color:#666;"></span>'
                          + '<a href="'+BASE_UPLOAD+f.filename+'" target="_blank" title="'+f.original_name+'">'+f.original_name+'</a>'
                          + '<span style="color:#999;font-size:11px;margin:0 8px;white-space:nowrap;">'+f.uploaded_at+'</span>'
                          + '<button class="gpm-del-file" data-fileid="'+f.id+'" title="Remove">&times;</button>'
                          + '</div>';
                });
                $('#gpm-file-list-inner').html(html);
                $('#gpm-existing-files').show();
            }
        });
    }

    /* ── delete a file ── */
    $(document).on('click','.gpm-del-file',function(){
        var fid=$(this).data('fileid');
        if(!confirm('Remove this file?')) return;
        var $row=$('#gpm-file-'+fid);
        $.ajax({
            type:'POST', url:URL_DELFILE, dataType:'json', data:{fileid:fid},
            success:function(d){
                $row.remove();
                if(!$('.gpm-file-item').length) $('#gpm-existing-files').hide();
            },
            error:function(xhr){ alert('Could not delete file ('+xhr.status+').'); }
        });
    });

    /* ── select project by clicking the card body (not the action buttons) ── */
    $(document).on('click','.gpm-card',function(e){
        if($(e.target).closest('.gpm-card-actions').length) return; /* ignore button clicks */
        var pid  = $(this).data('id');
        var name = $(this).data('name');
        if(String(pid) === String(CURRENT_PID)) return; /* already selected */

        var $card = $(this);
        $card.css('opacity','0.6');
        $.ajax({
            type:'POST', url:URL_SELECT, dataType:'json', data:{prjctid:pid},
            success:function(d){
                $card.css('opacity','');
                if(d.error !== 'No'){ alert('Could not select project.'); return; }
                /* update selection state */
                CURRENT_PID = String(pid);
                $('.gpm-card').removeClass('gpm-card-selected');
                $('.gpm-card .gpm-card-name .gpm-selected-badge').remove();
                $card.addClass('gpm-card-selected');
                $card.find('.gpm-card-name').append('<span class="gpm-selected-badge">Active</span>');
                /* update nav project name */
                $('#selectedProject').val(name);
                /* reload page so Gantt/WBS/procurement all pick up the new project */
                window.location.reload();
            },
            error:function(){ $card.css('opacity',''); alert('Server error selecting project.'); }
        });
    });

    /* ── edit project ── */
    $(document).on('click','.gpm-edit-btn',function(){
        var pid=$(this).data('id');
        $.ajax({
            type:'POST', url:URL_GET, dataType:'json', data:{project_id:pid},
            success:function(d){
                if(d.error!=='No'){ alert(d.errortext||'Could not load project.'); return; }
                $('#gpm_editing_id').val(d.Project_Id);
                $('#gpm_name').val(d.Name);
                $('#gpm_duration').val(d.duration);
                $('#gpm_startdate').val(d.start_date);
                $('#gpm_enddate').val(d.end_date);
                $('#gpm_value').val(d.project_value);
                $('#gpm_client').val(d.client_name);
                $('#gpm_location').val(d.location);
                $('#gpm_wrkhrs').val(d.wrkhours);
                $('.gpm-err').hide();
                gpmShowFormPanel(true);
                gpmLoadFiles(d.Project_Id);
            },
            error:function(xhr){ alert('Server error ('+xhr.status+').'); }
        });
    });

    /* ── delete project ── */
    $(document).on('click','.gpm-delete-btn',function(){
        var pid=$(this).data('id'), name=$(this).data('name');
        if(!confirm('Delete project "'+name+'"? This cannot be undone.')) return;
        $.ajax({
            type:'POST', url:URL_DELETE, dataType:'json', data:{project_id:pid},
            success:function(d){
                if(d.error==='No'){
                    $('#gpm-card-'+pid).remove();
                    var remaining=$('.gpm-card').length;
                    $('#gpm-count-label').text(remaining+' project'+(remaining===1?'':'s'));
                    if(!remaining) $('#gpm-cards').html('<div class="gpm-no-projects">No projects found. Click <strong>Add Project</strong> to create one.</div>');
                } else { alert(d.errortext||'Could not delete.'); }
            },
            error:function(xhr){ alert('Server error ('+xhr.status+').'); }
        });
    });

    /* ── save (add or update) using FormData for file upload ── */
    $(document).on('click','#gpm-form-save',function(){
        var err=0; $('.gpm-err').hide();
        var name   = $.trim($('#gpm_name').val());
        var dur    = $.trim($('#gpm_duration').val());
        var sd     = $('#gpm_startdate').val();
        var ed     = $('#gpm_enddate').val();
        var val    = $.trim($('#gpm_value').val()).replace(/,/g,'');
        var client = $.trim($('#gpm_client').val());
        var hrs    = $('#gpm_wrkhrs').val();
        var pid    = $('#gpm_editing_id').val();

        if(!name)   { $('#gpm_name_err').text('Enter project name').show(); err=1; }
        if(!dur)    { $('#gpm_duration_err').text('Enter duration').show(); err=1; }
        if(!sd)     { $('#gpm_startdate_err').text('Select start date').show(); err=1; }
        if(!val)    { $('#gpm_value_err').text('Enter project value').show(); err=1; }
        if(!client) { $('#gpm_client_err').text('Enter client name').show(); err=1; }
        if(!hrs)    { $('#gpm_wrkhrs_err').text('Select work hours').show(); err=1; }
        if(err) return;

        var isEdit = pid ? true : false;
        var fd = new FormData();
        fd.append('projectname', name);
        fd.append('duration', dur);
        fd.append('startdate', sd);
        fd.append('enddate', ed);
        fd.append('projectvalue', val);
        fd.append('clientname', client);
        fd.append('location', $('#gpm_location').val());
        fd.append('wrkhrss', hrs);
        if(isEdit) fd.append('project_id', pid);

        var btn=$('#gpm-form-save').prop('disabled',true).text('Saving…');
        $.ajax({
            type:'POST',
            url: isEdit ? URL_UPDATE : URL_CREATE,
            data: fd,
            dataType:'json',
            contentType:false,
            processData:false,
            success:function(data){
                btn.prop('disabled',false).html('<span class="icon-check"></span> '+(isEdit?'Update Project':'Save Project'));
                if(data.error==='No'){
                    gpmShowList();
                } else {
                    alert(data.errortext||'Could not save project.');
                }
            },
            error:function(xhr){
                btn.prop('disabled',false).html('<span class="icon-check"></span> '+(isEdit?'Update Project':'Save Project'));
                alert('Server error ('+xhr.status+'). Please try again.');
            }
        });
    });
})();
</script>
<!-- ── End Projects Manager Overlay ───────────────────────────────────── -->

<!-- ── Project Documents Floating Popup ──────────────────────────────── -->
<div id="pdoc-popup">
  <div id="pdoc-header">
    <span style="font-size:15px;opacity:.85;">&#128196;</span>
    <h4>Project Documents</h4>
    <button id="pdoc-upload-toggle" title="Upload Document" style="margin-left:auto;margin-right:8px;background:#27ae60;border:1px solid #1e8449;color:#fff;border-radius:20px;padding:3px 14px;font-size:12px;cursor:pointer;font-weight:600;">&#8679; Upload</button>
    <button id="pdoc-close-x">&times;</button>
  </div>
  <!-- Upload Panel -->
  <div id="pdoc-upload-panel" style="display:none;background:#f0f4fb;border-bottom:2px solid #dde3ef;padding:14px 16px;">
    <!-- Active project name display -->
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
      <label style="font-size:11px;color:#555;font-weight:700;white-space:nowrap;">Project:</label>
      <span id="pdoc-up-project-name" style="font-size:13px;font-weight:700;color:#1565C0;">
        <?php echo $ProjectName ? Html::encode($ProjectName) : '<span style="color:#999;font-weight:400;font-style:italic;">No project selected</span>'; ?>
      </span>
    </div>
    <!-- Two upload columns -->
    <div style="display:flex;gap:16px;align-items:flex-start;">

      <!-- LEFT: Documents -->
      <div style="flex:1;background:#fff;border:1px solid #dde3ef;border-radius:8px;padding:14px 16px;">
        <div style="font-size:12px;font-weight:700;color:#1565C0;margin-bottom:10px;border-bottom:1px solid #e8eaf0;padding-bottom:6px;">&#128196; Upload Document</div>
        <div style="margin-bottom:8px;">
          <label style="font-size:11px;color:#666;font-weight:600;display:block;margin-bottom:3px;">File <span style="color:red">*</span></label>
          <input id="pdoc-up-doc-file" type="file" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" style="font-size:12px;width:100%;">
        </div>
        <div style="display:flex;align-items:center;gap:10px;margin-top:10px;">
          <button id="pdoc-up-doc-submit" style="padding:5px 20px;background:#1565C0;color:#fff;border:none;border-radius:20px;font-size:12px;font-weight:700;cursor:pointer;">&#8679; Upload</button>
          <button id="pdoc-up-doc-cancel" type="button" style="padding:5px 16px;background:#fdecea;color:#c0392b;border:1px solid #f5c6c2;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;">Cancel</button>
          <span id="pdoc-up-doc-msg" style="font-size:12px;font-weight:600;"></span>
        </div>
      </div>

      <!-- RIGHT: Correspondence -->
      <div style="flex:1;background:#fff;border:1px solid #dde3ef;border-radius:8px;padding:14px 16px;">
        <div style="font-size:12px;font-weight:700;color:#6a1b9a;margin-bottom:10px;border-bottom:1px solid #e8eaf0;padding-bottom:6px;">&#9993; Upload Correspondence</div>
        <div style="margin-bottom:8px;">
          <label style="font-size:11px;color:#666;font-weight:600;display:block;margin-bottom:3px;">File <span style="color:red">*</span></label>
          <input id="pdoc-up-corr-file" type="file" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" style="font-size:12px;width:100%;">
        </div>
        <div id="pdoc-up-corr-meta" style="display:none;">
          <div style="margin-bottom:6px;">
            <label style="font-size:11px;color:#666;font-weight:600;display:block;margin-bottom:3px;">Addressee</label>
            <input id="pdoc-up-addressee" type="text" placeholder="Name of addressee" style="height:30px;font-size:12px;border:1px solid #ccc;border-radius:4px;padding:0 8px;width:100%;box-sizing:border-box;">
          </div>
          <div style="margin-bottom:6px;">
            <label style="font-size:11px;color:#666;font-weight:600;display:block;margin-bottom:3px;">Subject</label>
            <input id="pdoc-up-subject" type="text" placeholder="Subject of correspondence" style="height:30px;font-size:12px;border:1px solid #ccc;border-radius:4px;padding:0 8px;width:100%;box-sizing:border-box;">
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;margin-top:10px;">
          <button id="pdoc-up-corr-submit" style="padding:5px 20px;background:#6a1b9a;color:#fff;border:none;border-radius:20px;font-size:12px;font-weight:700;cursor:pointer;">&#8679; Upload</button>
          <button id="pdoc-up-corr-cancel" type="button" style="padding:5px 16px;background:#fdecea;color:#c0392b;border:1px solid #f5c6c2;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;">Cancel</button>
          <span id="pdoc-up-corr-msg" style="font-size:12px;font-weight:600;"></span>
        </div>
      </div>

    </div>
  </div>
  <div id="pdoc-search-bar">
    <div class="pdoc-sf">
      <label>Project</label>
      <select id="pdoc-filter-project" style="min-width:140px;">
        <option value="">All Projects</option>
      </select>
    </div>
    <div class="pdoc-sf">
      <label>Addressee</label>
      <input type="text" id="pdoc-filter-addressee" placeholder="Addressee…" style="width:280px;">
    </div>
    <div class="pdoc-sf">
      <label>Subject</label>
      <input type="text" id="pdoc-filter-subject" placeholder="Subject…" style="width:280px;">
    </div>
    <div class="pdoc-sf">
      <label>Date</label>
      <input type="date" id="pdoc-filter-date" style="width:130px;">
    </div>
    <div class="pdoc-sf">
      <label>Type</label>
      <select id="pdoc-filter-type" style="min-width:110px;">
        <option value="">All Types</option>
        <option value="documents">Documents</option>
        <option value="correspondence">Correspondence</option>
      </select>
    </div>
    <button id="pdoc-search-btn">Search</button>
    <button id="pdoc-clear-btn">Clear</button>
  </div>
  <div id="pdoc-table-wrap">
    <div id="pdoc-loading">Loading…</div>
    <div id="pdoc-empty">No documents found.</div>
    <table id="pdoc-table" style="display:none;">
      <thead>
        <tr>
          <th style="width:90px;">Date</th>
          <th>Project</th>
          <th>Addressee</th>
          <th>Subject / File</th>
          <th style="width:60px;text-align:center;">View</th>
          <th style="width:70px;text-align:center;">Remove</th>
        </tr>
      </thead>
      <tbody id="pdoc-tbody"></tbody>
    </table>
  </div>
</div>

<!-- File Viewer Floating Popup -->
<div id="pdoc-viewer-popup">
  <div id="pdoc-viewer-header">
    <span id="pdoc-viewer-title"></span>
    <button id="pdoc-viewer-close">&times;</button>
  </div>
  <div id="pdoc-viewer-body"></div>
</div>

<script>
(function(){
    var PDOC_LIST_URL  = '<?php echo \yii\helpers\Url::to(["/projects/pdoclist"]); ?>';
    var PDOC_PROJ_URL  = '<?php echo \yii\helpers\Url::to(["/projects/pdocprojects"]); ?>';
    var PDOC_FILE_BASE   = '/uploads/projects/';
    var PDOC_DELETE_URL  = '<?php echo \yii\helpers\Url::to(["/projects/deleteprojectfile"]); ?>';
    var pdocInited = false, pdocViewerInited = false;

    function pdocShow(){
        document.dispatchEvent(new Event('pdoc:open'));
        var $p = $('#pdoc-popup');
        if(!pdocInited){
            /* show briefly off-screen to measure, then position */
            $p.css({ visibility:'hidden', top:0, left:0 }).addClass('pdoc-open');
            var pw = $p.outerWidth(), ph = $p.outerHeight();
            var ww = $(window).width(), wh = $(window).height();
            $p.css({ visibility:'', top: Math.max(60, (wh - ph) / 2), left: Math.max(0, (ww - pw) / 2) });
            $p.draggable({ handle:'#pdoc-header', containment:'window', scroll:false });
            $p.resizable({ minWidth:500, minHeight:280, handles:'all' });
            pdocInited = true;
        } else {
            $p.addClass('pdoc-open');
        }
        pdocLoadProjects();
        pdocSearch();
    }

    function pdocHide(){
        $('#pdoc-popup').removeClass('pdoc-open');
    }

    function pdocLoadProjects(){
        if($('#pdoc-filter-project option').length > 1) return;
        $.ajax({ type:'POST', url:PDOC_PROJ_URL, dataType:'json', success:function(d){
            if(!d || d.error !== 'No') return;
            var sel = $('#pdoc-filter-project');
            $.each(d.projects||[], function(i,p){
                var opt = '<option value="'+p.id+'">'+$('<div>').text(p.name).html()+'</option>';
                sel.append(opt);
            });
        }});
    }

    function pdocSearch(){
        $('#pdoc-loading').show();
        $('#pdoc-empty').hide();
        $('#pdoc-table').hide();
        $('#pdoc-tbody').empty();
        $.ajax({
            type:'POST', url:PDOC_LIST_URL, dataType:'json',
            data:{
                project_id : $('#pdoc-filter-project').val(),
                addressee  : $('#pdoc-filter-addressee').val(),
                subject    : $('#pdoc-filter-subject').val(),
                date_filter: $('#pdoc-filter-date').val(),
                file_type  : $('#pdoc-filter-type').val()
            },
            success:function(d){
                $('#pdoc-loading').hide();
                if(!d || d.error!=='No' || !d.files || !d.files.length){ $('#pdoc-empty').show(); return; }
                var rows='';
                $.each(d.files,function(i,f){
                    var badge = f.file_type==='documents'
                        ? '<span class="pdoc-type-badge pdoc-badge-doc">Document</span>'
                        : '<span class="pdoc-type-badge pdoc-badge-corr">Correspondence</span>';
                    var fn   = $('<div>').text(f.original_name).html();
                    var addr = $('<div>').text(f.addressee||'—').html();
                    var subj = f.subject ? $('<div>').text(f.subject).html() : '';
                    var proj = $('<div>').text(f.project_name||'—').html();
                    var subjCell = subj
                        ? '<span style="color:#1a1a1a;">'+subj+'</span><div style="font-size:10px;color:#444;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="'+fn+'">'+fn+'</div>'
                        : '<span style="color:#1a1a1a;">'+fn+'</span>';
                    rows += '<tr>'
                        +'<td style="white-space:nowrap;">'+f.uploaded_at+'</td>'
                        +'<td>'+proj+'</td>'
                        +'<td style="color:#1a1a1a;">'+addr+'</td>'
                        +'<td>'+subjCell+'</td>'
                        +'<td style="text-align:center;"><button class="pdoc-view-btn" data-fn="'+f.filename+'" data-name="'+fn+'">View</button></td>'
                        +'<td style="text-align:center;"><button class="pdoc-remove-btn" data-id="'+f.id+'" data-name="'+fn+'">Remove</button></td>'
                        +'</tr>';
                });
                $('#pdoc-tbody').html(rows);
                $('#pdoc-table').show();
            },
            error:function(){ $('#pdoc-loading').hide(); $('#pdoc-empty').show(); }
        });
    }

    function pdocViewFile(filename, displayName){
        document.dispatchEvent(new Event('pdocv:open'));
        var url = PDOC_FILE_BASE + filename;
        var ext = filename.split('.').pop().toLowerCase();
        var $body = $('#pdoc-viewer-body');
        var $vp   = $('#pdoc-viewer-popup');
        $body.empty();
        $('#pdoc-viewer-title').text(displayName);
        if(ext==='pdf'){
            $body.html('<iframe src="'+url+'" style="width:100%;height:100%;border:none;display:block;"></iframe>');
        } else if(['jpg','jpeg','png','gif','bmp','webp','svg'].indexOf(ext)>=0){
            $body.html('<img src="'+url+'" alt="'+displayName+'" style="max-width:100%;max-height:100%;object-fit:contain;display:block;margin:auto;">');
        } else {
            $body.html('<div class="pdoc-no-preview"><div style="font-size:44px;">&#128196;</div><div>Preview not available.</div><a href="'+url+'" target="_blank" style="color:#1565C0;font-weight:600;">Download / Open in new tab</a></div>');
        }
        if(!pdocViewerInited){
            $vp.css({ visibility:'hidden', top:0, left:0 }).addClass('pdoc-open');
            var vw = $vp.outerWidth(), vh = $vp.outerHeight();
            var ww = $(window).width(), wh = $(window).height();
            $vp.css({ visibility:'', top: Math.max(40, (wh - vh) / 2), left: Math.max(0, (ww - vw) / 2) });
            $vp.draggable({ handle:'#pdoc-viewer-header', containment:'window', scroll:false });
            $vp.resizable({ minWidth:320, minHeight:260, handles:'all' });
            pdocViewerInited = true;
        } else {
            $vp.addClass('pdoc-open');
        }
    }

    var PDOC_UPLOAD_URL = '<?php echo \yii\helpers\Url::to(["/projects/pdocupload"]); ?>';

    /* ── Upload panel toggle ── */
    $(document).on('click','#pdoc-upload-toggle', function(){
        $('#pdoc-upload-panel').slideToggle(150);
    });

    /* Show Addressee+Subject as soon as a correspondence file is chosen */
    $(document).on('change','#pdoc-up-corr-file', function(){
        if(this.files && this.files.length > 0){
            $('#pdoc-up-corr-meta').slideDown(150);
        } else {
            $('#pdoc-up-corr-meta').slideUp(150);
            $('#pdoc-up-addressee,#pdoc-up-subject').val('');
        }
    });

    /* ── Generic upload helper ── */
    function pdocDoUpload(type, fileInput, $btn, $msg, onSuccess){
        var pid   = CURRENT_PID;
        var files = fileInput.files;
        $msg.css('color','#c00').text('');
        if(!pid)         { $msg.text('No active project. Please select a project first.'); return; }
        if(!files.length){ $msg.text('Choose a file first.'); return; }

        var fd = new FormData();
        fd.append('project_id', pid);
        fd.append('file_type',  type);
        if(type === 'correspondence'){
            fd.append('addressee', $.trim($('#pdoc-up-addressee').val()));
            fd.append('subject',   $.trim($('#pdoc-up-subject').val()));
        }
        var field = (type === 'correspondence') ? 'project_correspondence[]' : 'project_documents[]';
        for(var i=0;i<files.length;i++) fd.append(field, files[i]);

        $btn.prop('disabled',true).text('Uploading…');
        $.ajax({
            type:'POST', url:PDOC_UPLOAD_URL,
            data:fd, dataType:'json', contentType:false, processData:false,
            success:function(d){
                $btn.prop('disabled',false).html('&#8679; Upload');
                if(d.error !== 'No'){
                    $msg.css('color','#c00').text(d.errortext||'Upload failed.');
                } else {
                    $msg.css('color','#197a3a').text('Uploaded successfully.');
                    onSuccess();
                    pdocSearch();
                    setTimeout(function(){ $msg.text(''); }, 3000);
                }
            },
            error:function(xhr){
                $btn.prop('disabled',false).html('&#8679; Upload');
                $msg.css('color','#c00').text('Server error ('+xhr.status+').');
            }
        });
    }

    /* Document upload */
    $(document).on('click','#pdoc-up-doc-submit', function(){
        pdocDoUpload('documents', document.getElementById('pdoc-up-doc-file'),
            $(this), $('#pdoc-up-doc-msg'), function(){
                $('#pdoc-up-doc-file').val('');
            });
    });

    /* Correspondence upload */
    $(document).on('click','#pdoc-up-corr-submit', function(){
        pdocDoUpload('correspondence', document.getElementById('pdoc-up-corr-file'),
            $(this), $('#pdoc-up-corr-msg'), function(){
                $('#pdoc-up-corr-file').val('');
                $('#pdoc-up-addressee,#pdoc-up-subject').val('');
                $('#pdoc-up-corr-meta').slideUp(150);
            });
    });

    /* Cancel buttons */
    $(document).on('click','#pdoc-up-doc-cancel', function(){
        $('#pdoc-up-doc-file').val('');
        $('#pdoc-up-doc-msg').text('');
    });
    $(document).on('click','#pdoc-up-corr-cancel', function(){
        $('#pdoc-up-corr-file').val('');
        $('#pdoc-up-addressee,#pdoc-up-subject').val('');
        $('#pdoc-up-corr-meta').slideUp(150);
        $('#pdoc-up-corr-msg').text('');
    });

    $(document).on('click','#pdoc-nav-btn',  function(e){ e.preventDefault(); pdocShow(); });
    $(document).on('click','#pdoc-close-x',  function(){ pdocHide(); });
    $(document).on('click','#pdoc-search-btn',function(){ pdocSearch(); });
    $(document).on('click','#pdoc-clear-btn', function(){
        $('#pdoc-filter-project,#pdoc-filter-type').val('');
        $('#pdoc-filter-addressee,#pdoc-filter-subject,#pdoc-filter-date').val('');
        pdocSearch();
    });
    $(document).on('click','.pdoc-view-btn', function(){
        pdocViewFile($(this).data('fn'), $(this).data('name'));
    });
    $(document).on('click','.pdoc-remove-btn', function(){
        var id   = $(this).data('id');
        var name = $(this).data('name');
        if(!confirm('Remove "'+name+'"? This cannot be undone.')) return;
        var $btn = $(this).prop('disabled',true).text('…');
        $.ajax({ type:'POST', url:PDOC_DELETE_URL, dataType:'json', data:{fileid:id},
            success:function(d){
                if(d && d.error==='No'){ $btn.closest('tr').fadeOut(200,function(){ $(this).remove(); }); }
                else { $btn.prop('disabled',false).text('Remove'); alert('Could not remove file.'); }
            },
            error:function(){ $btn.prop('disabled',false).text('Remove'); alert('Server error.'); }
        });
    });
    $(document).on('click','#pdoc-viewer-close', function(){
        $('#pdoc-viewer-popup').removeClass('pdoc-open');
        $('#pdoc-viewer-body').empty();
    });
})();
</script>
<!-- ── End Project Documents Floating Popup ───────────────────────────── -->

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
#pd-bk{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:100000}
#pd-bk.pd-open{display:block}
#pd-modal{display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:78vw;max-width:1180px;height:88vh;z-index:100001;border-radius:6px;overflow:hidden;background:#f0f2f7;box-shadow:0 8px 32px rgba(0,0,0,.7);flex-direction:column}
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
#pd-tip{position:fixed;z-index:100300;background:#0c1535;color:#e8ecf4;font-family:'Barlow Condensed',sans-serif;font-size:12px;line-height:1.4;padding:6px 18px;border-radius:4px;pointer-events:none;display:none;white-space:pre;box-shadow:0 3px 12px rgba(0,0,0,.4);min-width:320px;max-width:420px;}
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
#pd-tasks-tip{position:fixed;z-index:100300;background:#0c1535;border:1px solid #263d6e;border-radius:8px;box-shadow:0 8px 28px rgba(0,0,0,.6);padding:14px 16px 12px;display:none;pointer-events:auto;box-sizing:border-box;overflow-y:auto;}
#pd-tasks-tip .tip-title{font-family:'Barlow Condensed',sans-serif;font-size:17px;font-weight:700;color:#a8d4f5;text-transform:uppercase;letter-spacing:.7px;margin-bottom:10px;border-bottom:1px solid rgba(255,255,255,.15);padding-bottom:7px;}
#pd-tasks-tip table{width:100%;border-collapse:collapse;}
#pd-tasks-tip table th{font-family:'Barlow Condensed',sans-serif;font-size:12px;color:#7aafd4 !important;text-transform:uppercase;letter-spacing:.5px;font-weight:600;padding:0 10px 8px 0;}
#pd-tasks-tip table td{font-family:'Barlow Condensed',sans-serif;font-size:16px;color:#e8f0fc !important;padding:6px 10px 6px 0;border-top:1px solid rgba(255,255,255,.07);}
#pd-tasks-tip table td:not(:first-child){text-align:right;padding-right:0;}
</style>
<style>
#cd-bk{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:100000}
#cd-bk.cd-open{display:block}
#cd-modal{display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:78vw;max-width:1180px;height:88vh;z-index:100001;border-radius:6px;overflow:hidden;background:#f0f2f7;box-shadow:0 8px 32px rgba(0,0,0,.7);flex-direction:column}
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

<script>
/* ── Shared touch adapter for draggable/resizable popup windows ──
   Every floating popup (WBS Entry, Gantt, Resource Library, Activity
   Relationships, Activity Library) implements its own drag/resize using
   raw mousedown/mousemove/mouseup, which never fire on touch devices
   (iPad, tablets). Rather than rewrite each one, this registers the touch
   equivalent alongside every such listener and normalizes the touch event
   to look like a mouse event (clientX/clientY) before calling the same
   handler, so none of the existing drag logic needs to change. */
(function(){
  function _touchToMouseLike(te){
    var t = te.touches[0] || te.changedTouches[0];
    return { clientX: t ? t.clientX : 0, clientY: t ? t.clientY : 0,
             target: te.target, preventDefault: function(){ te.preventDefault(); },
             stopPropagation: function(){ te.stopPropagation(); } };
  }
  window.bindDragTouch = function(el, mouseType, handler, opts){
    if(!el) return; /* guard against a null element so callers never throw */
    var touchType = mouseType === 'mousedown' ? 'touchstart'
                  : mouseType === 'mousemove' ? 'touchmove' : 'touchend';
    el.addEventListener(mouseType, handler, opts);
    el.addEventListener(touchType, function(te){
      if(te.touches && te.touches.length > 1) return; /* let pinch-zoom etc. through */
      handler(_touchToMouseLike(te));
    }, Object.assign({ passive: false }, opts || {}));
  };
})();
</script>

<!-- ══════════════════════════════════════════════════════════════════════
     WBS / IOW ENTRY MODAL
════════════════════════════════════════════════════════════════════════ -->
<style>
#qe-bk{display:none}
#qe-modal{
  display:none;position:fixed;top:60px;right:20px;
  width:760px;height:92vh;
  min-width:380px;min-height:280px;
  z-index:100100;border-radius:6px;overflow:hidden;
  background:#fff;box-shadow:0 8px 32px rgba(0,0,0,.45);
  flex-direction:column;
  font-family:'Barlow',sans-serif;
}
/* resize handles */
.qe-rs{position:absolute;z-index:10;background:transparent;}
.qe-rs-e {right:0;top:6px;bottom:6px;width:6px;cursor:e-resize;}
.qe-rs-w {left:0;top:6px;bottom:6px;width:6px;cursor:w-resize;}
.qe-rs-s {bottom:0;left:6px;right:6px;height:6px;cursor:s-resize;}
.qe-rs-n {top:0;left:6px;right:6px;height:6px;cursor:n-resize;}
.qe-rs-se{right:0;bottom:0;width:14px;height:14px;cursor:se-resize;}
.qe-rs-sw{left:0;bottom:0;width:14px;height:14px;cursor:sw-resize;}
.qe-rs-ne{right:0;top:0;width:14px;height:14px;cursor:ne-resize;}
.qe-rs-nw{left:0;top:0;width:14px;height:14px;cursor:nw-resize;}
#qe-modal.qe-open{display:flex}
#qe-hdr{
  display:flex;align-items:center;justify-content:space-between;
  background:#1a202c;color:#fff;padding:8px 14px;
  cursor:move;user-select:none;flex-shrink:0;
  font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;letter-spacing:.5px;
}
#qe-close{
  display:inline-flex;align-items:center;justify-content:center;
  background:transparent;border:none;color:#fff;font-size:18px;
  cursor:pointer;padding:0 4px;line-height:1;
}
#qe-body{flex:1;min-height:0;overflow-y:auto;overflow-x:hidden;padding:0 20px 20px;display:flex;flex-direction:column;gap:0;scroll-behavior:smooth;}
#qe-body::-webkit-scrollbar{width:5px}
#qe-body::-webkit-scrollbar-track{background:#f1f1f1}
#qe-body::-webkit-scrollbar-thumb{background:#a0aab8;border-radius:3px}
#qe-body::-webkit-scrollbar-thumb:hover{background:#6b7a93}

/* Section wrapper */
.qe-section{background:#fff;border-radius:0;border:none;margin-bottom:0;overflow:visible}
.qe-section+.qe-section{margin-top:0}
.qe-sec-hdr{display:none}
.qe-sec-body{padding:14px 16px}

/* Label + field */
.qe-row{display:flex;flex-wrap:wrap;gap:14px 16px;align-items:flex-end}
.qe-field{display:flex;flex-direction:column;min-width:80px}
.qe-field.wide{flex:1 1 180px}
.qe-field.med{flex:1 1 120px}
.qe-field.sm{flex:0 0 100px}
.qe-field.xs{flex:0 0 80px}
.qe-label{font-size:11px;font-weight:900 !important;color:#000 !important;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px}
.qe-input,.qe-select{
  width:100%;padding:5px 10px;font-size:13px;color:#718096 !important;
  border:1px solid #a0aab8;border-radius:0;background:#fff;
  outline:none;box-sizing:border-box;font-family:'Barlow',sans-serif;
  transition:border-color .15s;height:28px;font-weight:600;
}
.qe-input:focus,.qe-select:focus{border-color:#4a5568;background:#fff}
.qe-input[readonly]{background:#f5f5f5;color:#555;cursor:default}
.qe-needs-data{border-color:#e53e3e !important;}
.qe-act-wrap{position:relative;display:flex;align-items:stretch;}
.qe-act-wrap .qe-input{flex:1;border-radius:4px 0 0 4px;border-right:none;}
.qe-act-drop-btn{width:26px;flex-shrink:0;border:1px solid #a0aab8;border-left:none;border-radius:0 4px 4px 0;background:#f0f3fa;color:#4a5568;font-size:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;line-height:1;}
.qe-act-drop-btn:hover{background:#dde3ef;}
.qe-act-list{display:none;position:absolute;top:100%;left:0;right:0;z-index:99999;background:#fff;border:1px solid #a0aab8;border-top:none;border-radius:0 0 4px 4px;max-height:220px;overflow-y:auto;margin:0;padding:0;list-style:none;box-shadow:0 4px 14px rgba(0,0,0,.18);}
.qe-act-list.open{display:block;}
.qe-act-list li{padding:6px 10px;font-size:12px;color:#1a202c;cursor:pointer;border-bottom:1px solid #f0f3fa;}
.qe-act-list li:last-child{border-bottom:none;}
.qe-act-list li:hover,.qe-act-list li.hl{background:#ebf0ff;color:#1a2540;}
.qe-act-list li.new-act{color:#2b6cb0;font-style:italic;}
.qe-repeat-tbl td input,.qe-repeat-tbl td select{font-weight:700;}
.qe-repeat-tbl td input,.qe-repeat-tbl td select{height:32px;}
.qe-repeat-tbl td input.qe-res-amt{border:1px solid #a0aab8;}
.qe-persist-note{font-size:9px;color:#888;font-style:italic;margin-top:2px}

/* Divider */
.qe-divider{display:none}

/* Repeating rows (tasks / resources) */
.qe-repeat-tbl{width:100%;border-collapse:collapse}
.qe-repeat-tbl th{
  font-size:11px;font-weight:900 !important;color:#000 !important;text-transform:uppercase;
  letter-spacing:.4px;padding:0 6px 8px 0;border-bottom:2px solid #000;
  white-space:nowrap;
}
.qe-repeat-tbl td{padding:6px 6px 6px 0;vertical-align:middle}
.qe-repeat-tbl td:last-child{padding-right:0}
.qe-repeat-tbl input,.qe-repeat-tbl select{
  width:100%;padding:7px 9px;font-size:13px;color:#718096 !important;
  border:1px solid #a0aab8;border-radius:0;background:#fff;
  outline:none;font-family:'Barlow',sans-serif;transition:border-color .15s;
  box-sizing:border-box;height:28px;
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
.qe-map-btn{
  display:inline-flex;align-items:center;justify-content:center;
  width:18px;height:18px;border-radius:50%;border:none;cursor:pointer;
  background:#27ae60;color:#fff;font-size:10px;line-height:1;
  flex-shrink:0;opacity:.9;
}
.qe-map-btn:hover{opacity:1;background:#1e8449}
.qe-map-btn.has-map{background:#1a7a42;box-shadow:0 0 0 2px #a9dfbf;}

/* Task Mapping Popup */
#qe-map-bk{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:100200;}
#qe-map-bk.open{display:block;}
#qe-map-popup{
  display:none;position:fixed;z-index:100201;
  top:50%;left:50%;transform:translate(-50%,-50%);
  width:420px;max-width:92vw;max-height:80vh;
  background:#fff;border-radius:0;
  box-shadow:0 12px 40px rgba(0,0,0,.6);
  flex-direction:column;overflow:hidden;
  font-family:'Barlow',sans-serif;
}
#qe-map-popup.open{display:flex;}
#qe-map-hdr{
  background:#1a3052;padding:12px 16px;flex-shrink:0;
}
#qe-map-title{
  font-size:13px;font-weight:900;color:#e8f0fb;
  text-transform:uppercase;letter-spacing:.5px;
}
#qe-map-res-name{
  font-size:11px;color:#7aacda;margin-top:3px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
#qe-map-body{
  flex:1;min-height:0;overflow-y:auto;padding:0;
}
#qe-map-no-tasks{
  padding:30px 16px;text-align:center;
}
.qe-map-item{
  display:flex;align-items:center;gap:12px;
  padding:11px 16px;border-bottom:1px solid #edf0f4;
  cursor:pointer;transition:background .1s;
}
.qe-map-item:hover{background:#f0faf4;}
.qe-map-item.selected{background:#e8f8ef;}
.qe-map-item input[type=checkbox]{
  width:16px;height:16px;flex-shrink:0;
  accent-color:#27ae60;cursor:pointer;
}
.qe-map-item-idx{
  width:22px;height:22px;border-radius:50%;
  background:#e2e8f0;color:#4a5568;
  font-size:10px;font-weight:700;
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.qe-map-item.selected .qe-map-item-idx{background:#27ae60;color:#fff;}
.qe-map-item-name{font-size:13px;color:#1a202c;font-weight:600;flex:1;}
.qe-map-item-unit{font-size:11px;color:#718096;}
#qe-map-footer{
  background:#f7f8fa;border-top:1px solid #dde;
  padding:8px 16px;display:flex;align-items:center;gap:8px;flex-shrink:0;
}
#qe-map-select-all,#qe-map-clear{
  background:none;border:1px solid #a0aab8;color:#4a5568;
  font-size:11px;font-weight:700;padding:4px 12px;cursor:pointer;border-radius:3px;
}
#qe-map-select-all:hover{background:#edf0f4;}
#qe-map-clear:hover{background:#edf0f4;}
#qe-map-done{
  background:#27ae60;color:#fff;border:none;
  font-size:12px;font-weight:700;padding:6px 20px;
  cursor:pointer;border-radius:3px;
}
#qe-map-done:hover{background:#1e8449;}

/* Footer bar */
#qe-footer{
  background:#fff;border-top:1px solid #ddd;
  padding:8px 20px;display:flex;align-items:center;gap:12px;flex-shrink:0;
}
#qe-duration-display{
  flex:1;font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:600;color:#2d3748;
}
#qe-duration-display span{color:#4a5568;margin-left:4px}
#qe-save-msg{font-size:11px;font-weight:700;flex:1}
#qe-btn-save{
  background:#27ae60;color:#fff;border:none;border-radius:999px;
  padding:6px 22px;font-size:12px;font-weight:700;font-family:'Nunito',sans-serif;
  cursor:pointer;letter-spacing:.3px;text-transform:uppercase;
}
#qe-btn-save:hover{background:#1e8449}
#qe-btn-save:disabled{background:#aaa;cursor:default}
#qe-btn-add{
  background:#4a4a4a !important;color:#fff !important;border:none;border-radius:999px;
  padding:6px 22px;font-size:12px;font-weight:700;font-family:'Nunito',sans-serif;
  cursor:pointer;letter-spacing:.3px;text-transform:uppercase;
}
#qe-btn-add:hover{background:#333}
#qe-btn-add:disabled{background:#aaa;cursor:default}
</style>

<div id="qe-bk"></div>
<div id="qe-modal">
  <div class="qe-rs qe-rs-e"  data-dir="e"></div>
  <div class="qe-rs qe-rs-w"  data-dir="w"></div>
  <div class="qe-rs qe-rs-s"  data-dir="s"></div>
  <div class="qe-rs qe-rs-n"  data-dir="n"></div>
  <div class="qe-rs qe-rs-se" data-dir="se"></div>
  <div class="qe-rs qe-rs-sw" data-dir="sw"></div>
  <div class="qe-rs qe-rs-ne" data-dir="ne"></div>
  <div class="qe-rs qe-rs-nw" data-dir="nw"></div>
  <div id="qe-hdr">
    <span>&#9998; WBS Entry &mdash; <span style="font-weight:400;opacity:.75;font-size:11px;">drag to move</span></span>
    <button id="qe-close">&times;</button>
  </div>
  <div id="qe-body">

    <div style="padding:14px 4px 10px;font-family:'Nunito',sans-serif;font-size:18px;font-weight:900;color:#1a202c;letter-spacing:.5px;text-transform:uppercase;border-bottom:3px solid #1a202c;margin-bottom:14px;">WBS <span id="qe-proj-label" style="font-size:13px;font-weight:600;color:#4a5568;text-transform:none;letter-spacing:0;"></span></div>

    <!-- ── SECTION 1 : Project Type + Group (persists) ─────────────── -->
    <div class="qe-section">
      <div class="qe-sec-hdr">Project Type &amp; Group <span style="font-weight:400;opacity:.7">&mdash; stays selected until you change it</span></div>
      <div class="qe-sec-body">
        <div class="qe-row">
          <div class="qe-field wide">
            <span class="qe-label">Project Type</span>
            <select id="qe-proj-type" class="qe-select qe-needs-data">
              <option value="">— Select Project Type —</option>
            </select>
          </div>
          <div class="qe-field wide">
            <span class="qe-label">Activity Type</span>
            <select id="qe-group" class="qe-select qe-needs-data">
              <option value="">— Select Group —</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- ── SECTION 2 : IOW Group + IOW + Activity ───────────────────── -->
    <div class="qe-section">
      <div class="qe-sec-hdr">IOW Group, IOW &amp; Activity</div>
      <div class="qe-sec-body">
        <div style="border-top:2px solid #2d3748;margin-bottom:10px;"></div>
        <div class="qe-row">
          <div class="qe-field wide">
            <span class="qe-label">IOW Group</span>
            <select id="qe-iow-group" class="qe-select qe-needs-data">
              <option value="">— Select IOW Group —</option>
            </select>
          </div>
          <div class="qe-field wide">
            <span class="qe-label">IOW</span>
            <input type="text" id="qe-iow" class="qe-input qe-needs-data" placeholder="Enter IOW name">
            <input type="hidden" id="qe-wg-id" value="">
          </div>
        </div>
        <div class="qe-row">
          <div class="qe-field wide">
            <span class="qe-label">Activity</span>
            <div class="qe-act-wrap">
              <input type="text" id="qe-activity-text" class="qe-input qe-needs-data" placeholder="Select or type activity" autocomplete="off">
              <button type="button" class="qe-act-drop-btn" id="qe-act-drop-btn" tabindex="-1">&#9660;</button>
              <ul class="qe-act-list" id="qe-act-list"></ul>
            </div>
            <input type="hidden" id="qe-activity-id">
          </div>
        </div>
      </div>
    </div>

    <!-- ── SECTION 3 : Activity Details ──────────────────────────────── -->
    <div class="qe-section">
      <div class="qe-sec-hdr">Activity Details</div>
      <div class="qe-sec-body">
        <div class="qe-row">
          <div style="font-size:9px;font-weight:700;color:#4a5568;text-transform:uppercase;letter-spacing:.5px;width:100%;margin-bottom:2px;">Estimate</div>
          <div class="qe-field sm">
            <span class="qe-label">Unit</span>
            <input id="qe-unit" type="text" class="qe-input" placeholder="e.g. m³" data-alpha="1">
          </div>
          <div class="qe-field xs">
            <span class="qe-label">Quantity</span>
            <input id="qe-qty" type="number" class="qe-input qe-needs-data" placeholder="0" step="0.001">
          </div>
          <div class="qe-field xs">
            <span class="qe-label">Rate</span>
            <input id="qe-rate" type="number" class="qe-input" placeholder="0.00" step="0.01" readonly>
          </div>
          <div class="qe-field" style="flex:0 0 120px;">
            <span class="qe-label">Amount</span>
            <input id="qe-amount" type="number" class="qe-input" placeholder="0.00" readonly>
          </div>
          <div class="qe-field sm">
            <span class="qe-label">Sch. Unit</span>
            <input id="qe-sch-unit" type="text" class="qe-input qe-needs-data" placeholder="e.g. Nos" data-alpha="1">
          </div>
          <div class="qe-field xs">
            <span class="qe-label">Sch. Qty</span>
            <input id="qe-sch-qty" type="number" class="qe-input qe-needs-data" placeholder="0" step="0.001">
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
              <th style="width:28%">Task Name</th>
              <th style="width:12%">Unit</th>
              <th style="width:12%">Qty</th>
              <th style="width:16%">Productivity / Day</th>
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
        <div style="font-size:12px;color:#4a5568;font-style:italic;margin-bottom:8px;">Resources Qty should be allocated for one unit of the activity</div>
        <table class="qe-repeat-tbl" id="qe-res-tbl">
          <thead>
            <tr>
              <th style="width:15%">Resource Type</th>
              <th style="width:15%">Resource Group</th>
              <th style="width:18%">Resource Name</th>
              <th style="width:9%">Unit</th>
              <th style="width:9%">Qty/Unit</th>
              <th style="width:10%">Rate/Unit</th>
              <th style="width:10%">Amount</th>
              <th style="width:7%;text-align:right">
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
    <div id="qe-save-msg"></div>
    <button id="qe-btn-save">&#128190; Save</button>
    <button id="qe-btn-add">&#43; Add to Gantt</button>
    <button id="qe-btn-delete" style="display:none;background:#c0392b;color:#fff;border:none;padding:6px 20px;font-size:12px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;border-radius:999px;">&#128465; Delete</button>
    <button id="qe-close-btn" style="background:#e67e22;color:#fff;border:none;padding:6px 20px;font-size:12px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;border-radius:999px;">&#10005; Close</button>
  </div>
</div><!-- /qe-modal -->

<!-- ── Task Mapping Popup ──────────────────────────────────────────────── -->
<div id="qe-map-bk"></div>
<div id="qe-map-popup">
  <div id="qe-map-hdr">
    <div id="qe-map-title">Map Resource to Tasks</div>
    <div id="qe-map-res-name"></div>
  </div>
  <div id="qe-map-body">
    <div id="qe-map-no-tasks" style="display:none;">
      <div class="icon-info3" style="font-size:32px;color:#a0aab8;margin-bottom:8px;"></div>
      <div style="color:#6b7a93;font-size:13px;">No tasks added yet.<br>Add tasks in the Tasks section first.</div>
    </div>
    <div id="qe-map-list"></div>
  </div>
  <div id="qe-map-footer">
    <button id="qe-map-select-all">Select All</button>
    <button id="qe-map-clear">Clear</button>
    <div style="flex:1"></div>
    <button id="qe-map-done">&#10003; Done</button>
  </div>
</div>

<script>
(function(){
'use strict';

/* ── mode tracking ── */
/* _wbsMode: 'new' (WBS icon) or 'edit' (bar click) */
var _wbsMode   = 'new';
var _wbsWanId  = 0;   /* wan_id returned by wbssave / from wbsget */
var _wbsSaId   = 0;   /* scheduleactivities.id — set in edit mode */

/* ── open / close ── */
function openModal(saId, wanId){
  document.getElementById('qe-modal').classList.add('qe-open');
  document.dispatchEvent(new Event('wbs:open'));
  if(window._actComboInit) _actComboInit();
  var pname = (document.getElementById('selectedProject')||{}).value || '';
  var lbl = document.getElementById('qe-proj-label');
  if(lbl && pname) lbl.textContent = '— ' + pname;
  loadProjTypes();
  loadIowGroups();

  if(saId || wanId){
    /* edit mode — existing bar */
    _wbsMode  = 'edit';
    _wbsWanId = wanId || 0;
    _wbsSaId  = saId  || 0;
    document.getElementById('qe-btn-add').style.display = 'none';
    document.getElementById('qe-btn-delete').style.display = '';
    document.getElementById('qe-btn-save').textContent = '💾 Save';
    $.ajax({
      type:'POST', url:'../projectsmain/wbsget', dataType:'json',
      data:{ sa_id: saId || 0, wan_id: wanId || 0 },
      success: function(d){
        if(d.error) { addTaskRow(); loadResTypes(function(){ addResRow(); }); return; }
        if(d.wan_id) _wbsWanId = d.wan_id;
        if(d.sa_id)  _wbsSaId  = d.sa_id;
        _prefillModal(d);
      },
      error: function(){ addTaskRow(); loadResTypes(function(){ addResRow(); }); }
    });
  } else {
    /* new entry mode — WBS icon */
    _wbsMode  = 'new';
    _wbsWanId = 0;
    _wbsSaId  = 0;
    document.getElementById('qe-btn-add').style.display = '';
    document.getElementById('qe-btn-delete').style.display = 'none';
    document.getElementById('qe-btn-save').textContent = '💾 Save';
    if(!document.querySelector('#qe-task-body tr')) addTaskRow();
    if(!document.querySelector('#qe-res-body tr'))  { loadResTypes(function(){ addResRow(); }); }
    recalcDuration();
  }
}

function _prefillModal(d){
  /* Activity name — always shown from the saved record, independent of
     whether Project Type/Group are set on it (older/incomplete rows may
     have no project type, which must not hide the activity itself). */
  var actTxt = document.getElementById('qe-activity-text');
  if(actTxt && d.act_name){
    actTxt.value = d.act_name;
    actTxt.classList.remove('qe-needs-data');
  }
  if(d.iow_act_id){
    document.getElementById('qe-activity-id').value = d.iow_act_id;
  }

  /* Project Type — cascade: type → groups → activities, then set all values */
  var ptSel = document.getElementById('qe-proj-type');
  loadProjTypes(function(){
    if(d.proj_type_id){
      ptSel.value = d.proj_type_id;
      ptSel.classList.remove('qe-needs-data');
      loadGroups(d.proj_type_id, function(){
        var grpSel = document.getElementById('qe-group');
        if(d.act_type_id){ grpSel.value = d.act_type_id; grpSel.classList.remove('qe-needs-data'); }
        loadActivities(d.proj_type_id, d.act_type_id, function(){
          if(d.iow_act_id){
            _setActivityById(d.iow_act_id, d.act_name);
          }
        });
      });
    }
  });

  /* IOW Group */
  loadIowGroups(function(){
    var iowGrpSel = document.getElementById('qe-iow-group');
    if(iowGrpSel && d.iow_group_id){ iowGrpSel.value = d.iow_group_id; iowGrpSel.classList.remove('qe-needs-data'); }
  });
  /* IOW */
  var iowEl = document.getElementById('qe-iow');
  iowEl.value = d.iow_name || d.iow_group_name || '';
  if(iowEl.value) iowEl.classList.remove('qe-needs-data');
  document.getElementById('qe-wg-id').value = d.wbs_id || '';

  /* Estimate fields — this is the "click an existing bar to edit" path
     (see _wbsMode='edit' above), so prefill Quantity with the real saved
     value. New-activity entry (WBS icon / typeahead-select while adding
     more activities) intentionally leaves Quantity blank — see
     mirrorEstimateToSchedule() and the typeahead 'change' handler below. */
  document.getElementById('qe-unit').value   = d.est_unit  || '';
  document.getElementById('qe-qty').value    = d.est_qty   || '';
  document.getElementById('qe-rate').value   = d.est_rate  || '';
  document.getElementById('qe-amount').value = d.est_amt   || '';
  if(d.est_qty) document.getElementById('qe-qty').classList.remove('qe-needs-data');

  /* Schedule fields — Sch. Unit and Sch. Qty both prefill here for the
     same edit-existing-bar reason as Estimate Qty above. */
  var schUnitEl = document.getElementById('qe-sch-unit');
  var schQtyEl  = document.getElementById('qe-sch-qty');
  schUnitEl.value = d.sch_unit || '';
  schQtyEl.value  = (d.sch_qty != null && d.sch_qty !== '') ? d.sch_qty : '';
  if(schUnitEl.value) schUnitEl.classList.remove('qe-needs-data');
  if(schQtyEl.value)  schQtyEl.classList.remove('qe-needs-data');
  /* real saved values — don't let the estimate→schedule mirror overwrite them */
  _schUnitTouched = !!schUnitEl.value;
  _schQtyTouched  = !!schQtyEl.value;

  /* Tasks and Resources — fetch from getactivityresources directly */
  if(d.iow_act_id){
    $.ajax({
      type:'POST', url:'../projectsmain/getactivityresources', dataType:'json',
      data:{ activity_id: d.iow_act_id },
      success: function(data){
        if(data.unit && !document.getElementById('qe-unit').value)
          document.getElementById('qe-unit').value = data.unit;
        /* Schedule unit/qty — only fill if not already set from wbsget */
        if(data.sch_unit && !document.getElementById('qe-sch-unit').value){
          document.getElementById('qe-sch-unit').value = data.sch_unit;
          document.getElementById('qe-sch-unit').classList.remove('qe-needs-data');
        }
        if(data.sch_qty && !document.getElementById('qe-sch-qty').value){
          document.getElementById('qe-sch-qty').value = data.sch_qty;
          document.getElementById('qe-sch-qty').classList.remove('qe-needs-data');
        }

        /* Tasks */
        var taskBody = document.getElementById('qe-task-body');
        taskBody.innerHTML = '';
        if(data.tasks && data.tasks.length){
          _task1NameTouched = true; _task1UnitTouched = true;
          data.tasks.forEach(function(task){
            var tr = document.createElement('tr');
            tr.innerHTML =
              '<td><input type="text" class="qe-task-name" value="'+(task.task_name||task.name||'').replace(/"/g,'&quot;')+'" placeholder="Task name"></td>'+
              '<td><input type="text" class="qe-task-unit" value="'+(task.task_unit||task.unit||'').replace(/"/g,'&quot;')+'" placeholder="Unit"></td>'+
              '<td><input type="number" class="qe-task-qty" value="'+parseFloat(task.task_qty||task.qty||0).toFixed(2)+'" placeholder="0.00" step="0.01" min="0"></td>'+
              '<td><input type="number" class="qe-task-prod" value="'+parseFloat(task.productivity||task.prod||0).toFixed(2)+'" placeholder="0.00" step="0.01" min="0"></td>'+
              '<td><input type="number" class="qe-task-resunits" value="'+(task.resunits||1)+'" placeholder="1" step="1" min="1"></td>'+
              '<td style="text-align:right"><button class="qe-del-btn qe-task-del" title="Remove">&times;</button></td>';
            taskBody.appendChild(tr);
            _bindNumField(tr.querySelector('.qe-task-qty'));
            tr.querySelector('.qe-task-prod').addEventListener('input', recalcDuration);
            tr.querySelector('.qe-task-resunits').addEventListener('input', recalcDuration);
            tr.querySelector('.qe-task-del').addEventListener('click', function(){
              if(document.querySelectorAll('#qe-task-body tr').length > 1){ tr.remove(); recalcDuration(); }
            });
            /* Loaded with a real saved qty — not the sch_qty default, so mark
               touched to keep mirrorScheduleToTasks() from overwriting it. */
            tr._taskQtyTouched = true;
            tr.querySelector('.qe-task-qty').addEventListener('input', function(){ tr._taskQtyTouched = true; });
          });
        } else { addTaskRow(); }

        /* Resources */
        document.getElementById('qe-res-body').innerHTML = '';
        if(data.items && data.items.length){
          loadResTypes(function(){
            data.items.forEach(function(res){
              addResRow({
                type_id:     res.type_id,
                group_id:    res.group_id,
                resource_id: res.est_resource_id || res.resource_id,
                unit:        res.resource_unit || res.unit || '',
                qty:         res.est_resource_quantity || res.qty,
                rate:        res.est_resource_rate || res.rate
              });
            });
          });
        } else { loadResTypes(function(){ addResRow(); }); }
        recalcDuration();
      }
    });
  } else {
    addTaskRow();
    loadResTypes(function(){ addResRow(); });
    recalcDuration();
  }
}

window.openQeModal    = openModal;
function closeModal(){
  document.getElementById('qe-modal').classList.remove('qe-open');
  _wbsMode = 'new'; _wbsWanId = 0; _wbsSaId = 0;
  _resetModal();
}

function _resetModal(){
  /* Section 1 — Project Type & Group */
  var ptEl = document.getElementById('qe-proj-type');
  if(ptEl){ ptEl.selectedIndex = 0; ptEl.classList.add('qe-needs-data'); }
  var grpEl = document.getElementById('qe-group');
  if(grpEl){ grpEl.innerHTML = '<option value="">— Select Group —</option>'; grpEl.classList.add('qe-needs-data'); }
  _activityItems = [];
  if(window._actListClose) _actListClose();

  /* Section 2 — IOW Group & IOW */
  var iowGrpSel = document.getElementById('qe-iow-group');
  if(iowGrpSel){ iowGrpSel.selectedIndex = 0; iowGrpSel.classList.add('qe-needs-data'); }
  var iowEl = document.getElementById('qe-iow');
  if(iowEl){ iowEl.value = ''; iowEl.classList.add('qe-needs-data'); }
  var wgIdEl = document.getElementById('qe-wg-id');
  if(wgIdEl){ wgIdEl.value = ''; }

  _resetModalActivityOnward();
}

/* Clears Activity, Estimate, Schedule, Tasks & Resources — leaves
   Project Type, Activity Type, IOW Group and IOW untouched. Used after a
   successful "Add to Gantt" so the user can keep adding activities under
   the same WBS context without reselecting it each time. */
function _resetModalActivityOnward(){
  var actTxt = document.getElementById('qe-activity-text');
  if(actTxt){ actTxt.value = ''; actTxt.classList.add('qe-needs-data'); }
  document.getElementById('qe-activity-id').value = '';

  /* Section 3 — Activity Details */
  ['qe-unit','qe-qty','qe-rate','qe-amount','qe-sch-unit','qe-sch-qty'].forEach(function(id){
    var el = document.getElementById(id);
    if(!el) return;
    el.value = '';
    if(el.classList.contains('qe-needs-data') !== undefined){ el.classList.add('qe-needs-data'); }
  });
  _schUnitTouched = false; _schQtyTouched = false;

  /* Section 4 & 5 — Tasks & Resources */
  document.getElementById('qe-task-body').innerHTML = '';
  document.getElementById('qe-res-body').innerHTML  = '';
  addTaskRow();
  loadResTypes(function(){ addResRow(); });

  /* Footer */
  document.getElementById('qe-save-msg').textContent = '';
  recalcDuration();
}

/* ── cascading dropdowns ── */

function loadIowGroups(cb){
  var sel = document.getElementById('qe-iow-group');
  if(!sel) { if(cb) cb(); return; }
  sel.innerHTML = '<option value="">— Select IOW Group —</option>';
  $.ajax({
    type:'POST', url:'../projects/getiowgrouplist', dataType:'json',
    success: function(d){
      (d.items||[]).forEach(function(item){
        var o = document.createElement('option');
        o.value = item.id; o.textContent = item.name;
        sel.appendChild(o);
      });
      if(cb) cb();
    }
  });
}

function loadProjTypes(cb){
  var sel = document.getElementById('qe-proj-type');
  if(sel.options.length > 1){ if(cb) cb(); return; }
  $.ajax({
    type:'POST', url:'../projectsmain/getwbtypelist', dataType:'json',
    success: function(d){
      (d.items||[]).forEach(function(item){
        var o = document.createElement('option');
        o.value = item.id; o.textContent = item.name;
        sel.appendChild(o);
      });
      if(cb) cb();
    }
  });
}

function loadGroups(typeId, cb){
  var sel = document.getElementById('qe-group');
  sel.innerHTML = '<option value="">— Select Group —</option>';
  _activityItems = [];
  if(window._actListClose) _actListClose();
  if(!typeId){ if(cb) cb(); return; }
  $.ajax({
    type:'POST', url:'../projectsmain/getwbgrouplist', dataType:'json',
    data:{typeId: typeId},
    success: function(d){
      (d.items||[]).forEach(function(item){
        var o = document.createElement('option');
        o.value = item.id; o.textContent = item.name;
        sel.appendChild(o);
      });
      if(cb) cb();
    }
  });
}

/* activity items cache for the current type/group combo */
var _activityItems = [];

function loadActivities(typeId, groupId, iowGroupId, cb){
  if(typeof iowGroupId === 'function'){ cb = iowGroupId; iowGroupId = null; }
  if(iowGroupId == null){
    var iowGroupEl = document.getElementById('qe-iow-group');
    iowGroupId = iowGroupEl ? iowGroupEl.value : '';
  }
  _activityItems = [];
  _actListClose();
  if(!typeId && !groupId && !iowGroupId){ if(cb) cb(); return; }
  $.ajax({
    type:'POST', url:'../projectsmain/getactivitiesbytypeandgroup', dataType:'json',
    data:{typeId: typeId, groupId: groupId, iowGroupId: iowGroupId},
    success: function(d){
      _activityItems = d.items || [];
      if(cb) cb();
    }
  });
}

/* ── Activity combobox ── */
(function(){
  var _inp, _btn, _list;
  function _init(){
    _inp  = document.getElementById('qe-activity-text');
    _btn  = document.getElementById('qe-act-drop-btn');
    _list = document.getElementById('qe-act-list');
    if(!_inp||!_btn||!_list) return;
    _inp.addEventListener('input', function(){ document.getElementById('qe-activity-id').value=''; _render(_inp.value); });
    _inp.addEventListener('keydown', _key);
    _inp.addEventListener('focus', function(){ _render(_inp.value); });
    _btn.addEventListener('mousedown', function(e){ e.preventDefault(); _list.classList.contains('open') ? _actListClose() : (_inp.focus(), _render('')); });
    document.addEventListener('mousedown', function(e){ if(!e.target.closest('.qe-act-wrap')) _actListClose(); });
  }
  function _render(q){
    _list.innerHTML='';
    var f=q.trim().toLowerCase();
    var matched=f ? _activityItems.filter(function(i){return i.name.toLowerCase().indexOf(f)!==-1;}) : _activityItems;
    matched.forEach(function(item){
      var li=document.createElement('li'); li.textContent=item.name;
      li.addEventListener('mousedown',function(e){e.preventDefault();_pick(item);});
      _list.appendChild(li);
    });
    if(f && !_activityItems.some(function(i){return i.name.toLowerCase()===f;})){
      var li=document.createElement('li'); li.className='new-act';
      li.textContent='+ New: "'+q.trim()+'"';
      li.addEventListener('mousedown',function(e){e.preventDefault();_inp.value=q.trim();document.getElementById('qe-activity-id').value='';_inp.classList.remove('qe-needs-data');_actListClose();});
      _list.appendChild(li);
    }
    _list.children.length ? _list.classList.add('open') : _list.classList.remove('open');
  }
  function _pick(item){ _inp.value=item.name; document.getElementById('qe-activity-id').value=item.id; _inp.classList.remove('qe-needs-data'); _actListClose(); _inp.dispatchEvent(new Event('change')); }
  function _key(e){
    if(!_list.classList.contains('open')) return;
    var items=_list.querySelectorAll('li'), cur=_list.querySelector('li.hl'), idx=cur?Array.prototype.indexOf.call(items,cur):-1;
    if(e.key==='ArrowDown'){e.preventDefault();_hl(items,idx+1);}
    else if(e.key==='ArrowUp'){e.preventDefault();_hl(items,idx-1);}
    else if(e.key==='Enter'&&cur){e.preventDefault();cur.dispatchEvent(new MouseEvent('mousedown'));}
    else if(e.key==='Escape'){_actListClose();}
  }
  function _hl(items,i){ items.forEach(function(l){l.classList.remove('hl');}); if(i>=0&&i<items.length){items[i].classList.add('hl');items[i].scrollIntoView({block:'nearest'});} }
  window._actListClose=function(){ if(_list) _list.classList.remove('open'); };
  /* init on first modal open */
  var _ready=false;
  $(function(){ _init(); _ready=true; });
  window._actComboInit=function(){ if(!_ready){_init();_ready=true;} };
})();

/* set activity text+id by id (used during prefill) */
function _setActivityById(actId, actName){
  var textEl = document.getElementById('qe-activity-text');
  var idEl   = document.getElementById('qe-activity-id');
  var found = _activityItems.filter(function(i){ return +i.id === +actId; });
  textEl.value = found.length ? found[0].name : (actName || '');
  idEl.value   = actId;
  if(textEl.value) textEl.classList.remove('qe-needs-data');
}

/* resolve typed text to an id (or 0 for new activity) */
function _resolveActivityId(){
  var text   = document.getElementById('qe-activity-text').value.trim();
  var idEl   = document.getElementById('qe-activity-id');
  var found  = _activityItems.filter(function(i){ return i.name.toLowerCase() === text.toLowerCase(); });
  if(found.length){
    idEl.value = found[0].id;
  } else {
    idEl.value = '';  /* new activity — backend will create */
  }
  return { id: idEl.value, name: text };
}

/* ── duration calculation ──
   Duration = ceil(SCH QTY / (Productivity/Day × Resource Units))
   For multiple tasks, the one that takes longest controls the duration. */
function recalcDuration(){
  var schQty = parseFloat(document.getElementById('qe-sch-qty').value) || 0;

  var maxDays = 0;
  document.querySelectorAll('#qe-task-body tr').forEach(function(tr){
    var prod     = parseFloat(tr.querySelector('.qe-task-prod')     ? tr.querySelector('.qe-task-prod').value     : 0) || 0;
    var resUnits = parseFloat(tr.querySelector('.qe-task-resunits') ? tr.querySelector('.qe-task-resunits').value : 0) || 0;
    if(prod > 0 && resUnits > 0 && schQty > 0){
      var days = schQty / (prod * resUnits);
      if(days > maxDays) maxDays = days;
    }
  });

  var duration = maxDays > 0 ? Math.ceil(maxDays) : 0;
  var durEl = document.getElementById('qe-dur-val'); if(durEl) durEl.textContent = duration > 0 ? duration : '—';
  return duration;
}

/* ── Resource Groups (keyed by typeId, '' = all) ── */
var _resGroupCache = {};
function loadResGroups(cb, typeId){
  var key = typeId ? String(typeId) : '';
  if(_resGroupCache[key]){ if(cb) cb(_resGroupCache[key]); return; }
  $.ajax({
    type:'POST', url:'../projectsmain/getresgrouplist', dataType:'json',
    data: typeId ? {type_id: typeId} : {},
    success: function(d){
      _resGroupCache[key] = d.items || [];
      if(cb) cb(_resGroupCache[key]);
    },
    error: function(){ if(cb) cb([]); }
  });
}
function makeResGroupOptions(groups, selectedId){
  var html = '<option value="">— Group —</option>';
  (groups||[]).forEach(function(g){ html += '<option value="'+g.id+'"'+(selectedId && +g.id===+selectedId?' selected':'')+'>'+g.name+'</option>'; });
  return html;
}

/* ── Resource Types ── */
var _resTypes = [];
var _resTypesLoaded = false;
function loadResTypes(cb){
  if(_resTypesLoaded){ if(cb) cb(); return; }
  $.ajax({
    type:'POST', url:'../projectsmain/getrestypelist', dataType:'json',
    success: function(d){
      _resTypes = d.items || [];
      _resTypesLoaded = true;
      if(cb) cb();
    },
    error: function(){
      _resTypesLoaded = true;
      if(cb) cb();
    }
  });
}

/* ── Task rows ── */
function addTaskRow(){
  var tbody = document.getElementById('qe-task-body');
  var isFirstRow = tbody.children.length === 0;
  var tr = document.createElement('tr');
  tr.innerHTML =
    '<td><input type="text" class="qe-task-name" placeholder="Task name"></td>'+
    '<td><input type="text" class="qe-task-unit" placeholder="Unit"></td>'+
    '<td><input type="number" class="qe-task-qty" placeholder="0.00" step="0.01" min="0"></td>'+
    '<td><input type="number" class="qe-task-prod" placeholder="0.00" step="0.01" min="0"></td>'+
    '<td><input type="number" class="qe-task-resunits" value="1" placeholder="1" step="1" min="1"></td>'+
    '<td style="text-align:right"><button class="qe-del-btn qe-task-del" title="Remove">&times;</button></td>';
  tbody.appendChild(tr);
  _bindAlphaField(tr.querySelector('.qe-task-unit'));
  _bindNumField(tr.querySelector('.qe-task-qty'));
  _bindNumField(tr.querySelector('.qe-task-prod'));
  _bindNumField(tr.querySelector('.qe-task-resunits'));
  tr.querySelector('.qe-task-prod').addEventListener('input', recalcDuration);
  tr.querySelector('.qe-task-resunits').addEventListener('input', recalcDuration);
  tr.querySelector('.qe-task-del').addEventListener('click', function(){
    if(document.querySelectorAll('#qe-task-body tr').length > 1){ tr.remove(); recalcDuration(); }
  });
  if(isFirstRow){
    _task1NameTouched = false; _task1UnitTouched = false;
    tr.querySelector('.qe-task-name').addEventListener('input', function(){ _task1NameTouched = true; });
    tr.querySelector('.qe-task-unit').addEventListener('input', function(){ _task1UnitTouched = true; });
    mirrorActivityToFirstTask();
  }
  tr._taskQtyTouched = false;
  tr.querySelector('.qe-task-qty').addEventListener('input', function(){ tr._taskQtyTouched = true; });
  tr.querySelector('.qe-task-qty').value = document.getElementById('qe-sch-qty').value;
}

/* ── Resource rows ── */
function makeResTypeOptions(selectedId){
  var html = '<option value="">— Type —</option>';
  _resTypes.forEach(function(t){ html += '<option value="'+t.id+'"'+(selectedId && +t.id===+selectedId?' selected':'')+'>'+t.name+'</option>'; });
  return html;
}

function _loadRowGroups(tr, typeId, selectedGroupId, cb){
  loadResGroups(function(groups){
    tr.querySelector('.qe-res-group').innerHTML = makeResGroupOptions(groups, selectedGroupId);
    if(cb) cb();
  }, typeId||null);
}

function _loadRowResources(tr, typeId, groupId, selectedResId, cb){
  var sel = tr.querySelector('.qe-res-name');
  sel.innerHTML = '<option value="">— loading… —</option>';
  if(!typeId && !groupId){ sel.innerHTML = '<option value="">— Resource Name —</option>'; if(cb) cb(); return; }
  $.ajax({
    type:'POST', url:'../projectsmain/getwbresources', dataType:'json',
    data:{ type_id: typeId||0, group_id: groupId||0 },
    success: function(d){
      var items = d.items || [];
      var html = '<option value="">— Select Resource —</option>';
      items.forEach(function(r){
        html += '<option value="'+r.id+'" data-unit="'+r.unit+'" data-price="'+r.price+'"'+(selectedResId && +r.id===+selectedResId?' selected':'')+'>'+r.name+'</option>';
      });
      if(!items.length) html = '<option value="">— No resources found —</option>';
      sel.innerHTML = html;
      /* auto-fill rate if a resource is already selected */
      var opt = sel.options[sel.selectedIndex];
      if(opt && opt.value){
        var rateEl = tr.querySelector('.qe-res-rate');
        if(!parseFloat(rateEl.value)) rateEl.value = opt.getAttribute('data-price')||'';
      }
      if(cb) cb();
    },
    error: function(){ sel.innerHTML = '<option value="">— Error loading —</option>'; if(cb) cb(); }
  });
}

function _bindResRow(tr){
  var typeEl  = tr.querySelector('.qe-res-type');
  var grpEl   = tr.querySelector('.qe-res-group');
  var nameSel = tr.querySelector('.qe-res-name');
  var unitEl  = tr.querySelector('.qe-res-unit');
  var qtyEl   = tr.querySelector('.qe-res-qty');
  var rateEl  = tr.querySelector('.qe-res-rate');
  var amtEl   = tr.querySelector('.qe-res-amt');

  _bindAlphaField(unitEl);
  _bindNumField(qtyEl);
  _bindNumField(rateEl);

  function calcAmt(){
    var q = parseFloat(qtyEl.value)||0, r = parseFloat(rateEl.value)||0;
    amtEl.value = (q*r).toFixed(2);
    recalcEstRate();
  }
  qtyEl.addEventListener('input', calcAmt);
  rateEl.addEventListener('input', calcAmt);

  typeEl.addEventListener('change', function(){
    grpEl.innerHTML = '<option value="">— Group —</option>';
    nameSel.innerHTML = '<option value="">— Select Resource —</option>';
    _loadRowGroups(tr, typeEl.value, null, function(){
      _loadRowResources(tr, typeEl.value, null, null);
    });
  });

  grpEl.addEventListener('change', function(){
    _loadRowResources(tr, typeEl.value, grpEl.value, null);
  });

  nameSel.addEventListener('change', function(){
    var opt = this.options[this.selectedIndex];
    if(opt && opt.value){
      rateEl.value = opt.getAttribute('data-price') || '';
      calcAmt();
    }
  });

  tr.querySelector('.qe-res-del').addEventListener('click', function(){
    if(document.querySelectorAll('#qe-res-body tr').length > 1){ tr.remove(); recalcEstRate(); }
  });

  tr.querySelector('.qe-map-btn').addEventListener('click', function(){ openMapPopup(tr); });
}

/* ── Task Map Popup ── */
var _mapTargetTr = null;

function openMapPopup(resTr){
  _mapTargetTr = resTr;
  var nameSel = resTr.querySelector('.qe-res-name');
  var resName = (nameSel.selectedIndex > 0 ? nameSel.options[nameSel.selectedIndex].text : '') || 'Resource';
  document.getElementById('qe-map-res-name').textContent = resName;

  var list = document.getElementById('qe-map-list');
  var noTasks = document.getElementById('qe-map-no-tasks');
  list.innerHTML = '';
  var taskRows = document.querySelectorAll('#qe-task-body tr');

  if(!taskRows.length){
    noTasks.style.display = 'block'; list.style.display = 'none';
  } else {
    noTasks.style.display = 'none'; list.style.display = 'block';
    var prev = resTr._taskMap || [];
    taskRows.forEach(function(ttr, idx){
      var tname = (ttr.querySelector('.qe-task-name')||{}).value || '';
      tname = tname.trim() || ('Task '+(idx+1));
      var tunit = (ttr.querySelector('.qe-task-unit')||{}).value || '';
      var sel = prev.indexOf(idx) !== -1;
      var item = document.createElement('div');
      item.className = 'qe-map-item' + (sel ? ' selected' : '');
      item.innerHTML =
        '<input type="checkbox" value="'+idx+'"'+(sel?' checked':'')+'>'+
        '<div class="qe-map-item-idx">'+(idx+1)+'</div>'+
        '<div class="qe-map-item-name">'+tname+'</div>'+
        (tunit ? '<div class="qe-map-item-unit">'+tunit+'</div>' : '');
      var chk = item.querySelector('input');
      function toggle(){
        chk.checked = !chk.checked;
        item.classList.toggle('selected', chk.checked);
      }
      item.addEventListener('click', function(e){
        if(e.target !== chk) toggle(); else item.classList.toggle('selected', chk.checked);
      });
      list.appendChild(item);
    });
  }

  /* qe-modal's own z-index rises every time it's clicked/dragged (shared
     floater focus manager), so a static CSS z-index here eventually gets
     overtaken and the map popup renders behind the WBS form. Always sit
     above the current floater ceiling when opening. */
  var mapZ = (typeof window.popupSubZBase === 'function') ? window.popupSubZBase() : 100200;
  document.getElementById('qe-map-bk').style.zIndex = mapZ;
  document.getElementById('qe-map-popup').style.zIndex = mapZ + 1;

  document.getElementById('qe-map-bk').classList.add('open');
  document.getElementById('qe-map-popup').classList.add('open');
}

function closeMapPopup(){
  document.getElementById('qe-map-bk').classList.remove('open');
  document.getElementById('qe-map-popup').classList.remove('open');
  _mapTargetTr = null;
}

function addResRow(prefill){
  /* prefill = {type_id, group_id, resource_id, qty, rate, task_map} optional */
  var tbody = document.getElementById('qe-res-body');
  var tr = document.createElement('tr');
  tr._taskMap = (prefill && prefill.task_map) || [];
  tr.innerHTML =
    '<td><select class="qe-res-type">'+makeResTypeOptions(prefill&&prefill.type_id)+'</select></td>'+
    '<td><select class="qe-res-group"><option value="">— Group —</option></select></td>'+
    '<td><select class="qe-res-name"><option value="">— Resource Name —</option></select></td>'+
    '<td><input type="text" class="qe-res-unit" value="'+(prefill&&prefill.unit||'')+'" placeholder="e.g. m³" data-alpha="1"></td>'+
    '<td><input type="number" class="qe-res-qty" value="'+(prefill&&prefill.qty!=null?prefill.qty:1)+'" step="0.001"></td>'+
    '<td><input type="number" class="qe-res-rate" value="'+(prefill&&prefill.rate||'')+'" placeholder="0.00" step="0.01"></td>'+
    '<td><input type="number" class="qe-res-amt" placeholder="0.00" readonly></td>'+
    '<td style="text-align:right;white-space:nowrap;">'+
      '<button class="qe-map-btn" title="Map to Tasks">&#x1F517;</button> '+
      '<button class="qe-del-btn qe-res-del" title="Remove">&times;</button>'+
    '</td>';
  tbody.appendChild(tr);
  _bindResRow(tr);

  /* if prefilling, cascade type→group→resource then recalc */
  if(prefill && (prefill.type_id || prefill.group_id)){
    _loadRowGroups(tr, prefill.type_id, prefill.group_id, function(){
      _loadRowResources(tr, prefill.type_id, prefill.group_id, prefill.resource_id, function(){
        /* recalc amount after rate is populated */
        var q = parseFloat(tr.querySelector('.qe-res-qty').value)||0;
        var r = parseFloat(tr.querySelector('.qe-res-rate').value)||0;
        tr.querySelector('.qe-res-amt').value = (q*r).toFixed(2);
        recalcEstRate();
      });
    });
  }

  var body = document.getElementById('qe-body');
  if(body) setTimeout(function(){ body.scrollTop = body.scrollHeight; }, 50);
}

/* ── clear activity fields (keeps Project Type, Activity Type and IOW) ── */
function clearActivityFields(){
  _wbsWanId = 0;
  _resetModalActivityOnward();
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
      qty:      parseFloat(tr.querySelector('.qe-task-qty').value)      || 0,
      prod:     parseFloat(tr.querySelector('.qe-task-prod').value)     || 0,
      resunits: parseFloat(tr.querySelector('.qe-task-resunits').value) || 1
    });
  });
  var resources = [];
  document.querySelectorAll('#qe-res-body tr').forEach(function(tr){
    var nameSel = tr.querySelector('.qe-res-name');
    var resId   = nameSel ? nameSel.value : '';
    var name    = (nameSel && nameSel.selectedIndex > 0) ? nameSel.options[nameSel.selectedIndex].text.trim() : '';
    if(!name) return;
    resources.push({
      resource_id: resId,
      group_id:    tr.querySelector('.qe-res-group').value,
      type_id:     tr.querySelector('.qe-res-type').value,
      name:        name,
      unit:        tr.querySelector('.qe-res-unit') ? tr.querySelector('.qe-res-unit').value.trim() : '',
      qty:         parseFloat(tr.querySelector('.qe-res-qty').value)  || 0,
      rate:        parseFloat(tr.querySelector('.qe-res-rate').value) || 0,
      task_map:    tr._taskMap || []
    });
  });

  var iowEl          = document.getElementById('qe-iow');
  var iowGroupSel    = document.getElementById('qe-iow-group');
  var projTypeSel    = document.getElementById('qe-proj-type');
  var groupSel       = document.getElementById('qe-group');
  var act            = _resolveActivityId();
  var iowGroupId     = iowGroupSel ? iowGroupSel.value : '';
  var iowGroupName   = iowGroupSel && iowGroupSel.selectedIndex > 0
                       ? iowGroupSel.options[iowGroupSel.selectedIndex].text.trim() : '';

  return {
    proj_type_id:    projTypeSel.value,
    group_id:        groupSel.value,
    iow_group_id:    iowGroupId,
    iow_group_name:  iowGroupName,
    iow_name:        iowEl.value.trim(),
    iow_group:       iowEl.value.trim(),
    iow_act_id:      act.id,
    act_name:        act.name,
    wan_id:       _wbsWanId,
    wg_id:        document.getElementById('qe-wg-id').value || '',
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

/* ── Estimate rate / amount recalc (module-level so _bindResRow can call them) ── */
function calcActivityAmount(){
  var q = parseFloat(document.getElementById('qe-qty').value)  || 0;
  var r = parseFloat(document.getElementById('qe-rate').value) || 0;
  document.getElementById('qe-amount').value = (q * r).toFixed(2);
}

function recalcEstRate(){
  var total = 0;
  document.querySelectorAll('#qe-res-body .qe-res-amt').forEach(function(el){
    total += parseFloat(el.value) || 0;
  });
  document.getElementById('qe-rate').value = total.toFixed(2);
  calcActivityAmount();
}

/* ── Default Sch. Unit/Qty from Estimate Unit/Qty, and the first Task's
   Name/Unit from Activity/Estimate Unit — only while the target field
   hasn't been manually edited by the user, so it acts as a starting
   default that's always freely overridable. ── */
var _schUnitTouched = false, _schQtyTouched = false;
var _task1NameTouched = false, _task1UnitTouched = false;

function mirrorEstimateToSchedule(){
  var schUnitEl = document.getElementById('qe-sch-unit');
  var schQtyEl  = document.getElementById('qe-sch-qty');
  if(schUnitEl && !_schUnitTouched){
    schUnitEl.value = document.getElementById('qe-unit').value;
    schUnitEl.classList.toggle('qe-needs-data', !schUnitEl.value.trim());
  }
  if(schQtyEl && !_schQtyTouched){
    schQtyEl.value = document.getElementById('qe-qty').value;
    schQtyEl.classList.toggle('qe-needs-data', !schQtyEl.value.trim());
    recalcDuration();
  }
}

function mirrorActivityToFirstTask(){
  var firstRow = document.querySelector('#qe-task-body tr');
  if(!firstRow) return;
  var nameEl = firstRow.querySelector('.qe-task-name');
  var unitEl = firstRow.querySelector('.qe-task-unit');
  if(nameEl && !_task1NameTouched) nameEl.value = document.getElementById('qe-activity-text').value;
  if(unitEl && !_task1UnitTouched) unitEl.value = document.getElementById('qe-unit').value;
}

/* Sch. Qty → each task row's Qty, until that row's qty is manually edited.
   Mirrors the same touched-flag pattern as mirrorEstimateToSchedule/
   mirrorActivityToFirstTask above — a starting default, freely overridable. */
function mirrorScheduleToTasks(){
  var schQty = document.getElementById('qe-sch-qty').value;
  document.querySelectorAll('#qe-task-body tr').forEach(function(tr){
    var qtyEl = tr.querySelector('.qe-task-qty');
    if(qtyEl && !tr._taskQtyTouched) qtyEl.value = schQty;
  });
}

/* ── Input validation helpers ── */
/* Blocks digits in alpha-only fields; blocks letters in number fields */
function _bindAlphaField(el){
  el.addEventListener('keypress', function(e){
    if(/[0-9]/.test(e.key)) e.preventDefault();
  });
  el.addEventListener('input', function(){
    this.value = this.value.replace(/[0-9]/g, '');
  });
}
function _bindNumField(el){
  el.addEventListener('keypress', function(e){
    if(!/[0-9.\-]/.test(e.key) && !e.ctrlKey && !e.metaKey) e.preventDefault();
  });
}
/* Apply to static fields after DOM ready; dynamic rows call these on creation */
function bindStaticFieldValidation(){
  document.querySelectorAll('[data-alpha]').forEach(_bindAlphaField);
}

/* ── Bind ── */
$(function(){
  bindStaticFieldValidation();

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

  /* close WBS modal */
  document.getElementById('qe-close').addEventListener('click', closeModal);
  document.getElementById('qe-close-btn').addEventListener('click', closeModal);

  /* delete activity (from Gantt if scheduled, or the saved WBS item otherwise) */
  document.getElementById('qe-btn-delete').addEventListener('click', function(){
    var wgId = document.getElementById('qe-wg-id').value || '';
    if(!_wbsSaId && !wgId){ alert('No activity to delete.'); return; }
    if(!confirm('Delete this activity?')) return;
    var btn = this;
    btn.disabled = true;
    $.ajax({
      type:'POST', url:'../projectsmain/wbsdelete', dataType:'json',
      data:{ sa_id: _wbsSaId || '', wg_id: wgId },
      success: function(d){
        btn.disabled = false;
        if(d.error){
          alert(d.error);
        } else {
          closeModal();
          if(typeof window.reloadGantt === 'function') window.reloadGantt();
        }
      },
      error: function(){ btn.disabled = false; alert('Delete failed. Please try again.'); }
    });
  });

  /* drag + resize */
  (function(){
    var modal = document.getElementById('qe-modal');
    var hdr   = document.getElementById('qe-hdr');
    var MIN_W = 380, MIN_H = 280;
    var _action=null, _sx=0,_sy=0,_ox=0,_oy=0,_ow=0,_oh=0;

    function _anchorLeft(){
      var r = modal.getBoundingClientRect();
      modal.style.right = 'auto';
      modal.style.left  = r.left + 'px';
      modal.style.top   = r.top  + 'px';
      modal.style.width  = r.width  + 'px';
      modal.style.height = r.height + 'px';
      return r;
    }

    /* drag */
    bindDragTouch(hdr, 'mousedown', function(e){
      if(e.target.id==='qe-close') return;
      var r = _anchorLeft();
      _action='drag'; _sx=e.clientX; _sy=e.clientY;
      _ox=r.left; _oy=r.top;
      e.preventDefault();
    });

    /* resize handles */
    document.querySelectorAll('.qe-rs').forEach(function(el){
      bindDragTouch(el, 'mousedown', function(e){
        var r = _anchorLeft();
        _action = el.getAttribute('data-dir');
        _sx=e.clientX; _sy=e.clientY;
        _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height;
        e.preventDefault(); e.stopPropagation();
      });
    });

    bindDragTouch(document, 'mousemove', function(e){
      if(!_action) return;
      e.preventDefault();
      var dx=e.clientX-_sx, dy=e.clientY-_sy;
      if(_action==='drag'){
        var x=Math.max(0,Math.min(_ox+dx, window.innerWidth -modal.offsetWidth));
        var y=Math.max(0,Math.min(_oy+dy, window.innerHeight-modal.offsetHeight));
        modal.style.left=x+'px'; modal.style.top=y+'px';
      } else {
        var l=_ox,t=_oy,w=_ow,h=_oh;
        if(_action.indexOf('e')>-1){ w=Math.max(MIN_W,_ow+dx); }
        if(_action.indexOf('s')>-1){ h=Math.max(MIN_H,_oh+dy); }
        if(_action.indexOf('w')>-1){ var nw=Math.max(MIN_W,_ow-dx); l=_ox+(_ow-nw); w=nw; }
        if(_action.indexOf('n')>-1){ var nh=Math.max(MIN_H,_oh-dy); t=_oy+(_oh-nh); h=nh; }
        modal.style.left=l+'px'; modal.style.top=t+'px';
        modal.style.width=w+'px'; modal.style.height=h+'px';
      }
    }, { passive: false });
    bindDragTouch(document, 'mouseup', function(){ _action=null; });
  })();

  /* task map popup */
  document.getElementById('qe-map-bk').addEventListener('click', closeMapPopup);
  document.getElementById('qe-map-done').addEventListener('click', function(){
    if(!_mapTargetTr) return;
    var sel = [];
    document.querySelectorAll('#qe-map-list .qe-map-item input:checked').forEach(function(c){ sel.push(+c.value); });
    _mapTargetTr._taskMap = sel;
    /* update button style to show mapping exists */
    var btn = _mapTargetTr.querySelector('.qe-map-btn');
    btn.classList.toggle('has-map', sel.length > 0);
    btn.title = sel.length > 0 ? 'Mapped to '+sel.length+' task(s) — click to edit' : 'Map to Tasks';
    closeMapPopup();
  });
  document.getElementById('qe-map-select-all').addEventListener('click', function(){
    document.querySelectorAll('#qe-map-list .qe-map-item').forEach(function(item){
      item.querySelector('input').checked = true;
      item.classList.add('selected');
    });
  });
  document.getElementById('qe-map-clear').addEventListener('click', function(){
    document.querySelectorAll('#qe-map-list .qe-map-item').forEach(function(item){
      item.querySelector('input').checked = false;
      item.classList.remove('selected');
    });
  });

  /* cascade: Project Type → Groups + reload Activities */
  document.getElementById('qe-proj-type').addEventListener('change', function(){
    document.getElementById('qe-activity-text').value = '';
    document.getElementById('qe-activity-id').value   = '';
    loadGroups(this.value);
    loadActivities(this.value, document.getElementById('qe-group').value);
  });

  /* cascade: Group → Activities */
  document.getElementById('qe-group').addEventListener('change', function(){
    document.getElementById('qe-activity-text').value = '';
    document.getElementById('qe-activity-id').value   = '';
    loadActivities(document.getElementById('qe-proj-type').value, this.value);
  });

  /* cascade: IOW Group → Activities */
  document.getElementById('qe-iow-group').addEventListener('change', function(){
    document.getElementById('qe-activity-text').value = '';
    document.getElementById('qe-activity-id').value   = '';
    loadActivities(document.getElementById('qe-proj-type').value, document.getElementById('qe-group').value, this.value);
  });

  /* activity typeahead — prefill when user selects an existing activity */
  document.getElementById('qe-activity-text').addEventListener('change', function(){
    var text = this.value.trim();
    if(!text) return;
    var actId = document.getElementById('qe-activity-id').value;
    if(!actId) { _resolveActivityId(); actId = document.getElementById('qe-activity-id').value; }
    if(!actId) return; /* new activity — nothing to prefill yet */
    $.ajax({
      type:'POST', url:'../projectsmain/getactivityresources', dataType:'json',
      data:{ activity_id: actId },
      success: function(data){
        if(data.unit){ document.getElementById('qe-unit').value = data.unit; }
        if(data.sch_unit){
          document.getElementById('qe-sch-unit').value = data.sch_unit;
          document.getElementById('qe-sch-unit').classList.remove('qe-needs-data');
          _schUnitTouched = true;
        }
        /* Sch. Qty (and Estimate Qty, above) intentionally not prefilled — always re-entered fresh */

        var taskBody = document.getElementById('qe-task-body');
        taskBody.innerHTML = '';
        if(data.tasks && data.tasks.length){
          _task1NameTouched = true; _task1UnitTouched = true;
          data.tasks.forEach(function(task){
            var tr = document.createElement('tr');
            tr.innerHTML =
              '<td><input type="text" class="qe-task-name" value="'+(task.task_name||'').replace(/"/g,'&quot;')+'" placeholder="Task name"></td>'+
              '<td><input type="text" class="qe-task-unit" value="'+(task.task_unit||'').replace(/"/g,'&quot;')+'" placeholder="Unit"></td>'+
              '<td><input type="number" class="qe-task-qty" value="'+parseFloat(task.task_qty||0).toFixed(2)+'" placeholder="0.00" step="0.01" min="0"></td>'+
              '<td><input type="number" class="qe-task-prod" value="'+parseFloat(task.productivity||0).toFixed(2)+'" placeholder="0.00" step="0.01" min="0"></td>'+
              '<td><input type="number" class="qe-task-resunits" value="1" placeholder="1" step="1" min="1"></td>'+
              '<td style="text-align:right"><button class="qe-del-btn qe-task-del" title="Remove">&times;</button></td>';
            taskBody.appendChild(tr);
            _bindNumField(tr.querySelector('.qe-task-qty'));
            tr.querySelector('.qe-task-prod').addEventListener('input', recalcDuration);
            tr.querySelector('.qe-task-resunits').addEventListener('input', recalcDuration);
            tr.querySelector('.qe-task-del').addEventListener('click', function(){
              if(document.querySelectorAll('#qe-task-body tr').length > 1){ tr.remove(); recalcDuration(); }
            });
            /* Loaded with a real saved qty — not the sch_qty default, so mark
               touched to keep mirrorScheduleToTasks() from overwriting it. */
            tr._taskQtyTouched = true;
            tr.querySelector('.qe-task-qty').addEventListener('input', function(){ tr._taskQtyTouched = true; });
          });
        } else { addTaskRow(); }
        recalcDuration();

        document.getElementById('qe-res-body').innerHTML = '';
        if(data.items && data.items.length){
          loadResTypes(function(){
            data.items.forEach(function(res){
              addResRow({
                type_id:     res.type_id,
                group_id:    res.group_id,
                resource_id: res.est_resource_id,
                qty:         res.est_resource_quantity,
                rate:        res.est_resource_rate
              });
            });
          });
        } else { loadResTypes(function(){ addResRow(); }); }
      }
    });
  });

  /* remove red border when field is filled */
  ['qe-proj-type','qe-group','qe-iow','qe-activity-text','qe-qty','qe-sch-unit','qe-sch-qty'].forEach(function(id){
    var el = document.getElementById(id);
    if(!el) return;
    el.addEventListener('change', function(){ if(this.value.trim()) this.classList.remove('qe-needs-data'); else this.classList.add('qe-needs-data'); });
    el.addEventListener('input',  function(){ if(this.value.trim()) this.classList.remove('qe-needs-data'); else this.classList.add('qe-needs-data'); });
  });

  document.getElementById('qe-qty').addEventListener('input', calcActivityAmount);
  document.getElementById('qe-sch-qty').addEventListener('input', recalcDuration);
  document.getElementById('qe-sch-qty').addEventListener('input', mirrorScheduleToTasks);

  /* mirror Estimate → Schedule, and Activity/Estimate → first Task, until user edits the target */
  document.getElementById('qe-unit').addEventListener('input', function(){ mirrorEstimateToSchedule(); mirrorActivityToFirstTask(); });
  document.getElementById('qe-qty').addEventListener('input', mirrorEstimateToSchedule);
  document.getElementById('qe-activity-text').addEventListener('input', mirrorActivityToFirstTask);
  document.getElementById('qe-sch-unit').addEventListener('input', function(){ _schUnitTouched = true; });
  document.getElementById('qe-sch-qty').addEventListener('input', function(){ _schQtyTouched = true; });

  /* add task row */
  document.getElementById('qe-task-add').addEventListener('click', addTaskRow);

  /* add resource row */
  document.getElementById('qe-res-add').addEventListener('click', function(){
    loadResTypes(function(){ addResRow(); });
  });

  /* ── Save button ── */
  document.getElementById('qe-btn-save').addEventListener('click', function(){
    var payload = collectPayload();
    var actText = document.getElementById('qe-activity-text').value.trim();
    if(!actText){ alert('Please enter or select an Activity.'); return; }

    var btn = document.getElementById('qe-btn-save');
    btn.disabled = true; btn.textContent = 'Saving…';

    $.ajax({
      type:'POST', url:'../projectsmain/wbssave',
      data:{ payload: JSON.stringify(payload) }, dataType:'json',
      success: function(d){
        btn.disabled = false; btn.textContent = '💾 Save';
        if(d.error && d.error !== 'No'){ alert('Save error: ' + d.error); return; }
        _wbsWanId = d.wan_id || 0;
        if(d.iow_act_id) document.getElementById('qe-activity-id').value = d.iow_act_id;
        if(_wbsMode === 'edit'){
          if(typeof window.reloadGantt === 'function') window.reloadGantt();
          clearActivityFields();
          _wbsMode = 'new';
          document.getElementById('qe-btn-add').style.display = '';
        } else {
          var msg = document.getElementById('qe-save-msg');
          msg.style.color = '#27ae60'; msg.textContent = '✔ Saved';
          setTimeout(function(){ msg.textContent = ''; }, 2000);
        }
      },
      error: function(){
        btn.disabled = false; btn.textContent = '💾 Save';
        alert('Server error — please try again.');
      }
    });
  });

  /* ── Add to Gantt ── */
  document.getElementById('qe-btn-add').addEventListener('click', function(){
    var actText = document.getElementById('qe-activity-text').value.trim();
    if(!actText){ alert('Please enter or select an Activity.'); return; }

    if(recalcDuration() <= 0){
      var schQty = parseFloat(document.getElementById('qe-sch-qty').value) || 0;
      var missing = [];
      if(schQty <= 0) missing.push('Sch. Qty');
      var hasTaskInputs = false;
      document.querySelectorAll('#qe-task-body tr').forEach(function(tr){
        var prod     = parseFloat(tr.querySelector('.qe-task-prod')     ? tr.querySelector('.qe-task-prod').value     : 0) || 0;
        var resUnits = parseFloat(tr.querySelector('.qe-task-resunits') ? tr.querySelector('.qe-task-resunits').value : 0) || 0;
        if(prod > 0 && resUnits > 0) hasTaskInputs = true;
      });
      if(!hasTaskInputs) missing.push('Productivity / Day (and Resource Units) for at least one task');
      alert('Duration could not be calculated — missing: ' + missing.join(', ') + '.\n\nThe activity will be added with a placeholder 1-day duration; fill these in and re-save to get the correct duration.');
    }

    var btn = document.getElementById('qe-btn-add');
    btn.disabled = true; btn.textContent = 'Adding…';

    var payload = collectPayload();

    $.ajax({
      type:'POST', url:'../projectsmain/wbsadd',
      data:{ payload: JSON.stringify(payload) }, dataType:'json',
      success: function(d){
        btn.disabled = false; btn.textContent = '+ Add to Gantt';
        if(d.error && d.error !== 'No'){ alert(d.error); return; }
        if(typeof window.reloadGantt === 'function') window.reloadGantt();
        /* Keep the modal open with Project Type / Activity Type / IOW Group / IOW
           intact so the user can add another activity under the same WBS context;
           only Close (or the × button) fully resets and closes the window. */
        _wbsMode = 'new'; _wbsSaId = 0;
        clearActivityFields();
      },
      error: function(x){ btn.disabled = false; btn.textContent = '+ Add to Gantt'; alert('Failed: ' + x.status); }
    });
  });

});

})();
</script>

<!-- ══════════════════════════════════════════════════════════════════════
     GANTT CHART FLOATING WINDOW
════════════════════════════════════════════════════════════════════════ -->
<style>
/* Holiday modal must appear above the Gantt window */
#holidayPopup.modal { z-index: 110000 !important; }
#holidayPopup + .modal-backdrop,
.modal-backdrop { z-index: 109999 !important; }

#gantt-win {
  display:none; position:fixed; top:80px; left:20px;
  width:1040px; height:calc(100vh - 110px);
  min-width:420px; min-height:300px;
  z-index:100000; background:#fff; border-radius:6px;
  border:1px solid #d0d0d0;
  box-shadow:0 4px 16px rgba(0,0,0,.15);
  flex-direction:column; overflow:hidden;
}
#gantt-win.gw-open { display:flex; }
#gantt-win-hdr {
  background:#0a1f44; color:#fff; padding:18px 15px;
  display:flex; align-items:center; justify-content:space-between;
  cursor:move; user-select:none; flex-shrink:0;
  font-family:'Nunito',sans-serif;
  border-bottom:1px solid #e0e0e0;
}
#gantt-win-hdr-btns { display:flex; align-items:center; gap:8px; }
#gantt-win-hdr-btns button {
  background:none; border:none; color:#fff; font-size:20px;
  cursor:pointer; line-height:1; padding:0 4px;
}
#gantt-win-body { flex:1; min-height:0; overflow:auto; }
#gantt-win-loading { padding:50px; text-align:center; color:#666; font-size:14px; }
.gw-rs { position:absolute; z-index:10; background:transparent; }
.gw-rs-e  { right:0;  top:6px;    bottom:6px; width:6px;  cursor:e-resize; }
.gw-rs-w  { left:0;   top:6px;    bottom:6px; width:6px;  cursor:w-resize; }
.gw-rs-s  { bottom:0; left:6px;   right:6px;  height:6px; cursor:s-resize; }
.gw-rs-n  { top:0;    left:6px;   right:6px;  height:6px; cursor:n-resize; }
.gw-rs-se { right:0;  bottom:0; width:14px; height:14px; cursor:se-resize; }
.gw-rs-sw { left:0;   bottom:0; width:14px; height:14px; cursor:sw-resize; }
.gw-rs-ne { right:0;  top:0;    width:14px; height:14px; cursor:ne-resize; }
.gw-rs-nw { left:0;   top:0;    width:14px; height:14px; cursor:nw-resize; }
</style>

<div id="gantt-win">
  <div class="gw-rs gw-rs-n" data-dir="n"></div>
  <div class="gw-rs gw-rs-s" data-dir="s"></div>
  <div class="gw-rs gw-rs-e" data-dir="e"></div>
  <div class="gw-rs gw-rs-w" data-dir="w"></div>
  <div class="gw-rs gw-rs-ne" data-dir="ne"></div>
  <div class="gw-rs gw-rs-nw" data-dir="nw"></div>
  <div class="gw-rs gw-rs-se" data-dir="se"></div>
  <div class="gw-rs gw-rs-sw" data-dir="sw"></div>
  <div id="gantt-win-hdr">
    <div style="display:flex;align-items:baseline;gap:6px;flex-wrap:nowrap;">
      <span style="font-size:20px;font-weight:700;color:#fff;">Schedule</span>
      <span style="font-size:18px;font-weight:700;color:#fff;">Gantt Chart</span>
      <span style="font-size:16px;font-weight:500;color:#cbd5e6;" id="gantt-win-proj-name"><?php echo htmlspecialchars($ProjectName); ?></span>
    </div>
    <div id="gantt-win-hdr-btns">
      <a class="icon-pencil" id="btn-quick-entry" title="WBS" href="#" style="width:24px;height:24px;font-size:11px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:#3a7d3a;color:#fff;cursor:pointer;text-decoration:none;box-shadow:0 2px 6px rgba(0,0,0,.25);"> </a>
      <a class="icon-sitemap" id="btn-gantt-relations-inline" title="Activity Relationships" href="#" style="width:24px;height:24px;font-size:11px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:#5c3d8f;color:#fff;cursor:pointer;text-decoration:none;box-shadow:0 2px 6px rgba(0,0,0,.2);"></a>
      <a class="icon-calendar" id="btn-gantt-calendar-inline" title="Holidays" href="#" style="width:24px;height:24px;font-size:11px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:#B8860B;color:#fff;cursor:pointer;text-decoration:none;box-shadow:0 2px 6px rgba(0,0,0,.2);"></a>
      <button id="gantt-win-expand" title="Fullscreen">&#x26F6;</button>
      <button id="gantt-win-close" title="Close">&times;</button>
    </div>
  </div>
  <div id="gantt-win-body">
    <div id="gantt-win-loading">Loading Gantt chart&hellip;</div>
  </div>
</div>

<script>
(function(){
  var win = document.getElementById('gantt-win');
  var hdr = document.getElementById('gantt-win-hdr');
  var body = document.getElementById('gantt-win-body');
  var MIN_W=500, MIN_H=300, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0, _saved=null;
  var _loaded = false;

  function _anchor(){
    var r=win.getBoundingClientRect();
    win.style.right='auto'; win.style.left=r.left+'px';
    win.style.top=r.top+'px'; win.style.width=r.width+'px';
    win.style.height=r.height+'px'; return r;
  }

  var _ganttPid = (document.getElementById('gantt-win-open') || {}).getAttribute ? document.getElementById('gantt-win-open').getAttribute('data-projectid') : '';
  var _ganttUrl = '<?php echo Yii::$app->urlManager->createUrl("projectsmain/newganttchart")?>' + '?layout=false';

  function _loadGanttWin(pid) {
    var url = _ganttUrl + '&id=' + (pid || _ganttPid);
    $('#gantt-win-body').html('<div id="gantt-win-loading">Loading Gantt chart&hellip;</div>');
    $.ajax({ url: url, success: function(html){
      $('#gantt-win-body').html(html);
    }, error: function(){
      $('#gantt-win-body').html('<div style="padding:30px;color:red;">Failed to load Gantt.</div>');
    }});
  }

  // Expose for external callers (WBS modal Add to Gantt / Delete)
  window.reloadGantt = function() {
    if(win.classList.contains('gw-open')){
      // Floating gantt window is open — reload its content
      _loaded = true;
      _loadGanttWin();
    } else if(typeof window.loadGantt === 'function'){
      // Full-page gantt view
      window.loadGantt();
    }
  };

  // Open
  var openBtn = document.getElementById('gantt-win-open');
  if(openBtn){
    openBtn.addEventListener('click', function(e){
      e.preventDefault();
      var pid = this.getAttribute('data-projectid');
      if(!pid){
        win.classList.add('gw-open');
        document.dispatchEvent(new Event('gantt:open'));
        document.getElementById('gantt-win-body').innerHTML = '<div style="padding:50px;text-align:center;color:#888;font-size:14px;">Please select a project first.</div>';
        return;
      }
      win.classList.add('gw-open');
      document.dispatchEvent(new Event('gantt:open'));
      if(!_loaded){
        _loaded = true;
        _loadGanttWin(pid);
      }
    });
  }

  // Close
  document.getElementById('gantt-win-close').addEventListener('click', function(){
    win.classList.remove('gw-open');
  });

  // WBS Entry shortcut inside Gantt header — bound here (not inside the
  // AJAX-loaded Gantt content) so it works immediately, even before/while
  // the Gantt chart itself is still loading.
  document.getElementById('btn-quick-entry').addEventListener('click', function(e){
    e.preventDefault();
    if(typeof window.openQeModal === 'function') window.openQeModal();
  });

  // Relationships shortcut inside Gantt header
  document.getElementById('btn-gantt-relations-inline').addEventListener('click', function(e){
    e.preventDefault();
    document.getElementById('btn-gantt-relations').click();
  });

  // Calendar shortcut inside Gantt header — delegates to the original link so getHolidayCalendar fires
  document.getElementById('btn-gantt-calendar-inline').addEventListener('click', function(e){
    e.preventDefault();
    document.getElementById('holidayPopupLink').click();
  });

  // Expand / restore
  document.getElementById('gantt-win-expand').addEventListener('click', function(){
    if(_saved){
      win.style.left=_saved.left; win.style.top=_saved.top;
      win.style.width=_saved.width; win.style.height=_saved.height;
      _saved=null; this.innerHTML='&#x26F6;'; this.title='Fullscreen';
    } else {
      var r=win.getBoundingClientRect();
      _saved={left:win.style.left,top:win.style.top,width:win.style.width,height:win.style.height};
      win.style.left='0'; win.style.top='0';
      win.style.width='100vw'; win.style.height='100vh';
      this.innerHTML='&#x2716;'; this.title='Restore';
    }
  });

  // Drag
  bindDragTouch(hdr, 'mousedown', function(e){
    if(e.target.closest('#gantt-win-hdr-btns')) return;
    var r=_anchor(); _action='drag';
    _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault();
  });

  // Resize
  document.querySelectorAll('.gw-rs').forEach(function(el){
    bindDragTouch(el, 'mousedown', function(e){
      var r=_anchor(); _action=el.getAttribute('data-dir');
      _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height;
      e.preventDefault(); e.stopPropagation();
    });
  });

  bindDragTouch(document, 'mousemove', function(e){
    if(!_action) return;
    e.preventDefault();
    var dx=e.clientX-_sx, dy=e.clientY-_sy;
    if(_action==='drag'){
      var x=Math.max(0,Math.min(_ox+dx,window.innerWidth-win.offsetWidth));
      var y=Math.max(0,Math.min(_oy+dy,window.innerHeight-win.offsetHeight));
      win.style.left=x+'px'; win.style.top=y+'px';
    } else {
      var l=_ox,t=_oy,w=_ow,h=_oh;
      if(_action.indexOf('e')>-1){w=Math.max(MIN_W,_ow+dx);}
      if(_action.indexOf('s')>-1){h=Math.max(MIN_H,_oh+dy);}
      if(_action.indexOf('w')>-1){var nw=Math.max(MIN_W,_ow-dx);l=_ox+(_ow-nw);w=nw;}
      if(_action.indexOf('n')>-1){var nh=Math.max(MIN_H,_oh-dy);t=_oy+(_oh-nh);h=nh;}
      win.style.left=l+'px'; win.style.top=t+'px';
      win.style.width=w+'px'; win.style.height=h+'px';
    }
  });
  bindDragTouch(document, 'mouseup', function(){ _action=null; });
})();
</script>

<!-- ══════════════════════════════════════════════════════════════════════
     PROCUREMENT FLOATING WINDOW
════════════════════════════════════════════════════════════════════════ -->
<style>
#procurement-win {
  display:none; position:fixed; top:80px; left:20px;
  width:1040px; height:calc(100vh - 110px);
  min-width:420px; min-height:300px;
  z-index:100000; background:#fff; border-radius:6px;
  border:1px solid #d0d0d0;
  box-shadow:0 4px 16px rgba(0,0,0,.15);
  flex-direction:column; overflow:hidden;
}
#procurement-win.pw-open { display:flex; }
#procurement-win-hdr {
  background:#1a2f57; color:#fff; padding:7px 15px;
  display:flex; align-items:center; justify-content:space-between;
  cursor:move; user-select:none; flex-shrink:0;
  font-family:'Nunito',sans-serif;
  border-bottom:1px solid #0f1d38;
}
#procurement-win-hdr-btns { display:flex; align-items:center; gap:8px; }
#procurement-win-hdr-btns button {
  background:none; border:none; color:#fff; font-size:16px;
  cursor:pointer; line-height:1; padding:0 4px; opacity:.85;
}
#procurement-win-hdr-btns button:hover { opacity:1; }
#procurement-win-body { flex:1; min-height:0; overflow:auto; }
#procurement-win-loading { padding:50px; text-align:center; color:#666; font-size:14px; }
.pw-rs { position:absolute; z-index:10; background:transparent; }
.pw-rs-e  { right:0;  top:6px;    bottom:6px; width:6px;  cursor:e-resize; }
.pw-rs-w  { left:0;   top:6px;    bottom:6px; width:6px;  cursor:w-resize; }
.pw-rs-s  { bottom:0; left:6px;   right:6px;  height:6px; cursor:s-resize; }
.pw-rs-n  { top:0;    left:6px;   right:6px;  height:6px; cursor:n-resize; }
.pw-rs-se { right:0;  bottom:0; width:14px; height:14px; cursor:se-resize; }
.pw-rs-sw { left:0;   bottom:0; width:14px; height:14px; cursor:sw-resize; }
.pw-rs-ne { right:0;  top:0;    width:14px; height:14px; cursor:ne-resize; }
.pw-rs-nw { left:0;   top:0;    width:14px; height:14px; cursor:nw-resize; }
</style>

<div id="procurement-win">
  <div class="pw-rs pw-rs-n" data-dir="n"></div>
  <div class="pw-rs pw-rs-s" data-dir="s"></div>
  <div class="pw-rs pw-rs-e" data-dir="e"></div>
  <div class="pw-rs pw-rs-w" data-dir="w"></div>
  <div class="pw-rs pw-rs-ne" data-dir="ne"></div>
  <div class="pw-rs pw-rs-nw" data-dir="nw"></div>
  <div class="pw-rs pw-rs-se" data-dir="se"></div>
  <div class="pw-rs pw-rs-sw" data-dir="sw"></div>
  <div id="procurement-win-hdr">
    <div style="display:flex;align-items:baseline;gap:6px;flex-wrap:nowrap;">
      <span style="font-size:14px;font-weight:600;color:#fff;letter-spacing:0.5px;">Order Management</span>
    </div>
    <div id="procurement-win-hdr-btns">
      <button id="procurement-win-expand" title="Fullscreen">&#x26F6;</button>
      <button id="procurement-win-close" title="Close">&times;</button>
    </div>
  </div>
  <div id="procurement-win-body">
    <div id="procurement-win-loading">Loading Procurement&hellip;</div>
  </div>
</div>

<script>
(function(){
  var win = document.getElementById('procurement-win');
  var hdr = document.getElementById('procurement-win-hdr');
  var MIN_W=500, MIN_H=300, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0, _saved=null;
  var _loaded = false;

  function _anchor(){
    var r=win.getBoundingClientRect();
    win.style.right='auto'; win.style.left=r.left+'px';
    win.style.top=r.top+'px'; win.style.width=r.width+'px';
    win.style.height=r.height+'px'; return r;
  }

  var _procurementUrl = '<?php echo Yii::$app->urlManager->createUrl("procurement/index")?>' + '?layout=false';

  function _loadProcurementWin() {
    $('#procurement-win-body').html('<div id="procurement-win-loading">Loading Procurement&hellip;</div>');
    $.ajax({ url: _procurementUrl, success: function(html){
      $('#procurement-win-body').html(html);
    }, error: function(){
      $('#procurement-win-body').html('<div style="padding:30px;color:red;">Failed to load Procurement.</div>');
    }});
  }

  window.reloadProcurement = function() {
    if(win.classList.contains('pw-open')){
      _loaded = true;
      _loadProcurementWin();
    }
  };

  // Open
  document.addEventListener('click', function(e){
    if(!e.target.closest('.procurementlib-btn')) return;
    e.preventDefault();
    win.classList.add('pw-open');
    if(typeof window.popupBringToFront === 'function') window.popupBringToFront('procurement-win');
    if(typeof window.popupCascade === 'function') window.popupCascade('procurement-win');
    if(!_loaded){
      _loaded = true;
      _loadProcurementWin();
    }
  });

  // Close
  document.getElementById('procurement-win-close').addEventListener('click', function(){
    win.classList.remove('pw-open');
  });

  // Expand / restore
  document.getElementById('procurement-win-expand').addEventListener('click', function(){
    if(_saved){
      win.style.left=_saved.left; win.style.top=_saved.top;
      win.style.width=_saved.width; win.style.height=_saved.height;
      _saved=null; this.innerHTML='&#x26F6;'; this.title='Fullscreen';
    } else {
      var r=win.getBoundingClientRect();
      _saved={left:win.style.left,top:win.style.top,width:win.style.width,height:win.style.height};
      win.style.left='0'; win.style.top='0';
      win.style.width='100vw'; win.style.height='100vh';
      this.innerHTML='&#x2716;'; this.title='Restore';
    }
  });

  // Drag
  bindDragTouch(hdr, 'mousedown', function(e){
    if(e.target.closest('#procurement-win-hdr-btns')) return;
    var r=_anchor(); _action='drag';
    _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault();
  });

  // Resize
  document.querySelectorAll('.pw-rs').forEach(function(el){
    bindDragTouch(el, 'mousedown', function(e){
      var r=_anchor(); _action=el.getAttribute('data-dir');
      _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height;
      e.preventDefault(); e.stopPropagation();
    });
  });

  bindDragTouch(document, 'mousemove', function(e){
    if(!_action) return;
    e.preventDefault();
    var dx=e.clientX-_sx, dy=e.clientY-_sy;
    if(_action==='drag'){
      var x=Math.max(0,Math.min(_ox+dx,window.innerWidth-win.offsetWidth));
      var y=Math.max(0,Math.min(_oy+dy,window.innerHeight-win.offsetHeight));
      win.style.left=x+'px'; win.style.top=y+'px';
    } else {
      var l=_ox,t=_oy,w=_ow,h=_oh;
      if(_action.indexOf('e')>-1){w=Math.max(MIN_W,_ow+dx);}
      if(_action.indexOf('s')>-1){h=Math.max(MIN_H,_oh+dy);}
      if(_action.indexOf('w')>-1){var nw=Math.max(MIN_W,_ow-dx);l=_ox+(_ow-nw);w=nw;}
      if(_action.indexOf('n')>-1){var nh=Math.max(MIN_H,_oh-dy);t=_oy+(_oh-nh);h=nh;}
      win.style.left=l+'px'; win.style.top=t+'px';
      win.style.width=w+'px'; win.style.height=h+'px';
    }
  });
  bindDragTouch(document, 'mouseup', function(){ _action=null; });
})();
</script>

<!-- ══════════════════════════════════════════════════════════════════════
     SITE OFFICE FLOATING WINDOW
════════════════════════════════════════════════════════════════════════ -->
<style>
#storeoffice-win {
  display:none; position:fixed; top:80px; left:20px;
  width:1040px; height:calc(100vh - 110px);
  min-width:420px; min-height:300px;
  z-index:100000; background:#fff; border-radius:6px;
  border:1px solid #d0d0d0;
  box-shadow:0 4px 16px rgba(0,0,0,.15);
  flex-direction:column; overflow:hidden;
}
#storeoffice-win.pw-open { display:flex; }
#storeoffice-win-hdr {
  background:#2c4a4a; color:#fff; padding:7px 15px;
  display:flex; align-items:center; justify-content:space-between;
  cursor:move; user-select:none; flex-shrink:0;
  font-family:'Nunito',sans-serif;
  border-bottom:1px solid #1c3232;
}
#storeoffice-win-hdr-btns { display:flex; align-items:center; gap:8px; }
#storeoffice-win-hdr-btns button {
  background:none; border:none; color:#fff; font-size:16px;
  cursor:pointer; line-height:1; padding:0 4px; opacity:.85;
}
#storeoffice-win-hdr-btns button:hover { opacity:1; }
#storeoffice-win-body { flex:1; min-height:0; overflow:auto; }
#storeoffice-win-loading { padding:50px; text-align:center; color:#666; font-size:14px; }
</style>

<div id="storeoffice-win">
  <div class="pw-rs pw-rs-n" data-dir="n"></div>
  <div class="pw-rs pw-rs-s" data-dir="s"></div>
  <div class="pw-rs pw-rs-e" data-dir="e"></div>
  <div class="pw-rs pw-rs-w" data-dir="w"></div>
  <div class="pw-rs pw-rs-ne" data-dir="ne"></div>
  <div class="pw-rs pw-rs-nw" data-dir="nw"></div>
  <div class="pw-rs pw-rs-se" data-dir="se"></div>
  <div class="pw-rs pw-rs-sw" data-dir="sw"></div>
  <div id="storeoffice-win-hdr">
    <div style="display:flex;align-items:baseline;gap:6px;flex-wrap:nowrap;">
      <span style="font-size:14px;font-weight:600;color:#fff;letter-spacing:0.5px;">Site Office</span>
    </div>
    <div id="storeoffice-win-hdr-btns">
      <button id="storeoffice-win-expand" title="Fullscreen">&#x26F6;</button>
      <button id="storeoffice-win-close" title="Close">&times;</button>
    </div>
  </div>
  <div id="storeoffice-win-body">
    <div id="storeoffice-win-loading">Loading Site Office&hellip;</div>
  </div>
</div>

<script>
(function(){
  var win = document.getElementById('storeoffice-win');
  var hdr = document.getElementById('storeoffice-win-hdr');
  var MIN_W=500, MIN_H=300, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0, _saved=null;
  var _loaded = false;

  function _anchor(){
    var r=win.getBoundingClientRect();
    win.style.right='auto'; win.style.left=r.left+'px';
    win.style.top=r.top+'px'; win.style.width=r.width+'px';
    win.style.height=r.height+'px'; return r;
  }

  var _storeofficeUrl = '<?php echo Yii::$app->urlManager->createUrl("storekeeper/index")?>' + '?layout=false';

  function _loadStoreofficeWin() {
    $('#storeoffice-win-body').html('<div id="storeoffice-win-loading">Loading Site Office&hellip;</div>');
    $.ajax({ url: _storeofficeUrl, success: function(html){
      $('#storeoffice-win-body').html(html);
    }, error: function(){
      $('#storeoffice-win-body').html('<div style="padding:30px;color:red;">Failed to load Site Office.</div>');
    }});
  }

  window.reloadStoreoffice = function() {
    if(win.classList.contains('pw-open')){
      _loaded = true;
      _loadStoreofficeWin();
    }
  };

  // Open
  document.addEventListener('click', function(e){
    if(!e.target.closest('.storeofficelib-btn')) return;
    e.preventDefault();
    win.classList.add('pw-open');
    if(typeof window.popupBringToFront === 'function') window.popupBringToFront('storeoffice-win');
    if(typeof window.popupCascade === 'function') window.popupCascade('storeoffice-win');
    if(!_loaded){
      _loaded = true;
      _loadStoreofficeWin();
    }
  });

  // Close
  document.getElementById('storeoffice-win-close').addEventListener('click', function(){
    win.classList.remove('pw-open');
  });

  // Expand / restore
  document.getElementById('storeoffice-win-expand').addEventListener('click', function(){
    if(_saved){
      win.style.left=_saved.left; win.style.top=_saved.top;
      win.style.width=_saved.width; win.style.height=_saved.height;
      _saved=null; this.innerHTML='&#x26F6;'; this.title='Fullscreen';
    } else {
      var r=win.getBoundingClientRect();
      _saved={left:win.style.left,top:win.style.top,width:win.style.width,height:win.style.height};
      win.style.left='0'; win.style.top='0';
      win.style.width='100vw'; win.style.height='100vh';
      this.innerHTML='&#x2716;'; this.title='Restore';
    }
  });

  // Drag
  bindDragTouch(hdr, 'mousedown', function(e){
    if(e.target.closest('#storeoffice-win-hdr-btns')) return;
    var r=_anchor(); _action='drag';
    _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault();
  });

  // Resize
  win.querySelectorAll('.pw-rs').forEach(function(el){
    bindDragTouch(el, 'mousedown', function(e){
      var r=_anchor(); _action=el.getAttribute('data-dir');
      _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height;
      e.preventDefault(); e.stopPropagation();
    });
  });

  bindDragTouch(document, 'mousemove', function(e){
    if(!_action) return;
    if(getComputedStyle(win).display === 'none') return;
    e.preventDefault();
    var dx=e.clientX-_sx, dy=e.clientY-_sy;
    if(_action==='drag'){
      var x=Math.max(0,Math.min(_ox+dx,window.innerWidth-win.offsetWidth));
      var y=Math.max(0,Math.min(_oy+dy,window.innerHeight-win.offsetHeight));
      win.style.left=x+'px'; win.style.top=y+'px';
    } else {
      var l=_ox,t=_oy,w=_ow,h=_oh;
      if(_action.indexOf('e')>-1){w=Math.max(MIN_W,_ow+dx);}
      if(_action.indexOf('s')>-1){h=Math.max(MIN_H,_oh+dy);}
      if(_action.indexOf('w')>-1){var nw=Math.max(MIN_W,_ow-dx);l=_ox+(_ow-nw);w=nw;}
      if(_action.indexOf('n')>-1){var nh=Math.max(MIN_H,_oh-dy);t=_oy+(_oh-nh);h=nh;}
      win.style.left=l+'px'; win.style.top=t+'px';
      win.style.width=w+'px'; win.style.height=h+'px';
    }
  });
  bindDragTouch(document, 'mouseup', function(){ _action=null; });
})();
</script>

<script>
/* Ensure all floating popups are direct children of <body> so they
   are never trapped inside a parent stacking context */
(function(){
  var ids = ['pdoc-popup','pdoc-viewer-popup','gpm-overlay','gantt-win','qe-modal','qe-map-bk','cb-win','cb-btn','reslib-win','rel-win','menu4-popup-cntnr','procurement-win','storeoffice-win'];
  ids.forEach(function(id){
    var el = document.getElementById(id) || document.querySelector('.' + id);
    if(el && el.parentNode !== document.body) document.body.appendChild(el);
  });
})();
</script>

<script>
/* ── Popup focus manager: clicking any floating window brings it to front,
   and each newly-opened window cascades diagonally off the last one that
   was opened, so opening several in a row visibly staggers them instead of
   stacking them exactly on top of each other. ── */
(function(){
  var _z = 100200; /* base — all floaters start here and increment up */
  var SUB_POPUP_GAP = 5000; /* sub-popups (Resource Library / Relationships
    "+add"-type popups) always sit at parent-floater-ceiling + this gap, so
    they can never end up under a repeatedly-clicked parent window. */
  var floaters = ['pdoc-popup','pdoc-viewer-popup','gantt-win','qe-modal','cb-win','reslib-win','rel-win','menu4-popup-cntnr','procurement-win','storeoffice-win'];

  window.popupBringToFront = function(id){
    var el = document.getElementById(id) || document.querySelector('.' + id);
    if(el) el.style.zIndex = ++_z;
    /* Re-raise any currently-open sub-popups registered against this
       floater, so this floater's own rise never overtakes its children. */
    var cb = _reraiseSubs[id];
    if(typeof cb === 'function') cb();
  };

  /* Sub-popup owners (Resource Library, Relationships) register a callback
     here that re-raises their own open sub-popups whenever this floater
     is brought to front, keeping them permanently above it. */
  var _reraiseSubs = {};
  window.registerSubPopupReraise = function(parentId, cb){ _reraiseSubs[parentId] = cb; };

  /* Current top-level floater z-index ceiling, for sub-popups to build on */
  window.popupZCeiling = function(){ return _z; };
  window.popupSubZBase = function(){ return _z + SUB_POPUP_GAP; };

  floaters.forEach(function(id){
    var el = document.getElementById(id) || document.querySelector('.' + id);
    if(!el) return;
    bindDragTouch(el, 'mousedown', function(){ window.popupBringToFront(id); }, true);
  });

  /* ── Cascade positioning ── */
  var CASCADE_STEP = 32, CASCADE_MAX = 6;
  var _cascadeCount = 0;
  var _lastOpenedId = null;

  /* Called by each popup right after it becomes visible, before the user
     drags it. Skips the offset if this is the only open floater, or if the
     popup has already been moved/resized by the user in this session
     (tracked via a data attribute the first time we touch it). */
  window.popupCascade = function(id){
    var el = document.getElementById(id) || document.querySelector('.' + id);
    if(!el) return;
    var anyOtherOpen = floaters.some(function(fid){
      if(fid === id) return false;
      var o = document.getElementById(fid) || document.querySelector('.' + fid);
      return o && o.offsetParent !== null;
    });
    if(!anyOtherOpen){ _cascadeCount = 0; _lastOpenedId = id; return; }
    if(_lastOpenedId === id) return; /* re-opening the same popup — leave it where the user left it */
    var r = el.getBoundingClientRect();
    if(r.width < 10 || r.height < 10) return; /* not laid out yet — bogus rect, skip rather than offset from (0,0) */
    _cascadeCount = (_cascadeCount % CASCADE_MAX) + 1;
    var dx = CASCADE_STEP * _cascadeCount, dy = CASCADE_STEP * _cascadeCount;
    var maxLeft = Math.max(0, window.innerWidth  - r.width  - 10);
    var maxTop  = Math.max(0, window.innerHeight - r.height - 10);
    el.style.left = Math.min(maxLeft, Math.max(0, r.left + dx)) + 'px';
    el.style.top  = Math.min(maxTop,  Math.max(0, r.top  + dy)) + 'px';
    el.style.right = 'auto'; /* some floaters position via right: override to left-based once moved */
    _lastOpenedId = id;
  };

  /* General-purpose cascade for sub-popups launched from inside a main
     popup (e.g. Resource Library's "+ Resource Type"/"+ Resource Group"
     buttons, Activity Relationships' "Add"/"List" buttons). `group` scopes
     the cascade counter so unrelated popup families don't interfere with
     each other; pass the element directly since sub-popups aren't in the
     top-level `floaters` list. */
  var _subCascade = {};
  window.cascadeSubWindow = function(el, group, siblingIds){
    if(!el) return;
    var st = _subCascade[group] || (_subCascade[group] = { count: 0, last: null });
    var anyOtherOpen = (siblingIds || []).some(function(sid){
      if(sid === el.id) return false;
      var o = document.getElementById(sid);
      return o && o.offsetParent !== null;
    });
    if(!anyOtherOpen){ st.count = 0; st.last = el.id; return; }
    if(st.last === el.id) return;
    var r = el.getBoundingClientRect();
    if(r.width < 10 || r.height < 10) return; /* not laid out yet — bogus rect, skip rather than offset from (0,0) */
    st.count = (st.count % 6) + 1;
    var dx = 32 * st.count, dy = 32 * st.count;
    var maxLeft = Math.max(0, window.innerWidth  - r.width  - 10);
    var maxTop  = Math.max(0, window.innerHeight - r.height - 10);
    el.style.left = Math.min(maxLeft, Math.max(0, r.left + dx)) + 'px';
    el.style.top  = Math.min(maxTop,  Math.max(0, r.top  + dy)) + 'px';
    el.style.right = 'auto';
    st.last = el.id;
  };

  /* Also bring to front (and cascade) on open */
  document.addEventListener('pdoc:open',   function(){ window.popupBringToFront('pdoc-popup'); window.popupCascade('pdoc-popup'); });
  document.addEventListener('pdocv:open',  function(){ window.popupBringToFront('pdoc-viewer-popup'); window.popupCascade('pdoc-viewer-popup'); });
  document.addEventListener('gantt:open',  function(){ window.popupBringToFront('gantt-win'); window.popupCascade('gantt-win'); });
  document.addEventListener('wbs:open',    function(){ window.popupBringToFront('qe-modal'); window.popupCascade('qe-modal'); });
  document.addEventListener('chat:open',   function(){ window.popupBringToFront('cb-win'); window.popupCascade('cb-win'); });
  document.addEventListener('reslib:open', function(){ window.popupBringToFront('reslib-win'); window.popupCascade('reslib-win'); });
  document.addEventListener('rel:open',    function(){ window.popupBringToFront('rel-win'); window.popupCascade('rel-win'); });
  document.addEventListener('actlib:open', function(){ window.popupBringToFront('menu4-popup-cntnr'); window.popupCascade('menu4-popup-cntnr'); });
})();
</script>

<?php $this->endPage() ?>
