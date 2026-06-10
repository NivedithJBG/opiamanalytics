<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/activitiesfunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Costactivities"><a href="#Costactivities">5. Activities</a></h2>
    <div class="acc_container">
        <div class="block">
            <div class="jumbotron">
                <div class="row show-grid">
                    <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/activities/create"><button type="button" class="btn btn-success"  id="addcostactivities"><span class="glyphicon glyphicon-plus-sign"></span>Add Activity</button></a> </div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger" id="listcostactivities"><span class="glyphicon glyphicon-list-alt"></span>List Activity</button></div>
                </div>
                <div id="costactivitieslist">
                    <div id="searchdiv" class="row show-grid" style="display: block;">
                        <div class="col-md-3">
                            <select class="form-control" name="activitygrp" id="activitygrp">
                                <option value="0">Select Activity Group</option>
                                <?php $group=ActivityGroup::model()->findAll();
                                foreach($group AS $activitygrp):?>
                                    <option value="<?php echo $activitygrp['group_id']?>"><?php echo $activitygrp['name']?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="text" placeholder="Search" id="searchcostactivitiesname" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <button id="costactivitiessearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <!--Table-->
                        <form>
                            <table class="table table-bordered" id="costactivitiestable" style="display: table; overflow: hidden;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Activity</th>
                                    <th>Unit</th>
                                    <th>Rate</th>

                                    <th colspan="2"></th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="costactivitiesitems">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>