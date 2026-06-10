<?php
use app\models\Vendortypes;   
use app\models\VendorGroup; 
?>

<div class="panel panel-default vendors-tab tab acco-three">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/vendorfunctions.js" type="text/javascript"></script>
    <!-- <input type="radio" id="rd5" class="master-vendortab" name="rd"> -->

    <div class="panel-heading" >
        <h4 class="panel-title acc_trigger" id="master-vendortab">
            <a data-toggle="collapse" data-parent="#accordionmaster" href="#collapsevendorss">
            <span class="image-icon acc_trigger"></span>Vendors</a>
        </h4>
    </div>

    <div id="collapsevendorss" class="tab-content cOrder-body panel-collapse collapse">
        <div class="panel-body">
            <div class="search-and-content-wrpr">
                <div class="search-and-actions-wrpr row">
                    <div class="content-search-wrpr col-md-3 col-sm-3" id="searchvendordiv">
                        <input type="text" placeholder="Search" id="searchvendorname" class="form-control">

                        <button id="vendorsearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>

                    </div>

                    <div class="content-action-wrpr col-md-9 col-sm-9">

                        <button type="button" class="btn btn-primary list-vendors" id="listvendor" style="display: none"><span class="icon-th-list"></span>List Resources</button>

                    </div>
                </div>

                <div class="content-wrpr">

                    <!-- edit vendors form start here -->
                    <form id="editvendor">
                        <div class="vendors-edit-cntnt-wrpr row">
                            <div class="col-md-1"></div>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-title">Edit Vendor</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Vendor Name</label>
                                            <input class="form-control" type="text" placeholder="Vendor Name"  id="vendornames"/>
                                            <span class='error'></span>
                                        </div>
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input class="form-control" type="text" placeholder="Enter Phone Number"  id="vendorphones"/>
                                        </div>                           
                                    </div>
                                    <div class="col-md-4">
                                       <div class="form-group">
                                            <label>Email</label>
                                            <input class="form-control" type="text" placeholder="Email"  id="vendoremails"/>
                                        </div>
                                        <div class="form-group">
                                            <label>City</label>
                                            <input class="form-control" type="text" placeholder="Enter City"  id="vendorcityy"/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Vendor Address</label>
                                            <textarea class="form-control" style="min-height:154px;" id="vendoraddresss"></textarea>
                                        </div>
                                    </div>
                        
                                    <div class="text-center">
                                        <button type="button" class="btn btn-danger cancel" id="cancelsedit"><span class="icon-close"></span> Cancel</button>
                                        <button type="button" class="btn btn-primary savevendorbutton" id="savevendorbutton" value=""><span class="icon-check"></span> Edit Vendor</button>
                                       <input type="hidden" id="savevendortypeval" value="'.$data['vendor_type'].'">
                                    </div>
                        
                                </div>
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                    </form>
                    <!-- edit vendors form end here -->

                    <!-- list start here -->

                    <div class="col-md-12">
                        <form>
                            <table class="table table-bordered indent-table vendhead" id="vendortable" style="display: table; overflow: hidden;">
                                <thead class="vendhead">
                                <tr>
                                    <th>#</th>
                                    <!-- <th>Vendor Type</th>
                                    <th>Vendor Group</th> -->
                                    <th>Vendor</th>
                                   <!--  <th>Brand</th> -->
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th style="width: 20%">Address</th>
                                   <!--  <th>Account</th> -->
                                    <th colspan="3"></th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="12" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="vendoritems">

                                </tbody>
                            </table>
                        </form>
                    </div>




                    <!-- list start here -->

                </div>
            </div>

        </div>
    </div>
    <style type="text/css">
        .vendortable>thead>tr>th,.vendortable>tbody>tr>td {
            padding: 4px;
            font-size: 14px;
        }
    </style>
</div>