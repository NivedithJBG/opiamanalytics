<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<script type="text/javascript">
    $(document).on('focus','#podate',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });
    $(document).on('focus','#invoicedate',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    })
</script>
<h1>Approve Invoice</h1>
<form method="POST" action="" id="invoiceform">
    <table class="table table-bordered " >
        <thead>
            <tr>
                <th></th>
                <th>Item</th>
                <th>Unit</th>
                <th class="small75">Rate</th>
                <th class="small75">Quantity</th>
                <th class="small75">Amount</th>
                <?php if($gstcount > 0):?>
                <th class="small75">SGST/CGST (%)</th>
                <th class="small75">GST Amount</th>
                <?php else:
                    if($igstcount > 0):?>
                        <th class="small75">IGST</th>
                        <th class="small75">IGST Amount</th>
                    <?php else:?>
                        <th class="small75">SGST/CGST (%)</th>
                        <th class="small75">GST Amount</th>
                    <?php endif;?>
                <?php endif;?>
                <th class="small75">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $datarows;?>
        </tbody>
    </table>
    <table>
        <tbody>
        <!--<tr>
            <th>Specification</th>
            <td colspan="3">
                <?php /*echo $invoice['Specification'];*/?>
            </td>
            <th>Invoice No</th>
            <td>
                <?php /*echo $invoice['invoice_no'];*/?>
            </td>
        </tr>-->
        <!--<tr>
            <th>Date of Delivery</th>
            <td>
                <?php /*echo date('d-m-Y',strtotime($invoice['DeliveryDate']));*/?>
            </td>
            <th>Contact Person</th>
            <td>
                <?php /*echo $invoice['Contact'];*/?>
            </td>
        </tr>-->
        <tr>
            <th>PO Number</th>
            <td colspan="3">
                <input type="text" name="ponumber" class="form-control" placeholder="PO Number">
            </td>
        </tr>
        <tr>
            <th>PO Date</th>
            <td colspan="3">
                <input type="text" class="form-control" name="podate" id="podate" value="<?php echo date('d-m-Y',strtotime($ordermodel['date']))?>">
            </td>
        </tr>
        <tr>
            <th>Invoice Date</th>
            <td colspan="3">
                <input type="text" class="form-control" name="invoicedate" id="invoicedate" value="<?php echo date('d-m-Y')?>">
            </td>
        </tr>
        <tr>
            <th>Place of Delivery</th>
            <td colspan="3">
                <input type="text" class="form-control" name="deliveryplace" value="<?php echo $invoice['Place'];?>">
            </td>
        </tr>
        <tr>
            <th>Mode of Payment</th>
            <td colspan="3">
                <input type="text" class="form-control" name="paymentmode" value="<?php echo $invoice['Payment'];?>">
            </td>
        </tr>
        <tr>
            <th>Advance</th>
            <td colspan="3">
                <input type="text" class="form-control" name="advance" value="<?php echo $invoice['Advance'];?>">
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td><input type="submit" name="approve" value="Approve" class="btn btn-primary"></td>
            <td><input type="button" value="Cancel" class="btn btn-primary" id="cancelinvoice" onclick="goBack()"></td>
        </tr>
        </tbody>
    </table>
</form>
<script type="application/javascript">
    $(document).on("change",".invoiceqty",function(){
        var resid=$(this).attr('data-id');
        var qty=parseFloat($(this).val());
        var rate=$('#rate'+resid).val();
        var amount=rate * qty;
        $('#amount'+resid).html(amount.toFixed(2));
        var gst=$('#gstper'+resid).val();
        var gstamount=(amount * gst) / 100;
        $('#gstamount'+resid).html(gstamount.toFixed(2));
        var totalamount=amount + gstamount;
        $('#totalamount'+resid).html(totalamount.toFixed(2));
        $('#totalamountval'+resid).val(totalamount.toFixed(2));

        var netamount=0;
        $('.totalamount').each(function(){
            netamount=netamount + parseFloat($(this).val());
        });
        $('#netamount').html(netamount.toFixed(2));
    });
</script>