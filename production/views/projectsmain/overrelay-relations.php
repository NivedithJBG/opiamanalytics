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
  top:80px; left:calc(50% - 380px);
  width:760px; height:460px;
  min-width:560px; min-height:280px;
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

/* ── Tab strip (Add / List) inside the main window ──────────────── */
#rel-tabs { display:flex; background:#f9f5ff; flex-shrink:0; border-bottom:1px solid #e8e0f5; }
.rel-tab {
  flex:1; background:#f9f5ff; border:none; padding:10px 10px; font-size:13px; font-weight:700;
  color:#7a6a99; cursor:pointer; text-transform:uppercase; letter-spacing:.3px;
  border-bottom:3px solid transparent;
}
.rel-tab:hover { background:#f0e9fb; }
.rel-tab.active { background:#fff; color:#3d2060; border-bottom-color:#5c3d8f; }
.rel-tab-pane { display:none; flex-direction:column; flex:1; min-height:0; }
.rel-tab-pane.active { display:flex; }

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
  grid-template-columns: 1fr 48px 1fr;
  gap:0 8px;
  align-items:start;
}
.rl2-arrow-col {
  display:flex; align-items:center; justify-content:center;
  padding-top:135px;
}
#rel-type-badge {
  display:inline-block; width:28px; height:28px; line-height:28px;
  border-radius:50%; background:#5c3d8f; color:#fff;
  font-size:10px; font-weight:700; text-align:center;
  box-shadow:0 2px 6px rgba(92,61,143,.4);
}
.rl2-bottom-row {
  grid-column: 1 / -1;
  display:flex; align-items:center; justify-content:center;
  gap:20px; padding-top:14px; margin-top:10px;
  border-top:2px solid #ede7f6;
}
.rl2-bottom-row .rl2-section-lbl {
  font-size:11px; font-weight:700; color:#5c3d8f; text-transform:uppercase;
  letter-spacing:.5px; margin-right:6px;
}
.rl2-type-btn {
  width:52px; height:28px; border-radius:6px; border:1px solid #bbb;
  background:#f5f5f5; font-size:12px; font-weight:700; cursor:pointer;
  color:#333;
}
.rl2-type-btn.active { background:#5c3d8f; color:#fff; border-color:#5c3d8f; }

.rl2-lag-wrap {
  display:flex; align-items:center; gap:4px;
}
.rl2-lag-toggle {
  width:32px; height:28px; border-radius:6px; border:1px solid #bbb;
  background:#f5f5f5; font-size:14px; font-weight:700; cursor:pointer; color:#333;
}
.rl2-lag-toggle.lead { background:#e57373; color:#fff; border-color:#e57373; }
.rl2-lag-toggle.lag  { background:#4caf50; color:#fff; border-color:#4caf50; }
#rel-lag-days { width:52px; height:28px; border-radius:6px; border:1px solid #bbb; text-align:center; font-size:13px; }
#rel-lag-label { font-size:11px; color:#888; min-width:54px; }

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

  <div id="rel-tabs">
    <button type="button" class="rel-tab active" data-tab="add">Add Relationship</button>
    <button type="button" class="rel-tab" data-tab="list">&#x2630; List</button>
  </div>

  <!-- ── TAB: Add Relationship ────────────────────────────────── -->
  <div class="rel-tab-pane active" id="rel-tab-add">
    <div id="rel-body">
      <div id="rel-pick-hint" style="padding:6px 4px 10px;font-size:12px;color:#5c3d8f;font-weight:600;">
        Tip: instead of using the dropdowns below, click an activity directly on the Gantt chart — the first click sets Precedent, the next sets Dependent.
      </div>
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

        <!-- TYPE BADGE -->
        <div class="rl2-arrow-col">
          <span id="rel-type-badge">FS</span>
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

        <!-- BOTTOM ROW: Type + Lag/Lead -->
        <div class="rl2-bottom-row">
          <span class="rl2-section-lbl">Type:</span>
          <button class="rl2-type-btn active" data-type="2">FS</button>
          <button class="rl2-type-btn" data-type="1">SS</button>
          <button class="rl2-type-btn" data-type="3">FF</button>
          <span class="rl2-section-lbl" style="margin-left:16px;">Lag / Lead:</span>
          <div class="rl2-lag-wrap">
            <button class="rl2-lag-toggle lag" id="rel-lag-toggle" title="Toggle Lag/Lead">+</button>
            <input type="number" id="rel-lag-days" value="0" min="0">
            <span id="rel-lag-label">Lag days</span>
          </div>
        </div>

      </div><!-- /.rl2-add-grid -->

      <!-- Relationships added in THIS session — grows below the form as
           each tick saves one to the Gantt. The form stays open the whole
           time (IOW/Activity selections stay put too); this is just a
           running confirmation list, not a separate screen. -->
      <div id="rel-session-added" style="margin-top:14px;"></div>
    </div>
    <div class="rl2-sw-footer">
      <button id="rel-add-to-list" class="btn" title="Add Relationship" style="background:#27ae60;color:#fff;border-radius:50%;width:36px;height:36px;padding:0;font-size:16px;font-weight:700;">&#10003;</button>
      <button id="rel-win-close-2" class="btn" style="background:#aaa;color:#fff;border-radius:20px;">Close</button>
    </div>
  </div>

  <!-- ── TAB: List ────────────────────────────────────────────── -->
  <div class="rel-tab-pane" id="rel-tab-list">
    <div style="padding:10px 14px;flex-shrink:0;">
      <input type="text" id="rel-tpl-search" placeholder="Search activity…" style="width:280px;height:32px;border:1px solid #ccc;border-radius:20px;padding:0 14px;font-size:13px;">
    </div>
    <div id="rel-list-content" style="flex:1;overflow-y:auto;padding:0 14px 14px;">
      <p style="color:#aaa;text-align:center;padding:20px;">Loading…</p>
    </div>
  </div>
</div>


<!-- ══ LIVE RELATIONSHIPS SUB-POPUP ═════════════════════════════
     Opened by clicking the project row/bar on the Gantt — shows only
     the relationships ALREADY on THIS project's Gantt right now, with
     Edit (change Type/Lag/which activities) and Delete. Distinct from
     the List tab inside #rel-win (the historical search+tick template
     catalog, reachable via the tab strip's "List" tab). ═══════════════ -->
<div id="rel-live-popup" class="rl2-subwin" style="top:110px;left:calc(50% - 500px);width:1000px;height:480px;">
  <div class="rl2-rs rl2-rs-n" data-dir="n"></div><div class="rl2-rs rl2-rs-s" data-dir="s"></div>
  <div class="rl2-rs rl2-rs-e" data-dir="e"></div><div class="rl2-rs rl2-rs-w" data-dir="w"></div>
  <div class="rl2-rs rl2-rs-ne" data-dir="ne"></div><div class="rl2-rs rl2-rs-nw" data-dir="nw"></div>
  <div class="rl2-rs rl2-rs-se" data-dir="se"></div><div class="rl2-rs rl2-rs-sw" data-dir="sw"></div>
  <div class="rl2-sw-hdr">
    <h4>&#x2630; Relationships in this Gantt Chart</h4>
    <div class="rl2-sw-hdr-btns"><button id="rel-live-close">&times;</button></div>
  </div>
  <div class="rl2-sw-body" style="padding:10px 14px;">
    <div id="rel-live-content">
      <p style="color:#aaa;text-align:center;padding:20px;">Loading…</p>
    </div>
  </div>
</div>


<script>
(function(){
  /* Fallback in case this script ever runs before main.php's shared
     bindDragTouch is defined — without this, a ReferenceError here would
     silently abort every click handler bound later in this file (Activity
     Relationships nav icon, sub-popup open buttons, etc). */
  if(typeof window.bindDragTouch !== 'function'){
    window.bindDragTouch = function(el, mouseType, handler, opts){
      if(el) el.addEventListener(mouseType, handler, opts);
    };
  }

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
    bindDragTouch(hdr, 'mousedown', function(e){
      if(e.target.closest('.rl2-sw-hdr-btns, #rel-win-hdr-btns')) return;
      var r = rl2Anchor(win);
      _drag2 = {win:win,action:'drag',sx:e.clientX,sy:e.clientY,ox:r.left,oy:r.top,ow:r.width,oh:r.height,mw:minW,mh:minH};
      e.preventDefault();
    });
    win.querySelectorAll('.rl2-rs').forEach(function(el){
      bindDragTouch(el, 'mousedown', function(e){
        var r = rl2Anchor(win);
        _drag2 = {win:win,action:el.dataset.dir,sx:e.clientX,sy:e.clientY,ox:r.left,oy:r.top,ow:r.width,oh:r.height,mw:minW,mh:minH};
        e.preventDefault(); e.stopPropagation();
      });
    });
  }

  bindDragTouch(document, 'mousemove', function(e){
    if(!_drag2) return;
    e.preventDefault();
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
  }, { passive: false });
  bindDragTouch(document, 'mouseup', function(){ _drag2=null; });

  /* ═══════════════════════════════════════════════════════════════
     State
  ═══════════════════════════════════════════════════════════════ */
  var _pid   = '';   /* active project id */
  var _groups= [];   /* [{Workgroup_Id, iow_name}, ...] */
  var _acts  = [];   /* [{id, name, scheduleitem_id}, ...] */
  var _staged= [];   /* pending rows not yet saved to DB */
  var _swZ   = 200100;

  /* Sub-popups must always sit above the CURRENT top-level floater ceiling
     (window.popupSubZBase, from the shared focus-manager), not a fixed
     number — the parent window's own z-index keeps rising every time it's
     clicked/dragged, so a static base gets overtaken eventually. */
  function _bringToFront(el){
    var base = (typeof window.popupSubZBase === 'function') ? window.popupSubZBase() : 200100;
    if (base >= _swZ) _swZ = base;
    el.style.zIndex = ++_swZ;
  }

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
    if(typeof window.popupBringToFront === 'function') window.popupBringToFront('rel-win');
    document.dispatchEvent(new Event('rel:open'));
    /* Opens straight into the Add Relationship tab (no chooser screen) —
       loads the IOW/Activity options fresh each time the window opens,
       but does NOT reset a selection the user already made mid-session;
       that only happens the first time (options are empty anyway). */
    loadGanttActivities(function(){
      var iowOpts = _buildIowOptions();
      var precWasEmpty = !document.getElementById('rel-prec-iow').value;
      var depWasEmpty  = !document.getElementById('rel-dep-iow').value;
      var precVal = document.getElementById('rel-prec-iow').value;
      var depVal  = document.getElementById('rel-dep-iow').value;
      document.getElementById('rel-prec-iow').innerHTML = iowOpts;
      document.getElementById('rel-dep-iow').innerHTML  = iowOpts;
      if(!precWasEmpty){
        document.getElementById('rel-prec-iow').value = precVal;
        document.getElementById('rel-prec-act').innerHTML = _buildActOptions(precVal);
      }
      if(!depWasEmpty){
        document.getElementById('rel-dep-iow').value = depVal;
        document.getElementById('rel-dep-act').innerHTML = _buildActOptions(depVal);
      }
    });
  });
  document.getElementById('rel-win-close').addEventListener('click', function(){
    relWin.classList.remove('rl2-open');
  });
  document.getElementById('rel-win-close-2').addEventListener('click', function(){
    relWin.classList.remove('rl2-open');
  });

  /* ── Tab switching (Add / List) — independent of the old sub-popup
     open/close helpers below, which now only serve rel-live-popup. ── */
  document.querySelectorAll('.rel-tab').forEach(function(tabBtn){
    tabBtn.addEventListener('click', function(){
      document.querySelectorAll('.rel-tab').forEach(function(b){ b.classList.remove('active'); });
      document.querySelectorAll('.rel-tab-pane').forEach(function(p){ p.classList.remove('active'); });
      tabBtn.classList.add('active');
      var tabName = tabBtn.getAttribute('data-tab');
      document.getElementById('rel-tab-' + tabName).classList.add('active');
      if(tabName === 'list') loadTemplateList();
    });
  });

  /* ═══════════════════════════════════════════════════════════════
     Sub-popup open helpers — only rel-live-popup uses these now
  ═══════════════════════════════════════════════════════════════ */
  var _rel2SubIds = ['rel-live-popup'];
  if(typeof window.registerSubPopupReraise === 'function'){
    window.registerSubPopupReraise('rel-win', function(){
      _rel2SubIds.forEach(function(id){
        var el = document.getElementById(id);
        if(el && el.classList.contains('rl2-sw-open')) _bringToFront(el);
      });
    });
  }
  function openSub(id){
    var el=document.getElementById(id); el.classList.add('rl2-sw-open'); _bringToFront(el);
    if(typeof window.cascadeSubWindow === 'function') window.cascadeSubWindow(el, 'rel', _rel2SubIds);
  }
  function closeSub(id){ document.getElementById(id).classList.remove('rl2-sw-open'); }

  _rel2SubIds.forEach(function(id){
    var el=document.getElementById(id);
    bindDragTouch(el, 'mousedown', function(){ _bringToFront(el); }, true);
  });

  /* ═══════════════════════════════════════════════════════════════
     ADD RELATIONSHIP tab — form is always present in #rel-tab-add now;
     no open/close plumbing needed, just the field wiring below.
  ═══════════════════════════════════════════════════════════════ */

  /* IOW → Activity cascade */
  $(document).on('change','#rel-prec-iow', function(){
    $('#rel-prec-act').html(_buildActOptions($(this).val()));
  });
  $(document).on('change','#rel-dep-iow', function(){
    $('#rel-dep-act').html(_buildActOptions($(this).val()));
  });

  /* Pick Precedent/Dependent by clicking activities directly on the
     Gantt chart instead of using the dropdowns — called from
     newganttview.php's own activity row/bar click handler. Only takes
     effect while the Add Relationship tab is actually open and showing
     (checked here, not by the caller, so the Gantt's click logic stays
     simple); otherwise a stray click elsewhere in the app could
     silently overwrite a form the user isn't even looking at.
     Alternates PRECEDENT → DEPENDENT → PRECEDENT... each call, so
     clicking two activities in sequence fills both sides in the order
     clicked, same as manually picking IOW then Activity in each column.
     Needs loadGanttActivities's _acts (id → name/scheduleitem_id) to
     already be loaded — same data source the manual dropdowns use — so
     it's loaded on demand if this is the very first pick of the
     session. */
  var _relPickSide = 'prec';
  // Cheap "is the picker actually live right now" check — used by
  // newganttview.php's KPI-popup click handler to bail out WITHOUT
  // consuming/filling a field (that's what the full _relPickFromGantt
  // call further below in that same file does); this one only answers
  // the question so the KPI popup doesn't also fire on the same click.
  window._relPickIsActive = function(){
    var relWin = document.getElementById('rel-win');
    var addTab = document.querySelector('.rel-tab.active[data-tab="add"]');
    return !!(relWin && relWin.classList.contains('rl2-open') && addTab);
  };
  window._relPickFromGantt = function(actId){
    if(!window._relPickIsActive()) return false;

    function fill(){
      var act = _acts.filter(function(a){ return String(a.id) === String(actId); })[0];
      if(!act) return;
      var iowSel  = _relPickSide === 'prec' ? '#rel-prec-iow' : '#rel-dep-iow';
      var actSel  = _relPickSide === 'prec' ? '#rel-prec-act' : '#rel-dep-act';
      $(iowSel).val(act.scheduleitem_id);
      $(actSel).html(_buildActOptions(act.scheduleitem_id)).val(act.id);
      _relPickSide = (_relPickSide === 'prec') ? 'dep' : 'prec';
      _updatePickHint();
    }
    if(_acts.length){ fill(); } else { loadGanttActivities(fill); }
    return true;
  };

  function _updatePickHint(){
    var el = document.getElementById('rel-pick-hint');
    if(!el) return;
    el.textContent = _relPickSide === 'prec'
      ? 'Click an activity on the Gantt chart to set it as PRECEDENT.'
      : 'Precedent set — now click an activity on the Gantt chart to set it as DEPENDENT.';
  }
  _updatePickHint();

  /* Relation type toggle */
  $(document).on('click','.rl2-type-btn', function(){
    document.querySelectorAll('.rl2-type-btn').forEach(function(b){ b.classList.remove('active'); });
    this.classList.add('active');
    document.getElementById('rel-type-badge').textContent = this.textContent;
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

    var row = {
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
      lag_days:                lagDays
    };

    /* Saves THIS one relationship immediately — no staging, no switch to
       the List popup. The form stays open; a running list of what's been
       added in this session grows below it (#rel-session-added). */
    _pid = _getPid();
    var $tick = $(this).prop('disabled', true);
    $.ajax({ type:'POST', url:'../relation/save', dataType:'json',
      data:{ projectid:_pid, rows:[row] },
      success:function(d){
        $tick.prop('disabled', false);
        if(d && d.error==='No' && d.inserted > 0){
          _sessionAdded.push(row);
          renderSessionAdded();
          if(typeof window.reloadGantt === 'function') window.reloadGantt();

          /* Reset only the Activity pickers + lag for the next entry —
             IOW selections on both sides stay put (per user request: keep
             adding relationships under the same IOWs without reselecting
             them every time). */
          document.getElementById('rel-prec-act').value = '';
          document.getElementById('rel-dep-act').value  = '';
          document.getElementById('rel-lag-days').value = '0';
        } else if(d && d.inserted === 0){
          alert('This relationship already exists.');
        } else {
          alert((d && d.errortext) || 'Could not save.');
        }
      },
      error:function(){ $tick.prop('disabled', false); alert('Server error.'); }
    });
  });

  /* Running list of relationships added in this Add-form session —
     purely a confirmation display, not the historical List popup. */
  var _sessionAdded = [];
  function renderSessionAdded(){
    var target = document.getElementById('rel-session-added');
    if(!_sessionAdded.length){ target.innerHTML = ''; return; }
    var html = '<div style="font-size:11px;font-weight:700;color:#5c3d8f;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Added this session</div>'
      + '<table class="rl2-list-table"><thead><tr>'
      + '<th>Precedent</th><th style="text-align:center;">Type</th><th>Dependent</th><th style="text-align:center;">Lag</th>'
      + '</tr></thead><tbody>';
    _sessionAdded.forEach(function(r){
      var lag = r.lag_days > 0 ? '+'+r.lag_days+'d' : (r.lag_days < 0 ? r.lag_days+'d (lead)' : '—');
      html += '<tr>'
        + '<td>'+esc(r.precedent_iow)+' — '+esc(r.precedent_name)+'</td>'
        + '<td style="text-align:center;"><span class="rl2-badge">'+esc(r.type_name)+'</span></td>'
        + '<td>'+esc(r.dependent_iow)+' — '+esc(r.dependent_name)+'</td>'
        + '<td style="text-align:center;font-size:11px;">'+lag+'</td>'
        + '</tr>';
    });
    html += '</tbody></table>';
    target.innerHTML = html;
  }


  /* ═══════════════════════════════════════════════════════════════
     LIST tab — content lives in #rel-tab-list inside the same window;
     tab click handler above calls loadTemplateList() on activation.
  ═══════════════════════════════════════════════════════════════ */

  /* Search box — debounced re-query against relation/listtemplates. */
  var _tplSearchTimer = null;
  document.getElementById('rel-tpl-search').addEventListener('input', function(){
    clearTimeout(_tplSearchTimer);
    _tplSearchTimer = setTimeout(loadTemplateList, 300);
  });

  /* Loads the historical relationship catalog — every distinct
     (precedent library activity, dependent library activity, type)
     combination ever created across ALL projects, optionally filtered
     by the search box. Ticking a row recreates it for THIS project. */
  function loadTemplateList(){
    _pid = _getPid();
    $('#rel-list-content').html('<p style="color:#aaa;text-align:center;padding:20px;">Loading…</p>');
    if(!_pid){ $('#rel-list-content').html('<p style="color:#e57373;text-align:center;padding:20px;">No project selected.</p>'); return; }
    var search = document.getElementById('rel-tpl-search').value.trim();
    $.ajax({ type:'POST', url:'../relation/listtemplates', dataType:'json',
      data:{ search: search },
      success:function(d){
        renderTemplateList(d && d.error==='No' ? d.rows : []);
      },
      error:function(){ $('#rel-list-content').html('<p style="color:#e57373;text-align:center;padding:20px;">Could not load.</p>'); }
    });
  }

  function renderTemplateList(rows){
    rows = rows || [];
    var typeLabel = {1:'SS',2:'FS',3:'FF'};
    var html = '<table class="rl2-list-table">'
      + '<thead><tr>'
      + '<th>Precedent Activity</th>'
      + '<th style="text-align:center;">Type</th>'
      + '<th>Dependent Activity</th>'
      + '<th style="text-align:center;">Lag</th>'
      + '<th style="text-align:center;width:44px;"></th>'
      + '<th style="text-align:center;width:40px;">&#x2714;</th>'
      + '</tr></thead><tbody>';

    rows.forEach(function(r){
      var lag = parseInt(r.lag_days) || 0;
      var lagStr = lag > 0 ? '+'+lag+'d' : (lag < 0 ? lag+'d (lead)' : '—');
      html += '<tr data-preclib="'+r.prec_lib_id+'" data-deplib="'+r.dep_lib_id+'" data-reltype="'+r.relation_type+'" data-lag="'+lag+'">'
        + '<td class="rl2-col-precname">'+esc(r.prec_name||'')+'</td>'
        + '<td class="rl2-col-type" style="text-align:center;"><span class="rl2-badge" style="background:#00796b;">'+esc(typeLabel[r.relation_type]||'FS')+'</span></td>'
        + '<td class="rl2-col-depname">'+esc(r.dep_name||'')+'</td>'
        + '<td class="rl2-col-lag" style="text-align:center;font-size:11px;">'+lagStr+'</td>'
        + '<td style="text-align:center;"><button class="rl2-tpl-edit-btn btn btn-xs" title="Edit" style="background:#5c3d8f;color:#fff;border:none;border-radius:50%;width:22px;height:22px;padding:0;font-size:11px;">&#9998;</button></td>'
        + '<td style="text-align:center;"><button class="rl2-tpl-tick-btn btn btn-xs" title="Add to Gantt" style="background:#27ae60;color:#fff;border:none;border-radius:50%;width:22px;height:22px;padding:0;font-size:12px;">&#10003;</button></td>'
        + '</tr>';
    });

    if(!rows.length){
      html += '<tr><td colspan="6" style="text-align:center;color:#aaa;padding:20px;">No matching relationships found.</td></tr>';
    }

    html += '</tbody></table>';
    $('#rel-list-content').html(html);
  }

  /* Edit (pencil) on a template row — unlocks Type/Lag only, NOT which
     activities are paired (that's the template's own identity). Second
     click re-locks the row; it does not save anything by itself — the
     new Type/Lag values are only used when the tick is clicked. */
  $(document).on('click', '.rl2-tpl-edit-btn', function(){
    var btn = this;
    var $row = $(btn).closest('tr');
    var isEditing = $(btn).hasClass('rl2-editing');

    if(!isEditing){
      var relType = parseInt($row.data('reltype')) || 2;
      var lag = parseInt($row.data('lag')) || 0;
      $row.find('.rl2-col-type').html(
        '<select class="rl2-tpl-type-edit form-control input-sm" style="width:60px;">'
        + '<option value="2"'+(relType===2?' selected':'')+'>FS</option>'
        + '<option value="1"'+(relType===1?' selected':'')+'>SS</option>'
        + '<option value="3"'+(relType===3?' selected':'')+'>FF</option>'
        + '</select>'
      );
      $row.find('.rl2-col-lag').html('<input type="number" class="rl2-tpl-lag-edit form-control input-sm" style="width:60px;" value="'+lag+'">');
      $(btn).addClass('rl2-editing').css('background','#27ae60');
      return;
    }

    var newType = parseInt($row.find('.rl2-tpl-type-edit').val()) || 2;
    var newLag  = parseInt($row.find('.rl2-tpl-lag-edit').val()) || 0;
    $row.data('reltype', newType);
    $row.data('lag', newLag);
    var typeLabel = {1:'SS',2:'FS',3:'FF'};
    $row.find('.rl2-col-type').html('<span class="rl2-badge" style="background:#00796b;">'+esc(typeLabel[newType])+'</span>');
    var lagStr = newLag > 0 ? '+'+newLag+'d' : (newLag < 0 ? newLag+'d (lead)' : '—');
    $row.find('.rl2-col-lag').text(lagStr);
    $(btn).removeClass('rl2-editing').css('background','');
  });

  /* Tick — recreates this template's relationship for the CURRENT
     project via relation/createfromtemplate, which resolves both
     library activities to their live scheduleactivities row in this
     project (blocking with a clear message if either isn't scheduled
     here yet) and saves exactly like the Add form's single-row save. */
  $(document).on('click', '.rl2-tpl-tick-btn', function(){
    var btn = this;
    var $row = $(btn).closest('tr');
    _pid = _getPid();
    if(!_pid){ alert('No project selected.'); return; }

    $(btn).prop('disabled', true);
    $.ajax({ type:'POST', url:'../relation/createfromtemplate', dataType:'json',
      data:{
        projectid: _pid,
        prec_lib_id: $row.data('preclib'),
        dep_lib_id: $row.data('deplib'),
        relation_type: $row.data('reltype'),
        lag_days: $row.data('lag')
      },
      success:function(d){
        $(btn).prop('disabled', false);
        if(d && d.error==='No'){
          if(typeof window.reloadGantt === 'function') window.reloadGantt();
          $(btn).css('background','#1e8449').html('&#10003;');
        } else {
          alert((d && d.errortext) || 'Could not add.');
        }
      },
      error:function(){ $(btn).prop('disabled', false); alert('Server error.'); }
    });
  });

  function esc(s){ return $('<span>').text(s).html(); }

  /* ═══════════════════════════════════════════════════════════════
     LIVE RELATIONSHIPS popup — entry point for clicking the project
     row/bar on the Gantt. Shows only relationships already saved and
     live on THIS project's Gantt (actionList — unlike the template
     catalog above, this is scoped strictly to _pid), with Edit
     (change which activities/type/lag) and Delete.
  ═══════════════════════════════════════════════════════════════ */
  // pTarget (optional): {precAct, depAct} — the exact relationship to
  // jump straight into edit mode for, e.g. when the user clicked a
  // specific dependency line/arrow on the Gantt rather than the project
  // row/bar (which opens the plain unscoped list). Matched against each
  // row's own data-precact/data-depact once the list has loaded — see
  // the click handler on .gDep* elements in newganttview.php, which
  // reads the same precedent/successor activity ids straight off the
  // line's own gDepDataId/gDepNextDataId CSS classes (no extra lookup
  // needed, since JSGantt already embeds them there for the critical-
  // path check).
  window.openProjectRelationsList = function(pTarget){
    openSub('rel-live-popup');
    loadLiveList(pTarget || null);
  };
  document.getElementById('rel-live-close').addEventListener('click', function(){ closeSub('rel-live-popup'); });

  function loadLiveList(pTarget){
    _pid = _getPid();
    $('#rel-live-content').html('<p style="color:#aaa;text-align:center;padding:20px;">Loading…</p>');
    if(!_pid){ $('#rel-live-content').html('<p style="color:#e57373;text-align:center;padding:20px;">No project selected.</p>'); return; }
    $.ajax({ type:'POST', url:'../relation/list', dataType:'json',
      data:{ projectid: _pid },
      success:function(d){
        renderLiveList(d && d.error==='No' ? d.rows : []);
        if(pTarget) _jumpToEdit(pTarget);
      },
      error:function(){ $('#rel-live-content').html('<p style="color:#e57373;text-align:center;padding:20px;">Could not load.</p>'); }
    });
  }

  // Finds the row(s) matching pTarget and clicks Edit, same as a user
  // would — no separate code path, so it stays in sync with whatever
  // the manual edit flow does. Two match modes:
  //  - pTarget.precAct + pTarget.depAct: the exact relationship (both
  //    real scheduleactivities ids) — used when a specific dependency
  //    line/arrow was clicked, unambiguous, always exactly one row.
  //  - pTarget.actId alone: any row where this activity is EITHER the
  //    precedent or the dependent — used when the click only identified
  //    an activity (e.g. clicking anywhere in its row), not a specific
  //    line. If the activity has exactly one relationship, jumps straight
  //    into editing it, same as the exact-pair case. If it has more than
  //    one, opens all of them into edit mode at once and scrolls to the
  //    first — editing multiple relationships for the same activity
  //    side-by-side is exactly what "show that activity's relationship"
  //    means once there's more than one to show.
  // When pTarget.actId has NO existing relationship at all (neither as
  // precedent nor dependent), falls through to _openAddWithPrecedent
  // instead of leaving the (now-empty) live list showing — see there.
  function _jumpToEdit(pTarget){
    var $all = $('#rel-live-content tr[data-id]');
    var $rows;
    if(pTarget.precAct !== undefined && pTarget.depAct !== undefined){
      $rows = $all.filter(function(){
        return String($(this).data('precact')) === String(pTarget.precAct)
            && String($(this).data('depact'))  === String(pTarget.depAct);
      });
    } else if(pTarget.actId !== undefined){
      $rows = $all.filter(function(){
        return String($(this).data('precact')) === String(pTarget.actId);
      });
    } else {
      return;
    }
    if(!$rows.length){
      if(pTarget.actId !== undefined) _openAddWithPrecedent(pTarget.actId);
      return;
    }
    // Hide every non-matching row so the popup reads as "this activity's
    // relationship(s)" rather than the full unscoped list with a couple
    // of rows quietly switched into edit mode — that's what originally
    // read as "all relationships popping up" even though the match
    // itself was already correct. A "Show all" link (prepended once,
    // above the table) brings the full list back on demand.
    $all.not($rows).hide();
    // Clicking an activity (not a specific arrow) must never edit its existing
    // relationship: that overwrote the row on save, so a second relationship
    // replaced the first. Show the existing rows read-only and open a new Add row.
    if(pTarget.precAct === undefined && pTarget.actId !== undefined){ _openAddWithPrecedent(pTarget.actId); return; }
    $rows.show().each(function(){ $(this).find('.rl2-live-edit-btn').trigger('click'); });
    $rows[0].scrollIntoView({ block: 'center' });

    if(!$('#rel-live-showall').length){
      $('<div id="rel-live-showall" style="padding:8px 4px;"><a href="#" style="font-size:13px;color:#5c3d8f;">&#8592; Show all relationships</a></div>')
        .insertBefore('#rel-live-content table')
        .on('click', 'a', function(e){
          e.preventDefault();
          $all.show();
          $('#rel-live-showall').remove();
        });
    }
  }

  // Clicking an activity's row when it has NO relationship at all (see
  // _jumpToEdit above) inserts a new, blank row at the top of THIS
  // same live-relationships table (rel-live-popup) — same
  // dropdown/Type/Lag layout as editing an existing relationship, just
  // pre-filled with this activity as PRECEDENT and Dependent left
  // empty, already in edit mode. Saves via relation/save (a genuine
  // INSERT — relation/update, what an existing row's Save button
  // calls, requires an id and errors without one).
  function _openAddWithPrecedent(actId){
    function build(){
      var act = _acts.filter(function(a){ return String(a.id) === String(actId); })[0];
      var iowOpts = _buildIowOptions();
      var precActOpts = act ? _buildActOptions(act.scheduleitem_id) : '<option value="">-- Select Activity --</option>';

      var $row = $(
        '<tr class="rl2-new-row">'
        + '<td><select class="rl2-new-preciow form-control input-sm">' + iowOpts + '</select></td>'
        + '<td><select class="rl2-new-precact form-control input-sm">' + precActOpts + '</select></td>'
        + '<td style="text-align:center;"><select class="rl2-new-type form-control input-sm" style="width:60px;">'
          + '<option value="2" selected>FS</option><option value="1">SS</option><option value="3">FF</option>'
          + '</select></td>'
        + '<td><select class="rl2-new-depiow form-control input-sm">' + iowOpts + '</select></td>'
        + '<td><select class="rl2-new-depact form-control input-sm"><option value="">-- Select Activity --</option></select></td>'
        + '<td><input type="number" class="rl2-new-lag form-control input-sm" style="width:60px;" value="0"></td>'
        + '<td style="text-align:center;white-space:nowrap;">'
          + '<button class="rl2-new-save-btn btn btn-xs" title="Save" style="background:#27ae60;color:#fff;border:none;border-radius:50%;width:22px;height:22px;padding:0;font-size:11px;margin-right:3px;">&#10003;</button>'
          + '<button class="rl2-new-cancel-btn btn btn-xs" title="Cancel" style="background:#e57373;color:#fff;border:none;border-radius:50%;width:22px;height:22px;padding:0;font-size:12px;">&#x2715;</button>'
          + '</td>'
        + '</tr>'
      );
      if(act){
        $row.find('.rl2-new-preciow').val(act.scheduleitem_id);
        $row.find('.rl2-new-precact').val(act.id);
      }

      $row.find('.rl2-new-preciow').on('change', function(){
        $row.find('.rl2-new-precact').html(_buildActOptions($(this).val()));
      });
      $row.find('.rl2-new-depiow').on('change', function(){
        $row.find('.rl2-new-depact').html(_buildActOptions($(this).val()));
      });

      var $table = $('#rel-live-content table');
      if(!$table.length){
        renderLiveList([]);
        $table = $('#rel-live-content table');
      }
      // Same "only this activity" framing as the has-relationship case
      // (_jumpToEdit) — hide every existing relationship row so the new
      // blank row isn't buried in the full unfiltered list. The "Show
      // all relationships" link (added once, same as _jumpToEdit's)
      // brings them back on demand.
      var $existing = $table.find('tbody tr[data-id]');
      $existing.filter(function(){ return String($(this).data('precact')) !== String(actId); }).hide();
      $table.find('tbody tr td[colspan]').closest('tr').remove();
      $table.find('tbody').prepend($row);
      $row[0].scrollIntoView({ block: 'center' });

      if(!$('#rel-live-showall').length){
        $('<div id="rel-live-showall" style="padding:8px 4px;"><a href="#" style="font-size:13px;color:#5c3d8f;">&#8592; Show all relationships</a></div>')
          .insertBefore('#rel-live-content table')
          .on('click', 'a', function(e){
            e.preventDefault();
            $existing.show();
            $('#rel-live-showall').remove();
          });
      }
    }
    if(_acts.length){ build(); } else { loadGanttActivities(build); }
  }

  $(document).on('click', '.rl2-new-save-btn', function(){
    var btn = this;
    var $row = $(btn).closest('tr');
    var precAct = $row.find('.rl2-new-precact').val();
    var depAct  = $row.find('.rl2-new-depact').val();
    var precIow = $row.find('.rl2-new-preciow').val();
    var depIow  = $row.find('.rl2-new-depiow').val();
    var relType = parseInt($row.find('.rl2-new-type').val()) || 2;
    var lagDays = parseInt($row.find('.rl2-new-lag').val()) || 0;

    if(!precAct){ alert('Please select the Precedent Activity.'); return; }
    if(!depAct){ alert('Please select the Dependent Activity.'); return; }
    if(precAct === depAct){ alert('Precedent and Dependent cannot be the same activity.'); return; }

    $(btn).prop('disabled', true);
    $.ajax({ type:'POST', url:'../relation/save', dataType:'json',
      data:{
        projectid: _getPid(),
        rows: [{
          precedent_activity: precAct, dependent_activity: depAct,
          precedent_schedule_item: precIow, dependent_schedule_item: depIow,
          relation_type: relType, lag_days: lagDays
        }]
      },
      success:function(d){
        $(btn).prop('disabled', false);
        if(d && d.error==='No' && d.inserted > 0){
          if(typeof window.reloadGantt === 'function') window.reloadGantt();
          loadLiveList();
        } else {
          alert((d && d.errortext) || 'Could not save — check that both activities are valid and this relationship doesn\'t already exist.');
        }
      },
      error:function(){ $(btn).prop('disabled', false); alert('Server error.'); }
    });
  });

  $(document).on('click', '.rl2-new-cancel-btn', function(){
    $(this).closest('tr').remove();
  });

  function renderLiveList(rows){
    rows = rows || [];
    var typeLabel = {1:'SS',2:'FS',3:'FF'};
    var html = '<table class="rl2-list-table"><thead><tr>'
      + '<th>Precedent IOW</th><th>Precedent Activity</th>'
      + '<th style="text-align:center;">Type</th>'
      + '<th>Dependent IOW</th><th>Dependent Activity</th>'
      + '<th style="text-align:center;">Lag</th>'
      + '<th style="text-align:center;width:60px;"></th>'
      + '</tr></thead><tbody>';

    rows.forEach(function(r){
      var lag = parseInt(r.lag_days);
      var lagStr = lag > 0 ? '+'+lag+'d' : (lag < 0 ? lag+'d (lead)' : '—');
      html += '<tr data-id="'+r.id+'" data-precact="'+r.precedent_activity+'" data-preciow="'+(r.precedent_schedule_item||'')+'" data-depact="'+r.dependent_activity+'" data-depiow="'+(r.dependent_schedule_item||'')+'" data-reltype="'+r.relation_type+'" data-lag="'+r.lag_days+'">'
        + '<td>'+esc(r.prec_iow||'')+'</td>'
        + '<td>'+esc(r.prec_name||'')+'</td>'
        + '<td style="text-align:center;"><span class="rl2-badge" style="background:#00796b;">'+esc(typeLabel[r.relation_type]||'FS')+'</span></td>'
        + '<td>'+esc(r.dep_iow||'')+'</td>'
        + '<td>'+esc(r.dep_name||'')+'</td>'
        + '<td style="text-align:center;font-size:11px;">'+lagStr+'</td>'
        + '<td style="text-align:center;white-space:nowrap;">'
        +   '<button class="rl2-live-edit-btn btn btn-xs" title="Edit" style="background:#5c3d8f;color:#fff;border:none;border-radius:50%;width:22px;height:22px;padding:0;font-size:11px;margin-right:3px;">&#9998;</button>'
        +   '<button class="rl2-live-del-btn btn btn-xs" title="Delete" style="background:#e57373;color:#fff;border:none;border-radius:50%;width:22px;height:22px;padding:0;font-size:12px;">&#x2715;</button>'
        + '</td>'
        + '</tr>';
    });

    if(!rows.length){
      html += '<tr><td colspan="7" style="text-align:center;color:#aaa;padding:20px;">No relationships added to this Gantt chart yet.</td></tr>';
    }

    html += '</tbody></table>';
    $('#rel-live-content').html(html);
  }

  /* Delete a live relationship */
  $(document).on('click', '.rl2-live-del-btn', function(){
    var $row = $(this).closest('tr');
    var id = $row.data('id');
    if(!confirm('Delete this relationship?')) return;
    $.ajax({ type:'POST', url:'../relation/delete', dataType:'json',
      data:{ id:id, projectid:_getPid() },
      success:function(d){
        if(d && d.error==='No'){
          if(typeof window.reloadGantt === 'function') window.reloadGantt();
          loadLiveList();
        } else { alert('Could not delete.'); }
      }
    });
  });

  /* Edit a live relationship — toggle between view (plain text) and
     edit (the same IOW/Activity/Type/Lag pickers the Add form uses,
     rendered inline in the row). First click swaps cells 0-5 to
     inputs; second click reads them back, saves via relation/update. */
  $(document).on('click', '.rl2-live-edit-btn', function(){
    var btn = this;
    var $row = $(btn).closest('tr');
    var isEditing = $(btn).hasClass('rl2-editing');

    if(!isEditing){
      loadGanttActivities(function(){
        var precIow = $row.data('preciow');
        var depIow  = $row.data('depiow');
        var precAct = $row.data('precact');
        var depAct  = $row.data('depact');
        var relType = parseInt($row.data('reltype')) || 2;
        var lag     = parseInt($row.data('lag')) || 0;

        var iowOpts = _buildIowOptions();
        var precActOpts = _buildActOptions(precIow);
        var depActOpts  = _buildActOptions(depIow);

        $row.find('td').eq(0).html('<select class="rl2-live-edit-preciow form-control input-sm">'+iowOpts+'</select>');
        $row.find('td').eq(1).html('<select class="rl2-live-edit-precact form-control input-sm">'+precActOpts+'</select>');
        $row.find('td').eq(2).html(
          '<select class="rl2-live-edit-type form-control input-sm" style="width:60px;">'
          + '<option value="2"'+(relType===2?' selected':'')+'>FS</option>'
          + '<option value="1"'+(relType===1?' selected':'')+'>SS</option>'
          + '<option value="3"'+(relType===3?' selected':'')+'>FF</option>'
          + '</select>'
        );
        $row.find('td').eq(3).html('<select class="rl2-live-edit-depiow form-control input-sm">'+iowOpts+'</select>');
        $row.find('td').eq(4).html('<select class="rl2-live-edit-depact form-control input-sm">'+depActOpts+'</select>');
        $row.find('td').eq(5).html('<input type="number" class="rl2-live-edit-lag form-control input-sm" style="width:60px;" value="'+lag+'">');

        $row.find('.rl2-live-edit-preciow').val(precIow);
        $row.find('.rl2-live-edit-depiow').val(depIow);
        $row.find('.rl2-live-edit-precact').val(precAct);
        $row.find('.rl2-live-edit-depact').val(depAct);

        $row.find('.rl2-live-edit-preciow').on('change', function(){
          $row.find('.rl2-live-edit-precact').html(_buildActOptions($(this).val()));
        });
        $row.find('.rl2-live-edit-depiow').on('change', function(){
          $row.find('.rl2-live-edit-depact').html(_buildActOptions($(this).val()));
        });

        $(btn).addClass('rl2-editing').html('&#10003;').attr('title','Save').css('background','#4caf50');
      });
      return;
    }

    var newPrecAct = $row.find('.rl2-live-edit-precact').val();
    var newDepAct  = $row.find('.rl2-live-edit-depact').val();
    var newPrecIow = $row.find('.rl2-live-edit-preciow').val();
    var newDepIow  = $row.find('.rl2-live-edit-depiow').val();
    var newType    = parseInt($row.find('.rl2-live-edit-type').val()) || 2;
    var newLag     = parseInt($row.find('.rl2-live-edit-lag').val()) || 0;

    if(!newPrecAct){ alert('Please select the Precedent Activity.'); return; }
    if(!newDepAct){ alert('Please select the Dependent Activity.'); return; }
    if(newPrecAct === newDepAct){ alert('Precedent and Dependent cannot be the same activity.'); return; }

    var id = $row.data('id');
    $(btn).prop('disabled', true);
    $.ajax({ type:'POST', url:'../relation/update', dataType:'json',
      data:{
        id: id, projectid: _getPid(),
        precedent_activity: newPrecAct, dependent_activity: newDepAct,
        precedent_schedule_item: newPrecIow, dependent_schedule_item: newDepIow,
        relation_type: newType, lag_days: newLag
      },
      success:function(d){
        $(btn).prop('disabled', false);
        if(d && d.error==='No'){
          if(typeof window.reloadGantt === 'function') window.reloadGantt();
          loadLiveList();
        } else {
          alert((d && d.errortext) || 'Could not save.');
        }
      },
      error:function(){ $(btn).prop('disabled', false); alert('Server error.'); }
    });
  });

  /* NOTE: "Create Selected" (bulk-save of staged/checked rows) was
     removed along with the List popup's footer buttons — staged rows
     from the Add form currently have no save path. This is a known gap,
     to be addressed separately when the Add flow is reworked to save
     directly instead of staging. The List popup itself no longer shows
     already-saved relationships (Edit/Delete on a specific saved row) —
     it's now the historical-template search+tick catalog above. */

  /* ═══════════════════════════════════════════════════════════════
     Init drag+resize bindings
  ═══════════════════════════════════════════════════════════════ */
  $(function(){
    rl2BindDragResize(document.getElementById('rel-live-popup'), '.rl2-sw-hdr', 600, 300);
  });

})();
</script>
