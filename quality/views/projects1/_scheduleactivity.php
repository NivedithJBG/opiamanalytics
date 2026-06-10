<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/wbsscheduleactivity.js" type="text/javascript"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/ckeditor/ckeditor.js"></script>
<style type="text/css">
    textarea{
        height:500px;
        min-height:500px;
        max-height:500px;
    }
</style>
<h2 class="acc_trigger" id="wbs_scheduleactivity"><a href="#">4. Schedule Activities</a></h2>
<div class="acc_container">
    <input type="hidden" id="selectedScheduleItem">
    <div class="block">
        <div class="jumbotron">
            <div class="modal fade " id="solutionModel" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><span id="modalprojectname"></span></h4>
                        </div>
                        <div class="panel-heading">
                            <div id="itemname">


                            </div>
                        </div>
                        <div class="modal-body" >
                            <div class="preloader" style="display: none;"><span align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </span></div>
                            <div id="methedologyshow"></div>
                            <input type="hidden" id="currentiow" value="0">
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-6"></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" id="savemethedology" data-dismiss="modal">Save</button></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>


                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div class="modal fade " id="specsheetModel" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><span id="modalprojectnamespecsheet"></span></h4>
                        </div>
                        <div class="panel-heading">
                            <div id="itemnamespecshhet">


                            </div>
                        </div>
                        <div class="modal-body" >
                            <div class="preloader" style="display: none;"><span align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </span></div>
                            <div id="specsheetshow"></div>
                            <input type="hidden" id="currentiowspecsheet" value="0">
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-6"></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" id="savespecsheet" data-dismiss="modal">Save</button></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>


                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div class="modal fade " id="checklistModel" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><span id="checklistmodalprojectname"></span></h4>
                        </div>
                        <div class="panel-heading">
                            <div id="itemnamechecklist">


                            </div>
                        </div>
                        <div class="modal-body" >
                            <div class="preloader" style="display: none;"><span align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </span></div>
                            <div id="checklistshow"></div>
                            <input type="hidden" id="currentiowchecklist" value="0">
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-6"></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" id="savechecklist" data-dismiss="modal">Save</button></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>


                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div class="row show-grid">
                <div class="col-md-2" id="projectnamedisplay" style="display:none">

                </div>
                <!--<input type="hidden" id="iowProjectId">-->
                <input type="hidden" id="selectedWorktypeId">
                <div class="col-md-2" id="scheduleitemnamedisplay">
                </div>
                <input type="hidden" id="IOWWorkgroupId2">
                <div class="col-md-2"><button type="button" class="btn btn-success" id="addscheduleact"><span class="glyphicon glyphicon-plus-sign"></span>Add Activity</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="listscheduleact"><span class="glyphicon glyphicon-list-alt"></span>List Activity</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="wbs_schedule_relation_new"><span class="glyphicon glyphicon-list-alt"></span>Relation</button></div>
                <div class="col-md-2"><div id="ganttchartshow1"></div></div>
            </div>
            <div id="scheduleactivitylistsection" >
             <!-- <div class="col-md-2 pull-right"><button type="button" id="saveiowactivitybutton1" class="btn btn-primary saveiowactivitybutton">Save Activities</button></div> -->
                <div class="col-md-6 alert alert-success succmsg" style="display:none; float: none; margin: 0 auto;"></div>
                <div class="row show-grid">
                    <form id="scheduleactivitysaveform">
                    <!--<input type="hidden" id="ProId" name="ProId">
                    <input type="hidden" id="IOWorkgroupId" name="IOWorkgroupId">-->
                        <table class="table table-bordered " id="iowtable" style="display: table;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <!-- <th>Process</th> -->
                                <th>Activity Name</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                                <th>Duration</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <!--<th>Lag</th>-->
                                <th></th>
                                <th></th> 
                                <th></th>
                                <!-- <th>Amount</th> -->
                                <!-- <th colspan="3"></th> -->
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="scheduleactivityitems" class="ui-sortable">

                            </tbody>
                        </table>
                        <div class="col-md-6 alert alert-success succmsg" style="display:none; float: none; margin: 0 auto;"></div>
                        <!-- <div class="col-md-2 pull-right"><button type="button" id="saveiowactivitybutton" class="btn btn-primary pull-right saveiowactivitybutton">Save Activities</button></div> -->
                    </form>
                </div>
            </div>
            <div id="scheduleactaddsection" class="row show-grid">
                <form id="enggactivitiesform" method="post">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);">
                        <tbody>
                          <tr>
                                <th>Activity Name</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                                <th>Duration</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th></th>                                                        
                          </tr> 
                          <tr>
                            <!-- <th>#</th>  -->
                            <!-- <th>
                                <select name="enggprocess" class="form-control" id="scheduleprocess">
                                    <option value="none">Select Process</option>
                                    <option value="2">Project Set-up</option>
                                    <option value="3">Production</option>
                                    <option value="4">Logistic</option>
                                    <option value="5">Construction</option>
                                    <option value="8">Overheads</option>
                                </select>
                                <span class="error" style="display: none;"></span>
                            </th> -->
                            <th><input class="form-control" type="text" id="scheduleactivitiesname" name="scheduleactivitiesname" placeholder="Activity Name"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="scheduleactivitiesunit" name="scheduleactivitiesunit" placeholder="Unit"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="scheduleactivitiesQuantity" name="scheduleactivitiesQuantity" placeholder="Quantity"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="scheduleActivityDuration" name="scheduleActivityDuration" placeholder="Duration"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control date_field" type="date" id="scheduleactivitiesStartDate" name="scheduleactivitiesStartDate" placeholder="Start Date" value="<?php echo date('Y-m-d'); ?>"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control date_field" type="date" id="scheduleactivitiesEndDate" name="scheduleactivitiesEndDate" placeholder="End Date" readonly><span class="error" style="display: none;"></span>
                            <!-- <input type="hidden" id="scheduleActivityDuration" name="scheduleActivityDuration"> -->   
                            </th>
                            <!-- <th><input type='checkbox' value='1' id='estimate' name='estimate' style='visibility: visible;'> Estimate</th> -->
                            <th><button type="button" class="btn btn-danger" id="savescheduleactivities"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>

            <div id="wbsactivityrelation" style="display:none">
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="wbsscheduleitemtable" style="display: table;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Schedule Item</th>
                                <th>Precedent Activity</th>
                                <th>Schedule Item</th>
                                <th>Dependent Activity</th>
                                <th>Relation</th>
                                <th>Lag/Lead</th>
                                <th> </th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="activity_relation_content">

                            </tbody>
                        </table>
                    </form>
                </div>

                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="wbsscheduleitemlidttable" style="display: table;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Schedule Item</th>
                                <th>Precedent Activity</th>
                                <th>Schedule Item</th>
                                <th>Dependent Activity</th>
                                <th>Relation</th>
                                <th>Lag/Lead</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="activity_relation_list">

                            </tbody>
                            <tbody id="structure_relation_list" style="display: none;">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>


    </div>

