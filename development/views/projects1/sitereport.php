<h1>Project :<?php echo $project->Name; ?></h1>
<h3 class="pull-left">Per Unit Quantity Variation</h3>
<table class="table table-bordered" id="variationtable">
    <thead>
    <tr>
        <th><b>Resource Name</b></th>
        <th><b>Estimated Quantity</b></th>
        <th>Quantity Consumed</th>
        <th>Excess</th>
        <th>To be spent</th>
        <!--<th colspan="2" ><b>Edit Product</b></th>-->
    </tr>
    <tr class="preloaderitems">
        <td colspan="6" align="center">
            <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle">
        </td>
    </tr>
    </thead>
    <tbody>
    <?php echo $datarows;?>
    </tbody>
</table>