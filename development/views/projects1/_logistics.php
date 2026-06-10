<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/logisticsfunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Logistics"><a href="#Logistics">3. Logistics</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
<!--                <div class="col-md-2" style="text-align: left;" id="logdispprojectname"></div>-->
                <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/logistics/create"><button type="button" class="btn btn-success"  id="addlogistics"><span class="glyphicon glyphicon-plus-sign"></span>Add Activity</button></a> </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listlogistics"><span class="glyphicon glyphicon-list-alt"></span>List Activity</button></div>
            </div>
            <div id="logisticslistsection">
               <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchlogisticsname" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="logisticssearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="logisticstable" style="display: table; overflow: hidden;">
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
                            <tbody id="logisticsitems" class="ui-sortable"> 

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="logisticsaddsection" class="row show-grid">
                <form id="addlogisticsform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="logisticsvalueadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="logisticsname" name="logisticsname" placeholder="Logistics Name"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="logisticsunit" name="logisticsunit" placeholder="Logistics Unit"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="logisticsqty" name="logisticsqty" placeholder="Logistics Quantity"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="savelogistics"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>