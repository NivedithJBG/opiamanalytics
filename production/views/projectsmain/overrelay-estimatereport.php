<style>
/* ── Estimate Report overlay — resize handles (shared pattern) ─── */
.er-rs { position:absolute; z-index:10; background:transparent; }
.er-rs-e  { right:0;  top:6px;    bottom:6px; width:6px;  cursor:e-resize; }
.er-rs-w  { left:0;   top:6px;    bottom:6px; width:6px;  cursor:w-resize; }
.er-rs-s  { bottom:0; left:6px;   right:6px;  height:6px; cursor:s-resize; }
.er-rs-n  { top:0;    left:6px;   right:6px;  height:6px; cursor:n-resize; }
.er-rs-se { right:0;  bottom:0; width:14px; height:14px; cursor:se-resize; }
.er-rs-sw { left:0;   bottom:0; width:14px; height:14px; cursor:sw-resize; }
.er-rs-ne { right:0;  top:0;    width:14px; height:14px; cursor:ne-resize; }
.er-rs-nw { left:0;   top:0;    width:14px; height:14px; cursor:nw-resize; }

/* ── Main Estimate Report window ──────────────────────────────── */
#estreport-win {
  display:none; position:fixed;
  top:50px; left:calc(50% - 620px);
  width:1240px; height:calc(100vh - 90px);
  min-width:480px; min-height:320px;
  background:#fff; border-radius:8px;
  box-shadow:0 8px 32px rgba(0,0,0,.35);
  z-index:10005; flex-direction:column; overflow:hidden;
}
#estreport-win.er-open { display:flex; }
#estreport-win.er-resizing #estreport-body { overflow:hidden; }

#estreport-win-hdr {
  background:linear-gradient(135deg,#37475a,#26313f); color:#fff; padding:10px 15px;
  display:flex; align-items:center; justify-content:space-between;
  cursor:move; user-select:none; flex-shrink:0;
}
#estreport-win-hdr span { font-size:15px; font-weight:700; }
#estreport-win-hdr-btns { display:flex; align-items:center; gap:6px; }
#estreport-win-hdr-btns button {
  background:none; border:none; color:#fff;
  font-size:18px; line-height:1; cursor:pointer; padding:0 4px; opacity:.8;
}
#estreport-win-hdr-btns button:hover { opacity:1; }

#estreport-action-bar {
  display:flex; align-items:center; gap:8px;
  padding:9px 15px; border-bottom:1px solid #dde2e7; flex-shrink:0;
  background:#eef1f4;
}
#estreport-print-btn {
  border-radius:20px; padding:6px 18px; font-size:12px; font-weight:600;
  cursor:pointer; border:none; background:#37475a; color:#fff;
}
#estreport-print-btn:hover { background:#26313f; }

#estreport-body { flex:1; min-height:0; min-width:0; overflow-y:auto; overflow-x:hidden; padding:16px 22px; background:#f4f5f6; box-sizing:border-box; }

