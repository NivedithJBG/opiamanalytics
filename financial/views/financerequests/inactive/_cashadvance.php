<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/advance.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#advancedate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
        $('#hoadvancedate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
        $('#editadvancedate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var x = 1; //initlal text box count
        //$(add_button).click(function(e){
        $(wrapper).on("click",".add_field_button", function(e){
            //alert('add')
            e.preventDefault();
            if(x < max_fields){
                $('.cashadvancerow').before('<tr>' +
                    '</td>' +
                    '<td>' +
                    '<textarea rows="1" cols="30" class="form-control purpose" data-id="'+x+'" id="purpose'+x+'" name="Purpose[]" placeholder="Purpose..."></textarea>' +
                    '<span class="error"></span></td>' +
                    '<td>' +
                    '<input type="text" class="form-control advanceamount" id="advanceamount'+x+'" data-id="'+x+'" name="Amount[]" placeholder="Amount">' +
                    '<span class="error"></span></td>' +
                    //'<td><select class="form-control advancepaytype" id="advancepaytype'+x+'" data-id="'+x+'" name="paymenttype[]">' +
                    //'<option value="none">Select Payment Type</option>' +
                    //'<option value="1">Cash</option>' +
                    //'<option value="2">Bank</option>' +
                    //'</select></td>' +
                    '<td>' +
                    '<a href="javascript:void(0)" class="btn btn-primary remove_field">Remove</a>' +
                    '</td></tr>');
                x++;

            }
        });
        $(wrapper).on("click",".ho_add_field_button", function(e){
            //alert('add')
            e.preventDefault();
            if(x < max_fields){
                $('#hoadvancerow').before('<tr>' +
                    '</td>' +
                    '<td>' +
                    '<textarea rows="1" cols="30" class="form-control hopurpose" data-id="'+x+'" id="hopurpose'+x+'" name="Purpose[]" placeholder="Purpose..."></textarea>' +
                    '<span class="error"></span></td>' +
                    '<td><select class="form-control hoschedule" id="hoschedule'+x+'" data-id="'+x+'" name="schedule[]" >' +
                                '<option value="none">Select Account</option>' +
                                '<?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                                foreach($acnts AS $accounts):
                                    echo "<option value=".$accounts['id']." >".$accounts->name."</option>";
                                endforeach;?></select><span class="error"></span></td>' +
                    '<td>' +
                    '<input type="text" class="form-control hoadvanceamount" id="hoadvanceamount'+x+'" data-id="'+x+'" name="Amount[]" placeholder="Amount">' +
                    '<span class="error"></span></td>' +
                        //'<td><select class="form-control advancepaytype" id="advancepaytype'+x+'" data-id="'+x+'" name="paymenttype[]">' +
                        //'<option value="none">Select Payment Type</option>' +
                        //'<option value="1">Cash</option>' +
                        //'<option value="2">Bank</option>' +
                        //'</select></td>' +
                    '<td>' +
                    '<a href="javascript:void(0)" class="btn btn-primary remove_field">Remove</a>' +
                    '</td></tr>');
                x++;

            }
        });
        $(".updateadd_field_button").click(function(e){
            //alert('add')
            e.preventDefault();
            if(x < max_fields){
                $('#updatecashadvancerow').before('<tr>' +
                    '</td>' +
                    '<td>' +
                    '<textarea rows="1" cols="30" class="form-control newpurpose" data-id="'+x+'" id="newpurpose'+x+'" name="newPurpose[]" placeholder="Purpose..."></textarea>' +
                    '<span class="error"></span></td>' +
                    '<td>' +
                    '<input type="text" class="form-control newadvanceamount" id="newadvanceamount'+x+'" data-id="'+x+'" name="newAmount[]" placeholder="Amount">' +
                    '<span class="error"></span></td>' +
                        //'<td><select class="form-control advancepaytype" id="advancepaytype'+x+'" data-id="'+x+'" name="paymenttype[]">' +
                        //'<option value="none">Select Payment Type</option>' +
                        //'<option value="1">Cash</option>' +
                        //'<option value="2">Bank</option>' +
                        //'</select></td>' +
                    '<td>' +
                    '<a href="javascript:void(0)" class="btn btn-primary updateremove_field">Remove</a>' +
                    '</td></tr>');
                x++;

            }
        });
        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
            var totalamount=0;
            $('.advanceamount').each(function(){
                totalamount+=$(this).val()*1;
            });
            var hototalamount=0;
            $('.hoadvanceamount').each(function(){
                hototalamount+=$(this).val()*1;
            });
            $('#totaladv').html(totalamount);
            $('#hototaladv').html(hototalamount);
        });
        $(".updateinput_fields_wrap").on("click",".updateremove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
            var cashadvanceid=$(this).data("id");
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/deletecashadvance',
                beforeSend : function(){
                    $('#updateremove_field'+cashadvanceid).attr("disabled", true);
                },
                dataType: "json",
                data: {cashadvance:cashadvanceid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#updateremove_field'+cashadvanceid).attr("disabled", false);
                    }

                }
            });
            var totalamount=0;
            $('.updateadvanceamount').each(function(){
                totalamount+=$(this).val()*1;
            });
            $('.newadvanceamount').each(function(){
                totalamount+=$(this).val()*1;
            });
            $('#edittotaladv').html(totalamount);
        })
    });
