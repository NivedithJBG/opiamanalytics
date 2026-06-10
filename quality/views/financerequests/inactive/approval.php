<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
    function goBack()
    {
        window.history.back()
    }
</script>

    <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);if($user['superuser']==2): ?>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.paymenttype').each(function(){
                var id=$(this).attr('data-id');
                var payment=($('input[name=payment'+id+']:checked').val());
                if (payment==2)
                {
                    var data="<option value='0'>Pending</option><option value='5' selected>Draft</option>";
                    $('#rqststatus'+id).html(data);
                }
                else
                {
                    var listdata="<option value='0'>Pending</option><option value='1'>Approve</option><option value='5'>Draft</option><option value='2'>Deny</option>";
                    $('#rqststatus'+id).html(listdata);
                }
            });
            $('.contraentry').each(function(){
                var id=$(this).attr('data-id');
                var data="<option value='1'>Approve</option><option value='5' selected>Draft</option>";
                var listdata="<option value='0'>Pending</option><option value='1'>Approve</option><option value='5'>Draft</option><option value='2'>Deny</option>";
                if($(this).is(":checked")) {
                    $('#rqststatus'+id).html(data);
                    //$('#rqststatus'+id).prop('disabled', 'disabled');
                    $('.paymentmode'+id).prop('disabled', 'disabled');
                }
            });

        });
        $(document).on( "change",".requeststatus", function(){
            var requestid=$(this).attr('data-id');
            var status=$('#rqststatus'+requestid).val();
            //alert(status)
            if(status==1){
                if($('#advance'+requestid).prop("checked") == true){
                    var amount=$('#request_amount'+requestid).val();
                    var ledgamount=$('#ledgbal'+requestid).val();
                    if(parseFloat(amount) > parseFloat(ledgamount))
                    {
                        alert('Amount cannot be greater than ledger amount');
                        $("#approverequest").attr('disabled','disabled');
                    }
                    else {
                        $("#approverequest").removeAttr('disabled');
                    }
                }
                $('#advance'+requestid).click(function(){
                    if($(this).prop("checked") == true){
                        var amount=$('#request_amount'+requestid).val();
                        var ledgamount=$('#ledgbal'+requestid).val();
                        if(parseFloat(amount) > parseFloat(ledgamount))
                        {
                            alert('Amount cannot be greater than ledger amount');
                            $("#approverequest").attr('disabled','disabled');
                        }
                    }
                    else if($(this).prop("checked") == false){
                        $("#approverequest").removeAttr('disabled');
                    }
                });

            }
            if(status==5){
                if($('#advance'+requestid).prop("checked") == true){
                    var amount=$('#request_amount'+requestid).val();
                    var ledgamount=$('#ledgbal'+requestid).val();
                    if(parseFloat(amount) > parseFloat(ledgamount))
                    {
                        alert('Amount cannot be greater than ledger amount');
                        $("#saveasdraft").attr('disabled','disabled');
                    }
                    else {
                        $("#saveasdraft").removeAttr('disabled');
                    }
                }
                $('#advance'+requestid).click(function(){
                    if($(this).prop("checked") == true){
                        var amount=$('#request_amount'+requestid).val();
                        var ledgamount=$('#ledgbal'+requestid).val();
                        if(parseFloat(amount) > parseFloat(ledgamount))
                        {
                            alert('Amount cannot be greater than ledger amount');
                            $("#saveasdraft").attr('disabled','disabled');
                        }
                    }
                    else if($(this).prop("checked") == false){
                        $("#saveasdraft").removeAttr('disabled');
                    }
                });
            }

        });
        /*$(document).on( "change",".requestamount", function(){
            var requestid=$(this).attr('data-id');
            if($('#advance'+requestid).prop("checked") == true){
                var amount=$('#requestamount'+requestid).val();
                var ledgamount=$('#ledgbal'+requestid).val();
                if(parseFloat(amount) > parseFloat(ledgamount))
                {
                    alert('Amount cannot be greater than ledger amount');
                    $("#saveasdraft").attr('disabled','disabled');
                    $("#approverequest").attr('disabled','disabled');
                }
                else {
                    $("#approverequest").removeAttr('disabled');
                    $("#saveasdraft").removeAttr('disabled');
                }
            }
        });*/
        $(document).on('click','.paymenttype',function(){
            var id=$(this).attr('data-id');
            if($(this).val()==2)
            {
                var data="<option value='0'>Pending</option><option value='5'>Draft</option>";
                $('#rqststatus'+id).html(data);
                //$('#rqststatus'+id).prop('disabled', 'disabled');
            }
            else
            {
                var listdata="<option value='0'>Pending</option><option value='1'>Approve</option><option value='5'>Draft</option><option value='2'>Deny</option>";
                $('#rqststatus'+id).html(listdata);
                //$('#rqststatus'+id).prop('disabled', false);
            }
        });
        $(document).on('change','.contraentry',function(){
            var id=$(this).attr('data-id');
            var data="<option value='1'>Approve</option><option value='5'>Draft</option>";
            var listdata="<option value='0'>Pending</option><option value='1'>Approve</option><option value='5'>Draft</option><option value='2'>Deny</option>";
            if($(this).is(":checked")) {
                $('#rqststatus'+id).html(data);
                //$('#rqststatus'+id).prop('disabled', 'disabled');
                $('.paymentmode'+id).prop('disabled', 'disabled');
            }
            else{
                $('#rqststatus'+id).html(listdata);
                //$('#rqststatus'+id).prop('disabled', false);
                $('.paymentmode'+id).prop('disabled', false);
                var payment=($('input[name=payment'+id+']:checked').val());

                if (payment==2)
                {
                    var data="<option value='0'>Pending</option><option value='5' selected>Draft</option>";
                    $('#rqststatus'+id).html(data);
                }
                else
                {
                    var listdata="<option value='0'>Pending</option><option value='1'>Approve</option><option value='5'>Draft</option><option value='2'>Deny</option>";
                    $('#rqststatus'+id).html(listdata);
                }


            }
        });
    </script>
    <?php endif;?>
