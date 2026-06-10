<?php

$addedones='';
foreach($resourcesadded AS $key=>$data):
    $addedones.="<tr id='tempresrow".$data['Resource_Id']."'>
    <td>".$data['resource']."
    <input type='hidden' name='resid[]' value='".$data['Resource_Id']."'>
    <input type='hidden' name='logid' value='".$data['Logistics_Id']."'></td>
    <td style='width: 20%'>".$data['Unit']."</td>
    <td style='width: 20%'>".$data['actual_price']."</td>
    <td style='width: 20%'>".$data['Quantity']."<input type='hidden' name='resqty[]' value='".$data['Quantity']."'></td>
    <td style='width: 20%'><input type='text' data-id='".$data['Resource_Id']."' class='form-control specrate' name='specrate[]' id='specrate".$data['Resource_Id']."' value='".$data['actual_price']."' ><span class='error'></span>
    </tr>";

endforeach;
?>
<h1>Logistics Resources</h1>
<form action="" method="post" id="resourceform">
    <table class="table table-bordered"  >
        <thead>
        <tr>
            <th style="width: 50%"><span class="headings">Logistics Name</span>
                <input type="text" class="form-control" placeholder="Product Name" name="Product_Name" id="Name" value="<?php echo $datavalue['Name'];?>" readonly="readonly"><span class='error'></span></th>
            <th style="width: 20%"><span class="headings">Unit</span><input type="text" class="form-control" placeholder="Unit" name="Product_Unit" id="Unit" readonly="readonly" value="<?php echo $datavalue['Unit'];?>" ><span class='error'></span></th>
            <th style="width: 20%"><span class="headings">Rate</span><input type="text" class="form-control" placeholder="Rate"  id="productratetotal" name="Product_Rate" readonly="readonly"  value="<?php echo $datavalue['rate'];?>"><span class='error'></span></th>
            <th style="width: 10%"><button type="submit" class="btn btn-primary" id="updateresource" name="updateresource" title='Save Product'>Save</button></th>
        </tr>
        </thead>
    </table>
    <table class="table table-bordered"  >
        <thead>
        <tr>
            <th colspan="5"><span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center">Resources</span></th>
        </tr>
        <tr>
            <th><b>Resource</b></th>
            <th><b>Unit</b></th>
            <th><b>Rate</b></th>
            <th><b>Quantity</b></th>
            <th><b>Specific Rate</b></th>
        </tr>
        <tr class="preloaderitems"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
        </thead>
        <tbody  id="addedresources">
        <?php echo $addedones;?>
        </tbody>
    </table>
</form>