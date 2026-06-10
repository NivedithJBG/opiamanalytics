<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/accountschedule.js" type="text/javascript"></script>
<button type="button" value="Back" name="goback" title="back" class="btn btn-primary" style="float: right;width: 100px" onclick="goBack()">Back</button>
<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<h1>Account Schedule</h1>
<input type="hidden" value="<?php echo $model->id;?>" id="scheduleid">
    <form method="POST" action="" id="accountsform">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><span class="headings">Account Group</span></th>
                    <th><span class="headings">Account Sub-Group</span></th>
                    <th><span class="headings">Name</span></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <select name="groupid" id="groupid" class="form-control">
                     <?php $accountgrp=Accountsmaster::model()->findAll();
                        foreach($accountgrp AS $groups):
                            if($groups['id']==$model->master_id):$selected="selected";else:$selected="";endif;?>
                            <option value="<?php echo $groups['id'];?>" <?php echo $selected;?>><?php echo $groups['name'];?></option>
                    <?php endforeach;?>
                    </select>
                </td>
                <td>
                    <select name="subgroupid" id="subgroupid" class="form-control">
                     <?php $subaccountgrp=AccountsSub::model()->findAll(array('condition'=>'master_id='.$model->master_id,'order'=>'sortorder ASC'));
                        foreach($subaccountgrp AS $subgroups):
                            if($subgroups['id']==$model->sub_id):$selected="selected";else:$selected="";endif;?>
                            <option value="<?php echo $subgroups['id'];?>" <?php echo $selected;?>><?php echo $subgroups['name'];?></option>
                    <?php endforeach;?>
                    </select>
                </td>                
                <td><input type="text" class="form-control" name="schedulename" value="<?php echo $model->name;?>"></td>
                <td><button type="submit" class="btn btn-primary" >Save</button></td>
            </tr>
            </tbody>
        </table>
    </form>
       <!-- <table class="table table-bordered">
            <thead>
            <tr>
                <th colspan="5"><span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center">Added Accounts</span></th>
            </tr>
            <tr>
                <th><b>Account Name</b></th>
                <th><b>TDS</b></th>
                <th><b>Service Tax</b></th>
                <th colspan="2"><b>Account Type</b></th>
            </tr>
            <tr class="preloaderitems"><td colspan="9" align="center"><img src="<?php /*echo Yii::app()->request->baseUrl; */?>/images/loader.gif" align="middle"> </td></tr>
            </thead>
            <tbody  id="addedaccounts">
                <?php
/*                    foreach($dataProvider AS $key=>$data):
                        if($data['account_type']==1){
                            $type='Cash';
                        }
                        elseif($data['account_type']==2){
                            $type='Bank';
                        }
                        elseif($data['account_type']==4){
                            $type='Income';
                        }
                        elseif($data['account_type']==5){
                            $type='Expense';
                        }
                        elseif($data['account_type']==6){
                            $type='Asset';
                        }
                        elseif($data['account_type']==7){
                            $type='Liability';
                        }
                        echo "<tr id='tempaccountrow".$data['id']."'>
                                    <td>".$data['name']."</td>
                                    <td>".$data['tds']."</td>
                                    <td>".$data['servicetax']."</td>
                                    <td>$type</td>
                                    <td><button type='button' class='btn btn-primary removeaccount' id='removeaccount".$data['id']."' value='".$data['id']."'>Remove</button></td>
                                    </tr>";
                    endforeach;
                */?>
            </tbody>
        </table>

<div id="accountsection">
    <div class="row show-grid">
        <div class="col-md-6">
            <input class="form-control" id="accountname" type="text" placeholder="Search">
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary" id="accountsearch"><span class="glyphicon glyphicon-search" ></span>Search</button>
        </div>
    </div>

    <table class="table table-bordered" id="accountlists" style="display: none;">

        <thead>
        <tr>
            <th >Account Name</th>
            <th><b>TDS</b></th>
            <th colspan="2"><b>Service Tax</b></th>

        </tr>
        <tr class="preloader"><td colspan="4" align="center"><img src="<?php /*echo Yii::app()->request->baseUrl; */?>/images/loader.gif" align="middle"> </td></tr>
        </thead>
        <tbody id="accountitems">

        </tbody>
    </table>
</div>-->