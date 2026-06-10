<?php
use app\models\Projects;
?>

<div class="panel panel-default /*acco-cart*/ acco-four tab">

	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/allorders.js" type="text/javascript"></script>

	<div class="panel-heading">
      <h4 class="panel-title " id="chooseallorder">
        <a data-toggle="collapse" data-parent="#accordionindex" href="#collapseallorders">
        <span class="icon-shopping_cart"></span>Place Orders</a>
      </h4>
    </div>

    <div id="collapseallorders" class="tab-content panel-collapse cOrder-body panel-collapse collapse">

    	<!-- <div class="orders-wrpr">
    
	      <div class="order-head text-center">

	      	<php

	      		$connection = \Yii::$app->db;
    				$sql = "SELECT * FROM cart where status=0 GROUP BY Project ORDER BY cartID ASC";

						$command = $connection->createCommand($sql);
				   	$dataReader = $command->query();  
				  	$carts = $dataReader->readAll();


				  	foreach($carts as $cart){

	  				$projname=Projects::findOne($cart['Project']);

	      	?>

	        	<a href="#<php echo $projname->Project_Id; ?>" id="projectdire" class="repor projctsel<php echo $projname->Project_Id; ?> " data-id="<php echo $projname->Project_Id; ?>"><php echo $projname->Name; ?></a>
				

	        <php } ?>
	    		 
			  </div>
    	</div> -->

    	<!-- <php

    	$connection = \Yii::$app->db;
    	$sql = "SELECT * FROM cart where status=0 GROUP BY Project ORDER BY cartID ASC";

		  		$command = $connection->createCommand($sql);
			   	$dataReader = $command->query();  
			  	$carts = $dataReader->readAll();

			  	foreach($carts as $cart){

			  		$projname=Projects::findOne($cart['Project']);
			  		?>

			  		<label style="padding-left: 25px;">Project: //<php echo $projname['Name']; ?></label>
			  	 -->
			  	 
				   <input type="hidden" id="identord" >
		<ul class="nav nav-tabs text-center topsbars">
			
			<li class="frstcl"><a data-toggle="pill" href="#repord" id="prepeated"><span class="icon-shopping_cart"></span> Repeated Orders</a></li> 
			<li><a data-toggle="pill" href="#poopord" id="ppurchor"><span class="icon-shopping_cart"></span> Purchase Orders</a></li>
			<li><a data-toggle="pill" href="#wrord" id="pworko"><span class="icon-shopping_cart"></span> Work Orders</a></li>
			<li><a data-toggle="pill" href="#dirrord" id="pdirec"><span class="icon-shopping_cart"></span> Direct Work Orders</a></li>
			<li><a data-toggle="pill" href="#lesord" id="pleaso"><span class="icon-shopping_cart"></span> Lease Orders</a></li>
			<li><a data-toggle="pill" href="#desord" id="pdespto"><span class="icon-shopping_cart"></span> P&M Movement</a></li>
		</ul>

    	<!-- Tab 1 -->
    	<input type="hidden" id="cartsearch"> 
    	<form>
        <div  class="placeorder-list"> 
        	<div class="preloader"  style="display: none;" align="center">
				<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
			</div>

        	   


                		<!-- <php $this->recart($projname['Project_Id']); ?> -->

	                    <!-- <table class="table table-bordered">
	                        <thead class="thdorders">
	                            <tr>
	                                <th></th>
	                                <th>Vendor Name</th>
	                                <th>Amount</th>
	                                <th colspan="2"></th>
	                            </tr>
	                       
	                        </thead>
	                        <tbody id="recartitems">

	                        </tbody>
	                    </table> -->
</div></form>

	
	<div id="repurchaseorder"></div>
	
	
	<div id="purchaseorder"></div>
	
	
	<div id="despporders"></div>
	
	
	<div id="wrkorders"></div>
	
	
	<div id="directwrkorders"></div>
	
	
	<div id="lsorders"></div>
	

        <!-- Tab 1 End -->

        <!-- Tab 2 -->

        
	                    
	                

        <!-- Tab 2 End -->

        <!-- Tab 3 -->

        
	                    <!-- <table class="table table-bordered">
	                        <thead class="thdorders">
	                            <tr>
	                                <th></th>
	                                <th>Vendor Name</th>
	                                <th>Amount</th>
	                                <th colspan="2"></th>
	                            </tr>
	                       
	                        </thead>
	                        <tbody id="wrkorderitems">

	                        </tbody>
	                    </table> -->
                	


        <!-- Tab 3 End -->

        <!-- Tab 4 -->
        	
	                    <!-- <table class="table table-bordered">
	                        <thead class="thdorders">
	                            <tr>
	                                <th></th>
	                                <th>Vendor Name</th>
	                                <th>Amount</th>
	                                <th colspan="2"></th>
	                            </tr>
	                       
	                        </thead>
	                        <tbody id="directwrkorderitems">

	                        </tbody>
	                    </table> -->
                	

        <!-- Tab 4 End -->

        <!-- Tab 5 --> 

        
	                    <!-- <table class="table table-bordered">
	                        <thead class="thdorders">
	                            <tr>
	                                <th></th>
	                                <th>Vendor Name</th>
	                                <th>Amount</th>
	                                <th colspan="2"></th>
	                            </tr>
	                       
	                        </thead>
	                        <tbody id="leaseorderitems">

	                        </tbody>
	                    </table> -->
                	

        <!-- Tab 5 End --> 

        <!-- Tab 6 -->

        
	                    <!-- <table class="table table-bordered">
	                        <thead class="thdorders">
	                            <tr>
	                                <th></th>
	                                <th>Vendor Name</th>
	                                <th>Amount</th>
	                                <th colspan="2"></th>
	                            </tr>
	                       
	                        </thead>
	                        <tbody id="desporderitems">

	                        </tbody>
	                    </table> -->
                	

        <!-- Tab 6 End-->

     <!--    <php
    }
    ?> -->




    </div>



