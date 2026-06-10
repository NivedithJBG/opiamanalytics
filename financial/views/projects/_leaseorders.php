<div class="panel panel-default invoice-leased-equipment-tab tab tab-wrapper acco-nine">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/leaseorders.js" type="text/javascript"></script>
			  <!-- <input type="radio" class="viewleaseorders" id="rd5" name="rd"> -->
				<div class="panel-heading" >
				  <h4 class="panel-title" id=viewleaseordersbill>
					<a data-toggle="collapse" data-parent="#accordionoper" href="#collapseleased">
					<span class="icon-file3"></span>Lease Bills</a>
				  </h4>
				</div>
				<div id="collapseleased" class="tab-content cOrder-body panel-collapse collapse">
				  <div class="panel-body ">
					<div class="search-and-content-wrpr">
						<div class="search-and-actions-wrpr row">
							<div class="content-search-wrpr col-md-7 col-sm-7 text-left">
								<h6 class="projectname" id="projectname-ILE" style="display: none;"></h6>
							</div>
							<div class="col-md-3 col-sm-3"></div>
							<div class="content-action-wrpr col-md-2 col-sm-2">	
								<a href="#" class="btn btn-primary order-history-btn " id="leaseorderhistory" title="Leasorder History"><span class="icon-history"></span> Bill History</a>
                                <a href="#" class="btn btn-primary close-order-history-btn lbillhis"><span class="icon-close"></span> Close History</a>
                                <a href="#" type="hidden" id="leaseordersearch"></a>	
							</div>
						</div>
						<div class="content-wrpr" style="overflow: hidden;">
							<div class="text-center row wrkbillhead" style="display:none" id="leashd" style="margin-left: 15px;margin-right: 15px;margin-bottom: 20px;"><label style="font-size: 15px;">Lease order Bills</label></div>
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
                            <div class="text-center row wrkbillhead" id="leashd" style="margin-left: 15px;margin-right: 15px;margin-bottom: 20px;"><label style="font-size: 15px;">Bill History</label></div>
                          	<div id="leaseorderbillitemshistory"></div>
                          </div>
                           
                          </div>
                          
							
							
						</div>
					</div>
				  </div>
				</div>
			  </div>