<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/wbs_itemlist.js" type="text/javascript"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/ckeditor/ckeditor.js"></script>
<h2 class="acc_trigger" id="wbsitemslist"><a href="#">WBS Items List</a></h2>
<div class="acc_container">
    <div class="row" style="margin-top: 10px;
    margin-left: 10px;">        
        <div class="col-md-4" style="text-align:center;display:none;">
          <button type="button" class="btn btn-danger wbs_item_button" style="font-weight: 600;padding-left: 60px;padding-right: 60px;" id="wbs_structure">WBS Schedule</button>
        </div>
    </div>
    
    <!-- WBS SCHEDULE BLOCK -->
    <div id="wbs_structure_block" class="wbs_item" style="display:none;">
        <div class="block">
            <div class="jumbotron"> 
                <div class="row show-grid">
                    <div class="col-md-2" style="text-align: left;" id="dispprojectname_schedule">
                    </div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger" id="listwbs_structure"><span class="glyphicon glyphicon-list-alt"></span>List Schedule Item</button></div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger" id="wbs_structure_relation"><span class="glyphicon glyphicon-list-alt"></span>Relation</button></div>           
                </div>
                <div id="wbsstructureitemlistsection">
                    <div class="row show-grid">
                        <!--Table-->
                        <form>
                            <table class="table table-bordered" id="wbsstructureitemtable" style="display: table;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Schedule Group</th>
                                    <th>Schedule Item</th>
                                    <th></th>
                                    <!-- <th></th>
                                    <th></th> -->
                                    <!--<th></th>-->
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="wbsstructureitems">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>

                 <div id="wbsstructurerelation">

                     <div class="row show-grid">
                        <!--Table-->
                        <form>
                            <table class="table table-bordered" id="wbsstructureitemlisttable" style="display: table;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Schedule Item</th>
                                    <th>Schedule Activity</th>
                                    <th>Schedule Item</th>
                                    <th>Schedule Activity</th>
                                    <th>Relation</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="structure_relation_list">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <!-- WBS CASHFLOW BLOCK -->
    <div id="wbs_cashflow_block" class="wbs_item" style="display:none;">
        <div class="block">
            <div class="jumbotron"> 
                <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="">
                    <tbody>
                        <tr id="nodata"><td colspan="7" style="text-align: center;">No Data Found</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>