<script>
	$('.repor').click(function () {

		var id = $(this).attr('data-id');

		
		$('.repor').removeClass('active');
		$(this).addClass('active');
		
		$("html, body").animate({ scrollTop: $('#'+id).offset().top }, 1000);

	});
	/*$('#purpor').click(function () {
		$('#purpor').addClass('active');
		$('#repor').removeClass('active');
		$('#wrkpor').removeClass('active');
		$('#dwpor').removeClass('active');
		$('#leordd').removeClass('active');
		$('#despor').removeClass('active');

		$("html, body").animate({ scrollTop: $('#purporr').offset().top }, 1000);

	});
	$('#wrkpor').click(function () {
		$('#purpor').removeClass('active');
		$('#repor').removeClass('active');
		$('#wrkpor').addClass('active');
		$('#dwpor').removeClass('active');
		$('#leordd').removeClass('active');
		$('#despor').removeClass('active');

		$("html, body").animate({ scrollTop: $('#wrkporr').offset().top }, 1000);

	});
	$('#dwpor').click(function () {
		$('#purpor').removeClass('active');
		$('#repor').removeClass('active');
		$('#wrkpor').removeClass('active');
		$('#dwpor').addClass('active');
		$('#leordd').removeClass('active');
		$('#despor').removeClass('active');

		$("html, body").animate({ scrollTop: $('#dwporr').offset().top }, 1000);

	});
	
	$('#leordd').click(function () {
		$('#purpor').removeClass('active');
		$('#repor').removeClass('active');
		$('#wrkpor').removeClass('active');
		$('#dwpor').removeClass('active');
		$('#leordd').addClass('active');
		$('#despor').removeClass('active');

		$("html, body").animate({ scrollTop: $('#leord').offset().top }, 1000);

	});
	$('#despor').click(function () {
		$('#purpor').removeClass('active');
		$('#repor').removeClass('active');
		$('#wrkpor').removeClass('active');
		$('#dwpor').removeClass('active');
		$('#leordd').removeClass('active');
		$('#despor').addClass('active');

		$("html, body").animate({ scrollTop: $('#desporr').offset().top }, 1000);

	});*/
	$(document).ready(function() {
   history.replaceState(null, null, ' ');
})
	
</script>
</div>