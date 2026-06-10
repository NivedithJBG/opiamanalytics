<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/resourcetype.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Resourcetype"><a href="#Resourcetype">2. Resource Types</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addrestype"><span class="glyphicon glyphicon-plus-sign"></span>Add Resource Type</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listrestype"><span class="glyphicon glyphicon-list-alt"></span>List Resource Types</button></div>
            </div>
            <div id="restypelistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">

                    <div class="col-md-6">
                        <input type="text" placeholder="Search" id="searchrestypename" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="resourcetypesearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="restypetable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>

                                <th>Resource Type</th>
                                <th>Account Sub-Group</th>
                                <th>Unit Source</th>
                                <th>Order Type</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="restypeitems" class="ui-sortable">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="restypeaddsection" class="row show-grid">
                <form id="addrestypeform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="resourcevalueadd">
                        <tbody><tr>
                            <th>#</th>
                            <!--<th>
                                    <select id="searcselectgroupcreate" class="form-control">
                                        <option value="none">Select Resource Group</option>
                                        <?php
/*                                        $typelist=ResourceGroup::model()->findAll();
                                        foreach($typelist AS $list):
                                            echo "<option value='".$list->Resource_group_Id."'>".$list->Resource_group_Name."</option>";
                                        endforeach;
                                        */?>
                                    </select>


                            </th>-->
							<th>
								<select name="rstpaccountsub" class="form-control"  id="rstpaccountsub">
									<option value="">Select Account Sub Group</option>
									<?php 
									$subgeoups=AccountsSub::model()->findAll(array('condition'=>'master_id=1'));
									foreach ($subgeoups as $key => $group):
										echo '<option value="'.$group->id.'">'.$group->name.'</option>';	
									endforeach;
									?>
								</select>
								<span class="error" style="display: none;"></span>
							</th>
                            <th>
                                <select name="unitsource" class="form-control" id="unitsource">
                                    <option value="0">Select Source Unit</option>
                                    <option value="1">Purchase Bills</option>
                                    <option value="2">Pay Roll</option>
                                    <option value="3">Muster Roll</option>
                                    <option value="4">Work Bill</option>
                                    <option value="5">Lease Order</option>
                                    <option value="6">Log Book and Books of Accounts</option>
                                    <option value="7">Books of Accounts</option>
                                    <option value="8">Book of Accounts - OT</option>
                                </select>
                            </th>
                            <th>
                                <select name="projecttype" class="form-control" id="projecttype">
                                    <option value="0">Select Order Type</option>
                                    <option value="1">Purchase Order</option>
                                    <option value="2">Work Order</option>
                                    <option value="3">Muster Roll</option>
                                    <option value="4">Lease Order</option>
                                </select>
                            </th>
                            <th><input class="form-control" type="text" id="restypename" name="restypename" placeholder="Resource Type Name"><span class="error" style="display: none;"></span></th>

                            <th><button type="button" class="btn btn-danger" id="saverestype"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>