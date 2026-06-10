<?php 
use app\models\Resources;


?>              
                <div class="panel panel-default project-tab acco-four tab tab-wrapper allocate-resource-tabs">



                    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/estimate-tab.js" type="text/javascript"></script>
                    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projects/allocation.js?v=<?php echo filemtime(Yii::getAlias('@webroot').'/jsnew/projects/allocation.js'); ?>" type="text/javascript"></script>
                    <!--<input type="radio" id="rd5" class="view_estimate" name="rd">-->
                    <div class="panel-heading" >
                        <h4 class="panel-title view_estimate">
                            <a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapseestimate">
                            <span class="icon-note1"></span>Project Estimate</a>
                        </h4>
                    </div>
                    <div id="collapseestimate" class="tab-content cOrder-body panel-collapse collapse">
                        <div class="panel-body">
                            <form method="POST" action="" id="pricingestimateform">
                                <div class="search-and-content-wrpr">
                                    <div class="confirm-purchase"></div>
                                    <div class="search-and-actions-wrpr row heads" style="padding-bottom:0px;" id="Estimate-allocate-body-one-head">
                                        <input type="hidden" id="estimateProject_Id" name="">
                                        <div style="display: flex;" class="col-md-12 col-sm-12 typeGroup-indication ">
                                            <div class="col-md-4">
                                                Estimate:
                                                <span  id="projectname" class="headings type"></span>
                                            </div>
                                            <div  class="col-md-3"></div>
                                            <div style="display: flex;" class="col-md-5">
                                                <p style="margin-top: 6px;margin-right: 5px;">Value:</p>  
                                                <input type="number" name="projectvalue" placeholder="Value" id="projectvalue" class="form-control projectvalue" readonly>
                                                <!-- <select name="processwise" id="processwise" style="margin-left: 8px;" class="form-control"></select> -->
                                                <button  value="1" name="Product_saveproduct" style="margin-left: 8px;"  id="saveproduct" class="btn btn-primary" type="button"><span>Save</span></button>
                                                <button  value="1" name="est_purchase" style="margin-left: 8px;"  id="est_purchase" class="btn btn-primary est_purchase" type="button"><span>Confirm</span></button>
                                            </div>
                                            <a href="#" id="listestimateitems"></a>
                                        </div>
                                        
                                    </div>
                                    <div class="content-wrpr rowhead">

                                        <div class="add-form add-wbs-form">
                                        </div>


                                        <div class="wbs-list-wrpr data-content-list" id="Estimate-allocate-body-one">
                                            <div class="resources-cntnt-wrpr row">
                                                <table class="table table-bordered" id="procresourcetable" style="display: table; overflow: hidden; background-color: #ffffff ;">
                                                <thead class="esthead">
                                                <tr>
                                                    <th><label>#</label></th>
                                                    <th style="width:20%;"><label>Activity Type</label></th>
                                                    <th style="width:32%;"><label>Activity</label></th>
                                                    <th style="width:5%;"><label class="align-center">Unit</label></th>
                                                    <th style="width:8%;"><label class="align-center">Quantity</label></th>
                                                    <th style="width:11%;"><label class="align-center">Rate</label></th>
                                                    <th style="width:18%;text-align:right;padding-right:14px;">Amount</th>
                                                    <th colspan="2" style="width:10%;" ></th>
                                                </tr>
                                                <tr class="preloader" style="display: none;"><td colspan="12" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"></td></tr>
                                                </thead>
                                                <tbody id="estimateitems">
                                                </tbody> 
                                            </table>
                                            </div>
                                        </div> </form>
                                        <!-- list end here -->
                                        <!-- Estimate-allocate body start here -->
                                        <div class="Estimate-allocate" id="Estimate-allocate-body-two" style="display: none;">

                                            <a href="#" id="estimateallocationlist"></a>
                                            <!-- list start here -->
                                            <div class="allocated-list-cntnt-wrpr added-alloc-items-wrpr data-content-list">
                                                <div class="row added-alloc-items-heading">
                                                <div class="col-md-12 ">
                                                    <div class="row  alloc-activity-title">
                                                        <div class="col-md-6">
                                                            <label><span><b id="activitycost" style="color: #4e5d6c;"></b></span></label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label><span><b id="activityunitnow" style="color: #4e5d6c;"></b></span></label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label><span><b id="activitycostnow" style="color: #4e5d6c;"></b></span></label>
                                                        </div>
                                                        <!-- <div id="activitycosthead" class="col-md-8 type">
                                                            <span></span>
                                                        </div> -->
                                                        <div class="col-md-2 text-right icon-groups">
                                                            <a href="#" class="btn btn-primary text-button resource-back-button" title="Back"> Back</a>
                                                            <!-- <a href="#" class="btn btn-primary close-resource-list-btn">Close Allocation</a> -->
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>

                                                <div class="allocation-list-items-cntnr">
                                                    <div class="estimationaddedallocation added-activity-list-wrpr collapse in"></div><br>
                                                    <div class="col-md-12 type project-boq" >
                                                        <a href="javascript:void(0);"  class="btn btn-primary add-project-activities-btnnew resource-list-btn1"><span class="icon-add"></span>Allocate Resources</a>
                                                        <a href="#" class="btn btn-primary close-resource-list-btn1">Close Allocation</a>&nbsp;&nbsp;&nbsp;
                                                        <a href="javascript:void(0);" class="expand-collapse-palist icon-arrow-up1" data-toggle="collapse" data-target=".added-activity-list-wrpr" style="display: none;"></a>
                                                    </div>
                                                </div>
                                            </div>
							                <div class="allocation-cntnt-wrpr resource-not-allocated">
					                            <div class="estimation-left-bar allocation-left-bar items-select-bar">
					                            </div>
                                                <div class="resource-not-allocated-info">
                                                    <div class="icon-info3"></div>
                                                    <div class="info-text">
                                                        <p>Resources not allocated to the activity - Investments in Floating Crafts</p>
                                                        <span>To allocate resources, click on the left tab</span>
                                                    </div>
                                                </div>
                                                <div class="resources-from-selected-item-wrpr ">
                                                    <div class="row">
                                                        <div class="col-md-12 ">
                                                            <div class="search-and-allocate-content-wrpr">
                                                            <div class="search-and-actions-wrpr row">
                                                                <div class="content-search-wrpr col-md-12 col-sm-12">
                                                                    <a href="#" id="deleterefresh"></a>
                                                                    <input type="hidden" id="resourcegroupval" name="" value="">
                                                                    <input type="hidden" id="EstActivity_Id" name="" value="">
                                                                    <input type="hidden" id="project_id" name="" value="">
                                                                    <input type="hidden" id="proces_id" name="" value="">
                                                                    <input type="hidden" id="activity_id" name="">
                                                                    <input type="hidden" id="activityunit" value="" name="">
                                                                    <input type="hidden" id="activity_name" value="" name="">

                                                                    <!--<input type="search" onsearch="OnSearch()" class="form-control searchdefault resources resourcegroupsearch"  id="resourcegroup" name="accounthead-choice" placeholder="Search"/>-->

                                                                    <input type="search" class="form-control searchdefault resources resourcegroupsearch" id="resourcegroup" name="accounthead-choice" placeholder="Search"/>

                                                                    <button id="actestimate-res" class="btn btn-primary searchresoureskey" type="button"><span class="icon-search5" ></span></button>

                                                                   <!--  <datalist id="resourcelist">
                                                                    <?php                 
                                                                    $resources//=Resources::find()->where(['status'=>0])->andwhere(['pricing_status'=>0])->groupBy(['Name'])->all();

                                                                    //foreach($resources AS $resource):
                                                                       // echo "<option value='".'.$resource->Name.'."'></option>";
                                                                   // endforeach;
                                                                    ?>
                                                                    </datalist> -->
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="panel-group allocation-items allocation-items-estimate" id="accordion">
                                        
                                                            </div>
                                                        </div>
                                                            
                                                        </div>	
                                                        
                                                    </div>
                                                    
                                                </div>
					
				                            </div>
                                        </div>
                                        <!-- Estimate-allocate body end here -->
                                    </div>
                                </div>
                        


      




                        </div>
                    </div>
                    <script>
                        $(document).on( "blur",".quantity", function(){

                            //alert("hi");//var itemid=$(this).attr('data-id');
                            var datatype=$(this).attr('data-type');
                            datatype = datatype.replace(/ +/g, "");
                            var dataid=$(this).attr('data-id');
                            var iowid=$(this).attr('data-iow');
                            //console.log(iowid)
                            var error=0;
                            $('.error').hide();
                            if($('#'+datatype+'quantity'+dataid).val()==0)
                            {
                                $('#'+datatype+'quantity'+dataid).next("span").html('Enter Valid Quantity').show('slow');
                                error=1;
                            }
                            var quantity=($(this).val()*1);
                            var specrate=($('#'+datatype+'rate'+dataid).val()*1);
                            var amount=specrate*quantity;
                            $('#'+datatype+'amount'+dataid).html(amount.toFixed(2));
                            $('#amount'+dataid).val(amount);
                            var totalrate=0;
                            $('.'+datatype+'amount').each(function(){
                                //alert($(this).val()*1)
                                totalrate=totalrate+($(this).val()*1);
                            });

                            $('#total'+datatype+'cost').html(totalrate.toFixed(2));
                            var totaliowrate=0;
                            $('.iowamount'+iowid).each(function(){
                                //console.log($(this).val()*1)
                                totaliowrate=totaliowrate+($(this).val()*1);
                            });

                            $('#totaliowcost'+iowid).html(totaliowrate.toFixed(2));
                            $('#totaliowcostval'+iowid).val(totaliowrate);
                            var totalprojcost=0;
                            $('.totaliowcost').each(function(){
                                //console.log($(this).val()*1)
                                totalprojcost=totalprojcost+($(this).val()*1);
                            });
                            $('#totalcost').html(totalprojcost.toFixed(2));
                        });
                        $(document).on( "click",".removeiowitem", function(){
                            $('#listestimateitems').trigger('click') ;
                        });

                        
                        $(document).on('change','.estimate-resourcetime',function(){
                            

                            var id = $(this).attr('data-id');
                            var fuelrate = $('#estimate-fuelrate'+id).val();
                            var fuelqty = $('#estimate-fuelqty'+id).val();
                            var hrs = $('#estimate-resourcetime'+id).val();
                            var repair = $('#estimate-repair'+id).val();

                            var res_qty = hrs;

                            if(repair != ''){

                                var res_rate =   parseInt(fuelrate) *  parseInt(fuelqty) +  parseInt(res_qty);
                            }else{
                                var res_rate =  parseInt(fuelrate) *  parseInt(fuelqty) ;
                            }

                            var amount =  res_rate * res_qty;

                            $('#estimate-acamnt'+id).val(amount);

                            


                       });


                    </script>
                </div>

