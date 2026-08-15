<?php
/* newganttview.php — Clean Gantt chart view.
 * Works both as a full page (layout=true) and in the popup modal (layout=false, injected via $.ajax).
 * Data comes from actionGanttitems + actionGanttactivities (direct scheduleactivities query, no JOIN).
 * CPM coloring: gtaskpink = critical path, gtaskblue = normal.
 * Root task uses projectId as pID — never 0 (JSGantt reserves 0 as the virtual root sentinel).
 */
?>
<style>
/* ── Activity name hover tooltip: Planned/Actual Start-End grid ───────────── */
#gname-tip {
  display:none; position:fixed; z-index:200400;
  background:#fff; border-radius:6px; box-shadow:0 6px 24px rgba(0,0,0,.3);
  border:1px solid #dde3ef; padding:0; min-width:340px; font-size:11px;
}
#gname-tip.gname-tip-open { display:block; }
#gname-tip .gname-tip-row {
  display:flex; justify-content:space-between; padding:6px 12px;
  white-space:nowrap;
}
#gname-tip .gname-tip-row.gname-tip-planned { background:#eef1f5; color:#1a2540; }
#gname-tip .gname-tip-row.gname-tip-actual  { background:#f7f8f9; color:#1a2540; }
#gname-tip .gname-tip-name {
  padding:8px 12px; text-align:center; font-weight:700; font-size:12px;
  color:#fff; background:#1a2540; border-bottom:1px solid #0d1730;
  border-radius:5px 5px 0 0;
}
#gname-tip .gname-tip-row span { display:inline-block; }
#gname-tip .gname-tip-row b { color:#1a2540; }
#gname-tip .gname-tip-row.gname-tip-actual b { color:#1a2540; }
#gname-tip .gname-date-late  { color:#e53935; font-weight:700; }
#gname-tip .gname-date-early { color:#27ae60; font-weight:700; }
/* Editable date value — Planned Start / Actual Start only (per-field class) */
#gname-tip .gname-date-editable { cursor:pointer; border-bottom:1px dashed #8a93a6; }
#gname-tip .gname-date-editable:hover { background:#dde6f5; }
#gname-tip .gname-date-input {
  border:1px solid #4a6fa5; border-radius:3px; font-size:11px; padding:1px 3px;
  width:104px; font-family:inherit;
}
#gname-tip .gname-tip-err {
  padding:5px 12px; font-size:10px; color:#e53935; background:#fdecea;
  border-top:1px solid #f5c6c2;
}

/* ── Gantt row highlight when cost modal is open ──────────────────────────── */
tr.gcm-row-highlight td { background: #fff8e1 !important; outline: 2px solid #e8820c; outline-offset: -1px; }

/* ── KPI Click Modal ─────────────────────────────────────────────────────── */
#gkm-bk { display:none; }
#gkm-modal {
  display:none; position:fixed; z-index:10015;
  top:80px; left:calc(50% - 370px);
  width:740px; height:58vh;
  min-width:380px; min-height:420px;
  background:#f0f3fa; border-radius:10px;
  box-shadow:0 8px 40px rgba(0,0,0,0.28);
  flex-direction:column; overflow:hidden;
}
#gkm-modal.gkm-open { display:flex; }
#gkm-header {
  background:#1a2540; color:#fff; padding:10px 16px;
  display:flex; align-items:center; justify-content:space-between; flex-shrink:0;
  cursor:move; user-select:none;
}
#gkm-title { font-size:13px; font-weight:600; font-family:'Barlow Condensed',sans-serif; }
#gkm-close { background:none; border:none; color:#fff; font-size:18px; cursor:pointer; padding:0 4px; line-height:1; }
.gkm-rs { position:absolute; z-index:20; background:transparent; }
.gkm-rs-e  { right:-4px; top:10px; bottom:10px; width:10px; cursor:e-resize; }
.gkm-rs-w  { left:-4px;  top:10px; bottom:10px; width:10px; cursor:w-resize; }
.gkm-rs-s  { bottom:-4px; left:10px; right:10px; height:10px; cursor:s-resize; }
.gkm-rs-n  { top:-4px;  left:10px; right:10px; height:10px; cursor:n-resize; }
.gkm-rs-se { right:-4px; bottom:-4px; width:14px; height:14px; cursor:se-resize; }
.gkm-rs-sw { left:-4px;  bottom:-4px; width:14px; height:14px; cursor:sw-resize; }
.gkm-rs-ne { right:-4px; top:-4px; width:14px; height:14px; cursor:ne-resize; }
.gkm-rs-nw { left:-4px;  top:-4px; width:14px; height:14px; cursor:nw-resize; }
#gkm-loading { text-align:center; padding:40px; font-size:13px; color:#5a6e8c; }
#gkm-body { flex:1; min-height:0; display:flex; flex-direction:column; padding:6px 10px 0; gap:0; overflow:hidden; }
#gkm-content { display:grid; grid-template-columns:repeat(3, 1fr); grid-template-rows:repeat(2, 1fr); gap:5px; flex:1; min-height:0; overflow-y:auto; }
#gkm-row1 { display:contents; }
#gkm-row2 { display:contents; }
.gkm-panel {
  background:#fff; border-radius:5px; border:1px solid #dde3ef;
  display:flex; flex-direction:column; overflow:hidden; min-width:0; min-height:0;
}
.gkm-panel-title {
  font-size:10px; font-weight:700; color:#e8efff; text-transform:uppercase;
  letter-spacing:0.04em; padding:5px 8px; border-bottom:1px solid #0d1f3c; flex-shrink:0;
  background:#1a2540;
}
.gkm-panel-body { flex:1; min-height:0; overflow:auto; display:flex; flex-direction:column; }

