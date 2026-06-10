<?php
use app\models\AssetRegisterItem;


?> 

 <div class="menu7-popup-cntnr" id="asset_library" style="display: none;" >
    <div class="menu7-cntnt-wrpr">
      <div class="icon-groups type"> 
        <!-- <a href="#" title="Close" class="btn btn-primary text-button menu01-win-close">&#10006; Close</a> -->
      </div>
      <!--<div style="text-align: center;"><b><h4>Project Report</h4></b></div>-->
      <div class="col-md-12">
        <div class="panel-group acco-one-active" id="accordionAssetLibItem">

            <div class="panel panel-default asset-register-tab tab tab-wrapper acco-twelve">
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

            <div class="panel panel-default asset-register-tab tab tab-wrapper acco-thirteen">

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



  