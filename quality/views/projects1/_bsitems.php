<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/bsitems.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="bsitems"><a href="javascript:void(0)">3. BS Items</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3">
                    <button type="button" class="btn btn-success"  id="addbsitem"><span class="glyphicon glyphicon-plus-sign"></span>Add BS Item</button>
                    <!--<a href="<?php /*echo Yii::app()->request->baseUrl; */?>/AccountsSub/Addaccounts"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span>Add Account Subgroups</button></a>-->
                </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listbsitems"><span class="glyphicon glyphicon-list-alt"></span>List BS Items</button></div>
            </div>
            <div id="bsitemslistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-3">
                        <select id="searchacntsubgrp" class="form-control">
                            <option value="none">Select Account SubGroup</option>
                            <?php
                            $acntgrp=AccountsSub::model()->findAll(array('condition'=>'master_id=6','order'=>'sortorder ASC'));
                            foreach($acntgrp AS $list):
                                echo "<option value='".$list->id."'>".$list->name."</option>";
                            endforeach;
                            ?>
                        </select>

                    </div>
                    <div class="col-md-6">
                        <input type="text" placeholder="Search" id="searchbsitems" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="bsitemssearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="bsitemstable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Account Group</th>
                                <th>Name</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="bsitemsitems" class="ui-sortable">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="bsitemsaddsection" class="row show-grid">
                <form id="bsitemsform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="bsitemsadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><select class="form-control" id="choosesubacntgrp" name="subacntgrp" >
                                    <option value="none">Select Account SubGroup</option>
                                    <?php
                                    $acntgrp=AccountsSub::model()->findAll(array('condition'=>'master_id=6','order'=>'sortorder ASC'));
                                    foreach($acntgrp AS $list):
                                        echo "<option value='".$list->id."'>".$list->name."</option>";
                                    endforeach;
                                    ?>
                                </select><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="bsitemname" name="bsitemname" placeholder="BS Item Name"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="savebsitems"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>