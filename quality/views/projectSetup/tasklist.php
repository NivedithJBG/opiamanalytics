<?php

$addedones='';
foreach($resourcesadded AS $key=>$data):
    $price=$price+$data['Price'];
    $addedones.="<tr id='tempresrow".$data['COR_Id']."'><td>".Resourcetype::model()->findByPk( $data['ResourceType_Id'])->Name."</td><td>".$data['resource']."</td><td style='width: 20%'>".$data['Unit']."</td><td style='width: 20%'><input type='hidden' name='resourceid[]' value='".$data['COR_Id']."'><input type='text' data-id='".$data['COR_Id']."' class='form-control resourceqty' name='resourceqty[]' id='quantity".$data['COR_Id']."' value='".$data['COR_Qunatity']."' ><span class='error'></span></td><td style='width: 20%'><input type='text' data-id='".$data['COR_Id']."' class='form-control resourcerate' name='resourcerate[]' id='rate".$data['COR_Id']."' value='".$data['COR_Actual_Price']."' ></td><td class='resource-amount' id='amount".$data['COR_Id']."'><input type='hidden' name='resourceamount[]' value='".$data['COR_Price']."'>".$data['COR_Price']."</td>
                                <td class='small75'><button type='button' class='btn btn-primary removeresourceitem' id='removeresourceitem".$data['COR_Id']."' value='".$data['COR_Id']."' title='Remove Item'><span >Remove</span></button></td>
                            </tr>";

endforeach; 
?>
<button type="button" value="Back" name="goback" title="back" class="btn btn-primary" style="float: right;width: 100px" onclick="goBack()">Back</button>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/tasklist.js" type="text/javascript"></script>

<h1>Activity  Tasks</h1>

<script>
    function goBack()
    {
        window.history.back()
    }
</script>
<form method="POST" action="" id="productform">
    <table class="table table-bordered"  >
        <thead>
        <tr>
           <input type="hidden" class="form-control" placeholder="ProjectSetup Id" name="PS_Id" id="PS_Id" value="<?php echo $model->PS_Id;?>" >
            <th style="width: 50%"><span class="headings">Activity Name</span><input type="text" class="form-control" placeholder="Project Setup Name" name="PS_Name" id="Name" readonly="readonly" value="<?php echo $model->PS_Name;?>" ><span class='error'></span></th>
            <th style="width: 20%"><span class="headings">Unit</span><input type="text" class="form-control" placeholder="Unite" name="ProjectSetup_Unit" id="Unit" readonly="readonly" value="<?php echo $model->PS_Unit;?>" ><span class='error'></span></th>
            <th style="width: 20%"><span class="headings">Rate</span><input type="text" class="form-control" placeholder="Rate"  id="investmentratetotal" name="PS_Rate" readonly="readonly"  value="<?php echo $model->PS_Price;?>"><span class='error'></span></th>
            <!--<th style="width: 10%"><button type="submit" class="btn btn-primary" id="updateproduct" value="1" name="Product_saveproduct" title='Save Product'>Save</button></th>-->
        </tr>
        </thead>
    </table>
</form>
<input type="hidden" id="Consumables_Id" name="Consumables_Id" value="<?php echo $model->PS_Id;?>">
<input type="hidden" id="pageaction" value="update">

<div class="acc_container">
    <div class="block">
        <div class="jumbotron"> 
            <!--buttons to add and list tasks-->
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addtask_prosetup"><span class="glyphicon glyphicon-plus-sign"></span>Add Task</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listtask_prosetup"><span class="glyphicon glyphicon-list-alt"></span>List Task</button></div>
            </div>
            <div id="tasklistsection">
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="tasktable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>Task Id</th>
                                <th>Task</th>
                                <th colspan="7"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="taskitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="taskaddsection" class="row show-grid">
                <form id="addtaskform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="resourcevalueadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="taskname" name="taskname" placeholder="Task Name"><span class="error" style="display: none;"></span></th>
                           
                            <th><button type="button" class="btn btn-danger" id="savetask_prosetup"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>