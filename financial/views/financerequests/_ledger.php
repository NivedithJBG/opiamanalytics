<?php 
use amnah\yii2\user\models\User;
use app\models\UserProjects;
use app\models\Projects;
use app\models\AccountsItem;

?>

<div class="panel panel-default ledger-tab acco-eight tab">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/ledger.js" type="text/javascript"></script>
    <!-- <input type="radio" id="rd5" name="rd"> -->
    
    <div class="panel-heading" >
      <h4 class="panel-title" id="ledgerlist">
        <a data-toggle="collapse" data-parent="#accordionfin" href="#collapseledger">
        <span class="icon-calculator1"></span>Ledger</a>
      </h4>
    </div>
     
    <div id="collapseledger" class="tab-content panel-collapse cOrder-body collapse">
        <div class="panel-body ">     
            <div class="search-and-content-wrpr">
                <div class="search-and-actions-wrpr srchndwrpr ledgerwrpr row">
                    <div class="content-search-wrpr col-md-12 col-sm-12" id="ledgerhead">
                        <!-- <select class="form-control" id="accounthead" name="accounthead">
                            <option value="0">Select Account Head</option>
                            <php 
                                $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy(['name' => SORT_ASC])->all();
                                foreach($acnts AS $accounts):
                                    echo "<option value='".$accounts->id."' id='acnts'>".$accounts->name."</option>";
                                endforeach;
                            ?>
                        </select> -->

                        <input id="accounthead" class="form-control" list="ledgeraccountheadlist" name="accounthead" autocomplete="off" onchange="this.blur();" placeholder = "Select Account Head">

                        <datalist id="ledgeraccountheadlist">
                            <?php 
                                $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy([
                                                        'name' => SORT_ASC])->all();
                                foreach($acnts AS $accounts):
                                    echo "<option data-value='".$accounts->id."' value='".$accounts->name."'></option>";
                                endforeach;
                            ?> 
                        </datalist>

                        <select class="form-control" id="ledgerplace" name="ledgerplace">
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
                        <select class="form-control" id="projectid" name="projectid">
                            <option value="0">Select Project</option>
                            <?php
                                $project=Projects::find()->where(['Project_Delete_Status' => 0])->all();
                                foreach($project AS $list):
                                    echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                                endforeach;
                            ?>
                        </select>
                        
                        <input class="form-control" type="date" id="ledgerfromdate" name="ledgerfromdate" value="" /> 
                        <input class="form-control" type="date" id="ledgertodate" name="ledgertodate" value="" />
                        
                        <button id="ledgersearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                    </div>
                    <!-- <div class="col-md-12" style="padding-top: 30px;">
                    <label style="text-align: center;font-size: 15px;">Ledger</label>
                    </div> -->
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <span class='error shwnpad' id="ledger_accnthead" style="float: left;font-size: 12px;color: red;">
                            </span>
                        </div>
                        <div class="col-md-2">
                            <span class='error' id="ledger_place" style="float: left;font-size: 12px;color: red;">
                            </span>
                        </div>
                        <div class="col-md-3">
                        </div>    
                        <div class="col-md-4">
                        </div>
                    </div>
                </div>
                <div class="content-wrpr">
                    
                    <div class="ledger-book-list-wrpr" id="ledgeritems">
                        
                        
                    </div>
                    
                </div>
                
            </div>
      
        </div>
    </div>
</div>
