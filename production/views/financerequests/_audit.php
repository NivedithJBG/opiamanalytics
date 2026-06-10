
<div class="panel panel-default vouchers-tab acco-ten tab">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/_audit.js" type="text/javascript"></script>
	
	<!-- <script type="text/javascript" src="/opiamnew/web/jsnew/jquery.min.js"></script> -->
	<!-- <input type="radio" id="rd5" name="rd" class="audit-tab"> -->

	<div class="panel-heading" >
	  <h4 class="panel-title" id="audit-tab">
		<a data-toggle="collapse" data-parent="#accordionfin" href="#collapseaudit">
		<span class="icon-playlist_add_check"></span>Audit</a>
	  </h4>
	</div>

	<div id="collapseaudit" class="tab-content panel-collapse collapse">
	  	<div class="panel-body cOrder-body">		  
			<div class="search-and-content-wrpr">
				
				<div class="content-wrpr">	
					
					<ul class="nav nav-tabs">
					  <li class="active"><a data-toggle="pill" href="#cash-voucher" id="cash-voucher"><span class="icon-banknote"></span> Cash Vouchers</a></li>
					  <li><a data-toggle="pill" href="#bank-voucher" id="bank-voucher"><span class="icon-library1"></span> Bank Vouchers</a></li>
					  <li><a data-toggle="pill" href="#journal-voucher" id="journal-voucher"><span class="icon-file-text2"></span> Journal Vouchers</a></li>
					  <li><a data-toggle="pill" href="#audit-history" id="audit-history"><span class="icon-history"></span>History</a></li>
					</ul>
					
					<div class="nav-tab-content">

						<!--<div class="tab-content bootstrap-tabs">-->
						<div>
							
						  	<div id="cash-voucher" class="cash-voucher tab-pane fade in active">
								<!-- list starts -->
								<div class="vouchers-list">
									<div class="preloader" id="tabcashvoucher" style="display: none;" align="center">
										<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
									</div>
									<div id="cash-vouchertable">

									</div>
									<!-- list ends -->
								</div>
						  	</div>
						  

						  	<div id="bank-voucher" class="bank-voucher tab-pane fade">
								<!-- list starts -->
								<div class="vouchers-list">
									<div class="preloader" id="tabbankvoucher" style="display: none;" align="center">
										<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
									</div>
									<div id="bank-vouchertable">

									</div>
									<!-- list ends -->
								</div>
						  	</div>

						  	<div id="journal-voucher" class="journal-voucher tab-pane fade">
								<!-- list starts -->
								<div class="vouchers-list">
									<div class="preloader" id="tabjournalvoucher" style="display: none;" align="center">
										<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
									</div>
									<div id="journal-vouchertable">

									</div>
									<!-- list ends -->
								</div>
						  	</div>

						  	<div id="audit-history" class="audit-history tab-pane fade">
								<!-- list starts -->
								<div class="vouchers-list">
									<div class="preloader" id="tabaudithistory" style="display: none;" align="center">
										<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
									</div>

									<div class="search-and-actions-wrpr row">
					                    <div class="content-search-wrpr col-md-12 col-sm-12">
					                    	<div class="col-md-4">
						                        <select class="form-control" id="vouchertype" name="journalproject">
						                            <option value="">Select Voucher Type</option>
						                            <option value="1">Cash Voucher</option>
						                            <option value="2">Bank Voucher</option>
						                            <option value="3">Journal Voucher</option>
						                        </select>
						                        <span class='error'></span>
						                    </div>
						                    <div class="col-md-3">
					                        	<input class="form-control" type="date" id="voucherfromdate" name="fromdate" value="" /> 
					                        </div>
					                        <div class="col-md-3">
					                        	<input class="form-control" type="date" id="vouchertodate" name="todate" value=""/>
					                        </div>
					                        <div class="col-md-2">
					                        	<button id="auditvouchersearch" class="btn btn-primary auditvouchersearch" type="button"><span class="icon-search5"></span></button>
					                        </div>
					                    </div>
					                </div>

									<div id="audit-historytable">

									</div>
									<!-- list ends -->
								</div>
						  	</div>
						  
						</div>
					</div>

				</div>
			</div> 
	  	</div>
	</div>

</div>