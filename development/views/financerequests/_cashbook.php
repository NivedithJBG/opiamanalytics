<?php 
use amnah\yii2\user\models\User;
use app\models\UserProjects;
use app\models\Projects;
use app\models\AccountsItem;
use app\models\CashbankuserSelection;

$uid= Yii::$app->user->Id;
$cashaccntuser=CashbankuserSelection::find()->where(['userid'=>$uid])->andWhere(['account_typeid'=>1])->one();

?>

<?php if($cashaccntuser){ ?>
<div class="panel panel-default cash-book-tab acco-five tab">
<?php } else { ?>
<div class="panel panel-default cash-book-tab acco-five tab" style="pointer-events:none;">
<?php } ?>

    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/cashbook.js" type="text/javascript"></script>
  <!-- <input type="radio" id="rd5" name="rd"> -->
    
    <div class="panel-heading" >
      <h4 class="panel-title" id="cash-book">
        <a data-toggle="collapse" data-parent="#accordionfin" href="#collapsecash">
        <span class="icon-book1"></span>Cash Book</a>
      </h4>
    </div>
       
    <div id="collapsecash" class="tab-content panel-collapse cOrder-body collapse">
      <div class="panel-body ">
        
            <div class="search-and-content-wrpr">
                <div class="search-and-actions-wrpr row">
                    <div class="content-search-wrpr col-md-12 col-sm-12" id="bank_bookhead">
                        <!-- <select class="form-control" id="place" name="place">
                            <option value="0">Select Place</option>
                            <?php
                                $userid = Yii::$app->user->Id; 
                                $user = User::find()->where(['id'=>$userid])->one();
                                if($user->superuser==1)
                                {
                                    $project=Projects::find()->where(['Status' => 0])->all();
                                    foreach($project AS $list):
                                        echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                                    endforeach;
                                }
                                elseif($user->superuser==2)
                                {
                                    $project=Projects::find()->where(['Status' => 0])->all();
                                    foreach($project AS $list):
                                        echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                                    endforeach;
                                }
                                else{
                                    $userprojects=UserProjects::find()->where(['userid' => $userid])->all();
                                    foreach($userprojects AS $projects):
                                        $project=Projects::find()->where(['Project_Id' => $projects['projectid']])->all();
                                        foreach($project AS $list):
                                            echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                                        endforeach;
                                    endforeach;
                                }
                            ?>
                        </select> -->
                       <!--  <select class="form-control" id="bankbookproject" name="bankbookproject">
                            <option value="0">Select Project</option>
                            <?php
                                $project=Projects::find()->where(['Project_Delete_Status' => 0])->all();
                                foreach($project AS $list):
                                    echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                                endforeach;
                            ?>
                        </select> -->
                        <!--<input class="form-control" type="date" id="fromdate" name="fromdate" value="<php echo date("Y-m-d");?>" /> 
                        <input class="form-control" type="date" id="todate" name="todate" value="<php echo date("Y-m-d");?>"/>-->
                        <input class="form-control" type="date" id="fromdate" name="fromdate" value="<?php echo date("Y-m-d");?>" /> 
                        <input class="form-control" type="date" id="todate" name="todate" value="<?php echo date("Y-m-d");?>"/>

                        <input id="cashbookaccounthead" class="form-control" list="cashbookaccountheadlist" name="accounthead" autocomplete="off" onchange="this.blur();" placeholder = "Select Account Head">

                        <datalist id="cashbookaccountheadlist">
                            <?php 
                                $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy([
                                                        'name' => SORT_ASC])->all();
                                foreach($acnts AS $accounts):
                                    echo "<option data-value='".$accounts->id."' value='".$accounts->name."'></option>";
                                endforeach;
                            ?> 
                        </datalist>

                        <button id="cashbooksearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                    </div>
                    <span class='error' style="float: left;font-size: 12px;margin-left: 25px;color: red;"></span>
                </div>
                <div class="content-wrpr">
                    
                    <div class="curr-cash-booklist" id="curr-cash-booklist" style="    padding-left: 15px;padding-right: 15px;padding-top: 25px;">
                        
                    </div>
                    <div class="cash-book-list-wrpr" id="cashbookitems">
                                               
                        
                    </div>
                    
                </div>
                
            </div>
      
      </div>
    </div>
</div>