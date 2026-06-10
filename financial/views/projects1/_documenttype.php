<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/documenttype.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="documenttype"><a href="javascript:void(0)">10. Document Type</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="adddocumenttype"><span class="glyphicon glyphicon-plus-sign"></span>Add Document Type</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listdocumenttype"><span class="glyphicon glyphicon-list-alt"></span>List Document Type</button></div>
            </div>
            <div id="documenttypelistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchdocumenttype" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="documenttypesearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="documenttypetable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="4" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"></td></tr>
                            </thead>
                            <tbody id="documenttypeitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="documenttypeadd" class="row show-grid">
                <form id="documenttypeform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="documenttypeadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="documenttypename" name="documenttypename" placeholder="Document Type"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="savedocumenttype"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>