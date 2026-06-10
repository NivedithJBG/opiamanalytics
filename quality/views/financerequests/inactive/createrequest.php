<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/request.js" type="text/javascript"></script>
<script>
    function goBack()
    {
        newwindow = window.open('<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/index','_self',false);
    }

</script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID

        $('#datepicker0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});


        var x = 1; //initlal text box count
        var y = 1; //initlal text box count
        $(add_button).click(function(e){ //on add input button click

            var z=x+y;
            var w=x-y;

            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                //text box increment
                $('#userrequest'+w).after('<tr id="userrequest'+x+'" style="background-color: #ffffff;">' +
                        /*'<td><input type="text" class="form-control datepicker" name="Request_Date[]" id="datepicker'+x+'"  value="<?php echo date("d-m-Y");?>"></td>' +*/
                    '<td colspan="2"><select class="form-control accountshead" id="accountshead'+x+'" data-id="'+x+'" name="accountshead[]" >' +
                    '<option value="0">Select Account</option>' +
                    '<?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                                foreach($acnts AS $accounts):
                                    echo "<option value=".$accounts['id']." >".$accounts->name."</option>";
                                endforeach;?></select><span class="error"></span></td>' +
                    '<td colspan="4"><textarea rows="1" class="form-control Purpose" cols="50" id="Purpose'+x+'" data-id="'+x+'" name="Request_Purpose[]" ></textarea> <span class="error"></span></td>' +
                    '<td><input type="text" class="form-control Amount" placeholder="Amount" name="Request_Amount[]" id="Amount'+x+'" data-id="'+x+'"><span class="error"></span> </td>' +
                    '<td><select class="form-control paymethod" id="paymethod'+x+'" data-id="'+x+'" name="paymethod[]">' +
                    '<option value="none">Select Payment Type</option>' +
                    '<option value="1">Cash Bills</option>' +
                    '<option value="2">Credit Bills</option>' +
                    '<option value="3">Advances</option>' +
                    '<option value="4">Transfers</option>' +
                    '<option value="5">Withdrawals</option>' +
                    '<option value="7">Statutory Payments</option>' +
                    '<option value="8">Miscellaneous</option>' +
                    '</select><span class="error"></span></td>' +
                    '<td id="paytype'+x+'"></td>'+
                    '<td><a href="#" class="remove_field">Remove</a></td></tr>');
                //$(wrapper).append('<div><input type="text" name="mytext[]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
                $('#datepicker'+x+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
                var project = $("#project").val();

                $.ajax({
                    type:'POST',
                    url:'../projects/WbsSearch',
                    dataType:"json",
                    data:{project:project},
                    success:function (data) {
                        if (data.error == 'No') {
                            var option = data.result;

                            $("#wbsid"+(x-1)).empty().append(option);

                        }
                        else {
                            alert(data.errortext);
                        }

                    }
                });
                x++;
            }

        });

        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        });
        $("form").submit(function(){
            $("#project").prop("disabled", false);
        });
    });

    $(document).on("change", ".paymethod", function () {
        var id=$(this).attr('data-id');
        var paymethod = $("#paymethod"+id).val();
        $.ajax({
            type:'POST',
            url:'../FinanceRequests/getbillno',
            dataType:"json",
            data:{paytype:paymethod},
            success:function (data) {
                if (data.error == 'No') {
                    if (data.paytype==1 || data.paytype==2)
                    {
                        var selectbox="<select class='form-control billnoinfo' id='billnoinfo"+id+"' data-id='"+id+"' name='billnoinfo[]'></select><span class='error'></span>";
                        $('#paytype'+id).html(selectbox);
                        $('#billnoinfo'+id).html(data.result);
                    }
                    if (data.paytype==3 || data.paytype==4 || data.paytype==6)
                    {
                        var selectbox="<select class='form-control partyinfo' id='partyinfo"+id+"' data-id='"+id+"' name='partyinfo[]'></select>";
                        $('#paytype'+id).html(selectbox);
                        $('#partyinfo'+id).html(data.result);
                    }
                }
                else {
                    var selectbox="<select class='form-control' style='display: none'></select>";
                    $('#paytype'+id).html(selectbox);
                }
            }
        });
    });
    /*$(document).on("change", "#project", function () {
     //var itemid = $(this).attr('data-id');
     var project = $("#project").val();
     $.ajax({
     type:'POST',
     url:'../projects/WbsSearch',
     dataType:"json",
     data:{project:project},
     success:function (data) {
     if (data.error == 'No') {
     var option = data.result;
     $(".wbsid").empty().append(option);
     $('#project').prop('disabled', 'disabled');
     }
     else {
     alert(data.errortext);
     }

     }
     });
     });*/
    $(document).on("change", ".wbsid", function () {
        //var itemid = $(this).attr('data-id');
        var wbs = $(this).val();
        var requestid=$(this).attr('data-id');
        $.ajax({
            type:'POST',
            url:'../projects/IOWSearch',
            dataType:"json",
            data:{wbs:wbs},
            success:function (data) {
                if (data.error == 'No') {
                    var option = data.result;
                    $("#iowid"+requestid).empty().append(option);
                }
                else {
                    alert(data.errortext);
                }
            }
        });
    });
