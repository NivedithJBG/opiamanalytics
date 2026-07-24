<?php
use app\models\Resourcetype;
use app\models\ResourceGroup;
$resourceTypes  = Resourcetype::find()->where(['Status' => 0])->orderBy(['sortorder' => SORT_ASC, 'Name' => SORT_ASC])->all();
$resourceGroups = ResourceGroup::find()->where(['status' => 0])->orderBy(['RG_sortorder' => SORT_ASC, 'Resource_group_Name' => SORT_ASC])->all();
?>

<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/resourcetype.js?v=6" type="text/javascript"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/resourcegroup.js?v=2" type="text/javascript"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/resource.js?v=2" type="text/javascript"></script>

<style>
/* ── shared resize handles ──────────────────────────────────── */
.rl-rs { position:absolute; z-index:10; background:transparent; }
.rl-rs-e  { right:0;  top:6px;    bottom:6px; width:6px;  cursor:e-resize; }
.rl-rs-w  { left:0;   top:6px;    bottom:6px; width:6px;  cursor:w-resize; }
.rl-rs-s  { bottom:0; left:6px;   right:6px;  height:6px; cursor:s-resize; }
.rl-rs-n  { top:0;    left:6px;   right:6px;  height:6px; cursor:n-resize; }
.rl-rs-se { right:0;  bottom:0; width:14px; height:14px; cursor:se-resize; }
.rl-rs-sw { left:0;   bottom:0; width:14px; height:14px; cursor:sw-resize; }
.rl-rs-ne { right:0;  top:0;    width:14px; height:14px; cursor:ne-resize; }
.rl-rs-nw { left:0;   top:0;    width:14px; height:14px; cursor:nw-resize; }

/* ── Resource Library main popup ──────────────────────────── */
#reslib-win {
  display:none; position:fixed;
  top:80px; left:calc(50% - 480px);
  width:960px; height:calc(100vh - 110px);
  min-width:480px; min-height:280px;
  background:#fff; border-radius:8px;
  box-shadow:0 8px 32px rgba(0,0,0,.35);
  z-index:10003; flex-direction:column; overflow:hidden;
}
#reslib-win.rl-open { display:flex; }

#reslib-win-hdr {
  background:#fff; color:#333; padding:10px 15px;
  display:flex; align-items:center; justify-content:space-between;
  cursor:move; user-select:none; flex-shrink:0;
  border-bottom:1px solid #e5e5e5;
}
#reslib-win-hdr-btns { display:flex; align-items:center; gap:4px; }
#reslib-win-hdr-btns button {
  background:none; border:none; color:#333;
  font-size:22px; line-height:1; cursor:pointer; padding:0 4px; opacity:.6;
}
#reslib-win-hdr-btns button:hover { opacity:1; }

#reslib-action-bar {
  display:flex; align-items:center; gap:4px; flex-wrap:nowrap;
  padding:8px 15px; border-bottom:1px solid #e0f2f1; flex-shrink:0;
  background:#f5fffe;
}
#reslib-action-bar .rl-type-btn {
  background:#00796b; color:#fff !important; border:1px solid #00695c;
  border-radius:20px; padding:5px 16px; font-size:13px;
  cursor:pointer; text-decoration:none !important; display:inline-block; line-height:1.5;
}
#reslib-action-bar .rl-type-btn:hover { background:#00695c; color:#fff !important; }
#reslib-action-bar .rl-action-btn {
  background:#004d40; color:#fff !important; border:1px solid #003d33;
  border-radius:20px; padding:5px 16px; font-size:13px;
  cursor:pointer; text-decoration:none !important; display:inline-block; line-height:1.5;
}
#reslib-action-bar .rl-action-btn:hover { background:#003d33; color:#fff !important; }

#reslib-body { flex:1; min-height:0; overflow-y:auto; padding:14px 18px; }
.edit-form { display:none; padding:10px 0; }

/* ── Sub-popup shared styles ──────────────────────────────── */
.rl-subwin {
  display:none; position:fixed;
  min-width:360px; min-height:220px;
  background:#fff; border-radius:8px;
  box-shadow:0 10px 40px rgba(0,0,0,.45);
  z-index:200000; flex-direction:column; overflow:hidden;
}
.rl-subwin.rl-sw-open { display:flex; }

