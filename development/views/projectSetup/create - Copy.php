<?php
$this->breadcrumbs=array(
    'Products'=>array('index'),
    'create'
);
?>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/productfunctions.js" type="text/javascript"></script>
<h1>Products </h1>
<form method="POST" action="" id="productform">
    <table class="table table-bordered"  >
        <thead>
            <tr>
                <th><span class="headings">Product Name</span><input type="text" class="form-control" placeholder="Product Name" name="Product_Name" id="Name"><span class='error'></span></th>
                <th><span class="headings">Unit</span><input type="text" class="form-control" placeholder="Unite" name="Product_Unit" id="Unit"><span class='error'></span></th>
                <th><span class="headings">Rate</span><input type="text" class="form-control" placeholder="Rate"  id="productratetotal" name="Product_Rate" readonly="readonly"  value="0"><span class='error'></span></th>
                <th><button type="button" class="btn btn-primary" id="saveproduct" value="1" name="Product_saveproduct">Save</button></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4">
                    <table class="table table-bordered"  >
                        <thead>
                        <tr>
                            <td><input type="hidden" id="pageaction" value="create"><b>Resource</b></td>
                            <td><b>Quantity</b></td>
                            <td colspan="2"><b>Rate</b></td>
                        </tr>
                        <tr class="preloaderitems"><td colspan="4" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody  id="addedresources">
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>

    </table>
</form>

<div class="row show-grid">
    <div class="col-md-3">
        <select class="form-control" id="selecttype">
            <option value="none">Select</option>
            <?php $list = Resourcetype::model()->findAll('Status=:type', array(':type' => '0'));
            foreach($list AS $type):
                echo "<option value='".$type->ResourceType_Id."'>".$type->Name."</option>";
            endforeach;
            ?>
        </select>

    </div>
    <div class="col-md-6">
        <input class="form-control" id="searchname" type="text" placeholder="Search">
    </div>
    <div class="col-md-3">
        <button type="button" class="btn btn-primary" id="resourcesearch"><span class="glyphicon glyphicon-search" ></span>Search</button>
    </div>
</div>



<!-- Squared FOUR -->




<!--Table-->

<table class="table table-bordered" id="resourcetable">

    <thead>
    <tr>
        <th >Resource Name</th>
        <th >Unit</th>
        <th >Rate</th>
        <th >Quantity</th>
        <th ><input type="hidden" value="<?php echo $tempid;?>" id="tempprodid"> </th>
    </tr>
    <tr class="preloader"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
    </thead>
    <tbody id="resourceitems">





    </tbody>
</table>
<script type="text/javascript">
    $("#searchname").autocompleteArray([<?php echo $dataProvider;?>]);
</script>