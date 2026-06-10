<script>
    function goBack() {

        window.history.back()
    }


</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#startdate0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
        $('#enddate0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});

        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var x = 1; //initlal text box count
        $(add_button).click(function(e){
            e.preventDefault();
            if(x < max_fields){
                $('#travelbillrow').after('<tr>' +
                    '<td><input type="text" class="form-control" id="startdate'+x+'" name="startdate[]" value="<?php echo date('d-m-Y');?>"></td>' +
                    '<td><input type="text" class="form-control" id="enddate'+x+'" name="enddate[]" value="<?php echo date('d-m-Y');?>"></td>' +
                    '<td><select class="form-control" id="travelmode'+x+'" name="travelmode[]">' +
                    '<option value="none">Select Mode of Travel</option>' +
                    '<option value="1">Air</option>' +
                    '<option value="2">Train</option>' +
                    '<option value="3">Bus</option>' +
                    '<option value="4">Auto</option>' +
                    '</select></td>' +
                    '<td><a href="#" class="remove_field">Remove</a></td></tr>');
                var id=(x - 1);
                $('#startdate'+x).datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
                $('#enddate'+x).datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
                x++;
            }
        });
        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        })
    });

    $(document).on('change','#amount',function(){
        var amount=$(this).val() * 1;
        var lcharge=$('#lodging').val() * 1;
        var bcharge=$('#boarding').val() * 1;
        var total=amount + bcharge + lcharge;
        $('#total').text(total);
        $('#totalval').val(total);
    });
    $(document).on('change','#boarding',function(){
        var bcharge=$(this).val() * 1;
        var lcharge=$('#lodging').val() * 1;
        var amount=$('#amount').val() * 1;
        var total=amount + bcharge + lcharge;
        $('#total').text(total);
        $('#totalval').val(total);
    });
    $(document).on('change','#lodging',function(){
        var lcharge=$(this).val() * 1;
        var bcharge=$('#boarding').val() * 1;
        var amount=$('#amount').val() * 1;
        var total=amount + bcharge + lcharge;
        $('#total').text(total);
        $('#totalval').val(total);
    });
    $(document).on("click", "#savebill", function () {
        var error = 0;
        $('.error').hide();
        if ($('#placebill').val() == 0) {
            $("#placebill").next("span").html('Select Project').show('slow');
            error = 1;
        }
        if ($('#amount').val() == '') {
            $("#amount").next("span").html('Enter conveyance amount').show('slow');
            error = 1;
        }
        if ($('#boarding').val() == '') {
            $("#boarding").next("span").html('Enter Boarding amount').show('slow');
            error = 1;
        }
        if ($('#lodging').val() == '') {
            $("#lodging").next("span").html('Enter lodging amount').show('slow');
            error = 1;
        }
        if (error == 0) {
            return true;
        }
        else {
            return false;
        }
    });

</script>
<form method="POST" action="" id="travelbillsform">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>User</th>
                <td><?php echo $username;?></td>
                <th>Designation</th>
                <td><?php echo $designation;?></td>
            </tr>
            <tr>
                <th>Project</th>
                <td>
                    <select class="form-control" name="place" id="placebill">
                        <option value="0">Select Project</option>
                        <?php $projects = Projects::model()->findAll(array('condition'=>'Project_Delete_Status=0'));
                        foreach ($projects AS $project):?>
                            <option value="<?php echo $project['Project_Id']?>"><?php echo $project['Name']?></option>
                        <?php endforeach;?>
                    </select>
                    <span class='error'></span>
                </td>
                <th>Purpose</th>
                <td>
                    <textarea rows="3" cols="25" class="form-control" name="purpose" placeholder="Purpose"></textarea>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered" align="center" >
        <tbody class="input_fields_wrap">
            <tr>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Fare</th>
                <th>
                    <input type="button" class="btn btn-primary add_field_button" name="addmore" id="addmore" title="Add more" value="Add more">
                </th>
            </tr>
            <tr id="travelbillrow">
                <td><input type="text" class="form-control" name="startdate[]" id="startdate0" value="<?php echo date('d-m-Y');?>"></td>
                <td><input type="text" class="form-control" name="enddate[]" id="enddate0" value="<?php echo date('d-m-Y');?>"></td>
                <td>
                    <select class="form-control" id="travelmode0" name="travelmode[]">
                        <option value="none">Select Mode of Travel</option>
                        <option value="1">Air</option>
                        <option value="2">Train</option>
                        <option value="3">Bus</option>
                        <option value="4">Auto</option>
                    </select>
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <table class="table">
        <tbody >
            <tr>
                <td>Total Amount Incurred for conveyance Rs </td>
                <td><input type="text" class="form-control" id="amount" name="travelamount"><span class='error'></span></td>
            </tr>
            <tr>
                <td>Boarding charges for </td>
                <td class="small752"><input type="text" class="form-control" id="boarding" name="boardingamount"><span class='error'></span></td>
            </tr>
            <tr>
                <td>Lodging charges for </td>
                <td class="small752"><input type="text" class="form-control" id="lodging" name="lodgingamount"><span class='error'></span></td>
            </tr>
            <tr>
                <th>Total</th>
                <td>
                    <span id="total"></span>
                    <input type="hidden" name="biltot" id="totalval">
                </td>
            </tr>
            <tr>
                <td><input type="button" class=" btn btn-primary" value="Cancel" onclick="goBack();"></td>
                <td colspan="2"><input type="submit" class=" btn btn-primary" id="savebill" value="Save"></td>
            </tr>
        </tbody>
    </table>