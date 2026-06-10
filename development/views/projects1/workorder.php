<script type="text/javascript">
    $(function(){
        $('#cancelworkorder').click(function(){
            window.location = '<?php echo Yii::app()->createUrl('projects/report');?>'
        });
    });

</script>
<script type="text/javascript">
    $(document).on('focus','#datepicker',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    })
</script>
<script>
    function myFunction() {
        window.print();
    }
</script>
<style type="text/css">
    @media print {
        .header,.submitrows,.homeurl {
            display: none;
        }
    }
</style>
<?php if($order['order_type']==2):$quantity="Quantity";?>
<h1>Work bill</h1>
<?php else:$quantity="Duration";?>
    <h1>Lease bill</h1>
<?php endif;?>
<form action="<?php echo Yii::app()->request->baseUrl; ?>/projects/Workorder" method="POST" id="workorderform">
    <table class="table table-bordered ">
        <tbody>
            <tr>
                <th><span class="headings">Date</span></th>
                <td>
                    <input type="text" class="form-control" name="Bill_Date" id="datepicker" value="<?php echo date("d-m-Y"); ?>">
                </td>
                <th><span class="headings">Project</span></th>
                <td>
                    <?php echo $projects = Projects::model()->findByPk($order['project_id'])->Name;?>
                    <input type="hidden" name="place" id="placeworkbill" value="<?php echo $order['project_id'];?>">
                    <span class='error'></span>
                </td>
                <th colspan="2"><span class="headings">Vendor</span></th>
                <td colspan="2">
                    <?php echo $vendors=Vendors::model()->findByPk($order['vendor_id'])->Name;?>
                    <input type="hidden" name="party" id="partyforwork" value="<?php echo $order['vendor_id'];?>">
                    <span class='error'></span>
                </td>

                <th><span class="headings">Bill No</span></th>
                <td>
                    <input type="text" class="form-control" name="billno" id="billno" placeholder="Bill No">
                    <span class='error'></span>
                </td>
            </tr>
            <tr>

                <!--<th><span class="headings">WO No</span></th>
                <td colspan="2">
                    <select class="form-control" name="wono" id="WO_No">
                        <option value="0">Select Work Order</option>
                    </select>
                    <span class='error'></span>
                </td>-->

            </tr>
        </tbody>
    </table>
        <table class="table table-bordered ">
            <tbody>
            <tr>
                <th></th>
                <th style="width: 35%">Item</th>
                <th>Unit</th>
                <th>Rate</th>
                <th><?php echo $quantity;?> upto Last bill</th>
                <th>Current <?php echo $quantity;?></th>
                <th>Amount upto Last bill</th>
                <th>Current Amount</th>
                <th>Total Quantity</th>
                <th>Total Amount</th>
            </tr>
            <?php echo $datarows;?>
            <tr style="background: #ffffff;">
                <td colspan="4"><b>Gross Amount</b></td>
                <td colspan="5" style="text-align: right"><span class='error'></span></td>
                <td style="text-align: right">
                    <span id="billtotal"><b><?php echo number_format((float)$total, 2);?></b></span>
                    <input type="hidden" name="biltot" id="grossamount" value="<?php echo $total;?>">
                    <input type="hidden" name="orderid" value="<?php echo $id;?>">
                </td>
            </tr>
            <?php if($order['sgst']!=0):
                $gstamount=($total * $order['sgst']) / 100;?>
            <tr style="background: #ffffff;">
                <td colspan="4"><b>SGST (<?php echo $order['sgst'];?> %)</b></td>
                <td colspan="5">
                    <input type="hidden" class="form-control" name="sgst" id="sgst" value="<?php echo $order['sgst'];?>">
                    <span class='error'></span>
                </td>
                <td style="text-align: right"><span id="sgstsmount"><b><?php echo number_format((float)$gstamount, 2);?></b></span></td>
            </tr>
            <tr style="background: #ffffff;">
                <td colspan="4"><b>CGST (<?php echo $order['cgst'];?> %)</b></td>
                <td colspan="5">
                    <input type="hidden" name="cgst" id="cgst" value="<?php echo $order['cgst'];?>">
                    <span class='error'></span></td>
                <td style="text-align: right">
                    <span id="cgstamount"><b><?php echo number_format((float)$gstamount, 2);?></b></span>
                    <?php $amountinc=$total + $gstamount + $gstamount;?>
                </td>
            </tr>
            <?php else:
                $igstamount=($total * $order['igst']) / 100;?>
            <tr style="background: #ffffff;">
                <td colspan="4"><b>IGST (<?php echo $order['igst'];?> %)</b></td>
                <td colspan="5">
                    <input type="hidden" name="igst" id="igst" value="<?php echo $order['igst'];?>">
                    <span class='error'></span>
                </td>
                <td style="text-align: right">
                    <span id="igstamount"><b><?php echo number_format((float)$igstamount, 2);?></b></span>
                    <?php $amountinc=$total + $igstamount;?>
                </td>
            </tr>
            <?php endif;?>
            <tr style="background: #ffffff;">
                <td colspan="4"><b>Amount Including Taxes</b></td>
                <td colspan="5">
                </td>
                <td style="text-align: right"><span id="amountinclusive"><b><?php echo number_format((float)$amountinc, 2)?></span></b></td>
            </tr>
            <tr style="background: #ffffff;">
                <td colspan="4"><b>Retention (<?php echo $order['Retention'];?> %)</b></td>
                <td colspan="5">
                    <input type="hidden" name="retention" id="retention" value="<?php echo $order['Retention'];?>">
                </td>
                <td style="text-align: right">
                    <span id="billretention"><b><?php echo number_format((float)$retamount=($total * $order['Retention']) / 100, 2);?></b></span>
                    <?php $nettot=($total + ($order['advance'] - $order['Retention']));?>
                    <input type="hidden" name="nettotal" id="nettotal" value="<?php echo $nettot;?>">
                </td>
            </tr>
            <tr style="background: #ffffff;">
                <td colspan="4"><b>Net Amount</b></td>
                <td colspan="5"></td>
                <td style="text-align: right">
                    <?php
                    $retamount=($total * $order['Retention']) / 100;
                    $netamount=$amountinc - $retamount;?>
                    <span id="billnetamount"><b><?php echo number_format((float)$netamount, 2);?></b></span>
                </td>
            </tr>
            <tr style="background: #ffffff;">
                <td colspan="4"><b>Amount paid till today</b></td>
                <td colspan="5">
                    <input type="hidden" name="advance" id="advance" value="<?php echo $ledgerbal;?>">
                </td>
                <td style="text-align: right">
                    <span id="billadvance"><b><?php echo number_format((float)$ledgerbal, 2);?></b></span>
                </td>
            </tr>
            <tr style="background: #ffffff;">
                <td colspan="4"><b>Amount Payable</b></td>
                <td colspan="5"></td>
                <td style="text-align: right"><span id="amountpayabe"><b><?php echo number_format((float)($netamount - $ledgerbal), 2)?></b></span></td>
            </tr>
            <tr style="background: #ffffff;" class="submitrows">
                <td colspan="6"><input type="hidden" name="ordertype" value="<?php echo $order['order_type'];?>"></td>
                <td colspan="2">
                    <button type="submit" class="btn btn-primary" id="savebill" name="Workorder">Raise Bill</button>
                </td>
                <td>
                    <button type="button" class="btn btn-primary" id="prillbill" name="Bill_print" onclick="myFunction()">Print</button>
                </td>
                <td>
                    <button type="button" class="btn btn-primary" id="cancelworkorder" name="Bill_cancel">Cancel</button>
                </td>
            </tr>
        </tbody>
    </table>
