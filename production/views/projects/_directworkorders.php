<?php
use app\models\Projects;  
?>
<div class="panel panel-default raise-wage-roll-tab tab tab-wrapper acco-fourteen">


			  <input type="radio" class="Report-Labour-Attendance" id="rd5" name="rd">
				<div class="panel-heading" >
				  <h4 class="panel-title">
					<a  href="#">
					<span class="icon-banknote"></span>Report Labour Attendance</a>
				  </h4>
				</div>
				<div  class="tab-content cOrder-body panel-collapse ">
				  <div class="panel-body ">
				  
					<div class="search-and-content-wrpr">
					
					
						  <a href="#" type="hidden" id="receivedirectworkss"></a>
						
						<div class="content-wrpr">
							<!-- form starts here -->
                    <div class="col-md-12">

                          <form id="musterform">
                <div class="row show-grid">
                    <div class="col-md-3 row">
                        <?php
                          //  $projects = Projects::model()->findAll(array('condition'=>'Project_Delete_Status = 0'));

                             $projects=Projects::find()->where(['Project_Delete_Status' => 0])->all();
                      
                          //  print_r($projects); exit;



                            if(count($projects) > 0) {
                                ?>
                                <div class="search-and-actions-wrpr row">
                                <div class="content-search-wrpr col-md-9 col-sm-9" id="resourcesearchdiv" style="display: none;">
                                <select name="project_id" class="form-control" id="selectedprojct">
                                    <option value="">Select Project</option>
                                    <?php foreach($projects AS $project) {
                                        ?>
                                        <option value="<?= $project->Project_Id ?>"><?= $project->Name ?></option>
                                        
                                    <?php } ?>  
                                </select>



                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-3"></div>
                    <div class="col-md-2" style="text-align: left;" id="dispprojectname">
                    </div>
                </div>
                <div id="receivedirectworkorders">
                    
                        <table class="table table-bordered indent-table" id="receivedirectworktabless">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <!-- <th>Project</th> -->
                                <th>Activity Name</th>
                                <th>Vendor Name</th>
                                <th>Amount</th>
                                <th></th>
                            </tr>
                            <tr class="preloader" style="display:none;"><td colspan="6" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody  id="receivedirectworkitemsss">
                              <!--   <tr><td colspan="6" style="text-align: center">No Direct Workers Orders Foundss</td></tr> -->
                            </tbody>
                        </table>
                   
                </div>

                <div id="musteraddsection" style="display: none">
                    <div id="musterprocess" class="row show-grid">
                        
                        <div class="col-md-3">
                            <input type="text" class="form-control datepicker" name="Muster_Date" id="date0" value="<?php echo date("d-m-Y");?>">
                        </div>
                        <div class="col-md-6">
                            <h4 id="activitydiv"></h4>
                        </div>

                       
                        <div class="col-md-3">
                            <button style="width:50%;float:right;" type="button" class="btn btn-primary" id="back_button">Back</button>
                        </div>
                    </div>
                    <div id="reportmuster">
                        <table class="table table-bordered indent-table" id="musterreporttable" style="display: table;">
                            <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Name of the Employee</th>
                                <th >Trade</th>
                                 <th >Hours Worked</th>
                                <th >Overtime Hours</th>
                             </tr>
                            <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            <tbody id="musterreportitems" class="input_fields_wrap">

                              </tbody>
                        </table>
                    </div>
                </div>
            </form>

        </div>

	
						<!-- list end here -->
						</div>
					</div>
				  </div>
				</div>
			  </div>


<script>
	$(document).on('click','#musterreport',function(){
        var error=0;
        var url="<?php echo Yii::$app->request->baseUrl; ?>/projects/report"; 
        /*if($('#musteractivity').val()=='none')
        {
            error=1;
        }*/

        $('.dwoworker').each(function(){
             if($(this).val()=='')
             {
                error=1;
             }
         });
        $('.dwoworkedhrs').each(function(){
            //alert($(this).val())

            if($(this).val()=='')
            {
                error=1;
            }

        });
        var numdays=$('#noofdays').val();
        var repdays=$('#repdays').val();
        if (parseInt(repdays) == parseInt(numdays)){
            //alert('Cannot report more than the order days');
            //error=1;
        }


         //var count=0;
        $('.dwoworkedhrs').each(function(){
            var id=$(this).attr('data-id');
            var workinghrs=$('#workinghrs'+id).val()*1;  

          //$('.overtime').each(function(){
            var id=$(this).attr('data-id');
            var totaldays=$('#overtime'+id).val()*1;  
           
            //alert (totaldays);


            //var totaldays= workinghrs + totaldays;
            var totaldays=  totaldays;

            var final= totaldays + totaldays;
       // });

            var id=$(this).attr('data-id');
            var tt=$('#totaldays'+id).val()*1;  

           // alert(tt);
            if(final == tt)
            {

                //error==0
               //  $('#workedhrs'+id).next('span').html('Hours worked not be greater than '+ workinghrs).show('slow');
               // count++;
            }
            else if(final <= tt){
               // 
            }else {
                error=1;
                alert ("Total Working hours not more than " + tt); 

            }


     
           //alert (final);
        });
      

      

      


        if(error==0){
            $.ajax({
                type: 'POST',
                url: '../projects/reportmuster',
                beforeSend : function(){
                    $('#musterreport').attr("disabled", true);

                },
                dataType: "json",
                data: $( "#musterform" ).serialize(),
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#musterform')[0].reset();
                        //window.location.href = url;
                        $('#receivedirectwork').trigger('click') ;
                        //$('#projectiddetails').html(data.project_id);
                        $('#selectedprojct').val(data.project_id);

                        $('#newone').show();
                      
                    }

                    $('#musterreport').attr("disabled", false);
                }
            });
        }
        else{
            alert("You have to enter all the values for reporting");
            return  false;
        }
    })


      $(document).on('keyup','.dwoworkedhrs',function(){ 
        $('.error').hide();
        var count=0;
        $('.dwoworkedhrs').each(function(){
            var id=$(this).attr('data-id');
            var workinghrs=$('#workinghrs'+id).val()*1;  
            if($(this).val() > workinghrs)
            {
                $('#workedhrs'+id).next('span').html('Hours worked not be greater than '+ workinghrs).show('slow');
               count++;
            }

        });
        if (count==0)
        {
            $('#musterreport').attr("disabled", false);
        }
        else {
            $('#musterreport').attr("disabled", true);
        }

    });
</script>

