<button type="button" value="Back" name="goback" title="back" class="btn btn-primary" style="float: right;width: 100px" onclick="goBack()">Back</button>
<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<h1>View Activity :<?php echo $model->PS_Name;?></h1>
<form method="POST" action="" id="productform">
    <table class="table table-bordered"  >
        <thead>
        <tr>
            <th><span class="headings">Activity Name</span></th>
            <th><span class="headings">Unit</span></th>
            <th><span class="headings">Rate</span></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><span class="headings"><?php echo $model->PS_Name;?></span></td>
            <td><span class="headings"><?php echo $model->PS_Unit;?></span></td>
            <td><span class="headings"><?php echo $model->PS_Price;?></span></td>
        </tr>
        <tr>
            <td colspan="4">
                <table class="table table-bordered"  >
                    <thead>
                    <tr>
                        <td><b>Resource</b></td>
                        <td><b>Quantity</b></td>
                        <td><b>Rate</b></td>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach($dataProvider AS $key=>$data):?>
                            <tr>
                                <td><?php echo $data['Name'];?></td>
                                <td><?php echo $data['PSR_Quantity'];?></td>
                                <td ><?php echo $data['resprice'];?></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>

    </table>
</form>