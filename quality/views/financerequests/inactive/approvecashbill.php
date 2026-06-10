<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<h1>Approve Cashbill</h1>
<form method="POST" action="" id="receiptform">
    <table class="table table-bordered ">
        <thead>
        <tr>
            <th>Project</th>
            <th>Activity</th>
            <th>Accounthead</th>
            <th>Purpose</th>
            <th>Amount</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo Projects::model()->findByPk($cashbill['project_id'])->Name;?></td>
            <td><?php echo $name;?></td>
            <td>
                <select class="form-control accountshead" id="accountshead" name="accountshead">
                    <option value="none">Select Account</option>
                    <?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                    foreach($acnts AS $accounts):
                        if($cashbill['accounthead']==$accounts->id):$selected='selected';else:$selected='';endif;
                        echo "<option value='".$accounts->id."' id='acnts' $selected>".$accounts->name."</option>";
                    endforeach;?>
                </select><span class="error"></span>
            </td>
            <td><input type="text" class="form-control" name="cashbillpurpose" id="cashbillpurpose" value="<?php echo $cashbill['purpose'];?>"><span class='error'></span></td>
            <td><input type="text" class="form-control" name="cashbillamount" id="cashbillamount" value="<?php echo $cashbill['amount'];?>"><span class='error'></span></td>
            <td><select class="form-control" id="cashbillstatus" name="cashbillstatus" >
                    <option value="0" selected="selected">Pending</option>
                    <option value="1" >Approve</option>
                    <option value="2" >Deny</option>
                </select>
                <span class='error'></span>
            </td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td ><input type="submit" class="btn btn-primary" value="Save" name="Cashbill_Approve" id="cashbillapprove"></td>
            <td ><input type="button" class="btn btn-primary" value="Cancel" name="Cashbill_Cancel" onclick="goBack();"></td>
        </tr>
        </tbody>
    </table>
</form>
<script type="text/javascript">
    $(function(){
        $('#cashbillapprove').click(function(){
            var error=0;
            $('.error').hide();
            if ($('#accountshead').val()=='none'){
                $('#accountshead').next("span").html('Select Accounthead').show('slow');
                error=1;
            }
            if ($('#cashbillpurpose').val()==''){
                $('#cashbillpurpose').next("span").html('Enter Purpose').show('slow');
                error=1;
            }
            if(!$.isNumeric($('#cashbillamount').val()))
            {
                $('#cashbillamount').next("span").html('Enter Valid Amount').show('slow');
                error=1;
            }
            if ($('#cashbillamount').val()==''){
                $('#cashbillamount').next("span").html('Enter Amount').show('slow');
                error=1;
            }
            if (error==0){
                return true;
            }
            else {
                return false;
            }
        });
    })
</script>