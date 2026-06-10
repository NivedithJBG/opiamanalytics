<?php
use amnah\yii2\user\models\User;
use app\models\DepartmentTab;
use app\models\UserTabs;
?>
<div class="container-fluid procu-accordion">
    <div class="row">
        <div class="col-md-12">
            <div class="panel-group acco-one-active" id="accordionfin">
                <?php
                    $user=User::find()->where(['id'=>Yii::$app->user->id])->one();
                    $functiontabs=DepartmentTab::find()->orderby(['sortorder'=>SORT_ASC])->all();

                    if($user->role_id !=0){

                        foreach ($functiontabs as $key => $functiontab) {

                            $functabs=UserTabs::find()->where(['function_id'=>3])->andWhere(['tab_id'=>$functiontab->tab_id])->andWhere(['role_id'=>$user->role_id])->one();

                                if($functabs){
                                  //echo $functiontab->tabname;exit;
                                    if($functabs->tab_id==37){

                                    }else{
                                    echo $this->render(''.$functiontab->tabname.'');
                                 }
                                }
                        }
                    }else{
                ?>
                <?php echo $this->render('_paymentdue'); ?>
                <?php echo $this->render('_financeverifications'); ?>
                <?php //echo $this->render('_financeapprovals'); ?>
                <?php echo $this->render('_fundreceipt'); ?>
                <?php echo $this->render('_financejournals'); ?>
                <?php echo $this->render('_vouchers'); ?>
                <?php echo $this->render('_cashbook'); ?>
                <?php echo $this->render('_bankbook'); ?>
                <?php echo $this->render('_journalbook'); ?>
                <?php echo $this->render('_ledger'); ?>
                <?php echo $this->render('_trialbal'); ?>
                <?php echo $this->render('_profit_loss'); ?>
                <?php echo $this->render('_balance_sheet'); ?>
                <?php //echo $this->render('_reports'); ?>
                <?php echo $this->render('_audit'); 
                
                }

                ?>
    
            </div>
        </div>
    </div>

    <style>
        .tab-content{
            max-height:unset;
        }

    </style>
</div>




<script>
  $( document ).ready(function() {

    var value = 1;
    $('.panel-default').removeClass('acco-one acco-two acco-three acco-four acco-five acco-six acco-seven acco-eight acco-nine acco-ten acco-eleven acco-twelve acco-thirteen');


    $('.panel-default').each(function() {

     var va = value++;

      var wordss = {
      1: 'one',
      2: 'two',
      3:'three',
      4:'four',
      5:'five',
      6:'six',
      7:'seven',
      8:'eight',
      9:'nine',
      10:'ten',
      11:'eleven',
      12:'one',
      13:'two',
      14:'three',
      15:'four',
      16:'one',
      17:'two'
      };
      $(this).addClass('acco-'+wordss[va]);

    });

  });
</script>
