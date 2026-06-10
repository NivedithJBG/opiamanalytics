<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/overheadfunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Overheads"><a href="#Overheads">5. Overheads</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/overheads/create"><button type="button" class="btn btn-success"  id="addoverheads"><span class="glyphicon glyphicon-plus-sign"></span>Add Activity</button></a> </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listoverheads"><span class="glyphicon glyphicon-list-alt"></span>List Activity</button></div>
            </div>
            <div id="overheadslistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchoverheadname" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="overheadsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="overheadstable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Activity</th>
                                <th>Unit</th>
                                <th>Rate</th>

                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="overheaditems" class="ui-sortable">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

