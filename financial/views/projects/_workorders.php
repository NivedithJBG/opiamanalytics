<?php
use app\models\Vendors;
?>
<div class="panel panel-default work-order-tab tab tab-wrapper acco-five">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/workorders.js" type="text/javascript"></script> 
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/workordersbill.js" type="text/javascript"></script> 
  <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/leaseorders.js" type="text/javascript"></script>
    
    <!-- <input class="viewworkorders" type="radio" id="rd5" name="rd"> -->
                
                <div class="panel-heading" >
                  <h4 class="panel-title" id="viewworkorders">
                    <a  data-toggle="collapse" data-parent="#accordionoper" href="#collapsework">
                    <span class="icon-directions_run"></span>Sub Contractor</a>
                  </h4>
                </div>
                
                
                <div id="collapsework" class="tab-content cOrder-body panel-collapse collapse">
                  <div class="panel-body ">

                      <div class="container" style="display: none;">
                      <ul class="nav nav-tabs nav-justified mb-3">
                <li class="active"><a data-toggle="tab" href="#" id="woord" style="background: #F5F5F5;"><span class="icon-document-text"></span> Work Order</a></li>
                <li><a data-toggle="tab" href="#"  id="viewworkordersbil" style="background: #F5F5F5;margin: inherit;"><span class="icon-document-text"></span>Bills</a></li>
                
                </ul>
                </div>




                  <!--  <div class="tab-content"> -->

           <!-- recieve materials starts here -->

      <!--  <div id="homee" class="tab-pane fade in active"> -->




                    <div class="search-and-content-wrpr" style="display: none;">
                        <hr>
                      
                      <div class="search-and-actions-wrpr row">

                          <div class="col-md-7 col-sm-7">
                           <!--  <ul class="nav nav-tabs text-center"> -->
        
                              <!--<li class="ordss"><a data-toggle="pill" href="#poordd" id="poord"><span class="icon-document-text"></span>Purchase Orders</a></li>-->
                            <!--   <li><a data-toggle="pill" href="#woordd" id="woord"><span class="icon-document-text"></span> Work Orders</a></li> -->
                              <!--<li><a data-toggle="pill" href="#loordd" id="loord"><span class="icon-document-text"></span> Lease Orders</a></li>-->
                          
                        <!--   </ul> -->
  
                          </div>
                          <div class="content-action-wrpr col-md-2 col-sm-2"> <!--<a href="javascript:void(0);" class="btn btn-primary bill-history-btn " id="workorderhistory"><span class="icon-history" title="History"></span> Bill History</a>
                              <a href="javascript:void(0);" class="btn btn-primary close-bill-history-btn"><span class="icon-close"></span> Close Bill History</a>-->
                              <a href="#" type="hidden" id="workordersearch"></a>
                              <a href="#" type="hidden" id="posearch"></a>
                              <a href="#" type="hidden" id="leaseorderbillsearch"></a>
                          </div>
                          <div class="content-search-wrpr col-md-3 col-sm-3">
                              <select class="form-control" id="searchwovendor">
                                <option value="none">Select Vendor</option>
                                <?php
                                $vendors = Vendors::find()->where(['Status'=>0])->orderBy(['Name' => SORT_ASC])->all();
                                foreach($vendors AS $vendor) { ?>
                                    <option value="<?= $vendor->Vendor_Id ?>"><?= $vendor->Name ?></option>
                                <?php } ?>
                              </select>
                              
                          </div>
                          
                          
                              
                             
                      </div>
                      
                      
                      <div class="content-wrpr">
                          <!-- form starts here -->
                          <div class="add-form raise-bill-form  row">
                            <div class="preloader" style="display: none;" align="center">
                                <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                            </div>
                            <div id="raisebillitems"></div>
                          </div>
                          <!-- form ends here -->
                          <!--<div class="poss"  style="display: none;">-->

                            <!-- edit form starts here -->
                          <!--  <div class="edit-form raise-bill-form  row">-->
                          <!--    <div class="preloader" style="display: none;" align="center">-->
                          <!--        <img src="<//?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">-->
                          <!--    </div>-->
                          <!--    <div id="raisebillitemsview"></div>-->
                          <!--  </div>-->
                            <!-- edit form ends here -->

                            <!-- list start here -->
                          <!--  <div class="work-order-list-wrpr data-content-list">-->
                          <!--  <div class="preloader" style="display: none;" align="center">-->
                          <!--      <img src="<//?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">-->
                          <!--  </div>-->
                          <!--  <div id="poorderitems"></div>-->
                          <!--  </div>-->
                            
                            
                          <!--  <div class="bill-history-list-wrpr data-content-list">-->
                          <!--    <div class="preloader" style="display: none;" align="center">-->
                          <!--        <img src="<//?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">-->
                          <!--    </div>-->
                          <!--    <div id="poorderitemshistory"></div>-->
                          <!--  </div>-->
                                  

                          <!--</div>-->

                          <div class="workss"  style="display: none;">
                          
                            <!-- edit form starts here -->
                            <div class="edit-form raise-bill-form  row">
                              <div class="preloader" style="display: none;" align="center">
                                  <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                              </div>
                              <div id="raisebillitemsview"></div>
                            </div>
                            <!-- edit form ends here -->

                            <!-- list start here -->
                            <div class="work-order-list-wrpr data-content-list">
                            <div class="preloader" style="display: none;" align="center">
                                <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                            </div>
                            <div id="workorderitems"></div>
                            </div>
                            
                            
                            <div class="bill-history-list-wrpr data-content-list">
                              <div class="preloader" style="display: none;" align="center">
                                  <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                              </div>
                              <div id="workorderitemshistory"></div>
                            </div>
                            </div>
                          </div>

                          <!--<div class="leord"  style="display: none;">-->

                                  <!-- edit form starts here -->
                          <!--        <div class="edit-form raise-bill-form  row">-->
                          <!--                          <div class="preloader" style="display: none;" align="center">-->
                          <!--                              <img src="<//?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">-->
                          <!--                          </div>-->
                          <!--                          <div id="viewleaseorder"> </div>-->
                          <!--        </div>-->
                                  <!-- edit form ends here -->

                                  <!-- list start here -->
                          <!--        <div class="invoice-leased-equipment-list-wrpr data-content-list">-->
                          <!--            <div class="preloader" style="display: none;" align="center">-->
                          <!--                              <img src="<//?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">-->
                          <!--                          </div>-->
                          <!--                          <div id="leaseorderitems"></div>  -->
                          <!--        </div>-->
                                  
                          <!--        <div class="invoice-le-order-history-list-wrpr data-content-list">-->
                          <!--            <div class="preloader" style="display: none;" align="center">-->
                          <!--                              <img src="<//?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">-->
                          <!--                          </div>-->
                          <!--                          <div id="leaseorderhistoryitems"></div>-->
                          <!--        </div>-->
                                  <!-- list end here -->
                          <!--</div>-->
                       
                        


                    </div>

                  <!-- </div> -->





       <!-- <div id="menu11" class="tab-pane fade"> -->
        

                  <div class="search-and-content-wrpr">
                     <!--  <hr> -->
          <div class="search-and-actions-wrpr row" id="shozz">

            <div class="content-search-wrpr col-md-12 col-sm-12"  style="margin-top: -20px;position: unset;">
                 <div class="container">
                <ul class="nav nav-tabs nav-justified mb-3"  id="headerzz">
                <li class="active"><a data-toggle="tab" href="#opwrbill" id="wrkbls" style="background: #F5F5F5;"><span class="icon-file3"></span>  Work Bills</a></li>
                <li><a data-toggle="tab" href="#oplsbill" id="lskbls" style="background: #F5F5F5;margin: inherit;"><span class="icon-file3"></span>Lease Bills</a></li>
                
                </ul>
            </div>
           <!--  <ul class="nav nav-tabs text-center" id="tabshw">
      
              <li class="wokbbb"><a data-toggle="pill" href="#opwrbill" id="wrkbls"><span class="icon-file3"></span> Work Bills</a></li>
              <li><a data-toggle="pill" href="#oplsbill" id="lskbls"><span class="icon-file3"></span> Lease Bills</a></li>
             
          </ul> -->
            </div>


             <div class="content-action-wrpr col-md-9 col-sm-9">

              </div>

          
            
            <div class="content-action-wrpr col-md-12 col-sm-12">
                              <div id="wrkbillhistbutton" style="display:none;">
                                <a href="javascript:void(0);" class="btn btn-primary bill-history-btn " id="histry"><span class="icon-history" title="History"></span> Bill History</a>
                                <a href="javascript:void(0);" class="btn btn-primary close-bill-history-btn"><span class="icon-close"></span> Close Bill History</a>
                              </div>
                              <div id="lsbillhistbutton" style="display:none;">
                                <a href="#" class="btn btn-primary order-history-btn " id="leaseorderhistory" title="Leasorder History"><span class="icon-history"></span> Bill History</a>
                                <a href="#" class="btn btn-primary close-order-history-btn lbillhis"><span class="icon-close"></span> Close History</a>
                              </div>
                          </div>
          </div>
            <a href="#" type="hidden" id="workorderbillsearch"></a>
            



            <div class="content-wrpr" id="wbhis" style="overflow: hidden;">

              
                          <div class="wrkbillviews row" style="margin-left: 10px;margin-right: 10px;">
                             
                            <form id="billssview" method="post"><div id="workorderbillitemsview"></div>
                            </form>
                          </div>
                        <div class="history-bill-list data-content-list" style="display: none;">
                          <div class="text-center"><label style="font-size: 15px;">Work Bill History</label></div>
                          <div id="workorderbillitemshistory"></div>
                        </div>
                        <div class="row">
                            
                            <div id="billitemhist"></div>
                          </div>




