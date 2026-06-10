<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/muster.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#date0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var x = 2; //initlal text box count
        $(add_button).click(function(e){
            e.preventDefault();
            if(x < max_fields){
                $('#workerrow').before('<tr id="workerrow'+x+'" style="background-color: #ffffff;">' +
                    '<td class="small75">'+x+'</td>' +
                    '<td><input type="text" name="worker[]" class="form-control workerold"></td>' +
                    '<td><select class="form-control trade" name="trade[]" id="trade'+x+'" data-id="'+x+'">' +
                    '<option value="none">Select Trade</option>' +
                    '<?php
                        $connection = CActiveRecord::getDbConnection();
                        $sql="SELECT a.trade_id,a.rate,a.ot,a.name,b.Name FROM trade AS a INNER JOIN resources AS b ON a.name=b.Resource_Id ";
                        $sql.="ORDER BY trade_id ASC";
                        $command = $connection->createCommand($sql);
                        $dataReader = $command->query();
                        $trades = $dataReader->readAll();
                        foreach($trades AS $trade):
                            echo "<option value=".$trade['trade_id']." >".$trade['Name']."</option>";
                        endforeach;?></select></td>' +
                    '<td class="small75"><span id="rate'+x+'"></span><input type="hidden" id="rateval'+x+'" name="rateval'+x+'"></td>' +
                    '<td class="small75"><span id="ot'+x+'"></span><input type="hidden" id="otval'+x+'" name="otval'+x+'"></td>' +
                    '<td class="small75"><input type="text" name="workedhrs[]" id="workedhrsold'+x+'" class="form-control workedhrsold" data-id="'+x+'"></td>' +
                    '<td class="small75"><input type="text" name="overtime[]" id="overtime'+x+'" class="form-control overtime" data-id="'+x+'"></td>' +
                    //'<td class="small75"><input type="text" name="totalhrs[]" class="form-control"></td>' +
                    '<td class="small75"><span id="wages'+x+'"></span>' +
                    '<input type="hidden" id="wagesval'+x+'" class="wagesval" name="wages[]"></td>' +
                    '<td><a href="javascript:void(0)" class="remove_field">Remove</a></td>' +
                    '</tr>');

                $('#datepicker'+x+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
                x++;
            }

        });
        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        })
    });
    $(document).on('click','#musterreport',function(){
        var error=0;
        var url="<?php echo Yii::app()->request->baseUrl; ?>/projects/report";
        /*if($('#musteractivity').val()=='none')
        {
            error=1;
        }*/

        $('.worker').each(function(){
             if($(this).val()=='')
             {
                error=1;
             }
         });
        $('.workedhrs').each(function(){
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
        if(error==0){
            $.ajax({
                type: 'POST',
                url: '../projects/Reportmuster',
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
                    }

                    $('#musterreport').attr("disabled", false);
                }
            });
        }
        else{
            alert("You have to enter all values for reporting");
            return  false;
        }
    })
</script>

<h2 class="acc_trigger" id="muster"><a href="javascript:void(0)">4. Direct Workers Order</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <form id="musterform">
                <div class="row show-grid">
                    <!--<div class="col-md-3"><button type="button" class="btn btn-success"  id="addmuster"><span class="glyphicon glyphicon-plus-sign"></span>Add Muster</button></div>-->
                    <div class="col-md-3"><button type="button" class="btn btn-danger" id="receivedirectwork"><span class="glyphicon glyphicon-list-alt"></span>List Direct Work Orders</button></div>
                    <!--<div class="col-md-3"><button type="button" class="btn btn-danger" id="listdirectwork"><span class="glyphicon glyphicon-list-alt"></span>List Direct Work Orders</button></div>-->
                    <div class="col-md-3"><button type="button" class="btn btn-danger" id="listmuster"><span class="glyphicon glyphicon-list-alt"></span>List Muster</button></div>
                    <div class="col-md-2" style="text-align: left;" id="dispprojectname">
                        <input type="hidden" name="project_id" id="musterprojectid">
                    </div>
                </div>
                <div id="musterlistsection">
                    <div class="row show-grid">
                        <div class="col-md-12" id="dateinfodiv"></div>
                    </div>
                    <div class="row show-grid">
                        <table class="table table-bordered" id="mustertable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th >Activity</th>
                                <th >Total amount</th>
                                <th colspan="4"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="musteritems">

                            </tbody>
                        </table>
                    </div>
                </div>
                <!--<div id="directworklistsection">
                    <div class="row show-grid">
                        <table class="table table-bordered" id="directworktable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Project</th>
                                <th >Vendor Name</th>
                                <th >Activity Name</th>
                                <th >Amount</th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php /*echo Yii::app()->request->baseUrl; */?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="directworkitems">

                            </tbody>
                        </table>
                    </div>
                </div>-->
                <div id="receivedirectworkorders">
                    <div class="row show-grid">
                        <table class="table table-bordered" id="receivedirectworktable">
                            <thead>
                            <!--<tr>
                                <th colspan="9"><span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center">Cart</span></th>
                            </tr>-->
                            <tr>
                                <th>Date</th>
                                <th>Project</th>
                                <th>Activity Name</th>
                                <th>Vendor Name</th>
                                <th>Amount</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody  id="receivedirectworkitems">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="musteraddsection" style="display: none">
                    <div id="musterprocess" class="row show-grid">
                        <!--<div class="col-md-3" id="musterprocessdiv">

                        </div>-->
                        <!--<div class="col-md-3">
                            <select id="musteractivity" name="musteractivity" class="form-control">
                                <option value="none">Select Activity</option>
                            </select>
                        </div>-->
                        <div class="col-md-3">
                            <input type="text" class="form-control datepicker" name="Muster_Date" id="date0" value="<?php echo date("d-m-Y");?>">
                        </div>
                        <div class="col-md-6">
                            <h4 id="activitydiv"></h4>
                        </div>
                    </div>
                    <div id="reportmuster">
                        <table class="table table-bordered" id="musterreporttable" style="display: table;">
                            <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Name of the Employee</th>
                                <th >Trade</th>
                                <!--<th >Rate of wages</th>-->
                                <!--<th >OT Wages</th>-->
                                <th >Hours Worked</th>
                                <th >Overtime Hours</th>
                                <!--<th >Total Hours</th>-->
                                <!--<th >Wages Earned</th>-->
                                <!--<th></th>-->
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            <tbody id="musterreportitems" class="input_fields_wrap">



                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
            <form id="raisemusterform">
                <div id="raisemustersection" style="display: none">
                    <div class="col-md-12">
                        <h4 id="raiseactivitydiv"></h4>
                    </div>
                    <div id="raisereportmuster">
                        <table class="table table-bordered" id="raisemustertable" style="display: table;">
                            <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Name of the Employee</th>
                                <th >Trade</th>
                                <th >Daily Rate of Wages</th>
                                <th >OT Wages</th>
                                <th >No of Days Worked</th>
                                <th >Overtime Hours</th>
                                <th >Wages Earned</th>
                                <th >Deductions if any</th>
                                <th >Net Amount</th>
                                <!--<th></th>-->
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            <tbody id="raisemusteritems" class="input_fields_wrap">



                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>