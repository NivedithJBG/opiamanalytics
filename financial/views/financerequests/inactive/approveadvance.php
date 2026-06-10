<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<h1>Approve Advance Request</h1>
<form method="POST" action="" id="receiptform">
    <table class="table table-bordered ">
        <thead>
        <tr>
            <th>Username</th>
            <th>Purpose</th>
            <th>Amount</th>
            <!--<th>Payment Type</th>-->
            <th colspan="2"></th>
        </tr>
        </thead>
        <tbody>

        <?php
            $total=0;
            foreach($cashbills AS $key=>$cashbill):?>
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
                    <input type="hidden" name="advanceid[]" value="<?php echo $cashbill['cashadvance_id'];?>">
                </td>
                <!--<td><select class="form-control advancepaytype" id="advancepaytype<?php /*echo $key;*/?>" data-id="<?php /*echo $key;*/?>" name="advancepaytype[]" >
                        <option value="none">Select Payment Type</option>
                        <option <?php /*echo ($cashbill['paymenttype']==1?'selected':'')*/?> value="1">Cash</option>
                        <option <?php /*echo ($cashbill['paymenttype']==2?'selected':'')*/?> value="2">Bank</option>
                    </select>
                    <span class='error'></span>
                </td>-->
                <td>
                    <select class="form-control" id="cashadvancestatus" name="cashadvancestatus[]" >
                        <option value="none">Select Status</option>
                        <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);
                        if(Yii::app()->user->isAdmin() || $user['superuser']==2): ?>
                            <option value="0">Pending</option>
                            <!--<option <?php /*echo ($cashbill['status']==5?'selected':'')*/?> value="5">Save as draft</option>-->
                            <option value="1">Approve</option>
                            <option value="2">Deny</option>
                        <?php /*else:*/?><!--
                            <option <?php /*echo ($cashbill['status']==5?'selected':'')*/?> value="5">Save as draft</option>-->
                        <?php endif;?>
                    </select>
                    <span class='error'></span>
                </td>
                <td><a href="javascript:void(0)" class="btn btn-primary updateremove_field" id="updateremove_field<?php echo $cashbill['cashadvance_id'];?>" data-id="<?php echo $cashbill['cashadvance_id'];?>">Remove</a></td>
            </tr>
        <?php
            $total+=$cashbill['amount'];
            endforeach;?>
        <tr>
            <td colspan="2">Total</td>
            <td><?php echo $total;?></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3"></td>
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
    });
    $(document).on('click','.updateremove_field',function(){
        var cashadvanceid=$(this).data("id");
        var r = confirm("Are you sure you want to delete this Advance?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../../FinanceRequests/deletecashadvance',
                beforeSend : function(){
                    $('#updateremove_field'+cashadvanceid).attr("disabled", true);
                },
                dataType: "json",
                data: {cashadvance:cashadvanceid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#updateremove_field'+cashadvanceid).attr("disabled", false);
                    }

                }
            });
        }

    });
</script>