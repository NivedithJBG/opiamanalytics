<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/logbook.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#logdate0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".log_add_field"); //Add button ID
        var x = 1; //initlal text box count
        $(add_button).click(function(e){
            e.preventDefault();
            if(x < max_fields){
                $('#equipmentrow').before('<tr style="background-color: #ffffff;">' +
                    '<td><select class="form-control equipment" name="equipment[]" id="equipment'+x+'" data-id="'+x+'">' +
                    '<option value="0">Select Equipment</option>' +
                    '<?php $equipments=Schedule::model()->findAll(array('condition'=>'resource_type=:type','params'=>array(':type' => 26),'order'=>'groupid ASC'));
                        foreach($equipments AS $equipment):
                            echo "<option value=".$equipment['resource_id']." >".$equipment->name."</option>";
                        endforeach;?></select></td>' +
                    //'<td><span id="equnit'+x+'"></span><input type="hidden" name="equnit"></td>' +
                    //'<td><input type="text" name="nohours[]" class="form-control nohours"></td>' +
                    '<td><input type="time" name="starttime[]" class="form-control starttime"></td>' +
                    '<td><input type="time" name="endtime[]" class="form-control endtime"></td>' +
                    '<td><input type="text" name="diesel[]" class="form-control diesel" id="diesel'+x+'" data-id="'+x+'"></td>' +
                    '<td><a href="javascript:void(0)" class="remove_field">Remove</a></td>' +
                    '</tr>');

                $('#logdate'+x+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
                x++;
            }

        });
        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
            var totalhours=0;
            $('.nohours').each(function(){
                totalhours=totalhours+$(this).val()*1;
            });
            $('#totalhours').html(totalhours);
            var totalhours=0;
            $('.diesel').each(function(){
                totalhours=totalhours+$(this).val()*1;
            });
            $('#totaldiesel').html(totalhours);
        })
    });
    $(document).on('click','#logbookreport',function(){
        var error=0;
        var url="<?php echo Yii::app()->request->baseUrl; ?>/projects/report";
        if($('#logbookactivity').val()=='none')
        {
            $('#logbookactivity').next("span").html('Select Activity').show('slow');
            error=1;
        }
        $('.unit').each(function(){
            var id=$(this).attr('data-id');
            if ($('#unit'+id).val()==3){
                if($('#trips'+id).val()==''){
                    error=1;
                }
            }
            else if ($('#unit'+id).val()==1 || $('#unit'+id).val()==2){
                if($('#starttime'+id).val()==''){
                    error=1;
                }
                if($('#endtime'+id).val()==''){
                    error=1;
                }
            }

        });
        /*$('.diesel').each(function(){
            var id=$(this).attr('data-id');
            if(!$.isNumeric($('#diesel'+id).val()))
            {
                //$('#diesel'+id).next("span").html('Quantity must be number').show('slow');
                error=1;
            }
        });*/
        /*$('.starttime').each(function(){
            var id=$(this).attr('data-id');
            if($('#starttime'+id).val()=='')
            {
                //$('#diesel'+id).next("span").html('Quantity must be number').show('slow');
                error=1;
            }
        });
        $('.endtime').each(function(){
            var id=$(this).attr('data-id');
            if($('#endtime'+id).val()=='')
            {
                //$('#diesel'+id).next("span").html('Quantity must be number').show('slow');
                error=1;
            }
        });*/
        /*$('.resourceqty').each(function(){
         if($(this).val()=='' ||$(this).val()=='0')
         {
         error=1;
         }
         });*/
        if(error==0){
            $.ajax({
                type: 'POST',
                url: '../projects/Reportlog',
                beforeSend : function(){
                    $('#logbookreport').attr("disabled", true);

                },
                dataType: "json",
                data: $( "#logbookform" ).serialize(),
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#logbookform')[0].reset();
                        //window.location.href = url;
                        $('#listlog').trigger('click') ;
                    }

                    $('#logbookreport').attr("disabled", false);
                }
            });
        }
        else{
            alert("You have to enter all values for reporting");
            return  false;
        }
    })
</script>
<h2 class="acc_trigger" id="logbook"><a href="javascript:void(0)">6. Despatch Orders</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <form id="logbookform">
                <div class="row show-grid">
                    <!--<div class="col-md-3"><button type="button" class="btn btn-success"  id="addlog"><span class="glyphicon glyphicon-plus-sign"></span>Add Log</button></div>-->
                    <div class="col-md-3"><button type="button" class="btn btn-danger" id="adddespatchorders"><span class="glyphicon glyphicon-list-alt"></span>Add Despatch Order</button></div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger" id="listdespatchorders"><span class="glyphicon glyphicon-list-alt"></span>List Despatch Orders</button></div>
                    <!--<div class="col-md-3"><button type="button" class="btn btn-danger" id="listdespatch"><span class="glyphicon glyphicon-list-alt"></span>List Despatch Orders</button></div>-->
                    <div class="col-md-3"><button type="button" class="btn btn-danger" id="listlog"><span class="glyphicon glyphicon-list-alt"></span>List Log</button></div>
                    <div class="col-md-2" style="text-align: left;" id="dispprojectname">
                        <input type="hidden" name="project_id" id="logprojectid">
                    </div>
                </div>
                <div id="loglistsection">
                    <div class="row show-grid">
                        <table class="table table-bordered" id="logtable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th >Machinery Name</th>
                                <th >Total Diesel</th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="logitems">

                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="desporderslist">
                    <div class="row show-grid">
                        <table class="table table-bordered" id="desporderstable">
                            <thead>
                            <!--<tr>
                                <th colspan="9"><span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center">Cart</span></th>
                            </tr>-->
                            <tr>
                                <th>Date</th>
                                <th>Machinery Name</th>
                                <th>Status</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="desporderitems">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="logaddsection" style="display: none">
                    <div id="logbookprocess" class="row show-grid">
                        <div class="col-md-3">
                            <input type="text" class="form-control datepicker" name="Logbook_Date" id="logdate0" value="<?php echo date("d-m-Y");?>">
                        </div>
                        <div class="col-md-3" id="logbookprocessdiv">

                        </div>
                        <div class="col-md-3">
                            <select id="logbookactivity" name="logactivity" class="form-control">
                                <option value="none">Select Activity</option>
                            </select>
                            <span class="error"></span>
                        </div>
                    </div>
                    <div id="reportlogbook">
                        <table class="table table-bordered" id="logbookreporttable" style="display: table;">
                            <thead>
                            <tr>
                                <th style="width: 50%">Equipment(Resource)</th>
                                <!--<th>Number of hours/km</th>-->
                                <th style="width: 11%">Unit</th>
                                <th>Start Time/Kms</th>
                                <th>End Time/Kms</th>
                                <th>Net Time/Kms</th>
                                <th style="width: 7%">No of Trips</th>
                                <th>Diesel Consumed</th>
                            </tr>
                            </thead>
                            <tbody id="logbookitems" class="input_fields_wrap">

                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>