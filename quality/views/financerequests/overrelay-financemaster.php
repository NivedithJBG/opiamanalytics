<?php
use app\models\Accountsmaster;  
use app\models\Resourcetype; 
use app\models\AccountsSub; 
use app\models\AccountTypes;  
?>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/over_menu_financemaster.js" type="text/javascript"></script>
<div class="container-fluid procu-accordion">
    <div class="row">
        
        <div class="finmenu-popup-cntnr">

            <div class="finmenu-cntnt-wrpr">
                <div class="icon-groups type"> 
                    <!-- <a href="#" title="Close" class="btn btn-primary text-button menu1-win-close">&#10006; Close</a> -->
                </div>
                <div class="row show-grid"> </div>
                <div style="text-align: center;"><b><h4>Finance Master</h4></b></div>
                <div class="col-md-12">
                    <div class="panel-group acco-one-active" >

                    <!-- tab 1  -->

                    
                        <div class="panel panel-default acco-one accounttype-tab tab tab-wrapper">
                            <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/masters/accounttypes.js" type="text/javascript"></script>
                            <input type="radio" id="rd3" class="acc-types" name="rd">
                                    
                            <div class="panel-heading" >
                            <h4 class="panel-title acc_trigger" id="Accounttypes">
                                <a  href="javascript:void(0)">
                                <span class="icon-calculator4 acc_trigger"></span>Account Type</a>
                            </h4>
                            </div>
                                            
                            <div  class="tab-content cOrder-body panel-collapse ">
                                <div class="panel-body ">
                                    <div class="search-and-content-wrpr">
                                        <div class="search-and-actions-wrpr row">
                                            <div class="content-search-wrpr col-md-3 col-sm-3" id="searchdiv">
                                                <input type="text" placeholder="Search" id="searchaccounttypes" class="form-control">
                                                <button id="accounttypessearch" class="btn btn-primary" type="button">
                                                    <span class="icon-search5"></span>
                                                </button>
                                            </div>
                                            <div class="col-md-7 col-sm-7"></div>
                                            <div class="content-action-wrpr col-md-2 col-sm-2">
                                                <a href="#" class="btn btn-primary addForm"><span class="icon-add"></span> Add</a>
                                                <a href="#" class="btn btn-primary list-accountType"  id="listaccounttypes"><span class="icon-th-list"></span> List</a>
                                            </div>
                                        </div>
                                        <div class="content-wrpr">
                                            <!-- form starts here -->
                                            <div class="account-type-add-cntnt-wrpr row" id="accounttypesaddsection">
                                                <form id="accounttypesform">
                                                    <div class="col-md-12">
                                                        <div class="form-title">Add Account Type</div>
                                                    </div>
                                                    <div class="col-md-1"></div>
                                                    <div class="col-md-5">                                   
                                                        <div class="add-new-form-wrpr">
                                                            <div class="form-group">
                                                                <label>Account Type Name</label>
                                                                <input class="form-control" id="accounttypesname" name="accounttypesname" placeholder="Enter account type name" type="text" />
                                                                <span class="error" style="display: none;"></span>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    
                                                    <div class="col-md-5">   
                                                        <div class="text-center">
                                                            <label>&nbsp;</label>
                                                            <button type="button" class="btn btn-danger cancel" ><span class="icon-close"></span> Cancel</button>

                                                            <button type="button" class="btn btn-primary" id="saveaccounttypes"><span class="icon-check"></span>Add Account Type</button>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1"></div>
                                                </form>
                                            </div>
                                            <!-- form ends here -->

                                            <div class="preloader" style="display: none;"><center><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"></center></div>
                                            <div id="accounttypesitems"></div>                         
                                        </div>
                                    </div>                 
                                </div>
                            </div>
                        </div>

                    <!-- tab 2  -->
                    
                        <div class="panel panel-default accountgroups-tab tab acco-two">
                            <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/masters/accountgroups.js" type="text/javascript"></script>
                            <input type="radio" id="rd5" class="acc-groups" name="rd">
                                        
                            <div class="panel-heading" >
                            <h4 class="panel-title acc_trigger"  id="Accountgroups">
                            <a  href="javascript:void(0)">
                                <span class="icon-calculator4 acc_trigger"></span>Account Groups</a>
                            </h4>
                            </div>         
                            <div  class="tab-content cOrder-body panel-collapse ">
                                <div class="panel-body ">                
                                    <div class="search-and-content-wrpr">
                                        <div class="search-and-actions-wrpr row">
                                            <div class="content-search-wrpr col-md-3 col-sm-3" id="searchdiv">
                                                <input type="text" placeholder="Search" id="searchaccountgroups" class="form-control">

                                                <button id="acntgrpssearch" class="btn btn-primary" type="button" ><span class="icon-search5"></span></button>
                                            </div>
                                            <div class="col-md-7 col-sm-7"></div>
                                            <div class="content-action-wrpr col-md-2 col-sm-2">
                                                <a href="#" class="btn btn-primary addForm"><span class="icon-add"></span> Add</a>

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

                        <!-- tab 3  -->

                        <div class="panel panel-default accountsubgroup-tab tab acco-three">
                            <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/masters/accountsubgroups.js" type="text/javascript"></script>
                            <input type="radio" id="rd5" class="acc-subgrps" name="rd">
                            
                            <div class="panel-heading" >
                            <h4 class="panel-title acc_trigger" id="Accountsubgroups">
                                <a  href="javascript:void(0)">
                                <span class="icon-calculator4 acc_trigger"></span>Account Sub Group</a>
                            </h4>
                            </div>

                            <div  class="tab-content cOrder-body panel-collapse">
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
                                                <button type="button" class="btn btn-success"  id="addacntsubgrps" value=""><span class="glyphicon glyphicon-plus-sign"></span>Add</button>
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
                                        
                                </div>
                            </div>
                        </div>


                        <!-- tab 4  -->

                        <div class="panel panel-default accountheads-tab tab acco-four">
                            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

                            <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/masters/accounts.js" type="text/javascript"></script>
                            <input type="radio" id="rd5" class="acc-heads" name="rd">
                                            
                            <div class="panel-heading">
                                <h4 class="panel-title acc_trigger" id="Accounts">
                                <a  href="javascript:void(0)">
                                <span class="icon-calculator4 acc_trigger"></span>Account Heads</a>
                                </h4>
                            </div>
                                            
                            <div  class="tab-content cOrder-body panel-collapse ">
                                <div class="panel-body ">                  
                                <div class="search-and-content-wrpr" id="account_heads_listing">
                                    <div class="search-and-actions-wrpr row">
                                    <div class="content-search-wrpr col-md-8 col-sm-8" id="searchdiv">
                                        <select id="accountsubgrp" class="form-control">
                                        <option value="none" class="form-control">Select Account Subgroup</option>
                                        <?php
                                            $acntsubgrp=AccountsSub::find()->where(['Status'=>0])->all();
                                            foreach($acntsubgrp AS $subgrp):
                                                echo "<option value='".$subgrp->id."' id='acntsubgrp'>".$subgrp->name."</option>";
                                            endforeach;
                                        ?>
                                        </select>
                                        <select id="accounttype" class="form-control">
                                        <option value="none" class="form-control">Select Account Type</option>
                                        <?php
                                            $acnttypes=AccountTypes::find()->where(['Status'=>0])->all();
                                            foreach($acnttypes AS $acnttype):
                                                echo "<option value='".$acnttype->type_id."' id='acnttype'>".$acnttype->name."</option>";
                                            endforeach;
                                        ?>
                                        </select>
                                        <input type="text" placeholder="Search" id="searchaccounts" class="form-control">
                                        <button id="accountsearch" class="btn btn-primary" type="button" ><span class="icon-search5"></span></button>
                                    </div>
                                    <div class="col-md-2 col-sm-2"></div>
                                    <div class="content-action-wrpr col-md-2 col-sm-2">
                                        <a href="#" class="btn btn-primary addForm" id="addaccount"><span class="icon-add"></span> Add</a>
                                        <button type="button" style="display: none;" class="btn btn-danger list-accountType" id="listaccounts"><span class="glyphicon glyphicon-list-alt"></span>List Accounts</button>
                                    </div>
                                    </div>
                                    <div class="content-wrpr">
                                    <!-- form starts here -->
                                    <form id="accountsform"> 
                                        <div class="account-heads-add-cntnt-wrpr row">
                                        <div class="col-md-12">
                                            <div class="form-title">Add Account Head</div>
                                        </div> 
                                        <div class="col-md-3">
                                            <div class="form-group">
                                            <label>Account Type</label>
                                            <select class="form-control" id="accounttypes" name="accounttype">
                                                <option value="0">Select Account type</option>
                                                <?php
                                                $acnttypes=AccountTypes::find()->Where(['NOT LIKE', 'name', 'Expense'])->andWhere(['Status'=>0])->all();
                                                foreach($acnttypes AS $acnttype):
                                                    echo "<option value='".$acnttype->type_id."' id='acnttype'>".$acnttype->name."</option>";
                                                endforeach;
                                            ?>
                                            </select>
                                            <span class="error" style="display: none;"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                            <label>Account Name</label>
                                            <input class="form-control" id="accountsname"  name="accountsname" placeholder="Account Name" type="text" />
                                            <input type="hidden" name="vendorid" value="">
                                            <span class="error" style="display: none;"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                <label>TDS(%)</label>
                                                <input class="form-control" id="accounttds" name="accounttds" placeholder="TDS(%)" type="text" />
                                                <span class="error" style="display: none;"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                <label>GST(%)</label>
                                                <input class="form-control"  id="accountservtax" name="accountservtax" placeholder="TDS(%)" type="text" />
                                                <span class="error" style="display: none;"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                <label class="text-center">Schedule</label>
                                                <input style="height:20px;visibility: visible;box-shadow: none;" class="form-control"  id="schedule" name="schedule"  type="checkbox" />

                                                <span class="error" style="display: none;"></span>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1"></div>
                                        <div class="col-md-1"></div>
                                        <div class="col-md-12">
                                            <hr class="customHr" />
                                            <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th><span class="headings">Account Groups</span></th>
                                                    <th><span class="headings">Account Sub-Groups</span></th>
                                                    <!--<th><span class="headings">&nbsp;</span></th>
                                                    <th><span class="headings">&nbsp;</span></th>-->
                                                </tr>                                            
                                                <?php 
                                                $groups=Accountsmaster::find()->Where(['NOT LIKE', 'name', 'Project Expenditure'])->andWhere(['NOT LIKE', 'name', 'Corporate Expenditure'])->andWhere(['Status'=>0])->all();
                                                foreach($groups AS $group): 
                                                    if($group->id==1){
                                                ?>      

                                                <tr>
                                                <td>
                                                    <input type="checkbox" class="form-control accountgroup selectcheckbox"  style="visibility: visible;" data-id="<?php echo $group->id;?>" name="accountgroup[]" value="<?php echo $group->id;?>"  /> <?php echo $group->name;?>                                                         
                                                </td>
                                                <td>
                                                    <select id="accountsubgrps<?php echo $group->id;?>" data-id="<?php echo $group->id;?>" name="accountsubgrps<?php echo $group->id;?>" class="form-control accountsubgrps">
                                                    <option value="">Select Account Sub-Groups</option>                                   
                                                    </select> 
                                                </td>
                                                <!--<td >
                                                </td>-->
                                                </tr>
                                                <?php } elseif($group->id==9) {?>
                                                <tr>
                                                <td>
                                                    <input type="checkbox" class="accountgroup selectcheckbox1"  style="visibility: visible;"  data-id="<?php echo $group->id;?>" name="accountgroup[]" value="<?php echo $group->id;?>"  /> <?php echo $group->name;?>                                                     
                                                </td>
                                                <td>
                                                    <select id="accountsubgrps<?php echo $group->id;?>" data-id="<?php echo $group->id;?>" name="accountsubgrps<?php echo $group->id;?>" class="form-control accountsubgrps">
                                                    <option value="">Select Account Sub-Groups</option>
                                                    </select>                              
                                                </td>
                                                <!--<td></td>                                              
                                                <td>
                                                    <select  class="form-control bsitems" style="display: none">
                                                        <option value="">Select BS Item</option>
                                                    </select>
                                                </td>-->
                                                </tr>
                                                <?php } else {?>
                                                <tr>
                                                <td>
                                                    <input type="checkbox" class="form-control accountgroup" style="visibility: visible;"  data-id="<?php echo $group->id;?>" name="accountgroup[]" value="<?php echo $group->id;?>"  /> <?php echo $group->name;?>
                                                </td>
                                                <td>
                                                    <select id="accountsubgrps<?php echo $group->id;?>" data-id="<?php echo $group->id;?>" name="accountsubgrps<?php echo $group->id;?>" class="form-control accountsubgrps" >
                                                    <option value="">Select Account Sub-Groups</option>
                                                    </select>
                                                </td>
                                                <!--<td></td>
                                                <td>
                                                    <select class="form-control bsitems" style=" display: none;">
                                                    <option value="0">Select BS Item</option>
                                                    </select>
                                                </td>-->
                                                </tr>
                                                <?php } endforeach;?>                                                
                                            </tbody>                                    
                                            </table>
                                        </div>
                                        <div class="col-md-1"></div>
                                        <div class="col-md-12 buttonCstmPos">
                                            <div class="text-center">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-danger cancel" ><span class="icon-close"></span> Cancel</button>
                                            <button type="button" class="btn btn-primary" id="saveaccounts"><span class="icon-check"></span> Add Account Head</button>
                                            </div>
                                        </div>
                                        </div>
                                    </form>
                                    <!-- form ends here -->
                                    <!-- edit form starts here -->
                                    <form id="accountseditform">
                                        <div class="account-heads-edit-cntnt-wrpr row">
                                            <div class="col-md-12">
                                                <div class="form-title">Edit Account Head</div>
                                            </div>              
                                            <div id="editaccountheads"></div>
                                        </div>
                                    </form>
                                    <!-- edit form ends here -->
                                    <!-- list start here -->
                                    <div class="preloader" style="display: none;"><center><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"></center></div>
                                    <div id="accountsitems"></div>                    
                                    <!-- list end here -->   
                                    </div>
                                </div>
                                </div>
                            </div>

                            <script>
                                $(document).on('change', '.accountgroup', function() {  
                                    var accountgroup=$(this).val();        
                                    if($(this). prop("checked") == true){            
                                        $.ajax({
                                            type: 'POST',
                                            url: '../accountschedule/getsubgroups',                        
                                            data: {accountgroup:accountgroup},
                                            success: function(data){
                                                $('#accountsubgrps'+accountgroup).html(data);
                                            }
                                        });
                                        if (accountgroup==6){
                                            var databsitem='<option value="0">Select BS Item</option>';
                                            $('#bsitems'+accountgroup).html(databsitem);
                                            $('#bsitems'+accountgroup).show();
                                        }

                                    }
                                    else
                                    {        
                                        var data='<option value="0">Select Account Sub-Groups</option>';
                                        var datasched='<option value="0">Select Account Schedule</option>';
                                        $('#accountsubgrps'+accountgroup).html(data);
                                        $('#accountschedule'+accountgroup).html(datasched);
                                        $('#bsitems'+accountgroup).hide();

                                    }                                      
                                });        
                                
                                $(document).on('change', '.accountsubgrps', function() {
                                var accountgroup=$(this).data('id');
                                var accountsubgroup=$(this).val();        
                                if(accountsubgroup !=''){            
                                    $.ajax({
                                        type: 'POST',
                                        url: '../accountschedule/getschedules',                        
                                        data: {accountgroup:accountgroup,accountsubgroup:accountsubgroup},
                                        success: function(data){
                                            $('#accountschedule'+accountgroup).html(data);                
                                        }
                                    });  
                                    
                                    if(accountgroup==1){
                                        $.ajax({

                                            type: 'POST',

                                            url: '../accountsitem/resourcetype',

                                            dataType:"json",

                                            data:{accountsubgrp:accountsubgroup},

                                            success: function(data){

                                                if(data.error=='No')
                                                {
                                                    $('#resourcetype').html(data.result);

                                                }
                                                else
                                                {
                                                    alert(data.error);
                                                }

                                            }

                                        });
                                    }
                                    else if(accountgroup==9){
                                        $.ajax({

                                            type: 'POST',

                                            url: '../accountsitem/resourcetype',

                                            dataType:"json",

                                            data:{accountsubgrp:accountsubgroup},

                                            success: function(data){

                                                if(data.error=='No')
                                                {
                                                    $('#resourcetype_cor').html(data.result);

                                                }
                                                else
                                                {
                                                    alert(data.error);
                                                }

                                            }

                                        });
                                    }
                                }
                                else
                                {                    
                                    var datasched='<option value="0">Select Account Schedule</option>';               
                                    $('#accountschedule'+accountgroup).html(datasched);  
                                }        
                                            
                                });
                                
                                $(document).on('change', '.accountsubgrps', function() {  
                                var accountgroup=$(this).data('id');
                                var accountsubgroup=$(this).val();
                                if(accountsubgroup !=''){
                                    $.ajax({
                                        type: 'POST',
                                        url: '../accountssub/getbsitems',
                                        data: {accountsubgroup:accountsubgroup},
                                        success: function(data){
                                            $('#bsitems'+accountgroup).html(data);
                                        }
                                    });
                                }
                                else
                                {
                                    var datasched='<option value="0">Select BS Item</option>';
                                    $('#bsitems'+accountgroup).html(datasched);
                                }

                                });
                                
                                $(document).on('change', '#accounttype', function() { 
                                var type=$(this).val();
                                if(type==8)
                                {
                                    $('.selectcheckbox').prop('checked', true).trigger("change");
                                    $('#resource_type_list').show();
                                    $('#resource_list').show();
                                }
                                else
                                {
                                    $('.selectcheckbox').prop('checked', false).trigger("change");
                                    $('#resourcetype').val(0);
                                    $('.ms-close-btn').each(function(){
                                        $('.ms-close-btn').trigger("click");
                                    });
                                    $('#resourceunitrow').hide();
                                    $('#account_subgrp_list').hide();
                                    $('#resource_type_list').hide();
                                    $('#resource_list').hide();
                                }
                                });

                                $('#saveaccounts').click(function(){
                                $('#save_create').val('');
                                var error=0;
                                $('.error').hide();
                                var str = $('#accountsname').val();
                                if(str=='')
                                {
                                    $("#accountsname").next("span").html('Enter Account Name').show('slow');
                                    error=1;
                                }
                                if($('#accounttype').val()==0){
                                    $("#accounttype").next("span").html('Select Account type').show('slow');
                                    error=1;
                                }
                                if($('#accounttype').val()==8){
                                    if($('.selectcheckbox').prop('checked')==true){
                                        if($('#resourcetype').val()==0){
                                            $("#resourcetype").next("span").html('Select Resource Group').show('slow');
                                            error=1;
                                        }
                                    }

                                    if($('.selectcheckbox1').prop('checked')==true){
                                        if($('#resourcetype_cor').val()==0){
                                            $("#resourcetype_corinfo").html('Select Resource Group').show('slow');
                                            error=1;
                                        }
                                    }
                                }

                                if (error==0){
                                    return true;
                                }
                                else {
                                    return false;
                                }
                                });
                                
                                $('#save-create').click(function(){
                                var error=0;
                                $('#save_create').val('mode');
                                $('.error').hide();
                                var str = $('#accountsname').val();
                                if(str=='')
                                {
                                    $("#accountsname").next("span").html('Enter Account Name').show('slow');
                                    error=1;
                                }
                                if($('#accounttype').val()==0){
                                    $("#accounttype").next("span").html('Select Account type').show('slow');
                                    error=1;
                                }
                                if($('#accounttype').val()==8){
                                    if($('#resourcetype').val()==0){
                                        $("#resourcetype").next("span").html('Select Resource Group').show('slow');
                                        error=1;
                                    }
                                }

                                if (error==0){
                                    return true;
                                }
                                else {
                                    return false;
                                }
                                });

                            </script>


                            </div>


                        <!-- tabs end  -->

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
