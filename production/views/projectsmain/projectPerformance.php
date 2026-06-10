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
  

  
?>


<div style="padding:50px; padding-top:20px;">
  <!-- <div ><h3 style="padding-left:50px;">Project Performance</h3></div> -->
  <div class="content-wrpr project-fav-cards-cntnr project-performance" >

    <?php echo $datarows; ?>
 </div>

</div>


<style type="text/css">
  /*.fav-project-wrpr.card .card-body {
    background: #0797bf;
  }*/

  .project-performance .fav-project-wrpr .card-body {
      background-color: #4a4a4a !important;
      color: #ffffff !important;
  }

  .project-performance .fav-project-wrpr .card-body a {
      color: #ffffff !important;
  }

  .project-performance .card-footer  {
      background-color: #d6d6d6 !important;
  }

  .fav-project-wrpr.card .type label, .fav-project-wrpr.card .acc-sub-group label{
    font-size: 12px;
    font-weight: 500;
    min-width: 95px;
    margin: 0;
  }

  .project-performance  .project-spec{
    margin-bottom: 15px;
    display: flex;
    align-items: center;
  }

  .project-performance  .project-spec .project-spec-icon{
    font-size: 15px;
    margin-right: 10px;
  }
  .project-performance  .project-spec .project-spec-text{
    font-size: 12px;
  }

  .project-performance  .project-spec .project-spec-dates{
    overflow: hidden;
    max-width: 202px;
    white-space: nowrap;
  }

  .project-info{
    padding-top: 15px;
    padding-bottom: 0px;
    background: #d6d6d6;
    border-radius: 6px;
    display: inline-block;
    width: 100%;
  }

  .project-status-bar-container{
    padding: 0px;
    background-color: #f0f0f0;
    border-radius: 0 0 6px 6px;
  }

  .project-status{
    padding-top: 15px;  
    align-items: center;
    display: flex;
  }

  .project-status label{
      float: left;
      min-width: 90px;
      margin-bottom: 0;
      font-size: 13px !important;

  }

  .dashboard-link{
   /* padding-left: 40px;*/
  }

  .dashboard-link .btn{
    text-decoration: none;
    border-radius: 30px;
    padding: 5px 5px;
    /*display: flex;*/
    font-size: 13px;
    justify-content: center;
    align-items: center;
    background-color: #337ab7;
    border-color: #2e6da4;
    color: #fff;
  }

  .dashboard-link .icon-schedule{
    background-color: #337ab7;
    border-color: #2e6da4;
    color: #fff;
    font-size: 14px;

  }

  /*.dashboard-link .icon-dollar1 {
  .dashboard-link .icon-monetization_on{*/
  .dashboard-link .icon-attach_money{
    background-color: #fdb616;
    border-color: #e2a009;
    color: #fff;
    font-size: 16px;
    padding: 4px;
  }

  .dashboard-link .icon-compare_arrows{
    background-color: #08b7a7;
    border-color: #0aafa0;
    color: #fff;
    padding: 4px;
    font-size: 17px;
  }

  .dashboard-link .icon-graph{
    background-color: #7d7c7c;
    border-color: #747474;
    color: #fff;
    font-size: 12px;
    padding: 0.56em;  
}


  .project-status .status-bar{
    height: 14px;
    background-color: #ccc;
    /*min-width: 210px;*/
    min-width: 70px;
    width: 100%;
    float: left;
    border-radius: 16px;

    font-size: 10px;
    text-align: center;
    color: white;
    font-weight: 500;
  }
  .project-status .icon-warning3{
    color: white;
    margin-right: 5px;
    font-size: 10px;
  }
  .project-status .red-bar{
    background-color: #eb0f0f;
  }
  .project-status .yellow-bar{
    background-color: #feae00;
  }
  .project-status .green-bar{
    background-color: #5ed937;
  }


</style>