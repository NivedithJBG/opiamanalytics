<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/journal.js" type="text/javascript"></script>
<h1>Journals</h1>
<form method="POST" action="" id="journalapprovform">
    <table class="table table-bordered" style="width: 70%;margin: auto;">
        <thead>
        <tr>
            <th>Date</th>
            <th>User</th>
            <th>Project</th>
            <th>Place</th>
        </tr>
        <tr style="background-color: #ffffff;">
            <td><?php echo $dataProvider['date'];?>
                <input type="hidden" name="journalid" value="<?php echo $dataProvider['id'];?>">
            </td>
            <td><?php echo $dataProvider['username'];?></td>
            <td><?php echo $dataProvider['Name'];?></td>
            <td><?php echo Projects::model()->findByPk($dataProvider['place'])->Name;?></td>
        </tr>
        </thead>
    </table>
    <table style="width: 70%;margin: auto">
        <tbody>
        <?php if(count($debits)>0):?>
            <tr>
                <th colspan="2"><b>Narration</b></th>
                <th><b>Debit Account</b></th>
                <th><b>Amount</b></th>
            </tr>
            <?php foreach($debits AS $debit):?>
                <tr>
                    <td colspan="2"><?php echo $debit['narration']?></td>
                    <td><?php echo $debit['accountname']?></td>
                    <td ><?php echo number_format((float)$debit['amount'], 2);?></td>
                </tr>
            <?php endforeach;?>
            <tr>
                <th colspan="2"><b>Credit Account</b></th>
                <td><?php echo $dataProvider['accountname'];?></td>
                <td></td>
            </tr>
        <?php else:?>
            <tr>
                <th colspan="2"><b>Narration</b></th>
                <th><b>Credit Account</b></th>
                <th ><b>Amount</b></th>
            </tr>
            <?php foreach($credits AS $credit):?>
                <tr>
                    <td colspan="2"><?php echo $credit['narration']?></td>
                    <td><?php echo $credit['accountname']?></td>
                    <td ><?php echo number_format((float)$credit['amount'], 2);?></td>
                </tr>
            <?php endforeach;?>
            <tr>
                <th colspan="2"><b>Debit Account</b></th>
                <td><?php echo $dataProvider['accountname'];?></td>
                <td></td>
            </tr>
        <?php endif;?>
        <tr >
            <th colspan="2"><b>Total Amount</b></th>
            <th></th>
            <th><?php echo number_format((float)$dataProvider['total'], 2);?></th>
        </tr>
        <tr>
            <td style="width: 200px" colspan="2"><b>Status</b></td>
            <td></td>
            <td style="width: 170px"><select class="form-control" id="journalstatus" name="journalstatus" >
                    <option value="0" selected="selected">Pending</option>
                    <option value="1">Approve</option>
                    <option value="2">Deny</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td>
                <input type="submit" class="btn btn-primary pull-right" style="width: 40%" value="Cancel" onclick="goBack();">
                <input type="submit" class="btn btn-primary pull-right" style="width: 40%;margin-right: 10px;" name="Journal_approve" value="Submit">
            </td>
        </tr>
        </tbody>
    </table>
</form>