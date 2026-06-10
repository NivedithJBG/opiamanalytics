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
            if(x < max_fields){ //max input box allowed
                //text box increment
                $('#creditjournalrow').before('<tr style="background-color: #ffffff;">' +
                    '<th><span class="headings">Debit Account</span></th>' +
                    '<td><select class="form-control debitaccount" id="debitaccount'+x+'" name="debitaccount[]" data-id="'+x+'">' +
                    '<option value="0">Select Account</option>' +
                    '<?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                    foreach($acnts AS $accounts):
                        echo "<option value=".$accounts['id']." >".$accounts->name."</option>";
                    endforeach;?></select><span class="error"></span></td>' +
                    '<th><span class="headings">Narration</span></th>' +
                    '<td colspan="2"><textarea rows="3" class="form-control Narration" cols="50" id="Narration'+x+'" data-id="'+x+'" name="Journal_Narration[]" autocomplete="off"></textarea><span class="error"></span></td>' +
                    '<th><span class="headings">Debit Amount</span></th>' +
                    '<td><input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount'+x+'" data-id="'+x+'"><span class="error"></span></td>' +
                    '<td><a href="#" class="remove_field">Remove</a></td></tr>');
                x++;
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
            $('#creditamount0').val(debitrate);

            x--;
        });
        $(document).on('blur','.debitamount',function(){
            var debitrate=0;
            $('.debitamount').each(function(){
                debitrate=debitrate+$(this).val()*1;
            });
            //$('#amount').html(totalrate);
            $('#creditamount0').val(debitrate);
        })
    });
