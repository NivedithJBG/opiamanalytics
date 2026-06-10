<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/cashbill.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="cashbill"><a href="javascript:void(0)">10. Cash Bill</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3" style="text-align: left;">
                    <h4 id="cashbillprojname"></h4>
                </div>
                <div class="col-md-2"><button type="button" class="btn btn-success"  id="addcashbill"><span class="glyphicon glyphicon-plus-sign"></span>Add Cash Bill</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="listcashbill"><span class="glyphicon glyphicon-list-alt"></span>List Cash Bill</button></div>
            </div>
           <div id="cashbilllistsection">
               <div class="row show-grid">
                   <table class="table table-bordered" id="cashbilltable" style="display: table; overflow: hidden;">
                       <thead>
                       <tr>
                           <th>#</th>
                           <th>Date</th>
                           <th>User</th>
                           <th >Activity</th>
                           <th >Purpose</th>
                           <th >Accounthead</th>
                           <th >Amount</th>
                           <!--<th colspan="2"></th>-->
                       </tr>
                       <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                       </thead>
                       <tbody id="cashbillitems">

                       </tbody>
                   </table>
               </div>
           </div>
            <div id="cashbilladdsection" style="display: none">
                <form action="" id="cashbillform">
                    <table class="table table-bordered" id="cashbilltable" style="display: table;">
                        <tbody>
                        <tr>
                            <th>
                                <select class="form-control" id="cashactivitylist" name="cashactivity">
                                    <option value="none">Select Activity</option>
                                </select>
                            </th>
                            <th>
                                <select class="form-control" id="cashresourcelist" name="cashresource">
                                    <option value="none">Select Resource</option>
                                </select>
                            </th>
                            <th>
                                <select class="form-control" id="cashaccountheadlist" name="cashaccounthead">
                                    <option value="none">Select Accounthead</option>
                                    <?php $acnts=AccountsItem::model()->findAll(array('order'=>'name ASC'));
                                    foreach($acnts AS $accounts):
                                        echo "<option value='".$accounts->id."' >".$accounts->name."</option>";
                                    endforeach;?>
                                </select>
                                <span class="error"></span>
                            </th>
                            <th>
                                <input type="text" class="form-control" id="cashpurpose" name="cashpurpose" placeholder="Purpose">
                            </th>
                            <th>
                                <input type="text" class="form-control" id="cashamount" name="cashamount" placeholder="Amount">
                                <input type="hidden" id="cashbillProjectId" name="cashbillProjectId">
                            </th>
                            <th>
                                <button type="button" class="btn btn-danger" id="savecashbill" value="Save">
                                    <span class="glyphicon glyphicon-saved"></span>Save
                                </button>
                            </th>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>