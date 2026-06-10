<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/trade.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Trade"><a href="javascript:void(0)">6. Trade</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addtrade"><span class="glyphicon glyphicon-plus-sign"></span>Add Trade</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listtrade"><span class="glyphicon glyphicon-list-alt"></span>List Trade</button></div>
            </div>
            <div id="tradelistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchtrade" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="tradesearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="tradetable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Resource Type</th>
                                <th>Resource(Trade)</th>
                                <th>Normal Rate per hour</th>
                                <th>OT rate per hour</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="tradeitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="tradeaddsection" class="row show-grid">
                <form id="tradeform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="tradeadd">
                        <tbody><tr>
                            <th>#</th>
                            <th>
                                <select id="resourcetype" class="form-control">
                                    <option value="none">Select Resource Type</option>
                                    <?php
                                    $typelist=Resourcetype::model()->findAll(array('order'=>'Name ASC'));
                                    foreach($typelist AS $list):
                                        echo "<option value='".$list->ResourceType_Id."'>".$list->Name."</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </th>
                            <th>
                                <select id="resource" class="form-control">
                                    <option value="none">Select Resource</option>

                                </select>
                                <span class="error" style="display: none;"></span>
                            </th>
                            <!--<th><input class="form-control" type="text" id="tradename" name="tradename" placeholder="Trade Name"><span class="error" style="display: none;"></span></th>-->
                            <th><input class="form-control" type="text" id="rateperday" name="rateperday" placeholder="Rate per hour"><span class="error" style="display: none;"></span></th>
                            <th><input class="form-control" type="text" id="otperhour" name="otperhour" placeholder="OT rate per hour"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="savetrade"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>
        </div>
    </div>
</div>