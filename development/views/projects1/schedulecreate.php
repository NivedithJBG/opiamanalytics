<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/schedulefunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="scheduleitems"><a href="#">Schedule List</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div id="iowaddsection" class="row show-grid">
                <form method="POST" action="" id="productform">
                    <table cellpadding="0" cellspacing="0 " id="activitytable">
                        <thead>
                        <tr>
                            <td><?php echo '<b>Item Of Work</b> : '.$iow->Name;?></td>
                            <td><?php echo '<b>Unit</b> : '.$iow->Unit;?></td>
                            <td><?php echo '<b>Quantity</b> : '.$iow->Quantity;?></td>
                        </tr>
                        <tr>
                            <td style="width: 40%;"><b>Activity</b></td>
                            <td style="width: 25%;"><b>Budgeted Duration(Days)</b></td>
                            <td style="width: 25%;"><b>Actual Duration(Days)</b></td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($activities AS $activity):?>
                            <tr>
                                <td><?php echo $activity['Name'];?></td>
                                <td>
                                    <input type="hidden" name="activityid[]" value="<?php echo $activity['Activity_Id'];?>">
                                    <input type="text" class="form-control bduration" name="bduration[]" value="<?php echo $activity['Budgeted_Duration'];?>"><span class="error"></span></td>
                                <td><input type="text" class="form-control eduration" name="eduration[]" value="<?php echo $activity['End_Duration'];?>"><span class="error"></span></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>


                    <table cellpadding="0" cellspacing="0" style="margin-top: 15px;">
                        <tbody>
                        <tr>
                            <th>Number of Resource units <input type="hidden" name="iowid" value="<?php echo $iow->IOW_Id;?>"> <input type="text" class="form-control" name="resourceunit" id="resourceunit" value="<?php echo $iow->Resourceunits;?>"><span class="error"></span></th>
                            <th>Work hours <input type="text" class="form-control" name="workhours" id="workhours" value="<?php echo $iow->Workhours;?>"><span class="error"></span></th>
                            <th>Number of Cycles<input type="text" class="form-control" name="cycles" id="cycles" value="<?php echo $iow->Cycles;?>"><span class="error"></span></th>
                        </tr>
                        </tbody>


                    </table>

                    <div style="margin-top:10px;"><button type="submit" class="btn btn-primary" id="saveschedule" name="saveschedule"><span class="glyphicon glyphicon-saved"></span>Save</button></div>
                </form>



            </div>


        </div>


    </div>

</div>