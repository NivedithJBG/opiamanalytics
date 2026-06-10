<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/iowfunction.js" type="text/javascript"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/ckeditor/ckeditor.js"></script>
<style type="text/css">
    textarea{


        height:500px;
        min-height:500px;
        max-height:500px;

    }
</style>

<h2 class="acc_trigger" id="itemofwork"><a href="#">3. Items Of Work</a></h2>
<div class="acc_container">
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

                <div class="col-md-2" id="projectnamedisplay">

                </div>
                <input type="hidden" id="iowProjectId">
                <div class="col-md-2" id="workgroupnamedisplay">

                </div>
                <input type="hidden" id="IOWWorkgroupId">
                <div class="col-md-2"><button type="button" class="btn btn-success" id="addiow"><span class="glyphicon glyphicon-plus-sign"></span>Add IOW</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="listiow"><span class="glyphicon glyphicon-list-alt"></span>List IOW</button></div>

            </div>
            <div id="iowlistsection" >

                <div class="row show-grid">
                    <form>
                        <table class="table table-bordered " id="iowtable" style="display: table;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Item of works</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>

                                </th>
                                <th></th>

                                <th>Edit</th>
                                <th >Delete</th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="12" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="iowitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="iowaddsection" class="row show-grid">
                <form method="POST" action="" id="addiowform">
                    <div class="table-responsive">
                        <table class="table table-bordered"  style="display: table;">
                            <thead>
                            <tr>
                                <th>Item of work name</th>
                                <th>Unit</th>
                                <th colspan="2">Quantity</th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="4" align="center"><img src="/geotech/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody ><tr >
                                <td class="small75"><input type="text" class="form-control" placeholder="IOW Name" id="IOW_Name" name="IOW_Name"><span class="error" style="display: none;"></span></td>
                                <td class="small75"><input type="text" class="form-control" placeholder="IOW Unit" id="IOW_Unit" name="IOW_Unit"><span class="error" style="display: none;"></span></td>
                                <td class="small75"><input type="text" class="form-control" placeholder="IOW Quantity" id="IOW_Quantity" name="IOW_Quantity"><span class="error" style="display: none;"></span></td>
                                <td class="small75"><button type="button" class="btn btn-primary saveiow"  id="saveiow" > <span class="glyphicon glyphicon-saved"></span>Save</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>


    </div>

</div>
<script type="text/javascript">
    $(document).ready(function() {

    });
        $(document).on("click",'.methedologies',function(){

        var id=$(this).val();

        var name=$(this).attr('data-name');
        $('#itemname').html(name);
        $('#currentiow').val(id);
        $.ajax({
            type: 'POST',
            url: '../itemofworks/Methedology',
            beforeSend : function(){

                $('.preloader').show();
            },
            dataType: "json",
            data: {id:id},
            success: function(data){
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
            }
        });
    });
        $(document).on("click",'#savemethedology',function(){

            var methedology=$('#methedology').val();
            var iow=$('#currentiow').val();

            $.ajax({
                type: 'POST',
                url: '../itemofworks/SaveMethedology',

                dataType: "json",
                data: {methedology:methedology,iow:iow,},
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
                url: '../itemofworks/Specsheet',
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
            var iow=$('#currentiowspecsheet').val();

            $.ajax({
                type: 'POST',
                url: '../itemofworks/SaveSpecsheet',

                dataType: "json",
                data: {methedology:methedology,iow:iow,},
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
            url: '../itemofworks/Checklist',
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
        var iow=$('#checklistshow').val();

        $.ajax({
            type: 'POST',
            url: '../itemofworks/SaveChecklist',

            dataType: "json",
            data: {methedology:methedology,iow:iow,},

        });
    });


</script>