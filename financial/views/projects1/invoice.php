<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<h1>Invoice</h1>
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
            <th class="small75">GST</th>
            <th class="small75">IGST</th>
        </tr>
        </thead>
        <tbody>
            <?php echo $datarows;?>
        </tbody>
    </table>
    <table>
        <tbody>
        <tr>
            <th>Specification</th>
            <td colspan="3">
                <?php echo $invoice['Specification'];?>
            </td>
            <th>Invoice No</th>
            <td>
                <?php echo $invoice['invoice_no'];?>
            </td>
        </tr>
        <tr>
            <th>Contact Person</th>
            <td>
                <?php echo $invoice['Contact'];?>
            </td>
            <th>Date of Delivery</th>
            <td>
                <?php echo date('d-m-Y',strtotime($invoice['DeliveryDate']));?>
            </td>
            <th>Place of Delivery</th>
            <td>
                <?php echo $invoice['Place'];?>
            </td>
        </tr>
        <tr>
            <th>Advance</th>
            <td>
                <?php echo $invoice['Advance'];?>
            </td>
            <th>Mode of Payment</th>
            <td colspan="3">
                <?php echo $invoice['Payment'];?>
            </td>
        </tr>
        </tbody>
    </table>
</form>