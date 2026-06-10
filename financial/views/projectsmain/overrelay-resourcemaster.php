<?php
use app\models\Vendors;  
use app\models\Resourcetype;  
use app\models\Brand;  
use app\models\AccountsItem;  
use app\models\AccountsSub;
use app\models\TermsCondtns;
?>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/over_menu_resourcemaster.js" type="text/javascript"></script>
<div class="container-fluid procu-accordion">
    <div class="row">
        
        <div class="menu-popup-cntnr">

            <div class="menu-cntnt-wrpr">
                <div class="icon-groups type"> 
                    <!-- <a href="#" title="Close" class="btn btn-primary text-button menu-win-close">&#10006; Close</a> -->
                </div>
                <div class="row show-grid"> </div>
                <div style="text-align: center;"><b><h4>Resource Master</h4></b></div>
                <div class="col-md-12">
                    <div class="panel-group acco-one-active" >

                    <!-- tab 1  -->

                    <div class="panel panel-default acco-one resourcetype-tab tab tab-wrapper">
                        <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/resourcetype.js" type="text/javascript"></script>

                        <input class="res-type-tab" type="radio" id="rd5" name="rd">
                        <div class="panel-heading" >
                            <h4 class="panel-title acc_trigger" id="Resourcetype">
                                <a  href="javascript:void(0)">
                                    <span class="image-icon acc_trigger"></span>Resource Types
                                </a>
                            </h4>
                        </div>

                        <div  class="tab-content cOrder-body panel-collapse ">
                            <div class="panel-body ">
                                <div class="search-and-content-wrpr">
                                    <div class="search-and-actions-wrpr row">
                                        <div class="content-search-wrpr col-md-3 col-sm-3">
                                            <input type="text" placeholder="Search" id="searchrestypename" class="form-control">
                                            <button id="resourcetypesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                                        </div>
                                        <div class="ordertype_labels col-md-7 col-sm-7">
                                            <label><span class="image-icon2"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/carlabel-icon.png" /></span>Purchase Order</label>
                                            <label><span class="image-icon2"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/workorderl-icon.png" /></span></span>Work Order</label>
                                            <label><span class="image-icon2"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/musterrolllabel-icon.png" /></span>Muster Roll</label>
                                            <label><span class="image-icon2"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/leaselabel-icon.png" /></span>Lease Order</label>
                                        </div>
                                        <div class="content-action-wrpr col-md-2 col-sm-2">
                                            <a href="#" class="btn btn-primary addForm" id="addrestype"><span class="icon-add"></span> Add</a>
                                            <a href="#" class="btn btn-primary list-resourceType" id="listrestype"><span class="icon-th-list"></span> List</a>
                                        </div>
                                    </div>
                                    <div class="content-wrpr">

                                        <div id="restypeaddsection">
                                            <!-- form starts here -->
                                            <form id="addrestypeform">                                
                                            <div class="resource-type-add-cntnt-wrpr row">
                                                <div class="col-md-12">
                                                    <div class="form-title">Add new Resource Type</div>
                                                </div>
                                                <div class="col-md-1"></div>
                                                <div class="col-md-5">
                                                <div class="add-new-form-wrpr">
                                                    <div class="form-group">
                                                        <label>Enter Resource Type</label>
                                                        <input class="form-control" id="restypename1" name="restypename" value="" placeholder="Enter resource type name" type="text" />
                                                        <span class="error" style="display: none;"></span>
                                                    </div>
                                                </div>                                    
                                                </div>
                                                <div class="col-md-5">
                                                <div class="add-new-form-wrpr">
                                                    <div class="form-group">
                                                        <label>Select order type</label>
                                                        <select name="projecttype" class="form-control" id="projecttype">
                                                            <option value="0">Select Order Type</option>
                                                            <option value="1">Purchase Order</option>
                                                            <option value="2">Work Order</option>
                                                            <option value="3">Muster Roll</option>
                                                            <option value="4">Lease Order</option>
                                                        </select>
                                                    </div>
                                                                        
                                                </div>
                                                <div class="text-center">
                                                    <label>&nbsp;</label>
                                                    <button type="button" class="btn btn-danger cancel" id="cancelrestype"><span class="icon-close"></span> Cancel</button>
                                                    <button type="button" class="btn btn-primary" id="saverestype"><span class="icon-check"></span> Add Resource Type</button>
                                                    
                                                </div>
                                                </div>
                                                <div class="col-md-1"></div>
                                            </div>
                                                
                                            </form>
                                        </div>
                                        
                                    
                                        <div class="resource-type-edit-cntnt-wrpr row">
                                            <div class="col-md-12">
                                                <div class="form-title">Edit Resource Type</div>
                                            </div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-5">                 
                                            <div class="add-new-form-wrpr">                   
                                                <div class="form-group">
                                                    <label>Enter Resource Type</label>
                                                    <input class="form-control" id="restypenames" name="restypenames" placeholder="Enter resource type name" type="text" />
                                                    <span class="error" style="display: none;"></span>
                                                </div>
                                            </div>                 
                                            </div>
                                            <div class="col-md-5">                 
                                                <div class="add-new-form-wrpr">
                                                    <div class="form-group">
                                                        <label>Select Order Type</label>
                                                        <select name="projecttype" class="form-control" id="projecttypes">
                                                            <option value="0">Select Order Type</option>
                                                            <option value="1">Purchase Order</option>
                                                            <option value="2">Work Order</option>
                                                            <option value="3">Muster Roll</option>
                                                            <option value="4">Lease Order</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <label>&nbsp;</label>
                                                    <button type="button" class="btn btn-danger cancel" id="cancelrestypes"><span class="icon-close"></span> Cancel</button>
                                                    <button type="button" class="btn btn-primary" id="saveresourcetypebutton"><span class="icon-check"></span> Edit Resource Type</button>
                                                    <input type="hidden" id="saveresourcetypeval" value="'.$data['id'].'">                   
                                                </div>
                                            </div>
                                            <div class="col-md-1"></div>
                                        </div>
                                
                                
                                        <div class="col-md-12 list-restypehead">
                                        <div class="resource-type-cntnt-wrpr row">
                                            <div class="col-md-10 col-sm-10">
                                                <div class="row">
                                                    <div class="col-md-1 col-sm-1 type" style="right: auto;margin-left: 14px;" >
                                                        <label>#</label><br/>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <span>&nbsp;</span> 
                                                            </div>
                                                
                                                        </div>
                                                    </div>

                                                    <div class="col-md-1 col-sm-1 type" style="left: auto;right: 30px;padding-left: 0px;padding-right: 0px;height: 55px;width: 80px;">
                                                    <label>Order Type</label><br/>
                                            
                                                </div>

                                                <div class="col-md-2 col-sm-2 type">
                                                    <label>Resource Type</label><br/>                         
                                                </div>
                                            
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-2 "> </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 list-content" style="max-height:400px; overflow-y:auto;">
                                        
                                    <div class="preloader" style="display: none;"><center><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"></center></div>
                                    <div id="restypelistsection"></div>

                                    </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>




                    <!-- tab 2  -->
                    
                    <div class="panel panel-default resources-tab tab acco-two">
                        <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/resourcefunctions.js" type="text/javascript"></script>
                        <script src="<?php echo Yii::$app->request->baseUrl; ?>/js/magicsuggest.js" type="text/javascript"></script>
                        <input type="radio" id="rd5" class="res-tab" name="rd">
                                
                        <div class="panel-heading" >
                            <h4 class="panel-title acc_trigger"  id="Resources">
                                <a  href="javascript:void(0)">
                                    <span class="image-icon acc_trigger"></span>Resources
                                </a>
                            </h4>
                        </div>
                                
                        <div  class="tab-content cOrder-body panel-collapse ">
                            <div class="panel-body">				  
                                <div id="resourceslistsections" style="overflow: hidden;/* display: none; */">
                                    <div class="search-and-content-wrpr">
                                        <div class="search-and-actions-wrpr row" style="padding-bottom:0px;">
                                            <div class="content-search-wrpr col-md-9 col-sm-9" id="resourcesearchdiv">
                                                <select class="form-control" id="searcselectvendor" >
                                                    <option value="none">Select Vendor</option>
                                                    <?php
                                                    $vendorlist=Vendors::find()->where(['Status'=>0])->all();
                                                    foreach($vendorlist AS $list):
                                                        echo "<option value='".$list->Vendor_Id."'>".$list->Name."</option>";
                                                    endforeach;
                                                    ?> 
                                                </select>

                                                <input type="text" placeholder="Location" id="searchlocation" class="form-control location">
                                                <select class="form-control" id="searchbrand">
                                                    <option value="none">Select Brand</option>
                                                    <?php
                                                        $brandlist=Brand::find()->where(['status'=>0])->all();
                                                        foreach($brandlist AS $list):
                                                            echo "<option value='".$list->id."'>".$list->name."</option>";
                                                        endforeach;
                                                    ?> 
                                                </select>	

                                                <input type="text" placeholder="Resource Search" id="searchresourcename" class="form-control">
                                                <button id="resourcesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                                            </div>

                                            <div class="col-md-3" id="restyperefresh" style="display: none">
                                                <select id="searcselectrestype" class="form-control">
                                                    <option value="none">Select Resource Type</option>
                                                    <?php
                                                        $typelist=Resourcetype::find()->where(['Status'=>0])->all();
                                                        foreach($typelist AS $list):
                                                            echo "<option value='".$list->ResourceType_Id."'>".$list->Name."</option>";
                                                        endforeach;
                                                    ?>
                                                </select>

                                            </div>

                                            <div class="col-md-3" style="display: none">
                                                <select id="searcselectgroup" class="form-control">
                                                    <option value="none">Select Resource Group</option>
                                                </select>

                                            </div>

                                            <div class="content-action-wrpr col-md-3 col-sm-3">

                                                <button type="button" class="btn btn-primary addForm" id="addresources"><span class="icon-add"></span> Add</button>

                                                <button type="button" class="btn btn-danger" id="listresources" style="display: none"><span class="glyphicon glyphicon-list-alt"></span>List Resources</button>

                                            </div>
                                            <div class="col-md-12 col-sm-12 typeGroup-indication ">
                                                <span class="type">Resource Type:</span><span id="procrestypename"></span>  
                                            </div>
                                                
                                        </div>

                                        <div class="content-wrpr">
                                            <div id="resourceeaddsections" style="overflow: hidden;/* display: none; */"> 
                                                <form id="addresourceform">
                                                    <!-- form starts here -->
                                                    <div class="resources-add-cntnt-wrpr row" style="padding-top:3px; padding-bottom:3px;">
                                                        <div class="col-md-1"></div>
                                                            <div class="col-md-10">
                                                                <div class="row">
                                                                    <div class="col-md-3" style="width: 180px;">
                                                                        <div class="form-title" style="display:block; margin-top:12px; font-size:17px; margin-bottom:0px; margin-top:10px; text-align:left;">Resource Type: 

                                                                        </div> 
                                                                    </div>

                                                                    <div class="col-md-4" style="display:block; margin-top:11px; font-size:17px; margin-bottom:0px; margin-top:9px; text-align:left;padding-right: 0px;width: 250px;">
                                                                        <select class="form-control" id="restyperes" name="resourcetype" >
                                                                            <option value="none">Select Resource Type</option>
                                                                            <?php

                                                                                $typelist=Resourcetype::find()->where(['Status'=>0])->andWhere(['Status' => 0])->all();

                                                                                foreach($typelist AS $list):  
                                                                                    echo "<option value='".$list->ResourceType_Id."'>".$list->Name."</option>";
                                                                                endforeach;
                                                                            ?>
                                                        
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-12">
                                                                        &nbsp;
                                                                    </div>
                                                                    <div class="col-md-4">
                                                            
                                                                        <div class="form-group">
                                                                            <label>Resource Name</label>
                                                                            <input class="form-control" type="text" id="resourcename" name="resourcename" placeholder="Resource Name">
                                                                            <span class="error" style="display: none;"></span>
                                                                        </div>
                                                                
                                                                
                                                                        <div class="form-group">
                                                                            <label>Vendor</label>
                                                                            <input class="form-control" list="vendorlist" id="vendor" name="vendor-choice" placeholder="Select Vendor"/>

                                                                            <datalist id="vendorlist">
                                                                                <?php

                                                                                $vendors=Vendors::find()->Where(['Status'=>0])->all();
                                                                                foreach($vendors AS $vendor):
                                                                                    echo "<option value='".$vendor->Name."'></option>";
                                                                                endforeach;
                                                                                ?>
                                                                            </datalist>

                                                                            <span class="error" style="display: none;"></span>

                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label>Email Address</label>
                                                                            <input class="form-control" id="vendoremail" name="vendoremail" placeholder="Enter email address" type="text" />
                                                                        </div>
                                                                
                                                                    </div>
                                                            
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Brand</label>

                                                                            <input class="form-control" list="brandlist" id="brand" name="brand-choice" placeholder="Select Brand"/>

                                                                            <datalist id="brandlist">
                                                                                <?php
                                                                            // $vendors = Vendors::model()->findAll('Status=:type', array(':type' => '0'));

                                                                                $brands=Brand::find()->Where(['Status'=>0])->all();
                                                                                foreach($brands AS $brand):
                                                                                    echo "<option value='".$brand->name."'></option>";
                                                                                endforeach;
                                                                                ?>
                                                                            </datalist>

                                                                            <span class="error" style="display: none;"></span> 

                                                                        </div>  
                                                                
                                                                        <div class="form-group">
                                                                            <label>Vendor Location</label>

                                                                            <input class="form-control" type="text" id="vendorlocation" name="vendorlocation" placeholder="Vendor Location">
                                                                            <span class="error" style="display: none;"></span>
                                                                    
                                                                        </div>
                                                                
                                                                        <div class="form-group">
                                                                            <label>Account Head</label>

                                                                            <input class="form-control accounthead" list="accountheadlist" id="accounthead" name="accounthead-choice" placeholder="Account Head"/>

                                                                            <datalist id="accountheadlist">
                                                                                <?php
                                                                            // $accountheads=AccountsItem::model()->findAll();
                                                                                $accountheads=AccountsItem::find()->where(['Status'=>0])->all();

                                                                                foreach($accountheads AS $accounthead):
                                                                                    echo "<option value='".$accounthead->name."'></option>";
                                                                                endforeach;
                                                                                ?>
                                                                            </datalist>

                                                                            <span class="error" style="display: none;"></span>
                                                                    
                                                                        </div>	
                                                                
                                                                    </div>
                                                            
                                                                    <div class="col-md-4">

                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label>Unit</label>
                                                                                    <input class="form-control" type="text" id="resourceunit" name="resourceunit" placeholder="Unit">
                                                                                    <span class="error" style="display: none;"></span>
                                                                                </div>
                                                                            </div>
                                                                    
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label>Rate</label>
                                                                                    <input class="form-control"  type="text" id="resourcerate" name="resourcerate" placeholder="Rate">
                                                                                    <span class="error" style="display: none;"></span>                                         
                                                                                </div>
                                                                            </div>
                                                                
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label>Phone</label>
                                                                            <input class="form-control" id="vendorphone" name="vendorphone" placeholder="Enter Phone Number" type="text" />
                                                                            <span class="error" style="display: none;"></span> 
                                                                            
                                                                        </div>
                                                                
                                                                        <div class="form-group">
                                                                            <label>Account Sub Group</label>
                                                                            <input class="form-control" list="accountsubgrplist" id="accountsubgrp" name="accountsubgrp-choice" value="" placeholder="Select Account Subgroup"/>

                                                                            <datalist id="accountsubgrplist">
                                                                                <?php

                                                                                $accountsubs=AccountsSub::find()->where(['Status'=>0])->andWhere(['master_id'=>5])->all();
                                                                        
                                                                                foreach($accountsubs AS $accountsub):
                                                                                    echo "<option value='".$accountsub->name."'></option>";
                                                                                endforeach;
                                                                                ?>
                                                                            </datalist>

                                                                            <span class="error" style="display: none;"></span>
                                                                    
                                                                        </div>
                                                                
                                                                    </div>

                                                                    <div class="col-md-12 text-center">
                                                                        <div class="form-group text-center" style="position:relative; top:6px;">
                                                                            <span>&nbsp;</span><br/>
                                                                            <button type="button" class="btn btn-danger cancel" id="cancelres" ><span class="icon-close"></span> Cancel</button>

                                                                            <!-- <button type="button" class="btn btn-danger" id="saveresource"><span class="glyphicon glyphicon-saved"></span>Save</button> -->

                                                                            <button type="button" class="btn btn-primary" 
                                                                            id="saveresource"><span class="icon-check"></span> Add Resource</button>
                                                                        </div>
                                                                    </div>				
                                                                </div>

                                                                <div class="text-center">

                                                                </div>
                                                        
                                                            </div>

                                                            <div class="col-md-1"></div>
                                                        </div>
                                                    </form>
                                                </div>

                                                <!-- form ends here -->
                                                
                                                <div class="addvendor-resource-cntnt-wrpr row">
                                                    
                                                    <div class="col-md-1"></div>
                                                    <div class="col-md-10">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-title" style="display:block; margin-top:10px; font-size:17px;">Resource: <span class="resourcename"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                &nbsp;
                                                            </div>
                                                            <div id="resvendoradd">
                                                            
                                                            </div>	
                                                            
                                                        </div>
                                                
                                                        <div class="text-center">										
                                                            
                                                        </div>
                            
                                                    </div>
                                                    <div class="col-md-1"></div>
                                                    
                                                </div>
                                        
                                                <!-- edit (Resrouces) form starts here -->
                                                <div class="resources-edit-cntnt-wrpr row">
                                                    <div class="col-md-2"></div>
                                                    <div class="col-md-8">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-title">Edit Resource</div>
                                                            </div>
                                                            
                                                            <div id="editresource">
                                                            
                                                            </div>	
                                                            
                                                        </div>

                                                        <div class="text-center">
                                                            
                                                            
                                                        </div>									
                                                        
                                                    </div>
                                                    <div class="col-md-2"></div>
                                                </div>
                                                <!-- edit (Resrouces) form ends here -->
                                                
                                                <!-- list start here -->
                                                <div class="resources-cntnt-wrpr row">
                                                    <form>
                                                        <table class="table table-bordered" id="procresourcetable" style="display: table; overflow: hidden;">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th style="width:230px;">Resource</th>
                                                                    <th>Location</th>
                                                                <!--  <th style="width:230px;"></th> -->
                                                                    <th>Brand</th>
                                                                    <th>Unit</th>
                                                                    <th>Rate</th>
                                                                    <th>Last Updated</th>                              
                                                                    <th style="width:100px;" ></th>
                                                                </tr>
                                                                <tr class="preloader" style="display: none;">
                                                                    <td colspan="12" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="procresourceitems">

                                                            </tbody>
                                                        
                                                        </table>
                                                    </form>
                                                    
                                                </div>	
                                                <!-- list end here -->
                                            </div>



                                    </div>
                                </div>
                                    
                            </div>
                        </div>
                    </div>



                        <!-- tab 3  -->

                        <div class="panel panel-default vendors-tab tab acco-three tab-wrapper">
                            <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/vendorfunctions.js" type="text/javascript"></script>
                            <input class="vendormasterpage" type="radio" id="rd5" name="rd">

                            <div class="panel-heading" >
                                <h4 class="panel-title acc_trigger" id="ProcVendor">
                                    <a  href="javascript:void(0)">
                                    <span class="image-icon acc_trigger"></span>Vendors</a>
                                </h4>
                            </div>

                            <div  class="tab-content cOrder-body panel-collapse ">
                                <div class="panel-body">
                                    <div class="search-and-content-wrpr">
                                        <div class="search-and-actions-wrpr row">
                                            <div class="content-search-wrpr col-md-3 col-sm-3" id="searchvendordiv">
                                                <input type="text" placeholder="Search" id="searchvendorname" class="form-control">

                                                <button id="vendorsearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>

                                            </div>

                                            <div class="content-action-wrpr col-md-9 col-sm-9">

                                                <button type="button" class="btn btn-primary list-vendors" id="listvendor" style="display: none"><span class="icon-th-list"></span>List Resources</button>

                                            </div>
                                        </div>

                                        <div class="content-wrpr">

                                            <!-- edit vendors form start here -->
                                            <form id="editvendor">
                                                <div class="vendors-edit-cntnt-wrpr row">
                                                    <div class="col-md-1"></div>
                                                    <div class="col-md-10">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-title">Edit Vendor</div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Vendor Name</label>
                                                                    <input class="form-control" type="text" placeholder="Vendor Name"  id="vendornames"/>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Phone</label>
                                                                    <input class="form-control" type="text" placeholder="Enter Phone Number"  id="vendorphones"/>
                                                                </div>                           
                                                            </div>
                                                            <div class="col-md-4">
                                                            <div class="form-group">
                                                                    <label>Email</label>
                                                                    <input class="form-control" type="text" placeholder="Email"  id="vendoremails"/>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>City</label>
                                                                    <input class="form-control" type="text" placeholder="Enter City"  id="vendorcityy"/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Vendor Address</label>
                                                                    <textarea class="form-control" style="min-height:154px;" id="vendoraddresss"></textarea>
                                                                </div>
                                                            </div>
                                                
                                                            <div class="text-center">
                                                                <button type="button" class="btn btn-danger cancel" id="cancelsedit"><span class="icon-close"></span> Cancel</button>
                                                                <button type="button" class="btn btn-primary savevendorbutton" id="savevendorbutton" value=""><span class="icon-check"></span> Edit Vendor</button>
                                                            <input type="hidden" id="savevendortypeval" value="'.$data['vendor_type'].'">
                                                            </div>
                                                
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1"></div>
                                                </div>
                                            </form>
                                            <!-- edit vendors form end here -->

                                            <!-- list start here -->

                                            <div class="preloader" style="display: none;"><center><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"></center></div>
                                            <div id="vendoritems"></div>
                                            <!-- list start here -->

                                        </div>
                                    </div>

                                </div>
                            </div>
                            <style type="text/css">
                                .vendortable>thead>tr>th,.vendortable>tbody>tr>td {
                                    padding: 4px;
                                    font-size: 14px;
                                }
                            </style>
                        </div>


                        <!-- tab 4  -->

                        <div class="panel panel-default acco-four termscond-tab tab tab-wrapper">
                            <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/termscondtn.js" type="text/javascript"></script>
                            <input type="radio" id="rd5" name="rd">
                            <div class="panel-heading" >
                                <h4 class="panel-title acc_trigger" id="termscond">
                                    <a  href="javascript:void(0)">
                                        <span class="image-icon acc_trigger"></span>Terms & Conditions
                                    </a>
                                </h4>
                            </div>

                            <div  class="tab-content cOrder-body panel-collapse ">
                                <div class="panel-body">
                                    <div class="search-and-content-wrpr">
                                        <div class="search-and-actions-wrpr row">
                                            <div class="content-action-wrpr col-md-12 col-sm-9">
                                                <a href="#" class="btn btn-primary addtermscndtn" id="addtermscndtn"><span class="icon-add"></span> Add</a>

                                                <!--<a class="list-terms" id="listtermscndtn"></a>-->
                                                
                                            </div>
                                        </div>

                                        <div>
                                            <div id="termscondtnsection">
                                            <!-- list start here -->
                                            <div class="resource-type-cntnt-wrpr row">
                                                    <div class="col-md-10 col-sm-10">
                                                        <div class="row">
                                                            
                                                            <div class="col-md-2 col-sm-1">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <label>#</label><br/>	
                                                                    </div>
                                                                    <div class="col-md-9">
                                                                    </div>
                                                                </div>
                                                                
                                                                
                                                            </div>
                                                            <div class="col-md-5 col-sm-3 type">
                                                                <label>Title</label><br/>
                                                            </div>
                                                            <div class="col-md-3 col-sm-4 acc-sub-group">
                                                                <label>Content</label><br/>
                                                            </div>
                                                            <div class="col-md-2 col-sm-4"></div>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-md-2 col-sm-2 icon-groups">
                                                    </div>
                                                </div>
                                            <?php 

                                                $models = TermsCondtns::find()->where(['status' => 0])->all();

                                                if(count($models)>0){

                                                foreach($models as $key => $model):

                                            ?>
                                                <div class="resource-type-cntnt-wrpr row">
                                                    <div class="col-md-10 col-sm-10">
                                                        <div class="row">
                                                            
                                                            <div class="col-md-2 col-sm-1">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <span><?= ($key+1) ?></span>	
                                                                    </div>
                                                                    <div class="col-md-9">
                                                                    </div>
                                                                </div>
                                                                
                                                                
                                                            </div>
                                                            <div class="col-md-5 col-sm-3 type">
                                                                <span><?= $model->title ?></span>
                                                            </div>
                                                            <div class="col-md-3 col-sm-4 acc-sub-group">
                                                                <span><?= $model->content ?></span>
                                                            </div>
                                                            <div class="col-md-2 col-sm-4"></div>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-md-2 col-sm-2 icon-groups">
                                                        <button type="button" class="edittermstypebutton" value="<?= $model->id ?>" id="edittermstypebutton<?= $model->id ?>" title="Edit terms & conditions">
                                                        <a href="#" class="icon-pencil"></a></button>
                                                        <button type="button" class="deletetermstypebutton" value="<?= $model->id ?>" id="deletetermstypebutton<?= $model->id ?>" title="Delete terms & conditions">
                                                        <a href="#" class="icon-trash1"></a></button>
                                                    </div>
                                                </div>
                                                <?php endforeach; } ?>
                                                <!--<div class="resource-type-cntnt-wrpr row">
                                                    <div class="col-md-10 col-sm-10">
                                                        <div class="row">
                                                            
                                                            <div class="col-md-2 col-sm-1">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <span></span>	
                                                                    </div>
                                                                    <div class="col-md-9">
                                                                    </div>
                                                                </div>
                                                                
                                                                
                                                            </div>
                                                            <div class="col-md-5 col-sm-3 type">
                                                                <label>Title</label><br/>
                                                                <span></span>
                                                            </div>
                                                            <div class="col-md-3 col-sm-4 acc-sub-group">
                                                                <label>Content</label><br/>
                                                                <span></span>
                                                            </div>
                                                            <div class="col-md-2 col-sm-4"></div>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-md-2 col-sm-2 icon-groups">
                                                    </div>
                                                </div>-->

                                            </div>

                                            <div id="termscondtnaddsection">
                                            
                                            
                                            </div>

                                            <div id="termscondtneditsection">
                                            
                                            
                                            </div>
                                            
                                        </div>  
                                    </div>
                                
                                </div>
                            </div>
                        </div>
         


                        <!-- tabs end  -->

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
