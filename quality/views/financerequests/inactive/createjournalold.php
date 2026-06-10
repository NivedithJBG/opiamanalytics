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
<h1>Create Journal</h1>
<form method="POST" action="" id="createjournalform">
    <?php
    if($ordertype=='Workorder'):
        $projectid=$invoice['place'];
    else:
        $projectid=$invoice['project_id'];
    endif;
    ?>
    <table class="table table-bordered">
        <tbody>
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
        </tr>
        <?php
        if($ordertype=='Workorder'):
            $invoiceid=$_GET['invoiceid'];
            ///$billid=WorkorderBills::model()->find(array('condition'=>'bill_id='.$orderid.' '))->bill_id;
            $connection = CActiveRecord::getDbConnection();
            $sql="SELECT resource_acnt, COUNT(*) as count FROM workorder_items where order_id=".$orderid." AND bill_id=".$_GET['invoiceid']." AND delete_status=0 GROUP BY resource_acnt HAVING COUNT(*) > 1";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $dataProvider = $dataReader->read();
            if($dataProvider['count']==0):
                $orderres=WorkorderItems::model()->findAll(array('condition'=>'order_id='.$orderid.' AND bill_id='.$_GET['invoiceid'].' AND delete_status=0'));
            else:
                $orderres=WorkorderItems::model()->findAll(array('condition'=>'order_id='.$orderid.' AND bill_id='.$_GET['invoiceid'].' AND delete_status=0','group'=>'resource_acnt'));
            endif;
        else:
            //$invoiceid=Invoice::model()->find(array('condition'=>'order_id='.$orderid.' '))->invoice_id;
            $invoiceid=$_GET['invoiceid'];
            $connection = CActiveRecord::getDbConnection();
            $sql="SELECT resource_acnt, COUNT(*) as count FROM invoice_resources where order_id=".$invoice['order_id']." AND invoice_id=".$invoiceid." GROUP BY resource_acnt HAVING COUNT(*) > 1";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $dataProvider = $dataReader->read();
            //echo $sql;exit;
            //echo $dataProvider['count'];exit;
            if($dataProvider['count']==0):
                $orderres=InvoiceResources::model()->findAll(array('condition'=>'order_id='.$orderid.' AND invoice_id='.$invoiceid.' '));
            else:
                $orderres=InvoiceResources::model()->findAll(array('condition'=>'order_id='.$orderid.' AND invoice_id='.$invoiceid.' ','group'=>'resource_acnt'));
            endif;
        endif;
        //print_r($orderres);exit;
        $order=Orders::model()->findByPk($invoice['order_id']);
        $creditacnt=Vendors::model()->findByPk($order->vendor_id)->account_id;
        if(count($orderres)>0):
            $totalamount=0;
        foreach($orderres AS $resource):
            $resdetails=Resources::model()->findByPk($resource['resource_id']);
            $resrate=OrderedResource::model()->find(array('condition'=>'order_id='.$invoice['order_id'].' AND resource_id='.$resource['resource_id'].' '));
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
                    $invres=WorkorderItems::model()->findAll(array('condition'=>'order_id='.$orderid.' AND bill_id='.$invoiceid.' AND resource_acnt='.$resource['resource_acnt'].' '));
                    $resamnt=0;
                    foreach($invres AS $invre):
                        $orresrate=OrderedResource::model()->find(array('condition'=>'order_id='.$invre['order_id'].' AND resource_id='.$invre['resource_id'].''));
                        $resamnt=$resamnt + ($invre['resource_qty'] * $orresrate['rate']);
                    endforeach;
                    $debitamount=$resamnt;
                endif;
            else:
                if($dataProvider['count']==0):
                    if($order['order_type']==3):
                        $debitamount=$invoice['total_wages'];
                    else:
                        $debitamount=$resrate['rate'] * $resource['resource_qty'];
                    endif;
                    //echo $debitamount;exit;
                else:
                    if($order['order_type']==3):
                        $debitamount=$invoice['total_wages'];
                    else:
                        $invres=InvoiceResources::model()->findAll(array('condition'=>'order_id='.$orderid.' AND invoice_id='.$invoiceid.' AND resource_acnt='.$resource['resource_acnt'].' '));
                        $resamnt=0;
                        foreach($invres AS $invre):
                            $orresrate=OrderedResource::model()->find(array('condition'=>'order_id='.$invre['order_id'].' AND resource_id='.$invre['resource_id'].''));
                            $resamnt=$resamnt + ($invre['resource_qty'] * $orresrate['rate']);
                        endforeach;
                        $debitamount=$resamnt;
                    endif;

                endif;
            endif;
            //echo $debitamount;exit;
            //$totalamount=$totalamount + ($resrate['rate'] * $resource['resource_qty']);
            $resacntid=$resource['resource_acnt'];
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
                <textarea rows="2" class="form-control" cols="50" id="Narration" name="Journal_Narration[]"><?php echo $invoice['Specification'];?></textarea>
                <span class='error'></span>
            </td>
            <th><span class="headings">Debit Amount</span></th>
            <td><input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount0" value="<?php echo $debitamount;?>"><span class='error'></span></td>
        </tr>
        <?php endforeach;endif;?>
        <?php
        $gstdet=OrderedResource::model()->find(array('condition'=>'order_id='.$orderid.' '));
        if($gstdet['cgst']!=0):
            //$debitamount=$resrate['rate'] * $resource['resource_qty'];
            $gstamount=($debitamount * $gstdet['cgst']) / 100;
            $totalamount=$totalamount + ($debitamount) + ($gstamount * 2);
            $gstrow='<tr>
                        <th><span class="headings">Debit Account</span></th>
                        <td><select class="form-control debitaccount" id="debitaccount0" name="debitaccount[]" data-id="0">
                        <option value="0">Select Account</option>
                        '.$cgstoptions.'
                        </select><span class="error"></span>
                        </td>
                        <th><span class="headings">Narration</span></th>
                        <td colspan="2"><textarea rows="2" class="form-control" cols="50" id="Narration" name="Journal_Narration[]">Being Input CGST @ '.$gstdet['cgst'].' %</textarea>
                        <span class="error"></span>
                        </td>
                        <th><span class="headings">Debit Amount</span></th>
                        <td><input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount0" value="'.$gstamount.'"><span class="error"></span></td>
                        </tr>
                        <tr>
                        <th><span class="headings">Debit Account</span></th>
                        <td><select class="form-control debitaccount" id="debitaccount0" name="debitaccount[]" data-id="0">
                        <option value="0">Select Account</option>
                        '.$sgstoptions.'
                        </select><span class="error"></span>
                        </td>
                        <th><span class="headings">Narration</span></th>
                        <td colspan="2"><textarea rows="2" class="form-control" cols="50" id="Narration" name="Journal_Narration[]">Being Input SGST @ '.$gstdet['sgst'].' %</textarea>
                        <span class="error"></span>
                        </td>
                        <th><span class="headings">Debit Amount</span></th>
                        <td><input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount0" value="'.$gstamount.'"><span class="error"></span></td>
                        </tr>';
        elseif($gstdet['igst']!=0):
            //$debitamount=$resrate['rate'] * $resource['resource_qty'];
            $gstamount=($debitamount * $gstdet['igst']) / 100;
            $totalamount=$totalamount + ($debitamount) + $gstamount;
            $gstrow='<tr>
                        <th><span class="headings">Debit Account</span></th>
                        <td><select class="form-control debitaccount" id="debitaccount0" name="debitaccount[]" data-id="0">
                        <option value="0">Select Account</option>
                        '.$igstoptions.'
                        </select><span class="error"></span>
                        </td>
                        <th><span class="headings">Narration</span></th>
                        <td colspan="2"><textarea rows="2" class="form-control" cols="50" id="Narration" name="Journal_Narration[]">Being Input IGST @ '.$gstdet['igst'].' %</textarea>
                        <span class="error"></span>
                        </td>
                        <th><span class="headings">Debit Amount</span></th>
                        <td><input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount0" value="'.$gstamount.'"><span class="error"></span></td>
                        </tr>';
        else:
            $totalamount=$totalamount + $debitamount;
        endif;
        ?>
        <?php echo $gstrow;?>
        <?php if($order['transportation']!=0):?>
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
            <td><input type="text" class="form-control debitamount" placeholder="Amount" name="trpamount" id="trpamount0" value="<?php echo $order['transportation'];?>"><span class='error'></span></td>
        </tr>
        <?php endif;?>
        <tr>
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
                <input type="text" class="form-control creditamount" placeholder="Amount" name="creditamount" id="creditamount0" value="<?php echo $totalamount;?>"><span class='error'></span>
                <input type="hidden" name="grossamount" value="<?php echo $debitamount;?>">
            </td>
            <th colspan="3"></th>
        </tr>
        <tr >
            <th colspan="5"><input type="hidden" name="ordertype" value="<?php echo $ordertype;?>"></th>
            <th ><button type="button" class="btn btn-primary" id="canceljournal" name="Journal_cancel">Cancel</button></th>
            <th ><button type="submit" class="btn btn-primary" id="createjournal" name="Journal_save">Save</button></th>
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
            if($('#Narration').val()=='')

            {

                $("#Narration").next("span").html('Enter Narration').show('slow');

                error=1;

            }
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