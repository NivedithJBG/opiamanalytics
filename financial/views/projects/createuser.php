<?php
use app\models\Projects;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;
use app\models\Departments;
use app\models\DepartmentTab;
use app\models\Micropermission;
?>



<div class="panel panel-default accountgroups-tab tab acco-two">

	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projects/createuser.js" type="text/javascript"></script>

<!-- <input type="radio" id="rd5" name="rd"> -->

<div class="panel-heading" >
<h4 class="panel-title" id="user-tab">
<a data-toggle="collapse" data-parent="#accordionuserrole" href="#collapsecreate">
<span class="icon-user-plus"></span>Create User</a>
</h4>
</div>


<div id="collapsecreate" class="tab-content cOrder-body panel-collapse collapse">
<div class="panel-body ">

<div class="search-and-content-wrpr">
	<input type="hidden" id="listusrs">
	<div class="content-wrpr" style="overflow-y: unset !important;">

		<div id="userheader" style="display:none;margin-bottom: -45px;">
			<div class="search-and-actions-wrpr row ">
				
					<div class="content-action-wrpr col-md-9 col-sm-9" style="margin-left: 286px;">
						<a href="javascript:void(0);" class="btn btn-primary addform" id="addcruserrole" title="Add User"><span class="icon-add"></span> Add</a>
						
					</div>
				</div>
		</div>
		<div class="listiserss"></div>
		<div class="edituserss" style="padding: 10px;"></div>
		
		
		
		<!-- list start here -->
		
		<form id="cretuss" method="POST">
		<div class="finance-type-cntnt-wrpr row">
			<div class="col-md-4 col-sm-4" style="height: 72px;">
				<div class="form-group">
					<label>First Name</label>
					<input class="form-control" id="usfname" type="text" placeholder="Enter First Name" />
					<span class="error"></span>
				</div>
			</div>
			<div class="col-md-4 col-sm-4">
				<div class="form-group">
					<div class="form-group">
					<label>Last Name</label>
					<input class="form-control" id="uslname" type="text" placeholder="Enter Last Name" />

				</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-4">
				<div class="form-group">
					<label>Email</label>
					<input class="form-control" id="usemail" type="text" placeholder="Enter Email" />
				</div>
			</div>
			
			<div class="col-md-4 col-sm-4">
				<div class="form-group">
					<label>Username</label>
					<input class="form-control" id="usrname" type="text" placeholder="Enter Username" />
					<span class="error"></span>
				</div>
			</div>
			<div class="col-md-4 col-sm-4">
				<div class="form-group">
					<div class="form-group">
					<label>Password</label>
					<input class="form-control" id="uspswd" type="text" placeholder="Enter Password" />
					<span class="error"></span>
				</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-4">
				<div class="form-group" id="addusrr">
					
					
					
				
					


				</div>
			</div>
			
			
			<div class="col-md-12 col-sm-12">
				<label>Assigned Projects</label>
				<hr style="margin:0;" />
				
			<!-- <input type="text" id="usprjct" data-role="tagsinput"/> -->
			<select class="form-control usprjct" multiple="multiple" id="usprjct">
			<?php

			   $connection = \Yii::$app->db;
		        $sql = "SELECT * FROM projects WHERE status='0' AND Project_Delete_Status='0' AND favourite=1";
		        $command=$connection->createCommand($sql);
		        $dataReader=$command->query();
		        $projects=$dataReader->readAll();   
		       
		            foreach ($projects as $key => $project) { ?>
		            	<option value="<?php echo $project['Project_Id']; ?>"><?php echo $project['Name']; ?></option>
		            <?php } 
				?>
			</select>
			
				
			</div>
			
			
			<div class="col-md-12 col-sm-12">
				<label>Assigned Accountheads</label>
				<hr style="margin:0;" />
				

				<select class="form-control usracnt" multiple="multiple" id="usracnt">
			<?php
               $connection = \Yii::$app->db; 

		        $sql="SELECT * FROM accounts_item WHERE (account_type=16 and schedule=3) OR (account_type IN (1,2) ) ORDER BY name ASC";
		        $command=$connection->createCommand($sql);
		        $dataReader=$command->query();
		        $datas=$dataReader->readAll();
		        foreach ($datas as $key => $data) {    ?>
		            	<option value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>
		            <?php } 
				?>
			</select>
				
					
				
				
			</div>
			
			<div class="col-md-4 col-sm-4">
				<div class="form-group">
					<label>Status</label>
					<select class="form-control" id="usstatus" >
						<option value="0" selected="selected">Not Active</option>
						<option value="1">Active</option>
						<option value="-1">Banned</option>
					</select>
				</div>
			</div>
			<div class="col-md-2 col-sm-2">
				&nbsp;
			</div>
			<div class="col-md-6 col-sm-6 text-right">
				
					<label></label>
					<button type="button" class="btn btn-danger cancel" id="canceluser"><span class="icon-close"></span> Cancel</button>
					<button type="button" class="btn btn-primary createuser" id="createuser"><span class="icon-check"></span> Create User</button>
				
			</div>
			
		</div>
		
		
		
		
		
		
		</form>
		
		
		<!-- list end here -->
	
		
	</div>

	
	
	
	
</div>

</div>
</div>
</div>
