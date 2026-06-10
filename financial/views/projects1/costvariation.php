<h1>Project :<?php echo $project->Name; ?></h1>
<h3 class="pull-left">Cost Variation</h3>
<table class="table table-bordered" id="costvariationtable">
    <thead>
    <tr>
        <th><b>Process</b></th>
        <th><b>Sl No</b></th>
        <th><b>Activity</b></th>
        <th><b>Unit</b></th>
        <th><b>Quantity</b></th>
        <th><b>Estimated Rate</b></th>
        <th>Actual Rate</th>
        <th><b>Estimated Amount</b></th>
        <th>Actual Amount</th>
        <th>Variation</th>
        <!--<th colspan="2" ><b>Edit Product</b></th>-->
    </tr>
    <tr class="preloaderitems">
        <td colspan="10" align="center">
            <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle">
        </td>
    </tr>
    </thead>
    <tbody id="addedproducts">
    <?php echo $datarows;?>
    </tbody>
</table>
<script>
    function print(activityid)
    {
        newwindow=window.open('<?php echo Yii::app()->request->baseUrl; ?>/projects/Rescostvariation/'+activityid,'Resource','height=500,width=850');
        if (window.focus) {
            newwindow.focus()
        }
        return false;
    }
</script>