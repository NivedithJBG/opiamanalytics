
<div class="panel panel-default accountgroups-tab tab acco-two">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/masters/accountgroups.js" type="text/javascript"></script>
    <!-- <input type="radio" id="rd5" class="acc-groups" name="rd"> -->
                
    <div class="panel-heading" >
      <h4 class="panel-title acc_trigger"  id="acc-groups">
       <a data-toggle="collapse" data-parent="#accordionfinmaster" href="#collapseaccntgrp">
        <span class="icon-calculator4 acc_trigger"></span>Account Groups</a>
      </h4>
    </div>         
    <div id="collapseaccntgrp" class="tab-content cOrder-body panel-collapse collapse">
        <div class="panel-body ">                
            <div class="search-and-content-wrpr">
                <div class="search-and-actions-wrpr row">
                    <div class="content-search-wrpr col-md-3 col-sm-3" id="searchdiv">
                        <input type="text" placeholder="Search" id="searchaccountgroups" class="form-control">

                          <button id="acntgrpssearch" class="btn btn-primary" type="button" ><span class="icon-search5"></span></button>
                    </div>
                    <div class="col-md-7 col-sm-7"></div>
                    <div class="content-action-wrpr col-md-2 col-sm-2">
                        <a href="#" class="btn btn-primary addForm" title="Add Account group"><span class="icon-add"></span> Add</a>

                        <a href="#" class="btn btn-primary list-accountType" id="listaccountgroups"><span class="icon-th-list"></span> List</a>

                    </div>
                </div>
                <div class="content-wrpr">
                    <!-- form starts here -->
                    <div class="account-group-add-cntnt-wrpr row" id="acntgrpsaddsection">
                        <form id="acntgrpsform">
                            <div class="col-md-12">
                                <div class="form-title">Add Account Group</div>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-5">                                  
                                <div class="add-new-form-wrpr">                                        
                                    <div class="form-group">
                                        <label>Account Group Name</label>
                                        <input class="form-control" type="text" id="accountgroupsname" name="accountgroupsname" placeholder="Account Groups Name"><span class="error" style="display: none;"></span>

                                    </div>
                                </div>
                            </div>                               
                            <div class="col-md-5">
                                <div class="text-center">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger cancel" ><span class="icon-close"></span> Cancel</button>
                                    
                                    <button type="button" class="btn btn-primary" id="saveacntgrps"><span class="icon-check"></span> Add Account Group</button>
                                    
                                </div>
                            </div>
                            <div class="col-md-1"></div>
                        </form>
                    </div>
                    <!-- form ends here -->                                                       
                    <div class="preloader" style="display: none;"><center><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"></center></div>
                    <div id="acntgrpsitems"></div>
                                                      
                </div>
            </div>                 
        </div>
    </div>
</div>
              














