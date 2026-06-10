
<?php
use app\models\AccountTypes;  
use app\models\Resources; 
use app\models\Accountsmaster;
?>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/accounts.js" type="text/javascript"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/js/script.js" type="text/javascript"></script>

<div class="container procu-accordion">
    <div class="row">
        <div class="col-md-12">
            
            <div class="panel-group acco-one-active" >

<button type="button" value="Back" name="goback" title="back" class="btn btn-danger cancel" style="float: right;width: 100px;margin-bottom: 15px;" onclick="goBack()">Back</button>
<script>  
    function goBack()
    {
        window.history.back()
    }
   /* $(function() {
        $('.resourceunit').selectpicker();
    });*/
</script>
<h4><center>Add Account Head</center></h4>
<form method="POST" action="" id="accountheadform">

    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
    <table class="table table-bordered"  >
        <tbody>
        <tr>
            <th><span class="headings">Account type</span></th>
            <td><select id="accounttype" name="accounttype" class="form-control" style="width: 50%;">
                <option value="0">Select Account type</option>
                    <?php
                     $acnttypes=AccountTypes::find()->all();
                    foreach($acnttypes AS $acnttype):
                        echo "<option value='".$acnttype->type_id."' id='acnttype'>".$acnttype->name."</option>";
                    endforeach;
                    ?>
            </select>
                <span class="error" id="typeerroinfo" style="display: none;"></span>
            </td>
        </tr>    
        <tr style="background-color: white;">
            <th style="width: 25%"><span class="headings">Accounts Name</span></th>
            <td style="width: 75%">
                <input class="form-control" style="width: 50%;" type="text" id="accountsname" name="accountsname" placeholder="Accounts Name" value="">
                <span class="error" id="nameerrorinfo" style="display: none;"></span>
                <input type="hidden" name="vendorid" value="">
            </td>
        </tr>
      
        <tr style="background-color: white;display: none" id="resourceunitrow">
            <th><span class="headings">Resource item</span></th>
            <td><select id="resourceunit" name="resourceunit" class="form-control resourceunit" data-live-search="true" style="width: 50% !important;">
                    <option data-tokens='Select Resource item' value="0">Select Resource item</option>
                    <?php
                   // $resources=Resources::model()->findAll(array('condition'=>'pricing_status=0','order'=>'Resource_Id ASC'));

                    $resources=Resources::find()->Where(['pricing_status'=>0])->orderBy(['Resource_Id'=> 'SORT_ASC'])->all();


                    foreach($resources AS $resource):
                        echo "<option data-tokens='".$resource->Name."' value='".$resource->Resource_Id."'>".$resource->Name."</option>";
                    endforeach;
                    ?>
                </select>
                <span class="error" id="resuniterroinfo" style="display: none;"></span>
            </td>
        </tr>
        <tr style="background-color: white">
            <th><span class="headings">TDS(%)</span></th>
            <td><input class="form-control" style="width: 16%;" type="text" id="accounttds" name="accounttds" placeholder="TDS(%)"><span class="error" style="display: none;"></span></td>
        </tr>
        <tr style="background-color: white">
            <th><span class="headings">GST(%)</span></th>
            <td><input class="form-control" style="width: 16%;" type="text" id="accountservtax" name="accountservtax" placeholder="Gst Tax(%)"><span class="error" style="display: none;"></span></td>
        </tr>
        <tr style="background-color: white">
            <th><span class="headings">Schedule</span></th>
            <td><input type="checkbox" value="3" id="schedule" name="schedule" style="visibility: visible;"></td>
        </tr>
        <tr style="background-color: white">
            <td colspan="2">
                <table class="table table-bordered"  >
                    <tbody>
                        <tr>
                            <th><span class="headings">Account Groups</span></th>
                            <th><span class="headings">Account Sub-Groups</span></th>
                           <!--  <th><span class="headings">Resource Group</span></th> -->

                            <!--<th><span class="headings">Resource</span></th>-->

                            <!-- <th><span class="headings">BS Items</span></th> -->

                            <!--<th><span class="headings">Account Schedule</span></th>-->
                        </tr>
                        <?php 
                            //$groups=Accountsmaster::model()->findAll();
                            $groups=Accountsmaster::find()->all();
                            foreach($groups AS $group): 
                               if($group->id==1){
                        ?>                                                
                        <tr>
                            <td>
                                <input type="checkbox" class="accountgroup selectcheckbox"  style="visibility: visible;" data-id="<?php echo $group->id;?>" name="accountgroup[]" value="<?php echo $group->id;?>"  /> <?php echo $group->name;?>                                                           
                            </td>
                            <td>
                                <select id="accountsubgrps<?php echo $group->id;?>" data-id="<?php echo $group->id;?>" name="accountsubgrps<?php echo $group->id;?>" class="form-control accountsubgrps">
                                    <option value="">Select Account Sub-Groups</option>                                   
                                </select>                            
                            </td>
                           
                        </tr>
                        <?php } elseif($group->id==9) {?> 

                        <tr>
                            <td>
                                <input type="checkbox" class="accountgroup selectcheckbox1"  style="visibility: visible;"  data-id="<?php echo $group->id;?>" name="accountgroup[]" value="<?php echo $group->id;?>"  /> <?php echo $group->name;?>                                                           
                            </td>
                            <td>
                                <select id="accountsubgrps<?php echo $group->id;?>" data-id="<?php echo $group->id;?>" name="accountsubgrps<?php echo $group->id;?>" class="form-control accountsubgrps">
                                    <option value="">Select Account Sub-Groups</option>                                   
                                </select>                            
                            </td>
                          
                        </tr>

                        <?php } else {?>
                        <tr>
                            <td>
                                <input type="checkbox" class="accountgroup" style="visibility: visible;"  data-id="<?php echo $group->id;?>" name="accountgroup[]" value="<?php echo $group->id;?>"  /> <?php echo $group->name;?>                                                           
                            </td>
                            <td>
                                <select id="accountsubgrps<?php echo $group->id;?>" data-id="<?php echo $group->id;?>" name="accountsubgrps<?php echo $group->id;?>" class="form-control accountsubgrps" >
                                    <option value="">Select Account Sub-Groups</option>                                   
                                </select>                            
                            </td>
                         
                        </tr>
                        <?php } endforeach;?>                                                
                    </tbody>                                    
                </table>                
            </td>        
        </tr>                        
        <tr >
            <th style="width: 50%;"><button type="submit" class="btn btn-primary" id="saveaccounts">Save</button></th>
            <th style="width: 50%;"><button type="submit" class="btn btn-primary" id="save-create">Save & Create</button></th>
            <input type="hidden" id="save_create" name="save_create" value="">
        </tr>
        </tbody>
       
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
                url: '../accountschedule/getsubgroups',                        
                data: {accountgroup:accountgroup},
                success: function(data){
                    $('#accountsubgrps'+accountgroup).html(data);
                }
            });
            if (accountgroup==6){
                var databsitem='<option value="0">Select BS Item</option>';
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
                url: '../accountschedule/getschedules',                        
                data: {accountgroup:accountgroup,accountsubgroup:accountsubgroup},
                success: function(data){
                    $('#accountschedule'+accountgroup).html(data);                
                }
            });  
            
            if(accountgroup==1){
               $.ajax({

                    type: 'POST',

                    url: '../accountsitem/resourcetype',

                    dataType:"json",

                    data:{accountsubgrp:accountsubgroup},

                    success: function(data){

                        if(data.error=='No')
                        {
                            $('#resourcetype').html(data.result);

                        }
                        else
                        {
                            alert(data.error);
                        }

                    }

                });
            }
            else if(accountgroup==9){
               $.ajax({

                    type: 'POST',

                    url: '../accountsitem/resourcetype',

                    dataType:"json",

                    data:{accountsubgrp:accountsubgroup},

                    success: function(data){

                        if(data.error=='No')
                        {
                            $('#resourcetype_cor').html(data.result);

                        }
                        else
                        {
                            alert(data.error);
                        }

                    }

                });
            }
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
                url: '../accountssub/getbsitems',
                data: {accountsubgroup:accountsubgroup},
                success: function(data){
                    $('#bsitems'+accountgroup).html(data);
                }
            });
        }
        else
        {
            var datasched='<option value="0">Select BS Item</option>';
            $('#bsitems'+accountgroup).html(datasched);
        }

    });
    $('#accounttype').change(function(){
        var type=$(this).val();
        if(type==8)
        {
            ///$('#resourceunitrow').show();
            $('.selectcheckbox').prop('checked', true).trigger("change");
            //$('#account_subgrp_list').show();
            $('#resource_type_list').show();
            $('#resource_list').show();
        }
        else
        {
            $('.selectcheckbox').prop('checked', false).trigger("change");
            $('#resourcetype').val(0);
           // $('.ms-close-btn').trigger("click");
            $('.ms-close-btn').each(function(){
                $('.ms-close-btn').trigger("click");
            });
            $('#resourceunitrow').hide();
            $('#account_subgrp_list').hide();
            $('#resource_type_list').hide();
            $('#resource_list').hide();
        }
    });
    $('#saveaccounts').click(function(){
        $('#save_create').val('');
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
        if($('#accounttype').val()==8){
            /*if($('#resourceunit').val()==0){
                $("#resourceunit").next("span").html('Select Resource item').show('slow');
                error=1;
            }*/
            if($('.selectcheckbox').prop('checked')==true){
                //alert('hai1')
                if($('#resourcetype').val()==0){
                    $("#resourcetype").next("span").html('Select Resource Group').show('slow');
                    error=1;
                }
            }

            if($('.selectcheckbox1').prop('checked')==true){
                //alert('hai2')
                if($('#resourcetype_cor').val()==0){
                    $("#resourcetype_corinfo").html('Select Resource Group').show('slow');
                    error=1;
                }
            }
            /* if($('.creditacnt').attr('placeholder')=='Type or click here'){
                $('#resourceinfo').show('slow');
                error=1;
            } */
        }

        if (error==0){
            return true;
        }
        else {
            return false;
            //$("#saveaccounts").removeAttr('disabled');
        }
    });
    
    $('#save-create').click(function(){
        var error=0;
        $('#save_create').val('mode');
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
        if($('#accounttype').val()==8){
            /*if($('#resourceunit').val()==0){
                $("#resourceunit").next("span").html('Select Resource item').show('slow');
                error=1;
            }*/
            if($('#resourcetype').val()==0){
                $("#resourcetype").next("span").html('Select Resource Group').show('slow');
                error=1;
            }
           /* if($('.creditacnt').attr('placeholder')=='Type or click here'){
                $('#resourceinfo').show('slow');
                error=1;
            } */
        }

        if (error==0){
            return true;
        }
        else {
            return false;
            //$("#saveaccounts").removeAttr('disabled');
        }
    });


});
</script>