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
  top:50px; left:calc(50% - 490px);
  width:980px; max-width:96vw; height:calc(100vh - 90px);
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
.er-table th:nth-child(2), .er-table td:nth-child(2) { width:25%; }
.er-table th:nth-child(3), .er-table td:nth-child(3) { width:6%; }
.er-table th:nth-child(4), .er-table td:nth-child(4),
.er-table th:nth-child(5), .er-table td:nth-child(5) { width:9%; }
.er-table th:nth-child(6), .er-table td:nth-child(6) { width:11%; }
.er-table th:nth-child(7), .er-table td:nth-child(7),
.er-table th:nth-child(8), .er-table td:nth-child(8),
.er-table th:nth-child(9), .er-table td:nth-child(9) { width:11.66%; }
.er-table td {
  padding:6px 9px; border:1px solid #ddd; vertical-align:middle; color:#222;
  white-space:normal; word-break:break-word;
}
.er-table tr.er-iow-row td {
  background:#e4e7eb; font-weight:700; color:#37475a; padding:6px 9px;
  border:1px solid #cfd5db; font-size:12px;
}
.er-table tr.er-sub-row td {
  background:#eef3f2; font-weight:700; color:#0f5c52; padding:5px 9px;
  border:1px solid #d5dfdd; font-size:12px;
}
.er-table tr.er-subtotal-row.er-sub-subtotal td { background:#f6f9f8; font-weight:600; color:#0f5c52; }
.er-table tr.er-subtotal-row td {
  background:#f0f2f4; font-weight:700; color:#37475a; border:1px solid #dde2e7;
}
.er-table tr.er-grand-row td {
  background:#6b7c93; color:#fff; font-weight:700; font-size:13px; border:1px solid #55647a;
}
.er-table td.er-editable{cursor:pointer;}
.er-table td.er-editable:hover{background:#eef4fb;outline:1px dashed #7ba3d0;outline-offset:-2px;}
.er-table td.er-editable input{
  width:100%;box-sizing:border-box;border:1px solid #7ba3d0;border-radius:3px;
  padding:2px 4px;font-size:12px;font-family:inherit;
}
.er-table td.er-editable.er-num input{text-align:right;}
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

/* ── Clicking anywhere in an activity row opens the full Cost Dashboard
     popup (all 6 panels) for that activity — see .er-act-row handler ── */
.er-act-row { cursor:pointer; }

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

/* ── Clicking an IOW heading row opens this popup — that IOW's
     activities with a Resource Allocation button and an Edit (Unit +
     Quantity) button per row. Same drag/resize chrome as every other
     popup in this app. ── */
.er-iow-row { cursor:pointer; }
.er-iow-row:hover td { background:#d8dce1 !important; }
#eriow-bk{display:none;position:fixed;inset:0;background:transparent;z-index:200300;}
#eriow-bk.eriow-open{display:block}
#eriow-popup{
  display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
  width:1000px;height:480px;max-width:97vw;max-height:92vh;min-width:500px;min-height:300px;
  background:#fff;border-radius:8px;
  box-shadow:0 8px 32px rgba(0,0,0,.35);z-index:200301;flex-direction:column;overflow:hidden;
  font-family:'Nunito',sans-serif;
}
#eriow-popup.eriow-open{display:flex}
.eriow-rs{position:absolute;z-index:10;background:transparent;}
.eriow-rs-e {right:0;top:6px;bottom:6px;width:6px;cursor:e-resize;}
.eriow-rs-w {left:0;top:6px;bottom:6px;width:6px;cursor:w-resize;}
.eriow-rs-s {bottom:0;left:6px;right:6px;height:6px;cursor:s-resize;}
.eriow-rs-n {top:0;left:6px;right:6px;height:6px;cursor:n-resize;}
.eriow-rs-se{right:0;bottom:0;width:14px;height:14px;cursor:se-resize;}
.eriow-rs-sw{left:0;bottom:0;width:14px;height:14px;cursor:sw-resize;}
.eriow-rs-ne{right:0;top:0;width:14px;height:14px;cursor:ne-resize;}
.eriow-rs-nw{left:0;top:0;width:14px;height:14px;cursor:nw-resize;}
#eriow-hdr{
  background:#fff;color:#333;padding:8px 14px;display:flex;justify-content:space-between;
  align-items:center;font-size:15px;font-weight:700;border-bottom:2px solid #b0b4b9;
  flex-shrink:0;cursor:move;
}
#eriow-hdr-suffix{font-size:13px;font-weight:700;color:#00838f;margin-left:8px;}
#eriow-close{background:none;border:none;color:#333;font-size:20px;line-height:1;cursor:pointer;padding:0 4px;opacity:.6;}
#eriow-close:hover{opacity:1;}
#eriow-body{padding:14px 18px;overflow:auto;flex:1;}
#eriow-body table{table-layout:auto;width:100%;border-collapse:collapse;}
#eriow-body th, #eriow-body td{white-space:nowrap;padding:8px 10px;border:none;border-bottom:1px solid #888;font-size:13px;}
#eriow-body th{background:#555;color:#fff;font-weight:600;font-size:11px;line-height:1;padding:5px 8px;text-align:left;}
#eriow-body tbody tr:last-child td{border-bottom:none;}
#eriow-body td.eriow-col-sno{color:#999;text-align:center;width:36px;}
.eriow-resalloc-btn{
  background:#8a5a1e;color:#fff;border:none;border-radius:4px;padding:4px 10px;
  font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;
}
#eriow-empty{color:#aaa;font-size:13px;text-align:center;padding:30px 0;}
#eriow-footer{
  background:#fff;border-top:1px solid #ddd;padding:10px 16px;display:flex;justify-content:flex-end;
  flex-shrink:0;
}
#eriow-btn-close{
  background:#e67e22;color:#fff;border:none;border-radius:999px;padding:8px 24px;font-size:13px;
  font-weight:700;cursor:pointer;letter-spacing:.3px;text-transform:uppercase;
}

/* ── Resource Allocation popup — opened from the eriow-popup's own
     "Resource Allocation" button. Standalone, writes directly to
     estactivity_resources (the same table the Activity Library screen
     uses) via EstresourceallocController, generic-case only. Left
     sidebar of Resource Type tabs; each tab's list is a single search-
     filtered view combining not-yet-allocated resources (tick to add)
     and already-allocated ones (inline edit/delete) — no second popup. ── */
