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

        $('#datepicker0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});


        var x = 1; //initlal text box count
        $(add_button).click(function(e){
            $.ajax({
                type: 'POST',
                url: '../../FinanceRequests/checkstatus',
                dataType: "json",
                data: {groupid:$('#groupid').val()},
                success: function(data){
                    if(data.result==0)
                    {
                        e.preventDefault();
                        if(x < max_fields){ //max input box allowed
                            //text box increment
                            $('#userrequest').before('<tr style="background-color: #ffffff;">' +
                                '<td><input type="text" class="form-control datepicker" name="Request_Date[]" id="datepicker'+x+'"  value="<?php echo date("d-m-Y");?>"></td>' +
                                '<td><textarea rows="1" class="form-control" cols="50" id="Purpose" name="Request_Purpose[]" ></textarea> <span class="error"></span></td>' +
                                '<td><input type="text" class="form-control" placeholder="Amount" name="Request_Amount[]" id="Amount"><span class="error"></span> </td>' +
                                '<td><a href="#" class="remove_field">Remove</a></td></tr>');
                            //$(wrapper).append('<div><input type="text" name="mytext[]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
                            $('#datepicker'+x+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
                            x++;
                        }
                    }
                    else
                    {
                        alert("Cannot Add Request Now");
                    }
                }
            });

        });

        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        })
    });
</script>
<h1>View Requests</h1>
    <form method="POST" action="" id="approvalfrom">
        <table class="table table-bordered ">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Purpose</th>
                    <th>Amount</th>
                    <th style="width: 15%;">
                        <input type="button" class="btn btn-primary add_field_button" id="addmore" value="Add Requests">
                    </th>
                </tr>
            </thead>
            <tbody class="input_fields_wrap">
            <?php foreach($datarows AS $key=>$data):?>
            <tr>
                <td>
                    <input type="hidden" value="<?php echo date('d-m-Y',strtotime($data['date'])); ?>" name="date">
                    <?php echo $data['date']; ?>
                    <input type="hidden" value="<?php echo $data['group_id'];?>" id="groupid" name="groupid">
                </td>
                <td><?php echo $data['Purpose']; ?>
                    <input type="hidden" value="<?php echo $data['project_id'];?>" name="project_id"></td>
                <td><?php echo $data['Amount']; ?>
                    <input type="hidden" value="<?php echo $data['place'];?>" name="place"></td>
                <td></td>
            </tr>
            <?php endforeach;?>
            <tr id="userrequest">
                <td colspan="2"></td>
                <td ><button type="submit" class="btn btn-primary">Save</button></td>
                <td><button type="button" class="btn btn-primary" id="cancelrequest" onclick="goBack()">Cancel</button></td>
            </tr>
            </tbody>
        </table>
    </form>
