<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/accountsubgroups.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Accountsubgroups"><a href="javascript:void(0)">2. Account Subgroups</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3">
                    <button type="button" class="btn btn-success"  id="addacntsubgrps"><span class="glyphicon glyphicon-plus-sign"></span>Add Account Subgroups</button>
                    <!--<a href="<?php /*echo Yii::app()->request->baseUrl; */?>/AccountsSub/Addaccounts"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span>Add Account Subgroups</button></a>-->
                </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listacntsubgrps"><span class="glyphicon glyphicon-list-alt"></span>List Account Subgroups</button></div>
            </div>
            <div id="acntsubgrpslistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-3">
                        <select id="searchacntgrp" class="form-control">
                            <option value="none">Select Account Group</option>
                            <?php
                            $acntgrp=Accountsmaster::model()->findAll();
                            foreach($acntgrp AS $list):
                                echo "<option value='".$list->id."'>".$list->name."</option>";
                            endforeach;
                            ?>
                        </select>

                    </div>
                    <div class="col-md-6">
                        <input type="text" placeholder="Search" id="searchacntsubgrps" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="acntsubgrpssearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="acntsubgrpstable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Account Group</th>
                                <th>Name</th>
                                <th>Type</th>
                                <!--<th>Spread</th>-->
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="acntsubgrpsitems" class="ui-sortable">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="acntsubgrpsaddsection" class="row show-grid">
                <form id="acntsubgrpsform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="accountsubgroupsadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><select class="form-control" id="addacntgrp" name="addacntgrp" >
                                <option value="none">Select Account Group</option>
                                <?php
                                $acntgrp=Accountsmaster::model()->findAll();
                                foreach($acntgrp AS $list):
                                    echo "<option value='".$list->id."'>".$list->name."</option>";
                                endforeach;
                                ?>
                            </select><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="accountsubgroupsname" name="accountsubgroupsname" placeholder="Account Subgroups Name"><span class="error" style="display: none;"></span></th>
                            <th>
                                <select class="form-control" name="type" id="type">
                                    <option value='none'>Select Type</option>
                                    <option value='1'>Cash Inflow</option>
                                    <option value='2'>Cash Outflow</option>
                                </select>
                            </th>
                            <!--<th>
                            	<select class="form-control"  name="spread" id="spread">
                            		<option value="">Select Spread</option>
                            		<option value="0">Begin</option>
                            		<option value="1">In Between</option>
                            		<option value="2">End</option>
                            	</select>
                            </th>-->
                            <th><button type="button" class="btn btn-danger" id="savesubacntgrps"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>