<?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);if($user['superuser']==1): ?>
    <script type="text/javascript">
        $(document).on('submit','#approvalfrom',function(){
            var numberOfChecked = $('input:checkbox:checked').length;
            if(numberOfChecked==0)
            {
                /*if(!$('.payment'+':checked').val())
                {
                    alert('Select Payment Mode');
                    return false;
                }*/
                return true;
            }

        });
        $(document).on('change','.contraentry',function(){
            var id=$(this).attr('data-id');
            if($(this).is(":checked")) {
                $('.paymentmode'+id).prop('disabled', 'disabled');
                $('#transferrow').hide();
            }
            else{
                $('.paymentmode'+id).prop('disabled', false);
                $('#transferrow').show();
            }
        });
        $(document).on('change','.advance',function(){
            var id=$(this).attr('data-id');
            if($(this).is(":checked")) {
                //$("#approverequest").attr('disabled', 'disabled');
                //$('#approverequest').prop('disabled', false);
                getorders(id);

            }
            else{
                $('#work'+id).hide();
                /*var amount=$('#requestamount'+id).val();
                var ledgamount=$('#ledgbal'+id).val();

                if(parseFloat(amount) > parseFloat(ledgamount))
                {
                    $('#approverequest').prop('disabled', 'disabled');
                }*/

            }
        });

        function getorders(id)
        {
            $.ajax({
                type: 'POST',
                url: '../../FinanceRequests/getorder',
                dataType: "json",
                data: {reqid:id},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#po'+id).html(data.result);
                    }
                }
            });
        }
    </script>
