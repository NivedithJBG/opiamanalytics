
<?php
use app\models\AccountTypes;   
use app\models\Accountsmaster; 
use app\models\AccountsSub;
use yii\helpers\ArrayHelper; 
use yii\helpers\Html;  
?>

<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/accounts.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css" rel="stylesheet" />-->
<div class="container procu-accordion"> 
    <div class="row">
        <div class="col-md-12">
            
            <div class="panel-group acco-one-active" >
<button type="button" value="Back" name="goback" title="back" class="btn btn-primary" style="float: right;width: 100px;margin-bottom: 12px;" onclick="goBack()">Back</button>
<script>
    function goBack() {
        window.history.back()
    }
    /*$(function() {
        $('.resourceunit').selectpicker();
    });*/
</script>
 
<h4><center>Update Account Head</center></h4>  
<form method="POST" action="" id="accountheadform">
     <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
    <table class="table table-bordered">
        <tbody>
        <tr>
            <th style="width: 25%"><span class="headings">Accounts Name</span></th>
            <td style="width: 75%"><input class="form-control" style="width: 50%;" type="text" id="accountsname" name="accountsname"
                       placeholder="Accounts Name" value="<?php echo $model->name?>"><span class="error" style="display: none;"></span></td>
        </tr>
        <tr style="background-color: white;">
            <th><span class="headings">Account type</span></th>
            <td><select id="accounttype" name="accounttype" class="form-control" style="width: 50%;">
                    <option value="0">Select Account type</option>
                    <?php
                    $acnttypes=AccountTypes::find()->all();
                    foreach($acnttypes AS $acnttype):
                        if($model->account_type == $acnttype->type_id):
                            $selected='selected';
                        else:
                            $selected='';
                        endif;
                        echo "<option value='".$acnttype->type_id."' $selected id='acnttype'>".$acnttype->name."</option>";
                    endforeach;
                    ?>

                </select><span class="error" style="display: none;"></span></td>
        </tr>

        <tr style="background-color: white">
            <th><span class="headings">TDS(%)</span></th>
            <td><input class="form-control" style="width: 16%;" type="text" id="accounttds" name="accounttds"
                       placeholder="TDS(%)" value="<?php echo $model->tds ?>"><span class="error"
                                                                                    style="display: none;"></span></td>
        </tr>
        <tr style="background-color: white">
            <th><span class="headings">CGST/SGST(%)</span></th>
            <td><input class="form-control" style="width: 16%;" type="text" id="accountservtax" name="accountservtax"
                       placeholder="Service Tax(%)" value="<?php echo $model->servicetax ?>"><span class="error"
                                                                                                   sts>
        </tr>
        <tr style="background-color: white">
            <th><span class="headings">IGST(%)</span></th>
            <td><input class="form-control" style="width: 16%;" type="text" id="accountigst" name="accountigst"
                       placeholder="IGST(%)" value="<?php echo $model->igst ?>"><span class="error"
                                                                                                   styn>
            </td>
        </tr>
        <tr style="background-color: white">
            <th><span class="headings">Schedule</span></th>
            <td><input type="checkbox" style="visibility: visible;" <?php echo ($model['schedule']=='3'?'checked="checked"':'');?> value="3" id="schedule" name="schedule" style="visibility: visible;"></td>
        </tr>
        <tr style="background-color: white">
            <td colspan="2">
                <table class="table table-bordered"  >
                    <tbody>
                        <tr>
                            <th><span class="headings">Account Groups</span></th>
                            <th><span class="headings">Account Sub-Groups</span></th>
                           <!--  <th><span class="headings">Balancesheet Items</span></th> -->
                            <!--<th><span class="headings">Account Schedule</span></th>-->
                        </tr>
                        <?php 
                            $groups=Accountsmaster::find()->all();
                            foreach($groups AS $group):
                               // $ids= $this->context->getfolderids();
                                $check=$this->context->GetCheckedvalues($group->id,$model->id);
                                                                                   
                        ?>                                                
                        <tr>
                            <td>                                
                                <input type="checkbox"  style="visibility: visible;" class="accountgroup" <?php echo ($check['count']>0?'Checked':'');?> data-id="<?php echo $group->id;?>" name="accountgroup[]" value="<?php echo $group->id;?>"  /> <?php echo $group->name;?>                                                           
                            </td>
                            <td>
                                <select id="accountsubgrps<?php echo $group->id;?>" data-id="<?php echo $group->id;?>" name="accountsubgrps<?php echo $group->id;?>" class="form-control accountsubgrps" style="width: 50%;">
                                    <option value="">Select Account Sub-Groups</option>
                                    <?php 
                                        if($check['subgroup']!=0):

                                             $code = AccountsSub::find()->where(['master_id'=>$group->id])->all();
                                           $data = ArrayHelper::map($code, 'id', 'name');


                                            foreach($data as $value=>$name):  
                                                if($check['subgroup']==$value):
                                                   // $selected=array('value'=>$value,'selected'=>'selected');
                                                    $selected="selected"; 
                                                else:
                                                    //$selected=array('value'=>$value);
                                                    $selected="";
                                                endif;                                                              
                                              

                                             echo '<option value="'.$value.'" '.$selected.'>'.$name.'</option>';
                                            endforeach;
                                        endif;
                                                                                                
                                    ?>                                                                       
                                </select>                            
                            </td>
                            <?php if($group->id==6):?>
                              

                            <?php else:?>
                                <!-- <td></td> -->
                            <?php endif;?>
                           
                        </tr>
                        <?php endforeach;?>                                                
                    </tbody>                                    
                </table>                
            </td>        
        </tr>   


        <tr>
            <th colspan="2">
                <button type="submit" class="btn btn-primary" id="saveaccounts">Save</button>
            </th>
        </tr>
        </tbody>
       
        </thead>
    </table>
