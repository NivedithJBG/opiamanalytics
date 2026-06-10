
<?php
/**
 * Created by PhpStorm.
 * User: SolmindsDelli5
 * Date: 01-08-2017
 * Time: 03:52 PM
 */

//use dektrium\user\models\User;
use amnah\yii2\user\models\User;
use app\models\UserProfile;
use app\models\Departments;
//use amnah\yii2\user\models\UserProfile;
?>

<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>GeoTech</title>
    <!-- Bootstrap core CSS -->
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/bootstrap.min.css?v=2" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jumbotron-narrow.css" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jquery.autocomplete.css" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/custom.css" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/jquery-ui.css" rel="stylesheet">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/ipad.css?v=1.38" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,500,700' rel='stylesheet' type='text/css'>
<!--    <link href="--><?php //echo Yii::app()->request->baseUrl; ?><!--/cssnew/jumbotron-narrow.css" rel="stylesheet">-->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/icons.css" rel="stylesheet">
     <link href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/style.css?v=1.18" rel="stylesheet">
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/custom.js"></script>
    
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/common.js?v=1.6"></script>
    
    
     
    <style type="text/css">
        span#clearnotif:hover {
            text-decoration: underline;
        }
    </style>

</head>

<body class="procurement">

   
    <?php 

 //echo "hi"; exit;
       $requestaction=Yii::$app->urlManager->parseUrl(Yii::app()->request);
        //echo $requestaction;exit;
       $controller=explode('/', $requestaction);
       // echo $controller[1];exit;
      
        if($controller[0]=='procurement'):                 
           $currentmenu="Procurement Process";
        elseif($controller[1]=='procurement'):       
            $currentmenu="Masters";
        elseif($controller[0]=='engineering'):
           $currentmenu="Engineering";
       elseif($controller[0]=='projects1'):
           $currentmenu="Projects";
        elseif($controller[0]=='report'):
           $currentmenu="Reporting";
        elseif($controller[0]=='Administration'):
           $currentmenu="Administration";
       elseif($controller[0]=='FinanceRequests'):
           $currentmenu="Finance";
        elseif($controller[0]=='projects'):
           $currentmenu="Operations";
        elseif($controller[0]=='cmd'):
           $currentmenu="CMD";
       //else:
         //   $currentmenu="Masters";    
        endif;

        //echo $currentmenu; exit;
    ?>