/* ── Report sheet ──────────────────────────────────────────────── */
#estreport-sheet { background:#fff; padding:20px 24px; border-radius:4px; box-shadow:0 1px 4px rgba(0,0,0,.08); }
.er-sheet-hdr { text-align:left; margin-bottom:16px; }
.er-sheet-hdr h2 { margin:0 0 4px; font-size:17px; font-weight:700; color:#37475a; }
.er-sheet-hdr .er-sheet-sub { font-size:11px; color:#888; letter-spacing:.5px; text-transform:uppercase; }

.er-table { width:100%; table-layout:fixed; border-collapse:collapse; font-size:12px; }
.er-table th {
  background:#6b7c93; color:#fff; padding:7px 9px;
  border:1px solid #55647a; text-align:left; white-space:normal; word-break:break-word;
}
.er-table th.er-num, .er-table td.er-num { text-align:right; }
.er-table th.er-sl, .er-table td.er-sl { text-align:center; width:5%; }
.er-table th:nth-child(2), .er-table td:nth-child(2) { width:20%; }
.er-table th:nth-child(3), .er-table td:nth-child(3) { width:7%; }
.er-table th:nth-child(4), .er-table td:nth-child(4),
.er-table th:nth-child(5), .er-table td:nth-child(5) { width:9%; }
.er-table th:nth-child(6) { width:11%; }
.er-table th:nth-child(7), .er-table td:nth-child(7) { width:10%; }
.er-table th:nth-child(8), .er-table td:nth-child(8),
.er-table th:nth-child(9), .er-table td:nth-child(9),
.er-table th:nth-child(10), .er-table td:nth-child(10) { width:10%; }
.er-table td {
  padding:6px 9px; border:1px solid #ddd; vertical-align:middle; color:#222;
  white-space:normal; word-break:break-word;
}
.er-table tr.er-iow-row td {
  background:#e4e7eb; font-weight:700; color:#37475a; padding:6px 9px;
  border:1px solid #cfd5db; font-size:12px;
}
.er-table tr.er-subtotal-row td {
  background:#f0f2f4; font-weight:700; color:#37475a; border:1px solid #dde2e7;
}
.er-table tr.er-grand-row td {
  background:#6b7c93; color:#fff; font-weight:700; font-size:13px; border:1px solid #55647a;
}
.er-table tbody tr:not(.er-iow-row):not(.er-subtotal-row):not(.er-grand-row):hover td { background:#f7f8f9; }

#estreport-empty { text-align:center; color:#aaa; padding:30px 0; font-size:13px; }

/* ── Resource breakdown tooltip (hover on activity name) ─────────── */
.er-act-name { cursor:default; border-bottom:1px dotted #999; }
#er-restip {
  display:none; position:fixed; z-index:200200;
  background:#fff; border-radius:6px; box-shadow:0 6px 24px rgba(0,0,0,.28);
  padding:0; min-width:480px; max-width:640px; font-size:11px;
}
#er-restip.er-tip-open { display:block; }
#er-restip table { width:100%; border-collapse:collapse; }
#er-restip th, #er-restip td {
  border:1px solid #d8c9a3;
}
#er-restip th {
  background:#8a5a1e; color:#fff; padding:6px 10px; text-align:left; font-size:10.5px;
  white-space:nowrap; border-color:#6e4717;
}
#er-restip th.er-num, #er-restip td.er-num { text-align:right; }
#er-restip td { padding:5px 10px; color:#3a2f1f; background:#fffaf0; }
#er-restip tr.er-tip-total td {
  background:#f3e3c6; font-weight:700; color:#8a5a1e;
}
#er-restip .er-tip-empty { padding:10px; text-align:center; color:#aaa; border:1px solid #d8c9a3; background:#fffaf0; }

/* ── Qty-Work Done / E.Amount / A.Amount / Rate columns — clicking opens
     the full Cost Dashboard popup (all 6 panels) for that activity ───── */