/* ── KPI Float Panel ─────────────────────────────────────────────────────── */
#gkp-popup {
  display: none; position: fixed; z-index: 100200;
  top: 80px; left: calc(50% - 370px);
  width: 740px; height: 58vh;
  min-width: 380px; min-height: 420px;
  background: #f0f3fa;
  border-radius: 10px; box-shadow: 0 8px 36px rgba(0,0,0,0.32);
  overflow: hidden; pointer-events: auto;
  flex-direction: column;
}
#gkp-popup.gkp-visible { display: flex; }
#gkp-hdr {
  background: #1a2540; color: #fff; padding: 10px 14px;
  font-size: 13px; font-weight: 600; font-family: 'Barlow Condensed', sans-serif;
  display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
  cursor: move; user-select: none;
}
#gkp-close { background:none; border:none; color:#fff; font-size:18px; cursor:pointer; padding:0 4px; line-height:1; flex-shrink:0; }
#gkp-body {
  display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(2, 1fr);
  gap: 5px; padding: 6px; flex: 1; min-height: 0; overflow-y: auto;
}
#gkp-row1 { display: contents; }
#gkp-row2 { display: contents; }
.gkp-rs { position:absolute; z-index:20; background:transparent; }
.gkp-rs-e  { right:-4px; top:10px; bottom:10px; width:10px; cursor:e-resize; }
.gkp-rs-w  { left:-4px;  top:10px; bottom:10px; width:10px; cursor:w-resize; }
.gkp-rs-s  { bottom:-4px; left:10px; right:10px; height:10px; cursor:s-resize; }
.gkp-rs-n  { top:-4px;  left:10px; right:10px; height:10px; cursor:n-resize; }
.gkp-rs-se { right:-4px; bottom:-4px; width:14px; height:14px; cursor:se-resize; }
.gkp-rs-sw { left:-4px;  bottom:-4px; width:14px; height:14px; cursor:sw-resize; }
.gkp-rs-ne { right:-4px; top:-4px; width:14px; height:14px; cursor:ne-resize; }
.gkp-rs-nw { left:-4px;  top:-4px; width:14px; height:14px; cursor:nw-resize; }
.gkp-panel {
  background: #fff; border-radius: 5px; border: 1px solid #dde3ef;
  display: flex; flex-direction: column; overflow: hidden; min-width: 0; min-height: 0;
}
.gkp-panel-title {
  font-size: 9px; font-weight: 700; color: #e8efff; text-transform: uppercase;
  letter-spacing: 0.04em; padding: 4px 7px; flex-shrink: 0;
  background: #1a2540; border-bottom: 1px solid #0d1f3c;
}
.gkp-panel-body { flex: 1; min-height: 0; overflow: hidden; display: flex; flex-direction: column; }
.gkp-kv { display:flex; justify-content:space-around; padding:3px 4px; background:#f5f7fc; border-top:1px solid #e0e6f0; flex-shrink:0; font-family:'Barlow Condensed',sans-serif; font-size:10px; }
.gkp-kv-item { display:flex; flex-direction:column; align-items:center; }
.gkp-kv-lbl { font-size:8px; color:#7a8aaa; text-transform:uppercase; letter-spacing:0.03em; }
.gkp-kv-val { font-size:11px; font-weight:700; color:#1a2540; }
.gkp-kv-val.over { color:#e53935; }
.gkp-kv-val.good { color:#27ae60; }

/* ── Resource Capacity bar hover tooltip — reuses .gm-bar-tip (Cost
   Dashboard's Est/Act popup style), sized up slightly ── */
#gkp-res-tip.gkp-res-tip-big { padding: 14px 16px; min-width: 280px; }
#gkp-res-tip.gkp-res-tip-big .gm-bar-tip-hdr { font-size: 12px; margin-bottom: 10px; }
#gkp-res-tip.gkp-res-tip-big .gm-bar-stack { width: 34px; }
#gkp-res-tip.gkp-res-tip-big .gm-bar-val { font-size: 12px; }
#gkp-res-tip.gkp-res-tip-big .gm-bar-lbl { font-size: 11px; max-width: 68px; }

/* ── Project Duration hover panel — independent of KPI/Cost dashboards.
   Triggered by hovering #gantt-win-proj-name in the Gantt window header. ── */
#gpd-tip {
  display: none; position: fixed; z-index: 10020;
  width: 520px;
  background: #fff; border-radius: 6px; border: 1px solid #dde3ef;
  box-shadow: 0 8px 30px rgba(0,0,0,0.3);
  overflow: hidden; font-family: 'Barlow Condensed', sans-serif;
}
#gpd-tip.gpd-open { display: block; }
#gpd-tip-title {
  font-size: 12px; font-weight: 700; color: #e8efff; text-transform: uppercase;
  letter-spacing: 0.04em; text-align: center;
  padding: 8px 12px; background: #1a2540;
  border-bottom: 1px solid #0d1f3c;
  cursor: move; user-select: none;
}
#gpd-tip-body { padding: 16px 18px; }
#gpd-proj-name { font-size:14px; font-weight:700; color:#1a2540; text-align:left; margin-bottom:10px; }
#gpd-bar-status { font-size:12px; font-weight:700; margin-bottom:4px; min-height:15px; }
#gpd-bar-status.gpd-late  { color:#e53935; }
#gpd-bar-status.gpd-early { color:#27ae60; }
#gpd-bar { position:relative; display:flex; align-items:stretch; height:10px; border-radius:4px; overflow:hidden; margin:8px 0 15px; background:#eef1f6; }
.gpd-bar-labels { display:flex; justify-content:space-between; font-size:13px; font-weight:400; color:#000; }
.gpd-bar-labels b { color:#00838f; font-weight:700; }
.gpd-divider { border-top:1px solid #b9c0cc; margin:11px 0; }
.gpd-date-labels { font-size:13px; color:#000; }
.gpd-date-labels b { color:#1a2540; }
#gpd-tip-loading { padding:16px; text-align:center; color:#9aa5ba; font-size:11px; }

/* ── Project Snapshot hover card — hovering the project name in the
   Gantt window header (#gantt-win-proj-name). Independent of #gpd-tip
   (the existing click-triggered popup) — separate element, separate
   trigger, same visual language as the "Projects" list card (.gpm-card
   in main.php: rounded white card, bold name in a bordered header
   strip, label/value rows below). Values are placeholders for now —
   the source formulas for several fields (Actual Duration,
   Delayed/Earlier, Actual Completion Date) are still to be defined. ── */
#gsc-tip {
  display: none; position: fixed; z-index: 10025;
  width: 480px;
  border-radius:16px;
  box-sizing:border-box;background:#efefef;overflow:hidden;
  box-shadow:0 8px 30px rgba(0,0,0,0.25);
  font-family: 'Barlow Condensed', sans-serif;
}
#gsc-tip.gsc-open { display: block; }
#gsc-tip-top { background:#d8d8d8;padding:14px 20px; cursor:move; user-select:none; display:flex; align-items:center; justify-content:space-between; gap:8px; }
#gsc-tip-name { font-size:15px;font-weight:700;color:#1a1a1a;margin:0; }
#gsc-tip-close { background:none; border:none; font-size:19px; line-height:1; color:#777; cursor:pointer; padding:0 2px; flex-shrink:0; }
#gsc-tip-close:hover { color:#222; }
#gsc-tip-body { padding:12px 22px 22px;background:transparent; }
#gsc-tip-content { display:flex; flex-wrap:wrap; row-gap:16px; }
.gsc-row { font-size:15px;color:#1a1a1a;display:flex; align-items:center; gap:10px; flex:0 0 50%; min-width:0; box-sizing:border-box; white-space:nowrap; }
.gsc-row label { font-weight:400;color:#1a1a1a;margin:0;display:flex; align-items:center; gap:10px; }
.gsc-swatch { display:inline-block; width:12px; height:12px; border-radius:2px; flex-shrink:0; }
.gsc-row .gsc-val { font-weight:700; color:#1a1a1a; margin-left:6px; }
.gsc-row .gsc-val.gsc-late  { color:#e53935; }
.gsc-row .gsc-val.gsc-early { color:#27ae60; }
#gsc-tip-loading { padding:16px; text-align:center; color:#9aa5ba; font-size:11px; }

/* ── Gantt Cost Float Panel ─────────────────────────────────────────────────── */
#gcm-popup {
  display: none; position: fixed; z-index: 100200;
  top: 80px; left: calc(50% - 370px);
  width: 740px; height: 58vh;
  min-width: 380px; min-height: 420px;
  background: #f0f3fa;
  border-radius: 10px; box-shadow: 0 8px 36px rgba(0,0,0,0.32);
  overflow: hidden; pointer-events: auto;
  flex-direction: column;
}
#gcm-popup.gcm-visible { display: flex; }
#gcm-pop-hdr {
  background: #1a2540; color: #fff; padding: 10px 14px;
  display: flex; align-items: center; flex-shrink: 0;
  cursor: move; user-select: none;
}
#gcm-pop-hdr-left, #gcm-pop-hdr-right { flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
#gcm-pop-hdr-right { display: flex; justify-content: flex-end; }
#gcm-pop-hdr-left, #gcm-pop-hdr-center {
  font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
}
#gcm-pop-hdr-left { color: #e8efff; }
#gcm-pop-hdr-center {
  flex-shrink: 0; text-align: center; padding: 0 12px; color: #ffffff;
}
#gcm-pop-close { background:none; border:none; color:#fff; font-size:18px; cursor:pointer; padding:0 4px; line-height:1; flex-shrink:0; }
#gcm-pop-body {
  display: flex; flex-direction: column; gap: 5px; padding: 6px; flex: 1; min-height: 0; overflow: hidden;
}
#gcm-pop-row1 { display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(2, 1fr); gap: 5px; flex: 1; min-height: 0; overflow-y: auto; }
#gcm-pop-row2 { display: none !important; }
.gcm-rs { position:absolute; z-index:20; background:transparent; }
.gcm-rs-e  { right:-4px; top:10px; bottom:10px; width:10px; cursor:e-resize; }
.gcm-rs-w  { left:-4px;  top:10px; bottom:10px; width:10px; cursor:w-resize; }
.gcm-rs-s  { bottom:-4px; left:10px; right:10px; height:10px; cursor:s-resize; }
.gcm-rs-n  { top:-4px;  left:10px; right:10px; height:10px; cursor:n-resize; }
.gcm-rs-se { right:-4px; bottom:-4px; width:14px; height:14px; cursor:se-resize; }
.gcm-rs-sw { left:-4px;  bottom:-4px; width:14px; height:14px; cursor:sw-resize; }
.gcm-rs-ne { right:-4px; top:-4px; width:14px; height:14px; cursor:ne-resize; }
.gcm-rs-nw { left:-4px;  top:-4px; width:14px; height:14px; cursor:nw-resize; }
.gcm-pop-panel {
  background: #fff; border-radius: 5px; border: 1px solid #dde3ef;
  display: flex; flex-direction: column; overflow: hidden; min-width: 0; min-height: 0;
}
.gcm-pop-panel-title {
  font-size: 9px; font-weight: 700; color: #e8efff; text-transform: uppercase;
  letter-spacing: 0.04em; padding: 4px 7px; flex-shrink: 0;
  background: #1a2540; border-bottom: 1px solid #0d1f3c;
}
.gcm-pop-panel-body { flex: 1; min-height: 0; overflow: hidden; display: flex; flex-direction: column; }
#gcm-pop-loading { text-align: center; padding: 18px; font-size: 11px; color: #5a6e8c; }

/* ── Resource hover tooltips (vertical Est vs Act bars) — shared by Unit
   Cost of Resources (#gm-ucr-tip) and Yield (#gm-rcon-tip) panels ── */
#gm-cd-ucr, #gm-cd-rcon, #gm-cd-rcost { cursor:default; }
.gm-bar-tip {
  display:none; position:fixed; z-index:200300;
  background:#fff; border-radius:6px; box-shadow:0 6px 24px rgba(0,0,0,.3);
  padding:10px 12px; min-width:220px; max-width:560px; font-size:11px;
}
.gm-bar-tip.gm-bar-tip-open { display:block; }
.gm-bar-tip .gm-bar-tip-hdr {
  font-size:10px; font-weight:700; color:#1a2540; text-transform:uppercase;
  letter-spacing:.04em; margin-bottom:8px;
}
.gm-bar-tip .gm-bar-bars {
  display:flex; align-items:flex-end; gap:10px; overflow-x:auto; padding-bottom:2px;
}
.gm-bar-tip .gm-bar-col { display:flex; flex-direction:column; align-items:center; flex-shrink:0; width:auto; min-width:52px; }
.gm-bar-tip .gm-bar-val { font-size:10px; font-weight:700; color:#1a2540; margin-bottom:3px; white-space:nowrap; padding:0 2px; }
.gm-bar-tip .gm-bar-stack {
  width:26px; display:flex; flex-direction:column; justify-content:flex-end;
  border:1px solid #dde3ef; border-bottom:2px solid #1a2540; border-radius:2px 2px 0 0;
  background-image: repeating-linear-gradient(to top, #eef1f5 0, #eef1f5 1px, transparent 1px, transparent 22.5px);
  background-position: bottom; background-repeat: repeat-y;
  position: relative; overflow:hidden;
}
.gm-bar-tip .gm-bar-lbl {
  font-size:9.5px; color:#5a6e8c; margin-top:5px; text-align:center; line-height:1.2;
  max-width:52px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.gm-bar-tip .gm-bar-tip-legend {
  display:flex; gap:12px; flex-wrap:wrap; margin-top:10px; padding-top:8px;
  border-top:1px solid #eee; font-size:9.5px; color:#5a6e8c;
}
.gm-bar-tip .gm-bar-tip-legend span { display:inline-flex; align-items:center; gap:4px; }
.gm-bar-tip .gm-bar-tip-legend i { width:9px; height:9px; border-radius:2px; display:inline-block; }
.gm-bar-tip .gm-bar-tip-empty { padding:10px; text-align:center; color:#aaa; }

/* ── Gantt Cost Modal (kept for backwards compat, hidden by default) ─────── */
#gcm-bk { display: none; }
#gcm-modal { display: none; }
.gcm-panel {
  flex: 1; background: #fff; border-radius: 6px; border: 1px solid #dde3ef;
  display: flex; flex-direction: column; overflow: hidden; min-width: 0;
}
.gcm-panel-title {
  font-size: 10px; font-weight: 700; color: #e8efff; text-transform: uppercase;
  letter-spacing: 0.04em; padding: 5px 8px; border-bottom: 1px solid #0d1f3c; flex-shrink: 0;
  background: #1a2540;
}
.gcm-panel-body {
  flex: 1; min-height: 0; overflow: auto; display: flex; flex-direction: column;
}

/* Gantt floating panel — standalone page only */
<?php if(empty($embedMode)): ?>
#gantt-float {
  position: fixed;
  top: 74px; left: 20px;
  width: calc(100vw - 40px);
  height: calc(100vh - 110px);
  min-width: 500px; min-height: 300px;
  z-index: 9500;
  background: #fff;
  border-radius: 6px;
  box-shadow: 0 8px 32px rgba(0,0,0,.4);
  display: flex; flex-direction: column;
  overflow: hidden;
}
<?php endif; ?>
/* Embed mode: fill the gantt-win-body container */
<?php if(!empty($embedMode)): ?>
#gantt-embed-wrap {
  display: flex; flex-direction: column;
  width: 100%; height: 100%; overflow: hidden;
}
<?php endif; ?>
#gantt-float-hdr {
  background: #fff; color: #333;
  padding: 10px 15px;
  display: flex; align-items: center; justify-content: space-between;
  cursor: move; user-select: none; flex-shrink: 0;
  font-family: 'Nunito',sans-serif; font-size: 18px; font-weight: 700;
  border-bottom: 1px solid #e5e5e5;
}
#gantt-float-body {
  flex: 1; min-height: 0; display: flex; flex-direction: column;
  padding: 8px 10px; overflow: hidden;
}
/* resize handles */
.gf-rs { position: absolute; z-index: 10; background: transparent; }
.gf-rs-e  { right:0;  top:6px;    bottom:6px; width:6px;  cursor:e-resize; }
.gf-rs-w  { left:0;   top:6px;    bottom:6px; width:6px;  cursor:w-resize; }
.gf-rs-s  { bottom:0; left:6px;   right:6px;  height:6px; cursor:s-resize; }
.gf-rs-n  { top:0;    left:6px;   right:6px;  height:6px; cursor:n-resize; }
.gf-rs-se { right:0;  bottom:0; width:14px; height:14px; cursor:se-resize; }
.gf-rs-sw { left:0;   bottom:0; width:14px; height:14px; cursor:sw-resize; }
.gf-rs-ne { right:0;  top:0;    width:14px; height:14px; cursor:ne-resize; }
.gf-rs-nw { left:0;   top:0;    width:14px; height:14px; cursor:nw-resize; }
#gantt-toolbar {
  padding: 4px 0 6px;
  display: flex; align-items: center; gap: 10px; flex-wrap: wrap; flex-shrink: 0;
  position: relative;
}
#gantt-container {
  border: 2px solid #000;
  overflow-y: auto; overflow-x: hidden;
  width: 100%; flex: 1; min-height: 0;
}
/* Left panel: wider, never scrolls horizontally */
#gantt-container .gmainleft {
  overflow-x: hidden !important;
}
#gantt-container .glistlbl,
#gantt-container .gtasktablewrapper,
#gantt-container .gtasktableouterwrapper {
  overflow-x: hidden !important;
}
/* Widen the activity name column */
#gantt-container .gtaskname,
#gantt-container .gspanning.gtaskname {
  min-width: 280px !important;
  width: 280px !important;
  max-width: 280px !important;
}
/* Dedicated status-indicator column (sits just before the activity name column) */
#gantt-container .gtasklist {
  min-width: 32px !important;
  width: 32px !important;
  max-width: 32px !important;
  text-align: center !important;
  white-space: nowrap;
}
.gact-reorder-handle {
  display: inline-block; cursor: grab; color: #9aa3b2; font-size: 11px;
  letter-spacing: -2px; vertical-align: middle; margin-left: 3px;
  user-select: none;
}
.gact-reorder-handle:hover { color: #1a6fc4; }
.gact-reorder-handle.gact-dragging { cursor: grabbing; }
/* Ghost row that follows the cursor while dragging */
#gact-drag-ghost {
  display: none; position: fixed; z-index: 300000; pointer-events: none;
  background: #fff; border: 1px solid #4a6fa5; border-radius: 4px;
  box-shadow: 0 4px 14px rgba(0,0,0,.25); padding: 4px 10px;
  font-size: 12px; color: #1a2540; white-space: nowrap;
}
/* Highlight the row currently under the cursor as the drop target */
#gantt-container .gtasktablewrapper tr.gact-drop-target td {
  background: #eef4fb !important; outline: 1px dashed #4a6fa5; outline-offset: -1px;
}
.btn-opiam {
  position: relative;
  padding: 4px 14px;
  background: #072c47;
  border-radius: 20px;
  color: #fff;
  border: none;
  font-size: 13px;
  cursor: pointer;
}
.btn-opiam:hover { background: #0a3d62; color: #fff; }
#relations-panel {
  display: none;
  margin-top: 12px;
  border: 1px solid #b6b6b6;
  border-radius: 4px;
  padding: 12px;
  background: #fff;
}
.gantt-legend-row { display: flex; align-items: center; gap: 20px; padding: 8px 0; flex-wrap: wrap; }
.gantt-legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #333; }
.gantt-legend-dot {
  width: 15px; height: 15px; border-radius: 50%;
  border: 1px solid darkgrey; display: inline-block; flex-shrink: 0;
}
.dot-critical  { background: #00ACC1; }
.dot-normal    { background: #337ab7; }
/* Critical path bar colour — overrides external CSS (needed when view loads without full layout) */
.gtaskpink, div.gtaskpink.gplan { background: #00ACC1 !important; border-color: #0097A7 !important; }
/* Completed activity bar colour */
.gtaskgreen, div.gtaskgreen.gplan { background: #1b5e20 !important; border-color: #0d3d10 !important; }
/* Status dots */
.act-dot {
  display: inline-block; width: 12px; height: 12px;
  border-radius: 50%; vertical-align: middle;
}
.act-dot.ongoing   { background: #546e7a; }
.act-dot.upcoming  { background: #0d2b6e; }
.act-dot.overdue   { background: #ff2800; }
.act-dot-check {
  display: inline-block; width: 12px; height: 12px;
  border-radius: 50%; background: #43a047;
  color: #fff; font-size: 9px; line-height: 12px;
  text-align: center; vertical-align: middle;
}
</style>

<?php if(empty($embedMode)): ?>
<div id="gantt-float">
  <!-- resize handles -->
  <div class="gf-rs gf-rs-n"  data-dir="n"></div>
  <div class="gf-rs gf-rs-s"  data-dir="s"></div>
  <div class="gf-rs gf-rs-e"  data-dir="e"></div>
  <div class="gf-rs gf-rs-w"  data-dir="w"></div>
  <div class="gf-rs gf-rs-ne" data-dir="ne"></div>
  <div class="gf-rs gf-rs-nw" data-dir="nw"></div>
  <div class="gf-rs gf-rs-se" data-dir="se"></div>
  <div class="gf-rs gf-rs-sw" data-dir="sw"></div>
  <!-- drag header -->
  <div id="gantt-float-hdr">
    <span>&#9776; Gantt Chart — drag to move</span>
    <span style="display:flex;align-items:center;gap:8px;">
      <button id="gantt-btn-expand" title="Fullscreen" style="background:none;border:none;color:#333;font-size:16px;cursor:pointer;line-height:1;padding:0 4px;">&#x26F6;</button>
      <button id="gantt-btn-close" title="Close" style="background:none;border:none;color:#333;font-size:22px;cursor:pointer;line-height:1;padding:0 4px;">&times;</button>
    </span>
  </div>
  <!-- body -->
  <div id="gantt-float-body">
<?php else: ?>
<div id="gantt-embed-wrap">
  <div id="gantt-float-body" style="flex:1;min-height:0;display:flex;flex-direction:column;padding:8px 10px;overflow:hidden;">
<?php endif; ?>
    <div id="gantt-toolbar">

      <span id="gantt-status" style="font-size:12px;color:#666;margin-left:8px;"></span>
    </div>
    <div id="gantt-container">
      <div id="gantt-loading" style="padding:50px;text-align:center;">Loading Gantt chart&hellip;</div>
    </div>
    <div class="gantt-legend-row" style="margin-top:6px;">
      <div class="gantt-legend-item"><span class="gantt-legend-dot dot-critical"></span> Critical Path</div>
      <div class="gantt-legend-item"><span class="gantt-legend-dot dot-normal"></span> Normal Activity</div>
      <div class="gantt-legend-item"><span class="act-dot" style="background:#0d2b6e;display:inline-block;width:12px;height:12px;border-radius:50%;"></span> Upcoming</div>
      <div class="gantt-legend-item"><span class="act-dot ongoing"></span> Ongoing</div>
      <div class="gantt-legend-item"><span class="act-dot overdue"></span> Overdue</div>
      <div class="gantt-legend-item"><span class="act-dot-check">✓</span> Completed</div>
    </div>
    <div id="relations-panel">
      <div id="relations-content"><em>Loading&hellip;</em></div>
    </div>
  </div>
<?php if(empty($embedMode)): ?>
</div>
<?php else: ?>
</div>
<?php endif; ?>

<!-- Gantt Cost Float Panel -->
<div id="gcm-popup">
  <div class="gcm-rs gcm-rs-n" data-dir="n"></div>
  <div class="gcm-rs gcm-rs-s" data-dir="s"></div>
  <div class="gcm-rs gcm-rs-e" data-dir="e"></div>
  <div class="gcm-rs gcm-rs-w" data-dir="w"></div>
  <div class="gcm-rs gcm-rs-ne" data-dir="ne"></div>
  <div class="gcm-rs gcm-rs-nw" data-dir="nw"></div>
  <div class="gcm-rs gcm-rs-se" data-dir="se"></div>
  <div class="gcm-rs gcm-rs-sw" data-dir="sw"></div>
  <div id="gcm-pop-hdr">
    <span id="gcm-pop-hdr-left">Cost Dashboard</span>
    <span id="gcm-pop-hdr-center">Cost Drivers - Panel</span>
    <span id="gcm-pop-hdr-right"><button id="gcm-pop-close" title="Close">&times;</button></span>
  </div>
  <div id="gcm-pop-body">
    <div id="gcm-pop-loading">Loading&hellip;</div>
    <div id="gcm-pop-row1" style="display:none">
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Unit Cost of Resources</div><div class="gcm-pop-panel-body" id="gm-cd-ucr" style="align-items:center;justify-content:flex-start"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Yield</div><div class="gcm-pop-panel-body" id="gm-cd-rcon" style="align-items:center;justify-content:flex-start"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Cost of Resources</div><div class="gcm-pop-panel-body" id="gm-cd-rcost" style="align-items:center;justify-content:flex-start"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Unit Cost of Activity</div><div class="gcm-pop-panel-body" id="gm-cd-g5" style="align-items:center;justify-content:flex-start"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Work Done</div><div class="gcm-pop-panel-body" id="gm-cd-g4" style="align-items:center;justify-content:flex-start"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Cost of Activity</div><div class="gcm-pop-panel-body" id="gm-cd-g2"></div></div>
    </div>
    <div id="gcm-pop-row2" style="display:none"></div>
  </div>
</div>

<div id="gm-ucr-tip" class="gm-bar-tip"></div>
<div id="gm-rcon-tip" class="gm-bar-tip"></div>
<div id="gm-rcost-tip" class="gm-bar-tip"></div>
<div id="gname-tip"></div>
<div id="gact-drag-ghost"></div>

<!-- KPI Click Modal -->
<div id="gkm-bk"></div>
<div id="gkm-modal">
  <div class="gkm-rs gkm-rs-n" data-dir="n"></div>
  <div class="gkm-rs gkm-rs-s" data-dir="s"></div>
  <div class="gkm-rs gkm-rs-e" data-dir="e"></div>
  <div class="gkm-rs gkm-rs-w" data-dir="w"></div>
  <div class="gkm-rs gkm-rs-ne" data-dir="ne"></div>
  <div class="gkm-rs gkm-rs-nw" data-dir="nw"></div>
  <div class="gkm-rs gkm-rs-se" data-dir="se"></div>
  <div class="gkm-rs gkm-rs-sw" data-dir="sw"></div>
  <div id="gkm-header">
    <span id="gkm-title">KPI Dashboard</span>
    <button id="gkm-close" title="Close">&times;</button>
  </div>
  <div id="gkm-body">
    <div id="gkm-loading">Loading&hellip;</div>
    <div id="gkm-content" style="display:none">
      <div id="gkm-row1">
        <div class="gkm-panel"><div class="gkm-panel-title">Target Production</div><div class="gkm-panel-body" id="gkm-tp"></div></div>
        <div class="gkm-panel"><div class="gkm-panel-title">Activity Duration</div><div class="gkm-panel-body" id="gkm-dur"></div></div>
        <div class="gkm-panel"><div class="gkm-panel-title">Capacity Utilisation</div><div class="gkm-panel-body" id="gkm-g5"></div></div>
      </div>
      <div id="gkm-row2">
        <div class="gkm-panel"><div class="gkm-panel-title">Cycle Time</div><div class="gkm-panel-body" id="gkm-g4"></div></div>
        <div class="gkm-panel"><div class="gkm-panel-title">Productivity</div><div class="gkm-panel-body" id="gkm-g3"></div></div>
      </div>
    </div>
  </div>
</div>

<!-- KPI Float Panel -->
<div id="gkp-popup">
  <div class="gkp-rs gkp-rs-n" data-dir="n"></div>
  <div class="gkp-rs gkp-rs-s" data-dir="s"></div>
  <div class="gkp-rs gkp-rs-e" data-dir="e"></div>
  <div class="gkp-rs gkp-rs-w" data-dir="w"></div>
  <div class="gkp-rs gkp-rs-ne" data-dir="ne"></div>
  <div class="gkp-rs gkp-rs-nw" data-dir="nw"></div>
  <div class="gkp-rs gkp-rs-se" data-dir="se"></div>
  <div class="gkp-rs gkp-rs-sw" data-dir="sw"></div>
  <div id="gkp-hdr"><span id="gkp-title">KPI Dashboard</span><button id="gkp-close" title="Close">&times;</button></div>
  <div id="gkp-body">
    <div id="gkp-row1">
      <div class="gkp-panel"><div class="gkp-panel-title">Resource Capacity</div><div class="gkp-panel-body" id="gkp-res"></div></div>
      <div class="gkp-panel"><div class="gkp-panel-title">Capacity Utilisation</div><div class="gkp-panel-body" id="gkp-g5"></div></div>
      <div class="gkp-panel"><div class="gkp-panel-title">Cycle Time</div><div class="gkp-panel-body" id="gkp-g4"></div></div>
    </div>
    <div id="gkp-row2">
      <div class="gkp-panel"><div class="gkp-panel-title">Productivity</div><div class="gkp-panel-body" id="gkp-g3"></div></div>
      <div class="gkp-panel"><div class="gkp-panel-title">Target Production</div><div class="gkp-panel-body" id="gkp-tp"></div></div>
      <div class="gkp-panel"><div class="gkp-panel-title">Activity Duration</div><div class="gkp-panel-body" id="gkp-dur"></div></div>
    </div>
  </div>
</div>

<!-- Resource Capacity bar hover tooltip — independent, own trigger, own data -->
<div id="gkp-res-tip" class="gm-bar-tip"></div>

<!-- Project Duration hover panel — independent, own trigger, own data -->
<div id="gpd-tip">
  <div id="gpd-tip-title">Project Schedule Summery</div>
  <div id="gpd-tip-body">
    <div id="gpd-proj-name"><?= htmlspecialchars(isset($project) && $project ? $project->Name : 'Project') ?></div>
    <div id="gpd-tip-loading">Loading&hellip;</div>
    <div id="gpd-tip-content" style="display:none">
      <div id="gpd-bar-status"></div>
      <div id="gpd-bar"></div>
      <div class="gpd-bar-labels">
        <span>Target Duration: <b id="gpd-target-comp">—</b></span>
        <span>Expected Duration: <b id="gpd-expected-comp">—</b></span>
      </div>
      <div class="gpd-divider"></div>
      <div class="gpd-bar-labels gpd-date-labels">
        <span>Target Completion Date: <b id="gpd-target-date">—</b></span>
        <span>Expected Completion Date: <b id="gpd-expected-date">—</b></span>
      </div>
      <div class="gpd-divider"></div>
      <div class="gpd-bar-labels gpd-date-labels">
        <span>Elapsed Days: <b id="gpd-elapsed-days">—</b></span>
        <span>Remaining Days: <b id="gpd-remaining-days">—</b></span>
      </div>
      <div class="gpd-divider"></div>
      <div class="gpd-bar-labels gpd-date-labels">
        <span>Start Date: <b id="gpd-start-date">—</b></span>
        <span>Work Completed: <b id="gpd-pct-complete">—</b></span>
      </div>
    </div>
  </div>
</div>

<!-- Project Snapshot hover card — hovering the project name in the Gantt
     window header. Independent of #gpd-tip (own trigger, own data, own
     styling — matches the "Projects" list card look). -->
<div id="gsc-tip">
  <div id="gsc-tip-top"><div id="gsc-tip-name">Project</div><button id="gsc-tip-close" title="Close">&times;</button></div>
  <div id="gsc-tip-body">
    <div id="gsc-tip-loading">Loading&hellip;</div>
    <div id="gsc-tip-content" style="display:none">
      <div class="gsc-row"><label><span class="gsc-swatch" style="background:#ff9800;"></span>Planned Duration</label><span class="gsc-val" id="gsc-planned-dur">—</span></div>
      <div class="gsc-row"><label><span class="gsc-swatch" style="background:#2979ff;"></span>Actual Duration</label><span class="gsc-val" id="gsc-actual-dur">—</span></div>
      <div class="gsc-row"><label><span class="gsc-swatch" style="background:#43a047;"></span>Planned Completion</label><span class="gsc-val" id="gsc-planned-comp">—</span></div>
      <div class="gsc-row"><label><span class="gsc-swatch" style="background:#fb8c00;"></span>Actual Completion</label><span class="gsc-val" id="gsc-actual-comp">—</span></div>
      <div class="gsc-row"><label><span class="gsc-swatch" style="background:#ab47bc;"></span>Delayed</label><span class="gsc-val" id="gsc-status">—</span></div>
      <div class="gsc-row"><label><span class="gsc-swatch" style="background:#00c853;"></span>% Completion</label><span class="gsc-val" id="gsc-pct">—</span></div>
      <div class="gsc-row"><label><span class="gsc-swatch" style="background:#26c6da;"></span>Elapsed Days</label><span class="gsc-val" id="gsc-elapsed">—</span></div>
      <div class="gsc-row"><label><span class="gsc-swatch" style="background:#e53935;"></span>Remaining Days</label><span class="gsc-val" id="gsc-remaining">—</span></div>
    </div>
  </div>
</div>

<script type="text/javascript">
(function($) {
  var criticalid = [];
  var projectId  = <?= (int)$projectId ?>;
  var projectName = '<?= addslashes(isset($project) && $project ? $project->Name : 'Project') ?>';
  var _base = '<?= Yii::$app->urlManager->createUrl("projectsmain/index") ?>'.replace(/\/index$/, '');

  // ---- Date helpers ---------------------------------------------------------

  function addOneDay(dateStr) {
    var d = new Date(dateStr);
    d.setDate(d.getDate() + 1);
    var y = d.getFullYear();
    var m = ('0' + (d.getMonth() + 1)).slice(-2);
    var dd = ('0' + d.getDate()).slice(-2);
    return y + '-' + m + '-' + dd;
  }

  function safeDate(val) {
    return (!val || val === '0000-00-00') ? '' : val;
  }

  function addDays(dateStr, days) {
    var d = new Date(dateStr);
    d.setDate(d.getDate() + days);
    return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
  }

  var todayStr = (function(){
    var d = new Date();
    return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
  })();

  function spanDays(s, e) {
    return (s && e) ? Math.round((new Date(e) - new Date(s)) / 86400000) + 1 : null;
  }

  function formatGanttDate(isoStr) {
    if (!isoStr) return '';
    var p = isoStr.split('-');
    if (p.length !== 3) return isoStr;
    return p[2] + '/' + p[1] + '/' + p[0];
  }

  function formatGanttDur(days) {
    if (days === null || days === undefined || days === '') return '';
    var n = parseInt(days, 10);
    return n + ' ' + (n === 1 ? 'dy' : 'dys');
  }

  // ---- Critical path --------------------------------------------------------

  function refreshCriticalPath(callback) {
    $.ajax({
      type: 'POST',
      url: _base + '/ganttlastactivity',
      dataType: 'json',
      async: false,
      data: { projectid: projectId },
      success: function(data) {
        if (!data.id) { if (callback) callback(); return; }
        $.ajax({
          type: 'POST',
          url: _base + '/getcriticalpath',
          dataType: 'json',
          async: false,
          data: { activityid: data.id },
          success: function(cpm) {
            criticalid = cpm.criticalIDs || [];
            if (callback) callback();
          },
          error: function() { if (callback) callback(); }
        });
      },
      error: function() { if (callback) callback(); }
    });
  }

  // ---- Gantt loader ---------------------------------------------------------

  // Exception-safe wrapper: a JS error mid-build must never leave the chart blank
  window.loadGantt = function loadGantt() {
    try {
      loadGanttInner();
    } catch (err) {
      console.error('Gantt draw failed, retrying', err);
      try {
        loadGanttInner();
      } catch (err2) {
        console.error('Gantt redraw retry failed', err2);
        $('#gantt-status').text('Could not draw the chart — please reload the page.');
      }
    }
  }

  function loadGanttInner() {
    $('#gantt-container').empty();

    var g = new JSGantt.GanttChart(document.getElementById('gantt-container'), 'week');
    if (!g.getDivId()) return;

    g.setUseToolTip(0);
    g.setUseSort(0);
    g.setCaptionType('None');
    g.setShowDur(1);
    g.setShowStartDate(0);
    g.setShowEndDate(0);
    g.setShowPlanStartDate(0);
    g.setShowPlanEndDate(0);
    g.setShowComp(0);
    g.setShowRes(1);
    g.setShowDeps(1);
    g.setFormatArr('Day', 'Week', 'Month');
    g.setDayMajorDateDisplayFormat('mon yyyy - Week ww');
    g.setWeekMinorDateDisplayFormat('dd mon');
    g.setUseSingleCell(10000);
    // Columns: B.Duration, A.Duration — Status/KPI/Cost injected via DOM after Draw()
    g.vColumnOrder = ['vShowDur', 'vShowRes'];

    // Root project row — pID = projectId (never 0; JSGantt reserves 0 as virtual root)
    var _projTi = new JSGantt.TaskItem(
      projectId, projectName, '', '', 'ggroupblack', '', 0, '', 0, 1, 0, 1, '', '', '', g
    );
    var _projHid = _projTi.getID();
    g.AddTaskItem(_projTi);

    // Map from hashed task ID → status for dot column
    var _actStatus = {};
    // Map from hashed task ID → raw DB values, used after Draw() to patch B. columns
    var _actCells = {};
    window._ganttActCells = _actCells;
    // A. Duration/Start/End for group rows (WBS items and IOW groups) — separate to avoid clearing B. columns
    var _groupActDur   = {};
    var _groupActStart = {};
    var _groupActEnd   = {};
    // B. Duration/Start/End for group rows — patched explicitly since bar may use A. dates
    var _groupBDur   = {};
    var _groupBStart = {};
    var _groupBEnd   = {};
    var iowActDates  = {};
    var iowBDates    = {};
    var iowHashedIds = {};
    // Project row: min/max dates across all WBS items; duration = span of those dates
    var _projBStartMin = null, _projBEndMax = null;
    var _projAStartMin = null, _projAEndMax = null;

    // Load WBS items synchronously
    $.ajax({
      type: 'POST',
      url: _base + '/ganttitems',
      dataType: 'json',
      async: false,
      data: { projectid: projectId },
      success: function(data) {
        if (!data.result) return;
        var iowGroups = {};
        for (var i = 0; i < data.result.length; i++) {
          var item = data.result[i];

          // IOW Group tier
          var wbsParent;
          if (item.iowGroupid) {
            var iowPid = 'iow_' + item.iowGroupid;
            if (!iowGroups[item.iowGroupid]) {
              var _iowTi = new JSGantt.TaskItem(
                iowPid, item.iow_group_name,
                '', '', 'ggroupblack', '', 0, '', 0, 1, projectId, 1, '', '', '', g
              );
              iowHashedIds[item.iowGroupid] = _iowTi.getID();
              g.AddTaskItem(_iowTi);
              iowGroups[item.iowGroupid] = true;
            }
            wbsParent = iowPid;
          } else {
            wbsParent = projectId;
          }

          // WBS Item tier — capture TaskItem to get hashed ID for A. Duration patching
          var _wbsADur   = (item.a_duration !== null && item.a_duration !== undefined) ? item.a_duration : null;
          var _wbsAStart = safeDate(item.a_min_start) || null;
          var _wbsAEnd   = (_wbsAStart && _wbsADur !== null) ? addDays(_wbsAStart, _wbsADur - 1) : null;
          // WBS bar uses A. dates when available, falls back to B. dates
          var _wbsBarS = _wbsAStart || safeDate(item.start_date);
          var _wbsBarE = _wbsAEnd ? addOneDay(_wbsAEnd) : (item.end_date ? addOneDay(item.end_date) : '');
          var _wbsTi = new JSGantt.TaskItem(
            item.scheduleitem_id,
            item.name,
            _wbsBarS, _wbsBarE,
            'ggroupblack', '', 0, '', 0, 1, wbsParent, 1, '', '', '', g
          );
          var _wbsHid = _wbsTi.getID();
          // B. duration span = span of child schedule dates
          var _wbsBDur = (item.start_date && item.end_date)
              ? Math.round((new Date(item.end_date) - new Date(item.start_date)) / 86400000) + 1
              : null;
          _groupActDur  [_wbsHid] = _wbsADur   !== null ? _wbsADur   : _wbsBDur;
          _groupActStart[_wbsHid] = _wbsAStart          || safeDate(item.start_date);
          _groupActEnd  [_wbsHid] = _wbsAEnd            || safeDate(item.end_date);
          // B. columns always show schedule values regardless of bar position
          _groupBDur  [_wbsHid] = _wbsBDur;
          _groupBStart[_wbsHid] = safeDate(item.start_date);
          _groupBEnd  [_wbsHid] = safeDate(item.end_date);
          // Accumulate min/max dates into project totals
          if (_wbsBDur !== null) {
            var _pbs = safeDate(item.start_date), _pbe = safeDate(item.end_date);
            if (_pbs && (!_projBStartMin || _pbs < _projBStartMin)) _projBStartMin = _pbs;
            if (_pbe && (!_projBEndMax   || _pbe > _projBEndMax  )) _projBEndMax   = _pbe;
          }
          var _wbsADurEff = _wbsADur !== null ? _wbsADur : _wbsBDur;
          if (_wbsADurEff !== null) {
            var _pas = _wbsAStart || safeDate(item.start_date);
            var _pae = _wbsAEnd   || safeDate(item.end_date);
            if (_pas && (!_projAStartMin || _pas < _projAStartMin)) _projAStartMin = _pas;
            if (_pae && (!_projAEndMax   || _pae > _projAEndMax  )) _projAEndMax   = _pae;
          }
          g.AddTaskItem(_wbsTi);
          // Accumulate IOW group min/max dates; group duration = span of these
          if (item.iowGroupid) {
            var _iod = iowActDates[item.iowGroupid];
            if (!_iod) { _iod = { minStart: null, maxEnd: null }; iowActDates[item.iowGroupid] = _iod; }
            if (item.a_min_start && (!_iod.minStart || item.a_min_start < _iod.minStart)) _iod.minStart = item.a_min_start;
            if (item.a_max_end   && (!_iod.maxEnd   || item.a_max_end   > _iod.maxEnd  )) _iod.maxEnd   = item.a_max_end;

            var _ibd = iowBDates[item.iowGroupid];
            if (!_ibd) { _ibd = { minStart: null, maxEnd: null }; iowBDates[item.iowGroupid] = _ibd; }
            var _bS = safeDate(item.start_date), _bE = safeDate(item.end_date);
            if (_bS && (!_ibd.minStart || _bS < _ibd.minStart)) _ibd.minStart = _bS;
            if (_bE && (!_ibd.maxEnd   || _bE > _ibd.maxEnd  )) _ibd.maxEnd   = _bE;
          }

          // Load activities for this WBS item synchronously
          $.ajax({
            type: 'POST',
            url: _base + '/ganttactivities',
            dataType: 'json',
            async: false,
            data: { itemId: item.scheduleitem_id },
            success: (function(parentId) {
              return function(actData) {
                if (!actData.result) return;
                for (var j = 0; j < actData.result.length; j++) {
                  var act = actData.result[j];
                  if (!safeDate(act.actual_start_date) || !safeDate(act.actual_end_date)) continue;

                  var pId       = act.id + 'ABC' + act.id;
                  var isCritical = ($.inArray(Number(act.id), criticalid) !== -1);
                  // Completed overrides critical for the BAR colour — once
                  // done, the bar shows green regardless of whether it sat
                  // on the critical path. The status dot below keeps its
                  // own separate priority (critical still wins there), so
                  // isCritical is tracked independently of pClass.
                  var pClass = (parseInt(act.completed_status) === 1) ? 'gtaskgreen'
                               : isCritical ? 'gtaskpink' : 'gtaskblue';

                  var plannedStart = safeDate(act.actual_start_date);
                  var plannedEnd   = safeDate(act.actual_end_date);
                  // B. Duration always matches Planned Start/End exactly.
                  var bDur = (plannedStart && plannedEnd)
                    ? Math.round((new Date(plannedEnd) - new Date(plannedStart)) / 86400000) + 1
                    : act.old_duration;

                  var hasProgress = (act.actual_duration !== null && act.actual_duration !== undefined);
                  var aStart, aEndComputed, actDur;

                  if (!hasProgress) {
                    // No progress reported yet: Actual = Planned, shifted by the start
                    // delay (days between planned start and today, if the planned
                    // start has already passed). Duration itself doesn't change.
                    aStart = plannedStart;
                    var startDelayDays = (plannedStart && plannedStart < todayStr)
                      ? Math.round((new Date(todayStr) - new Date(plannedStart)) / 86400000) : 0;
                    aEndComputed = (plannedEnd && startDelayDays > 0) ? addDays(plannedEnd, startDelayDays) : plannedEnd;
                    actDur = bDur + startDelayDays;
                  } else {
                    // Progress has been reported: A. Start is the real reported start,
                    // A. Duration/End follow the productivity projection (elapsed ÷ qty
                    // done × total qty), floored at the last report date so a rounded
                    // projection can never undo real reported progress.
                    aStart = safeDate(act.spr_start_date) || plannedStart;
                    aEndComputed = aStart ? addDays(aStart, act.actual_duration - 1) : null;
                    var lastReportDate = safeDate(act.spr_end_date);
                    if (lastReportDate && aEndComputed && lastReportDate > aEndComputed) {
                      aEndComputed = lastReportDate;
                    }
                    actDur = (aStart && aEndComputed)
                      ? Math.round((new Date(aEndComputed) - new Date(aStart)) / 86400000) + 1
                      : act.actual_duration;
                  }

                  // Bar position uses A. dates when available, falls back to B. dates
                  var barStart = aStart       || act.actual_start_date;
                  var barEnd   = aEndComputed ? addOneDay(aEndComputed) : addOneDay(act.actual_end_date);

                  var _ti = new JSGantt.TaskItem(
                    pId, act.name,
                    barStart, barEnd,
                    pClass, '', 0, actDur, 0, 0,
                    parentId, 1,
                    act.depends || null,
                    '', '', g, null,
                    null, null
                  );
                  // Status dot data
                  _actStatus[_ti.getID()] = {
                    completed:   (parseInt(act.completed_status) === 1),
                    hasProgress: (parseFloat(act.cumulated_qty) > 0),
                    planStart:   safeDate(act.actual_start_date),
                    critical:    isCritical
                  };
                  // B. columns show schedule values; A. columns fall back to B. when no progress
                  _actCells[_ti.getID()] = {
                    dur:    bDur,
                    start:  act.actual_start_date,
                    end:    act.actual_end_date,
                    actdur: actDur,
                    astart: aStart || act.actual_start_date,
                    aend:   aEndComputed || act.actual_end_date,
                    scheduleitemId: parentId,
                    rawId:  act.id,
                    wanId:  act.wan_id,
                    hasProgress: hasProgress
                  };
                  g.AddTaskItem(_ti);
                }
              };
            })(item.scheduleitem_id)
          });
        }
      }
    });

    // Store project row totals (duration = span of earliest start to latest end)
    if (_projBStartMin && _projBEndMax) {
      _groupBDur  [_projHid] = spanDays(_projBStartMin, _projBEndMax);
      _groupBStart[_projHid] = _projBStartMin;
      _groupBEnd  [_projHid] = _projBEndMax;
    }
    if (_projAStartMin && _projAEndMax) {
      _groupActDur  [_projHid] = spanDays(_projAStartMin, _projAEndMax);
      _groupActStart[_projHid] = _projAStartMin;
      _groupActEnd  [_projHid] = _projAEndMax;
    }

    // IOW group: duration = span of earliest child start to latest child end
    for (var _iowId in iowHashedIds) {
      var _iowHid = iowHashedIds[_iowId];
      var _iod = iowActDates[_iowId] || { minStart: null, maxEnd: null };
      var _ibd = iowBDates[_iowId]   || { minStart: null, maxEnd: null };

      // A. columns: span of A. dates, falling back to B. dates
      if (_iod.minStart || _ibd.minStart) {
        var _iaS = _iod.minStart || _ibd.minStart;
        var _iaE = _iod.maxEnd   || _ibd.maxEnd;
        _groupActDur  [_iowHid] = spanDays(_iaS, _iaE);
        _groupActStart[_iowHid] = _iaS;
        _groupActEnd  [_iowHid] = _iaE;
      }
      // B. columns: span of B. dates
      if (_ibd.minStart) {
        _groupBDur  [_iowHid] = spanDays(_ibd.minStart, _ibd.maxEnd);
        _groupBStart[_iowHid] = _ibd.minStart;
        _groupBEnd  [_iowHid] = _ibd.maxEnd;
      }
    }

    g.Draw();

    // Relabel native column headers and inject custom columns
    var _c = document.getElementById('gantt-container');

    // JSGantt puts text directly in <td class="gtaskheading gdur"> — no inner div
    // querySelectorAll gets ALL matches across nested tables; we want the one whose
    // parent row also contains a gtaskname cell (the real header row)
    function _findHdrCell(cls) {
      if (!_c) return null;
      var els = _c.querySelectorAll('.gtaskheading.' + cls);
      for (var i = 0; i < els.length; i++) {
        if (els[i].closest('tr') && els[i].closest('tr').querySelector('.gtaskname')) return els[i];
      }
      return els[0] || null;
    }

    var _durHdr = _findHdrCell('gdur');
    var _resHdr = _findHdrCell('gres');

    if (_durHdr) _durHdr.textContent = 'B. Duration';
    if (_resHdr) _resHdr.textContent = 'A. Duration';

    // Label the activity name column
    var _nameHdrs = _c ? _c.querySelectorAll('.gtaskname') : [];
    for (var _ni = 0; _ni < _nameHdrs.length; _ni++) {
      var _nr = _nameHdrs[_ni].closest('tr');
      if (_nr && _nr.querySelector('.gtaskheading')) {
        _nameHdrs[_ni].textContent = 'Activities';
        _nameHdrs[_ni].style.cssText = 'font-weight:600;font-size:11px;text-align:left;padding-left:4px;';
        break;
      }
    }

    // Apply progressive indentation + patch B. columns and A. columns with formatted values
    var _tasks = g.getList ? g.getList() : [];
    for (var _i = 0; _i < _tasks.length; _i++) {
      var _t = _tasks[_i];
      var _tid = _t.getID();
      var _row = document.getElementById('gantt-container' + 'child_' + _tid);
      if (!_row) continue;

      // Indentation on the activity name cell
      var _cell = _row.querySelector('td.gtaskname');
      if (_cell) {
        var _indent = [0, 4, 18, 32, 46][_t.getLevel()] || 4;
        _cell.style.paddingLeft = _indent + 'px';
      }

      // Status dot goes in its own dedicated column (td.gtasklist), leaf activities only
      var _dotCell = _row.querySelector('td.gtasklist');
      if (_dotCell) {
        var _st = _actStatus[_tid];
        if (_st && !_dotCell.querySelector('.act-dot-inline')) {
          _dotCell.innerHTML = '';
          var _dot = document.createElement('span');
          _dot.className = 'act-dot-inline';
          _dot.style.cssText = 'display:inline-block;width:9px;height:9px;border-radius:50%;vertical-align:middle;';
          if (_st.critical) {
            // Critical path activities: always show critical bar colour (#00ACC1) regardless of status
            _dot.style.background = '#00ACC1';
            _dot.title = 'Critical';
          } else if (_st.completed) {
            _dot.style.background = '#43a047';
            _dot.style.color = '#fff';
            _dot.style.fontSize = '7px';
            _dot.style.lineHeight = '9px';
            _dot.style.textAlign = 'center';
            _dot.title = 'Completed';
            _dot.textContent = '✓';
          } else if (_st.hasProgress) {
            _dot.style.background = '#546e7a';
            _dot.title = 'Ongoing';
          } else if (_st.planStart && _st.planStart < todayStr) {
            _dot.style.background = '#ff2800';
            _dot.title = 'Overdue';
          } else {
            _dot.style.background = '#0d2b6e';
            _dot.title = 'Upcoming';
          }
          _dotCell.appendChild(_dot);
        }
      }

      // Leaf activity: patch B. Duration and A. Duration
      var _db = _actCells[_tid];
      if (_db) {
        // Drag handle for reordering within this activity's WBS item —
        // "press and hold, then move" like the KPI/Cost floating panels'
        // own drag (raw mousedown/mousemove/mouseup), not jQuery UI
        // sortable, which never engaged reliably against JSGantt's row
        // structure. See _initActivityDrag below.
        if (_dotCell && !_dotCell.querySelector('.gact-reorder-handle')) {
          var _handle = document.createElement('span');
          _handle.className = 'gact-reorder-handle';
          _handle.title = 'Press and drag to reorder';
          _handle.textContent = '⋮⋮';
          _handle.setAttribute('data-tid', _tid);
          _dotCell.appendChild(_handle);
        }

        var _durEl = _row.querySelector('td.gdur div');
        if (_durEl) _durEl.textContent = formatGanttDur(_db.dur);

        var _adEl = _row.querySelector('td.gres div');
        if (_adEl) {
          _adEl.textContent = formatGanttDur(_db.actdur);
          if (_db.actdur && _db.dur && Number(_db.actdur) > Number(_db.dur)) {
            _adEl.style.color = '#ff2800';
            _adEl.style.fontWeight = '600';
          }
        }
      }

      // Group row A. Duration
      if (_groupActDur.hasOwnProperty(_tid)) {
        var _gadEl = _row.querySelector('td.gres div');
        if (_gadEl) _gadEl.textContent = formatGanttDur(_groupActDur[_tid]);
      }

      // Group row B. Duration
      if (_groupBDur.hasOwnProperty(_tid)) {
        var _gbdEl = _row.querySelector('td.gdur div');
        if (_gbdEl) _gbdEl.textContent = formatGanttDur(_groupBDur[_tid]);
      }

    }


    // Delay overlay — orange-red on bars where A.Duration exceeds B.Duration, skip completed
    for (var _tid in _actCells) {
      var _db = _actCells[_tid];
      if (!_db.dur || !_db.actdur || Number(_db.actdur) <= Number(_db.dur)) continue;
      if (_actStatus[_tid] && _actStatus[_tid].completed) continue;

      var _barDiv = document.getElementById('gantt-container' + 'bardiv_' + _tid);
      if (!_barDiv) continue;
      var _barW = parseInt(_barDiv.style.width) || 0;
      if (!_barW) continue;

      var _budgetedPx = Math.round((Number(_db.dur) / Number(_db.actdur)) * _barW);
      var _delayPx    = _barW - _budgetedPx;
      if (_delayPx <= 0) continue;

      var _overlay = document.createElement('div');
      _overlay.style.cssText = 'position:absolute;left:' + _budgetedPx + 'px;width:' + _delayPx + 'px;top:1px;height:13px;background:rgba(255,40,0,0.9);pointer-events:none;z-index:2;border-radius:0 3px 3px 0;';
      _barDiv.appendChild(_overlay);
    }

    // Colour completed activity bars green
    for (var _tid in _actStatus) {
      if (!_actStatus[_tid].completed) continue;
      var _barDiv = document.getElementById('gantt-container' + 'taskbar_' + _tid);
      if (_barDiv) {
        _barDiv.style.background = '#43a047';
        _barDiv.style.borderColor = '#388e3c';
      }
    }

    // ── Kill left-panel horizontal scroll ────────────────────────────────────
    // JSGantt's syncScroll wired: chartBody.scrollLeft → gListLbl.scrollLeft
    //                         and: gtasktablewrapper.scrollLeft → gListLbl.scrollLeft
    // Both make left columns slide out of view when user scrolls the chart or left body.
    // Fix: clone those two elements (strips all JS event listeners), put clones back in DOM,
    // then rewire ONLY vertical scrollTop sync between left body and chart body.
    // Must run AFTER all DOM injection above so clones capture the injected cells.
    (function() {
      var _gc = document.getElementById('gantt-container');
      if (!_gc) return;

      var _listHead  = _gc.querySelector('.glistlbl');           // left header div
      var _listBody  = _gc.querySelector('.gtasktablewrapper');  // left body div
      var _chartBody = _gc.querySelector('.gchartgrid');         // right chart body

      if (!_listHead || !_listBody || !_chartBody) return;

      // Measure the actual content width of the left panel header table before cloning
      var _headerTable = _listHead.querySelector('table');
      var _contentWidth = _headerTable ? _headerTable.scrollWidth : _listHead.scrollWidth;
      // Set .gmainleft to exactly the pixel width needed to show all columns
      var _mainLeft = _gc.querySelector('.gmainleft');
      if (_mainLeft && _contentWidth > 0) {
        _mainLeft.style.flex = '0 0 ' + _contentWidth + 'px';
        _mainLeft.style.width = _contentWidth + 'px';
      }

      // cloneNode(true) copies DOM but NOT addEventListener listeners → JSGantt scroll sync gone
      var _lhClone = _listHead.cloneNode(true);
      var _lbClone = _listBody.cloneNode(true);
      _listHead.parentNode.replaceChild(_lhClone, _listHead);
      _listBody.parentNode.replaceChild(_lbClone, _listBody);

      // Force both to position 0 and keep there
      _lhClone.scrollLeft = 0;
      _lbClone.scrollLeft = 0;

      // Rewire vertical scroll only (scrollTop) between left body ↔ chart body
      var _lock = false;
      _chartBody.addEventListener('scroll', function() {
        if (_lock) return; _lock = true;
        _lbClone.scrollTop = _chartBody.scrollTop;
        _lock = false;
      });
      _lbClone.addEventListener('scroll', function() {
        if (_lock) return; _lock = true;
        _chartBody.scrollTop = _lbClone.scrollTop;
        _lock = false;
      });
    })();

    $('#gantt-status').text('');
  }

  // ── Reorder activities within one WBS item — press-and-hold drag ───────────
  // Same raw mousedown/mousemove/mouseup shape as the KPI/Cost floating
  // panels' own drag (see "Drag + Resize: KPI modal" above) — NOT jQuery UI
  // sortable, which was tried first and never reliably engaged against
  // JSGantt's row structure. Bound once via delegation on document (not
  // per-row), so it keeps working across every loadGantt() reload without
  // needing to re-bind. Reordering only ever changes
  // workgroup_activities_new.sortorder — no dates are touched — and is
  // rejected server-side if any activity in the WBS item has an active
  // relationship (see actionReordertaskswithinwbs).
  (function(){
    var $ghost = $('#gact-drag-ghost');
    var _dragTid = null, _dragScheduleitemId = null, _dragRow = null;
    var _dropRow = null;

    function _rowFromPoint(x, y) {
      var el = document.elementFromPoint(x, y);
      if (!el) return null;
      var row = el.closest('tr');
      if (!row || row.id.indexOf('child_') === -1 || row.id.indexOf('childrow_') !== -1) return null;
      return row;
    }

    $(document).off('mousedown.gactdrag').on('mousedown.gactdrag', '#gantt-container .gact-reorder-handle', function(e) {
      var tid = $(this).attr('data-tid');
      var db = (window._ganttActCells || {})[tid];
      if (!db) return;
      var row = document.getElementById('gantt-container' + 'child_' + tid);
      if (!row) return;

      _dragTid = tid;
      _dragScheduleitemId = db.scheduleitemId;
      _dragRow = row;
      $(this).addClass('gact-dragging');

      var nameCell = row.querySelector('td.gtaskname');
      $ghost.text(nameCell ? nameCell.textContent.trim() : 'Activity')
        .css({ left: (e.clientX + 14) + 'px', top: (e.clientY + 10) + 'px', display: 'block' });

      e.preventDefault();
    });

    $(document).off('mousemove.gactdrag').on('mousemove.gactdrag', function(e) {
      if (!_dragTid) return;
      $ghost.css({ left: (e.clientX + 14) + 'px', top: (e.clientY + 10) + 'px' });

      if (_dropRow) { $(_dropRow).removeClass('gact-drop-target'); _dropRow = null; }
      var cells = window._ganttActCells || {};
      var row = _rowFromPoint(e.clientX, e.clientY);
      if (row && row !== _dragRow) {
        var rowTid = row.id.slice(row.id.indexOf('child_') + 'child_'.length);
        var rowDb = cells[rowTid];
        if (rowDb && rowDb.scheduleitemId === _dragScheduleitemId) {
          _dropRow = row;
          $(row).addClass('gact-drop-target');
        }
      }
    });

    $(document).off('mouseup.gactdrag').on('mouseup.gactdrag', function(e) {
      if (!_dragTid) return;
      $('.gact-reorder-handle.gact-dragging').removeClass('gact-dragging');
      $ghost.css('display', 'none');
      if (_dropRow) $(_dropRow).removeClass('gact-drop-target');

      var dragTid = _dragTid, scheduleitemId = _dragScheduleitemId, dropRow = _dropRow;
      _dragTid = null; _dragScheduleitemId = null; _dragRow = null; _dropRow = null;
      if (!dropRow) return; // released outside a valid same-WBS-item row — no-op

      var cells = window._ganttActCells || {};
      var dropTid = dropRow.id.slice(dropRow.id.indexOf('child_') + 'child_'.length);

      // Build the new order: every activity in this WBS item, in current DOM
      // order, with the dragged one removed and reinserted at the drop row's
      // position.
      var orderedTids = [];
      $('#gantt-container .gtasktablewrapper tr[id*="child_"]').each(function() {
        if (this.id.indexOf('childrow_') !== -1) return;
        var rowTid = this.id.slice(this.id.indexOf('child_') + 'child_'.length);
        var rowDb = cells[rowTid];
        if (rowDb && rowDb.scheduleitemId === scheduleitemId) orderedTids.push(rowTid);
      });
      var fromIdx = orderedTids.indexOf(dragTid);
      var toIdx = orderedTids.indexOf(dropTid);
      if (fromIdx === -1 || toIdx === -1 || fromIdx === toIdx) return;
      orderedTids.splice(fromIdx, 1);
      toIdx = orderedTids.indexOf(dropTid);
      orderedTids.splice(toIdx, 0, dragTid);

      var wanIds = orderedTids.map(function(t){ return cells[t].wanId; });

      $.ajax({
        type: 'POST',
        url: _base + '/reordertaskswithinwbs',
        dataType: 'json',
        data: { scheduleitem_id: scheduleitemId, wan_ids: wanIds },
        success: function(resp) {
          if (!resp || resp.error !== 'No') {
            alert((resp && resp.errortext) || 'Could not reorder — please try again.');
            return;
          }
          if (typeof window.loadGantt === 'function') window.loadGantt();
        },
        error: function() {
          alert('Network error — could not save the new order.');
        }
      });
    });
  })();

  // ── Gantt Cost Popup — wired once at page level (not per-draw) ──────────────
  (function() {
    var _gcmSentinel = document.getElementById('gcm-popup');
    if (!_gcmSentinel || _gcmSentinel.__gcmWired) return;
    _gcmSentinel.__gcmWired = true;

    // ── Helper functions (mirrors of _performancedashboard.js, scoped here) ──
    function _fmtCost(v) {
      if (!v) return '0.00';
      if (v >= 1e7) return (v / 1e7).toFixed(2) + 'Cr';
      if (v >= 1e5) return (v / 1e5).toFixed(2) + 'L';
      if (v >= 1e3) return (v / 1e3).toFixed(2) + 'K';
      return (+v).toFixed(2);
    }
    function _sh(str, n) { str = str || ''; return str.length > n ? str.substring(0, n - 1) + '…' : str; }
    function _fm(v) { v = +v || 0; return Number.isInteger(v) ? v : v.toFixed(1); }
    var _UNIT_ABBR = {
      'numbers':'Nos','number':'Nos','nos':'Nos','each':'Ea',
      'cubic meter':'Cum','cubic meters':'Cum','cubic metre':'Cum','cubic metres':'Cum',
      'square meter':'Sqm','square meters':'Sqm','square metre':'Sqm','square metres':'Sqm',
      'square feet':'Sft','square foot':'Sft',
      'running meter':'Rmt','running metre':'Rmt','running meters':'Rmt','running metres':'Rmt',
      'meter':'m','meters':'m','metre':'m','metres':'m',
      'kilogram':'kg','kilograms':'kg','kgs':'kg',
      'metric ton':'MT','metric tons':'MT','metric tonne':'MT','metric tonnes':'MT',
      'tonne':'MT','tonnes':'MT','ton':'MT','tons':'MT',
      'litre':'L','litres':'L','liter':'L','liters':'L',
      'hours':'hrs','hour':'hr','days':'d','day':'d',
      'man days':'MD','man-days':'MD','mandays':'MD',
      'lump sum':'LS','lumpsum':'LS','percentage':'%','percent':'%'
    };
    function _fmtFull(v) {
      v = +v || 0;
      if (v === 0) return '0';
      if (Number.isInteger(v)) return v.toLocaleString('en-IN');
      return (+v.toFixed(2)).toLocaleString('en-IN');
    }
    function _shu(u) {
      u = (u || '').trim();
      if (!u) return '';
      var key = u.toLowerCase();
      if (_UNIT_ABBR[key]) return _UNIT_ABBR[key];
      var stripped = u.replace(/^(?:no\.?s?|number)\s+of\s+/i, '');
      if (stripped !== u) return _shu(stripped);
      return u.length > 8 ? _sh(u, 8) : u;
    }

    function _gauge(elId, val, maxVal, trackStyle, targetFrac, lbl1, v1, lbl2, v2) {
      var el = document.getElementById(elId);
      if (!el) return;
      val = +val || 0; maxVal = +maxVal || 1;
      var f = Math.max(0, Math.min(1, val / maxVal));
      var cx = 105, cy = 92, r = 76, sw = 14;
      function ptF(frac) {
        var a = Math.PI * (1 - frac);
        return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)];
      }
      function arc(f1, f2, col, cap) {
        if (f2 <= f1) return '';
        cap = cap || 'butt';
        var p1 = ptF(f1), p2 = ptF(f2);
        if ((f2 - f1) >= 1) {
          var pm = ptF(0.5);
          return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + pm[0] + ',' + pm[1] +
                 ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="' + cap + '"/>';
        }
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="' + cap + '"/>';
      }
      var trackSvg = (trackStyle === 'flat') ? arc(0, 1, '#a8d4f5')
        : (trackStyle === 'cost') ? arc(0, 0.5, '#00838f') + arc(0.5, 1, '#FF6D00')
        : (trackStyle === 'qtygreen' || trackStyle === 'qtypercent') ? arc(0, 0.5, '#FF6D00') + arc(0.5, 1, '#27ae60')
        : (trackStyle === 'costgreen' || trackStyle === 'costpercent') ? arc(0, 0.5, '#27ae60') + arc(0.5, 1, '#FF6D00')
        : arc(0, 0.5, '#8B0000') + arc(0.5, 1, '#81C784');
      var nr = r - 15, na = Math.PI * (1 - f);
      var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
      var fillCol = (trackStyle === 'flat') ? '#0d1f6e' : (trackStyle === 'cost' || trackStyle === 'costgreen' || trackStyle === 'qtygreen' || trackStyle === 'costpercent' || trackStyle === 'qtypercent') ? '#1b9e8e' : '#1a3a6b';
      var dotCol  = (trackStyle === 'flat') ? '#a8d4f5' : '#dce3ef';
      var centreVal = (trackStyle === 'costpercent' || trackStyle === 'qtypercent') ? (Math.round(val) + '%') : (trackStyle === 'cost' || trackStyle === 'costgreen') ? _fmtCost(val) : _fm(val);
      var lblSvg = '';
      if (lbl1) lblSvg += '<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">' + lbl1 + ' <tspan font-weight="700">' + v1 + '</tspan></text>';
      if (lbl2) lblSvg += '<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">' + lbl2 + ' <tspan font-weight="700">' + v2 + '</tspan></text>';
      var svg = '<svg width="100%" height="120" viewBox="0 0 210 138" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" style="display:block;overflow:hidden;flex-shrink:0;">'
        + trackSvg
        + ((f > 0 && trackStyle !== 'costgreen' && trackStyle !== 'qtygreen' && trackStyle !== 'costpercent' && trackStyle !== 'qtypercent') ? arc(0, f, fillCol, (trackStyle === 'cost' ? 'butt' : 'round')) : '')
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="' + dotCol + '"/>'
        + '<text x="' + cx + '" y="' + (cy - 18) + '" text-anchor="middle" font-size="22" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">' + centreVal + '</text>'
        + lblSvg
        + '</svg>';
      el.innerHTML += svg;
    }

    // 1. Unit Cost of Resources → gm-cd-ucr — dial: centre = sum(estimated unit
    // cost), needle = sum(actual unit cost, falling back to estimated where no
    // actual exists). Needle sits left of centre (green) when the actual sum is
    // under the estimated sum, right of centre (orange) when over. The dial's
    // full-scale end (needle pinned) represents double the estimated sum.
    var _ucrTipItems = [];
    function _renderUnitCostOfResource(items, actName) {
      var el = document.getElementById('gm-cd-ucr');
      if (!el) return;
      items = items || [];
      _ucrTipItems = items;
      if (!items.length) { el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No resources allocated</div>'; return; }

      var estSum = 0, actSum = 0;
      items.forEach(function(r) {
        var est = +r.rate || 0;
        var hasActual = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
        var act = hasActual ? +r.actual_unit_cost : est;
        estSum += est;
        actSum += act;
      });

      el.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + _sh(actName||'', 40) + '</div>';

      var actPct = estSum > 0 ? (actSum / estSum) * 100 : 0;
      _gauge('gm-cd-ucr', actPct, 200, 'costpercent', 0.5, 'Est', '100%', 'Act', Math.round(actPct) + '%');
    }

    // Shared renderer for both resource-hover tooltips: a vertical bar per
    // resource — grey base up to the lower of Est/Act, then an orange
    // segment on top when actual exceeds estimate, or a green segment when
    // actual is under estimate. `rows` = [{name, est, act, hasActual}, ...],
    // `fmt` formats the value shown above each bar, `title` is the tooltip heading.
    function _renderBarTip(rows, fmt, title, hideLegend) {
      if (!rows.length) return '<div class="gm-bar-tip-empty">No resources allocated</div>';
      var maxVal = 0;
      rows.forEach(function(r) { maxVal = Math.max(maxVal, r.est, r.act); });
      if (maxVal <= 0) maxVal = 1;

      var BAR_H = 90;
      var html = '<div class="gm-bar-tip-hdr">' + title + '</div>'
        + '<div class="gm-bar-bars">';
      rows.forEach(function(r) {
        var over  = r.hasActual && r.act > r.est;
        var under = r.hasActual && r.act < r.est;
        var baseVal = Math.min(r.est, r.act);
        var baseH = Math.round((baseVal / maxVal) * BAR_H);
        var diffH = Math.round((Math.abs(r.act - r.est) / maxVal) * BAR_H);
        var diffCol = over ? '#e8820c' : (under ? '#1b9e8e' : '');
        var diffSeg = (r.hasActual && diffH > 0) ? '<div style="height:' + diffH + 'px;background:' + diffCol + ';width:100%;"></div>' : '';
        var unitLbl = r.unit ? (' ' + r.unit) : '';
        html += '<div class="gm-bar-col">'
          + '<div class="gm-bar-val">' + fmt(r.act) + unitLbl + '</div>'
          + '<div class="gm-bar-stack" style="height:' + BAR_H + 'px;">'
          +   diffSeg
          +   '<div style="height:' + baseH + 'px;background:#64748b;width:100%;"></div>'
          + '</div>'
          + '<div class="gm-bar-lbl" title="' + $('<span>').text(r.name).html() + '">' + _sh($('<span>').text(r.name).html(), 12) + '</div>'
          + '</div>';
      });
      html += '</div>';
      if (!hideLegend) {
        html += '<div class="gm-bar-tip-legend">'
          +   '<span><i style="background:#64748b"></i> Base</span>'
          +   '<span><i style="background:#e8820c"></i> Over Estimate</span>'
          +   '<span><i style="background:#1b9e8e"></i> Under Estimate</span>'
          + '</div>';
      }
      return html;
    }

    // Binds hover/move/leave handlers for a bar-tip panel. `panelSel` is the
    // panel body to hover over, `$tip` its tooltip element, `getRows` lazily
    // builds the [{name,est,act,hasActual}] rows from that panel's current data.
    function _bindBarTip(panelSel, $tip, getRows, fmt, title) {
      $(document).on('mouseenter', panelSel, function(){
        $tip.html(_renderBarTip(getRows(), fmt, title)).addClass('gm-bar-tip-open');
      });
      $(document).on('mousemove', panelSel, function(e){
        if (!$tip.hasClass('gm-bar-tip-open')) return;
        var tw = $tip.outerWidth(), th = $tip.outerHeight();
        var left = Math.min(e.clientX + 14, window.innerWidth  - tw - 8);
        var top  = Math.min(e.clientY + 14, window.innerHeight - th - 8);
        $tip.css({ left: Math.max(8,left) + 'px', top: Math.max(8,top) + 'px' });
      });
      $(document).on('mouseleave', panelSel, function(){
        $tip.removeClass('gm-bar-tip-open');
      });
    }

    _bindBarTip('#gm-cd-ucr', $('#gm-ucr-tip'), function(){
      return _ucrTipItems.map(function(r) {
        var est = +r.rate || 0;
        var hasActual = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
        var act = hasActual ? +r.actual_unit_cost : est;
        var unit = (+r.type_id === 4 && r.task_unit) ? r.task_unit : (r.unit || '');
        return { name: r.name || '', est: est, act: act, hasActual: hasActual, unit: unit ? ('/' + _shu(unit)) : '' };
      });
    }, _fmtFull, 'Est. vs Act. Unit Cost');

    _bindBarTip('#gm-cd-rcon', $('#gm-rcon-tip'), function(){
      return _rconTipItems.map(function(r) {
        var est = +r.planned_consumption || 0;
        var typeId = +r.type_id;
        var isMaterial = [2, 6, 7, 8].indexOf(typeId) >= 0;
        var isSC = typeId === 4;
        var hasActual = (isMaterial && r.indent_raised) || (isSC && r.has_mb);
        var act = hasActual ? (+r.actual_consumption || 0) : est;
        var unit = (isSC && r.task_unit) ? r.task_unit : (r.unit || '');
        return { name: r.name || '', est: est, act: act, hasActual: hasActual, unit: unit ? _shu(unit) : '' };
      });
    }, _fm, 'Est. vs Act. Quantity Consumed');

    _bindBarTip('#gm-cd-rcost', $('#gm-rcost-tip'), function(){
      var schedQty = _rcostTipSchedQty;
      return _rcostTipItems.map(function(r) {
        var estUC = +r.rate || 0;
        var hasActual = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
        var actUC = hasActual ? +r.actual_unit_cost : estUC;
        var est = estUC * (+r.planned_consumption || 0) * schedQty;
        var act = actUC * (+r.actual_consumption  || 0) * schedQty;
        return { name: r.name || '', est: est, act: act, hasActual: hasActual };
      });
    }, _fmtFull, 'Est. vs Act. Cost of Resources');

    // 2. Consumption of Resources → gm-cd-rcon
    // Yield dial: centre = sum(planned consumption), needle = sum(actual
    // consumption, falling back to planned per-resource where no actual
    // exists yet). Same mechanic as the Unit Cost of Resources dial above.
    var _rconTipItems = [];
    function _renderResourceConsumption(items, actName, lastQty, actUnit) {
      var el = document.getElementById('gm-cd-rcon');
      if (!el) return;
      items = items || [];
      _rconTipItems = items;
      if (!items.length) { el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No resources allocated</div>'; return; }

      var estSum = 0, actSum = 0;
      items.forEach(function(r) {
        var est = +r.planned_consumption || 0;
        var typeId = +r.type_id;
        var isMaterial = [2, 6, 7, 8].indexOf(typeId) >= 0;
        var isSC = typeId === 4;
        var hasActual = (isMaterial && r.indent_raised) || (isSC && r.has_mb);
        var act = hasActual ? (+r.actual_consumption || 0) : est;
        estSum += est;
        actSum += act;
      });

      el.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + _sh(actName||'', 40) + '</div>';

      var actPct = estSum > 0 ? (actSum / estSum) * 100 : 0;
      _gauge('gm-cd-rcon', actPct, 200, 'qtypercent', 0.5, 'Est', '100%', 'Act', Math.round(actPct) + '%');
    }

    // 3. Cost of Resources → gm-cd-rcost — dial: centre/needle = sum of all
    // resources' actual cost (rate × consumption × Sch. Qty), estimated sum =
    // dial half-scale. Green when actual cost is under estimate, orange when over.
    var _rcostTipItems = [], _rcostTipSchedQty = 0;
    function _renderResourceCost(items, actName, actUnit, schedQty) {
      var el = document.getElementById('gm-cd-rcost');
      if (!el) return;
      items = items || [];
      schedQty = +schedQty || 0;
      _rcostTipItems = items;
      _rcostTipSchedQty = schedQty;
      if (!items.length) { el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No resources</div>'; return; }

      var estSum = 0, actSum = 0;
      items.forEach(function(r) {
        var estUC = +r.rate || 0;
        var actUC = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined) ? +r.actual_unit_cost : estUC;
        estSum += estUC * (+r.planned_consumption || 0) * schedQty;
        actSum += actUC * (+r.actual_consumption  || 0) * schedQty;
      });

      el.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + _sh(actName||'', 40) + '</div>';

      _gauge('gm-cd-rcost', actSum, estSum * 2, 'costgreen', 0.5, 'Est', _fmtFull(estSum), 'Act', _fmtFull(actSum));
    }

    // 4. Unit Cost of Activity → gm-cd-g5
    function _renderUnitCostOfActivity(items, actName, actUnit, schedQty) {
      items = items || []; schedQty = +schedQty || 0;
      var estTotal = 0, actTotal = 0;
      items.forEach(function(r) {
        var estUC = +r.rate || 0;
        var hasAct = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
        var actUC = hasAct ? +r.actual_unit_cost : estUC;
        estTotal += estUC * (+r.planned_consumption || 0);
        actTotal += actUC * (+r.actual_consumption  || 0);
      });
      var maxVal = estTotal > 0 ? estTotal * 2 : 1;
      var unitLbl = actUnit ? ' /' + actUnit : '';
      var el5 = document.getElementById('gm-cd-g5');
      if (el5) el5.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + _sh(actName||'', 40) + '</div>';
      _gauge('gm-cd-g5', actTotal, maxVal, 'cost', 0.5, 'Est', _fmtFull(estTotal) + unitLbl, 'Act', _fmtFull(actTotal) + unitLbl);
    }

    // 5. Work Done → gm-cd-g4
    function _renderValueOfWorkDone(d) {
      var el = document.getElementById('gm-cd-g4');
      if (!el) return;
      var schedQty = +d.schedule_qty || 0;
      var lastQty  = +d.last_report_qty || 0;
      var unit     = d.unit || '';
      var actName  = d.activity_name || '';
      var maxVal   = schedQty > 0 ? schedQty : 1;
      var cx=105, cy=92, r=76, sw=14;
      function ptF(frac){ var a=Math.PI*(1-frac); return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)]; }
      function arc(f1,f2,col,cap){
        if(f2<=f1) return ''; cap=cap||'butt';
        var p1=ptF(f1),p2=ptF(f2);
        if((f2-f1)>=1){ var pm=ptF(0.5); return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>'; }
        return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
      }
      var f=Math.max(0,Math.min(1,lastQty/maxVal));
      var nr=r-15,na=Math.PI*(1-f);
      var nx=(cx+nr*Math.cos(na)).toFixed(1),ny=(cy-nr*Math.sin(na)).toFixed(1);
      var pct=schedQty>0?(lastQty/schedQty*100).toFixed(1):'0.0';
      var svg='<svg width="100%" height="120" viewBox="0 0 210 138" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" style="display:block;overflow:hidden;flex-shrink:0;">'
        +arc(0,1,'#64748b')+(f>0?arc(0,f,'#94a3b8','butt'):'')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#dce3ef"/>'
        +'<text x="'+cx+'" y="'+(cy-22)+'" text-anchor="middle" font-size="22" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+pct+'%</text>'
        +'<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Done <tspan font-weight="700">'+_fmtFull(lastQty)+(unit?' '+unit:'')+'</tspan></text>'
        +'<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Sched <tspan font-weight="700">'+_fmtFull(schedQty)+(unit?' '+unit:'')+'</tspan></text>'
        +'</svg>';
      el.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">'+_sh(actName,40)+'</div>' + svg;
    }

    // 6. Cost of Activity → gm-cd-g2
    function _renderCostOfActivity(d) {
      var el = document.getElementById('gm-cd-g2');
      if (!el) return;
      var items = d.items || [], schedQty = +d.schedule_qty || 0, actName = d.activity_name || '';
      var estUCTotal = 0, actUCTotal = 0, hasActual = false;
      items.forEach(function(r) {
        var estUC  = +r.rate || 0;
        var estCons = +r.planned_consumption || 0;
        estUCTotal += estUC * estCons;
        var hasAct = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
        var actUC  = hasAct ? +r.actual_unit_cost : estUC;
        var actCons = +r.actual_consumption || 0;
        actUCTotal += actUC * actCons;
        if (hasAct) hasActual = true;
      });
      var estCost = estUCTotal * schedQty;
      var actCost = hasActual ? actUCTotal * schedQty : estCost;
      var diff = actCost - estCost;
      var over = diff > 0;
      var barHtml;
      if (!hasActual || diff === 0) {
        barHtml = '<div style="width:100%;height:14px;background:#64748b;border-radius:3px"></div>';
      } else if (over) {
        var estPct = (estCost / actCost * 100).toFixed(1);
        var diffPct = (diff / actCost * 100).toFixed(1);
        barHtml = '<div style="display:flex;height:14px;border-radius:3px;overflow:hidden;width:100%"><div style="width:'+estPct+'%;background:#64748b;flex-shrink:0"></div><div style="width:'+diffPct+'%;background:#e8820c;flex-shrink:0"></div></div>';
      } else {
        var actPct3 = (actCost / estCost * 100).toFixed(1);
        var savePct3 = (Math.abs(diff) / estCost * 100).toFixed(1);
        barHtml = '<div style="display:flex;height:14px;border-radius:3px;overflow:hidden;width:100%"><div style="width:'+actPct3+'%;background:#64748b;flex-shrink:0"></div><div style="width:'+savePct3+'%;background:#1b9e8e;flex-shrink:0"></div></div>';
      }
      var diffLabel = hasActual && diff !== 0 ? (over?'+':'-') + _fmtFull(Math.abs(diff)) + ' ' + (over?'over':'saving') : '';
      var diffCol = over ? '#e8820c' : '#1b9e8e';
      var workDone = +d.last_report_qty || 0;
      var estCostWD = estUCTotal * workDone;
      var actCostWD = actUCTotal * workDone;
      var diffWD = actCostWD - estCostWD;
      var overWD = diffWD > 0;
      var diffWDLabel = diffWD !== 0 ? (overWD?'+':'-') + _fmtFull(Math.abs(diffWD)) + ' ' + (overWD?'over':'saving') : '';
      var valRow = '<div style="display:flex;justify-content:space-between;align-items:baseline;font-family:\'Barlow Condensed\',sans-serif;font-size:15px;font-weight:700;color:#111;margin-bottom:6px">'
        +'<span>Est: ' + _fmtFull(estCost) + (hasActual ? '&nbsp;&nbsp;&nbsp;Act: <span style="color:'+(over?'#e8820c':'#1b9e8e')+'">' + _fmtFull(actCost) + '</span>' : '') + '</span>'
        +(diffLabel ? '<span style="color:'+diffCol+'">'+diffLabel+'</span>' : '')
        +'</div>';
      var wdRow = '<div style="font-family:\'Barlow Condensed\',sans-serif;font-size:13px;font-weight:600;color:#444;margin-top:8px;line-height:1.7">'
        +'<div>Estimated Cost of Work Done &nbsp;<span style="color:#111">' + _fmtFull(estCostWD) + '</span></div>'
        +(hasActual
          ? '<div>Actual Cost of Work Done &nbsp;<span style="color:'+(overWD?'#e8820c':'#1b9e8e')+'">' + _fmtFull(actCostWD) + '</span></div>'
            +'<div style="color:'+(overWD?'#e8820c':'#1b9e8e')+'">Difference &nbsp;'+(diffWDLabel||_fmtFull(0))+'</div>'
          : '<div>Actual Cost of Work Done &nbsp;<span style="color:#888;font-style:italic;font-weight:500">No Cost Reported</span></div>'
        )
        +'</div>';
      el.style.overflow = 'auto';
      el.innerHTML = '<div style="padding:4px 6px 8px;width:100%;box-sizing:border-box">'
        +'<div style="font-size:10px;color:#3461b8;font-weight:600;padding-bottom:4px;border-bottom:1px solid #e8efff;margin-bottom:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">'+_sh(actName,40)+'</div>'
        + valRow + barHtml + wdRow
        +'</div>';
    }

    // ── Cost icon hover → floating popup ─────────────────────────────────────
    var _gcpTimer  = null;
    var _gcpXhr    = null;
    var _gcpCache  = {};

    function _positionCostPopup(anchorEl) {
      var pop = document.getElementById('gcm-popup');
      var pw = pop.offsetWidth || 760, ph = pop.offsetHeight || 280;
      var vw = window.innerWidth, vh = window.innerHeight;
      var rect = anchorEl ? anchorEl.getBoundingClientRect() : null;
      var left = rect ? Math.min(rect.right - 70, vw - pw - 8) : vw - pw - 8;
      if (left < 4) left = 4;
      var top = rect ? vh * 0.68 - ph * 0.5 : vh / 2 - ph / 2;
      if (top + ph > vh - 8) top = vh - ph - 8;
      if (top < 4) top = 4;
      pop.style.left = left + 'px';
      pop.style.top  = top  + 'px';
    }

    function _hideCostPopup() {
      clearTimeout(_gcpTimer);
      if (_gcpXhr) { _gcpXhr.abort(); _gcpXhr = null; }
      var pop = document.getElementById('gcm-popup');
      if (pop) { pop.classList.remove('gcm-visible'); pop.style.display = 'none'; }
    }

    // Guarantees the popup is actually laid out and visible even if this
    // AJAX-loaded fragment's own <style> block hasn't taken effect for any
    // reason (e.g. injected into a page that already had conflicting rules) —
    // sets the same properties the .gcm-visible CSS class is meant to apply,
    // directly as inline style, which always wins.
    function _forceShowCostPopup(pop) {
      pop.classList.add('gcm-visible');
      pop.style.display = 'flex';
      if (!pop.style.width)  pop.style.width  = '740px';
      if (!pop.style.height) pop.style.height = '58vh';
      if (!pop.style.position) pop.style.position = 'fixed';
      // #gcm-popup is a true floater (like cd-modal/cf-modal), not a
      // sub-popup — it must get its own live ++_z on every open, not a
      // one-time snapshot (popupSubZBase's _z + 5000 goes stale the moment
      // any other floater is clicked/opened afterward, letting this popup
      // get buried again — the exact bug class this fixes, see KPI/Cost
      // Dashboard/Cashflow all needing to stay visible simultaneously).
      if (typeof window.popupBringToFront === 'function') { window.popupBringToFront('gcm-popup'); }
      else { pop.style.zIndex = 10015; }
    }

    function _showCostPopup(actId, anchorEl) {
      var pop = document.getElementById('gcm-popup');
      if (!pop) return;

      function _doRender(d) {
        document.getElementById('gcm-pop-hdr-left').textContent = 'Cost Dashboard — ' + (d.activity_name || '');
        ['gm-cd-rcost','gm-cd-ucr','gm-cd-rcon','gm-cd-g5','gm-cd-g4','gm-cd-g2'].forEach(function(id) {
          var el = document.getElementById(id); if (el) el.innerHTML = '';
        });
        document.getElementById('gcm-pop-loading').style.display = 'none';
        document.getElementById('gcm-pop-row1').style.display = 'grid';
        _renderResourceCost(d.items, d.activity_name, d.unit, d.schedule_qty);
        _renderUnitCostOfResource(d.items, d.activity_name);
        _renderResourceConsumption(d.items, d.activity_name, d.last_report_qty, d.unit);
        _renderUnitCostOfActivity(d.items, d.activity_name, d.unit, d.schedule_qty);
        _renderValueOfWorkDone(d);
        _renderCostOfActivity(d);
        _positionCostPopup(anchorEl);
      }

      if (_gcpCache[actId]) {
        document.getElementById('gcm-pop-loading').style.display = 'none';
        _forceShowCostPopup(pop);
        _positionCostPopup(anchorEl);
        _doRender(_gcpCache[actId]);
        return;
      }

      // Show loading state
      document.getElementById('gcm-pop-hdr-left').textContent = 'Cost Dashboard — Loading…';
      document.getElementById('gcm-pop-loading').style.display = 'block';
      document.getElementById('gcm-pop-row1').style.display = 'none';
      _forceShowCostPopup(pop);
      _positionCostPopup(anchorEl);

      if (_gcpXhr) _gcpXhr.abort();
      _gcpXhr = $.ajax({
        type: 'POST', url: _base + '/costdashboardactivity',
        data: { actid: actId }, dataType: 'json',
        success: function(d) {
          _gcpXhr = null;
          if (!d || d.error) {
            document.getElementById('gcm-pop-loading').textContent = d && d.error ? d.error : 'Error loading data';
            return;
          }
          _gcpCache[actId] = d;
          _doRender(d);
        },
        error: function(x, s) { _gcpXhr = null; if (s !== 'abort') _hideCostPopup(); }
      });
    }

    // The Cost Dashboard is intentionally not triggerable from anywhere in
    // the Gantt chart itself (its own gcol-cost trigger cell was removed) —
    // it is only ever opened from the Project Estimate report, which calls
    // this exposed function directly for a given scheduleactivities id.
    window.showCostDashboardPopup = _showCostPopup;

    $('#gcm-pop-close').on('click', function() {
      _hideCostPopup();
    });

    // ── KPI icon click → handled inside KPI IIFE where _showKpiForAct is in scope ──
    var _gkmCache = {};
    $(document).on('click', '#gantt-container .gcol-kpi span[data-kpiactid]', function(e) {
      e.stopPropagation();
      return; // handled below inside KPI IIFE
      $('#gkm-loading').show();
      $('#gkm-content').hide();
      $('#gkm-title').text('KPI Dashboard — Loading…');
      $('#gkm-bk, #gkm-modal').addClass('gkm-open');

      function _renderIntoModal(k) {
        $('#gkm-title').text('KPI Dashboard — ' + (k.activity_name || ''));
        // Capacity
        (function(){
          var el=document.getElementById('gkm-g5'); if(!el)return;
          var maxV=+k.cap_max||0,used=+k.cap_used||0,f=maxV>0?used/maxV:0,pct=maxV>0?((used/maxV)*100).toFixed(1):'0';
          var cx=105,cy=92,r=76,sw=14;
          function ptF(fr){var a=Math.PI*(1-fr);return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)];}
          function arc(f1,f2,col){if(f2<=f1)return'';var p1=ptF(f1),p2=ptF(f2);if((f2-f1)>=1){var pm=ptF(0.5);return'<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="butt"/>';}return'<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="butt"/>';}
          var nr=r-15,na=Math.PI*(1-f),nx=(cx+nr*Math.cos(na)).toFixed(1),ny=(cy-nr*Math.sin(na)).toFixed(1);
          el.innerHTML='<svg width="100%" viewBox="0 -12 210 150" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet" style="display:block;width:100%;height:auto;">'
            +arc(0,1,'#FFD700')+(f>0?arc(0,f,'#90EE90','butt'):'')
            +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
            +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/><circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#FFD700"/>'
            +'<text x="105" y="62" text-anchor="middle" font-size="22" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+pct+'%</text>'
            +'<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Used <tspan font-weight="700">'+_fmtFull(used)+' h</tspan></text>'
            +'<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Max <tspan font-weight="700">'+_fmtFull(maxV)+' h</tspan></text>'
            +'</svg>';
        })();
        // Cycle Time
        (function(){
          var el=document.getElementById('gkm-g4'); if(!el)return;
          var tc=+k.target_cycle_time||0,ac=+k.actual_cycle_time>0?+k.actual_cycle_time:tc;
          var maxV=tc>0?tc*2:1,f=ac/maxV,acCol=ac>tc?'#e53935':ac<tc?'#27ae60':'#1a2540';
          var cx=105,cy=92,r=76,sw=14;
          function ptF(fr){var a=Math.PI*(1-fr);return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)];}
          function arc(f1,f2,col){if(f2<=f1)return'';var p1=ptF(f1),p2=ptF(f2);return'<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="butt"/>';}
          var nr=r-15,na=Math.PI*(1-f),nx=(cx+nr*Math.cos(na)).toFixed(1),ny=(cy-nr*Math.sin(na)).toFixed(1);
          el.innerHTML='<svg width="100%" viewBox="0 -12 210 150" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet" style="display:block;width:100%;height:auto;">'
            +arc(0,0.5,'#00838f')+arc(0.5,1,'#FF6D00')
            +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
            +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/><circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#dce3ef"/>'
            +'<text x="105" y="40" text-anchor="middle" font-size="10" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Target</text>'
            +'<text x="105" y="53" text-anchor="middle" font-size="15" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+_fmtFull(tc)+' Hrs</text>'
            +'<text x="105" y="66" text-anchor="middle" font-size="10" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Actual</text>'
            +'<text x="105" y="79" text-anchor="middle" font-size="15" font-weight="700" fill="'+acCol+'" font-family="Barlow Condensed,Arial">'+_fmtFull(ac)+' Hrs</text>'
            +'<text x="8" y="112" text-anchor="start" font-size="11" fill="#111" font-family="Barlow Condensed,Arial">Fast</text>'
            +'<text x="202" y="112" text-anchor="end" font-size="11" fill="#111" font-family="Barlow Condensed,Arial">Slow</text>'
            +'</svg>';
        })();
        // Productivity
        (function(){
          var el=document.getElementById('gkm-g3'); if(!el)return;
          var tp=+k.target_productivity||0,ap=+k.actual_productivity>0?+k.actual_productivity:tp;
          var u=k.unit?' '+k.unit:'',maxV=tp>0?tp*2:1,f=ap/maxV,apCol=ap<tp?'#e53935':ap>tp?'#27ae60':'#1a2540';
          var cx=105,cy=92,r=76,sw=14;
          function ptF(fr){var a=Math.PI*(1-fr);return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)];}
          function arc(f1,f2,col){if(f2<=f1)return'';var p1=ptF(f1),p2=ptF(f2);return'<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="butt"/>';}
          var nr=r-15,na=Math.PI*(1-f),nx=(cx+nr*Math.cos(na)).toFixed(1),ny=(cy-nr*Math.sin(na)).toFixed(1);
          el.innerHTML='<svg width="100%" viewBox="0 -12 210 150" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet" style="display:block;width:100%;height:auto;">'
            +arc(0,0.5,'#E65100')+arc(0.5,1,'#00695C')
            +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
            +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/><circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#dce3ef"/>'
            +'<text x="105" y="40" text-anchor="middle" font-size="10" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Target</text>'
            +'<text x="105" y="53" text-anchor="middle" font-size="15" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+_fmtFull(tp)+u+'/d</text>'
            +'<text x="105" y="66" text-anchor="middle" font-size="10" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Actual</text>'
            +'<text x="105" y="79" text-anchor="middle" font-size="15" font-weight="700" fill="'+apCol+'" font-family="Barlow Condensed,Arial">'+_fmtFull(ap)+u+'/d</text>'
            +'<text x="8" y="112" text-anchor="start" font-size="11" fill="#111" font-family="Barlow Condensed,Arial">Low</text>'
            +'<text x="202" y="112" text-anchor="end" font-size="11" fill="#111" font-family="Barlow Condensed,Arial">High</text>'
            +'</svg>';
        })();
        // Target Production — dial (same as KPI Dashboard)
        (function(){
          var el=document.getElementById('gkm-tp'); if(!el)return;
          var tq=+k.target_qty||0,aq=+k.actual_qty||0,dur=+k.b_duration||0,u=k.unit?' '+k.unit:'';
          var asd=k.act_start_date||'',lrd=k.last_reported_date||'';
          var elDays=(asd&&lrd)?Math.max(0,Math.round((new Date(lrd)-new Date(asd))/86400000)+1):0;
          var compTarget=(dur>0&&tq>0)?Math.min(tq,elDays*(tq/dur)):0;
          var fAct=tq>0?Math.max(0,Math.min(1,aq/tq)):0;
          var fTgt=tq>0?Math.max(0,Math.min(1,compTarget/tq)):0;
          var actCol=aq<compTarget?'#e53935':aq>compTarget?'#27ae60':'#1a2540';
          var cx=105,cy=92,r=76,sw=14;
          function ptF(fr){var a=Math.PI*(1-fr);return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)];}
          function arcD(f1,f2,col,cap){if(f2<=f1)return'';cap=cap||'butt';var p1=ptF(f1),p2=ptF(f2);if((f2-f1)>=1){var pm=ptF(0.5);return'<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';}return'<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';}
          var nr=r-15,na=Math.PI*(1-fTgt),nx=(cx+nr*Math.cos(na)).toFixed(1),ny=(cy-nr*Math.sin(na)).toFixed(1);
          el.innerHTML='<svg width="100%" viewBox="0 -12 210 146" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet" style="display:block;width:100%;height:auto;">'
            +arcD(0,1,'#a8aeb8')+(fAct>0?arcD(0,fAct,'#0d1f6e','butt'):'')
            +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
            +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/><circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#a8aeb8"/>'
            +(tq>0?'<text x="105" y="40" text-anchor="middle" font-size="10" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Target to Date</text>':'')
            +(tq>0?'<text x="105" y="53" text-anchor="middle" font-size="15" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+_fmtFull(compTarget)+u+'</text>':'')
            +'<text x="105" y="66" text-anchor="middle" font-size="10" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Actual</text>'
            +'<text x="105" y="79" text-anchor="middle" font-size="15" font-weight="700" fill="'+actCol+'" font-family="Barlow Condensed,Arial">'+_fmtFull(aq)+u+'</text>'
            +'<text x="8" y="112" text-anchor="start" font-size="11" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Actual</text>'
            +'<text x="8" y="123" text-anchor="start" font-size="12" font-weight="700" fill="'+actCol+'" font-family="Barlow Condensed,Arial">'+_fmtFull(aq)+u+'</text>'
            +'<text x="202" y="112" text-anchor="end" font-size="11" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Schedule Qty</text>'
            +'<text x="202" y="123" text-anchor="end" font-size="12" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+_fmtFull(tq)+u+'</text>'
            +'</svg>';
        })();
        // Activity Duration
        (function(){
          var el=document.getElementById('gkm-dur'); if(!el)return;
          var bDur=+k.b_duration||+k.duration||0,aDur=+k.projected_duration||0;
          var elapsed=+k.elapsed||0,startDelay=+k.start_delay||0;
          if(!aDur)aDur=bDur; if(!bDur){el.innerHTML='<div style="text-align:center;padding:20px;color:#aaa;font-size:12px;">No duration data</div>';return;}
          var maxDur=Math.max(bDur,aDur,1),isOver=aDur>bDur,isUnder=bDur>aDur;
          var baseCol=k.critical?'#00838f':'#37474F';
          var fam="font-family:'Barlow Condensed',sans-serif;";
          var bar='<div style="position:relative;display:flex;align-items:stretch;height:14px;border-radius:3px;overflow:hidden;margin:8px 0;">';
          if(isOver){bar+='<div style="width:'+(bDur/maxDur*100).toFixed(1)+'%;background:'+baseCol+';min-width:3px;"></div><div style="width:'+((aDur-bDur)/maxDur*100).toFixed(1)+'%;background:#e53935;min-width:3px;"></div>';}
          else if(isUnder){bar+='<div style="width:'+(aDur/maxDur*100).toFixed(1)+'%;background:'+baseCol+';min-width:3px;"></div><div style="width:'+((bDur-aDur)/maxDur*100).toFixed(1)+'%;background:#f0c419;min-width:3px;"></div>';}
          else{bar+='<div style="width:100%;background:'+baseCol+';"></div>';}
          bar+='</div>';
          var elapsedDays=elapsed;
          if(k.act_start_date&&k.act_start_date!=='0000-00-00'){var td2=new Date();td2.setHours(0,0,0,0);var sd2=new Date(k.act_start_date);sd2.setHours(0,0,0,0);elapsedDays=Math.max(0,Math.round((td2-sd2)/86400000)+1);}
          function fmD(s){if(!s||s==='0000-00-00')return'—';var p=s.split('-');return p[2]+'-'+p[1]+'-'+p[0];}
          el.innerHTML='<div style="padding:10px 14px;'+fam+'font-size:12px;box-sizing:border-box;">'
            +'<div style="display:flex;justify-content:space-between;align-items:baseline;font-weight:700;color:#1a2540;">'
            +'<span>'+bDur+'d Planned'+(isOver?' <span style="color:#e53935;">+' +(aDur-bDur)+'d</span>':isUnder?' <span style="color:#27ae60;">−'+(bDur-aDur)+'d</span>':'')+' </span>'
            +(startDelay>0&&!isOver?'<span style="color:#E65100;">Start +'+startDelay+'d</span>':'')+'</div>'
            +bar
            +'<div style="display:flex;justify-content:space-between;color:#5a6e8c;font-size:11px;">'
            +'<span>Act. Start: <b style="color:#1a2540;">'+fmD(k.act_start_date)+'</b></span>'
            +'<span>Elapsed: <b style="color:#1a2540;">'+elapsedDays+'d</b></span>'
            +'<span>Plan End: <b style="color:#1a2540;">'+fmD(k.adj_end_date)+'</b></span>'
            +'</div>'
            +'</div>';
        })();
        $('#gkm-loading').hide();
        $('#gkm-content').show();
      }

      if (_gkmCache[actId]) {
        _renderIntoModal(_gkmCache[actId]);
        return;
      }
      $.ajax({
        type: 'POST', url: _base + '/performancedashboardkpi',
        data: { actid: actId }, dataType: 'json',
        success: function(d) {
          if (!d || !d.kpi) { $('#gkm-loading').text('No KPI data.'); return; }
          _gkmCache[actId] = d.kpi;
          _renderIntoModal(d.kpi);
        },
        error: function() { $('#gkm-loading').text('Failed to load KPI data.'); }
      });
    });

    // ── Close KPI modal ────────────────────────────────────────────────────────
    $('#gkm-close').on('click', function() {
      $('#gkm-bk, #gkm-modal').removeClass('gkm-open');
    });

    // ── Drag + Resize: KPI modal ───────────────────────────────────────────────
    (function(){
      var win = document.getElementById('gkm-modal');
      var hdr = document.getElementById('gkm-header');
      // Join the app-wide floater stacking authority — clicking this panel
      // brings it above every other floating window, in actual click order,
      // same as Resource Library/Vendor Library/etc. already do.
      if(typeof window.bindDragTouch === 'function' && typeof window.popupBringToFront === 'function'){
        window.bindDragTouch(win, 'mousedown', function(){ window.popupBringToFront('gkm-modal'); }, true);
      }
      var MIN_W=420, MIN_H=280, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0;
      function _anchor(){ var r=win.getBoundingClientRect(); win.style.left=r.left+'px'; win.style.top=r.top+'px'; win.style.width=r.width+'px'; win.style.height=r.height+'px'; return r; }
      hdr.addEventListener('mousedown', function(e){ if(e.target.id==='gkm-close') return; var r=_anchor(); _action='drag'; _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault(); });
      document.querySelectorAll('.gkm-rs').forEach(function(el){ el.addEventListener('mousedown', function(e){ var r=_anchor(); _action=el.getAttribute('data-dir'); _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height; e.preventDefault(); e.stopPropagation(); }); });
      document.addEventListener('mousemove', function(e){ if(!_action) return; var dx=e.clientX-_sx, dy=e.clientY-_sy; if(_action==='drag'){ win.style.left=Math.max(0,_ox+dx)+'px'; win.style.top=Math.max(0,_oy+dy)+'px'; } else { var l=_ox,t=_oy,w=_ow,h=_oh; if(_action.indexOf('e')>-1){w=Math.max(MIN_W,_ow+dx);} if(_action.indexOf('s')>-1){h=Math.max(MIN_H,_oh+dy);} if(_action.indexOf('w')>-1){var nw=Math.max(MIN_W,_ow-dx);l=_ox+(_ow-nw);w=nw;} if(_action.indexOf('n')>-1){var nh=Math.max(MIN_H,_oh-dy);t=_oy+(_oh-nh);h=nh;} win.style.left=l+'px'; win.style.top=t+'px'; win.style.width=w+'px'; win.style.height=h+'px'; } });
      document.addEventListener('mouseup', function(){ _action=null; });
    })();

    // ── Drag + Resize: Cost panel ──────────────────────────────────────────────
    (function(){
      var win = document.getElementById('gcm-popup');
      var hdr = document.getElementById('gcm-pop-hdr');
      // Join the app-wide floater stacking authority — see the KPI modal's
      // identical binding just above for why.
      if(typeof window.bindDragTouch === 'function' && typeof window.popupBringToFront === 'function'){
        window.bindDragTouch(win, 'mousedown', function(){ window.popupBringToFront('gcm-popup'); }, true);
      }
      var MIN_W=420, MIN_H=280, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0;
      function _anchor(){ var r=win.getBoundingClientRect(); win.style.left=r.left+'px'; win.style.top=r.top+'px'; win.style.width=r.width+'px'; win.style.height=r.height+'px'; return r; }
      hdr.addEventListener('mousedown', function(e){ if(e.target.closest('#gcm-pop-close')) return; var r=_anchor(); _action='drag'; _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault(); });
      document.querySelectorAll('.gcm-rs').forEach(function(el){ el.addEventListener('mousedown', function(e){ var r=_anchor(); _action=el.getAttribute('data-dir'); _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height; e.preventDefault(); e.stopPropagation(); }); });
      document.addEventListener('mousemove', function(e){ if(!_action) return; var dx=e.clientX-_sx, dy=e.clientY-_sy; if(_action==='drag'){ win.style.left=Math.max(0,_ox+dx)+'px'; win.style.top=Math.max(0,_oy+dy)+'px'; } else { var l=_ox,t=_oy,w=_ow,h=_oh; if(_action.indexOf('e')>-1){w=Math.max(MIN_W,_ow+dx);} if(_action.indexOf('s')>-1){h=Math.max(MIN_H,_oh+dy);} if(_action.indexOf('w')>-1){var nw=Math.max(MIN_W,_ow-dx);l=_ox+(_ow-nw);w=nw;} if(_action.indexOf('n')>-1){var nh=Math.max(MIN_H,_oh-dy);t=_oy+(_oh-nh);h=nh;} win.style.left=l+'px'; win.style.top=t+'px'; win.style.width=w+'px'; win.style.height=h+'px'; } });
      document.addEventListener('mouseup', function(){ _action=null; });
    })();

    // Whenever #gantt-win itself is brought to front, re-raise gpd-tip (a
    // true small anchored sub-popup) so it stays above it — same guarantee
    // Resource Library gives its own sub-popups. gkp-popup and gcm-popup
    // are NOT included here any more — they're true floaters now (see
    // _raiseKpiPopup / _forceShowCostPopup), each with their own live
    // popupBringToFront() z-index; overwriting that with a stale
    // popupSubZBase() snapshot here would re-bury them behind
    // cd-modal/cf-modal, which is exactly the bug this replaced. gkm-modal
    // is dead/unreachable code (see its own comment above) — left in place
    // only because it can never actually be visible.
    if(typeof window.registerSubPopupReraise === 'function'){
      window.registerSubPopupReraise('gantt-win', function(){
        ['gkm-modal','gpd-tip'].forEach(function(id){
          var el = document.getElementById(id);
          if(el && getComputedStyle(el).display !== 'none'){
            el.style.zIndex = window.popupSubZBase();
          }
        });
      });
    }
  })();

  // ── Project Duration popup — independent of the KPI/Cost dashboards.
  // Clicking the small clock icon next to the project name in the Gantt
  // window header (#btn-project-duration) shows planned vs actual project
  // duration, fetched via the existing performancedashboard endpoint's
  // project_bar payload (no new backend).
  (function(){
    var tip = document.getElementById('gpd-tip');
    if (!tip) return;
    var _cache = null;

    function fmD(s){
      if(!s || s==='0000-00-00') return '—';
      var d = new Date(s); if (isNaN(d)) return '—';
      var months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      return d.getDate()+' '+months[d.getMonth()]+' '+d.getFullYear();
    }

    function render(pb){
      var planned = Math.max(0, +pb.budgeted || 0);
      var diff = Math.max(0, +pb.delay || 0);
      var expectedDur = Math.max(0, +pb.actual || 0);

      document.getElementById('gpd-target-comp').textContent   = planned + ' d';
      document.getElementById('gpd-expected-comp').textContent = expectedDur > 0 ? (expectedDur + ' d') : '—';
      document.getElementById('gpd-target-date').textContent   = fmD(pb.b_end_date);
      document.getElementById('gpd-expected-date').textContent = fmD(pb.proj_end_date || pb.a_end_date);

      // Elapsed Days = planned start date of the first activity till today.
      // Remaining Days = project planned duration - elapsed days.
      var plannedStart = pb.b_start_date || '';
      var elapsedDays = 0;
      if (plannedStart) {
        var sd = new Date(plannedStart); sd.setHours(0,0,0,0);
        var td = new Date(); td.setHours(0,0,0,0);
        elapsedDays = Math.max(0, Math.round((td - sd) / 86400000) + 1);
      }
      var remainingDays = Math.max(0, planned - elapsedDays);
      document.getElementById('gpd-elapsed-days').textContent   = plannedStart ? (elapsedDays + ' d') : '—';
      document.getElementById('gpd-remaining-days').textContent = remainingDays + ' d';

      document.getElementById('gpd-start-date').textContent = fmD(plannedStart);
      document.getElementById('gpd-pct-complete').textContent = (pb.pct_complete != null ? pb.pct_complete : 0) + '%';

      // Delayed/Savings status line above the bar — delay comes from the
      // critical-path overrun (pb.delay); savings is when the expected
      // duration undercuts the target duration.
      (function(){
        var statusEl = document.getElementById('gpd-bar-status');
        statusEl.className = '';
        if (diff > 0) {
          statusEl.textContent = 'Delayed by: ' + diff + ' d';
          statusEl.className = 'gpd-late';
        } else if (expectedDur > 0 && expectedDur < planned) {
          statusEl.textContent = 'Savings by: ' + (planned - expectedDur) + ' d';
          statusEl.className = 'gpd-early';
        } else {
          statusEl.textContent = '';
        }
      })();

      // Duration bar — planned span in the Gantt's critical-path colour
      // (#00ACC1), overrun (if any) in red, same visual language as the
      // Activity Duration panel's bar. Bar length = B. Duration, the same
      // planned/budgeted duration shown against the project row in the Gantt.
      (function(){
        var actual = planned + diff;
        var maxDur = Math.max(planned, actual, 1);
        var barHtml;
        if (diff > 0) {
          barHtml = '<div style="width:'+(planned/maxDur*100).toFixed(1)+'%;background:#00ACC1;min-width:3px;"></div>'
                  + '<div style="width:'+(diff/maxDur*100).toFixed(1)+'%;background:#e53935;min-width:3px;"></div>';
        } else {
          barHtml = '<div style="width:100%;background:#00ACC1;"></div>';
        }
        document.getElementById('gpd-bar').innerHTML = barHtml;
      })();

      document.getElementById('gpd-tip-loading').style.display = 'none';
      document.getElementById('gpd-tip-content').style.display = 'block';
    }

    var _dragged = false;

    function position(anchorEl){
      var r = anchorEl.getBoundingClientRect();
      var top = r.bottom + 6;
      var left = Math.min(r.left, window.innerWidth - 536);
      tip.style.top = Math.max(4, top) + 'px';
      tip.style.left = Math.max(4, left) + 'px';
    }

    function show(anchorEl){
      if (!_dragged) position(anchorEl);
      tip.style.zIndex = (typeof window.popupSubZBase === 'function') ? window.popupSubZBase() : 10020;
      tip.classList.add('gpd-open');
      if (_cache) { render(_cache); return; }
      document.getElementById('gpd-tip-loading').style.display = 'block';
      document.getElementById('gpd-tip-content').style.display = 'none';
      $.ajax({
        type: 'POST', url: _base + '/performancedashboard', dataType: 'json',
        success: function(d){
          if (!d || !d.project_bar) { document.getElementById('gpd-tip-loading').textContent = 'No data.'; return; }
          _cache = d.project_bar;
          var acts = d.activities || [];
          var total = acts.length, done = 0;
          acts.forEach(function(a){ if (+a.completed_status !== 0) done++; });
          _cache.pct_complete = total > 0 ? Math.round((done / total) * 100) : 0;
          render(_cache);
        },
        error: function(){ document.getElementById('gpd-tip-loading').textContent = 'Failed to load.'; }
      });
    }
    function hide(){ tip.classList.remove('gpd-open'); }

    function bind(){
      var btn = document.getElementById('btn-project-duration');
      if (!btn || btn.dataset.gpdBound) return;
      btn.dataset.gpdBound = '1';
      btn.addEventListener('click', function(e){
        e.preventDefault(); e.stopPropagation();
        if (tip.classList.contains('gpd-open')) { hide(); return; }
        show(btn);
      });
      document.addEventListener('click', function(e){
        if (!tip.classList.contains('gpd-open')) return;
        if (e.target === btn || btn.contains(e.target)) return;
        if (tip.contains(e.target)) return;
        hide();
      });
    }
    bind();
    // #btn-project-duration lives in main.php outside this embedded view; if
    // this script runs before it's in the DOM, retry shortly.
    if (!document.getElementById('btn-project-duration')) setTimeout(bind, 500);

    // ── Drag ──────────────────────────────────────────────────────────────
    (function(){
      var titleEl = document.getElementById('gpd-tip-title');
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

  // ── Project Snapshot hover card ─────────────────────────────────────────
  // Hovering the project name in the Gantt window header
  // (#gantt-win-proj-name) shows a compact card styled like the
  // "Projects" list card (.gpm-card in main.php), independent of the
  // click-triggered #gpd-tip popup above (own trigger, own element, own
  // styling — same underlying performancedashboard data for now, since
  // several of the 8 fields share the same source; formulas for
  // Actual Duration / Status / Actual Completion are placeholders
  // pending further direction).
  (function(){
    var tip = document.getElementById('gsc-tip');
    if (!tip) return;
    var _cache = null;

    function fmD(s){
      if(!s || s==='0000-00-00') return '—';
      var d = new Date(s); if (isNaN(d)) return '—';
      var months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      return d.getDate()+' '+months[d.getMonth()]+' '+d.getFullYear();
    }

    function render(pb, projName){
      document.getElementById('gsc-tip-name').textContent = projName || 'Project';

      var planned = Math.max(0, +pb.budgeted || 0);
      var diff = Math.max(0, +pb.delay || 0);
      var expectedDur = Math.max(0, +pb.actual || 0);

      document.getElementById('gsc-planned-dur').textContent = planned + ' d';
      document.getElementById('gsc-actual-dur').textContent = expectedDur > 0 ? (expectedDur + ' d') : '—';
      document.getElementById('gsc-planned-comp').textContent = fmD(pb.b_end_date);
      document.getElementById('gsc-actual-comp').textContent = fmD(pb.proj_end_date || pb.a_end_date);

      var plannedStart = pb.b_start_date || '';
      var elapsedDays = 0;
      if (plannedStart) {
        var sd = new Date(plannedStart); sd.setHours(0,0,0,0);
        var td = new Date(); td.setHours(0,0,0,0);
        elapsedDays = Math.max(0, Math.round((td - sd) / 86400000) + 1);
      }
      var remainingDays = Math.max(0, planned - elapsedDays);
      document.getElementById('gsc-elapsed').textContent = plannedStart ? (elapsedDays + ' d') : '—';
      document.getElementById('gsc-remaining').textContent = remainingDays + ' d';
      document.getElementById('gsc-pct').textContent = (pb.pct_complete != null ? pb.pct_complete : 0) + '%';

      var statusEl = document.getElementById('gsc-status');
      statusEl.className = 'gsc-val';
      if (diff > 0) {
        statusEl.textContent = 'Delayed by ' + diff + ' d';
        statusEl.className = 'gsc-val gsc-late';
      } else if (expectedDur > 0 && expectedDur < planned) {
        statusEl.textContent = 'Earlier by ' + (planned - expectedDur) + ' d';
        statusEl.className = 'gsc-val gsc-early';
      } else {
        statusEl.textContent = '—';
      }

      document.getElementById('gsc-tip-loading').style.display = 'none';
      document.getElementById('gsc-tip-content').style.display = 'flex';
    }

    function position(anchorEl){
      var r = anchorEl.getBoundingClientRect();
      var top = r.bottom + 6;
      var left = Math.min(r.left, window.innerWidth - 496);
      tip.style.top = Math.max(4, top) + 'px';
      tip.style.left = Math.max(4, left) + 'px';
    }

    // Once the user starts dragging the card by its header, it "pins"
    // open — mouseleave no longer auto-hides it, and the card stays
    // wherever it was dragged to until the user explicitly closes it
    // (× button) or clicks elsewhere. Un-pinned (the normal hover
    // state), it still auto-hides ~150ms after the mouse leaves both
    // the trigger and the card, same as before.
    var _pinned = false;

    function show(anchorEl){
      if (!_pinned) position(anchorEl);
      tip.style.zIndex = (typeof window.popupSubZBase === 'function') ? window.popupSubZBase() : 10025;
      tip.classList.add('gsc-open');
      var projName = anchorEl.textContent || 'Project';
      if (_cache) { render(_cache, projName); return; }
      document.getElementById('gsc-tip-loading').style.display = 'block';
      document.getElementById('gsc-tip-content').style.display = 'none';
      $.ajax({
        type: 'POST', url: _base + '/performancedashboard', dataType: 'json',
        success: function(d){
          if (!d || !d.project_bar) { document.getElementById('gsc-tip-loading').textContent = 'No data.'; return; }
          _cache = d.project_bar;
          var acts = d.activities || [];
          var total = acts.length, done = 0;
          acts.forEach(function(a){ if (+a.completed_status !== 0) done++; });
          _cache.pct_complete = total > 0 ? Math.round((done / total) * 100) : 0;
          render(_cache, projName);
        },
        error: function(){ document.getElementById('gsc-tip-loading').textContent = 'Failed to load.'; }
      });
    }
    function hide(){ tip.classList.remove('gsc-open'); _pinned = false; }

    function bind(){
      var nameEl = document.getElementById('gantt-win-proj-name');
      if (!nameEl || nameEl.dataset.gscBound) return;
      nameEl.dataset.gscBound = '1';
      var hideTimer = null;
      nameEl.addEventListener('mouseenter', function(){
        clearTimeout(hideTimer);
        show(nameEl);
      });
      nameEl.addEventListener('mouseleave', function(){
        if (_pinned) return;
        hideTimer = setTimeout(hide, 150);
      });
      tip.addEventListener('mouseenter', function(){ clearTimeout(hideTimer); });
      tip.addEventListener('mouseleave', function(){
        if (_pinned) return;
        hideTimer = setTimeout(hide, 150);
      });
    }
    bind();
    // #gantt-win-proj-name lives in main.php outside this embedded view; if
    // this script runs before it's in the DOM, retry shortly.
    if (!document.getElementById('gantt-win-proj-name')) setTimeout(bind, 500);

    document.getElementById('gsc-tip-close').addEventListener('click', function(e){
      e.stopPropagation();
      hide();
    });

    // ── Drag by the header (#gsc-tip-top) — same mechanics as the other
    // draggable popups in this app. Starting a drag pins the card open.
    (function(){
      var hdr = document.getElementById('gsc-tip-top');
      var _action = false, _sx = 0, _sy = 0, _ox = 0, _oy = 0;
      function _anchor(){ var r = tip.getBoundingClientRect(); tip.style.left = r.left + 'px'; tip.style.top = r.top + 'px'; return r; }
      hdr.addEventListener('mousedown', function(e){
        if (e.target.id === 'gsc-tip-close') return;
        var r = _anchor();
        _action = true; _pinned = true;
        _sx = e.clientX; _sy = e.clientY; _ox = r.left; _oy = r.top;
        e.preventDefault();
      });
      document.addEventListener('mousemove', function(e){
        if (!_action) return;
        var dx = e.clientX - _sx, dy = e.clientY - _sy;
        tip.style.left = Math.max(0, _ox + dx) + 'px';
        tip.style.top = Math.max(0, _oy + dy) + 'px';
      });
      document.addEventListener('mouseup', function(){ _action = false; });
    })();
  })();

  // ---- Manage Relations panel -----------------------------------------------

  $('#btn-manage-relations').on('click', function() {
    var panel = $('#relations-panel');
    if (panel.is(':visible')) { panel.slideUp(200); return; }
    $('#relations-content').html('<em>Loading&hellip;</em>');
    panel.slideDown(200);
    $.ajax({
      type: 'POST',
      url: _base + '/activityrelation',
      dataType: 'json',
      data: { projectid: projectId },
      success: function(data) {
        if (!data || data.error !== 'No') {
          $('#relations-content').html('<span style="color:red;">Could not load relations panel.</span>');
          return;
        }
        $('#relations-content').html(
          '<div class="relations-form-wrap">' + (data.result || '') + '</div>' +
          '<hr>' +
          '<div class="relations-list-wrap">' + (data.relationList || '<div class="text-center">No relations yet.</div>') + '</div>'
        );
        $('#schedule_item_first-new').val(data.selecteditemone || '');
        $('#schedule_item_second-new').val(data.selecteditemtwo || '');
      },
      error: function() { $('#relations-content').html('<span style="color:red;">Could not load relations panel.</span>'); }
    });
  });

  // ---- Refresh CPM button ---------------------------------------------------

  $('#btn-refresh-cpm').on('click', function() {
    $('#gantt-status').text('Recalculating critical path…');
    refreshCriticalPath(function() { loadGantt(); });
  });

  // ---- Quick Entry button ---------------------------------------------------
  $(document).off('click.qe').on('click.qe', '#btn-quick-entry', function(e) {
    e.preventDefault();
    if (typeof openQeModal === 'function') {
      openQeModal();
    } else {
      $('#qe-bk').addClass('qe-open');
      $('#qe-modal').addClass('qe-open');
    }
  });

  // ---- Click on any activity bar opens Quick Entry prefilled ---------------
  // (rawId lookup happens inside the IIFE where _actCells is in scope — see below)

  // ---- Init -----------------------------------------------------------------

  $(document).ready(function() {
    refreshCriticalPath(function() { loadGantt(); });
  });


  // ── Manage Relations: item / activity selection ──────────────────────────
  // Unbind any handlers registered by a previous load of this page (AJAX navigation zombie fix)
  $(document).off('.ganttrelation');

  $(document).on('click.ganttrelation', '.schedule_item_first', function(){
    var itemId = $(this).attr('data-v');
    $('.schedule_item_first').removeClass('active');
    $(this).addClass('active');
    $('#schedule_item_first-new').val(itemId);
    $.ajax({
      type: 'POST', url: _base + '/getscheduleactivityone', dataType: 'json',
      data: { scheduleItem: itemId, projectid: $('#selectedProjectId').val() },
      success: function(data){ if(data.error=='No') $('#schedule_activity_first-data').html(data.result); }
    });
  });
  $(document).on('click.ganttrelation', '.schedule_activity_first', function(){
    $('.schedule_activity_first').removeClass('active');
    $(this).addClass('active');
    $('#schedule_activity_first-new').val($(this).attr('data-v'));
  });

  $(document).on('click.ganttrelation', '.schedule_item_second', function(){
    var itemId = $(this).attr('data-v');
    $('.schedule_item_second').removeClass('active');
    $(this).addClass('active');
    $('#schedule_item_second-new').val(itemId);
    $.ajax({
      type: 'POST', url: _base + '/getscheduleactivitytwo', dataType: 'json',
      data: { scheduleItem: itemId, projectid: $('#selectedProjectId').val() },
      success: function(data){ if(data.error=='No') $('#schedule_activity_second-data').html(data.result); }
    });
  });
  $(document).on('click.ganttrelation', '.schedule_activity_second', function(){
    $('.schedule_activity_second').removeClass('active');
    $(this).addClass('active');
    $('#schedule_activity_second-new').val($(this).attr('data-v'));
  });

  // Relation type radio → reveal lag input
  $(document).on('click.ganttrelation', '.relation_type', function(){
    $('#relation_type-new').val($(this).val());
    $('#lag').show();
  });

  // ── SAVE & ADD ────────────────────────────────────────────────────────────
  $(document).on('click.ganttrelation', '.save_relation_new', function(){
    var firstItem      = $('#schedule_item_first-new').val();
    var firstActivity  = $('#schedule_activity_first-new').val();
    var secondItem     = $('#schedule_item_second-new').val();
    var secondActivity = $('#schedule_activity_second-new').val();
    var relationType   = $('#relation_type-new').val();
    var lag            = $('#lag').val();

    $('#first_item_error').toggle(firstItem == '');        if(firstItem == '')        return;
    $('#first_activity_error').toggle(firstActivity == '');if(firstActivity == '')    return;
    $('#second_item_error').toggle(secondItem == '');      if(secondItem == '')       return;
    $('#second_activity_error').toggle(secondActivity=='');if(secondActivity == '')   return;
    $('#relation_error').toggle(relationType == '');       if(relationType == '')     return;

    var origText = $('.save_relation_new').first().text();
    $('.save_relation_new').attr('disabled', true).text('Saving…');
    $.ajax({
      type: 'POST', url: _base + '/saverelation', dataType: 'json',
      data: {
        lag: lag, firstItem: firstItem, firstActivity: firstActivity,
        secondItem: secondItem, secondActivity: secondActivity,
        relationType: relationType, projectId: $('#selectedProjectId').val()
      },
      success: function(data){
        if(data.error == 'No' || data.error == 'Durerror') {
          var _warn = (data.error === 'Durerror') ? data.errortext : null;
          reloadRelationsPanel('Relation saved — chart updated', _warn);
          refreshCriticalPath(function(){ loadGantt(); });
        } else {
          alert(data.errortext || 'Save failed.');
        }
      },
      error: function(xhr){ alert('Save failed: ' + (xhr.responseText || '').substring(0,200)); },
      complete: function(){ $('.save_relation_new').attr('disabled', false).text(origText || 'SAVE & ADD'); }
    });
  });

  // ── CLOSE button ──────────────────────────────────────────────────────────
  $(document).on('click.ganttrelation', '.cancel', function(){
    $('#relations-panel').slideUp(200);
  });

  // ── Edit existing relation (show inline edit form) ────────────────────────
  $(document).on('click.ganttrelation', '.editrelation', function(){
    var id = $(this).attr('data-v') || $(this).attr('value');
    $('#precedentitem'+id).hide();          $('#editrelationprecedentitem'+id).show();
    $('#precedentactivity'+id).hide();      $('#editrelationprecedentactivity'+id).show();
    $('#dependentitem'+id).hide();          $('#editrelationdependentitem'+id).show();
    $('#dependentactivity'+id).hide();      $('#editrelationdependentactivity'+id).show();
    $('#relationtype'+id).hide();           $('#editrelationrelationtype'+id).show();
    $('#lag'+id).hide();                    $('#editlag'+id).show();
    $('#editrelationbutton'+id).hide();     $('#saveeditrelationbutton'+id).show();
  });

  // ── Save edited relation ──────────────────────────────────────────────────
  $(document).on('click.ganttrelation', '.saveeditrelation', function(){
    var id = $(this).attr('data-v') || $(this).attr('value');
    $.ajax({
      type: 'POST', url: _base + '/updaterelation', dataType: 'json',
      data: {
        id: id,
        firstItem:      $('#editrelationprecedentitem'+id).val(),
        firstActivity:  $('#editrelationprecedentactivity'+id).val(),
        secondItem:     $('#editrelationdependentitem'+id).val(),
        secondActivity: $('#editrelationdependentactivity'+id).val(),
        relationType:   $('#editrelationrelationtype'+id).val(),
        lag:            $('#editlag'+id).val(),
        projectId:      $('#selectedProjectId').val()
      },
      success: function(data){
        if(data.error == 'No') {
          reloadRelationsPanel('Relation updated — chart updated');
          refreshCriticalPath(function(){ loadGantt(); });
        } else {
          alert(data.errortext || 'Update failed.');
        }
      }
    });
  });

  // ── Delete a relation ─────────────────────────────────────────────────────
  $(document).on('click.ganttrelation', '.deleterelation', function(){
    var id = $(this).attr('data-v');
    if(!confirm('Are you sure you want to delete this Relation?')) return;
    $.ajax({
      type: 'POST', url: _base + '/deleterelation', dataType: 'json',
      data: { relationId: id },
      success: function(data){
        if(data.error == 'No') {
          reloadRelationsPanel('Relation deleted — chart updated');
          refreshCriticalPath(function(){ loadGantt(); });
        } else {
          alert(data.errortext || 'Delete failed.');
        }
      }
    });
  });

  // ── KPI hover popup on Gantt bars ─────────────────────────────────────────
  (function(){
    var _kpTimer   = null;   // debounce timer
    var _kpXhr     = null;   // in-flight request
    var _kpCache   = {};     // actid → kpi data cache
    var _kpOpenActId = null; // actid whose popup is currently shown, for click-to-toggle

    var _fmK = function(v){ v=+v||0; if(!v)return'0'; return(+v.toFixed(2)).toLocaleString('en-IN'); };
    var _shK = function(s,n){ s=String(s||''); return s.length>n?s.slice(0,n-1)+'…':s; };
    var _fmDateK = function(s){ if(!s||s==='0000-00-00')return'-'; var p=s.split('-'); return p[2]+'-'+p[1]+'-'+p[0]; };

    function _gauge(elId, frac, leftCol, rightCol, centerHtml, lblL, lblR) {
      var el = document.getElementById(elId);
      if (!el) return;
      var cx=90, cy=80, r=66, sw=12;
      var f = Math.max(0, Math.min(1, frac||0));
      function ptF(fr){ var a=Math.PI*(1-fr); return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)]; }
      function arc(f1,f2,col){
        if(f2<=f1) return '';
        var p1=ptF(f1),p2=ptF(f2);
        if((f2-f1)>=1){ var pm=ptF(0.5); return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="butt"/>'; }
        return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="butt"/>';
      }
      var nr=r-12, na=Math.PI*(1-f);
      var nx=(cx+nr*Math.cos(na)).toFixed(1), ny=(cy-nr*Math.sin(na)).toFixed(1);
      var svg='<svg width="100%" viewBox="0 -8 180 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet" style="display:block;width:100%;height:auto;">'
        +arc(0,0.5,leftCol)+arc(0.5,1,rightCol)
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="2.5" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="5" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2" fill="#dce3ef"/>'
        + centerHtml
        +'<text x="6" y="100" text-anchor="start" font-size="9" fill="#555" font-family="Barlow Condensed,Arial">'+lblL+'</text>'
        +'<text x="174" y="100" text-anchor="end" font-size="9" fill="#555" font-family="Barlow Condensed,Arial">'+lblR+'</text>'
        +'</svg>';
      el.innerHTML = svg;
    }

    function _renderKpuCapacity(k) {
      var maxV=+k.cap_max||0, used=+k.cap_used||0;
      var f = maxV>0 ? used/maxV : 0;
      var pct = maxV>0 ? ((used/maxV)*100).toFixed(1) : '0';
      _gauge('gkp-g5', f, '#FFD700', '#FFD700',
        '<text x="90" y="60" text-anchor="middle" font-size="19" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+pct+'%</text>'
        +'<text x="90" y="72" text-anchor="middle" font-size="8" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Used '+_fmK(used)+'h / Max '+_fmK(maxV)+'h</text>',
        'Low','High');
      var el=document.getElementById('gkp-g5'); if(!el) return;
      el.insertAdjacentHTML('beforeend','<div class="gkp-kv">'
        +'<div class="gkp-kv-item"><span class="gkp-kv-lbl">Target (Max)</span><span class="gkp-kv-val">'+_fmK(maxV)+' h</span></div>'
        +'<div class="gkp-kv-item"><span class="gkp-kv-lbl">Actual (Used)</span><span class="gkp-kv-val '+(used>maxV?'over':'good')+'">'+_fmK(used)+' h</span></div>'
        +'</div>');
    }

    function _renderKpuCycleTime(k) {
      var tc=+k.target_cycle_time||0, ac=+k.actual_cycle_time>0?+k.actual_cycle_time:tc;
      var maxV=tc>0?tc*2:1, f=ac/maxV;
      var acCol=ac>tc?'#e53935':ac<tc?'#27ae60':'#1a2540';
      _gauge('gkp-g4', f, '#00838f', '#FF6D00',
        '<text x="90" y="54" text-anchor="middle" font-size="8" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Target</text>'
        +'<text x="90" y="65" text-anchor="middle" font-size="14" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+_fmK(tc)+' Hrs</text>'
        +'<text x="90" y="74" text-anchor="middle" font-size="8" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Actual</text>'
        +'<text x="90" y="85" text-anchor="middle" font-size="13" font-weight="700" fill="'+acCol+'" font-family="Barlow Condensed,Arial">'+_fmK(ac)+' Hrs</text>',
        'Fast','Slow');
      var el=document.getElementById('gkp-g4'); if(!el) return;
      var acCls=ac>tc?'over':ac<tc?'good':'';
      el.insertAdjacentHTML('beforeend','<div class="gkp-kv">'
        +'<div class="gkp-kv-item"><span class="gkp-kv-lbl">Target</span><span class="gkp-kv-val">'+_fmK(tc)+' Hrs</span></div>'
        +'<div class="gkp-kv-item"><span class="gkp-kv-lbl">Actual</span><span class="gkp-kv-val '+acCls+'">'+_fmK(ac)+' Hrs</span></div>'
        +'</div>');
    }

    function _renderKpuProductivity(k) {
      var tp=+k.target_productivity||0, ap=+k.actual_productivity>0?+k.actual_productivity:tp;
      var u=k.unit?' '+k.unit:''; var maxV=tp>0?tp*2:1, f=ap/maxV;
      var apCol=ap<tp?'#e53935':ap>tp?'#27ae60':'#1a2540';
      _gauge('gkp-g3', f, '#E65100', '#00695C',
        '<text x="90" y="54" text-anchor="middle" font-size="8" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Target</text>'
        +'<text x="90" y="65" text-anchor="middle" font-size="13" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+_fmK(tp)+u+'/d</text>'
        +'<text x="90" y="74" text-anchor="middle" font-size="8" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Actual</text>'
        +'<text x="90" y="85" text-anchor="middle" font-size="13" font-weight="700" fill="'+apCol+'" font-family="Barlow Condensed,Arial">'+_fmK(ap)+u+'/d</text>',
        'Low','High');
      var el=document.getElementById('gkp-g3'); if(!el) return;
      var apCls=ap<tp?'over':ap>tp?'good':'';
      el.insertAdjacentHTML('beforeend','<div class="gkp-kv">'
        +'<div class="gkp-kv-item"><span class="gkp-kv-lbl">Target</span><span class="gkp-kv-val">'+_fmK(tp)+u+'/d</span></div>'
        +'<div class="gkp-kv-item"><span class="gkp-kv-lbl">Actual</span><span class="gkp-kv-val '+apCls+'">'+_fmK(ap)+u+'/d</span></div>'
        +'</div>');
    }

    function _renderKpuTargetProd(k) {
      var el=document.getElementById('gkp-tp'); if(!el)return;
      var tq=+k.target_qty||0, aq=+k.actual_qty||0, dur=+k.b_duration||0, u=k.unit?' '+k.unit:'';
      var asd=k.act_start_date||'', lrd=k.last_reported_date||'';
      var elDays=(asd&&lrd)?Math.max(0,Math.round((new Date(lrd)-new Date(asd))/86400000)+1):0;
      var compTarget=(dur>0&&tq>0)?Math.min(tq,elDays*(tq/dur)):0;
      var fAct=tq>0?Math.max(0,Math.min(1,aq/tq)):0;
      var fTgt=tq>0?Math.max(0,Math.min(1,compTarget/tq)):0;
      var actCol=aq<compTarget?'#e53935':aq>compTarget?'#27ae60':'#1a2540';
      var cx=90,cy=80,r=66,sw=12;
      function ptF(fr){var a=Math.PI*(1-fr);return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)];}
      function arcP(f1,f2,col,cap){if(f2<=f1)return'';cap=cap||'butt';var p1=ptF(f1),p2=ptF(f2);if((f2-f1)>=1){var pm=ptF(0.5);return'<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';}return'<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';}
      var nr=r-12,na=Math.PI*(1-fTgt),nx=(cx+nr*Math.cos(na)).toFixed(1),ny=(cy-nr*Math.sin(na)).toFixed(1);
      el.innerHTML='<svg width="100%" viewBox="0 -8 180 130" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet" style="display:block;width:100%;height:auto;">'
        +arcP(0,1,'#a8aeb8')+(fAct>0?arcP(0,fAct,'#0d1f6e','butt'):'')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="2.5" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="5" fill="#555"/><circle cx="'+cx+'" cy="'+cy+'" r="2" fill="#a8aeb8"/>'
        +(tq>0?'<text x="90" y="44" text-anchor="middle" font-size="8" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Target to Date</text>':'')
        +(tq>0?'<text x="90" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+_fmK(compTarget)+u+'</text>':'')
        +'<text x="90" y="65" text-anchor="middle" font-size="8" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Actual</text>'
        +'<text x="90" y="76" text-anchor="middle" font-size="12" font-weight="700" fill="'+actCol+'" font-family="Barlow Condensed,Arial">'+_fmK(aq)+u+'</text>'
        +'<text x="4" y="100" text-anchor="start" font-size="9" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Actual <tspan font-weight="700" fill="'+actCol+'">'+_fmK(aq)+u+'</tspan></text>'
        +'<text x="176" y="100" text-anchor="end" font-size="9" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Schedule <tspan font-weight="700" fill="#1a2540">'+_fmK(tq)+u+'</tspan></text>'
        +'</svg>';
      var actCls=aq<compTarget?'over':aq>=compTarget?'good':'';
      el.insertAdjacentHTML('beforeend','<div class="gkp-kv">'
        +'<div class="gkp-kv-item"><span class="gkp-kv-lbl">Target to Date</span><span class="gkp-kv-val">'+_fmK(compTarget)+u+'</span></div>'
        +'<div class="gkp-kv-item"><span class="gkp-kv-lbl">Actual</span><span class="gkp-kv-val '+actCls+'">'+_fmK(aq)+u+'</span></div>'
        +'</div>');
    }

    function _renderKpuDuration(k) {
      var el=document.getElementById('gkp-dur'); if(!el)return;
      var bDur=+k.b_duration||+k.duration||0, aDur=+k.projected_duration||0;
      var elapsed=+k.elapsed||0, startDelay=+k.start_delay||0;
      if(!aDur) aDur=bDur;
      var maxDur=Math.max(bDur,aDur,1);
      var isOver=aDur>bDur, isUnder=bDur>aDur;
      var baseCol=k.critical?'#00838f':'#37474F';
      var fam="font-family:'Barlow Condensed',sans-serif;";
      var bar='<div style="position:relative;display:flex;align-items:stretch;height:11px;border-radius:3px;overflow:hidden;margin:4px 0;">';
      if(isOver){
        bar+='<div style="width:'+(bDur/maxDur*100).toFixed(1)+'%;background:'+baseCol+';min-width:3px;"></div>';
        bar+='<div style="width:'+((aDur-bDur)/maxDur*100).toFixed(1)+'%;background:#e53935;min-width:3px;"></div>';
      } else if(isUnder){
        bar+='<div style="width:'+(aDur/maxDur*100).toFixed(1)+'%;background:'+baseCol+';min-width:3px;"></div>';
        bar+='<div style="width:'+((bDur-aDur)/maxDur*100).toFixed(1)+'%;background:#f0c419;min-width:3px;"></div>';
      } else {
        bar+='<div style="width:100%;background:'+baseCol+';"></div>';
      }
      bar+='</div>';
      // Elapsed days from act_start_date to today
      var elapsedDays=elapsed;
      if(k.act_start_date&&k.act_start_date!=='0000-00-00'){
        var td=new Date();td.setHours(0,0,0,0);
        var sd=new Date(k.act_start_date);sd.setHours(0,0,0,0);
        elapsedDays=Math.max(0,Math.round((td-sd)/86400000)+1);
      }
      el.innerHTML='<div style="padding:5px 7px;'+fam+'font-size:10px;box-sizing:border-box;">'
        +'<div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:2px;">'
        +'<span style="color:#1a2540;font-weight:700;">'+bDur+'d Planned'+(isOver?' <span style="color:#e53935;">+' +(aDur-bDur)+'d</span>':isUnder?' <span style="color:#27ae60;">−'+(bDur-aDur)+'d</span>':'')+' </span>'
        +(startDelay>0&&!isOver?'<span style="color:#E65100;">Start +'+startDelay+'d</span>':'')
        +'</div>'
        +bar
        +'<div style="display:flex;justify-content:space-between;color:#5a6e8c;font-size:9px;">'
        +'<span>Start: <b style="color:#1a2540;">'+(_fmDateK(k.act_start_date)||'—')+'</b></span>'
        +'<span>Elapsed: <b style="color:#1a2540;">'+elapsedDays+'d</b></span>'
        +'<span>Plan End: <b style="color:#1a2540;">'+(_fmDateK(k.adj_end_date)||'—')+'</b></span>'
        +'</div>'
        +'</div>';
      var durCls=aDur>bDur?'over':aDur<bDur?'good':'';
      el.insertAdjacentHTML('beforeend','<div class="gkp-kv">'
        +'<div class="gkp-kv-item"><span class="gkp-kv-lbl">Target (Planned)</span><span class="gkp-kv-val">'+bDur+' d</span></div>'
        +'<div class="gkp-kv-item"><span class="gkp-kv-lbl">Actual (Projected)</span><span class="gkp-kv-val '+durCls+'">'+aDur+' d</span></div>'
        +'</div>');
    }

    var _gkpResData = []; // last-rendered resource_latest rows, indexed same as the bars, for hover lookup

    function _renderKpuResourceCapacity(k) {
      var el = document.getElementById('gkp-res'); if (!el) return;
      var items = k.resource_latest || [];
      _gkpResData = items;
      if (!items.length) {
        el.innerHTML = '<div style="text-align:center;font-size:11px;color:#9aa5ba;padding:14px 0;font-family:\'Barlow Condensed\',sans-serif;">No resource data</div>';
        return;
      }
      var cols = ['#3461b8','#d4845a','#27afc4','#7c5cbf','#f0c419','#8fa3bc','#ec407a','#26a69a'];
      var maxVal = 0;
      items.forEach(function(r){ maxVal = Math.max(maxVal, +r.qty || 0); });
      if (!maxVal) maxVal = 1;
      var fam = "font-family:'Barlow Condensed',sans-serif;";
      var bars = '<div class="gkp-res-hover-area" style="flex:1;min-height:0;display:flex;align-items:flex-end;gap:4px;padding:6px 8px 0;border-bottom:1px solid #dde3ef;overflow-x:auto;cursor:default;">';
      var labels = '<div style="display:flex;gap:4px;padding:3px 8px 5px;flex-shrink:0;overflow-x:hidden;">';
      items.forEach(function(r, i) {
        var qty = +r.qty || 0;
        var col = cols[i % cols.length];
        var pct = Math.max(4, (qty / maxVal * 100)).toFixed(1) + '%';
        bars += '<div style="flex:1;min-width:14px;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%;">'
          + '<div style="width:60%;height:' + pct + ';min-height:3px;background:' + col + ';border-radius:2px 2px 0 0;"></div>'
          + '</div>';
        labels += '<div style="flex:1;min-width:14px;text-align:center;font-size:8px;color:#5a6e8c;' + fam + 'overflow:hidden;white-space:nowrap;">' + _shK(r.type_name || '', 10) + '</div>';
      });
      bars += '</div>';
      labels += '</div>';
      el.innerHTML = bars + labels;
    }

    // Hover the panel → rich bar-chart tooltip (same style/classes as the
    // Cost Dashboard's Est/Act popups: gridline-textured bars + legend),
    // one bar per resource, sized slightly larger per request.
    (function(){
      var $tip = $('#gkp-res-tip');
      function _rows(){
        return _gkpResData.map(function(r){
          return { name: r.name || '', est: +r.qty || 0, act: +r.qty || 0, hasActual: false, unit: '' };
        });
      }
      $(document).on('mouseenter', '.gkp-res-hover-area', function(){
        $tip.html(_renderBarTip(_rows(), function(v){ return _fmK(v); }, 'Resource Quantity (Latest Reported)', true)).addClass('gm-bar-tip-open gkp-res-tip-big');
      });
      $(document).on('mousemove', '.gkp-res-hover-area', function(e){
        if (!$tip.hasClass('gm-bar-tip-open')) return;
        var tw = $tip.outerWidth(), th = $tip.outerHeight();
        var left = Math.min(e.clientX + 14, window.innerWidth  - tw - 8);
        var top  = Math.min(e.clientY + 14, window.innerHeight - th - 8);
        $tip.css({ left: Math.max(8,left) + 'px', top: Math.max(8,top) + 'px' });
      });
      $(document).on('mouseleave', '.gkp-res-hover-area', function(){
        $tip.removeClass('gm-bar-tip-open gkp-res-tip-big');
      });
    })();

    function _renderKpi(k) {
      document.getElementById('gkp-title').textContent = 'KPI — ' + (k.activity_name || '');
      _renderKpuCapacity(k);
      _renderKpuCycleTime(k);
      _renderKpuProductivity(k);
      _renderKpuTargetProd(k);
      _renderKpuDuration(k);
      _renderKpuResourceCapacity(k);
    }

    function _positionPopup(mouseX, mouseY) {
      var pop = document.getElementById('gkp-popup');
      var pw = pop.offsetWidth || 560, ph = pop.offsetHeight || 300;
      var vw = window.innerWidth, vh = window.innerHeight;
      // Always anchor to the Gantt (right) side — prefer right of cursor
      var left = mouseX + 18;
      if (left + pw > vw - 8) left = vw - pw - 8;
      if (left < 4) left = 4;
      var top = vh * 0.68 - ph * 0.5;
      if (top + ph > vh - 8) top = vh - ph - 8;
      if (top < 4) top = 4;
      pop.style.left = left + 'px';
      pop.style.top  = top  + 'px';
    }

    function _positionPopupRight(anchorEl) {
      // Position popup to the right of the viewport (Gantt chart side),
      // vertically aligned near the anchor element
      var pop = document.getElementById('gkp-popup');
      var pw = pop.offsetWidth || 560, ph = pop.offsetHeight || 300;
      var vw = window.innerWidth, vh = window.innerHeight;
      var rect = anchorEl ? anchorEl.getBoundingClientRect() : null;
      var left = rect ? Math.min(rect.right + 10, vw - pw - 8) : vw - pw - 8;
      if (left < 4) left = 4;
      var top = rect ? vh * 0.68 - ph * 0.5 : vh / 2 - ph / 2;
      if (top + ph > vh - 8) top = vh - ph - 8;
      if (top < 4) top = 4;
      pop.style.left = left + 'px';
      pop.style.top  = top  + 'px';
    }

    function _hidePopup() {
      clearTimeout(_kpTimer);
      if (_kpXhr) { _kpXhr.abort(); _kpXhr = null; }
      var pop = document.getElementById('gkp-popup');
      pop.classList.remove('gkp-visible');
      _kpOpenActId = null;
    }

    // Extract raw actid from the bar's hashed task ID via _actCells lookup
    function _getActIdFromBarDiv(barDiv) {
      var idAttr = barDiv.id || '';   // e.g. "gantt-containerbardiv_<hashed_tid>"
      var pfx = 'gantt-containerbardiv_';
      if (idAttr.indexOf(pfx) !== 0) return null;
      var tid = idAttr.slice(pfx.length);
      var cells = window._ganttActCells || {};
      var db  = cells[tid];
      return (db && db.rawId) ? db.rawId : null;
    }

    // Same lookup, keyed off the grid ROW's id instead of the bar's — the
    // row (left-hand name/columns table) is a separate element from the bar
    // (right-hand timeline), so hovering it can trigger the KPI popup
    // without competing with clicking the bar to open the WBS entry form.
    function _getActIdFromRow(row) {
      var idAttr = row.id || '';   // e.g. "gantt-containerchildrow_<hashed_tid>"
      var pfx = 'gantt-containerchildrow_';
      if (idAttr.indexOf(pfx) !== 0) return null;
      var tid = idAttr.slice(pfx.length);
      var cells = window._ganttActCells || {};
      var db  = cells[tid];
      return (db && db.rawId) ? db.rawId : null;
    }

    // #gkp-popup is a true floater (like cd-modal/cf-modal), not a
    // sub-popup — it must get its own live ++_z on every open, not a
    // one-time snapshot (popupSubZBase's _z + 5000 goes stale the moment
    // any other floater is clicked/opened afterward, letting this popup
    // get buried again — the exact bug class this fixes, see KPI/Cost
    // Dashboard/Cashflow all needing to stay visible simultaneously).
    function _raiseKpiPopup(pop) {
      if (typeof window.popupBringToFront === 'function') { window.popupBringToFront('gkp-popup'); return; }
      pop.style.zIndex = 10015;
    }

    // Generic KPI fetch + show function (used by bar hover and KPI icon hover)
    function _showKpiForAct(actId, positionFn) {
      _kpOpenActId = actId;
      if (_kpCache[actId]) {
        _renderKpi(_kpCache[actId]);
        var pop = document.getElementById('gkp-popup');
        pop.classList.add('gkp-visible');
        _raiseKpiPopup(pop);
        positionFn();
        return;
      }
      if (_kpXhr) _kpXhr.abort();
      document.getElementById('gkp-title').textContent = 'Loading KPI…';
      ['gkp-g5','gkp-g4','gkp-g3','gkp-tp','gkp-dur','gkp-res'].forEach(function(id){
        var el=document.getElementById(id); if(el) el.innerHTML='';
      });
      var pop = document.getElementById('gkp-popup');
      pop.classList.add('gkp-visible');
      _raiseKpiPopup(pop);
      positionFn();
      _kpXhr = $.ajax({
        type: 'POST', url: _base + '/performancedashboardkpi',
        data: { actid: actId }, dataType: 'json',
        success: function(d) {
          _kpXhr = null;
          if (!d || !d.kpi) return;
          _kpCache[actId] = d.kpi;
          _renderKpi(d.kpi);
          positionFn();
        },
        error: function(x, s) { _kpXhr = null; if(s!=='abort') _hidePopup(); }
      });
    }

    // Row click — KPI popup. Bound to #gantt-container itself (not the row
    // element directly) so it also catches clicks on nested cells/backdrop
    // divs inside the row whose own delegated-selector match might not be
    // the <tr> — resolves the activity id via closest('tr') off the actual
    // click target rather than relying on `this`. Explicitly excludes the
    // bar itself so it doesn't compete with clicking the bar to open the
    // WBS entry form (see the click.qebar handler further below). Stays
    // open until the user clicks a different row (which replaces its
    // contents), clicks the same row again while its popup is open
    // (toggle-close), or clicks the × close button.
    (function(){
      var _gc = document.getElementById('gantt-container');
      if (!_gc) return;
      _gc.addEventListener('click', function(e) {
        if (e.target.closest('.gtaskbarcontainer')) return;
        var row = e.target.closest('[id^="gantt-containerchildrow_"]');
        if (!row) return;
        var actId = _getActIdFromRow(row);
        if (!actId) return;
        var pop = document.getElementById('gkp-popup');
        if (_kpOpenActId === actId && pop.classList.contains('gkp-visible')) {
          _hidePopup();
          return;
        }
        var mx = e.clientX, my = e.clientY;
        _showKpiForAct(actId, function(){ _positionPopup(mx, my); });
      }, true);
    })();

    // Activity name click — Planned/Actual Start-End grid tooltip. Bound to
    // the .gtaskname cell specifically (not the whole row, unlike the KPI
    // hover above). Opens on click and stays open (unlike the old hover
    // version) until the user clicks elsewhere — see click.gnametipoutside
    // below — so Planned Start / Actual Start can be clicked to edit without
    // the tooltip vanishing first.
    (function(){
      var $nameTip = $('#gname-tip');
      var _curActId = null;   // rawId (scheduleactivities.id) of the activity currently shown
      function _fmtNameDate(d) {
        if (!d || d === '0000-00-00') return '—';
        var p = d.split('-');
        return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : d;
      }
      // Actual vs Planned, per date (Start compared to Start, End to End,
      // independently) — later than planned = red, earlier than planned =
      // green, same date = no colour change. Raw YYYY-MM-DD strings compare
      // correctly with plain string comparison.
      function _dateCls(actual, planned) {
        if (!actual || actual === '0000-00-00' || !planned || planned === '0000-00-00' || actual === planned) return '';
        return actual > planned ? 'gname-date-late' : 'gname-date-early';
      }
      function _dateSpan(field, raw, cls, editable) {
        var tag = editable ? 'u' : 'span';
        var cssCls = (editable ? 'gname-date-editable ' : '') + (cls || '');
        return '<' + tag + ' class="' + cssCls + '" data-field="' + field + '" data-date="' + (raw || '') + '">' + _fmtNameDate(raw) + '</' + tag + '>';
      }
      function _render(name, db) {
        // Before any progress is reported, "Actual Start" is only ever a
        // mirror of Planned Start (see astart: aStart || act.actual_start_date
        // above) — both are the same editable value/edit window. Once
        // progress exists, schedule_progress_report.start_date becomes the
        // historical record and neither is editable any more (server also
        // enforces this — see actionUpdateactivitystartdate).
        var editable = !db.hasProgress;
        $nameTip.html(
          '<div class="gname-tip-name">' + $('<span>').text(name).html() + '</div>'
          + '<div class="gname-tip-row gname-tip-planned">'
            + '<span><b>Planned Start:</b> ' + _dateSpan('planned', db.start, '', editable) + '</span>'
            + '<span><b>Planned End:</b> ' + _fmtNameDate(db.end) + '</span>'
          + '</div>'
          + '<div class="gname-tip-row gname-tip-actual">'
            + '<span><b>Actual Start:</b> ' + _dateSpan('actual', db.astart, _dateCls(db.astart, db.start), editable) + '</span>'
            + '<span><b>Actual End:</b> <span class="' + _dateCls(db.aend, db.end) + '">' + _fmtNameDate(db.aend) + '</span></span>'
          + '</div>'
        ).addClass('gname-tip-open');
      }
      function _showErr(msg) {
        $nameTip.find('.gname-tip-err').remove();
        $nameTip.append('<div class="gname-tip-err">' + $('<span>').text(msg).html() + '</div>');
      }
      var _editing = false;   // true while a date input inside the tooltip is open

      // Anchored to the cell's own bounding box (not the mouse position) and
      // positioned immediately to its right, touching/overlapping the cell
      // edge — not just "near" it. This removes the empty gap a cursor had
      // to cross to reach the tooltip, which is what let mouseleave fire
      // and close it before the cursor ever arrived (the previous
      // cursor-offset + delayed-close approach was too fragile/timing
      // dependent). No mousemove repositioning either — it stays put once shown.
      function _positionNearCell(cellEl) {
        var r = cellEl.getBoundingClientRect();
        var tw = $nameTip.outerWidth() || 340, th = $nameTip.outerHeight() || 90;
        var left = r.right - 6;   // slight overlap with the cell so there's no gap to cross
        if (left + tw > window.innerWidth - 8) left = Math.max(8, r.left - tw + 6);
        var top = r.top;
        if (top + th > window.innerHeight - 8) top = Math.max(8, window.innerHeight - th - 8);
        $nameTip.css({ left: left + 'px', top: top + 'px' });
      }
      $(document).off('mouseenter.gnametip').on('mouseenter.gnametip', '#gantt-container td.gtaskname', function(e){
        // .gtaskname lives in the LEFT list table's row — id prefix
        // "...child_<id>" — a distinct element from the chart/bar table's
        // row ("...childrow_<id>", used by the KPI row-hover). Match "child_"
        // but not "childrow_" (which also starts with "child").
        var row = this.closest('tr');
        if (!row || row.id.indexOf('child_') === -1 || row.id.indexOf('childrow_') !== -1) return;
        var tid = row.id.slice(row.id.indexOf('child_') + 'child_'.length);
        var cells = window._ganttActCells || {};
        var db = cells[tid];
        if (!db) return;
        _curActId = db.rawId || null;
        var name = this.textContent.trim();
        _render(name, db);
        _positionNearCell(this);
      });
      // Leaving the name cell schedules a close — a short grace period still
      // helps for the sliver of travel between the cell edge and the
      // (now overlapping) tooltip edge, cancelled the instant the cursor
      // reaches either the cell again or the tooltip itself.
      var _closeTimer = null;
      function _scheduleClose() {
        if (_editing) return;
        clearTimeout(_closeTimer);
        _closeTimer = setTimeout(function(){
          if (_editing) return;
          $nameTip.removeClass('gname-tip-open').empty();
          _curActId = null;
        }, 300);
      }
      function _cancelClose() { clearTimeout(_closeTimer); }
      $(document).off('mouseleave.gnametip').on('mouseleave.gnametip', '#gantt-container td.gtaskname', _scheduleClose);
      $(document).off('mouseenter.gnametipcancel').on('mouseenter.gnametipcancel', '#gantt-container td.gtaskname', _cancelClose);
      $nameTip.off('mouseenter.gnametip').on('mouseenter.gnametip', _cancelClose);
      $nameTip.off('mouseleave.gnametip').on('mouseleave.gnametip', _scheduleClose);

      // Click on an editable date value → swap it for a native date input
      // (browser renders its own calendar picker on focus/click). One more
      // click on the date, or anywhere else, closes/commits it — handled by
      // the input's own change/blur below.
      $nameTip.on('click', '.gname-date-editable', function(e){
        e.stopPropagation();
        if (!_curActId) return;
        var $val = $(this);
        if ($val.is('input')) return;
        _editing = true;
        var field = $val.data('field');
        var raw = $val.data('date');
        var $input = $('<input type="date" class="gname-date-input">').val(raw && raw !== '0000-00-00' ? raw : '');
        $val.replaceWith($input);
        $input.trigger('focus');
        if ($input[0].showPicker) { try { $input[0].showPicker(); } catch(err) {} }

        function _commit() {
          var newDate = $input.val();
          $input.off();
          if (!newDate || newDate === raw) { _closeEditor(); return; }
          $.ajax({
            type: 'POST', url: _base + '/updateactivitystartdate',
            data: { id: _curActId, field: field, date: newDate }, dataType: 'json',
            success: function(resp) {
              if (resp && resp.error === 'No') {
                // scheduleactivities/schedule_progress_report changed server-side
                // (planned edits also shift the End date to preserve duration) —
                // simplest correct path is a full reload rather than patching the
                // cached _ganttActCells values and risking drift.
                _editing = false;
                $nameTip.removeClass('gname-tip-open').empty();
                _curActId = null;
                if (typeof window.loadGantt === 'function') window.loadGantt();
              } else {
                _closeEditor();
                _showErr((resp && resp.errortext) || 'Could not update the date.');
              }
            },
            error: function() { _closeEditor(); _showErr('Could not update the date.'); }
          });
        }
        function _closeEditor() {
          _editing = false;
          var $u = $('<u class="gname-date-editable ' + (field === 'actual' ? _dateCls($input.val(), raw) : '') + '" data-field="' + field + '" data-date="' + (raw || '') + '">' + _fmtNameDate(raw) + '</u>');
          $input.replaceWith($u);
        }
        $input.off('change.gnamedate').on('change.gnamedate', _commit);
        $input.off('blur.gnamedate').on('blur.gnamedate', function(){
          // Native date pickers fire change on pick; a blur with no change
          // (user clicked away without picking) just reverts the editor.
          setTimeout(function(){ if ($input.parent().length || $input.closest('#gname-tip').length) _commit(); }, 150);
        });
        $input.off('click.gnamedate').on('click.gnamedate', function(ev){ ev.stopPropagation(); });
      });

      // While mid-edit the tooltip ignores mouseleave (see above), so a
      // click anywhere outside it is the only way to abandon the edit —
      // reverts the open input back to its display value without saving.
      $(document).off('click.gnametipoutside').on('click.gnametipoutside', function(e) {
        if (!$nameTip.hasClass('gname-tip-open')) return;
        if (e.target.closest && e.target.closest('#gname-tip')) return;
        _editing = false;
        $nameTip.removeClass('gname-tip-open').empty();
        _curActId = null;
      });
    })();

    $('#gkp-close').off('click').on('click', function() { _hidePopup(); });

    // NOTE: deliberately no "click outside closes it" handler here — KPI,
    // Cost Dashboard, and Cashflow Dashboard must all be able to stay open
    // at the same time (user requirement), so clicking one popup's trigger
    // must never auto-close another. Close only via the × button
    // (#gkp-close, bound above) or clicking the same row again (toggle,
    // bound in the row-click handler below) — matching how #cd-modal and
    // #cf-modal already behave (their own × / backdrop only, never a
    // generic outside-click).
    $(document).off('click.gkpioutside');

    // Drag + Resize: KPI float panel
    (function(){
      var win = document.getElementById('gkp-popup');
      var hdr = document.getElementById('gkp-hdr');
      // Join the app-wide floater stacking authority — see the KPI modal's
      // identical binding earlier in this file for why.
      if(typeof window.bindDragTouch === 'function' && typeof window.popupBringToFront === 'function'){
        window.bindDragTouch(win, 'mousedown', function(){ window.popupBringToFront('gkp-popup'); }, true);
      }
      var MIN_W=400, MIN_H=280, _action=null, _sx=0, _sy=0, _ox=0, _oy=0, _ow=0, _oh=0;
      function _anchor(){ var r=win.getBoundingClientRect(); win.style.left=r.left+'px'; win.style.top=r.top+'px'; win.style.width=r.width+'px'; win.style.height=r.height+'px'; return r; }
      hdr.addEventListener('mousedown', function(e){ if(e.target.id==='gkp-close') return; var r=_anchor(); _action='drag'; _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault(); });
      document.querySelectorAll('.gkp-rs').forEach(function(el){ el.addEventListener('mousedown', function(e){ var r=_anchor(); _action=el.getAttribute('data-dir'); _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height; e.preventDefault(); e.stopPropagation(); }); });
      document.addEventListener('mousemove', function(e){ if(!_action) return; var dx=e.clientX-_sx, dy=e.clientY-_sy; if(_action==='drag'){ win.style.left=Math.max(0,_ox+dx)+'px'; win.style.top=Math.max(0,_oy+dy)+'px'; } else { var l=_ox,t=_oy,w=_ow,h=_oh; if(_action.indexOf('e')>-1){w=Math.max(MIN_W,_ow+dx);} if(_action.indexOf('s')>-1){h=Math.max(MIN_H,_oh+dy);} if(_action.indexOf('w')>-1){var nw=Math.max(MIN_W,_ow-dx);l=_ox+(_ow-nw);w=nw;} if(_action.indexOf('n')>-1){var nh=Math.max(MIN_H,_oh-dy);t=_oy+(_oh-nh);h=nh;} win.style.left=l+'px'; win.style.top=t+'px'; win.style.width=w+'px'; win.style.height=h+'px'; } });
      document.addEventListener('mouseup', function(){ _action=null; });
    })();

    // Bar click — open WBS Quick Entry prefilled with saved data
    // Unbind any handler registered by a previous load of this page (this
    // whole script is re-executed via reloadGantt() -> $.html() on every
    // WBS save/add, and a plain un-namespaced $(document).on() here would
    // otherwise stack a new copy each time, firing openQeModal() multiple
    // times per click and duplicating the resource rows it re-fetches).
    $(document).off('click.qebar').on('click.qebar', '.gtaskbarcontainer:not(.gplan)', function() {
      var idAttr = this.id || '';
      var pfx = 'gantt-containerbardiv_';
      var rawId = null, wanId = null;
      if (idAttr.indexOf(pfx) === 0) {
        var tid = idAttr.slice(pfx.length);
        var cells = window._ganttActCells || {};
        var db = cells[tid];
        rawId = (db && db.rawId) ? db.rawId : null;
        wanId = (db && db.wanId)  ? db.wanId  : null;
      }
      if (typeof window.openQeModal === 'function') window.openQeModal(rawId || null, wanId || null);
    });
  })();

  function reloadRelationsPanel(notice, warningText) {
    $.ajax({
      type: 'POST', url: _base + '/activityrelation', dataType: 'json',
      data: { projectid: projectId },
      success: function(data) {
        if (data && data.error === 'No') {
          $('#relations-content').html(
            '<div class="relations-form-wrap">' + (data.result || '') + '</div>' +
            '<hr>' +
            '<div class="relations-list-wrap">' + (data.relationList || '<div class="text-center">No relations yet.</div>') + '</div>'
          );
          $('#schedule_item_first-new').val(data.selecteditemone || '');
          $('#schedule_item_second-new').val(data.selecteditemtwo || '');
          if (warningText) {
            var $w = $('<div style="background:#fff3cd;color:#856404;border:1px solid #ffeeba;border-radius:4px;padding:7px 14px;margin-bottom:10px;font-size:13px;">&#9888; ' + warningText + '</div>');
            $('#relations-content').prepend($w);
            setTimeout(function(){ $w.fadeOut(400, function(){ $w.remove(); }); }, 6000);
          }
          if (notice) {
            var $n = $('<div style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;border-radius:4px;padding:7px 14px;margin-bottom:10px;font-size:13px;font-weight:600;">&#10003; ' + notice + '</div>');
            $('#relations-content').prepend($n);
            setTimeout(function(){ $n.fadeOut(400, function(){ $n.remove(); }); }, 3000);
          }
        }
      }
    });
  }

})(jQuery);

