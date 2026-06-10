<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/accountschedule.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Accountschedules"><a href="javascript:void(0)">11. Account Schedules</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3">
                    <button type="button" class="btn btn-success"  id="addacntschedules"><span class="glyphicon glyphicon-plus-sign"></span>Add Account Schedules</button>
                    <!--<a href="<?php /*echo Yii::app()->request->baseUrl; */?>/AccountsSub/Addaccounts"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span>Add Account Schedules</button></a>-->
                </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listacntschedules"><span class="glyphicon glyphicon-list-alt"></span>List Account Schedules</button></div>
            </div>
            <div id="acntscheduleslistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-3">
                        <select id="searchschedacntgrp" class="form-control">
                            <option value="none">Select Account Group</option>
                            <?php
                            $acntgrp=Accountsmaster::model()->findAll();
                            foreach($acntgrp AS $list):
                                echo "<option value='".$list->id."'>".$list->name."</option>";
                            endforeach;
                            ?>
                        </select>

                    </div>
                    <div class="col-md-3">
                        <select id="searchacntsubgrp" class="form-control">
                            <option value="">Select Account Sub-Group</option>                            
                        </select>

                    </div>                    
                    <div class="col-md-3">
                        <input type="text" placeholder="Search" id="searchacntschedules" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="acntschedulessearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="acntschedulestable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Account Group</th>
                                <th>Account Sub-Group</th>
                                <th>Name</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="acntschedulesitems" class="ui-sortable">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="acntschedulesaddsection" class="row show-grid">
                <form id="acntschedulesform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="accountschedulesadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><select class="form-control" id="addacntgrpschedule" name="addacntgrpschedule" >
                                <option value="none">Select Account Group</option>
                                <?php
                                $acntgrp=Accountsmaster::model()->findAll();
                                foreach($acntgrp AS $list):
                                    echo "<option value='".$list->id."'>".$list->name."</option>";
                                endforeach;
                                ?>
                            </select><span class="error" style="display: none;"></span></th>
                            <th><select class="form-control" id="addacntsubgrpsch" name="addacntsubgrpsch" >
                                <option value="">Select Account Sub-Group</option>
                            </select><span class="error" style="display: none;"></span></th>                            
                            <th><input class="form-control" type="text" id="accountschedulesname" name="accountschedulesname" placeholder="Account Schedule Name"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="saveaccountschedule"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>