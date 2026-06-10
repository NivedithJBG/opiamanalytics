
<?php 
use amnah\yii2\user\models\User;

 ?>

 <!--div class="container procu-accordion">
    <div class="row">
        <div class="col-md-12">
            <div class="panel-group acco-billofres-active" > -->
<?php 
$uid = Yii::$app->user->Id; 
$row = User::findOne($uid);
 $roleid = $row->superuser; 
if ($roleid==1):  ?>

    <?php echo $this->render('_financeverifications'); ?>
    <?php echo $this->render('_financeapprovals'); ?>
    <?php echo $this->render('_financejournals'); ?>
    <?php echo $this->render('_vouchers'); ?>
    <?php echo $this->render('_cashbook'); ?>
    <?php echo $this->render('_bankbook'); ?>
    <?php echo $this->render('_journalbook'); ?>
    <?php echo $this->render('_ledger'); ?>
    <?php echo $this->render('_trialbal'); ?>
    <?php echo $this->render('_reports'); ?>
    
<?php else:?>
    <?php
    ///echo count($tabs);exit;
    if(count($tabs)>0):
        //$financetabs=explode(',',$tabs);
        //echo count($financetabs);exit;
        foreach($tabs AS $tab):
            $tabname = DepartmentTab::model()->findByPk($tab['tab_id'])->tabname;
            echo $this->render($tabname);
        endforeach;
    endif;
    ?>
<?php endif; ?> </div>
<!--</div>
</div></div>-->




