<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/equipments.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="equipments"><a href="javascript:void(0)">7. Equipments</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addequipment"><span class="glyphicon glyphicon-plus-sign"></span>Add Equipment</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listequipments"><span class="glyphicon glyphicon-list-alt"></span>List Equipments</button></div>
            </div>
            <div id="equipmentlistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchequipment" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="equipmentsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="equipmenttable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="4" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="equipmentitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="equipmentaddsection" class="row show-grid">
                <form id="equipmentform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="equipmentadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="equipmentname" name="equipmentname" placeholder="Equipment Name"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="equipmentunit" name="equipmentunit" placeholder="Equipment Unit"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="saveequipment"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>