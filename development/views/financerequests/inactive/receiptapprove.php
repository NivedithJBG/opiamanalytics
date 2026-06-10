<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/receipt.js" type="text/javascript"></script>
<h1>Receipts</h1>
    <form method="POST" action="" id="receiptform">
        <table class="table table-bordered ">
            <thead>
            <tr>
                <th><b>Date</b></th>
                <th><b>User</b></th>
                <th style="width: 250px;"><b>Place</b></th>
                <th><b>Amount</b></th>
                <th style="width: 200px;"><b>Purpose</b></th>
                <th><b>Account Head</b></th>
                <th colspan="2"><b>Receipt type</b></th>
                <th ></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo date('d M Y',strtotime($model->date));?></td>
                <td><?php echo $username;?></td>
                <td><?php echo $project;?></td>
                <td><?php echo number_format((float)$model->Amount, 2);?><input type="hidden" name="receiptamount" value="<?php echo $model->Amount;?>"></td>
                <td><?php echo $model->Purpose;?></td>
                <td>
                    <select class="form-control accountshead" id="accountshead" name="accountshead" style="width: 125px;">
                        <option value="0">Select Account</option>
                        <?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                        foreach($acnts AS $accounts):
                            if($model->debitacnt==$accounts->id):$selected='selected';else:$selected='';endif;
                            echo "<option value='".$accounts->id."' id='acnts' $selected>".$accounts->name."</option>";
                        endforeach;?>
                    </select><span class="error"></span>
                </td>
                <td><input type="radio" <?php echo ($model['payment']=='1'?'checked="checked"':'');?> class="paymenttype" name="payment" value="1">Cash</td>
                <td><input type="radio" <?php echo ($model['payment']=='2'?'checked="checked"':'');?> class="paymenttype" name="payment" value="2">Bank</td>
                <td><select class="form-control" id="receiptstatus" name="receiptstatus" >
                    <option value="0" selected="selected">Pending</option>
                    <option value="1" >Approve</option>
                    <option value="4" <?php echo ($model['Status']=='4'?'selected':'');?> >Save as draft</option>
                    <option value="2" >Deny</option>
                    </select>
                    <span class='error'></span>
                </td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td ><input type="submit" class="btn btn-primary" value="Save" name="Receipt_Approve" id="receiptapprove"></td>
                <td ><input type="button" class="btn btn-primary" value="Cancel" name="Receipt_Cancel" onclick="goBack();"></td>
            </tr>
            </tbody>
        </table>
    </form>