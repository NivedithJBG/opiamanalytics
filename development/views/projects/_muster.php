<div class="panel panel-default raise-wage-roll-tab tab tab-wrapper acco-eight">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/muster.js" type="text/javascript"></script>



              <!-- <input type="radio" class="Raise-Wage-Roll" id="rd5" name="rd"> -->
                <div class="panel-heading" id="Raise-Wage-Roll">
                  <h4 class="panel-title" >
                    <a  data-toggle="collapse" data-parent="#accordionoper" href="#collapsemuster">
                    <!-- <span class="icon-banknote"></span>Wage Roll</a> -->
                        <span class="icon-banknote"></span>Direct Labour</a>
                  </h4>
                </div>
                <div id="collapsemuster" class="tab-content cOrder-body panel-collapse collapse">
                  <div class="panel-body ">





                  
                    <div class="search-and-content-wrpr">
                    
                    
                        <div class="search-and-actions-wrpr row">



                              <div class="content-search-wrpr col-md-12 col-sm-12" style="margin-top: -20px;position: unset;">
                             <div class="container">
                            <ul class="nav nav-tabs nav-justified mb-3"  id="headerzz">
                            <li class="active"><a data-toggle="tab" href="#opwrbill" id="rptattendance" style="background: #F5F5F5;"><span class="icon-file3"></span>Report  Attendance</a></li>
                            <li><a data-toggle="tab" href="#oplsbill" id="mustroll" style="background: #F5F5F5;margin: inherit;"><span class="icon-file3"></span>Muster Roll</a></li>
                            
                            </ul>
                        </div>
                      
                        </div>




                            <!-- <div class="content-search-wrpr col-md-7 col-sm-7 text-left" id="dateinfodiv"></div> -->



                         <div class="content-action-wrpr col-md-9 col-sm-9">

                        </div>

                            <div class="content-action-wrpr col-md-2 col-sm-2">
                                
                                <a href="javascript:void(0);" style="display:none;" class="btn btn-primary muster-btn123" id="listattendance123" title="Attendance History"><span class="icon-history"></span> Attendance History</a>

                                <a href="javascript:void(0);" class="btn btn-primary muster-btn " id="listmuster" title="History"><span class="icon-history"></span> History Of Muster</a>


                                 <a href="javascript:void(0);" class="btn btn-primary muster-btn " id="atthis" title="Attendance History"><span class="icon-history"></span> Attendance History</a>



                                <a href="javascript:void(0);" style="display: none;" class="btn btn-primary muster-btn " id="raisehistory"><span class="icon-history"></span> History</a>

                                <a href="javascript:void(0);" class="btn btn-primary close-muster-btn"><span class="icon-close"></span> Close</a>
                                <a href="#" type="hidden" id="receivedirectwork"></a>

                                <a href="javascript:void(0);" style="display:none;" id="closeattendance" class="btn btn-primary close-attendance-btn"><span class="icon-close"></span> Close Attendance</a>
                                <a href="#" type="hidden" id="receiveattendance"></a>
                                
                            </div>
                        </div>
                        <div class="col-md-12 drctworkorderitem text-center" id="directdis" style="padding-bottom: 20px;">
                            <div class="row drcthead">
                                <label style="font-size: 15px;" id="directLabourTitle">Direct Work orders</label>
                            </div>
                        </div>
                        
                        
                        <div class="content-wrpr" id="newone">
                            <!-- form starts here -->
                                <div class="add-form raise-bill-form  row" style="padding-bottom: 55px">
                                    <div class="preloader" style="display: none;" align="center">
                                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                    </div>
                                    <div id="raisemusteritems"></div>
                                </div>
                            <!-- form ends here -->
                            
                            <!-- edit form starts here -->
                            <div class="edit-form raise-bill-form  row" id="msrll" style="padding-bottom:50px;">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="raisemusteritemsview"></div>
                            </div>
                            <!-- edit form ends here -->

                            <!--  report-attend form starts here -->
                            <div class="report-attend-form raise-bill-form row" id="attendreports">
                                <div class="preloader" style="display: none;" align="center">
                                    
                                </div>
                                <div id="raisattenditemsview"></div>
                            </div>
                            <!--  report-attend form ends here -->

                            <!-- list start here -->
                            <div class="raise-wage-roll-list-wrpr data-content-list rtpdatazz" id="rtpdatazzr" style="padding-bottom: 40px;">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="receivedirectworkitems" class="receivedirectworkitemss"></div>
                                <div id="attendancedata"></div>
                            </div>



                           <!--   <div class="raise-wage-roll-list-wrpr data-content-list">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<//?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="receivedirectworkitems" class="receivedirectworkitemss"></div> -->
                                 <div class="raise-wage-roll-list-wrpr data-content-list" id="attnhis">
                               <!--  <div class="preloader" style="display: none;" align="center">
                                    <img src="<//?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div> -->
                                <div id="attendancedataz"></div>
                               </div>
                           <!--  </div> -->


                             



                            
                            <div class="muster-list-wrpr data-content-list" id="msttems" style="padding-bottom:50px;">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="musteritems"></div>        
                            </div>

                         
                            <!-- list end here -->
                        </div>


              
                        <!-- form starts here -->

                        <div class="data-content-wagerolllist" style="padding-bottom: 40px;">

                            <div class="col-md-12">
                  
                                <form id="musterform">

                                    <div id="musteraddsection" style="display: none">
                                        <div id="musterprocess" class="row show-grid">
                                            
                                            <div class="col-md-3">
                                                <input type="text" class="form-control datepicker muster_date" name="Muster_Date" id="date0" value="<?php echo date("d-m-Y");?>">
                                            </div>
                                            <div class="col-md-4" >
                                                <h6 id="activitydiv" style="display:none;" class="text-center"></h6>
                                                <div id="activityselect">
                                                    
                                                </div>
                                                
                                            </div>
                                            <div class="col-md-2">
                                                <h6>Working Hours : <span id="wrkinghours"></span></h6>
                                            </div>
                                           
                                            <div class="col-md-2">
                                                <button style="width:50%;float:right;" type="button" class="btn btn-primary" id="back_buttons">Back</button>
                                            </div>
                                        </div>
                                        <div id="reportmuster">
                                            <table class="table table-bordered indent-table" id="musterreporttable" style="display: table;">
                                                <thead>
                                                <tr>
                                                    <th style="width: 110px;">Sl no</th>
                                                    <th style="width: 240px;">Name of Resource</th>
                                                    <th style="width: 340px;" >Trade</th>
                                                    <th style="width: 110px; display: none;" class="balhours">Balance hours</th>
                                                    <th style="width: 110px;" >Hours Worked</th>
                                                    <th style="width: 140px;">Overtime Hours</th>
                                                 </tr>
                                                <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                                <tbody id="musterreportitems" class="input_fields_wrap">

                                                  </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>


                <div class="content-wrpr" id="newtwo">
                 <div class="invoice-leased-equipment-list-wrpr data-content-list">
                               
                                <div id="raisewagerollhistory"></div>
                            </div>
                 </div>



                    </div>
                  </div>
                </div>
              </div>