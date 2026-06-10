
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/productfunctions.js" type="text/javascript"></script>
<button type="button" value="Back" name="goback" title="back" class="btn btn-primary" style="float: right;width: 100px" onclick="goBack()">Back</button>
<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<h1 >Activity </h1>
<form method="POST" action="" id="productform">
    <table class="table table-bordered"  >
        <tbody>
        <tr>
            <th><span class="headings">Activity Name</span></th>
            <td><input type="text" class="form-control" placeholder="Activity Name" name="Setup_Name" id="Name"><span class='error'></span></td>
        </tr>
        <tr style="background-color: white">
            <th><span class="headings">Unit</span></th>

            <td><input type="hidden" name="projid" value="<?php echo $_GET['projectid']; ?>"><input type="text" class="form-control" style="width: 100px;" placeholder="Unit" name="Setup_Unit" id="Unit"><span class='error'></span></td>
        </tr>


        <tr >
            <th colspan="2"><button type="submit" class="btn btn-primary" id="saveproduct" value="1" name="Product_saveproduct">Save</button></th>
        </tr>
        </tbody>
        <!--<thead>
            <tr>
                <th><span class="headings">Product Name</span><input type="text" class="form-control" placeholder="Product Name" name="Product_Name" id="Name"><span class='error'></span></th>
                <th><span class="headings">Unit</span><input type="text" class="form-control" placeholder="Unite" name="Product_Unit" id="Unit"><span class='error'></span></th>
                <th><span class="headings">Rate</span><input type="text" class="form-control" placeholder="Rate"  id="productratetotal" name="Product_Rate" readonly="readonly"  value="0"><span class='error'></span></th>
                <th><button type="button" class="btn btn-primary" id="saveproduct" value="1" name="Product_saveproduct">Save</button></th>
            </tr>-->
        </thead>
    </table>
</form>
<input type="hidden" id="pageaction" value="create">
<input type="hidden" id="ProductId" value="<?php echo $model->Product_Id;?>">
<!--<script type="text/javascript">
    $("#searchname").autocompleteArray([<?php /*echo $dataProvider;*/?>]);
</script>-->