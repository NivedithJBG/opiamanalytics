<?php

use app\models\Vendors; ?>

<div class="panel panel-default receive-materials-tab tab tab-wrapper acco-four">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/operationorders.js" type="text/javascript"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/issueslips.js" type="text/javascript"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/fuelslip.js" type="text/javascript"></script>

<script type="text/javascript">

 $(document).ready(function() {
        $('#issueslipdate1').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});

        var max_fields      = 10; //maximum input boxes allowed
        var x = 2; //initlal text box count
        $(document).on( "click","#addmore", function(e){
            e.preventDefault();
            if(x < max_fields){
                $('#issuesliprow').before('<tr><td><span class="number">'+x+'</span></td>' +
                    '<td><input type="text" class="form-control datepicker issueslipdate" name="Issueslip_Date[]" id="issueslipdate'+x+'" data-id="'+x+'" value="<?php echo date("d-m-Y")?>">' +
                    '<span class="error" style="display: none;float: left"></span></td>' +
                    '<td><select id="issueslipiowlist'+x+'" name="issueslipiow[]" class="form-control issueslipiowlist" data-id="'+x+'">' +
                    '<option value="none">Select IOW</option></select>' +
                    '<span class="error" style="display: none;float: left"></span></td>' +
                    '<td><select id="issueslipactivity'+x+'" name="Issueslip_Activity[]" class="form-control issueslipactivity" data-id="'+x+'">' +
                    '<option value="none">Select Activity</option></select>' +
                    '<span class="error" style="display: none;float: left"></span></td>' +
                    '<td><select id="IssueslipResource'+x+'" data-id="'+x+'" name="Resource[]" class="form-control IssueslipResource">' +
                    '<option value="none">Select Resource</option></select><span class="error" style="display: none;"></span></td>' +
                    '<td><input type="text" class="form-control" readonly name="Unit[]" id="IssueslipUnit'+x+'" data-id="'+x+'" placeholder="Unit"></td>' +
                    '<td><input type="text" class="form-control IssuedQuantity" name="IssuedQuantity[]" id="IssuedQuantity'+x+'" data-id="'+x+'" placeholder="Quantity" autocomplete="off">' +
                    '<span class="error" style="display: none;"></span></td>' +
                    '<td class="icon-groups"><a href="javascript:void(0)" class="btn btn-primary icon-remove remove_field1"></a></td>' +
                    '</tr>');
                var projectid=$('#projissuesliplist').val();
                //var structure=$('#isslipwbsstructlist1').val();
                var iow=$('#issueslipiowlist1').val();
                var activity=$('#issueslipactivity1').val();
                //alert(activity)
                $.ajax({
                    type: 'POST',
                    url: '../report/addresources',
                    dataType: "json",
                    data: {activity:activity,projectid:projectid,iow:iow},
                    success: function(data){
                        if(data.error=='No')
                        {
                            var id=(x - 1);
                            //$('#isslipwbsstructlist'+id+'').html(data.structure);
                            $('#issueslipiowlist'+id+'').html(data.iow);
                            $('#issueslipactivity'+id+'').html(data.activity);
                            $('#IssueslipResource'+id+'').html(data.resource);
                        }

                    }
                });
                $('#issueslipdate'+x).datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
                x++;
            }
        });
        $(document).on( "click",".remove_field1", function(e){
            //alert('ssss')
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        })
    });


    /* Fuel issue slips script */

    $(document).ready(function() {
        $('#fissueslipdate1').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});

        var max_fields      = 10; //maximum input boxes allowed
        var x = 2; //initlal text box count
        $(document).on( "click","#addmore-fuelissue", function(e){
            e.preventDefault();
            if(x < max_fields){
                $.ajax({
                    type: 'POST',
                    url: '../report/fuelissuerow',
                    dataType: "json",
                    data: {slno:x},
                    success: function(data){
                        if(data.error=='No')
                        {
                            $('#fuelissuesliprow').before(data.result);
                        }

                    }
                });

                $('#fissueslipdate'+x).datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
                x++;
            }
        });
        $(document).on( "click",".fuel_remove_field1", function(e){
            //alert('ssss')
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        })
    });