</form>
</div></div></div></div>

<script>
$(function(){
    $('.accountgroup').change(function(){
        var accountgroup=$(this).val();        
        if($(this). prop("checked") == true){            
            $.ajax({
                type: 'POST',
                url: '<?php echo Yii::$app->request->baseUrl; ?>/accountschedule/getsubgroups',                        
                data: {accountgroup:accountgroup},
                success: function(data){
                    $('#accountsubgrps'+accountgroup).html(data);                
                }
            });
            if (accountgroup==6){
                var databsitem='<option value="0">Select Balancesheet Items</option>';
                $('#bsitems'+accountgroup).html(databsitem);
                $('#bsitems'+accountgroup).show();
            }
        }
        else
        {        
            var data='<option value="0">Select Account Sub-Groups</option>';
            var datasched='<option value="0">Select Account Schedule</option>';            
            $('#accountsubgrps'+accountgroup).html(data);   
            $('#accountschedule'+accountgroup).html(datasched);
            $('#bsitems'+accountgroup).hide();
                    
        }                                      
    });     
    $('.accountsubgrps').change(function(){            
        var accountgroup=$(this).data('id');
        var accountsubgroup=$(this).val();        
        if(accountsubgroup !=''){            
            $.ajax({
                type: 'POST',
                url: '<?php echo Yii::$app->request->baseUrl; ?>/accountschedule/getschedules',                        
                data: {accountgroup:accountgroup,accountsubgroup:accountsubgroup},
                success: function(data){
                    $('#accountschedule'+accountgroup).html(data);                
                }
            });         
        }
        else
        {                    
            var datasched='<option value="0">Select Account Schedule</option>';               
            $('#accountschedule'+accountgroup).html(datasched);  
        }        
               
    });
    $('.accountsubgrps').change(function(){
        var accountgroup=$(this).data('id');
        var accountsubgroup=$(this).val();
        if(accountsubgroup !=''){
            $.ajax({
                type: 'POST',
                url: '<?php echo Yii::$app->request->baseUrl; ?>/accountssub/getbsitems',
                data: {accountsubgroup:accountsubgroup},
                success: function(data){
                    $('#bsitems'+accountgroup).html(data);
                }
            });
        }
        else
        {
            var datasched='<option value="0">Select Balancesheet Items</option>';
            $('#bsitems'+accountgroup).html(datasched);
        }

    });
    $('#saveaccounts').click(function(){
        var error=0;
        $('.error').hide();
        var str = $('#accountsname').val();
        if(str=='')
        {
            $("#accountsname").next("span").html('Enter Account Name').show('slow');
            error=1;
        }
        /*if (/^[a-zA-Z0-9- ]*$/.test(str) == false) {
         $("#accountsname").next("span").html('Name Contains special Characters.').show('slow');
         error=1;
         }*/
        if($('#accounttype').val()==0){
            $("#accounttype").next("span").html('Select Account type').show('slow');
            error=1;
        }
        /*if($('#accounttype').val()==8){
            if($('#resourceunit').val()==0){
                $("#resourceunit").next("span").html('Select Resource item').show('slow');
                error=1;
            }
        }*/

        if (error==0){
            return true;
        }
        else {
            return false;
            ///$("#saveaccounts").removeAttr('disabled');
        }
    });
});
</script>