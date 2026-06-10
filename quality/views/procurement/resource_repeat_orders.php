<?php
use app\models\Projects;
use app\models\Resources;
use app\models\Resourcetype;


$material_resources = '';
$plant_equipment_resources = '';
$direct_labour_resources = '';
$sub_cont_resources = '';

if($activities){
		foreach ($activities as $activity) {
				$res = Resources::findOne($activity->resource_Id);
				$resType = Resourcetype::findOne($res->ResourceType_Id);

				//----------Materials / Major Consumables / Purchased Inputs----------------
				if($res->ResourceType_Id == 16 || $res->ResourceType_Id == 20 || $res->ResourceType_Id == 21){
						$material_resources .= '<div class="col-md-12" style="padding:8px 0px; border-bottom: 1px solid #eee;">
	                        <div class="col-md-4" style="padding-top:5px; ">
	                            '.$res->Name.'
	                        </div>
	                        <div class="col-md-1" style="text-align:center; padding-top:5px;">
	                            '.$res->Unit.'
	                        </div>
	                        <div class="col-md-3" style="text-align:center; padding-top:5px;">
	                            Vendor
	                        </div>
	                        <div class="col-md-1" style="text-align:center;">
	                            100
	                        </div>
	                        <div class="col-md-3" style="text-align:center; padding-left:50px; padding-top:5px;">
                              <button type="button" class="btn btn-primary btn-report saveresourcerpt" id="saveresourcerpt" data-id="" data-res-type="Material" value="" title="Save">Purchase Order</button>
	                        </div>
	                    </div>';
        }
		}
}


?>

<div class="panel panel-default  acco-two tab">

	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/allorders.js" type="text/javascript"></script>

	<div class="panel-heading">
      <h4 class="panel-title " id="chooseallorder">
        <a data-toggle="collapse" data-parent="#accordionindex" href="#collapserepeatorders">
        <span class="icon-shopping_cart"></span>Repeat Orders</a>
      </h4>
    </div>

    <div id="collapserepeatorders" class="tab-content panel-collapse cOrder-body panel-collapse collapse">

    	<div style=" padding: 20px;">

								<div class="row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-10" >
                    	<div class="row prg-reprt-heads progresshead" style="height:50px; font-size: 12px; border-radius: 10px 10px 0 0;">
		                    <div class="col-md-12" style="padding:0px;">
		                        <div class="col-md-4" >
		                            <label style="margin-bottom:0px;">Resource</label>
		                        </div>
		                        <div class="col-md-1" style="text-align:center;">
		                            <label style="margin-bottom:0px;">Unit</label>
		                        </div>
		                        <div class="col-md-3"  style="text-align:center;">
		                            <label style="margin-bottom:0px; padding-left:15px;">Vendor</label>
		                        </div>
		                        <div class="col-md-1" style="text-align:center;">
		                            <label style="margin-bottom:0px;" title="">Amount</label>
		                        </div>
		                        <div class="col-md-3" style="text-align:right; padding-right: 30px;">
		                        </div>
		                    </div>
		                </div>

		                <div class="row "  style="">
		                	<?php echo $material_resources ?>
                    </div>

                          <div class="row text-center" style="padding:10px 0;background:#ecedef;border-radius: 0px 0px 10px 10px;">
                          </div>

                    </div>
                    <div class="col-md-1">&nbsp;</div>
								</div>
		    	</div>


    </div>



<script>

</script>
</div>