.rl-sw-hdr {
  background:#e0f2f1; padding:10px 14px;
  display:flex; align-items:center; justify-content:space-between;
  cursor:move; user-select:none; flex-shrink:0;
  border-bottom:3px solid #00796b;
}
.rl-sw-hdr h4 { margin:0; color:#004d40; font-weight:700; font-size:15px; }
.rl-sw-hdr-btns { display:flex; align-items:center; gap:4px; }
.rl-sw-hdr-btns button {
  background:none; border:none; color:#004d40;
  font-size:22px; line-height:1; cursor:pointer; padding:0 4px; opacity:.7;
}
.rl-sw-hdr-btns button:hover { opacity:1; }

.rl-sw-body { padding:16px; overflow-y:auto; flex:1; min-height:0; }
.rl-sw-footer {
  padding:10px 16px; border-top:1px solid #e5e5e5;
  text-align:right; flex-shrink:0; background:#f5fffe;
}
.rl-modal-save {
  background:#00796b !important; color:#fff !important;
  border-color:#00695c !important; border-radius:20px !important;
}
.rl-modal-save:hover { background:#00695c !important; }
</style>

<!-- ══ RESOURCE LIBRARY MAIN POPUP ══════════════════════════════ -->
<div id="reslib-win">
  <div class="rl-rs rl-rs-n" data-dir="n"></div><div class="rl-rs rl-rs-s" data-dir="s"></div>
  <div class="rl-rs rl-rs-e" data-dir="e"></div><div class="rl-rs rl-rs-w" data-dir="w"></div>
  <div class="rl-rs rl-rs-ne" data-dir="ne"></div><div class="rl-rs rl-rs-nw" data-dir="nw"></div>
  <div class="rl-rs rl-rs-se" data-dir="se"></div><div class="rl-rs rl-rs-sw" data-dir="sw"></div>

  <div id="reslib-win-hdr">
    <span style="font-size:18px;font-weight:700;color:#004d40;">Resource Library</span>
    <div id="reslib-win-hdr-btns">
      <button id="reslib-expand" title="Fullscreen">&#x26F6;</button>
      <button id="reslib-close" title="Close">&times;</button>
    </div>
  </div>

  <div id="reslib-action-bar">
    <a href="#" class="rl-type-btn"   id="rlOpenResTypePopup">+ Resource Type</a>
    <a href="#" class="rl-type-btn"   id="rlOpenResGroupPopup">+ Resource Group</a>
    <a href="#" class="rl-action-btn" id="rlOpenResourcePopup"><span class="icon-add"></span> Resource</a>
    <a href="#" class="rl-action-btn" id="rl-list-btn"><span class="icon-th-list"></span> List</a>
    <select id="searchresourcetype" style="display:none;margin-left:8px;width:150px;border-radius:20px;height:32px;border:1px solid #b2dfdb;padding:0 10px;font-size:13px;">
      <option value="0">All Types</option>
      <?php foreach ($resourceTypes as $rt): ?>
      <option value="<?php echo $rt->ResourceType_Id; ?>"><?php echo htmlspecialchars($rt->Name, ENT_QUOTES); ?></option>
      <?php endforeach; ?>
    </select>
    <select id="searchresourcegroup" style="display:none;margin-left:4px;width:160px;border-radius:20px;height:32px;border:1px solid #b2dfdb;padding:0 10px;font-size:13px;">
      <option value="0">All Groups</option>
    </select>
    <button id="rl-resource-search-btn" style="display:none;height:32px;padding:0 12px;border-radius:20px;background:#00796b;color:#fff;border:none;cursor:pointer;font-size:13px;"><span class="icon-search5"></span></button>
  </div>

  <div id="reslib-body">
    <a href="#" id="listrestype"  style="display:none;"></a>
    <a href="#" id="listresgroup" style="display:none;"></a>
    <a href="#" id="listresource" style="display:none;"></a>

    <div class="edit-form edit-resource-type-form">
      <div class="row"><div class="col-md-1"></div>
        <div class="col-md-5"><div class="form-group"><label>Resource Type Name</label>
          <input class="form-control" id="restypenames" placeholder="Enter resource type name" type="text">
          <span class="error" style="display:none;color:red;"></span></div></div>
        <div class="col-md-6 text-left"><label>&nbsp;</label><br>
          <button type="button" class="btn btn-danger" id="cancelrestypes"><span class="icon-close"></span> Cancel</button>
          <button type="button" class="btn btn-primary" id="saveresourcetypebutton"><span class="icon-check"></span> Save Changes</button>
          <input type="hidden" id="saveresourcetypeval" value=""></div>
      </div>
    </div>

    <div class="edit-form edit-resource-group-form">
      <div class="row"><div class="col-md-1"></div>
        <div class="col-md-4"><div class="form-group"><label>Resource Group Name</label>
          <input class="form-control" id="resgroupnames" placeholder="Enter resource group name" type="text">
          <span class="error" style="display:none;color:red;"></span></div></div>
        <div class="col-md-4"><div class="form-group"><label>Resource Type</label>
          <select id="editresgrouptype" class="form-control"><option value="0">-- Select Type --</option>
            <?php foreach ($resourceTypes as $rt): ?>
            <option value="<?php echo $rt->ResourceType_Id; ?>"><?php echo htmlspecialchars($rt->Name, ENT_QUOTES); ?></option>
            <?php endforeach; ?>
          </select></div></div>
        <div class="col-md-3 text-left"><label>&nbsp;</label><br>
          <button type="button" class="btn btn-danger" id="cancelresgroups"><span class="icon-close"></span> Cancel</button>
          <button type="button" class="btn btn-primary" id="saveresgroupbutton"><span class="icon-check"></span> Save Changes</button>
          <input type="hidden" id="saveresgroupval" value=""></div>
      </div>
    </div>

    <div class="edit-form edit-resource-form">
      <div class="row"><div class="col-md-1"></div>
        <div class="col-md-2"><div class="form-group"><label>Resource Name</label>
          <input class="form-control" id="editresourcename" placeholder="Enter resource name" type="text">
          <span class="error" style="display:none;color:red;"></span></div></div>
        <div class="col-md-2"><div class="form-group"><label>Unit</label>
          <input class="form-control" id="editresourceunit" placeholder="e.g. kg, m, nos" type="text"></div></div>
        <div class="col-md-2"><div class="form-group"><label>Resource Type</label>
          <select id="editresourcetype" class="form-control"><option value="0">-- Select Type --</option>
            <?php foreach ($resourceTypes as $rt): ?>
            <option value="<?php echo $rt->ResourceType_Id; ?>"><?php echo htmlspecialchars($rt->Name, ENT_QUOTES); ?></option>
            <?php endforeach; ?>
          </select></div></div>
        <div class="col-md-2"><div class="form-group"><label>Resource Group</label>
          <select id="editresourcegroup" class="form-control"><option value="0">-- Select Group --</option>
            <?php foreach ($resourceGroups as $rg): ?>
            <option value="<?php echo $rg->Resource_group_Id; ?>"><?php echo htmlspecialchars($rg->Resource_group_Name, ENT_QUOTES); ?></option>
            <?php endforeach; ?>
          </select></div></div>
        <div class="col-md-3 text-left"><label>&nbsp;</label><br>
          <button type="button" class="btn btn-danger" id="cancelresources"><span class="icon-close"></span> Cancel</button>
          <button type="button" class="btn btn-primary" id="saveresourcebutton"><span class="icon-check"></span> Save</button>
          <input type="hidden" id="saveresourceval" value=""></div>
      </div>
    </div>

    <div class="preloader"          style="display:none;text-align:center;padding:20px 0;"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif"></div>
    <div class="preloader-group"    style="display:none;text-align:center;padding:20px 0;"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif"></div>
    <div class="preloader-resource" style="display:none;text-align:center;padding:20px 0;"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif"></div>

    <div id="restypelistsection"  class="data-content-list"></div>
    <div id="resgrouplistsection" class="data-content-list"></div>
    <div id="resourcelistsection" class="data-content-list"></div>
  </div>

  <div style="padding:8px 15px;border-top:1px solid #e5e5e5;text-align:right;flex-shrink:0;background:#f5fffe;">
    <button id="reslib-close-footer" class="btn" style="background:#e67e22;color:#fff;border-color:#d35400;border-radius:4px;padding:6px 14px;"><span class="icon-close"></span> Close</button>
  </div>
</div>


<!-- ══ RESOURCE TYPE SUB-POPUP ══════════════════════════════════ -->
<div id="rlResTypePopup" class="rl-subwin" style="top:120px;left:calc(50% - 320px);width:640px;height:480px;">
  <div class="rl-rs rl-rs-n" data-dir="n"></div><div class="rl-rs rl-rs-s" data-dir="s"></div>
  <div class="rl-rs rl-rs-e" data-dir="e"></div><div class="rl-rs rl-rs-w" data-dir="w"></div>
  <div class="rl-rs rl-rs-ne" data-dir="ne"></div><div class="rl-rs rl-rs-nw" data-dir="nw"></div>
  <div class="rl-rs rl-rs-se" data-dir="se"></div><div class="rl-rs rl-rs-sw" data-dir="sw"></div>
  <div class="rl-sw-hdr">
    <h4>Resource Type</h4>
    <div class="rl-sw-hdr-btns"><button class="rl-sw-close" data-target="rlResTypePopup">&times;</button></div>
  </div>
  <div class="rl-sw-body">
    <div class="row">
      <div class="col-md-7">
        <label>Resource Type Name</label>
        <input type="text" class="form-control" id="restypename1" placeholder="e.g. Labour, Material, Equipment">
        <span class="error" style="color:red;display:none;"></span>
      </div>
      <div class="col-md-5" style="padding-top:25px;">
        <button type="button" id="saverestype" class="btn rl-modal-save"><span class="icon-check"></span> Add Resource Type</button>
      </div>
    </div>
    <hr>
    <div id="rl-restype-modal-list"></div>
  </div>
  <div class="rl-sw-footer">
    <button type="button" class="btn rl-sw-close" data-target="rlResTypePopup" style="background:#e67e22;color:#fff;border-color:#d35400;border-radius:4px;"><span class="icon-close"></span> Close</button>
  </div>
</div>

<!-- ══ RESOURCE GROUP SUB-POPUP ════════════════════════════════ -->
<div id="rlResGroupPopup" class="rl-subwin" style="top:130px;left:calc(50% - 300px);width:620px;height:480px;">
  <div class="rl-rs rl-rs-n" data-dir="n"></div><div class="rl-rs rl-rs-s" data-dir="s"></div>
  <div class="rl-rs rl-rs-e" data-dir="e"></div><div class="rl-rs rl-rs-w" data-dir="w"></div>
  <div class="rl-rs rl-rs-ne" data-dir="ne"></div><div class="rl-rs rl-rs-nw" data-dir="nw"></div>
  <div class="rl-rs rl-rs-se" data-dir="se"></div><div class="rl-rs rl-rs-sw" data-dir="sw"></div>
  <div class="rl-sw-hdr">
    <h4>Resource Group</h4>
    <div class="rl-sw-hdr-btns"><button class="rl-sw-close" data-target="rlResGroupPopup">&times;</button></div>
  </div>
  <div class="rl-sw-body">
    <div class="row">
      <div class="col-md-4">
        <label>Resource Group Name</label>
        <input type="text" class="form-control" id="resgroupname1" placeholder="Group name">
        <span class="error" style="color:red;display:none;"></span>
      </div>
      <div class="col-md-4">
        <label>Resource Type</label>
        <select id="addresgrouptype" class="form-control">
          <option value="0">-- Select Type --</option>
          <?php foreach ($resourceTypes as $rt): ?>
          <option value="<?php echo $rt->ResourceType_Id; ?>"><?php echo htmlspecialchars($rt->Name, ENT_QUOTES); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4" style="padding-top:25px;">
        <button type="button" id="saveresgroup" class="btn rl-modal-save"><span class="icon-check"></span> Add Resource Group</button>
      </div>
    </div>
    <hr>
    <div id="rl-resgroup-modal-list"></div>
  </div>
  <div class="rl-sw-footer">
    <button type="button" class="btn rl-sw-close" data-target="rlResGroupPopup" style="background:#e67e22;color:#fff;border-color:#d35400;border-radius:4px;"><span class="icon-close"></span> Close</button>
  </div>
</div>

<!-- ══ RESOURCE SUB-POPUP ══════════════════════════════════════ -->
<div id="rlResourcePopup" class="rl-subwin" style="top:140px;left:calc(50% - 320px);width:660px;height:320px;">
  <div class="rl-rs rl-rs-n" data-dir="n"></div><div class="rl-rs rl-rs-s" data-dir="s"></div>
  <div class="rl-rs rl-rs-e" data-dir="e"></div><div class="rl-rs rl-rs-w" data-dir="w"></div>
  <div class="rl-rs rl-rs-ne" data-dir="ne"></div><div class="rl-rs rl-rs-nw" data-dir="nw"></div>
  <div class="rl-rs rl-rs-se" data-dir="se"></div><div class="rl-rs rl-rs-sw" data-dir="sw"></div>
  <div class="rl-sw-hdr">
    <h4>Add Resource</h4>
    <div class="rl-sw-hdr-btns"><button class="rl-sw-close" data-target="rlResourcePopup">&times;</button></div>
  </div>
  <div class="rl-sw-body">
    <form id="addresourceform">
      <div class="row">
        <div class="col-md-3"><div class="form-group"><label>Resource Type</label>
          <select id="addresourcetype" class="form-control"><option value="0">-- Select Type --</option>
            <?php foreach ($resourceTypes as $rt): ?>
            <option value="<?php echo $rt->ResourceType_Id; ?>"><?php echo htmlspecialchars($rt->Name, ENT_QUOTES); ?></option>
            <?php endforeach; ?>
          </select></div></div>
        <div class="col-md-3"><div class="form-group"><label>Resource Group</label>
          <select id="addresourcegroup" class="form-control"><option value="0">-- Select Group --</option>
            <?php foreach ($resourceGroups as $rg): ?>
            <option value="<?php echo $rg->Resource_group_Id; ?>"><?php echo htmlspecialchars($rg->Resource_group_Name, ENT_QUOTES); ?></option>
            <?php endforeach; ?>
          </select></div></div>
        <div class="col-md-3"><div class="form-group"><label>Resource Name</label>
          <input id="resourcename1" type="text" class="form-control" placeholder="Resource Name">
          <span class="error" style="display:none;color:red;"></span></div></div>
        <div class="col-md-3"><div class="form-group"><label>Unit</label>
          <input id="resourceunit1" type="text" class="form-control" placeholder="e.g. kg, m, nos">
          </div></div>
      </div>
    </form>
  </div>
  <div class="rl-sw-footer">
    <button type="button" class="btn rl-sw-close" data-target="rlResourcePopup" id="cancelresource" style="background:#d9534f;color:#fff;border-color:#d43f3a;border-radius:4px;"><span class="icon-close"></span> Cancel</button>
    <button type="button" id="saveresource" class="btn rl-modal-save"><span class="icon-check"></span> Add Resource</button>
  </div>
</div>


<script>
(function(){

  /* Fallback in case this script ever runs before main.php's shared
     bindDragTouch is defined — without this, a ReferenceError here would
     silently abort every click handler bound later in this file (Resource
     Library nav icon, sub-popup open buttons, etc). */
  if(typeof window.bindDragTouch !== 'function'){
    window.bindDragTouch = function(el, mouseType, handler, opts){
      if(el) el.addEventListener(mouseType, handler, opts);
    };
  }

  /* ═══════════════════════════════════════════════════════════════
     Generic drag+resize engine — works for any .rl-subwin or #reslib-win
  ═══════════════════════════════════════════════════════════════ */
  var _drag = null; /* { win, action, sx, sy, ox, oy, ow, oh } */

  function rlAnchor(win){
    var r = win.getBoundingClientRect();
    win.style.left   = r.left   + 'px';
    win.style.top    = r.top    + 'px';
    win.style.width  = r.width  + 'px';
    win.style.height = r.height + 'px';
    return r;
  }

  function rlBindDragResize(win, hdrSel, minW, minH){
    var hdr = win.querySelector(hdrSel);

    /* drag via header */
    bindDragTouch(hdr, 'mousedown', function(e){
      if(e.target.closest('.rl-sw-hdr-btns, #reslib-win-hdr-btns')) return;
      var r = rlAnchor(win);
      _drag = { win:win, action:'drag', sx:e.clientX, sy:e.clientY, ox:r.left, oy:r.top, ow:r.width, oh:r.height, mw:minW, mh:minH };
      e.preventDefault();
    });

    /* resize via handles */
    win.querySelectorAll('.rl-rs').forEach(function(el){
      bindDragTouch(el, 'mousedown', function(e){
        var r = rlAnchor(win);
        _drag = { win:win, action:el.dataset.dir, sx:e.clientX, sy:e.clientY, ox:r.left, oy:r.top, ow:r.width, oh:r.height, mw:minW, mh:minH };
        e.preventDefault(); e.stopPropagation();
      });
    });
  }

  bindDragTouch(document, 'mousemove', function(e){
    if(!_drag) return;
    e.preventDefault();
    var d = _drag, dx = e.clientX - d.sx, dy = e.clientY - d.sy;
    var l = d.ox, t = d.oy, w = d.ow, h = d.oh;
    if(d.action === 'drag'){
      l = Math.max(0, d.ox + dx);
      t = Math.max(0, d.oy + dy);
    } else {
      if(d.action.indexOf('e') > -1){ w = Math.max(d.mw, d.ow + dx); }
      if(d.action.indexOf('s') > -1){ h = Math.max(d.mh, d.oh + dy); }
      if(d.action.indexOf('w') > -1){ var nw = Math.max(d.mw, d.ow - dx); l = d.ox + (d.ow - nw); w = nw; }
      if(d.action.indexOf('n') > -1){ var nh = Math.max(d.mh, d.oh - dy); t = d.oy + (d.oh - nh); h = nh; }
    }
    d.win.style.left = l + 'px'; d.win.style.top  = t + 'px';
    d.win.style.width = w + 'px'; d.win.style.height = h + 'px';
  }, { passive: false });
  bindDragTouch(document, 'mouseup', function(){ _drag = null; });


  /* ═══════════════════════════════════════════════════════════════
     Main Resource Library popup
  ═══════════════════════════════════════════════════════════════ */
  var win = document.getElementById('reslib-win');
  var _saved = null;

  rlBindDragResize(win, '#reslib-win-hdr', 420, 280);

  function openReslib(){
    win.classList.add('rl-open');
    document.dispatchEvent(new Event('reslib:open'));
  }
  function closeReslib(){
    win.classList.remove('rl-open');
    document.querySelectorAll('.reslib-btn').forEach(function(el){ el.classList.remove('active'); });
  }

  document.getElementById('reslib-close').addEventListener('click', closeReslib);
  document.getElementById('reslib-close-footer').addEventListener('click', closeReslib);

  document.getElementById('reslib-expand').addEventListener('click', function(){
    if(_saved){
      win.style.left = _saved.left; win.style.top  = _saved.top;
      win.style.width = _saved.width; win.style.height = _saved.height;
      _saved = null; this.innerHTML = '&#x26F6;'; this.title = 'Fullscreen';
    } else {
      rlAnchor(win);
      _saved = { left:win.style.left, top:win.style.top, width:win.style.width, height:win.style.height };
      win.style.left = '0'; win.style.top = '0';
      win.style.width = '100vw'; win.style.height = '100vh';
      this.innerHTML = '&#x2716;'; this.title = 'Restore';
    }
  });

  $(document).on('click', '.navbar-nav .reslib-btn', function(e){
    e.preventDefault();
    if(win.classList.contains('rl-open')){ closeReslib(); return; }
    var others = { '.overNow4':'.menu4-popup-cntnr', '.overNow2':'.finmenu-popup-cntnr', '.overNow8':'.menu8-popup-cntnr', '.overNow':'.menu-popup-cntnr' };
    $.each(others, function(cls, cntnr){
      if($(cls).hasClass('active')){ $(cls).removeClass('active'); $(cntnr).removeClass('active'); $('body').css('overflow-y','auto'); }
    });
    openReslib();
    $(this).addClass('active');
  });

  document.getElementById('rl-list-btn').addEventListener('click', function(e){
    e.preventDefault();
    $('#searchresourcetype').show();
    $('#searchresourcegroup').show();
    $('#rl-resource-search-btn').show();
    setTimeout(function(){ $('#listresource').trigger('click'); }, 100);
  });
  $(document).on('click','#rl-resource-search-btn', function(){
    setTimeout(function(){ $('#listresource').trigger('click'); }, 100);
  });



  /* ═══════════════════════════════════════════════════════════════
     Sub-popup open / close
  ═══════════════════════════════════════════════════════════════ */
  var _swZ = 200000;

  var _rlSubIds = ['rlResTypePopup','rlResGroupPopup','rlResourcePopup'];
  function rlSwOpen(id, onOpen){
    var el = document.getElementById(id);
    if(!el) return;
    el.classList.add('rl-sw-open');
    el.style.zIndex = ++_swZ; /* bring to front among sub-popups */
    if(typeof window.cascadeSubWindow === 'function') window.cascadeSubWindow(el, 'reslib', _rlSubIds);
    if(typeof onOpen === 'function') onOpen();
  }
  function rlSwClose(id, onClose){
    var el = document.getElementById(id);
    if(el) el.classList.remove('rl-sw-open');
    if(typeof onClose === 'function') onClose();
  }

  /* bring sub-popup to front on click */
  ['rlResTypePopup','rlResGroupPopup','rlResourcePopup'].forEach(function(id){
    var el = document.getElementById(id);
    if(el) bindDragTouch(el, 'mousedown', function(){ el.style.zIndex = ++_swZ; }, true);
  });

  /* open buttons */
  document.getElementById('rlOpenResTypePopup').addEventListener('click', function(e){
    e.preventDefault();
    rlSwOpen('rlResTypePopup', function(){
      document.getElementById('restypename1').value = '';
      document.getElementById('restypename1').focus();
      rlLoadResTypeList();
    });
  });
  document.getElementById('rlOpenResGroupPopup').addEventListener('click', function(e){
    e.preventDefault();
    rlSwOpen('rlResGroupPopup', function(){
      document.getElementById('resgroupname1').value = '';
      document.getElementById('resgroupname1').focus();
      rlLoadResGroupList();
    });
  });
  document.getElementById('rlOpenResourcePopup').addEventListener('click', function(e){
    e.preventDefault();
    rlSwOpen('rlResourcePopup', function(){
      document.getElementById('resourcename1').focus();
    });
  });

  /* close buttons (shared .rl-sw-close class, data-target = panel id) */
  $(document).on('click', '.rl-sw-close', function(){
    var id = $(this).data('target');
    rlSwClose(id, function(){
      if(id === 'rlResourcePopup' && $('#resourcelistsection').html().trim()){
        setTimeout(function(){ $('#listresource').trigger('click'); }, 200);
      }
    });
  });

  /* ── Sub-popup save handlers ─────────────────────────────────
     Replace the resourcetype.js / resourcegroup.js / resource.js
     handlers by unbinding them after DOM-ready and re-binding here.
     Those files target old tab-based DOM (#restypelistsection etc.)
     that no longer exists in this sub-popup layout.
  ─────────────────────────────────────────────────────────────── */
  $(function(){

    /* Unbind old handlers from the external JS files */
    $('#saverestype').off('click');
    $('#saveresgroup').off('click');
    $('#saveresource').off('click');
    $('#listrestype').off('click');
    $('#listresgroup').off('click');
    $('#listresource').off('click');
    $(document).off('change', '#searchresourcetype');  /* resource.js binds this too */

    /* Cascade: Resource Type → Resource Group in list filter bar */
    $(document).on('change', '#searchresourcetype', function(){
      var typeId = $(this).val();
      var $grp = $('#searchresourcegroup');
      $grp.html('<option value="0">All Groups</option>');
      if(typeId && typeId != '0'){
        $.ajax({ type:'POST', url:'../resourcegroup/getbytype', dataType:'json',
          data:{ restypeid: typeId },
          success:function(d){
            if(d && d.error === 'No' && d.groups){
              $.each(d.groups, function(i, g){
                $grp.append('<option value="'+g.Resource_group_Id+'">'+g.Resource_group_Name+'</option>');
              });
            }
            /* refresh list after groups are loaded */
            $('#listresource').trigger('click');
          }
        });
      } else {
        $('#listresource').trigger('click');
      }
    });
    $(document).on('change', '#searchresourcegroup', function(){
      $('#listresource').trigger('click');
    });

    /* ADD RESOURCE TYPE */
    $('#saverestype').on('click', function(){
      var name = $('#restypename1').val().trim();
      $('#restypename1').next('.error').hide();
      if(!name){ $('#restypename1').next('.error').text('Enter Resource Type Name').show(); return; }
      var $btn = $(this).attr('disabled', true);
      $.ajax({ type:'POST', url:'../resourcetype/create', dataType:'json',
        data:{ restypename: name, addacntgrp: 1 },
        success:function(d){
          $btn.attr('disabled', false);
          if(d && d.error === 'No'){
            $('#restypename1').val('').focus();
            rlLoadResTypeList();
            rlRefreshTypeDropdownsFromDB();
          } else { alert(d && d.errortext ? d.errortext : 'Could not save.'); }
        },
        error:function(){ $btn.attr('disabled', false); alert('Server error. Please try again.'); }
      });
    });

    /* ADD RESOURCE GROUP */
    $('#saveresgroup').on('click', function(){
      var name = $('#resgroupname1').val().trim();
      $('#resgroupname1').next('.error').hide();
      if(!name){ $('#resgroupname1').next('.error').text('Enter Resource Group Name').show(); return; }
      var $btn = $(this).attr('disabled', true);
      $.ajax({ type:'POST', url:'../resourcegroup/create', dataType:'json',
        data:{ resourcegroup: name, restypeid: $('#addresgrouptype').val() },
        success:function(d){
          $btn.attr('disabled', false);
          if(d && d.error === 'No'){
            $('#resgroupname1').val('').focus();
            $('#addresgrouptype').val('0');
            rlLoadResGroupList();
          } else { alert(d && d.errortext ? d.errortext : 'Could not save.'); }
        },
        error:function(){ $btn.attr('disabled', false); alert('Server error. Please try again.'); }
      });
    });

    /* ADD RESOURCE */
    $('#saveresource').on('click', function(){
      var name = $('#resourcename1').val().trim();
      $('#resourcename1').next('.error').hide();
      if(!name){ $('#resourcename1').next('.error').text('Enter Resource Name').show(); return; }
      var $btn = $(this).attr('disabled', true);
      $.ajax({ type:'POST', url:'../resources/create', dataType:'json',
        data:{
          resourcename: name,
          unit:         $('#resourceunit1').val().trim(),
          restypeid:    $('#addresourcetype').val(),
          resgroupid:   $('#addresourcegroup').val()
        },
        success:function(d){
          $btn.attr('disabled', false);
          if(d && d.error === 'No'){
            $('#resourcename1').val('').focus();
            $('#resourceunit1').val('');
            $('#addresourcetype').val('0');
            $('#addresourcegroup').html('<option value="0">-- Select Group --</option>');
            if($('#resourcelistsection').html().trim()){
              setTimeout(function(){ $('#listresource').trigger('click'); }, 200);
            }
          } else { alert(d && d.errortext ? d.errortext : 'Could not save.'); }
        },
        error:function(){ $btn.attr('disabled', false); alert('Server error. Please try again.'); }
      });
    });

    /* Rebind list triggers used by edit/delete handlers in the external JS files */
    $('#listrestype').on('click',  function(){ rlLoadResTypeList(); });
    $('#listresgroup').on('click', function(){ rlLoadResGroupList(); });
    $('#listresource').on('click', function(){
      $('.preloader-resource').show();
      $.ajax({ type:'POST', url:'../resources/search', dataType:'json',
        data:{
          restypeid:  $('#searchresourcetype').val() || 0,
          resgroupid: $('#searchresourcegroup').val() || 0
        },
        success:function(d){
          $('.preloader-resource').hide();
          if(d && d.error === 'No'){
            $('#resourcelistsection').html(d.resource);
          } else { alert(d && d.errortext ? d.errortext : 'Could not load.'); }
        },
        error:function(){ $('.preloader-resource').hide(); }
      });
    });

  });


  /* bind drag+resize to each sub-popup */
  $(function(){
    rlBindDragResize(document.getElementById('rlResTypePopup'),  '.rl-sw-hdr', 360, 220);
    rlBindDragResize(document.getElementById('rlResGroupPopup'), '.rl-sw-hdr', 360, 220);
    rlBindDragResize(document.getElementById('rlResourcePopup'), '.rl-sw-hdr', 360, 220);
  });


  /* ═══════════════════════════════════════════════════════════════
     List loaders — refresh sub-popup lists
  ═══════════════════════════════════════════════════════════════ */
  function rlLoadResTypeList(){
    var $list = $('#rl-restype-modal-list');
    $list.html('<div style="padding:8px;color:#999;">Loading…</div>');
    $.ajax({ type:'POST', url:'../resourcetype/search', dataType:'json', data:{ restypename:'' },
      success:function(d){
        $list.html((d&&d.error==='No'&&d.restype)?d.restype:'<div style="padding:8px;color:#999;">No resource types yet.</div>');
        /* also refresh all Resource Type <select> dropdowns with fresh data */
        rlRefreshTypeDropdownsFromDB();
      },
      error:function(){ $list.html('<div style="padding:8px;color:red;">Could not load.</div>'); }
    });
  }

  function rlLoadResGroupList(){
    var $list = $('#rl-resgroup-modal-list');
    $list.html('<div style="padding:8px;color:#999;">Loading…</div>');
    $.ajax({ type:'POST', url:'../resourcegroup/search', dataType:'json', data:{ resgroupname:'' },
      success:function(d){
        $list.html((d&&d.error==='No'&&d.resgroup)?d.resgroup:'<div style="padding:8px;color:#999;">No resource groups yet.</div>');
      },
      error:function(){ $list.html('<div style="padding:8px;color:red;">Could not load.</div>'); }
    });
  }

  /* Rebuild all Resource Type dropdowns from a fresh DB fetch */
  function rlRefreshTypeDropdownsFromDB(){
    $.ajax({ type:'POST', url:'../resourcetype/getoptions', dataType:'json',
      success:function(d){
        if(!d || d.error !== 'No' || !d.options) return;
        var opts = '<option value="0">-- Select Type --</option>' + d.options;
        var allOpts = '<option value="0">All Types</option>' + d.options;
        /* type-filter in action bar */
        var $sf = $('#searchresourcetype');
        var sfVal = $sf.val();
        $sf.html(allOpts).val(sfVal || '0');
        /* sub-popup: Resource Group add form */
        ['addresgrouptype','addresourcetype'].forEach(function(id){
          $('#'+id).html(opts);
        });
        /* main-popup edit forms */
        ['editresgrouptype','editresourcetype'].forEach(function(id){
          var sel = document.getElementById(id);
          if(sel){ var cur = sel.value; sel.innerHTML = opts; sel.value = cur; }
        });
      }
    });
  }

})();
</script>
