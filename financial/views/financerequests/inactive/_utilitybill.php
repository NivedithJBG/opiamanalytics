<script type="text/javascript">
    $(document).ready(function() {
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID

        /*$('#datepicker0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});*/

        var x = 1; //initlal text box count
        var y=1;
        $(add_button).click(function(e){ //on add input button click
            /*var fulldate=new Date();
             var date=fulldate.getDate();
             var month=fulldate.getMonth() +1;
             var year=fulldate.getFullYear()

             //$("#datepicker").val(currentdate);
             alert(currentdate)*/
            var z=x+y;
            var w=x-y;

            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                //text box increment
                $('#addbillsrow'+w).after('<tr id="addbillsrow'+x+'" style="background-color: #ffffff;">' +

                    '<td ><input type="text" class="form-control billpurpose" placeholder="Item" id="Purpose'+x+'" data-id="'+x+'" name="Bill_Purpose[]"><span class="error"></span></td>' +
                    '<td ><input type="text" class="form-control billunit" placeholder="Unit" name="Bill_Unit[]" id="Unit'+x+'" data-id="'+x+'"><span class="error"></span></td>' +
                    '<td ><input type="text" class="form-control billrate" placeholder="Rate" name="Bill_Rate[]" id="Rate'+x+'" data-id="'+x+'"><span class="error"></span></td>' +
                    '<td ><input type="text" class="form-control billqty" placeholder="Quantity" name="Bill_Quantity[]" id="Quantity'+x+'" data-id="'+x+'"><span class="error"></span></td>' +
                    '<td ><span class="bill-amount" id="billamount'+x+'"></span><input type="hidden" class="form-control" placeholder="Amount" name="Bill_Amount[]" id="Amount'+x+'"><span class="error"></span></td>' +
                    '<td ><span class="tax-amount" id="taxamount'+x+'"></span><input type="text" class="form-control billtax" placeholder="Tax Amount" name="Tax_Amount[]" id="Tax_Amount'+x+'" data-id="'+x+'"><span class="error"></span></td>' +
                    '<td ><span class="net-amount" id="netamount'+x+'"></span><input type="hidden" name="Net_Amount[]" id="Net_Amount'+x+'"><span class="error"></span></td>' +
                    '<td><a href="#" class="remove_field">Remove</a></td></tr>');
                //$(wrapper).append('<div><input type="text" name="mytext[]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
                /*$('#datepicker'+x+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});*/
                x++;
            }
        });

        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        })
    });
</script>

<form method="POST" action="" id="billsform">
    <table class="table table-bordered" align="center" >
        <tbody>
        <tr>
            <th ><span class="headings">Date</span></th>
            <td colspan="2"><input type="text" class="form-control" name="Bill_Date" id="datepicker" value="<?php echo date("d-m-Y");?>"></td>
            <th ><span class="headings">Project</span></th>
            <td colspan="2"><select class="form-control" name="projectid" id="projectid">
                    <option value="0">Select Project</option>
                    <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);
                    if(Yii::app()->user->isAdmin() || $user['superuser']==2 || $user['superuser']==4): ?>
                        <?php foreach($adminprojects AS $data):?>
                            <option value="<?php echo $data['Project_Id'];?>"><?php echo $data['Name']; ?></option>
                        <?php endforeach;?>
                    <?php else: ?>
                        <?php foreach($userprojects AS $data):?>
                            <option value="<?php echo $data['projectid'];?>"><?php echo $data['Name']; ?></option>
                        <?php endforeach;?>
                    <?php endif;?>
                </select>
                <span class='error'></span></td>
            <th ><span class="headings">Place</span></th>
            <td>
                <select class="form-control" name="place" id="place">
                    <option value="0">Select Place</option>
                    <?php $projects=Projects::model()->findAll(array('condition'=>'Project_Delete_Status=0'));
                    foreach($projects AS $project):?>
                        <option value="<?php echo $project['Project_Id']?>"><?php echo $project['Name']?></option>
                    <?php endforeach;?>
                </select>
                <span class='error'></span>
            </td>
        </tr>
        <tr>
            <th ><span class="headings">Bill no</span></th>
            <td colspan="2"><input type="text" class="form-control" name="billno" id="billno" placeholder="Bill no"><span class='error'></span></td>
            <th ><span class="headings">Party</span></th>
            <?php if($_GET['billtype']==5):?>
            <td colspan="2"><input type="text" class="form-control" name="partyaccount"><span class='error'></span></td>
            <td colspan="3"></td>
            <?php else: ?>
            <td colspan="2"><input id="ms" class="form-control" name="partyaccounts[]"><span class='error'></span></td>
            <th ><span class="headings">Due Date</span></th>
            <td colspan="2"><input type="text" class="form-control" name="Bill_Duedate" id="duedate" value="<?php echo date("d-m-Y");?>"></td>
            <?php endif; ?>

        </tr>
        </tbody>
    </table>
    <table class="table table-bordered">
        <tbody class="input_fields_wrap">
        <tr>
            <th ><span class="headings">Item</span></th>
            <th><span class="headings">Unit</span></th>
            <th><span class="headings">Rate</span></th>
            <th><span class="headings">Quantity</span></th>
            <th><span class="headings">Amount</span></th>
            <th><span class="headings">Tax Amount</span></th>
            <th><span class="headings">Net Amount</span></th>
            <th><input type="button" class="add_field_button" name="addmore" id="addmore" title="Add more" value="Add more"></th>
        </tr>

        <tr id="addbillsrow0" style="background-color: #ffffff;">
            <td ><input type="text" class="form-control billpurpose" placeholder="Item" id="Purpose0" data-id="0" name="Bill_Purpose[]"><span class='error'></span></td>
            <td ><input type="text" class="form-control billunit" placeholder="Unit" name="Bill_Unit[]" id="Unit0" data-id="0"><span class='error'></span></td>
            <td ><input type="text" class="form-control billrate" placeholder="Rate" name="Bill_Rate[]" id="Rate0" data-id="0"><span class='error'></span></td>
            <td ><input type="text" class="form-control billqty" placeholder="Quantity" name="Bill_Quantity[]" id="Quantity0" data-id="0"><span class='error'></span></td>
            <td ><span class="bill-amount" id="billamount0"></span><input type="hidden" placeholder="Amount" name="Bill_Amount[]" id="Amount0" value=""><span class='error'></span></td>
            <td ><span class="tax-amount" id="taxamount0"></span><input type="text" class="form-control billtax" placeholder="Tax Amount" name="Tax_Amount[]" id="Tax_Amount0" data-id="0"><span class='error'></span></td>
            <td ><span class="net-amount" id="netamount0"></span><input type="hidden" name="Net_Amount[]" id="Net_Amount0" ><span class='error'></span></td>
            <td></td>
        </tr>
        <tr><th colspan="6">Total amount</th><th colspan="2"><span id="billtotal"></span></th><input type="hidden" name="biltot"
                                                                                                     id="biltot" value=""></tr>
        <tr >
            <th colspan="6"></th>
            <th ><button type="submit" class="btn btn-primary" id="savebill" name="Bill_save">Save</button></th>
            <!--            <th ><button type="submit" class="btn btn-primary" id="saveandnewbill" name="Bill_savenew">Save and New</button></th>-->
            <th ><button type="button" class="btn btn-primary" id="cancelbill" name="Bill_cancel" >Cancel</button></th>
        </tr>
        </tbody>
    </table>
</form><?php
/**
 * Created by PhpStorm.
 * User: SolmindsDelli5
 * Date: 11-12-2015
 * Time: 13:36
 */