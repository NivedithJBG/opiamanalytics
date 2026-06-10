<?php 
use amnah\yii2\user\models\User;
use app\models\DepartmentTab;
use app\models\UserTabs;
 ?>

<?php 
$uid = Yii::$app->user->Id; 
$row = User::findOne($uid);
$roleid = $row->superuser; 

$user=User::find()->where(['id'=>Yii::$app->user->id])->one();
$functiontabs=DepartmentTab::find()->all();
  if($user->role_id !=0){
        foreach ($functiontabs as $key => $functiontab) {
            $functabs=UserTabs::find()->where(['function_id'=>1])->andWhere(['tab_id'=>$functiontab->tab_id])->andWhere(['role_id'=>$user->role_id])->one();

            if($functabs){
              //echo $functiontab->tabname;exit;
              if($functabs->tab_id==32){

              }elseif($functabs->tab_id==33){

              }elseif($functabs->tab_id==34){

              }else{
                echo $this->render(''.$functiontab->tabname.'');
              }

              }
            }
          }
              
  else{


  echo $this->render('_projects');
  
  if($user->account_type != 'perfm_pad_reporting_only'){ 
    //echo $this->render('dashboard');
    //echo $this->render('_boq');
    echo $this->render('_workgroup');
    //echo $this->render('_activity');
    echo $this->render('_estimate');
  }
  echo $this->render('_schedulelisting');
  //echo $this->render('_scheduleactivity');
  //echo $this->render('_schedulerelations');
  //echo $this->render('_resources');
  //echo $this->render('_estimateallocation');
  //echo $this->render('_scheduleassignresources');
  echo $this->render('_schedulereport');

  }
  
?>

<style>
    .tab-content{
        max-height:unset;
    }
    .resource_usage_container{
      display: none;
    }
    .resource_usage_nav_bar .nav-tabs > li {
        float:none;
        display:inline-block;
        zoom:1;
    }

    .resource_usage_nav_bar .nav-tabs {
        text-align:center;
    }

</style>

<link rel="stylesheet" href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/mobiscroll.jquery.min.css">

<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
    <!-- Mobiscroll JS and CSS Includes -->
<link rel="stylesheet" href="<?php echo Yii::$app->request->baseUrl; ?>/cssnew/mobiscroll.jquery.min.css">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/mobiscroll.jquery.min.js"></script>

<script>
    var $j = jQuery.noConflict(true);
    window.isMbscDemo = false;
</script>


<script type="text/javascript">

  $(document).on( "click", ".navbar-nav .icon-dashboard", function(){
      if($(".icon-dashboard").hasClass("active")) {
        $('#project-title-head').html('Project Dashboard');
      }
      else{
        $('#project-title-head').html('Projects');
      }
  });


  $( document ).ready(function() {

    var value = 0;
    $('.panel-default').removeClass('acco-one acco-two acco-three acco-four acco-five acco-six acco-seven acco-eight acco-nine acco-ten acco-eleven acco-twelve acco-thirteen');


    $('.panel-default').each(function() {

      value ++;
      var wordss = {
      1:'one',
      2:'two',
      3:'three',
      4:'four',
      5:'five',
      6:'',
      7:'one',
      8:'two',
      9:'three',
      10:'four',


      };
      $(this).addClass('acco-'+wordss[value]);

    });

  });
</script>
