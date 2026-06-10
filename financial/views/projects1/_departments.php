<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/departments.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="departments"><a href="javascript:void(0)">8. Functions</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="adddepartment"><span class="glyphicon glyphicon-plus-sign"></span>Add Function</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listdepartments"><span class="glyphicon glyphicon-list-alt"></span>List Functions</button></div>
            </div>
            <div id="departmentlistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchdepartment" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="departmentsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="departmenttable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="4" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="departmentitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="departmentadd" class="row show-grid">
                <form id="departmentform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="departmentadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="departmentname" name="departmentname" placeholder="Function Name"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="savedepartment"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>