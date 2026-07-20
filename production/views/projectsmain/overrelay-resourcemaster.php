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
/* ── Resource Library floating popup ─────────────────────────── */
#reslib-win {
  display:none; position:fixed;
  top:80px; left:calc(50% - 500px);
  width:1000px; height:calc(100vh - 110px);
  min-width:620px; min-height:300px;
  background:#fff; border-radius:8px;
  box-shadow:0 8px 32px rgba(0,0,0,.35);
  z-index:10003; flex-direction:column; overflow:hidden;
}
#reslib-win.rl-open { display:flex; }

#reslib-win-hdr {
  background:#fff; color:#333; padding:10px 15px;
  display:flex; align-items:center; justify-content:space-between;
  cursor:move; user-select:none; flex-shrink:0;
  font-family:'Nunito',sans-serif;
  border-bottom:1px solid #e5e5e5;
}
#reslib-win-hdr-btns { display:flex; align-items:center; gap:4px; }
#reslib-win-hdr-btns button {
  background:none; border:none; color:#333;
  font-size:22px; line-height:1; cursor:pointer;
  padding:0 4px; opacity:.6;
}
#reslib-win-hdr-btns button:hover { opacity:1; }

/* action bar */
#reslib-action-bar {
  display:flex; align-items:center; gap:6px; flex-wrap:wrap;
  padding:10px 15px; border-bottom:1px solid #f0f0f0; flex-shrink:0;
}
#reslib-action-bar .rl-tab-btn {
  background:#6b7a93; color:#fff; border:1px solid #56657a;
  border-radius:20px; padding:5px 16px; font-size:13px;
  cursor:pointer; font-family:'Nunito',sans-serif;
}
#reslib-action-bar .rl-tab-btn:hover,
#reslib-action-bar .rl-tab-btn.active { background:#072c47; border-color:#051f33; }

/* scrollable body */
#reslib-body {
  flex:1; min-height:0; overflow-y:auto; padding:14px 18px;
}

/* section panels */
.rl-section { display:none; }
.rl-section.active { display:block; }

/* resize handles */
.rl-rs { position:absolute; z-index:10; background:transparent; }
.rl-rs-e  { right:0;  top:6px;    bottom:6px; width:6px;  cursor:e-resize; }
.rl-rs-w  { left:0;   top:6px;    bottom:6px; width:6px;  cursor:w-resize; }
.rl-rs-s  { bottom:0; left:6px;   right:6px;  height:6px; cursor:s-resize; }
.rl-rs-n  { top:0;    left:6px;   right:6px;  height:6px; cursor:n-resize; }
.rl-rs-se { right:0;  bottom:0; width:14px; height:14px; cursor:se-resize; }
.rl-rs-sw { left:0;   bottom:0; width:14px; height:14px; cursor:sw-resize; }
.rl-rs-ne { right:0;  top:0;    width:14px; height:14px; cursor:ne-resize; }
.rl-rs-nw { left:0;   top:0;    width:14px; height:14px; cursor:nw-resize; }
</style>

