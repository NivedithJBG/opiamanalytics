<?php
use app\models\Projects; 
?>
    
    
    <div class="panel panel-default acco-one tab tab-wrapper"> 
    <!-- <input type="radio" id="rd1" name="rd" > --> 
    
	
    
    
    <div class="panel-heading" >
        <input type="hidden" id="listdasboard">
      <h4 class="panel-title acc_trigger" id="liststocks">
        <a data-toggle="collapse" data-parent="#accordionprocureport" href="#collapsestock">
        <span class="icon-note1 acc_trigger"></span>Stock Statements</a>
      </h4>
    </div>
    <div id="collapsestock" class="tab-content cOrder-body panel-collapse collapse">
        <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/stockstatement-materials.js" type="text/javascript"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <div class="panel-body">

	
    
		<div class="report-list-wrpr" style="display: none;">                               
                        <div class="row" id="reporthead" >
                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                            
                                <div class="card">
                                    <div class="card-body">
                                        <span>1. Stock Statement - Materials</span>
                                        <span class="icon-groups"><a href="#" class="btn btn-primary"><span class="icon-eye viewstock" id="viewstockstatement"></span></a></span>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-3" >
                            
                                <div class="card">
                                    <div class="card-body">
                                        <span>2. Stock Statement - Consumables</span>
                                        <span class="icon-groups"><a href="#" class="btn btn-primary"><span class="icon-eye" id="viewconsumables"></span></a></span>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-3"></div>
                            <div class="col-md-3" >
                            
                                <div class="card">
                                    <div class="card-body">
                                        <span>3. Stock Statement - Purchased Inputs</span>
                                        <span class="icon-groups"><a href="#" class="btn btn-primary"><span class="icon-eye" id="viewpurchasein"></span></a></span>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-3" >
                            
                                <div class="card">
                                    <div class="card-body">
                                        <span>4. Stock Statement - Tools and Tackles</span>
                                        <span class="icon-groups"><a href="#" class="btn btn-primary"><span class="icon-eye" id="viewtoolsandtackles"></span></a></span>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-3"></div>
                            <div class="col-md-3" style="display: none;">
                            
                                <div class="card">
                                    <div class="card-body">
                                        <span>5. Stock Statement - Subcontractors</span>
                                        <span class="icon-groups"><a href="#" class="btn btn-primary"><span class="icon-eye" id="viewbalancesheet"></span></a></span>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-3"  style="display: none;">
                            
                                <div class="card">
                                    <div class="card-body">
                                        <span>6. Resource Register</span>
                                        <span class="icon-groups"><a href="#" class="btn btn-primary"><span class="icon-eye" id="viewbalancesheet"></span></a></span>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-3"></div>
                 
                        </div>
                         <!-- Stock Statement - Materials -->
                        <div id="stockstatements" style="display: none;">
                        	<div class="search-and-content-wrpr" >
                        		
    		              <div class="search-and-actions-wrpr row" style="padding-bottom:0px; display: block;">
                                
                            
                            <input type="hidden" id="estimateProject_Id" name="" value="63">
                            <div style="display: flex;" class="col-md-12 col-sm-12 typeGroup-indication ">
                                <div class="content-search-wrpr col-md-12 col-sm-3" style="    margin-left: -290px;">

                             <div style="display: flex;" class="col-md-6">
                               <select class="form-control" id="stockproject" name="place">
                                <option value="none">Select Project</option>
                                <?php
                                $projects = Projects::find()->where(['status'=>0]) ->andwhere(['Project_Delete_Status'=>0])->all();
                                if(count($projects) > 0) {
                                foreach($projects AS $project) {
                                ?>
                                    <option value="<?= $project->Project_Id ?>"><?= $project->Name ?></option>
                                <?php } ?>
                                <?php } ?>

                            </select>
                                <select class="form-control" id="stockitem" name="resource" style="margin-left: 38px;">
                                <option value="none">Select Item</option>

                            </select>
                                <button value="1" name="Product_saveproduct" style="margin-left: 8px;" id="stocksearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                            </div>

                            </div>
                            <div class="backbttn" style="margin-left: 243px;">
                                <span class="icon-arrow-left-thick mainlistback" style= "cursor: pointer;"></span>
                            </div>
                            </div>
                            
                        </div>
                        <div class="content-wrpr">
                        	<div class="resources-cntnt-wrpr row">

                        <table class="table table-bordered" id="stockresourcetable" style="display: table; overflow: hidden;background-color: #ffffff">
                            <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Item</th>
                                <th>Unit</th>
                                <th>P.Qnty</th>
                                <th>U.Qnty</th>
                                <th>Stock</th>
                                <th>Rate</th>
                                <th>Stock Amount</th>
                                <th>Purchase Amount</th>
                                <th>Used Amount</th>
                                <th colspan="1"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="11" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="stockresourceitems">

                            </tbody>
                        </table>
                    
                        	</div>
                        </div>

    	
               
           				</div>
                        </div>
                        <!-- Details -->
                        <div id="stockdetails" style="display: none;">
                        	
                            <div class="detailedstock" style="display: block;">
                                <div class="drilldown2back" style="margin-left: 1231px;">
                                <a href="javascript:void(0)" class="drilldown2" style="display: inline-block;position: relative;z-index: 9999;padding: 2em;margin: -2em;margin-top: 1px;color: inherit;">
                                <span class="icon-arrow-left-thick " ></span></a>
                            </div>
                        	<h2 style="text-align: left">Details of procurement</h2>
							<h3 class="pull-left" id="resourcename"></h3>
						<table class="table table-bordered" id="procdetailstable" style="background-color: #ffffff;">
						    <thead>
						    <tr >
						        <th><b>Sl no</b></th>
						        <th><b>Date</b></th>
						        <th>Vendor</th>
						        <th>Unit</th>
						        <th>Qnty</th>
						        <th>Rate</th>
						        <th>Amount</th>
						        <th>GST</th>
						        <th>Amount Inc GST</th>
						    </tr>
						    <tr class="preloaderitems">
						        <td colspan="9" align="center">
						            <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
						        </td>
						    </tr>
						    </thead>
						    <tbody id="datarows">
						        
						    </tbody>
						</table>
						<h2 style="text-align: left">Details of Usage</h2>
						<h3 class="pull-left" id="resourcename"></h3>
						<table class="table table-bordered" id="procdetailstable" style="background-color: #ffffff;">
						    <thead>
						    <tr>
						        <th><b>Sl no</b></th>
						        <th><b>Activity Used</b></th>
						        <th>Unit</th>
						        <th>Qnty</th>
						        <th>Rate</th>
						        <th>Amount</th>
						    </tr>
						    <tr class="preloaderitems">
						        <td colspan="7" align="center">
						            <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
						        </td>
						    </tr>
						    </thead>
						    <tbody id="dataactrows">
						        
						    </tbody>
						</table>
                            </div>
                        </div>
                        <!--  Stock Statement - Consumables -->
                        <div id="stockstatementsconsumables" style="display: none;">
                            <div class="search-and-content-wrpr">
                                
                               <div class="search-and-actions-wrpr row" style="padding-bottom:0px; display: block; ">
                               
                                 <input type="hidden" id="estimateProject_Id" name="" value="63">
                                 <div style="display: flex;" class="col-md-12 col-sm-12 typeGroup-indication ">
                                    <div class="content-search-wrpr col-md-12 col-sm-3" style="    margin-left: -290px;">
                                    <div style="display: flex;" class="col-md-6">
                                         <select class="form-control" id="stockconsproject" name="place">
                                <option value="none">Select Project</option>
                                <?php
                                $projects = Projects::find()->where(['status'=> 0])->andWhere(['Project_Delete_Status'=> 0])->all();
                                if(count($projects) > 0) {
                                    foreach($projects AS $project) {
                                        ?>
                                        <option value="<?= $project->Project_Id ?>"><?= $project->Name ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        
                            <select class="form-control" id="stockconsitem" name="resource" style="margin-left: 38px;" >
                                <option value="none">Select Item</option>
                            </select>
                        
                        
                            <button id="stockconssearch" class="btn btn-primary" type="button" style="margin-left: 8px;"><span class="icon-search5"></span></button>
                        </div>
                                    </div>
                                        <div class="backbttn1" style="margin-left: 243px;">
                                         <span class="icon-arrow-left-thick mainlistback1" style= "cursor: pointer;"></span>
                                     </div>
                                 </div>

                               </div>
                               <div class="content-wrpr">
                                <div class="resources-cntnt-wrpr row">
                                     <table class="table table-bordered" id="stockconsresourcetable" style="display: table; overflow: hidden; background-color: #ffffff">
                            <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Item</th>
                                <th>Unit</th>
                                <th>P.Qnty</th>
                                <th>U.Qnty</th>
                                <th>Stock</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th colspan="1"></th>
                            </tr>
                             <tr class="preloader" style="display: none;"><td colspan="11" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="stockconsresourceitems">

                            </tbody>
                        </table>
                                </div>
                               </div>
                            </div>

                        </div>
                         <!-- Stock Statement - Purchased Inputs -->
                        <div id="stockstatementspurchase" style="display: none;">
                            <div class="search-and-content-wrpr">
                                
                                <div class="search-and-actions-wrpr row" style="padding-bottom:0px; display: block;" >
                                   
                                    <input type="hidden" id="estimateProject_Id" name="" value="63">
                                    <div style="display: flex;" class="col-md-12 col-sm-12 typeGroup-indication ">
                                        <div class="content-search-wrpr col-md-12 col-sm-3" style="    margin-left: -290px;">
                                         <div style="display: flex;" class="col-md-6">
                                            <select class="form-control" id="stockpurchproject" name="place">
                                        <option value="none">Select Project</option>
                                        <?php
                                        $projects = Projects::find()->where(['status' => 0])->andWhere(['Project_Delete_Status' => 0])->all();
                                        if(count($projects) > 0) {
                                            foreach($projects AS $project) {
                                                ?>
                                                <option value="<?= $project->Project_Id ?>"><?= $project->Name ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <select class="form-control" id="stockpurchitem" name="resource" style="margin-left: 38px;">
                                        <option value="none">Select Item</option>
                                    </select>
                                    <button id="stockpurchsearch" class="btn btn-primary" type="button" style="margin-left: 8px;" ><span class="icon-search5"></span></button>

                                         </div>
                                     </div>
                                     <div class="backbttn2" style="margin-left: 243px;">
                                         <span class="icon-arrow-left-thick mainlistback2" style= "cursor: pointer;"></span>
                                     </div>
                                    </div>
                                </div>
                                <div class="content-wrpr">
                                     <div class="resources-cntnt-wrpr row">
                                        <table class="table table-bordered" id="stockpurchresourcetable" style="display: table; overflow: hidden; background-color: #ffffff">
                            <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Item</th>
                                <th>Unit</th>
                                <th>P.Qnty</th>
                                <th>U.Qnty</th>
                                <th>Stock</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th colspan="1"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="stockpurchresourceitems">

                            </tbody>
                        </table>
                                     </div>
                                </div>
                            </div>
                        </div>
                         <!-- Stock Statement - Tools and Tackles -->
                        <div id="stockstatementstool" style="display: none;">
                             <div class="search-and-content-wrpr">
                                 
                                  <div class="search-and-actions-wrpr row" style="padding-bottom:0px; display: block;" >
                                    
                                    <input type="hidden" id="estimateProject_Id" name="" value="63">
                                    <div style="display: flex;" class="col-md-12 col-sm-12 typeGroup-indication ">
                                        <div class="content-search-wrpr col-md-12 col-sm-3" style="    margin-left: -290px;">
                                        <div style="display: flex;" class="col-md-6">
                                            <select class="form-control" id="stocktoolproject" name="place">
                                <option value="none">Select Project</option>
                                <?php
                                $projects = Projects::find()->where(['status' => 0])->andWhere(['Project_Delete_Status' => 0])->all();
                                if(count($projects) > 0) {
                                    foreach($projects AS $project) {
                                        ?>
                                        <option value="<?= $project->Project_Id ?>"><?= $project->Name ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <select class="form-control" id="stocktoolitem" name="resource" style="margin-left: 38px;">
                                <option value="none">Select Item</option>
                            </select>
                            <button id="stocktoolssearch" class="btn btn-primary" type="button" style="margin-left: 8px;"><span class="icon-search5"></span></button>
                                        </div>
                                    </div>
                                    <div class="backbttn3" style="margin-left: 243px;">
                                         <span class="icon-arrow-left-thick mainlistback3" style= "cursor: pointer;"></span>
                                    </div>
                                    </div>
                                  </div>
                                  <div class="content-wrpr">
                                    <div class="resources-cntnt-wrpr row">
                                        <table class="table table-bordered" id="stocktoolresourcetable" style="display: table; overflow: hidden;background-color: #ffffff;">
                            <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Item</th>
                                <th>Unit</th>
                                <th>P.Qnty</th>
                                <th>U.Qnty</th>
                                <th>Stock</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th colspan="1"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="stocktoolresourceitems">

                            </tbody>
                        </table>
                                    </div>
                                  </div>
                             </div>
                        </div>
                                    
                    </div>
                </div>
                </div>
            </div>
	


