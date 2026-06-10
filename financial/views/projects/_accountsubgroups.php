
<?php
use app\models\Accountsmaster;  
use app\models\Resourcetype; 
?>

 <div class="panel panel-default accountsubgroup-tab tab acco-three">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/masters/accountsubgroups.js" type="text/javascript"></script>
   <!--  <input type="radio" id="rd5" class="acc-subgrps" name="rd"> -->
    
    <div class="panel-heading" >
      <h4 class="panel-title acc_trigger" id="acc-subgrps">
        <a data-toggle="collapse" data-parent="#accordionfinmaster" href="#collapseaccntsubgrp">
        <span class="icon-calculator4 acc_trigger"></span>Account Sub Group</a>
      </h4>
    </div>

    <div id="collapseaccntsubgrp" class="tab-content cOrder-body panel-collapse collapse">
        <div class="panel-body ">                  
            <div class="search-and-content-wrpr" id="account_sub_group_listing">
                <div class="search-and-actions-wrpr row">
                   <select id="searchacntgrp" class="form-control" style="display: none;">
                        <option value="none">Select Account Group</option>
                        <?php
                       // $acntgrp=Accountsmaster::model()->findAll();
                        $acntgrp=Accountsmaster::find()->where(['Status'=>0])->all();
                        foreach($acntgrp AS $list):
                            echo "<option value='".$list->id."'>".$list->name."</option>";
                        endforeach;
                        ?>
                    </select>
                    <div class="content-search-wrpr col-md-4 col-sm-4" id="searchdiv">
                        <input type="text" placeholder="Search" id="searchacntsubgrps" class="form-control">
                        <button id="acntsubgrpssearch" class="btn btn-primary" type="button" ><span class="icon-search5"></span></button>
                    </div>
                    <div class="col-md-6 col-sm-6"></div>
                    <div class="content-action-wrpr col-md-2 col-sm-2">
                        <button type="button" class="btn btn-success"  id="addacntsubgrps" value="" title="Add Account subgroup"><span class="glyphicon glyphicon-plus-sign"></span>Add</button>
                        <a href="#" class="btn btn-primary list-accountType" id="listacntsubgrps" style="display: none;"><span class="icon-th-list"></span> List</a>
                    </div>
                </div>
                <div class="content-wrpr">                       
                    <!-- form starts here -->
                    <div class="account-subgroup-add-cntnt-wrpr row"  id="acntsubgrpsaddsection">
                        <form id="acntsubgrpsform">
                            <div class="col-md-12">
                                <div class="form-title">Add Account Sub Group</div>
                            </div>
                            <div class="col-md-1"></div>                             
                            <div class="col-md-3">                               
                                <div class="form-group">
                                    <label>Account Group</label>
                                    <select class="form-control" id="addacntgrp" name="addacntgrp" disabled="disabled">
                                        <option value="none">Select Account Group</option>
                                        <?php
                                        //$acntgrp=Accountsmaster::model()->findAll();
                                        $acntgrp=Accountsmaster::find()->where(['Status'=>0])->all();
                                        foreach($acntgrp AS $list):
                                            echo "<option value='".$list->id."'>".$list->name."</option>";
                                        endforeach;
                                        ?>
                                    </select>
                                    <span class="error" style="display: none;"></span>
                                </div>                                     
                            </div>                               
                            <div class="col-md-4">                                   
                                <div class="add-new-form-wrpr">
                                    <div class="form-group">
                                        <label>Account Sub Group Name</label>
                                       <input class="form-control" type="text" id="accountsubgroupsname" name="accountsubgroupsname" placeholder="Account Subgroups Name"><span class="error" style="display: none;"></span>
                                    </div>      
                                </div>                                    
                            </div>
                            <div class="col-md-3 hiding" id="hiding">                                
                                <div class="add-new-form-wrpr">
                                    <div class="form-group">
                                        <label>Resource Type</label>
                                       <select class="form-control" id="addres_type" name="addres_type" >
                                            <option value="none">Select Resource Type</option>
                                            <?php
                                            $resourcetypes=Resourcetype::find()->where(['Status'=>0])->all();
                                            foreach($resourcetypes AS $resourcetype):
                                                echo "<option value='".$resourcetype->ResourceType_Id."'>".$resourcetype->Name."</option>";
                                            endforeach;
                                            ?>
                                        </select>
                                        <span class="error" style="display: none;"></span>    
                                    </div>                                   
                                </div>                                   
                            </div>           
                            <div class="col-md-1"></div>
                            <div class="col-md-12">
                            
                                <div class="text-center">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger cancel" id="cancelsubgrp"><span class="icon-close"></span> Cancel</button>
                                    <button type="button" class="btn btn-primary" id="savesubacntgrps"><span class="icon-check"></span> Add Account Sub Group</button>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- form ends here -->
                    <!-- edit form starts here -->
                    <form id="editaccsubgrps">
                        <div class="account-subgroup-edit-cntnt-wrpr row">

                            <div id="editaccountsubgroups"></div>

                        </div>
                    </form>
                    <!-- edit form ends here -->                     
                    <div id="acntsubgrpsdata"> </div>
                            
                    <div id="acntsubgrpsitems" class="ui-sortable"> </div>
                                                  
                </div>
            </div>

            <!-- Schedule items start -->

            <div id="bsitemslist"> </div>

            <div id="bsitemsaddsection" style="display:none">
                <form id="bsitemsform">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Schedule Item</label>
                                <input type="text" class="form-control" id="addbsname" placeholder="Schedule Item">
                                <span class="error" style="display: none;"></span>
                            </div>  
                        </div>
                        <div class="col-md-3 text-left">
                            <label></label>
                            <input type="hidden" id="accntsubgrpid" value="">
                            <button type="button" class="btn btn-danger cancel" id="cancelbscreate"><span class="icon-close"></span> Cancel</button>
                            <button type="button" class="btn btn-primary savebscreate" id=""><span class="icon-check"></span> Save </button>
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                </form>
            </div>

            <!-- Schedule items end -->

            <form id="schaccountseditform">
                <div class="account-heads-edit-cntnt-wrpr row">
                    <div class="col-md-12">
                        <div class="form-title">Edit Account Head</div>
                    </div>              
                    <div id="scheditaccountheads"></div>
                </div>
            </form>

            <!-- list start here -->
                <div id="scheduleaccountsitems"></div>                    
            <!-- list end here -->  
                  
        </div>
    </div>
</div>