<div id="reslib-win">
  <!-- resize handles -->
  <div class="rl-rs rl-rs-n"  data-dir="n"></div>
  <div class="rl-rs rl-rs-s"  data-dir="s"></div>
  <div class="rl-rs rl-rs-e"  data-dir="e"></div>
  <div class="rl-rs rl-rs-w"  data-dir="w"></div>
  <div class="rl-rs rl-rs-ne" data-dir="ne"></div>
  <div class="rl-rs rl-rs-nw" data-dir="nw"></div>
  <div class="rl-rs rl-rs-se" data-dir="se"></div>
  <div class="rl-rs rl-rs-sw" data-dir="sw"></div>

  <!-- header -->
  <div id="reslib-win-hdr">
    <span style="font-size:18px;font-weight:700;color:#333;">Resource Library</span>
    <div id="reslib-win-hdr-btns">
      <button id="reslib-expand" title="Fullscreen">&#x26F6;</button>
      <button id="reslib-close" title="Close">&times;</button>
    </div>
  </div>

  <!-- action bar: tab buttons -->
  <div id="reslib-action-bar">
    <button class="rl-tab-btn" data-tab="reslib-type-section"><span class="icon-equalizer"></span> Resource Types</button>
    <button class="rl-tab-btn" data-tab="reslib-group-section"><span class="icon-folder"></span> Resource Groups</button>
    <button class="rl-tab-btn" data-tab="reslib-res-section"><span class="icon-settings"></span> Resources</button>
  </div>

  <!-- scrollable body with sections -->
  <div id="reslib-body">

    <!-- ── RESOURCE TYPES ── -->
    <div id="reslib-type-section" class="rl-section">
      <a href="#" id="listrestype" style="display:none;"></a>
      <div class="search-and-actions-wrpr row" style="margin-bottom:10px;">
        <div class="content-search-wrpr col-md-6 col-sm-6">
          <input type="text" placeholder="Search" id="searchrestypename" class="form-control">
          <button id="resourcetypesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
        </div>
        <div class="col-md-6 col-sm-6 text-right" style="padding-top:4px;">
          <button type="button" id="addrestype" class="btn btn-sm" style="background-color:#072c47;color:#fff;border-color:#072c47;border-radius:20px;padding:5px 22px;"><span class="icon-plus"></span> Add</button>
        </div>
      </div>

      <!-- Add form -->
      <div class="add-form add-resource-type-form" style="display:none;padding:10px 0;">
        <div class="row">
          <form id="addrestypeform">
            <div class="col-md-1"></div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Resource Type</label>
                <input id="restypename1" type="text" class="form-control" placeholder="Resource Type">
                <span class="error" style="display:none;color:red;"></span>
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
              <span class="error" style="display:none;color:red;"></span>
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

      <div class="preloader" style="display:none;text-align:center;padding:20px 0;">
        <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif">
      </div>
      <div id="restypelistsection" class="data-content-list"></div>
    </div>

    <!-- ── RESOURCE GROUPS ── -->
    <div id="reslib-group-section" class="rl-section">
      <a href="#" id="listresgroup" style="display:none;"></a>
      <div class="search-and-actions-wrpr row" style="margin-bottom:10px;">
        <div class="content-search-wrpr col-md-6 col-sm-6">
          <input type="text" placeholder="Search" id="searchresgroupname" class="form-control">
          <button id="resgroupsearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
        </div>
        <div class="col-md-6 col-sm-6 text-right" style="padding-top:4px;">
          <button type="button" id="addresgroup" class="btn btn-sm" style="background-color:#072c47;color:#fff;border-color:#072c47;border-radius:20px;padding:5px 22px;"><span class="icon-plus"></span> Add</button>
        </div>
      </div>

      <!-- Add form -->
      <div class="add-form add-resource-group-form" style="display:none;padding:10px 0;">
        <div class="row">
          <form id="addresgroupform">
            <div class="col-md-1"></div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Resource Group</label>
                <input id="resgroupname1" type="text" class="form-control" placeholder="Resource Group">
                <span class="error" style="display:none;color:red;"></span>
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
              <span class="error" style="display:none;color:red;"></span>
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

      <div class="preloader-group" style="display:none;text-align:center;padding:20px 0;">
        <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif">
      </div>
      <div id="resgrouplistsection" class="data-content-list"></div>
    </div>

    <!-- ── RESOURCES ── -->
    <div id="reslib-res-section" class="rl-section">
      <a href="#" id="listresource" style="display:none;"></a>
      <div class="search-and-actions-wrpr row" style="margin-bottom:10px;">
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

      <!-- Add form -->
      <div class="add-form add-resource-form" style="display:none;padding:10px 0;">
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
                <span class="error" style="display:none;color:red;"></span>
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
              <span class="error" style="display:none;color:red;"></span>
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

      <div class="preloader-resource" style="display:none;text-align:center;padding:20px 0;">
        <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif">
      </div>
      <div id="resourcelistsection" class="data-content-list"></div>
    </div>

  </div><!-- /#reslib-body -->

  <!-- footer close -->
  <div style="padding:8px 15px;border-top:1px solid #e5e5e5;text-align:right;flex-shrink:0;">
    <button id="reslib-close-footer" class="btn" style="background:#e67e22;color:#fff;border-color:#d35400;border-radius:4px;padding:6px 14px;"><span class="icon-close"></span> Close</button>
  </div>
</div>