</script>



    <!-- <input type="radio" class="Purchasevieworders" id="rd5" name="rd"> -->
    <div class="panel-heading" >
        <h4 class="panel-title" id="Purchasevieworders">
        <a  data-toggle="collapse" data-parent="#accordionoper" href="#collapseorder">
        <span class="icon-cart"></span>Materials</a>
        </h4>
    </div>
    <div id="collapseorder" class="tab-content cOrder-body panel-collapse collapse">
        <div class="panel-body ">


              <!--  <ul class="nav nav-tabs text-center">
            
                <li class="slipsss"><a data-toggle="tab"  href="rm" id="Purchasevieworders"><span class="icon-document-text"></span>Receive Material</a></li>
                <li><a data-toggle="tab"  href="im" id="issueslips"><span class="icon-document-text"></span>Issue Material</a></li>
                        
            </ul>
 -->     
                <div class="container">
                <ul class="nav nav-tabs nav-justified mb-3"  id="headerzz">
                <li class="active"><a data-toggle="tab" href="#home2" id="Purchasevieworders" style="background: #F5F5F5;"><span class="icon-document-text"></span> Receive Material</a></li>
                <li><a data-toggle="tab" href="#menu12" id="issueslips" style="background: #F5F5F5;margin: inherit;"><span class="icon-document-text"></span>Issue Material</a></li>
                
                </ul>
            </div>


         
    <div class="tab-content">

           <!-- recieve materials starts here -->

    <div id="home2" class="tab-pane fade in active">
     
       <div class="search-and-content-wrpr">

            <hr>
        
            <div class="search-and-actions-wrpr row">

                <div class="content-search-wrpr col-md-3 col-sm-3" id="projorderslist">
                    <select class="form-control" id="searchpovendor" style="display: none;">
                        <option value="none">Select Vendor</option>
                        <?php
                        $vendors = Vendors::find()->where(['Status' => 0])->orderBy(['Name'=> SORT_ASC])->all();
                        foreach($vendors AS $vendor) { ?>
                            <option value="<?= $vendor->Vendor_Id ?>"><?= $vendor->Name ?></option>
                        <?php } ?>
                    </select>
                   <!--  <button id="posearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button> -->
                </div>
                <div class="content-search-wrpr col-md-3 col-sm-3" id="purchasehistorysection" style="display: none;">
                    <input type="text" placeholder="Search" id="searchpohistory" class="form-control">
                    <button id="pohistorysearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                </div>
                <div class="col-md-7 col-sm-7"></div>
                <div class="content-action-wrpr col-md-2 col-sm-2">
                    
                    <a href="#" class="btn btn-primary order-history-btn " id="purchasehistory" title="Order History"><span class="icon-th-list"></span> Order History</a>
                    <a href="#" class="btn btn-primary close-order-history-btn" id="clzz"><span class="icon-close"></span> Close Order History</a>
                    <input type="hidden" id="projordersearch">
                    
                </div>
            </div>

            <div class="content-wrpr">
                <!-- form starts here -->
                    <div class="add-form receive-meterials-form  row" id="rmdtaa">
                        <div class="preloader" id="Purchase-Orders-form-sectionload" style="display: none;" align="center">
                            <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                        </div>
                        <div id="Purchase-Orders-Receive-Materials"></div>
                    </div>
                <!-- form ends here -->

                <div class="receive-materials-cntnt-wrpr data-content-list">
                    <div class="preloader" id="preloader-Purchase-Orders" style="display: none;" align="center">
                      <!--   <img src="<//?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> -->
                    </div>
                    <div id="Purchase-Orders-List"></div>
                </div>
                
                
                <div class="rm-order-history-cntnt-wrpr data-content-list" id="coh">
                    <div class="preloader" id="preloader-Purchase-Orders" style="display: none;" align="center">
                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                    </div>
                    <div id="Purchase-Orders-History"></div>
                </div>
            </div>
        </div>




    </div>



   <!-- recieve materials ends here -->




    <div id="menu12" class="tab-pane fade">

   
      
  <!-- issue slips starts here -->



  <div class="search-and-content-wrpr">
    <hr>
                        <div class="search-and-actions-wrpr row frstwrapp" style="display: none;">
                            <div class="content-search-wrpr col-md-7 col-sm-7 text-left">
                                    <h6><span id="prodropname"></span></h6>&nbsp;&nbsp;&nbsp;
                                    <input type="text" placeholder="Resource" id="issresval" class="form-control">
                                    <button id="listissuesearch" class="btn btn-primary"  type="button"><span class="icon-search5"></span></button>
                            </div>
                            <div class="col-md-3 col-sm-3"></div>
                            <div class="content-action-wrpr col-md-2 col-sm-2">
                                <a href="#" class="btn btn-primary addForm " id="issueslipsaddForm" title="Raise Issue Slips"><span class="icon-add"></span> Raise Issue Slips</a>
                                <a href="#" type="hidden" id="listissueslips"></a>  
                            </div>
                        </div>
                        <div class="content-wrpr">
                     <!--    <ul class="nav nav-tabs text-center">
            
                            <li class="slipsss"><a data-toggle="pill" href="#materil" id="materilslip"><span class="icon-document-text"></span>Material Issue Slips</a></li>
                            <li><a data-toggle="pill" href="#fuless" id="fuelsli"><span class="icon-document-text"></span> Fuel Issue Slips</a></li>
                        
                        </ul> -->
                            <div class="firtslip">
                        
                            <!-- form starts here -->
                                <div class="add-form raise-bill-form  row">
                                    <div class="preloader" style="display: none;" align="center">
                                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                    </div>
                                    
                                    <div id="issueslipsadd"></div>

                                </div>
                                
                            <!-- form ends here -->

                            <!-- edit form starts here -->
                            <div class="edit-form raise-bill-form  row" id="viw">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="issueslipsedit"> </div>
                            </div>
                            <!-- edit form ends here -->

                            <!-- list start here -->
                            <div class="issue-material-slips-list-wrpr data-content-list" id="isuzslp">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="issueslipsitems"></div>                            
                            </div>          
                            <!-- list end here -->
                         </div>
                         

                        <div class="scndslips">

                        <div class="search-and-actions-wrpr row secwrapp" style="display: none;">
                            <div class="content-search-wrpr col-md-7 col-sm-7 text-left">
                                    <h6><span id="fuelprodropname"></span></h6>&nbsp;&nbsp;&nbsp;
                                    <input type="text" placeholder="Resource" id="fuelissresval" class="form-control">
                                    <button id="fuellistissuesearch" class="btn btn-primary"  type="button"><span class="icon-search5"></span></button>
                            </div>
                            <div class="col-md-3 col-sm-3"></div>
                            <div class="content-action-wrpr col-md-2 col-sm-2">
                                <a href="javascript:void(0);" class="btn btn-primary addForm " id="fuelissueslipsaddForm" title="Raise Issue Slips"><span class="icon-add"></span> Raise Fuel Issue Slip</a>
                                <a href="#" type="hidden" id="fuellistissueslips"></a>  
                            </div>
                        </div>
                            <!-- form starts here -->
                                <div class="add-form raise-bill-form  row">
                                    <!-- <div class="preloader" style="display: none;" align="center">
                                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                    </div> -->
                                    <div id="fuelissueslipsadd"></div>

                                </div>
                            <!-- form ends here -->

                            <!-- edit form starts here -->
                            <div class="edit-form raise-bill-form  row">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="fuelissueslipsedit"> </div>
                            </div>
                            <!-- edit form ends here -->

                            <!-- list start here -->
                            <div class="fissue-material-slips-list-wrpr data-content-list">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="fuelissueslipsitems"></div>                            
                            </div>          
                            <!-- list end here -->

                        </div>
                        </div>
                    </div>  




  <!-- issue slips ends here -->


    </div>
   
    </div>



       <!--  </div> -->
        
        </div>
    </div>
    </div>


   