<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/accounts.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Accounts"><a href="#Accounts">4. Account Heads</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3">
                    <a href="<?php echo Yii::app()->request->baseUrl; ?>/AccountsItem/create"><button type="button" class="btn btn-success"  id="addaccounts"><span class="glyphicon glyphicon-plus-sign"></span>Add Accounts</button></a>
                    </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listaccounts"><span class="glyphicon glyphicon-list-alt"></span>List Accounts</button></div>
            </div>
            <div id="accountslistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-3">
                        <select id="accountsubgrp" class="form-control">
                            <option value="none" class="form-control">Select Account Subgroup</option>
                            <?php
                                $acntsubgrp=AccountsSub::model()->findAll();
                                foreach($acntsubgrp AS $subgrp):
                                    echo "<option value='".$subgrp->id."' id='acntsubgrp'>".$subgrp->name."</option>";
                                endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="accounttype" class="form-control">
                            <option value="none" class="form-control">Select Account Type</option>
                            <?php
                            $acnttypes=AccountTypes::model()->findAll();
                            foreach($acnttypes AS $acnttype):
                                echo "<option value='".$acnttype->type_id."' id='acnttype'>".$acnttype->name."</option>";
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" placeholder="Search" id="searchaccounts" class="form-control">
                        <!--<input type="hidden" id="subgrpid" class="form-control">-->
                    </div>
                    <div class="col-md-3">
                        <button id="accountsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="accountstable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Name</th>
                                <th>Account type</th>
                                <th>TDS</th>
                                <th>GST</th>
                                <th>Account Subgroup</th>
                                <th>Schedule</th>
<!--                                <th>Account Subgroup</th>-->
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="accountsitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="accountsaddsection" class="row show-grid">
                <form id="accountsform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="accountsadd">
                        <tbody><tr>
                            <th>#</th>
                            <th><input class="form-control" type="text" id="accountsname" name="accountsname" placeholder="Accounts Name"><span class="error" style="display: none;"></span></th>
                            <th style="width: 10%"><input class="form-control" type="text" id="accounttds" name="accounttds" placeholder="TDS(%)"><span class="error" style="display: none;"></span></th>
                            <th style="width: 14%"><input class="form-control" type="text" id="accountservtax" name="accountservtax" placeholder="Service Tax(%)"><span class="error" style="display: none;"></span></th>
                            <th><select id="accounttype" name="accounttype" class="form-control" >
                                <option value="0">Select Account type</option>
                                <option value="4">Income</option>
                                <option value="5">Expense</option>
                                <option value="6">Asset</option>
                                <option value="7">Liability</option>
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                                </select>
                            </th>
                            <!--<th><span class="input-group-addon"><input type="checkbox" value="1" class="accountype" id="cash" name="account_type" style="visibility: visible;">Cash</span></th>
                            <th><span class="input-group-addon"><input type="checkbox" value="2" class="accountype" id="bank" name="account_type" style="visibility: visible;">Bank</span></th>-->
                            <th><span class="input-group-addon"><input type="checkbox" value="3" id="schedule" name="account_type" style="visibility: visible;">Schedule</span></th>
                            <th><button type="button" class="btn btn-danger" id="saveaccounts"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <form id="subgrpseditform" action="" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Subgroups</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" >
                        <thead>
                            <tr class="preloader" style="display: none;"><td colspan="2" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="subgrpdetails">

                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <input type="hidden" id="accountid" name="account_id">
                    <input type="button" class="btn btn-default" id="updatesubgrpacnts" value="Update">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

