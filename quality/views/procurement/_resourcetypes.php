<?php
  use app\models\AccountsSub;
  use app\models\Accountsmaster; 
?>

<div class="panel panel-default acco-one resourcetype-tab tab">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/resourcetype.js" type="text/javascript"></script>

    <!--<input type="radio" id="rd3" class="res-type-tab" name="rd">-->
    <div class="panel-heading" >
        <h4 class="panel-title acc_trigger" id="Resourcetype">
            <a data-toggle="collapse" data-parent="#accordionmaster" href="#collapserestypes">
                <span class="image-icon acc_trigger"></span>Resource Types
            </a>
        </h4>
    </div>

    <div id="collapserestypes" class="tab-content cOrder-body panel-collapse collapse">
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
                        <a href="#" class="btn btn-primary addForm" id="addrestype" title="Add Resource Type"><span class="icon-add"></span> Add</a>
                        <a href="#" class="btn btn-primary list-resourceType" id="listrestype"><span class="icon-th-list"></span> List</a>
                    </div>
                </div>
                <div class="content-wrpr" style="overflow: hidden;">

                    <div id="restypeaddsection">
                        <!-- form starts here -->
                        <form id="addrestypeform">                                
                          <div class="resource-type-add-cntnt-wrpr row">
                            <div class="col-md-12">
                                <div class="form-title">Add new Resource Type</div>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-3">
                              <div class="add-new-form-wrpr">
                                  <div class="form-group">
                                      <label>Enter Resource Type</label>
                                      <input class="form-control" id="restypenameshw" name="restypename" placeholder="Enter resource type name" type="text" />
                                      <span class="error" style="display: none;"></span>
                                  </div>
                              </div>                                    
                            </div>
                            <div class="col-md-3">                               
                                <div class="form-group">
                                    <label>Account Group</label>
                                    <select class="form-control" id="addacntgrp" name="addacntgrp">
                                        <option value="">Select Account Group</option>
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
                                    <label>Select order type</label>
                                    <select name="projecttype" class="form-control" id="projecttype">
                                        <option value="0">Select Order Type</option>
                                        <option value="1">Purchase Order</option>
                                        <option value="2">Work Order</option>
                                        <option value="3">Muster Roll</option>
                                        <option value="4">Lease Order</option>
                                    </select>
                                    <span class="error" style="display: none;"></span>
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
                        <div class="col-md-3">                 
                          <div class="add-new-form-wrpr">                   
                              <div class="form-group">
                                  <label>Enter Resource Type</label>
                                  <input class="form-control" id="restypenames_new" name="restypenames_new" placeholder="Enter resource type name" type="text" />
                                  <span class="error" style="display: none;"></span>
                              </div>
                          </div>                 
                        </div>
                        <div class="col-md-3">                               
                            <div class="form-group">
                                <label>Account Group</label>
                                <select class="form-control" id="editacntgrp" name="editacntgrp">
                                    <option value="">Select Account Group</option>
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
                      <div class="resource-type-cntnt-wrpr restpeheads row">
                        <div class="col-md-10 col-sm-10">
                            <div class="row">
                                <div class="col-md-8 col-sm-1">
                                  <div class="row">
                                    <div class="col-md-3" style="right: auto;margin-left: 14px;">
                                      <label>#</label><br/>
                                    </div>
                                    <div class="col-md-5 col-sm-3">
                                      <label>Resource Type</label><br/>   
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-3" style="left: auto;right: 10px;padding-left: 0px;padding-right: 0px;height: 55px;margin-left: 45px;">
                                  <label>Order Type</label><br/>
                                </div>
                                <div class="col-md-1 col-sm-4"></div>

                                <!-- <div class="col-md-1 col-sm-1 type" style="right: auto;margin-left: 14px;" >
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
                            </div> -->
                        
                        </div>
                      </div>
                      <div class="col-md-2 col-sm-2 "> </div>
                    </div>
                </div>


                

                <div class="col-md-12 list-content" style="overflow-y:auto;">
                       
                  <div class="preloader" style="display: none;"><center><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"></center></div>
                  <div id="restypelistsection"></div>

                </div>

                </div>
            </div>
        </div>
    </div>
</div>
                


                

              