#erra-bk{display:none;position:fixed;inset:0;background:transparent;z-index:200400;}
#erra-bk.erra-open{display:block}
#erra-popup{
  display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
  width:1220px;height:720px;max-width:97vw;max-height:92vh;min-width:600px;min-height:360px;
  background:#fff;border-radius:8px;
  box-shadow:0 8px 32px rgba(0,0,0,.35);z-index:200401;flex-direction:column;overflow:hidden;
  font-family:'Nunito',sans-serif;
}
#erra-popup.erra-open{display:flex}
.erra-rs{position:absolute;z-index:10;background:transparent;}
.erra-rs-e {right:0;top:6px;bottom:6px;width:6px;cursor:e-resize;}
.erra-rs-w {left:0;top:6px;bottom:6px;width:6px;cursor:w-resize;}
.erra-rs-s {bottom:0;left:6px;right:6px;height:6px;cursor:s-resize;}
.erra-rs-n {top:0;left:6px;right:6px;height:6px;cursor:n-resize;}
.erra-rs-se{right:0;bottom:0;width:14px;height:14px;cursor:se-resize;}
.erra-rs-sw{left:0;bottom:0;width:14px;height:14px;cursor:sw-resize;}
.erra-rs-ne{right:0;top:0;width:14px;height:14px;cursor:ne-resize;}
.erra-rs-nw{left:0;top:0;width:14px;height:14px;cursor:nw-resize;}
#erra-hdr{
  background:#4a4f57;color:#fff;padding:8px 14px;display:flex;justify-content:space-between;
  align-items:center;font-size:15px;font-weight:700;flex-shrink:0;cursor:move;
  border-bottom:2px solid #b0b4b9;
}
#erra-close{background:none;border:none;color:#fff;font-size:20px;line-height:1;cursor:pointer;padding:0 4px;opacity:.75;flex-shrink:0;}
#erra-close:hover{opacity:1;}
#erra-activity-hdr{
  background:#f4f6f8;border-bottom:1px solid #e3e5e8;padding:20px 16px;
  display:flex;align-items:baseline;gap:18px;flex-shrink:0;flex-wrap:wrap;
}
#erra-activity-hdr .erra-hdr-label{font-size:11px;font-weight:600;color:#888;text-transform:uppercase;margin-right:4px;}
#erra-activity-hdr #erra-hdr-name{font-size:14px;font-weight:700;color:#2d333b;}
/* Everything below the header (Resource Type buttons, the resources-
   from-selected-item-wrpr accordion, the added-resources list) uses the
   app's own real classes (allocation-left-bar, rounded-coner-btn,
   resmasterss, panel/panel-collapse, etc.) — already styled globally by
   cssnew/style.css + developer*.css + bootstrap.css (views/layouts/
   main.php), so it renders identically to the real Activity Library
   screen without any re-skinning here. Only structural/scroll rules for
   fitting this content into a floating popup instead of an in-page tab
   are added. */
.allocated-list-cntnt-wrpr{flex-shrink:0;max-height:170px;overflow-y:auto;}
.resources-from-selected-item-wrpr{max-height:none;}
#erra-tabs{width:100%;overflow-y:auto;}
#erra-accordion{overflow-y:visible;}
#erra-footer{
  background:#fff;border-top:1px solid #ddd;padding:10px 16px;display:flex;justify-content:flex-end;
  flex-shrink:0;
}
#erra-btn-close{
  background:#e67e22;color:#fff;border:none;border-radius:999px;padding:8px 24px;font-size:13px;
  font-weight:700;cursor:pointer;letter-spacing:.3px;text-transform:uppercase;
}

/* ── Map to Task sub-popup — nested above #erra-popup ── */
#ertask-bk{display:none;position:fixed;inset:0;background:rgba(0,0,0,.25);z-index:200500;}
#ertask-bk.ertask-open{display:block}
#ertask-popup{
  display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
  width:420px;max-width:92vw;max-height:80vh;background:#fff;border-radius:8px;
  box-shadow:0 8px 32px rgba(0,0,0,.4);z-index:200501;flex-direction:column;overflow:hidden;
  font-family:'Nunito',sans-serif;
}
#ertask-popup.ertask-open{display:flex}
#ertask-hdr{
  background:#fff;color:#333;padding:10px 14px;display:flex;justify-content:space-between;
  align-items:center;font-size:15px;font-weight:700;flex-shrink:0;border-bottom:2px solid #b0b4b9;
}
#ertask-hdr-resname{font-size:12px;font-weight:500;color:#888;margin-left:8px;}
#ertask-close{background:none;border:none;color:#333;font-size:20px;line-height:1;cursor:pointer;padding:0 4px;opacity:.6;}
#ertask-close:hover{opacity:1;}
#ertask-body{padding:14px 16px;overflow-y:auto;flex:1;min-height:80px;}
.ertask-row{display:flex;align-items:center;gap:10px;padding:7px 4px;border-bottom:1px solid #f0f0f0;font-size:13px;}
.ertask-row:last-child{border-bottom:none;}
.ertask-row input[type="checkbox"]{width:16px;height:16px;flex-shrink:0;}
.ertask-row .ertask-name{flex:1;color:#2d333b;}
.ertask-row .ertask-unit{color:#999;font-size:11px;}
#ertask-footer{
  background:#fff;border-top:1px solid #ddd;padding:10px 16px;display:flex;justify-content:flex-end;
  gap:8px;flex-shrink:0;
}
#ertask-btn-cancel{
  background:#eee;color:#555;border:none;border-radius:999px;padding:8px 20px;font-size:13px;
  font-weight:700;cursor:pointer;letter-spacing:.3px;text-transform:uppercase;
}
#ertask-btn-save{
  background:#e67e22;color:#fff;border:none;border-radius:999px;padding:8px 24px;font-size:13px;
  font-weight:700;cursor:pointer;letter-spacing:.3px;text-transform:uppercase;
}
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

<!-- IOW activities popup — opened by clicking an IOW heading row in the
     Project Estimate sheet. Shows that IOW's activities with a Resource
     Allocation button and an Edit (Unit + Quantity) button per row. -->
<div id="eriow-bk"></div>
<div id="eriow-popup">
  <div class="eriow-rs eriow-rs-e"  data-dir="e"></div>
  <div class="eriow-rs eriow-rs-w"  data-dir="w"></div>
  <div class="eriow-rs eriow-rs-s"  data-dir="s"></div>
  <div class="eriow-rs eriow-rs-n"  data-dir="n"></div>
  <div class="eriow-rs eriow-rs-se" data-dir="se"></div>
  <div class="eriow-rs eriow-rs-sw" data-dir="sw"></div>
  <div class="eriow-rs eriow-rs-ne" data-dir="ne"></div>
  <div class="eriow-rs eriow-rs-nw" data-dir="nw"></div>
  <div id="eriow-hdr">
    <span>Activities<span id="eriow-hdr-suffix"></span></span>
    <button id="eriow-close">&times;</button>
  </div>
  <div id="eriow-body">
    <div id="eriow-list-target"><p id="eriow-empty">Loading…</p></div>
  </div>
  <div id="eriow-footer">
    <button id="eriow-btn-close">Close</button>
  </div>
