<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/billofquantities.js" type="text/javascript"></script>
<script>
    $(document).on('mouseenter','.hover',function(){
        var tooltip=$(this).attr('data-tooltip');
        $('.tooltiptable').hide();
        $('#'+tooltip).fadeIn('fast');
    });
    $(document).on('mouseleave','.hover',function(){
        var tooltip=$(this).attr('data-tooltip');
        $('#'+tooltip).fadeOut('slow');
    });
    $(document).on('mouseenter','.hover2',function(){
        var tooltip=$(this).attr('data-tooltip2');
        $('.tooltiptable').hide();
        $('#'+tooltip).fadeIn('fast');
    });
    $(document).on('mouseleave','.hover2',function(){
        var tooltip=$(this).attr('data-tooltip2');
        $('#'+tooltip).fadeOut('slow');
    });
</script>
<h2 class="acc_trigger" id="billsofquantity"><a href="#billsofquantity">8. Reports</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">

            <div id="projectsetuplistsection">
                <div class="col-md-3" style="display: none;" id="">
                    <!--<select class="form-control" id="billselection">
                        <option value="none">All</option>
                        <option value="1">Bill Of Quantity</option>
                        <option value="2">Bill Of Materials</option>
                    </select>-->
                    <input type="hidden" id="billselection" value="">
                </div>
                <div id="searchdivprojectsetup" class="row show-grid" style="display: none;">
                    <div class="col-md-6">
                        <input type="hidden" placeholder="Search" id="billsquantitysearchdata" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="billsquantitysearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <div class="col-md-12" style="text-align: left;" id="dispprojectnamebills">

                        </div>
                        <table class="table table-bordered" id="billselectiontable">
                            <tr><td>Project Estimate</td><td style="text-align: center">
                                    <button id="billofestimate" class="btn btn-primary " type="button" style="width: 150px">View</button>
                                </td></tr>
                            <tr><td>Bill Of quantity</td>
                                <td style="text-align: center"><button  id="billofquantity" class="btn btn-primary "style="width: 150px" type="button" >View</button></td>
                            </tr>
                            <tr><td>Bill Of quantity - IOW</td>
                                <td style="text-align: center"><button  id="billofquantityiow" class="btn btn-primary "style="width: 150px" type="button" >View</button></td>
                            </tr>
                            <tr><td >Bill Of Materials</td><td style="text-align: center">
                                    <button id="billofmaterial" class="btn btn-primary " type="button" style="width: 150px">View</button>
                                </td></tr>
                            <tr><td >Iow Estimate</td><td style="text-align: center">
                                    <button id="estimateiow" class="btn btn-primary " type="button" style="width: 150px">View</button>
                                </td></tr>
                                <?php /*
								 * Added By Karthik
								 */?>
                            <tr>
                            	<td>Project cash flow</td>
                            	<td style="text-align: center"><button id="procashflow" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>
                            <tr>
                                <td>Rate Variance</td>
                                <td style="text-align: center"><button id="ratevariance" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>
                            <tr>
                                <td>Quantity Variance</td>
                                <td style="text-align: center"><button id="quantityvariance" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>
                            <tr>
                                <td>Cost Variance</td>
                                <td style="text-align: center"><button id="costvariance" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>
                            <tr>
                                <td>Project Value</td>
                                <td style="text-align: center"><button id="activitycost" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>
                            <tr>
                                <td>IOW Actual Cost</td>
                                <td style="text-align: center"><button id="activityactualcost" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>
                            <tr>
                                <td>Activity Actual Cost</td>
                                <td style="text-align: center"><button id="actvtyactualcost" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>
                            <!--<tr>
                                <td>Resourcetype Actual Cost</td>
                                <td style="text-align: center"><button id="restype_actualcost" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>-->
                            <tr>
                                <td>Activity Amount</td>
                                <td style="text-align: center"><button id="activity_amount" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>
                            <tr>
                                <td>Resource Group</td>
                                <td style="text-align: center"><button id="resource_group" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>
                            <tr>
                                <td>Ongoing Activities</td>
                                <td style="text-align: center"><button id="weekly_target" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>
                            <tr>
                                <td>Activities due for this week</td>
                                <td style="text-align: center"><button id="due_start" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>
                            <tr>
                                <td>Overdue Activities</td>
                                <td style="text-align: center"><button id="overdue_act" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>
                            <tr>
                                <td>Activity based Cost</td>
                                <td style="text-align: center"><button id="actvtybasedcost" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>
                            <?php /*
								 * Added By Karthik
								 */?>
                            <!--<tr>
                                <td>Corporate Office cash flow</td>
                                <td style="text-align: center"><button id="cocashflow" class="btn btn-primary " type="button" style="width: 150px">View</button></td>
                            </tr>-->
                        </table>
                        <div class="col-md-6"></div><div class="col-md-6" id="print" style="display: none;text-align: right"></div>
                        <div class="col-md-12" style="display: none ; font-size: large" id="billsinfo">Geotech Offshore Structures (P) Ltd</div>
                        <div class="col-md-12" style="display: none ; font-size: large" id="billstypeinfo"></div>
                        <div class="col-md-3" style="display: none;font-size: large" id="quarterdiv">
                        <select id="quarterselector" class="form-control" style="display: none;">
                            <option value="1"><?php echo date('jS F Y ', strtotime('first day of april')).' - '.date('jS F Y ', strtotime('last day of june'));?> </option>
                            <option value="2"><?php echo date('jS F Y ', strtotime('first day of july')).' - '.date('jS F Y ', strtotime('last day of september'));?> </option>
                            <option value="3"><?php echo date('jS F Y ', strtotime('first day of october')).' - '.date('jS F Y ', strtotime('last day of december'));?> </option>
                            <option value="3"><?php echo date('jS F Y ', strtotime('first day of january next year')).' - '.date('jS F Y ', strtotime('last day of march next year'));?> </option>
                        </select>
                        </div>
                        <table class="table table-bordered" id="billstable" style="display: none; overflow: hidden;">
                            <thead>
                            <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>


                            <tbody id="billitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

