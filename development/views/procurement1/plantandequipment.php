<?php
use app\models\Projects; 
use app\models\UserProjects;
?>
    
    
    <div class="panel panel-default acco-three tab tab-wrapper">
	
    <!-- <input type="radio" id="rd3" name="rd" > -->
    
    <div class="panel-heading" >
        <input type="hidden" id="listdasboard">
      <h4 class="panel-title acc_trigger" id="plantandequip">
        <a data-toggle="collapse" data-parent="#accordionprocureport" href="#collapselantequip">
        <span class="icon-note1 acc_trigger"></span>Plant and Equipment Usage</a>
      </h4>
    </div>
    <div id="collapselantequip" class="tab-content cOrder-body panel-collapse collapse">
        <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/plantandequpment.js" type="text/javascript"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <div class="panel-body">
   
    <div class="report-list-wrprs" style="display: none;"> 
    	 <div class="row" id="reportshead" >
    	 	<div class="col-md-3"></div>
                            <div class="col-md-3">
                            
                                <div class="card">
                                    <div class="card-body">
                                        <span>1. Engine driven equipments</span>
                                        <span class="icon-groups"><a href="#" class="btn btn-primary"><span class="icon-eye" id="viewengin"></span></a></span>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-3" >
                            
                                <div class="card">
                                    <div class="card-body">
                                        <span>2. Motor driven equipments</span>
                                        <span class="icon-groups"><a href="#" class="btn btn-primary"><span class="icon-eye" id="viewmotor"></span></a></span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                            <!-- Engine driven equipments report -->
                            <div id="engindrivenreport" style="display: none;">
                            	<div class="search-and-content-wrpr" >
                            		<div class="search-and-actions-wrpr row" style="padding-bottom:0px; display: block;">
                                        <h3 class="pull-left" id="rsgroupname" style="font-size: 16px;margin-left: 49px;"></h3>
                            			<input type="hidden" id="estimateProject_Id" name="" value="63">
                            			<div style="display: flex;" class="col-md-12 col-sm-12 typeGroup-indication ">
                            				<div class="content-search-wrpr col-md-12 col-sm-3" style="    margin-left: -290px;">
                            					 <div style="display: flex;" class="col-md-6">
                            					 	<select class="form-control" id="projequipmentcost" name="equipmentcostproject">
							                        <option value="none">Select Project</option>
							                        <?php
							                            $projects = Projects::find()->where(['status' =>0])->andWhere(['Project_Delete_Status' =>0])->all();
							                            if(count($projects) > 0) {
							                                $userproject=UserProjects::find()->where(['userid' => Yii::$app->user->id ])->one();
							                                foreach($projects AS $project) {
							                                    if(36==$project->Project_Id):
							                                        $selected="selected";
							                                    else:
							                                        $selected="";
							                                    endif;
							                        ?>
							                                <option value="<?= $project->Project_Id ?>" <?= $selected;?>><?= $project->Name ?></option>
							                            <?php } ?>
							                        <?php } ?>
							                    	</select>
							                    	<button value="1" name="Product_saveproduct" style="margin-left: 8px;" id="engindrivensearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
							                	</div>
                            				</div>
                            				 <div class="backbttn" style="margin-left: 243px;">
                                				<span class="icon-arrow-left-thick firstlistback" style= "cursor: pointer;"></span>
                            				 </div>

                            			</div>
                            		</div>
                            		<div class="content-wrpr">
                            			<div class="resources-cntnt-wrpr row">
                            				 <table class="table table-bordered" id="equipmentcosttable" style="display: table; overflow: hidden;">
						                        <thead>
						                        <tr>
						                            <th>#</th>
						                            <th>Activity Name</th>
						                            <th>Resource Name</th>
						                            <th>Diesel issued</th>
						                            <th>Avergae rate</th>
						                            <th>Amount</th>
						                            <th>Repairs and maintance cost</th>
						                            <th>Total amount</th>
                                                    <th>Total units worked</th>
                                                    <th>Average rate/Unit</th>
						                            <!-- <th colspan="1"></th> -->
						                        </tr>
						                        <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
						                        </thead>
						                        <tbody id="equipmentcostitems">

						                        </tbody>
						                    </table>
                            			</div>
                            		</div>
                            	</div>
                            </div>
                             <!-- Motor driven equipments report -->
                            <div id="motordrivenreport" style="display: none;">
                                <div class="search-and-content-wrpr" >
                                    <div class="search-and-actions-wrpr row" style="padding-bottom:0px; display: block;">
                                         <h3 class="pull-left" id="resgnames" style="font-size: 16px;margin-left: 49px;"></h3>
                                        <input type="hidden" id="estimateProject_Id" name="" value="63">
                                        <div style="display: flex;" class="col-md-12 col-sm-12 typeGroup-indication ">
                                            <div class="content-search-wrpr col-md-12 col-sm-3" style="    margin-left: -290px;">
                                                 <div style="display: flex;" class="col-md-6">
                                                    <select class="form-control" id="projequipmentcosts" name="equipmentcostproject">
                                                    <option value="none">Select Project</option>
                                                    <?php
                                                        $projects = Projects::find()->where(['status' =>0])->andWhere(['Project_Delete_Status' =>0])->all();
                                                        if(count($projects) > 0) {
                                                            $userproject=UserProjects::find()->where(['userid' => Yii::$app->user->id ])->one();
                                                            foreach($projects AS $project) {
                                                                if(36==$project->Project_Id):
                                                                    $selected="selected";
                                                                else:
                                                                    $selected="";
                                                                endif;
                                                    ?>
                                                            <option value="<?= $project->Project_Id ?>" <?= $selected;?>><?= $project->Name ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    </select>
                                                    <button value="1" name="Product_saveproduct" style="margin-left: 8px;" id="motordrivensearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                                                </div>
                                            </div>
                                             <div class="backbttn" style="margin-left: 243px;">
                                                <span class="icon-arrow-left-thick firstlistback1" style= "cursor: pointer;"></span>
                                             </div>

                                        </div>
                                    </div>
                                    <div class="content-wrpr">
                                        <div class="resources-cntnt-wrpr row">
                                             <table class="table table-bordered" id="motorequipmentcosttable" style="display: table; overflow: hidden;">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Activity Name</th>
                                                    <th>Resource Name</th>
                                                    <th>Power used</th>
                                                    <th>No of Hours worked</th>
                                                    <th>Avergae rate</th>
                                                    <th>Amount</th>
                                                    
                                                    <!-- <th colspan="1"></th> -->
                                                </tr>
                                                <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                                </thead>
                                                <tbody id="motorequipmentcostitems">

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
</div>