<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/projects/projectfunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="project"><a href="#">1. Project</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addproject"><span class="glyphicon glyphicon-plus-sign"></span>Add Projects</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listproject"><span class="glyphicon glyphicon-list-alt"></span>List Projects</button></div>
            </div>
            <div id="projectlistsection">
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="projecttable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Project</th>
                                <th colspan="9"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="projectitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div class="modal fade " id="notesModel" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><span id="notesprojectname"></span></h4>
                        </div>
                        <div class="panel-heading">
                            <!--<div id="notesitems">


                            </div>-->
                        </div>
                        <div class="modal-body" >
                            <div class="preloader" style="display: none;"><span align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </span></div>
                            <div id="notesshow"></div>
                            <input type="hidden" id="currentproj" value="0">
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-6"></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" id="savenotes" data-dismiss="modal">Save</button></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>


                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
            <div id="projectaddsection" class="row show-grid">
                <form id="addprojectform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="resourcevalueadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="projectname" name="projectname" placeholder="Project Name"><span class="error" style="display: none;"></span></th>
                            <!--<th style="font-size:12px;">Sector</th>
                            <th style="width: 16%">
                                <select class="form-control" id="sector" name="sector">
                                    <option value="0">Select Sector</option>
                                    <?php /*$sector=Sector::model()->findAll();
                                    foreach($sector AS $sectors):
                                        echo "<option value='".$sectors->Sector_id."' >".$sectors->Name."</option>";
                                    endforeach;*/?>
                                </select>
                            </th>-->
                            <th style="font-size:12px;">Cash Account</th>
                            <th style="width: 17%">
                                <select class="form-control cashaccount" id="cashaccount" name="cashaccount">
                                <option value="0">Select Account</option>
                                <?php $acnts=AccountsItem::model()->findAll(array('condition'=>'account_type=1','order'=>'name ASC'));
                                foreach($acnts AS $accounts):
                                    echo "<option value='".$accounts->id."' >".$accounts->name."</option>";
                                endforeach;?>
                                </select>
                                <span class="error" style="display: none;"></span>
                            </th>
                            <th style="font-size: 12px;">Bank Account</th>
                            <th style="width: 17%">
                                <select class="form-control bankaccount" id="bankaccount" name="bankaccount">
                                    <option value="0">Select Account</option>
                                    <?php $acnts=AccountsItem::model()->findAll(array('condition'=>'account_type=2','order'=>'name ASC'));
                                    foreach($acnts AS $accounts):
                                        echo "<option value='".$accounts->id."' >".$accounts->name."</option>";
                                    endforeach;?>
                                </select>
                                <span class="error" style="display: none;"></span>
                            </th>
                            <th style="font-size: 12px;">Start Date</th>
                            <th style="width: 10%">
                                <input type="text" class="form-control datepicker startdate" name="startdate" id="startdate" >
                                <span class="error" style="display: none;"></span>
                            </th>
                            <th style="font-size: 12px;">Duration</th>
                            <th style="width: 5%">
                                <input type="text" class="form-control duration" name="duration" id="duration" >
                                <span class="error" style="display: none;"></span>
                            </th>
                            <!--<th>
                                <input type="radio" class="account" name="account" value="1" >Cash
                                <input type="radio" class="account" name="account" value="2" >Bank
                                <span class="error " style="display: none;position: absolute;margin-top: 22px;margin-left: -99px;"><br></span>
                            </th>-->
                            <th><button type="button" class="btn btn-danger" id="saveproject"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).on("click",'.viewnotes',function(){

        var id=$(this).val();

        var name=$(this).attr('data-name');
        $('#notesitems').html(name);
        $('#currentproj').val(id);
        $.ajax({
            type: 'POST',
            url: '../projects/Notes',
            beforeSend : function(){

                $('.preloader').show();
            },
            dataType: "json",
            data: {id:id},
            success: function(data){
                if(data.error=='No')
                {
                    $('#notesshow').html(data.result);
                    $('#notesprojectname').html(data.project);
                }
                else
                {
                    alert(data.errortext);
                }


                $('.preloader').hide();
            }
        });
    });

    $(document).on("click",'#savenotes',function(){

        var notes=$('#notes').val();
        var currentproj=$('#currentproj').val();

        $.ajax({
            type: 'POST',
            url: '../projects/SaveNotes',

            dataType: "json",
            data: {notes:notes,currentproj:currentproj}

        });
    });

    $(document).ready(function() {
        $('#startdate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});

    });
</script>