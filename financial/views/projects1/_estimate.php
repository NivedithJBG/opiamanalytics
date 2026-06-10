<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/projects/estimate.js" type="text/javascript"></script>
<?php if($_GET['project']!=''):?>
    <script type="application/javascript">
        $(function(){
            setTimeout(function(){
                //alert("Hello");
                $('#estimateprojtab').trigger('click') ;
            }, 1000);

        });
    </script>
<?php endif;?>
<h2 class="acc_trigger" id="estimateprojtab"><a href="javascript:void (0)">5. Project Estimate</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <form method="POST" action="" id="pricingestimateform">
                <input type="hidden" id="estimateProject_Id" name="Project_Id" value="<?php echo $_GET['project'];?>">
                <input type="hidden" id="listestimateitems">
                <table class="table table-bordered">
                    <thead>
                        <tr style="background-color: ghostwhite">
                            <td>
                                <span class="headings" id="projectname"><b>Project Name </b><h4></h4></span>
                            </td>
                            <td style="width:15%;">
                                <span class="headings">
                                    <b>Project Value</b>
                                </span>
                                <input type="text" value="" name="projectvalue" id="projectvalue" class="form-control">
                            </td>
                            <!-- <td>
                                <span class="headings"><b>Quote Amount</b></span>
                                <input type="text" value="" name="qouteamount" id="qouteamount" class="form-control">
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary" id="saveproduct" value="1" name="Product_saveproduct">
                                    Save
                                </button>
                            </td> -->
                            <!--<td>                           
                              <select name="estimatestructurelist" class="form-control" id="estimatestructurelist">

                              </select>
                            </td>-->
                            <td>    
                              <span class="headings">&nbsp;</span>
                              <select name="processwise" class="form-control" id="processwise">

                              </select>
                            </td>
                            <td>
                                <span class="headings">&nbsp;</span>
                                <button type="button" class="btn btn-primary" id="saveproduct" value="1" name="Product_saveproduct">
                                    Save
                                </button>
                            </td>
                        </tr>
                    </thead>
                </table>
                <table class="table table-bordered" id="estimatetable">
                    <thead>
                        <tr>
                            <th><b>Activity Type</b></th>
                            <th><b>Activity</b></th>
                            <th><b>Unit</b></th>
                            <th class="small75"><b>Quantity</b></th>
                            <th class="small75"><b>Rate</b></th>
                            <th class="small75"><b>Amount</b></th>
                            <th colspan="4"></th>
                           <!-- <th></th> -->
                        </tr>
                        <tr class="preloaderitems">
                            <td colspan="9" align="center">
                                <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle">
                            </td>
                        </tr>
                    </thead>
                    <!--<tbody id="addedproducts" class="ui-sortable"> -->
                    <tbody id="addedproducts" class="addedproducts">
                        
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>