<?php if(empty($embedMode)): ?>
// Gantt float panel: close + fullscreen
(function(){
  var panel = document.getElementById('gantt-float');
  var _saved = null;

  document.getElementById('gantt-btn-close').addEventListener('click', function(){
    window.history.back();
  });

  document.getElementById('gantt-btn-expand').addEventListener('click', function(){
    var btn = this;
    if(_saved){
      panel.style.left=_saved.left; panel.style.top=_saved.top;
      panel.style.width=_saved.width; panel.style.height=_saved.height;
      panel.style.right=''; panel.style.bottom='';
      _saved=null; btn.innerHTML='&#x26F6;'; btn.title='Fullscreen';
    } else {
      var r=panel.getBoundingClientRect();
      _saved={left:panel.style.left,top:panel.style.top,width:panel.style.width,height:panel.style.height};
      panel.style.left='0'; panel.style.top='0';
      panel.style.width='100vw'; panel.style.height='100vh';
      btn.innerHTML='&#x2716;'; btn.title='Restore';
    }
  });
})();

// Gantt float panel: drag + resize
(function(){
  var panel = document.getElementById('gantt-float');
  var hdr   = document.getElementById('gantt-float-hdr');
  // Join the app-wide floater stacking authority (this standalone page
  // loads the normal layout, so main.php's floater script is present here
  // too) — see the embed-mode KPI/Cost panels' identical binding for why.
  if(typeof window.bindDragTouch === 'function' && typeof window.popupBringToFront === 'function'){
    window.bindDragTouch(panel, 'mousedown', function(){ window.popupBringToFront('gantt-float'); }, true);
  }
  var MIN_W = 500, MIN_H = 300;
  var _action=null,_sx=0,_sy=0,_ox=0,_oy=0,_ow=0,_oh=0;
  function _anchor(){
    var r = panel.getBoundingClientRect();
    panel.style.right='auto'; panel.style.left=r.left+'px';
    panel.style.top=r.top+'px'; panel.style.width=r.width+'px';
    panel.style.height=r.height+'px'; return r;
  }
  hdr.addEventListener('mousedown',function(e){
    var r=_anchor(); _action='drag';
    _sx=e.clientX;_sy=e.clientY;_ox=r.left;_oy=r.top; e.preventDefault();
  });
  document.querySelectorAll('.gf-rs').forEach(function(el){
    el.addEventListener('mousedown',function(e){
      var r=_anchor(); _action=el.getAttribute('data-dir');
      _sx=e.clientX;_sy=e.clientY;_ox=r.left;_oy=r.top;_ow=r.width;_oh=r.height;
      e.preventDefault(); e.stopPropagation();
    });
  });
  document.addEventListener('mousemove',function(e){
    if(!_action) return;
    var dx=e.clientX-_sx, dy=e.clientY-_sy;
    if(_action==='drag'){
      var x=Math.max(0,Math.min(_ox+dx,window.innerWidth-panel.offsetWidth));
      var y=Math.max(0,Math.min(_oy+dy,window.innerHeight-panel.offsetHeight));
      panel.style.left=x+'px'; panel.style.top=y+'px';
    } else {
      var l=_ox,t=_oy,w=_ow,h=_oh;
      if(_action.indexOf('e')>-1){w=Math.max(MIN_W,_ow+dx);}
      if(_action.indexOf('s')>-1){h=Math.max(MIN_H,_oh+dy);}
      if(_action.indexOf('w')>-1){var nw=Math.max(MIN_W,_ow-dx);l=_ox+(_ow-nw);w=nw;}
      if(_action.indexOf('n')>-1){var nh=Math.max(MIN_H,_oh-dy);t=_oy+(_oh-nh);h=nh;}
      panel.style.left=l+'px';panel.style.top=t+'px';
      panel.style.width=w+'px';panel.style.height=h+'px';
    }
  });
  document.addEventListener('mouseup',function(){_action=null;});
})();
<?php endif; ?>

</script>