.er-num.er-cd-link { cursor:pointer; border-bottom:1px dotted #37475a; }
.er-num.er-cd-link:hover { background:#eef1f4 !important; }

/* ── Project Cost Summary popup — independent, own trigger, own data.
     Matches the Gantt window's Project Duration panel style exactly. ── */
#ecs-tip {
  display: none; position: fixed; z-index: 10020;
  width: 720px;
  background: #fff; border-radius: 6px; border: 1px solid #dde3ef;
  box-shadow: 0 8px 30px rgba(0,0,0,0.3);
  overflow: hidden; font-family: 'Barlow Condensed', sans-serif;
}
#ecs-tip.ecs-open { display: block; }
#ecs-tip-title {
  font-size: 12px; font-weight: 700; color: #e8efff; text-transform: uppercase;
  letter-spacing: 0.04em; text-align: center;
  padding: 8px 12px; background: #1a2540;
  border-bottom: 1px solid #0d1f3c;
  cursor: move; user-select: none;
}
#ecs-tip-body { padding: 16px 18px; }
#ecs-proj-name { font-size:14px; font-weight:700; color:#1a2540; text-align:left; margin-bottom:10px; }
#ecs-bar-status { font-size:12px; font-weight:700; margin-bottom:4px; min-height:15px; }
#ecs-bar-status.ecs-over  { color:#e53935; }
#ecs-bar-status.ecs-under { color:#27ae60; }
#ecs-bar { position:relative; display:flex; align-items:stretch; height:10px; border-radius:4px; overflow:hidden; margin:8px 0 15px; background:#eef1f6; }
.ecs-bar-labels { display:flex; justify-content:space-between; font-size:13px; font-weight:400; color:#000; }
.ecs-bar-labels b { color:#5a6472; font-weight:700; padding-left:8px; margin-left:6px; border-left:1px solid #c7cdd6; }
.ecs-bar-labels.ecs-row3 { display:grid; grid-template-columns: repeat(3, 1fr); gap:14px; }
.ecs-bar-labels.ecs-row3 span { display:flex; justify-content:space-between; align-items:baseline; min-width:0; }
.ecs-bar-labels.ecs-row3 b { flex-shrink:0; }
.ecs-bar-labels.ecs-row3 b.over  { color:#e53935; }
.ecs-bar-labels.ecs-row3 b.under { color:#27ae60; }
.ecs-divider { border-top:1px solid #b9c0cc; margin:11px 0; }
#ecs-tip-loading { padding:16px; text-align:center; color:#9aa5ba; font-size:11px; }
</style>

<!-- ══ ESTIMATE REPORT WINDOW ═══════════════════════════════════ -->
<div id="estreport-win">
  <div class="er-rs er-rs-n" data-dir="n"></div><div class="er-rs er-rs-s" data-dir="s"></div>
  <div class="er-rs er-rs-e" data-dir="e"></div><div class="er-rs er-rs-w" data-dir="w"></div>
  <div class="er-rs er-rs-ne" data-dir="ne"></div><div class="er-rs er-rs-nw" data-dir="nw"></div>
  <div class="er-rs er-rs-se" data-dir="se"></div><div class="er-rs er-rs-sw" data-dir="sw"></div>

  <div id="estreport-win-hdr">
    <div style="display:flex;align-items:baseline;gap:6px;flex-wrap:nowrap;min-width:0;">
      <span>&#x1F4CB; Project Estimate</span>
      <span id="estreport-win-proj-name" style="font-size:12px;font-weight:500;color:#c9c9c9;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></span>
    </div>
    <div id="estreport-win-hdr-btns">
      <a class="icon-chart" id="btn-estcost-summary" title="Project Cost Summary" href="#" style="width:24px;height:24px;font-size:11px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:#2878c0;color:#fff;cursor:pointer;text-decoration:none;box-shadow:0 2px 6px rgba(0,0,0,.2);">&#128176;</a>
      <button id="estreport-win-close" title="Close">&times;</button>
    </div>
  </div>

  <div id="estreport-action-bar">
    <button id="estreport-print-btn">&#x1F5A8; Print</button>
  </div>

  <div id="estreport-body">
    <div id="estreport-empty">Loading estimate…</div>
    <div id="estreport-sheet" style="display:none;"></div>
  </div>
</div>

<div id="er-restip"></div>

<!-- Project Cost Summary popup — independent, own trigger, own data.
     Same compact card style as the Gantt window's Project Duration panel. -->
<div id="ecs-tip">
  <div id="ecs-tip-title">Project Cost Summary</div>
  <div id="ecs-tip-body">
    <div id="ecs-proj-name"></div>
    <div id="ecs-tip-loading">Loading&hellip;</div>
    <div id="ecs-tip-content" style="display:none">
      <div id="ecs-bar-status"></div>
      <div id="ecs-bar"></div>
      <div class="ecs-bar-labels ecs-row3">
        <span>Estimated Cost on Completion: <b id="ecs-estimated">—</b></span>
        <span>Actual Cost on Completion: <b id="ecs-actual">—</b></span>
        <span>Difference: <b id="ecs-diff-completion">—</b></span>
      </div>
      <div class="ecs-divider"></div>
      <div class="ecs-bar-labels ecs-row3">
        <span>Estimated Cost of Work Done: <b id="ecs-est-wd">—</b></span>
        <span>Actual Cost of Work Done: <b id="ecs-cost-wd">—</b></span>
        <span>Difference: <b id="ecs-diff-wd">—</b></span>
      </div>
      <div class="ecs-divider"></div>
      <div class="ecs-bar-labels ecs-row3">
        <span>Planned Remaining Cost: <b id="ecs-planned-remaining">—</b></span>
        <span>Actual Remaining Cost: <b id="ecs-actual-remaining">—</b></span>
        <span>Difference: <b id="ecs-diff-remaining">—</b></span>
      </div>
      <div class="ecs-divider"></div>
      <div class="ecs-bar-labels">
        <span>% of Work Completed: <b id="ecs-pct-complete">—</b></span>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  if(typeof window.bindDragTouch !== 'function'){
    window.bindDragTouch = function(el, mouseType, handler, opts){
      if(el) el.addEventListener(mouseType, handler, opts);
    };
  }

  /* ═══════════════════════════════════════════════════════════════
     Drag+resize engine (same pattern as Activity Relationships)
  ═══════════════════════════════════════════════════════════════ */
  var _erDrag = null;
  var _erRaf = null;
  var _erResourcesByRow = [];
  var _erSchedIdByRow = [];

  function erAnchor(win){
    var r = win.getBoundingClientRect();
    win.style.left=r.left+'px'; win.style.top=r.top+'px';
    win.style.width=r.width+'px'; win.style.height=r.height+'px';
    return r;
  }

  function erBindDragResize(win, hdrSel, minW, minH){
    var hdr = win.querySelector(hdrSel);
    bindDragTouch(hdr, 'mousedown', function(e){
      if(e.target.closest('#estreport-win-hdr-btns')) return;
      var r = erAnchor(win);
      _erDrag = {win:win,action:'drag',sx:e.clientX,sy:e.clientY,lastX:e.clientX,lastY:e.clientY,ox:r.left,oy:r.top,ow:r.width,oh:r.height,mw:minW,mh:minH};
      win.classList.add('er-resizing');
      e.preventDefault();
    });
    win.querySelectorAll('.er-rs').forEach(function(el){
      bindDragTouch(el, 'mousedown', function(e){
        var r = erAnchor(win);
        _erDrag = {win:win,action:el.dataset.dir,sx:e.clientX,sy:e.clientY,lastX:e.clientX,lastY:e.clientY,ox:r.left,oy:r.top,ow:r.width,oh:r.height,mw:minW,mh:minH};
        win.classList.add('er-resizing');
        e.preventDefault(); e.stopPropagation();
      });
    });
  }

  function _erApplyDrag(){
    if(!_erDrag) return;
    var d=_erDrag, dx=d.lastX-d.sx, dy=d.lastY-d.sy;
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
  }

  bindDragTouch(document, 'mousemove', function(e){
    if(!_erDrag) return;
    e.preventDefault();
    _erDrag.lastX = e.clientX; _erDrag.lastY = e.clientY;
    if(_erRaf) return;
    _erRaf = requestAnimationFrame(function(){
      _erRaf = null;
      _erApplyDrag();
    });
  }, { passive: false });
  bindDragTouch(document, 'mouseup', function(){
    if(_erRaf){ cancelAnimationFrame(_erRaf); _erRaf=null; }
    _erApplyDrag(); /* commit the final pointer position, even if its frame hadn't painted yet */
    if(_erDrag && _erDrag.win) _erDrag.win.classList.remove('er-resizing');
    _erDrag=null;
  });

  var win = document.getElementById('estreport-win');
  erBindDragResize(win, '#estreport-win-hdr', 480, 320);

  function esc(s){ return $('<span>').text(s == null ? '' : s).html(); }
  function fmtNum(n){ return (+n || 0).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2}); }

  function _getPid(){
    var btn = document.getElementById('gantt-win-open');
    return btn ? (btn.getAttribute('data-projectid') || '') : '';
  }

  function renderReport(d){
    var $sheet = $('#estreport-sheet');
    var $empty = $('#estreport-empty');

    document.getElementById('estreport-win-proj-name').textContent = (d && d.project_name) ? d.project_name : '';

    if (!d || d.error !== 'No' || !d.sections || !d.sections.length) {
      $sheet.hide();
      $empty.text('No estimate data found for this project.').show();
      return;
    }

    var html = '<div class="er-sheet-hdr">'
      + '<h2>' + esc(d.project_name || 'Project') + '</h2>'
      + '<div class="er-sheet-sub">Project Estimate</div>'
      + '</div>';

    html += '<table class="er-table"><thead><tr>'
      + '<th class="er-sl">Sl No</th>'
      + '<th>Activity</th>'
      + '<th>Unit</th>'
      + '<th class="er-num">Quantity</th>'
      + '<th class="er-num">Rate</th>'
      + '<th class="er-num">Amount</th>'
      + '<th class="er-num er-wd">Qty-Work Done</th>'
      + '<th class="er-num er-wd">E.Amount</th>'
      + '<th class="er-num er-wd">A.Amount</th>'
      + '<th class="er-num er-wd">Rate</th>'
      + '</tr></thead><tbody>';

    _erResourcesByRow = [];
    _erSchedIdByRow = [];
    var sl = 1;
    d.sections.forEach(function(sec){
      html += '<tr class="er-iow-row"><td colspan="10">' + esc(sec.iow_name || 'IOW') + '</td></tr>';
      (sec.items || []).forEach(function(it){
        var rowIdx = _erResourcesByRow.length;
        _erResourcesByRow.push(it.resources || []);
        _erSchedIdByRow.push(it.sched_id || 0);
        html += '<tr>'
          + '<td class="er-sl">' + (sl++) + '</td>'
          + '<td class="er-act-name" data-rowidx="' + rowIdx + '">' + esc(it.activity_name) + '</td>'
          + '<td>' + esc(it.unit) + '</td>'
          + '<td class="er-num">' + fmtNum(it.quantity) + '</td>'
          + '<td class="er-num">' + fmtNum(it.rate) + '</td>'
          + '<td class="er-num">' + fmtNum(it.amount) + '</td>'
          + '<td class="er-num er-wd er-cd-link" data-rowidx="' + rowIdx + '">' + fmtNum(it.work_done_qty) + '</td>'
          + '<td class="er-num er-wd er-cd-link" data-rowidx="' + rowIdx + '">' + fmtNum(it.e_amount) + '</td>'
          + '<td class="er-num er-wd er-cd-link" data-rowidx="' + rowIdx + '">' + fmtNum(it.a_amount) + '</td>'
          + '<td class="er-num er-wd er-cd-link" data-rowidx="' + rowIdx + '">' + fmtNum(it.actual_rate) + '</td>'
          + '</tr>';
      });
      html += '<tr class="er-subtotal-row">'
        + '<td colspan="5" style="text-align:right;">Subtotal — ' + esc(sec.iow_name || 'IOW') + '</td>'
        + '<td class="er-num">' + fmtNum(sec.subtotal) + '</td>'
        + '<td colspan="4" class="er-wd"></td>'
        + '</tr>';
    });

    html += '<tr class="er-grand-row">'
      + '<td colspan="5" style="text-align:right;">Grand Total</td>'
      + '<td class="er-num">' + fmtNum(d.grand_total) + '</td>'
      + '<td colspan="4" class="er-wd"></td>'
      + '</tr>';

    html += '</tbody></table>';

    $sheet.html(html).show();
    $empty.hide();
  }

  /* ═══════════════════════════════════════════════════════════════
     Resource breakdown tooltip — hover on an activity's name shows
     the resources allocated to it (unit, rate, amount) and their
     total, which is the same estimated unit rate shown in the Rate
     column for that activity.
  ═══════════════════════════════════════════════════════════════ */
  var $erTip = $('#er-restip');

  function _renderResTip(resources){
    resources = resources || [];
    if (!resources.length) {
      return '<div class="er-tip-empty">No resources allocated</div>';
    }
    var total = 0;
    var html = '<table><thead><tr>'
      + '<th>Resource</th><th>Unit</th><th class="er-num">Rate</th><th class="er-num">Amount</th>'
      + '</tr></thead><tbody>';
    resources.forEach(function(r){
      total += (+r.amount || 0);
      html += '<tr>'
        + '<td>' + esc(r.name) + '</td>'
        + '<td>' + esc(r.unit) + '</td>'
        + '<td class="er-num">' + fmtNum(r.rate) + '</td>'
        + '<td class="er-num">' + fmtNum(r.amount) + '</td>'
        + '</tr>';
    });
    html += '<tr class="er-tip-total">'
      + '<td colspan="3" style="text-align:right;">Total (Unit Rate)</td>'
      + '<td class="er-num">' + fmtNum(total) + '</td>'
      + '</tr>';
    html += '</tbody></table>';
    return html;
  }

  $(document).on('mouseenter', '.er-act-name', function(){
    var rowIdx = +$(this).data('rowidx');
    var resources = _erResourcesByRow[rowIdx] || [];
    $erTip.html(_renderResTip(resources)).addClass('er-tip-open');
  });
  $(document).on('mousemove', '.er-act-name', function(e){
    if (!$erTip.hasClass('er-tip-open')) return;
    var tw = $erTip.outerWidth(), th = $erTip.outerHeight();
    var left = Math.min(e.clientX + 14, window.innerWidth  - tw - 8);
    var top  = Math.min(e.clientY + 14, window.innerHeight - th - 8);
    $erTip.css({ left: Math.max(8,left) + 'px', top: Math.max(8,top) + 'px' });
  });
  $(document).on('mouseleave', '.er-act-name', function(){
    $erTip.removeClass('er-tip-open');
  });

  /* ═══════════════════════════════════════════════════════════════
     Qty-Work Done / E.Amount / A.Amount / Rate columns — clicking any
     of these opens the exact same Cost Dashboard popup (all 6 panels:
     Unit Cost of Resources, Yield, Cost of Resources, Unit Cost of
     Activity, Work Done, Cost of Activity) used on the Gantt chart,
     for this row's scheduleactivities id.
  ═══════════════════════════════════════════════════════════════ */
  $(document).on('click', '.er-cd-link', function(){
    var rowIdx = +$(this).data('rowidx');
    var schedId = _erSchedIdByRow[rowIdx];
    if (!schedId) return;
    var anchorEl = this;
    function _open(){
      if (typeof window.showCostDashboardPopup === 'function') {
        window.showCostDashboardPopup(schedId, anchorEl);
      }
    }
    if (typeof window.showCostDashboardPopup === 'function') {
      _open();
    } else if (typeof window.ensureGanttLoaded === 'function') {
      // Gantt chart (and the Cost Dashboard functions it exposes) is only
      // ever loaded on demand — load it silently in the background first.
      window.ensureGanttLoaded(_open);
    }
  });

  /* ═══════════════════════════════════════════════════════════════
     Project Cost Summary popup — independent, own trigger, own data.
     Aggregates activities[].activity_cost (estimated) and
     activities[].actual_work_done (actual) from the same
     performancedashboard endpoint the Project Duration panel uses —
     no new backend needed.
  ═══════════════════════════════════════════════════════════════ */
  (function(){
    var tip = document.getElementById('ecs-tip');
    if (!tip) return;
    var _cache = null, _dragged = false;

    function fmtCr(n){
      n = +n || 0;
      if (n >= 10000000) return (n / 10000000).toFixed(2) + ' Cr';
      if (n >= 100000) return (n / 100000).toFixed(2) + ' L';
      return n.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    function render(data){
      var estimated       = data.estimated;
      var actualCostWd    = data.actualCostWd;
      var estimatedCostWd = data.estimatedCostWd;
      var diff = actualCostWd - estimatedCostWd;

      var plannedRemaining = Math.max(0, estimated - estimatedCostWd);
      var actualRemaining  = Math.max(0, estimated - actualCostWd);
      var diffCompletion = actualCostWd - estimated;
      var diffWd         = actualCostWd - estimatedCostWd;
      var diffRemaining  = actualRemaining - plannedRemaining;

      function setDiff(id, v){
        var el = document.getElementById(id);
        el.textContent = (v > 0 ? '+' : '') + fmtCr(v);
        el.className = v > 0 ? 'over' : v < 0 ? 'under' : '';
      }

      document.getElementById('ecs-estimated').textContent = fmtCr(estimated);
      document.getElementById('ecs-actual').textContent    = fmtCr(actualCostWd);
      document.getElementById('ecs-est-wd').textContent    = fmtCr(estimatedCostWd);
      document.getElementById('ecs-cost-wd').textContent   = fmtCr(actualCostWd);
      document.getElementById('ecs-planned-remaining').textContent = fmtCr(plannedRemaining);
      document.getElementById('ecs-actual-remaining').textContent  = fmtCr(actualRemaining);
      document.getElementById('ecs-pct-complete').textContent = (data.pctComplete != null ? data.pctComplete : 0) + '%';

      setDiff('ecs-diff-completion', diffCompletion);
      setDiff('ecs-diff-wd', diffWd);
      setDiff('ecs-diff-remaining', diffRemaining);

      (function(){
        var maxV = Math.max(estimated, actualCostWd, 1);
        var barHtml;
        if (diff > 0) {
          barHtml = '<div style="width:'+(estimatedCostWd/maxV*100).toFixed(1)+'%;background:#8a94a3;min-width:3px;"></div>'
                  + '<div style="width:'+(diff/maxV*100).toFixed(1)+'%;background:#e53935;min-width:3px;"></div>';
        } else {
          barHtml = '<div style="width:100%;background:#8a94a3;"></div>';
        }
        document.getElementById('ecs-bar').innerHTML = barHtml;
      })();

      var statusEl = document.getElementById('ecs-bar-status');
      statusEl.className = '';
      if (diff > 0) {
        statusEl.textContent = 'Over Budget by: ' + fmtCr(diff);
        statusEl.className = 'ecs-over';
      } else if (actualCostWd > 0 && diff < 0) {
        statusEl.textContent = 'Under Budget by: ' + fmtCr(-diff);
        statusEl.className = 'ecs-under';
      } else {
        statusEl.textContent = '';
      }

      document.getElementById('ecs-tip-loading').style.display = 'none';
      document.getElementById('ecs-tip-content').style.display = 'block';
    }

    function position(anchorEl){
      var r = anchorEl.getBoundingClientRect();
      var top = r.bottom + 6;
      var left = Math.min(r.left, window.innerWidth - 736);
      tip.style.top = Math.max(4, top) + 'px';
      tip.style.left = Math.max(4, left) + 'px';
    }

    function show(anchorEl){
      if (!_dragged) position(anchorEl);
      tip.style.zIndex = (typeof window.popupSubZBase === 'function') ? window.popupSubZBase() : 10020;
      tip.classList.add('ecs-open');
      if (_cache) { render(_cache); return; }
      document.getElementById('ecs-tip-loading').style.display = 'block';
      document.getElementById('ecs-tip-content').style.display = 'none';
      var pid = _getPid();
      $.ajax({
        type: 'POST', url: '../projectsmain/performancedashboard', dataType: 'json',
        success: function(d){
          if (!d || !d.activities) { document.getElementById('ecs-tip-loading').textContent = 'No data.'; return; }
          document.getElementById('ecs-proj-name').textContent = d.project_name || '';
          var estimated = 0, actualCostWd = 0, estimatedCostWd = 0;
          var total = d.activities.length, done = 0;
          d.activities.forEach(function(a){
            var est = (+a.activity_cost || 0);
            estimated    += est;
            actualCostWd += (+a.actual_work_done || 0);
            if (+a.completed_status !== 0) { estimatedCostWd += est; done++; }
          });
          _cache = {
            estimated: estimated,
            actualCostWd: actualCostWd,
            estimatedCostWd: estimatedCostWd,
            pctComplete: total > 0 ? Math.round((done / total) * 100) : 0
          };
          render(_cache);
        },
        error: function(){ document.getElementById('ecs-tip-loading').textContent = 'Failed to load.'; }
      });
    }
    function hide(){ tip.classList.remove('ecs-open'); }

    function bind(){
      var btn = document.getElementById('btn-estcost-summary');
      if (!btn || btn.dataset.ecsBound) return;
      btn.dataset.ecsBound = '1';
      btn.addEventListener('click', function(e){
        e.preventDefault(); e.stopPropagation();
        if (tip.classList.contains('ecs-open')) { hide(); return; }
        show(btn);
      });
      document.addEventListener('click', function(e){
        if (!tip.classList.contains('ecs-open')) return;
        if (e.target === btn || btn.contains(e.target)) return;
        if (tip.contains(e.target)) return;
        hide();
      });
    }
    bind();

    // Drag
    (function(){
      var titleEl = document.getElementById('ecs-tip-title');
      var _action=false, _sx=0, _sy=0, _ox=0, _oy=0;
      function _anchor(){ var r=tip.getBoundingClientRect(); tip.style.left=r.left+'px'; tip.style.top=r.top+'px'; return r; }
      titleEl.addEventListener('mousedown', function(e){
        var r=_anchor(); _action=true; _dragged=true;
        _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault();
      });
      document.addEventListener('mousemove', function(e){
        if(!_action) return;
        var dx=e.clientX-_sx, dy=e.clientY-_sy;
        tip.style.left=Math.max(0,_ox+dx)+'px'; tip.style.top=Math.max(0,_oy+dy)+'px';
      });
      document.addEventListener('mouseup', function(){ _action=false; });
    })();
  })();

  function loadReport(){
    var pid = _getPid();
    $('#estreport-sheet').hide();
    $('#estreport-empty').text('Loading estimate…').show();
    if (!pid) { $('#estreport-empty').text('No project selected.'); return; }
    $.ajax({ type:'POST', url:'../estimatereport/get', dataType:'json',
      data:{ projectid: pid },
      success: renderReport,
      error: function(){ $('#estreport-empty').text('Could not load estimate report.'); }
    });
  }

  document.getElementById('btn-estimate-report').addEventListener('click', function(){
    if(win.classList.contains('er-open')){ win.classList.remove('er-open'); return; }
    win.classList.add('er-open');
    if(typeof window.popupBringToFront === 'function') window.popupBringToFront('estreport-win');
    document.dispatchEvent(new Event('estreport:open'));
    loadReport();
  });
  document.getElementById('estreport-win-close').addEventListener('click', function(){
    win.classList.remove('er-open');
  });

  document.getElementById('estreport-print-btn').addEventListener('click', function(){
    var content = document.getElementById('estreport-sheet').outerHTML;
    var w = window.open('', '_blank', 'width=900,height=700');
    w.document.write(
      '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Project Estimate</title>'
      + '<style>* { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }'
      + 'body { margin:24px; padding:0; font-family:Arial,sans-serif; }'
      + '.er-sheet-hdr { text-align:left; margin-bottom:16px; }'
      + '.er-sheet-hdr h2 { margin:0 0 4px; font-size:17px; font-weight:700; color:#37475a; }'
      + '.er-sheet-hdr .er-sheet-sub { font-size:11px; color:#888; letter-spacing:.5px; text-transform:uppercase; }'
      + '.er-table { width:100%; border-collapse:collapse; font-size:12px; }'
      + '.er-table th { background:#6b7c93; color:#fff; padding:7px 9px; border:1px solid #55647a; text-align:left; }'
      + '.er-table th.er-num, .er-table td.er-num { text-align:right; }'
      + '.er-table th.er-sl, .er-table td.er-sl { text-align:center; width:44px; }'
      + '.er-table td { padding:6px 9px; border:1px solid #ddd; }'
      + '.er-table tr.er-iow-row td { background:#e4e7eb; font-weight:700; color:#37475a; }'
      + '.er-table tr.er-subtotal-row td { background:#f0f2f4; font-weight:700; color:#37475a; }'
      + '.er-table tr.er-grand-row td { background:#6b7c93; color:#fff; font-weight:700; font-size:13px; }'
      + '@media print { body { margin:0; } }</style></head><body>'
      + content
      + '<script>window.onload=function(){window.print();window.close();};<\/script></body></html>'
    );
    w.document.close();
  });

})();
</script>