<aside class="leftside">
    <nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="#">
          <!--<img src="images/logo.jpg" width="" height="" alt />-->
          <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/logo3.jpg" width="" height="" alt />
      </a>
    </div>
    <div class="collapse navbar-collapse headernav"  id="myNavbar">
		<h1><?php  echo $currentmenu;?></h1>
		
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
            <?php if(Yii::$app->user->isGuest): ?>
                
            <?php else:?>

                 <li><a class="icon-tools" href="<?php echo Yii::$app->urlManager->createUrl('procurement/masterindex')?>"> 
                            <!--<span class="glyphicon glyphicon-shopping-cart" style="font-size: 1.2em;padding-left: 3px;"></span>Cart-->
                            <!-- <span class="count"><?php //echo Yii::app()->controller->getCartCount();?></span> -->
                        </a>
                    </li>


                <?php //if(User::model()->isAdmin())://  

                     $uid = Yii::$app->user->Id; 
                     $row = User::findOne($uid);
                     $roleid = $row->superuser; 

                     if ($roleid==1):  ?>

                   
                     <li   id="notification_li">
                        
                        <a href="#" class="icon-bell2 dropdown-toggle" data-toggle="dropdown" claddss="notificationread" id="notificationLink?">
                            <?php// if(Yii::app()->controller->getNotificationCount()>0):?>
                            <span class="count" id="notification_count"><?php //echo Yii::app()->controller->getNotificationCount();?></span>
                        <?php endif;?>
                            
                        </a>
                        <ul class="dropdown-menu ">
            			  <li> <?php //echo Yii::app()->controller->getNotifications();?></li>
            			  
            			 
            			</ul>
                        

                        <!--<div id="notificationContainer">
                            <div id="notificationTitle">Notifications
                                <span style="float: right;cursor: pointer" id="clearnotif">Clear all</span>
                            </div>
                            <div id="notificationsBody" class="notifications" style="height: 300px;overflow-y: auto">
                                <?php// echo Yii::app()->controller->getNotifications();?>
                            </div>
                            
                        </div> -->
                    </li> 
                    <li><a class="icon-shopping_cart" href="<?php echo Yii::$app->urlManager->createUrl('procurement/index#Cart')?>">
                            <!--<span class="glyphicon glyphicon-shopping-cart" style="font-size: 1.2em;padding-left: 3px;"></span>Cart-->
                            <span class="count"><?php //echo Yii::app()->controller->getCartCount();?></span>
                        </a>
                    </li>
                    <li><a href="#" class="icon-user3 dropdown-toggle" data-toggle="dropdown">
                			
                		</a>
                		<ul class="dropdown-menu" style="height: auto;margin-left: -55px;">
                		    <li><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?php echo Yii::$app->user->displayName ;?></a></li>
                            <li class="dropdownli"><a href="<?php echo Yii::$app->urlManager->createUrl('ProjectPricing/index')?>">Project Pricing</a></li>
                            <li class="dropdownli"><a href="<?php echo Yii::$app->urlManager->createUrl('Legal/index')?>">Legal</a></li>
                            <li class="dropdownli"><a href="<?php echo Yii::$app->urlManager->createUrl('doccumentManager/index')?>">Document Manager</a></li>
                            <li class="dropdownli"><a href="<?php echo Yii::$app->urlManager->createUrl('projects/masters')?>">Masters</a></li>
                            <li class="dropdownli"><a href="<?php echo Yii::$app->urlManager->createUrl('user/user/admin')?>">Human Resources</a></li>
                            <li class="dropdownli"><a href="<?php echo Yii::$app->urlManager->createUrl('user/user/logout')?>">Logout</a></li>
                        </ul>
                	</li>
                	<li><a href="#" class="icon-menu1"></a></li>
                    
                <?php //else:?>
                    <?php
                   

                    //$user=User::model()->active()->findbyPk(Yii::app()->user->id);
                    $user = User::findOne(Yii::$app->user->id);
                   // print_r($user); exit;
                   // $userfun=UserProfile::model()->find(array('condition'=>'user_id='.Yii::app()->user->id.' '))->function;

                    //$userfun = UserProfile::findOne(array('user_id='.Yii::$app->user->id.' '));
                    $userfun = UserProfile::find()->where(['user_id' => Yii::$app->user->id])->one()->function;
                 
                    if($user['superuser']==2):
                        echo '<li><a href="'.Yii::app()->createUrl('projects1/index').'">Projects</a></li>';
                    endif;

                    $searchForValue = ',';

                    if( strpos($userfun, $searchForValue) !== false ) {
                         $functions=explode(",",$userfun);
                    }
                    else{
                        $functions= array($userfun);
                    }

                    
                    if(count($functions)>0):
                        foreach($functions AS $function): 
                            //$depart=$function['name'];  
                           // echo $depart; exit;
                            // $depname = Departments::findOne($function->name); 

                            $depname = Departments::find()->where(['dep_id' => $function])->one()->name;

                             //echo $depname; exit;

                            if($depname=='Finance'):
                                echo '<li><a href="'.Yii::$app->urlManager->createUrl('FinanceRequests/index').'">Finance</a></li>';
                            elseif($depname=='Procurement'):
                                echo '<li><a href="'.Yii::$app->urlManager->createUrl('Procurement/index').'">Procurement</a></li>';
                            elseif($depname=='Administration'):
                                echo '<li><a href="'.Yii::$app->urlManager->createUrl('Administration/index').'">Administration</a></li>';
                            elseif($depname=='Operations'):
                                echo '<li><a href="'.Yii::$app->urlManager->createUrl('projects/report').'">Operations</a></li>';
                            elseif($depname=='Engineering'):
                                echo '<li><a href="'.Yii::$app->urlManager->createUrl('engineering/index').'">Engineering</a></li>';
                            elseif($depname=='Legal'):
                                echo '<li><a href="'.Yii::$app->urlManager->createUrl('Legal/index').'">Legal</a></li>';
                            elseif($depname=='Reporting'):
                                echo '<li><a href="'.Yii::$app->urlManager->createUrl('report/index').'">Reporting</a></li>';
                            endif;
                        endforeach;
                    endif;

                    ?>
                    <!-- <?php if($user['superuser']==2):?>
                        <li><a href="<?php echo Yii::app()->createUrl('projects/masters')?>">Masters</a></li>
                    <?php endif;?> -->
                    <?php if($user['superuser']!=4):?>
                        <!--<li id="notification_li">
                            <php if(Yii::app()->controller->getNotificationCount()>0):?>
                                <span id="notification_count"><php echo Yii::app()->controller->getNotificationCount();?></span>
                            <php endif;?>
                            <a href="#" class="notificationread" id="notificationLink">Notifications</a>

                            <div id="notificationContainer">
                                <div id="notificationTitle">Notifications
                                    <span style="float: right;cursor: pointer" id="clearnotif">Clear all</span>
                                </div>
                                <div id="notificationsBody" class="notifications" style="height: 300px;overflow-y: auto">
                                    <php echo Yii::app()->controller->getNotifications();?>
                                </div>
                            </div>
                        </li>-->
                    <?php endif;?>
                    <li><a href="<?php echo Yii::$app->urlManager->createUrl('user/logout')?>">Logout(<?php echo Yii::$app->user->identity->username;?>)</a></li>
                <?php endif;?>
            <?php //endif;?>
        </ul>
      
      
      
      
    </div>
  </div>
