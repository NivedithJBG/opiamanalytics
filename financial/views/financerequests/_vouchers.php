
<div class="panel panel-default vouchers-tab acco-four tab">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/_vouchers.js" type="text/javascript"></script>
	<!-- <input type="radio" id="rd5" name="rd"> -->

	<div class="panel-heading" >
	  <h4 class="panel-title">
		<a data-toggle="collapse" data-parent="#accordionfin" href="#collapsevoucher">
		<span class="icon-tag2"></span>Vouchers</a>
	  </h4>
	</div>
					
					
	<div id="collapsevoucher" class="tab-content panel-collapse collapse">
	  	<div class="panel-body cOrder-body">		  
			<div class="search-and-content-wrpr">
				
				<div class="content-wrpr">

					<!-- Genarate Voucher form -->
					<div class="generate-voucher-form">
						<div class="preloader" id="fin-preloader-GenarateVoucher" style="display: none;" align="center">
							<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
						</div>
						<div id="generate-voucher-table"></div>
					</div>
					
					
					<!-- Genarate Voucher form end -->
						
					
					<ul class="nav nav-tabs">
					  <li class="active"><a data-toggle="pill" href="#cash-payment" id="cashvoucher"><span class="icon-banknote"></span> Cash Payment</a></li>
					  <li><a data-toggle="pill" href="#cash-receipt" id="cashreceipt"><span class="icon-file3"></span> Cash Receipt</a></li>
					  <li><a data-toggle="pill" href="#bank-payment" id="bankvoucher"><span class="icon-library1"></span> Bank Payment</a></li>
					  <li><a data-toggle="pill" href="#bank-receipt" id="bankreceipt"><span class="icon-receipt"></span> Bank Receipt</a></li>
					  <li><a data-toggle="pill" href="#journal" id="vjournal"><span class="icon-file-text2"></span> Journal</a></li>
					</ul>
					
					<div class="nav-tab-content">

						<div class="tab-content bootstrap-tabs">
						  <div id="cash-payment" class="tab-pane fade in active">
								<!-- list starts -->
								<div class="vouchers-list">
									<div class="preloader" id="fin-preloader-vtabcashpayment" style="display: none;" align="center">
										<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
									</div>
									<div id="cash-paymenttable">
									</div>
									<!-- list ends -->
								</div>
						  </div>
						  <div id="cash-receipt" class="tab-pane fade">
						  
							<!-- list starts -->
							<div class="vouchers-list">
								<div class="preloader" id="fin-preloader-vtabcashreceipt" style="display: none;" align="center">
									<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
								</div>
								<div id="cash-receipttable">
								</div>
								<!-- list ends -->
							</div>
							
						  </div>
						  <div id="bank-payment" class="tab-pane fade">
							
							<!-- list starts -->
							<div class="vouchers-list">
								<div class="preloader" id="fin-preloader-vtabbankpayment" style="display: none;" align="center">
									<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
								</div>
								<div id="bank-paymenttable">
								</div>
								<!-- list ends -->
							</div>
							
						  </div>
						  <div id="bank-receipt" class="tab-pane fade">
							
							<!-- list starts -->
							<div class="vouchers-list">
								<div class="preloader" id="fin-preloader-vtabbankreceipt" style="display: none;" align="center">
									<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
								</div>
								<div id="bank-receipttable">
								</div>
								<!-- list ends -->
							</div>
							
						  </div>
						  <div id="journal" class="tab-pane fade">
								<!-- list starts -->
							<div class="vouchers-list">
								<div class="preloader" id="fin-preloader-vtabjournal" style="display: none;" align="center">
									<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
								</div>
								<div id="journaltable">
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