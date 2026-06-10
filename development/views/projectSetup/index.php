<?php
    $this->breadcrumbs=array(
        'Products'=>array('index'),
    );
?>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/productfunctions.js" type="text/javascript"></script>
<h1>Products </h1>

<div class="row show-grid">
    <div class="col-md-6"><a href="<?php echo Yii::app()->request->baseUrl; ?>/products/create" ><button type="button" class="btn btn-success" ><span class="glyphicon glyphicon-plus-sign"></span>Add Products</button></a></div>
</div>

<div class="row show-grid">
    <div class="col-md-9">
        <input class="form-control" id="productsearchname" type="text" placeholder="Search">
    </div>
    <div class="col-md-3">
        <button type="button" class="btn btn-primary" id="productsearch"><span class="glyphicon glyphicon-search" ></span>Search</button>
    </div>
</div>
<table class="table table-bordered" id="resourcetable">

    <thead>
    <tr>
        <th>#</th>
        <th>Product Name</th>
        <th>Unit</th>
        <th>Rate</th>
        <th colspan="2"></th>
    </tr>
    <tr class="preloader"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
    </thead>
    <tbody id="productslist">
     <?php
     foreach($dataProvider AS $key=>$data):
         echo "<tr>
                                <td>".$data['Product_Id']."</td>
                                <td>".$data['Name']."</td>
                                <td>".$data['Unit']."</td>
                                <td>".$data['Price']."</td>
                                <td>
                                    <a href='".Yii::app()->request->baseUrl."/products/update?id=".$data['Product_Id']."'><button type='button' class='btn btn-primary editproduct' value='".$data['Product_Id']."' id='editproductbutton".$data['Product_Id']."'> <span class='glyphicon '></span>Edit</button></a>
                                </td>
                                <td><button type='button' class='btn btn-primary deleteproduct' value='".$data['Product_Id']."' id='deleteproductbotton".$data['Product_Id']."'> <span class='glyphicon '></span>Delete</button></td>
                            </tr>";
     endforeach;
     ?>

    </tbody>
</table>
<script type="text/javascript">
    $("#productsearchname").autocompleteArray([<?php echo $names;?>]);
</script>