<div class="panel panel-default log-equipment-usage-log-book-tab tab tab-wrapper acco-seven">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/reportlog.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $('#logdate0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
                $('#electlogdate0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
                var max_fields      = 10; //maximum input boxes allowed
                var wrapper         = $(".input_fields_wrap"); //Fields wrapper
                var x = 2; //initlal text box count

                var id=$(this).attr('data-id');
                $(document).on( "click", "#addmores", function(e){
                    e.preventDefault();
                    if(x < max_fields){
                        $('#enginerows').before('<tr>' +

                            '<td><input type="text" class="form-control startreading" placeholder="Start Reading" name="startreading[]" id="startreading'+x+'" data-id="'+x+'" autocomplete="off">' +
                            '<span class="error" style="color:red;font-size:12px;"></span> </td>' +
                            
                            '<td><input type="text" class="form-control endreading" placeholder="End Reading" name="endreading[]" id="endreading'+x+'" data-id="'+x+'" autocomplete="off">' +
                            '<span class="error" style="color:red;font-size:12px;"></span> </td>' +

                            '<td><select id="logbookactlist'+x+'" name="logactivity[]" class="form-control logactivity" data-id="'+x+'">'+

                            '<option value="none">Select Activity</option></select>' +

                            '<span class="error" style="color:red;font-size:12px;"></span> </td>' +

                            '<td class="text-right"><span id="nreading'+x+'"></span><input type="hidden" id="nreadingval'+x+'" class="nreadingval"></td>'+
                            '<td><input class="form-control hourss" id="hours'+x+'" data-id="'+x+'" name="hours[]"  type="text"></td>'+

                            '<td><input type="text" class="form-control trips" placeholder="No. of Trips" name="trips[]" id="trips'+x+'" data-id="'+x+'" autocomplete="off">' +
                            '<span class="error" style="color:red;font-size:12px;"></span> </td>' +

                            '<td class="icon-groups"><a href="javascript:void(0)" class="btn btn-primary icon-remove remove_field2"></a></td>' +
                            '</tr>');  
                        var unit=$('#eqpunit').val();
                        console.log(unit)
                        if (unit==1)
                        {
                            $('.startreading').prop('readonly', false);
                            $('.endreading').prop('readonly', false);
                            $('.trips').prop('readonly', true);
                        }
                        else if (unit==2){
                            $('.startreading').prop('readonly', false);
                            $('.endreading').prop('readonly', false);
                            $('.trips').prop('readonly', true);
                        }
                        else if (unit==3){
                            $('.startreading').prop('readonly', true);
                            $('.endreading').prop('readonly', true);
                            $('.trips').prop('readonly', false);
                        }
                    
                        $.ajax({

                            type: 'POST',

                            url: '../projects/logactivity',

                            dataType: "json",

                            data: {projectid:$('#projlogbook').val(),resourceid:$('#equipment').val()}, 
                    
                            success: function(data){

                                if(data.error=='No')

                                {
                                    var id=(x - 1);
                                            
                                    $('#logbookactlist'+id+'').html(data.result);    
                                                
                                }
                            }
                        });
                        x++;
                    }
                });

                $(document).on("click",".remove_field2", function(e){
                    //alert('ssss')
                    e.preventDefault();
                    $(this).parent('td').parent('tr').remove();
                    x--;
                })
            });
        </script>
			  <!-- <input type="radio" class="LogEquipmentUsage" id="rd5" name="rd"> -->
				<div class="panel-heading" >
				  <h4 class="panel-title" id="LogEquipmentUsage">
					<a data-toggle="collapse" data-parent="#accordionoper" href="#collapselogbook">
					<span class="icon-book2"></span>Log Equipment Usage</a>
				  </h4>
				</div>
				<div id="collapselogbook" class="tab-content cOrder-body panel-collapse collapse">
				  <div class="panel-body ">				  
					<div class="search-and-content-wrpr">								
						<div class="search-and-actions-wrpr row">
							<div class="content-search-wrpr col-md-7 col-sm-7 text-left machinery-list-search-cntnr" id="Log-head-One">		
							</div>
							<div class="content-search-wrpr col-md-7 col-sm-7 text-left log-list-search-cntnr" id="Log-head-Two">							
							</div>
							<div class="col-md-3 col-sm-3"></div>
							<div class="content-action-wrpr col-md-2 col-sm-2">								
								<a href="#" class="btn btn-primary log-list-btn " title="List Log" id="listlog"><span class="icon-book"></span> Log</a>
                                <a href="#" class="btn btn-primary close-log-list-btn"><span class="icon-close"></span> Close Log</a>
                                <a href="#" type="hidden" id="LogEquipmenthead"></a>	
                                <a href="#" type="hidden" id="listdespatchorders"></a>		
                                <input type="hidden" id="projelectlogbook" value="">						
							</div>
						</div>
						<div class="content-wrpr">
							<!-- form starts here -->
								<div class="add-form raise-bill-form  row">
                                    <div class="preloader" style="display: none;" align="center">
                                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                    </div>
                                    <div id="Log-Equipment-add-form"></div>	
								</div>
							<!-- form ends here -->

							<!-- edit form starts here -->
							<div class="edit-form raise-bill-form  row">
                                <div id="Log-Equipment-view-form"> </div>
							</div>
							<!-- edit form ends here -->

                            <!-- list start here -->
							<div class="machinery-list-wrpr data-content-list">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="Log-Equipment-list"></div>								
							</div>
							
							
							<div class="machinery-log-list-wrpr data-content-list">
							    <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="Log-Equipment-list-log"></div>		
							</div>
							<!-- list end here -->	
						</div>
					</div>
				  
				  </div>
				</div>
			  </div>