<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/iowschedule.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="iowschedule"><a href="#">4. IOW</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-2" style="text-align: left;" id="iowschprojectname">
                </div>
                <div class="col-md-3"><button type="button" class="btn btn-success" id="addiowschworkgroup"><span class="glyphicon glyphicon-plus-sign"></span>Add IOW</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listiowschworkgroup"><span class="glyphicon glyphicon-list-alt"></span>List IOW</button></div>
            </div>
            <div id="iowschworkgrouplist">
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="iowschworkgrouptable" style="display: table;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>IOW</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="iowschworkgroupitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="iowschworkgroupadd" class="row show-grid">
                <form id="addiowschworkgroupform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);">
                        <tbody>
                        <tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="iowschworkgroupname" name="projectname" placeholder="IOW Name"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="saveiowschworkgroup"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal fade " id="Methedologyiowsch" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><span id="iowschmethprojectname"></span></h4>
                        </div>
                        <div class="panel-heading">
                            <div id="iowschitemname">


                            </div>
                        </div>
                        <div class="modal-body" >
                            <div class="iowschpreloader" style="display: none;"><span align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </span></div>
                            <div id="iowschshow"></div>
                            <input type="hidden" id="currentiowsch" value="0">
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-6"></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" id="saveiowsch" data-dismiss="modal">Save</button></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>


                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).on("click",'.viewiowschmethedology',function(){

        var id=$(this).val();

        var name=$(this).attr('data-name');
        $('#iowschitemname').html(name);
        $('#currentiowsch').val(id);
        $.ajax({
            type: 'POST',
            url: '../workgroups/Methedologyiowsch',
            beforeSend : function(){

                $('.iowschpreloader').show();
            },
            dataType: "json",
            data: {id:id},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowschshow').html(data.result);
                    var editor=CKEDITOR.replace( 'iowschmethedology' );
                    editor.config.height = 500;
                    $('#iowschmethprojectname').html(data.project);
                    $('.iowschpreloader').hide();
                }

            }
        });
    });
    $(document).on("click",'#saveiowsch',function(){
        var methedology=CKEDITOR.instances.iowschmethedology.getData();
        var iow=$('#currentiowsch').val();

        $.ajax({
            type: 'POST',
            url: '../workgroups/SaveiowschMethedology',

            dataType: "json",
            data: {methedology:methedology,iow:iow}

        });
    });
</script>