<h1>Project : <?php echo $project->Name;?></h1>
<table class="table table-bordered" id="costvariationtable">
    <thead>
    <tr>
        <th><b>Resources</b></th>
        <th><b>Rate Variance</b></th>
        <th><b>Quantity Variance</b></th>
        <th><b>Cost Variance</b></th>
    </tr>
    <tr class="preloaderitems">
        <td colspan="4" align="center">
            <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle">
        </td>
    </tr>
    </thead>
    <tbody id="addedproducts">
    <?php echo $datarows;?>
    </tbody>
</table>