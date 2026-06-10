<?php
use app\models\AccountsSub; 
use app\models\AccountTypes;  
use app\models\Accountsmaster; 
use app\models\AccountsItem; 
?>

<div class="panel panel-default accountheads-tab tab acco-four">
  <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->

  <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/masters/accounts.js" type="text/javascript"></script>
  <!-- <input type="radio" id="rd5" class="acc-heads" name="rd"> -->
                
  <div class="panel-heading">
    <h4 class="panel-title acc_trigger" id="acc-heads">
      <a data-toggle="collapse" data-parent="#accordionfinmaster" href="#collapseaccnts">
      <span class="icon-calculator4 acc_trigger"></span>Account Heads</a>
    </h4>
  </div>
                
  <div id="collapseaccnts" class="tab-content cOrder-body panel-collapse collapse">
    <div class="panel-body ">                  
      <div class="search-and-content-wrpr" id="account_heads_listing">
        <div class="search-and-actions-wrpr row">
          <div class="content-search-wrpr col-md-8 col-sm-8" id="searchdiv">
            <select id="accountsubgrp" class="form-control">
              <option value="none" class="form-control">Select Account Subgroup</option>
              <?php
                  $acntsubgrp=AccountsSub::find()->where(['Status'=>0])->all();
                  foreach($acntsubgrp AS $subgrp):
                      echo "<option value='".$subgrp->id."' id='acntsubgrp'>".$subgrp->name."</option>";
                  endforeach;
              ?>
            </select>
            <select id="accounttype" class="form-control">
              <option value="none" class="form-control">Select Account Type</option>
              <?php
                $acnttypes=AccountTypes::find()->where(['Status'=>0])->all();
                foreach($acnttypes AS $acnttype):
                    echo "<option value='".$acnttype->type_id."' id='acnttssype'>".$acnttype->name."</option>";
                endforeach;
              ?>
            </select>
            <input type="hidden" id="identify">
            <input type="hidden" id="identifygrp">
            <!--<input id="searchaccounts" class="form-control" list="accountheadsearch" name="searchaccountschoice" onfocus="this.value=''" onchange="this.blur();" placeholder = "Search account head">-->

            <input id="searchaccounts" class="form-control" list="accountheadsearch" name="searchaccountschoice" autocomplete="off" onchange="this.blur();" placeholder = "Search account head">

            <datalist id="accountheadsearch">
                <?php
               // $accountheads=AccountsItem::model()->findAll();
                 $accountheads=AccountsItem::find()->where(['Status'=>0])->orderBy(['name'=>SORT_ASC])->all();

                foreach($accountheads AS $accounthead):
                    echo "<option value='".$accounthead->name."'></option>";
                endforeach;
                ?>
            </datalist>

            <!--<input type="text" placeholder="Search" id="searchaccounts" class="form-control">-->
            <button id="accountsearch" class="btn btn-primary" type="button" ><span class="icon-search5"></span></button>
          </div>
          <div class="col-md-2 col-sm-2"></div>
          <div class="content-action-wrpr col-md-2 col-sm-2">
            <a href="#" class="btn btn-primary" id="backaccount" title="Back"> Back</a>
            <a href="#" class="btn btn-primary addForm" id="addaccount" title="Add Account Head"><span class="icon-add"></span> Add</a>
            <button type="button" style="display: none;" class="btn btn-danger list-accountType" id="listaccounts"><span class="glyphicon glyphicon-list-alt"></span>List Accounts</button>
          </div>
        </div>
        <div class="content-wrpr">
          <!-- form starts here -->
          <form id="accountsform"> 
            <div class="account-heads-add-cntnt-wrpr row">
              <div class="col-md-12">
                  <div class="form-title">Add Account Head</div>
              </div> 
              <div class="col-md-3">
                <div class="form-group">
                  <label>Account Type</label>
                  <select class="form-control" id="accounttypes" name="accounttype">
                    <option value="0">Select Account type</option>
                    <?php
                      $acnttypes=AccountTypes::find()->andWhere(['Status'=>0])->all();
                      foreach($acnttypes AS $acnttype):
                          echo "<option value='".$acnttype->type_id."' id='acnttype'>".$acnttype->name."</option>";
                      endforeach;
                   ?>
                  </select>
                  <span class="error" style="display: none;"></span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Account Head</label>
                  <input class="form-control" id="accountsname"  name="accountsname" placeholder="Account Name" type="text" />
                  <input type="hidden" name="vendorid" value="">
                  <span class="error" style="display: none;"></span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>TDS(%)</label>
                      <input class="form-control" id="accounttds" name="accounttds" placeholder="TDS(%)" type="text" />
                      <span class="error" style="display: none;"></span>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>GST(%)</label>
                      <input class="form-control"  id="accountservtax" name="accountservtax" placeholder="TDS(%)" type="text" />
                      <span class="error" style="display: none;"></span>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="text-center">Schedule</label>
                      <input style="height:20px;visibility: visible;box-shadow: none;" class="form-control"  id="schedule" name="schedule"  type="checkbox" />

                      <span class="error" style="display: none;"></span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-1"></div>
              <div class="col-md-1"></div>
              <div class="col-md-12">
                <hr class="customHr" />
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                        <th><span class="headings">Account Groups</span></th>
                        <th><span class="headings">Account Sub-Groups</span></th>
                        <th><span class="headings">Schedule Items</span></th>
                        <!--<th><span class="headings">&nbsp;</span></th>-->
                    </tr>                                            
                    <?php 
                      $groups=Accountsmaster::find()->Where(['NOT LIKE', 'name', 'Project Expenditure'])->andWhere(['NOT LIKE', 'name', 'Corporate Expenditure'])->andWhere(['Status'=>0])->all();
                      foreach($groups AS $group): 
                        if($group->id==1){
                    ?>      

                    <tr>
                      <td>
                        <input type="checkbox" class="form-control accountgroup selectcheckbox"  style="visibility: visible;" data-id="<?php echo $group->id;?>" name="accountgroup[]" value="<?php echo $group->id;?>"  /> <?php echo $group->name;?>                                                         
                      </td>
                      <td>
                        <select id="accountsubgrps<?php echo $group->id;?>" data-id="<?php echo $group->id;?>" name="accountsubgrps<?php echo $group->id;?>" class="form-control accountsubgrps">
                          <option value="">Select Account Sub-Groups</option>                                   
                        </select> 
                      </td>
                      <td >
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
                      <td></td>                                            
                      <!--<td>
                          <select  class="form-control bsitems" style="display: none">
                              <option value="">Select Schedule Item</option>
                          </select>
                      </td>-->  
                    </tr>
                    <?php } else {?>
                    <tr>
                      <td>
                        <input type="checkbox" class="form-control accountgroup" style="visibility: visible;"  data-id="<?php echo $group->id;?>" name="accountgroup[]" value="<?php echo $group->id;?>"  /> <?php echo $group->name;?>
                      </td>
                      <td>
                        <select id="accountsubgrps<?php echo $group->id;?>" data-id="<?php echo $group->id;?>" name="accountsubgrps<?php echo $group->id;?>" class="form-control accountsubgrps" >
                          <option value="">Select Account Sub-Groups</option>
                        </select>
                      </td>
                      <!--<td></td>-->
                      <td>
                        <select id="bsitems<?php echo $group->id;?>" data-id="<?php echo $group->id;?>" name="bsitems<?php echo $group->id;?>" class="form-control bsitems" style=" display: none;">
                          <option value="0">Select Schedule Item</option>
                        </select>
                      </td>
                    </tr>
                    <?php } endforeach;?>                                                
                  </tbody>                                    
                </table>
              </div>
              <div class="col-md-1"></div>
              <div class="col-md-12 buttonCstmPos">
                <div class="text-center">
                  <label>&nbsp;</label>
                  <button type="button" class="btn btn-danger cancel" ><span class="icon-close"></span> Cancel</button>
                  <button type="button" class="btn btn-primary" id="saveaccounts"><span class="icon-check"></span> Add Account Head</button>
                </div>
              </div>
            </div>
          </form>
          <!-- form ends here -->
          <!-- edit form starts here -->
          <form id="accountseditform">
              <div class="account-heads-edit-cntnt-wrpr row">
                  <div class="col-md-12">
                      <div class="form-title">Edit Account Head</div>
                  </div>              
                  <div id="editaccountheads"></div>
              </div>
          </form>
          <!-- edit form ends here -->
          <!-- list start here -->
          <div class="preloader_accnthead" style="display: none;"><center><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"></center></div>
          <div id="accountsitems"></div>                    
          <!-- list end here -->   
        </div>
      </div>

      <!-- Schedule items start -->

      <div id="accountbsitemslist"> </div>

      <div id="accountbsitemsaddsection" style="display:none">
          <form id="accountbsitemsform">
              <div class="row">
                  <div class="col-md-2"></div>
                  <div class="col-md-6">
                      <div class="form-group">
                          <label>Schedule Item</label>
                          <input type="text" class="form-control" id="accountaddbsname" placeholder="Schedule Item">
                          <span class="error" style="display: none;"></span>
                      </div>  
                  </div>
                  <div class="col-md-3 text-left">
                      <label></label>
                      <input type="hidden" id="accounthead_id" value="">
                      <button type="button" class="btn btn-danger cancel" id="accountcancelbscreate"><span class="icon-close"></span> Cancel</button>
                      <button type="button" class="btn btn-primary accountsavebscreate" id=""><span class="icon-check"></span> Save </button>
                  </div>
                  <div class="col-md-1"></div>
              </div>
          </form>
      </div>

      <!-- Schedule items end -->

    </div>
  </div>

  <script>
    /*$(document).on('change', '.accountgroup', function() {  
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
      
    $(document).on('change', '.accountsubgrps', function() {
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
    
    $(document).on('change', '.accountsubgrps', function() {  
      var accountgroup=$(this).data('id');
      var accountsubgroup=$(this).val();
      if(accountsubgroup !=''){
          $.ajax({
              type: 'POST',
              url: '../accountssub/getbsitems',
              data: {accountsubgroup:accountsubgroup},
              success: function(data){
                  //$('#bsitems'+accountgroup).html(data);
                  $('#bsitems'+accountgroup).html(data.result);
              }
          });
      }
      else
      {
          var datasched='<option value="0">Select BS Item</option>';
          $('#bsitems'+accountgroup).html(datasched);
      }

    });*/
    
    $(document).on('change', '#accounttype', function() { 
      var type=$(this).val();
      if(type==8)
      {
          $('.selectcheckbox').prop('checked', true).trigger("change");
          $('#resource_type_list').show();
          $('#resource_list').show();
      }
      else
      {
          $('.selectcheckbox').prop('checked', false).trigger("change");
          $('#resourcetype').val(0);
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
      if($('#accounttype').val()==0){
          $("#accounttype").next("span").html('Select Account type').show('slow');
          error=1;
      }
      if($('#accounttype').val()==8){
          if($('.selectcheckbox').prop('checked')==true){
              if($('#resourcetype').val()==0){
                  $("#resourcetype").next("span").html('Select Resource Group').show('slow');
                  error=1;
              }
          }

          if($('.selectcheckbox1').prop('checked')==true){
              if($('#resourcetype_cor').val()==0){
                  $("#resourcetype_corinfo").html('Select Resource Group').show('slow');
                  error=1;
              }
          }
      }

      if (error==0){
          return true;
      }
      else {
          return false;
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
      if($('#accounttype').val()==0){
          $("#accounttype").next("span").html('Select Account type').show('slow');
          error=1;
      }
      if($('#accounttype').val()==8){
          if($('#resourcetype').val()==0){
              $("#resourcetype").next("span").html('Select Resource Group').show('slow');
              error=1;
          }
      }

      if (error==0){
          return true;
      }
      else {
          return false;
      }
    });

  </script>


</div>