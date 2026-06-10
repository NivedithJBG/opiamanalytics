<div class="panel panel-default receive-plant-and-equipment-tab tab tab-wrapper acco-four">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/logbook.js" type="text/javascript"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/reportlog.js" type="text/javascript"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/fuelslipnew.js" type="text/javascript"></script>


 <script type="text/javascript">
            $(document).ready(function(){
                $('#logdate0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
                $('#electlogdate0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
                var max_fields      = 10; //maximum input boxes allowed
                var wrapper         = $(".input_fields_wrap"); //Fields wrapper
                var x = 2; //initlal text box count

                var id=$(this).attr('data-id');
                $(document).on( "click", "#addmores", function(e){
                    e.preventDefault();
                    if(x < max_fields){
                        $('#enginerows').before('<tr>' +

                            '<td><input type="text" class="form-control startreading" placeholder="Start Reading" name="startreading[]" id="startreading'+x+'" data-id="'+x+'" autocomplete="off">' +
                            '<span class="error" style="color:red;font-size:12px;"></span> </td>' +
                            
                            '<td><input type="text" class="form-control endreading" placeholder="End Reading" name="endreading[]" id="endreading'+x+'" data-id="'+x+'" autocomplete="off">' +
                            '<span class="error" style="color:red;font-size:12px;"></span> </td>' +

                            '<td><select id="logbookactlist'+x+'" name="logactivity[]" class="form-control logactivity" data-id="'+x+'">'+

                            '<option value="none">Select Activity</option></select>' +

                            '<span class="error" style="color:red;font-size:12px;"></span> </td>' +

                            '<td class="text-right"><span id="nreading'+x+'"></span><input type="hidden" id="nreadingval'+x+'" class="nreadingval"></td>'+
                            '<td><input class="form-control hourss" id="hours'+x+'" data-id="'+x+'" name="hours[]"  type="text"></td>'+

                            '<td><input type="text" class="form-control trips" placeholder="No. of Trips" name="trips[]" id="trips'+x+'" data-id="'+x+'" autocomplete="off">' +
                            '<span class="error" style="color:red;font-size:12px;"></span> </td>' +

                            '<td class="icon-groups"><a href="javascript:void(0)" class="btn btn-primary icon-remove remove_field2"></a></td>' +
                            '</tr>');  
                        var unit=$('#eqpunit').val();
                        console.log(unit)
                        if (unit==1)
                        {
                            $('.startreading').prop('readonly', false);
                            $('.endreading').prop('readonly', false);
                            $('.trips').prop('readonly', true);
                        }
                        else if (unit==2){
                            $('.startreading').prop('readonly', false);
                            $('.endreading').prop('readonly', false);
                            $('.trips').prop('readonly', true);
                        }
                        else if (unit==3){
                            $('.startreading').prop('readonly', true);
                            $('.endreading').prop('readonly', true);
                            $('.trips').prop('readonly', false);
                        }
                    
                        $.ajax({

                            type: 'POST',

                            url: '../projects/logactivity',

                            dataType: "json",

                            data: {projectid:$('#projlogbook').val(),resourceid:$('#equipment').val()}, 
                    
                            success: function(data){

                                if(data.error=='No')

                                {
                                    var id=(x - 1);
                                            
                                    $('#logbookactlist'+id+'').html(data.result);    
                                                
                                }
                            }
                        });
                        x++;
                    }
                });

                $(document).on("click",".remove_field2", function(e){
                    //alert('ssss')
                    e.preventDefault();
                    $(this).parent('td').parent('tr').remove();
                    x--;
                })
            });

       $(document).ready(function() {
        $('#issueslipdate1').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});

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

                $('#issueslipdate'+x).datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
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


       /* Fuel issue slips script */

    $(document).ready(function() {
        $('#fissueslipdatee1').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});

        var max_fields      = 10; //maximum input boxes allowed
        var x = 2; //initlal text box count
        $(document).on( "click","#addmore-fuelissuenew", function(e){
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
                            $('#fuelissuesliprownew').before(data.result);
                        }

                    }
                });

                $('#fissueslipdatenew'+x).datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
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


              <!-- <input type="radio" class="viewlogbook" id="rd5" name="rd"> -->
                <div class="panel-heading" >
                  <h4 class="panel-title" id="viewlogbook">
                    <a data-toggle="collapse" data-parent="#accordionoper" href="#collapselog">
                    <span class="icon-file3"></span>Plant & Equipment</a>
                  </h4>
                </div>



                <div id="collapselog" class="tab-content cOrder-body panel-collapse collapse">
                  <div class="panel-body ">

                       <div class="container">
                     <ul class="nav nav-tabs nav-justified mb-3" id="headlog">
                <li class="active"><a data-toggle="tab" href="#homelog" id="viewlogbook" style="background: #F5F5F5;"><span class="icon-document-text"></span> Movement of Plant and Machinery</a></li>
                <li><a data-toggle="tab" href="#menu13" id="LogEquipmentUsage" style="background: #F5F5F5;margin: inherit;"><span class="icon-document-text"></span>Log Equipment Usage</a></li>
                <li><a data-toggle="tab" href="#menu134" id="fuelslips" style="background: #F5F5F5;margin: inherit;"><span class="icon-document-text"></span>Fuel Issue Slips</a></li>
                
                
                </ul>

            </div>


                   <div class="tab-content">

           <!-- recieve materials starts here -->

             <div id="homelog" class="tab-pane fade in active">

  


                    <div class="search-and-content-wrpr">
                         <hr>

                        <div class="container">
                          <div class="row">
                         <!--    <div class="content-search-wrpr col-md-3 col-sm-3 text-left">
                                <h6 class="projectname" id="projectName-head"></h6>
   
                        </div> -->
                         <div class="content-action-wrpr col-md-6 col-sm-6">
                        <a href="javascript:void(0);" class="btn btn-primary recv-Orders-btn btn-block" id="receivvvorders" title="Receive Orders" style="border-radius: 5px;height: 34px;"><span class="icon-box"></span> Receive</a>
                        </div>
                        <div class="content-action-wrpr col-md-6 col-sm-6" id="desp">
                        <a href="javascript:void(0);" class="btn btn-primary despatch-Orders-btn btn-block" id="listmovfromorders" title="Despatch Orders" style="border-radius: 5px;height: 34px;"><span class="icon-box"></span> Despatch</a>
                                <a href="javascript:void(0);" class="btn btn-primary close-despatch-Orders-btn"><span class="icon-box"></span> Close Despatch</a>  
                                <a href="#" type="hidden" id="listmovtoorders"></a>
                        </div>
                      </div>
                    </div>




                        <!-- <div class="search-and-actions-wrpr row">
                            <div class="content-search-wrpr col-md-3 col-sm-3 text-left">
                                <h6 class="projectname" id="projectName-head"></h6>
                            </div> -->
                            <!-- <div class="col-md-3 col-sm-3"></div> -->

                        <!--    <div class="content-action-wrpr col-md-9 col-sm-9">
                                <div class="content-action-wrpr col-md-5 col-sm-5">
                                <a href="javascript:void(0);" class="btn btn-primary recv-Orders-btn" id="receivvvorders" title="Receive Orders"><span class="icon-box"></span> Receive</a>
                            </div> -->
                                <!-- <div class="content-action-wrpr col-md-4 col-sm-4">
                                <a href="javascript:void(0);" class="btn btn-primary despatch-Orders-btn" id="listmovfromorders" title="Despatch Orders"><span class="icon-box"></span> Despatch</a> -->
                                <!-- <a href="javascript:void(0);" class="btn btn-primary close-despatch-Orders-btn"><span class="icon-box"></span> Close Despatch</a>   -->
                              <!--   <a href="#" type="hidden" id="listmovtoorders"></a>
                            </div>
                            </div>

                        </div> -->


                        <div class="col-md-12 plantss" style="padding-bottom: 10px;">
                            <div class="row planthead">
                            
                                <label style="text-align: center;font-size: 15px;">Plant and Equipment</label>
                            </div>
                           
                        </div>

                        <div class="content-wrpr">
                            <!-- form starts here -->
                                
                            <!-- form ends here -->

                            <!-- edit form starts here -->                      
                            
                            <!-- edit form ends here -->

                            <!-- list start here -->
                            <div class="operations-receive-orders-list-wrpr data-content-list" id="mndes">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="movetoorderitems"></div>                       
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="confirm-purchase" id="receiveeqmsg" style="text-align: right; font-weight: bold;" color: #0ccb0c;></div>
                                    </div>
                                    <div class="col-md-6 text-right" id="hdedataz">
                                        <div class="form-group form-inline" id="ndataz">
                                            <input class="form-control receive-equipment-date datepicker" id="dateofrec" type="text" placeholder="Date of Receipt" />
                                            <span class="error" style="float: left"></span>
                                            <button class="btn btn-primary receiveeq" id="receiveeq" title="Receive Equipment"><span class="icon-check"></span> Receive Equipment</button>
                                            <input type="hidden" id="recorderres" name="recorderres">
                                            <input type="hidden" id="recorderids" name="recorderids">   
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="operations-despatch-orders-list-wrpr data-content-list" id="mnndese">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="movefromorderitems"></div> 
                                <div class="row">
                                    <div class="col-md-4 no-padding">
                                            <button class="btn btn-primary cancel_despatch" id="cancel_despatch" title="Cancel Despatch Order">Cancel</button>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="confirm-purchase" id="despatcheqmsg" style="text-align: right; font-weight: bold; color: #0ccb0c;"></div>
                                    </div>
                                    <div class="col-md-4 text-right" id="hdedata">
                                        <div class="form-group form-inline"  id="ndatazd">
                                            <input class="form-control vehicle-number" id="vehiclenum" type="text" placeholder="Vehicle Number" />
                                            <span class="error" style="float: left"></span>
                                            <button class="btn btn-primary despatcheq" id="despatcheq" title="Despatch Equipment"><span class="icon-check"></span> Despatch Equipment</button>
                                            <input type="hidden" id="order_res_id" name="order_res_id">
                                            <input type="hidden" id="orderres" name="orderres">
                                            <input type="hidden" id="orderids" name="orderids">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- list end here -->
                        </div>
                    </div>

                </div>





                 <div id="menu13" class="tab-pane fade">


                    <div class="search-and-content-wrpr">   
                     <hr>   
                        <a href="#" type="hidden" id="LogEquipmenthead"></a>
                             <div id="tab-listingz"></div>
                              <!-- <hr>   --> 
                            <!--    <div class="col-md-12 plantss" style="padding-bottom: 10px;">  
                              <div class="row planthead">
                            
                                <label style="text-align: center;font-size: 15px;">Log Equipment Usage</label>
                            </div>  
                            </div> --> 
                            <br> 


                     <div class="container">
                          <div class="row">
                            <div class="content-action-wrpr col-md-5 col-sm-5"> 

                            <!--  <a href="javascript:void(0);" class="btn btn-primary  btn-block" id="Log-head-One" title="Engine Driven Equipments " style="border-radius: 5px;height: 34px;"><span class="icon-box"></span> Engine Driven Equipments </a> -->

                            <a href="javascript:void(0);" class="btn btn-primary recv-Orders-btn btn-block" id="Log-head-One" title="Engine Driven Equipments" style="border-radius: 5px;height: 34px;" data-p="'.$projuser->projectid.'" data-r="102"><span class="icon-box"></span> Engine Driven Equipments</a>


                            </div>
                            <div class="content-action-wrpr col-md-5 col-sm-5"> 
                             <!-- <a href="javascript:void(0);" class="btn btn-primary  btn-block" id="Log-head-Two" title="Motor Driven Equipments " style="border-radius: 5px;height: 34px;"><span class="icon-box"></span> Motor Driven Equipments </a>   -->

                              <a href="javascript:void(0);" class="btn btn-primary recv-Orders-btn btn-block" id="Log-head-Two" title="Engine Driven Equipments" style="border-radius: 5px;height: 34px;"  data-p="'.$projuser->projectid.'" data-r="154"><span class="icon-box"></span> Motor Driven Equipments</a>

                             

                            </div>
                            
                            <div class="content-action-wrpr col-md-2 col-sm-2">                             
                                <a href="#" class="btn btn-primary log-list-btn " title="List Log" id="listlog"><span class="icon-book"></span> History</a>
                                <a href="#" class="btn btn-primary close-log-list-btn" id="logclz">
                                   <!--  <span class="icon-close"></span> -->
                                 Close</a>
                                <!-- <a href="#" type="hidden" id="LogEquipmenthead"></a> -->    
                               <!-- <a href="#" type="hidden" id="listdespatchorders"></a> -->      
                                <input type="hidden" id="projelectlogbook" value="">                        
                            </div><br>
                        </div></div><br>





                        
                        <div class="content-wrpr">
                            <!-- form starts here -->
                                <div class="add-form raise-bill-form  row">
                                    <div class="preloader" style="display: none;" align="center">
                                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                    </div>
                                    <div id="Log-Equipment-add-form"></div> 
                                </div>
                            <!-- form ends here -->

                            <!-- edit form starts here -->
                            <div class="edit-form raise-bill-form  row">
                                <div id="Log-Equipment-view-form"> </div>
                            </div>
                            <!-- edit form ends here -->

                            <!-- list start here -->
                            <div class="machinery-list-wrpr data-content-list">
                                <div class="preloader" style="display: none;" align="center">
                                   <!--  <img src="<//?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> -->
                                </div>
                                <div id="Log-Equipment-list"></div>                             
                            </div>
                            
                            
                            <div class="machinery-log-list-wrpr data-content-list">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="Log-Equipment-list-log"></div>     
                            </div>
                            <!-- list end here -->  
                        </div>
                    </div>


                 </div>






                  <div id="menu134" class="tab-pane fade">

                 
                 <div class="search-and-content-wrpr">

                     <hr>
            <div class="search-and-actions-wrpr row">
                <div class="content-search-wrpr col-md-7 col-sm-7 text-left">
                       <!--  <h6><span id="fuelprodropname"></span></h6>&nbsp;&nbsp;&nbsp;
                        <input type="text" placeholder="Resource" id="fuelissresval" class="form-control">
                        <button id="fuellistissuesearch" class="btn btn-primary"  type="button"><span class="icon-search5"></span></button> -->
                </div>
                <div class="col-md-3 col-sm-3"></div>
                <div class="content-action-wrpr col-md-2 col-sm-2">
                    <!-- <a href="javascript:void(0);" class="btn btn-primary addForm " id="fuelissueslipsaddFormnew" title="Raise Issue Slips"><span class="icon-add"></span> Raise Fuel Issue Slip</a> -->
                    <a href="javascript:void(0);" class="btn btn-primary addForm " id="fuelissueslipsaddFormnew" title="Raise Issue Slips">Close</a>
                    <a href="javascript:void(0);" class="btn btn-primary addForm fuelhistoryy" id="fuelissueslipsHistory" title="Hilstory - Fuel Issue Slips">Hilstory</a>
                    <a href="#" type="hidden" id="fuellistissueslips"></a>  
                </div>
            </div>
            <div class="content-wrpr">
                <!-- form starts here -->
                    <div class="add-form raise-bill-form  row listzzful">
                        <div class="preloader" style="display: none;" align="center">
                            <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                        </div>
                        
                        <div id="fuelissueslipsaddz"></div>

                    </div>
                <!-- form ends here -->

                <!-- edit form starts here -->
                <div class="edit-form raise-bill-form  row edtfrm">
                    <div class="preloader" style="display: none;" align="center">
                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                    </div>
                    <div id="fuelissueslipseditz"> </div>
                </div>
                <!-- edit form ends here -->

                <!-- list start here -->
                <div class="issue-material-slips-list-wrpr data-content-list">
                    <div class="preloader" style="display: none;" align="center">
                       <!--  <img src="<//?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> -->
                    </div>
                    <div id="fuelissueslipsitemsz"></div>                            
                </div>          
                <!-- list end here -->
            </div>
          </div>    



                  </div>


                </div>






                  </div>
                </div>
              </div>

