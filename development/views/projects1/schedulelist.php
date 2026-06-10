<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/schedulefunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="scheduleitems"><a href="#">Schedule List</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div id="iowaddsection" class="row show-grid">
                <div class="row show-grid">

                    <div class="col-md-2">
                        <?php echo $project->Name;?>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="scheduleworkgroups" id="scheduleworkgroups">

                            <?php foreach($workgroups AS $work):
                                if(isset($_GET['workgroup'])):
                                    if($work['Workgroup_Id']==$_GET['workgroup']):
                                        $selected='selected';
                                    else:
                                        $selected='';
                                    endif;
                                endif;
                            ?>
                                <option value="<?php echo $work['Workgroup_Id'];?>" <?php echo $selected;?> ><?php echo $work['Name'];?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <form method="POST" action="" id="productform">

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>

                                <th>#</th>
                                <th>Item of work name</th>
                                <th>Activity Status</th>
                                <th></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="schedulelist">
                            <tr><td colspan="4" style="text-align: center">Select Work Group</td></tr>
                            </tbody>
                        </table>
                    </div>



                </form>



            </div>


        </div>


    </div>

</div>