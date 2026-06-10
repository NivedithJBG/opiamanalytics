<?php 
use amnah\yii2\user\models\User;
use app\models\UserProjects;
use app\models\Projects;
use app\models\AccountsItem;
use app\models\CashbankuserSelection;

$uid= Yii::$app->user->Id;
$cashaccntuser=CashbankuserSelection::find()->where(['userid'=>$uid])->andWhere(['account_typeid'=>2])->one();

?>

<?php if($cashaccntuser){ ?>
<div class="panel panel-default bank-book-tab acco-six tab">
<?php } else { ?>
<div class="panel panel-default bank-book-tab acco-six tab" style="pointer-events:none;">
<?php } ?>

    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/bankbook.js" type="text/javascript"></script>
  <!-- <input type="radio" id="rd5" name="rd"> -->
    
    <div class="panel-heading" >
      <h4 class="panel-title" id="bank_book">
        <a data-toggle="collapse" data-parent="#accordionfin" href="#collapsebank">
        <span class="icon-library"></span>Bank Book</a>
      </h4>
    </div>
    
    
    <div id="collapsebank" class="tab-content panel-collapse cOrder-body collapse">
      <div class="panel-body ">
        
            <div class="search-and-content-wrpr">
                <div class="search-and-actions-wrpr row">
                    <div class="content-search-wrpr col-md-12 col-sm-12" id="cash_bookhead">
                        <!-- <select class="form-control" id="bankproject" name="bankproject">
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
                        </select>
                        <select class="form-control" id="bankbookproject" name="bankbookproject">
                            <option value="0">Select Project</option>
                            <?php
                                $project=Projects::find()->where(['Project_Delete_Status' => 0])->all();
                                foreach($project AS $list):
                                    echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                                endforeach;
                            ?>
                        </select> -->
                        <select class="form-control" id="bank" name="bank">
                            <option value="">Select Bank</option>
                            <?php
                            $banks=AccountsItem::find()->where(['account_type' => 2])->andWhere(['Status'=>0])->all();
                            foreach($banks AS $key=>$bank):
                                echo"<option value='".$bank->id."'>".$bank->name."</option>";
                            endforeach;
                            ?>
                        </select>
                        <input class="form-control" type="date" id="bankbookfromdate" name="fromdate" value="<?php echo date("Y-m-d");?>" /> 
                        <input class="form-control" type="date" id="bankbooktodate" name="todate" value="<?php echo date("Y-m-d");?>" />

                        <input id="bankbookaccounthead" class="form-control" list="bankbookaccountheadlist" name="accounthead" autocomplete="off" onchange="this.blur();" placeholder = "Select Account Head">

                        <datalist id="bankbookaccountheadlist">
                            <?php 
                                $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy([
                                                        'name' => SORT_ASC])->all();
                                foreach($acnts AS $accounts):
                                    echo "<option data-value='".$accounts->id."' value='".$accounts->name."'></option>";
                                endforeach;
                            ?> 
                        </datalist>
                        <button id="bankbooksearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                    </div>
                    <span class='error' style="float: left;font-size: 12px;margin-left: 25px;color: red;"></span>
                </div>
                <div class="content-wrpr">
                    <div class="current-bank-list" id="current-bank-list" style="    padding-left: 15px;padding-right: 15px;padding-top: 25px;"></div>
                    <div class="bank-book-list-wrpr" id="bankbookitems">  
                        
                        
                    </div>
                    
                </div>
                
            </div>
      
      </div>
    </div>
</div>