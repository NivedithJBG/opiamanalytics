<h1>Project :<?php echo $project->Name; ?></h1>
<h3 class="pull-left">Per Unit Rate Variation</h3>
<table class="table table-bordered" id="variationtable">
    <thead>
    <tr>
        <th><b>Resource Name</b></th>
        <th><b>Estimated Amount</b></th>
        <th>Amount Spend</th>
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
        <?php foreach ($dataProvider AS $key => $data):
            $resourcedetails=$this->getresourcedetails($data['project_estimate_Id']);
            //echo ($resourcedetails);exit;
            $estamount=$data['Price'] / $data['Quantity'];
            $variation=$resourcedetails - $estamount;
            if($variation>0):
                $excess=$variation;
                $tospent='';
            else:
                $excess='';
                $tospent=abs($variation);
            endif;
            ?>
                <?php echo $resourcedetails;?>
            <!--<tr>
                <td>
                    <?php /*echo $process;*/?>
                    <input type="hidden" value="<?php /*echo $data['Resource_group_Id'];*/?>" name="process">
                </td>
                <td>
                    <?php /*echo $processitem;*/?>
                    <input type="hidden" value="<?php /*echo $data['project_estimate_Id'];*/?>" name="processitem">
                </td>
                <td><?php /*echo number_format($estamount,2);*/?></td>
                <td><?php /*echo number_format($resourcedetails,2);*/?></td>
                <td><?php /*echo number_format($excess,2);*/?></td>
                <td><?php /*echo number_format($tospent,2);*/?></td>
            </tr>-->
        <?php endforeach;?>
    </tbody>
</table>