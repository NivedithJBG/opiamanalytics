<!-- <script src="<?php //echo Yii::$app->request->baseUrl; ?>/jsnew/projects/over_menu_usermasters.js" type="text/javascript"></script> -->
<!-- <script src="<?php //echo Yii::$app->request->baseUrl; ?>/js/jquery-3.3.1.min.js"></script> 

<script src="<?php //echo Yii::$app->request->baseUrl; ?>/js/bootstrap.js"></script> -->
<?php
use amnah\yii2\user\models\User;

?>
<div class="container-fluid procu-accordion">
    <div class="menu6-popup-cntnr">
    <div class="row">
    	<div class="col-md-12">
            <div class="panel-group acco-one-active" id="accordionuserrole">
		    
                <?php 
                    $uid = Yii::$app->user->Id; 
                    $row = User::findOne($uid);
                    $roleid = $row->superuser; 
                ?>
                    
               <?php echo $this->render('adduserrole'); ?> 
               <?php echo $this->render('createuser'); ?> 

               </div>    
        </div>
    </div>

    <style>
        .tab-content{
            max-height:unset;
        }

    </style>
</div>
</div>