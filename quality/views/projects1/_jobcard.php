<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/jobcard.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#jobcarddate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
        $('#startdate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
        $('#enddate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var x = 2; //initlal text box count
        $(add_button).click(function(e){
            e.preventDefault();

            if(x < max_fields){
                $('#jobcardrow').before('<tr><td class="small75">'+x+'</td>' +
                    '<td><select id="JobcardResource'+x+'" data-id="'+x+'" name="Resource[]" class="form-control JobcardResource">' +
                    '<option value="none">Select Resource</option></select></td>' +
                    '<td><input type="text" class="form-control" readonly name="Unit[]" id="Unit'+x+'" data-id="'+x+'" placeholder="Unit"></td>' +
                    '<td><span id="EstQuantity'+x+'"></span><input type="hidden" id="EstQty'+x+'" name="EstQuantity[]" value=""></td>' +
                    //'<td id="exstresqty'+x+'"></td>' +
                    '<td id="recresqty'+x+'"></td>' +
                    '<td><input type="text" class="form-control Quantity" name="PropQuantity[]" id="PropQuantity'+x+'" data-id="'+x+'" placeholder="Quantity" autocomplete="off">' +
                    '<span class="error" style="display: none;"></span></td>' +
                    '<td><span id="remquantity'+x+'"></span>' +
                    '<input type="hidden" id="remqty'+x+'" name="RemQuantity[]"></td>' +
                    '<td><a href="javascript:void(0)" class="remove_field">Remove</a></td>' +
                    '</tr>');
                var activityid=$('#jobcardactivity').val();
                $.ajax({
                    type: 'POST',
                    url: '../Jobcard/Getresources',
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
        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        })
    });

</script>

<h2 class="acc_trigger" id="jobcard"><a href="javascript:void(0)">7. Job Card</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3" style="text-align: left;">

                    <h4 id="jobcardprojname"></h4>

                </div>
                <div class="col-md-2"><button type="button" class="btn btn-success"  id="addjobcard"><span class="glyphicon glyphicon-plus-sign"></span>Raise Job Card</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="listjobcard"><span class="glyphicon glyphicon-list-alt"></span>Job Cards For Approval</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="listappjobcard"><span class="glyphicon glyphicon-list-alt"></span>Approved Job Card</button></div>
                <!--<div class="col-md-2"><button type="button" class="btn btn-danger" id="listcompjobcard"><span class="glyphicon glyphicon-list-alt"></span>Completed Job Card</button></div>-->
                <!--<div class="col-md-2" style="text-align: left;" id="dispprojectname">

                </div>-->
            </div>
            <div id="jobcardlistsection">
                <div class="row show-grid">
                    <table class="table table-bordered" id="jobcardtable" style="display: table; overflow: hidden;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>User</th>
                            <th >Activity</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="jobcarditems">

                        </tbody>
                    </table>
                </div>
            </div>
            <form id="jobcardform">
                <div id="jobcardaddsection" style="display: none">
                    <div class="row show-grid">
                        <div class="col-md-2">
                            <input type="text" class="form-control datepicker" name="Job_Date" id="jobcarddate" value="<?php echo date("d-m-Y");?>">
                            <input type="hidden" name="project_id" id="jobcardprojectid">
                        </div>
                        <div class="col-md-3">
                            <select id="jobcardprocesslist" name="jobcardprocess" class="form-control">
                                <option value="none">Select Process</option>

                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="jobcardactivity" name="Job_Activity" class="form-control">
                                <option value="none">Select Activity</option>
                            </select>
                            <input type="hidden" id="actqty">
                            <input type="hidden" id="jobcards">
                            <span class="error" style="display: none" id="jobcardserror"></span>
                        </div>
                        <!--<div class="col-md-3">
                            <span class="headings">
                                Unit <h5 id="actunit"></h5>
                            </span>
                        </div>-->
                    </div>
                    <!--<table class="table table-bordered" id="jobcardreporttable" style="display: table;width: 60%">
                        <thead>
                            <tr style="background-color: #ffffff">
                                <td>Date</td>
                                <td>Process</td>
                                <td>Activity</td>
                                <td>Unit</td>
                                <td>Estimated Quantity</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td ></td>
                                <td id="actquantity"></td>
                            </tr>
                        </thead>
                    </table>-->
                    <table>
                        <tbody class="table-bordered input_fields_wrap">
                            <tr>
                                <th class="small75">#</th>
                                <th>Resource</th>
                                <th>Unit</th>
                                <th>Estimated Quantity</th>
                                <!--<th>Earlier Proposed Quantity</th>-->
                                <th>Received Quantity</th>
                                <th>Proposed Quantity</th>
                                <th>Remaining Quantity</th>
                                <th></th>
                            </tr>
                            <tr>
                                <td class="small75">1</td>
                                <td>
                                    <select id="JobcardResource1" data-id="1" name="Resource[]" class="form-control JobcardResource">
                                        <option value="none">Select Resource</option>
                                    </select>
                                    <!--<input type="text" class="form-control" name="Resource[]" id="Resource" placeholder="Resource">-->
                                </td>
                                <td><input type="text" class="form-control" readonly name="Unit[]" id="Unit1" data-id="1" placeholder="Unit"></td>
                                <td>
                                    <!--<input type="text" class="form-control Quantity" name="Quantity[]" id="Quantity1" data-id="1"placeholder="Quantity">-->
                                    <span id="EstQuantity1"></span>
                                    <input type="hidden" id="EstQty1" name="EstQuantity[]">
                                </td>
                                <!--<td id="exstresqty1"></td>-->
                                <td id="recresqty1">

                                </td>
                                <td>
                                    <input type="text" class="form-control Quantity" name="PropQuantity[]" id="PropQuantity1" data-id="1" placeholder="Quantity">
                                    <span class="error" style="display: none;"></span>
                                </td>
                                <td>
                                    <span id="remquantity1"></span>
                                    <input type="hidden" id="remqty1" name="RemQuantity[]">
                                </td>
                                <td><input type="button" style="display: block;margin: auto;" class="btn btn-primary add_field_button small75" id="addmore" value="Add"></td>
                            </tr>
                            <tr id="jobcardrow">
                                <td colspan="7"></td>
                                <td colspan="2"><button type="submit" class="btn btn-primary" name="jobcardreport" id="jobcardreport">Save</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>