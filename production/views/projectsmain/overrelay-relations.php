<style>
/* ── Relations overlay — resize handles (shared pattern) ─────── */
.rl2-rs { position:absolute; z-index:10; background:transparent; }
.rl2-rs-e  { right:0;  top:6px;    bottom:6px; width:6px;  cursor:e-resize; }
.rl2-rs-w  { left:0;   top:6px;    bottom:6px; width:6px;  cursor:w-resize; }
.rl2-rs-s  { bottom:0; left:6px;   right:6px;  height:6px; cursor:s-resize; }
.rl2-rs-n  { top:0;    left:6px;   right:6px;  height:6px; cursor:n-resize; }
.rl2-rs-se { right:0;  bottom:0; width:14px; height:14px; cursor:se-resize; }
.rl2-rs-sw { left:0;   bottom:0; width:14px; height:14px; cursor:sw-resize; }
.rl2-rs-ne { right:0;  top:0;    width:14px; height:14px; cursor:ne-resize; }
.rl2-rs-nw { left:0;   top:0;    width:14px; height:14px; cursor:nw-resize; }

/* ── Main relations window ────────────────────────────────────── */
#rel-win {
  display:none; position:fixed;
  top:90px; left:calc(50% - 320px);
  width:640px; height:320px;
  min-width:400px; min-height:200px;
  background:#fff; border-radius:8px;
  box-shadow:0 8px 32px rgba(0,0,0,.35);
  z-index:10004; flex-direction:column; overflow:hidden;
}
#rel-win.rl2-open { display:flex; }

#rel-win-hdr {
  background:#3d2060; color:#fff; padding:10px 15px;
  display:flex; align-items:center; justify-content:space-between;
  cursor:move; user-select:none; flex-shrink:0;
}
#rel-win-hdr span { font-size:16px; font-weight:700; }
#rel-win-hdr-btns { display:flex; align-items:center; gap:6px; }
#rel-win-hdr-btns button {
  background:none; border:none; color:#fff;
  font-size:20px; line-height:1; cursor:pointer; padding:0 4px; opacity:.75;
}
#rel-win-hdr-btns button:hover { opacity:1; }

#rel-action-bar {
  display:flex; align-items:center; gap:8px;
  padding:10px 15px; border-bottom:1px solid #e8e0f5; flex-shrink:0;
  background:#f9f5ff;
}
#rel-action-bar button {
  border-radius:20px; padding:6px 18px; font-size:13px; font-weight:600;
  cursor:pointer; border:none;
}
#rel-btn-add  { background:#5c3d8f; color:#fff; }
#rel-btn-add:hover  { background:#4a2f75; }
#rel-btn-list { background:#00796b; color:#fff; }
#rel-btn-list:hover { background:#00695c; }

#rel-body { flex:1; min-height:0; overflow-y:auto; padding:12px 16px; font-size:13px; color:#555; }

/* ── Sub-popup shared ─────────────────────────────────────────── */
.rl2-subwin {
  display:none; position:fixed;
  min-width:360px; min-height:200px;
  background:#fff; border-radius:8px;
  box-shadow:0 10px 40px rgba(0,0,0,.45);
  z-index:200100; flex-direction:column; overflow:hidden;
}
.rl2-subwin.rl2-sw-open { display:flex; }

