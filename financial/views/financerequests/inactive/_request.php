<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/request.js" type="text/javascript"></script>
<!--<script type="text/javascript">
    $(function(){
        var type = 'request';
        //alert(type)
        setTimeout(function() {
            $('#'+type).trigger('click');
        },1000);
        //$('#request').addClass('active').next('.acc_container').slideUp();
    });
</script>-->
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
                $('#fundreqsaverow').before('<tr id="fundreqrow'+x+'">' +
                    '<td>'+(x+1)+'</td>' +
                    '<td><textarea class="form-control fundpurpose" name="fundpurpose[]" rows="3" cols="45" placeholder="Purpose" data-id="'+x+'" id="fundpurpose'+x+'"></textarea>' +
                    '<span class="error"></span></td>' +
                    '<td><select class="form-control fundpaymode" name="fundpaymode[]" id="fundpaymode'+x+'" data-id="'+x+'">' +
                    '<option value="none">Select Payment Mode</option>' +
                    '<option value="1">Cash</option>' +
                    '<option value="2">Bank</option>' +
                    '<option value="3">Contra</option></select>' +
                    '<span class="error"></span></td>' +
                    '<td><select class="form-control fundpaytype" name="fundpaytype[]" id="fundpaytype'+x+'" data-id="'+x+'">' +
                    '<option value="none">Select Payment Type</option>' +
                    '<option value="1">Local Purchase-Cash</option>' +
                    '<option value="2">Local Purchase-Credit</option>' +
                    '<option value="3">Cash Expanse</option>' +
                    '<option value="4">Credit Expanse</option>' +
                    '<option value="5">Credit Invoice against PO</option>' +
                    '<option value="6">Withdrawal</option>' +
                    '<option value="7">Transfer</option></select>' +
                    '<span class="error"></span></td>' +
                    '<td><input type="text" class="form-control fundamount" name="fundamount[]" data-id="'+x+'" id="fundamount'+x+'" placeholder="Amount" value="">' +
                    '<span class="error"></span></td>' +
                    '<td><input type="text" class="form-control fundcgstamount" name="fundcgstamount[]" data-id="'+x+'" id="fundcgstamount'+x+'" placeholder="CGST" value="">' +
                    '<span class="error"></span></td>' +
                    '<td><input type="text" class="form-control fundsgstamount" readonly name="fundsgstamount[]" data-id="'+x+'" id="fundsgstamount'+x+'" placeholder="SGST" value="">' +
                    '<span class="error"></span></td>' +
                    '<td><input type="text" class="form-control fundigstamount" name="fundigstamount[]" data-id="'+x+'" id="fundigstamount'+x+'" placeholder="IGST" value="">' +
                    '<span class="error"></span></td>' +
                    '<td><span id="fundreqnet'+x+'"></span></td>' +
                    '<td><input type="text" class="form-control fundpurchaseadv" name="fundpurchaseadv[]" data-id="'+x+'" id="fundpurchaseadv'+x+'" placeholder="Purchase Advance"></td>' +
                    '<td><a href="#" class="remove_field">Remove</a></td>' +
                    '</tr>');
                //$(wrapper).append('<div><input type="text" name="mytext[]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
                $('#datepicker'+x+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
                var project = $("#project").val();

                /*$.ajax({
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
                });*/
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
</script>
<script type="text/javascript">
    $(function() {
        $( "#reqhistoryfromdate" ).datepicker({ defaultDate:new Date(),changeMonth: true,
            changeYear: true,dateFormat: 'dd-mm-yy' });
    });
    $(function() {
        $( "#reqhistorytodate" ).datepicker({  maxDate: new Date(),changeMonth: true,
            changeYear: true,dateFormat: 'dd-mm-yy' });
    });
</script>
<h2 class="acc_trigger" id="request"><a href="javascript:void (0)">4. Fund Request</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <?php /*if(User::model()->active()->findbyPk(Yii::app()->user->id)->superuser==3):*/?><!--
                    <div class="col-md-3"><a href="<?php /*echo Yii::app()->request->baseUrl; */?>/FinanceRequests/createrequest"><button type="button" class="btn btn-success"  id="addrequest"><span class="glyphicon glyphicon-plus-sign"></span>Add Request</button></a> </div>
                <?php /*else:*/?>
                    <div class="col-md-3"><a href="<?php /*echo Yii::app()->request->baseUrl; */?>/FinanceRequests/create"><button type="button" class="btn btn-success"  id="addrequest"><span class="glyphicon glyphicon-plus-sign"></span>Add Request</button></a> </div>
                --><?php /*endif;*/?>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger" id="addrequest"><span class="glyphicon glyphicon-list-alt"></span>Add Request</button>
                    <button style="display: none" type="button" class="btn btn-danger" id="closefundreq"><span class="glyphicon glyphicon-list-alt"></span>Close Request</button>
                </div>
                <!--<div class="col-md-3"><button type="button" class="btn btn-success"  id="addproduct"><span class="glyphicon glyphicon-plus-sign"></span>Add Product</button> </div>-->
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger" style="display: none" id="listrequest"><span class="glyphicon glyphicon-list-alt"></span>List Request</button>
                    <button type="button" class="btn btn-danger" id="listpendingrequest"><span class="glyphicon glyphicon-list-alt"></span>Pending Approval</button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger" id="requesthistory"><span class="glyphicon glyphicon-list-alt"></span>Request History</button>
                </div>
            </div>
            <div id="requestaddsection" style="display: none">
                <form action="" id="fundrequestform">
                    <div class="row show-grid">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Place</th>
                                    <th>Project</th>
                                    <th>Accounthead</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control datepicker" name="Request_Date" id="datepicker0" value="<?php echo date('d-m-Y');?>">
                                        <span class="error"></span>
                                    </td>
                                    <td>
                                        <?php
                                        $userid=Yii::app()->user->id;
                                        $user=User::model()->active()->findbyPk($userid);
                                        echo $user['username'];
                                        ?>
                                        <input type="hidden" name="requser_id" value="<?php echo $userid;?>">
                                    </td>
                                    <td>
                                        <?php
                                        $userid=Yii::app()->user->id;
                                        $userproject=UserProjects::model()->find(array('condition'=>'userid='.$userid.' '));
                                        $useraccount=UserAccounts::model()->find(array('condition'=>'user_id='.$userid.' ','order'=>'account_id ASC'));
                                        $projectname=Projects::model()->findByPk($userproject['projectid'])->Name;
                                        echo $projectname;
                                        ?>
                                        <input type="hidden" name="requser_place" id="requser_place" value="<?php  echo $userproject['projectid'];?>">
                                    </td>
                                    <td>
                                        <select class="form-control" name="Request_Project" id="project"  title="Select Project">
                                            <option value="0">Select Project</option>
                                            <?php
                                            $userid=Yii::app()->user->id;
                                            $user=User::model()->active()->findbyPk($userid);
                                            if($user['superuser']==1)
                                            {
                                                $project=Projects::model()->findAll(array('condition'=>'Status=0 AND Project_Delete_Status=0'));
                                                foreach($project AS $list):
                                                    if($list->Project_Id==$userproject['projectid']):
                                                        $selected="selected";
                                                    else:
                                                        $selected="";
                                                    endif;
                                                    echo "<option value='".$list->Project_Id."' ".$selected.">".$list->Name."</option>";
                                                endforeach;
                                            }
                                            elseif($user['superuser']==2)
                                            {
                                                $project=Projects::model()->findAll(array('condition'=>'Status=0 AND Project_Delete_Status=0'));
                                                foreach($project AS $list):
                                                    if($list->Project_Id==$userproject['projectid']):
                                                        $selected="selected";
                                                    else:
                                                        $selected="";
                                                    endif;
                                                    echo "<option value='".$list->Project_Id."' ".$selected.">".$list->Name."</option>";
                                                endforeach;
                                            }
                                            else{
                                                $userprojects=UserProjects::model()->findAll(
                                                    array('condition'=>'userid =:id','params'=> array(':id' => $userid))
                                                );
                                                foreach($userprojects AS $projects):
                                                    $project=Projects::model()->find(
                                                        array('condition'=>'Project_Id =:id AND Project_Delete_Status=0','params'=> array(':id' => $projects['projectid']))
                                                    );
                                                    if($projects['projectid']==$userproject['projectid']):
                                                        $selected="selected";
                                                    else:
                                                        $selected="";
                                                    endif;
                                                    echo "<option value='".$project->Project_Id."' ".$selected.">".$project->Name."</option>";
                                                endforeach;
                                            }
                                            ?>
                                        </select>
                                        <span class='error' style="float: left"></span>
                                    </td>
                                    <td>
                                        <select class="form-control" name="requser_account" id="requser_account"  title="Select Accounthead">
                                            <option value="0">Select Accounthead</option>
                                            <?php
                                            $userid=Yii::app()->user->id;
                                            $user=User::model()->findbyPk($userid);
                                            if($user['superuser']==1 || $user['superuser']==2):
                                                $useraccounts=UserAccounts::model()->findAll(array('condition'=>'user_id='.$userid.' '));
                                                foreach($useraccounts AS $account):
                                                    if($account['account_id']!=0):
                                                        if($account['account_id']==$useraccount['account_id']):
                                                            $selected="selected";
                                                        else:
                                                            $selected="";
                                                        endif;
                                                        $accountitem=AccountsItem::model()->findByPk($account['account_id']);
                                                        echo '<option value="'.$accountitem['id'].'" '.$selected.'>'.$accountitem['name'].'</option>';
                                                    endif;
                                                endforeach;
                                                echo '<option value="169">Axis Bank - 915020065069515</option>';
                                                echo '<option value="631">SIB - OD - 0025081000002165</option>';
                                                echo '<option value="722">SIB Current -0025073000002697</option>';
                                            ?>
                                            <?php else:
                                                $useraccounts=UserAccounts::model()->findAll(array('condition'=>'user_id='.$userid.' ','order'=>'account_id ASC'));
                                                foreach($useraccounts AS $account):
                                                    if($account['account_id']!=0):
                                                        if($account['account_id']==$useraccount['account_id']):
                                                            $selected="selected";
                                                        else:
                                                            $selected="";
                                                        endif;
                                                        $accountitem=AccountsItem::model()->findByPk($account['account_id']);
                                                        echo '<option value="'.$accountitem['id'].'" '.$selected.'>'.$accountitem['name'].'</option>';
                                                    endif;
                                                endforeach;
                                            ?>
                                            <?php endif;?>
                                        </select>
                                        <span class="error"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Purpose</th>
                                <th>Payment Mode</th>
                                <th>Payment Type</th>
                                <th>Amount</th>
                                <th>CGST</th>
                                <th>SGST</th>
                                <th>IGST</th>
                                <th>Net Amount</th>
                                <th>Purchase Advance</th>
                                <th><input type="button" style="display:  none;margin: auto;" class="btn btn-primary add_field_button small75" name="addmore" id="addmore" value="Add more"></th>
                            </tr>
                            </thead>
                            <tbody class="input_fields_wrap" id="fundreqitems">
                                <!--<tr id="fundreqrow" style="display: none">
                                    <td>1</td>
                                    <td><textarea class="form-control fundpurpose" name="fundpurpose[]" rows="3" cols="45" placeholder="Purpose" data-id="0" id="fundpurpose0"></textarea>
                                        <span class="error"></span>
                                    </td>
                                    <td><select class="form-control fundpaymode" name="fundpaymode[]" id="fundpaymode0" data-id="0">
                                            <option value="none">Select Payment Mode</option>
                                            <option value="1">Cash</option>
                                            <option value="2">Bank</option>
                                            <option value="3">Contra</option>
                                        </select>
                                        <span class="error"></span>
                                    </td>
                                    <td><select class="form-control fundpaytype" name="fundpaytype[]" id="fundpaytype0" data-id="0">
                                            <option value="none">Select Payment Type</option>
                                            <option value="1">Local Purchase-Cash</option>
                                            <option value="2">Local Purchase-Credit</option>
                                            <option value="3">Cash Expanse</option>
                                            <option value="4">Credit Expanse</option>
                                            <option value="5">Credit Invoice against PO</option>
                                            <option value="6">Withdrawal</option>
                                            <option value="7">Transfer</option>
                                        </select>
                                        <span class="error"></span>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control fundamount" name="fundamount[]" data-id="0" id="fundamount0" value="">
                                        <span class="error"></span>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="fundpurchaseadv[]">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr id="fundreqsaverow" style="display: none">
                                    <td colspan="4"></td>
                                    <td><input type="button" class="btn btn-primary" value="Save as draft" id="saveasdraftreq"></td>
                                    <td colspan="2"><input type="button" class="btn btn-primary" value="Save" id="savefundreq"></td>
                                </tr>-->
                            </tbody>

                            <!--<tr id="userrequest0">
                                <td class="small75">1</td>
                                <td>
                                    <textarea rows="1" class="form-control Purpose" cols="50" id="Purpose0" data-id="0" name="Request_Purpose[]" ></textarea><span class='error'></span>
                                </td>
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
                                <td></td>
                            </tr>-->
                            <!--<tr id="jobcardrow">
                                <td colspan="4"></td>
                                <td colspan="1"><button type="submit" class="btn btn-primary" name="jobcardreport" id="jobcardreport">Save</button></td>
                            </tr>-->
                        </table>
                    </div>
                </form>
            </div>
            <div id="requestlistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchrequest" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="requestsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="requesttable" style="display: table; overflow: hidden;">
                            <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);if(User::model()->isAdmin() || $user['superuser']==2): ?>
                            <thead>
                            <tr>
                                <th>SlNo</th>
                                <th>Date</th>
                                <th>User</th>
                                <th>Purpose</th>
                                <th>Payment Mode</th>
                                <th>Payment Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <?php else:?>
                            <thead>
                            <tr>
                                <th>SlNo</th>
                                <th>Date</th>
                                <th>Purpose</th>
                                <th>Payment Mode</th>
                                <th>Payment Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <?php endif;?>
                            <tbody id="requestitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="requesthistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="reqhistoryfromdate" name="fromdate" placeholder="Select Date" value="">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="reqhistorytodate" name="todate" placeholder="Select Date" value="">
                    </div>
                    <div class="col-md-3">
                        <button id="requesthistsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="requesthisttable" style="display: table; overflow: hidden;">
                            <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);if(User::model()->isAdmin() || $user['superuser']==2): ?>
                                <thead>
                                <tr>
                                    <th>SlNo</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Purpose</th>
                                    <th>Payment Mode</th>
                                    <th>Payment Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                            <?php else:?>
                                <thead>
                                <tr>
                                    <th>SlNo</th>
                                    <th>Date</th>
                                    <th>Purpose</th>
                                    <th>Payment Mode</th>
                                    <th>Payment Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                            <?php endif;?>
                            <tbody id="requesthistitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>