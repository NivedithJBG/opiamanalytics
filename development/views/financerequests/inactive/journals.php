<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/journal.js" type="text/javascript"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/script.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).on('focus','#datepicker',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });
</script>
<script type="text/javascript">
    $(function(){
        $('#canceljournal').click(function(){
            window.location = '<?php echo Yii::app()->createUrl('FinanceRequests/index');?>'
        });
    });

</script>
<script type="text/javascript">
    $(document).ready(function() {
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var x = 1; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            var type=$(this).attr('data-id');
            if (type=='debit'){
                $("#creditaddmore").attr('disabled', 'disabled');
                $("#creditNarration0").attr('disabled', 'disabled');
                if(x < max_fields){ //max input box allowed
                    //text box increment
                    $('#adddebitrow').after('<tr style="background-color: #ffffff;">' +
                        '<th><span class="headings">Debit Account</span></th>' +
                        '<td><select class="form-control debitaccount" id="debitaccount'+x+'" name="debitaccount[]" data-id="'+x+'">' +
                        '<option value="0">Select Account</option>' +
                        '<?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                    foreach($acnts AS $accounts):
                        echo "<option value=".$accounts['id']." >".$accounts->name."</option>";
                    endforeach;?></select><span class="error"></span></td>' +
                        '<th><span class="headings">Narration</span></th>' +
                        '<td colspan="3"><textarea rows="3" class="form-control Narration" cols="50" id="debitNarration'+x+'" data-id="'+x+'" name="Journal_Narration[]" autocomplete="off"></textarea><span class="error"></span></td>' +
                        '<th><span class="headings">Debit Amount</span></th>' +
                        '<td><input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount'+x+'" data-id="'+x+'"><span class="error"></span></td>' +
                        '<td><a href="#" class="remove_field">Remove</a></td></tr>');
                    x++;
                }
            }
            else {
                $("#debitaddmore").attr('disabled', 'disabled');
                $("#debitNarration0").attr('disabled', 'disabled');
                if(x < max_fields){ //max input box allowed
                    //text box increment
                    $('#addcreditrow').after('<tr style="background-color: #ffffff;">' +
                        '<th><span class="headings">Credit Account</span></th>' +
                        '<td><select class="form-control creditaccount" id="creditaccount'+x+'" name="creditaccount[]" data-id="'+x+'">' +
                        '<option value="0">Select Account</option>' +
                        '<?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                    foreach($acnts AS $accounts):
                        echo "<option value=".$accounts['id']." >".$accounts->name."</option>";
                    endforeach;?></select><span class="error"></span></td>' +
                        '<th><span class="headings">Narration</span></th>' +
                        '<td colspan="3"><textarea rows="3" class="form-control Narration" cols="50" id="creditNarration'+x+'" data-id="'+x+'" name="Journal_Narration[]" autocomplete="off"></textarea><span class="error"></span></td>' +
                        '<th><span class="headings">Credit Amount</span></th>' +
                        '<td><input type="text" class="form-control creditamount" placeholder="Amount" name="creditamount[]" id="creditamount'+x+'" data-id="'+x+'"><span class="error"></span></td>' +
                        '<td><a href="#" class="remove_field">Remove</a></td></tr>');
                    x++;
                }
            }

        });

        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            var debitrate=0;
            $('.debitamount').each(function(){
                debitrate=debitrate+$(this).val()*1;
            });
            //$('#amount').html(totalrate);
            $('#creditamount').val(debitrate);

            x--;
        });
    });
