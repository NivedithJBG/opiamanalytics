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
            <th>User</th>
            <th>Designation</th>
            <th>Project</th>
            <th>Purpose</th>
        </tr>
        <tr style="background-color: #ffffff;">

            <td><?php echo $dataProvider['username']; ?></td>
            <td><?php echo User::itemAlias("AdminStatus",$dataProvider['User_Id']); ?></td>
            <td><?php echo Projects::model()->findByPk($billmaster['place'])->Name; ?></td>
            <td><?php echo $dataProvider['bill_purpose']; ?></td>
        </tr>
        <tr>
            <th>Start Date</th>
            <th>End Date</th>
            <th colspan="2">Fare</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data AS $key => $billitems):
            if($billitems['travelmode']==1):
                $travelmode='Air';
            elseif($billitems['travelmode']==2):
                $travelmode='Train';
            elseif($billitems['travelmode']==3):
                $travelmode='Bus';
            elseif($billitems['travelmode']==4):
                $travelmode='Auto';
            endif;
            ?>
            <tr>
            <td><?php echo date('d-m-Y', strtotime($billitems['startdate'])); ?></td>
            <td><?php echo date('d-m-Y', strtotime($billitems['enddate'])); ?></td>
            <td colspan="2"><?php echo $travelmode; ?></td>
            </tr>
        <?php endforeach;?>
        <tr>
            <td><b>Total Amount Incurred for conveyance Rs</b></td>
            <td colspan="3" style="text-align: right"><?php echo number_format($billmaster->bill_travel_amount,2) ?></td>
        </tr>
        <tr>
            <td><b>Boarding charges for</b></td>
            <td colspan="3" style="text-align: right"><?php echo number_format($billmaster->bill_Boarding_amount,2) ?></td>
        </tr>
        <tr>
            <td><b>Lodging charges for</b></td>
            <td colspan="3" style="text-align: right"><?php echo number_format($billmaster->bill_Lodging_amount,2) ?></td>
        </tr>
        <tr>
            <td><b>Gross Amount</b></td>
            <td colspan="3" style="text-align: right">
                <?php echo number_format($billmaster->bill_Total_amount,2) ?>
                <input type="hidden" id="itemid" value="<?php echo $name ?>" name="itemid[]">
                <input type="hidden" name="bill_id" value="<?php echo $dataProvider['bill_id']; ?>">
            </td>
        </tr>
        <?php if($billmaster->Status!=1):?>
        <tr>
            <td ><b>Account Head</b></td>
            <?php if ($billitems['accountid'] == 0): ?>
                <td>
                    <select id="accounthead" name="accounthead"
                            class="form-control accounthead" data-id="<?php echo $billitems['itemid']; ?>">
                        <option value="0">Select Account</option>
                        <?php $acnts = AccountsItem::model()->findAll(array('order' => 'name ASC'));
                        foreach ($acnts AS $accounts):

                            echo "<option value='" . $accounts->id . "' id='acnts' >" . $accounts->name . "</option>";
                        endforeach; ?>
                    </select><span class='error'></span>
                </td>
            <?php else: ?>
                <td><span id="account_head"><?php echo $billitems['accountname']; ?></span><input type="hidden"
                                                                                                              value="<?php echo $billitems['accountid']; ?>"
                                                                                                              name="accounthead">
                </td>
            <?php endif; ?>
            <td><b>Status</b></td>
            <td><select class="form-control" id="billstatus" name="billstatus">
                    <option value="0" selected="selected">Pending</option>
                    <option value="1">Approve</option>
                    <option value="2">Deny</option>
                </select></td>
        </tr>
        <?php endif;?>
        <tr>
            <td colspan="2"></td>
            <td>
                <input type="submit" class="btn btn-primary" name="Bills_approve" value="Save" "></td>
            <td><input type="submit" class="btn btn-primary" value="Cancel" onclick="goBack();"></td>
        </tr>
        </tbody>
    </table>
</form>