</div>

<!-- Resource Allocation popup — opened from an activity row's "Resource
     Allocation" button above. Standalone screen, own data (estactivity_
     resources), own Add/Edit/Delete. -->
<div id="erra-bk"></div>
<div id="erra-popup">
  <div class="erra-rs erra-rs-e"  data-dir="e"></div>
  <div class="erra-rs erra-rs-w"  data-dir="w"></div>
  <div class="erra-rs erra-rs-s"  data-dir="s"></div>
  <div class="erra-rs erra-rs-n"  data-dir="n"></div>
  <div class="erra-rs erra-rs-se" data-dir="se"></div>
  <div class="erra-rs erra-rs-sw" data-dir="sw"></div>
  <div class="erra-rs erra-rs-ne" data-dir="ne"></div>
  <div class="erra-rs erra-rs-nw" data-dir="nw"></div>
  <div id="erra-hdr">
    <span>Resource Allocation</span>
    <button id="erra-close">&times;</button>
  </div>

  <!-- Ported verbatim from the real Activity Library "Allocate Resources"
       screen (views/projects/_estactivity.php + web/jsnew/masters/
       estactivity.js): identical wrapper classes (allocated-list-cntnt-wrpr,
       allocation-left-bar, rounded-coner-btn, resources-from-selected-item-wrpr,
       accordionmast panel/panel-collapse) so the HTML fragments the real
       ProjectsController actions (allocationlist/allocationsearch/
       addactivityresource/updateallocaterrated/deleteactivityresource)
       already return render pixel-for-pixel like the real screen — this
       page already loads bootstrap.css + style.css/developer*.css globally
       (views/layouts/main.php), which is what actually styles all of this,
       not custom CSS in this file. -->
  <input type="hidden" id="erra-activity-id-field">

  <!-- Activity title bar — moved down from the popup's own drag-bar so it
       reads as the heading for the allocated-resources list right below
       it, rather than as window chrome. -->
  <div id="erra-activity-hdr">
    <span><span class="erra-hdr-label">Activity</span><span id="erra-hdr-name">—</span></span>
    <span><span class="erra-hdr-label">Unit</span><span id="erra-hdr-unit">—</span></span>
    <span><span class="erra-hdr-label">Rate</span><span id="erra-hdr-rate">—</span></span>
  </div>

  <div class="allocated-list-cntnt-wrpr added-alloc-items-wrpr" style="padding:0 16px;">
    <div class="estimationaddedallocation allocation-list-items-cntnr" id="erra-added-list"></div>
  </div>

  <div class="row" style="margin:10px 16px 0;flex:1;min-height:0;display:flex;align-items:stretch;">
    <div class="col-md-2" style="padding-right:0;display:flex;min-height:0;width:260px;max-width:260px;flex:0 0 260px;">
      <div class="allocation-left-bar items-select-bar" id="erra-tabs" style="max-height:none;height:auto;overflow-y:auto;width:100%;max-width:none;"></div>
    </div>
    <div class="col-md-10" style="min-height:0;overflow-y:auto;width:auto;flex:1 1 auto;">
      <div class="resources-from-selected-item-wrpr" style="display:block;margin-right:0;">
        <div class="search-and-actions-wrpr row">
          <div class="content-search-wrpr col-md-12 col-sm-12">
            <input type="search" class="form-control searchdefault resources" id="erra-search" placeholder="Search">
            <button id="erra-search-btn" class="btn btn-primary searchresoureskey" type="button"><span class="icon-search5"></span></button>
          </div>
        </div>
        <div class="panel-group allocation-items" id="erra-accordion">
          <p style="color:#aaa;padding:16px 4px;font-size:13px;">Select a Resource Type on the left.</p>
        </div>
      </div>
    </div>
  </div>

  <div id="erra-footer">
    <button id="erra-btn-close">Close</button>
  </div>
</div>

<!-- Map to Task sub-popup — opened from a resource row's Task button
     inside #erra-popup. Lets a resource allocated to an activity be tied
     to one or more of that activity's tasks (activity_tasks), writing
     pricing_estimate_resources_new.task_ids — the field Procurement's
     PO/WO screens already require to be non-empty before they'll let a
     resource go to a Purchase/Work Order. Sub-popup, not a third floating
     window, so it re-raises with #erra-popup automatically. -->
<div id="ertask-bk"></div>
<div id="ertask-popup">
  <div id="ertask-hdr">
    <span>Map Resource to Task<span id="ertask-hdr-resname"></span></span>
    <button id="ertask-close">&times;</button>
  </div>
  <div id="ertask-body">
    <p id="ertask-loading" style="color:#aaa;font-size:13px;padding:10px 2px;">Loading…</p>
    <p id="ertask-empty" style="display:none;color:#aaa;font-size:13px;padding:10px 2px;">No tasks defined for this activity yet — add tasks first.</p>
    <div id="ertask-list" style="display:none;"></div>
  </div>
  <div id="ertask-footer">
    <button id="ertask-btn-cancel">Cancel</button>
    <button id="ertask-btn-save">Save</button>
  </div>
</div>

