<script>
    function goBack() {

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
            <th>User</th>
            <th>Wo No</th>
            <!--<th >From</th>
            <th >To</th>-->
            <th colspan="2">Party</th>
            <th colspan="3">Project</th>
        </tr>

        <tr style="background-color: #ffffff;">
            <td><?php echo $dataProvider['billno']; ?><input type="hidden" name="bill_id"
                                                             value="<?php echo $dataProvider['bill_id']; ?>"></td>
            <td><?php echo $dataProvider['date']; ?></td>
            <td><?php echo $dataProvider['username']; ?></td>
            <td ><?php echo $billmaster['bill_WO_No']; ?></td>
            <!--<td ><?php /*echo $billmaster['Bill_period_start']; */?></td>
            <td ><?php /*echo $billmaster['Bill_period_end']; */?></td>-->
            <td colspan="2"><?php echo Vendors::model()->findByPk($billmaster['party'])->Name; ?></td>
            <td colspan="3"><?php echo Projects::model()->findByPk($billmaster['place'])->Name; ?></td>

        </tr>

        <tr style="background-color: #fff">
            <td colspan="9"></td>
        </tr>
        <tr>
            <th><b>SI No</b></th>


            <th colspan="2"><b>Item</b></th>
            <th><b>Unit</b></th>
            <th><b>Rate</b></th>
            <th><b>Quantity upto lastbill</b></th>
            <th><b>Amount upto lastbill</b></th>
            <th><b>Current Quantity</b></th>
            <th><b>Current Amount</b></th>


        </tr>
        </thead>
        <tbody>
        <?php foreach ($data AS $key => $billitems): ?>
            <tr>
                <td><?php echo $key + 1 ?></td>
                <?php
                $workitem = WorkItems::model()->findByPk($billitems['item']);

                if ($workitem->WI_WBS == 1):

                    $name = Investments::model()->findByPk(ProjectEstimate::model()->findByPk($workitem->WI_Item)->Item_Id)->IN_Name;
                elseif ($workitem->WI_WBS == 2):
                    $name = ProjectSetup::model()->findByPk(ProjectEstimate::model()->findByPk($workitem->WI_Item)->Item_Id)->PS_Name;
                elseif ($workitem->WI_WBS == 3):
                    $name = Products::model()->findByPk(ProjectEstimate::model()->findByPk($workitem->WI_Item)->Item_Id)->Name;
                elseif ($workitem->WI_WBS == 4):
                    $name = Logistics::model()->findByPk(ProjectEstimate::model()->findByPk($workitem->WI_Item)->Item_Id)->Name;
                elseif ($workitem->WI_WBS == 5):
                    $name = Construction::model()->findByPk(ProjectEstimate::model()->findByPk($workitem->WI_Item)->Item_Id)->CO_Name;
                elseif ($workitem->WI_WBS == 6):
                    $name = MajorConsumables::model()->findByPk(ProjectEstimate::model()->findByPk($workitem->WI_Item)->Item_Id)->MC_Name;
                elseif ($workitem->WI_WBS == 7):
                    $name = Purchasedinputs::model()->findByPk(ProjectEstimate::model()->findByPk($workitem->WI_Item)->Item_Id)->Name;
                elseif ($workitem->WI_WBS == 8):
                    $name = Overheads::model()->findByPk(ProjectEstimate::model()->findByPk($workitem->WI_Item)->Item_Id)->Name;
                endif;
                ?>
                <td colspan="2"><?php echo $name ?><input type="hidden" id="itemid"
                                                           value="<?php echo $name ?>" name="itemid[]">
                </td>
                <td><?php echo $billitems['unit'] ?></td>
                <td><?php echo $billitems['rate'] ?></td>
                <td><?php echo $this->projectItemPrevQuanatity($billitems['item_Project_Id'], $billitems['item']); ?></td>
                <td><?php echo number_format(($this->projectItemPrevAmount($billitems['item_Project_Id'], $billitems['item'])),2); ?></td>

                <td><?php echo $billitems['quantity'] ?></td>

                <td><?php echo number_format((float)$billitems['amount'], 2); ?></td>

            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><b>Gross Amount</b></td>
            <td colspan="6" style="text-align: right"><?php echo number_format($billmaster->bill_Total_amount,2) ?></td>
        </tr>
        <tr>
            <td colspan="3"><b>Service Tax Payable</b></td>
            <td colspan="6" style="text-align: right"><?php echo $billmaster->bill_Total_amount * ($billmaster->bill_Tax_Percentage / 100); ?></td>
        </tr>
        <tr>
            <td colspan="3"><b>TDS Payable</b></td>
            <td colspan="6" style="text-align: right"><?php echo $billmaster->bill_Total_amount * ($billmaster->bill_TDS_Percentage / 100); ?></td>
        </tr>

        <tr>
            <td colspan="3"><b>Vat Payable</b></td>
            <td colspan="6" style="text-align: right"><?php echo  $billmaster->bill_Total_amount * ($billmaster->bill_Other_Deductions / 100); ?></td>
        </tr>
        <tr>
            <td colspan="3" ><b>Total Including tax</b></td>
            <td colspan="6" style="text-align: right"><?php echo number_format(($billmaster->bill_Total_amount+($billmaster->bill_Total_amount * ($billmaster->bill_Other_Deductions / 100))+($billmaster->bill_Total_amount * ($billmaster->bill_TDS_Percentage / 100))+($billmaster->bill_Total_amount * ($billmaster->bill_Tax_Percentage / 100))),2)  ; ?></td>
        </tr>
        <?php $amountinclusive=($billmaster->bill_Total_amount+($billmaster->bill_Total_amount * ($billmaster->bill_Other_Deductions / 100))+($billmaster->bill_Total_amount * ($billmaster->bill_TDS_Percentage / 100))+($billmaster->bill_Total_amount * ($billmaster->bill_Tax_Percentage / 100)));?>
        <tr>
            <td colspan="3"><b>Less Retension </b></td>
            <td colspan="6" style="text-align: right"><?php echo $billmaster->bill_Total_amount * ($billmaster->bill_Retention_Percentage / 100); ?></td>
        </tr>
        <tr>
            <td colspan="3"><b>Net Amount Payable </b></td>
            <td colspan="6" style="text-align: right"><?php echo number_format($amountinclusive-($billmaster->bill_Total_amount * ($billmaster->bill_Retention_Percentage / 100)),2); ?></td>
        </tr>
        <?php if($billmaster->Status!=1):?>
            <?php $user = User::model()->active()->findbyPk(Yii::app()->user->id);
            if ($user['superuser'] != 2): ?>
            <tr>
                <td colspan="3" style="width: 200px"><b>Account Head</b></td>
                <?php if ($billitems['accountid'] == 0): ?>
                    <td colspan="2">
                        <select id="accounthead" name="accounthead"
                                class="form-control accounthead" data-id="<?php echo $billitems['itemid']; ?>">
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
                    <td colspan="2">
                        <span id="account_head"><?php echo $billitems['accountname']; ?></span>
                        <input type="hidden" value="<?php echo $billitems['accountid']; ?>" name="accounthead"></td>
                <?php endif; ?>

                <td ><b>Deduction Account</b></td>
                <?php if ($billitems['accountid'] == 0): ?>
                    <td >
                        <select id="accountheaddeduction" name="deductionAccount"
                                class="form-control accounthead" data-id="<?php echo $billitems['itemid']; ?>">
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
                    <td>
                        <span id="account_head"><?php echo $billitems['accountname']; ?></span>
                        <input type="hidden" value="<?php echo $billitems['accountid']; ?>" name="deductionAccount"></td>
                <?php endif; ?>
                <td style="width: 200px"><b>Status</b></td>
                <td style="width: 170px">
                    <select class="form-control" id="billstatus" name="billstatus">
                        <option value="0" selected="selected">Pending</option>
                        <option value="1">Approve</option>
                        <option value="2">Deny</option>
                    </select>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td colspan="7"></td>
            <td>
                <input type="submit" class="btn btn-primary" name="Bills_approve" value="Save" "></td>
            <td><input type="submit" class="btn btn-primary" value="Cancel" onclick="goBack();"></td>
        </tr>
        <?php else: ?>
            <tr>
                <td colspan="8"></td>
                <td><input type="submit" class="btn btn-primary" value="Cancel" onclick="goBack();"></td>
            </tr>
        <?php endif;?>
        </tbody>
    </table>
</form>