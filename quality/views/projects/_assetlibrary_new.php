<?php
use app\models\AssetRegisterItem;


?> 
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projects/over_menu_operationreport.js" type="text/javascript"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/assetregister.js" type="text/javascript"></script>




<div class="container-fluid procu-accordion">
  

  <!-- tab2 -->
  
 <div class="" id="asset_library"  >
    <div class="">
      <!--<div style="text-align: center;"><b><h4>Project Report</h4></b></div>-->
      <div class="col-md-12">
        <div class="panel-group acco-one-active" id="accordionAssetLibItem">

            <div class="panel panel-default asset-register-tab tab tab-wrapper acco-one">
              <!-- <input type="radio" class="AssetregisterTab" id="rd5" name="rd"> -->
              
              <div class="panel-heading" >
                <h4 class="panel-title" id="AssetLibraryItem">
                <a data-toggle="collapse" data-parent="#accordionAssetLibItem" href="#collapseAssetLibItem">
                <span class="icon-archive1"></span>Asset Library Item</a>
                </h4>
              </div>

              <div id="collapseAssetLibItem" class="tab-content cOrder-body panel-collapse collapse">
                <div class="panel-body ">
                  <div class="search-and-content-wrpr">

                    <div class="search-and-actions-wrpr row">
                        <div class="content-search-wrpr col-md-5 col-sm-5">
                            <!-- <input type="text" placeholder="Search" id="searchestworktypename" class="form-control" >
                            <button id="estworktypesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button> -->
                        </div>
                        <div class="col-md-5 col-sm-5"></div>
                        <div class="content-action-wrpr col-md-2 col-sm-2">
                            <a data-target="#addAssetLibItemPopup" href="#addAssetLibItemPopup" data-toggle="modal"  id="addAssetLibItemBtn" class="btn btn-primary addAssetLibItemForm" title="Add Asset Library Item"><span class="icon-add"></span> Add</a>
                        </div>
                    </div>

                    <div class="content-wrpr">
                      <a href="#" type="hidden" id="assetLibItemsearch"></a>  
                      <!-- list start here -->
                      <div class="asset-register-list-wrpr">
                        <div class="preloader" style="display: none;" align="center">
                            <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                        </div>
                        <div id="assetLibitems"></div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

            </div>

            <div class="panel panel-default asset-register-tab tab tab-wrapper acco-two">

              <div class="panel-heading" >
                <h4 class="panel-title" id="AssetLibrary">
                <a data-toggle="collapse" data-parent="#accordionAssetLib" href="#collapseAssetLib">
                <span class="icon-archive1"></span>Asset Library</a>
                </h4>
              </div>

              <div id="collapseAssetLib" class="tab-content cOrder-body panel-collapse collapse">
                <div class="panel-body ">
                <div class="search-and-content-wrpr">

                  <div class="search-and-actions-wrpr row no-left-padding">

                      <div class=" col-md-1 no-padding"></div>
                      <div class="content-search-wrpr col-md-5 col-sm-5 no-padding">
                          <select class="form-control" id="asset_register_item_id" name="asset_register_item_id"  tabindex="1">
                          <option value="">Select Asset Register Item</option>
                          <?php
                      $itemList = AssetRegisterItem::find()->where(['Status'=>1])->orderBy(['item_name'=>SORT_ASC])->all();
                        foreach($itemList AS $list):  
                                echo "<option data-eq-type='".$list->type."' value='".$list->id."'>".$list->item_name."</option>";
                            endforeach;
                          ?>
                      </select>
                          <button id="estworktypesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                      </div>
                      <div class="col-md-5 col-sm-5"></div>
                      <div class=" col-md-1"></div>

                  </div>

                  <div class="content-wrpr">
                    <a href="#" type="hidden" id="assetLibSearch"></a>  
                    <!-- list start here -->
                    <div class="asset-register-list-wrpr">
                      <div class="preloader" style="display: none;" align="center">
                          <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                      </div>
                      <div id="assetLib"></div>
                    </div>
                  </div>


                </div>
                </div>
              </div>


            </div>
    
        </div>
      </div>
    </div>






  </div>



  
  <!-- tab2 ends -->
 
  <style>
        .tab-content{
            max-height:unset;
        }

    </style>
</div>



<!---------------- POPUP - ASSET LIBRARY ITEM ------------------->
<div class="modal fade addAssetLibItemPopup" id="addAssetLibItemPopup" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addAssetLibItemform" >

              <!-- Modal Header -->
              <div class="modal-header">
                  <h4 class="modal-title" id="addAssetLibItemPopupTitle"  style="float: left;">Create - Asset Library Item</h4>
                  <button type="button" class="close addAssetLibItemPopup" data-dismiss="modal" style="float:right; font-size: 30px;">×</button>
              </div>

              <!-- Modal body -->
              <div class="modal-body">

                    <!-- form starts here -->
                <div style="display: none;" id="addAssetLibItemwindow" class="add-project-master-form row">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Item Name</label>
                            <input type="text" class="form-control" id="asset_item_name" name="item_name" placeholder="Item Name">
                            <span class="error" style="display: none;"></span>
                          </div>
                        </div>
                        <div class="col-md-5">
                          <div class="form-group">
                              <label>Equipment Type</label>
                            <select id="asset_equipment_type" name="type" class="form-control">
                                  <option value="none">Select Equipment Type</option>
                                  <option data-eq-type="engine" value="engine">Engine Driven Equipments </option>
                                  <option data-eq-type="motor" value="motor">Motor Driven Equipments</option>
                                </select>
                            <span class="error" style="display: none;"></span>
                          </div>
                        </div>
                      <!-- <div class="row">
                        <div class="col-md-12 text-center">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-primary" id="saveworkgroup"><span class="icon-check"></span> Add WBS</button>
                            <button type="button" class="btn btn-danger cancel iowFormPopup" id="cancelworkgroup" data-dismiss="modal"><span class="icon-close"></span> Cancel</button>
                        </div>
                      </div> -->
                </div>
                      <!-- form ends here -->
              </div>
              </div>

              <!-- Modal footer -->
              <div class="modal-footer">
                  <input type="hidden" id="asset_updateId">
                  <button type="button" class="btn btn-primary" id="saveAssetLibItem"><span class="icon-check"></span> Submit</button>
                  <button type="button" class="btn btn-danger cancel addAssetLibItemPopup" id="cancelAssetLibItem" data-dismiss="modal"><span class="icon-close"></span> Cancel</button>
          
              </div>
            </form>  

        </div>
    </div>
</div>

<!------------------------------------------->


<script>
  $( document ).ready(function() {

    var value = 0;
    $('.panel-default').removeClass('acco-one acco-two acco-three acco-four acco-five acco-six acco-seven acco-eight acco-nine acco-ten acco-eleven acco-twelve acco-thirteen acco-fourteen acco-fifteen acco-sixteen');


    $('.panel-default').each(function() { 

     var va = value++; 
      var wordss = {
      1:'one',
      2:'two',
      3:'three',
      4:'four',
      5:'five',
      6:'six',
      7:'',
      8:'eight',
      9:'nine',
      10:'ten',
      11:'eleven',
      
     
      12:'one',
      13:'two',
      14:'three',
      15:'four',
      };
      $(this).addClass('acco-'+wordss[va]);

    });

  });
</script>
