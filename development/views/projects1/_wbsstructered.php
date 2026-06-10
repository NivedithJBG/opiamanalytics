<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/projects/wbs_structure.js" type="text/javascript"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/ckeditor/ckeditor.js"></script>
<h2 class="acc_trigger" id="wbsstructered"><a href="#">4. Schedule</a></h2>
<div class="acc_container">
    <div id="wbs_estimate_block" class="wbs_item_str">
        <div class="block">
            <div class="jumbotron"> 

                <div class="row show-grid">
                    <div class="col-md-2" style="text-align: left;" id="dispprojectname">
                    </div>
                    <div class="col-md-3" style="display:none;"><button type="button" class="btn btn-danger" id="listwbsname"><span class="glyphicon glyphicon-list-alt"></span>List WBS structure</button></div>
                </div>
                <div id="wbslistsection">
                    <!--<div class="row show-grid" style="background-color: rgb(186, 211, 235);padding-top: 18px;">
                        <div class="col-md-4">
                            <input class="form-control" id="searchname" type="text" placeholder="Search">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger" id="resourcesearch"><span class="glyphicon glyphicon-search" ></span>Search</button>
                        </div>
                        </div>-->
                    <div class="row show-grid">
                        <!--Table-->
                        <form>
                            <table class="table table-bordered" id="wbstable" style="display: table;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <!-- <th>Worktype</th> -->
                                    <th>WBS Stucture Name</th>
                                    <th></th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="wbsitems">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
			 </div>
		</div>
    </div>
</div>