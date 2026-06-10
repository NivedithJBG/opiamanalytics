    <table class="table table-bordered" align="center">
        <tbody>
            <tr>
                <th><span class="headings">Date</span></th>
                <td colspan="2">
                    <input type="text" class="form-control" name="Bill_Date" id="datepicker" value="<?php echo date("d-m-Y"); ?>">
                </td>
                <!--<th><span class="headings">Project</span></th>
                <td colspan="2"><select class="form-control" name="projectid" id="projectid">
                    <option value="0">Select Project</option>
                    <?php /*$user = User::model()->active()->findbyPk(Yii::app()->user->id);
                    if (Yii::app()->user->isAdmin() || $user['superuser'] == 2 || $user['superuser'] == 4): */?>
                        <?php /*foreach ($adminprojects AS $data): */?>
                            <option value="<?php /*echo $data['Project_Id']; */?>"><?php /*echo $data['Name']; */?></option>
                        <?php /*endforeach; */?>
                    <?php /*else: */?>
                        <?php /*foreach ($userprojects AS $data): */?>
                            <option value="<?php /*echo $data['projectid']; */?>"><?php /*echo $data['Name']; */?></option>
                        <?php /*endforeach; */?>
                    <?php /*endif; */?>
                </select>
                <span class='error'></span></td>-->
                <th colspan="2"><span class="headings">Place</span></th>
                <td colspan="4">
                    <select class="form-control" name="place" id="placeworkbill">
                        <option value="0">Select Place</option>
                            <?php $projects = Projects::model()->findAll(array('condition'=>'Project_Delete_Status=0'));
                            foreach ($projects AS $project):?>
                        <option value="<?php echo $project['Project_Id'] ?>"><?php echo $project['Name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class='error'></span>
                </td>
            </tr>
            <tr>
                <th><span class="headings">Party</span></th>
                <td colspan="2"><!--<input id="ms" class="form-control" name="partyaccounts[]">-->
                    <select class="form-control" name="party" id="partyforwork">
                    <option value="0">Select Vendor</option>
                    </select>
                    <span class='error'></span>
                </td>
                <th><span class="headings">WO No</span></th>
                <td colspan="2">
                    <select class="form-control" name="wono" id="WO_No">
                        <option value="0">Select Work Order</option>
                    </select>
                    <span class='error'></span>
                </td>
                <th><span class="headings">Bill No</span></th>
                <td colspan="2">
                    <input type="text" class="form-control" name="billno" id="billno" placeholder="Bill No">
                    <span class='error'></span>
                </td>
                <!--<th><span class="headings">Bill Period From</span></th>
                <td colspan="2"><input type="text" class="form-control" name="Bill_period_start" id="begindate"
                               value="<?php /*echo date("d-m-Y"); */?>"></span></td>
                <th><span class="headings">Bill Period To</span></th>
                <td colspan="2"><input type="text" class="form-control" name="Bill_period_end" id="enddate"
                               value="<?php /*echo date("d-m-Y"); */?>"></td>-->
            </tr>
        </tbody
    </table>
    <table class="table table-bordered">
        <tbody class="input_fields_wrap">
            <tr>
                <th>SI No</th>
                <th><span class="headings">Item</span></th>
                <th><span class="headings">Unit</span></th>
                <th><span class="headings">Rate</span></th>
                <th><span class="headings">Quantity upto Last bill </span></th>
                <th><span class="headings">Amount upto Last bill </span></th>
                <th><span class="headings">Current Quantity</span></th>
                <th><span class="headings">Current Amount</span></th>
                <th><span class="headings">Total Quantity</span></th>
                <th><span class="headings">Total Amount</span></th>
                <th><span class="headings">Net Amount</span></th>
                <th colspan="2">
                    <input type="button" class="add_field_button" name="addmore" id="addmore" title="Add more" value="Add more">
                </th>
            </tr>
            <tr id="addbillsrow0" style="background-color: #ffffff;">
                <td>1</td>
                <td>
                    <select id="PurchasePurpose0" class="form-control workpurpose" data-id="0" name="Bill_Purpose[]">
                        <option value="none">Select Item</option>
                    </select>
                    <span class='error'></span>
                </td>
                <td>
                    <input type="text" class="form-control billunit" placeholder="Unit" name="Bill_Unit[]" id="Unit0" data-id="0">
                    <span class='error'></span>
                </td>
                <td>
                    <input type="text" class="form-control billrate" placeholder="Rate" name="Bill_Rate[]" id="Rate0" data-id="0">
                    <span class='error'></span>
                </td>
                <td>
                    <span class="" id="prevquantity0"></span>
                    <input type="hidden" placeholder="Amount" name="" id="prev0" value="">
                    <span class='error'></span>
                </td>
                <td>
                    <span class="" id="prevquantityamount0"></span>
                    <input type="hidden" placeholder="Amount" name="" id="prevamount0" value="">
                    <span class='error'></span>
                </td>
                <td>
                    <input type="text" class="form-control billqtywork" placeholder="Quantity" name="Bill_Quantity[]" id="Quantity0" data-id="0">
                    <span class='error'></span>
                </td>
                <td>
                    <span class="bill-amount" id="billamount0"></span>
                    <input type="hidden" placeholder="Amount" name="Bill_Amount[]" id="Amount0" value="">
                    <span class='error'></span>
                </td>
                <td>
                    <span class="" id="totalquantity0"></span>
                    <input type="hidden" placeholder="Amount" name="" id="Total0" value="">
                    <span class='error'></span>
                </td>
                <td>
                    <span class="" id="totalquantityamount0"></span>
                    <input type="hidden" placeholder="Amount" name="" id="totalAmount0" value="">
                    <span class='error'></span>
                </td>
                <td>
                    <span class="net-amount" id="netamount0"></span>
                    <input type="hidden" name="Net_Amount[]" id="Net_Amount0">
                    <span class='error'></span>
                </td>
                <td></td>
            </tr>
            <tr>
                <th colspan="3">Gross Amount</th>
                <th colspan="7" style="text-align: right"><span class='error'></span></th>
                <th>
                    <span id="billtotal"></span>
                    <input type="hidden" name="biltot" id="biltot" value="">
                </th>
                <th></th>
            </tr>
            <tr>
                <th colspan="3">Service Tax Payable</th>
                <th colspan="7">
                    <span id="taxpercent"></span>
                    <input type="hidden" class="form-control" placeholder="Service Tax Percentage" name="taxPercentage" id="tax" data-id="0" style="width: 50%">
                    <span class='error'></span>
                </th>
                <th><span id="taxamount"></span></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="3">TDS Payable</th>
                <th colspan="7">
                    <span id="tdspercent"></span>
                    <input type="hidden" class="form-control" placeholder="TDS Percentage" name="tdsPercentage" id="tds" data-id="0" style="width: 50%">
                    <span class='error'></span></th>
                <th><span id="tdsamount"></span></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="3">Vat Payable</th>
                <th colspan="7"><input type="hidden" class="form-control" placeholder="Vat(%)" name="vat" id="vatid" data-id="0" style="width: 50%">
                    <span id="vatpercent"></span>
                    <span class='error'></span>
                </th>
                <th><span id="deductions"></span></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="3">Amount Including Taxes</th>
                <th colspan="7">
                    <span id="amountinclusive"></span>
                    <span class='error'></span>
                </th>
                <th><span id="totalincluding"></span></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="3">Less Retention</th>
                <th colspan="7">
                    <span id="retpercent"></span>
                    <input type="hidden" class="form-control" placeholder="Retention Percentage" name="retentionPercentage" id="retention" data-id="0" style="width: 50%">
                    <span class='error'></span>
                </th>
                <th><span id="retentionamount"></span></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="3">Amount Payable</th>
                <th colspan="7"></th>
                <th><span id="amountpayabe"></span></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="7"></th>
                <th colspan="2">
                    <button type="submit" class="btn btn-primary" id="savebill" name="Bill_save">Save</button>
                </th>
                <!--<th ><button type="submit" class="btn btn-primary" id="saveandnewbill" name="Bill_savenew">Save and New</button></th>-->
                <th colspan="3">
                    <button type="button" class="btn btn-primary" id="cancelbill" name="Bill_cancel">Cancel</button>
                </th>
            </tr>
        </tbody>
    </table>

<script type="text/javascript">
    $(document).ready(function () {
        var max_fields = 10; //maximum input boxes allowed
        var wrapper = $(".input_fields_wrap"); //Fields wrapper
        var add_button = $(".add_field_button"); //Add button ID

        /*$('#datepicker0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});*/


        var x = 1;
        //initlal text box count
        $(add_button).click(function (e) { //on add input button click
            /*var fulldate=new Date();
             var date=fulldate.getDate();
             var month=fulldate.getMonth() +1;
             var year=fulldate.getFullYear()

             //$("#datepicker").val(currentdate);
             alert(currentdate)*/
            var y = 1;
            var z = x + y;
            var w = x - y;
            e.preventDefault();
            if (x < max_fields) { //max input box allowed
                //text box increment
                $('#addbillsrow' + w).after('<tr id="addbillsrow' + x + '" style="background-color: #ffffff;">' +
                    '<td>' + z + '</td>' +
                    '<td ><input type="text" class="form-control billpurpose" placeholder="Item" id="Purpose' + x + '" data-id="' + x + '" name="Bill_Purpose[]"><span class="error"></span></td>' +
                    '<td ><input type="text" class="form-control" placeholder="Unit" name="Bill_Unit[]" id="Unit"><span class="error"></span></td>' +
                    '<td ><input type="text" class="form-control billrate" placeholder="Rate" name="Bill_Rate[]" id="Rate' + x + '" data-id="' + x + '"><span class="error"></span></td>' +
                    '<td ><span class="" id="prevquantity' + x + '"></span><input type="hidden" placeholder="Amount" name="" id="prev' + x + '" value=""><span class="error"></span></td>' +
                    '<td ><span class="" id="prevquantityamount' + x + '"></span><input type="hidden" placeholder="Amount" name="" id="prevamount' + x + '" value=""><span class="error"></span></td>' +
                    '<td ><input type="text" class="form-control billqtywork" placeholder="Quantity" name="Bill_Quantity[]" id="Quantity' + x + '" data-id="' + x + '"><span class="error"></span></td>' +
                    '<td ><span class="bill-amount" id="billamount' + x + '"></span><input type="hidden" class="form-control" placeholder="Amount" name="Bill_Amount[]" id="Amount' + x + '"><span class="error"></span></td>' +
                    '<td ><span class="" id="totalquantity' + x + '"></span><input type="hidden" placeholder="Amount" name="" id="Total' + x + '" value=""><span class="error"></span></td>' +
                    '<td ><span class="" id="totalquantityamount' + x + '"></span><input type="hidden" placeholder="Amount" name="" id="totalAmount' + x + '" value=""><span class="error"></span></td>' +
                    '<td ><span class="net-amount" id="netamount' + x + '"></span><input type="hidden" name="Net_Amount[]" id="Net_Amount' + x + '"><span class="error"></span></td>' +
                    '<td><a href="#" class="remove_field">Remove</a></td></tr>');
                //$(wrapper).append('<div><input type="text" name="mytext[]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
                /*$('#datepicker'+x+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});*/
                x++;
            }
        });

        $(wrapper).on("click", ".remove_field", function (e) {
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        })
    });
    function calculatetax(){
        var totalrate = 0;
        $('.net-amount').each(function () {
            totalrate = totalrate + ($(this).text() * 1)
        });

        var taxrate = $('#tax').val() * 1;

        var tdsrate = $('#tds').val() * 1;

        var retentionrate = $('#retention').val() * 1;

        var deductions = $('#vatid').val() * 1;
        var taxamount=totalrate * taxrate / 100;
        var tdsamount=totalrate * tdsrate / 100;
        var vatamount=totalrate * tdsrate / 100;
        var amountinclusive=totalrate + taxamount +tdsamount + vatamount;
        var retentionamt=totalrate * retentionrate / 100;
        var amountpayable = amountinclusive-retentionamt;
        $('#totalincluding').text(amountinclusive.toFixed(2));
        $('#amountpayabe').text(amountpayable.toFixed(2));
        $('#taxamount').text(taxamount.toFixed(2));
    }
    function calculatetds(){
        var totalrate = 0;
        $('.net-amount').each(function () {
            totalrate = totalrate + ($(this).text() * 1)
        });

        var tdsrate = $('#tds').val() * 1;

        var taxrate = $('#tax').val() * 1;
        var retentionrate = $('#retention').val() * 1;
        var deductions = $('#vatid').val() * 1;
        var taxamount=totalrate * taxrate / 100;
        var tdsamount=totalrate * tdsrate / 100;
        var vatamount=totalrate * tdsrate / 100;
        var amountinclusive=totalrate + taxamount +tdsamount + vatamount;
        var retentionamt=totalrate * retentionrate / 100;

        var amountpayable = amountinclusive-retentionamt;
        $('#totalincluding').text(amountinclusive.toFixed(2));
        $('#amountpayabe').text(amountpayable.toFixed(2));
        $('#tdsamount').text(tdsamount.toFixed(2));
    }
    function calculateret(){
        var totalrate = 0;
        $('.net-amount').each(function () {
            totalrate = totalrate + ($(this).text() * 1)
        });

        var retentionrate = $('#retention').val() * 1;

        var taxrate = $('#tax').val() * 1;
        var tdsrate = $('#tds').val() * 1;
        var deductions = $('#vatid').val() * 1;
        var taxamount=totalrate * taxrate / 100;
        var tdsamount=totalrate * tdsrate / 100;
        var vatamount=totalrate * tdsrate / 100;
        var amountinclusive=totalrate + taxamount +tdsamount + vatamount;
        var retentionamt=totalrate * retentionrate / 100;
        var amountpayable = amountinclusive-retentionamt;
        $('#totalincluding').text(amountinclusive.toFixed(2));
        $('#amountpayabe').text(amountpayable.toFixed(2));
        $('#retentionamount').text(retentionamt.toFixed(2));

    }
    function calculatevat(){
        var totalrate = 0;
        $('.net-amount').each(function () {
            totalrate = totalrate + ($(this).text() * 1)
        });

        var deductions = $('#vatid').val() * 1;
        var tdsrate = $('#tds').val() * 1;
        var retentionrate = $('#retention').val() * 1;
        var taxrate = $('#tax').val() * 1;
        var taxamount=totalrate * taxrate / 100;
        var tdsamount=totalrate * tdsrate / 100;
        var vatamount=totalrate * tdsrate / 100;
        var amountinclusive=totalrate + taxamount +tdsamount + vatamount;
        var retentionamt=totalrate * retentionrate / 100;
        var amountpayable = amountinclusive-retentionamt;

        $('#totalincluding').text(amountinclusive.toFixed(2));
        $('#amountpayabe').text(amountpayable.toFixed(2));
        $('#deductions').text(vatamount.toFixed(2));
    }
    $(document).on("blur", "#tax", function () {
        calculatetax();
    });
    $(document).on("blur", "#tds", function () {
    calculatetds();
    });
    $(document).on("blur", "#retention", function () {
    calculateret();
    });


    $(document).on("blur", "#vatid", function () {
    calculatevat();
    });
    $(document).on("blur", ".billqtywork", function () {
        var itemid = $(this).attr('data-id');
        var quantity = $(this).val() * 1;

        var rate = $('#Rate' + itemid).val() * 1;
        var prevquantity = $('#prev' + itemid).val() * 1;
        var prevquantityamount = $('#prevamount' + itemid).val() * 1;
        $('#totalquantity' + itemid).text((prevquantity + quantity).toFixed(2));
        var taxget = $('#Tax_Amount' + itemid).val() * 1;
        var tax=0;
        if(isNaN(taxget)){
            tax=0;
        }
        else{
            tax=taxget;
        }
        var amount = (rate * quantity)+tax;
        $('#billamount' + itemid).text(amount.toFixed(2));
        $('#Amount' + itemid).val(amount.toFixed(2));

        $('#totalquantityamount' + itemid).text((prevquantityamount + amount).toFixed(2));
        $("#totalAmount" + itemid).val(prevquantityamount + amount);
        $('#netamount' + itemid).text(amount.toFixed(2));
        $('#Net_Amount' + itemid).val(amount.toFixed(2));
        var totalrate = 0;
        $('.net-amount').each(function () {
            totalrate = totalrate + ($(this).text() * 1)
        });
        $('#billtotal').text(totalrate.toFixed(2));
        $('#biltot').val(totalrate.toFixed(2));
        calculateret();
        calculatetax();
        calculatetds();
        calculatevat();
        /*$('#tax').trigger('blur');
        $('#tds').trigger('blur');
        $('#retention').trigger('blur');
        $('#vatid').trigger('blur');*/
    });
</script>
