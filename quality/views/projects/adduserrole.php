
		
<?php
use app\models\Projects;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;
use app\models\Departments;
use app\models\DepartmentTab;
use app\models\Micropermission;
use app\models\UserTabs;
?>






<div class="panel panel-default acco-one accounttype-tabs tab">

<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projects/adduserrole.js" type="text/javascript"></script>
			

		
				<!-- <input type="radio" id="rd3" class="userrole-tab" name="rd"> -->
				
					<div class="panel-heading">
					  <h4 class="panel-title" id="userrole-tab">
						<a data-toggle="collapse" data-parent="#accordionuserrole" href="#collapseaddrole">
						<span class="icon-user-check"></span>Add User Role</a>
					  </h4>
					</div>
				
				
				<div id="collapseaddrole" class="tab-content cOrder-body panel-collapse collapse">
				  <div class="panel-body ">
				  
					<div class="search-and-content-wrpr">
						
						<div class="content-wrpr" style="overflow: hidden;">
							
								
							
							
							
							<!-- list start here -->
							
							<form id="functform" method="POST" >
							<div class="finance-type-cntnt-wrpr row" id="finance-type-cntnt-wrpr" style="display: none;" >
								<div id="roleheader">
									<div class="search-and-actions-wrpr row ">
											<div class="content-search-wrpr col-md-3 col-sm-3">
											</div>
											<div class="content-action-wrpr col-md-9 col-sm-9">
												<a href="javascript:void(0);" class="btn btn-primary addform" id="adduserrole" title="Add User Role"><span class="icon-add"></span> Add</a>
												<a style="display: none;" href="javascript:void(0);" class="btn btn-primary user-role" id="userrolelists"><span class="icon-th-list"></span> List</a>
											</div>
										</div>
								</div>
								<div class="content-wrpr">
									<div class="userrole-list-wrpr data-content-list">
										<div class="row">
											<div class="col-md-12">
													<div class="preloader" id="Promain-preloader-Listwbs" style="display: none;" align="center">
														<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
													</div>
													<div id="listuserrole-data" class="userrolelist row"></div>
												</div>
										</div>

									</div>
									<div class="creuserroles" style="display: none;">
								<div class="col-md-4 col-sm-4">
									<div class="form-group">
										<label>User Role</label>
										<input class="form-control" name="rolename" id="urname" type="text" placeholder="Enter User Role" />
										<span style="color: red;"></span>
									</div>
								</div>
								<div class="col-md-4 col-sm-4">
									<div class="form-group">
										<label>Status</label>
										<select class="form-control" name="rolestatus" id="urstatus">
											<option value="0" selected="selected">Not Active</option>
											<option value="1">Active</option>
											<option value="-1">Banned</option>
										</select>
									</div>
								</div>
								<div class="col-md-4 col-sm-4">
									
								</div>
								
								<div class="col-md-12 col-sm-12 functionst">
									<label>Function</label>
									<hr style="margin:0;" />
									
									<div class="row" >
										<div class="col-md-4">
											<div class="userrole-functions-list-wrpr">

												<?php
													$functions=Departments::find()->where(['Delete_status'=>0])->all();
													foreach ($functions as $key => $function) {
														
													
												  ?>
												<div class="userrole-functions-list userrole-project" data-id="<?php echo $function->dep_id;?>" id="funct">
													<span><?php echo $function->name; ?></span>
													<input type="checkbox" id="functio" name="User[functions][]" value="<?php echo $function->dep_id;?>" />

												</div>
											<?php } ?>
												
											</div>
										</div>
										<div class="col-md-8 ">
											<div class="urser-roles-function-tabs-cntnr">
											
												<div class="user-role-tab-wrpr user-role-project-tab" id="1">
													<div class="finance-tab-header">
														<span>Projects Tabs</span>
														<label >
															Select All/ Unselect All <input id="checkallproj" type="checkbox" />
															<input type="hidden" value="0" id="checkhidden">
														</label>
														
													</div>
													<div class="finance-tab-roles-body ">
														
														<div class="user-roles-body">
															<?php 
															$fun_tabs=DepartmentTab::find()->where(['dep_id'=>1])->all();
															foreach ($fun_tabs as $key => $fun_tab) {
															
															?>
															<div class="userrole-label-wrpr" data-id="<?php echo $fun_tab->tab_id; ?>" id="fun_tab<?php echo $fun_tab->tab_id; ?>"><span><?php echo $fun_tab->name; ?></span> <label><input type="checkbox" class="checkclassproj" id="functtabss" name="User[functiontabs<?php echo $fun_tab->dep_id; ?>][]" value="<?php echo $fun_tab->tab_id; ?>"/></label></div>
														<?php } ?>
															
														</div>
														
														<div class="user-role-micro-permission projecttab-micro">
															<?php $funcf_tabs=DepartmentTab::find()->where(['dep_id'=>1])->andWhere(['tab_id'=>1])->all();
															foreach ($funcf_tabs as $key => $funcf_tab){
															 ?>
															<div class="micro-permission-header">
																<span>Micro Permission: <em><?php echo $funcf_tab->name; ?></em></span>
															</div> <?php } ?>
															<div class="micro-permission-body">
																<?php 
																$micro_per= Micropermission::find()->where(['tab_id'=>1])->all();

																foreach ($micro_per as $key => $data) {
																	
																
																?>
																<div class="micropermission-label-wrpr" id="<?php echo $data->id; ?>"><span><?php echo $data->name; ?></span> <label><input type="checkbox" name="User[tabspermission<?php echo $data->tab_id; ?>][]" value="<?php echo $data->id; ?>" /></label></div>
															<?php } ?>
																
																
															</div>
														</div>
													</div>
													
												</div>
											
											
												<div class="user-role-tab-wrpr user-role-procurement-tab" id="2">
													<div class="finance-tab-header">
														<span>Procurement Tab</span>
														<label >
															Select All/ Unselect All <input id="checkallprocu" type="checkbox" />
															<input type="hidden" value="0" id="check1hidden">
														</label>
														
													</div>
													<div class="finance-tab-roles-body ">
														
														<div class="user-roles-body">
															<?php $funcf_tabs=DepartmentTab::find()->where(['dep_id'=>2])->orderby(['sortorder'=>SORT_ASC])->all();
															foreach ($funcf_tabs as $key => $funcf_tab){
															 ?>
															<div class="userrole-label-wrpr" data-id="<?php echo $funcf_tab->tab_id; ?>" id="<?php echo $funcf_tab->tab_id; ?>"><span><?php echo $funcf_tab->name; ?></span> <label><input type="checkbox" class="checkclassprocu" name="User[functiontabs<?php echo $funcf_tab->dep_id; ?>][]" value="<?php echo $funcf_tab->tab_id; ?>" /></label></div>
														<?php } ?>
														
										
														</div>
														
														<div class="user-role-micro-permission procuretab-micro">
														  <?php $funcf_tabs=DepartmentTab::find()->where(['dep_id'=>2])->andWhere(['tab_id'=>6])->all();
															foreach ($funcf_tabs as $key => $funcf_tab){
															 ?>


															<div class="micro-permission-header">
																<span>Micro Permission: <em><?php echo $funcf_tab->name; ?></em></span>
															</div> <?php } ?>
															<div class="micro-permission-body">


																<?php 
																$micro_per= Micropermission::find()->where(['tab_id'=>6])->all();

																foreach ($micro_per as $key => $data) {
																	
																
																?>
																<div class="micropermission-label-wrpr" id="<?php echo $data->id; ?>"><span><?php echo $data->name; ?></span> <label><input type="checkbox" name="User[tabspermission<?php echo $data->tab_id; ?>][]" value="<?php echo $data->id; ?>" /></label></div>
															<?php } ?>
																
																
															</div>
														</div>
													</div>
													
												</div>
												
												
												
												
												
												<div class="user-role-tab-wrpr user-role-finance-tab active" id="3" style="display: block;">
													<div class="finance-tab-header">
														<span>Finance Tabs</span>
														<label >
															Select All/ Unselect All <input id="checkallfin" type="checkbox" />
															<input type="hidden" value="0" id="check2hidden">
														</label>
														
													</div>
													<div class="finance-tab-roles-body ">
														
														<div class="user-roles-body" >
														<?php $funcf_tabs=DepartmentTab::find()->where(['dep_id'=>3])->orderby(['sortorder'=>SORT_ASC])->all();
															foreach ($funcf_tabs as $key => $funcf_tab){
															 ?>
															
															<div class="userrole-label-wrpr finance_role" data-id="<?php echo $funcf_tab->tab_id; ?>" id="fun_tab<?php echo $funcf_tab->tab_id; ?>"><span><?php echo $funcf_tab->name; ?></span> <label><input type="checkbox" class="checkclassfin" name="User[functiontabs<?php echo $funcf_tab->dep_id; ?>][]" value="<?php echo $funcf_tab->tab_id; ?>" /></label></div>
														<?php } ?>
															
														</div>
														
														<div class="user-role-micro-permission fincancetab-micro">
															<?php $funcf_tabs=DepartmentTab::find()->where(['dep_id'=>3])->andWhere(['tab_id'=>11])->all();
															foreach ($funcf_tabs as $key => $funcf_tab){
															 ?>
															<div class="micro-permission-header" >
																<span>Micro Permission: <em><?php echo $funcf_tab->name; ?></em></span>
															</div> <?php } ?>
															<div class="micro-permission-body">
																<?php 
																$micro_per= Micropermission::find()->where(['tab_id'=>11])->all();

																foreach ($micro_per as $key => $data) {
																	
																
																?>
																<div class="micropermission-label-wrpr" id="<?php echo $data->id; ?>"><span><?php echo $data->name; ?></span> <label><input type="checkbox" name="User[tabspermission<?php echo $data->tab_id; ?>][]" value="<?php echo $data->id; ?>" /></label></div>
															<?php } ?>
																
															</div>
														</div>
													</div>
													
												</div>
												


												<div class="user-role-tab-wrpr user-role-operation-tab" id="4">
													<div class="finance-tab-header">
														<span>Operations Tabs</span>
														<label >
															Select All/ Unselect All <input id="checkalloper" type="checkbox" />
															<input type="hidden" value="0" id="check3hidden">
														</label>
														
													</div>
													<div class="finance-tab-roles-body ">
														
														<div class="user-roles-body">
															<?php $funcf_tabs=DepartmentTab::find()->where(['dep_id'=>4])->orderby(['sortorder'=>SORT_ASC])->all();
															foreach ($funcf_tabs as $key => $funcf_tab){
															 ?>
															
															<div class="userrole-label-wrpr" id="fun_tab<?php echo $funcf_tab->tab_id; ?>"><span><?php echo $funcf_tab->name; ?></span> <label><input type="checkbox" class="checkclassoper" name="User[functiontabs<?php echo $funcf_tab->dep_id; ?>][]" value="<?php echo $funcf_tab->tab_id; ?>" /></label></div>
															<?php } ?>
										
														</div>
														
														<div class="user-role-micro-permission operationtab-micro">
															<?php $funcf_tabs=DepartmentTab::find()->where(['dep_id'=>4])->andWhere(['tab_id'=>20])->all();
															foreach ($funcf_tabs as $key => $funcf_tab){
															 ?>
															<div class="micro-permission-header">
																<span>Micro Permission: <em><?php echo $funcf_tab->name; ?></em></span>
															</div> <?php } ?>
															<div class="micro-permission-body">

																<?php 
																$micro_per= Micropermission::find()->where(['tab_id'=>20])->all();

																foreach ($micro_per as $key => $data) {
																	
																
																?>
																<div class="micropermission-label-wrpr" id="<?php echo $data->id; ?>"><span><?php echo $data->name; ?></span> <label><input type="checkbox" name="User[tabspermission<?php echo $data->tab_id; ?>][]" value="<?php echo $data->id; ?>"/></label></div>
															<?php } ?>
																
																
															</div>
														</div>
													</div>
													
												</div>


												<div class="text-right">
													<label></label>
													<button type="button" class="btn btn-danger cancel" id="cancelcreate"><span class="icon-close"></span> Cancel</button>
													<button type="button" class="btn btn-primary userolecreate" id="userolecreate"><span class="icon-check"></span>Create User Role </button>
												</div>
												
												
											</div>
							
											
											
											
											
										</div>
									</div>
									
								</div>
							</div>



							<!-- edit screen -->
							<div class="edituserroless" id="edituserroless" style="display: none;"></div>
							<!-- edit screen end-->
						</div>
								
							</div></form>
							
							
							
							
							
							
							
							
							
							<!-- list end here -->
						
							
						</div>
					</div>
				  
				  </div>
				</div>
			  </div>



