<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/accountgroups.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Accountgroups"><a href="javascript:void(0)">1. Account Groups</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addaccountgroups"><span class="glyphicon glyphicon-plus-sign"></span>Add Account Groups</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listaccountgroups"><span class="glyphicon glyphicon-list-alt"></span>List Account Groups</button></div>
            </div>
            <div id="acntgrpslistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchaccountgroups" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="acntgrpssearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="acntgrpstable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="acntgrpsitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="acntgrpsaddsection" class="row show-grid">
                <form id="acntgrpsform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="accountgroupsadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="accountgroupsname" name="accountgroupsname" placeholder="Account Groups Name"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="saveacntgrps"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>