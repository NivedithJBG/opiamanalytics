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
  top:80px; left:calc(50% - 400px);
  width:800px; height:calc(100vh - 110px);
  min-width:500px; min-height:300px;
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

/* scrollable body */
#reslib-body {
  flex:1; min-height:0; overflow-y:auto; padding:14px 18px;
}

.edit-form { display:none; padding:10px 0; }

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

.rl-modal-save {
  background:#00796b !important; color:#fff !important;
  border-color:#00695c !important; border-radius:20px !important;
}
.rl-modal-save:hover { background:#00695c !important; }
</style>

<!-- ══════════════════════════════════════════════════════════════
     RESOURCE LIBRARY FLOATING POPUP
═══════════════════════════════════════════════════════════════ -->
<div id="reslib-win">
  <div class="rl-rs rl-rs-n"  data-dir="n"></div>
  <div class="rl-rs rl-rs-s"  data-dir="s"></div>
  <div class="rl-rs rl-rs-e"  data-dir="e"></div>
  <div class="rl-rs rl-rs-w"  data-dir="w"></div>
  <div class="rl-rs rl-rs-ne" data-dir="ne"></div>
  <div class="rl-rs rl-rs-nw" data-dir="nw"></div>
  <div class="rl-rs rl-rs-se" data-dir="se"></div>
  <div class="rl-rs rl-rs-sw" data-dir="sw"></div>

  <div id="reslib-win-hdr">
    <span style="font-size:18px;font-weight:700;color:#004d40;">Resource Library</span>
    <div id="reslib-win-hdr-btns">
      <button id="reslib-expand" title="Fullscreen">&#x26F6;</button>
      <button id="reslib-close" title="Close">&times;</button>
    </div>
  </div>

  <!-- single action bar — mirrors Activity Library layout -->
  <div id="reslib-action-bar">
    <a href="#rlResTypePopup"  class="rl-type-btn"   data-toggle="modal" data-target="#rlResTypePopup">+ Resource Type</a>
    <a href="#rlResGroupPopup" class="rl-type-btn"   data-toggle="modal" data-target="#rlResGroupPopup">+ Resource Group</a>
    <a href="#rlResourcePopup" class="rl-action-btn" data-toggle="modal" data-target="#rlResourcePopup"><span class="icon-add"></span> Resource</a>
    <a href="#"                class="rl-action-btn" id="rl-list-btn"><span class="icon-th-list"></span> List</a>
    <select id="searchresourcetype" style="display:none;margin-left:8px;width:160px;border-radius:20px;height:34px;border:1px solid #b2dfdb;padding:0 10px;font-size:13px;">
      <option value="0">All Types</option>
      <?php foreach ($resourceTypes as $rt): ?>
      <option value="<?php echo $rt->ResourceType_Id; ?>"><?php echo htmlspecialchars($rt->Name, ENT_QUOTES); ?></option>
      <?php endforeach; ?>
    </select>
    <button id="rl-resource-search-btn" style="display:none;height:34px;padding:0 14px;border-radius:20px;background:#00796b;color:#fff;border:none;cursor:pointer;font-size:13px;"><span class="icon-search5"></span></button>
  </div>

  <!-- scrollable body -->
  <div id="reslib-body">
    <!-- hidden triggers for existing JS files -->
    <a href="#" id="listrestype"  style="display:none;"></a>
    <a href="#" id="listresgroup" style="display:none;"></a>
    <a href="#" id="listresource" style="display:none;"></a>

    <!-- edit forms (populated by resourcetype.js / resourcegroup.js / resource.js) -->
    <div class="edit-form edit-resource-type-form">
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

    <div class="edit-form edit-resource-group-form">
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

    <div class="edit-form edit-resource-form">
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

    <!-- preloaders -->
    <div class="preloader"          style="display:none;text-align:center;padding:20px 0;"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif"></div>
    <div class="preloader-group"    style="display:none;text-align:center;padding:20px 0;"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif"></div>
    <div class="preloader-resource" style="display:none;text-align:center;padding:20px 0;"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif"></div>

    <!-- list containers (filled by existing JS) -->
    <div id="restypelistsection"  class="data-content-list"></div>
    <div id="resgrouplistsection" class="data-content-list"></div>
    <div id="resourcelistsection" class="data-content-list"></div>
  </div>

  <div style="padding:8px 15px;border-top:1px solid #e5e5e5;text-align:right;flex-shrink:0;background:#f5fffe;">
    <button id="reslib-close-footer" class="btn" style="background:#e67e22;color:#fff;border-color:#d35400;border-radius:4px;padding:6px 14px;"><span class="icon-close"></span> Close</button>
  </div>
</div>


<!-- ══ RESOURCE TYPE MODAL ══════════════════════════════════════ -->
<div class="modal fade" id="rlResTypePopup" data-backdrop="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="float:left;">Resource Type</h4>
        <button type="button" class="close" data-dismiss="modal" style="float:right;font-size:28px;">&times;</button>
      </div>
      <div class="modal-body">
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
      <div class="modal-footer">
        <button type="button" class="btn" data-dismiss="modal" style="background:#e67e22;color:#fff;border-color:#d35400;border-radius:4px;"><span class="icon-close"></span> Close</button>
      </div>
    </div>
  </div>
</div>

<!-- ══ RESOURCE GROUP MODAL ════════════════════════════════════ -->
<div class="modal fade" id="rlResGroupPopup" data-backdrop="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="float:left;">Resource Group</h4>
        <button type="button" class="close" data-dismiss="modal" style="float:right;font-size:28px;">&times;</button>
      </div>
      <div class="modal-body">
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
      <div class="modal-footer">
        <button type="button" class="btn" data-dismiss="modal" style="background:#e67e22;color:#fff;border-color:#d35400;border-radius:4px;"><span class="icon-close"></span> Close</button>
      </div>
    </div>
  </div>
</div>

<!-- ══ RESOURCE MODAL — add form only ══════════════════════════ -->
<div class="modal fade" id="rlResourcePopup" data-backdrop="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="float:left;">Add Resource</h4>
        <button type="button" class="close" data-dismiss="modal" style="float:right;font-size:28px;">&times;</button>
      </div>
      <div class="modal-body">
        <form id="addresourceform">
          <div class="row">
            <div class="col-md-3">
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
            <div class="col-md-3">
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
            <div class="col-md-3">
              <div class="form-group">
                <label>Resource Name</label>
                <input id="resourcename1" type="text" class="form-control" placeholder="Resource Name">
                <span class="error" style="display:none;color:red;"></span>
              </div>
            </div>
            <div class="col-md-3" id="resourceunit1-wrap">
              <div class="form-group">
                <label>Unit</label>
                <input id="resourceunit1" type="text" class="form-control" placeholder="e.g. kg, m, nos">
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" data-dismiss="modal" id="cancelresource" style="background:#d9534f;color:#fff;border-color:#d43f3a;border-radius:4px;"><span class="icon-close"></span> Cancel</button>
        <button type="button" id="saveresource" class="btn rl-modal-save"><span class="icon-check"></span> Add Resource</button>
      </div>
    </div>
  </div>
</div>


<script>
(function(){
  var win = document.getElementById('reslib-win');
  var hdr = document.getElementById('reslib-win-hdr');
  var MIN_W=400, MIN_H=300, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0, _saved=null;

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
    document.querySelectorAll('.reslib-btn').forEach(function(el){ el.classList.remove('active'); });
  }

  /* ── List button ── */
  document.getElementById('rl-list-btn').addEventListener('click', function(e){
    e.preventDefault();
    $('#searchresourcetype').show();
    $('#rl-resource-search-btn').show();
    setTimeout(function(){ $('#listresource').trigger('click'); }, 100);
  });
  $(document).on('click','#rl-resource-search-btn', function(){
    setTimeout(function(){ $('#listresource').trigger('click'); }, 100);
  });

  /* ── Open / close via navbar icon ── */
  $(document).on('click', '.navbar-nav .reslib-btn', function(e){
    e.preventDefault();
    if(win.classList.contains('rl-open')){
      closeReslib();
    } else {
      var others = {
        '.overNow4': '.menu4-popup-cntnr',
        '.overNow2': '.finmenu-popup-cntnr',
        '.overNow8': '.menu8-popup-cntnr',
        '.overNow':  '.menu-popup-cntnr'
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

  /* ── Move modals to <body> — same pattern as Activity Library ── */
  $(function(){
    $('#rlResTypePopup, #rlResGroupPopup, #rlResourcePopup').appendTo('body');

    /* Load list when Resource Type modal opens */
    $('#rlResTypePopup').on('shown.bs.modal', function(){
      $('#restypename1').val('').focus();
      rlLoadResTypeList();
    });
    $(document).on('click','#saverestype', function(){
      setTimeout(function(){ rlLoadResTypeList(); }, 500);
    });

    /* Load list when Resource Group modal opens */
    $('#rlResGroupPopup').on('shown.bs.modal', function(){
      $('#resgroupname1').val('').focus();
      rlLoadResGroupList();
    });
    $(document).on('click','#saveresgroup', function(){
      setTimeout(function(){ rlLoadResGroupList(); }, 500);
    });

    /* Refresh resource list in body after Resource modal closes */
    $('#rlResourcePopup').on('hidden.bs.modal', function(){
      if($('#resourcelistsection').html().trim()){
        setTimeout(function(){ $('#listresource').trigger('click'); }, 200);
      }
    });
  });

  /* ── Resource Type list (inside modal) ── */
  function rlLoadResTypeList(){
    $('#rl-restype-modal-list').html('<div style="padding:8px;color:#999;">Loading…</div>');
    $.ajax({
      type:'POST', url:'../resourcetype/search', dataType:'json',
      data:{ restypename: '' },
      success:function(d){
        $('#rl-restype-modal-list').html(
          (d && d.error==='No' && d.restype) ? d.restype : '<div style="padding:8px;color:#999;">No resource types yet.</div>'
        );
      },
      error:function(){ $('#rl-restype-modal-list').html('<div style="padding:8px;color:red;">Could not load.</div>'); }
    });
  }

  /* ── Resource Group list (inside modal) ── */
  function rlLoadResGroupList(){
    $('#rl-resgroup-modal-list').html('<div style="padding:8px;color:#999;">Loading…</div>');
    $.ajax({
      type:'POST', url:'../resourcegroup/search', dataType:'json',
      data:{ resgroupname: '' },
      success:function(d){
        $('#rl-resgroup-modal-list').html(
          (d && d.error==='No' && d.resgroup) ? d.resgroup : '<div style="padding:8px;color:#999;">No resource groups yet.</div>'
        );
      },
      error:function(){ $('#rl-resgroup-modal-list').html('<div style="padding:8px;color:red;">Could not load.</div>'); }
    });
  }

})();
</script>
