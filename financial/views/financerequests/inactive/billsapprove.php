<script>
    function goBack() {
        window.history.back()
    }
    $('#approvebill').click(function () {
        alert('hii');
        $('.accounthead').each(function () {
            var id = $(this).attr('data-id');
            if ($(this).val() == '') {
                $("#accounthead" + id).next("span").html('Enter Purpose').show('slow');
                error = 1;
            }
        });

    });
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
            <th>User</th>
            <th colspan="2">Project</th>
            <th colspan="3">Vendor</th>
        </tr>
        <tr style="background-color: #ffffff;">
            <td><?php echo $dataProvider['billno']; ?>
                <input type="hidden" name="bill_id" value="<?php echo $dataProvider['bill_id']; ?>">
            </td>
            <td><?php echo $dataProvider['date']; ?></td>
            <td><?php echo $billmaster['duedate']; ?></td>
            <td><?php echo $dataProvider['username']; ?></td>
            <td colspan="2"><?php echo $dataProvider['Name']; ?></td>
            <td colspan="3"><?php echo Vendors::model()->findbyPk($billmaster['party'])->Name; ?></td>
        </tr>
        <tr style="background-color: #fff">
            <td colspan="9"></td>
        </tr>
        <tr>
            <th><b>SI No</b></th>
            <th colspan="3"><b>Item</b></th>
            <th><b>Unit</b></th>
            <th><b>Quantity</b></th>
            <th><b>Rate</b></th>
            <th><b>Tax Amount</b></th>
            <th><b>Amount</b></th>
        </tr>
        </thead>
        <tbody>

        <?php $billitems=Billitems::model()->findAll(array('condition'=>'billid = '.$billmaster->bill_id.''))?>
        <?php foreach ($billitems AS $key => $billitem): ?>
            <tr>
                <td><?php echo $key + 1 ?></td>
                <?php
                $pitem=PurchaseItems::model()->findByPk($billitem['item']);
                $res=Resources::model()->findByPk($pitem->PI_Resource_Id);
                if($res->Name=='diesel'):
                ?>
                <td colspan="3">Diesel<input type="hidden" id="itemid" value="<?php echo $billitem['itemid']; ?>" name="itemid[]"></td>
                <?php else:?>
                <td colspan="3">
                    <?php echo Resources::model()->findbyPk(EstimateProjectResources::model()->findbyPk(PurchaseItems::model()->findbyPk($billitem['item'])->PI_Resource_Id)->Resource_Id)->Name ?>
                    <input type="hidden" id="itemid" value="<?php echo $billitem['itemid']; ?>" name="itemid[]">
                </td>
                <?php endif;?>
                <?php if($res->Name=='diesel'):?>
                <td><?php echo $res->Unit;?></td>
                <?php else:?>
                <td><?php echo Resources::model()->findbyPk(EstimateProjectResources::model()->findbyPk(PurchaseItems::model()->findbyPk($billitem['item'])->PI_Resource_Id)->Resource_Id)->Unit ?></td>
                <?php endif;?>
                <td><?php echo $billitem->quantity; ?></td>
                <td><?php echo $billitem->rate; ?></td>
                <td><?php echo $billitem->item_Tax_Amount; ?></td>
                <td><?php echo number_format((float)$billitem->amount, 2); ?></td>
                <!--<td><?php /*echo $this->projectItemPrevQuanatity($billitems['item_Project_Id'], $billitems['item']); */?></td>
                <td><?php /*echo $this->projectItemPrevAmount($billitems['item_Project_Id'], $billitems['item']); */?></td>-->
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><b>Total Amount</b></td>
            <td colspan="8" style="text-align: right"><?php echo number_format((float)$totalamount, 2); ?></td>
        </tr>
        <?php $user = User::model()->active()->findbyPk(Yii::app()->user->id);
        if ($user['superuser'] != 2): ?>
            <tr>
                <td> Account Head</td>
                <?php if ($billitems['accountid'] == 0): ?>
                    <td colspan="6">
                        <select id="accounthead" name="accounthead" class="form-control accounthead"
                                data-id="<?php echo $billitems['itemid']; ?>">
                            <option value="0">Select Account</option>
                            <?php $acnts = AccountsItem::model()->findAll(array('order' => 'name ASC'));
                            foreach ($acnts AS $accounts):
                                if ($data['account_id'] == $accounts->id):$selected = 'selected';
                                else:$selected = '';endif;
                                echo "<option value='" . $accounts->id . "' id='acnts' $selected>" . $accounts->name . "</option>";
                            endforeach; ?>
                        </select><span class='error'></span>
                    </td>
                <?php else: ?>
                    <td colspan="6"><span id="account_head"><?php echo $billitems['accountname']; ?></span><input
                            type="hidden" value="<?php echo $billitems['accountid']; ?>" name="accounthead"></td>
                <?php endif; ?>

                <td style="width: 200px"><b>Status</b></td>
                <td style="width: 170px"><select class="form-control" id="billstatus" name="billstatus">
                        <option value="0" selected="selected">Pending</option>
                        <option value="1">Approve</option>
                        <option value="2">Deny</option>
                    </select></td>

            </tr>
        <?php endif; ?>
        <tr>
            <td colspan="7"></td>
            <td><input type="submit" class="btn btn-primary" name="Bills_approve" value="Save" id="billsapprove"></td>
            <td><input type="submit" class="btn btn-primary" value="Cancel" onclick="goBack();"></td>
        </tr>
        </tbody>
    </table>
</form>