</script>
<h1>Create Journal</h1>
<form method="POST" action="" id="createjournalform">
    <?php
    //echo $ordertype;exit;
    if($ordertype=='Workorder'):
        $projectid=$invoice[0]['place'];
    else:
        $projectid=$invoice[0]['project_id'];
    endif;
    ?>
    <table class="table table-bordered">
        <tbody class="input_fields_wrap">
        <tr>
            <th><span class="headings">Date</span></th>
            <td colspan="1"><input type="text" class="form-control" name="Journal_Date" id="datepicker" value="<?php echo date("d-m-Y");?>"></td>
            <th><span class="headings">Project</span></th>
            <td colspan="2"><select class="form-control" name="projectid" id="projectid">
                    <option value="0">Select Project</option>
                    <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);
                    if(Yii::app()->user->isAdmin() || $user['superuser']==2): ?>
                        <?php foreach($adminprojects AS $data):
                            if($data['Project_Id']==$projectid):$selected="selected";else:$selected="";endif;
                            ?>
                            <option value="<?php echo $data['Project_Id'];?>" <?php echo $selected;?> ><?php echo $data['Name']; ?></option>
                        <?php endforeach;?>
                    <?php else: ?>
                        <?php foreach($userprojects AS $data):
                            if($data->Project_Id==$projectid):$selected="selected";else:$selected="";endif;
                            ?>
                            <option value="<?php echo $data['projectid'];?>" <?php echo $selected;?> ><?php echo $data['Name']; ?></option>
                        <?php endforeach;?>
                    <?php endif;?>
                </select>
                <span class='error'></span>
            </td>
            <th><span class="headings">Place</span></th>
            <td colspan="1">
                <select class="form-control" name="place" id="place">
                    <option value="0">Select Place</option>
                    <?php $projects=Projects::model()->findAll(array('condition'=>'Project_Delete_Status=0'));
                    foreach($projects AS $project):?>
                        <option value="<?php echo $project['Project_Id']?>"><?php echo $project['Name']?></option>
                    <?php endforeach;?>
                </select>
                <span class='error'></span>
            </td>
            <td></td>
        </tr>
        <?php
        if($ordertype=='Workorder'):
            $invoiceid=$_POST['crinvoices'];
            ///$billid=WorkorderBills::model()->find(array('condition'=>'bill_id='.$orderid.' '))->bill_id;
            $connection = CActiveRecord::getDbConnection();
            $sql="SELECT resource_acnt, COUNT(*) as count FROM workorder_items where bill_id IN (".$invoiceid.") AND delete_status=0 GROUP BY resource_acnt HAVING COUNT(*) > 1";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $dataProvider = $dataReader->read();
            if($dataProvider['count']==0):
                $orderres=WorkorderItems::model()->findAll(array('condition'=>'bill_id IN ('.$invoiceid.') AND delete_status=0'));
            else:
                $orderres=WorkorderItems::model()->findAll(array('condition'=>'bill_id IN ('.$invoiceid.') AND delete_status=0','group'=>'resource_acnt'));
            endif;
        else:
            //$invoiceid=Invoice::model()->find(array('condition'=>'order_id='.$orderid.' '))->invoice_id;
            $invoiceid=$_POST['crinvoices'];
            $connection = CActiveRecord::getDbConnection();
            $sql="SELECT resource_acnt, COUNT(*) as count FROM invoice_resources where invoice_id IN (".$invoiceid.") GROUP BY resource_acnt HAVING COUNT(*) > 1";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $dataProvider = $dataReader->read();
            //echo $sql;exit;
            //echo $dataProvider['count'];exit;
            if($dataProvider['count']==0):
                $orderres=InvoiceResources::model()->findAll(array('condition'=>'invoice_id IN('.$invoiceid.') '));
            else:
                $orderres=InvoiceResources::model()->findAll(array('condition'=>'invoice_id IN('.$invoiceid.') ','group'=>'resource_acnt'));
            endif;
        endif;
        //print_r($orderres);exit;
        $crvendorval=explode(',',$_POST['crvendor']);
        //$order=Orders::model()->findByPk($invoice['order_id']);
        $creditacnt=Vendors::model()->findByPk($crvendorval[0])->account_id;
        if(count($orderres)>0):
            //$totalamount=0;
            $debittotal=0;
        foreach($orderres AS $key=>$resource):
            $resdetails=Resources::model()->findByPk($resource['resource_id']);
            $resrate=OrderedResource::model()->find(array('condition'=>'order_id IN ('.$_POST['crorders'].') AND resource_id='.$resource['resource_id'].' '));
            if($ordertype=='Workorder'):
                /*if($invoice['amount_pay']!=0):
                    $debitamount=$invoice['amount_pay'];
                else:
                    $debitamount=$resrate['rate'] * $resource['resource_qty'];
                endif;*/
                if($dataProvider['count']==0):
                    $debitamount=$resrate['rate'] * $resource['resource_qty'];
                //echo $debitamount;exit;
                else:
                    $invres=WorkorderItems::model()->findAll(array('condition'=>'bill_id IN('.$invoiceid.') AND resource_acnt='.$resource['resource_acnt'].' '));
                    $resamnt=0;
                    foreach($invres AS $invre):
                        $orresrate=OrderedResource::model()->find(array('condition'=>'order_id='.$invre['order_id'].' AND resource_id='.$invre['resource_id'].''));
                        $resamnt=$resamnt + ($invre['resource_qty'] * $orresrate['rate']);
                    endforeach;
                    $debitamount=$resamnt;
                endif;
                $narration=WorkorderBills::model()->findByPk($resource['bill_id'])->Specification;
            else:
                if($dataProvider['count']==0):
                    if($crordertype==3):
                        $totwages=0;
                        foreach($invoice AS $value):
                            $totwages+=$value['total_wages'];
                        endforeach;
                        $debitamount=$totwages;
                    else:
                        $debitamount=$resrate['rate'] * $resource['resource_qty'];
                    endif;
                    //echo $debitamount;exit;
                else:
                    if($crordertype==3):
                        $totwages=0;
                        foreach($invoice AS $value):
                            $totwages+=$value['total_wages'];
                        endforeach;
                        $debitamount=$totwages;
                    else:
                        $invres=InvoiceResources::model()->findAll(array('condition'=>'invoice_id IN('.$invoiceid.') AND resource_acnt='.$resource['resource_acnt'].' '));
                        $resamnt=0;
                        foreach($invres AS $invre):
                            $orresrate=OrderedResource::model()->find(array('condition'=>'order_id='.$invre['order_id'].' AND resource_id='.$invre['resource_id'].''));
                            $resamnt=$resamnt + ($invre['resource_qty'] * $orresrate['rate']);
                        endforeach;
                        $debitamount=$resamnt;
                    endif;
                endif;
                $narration=Invoice::model()->findByPk($resource['invoice_id'])->Specification;
            endif;
            //echo $debitamount;exit;
            //$totalamount=$totalamount + ($resrate['rate'] * $resource['resource_qty']);
            $debittotal+=$debitamount;
            //$resacntid=$resource['resource_acnt'];
            $res_details=Resources::model()->findByPk($resource['resource_id']);
            $resacntid=$res_details['Resource_Acc_Id'];
            $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
            $cgstoptions='';
            foreach($acnts AS $accounts):
                if($accounts->id==498):$selected="selected";else:$selected="";endif;
                $cgstoptions.="<option value='".$accounts->id."' id='acnts' ".$selected.">".$accounts->name."</option>";
            endforeach;
            $sgstoptions='';
            foreach($acnts AS $accounts):
                if($accounts->id==499):$selected="selected";else:$selected="";endif;
                $sgstoptions.="<option value='".$accounts->id."' id='acnts' ".$selected.">".$accounts->name."</option>";
            endforeach;
            $igstoptions='';
            foreach($acnts AS $accounts):
                if($accounts->id==506):$selected="selected";else:$selected="";endif;
                $igstoptions.="<option value='".$accounts->id."' id='acnts' ".$selected.">".$accounts->name."</option>";
            endforeach;
        ?>
        <tr>
            <th><span class="headings">Debit Account</span></th>
            <td><select class="form-control debitaccount" id="debitaccount0" name="debitaccount[]" data-id="0">
                    <option value="0">Select Account</option>
                    <?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                    foreach($acnts AS $accounts):
                        if($accounts->id==$resacntid):$selected="selected";else:$selected="";endif;
                        echo "<option value='".$accounts->id."' id='acnts' ".$selected.">".$accounts->name."</option>";
                    endforeach;?>
                </select><span class='error'></span></td>
            <th><span class="headings">Narration</span></th>
            <td colspan="2">
                <textarea rows="2" class="form-control" cols="50" id="Narration" name="Journal_Narration[]"><?php echo $narration;?></textarea>
                <span class='error'></span>
            </td>
            <th><span class="headings">Debit Amount</span></th>
            <td><input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount0" value="<?php echo number_format((float)$debitamount, 2, '.', '');?>"><span class='error'></span></td>
            <?php
            if($key==0):
                echo '<td><input type="button" class="add_field_button btn btn-primary" name="addmore" id="debitaddmore" data-id="debit" title="Add more" value="Add more"></td>';
            else:
                echo '<td></td>';
            endif;?>
        </tr>
        <?php endforeach;endif;?>
        <?php
        //echo $debittotal;exit;
        //echo $_POST['crorders'];exit;
        $gstdet=OrderedResource::model()->find(array('condition'=>'order_id IN ('.$_POST['crorders'].') '));
        if($gstdet['cgst']!=0):
            $criteria = new CDbCriteria;
            $criteria->select='max(cgst) as cgst,max(sgst) as sgst';
            $criteria->condition='order_id IN ('.$_POST['crorders'].')';
            $product = OrderedResource::model()->find($criteria);
            //print_r($product['cgst']);exit;
            //$debitamount=$resrate['rate'] * $resource['resource_qty'];
            $gstamount=($debittotal * $product['cgst']) / 100;
            $totalamount=($debittotal) + ($gstamount * 2);
            $gstrow='<tr>
                        <th><span class="headings">Debit Account</span></th>
                        <td><select class="form-control debitaccount" id="debitaccount0" name="debitaccount[]" data-id="0">
                        <option value="0">Select Account</option>
                        '.$cgstoptions.'
                        </select><span class="error"></span>
                        </td>
                        <th><span class="headings">Narration</span></th>
                        <td colspan="2"><textarea rows="2" class="form-control" cols="50" id="Narration" name="Journal_Narration[]">Being Input CGST @ '.$product['cgst'].' %</textarea>
                        <span class="error"></span>
                        </td>
                        <th><span class="headings">Debit Amount</span></th>
                        <td><input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount0" value="'.number_format((float)$gstamount, 2, '.', '').'"><span class="error"></span></td>
                        <td></td>
                        </tr>
                        <tr>
                        <th><span class="headings">Debit Account</span></th>
                        <td><select class="form-control debitaccount" id="debitaccount0" name="debitaccount[]" data-id="0">
                        <option value="0">Select Account</option>
                        '.$sgstoptions.'
                        </select><span class="error"></span>
                        </td>
                        <th><span class="headings">Narration</span></th>
                        <td colspan="2"><textarea rows="2" class="form-control" cols="50" id="Narration" name="Journal_Narration[]">Being Input SGST @ '.$product['sgst'].' %</textarea>
                        <span class="error"></span>
                        </td>
                        <th><span class="headings">Debit Amount</span></th>
                        <td><input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount0" value="'.number_format((float)$gstamount, 2, '.', '').'"><span class="error"></span></td>
                        <td></td>
                        </tr>';
        elseif($gstdet['igst']!=0):
            //$debitamount=$resrate['rate'] * $resource['resource_qty'];
            $criteria = new CDbCriteria;
            $criteria->select='max(igst) as igst';
            $criteria->condition='order_id IN ('.$_POST['crorders'].')';
            $product = OrderedResource::model()->find($criteria);
            $gstamount=($debittotal * $product['igst']) / 100;
            $totalamount=($debittotal) + $gstamount;
            $gstrow='<tr>
                        <th><span class="headings">Debit Account</span></th>
                        <td><select class="form-control debitaccount" id="debitaccount0" name="debitaccount[]" data-id="0">
                        <option value="0">Select Account</option>
                        '.$igstoptions.'
                        </select><span class="error"></span>
                        </td>
                        <th><span class="headings">Narration</span></th>
                        <td colspan="2"><textarea rows="2" class="form-control" cols="50" id="Narration" name="Journal_Narration[]">Being Input IGST @ '.$product['igst'].' %</textarea>
                        <span class="error"></span>
                        </td>
                        <th><span class="headings">Debit Amount</span></th>
                        <td><input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount0" value="'.number_format((float)$gstamount, 2, '.', '').'"><span class="error"></span></td>
                        <td></td>
                        </tr>';
        else:
            $totalamount=$debittotal;
        endif;
        ?>
        <?php echo $gstrow;?>
        <?php
        $ordervalues=Orders::model()->findAll(array('condition'=>'order_id IN ('.$_POST['crorders'].')'));
        $tottp=0;
        foreach($ordervalues AS $ordervalue):
            $tottp+=$ordervalue['transportation'];
        endforeach;
        ?>
        <?php if($tottp!=0):?>
        <tr>
            <th><span class="headings">Debit Account</span></th>
            <td><select class="form-control trpaccount" id="trpaccount0" name="trpaccount" data-id="0">
                    <option value="0">Select Account</option>
                    <?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                    $trpacnt=AccountsItem::model()->find(array('condition'=>'name LIKE "Transportation Charges" '))->id;
                    foreach($acnts AS $accounts):
                        if($accounts->id==$trpacnt):$selected="selected";else:$selected="";endif;
                        echo "<option value='".$accounts->id."' id='acnts' ".$selected.">".$accounts->name."</option>";
                    endforeach;?>
                </select><span class='error'></span></td>
            <th><span class="headings">Narration</span></th>
            <td colspan="2">
                <textarea rows="2" class="form-control" cols="50" id="Narration" name="trp_Narration">Transportation charges</textarea>
                <span class='error'></span>
            </td>
            <th><span class="headings">Debit Amount</span></th>
            <td><input type="text" class="form-control debitamount" placeholder="Amount" name="trpamount" id="trpamount0" value="<?php echo $tottp;?>"><span class='error'></span></td>
            <td></td>
        </tr>
        <?php endif;?>
        <tr id="creditjournalrow">
            <th><span class="headings">Credit Account</span></th>
            <td colspan="1"><select class="form-control creditaccount" id="creditaccount" name="creditaccount">
                    <option value="0">Select Account</option>
                    <?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                    foreach($acnts AS $accounts):
                        if($creditacnt==$accounts->id):
                            $selected="selected";
                        else:
                            $selected='';
                        endif;
                        echo "<option value='".$accounts->id."' id='acnts' ".$selected.">".$accounts->name."</option>";
                    endforeach;?>
                </select>
                <span class='error'></span></td>
            <th><span class="headings">Credit Amount</span></th>
            <td>
                <input type="text" class="form-control creditamount" placeholder="Amount" name="creditamount" id="creditamount0" value="<?php echo number_format((float)$totalamount, 2, '.', '');?>"><span class='error'></span>
                <input type="hidden" name="grossamount" value="<?php echo $debittotal;?>">
            </td>
            <th colspan="4"></th>
        </tr>
        <tr >
            <th colspan="5">
                <input type="hidden" name="ordertype" value="<?php echo $ordertype;?>">
                <input type="hidden" name="crinvoices" value="<?php echo $crinvoices;?>">
                <input type="hidden" name="crorders" value="<?php echo $_POST['crorders'];?>">
            </th>
            <th><button type="button" class="btn btn-primary" id="canceljournal" name="Journal_cancel">Cancel</button></th>
            <th colspan="2"><button type="submit" class="btn btn-primary" id="createjournal" name="Journal_save">Save</button></th>
        </tr>
        </tbody>
    </table>