<!-- Work bills -->
              
              <div class="work-orderbill-list-wrpr">
                
                <div class="text-center row wrkbillhead"><label style="font-size: 15px;">Work Bills</label></div>
                          <div class="preloader" style="display: none;" align="center">
                              <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                          </div>
                          <div id="workorderbills" style="padding: 15px;"></div>

                          </div>
            </div>

<!-- Lease bills -->

            <div class="content-wrpr leasebillshwng" style="overflow: hidden;display: none;">
              <div class="text-center row wrkbillhead leaebilhdshws" id="leashd" style="margin-left: 15px;margin-right: 15px;margin-bottom: 20px;"><label style="font-size: 15px;">Lease order Bills</label></div>
              <div class="add-form raise-bill-form  row">
                                    <div class="preloader" style="display: none;" align="center">
                                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                    </div>

                                    
                </div>
              
              <div id="leaseorderbillitems"></div>


              <div class="leasbillviews row" style="margin-left: 10px;margin-right: 10px;">
                             
                            <form id="lbillssview" method="post"><div id="leasebillitemsview"></div>
                            </form>
                          </div>
                          <div class="bilhis row" style="margin-left: 10px;margin-right: 10px;">

                            <div class="bilhdd">
                            <div class="text-center row wrkbillhead"  style="display:none;"  id="leashd" style="margin-left: 15px;margin-right: 15px;margin-bottom: 20px;"><label style="font-size: 15px;">Lease Bill History</label></div>
                            <div id="leaseorderbillitemshistory"></div>
                          </div>
                           
                          </div>
                          
              
              
            </div>


          <!-- </div> -->
        </div>

     <!--  </div> -->






<!-- tab closing
 -->                  <!-- </div>   -->



                  
                  </div>
                </div>
              </div>