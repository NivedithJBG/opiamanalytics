<div class="panel panel-default work-order-tab tab tab-wrapper acco-six">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/workordersbill.js" type="text/javascript"></script> 
  <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/leaseorders.js" type="text/javascript"></script>

	<div class="panel-heading" >
      <h4 class="panel-title" id="viewworkordersbil">
        <a  data-toggle="collapse" data-parent="#accordionoper" href="#collapseworkbill">
        <span class="icon-file3"></span>Bills</a>
      </h4>
    </div>

    <div id="collapseworkbill" class="tab-content cOrder-body panel-collapse collapse">

    	<div class="panel-body ">
    		<div class="search-and-content-wrpr">
    			<div class="search-and-actions-wrpr row">
    				<div class="content-search-wrpr col-md-10 col-sm-10">
            <ul class="nav nav-tabs text-center">
			
              <li class="wokbbb"><a data-toggle="pill" href="#opwrbill" id="wrkbls"><span class="icon-file3"></span> Work Bills</a></li>
              <li><a data-toggle="pill" href="#oplsbill" id="lskbls"><span class="icon-file3"></span> Lease Bills</a></li>
             
          </ul>
            </div>
    				
    				<div class="content-action-wrpr col-md-2 col-sm-2">
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
    				



    				<div class="content-wrpr" style="overflow: hidden;">

    					
                          <div class="wrkbillviews row" style="margin-left: 10px;margin-right: 10px;">
                             
                            <form id="billssview" method="post"><div id="workorderbillitemsview"></div>
                            </form>
                          </div>
                        <div class="history-bill-list data-content-list">
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

            <div class="content-wrpr leasebillshwng" style="overflow: hidden;">
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
    	</div>
    </div>




</div>