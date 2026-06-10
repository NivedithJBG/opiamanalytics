<h1>Project : <?php echo $project->Name;?></h1>
<h3 class="pull-left"><?php echo $activityname;?></h3>
<table class="table table-bordered" id="costvariationtable">
    <thead>
        <tr>
            <th><b>Sl No</b></th>
            <th><b>Resources</b></th>
            <th><b>Unit</b></th>
            <th><b>E.Rate</b></th>
            <th><b>A.Rate</b></th>
            <th><b>E.Quantity</b></th>
            <th><b>A.Quantity</b></th>
            <th><b>E.Amount</b></th>
            <th>A.Amount</th>
            <th>Variation</th>
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
<script type="text/javascript">
    function printres(resid,prodid)
    {
        newwindow=window.open('<?php echo Yii::app()->request->baseUrl; ?>/projects/Resourcecostvariance?resid='+resid+'&prodid='+prodid,'Resource Cost Variance','height=500,width=850');

        if (window.focus) {
            newwindow.focus()
        }
        return false;
    }
</script>