</script>
<h2 class="acc_trigger" id="advance"><a href="javascript:void(0)">3. Request For Advance</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <!--<div class="row show-grid">
                <div class="col-md-3"><a href="<?php /*echo Yii::app()->request->baseUrl; */?>/FinanceRequests/cashbill"><button type="button" class="btn btn-success"  id="addcashbill"><span class="glyphicon glyphicon-plus-sign"></span>Add Cash Bill</button></a> </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listcashbill"><span class="glyphicon glyphicon-list-alt"></span>List Cash Bill</button></div>
            </div>-->
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addcashadvance"><span class="glyphicon glyphicon-plus-sign"></span>Create Advance Request</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listcashadvance"><span class="glyphicon glyphicon-list-alt"></span>List Advance Request</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listappadvance"><span class="glyphicon glyphicon-list-alt"></span>Approved Advance Request</button></div>
            </div>
            <div id="cashadvancelistsection">
                <div class="row show-grid">
                    <table class="table table-bordered" id="cashadvancetable" style="display: table; overflow: hidden;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <!--<th>User</th>-->
                            <th>Purpose</th>
                            <th>Amount</th>
                            <!--<th>Ledger</th>-->
                            <th colspan="3"></th>
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="cashadvanceitems">

                        </tbody>
                    </table>
                </div>
            </div>
            <div id="appadvancelistsection">
                <div class="row show-grid">
                    <table class="table table-bordered" id="appadvancetable" style="display: table; overflow: hidden;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <!--<th>User</th>-->
                            <th>Purpose</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="appadvanceitems">

                        </tbody>
                    </table>
                </div>
            </div>
            <div id="cashadvanceaddsection" style="display: none">
                <form action="" id="cashadvanceform">
                    <div class="row show-grid">
                        <div class="col-md-2">
                            <input type="text" class="form-control datepicker" name="Advance_Date" id="advancedate" value="<?php echo date('d-m-Y');?>">
                            <span class="error"></span>
                        </div>
                        <!--<div class="col-md-3" style="text-align: left">
                            <select id="advanceprojlist" name="advanceprojlist" class="form-control">
                                <option value="none">Select Place</option>
                                <?php
/*                                $userid=Yii::app()->user->id;
                                $user=User::model()->active()->findbyPk($userid);
                                if($user['superuser']==1 || $user['superuser']==2)
                                {
                                    $project=Projects::model()->findAll(array('condition'=>'Status=0 AND Project_Delete_Status=0'));
                                    foreach($project AS $list):
                                        echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                                    endforeach;
                                }
                                else{
                                    $userprojects=UserProjects::model()->findAll(
                                        array('condition'=>'userid =:id','params'=> array(':id' => $userid))
                                    );
                                    foreach($userprojects AS $projects):
                                        $project=Projects::model()->findAll(
                                            array('condition'=>'Project_Id =:id','params'=> array(':id' => $projects['projectid']))
                                        );
                                        foreach($project AS $list):
                                            echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                                        endforeach;
                                    endforeach;
                                }
                                */?>
                            </select>
                            <span class="error"></span>
                        </div>-->
                        <!--<div class="col-md-3" style="text-align: left">
                            <select id="schedule" class="form-control schedule" name="schedule">
                                <option value="none">Select Accounthead</option>
                                <?php
