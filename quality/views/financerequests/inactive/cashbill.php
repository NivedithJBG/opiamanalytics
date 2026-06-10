
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
    function goBack()
    {
        window.history.back()
    }
</script>
<h1>Cash Bill</h1>
<form method="POST" action="" id="cashbillform">
    <table class="table table-bordered" align="center" style="width: 50%">
        <tbody>
        <tr>
            <th style="width: 40%"><span class="headings">Date</span></th>
            <td style="width: 60%"><input type="text" class="form-control" name="Cashbill_Date" id="datepicker" value="<?php echo date("d-m-Y");?>"></td>
        </tr>
        <tr>
            <th><span class="headings">Bill no</span></th>
            <td><input type="text" class="form-control" placeholder="Bill no" name="Cashbill_no" id="Cashbill_no"><span class='error'></span></td>
        </tr>
        <tr>
            <th><span class="headings">Vendor Name</span></th>
            <td><input type="text" class="form-control" placeholder="Vendor Name" name="Cashbill_Vendor" id="Cashbill_Vendor"><span class='error'></span></td>
        </tr>
        <tr>
            <th><span class="headings">Item</span></th>
            <td><input type="text" class="form-control" placeholder="Item" name="Cashbill_Item" id="Cashbill_Item"><span class='error'></span></td>
        </tr>
        <tr>
            <th><span class="headings">Unit</span></th>
            <td><input type="text" class="form-control" placeholder="Unit" name="Cashbill_Unit" id="Cashbill_Unit"><span class='error'></span></td>
        </tr>
        <tr>
            <th><span class="headings">Quantity</span></th>
            <td><input type="text" class="form-control" placeholder="Quantity" name="Cashbill_Quantity" id="Cashbill_Quantity"><span class='error'></span></td>
        </tr>
        <tr>
            <th><span class="headings">Purpose</span></th>
            <td><textarea rows="4" class="form-control" cols="50" id="Purpose" name="Cashbill_Purpose" ></textarea><span class='error'></span></td>
        </tr>
        <tr>
            <th><span class="headings">Amount</span></th>
            <td><input type="text" class="form-control" placeholder="Amount" name="Cashbill_Amount" id="Amount"><span class='error'></span></td>
        </tr>

        <tr >
            <th ><button type="submit" class="btn btn-primary" id="savereceipt" name="Cashbill_save">Save</button></th>
            <th ><button type="button" class="btn btn-primary" id="cancelcashbill" name="Cashbill_cancel" onclick="goBack()">Cancel</button></th>
        </tr>
        </tbody>
    </table>
</form>