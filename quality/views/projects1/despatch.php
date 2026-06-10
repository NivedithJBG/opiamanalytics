<script type="text/javascript">
    $(function(){
        $('#cancelorder').click(function(){
            window.location = '<?php echo Yii::app()->createUrl('projects/report');?>'
        });
    });

</script>
<script type="text/javascript">

    $(document).on('focus','#date',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            maxDate: new Date()
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var x = 1; //initlal text box count
        $(add_button).click(function(e){
            e.preventDefault();
            if(x < max_fields){
                $('#resourceitemsrow').before('<tr><td><input type="text" class="form-control" name="date[]" id="date'+x+'"></td>' +
                    '<td><select class="form-control restype" name="restype[]" id="items'+x+'" data-id="'+x+'" disabled>' +
                     '<?php $restypes=Resourcetype::model()->findAll();
                        foreach($restypes AS $restype):
                        if($restype['ResourceType_Id']==26):
                            $selected='selected';
                        else:
                            $selected='';
                        endif;
                        echo "<option value=".$restype['ResourceType_Id']." ".$selected.">".$restype['Name']."</option>";
                    endforeach;
                     ?>'+
                    '</select><span class="error"></span></td>' +
                    '<td><select class="form-control resgroup" name="resgroup[]" id="resgroup'+x+'" data-id="'+x+'" data-type="26">' +
                    '<option value="none">Select Machinery Group</option>' +
                    '<?php $resgrps=ResourceGroup::model()->findAll(array('condition'=>'ResourceType_Id=26'));
                    foreach($resgrps AS $resgrp):
                        echo "<option value=".$resgrp['Resource_group_Id'].">".$resgrp['Resource_group_Name']."</option>";
                    endforeach;?>'+
                    '</select>' +
                    '</td>' +
                    '<td><select class="form-control resource" name="resource[]" id="resource'+x+'" data-id="'+x+'">' +
                    '<option value="none">Select Machinery Name</option></select></td>' +
                    '<td><select class="form-control from" name="from[]" id="from'+x+'" data-id="'+x+'">' +
                    '<?php echo $projoptions;?>'+
                    '</select><span class="error"></span></td>' +
                    '<td><select class="form-control to" name="to[]" id="to'+x+'" data-id="'+x+'">' +
                    '<?php echo $projoptions;?>'+
                    '</select><span class="error"></span></td>' +
                    '<td><input type="text" class="form-control vehicle_no" name="vehicle_no[]" id="vehicle_no'+x+'" data-id="'+x+'"><span class="error"></span></td>' +
                    '<td><a href="javascript:void(0)" class="remove_field">Remove</a></td>' +
                    '</tr>');
                $('#date'+x+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
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
<script type="application/javascript">
    $(document).on( "change",".resgroup", function(){
        var restype=$(this).attr('data-type');
        var id=$(this).attr('data-id');
        var resgrpid=$(this).val();
        $.ajax({
            type: 'POST',
            url: '../projects/groupres',
            dataType: "json",
            data: {restype:restype,resgrpid:resgrpid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#resource'+id).html(data.result);
                }
            }
        });
    });
</script>
<h1>Despatch Order </h1>
<form method="POST" action="" id="placeorderform">
    <table class="table table-bordered ">
        <thead>
        <tr>
            <th>Date</th>
            <th>Machinery Type</th>
            <th>Machinery Group</th>
            <th>Machinery Name</th>
            <th>Move From</th>
            <th>Move To</th>
            <th>Vehicle No</th>
            <th></th>
        </tr>
        </thead>
        <tbody class="input_fields_wrap">
        <tr>
            <td>
                <input type="text" class="form-control" name="date[]" id="date">
            </td>
            <td style="width: 13%;">
                <select class="form-control restype" name="restype[]" id="restype0" data-id="0" disabled>
                    <option value="none">Select Machinery Type</option>
                    <?php $restypes=Resourcetype::model()->findAll();
                    foreach($restypes AS $restype):
                        if($restype['ResourceType_Id']==26):
                            $selected='selected';
                        else:
                            $selected='';
                        endif;
                        echo "<option value='".$restype['ResourceType_Id']."' ".$selected.">".$restype['Name']."</option>";
                    endforeach;?>
                </select>
                <span class="error"></span>
            </td>
            <td style="width: 15%;">
                <select class="form-control resgroup" name="resgroup[]" id="resgroup0" data-id="0" data-type="26">
                    <option value="none">Select Machinery Group</option>
                    <?php $resgrps=ResourceGroup::model()->findAll(array('condition'=>'ResourceType_Id=26'));
                    foreach($resgrps AS $resgrp):
                        echo "<option value='".$resgrp['Resource_group_Id']."'>".$resgrp['Resource_group_Name']."</option>";
                    endforeach;?>
                </select>
            </td>
            <td style="width: 15%;">
                <select class="form-control resource" name="resource[]" id="resource0" data-id="0">
                    <option value="none">Select Machinery Name</option>
                </select>
            </td>
            <!--<td colspan="2">
                <select class="form-control items" name="items[]" id="items0" data-id="0"><?php /*echo $options;*/?></select>
                <span class="error"></span>
            </td>-->
            <td>
                <select class="form-control from" name="from[]" id="from0" data-id="0">
                    <?php echo $projoptions;?>
                </select>
                <span class="error"></span>
            </td>
            <td>
                <select class="form-control to" name="to[]" id="to0" data-id="0">
                    <?php echo $projoptions;?>
                </select><span class="error"></span>
            </td>
            <td><input type="text" class="form-control vehicle_no" name="vehicle_no[]" id="vehicle_no0" data-id="0"><span class="error"></span></td>
            <td><input type="button" style="display: block;margin: auto;" class="btn btn-primary add_field_button small75" id="addmore" value="Add"></td>
        </tr>
        <tr id="resourceitemsrow">
            <th>GSTN</th>
            <td colspan="1">
                <input type="text" class="form-control" name="gstn_no" value="32AAFCG7358B1ZV" readonly>
            </td>
            <th>Raised By</th>
            <td colspan="2">
                <select class="form-control" name="raisedby" id="raisedby"><?php echo $useroptions;?></select>
            </td>
            <th>Received by</th>
            <td colspan="2">
                <select class="form-control" name="receivedby" id="receivedby"><?php echo $useroptions;?></select>
                <input type="hidden" name="ordertype" value="5">
                <input type="hidden" id="vendors" name="vendor" value="44">
            </td>

        </tr>
        <tr>
            <td colspan="5"></td>
            <td><input type="button" value="Cancel" class="btn btn-primary" id="cancelorder"></td>
            <td colspan="2"><input type="submit" value="Submit" id="despatchorderbtn" class="btn btn-primary" name="submit"></td>
        </tr>
        </tbody>
    </table>
</form>

<script type="text/javascript">
    $(document).on('click','#despatchorderbtn',function(){
        var error=0;
        $('.error').hide();
        $('.items').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#items"+id).next("span").html('Select Item').show('slow');
                error=1;
            }
        });
        $('.from').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#from"+id).next("span").html('Select Move from').show('slow');
                error=1;
            }
        });
        $('.to').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#to"+id).next("span").html('Select Move to').show('slow');
                error=1;
            }
        });
        $('.vehicle_no').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#vehicle_no"+id).next("span").html('Enter Period of Vehicle No').show('slow');
                error=1;
            }
        });
        if(error==0){
            return true;
        }
        else{
            //alert("You have to enter all values for reporting");
            return  false;
        }
    });
</script>