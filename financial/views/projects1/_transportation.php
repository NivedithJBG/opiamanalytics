<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/pmtfunction.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="PMT"><a href="javascript:void(0);">4. Plant,Machinery And Transportation</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/materials/create"><button type="button" class="btn btn-success"  id="addpmt"><span class="glyphicon glyphicon-plus-sign"></span>Add PMT</button></a> </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listpmt"><span class="glyphicon glyphicon-list-alt"></span>List PMT</button></div>
            </div>
            <div id="pmtlistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchpmtname" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="pmtsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="pmttable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Project Material</th>
                                <th>Unit</th>
                                <th>Rate</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="pmtitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <!--<div id="productaddsection" class="row show-grid">
                <form id="addproductform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="productvalueadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="productname" name="productname" placeholder="Product Name"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="saveproduct"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>-->
        </div>
    </div>
</div>