</script>
<h1>Fund Request </h1>
<form method="POST" action="" id="requestform">
    <table class="table table-bordered ">
        <tbody class="input_fields_wrap">
        <tr style="background-color: #ffffff;">
            <th>Date</th>
            <td><input type="text" class="form-control datepicker" name="Request_Date" id="datepicker0" value="<?php /*echo date("d-m-Y");*/?>"></td>
            <th><span class="headings">Place</span></th>
            <td colspan="3">
                <select class="form-control" name="place" id="place">
                    <option value="0">Select Place</option>
                    <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);
                    if(Yii::app()->user->isAdmin() || $user['superuser']==2): ?>
                        <?php foreach($adminprojects AS $data):?>
                            <option value="<?php echo $data['Project_Id'];?>"><?php echo $data['Name']; ?></option>
                        <?php endforeach;?>
                    <?php else: ?>
                        <?php foreach($userprojects AS $data):?>
                            <option value="<?php echo $data['projectid'];?>"><?php echo $data['Name']; ?></option>
                        <?php endforeach;?>
                    <?php endif;?>
                </select>
                <span class='error'></span>
            </td>
            <th><span class="headings">Project</span></th>
            <td colspan="3">
                <select class="form-control" name="Request_Project" id="project"  title="Select Project">
                    <option value="0">Select Project</option>
                    <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);
                    if(Yii::app()->user->isAdmin() || $user['superuser']==2): ?>
                        <?php foreach($adminprojects AS $data):?>
                            <option value="<?php echo $data['Project_Id'];?>"><?php echo $data['Name']; ?></option>
                        <?php endforeach;?>
                    <?php else: ?>
                        <?php foreach($userprojects AS $data):?>
                            <option value="<?php echo $data['projectid'];?>"><?php echo $data['Name']; ?></option>
                        <?php endforeach;?>
                    <?php endif;?>
                    <?php /*$projects=Projects::model()->findAll(array('condition'=>'Project_Delete_Status=0'));
                        foreach($projects AS $project):*/?><!--
                            <option value="<?php /*echo $project['Project_Id']*/?>"><?php /*echo $project['Name']*/?></option>
                            --><?php /*endforeach;*/?>
                </select>
                <span class='error'></span>
            </td>
        </tr>
        <tr>
            <th colspan="2">Account Head</th>
            <th colspan="4">Purpose</th>
            <th>Amount</th>
            <th>Payment Type</th>
            <th></th>
            <th><input type="button" class="add_field_button" name="addmore" id="addmore" title="Add more" value="Add more"></th>
        </tr>
        <tr id="userrequest0" style="background-color: #ffffff;">
            <td colspan="2">
                <select class="form-control accountshead" id="accountshead<?php echo $data['Id']; ?>" data-id="<?php echo $data['Id']; ?>" name="accountshead[]" >
                    <option value="0">Select Account</option>
                    <?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                    foreach($acnts AS $accounts):
                        if($data['account_id']==$accounts->id):$selected='selected';else:$selected='';endif;
                        echo "<option value='".$accounts->id."' id='acnts' $selected>".$accounts->name."</option>";
                    endforeach;?>
                </select>
                <span class="error"></span>
            </td>
            <td colspan="4"><textarea rows="1" class="form-control Purpose" cols="50" id="Purpose0" data-id="0" name="Request_Purpose[]" ></textarea><span class='error'></span></td>
            <td><input type="text" class="form-control Amount" placeholder="Amount" name="Request_Amount[]" id="Amount0" data-id="0"><span class='error'></span></td>
            <td>
                <select class="form-control paymethod" id="paymethod0" data-id="0" name="paymethod[]">
                    <option value="none">Select Payment Type</option>
                    <option value="1">Cash Bills</option>
                    <option value="2">Credit Bills</option>
                    <option value="3">Advances</option>
                    <option value="4">Transfers</option>
                    <option value="5">Withdrawals</option>
                    <option value="6">Statutory Payments</option>
                    <option value="7">Miscellaneous</option>
                </select><span class='error'></span>
            </td>
            <td id="paytype0">

            </td>
            <td></td>
        </tr>
        <tr>
            <th colspan="7"></th>
            <th><button type="submit" class="btn btn-primary" id="saverequest" value="1" name="Request_saverequest">Save</button></th>
            <th><button type="submit" class="btn btn-primary" id="saveandcreate" value="1" name="saveandcreate">Save And Create New</button></th>
            <th><button type="button" class="btn btn-primary" id="cancelrequest" value="0" name="Request_cancelrequest" onclick="goBack()">Cancel</button></th>
        </tr>
        </tbody>
    </table>
</form>
<input type="hidden" id="pageaction" value="create">
<!--<input type="hidden" id="ProductId" value="<?php /*echo $model->Product_Id;*/?>">-->
<!--<script type="text/javascript">
    $("#searchname").autocompleteArray([<?php /*echo $dataProvider;*/?>]);
</script>-->
<!--<script type="text/javascript">

    $(document).on('focus','.datepicker',function(){
            $(this).datepicker({
                dateFormat: $.datepicker.W3C,
                changeMonth: true,
                changeYear: true
            });
    })
</script>-->