<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/invesments.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Investments"><a href="#Investments">7. Investments</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/Investments/create"><button type="button" class="btn btn-success"  id="addinvestments"><span class="glyphicon glyphicon-plus-sign"></span>Add Investments</button></a> </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listinvestments"><span class="glyphicon glyphicon-list-alt"></span>List Investments</button></div>
            </div>
            <div id="investmentslistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchinvestmentname" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="overheadsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="investmentstable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Investments</th>
                                <th>Unit</th>
                                <th>Rate</th>

                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="investmentitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