<script>
(function(){
  var win  = document.getElementById('reslib-win');
  var hdr  = document.getElementById('reslib-win-hdr');
  var MIN_W=500, MIN_H=300, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0, _saved=null;

  function _anchor(){
    var r=win.getBoundingClientRect();
    win.style.left=r.left+'px'; win.style.top=r.top+'px';
    win.style.width=r.width+'px'; win.style.height=r.height+'px'; return r;
  }

  function openReslib(){
    win.classList.add('rl-open');
    document.dispatchEvent(new Event('reslib:open'));
  }

  function closeReslib(){
    win.classList.remove('rl-open');
    document.querySelectorAll('.icon-wrench.reslib-btn').forEach(function(el){ el.classList.remove('active'); });
  }

  /* ── Tab switching ── */
  document.querySelectorAll('.rl-tab-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      var target = btn.getAttribute('data-tab');

      // toggle: clicking active tab hides it
      var wasActive = btn.classList.contains('active');
      document.querySelectorAll('.rl-tab-btn').forEach(function(b){ b.classList.remove('active'); });
      document.querySelectorAll('.rl-section').forEach(function(s){ s.classList.remove('active'); });

      if(!wasActive){
        btn.classList.add('active');
        var section = document.getElementById(target);
        if(section){ section.classList.add('active'); }

        // trigger list load for the section
        if(target === 'reslib-type-section')  setTimeout(function(){ $('#listrestype').trigger('click'); }, 100);
        if(target === 'reslib-group-section') setTimeout(function(){ $('#listresgroup').trigger('click'); }, 100);
        if(target === 'reslib-res-section')   setTimeout(function(){ $('#listresource').trigger('click'); }, 100);
      }
    });
  });

  /* ── Open via .reslib-btn icons in navbar ── */
  $(document).on('click', '.navbar-nav .reslib-btn', function(e){
    e.preventDefault();
    if(win.classList.contains('rl-open')){
      closeReslib();
    } else {
      // close other overlays
      var others = {
        '.overNow4': '.menu4-popup-cntnr',
        '.overNow2': '.finmenu-popup-cntnr',
        '.overNow8': '.menu8-popup-cntnr',
        '.overNow': '.menu-popup-cntnr'
      };
      $.each(others, function(cls, cntnr){
        if($(cls).hasClass('active')){
          $(cls).removeClass('active');
          $(cntnr).removeClass('active');
          $('body').css('overflow-y','auto');
        }
      });
      openReslib();
      $(this).addClass('active');
    }
  });

  /* ── Close buttons ── */
  document.getElementById('reslib-close').addEventListener('click', closeReslib);
  document.getElementById('reslib-close-footer').addEventListener('click', closeReslib);

  /* ── Expand / restore ── */
  document.getElementById('reslib-expand').addEventListener('click', function(){
    if(_saved){
      win.style.left=_saved.left; win.style.top=_saved.top;
      win.style.width=_saved.width; win.style.height=_saved.height;
      _saved=null; this.innerHTML='&#x26F6;'; this.title='Fullscreen';
    } else {
      _anchor();
      _saved={left:win.style.left,top:win.style.top,width:win.style.width,height:win.style.height};
      win.style.left='0'; win.style.top='0';
      win.style.width='100vw'; win.style.height='100vh';
      this.innerHTML='&#x2716;'; this.title='Restore';
    }
  });

  /* ── Drag ── */
  hdr.addEventListener('mousedown', function(e){
    if(e.target.closest('#reslib-win-hdr-btns')) return;
    var r=_anchor(); _action='drag';
    _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault();
  });

  /* ── Resize ── */
  document.querySelectorAll('.rl-rs').forEach(function(el){
    el.addEventListener('mousedown', function(e){
      var r=_anchor(); _action=el.getAttribute('data-dir');
      _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height;
      e.preventDefault(); e.stopPropagation();
    });
  });

  document.addEventListener('mousemove', function(e){
    if(!_action) return;
    var dx=e.clientX-_sx, dy=e.clientY-_sy;
    if(_action==='drag'){
      win.style.left=Math.max(0,_ox+dx)+'px'; win.style.top=Math.max(0,_oy+dy)+'px';
    } else {
      var l=_ox,t=_oy,w=_ow,h=_oh;
      if(_action.indexOf('e')>-1){w=Math.max(MIN_W,_ow+dx);}
      if(_action.indexOf('s')>-1){h=Math.max(MIN_H,_oh+dy);}
      if(_action.indexOf('w')>-1){var nw=Math.max(MIN_W,_ow-dx);l=_ox+(_ow-nw);w=nw;}
      if(_action.indexOf('n')>-1){var nh=Math.max(MIN_H,_oh-dy);t=_oy+(_oh-nh);h=nh;}
      win.style.left=l+'px'; win.style.top=t+'px';
      win.style.width=w+'px'; win.style.height=h+'px';
    }
  });
  document.addEventListener('mouseup', function(){ _action=null; });

  /* ── Move to <body> to avoid stacking context clipping ── */
  $(function(){
    if(win && win.parentNode !== document.body) document.body.appendChild(win);
  });
})();
</script>
