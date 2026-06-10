<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/schedulegroups.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Schedulegroups"><a href="javascript:void(0)">5. Schedule</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addschedulegroups"><span class="glyphicon glyphicon-plus-sign"></span>Add Schedule Name</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listschedulegroups"><span class="glyphicon glyphicon-list-alt"></span>List Schedule Names</button></div>
            </div>
            <div id="schedulegrplist">
                <!--<div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchaccountgroups" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="acntgrpssearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>-->
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="schedulegrptable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="schedulegrpitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="schedulegrpsadd" class="row show-grid">
                <form id="schedulegrpsform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="schedulegrpadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="schedulegroupsname" name="schedulegroupsname" placeholder="Schedule Name"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="saveschedulegrps"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>