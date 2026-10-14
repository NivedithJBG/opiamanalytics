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
use app\models\AccountsItem;

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
    <style>html,body{overflow-x:hidden;}</style>
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
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer2.css?v=20260729e" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer3.css" rel="stylesheet">

    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer4.css" rel="stylesheet">

    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/dashboard.css" rel="stylesheet">
     <!--<link href="<?php //echo Yii::$app->request->baseUrl; ?>/cssnew/style.css?v=2.11" rel="stylesheet"> 
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
     -->


    <link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jsgantt.css?v=20260827f" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jsgantt-extra.css?v=20260827f" />
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
        .round-icons .icon-shop.overNowVendorLib,
        .round-icons .icon-cart.procurementlib-btn,
        .round-icons .icon-office.storeofficelib-btn,
        .round-icons .icon-lock.userpermissionslib-btn,
        .round-icons .rel-nav-btn,
        .round-icons .estreport-btn,
        .round-icons .pdoc-btn,

        .round-icons .perf-dashboard-btn,
        .round-icons .cost-dashboard-btn,
        .round-icons #add-project-nav-btn,
        .round-icons .pwt-nav-btn { color: #fff !important; }

        .round-icons .icon-chart3            { background-color: #003580 !important; }
        .round-icons .rel-nav-btn            { background-color: #5c3d8f !important; }
        .round-icons .estreport-btn          { background-color: #37475a !important; }
        .round-icons .icon-calendar          { background-color: #B8860B !important; }
        .round-icons .icon-copy.duplicateProject { background-color: #E65C00 !important; }
        .round-icons .icon-tools.overNow4    { background-color: #CC0000 !important; }
        .round-icons .icon-wrench.reslib-btn { background-color: #555555 !important; }
        .round-icons .icon-shop.overNowVendorLib { background-color: #795548 !important; }
        .round-icons .icon-cart.procurementlib-btn { background-color: #00695C !important; }
        .round-icons .icon-office.storeofficelib-btn { background-color: #2c4a4a !important; }
        .round-icons .icon-lock.userpermissionslib-btn { background-color: #8e24aa !important; }
        .round-icons .perf-dashboard-btn     { background-color: #2e7d32 !important; height: 26px !important; width: 26px !important; font-size: 12px !important; }
        .round-icons .cost-dashboard-btn     { background-color: #7b1fa2 !important; height: 26px !important; width: 26px !important; font-size: 9px !important; padding: 0 !important; box-sizing: content-box !important; }
        /* Keep coloured backgrounds on hover/focus */
        .round-icons .icon-chart3:hover,            .round-icons .icon-chart3:focus            { background: #002060 !important; }
        .round-icons .rel-nav-btn:hover,            .round-icons .rel-nav-btn:focus            { background: #4a2f75 !important; }
        .round-icons .estreport-btn:hover,          .round-icons .estreport-btn:focus          { background: #26313f !important; }
        .round-icons .icon-calendar:hover,          .round-icons .icon-calendar:focus          { background: #8a6200 !important; }
        .round-icons .icon-copy.duplicateProject:hover, .round-icons .icon-copy.duplicateProject:focus { background: #b84400 !important; }
        .round-icons .icon-tools.overNow4:hover,    .round-icons .icon-tools.overNow4:focus    { background: #990000 !important; }
        .round-icons .icon-wrench.reslib-btn:hover, .round-icons .icon-wrench.reslib-btn:focus { background: #333333 !important; }
        .round-icons .icon-shop.overNowVendorLib:hover, .round-icons .icon-shop.overNowVendorLib:focus { background: #5d4037 !important; }
        .round-icons .icon-cart.procurementlib-btn:hover, .round-icons .icon-cart.procurementlib-btn:focus { background: #004D40 !important; }
        .round-icons .icon-office.storeofficelib-btn:hover, .round-icons .icon-office.storeofficelib-btn:focus { background: #1c3232 !important; }
        .round-icons .icon-lock.userpermissionslib-btn:hover, .round-icons .icon-lock.userpermissionslib-btn:focus { background: #6a1b7a !important; }
        .round-icons .perf-dashboard-btn:hover,     .round-icons .perf-dashboard-btn:focus     { background: #1b5e20 !important; }
        .round-icons .cost-dashboard-btn:hover,     .round-icons .cost-dashboard-btn:focus     { background: #4a148c !important; }
        .round-icons .pdoc-btn                      { background-color: #1565C0 !important; }
        .round-icons .pdoc-btn:hover,               .round-icons .pdoc-btn:focus               { background: #0d47a1 !important; }
        .round-icons #add-project-nav-btn           { background-color: #0277BD !important; }
        .round-icons #add-project-nav-btn:hover,    .round-icons #add-project-nav-btn:focus    { background: #01579B !important; }
        .round-icons .pwt-nav-btn                   { background: linear-gradient(135deg,#0d3b4f,#0f5c52) !important; }
        .round-icons .pwt-nav-btn:hover,            .round-icons .pwt-nav-btn:focus            { background: linear-gradient(135deg,#0a2e3d,#0c463f) !important; }

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

        /* ── Project Workflow Tools Floating Popup ── */
        #pwt-popup {
            display:none; flex-direction:column;
            position:fixed;
            width:640px; height:420px; min-width:420px; min-height:280px;
            z-index:100000;
            background:#fff; border-radius:10px; overflow:hidden;
            box-shadow:0 8px 40px rgba(0,0,0,0.32), 0 2px 8px rgba(0,0,0,0.12);
        }
        #pwt-popup.pwt-open { display:flex; }
        #pwt-header {
            background:linear-gradient(135deg,#0d3b4f,#0f5c52);
            color:#fff; padding:10px 16px; display:flex; align-items:center; gap:8px;
            cursor:move; user-select:none; flex-shrink:0;
        }
        #pwt-header h4 { margin:0; font-size:14px; font-weight:700; flex:1; }
        #pwt-close-x {
            background:rgba(255,255,255,0.2); border:none; color:#fff; border-radius:50%;
            width:24px; height:24px; font-size:15px; cursor:pointer; line-height:24px; text-align:center; flex-shrink:0;
        }
        #pwt-close-x:hover { background:rgba(255,255,255,0.35); }
        #pwt-tabs {
            display:flex; flex-wrap:wrap; gap:6px; background:#eef5f3;
            border-bottom:1px solid #d6e3e0; padding:10px 14px; flex-shrink:0;
        }
        #pwt-tabs .pwt-tab {
            background:linear-gradient(135deg,#0d3b4f,#0f5c52); color:#fff;
            border:none; border-radius:20px; padding:7px 16px; font-size:12px; font-weight:600;
            cursor:pointer; opacity:.6; transition:opacity .15s;
        }
        #pwt-tabs .pwt-tab:hover { opacity:.85; }
        #pwt-tabs .pwt-tab.active { opacity:1; box-shadow:0 2px 6px rgba(13,59,79,.4); }
        #pwt-body { flex:1; overflow-y:auto; padding:18px; min-height:0; }
        .pwt-pane { display:none; }
        .pwt-pane.active { display:block; }
        .pwt-placeholder { text-align:center; color:#888; padding:40px 20px; font-size:13px; }
        #pwt-popup .ui-resizable-se { background:#0f5c52; opacity:.4; border-radius:0 0 8px 0; }
        #pwt-popup .ui-resizable-se:hover { opacity:.7; }

        /* ── Create WBS Floating Popup ── */
        #cwbs-popup {
            display:none; flex-direction:column;
            position:fixed;
            width:680px; height:640px; min-width:460px; min-height:400px;
            z-index:100000;
            background:#fff; border-radius:10px; overflow:hidden;
            box-shadow:0 8px 40px rgba(0,0,0,0.32), 0 2px 8px rgba(0,0,0,0.12);
        }
        #cwbs-popup.cwbs-open { display:flex; }
        #cwbs-header {
            background:linear-gradient(135deg,#0d3b4f,#0f5c52);
            color:#fff; padding:10px 16px; display:flex; align-items:center; gap:8px;
            cursor:move; user-select:none; flex-shrink:0;
        }
        #cwbs-header h4 { margin:0; font-size:14px; font-weight:700; flex:1; }
        #cwbs-close-x {
            background:rgba(255,255,255,0.2); border:none; color:#fff; border-radius:50%;
            width:24px; height:24px; font-size:15px; cursor:pointer; line-height:24px; text-align:center; flex-shrink:0;
        }
        #cwbs-close-x:hover { background:rgba(255,255,255,0.35); }
        #cwbs-boq-toggle {
            display:flex; align-items:center; gap:8px; background:#fff;
            border:none; border-bottom:1px solid #d6e3e0; padding:10px 16px;
            font-size:12.5px; font-weight:700; color:#0d3b4f; cursor:pointer; width:100%; text-align:left; flex-shrink:0;
        }
        #cwbs-boq-toggle:hover { background:#f6faf9; }
        #cwbs-boq-toggle .cwbs-boq-caret { transition:transform .15s; font-size:10px; }
        #cwbs-boq-toggle.cwbs-boq-open .cwbs-boq-caret { transform:rotate(90deg); }
        #cwbs-boq-section {
            display:none; background:#fafcfc; border-bottom:1px solid #d6e3e0;
            padding:12px 16px; flex-shrink:0; max-height:200px; overflow-y:auto;
            gap:20px;
        }
        #cwbs-boq-section.cwbs-boq-open { display:flex; }
        #cwbs-boq-upload-col { flex:0 0 320px; display:flex; flex-direction:column; }
        #cwbs-boq-list-col { flex:1; min-width:0; }
        #cwbs-boq-dropzone {
            display:inline-flex; align-items:center; gap:10px; max-width:320px;
            border:2px dashed #8fb8ae; border-radius:8px; background:#fff;
            padding:10px 14px; text-align:left; cursor:pointer; transition:border-color .15s,background .15s;
        }
        #cwbs-boq-dropzone:hover, #cwbs-boq-dropzone.cwbs-boq-drag-over { border-color:#0f5c52; background:#eef5f3; }
        #cwbs-boq-dropzone .cwbs-boq-dz-icon { font-size:18px; color:#5c7a73; flex-shrink:0; }
        #cwbs-boq-dropzone .cwbs-boq-dz-text { font-size:12.5px; color:#2d333b; font-weight:600; }
        #cwbs-boq-dropzone .cwbs-boq-dz-sub { font-size:10.5px; color:#8aa39c; margin-top:1px; }
        #cwbs-boq-file-input { display:none; }
        #cwbs-boq-selected-file {
            display:none; align-items:center; gap:10px; margin-top:10px;
            background:#eef5f3; border:1px solid #c7ded8; border-radius:8px; padding:9px 12px;
        }
        #cwbs-boq-selected-file.show { display:flex; }
        #cwbs-boq-selected-file .cwbs-boq-sf-name { flex:1; min-width:0; font-size:12.5px; font-weight:600; color:#2d333b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        #cwbs-boq-selected-file .cwbs-boq-sf-remove { background:none; border:none; color:#c0392b; font-size:16px; cursor:pointer; padding:0 4px; }
        #cwbs-boq-upload-btn {
            margin-top:10px; padding:8px 20px; border:none; border-radius:20px; align-self:flex-start;
            background:linear-gradient(135deg,#0d3b4f,#0f5c52); color:#fff; font-size:12.5px; font-weight:700; cursor:pointer;
        }
        #cwbs-boq-upload-btn:hover { opacity:.88; }
        #cwbs-boq-upload-btn:disabled { opacity:.5; cursor:not-allowed; }
        #cwbs-boq-list-hdr { font-size:11px; font-weight:700; color:#5c7a73; text-transform:uppercase; letter-spacing:.5px; margin:0 0 8px; }
        #cwbs-boq-list { display:flex; flex-direction:column; gap:6px; max-height:170px; overflow-y:auto; }
        .cwbs-boq-file-row { display:flex; align-items:center; gap:10px; background:#fff; border:1px solid #d6e3e0; border-radius:8px; padding:8px 12px; }
        .cwbs-boq-file-row .cwbs-boq-fr-icon { font-size:15px; color:#0d3b4f; flex-shrink:0; }
        .cwbs-boq-file-row .cwbs-boq-fr-info { flex:1; min-width:0; }
        .cwbs-boq-file-row .cwbs-boq-fr-name { font-size:12.5px; font-weight:600; color:#2d333b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .cwbs-boq-file-row .cwbs-boq-fr-date { font-size:10.5px; color:#8aa39c; margin-top:2px; }
        .cwbs-boq-file-row .cwbs-boq-fr-view {
            background:#0f5c52; color:#fff; border:none; border-radius:14px; padding:4px 12px;
            font-size:11px; font-weight:700; cursor:pointer; flex-shrink:0;
        }
        .cwbs-boq-file-row .cwbs-boq-fr-view:hover { background:#0a3f38; }
        .cwbs-boq-file-row .cwbs-boq-fr-del { background:#e57373; color:#fff; border:none; border-radius:50%; width:22px; height:22px; font-size:11px; cursor:pointer; flex-shrink:0; }
        #cwbs-boq-empty { color:#aaa; font-size:12.5px; text-align:center; padding:10px 0; }
        #cwbs-main { flex:1; display:flex; min-height:0; }
        #cwbs-right { flex:1; min-width:0; display:flex; flex-direction:column; }
        #cwbs-filters {
            display:flex; gap:12px; background:#eef5f3; border-bottom:1px solid #d6e3e0;
            padding:12px 16px; flex-shrink:0; flex-wrap:wrap;
        }
        .cwbs-f { display:flex; flex-direction:column; gap:3px; flex:1; min-width:160px; }
        .cwbs-f label { font-size:11px; font-weight:700; color:#0d3b4f; }
        .cwbs-f select {
            border:1px solid #bcd0cc; border-radius:5px; padding:6px 8px; font-size:12px;
            height:32px; background:#fff; outline:none;
        }
        .cwbs-f select:focus { border-color:#0f5c52; }
        #cwbs-msg { font-size:12px; color:#c0392b; padding:0 16px; flex-shrink:0; min-height:0; }
        #cwbs-msg:empty { display:none; }
        #cwbs-list-wrap { flex:1; overflow-y:auto; padding:10px 16px; min-height:0; }
        .cwbs-placeholder { text-align:center; color:#888; padding:40px 20px; font-size:13px; }
        #cwbs-table { width:100%; border-collapse:collapse; font-size:13px; border:1px solid #8fb8ae; }
        #cwbs-table thead th {
            background:#cfe8e0; color:#0d3b4f; font-weight:700; font-size:11px;
            text-align:left; padding:8px 10px; border:1px solid #8fb8ae;
            position:sticky; top:0; z-index:1;
        }
        #cwbs-table tbody tr:nth-child(even) { background:#eaf5f1; }
        #cwbs-table tbody tr:hover { background:#d7ede6; }
        #cwbs-table td { padding:8px 10px; vertical-align:middle; border:1px solid #bcdcd2; }
        .cwbs-sno-td { width:40px; text-align:center; color:#6c7a89; }
        .cwbs-act-name { color:#1a1a1a; }
        .cwbs-unit-td { width:70px; text-align:center; color:#6c7a89; }
        .cwbs-qty-td { width:90px; text-align:center; }
        .cwbs-runits-td { width:90px; text-align:center; }
        .cwbs-qty-input, .cwbs-runits-input {
            width:100%; box-sizing:border-box; border:1px solid #bcd0cc; border-radius:5px;
            padding:5px 6px; font-size:12px; text-align:center; outline:none;
        }
        .cwbs-qty-input:focus, .cwbs-runits-input:focus { border-color:#0f5c52; }
        .cwbs-edit-td { width:40px; text-align:center; }
        .cwbs-tick-td { width:36px; text-align:center; }
        .cwbs-edit-btn {
            background:#fff; border:2px solid #b7c4c1; color:#5c7a73;
            border-radius:50%; width:26px; height:26px; cursor:pointer; flex-shrink:0;
            font-size:12px; line-height:1; transition:background .12s, border-color .12s, color .12s;
        }
        .cwbs-edit-btn:hover { border-color:#0f5c52; color:#0f5c52; }
        .cwbs-tick-btn {
            background:#fff; border:2px solid #b7c4c1; color:transparent;
            border-radius:50%; width:26px; height:26px; cursor:pointer; flex-shrink:0;
            font-size:14px; line-height:1; transition:background .12s, border-color .12s, color .12s;
        }
        .cwbs-tick-btn:hover { border-color:#0f5c52; }
        .cwbs-tick-btn.cwbs-selected { background:#0f5c52; border-color:#0f5c52; color:#fff; }
        #cwbs-footer {
            display:flex; justify-content:flex-end; gap:10px; padding:12px 16px;
            border-top:1px solid #eee; flex-shrink:0;
        }
        .cwbs-btn { border:none; border-radius:20px; padding:8px 20px; font-size:13px; font-weight:700; cursor:pointer; }
        .cwbs-btn-cancel { background:#eee; color:#444; }
        .cwbs-btn-cancel:hover { background:#e0e0e0; }
        .cwbs-btn-add { background:linear-gradient(135deg,#0d3b4f,#0f5c52); color:#fff; }
        .cwbs-btn-add:hover { opacity:.88; }
        .cwbs-btn-add:disabled { opacity:.5; cursor:default; }
        #cwbs-popup .ui-resizable-se { background:#0f5c52; opacity:.4; border-radius:0 0 8px 0; }
        #cwbs-popup .ui-resizable-se:hover { opacity:.7; }

        /* ── Create WBS: Edit Activity Floating Popup ── */
        #cwbsed-popup {
            display:none; flex-direction:column;
            position:fixed;
            width:700px; height:520px; min-width:460px; min-height:360px;
            z-index:100000;
            background:#fff; border-radius:10px; overflow:hidden;
            box-shadow:0 8px 40px rgba(0,0,0,0.32), 0 2px 8px rgba(0,0,0,0.12);
        }
        #cwbsed-popup.cwbsed-open { display:flex; }
        #cwbsed-header {
            background:linear-gradient(135deg,#0d3b4f,#0f5c52);
            color:#fff; padding:10px 16px; display:flex; align-items:center; gap:8px;
            cursor:move; user-select:none; flex-shrink:0;
        }
        #cwbsed-header h4 { margin:0; font-size:14px; font-weight:700; flex:1; }
        #cwbsed-close-x {
            background:rgba(255,255,255,0.2); border:none; color:#fff; border-radius:50%;
            width:24px; height:24px; font-size:15px; cursor:pointer; line-height:24px; text-align:center; flex-shrink:0;
        }
        #cwbsed-close-x:hover { background:rgba(255,255,255,0.35); }
        #cwbsed-body { flex:1; overflow-y:auto; padding:16px; min-height:0; }
        .cwbsed-row { display:flex; gap:12px; margin-bottom:12px; flex-wrap:wrap; }
        .cwbsed-f { display:flex; flex-direction:column; gap:3px; min-width:140px; }
        .cwbsed-f label { font-size:11px; font-weight:700; color:#0d3b4f; }
        .cwbsed-f select, .cwbsed-f input {
            border:1px solid #bcd0cc; border-radius:5px; padding:6px 8px; font-size:12px;
            height:32px; background:#fff; outline:none; width:100%; box-sizing:border-box;
        }
        .cwbsed-f select:focus, .cwbsed-f input:focus { border-color:#0f5c52; }
        .cwbsed-tasks-hdr { border-top:1px solid #eee; padding-top:10px; margin-top:4px; }
        .cwbsed-task-col-hdr { display:flex; gap:6px; margin-top:8px; font-size:11px; color:#777; }
        .cwbsed-task-col-hdr span { min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .cwbsed-task-row { display:flex; gap:6px; margin-top:6px; align-items:center; }
        .cwbsed-task-row input {
            border:1px solid #bcd0cc; border-radius:5px; padding:5px 6px; font-size:11.5px;
            height:30px; outline:none; box-sizing:border-box; min-width:0; width:100%;
        }
        .cwbsed-task-row input:focus { border-color:#0f5c52; }
        .cwbsed-task-row .cwbsed-task-name { flex:2.2; min-width:0; }
        .cwbsed-task-row .cwbsed-task-unit { flex:1; min-width:0; }
        .cwbsed-task-row .cwbsed-task-qty { flex:1; min-width:0; }
        .cwbsed-task-row .cwbsed-task-prod { flex:1.2; min-width:0; }
        .cwbsed-task-remove {
            width:28px; height:28px; flex-shrink:0; background:none; border:none;
            color:#d9534f; font-size:18px; cursor:pointer; line-height:1;
        }
        #cwbsed-footer {
            display:flex; justify-content:flex-end; gap:10px; padding:12px 16px;
            border-top:1px solid #eee; flex-shrink:0;
        }
        #cwbsed-popup .ui-resizable-se { background:#0f5c52; opacity:.4; border-radius:0 0 8px 0; }
        #cwbsed-popup .ui-resizable-se:hover { opacity:.7; }

        /* ── File Viewer Floating Popup ── */
        #pdoc-viewer-popup {
            display:none; flex-direction:column;
            position:fixed;
            width:680px; height:760px; min-width:320px; min-height:280px;
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
                    <a class="icon-rocket pwt-nav-btn" id="pwt-nav-btn" title="Project Workflow Tools" href="#"></a>
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
                <li>
                    <a class="icon-flow-parallel" id="gantt-win-dep-open" title="Dependency View (experimental)" href="#" data-projectid="<?php echo $ProjectId?>"></a>
                </li>
                <li>
                    <a class="icon-file-text estreport-btn" id="btn-estimate-report" title="Project Estimate" href="#" data-projectid="<?php echo $ProjectId?>"></a>
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
                    <li><a class="icon-shop overNowVendorLib" title="Vendor Library" href="#"> </a></li>
                                <li><a class="icon-cart procurementlib-btn" title="Procurement" href="#"> </a></li>
                                <li><a class="icon-office storeofficelib-btn" title="Site Office" href="#"> </a></li>
                                <?php if($user->role_id == 0){ ?>
                                <li><a class="icon-lock userpermissionslib-btn" title="Manage Permissions" href="#"> </a></li>
                                <?php } ?>
                    <li><a class="icon-stats perf-dashboard-btn" title="KPI" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-stats cost-dashboard-btn" title="Cost Dashboard" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-moneybag cashflow-dashboard-btn" title="Cashflow" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-plus" id="iow-btn-test" title="Add IOWs, Activities &amp; BOQ" href="#" style="cursor:pointer;"> </a></li>
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
                    <li><a class="icon-shop overNowVendorLib" title="Vendor Library" href="#"> </a></li>
                                <li><a class="icon-cart procurementlib-btn" title="Procurement" href="#"> </a></li>
                                <li><a class="icon-office storeofficelib-btn" title="Site Office" href="#"> </a></li>
                                <?php if($user->role_id == 0){ ?>
                                <li><a class="icon-lock userpermissionslib-btn" title="Manage Permissions" href="#"> </a></li>
                                <?php } ?>
                    <li><a class="icon-stats perf-dashboard-btn" title="KPI" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-stats cost-dashboard-btn" title="Cost Dashboard" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-moneybag cashflow-dashboard-btn" title="Cashflow" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-plus" id="iow-btn-test" title="Add IOWs, Activities &amp; BOQ" href="#" style="cursor:pointer;"> </a></li>
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
                    <li><a class="icon-shop overNowVendorLib" title="Vendor Library" href="#"> </a></li>
                                <li><a class="icon-cart procurementlib-btn" title="Procurement" href="#"> </a></li>
                                <li><a class="icon-office storeofficelib-btn" title="Site Office" href="#"> </a></li>
                                <?php if($user->role_id == 0){ ?>
                                <li><a class="icon-lock userpermissionslib-btn" title="Manage Permissions" href="#"> </a></li>
                                <?php } ?>
                    <li><a class="icon-stats perf-dashboard-btn" title="KPI" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-stats cost-dashboard-btn" title="Cost Dashboard" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-moneybag cashflow-dashboard-btn" title="Cashflow" href="#" style="cursor:pointer;"> </a></li>
                    <li><a class="icon-plus" id="iow-btn-test" title="Add IOWs, Activities &amp; BOQ" href="#" style="cursor:pointer;"> </a></li>
                    <?php if(!$user->account_type || $user->account_type == 'normal'){ ?>
                    <!-- <li><a class="icon-flickr overNow1" title="Project Report" href="<?php //echo Yii::$app->urlManager->createUrl('projectsmain/reports')?>">
                        </a>
                    </li> -->
                    <?php } ?>


                <?php } elseif(Yii::$app->controller->id == 'procurement') { ?>
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
                    <li><a class="icon-moneybag cashflow-dashboard-btn" title="Cashflow" href="#" style="cursor:pointer;"> </a></li>
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

                        <li><a href="<?php echo Yii::$app->urlManager->createUrl('financerequests/index')?>">Finance</a></li>

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

        //Estimate Report Overrelay — always present when gantt-win is available
        echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/projectsmain/overrelay-estimatereport.php');

        //Vendor Library Overrelay start
        if(Yii::$app->controller->id == 'procurement' || (Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'index') || (Yii::$app->controller->id == 'projectsmain' && Yii::$app->controller->action->id == 'reports') || (Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'projectmasters')) {
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
        <!-- Opened via Bootstrap's own data-toggle="modal" (see the
             .taskreport button markup in ReportController), independent of
             any JS z-index handling — Bootstrap's default .modal/.modal-backdrop
             z-index (1050/1040) sits far below this app's floating-window
             family (100000+, e.g. #storeoffice-win and its dynamically
             created sk-win-* sub-windows). When this "Report Progress"
             button is clicked from inside one of those floating windows,
             the modal opened underneath it, appearing stuck/unreachable.
             Pinned above the entire floater range (via JS on show.bs.modal,
             since Bootstrap's backdrop <div> has no id to target from CSS
             alone) so it always wins regardless of which window it was
             triggered from. -->
        <!-- storekeeper/index.php's own "Report Progress" sub-window
             (#sk-win-* built by _skBuildWin) uses a hardcoded z-index of
             999999+ for itself — higher than the 200500 first tried here —
             so this modal needs to clear THAT too, not just the 100000-range
             floater family. -->
        <style>#taskReportPopup.modal{ z-index:1000500; }</style>
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
        <script>
        $(document).on('show.bs.modal', '#taskReportPopup', function(){
          // Bootstrap's backdrop <div> is appended to <body> with no id to
          // target from CSS — bump the one just created for THIS modal
          // above the floating-window z-index range (see the style block
          // above #taskReportPopup for why).
          setTimeout(function(){
            $('.modal-backdrop').last().css('z-index', 1000450);
          }, 0);
        });
        </script>
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
#cb-win{position:fixed;bottom:80px;right:24px;z-index:100000;width:460px;height:640px;max-width:calc(100vw - 48px);max-height:calc(100vh - 104px);background:#fff;border-radius:14px;box-shadow:0 8px 40px rgba(0,0,0,.28);display:none;flex-direction:column;font-family:'Times New Roman',Times,serif;overflow:hidden}

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
    var cbPendingDocuments = null;
    var win     = document.getElementById('cb-win');
    var msgs    = document.getElementById('cb-msgs');
    var inp     = document.getElementById('cb-input');
    var sendBtn = document.getElementById('cb-send');
    var chatUrl = '<?php echo Yii::$app->urlManager->createAbsoluteUrl(["/chatbot/chat"]); ?>';

    /* Allows other popups (e.g. Project Documents) to attach PDF/image
       content to the NEXT outgoing message only. */
    window.cbSetPendingDocuments = function(files){
        cbPendingDocuments = files && files.length ? files : null;
    };

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
        var docsForThisSend = cbPendingDocuments;
        cbPendingDocuments = null; // documents attach to this one outgoing message only
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
        var body = 'message=' + encodeURIComponent(text) + '&history=' + encodeURIComponent(JSON.stringify(priorHistory));
        if(docsForThisSend) body += '&documents=' + encodeURIComponent(JSON.stringify(docsForThisSend));
        xhr.send(body);
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
#gpm-overlay { display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.55); }
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
                <input id="gpm_duration" type="number" min="1" class="form-control" placeholder="">
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
                <input id="gpm_value" type="text" class="form-control" placeholder="">
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
            <div class="col-md-4">
              <div class="form-group">
                <label>Bank Account</label>
                <select id="gpm_bankaccount" class="form-control">
                  <option value="">-- Select --</option>
                  <?php
                    $gpmBanks = AccountsItem::find()->where(['account_type' => 2])->andWhere(['Status' => 0])->orderBy(['name' => SORT_ASC])->all();
                    foreach ($gpmBanks as $gpmBank):
                  ?>
                  <option value="<?php echo $gpmBank->id; ?>"><?php echo htmlspecialchars($gpmBank->name); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Billing Cycle (days)</label>
                <input type="number" min="0" step="1" id="gpm_billingcycle" class="form-control" placeholder="e.g. 45">
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

    /* ── open / close overlay ──
       z-index was a hardcoded 100000 — every other floater (gantt-win,
       etc.) starts ABOVE that and climbs higher every time any one of
       them is opened/clicked (see popupBringToFront, main.php), so this
       overlay could never win against a Gantt window opened even once
       after page load, and clicking it again did nothing since nothing
       here ever raised it. Now uses the same shared z-index ceiling. */
    function gpmShow(){
        $('#gpm-overlay').show();
        if (typeof window.popupBringToFront === 'function') window.popupBringToFront('gpm-overlay');
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
                $('#gpm_bankaccount').val(d.bank_account_id || '');
                $('#gpm_billingcycle').val(d.billing_cycle || '');
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
        fd.append('bankaccount', $('#gpm_bankaccount').val());
        fd.append('billingcycle', $('#gpm_billingcycle').val());
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

<!-- ── Project Workflow Tools Floating Popup ─────────────────────────── -->
<div id="pwt-popup">
  <div id="pwt-header">
    <span style="font-size:15px;opacity:.85;">&#128640;</span>
    <h4>Project Workflow Tools</h4>
    <button id="pwt-close-x" style="margin-left:auto;">&times;</button>
  </div>
  <div id="pwt-tabs">
    <button type="button" id="cwbs-open-btn" class="pwt-tab">Create WBS</button>
    <button type="button" class="pwt-tab" data-pwt-pane="pwt-pane-schedule">Schedule Project</button>
    <button type="button" class="pwt-tab" data-pwt-pane="pwt-pane-estimate">Estimate Project</button>
    <button type="button" class="pwt-tab" data-pwt-pane="pwt-pane-optimise">Optimise Project</button>
  </div>
  <div id="pwt-body">
    <div class="pwt-pane" id="pwt-pane-schedule">
      <div class="pwt-placeholder">Schedule Project — coming soon.</div>
    </div>
    <div class="pwt-pane" id="pwt-pane-estimate">
      <div class="pwt-placeholder">Estimate Project — coming soon.</div>
    </div>
    <div class="pwt-pane" id="pwt-pane-optimise">
      <div class="pwt-placeholder">Optimise Project — coming soon.</div>
    </div>
  </div>
</div>

<!-- ── Create WBS Floating Popup ──────────────────────────────────────── -->
<div id="cwbs-popup">
  <div id="cwbs-header">
    <span style="font-size:15px;opacity:.85;">&#128640;</span>
    <h4>Create WBS</h4>
    <button id="cwbs-close-x" style="margin-left:auto;">&times;</button>
  </div>
  <button type="button" id="cwbs-boq-toggle">
    <span class="cwbs-boq-caret">&#9656;</span> Upload BOQ
  </button>
  <div id="cwbs-boq-section">
    <div id="cwbs-boq-upload-col">
      <div id="cwbs-boq-dropzone">
        <div class="cwbs-boq-dz-icon">&#x2B06;</div>
        <div>
          <div class="cwbs-boq-dz-text">Click or drag file here</div>
          <div class="cwbs-boq-dz-sub">Excel (.xlsx, .xls) or PDF</div>
        </div>
      </div>
      <input type="file" id="cwbs-boq-file-input" accept=".xlsx,.xls,.pdf">
      <div id="cwbs-boq-selected-file">
        <span class="cwbs-boq-sf-name" id="cwbs-boq-sf-name"></span>
        <button type="button" class="cwbs-boq-sf-remove" id="cwbs-boq-sf-remove" title="Remove">&times;</button>
      </div>
      <button type="button" id="cwbs-boq-upload-btn" disabled>Upload BOQ</button>
    </div>
    <div id="cwbs-boq-list-col">
      <div id="cwbs-boq-list-hdr">Uploaded BOQs</div>
      <div id="cwbs-boq-list"><p id="cwbs-boq-empty">Loading&hellip;</p></div>
    </div>
  </div>
  <div id="cwbs-main">
    <div id="cwbs-right">
      <div id="cwbs-filters">
        <div class="cwbs-f">
          <label>Project Type</label>
          <select id="cwbs-proj-type"><option value="">Select Project Type</option></select>
        </div>
        <div class="cwbs-f">
          <label>IOW Group</label>
          <select id="cwbs-iow-group"><option value="">Select IOW Group</option></select>
        </div>
      </div>
      <div id="cwbs-msg"></div>
      <div id="cwbs-list-wrap">
        <div id="cwbs-empty" class="cwbs-placeholder">Select a Project Type and IOW Group to list activities.</div>
        <div id="cwbs-loading" class="cwbs-placeholder" style="display:none;">Loading activities&hellip;</div>
        <table id="cwbs-table" style="display:none;">
          <thead>
            <tr>
              <th class="cwbs-sno-td">#</th>
              <th>Activity Name</th>
              <th class="cwbs-unit-td">Unit</th>
              <th class="cwbs-qty-td">Qty</th>
              <th class="cwbs-runits-td">R.Unit</th>
              <th class="cwbs-edit-td">Edit</th>
              <th class="cwbs-tick-td">Select</th>
            </tr>
          </thead>
          <tbody id="cwbs-tbody"></tbody>
        </table>
      </div>
      <div id="cwbs-footer">
        <button type="button" id="cwbs-cancel-btn" class="cwbs-btn cwbs-btn-cancel">Cancel</button>
        <button type="button" id="cwbs-add-btn" class="cwbs-btn cwbs-btn-add">Add</button>
      </div>
    </div>
  </div>
</div>

<!-- ── Create WBS: Edit Activity Floating Popup ──────────────────────── -->
<div id="cwbsed-popup">
  <div id="cwbsed-header">
    <span style="font-size:15px;opacity:.85;">&#9998;</span>
    <h4>Edit Activity</h4>
    <button id="cwbsed-close-x" style="margin-left:auto;">&times;</button>
  </div>
  <div id="cwbsed-body">
    <input type="hidden" id="cwbsed-activity-id" value="">
    <div class="cwbsed-row">
      <div class="cwbsed-f" style="flex:1;">
        <label>Project Type</label>
        <select id="cwbsed-proj-type"><option value="0">Select Project Type</option></select>
      </div>
      <div class="cwbsed-f" style="flex:1;">
        <label>IOW Group</label>
        <select id="cwbsed-iow-group"><option value="">Select IOW Group</option></select>
      </div>
    </div>
    <div class="cwbsed-row">
      <div class="cwbsed-f" style="flex:3;">
        <label>Activity Name</label>
        <input type="text" id="cwbsed-act-name" placeholder="Activity Name">
      </div>
      <div class="cwbsed-f" style="flex:1;">
        <label>Unit</label>
        <input type="text" id="cwbsed-act-unit" placeholder="Unit">
      </div>
    </div>
    <div class="cwbsed-tasks-hdr">
      <span style="font-weight:700;color:#0d3b4f;font-size:12px;">Tasks</span>
    </div>
    <div class="cwbsed-task-col-hdr">
      <span style="flex:2.2;">Task Name</span>
      <span style="flex:1;">Unit</span>
      <span style="flex:1;">Qty/Unit</span>
      <span style="flex:1.2;">Productivity/day</span>
      <span style="width:28px;flex-shrink:0;"></span>
    </div>
    <div id="cwbsed-task-rows"></div>
    <button type="button" id="cwbsed-add-task-row" class="cwbs-btn" style="background:#eef5f3;color:#0d3b4f;margin-top:8px;">+ Add Task</button>
  </div>
  <div id="cwbsed-footer">
    <button type="button" id="cwbsed-cancel-btn" class="cwbs-btn cwbs-btn-cancel">Close</button>
    <button type="button" id="cwbsed-save-btn" class="cwbs-btn cwbs-btn-add">Save</button>
  </div>
</div>

<!-- ── Project Documents Floating Popup ──────────────────────────────── -->
<div id="pdoc-popup">
  <div id="pdoc-header">
    <span style="font-size:15px;opacity:.85;">&#128196;</span>
    <h4>Project Documents</h4>
    <button id="pdoc-close-x" style="margin-left:auto;">&times;</button>
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
    <button id="pdoc-upload-toggle" title="Upload Document" style="margin-left:auto;background:#27ae60;border:1px solid #1e8449;color:#fff;border-radius:20px;padding:3px 14px;font-size:12px;cursor:pointer;font-weight:600;height:28px;">&#8679; Upload</button>
    <button id="pdoc-ask-claude" title="Ask Claude about selected documents" disabled style="background:#8e44ad;border:1px solid #6c3483;color:#fff;border-radius:20px;padding:3px 14px;font-size:12px;cursor:pointer;font-weight:600;opacity:.5;height:28px;">&#129504; Ask Claude</button>
  </div>
  <div id="pdoc-table-wrap">
    <div id="pdoc-loading">Loading…</div>
    <div id="pdoc-empty">No documents found.</div>
    <table id="pdoc-table" style="display:none;">
      <thead>
        <tr>
          <th style="width:30px;text-align:center;"><input type="checkbox" id="pdoc-select-all" title="Select all eligible (PDF/image) documents"></th>
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
    var PDOC_GETCONTENT_URL = '<?php echo \yii\helpers\Url::to(["/projects/pdocgetcontent"]); ?>';
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
                var PDOC_ASKABLE_EXT = ['pdf','jpg','jpeg','png'];
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
                    var ext = (f.filename||'').split('.').pop().toLowerCase();
                    var askable = PDOC_ASKABLE_EXT.indexOf(ext) >= 0;
                    var cbCell = askable
                        ? '<input type="checkbox" class="pdoc-select-cb" data-id="'+f.id+'" data-name="'+fn+'">'
                        : '<input type="checkbox" disabled title="Only PDF and image files can be sent to Claude">';
                    rows += '<tr>'
                        +'<td style="text-align:center;">'+cbCell+'</td>'
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
                pdocUpdateAskClaudeState();
            },
            error:function(){ $('#pdoc-loading').hide(); $('#pdoc-empty').show(); }
        });
    }

    window.pdocViewFile = function pdocViewFile(filename, displayName, fullUrl){
        document.dispatchEvent(new Event('pdocv:open'));
        var url = fullUrl || (PDOC_FILE_BASE + filename);
        var ext = filename.split('.').pop().toLowerCase();
        var $body = $('#pdoc-viewer-body');
        var $vp   = $('#pdoc-viewer-popup');
        $body.empty();
        $('#pdoc-viewer-title').text(displayName);
        if(ext==='pdf'){
            // #toolbar=0&navpanes=0&scrollbar=0 suppresses the browser's
            // built-in PDF viewer chrome (toolbar/side panel) in Chrome —
            // that chrome, not our own CSS, is what renders as a dark bar
            // around the page since the PDF is handed off to the browser's
            // own internal viewer inside the iframe, outside our DOM/CSS
            // reach. This is the one lever PHP/CSS has over it.
            $body.html('<iframe src="'+url+'#toolbar=0&navpanes=0&scrollbar=0" style="width:100%;height:100%;border:none;display:block;"></iframe>');
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
            // The PDF <iframe> captures its own mouse events, so dragging
            // the popup by its header loses sync with the cursor the
            // instant the pointer crosses over the PDF content area
            // (the iframe is a separate document — mousemove events
            // inside it never reach jQuery UI's drag handler). Disabling
            // pointer-events on the iframe for the duration of the drag
            // keeps the document-level mousemove listener receiving every
            // move, same fix this class of bug always needs.
            $vp.draggable({
                handle:'#pdoc-viewer-header', containment:'window', scroll:false,
                start: function(){ $vp.find('iframe').css('pointer-events', 'none'); },
                stop: function(){ $vp.find('iframe').css('pointer-events', ''); }
            });
            $vp.resizable({
                minWidth:320, minHeight:260, handles:'all',
                start: function(){ $vp.find('iframe').css('pointer-events', 'none'); },
                stop: function(){ $vp.find('iframe').css('pointer-events', ''); }
            });
            pdocViewerInited = true;
        } else {
            $vp.addClass('pdoc-open');
        }
    }

    var PDOC_UPLOAD_URL = '<?php echo \yii\helpers\Url::to(["/projects/pdocupload"]); ?>';
    var PDOC_CURRENT_PID = '<?php echo $ProjectId; ?>';

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
        var pid   = PDOC_CURRENT_PID;
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

    /* ── Project Workflow Tools popup ── */
    var pwtInited = false;
    function pwtShow(){
        document.dispatchEvent(new Event('pwt:open'));
        var $p = $('#pwt-popup');
        if(!pwtInited){
            $p.css({ visibility:'hidden', top:0, left:0 }).addClass('pwt-open');
            var pw = $p.outerWidth(), ph = $p.outerHeight();
            var ww = $(window).width(), wh = $(window).height();
            $p.css({ visibility:'', top: Math.max(60, (wh - ph) / 2), left: Math.max(0, (ww - pw) / 2) });
            $p.draggable({ handle:'#pwt-header', containment:'window', scroll:false });
            $p.resizable({ minWidth:420, minHeight:280, handles:'all' });
            pwtInited = true;
        } else {
            $p.addClass('pwt-open');
        }
    }
    function pwtHide(){
        $('#pwt-popup').removeClass('pwt-open');
    }
    $(document).on('click','#pwt-nav-btn',  function(e){ e.preventDefault(); pwtShow(); });
    $(document).on('click','#pwt-close-x',  function(){ pwtHide(); });
    $(document).on('click','#pwt-tabs .pwt-tab[data-pwt-pane]', function(){
        var pane = $(this).data('pwt-pane');
        $('#pwt-tabs .pwt-tab[data-pwt-pane]').removeClass('active');
        $(this).addClass('active');
        $('.pwt-pane').removeClass('active');
        $('#' + pane).addClass('active');
    });

    /* ── Create WBS popup ──
       Project Type + IOW Group pickers -> tick-select activities ->
       batch "Add" writes each selected activity into the Gantt via the
       same actionWbsadd() endpoint the existing Activity Picker tick
       button uses, all resolving to ONE shared IOW (named after the
       selected IOW Group) so the whole batch lands under a single WBS
       row instead of one IOW per activity. Both this popup and the
       Gantt window are left open afterward so the additions are visible
       immediately — reloadGantt() only refreshes the Gantt's own
       content, it doesn't touch this popup. */
    var cwbsInited = false;
    var cwbsSelected = {};   // activity_id -> {id, name}
    var cwbsResCache = {};   // activity_id -> actionGetactivityresources() result

    function cwbsShow(){
        var $p = $('#cwbs-popup');
        if(!cwbsInited){
            $p.css({ visibility:'hidden', top:0, left:0 }).addClass('cwbs-open');
            var pw = $p.outerWidth(), ph = $p.outerHeight();
            var ww = $(window).width(), wh = $(window).height();
            $p.css({ visibility:'', top: Math.max(60, (wh - ph) / 2), left: Math.max(0, (ww - pw) / 2) });
            $p.draggable({ handle:'#cwbs-header', containment:'window', scroll:false });
            $p.resizable({ minWidth:460, minHeight:400, handles:'all' });
            cwbsInited = true;
        } else {
            $p.addClass('cwbs-open');
        }
        if(typeof window.popupBringToFront === 'function') window.popupBringToFront('cwbs-popup');
        if($('#cwbs-proj-type option').length <= 1){
            $.ajax({ type:'POST', url:'../projectsmain/getwbtypelist', dataType:'json', success:function(d){
                var sel = $('#cwbs-proj-type');
                (d.items||[]).forEach(function(it){
                    sel.append('<option value="'+it.id+'">'+$('<div>').text(it.name).html()+'</option>');
                });
            }});
        }
        cwbsLoadIowGroups();
        cwbsLoadBoqList();
    }
    function cwbsHide(){
        $('#cwbs-popup').removeClass('cwbs-open');
    }

    /* ── Upload BOQ section (collapsible) ──
       Same uploadboq/listboq/deleteboq endpoints the existing #iow-modal
       "Upload BOQ" tab already uses, reused here under their own cwbs-*
       ids/classes so both features' JS stay fully independent. Project id
       is resolved the same way that tab's JS does — from #gantt-win-open's
       data-projectid attribute, a global layout element always present
       regardless of which popup is open. View reuses the Project
       Documents viewer popup (window.pdocViewFile), pointed at the BOQ's
       own storage folder via an explicit full URL (its 3rd, optional arg)
       since BOQs live under /uploads/boq/, not /uploads/projects/ — that
       viewer is its own separate draggable/resizable popup already
       registered in the app's floaters list, so it and Create WBS (and
       the Gantt) all stay open together. */
    var _cwbsBoqSelectedFile = null;
    var CWBS_BOQ_ALLOWED_EXT = ['xlsx', 'xls', 'pdf'];
    var CWBS_BOQ_FILE_BASE = '<?php echo \yii\helpers\Url::base(true); ?>/uploads/boq/';

    function cwbsBoqProjectId(){
        var el = document.getElementById('gantt-win-open');
        return el ? el.getAttribute('data-projectid') : '';
    }
    function cwbsBoqExtOf(name){
        var parts = (name || '').split('.');
        return parts.length > 1 ? parts.pop().toLowerCase() : '';
    }
    function cwbsBoqSetSelectedFile(file){
        if(!file){
            _cwbsBoqSelectedFile = null;
            $('#cwbs-boq-selected-file').removeClass('show');
            $('#cwbs-boq-upload-btn').prop('disabled', true);
            return;
        }
        if(CWBS_BOQ_ALLOWED_EXT.indexOf(cwbsBoqExtOf(file.name)) === -1){
            alert('Only Excel (.xlsx, .xls) or PDF files are allowed.');
            return;
        }
        _cwbsBoqSelectedFile = file;
        $('#cwbs-boq-sf-name').text(file.name);
        $('#cwbs-boq-selected-file').addClass('show');
        $('#cwbs-boq-upload-btn').prop('disabled', false);
    }
    function cwbsBoqFmtDate(s){
        if(!s) return '';
        var d = new Date(s.replace(' ', 'T'));
        if(isNaN(d.getTime())) return s;
        return d.toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' }) + ' ' +
               d.toLocaleTimeString('en-IN', { hour:'2-digit', minute:'2-digit' });
    }
    function cwbsBoqRenderList(rows){
        if(!rows.length){ $('#cwbs-boq-list').html('<p id="cwbs-boq-empty">No BOQ uploaded yet.</p>'); return; }
        var html = '';
        rows.forEach(function(r){
            html += '<div class="cwbs-boq-file-row" data-id="'+r.id+'" data-filename="'+$('<div>').text(r.filename).html()+'" data-name="'+$('<div>').text(r.original_name).html()+'">'
                + '<span class="cwbs-boq-fr-icon">&#x1F4C4;</span>'
                + '<div class="cwbs-boq-fr-info">'
                +   '<div class="cwbs-boq-fr-name">'+$('<span>').text(r.original_name).html()+'</div>'
                +   '<div class="cwbs-boq-fr-date">'+$('<span>').text(cwbsBoqFmtDate(r.uploaded_at)).html()+'</div>'
                + '</div>'
                + '<button type="button" class="cwbs-boq-fr-view" data-id="'+r.id+'" title="View">View</button>'
                + '<button type="button" class="cwbs-boq-fr-del" data-id="'+r.id+'" title="Delete">&times;</button>'
                + '</div>';
        });
        $('#cwbs-boq-list').html(html);
    }
    function cwbsLoadBoqList(){
        var pid = cwbsBoqProjectId();
        if(!pid){ $('#cwbs-boq-list').html('<p id="cwbs-boq-empty">No project selected.</p>'); return; }
        $.ajax({
            type:'POST', url:'../projectsmain/listboq', dataType:'json', data:{ project_id: pid },
            success:function(d){
                if(d.error && d.error !== 'No'){ $('#cwbs-boq-list').html('<p id="cwbs-boq-empty">'+$('<span>').text(d.errortext || 'Error loading files.').html()+'</p>'); return; }
                cwbsBoqRenderList(d.rows || []);
            },
            error:function(){ $('#cwbs-boq-list').html('<p id="cwbs-boq-empty">Failed to load.</p>'); }
        });
    }
    $(document).on('click','#cwbs-boq-toggle', function(){
        $(this).toggleClass('cwbs-boq-open');
        $('#cwbs-boq-section').toggleClass('cwbs-boq-open');
    });
    $(document).on('click','#cwbs-boq-dropzone', function(){ $('#cwbs-boq-file-input').trigger('click'); });
    $(document).on('change','#cwbs-boq-file-input', function(){
        if(this.files && this.files[0]) cwbsBoqSetSelectedFile(this.files[0]);
    });
    $(document).on('dragover','#cwbs-boq-dropzone', function(e){ e.preventDefault(); e.stopPropagation(); $(this).addClass('cwbs-boq-drag-over'); });
    $(document).on('dragleave','#cwbs-boq-dropzone', function(e){ e.preventDefault(); e.stopPropagation(); $(this).removeClass('cwbs-boq-drag-over'); });
    $(document).on('drop','#cwbs-boq-dropzone', function(e){
        e.preventDefault(); e.stopPropagation();
        $(this).removeClass('cwbs-boq-drag-over');
        var files = e.originalEvent.dataTransfer ? e.originalEvent.dataTransfer.files : null;
        if(files && files[0]) cwbsBoqSetSelectedFile(files[0]);
    });
    $(document).on('click','#cwbs-boq-sf-remove', function(e){
        e.stopPropagation();
        cwbsBoqSetSelectedFile(null);
        $('#cwbs-boq-file-input').val('');
    });
    $(document).on('click','#cwbs-boq-upload-btn', function(){
        var pid = cwbsBoqProjectId();
        if(!pid){ alert('No project selected.'); return; }
        if(!_cwbsBoqSelectedFile) return;
        var fd = new FormData();
        fd.append('project_id', pid);
        fd.append('boq_file', _cwbsBoqSelectedFile);
        var $btn = $('#cwbs-boq-upload-btn').prop('disabled', true).text('Uploading…');
        $.ajax({
            type:'POST', url:'../projectsmain/uploadboq', data:fd,
            processData:false, contentType:false, dataType:'json',
            success:function(d){
                $btn.prop('disabled', false).text('Upload BOQ');
                if(d.error && d.error !== 'No'){ alert(d.errortext || 'Upload failed.'); return; }
                cwbsBoqSetSelectedFile(null);
                $('#cwbs-boq-file-input').val('');
                cwbsLoadBoqList();
            },
            error:function(){
                $btn.prop('disabled', false).text('Upload BOQ');
                alert('Upload failed. Please try again.');
            }
        });
    });
    // Opens the shared, already-draggable/resizable file viewer popup
    // (#pdoc-viewer-popup — the same one Project Documents uses) as its
    // OWN floating window, separate from Create WBS, so both can be
    // moved/stay open independently at once. Pointed at the BOQ's own
    // storage folder via the explicit full-URL argument since BOQs live
    // under /uploads/boq/, not /uploads/projects/.
    $(document).on('click','.cwbs-boq-fr-view', function(){
        var $row = $(this).closest('.cwbs-boq-file-row');
        var filename = $row.data('filename');
        var displayName = $row.data('name');
        if(typeof window.pdocViewFile === 'function'){
            window.pdocViewFile(filename, displayName, CWBS_BOQ_FILE_BASE + filename);
        }
    });

    $(document).on('click','.cwbs-boq-fr-del', function(){
        if(!confirm('Delete this BOQ file?')) return;
        var id = $(this).data('id');
        $.ajax({
            type:'POST', url:'../projectsmain/deleteboq', dataType:'json', data:{ id:id },
            success:function(d){
                if(d.error && d.error !== 'No'){ alert(d.errortext || 'Delete failed.'); return; }
                cwbsLoadBoqList();
            },
            error:function(){ alert('Delete failed. Please try again.'); }
        });
    });
    function cwbsResetList(msg){
        cwbsSelected = {};
        $('#cwbs-tbody').empty();
        $('#cwbs-table').hide();
        $('#cwbs-loading').hide();
        $('#cwbs-empty').text(msg || 'Select a Project Type and IOW Group to list activities.').show();
        $('#cwbs-msg').text('');
        $('#cwbs-add-btn').prop('disabled', true);
    }
    function cwbsLoadActivities(){
        var typeId = $('#cwbs-proj-type').val();
        var groupId = $('#cwbs-iow-group').val();
        if(!typeId || !groupId){ cwbsResetList(); return; }
        $('#cwbs-empty').hide();
        $('#cwbs-table').hide();
        $('#cwbs-loading').show();
        $.ajax({
            type:'POST', url:'../projectsmain/getactivitiesbytypeandgroup', dataType:'json',
            data:{ typeId:typeId, iowGroupId:groupId },
            success:function(d){
                $('#cwbs-loading').hide();
                cwbsSelected = {};
                $('#cwbs-add-btn').prop('disabled', true);
                var items = d.items || [];
                if(!items.length){ cwbsResetList('No activities found under this IOW Group.'); return; }
                var rows = '';
                items.forEach(function(it, idx){
                    rows += '<tr data-actid="'+it.id+'">'
                        + '<td class="cwbs-sno-td">'+(idx+1)+'</td>'
                        + '<td class="cwbs-act-name">'+$('<div>').text(it.name).html()+'</td>'
                        + '<td class="cwbs-unit-td">'+$('<div>').text(it.unit || '').html()+'</td>'
                        + '<td class="cwbs-qty-td"><input type="text" inputmode="decimal" class="cwbs-qty-input" data-actid="'+it.id+'" placeholder="Qty" value="" oninput="this.value=this.value.replace(/[^0-9.]/g,\'\')"></td>'
                        + '<td class="cwbs-runits-td"><input type="text" inputmode="decimal" class="cwbs-runits-input" data-actid="'+it.id+'" placeholder="R.Unit" value="1" oninput="this.value=this.value.replace(/[^0-9.]/g,\'\')"></td>'
                        + '<td class="cwbs-edit-td"><button type="button" class="cwbs-edit-btn" data-actid="'+it.id+'" data-actname="'+String(it.name).replace(/"/g,'&quot;')+'" title="Edit Activity">&#9998;</button></td>'
                        + '<td class="cwbs-tick-td"><button type="button" class="cwbs-tick-btn" data-actid="'+it.id+'" data-actname="'+String(it.name).replace(/"/g,'&quot;')+'" title="Select">&#10003;</button></td>'
                        + '</tr>';
                });
                $('#cwbs-tbody').html(rows);
                $('#cwbs-table').show();
            },
            error:function(){
                $('#cwbs-loading').hide();
                cwbsResetList('Failed to load activities. Please try again.');
            }
        });
    }
    $(document).on('click','#cwbs-open-btn', function(){ cwbsShow(); });
    $(document).on('click','#cwbs-close-x, #cwbs-cancel-btn', function(){ cwbsHide(); });
    // IOW Group list depends on the selected Project Type: only groups that
    // have activities of that type are shown.
    function cwbsLoadIowGroups(){
        var typeId = $('#cwbs-proj-type').val();
        var sel = $('#cwbs-iow-group');
        sel.html('<option value="">Select IOW Group</option>');
        if(!typeId){ cwbsResetList(); return; }
        $.ajax({ type:'POST', url:'../projectsmain/getiowgroupsbytype', dataType:'json', data:{ typeId:typeId },
            success:function(d){
                if($('#cwbs-proj-type').val() !== typeId) return; // type changed meanwhile
                (d.items||[]).forEach(function(it){
                    sel.append('<option value="'+it.id+'">'+$('<div>').text(it.name).html()+'</option>');
                });
                cwbsLoadActivities();
            }
        });
    }
    $(document).on('change','#cwbs-proj-type', function(){ cwbsLoadIowGroups(); });
    $(document).on('change','#cwbs-iow-group', function(){ cwbsLoadActivities(); });
    $(document).on('click','.cwbs-tick-btn', function(){
        var id = $(this).data('actid');
        var name = $(this).data('actname');
        if(cwbsSelected[id]){
            delete cwbsSelected[id];
            $(this).removeClass('cwbs-selected');
        } else {
            cwbsSelected[id] = { id:id, name:name };
            $(this).addClass('cwbs-selected');
        }
        $('#cwbs-add-btn').prop('disabled', !Object.keys(cwbsSelected).length);
    });
    $(document).on('click','#cwbs-add-btn', function(){
        var ids = Object.keys(cwbsSelected);
        if(!ids.length) return;

        // Qty is mandatory for every selected activity — the Activity
        // Library has no resource_units field, so Create WBS's own R.Unit
        // input is the only source for that part of the formula, and Qty
        // is what the whole duration calculation is driven by.
        var missingQty = [];
        ids.forEach(function(id){
            var q = parseFloat($('.cwbs-qty-input[data-actid="'+id+'"]').val());
            if(!(q > 0)) missingQty.push(cwbsSelected[id].name);
        });
        if(missingQty.length){
            alert('Please enter a Qty for: ' + missingQty.join(', '));
            return;
        }

        var groupId = $('#cwbs-iow-group').val();
        var iowName = $('#cwbs-iow-group option:selected').text();
        var $btn = $(this).prop('disabled', true);
        $('#cwbs-msg').css('color','#555').text('Adding ' + ids.length + ' activity/activities…');

        function addOne(i){
            if(i >= ids.length){
                $btn.prop('disabled', false);
                $('#cwbs-msg').css('color','#197a3a').text('Added ' + ids.length + ' activity/activities to the Gantt.');
                cwbsSelected = {};
                $('.cwbs-tick-btn').removeClass('cwbs-selected');
                $('#cwbs-add-btn').prop('disabled', true);
                if(typeof window.reloadGantt === 'function') window.reloadGantt();
                return;
            }
            var act = cwbsSelected[ids[i]];
            var rowQty = parseFloat($('.cwbs-qty-input[data-actid="'+act.id+'"]').val());
            var rowRUnit = parseFloat($('.cwbs-runits-input[data-actid="'+act.id+'"]').val()) || 1;
            $.ajax({
                type:'POST', url:'../projectsmain/getactivityresources', dataType:'json',
                data:{ activity_id: act.id },
                success:function(res){
                    var tasks = (res.tasks || []).map(function(t){
                        return { name:t.task_name, qty:t.task_qty, prod:t.productivity, resunits:t.resunits };
                    });
                    // Time to do 1 unit of the activity = sum over tasks of
                    // (Qty/Unit ÷ Productivity/day) — the Activity Library
                    // has no resource_units field, so R.Unit here is the
                    // single value entered on this Create WBS row and
                    // divides the WHOLE total (not per task).
                    var sumDurPerUnit = 0;
                    (res.tasks || []).forEach(function(t){
                        var dpu = (t.productivity > 0) ? ((t.task_qty || 0) / t.productivity) : 0;
                        sumDurPerUnit += dpu;
                    });
                    var qty = rowQty;
                    var duration = (sumDurPerUnit > 0) ? Math.ceil((sumDurPerUnit * qty) / rowRUnit) : 1;
                    var payload = {
                        iow_name: iowName,
                        iow_group_id: groupId,
                        iow_act_id: act.id,
                        act_name: act.name,
                        sch_unit: res.sch_unit || res.unit || '',
                        sch_qty: qty,
                        qty: qty,
                        unit: res.unit || '',
                        duration: duration,
                        tasks: tasks,
                        resources: []
                    };
                    $.ajax({
                        type:'POST', url:'../projectsmain/wbsadd', dataType:'json',
                        data:{ payload: JSON.stringify(payload) },
                        success:function(){ addOne(i + 1); },
                        error:function(){
                            $('#cwbs-msg').css('color','#c00').text('Failed to add "' + act.name + '". Stopped.');
                            $btn.prop('disabled', false);
                        }
                    });
                },
                error:function(){
                    $('#cwbs-msg').css('color','#c00').text('Failed to load details for "' + act.name + '". Stopped.');
                    $btn.prop('disabled', false);
                }
            });
        }
        addOne(0);
    });

    /* ── Create WBS: Edit Activity popup ──
       Opened from the pencil icon on a Create WBS row. Prefills Project
       Type + IOW Group (same two filters already selected in the Create
       WBS popup), Activity Name + Unit, and the activity's Tasks — with
       the activity itself seeded as the first task row when the activity
       has no tasks yet, per explicit direction. Saves via the same
       createactivity/updateactivitywithtasks endpoints the real Activity
       Library "Add Activity" modal uses, so edits made here are fully
       equivalent to editing it from the Library directly. */
    var cwbsedInited = false;
    var cwbsedTaskRowSeq = 0;

    function cwbsedShow(){
        var $p = $('#cwbsed-popup');
        if(!cwbsedInited){
            $p.css({ visibility:'hidden', top:0, left:0 }).addClass('cwbsed-open');
            var pw = $p.outerWidth(), ph = $p.outerHeight();
            var ww = $(window).width(), wh = $(window).height();
            $p.css({ visibility:'', top: Math.max(60, (wh - ph) / 2), left: Math.max(0, (ww - pw) / 2) });
            $p.draggable({ handle:'#cwbsed-header', containment:'window', scroll:false });
            $p.resizable({ minWidth:460, minHeight:360, handles:'all' });
            cwbsedInited = true;
        } else {
            $p.addClass('cwbsed-open');
        }
        if(typeof window.popupBringToFront === 'function') window.popupBringToFront('cwbsed-popup');
    }
    function cwbsedHide(){
        $('#cwbsed-popup').removeClass('cwbsed-open');
    }
    function cwbsedTaskRowHtml(name, unit, qty, prod, taskId){
        cwbsedTaskRowSeq++;
        return '<div class="cwbsed-task-row" data-seq="'+cwbsedTaskRowSeq+'">'
            + '<input type="hidden" class="cwbsed-task-id" value="'+(taskId||0)+'">'
            + '<input type="text" class="cwbsed-task-name" placeholder="Task Name" value="'+$('<div>').text(name||'').html()+'">'
            + '<input type="text" class="cwbsed-task-unit" placeholder="Unit" value="'+$('<div>').text(unit||'').html()+'">'
            + '<input type="number" step="0.001" min="0" class="cwbsed-task-qty" placeholder="Qty/Unit" value="'+$('<div>').text(qty||'').html()+'">'
            + '<input type="number" step="0.001" min="0" class="cwbsed-task-prod" placeholder="Productivity/day" value="'+$('<div>').text(prod||'').html()+'">'
            + '<button type="button" class="cwbsed-task-remove" title="Remove">&times;</button>'
            + '</div>';
    }

    $(document).on('click','.cwbs-edit-btn', function(){
        var actId = $(this).data('actid');
        var actName = $(this).data('actname');
        var typeId = $('#cwbs-proj-type').val();
        var groupId = $('#cwbs-iow-group').val();

        $('#cwbsed-activity-id').val(actId);
        $('#cwbsed-act-name').val(actName);
        $('#cwbsed-act-unit').val('');
        $('#cwbsed-task-rows').empty();

        if($('#cwbsed-proj-type option').length <= 1){
            $.ajax({ type:'POST', url:'../projectsmain/getwbtypelist', dataType:'json', success:function(d){
                var sel = $('#cwbsed-proj-type');
                (d.items||[]).forEach(function(it){
                    sel.append('<option value="'+it.id+'">'+$('<div>').text(it.name).html()+'</option>');
                });
                sel.val(typeId || '0');
            }});
        } else {
            $('#cwbsed-proj-type').val(typeId || '0');
        }
        if(typeof window.loadIowGroupOptions === 'function'){
            window.loadIowGroupOptions(document.getElementById('cwbsed-iow-group'), function(){
                $('#cwbsed-iow-group').val(groupId || '');
            });
        }

        cwbsedShow();
        $('#cwbsed-task-rows').html('<div class="cwbsed-placeholder" style="padding:10px 0;color:#888;font-size:12px;">Loading tasks&hellip;</div>');
        $.ajax({
            type:'POST', url:'../projectsmain/getactivityresources', dataType:'json',
            data:{ activity_id: actId },
            success:function(res){
                $('#cwbsed-act-unit').val(res.unit || '');
                var tasks = res.tasks || [];
                var rows = '';
                if(tasks.length){
                    tasks.forEach(function(t){
                        rows += cwbsedTaskRowHtml(t.task_name, t.task_unit, t.task_qty, t.productivity, t.id);
                    });
                } else {
                    // No tasks on this activity yet — seed the first task
                    // row with the activity itself (name/unit), per spec.
                    rows += cwbsedTaskRowHtml(actName, res.unit || '', '', '', 0);
                }
                $('#cwbsed-task-rows').html(rows);
            },
            error:function(){
                $('#cwbsed-task-rows').html(cwbsedTaskRowHtml(actName, '', '', '', 0));
            }
        });
    });
    $(document).on('click','#cwbsed-close-x, #cwbsed-cancel-btn', function(){ cwbsedHide(); });
    $(document).on('click','#cwbsed-add-task-row', function(){
        $('#cwbsed-task-rows').append(cwbsedTaskRowHtml('', '', '', ''));
    });
    $(document).on('click','.cwbsed-task-remove', function(){
        $(this).closest('.cwbsed-task-row').remove();
    });
    $(document).on('click','#cwbsed-save-btn', function(){
        var actId = $('#cwbsed-activity-id').val();
        var actName = $.trim($('#cwbsed-act-name').val());
        var actUnit = $.trim($('#cwbsed-act-unit').val());
        if(!actName){ alert('Activity Name is required.'); return; }
        if(!actUnit){ alert('Unit is required.'); return; }

        var taskIds = [], taskNames = [], taskUnits = [], taskQtys = [], taskProds = [];
        var taskError = false;
        $('#cwbsed-task-rows .cwbsed-task-row').each(function(){
            var $row = $(this);
            var name = $.trim($row.find('.cwbsed-task-name').val());
            var unit = $.trim($row.find('.cwbsed-task-unit').val());
            var qty  = $.trim($row.find('.cwbsed-task-qty').val());
            var prod = $.trim($row.find('.cwbsed-task-prod').val());
            if(!name && !unit && !qty && !prod) return; // skip fully blank rows
            if(!name || !unit || !qty || !prod){ taskError = true; }
            taskIds.push($row.find('.cwbsed-task-id').val() || 0);
            taskNames.push(name);
            taskUnits.push(unit);
            taskQtys.push(qty);
            taskProds.push(prod);
        });
        if(taskError){ alert('Please fill in Task Name, Unit, Qty/Unit and Productivity/day for every task row (or remove the empty row).'); return; }

        var $btn = $(this).prop('disabled', true);
        var postData = {
            activity_id:          actId,
            activitytypeid:       0,
            worktypeid:           $('#cwbsed-proj-type').val() || 0,
            iow_group_id:         $('#cwbsed-iow-group').val() || 0,
            working_hours:        8,
            'estactivityname[]':  [actName],
            'estactivityunit[]':  [actUnit],
            'task_id[]':          taskIds,
            'task_name[]':        taskNames,
            'task_unit[]':        taskUnits,
            'task_qty[]':         taskQtys,
            'task_productivity[]':taskProds
        };
        $.ajax({
            type:'POST', url:'../projects/updateactivitywithtasks', dataType:'json',
            data: postData,
            success:function(d){
                $btn.prop('disabled', false);
                if(d.error === 'No'){
                    cwbsedHide();
                    // Refresh the Create WBS list behind it so the edited
                    // name/unit is reflected immediately.
                    if(typeof cwbsLoadActivities === 'function') cwbsLoadActivities();
                } else {
                    alert('Save failed: ' + (d.msg || 'Unknown error'));
                }
            },
            error:function(xhr){
                $btn.prop('disabled', false);
                alert('Save failed. Please try again. (' + xhr.status + ')');
            }
        });
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

    /* ── Ask Claude about selected documents ── */
    function pdocUpdateAskClaudeState(){
        var n = $('.pdoc-select-cb:checked').length;
        $('#pdoc-ask-claude').prop('disabled', n===0).css('opacity', n===0 ? .5 : 1);
        var allCbs = $('.pdoc-select-cb');
        $('#pdoc-select-all').prop('checked', allCbs.length>0 && n===allCbs.length);
    }

    $(document).on('change','.pdoc-select-cb', pdocUpdateAskClaudeState);

    $(document).on('change','#pdoc-select-all', function(){
        $('.pdoc-select-cb').prop('checked', this.checked);
        pdocUpdateAskClaudeState();
    });

    $(document).on('click','#pdoc-ask-claude', function(){
        var $btn = $(this);
        if($btn.prop('disabled')) return;
        var ids = [], names = [];
        $('.pdoc-select-cb:checked').each(function(){
            ids.push($(this).data('id'));
            names.push($(this).data('name'));
        });
        if(!ids.length) return;

        $btn.prop('disabled',true).html('&#8987; Loading…');
        $.ajax({
            type:'POST', url:PDOC_GETCONTENT_URL, dataType:'json',
            data:{ ids: ids },
            success:function(d){
                $btn.prop('disabled',false).html('&#129504; Ask Claude');
                pdocUpdateAskClaudeState();
                if(!d || d.error!=='No' || !d.files || !d.files.length){
                    alert(d && d.errortext ? d.errortext : 'Could not load selected documents.');
                    return;
                }
                if(window.cbSetPendingDocuments) window.cbSetPendingDocuments(d.files);
                var cbWin = document.getElementById('cb-win');
                if(cbWin.style.display !== 'flex') document.getElementById('cb-btn').click();
                var inp = document.getElementById('cb-input');
                if(inp){
                    inp.value = 'I\'ve attached: ' + names.join(', ') + '. ';
                    inp.focus();
                    var ln = inp.value.length;
                    if(inp.setSelectionRange) inp.setSelectionRange(ln, ln);
                }
            },
            error:function(xhr){
                $btn.prop('disabled',false).html('&#129504; Ask Claude');
                pdocUpdateAskClaudeState();
                alert('Server error ('+xhr.status+'). Please try again.');
            }
        });
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
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&family=Barlow+Condensed:wght@400;500;600;700;800&family=Barlow:wght@300;400;500;600;700;800;900&display=swap');
/* ── Modal shell ──────────────────────────────────────────────────────────── */
/* No dimming overlay: dashboards are plain floating windows, like every
   other popup in the app, so several can stay open and visible at once
   instead of one burying another behind a full-screen backdrop. Only the
   X button closes them (see the click handlers in _performancedashboard.js). */
#pd-bk{display:none !important}
#pd-modal{display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:78vw;max-width:1180px;height:88vh;z-index:100200;border-radius:6px;overflow:hidden;background:#f0f2f7;box-shadow:0 8px 32px rgba(0,0,0,.7);flex-direction:column}
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
#pd-right{flex:2.2;display:grid;grid-template-columns:1fr 1fr;grid-template-rows:repeat(4,1fr);gap:8px;}
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
/* ── Resource Capacity bar hover tooltip ──────────────────────────────────── */
#pd-res-tip{display:none;position:fixed;z-index:200500;background:#fff;border-radius:6px;border:1px solid #dde3ef;box-shadow:0 6px 20px rgba(0,0,0,.28);overflow:hidden;font-family:'Barlow Condensed',sans-serif;min-width:160px;}
#pd-res-tip table{width:100%;border-collapse:collapse;font-size:11px;}
#pd-res-tip th{background:#1a2540;color:#fff;padding:5px 10px;text-align:left;white-space:nowrap;}
#pd-res-tip td{padding:5px 10px;color:#1a2540;border-top:1px solid #eef1f5;}
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
/* No dimming overlay: see the pd-bk comment above — same reasoning applies
   to every dashboard modal so they can coexist on screen. */
#cd-bk{display:none !important}
#cd-modal{display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:78vw;max-width:1180px;height:88vh;z-index:100200;border-radius:6px;overflow:hidden;background:#f0f2f7;box-shadow:0 8px 32px rgba(0,0,0,.7);flex-direction:column}
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

#cf-bk{display:none;position:fixed;inset:0;background:transparent;z-index:100000;pointer-events:none}
#cf-bk.cf-open{display:block}
#cf-modal{display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:850px;max-width:94vw;height:72vh;z-index:100200;border-radius:6px;overflow:hidden;background:#f0f2f7;box-shadow:0 8px 32px rgba(0,0,0,.7);flex-direction:column}
#cf-modal.cf-open{display:flex}
#cf-hdr{background:linear-gradient(180deg,#0c1535 0%,#05091c 100%);padding:5px 16px;display:flex;align-items:center;border-bottom:1px solid #2a4a8a;flex-shrink:0;cursor:move;user-select:none}
.cf-rs{position:absolute;z-index:20;background:transparent}
.cf-rs-e{right:-4px;top:10px;bottom:10px;width:10px;cursor:e-resize}
.cf-rs-w{left:-4px;top:10px;bottom:10px;width:10px;cursor:w-resize}
.cf-rs-s{bottom:-4px;left:10px;right:10px;height:10px;cursor:s-resize}
.cf-rs-n{top:-4px;left:10px;right:10px;height:10px;cursor:n-resize}
.cf-rs-se{right:-4px;bottom:-4px;width:14px;height:14px;cursor:se-resize}
.cf-rs-sw{left:-4px;bottom:-4px;width:14px;height:14px;cursor:sw-resize}
.cf-rs-ne{right:-4px;top:-4px;width:14px;height:14px;cursor:ne-resize}
.cf-rs-nw{left:-4px;top:-4px;width:14px;height:14px;cursor:nw-resize}
#cf-close{background:none;border:none;color:#fff;opacity:.85;font-size:18px;line-height:1;cursor:pointer;padding:0 3px;margin-left:6px}
#cf-close:hover{opacity:1}
#cf-body{flex:1;min-height:0;overflow:hidden;padding:6px 10px 0;display:flex;flex-direction:column;background:#e2e4e8}
#cf-grid{flex:1;min-height:0;display:grid;grid-template-columns:repeat(4, 1fr);grid-template-rows:repeat(3, 1fr);row-gap:0;column-gap:0}
#cf-modal .ph{background:#5a5a5a;font-size:11px;}
#cf-modal .panel{border-right:3px solid #a0aec0}

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
<div id="cf-bk"></div>
<div id="cf-modal" class="dash-modal">
  <div class="cf-rs cf-rs-n" data-dir="n"></div>
  <div class="cf-rs cf-rs-s" data-dir="s"></div>
  <div class="cf-rs cf-rs-e" data-dir="e"></div>
  <div class="cf-rs cf-rs-w" data-dir="w"></div>
  <div class="cf-rs cf-rs-ne" data-dir="ne"></div>
  <div class="cf-rs cf-rs-nw" data-dir="nw"></div>
  <div class="cf-rs cf-rs-se" data-dir="se"></div>
  <div class="cf-rs cf-rs-sw" data-dir="sw"></div>
  <div id="cf-hdr">
    <span id="cf-title" style="flex:1;color:#ffffff;font-size:12px;font-weight:700;letter-spacing:0.04em;text-align:left;text-transform:uppercase">Manage Cashflow</span>
    <button id="cf-close">&times;</button>
  </div>
  <div id="cf-body">
    <div id="cf-subhdr-wrap" style="position:relative;flex:1;min-height:0;display:flex;flex-direction:column;">
      <div id="cf-subhdr" style="display:flex;flex-shrink:0;border:1px solid #4a4f57;margin-bottom:6px;">
        <div style="flex:1;background:#4a4f57;color:#fff;font-family:'Nunito',Arial,sans-serif;font-size:12px;font-weight:400;text-transform:uppercase;letter-spacing:.02em;text-align:center;padding:2px 0;">Cash-to-Cash Cycle&nbsp;&nbsp;&nbsp;<span id="cf-c2c-legend" style="text-transform:none;letter-spacing:normal;font-weight:400;"></span></div>
        <div style="flex:1;background:#1a0a2e;color:#fff;font-family:'Nunito',Arial,sans-serif;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.02em;text-align:center;padding:2px 0;border-left:1px solid #4a4f57;">ROI and Free Cash</div>
      </div>
      <div id="cf-grid">
      <div class="panel" style="grid-column:1;grid-row:1;background:#e3e5e8;"><div class="ph" style="background:#4a4f57;">Investments</div><div class="gp" style="background:#e3e5e8;" id="cf-currentassets"></div></div>
      <div class="panel" style="grid-column:1;grid-row:2;background:#e3e5e8;"><div class="ph" style="background:#4a4f57;">Work In Progress</div><div class="gp" style="background:#e3e5e8;" id="cf-billsreceivable"></div></div>
      <div class="panel" style="grid-column:1;grid-row:3;background:#e3e5e8;"><div class="ph" style="background:#4a4f57;">Bills Receivable</div><div class="gp" style="background:#e3e5e8;" id="cf-contract-income"></div></div>
      <div class="panel" style="grid-column:2;grid-row:1;background:#e3e5e8;"><div class="ph" style="background:#4a4f57;">Bank-Working Capital</div><div class="gp" style="background:#e3e5e8;" id="cf-bankdial"></div></div>
      <div class="panel" style="grid-column:2;grid-row:2;background:#e3e5e8;"><div class="ph" style="background:#4a4f57;">Current Liabilities</div><div class="gp" style="background:#e3e5e8;" id="cf-bank"></div></div>
      <div class="panel" style="grid-column:2;grid-row:3;background:#e3e5e8;"><div class="ph" style="background:#4a4f57;">Total Investment</div><div class="gp" style="background:#e3e5e8;" id="cf-netinvestment"></div></div>
      <div class="panel" style="grid-column:3;grid-row:1;background:#eceef0;"><div class="ph" style="background:#1a0a2e;">Contract Income</div><div class="gp" style="background:#eceef0;" id="cf-contractincome"></div></div>
      <div class="panel" style="grid-column:3;grid-row:2;background:#eceef0;"><div class="ph" style="background:#1a0a2e;">Project Expenditure</div><div class="gp" style="background:#eceef0;" id="cf-projectexpenditure"></div></div>
      <div class="panel" style="grid-column:3;grid-row:3;background:#eceef0;"><div class="ph" style="background:#1a0a2e;">Contribution Margin</div><div class="gp" style="background:#eceef0;" id="cf-contribmargin"></div></div>
      <div class="panel" style="grid-column:4;grid-row:1;background:#eceef0;"><div class="ph" style="background:#1a0a2e;">ROI</div><div class="gp" style="background:#eceef0;" id="cf-roi"></div></div>
      <div class="panel" style="grid-column:4;grid-row:2;background:#eceef0;"><div class="ph" style="background:#1a0a2e;">FREE CASH</div><div class="gp" style="background:#eceef0;" id="cf-expenses"></div></div>
      <div class="panel" style="grid-column:4;grid-row:3;background:#eceef0;"><div class="ph" style="background:#1a0a2e;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Investments &amp; Liabilities</div><div class="gp" style="background:#eceef0;" id="cf-currentliabilities"></div></div>
    </div>
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
        <div class="panel"><div class="ph">Resource Capacity</div><div class="pb" id="pd-g7"><span style="font-size:9px;color:#aaa">Loading&hellip;</span></div></div>
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


<!-- ── Add IOWs popup — simplified WBS entry (the old qe-modal form has
     been removed; this is the only WBS/activity entry point now) ──────
     Creates bare IOWs (workgroups_new rows) only, no activity/estimate/
     schedule bar. Adding an activity under an IOW is a separate, later
     step. Triggered by #iow-btn-test. -->
<style>
#iow-bk{
  display:none;position:fixed;inset:0;background:transparent;z-index:200700;
}
#iow-bk.iow-open{display:block}
#iow-modal{
  display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
  width:950px;height:560px;max-width:97vw;max-height:92vh;min-width:500px;min-height:300px;
  background:#fff;border-radius:8px;
  box-shadow:0 8px 32px rgba(0,0,0,.35);z-index:200701;flex-direction:column;overflow:hidden;
  font-family:'Nunito',sans-serif;
}
#iow-modal.iow-open{display:flex}
.iow-rs{position:absolute;z-index:10;background:transparent;}
.iow-rs-e {right:0;top:6px;bottom:6px;width:6px;cursor:e-resize;}
.iow-rs-w {left:0;top:6px;bottom:6px;width:6px;cursor:w-resize;}
.iow-rs-s {bottom:0;left:6px;right:6px;height:6px;cursor:s-resize;}
.iow-rs-n {top:0;left:6px;right:6px;height:6px;cursor:n-resize;}
.iow-rs-se{right:0;bottom:0;width:14px;height:14px;cursor:se-resize;}
.iow-rs-sw{left:0;bottom:0;width:14px;height:14px;cursor:sw-resize;}
.iow-rs-ne{right:0;top:0;width:14px;height:14px;cursor:ne-resize;}
.iow-rs-nw{left:0;top:0;width:14px;height:14px;cursor:nw-resize;}
#iow-hdr{
  background:#fff;color:#333;padding:8px 14px;display:flex;justify-content:space-between;
  align-items:center;font-family:'Nunito',sans-serif;font-size:15px;font-weight:700;
  border-bottom:2px solid #b0b4b9;flex-shrink:0;cursor:move;
}
#iow-hdr-title-suffix{font-size:13px;font-weight:700;color:#00838f;margin-left:8px;}
#iow-close{background:none;border:none;color:#333;font-size:20px;line-height:1;cursor:pointer;padding:0 4px;opacity:.6;}
#iow-close:hover{opacity:1;}
#iow-body{padding:14px 18px;overflow-y:auto;flex:1;display:flex;flex-direction:column;}
#iow-body .qe-label{display:block;font-size:13px;font-weight:700;color:#4a5568;text-transform:uppercase;margin-bottom:6px;}

/* ── Tabs — Tab 1 "Add IOW" (batch-create IOWs under one Group) and
     Tab 2 "Add Activity" (pick an existing IOW, attach activities to
     it). Independent panes/state; switching tabs never loses either
     pane's in-progress entry. ── */
#iow-tabs{display:flex;background:#f2f3f4;flex-shrink:0;border-bottom:1px solid #ddd;}
.iow-tab{
  flex:1;background:#f2f3f4;border:none;padding:11px 10px;font-size:13px;font-weight:700;color:#777;cursor:pointer;
  text-transform:uppercase;letter-spacing:.3px;border-bottom:3px solid transparent;
}
.iow-tab:hover{background:#e6e8ea;}
.iow-tab.active{background:#f2f3f4;color:#333;border-bottom-color:#00838f;}
.iow-tab-pane{display:none;flex-direction:column;flex:1;min-height:0;}
.iow-tab-pane.active{display:flex;}

/* ── Tab 3 "Upload BOQ" ── */
#iow-tab-boq{padding:14px 16px;overflow-y:auto;}
#boq-dropzone{
  display:inline-flex; align-items:center; gap:10px; max-width:320px;
  border:2px dashed #b7c0cc; border-radius:8px; background:#fff;
  padding:10px 14px; text-align:left; cursor:pointer; transition:border-color .15s,background .15s; flex-shrink:0;
}
#boq-dropzone:hover, #boq-dropzone.boq-drag-over{ border-color:#00838f; background:#eef1f4; }
#boq-dropzone .boq-dz-icon{ font-size:18px; color:#8a94a0; flex-shrink:0; }
#boq-dropzone .boq-dz-copy{ min-width:0; }
#boq-dropzone .boq-dz-text{ font-size:12.5px; color:#4a5560; font-weight:600; }
#boq-dropzone .boq-dz-sub{ font-size:10.5px; color:#94a3b8; margin-top:1px; }
#boq-file-input{ display:none; }
#boq-selected-file{
  display:none; align-items:center; gap:10px; margin-top:10px;
  background:#eef1f4; border:1px solid #d7dee6; border-radius:8px; padding:9px 12px; flex-shrink:0;
}
#boq-selected-file.show{ display:flex; }
#boq-selected-file .boq-sf-icon{ font-size:16px; color:#37475a; }
#boq-selected-file .boq-sf-name{ flex:1; min-width:0; font-size:12.5px; font-weight:600; color:#2d333b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
#boq-selected-file .boq-sf-remove{ background:none; border:none; color:#c0392b; font-size:16px; cursor:pointer; padding:0 4px; }
#boq-upload-btn{
  margin-top:10px; width:100%; padding:9px; border:none; border-radius:6px; flex-shrink:0;
  background:#0a1f44; color:#fff; font-size:13px; font-weight:700; cursor:pointer;
}
#boq-upload-btn:hover{ background:#061530; }
#boq-upload-btn:disabled{ opacity:.5; cursor:not-allowed; }
#boq-list-hdr{ font-size:11px; font-weight:700; color:#8a94a0; text-transform:uppercase; letter-spacing:.5px; margin:18px 0 8px; flex-shrink:0; }
#boq-list{ display:flex; flex-direction:column; gap:6px; }
.boq-file-row{ display:flex; align-items:center; gap:10px; background:#fff; border:1px solid #e5e7ea; border-radius:8px; padding:8px 12px; }
.boq-file-row .boq-fr-icon{ font-size:15px; color:#37475a; flex-shrink:0; }
.boq-file-row .boq-fr-info{ flex:1; min-width:0; }
.boq-file-row .boq-fr-name{ font-size:12.5px; font-weight:600; color:#2d333b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.boq-file-row .boq-fr-date{ font-size:10.5px; color:#94a3b8; margin-top:2px; }
.boq-file-row .boq-fr-del{
  background:#e57373; color:#fff; border:none; border-radius:50%; width:22px; height:22px;
  font-size:11px; cursor:pointer; flex-shrink:0;
}
#boq-empty{ color:#aaa; font-size:12.5px; text-align:center; padding:10px 0; }
#boq-action-row{ display:flex; gap:10px; margin-top:18px; flex-shrink:0; }
.boq-action-btn{
  flex:1; padding:10px; border:none; border-radius:6px; font-size:12.5px; font-weight:700;
  cursor:pointer; text-transform:uppercase; letter-spacing:.3px;
}
.boq-action-btn:disabled{ opacity:.5; cursor:not-allowed; }
#boq-create-wbs-btn{ background:#37475a; color:#fff; }
#boq-create-wbs-btn:hover:not(:disabled){ background:#28323f; }
#boq-create-sched-btn{ background:#27ae60; color:#fff; }
#boq-create-sched-btn:hover:not(:disabled){ background:#1e8449; }

#iow-group-select{width:100%;height:32px;border:1px solid #ccc;border-radius:4px;padding:0 10px;font-size:13px;}
#iow-rows{display:flex;flex-direction:column;gap:6px;margin:8px 0 8px;}
.iow-row{display:flex;gap:10px;align-items:center;}
.iow-row input{flex:1;height:32px;border:1px solid #ccc;border-radius:4px;padding:0 10px;font-size:13px;box-sizing:border-box;}
.iow-row-add{
  background:#00838f;color:#fff;border:none;border-radius:50%;width:30px;height:30px;flex-shrink:0;
  font-size:18px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;
}
.iow-row-add:hover{background:#005f6b}
.iow-row-remove{
  background:none;border:none;color:#c0392b;font-size:20px;cursor:pointer;width:26px;flex-shrink:0;
}
#iow-add-msg{font-size:12px;font-weight:700;min-height:16px;}
#iow-btn-addiow{
  background:#27ae60;color:#fff;border:none;border-radius:999px;padding:8px 28px;font-size:13px;
  font-weight:700;cursor:pointer;letter-spacing:.3px;text-transform:uppercase;
}
#iow-btn-addiow:hover{background:#1e8449}
#iow-btn-addiow:disabled{background:#aaa;cursor:default}

/* Tab 2 top row — Group + IOW dropdowns side by side, above the
   always-visible activity list. */
#ap-top-row{display:flex;gap:12px;align-items:flex-end;margin-bottom:8px;flex-shrink:0;}
#ap-top-row .iow-field{flex:1;}
#ap-top-row .qe-label{margin-bottom:6px;}
#ap-group-select, #ap-iow-select{width:100%;height:32px;border:1px solid #ccc;border-radius:4px;padding:0 10px;font-size:13px;box-sizing:border-box;}
#ap-select-msg{font-size:12px;font-weight:700;color:#c0392b;margin:-6px 0 12px;min-height:16px;flex-shrink:0;}

#iow-footer{
  background:#fff;border-top:1px solid #ddd;padding:10px 16px;display:flex;align-items:center;
  gap:12px;flex-shrink:0;
}
#iow-footer-spacer{flex:1;}
#iow-btn-cancel{
  background:#fff;color:#e67e22;border:1px solid #e67e22;border-radius:999px;padding:7px 24px;font-size:13px;
  font-weight:700;cursor:pointer;letter-spacing:.3px;text-transform:uppercase;
}
#iow-btn-close{
  background:#e67e22;color:#fff;border:none;border-radius:999px;padding:8px 24px;font-size:13px;
  font-weight:700;cursor:pointer;letter-spacing:.3px;text-transform:uppercase;
}

/* ── Activity list — always visible on Tab 2, full list on open, further
     filtered as Group/IOW are picked. Same ap-* ids/classes as before so
     all existing list-loading/Task-popup/tick-to-add JS keeps working
     unchanged. ── */
#ap-toolbar{padding:0 0 10px;display:flex;gap:6px;flex-shrink:0;align-items:center;}
#ap-search{
  height:34px;width:280px;border-radius:20px;padding:0 12px;font-size:13px;
  border:1px solid #ccc;font-family:'Nunito',sans-serif;
}
#ap-body{overflow-y:auto;overflow-x:hidden;flex:1;}
#ap-body table{table-layout:fixed;width:100%;border-collapse:collapse;}
#ap-body thead{position:sticky;top:0;z-index:1;}
#ap-body th, #ap-body td{white-space:normal;overflow:hidden;text-overflow:ellipsis;word-break:break-word;}
#ap-body th:nth-child(1), #ap-body td:nth-child(1){width:5%;}
#ap-body th:nth-child(2), #ap-body td:nth-child(2){width:14%;}
#ap-body th:nth-child(3), #ap-body td:nth-child(3){width:9%;}
#ap-body th:nth-child(4), #ap-body td:nth-child(4){width:auto;padding-right:8px;}
#ap-body th:nth-child(5), #ap-body td:nth-child(5){width:10%;}
#ap-body th:nth-child(6), #ap-body td:nth-child(6){width:15%;}
#ap-body th:nth-child(7), #ap-body td:nth-child(7){width:9%;}
#ap-body th:nth-child(8), #ap-body td:nth-child(8){width:7%;}
/* Strips the boxed-grid look this table gets from listestactivity()'s
   inline styles (shared with the real Activity Library page) — this
   popup gets a clean, borderless look with just a row separator and a
   plain header, without touching the real page's own markup/endpoint. */
#ap-body table{border:none !important;}
#ap-body th{background:#555 !important;color:#fff !important;border:none !important;padding:3px 6px !important;font-weight:600 !important;font-size:11px !important;line-height:1 !important;}
#ap-body td{border:none !important;border-bottom:1px solid #888 !important;}
#ap-body tbody tr:last-child td{border-bottom:none !important;}
#ap-body .ap-col-schunit input{width:100%;height:28px;border:1px solid #ccc;border-radius:4px;padding:0 6px;font-size:12px;box-sizing:border-box;}
#ap-body .ap-col-qty input{width:100%;height:28px;border:1px solid #ccc;border-radius:4px;padding:0 4px;font-size:12px;box-sizing:border-box;-moz-appearance:textfield;}
#ap-body .ap-col-qty input::-webkit-outer-spin-button,
#ap-body .ap-col-qty input::-webkit-inner-spin-button{-webkit-appearance:none;margin:0;}
.ap-task-btn{
  background:#888;border:1px solid #777;color:#fff;border-radius:4px;padding:4px 6px;
  font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;max-width:100%;
}
.ap-task-btn.ap-has-tasks{background:#6c7a89;border-color:#5a6672;color:#fff;}
.ap-tick-btn{
  background:#27ae60;color:#fff;border:none;border-radius:50%;width:28px;height:28px;
  font-size:14px;cursor:pointer;flex-shrink:0;
}
.ap-tick-btn:hover{background:#1e8449}
.ap-tick-btn.ap-added{background:#2878c0;cursor:default;}
.ap-tick-btn.ap-added:hover{background:#2878c0;}
</style>

<div id="iow-bk"></div>
<div id="iow-modal">
  <div class="iow-rs iow-rs-e"  data-dir="e"></div>
  <div class="iow-rs iow-rs-w"  data-dir="w"></div>
  <div class="iow-rs iow-rs-s"  data-dir="s"></div>
  <div class="iow-rs iow-rs-n"  data-dir="n"></div>
  <div class="iow-rs iow-rs-se" data-dir="se"></div>
  <div class="iow-rs iow-rs-sw" data-dir="sw"></div>
  <div class="iow-rs iow-rs-ne" data-dir="ne"></div>
  <div class="iow-rs iow-rs-nw" data-dir="nw"></div>
  <div id="iow-hdr">
    <span id="iow-hdr-title">Add IOWs &amp; Activities</span><span id="iow-hdr-title-suffix"></span>
    <button id="iow-close">&times;</button>
  </div>
  <div id="iow-tabs">
    <button type="button" class="iow-tab iow-tab-addiow active" data-tab="addiow">Add IOW</button>
    <button type="button" class="iow-tab iow-tab-addactivity" data-tab="addactivity">Add Activity</button>
    <button type="button" class="iow-tab iow-tab-boq" data-tab="boq">Upload BOQ</button>
  </div>
  <div id="iow-body">
    <!-- Tab 1: Add IOW — batch-create any number of IOWs under one IOW
         Group. Independent of Tab 2; this pane never auto-switches away
         after saving, so the user can keep adding more IOWs or flip to
         Tab 2 to attach activities to what they just created. -->
    <div class="iow-tab-pane active" id="iow-tab-addiow">
      <span class="qe-label">IOW Group</span>
      <select id="iow-group-select">
        <option value="">— Select IOW Group —</option>
      </select>
      <span class="qe-label" style="margin-top:8px;">IOWs</span>
      <div id="iow-rows">
        <div class="iow-row">
          <input type="text" class="iow-name-input" placeholder="Enter IOW name">
          <button type="button" class="iow-row-add" title="Add another IOW">&#43;</button>
          <button type="button" class="iow-row-remove" title="Remove">&times;</button>
        </div>
      </div>
      <div id="iow-add-msg"></div>
    </div>

    <!-- Tab 2: Add Activity — pick an existing IOW (via Group, then IOW
         dropdown), attach activities to it from the always-visible list
         below. The list shows ALL activities immediately on open/tab-
         switch, filtering further once a Group is picked. Ticking before
         an IOW is selected is blocked with #ap-select-msg. -->
    <div class="iow-tab-pane" id="iow-tab-addactivity">
      <div id="ap-top-row">
        <div class="iow-field">
          <span class="qe-label">IOW Group</span>
          <select id="ap-group-select">
            <option value="">— All Groups —</option>
          </select>
        </div>
        <div class="iow-field">
          <span class="qe-label">IOW</span>
          <select id="ap-iow-select">
            <option value="">— Select IOW —</option>
          </select>
        </div>
      </div>
      <div id="ap-select-msg"></div>
      <div id="ap-toolbar">
        <input type="text" id="ap-search" placeholder="Search...">
      </div>
      <div id="ap-body">
        <div id="ap-list-target"><p style="color:#aaa;font-size:13px;text-align:center;padding:30px 0;">Loading…</p></div>
      </div>
    </div>

    <!-- Tab 3: Upload BOQ — a simple file store for now (Excel/PDF), one
         BOQ list per project. What happens with an uploaded BOQ (e.g.
         auto-creating IOWs/Activities from it) is intentionally not wired
         up yet, pending scope. -->
    <div class="iow-tab-pane" id="iow-tab-boq">
      <div id="boq-dropzone">
        <div class="boq-dz-icon">&#x2B06;</div>
        <div class="boq-dz-copy">
          <div class="boq-dz-text">Click or drag file here</div>
          <div class="boq-dz-sub">Excel (.xlsx, .xls) or PDF</div>
        </div>
      </div>
      <input type="file" id="boq-file-input" accept=".xlsx,.xls,.pdf">

      <div id="boq-selected-file">
        <span class="boq-sf-icon">&#x1F4CE;</span>
        <span class="boq-sf-name" id="boq-sf-name"></span>
        <button type="button" class="boq-sf-remove" id="boq-sf-remove" title="Remove">&times;</button>
      </div>

      <button type="button" id="boq-upload-btn" disabled>Upload BOQ</button>

      <div id="boq-list-hdr">Uploaded BOQs</div>
      <div id="boq-list"><p id="boq-empty">Loading&hellip;</p></div>

      <div id="boq-action-row">
        <button type="button" id="boq-create-wbs-btn" class="boq-action-btn" disabled>Create WBS</button>
        <button type="button" id="boq-create-sched-btn" class="boq-action-btn" disabled>Create Schedule and Estimate</button>
      </div>
    </div>
  </div>
  <div id="iow-footer">
    <button type="button" id="iow-btn-addiow">Add</button>
    <span id="iow-footer-spacer"></span>
    <button id="iow-btn-cancel">Cancel</button>
    <button id="iow-btn-close">Close</button>
  </div>
</div>

<script>
(function(){
  // Populates a <select> with the project's IOW Groups. Used by both
  // Tab 1 (#iow-group-select, required, no "All" option) and Tab 2
  // (#ap-group-select, optional filter, keeps its "All Groups" option).
  window.loadIowGroupOptions = function(selectEl, cb){
    var placeholder = selectEl.options[0] ? selectEl.options[0].outerHTML : '<option value="">— Select IOW Group —</option>';
    $.ajax({
      type:'POST', url:'../projects/getiowgrouplist', dataType:'json',
      success: function(d){
        selectEl.innerHTML = placeholder;
        (d.items||[]).forEach(function(item){
          var o = document.createElement('option');
          o.value = item.id; o.textContent = item.name;
          selectEl.appendChild(o);
        });
        if(typeof cb === 'function') cb();
      }
    });
  };

  var IOW_MODAL_H = 560;

  function iowRowHtml(){
    return '<input type="text" class="iow-name-input" placeholder="Enter IOW name">'
      + '<button type="button" class="iow-row-add" title="Add another IOW">&#43;</button>'
      + '<button type="button" class="iow-row-remove" title="Remove">&times;</button>';
  }

  function addIowRow(afterEl){
    var wrap = document.createElement('div');
    wrap.className = 'iow-row';
    wrap.innerHTML = iowRowHtml();
    if(afterEl && afterEl.nextSibling) afterEl.parentNode.insertBefore(wrap, afterEl.nextSibling);
    else document.getElementById('iow-rows').appendChild(wrap);
    wrap.querySelector('input').focus();
  }

  document.getElementById('iow-rows').addEventListener('click', function(e){
    if(e.target.classList.contains('iow-row-add')){
      addIowRow(e.target.closest('.iow-row'));
    } else if(e.target.classList.contains('iow-row-remove')){
      var rows = document.querySelectorAll('#iow-rows .iow-row');
      if(rows.length > 1){ e.target.closest('.iow-row').remove(); }
      else e.target.closest('.iow-row').querySelector('input').value = '';
    }
  });

  // Tab switching — both panes keep their state when hidden (no reset on
  // switch), so in-progress entry on either tab survives flipping back
  // and forth. Tab 2 lazy-loads its group list + full activity list only
  // the first time it's opened in this popup session.
  var _ap2Loaded = false;

  // The footer's Add button only applies to Tab 1 (batch-saves the typed
  // IOW rows) — Tab 2 has no equivalent bulk action, activities are added
  // one at a time via each row's own tick, so it's hidden there.
  function eamSyncAddBtnVisibility(activeTab){
    document.getElementById('iow-btn-addiow').style.display = (activeTab === 'addiow') ? '' : 'none';
  }

  document.querySelectorAll('.iow-tab').forEach(function(tabBtn){
    tabBtn.addEventListener('click', function(){
      document.querySelectorAll('.iow-tab').forEach(function(b){ b.classList.remove('active'); });
      document.querySelectorAll('.iow-tab-pane').forEach(function(p){ p.classList.remove('active'); });
      tabBtn.classList.add('active');
      var tabName = tabBtn.getAttribute('data-tab');
      document.getElementById('iow-tab-' + tabName).classList.add('active');
      eamSyncAddBtnVisibility(tabName);
      if(tabName === 'addactivity' && !_ap2Loaded){
        _ap2Loaded = true;
        window.loadIowGroupOptions(document.getElementById('ap-group-select'));
        window.loadActivityList();
      }
      if(tabName === 'boq' && typeof window.loadBoqList === 'function'){
        window.loadBoqList();
      }
    });
  });

  // Resets #iow-modal fully back to a fresh, empty state — both tabs'
  // fields cleared, Tab 1 active. Called every time the popup is opened
  // fresh via the toolbar icon, so a previous session never leaks in.
  window.resetIowModalToCreateMode = function(){
    document.getElementById('iow-hdr-title').textContent = 'Add IOWs & Activities';
    document.getElementById('iow-hdr-title-suffix').textContent = '';
    document.getElementById('iow-modal').style.height = IOW_MODAL_H + 'px';
    document.getElementById('iow-modal').style.width = '';

    document.getElementById('iow-group-select').value = '';
    document.getElementById('iow-rows').innerHTML = '<div class="iow-row">' + iowRowHtml() + '</div>';
    document.getElementById('iow-add-msg').textContent = '';
    document.getElementById('iow-btn-addiow').disabled = false;
    document.getElementById('iow-btn-addiow').textContent = 'Add';

    document.getElementById('ap-group-select').value = '';
    document.getElementById('ap-iow-select').innerHTML = '<option value="">— Select IOW —</option>';
    document.getElementById('ap-select-msg').textContent = '';
    window._apCurrentWgId = null; window._apCurrentIowName = ''; window._apCurrentIowGroupId = 0;
    _ap2Loaded = false;

    document.querySelectorAll('.iow-tab').forEach(function(b){ b.classList.remove('active'); });
    document.querySelectorAll('.iow-tab-pane').forEach(function(p){ p.classList.remove('active'); });
    document.querySelector('.iow-tab[data-tab="addiow"]').classList.add('active');
    document.getElementById('iow-tab-addiow').classList.add('active');
    eamSyncAddBtnVisibility('addiow');

    _apReloadPendingFromCreate = false;
  }

  window.openIowModal = function(){
    window.resetIowModalToCreateMode();
    window.loadIowGroupOptions(document.getElementById('iow-group-select'));
    document.getElementById('iow-bk').classList.add('iow-open');
    document.getElementById('iow-modal').classList.add('iow-open');
  };

  // Toolbar icon ("Add IOWs, Activities & BOQ", #iow-btn-test) — the icon
  // itself and its title/comment referencing it have existed since this
  // modal was built, but no click handler was ever actually attached
  // anywhere in the codebase, so the icon has never done anything. This id
  // is duplicated (3 copies, one per nav-menu variant rendered
  // conditionally by user role), so this binds via delegation on
  // `document` matching the id, rather than getElementById on a single
  // instance, so it fires no matter which of the three copies is actually
  // present/visible for this user.
  document.addEventListener('click', function(e){
    var btn = e.target.closest('#iow-btn-test');
    if (!btn) return;
    e.preventDefault();
    window.openIowModal();
  });

  function closeIowModal(){
    document.getElementById('iow-bk').classList.remove('iow-open');
    document.getElementById('iow-modal').classList.remove('iow-open');
    if(window._apReloadPending || _apReloadPendingFromCreate){
      window._apReloadPending = false;
      _apReloadPendingFromCreate = false;
      if(typeof window.reloadGantt === 'function') window.reloadGantt();
    }
  }
  // Close: just exits, keeping whatever was already saved (Add already
  // wrote it to the server — nothing to "undo" on close).
  document.getElementById('iow-close').addEventListener('click', closeIowModal);
  document.getElementById('iow-btn-close').addEventListener('click', closeIowModal);
  document.getElementById('iow-bk').addEventListener('click', closeIowModal);
  // Cancel: discards whatever's currently typed in Tab 1's IOW name
  // row(s) (back to one blank row) before closing — same as Close
  // otherwise, since a completed Add can't be "undone" here either.
  document.getElementById('iow-btn-cancel').addEventListener('click', function(){
    document.getElementById('iow-rows').innerHTML = '<div class="iow-row">' + iowRowHtml() + '</div>';
    document.getElementById('iow-add-msg').textContent = '';
    closeIowModal();
  });

  // Drag + resize for the combined window — same pattern as the KPI
  // float panel. Bound once here (not duplicated per mode) since both
  // create and edit mode share the same #iow-modal element.
  (function(){
    var win = document.getElementById('iow-modal');
    var hdr = document.getElementById('iow-hdr');
    var MIN_W=500, MIN_H=300, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0;
    function _anchor(){
      var r=win.getBoundingClientRect();
      win.style.left=r.left+'px'; win.style.top=r.top+'px';
      win.style.width=r.width+'px'; win.style.height=r.height+'px';
      win.style.transform='none';
      return r;
    }
    bindDragTouch(hdr, 'mousedown', function(e){
      if(e.target.id==='iow-close') return;
      var r=_anchor(); _action='drag'; _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top;
      e.preventDefault();
    });
    document.querySelectorAll('.iow-rs').forEach(function(el){
      bindDragTouch(el, 'mousedown', function(e){
        var r=_anchor(); _action=el.getAttribute('data-dir');
        _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height;
        e.preventDefault(); e.stopPropagation();
      });
    });
    bindDragTouch(document, 'mousemove', function(e){
      if(!_action) return;
      var dx=e.clientX-_sx, dy=e.clientY-_sy;
      if(_action==='drag'){
        win.style.left=Math.max(0,_ox+dx)+'px'; win.style.top=Math.max(0,_oy+dy)+'px';
      } else {
        var l=_ox,t=_oy,w=_ow,h=_oh;
        if(_action.indexOf('e')>-1){ w=Math.max(MIN_W,_ow+dx); }
        if(_action.indexOf('s')>-1){ h=Math.max(MIN_H,_oh+dy); }
        if(_action.indexOf('w')>-1){ var nw=Math.max(MIN_W,_ow-dx); l=_ox+(_ow-nw); w=nw; }
        if(_action.indexOf('n')>-1){ var nh=Math.max(MIN_H,_oh-dy); t=_oy+(_oh-nh); h=nh; }
        win.style.left=l+'px'; win.style.top=t+'px'; win.style.width=w+'px'; win.style.height=h+'px';
      }
    }, { passive: false });
    bindDragTouch(document, 'mouseup', function(){ _action=null; });
  })();

  var _apReloadPendingFromCreate = false; // no longer set true anywhere — Add IOW now reloads the Gantt instantly on click instead of deferring to Close. Kept (always false) so closeIowModal()'s check stays harmless.

  // Tab 1 — batch-create any number of IOWs (one row each) under a
  // single selected Group. Stays on Tab 1 after saving (no auto-switch);
  // the rows are cleared back to one blank row so more can be added
  // immediately, and a confirmation message lists what was created.
  document.getElementById('iow-btn-addiow').addEventListener('click', function(){
    var groupSel = document.getElementById('iow-group-select');
    var groupId = groupSel.value;
    var groupName = groupSel.options[groupSel.selectedIndex] ? groupSel.options[groupSel.selectedIndex].textContent : '';
    var msg = document.getElementById('iow-add-msg');
    if(!groupId){ msg.style.color = '#c0392b'; msg.textContent = 'Please select an IOW Group.'; return; }

    var names = [];
    document.querySelectorAll('.iow-name-input').forEach(function(inp){
      var v = inp.value.trim();
      if(v) names.push(v);
    });
    if(!names.length){ msg.style.color = '#c0392b'; msg.textContent = 'Enter at least one IOW name.'; return; }

    var btn = document.getElementById('iow-btn-addiow');
    btn.disabled = true; btn.textContent = 'Saving…';
    msg.textContent = '';

    $.ajax({
      type: 'POST',
      url: '../projectsmain/wbsaddiows',
      dataType: 'json',
      data: { payload: JSON.stringify({ iow_group_id: groupId, iow_group_name: groupName, iow_names: names }) },
      success: function(d){
        btn.disabled = false; btn.textContent = 'Add';
        if(d.error && d.error !== 'No'){
          msg.style.color = '#c0392b'; msg.textContent = d.error;
          return;
        }
        var created = d.created || [];
        var skipped = d.skipped || [];
        var parts = [];
        if(created.length) parts.push(created.length + ' IOW' + (created.length > 1 ? 's' : '') + ' added.');
        if(skipped.length)  parts.push(skipped.length + ' already existed.');
        msg.style.color = created.length ? '#1e8449' : '#c0392b';
        msg.textContent = parts.length ? parts.join(' ') : 'Nothing was added.';
        document.getElementById('iow-rows').innerHTML = '<div class="iow-row">' + iowRowHtml() + '</div>';
        // Applies to the Gantt instantly (not deferred to Close) — same
        // accepted flicker tradeoff as the Edit Activities popup's
        // edit/delete, so a new IOW is never in doubt.
        if(created.length && typeof window.reloadGantt === 'function') window.reloadGantt();
        // If Tab 2 already loaded its IOW-by-group list this session and
        // is currently scoped to the same Group just added to, refresh
        // it so the new IOW(s) show up in the picker without reopening.
        if(_ap2Loaded && document.getElementById('ap-group-select').value === groupId){
          window.loadApIowOptions(groupId);
          window.loadActivityList();
        }
      },
      error: function(){
        btn.disabled = false; btn.textContent = 'Add';
        msg.style.color = '#c0392b'; msg.textContent = 'Save failed. Please try again.';
      }
    });
  });

  // Tab 2 — Group filters the IOW dropdown (and re-filters the activity
  // list, via iow_group_id already supported by loadActivityList/
  // listestactivity); IOW selects the actual attach target.
  window.loadApIowOptions = function(groupId, cb){
    var sel = document.getElementById('ap-iow-select');
    sel.innerHTML = '<option value="">— Select IOW —</option>';
    $.ajax({
      type: 'POST', url: '../projectsmain/listiowbygroup', dataType: 'json',
      data: { iow_group_id: groupId || 0 },
      success: function(d){
        (d.items || []).forEach(function(item){
          var o = document.createElement('option');
          o.value = item.id; o.textContent = item.name;
          o.setAttribute('data-group', item.iow_group_id);
          sel.appendChild(o);
        });
        if(typeof cb === 'function') cb();
      }
    });
  };

  document.getElementById('ap-group-select').addEventListener('change', function(){
    var groupId = this.value;
    window.loadApIowOptions(groupId);
    window._apCurrentWgId = null; window._apCurrentIowName = ''; window._apCurrentIowGroupId = groupId ? parseInt(groupId, 10) : 0;
    document.getElementById('ap-select-msg').textContent = '';
    window.loadActivityList();
  });

  document.getElementById('ap-iow-select').addEventListener('change', function(){
    var opt = this.options[this.selectedIndex];
    if(!this.value){
      window._apCurrentWgId = null; window._apCurrentIowName = '';
      return;
    }
    window._apCurrentWgId = parseInt(this.value, 10);
    window._apCurrentIowName = opt.textContent;
    document.getElementById('ap-select-msg').textContent = '';
  });

  /* ── Tab 3: Upload BOQ ── */
  var _boqSelectedFile = null;
  var $boqDropzone = $('#boq-dropzone');
  var $boqFileInput = $('#boq-file-input');
  var $boqSelectedFileRow = $('#boq-selected-file');
  var $boqUploadBtn = $('#boq-upload-btn');
  var BOQ_ALLOWED_EXT = ['xlsx', 'xls', 'pdf'];

  function boqExtOf(name){
    var parts = (name || '').split('.');
    return parts.length > 1 ? parts.pop().toLowerCase() : '';
  }

  function boqSetSelectedFile(file){
    if (!file) { _boqSelectedFile = null; $boqSelectedFileRow.removeClass('show'); $boqUploadBtn.prop('disabled', true); return; }
    if (BOQ_ALLOWED_EXT.indexOf(boqExtOf(file.name)) === -1) {
      alert('Only Excel (.xlsx, .xls) or PDF files are allowed.');
      return;
    }
    _boqSelectedFile = file;
    $('#boq-sf-name').text(file.name);
    $boqSelectedFileRow.addClass('show');
    $boqUploadBtn.prop('disabled', false);
  }

  $boqDropzone.on('click', function(){ $boqFileInput.trigger('click'); });
  $boqFileInput.on('change', function(){
    if (this.files && this.files[0]) boqSetSelectedFile(this.files[0]);
  });
  $boqDropzone.on('dragover', function(e){ e.preventDefault(); e.stopPropagation(); $boqDropzone.addClass('boq-drag-over'); });
  $boqDropzone.on('dragleave', function(e){ e.preventDefault(); e.stopPropagation(); $boqDropzone.removeClass('boq-drag-over'); });
  $boqDropzone.on('drop', function(e){
    e.preventDefault(); e.stopPropagation();
    $boqDropzone.removeClass('boq-drag-over');
    var files = e.originalEvent.dataTransfer ? e.originalEvent.dataTransfer.files : null;
    if (files && files[0]) boqSetSelectedFile(files[0]);
  });
  $('#boq-sf-remove').on('click', function(e){
    e.stopPropagation();
    boqSetSelectedFile(null);
    $boqFileInput.val('');
  });

  $boqUploadBtn.on('click', function(){
    var pid = (document.getElementById('gantt-win-open') || {}).getAttribute
      ? document.getElementById('gantt-win-open').getAttribute('data-projectid') : '';
    if (!pid) { alert('No project selected.'); return; }
    if (!_boqSelectedFile) return;

    var fd = new FormData();
    fd.append('project_id', pid);
    fd.append('boq_file', _boqSelectedFile);

    $boqUploadBtn.prop('disabled', true).text('Uploading…');
    $.ajax({
      type: 'POST', url: '../projectsmain/uploadboq', data: fd,
      processData: false, contentType: false, dataType: 'json',
      success: function(d){
        $boqUploadBtn.prop('disabled', false).text('Upload BOQ');
        if (d.error && d.error !== 'No') { alert(d.errortext || 'Upload failed.'); return; }
        boqSetSelectedFile(null);
        $boqFileInput.val('');
        window.loadBoqList();
      },
      error: function(){
        $boqUploadBtn.prop('disabled', false).text('Upload BOQ');
        alert('Upload failed. Please try again.');
      }
    });
  });

  function boqFmtDate(s){
    if (!s) return '';
    var d = new Date(s.replace(' ', 'T'));
    if (isNaN(d.getTime())) return s;
    return d.toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' }) + ' ' +
           d.toLocaleTimeString('en-IN', { hour:'2-digit', minute:'2-digit' });
  }

  window.loadBoqList = function(){
    var pid = (document.getElementById('gantt-win-open') || {}).getAttribute
      ? document.getElementById('gantt-win-open').getAttribute('data-projectid') : '';
    if (!pid) { $('#boq-list').html('<p id="boq-empty">No project selected.</p>'); return; }
    $.ajax({
      type: 'POST', url: '../projectsmain/listboq', dataType: 'json', data: { project_id: pid },
      success: function(d){
        if (d.error && d.error !== 'No') { $('#boq-list').html('<p id="boq-empty">' + $('<span>').text(d.errortext || 'Error loading files.').html() + '</p>'); return; }
        boqRenderList(d.rows || []);
      },
      error: function(){ $('#boq-list').html('<p id="boq-empty">Failed to load.</p>'); }
    });
  };

  function boqRenderList(rows){
    document.getElementById('boq-create-wbs-btn').disabled = !rows.length;
    document.getElementById('boq-create-sched-btn').disabled = !rows.length;
    if (!rows.length) { $('#boq-list').html('<p id="boq-empty">No BOQ uploaded yet.</p>'); return; }
    var html = '';
    rows.forEach(function(r){
      html += '<div class="boq-file-row" data-id="' + r.id + '">'
        + '<span class="boq-fr-icon">&#x1F4C4;</span>'
        + '<div class="boq-fr-info">'
        +   '<div class="boq-fr-name">' + $('<span>').text(r.original_name).html() + '</div>'
        +   '<div class="boq-fr-date">' + $('<span>').text(boqFmtDate(r.uploaded_at)).html() + '</div>'
        + '</div>'
        + '<button type="button" class="boq-fr-del" data-id="' + r.id + '" title="Delete">&times;</button>'
        + '</div>';
    });
    $('#boq-list').html(html);
  }

  $(document).on('click', '.boq-fr-del', function(){
    if (!confirm('Delete this BOQ file?')) return;
    var id = $(this).data('id');
    $.ajax({
      type: 'POST', url: '../projectsmain/deleteboq', dataType: 'json', data: { id: id },
      success: function(d){
        if (d.error && d.error !== 'No') { alert(d.errortext || 'Delete failed.'); return; }
        window.loadBoqList();
      },
      error: function(){ alert('Delete failed. Please try again.'); }
    });
  });

  // Placeholder actions — behavior not yet defined, wire up once scoped.
  document.getElementById('boq-create-wbs-btn').addEventListener('click', function(){
    alert('Create WBS — coming soon.');
  });
  document.getElementById('boq-create-sched-btn').addEventListener('click', function(){
    alert('Create Schedule and Estimate — coming soon.');
  });
})();
</script>

<!-- Task sub-popup — reuses the same Task table columns/duration-per-unit
     formula as the old WBS Quick Entry form (name, qty, productivity,
     duration/unit, resource units). Prefilled from the picked activity's
     library default tasks (activity_tasks, via getactivityresources). -->
<style>
#apt-bk{display:none;position:fixed;inset:0;background:transparent;z-index:200900;}
#apt-bk.apt-open{display:block}
#apt-modal{
  display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
  width:640px;max-width:92vw;max-height:80vh;background:#fff;border-radius:8px;
  box-shadow:0 12px 40px rgba(0,0,0,.35);z-index:200901;flex-direction:column;overflow:hidden;
  font-family:'Nunito',sans-serif;
}
#apt-modal.apt-open{display:flex}
#apt-hdr{
  background:#5a6470;color:#fff;padding:12px 20px;display:flex;justify-content:space-between;
  align-items:center;font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:700;
  text-transform:uppercase;flex-shrink:0;
}
#apt-close{background:none;border:none;color:#fff;font-size:22px;cursor:pointer;line-height:1}
#apt-body{padding:16px 20px;overflow-y:auto;flex:1;}
#apt-body table{width:100%;border-collapse:collapse;font-size:12px;}
#apt-body th{background:#555;color:#fff;font-weight:600;font-size:11px;padding:5px 6px;border:1px solid #444;}
#apt-body td{padding:4px 6px;border:1px solid #ddd;}
#apt-body input{width:100%;height:26px;border:1px solid #ccc;border-radius:3px;padding:0 5px;font-size:12px;box-sizing:border-box;}
#apt-body input[readonly]{background:#f3f4f6;color:#555;}
.apt-del-btn{background:none;border:none;color:#c0392b;font-size:16px;cursor:pointer;}
#apt-add-row-btn{
  background:none;border:1px dashed #5a6470;color:#5a6470;border-radius:4px;padding:5px 12px;
  font-size:11px;font-weight:700;cursor:pointer;margin-top:8px;
}
#apt-footer{padding:10px 20px;border-top:1px solid #ddd;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;}
#apt-dur-display{font-size:12px;font-weight:700;color:#2d3748;}
#apt-btn-done{
  background:#27ae60;color:#fff;border:none;border-radius:999px;padding:7px 22px;font-size:12px;
  font-weight:700;cursor:pointer;text-transform:uppercase;
}
</style>

<div id="apt-bk"></div>
<div id="apt-modal">
  <div id="apt-hdr">
    <span>Tasks <span id="apt-hdr-act" style="font-weight:400;opacity:.85;text-transform:none;"></span></span>
    <button id="apt-close">&times;</button>
  </div>
  <div id="apt-body">
    <table>
      <thead><tr>
        <th>Task</th><th id="apt-qty-hdr" style="width:70px;">Qnty/Unit</th><th style="width:80px;">Productivity/day</th>
        <th style="width:75px;">Dur/Unit</th><th style="width:75px;">Res Units</th><th style="width:30px;"></th>
      </tr></thead>
      <tbody id="apt-task-body"></tbody>
    </table>
    <button type="button" id="apt-add-row-btn">&#43; Add Task</button>
  </div>
  <div id="apt-footer">
    <div id="apt-dur-display">Duration: <span id="apt-dur-val">—</span></div>
    <button id="apt-btn-done">Done</button>
  </div>
</div>

<script>
(function(){
  window._apCurrentWgId = null;
  window._apCurrentIowName = '';   // selected/clicked IOW's own current name — MUST be resent on every wbsadd call, or actionWbsadd() falls back to renaming the IOW to the activity's name (see the iow_name/iowGroupId bug this replaced)
  var _apActivityUnits = {};  // activity_id -> library unit (prefill for Schd Unit)
  var _apActivityTasks = {};  // activity_id -> [{name, qty, productivity, resunits}], edited copy per activity
  window._apCurrentIowGroupId = 0; // selected/clicked IOW's parent IOW Group — filters the list to activities already tagged with it, and MUST be resent on wbsadd so the IOW's own group is preserved (not reset to 0)

  // Edit mode entry point — called by newganttview.php when an existing
  // IOW row/bar is clicked. Kept as window.openActivityPicker (name
  // unchanged) so the Gantt-side click handler needs no changes. Opens
  // the SAME popup straight onto Tab 2, with that IOW's Group and IOW
  // dropdowns pre-selected — no separate "edit mode" screen, this is
  // just Tab 2 pre-filled to skip the manual pick.
  window.openActivityPicker = function(wgId, iowName, iowGroupId){
    window.resetIowModalToCreateMode(); // start clean, then immediately switch — avoids stale state from a previous session
    document.getElementById('iow-bk').classList.add('iow-open');
    document.getElementById('iow-modal').classList.add('iow-open');

    document.querySelectorAll('.iow-tab').forEach(function(b){ b.classList.remove('active'); });
    document.querySelectorAll('.iow-tab-pane').forEach(function(p){ p.classList.remove('active'); });
    document.querySelector('.iow-tab[data-tab="addactivity"]').classList.add('active');
    document.getElementById('iow-tab-addactivity').classList.add('active');

    window._apCurrentWgId = wgId;
    window._apCurrentIowName = iowName || '';
    window._apCurrentIowGroupId = iowGroupId || 0;
    document.getElementById('iow-hdr-title-suffix').textContent = iowName ? ('— ' + iowName) : '';

    window.loadIowGroupOptions(document.getElementById('ap-group-select'), function(){
      document.getElementById('ap-group-select').value = iowGroupId || '';
      window.loadApIowOptions(iowGroupId, function(){
        document.getElementById('ap-iow-select').value = wgId || '';
      });
    });
    window.loadActivityList();
  };

  window._apReloadPending = false; // no longer set true anywhere — each tick now reloads the Gantt instantly instead of deferring to Close. Kept (always false) so closeIowModal()'s check stays harmless.

  window.loadActivityList = function(){
    var target = document.getElementById('ap-list-target');
    target.innerHTML = '<p style="color:#aaa;font-size:13px;text-align:center;padding:30px 0;">Loading…</p>';
    $.ajax({
      type: 'POST', url: '../projects/listestactivity', dataType: 'json', timeout: 30000,
      data: { estactivityname: document.getElementById('ap-search').value, estactivitytype: '', estworktype: '', iow_group_id: window._apCurrentIowGroupId },
      success: function(d){
        target.innerHTML = d.result || '<p style="color:#aaa;font-size:13px;text-align:center;padding:30px 0;">No activities found.</p>';
        augmentActivityRows();
      },
      error: function(xhr){
        if(xhr && xhr.status === 403){
          target.innerHTML = '<p style="color:#c0392b;font-size:13px;text-align:center;padding:30px 0;">Your session has expired. <a href="#" id="ap-relogin-reload" style="color:#00838f;font-weight:700;">Reload the page</a> and log in again.</p>';
          var reloadBtn = document.getElementById('ap-relogin-reload');
          if(reloadBtn) reloadBtn.addEventListener('click', function(e){ e.preventDefault(); window.location.reload(); });
          return;
        }
        target.innerHTML = '<p style="color:#c0392b;font-size:13px;text-align:center;padding:30px 0;">Failed to load activities. <a href="#" id="ap-retry-load" style="color:#00838f;font-weight:700;">Retry</a></p>';
        var retryBtn = document.getElementById('ap-retry-load');
        if(retryBtn) retryBtn.addEventListener('click', function(e){ e.preventDefault(); window.loadActivityList(); });
      }
    });
  };

  var _apSearchTimer = null;
  document.getElementById('ap-search').addEventListener('input', function(){
    clearTimeout(_apSearchTimer);
    _apSearchTimer = setTimeout(window.loadActivityList, 300);
  });

  // Injects Schd Unit / Qty / Task / Tick cells into each row of the
  // (unmodified) Activity Library list HTML — see the "inject client-
  // side" decision: listestactivity's own HTML/markup stays untouched,
  // this only appends extra <td> cells onto its existing <tr> rows.
  // Transforms the (unmodified) Activity Library list HTML in place:
  // relabels the existing "Unit" header/cell as "Schd Unit" (made
  // editable) and replaces the existing "Rate" header/cell with an
  // editable "Quantity" column — does NOT touch the real Activity
  // Library screen's own markup/endpoint, only this popup's copy of the
  // returned HTML. Column order: # | Project Type | Type | Activity |
  // Schd Unit | Quantity | Task | Allocate/Edit/Delete (unchanged) |
  // Tick (appended last).
  // This popup is a pure pick-and-add list — no edit/delete/resource-
  // allocation here (those belong to the real Activity Library, and to
  // the separate edit-attached-activities popup for an IOW's own
  // activities). Cell 6, the Activity Library's Allocate/Edit/Delete
  // icon cell, is dropped entirely rather than kept.
  function augmentActivityRows(){
    var table = document.querySelector('#ap-list-target table');
    if(!table) return;
    var headRow = table.querySelector('thead tr');
    if(headRow && headRow.cells.length >= 6){
      headRow.cells[4].textContent = 'Schd Unit';
      headRow.cells[5].textContent = 'Quantity';
      if(headRow.cells[6]) headRow.removeChild(headRow.cells[6]);
      var thTask = document.createElement('th');
      thTask.style.cssText = 'background:#555;color:#fff;font-weight:600;font-size:11px;padding:3px 6px;border:1px solid #444;text-align:center;width:60px;';
      thTask.textContent = 'Task';
      headRow.appendChild(thTask);
      var thTick = document.createElement('th');
      thTick.style.cssText = 'background:#555;color:#fff;font-weight:600;font-size:11px;padding:3px 6px;border:1px solid #444;text-align:center;width:40px;';
      headRow.appendChild(thTick);
    }
    var prefillQueue = [];
    table.querySelectorAll('tbody tr[data-id]').forEach(function(tr){
      if(tr.cells.length < 6) return;
      var actId = tr.getAttribute('data-id');
      var actName = (tr.querySelector('input[id^="activity"]') || {}).value || tr.cells[3].textContent.trim();
      var libUnit = (tr.querySelector('input[id^="activityunit"]') || {}).value || '';

      // Cell 4 (was read-only Unit text) -> editable Schd Unit input.
      var unitCell = tr.cells[4];
      unitCell.className = 'ap-col-schunit';
      unitCell.innerHTML = '<input type="text" class="ap-schunit-input" value="' + libUnit.replace(/"/g,'&quot;') + '">';

      // Cell 5 (was read-only Rate text) -> editable Quantity input.
      var rateCell = tr.cells[5];
      rateCell.className = 'ap-col-qty';
      rateCell.innerHTML = '<input type="number" class="ap-qty-input" placeholder="0" step="0.01" min="0">';

      // Cell 6 — Activity Library's own Allocate/Edit/Delete icons —
      // dropped, this list only ever adds, never edits/deletes/allocates.
      if(tr.cells[6]) tr.removeChild(tr.cells[6]);

      // Task button — grey, appended after Quantity.
      var tdTask = document.createElement('td');
      tdTask.style.cssText = 'border:1px solid #ddd;padding:4px;text-align:center;';
      tdTask.innerHTML = '<button type="button" class="ap-task-btn" data-actid="' + actId + '" data-actname="' + actName.replace(/"/g,'&quot;') + '">Task</button>';
      tr.appendChild(tdTask);

      // Tick (add to Gantt) — appended last.
      var tdTick = document.createElement('td');
      tdTick.style.cssText = 'border:1px solid #ddd;padding:4px;text-align:center;';
      tdTick.innerHTML = '<button type="button" class="ap-tick-btn" data-actid="' + actId + '" data-actname="' + actName.replace(/"/g,'&quot;') + '" title="Add to Gantt">&#10003;</button>';
      tr.appendChild(tdTick);

      prefillQueue.push({ tr: tr, actId: actId });
    });

    // Prefill Schd Unit / tasks from the project's own existing
    // workgroup_activities_new + activity_tasks defaults, if any (same
    // source the old WBS form used via getactivityresources). Run a few
    // at a time instead of firing one request per row simultaneously —
    // a full-table burst (e.g. 25 rows) was overwhelming shared hosting's
    // concurrent PHP worker limit and intermittently breaking the main
    // list request itself.
    var PREFILL_CONCURRENCY = 4;
    var qIndex = 0, qActive = 0;
    function runNextPrefill(){
      if(qIndex >= prefillQueue.length) return;
      var item = prefillQueue[qIndex++];
      qActive++;
      var tr = item.tr, actId = item.actId;
      $.ajax({
        type:'POST', url:'../projectsmain/getactivityresources', dataType:'json',
        data:{ activity_id: actId },
        success: function(d){
          if(d.sch_unit) tr.querySelector('.ap-schunit-input').value = d.sch_unit;
          if(d.sch_qty)  tr.querySelector('.ap-qty-input').value = d.sch_qty;
          _apActivityTasks[actId] = (d.tasks || []).map(function(t){
            return { name: t.task_name, qty: t.task_qty, productivity: t.productivity, resunits: t.resunits || 1 };
          });
          if(_apActivityTasks[actId].length){
            var btn = tr.querySelector('.ap-task-btn');
            if(btn) btn.classList.add('ap-has-tasks');
          }
        },
        complete: function(){
          qActive--;
          runNextPrefill();
        }
      });
    }
    for(var qi = 0; qi < PREFILL_CONCURRENCY; qi++) runNextPrefill();
  }

  document.getElementById('ap-list-target').addEventListener('click', function(e){
    var taskBtn = e.target.closest('.ap-task-btn');
    if(taskBtn){
      var actId = taskBtn.getAttribute('data-actid');
      var row = taskBtn.closest('tr');
      var qtyInput = row ? row.querySelector('.ap-qty-input') : null;
      var schUnitInput = row ? row.querySelector('.ap-schunit-input') : null;
      window.openTaskPopupFor(actId, taskBtn.getAttribute('data-actname'), _apActivityTasks[actId] || [], function(updatedTasks, dur){
        _apActivityTasks[actId] = updatedTasks;
        taskBtn.classList.toggle('ap-has-tasks', updatedTasks.length > 0);
      }, function(){ return qtyInput ? (parseFloat(qtyInput.value) || 0) : 0; }, schUnitInput ? schUnitInput.value.trim() : '');
      return;
    }
    var tickBtn = e.target.closest('.ap-tick-btn');
    if(tickBtn){
      addActivityToGantt(tickBtn);
      return;
    }
  });

  /* ── Task sub-popup — shared by the Add Activity picker (keyed by
       library activity_id, _apActivityTasks) and the Edit Activities
       popup (keyed by sa_id, _eamTasksByAct). Generic: takes the current
       task list + a save callback, has no knowledge of either caller's
       own state. ── */
  function taskRowHtml(t){
    t = t || {};
    return '<td data-taskid="' + (t.id != null ? t.id : '') + '"><input type="text" class="apt-task-name" value="' + (t.name||'').replace(/"/g,'&quot;') + '" placeholder="Task name"></td>'
      + '<td><input type="number" class="apt-task-qty" value="' + (t.qty!=null?t.qty:1) + '" step="0.01" min="0"></td>'
      + '<td><input type="number" class="apt-task-prod" value="' + (t.productivity!=null?t.productivity:'') + '" step="0.01" min="0"></td>'
      + '<td><input type="text" class="apt-task-durunit" readonly></td>'
      + '<td><input type="number" class="apt-task-resunits" value="' + (t.resunits!=null?t.resunits:1) + '" step="1" min="1"></td>'
      + '<td style="text-align:center;"><button type="button" class="apt-del-btn" title="Remove">&times;</button></td>';
  }

  function apRecalcTaskRow(tr){
    var qty = parseFloat(tr.querySelector('.apt-task-qty').value) || 0;
    var prod = parseFloat(tr.querySelector('.apt-task-prod').value) || 0;
    var resUnits = parseFloat(tr.querySelector('.apt-task-resunits').value) || 1;
    var durPerUnit = prod > 0 ? (qty / prod) / resUnits : 0;
    tr.querySelector('.apt-task-durunit').value = durPerUnit > 0 ? durPerUnit.toFixed(4) : '';
    return durPerUnit;
  }

  function apRecalcDuration(){
    var schQty = (typeof _apTaskGetQty === 'function') ? _apTaskGetQty() : 0;
    var sum = 0;
    document.querySelectorAll('#apt-task-body tr').forEach(function(tr){ sum += apRecalcTaskRow(tr); });
    var dur = (schQty > 0 && sum > 0) ? Math.ceil(schQty * sum) : 0;
    document.getElementById('apt-dur-val').textContent = dur > 0 ? dur : '—';
    return dur;
  }

  function addTaskRowTo(tbody, t){
    var tr = document.createElement('tr');
    tr.innerHTML = taskRowHtml(t);
    tbody.appendChild(tr);
    tr.querySelectorAll('.apt-task-qty,.apt-task-prod,.apt-task-resunits').forEach(function(inp){
      inp.addEventListener('input', apRecalcDuration);
    });
    tr.querySelector('.apt-del-btn').addEventListener('click', function(){
      if(document.querySelectorAll('#apt-task-body tr').length > 1){ tr.remove(); apRecalcDuration(); }
    });
    apRecalcTaskRow(tr);
  }

  var _apTaskOnSave = null;   // caller's save callback for the currently-open Task popup
  var _apTaskGetQty  = null;  // caller's fn returning the current row's Qty, for the live duration preview

  // Generic entry point — any caller (Add Activity picker, Edit
  // Activities popup) opens this with: a label, the current task list,
  // an onSave(updatedTasks, duration) callback, and a getQty() callback
  // for the live duration display. Neither caller's own state is
  // touched here — onSave is where the caller decides what to do with
  // the edited list.
  window.openTaskPopupFor = function(id, label, tasks, onSave, getQty, unit){
    document.getElementById('apt-hdr-act').textContent = label ? ('— ' + label) : '';
    document.getElementById('apt-qty-hdr').textContent = unit ? ('Qty/' + unit) : 'Qnty/Unit';
    _apTaskOnSave = onSave;
    _apTaskGetQty = getQty || function(){ return 0; };

    var tbody = document.getElementById('apt-task-body');
    tbody.innerHTML = '';
    var rows = (tasks && tasks.length) ? tasks : [{}];
    rows.forEach(function(t){ addTaskRowTo(tbody, t); });
    apRecalcDuration();

    document.getElementById('apt-bk').classList.add('apt-open');
    document.getElementById('apt-modal').classList.add('apt-open');
  };

  function closeTaskPopup(){
    var tasks = [];
    document.querySelectorAll('#apt-task-body tr').forEach(function(tr){
      var name = tr.querySelector('.apt-task-name').value.trim();
      if(!name) return;
      var idCell = tr.cells[0];
      var idAttr = idCell ? idCell.getAttribute('data-taskid') : '';
      tasks.push({
        id: idAttr ? parseInt(idAttr, 10) : null,
        name: name,
        qty: parseFloat(tr.querySelector('.apt-task-qty').value) || 0,
        productivity: parseFloat(tr.querySelector('.apt-task-prod').value) || 0,
        resunits: parseFloat(tr.querySelector('.apt-task-resunits').value) || 1
      });
    });
    var dur = apRecalcDuration();
    if(typeof _apTaskOnSave === 'function') _apTaskOnSave(tasks, dur);
    _apTaskOnSave = null;
    _apTaskGetQty = null;
    document.getElementById('apt-bk').classList.remove('apt-open');
    document.getElementById('apt-modal').classList.remove('apt-open');
  }
  document.getElementById('apt-close').addEventListener('click', closeTaskPopup);
  document.getElementById('apt-btn-done').addEventListener('click', closeTaskPopup);
  document.getElementById('apt-add-row-btn').addEventListener('click', function(){
    addTaskRowTo(document.getElementById('apt-task-body'), {});
  });

  /* ── Add to Gantt (tick) ── */
  function addActivityToGantt(tickBtn){
    if(!window._apCurrentWgId){
      document.getElementById('ap-select-msg').textContent = 'Select an IOW before adding activities.';
      return;
    }
    var actId = tickBtn.getAttribute('data-actid');
    var actName = tickBtn.getAttribute('data-actname');
    var row = tickBtn.closest('tr');
    var schUnit = row.querySelector('.ap-schunit-input').value.trim();
    var schQty = parseFloat(row.querySelector('.ap-qty-input').value) || 0;
    var tasks = _apActivityTasks[actId] || [];

    if(!schQty){ alert('Please enter a Qty before adding this activity.'); return; }

    // Same duration formula as the old WBS form's recalcDuration():
    // duration = ceil(schQty * sum(task durPerUnit)), durPerUnit = (task.qty / task.productivity) / task.resunits.
    var sumDurPerUnit = 0;
    tasks.forEach(function(t){
      var dpu = (t.productivity > 0) ? ((t.qty || 0) / t.productivity) / (t.resunits || 1) : 0;
      sumDurPerUnit += dpu;
    });
    var duration = (schQty > 0 && sumDurPerUnit > 0) ? Math.ceil(schQty * sumDurPerUnit) : 1;

    // sumDurPerUnit === 0 means every task either has no Productivity/day
    // set or there are no tasks at all — duration silently falls back to
    // the 1-day minimum with no indication anything's wrong (this is
    // exactly how "Project Overhead" ended up stuck at 1 day instead of
    // 180: its task's Qty/Productivity were still 0 the moment this tick
    // was clicked). Ask for confirmation instead of proceeding silently —
    // the user can go back, set Productivity/day on the Task popup, then
    // add, or explicitly accept a 1-day placeholder if that's intended.
    if (sumDurPerUnit === 0 && !window.confirm(
      'No task on "' + actName + '" has a Productivity/day set, so its duration will default to 1 day.\n\n' +
      'Click Cancel to go back and set Productivity/day on the Task popup first, or OK to add it anyway as a 1-day activity.'
    )) {
      return;
    }

    tickBtn.disabled = true;
    var payload = {
      wg_id: window._apCurrentWgId,
      // iow_name / iow_group_id: the SELECTED IOW's own current identity,
      // resent unchanged on every add — actionWbsadd() re-writes
      // workgroups_new's Name/iowGroupid from these fields whenever
      // wg_id already exists, so omitting them renames the IOW to the
      // activity name and resets its group to none.
      iow_name: window._apCurrentIowName,
      iow_group_id: window._apCurrentIowGroupId,
      iow_act_id: actId,
      act_name: actName,
      sch_unit: schUnit,
      sch_qty: schQty,
      qty: schQty,
      unit: schUnit,
      duration: duration,
      tasks: tasks.map(function(t){
        return { name: t.name, qty: t.qty, prod: t.productivity, resunits: t.resunits };
      }),
      resources: []
    };
    $.ajax({
      type: 'POST', url: '../projectsmain/wbsadd', dataType: 'json',
      data: { payload: JSON.stringify(payload) },
      success: function(d){
        tickBtn.disabled = false;
        if(d.error && d.error !== 'No'){ alert(d.errortext || d.error); return; }
        // Applies to the Gantt instantly on this tick (not deferred to
        // Close) — same accepted flicker tradeoff as Add IOW, so the
        // activity is never in doubt. Popup stays open — the user keeps
        // adding more activities under the same IOW without it closing.
        if(typeof window.reloadGantt === 'function') window.reloadGantt();
        // Tick stays clickable even after the first add (no 'ap-added'
        // inert state) — re-clicking it after editing Task Name/Unit/
        // Productivity/Qty on an already-added activity is how those
        // edits actually reach the server; see actionWbsadd()'s
        // already_exists branch, which now syncs tasks/unit/qty instead
        // of silently no-op'ing.
        tickBtn.innerHTML = '&#10003;';
        tickBtn.title = d.already_exists ? 'Updated — click again to re-sync Task/Unit/Qty changes' : 'Added to Gantt (click again to sync Task/Unit/Qty changes)';
        tickBtn.classList.add('ap-added');
      },
      error: function(){
        tickBtn.disabled = false;
        alert('Failed to add activity. Please try again.');
      }
    });
  }
})();
</script>

<!-- Edit Activities popup — opened by clicking an existing IOW row/bar on
     the Gantt. Shows only the activities ALREADY attached to that IOW
     (Qty/Task editable, Delete, save-tick per row) — the exact duplicate-
     for-editing counterpart to the "+" icon's pure add-only pickers.
     Adding MORE activities to this IOW happens via the "+" icon's Add
     Activity tab, not here. -->
<style>
#eam-bk{display:none;position:fixed;inset:0;background:transparent;z-index:200700;}
#eam-bk.eam-open{display:block}
#eam-modal{
  display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
  width:820px;height:480px;max-width:97vw;max-height:92vh;min-width:500px;min-height:300px;
  background:#fff;border-radius:8px;
  box-shadow:0 8px 32px rgba(0,0,0,.35);z-index:200701;flex-direction:column;overflow:hidden;
  font-family:'Nunito',sans-serif;
}
#eam-modal.eam-open{display:flex}
.eam-rs{position:absolute;z-index:10;background:transparent;}
.eam-rs-e {right:0;top:6px;bottom:6px;width:6px;cursor:e-resize;}
.eam-rs-w {left:0;top:6px;bottom:6px;width:6px;cursor:w-resize;}
.eam-rs-s {bottom:0;left:6px;right:6px;height:6px;cursor:s-resize;}
.eam-rs-n {top:0;left:6px;right:6px;height:6px;cursor:n-resize;}
.eam-rs-se{right:0;bottom:0;width:14px;height:14px;cursor:se-resize;}
.eam-rs-sw{left:0;bottom:0;width:14px;height:14px;cursor:sw-resize;}
.eam-rs-ne{right:0;top:0;width:14px;height:14px;cursor:ne-resize;}
.eam-rs-nw{left:0;top:0;width:14px;height:14px;cursor:nw-resize;}
#eam-hdr{
  background:#fff;color:#333;padding:8px 14px;display:flex;justify-content:space-between;
  align-items:center;font-family:'Nunito',sans-serif;font-size:15px;font-weight:700;
  border-bottom:2px solid #b0b4b9;flex-shrink:0;cursor:move;
}
#eam-hdr-title-suffix{font-size:13px;font-weight:700;color:#00838f;margin-left:8px;}
#eam-close{background:none;border:none;color:#333;font-size:20px;line-height:1;cursor:pointer;padding:0 4px;opacity:.6;}
#eam-close:hover{opacity:1;}
#eam-iow-row{
  display:flex;align-items:center;gap:10px;padding:10px 18px;background:#f4f5f6;
  border-bottom:2px solid #ccc;flex-shrink:0;font-size:14px;
}
#eam-iow-name{font-weight:700;color:#222;}
#eam-iow-group{font-size:12px;color:#00838f;font-weight:700;}
#eam-iow-name-input{height:30px;border:1px solid #ccc;border-radius:4px;padding:0 8px;font-size:13px;box-sizing:border-box;min-width:200px;}
#eam-iow-group-select{height:30px;border:1px solid #ccc;border-radius:4px;padding:0 8px;font-size:13px;box-sizing:border-box;min-width:180px;}
#eam-iow-spacer{flex:1;}
#eam-body{padding:14px 18px;overflow:auto;flex:1;scroll-snap-type:y proximity;}
#eam-body table{table-layout:auto;width:100%;min-width:600px;border-collapse:collapse;}
#eam-body tbody tr{scroll-snap-align:start;}
#eam-body th, #eam-body td{white-space:nowrap;padding:8px 10px;border:none;border-bottom:1px solid #888;font-size:13px;}
#eam-body th{background:#e9ebed;color:#555;font-weight:700;border-bottom:2px solid #ccc;text-align:left;}
#eam-body tbody tr:last-child td{border-bottom:none;}
#eam-body td.eam-col-sno{color:#999;text-align:center;width:36px;}
#eam-body td.eam-col-name{white-space:normal;}
.eam-name-input{width:100%;height:28px;border:1px solid #ccc;border-radius:4px;padding:0 6px;font-size:12px;box-sizing:border-box;}
.eam-unit-input{width:70px;height:28px;border:1px solid #ccc;border-radius:4px;padding:0 6px;font-size:12px;box-sizing:border-box;}
#eam-col-qty input{width:80px;height:28px;border:1px solid #ccc;border-radius:4px;padding:0 6px;font-size:12px;box-sizing:border-box;}
.eam-task-btn{
  background:#888;border:1px solid #777;color:#fff;border-radius:4px;padding:4px 10px;
  font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;
}
.eam-task-btn.eam-has-tasks{background:#6c7a89;border-color:#5a6672;color:#fff;}
.eam-edit-btn{
  background:#2d7dd2;color:#fff;border:none;border-radius:50%;width:28px;height:28px;
  font-size:13px;cursor:pointer;flex-shrink:0;
}
.eam-edit-btn:hover{background:#2564aa}
.eam-edit-btn.eam-editing{background:#27ae60;}
.eam-edit-btn.eam-editing:hover{background:#1e8449}
.eam-del-btn{
  background:#c0392b;color:#fff;border:none;border-radius:50%;width:28px;height:28px;
  font-size:14px;cursor:pointer;flex-shrink:0;
}
.eam-del-btn:hover{background:#992d22}
#eam-empty{color:#aaa;font-size:13px;text-align:center;padding:30px 0;}
#eam-footer{
  background:#fff;border-top:1px solid #ddd;padding:10px 16px;display:flex;justify-content:flex-end;
  gap:12px;flex-shrink:0;
}
#eam-btn-close{
  background:#e67e22;color:#fff;border:none;border-radius:999px;padding:8px 24px;font-size:13px;
  font-weight:700;cursor:pointer;letter-spacing:.3px;text-transform:uppercase;
}
</style>

<div id="eam-bk"></div>
<div id="eam-modal">
  <div class="eam-rs eam-rs-e"  data-dir="e"></div>
  <div class="eam-rs eam-rs-w"  data-dir="w"></div>
  <div class="eam-rs eam-rs-s"  data-dir="s"></div>
  <div class="eam-rs eam-rs-n"  data-dir="n"></div>
  <div class="eam-rs eam-rs-se" data-dir="se"></div>
  <div class="eam-rs eam-rs-sw" data-dir="sw"></div>
  <div class="eam-rs eam-rs-ne" data-dir="ne"></div>
  <div class="eam-rs eam-rs-nw" data-dir="nw"></div>
  <div id="eam-hdr">
    <span>Edit Activities<span id="eam-hdr-title-suffix"></span></span>
    <button id="eam-close">&times;</button>
  </div>
  <div id="eam-iow-row" data-wgid="">
    <span id="eam-iow-name"></span>
    <span id="eam-iow-group"></span>
    <span id="eam-iow-spacer"></span>
    <button type="button" class="eam-edit-btn" id="eam-iow-edit-btn" title="Edit IOW">&#9998;</button>
    <button type="button" class="eam-del-btn" id="eam-iow-del-btn" title="Delete IOW">&times;</button>
  </div>
  <div id="eam-body">
    <div id="eam-list-target"><p id="eam-empty">Loading…</p></div>
  </div>
  <div id="eam-footer">
    <button id="eam-btn-close">Close</button>
  </div>
</div>

<script>
(function(){
  var _eamWgId = null;
  var _eamIowName = '';
  var _eamIowGroupId = 0;
  var _eamReloadPending = false;

  function eamTaskRowHtml(actId){
    var tasks = _eamTasksByAct[actId] || [];
    return tasks.length ? (tasks.length + ' task' + (tasks.length > 1 ? 's' : '')) : 'No tasks';
  }

  var _eamTasksByAct = {}; // sa_id -> [{id, name, qty, productivity, resunits}], edited copy per row

  function eamRecalcDuration(schQty, tasks){
    var sumDurPerUnit = 0;
    (tasks || []).forEach(function(t){
      var dpu = (t.productivity > 0) ? ((t.qty || 0) / t.productivity) / (t.resunits || 1) : 0;
      sumDurPerUnit += dpu;
    });
    return (schQty > 0 && sumDurPerUnit > 0) ? Math.ceil(schQty * sumDurPerUnit) : 1;
  }

  var _eamGroupNamesById = {}; // populated from getiowgrouplist, id -> name, for the IOW row's group label

  window.openEditActivitiesModal = function(wgId, iowName, iowGroupId){
    _eamWgId = wgId;
    _eamIowName = iowName || '';
    _eamIowGroupId = iowGroupId || 0;
    _eamReloadPending = false;
    document.getElementById('eam-hdr-title-suffix').textContent = iowName ? (' — ' + iowName) : '';
    eamRenderIowRowView();
    document.getElementById('eam-bk').classList.add('eam-open');
    document.getElementById('eam-modal').classList.add('eam-open');
    eamLoadList();
    $.ajax({
      type: 'POST', url: '../projects/getiowgrouplist', dataType: 'json',
      success: function(d){
        _eamGroupNamesById = {};
        (d.items || []).forEach(function(g){ _eamGroupNamesById[g.id] = g.name; });
        eamRenderIowRowView();
      }
    });
  };

  function eamRenderIowRowView(){
    document.getElementById('eam-iow-row').setAttribute('data-wgid', _eamWgId);
    document.getElementById('eam-iow-name').textContent = _eamIowName;
    var groupName = _eamGroupNamesById[_eamIowGroupId] || '';
    document.getElementById('eam-iow-group').textContent = groupName ? ('— ' + groupName) : '';
  }

  function eamViewCellsHtml(it){
    return '<td class="eam-col-name">' + $('<div>').text(it.name).html() + '</td>'
      + '<td>' + $('<div>').text(it.unit).html() + '</td>'
      + '<td id="eam-col-qty">' + it.quantity + '</td>';
  }

  function eamEditCellsHtml(it){
    return '<td class="eam-col-name"><input type="text" class="eam-name-input" value="' + $('<div>').text(it.name).html() + '"></td>'
      + '<td><input type="text" class="eam-unit-input" value="' + $('<div>').text(it.unit).html() + '"></td>'
      + '<td id="eam-col-qty"><input type="number" class="eam-qty-input" value="' + it.quantity + '" step="0.01" min="0"></td>';
  }

  function eamLoadList(){
    var target = document.getElementById('eam-list-target');
    target.innerHTML = '<p id="eam-empty">Loading…</p>';
    $.ajax({
      type: 'POST', url: '../projectsmain/wbsactivitiesbyiow', dataType: 'json',
      data: { wg_id: _eamWgId },
      success: function(d){
        var items = d.items || [];
        _eamTasksByAct = {};
        if(!items.length){
          target.innerHTML = '<p id="eam-empty">No activities added under this IOW yet.</p>';
          return;
        }
        var html = '<table><thead><tr>'
          + '<th style="width:36px;">#</th><th>Activity</th><th style="width:70px;">Unit</th><th style="width:110px;">Quantity</th>'
          + '<th style="width:90px;">Task</th><th style="width:44px;"></th><th style="width:40px;"></th>'
          + '</tr></thead><tbody>';
        items.forEach(function(it, idx){
          _eamTasksByAct[it.sa_id] = it.tasks || [];
          html += '<tr data-said="' + it.sa_id + '">'
            + '<td class="eam-col-sno">' + (idx + 1) + '</td>'
            + eamViewCellsHtml(it)
            + '<td><button type="button" class="eam-task-btn' + (it.tasks && it.tasks.length ? ' eam-has-tasks' : '') + '" data-said="' + it.sa_id + '" data-actname="' + $('<div>').text(it.name).html() + '">' + eamTaskRowHtml(it.sa_id) + '</button></td>'
            + '<td style="text-align:center;"><button type="button" class="eam-edit-btn" data-said="' + it.sa_id + '" title="Edit">&#9998;</button></td>'
            + '<td style="text-align:center;"><button type="button" class="eam-del-btn" data-said="' + it.sa_id + '" title="Delete">&times;</button></td>'
            + '</tr>';
        });
        html += '</tbody></table>';
        target.innerHTML = html;
      },
      error: function(){
        target.innerHTML = '<p id="eam-empty" style="color:#c0392b;">Failed to load activities.</p>';
      }
    });
  }

  document.getElementById('eam-list-target').addEventListener('click', function(e){
    var taskBtn = e.target.closest('.eam-task-btn');
    if(taskBtn){
      var saId = taskBtn.getAttribute('data-said');
      if(typeof window.openTaskPopupFor === 'function'){
        var row = taskBtn.closest('tr');
        var qtyInput = row ? row.querySelector('.eam-qty-input') : null;
        var unitInput = row ? row.querySelector('.eam-unit-input') : null;
        // Row is normally in view mode here (plain <td> text, no input) —
        // same fallback eamSaveActivity already uses: read cells[2]'s text
        // directly instead of assuming an .eam-unit-input exists. Qty needs
        // the identical fallback (was missing before: qtyInput was always
        // null in view mode, so getQty() always returned 0 and the Task
        // popup's live Duration preview showed "—" no matter what
        // Productivity/Qty the user typed, even though Dur/Unit per row
        // computed correctly).
        var unitVal = unitInput ? unitInput.value.trim() : (row ? row.cells[2].textContent.trim() : '');
        var qtyVal = qtyInput ? qtyInput.value : (row ? row.querySelector('#eam-col-qty').textContent.trim() : '0');
        window.openTaskPopupFor(saId, taskBtn.getAttribute('data-actname'), _eamTasksByAct[saId] || [], function(updatedTasks){
          _eamTasksByAct[saId] = updatedTasks;
          taskBtn.textContent = eamTaskRowHtml(saId);
          taskBtn.classList.toggle('eam-has-tasks', updatedTasks.length > 0);
          // DONE on the Tasks popup used to only update this in-memory map —
          // nothing reached the server unless the row's pencil was ALSO
          // clicked twice afterward, so edits like Productivity silently
          // never persisted (no Budgeted_Duration, no Gantt bar). Save
          // immediately here instead, using the row's current Activity/
          // Unit/Qty exactly as eamToggleEdit's own save branch does.
          eamSaveActivity(saId, row);
        }, function(){ return parseFloat(qtyVal) || 0; }, unitVal);
      }
      return;
    }
    var editBtn = e.target.closest('.eam-edit-btn');
    if(editBtn){ eamToggleEdit(editBtn); return; }
    var delBtn = e.target.closest('.eam-del-btn');
    if(delBtn){ eamDeleteRow(delBtn); return; }
  });

  // One pencil button per row, toggling between view and edit mode.
  // First click: swap the row's Activity/Unit/Quantity cells into
  // editable inputs (pencil turns green). Second click: read the
  // inputs back, save to the server, and swap back to plain text.
  function eamToggleEdit(editBtn){
    var saId = editBtn.getAttribute('data-said');
    var row = editBtn.closest('tr');
    var isEditing = editBtn.classList.contains('eam-editing');

    if (!isEditing) {
      var current = {
        name: row.cells[1].textContent.trim(),
        unit: row.cells[2].textContent.trim(),
        quantity: row.cells[3].textContent.trim()
      };
      var wrap = document.createElement('tbody');
      wrap.innerHTML = '<tr>' + eamEditCellsHtml(current) + '</tr>';
      // Snapshot the 3 new cells BEFORE replacing anything — each
      // replaceChild below mutates row.cells' live indices, so reading
      // them from a separate, untouched NodeList avoids off-by-one bugs.
      var editCells = [].slice.call(wrap.querySelector('tr').cells);
      row.replaceChild(editCells[0], row.cells[1]);
      row.replaceChild(editCells[1], row.cells[2]);
      row.replaceChild(editCells[2], row.cells[3]);
      editBtn.classList.add('eam-editing');
      editBtn.title = 'Save';
      return;
    }

    eamSaveActivity(saId, row, editBtn);
  }

  // Shared save path for an Edit Activities row — POSTs Activity/Unit/Qty
  // plus whatever tasks are currently staged in _eamTasksByAct[saId] to
  // wbsactivityupdate. Reads Name/Unit/Qty from the row's edit <input>s if
  // it's mid-edit (eamToggleEdit's second click), otherwise from the plain
  // <td> text (called straight from the Tasks popup's DONE button, where
  // the row is still in view mode). editBtn is optional — only present
  // when called via the pencil toggle, for its disabled/label bookkeeping.
  function eamSaveActivity(saId, row, editBtn){
    var nameInput = row.querySelector('.eam-name-input');
    var name = nameInput ? nameInput.value.trim() : row.cells[1].textContent.trim();
    var unitInput = row.querySelector('.eam-unit-input');
    var unit = unitInput ? unitInput.value.trim() : row.cells[2].textContent.trim();
    var qtyInput = row.querySelector('.eam-qty-input');
    var qty = qtyInput ? (parseFloat(qtyInput.value) || 0) : (parseFloat(row.cells[3].textContent) || 0);
    if (!name) { alert('Activity name is required.'); return; }
    if (!qty)  { alert('Please enter a Qty before saving.'); return; }

    var tasks = _eamTasksByAct[saId] || [];
    if (editBtn) editBtn.disabled = true;
    $.ajax({
      type: 'POST', url: '../projectsmain/wbsactivityupdate', dataType: 'json',
      data: { payload: JSON.stringify({
        sa_id: saId, name: name, unit: unit, qty: qty,
        tasks: tasks.map(function(t){ return { id: t.id, name: t.name, qty: t.qty, productivity: t.productivity, resunits: t.resunits }; })
      }) },
      success: function(d){
        if (editBtn) editBtn.disabled = false;
        if(d.error && d.error !== 'No'){ alert(d.errortext || d.error); return; }
        // Edits apply to the Gantt instantly (not deferred to Close) —
        // accepted flicker tradeoff so the change is never in doubt.
        if(typeof window.reloadGantt === 'function') window.reloadGantt();
        if (nameInput) {
          var cells = row.cells;
          cells[1].outerHTML = '<td class="eam-col-name">' + $('<div>').text(name).html() + '</td>';
          cells = row.cells;
          cells[2].outerHTML = '<td>' + $('<div>').text(unit).html() + '</td>';
          cells = row.cells;
          cells[3].outerHTML = '<td id="eam-col-qty">' + qty + '</td>';
        }
        if (editBtn) { editBtn.classList.remove('eam-editing'); editBtn.title = 'Edit'; }
        var taskBtn = row.querySelector('.eam-task-btn');
        if(taskBtn) taskBtn.setAttribute('data-actname', name);
      },
      error: function(){
        if (editBtn) editBtn.disabled = false;
        alert('Failed to save. Please try again.');
      }
    });
  }

  function eamDeleteRow(delBtn){
    if(!confirm('Delete this activity from the IOW?')) return;
    var saId = delBtn.getAttribute('data-said');
    eamDoDelete(saId, false);
  }

  function eamDoDelete(saId, force){
    $.ajax({
      type: 'POST', url: '../projectsmain/wbsdelete', dataType: 'json',
      data: { sa_id: saId, force: force ? 1 : 0 },
      success: function(d){
        if(d.conflict && !force){
          if(confirm((d.errortext || 'This will also delete related data.') + ' Continue?')){
            eamDoDelete(saId, true);
          }
          return;
        }
        if(d.error && d.error !== 'No' && !d.ok){ alert(d.errortext || d.error); return; }
        // Deletes apply to the Gantt instantly (not deferred to Close) —
        // accepted flicker tradeoff so the change is never in doubt.
        eamLoadList();
        if(typeof window.reloadGantt === 'function') window.reloadGantt();
      },
      error: function(){ alert('Failed to delete. Please try again.'); }
    });
  }

  // IOW row's own pencil — same view/edit toggle pattern as an activity
  // row: first click swaps Name/Group into editable inputs, second click
  // saves via wbsupdateiow and reverts to plain text.
  document.getElementById('eam-iow-edit-btn').addEventListener('click', function(){
    var btn = this;
    var isEditing = btn.classList.contains('eam-editing');
    if (!isEditing) {
      var nameSpan = document.getElementById('eam-iow-name');
      nameSpan.outerHTML = '<input type="text" id="eam-iow-name-input" value="' + $('<div>').text(_eamIowName).html() + '">';
      var groupSpan = document.getElementById('eam-iow-group');
      var opts = '<option value="">— Select IOW Group —</option>';
      Object.keys(_eamGroupNamesById).forEach(function(id){
        opts += '<option value="' + id + '"' + (parseInt(id,10) === _eamIowGroupId ? ' selected' : '') + '>' + $('<div>').text(_eamGroupNamesById[id]).html() + '</option>';
      });
      groupSpan.outerHTML = '<select id="eam-iow-group-select">' + opts + '</select>';
      btn.classList.add('eam-editing');
      btn.title = 'Save';
      return;
    }

    var name = document.getElementById('eam-iow-name-input').value.trim();
    var groupId = document.getElementById('eam-iow-group-select').value;
    if (!name) { alert('IOW name is required.'); return; }
    if (!groupId) { alert('Please select an IOW Group.'); return; }

    btn.disabled = true;
    $.ajax({
      type: 'POST', url: '../projectsmain/wbsupdateiow', dataType: 'json',
      data: { wg_id: _eamWgId, name: name, iow_group_id: groupId },
      success: function(d){
        btn.disabled = false;
        if(d.error && d.error !== 'No'){ alert(d.error); return; }
        _eamIowName = name;
        _eamIowGroupId = parseInt(groupId, 10);
        document.getElementById('eam-hdr-title-suffix').textContent = ' — ' + _eamIowName;
        document.getElementById('eam-iow-name-input').outerHTML = '<span id="eam-iow-name"></span>';
        document.getElementById('eam-iow-group-select').outerHTML = '<span id="eam-iow-group"></span>';
        eamRenderIowRowView();
        btn.classList.remove('eam-editing');
        btn.title = 'Edit IOW';
        if(typeof window.reloadGantt === 'function') window.reloadGantt();
      },
      error: function(){
        btn.disabled = false;
        alert('Failed to save. Please try again.');
      }
    });
  });

  // IOW row's delete — removes the whole IOW and every activity under
  // it. Same conflict/force confirmation pattern as a single activity's
  // delete, via actionWbsdeleteiow.
  document.getElementById('eam-iow-del-btn').addEventListener('click', function(){
    if(!confirm('Delete this entire IOW and all its activities?')) return;
    eamDoDeleteIow(false);
  });

  function eamDoDeleteIow(force){
    $.ajax({
      type: 'POST', url: '../projectsmain/wbsdeleteiow', dataType: 'json',
      data: { wg_id: _eamWgId, force: force ? 1 : 0 },
      success: function(d){
        if(d.conflict && !force){
          if(confirm((d.errortext || 'This will also delete related data.') + ' Continue?')){
            eamDoDeleteIow(true);
          }
          return;
        }
        if(d.error && d.error !== 'No' && !d.ok){ alert(d.errortext || d.error); return; }
        if(typeof window.reloadGantt === 'function') window.reloadGantt();
        closeEamModal();
      },
      error: function(){ alert('Failed to delete IOW. Please try again.'); }
    });
  }

  function closeEamModal(){
    document.getElementById('eam-bk').classList.remove('eam-open');
    document.getElementById('eam-modal').classList.remove('eam-open');
    if(_eamReloadPending){
      _eamReloadPending = false;
      if(typeof window.reloadGantt === 'function') window.reloadGantt();
    }
  }
  document.getElementById('eam-close').addEventListener('click', closeEamModal);
  document.getElementById('eam-btn-close').addEventListener('click', closeEamModal);
  document.getElementById('eam-bk').addEventListener('click', closeEamModal);

  // Drag + resize — identical pattern to #iow-modal's.
  (function(){
    var win = document.getElementById('eam-modal');
    var hdr = document.getElementById('eam-hdr');
    var MIN_W=500, MIN_H=300, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0;
    function _anchor(){
      var r=win.getBoundingClientRect();
      win.style.left=r.left+'px'; win.style.top=r.top+'px';
      win.style.width=r.width+'px'; win.style.height=r.height+'px';
      win.style.transform='none';
      return r;
    }
    bindDragTouch(hdr, 'mousedown', function(e){
      if(e.target.id==='eam-close') return;
      var r=_anchor(); _action='drag'; _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top;
      e.preventDefault();
    });
    document.querySelectorAll('.eam-rs').forEach(function(el){
      bindDragTouch(el, 'mousedown', function(e){
        var r=_anchor(); _action=el.getAttribute('data-dir');
        _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height;
        e.preventDefault(); e.stopPropagation();
      });
    });
    bindDragTouch(document, 'mousemove', function(e){
      if(!_action) return;
      var dx=e.clientX-_sx, dy=e.clientY-_sy;
      if(_action==='drag'){
        win.style.left=Math.max(0,_ox+dx)+'px'; win.style.top=Math.max(0,_oy+dy)+'px';
      } else {
        var l=_ox,t=_oy,w=_ow,h=_oh;
        if(_action.indexOf('e')>-1){ w=Math.max(MIN_W,_ow+dx); }
        if(_action.indexOf('s')>-1){ h=Math.max(MIN_H,_oh+dy); }
        if(_action.indexOf('w')>-1){ var nw=Math.max(MIN_W,_ow-dx); l=_ox+(_ow-nw); w=nw; }
        if(_action.indexOf('n')>-1){ var nh=Math.max(MIN_H,_oh-dy); t=_oy+(_oh-nh); h=nh; }
        win.style.left=l+'px'; win.style.top=t+'px'; win.style.width=w+'px'; win.style.height=h+'px';
      }
    }, { passive: false });
    bindDragTouch(document, 'mouseup', function(){ _action=null; });
  })();
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
  background:#4a5a6a; color:#fff; padding:10px 15px;
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
      <span style="font-size:15px;font-weight:700;color:#fff;">Schedule</span>
      <span style="font-size:14px;font-weight:700;color:#fff;">Gantt Chart</span>
      <span style="font-size:12px;font-weight:500;color:#c9c9c9;padding:3px 6px;border-radius:4px;cursor:pointer;" id="gantt-win-proj-name" title="Hover for project snapshot" onmouseover="this.style.background='rgba(255,255,255,.12)'" onmouseout="this.style.background='transparent'"><?php echo htmlspecialchars($ProjectName); ?></span>
    </div>
    <div id="gantt-win-hdr-btns">
      <a class="icon-clock" id="btn-project-duration" title="Project Duration" href="#" style="width:24px;height:24px;font-size:11px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:#2878c0;color:#fff;cursor:pointer;text-decoration:none;box-shadow:0 2px 6px rgba(0,0,0,.2);">&#8986;</a>
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

  var _gcmPopupIds = ['gcm-popup','gm-ucr-tip','gm-rcon-tip','gm-rcost-tip','gkm-bk','gkm-modal','gkp-popup','gpd-tip'];

  // Every reload of #gantt-win-body's HTML brings a brand-new copy of
  // these popups with it, but getElementById only ever finds the FIRST
  // element with a given id — if an older copy is still sitting in
  // <body> from a previous reparent, it stays "found" and the fresh one
  // injected into the new HTML never gets reparented (or shown/hidden)
  // correctly, which is what produced the stray dial flash on save:
  // the old, orphaned popup copy briefly existed with whatever visible
  // state it was last left in. Removing every old copy before the new
  // HTML lands guarantees there is only ever one of each at a time.
  function _removeStaleGcmPopups(){
    _gcmPopupIds.forEach(function(id){
      document.querySelectorAll('#' + id).forEach(function(el){ el.remove(); });
    });
  }

  function _reparentGcmPopup(){
    // See window.ensureGanttLoaded below for why this is necessary. Also
    // covers the KPI modal/float panel (gkm-bk/gkm-modal/gkp-popup) — same
    // trapped-stacking-context problem, they're rendered as DOM siblings of
    // gcm-popup inside newganttview.php's injected markup.
    _gcmPopupIds.forEach(function(id){
      var el = document.getElementById(id);
      if(el && el.parentNode !== document.body) document.body.appendChild(el);
    });
  }

  function _loadGanttWin(pid) {
    var url = _ganttUrl + '&id=' + (pid || _ganttPid);
    _removeStaleGcmPopups();
    $('#gantt-win-body').html('<div id="gantt-win-loading">Loading Gantt chart&hellip;</div>');
    $.ajax({ url: url, cache: false, success: function(html){
      $('#gantt-win-body').html(html);
      _reparentGcmPopup();
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

  // Lazily loads the Gantt chart's markup/script into the (hidden)
  // #gantt-win-body without opening the floating window itself, so pages
  // that never open the Gantt window directly (e.g. the Project Estimate
  // report) can still reach functions it exposes on window, such as
  // showCostDashboardPopup. Calls cb() once ready — immediately if the
  // content is already loaded.
  // #gcm-popup (Cost Dashboard) and its hover-tooltips are meant to be
  // usable even when the Gantt window itself stays closed (e.g. opened
  // from the Project Estimate report) — but display:none on any ancestor
  // (here, #gantt-win) hides position:fixed descendants too, so they must
  // be reparented to <body>, out from under #gantt-win, the same way every
  // other floating popup in this app is (see _reparentGcmPopup above).
  window.ensureGanttLoaded = function(cb) {
    if(_loaded){ cb && cb(); return; }
    _loaded = true;
    var url = _ganttUrl + '&id=' + _ganttPid;
    $.ajax({ url: url, cache: false, success: function(html){
      $('#gantt-win-body').html(html);
      _reparentGcmPopup();
      cb && cb();
    }, error: function(){
      _loaded = false;
    }});
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
    _ganttAutoFormat();
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

  // NOTE: auto format-switching on resize was attempted here (calling
  // g.setFormat() directly) but reverted — it bypasses newganttview.php's
  // post-Draw() custom header relabeling and status-color styling, which
  // corrupted the header (overlapping date labels) and lost task bar
  // colors (fell back to JSGantt's unstyled black/white). Re-implement
  // via a full loadGantt()-equivalent reload if this is revisited, not
  // a bare setFormat() call.
  function _ganttAutoFormat(){}

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
  bindDragTouch(document, 'mouseup', function(){
    if(_action && _action!=='drag') _ganttAutoFormat();
    _action=null;
  });
})();
</script>

<!-- ══════════════════════════════════════════════════════════════════════
     GANTT DEPENDENCY VIEW FLOATING WINDOW (experimental — separate popup,
     separate view file newganttview_dependency.php. Safe to remove
     wholesale (this block + the icon above + the controller action +
     the view file) if the idea doesn't prove useful.
════════════════════════════════════════════════════════════════════════ -->
<style>
#gantt-win-dep {
  display:none; position:fixed; top:90px; left:40px;
  width:1040px; height:calc(100vh - 110px);
  min-width:420px; min-height:300px;
  z-index:100000; background:#fff; border-radius:6px;
  border:1px solid #d0d0d0;
  box-shadow:0 4px 16px rgba(0,0,0,.15);
  flex-direction:column; overflow:hidden;
}
#gantt-win-dep.gw-open { display:flex; }
#gantt-win-dep-hdr {
  background:#4a5a6a; color:#fff; padding:10px 15px;
  display:flex; align-items:center; justify-content:space-between;
  cursor:move; user-select:none; flex-shrink:0;
  font-family:'Nunito',sans-serif;
  border-bottom:1px solid #e0e0e0;
}
#gantt-win-dep-hdr-btns { display:flex; align-items:center; gap:8px; }
#gantt-win-dep-hdr-btns button {
  background:none; border:none; color:#fff; font-size:20px;
  cursor:pointer; line-height:1; padding:0 4px;
}
#gantt-win-dep-body { flex:1; min-height:0; overflow:auto; }
#gantt-win-dep-loading { padding:50px; text-align:center; color:#666; font-size:14px; }
.gwd-rs { position:absolute; z-index:10; background:transparent; }
.gwd-rs-e  { right:0;  top:6px;    bottom:6px; width:6px;  cursor:e-resize; }
.gwd-rs-w  { left:0;   top:6px;    bottom:6px; width:6px;  cursor:w-resize; }
.gwd-rs-s  { bottom:0; left:6px;   right:6px;  height:6px; cursor:s-resize; }
.gwd-rs-n  { top:0;    left:6px;   right:6px;  height:6px; cursor:n-resize; }
.gwd-rs-se { right:0;  bottom:0; width:14px; height:14px; cursor:se-resize; }
.gwd-rs-sw { left:0;   bottom:0; width:14px; height:14px; cursor:sw-resize; }
.gwd-rs-ne { right:0;  top:0;    width:14px; height:14px; cursor:ne-resize; }
.gwd-rs-nw { left:0;   top:0;    width:14px; height:14px; cursor:nw-resize; }
</style>

<div id="gantt-win-dep">
  <div class="gwd-rs gwd-rs-n" data-dir="n"></div>
  <div class="gwd-rs gwd-rs-s" data-dir="s"></div>
  <div class="gwd-rs gwd-rs-e" data-dir="e"></div>
  <div class="gwd-rs gwd-rs-w" data-dir="w"></div>
  <div class="gwd-rs gwd-rs-ne" data-dir="ne"></div>
  <div class="gwd-rs gwd-rs-nw" data-dir="nw"></div>
  <div class="gwd-rs gwd-rs-se" data-dir="se"></div>
  <div class="gwd-rs gwd-rs-sw" data-dir="sw"></div>
  <div id="gantt-win-dep-hdr">
    <div style="display:flex;align-items:baseline;gap:6px;flex-wrap:nowrap;">
      <span style="font-size:15px;font-weight:700;color:#fff;">Dependency View</span>
      <span style="font-size:11px;font-weight:400;color:#c9c9c9;">(experimental)</span>
      <span style="font-size:12px;font-weight:500;color:#c9c9c9;padding:3px 6px;border-radius:4px;" id="gantt-win-dep-proj-name"><?php echo htmlspecialchars($ProjectName); ?></span>
    </div>
    <div id="gantt-win-dep-hdr-btns">
      <button id="gantt-win-dep-expand" title="Fullscreen">&#x26F6;</button>
      <button id="gantt-win-dep-close" title="Close">&times;</button>
    </div>
  </div>
  <div id="gantt-win-dep-body">
    <div id="gantt-win-dep-loading">Loading Dependency view&hellip;</div>
  </div>
</div>

<script>
(function(){
  var win = document.getElementById('gantt-win-dep');
  var hdr = document.getElementById('gantt-win-dep-hdr');
  var MIN_W=500, MIN_H=300, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0, _saved=null;
  var _loaded = false;

  function _anchor(){
    var r=win.getBoundingClientRect();
    win.style.right='auto'; win.style.left=r.left+'px';
    win.style.top=r.top+'px'; win.style.width=r.width+'px';
    win.style.height=r.height+'px'; return r;
  }

  var _ganttDepPid = (document.getElementById('gantt-win-dep-open') || {}).getAttribute ? document.getElementById('gantt-win-dep-open').getAttribute('data-projectid') : '';
  var _ganttDepUrl = '<?php echo Yii::$app->urlManager->createUrl("projectsmain/newganttchartdep")?>' + '?layout=false';

  function _loadGanttDepWin(pid) {
    var url = _ganttDepUrl + '&id=' + (pid || _ganttDepPid);
    $('#gantt-win-dep-body').html('<div id="gantt-win-dep-loading">Loading Dependency view&hellip;</div>');
    $.ajax({ url: url, cache: false, success: function(html){
      $('#gantt-win-dep-body').html(html);
    }, error: function(){
      $('#gantt-win-dep-body').html('<div style="padding:30px;color:red;">Failed to load Dependency view.</div>');
    }});
  }

  // Open
  var openBtn = document.getElementById('gantt-win-dep-open');
  if(openBtn){
    openBtn.addEventListener('click', function(e){
      e.preventDefault();
      var pid = this.getAttribute('data-projectid');
      if(!pid){
        win.classList.add('gw-open');
        document.dispatchEvent(new Event('gantt-dep:open'));
        document.getElementById('gantt-win-dep-body').innerHTML = '<div style="padding:50px;text-align:center;color:#888;font-size:14px;">Please select a project first.</div>';
        return;
      }
      win.classList.add('gw-open');
      document.dispatchEvent(new Event('gantt-dep:open'));
      if(!_loaded){
        _loaded = true;
        _loadGanttDepWin(pid);
      }
    });
  }

  // Close
  document.getElementById('gantt-win-dep-close').addEventListener('click', function(){
    win.classList.remove('gw-open');
  });

  // Expand / restore
  document.getElementById('gantt-win-dep-expand').addEventListener('click', function(){
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
    if(e.target.closest('#gantt-win-dep-hdr-btns')) return;
    var r=_anchor(); _action='drag';
    _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault();
  });

  // Resize
  document.querySelectorAll('.gwd-rs').forEach(function(el){
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
  bindDragTouch(document, 'mouseup', function(){
    _action=null;
  });
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

<!-- ══════════════════════════════════════════════════════════════════════
     USER PERMISSIONS FLOATING WINDOW
════════════════════════════════════════════════════════════════════════ -->
<style>
#userpermissions-win {
  display:none; position:fixed; top:80px; left:20px;
  width:900px; height:calc(100vh - 110px);
  min-width:420px; min-height:300px;
  z-index:100000; background:#fff; border-radius:6px;
  border:1px solid #d0d0d0;
  box-shadow:0 4px 16px rgba(0,0,0,.15);
  flex-direction:column; overflow:hidden;
}
#userpermissions-win.pw-open { display:flex; }
#userpermissions-win-hdr {
  background:#3a3f47; color:#fff; padding:7px 15px;
  display:flex; align-items:center; justify-content:space-between;
  cursor:move; user-select:none; flex-shrink:0;
  font-family:'Nunito',sans-serif;
  border-bottom:1px solid #24272c;
}
#userpermissions-win-hdr-btns { display:flex; align-items:center; gap:8px; }
#userpermissions-win-hdr-btns button {
  background:none; border:none; color:#fff; font-size:16px;
  cursor:pointer; line-height:1; padding:0 4px; opacity:.85;
}
#userpermissions-win-hdr-btns button:hover { opacity:1; }
#userpermissions-win-body { flex:1; min-height:0; overflow:auto; }
#userpermissions-win-loading { padding:50px; text-align:center; color:#666; font-size:14px; }
</style>

<div id="userpermissions-win">
  <div class="pw-rs pw-rs-n" data-dir="n"></div>
  <div class="pw-rs pw-rs-s" data-dir="s"></div>
  <div class="pw-rs pw-rs-e" data-dir="e"></div>
  <div class="pw-rs pw-rs-w" data-dir="w"></div>
  <div class="pw-rs pw-rs-ne" data-dir="ne"></div>
  <div class="pw-rs pw-rs-nw" data-dir="nw"></div>
  <div class="pw-rs pw-rs-se" data-dir="se"></div>
  <div class="pw-rs pw-rs-sw" data-dir="sw"></div>
  <div id="userpermissions-win-hdr">
    <div style="display:flex;align-items:baseline;gap:6px;flex-wrap:nowrap;">
      <span style="font-size:14px;font-weight:600;color:#fff;letter-spacing:0.5px;">User Permissions</span>
    </div>
    <div id="userpermissions-win-hdr-btns">
      <button id="userpermissions-win-expand" title="Fullscreen">&#x26F6;</button>
      <button id="userpermissions-win-close" title="Close">&times;</button>
    </div>
  </div>
  <div id="userpermissions-win-body">
    <div id="userpermissions-win-loading">Loading User Permissions&hellip;</div>
  </div>
</div>

<script>
(function(){
  var win = document.getElementById('userpermissions-win');
  var hdr = document.getElementById('userpermissions-win-hdr');
  var MIN_W=500, MIN_H=300, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0, _saved=null;
  var _loaded = false;

  function _anchor(){
    var r=win.getBoundingClientRect();
    win.style.right='auto'; win.style.left=r.left+'px';
    win.style.top=r.top+'px'; win.style.width=r.width+'px';
    win.style.height=r.height+'px'; return r;
  }

  var _userpermissionsUrl = '<?php echo Yii::$app->urlManager->createUrl("userpermissions/index")?>' + '?layout=false';

  function _loadUserpermissionsWin() {
    $('#userpermissions-win-body').html('<div id="userpermissions-win-loading">Loading User Permissions&hellip;</div>');
    $.ajax({ url: _userpermissionsUrl, success: function(html){
      $('#userpermissions-win-body').html(html);
    }, error: function(){
      $('#userpermissions-win-body').html('<div style="padding:30px;color:red;">Failed to load User Permissions.</div>');
    }});
  }

  // Open
  document.addEventListener('click', function(e){
    if(!e.target.closest('.userpermissionslib-btn')) return;
    e.preventDefault();
    win.classList.add('pw-open');
    if(typeof window.popupBringToFront === 'function') window.popupBringToFront('userpermissions-win');
    if(typeof window.popupCascade === 'function') window.popupCascade('userpermissions-win');
    if(!_loaded){
      _loaded = true;
      _loadUserpermissionsWin();
    }
  });

  // Close
  document.getElementById('userpermissions-win-close').addEventListener('click', function(){
    win.classList.remove('pw-open');
  });

  // Expand / restore
  document.getElementById('userpermissions-win-expand').addEventListener('click', function(){
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
    if(e.target.closest('#userpermissions-win-hdr-btns')) return;
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
  var ids = ['pwt-popup','cwbs-popup','cwbsed-popup','pdoc-popup','pdoc-viewer-popup','gpm-overlay','gantt-win','gantt-win-dep','cb-win','cb-btn','reslib-win','vendorlib-win','rel-win','estreport-win','menu4-popup-cntnr','procurement-win','storeoffice-win','userpermissions-win'];
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
  var floaters = ['pwt-popup','cwbs-popup','cwbsed-popup','pdoc-popup','pdoc-viewer-popup','gantt-win','gantt-win-dep','cb-win','reslib-win','vendorlib-win','rel-win','estreport-win','menu4-popup-cntnr','procurement-win','storeoffice-win','userpermissions-win','cd-modal','cf-modal','pd-modal','gkp-popup','gkm-modal','gcm-popup','gpm-overlay'];

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

  /* Shrinks a popup's height to 3/4 of what it just opened at, but only on
     a small/portrait screen (checked via actual window dimensions, not a
     CSS media query — @media (max-width:900px) and an orientation-based
     variant both silently failed to match on iPad Chrome in testing, even
     though the device is clearly in portrait, so this checks the real
     viewport directly instead). Height only — width is left untouched.
     Runs once per open (guarded so repeated calls, e.g. from re-renders,
     don't keep shrinking it further), and only if the user hasn't already
     resized this popup by hand in this session (same drag/resize engine
     that sets inline height also stamps a data attribute the first time
     the user touches it — see each popup's own _anchor()). */
  window.popupFitPortrait = function(id){
    var el = document.getElementById(id) || document.querySelector('.' + id);
    if(!el) return;
    var isSmallScreen = window.innerWidth <= 900 || window.innerHeight > window.innerWidth;
    if(!isSmallScreen) return;
    if(el.dataset.userSized === '1') return; /* user already dragged/resized this popup */
    if(el.dataset.portraitFitted === '1') return; /* already shrunk once this open */
    /* Deferred a frame: called right after addClass('...-open') toggles
       display:flex, before the browser has necessarily laid it out, so an
       immediate getBoundingClientRect() can read a stale/zero height. */
    requestAnimationFrame(function(){
      var r = el.getBoundingClientRect();
      if(r.height < 10) return; /* still not laid out — bail rather than shrink from a bogus height */
      el.style.height = (r.height * 0.55) + 'px';
      el.dataset.portraitFitted = '1';
    });
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
  document.addEventListener('gantt-dep:open', function(){ window.popupBringToFront('gantt-win-dep'); window.popupCascade('gantt-win-dep'); });
  document.addEventListener('chat:open',   function(){ window.popupBringToFront('cb-win'); window.popupCascade('cb-win'); });
  document.addEventListener('reslib:open', function(){ window.popupBringToFront('reslib-win'); window.popupCascade('reslib-win'); });
  document.addEventListener('vendorlib:open', function(){ window.popupBringToFront('vendorlib-win'); window.popupCascade('vendorlib-win'); });
  document.addEventListener('rel:open',    function(){ window.popupBringToFront('rel-win'); window.popupCascade('rel-win'); });
  document.addEventListener('estreport:open', function(){ window.popupBringToFront('estreport-win'); window.popupCascade('estreport-win'); });
  document.addEventListener('actlib:open', function(){ window.popupBringToFront('menu4-popup-cntnr'); window.popupCascade('menu4-popup-cntnr'); });
  document.addEventListener('pwt:open',    function(){ window.popupBringToFront('pwt-popup'); window.popupCascade('pwt-popup'); });
})();
</script>

<!-- ── Responsive overrides: every floating popup goes full-screen on
     tablet/phone. Placed last so it cascades after each popup's own
     inline <style> block regardless of selector specificity. Only
     touches layout (position/size); each popup's internal content
     still scrolls on overflow via its own existing rules. ── -->
<style>
@media (max-width: 900px) {
    #pdoc-popup,
    #pdoc-viewer-popup,
    #gpm-dialog,
    #gantt-win,
    #gantt-win-dep,
    #procurement-win,
    #storeoffice-win,
    #userpermissions-win,
    .menu4-popup-cntnr,
    #reslib-win,
    #vendorlib-win,
    #rel-win,
    #estreport-win {
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        min-width: 0 !important;
        min-height: 0 !important;
        border-radius: 0 !important;
    }

    /* Smaller floating popups (Cost Dashboard, KPI modal, Resource
       Library sub-popup, dashboard modals, performance dashboard) don't
       need — and look worse — full-screen on a tall portrait phone/tablet
       screen: forcing them to 100vh stretched them into a very elongated
       box. These get a max-width/max-height ceiling instead of full-screen.
       Deliberately NOT forcing top/left/transform/width/height with
       !important here (unlike the full-screen group above): each popup's
       own CSS already opens it at a sensible default size/position, and
       the drag/resize engine re-anchors it afterward with inline
       left/top/width/height styles — !important on those properties would
       permanently override the inline styles and make the popup neither
       draggable nor resizable on portrait. Only the max ceiling is set,
       so the user can still drag anywhere and resize smaller/larger up to
       that ceiling. */
    #gcm-popup,
    #cd-modal,
    #cf-modal,
    #pd-modal,
    #gkm-modal,
    #gkp-popup {
        max-width: 94vw !important;
        min-width: 0 !important;
        min-height: 0 !important;
    }

    #gpm-overlay { padding: 0 !important; }

    .rl-subwin, .rl2-subwin {
        width: 94vw !important;
        max-width: 94vw !important;
        left: 3vw !important;
    }

    /* Bootstrap col-md-* doesn't reflow below the md breakpoint by
       design (that's what col-sm-*/col-xs-* are for) — these popups
       only ever used col-md-*, so force every grid column full-width
       here instead of touching every view file's markup. */
    #pdoc-popup [class*="col-md-"],
    .menu4-popup-cntnr [class*="col-md-"],
    #reslib-win [class*="col-md-"],
    #vendorlib-win [class*="col-md-"],
    #rel-win [class*="col-md-"] {
        width: 100% !important;
        float: none !important;
    }
}

@media (max-width: 480px) {
    #pdoc-popup table,
    .menu4-popup-cntnr table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
}
</style>

<?php $this->endPage() ?>
