<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/vendorfunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Vendor"><a href="#Vendor">5. Vendors</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addvendor"><span class="glyphicon glyphicon-plus-sign"></span>Add Vendor</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listvendor"><span class="glyphicon glyphicon-list-alt"></span>List Vendor</button></div>
            </div>
            <div id="vendorlistsection">
                <div id="searchvendordiv" class="row show-grid">
                    <div class="col-md-3">
                        <select class="form-control" name="vendortypes" id="vendortypes">
                            <option value="none">Select Vendor Type</option>
                            <?php $types=Vendortypes::model()->findAll();
                            foreach($types AS $type):
                                echo "<option value='".$type->vendor_type_id."'>".$type->name."</option>";
                            endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" placeholder="Search" id="searchvendorname" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="vendorsearch" class="btn btn-primary" type="button"><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered vendortable" id="vendortable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Vendor Type</th>
                                <th>Vendor</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Address</th>
                                <th>Account</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="11" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="vendoritems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="vendoraddsection" class="row show-grid">
                <form id="addvendorform" method="post" action="<?php echo Yii::app()->request->baseUrl; ?>/vendors/create">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="vendorvaluradd">
                        <tbody><tr>
                            <th>#</th>
                            <th>
                                <select class="form-control" name="vendortype" id="vendortype">
                                    <option value="none">Select Vendor Type</option>
                                    <?php $types=Vendortypes::model()->findAll();
                                    foreach($types AS $type):
                                        echo "<option value='".$type->vendor_type_id."'>".$type->name."</option>";
                                    endforeach; ?>
                                </select>
                            </th>
                            <th><input class="form-control" type="text" id="vendorname" name="vendorname" placeholder="Vendor Name"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="vendoremail" name="vendoremail" placeholder="Email"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="vendorphone" name="vendorphone" placeholder="Phone"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="vendorcity" name="vendorcity" placeholder="City"><span class="error" style="display: none;"></span></th>
                            <th><textarea placeholder="Vendor Address" name="vendoraddress" id="vendoraddress" class="form-control"></textarea><span class="error" style="display: none;"></span></th>
                            <th><button type="submit" class="btn btn-danger" id="savevendor"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
    .vendortable>thead>tr>th,.vendortable>tbody>tr>td {
        padding: 5px;
    }
</style>