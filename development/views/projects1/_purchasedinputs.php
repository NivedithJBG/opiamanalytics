<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/pifunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Purchasedinputs"><a href="#Purchasedinputs">13. Purchased inputs</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
<!--                <div class="col-md-2" style="text-align: left;" id="pidispprojectname"></div>-->
                <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/purchasedinputs/create"><button type="button" class="btn btn-danger"  id="addpi"><span class="glyphicon glyphicon-plus-sign"></span>Add Purchased inputs</button></a> </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listpurchasedinputs"><span class="glyphicon glyphicon-list-alt"></span>List Purchased inputs</button></div>
            </div>
            <div id="pilistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchpiname" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="pisearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="pitable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Purchased inputs</th>
                                <th>Unit</th>
                                <th>Rate</th>

                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="piitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="piaddsection" class="row show-grid">
                <form id="addpiform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="pivalueadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="piname" name="piname" placeholder="Purchased inputs Name"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="piunit" name="piunit" placeholder="Purchased inputs Unit"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="piqty" name="piqty" placeholder="Purchased inputs Quantity"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="savepi"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>