/*                                $employees=AccountsItem::model()->findAll(array('condition'=>'(account_type=16 AND schedule=3) OR account_type=1 OR account_type=2','order'=>'name ASC'));
                                //echo count($employees);exit;
                                foreach($employees AS $employee):
                                    echo "<option value='".$employee['id']."'>".$employee['name']."</option>";
                                endforeach;
                                */?>
                            </select>
                            <span class="error"></span>
                        </div>-->
                    </div>
                    <table class="table table-bordered" id="cashadvancetable" style="display: table;">
                        <thead>
                            <tr>
                                <th>Purpose</th>
                                <th>Amount</th>
                                <!--<th>Payment Type</th>-->
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="input_fields_wrap">
                            <tr>
                                <td>Opening Balance</td>
                                <td id="cashadvopenbal"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <textarea rows="1" cols="30" class="form-control purpose" id="purpose0" data-id="0" name="Purpose[]" placeholder="Purpose..."></textarea>
                                    <span class="error"></span>
                                </td>
                                <td>
                                    <input type="text" class="form-control advanceamount" id="advanceamount0" data-id="0" name="Amount[]" placeholder="Amount">
                                    <span class="error"></span>
                                </td>
                                <!--<td>
                                    <select class="form-control advancepaytype" id="advancepaytype0" data-id="0" name="paymenttype[]">
                                        <option value="none">Select Payment Type</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Bank</option>
                                    </select>
                                </td>-->
                                <td>
                                    <button type="button" class="btn btn-primary add_field_button" id="addmore" value="Add">Add</button>
                                </td>
                            </tr>
                            <tr class="cashadvancerow" id="cashadvancerow">
                                <td>Total</td>
                                <td colspan="2">
                                    <span id="totaladv"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><button type="button" class="btn btn-primary cashadvancecreate" name="cashadvancecreate" value="draft">Save as draft</button></td>
                                <td colspan="2"><button type="button" class="btn btn-primary cashadvancecreate" name="cashadvancecreate" value="approval">Send for approval</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div id="hoadvanceaddsection" style="display: none">
                <form action="" id="hoadvanceform">
                    <div class="row show-grid">
                        <div class="col-md-2">
                            <input type="text" class="form-control datepicker" name="Advance_Date" id="hoadvancedate" value="<?php echo date('d-m-Y');?>">
                            <span class="error"></span>
                        </div>
                    </div>
                    <table class="table table-bordered" id="hoadvancetable" style="display: table;">
                        <thead>
                        <tr>
                            <th>Purpose</th>
                            <th>Accounthead</th>
                            <th>Amount</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody class="input_fields_wrap">
                        <tr>
                            <td colspan="2">Opening Balance</td>
                            <td id="hoadvopenbal"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <textarea rows="1" cols="30" class="form-control hopurpose" id="hopurpose0" data-id="0" name="Purpose[]" placeholder="Purpose..."></textarea>
                                <span class="error"></span>
                            </td>
                            <td>
                                <select id="hoschedule0" class="form-control hoschedule" name="schedule[]" data-id="0">
                                    <option value="none">Select Accounthead</option>
                                    <?php
                                    $employees=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                                    foreach($employees AS $employee):
                                        echo "<option value='".$employee['id']."'>".$employee['name']."</option>";
                                    endforeach;
                                    ?>
                                </select>
                                <span class="error"></span>
                            </td>
                            <td>
                                <input type="text" class="form-control hoadvanceamount" id="hoadvanceamount0" data-id="0" name="Amount[]" placeholder="Amount">
                                <span class="error"></span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary ho_add_field_button" id="hoaddmore" value="Add">Add</button>
                            </td>
                        </tr>
                        <tr class="hoadvancerow" id="hoadvancerow">
                            <td>Total</td>
                            <td></td>
                            <td colspan="2">
                                <span id="hototaladv"></span>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><button type="button" class="btn btn-primary hoadvancecreate" name="cashadvancecreate" value="draft">Save as draft</button></td>
                            <td colspan="2"><button type="button" class="btn btn-primary hoadvancecreate" name="cashadvancecreate" value="approval">Send for approval</button></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div id="cashadvanceviewsection" style="display: none">
                <form action="" id="cashadvanceviewform">
                    <div class="row show-grid">
                        <div class="col-md-2">
                            <input type="text" class="form-control datepicker" name="Advance_Date" id="editadvancedate">
                        </div>
                        <!--<div class="col-md-3" style="text-align: left">
                            <select id="editadvanceprojlist" name="advanceprojlist" class="form-control">
                                <option value="none">Select Place</option>

                            </select>
                            <span class="error"></span>
                        </div>-->
                        <!--<div class="col-md-3" style="text-align: left">
                            <select id="editschedule" class="form-control schedule" name="schedule">
                                <option value="none">Select Accounthead</option>

                            </select>
                            <span class="error"></span>
                        </div>-->
                    </div>
                    <table class="table table-bordered" id="editcashadvancetable" style="display: table;">
                        <thead>
                        <tr>
                            <th>Purpose</th>
                            <th>Amount</th>
                            <!--<th>Payment Type</th>-->
                            <th><button type="button" class="btn btn-primary updateadd_field_button" id="editaddmore" value="Add">Add</button></th>
                        </tr>
                        </thead>
                        <tbody class="updateinput_fields_wrap" id="viewadvanceitems">


                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>