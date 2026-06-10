
<div class="panel panel-default acco-three tab" id="acco-vendors">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/vendors.js" type="text/javascript"></script>
    <!--<input type="radio" id="rd1" name="rd">-->
    <div class="panel-heading">
      <h4 class="panel-title " id="choosevendors">
        <a  data-toggle="collapse" data-parent="#accordionindex" href="#collapsevendors">
        <span class="icon-user3"></span>Choose Vendor</a>
      </h4>
    </div>
    <div id="collapsevendors" class="tab-content panel-collapse collapse">
        <div class="panel-body">
            <div class="acc_container123" >
                <div id="vendorslistsection">
                    <form class="row" method="POST" action="" id="choosevendorsform">
                        <div class="col-md-12" id="choosevendorstable">
                            <div class="row">
                               <div class="col-md-12 col-sm-12">

                                 <div class="col-md-4 col-sm-4 type">
                                    <label>Resource Type</label>
                                    <span id="newdatarestype"></span>
                                      
                                  </div>

                                  <div class="col-md-4 col-sm-4 type">
                                    <label>Resource Name</label>
                                    <span id="newdata"></span>
                                      
                                  </div> 
                                
                                   <div class="col-md-4 col-sm-4 type">
                                    <label>Activity</label>
                                    <span id="newdataactivity"></span>
                                      
                                  </div> 




                               </div> 

                            </div>



                            <div id="newdata"></div>

                            <table class="table table-bordered vendor-table" id="choosevendorstable">
                            <thead>
                            <tr>
                                <th>#</th>
                              <!--   <th>Resource Type</th> -->
                               <!--  <th>Resource Name</th> -->
                               <th>Vendor Name</th>
                               <th>Location</th>
                               <th>Brand<br> 
                                <th>Unit</th>
                                <th>Rate</th>
                                <th id="Quantityh">Quantity</th>
                                <th id="Numdaysh" style="display: none">No of Days</th>
                                <th id="Numworkersh" style="display: none">No of Workers</th>
                                <th id="Otrateh" style="display: none">OT Rate</th>
                                <th colspan="3"></th>

                            </tr>
                            <tr class="preloader" style="display:none;"><td colspan="12" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody  id="addedresources">
                            <?php /*echo $datarows;*/?>
                            </tbody>
                        </table>
                        </div>
                    </form>
                </div>   
            </div> 
        </div>
    </div>

    
    
<!-- <h2 class="acc_trigger" id="choosevendors"><a href="javascript:void(0)">2. Vendors</a></h2>-->
</div>    

