<!--<script src="<?php /*echo Yii::app()->request->baseUrl; */?>/jsnew/estimate.js" type="text/javascript"></script>-->
<h2 class="acc_trigger" id="estimate"><a href="javascript:void(0)">7. Estimate</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div id="estimatesection">
                <form method="POST" action="" id="productform">
                    <input type="hidden" id="Project_Id" name="Project_Id" value="<?php echo $model->Project_Id?>">
                    <table class="table table-bordered">
                        <thead>
                        <tr style="background-color: ghostwhite">
                            <td>
                                <span class="headings"><b>Project Name </b><h4><?php echo $project->Name; ?></h4></span>
                                <input type="hidden" value="<?php echo $project->Project_Id; ?>" name="projectid" id="projectid">
                            </td>
                            <td>
                                <span class="headings"><b>Quote Amount</b></span>
                                <input type="text" value="" name="qouteamount" id="qouteamount" class="form-control">
                            </td>
                            <!--<th style="width: 150px;">
                    <div class="hover" data-tooltip="tooltip"><h3 style="text-align: center">View Activities</h3>

                        <div class="tooltiactivity" id="tooltip">
                          <table cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <th>Activities</th>
                                </tr><?php /*echo $datarows; */?></table>
                </th>-->
                            <!--<th style="width: 10%"><div class="tooltip" style="border: 1px solid rgb(43, 176, 215); opacity: 0; display: none; background: rgb(159, 218, 238);"><?php /*echo $datarows;*/ ?></div><span class="headings"><label  id="showtooltop">View Activities</label></span></th>-->
                            <td>
                                <button type="button" class="btn btn-primary" id="saveproduct" value="1" name="Product_saveproduct">
                                    Save
                                </button>
                            </td>
                        </tr>
                        </thead>
                    </table>

                    <table class="table table-bordered" id="estimatetable">
                        <thead>
                        <!--<tr>
                            <th colspan="9"><span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center">Added Items</span>
                            </th>
                        </tr>-->
                        <tr>
                            <th><b>Process</b></th>
                            <th><input type="hidden" id="pageaction" value="create"><b>Activity</b></th>
                            <th><b>Unit</b></th>
                            <th class="small75"><b>Quantity</b></th>
                            <th class="small75"><b>Specific Rate</b></th>
                            <th class="small75"><b>Amount</b></th>
                            <th colspan="2"></th>
                            <th></th>
                            <!--<th colspan="2" ><b>Edit Product</b></th>-->
                        </tr>
                        <tr class="preloaderitems">
                            <td colspan="9" align="center">
                                <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle">
                            </td>
                        </tr>
                        </thead>
                        <tbody id="addedproducts" class="ui-sortable">
                        <?php
                        $pricesum = 0;

                        $projsetup_price_sum = 0;
                        $production_price_sum = 0;
                        $logistics_price_sum = 0;
                        $construction_price_sum = 0;
                        $overheads_price_sum = 0;

                        //checking for processes
                        $process2 = 0;
                        $process3 =0;
                        $process4 =0;
                        $process5 =0;
                        $process8 =0;


                        foreach ($dataProvider AS $key => $data):
                            if($data['Resource_group_Id']==2):
                                $process="Project Setup";
                            elseif($data['Resource_group_Id']==3):
                                $process="Production";
                            elseif($data['Resource_group_Id']==4):
                                $process="Logistics";
                            elseif($data['Resource_group_Id']==5):
                                $process="Construction";
                            elseif($data['Resource_group_Id']==8):
                                $process="Overheads";
                            endif;
                            echo "<tr id='tempprodrow' style='background: white;'><td></td><th colspan='8' style='font-size: 16px;background: white;'>".$process."</th></tr>";
                            //echo $data['activity_name'];exit;
                            // echo ResourceGroup::model()->findByPk($data['Resource_group_Id'])->Resource_group_Name;

                            $connection = CActiveRecord::getDbConnection();
                            $sqltemvals1 = "SELECT * FROM project_estimate WHERE Project_Id=' $id ' AND Resource_group_Id ='".$data['Resource_group_Id']."' ORDER BY sortorder ASC";
                            //echo $sqltemvals1;exit;
                            $command = $connection->createCommand($sqltemvals1);
                            $dataReader1 = $command->query();
                            $dataProvider1 = $dataReader1->readAll();
                            $grouptot=0;
                            foreach($dataProvider1 AS $data1){
                                echo "<tr class='ui-state-default no' id='tempprodrow" . $data1['project_estimate_Id'] . "' data-id='" . $data1['project_estimate_Id'] . "'>
                        ";
                                if($data1['Resource_group_Id']==2):
                                    $process2++;
                                    //sum of project setup amounts
                                    $projsetup_price_sum += $data1['Price'];
                                    // $prosetid[]=$data['Item_Id']
                                    echo "<td>Project Setup</td>";
                                    $projset= ProjectSetup::model()->findByPk($data1['Item_Id']);
                                    if($data1['activity_name']!=''){
                                        echo "<td>" . $data1['activity_name']. "</td>";
                                    }else{
                                        echo "<td>" . $projset->PS_Name. "</td>";
                                    }
                                    echo "<td>" . $projset->PS_Unit. "</td>";
                                elseif($data1['Resource_group_Id']==3):
                                    $process3++;
                                    //    sum of product act amounts
                                    $production_price_sum += $data1['Price'];

                                    echo "<td>Production</td>";
                                    $prod= Products::model()->findByPk($data1['Item_Id']);

                                    if($data1['activity_name']!=''){
                                        echo "<td>" . $data1['activity_name']. "</td>";
                                    }else{
                                        echo "<td>" . $prod->Name. "</td>";
                                    }
                                    echo "<td>" . $prod->Unit. "</td>";
                                elseif($data['Resource_group_Id']==4):
                                    $process4++;
                                    // sum of logistics amounts
                                    $logistics_price_sum +=$data1['Price'];

                                    echo "<td>Logistics</td>";
                                    $logi= Logistics::model()->findByPk($data1['Item_Id']);
                                    if($data1['activity_name']!=''){
                                        echo "<td>" . $data1['activity_name']. "</td>";
                                    }else{
                                        echo "<td>" . $logi->Name. "</td>";
                                    }
                                    echo "<td>" . $logi->Unit. "</td>";
                                elseif($data1['Resource_group_Id']==5):
                                    $process5++;
                                    // sum of constructions amounts
                                    $construction_price_sum +=$data1['Price'];

                                    echo "<td>Construction</td>";
                                    $constr= Construction::model()->findByPk($data1['Item_Id']);
                                    if($data1['activity_name']!=''){
                                        echo "<td>" . $data1['activity_name']. "</td>";
                                    }else{
                                        echo "<td>" . $constr->CO_Name. "</td>";
                                    }
                                    echo "<td>" . $constr->CO_Unit. "</td>";

                                elseif($data['Resource_group_Id']==8):
                                    $process8++;
                                    $overheads_price_sum += $data['Price'];

                                    echo "<td>Overheads</td>";
                                    $over= Overheads::model()->findByPk($data1['Item_Id']);
                                    if($data1['activity_name']!=''){
                                        echo "<td>" . $data1['activity_name']. "</td>";
                                    }else{
                                        echo "<td>" . $over->Name. "</td>";
                                    }
                                    echo "<td>" . $over->Unit. "</td>";
                                else:
                                    echo "<td></td><td></td><td></td>";
                                endif;
                                echo " <td ><input type='hidden' name='itemid[]' value='" . $data1['project_estimate_Id'] . "'>
                        <input type='text' data-type='" . $data1['type'] . "' data-id='" . $data1['project_estimate_Id'] . "' class='form-control quantity' name='quantity[]' id='" . $data1['type'] . "quantity" . $data1['project_estimate_Id'] . "' value='" . $data1['Quantity'] . "' >
                        <span class='error'></span>
                        <input type='hidden' value='" . $data1['specific_rate'] . "' id='" . $data1['type'] . "rate" . $data1['project_estimate_Id'] . "' data-id='" . $data1['project_estimate_Id'] . "' name='rate[]'>
                        </td>";

                                echo "<td ><input type='text' readonly='readonly' data-type='" . $data1['type'] . "' data-id='" . $data1['project_estimate_Id'] . "' class='form-control specrate amount' name='specrate[]' id='" . $data1['type'] . "specrate" . $data1['project_estimate_Id'] . "' value='" . $data1['specific_rate'] . "' >
                      <span class='error'></span></td>";

                                echo "<td class='amount'>
                <span id='" . $data1['type'] . "amount" . $data1['project_estimate_Id'] . "'>" . number_format($data1['Price'],2) . "</span>
                <input type='hidden' value='" . $data1['Price'] . "' class='iowamount' id='amount" . $data1['project_estimate_Id'] . "'></td>";

                                echo "<td colspan='2'><a href='" . Yii::app()->request->baseUrl . "/EstimateProject/UpdateResources?id=" . $data1['Item_Id'] . "&Project_Id=" . $project->Project_Id . "&project_estimate_Id=" . $data1['project_estimate_Id'] . "&ResourceGrop=" . $data1['Resource_group_Id'] . "'><button type='button' class='btn btn-primary ' id='editiowresources" . $data1['project_estimate_Id'] . "' value='" . $data1['project_estimate_Id'] . "'>View / Modify</button></a></td>";

                                echo "<td><button type='button' class='btn btn-primary removeiowitem' id='removeiowitem" . $data1['project_estimate_Id'] . "' value='" . $data1['project_estimate_Id'] . "'>Remove</button></td>

                        </tr>";
                                $grouptot = $grouptot + $data1['Price'];
                            }


                            echo "<tr><th></th><th colspan='4'>Group Total</th><th  id='totalcost' style='text-align: right'>" . number_format($grouptot,2) . "</th><th colspan='2'></th><th colspan='2'></th></tr>";
                            $pricesum = $pricesum + $grouptot;
                        endforeach;



                        echo "<tr style='font-size:16px;'><th></th><th colspan='4'>Total Cost</th><th id='totalcost' style='text-align: right'>" . number_format($pricesum,2) . "</th><th colspan='2'></th><th colspan='2'></th></tr>
                              <tr><th colspan='8'></th>
                              <th><a href='" . Yii::app()->request->baseUrl . '/EstimateProject/Export/' . $model->Project_Id . "' target='_blank'>
                              <button type='button'  class='btn btn-primary' value='" . $model->Project_Id . "' title='Export'>Export</button></a></th></tr>";
                        ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>