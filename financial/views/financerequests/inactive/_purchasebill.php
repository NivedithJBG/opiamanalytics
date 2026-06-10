    <table class="table table-bordered" style="overflow: visible" align="center">
        <tbody>
            <tr>
                <th><span class="headings">Date</span></th>
                <td colspan="2">
                    <input type="text" class="form-control" name="Bill_Date" id="datepicker" value="<?php echo date("d-m-Y"); ?>">
                </td>
                <th colspan="2"><span  class="headings">Project</span></th>
                <td colspan="8">
                    <select class="form-control" name="place" id="place">
                        <option value="0">Select Project</option>
                        <?php $projects = Projects::model()->findAll(array('condition'=>'Project_Delete_Status=0'));
                        foreach ($projects AS $project):?>
                            <option value="<?php echo $project['Project_Id'] ?>"><?php echo $project['Name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class='error'></span>
                </td>
            </tr>
            <tr>
                <th colspan="1"><span class="headings">Bill No</span></th>
                <td colspan="2">
                    <input type="text" class="form-control" name="billno" id="billno" placeholder="Bill No">
                    <span class='error'></span>
                </td>
                <th ><span class="headings">Vendor</span></th>
                <!--<td colspan="5"><input id="ms" class="form-control" name="partyaccounts[]"></td>-->
                <td colspan="4">
                    <select class="form-control" name="party" id="party">
                        <option value="0">Select Vendor</option>
                    </select>
                </td>
                <th ><span class="headings">PO No</span></th>
                <td colspan="3">
                    <select class="form-control" name="PO_No" id="PO_No">
                        <option value="0">Select PO NO</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><span class="headings">TIN No</span></th>
                <td colspan="2">
                    <input type="text" class="form-control" name="tinno" id="tinno" placeholder="TIN No">
                    <span class='error'></span>
                </td>
                <th><span class="headings">CST Reg No</span></th>
                <td colspan="2">
                    <input type="text" class="form-control" name="cstregno" id="cstregno" placeholder="CST Reg No">
                    <span class='error'></span>
                </td>
                <th><span class="headings">Invoice No</span></th>
                <td colspan="2">
                    <input type="text" class="form-control" name="invoiceno" id="invoiceno" placeholder="Invoice No">
                    <span class='error'></span>
                </td>
                <th><span class="headings">Due Date</span></th>
                <td colspan="2">
                    <input type="text" class="form-control" name="Bill_Duedate" id="duedate" value="<?php echo date("d-m-Y"); ?>">
                </td>
            </tr>
        </tbody
    </table>
    <table class="table table-bordered" style="overflow: visible">
        <tbody class="input_fields_wrap">
            <tr>
                <th><span class="headings">SI No</span></th>
                <th colspan="3"><span class="headings ">Resource Item</span></th>
                <th colspan="2"><span class="headings">Current Quantity</span></th>
                <th><span class="headings">Rate</span></th>
                <th><span class="headings">Tax</span></th>
                <th colspan="3"><span class="headings">Current Amount</span></th>
                <th colspan="3">
                    <input type="button" class="add_field_button" name="addmore" id="addmore" title="Add more" value="Add more">
                </th>
            </tr>
            <tr id="addbillsrow0" style="background-color: #ffffff;">
                <td>1</td>
                <td colspan="3" id="resitem0">
                    <select id="PurchasePurpose0" class="form-control purchasepurpose" data-id="0" name="Bill_Purpose[]">
                        <option value="none">Select Resource</option>
                        <?php
                        /*$typelist = Resources::model()->findAll();
                        foreach ($typelist AS $list):
                            echo "<option value='" . $list->Resource_Id . "'>" . $list->Name . "</option>";
                        endforeach;
                        */?>
                    </select>
                    <span class='error'></span>
                </td>
                <!-- <td ><input type="text" class="form-control" placeholder="Unit" name="Bill_Unit[]" id="Unit0"><span class='error'></span></td>-->
                <!--<td><span class="" id="prevquantity0"></span><input type="hidden" placeholder="Amount" name="" id="prev0"
                                                                    value=""><span class='error'></span></td>
                <td><span class="" id="prevquantityamount0"></span><input type="hidden" placeholder="Amount" name=""
                                                                          id="prevamount0" value=""><span class='error'></span>
                </td>-->
                <td colspan="2">
                    <input type="number" class="form-control billqty" placeholder="Quantity" name="Bill_Quantity[]" id="Quantity0" data-id="0" max="" step="0.1">
                    <span class='error'></span>
                </td>
                <td>
                    <input type="text" class="form-control billrate" placeholder="Rate" name="Bill_Rate[]" id="Rate0" data-id="0">
                    <span class='error'></span>
                </td>
                <td>
                    <span class="tax-amount" id="taxamount0"></span>
                    <input type="text" class="form-control billtax" placeholder="Tax Amount" name="Tax_Amount[]" id="Tax_Amount0" data-id="0">
                    <span class='error'></span>
                </td>
                <td colspan="3">
                    <span class="bill-amount" id="billamount0"></span>
                    <input type="hidden" placeholder="Amount" name="Bill_Amount[]" id="Amount0" value="">
                    <span class='error'></span>
                    <input type="hidden" name="Net_Amount[]" id="Net_Amount0"><span class='error'>
                </td>
                <!--<td><span class="" id="totalquantity0">
                </span><input type="hidden" placeholder="Amount" name="" id="Total0" value=""><span class='error'></span></td>-->
                <td colspan="2">
                    <span class="net-amount" id="netamount0"></span>
                    <input type="hidden" name="Net_Amount[]" id="Net_Amount0">
                    <span class='error'></span>
                </td>
                <td></td>
            </tr>
            <tr>
                <th colspan="4">Total amount</th>
                <th colspan="7"></th>
                <th colspan="2"><span id="billtotal"></span></th>
                <input type="hidden" name="biltot" id="biltot" value="">
                <th></th>
            </tr>
            <tr>
                <th colspan="11"></th>
                <th>
                    <button type="submit" class="btn btn-primary" id="savebill" name="Bill_save">Save</button>
                </th>
                <!--<th ><button type="submit" class="btn btn-primary" id="saveandnewbill" name="Bill_savenew">Save and New</button></th>-->
                <th colspan="2">
                    <button type="button" class="btn btn-primary" id="cancelbill" name="Bill_cancel">Cancel</button>
                </th>
            </tr>
        </tbody>
    </table>

<script type="text/javascript">
    $(document).ready(function () {
        $('.resourseamagic').magicSuggest({
            maxSelection: 1,
            data: '../Resources/autocompleteResources',
            valueField: 'Name',
            displayField: 'Name'
        });
        var max_fields = 10; //maximum input boxes allowed
        var wrapper = $(".input_fields_wrap"); //Fields wrapper
        var add_button = $(".add_field_button"); //Add button ID

        /*$('#datepicker0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});*/


        var x = 1; //initlal text box count
        var y = 1;
        $(add_button).click(function (e) { //on add input button click

            var z = x + y;
            var w = x - y;
            e.preventDefault();
            if (x < max_fields) { //max input box allowed
                //text box increment
                $('#addbillsrow' + w).after('<tr id="addbillsrow' + x + '" style="background-color: #ffffff;">' +
                    '<td>' + z + '</td>' +
                    '<td id="resitem'+x+'" colspan="3"><select id="PurchasePurpose' + x + '" class="form-control purchasepurpose" data-id="'+ x +'" name="Bill_Purpose[]">' +
                    '<option value="none">Select Resource</option><span class="error"></span></td>' +
                    '</select><span class="error"></span></td>' +
                    '<td colspan="2"><input type="number" class="form-control billqty" placeholder="Quantity" name="Bill_Quantity[]" id="Quantity' + x + '" data-id="' + x + '" max="" step="0.1"><span class="error"></span></td>' +
                    '<td ><input type="text" class="form-control billrate" placeholder="Rate" name="Bill_Rate[]" id="Rate' + x + '" data-id="' + x + '"><span class="error"></span></td>' +
                    '<td ><span class="tax-amount" id="taxamount' + x + '"></span><input type="text" class="form-control billtax" placeholder="Tax Amount" name="Tax_Amount[]" id="Tax_Amount' + x + '" data-id="' + x + '"><span class="error"></span></td>' +
                    '<td colspan="3"><span class="bill-amount" id="billamount' + x + '"></span><input type="hidden" class="form-control" placeholder="Amount" name="Bill_Amount[]" id="Amount' + x + '"><span class="error"></span></td>' +
                    '<td colspan="2"><span class="net-amount" id="netamount' + x + '"></span><input type="hidden" name="Net_Amount[]" id="Net_Amount' + x + '"><span class="error"></span></td>'+
                    '<td><a href="javascript:void(0)" class="remove_field">Remove</a></td></tr>');
                //$(wrapper).append('<div><input type="text" name="mytext[]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
                /*$('#datepicker'+x+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});*/
                x++;
            }
            var poid = $('#PO_No').val();
            var projectid = $('#place').val();
            //var requestid = $(this).attr('data-id');
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/GetResourcename',
                dataType: "json",
                data: {poid: poid},
                success: function (data) {
                    if (data.error == 'No') {
                        var option = data.result;
                        $("#PurchasePurpose"+(x-1)).empty().append(option);

                    }
                    else {
                        alert(data.errortext);
                    }

                }
            });
        });


        $(wrapper).on("click",".remove_field", function(e){
            //e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
            var totalrate = 0;
            $('.net-amount').each(function () {
                totalrate = totalrate + ($(this).text() * 1)
            });
            $('#billtotal').text(totalrate.toFixed(2));
            $('#biltot').val(totalrate.toFixed(2));
        });
    });
    // A $( document ).ready() block.
    $(document).ready(function () {
        $(document).on("change", ".restype", function (e) {
            var resid = $(this).val();


            var itemid = $(this).attr('data-id');

            $.post("<?php echo Yii::app()->createUrl('Resources/getresourceitems');?>", {id: resid, itemid: itemid})
                .done(function (data) {

                    $('#resitem'+ itemid).html(data)

                });
        });
    });

</script>