</form>
<script type="text/javascript">
    $(function(){
        $('#createjournal').click(function(){
            var error=0;

            $('.error').hide();

            if($('#place').val()=='0')

            {

                $("#place").next("span").html('Select Place').show('slow');

                error=1;

            }
            if($('#creditaccount').val()=='0')

            {

                $("#creditaccount").next("span").html('Select Credit Account').show('slow');

                error=1;

            }
            $('.debitaccount').each(function(){
                var id=$(this).attr('data-id');
                if($('#debitaccount'+id).val()=='0')

                {

                    $("#debitaccount"+id).next("span").html('Select Debit Account').show('slow');

                    error=1;

                }
            });
            $('.debitamount').each(function(){
                var id=$(this).attr('data-id');
                if($('#debitamount'+id).val()=='')

                {

                    $("#debitamount"+id).next("span").html('Enter Debit Amount').show('slow');

                    error=1;

                }
            });
            $('.Narration').each(function(){
                var id=$(this).attr('data-id');
                if($('#Narration'+id).val()=='')

                {

                    $("#Narration"+id).next("span").html('Enter Narration').show('slow');

                    error=1;

                }
            });
            /*if($('#Narration').val()=='')

            {

                $("#Narration").next("span").html('Enter Narration').show('slow');

                error=1;

            }*/
            if (error==1)
            {
                return false;
            }
            else
            {
                return true;
            }
        });
    });
</script>