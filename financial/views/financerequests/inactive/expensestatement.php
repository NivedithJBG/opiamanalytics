<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID

        var x = 1; //initlal text box count
        $(add_button).click(function(e){
            if(x < max_fields){ //max input box allowed
                //text box increment
                $('#expenserow').before('<tr><td>' +
                    '<select class="form-control accountshead" id="accountshead'+x+'" name="accountshead[]" data-id="'+x+'">' +
                    '<option value="none">Select Username</option>'+
                    '<?php $employees=AccountsItem::model()->findAll(array('condition'=>'id='.$cashbills[0]['accounthead'].'','order'=>'name ASC'));
                        foreach($employees AS $employee):
                            echo "<option value=".$employee['id']." selected>".$employee['name']."</option>";
                        endforeach;?></select><span class="error"></span></td>' +
                    '<td><input type="text" class="form-control cashadvancepurpose" name="cashadvancepurpose[]" id="cashadvancepurpose'+x+'" data-id="'+x+'" value="">' +
                    '<span class="error"></span></td>' +
                    '<td><input type="text" class="form-control cashadvanceamount" name="cashadvanceamount[]" id="cashadvanceamount'+x+'" data-id="'+x+'" value="">' +
                    '<span class="error"></span>' +
                    '<input type="hidden" name="advanceid[]" value="'+x+'">' +
                    '<input type="hidden" name="projectid[]" value="<?php echo $cashbills[0]['project_id'];?>"></td>' +
                    '<td><a href="#" class="remove_field">Remove</a></td></tr>');
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
<h1>Expense Statement</h1>
<form method="POST" action="" id="receiptform">
    <table class="table table-bordered ">
        <thead>
        <tr>
            <th>Username</th>
            <th>Purpose</th>
            <th>Amount</th>
            <th><input type="button" style="float: right;width: 100%;" class="btn btn-primary add_field_button" id="addmore" value="Add Items"></th>
        </tr>
        </thead>
        <tbody class="input_fields_wrap">

        <?php foreach($cashbills AS $key=>$cashbill):?>
            <tr>
                <td>
                    <select class="form-control accountshead" id="accountshead<?php echo $cashbill['cashadvance_id'];?>" name="accountshead[]" data-id="<?php echo $cashbill['cashadvance_id'];?>">
                        <option value="none">Select Username</option>
                        <?php
                        $employees=AccountsItem::model()->findAll(array('condition'=>'id='.$cashbill['accounthead'].' ','order'=>'name ASC'));
                        foreach($employees AS $employee):
                            if($cashbill['accounthead']==$employee['id']):$selected='selected';else:$selected='';endif;
                            echo "<option value='".$employee['id']."' ".$selected.">".$employee['name']."</option>";
                        endforeach;
                        ?>
                    </select><span class="error"></span>
                </td>
                <td><input type="text" class="form-control cashadvancepurpose" name="cashadvancepurpose[]" id="cashadvancepurpose<?php echo $cashbill['cashadvance_id'];?>" data-id="<?php echo $cashbill['cashadvance_id'];?>" value="<?php echo $cashbill['purpose'];?>"><span class='error'></span></td>
                <td>
                    <input type="text" class="form-control cashadvanceamount" name="cashadvanceamount[]" id="cashadvanceamount<?php echo $cashbill['cashadvance_id'];?>" data-id="<?php echo $cashbill['cashadvance_id'];?>" value="<?php echo $cashbill['amount'];?>">
                    <span class='error'></span>
                    <input type="hidden" name="advanceid[]" value="<?php echo $cashbill['cashadvance_id'];?>">
                    <input type="hidden" name="projectid[]" value="<?php echo $cashbill['project_id'];?>">
                </td>
                <td></td>
            </tr>
        <?php endforeach;?>
        <tr id="expenserow">
            <td colspan="2"></td>
            <td><input type="submit" class="btn btn-primary" value="Save" name="Cashadvance_Save" id="cashadvanceapprove"></td>
            <td><input type="button" class="btn btn-primary" value="Cancel" name="Cashadvance_Cancel" onclick="goBack();"></td>
        </tr>
        </tbody>
    </table>
</form>
<script type="text/javascript">
    $(function(){
        $('#cashadvanceapprove').click(function(){
            var error=0;
            $('.error').hide();
            $('.accountshead').each(function(){
                var id=$(this).attr('data-id');
                if($('#accountshead'+id).val()=='none')
                {
                    $('#accountshead'+id).next("span").html('Select Username').show('slow');
                    error=1;
                }
            });
            $('.cashadvancepurpose').each(function(){
                var id=$(this).attr('data-id');
                if($('#cashadvancepurpose'+id).val()=='')
                {
                    $('#cashadvancepurpose'+id).next("span").html('Enter Purpose').show('slow');
                    error=1;
                }
            });
            $('.cashadvanceamount').each(function(){
                var id=$(this).attr('data-id');
                if($('#cashadvanceamount'+id).val()=='')
                {
                    $('#cashadvanceamount'+id).next("span").html('Enter Amount').show('slow');
                    error=1;
                }
            });
            if (error==0){
                return true;
            }
            else {
                return false;
            }
        });
    })
</script>