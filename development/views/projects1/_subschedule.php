<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/schedulesubgrps.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Subschedule"><a href="javascript:void(0)">13. Sub Schedule</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addsubschedule"><span class="glyphicon glyphicon-plus-sign"></span>Add Sub Schedule</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listsubschedule"><span class="glyphicon glyphicon-list-alt"></span>List Sub Schedule</button></div>
            </div>
            <div id="subschedulesection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-3">
                        <select id="searchschedulegrp" class="form-control">
                            <option value="none">Select Schedule Group</option>
                            <?php
                           $schedulegrp=Schedule::model()->findAll();
                            foreach($schedulegrp AS $list):
                                echo "<option value='".$list->groupid."'>".$list->name."</option>";
                            endforeach;
                            ?>
                        </select>

                    </div>
                    <!--<div class="col-md-3">
                        <input type="text" placeholder="Search" id="searchacntsubgrps" class="form-control">
                    </div>-->
                    <div class="col-md-3">
                        <button id="subschedulesearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="subscheduletable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Schedule Group</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="subscheduleitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="subscheduleaddsection" class="row show-grid">
                <form id="subscheduleform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="subscheduleadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><select class="form-control" id="addschedulegrp" name="addschedulegrp" >
                                <option value="none">Select Schedule Group</option>
                                <?php
/*                                $schedulegrp=Schedule::model()->findAll();
                                foreach($schedulegrp AS $list):
                                    echo "<option value='".$list->groupid."'>".$list->name."</option>";
                                endforeach;
                                */?>
                            </select><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="subschedulename" name="subschedulename" placeholder="Subschedule Name"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="savesubschedule"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>