</script>
<h1>Journals</h1>
    <form method="POST" action="" id="journalform">
        <table class="table table-bordered" >
            <tbody class="input_fields_wrap">
            <?php
            if($cashbillid!=''):
            $cashbillmodel=Cashbills::model()->find(array('condition'=>'advance_id='.$cashbillid.' '));
            $cashadvancemodel=Cashadvance::model()->find(array('condition'=>'group_id='.$cashbillid.' '));
            endif;
            ?>
            <tr>
                <th><span class="headings">Date</span></th>
                <?php
                if($cashbillid!=''):?>
                <td colspan="2"><input type="text" class="form-control" name="Journal_Date" id="datepicker" value="<?php echo date("d-m-Y",strtotime($cashbillmodel['date']));?>"></td>
                <?php else:?>
                    <td colspan="2"><input type="text" class="form-control" name="Journal_Date" id="datepicker" value="<?php echo date("d-m-Y");?>"></td>
                <?php endif;?>
                <th><span class="headings">Project</span></th>
                <td colspan="2"><select class="form-control" name="projectid" id="projectid">
                    <option value="0">Select Project</option>
                    <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);
                    if(Yii::app()->user->isAdmin() || $user['superuser']==2): ?>
                        <?php foreach($adminprojects AS $data):
                            if($data['Project_Id']==$cashadvancemodel['project_id']):
                                $selected='selected';
                            else:
                                $selected='';
                            endif;
                            ?>
                            <option value="<?php echo $data['Project_Id'];?>" <?php echo $selected;?> ><?php echo $data['Name']; ?></option>
                            <?php endforeach;?>
                        <?php else: ?>
                        <?php foreach($userprojects AS $data):
                            if($data['projectid']==$cashadvancemodel['project_id']):
                                $selected='selected';
                            else:
                                $selected='';
                            endif;
                            ?>
                            <option value="<?php echo $data['projectid'];?>" <?php echo $selected;?> ><?php echo $data['Name']; ?></option>
                            <?php endforeach;?>
                        <?php endif;?>
                </select>
                <span class='error'></span>
                </td>
                <th><span class="headings">Place</span></th>
                <td colspan="2">
                    <select class="form-control" name="place" id="place">
                        <option value="0">Select Place</option>
                        <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);
                        if(Yii::app()->user->isAdmin() || $user['superuser']==2): ?>
                            <?php foreach($adminprojects AS $data):
                                if($data['Project_Id']==$cashadvancemodel['project_id']):
                                    $selected='selected';
                                else:
                                    $selected='';
                                endif;
                                ?>
                                <option value="<?php echo $data['Project_Id'];?>" <?php echo $selected;?> ><?php echo $data['Name']; ?></option>
                            <?php endforeach;?>
                        <?php else: ?>
                            <?php foreach($userprojects AS $data):
                                if($data['projectid']==$cashadvancemodel['project_id']):
                                    $selected='selected';
                                else:
                                    $selected='';
                                endif;
                                ?>
                                <option value="<?php echo $data['projectid'];?>" <?php echo $selected;?> ><?php echo $data['Name']; ?></option>
                            <?php endforeach;?>
                        <?php endif;?>
                        <?php /*$projects=Projects::model()->findAll(array('condition'=>'Project_Delete_Status=0'));
                        foreach($projects AS $project):
                            if($project['Project_Id']==$cashadvancemodel['project_id']):
                                $selected='selected';
                            else:
                                $selected='';
                            endif;
                            */?><!--
                            <option value="<?php /*echo $project['Project_Id']*/?>" <?php /*echo $selected;*/?> ><?php /*echo $project['Name']*/?></option>
                        --><?php /*endforeach;*/?>
                    </select>
                    <span class='error'></span>
                </td>
            </tr>
            <?php if($cashbillid!=''):?>
                <?php $cashbills=Cashbills::model()->findAll(array('condition'=>'advance_id='.$cashbillid.' '));
                $totalamount=0;
                foreach($cashbills AS $cashbill):
                    $debit=Vendors::model()->findByPk($cashbill['vendor'])->account_id;
                    $credit=$cashbill['accounthead'];
                    $totalamount=$totalamount + $cashbill['amount'];
                    ?>
                <tr id="adddebitrow" >
                    <th><span class="headings">Debit Account</span></th>
                    <td><select class="form-control debitaccount" id="debitaccount0" name="debitaccount[]" data-id="0">
                        <option value="0">Select Account</option>
                        <?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                        foreach($acnts AS $accounts):
                            if($accounts->id==$debit):
                                $selected='selected';
                            else:
                                $selected='';
                            endif;
                            echo "<option value='".$accounts->id."' id='acnts' ".$selected.">".$accounts->name."</option>";
                        endforeach;?>
                    </select><span class='error'></span></td>
                    <th><span class="headings">Narration</span></th>
                    <td colspan="3">
                        <textarea rows="3" class="form-control Narration" cols="50" id="debitNarration0" name="Journal_Narration[]" data-id="0"><?php echo $cashbill['purpose']?></textarea>
                        <span class='error'></span>
                    </td>
                    <th><span class="headings">Debit Amount</span></th>
                    <td><input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount0" data-id="0" value="<?php echo $cashbill['amount']?>"><span class='error'></span></td>
                    <td><input type="button" class="add_field_button" name="addmore" id="debitaddmore" data-id="debit" title="Add more" value="Add more"></td>
                </tr>
                <?php endforeach;?>
            <?php else:?>
                <tr id="adddebitrow" >
                    <th><span class="headings">Debit Account</span></th>
                    <td><select class="form-control debitaccount" id="debitaccount0" name="debitaccount[]" data-id="0">
                            <option value="0">Select Account</option>
                            <?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                            foreach($acnts AS $accounts):
                                echo "<option value='".$accounts->id."' id='acnts' >".$accounts->name."</option>";
                            endforeach;?>
                        </select><span class='error'></span></td>
                    <th><span class="headings">Narration</span></th>
                    <td colspan="3">
                        <textarea rows="3" class="form-control Narration" cols="50" id="debitNarration0" name="Journal_Narration[]" data-id="0"></textarea>
                        <span class='error'></span>
                    </td>
                    <th><span class="headings">Debit Amount</span></th>
                    <td><input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount0" data-id="0" ><span class='error'></span></td>
                    <td><input type="button" class="add_field_button" name="addmore" id="debitaddmore" data-id="debit" title="Add more" value="Add more"></td>
                </tr>
            <?php endif;?>
            <tr id="addcreditrow">
                <th><span class="headings">Credit Account</span></th>
                <td><select class="form-control creditaccount" id="creditaccount0" name="creditaccount[]" >
                    <option value="0">Select Account</option>
                    <?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                    foreach($acnts AS $accounts):
                        /*if($bills->party==$accounts->id):
                            $selected="selected";
                        else:
                            $selected='';
                        endif;*/
                        if($credit==$accounts->id):
                            $selected="selected";
                        else:
                            $selected='';
                        endif;
                        echo "<option value='".$accounts->id."' id='acnts' ".$selected.">".$accounts->name."</option>";
                    endforeach;?>
                </select>
                <span class='error'></span></td>
                <th><span class="headings">Narration</span></th>
                <td colspan="3">
                    <textarea rows="3" class="form-control Narration" cols="50" id="creditNarration0" name="Journal_Narration[]" data-id="0"></textarea>
                    <span class='error'></span>
                </td>
                <th><span class="headings">Credit Amount</span></th>
                <td><input type="text" class="form-control creditamount" placeholder="Amount" name="creditamount[]" id="creditamount0" data-id="0" value="<?php echo $totalamount; ?>"><span class='error'></span></td>
                <td><input type="button" class="add_field_button" name="addmore" id="creditaddmore" data-id="credit" title="Add more" value="Add more"></td>
                </th>
            </tr>
            <tr>

            </tr>
            <tr >
                <th colspan="6"></th>
                <th ><button type="submit" class="btn btn-primary" id="saveandcreate" name="saveandcreate">Save And Create New</button></th>
                <th ><button type="submit" class="btn btn-primary" id="savejournal" name="Journal_save">Save</button></th>
                <th ><button type="button" class="btn btn-primary" id="canceljournal" name="Journal_cancel">Cancel</button></th>
            </tr>
            </tbody>
        </table>
    </form>