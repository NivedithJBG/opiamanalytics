<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/expensestmt.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var x = 1; //initlal text box count
        $(add_button).click(function(e){
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                //text box increment
                var expacntid=$('#expacntid').val();
                var expacntname=$('#expacntname').val();
                var expdate=$('#expdate').val();
                var cashcount=$('#acntlistcount').val();
                if(cashcount==0)
                {
                    $('#totalexprow').before('<tr><td><input type="text" class="form-control" name="expdate[]" id="datepicker'+x+'" value="<?php echo date('d-m-Y')?>"></td>' +
                    '<td><select class="form-control" name="expacnt[]">' +
                    '<option value="none">Select Accounthead</option>' +
                    '<?php $users=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                    foreach($users AS $user):
                    echo "<option value=".$user['id']." >".$user->name."</option>";
                    endforeach;?></select></td>' +
                    '<td><textarea rows="2" cols="30" class="form-control" name="exppurpose[]"></textarea></td><td></td>' +
                    '<td style="text-align: right;"><input type="text" class="form-control expamount" name="expamount[]" value=""></td>' +
                    '<td><a href="#" class="btn btn-primary remove_field">Remove</a></td></tr>');
                $('#datepicker'+x+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
                x++;
                }
                else {

                    $('#totalexprow').before('<tr><td><input type="text" class="form-control" name="expdate[]" id="datepicker'+x+'" value="<?php echo date('d-m-Y')?>"></td>' +
                        '<td><textarea rows="2" cols="30" class="form-control" name="exppurpose[]"></textarea></td><td></td>' +
                        '<td style="text-align: right;"><input type="text" class="form-control expamount" name="expamount[]" value=""></td>' +
                        '<td><a href="#" class="btn btn-primary remove_field">Remove</a></td></tr>');
                    $('#datepicker'+x+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
                    x++;
                }
            }
        });
        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        })
    });
</script>
<!--<script>
    function PrintElem(elem)
    {

        var mywindow = window.open('', 'PRINT', 'height=400,width=600');

        mywindow.document.write('<html><head><title>' + document.title  + '</title>');
        mywindow.document.write('</head><body >');
        mywindow.document.write('<h1>' + document.title  + '</h1>');
        mywindow.document.write(document.getElementById(elem).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }
</script>-->
<h2 class="acc_trigger" id="expensestmt"><a href="javascript:void(0)">3. Expense Statement</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <input type="hidden" id="listexpensestmt">
                <input type="hidden" id="closeexpensestmt">
                <input type="hidden" id="advancegroupid">
                <div class="col-md-10" style="padding-left: 10%"><p>Expense Statement</p></div>
                <!--<div class="col-md-2" id="print" style="padding-top: 10px;">
                    <button type="button" class="btn btn-primary" onclick="PrintElem('expensestmtlistsection')">Print
                        <i class="glyphicon glyphicon-print"></i>
                    </button>
                </div>-->

                <!--<div class="col-md-3"><button type="button" class="btn btn-danger" id="listexpensestmt"><span class="glyphicon glyphicon-list-alt"></span>List Expense Statement</button></div>-->
                <!--<div class="col-md-3"><button type="button" class="btn btn-danger" id="listappexpensestmt"><span class="glyphicon glyphicon-list-alt"></span>Approved Expense Statement</button></div>-->
            </div>
            <form id="expensereportform">
                <div id="expensestmtlistsection">
                    <div class="row show-grid">
                        <table class="table table-bordered" id="expensestmttable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <!--<th>#</th>-->
                                <th>Date</th>
                                <th id="acnthdth">Account Head</th>
                                <th>Description</th>
                                <!--<th>Amount</th>-->
                                <th>Income</th>
                                <th>Expense</th>
                                <th><button class="btn btn-primary add_field_button">Add</button></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody class="input_fields_wrap" id="expensestmtitems">

                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
            <div id="appexpensestmtlistsection">
                <div class="row show-grid">
                    <table class="table table-bordered" id="appexpensestmttable" style="display: table; overflow: hidden;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>User</th>
                            <th>Purpose</th>
                            <th>Amount</th>
                            <th colspan="1"></th>
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="appexpensestmtitems">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>