<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<h1>Work Order</h1>
<form action="" method="POST" id="workorderform">
    <table class="table table-bordered ">
        <tbody>
        <tr>
            <th class="small75"><span class="headings">Date</span></th>
            <td>
                <?php echo date('d-m-Y',strtotime($invoice['date']));?>
            </td>
            <th><span class="headings">Place</span></th>
            <td colspan="3">
                <?php $project=Projects::model()->findByPk($order['project_id']);
                echo $project['Name'];?>
            </td>
        </tr>
        <tr>
            <th><span class="headings">Bill No</span></th>
            <td>
                <?php echo $invoice['bill_no'];?>
            </td>
            <th><span class="headings">Party</span></th>
            <td colspan="3">
                <?php $party=Vendors::model()->findByPk($invoice['party']);
                echo $party['Name'];?>
            </td>
            <!--<th><span class="headings">WO No</span></th>
            <td colspan="2">
                <select class="form-control" name="wono" id="WO_No">
                    <option value="0">Select Work Order</option>
                </select>
                <span class='error'></span>
            </td>-->

        </tr>
        <tr>
            <th></th>
            <th>Item</th>
            <th>Unit</th>
            <th>Rate</th>
            <th>Quantity</th>
            <th>Amount</th>
        </tr>
        <?php echo $datarows;?>
        <tr>
            <th colspan="5">Gross Amount</th>
            <th style="text-align: right">
                <span id="billtotal"><?php echo number_format((float)$total, 2);?></span>
            </th>
        </tr>

        <?php if($invoice['sgst']!=0):
            $gstamount=($total * $invoice['sgst']) / 100;?>
            <tr>
                <th colspan="5">SGST (<?php echo $invoice['sgst'];?> %)</th>
                <th style="text-align: right"><span id="sgstsmount"><?php echo number_format((float)$gstamount, 2);?></span></th>
            </tr>
            <tr>
                <th colspan="5">CGST (<?php echo $invoice['cgst'];?> %)</th>
                <th style="text-align: right"><span id="cgstamount"><?php echo number_format((float)$gstamount, 2);?></span></th>
                <?php $amountinc=$total + $gstamount + $gstamount;?>
            </tr>
        <?php else:
            $igstamount=($total * $invoice['igst']) / 100;?>
            <tr>
                <th colspan="5">IGST (<?php echo $invoice['igst'];?> %)</th>
                <th style="text-align: right"><span id="igstamount"><?php echo number_format((float)$igstamount, 2);?></span></th>
                <?php $amountinc=$total + $igstamount;?>
            </tr>
        <?php endif;?>
        <tr>
            <th colspan="5">Amount Including Taxes</th>
            <th style="text-align: right"><span id="amountinclusive"><?php echo number_format((float)$amountinc, 2)?></span></th>
        </tr>
        <tr>
            <th colspan="5">Retention (%)</th>
            <th style="text-align: right">
                <span id="billretention"><?php echo number_format((float)$order['Retention'], 2);?></span>
                <?php $nettot=($total + ($order['advance'] - $order['Retention']));?>
                <input type="hidden" name="nettotal" id="nettotal" value="<?php echo $nettot;?>">
            </th>
        </tr>
        <tr>
            <th colspan="5">Net Amount</th>
            <th style="text-align: right">
                <?php
                $retamount=($total * $order['Retention']) / 100;
                $netamount=$amountinc - $retamount;?>
                <span id="billnetamount"><?php echo number_format((float)$netamount, 2);?></span>
            </th>
        </tr>
        <tr>
            <th colspan="5">Advance</th>
            <th style="text-align: right">
                <span id="billadvance"><?php echo number_format((float)$order['advance'], 2);?></span>
            </th>
        </tr>
        <tr>
            <th colspan="5">Amount Payable</th>
            <th style="text-align: right"><span id="amountpayabe"><?php echo number_format((float)($netamount - $order['advance']), 2)?></span></th>
        </tr>

        </tbody>
    </table>
</form>