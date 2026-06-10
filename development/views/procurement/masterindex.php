<?php
/**
 * Created by PhpStorm.
 * User: SolmindsDelli5
 * Date: 01-08-2017
 * Time: 03:52 PM
 */
use amnah\yii2\user\models\User;
?>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/over_menu_resourcemaster.js" type="text/javascript"></script>
<div class="container-fluid procu-accordion">
    <div class="row">
    <div class="menu-popup-cntnr">

    <div class="menu-cntnt-wrpr">
        <div class="icon-groups type"> 
            <!-- <a href="#" title="Close" class="btn btn-primary text-button menu-win-close">&#10006; Close</a> -->
        </div>
        <div class="row show-grid"> </div>
            <!--<div style="text-align: center;"><b><h4>Resource Master</h4></b></div>-->
            <div class="col-md-12">
                <div class="panel-group acco-billofres-active" id="accordionmaster">
                    <?php 
                        $uid = Yii::$app->user->Id; 
                        $row = User::findOne($uid);
                        $roleid = $row->superuser; 

                    ?>

                    <?php echo $this->render('_resourcetypes'); ?>
                    <?php echo $this->render('_resources'); ?>
                    <?php echo $this->render('_mastervendors'); ?>
                    <?php echo $this->render('_termsconditions'); ?>   

                </div>
            </div>
            </div>
        </div>
    </div>
    
</div>