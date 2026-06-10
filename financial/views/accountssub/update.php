<?php
use app\models\Accountsmaster;  
use app\models\Resourcetype;
?>

<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/subgroup.js" type="text/javascript"></script>

<div class="container procu-accordion">
    <div class="row">
        <div class="col-md-12">
            
            <div class="panel-group acco-one-active" >
<button type="button" value="Back" name="goback" title="back" class="btn btn-danger cancel" style ="float: right;width: 100px;margin-bottom: 11px;" onclick="goBack()">Back</button>
<script>
    function goBack()
    {
        window.history.back()
    }
</script>

<h4>Subgroup: <?php echo $model->name;?></h4>
<input type="hidden" value="<?php echo $model->id;?>" id="subgrpid">
    <form method="POST" action="" id="accountsform">
          <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><span class="headings">Account Group</span></th>
                    <th><span class="headings">Resource Type</span></th>
                    <th><span class="headings">Name</span></th>
                  <!--   <th><span class="headings">Type</span></th> -->
                    <!--<th><span class="headings">Spread</span></th>-->
                    <th></th>
                   <!--  <th></th> -->
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <select name="groupid" class="form-control">
                     <?php $accountgrp=Accountsmaster::find()->all();
                        foreach($accountgrp AS $groups):
                            if($groups['id']==$group['master_id']):$selected="selected";else:$selected="";endif;?>
                            <option value="<?php echo $groups['id'];?>" <?php echo $selected;?>><?php echo $groups['name'];?></option>
                    <?php endforeach;?>
                    </select>
                </td>
                <td>
                    <select name="resource_typeid" class="form-control">
                     <?php $resourcetypes=Resourcetype::find()->all();
                        foreach($resourcetypes AS $resourcetype):
                            if($resourcetype['ResourceType_Id']==$group['ResourceType_Id']):$selected="selected";else:$selected="";endif;?>
                            <option value="<?php echo $resourcetype['ResourceType_Id'];?>" <?php echo $selected;?>><?php echo $resourcetype['Name'];?></option>
                    <?php endforeach;?>
                    </select>
                </td>
                <td><input type="text" class="form-control" name="subgrpname" value="<?php echo $model->name;?>"></td>
              
                <td style="display: none;">
                    <select class="form-control" name="type" style="display: none">
                        <option value='none'>Select Type</option>
                        <option value='1' <?php echo ($model->type=='1'?'selected':'');?> >Cash Inflow</option>
                        <option value='2' <?php echo ($model->type=='2'?'selected':'');?> >Cash Outflow</option>
                    </select>
                </td>
                <td><button type="submit" class="btn btn-primary" > <span class="icon-check"></span>Edit Account Sub Group</button></td>
            </tr>
            </tbody>
        </table>
    </form>

</div></div></div></div>
      