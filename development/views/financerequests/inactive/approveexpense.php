<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<h1>Approve Expense Request</h1>
<form method="POST" action="" id="receiptform">
    <table class="table table-bordered ">
        <thead>
        <tr>
            <th>Username</th>
            <th>Purpose</th>
            <th>Amount</th>
            <th>Payment Type</th>
            <th></th>
        </tr>
        </thead>
        <tbody>

        <?php foreach($cashbills AS $key=>$cashbill):?>
            <tr>
                <td>
                    <select class="form-control accountshead" id="accountshead<?php echo $key;?>" name="accountshead[]" data-id="<?php echo $key;?>">
                        <option value="none">Select Username</option>
                        <?php
                        $employees=AccountsItem::model()->findAll(array('condition'=>'(account_type=16 AND schedule=3) OR account_type=1 OR account_type=2','order'=>'name ASC'));
                        foreach($employees AS $employee):
                            if($cashbill['accounthead']==$employee['id']):$selected='selected';else:$selected='';endif;
                            echo "<option value='".$employee['id']."' ".$selected.">".$employee['name']."</option>";
                        endforeach;
                        ?>
                    </select><span class="error"></span>
                </td>
                <td><input type="text" class="form-control cashadvancepurpose" name="cashadvancepurpose[]" id="cashadvancepurpose<?php echo $key;?>" data-id="<?php echo $key;?>" value="<?php echo $cashbill['purpose'];?>"><span class='error'></span></td>
                <td>
                    <input type="text" class="form-control cashadvanceamount" name="cashadvanceamount[]" id="cashadvanceamount<?php echo $key;?>" data-id="<?php echo $key;?>" value="<?php echo $cashbill['amount'];?>">
                    <span class='error'></span>
                    <input type="hidden" name="advanceid[]" value="<?php echo $cashbill['expense_id'];?>">
                </td>
                <td><select class="form-control advancepaytype" id="advancepaytype<?php echo $key;?>" data-id="<?php echo $key;?>" name="advancepaytype[]" >
                        <option value="none">Select Payment Type</option>
                        <option <?php echo ($cashbill['paymenttype']==1?'selected':'')?> value="1">Cash</option>
                        <option <?php echo ($cashbill['paymenttype']==2?'selected':'')?> value="2">Bank</option>
                        <option <?php echo ($cashbill['paymenttype']==3?'selected':'')?> value="3">Journal</option>
                    </select>
                    <span class='error'></span>
                </td>
                <td>
                    <select class="form-control" id="cashadvancestatus" name="cashadvancestatus[]" >
                        <option value="none">Select Status</option>
                        <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);
                        if(Yii::app()->user->isAdmin() || $user['superuser']==2): ?>
                            <option value="0">Pending</option>
                            <option <?php echo ($cashbill['status']==5?'selected':'')?> value="5">Save as draft</option>
                            <option value="1">Approve</option>
                            <option value="2">Deny</option>
                        <?php else:?>
                            <option <?php echo ($cashbill['status']==5?'selected':'')?> value="5">Save as draft</option>
                        <?php endif;?>
                    </select>
                    <span class='error'></span>
                </td>
            </tr>
        <?php endforeach;?>
        <tr>
            <td colspan="3"></td>
            <td><input type="button" class="btn btn-primary" value="Cancel" name="Cashadvance_Cancel" onclick="goBack();"></td>
            <td><input type="submit" class="btn btn-primary" value="Save" name="Cashadvance_Save" id="cashadvanceapprove"></td>
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
            $('.advancepaytype').each(function(){
                var id=$(this).attr('data-id');
                if($('#advancepaytype'+id).val()=='none')
                {
                    $('#advancepaytype'+id).next("span").html('Select Payment Type').show('slow');
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