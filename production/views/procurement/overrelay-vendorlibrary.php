<?php
$db           = Yii::$app->db;
$resourceTypes = $db->createCommand("SELECT ResourceType_Id, Name FROM resourcetype WHERE Status = 0 ORDER BY sortorder ASC, Name ASC")->queryAll();
?>
<style>
/* ── shared resize handles (reused from Resource Library's .rl-rs pattern) ── */
.rl-rs { position:absolute; z-index:10; background:transparent; }
.rl-rs-e  { right:0;  top:6px;    bottom:6px; width:6px;  cursor:e-resize; }
.rl-rs-w  { left:0;   top:6px;    bottom:6px; width:6px;  cursor:w-resize; }
.rl-rs-s  { bottom:0; left:6px;   right:6px;  height:6px; cursor:s-resize; }
.rl-rs-n  { top:0;    left:6px;   right:6px;  height:6px; cursor:n-resize; }
.rl-rs-se { right:0;  bottom:0; width:14px; height:14px; cursor:se-resize; }
.rl-rs-sw { left:0;   bottom:0; width:14px; height:14px; cursor:sw-resize; }
.rl-rs-ne { right:0;  top:0;    width:14px; height:14px; cursor:ne-resize; }
.rl-rs-nw { left:0;   top:0;    width:14px; height:14px; cursor:nw-resize; }

/* ── Vendor Library floating window ─────────────────────────── */
#vendorlib-win {
  display:none; position:fixed;
  top:80px; left:calc(50% - 590px);
  width:1180px; height:calc(100vh - 110px);
  min-width:480px; min-height:280px;
  background:#fff; border-radius:8px;
  box-shadow:0 8px 32px rgba(0,0,0,.35);
  z-index:10003; flex-direction:column; overflow:hidden;
}
#vendorlib-win.rl-open { display:flex; }

#vendorlib-win-hdr {
  background:#fff; color:#333; padding:5px 15px;
  display:flex; align-items:center; justify-content:space-between;
  cursor:move; user-select:none; flex-shrink:0;
  border-bottom:1px solid #e5e5e5;
}
#vendorlib-win-hdr-btns { display:flex; align-items:center; gap:4px; }
#vendorlib-win-hdr-btns button {
  background:none; border:none; color:#333;
  font-size:22px; line-height:1; cursor:pointer; padding:0 4px; opacity:.6;
}
#vendorlib-win-hdr-btns button:hover { opacity:1; }

#vendorlib-action-bar {
  display:flex; align-items:center; gap:4px; flex-wrap:nowrap;
  padding:8px 15px; border-bottom:1px solid #e0f2f1; flex-shrink:0;
  background:#f5fffe;
}
#vendorlib-action-bar .rl-action-btn {
  background:#004d40; color:#fff !important; border:1px solid #003d33;
  border-radius:20px; padding:5px 16px; font-size:13px;
  cursor:pointer; text-decoration:none !important; display:inline-block; line-height:1.5;
}
#vendorlib-action-bar .rl-action-btn:hover { background:#003d33; color:#fff !important; }

#vendorlib-body { flex:1; min-height:0; overflow-y:auto; padding:14px 18px; }
</style>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/over_menu_vendorlibrary.js?v=<?php echo time(); ?>" type="text/javascript"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/vendorslib.js?v=<?php echo time(); ?>" type="text/javascript"></script>

<!-- ══ VENDOR LIBRARY MAIN POPUP ══════════════════════════════ -->
<div id="vendorlib-win">
  <div class="rl-rs rl-rs-n" data-dir="n"></div><div class="rl-rs rl-rs-s" data-dir="s"></div>
  <div class="rl-rs rl-rs-e" data-dir="e"></div><div class="rl-rs rl-rs-w" data-dir="w"></div>
  <div class="rl-rs rl-rs-ne" data-dir="ne"></div><div class="rl-rs rl-rs-nw" data-dir="nw"></div>
  <div class="rl-rs rl-rs-se" data-dir="se"></div><div class="rl-rs rl-rs-sw" data-dir="sw"></div>

  <div id="vendorlib-win-hdr">
    <span style="font-size:18px;font-weight:700;color:#004d40;">Vendor Library</span>
    <div id="vendorlib-win-hdr-btns">
      <button id="vendorlib-close" title="Close">&times;</button>
    </div>
  </div>

  <div id="vendorlib-action-bar">
    <a href="#" class="rl-action-btn" id="addvendorlibbtn"><span class="icon-add"></span> Add Vendor</a>
  </div>

  <div id="vendorlib-body">

        <a href="#" id="listvendorslib" style="display:none;"></a>

        <!-- ── Search bar ── -->
        <div class="search-and-actions-wrpr" id="vendor-search-bar" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
            <select id="search-restype" class="form-control" style="width:160px;flex-shrink:0;">
                <option value="0">-- Resource Type --</option>
                <?php foreach ($resourceTypes as $rt): ?>
                <option value="<?php echo (int)$rt['ResourceType_Id']; ?>"><?php echo htmlspecialchars($rt['Name'], ENT_QUOTES); ?></option>
                <?php endforeach; ?>
            </select>
            <select id="search-resid" class="form-control" disabled style="width:160px;flex-shrink:0;">
                <option value="0">-- Resource --</option>
            </select>
            <div class="content-search-wrpr" style="display:inline-flex;align-items:center;gap:3px;">
                <input type="text" placeholder="Search Vendor..." id="searchvendorlibname" class="form-control" style="width:120px;border-radius:20px;height:34px;">
                <button id="vendorlibsearch" class="btn btn-primary" type="button" style="border-radius:20px;width:34px;height:34px;padding:0;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;"><span class="icon-search5"></span></button>
            </div>
        </div>

        <!-- ── Add / Edit form ── -->
        <div class="add-form add-vendor-form" style="display:none;">
            <form id="addvendorform">
                <input type="hidden" id="vlf-id" value="">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-2">
                        <div class="form-group" style="position:relative;">
                            <label>Vendor Name</label>
                            <input type="hidden" id="vlf-vendor-id" value="">
                            <input type="text" class="form-control" id="vlf-name" placeholder="Type to search or add new" autocomplete="off">
                            <div id="vlf-name-suggest" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:1100;background:#fff;border:1px solid #c5ccd4;border-top:none;max-height:180px;overflow-y:auto;box-shadow:0 3px 8px rgba(0,0,0,0.15);"></div>
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
                    <div class="col-md-2" id="vlf-resid-group">
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

        <!-- ── Preloader + List ── -->
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
