<?php
$db           = Yii::$app->db;
$resourceTypes = $db->createCommand("SELECT ResourceType_Id, Name FROM resourcetype WHERE Status = 0 ORDER BY sortorder ASC, Name ASC")->queryAll();
?>
<style>
.vendorlib-popup-cntnr {
    position: fixed;
    background: #fff;
    top: 86px;
    bottom: 0;
    overflow: auto;
    left: 0;
    right: 0;
    z-index: -1;
    opacity: 0;
    transition: all .3s linear;
    -o-transition: all .3s linear;
    -moz-transition: all .3s linear;
    -webkit-transition: all .3s linear;
    transform: translateY(100px);
}
.vendorlib-popup-cntnr.active {
    z-index: 1000;
    opacity: 1;
    transform: translateY(0px);
}
</style>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/over_menu_vendorlibrary.js" type="text/javascript"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/vendorslib.js" type="text/javascript"></script>

<div class="vendorlib-popup-cntnr">
    <div id="vendorlib-heading" style="background:#072c47;color:#fff;padding:10px 20px;font-size:15px;font-weight:600;letter-spacing:0.5px;">VENDOR</div>
    <div class="container-fluid procu-accordion" style="margin-top:15px;">
        <div class="col-md-12">

            <a href="#" id="listvendorslib" style="display:none;"></a>

            <!-- ── Search + Add bar ── -->
            <div class="search-and-actions-wrpr row" id="vendor-search-bar">
                <div class="col-md-2">
                    <select id="search-restype" class="form-control">
                        <option value="0">-- Resource Type --</option>
                        <?php foreach ($resourceTypes as $rt): ?>
                        <option value="<?php echo (int)$rt['ResourceType_Id']; ?>"><?php echo htmlspecialchars($rt['Name'], ENT_QUOTES); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="search-resid" class="form-control" disabled>
                        <option value="0">-- Resource --</option>
                    </select>
                </div>
                <div class="content-search-wrpr col-md-4">
                    <input type="text" placeholder="Search Vendor..." id="searchvendorlibname" class="form-control">
                    <button id="vendorlibsearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                </div>
                <div class="content-action-wrpr col-md-4 text-right" style="padding-top:4px;">
                    <button type="button" id="addvendorlibbtn" class="btn btn-sm" style="background-color:#072c47;color:#fff;border-color:#072c47;border-radius:20px;padding:5px 22px;"><span class="icon-plus"></span> Add Vendor</button>
                </div>
            </div>

            <!-- ── Add / Edit form ── -->
            <div class="add-form add-vendor-form" style="display:none;">
                <form id="addvendorform">
                    <input type="hidden" id="vlf-id" value="">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Vendor Name</label>
                                <input type="text" class="form-control" id="vlf-name" placeholder="Vendor Name">
                                <span class="error" style="display:none;"></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Contact Person</label>
                                <input type="text" class="form-control" id="vlf-contact" placeholder="Contact Name">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" class="form-control" id="vlf-address" placeholder="Address">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Phone No</label>
                                <input type="tel" class="form-control" id="vlf-phone" placeholder="Phone" oninput="this.value=this.value.replace(/[^0-9+\-\s\(\)]/g,'')">
                                <span class="error" id="vlf-phone-error" style="display:none;"></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" id="vlf-email" placeholder="Email">
                                <span class="error" id="vlf-email-error" style="display:none;"></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Resource Type</label>
                                <select class="form-control" id="vlf-restype">
                                    <option value="0">-- Select --</option>
                                    <?php foreach ($resourceTypes as $rt): ?>
                                    <option value="<?php echo (int)$rt['ResourceType_Id']; ?>"><?php echo htmlspecialchars($rt['Name'], ENT_QUOTES); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Resource Name</label>
                                <select class="form-control" id="vlf-resid">
                                    <option value="0">-- Select Resource Type first --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">
                            <button type="button" id="vlf-cancel" class="btn btn-sm" style="border-radius:20px;padding:6px 20px;background-color:#d9534f!important;color:#fff!important;border-color:#d43f3a!important;margin-right:6px;">Cancel</button>
                            <button type="button" id="vlf-save" class="btn btn-sm" style="border-radius:20px;padding:6px 20px;background-color:#072c47!important;color:#fff!important;border-color:#072c47!important;">Add Vendor</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ── Preloader + Permanent header + List ── -->
            <div class="preloader-vendorlib" style="display:none;text-align:center;padding:20px 0;">
                <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif">
            </div>
            <table style="width:100%;border-collapse:collapse;margin-top:10px;table-layout:fixed;">
                <colgroup>
                    <col style="width:40px;">
                    <col style="width:14%;">
                    <col style="width:16%;">
                    <col style="width:16%;">
                    <col style="width:13%;">
                    <col style="width:11%;">
                    <col style="width:18%;">
                    <col style="width:80px;">
                </colgroup>
                <thead>
                    <tr>
                        <th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;font-size:13px;text-align:center;">#</th>
                        <th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;font-size:13px;">Resource Name</th>
                        <th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;font-size:13px;">Vendor</th>
                        <th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;font-size:13px;">Address</th>
                        <th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;font-size:13px;">Contact Person</th>
                        <th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;font-size:13px;">Phone No</th>
                        <th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;font-size:13px;">Email</th>
                        <th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;font-size:13px;text-align:center;"></th>
                    </tr>
                </thead>
                <tbody id="vendor-list-body">
                    <tr><td colspan="8" style="text-align:center;padding:30px;color:#aaa;font-size:13px;border:1px solid #eee;">Select a Resource Type to view vendors</td></tr>
                </tbody>
            </table>
            <div id="vendorliblistsection" style="display:none;"></div>

        </div>
    </div>
</div>
