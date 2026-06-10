<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/accounttypes.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Accounttypes"><a href="javascript:void(0)">3. Account Types</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addaccounttypes"><span class="glyphicon glyphicon-plus-sign"></span>Add Account Types</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listaccounttypes"><span class="glyphicon glyphicon-list-alt"></span>List Account Types</button></div>
            </div>
            <div id="accounttypeslistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchaccounttypes" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="accounttypessearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="accounttypestable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="accounttypesitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="accounttypesaddsection" class="row show-grid">
                <form id="accounttypesform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="accounttypesadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="accounttypesname" name="accounttypesname" placeholder="Account Type Name"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="saveaccounttypes"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>