<?php endif;?>
<script type="text/javascript">
    $(document).ready(function() {
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID

        //$('#datepicker0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});


        var x = 1; //initlal text box count
        $(add_button).click(function(e){
            $.ajax({
                type: 'POST',
                url: '../../FinanceRequests/checkstatus',
                dataType: "json",
                data: {groupid:$('#groupid').val()},
                success: function(data){
                    if(data.result==0)
                    {
                        e.preventDefault();
                        if(x < max_fields){ //max input box allowed
                            //text box increment
                            $('.total').before('<tr style="background-color: #ffffff;">' +
                                '<td><input type="text" class="form-control datepicker" name="New_Date[]" id="datepicker'+x+'"  value="<?php echo date("d-m-Y");?>">'+
                                '<input type="hidden" name="id[]" value="'+x+'"></td>' +
                                '<td><textarea rows="3" class="form-control" cols="60" id="newpurpose'+x+'" name="New_Purpose[]" ></textarea><span class="error"></span></td>' +
                                '<td><input type="text" class="form-control" placeholder="Amount" name="New_Amount[]" id="request_amount'+x+'"><span class="error"></span> </td>' +
                                //'<td><input type="text" class="form-control requestamount" data-id="'+x+'" name="New_rqstamount[]" id="requestamount'+x+'"></td>'+
                                '<td><select class="form-control accountshead" id="accountshead'+x+'" data-id="'+x+'" name="New_accountshead[]" >' +
                                '<option value="0">Select Account</option>' +
                                '<?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                                foreach($acnts AS $accounts):
                                    echo "<option value=".$accounts['id']." >".$accounts->name."</option>";
                                endforeach;?></select><span class="error"></span></td>' +
                                '<td><span id="advance'+x+'"></span></td>' +
                                '<td id="po'+x+'"></td>' +
                                '<td><span id="ledgerbal'+x+'"></span></td>' +
                                '<td><input type="text" class="form-control account_tds" name="New_tds[]" data-id="'+x+'" id="account_tds'+x+'">' +
                                '<input type="hidden" name="newtdsper[]" id="tdsper'+x+'" value=""></td>'+
                                '<td><span id="accountservtax'+x+'"></span><input type="hidden" name="New_tax[]" id="account_tax'+x+'" value="">' +
                                '<input type="hidden" name="newtaxper[]" id="taxper'+x+'" value=""></td>'+
                                '<td><span id="accountnet'+x+'"></span><input type="hidden" class="account_net" id="account_net'+x+'" name="New_net[]" value=""></td>'+
                                '<td><input type="radio" class="paymenttype paymentmode'+x+'" data-id="'+x+'" data-type="cash" name="newpayment'+x+'" value="1">Cash</td>'+
                                '<td><input type="radio" class="paymenttype paymentmode'+x+'" data-id="'+x+'" data-type="bank" name="newpayment'+x+'" value="2">Bank</td>' +
                                '<td><input type="checkbox" id="contraentry" class="contraentry" data-id="'+x+'" style="visibility: visible;" name="newcontraentry'+x+'" value="1">Contra</td>'+
                                '<td colspan="2"><select id="rqststatus'+x+'" name="New_status[]" data-id="'+x+'" class="form-control requeststatus" style="width:120px;">' +
                                '<option  value="0">Pending</option>' +
                                '<option value="1">Approve</option>' +
                                '<option value="5">Draft</option>' +
                                '<option value="2">Deny</option></select></td>'+
                                '<td><a href="#" class="remove_field">Remove</a></td></tr>');
                            //$(wrapper).append('<div><input type="text" name="mytext[]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
                            $('#datepicker'+x+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
                            x++;
                        }
                    }
                    else
                    {
                        alert("Cannot Add Request Now");
                    }
                }
            });

        });

        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        })
    });