</nav>

<?php echo $content; ?>

</aside>
<aside class="rightside">
	<div class="col-md-12">
			 <div class="rightside-logo">
			     <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo3.jpg" width="" height="" alt />
			     <!-- <img src="<?php //echo Yii::app()->request->baseUrl; ?>/images/logo3.jpg?v=1" id="logoimage"> -->
			     </div> 
		  
		  
		  <ul class="nav nav-pills nav-stacked">
            <?php if(Yii::app()->user->isGuest): ?>
                <li><a href="<?php echo Yii::app()->createUrl('user/user/login')?>">Login</a></li>
            <?php else:?>
                <?php if(User::model()->isAdmin()): ?>
                    <!--<li><a href="<?php /*echo Yii::app()->createUrl('projects/index')*/?>">Pricing and Scheduling</a></li>-->
                    <li><a href="<?php echo Yii::app()->createUrl('engineering/index')?>">Engineering</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('projects1/index')?>">Projects</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('report/index')?>">Reporting</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('cmd/index')?>">CMD</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('procurement/index')?>">Procurement</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('Administration/index')?>">Administration</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('FinanceRequests/index')?>">Finance</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('projects/report')?>">Operations</a></li>
                    
                    <li><a href="<?php echo Yii::app()->createUrl('procurement/index#Cart')?>">
                            <span class="glyphicon glyphicon-shopping-cart" style="font-size: 1.2em;padding-left: 3px;"></span>Cart
                            <span class="cartcount"><?php echo Yii::app()->controller->getCartCount();?></span>
                        </a>
                    </li>
                    
                <?php else:?>
                    <?php
                    $user=User::model()->active()->findbyPk(Yii::app()->user->id);
                    $userfun=UserProfile::model()->find(array('condition'=>'user_id='.Yii::app()->user->id.' '))->function;
                    if($user['superuser']==2):
                        echo '<li><a href="'.Yii::app()->createUrl('projects1/index').'">Projects</a></li>';
                    endif;
                    $functions=explode(",",$userfun);
                    if(count($functions)>0):
                        foreach($functions AS $function):
                            $depname=Departments::model()->findByPk($function)->name;
                            if($depname=='Finance'):
                                echo '<li><a href="'.Yii::app()->createUrl('FinanceRequests/index').'">Finance</a></li>';
                            elseif($depname=='Procurement'):
                                echo '<li><a href="'.Yii::app()->createUrl('Procurement/index').'">Procurement</a></li>';
                            elseif($depname=='Administration'):
                                echo '<li><a href="'.Yii::app()->createUrl('Administration/index').'">Administration</a></li>';
                            elseif($depname=='Operations'):
                                echo '<li><a href="'.Yii::app()->createUrl('projects/report').'">Operations</a></li>';
                            elseif($depname=='Engineering'):
                                echo '<li><a href="'.Yii::app()->createUrl('engineering/index').'">Engineering</a></li>';
                            elseif($depname=='Legal'):
                                echo '<li><a href="'.Yii::app()->createUrl('Legal/index').'">Legal</a></li>';
                            elseif($depname=='Reporting'):
                                echo '<li><a href="'.Yii::app()->createUrl('report/index').'">Reporting</a></li>';
                            endif;
                        endforeach;
                    endif;?>
                    <!-- <?php if($user['superuser']==2):?>
                        <li><a href="<?php echo Yii::app()->createUrl('projects/masters')?>">Masters</a></li>
                    <?php endif;?> -->
                    <?php if($user['superuser']!=4):?>
                        <li id="notification_li">
                            <?php if(Yii::app()->controller->getNotificationCount()>0):?>
                                <span id="notification_count"><?php echo Yii::app()->controller->getNotificationCount();?></span>
                            <?php endif;?>
                            <a href="#" class="notificationread" id="notificationLink">Notifications</a>

                            <div id="notificationContainer">
                                <div id="notificationTitle">Notifications
                                    <span style="float: right;cursor: pointer" id="clearnotif">Clear all</span>
                                </div>
                                <div id="notificationsBody" class="notifications" style="height: 300px;overflow-y: auto">
                                    <?php echo Yii::app()->controller->getNotifications();?>
                                </div>
                                <!--<div id="notificationFooter"><a href="#">See All</a></div>-->
                            </div>
                        </li>
                    <?php endif;?>
                    <li><a href="<?php echo Yii::app()->createUrl('user/user/logout')?>">Logout(<?php echo Yii::app()->user->name;?>)</a></li>
                <?php endif;?>
            <?php endif;?>
        </ul>
		  
		</div>

