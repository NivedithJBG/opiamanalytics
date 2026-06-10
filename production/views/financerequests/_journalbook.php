<?php 
use amnah\yii2\user\models\User;
use app\models\UserProjects;
use app\models\Projects;
use app\models\AccountsItem;

?>

<div class="panel panel-default journal-book-tab acco-seven tab">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/journalbook.js" type="text/javascript"></script>
    <!-- <input type="radio" id="rd5" name="rd"> -->

    <div class="panel-heading" >
      <h4 class="panel-title" id="journal-book">
        <a data-toggle="collapse" data-parent="#accordionfin" href="#collapsejourn">
        <span class="icon-book2"></span>Journal Book</a>
      </h4>
    </div>

    <div id="collapsejourn" class="tab-content panel-collapse cOrder-body collapse">
        <div class="panel-body ">   
            <div class="search-and-content-wrpr">
                <div class="search-and-actions-wrpr row">
                    <div class="content-search-wrpr col-md-12 col-sm-12" id="journal_bookhead">
                        <select class="form-control" id="journalproject" name="journalproject">
                            <option value="0">Select Project</option>
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
                        <input class="form-control" type="date" id="journalbookfromdate" name="fromdate" value="<?php echo date("Y-m-d");?>" /> 
                        <input class="form-control" type="date" id="journalbooktodate" name="todate" value="<?php echo date("Y-m-d");?>"/>
                        <!-- <select class="form-control" id="journalaccounthead" name="accounthead">
                            <option value="">Select Account Head</option>
                            <php 
                                $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy([
                                                        'name' => SORT_ASC])->all();
                                foreach($acnts AS $accounts):
                                    echo "<option value='".$accounts->id."' id='acnts'>".$accounts->name."</option>";
                                endforeach;
                            ?> 
                        </select> -->

                        <input id="journalaccounthead" class="form-control" list="journalaccountheadlist" name="accounthead" autocomplete="off" onchange="this.blur();" placeholder = "Select Account Head">

                        <datalist id="journalaccountheadlist">
                            <?php 
                                $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy([
                                                        'name' => SORT_ASC])->all();
                                foreach($acnts AS $accounts):
                                    echo "<option data-value='".$accounts->id."' value='".$accounts->name."'></option>";
                                endforeach;
                            ?> 
                        </datalist>
                        <button id="journalbooksearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                    </div>
                    
                    <span class='error' style="float: left;font-size: 12px;margin-left: 25px;color: red;"></span>
                </div>
                <div class="content-wrpr"> 
                <div class="curr-journalbook-list" id="curr-journalbook-list" style="padding-left: 15px;
    padding-right: 15px;"></div>             
                    <div class="journal-book-list-wrpr" id="journalbookitems">

                                           
                    </div>                
                </div>            
            </div>  
        </div>
    </div>
</div>