</div>
<script type="text/javascript">
 $(document).on("click",'.methedologies',function(){

        var id=$(this).val();

        var name=$(this).attr('data-name');
        $('#itemname').html(name);
        $('#currentiow').val(id);
        $.ajax({
            type: 'POST',
            url: '../activity/Methedology',
            beforeSend : function(){

                $('.preloader').show();
            },
            dataType: "json",
            data: {id:id},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#methedologyshow').html(data.result);
                    //$('#modalprojectname').html(data.project);

                    $('#methedologyshow').html(data.result);
                    var editor=CKEDITOR.replace( 'editor2' );
                    editor.config.height = 500;
                    $('#modalprojectname').html(data.project);
                }
                else
                {
                    alert(data.errortext);
                }


                $('.preloader').hide();
            }
        });
    });
       $(document).on("click",'#savemethedology',function(){

            //var methedology=$('#methedology').val();
            var methedology=CKEDITOR.instances.editor2.getData();
            var estimateid=$('#currentiow').val();

            $.ajax({
                type: 'POST',
                url: '../activity/SaveMethedology',

                dataType: "json",
                data: {methedology:methedology,estimateid:estimateid,},
                /*success: function(data){
                    if(data.error=='No')
                    {
                        $('#methedologyshow').html(data.result);
                        $('#modalprojectname').html(data.project);
                    }
                    else
                    {
                        alert(data.errortext);
                    }


                    $('.preloader').hide();
                }*/
            });
        });
        $(document).on("click",'.specsheets',function(){

            var id=$(this).val();

            var name=$(this).attr('data-name');
            $('#itemnamespecshhet').html(name);
            $('#currentiowspecsheet').val(id);
            $.ajax({
                type: 'POST',
                url: '../activity/Specsheet',
                beforeSend : function(){

                    $('.preloader').show();
                },
                dataType: "json",
                data: {id:id},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#specsheetshow').html(data.result);
                        $('#modalprojectnamespecsheet').html(data.project);
                    }
                    else
                    {
                        alert(data.errortext);
                    }


                    $('.preloader').hide();
                }
            });
        });
           $(document).on("click",'#savespecsheet',function(){

            var methedology=$('#specsheettext').val();
            var estimateid=$('#currentiowspecsheet').val();

            $.ajax({
                type: 'POST',
                url: '../activity/SaveSpecsheet',

                dataType: "json",
                data: {methedology:methedology,estimateid:estimateid,},
                /*success: function(data){
                 if(data.error=='No')
                 {
                 $('#methedologyshow').html(data.result);
                 $('#modalprojectname').html(data.project);
                 }
                 else
                 {
                 alert(data.errortext);
                 }


                 $('.preloader').hide();
                 }*/
            });
        });
         $(document).on("click",'.checklist',function(){

        var id=$(this).val();

        var name=$(this).attr('data-name');
        $('#itemnamechecklist').html(name);
        $('#checklistshow').val(id);
        $.ajax({
            type: 'POST',
            url: '../activity/Checklist',
            beforeSend : function(){

                $('.preloader').show();
            },
            dataType: "json",
            data: {id:id},
            success: function(data){
                if(data.error=='No')
                {
                    $('#checklistshow').html(data.result);
                    var editor=CKEDITOR.replace( 'editor1' );
                    editor.config.height = 500;
                    $('#checklistmodalprojectname').html(data.project);
                }
                else
                {
                    alert(data.errortext);
                }


                $('.preloader').hide();
            }
        });
    });
    $(document).on("click",'#savechecklist',function(){

        var methedology=CKEDITOR.instances.editor1.getData();
        var estimate=$('#checklistshow').val();

        $.ajax({
            type: 'POST',
            url: '../activity/SaveChecklist',

            dataType: "json",
            data: {methedology:methedology,estimateid:estimate,},

        });
    });
    
    $('#scheduleactivitiesEndDate').change(function(){
        var startDate = $('#scheduleactivitiesStartDate').val();
        var endDate = $('#scheduleactivitiesEndDate').val();
        // console.log(endDate);
        if(endDate != '' && startDate != '')
        {
            var startDate1 = Date.parse(startDate);
            var endDate1 = Date.parse(endDate);
            var timeDiff = endDate1 - startDate1;
            daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
            console.log(daysDiff);
            $('#scheduleActivityDuration').val(daysDiff+1);
        }
    });
    
    $('#scheduleActivityDuration').change(function(){
        var today = new Date();
        var tempstart = (today.getDate());
        if (tempstart < 10) tempstart = '0' + tempstart;
        var tempMonth = (today.getMonth() + 1);
        if (tempMonth < 10) tempMonth = '0' + tempMonth;
        var startDate = today.getFullYear() + '-' + tempMonth + '-' + tempstart;
        var duration = $('#scheduleActivityDuration').val();
        //var endDate = $('#scheduleactivitiesEndDate').val();
        // console.log(endDate);
        if(duration != '' && startDate != '')
        {
            var newdate = new Date(today).setDate(today.getDate() + (+duration) - 1);
            var endDate1 = new Date(newdate);
            var tempoMonth = (endDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (endDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var endDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
            
            $('#scheduleactivitiesStartDate').val(startDate);
            $('#scheduleactivitiesEndDate').val(endDate);
        }
    });
    
    $('#scheduleactivitiesStartDate').change(function(){
       var startDate = new Date($('#scheduleactivitiesStartDate').val());
       var duration = $('#scheduleActivityDuration').val();
       if(duration != '' && startDate != '')
        {
            var newdate = new Date(startDate).setDate(startDate.getDate() + (+duration) - 1);
            var endDate1 = new Date(newdate);
            var tempoMonth = (endDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (endDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var endDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
            
            $('#scheduleactivitiesEndDate').val(endDate);
        }
    });
    
    $( "#scheduleactivityitems" ).sortable({
        items: '.no',
        update:function( event, ui ) {
            //alert($(this).index());
            var updatedrows=[];
            $(this).closest('table').find('tbody tr.ui-state-default').each(function (i) {
                var rowid=$(this).attr('data-id');
                var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    rowindex:rowindex
                })
            });
            $.ajax({
                type: 'POST',
                url: '../projects1/updateganttsort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection()

</script>