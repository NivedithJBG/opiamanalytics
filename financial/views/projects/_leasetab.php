<div class="panel panel-default invoice-leased-equipment-tab tab tab-wrapper acco-eight">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/leasetab.js" type="text/javascript"></script>


	<div class="panel-heading" >
	  <h4 class="panel-title" id=viewleaseorders>
		<a data-toggle="collapse" data-parent="#accordionoper" href="#collapseleasebill">
		<span class="icon-file3"></span>Lease Order</a>
	  </h4>
	</div>

	<div id="collapseleasebill" class="tab-content cOrder-body panel-collapse collapse">
		<div class="panel-body ">
			<div class="search-and-content-wrpr">
				<div class="search-and-actions-wrpr row">
							<div class="content-search-wrpr col-md-7 col-sm-7 text-left">
								<h6 class="projectname" id="projectname-ILE" style="display: none;"></h6>
							</div>
							<div class="col-md-3 col-sm-3"></div>
							<div class="content-action-wrpr col-md-2 col-sm-2" style="display:none;">	
								<a href="#" class="btn btn-primary order-history-btn " id="leaseorderhistory" title="Leasorder History"><span class="icon-history"></span> Order History</a>
                                <a href="#" class="btn btn-primary close-order-history-btn"><span class="icon-close"></span> Close Order History</a>
                                <a href="#" type="hidden" id="leaseorderbillsearch"></a>	
							</div>
						</div>



						<div class="content-wrpr">
							<!-- form starts here -->
								<div class="add-form raise-bill-form  row">
                                    <div class="preloader" style="display: none;" align="center">
                                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                    </div>
                                    <div id="leaseorderadd"></div>
								</div>
							<!-- form ends here -->
							
							<!-- edit form starts here -->
							<div class="edit-form raise-bill-form  row">
                                <div class="preloader"style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="viewleaseorder"> </div>
							</div>
							<!-- edit form ends here -->

							<!-- list start here -->
							<div class="invoice-leased-equipment-list-wrpr data-content-list">
							    <div class="preloader"style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="leaseorderitems"></div>	
							</div>
							
							<div class="invoice-le-order-history-list-wrpr data-content-list">
							    <div class="preloader"style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="leaseorderhistoryitems"></div>
							</div>
							<!-- list end here -->
						</div>
			</div>
		</div>
	</div>
</div>