<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/majorconsumables.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Majorconsumables"><a href="#Majorconsumables">12. Major Consumables</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/MajorConsumables/create"><button type="button" class="btn btn-success"  id="addinvestments"><span class="glyphicon glyphicon-plus-sign"></span>Add MajorConsumables</button></a> </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listconsumables"><span class="glyphicon glyphicon-list-alt"></span>List MajorConsumables</button></div>
            </div>
            <div id="consumableslistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchconsumablesname" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="consumablessearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="consumablestable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Major Consumables</th>
                                <th>Unit</th>
                                <th>Rate</th>

                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="consumablesitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

