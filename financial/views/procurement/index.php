<?php
/**
 * Created by PhpStorm.
 * User: SolmindsDelli5
 * Date: 01-08-2017
 * Time: 03:52 PM
 */

//use dektrium\user\models\User;
//use Yii;
use amnah\yii2\user\models\User;
use app\models\DepartmentTab;
use app\models\UserTabs;
//use \amnah\yii2\user\models\User;
?>
<div class="container-fluid procu-accordion">
    <div class="row">
    	<div class="col-md-12">
		    <div class="panel-group acco-billofres-active" id="accordionindex">
                <?php 
                    $uid = Yii::$app->user->Id; 
                    $row = User::findOne($uid);
                    $roleid = $row->superuser;
                    
                    $user=User::find()->where(['id'=>Yii::$app->user->id])->one();
                    $functiontabs=DepartmentTab::find()->orderby(['sortorder'=>SORT_ASC])->all();

                    if($user->role_id !=0){

                        foreach ($functiontabs as $key => $functiontab) {

                            $functabs=UserTabs::find()->where(['function_id'=>2])->andWhere(['tab_id'=>$functiontab->tab_id])->andWhere(['role_id'=>$user->role_id])->one();

                                if($functabs){
                                    if($functabs->tab_id==35){

                                    }elseif($functabs->tab_id==36){

                                    }else{
                                  //echo $functiontab->tabname;exit;
                                    echo $this->render(''.$functiontab->tabname.'');
                                 }
                                }
                        }
                    }else{

                ?>
                    <?php echo $this->render('projects'); ?>
                    <?php //echo $this->render('_jobcard'); ?>
                    <?php //echo $this->render('_billresources'); ?>
                    <?php echo $this->render('vendors'); ?>
                    <?php //echo $this->render('placeorder'); ?>
                    <?php echo $this->render('allorders'); ?>
                    
                    <?php echo $this->render('orders'); ?>
                    <?php echo $this->render('despatchorder'); 
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
      8:'one',
      9:'two',
      10:'three',
      11:'four'
      
      };
      $(this).addClass('acco-'+wordss[va]);

    });

  });
</script>