</script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/request.js" type="text/javascript"></script>
    <h1>Requests</h1>
    <form method="POST" action="" id="approvalfrom">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>User</th>
                        <td><?php echo $username;?></td>
                        <th>Place</th>
                        <td colspan="4">
                            <input type="hidden" value="<?php echo $place;?>" name="placename"><?php echo $place;?></td>
                        <th>Project</th>
                        <td colspan="5"><?php echo $project;?></td>
                        <th colspan="4"><input type="button" style="float: right;width: 100%;" class="btn btn-primary add_field_button" id="addmore" value="Add Requests"></th>
                    </tr>
                    <tr style="background-color: #fff"><td colspan="17"> </td></tr>
                    <tr>
                        <th>Date</th>
                        <th>Purpose</th>
                        <th>Amount</th>
                        <!--<th style="width: 1px;">Approved amount</th>-->
                        <th>Account Head</th>
                        <th></th>
                        <th></th>
                        <th style="width: 1px;">Ledger Balance</th>
                        <th style="width: 1px;">TDS Amount</th>
                        <th style="width: 1px;">Service Tax</th>
                        <th style="width: 1px;">Net Amount</th>
                        <th colspan="2"><b>Payment type</b></th>
                        <th colspan="4"></th>
                    </tr>
                </thead>
                <tbody class="input_fields_wrap" id="grouprequests">
                    <?php
                    $cashtotal=0;
                    $banktotal=0;
                    foreach($datarows AS $key=>$data):
                        if($data['payment_type']==1):
                            $type="Cash Bills";
                            $billno=Bills::model()->findByPk($data['bill_no'])->billno;
                        elseif($data['payment_type']==2):
                            $type="Credit Bills";
                            $billno=Bills::model()->findByPk($data['bill_no'])->billno;
                        elseif($data['payment_type']==3):
                            $type="Advances";
                            $billno=Vendors::model()->findByPk($data['bill_no'])->Name;
                        elseif($data['payment_type']==4):
                            $type="Transfers";
                            $billno=Vendors::model()->findByPk($data['bill_no'])->Name;
                        elseif($data['payment_type']==5):
                            $type="Withdrawals";
                        elseif($data['payment_type']==6):
                            $type="Statutory Payments";
                            $billno=Vendors::model()->findByPk($data['bill_no'])->Name;
                        elseif($data['payment_type']==7):
                            $type="Miscellaneous";
                        endif;
                        $content='<table>
                                    <tr><th>Payment Type</th><th>Bill no</th></tr>
                                    <tr><td>'.$type.'</td><td>'.$billno.'</td></tr></table>';
                    ?>
                    <tr id="tempresrow<?php echo $data['Id']; ?>">
                        <td>
                            <input type="hidden" value="<?php echo date('d-m-Y',strtotime($data['date'])); ?>" name="date">
                            <?php echo $data['date']; ?>
                            <input type="hidden" value="<?php echo $data['group_id'];?>" id="groupid" name="groupid">
                        </td>
                        <td>
                            <input type="hidden" value="<?php echo $data['place'];?>" name="place" id="place">
                            <input type="hidden" value="<?php echo $data['project_id'];?>" name="projectid">
                            <a href="javascript:void(0)" data-toggle="popover" data-html="true" title="Request Payment Type" data-trigger="focus" data-content="<?php echo $content;?>"><?php echo $data['Purpose']; ?></a>
                            <?php //echo $data['Purpose']; ?>
                            <input type="hidden" name="purpose[]" value="<?php echo $data['Purpose']; ?>">
                        </td>
                        <td>
                            <?php echo number_format((float)$data['Amount'], 2); ?>
                            <input type="hidden" value="<?php echo $data['Amount']; ?>" id="request_amount<?php echo $data['Id']; ?>" name="request_amount">
                            <input type="hidden" class="form-control requestid" value="<?php echo $data['Id']; ?>" name="requestid[]">
                        </td>
                        <!--<td>
                            <input type="text" data-id="<?php /*echo $data['Id']; */?>" class="form-control requestamount" value="<?php /*echo $data['alloted_amount']; */?>" name="requestamount[]" id="requestamount<?php /*echo $data['Id']; */?>">
                            <span class="error"></span>
                            <input type="hidden" class="form-control requestid" value="<?php /*echo $data['Id'];*/?>" id="requestid" name="requestid[]">
                        </td>-->
                        <?php if($data['account_id']==0 || $data['account_id']!=0):?>
                        <td>
                            <select class="form-control accountshead" id="accountshead<?php echo $data['Id']; ?>" data-id="<?php echo $data['Id']; ?>" name="accountshead[]" >
                            <option value="0">Select Account</option>
                            <?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                            foreach($acnts AS $accounts):
                                if($data['account_id']==$accounts->id):$selected='selected';else:$selected='';endif;
                                echo "<option value='".$accounts->id."' id='acnts' $selected>".$accounts->name."</option>";
                            endforeach;?>
                            </select>
                            <span class="error"></span>
                        </td>
                        <td><span id="advancecheck<?php echo $data['Id']; ?>"></span></td>
                        <td id="po<?php echo $data['Id']; ?>"></td>
                        <td>
                            <span id="ledgerbal<?php echo $data['Id']; ?>"></span>
                            <input type="hidden" id="ledgbal<?php echo $data['Id']; ?>">
                        </td>
                        <?php else:?>
                        <td>
                            <?php echo $data['name'];?>
                            <input type="hidden" name="accountshead[]" id="accountshead<?php echo $data['Id'];?>" value="<?php echo $data['account_id'];?>">
                        </td>
                        <td><span id="advancecheck<?php echo $data['Id']; ?>"></span></td>
                        <td></td>
                        <td>
                            <span id="ledgerbal<?php echo $data['Id']; ?>"></span>
                            <input type="hidden" id="ledgbal<?php echo $data['Id']; ?>">
                        </td>
                        <?php endif;?>
                        <td>
                            <!--<span id="accountstds<?php /*echo $data['Id'];*/?>"><?php /*echo $data['tds'];*/?></span>-->
                            <input type="text" class="form-control account_tds" name="account_tds[]" data-id="<?php echo $data['Id'];?>" id="account_tds<?php echo $data['Id'];?>" value="<?php echo $data['tds'];?>">
                            <input type="hidden" name="tdsper[]" id="tdsper<?php echo $data['Id'];?>" value="<?php echo $data['tdsperc'];?>">
                        </td>
                        <td>
                            <span id="accountservtax<?php echo $data['Id'];?>"><?php echo $data['tax'];?></span>
                            <input type="hidden" name="account_tax[]" id="account_tax<?php echo $data['Id'];?>" value="<?php echo $data['tax'];?>">
                            <input type="hidden" name="taxper[]" id="taxper<?php echo $data['Id'];?>" value="<?php echo $data['servicetax'];?>">
                        </td>
                        <td>
                            <span id="accountnet<?php echo $data['Id'];?>"><?php echo number_format((float)$data['netamount'], 2);?></span>
                            <input type="hidden" class="account_net" id="account_net<?php echo $data['Id'];?>" name="account_net[]" value="<?php echo $data['netamount'];?>">
                        </td>

                        <td>
                            <input type="radio" <?php echo ($data['payment']=='1'?'checked="checked"':'');?> class="paymenttype paymentmode<?php echo $data['Id']; ?>" data-id="<?php echo $data['Id']; ?>" data-type="cash" name="payment<?php echo $data['Id']; ?>" value="1">Cash
                        </td>
                        <td>
                            <input type="radio" <?php echo ($data['payment']=='2'?'checked="checked"':'');?>class="paymenttype paymentmode<?php echo $data['Id']; ?>"" data-id="<?php echo $data['Id']; ?>" data-type="bank" name="payment<?php echo $data['Id']; ?>" value="2">Bank
                        </td>
                        <td>
                            <input type="checkbox" <?php echo ($data['contra']=='1'?'checked="checked"':'');?> id="contraentry<?php echo $data['Id']; ?>" class="contraentry" style="visibility: visible;" data-id="<?php echo $data['Id']; ?>" name="contraentry<?php echo $data['Id']; ?>" value="1">Contra
                        </td>
                        <td colspan="3" >
                            <select style="width:120px;" id="rqststatus<?php echo $data['Id']; ?>" name="requeststatus[]" data-id="<?php echo $data['Id']; ?>" class="form-control requeststatus">
                                <option  value="0" <?php echo ($data['Status']=='0'?'selected':'');?> >Pending</option>
                                <option value="1" <?php echo ($data['Status']=='1'?'selected':'');?>>Approve</option>
                                <option value="5" <?php echo ($data['Status']=='5'?'selected':'');?>>Draft</option>
                                <option value="2" <?php echo ($data['Status']=='2'?'selected':'');?>>Deny</option>
                            </select>
                        </td>
                    </tr>
                    <?php $amount=$amount + $data['Amount'];$allotamount=$allotamount + $data['alloted_amount'];$netamount=$netamount + $data['netamount'];?>
                    <?php
                    if($data['payment']=='1'):
                        $cashtotal=$cashtotal+$data['alloted_amount'];
                    elseif($data['payment']=='2'):
                        $banktotal=$banktotal+$data['alloted_amount'];
                    endif;
                    endforeach; ?>
                    <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);if($user['superuser']==2): ?>
                    <tr class="total">
                        <th colspan="2">Total</th>
                        <th><?php echo $amount; ?></th>
                        <th></th>
                        <th colspan="5"></th>
                        <th><span id="totalnetamnt"><?php echo $netamount; ?></span></th>
                        <th></th>
                        <th></th>
                        <th colspan="4"></th>
                    </tr>
                    <?php else:?>
                    <tr class="total">
                        <th colspan="2">Total</th>
                        <th><?php echo number_format((float)$amount, 2); ?></th>
                        <!--<th id="requestratetotal"><?php /*echo number_format((float)$allotamount, 2); */?></th>-->
                        <th colspan="6"></th>
                        <th><span id="totalnetamount"><?php echo number_format((float)$netamount, 2); ?></span></th>
                        <th> <span id="cashtotal"><?php echo number_format((float)$cashtotal, 2);?></span><input type="hidden" value="" name="cashamount" id="cashamount"></th>
                        <th > <span id="banktotal"><?php echo number_format((float)$banktotal, 2);?></span><input type="hidden" value="" name="bankamount" id="bankamount"></th>
                        <th colspan="4"></th>
                    </tr>
                    <tr id="transferrow">
                        <th colspan="10"></th>
                        <th><span id="totalamount">0.00</span>
                            <input type="hidden" name="totalamount" id="amounttotal"></th>
                        <th><!--<input type="radio" class="payment" name="paymentmode" value="1" >Cash--> </th>
                        <th colspan="5"><!--<input type="radio" class="payment" name="paymentmode" value="2" >Bank--></th>
                    </tr>
                    <?php endif;?>
                    <tr>
                        <td colspan="8"></td>
                        <td colspan="3"><button type="submit" class="btn btn-primary" id="approverequest" name="approverequest" value="1">Save</button></td>
                        <td colspan="3"><button type="submit" class="btn btn-primary" id="saveasdraft" name="saveasdraft" value="3">Save as draft</button></td>
                        <td colspan="3"><button type="button"  name="cancelrequest" class="btn btn-primary" id="cancelrequest" onclick="goBack()">Cancel</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
<script>
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover();
    });
</script>