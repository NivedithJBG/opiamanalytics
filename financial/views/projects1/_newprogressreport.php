<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/newprogressreport.js" type="text/javascript"></script>

<h2 class="acc_trigger" id="newprogressreport"><a href="javascript:void(0)">10. Report</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            
            <div class="row show-grid">
                <div class="col-md-3" style="text-align: left;" id="activityname">
                </div>
                <!-- <div class="col-md-3"><button type="button" class="btn btn-danger" id="all-activities" value="mode"><span class="glyphicon glyphicon-list-alt"></span>List</button></div> -->
            </div>
            
             <div id="progressreportedit">
                <div class="row show-grid">
                    <table class="table table-bordered" id="progressedittable" style="display: table; overflow: hidden;">
                      <tbody>
                       <!-- <tr style="background-color: #f3f3f3;">
                         <td id="activityname"></td>
                        </tr> -->
                        <tr>
                            <th>Start Date</th>
                            <td>
                                <input type="date" class="form-control start-date" id="start-date" value="" name="start-date">
                            </td>
                            <th>End Date</th>
                            <td>
                                <input type="date" class="form-control" name="end-date" id="end-date" value="<?php echo date('Y-m-d');?>" readonly>
                            </td>
                            <th>Total Quantity Completed</th>
                            <td>
                                <input type="text" class="form-control" name="quantity" id="quantity" value="">
                            </td>
                        </tr>
                        <tr>
                            <!-- <th> Actual Duration</th>
                            <td>
                                <input type="text" class="form-control" name="duration" value="" readonly>
                            </td> -->
                            <th colspan="4"></th>
                                <td><button type="button" class="btn btn-danger cancelactivityedit" id="cancelactivityedit" value="">Cancel</button></td>
                                <td><button type="button" class="btn btn-danger saveactivityedit" id="saveactivityedit" value="">Report</button></td>
                        </tr>
                       </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
    
</script>