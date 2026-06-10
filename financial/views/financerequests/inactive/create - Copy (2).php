<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/request.js" type="text/javascript"></script>
<!--<button type="button" value="Back" name="goback" title="back" class="btn btn-primary" style="float: right;width: 100px" onclick="goBack()">Back</button>-->
<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID

        var x = 1; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
            /*var fulldate=new Date();
            var date=fulldate.getDate();
            var month=fulldate.getMonth() +1;
            var year=fulldate.getFullYear()

            //$("#datepicker").val(currentdate);
            alert(currentdate)*/
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                x++; //text box increment
                $('#userrequest').before('<tr style="background-color: #ffffff;">' +
                    '<td><input type="text" class="form-control datepicker" name="Request_Date[]" id="datepicker" value="<?php echo date("d-m-y");?>"></td>' +
                    '<td><textarea rows="1" class="form-control" cols="50" id="Purpose" name="Request_Purpose[]" ></textarea> <span class="error"></span></td>' +
                    '<td><input type="text" class="form-control" placeholder="Amount" name="Request_Amount[]" id="Amount"><span class="error"></span> </td>' +
                    '<td><a href="#" class="remove_field">Remove</a></td></tr>');
                //$(wrapper).append('<div><input type="text" name="mytext[]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
            }
        });

        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        })
    });
</script>
<h1 >Fund Request </h1>
<form method="POST" action="" id="requestform">
    <table class="table table-bordered ">
        <tbody class="input_fields_wrap">
        <tr style="background-color: #ffffff;">
            <th><span class="headings">Project</span></th>
            <td ><select class="form-control" name="Request_Project" id="project"  title="Select Project">
                <option value="0">Select Project</option>
                <?php if(User::model()->isAdmin()): ?>
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
            <th colspan="2"></th>
        </tr>
        <tr>
            <th>Date</th>
            <th>Purpose</th>
            <th>Amount</th>
            <th><input type="button" class="add_field_button" name="addmore" id="addmore" title="Add more" value="Add more"></th>
        </tr>
        <tr id="userrequest" style="background-color: #ffffff;">
            <td><input type="text" class="form-control datepicker" name="Request_Date[]" id="datepicker" value="<?php echo date("d-m-y");?>"></td>
            <td><textarea rows="1" class="form-control" cols="50" id="Purpose" name="Request_Purpose[]" ></textarea> <span class='error'></span></td>
            <td ><input type="text" class="form-control" placeholder="Amount" name="Request_Amount[]" id="Amount"><span class='error'></span></td>
            <td></td>
        </tr>
        <!--<tr style="background-color: white">
            <th><span class="headings">Purpose</span></th>
            <td><textarea rows="4" class="form-control" cols="50" id="Purpose" name="Request_Purpose" style="width: 50%"></textarea> <span class='error'></span></td>
        </tr>-->


        <tr >
            <th></th>
            <th></th>
            <th ><button type="submit" class="btn btn-primary" id="saverequest" value="1" name="Request_saverequest">Save</button></th>
            <th ><button type="button" class="btn btn-primary" id="cancelrequest" value="0" name="Request_cancelrequest" onclick="goBack()">Cancel</button></th>
        </tr>
        </tbody>
        <!--<thead>
            <tr>
                <th><span class="headings">Product Name</span><input type="text" class="form-control" placeholder="Product Name" name="Product_Name" id="Name"><span class='error'></span></th>
                <th><span class="headings">Unit</span><input type="text" class="form-control" placeholder="Unite" name="Product_Unit" id="Unit"><span class='error'></span></th>
                <th><span class="headings">Rate</span><input type="text" class="form-control" placeholder="Rate"  id="productratetotal" name="Product_Rate" readonly="readonly"  value="0"><span class='error'></span></th>
                <th><button type="button" class="btn btn-primary" id="saveproduct" value="1" name="Product_saveproduct">Save</button></th>
            </tr>-->
        </thead>
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