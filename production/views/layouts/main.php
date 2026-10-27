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
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#1a0a2e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Opiam">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/icons/opiam-180.png">
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
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/developer2.css?v=20261027c" rel="stylesheet">
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
        .cwbs-hdr-btn { background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.35); color:#fff; border-radius:14px;
            padding:3px 12px; font-size:12px; font-weight:700; cursor:pointer; margin-right:8px; white-space:nowrap; }
        .cwbs-hdr-btn:hover { background:rgba(255,255,255,0.32); }
        #cwbs-tabs { display:flex; gap:6px; padding:8px 14px 0; border-bottom:2px solid #0f5c52; flex-shrink:0; }
        .cwbs-tab { background:#e3efec; color:#0d3b4f; border:none; border-radius:8px 8px 0 0; padding:7px 20px; font-size:13px; font-weight:700; cursor:pointer; }
        .cwbs-tab:hover { background:#d1e6e1; }
        .cwbs-tab.on { background:#0f5c52; color:#fff; }
        #cwbs-popup #cwbs-main { display:none; }
        #cwbs-popup.cwbs-acts #cwbs-main { display:flex; }
        .cwbs-hp { position:fixed; display:none; flex-direction:column; width:560px; max-width:94vw; max-height:80vh; background:#fff; border-radius:10px;
            box-shadow:0 12px 40px rgba(0,0,0,.35); overflow:hidden; font-family:Nunito,Arial,sans-serif; }
        .cwbs-hp.on { display:flex; }
        .cwbs-hp-hdr { background:linear-gradient(135deg,#0d3b4f,#0f5c52); color:#fff; padding:10px 14px; display:flex; align-items:center; cursor:move; }
        .cwbs-hp-hdr h4 { margin:0; font-size:14px; font-weight:700; flex:1; }
        .cwbs-hp-x { background:rgba(255,255,255,0.2); border:none; color:#fff; width:26px; height:26px; border-radius:50%; font-size:16px; line-height:1; cursor:pointer; }
        .cwbs-hp-body { padding:12px 14px; overflow-y:auto; flex:1; min-height:0; }
        .cwbs-hp-row { display:flex; gap:8px; align-items:flex-end; margin-bottom:10px; }
        .cwbs-hp-row label { font-size:11px; font-weight:700; color:#0d3b4f; display:block; margin-bottom:3px; }
        .cwbs-hp-row input, .cwbs-hp-row select { width:100%; box-sizing:border-box; border:1px solid #bcd0cc; border-radius:5px; padding:6px 8px; font-size:12px; height:32px; outline:none; }
        .cwbs-hp-msg { font-size:12px; min-height:16px; margin-bottom:6px; }
        .cwbs-hp table { width:100%; border-collapse:collapse; font-size:12.5px; }
        .cwbs-hp td { border:1px solid #d6e3e0; padding:5px 8px; }
        .cwbs-hp th { background:#0f5c52; color:#fff; font-size:11.5px; font-weight:700; padding:6px 8px; border:1px solid #0d4d45; text-align:left; }
        .cwbs-hp th.n, .cwbs-hp th.c, .cwbs-hp th.b { text-align:center; }
        .cwbs-hp td.n { width:34px; text-align:center; color:#888; }
        .cwbs-hp td.c { width:80px; text-align:center; color:#6c7a89; } .cwbs-hp th.c { width:80px; }
        .cwbs-hp td.b, .cwbs-hp th.b { width:150px; text-align:center; white-space:nowrap; }
        .cwbs-hp-ib { border:1px solid #bcd0cc; background:#fff; border-radius:4px; width:26px; height:24px; cursor:pointer; font-size:12px; padding:0; margin-left:2px; }
        .cwbs-hp-ib:hover { border-color:#0f5c52; }
        .cwbs-hp-ib.del { color:#c0392b; }
        .cwbs-hp-names .cwbs-hp-nrow { display:flex; gap:6px; margin-bottom:6px; }
        .cwbs-hp-plus { border:1px solid #0f5c52; background:#fff; color:#0f5c52; border-radius:50%; width:32px; height:32px; font-size:20px; line-height:1; cursor:pointer; flex-shrink:0; padding:0; }
        .cwbs-hp-plus:hover { background:#e3f1ee; }
        .cwbs-hp-nrm { border:1px solid #e0b4ae; background:#fff; color:#c0392b; border-radius:5px; width:32px; height:32px; cursor:pointer; flex-shrink:0; padding:0; }
        .cwbs-hp-addrow { text-align:right; margin:4px 0 10px; }
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
        /* Searchable IOW Group: a text box over the hidden select, with a match list. */
        .cwbs-ss { position:relative; }
        .cwbs-ss input { width:100%; box-sizing:border-box; border:1px solid #bcd0cc; border-radius:5px; padding:6px 26px 6px 8px; font-size:12px; height:32px; background:#fff; outline:none; }
        .cwbs-ss input { cursor:pointer; }
        .cwbs-ss input:focus { border-color:#0f5c52; cursor:text; }
        .cwbs-ss:after { content:'\25BE'; position:absolute; right:9px; top:7px; font-size:12px; color:#555; pointer-events:none; }
        .cwbs-ss-list { position:absolute; left:0; right:0; top:34px; max-height:260px; overflow-y:auto; background:#fff; border:1px solid #bcd0cc; border-radius:5px; box-shadow:0 6px 18px rgba(0,0,0,.18); z-index:50; display:none; }
        .cwbs-ss-item { padding:6px 10px; font-size:12px; cursor:pointer; }
        .cwbs-ss-item:hover, .cwbs-ss-item.on { background:#e3f1ee; }
        .cwbs-ss-none { padding:6px 10px; font-size:12px; color:#888; }
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
                    <a class="icon-rocket pwt-nav-btn" id="pwt-nav-btn" title="Create WBS" href="#"></a>
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

<?php if (!Yii::$app->user->isGuest): ?>
<!-- ── Notifications (bell next to the chatbot) ───────────────────────────
     Messages, alerts and action plans for the user / selected project. The bell
     is grey when nothing is unread, red (alerts) or blue (messages only) with a
     count when something is. Clicking opens a draggable, resizable window on top
     of everything (Cashflow dashboard included). Items count as read once their
     tab has been opened (remembered in this browser). Other code can add an item
     with window.cfNotify('messages' | 'alerts' | 'action_plans', title, text). -->
<style>
#nt-btn{position:fixed;bottom:24px;right:78px;z-index:100000;width:44px;height:44px;border-radius:50%;background:#8a94a6;color:#fff;border:none;cursor:pointer;box-shadow:0 4px 16px rgba(0,0,0,.35);display:flex;align-items:center;justify-content:center;transition:background .2s}
#nt-btn.nt-alert{background:#d84a30}
#nt-btn.nt-msg{background:#1565c0}
#nt-badge{position:absolute;top:-4px;right:-4px;min-width:18px;height:18px;padding:0 5px;box-sizing:border-box;border-radius:9px;background:#fff;color:#d84a30;font:700 11px/18px Nunito,Arial,sans-serif;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,.3);display:none}
#nt-win{position:fixed;right:24px;bottom:80px;width:380px;height:480px;min-width:280px;min-height:220px;max-width:calc(100vw - 16px);max-height:calc(100vh - 16px);z-index:100600;background:#f0f2f7;border-radius:8px;box-shadow:0 8px 32px rgba(0,0,0,.45);display:none;flex-direction:column;overflow:hidden;font-family:Nunito,Arial,sans-serif}
#nt-hdr{flex:none;display:flex;align-items:center;justify-content:space-between;background:#1a0a2e;color:#fff;padding:8px 12px;cursor:move;user-select:none;touch-action:none;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.03em}
#nt-close{background:none;border:none;color:#fff;font-size:20px;line-height:1;cursor:pointer;padding:0 2px}
#nt-tabs{flex:none;display:flex;background:#fff;border-bottom:1px solid #c8d0e0}
.nt-tab{flex:1;background:none;border:none;border-bottom:3px solid transparent;padding:8px 4px;font-size:12px;font-weight:700;color:#5a6e8c;cursor:pointer}
.nt-tab.on{color:#081142;border-bottom-color:#1a0a2e}
.nt-n{display:inline-block;min-width:16px;padding:0 4px;border-radius:8px;background:#c8d0e0;color:#081142;font-size:10px;line-height:16px;margin-left:3px}
.nt-n.unread{background:#d84a30;color:#fff}
#nt-list{flex:1;min-height:0;overflow-y:auto;padding:8px}
.nt-item{background:#fff;border-left:4px solid #8a94a6;border-radius:4px;padding:8px 10px;margin-bottom:6px;box-shadow:0 1px 2px rgba(0,0,0,.08)}
.nt-item b{display:block;font-size:12px;color:#081142;margin-bottom:2px}
.nt-item span{font-size:12px;color:#333;line-height:1.4}
#nt-rs{position:absolute;right:0;bottom:0;width:18px;height:18px;cursor:nwse-resize;touch-action:none;background:linear-gradient(135deg,transparent 50%,#8a94a6 50%,#8a94a6 60%,transparent 60%,transparent 72%,#8a94a6 72%,#8a94a6 82%,transparent 82%)}
@media(max-width:600px){#nt-btn{width:42px;height:42px;bottom:16px;right:66px}}
/* WhatsApp button, left of the bell: opens WhatsApp (app on phone/iPad, WhatsApp Web on a PC) to pick a contact */
#wa-btn{position:fixed;bottom:24px;right:132px;z-index:100000;width:44px;height:44px;border-radius:50%;background:#25d366;border:none;cursor:pointer;box-shadow:0 4px 16px rgba(0,0,0,.35);display:flex;align-items:center;justify-content:center;padding:0;text-decoration:none}
#wa-btn:hover{background:#1ebe5b}
@media(max-width:600px){#wa-btn{width:42px;height:42px;bottom:16px;right:116px}}
</style>
<script>
// WhatsApp without leaving the app: phones/iPads open the WhatsApp app directly
// (whatsapp:// link, the page stays where it is); a PC opens WhatsApp Web in its
// own side window, reused on later clicks instead of opening another one.
document.addEventListener('click', function(e){
  var a = e.target.closest ? e.target.closest('#wa-btn') : null;
  if (!a) return;
  e.preventDefault();
  var touch = /iPad|iPhone|Android|Mobile/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
  if (touch) { window.location.href = 'whatsapp://send'; return; }
  var w = Math.min(860, screen.availWidth - 80), h = Math.min(600, screen.availHeight - 80);
  var win = window.open('https://web.whatsapp.com/', 'opiam_whatsapp', 'width=' + w + ',height=' + h + ',left=' + (screen.availWidth - w - 20) + ',top=40');
  if (win) win.focus();
});
</script>
<a id="wa-btn" href="https://web.whatsapp.com/" title="WhatsApp"><svg width="24" height="24" viewBox="0 0 24 24" fill="#fff"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91A9.85 9.85 0 0 0 12.04 2zm0 18.15h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.22 8.22 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24a8.2 8.2 0 0 1 8.24 8.25c0 4.54-3.7 8.23-8.24 8.23zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.16.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.15.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.23.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.1-.22-.16-.47-.28z"/></svg></a>
<button type="button" id="nt-btn" title="Notifications"><svg width="20" height="20" viewBox="0 0 24 24" fill="#fff"><path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22zm7-6V11a7 7 0 0 0-5.5-6.84V3a1.5 1.5 0 0 0-3 0v1.16A7 7 0 0 0 5 11v5l-2 2v1h18v-1l-2-2z"/></svg><span id="nt-badge"></span></button>
<div id="nt-win">
  <div id="nt-hdr"><span>Notifications</span><span style="display:flex;align-items:center;gap:10px;"><button type="button" id="nt-push-icon" title="Turn on notifications for this device" style="display:none;background:none;border:1px solid rgba(255,255,255,.5);border-radius:12px;color:#fff;font-size:11px;cursor:pointer;padding:1px 8px;line-height:16px;">&#128276;+</button><button type="button" id="nt-close" title="Close">&times;</button></span></div>
  <div id="nt-tabs">
    <button type="button" class="nt-tab" data-tab="messages">Messages<span class="nt-n" data-n="messages">0</span></button>
    <button type="button" class="nt-tab" data-tab="alerts">Alerts<span class="nt-n" data-n="alerts">0</span></button>
    <button type="button" class="nt-tab" data-tab="action_plans">Action Plans<span class="nt-n" data-n="action_plans">0</span></button>
  </div>
  <div id="nt-list"></div>
  <div id="nt-push" style="flex:none;display:none;align-items:center;justify-content:space-between;gap:8px;padding:6px 24px 6px 10px;border-top:1px solid #d5dbe5;background:#fff;font-size:11px;color:#5a6e8c;"></div>
  <div id="nt-rs" title="Resize"></div>
</div>
<script>
(function(){
  var url = '<?php echo Yii::$app->urlManager->createAbsoluteUrl(["/projectsmain/cfnotifications"]); ?>';
  var KINDS = ['messages', 'alerts', 'action_plans'];
  var COL = { messages: '#1565c0', alerts: '#d84a30', action_plans: '#2ba899' };
  var EMPTY = { messages: 'No messages.', alerts: 'No alerts.', action_plans: 'No action plans.' };
  var data = { messages: [], alerts: [], action_plans: [] }, tab = null, local = 0;
  var btn = document.getElementById('nt-btn'), win = document.getElementById('nt-win');
  function key(kind, it){ return kind + ':' + (it.id != null ? it.id : (it.title || '') + '|' + (it.text || '')); }
  function seen(){ try { return JSON.parse(localStorage.getItem('ntSeen') || '{}'); } catch (e) { return {}; } }
  function markSeen(kind){
    var s = seen(); (data[kind] || []).forEach(function(it){ s[key(kind, it)] = 1; });
    try { localStorage.setItem('ntSeen', JSON.stringify(s)); } catch (e) {}
  }
  // Unread = not yet seen in this browser; for action plans only open ones count.
  function unread(kind){ var s = seen(); return (data[kind] || []).filter(function(it){ return !s[key(kind, it)] && stOf(kind, it) !== 'archive'; }).length; }
  // Status of an item: action plans are Pending, Overdue (past target date) or
  // Archive (done); messages and alerts are Pending or Archive (archived).
  var SCOL = { pending: '#2ba899', overdue: '#d84a30', archive: '#9aa3b2' };
  var SLBL = { pending: 'Pending', overdue: 'Overdue', archive: 'Archive' };
  // Every item is listed (archived ones last); its status shows only as a coloured dot.
  var filt = { messages: { pending: 1, archive: 1 }, alerts: { pending: 1, archive: 1 }, action_plans: { pending: 1, overdue: 1, archive: 1 } };
  function stOf(kind, it){
    if (kind === 'action_plans') return +it.status === 1 ? 'archive' : (it.target_date && String(it.target_date).substr(0, 10) < today ? 'overdue' : 'pending');
    return +it.archived ? 'archive' : 'pending';
  }
  function dot(st){ return '<span title="' + SLBL[st] + '" style="flex:none;display:inline-block;width:9px;height:9px;border-radius:50%;background:' + SCOL[st] + ';margin-right:6px;vertical-align:middle;"></span>'; }
  function chips(kind){
    var all = data[kind] || [];
    return '<div style="display:flex;gap:6px;margin-bottom:8px;">' + Object.keys(filt[kind]).map(function(st){
      var n = all.filter(function(it){ return stOf(kind, it) === st; }).length, on = filt[kind][st];
      return '<button type="button" class="nt-chip" data-st="' + st + '" style="display:flex;align-items:center;gap:0;background:' + (on ? '#fff' : 'transparent') + ';border:1px solid ' + (on ? SCOL[st] : '#c8d0e0') + ';border-radius:12px;padding:3px 10px;font-size:11px;font-weight:700;color:' + (on ? '#081142' : '#8a97ad') + ';cursor:pointer;">' + dot(st) + SLBL[st] + ' ' + n + '</button>';
    }).join('') + '</div>';
  }
  function shown(kind){ return (data[kind] || []).filter(function(it){ return filt[kind][stOf(kind, it)]; }); }
  function esc(t){ var d = document.createElement('div'); d.textContent = t == null ? '' : String(t); return d.innerHTML; }
  function render(){
    var ua = unread('alerts'), um = unread('messages'), up = unread('action_plans'), total = ua + um + up;
    btn.classList.toggle('nt-alert', ua > 0 || up > 0);
    btn.classList.toggle('nt-msg', ua === 0 && up === 0 && um > 0);
    var b = document.getElementById('nt-badge');
    b.style.display = total ? 'block' : 'none'; b.textContent = total > 99 ? '99+' : total;
    b.style.color = (ua || up) ? '#d84a30' : '#1565c0';
    btn.title = total ? total + ' unread notification' + (total > 1 ? 's' : '') : 'Notifications';
    KINDS.forEach(function(k){
      var n = win.querySelector('.nt-n[data-n="' + k + '"]'); n.textContent = (data[k] || []).filter(function(it){ return stOf(k, it) !== 'archive'; }).length;
      n.classList.toggle('unread', unread(k) > 0 && k !== tab);
    });
    if (win.style.display !== 'flex') return;
    win.querySelectorAll('.nt-tab').forEach(function(t){ t.classList.toggle('on', t.getAttribute('data-tab') === tab); });
    if (tab === 'action_plans') { if (!(editing !== null && document.querySelector('#nt-list .ap-form'))) renderPlans(); return; }   // keep a half-written plan during the 2-minute refresh
    var items = shown(tab), b2 = 'background:none;border:none;cursor:pointer;font-size:12px;line-height:1;padding:2px 4px;color:#5a6e8c;';
    document.getElementById('nt-list').innerHTML = (items.length ? items.map(function(it){
      var st = stOf(tab, it), arch = st === 'archive';
      return '<div class="nt-item" data-id="' + esc(it.id) + '" style="display:flex;align-items:flex-start;gap:6px;border-left-color:' + SCOL[st] + ';' + (arch ? 'opacity:.6;' : '') + '"><div style="flex:1;min-width:0;"><b>' + dot(st) + esc(it.title) + '</b><span>' + esc(it.text) + '</span></div>'
        + (it._local ? '' : '<span style="flex:none;display:flex;flex-direction:column;gap:2px;"><button type="button" class="nt-arch" data-arch="' + (arch ? 0 : 1) + '" title="' + (arch ? 'Restore' : 'Archive') + '" style="' + b2 + '">' + (arch ? '&#8634;' : '&#128451;') + '</button>'
          + '<button type="button" class="nt-del" title="Delete" style="' + b2 + 'color:#c0392b;font-size:15px;">&times;</button></span>') + '</div>';
    }).join('') : '<div style="text-align:center;color:#8a97ad;font-size:12px;padding:24px 8px;">' + EMPTY[tab] + '</div>');
  }
  // ── Action plans: written by users, one strip each ──────────────────────
  var apUrl = '<?php echo Yii::$app->urlManager->createAbsoluteUrl(["/projectsmain/actionplansave"]); ?>';
  var apStatusUrl = '<?php echo Yii::$app->urlManager->createAbsoluteUrl(["/projectsmain/actionplanstatus"]); ?>';
  var archUrl = '<?php echo Yii::$app->urlManager->createAbsoluteUrl(["/projectsmain/notifarchive"]); ?>';
  var delUrl = '<?php echo Yii::$app->urlManager->createAbsoluteUrl(["/projectsmain/notifdelete"]); ?>';
  var users = [], today = '', editing = null;   // editing: null (closed), 0 (new) or a plan id
  function post(u, params, done){
    var x = new XMLHttpRequest(), body = Object.keys(params).map(function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(params[k] == null ? '' : params[k]); }).join('&');
    x.open('POST', u); x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); x.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    x.onload = function(){ var r = null; try { r = JSON.parse(x.responseText); } catch (e) {} done(r); };
    x.send(body);
  }
  function fmtDate(d){ if (!d) return ''; var p = String(d).substr(0, 10).split('-'); var m = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][+p[1] - 1]; return (+p[2]) + ' ' + m + ' ' + p[0]; }
  function planForm(pl){
    pl = pl || {};
    var opts = '<option value="">— Responsible —</option>' + users.map(function(u){ return '<option value="' + u.id + '"' + (String(u.id) === String(pl.responsible_user_id || '') ? ' selected' : '') + '>' + esc(u.username) + '</option>'; }).join('');
    var fld = 'box-sizing:border-box;border:1px solid #c5ccd4;border-radius:4px;font:12px Nunito,Arial,sans-serif;color:#333;';
    return '<div class="ap-form" style="background:#fff;border-radius:6px;padding:10px;margin-bottom:8px;box-shadow:0 1px 3px rgba(0,0,0,.12);">'
      + '<textarea class="ap-text" rows="3" placeholder="Write the action plan..." style="' + fld + 'width:100%;padding:6px 8px;resize:vertical;">' + esc(pl.text || '') + '</textarea>'
      + '<div style="display:flex;gap:6px;margin-top:6px;"><select class="ap-resp" style="' + fld + 'flex:1;min-width:0;height:30px;padding:2px 6px;">' + opts + '</select>'
      + '<input type="date" class="ap-date" value="' + (pl.target_date ? String(pl.target_date).substr(0, 10) : (pl.id ? '' : today)) + '" title="Target date" style="' + fld + 'width:140px;height:30px;padding:2px 6px;"></div>'
      + '<div style="text-align:right;margin-top:8px;"><button type="button" class="ap-cancel" style="background:#f0f4f8;border:1px solid #c5ccd4;border-radius:14px;padding:4px 14px;font-size:12px;color:#465365;cursor:pointer;margin-right:6px;">Cancel</button>'
      + '<button type="button" class="ap-save" style="background:#1a0a2e;color:#fff;border:none;border-radius:14px;padding:4px 16px;font-size:12px;cursor:pointer;">Save</button></div></div>';
  }
  function renderPlans(){
    var list = document.getElementById('nt-list'), plans = shown('action_plans');
    var html = (editing === 0 ? planForm(null)
      : '<button type="button" class="ap-new" style="width:100%;margin-bottom:8px;background:#fff;border:1px dashed #8a94a6;border-radius:6px;padding:7px;font-size:12px;font-weight:700;color:#1a0a2e;cursor:pointer;">+ New Action Plan</button>');
    plans.forEach(function(pl){
      if (editing === +pl.id) { html += planForm(pl); return; }
      var done = +pl.status === 1, late = !done && pl.target_date && String(pl.target_date).substr(0, 10) < today;
      var edge = SCOL[stOf('action_plans', pl)];
      var meta = [];
      if (pl.responsible) meta.push('<span title="Responsible">&#128100; ' + esc(pl.responsible) + '</span>');
      if (pl.target_date) meta.push('<span title="Target date" style="' + (late ? 'color:#d84a30;font-weight:700;' : '') + '">&#128197; ' + fmtDate(pl.target_date) + (late ? ' · overdue' : '') + '</span>');
      meta.push('<span title="Written by">' + esc(pl.created_by || '') + ', ' + fmtDate(pl.created_at) + '</span>');
      var b = 'background:none;border:none;cursor:pointer;font-size:13px;line-height:1;padding:2px 3px;color:#5a6e8c;';
      html += '<div class="ap-strip" data-id="' + pl.id + '" style="display:flex;align-items:stretch;background:#fff;border-radius:6px;margin-bottom:6px;box-shadow:0 1px 3px rgba(0,0,0,.10);overflow:hidden;' + (done ? 'opacity:.6;' : '') + '">'
        + '<div style="flex:none;width:5px;background:' + edge + ';"></div>'
        + '<div style="flex:1;min-width:0;padding:8px 10px;">'
        + '<div style="font-size:12.5px;color:#111;line-height:1.4;white-space:pre-wrap;word-wrap:break-word;' + (done ? 'text-decoration:line-through;' : '') + '">' + dot(stOf('action_plans', pl)) + esc(pl.text) + '</div>'
        + '<div style="display:flex;flex-wrap:wrap;gap:4px 12px;margin-top:4px;font-size:11px;color:#6b7a90;">' + meta.join('') + '</div></div>'
        + '<div style="flex:none;display:flex;flex-direction:column;justify-content:center;gap:2px;padding:4px 6px;border-left:1px solid #eef1f5;">'
        + '<button type="button" class="ap-done" title="' + (done ? 'Reopen' : 'Mark done') + '" style="' + b + (done ? '' : 'color:#2ba899;') + '">' + (done ? '&#8634;' : '&#10003;') + '</button>'
        + (done ? '' : '<button type="button" class="ap-edit" title="Edit" style="' + b + '">&#9998;</button>')
        + '<button type="button" class="ap-del" title="Delete" style="' + b + 'color:#c0392b;">&times;</button></div></div>';
    });
    if (!plans.length && editing !== 0) html += '<div style="text-align:center;color:#8a97ad;font-size:12px;padding:16px 8px;">' + ((data.action_plans || []).length ? 'No action plans with this status.' : 'No action plans yet.') + '</div>';
    list.innerHTML = html;
  }
  document.getElementById('nt-list').addEventListener('click', function(e){
    var t = e.target.closest('button'); if (!t) return;
    if (t.classList.contains('nt-chip')) { var st = t.getAttribute('data-st'); filt[tab][st] = filt[tab][st] ? 0 : 1; render(); return; }
    if (t.classList.contains('nt-del')) {
      if (!confirm('Delete this ' + (tab === 'messages' ? 'message' : 'alert') + '? It cannot be restored.')) return;
      post(delUrl, { kind: tab, id: t.closest('.nt-item').getAttribute('data-id') }, function(){ load(); });
      return;
    }
    if (t.classList.contains('nt-arch')) {
      var item = t.closest('.nt-item');
      post(archUrl, { kind: tab, id: item.getAttribute('data-id'), archive: t.getAttribute('data-arch') }, function(){ load(); });
      return;
    }
    var strip = t.closest('.ap-strip'), id = strip ? +strip.getAttribute('data-id') : 0;
    if (t.classList.contains('ap-new')) { editing = 0; renderPlans(); var ta = document.querySelector('#nt-list .ap-text'); if (ta) ta.focus(); }
    else if (t.classList.contains('ap-cancel')) { editing = null; renderPlans(); }
    else if (t.classList.contains('ap-edit')) { editing = id; renderPlans(); }
    else if (t.classList.contains('ap-save')) {
      var f = t.closest('.ap-form'), txt = f.querySelector('.ap-text').value.trim();
      if (!txt) { f.querySelector('.ap-text').focus(); return; }
      t.disabled = true; t.textContent = 'Saving...';
      post(apUrl, { id: editing || '', text: txt, responsible: f.querySelector('.ap-resp').value, target_date: f.querySelector('.ap-date').value }, function(r){
        if (r && r.error === 'No') { editing = null; load(); } else { t.disabled = false; t.textContent = 'Save'; alert((r && r.errortext) || 'Could not save the action plan.'); }
      });
    }
    else if (t.classList.contains('ap-done')) {
      var pl = (data.action_plans || []).filter(function(p){ return +p.id === id; })[0];
      post(apStatusUrl, { id: id, status: pl && +pl.status === 1 ? 0 : 1 }, function(){ load(); });
    }
    else if (t.classList.contains('ap-del')) {
      if (!confirm('Delete this action plan?')) return;
      post(apStatusUrl, { id: id, delete: 1 }, function(){ load(); });
    }
  });

  function load(){
    var x = new XMLHttpRequest();
    x.open('POST', url); x.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    x.onload = function(){
      try { var r = JSON.parse(x.responseText); } catch (e) { return; }
      if (!r || r.error !== 'No') return;
      users = r.users || []; today = r.today || '';
      KINDS.forEach(function(k){
        var mine = (data[k] || []).filter(function(it){ return it._local; });   // keep items added on this page
        data[k] = mine.concat(r[k] || []);
      });
      if (win.style.display === 'flex' && tab) markSeen(tab);   // items arriving on the open tab count as read
      render();
    };
    x.send();
  }
  function selectTab(k){ tab = k; editing = null; markSeen(k); render(); }
  btn.addEventListener('click', function(){
    if (win.style.display === 'flex') { win.style.display = 'none'; return; }
    win.style.display = 'flex';
    if (!tab) tab = unread('alerts') ? 'alerts' : unread('messages') ? 'messages' : unread('action_plans') ? 'action_plans' : 'alerts';
    selectTab(tab);
  });
  document.getElementById('nt-close').addEventListener('click', function(){ win.style.display = 'none'; });
  win.querySelectorAll('.nt-tab').forEach(function(t){ t.addEventListener('click', function(){ selectTab(t.getAttribute('data-tab')); }); });
  // Drag by the title bar and resize from the corner (mouse and touch).
  function pt(e){ return e.touches ? e.touches[0] : e; }
  function pin(){ var r = win.getBoundingClientRect(); win.style.left = r.left + 'px'; win.style.top = r.top + 'px'; win.style.right = 'auto'; win.style.bottom = 'auto'; return r; }
  var op = null;
  function start(kind, e){
    if (e.target.closest && e.target.closest('#nt-close')) return;
    var p = pt(e), r = pin();
    op = { kind: kind, x: p.clientX, y: p.clientY, l: r.left, t: r.top, w: r.width, h: r.height };
    if (!e.touches) e.preventDefault();
  }
  function move(e){
    if (!op) return;
    var p = pt(e), dx = p.clientX - op.x, dy = p.clientY - op.y;
    if (op.kind === 'drag') {
      win.style.left = Math.max(0, Math.min(window.innerWidth - 60, op.l + dx)) + 'px';
      win.style.top = Math.max(0, Math.min(window.innerHeight - 40, op.t + dy)) + 'px';
    } else {
      win.style.width = Math.max(280, op.w + dx) + 'px';
      win.style.height = Math.max(220, op.h + dy) + 'px';
    }
    if (e.touches) e.preventDefault();
  }
  function end(){ op = null; }
  var hdr = document.getElementById('nt-hdr'), rs = document.getElementById('nt-rs');
  hdr.addEventListener('mousedown', function(e){ start('drag', e); });
  hdr.addEventListener('touchstart', function(e){ if (e.touches.length === 1) start('drag', e); }, { passive: true });
  rs.addEventListener('mousedown', function(e){ start('size', e); e.stopPropagation(); });
  rs.addEventListener('touchstart', function(e){ if (e.touches.length === 1) start('size', e); }, { passive: true });
  document.addEventListener('mousemove', move);
  document.addEventListener('touchmove', move, { passive: false });
  document.addEventListener('mouseup', end);
  document.addEventListener('touchend', end);
  window.cfNotify = function(kind, title, text){
    if (!data[kind]) return;
    data[kind].unshift({ id: 'local' + (++local) + '-' + Date.now(), title: title, text: text, _local: true });
    render();
  };
  // ── Push notifications to this phone / computer ─────────────────────────
  var pushKeyUrl = '<?php echo Yii::$app->urlManager->createAbsoluteUrl(["/projectsmain/pushkey"]); ?>';
  var pushSubUrl = '<?php echo Yii::$app->urlManager->createAbsoluteUrl(["/projectsmain/pushsubscribe"]); ?>';
  var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
  var standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
  var swReg = null;
  function b64ToBytes(b){ b = (b + '===='.slice((b.length % 4) || 4)).replace(/-/g, '+').replace(/_/g, '/'); var r = atob(b), a = new Uint8Array(r.length); for (var i = 0; i < r.length; i++) a[i] = r.charCodeAt(i); return a; }
  // Messages about turning notifications on show only while needed (bar hidden otherwise).
  var pushHint = '';
  function pushBar(html){ var el = document.getElementById('nt-push'); el.innerHTML = html; el.style.display = html ? 'flex' : 'none'; }
  function pushIcon(show, hint){ pushHint = hint || ''; document.getElementById('nt-push-icon').style.display = show ? 'inline-block' : 'none'; }
  function pushBtn(label){ return '<button type="button" id="nt-push-on" style="flex:none;background:#1a0a2e;color:#fff;border:none;border-radius:12px;padding:4px 12px;font-size:11px;font-weight:700;cursor:pointer;">' + label + '</button>'; }
  function saveSub(sub, done){ post(pushSubUrl, { subscription: JSON.stringify(sub) }, function(r){ if (done) done(r); }); }
  function pushState(){
    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
      pushBar('');
      if (isIOS && !standalone) pushIcon(true, '<span>&#128241; To get notifications on this iPhone/iPad: tap <b>Share</b> &rarr; <b>Add to Home Screen</b> (Safari or Chrome), then open Opiam from that icon.</span>');
      else pushIcon(false);
      return;
    }
    if (Notification.permission === 'denied') { pushBar(''); pushIcon(true, '<span>Notifications are blocked for Opiam. Allow them in this browser\'s site settings.</span>'); return; }
    (swReg ? Promise.resolve(swReg) : navigator.serviceWorker.ready).then(function(reg){ return reg.pushManager.getSubscription(); }).then(function(sub){
      if (sub && Notification.permission === 'granted') { pushBar(''); pushIcon(false); saveSub(sub); }
      else { pushBar(''); pushIcon(true, ''); }
    }).catch(function(){ pushBar(''); pushIcon(true, ''); });
  }
  function enablePush(){
    pushBar('<span>Turning notifications on...</span>');
    Notification.requestPermission().then(function(p){
      if (p !== 'granted') { pushState(); return; }
      var x = new XMLHttpRequest(); x.open('POST', pushKeyUrl); x.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      x.onload = function(){
        var k = null; try { k = JSON.parse(x.responseText).key; } catch (e) {}
        if (!k) { pushBar('<span>Could not turn notifications on. Try again.</span>' + pushBtn('Retry')); return; }
        navigator.serviceWorker.ready.then(function(reg){
          return reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: b64ToBytes(k) });
        }).then(function(sub){ saveSub(sub, function(){ pushState(); }); })
          .catch(function(){ pushBar('<span>Could not turn notifications on. Try again.</span>' + pushBtn('Retry')); });
      };
      x.send();
    });
  }
  document.getElementById('nt-push').addEventListener('click', function(e){ if (e.target.id === 'nt-push-on') enablePush(); });
  // Header bell: turn notifications on here, or show what to do on this device.
  document.getElementById('nt-push-icon').addEventListener('click', function(e){
    e.stopPropagation();
    if (pushHint) { var el = document.getElementById('nt-push'); if (el.style.display === 'flex') pushBar(''); else pushBar(pushHint); return; }
    enablePush();
  });
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/opiam-sw.js', { scope: '/' }).then(function(reg){ swReg = reg; pushState(); }).catch(function(){ pushState(); });
    // A tapped notification opens this window.
    navigator.serviceWorker.addEventListener('message', function(e){ if (e.data && e.data.type === 'opiam-open-notifications' && win.style.display !== 'flex') btn.click(); });
  } else pushState();
  if (/[?&]notif=1/.test(location.search)) setTimeout(function(){ if (win.style.display !== 'flex') { tab = 'action_plans'; btn.click(); } }, 800);
  // Clear the app-icon badge once the window has been opened.
  btn.addEventListener('click', function(){ if (navigator.clearAppBadge) navigator.clearAppBadge().catch(function(){}); });

  window.ntReload = load;
  load();
  setInterval(load, 120000);   // refresh every 2 minutes
})();
</script>
<!-- ── End Notifications ──────────────────────────────────────────────────── -->
<?php endif; ?>

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
                <label>Billing Cycle (days) <span class="text-danger">*</span></label>
                <input type="number" min="1" step="1" id="gpm_billingcycle" class="form-control" placeholder="e.g. 45">
                <span class="text-danger gpm-err" id="gpm_billingcycle_err" style="display:none;font-size:12px;"></span>
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
        if(!(parseInt($('#gpm_billingcycle').val(), 10) > 0)) { $('#gpm_billingcycle_err').text('Enter billing cycle (days)').show(); err=1; }
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

<!-- ── Create WBS Floating Popup ──────────────────────────────────────── -->
<div id="cwbs-popup">
  <div id="cwbs-header">
    <span style="font-size:15px;opacity:.85;">&#128640;</span>
    <h4>Create WBS</h4>
    <button type="button" class="cwbs-hdr-btn" id="cwbs-open-gantt" title="Open the Gantt chart on top, to check the added activities" style="margin-left:auto;">Gantt Chart</button>
    <button type="button" class="cwbs-hdr-btn" id="cwbs-open-actlib" title="Open the Activity Library on top, to add a new activity">Activity Library</button>
    <button id="cwbs-close-x">&times;</button>
  </div>
  <div id="cwbs-tabs">
    <button type="button" class="cwbs-tab" id="cwbs-tab-iow">IOW Group</button>
    <button type="button" class="cwbs-tab" id="cwbs-tab-sg">Sub Group</button>
    <button type="button" class="cwbs-tab" id="cwbs-tab-act">Activities</button>
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
        <div class="cwbs-f">
          <label>Sub Group</label>
          <select id="cwbs-subgroup" style="border:1px solid #bcd0cc;border-radius:5px;padding:6px 8px;font-size:12px;height:32px;"><option value="">None (directly under the IOW Group)</option></select>
        </div>
        <div class="cwbs-f">
          <label>Search Activity</label>
          <input type="text" id="cwbs-act-search" placeholder="Type to search activities" autocomplete="off" style="border:1px solid #bcd0cc;border-radius:5px;padding:6px 8px;font-size:12px;height:32px;outline:none;">
        </div>
      </div>
      <div id="cwbs-msg"></div>
      <div id="cwbs-list-wrap">
        <div id="cwbs-empty" class="cwbs-placeholder">Select a Project Type to list activities.</div>
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

<!-- ── Create WBS: IOW Group / Sub Group headings of this project's Gantt ── -->
<div id="cwbs-iowp" class="cwbs-hp">
  <div class="cwbs-hp-hdr"><h4>IOW Group &mdash; headings in this project's Gantt</h4><button type="button" class="cwbs-hp-x">&times;</button></div>
  <div class="cwbs-hp-body">
    <div class="cwbs-hp-row">
      <div style="flex:1;"><label>IOW Group</label><div class="cwbs-hp-names" id="cwbs-iowp-names" data-ph="Enter IOW Group name"></div></div>
      <button type="button" class="cwbs-hp-plus" data-for="cwbs-iowp-names" title="Add another IOW Group" style="margin-bottom:6px;">+</button>
    </div>
    <div class="cwbs-hp-addrow"><button type="button" class="cwbs-btn cwbs-btn-add" id="cwbs-iowp-add">Add to Gantt</button></div>
    <div class="cwbs-hp-msg" id="cwbs-iowp-msg"></div>
    <table><thead><tr><th class="n">#</th><th>IOW Group</th><th class="b">Move / Rename / Delete</th></tr></thead><tbody id="cwbs-iowp-list"></tbody></table>
  </div>
</div>
<div id="cwbs-sgp" class="cwbs-hp">
  <div class="cwbs-hp-hdr"><h4>Sub Group &mdash; under an IOW Group in this project's Gantt</h4><button type="button" class="cwbs-hp-x">&times;</button></div>
  <div class="cwbs-hp-body">
    <div class="cwbs-hp-row">
      <div style="flex:1;"><label>IOW Group</label><select id="cwbs-sgp-group"><option value="">Select IOW Group</option></select></div>
    </div>
    <div class="cwbs-hp-row">
      <div style="flex:1;"><label>Sub Group</label><div class="cwbs-hp-names" id="cwbs-sgp-names" data-ph="Enter Sub Group name"></div></div>
      <button type="button" class="cwbs-hp-plus" data-for="cwbs-sgp-names" title="Add another Sub Group" style="margin-bottom:6px;">+</button>
    </div>
    <div class="cwbs-hp-addrow"><button type="button" class="cwbs-btn cwbs-btn-add" id="cwbs-sgp-add">Add to Gantt</button></div>
    <div class="cwbs-hp-msg" id="cwbs-sgp-msg"></div>
    <table><thead><tr><th class="n">#</th><th>Sub Group</th><th class="b">Move / Rename / Delete</th></tr></thead><tbody id="cwbs-sgp-list"></tbody></table>
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
      <div class="cwbsed-f" style="flex:1;display:none;"><!-- activities have no IOW Group -->
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

    // The toolbar icon (rocket, "Create WBS") opens Create WBS directly.
    $(document).on('click','#pwt-nav-btn', function(e){ e.preventDefault(); cwbsShow(); });

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
            cwbsSplitWithBoq();
            setTimeout(cwbsSplitWithBoq, 120);   // after the viewer finishes opening
        }
    });
    // Two halves of the screen below the toolbar: BOQ viewer left, Create WBS right.
    function cwbsSplitWithBoq(){
        // Start below the top toolbar so its icons stay clickable.
        // Just below the toolbar icons (measured, so it holds at any zoom level).
        var ic = document.getElementById('pwt-nav-btn'), icb = ic ? ic.getBoundingClientRect().bottom : 0;
        var top = (icb > 20 && icb < 400) ? Math.round(icb) + 6 : 160;
        var h = Math.max(300, $(window).height() - top - 40);   // room for the bottom tab bar
        var half = Math.floor($(window).width() / 2);
        $('#pdoc-viewer-popup').css({ left: 0, top: top, width: half - 4, height: h, right: 'auto', bottom: 'auto', transform: 'none' });
        $('#cwbs-popup').css({ left: half, top: top, width: $(window).width() - half - 4, height: h, right: 'auto', bottom: 'auto', transform: 'none' });
    }
    // Gantt chart and Activity Library open on top of the split windows.
    $(document).on('click', '#cwbs-open-gantt', function(){
        if (typeof window.openGanttWin === 'function') window.openGanttWin();
        else $('#gantt-win-open').trigger('click');
        setTimeout(function(){ if (window.popupBringToFront) window.popupBringToFront('gantt-win'); }, 50);
    });
    $(document).on('click', '#cwbs-open-actlib', function(){
        var a = document.querySelector('.round-icons a.overNow4[title="Activity Library"]') || document.querySelector('a.overNow4[title="Activity Library"]');
        if (a) a.click();
        setTimeout(function(){ if (window.popupBringToFront) window.popupBringToFront('menu4-popup-cntnr'); }, 300);
    });
    // An activity saved in the Activity Library appears in the Create WBS list at once.
    $(document).on('actlib:saved', function(){
        if ($('#cwbs-popup').hasClass('cwbs-open') && $('#cwbs-proj-type').val()) cwbsLoadActivities();
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
        $('#cwbs-empty').text(msg || 'Select a Project Type to list activities.').show();
        $('#cwbs-msg').text('');
        cwbsAddState();
    }
    // Add is available once an IOW Group is chosen (it can be added on its own,
    // with no activities) or when activities are ticked.
    function cwbsAddState(){ $('#cwbs-add-btn').prop('disabled', !Object.keys(cwbsSelected).length); }
    function cwbsToast(msg){
        $('#cwbs-added-toast').remove();
        $('<div id="cwbs-added-toast"></div>').html(msg).css({ position:'fixed', left:'50%', top:'40%', transform:'translate(-50%,-50%)', background:'#0f5c52', color:'#fff', padding:'14px 26px', borderRadius:'8px', fontSize:'16px', fontWeight:700, boxShadow:'0 8px 24px rgba(0,0,0,.3)', zIndex:300000 }).appendTo('body').delay(2200).fadeOut(400, function(){ $(this).remove(); });
    }
    function cwbsLoadActivities(){
        // All activities of the Project Type (not filtered by IOW Group); the chosen
        // IOW Group is where the selected activities are added in the Gantt.
        var typeId = $('#cwbs-proj-type').val();
        if(!typeId){ cwbsResetList(); return; }
        $('#cwbs-empty').hide();
        $('#cwbs-table').hide();
        $('#cwbs-loading').show();
        $.ajax({
            type:'POST', url:'../projectsmain/getactivitiesbytypeandgroup', dataType:'json',
            data:{ typeId:typeId },
            success:function(d){
                $('#cwbs-loading').hide();
                // Keep what's already entered (Qty, R.Unit, ticks) across the refresh.
                var keepQty = {}, keepRun = {}, keepSel = cwbsSelected;
                $('#cwbs-tbody .cwbs-qty-input').each(function(){ keepQty[$(this).data('actid')] = $(this).val(); });
                $('#cwbs-tbody .cwbs-runits-input').each(function(){ keepRun[$(this).data('actid')] = $(this).val(); });
                cwbsSelected = {};
                cwbsAddState();
                var items = d.items || [];
                if(!items.length){ cwbsResetList('No activities found for this Project Type.'); return; }
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
                $('#cwbs-tbody .cwbs-qty-input').each(function(){ var v = keepQty[$(this).data('actid')]; if (v !== undefined) $(this).val(v); });
                $('#cwbs-tbody .cwbs-runits-input').each(function(){ var v = keepRun[$(this).data('actid')]; if (v !== undefined) $(this).val(v); });
                Object.keys(keepSel).forEach(function(id){
                    var btn = $('#cwbs-tbody .cwbs-tick-btn[data-actid="' + id + '"]');
                    if (btn.length) { cwbsSelected[id] = keepSel[id]; btn.addClass('cwbs-selected'); }
                });
                cwbsAddState();
                $('#cwbs-table').show();
                cwbsFilterActs();
            },
            error:function(){
                $('#cwbs-loading').hide();
                cwbsResetList('Failed to load activities. Please try again.');
            }
        });
    }
    $(document).on('click','#cwbs-close-x, #cwbs-cancel-btn', function(){ cwbsHide(); });
    // IOW Group list depends on the selected Project Type: only groups that
    // have activities of that type are shown.
    // IOW Groups to add under = this project's Gantt headings (made in the IOW Group tab).
    function cwbsLoadIowGroups(){
        cwbsLoadHeadings(function(){
            if($('#cwbs-proj-type').val()) cwbsLoadActivities(); else cwbsResetList();
        });
    }
    var cwbsHeads = { groups: [], subgroups: [] };
    var cwbsEsc = function(v){ return $('<div>').text(v == null ? '' : v).html(); };
    function cwbsLoadHeadings(done){
        $.ajax({ type:'POST', url:'../projectsmain/wbsheadings', dataType:'json',
            success:function(d){
                if(!d || d.error !== 'No') return;
                cwbsHeads = { groups: d.groups || [], subgroups: d.subgroups || [] };
                var opts = '<option value="">Select IOW Group</option>';
                cwbsHeads.groups.forEach(function(gr){ opts += '<option value="' + gr.id + '">' + cwbsEsc(gr.name) + '</option>'; });
                ['#cwbs-iow-group', '#cwbs-sgp-group'].forEach(function(sid){
                    var sel = $(sid), cur = sel.val();
                    sel.html(opts);
                    if (cur && sel.find('option[value="' + cur + '"]').length) sel.val(cur);
                });
                cwbsFillSubgroups();
                cwbsRenderIowList();
                cwbsRenderSgList();
                if (done) done();
            }
        });
    }
    function cwbsFillSubgroups(){
        var gid = String($('#cwbs-iow-group').val() || ''), sel = $('#cwbs-subgroup'), cur = sel.val();
        var opts = '<option value="">None (directly under the IOW Group)</option>';
        cwbsHeads.subgroups.forEach(function(sg){ if (String(sg.iow_group_id) === gid) opts += '<option value="' + sg.id + '">' + cwbsEsc(sg.name) + '</option>'; });
        sel.html(opts);
        if (cur && sel.find('option[value="' + cur + '"]').length) sel.val(cur);
    }
    $(document).on('change', '#cwbs-iow-group', cwbsFillSubgroups);
    function cwbsAfterHeadingChange(){
        cwbsLoadHeadings();
        if (typeof window.reloadGantt === 'function') window.reloadGantt();
    }
    function cwbsPost(url, data, $msg, okText){
        $.ajax({ type:'POST', url:'../projectsmain/' + url, dataType:'json', data:data,
            success:function(d){
                if (d && d.error === 'No') { if (okText) { $msg.css('color','#197a3a').text(okText); cwbsToast('&#10003; ' + okText); } else $msg.text(''); cwbsAfterHeadingChange(); }
                else $msg.css('color','#c00').text((d && d.errortext) || 'Could not save.');
            },
            error:function(){ $msg.css('color','#c00').text('Could not save.'); }
        });
    }
    function cwbsHpBtns(kind, id){
        return '<button type="button" class="cwbs-hp-ib" data-k="' + kind + '" data-a="up" data-id="' + id + '" title="Move up">&#9650;</button>'
             + '<button type="button" class="cwbs-hp-ib" data-k="' + kind + '" data-a="down" data-id="' + id + '" title="Move down">&#9660;</button>'
             + '<button type="button" class="cwbs-hp-ib" data-k="' + kind + '" data-a="ren" data-id="' + id + '" title="Rename">&#9998;</button>'
             + '<button type="button" class="cwbs-hp-ib del" data-k="' + kind + '" data-a="del" data-id="' + id + '" title="Delete">&#10005;</button>';
    }
    function cwbsRenderIowList(){
        var rows = '';
        cwbsHeads.groups.forEach(function(gr, i){
            rows += '<tr><td class="n">' + (i + 1) + '</td><td>' + cwbsEsc(gr.name) + '</td><td class="b">' + cwbsHpBtns('g', gr.id) + '</td></tr>';
        });
        $('#cwbs-iowp-list').html(rows || '<tr><td style="color:#888;text-align:center;">No IOW Group in this project\'s Gantt yet.</td></tr>');
    }
    function cwbsRenderSgList(){
        var gid = String($('#cwbs-sgp-group').val() || ''), rows = '', n = 0;
        cwbsHeads.subgroups.forEach(function(sg){
            if (String(sg.iow_group_id) !== gid) return;
            rows += '<tr><td class="n">' + (++n) + '</td><td>' + cwbsEsc(sg.name) + '</td><td class="b">' + cwbsHpBtns('s', sg.id) + '</td></tr>';
        });
        $('#cwbs-sgp-list').html(rows || '<tr><td style="color:#888;text-align:center;">' + (gid ? 'No Sub Group under this IOW Group yet.' : 'Select an IOW Group.') + '</td></tr>');
    }
    $(document).on('change', '#cwbs-sgp-group', cwbsRenderSgList);
    // Tabs: IOW Group and Sub Group open their popups; Activities shows the activity list.
    function cwbsOpenHp(id){
        var el = document.getElementById(id), cw = document.getElementById('cwbs-popup').getBoundingClientRect();
        if (!el.classList.contains('on')) {
            el.classList.add('on');
            var w = el.offsetWidth;
            el.style.left = Math.max(8, Math.min(window.innerWidth - w - 8, cw.left + (cw.width - w) / 2)) + 'px';
            el.style.top = Math.max(60, cw.top + 70) + 'px';
            if (!el.getAttribute('data-drag') && $.fn.draggable) { $(el).draggable({ handle: '.cwbs-hp-hdr', containment: 'window', scroll: false }); el.setAttribute('data-drag', '1'); }
        }
        if (window.popupBringToFront) window.popupBringToFront(id);
        cwbsLoadHeadings();
    }
    // Name boxes: + adds another box, so several can be added to the Gantt at once.
    function cwbsNameRow($box, first){
        var $r = $('<div class="cwbs-hp-nrow"><input type="text" class="cwbs-hp-name" autocomplete="off"></div>');
        $r.find('input').attr('placeholder', $box.data('ph'));
        if (!first) $r.append('<button type="button" class="cwbs-hp-nrm" title="Remove">&times;</button>');
        $box.append($r);
        return $r.find('input');
    }
    function cwbsNamesReset(id){ return cwbsNameRow($('#' + id).empty(), true); }
    function cwbsNames(id){ var a = []; $('#' + id + ' .cwbs-hp-name').each(function(){ var v = $.trim(this.value); if (v) a.push(v); }); return a; }
    $(document).on('click', '.cwbs-hp-plus', function(){ cwbsNameRow($('#' + $(this).data('for')), false).focus(); });
    $(document).on('click', '.cwbs-hp-nrm', function(){ $(this).closest('.cwbs-hp-nrow').remove(); });
    $(document).on('keydown', '.cwbs-hp-name', function(e){ if (e.key === 'Enter') { e.preventDefault(); cwbsNameRow($(this).closest('.cwbs-hp-names'), false).focus(); } });
    cwbsNamesReset('cwbs-iowp-names'); cwbsNamesReset('cwbs-sgp-names');
    // Adds each name in turn, then refreshes the lists and the Gantt once.
    function cwbsAddMany(url, datas, $msg, what, done){
        var ok = 0, errs = [];
        (function next(i){
            if (i >= datas.length) {
                var t = ok + ' ' + what + (ok > 1 ? 's' : '');
                if (ok) { cwbsToast('&#10003; ' + t + ' added to Gantt Chart'); cwbsAfterHeadingChange(); }
                $msg.css('color', errs.length ? '#c00' : '#197a3a').text(errs.length ? errs.join(' ') : t + ' added to the Gantt.');
                if (done) done(errs.length);
                return;
            }
            $.ajax({ type:'POST', url:'../projectsmain/' + url, dataType:'json', data:datas[i],
                success:function(d){ if (d && d.error === 'No') ok++; else errs.push(datas[i].name + ': ' + ((d && d.errortext) || 'not added.')); next(i + 1); },
                error:function(){ errs.push(datas[i].name + ': not added.'); next(i + 1); } });
        })(0);
    }
    $(document).on('click', '#cwbs-tab-iow', function(){ cwbsOpenHp('cwbs-iowp'); $('#cwbs-iowp-msg').text(''); setTimeout(function(){ $('#cwbs-iowp-names .cwbs-hp-name').first().focus(); }, 50); });
    $(document).on('click', '#cwbs-tab-sg', function(){ cwbsOpenHp('cwbs-sgp'); $('#cwbs-sgp-msg').text(''); });
    $(document).on('click', '#cwbs-tab-act', function(){
        var on = !$('#cwbs-popup').hasClass('cwbs-acts');
        $('#cwbs-popup').toggleClass('cwbs-acts', on);
        $(this).toggleClass('on', on);
    });
    $(document).on('click', '.cwbs-hp-x', function(){ $(this).closest('.cwbs-hp').removeClass('on'); });
    $(document).on('click', '#cwbs-iowp-add', function(){
        var names = cwbsNames('cwbs-iowp-names');
        if (!names.length) { $('#cwbs-iowp-msg').css('color','#c00').text('Enter the IOW Group name.'); return; }
        var $b = $(this).prop('disabled', true);
        cwbsAddMany('wbsheadingadd', names.map(function(n){ return { name: n }; }), $('#cwbs-iowp-msg'), 'IOW Group',
            function(errs){ $b.prop('disabled', false); if (!errs) cwbsNamesReset('cwbs-iowp-names').focus(); });
    });
    $(document).on('click', '#cwbs-sgp-add', function(){
        var gid = $('#cwbs-sgp-group').val(), names = cwbsNames('cwbs-sgp-names');
        if (!gid) { $('#cwbs-sgp-msg').css('color','#c00').text('Select the IOW Group.'); return; }
        if (!names.length) { $('#cwbs-sgp-msg').css('color','#c00').text('Enter the Sub Group name.'); return; }
        var $b = $(this).prop('disabled', true);
        cwbsAddMany('wbssubgroupadd', names.map(function(n){ return { iow_group_id: gid, name: n }; }), $('#cwbs-sgp-msg'), 'Sub Group',
            function(errs){ $b.prop('disabled', false); if (!errs) cwbsNamesReset('cwbs-sgp-names').focus(); });
    });
    $(document).on('click', '.cwbs-hp-ib', function(){
        var k = $(this).data('k'), a = $(this).data('a'), id = $(this).data('id');
        var $msg = k === 'g' ? $('#cwbs-iowp-msg') : $('#cwbs-sgp-msg');
        var base = k === 'g' ? 'wbsheading' : 'wbssubgroup';
        var name = $.trim($(this).closest('tr').children('td').eq(1).text());
        if (a === 'up' || a === 'down') { cwbsPost(base + 'move', { id: id, dir: a }, $msg, ''); return; }
        if (a === 'ren') {
            var nn = window.prompt('Rename "' + name + '" to:', name);
            if (nn === null || !$.trim(nn) || $.trim(nn) === name) return;
            cwbsPost(base + 'rename', { id: id, name: $.trim(nn) }, $msg, 'Renamed');
            return;
        }
        if (a === 'del') {
            if (!window.confirm(k === 'g' ? 'Remove IOW Group "' + name + '" from this project\'s Gantt?'
                                          : 'Delete Sub Group "' + name + '"? Its activities stay, directly under the IOW Group.')) return;
            cwbsPost(base + 'delete', { id: id }, $msg, k === 'g' ? 'IOW Group removed' : 'Sub Group deleted');
        }
    });
    $(document).on('change','#cwbs-proj-type', function(){ cwbsLoadIowGroups(); });
    // Choosing the IOW Group no longer reloads the list (it only sets where to add).
    $(document).on('change','#cwbs-iow-group', function(){ if(!$('#cwbs-tbody tr').length) cwbsLoadActivities(); cwbsAddState(); });
    function cwbsFilterActs(){
        var q = ($('#cwbs-act-search').val() || '').trim().toLowerCase(), n = 0;
        $('#cwbs-tbody tr').each(function(){
            var hit = !q || $(this).find('.cwbs-act-name').text().toLowerCase().indexOf(q) >= 0;
            $(this).toggle(hit); if (hit) n++;
        });
        $('#cwbs-msg').css('color','#555').text(q && !n ? 'No activity matches "' + q + '".' : '');
    }
    $(document).on('input','#cwbs-act-search', cwbsFilterActs);
    // IOW Group search: type to filter the groups, pick one from the list. The
    // hidden select stays the source of the value, so the rest works unchanged.
    (function(){
        var sel = document.getElementById('cwbs-iow-group');
        if (!sel || sel.getAttribute('data-ss')) return;
        sel.setAttribute('data-ss', '1');
        var wrap = document.createElement('div'); wrap.className = 'cwbs-ss';
        var inp = document.createElement('input'); inp.type = 'text'; inp.placeholder = 'Search IOW Group'; inp.autocomplete = 'off';
        var list = document.createElement('div'); list.className = 'cwbs-ss-list';
        sel.parentNode.insertBefore(wrap, sel); wrap.appendChild(inp); wrap.appendChild(list); wrap.appendChild(sel);
        sel.style.display = 'none';
        function selText(){ var o = sel.options[sel.selectedIndex]; return o && o.value ? o.textContent : ''; }
        function sync(){ inp.value = selText(); }
        function render(){
            var q = inp.value.trim().toLowerCase(), html = '', n = 0;
            for (var i = 0; i < sel.options.length; i++) {
                var o = sel.options[i]; if (!o.value) continue;
                if (q && o.textContent.toLowerCase().indexOf(q) < 0) continue;
                html += '<div class="cwbs-ss-item' + (o.value === sel.value ? ' on' : '') + '" data-v="' + o.value + '">' + $('<div>').text(o.textContent).html() + '</div>'; n++;
            }
            list.innerHTML = n ? html : '<div class="cwbs-ss-none">' + (sel.options.length > 1 ? 'No IOW Group matches' : 'Add an IOW Group in the IOW Group tab first') + '</div>';
            list.style.display = 'block';
        }
        inp.addEventListener('focus', function(){ inp.select(); render(); });
        inp.addEventListener('input', render);
        list.addEventListener('mousedown', function(e){
            var it = e.target.closest('.cwbs-ss-item'); if (!it) return;
            e.preventDefault();
            sel.value = it.getAttribute('data-v'); sync(); list.style.display = 'none'; inp.blur();
            $(sel).trigger('change');
        });
        inp.addEventListener('blur', function(){ setTimeout(function(){ list.style.display = 'none'; sync(); }, 150); });
        inp.addEventListener('keydown', function(e){
            if (e.key === 'Enter') { var f = list.querySelector('.cwbs-ss-item'); if (f) { e.preventDefault(); f.dispatchEvent(new MouseEvent('mousedown', { bubbles: true })); } }
            else if (e.key === 'Escape') { inp.blur(); }
        });
        // Keep the box in step when the list is reloaded or the value set in code.
        new MutationObserver(sync).observe(sel, { childList: true });
        $(sel).on('change', sync);
    })();
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
        cwbsAddState();
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
        if(!groupId){ alert('Select the IOW Group to add the activities under.'); return; }
        var $btn = $(this).prop('disabled', true);
        $('#cwbs-msg').css('color','#555').text('Adding ' + ids.length + ' activity/activities…');

        function addOne(i){
            if(i >= ids.length){
                $btn.prop('disabled', false);
                $('#cwbs-msg').css('color','#197a3a').text('Added ' + ids.length + ' activity/activities to the Gantt.');
                // Clear confirmation over the Create WBS window.
                $('#cwbs-added-toast').remove();
                $('<div id="cwbs-added-toast">&#10003; Added to Gantt Chart</div>').css({ position:'fixed', left:'50%', top:'40%', transform:'translate(-50%,-50%)', background:'#0f5c52', color:'#fff', padding:'14px 26px', borderRadius:'8px', fontSize:'16px', fontWeight:700, boxShadow:'0 8px 24px rgba(0,0,0,.3)', zIndex:300000 }).appendTo('body').delay(2200).fadeOut(400, function(){ $(this).remove(); });
                cwbsSelected = {};
                $('.cwbs-tick-btn').removeClass('cwbs-selected');
                cwbsAddState();
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
                    var pend = (window.cwbsEdits || {})[act.id];
                    var tasks = pend ? pend.tasks.map(function(t){ return { id:t.id, name:t.name, unit:t.unit, qty:t.qty, prod:t.prod, resunits:1 }; })
                        : (res.tasks || []).map(function(t){
                            return { id:t.id, name:t.task_name, unit:t.task_unit, qty:t.task_qty, prod:t.productivity, resunits:t.resunits };
                        });
                    if (pend) { res.tasks = pend.tasks.map(function(t){ return { task_qty:t.qty, productivity:t.prod }; }); res.unit = pend.unit; act.name = pend.name; }
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
                        subgroup_id: parseInt($('#cwbs-subgroup').val(), 10) || 0,
                        iow_act_id: act.id,
                        act_name: act.name,
                        sch_unit: res.sch_unit || res.unit || '',
                        sch_qty: qty,
                        qty: qty,
                        unit: res.unit || '',
                        duration: duration,
                        r_unit: rowRUnit,
                        tasks: tasks,
                        resources: []
                    };
                    $.ajax({
                        type:'POST', url:'../projectsmain/wbsadd', dataType:'json',
                        data:{ payload: JSON.stringify(payload) },
                        success:function(){ if (window.cwbsEdits) delete window.cwbsEdits[act.id]; addOne(i + 1); },
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
        cwbsRestoreEntries();
    }
    // Qty and R.Unit typed on the Create WBS list, kept in memory per activity so
    // they can be put back if the list's inputs are cleared (e.g. after the Edit
    // Activity popup is used).
    window.cwbsEntries = window.cwbsEntries || {};
    $(document).on('input', '#cwbs-tbody .cwbs-qty-input, #cwbs-tbody .cwbs-runits-input', function(){
        var id = $(this).data('actid');
        window.cwbsEntries[id] = window.cwbsEntries[id] || {};
        window.cwbsEntries[id][$(this).hasClass('cwbs-qty-input') ? 'qty' : 'run'] = $(this).val();
    });
    function cwbsRestoreEntries(){
        $('#cwbs-tbody .cwbs-qty-input').each(function(){
            var e = window.cwbsEntries[$(this).data('actid')];
            if (e && e.qty !== undefined && $(this).val() === '') $(this).val(e.qty);
        });
        $('#cwbs-tbody .cwbs-runits-input').each(function(){
            var e = window.cwbsEntries[$(this).data('actid')];
            if (e && e.run !== undefined && $(this).val() === '') $(this).val(e.run);
        });
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
        // Edits made here are for this project only: kept with the row and sent
        // when the activity is added to the Gantt (never saved to the Activity Library).
        var pend = (window.cwbsEdits || {})[actId];
        if (pend) {
            $('#cwbsed-act-name').val(pend.name);
            $('#cwbsed-act-unit').val(pend.unit);
            $('#cwbsed-task-rows').html(pend.tasks.length ? pend.tasks.map(function(t){ return cwbsedTaskRowHtml(t.name, t.unit, t.qty, t.prod, t.id); }).join('') : cwbsedTaskRowHtml(pend.name, pend.unit, '', '', 0));
            return;
        }
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

        // Kept for this project only — applied when the activity is added to the
        // Gantt (actionWbsadd). The Activity Library is not changed.
        var tasks = [];
        for (var i = 0; i < taskNames.length; i++) tasks.push({ id: +taskIds[i] || 0, name: taskNames[i], unit: taskUnits[i], qty: +taskQtys[i] || 0, prod: +taskProds[i] || 0 });
        window.cwbsEdits = window.cwbsEdits || {};
        window.cwbsEdits[actId] = { name: actName, unit: actUnit, tasks: tasks };
        cwbsedHide();
        // Update only the edited row in the Create WBS list, so the Qty/R.Unit/ticks
        // entered on the other rows are untouched.
        var $ed = $('#cwbs-tbody tr[data-actid="' + actId + '"]');
        $ed.find('.cwbs-act-name').html($('<div>').text(actName).html() + ' <span style="font-size:10px;color:#2ba899;font-weight:700;">(edited for this project)</span>');
        $ed.find('.cwbs-unit-td').html($('<div>').text(actUnit).html());
        $ed.find('.cwbs-edit-btn, .cwbs-tick-btn').attr('data-actname', actName).data('actname', actName);
        if (cwbsSelected[actId]) cwbsSelected[actId].name = actName;
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
.dash-modal .panel,.cf-seg-win .panel{background:#fff;border:none;border-right:1px solid #c8d0e0;overflow:hidden;display:flex;flex-direction:column;min-height:0}
.dash-modal .ph,.cf-seg-win .ph{background:linear-gradient(180deg,#0c1535 0%,#05091c 100%);padding:3px 6px;font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;letter-spacing:.7px;text-align:center;text-transform:uppercase;color:#fff;flex-shrink:0}
.dash-modal .pb,.cf-seg-win .pb{flex:1;min-height:0;overflow:hidden;padding:4px 7px 3px}
#pd-c2{overflow:auto !important;}
/* ── Legend ───────────────────────────────────────────────────────────────── */
.dash-modal .leg,.cf-seg-win .leg{display:flex;gap:6px;align-items:center;font-family:'Barlow Condensed',sans-serif;font-size:13px;color:#4a5a72;margin-bottom:3px}
.dash-modal .leg span,.cf-seg-win .leg span{display:flex;align-items:center;gap:3px}
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
#cf-modal{display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:960px;max-width:92vw;height:68vh;z-index:100200;border-radius:6px;overflow:hidden;background:#f0f2f7;box-shadow:0 8px 32px rgba(0,0,0,.7);flex-direction:column}
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
#cf-grid{flex:1;min-height:0;display:grid;grid-template-columns:1fr 26px 1fr 1fr 26px 1fr 1fr;grid-template-rows:repeat(3, 1fr);row-gap:0;column-gap:0}
#cf-subhdr{display:grid !important;grid-template-columns:1fr 26px 1fr 1fr 26px 1fr 1fr}
#cf-modal .ph,.cf-seg-win .ph{background:#5a5a5a;font-size:11px;}
#cf-modal .panel,.cf-seg-win .panel{border-right:3px solid #a0aec0}
.cf-seg-win.cf-seg-on{display:flex}
.cf-seg-hdr{touch-action:none}
.cf-seg-win button,.cf-seg-win .gp{touch-action:manipulation}

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
        <div style="grid-column:1;background:#4a4f57;color:#fff;font-family:'Nunito',Arial,sans-serif;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.02em;text-align:center;padding:2px 0;">Value Drivers</div>
        <div style="grid-column:3 / span 2;background:#4a4f57;color:#fff;font-family:'Nunito',Arial,sans-serif;font-size:12px;font-weight:400;text-transform:uppercase;letter-spacing:.02em;text-align:center;padding:2px 0;">Cash-to-Cash Cycle&nbsp;&nbsp;&nbsp;<span id="cf-c2c-legend" style="text-transform:none;letter-spacing:normal;font-weight:400;"></span></div>
        <div style="grid-column:6 / span 2;background:#1a0a2e;color:#fff;font-family:'Nunito',Arial,sans-serif;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.02em;text-align:center;padding:2px 0;">ROI and Free Cash</div>
      </div>
      <div id="cf-grid">
      <div class="panel" style="grid-column:1;grid-row:1;background:#e3e5e8;"><div class="ph" style="background:#1a0a2e;">Project Duration</div><div class="gp" style="background:#e3e5e8;" id="cf-value-duration"></div></div>
      <div class="panel" style="grid-column:1;grid-row:2;background:#e3e5e8;"><div class="ph" style="background:#1a0a2e;">Project Cost</div><div class="gp" style="background:#e3e5e8;" id="cf-value-cost"></div></div>
      <div class="panel" style="grid-column:1;grid-row:3;background:#e3e5e8;"><div class="ph" style="background:#1a0a2e;">Project Resources</div><div class="gp" style="background:#e3e5e8;" id="cf-value-procurement"></div></div>
      <div class="panel" style="grid-column:3;grid-row:1;background:#e3e5e8;"><div class="ph" style="background:#1a0a2e;">Advances and Deposits</div><div class="gp" style="background:#e3e5e8;" id="cf-currentassets"></div></div>
      <div class="panel" style="grid-column:3;grid-row:2;background:#e3e5e8;"><div class="ph" style="background:#1a0a2e;">Work In Progress</div><div class="gp" style="background:#e3e5e8;" id="cf-billsreceivable"></div></div>
      <div class="panel" style="grid-column:4;grid-row:3;background:#e3e5e8;"><div class="ph" style="background:#1a0a2e;">Bills Receivable</div><div class="gp" style="background:#e3e5e8;" id="cf-contract-income"></div></div>
      <div class="panel" style="grid-column:4;grid-row:1;background:#e3e5e8;"><div class="ph" style="background:#1a0a2e;">Bank</div><div class="gp" style="background:#e3e5e8;" id="cf-bankdial"></div></div>
      <div class="panel" style="grid-column:6;grid-row:2;background:#eceef0;"><div class="ph" style="background:#1a0a2e;">Current Liabilities</div><div class="gp" style="background:#eceef0;" id="cf-bank"></div></div>
      <div class="panel" style="grid-column:3;grid-row:3;background:#e3e5e8;"><div class="ph" style="background:#1a0a2e;">Total Investment</div><div class="gp" style="background:#e3e5e8;" id="cf-netinvestment"></div></div>
      <div class="panel" style="grid-column:4;grid-row:2;background:#e3e5e8;"><div class="ph" style="background:#1a0a2e;text-align:center;">Cash-to-Cash Cycle</div><div class="gp" style="background:#e3e5e8;" id="cf-c2cdial"></div></div>
      <div class="panel" style="grid-column:6;grid-row:3;background:#eceef0;"><div class="ph" style="background:#1a0a2e;">Current Assets</div><div class="gp" style="background:#eceef0;" id="cf-curassets"></div></div>
      <div class="panel" style="grid-column:6;grid-row:1;background:#eceef0;"><div class="ph" style="background:#1a0a2e;">Net Working Capital</div><div class="gp" style="background:#eceef0;" id="cf-workcap"></div></div>
      <div class="panel" style="grid-column:5;grid-row:1;background:#e3e5e8;"><div class="ph" style="background:#1a0a2e;">Contract Income</div><div class="gp" style="background:#e3e5e8;" id="cf-contractincome"></div></div>
      <div class="panel" style="grid-column:5;grid-row:2;background:#e3e5e8;"><div class="ph" style="background:#1a0a2e;">Project Expenditure</div><div class="gp" style="background:#e3e5e8;" id="cf-projectexpenditure"></div></div>
      <div class="panel" style="grid-column:5;grid-row:3;background:#e3e5e8;"><div class="ph" style="background:#1a0a2e;">Contribution Margin</div><div class="gp" style="background:#e3e5e8;" id="cf-contribmargin"></div></div>
      <div class="panel" style="grid-column:7;grid-row:2;background:#eceef0;"><div class="ph" style="background:#1a0a2e;">ROI</div><div class="gp" style="background:#eceef0;" id="cf-roi"></div></div>
      <div class="panel" style="grid-column:7;grid-row:1;background:#eceef0;"><div class="ph" style="background:#1a0a2e;">CASHFLOW FROM PROJECT</div><div class="gp" style="background:#eceef0;" id="cf-expenses"></div></div>
      <div class="panel" style="grid-column:7;grid-row:3;background:#eceef0;"><div class="ph" style="background:#1a0a2e;">Capital efficiency</div><div class="gp" style="background:#eceef0;" id="cf-wceff"></div></div>
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

<script>
// Populates a <select> with the project's IOW Groups. Used by the Create
// WBS popup's own IOW Group dropdowns.
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
</script>

<!-- Task sub-popup — shared by any caller that edits an activity's task
     list (Create WBS's Edit Activity popup has its own separate inline
     task editor; this one is used by the Gantt's Edit Activities popup's
     Task button). Duration formula matches Create WBS's Add button and
     Edit Activities' save path: duration = ceil((qty * sum(task
     qty/productivity)) / r_unit) — r_unit divides the whole row total,
     not applied per task. -->
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
        <th style="width:75px;">Dur/Unit</th><th style="width:30px;"></th>
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
  function taskRowHtml(t){
    t = t || {};
    return '<td data-taskid="' + (t.id != null ? t.id : '') + '"><input type="text" class="apt-task-name" value="' + (t.name||'').replace(/"/g,'&quot;') + '" placeholder="Task name"></td>'
      + '<td><input type="number" class="apt-task-qty" value="' + (t.qty!=null?t.qty:1) + '" step="0.01" min="0"></td>'
      + '<td><input type="number" class="apt-task-prod" value="' + (t.productivity!=null?t.productivity:'') + '" step="0.01" min="0"></td>'
      + '<td><input type="text" class="apt-task-durunit" readonly></td>'
      + '<td style="text-align:center;"><button type="button" class="apt-del-btn" title="Remove">&times;</button></td>';
  }

  function apRecalcTaskRow(tr){
    var qty = parseFloat(tr.querySelector('.apt-task-qty').value) || 0;
    var prod = parseFloat(tr.querySelector('.apt-task-prod').value) || 0;
    var durPerUnit = prod > 0 ? (qty / prod) : 0;
    tr.querySelector('.apt-task-durunit').value = durPerUnit ? durPerUnit.toFixed(3) : '';
    return durPerUnit;
  }

  function apRecalcDuration(){
    var schQty = (typeof _apTaskGetQty === 'function') ? _apTaskGetQty() : 0;
    var rUnit = (typeof _apTaskGetRUnit === 'function') ? (_apTaskGetRUnit() || 1) : 1;
    var sum = 0;
    document.querySelectorAll('#apt-task-body tr').forEach(function(tr){ sum += apRecalcTaskRow(tr); });
    var dur = (schQty > 0 && sum > 0) ? Math.ceil((schQty * sum) / rUnit) : 0;
    document.getElementById('apt-dur-val').textContent = dur > 0 ? dur : '—';
    return dur;
  }

  function addTaskRowTo(tbody, t){
    var tr = document.createElement('tr');
    tr.innerHTML = taskRowHtml(t);
    tbody.appendChild(tr);
    tr.querySelectorAll('.apt-task-qty,.apt-task-prod').forEach(function(inp){
      inp.addEventListener('input', apRecalcDuration);
    });
    tr.querySelector('.apt-del-btn').addEventListener('click', function(){
      if(document.querySelectorAll('#apt-task-body tr').length > 1){ tr.remove(); apRecalcDuration(); }
    });
    apRecalcTaskRow(tr);
  }

  var _apTaskOnSave = null;   // caller's save callback for the currently-open Task popup
  var _apTaskGetQty  = null;  // caller's fn returning the current row's Qty, for the live duration preview
  var _apTaskGetRUnit = null; // caller's fn returning the current row's R.Unit, for the live duration preview

  // Generic entry point — any caller opens this with: a label, the
  // current task list, an onSave(updatedTasks, duration) callback, a
  // getQty() callback and an optional getRUnit() callback for the live
  // duration display.
  window.openTaskPopupFor = function(id, label, tasks, onSave, getQty, unit, getRUnit){
    document.getElementById('apt-hdr-act').textContent = label ? ('— ' + label) : '';
    document.getElementById('apt-qty-hdr').textContent = unit ? ('Qty/' + unit) : 'Qnty/Unit';
    _apTaskOnSave = onSave;
    _apTaskGetQty = getQty || function(){ return 0; };
    _apTaskGetRUnit = getRUnit || function(){ return 1; };

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
        resunits: 1
      });
    });
    var dur = apRecalcDuration();
    if(typeof _apTaskOnSave === 'function') _apTaskOnSave(tasks, dur);
    _apTaskOnSave = null;
    _apTaskGetQty = null;
    _apTaskGetRUnit = null;
    document.getElementById('apt-bk').classList.remove('apt-open');
    document.getElementById('apt-modal').classList.remove('apt-open');
  }
  document.getElementById('apt-close').addEventListener('click', closeTaskPopup);
  document.getElementById('apt-btn-done').addEventListener('click', closeTaskPopup);
  document.getElementById('apt-add-row-btn').addEventListener('click', function(){
    addTaskRowTo(document.getElementById('apt-task-body'), {});
  });
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
#eam-iow-spacer{flex:1;}
#eam-body{padding:14px 18px;overflow:auto;flex:1;scroll-snap-type:y proximity;}
#eam-body table{table-layout:auto;width:100%;min-width:600px;border-collapse:collapse;border:1px solid #8fb8ae;}
#eam-body tbody tr{scroll-snap-align:start;}
#eam-body tbody tr:nth-child(even){background:#eaf5f1;}
#eam-body tbody tr:hover{background:#d7ede6;}
#eam-body th, #eam-body td{white-space:nowrap;padding:8px 10px;border:1px solid #bcdcd2;font-size:13px;vertical-align:middle;}
#eam-body th{background:#e9ebed;color:#555;font-weight:700;text-align:left;}
#eam-body td.eam-col-sno{color:#999;text-align:center;width:36px;}
#eam-body td.eam-col-name{white-space:normal;}
.eam-name-input{width:100%;height:28px;border:1px solid #ccc;border-radius:4px;padding:0 6px;font-size:12px;box-sizing:border-box;}
.eam-unit-input{width:70px;height:28px;border:1px solid #ccc;border-radius:4px;padding:0 6px;font-size:12px;box-sizing:border-box;}
#eam-col-qty input{width:80px;height:28px;border:1px solid #ccc;border-radius:4px;padding:0 6px;font-size:12px;box-sizing:border-box;}
#eam-col-runit input{width:60px;height:28px;border:1px solid #ccc;border-radius:4px;padding:0 6px;font-size:12px;box-sizing:border-box;}
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
  <div id="eam-iow-row">
    <span id="eam-iow-name"></span>
    <span id="eam-iow-spacer"></span>
    <button type="button" class="eam-del-btn" id="eam-iow-del-btn" title="Delete all activities under this IOW Group">&times;</button>
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
  var _eamIowName = '';
  var _eamIowGroupId = 0;
  var _eamReloadPending = false;

  function eamTaskRowHtml(actId){
    var tasks = _eamTasksByAct[actId] || [];
    return tasks.length ? (tasks.length + ' task' + (tasks.length > 1 ? 's' : '')) : 'No tasks';
  }

  var _eamTasksByAct = {}; // sa_id -> [{id, name, qty, productivity, resunits}], edited copy per row
  var _eamRUnitByAct = {}; // sa_id -> current R.Unit value (row-level, divides the whole task-duration sum)

  function eamRecalcDuration(schQty, tasks, rUnit){
    var sumDurPerUnit = 0;
    (tasks || []).forEach(function(t){
      var dpu = (t.productivity > 0) ? ((t.qty || 0) / t.productivity) : 0;
      sumDurPerUnit += dpu;
    });
    rUnit = rUnit || 1;
    return (schQty > 0 && sumDurPerUnit > 0) ? Math.ceil((schQty * sumDurPerUnit) / rUnit) : 1;
  }

  window.openEditActivitiesModal = function(iowName, iowGroupId){
    _eamIowName = iowName || '';
    _eamIowGroupId = iowGroupId || 0;
    _eamReloadPending = false;
    document.getElementById('eam-hdr-title-suffix').textContent = iowName ? (' — ' + iowName) : '';
    eamRenderIowRowView();
    document.getElementById('eam-bk').classList.add('eam-open');
    document.getElementById('eam-modal').classList.add('eam-open');
    eamLoadList();
  };

  function eamRenderIowRowView(){
    document.getElementById('eam-iow-name').textContent = _eamIowName;
  }

  function eamViewCellsHtml(it){
    return '<td class="eam-col-name">' + $('<div>').text(it.name).html() + '</td>'
      + '<td>' + $('<div>').text(it.unit).html() + '</td>'
      + '<td id="eam-col-qty">' + it.quantity + '</td>'
      + '<td id="eam-col-runit">' + (it.r_unit != null ? it.r_unit : 1) + '</td>';
  }

  function eamEditCellsHtml(it){
    return '<td class="eam-col-name"><input type="text" class="eam-name-input" value="' + $('<div>').text(it.name).html() + '"></td>'
      + '<td><input type="text" class="eam-unit-input" value="' + $('<div>').text(it.unit).html() + '"></td>'
      + '<td id="eam-col-qty"><input type="number" class="eam-qty-input" value="' + it.quantity + '" step="0.01" min="0"></td>'
      + '<td id="eam-col-runit"><input type="number" class="eam-runit-input" value="' + (it.r_unit != null ? it.r_unit : 1) + '" step="0.01" min="0.01"></td>';
  }

  function eamLoadList(){
    var target = document.getElementById('eam-list-target');
    target.innerHTML = '<p id="eam-empty">Loading…</p>';
    $.ajax({
      type: 'POST', url: '../projectsmain/wbsactivitiesbyiow', dataType: 'json',
      data: { iow_group_id: _eamIowGroupId },
      success: function(d){
        var items = d.items || [];
        _eamTasksByAct = {};
        if(!items.length){
          target.innerHTML = '<p id="eam-empty">No activities added under this IOW yet.</p>';
          return;
        }
        var html = '<table><thead><tr>'
          + '<th style="width:36px;">#</th><th>Activity</th><th style="width:70px;">Unit</th><th style="width:90px;">Quantity</th><th style="width:80px;">R.Unit</th>'
          + '<th style="width:90px;">Task</th><th style="width:44px;"></th><th style="width:40px;"></th>'
          + '</tr></thead><tbody>';
        items.forEach(function(it, idx){
          _eamTasksByAct[it.sa_id] = it.tasks || [];
          _eamRUnitByAct[it.sa_id] = it.r_unit != null ? it.r_unit : 1;
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
        var rUnitInput = row ? row.querySelector('.eam-runit-input') : null;
        // Row is normally in view mode here (plain <td> text, no input) —
        // same fallback eamSaveActivity already uses: read cells[2]'s text
        // directly instead of assuming an .eam-unit-input exists. Qty/
        // R.Unit need the identical fallback (qtyInput/rUnitInput are
        // always null in view mode).
        var unitVal = unitInput ? unitInput.value.trim() : (row ? row.cells[2].textContent.trim() : '');
        var qtyVal = qtyInput ? qtyInput.value : (row ? row.querySelector('#eam-col-qty').textContent.trim() : '0');
        var rUnitVal = rUnitInput ? rUnitInput.value : (row ? row.querySelector('#eam-col-runit').textContent.trim() : '1');
        window.openTaskPopupFor(saId, taskBtn.getAttribute('data-actname'), _eamTasksByAct[saId] || [], function(updatedTasks){
          _eamTasksByAct[saId] = updatedTasks;
          taskBtn.textContent = eamTaskRowHtml(saId);
          taskBtn.classList.toggle('eam-has-tasks', updatedTasks.length > 0);
          // DONE on the Tasks popup used to only update this in-memory map —
          // nothing reached the server unless the row's pencil was ALSO
          // clicked twice afterward, so edits like Productivity silently
          // never persisted (no Budgeted_Duration, no Gantt bar). Save
          // immediately here instead, using the row's current Activity/
          // Unit/Qty/R.Unit exactly as eamToggleEdit's own save branch does.
          eamSaveActivity(saId, row);
        }, function(){ return parseFloat(qtyVal) || 0; }, unitVal, function(){ return parseFloat(rUnitVal) || 1; });
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
        quantity: row.cells[3].textContent.trim(),
        r_unit: row.cells[4].textContent.trim()
      };
      var wrap = document.createElement('tbody');
      wrap.innerHTML = '<tr>' + eamEditCellsHtml(current) + '</tr>';
      // Snapshot the 4 new cells BEFORE replacing anything — each
      // replaceChild below mutates row.cells' live indices, so reading
      // them from a separate, untouched NodeList avoids off-by-one bugs.
      var editCells = [].slice.call(wrap.querySelector('tr').cells);
      row.replaceChild(editCells[0], row.cells[1]);
      row.replaceChild(editCells[1], row.cells[2]);
      row.replaceChild(editCells[2], row.cells[3]);
      row.replaceChild(editCells[3], row.cells[4]);
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
    var rUnitInput = row.querySelector('.eam-runit-input');
    var rUnit = rUnitInput ? (parseFloat(rUnitInput.value) || 1) : (parseFloat(row.cells[4].textContent) || 1);
    if (!name) { alert('Activity name is required.'); return; }
    if (!qty)  { alert('Please enter a Qty before saving.'); return; }

    var tasks = _eamTasksByAct[saId] || [];
    _eamRUnitByAct[saId] = rUnit;
    if (editBtn) editBtn.disabled = true;
    $.ajax({
      type: 'POST', url: '../projectsmain/wbsactivityupdate', dataType: 'json',
      data: { payload: JSON.stringify({
        sa_id: saId, name: name, unit: unit, qty: qty, r_unit: rUnit,
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
          cells = row.cells;
          cells[4].outerHTML = '<td id="eam-col-runit">' + rUnit + '</td>';
        }
        if (editBtn) { editBtn.classList.remove('eam-editing'); editBtn.title = 'Edit'; }
        var taskBtn = row.querySelector('.eam-task-btn');
        if(taskBtn) taskBtn.setAttribute('data-actname', name);
        // Duration fell back to the 1-day minimum because no task has both
        // a Qty/Unit and a Productivity/day set — flag it instead of
        // saving silently, so a missing task value is never mistaken for
        // an intentional 1-day activity.
        if(d.duration_is_fallback){
          alert('Saved — but duration defaulted to 1 day because no task on "' + name + '" has both a Qty/Unit and Productivity/day set. Open the Task button to fill those in.');
        }
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

  // IOW Group row's delete — removes every activity under this IOW Group
  // within the current project (the group itself, a shared library entry,
  // is untouched). Same conflict/force confirmation pattern as a single
  // activity's delete, via actionWbsdeleteiow.
  document.getElementById('eam-iow-del-btn').addEventListener('click', function(){
    if(!confirm('Delete all activities under this IOW Group (for this project)?')) return;
    eamDoDeleteIow(false);
  });

  function eamDoDeleteIow(force){
    $.ajax({
      type: 'POST', url: '../projectsmain/wbsdeleteiow', dataType: 'json',
      data: { iow_group_id: _eamIowGroupId, force: force ? 1 : 0 },
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
/* Gantt opened from the Cashflow Project Duration panel: the title shows
   "Activities in this Cycle" instead of "Schedule Gantt Chart <project>". */
#gantt-win-cur-title { display:none; font-size:15px; font-weight:700; color:#fff; }
#gantt-win.gw-current #gantt-win-cur-title { display:inline; }
#gantt-win.gw-current .gw-std-title { display:none; }
/* ...with a thinner title bar and no icons (only the close × stays). */
#gantt-win.gw-current #gantt-win-hdr { padding:7px 12px; background:#1a0a2e; }
#gantt-win.gw-current #gantt-win-cur-title { font-size:13px; }
#gantt-win.gw-current #btn-project-duration,
#gantt-win.gw-current #btn-gantt-calendar-inline,
#gantt-win.gw-current #gantt-win-expand { display:none !important; }
#gantt-win.gw-current #gantt-win-close { font-size:17px; }
/* Activity bars in the dashboard header colour, in every Gantt (critical activities keep their own colour). */
#gantt-container div.gtaskblue,
#gantt-container div.gtaskblue.gplan { background:#1a0a2e !important; border-color:#1a0a2e !important; }
#gantt-win-hdr {
  background:#1a0a2e; color:#fff; padding:10px 15px;
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
      <span class="gw-std-title" style="font-size:15px;font-weight:700;color:#fff;">Schedule</span>
      <span class="gw-std-title" style="font-size:14px;font-weight:700;color:#fff;">Gantt Chart</span>
      <span id="gantt-win-cur-title">Activities in this Cycle</span>
      <span class="gw-std-title" style="font-size:12px;font-weight:500;color:#c9c9c9;padding:3px 6px;border-radius:4px;cursor:pointer;" id="gantt-win-proj-name" title="Hover for project snapshot" onmouseover="this.style.background='rgba(255,255,255,.12)'" onmouseout="this.style.background='transparent'"><?php echo htmlspecialchars($ProjectName); ?></span>
    </div>
    <div id="gantt-win-hdr-btns">
      <a class="icon-clock" id="btn-project-duration" title="Project Duration" href="#" style="width:24px;height:24px;font-size:11px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:#2878c0;color:#fff;cursor:pointer;text-decoration:none;box-shadow:0 2px 6px rgba(0,0,0,.2);">&#8986;</a>
      <a class="icon-calendar" id="btn-gantt-calendar-inline" title="Holidays" href="#" style="width:24px;height:24px;font-size:11px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:#B8860B;color:#fff;cursor:pointer;text-decoration:none;box-shadow:0 2px 6px rgba(0,0,0,.2);"></a>
      <button id="gantt-win-min" title="Minimise" style="font-weight:700;">&minus;</button>
      <button id="gantt-win-expand" title="Fullscreen">&#x26F6;</button>
      <button id="gantt-win-close" title="Close">&times;</button>
    </div>
  </div>
  <div id="gantt-win-body">
    <div id="gantt-win-loading">Loading Gantt chart&hellip;</div>
  </div>
  <button type="button" id="gn-btn" title="Notes">&#128221;</button>
</div>

<div id="win-dock"></div>
<style>
#win-dock { position:fixed; left:12px; bottom:0; display:flex; gap:6px; z-index:300500; pointer-events:none; }
.win-dock-tab { pointer-events:auto; background:#081142; color:#fff; border:none; border-radius:6px 6px 0 0; padding:7px 16px;
  font:700 12px Nunito,Arial,sans-serif; cursor:pointer; box-shadow:0 -2px 10px rgba(0,0,0,.25); }
.win-dock-tab:hover { background:#13206a; }
</style>
<script>
// window.dockMinimise(el, label): hides the window and shows a tab in the bottom bar;
// clicking the tab shows the window again where it was.
window.dockMinimise = function(el, label){
  if (!el || el.getAttribute('data-docked')) return;
  el.setAttribute('data-docked', '1');
  el.style.display = 'none';
  var tab = document.createElement('button');
  tab.type = 'button'; tab.className = 'win-dock-tab'; tab.textContent = label;
  tab.addEventListener('click', function(){
    el.style.display = ''; el.removeAttribute('data-docked'); tab.remove();
    if (window.popupBringToFront && el.id) window.popupBringToFront(el.id);
    // The Gantt may have been reloaded while hidden (columns measured as 0): redraw it now it is visible.
    if (el.id === 'gantt-win' && typeof window.reloadGantt === 'function') window.reloadGantt();
  });
  document.getElementById('win-dock').appendChild(tab);
};
// Closing a docked window also removes its tab.
window.dockForget = function(el){
  if (!el || !el.getAttribute('data-docked')) return;
  el.style.display = ''; el.removeAttribute('data-docked');
  document.querySelectorAll('#win-dock .win-dock-tab').forEach(function(t){ if (t.textContent === el.getAttribute('data-dock-label')) t.remove(); });
};
$(document).on('click', '#gantt-win-close', function(){ window.dockForget(document.getElementById('gantt-win')); });
document.addEventListener('actlib:open', function(){ window.dockForget(document.querySelector('.menu4-popup-cntnr')); });
$(document).on('click', '#gantt-win-min', function(e){
  e.stopPropagation();
  var w = document.getElementById('gantt-win');
  w.setAttribute('data-dock-label', 'Gantt Chart');
  window.dockMinimise(w, 'Gantt Chart');
  var n = document.getElementById('gn-win'); if (n) n.classList.remove('gn-open');
});
</script>

<!-- Gantt Notes: the user's own notes for the project, kept open while working on
     the Gantt (draggable, resizable). Each note is a strip; the tick removes it. -->
<div id="gn-win">
  <div id="gn-hdr"><span>Notes</span><button type="button" id="gn-close" title="Close">&times;</button></div>
  <div id="gn-form">
    <textarea id="gn-text" rows="2" placeholder="Write a note&hellip;"></textarea>
    <button type="button" id="gn-add">Add</button>
  </div>
  <div id="gn-list"></div>
</div>
<style>
#gn-btn { position:absolute; right:18px; bottom:16px; width:42px; height:42px; border-radius:50%; border:none;
  background:#0f5c52; color:#fff; font-size:19px; cursor:pointer; box-shadow:0 4px 12px rgba(0,0,0,.3); z-index:30; }
#gn-btn:hover { background:#0d4c44; }
#gn-win { display:none; position:fixed; right:40px; top:120px; width:340px; height:420px; min-width:260px; min-height:200px;
  background:#fff; border-radius:8px; box-shadow:0 8px 28px rgba(0,0,0,.35); z-index:100050; flex-direction:column;
  overflow:hidden; resize:both; font-family:Nunito,Arial,sans-serif; }
#gn-win.gn-open { display:flex; }
#gn-hdr { flex:none; display:flex; justify-content:space-between; align-items:center; background:#081142; color:#fff;
  padding:8px 12px; font-weight:700; font-size:14px; cursor:move; user-select:none; touch-action:none; }
#gn-close { background:none; border:none; color:#fff; font-size:20px; line-height:1; cursor:pointer; }
#gn-form { flex:none; display:flex; gap:6px; padding:8px 10px; border-bottom:1px solid #e3e7ee; }
#gn-text { flex:1; resize:none; min-height:40px; max-height:220px; overflow-y:auto; border:1px solid #bcc6d4; border-radius:5px; padding:5px 7px; font:inherit; font-size:13px; }
#gn-add { flex:none; border:none; background:#0f5c52; color:#fff; border-radius:5px; padding:0 14px; font-weight:700; cursor:pointer; }
#gn-list { flex:1; overflow-y:auto; padding:8px 10px; }
.gn-strip { display:flex; align-items:flex-start; gap:8px; background:#f4f6fa; border-left:4px solid #2ba899;
  border-radius:5px; padding:7px 8px; margin-bottom:7px; font-size:13px; color:#111; }
.gn-strip-text { flex:1; white-space:pre-wrap; word-wrap:break-word; }
.gn-strip-date { display:block; font-size:10px; color:#8a94a3; margin-top:3px; }
.gn-tick { flex:none; width:24px; height:24px; border-radius:50%; border:1.5px solid #2ba899; background:#fff; color:#2ba899;
  font-size:13px; line-height:1; cursor:pointer; }
.gn-tick:hover { background:#2ba899; color:#fff; }
.gn-empty { color:#8a94a3; font-size:12px; text-align:center; padding:16px 0; }
</style>
<script>
(function(){
  var win = document.getElementById('gn-win'), hdr = document.getElementById('gn-hdr');
  function esc(s){ return $('<div>').text(s == null ? '' : String(s)).html(); }
  function load(){
    $('#gn-list').html('<div class="gn-empty">Loading&hellip;</div>');
    $.ajax({ type:'POST', url:'../projectsmain/ganttnotes', dataType:'json', success:function(d){
      var n = (d && d.notes) || [];
      $('#gn-list').html(n.length ? n.map(function(x){
        return '<div class="gn-strip" data-id="' + x.id + '"><div class="gn-strip-text"><span class="gn-t">' + esc(x.text) + '</span>'
          + '<span class="gn-strip-date">' + esc((x.created_at || '').slice(0, 16)) + '</span></div>'
          + '<button type="button" class="gn-tick gn-edit" title="Edit">&#9998;</button>'
          + '<button type="button" class="gn-tick gn-done" title="Done">&#10003;</button></div>';
      }).join('') : '<div class="gn-empty">No notes yet.</div>');
    } });
  }
  function add(){
    var t = $.trim($('#gn-text').val()); if (!t) return;
    $('#gn-add').prop('disabled', true);
    $.ajax({ type:'POST', url:'../projectsmain/ganttnoteadd', dataType:'json', data:{ text:t },
      success:function(d){ $('#gn-add').prop('disabled', false); if (d && d.error === 'No') { $('#gn-text').val('').focus(); fit(); load(); } else alert((d && d.errortext) || 'Could not save.'); },
      error:function(){ $('#gn-add').prop('disabled', false); alert('Could not save.'); } });
  }
  // Keep Notes above the Gantt window (which raises itself on tap, e.g. on iPad).
  function toTop(){ var g = document.getElementById('gantt-win'); var z = g ? (parseInt(window.getComputedStyle(g).zIndex, 10) || 100000) : 100000; win.style.zIndex = z + 50; }
  $(document).on('click', '#gn-btn', function(e){ e.stopPropagation(); if (win.classList.contains('gn-open')) { win.classList.remove('gn-open'); return; } toTop(); win.classList.add('gn-open'); load(); $('#gn-text').focus(); });
  $(document).on('mousedown touchstart', '#gn-win', toTop);
  $(document).on('mousedown touchstart', '#gantt-win', function(){ if (win.classList.contains('gn-open')) setTimeout(toTop, 0); });
  $(document).on('click', '#gn-close', function(){ win.classList.remove('gn-open'); });
  $(document).on('click', '#gn-add', add);
  // The note box grows with the text, so the whole note is visible while typing.
  function fit(){ var t = document.getElementById('gn-text'); t.style.height = 'auto'; t.style.height = Math.min(220, t.scrollHeight + 2) + 'px'; }
  $(document).on('input', '#gn-text', fit);
  $(document).on('keydown', '#gn-text', function(e){ if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) { e.preventDefault(); add(); } });
  $(document).on('click', '.gn-done', function(){
    var strip = $(this).closest('.gn-strip'), id = strip.data('id');
    strip.fadeOut(200, function(){ strip.remove(); if (!$('#gn-list .gn-strip').length) $('#gn-list').html('<div class="gn-empty">No notes yet.</div>'); });
    $.ajax({ type:'POST', url:'../projectsmain/ganttnotedone', dataType:'json', data:{ id:id } });
  });
  // Edit a note in place: the strip text becomes a box with Save / Cancel.
  $(document).on('click', '.gn-edit', function(){
    var strip = $(this).closest('.gn-strip'), box = strip.find('.gn-strip-text');
    if (box.find('textarea').length) return;
    var old = strip.find('.gn-t').text();
    box.data('html', box.html());
    box.html('<textarea class="gn-edit-text" style="width:100%;box-sizing:border-box;min-height:50px;border:1px solid #bcc6d4;border-radius:5px;padding:5px 7px;font:inherit;font-size:13px;resize:vertical;"></textarea>'
      + '<div style="display:flex;gap:6px;justify-content:flex-end;margin-top:5px;">'
      + '<button type="button" class="gn-edit-cancel" style="border:1px solid #bcc6d4;background:#fff;border-radius:4px;padding:3px 10px;cursor:pointer;">Cancel</button>'
      + '<button type="button" class="gn-edit-save" style="border:none;background:#0f5c52;color:#fff;border-radius:4px;padding:3px 12px;font-weight:700;cursor:pointer;">Save</button></div>');
    var ta = box.find('textarea').val(old).focus()[0];
    ta.style.height = 'auto'; ta.style.height = Math.min(220, ta.scrollHeight + 2) + 'px';
  });
  $(document).on('click', '.gn-edit-cancel', function(){ var box = $(this).closest('.gn-strip-text'); box.html(box.data('html')); });
  $(document).on('click', '.gn-edit-save', function(){
    var strip = $(this).closest('.gn-strip'), t = $.trim(strip.find('.gn-edit-text').val());
    if (!t) return;
    $(this).prop('disabled', true);
    $.ajax({ type:'POST', url:'../projectsmain/ganttnoteedit', dataType:'json', data:{ id:strip.data('id'), text:t },
      success:function(d){ if (d && d.error === 'No') load(); else alert((d && d.errortext) || 'Could not save.'); },
      error:function(){ alert('Could not save.'); } });
  });
  // Close with the Gantt window.
  $(document).on('click', '#gantt-win-close', function(){ win.classList.remove('gn-open'); });
  // Drag by the header (mouse and touch).
  var drag = null;
  window.bindDragTouch(hdr, 'mousedown', function(e){
    if (e.target.id === 'gn-close') return;
    var r = win.getBoundingClientRect(); drag = { sx:e.clientX, sy:e.clientY, ox:r.left, oy:r.top };
    win.style.right = 'auto'; win.style.left = r.left + 'px'; e.preventDefault();
  });
  window.bindDragTouch(document, 'mousemove', function(e){
    if (!drag) return;
    win.style.left = Math.max(0, Math.min(drag.ox + e.clientX - drag.sx, window.innerWidth - 60)) + 'px';
    win.style.top = Math.max(0, Math.min(drag.oy + e.clientY - drag.sy, window.innerHeight - 40)) + 'px';
  }, { passive:false });
  window.bindDragTouch(document, 'mouseup', function(){ drag = null; });
})();
</script>

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
    var url = _ganttUrl + '&id=' + (pid || _ganttPid) + (_loadedMode === 'current' ? '&current=1' : '');
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
  // Opens the window in 'full' (toolbar icon) or 'current' (Cashflow Project
  // Duration panel: smaller window, monthly format, only due/reported activities).
  var _loadedMode = null;
  window.openGanttWin = function(mode){
    if (window.dockForget) window.dockForget(win);   // reopening a minimised Gantt
    var want = (mode === 'current') ? 'current' : 'full';
    var pid = openBtn ? openBtn.getAttribute('data-projectid') : '';
    if(want !== _loadedMode){
      if(want === 'current'){ var _fw = window._ganttCurFitW; if(!_fw){ try { _fw = +localStorage.getItem('ganttCurFitW') || 0; } catch(e){ _fw = 0; } } win.style.width = 'min(' + (_fw || 920) + 'px, 96vw)'; win.style.height='min(650px, 90vh)'; }
      else { win.style.width=''; win.style.height=''; }
    }
    win.classList.add('gw-open');
    win.classList.toggle('gw-current', want === 'current');
    document.dispatchEvent(new Event('gantt:open'));
    if(!pid){
      document.getElementById('gantt-win-body').innerHTML = '<div style="padding:50px;text-align:center;color:#888;font-size:14px;">Please select a project first.</div>';
      return;
    }
    if(want !== _loadedMode) _loaded = false;
    if(!_loaded){
      _loaded = true;
      _loadedMode = want;
      _loadGanttWin(pid);
    }
  };

  var openBtn = document.getElementById('gantt-win-open');
  if(openBtn){
    openBtn.addEventListener('click', function(e){
      e.preventDefault();
      window.openGanttWin('full');
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
  var ids = ['cwbs-popup','cwbsed-popup','pdoc-popup','pdoc-viewer-popup','gpm-overlay','gantt-win','cb-win','cb-btn','reslib-win','vendorlib-win','rel-win','estreport-win','menu4-popup-cntnr','procurement-win','storeoffice-win','userpermissions-win'];
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
  var floaters = ['cwbs-popup','cwbs-iowp','cwbs-sgp','cwbsed-popup','pdoc-popup','pdoc-viewer-popup','gantt-win','cb-win','reslib-win','vendorlib-win','rel-win','estreport-win','menu4-popup-cntnr','procurement-win','storeoffice-win','userpermissions-win','cd-modal','cf-modal','pd-modal','gkp-popup','gkm-modal','gcm-popup','gpm-overlay'];

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
  document.addEventListener('chat:open',   function(){ window.popupBringToFront('cb-win'); window.popupCascade('cb-win'); });
  document.addEventListener('reslib:open', function(){ window.popupBringToFront('reslib-win'); window.popupCascade('reslib-win'); });
  document.addEventListener('vendorlib:open', function(){ window.popupBringToFront('vendorlib-win'); window.popupCascade('vendorlib-win'); });
  document.addEventListener('rel:open',    function(){ window.popupBringToFront('rel-win'); window.popupCascade('rel-win'); });
  document.addEventListener('estreport:open', function(){ window.popupBringToFront('estreport-win'); window.popupCascade('estreport-win'); });
  document.addEventListener('actlib:open', function(){ window.popupBringToFront('menu4-popup-cntnr'); window.popupCascade('menu4-popup-cntnr'); });
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
    #procurement-win,
    #storeoffice-win,
    #userpermissions-win,
    .menu4-popup-cntnr,
    #reslib-win,
    #vendorlib-win,
    #rel-win {
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

    /* Project Estimate: a centred window, not full-screen, on tablet/phone. */
    #estreport-win { left: 3vw; width: 94vw; top: 40px; height: calc(100vh - 70px); }

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
