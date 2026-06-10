<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/activitygroups.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Activitygroup"><a href="javascript:void(0)">5. Activity Groups</a></h2>
    <div class="acc_container">
        <div class="block">
            <div class="jumbotron">
                <div class="row show-grid">
                    <div class="col-md-3"><button type="button" class="btn btn-success"  id="addactivitygrp"><span class="glyphicon glyphicon-plus-sign"></span>Add Activity Group</button></div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger" id="listactivitygrp"><span class="glyphicon glyphicon-list-alt"></span>List Activity Groups</button></div>
                </div>
                <div id="activitygrplistsection">
                    <div id="searchdiv" class="row show-grid" style="display: block;">
                        <div class="col-md-9">
                            <input type="text" placeholder="Search" id="searchactivitygrps" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <button id="activitygrpsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <!--Table-->
                        <form>
                            <table class="table table-bordered" id="activitygrptable" style="display: table; overflow: hidden;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th colspan="2"></th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="4" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="activitygrpitems">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div id="activitygrpaddsection" class="row show-grid">
                    <form id="activitygrpform">
                        <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="activitygrpadd">
                            <tbody>
                            <tr>
                                <th>#</th>
                                <th><input class="form-control" type="text" id="activitygrpname" name="activitygrpname" placeholder="Activity Groups Name"><span class="error" style="display: none;"></span></th>
                                <th><button type="button" class="btn btn-danger" id="saveactivitygrp"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>