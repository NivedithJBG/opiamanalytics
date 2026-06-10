<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/financeschedule.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="schedule"><a href="javascript:void(0)">15. Schedule</a></h2>
    <div class="acc_container">
        <div class="block">
            <div class="jumbotron">
                <div class="row show-grid">
                    <div class="col-md-3">
                        <select class="form-control" id="scheduleprjct" name="scheduleprjct">
                            <option value="0">Select Project</option>
                            <?php
                                $project=Projects::model()->findAll(array('condition'=>'Status=0 AND Project_Delete_Status=0'));
                                foreach($project AS $list):
                                    echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                                endforeach;
                            ?>
                        </select>
                        <span class='error' style="float: left;"></span>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control schaccnthead" id="schaccnthead" name="schaccnthead">
                            <option value="0">Select Account Head</option>
                            <?php $acnts=AccountsItem::model()->findAll(array('condition'=>'schedule!=0'));
                            foreach($acnts AS $accounts):
                                echo "<option value='".$accounts->id."'>".$accounts->name."</option>";
                            endforeach;?>
                        </select>
                        <span class='error' style="float: left;"></span>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="scheduleitem" name="scheduleitem">
                            <option value="0">Select Schedule Item</option>
                        </select>
                        <span class='error' style="float: left;"></span>
                    </div>
                    <div class="col-md-3">
                        <button id="schedulesearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div id="schedulesection" style="display: none">
                    <div class="row show-grid">
                        <form>
                            <div id="accountname" class="col-md-10">

                            </div>
                            <div class="col-md-2" id="printschedule" style="padding-top: 20px;"></div>
                            <div class="row">
                                <table class="table table-bordered" id="scheduletable" style="display: table; overflow: hidden;">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Narration</th>
                                            <th >Debit Amount</th>
                                            <th >Credit Amount</th>
                                        </tr>
                                        <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                    </thead>
                                    <tbody id="scheduleitems">

                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

