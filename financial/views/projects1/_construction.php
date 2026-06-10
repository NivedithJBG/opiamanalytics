<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/construction.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Construction"><a href="#Construction">4.Construction</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid"> 
                <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/Construction/create"><button type="button" class="btn btn-success"  id="addprojectsetup"><span class="glyphicon glyphicon-plus-sign"></span>Add Activity</button></a> </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listconstruction"><span class="glyphicon glyphicon-list-alt"></span>List Activity</button></div>
            </div>
            <div id="constructionlistsection">
                <div id="searchdivprojectsetup" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchconstructionname" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="Constructionsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="Constructiontable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Activity</th>
                                <th>Unit</th>
                                <th>Rate</th>

                                <th colspan="4"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="Constructionitems" class="ui-sortable">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

