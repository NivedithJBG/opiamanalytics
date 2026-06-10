<?php

$addedones='';
foreach($resourcesadded AS $key=>$data):
    $price=$price+$data['Price'];
    $addedones.="<tr id='tempresrow".$data['PSR_Id']."'><td>".Resourcetype::model()->findByPk( $data['ResourceType_Id'])->Name."</td><td>".$data['resource']."</td><td style='width: 20%'>".$data['Unit']."</td><td style='width: 20%'><input type='hidden' name='resourceid[]' value='".$data['PSR_Id']."'><input type='text' data-id='".$data['PSR_Id']."' class='form-control resourceqty' name='resourceqty[]' id='quantity".$data['PSR_Id']."' value='".$data['PSR_Quantity']."' ><span class='error'></span></td><td style='width: 20%'><input type='text' data-id='".$data['PSR_Id']."' class='form-control resourcerate' name='resourcerate[]' id='rate".$data['PSR_Id']."' value='".$data['PSR_Actual_Price']."' ></td><td class='resource-amount' id='amount".$data['PSR_Id']."'><input type='hidden' name='resourceamount[]' value='".$data['PSR_Price']."'>".$data['PSR_Price']."</td>
                                <td class='small75'><button type='button' class='btn btn-primary removeresourceitem' id='removeresourceitem".$data['PSR_Id']."' value='".$data['PSR_Id']."' title='Remove Item'><span >Remove</span></button></td>
                            </tr>";

endforeach;
?>
<button type="button" value="Back" name="goback" title="back" class="btn btn-primary" style="float: right;width: 100px" onclick="goBack()">Back</button>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/projectsetup.js" type="text/javascript"></script>
<h1>Activity Resources</h1>

<script>
    function goBack()
    {
        window.location = '<?php echo Yii::app()->createUrl('engineering/index#worktype');?>'
    }
</script>
<form method="POST" action="" id="productform">
    <table class="table table-bordered"  >
        <thead>
        <tr>
            <th style="width: 50%"><span class="headings">Activity Name</span><input type="text" class="form-control" placeholder="Major Consumables Name" name="Setup_Name" id="Name" value="<?php echo $model->PS_Name;?>" ><span class='error'></span></th>
            <th style="width: 20%"><span class="headings">Unit</span><input type="text" class="form-control" placeholder="Unite" name="Setup_Unit" id="Unit" value="<?php echo $model->PS_Unit;?>" ><span class='error'></span></th>
            <th style="width: 20%"><span class="headings">Rate</span><input type="text" class="form-control" placeholder="Rate"  id="investmentratetotal" name="Consumables_Rate" readonly="readonly"  value="<?php echo $model->PS_Price;?>"><span class='error'></span></th>
            <th style="width: 10%"><button type="submit" class="btn btn-primary" id="updateproduct" value="1" name="Product_saveproduct" title='Save Product'>Save</button></th>
        </tr>
        </thead>
    </table>

<input type="hidden" id="Consumables_Id" name="Consumables_Id" value="<?php echo $model->PS_Id;?>">
<input type="hidden" id="pageaction" value="update">

<table class="table table-bordered"  >
    <thead>
    <tr>
        <th colspan="7"><span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center">Added Resources</span></th>
    </tr>
    <tr>
        <th><b>Resource Type</b></th>
        <th><b>Resource</b></th>
        <!--<th><b>Location</b></th>-->
        <th><b>Unit</b></th>
        <th><b>Quantity</b></th>
        <th><b>Rate</b></th>
        <th><b>Amount</b></th>
        <th></th>
    </tr>
    <tr class="preloaderitems"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
    </thead>
    <tbody  id="addedresources">
    <?php echo $addedones;?>
    </tbody>
</table>
</form>
<span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: left;font-size: 15px;">Click on the tabs to search and add Items</span>
<div class="row show-grid">
    <!--<div class="col-md-3">
        <select class="form-control" id="selecttype">
            <option value="none">Select Resource Type</option>
            <?php /*$list = Resourcetype::model()->findAll('Status=:type', array(':type' => '0'));
            foreach($list AS $type):
                echo "<option value='".$type->ResourceType_Id."'>".$type->Name."</option>";
            endforeach;
            */?>
        </select>

    </div>--><!--<div class="col-md-2">
    <select class="form-control" id="selectvendor">
        <option value="none">Select Vendor</option>
        <?php /*$list = Vendors::model()->findAll('Status=:type', array(':type' => '0'));
        foreach($list AS $type):
            echo "<option value='".$type->Vendor_Id."'>".$type->Name."</option>";
        endforeach;
        */?>
    </select>

</div>-->
    <!--<div class="col-md-6">
        <input class="form-control" id="searchname" type="text" placeholder="Search">
    </div>-->
    <div class="col-md-12 resourcetypebuttons">
        <?php
        $Criteria = new CDbCriteria();
        $Criteria->order='sortorder ASC'; 
        // $Criteria->order='ResourceType_Id ASC';
        // $Criteria->order='Name ASC';
        $resourcetypes = Resourcetype::model()->findAll($Criteria);
        foreach($resourcetypes AS $resourcetype):
        ?>
        <div class="col-md-2"><button type="button" class="btn btn-primary resourcesearch" id="selectedresource" value="<?php echo $resourcetype->ResourceType_Id ?>" title="<?php echo $resourcetype->Name ?>"><?php echo $resourcetype->Name ?></button></div>
        <?php
        endforeach;
        ?>
    </div>
    <!--<div class="col-md-2">
        <button type="button" class="btn btn-primary" id="resourcesearchsetup"><span class="glyphicon glyphicon-search" ></span>Search</button>
    </div>-->
</div>
<input type="hidden" id="selectresourceid">
<div class="resourcesadd" id="resourcetable">
<div class="row show-grid">
        <div class="col-md-3">
            <select id="resourcegroupselection" class="form-control">

            </select>
        </div>
        <div class="col-md-6">
            <input class="form-control" id="resourcename" type="text" placeholder="Search">
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary resourcesearch" id="resourcesearch">
                <span class="glyphicon glyphicon-search"></span>Search
            </button>
        </div>
    </div>

<table class="table table-bordered" id="">

    <thead>
    <tr>
        <!--<th >Resource Type</th>
        <th>Resource Group</th>-->
        <th >Resource Name</th>
        <th >Vendor Name</th>
        <th >Location</th>
        <th >Unit</th>
        <th >Rate</th>
        <th >Specific rate</th>
        <th >Quantity</th>
        <th ></th>
    </tr>
    <tr class="preloader"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
    </thead>
    <tbody id="resourceitems">





    </tbody>
</table>
</div>
<!--<script type="text/javascript">
    $("#searchname").autocompleteArray([<?php /*echo $dataProvider;*/?>]);
</script>-->