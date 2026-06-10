<div class="panel panel-default raise-job-card-tab tab tab-wrapper acco-one">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/jobcard.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#jobcarddate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
        $('#startdate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
        $('#enddate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var x = 2; //initlal text box count
        $(document).on( "click","#addmorerows", function(e){
            e.preventDefault();

            if(x < max_fields){
                $('#jobcardrow').before('<tr><td>'+x+'</td>' +
                    '<td><select id="JobcardResource'+x+'" data-id="'+x+'" name="Resource[]" class="form-control JobcardResource">' +
                    '<option value="0">Select Resource</option></select><span class="error"></span></td>' +
                    '<td><input type="text" class="form-control" readonly name="Unit[]" id="Unit'+x+'" data-id="'+x+'" placeholder="Unit"></td>' +
                    //'<td><span id="EstQuantity'+x+'"></span><input type="hidden" id="EstQty'+x+'" name="EstQuantity[]" value=""></td>' +
                    //'<td id="exstresqty'+x+'"></td>' +
                    //'<td id="recresqty'+x+'"></td>' +
                    //'<td id="issuedresqty'+x+'"></td>' +
                    //'<td id="stockresqty'+x+'"></td>' +
                   // '<td><span id="remquantity'+x+'"></span>' +
                    //'<input type="hidden" id="remqty'+x+'" name="RemQuantity[]"></td>' +
                    '<td><input type="text" class="form-control Quantity" name="PropQuantity[]" id="PropQuantity'+x+'" data-id="'+x+'" placeholder="Quantity" autocomplete="off">' +
                    '<span class="error" style="display: none;"></span></td>' +
                    '<td><a href="javascript:void(0)" class="remove_field">Remove</a></td>' +
                    '</tr>');
                var activityid=$('#jobcardactivity').val();
                $.ajax({
                    type: 'POST',
                    url: '../jobcard/getresources',
                    dataType: "json",
                    data: {activityid:activityid},
                    success: function(data){
                        if(data.error=='No')
                        {
                            var id=(x - 1);
                            $('#JobcardResource'+id+'').html(data.result);
                        }
                    }
                });
                x++;

            }
        });
        $(document).on( "click","#jobcard_wrap .remove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        })
    });

</script>
			  <!-- <input type="radio" class="raise-job-card" id="rd5" name="rd"> -->
				<div class="panel-heading" >
				  <h4 class="panel-title" id="raise-job-card">
					<a  data-toggle="collapse" data-parent="#accordionindex" href="#collapsejobcard">
					<span class="icon-file3"></span>Raise Job Cards</a>
				  </h4>
				</div>
				<div id="collapsejobcard" class="tab-content cOrder-body panel-collapse collapse">
				  <div class="panel-body ">
					<div class="search-and-content-wrpr jobhead">
						<div class="search-and-actions-wrpr row ">
							<div class="content-search-wrpr col-md-7 col-sm-7 text-left">
								<h6 class="projectname" id="projectname-jobcard"></h6>
							</div>
							<div class="col-md-3 col-sm-3"></div>
							<div class="content-action-wrpr col-md-2 col-sm-2">
								<a href="#" class="btn btn-primary addForm" id="addjobcard" style="display: none;"><span class="icon-add" ></span> Raise Job Card</a>
                                <a href="#" class="btn btn-primary " id="listappjobcard"><span class="icon-th-list" style="display: none;"></span> List</a>
                                <a href="#" class="btn btn-primary back" id="back" title="Back" >Back</a>
									
							</div>
						</div>
						<div class="content-wrpr">
							<!-- form starts here -->
								<div class="add-form raise-bill-form  row">
                                    <div class="preloader" style="display: none;" align="center">
                                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                    </div>
                                    <form id="jobcardform">
                                    <div class="content-history row">
                                        
                                        <div class="col-md-6 raisedate" id="raisedate"></div>
                                        <div class="col-md-6 text-right historyhead">
                                        <button type="button" class="btn btn-primary history" id="history" title="History"><span ></span> History</button></div>
                                    </div>


                                    





                                    
                                    <div id="createnew-jobcard"> </div></form>
								</div>
							<!-- form ends here -->

							<!-- edit form starts here -->
							<div class="edit-form raise-bill-form  row">
                                <div class="preloader"style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="view-jobcard"> </div>
							</div>
							
							<!-- edit form ends here -->

							<!-- list start here -->
							<div class="invoice-leased-equipment-list-wrpr data-content-list">
                                <div class="preloader"style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="jobcarditems"></div>
							</div>
							<!-- list end here -->	
						</div>
					</div>
				  
				  </div>
				</div>
			  </div>