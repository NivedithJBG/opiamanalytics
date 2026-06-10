<script type="text/javascript">
    $(function(){
        $('#cancelinvoice').click(function(){
            window.location = '<?php echo Yii::app()->createUrl('projects/report');?>'
        });
    });

</script>
<script type="text/javascript">

    $(document).on('focus','#dateofdelivery',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    })
</script>
<?php if($order['order_type']==3):?>
<h1>Receive Direct Work Order</h1>
<?php else:?>
    <h1>Receive Materials</h1>
<?php endif;?>
<form method="POST" action="" id="invoiceform">
    <table class="table table-bordered " >
        <thead>
        <?php if($order['order_type']!=3):?>
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
        <?php else:?>
            <tr>
                <th></th>
                <th>Item</th>
                <th>Unit</th>
                <th>Rate</th>
                <th>No of Workers</th>
                <th>No of Days</th>
                <th>OT Rate</th>
            </tr>
        <?php endif;?>
        </thead>
        <tbody>
            <?php echo $datarows;?>
        </tbody>
    </table>
    <table>
        <tbody>
        <?php if($order['order_type']!=3):?>
        <tr>
            <th>Specification</th>
            <td colspan="3">
                <input type="text" class="form-control" name="Specification" <?php echo $class;?> value="<?php echo $order['specification'];?>">
            </td>
            <th>Invoice No</th>
            <td>
                <input type="text" class="form-control" name="Invoiceno" id="invoicenum" <?php echo $class;?> >
                <span class="error"></span>
            </td>
        </tr>
        <tr>
            <th>Contact Person</th>
            <td>
                <input type="text" class="form-control" <?php echo $class;?> value="<?php echo $order['contact'];?>" name="Contact">
            </td>
            <th>Date of Delivery</th>
            <td>
                <input type="text" class="form-control" <?php echo $class;?> name="date" id="dateofdelivery" value="<?php echo date('d-m-Y',strtotime($order['date']));?>">
            </td>
            <th>Place of Delivery</th>
            <td>
                <input type="text" class="form-control" <?php echo $class;?> name="Place" value="<?php echo $order['place'];?>">
            </td>
        </tr>
        <tr>
            <th>Advance</th>
            <td>
                <input type="text" class="form-control" name="Advance" <?php echo $class;?> value="<?php echo $order['advance'];?>">
            </td>
            <th>Mode of Payment</th>
            <td>
                <input type="text" class="form-control" name="Payment" <?php echo $class;?> value="<?php echo $order['payment'];?>">
            </td>
            <th colspan="2"></th>

        </tr>
        <?php endif;?>
        <?php if($order['order_type']!=3):?>
            <tr>
                <td colspan="3"></td>
                <td><input type="button" value="Cancel" class="btn btn-primary" id="cancelinvoice"></td>
                <td><input type="submit" value="Receive" id="invoicebtn" class="btn btn-primary invoicebtn" name="Save"></td>
                <td><input type="submit" value="Complete" id="completebtn" class="btn btn-primary invoicebtn" name="Complete"></td>
            </tr>
        <?php else:?>
            <tr>
                <td colspan="5"></td>
                <td><input type="button" value="Cancel" class="btn btn-primary" id="cancelinvoice"></td>
                <td ><input type="submit" value="Received" id="invoicebtn" class="btn btn-primary" name="Save"></td>
            </tr>
        <?php endif;?>
        </tbody>
    </table>
</form>
<script type="text/javascript">
    $(document).on('click','.invoicebtn',function(){
        var error=0;
        $('.error').hide();
        $('.resourceqnty').each(function(){
            var qty=$(this).val();
            var resid=$(this).attr('data-id');
            var maxqty=parseFloat($('#resmaxqty'+resid).val());
            var resqty=parseFloat($('#resourceqnty'+resid).val());
            //alert($('#resourceqnty'+resid).val())
            if (resqty > maxqty)
            {
                $('#resourceqnty'+resid).next("span").html('Quantity cannot be greater than order quantity').show('slow');
                error=1;
            }
            if ($('#resourceqnty'+resid).val()==''){
                $('#resourceqnty'+resid).next("span").html('Quantity cannot be blank').show('slow');
                error=1;
            }
        });
        if ($('#invoicenum').val()==''){
            $('#invoicenum').next("span").html('Please enter Invoice Number').show('slow');
            error=1;
        }
        if (error==0){
            return true;
        }
        else {
            return false;
        }
    });
</script>