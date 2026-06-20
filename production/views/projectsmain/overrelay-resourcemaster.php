<?php
use app\models\Resourcetype;
use app\models\ResourceGroup;
$resourceTypes  = Resourcetype::find()->where(['Status' => 0])->orderBy(['sortorder' => SORT_ASC, 'Name' => SORT_ASC])->all();
$resourceGroups = ResourceGroup::find()->where(['status' => 0])->orderBy(['RG_sortorder' => SORT_ASC, 'Resource_group_Name' => SORT_ASC])->all();
?>
<style>
.reslib-tab-row {
    padding: 20px 20px 16px;
    text-align: center;
    border-bottom: 1px solid #e8e8e8;
    background: #f8fafc;
}
.reslib-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1.5px solid #c5ccd4;
    border-radius: 50px;
    padding: 6px 24px 6px 14px;
    margin: 0 5px;
    background: #fff;
    color: #465365;
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.4px;
    min-width: 150px;
    cursor: pointer;
    transition: background 0.2s, color 0.2s, border-color 0.2s;
    text-decoration: none;
}
.reslib-tab-btn:hover { background: #f0f2f5; text-decoration: none; color: #465365; }
.reslib-tab-btn.active { background: #072c47; border-color: #072c47; color: #fff; }
.reslib-tab-icon {
    display: inline-flex; align-items: center; justify-content: center;
    width: 22px; height: 22px; border-radius: 50%;
    background: rgba(7,44,71,0.10); font-size: 11px; flex-shrink: 0;
}
.reslib-tab-btn.active .reslib-tab-icon { background: rgba(255,255,255,0.18); color: #fff; }
.reslib-tab-content { display: none; }
.reslib-tab-content.active { display: block; }
</style>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/over_menu_resourcemaster.js" type="text/javascript"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/resourcetype.js?v=6" type="text/javascript"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/resourcegroup.js" type="text/javascript"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/resource.js" type="text/javascript"></script>

<div class="container-fluid procu-accordion">
    <div class="row">
        <div class="menu-popup-cntnr">
            <div class="menu-cntnt-wrpr">
                <div class="col-md-12">

                    <!-- Piano tab navigation -->
                    <div class="reslib-tab-row">
                        <a href="javascript:;" class="reslib-tab-btn active" data-tab="type">
                            <span class="reslib-tab-icon"><span class="icon-equalizer"></span></span>
                            Resource Types
                        </a>
                        <a href="javascript:;" class="reslib-tab-btn" data-tab="group">
                            <span class="reslib-tab-icon"><span class="icon-folder"></span></span>
                            Resource Groups
                        </a>
                        <a href="javascript:;" class="reslib-tab-btn" data-tab="res">
                            <span class="reslib-tab-icon"><span class="icon-settings"></span></span>
                            Resources
                        </a>
                    </div>

                    <!-- ===== TAB 1: Resource Types ===== -->
                    <div id="reslib-tab-type" class="reslib-tab-content active">
                        <div class="search-and-content-wrpr" style="padding:14px 6px 0;">
                            <a href="#" id="listrestype" style="display:none;"></a>
                            <div class="search-and-actions-wrpr row">
                                <div class="content-search-wrpr col-md-6 col-sm-6">
                                    <input type="text" placeholder="Search" id="searchrestypename" class="form-control">
                                    <button id="resourcetypesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                                </div>
                                <div class="col-md-6 col-sm-6 text-right" style="padding-top:4px;">
                                    <button type="button" id="addrestype" class="btn btn-sm" style="background-color:#072c47;color:#fff;border-color:#072c47;border-radius:20px;padding:5px 22px;"><span class="icon-plus"></span> Add</button>
                                </div>
                            </div>
                            <div class="content-wrpr">
                                <!-- Add form -->
                                <div class="add-form add-resource-type-form" style="display:none;">
                                    <div class="row">
                                        <form id="addrestypeform">
                                            <div class="col-md-1"></div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Resource Type</label>
                                                    <input id="restypename1" type="text" class="form-control" placeholder="Resource Type">
                                                    <span class="error" style="display:none;"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text-left">
                                                <label>&nbsp;</label><br>
                                                <button type="button" id="cancelrestype" class="btn btn-sm" style="border-radius:20px;padding:6px 20px;background-color:#d9534f!important;color:#fff!important;border-color:#d43f3a!important;margin-right:6px;">Cancel</button>
                                                <button type="button" id="saverestype" class="btn btn-sm" style="border-radius:20px;padding:6px 20px;background-color:#072c47!important;color:#fff!important;border-color:#072c47!important;">Add Resource Type</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- Edit form -->
                                <div class="edit-form edit-resource-type-form" style="display:none;padding:10px 0;">
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Resource Type Name</label>
                                                <input class="form-control" id="restypenames" placeholder="Enter resource type name" type="text">
                                                <span class="error" style="display:none;"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-left">
                                            <label>&nbsp;</label><br>
                                            <button type="button" class="btn btn-danger" id="cancelrestypes"><span class="icon-close"></span> Cancel</button>
                                            <button type="button" class="btn btn-primary" id="saveresourcetypebutton"><span class="icon-check"></span> Save Changes</button>
                                            <input type="hidden" id="saveresourcetypeval" value="">
                                        </div>
                                    </div>
                                </div>
                                <!-- Preloader -->
                                <div class="preloader" style="display:none;text-align:center;padding:20px 0;">
                                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif">
                                </div>
                                <!-- List -->
                                <div id="restypelistsection" class="data-content-list"></div>
                            </div>
                        </div>
                    </div>

                    <!-- ===== TAB 2: Resource Groups ===== -->
                    <div id="reslib-tab-group" class="reslib-tab-content">
                        <div class="search-and-content-wrpr" style="padding:14px 6px 0;">
                            <a href="#" id="listresgroup" style="display:none;"></a>
                            <div class="search-and-actions-wrpr row">
                                <div class="content-search-wrpr col-md-6 col-sm-6">
                                    <input type="text" placeholder="Search" id="searchresgroupname" class="form-control">
                                    <button id="resgroupsearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                                </div>
                                <div class="col-md-6 col-sm-6 text-right" style="padding-top:4px;">
                                    <button type="button" id="addresgroup" class="btn btn-sm" style="background-color:#072c47;color:#fff;border-color:#072c47;border-radius:20px;padding:5px 22px;"><span class="icon-plus"></span> Add</button>
                                </div>
                            </div>
                            <div class="content-wrpr">
                                <!-- Add form -->
                                <div class="add-form add-resource-group-form" style="display:none;">
                                    <div class="row">
                                        <form id="addresgroupform">
                                            <div class="col-md-1"></div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Resource Group</label>
                                                    <input id="resgroupname1" type="text" class="form-control" placeholder="Resource Group">
                                                    <span class="error" style="display:none;"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Resource Type</label>
                                                    <select id="addresgrouptype" class="form-control">
                                                        <option value="0">-- Select Type --</option>
                                                        <?php foreach ($resourceTypes as $rt): ?>
                                                        <option value="<?php echo $rt->ResourceType_Id; ?>"><?php echo htmlspecialchars($rt->Name, ENT_QUOTES); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-left">
                                                <label>&nbsp;</label><br>
                                                <button type="button" id="cancelresgroup" class="btn btn-sm" style="border-radius:20px;padding:6px 14px;background-color:#d9534f!important;color:#fff!important;border-color:#d43f3a!important;margin-right:4px;">Cancel</button>
                                                <button type="button" id="saveresgroup" class="btn btn-sm" style="border-radius:20px;padding:6px 14px;background-color:#072c47!important;color:#fff!important;border-color:#072c47!important;">Add Resource Group</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- Edit form -->
                                <div class="edit-form edit-resource-group-form" style="display:none;padding:10px 0;">
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Resource Group Name</label>
                                                <input class="form-control" id="resgroupnames" placeholder="Enter resource group name" type="text">
                                                <span class="error" style="display:none;"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Resource Type</label>
                                                <select id="editresgrouptype" class="form-control">
                                                    <option value="0">-- Select Type --</option>
                                                    <?php foreach ($resourceTypes as $rt): ?>
                                                    <option value="<?php echo $rt->ResourceType_Id; ?>"><?php echo htmlspecialchars($rt->Name, ENT_QUOTES); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-left">
                                            <label>&nbsp;</label><br>
                                            <button type="button" class="btn btn-danger" id="cancelresgroups"><span class="icon-close"></span> Cancel</button>
                                            <button type="button" class="btn btn-primary" id="saveresgroupbutton"><span class="icon-check"></span> Save Changes</button>
                                            <input type="hidden" id="saveresgroupval" value="">
                                        </div>
                                    </div>
                                </div>
                                <!-- Preloader -->
                                <div class="preloader-group" style="display:none;text-align:center;padding:20px 0;">
                                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif">
                                </div>
                                <!-- List -->
                                <div id="resgrouplistsection" class="data-content-list"></div>
                            </div>
                        </div>
                    </div>

                    <!-- ===== TAB 3: Resources ===== -->
                    <div id="reslib-tab-res" class="reslib-tab-content">
                        <div class="search-and-content-wrpr" style="padding:14px 6px 0;">
                            <a href="#" id="listresource" style="display:none;"></a>
                            <div class="search-and-actions-wrpr row">
                                <div class="col-md-3 col-sm-3">
                                    <label style="font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;display:block;">Resource Type</label>
                                    <select id="searchresourcetype" class="form-control">
                                        <option value="0">-- All Types --</option>
                                        <?php foreach ($resourceTypes as $rt): ?>
                                        <option value="<?php echo $rt->ResourceType_Id; ?>"><?php echo htmlspecialchars($rt->Name, ENT_QUOTES); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-9 col-sm-9 text-right" style="padding-top:22px;">
                                    <button type="button" id="addresource" class="btn btn-sm" style="background-color:#072c47;color:#fff;border-color:#072c47;border-radius:20px;padding:5px 22px;"><span class="icon-plus"></span> Add</button>
                                </div>
                            </div>
                            <div class="content-wrpr">
                                <!-- Add form -->
                                <div class="add-form add-resource-form" style="display:none;">
                                    <div class="row">
                                        <form id="addresourceform">
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Resource Type</label>
                                                    <select id="addresourcetype" class="form-control">
                                                        <option value="0">-- Select Type --</option>
                                                        <?php foreach ($resourceTypes as $rt): ?>
                                                        <option value="<?php echo $rt->ResourceType_Id; ?>"><?php echo htmlspecialchars($rt->Name, ENT_QUOTES); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Resource Group</label>
                                                    <select id="addresourcegroup" class="form-control">
                                                        <option value="0">-- Select Group --</option>
                                                        <?php foreach ($resourceGroups as $rg): ?>
                                                        <option value="<?php echo $rg->Resource_group_Id; ?>"><?php echo htmlspecialchars($rg->Resource_group_Name, ENT_QUOTES); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Resource Name</label>
                                                    <input id="resourcename1" type="text" class="form-control" placeholder="Resource Name">
                                                    <span class="error" style="display:none;"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="resourceunit1-wrap">
                                                <div class="form-group">
                                                    <label>Unit</label>
                                                    <input id="resourceunit1" type="text" class="form-control" placeholder="e.g. kg, m, nos">
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-left">
                                                <label>&nbsp;</label><br>
                                                <button type="button" id="cancelresource" class="btn btn-sm" style="border-radius:20px;padding:6px 14px;background-color:#d9534f!important;color:#fff!important;border-color:#d43f3a!important;margin-right:4px;">Cancel</button>
                                                <button type="button" id="saveresource" class="btn btn-sm" style="border-radius:20px;padding:6px 14px;background-color:#072c47!important;color:#fff!important;border-color:#072c47!important;">Add</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- Edit form -->
                                <div class="edit-form edit-resource-form" style="display:none;padding:10px 0;">
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Resource Name</label>
                                                <input class="form-control" id="editresourcename" placeholder="Enter resource name" type="text">
                                                <span class="error" style="display:none;"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Unit</label>
                                                <input class="form-control" id="editresourceunit" placeholder="e.g. kg, m, nos" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Resource Type</label>
                                                <select id="editresourcetype" class="form-control">
                                                    <option value="0">-- Select Type --</option>
                                                    <?php foreach ($resourceTypes as $rt): ?>
                                                    <option value="<?php echo $rt->ResourceType_Id; ?>"><?php echo htmlspecialchars($rt->Name, ENT_QUOTES); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Resource Group</label>
                                                <select id="editresourcegroup" class="form-control">
                                                    <option value="0">-- Select Group --</option>
                                                    <?php foreach ($resourceGroups as $rg): ?>
                                                    <option value="<?php echo $rg->Resource_group_Id; ?>"><?php echo htmlspecialchars($rg->Resource_group_Name, ENT_QUOTES); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-left">
                                            <label>&nbsp;</label><br>
                                            <button type="button" class="btn btn-danger" id="cancelresources"><span class="icon-close"></span> Cancel</button>
                                            <button type="button" class="btn btn-primary" id="saveresourcebutton"><span class="icon-check"></span> Save</button>
                                            <input type="hidden" id="saveresourceval" value="">
                                        </div>
                                    </div>
                                </div>
                                <!-- Preloader -->
                                <div class="preloader-resource" style="display:none;text-align:center;padding:20px 0;">
                                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif">
                                </div>
                                <!-- List -->
                                <div id="resourcelistsection" class="data-content-list"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