</form>
<script type="text/javascript">
    $(document).on('change','.resourceqnty',function(){
        var resid=$(this).attr('data-id');
        var cqty=$(this).val();
        var uqty=$('#qtyuptolast'+resid).val();
        var rate=$('#resrate'+resid).val();
        var camount=cqty * rate;
        var uamount=$('#amountuptolast'+resid).val();
        var totalqty=parseFloat(cqty) + parseFloat(uqty);
        var totalamount= parseFloat(camount) + parseFloat(uamount);
        $('#currentamount'+resid).html(camount.toFixed(2));
        $('#camountval'+resid).val(camount);
        $('#totalamount'+resid).html(totalamount.toFixed(2));
        $('#totalqty'+resid).html(totalqty);
        var grossamount=0;
        $('.currentamount').each(function(){
            grossamount=grossamount+($(this).val()*1)
        });
        $('#billtotal').html(grossamount.toFixed(2));
        $('#grossamount').val(grossamount);
        var grossamount=$('#grossamount').val();
        var sgst=$('#sgst').val();
        var cgst=$('#cgst').val();
        var igst=$('#igst').val();
        var gstamount=(grossamount * sgst) / 100;
        var igstamount=(grossamount * igst) / 100;
        $('#sgstsmount').html(gstamount.toFixed(2));
        $('#cgstamount').html(gstamount.toFixed(2));
        $('#igstamount').html(igstamount.toFixed(2));
        if (igst!=0){
            var amountinc=parseFloat(grossamount) + parseFloat(gstamount) + parseFloat(gstamount);
        }
        else {
            var amountinc=parseFloat(grossamount) + parseFloat(igstamount);
        }

        $('#amountinclusive').html(amountinc.toFixed(2));
        var retention=$('#retention').val();
        var retenamount=(grossamount * retention) / 100;
        $('#billretention').html(retenamount.toFixed(2));
        var netamount=amountinc - retenamount;
        $('#billnetamount').html(netamount.toFixed(2));
        var advance=$('#advance').val();
        var amountpay=netamount - advance;
        $('#amountpayabe').html(amountpay.toFixed(2));
    });
    $(document).on('click','#savebill',function(){
        var error=0;
        $('.resourceqnty').each(function(){
            var resid=$(this).attr('data-id');
            var cqty=$(this).val();
            var actreportqty=$('#actreportqty'+resid).val();
            if(parseFloat(cqty) > parseFloat(actreportqty))
            {
                alert('Current Quantity cannot be greater than reported quantity')
                error=1;
            }
        });
        if($('#billno').val()==''){
            $('#billno').next("span").html('Please enter Bill Number').show('slow');
            error=1;
        }
        if (error==0)
        {
            return true;
        }
        else {
            return false;
        }
    });
</script>