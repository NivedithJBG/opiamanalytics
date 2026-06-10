<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/projectfunctions.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function(){
        $('#cancel').click(function(){
            window.location = '<?php echo Yii::app()->createUrl('projects1/index');?>'
        });
    });

</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#startdate1').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var x = 1; //initlal text box count
        $(add_button).click(function(e){
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                //text box increment
                $('#bankaccount').after('<tr style="background-color: #ffffff;">' +
                    '<th><span class="headings">Bank Account</span></th>' +
                    '<td><select class="form-control bankaccount" name="bankaccount[]">' +
                    '<option value="0">Select Account</option>' +
                    '<?php $acnts=AccountsItem::model()->findAll(array('condition'=>'account_type=2','order'=>'name ASC'));
                    foreach($acnts AS $accounts):
                    echo "<option value=".$accounts->id." >".$accounts->name."</option>";
                    endforeach;?></select></td>' +
                    '<td><a href="#" class="btn btn-primary remove_field">Remove</a></td></tr>');
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
<h1>Update Project</h1>
    <form method="POST" action="" id="updateform">
        <table class="table table-bordered" align="center" style="width: 60%;">
            <tbody class="input_fields_wrap">
            <tr>
                <th><span class="headings">Project</span></th>
                <td colspan="2">
                    <input type="hidden" value="<?php echo $project['Project_Id']?>" id="projectid" name="projectid">
                    <input type="text" class="form-control" id="projectname" name="projectname" value="<?php echo $project['Name']?>">
                    <span class='error'></span>
                </td>
            </tr>

            <tr style="background-color: white">
                <th>
                    <span class="headings">Cash Account</span>
                </th>
                <td colspan="2">
                    <input type="hidden" value="<?php echo $cashaccount['id']?>" name="cashaccountid" id="cashaccountid">
                    <select class="form-control cashaccountname" id="cash" name="cashaccountname">
                        <option value="0">Select Account</option>
                        <?php $acnts=AccountsItem::model()->findAll(array('condition'=>'account_type=1','order'=>'name ASC'));
                        foreach($acnts AS $accounts):
                            if($cashaccount['id']==$accounts->id):$selected='selected';else:$selected='';endif;
                            echo "<option value='".$accounts->id."' id='acnts' $selected>".$accounts->name."</option>";
                        endforeach;?>
                    </select>
<!--                    <input type="text" class="form-control" id="cashaccountname" name="cashaccountname" value="--><?php //echo $cashaccount['name']?><!--">-->
                    <span class='error'></span>
                </td>
            </tr>
            <?php if($project['Project_Id']==12):if(count($bankaccount)>0) :
                foreach($bankaccount AS $key=>$bank):if($key==0):?>

                <tr id="bankaccount" style="background-color: white">
                    <th>
                        <span class="headings">Bank Account</span>
                    </th>
                    <td>
                        <input type="hidden" value="<?php echo $bank['id']?>" name="bankaccountid" >
                        <select class="form-control bankaccountname" id="bank" name="bankaccountname">
                            <option value="0">Select Account</option>
                            <?php $acnts=AccountsItem::model()->findAll(array('condition'=>'account_type=2','order'=>'name ASC'));
                            foreach($acnts AS $accounts):
                                if($bank['id']==$accounts->id):$selected='selected';else:$selected='';endif;
                                echo "<option value='".$accounts->id."' id='acnts' $selected>".$accounts->name."</option>";
                            endforeach;?>
                        </select>
<!--                        <input type="text" class="form-control"  name="bankaccountname" value="--><?php //echo $bank['name']?><!--">-->
                        <span class='error'></span>
                    </td>
                    <td>
                        <input type="button" class="add_field_button" name="addmore" id="addmore" title="Add more" value="Add more">
                    </td>
                </tr>
                <?php else:?>
                <tr id="bankaccount<?php echo $bank['id']?>" style="background-color: white">
                    <th>
                        <span class="headings">Bank Account</span>
                    </th>
                    <td >
                        <input type="hidden" value="<?php echo $bank['id']?>" name="bankaccountid" id="accountid">
                        <select class="form-control bankaccountname" id="bank" name="bankaccountname">
                            <option value="0">Select Account</option>
                            <?php $acnts=AccountsItem::model()->findAll(array('condition'=>'account_type=2','order'=>'name ASC'));
                            foreach($acnts AS $accounts):
                                if($bank['id']==$accounts->id):$selected='selected';else:$selected='';endif;
                                echo "<option value='".$accounts->id."' id='acnts' $selected>".$accounts->name."</option>";
                            endforeach;?>
                        </select>
<!--                        <input type="text" class="form-control"  name="bankaccountname" value="--><?php //echo $bank['name']?><!--">-->
                        <span class='error'></span>
                    </td>
                    <td><button type="button" class="btn btn-primary remove_account" id="remove_account<?php echo $bank['id']?>" value="<?php echo $bank['id']?>" title="Delete Account">Remove</button></td
                </tr>
                <?php endif;?>
                <?php endforeach;?>
                <?php else:?>
                <tr id="bankaccount" style="background-color: white">
                    <th>
                        <span class="headings">Bank Account</span>
                    </th>
                    <td>
                        <select class="form-control bankaccountname" id="bank" name="bankaccountname">
                            <option value="0">Select Account</option>
                            <?php $acnts=AccountsItem::model()->findAll(array('condition'=>'account_type=2','order'=>'name ASC'));
                            foreach($acnts AS $accounts):
                                if($bank['id']==$accounts->id):$selected='selected';else:$selected='';endif;
                                echo "<option value='".$accounts->id."' id='acnts' $selected>".$accounts->name."</option>";
                            endforeach;?>
                        </select>
<!--                        <input type="text" class="form-control"  name="bankaccountname" >-->
                        <span class='error'></span>
                    </td>
                    <td>
                        <input type="button" class="add_field_button" name="addmore" title="Add more" value="Add more">
                    </td>
                </tr>
            <?php endif;?>
            <?php else:if(count($bankaccount)>0) :
                foreach($bankaccount AS $key=>$bank):?>
                <tr id="bankaccount" style="background-color: white">
                    <th>
                        <span class="headings">Bank Account</span>
                    </th>
                    <td colspan="2">
                        <input type="hidden" value="<?php echo $bank['id']?>" name="bankaccountid" id="bankaccountid">
                        <select class="form-control bankaccountname" id="bank" name="bankaccountname">
                            <option value="0">Select Account</option>
                            <?php $acnts=AccountsItem::model()->findAll(array('condition'=>'account_type=2','order'=>'name ASC'));
                            foreach($acnts AS $accounts):
                                if($bank['id']==$accounts->id):$selected='selected';else:$selected='';endif;
                                echo "<option value='".$accounts->id."' id='acnts' $selected>".$accounts->name."</option>";
                            endforeach;?>
                        </select>
<!--                        <input type="text" class="form-control" id="bankaccountname" name="bankaccountname" value="--><?php //echo $bank['name']?><!--">-->
                        <span class='error'></span>
                    </td>
                </tr>
                <?php endforeach;?>
            <?php else:?>
            <tr id="bankaccount" style="background-color: white">
                <th>
                    <span class="headings">Bank Account</span>
                </th>
                <td colspan="2">
                    <select class="form-control bankaccountname" id="bank" name="bankaccountname">
                        <option value="0">Select Account</option>
                        <?php $acnts=AccountsItem::model()->findAll(array('condition'=>'account_type=2','order'=>'name ASC'));
                        foreach($acnts AS $accounts):
                            if($bank['id']==$accounts->id):$selected='selected';else:$selected='';endif;
                            echo "<option value='".$accounts->id."' id='acnts' $selected>".$accounts->name."</option>";
                        endforeach;?>
                    </select>
<!--                    <input type="text" class="form-control" name="bankaccountname" >-->
                    <span class='error'></span>
                </td>
            </tr>
            <?php endif;?>
            <?php endif;?>

            <tr id="startdate" style="background-color: white">
                <th>
                    <span class="headings">Start Date</span>
                </th>
                <td colspan="2">
                    <input type="text" class="form-control datepicker startdate" name="startdate" id="startdate1" value="<?php echo date("d-m-Y",strtotime($project['start_date']));?>">
                    <span class='error'></span>
                </td>
            </tr>

            <tr id="duration" style="background-color: white">
                <th>
                    <span class="headings">Duration</span>
                </th>
                <td colspan="2">
                    <input type="text" class="form-control duration" name="duration" id="duration" value="<?php echo $project['duration']?>">
                    <span class='error'></span>
                </td>
            </tr>

            <tr>
                <td colspan="2"><button type="submit" class="btn btn-primary" id="editproject" >Save</button></td>
                <td><button type="button" class="btn btn-primary" id="cancel" >Cancel</button></td>
            </tr>
            </tbody>
        </table>
        </form>