<script>
(function(){
  if(typeof window.bindDragTouch !== 'function'){
    // Despite the name, this previously only ever bound the mouse event —
    // no touchstart/touchmove/touchend equivalent was ever registered, so
    // drag/resize never worked on touch-only devices (iPad etc., which
    // fire touch events instead of mouse events). Now binds both: the
    // touch handler synthesizes clientX/clientY from the first touch
    // point (or the last known one on touchend, which carries no
    // touches list) so the same handler function — written for
    // e.clientX/e.clientY — works unmodified for either input type.
    var _touchTypeFor = { mousedown: 'touchstart', mousemove: 'touchmove', mouseup: 'touchend' };
    window.bindDragTouch = function(el, mouseType, handler, opts){
      if(!el) return;
      el.addEventListener(mouseType, handler, opts);
      var touchType = _touchTypeFor[mouseType];
      if(!touchType) return;
      el.addEventListener(touchType, function(e){
        var t = (e.touches && e.touches[0]) || (e.changedTouches && e.changedTouches[0]);
        if(t){ e.clientX = t.clientX; e.clientY = t.clientY; }
        handler(e);
      }, opts);
    };
  }

  /* ═══════════════════════════════════════════════════════════════
     Drag+resize engine (same pattern as Activity Relationships)
  ═══════════════════════════════════════════════════════════════ */
  var _erDrag = null;
  var _erRaf = null;
  var _erResourcesByRow = [];
  var _erSchedIdByRow = [];
  var _erWanIdByRow = [];
  var _erSectionsData = []; // last-loaded d.sections, cached so the IOW popup doesn't need its own fetch

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
      + '<th class="er-sl">#</th>'
      + '<th>Activity</th>'
      + '<th>Unit</th>'
      + '<th class="er-num">Quantity</th>'
      + '<th class="er-num">Rate</th>'
      + '<th class="er-num">Cost</th>'
      + '<th class="er-num">Actual Qty</th>'
      + '<th class="er-num">Actual Rate</th>'
      + '<th class="er-num">Actual Cost</th>'
      + '</tr></thead><tbody>';

    _erResourcesByRow = [];
    _erSchedIdByRow = [];
    _erWanIdByRow = [];
    _erSectionsData = d.sections;
    var sl = 1;
    d.sections.forEach(function(sec, secIdx){
      html += '<tr class="er-iow-row" data-secidx="' + secIdx + '"><td colspan="8">' + esc(sec.iow_name || 'IOW') + '</td></tr>';
      var _subName = {}, _subTot = {};
      (sec.subgroups || []).forEach(function(sg){ _subName[sg.id] = sg.name; _subTot[sg.id] = sg; });
      var _curSub = 0, _shownSubs = {};
      var _subTotalRow = function(sid){
        var sg = _subTot[sid];
        return '<tr class="er-subtotal-row er-sub-subtotal">'
          + '<td colspan="5" style="text-align:right;">Subtotal — ' + esc(sg.name) + '</td>'
          + '<td class="er-num">' + fmtNum(sg.subtotal) + '</td>'
          + '<td></td><td></td>'
          + '<td class="er-num">' + fmtNum(sg.subtotal_actual) + '</td>'
          + '</tr>';
      };
      (sec.items || []).forEach(function(it){
        // Sub Group heading (as in the Gantt) before its first activity.
        var _sid = it.subgroup_id || 0;
        if (_sid !== _curSub) {
          if (_curSub) html += _subTotalRow(_curSub);
          _curSub = _sid;
          if (_sid) { _shownSubs[_sid] = 1; html += '<tr class="er-sub-row"><td></td><td colspan="8">' + esc(_subName[_sid] || 'Sub Group') + '</td></tr>'; }
        }
        var rowIdx = _erResourcesByRow.length;
        _erResourcesByRow.push(it.resources || []);
        _erSchedIdByRow.push(it.sched_id || 0);
        _erWanIdByRow.push(it.wan_id || 0);
        html += '<tr class="er-act-row" data-wanid="' + it.wan_id + '" data-schedid="' + (it.sched_id || 0) + '" data-rowidx="' + rowIdx + '">'
          + '<td class="er-sl">' + (sl++) + '</td>'
          + '<td class="er-act-name" data-rowidx="' + rowIdx + '">' + esc(it.activity_name) + '</td>'
          + '<td class="er-editable er-unit-cell" title="Click to edit">' + esc(it.unit) + '</td>'
          + '<td class="er-num er-editable er-qty-cell" title="Click to edit">' + fmtNum(it.quantity) + '</td>'
          + '<td class="er-num">' + fmtNum(it.rate) + '</td>'
          + '<td class="er-num">' + fmtNum(it.amount) + '</td>'
          + '<td class="er-num">' + fmtNum(it.work_done_qty) + '</td>'
          + '<td class="er-num">' + fmtNum(it.actual_rate) + '</td>'
          + '<td class="er-num">' + fmtNum(it.a_amount) + '</td>'
          + '</tr>';
      });
      if (_curSub) html += _subTotalRow(_curSub);
      // Sub Groups with no activities yet still show as headings.
      (sec.subgroups || []).forEach(function(sg){
        if (!_shownSubs[sg.id]) html += '<tr class="er-sub-row"><td></td><td colspan="8">' + esc(sg.name) + '</td></tr>';
      });
      html += '<tr class="er-subtotal-row">'
        + '<td colspan="5" style="text-align:right;">Subtotal — ' + esc(sec.iow_name || 'IOW') + '</td>'
        + '<td class="er-num">' + fmtNum(sec.subtotal) + '</td>'
        + '<td></td><td></td>'
        + '<td class="er-num">' + fmtNum(sec.subtotal_actual) + '</td>'
        + '</tr>';
    });

    html += '<tr class="er-grand-row">'
      + '<td colspan="5" style="text-align:right;">Grand Total</td>'
      + '<td class="er-num">' + fmtNum(d.grand_total) + '</td>'
      + '<td></td><td></td>'
      + '<td class="er-num">' + fmtNum(d.grand_total_actual) + '</td>'
      + '</tr>';

    html += '</tbody></table>';

    $sheet.html(html).show();
    $empty.hide();
  }

  /* ═══════════════════════════════════════════════════════════════
     Inline Unit/Quantity editing directly on the sheet — click the
     cell, edit, blur/Enter to save. Same ../estimatereport/updateunitqty
     endpoint the IOW-popup's pencil-edit already uses, just triggered
     straight from the table instead of through that separate popup.
  ═══════════════════════════════════════════════════════════════ */
  $(document).on('click', '.er-editable', function(){
    var $td = $(this);
    if ($td.find('input').length) return; // already editing
    var row = $td.closest('tr')[0];
    var wanId = row.getAttribute('data-wanid');
    if (!wanId) return;
    var value = $td.text().trim();
    var isQty = $td.hasClass('er-qty-cell');
    $td.html('<input type="' + (isQty ? 'number' : 'text') + '" ' + (isQty ? 'step="0.01" min="0"' : '') + ' value="' + esc(value) + '">');
    var $input = $td.find('input').focus().select();

    function commit(){
      var newUnit = isQty ? row.querySelector('.er-unit-cell').textContent.trim() : $input.val().trim();
      var newQty  = isQty ? (parseFloat($input.val()) || 0) : (parseFloat(row.querySelector('.er-qty-cell').textContent) || 0);
      if (!newQty){ alert('Please enter a Qty before saving.'); loadReport(); return; }
      var schedId = row.getAttribute('data-schedid');
      $.ajax({
        type: 'POST', url: '../estimatereport/updateunitqty', dataType: 'json',
        data: { wan_id: wanId, sched_id: schedId, unit: newUnit, quantity: newQty },
        success: function(d){
          if (d.error && d.error !== 'No'){ alert(d.errortext || d.error); }
          if (typeof window.reloadGantt === 'function') window.reloadGantt();
          loadReport(); // refresh so Rate/Amount/subtotal reflect the new value
        },
        error: function(){ alert('Failed to save. Please try again.'); loadReport(); }
      });
    }

    $input.on('blur', commit);
    $input.on('keydown', function(e){
      if (e.which === 13){ e.preventDefault(); $input.blur(); }
      if (e.which === 27){ e.preventDefault(); loadReport(); }
    });
  });

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
     Clicking anywhere in an activity row opens the exact same Cost
     Dashboard popup (all 6 panels: Unit Cost of Resources, Yield, Cost
     of Resources, Unit Cost of Activity, Work Done, Cost of Activity)
     used on the Gantt chart, for this row's scheduleactivities id.
     Clicks on the Unit/Quantity inline-edit cells are excluded so
     editing still works without also popping the dashboard.
  ═══════════════════════════════════════════════════════════════ */
  $(document).on('click', '.er-act-row', function(e){
    if ($(e.target).closest('.er-editable').length) return;
    var rowIdx = +$(this).data('rowidx');
    var schedId = _erSchedIdByRow[rowIdx];
    // Estimate-only activities (no Gantt bar — e.g. auto-seeded "Project
    // Overhead"/"Project General Expenses") have no sched_id; fall back to
    // wan_id so the Cost Dashboard still opens for them instead of
    // silently doing nothing (see actionCostdashboardactivity's wan_id path).
    var wanId = _erWanIdByRow[rowIdx];
    var useWanId = !schedId;
    var popupActId = useWanId ? wanId : schedId;
    if (!popupActId) return;
    var anchorEl = this;
    function _open(){
      if (typeof window.showCostDashboardPopup === 'function') {
        window.showCostDashboardPopup(popupActId, anchorEl, useWanId);
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

  /* ═══════════════════════════════════════════════════════════════
     IOW ACTIVITIES POPUP — clicking an IOW heading row in the sheet.
     Reuses the already-loaded _erSectionsData (from the last report
     fetch) rather than a separate server call — the sheet and this
     popup always show the exact same underlying data.
  ═══════════════════════════════════════════════════════════════ */
  $(document).on('click', '.er-iow-row', function(){
    var secIdx = parseInt($(this).data('secidx'));
    var sec = _erSectionsData[secIdx];
    if (!sec) return;
    openEriowPopup(sec);
  });

  function openEriowPopup(sec){
    document.getElementById('eriow-hdr-suffix').textContent = sec.iow_name ? (' — ' + sec.iow_name) : '';
    document.getElementById('eriow-bk').classList.add('eriow-open');
    document.getElementById('eriow-popup').classList.add('eriow-open');
    renderEriowList(sec);
  }
  // Also opened from the Cashflow Project Cost popup (group heading click).
  window.openEriowPopup = openEriowPopup;

  function eriowViewCells(it){
    return '<td>' + esc(it.unit) + '</td>';
  }

  function renderEriowList(sec){
    var target = document.getElementById('eriow-list-target');
    var items = sec.items || [];
    if (!items.length){
      target.innerHTML = '<p id="eriow-empty">No activities added under this IOW yet.</p>';
      return;
    }
    var html = '<table><thead><tr>'
      + '<th style="width:36px;">#</th><th>Activity</th><th style="width:80px;">Unit</th>'
      + '<th style="width:120px;"></th>'
      + '</tr></thead><tbody>';
    items.forEach(function(it, idx){
      html += '<tr data-wanid="' + it.wan_id + '" data-schedid="' + it.sched_id + '" data-libactid="' + it.lib_activity_id + '">'
        + '<td class="eriow-col-sno">' + (idx + 1) + '</td>'
        + '<td>' + esc(it.activity_name) + '</td>'
        + eriowViewCells(it)
        + '<td><button type="button" class="eriow-resalloc-btn" title="Resource Allocation">Resource Allocation</button></td>'
        + '</tr>';
    });
    html += '</tbody></table>';
    target.innerHTML = html;
  }

  document.getElementById('eriow-list-target').addEventListener('click', function(e){
    var resBtn = e.target.closest('.eriow-resalloc-btn');
    if (resBtn){
      var row = resBtn.closest('tr');
      openResourceAllocation(row.getAttribute('data-libactid'), row.getAttribute('data-wanid'));
      return;
    }
  });

  function closeEriowPopup(){
    document.getElementById('eriow-bk').classList.remove('eriow-open');
    document.getElementById('eriow-popup').classList.remove('eriow-open');
  }
  document.getElementById('eriow-close').addEventListener('click', closeEriowPopup);
  document.getElementById('eriow-btn-close').addEventListener('click', closeEriowPopup);
  document.getElementById('eriow-bk').addEventListener('click', closeEriowPopup);

  // Drag + resize — identical pattern to every other popup in this app.
  (function(){
    var win = document.getElementById('eriow-popup');
    var hdr = document.getElementById('eriow-hdr');
    var MIN_W=500, MIN_H=300, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0;
    function _anchor(){
      var r=win.getBoundingClientRect();
      win.style.left=r.left+'px'; win.style.top=r.top+'px';
      win.style.width=r.width+'px'; win.style.height=r.height+'px';
      win.style.transform='none';
      return r;
    }
    hdr.addEventListener('mousedown', function(e){
      if(e.target.id==='eriow-close') return;
      var r=_anchor(); _action='drag'; _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top;
      e.preventDefault();
    });
    document.querySelectorAll('.eriow-rs').forEach(function(el){
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
        if(_action.indexOf('e')>-1){ w=Math.max(MIN_W,_ow+dx); }
        if(_action.indexOf('s')>-1){ h=Math.max(MIN_H,_oh+dy); }
        if(_action.indexOf('w')>-1){ var nw=Math.max(MIN_W,_ow-dx); l=_ox+(_ow-nw); w=nw; }
        if(_action.indexOf('n')>-1){ var nh=Math.max(MIN_H,_oh-dy); t=_oy+(_oh-nh); h=nh; }
        win.style.left=l+'px'; win.style.top=t+'px'; win.style.width=w+'px'; win.style.height=h+'px';
      }
    });
    document.addEventListener('mouseup', function(){ _action=null; });
  })();

  /* ═══════════════════════════════════════════════════════════════
     RESOURCE ALLOCATION popup — ported verbatim from the real, live
     Activity Library screen (views/projects/_estactivity.php's
     "Allocate Resources" tab + web/jsnew/masters/estactivity.js): same
     estactivity_resources table, same ProjectsController actions
     (allocationlist / allocationsearch / addactivityresource /
     updateallocaterrated / deleteactivityresource), and the server's
     raw HTML responses (d.result, d.listadded) are injected AS-IS using
     the app's real classes (resourcesearch, rounded-coner-btn,
     resmasterss, panel/panel-collapse, resource-add-btn, etc.) instead
     of being re-parsed into custom markup — this page already loads
     bootstrap.css + style.css/developer*.css globally (views/layouts/
     main.php), so it renders pixel-identical to the real screen. Click
     handling is delegated locally (not estactivity.js's own delegated
     handlers) so this popup doesn't interfere with the real Activity
     Library page if both happen to be open at once.
  ═══════════════════════════════════════════════════════════════ */
  // Drag the Resource Allocation popup by its header (mouse and touch).
  // Clears the centring transform on the first drag so the window moves freely.
  (function(){
    var win = document.getElementById('erra-popup');
    var drag = null;
    bindDragTouch(document.getElementById('erra-hdr'), 'mousedown', function(e){
      if(e.target.closest('#erra-close')) return;
      var r = win.getBoundingClientRect();
      win.style.transform = 'none';
      win.style.left = r.left + 'px'; win.style.top = r.top + 'px';
      drag = { sx: e.clientX, sy: e.clientY, ox: r.left, oy: r.top };
      e.preventDefault();
    });
    bindDragTouch(document, 'mousemove', function(e){
      if(!drag) return;
      e.preventDefault();
      win.style.left = Math.max(0, drag.ox + e.clientX - drag.sx) + 'px';
      win.style.top  = Math.max(0, drag.oy + e.clientY - drag.sy) + 'px';
    }, { passive: false });
    bindDragTouch(document, 'mouseup', function(){ drag = null; });
  })();

  var _erraActivityId = 0;
  var _erraActiveTypeId = 0;
  var _erraWanId = 0; // project-scoped workgroup_activities_new.id — needed so
                       // a saved allocation can also reach pricing_estimate_resources_new
                       // (Procurement's Purchase Order tab reads that table, not
                       // estactivity_resources — see addactivityresource below)

  function openResourceAllocation(libActId, wanId){
    libActId = parseInt(libActId) || 0;
    if (!libActId){ alert('This activity has no Activity Library link — cannot open Resource Allocation.'); return; }
    _erraActivityId = libActId;
    _erraWanId = parseInt(wanId) || 0;
    _erraActiveTypeId = 0;
    document.getElementById('erra-activity-id-field').value = libActId;
    document.getElementById('erra-bk').classList.add('erra-open');
    document.getElementById('erra-popup').classList.add('erra-open');
    document.getElementById('erra-search').value = '';
    document.getElementById('erra-tabs').innerHTML = '';
    document.getElementById('erra-accordion').innerHTML = '<p style="color:#aaa;padding:16px 4px;font-size:13px;">Select a Resource Type on the left.</p>';
    loadErraAllocationList();
  }

  /* Mirrors #resourceallocation's click handler in estactivity.js:
     one call gives the Resource Type buttons (data.result), the
     allocated-resources list (data.listadded) and the header
     Activity/Unit/Rate labels, all in one round trip. */
  function loadErraAllocationList(){
    $.ajax({
      // Opened from a project activity: the project's own allocation (never the
      // Activity Library's) is shown and edited here — see ProjectsController::actionProjallocationlist.
      type: 'POST', url: _erraWanId ? '../projects/projallocationlist' : '../projects/allocationlist', dataType: 'json',
      data: { id: _erraActivityId, wan_id: _erraWanId },
      success: function(d){
        if (d.error && d.error !== 'No') return;
        document.getElementById('erra-tabs').innerHTML = d.result || '';
        if (_erraActiveTypeId){
          document.querySelectorAll('#erra-tabs .resourcesearch').forEach(function(b){
            b.classList.toggle('active', String(b.getAttribute('data-id')) === String(_erraActiveTypeId));
          });
        }
        document.getElementById('erra-added-list').innerHTML = d.listadded || '';
        document.getElementById('erra-hdr-name').textContent = (d.activitynameval || '—').replace(/^Activity\s*:\s*/, '');
        document.getElementById('erra-hdr-unit').textContent = (d.actvtyunit || '—').replace(/^Unit\s*:\s*/, '');
        document.getElementById('erra-hdr-rate').textContent = (d.activitynamerate || '—').replace(/^Rate\s*:\s*&nbsp;?/, '').trim();
      }
    });
  }

  /* Resource Type buttons are the real .resourcesearch <a> elements
     (server-rendered, class="btn btn-primary rounded-coner-btn
     resourcesearch") — handled locally instead of estactivity.js's own
     delegated $(document).on('click', '.resourcesearch', ...). */
  document.getElementById('erra-tabs').addEventListener('click', function(e){
    var btn = e.target.closest('.resourcesearch');
    if (!btn) return;
    e.preventDefault();
    _erraActiveTypeId = btn.getAttribute('data-id');
    document.querySelectorAll('#erra-tabs .resourcesearch').forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
    loadErraAllocationSearch();
  });

  var _erraSearchTimer = null;
  document.getElementById('erra-search').addEventListener('keyup', function(){
    clearTimeout(_erraSearchTimer);
    _erraSearchTimer = setTimeout(function(){ if (_erraActiveTypeId) loadErraAllocationSearch(); }, 300);
  });
  document.getElementById('erra-search-btn').addEventListener('click', function(){
    if (_erraActiveTypeId) loadErraAllocationSearch();
  });

  /* Same call the real screen's .resourcesearch / #resourcegroup1 keyup
     handlers make — returns a ready-to-insert Bootstrap panel accordion
     (Resource Type fixed to the active tab; grouped by Resource Group). */
  function loadErraAllocationSearch(){
    var wrap = document.getElementById('erra-accordion');
    var searchTerm = document.getElementById('erra-search').value.trim();
    wrap.innerHTML = '<p style="color:#aaa;padding:16px 4px;font-size:13px;">Loading…</p>';
    $.ajax({
      type: 'POST', url: '../projects/allocationsearch', dataType: 'json',
      data: { activity_id: _erraActivityId, restypeid: _erraActiveTypeId, resgroup: searchTerm, accordion_parent: 'erra-accordion' },
      success: function(d){
        wrap.innerHTML = d.result || '<p style="color:#aaa;padding:16px 4px;font-size:13px;">No resources found.</p>';
        bindErraGroupToggles(wrap, !!searchTerm);
      },
      error: function(){ wrap.innerHTML = '<p style="color:#aaa;padding:16px 4px;font-size:13px;">Failed to load.</p>'; }
    });
  }

  /* Resource Group headers come back from the server as Bootstrap
     data-toggle="collapse" markup, but bootstrap.min.js itself is not
     actually loaded anywhere on this page (commented out in views/
     layouts/main.php) — the plugin that would normally bind these has
     no JS behind it. Rather than rely on event delegation + guessing
     which element the click landed on, each group's .panel-heading gets
     its own onclick bound directly here, right after the HTML is
     inserted, toggling its panel-body's inline display directly. */
  function bindErraGroupToggles(wrap, keepOpen){
    // While a search term is active, every returned group is already a
    // filtered match — start it expanded so results are visible right
    // away instead of forcing another click per keystroke (that click
    // was also what made re-searching look like the dropdown "closed":
    // each keystroke re-fetches and previously this always re-collapsed
    // every panel, wiping out whatever the user had just opened).
    wrap.querySelectorAll('.panel').forEach(function(panel){
      var heading = panel.querySelector('.panel-heading');
      var body = panel.querySelector('.panel-collapse');
      if (!heading || !body) return;
      body.style.display = keepOpen ? 'block' : 'none';
      heading.style.cursor = 'pointer';
      heading.onclick = function(e){
        e.preventDefault();
        var isOpen = body.style.display !== 'none';
        wrap.querySelectorAll('.panel-collapse').forEach(function(p){ p.style.display = 'none'; });
        body.style.display = isOpen ? 'none' : 'block';
      };
    });
  }

  document.getElementById('erra-accordion').addEventListener('click', function(e){
    var addBtn = e.target.closest('.resource-add-btn');
    if (addBtn){ erraAddRow(addBtn); return; }
  });

  document.getElementById('erra-added-list').addEventListener('click', function(e){
    var editBtn = e.target.closest('.updatesallocateresourceunit');
    if (editBtn){ erraStartEdit(editBtn); return; }
    var saveBtn = e.target.closest('.savesallocateresourceunit');
    if (saveBtn){ erraSaveEdit(saveBtn); return; }
    var delBtn = e.target.closest('.removeresourceitem');
    if (delBtn){ erraDeleteRow(delBtn); return; }
    var taskBtn = e.target.closest('.erra-task-btn');
    if (taskBtn){ openErraTaskPopup(taskBtn); return; }
  });

  /* ─── Map to Task sub-popup ─── */
  var _ertaskResId = 0;
  function openErraTaskPopup(btn){
    if (!_erraWanId){ alert('This activity is not linked to the project WBS yet — cannot map tasks.'); return; }
    _ertaskResId = parseInt(btn.getAttribute('data-resid')) || 0;
    if (!_ertaskResId) return;
    var row = btn.closest('.resmasterss');
    var nameEl = row ? row.querySelector('.rsnamemastr:nth-of-type(2) span, .rsnamemastr span') : null;
    var resName = row ? (row.querySelectorAll('.rsnamemastr span')[1] || {}).textContent || '' : '';
    document.getElementById('ertask-hdr-resname').textContent = resName ? ('— ' + resName) : '';
    document.getElementById('ertask-loading').style.display = '';
    document.getElementById('ertask-empty').style.display = 'none';
    document.getElementById('ertask-list').style.display = 'none';
    document.getElementById('ertask-list').innerHTML = '';
    document.getElementById('ertask-bk').classList.add('ertask-open');
    document.getElementById('ertask-popup').classList.add('ertask-open');

    $.ajax({
      type: 'POST', url: '../projects/getresourcetasks', dataType: 'json',
      data: { wan_id: _erraWanId, lib_activity_id: _erraActivityId, resource_id: _ertaskResId },
      success: function(d){
        document.getElementById('ertask-loading').style.display = 'none';
        if (d.error && d.error !== 'No'){ alert(d.errortext || 'Failed to load tasks.'); closeErtaskPopup(); return; }
        var tasks = d.tasks || [];
        var mapped = (d.mapped || []).map(String);
        if (!tasks.length){ document.getElementById('ertask-empty').style.display = ''; return; }
        var html = '';
        tasks.forEach(function(t){
          var checked = mapped.indexOf(String(t.id)) !== -1 ? 'checked' : '';
          html += '<label class="ertask-row">'
                + '<input type="checkbox" class="ertask-chk" value="' + t.id + '" ' + checked + '>'
                + '<span class="ertask-name">' + (t.task_name || '') + '</span>'
                + '<span class="ertask-unit">' + (t.task_unit || '') + '</span>'
                + '</label>';
        });
        document.getElementById('ertask-list').innerHTML = html;
        document.getElementById('ertask-list').style.display = '';
      },
      error: function(){
        document.getElementById('ertask-loading').style.display = 'none';
        alert('Failed to load tasks.');
        closeErtaskPopup();
      }
    });
  }

  function closeErtaskPopup(){
    document.getElementById('ertask-bk').classList.remove('ertask-open');
    document.getElementById('ertask-popup').classList.remove('ertask-open');
  }
  document.getElementById('ertask-close').addEventListener('click', closeErtaskPopup);
  document.getElementById('ertask-btn-cancel').addEventListener('click', closeErtaskPopup);
  document.getElementById('ertask-bk').addEventListener('click', closeErtaskPopup);

  document.getElementById('ertask-btn-save').addEventListener('click', function(){
    var ids = Array.prototype.map.call(document.querySelectorAll('.ertask-chk:checked'), function(c){ return c.value; });
    var saveBtn = this;
    saveBtn.disabled = true;
    $.ajax({
      type: 'POST', url: '../projects/saveresourcetasks', dataType: 'json',
      data: { wan_id: _erraWanId, resource_id: _ertaskResId, task_ids: ids },
      success: function(d){
        saveBtn.disabled = false;
        if (d.error && d.error !== 'No'){ alert(d.errortext || 'Failed to save.'); return; }
        closeErtaskPopup();
      },
      error: function(){ saveBtn.disabled = false; alert('Failed to save. Please try again.'); }
    });
  });

  /* The real screen's allocationlist HTML stacks the read-only <span>
     and the editable <input> in the same cell (input just display:none
     by default) rather than swapping one for the other, which shows both
     at once — the value AND a new input box underneath it. Hiding the
     span here turns the cell into a true in-place edit instead. */
  function erraStartEdit(editBtn){
    var mid = editBtn.getAttribute('data-v');
    document.getElementById('updatesallocateresourceunit' + mid).style.display = 'none';
    document.getElementById('savesallocateresourceunit' + mid).style.display = '';
    document.getElementById('allocateresourceqty' + mid).style.display = 'none';
    document.getElementById('allocateresourceunit' + mid).style.display = 'none';
    document.getElementById('editallocateresourceunit' + mid).style.display = '';
    document.getElementById('editallocateresourceqty' + mid).style.display = '';
  }

  function erraSaveEdit(saveBtn){
    var mid = saveBtn.getAttribute('data-v');
    var rateInput = document.getElementById('editallocateresourceunit' + mid);
    var qtyInput = document.getElementById('editallocateresourceqty' + mid);
    var updatevalue = (rateInput.value || '').replace(/,/g, '');
    var updateqty = qtyInput.value;
    if (!updatevalue || !updateqty){ alert('Please enter both Qty and Rate before saving.'); return; }

    saveBtn.disabled = true;
    $.ajax({
      type: 'POST', url: _erraWanId ? '../projects/projallocationupdate' : '../projects/updateallocaterrated', dataType: 'json',
      data: { PRID: mid, updatevalue: updatevalue, updateqty: updateqty },
      success: function(d){
        saveBtn.disabled = false;
        loadErraAllocationList();
        loadReport(); // refresh the sheet behind so its Rate/Amount reflect the new allocation
      },
      error: function(){ saveBtn.disabled = false; alert('Failed to save. Please try again.'); }
    });
  }

  function erraDeleteRow(delBtn){
    if (!confirm('Are you sure you want to remove this resource?')) return;
    var estactresId = delBtn.getAttribute('data-id');
    delBtn.disabled = true;
    $.ajax({
      type: 'POST', url: _erraWanId ? '../projects/projallocationdelete' : '../projects/deleteactivityresource', dataType: 'json',
      data: { resid: estactresId, activityid: _erraActivityId, wan_id: _erraWanId },
      success: function(d){
        loadErraAllocationList();
        if (_erraActiveTypeId) loadErraAllocationSearch();
        loadReport();
      },
      error: function(){ delBtn.disabled = false; alert('Failed to delete. Please try again.'); }
    });
  }

  /* Generic (no special resource-type) add path — matches the real
     .resource-add-btn handler in estactivity.js: restype 24/26/33
     branches exist server-side for Machinery/Labor/Manpower special
     fields, but those resourcetype IDs don't exist in live data, so
     this popup only ever hits the generic branch, same as before. */
  function erraAddRow(addBtn){
    var resId = addBtn.getAttribute('data-resid');
    var restype = addBtn.getAttribute('data-restype');
    // Read Qty/Rate from this button's own row rather than by global id —
    // a resource can legitimately appear in more than one place on the
    // page (e.g. also already sitting in the allocated list above), and
    // id="quantity{Resource_Id}" is only actually unique within a single
    // rendered add-row, not across the whole popup.
    var row = addBtn.closest('.row') || addBtn.closest('[id^="allocsearchrow"]');
    var qtyInput = row ? row.querySelector('input[id^="quantity"]') : document.getElementById('quantity' + resId);
    var rateInput = row ? row.querySelector('input[id^="srate"]') : document.getElementById('srate' + resId);
    var qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
    var rate = parseFloat(rateInput ? rateInput.value : 0) || 0;
    if (!qty || !rate){ alert('Please enter both Qty and Rate before adding.'); return; }

    addBtn.disabled = true;
    $.ajax({
      type: 'POST', url: _erraWanId ? '../projects/projallocationadd' : '../projects/addactivityresource', dataType: 'json',
      data: { EstActivity_Id: _erraActivityId, wan_id: _erraWanId, resid: resId, restype: restype, type: '', quantity: qty, srate: rate },
      success: function(d){
        addBtn.disabled = false;
        loadErraAllocationList();
        loadErraAllocationSearch();
        loadReport();
      },
      error: function(){ addBtn.disabled = false; alert('Failed to add. Please try again.'); }
    });
  }

  function closeErraPopup(){
    document.getElementById('erra-bk').classList.remove('erra-open');
    document.getElementById('erra-popup').classList.remove('erra-open');
  }
  document.getElementById('erra-close').addEventListener('click', closeErraPopup);
  document.getElementById('erra-btn-close').addEventListener('click', closeErraPopup);
  document.getElementById('erra-bk').addEventListener('click', closeErraPopup);

  eriowBindDragResize('erra-popup', 'erra-hdr', 'erra-rs', 600, 360);

  // Drag + resize — generic helper shared by erra-popup and any future
  // popup in this file that needs the same chrome (rs handles + header
  // drag), parameterized by element/class-prefix ids instead of being
  // hand-duplicated per popup like the other files in this app.
  function eriowBindDragResize(popupId, hdrId, rsClass, minW, minH){
    var win = document.getElementById(popupId);
    var hdr = document.getElementById(hdrId);
    var _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0;
    hdr.addEventListener('mousedown', function(e){
      if(e.target.id===hdrId.replace('hdr','close')) return;
      var r=win.getBoundingClientRect();
      win.style.left=r.left+'px'; win.style.top=r.top+'px'; win.style.width=r.width+'px'; win.style.height=r.height+'px'; win.style.transform='none';
      _action='drag'; _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top;
      e.preventDefault();
    });
    document.querySelectorAll('.' + rsClass).forEach(function(el){
      el.addEventListener('mousedown', function(e){
        var r=win.getBoundingClientRect();
        win.style.left=r.left+'px'; win.style.top=r.top+'px'; win.style.width=r.width+'px'; win.style.height=r.height+'px'; win.style.transform='none';
        _action=el.getAttribute('data-dir');
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
        if(_action.indexOf('e')>-1){ w=Math.max(minW,_ow+dx); }
        if(_action.indexOf('s')>-1){ h=Math.max(minH,_oh+dy); }
        if(_action.indexOf('w')>-1){ var nw=Math.max(minW,_ow-dx); l=_ox+(_ow-nw); w=nw; }
        if(_action.indexOf('n')>-1){ var nh=Math.max(minH,_oh-dy); t=_oy+(_oh-nh); h=nh; }
        win.style.left=l+'px'; win.style.top=t+'px'; win.style.width=w+'px'; win.style.height=h+'px';
      }
    });
    document.addEventListener('mouseup', function(){ _action=null; });
  }

})();
</script>
