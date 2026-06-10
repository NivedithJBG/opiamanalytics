<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/resourcegroup.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Resourcegroup"><a href="#ResourceGroup">2. Resource Groups</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addresgroup"><span class="glyphicon glyphicon-plus-sign"></span>Add Resource Groups</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listresgroup"><span class="glyphicon glyphicon-list-alt"></span>List Resource Groups</button></div>
            </div>
            <div id="resgrouplistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchrestyname" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="resourcegroupsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="resgrouptable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Resource Groups</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="resgroupitems" class="ui-sortable">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="resgroupaddsection" class="row show-grid">
                <form id="addresgroupform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="resourcevalueadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="resgroupname" name="resgroupname" placeholder="Resource Group Name"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="saveresgroup"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>