</aside>
        
    
<div class="header" style="border:0px; display:none;">
    <!--<img src="<?php //echo Yii::app()->request->baseUrl; ?>/images/logo3.jpg?v=1" id="logoimage">-->
    <nav class="clearfix">

        <ul class="clearfix">
            <?php if(Yii::app()->user->isGuest): ?>
                <li><a href="<?php echo Yii::app()->createUrl('user/user/login')?>">Login</a></li>
            <?php else:?>
                <?php if(User::model()->isAdmin()): ?>
                    <!--<li><a href="<?php /*echo Yii::app()->createUrl('projects/index')*/?>">Pricing and Scheduling</a></li>-->
                    <li><a href="<?php echo Yii::app()->createUrl('engineering/index')?>">Engineering</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('projects1/index')?>">Projects</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('report/index')?>">Reporting</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('cmd/index')?>">CMD</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('procurement/index')?>">Procurement</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('Administration/index')?>">Administration</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('FinanceRequests/index')?>">Finance</a></li>
                    <li><a href="<?php echo Yii::app()->createUrl('projects/report')?>">Operations</a></li>
                    <li id="notification_li">
                        <?php if(Yii::app()->controller->getNotificationCount()>0):?>
                            <span id="notification_count"><?php echo Yii::app()->controller->getNotificationCount();?></span>
                        <?php endif;?>
                        <a href="#" class="notificationread" id="notificationLink">Notifications</a>

                        <div id="notificationContainer">
                            <div id="notificationTitle">Notifications
                                <span style="float: right;cursor: pointer" id="clearnotif">Clear all</span>
                            </div>
                            <div id="notificationsBody" class="notifications" style="height: 300px;overflow-y: auto">
                                <?php echo Yii::app()->controller->getNotifications();?>
                            </div>
                            <!--<div id="notificationFooter"><a href="#">See All</a></div>-->
                        </div>
                    </li>
                    <li><a href="<?php echo Yii::app()->createUrl('procurement/index#Cart')?>">
                            <span class="glyphicon glyphicon-shopping-cart" style="font-size: 1.2em;padding-left: 3px;"></span>Cart
                            <span class="cartcount"><?php echo Yii::app()->controller->getCartCount();?></span>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="settings"><span class="glyphicon glyphicon-user"></span> <?php echo Yii::app()->user->name;?></a>
                        <ul class="dropdown-menu" id="settingsContainer" style="height: auto;margin-left: -55px;">
                            <li class="dropdownli"><a href="<?php echo Yii::app()->createUrl('ProjectPricing/index')?>">Project Pricing</a></li>
                            <li class="dropdownli"><a href="<?php echo Yii::app()->createUrl('Legal/index')?>">Legal</a></li>
                            <li class="dropdownli"><a href="<?php echo Yii::app()->createUrl('doccumentManager/index')?>">Document Manager</a></li>
                            <li class="dropdownli"><a href="<?php echo Yii::app()->createUrl('projects/masters')?>">Masters</a></li>
                            <li class="dropdownli"><a href="<?php echo Yii::app()->createUrl('user/user/admin')?>">Human Resources</a></li>
                            <li class="dropdownli"><a href="<?php echo Yii::app()->createUrl('user/user/logout')?>">Logout</a></li>
                        </ul>
                    </li>
                <?php else:?>
                    <?php
                    $user=User::model()->active()->findbyPk(Yii::app()->user->id);
                    $userfun=UserProfile::model()->find(array('condition'=>'user_id='.Yii::app()->user->id.' '))->function;
                    if($user['superuser']==2):
                        echo '<li><a href="'.Yii::app()->createUrl('projects1/index').'">Projects</a></li>';
                    endif;
                    $functions=explode(",",$userfun);
                    if(count($functions)>0):
                        foreach($functions AS $function):
                            $depname=Departments::model()->findByPk($function)->name;
                            if($depname=='Finance'):
                                echo '<li><a href="'.Yii::app()->createUrl('FinanceRequests/index').'">Finance</a></li>';
                            elseif($depname=='Procurement'):
                                echo '<li><a href="'.Yii::app()->createUrl('Procurement/index').'">Procurement</a></li>';
                            elseif($depname=='Administration'):
                                echo '<li><a href="'.Yii::app()->createUrl('Administration/index').'">Administration</a></li>';
                            elseif($depname=='Operations'):
                                echo '<li><a href="'.Yii::app()->createUrl('projects/report').'">Operations</a></li>';
                            elseif($depname=='Engineering'):
                                echo '<li><a href="'.Yii::app()->createUrl('engineering/index').'">Engineering</a></li>';
                            elseif($depname=='Legal'):
                                echo '<li><a href="'.Yii::app()->createUrl('Legal/index').'">Legal</a></li>';
                            elseif($depname=='Reporting'):
                                echo '<li><a href="'.Yii::app()->createUrl('report/index').'">Reporting</a></li>';
                            endif;
                        endforeach;
                    endif;?>
                    <!-- <?php if($user['superuser']==2):?>
                        <li><a href="<?php echo Yii::app()->createUrl('projects/masters')?>">Masters</a></li>
                    <?php endif;?> -->
                    <?php if($user['superuser']!=4):?>
                        <li id="notification_li">
                            <?php if(Yii::app()->controller->getNotificationCount()>0):?>
                                <span id="notification_count"><?php echo Yii::app()->controller->getNotificationCount();?></span>
                            <?php endif;?>
                            <a href="#" class="notificationread" id="notificationLink">Notifications</a>

                            <div id="notificationContainer">
                                <div id="notificationTitle">Notifications
                                    <span style="float: right;cursor: pointer" id="clearnotif">Clear all</span>
                                </div>
                                <div id="notificationsBody" class="notifications" style="height: 300px;overflow-y: auto">
                                    <?php echo Yii::app()->controller->getNotifications();?>
                                </div>
                                <!--<div id="notificationFooter"><a href="#">See All</a></div>-->
                            </div>
                        </li>
                    <?php endif;?>
                    <li><a href="<?php echo Yii::app()->createUrl('user/user/logout')?>">Logout(<?php echo Yii::app()->user->name;?>)</a></li>
                <?php endif;?>
            <?php endif;?>
        </ul>
        <a href="#" id="pull">Menu</a>
    </nav>

</div>

<script type="text/javascript">
    $(function(){
        var type = window.location.hash.substr(1);
        //alert(type)
        setTimeout(function() {
            $('#'+type).trigger('click');
        },1000);
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
            var userid='<?php echo Yii::app()->user->id;?>';
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
            var userid='<?php echo Yii::app()->user->id;?>';
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
        var userid='<?php echo Yii::app()->user->id;?>';
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
		            $('.acco-cart input[type=radio]').trigger('click');
			       $('.placeOrderPop-cntnt .icon-close').trigger('click');
			       
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

</body>


</html>
