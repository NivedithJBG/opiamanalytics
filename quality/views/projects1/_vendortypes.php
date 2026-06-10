<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/vendortype.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Vendortype"><a href="#Vendortype">4. Vendor Types</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addvendortype"><span class="glyphicon glyphicon-plus-sign"></span>Add Vendor Type</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listvendortype"><span class="glyphicon glyphicon-list-alt"></span>List Vendor Types</button></div>
            </div>
            <div id="vendortypelistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">

                    <div class="col-md-6">
                        <input type="text" placeholder="Search" id="searchvendortypename" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="vendortypesearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="vendortypetable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>vendor Type</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="vendortypeitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="vendortypeaddsection" class="row show-grid">
                <form id="addvendortypeform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="vendorvalueadd">
                        <tbody>
                        <tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="vendortypename" name="vendortypename" placeholder="Vendor Type Name"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="savevendortype"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>