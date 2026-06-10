<?php
use amnah\yii2\user\models\User;
use app\models\DepartmentTab;
use app\models\UserTabs;
?>


<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projects/over_menu_operationreport.js" type="text/javascript"></script>

<input type="hidden" id="selectedProjectId">
<input type="hidden" id="selectedWorkgrouId">
<input type="hidden" id="selectedIOWId">

<div class="container-fluid procu-accordion">
  <!-- tab1 -->
  <div class="menu5-popup-cntnr" id="asset" style="display: none;" >
    <div class="menu5-cntnt-wrpr">
      <div class="icon-groups type"> 
        <!-- <a href="#" title="Close" class="btn btn-primary text-button menu01-win-close">&#10006; Close</a> -->
      </div>
      <!--<div style="text-align: center;"><b><h4>Project Report</h4></b></div>-->
      <div class="col-md-12">
        <div class="panel-group acco-one-active" id="accordionoperre">

        <?php echo $this->render('_assetregister'); ?>
    
        </div>
      </div>
    </div>
  </div>
  <!-- tab1 ends -->
    
  <div class="row">
    <div class="col-md-12">
      <div class="panel-group acco-one-active" id="accordionoper" >
        <?php
          $user=User::find()->where(['id'=>Yii::$app->user->id])->one();
          $functiontabs=DepartmentTab::find()->orderby(['sortorder'=>SORT_ASC])->all();

          if($user->role_id !=0){

            foreach ($functiontabs as $key => $functiontab) {

               $functabs=UserTabs::find()->where(['function_id'=>4])->andWhere(['tab_id'=>$functiontab->tab_id])->andWhere(['role_id'=>$user->role_id])->one();

                if($functabs){
                  //echo $functiontab->tabname;exit;
                  if($functabs->tab_id==39){

                  }elseif($functabs->tab_id==42){

                  }else{

                    echo $this->render(''.$functiontab->tabname.'');
                  }
                }
              }
            }
           else{
        ?>



        <?php echo $this->render('_rproject'); ?> 

      <!--   <//?php echo $this->render('_purchorders'); ?>  -->

       <!--  <//?php echo $this->render('_subcontactor'); ?>  -->

        <?php echo $this->render('_orders'); ?>
        <?php echo $this->render('_muster'); ?>
       <!--  <//?php echo $this->render('_purchorders'); ?> -->
       <!--  <//?php echo $this->render('_issueslips'); ?> -->
        <?php echo $this->render('_workorders'); ?>
        
        <?php //echo $this->render('_leasetab'); ?>
       <!--  <//?php echo $this->render('_workordersbill'); ?> -->
        <?php //echo $this->render('_leaseorders'); ?>


        <?php echo $this->render('_log'); ?>

      <!--   <//?php echo $this->render('_logbook'); ?> -->


       <!--  <//?php echo $this->render('_fuelslip'); ?> -->


        <!-- <//?php echo $this->render('_activityreport'); ?> -->
        <?php echo $this->render('_schedulereport'); ?>
        <!-- <//?php echo $this->render('_muster'); ?> -->
      <!--   <//?php echo $this->render('_clientbill'); ?> -->
     
        <?php //echo $this->render('_assetregistertab'); ?>
        <?php
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

    var value = 0;
    $('.panel-default').removeClass('acco-one acco-two acco-three acco-four acco-five acco-six acco-seven acco-eight acco-nine acco-ten acco-eleven acco-twelve acco-thirteen acco-fourteen acco-fifteen acco-sixteen');


    $('.panel-default').each(function() { 

     var va = value++; 
      var wordss = {
      1:'one',
      2:'two',
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
      };
      $(this).addClass('acco-'+wordss[va]);

    });

  });
</script>
