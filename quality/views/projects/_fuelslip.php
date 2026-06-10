<div class="panel panel-default issue-fuel-slips-tab tab tab-wrapper acco-ten">
<!-- <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/fuelslip.js" type="text/javascript"></script>

<script type="text/javascript">
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

</script> -->

  <!-- <input type="radio" class="issueslips" id="rd5" name="rd"> -->		
	<div class="panel-heading" >
	  <h4 class="panel-title" id="fuelslips">
		<a data-toggle="collapse" data-parent="#accordionoper" href="#collapsefuelissue">
		<span class="icon-document-text"></span>Fuel Issue Slips</a>
	  </h4>
	</div>

	<div id="collapsefuelissue" class="tab-content cOrder-body panel-collapse collapse">
	  <div class="panel-body ">
		<div class="search-and-content-wrpr">
			<div class="search-and-actions-wrpr row">
                <div class="content-search-wrpr col-md-7 col-sm-7 text-left">
                        <h6><span id="fuelprodropname"></span></h6>&nbsp;&nbsp;&nbsp;
                        <input type="text" placeholder="Resource" id="fuelissresval" class="form-control">
                        <button id="fuellistissuesearch" class="btn btn-primary"  type="button"><span class="icon-search5"></span></button>
                </div>
                <div class="col-md-3 col-sm-3"></div>
                <div class="content-action-wrpr col-md-2 col-sm-2">
                    <a href="javascript:void(0);" class="btn btn-primary addForm " id="fuelissueslipsaddForm" title="Raise Issue Slips"><span class="icon-add"></span> Raise Fuel Issue Slip</a>
                    <a href="#" type="hidden" id="fuellistissueslips"></a>	
                </div>
			</div>
			<div class="content-wrpr">
				<!-- form starts here -->
					<div class="add-form raise-bill-form  row">
					    <div class="preloader" style="display: none;" align="center">
                            <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                        </div>
                        <div id="fuelissueslipsadd"></div>

					</div>
				<!-- form ends here -->

				<!-- edit form starts here -->
				<div class="edit-form raise-bill-form  row">
                    <div class="preloader"style="display: none;" align="center">
                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                    </div>
                    <div id="fuelissueslipsedit"> </div>
				</div>
				<!-- edit form ends here -->

				<!-- list start here -->
				<div class="issue-material-slips-list-wrpr data-content-list">
				    <div class="preloader"style="display: none;" align="center">
                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                    </div>
                    <div id="fuelissueslipsitems"></div>							
				</div>			
				<!-- list end here -->
			</div>
		</div>			  
	  </div>
	</div>
  </div>