<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/bills.js" type="text/javascript"></script>
<h1>Bills</h1>

<form method="POST" action="" id="billapprovform">
    <table class="table table-bordered ">
        <thead>
        <tr>
            <th>Bill no</th>
            <th>Date</th>
            <th>Due Date</th>
            <th colspan="3">Place</th>
            <th>User</th>
            <th colspan="2">Party</th>
        </tr>
        <tr style="background-color: #ffffff;">
            <td><?php echo $dataProvider['billno'];?><input type="hidden" name="bill_id" value="<?php echo $dataProvider['bill_id'];?>"></td>
            <td><?php echo $dataProvider['date'];?></td>
            <td>
                <?php if($billmaster['bill_type']!=5):echo 	$billmaster['duedate'];else:echo "";endif;?></td>
            <td colspan="3"><?php echo $dataProvider['Name'];?></td>
            <td><?php echo $dataProvider['username'];?></td>
            <td colspan="2"><?php if($billmaster['bill_type']!=5):echo $dataProvider['accountname'];else:echo $dataProvider['bill_party'];endif;?></td>
        </tr>
        <tr style="background-color: #fff"><td colspan="9"> </td></tr>
        <tr>
            <th><b>SI No</b></th>
            <th colspan="4"><b>Item</b></th>
            <th><b>Unit</b></th>
            <th><b>Rate</b></th>
            <th><b>Quantity</b></th>
            <th><b>Amount</b></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data AS $key=>$billitems):?>
            <tr><td><?php echo $key+1?></td>


                <td colspan="4"><?php echo $billitems['item']?><input type="hidden" id="itemid" value="<?php echo $billitems['itemid'];?>" name="itemid[]" ></td>
                <td><?php echo $billitems['unit']?></td>
                <td><?php echo $billitems['rate']?></td>
                <td><?php echo $billitems['quantity']?></td>

                <td><?php echo number_format((float)$billitems['amount'], 2);?></td>

            </tr>
        <?php endforeach;?>
        <tr >
            <td colspan="3"><b>Total Amount</b></td>
            <td colspan="6" style="text-align: right"><?php echo number_format((float)$totalamount, 2);?></td>
        </tr>
        <?php
        $user=User::model()->active()->findbyPk(Yii::app()->user->id);
        if($user['superuser']!=2):
            if($billmaster['bill_type']!=5):
            ?>
            <tr>
                <td colspan="6">
                    <?php if($billitems['accountid']==0):?>
                <td>
                    <select id="accounthead" name="accounthead" class="form-control accounthead" data-id="<?php echo $billitems['itemid']; ?>">
                        <option value="0">Select Account</option>
                        <?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                        foreach($acnts AS $accounts):
                            if($data['account_id']==$accounts->id):$selected='selected';else:$selected='';endif;
                            echo "<option value='".$accounts->id."' id='acnts' $selected>".$accounts->name."</option>";
                        endforeach;?>
                    </select><span class='error'></span>
                </td>
                <?php else:?>
                    <td><span id="account_head"><?php echo $billitems['accountname']; ?></span><input type="hidden" value="<?php echo $billitems['accountid']; ?>" name="accounthead[]"></td>
                <?php endif;?>
                </td>
                <td style="width: 200px"><b>Status</b></td>
                <td style="width: 170px"><select class="form-control" id="billstatus" name="billstatus" >
                        <option value="0" selected="selected">Pending</option>
                        <option value="1">Approve</option>
                        <option value="2">Deny</option>
                    </select></td>

            </tr>
        <?php endif;endif;?>
        <?php if($billmaster['bill_type']!=5): ?>
        <tr>
            <td colspan="7"></td>
            <td><input type="submit" class="btn btn-primary" name="Bills_approve" value="Save"></td>
            <td><input type="submit" class="btn btn-primary" value="Cancel" onclick="goBack();"></td>
        </tr>
        <?php else:?>
            <tr>
                <td colspan="8"></td>
                <td><input type="submit" class="btn btn-primary" value="Cancel" onclick="goBack();"></td>
            </tr>
        <?php endif;?>
        </tbody>
    </table>
</form>