.rl2-sw-hdr {
  background:#ede7f6; padding:10px 14px;
  display:flex; align-items:center; justify-content:space-between;
  cursor:move; user-select:none; flex-shrink:0;
  border-bottom:3px solid #5c3d8f;
}
.rl2-sw-hdr h4 { margin:0; color:#3d2060; font-weight:700; font-size:15px; }
.rl2-sw-hdr-btns button {
  background:none; border:none; color:#3d2060;
  font-size:22px; line-height:1; cursor:pointer; padding:0 4px; opacity:.7;
}
.rl2-sw-hdr-btns button:hover { opacity:1; }
.rl2-sw-body  { padding:16px; overflow-y:auto; flex:1; min-height:0; }
.rl2-sw-footer {
  padding:10px 16px; border-top:1px solid #e5e5e5;
  text-align:right; flex-shrink:0; background:#f9f5ff;
  display:flex; align-items:center; justify-content:flex-end; gap:8px;
}

/* ── Add form internals ───────────────────────────────────────── */
.rl2-col-hdr {
  text-align:center; font-weight:700; font-size:12px; letter-spacing:.5px;
  text-transform:uppercase; color:#5c3d8f; padding:4px 0 8px;
  border-bottom:2px solid #ede7f6; margin-bottom:10px;
}
.rl2-add-grid {
  display:grid;
  grid-template-columns: 1fr 60px 1fr;
  gap:0 16px;
  align-items:start;
}
.rl2-mid-col {
  display:flex; flex-direction:column; align-items:center;
  justify-content:flex-start; padding-top:28px; gap:10px;
}
.rl2-type-btn {
  width:52px; height:28px; border-radius:6px; border:1px solid #bbb;
  background:#f5f5f5; font-size:12px; font-weight:700; cursor:pointer;
  color:#333;
}
.rl2-type-btn.active { background:#5c3d8f; color:#fff; border-color:#5c3d8f; }

.rl2-lag-wrap {
  display:flex; align-items:center; gap:4px; margin-top:4px;
}
.rl2-lag-toggle {
  width:32px; height:28px; border-radius:6px; border:1px solid #bbb;
  background:#f5f5f5; font-size:14px; font-weight:700; cursor:pointer; color:#333;
}
.rl2-lag-toggle.lead { background:#e57373; color:#fff; border-color:#e57373; }
.rl2-lag-toggle.lag  { background:#4caf50; color:#fff; border-color:#4caf50; }
#rel-lag-days { width:52px; height:28px; border-radius:6px; border:1px solid #bbb; text-align:center; font-size:13px; }

/* ── List table ───────────────────────────────────────────────── */
.rl2-list-table { width:100%; border-collapse:collapse; font-size:12px; }
.rl2-list-table th {
  background:#5c3d8f; color:#fff; padding:7px 8px;
  border:1px solid #4a2f75; text-align:left; white-space:nowrap;
}
.rl2-list-table td {
  padding:6px 8px; border:1px solid #e0d7f5; vertical-align:middle;
}
.rl2-list-table tr:hover td { background:#f5f0ff; }
.rl2-chk { width:18px; height:18px; cursor:pointer; accent-color:#5c3d8f; }
.rl2-badge {
  display:inline-block; padding:2px 7px; border-radius:10px;
  font-size:11px; font-weight:700; color:#fff; background:#5c3d8f;
}
.rl2-staged td { background:#f0fff4; }
.rl2-staged td:first-child { border-left:3px solid #4caf50; }
</style>

<!-- ══ MAIN RELATIONS WINDOW ════════════════════════════════════ -->
<div id="rel-win">
  <div class="rl2-rs rl2-rs-n" data-dir="n"></div><div class="rl2-rs rl2-rs-s" data-dir="s"></div>
  <div class="rl2-rs rl2-rs-e" data-dir="e"></div><div class="rl2-rs rl2-rs-w" data-dir="w"></div>
  <div class="rl2-rs rl2-rs-ne" data-dir="ne"></div><div class="rl2-rs rl2-rs-nw" data-dir="nw"></div>
  <div class="rl2-rs rl2-rs-se" data-dir="se"></div><div class="rl2-rs rl2-rs-sw" data-dir="sw"></div>

  <div id="rel-win-hdr">
    <span>&#x1F517; Activity Relationships</span>
    <div id="rel-win-hdr-btns">
      <button id="rel-win-close" title="Close">&times;</button>
    </div>
  </div>

  <div id="rel-action-bar">
    <button id="rel-btn-add">+ Add Relationship</button>
    <button id="rel-btn-list">&#x2630; List</button>
  </div>

  <div id="rel-body">
    <p style="color:#aaa;text-align:center;padding:20px 0;">Click <strong>+ Add Relationship</strong> to define a new dependency,<br>or <strong>List</strong> to view existing relationships.</p>
  </div>
</div>


<!-- ══ ADD RELATIONSHIP SUB-POPUP ═══════════════════════════════ -->
<div id="rel-add-popup" class="rl2-subwin" style="top:100px;left:calc(50% - 440px);width:880px;height:360px;">
  <div class="rl2-rs rl2-rs-n" data-dir="n"></div><div class="rl2-rs rl2-rs-s" data-dir="s"></div>
  <div class="rl2-rs rl2-rs-e" data-dir="e"></div><div class="rl2-rs rl2-rs-w" data-dir="w"></div>
  <div class="rl2-rs rl2-rs-ne" data-dir="ne"></div><div class="rl2-rs rl2-rs-nw" data-dir="nw"></div>
  <div class="rl2-rs rl2-rs-se" data-dir="se"></div><div class="rl2-rs rl2-rs-sw" data-dir="sw"></div>
  <div class="rl2-sw-hdr">
    <h4>Add Relationship</h4>
    <div class="rl2-sw-hdr-btns"><button id="rel-add-close">&times;</button></div>
  </div>
  <div class="rl2-sw-body">
    <div class="rl2-add-grid">

      <!-- PRECEDENT -->
      <div>
        <div class="rl2-col-hdr">&#9664; Precedent (Predecessor)</div>
        <div class="form-group">
          <label style="font-size:12px;color:#666;">Select IOW</label>
          <select id="rel-prec-iow" class="form-control input-sm">
            <option value="">-- Select IOW --</option>
          </select>
        </div>
        <div class="form-group">
          <label style="font-size:12px;color:#666;">Select Activity</label>
          <select id="rel-prec-act" class="form-control input-sm">
            <option value="">-- Select Activity --</option>
          </select>
        </div>
      </div>

      <!-- MIDDLE: type + lag -->
      <div class="rl2-mid-col">
        <div style="text-align:center;font-size:11px;color:#888;font-weight:600;margin-bottom:4px;">TYPE</div>
        <button class="rl2-type-btn active" data-type="2">FS</button>
        <button class="rl2-type-btn" data-type="1">SS</button>
        <button class="rl2-type-btn" data-type="3">FF</button>
        <div style="margin-top:8px;text-align:center;font-size:11px;color:#888;font-weight:600;">LAG / LEAD</div>
        <div class="rl2-lag-wrap">
          <button class="rl2-lag-toggle lag" id="rel-lag-toggle" title="Toggle Lag/Lead">+</button>
          <input type="number" id="rel-lag-days" value="0" min="0">
        </div>
        <div id="rel-lag-label" style="font-size:10px;color:#888;text-align:center;">Lag days</div>
      </div>

      <!-- DEPENDENT -->
      <div>
        <div class="rl2-col-hdr">Dependent (Successor) &#9654;</div>
        <div class="form-group">
          <label style="font-size:12px;color:#666;">Select IOW</label>
          <select id="rel-dep-iow" class="form-control input-sm">
            <option value="">-- Select IOW --</option>
          </select>
        </div>
        <div class="form-group">
          <label style="font-size:12px;color:#666;">Select Activity</label>
          <select id="rel-dep-act" class="form-control input-sm">
            <option value="">-- Select Activity --</option>
          </select>
        </div>
      </div>

    </div><!-- /.rl2-add-grid -->
  </div>
  <div class="rl2-sw-footer">
    <button id="rel-add-cancel" class="btn" style="background:#aaa;color:#fff;border-radius:20px;">Cancel</button>
    <button id="rel-add-to-list" class="btn" style="background:#5c3d8f;color:#fff;border-radius:20px;font-weight:600;">Add to List &#9654;</button>
  </div>
</div>


<!-- ══ LIST SUB-POPUP ═══════════════════════════════════════════ -->
<div id="rel-list-popup" class="rl2-subwin" style="top:110px;left:calc(50% - 500px);width:1000px;height:480px;">
  <div class="rl2-rs rl2-rs-n" data-dir="n"></div><div class="rl2-rs rl2-rs-s" data-dir="s"></div>
  <div class="rl2-rs rl2-rs-e" data-dir="e"></div><div class="rl2-rs rl2-rs-w" data-dir="w"></div>
  <div class="rl2-rs rl2-rs-ne" data-dir="ne"></div><div class="rl2-rs rl2-rs-nw" data-dir="nw"></div>
  <div class="rl2-rs rl2-rs-se" data-dir="se"></div><div class="rl2-rs rl2-rs-sw" data-dir="sw"></div>
  <div class="rl2-sw-hdr">
    <h4>&#x2630; Relationships List</h4>
    <div class="rl2-sw-hdr-btns"><button id="rel-list-close">&times;</button></div>
  </div>
  <div class="rl2-sw-body" style="padding:10px 14px;">
    <div id="rel-list-content">
      <p style="color:#aaa;text-align:center;padding:20px;">Loading…</p>
    </div>
  </div>
  <div class="rl2-sw-footer">
    <label style="font-size:12px;color:#666;margin-right:auto;margin-bottom:0;">
      <input type="checkbox" id="rel-chk-all" style="margin-right:4px;"> Select all
    </label>
    <button id="rel-list-refresh" class="btn btn-default btn-sm" style="border-radius:20px;">&#x21BB; Refresh</button>
    <button id="rel-create-btn" class="btn" style="background:#5c3d8f;color:#fff;border-radius:20px;font-weight:600;padding:7px 22px;">
      &#x2714; Create Selected
    </button>
  </div>
</div>


<script>
(function(){
  /* ═══════════════════════════════════════════════════════════════
     Shared drag+resize engine (same pattern as resource library)
  ═══════════════════════════════════════════════════════════════ */
  var _drag2 = null;

  function rl2Anchor(win){
    var r = win.getBoundingClientRect();
    win.style.left=r.left+'px'; win.style.top=r.top+'px';
    win.style.width=r.width+'px'; win.style.height=r.height+'px';
    return r;
  }

  function rl2BindDragResize(win, hdrSel, minW, minH){
    var hdr = win.querySelector(hdrSel);
    hdr.addEventListener('mousedown', function(e){
      if(e.target.closest('.rl2-sw-hdr-btns, #rel-win-hdr-btns')) return;
      var r = rl2Anchor(win);
      _drag2 = {win:win,action:'drag',sx:e.clientX,sy:e.clientY,ox:r.left,oy:r.top,ow:r.width,oh:r.height,mw:minW,mh:minH};
      e.preventDefault();
    });
    win.querySelectorAll('.rl2-rs').forEach(function(el){
      el.addEventListener('mousedown', function(e){
        var r = rl2Anchor(win);
        _drag2 = {win:win,action:el.dataset.dir,sx:e.clientX,sy:e.clientY,ox:r.left,oy:r.top,ow:r.width,oh:r.height,mw:minW,mh:minH};
        e.preventDefault(); e.stopPropagation();
      });
    });
  }

  document.addEventListener('mousemove', function(e){
    if(!_drag2) return;
    var d=_drag2, dx=e.clientX-d.sx, dy=e.clientY-d.sy;
    var l=d.ox, t=d.oy, w=d.ow, h=d.oh;
    if(d.action==='drag'){ l=Math.max(0,d.ox+dx); t=Math.max(0,d.oy+dy); }
    else {
      if(d.action.indexOf('e')>-1){ w=Math.max(d.mw,d.ow+dx); }
      if(d.action.indexOf('s')>-1){ h=Math.max(d.mh,d.oh+dy); }
      if(d.action.indexOf('w')>-1){ var nw=Math.max(d.mw,d.ow-dx); l=d.ox+(d.ow-nw); w=nw; }
      if(d.action.indexOf('n')>-1){ var nh=Math.max(d.mh,d.oh-dy); t=d.oy+(d.oh-nh); h=nh; }
    }
    d.win.style.left=l+'px'; d.win.style.top=t+'px';
    d.win.style.width=w+'px'; d.win.style.height=h+'px';
  });
  document.addEventListener('mouseup', function(){ _drag2=null; });

  /* ═══════════════════════════════════════════════════════════════
     State
  ═══════════════════════════════════════════════════════════════ */
  var _pid   = '';   /* active project id */
  var _groups= [];   /* [{Workgroup_Id, iow_name}, ...] */
  var _acts  = [];   /* [{id, name, scheduleitem_id}, ...] */
  var _staged= [];   /* pending rows not yet saved to DB */
  var _swZ   = 200100;

  function _bringToFront(el){ el.style.zIndex = ++_swZ; }

  /* ═══════════════════════════════════════════════════════════════
     Get project id from the gantt open button (set at runtime)
  ═══════════════════════════════════════════════════════════════ */
  function _getPid(){
    var btn = document.getElementById('gantt-win-open');
    return btn ? (btn.getAttribute('data-projectid') || '') : '';
  }

  /* ═══════════════════════════════════════════════════════════════
     Load IOW groups + activities for this project's Gantt
  ═══════════════════════════════════════════════════════════════ */
  function loadGanttActivities(cb){
    _pid = _getPid();
    if(!_pid){ cb && cb(); return; }
    $.ajax({ type:'POST', url:'../relation/getganttactivities', dataType:'json',
      data:{ projectid: _pid },
      success:function(d){
        if(d && d.error==='No'){
          _groups = d.groups || [];
          _acts   = d.activities || [];
        }
        cb && cb();
      }
    });
  }

  function _buildIowOptions(){
    var html = '<option value="">-- Select IOW --</option>';
    _groups.forEach(function(g){
      var label = g.iow_name || g.iow_group_name || ('IOW #'+g.Workgroup_Id);
      html += '<option value="'+g.Workgroup_Id+'">'+$('<span>').text(label).html()+'</option>';
    });
    return html;
  }

  function _buildActOptions(iowId){
    var html = '<option value="">-- Select Activity --</option>';
    _acts.filter(function(a){ return String(a.scheduleitem_id)===String(iowId); })
         .forEach(function(a){
           html += '<option value="'+a.id+'" data-name="'+$('<span>').text(a.name).html()+'">'+$('<span>').text(a.name).html()+'</option>';
         });
    return html;
  }

  /* ═══════════════════════════════════════════════════════════════
     Main window open/close
  ═══════════════════════════════════════════════════════════════ */
  var relWin = document.getElementById('rel-win');
  rl2BindDragResize(relWin, '#rel-win-hdr', 400, 200);

  document.getElementById('btn-gantt-relations').addEventListener('click', function(){
    if(relWin.classList.contains('rl2-open')){ relWin.classList.remove('rl2-open'); return; }
    relWin.classList.add('rl2-open');
    _bringToFront(relWin);
    loadGanttActivities();
  });
  document.getElementById('rel-win-close').addEventListener('click', function(){
    relWin.classList.remove('rl2-open');
  });

  /* ═══════════════════════════════════════════════════════════════
     Sub-popup open helpers
  ═══════════════════════════════════════════════════════════════ */
  function openSub(id){ var el=document.getElementById(id); el.classList.add('rl2-sw-open'); _bringToFront(el); }
  function closeSub(id){ document.getElementById(id).classList.remove('rl2-sw-open'); }

  ['rel-add-popup','rel-list-popup'].forEach(function(id){
    var el=document.getElementById(id);
    el.addEventListener('mousedown', function(){ _bringToFront(el); }, true);
  });

  /* ═══════════════════════════════════════════════════════════════
     ADD RELATIONSHIP popup
  ═══════════════════════════════════════════════════════════════ */
  rl2BindDragResize(document.getElementById('rel-add-popup'), '.rl2-sw-hdr', 600, 280);

  document.getElementById('rel-btn-add').addEventListener('click', function(){
    loadGanttActivities(function(){
      var iowOpts = _buildIowOptions();
      document.getElementById('rel-prec-iow').innerHTML = iowOpts;
      document.getElementById('rel-dep-iow').innerHTML  = iowOpts;
      document.getElementById('rel-prec-act').innerHTML = '<option value="">-- Select Activity --</option>';
      document.getElementById('rel-dep-act').innerHTML  = '<option value="">-- Select Activity --</option>';
      document.getElementById('rel-lag-days').value = '0';
      /* reset type to FS */
      document.querySelectorAll('.rl2-type-btn').forEach(function(b){ b.classList.toggle('active', b.dataset.type==='2'); });
      openSub('rel-add-popup');
    });
  });

  document.getElementById('rel-add-close').addEventListener('click', function(){ closeSub('rel-add-popup'); });
  document.getElementById('rel-add-cancel').addEventListener('click', function(){ closeSub('rel-add-popup'); });

  /* IOW → Activity cascade */
  $(document).on('change','#rel-prec-iow', function(){
    $('#rel-prec-act').html(_buildActOptions($(this).val()));
  });
  $(document).on('change','#rel-dep-iow', function(){
    $('#rel-dep-act').html(_buildActOptions($(this).val()));
  });

  /* Relation type toggle */
  $(document).on('click','.rl2-type-btn', function(){
    document.querySelectorAll('.rl2-type-btn').forEach(function(b){ b.classList.remove('active'); });
    this.classList.add('active');
  });

  /* Lag/Lead toggle */
  var _isLead = false;
  document.getElementById('rel-lag-toggle').addEventListener('click', function(){
    _isLead = !_isLead;
    this.textContent = _isLead ? '−' : '+';
    this.classList.toggle('lead', _isLead);
    this.classList.toggle('lag',  !_isLead);
    document.getElementById('rel-lag-label').textContent = _isLead ? 'Lead days' : 'Lag days';
  });

  /* Add to List */
  document.getElementById('rel-add-to-list').addEventListener('click', function(){
    var precIowEl  = document.getElementById('rel-prec-iow');
    var precActEl  = document.getElementById('rel-prec-act');
    var depIowEl   = document.getElementById('rel-dep-iow');
    var depActEl   = document.getElementById('rel-dep-act');
    var typeBtn    = document.querySelector('.rl2-type-btn.active');
    var lagDays    = parseInt(document.getElementById('rel-lag-days').value) || 0;
    if(_isLead) lagDays = -lagDays;

    if(!precActEl.value){ alert('Please select the Precedent Activity.'); return; }
    if(!depActEl.value) { alert('Please select the Dependent Activity.'); return; }
    if(precActEl.value === depActEl.value){ alert('Precedent and Dependent cannot be the same activity.'); return; }

    var relType = typeBtn ? parseInt(typeBtn.dataset.type) : 2;
    var typeName = {1:'SS',2:'FS',3:'FF'}[relType] || 'FS';

    _staged.push({
      precedent_activity:      precActEl.value,
      precedent_name:          precActEl.options[precActEl.selectedIndex].getAttribute('data-name') || precActEl.options[precActEl.selectedIndex].text,
      precedent_iow:           precIowEl.options[precIowEl.selectedIndex].text,
      precedent_schedule_item: precIowEl.value,
      dependent_activity:      depActEl.value,
      dependent_name:          depActEl.options[depActEl.selectedIndex].getAttribute('data-name') || depActEl.options[depActEl.selectedIndex].text,
      dependent_iow:           depIowEl.options[depIowEl.selectedIndex].text,
      dependent_schedule_item: depIowEl.value,
      relation_type:           relType,
      type_name:               typeName,
      lag_days:                lagDays,
      staged:                  true
    });

    /* Reset fields for next entry */
    document.getElementById('rel-prec-act').innerHTML = '<option value="">-- Select Activity --</option>';
    document.getElementById('rel-dep-iow').value = '';
    document.getElementById('rel-dep-act').innerHTML  = '<option value="">-- Select Activity --</option>';
    document.getElementById('rel-lag-days').value = '0';

    /* Open list to show what was added */
    closeSub('rel-add-popup');
    openSub('rel-list-popup');
    renderList();
  });


  /* ═══════════════════════════════════════════════════════════════
     LIST popup
  ═══════════════════════════════════════════════════════════════ */
  rl2BindDragResize(document.getElementById('rel-list-popup'), '.rl2-sw-hdr', 600, 300);

  document.getElementById('rel-btn-list').addEventListener('click', function(){
    openSub('rel-list-popup');
    loadSavedList();
  });
  document.getElementById('rel-list-close').addEventListener('click', function(){ closeSub('rel-list-popup'); });
  document.getElementById('rel-list-refresh').addEventListener('click', function(){ loadSavedList(); });

  /* Select all checkbox */
  document.getElementById('rel-chk-all').addEventListener('change', function(){
    var checked = this.checked;
    document.querySelectorAll('.rl2-row-chk').forEach(function(chk){ chk.checked = checked; });
  });

  /* Load saved relationships from DB */
  function loadSavedList(){
    _pid = _getPid();
    $('#rel-list-content').html('<p style="color:#aaa;text-align:center;padding:20px;">Loading…</p>');
    if(!_pid){ $('#rel-list-content').html('<p style="color:#e57373;text-align:center;padding:20px;">No project selected.</p>'); return; }
    $.ajax({ type:'POST', url:'../relation/list', dataType:'json',
      data:{ projectid: _pid },
      success:function(d){
        renderList(d && d.error==='No' ? d.rows : []);
      },
      error:function(){ $('#rel-list-content').html('<p style="color:#e57373;text-align:center;padding:20px;">Could not load.</p>'); }
    });
  }

  /* Render combined staged + saved rows */
  function renderList(savedRows){
    savedRows = savedRows || [];
    var typeLabel = {1:'SS',2:'FS',3:'FF'};
    var html = '<table class="rl2-list-table">'
      + '<thead><tr>'
      + '<th>Precedent IOW</th><th>Precedent Activity</th>'
      + '<th style="text-align:center;">Type</th>'
      + '<th>Dependent IOW</th><th>Dependent Activity</th>'
      + '<th style="text-align:center;">Lag</th>'
      + '<th style="text-align:center;width:36px;">&#x2714;</th>'
      + '</tr></thead><tbody>';

    /* Staged rows first (green tint, data-staged) */
    _staged.forEach(function(r, i){
      var lag = r.lag_days > 0 ? '+'+r.lag_days+'d' : (r.lag_days < 0 ? r.lag_days+'d (lead)' : '—');
      html += '<tr class="rl2-staged" data-staged="'+i+'">'
        + '<td>'+esc(r.precedent_iow)+'</td>'
        + '<td>'+esc(r.precedent_name)+'</td>'
        + '<td style="text-align:center;"><span class="rl2-badge">'+esc(r.type_name)+'</span></td>'
        + '<td>'+esc(r.dependent_iow)+'</td>'
        + '<td>'+esc(r.dependent_name)+'</td>'
        + '<td style="text-align:center;font-size:11px;">'+lag+'</td>'
        + '<td style="text-align:center;"><input type="checkbox" class="rl2-row-chk" data-staged="'+i+'" checked></td>'
        + '</tr>';
    });

    /* Saved rows from DB */
    savedRows.forEach(function(r){
      var lag = parseInt(r.lag_days);
      var lagStr = lag > 0 ? '+'+lag+'d' : (lag < 0 ? lag+'d (lead)' : '—');
      html += '<tr data-id="'+r.id+'">'
        + '<td>'+esc(r.prec_iow||'')+'</td>'
        + '<td>'+esc(r.prec_name||'')+'</td>'
        + '<td style="text-align:center;"><span class="rl2-badge" style="background:#00796b;">'+esc(typeLabel[r.relation_type]||'FS')+'</span></td>'
        + '<td>'+esc(r.dep_iow||'')+'</td>'
        + '<td>'+esc(r.dep_name||'')+'</td>'
        + '<td style="text-align:center;font-size:11px;">'+lagStr+'</td>'
        + '<td style="text-align:center;">'
        +   '<button class="rl2-del-btn btn btn-xs" data-id="'+r.id+'" title="Delete" style="background:#e57373;color:#fff;border:none;border-radius:50%;width:22px;height:22px;padding:0;font-size:12px;">&#x2715;</button>'
        + '</td>'
        + '</tr>';
    });

    if(!_staged.length && !savedRows.length){
      html += '<tr><td colspan="7" style="text-align:center;color:#aaa;padding:20px;">No relationships defined yet.</td></tr>';
    }

    html += '</tbody></table>';
    if(_staged.length){
      html = '<div style="font-size:11px;color:#4caf50;font-weight:600;margin-bottom:6px;">&#x25CF; Green rows are staged (not yet saved). Select and click <em>Create Selected</em>.</div>' + html;
    }
    $('#rel-list-content').html(html);
    document.getElementById('rel-chk-all').checked = false;
  }

  function esc(s){ return $('<span>').text(s).html(); }

  /* Delete saved row */
  $(document).on('click','.rl2-del-btn', function(){
    var id = $(this).data('id');
    if(!confirm('Delete this relationship?')) return;
    $.ajax({ type:'POST', url:'../relation/delete', dataType:'json',
      data:{ id:id, projectid:_pid },
      success:function(d){
        if(d && d.error==='No'){ loadSavedList(); }
        else { alert('Could not delete.'); }
      }
    });
  });

  /* ── Create Selected ─────────────────────────────────────────── */
  document.getElementById('rel-create-btn').addEventListener('click', function(){
    _pid = _getPid();
    if(!_pid){ alert('No project selected.'); return; }

    /* Collect checked staged rows */
    var toSave = [];
    document.querySelectorAll('.rl2-row-chk[data-staged]').forEach(function(chk){
      if(!chk.checked) return;
      var idx = parseInt(chk.dataset.staged);
      var r = _staged[idx];
      if(r) toSave.push({
        precedent_activity:      r.precedent_activity,
        dependent_activity:      r.dependent_activity,
        precedent_schedule_item: r.precedent_schedule_item,
        dependent_schedule_item: r.dependent_schedule_item,
        relation_type:           r.relation_type,
        lag_days:                r.lag_days
      });
    });

    if(!toSave.length){ alert('No staged relationships selected. Tick the checkboxes next to the green rows.'); return; }

    var $btn = $(this).attr('disabled',true).text('Saving…');
    $.ajax({ type:'POST', url:'../relation/save', dataType:'json',
      data:{ projectid:_pid, rows:toSave },
      success:function(d){
        $btn.attr('disabled',false).html('&#x2714; Create Selected');
        if(d && d.error==='No'){
          /* Remove saved staged rows from memory */
          var savedIndices = [];
          document.querySelectorAll('.rl2-row-chk[data-staged]').forEach(function(chk){
            if(chk.checked) savedIndices.push(parseInt(chk.dataset.staged));
          });
          savedIndices.sort(function(a,b){return b-a;}).forEach(function(i){ _staged.splice(i,1); });

          /* Reload Gantt to show new dependency arrows */
          if(typeof window.reloadGantt === 'function') window.reloadGantt();

          /* Refresh list */
          loadSavedList();
        } else { alert((d && d.errortext) || 'Could not save.'); }
      },
      error:function(){ $btn.attr('disabled',false).html('&#x2714; Create Selected'); alert('Server error.'); }
    });
  });

  /* ═══════════════════════════════════════════════════════════════
     Init drag+resize bindings
  ═══════════════════════════════════════════════════════════════ */
  $(function(){
    rl2BindDragResize(document.getElementById('rel-add-popup'),  '.rl2-sw-hdr', 600, 280);
    rl2BindDragResize(document.getElementById('rel-list-popup'), '.rl2-sw-hdr', 600, 300);
  });

})();
</script>
