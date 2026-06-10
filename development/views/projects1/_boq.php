<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/boq.js" type="text/javascript"></script>
<script type="text/javascript">    
$(document).ready(function() {
            var max_fields = 10; //maximum input boxes allowed 
            var wrapper         = $(".input_fields_wrap"); //Fields wrapper       
            var add_button      = $(".add_field_button"); //Add button ID        
            var x = 2; //initlal text box count        
            $(add_button).click(function(e){            
                e.preventDefault();            
                if(x < max_fields){
                                    $('#boqrow').before('<tr><td class="small75"><input type="text" class="form-control Slno" name="slno[]" id="slno'+x+'" data-id="'+x+'" placeholder="Sl No" ></td>' +
                                    '<td><select class="form-control Activity" id="Activity'+x+'" name="Activity[]" data-id="'+x+'"></select><span class="error" style="float:left;display: none;"></span></td>' +
								    '<td><input type="text" class="form-control Item" name="Item[]" id="Item'+x+'" data-id="'+x+'" placeholder="Item" ><span class="error" style="display: none;"></span></td>' +
                                    '<td><input type="text" class="form-control Unit" name="Unit[]" id="Unit'+x+'" data-id="'+x+'" placeholder="Unit" ><span class="error" style="display: none;"></span></td>' +
                                    '<td><input type="text" class="form-control Quantity" name="Quantity[]" id="Quantity'+x+'" data-id="'+x+'" placeholder="Quantity" ><span class="error" style="display: none;"></span></td>' +
                                    '<td><input type="text" class="form-control Rate" name="Rate[]" id="Rate'+x+'" data-id="'+x+'" placeholder="Rate" ><span class="error" style="display: none;"></span></td>' +
                                    '<td><a href="javascript:void(0)" class="remove_field">Remove</a></td>' +
                                    '</tr>');
					var wbs=$('#wbs').val();
					$.ajax({

						type: 'POST',

						url: '../ProjectPricing/ListActivities',

						dataType: "json",

						data: {wbs:wbs},

						success: function(data){

							if(data.error=='No')

							{
                                var id=(x - 1);
								$('#Activity'+id+'').html(data.result);

							}

						}

					});
                    x++;
                                                                                        
                }        
            });
           $(wrapper).on("click",".remove_field", function(e){
                e.preventDefault();
                $(this).parent('td').parent('tr').remove();
                x--;
            })    
});
</script>
<h2 class="acc_trigger" id="boq"><a href="#BOQ">3. BOQ</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3" style="text-align: left;">
                <h4 id="boqprojectname"></h4>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger" id="listboq"><span class="glyphicon glyphicon-list-alt"></span>List BOQ</button>
                </div>
                <div class="col-md-2">
                    <!--<button type="button" class="btn btn-success"  id="addboq">
                    <span class="glyphicon glyphicon-plus-sign"></span>Add BOQ</button>-->
                </div>
                <div class="col-md-5">
                    <span id="printbtn"></span>
                    <span id="importbtn"></span>
                    <span id="clearboqbtn"></span>
                </div>
            </div>
            <div id="boqlistsection">
                <div class="row show-grid">
                    <table class="table table-bordered" id="boqtable" style="display: table; overflow: hidden;">
                        <thead>
                        <tr>
                            <th>WBS</th>
                            <!--<th>Activity</th>-->
                            <th>Item</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th></th>
                        </tr>
                        <tr class="preloader" style="display: none;">
                            <td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td>
                        </tr>
                        </thead>
                        <tbody id="boqitems">
                        </tbody>
                    </table>
                </div>
            </div>
            <form id="boqform">

                <div id="boqaddsection" style="display: none">
                    <div class="row show-grid">                        
                        <div class="col-md-3">                            
                            <select class="form-control" id="wbs" name="workgroup">
                                <option value="none">Select WBS</option>
                            </select>                            
                            <span class="error" style="float:left;display: none;"></span>                        
                        </div>                        
                    <!--<div class="col-md-3">                            
                    <select class="form-control" id="itemofwork" name="itemofwork"><option value="none">Select IOW</option></select>                            
                    <span class="error" style="float:left;display: none;"></span>
                    </div>-->
                    </div>                    
                    <table class="table table-bordered" id="boqaddtable" style="display: table;">
                        <thead>                        
                            <tr>                            
                            <th>SL No.</th>      
							<th>Activity</th> 
                            <th>Item</th>                            
                            <th >Unit</th>                            
                            <th >Quantity</th>                            
                            <th >Rate</th>                            
                            <th ></th>                        
                            </tr>                        
                        </thead>                        
                        <tbody class="input_fields_wrap">                        
                            <tr>                            
                                <td class="small75">
									<input type="text" class="form-control Slno" name="slno[]" id="slno1" data-id="1" placeholder="Sl No" >
								</td>       
								<td>
									<select class="form-control Activity" id="Activity1" name="Activity[]" data-id="1">
										<option value="none">Select Activity</option>
									</select>
									<span class="error" style="float:left;display: none;"></span> 
								</td>
                                <td>                                
                                    <input type="text" class="form-control Item" name="Item[]" id="Item1" data-id="1" placeholder="Item" >
                                    <span class="error" style="display: none;"></span>
                                    <input type="hidden" name="project_id" id="boqprojectid">
                                </td>                            
                                <td>                                
                                    <input type="text" class="form-control Unit" name="Unit[]" id="Unit1" data-id="1" placeholder="Unit" >                                
                                    <span class="error" style="display: none;"></span>                            
                                </td>                            
                                <td>                                
                                    <input type="text" class="form-control Quantity" name="Quantity[]" id="Quantity1" data-id="1" placeholder="Quantity" >
                                    <span class="error" style="display: none;"></span>
                                </td>                            
                                <td>                                
                                    <input type="text" class="form-control Rate" name="Rate[]" id="Rate1" data-id="1" placeholder="Rate" >                                
                                    <span class="error" style="display: none;"></span>
                                </td>                            
                                <td>
                                    <input type="button"  class="btn btn-primary add_field_button " id="addmore" value="Add">
                                </td>                        
                            </tr>                        
                            <tr id="boqrow">                            
                                <td colspan="6"></td>                            
                                <td>
                                <button type="submit" class="btn btn-primary" name="saveboq" id="saveboq">Save</button>
                                </td>                        
                            </tr>                        
                        </tbody>                    
                    </table>                
                </div>            
            </form> 
            <div id="boqimportsection" style="display: none">
                <form method="POST" id="import_boq_form" enctype="multipart/form-data">
                    <div class="panel-body form-horizontal payment-form">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="Type">Select CSV File<span class="required">*</span></label>
                            <div class="col-sm-4">
                                <input type="file" name="import_boq_file" class="form-control" id="import_boq_file">
                                <input type="hidden" name="projectid" id="boq_projid">
                            </div>
                            <div class="col-sm-2 pull-left">
                                <input type="submit" name="import_boq" id="import_boq" class="btn btn-primary pull-left small75" value="Import" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-3"></div>
                            <div class="col-md-3 pull-left" id="message">
                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>       
        </div>    
    </div>
</div>