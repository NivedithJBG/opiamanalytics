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
  display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
  cursor: move; user-select: none;
}
#gkp-title {
  font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
  color: #ffffff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; min-width: 0;
}
#gkp-title-actname { text-transform: none; font-weight: 500; opacity: .85; }
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
#gcm-pop-hdr-left { color: #ffffff; }
#gcm-title-actname { text-transform: none; font-weight: 500; opacity: .85; }
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
/* Unit Cost of Resources / Yield / Cost of Resources: as more Resource
   Types get allocated to an activity, their Type-bar list grows taller
   than the panel — scroll instead of hard-clipping new bars off, so every
   type stays reachable rather than silently disappearing beyond the fixed
   panel height. Every other panel (dials etc.) keeps the shared
   overflow:hidden above. */
#gm-cd-ucr, #gm-cd-rcon, #gm-cd-rcost { overflow-y: auto; }
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

/* ── Unit Cost of Resources drill-down popup — click-driven (Type bar →
   Group bars → Resource bars), reuses .gm-bar-tip's look but centers
   fixed on screen instead of following the mouse, since there is no
   hover/mousemove tracking for it. ── */
.gm-bar-tip.gm-ucr-drill-open {
  z-index: 200310; padding: 14px 16px;
  box-shadow: 0 10px 40px rgba(0,0,0,.4);
  width: 460px; max-width: 92vw;
  height: 360px; max-height: 82vh;
  flex-direction: column;
}
.gm-bar-tip.gm-ucr-drill-open { display: none; padding: 0 0 14px; overflow: hidden; }
.gm-bar-tip.gm-ucr-drill-open.gm-bar-tip-open { display: flex; }
#gm-ucr-tip-hdr {
  font-size: 12px; margin-bottom: 10px; flex-shrink: 0;
  background: #1a2540; color: #fff; padding: 10px 12px 10px 16px;
  border-radius: 6px 6px 0 0; text-transform: uppercase; letter-spacing: .04em;
  display: flex; align-items: center; justify-content: space-between; gap: 10px;
  cursor: move; user-select: none;
}
#gm-ucr-tip-title { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
#gm-ucr-tip-close {
  background: none; border: none; color: #fff; font-size: 18px; line-height: 1;
  cursor: pointer; padding: 0 4px; opacity: .8; flex-shrink: 0;
}
#gm-ucr-tip-close:hover { opacity: 1; }
#gm-ucr-tip-body { flex: 1; min-height: 0; display: flex; flex-direction: column; }
.gm-bar-tip.gm-ucr-drill-open .gm-ucr-drill-body,
.gm-bar-tip.gm-ucr-drill-open .gm-bar-tip-empty { padding-left: 16px; padding-right: 16px; }
.gm-ucr-drill-body { flex: 1; min-height: 0; overflow-y: auto; display: flex; flex-direction: column; justify-content: center; }

/* ── Horizontal bar chart (proper X/Y axes) — used for the Resource Type
   panel and the Resource Group popup: category names on the left (Y
   axis), value axis along the bottom (X axis) with gridlines at clean
   tick intervals, one bar per category sized against that axis.
   --gm-axis-w is set per-instance (label column width) by the JS. ── */
.gm-hchart { font-family:'Barlow Condensed',sans-serif; --gm-axis-w: 90px; flex-shrink:0; }
.gm-hchart-plot { position:relative; }
.gm-hchart-grid {
  position:absolute; top:0; bottom:0; left:var(--gm-axis-w); right:26px;
}
.gm-hchart-gridline {
  position:absolute; top:0; bottom:0; width:1px; background:#98a3b3;
}
.gm-hchart-rows { display:flex; flex-direction:column; gap:10px; padding:2px 0; }
.gm-hchart-row { display:flex; align-items:center; height:20px; }
.gm-hchart-rowlbl {
  width:var(--gm-axis-w); flex-shrink:0; padding-right:6px; box-sizing:border-box;
  font-size:9.5px; color:#1a2540; text-align:right;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.gm-hchart-rowlbl-lg { font-size:12.5px; font-weight:600; }
.gm-hchart-rowtrack {
  flex:1; margin-right:26px; position:relative; height:12px;
}
.gm-hchart-bar {
  position:relative; height:12px; min-width:2px;
  border-radius:0 3px 3px 0;
  display:flex; align-items:center;
  transition:width .25s ease;
}
.gm-hchart-barval {
  position:absolute; left:100%; margin-left:5px;
  font-size:9px; font-weight:700; color:#1a2540; white-space:nowrap;
}
.gm-hchart-bar-over {
  position:absolute; top:0; height:12px; background:#e64a19;
  border-radius:0 3px 3px 0;
}
.gm-hchart-rowtrack .gm-hchart-bar-over { display:flex; align-items:center; }
.gm-hchart-xaxis { position:relative; margin-top:4px; border-top:1px solid #b9c2d1; }
.gm-hchart-xaxis-ticks { position:relative; margin-left:var(--gm-axis-w); margin-right:26px; height:14px; }
.gm-hchart-xtick {
  position:absolute; top:3px; transform:translateX(-50%);
  font-size:8.5px; color:#5a6e8c; white-space:nowrap;
}
.gm-hchart-xtitle {
  margin-left:var(--gm-axis-w); margin-right:26px; text-align:center;
  font-size:9px; font-weight:700; color:#3461b8; text-transform:uppercase;
  letter-spacing:.04em; margin-top:4px;
}
.gm-hchart-sm .gm-hchart-rowlbl-lg { font-size:10px; }
.gm-hchart-sm .gm-hchart-barval { font-size:8px; }
.gm-hchart-sm .gm-hchart-xtick { font-size:7.5px; }
.gm-hchart-sm .gm-hchart-xtitle { font-size:8px; }
.gm-hchart-withytitle { display:flex; align-items:stretch; gap:6px; }
.gm-hchart-ytitle {
  writing-mode:vertical-rl; transform:rotate(180deg); flex-shrink:0;
  text-align:center; font-size:9px; font-weight:700; color:#3461b8;
  text-transform:uppercase; letter-spacing:.04em; padding:2px 0;
}
.gm-hchart-withytitle .gm-hchart-main { flex:1; min-width:0; }

/* ── Vertical bar chart (proper X/Y axes) — used for the Resources popup:
   value axis on the left (Y axis) with horizontal gridlines at clean
   tick intervals, resource names along the bottom (X axis), value
   labelled on top of each bar. ── */
.gm-vchart { font-family:'Barlow Condensed',sans-serif; display:flex; flex-direction:column; flex:1; min-height:0; }
.gm-vchart-plot { flex:1; min-height:0; display:flex; }
.gm-vchart-yaxis {
  width:32px; flex-shrink:0; display:flex; flex-direction:column; justify-content:space-between;
  padding:16px 4px 0 0; box-sizing:border-box; text-align:right;
}
.gm-vchart-ytick { font-size:8.5px; color:#5a6e8c; line-height:1; }
.gm-vchart-area { flex:1; min-width:0; position:relative; padding-top:16px; box-sizing:border-box; }
.gm-vchart-grid { position:absolute; left:0; right:0; top:16px; bottom:0; }
.gm-vchart-gridline { position:absolute; left:0; right:0; height:1px; background:#98a3b3; }
.gm-vchart-bars {
  position:relative; height:100%; display:flex; align-items:flex-end;
  gap:14px; padding:0 10px; border-left:1px solid #b9c2d1; border-bottom:1px solid #b9c2d1;
  box-sizing:border-box;
}
.gm-vchart-col { flex:1; max-width:70px; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; height:100%; }
.gm-vchart-bar-wrap {
  position:relative; width:60%; min-height:2px;
  display:flex; flex-direction:column; align-items:center;
  transition:height .25s ease;
}
.gm-vchart-bar {
  position:relative; width:100%; flex:1; min-height:0;
  border-radius:3px 3px 0 0;
  overflow:hidden;
}
.gm-vchart-diff { position:absolute; top:0; left:0; right:0; }
.gm-vchart-barval {
  flex-shrink:0; margin-bottom:1px; display:block;
  font-size:11.5px; font-weight:700; color:#1a2540; white-space:nowrap;
}
.gm-vchart-barval-diff {
  flex-shrink:0; margin-bottom:3px; display:block;
  font-size:10.5px; font-weight:700; white-space:nowrap;
}
.gm-vchart-legend {
  display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:8px;
  font-size:8.5px; color:#5a6e8c; flex-shrink:0;
}
.gm-vchart-legend span { display:inline-flex; align-items:center; gap:4px; }
.gm-vchart-legend i { width:8px; height:8px; border-radius:2px; display:inline-block; }
.gm-vchart-legend-lg {
  font-size:12.5px; font-weight:600; color:#1a2540; margin-top:14px;
  flex-direction:column; align-items:flex-start; justify-content:flex-start; gap:8px;
}
.gm-vchart-legend-lg i { width:10px; height:10px; }
.gm-vchart-legend-sm { font-size:8.5px; margin-top:8px; gap:4px; }
.gm-vchart-legend-sm i { width:7px; height:7px; }
.gm-vchart-xaxis { display:flex; margin-top:4px; }
.gm-vchart-xaxis-spacer { width:32px; flex-shrink:0; }
.gm-vchart-xlabels { flex:1; display:flex; gap:14px; padding:0 10px; box-sizing:border-box; }
.gm-vchart-xlbl {
  flex:1; max-width:70px; text-align:center; font-size:8.5px; color:#1a2540;
  white-space:normal; word-break:break-word; line-height:1.2;
}
.gm-vchart-ytitle {
  text-align:center; font-size:9px; font-weight:700; color:#3461b8;
  text-transform:uppercase; letter-spacing:.04em; margin-top:6px; flex-shrink:0;
}

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
.giow-reorder-handle {
  display: inline-block; cursor: grab; color: #cfd4db; font-size: 11px;
  letter-spacing: -2px; vertical-align: middle; margin-left: 3px;
  user-select: none;
}
.giow-reorder-handle:hover { color: #fff; }
.giow-reorder-handle.giow-dragging { cursor: grabbing; }
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
#gantt-container .gtasktablewrapper tr.giow-drop-target td {
  background: #3a4a5e !important; outline: 1px dashed #cfd4db; outline-offset: -1px;
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
.gtaskgreen, div.gtaskgreen.gplan { background: #00842e !important; border-color: #00611f !important; }
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
    <span id="gcm-pop-hdr-left">Manage Cost</span>
    <span id="gcm-pop-hdr-center"></span>
    <span id="gcm-pop-hdr-right"><button id="gcm-pop-close" title="Close">&times;</button></span>
  </div>
  <div id="gcm-pop-body">
    <div id="gcm-pop-loading">Loading&hellip;</div>
    <div id="gcm-pop-row1" style="display:none">
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title" id="gcm-title-g5">Unit Cost of Activity</div><div class="gcm-pop-panel-body" id="gm-cd-g5" style="align-items:center;justify-content:flex-start"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title" id="gcm-title-g4">Work Done</div><div class="gcm-pop-panel-body" id="gm-cd-g4" style="align-items:center;justify-content:flex-start"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title" id="gcm-title-g2">Cost of Activity</div><div class="gcm-pop-panel-body" id="gm-cd-g2"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Unit Cost of Resources</div><div class="gcm-pop-panel-body" id="gm-cd-ucr" style="align-items:center;justify-content:flex-start"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title" id="gcm-title-rcon">Yield</div><div class="gcm-pop-panel-body" id="gm-cd-rcon" style="align-items:center;justify-content:flex-start"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Cost of Resources</div><div class="gcm-pop-panel-body" id="gm-cd-rcost" style="align-items:center;justify-content:flex-start"></div></div>
    </div>
    <div id="gcm-pop-row2" style="display:none"></div>
  </div>
</div>

<div id="gm-ucr-tip" class="gm-bar-tip">
  <div id="gm-ucr-tip-hdr" class="gm-bar-tip-hdr">
    <span id="gm-ucr-tip-title"></span>
    <button type="button" id="gm-ucr-tip-close" title="Close">&times;</button>
  </div>
  <div id="gm-ucr-tip-body"></div>
</div>
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
  <div id="gkp-hdr"><span id="gkp-title">Manage Schedule</span><button id="gkp-close" title="Close">&times;</button></div>
  <div id="gkp-body">
    <div id="gkp-row1">
      <div class="gkp-panel"><div class="gkp-panel-title">Productivity</div><div class="gkp-panel-body" id="gkp-g3"></div></div>
      <div class="gkp-panel"><div class="gkp-panel-title">Target Production</div><div class="gkp-panel-body" id="gkp-tp"></div></div>
      <div class="gkp-panel"><div class="gkp-panel-title">Activity Duration</div><div class="gkp-panel-body" id="gkp-dur"></div></div>
    </div>
    <div id="gkp-row2">
      <div class="gkp-panel"><div class="gkp-panel-title">Resource Capacity</div><div class="gkp-panel-body" id="gkp-res"></div></div>
      <div class="gkp-panel"><div class="gkp-panel-title">Capacity Utilisation</div><div class="gkp-panel-body" id="gkp-g5"></div></div>
      <div class="gkp-panel"><div class="gkp-panel-title">Cycle Time</div><div class="gkp-panel-body" id="gkp-g4"></div></div>
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
        <span title="Planned Start + Duration. Fixed until the user changes the planned start date or duration.">Target Duration: <b id="gpd-target-comp">—</b></span>
        <span title="If no progress reported: Planned Duration + Start Delay (days elapsed since the activity was due to start). If progress reported: Elapsed Days ÷ Quantity Done &times; Total Quantity.">Expected Duration: <b id="gpd-expected-comp">—</b></span>
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
      <div class="gsc-row"><label title="Planned Start + Duration. Fixed until the user changes the planned start date or duration."><span class="gsc-swatch" style="background:#ff9800;"></span>Planned Duration</label><span class="gsc-val" id="gsc-planned-dur">—</span></div>
      <div class="gsc-row"><label title="If no progress reported: Planned Duration + Start Delay (days elapsed since the activity was due to start). If progress reported: Elapsed Days ÷ Quantity Done &times; Total Quantity."><span class="gsc-swatch" style="background:#2979ff;"></span>Actual Duration</label><span class="gsc-val" id="gsc-actual-dur">—</span></div>
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
(function(){
  if(typeof window.bindDragTouch !== 'function'){
    // Standalone fallback: this view can be loaded with layout=false (embedded
    // Gantt popup), which skips layouts/main.php where the shared shim normally
    // comes from. Without it, drag/resize here only ever bound mousedown, so
    // dragging never worked on touch devices (iPad).
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
})();
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
    // Columns: Unit, Quantity, B.Duration, A.Duration — Status/KPI/Cost injected via DOM after Draw()
    g.vColumnOrder = ['vAdditionalHeaders', 'vShowDur', 'vShowRes'];
    g.setAdditionalHeaders({
      unit: { title: 'Unit', class: 'gantt-col-unit' },
      qty:  { title: 'Quantity', class: 'gantt-col-qty' }
    });

    // Root project row — pID = projectId (never 0; JSGantt reserves 0 as virtual root)
    var _projTi = new JSGantt.TaskItem(
      projectId, projectName, '', '', 'ggroupblack', '', 0, '', 0, 1, 0, 1, '', '', '', g
    );
    var _projHid = _projTi.getID();
    window._ganttProjectRowHid = _projHid; // used by the project-row click handler to open the Relationships popup
    g.AddTaskItem(_projTi);

    // Map from hashed task ID → status for dot column
    var _actStatus = {};
    // Map from hashed task ID → raw DB values, used after Draw() to patch B. columns
    var _actCells = {};
    // Map from hashed task ID (TaskItem.getID()) → real scheduleactivities.id.
    // JSGantt's own getID() only ever returns a one-way hash of the pID
    // string passed to the TaskItem constructor (see hashKey() in
    // jsgantt.js) — there is no built-in getter back to the original id.
    // Dependency lines are stamped with gDepId<hash>/gDepNextId<hash>
    // classes (DrawDependencies in jsgantt.js), so this map is what lets
    // a line click be resolved back to the real precedent/successor
    // activity ids for openProjectRelationsList's edit-jump.
    window._ganttActRealId = {};
    window._ganttActCells = _actCells;
    // Map from EVERY WBS-tier row's hashed task ID → {wgId, iowGroupId}
    // (bare IOWs with no activity yet, AND IOWs that already have one or
    // more activities scheduled) — so clicking any IOW row opens the
    // Activity Picker popup scoped to that IOW AND filtered to its
    // parent IOW Group (see the click handler further below). Does NOT
    // include per-activity child rows or the IOW-Group/project tier
    // rows — only the WBS-item tier itself.
    var _wbsRowWgId = {};
    window._ganttWbsBareRowWgId = _wbsRowWgId;
    // A. Duration/Start/End for group rows (WBS items and IOW groups) — separate to avoid clearing B. columns
    var _groupActDur   = {};
    var _groupActStart = {};
    var _groupActEnd   = {};
    // B. Duration/Start/End for group rows — patched explicitly since bar may use A. dates
    var _groupBDur   = {};
    var _groupBStart = {};
    var _groupBEnd   = {};
    var iowHashedIds = {};
    // Project row: min/max dates across all WBS items; duration = span of those dates
    var _projBStartMin = null, _projBEndMax = null;
    var _projAStartMin = null, _projAEndMax = null;

    // Load IOW Groups used in this project synchronously — one row per
    // group (no WBS-item tier in between: Project → IOW Group → Activity).
    $.ajax({
      type: 'POST',
      url: _base + '/ganttitems',
      dataType: 'json',
      async: false,
      data: { projectid: projectId },
      success: function(data) {
        if (!data.result) return;
        for (var i = 0; i < data.result.length; i++) {
          var item = data.result[i];

          // IOW Group tier — the bar itself (B./A. Duration patched onto
          // this same hashed id below; there is no separate child row for
          // the group's own span anymore).
          var iowPid = 'iow_' + item.iowGroupid;
          var _iowADur   = (item.a_duration !== null && item.a_duration !== undefined) ? item.a_duration : null;
          var _iowAStart = safeDate(item.a_min_start) || null;
          var _iowAEnd   = (_iowAStart && _iowADur !== null) ? addDays(_iowAStart, _iowADur - 1) : null;
          var _iowBarS = _iowAStart || safeDate(item.start_date);
          var _iowBarE = _iowAEnd ? addOneDay(_iowAEnd) : (item.end_date ? addOneDay(item.end_date) : '');
          var _iowTi = new JSGantt.TaskItem(
            iowPid, item.iow_group_name,
            _iowBarS, _iowBarE,
            'ggroupblack', '', 0, '', 0, 1, projectId, 1, '', '', '', g
          );
          var _iowHid = _iowTi.getID();
          iowHashedIds[item.iowGroupid] = _iowHid;
          // Any IOW Group row can open the Edit Activities popup, scoped
          // to this group, to add/edit/delete its activities.
          _wbsRowWgId[_iowHid] = { iowGroupId: item.iowGroupid || 0 };
          // B. duration span = span of child schedule dates
          var _iowBDur = (item.start_date && item.end_date)
              ? Math.round((new Date(item.end_date) - new Date(item.start_date)) / 86400000) + 1
              : null;
          _groupActDur  [_iowHid] = _iowADur   !== null ? _iowADur   : _iowBDur;
          _groupActStart[_iowHid] = _iowAStart          || safeDate(item.start_date);
          _groupActEnd  [_iowHid] = _iowAEnd            || safeDate(item.end_date);
          // B. columns always show schedule values regardless of bar position
          _groupBDur  [_iowHid] = _iowBDur;
          _groupBStart[_iowHid] = safeDate(item.start_date);
          _groupBEnd  [_iowHid] = safeDate(item.end_date);
          // Accumulate min/max dates into project totals
          if (_iowBDur !== null) {
            var _pbs = safeDate(item.start_date), _pbe = safeDate(item.end_date);
            if (_pbs && (!_projBStartMin || _pbs < _projBStartMin)) _projBStartMin = _pbs;
            if (_pbe && (!_projBEndMax   || _pbe > _projBEndMax  )) _projBEndMax   = _pbe;
          }
          var _iowADurEff = _iowADur !== null ? _iowADur : _iowBDur;
          if (_iowADurEff !== null) {
            var _pas = _iowAStart || safeDate(item.start_date);
            var _pae = _iowAEnd   || safeDate(item.end_date);
            if (_pas && (!_projAStartMin || _pas < _projAStartMin)) _projAStartMin = _pas;
            if (_pae && (!_projAEndMax   || _pae > _projAEndMax  )) _projAEndMax   = _pae;
          }
          g.AddTaskItem(_iowTi);

          // Load activities for this IOW Group (in this project) synchronously.
          $.ajax({
            type: 'POST',
            url: _base + '/ganttactivities',
            dataType: 'json',
            async: false,
            data: { iowGroupId: item.iowGroupid, projectId: projectId },
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
                  // B. Duration = the TRUE original planned duration (old_duration),
                  // never derived from plannedStart/plannedEnd. GetRelationcorrect()
                  // (HelperComponent.php) keeps an activity's START anchored to its
                  // true baseline (act_start_date, never moved) and instead absorbs
                  // any start delay by EXTENDING the duration — so actual_end_date
                  // (plannedEnd) already comes back as baseline_start + (planned
                  // duration + overdue days). Deriving B. Duration from
                  // plannedEnd-plannedStart would read that INFLATED span, making
                  // B. and A. Duration always equal and hiding the delay entirely —
                  // old_duration is the one value that stays the true, un-inflated
                  // plan.
                  var bDur = (parseInt(act.old_duration, 10) > 0) ? parseInt(act.old_duration, 10) : 1;

                  var hasProgress = (act.actual_duration !== null && act.actual_duration !== undefined);
                  var aStart, aEndComputed, actDur;

                  if (!hasProgress) {
                    // No progress reported yet: Actual = Planned, straight from the
                    // server — plannedStart/plannedEnd (actual_start_date/
                    // actual_end_date) are already the fully correct, delay-inclusive
                    // dates (see bDur comment above). actDur is the ACTUAL current
                    // span (plannedEnd - plannedStart + 1) — this will naturally
                    // exceed bDur whenever there's a real start delay, which is what
                    // drives the red delay-overlay below.
                    aStart = plannedStart;
                    aEndComputed = plannedEnd;
                    actDur = (plannedStart && plannedEnd)
                      ? Math.round((new Date(plannedEnd) - new Date(plannedStart)) / 86400000) + 1
                      : bDur;
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
                    null, null, null, '',
                    { unit: act.unit || '', qty: (act.quantity != null ? act.quantity : '') }
                  );
                  window._ganttActRealId[_ti.getID()] = act.id;
                  // Status dot data
                  _actStatus[_ti.getID()] = {
                    completed:   (parseInt(act.completed_status) === 1),
                    hasProgress: (parseFloat(act.cumulated_qty) > 0),
                    planStart:   safeDate(act.actual_start_date),
                    critical:    isCritical
                  };
                  // B. Start/End: act_start_date is the true, never-moved baseline
                  // start; its end is derived from old_duration (bDur), matching the
                  // un-inflated B. Duration above. A. columns show the server's
                  // current actual dates (fall back to B. when no progress).
                  var bStartForCell = safeDate(act.act_start_date) || act.actual_start_date;
                  var bEndForCell = (bStartForCell) ? addDays(bStartForCell, bDur - 1) : act.actual_end_date;
                  _actCells[_ti.getID()] = {
                    dur:    bDur,
                    start:  bStartForCell,
                    end:    bEndForCell,
                    actdur: actDur,
                    astart: aStart || act.actual_start_date,
                    aend:   aEndComputed || act.actual_end_date,
                    iowGroupId: act.iow_group_id,
                    rawId:  act.id,
                    wanId:  act.wan_id,
                    hasProgress: hasProgress
                  };
                  g.AddTaskItem(_ti);
                }
              };
            })(_iowHid)
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

    g.Draw();

    // Exposed so the #gantt-win resize handler (main.php) can switch
    // Day/Week/Month zoom level to fit the window's new width — same
    // g.setFormat() the Format radio buttons already call, just
    // triggered automatically instead of by hand.
    window._ganttChartObj = g;

    // Re-applied after every draw (initial load and each format change), since
    // setFormat() redraws the table and wipes the injected columns and overlays.
    function _decorateGantt() {
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

    if (_durHdr) { _durHdr.textContent = 'B. Duration'; _durHdr.title = 'Planned Start + Duration. Fixed until the user changes the planned start date or duration.'; }
    if (_resHdr) { _resHdr.textContent = 'A. Duration'; _resHdr.title = 'If no progress reported: Planned Duration + Start Delay. If progress reported: Elapsed Days ÷ Quantity Done × Total Quantity.'; }

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

      // Indentation on the activity name cell. Project -> IOW Group ->
      // Activity is only 3 levels now (no WBS-item tier in between).
      // Activity (level 2) sits a step further right than its parent
      // IOW Group (level 1), same convention as the project row above
      // it — just a smaller step now that there's one less tier.
      var _cell = _row.querySelector('td.gtaskname');
      if (_cell) {
        var _indent = [0, 18, 60, 60, 60][_t.getLevel()] || 60;
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

      // IOW Group row: drag handle for reordering whole groups (and
      // everything under them) within the project — same press-and-hold
      // mechanism as the activity drag handle below, just moving a
      // group instead of a single activity. Placed in the same
      // dedicated dot/handle column so both row types line up.
      var _groupInfo = _wbsRowWgId[_tid];
      if (_groupInfo && _dotCell && !_dotCell.querySelector('.giow-reorder-handle')) {
        var _gHandle = document.createElement('span');
        _gHandle.className = 'giow-reorder-handle';
        _gHandle.title = 'Press and drag to reorder this IOW Group';
        _gHandle.textContent = '⋮⋮';
        _gHandle.setAttribute('data-tid', _tid);
        _gHandle.setAttribute('data-iowgroupid', _groupInfo.iowGroupId);
        _dotCell.appendChild(_gHandle);
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
    _decorateGantt();

    // Any format change (toolbar buttons, header Format links, window resize)
    // goes through setFormat(), so re-apply the decorations after it redraws.
    var _origSetFormat = g.setFormat;
    g.setFormat = function(f) { _origSetFormat.call(g, f); _decorateGantt(); };
  }

  // ── Reorder activities within one WBS item — press-and-hold drag ───────────
  // Same raw mousedown/mousemove/mouseup shape as the KPI/Cost floating
  // panels' own drag (see "Drag + Resize: KPI modal" above) — NOT jQuery UI
  // sortable, which was tried first and never reliably engaged against
  // JSGantt's row structure. Bound once via delegation on document (not
  // per-row), so it keeps working across every loadGantt() reload without
  // needing to re-bind. Reordering only ever changes
  // workgroup_activities_new.sortorder — no dates are touched, and there is
  // no relationship-based restriction (see actionReordertaskswithinwbs).
  (function(){
    var $ghost = $('#gact-drag-ghost');
    var _dragTid = null, _dragIowGroupId = null, _dragRow = null;
    var _dropRow = null;

    function _rowFromPoint(x, y) {
      // The activity list (left, name/duration columns) and the bar chart
      // (right, task bars) are separate DOM trees in JSGantt — a point
      // over the chart side resolves through .gtaskbarcontainer/other
      // chart layers, never through a list-table <tr>, so a plain
      // elementFromPoint()+closest('tr') on that side always returns null.
      // Same class of bug as the relationship-line click handler above
      // (see the elementsFromPoint note there) — use the row's Y position
      // via the LIST table specifically instead of trusting whichever
      // element happens to be topmost at this point.
      var listRows = document.querySelectorAll('#gantt-container .gtasktablewrapper tr[id*="child_"]');
      for (var i = 0; i < listRows.length; i++) {
        var r = listRows[i];
        if (r.id.indexOf('childrow_') !== -1) continue;
        var rect = r.getBoundingClientRect();
        if (y >= rect.top && y < rect.bottom) return r;
      }
      return null;
    }

    $(document).off('mousedown.gactdrag').on('mousedown.gactdrag', '#gantt-container .gact-reorder-handle', function(e) {
      var tid = $(this).attr('data-tid');
      var db = (window._ganttActCells || {})[tid];
      if (!db) return;
      var row = document.getElementById('gantt-container' + 'child_' + tid);
      if (!row) return;

      _dragTid = tid;
      _dragIowGroupId = db.iowGroupId;
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
        // Any other leaf-activity row is a valid drop target now — same
        // WBS item reorders in place, a different WBS item/IOW moves the
        // activity there (see mouseup below).
        if (rowDb) {
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

      var dragTid = _dragTid, iowGroupId = _dragIowGroupId, dropRow = _dropRow;
      _dragTid = null; _dragIowGroupId = null; _dragRow = null; _dropRow = null;
      if (!dropRow) return; // released outside a valid activity row — no-op

      var cells = window._ganttActCells || {};
      var dropTid = dropRow.id.slice(dropRow.id.indexOf('child_') + 'child_'.length);
      var dropDb = cells[dropTid];
      if (!dropDb) return;

      if (dropDb.iowGroupId === iowGroupId) {
        // Same IOW Group — reorder within it. Build the new order: every
        // activity in this group (this project), in current DOM order,
        // with the dragged one removed and reinserted at the drop row's
        // position.
        var orderedTids = [];
        $('#gantt-container .gtasktablewrapper tr[id*="child_"]').each(function() {
          if (this.id.indexOf('childrow_') !== -1) return;
          var rowTid = this.id.slice(this.id.indexOf('child_') + 'child_'.length);
          var rowDb = cells[rowTid];
          if (rowDb && rowDb.iowGroupId === iowGroupId) orderedTids.push(rowTid);
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
          data: { iow_group_id: iowGroupId, project_id: projectId, wan_ids: wanIds },
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
      } else {
        // Different IOW Group — move the activity there, placed right
        // after the row it was dropped on.
        $.ajax({
          type: 'POST',
          url: _base + '/moveactivitytoiow',
          dataType: 'json',
          data: {
            wan_id: cells[dragTid].wanId,
            target_iow_group_id: dropDb.iowGroupId,
            drop_after_wan_id: dropDb.wanId
          },
          success: function(resp) {
            if (!resp || resp.error !== 'No') {
              alert((resp && resp.errortext) || 'Could not move — please try again.');
              return;
            }
            if (typeof window.loadGantt === 'function') window.loadGantt();
          },
          error: function() {
            alert('Network error — could not save the move.');
          }
        });
      }
    });
  })();

  // ── Reorder whole IOW Groups — press-and-hold drag ──────────────────────────
  // Same raw mousedown/mousemove/mouseup shape as the activity-reorder drag
  // above, moving an entire IOW Group (header row + all its activities)
  // instead of a single activity. Only iow_group_order.sortorder changes —
  // no activity dates, relationships, or iow_group_id are touched.
  (function(){
    var $ghost = $('#gact-drag-ghost');
    var _dragGid = null, _dragRow = null;
    var _dropRow = null;

    function _groupRowFromPoint(x, y) {
      var listRows = document.querySelectorAll('#gantt-container .gtasktablewrapper tr[id*="child_"]');
      for (var i = 0; i < listRows.length; i++) {
        var r = listRows[i];
        if (r.id.indexOf('childrow_') !== -1) continue;
        var rect = r.getBoundingClientRect();
        if (y >= rect.top && y < rect.bottom) return r;
      }
      return null;
    }

    $(document).off('mousedown.giowdrag').on('mousedown.giowdrag', '#gantt-container .giow-reorder-handle', function(e) {
      var tid = $(this).attr('data-tid');
      var gid = $(this).attr('data-iowgroupid');
      var row = document.getElementById('gantt-container' + 'child_' + tid);
      if (!row || !gid) return;

      _dragGid = gid;
      _dragRow = row;
      $(this).addClass('giow-dragging');

      var nameCell = row.querySelector('td.gtaskname');
      $ghost.text(nameCell ? nameCell.textContent.trim() : 'IOW Group')
        .css({ left: (e.clientX + 14) + 'px', top: (e.clientY + 10) + 'px', display: 'block' });

      e.preventDefault();
    });

    $(document).off('mousemove.giowdrag').on('mousemove.giowdrag', function(e) {
      if (!_dragGid) return;
      $ghost.css({ left: (e.clientX + 14) + 'px', top: (e.clientY + 10) + 'px' });

      if (_dropRow) { $(_dropRow).removeClass('giow-drop-target'); _dropRow = null; }
      var row = _groupRowFromPoint(e.clientX, e.clientY);
      if (row && row !== _dragRow) {
        var rowTid = row.id.slice(row.id.indexOf('child_') + 'child_'.length);
        var rowInfo = (window._ganttWbsBareRowWgId || {})[rowTid];
        // Only another IOW Group header row is a valid drop target —
        // dropping on an activity row does nothing.
        if (rowInfo) {
          _dropRow = row;
          $(row).addClass('giow-drop-target');
        }
      }
    });

    $(document).off('mouseup.giowdrag').on('mouseup.giowdrag', function(e) {
      if (!_dragGid) return;
      $('.giow-reorder-handle.giow-dragging').removeClass('giow-dragging');
      $ghost.css('display', 'none');
      if (_dropRow) $(_dropRow).removeClass('giow-drop-target');

      var dragGid = _dragGid, dropRow = _dropRow;
      _dragGid = null; _dragRow = null; _dropRow = null;
      if (!dropRow) return; // released outside a valid IOW Group row — no-op

      var dropTid = dropRow.id.slice(dropRow.id.indexOf('child_') + 'child_'.length);
      var dropInfo = (window._ganttWbsBareRowWgId || {})[dropTid];
      if (!dropInfo || !dropInfo.iowGroupId) return;
      var dropGid = String(dropInfo.iowGroupId);
      if (dropGid === dragGid) return;

      // Build the new group order: every IOW Group header currently in
      // the DOM, top to bottom, with the dragged one removed and
      // reinserted at the drop row's position.
      var orderedGids = [];
      $('#gantt-container .gtasktablewrapper tr[id*="child_"]').each(function() {
        if (this.id.indexOf('childrow_') !== -1) return;
        var rowTid = this.id.slice(this.id.indexOf('child_') + 'child_'.length);
        var info = (window._ganttWbsBareRowWgId || {})[rowTid];
        if (info && info.iowGroupId) orderedGids.push(String(info.iowGroupId));
      });
      var fromIdx = orderedGids.indexOf(dragGid);
      var toIdx = orderedGids.indexOf(dropGid);
      if (fromIdx === -1 || toIdx === -1 || fromIdx === toIdx) return;
      orderedGids.splice(fromIdx, 1);
      toIdx = orderedGids.indexOf(dropGid);
      orderedGids.splice(toIdx, 0, dragGid);

      $.ajax({
        type: 'POST',
        url: _base + '/reorderiowgroups',
        dataType: 'json',
        data: { project_id: projectId, 'iow_group_ids[]': orderedGids },
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

    function _gauge(elId, val, maxVal, trackStyle, targetFrac, lbl1, v1, lbl2, v2, targetVal, extraLbl, centreOverrideVal, hideTickWhenNotOver) {
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
      // esttarget: the Estimate is the fixed target sitting at dial centre
      // (always 50%, by construction — maxVal is always 2×Estimate at the
      // one call site that uses this mode) — track is green on the left
      // half (Actual would land under-target there) and red on the right
      // half (over-target), and the needle/centre-dot colour itself
      // switches red/green live based on whether Actual is currently over
      // or under that Estimate, instead of a fixed colour by dial position.
      var isEstTarget = (trackStyle === 'esttarget');
      // esttarget-prop: same green/orange target-line mechanic as
      // esttarget, but the target (Planned Duration) sits at whatever
      // fraction of the dial it actually falls at (targetFrac), rather
      // than always dead-centre — maxVal is the larger of Planned/Actual
      // here, not a fixed 2×target, so the target line recedes left as
      // Actual grows past Planned instead of staying pinned at 50%.
      var isEstTargetProp = (trackStyle === 'esttarget-prop');
      var isAnyEstTarget = isEstTarget || isEstTargetProp;
      var tf = isEstTargetProp ? Math.max(0, Math.min(1, +targetFrac || 0)) : 0.5;
      var overTarget = isAnyEstTarget && (val > (+targetVal || 0));
      var underTarget = isAnyEstTarget && (val < (+targetVal || 0));
      var trackSvg = (trackStyle === 'flat') ? arc(0, 1, '#a8d4f5')
        : (trackStyle === 'cost') ? arc(0, 0.5, '#00838f') + arc(0.5, 1, '#FF6D00')
        : (trackStyle === 'qtygreen' || trackStyle === 'qtypercent') ? arc(0, 0.5, '#FF6D00') + arc(0.5, 1, '#27ae60')
        : (trackStyle === 'costgreen' || trackStyle === 'costpercent') ? arc(0, 0.5, '#27ae60') + arc(0.5, 1, '#FF6D00')
        // esttarget-prop: orange-red only fills the segment beyond the
        // target tick when Actual genuinely exceeds it (overTarget) — the
        // scale still stretches (×1.1 headroom) to fit whichever of
        // val/targetVal is larger even when there's no overrun, so without
        // this guard the segment from tf→1 would always paint orange even
        // when nothing is actually over (e.g. Work Done's Done=0,
        // Sched=5200 case). When not over, that same segment stays the
        // grey base colour instead of being left unpainted.
        : isEstTargetProp ? arc(0, tf, '#5a6b82') + arc(tf, 1, overTarget ? '#e64a19' : '#5a6b82')
        : isEstTarget ? arc(0, 0.5, '#27ae60') + arc(0.5, 1, '#e64a19')
        : arc(0, 0.5, '#8B0000') + arc(0.5, 1, '#81C784');
      var nr = r - 15, na = Math.PI * (1 - f);
      var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
      var fillCol = (trackStyle === 'flat') ? '#0d1f6e' : (trackStyle === 'cost' || trackStyle === 'costgreen' || trackStyle === 'qtygreen' || trackStyle === 'costpercent' || trackStyle === 'qtypercent') ? '#1b9e8e' : '#1a3a6b';
      var dotCol  = (trackStyle === 'flat') ? '#a8d4f5' : isAnyEstTarget ? (overTarget ? '#e64a19' : underTarget ? (isEstTargetProp ? '#5a6b82' : '#27ae60') : '#dce3ef') : '#dce3ef';
      var needleCol = isAnyEstTarget ? '#000' : '#333';
      var actValCol = isAnyEstTarget ? '#000' : '#333';
      var centreVal = isEstTargetProp ? _fm(+targetVal || 0) : isEstTarget ? _fmtCost(+targetVal || 0) : (trackStyle === 'costpercent' || trackStyle === 'qtypercent') ? (Math.round(val) + '%') : (trackStyle === 'cost' || trackStyle === 'costgreen') ? _fmtCost(val) : _fm(val);
      var lblSvg = '';
      if (lbl1) lblSvg += '<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">' + lbl1 + ' <tspan font-weight="700">' + v1 + '</tspan></text>';
      if (lbl2) lblSvg += '<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">' + lbl2 + ' <tspan font-weight="700">' + v2 + '</tspan></text>';
      // esttarget-prop only, and only when the caller explicitly passes
      // extraLbl (e.g. "Elapsed 80 d" for the Elapsed Days dial) — a third
      // row below Planned/Actual, centred, on its own y so it never
      // crowds against them. Not every esttarget-prop dial has a third
      // figure worth showing (Work Done doesn't), so this stays opt-in.
      if (isEstTargetProp && extraLbl) lblSvg += '<text x="' + cx + '" y="148" text-anchor="middle" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">' + extraLbl + '</text>';
      // esttarget: a yellow tick marking the exact target point on the
      // track — dead centre (top of dial) for the fixed-50% mode, or at
      // the computed targetFrac position for esttarget-prop.
      var targetTickSvg = '';
      if (isEstTarget) {
        targetTickSvg = '<line x1="' + cx + '" y1="' + (cy - r - 4) + '" x2="' + cx + '" y2="' + (cy - r + sw + 4) + '" stroke="#FFD500" stroke-width="3" stroke-linecap="round"/>';
      } else if (isEstTargetProp && !(hideTickWhenNotOver && !overTarget)) {
        var ta = Math.PI * (1 - tf);
        var tcos = Math.cos(ta), tsin = Math.sin(ta);
        var tx1 = (cx + (r - sw / 2 - 4) * tcos).toFixed(1), ty1 = (cy - (r - sw / 2 - 4) * tsin).toFixed(1);
        var tx2 = (cx + (r + sw / 2 + 4) * tcos).toFixed(1), ty2 = (cy - (r + sw / 2 + 4) * tsin).toFixed(1);
        targetTickSvg = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#FFD500" stroke-width="3" stroke-linecap="round"/>';
        // Planned-value label sitting just past the tick's outer end,
        // outside the dial arc — anchor flips left/right/centre depending
        // on which side of the dial the tick falls so the text never runs
        // off the edge.
        var tlx = (cx + (r + sw / 2 + 14) * tcos).toFixed(1), tly = Math.max(11, cy - (r + sw / 2 + 14) * tsin).toFixed(1);
        var tAnchor = tcos > 0.15 ? 'start' : (tcos < -0.15 ? 'end' : 'middle');
        targetTickSvg += '<text x="' + tlx + '" y="' + tly + '" text-anchor="' + tAnchor + '" dominant-baseline="middle" font-size="17" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + _fm(+targetVal || 0) + '</text>';
      }
      var needleWidth = isAnyEstTarget ? 4 : 3;
      var pivotCol = isAnyEstTarget ? '#000' : '#555';
      // esttarget only: Actual shown as a second, smaller line directly
      // under the Estimate figure at dial centre, in the same black as
      // the target text/needle — the track halves and centre-dot are
      // what carry the over/under signal now, not the text or needle.
      var actualCentreSvg = isEstTarget
        ? '<text x="' + cx + '" y="' + (cy - 14) + '" text-anchor="middle" font-size="16" font-weight="700" fill="' + actValCol + '" font-family="Barlow Condensed,Arial">Act ' + _fmtCost(val) + '</text>'
        : '';
      // esttarget-prop: dial centre shows centreOverrideVal if the caller
      // passed one (e.g. Elapsed Days shows Actual Duration at centre,
      // distinct from the needle's own Elapsed Days value), otherwise
      // falls back to the needle's own value (val) — correct default for
      // Work Done, where centre and needle both mean the same "Done" qty.
      var centreDisplayVal = (centreOverrideVal !== undefined && centreOverrideVal !== null) ? +centreOverrideVal : val;
      var centreValSvg = isEstTargetProp
        ? '<text x="' + cx + '" y="' + (cy - 14) + '" text-anchor="middle" font-size="16" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">Act ' + _fm(centreDisplayVal) + '</text>'
        : '<text x="' + cx + '" y="' + (cy - (isAnyEstTarget ? 32 : 18)) + '" text-anchor="middle" font-size="' + (isAnyEstTarget ? 16 : 22) + '" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">' + centreVal + '</text>';
      // esttarget-prop only: Elapsed Days label at the needle's tip,
      // just outside the dial track — the needle itself points to the
      // Elapsed Days fraction, so its own value is shown right there
      // rather than only at dial centre (which now reads Actual Duration).
      var needleTipSvg = '';
      if (isEstTargetProp) {
        var ntx = (cx + (r + sw / 2 + 12) * Math.cos(na)).toFixed(1), nty = (cy - (r + sw / 2 + 12) * Math.sin(na)).toFixed(1);
        var ntAnchor = Math.cos(na) > 0.15 ? 'start' : (Math.cos(na) < -0.15 ? 'end' : 'middle');
        needleTipSvg = '<text x="' + ntx + '" y="' + nty + '" text-anchor="' + ntAnchor + '" dominant-baseline="middle" font-size="13" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + _fm(val) + '</text>';
      }
      // esttarget-prop needs extra vertical room for the Elapsed-Days row
      // sitting below Planned/Actual, so it never crowds or clips.
      var svgH = isEstTargetProp ? 138 : 120;
      var svgVB = isEstTargetProp ? '0 0 210 156' : '0 0 210 138';
      var svg = '<svg width="100%" height="' + svgH + '" viewBox="' + svgVB + '" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" style="display:block;overflow:hidden;flex-shrink:0;">'
        + trackSvg
        + ((f > 0 && !isAnyEstTarget && trackStyle !== 'costgreen' && trackStyle !== 'qtygreen' && trackStyle !== 'costpercent' && trackStyle !== 'qtypercent') ? arc(0, f, fillCol, (trackStyle === 'cost' ? 'butt' : 'round')) : '')
        + targetTickSvg
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="' + needleCol + '" stroke-width="' + needleWidth + '" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="' + pivotCol + '"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="' + dotCol + '"/>'
        + centreValSvg
        + actualCentreSvg
        + needleTipSvg
        + lblSvg
        + '</svg>';
      el.innerHTML += svg;
    }

    // 1. Unit Cost of Resources → gm-cd-ucr — one horizontal bar per
    // distinct Resource Type allocated to the activity (Materials,
    // Support Resources, etc.), count of resources under that type as
    // the bar's value, drawn as a proper axis chart (gridlines + value
    // ticks) like the Resource Groups popup one level down.
    var _ucrTipItems = [];
    var _UCR_BAR_COLS = ['#3461b8','#d4845a','#27afc4','#7c5cbf','#f0c419','#8fa3bc','#ec407a','#26a69a'];
    // Base-bar palette for the Est/Act (Resources) chart specifically —
    // deliberately excludes orange-red/teal hues (#e64a19 / #1b9e8e are the
    // fixed over/under-estimate overlay colours) so a resource's base bar
    // never visually merges with its own overrun or saving indicator.
    var _UCR_ESTACT_COLS = ['#3461b8','#7c5cbf','#2d6ea5','#5b4b8a','#4472c4','#8064a2','#376092','#5c3d99'];

    // "Nice" axis step: pick a round number (1/2/5 × 10^n) so gridlines land
    // on clean values instead of awkward fractions of the max value.
    function _ucrNiceStep(maxVal, targetTicks) {
      if (maxVal <= 0) return 1;
      var rough = maxVal / targetTicks;
      var mag = Math.pow(10, Math.floor(Math.log(rough) / Math.LN10));
      var norm = rough / mag;
      var step = (norm <= 1) ? 1 : (norm <= 2) ? 2 : (norm <= 5) ? 5 : 10;
      return step * mag;
    }

    // Shared horizontal bar chart: `rows` = [{name, value}], category
    // names on the left (Y axis), value axis along the bottom (X axis)
    // with gridlines at clean tick intervals — matches a standard
    // horizontal bar-graph layout (axis line, tick marks, gridlines).
    function _renderHBarChart(rows, opts) {
      opts = opts || {};
      var onClick = opts.onClick || '';
      var labelW = opts.labelW || 90;
      var labelCls = opts.bigLabels ? ' gm-hchart-rowlbl-lg' : '';
      var fam = "font-family:'Barlow Condensed',sans-serif;";
      if (!rows.length) return '<div class="gm-bar-tip-empty">No data</div>';

      var showOverrun = !!opts.showOverrun;
      var maxVal = 0;
      rows.forEach(function(r) {
        var total = (+r.value || 0) + (showOverrun ? (+r.overCount || 0) : 0);
        maxVal = Math.max(maxVal, total);
      });
      if (maxVal <= 0) maxVal = 1;
      var step = _ucrNiceStep(maxVal, 4);
      // Whole-number data (e.g. a count of resources) should never show
      // fractional gridlines like 0.5 — round the step up to at least 1.
      if (opts.integerTicks) step = Math.max(1, Math.round(step));
      var axisMax = Math.ceil(maxVal / step) * step;
      if (axisMax <= 0) axisMax = step;
      var ticks = [];
      for (var t = 0; t <= axisMax + 1e-9; t += step) ticks.push(t);

      var html = opts.yTitle ? '<div class="gm-hchart-withytitle"><div class="gm-hchart-ytitle">' + opts.yTitle + '</div><div class="gm-hchart-main">' : '';
      html += '<div class="gm-hchart' + (opts.smallFont ? ' gm-hchart-sm' : '') + '" style="--gm-axis-w:' + labelW + 'px;">';
      html += '<div class="gm-hchart-plot">';
      html += '<div class="gm-hchart-grid">';
      ticks.forEach(function(tv) { html += '<div class="gm-hchart-gridline" style="left:' + ((tv / axisMax) * 100) + '%;"></div>'; });
      html += '</div><div class="gm-hchart-rows">';
      rows.forEach(function(r, i) {
        var val = +r.value || 0;
        var overCount = showOverrun ? (+r.overCount || 0) : 0;
        var pct = Math.max(0, Math.min(100, (val / axisMax) * 100));
        var overPct = Math.max(0, Math.min(100 - pct, (overCount / axisMax) * 100));
        var col = _UCR_BAR_COLS[i % _UCR_BAR_COLS.length];
        var cls = onClick ? (' ' + onClick + '-bar') : '';
        var hasOver = overPct > 0;
        var valLbl = _fmtFull(val) + (hasOver ? (' + ' + overCount) : '');
        var barH = opts.barHeight || 12;
        var trackStyle = opts.barHeight ? ('height:' + barH + 'px;') : '';
        var rowInlineStyle = (opts.barHeight ? ('height:' + (barH + 8) + 'px;') : '') + (onClick ? 'cursor:pointer;' : '');
        var overSeg = hasOver
          ? '<div class="gm-hchart-bar-over" style="left:' + pct + '%;width:' + overPct + '%;height:' + barH + 'px;" title="' + overCount + ' over estimate">' + (opts.hideBarVal ? '' : '<span class="gm-hchart-barval">' + valLbl + '</span>') + '</div>'
          : '';
        var barStyle = 'width:' + pct + '%;height:' + barH + 'px;background:' + (opts.singleColor || col) + ';' + (hasOver ? 'border-radius:0;' : '');
        var baseVal = (hasOver || opts.hideBarVal) ? '' : '<span class="gm-hchart-barval">' + valLbl + '</span>';
        html += '<div class="gm-hchart-row' + cls + '"' + (rowInlineStyle ? ' style="' + rowInlineStyle + '"' : '') + (onClick ? ' data-name="' + $('<span>').text(r.name).html() + '"' : '') + '>'
          + '<div class="gm-hchart-rowlbl' + labelCls + '" title="' + $('<span>').text(r.name).html() + '">' + $('<span>').text(r.name).html() + '</div>'
          + '<div class="gm-hchart-rowtrack" style="' + trackStyle + '"><div class="gm-hchart-bar" style="' + barStyle + '">' + baseVal + '</div>' + overSeg + '</div>'
          + '</div>';
      });
      html += '</div></div>';
      html += '<div class="gm-hchart-xaxis"><div class="gm-hchart-xaxis-ticks">';
      ticks.forEach(function(tv) { html += '<div class="gm-hchart-xtick" style="left:' + ((tv / axisMax) * 100) + '%;">' + _fmtFull(tv) + '</div>'; });
      html += '</div></div>';
      if (opts.xTitle) html += '<div class="gm-hchart-xtitle">' + opts.xTitle + '</div>';
      html += '</div>';
      if (opts.yTitle) html += '</div></div>';
      return html;
    }

    // Shared vertical bar chart: `rows` = [{name, value}] OR, when
    // opts.estAct is true, [{name, est, act, hasActual}] — category names
    // on the X axis (bottom), value axis on the left (Y axis) with
    // horizontal gridlines at clean tick intervals, value labelled on top
    // of each bar. In est/act mode, the bar is drawn to the Estimate
    // height with an overlay segment on top: orange extension above the
    // bar when Actual > Estimate (over budget), teal segment marking
    // where Actual sits within the bar when Actual < Estimate (under
    // budget) — same over/under convention as the rest of this popup.
    function _renderVBarChart(rows, opts) {
      opts = opts || {};
      if (!rows.length) return '<div class="gm-bar-tip-empty">No data</div>';
      var fmtBase = opts.fmt || _fmtFull;
      // valUnit: quantity/rate charts pass their unit (Sqm, Metre, …) so
      // every bar's value label carries it — cost/currency charts leave
      // this unset and show plain numbers, since ₹ amounts have no unit.
      // valUnitSep lets a rate chart use "/Sqm" instead of the default
      // " Sqm" separator, so a per-unit rate never reads like a quantity.
      var fmt = opts.valUnit ? function(v){ return fmtBase(v) + (opts.valUnitSep || ' ') + opts.valUnit; } : fmtBase;

      var maxVal = 0;
      rows.forEach(function(r) {
        if (opts.estAct) maxVal = Math.max(maxVal, +r.est || 0, +r.act || 0);
        else maxVal = Math.max(maxVal, (+r.value || 0) + (opts.showOverrun ? (+r.overCount || 0) : 0));
      });
      if (maxVal <= 0) maxVal = 1;
      var step = _ucrNiceStep(maxVal, 5);
      if (opts.integerTicks) step = Math.max(1, Math.round(step));
      var axisMax = Math.ceil(maxVal / step) * step;
      if (axisMax <= 0) axisMax = step;
      var ticks = [];
      for (var t = 0; t <= axisMax + 1e-9; t += step) ticks.push(t);
      ticks.reverse(); // top → bottom for the Y-axis label column

      var html = '<div class="gm-vchart">';
      html += '<div class="gm-vchart-plot">';
      html += '<div class="gm-vchart-yaxis">';
      ticks.forEach(function(tv) { html += '<div class="gm-vchart-ytick">' + fmtBase(tv) + '</div>'; });
      html += '</div>';
      html += '<div class="gm-vchart-area">';
      html += '<div class="gm-vchart-grid">';
      ticks.forEach(function(tv) { html += '<div class="gm-vchart-gridline" style="bottom:' + ((tv / axisMax) * 100) + '%;"></div>'; });
      html += '</div>';
      html += '<div class="gm-vchart-bars">';
      rows.forEach(function(r, i) {
        var col = _UCR_BAR_COLS[i % _UCR_BAR_COLS.length];
        if (opts.estAct) {
          var est = +r.est || 0, act = +r.act || 0;
          var over  = r.hasActual && act > est;
          var under = r.hasActual && act < est;
          var totalPct = Math.max(0, Math.min(100, (Math.max(est, act) / axisMax) * 100));
          var diffAbsPct = Math.max(0, (Math.abs(act - est) / axisMax) * 100);
          // .gm-vchart-diff sits inside a bar sized to totalPct of the chart —
          // its own height must be expressed as a % OF THE BAR, not of the
          // chart, since CSS % height resolves against the parent element.
          var diffPctOfBar = (totalPct > 0) ? Math.min(100, (diffAbsPct / totalPct) * 100) : 0;
          var diffCol = over ? '#e64a19' : (under ? '#1b9e8e' : '');
          var diffSeg = (r.hasActual && diffPctOfBar > 0) ? '<div class="gm-vchart-diff" style="height:' + diffPctOfBar + '%;background:' + diffCol + ';"></div>' : '';
          // When Actual is within/under Estimate: Estimate on top, full
          // Actual value below. When Actual exceeds Estimate: full Actual
          // value on top in red, Estimate below — so the higher figure
          // always reads first, and the bar's own colour signal is echoed
          // in the number itself.
          var hideVal = opts.hideBarVal ? true : false;
          var estLbl = hideVal ? '' : '<span class="gm-vchart-barval">' + fmt(est) + '</span>';
          var actLblCol = over ? '#e64a19' : (under ? '#1b9e8e' : '#1a2540');
          var actLbl = hideVal ? '' : ((r.hasActual) ? '<span class="gm-vchart-barval-diff" style="color:' + actLblCol + ';">' + fmt(act) + '</span>' : '');
          var barValHtml = over ? (actLbl + estLbl) : (estLbl + actLbl);
          var colCls = opts.onClick ? (' ' + opts.onClick + '-bar') : '';
          var colAttr = opts.onClick ? (' data-name="' + $('<span>').text(r.name).html() + '" style="cursor:pointer;"') : '';
          // Each resource keeps its own base colour (from the orange/teal-
          // free _UCR_ESTACT_COLS palette) so bars stay visually distinct,
          // while the over/under overlay colour never collides with it.
          var baseCol = _UCR_ESTACT_COLS[i % _UCR_ESTACT_COLS.length];
          html += '<div class="gm-vchart-col' + colCls + '"' + colAttr + ' title="' + $('<span>').text(r.name).html() + ': ' + fmt(est) + (r.hasActual ? ' (Act: ' + fmt(act) + ')' : '') + '">'
            + '<div class="gm-vchart-bar-wrap" style="height:' + totalPct + '%;">'
            +   barValHtml
            +   '<div class="gm-vchart-bar" style="background:' + (opts.singleColor || baseCol) + ';">' + diffSeg + '</div>'
            + '</div></div>';
        } else {
          var val = +r.value || 0;
          var overCnt = opts.showOverrun ? (+r.overCount || 0) : 0;
          var pct = Math.max(0, Math.min(100, ((val + overCnt) / axisMax) * 100));
          var overPctOfBar = (val + overCnt) > 0 ? (overCnt / (val + overCnt)) * 100 : 0;
          var overSeg = overCnt > 0 ? '<div class="gm-vchart-diff" style="height:' + overPctOfBar + '%;background:#e64a19;" title="' + overCnt + ' over estimate"></div>' : '';
          var colCls = opts.onClick ? (' ' + opts.onClick + '-bar') : '';
          var colAttr = opts.onClick ? (' data-name="' + $('<span>').text(r.name).html() + '" style="cursor:pointer;"') : '';
          var hideVal = opts.hideBarVal ? true : false;
          html += '<div class="gm-vchart-col' + colCls + '"' + colAttr + ' title="' + $('<span>').text(r.name).html() + ': ' + fmt(val) + (overCnt > 0 ? ' + ' + overCnt : '') + '">'
            + '<div class="gm-vchart-bar-wrap" style="height:' + pct + '%;">'
            +   (hideVal ? '' : '<span class="gm-vchart-barval">' + fmt(val) + (overCnt > 0 ? ' + ' + overCnt : '') + '</span>')
            +   '<div class="gm-vchart-bar" style="background:' + (opts.singleColor || col) + ';">' + overSeg + '</div>'
            + '</div></div>';
        }
      });
      html += '</div></div></div>';
      html += '<div class="gm-vchart-xaxis"><div class="gm-vchart-xaxis-spacer"></div><div class="gm-vchart-xlabels">';
      rows.forEach(function(r) { html += '<div class="gm-vchart-xlbl" title="' + $('<span>').text(r.name).html() + '">' + $('<span>').text(r.name).html() + '</div>'; });
      html += '</div></div>';
      if (opts.yTitle) html += '<div class="gm-vchart-ytitle">' + opts.yTitle + '</div>';
      html += '</div>';
      return html;
    }

    // Shared renderer for the three Type-bar panels (Unit Cost of
    // Resources / Yield / Cost of Resources) — all three now use the
    // same Type→Group→Resource bar-chart structure; only the metric's
    // own est/act/over formula (see _UCR_METRICS) differs between them.
    function _renderUcrTypePanel(elId, metric, items, actName) {
      var el = document.getElementById(elId);
      if (!el) return;
      if (!items.length) { el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No resources allocated</div>'; return; }

      var rows = _ucrTypesFor(metric);
      var html = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + _sh(actName||'', 40) + '</div>';
      // Centred vertically within the panel (justify-content:center) so a
      // single bar sits mid-panel, and the whole stack stays centred as
      // more Resource Types are added — growing evenly instead of pinned
      // to the top. overflow-y:auto covers the case where it eventually
      // outgrows the panel's own height.
      var chartOpts = { onClick: 'gm-ucr-type', integerTicks: true, showOverrun: true, hideBarVal: true };
      var anyOver = rows.some(function(r){ return (+r.overCount || 0) > 0; });
      var chartHtml = '<div style="display:flex;flex-direction:column;flex:1;min-height:150px;">'
        + _renderVBarChart(rows, chartOpts)
        + (anyOver ? '<div class="gm-vchart-legend gm-vchart-legend-sm"><span><i style="background:#e64a19;"></i>Over estimate</span></div>' : '')
        + '</div>';
      html += '<div data-metric="' + metric + '" style="width:100%;box-sizing:border-box;padding:8px 10px;flex:1;min-height:0;display:flex;flex-direction:column;justify-content:center;align-items:center;overflow-y:auto;">'
        + chartHtml
        + '</div>';
      el.innerHTML = html;
    }

    function _renderUnitCostOfResource(items, actName) {
      items = items || [];
      _ucrTipItems = items;
      _renderUcrTypePanel('gm-cd-ucr', 'ucr', items, actName);
    }

    /* ═══════════════════════════════════════════════════════════════
       Shared Type → Group → Resource drill-down popup (click-driven),
       used by all three resource-based panels — Unit Cost of Resources,
       Yield, and Cost of Resources. Each panel supplies its own items
       array and its own value/over-estimate formulas via _UCR_METRICS
       below; none of those formulas are touched here — this block only
       controls how the numbers are grouped and drawn (Type bars → Group
       popup → per-resource popup with vertical Est/Act bars).
    ═══════════════════════════════════════════════════════════════ */
    var _ucrDrillLevel = 'groups'; // 'groups' | 'resources'
    var _ucrDrillMetric = 'ucr';
    var _ucrDrillType = '';
    var _ucrDrillGroup = '';

    // Shared by rcost's isOver/estAct below — a top-level function rather
    // than an object method because isOver is called detached (e.g.
    // resRows.filter(m.isOver)), which would lose a `this` binding.
    function _rcostHasActual(r) {
      var typeId = +r.type_id;
      var isMaterial = [2, 6, 7, 8].indexOf(typeId) >= 0;
      var isSC = typeId === 4;
      var hasActualRate = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
      var hasActualQty  = (isMaterial && r.indent_raised) || (isSC && r.has_mb) || (!isMaterial && !isSC && r.has_acc_head);
      return hasActualRate || hasActualQty;
    }

    // One entry per panel. `items()` returns that panel's current items
    // array (already computed by its own _render* function below).
    // `isOver(r)` / `estAct(r)` reuse each panel's own existing formula —
    // copied verbatim from its dial/tooltip code, not reformulated here.
    var _UCR_METRICS = {
      ucr: {
        // A Support Resource (has_acc_head) with no transaction posted yet
        // to its Account Head has no actual value at all for this metric —
        // it's excluded everywhere Unit Cost of Activity/Resources draws
        // from this list (Type bars, Group bars, Resource drill-down)
        // until a real expense appears against that account.
        items: function(){
          return _ucrTipItems.filter(function(r){
            var hasActual = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
            return !(r.has_acc_head && !hasActual);
          });
        },
        // A resource counts as "over budget" only once it actually has a
        // recorded actual unit cost (GRN/purchase data) that exceeds its
        // estimated rate — no actual yet is not an overrun, it's unknown.
        isOver: function(r){
          var hasActual = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
          return hasActual && (+r.actual_unit_cost > +r.rate);
        },
        estAct: function(r){
          var hasActual = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
          return { est: +r.rate || 0, act: hasActual ? +r.actual_unit_cost : (+r.rate || 0), hasActual: hasActual };
        },
        unitOf: function(r){ return r.unit || ''; },
        yTitle: function(unit){ return 'Rate' + (unit ? (' (per ' + $('<span>').text(unit).html() + ')') : ''); },
        rateSuffix: function(unit){ return unit ? ' — Rate per ' + $('<span>').text(unit).html() : ''; }
      },
      rcon: {
        items: function(){ return _rconTipItems; },
        // Same formula as _renderResourceConsumption's dial and this
        // panel's own hover tooltip: actual only counts once indent/MB
        // data exists for that resource, otherwise falls back to planned.
        // Support Resources (linked to an Account Head) get their actual
        // from the Actual-Project-Duration ÷ Planned-Duration ratio
        // instead (see ProjectsmainController::actionCostdashboardactivity)
        // — always available once that resource exists, no raised-indent
        // or sent-MB precondition needed.
        isOver: function(r){
          var typeId = +r.type_id;
          var isMaterial = [2, 6, 7, 8].indexOf(typeId) >= 0;
          var isSC = typeId === 4;
          var hasActual = (isMaterial && r.indent_raised) || (isSC && r.has_mb) || (!isMaterial && !isSC && r.has_acc_head);
          // Same est/act basis as estAct() below — compares Actual
          // Consumption against Allocated Quantity per unit, not the
          // scaled totals (multiplying both sides by the same Activity
          // Qty doesn't change which one is bigger).
          var act = hasActual ? (+r.actual_consumption || 0) : (+r.alloc_qty || 0);
          return hasActual && (act > (+r.alloc_qty || 0));
        },
        estAct: function(r){
          var typeId = +r.type_id;
          var isMaterial = [2, 6, 7, 8].indexOf(typeId) >= 0;
          var isSC = typeId === 4;
          var hasActual = (isMaterial && r.indent_raised) || (isSC && r.has_mb) || (!isMaterial && !isSC && r.has_acc_head);
          var actQty = _rconTipActQty || 0;
          // Estimate = Activity Qty × the resource's own Allocated
          // Quantity — alloc_qty is the Activity Library master allocation
          // (estactivity_resources.est_resource_quantity), NOT res_qty
          // (pricing_estimate_resources_new.quantity, a different,
          // project-scaled figure the Cost/Rate formulas use instead).
          // Actual = Actual Consumption per unit of the activity × Activity
          // Qty.
          var est = actQty * (+r.alloc_qty || 0);
          var act = hasActual ? (actQty * (+r.actual_consumption || 0)) : est;
          return { est: est, act: act, hasActual: hasActual };
        },
        unitOf: function(r){ return (+r.type_id === 4 && r.task_unit) ? r.task_unit : (r.unit || ''); },
        yTitle: function(unit){ return 'Quantity' + (unit ? (' (' + $('<span>').text(unit).html() + ')') : ''); },
        rateSuffix: function(unit){ return unit ? ' — Quantity in ' + $('<span>').text(unit).html() : ''; }
      },
      rcost: {
        // Same exclusion as the ucr metric: a Support Resource with no
        // transaction posted yet to its Account Head is left out of this
        // panel entirely (Type bars, Group bars, Resource drill-down)
        // until a real expense appears — even though its consumption side
        // alone would otherwise make _rcostHasActual true.
        items: function(){
          return _rcostTipItems.filter(function(r){
            var hasActualRate = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
            return !(r.has_acc_head && !hasActualRate);
          });
        },
        // Cost = Yield × Unit Cost. Yield (this activity's Total Quantity
        // Required for the resource) = alloc_qty × Activity Qty — same
        // figure as the Yield panel. "Actual" data can arrive from either
        // side independently — a resource's rate can have real GRN/expense
        // data while its consumption is still just planned, or vice versa
        // (e.g. a Support Resource always has an actual consumption once
        // the project has a duration ratio, but its actual unit cost stays
        // at Rate until a transaction is posted) — so hasActual must be
        // true if EITHER side has real data, otherwise a resource whose
        // consumption alone is running over never shows its overrun here.
        isOver: function(r){
          var yield_ = _rcostTipActQty * (+r.alloc_qty || 0);
          var estUC = +r.rate || 0;
          var hasActualRate = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
          var actUC = hasActualRate ? +r.actual_unit_cost : estUC;
          var est = estUC * yield_;
          var act = actUC * yield_;
          return _rcostHasActual(r) && (act > est);
        },
        estAct: function(r){
          var yield_ = _rcostTipActQty * (+r.alloc_qty || 0);
          var estUC = +r.rate || 0;
          var hasActualRate = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
          var actUC = hasActualRate ? +r.actual_unit_cost : estUC;
          var est = estUC * yield_;
          var act = actUC * yield_;
          return { est: est, act: act, hasActual: _rcostHasActual(r) };
        },
        unitOf: function(r){ return ''; },
        yTitle: function(unit){ return 'Cost'; },
        rateSuffix: function(unit){ return ' — Cost of Resources'; }
      }
    };

    // Colour identity follows the resource type down through every
    // drill-down level (Type → Group → Resource) — a type shown orange at
    // the top of the chain stays that same orange in its Group bars and
    // its individual Resource bars, instead of each level picking a fresh
    // colour from the palette independently.
    function _ucrColorForType(metric, typeName) {
      var order = _ucrTypesFor(metric).map(function(r){ return r.name; });
      var idx = order.indexOf(typeName);
      return _UCR_BAR_COLS[(idx >= 0 ? idx : 0) % _UCR_BAR_COLS.length];
    }

    function _ucrTypesFor(metric) {
      var items = _UCR_METRICS[metric].items();
      var counts = {}, overs = {}, order = [];
      items.forEach(function(r) {
        var name = r.type_name || 'Unassigned';
        if (!(name in counts)) { counts[name] = 0; overs[name] = 0; order.push(name); }
        counts[name]++;
        if (_UCR_METRICS[metric].isOver(r)) overs[name]++;
      });
      return order.map(function(name) { return { name: name, value: counts[name], overCount: overs[name] }; });
    }

    function _ucrGroupsForType(metric, typeName) {
      var seen = {}; var groups = [];
      _UCR_METRICS[metric].items().forEach(function(r) {
        if ((r.type_name || 'Unassigned') !== typeName) return;
        var g = r.group_name || 'Unassigned';
        if (seen[g]) return;
        seen[g] = true;
        groups.push(g);
      });
      return groups;
    }

    function _ucrResourcesForGroup(metric, typeName, groupName) {
      return _UCR_METRICS[metric].items().filter(function(r) {
        return (r.type_name || 'Unassigned') === typeName && (r.group_name || 'Unassigned') === groupName;
      });
    }

    function _renderUcrGroupBars(metric, typeName) {
      var m = _UCR_METRICS[metric];
      var groups = _ucrGroupsForType(metric, typeName);
      var rows = groups.map(function(name) {
        var resRows = _ucrResourcesForGroup(metric, typeName, name);
        return { name: name, value: resRows.length, overCount: resRows.filter(m.isOver).length };
      });
      var typeColor = _ucrColorForType(metric, typeName);
      var anyOver = rows.some(function(r){ return (+r.overCount || 0) > 0; });
      return {
        title: $('<span>').text(typeName).html() + ' — Resource Groups',
        body: '<div class="gm-ucr-drill-body" style="display:flex;flex-direction:column;align-items:center;">'
          + _renderVBarChart(rows, { onClick: 'gm-ucr-group', integerTicks: true, showOverrun: true, hideBarVal: true, singleColor: typeColor })
          + (anyOver ? '<div class="gm-vchart-legend gm-vchart-legend-sm"><span><i style="background:#e64a19;"></i>Over estimate</span></div>' : '')
          + '</div>'
      };
    }

    function _renderUcrResourceBars(metric, typeName, groupName) {
      var m = _UCR_METRICS[metric];
      var resRows = _ucrResourcesForGroup(metric, typeName, groupName);
      if (!resRows.length) {
        return { title: $('<span>').text(groupName).html(), body: '<div class="gm-bar-tip-empty">No resources</div>' };
      }
      var unit = m.unitOf(resRows[0]);
      var rows = resRows.map(function(r) {
        var ea = m.estAct(r);
        return { name: r.name || '', est: ea.est, act: ea.act, hasActual: ea.hasActual };
      });
      var typeColor = _ucrColorForType(metric, typeName);

      return {
        title: $('<span>').text(groupName).html() + m.rateSuffix(unit),
        body: '<div class="gm-ucr-drill-body">'
          + _renderVBarChart(rows, { estAct: true, yTitle: m.yTitle(unit), valUnit: ((metric === 'rcon' || metric === 'ucr') && unit) ? unit : '', valUnitSep: (metric === 'ucr') ? '/' : ' ', fmt: (metric === 'rcost') ? _fmtCost : undefined, singleColor: typeColor })
          + '</div>'
      };
    }

    function _openUcrDrill(metric, typeName) {
      _ucrDrillLevel = 'groups';
      _ucrDrillMetric = metric;
      _ucrDrillType = typeName;
      var r = _renderUcrGroupBars(metric, typeName);
      $('#gm-ucr-tip-title').html(r.title);
      $('#gm-ucr-tip-body').html(r.body);
      var $tip = $('#gm-ucr-tip');
      // Re-centre on every fresh open (not on the group→resources drill
      // click within the same open popup) — matches every other floating
      // popup in this app: reopening always starts centred, dragging only
      // persists while that one popup instance stays open.
      var w = 460, h = 360;
      $tip.css({
        left: Math.max(8, (window.innerWidth  - w) / 2) + 'px',
        top:  Math.max(8, (window.innerHeight - h) / 2) + 'px'
      });
      $tip.addClass('gm-bar-tip-open gm-ucr-drill-open');
      if (typeof window.popupBringToFront === 'function') window.popupBringToFront('gm-ucr-tip');
    }

    $(document).on('click', '.gm-ucr-type-bar', function(e) {
      e.stopPropagation();
      _openUcrDrill($(this).closest('[data-metric]').data('metric') || 'ucr', $(this).data('name'));
    });

    $(document).on('click', '.gm-ucr-group-bar', function(e) {
      e.stopPropagation();
      var groupName = $(this).data('name');
      _ucrDrillLevel = 'resources';
      _ucrDrillGroup = groupName;
      var r = _renderUcrResourceBars(_ucrDrillMetric, _ucrDrillType, groupName);
      $('#gm-ucr-tip-title').html(r.title);
      $('#gm-ucr-tip-body').html(r.body);
    });

    $(document).on('click', '#gm-ucr-tip-close', function(e) {
      e.stopPropagation();
      $('#gm-ucr-tip').removeClass('gm-bar-tip-open gm-ucr-drill-open');
    });

    // Click anywhere outside the drill popup closes it; clicks inside it
    // (including the group/resource bars themselves) must not bubble to
    // this handler — each bar click above already stopPropagation()s.
    $(document).on('click', function() {
      $('#gm-ucr-tip').removeClass('gm-bar-tip-open gm-ucr-drill-open');
    });
    $(document).on('click', '#gm-ucr-tip', function(e) { e.stopPropagation(); });

    // ── Drag: Unit Cost of Resources drill-down popup ───────────────────
    (function(){
      var win = document.getElementById('gm-ucr-tip');
      var hdr = document.getElementById('gm-ucr-tip-hdr');
      if (!win || !hdr) return;
      var _dragging = false, _sx = 0, _sy = 0, _ox = 0, _oy = 0;
      bindDragTouch(hdr, 'mousedown', function(e){
        if (e.target.closest('#gm-ucr-tip-close')) return;
        var r = win.getBoundingClientRect();
        win.style.left = r.left + 'px'; win.style.top = r.top + 'px';
        _dragging = true; _sx = e.clientX; _sy = e.clientY; _ox = r.left; _oy = r.top;
        e.preventDefault(); e.stopPropagation();
      });
      bindDragTouch(document, 'mousemove', function(e){
        if (!_dragging) return;
        win.style.left = Math.max(0, _ox + (e.clientX - _sx)) + 'px';
        win.style.top  = Math.max(0, _oy + (e.clientY - _sy)) + 'px';
      });
      bindDragTouch(document, 'mouseup', function(){ _dragging = false; });
    })();

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

    // 2. Consumption of Resources → gm-cd-rcon
    // Yield dial: centre = sum(planned consumption), needle = sum(actual
    // consumption, falling back to planned per-resource where no actual
    // exists yet). Same mechanic as the Unit Cost of Resources dial above.
    var _rconTipItems = [];
    var _rconTipActQty = 0;
    function _renderResourceConsumption(items, actName, lastQty, actUnit, plannedDuration, actualDuration, activityQty) {
      items = items || [];
      _rconTipItems = items;
      // Total Quantity Required = Planned/Actual Consumption per unit of
      // the activity × the activity's own full Estimated Qty — the Yield
      // drill-down shows the total resource quantity needed for the whole
      // activity, not just the per-unit-of-activity ratio.
      _rconTipActQty = +activityQty || 0;
      // Project Overhead's Yield is a project-duration comparison, not a
      // per-resource breakdown — one plain Planned-vs-Actual-Duration bar,
      // no Type/Group/Resource drill-down and not clickable, per explicit
      // direction. Every other activity keeps the normal Type-bar panel.
      if (actName === 'Project Overhead') {
        _renderOverheadYieldBar('gm-cd-rcon', +plannedDuration || 0, +actualDuration || 0, actName);
        return;
      }
      _renderUcrTypePanel('gm-cd-rcon', 'rcon', items, actName);
    }

    function _renderOverheadYieldBar(elId, plannedDuration, actualDuration, actName) {
      var el = document.getElementById(elId);
      if (!el) return;
      if (!plannedDuration) { el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No project dates set</div>'; return; }
      // Single horizontal bar: length = Planned Duration, orange-red
      // extension = the overrun (Actual − Planned) when the project is
      // running long — same visual mechanic as the Type/Group overrun
      // extension, just carrying a day-count difference instead of a
      // resource count. Not clickable — no onClick handler passed.
      var overDays = Math.max(0, actualDuration - plannedDuration);
      var row = { name: 'Duration', value: plannedDuration, overCount: overDays };
      var html = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + _sh(actName||'', 40) + '</div>';
      // The chart itself is centred exactly like the other two Type-bar
      // panels (identical flex:1;min-height:0 sub-container, same as
      // _renderUcrTypePanel) so the bar lands at the same vertical
      // position across all three panels. The legend is absolutely
      // positioned below it — NOT a normal flex sibling — so it consumes
      // zero space from that centring container; a flex sibling here
      // (even flex-shrink:0) still shrinks the space the chart centres
      // within, nudging its bar upward relative to the other two panels.
      html += '<div style="width:100%;box-sizing:border-box;padding:8px 10px;flex:1;min-height:0;display:flex;flex-direction:column;justify-content:center;overflow:hidden;">'
        +   _renderHBarChart([row], { labelW: 50, bigLabels: true, smallFont: true, showOverrun: true, integerTicks: true, singleColor: '#3461b8', xTitle: 'Days' })
        + '</div>';
      el.innerHTML = html;
    }

    // 3. Cost of Resources → gm-cd-rcost — dial: centre/needle = sum of all
    // resources' actual cost (rate × consumption × Sch. Qty), estimated sum =
    // dial half-scale. Green when actual cost is under estimate, orange when over.
    var _rcostTipItems = [], _rcostTipSchedQty = 0, _rcostTipActQty = 0;
    function _renderResourceCost(items, actName, actUnit, schedQty, activityQty) {
      items = items || [];
      schedQty = +schedQty || 0;
      _rcostTipItems = items;
      _rcostTipSchedQty = schedQty;
      // Cost of Resources = Rate × Activity Qty only — the resource's own
      // Quantity (a per-unit-of-activity consumption ratio) and Schedule
      // Qty are no longer part of this formula; multiplying by both was
      // double-counting since Activity Qty already represents the full
      // quantity the activity covers.
      _rcostTipActQty = +activityQty || 0;
      // Project Overhead's Cost of Resources is a total-cost comparison,
      // not a per-resource breakdown — one plain Planned-vs-Actual
      // Resource Cost bar (summed across all resources), no Type/Group/
      // Resource drill-down and not clickable — same treatment as the
      // Yield panel's Planned-vs-Actual Duration bar. Every other
      // activity keeps the normal Type-bar panel.
      if (actName === 'Project Overhead') {
        // Same exclusion as the Unit Cost of Activity panel: a Support
        // Resource with no transaction posted yet to its Account Head has
        // no actual value at all — leave it out of this bar and its
        // drill-down entirely (not even an Estimate contribution) until a
        // real expense appears against that account.
        var liveItems = items.filter(function(r){
          var hasAct = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
          return !(r.has_acc_head && !hasAct);
        });
        var estCostTotal = 0, actCostTotal = 0;
        liveItems.forEach(function(r) {
          var estUC = +r.rate || 0;
          var hasAct = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
          var actUC = hasAct ? +r.actual_unit_cost : estUC;
          // Estimate = Rate × Activity Qty only (no resource-quantity/
          // consumption ratio or Schedule Qty multiplier).
          estCostTotal += estUC * _rcostTipActQty;
          // Support Resources' Actual Cost = Actual Unit Cost × Actual
          // Duration only. actual_consumption_cost carries the Actual
          // Duration for these rows — distinct from actual_consumption,
          // which the Unit Cost of Activity panel uses with Planned
          // Consumption instead.
          actCostTotal += actUC * (+r.actual_consumption_cost  || 0);
        });
        _renderOverheadCostBar('gm-cd-rcost', estCostTotal, actCostTotal, actName, liveItems, schedQty);
        return;
      }
      _renderUcrTypePanel('gm-cd-rcost', 'rcost', items, actName);
    }

    function _renderOverheadCostBar(elId, plannedCost, actualCost, actName, items, schedQty) {
      var el = document.getElementById(elId);
      if (!el) return;
      if (!plannedCost) { el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No resources allocated</div>'; return; }
      // Same single-bar mechanic as _renderOverheadYieldBar: length =
      // Planned Resource Cost, orange-red extension = the overrun
      // (Actual − Planned) when actual cost is running over. Clicking it
      // opens the shared drill-down popup straight at the per-resource
      // vertical-bar view (no Type/Group levels — Project Overhead's
      // resource list is short enough not to need that grouping here).
      var overCost = Math.max(0, actualCost - plannedCost);
      var row = { name: 'Cost', value: plannedCost, overCount: overCost };
      var html = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + _sh(actName||'', 40) + '</div>';
      html += '<div style="width:100%;box-sizing:border-box;padding:8px 10px;flex:1;min-height:0;overflow:hidden;">'
        + '<div class="gm-overhead-cost-bar" style="height:100%;display:flex;flex-direction:column;justify-content:center;cursor:pointer;">'
        +   _renderHBarChart([row], { labelW: 50, bigLabels: true, smallFont: true, showOverrun: true, singleColor: '#3461b8', xTitle: 'Cost', hideBarVal: true })
        + '</div>'
        + '</div>';
      el.innerHTML = html;
      $(el).find('.gm-overhead-cost-bar').off('click.overheadCost').on('click.overheadCost', function(e){
        e.stopPropagation();
        _openOverheadCostDrill(items, schedQty, actName);
      });
    }

    // Per-resource Estimate-vs-Actual Cost bars for Project Overhead's
    // Cost of Resources panel — same est/act vertical-bar mechanic as
    // _renderUcrResourceBars, but keyed on total cost (rate × consumption
    // × schedQty) per resource instead of unit rate, and formatted with
    // the Cr/Lakh/K notation (_fmtCost) rather than plain numbers, since
    // these are full rupee totals rather than per-unit rates.
    function _openOverheadCostDrill(items, schedQty, actName) {
      var rows = (items || []).map(function(r) {
        var estUC = +r.rate || 0;
        var hasActual = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
        var actUC = hasActual ? +r.actual_unit_cost : estUC;
        var est = estUC * _rcostTipActQty;
        var act = actUC * (+r.actual_consumption_cost  || 0);
        return { name: r.name || '', est: est, act: act, hasActual: hasActual };
      });
      var body = rows.length
        ? '<div class="gm-ucr-drill-body">' + _renderVBarChart(rows, { estAct: true, fmt: _fmtCost, yTitle: 'Cost' }) + '</div>'
        : '<div class="gm-bar-tip-empty">No resources</div>';
      $('#gm-ucr-tip-title').html($('<span>').text(actName || 'Cost of Resources').html());
      $('#gm-ucr-tip-body').html(body);
      var $tip = $('#gm-ucr-tip');
      var w = 460, h = 360;
      $tip.css({
        left: Math.max(8, (window.innerWidth  - w) / 2) + 'px',
        top:  Math.max(8, (window.innerHeight - h) / 2) + 'px'
      });
      $tip.addClass('gm-bar-tip-open gm-ucr-drill-open');
      if (typeof window.popupBringToFront === 'function') window.popupBringToFront('gm-ucr-tip');
    }

    // 4. Unit Cost of Activity → gm-cd-g5
    function _renderUnitCostOfActivity(items, actName, actUnit, schedQty, activityQty) {
      items = items || []; schedQty = +schedQty || 0;
      var estQty = +activityQty || 0;
      var estCostTotal = 0, actCostTotal = 0;
      items.forEach(function(r) {
        var hasAct = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
        // A Support Resource (has_acc_head) with no transaction posted yet
        // to its Account Head has no actual value at all — it's excluded
        // from this panel entirely (no Estimate contribution either) until
        // a real expense appears against that account.
        if (r.has_acc_head && !hasAct) return;
        var estUC = +r.rate || 0;
        var actUC = hasAct ? +r.actual_unit_cost : estUC;
        // Unit Cost of Activity = Cost of Resources ÷ Estimated Activity
        // Qty. Cost of Resources per resource = Rate × Yield, where Yield
        // = Allocated Qty (alloc_qty) × Activity Qty — same figures the
        // Cost of Resources / Yield panels use.
        var resYield = estQty * (+r.alloc_qty || 0);
        estCostTotal += estUC * resYield;
        actCostTotal += actUC * resYield;
      });
      var estTotal = estQty > 0 ? estCostTotal / estQty : 0;
      var actTotal = estQty > 0 ? actCostTotal / estQty : 0;
      var maxVal = estTotal > 0 ? estTotal * 2 : 1;
      var unitLbl = actUnit ? ' /' + actUnit : '';
      var el5 = document.getElementById('gm-cd-g5');
      if (el5) el5.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + _sh(actName||'', 40) + '</div>';
      _gauge('gm-cd-g5', actTotal, maxVal, 'esttarget', 0.5, 'Est', _fmtFull(estTotal) + unitLbl, 'Act', _fmtFull(actTotal) + unitLbl, estTotal);
    }

    // Elapsed Days dial (Project Overhead only) → gm-cd-g4. Dial's full
    // scale (0 → track end) = Actual Duration. Yellow tick marks Planned
    // Duration within that scale — the segment beyond it out to the
    // track's end (i.e. Actual − Planned) is the orange-red overrun.
    // Needle points to Elapsed Days (Today − Planned Start Date) within
    // that same 0 → Actual Duration scale — a third, independent figure
    // from either endpoint.
    function _renderElapsedDaysDial(d) {
      var el = document.getElementById('gm-cd-g4');
      if (!el) return;
      var plannedDur   = +d.planned_duration || 0;
      var actualDur    = +d.actual_duration  || 0;
      var elapsedDays  = +d.elapsed_days || 0;
      var actName      = d.activity_name || '';
      // Scale headroom (+10%) so the needle never lands exactly on the
      // dial's outer edge — pinned to f=1 there, its short radial line
      // sits flush against the track's own end and reads as "missing".
      var maxVal = Math.max(actualDur, 1) * 1.1;
      var targetFrac = plannedDur / maxVal;
      el.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + _sh(actName, 40) + '</div>';
      _gauge('gm-cd-g4', elapsedDays, maxVal, 'esttarget-prop', targetFrac, 'Planned', plannedDur + ' d', 'Actual', actualDur + ' d', plannedDur, 'Elapsed ' + _fm(elapsedDays) + ' d', actualDur, true);
    }

    // 5. Work Done → gm-cd-g4. Same esttarget-prop dial mechanic as
    // Elapsed Days: grey track up to Schedule Qty, yellow tick marking
    // that boundary, orange-red fills the gap beyond it when Done exceeds
    // Schedule Qty, needle always points to the real Done value (never
    // capped at Schedule Qty) — scale stretches to fit whichever is larger
    // so the yellow line recedes left as the overrun grows.
    function _renderValueOfWorkDone(d) {
      var el = document.getElementById('gm-cd-g4');
      if (!el) return;
      var schedQty = +d.schedule_qty || 0;
      var rawLastQty = +d.last_report_qty || 0;
      var estQty   = +d.activity_qty || 0;
      // Done = (Estimated Qty ÷ Schedule Qty) × Last Reported Qty — scales
      // the raw progress-reported quantity by the ratio between this
      // activity's own Estimated Qty and its full Schedule Qty, instead of
      // showing the raw reported quantity directly.
      var lastQty  = schedQty > 0 ? (estQty / schedQty) * rawLastQty : rawLastQty;
      var unit     = d.unit || '';
      var actName  = d.activity_name || '';
      // Dial scale/target boundary is driven by Estimated Qty (estQty),
      // not Schedule Qty — the tip at the dial's left/target position
      // represents the activity's own Estimated Qty, matching how Done
      // itself is scaled above.
      var maxVal = Math.max(estQty, lastQty, 1) * 1.1;
      var targetFrac = estQty / maxVal;
      el.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + _sh(actName, 40) + '</div>';
      _gauge('gm-cd-g4', lastQty, maxVal, 'esttarget-prop', targetFrac, 'Est', _fmtFull(estQty) + (unit ? ' ' + unit : ''), 'Done', _fmtFull(lastQty) + (unit ? ' ' + unit : ''), estQty, null, null, true);
    }

    // 6. Cost of Activity → gm-cd-g2
    function _renderCostOfActivity(d) {
      var el = document.getElementById('gm-cd-g2');
      if (!el) return;
      var items = d.items || [], actName = d.activity_name || '';
      var isProjectOverhead = (actName === 'Project Overhead');
      var schedQty = +d.schedule_qty || 0;
      var estQty   = +d.activity_qty || 0;
      var estUCTotal = 0, actUCTotal = 0, hasActual = false;
      // Project Overhead's Support Resources: Actual Cost contribution to
      // the bar/top-row total = Actual Unit Cost × Actual Duration only —
      // accumulated separately in actCostOverheadSum (already a total, not
      // a per-unit rate) and kept out of actUCTotal so it isn't
      // double-counted against the × Estimated Qty multiplier below.
      // estUCTotal/actUCTotal are Unit Cost of Activity's own totals
      // (Cost of Resources ÷ Estimated Qty, i.e. Σ(Rate×Yield) ÷ Estimated
      // Qty) — reused here for both the bar (× Estimated Qty, which
      // cancels back to Σ(Rate×Yield)) and the Work Done section (× Work
      // Done Qty) below, matching that panel's own formula.
      var estResCostTotal = 0, actResCostTotal = 0;
      var actCostOverheadSum = 0;
      items.forEach(function(r) {
        var hasAct = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
        // A Support Resource with no transaction posted yet to its Account
        // Head has no actual value at all — skip it entirely (no Estimate
        // contribution either), matching the exclusion already applied to
        // Unit Cost of Activity and Cost of Resources.
        if (isProjectOverhead && r.has_acc_head && !hasAct) return;
        var estUC  = +r.rate || 0;
        var actUC  = hasAct ? +r.actual_unit_cost : estUC;
        if (hasAct) hasActual = true;
        if (isProjectOverhead && r.has_acc_head) {
          actCostOverheadSum += actUC * (+r.actual_consumption_cost || 0);
        } else {
          var resYield = estQty * (+r.alloc_qty || 0);
          estResCostTotal += estUC * resYield;
          actResCostTotal += actUC * resYield;
        }
      });
      estUCTotal = estQty > 0 ? estResCostTotal / estQty : 0;
      actUCTotal = estQty > 0 ? actResCostTotal / estQty : 0;
      // Actual Activity Quantity: normally the same as the Estimated Qty,
      // but if Work Done has actually exceeded it, use the exceeded
      // (larger) quantity instead — same scaled Done value as the Work
      // Done dial: (Estimated Qty ÷ Schedule Qty) × Last Reported Qty.
      var rawLastQty = +d.last_report_qty || 0;
      var workDone = isProjectOverhead
        ? (+d.elapsed_days || 0)
        : (schedQty > 0 ? (estQty / schedQty) * rawLastQty : rawLastQty);
      var actQty = Math.max(estQty, workDone);
      var estCost = estUCTotal * estQty;
      var actCost = hasActual ? (actUCTotal * actQty + actCostOverheadSum) : estCost;
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
      var estCostWD = estUCTotal * workDone;
      var actCostWD = actUCTotal * workDone;
      var diffWD = actCostWD - estCostWD;
      var overWD = diffWD > 0;
      var diffWDLabel = diffWD !== 0 ? (overWD?'+':'-') + _fmtFull(Math.abs(diffWD)) + ' ' + (overWD?'over':'saving') : '';
      var valRow = '<div style="display:flex;justify-content:space-between;align-items:baseline;font-family:\'Barlow Condensed\',sans-serif;font-size:15px;font-weight:700;color:#111;margin-bottom:6px">'
        +'<span>Est: ' + _fmtFull(estCost) + '</span>'
        +(hasActual ? '<span>Act: <span style="color:'+(over?'#e8820c':'#1b9e8e')+'">' + _fmtFull(actCost) + '</span></span>' : '')
        +'</div>';
      // Project Overhead has no Work Done quantity to speak of — this
      // section is really the Planned/Actual cost accrued up to today
      // (Unit Cost × Elapsed Days), so it reads "as of Today" instead of
      // "of Work Done" only for Project Overhead; every other activity
      // keeps the original Work Done wording.
      var estWDLabel = isProjectOverhead ? 'Planned Cost as of Today' : 'Estimated Cost of Work Done';
      var actWDLabel = isProjectOverhead ? 'Actual Cost as of Today'  : 'Actual Cost of Work Done';
      var wdRow = '<div style="font-family:\'Barlow Condensed\',sans-serif;font-size:15px;font-weight:600;color:#444;margin-top:8px;line-height:1.7">'
        +'<div>'+estWDLabel+' &nbsp;<span style="color:#111">' + _fmtFull(estCostWD) + '</span></div>'
        +(hasActual
          ? '<div>'+actWDLabel+' &nbsp;<span style="color:'+(overWD?'#e8820c':'#1b9e8e')+'">' + _fmtFull(actCostWD) + '</span></div>'
            +'<div style="color:'+(overWD?'#e8820c':'#1b9e8e')+'">Difference &nbsp;'+(diffWDLabel||_fmtFull(0))+'</div>'
          : '<div>'+actWDLabel+' &nbsp;<span style="color:#888;font-style:italic;font-weight:500">No Cost Reported</span></div>'
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
      if (pop) { pop.classList.remove('gcm-visible'); pop.style.display = 'none'; delete pop.dataset.portraitFitted; }
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
      if (typeof window.popupFitPortrait === 'function') { window.popupFitPortrait('gcm-popup'); }
    }

    // isWanId: true when actId is actually a workgroup_activities_new.id
    // (Estimate-only activities with no Gantt bar — e.g. the auto-seeded
    // "Project Overhead"/"Project General Expenses" — pass their wan_id
    // here instead of a scheduleactivities id, since they don't have one;
    // see ProjectsmainController::actionCostdashboardactivity's wan_id
    // fallback).
    function _showCostPopup(actId, anchorEl, isWanId) {
      var pop = document.getElementById('gcm-popup');
      if (!pop) return;

      function _doRender(d) {
        document.getElementById('gcm-pop-hdr-left').textContent = 'Manage Cost';
        ['gm-cd-rcost','gm-cd-ucr','gm-cd-rcon','gm-cd-g5','gm-cd-g4','gm-cd-g2'].forEach(function(id) {
          var el = document.getElementById(id); if (el) el.innerHTML = '';
        });
        // Panel headings for "Project Overhead" specifically read
        // differently — it's a lump-sum/day-rate line item, not a
        // quantity-driven construction activity, so "Unit Cost of
        // Activity"/"Work Done"/"Cost of Activity"/"Yield" don't mean the
        // same thing here as they do elsewhere. Every other activity
        // (including "Project General Expenses") keeps the standard titles.
        var isProjectOverhead = (d.activity_name === 'Project Overhead');
        document.getElementById('gcm-title-g5').textContent   = isProjectOverhead ? 'Cost Per Day'          : 'Unit Cost of Activity';
        document.getElementById('gcm-title-g4').textContent   = isProjectOverhead ? 'Elapsed Days'          : 'Work Done';
        document.getElementById('gcm-title-g2').textContent   = isProjectOverhead ? 'Project Overhead Cost' : 'Cost of Activity';
        document.getElementById('gcm-title-rcon').textContent = isProjectOverhead ? 'Days'                  : 'Yield';
        document.getElementById('gcm-pop-loading').style.display = 'none';
        document.getElementById('gcm-pop-row1').style.display = 'grid';
        _renderResourceCost(d.items, d.activity_name, d.unit, d.schedule_qty, d.activity_qty);
        _renderUnitCostOfResource(d.items, d.activity_name);
        _renderResourceConsumption(d.items, d.activity_name, d.last_report_qty, d.unit, d.planned_duration, d.actual_duration, d.activity_qty);
        _renderUnitCostOfActivity(d.items, d.activity_name, d.unit, d.schedule_qty, d.activity_qty);
        if (isProjectOverhead) { _renderElapsedDaysDial(d); } else { _renderValueOfWorkDone(d); }
        _renderCostOfActivity(d);
        _positionCostPopup(anchorEl);
      }

      // This data is time-sensitive (Elapsed Days, live ledger balances,
      // Actual Duration) and must be recomputed fresh on every single
      // open — a per-session cache here means the popup keeps showing
      // whatever day it was first opened on, silently going stale as time
      // passes and new transactions post. Always re-fetch; no cache read.

      // Show loading state
      document.getElementById('gcm-pop-hdr-left').textContent = 'Manage Cost';
      document.getElementById('gcm-pop-loading').style.display = 'block';
      document.getElementById('gcm-pop-row1').style.display = 'none';
      _forceShowCostPopup(pop);
      _positionCostPopup(anchorEl);

      if (_gcpXhr) _gcpXhr.abort();
      _gcpXhr = $.ajax({
        type: 'POST', url: _base + '/costdashboardactivity',
        data: isWanId ? { wan_id: actId } : { actid: actId }, dataType: 'json',
        success: function(d) {
          _gcpXhr = null;
          if (!d || d.error) {
            document.getElementById('gcm-pop-loading').textContent = d && d.error ? d.error : 'Error loading data';
            return;
          }
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
          // Days elapsed since the activity's Planned Start Date (not its
          // Actual/reported start, and not gated on progress having been
          // reported yet) — Target to Date accrues from day one of the
          // plan regardless of whether work has actually begun.
          var psd=k.adj_start_date||'';
          var todayMid=new Date(); todayMid.setHours(0,0,0,0);
          var elDays=psd?Math.max(0,Math.round((todayMid-new Date(psd))/86400000)+1):0;
          var compTarget=(dur>0&&tq>0)?Math.min(tq,elDays*(tq/dur)):0;
          var fAct=tq>0?Math.max(0,Math.min(1,aq/tq)):0;
          var fTgt=tq>0?Math.max(0,Math.min(1,compTarget/tq)):0;
          var actCol=aq<compTarget?'#e53935':aq>compTarget?'#27ae60':'#1a2540';
          var cx=105,cy=92,r=76,sw=14;
          function ptF(fr){var a=Math.PI*(1-fr);return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)];}
          function arcD(f1,f2,col,cap){if(f2<=f1)return'';cap=cap||'butt';var p1=ptF(f1),p2=ptF(f2);if((f2-f1)>=1){var pm=ptF(0.5);return'<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';}return'<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';}
          var nr=r-15,na=Math.PI*(1-fTgt),nx=(cx+nr*Math.cos(na)).toFixed(1),ny=(cy-nr*Math.sin(na)).toFixed(1);
          el.innerHTML='<svg width="100%" viewBox="0 -12 210 146" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet" style="display:block;width:100%;height:auto;">'
            +arcD(0,1,'#5a6b82')+(fAct>0?arcD(0,fAct,'#0d1f6e','butt'):'')
            +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
            +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/><circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#5a6b82"/>'
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
          // Right-end label is the Actual/Projected End Date (Planned
          // Start + Actual/Projected Duration, i.e. aDur — which already
          // reflects any delay/overrun), not the fixed Planned End Date,
          // so it always shows when the activity is really headed to
          // finish rather than the static baseline target.
          var actEndDate='';
          if(k.adj_start_date&&k.adj_start_date!=='0000-00-00'&&aDur>0){
            var aed=new Date(k.adj_start_date); aed.setDate(aed.getDate()+(Math.max(1,Math.round(aDur))-1));
            actEndDate=aed.getFullYear()+'-'+String(aed.getMonth()+1).padStart(2,'0')+'-'+String(aed.getDate()).padStart(2,'0');
          }
          el.innerHTML='<div style="padding:10px 14px;'+fam+'font-size:12px;box-sizing:border-box;">'
            +'<div style="display:flex;justify-content:space-between;align-items:baseline;font-weight:700;color:#1a2540;">'
            +'<span>'+bDur+'d Planned'+(isOver?' <span style="color:#e53935;">+' +(aDur-bDur)+'d</span>':isUnder?' <span style="color:#27ae60;">−'+(bDur-aDur)+'d</span>':'')+' </span>'
            +(startDelay>0&&!isOver?'<span style="color:#E65100;">Start +'+startDelay+'d</span>':'')+'</div>'
            +bar
            +'<div style="display:flex;justify-content:space-between;color:#5a6e8c;font-size:11px;">'
            +'<span>Act. Start: <b style="color:#1a2540;">'+fmD(k.act_start_date)+'</b></span>'
            +'<span>Elapsed: <b style="color:#1a2540;">'+elapsedDays+'d</b></span>'
            +'<span>Act. End: <b style="color:#1a2540;">'+fmD(actEndDate)+'</b></span>'
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
      bindDragTouch(hdr, 'mousedown', function(e){ if(e.target.id==='gkm-close') return; var r=_anchor(); _action='drag'; _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault(); });
      document.querySelectorAll('.gkm-rs').forEach(function(el){ bindDragTouch(el, 'mousedown', function(e){ var r=_anchor(); _action=el.getAttribute('data-dir'); _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height; e.preventDefault(); e.stopPropagation(); }); });
      bindDragTouch(document, 'mousemove', function(e){ if(!_action) return; var dx=e.clientX-_sx, dy=e.clientY-_sy; if(_action==='drag'){ win.style.left=Math.max(0,_ox+dx)+'px'; win.style.top=Math.max(0,_oy+dy)+'px'; } else { var l=_ox,t=_oy,w=_ow,h=_oh; if(_action.indexOf('e')>-1){w=Math.max(MIN_W,_ow+dx);} if(_action.indexOf('s')>-1){h=Math.max(MIN_H,_oh+dy);} if(_action.indexOf('w')>-1){var nw=Math.max(MIN_W,_ow-dx);l=_ox+(_ow-nw);w=nw;} if(_action.indexOf('n')>-1){var nh=Math.max(MIN_H,_oh-dy);t=_oy+(_oh-nh);h=nh;} win.style.left=l+'px'; win.style.top=t+'px'; win.style.width=w+'px'; win.style.height=h+'px'; } });
      bindDragTouch(document, 'mouseup', function(){ _action=null; });
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
      function _anchor(){ var r=win.getBoundingClientRect(); win.style.left=r.left+'px'; win.style.top=r.top+'px'; win.style.width=r.width+'px'; win.style.height=r.height+'px'; win.dataset.userSized='1'; return r; }
      bindDragTouch(hdr, 'mousedown', function(e){ if(e.target.closest('#gcm-pop-close')) return; var r=_anchor(); _action='drag'; _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault(); });
      document.querySelectorAll('.gcm-rs').forEach(function(el){ bindDragTouch(el, 'mousedown', function(e){ var r=_anchor(); _action=el.getAttribute('data-dir'); _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height; e.preventDefault(); e.stopPropagation(); }); });
      bindDragTouch(document, 'mousemove', function(e){ if(!_action) return; var dx=e.clientX-_sx, dy=e.clientY-_sy; if(_action==='drag'){ win.style.left=Math.max(0,_ox+dx)+'px'; win.style.top=Math.max(0,_oy+dy)+'px'; } else { var l=_ox,t=_oy,w=_ow,h=_oh; if(_action.indexOf('e')>-1){w=Math.max(MIN_W,_ow+dx);} if(_action.indexOf('s')>-1){h=Math.max(MIN_H,_oh+dy);} if(_action.indexOf('w')>-1){var nw=Math.max(MIN_W,_ow-dx);l=_ox+(_ow-nw);w=nw;} if(_action.indexOf('n')>-1){var nh=Math.max(MIN_H,_oh-dy);t=_oy+(_oh-nh);h=nh;} win.style.left=l+'px'; win.style.top=t+'px'; win.style.width=w+'px'; win.style.height=h+'px'; } });
      bindDragTouch(document, 'mouseup', function(){ _action=null; });
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

    // #btn-project-duration lives in main.php, OUTSIDE #gantt-win-body —
    // every Gantt reload (_loadGanttWin) replaces #gantt-win-body's HTML,
    // destroying this #gpd-tip instance and re-running this whole script
    // block, but the button itself survives untouched. A direct
    // btn.addEventListener() + a per-button "already bound" dataset guard
    // (the previous approach) meant: after the first reload, the guard was
    // still set from the FIRST script run, so the fresh listener (closing
    // over the new, live #gpd-tip) never got attached — the button then
    // silently did nothing, or at best re-opened a #gpd-tip that no longer
    // existed in the DOM. Delegated listeners on `document`, registered
    // once globally (window._gpdDelegatedBound, survives across reloads
    // since `window` itself is never torn down), sidestep this entirely —
    // they always resolve #btn-project-duration/#gpd-tip fresh from the
    // DOM on each click, so they keep working no matter how many times
    // the Gantt has been reloaded since page load.
    if (!window._gpdDelegatedBound) {
      window._gpdDelegatedBound = true;
      document.addEventListener('click', function(e){
        var btn = e.target.closest('#btn-project-duration');
        var liveTip = document.getElementById('gpd-tip');
        if (btn) {
          e.preventDefault(); e.stopPropagation();
          if (!liveTip) return;
          if (liveTip.classList.contains('gpd-open')) { liveTip.classList.remove('gpd-open'); return; }
          if (typeof window._gpdShow === 'function') window._gpdShow(btn);
          return;
        }
        if (!liveTip || !liveTip.classList.contains('gpd-open')) return;
        var curBtn = document.getElementById('btn-project-duration');
        if (curBtn && (e.target === curBtn || curBtn.contains(e.target))) return;
        if (liveTip.contains(e.target)) return;
        liveTip.classList.remove('gpd-open');
      });
    }
    // Re-exposed on every script run so the delegated handler above always
    // calls the CURRENT closure's show()/render() (fresh _cache, fresh tip
    // reference) rather than a stale one left over from a prior Gantt load.
    window._gpdShow = show;

    // ── Drag ──────────────────────────────────────────────────────────────
    (function(){
      var titleEl = document.getElementById('gpd-tip-title');
      var _action=false, _sx=0, _sy=0, _ox=0, _oy=0;
      function _anchor(){ var r=tip.getBoundingClientRect(); tip.style.left=r.left+'px'; tip.style.top=r.top+'px'; return r; }
      bindDragTouch(titleEl, 'mousedown', function(e){
        var r=_anchor(); _action=true; _dragged=true;
        _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault();
      });
      bindDragTouch(document, 'mousemove', function(e){
        if(!_action) return;
        var dx=e.clientX-_sx, dy=e.clientY-_sy;
        tip.style.left=Math.max(0,_ox+dx)+'px'; tip.style.top=Math.max(0,_oy+dy)+'px';
      });
      bindDragTouch(document, 'mouseup', function(){ _action=false; });
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
      bindDragTouch(hdr, 'mousedown', function(e){
        if (e.target.id === 'gsc-tip-close') return;
        var r = _anchor();
        _action = true; _pinned = true;
        _sx = e.clientX; _sy = e.clientY; _ox = r.left; _oy = r.top;
        e.preventDefault();
      });
      bindDragTouch(document, 'mousemove', function(e){
        if (!_action) return;
        var dx = e.clientX - _sx, dy = e.clientY - _sy;
        tip.style.left = Math.max(0, _ox + dx) + 'px';
        tip.style.top = Math.max(0, _oy + dy) + 'px';
      });
      bindDragTouch(document, 'mouseup', function(){ _action = false; });
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
    var _kpNameCache = {};   // actid → activity name cache, shown in the popup title
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
      // Days elapsed since the activity's Planned Start Date (not its
      // Actual/reported start, and not gated on progress having been
      // reported yet) — Target to Date accrues from day one of the plan
      // regardless of whether work has actually begun.
      var psd=k.adj_start_date||'';
      var todayMid=new Date(); todayMid.setHours(0,0,0,0);
      var elDays=psd?Math.max(0,Math.round((todayMid-new Date(psd))/86400000)+1):0;
      var compTarget=(dur>0&&tq>0)?Math.min(tq,elDays*(tq/dur)):0;
      var fAct=tq>0?Math.max(0,Math.min(1,aq/tq)):0;
      var fTgt=tq>0?Math.max(0,Math.min(1,compTarget/tq)):0;
      var actCol=aq<compTarget?'#e53935':aq>compTarget?'#27ae60':'#1a2540';
      var cx=90,cy=80,r=66,sw=12;
      function ptF(fr){var a=Math.PI*(1-fr);return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)];}
      function arcP(f1,f2,col,cap){if(f2<=f1)return'';cap=cap||'butt';var p1=ptF(f1),p2=ptF(f2);if((f2-f1)>=1){var pm=ptF(0.5);return'<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';}return'<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';}
      var nr=r-12,na=Math.PI*(1-fTgt),nx=(cx+nr*Math.cos(na)).toFixed(1),ny=(cy-nr*Math.sin(na)).toFixed(1);
      el.innerHTML='<svg width="100%" viewBox="0 -8 180 130" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet" style="display:block;width:100%;height:auto;">'
        +arcP(0,1,'#5a6b82')+(fAct>0?arcP(0,fAct,'#0d1f6e','butt'):'')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="2.5" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="5" fill="#555"/><circle cx="'+cx+'" cy="'+cy+'" r="2" fill="#5a6b82"/>'
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
      var baseCol=k.completed?'#00842e':(k.critical?'#00838f':'#37474F');
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
      // Right-end label is the Actual/Projected End Date (Planned Start +
      // Actual/Projected Duration, i.e. aDur — already reflects any
      // delay/overrun), not the fixed Planned End Date.
      var actEndDate2='';
      if(k.adj_start_date&&k.adj_start_date!=='0000-00-00'&&aDur>0){
        var aed2=new Date(k.adj_start_date); aed2.setDate(aed2.getDate()+(Math.max(1,Math.round(aDur))-1));
        actEndDate2=aed2.getFullYear()+'-'+String(aed2.getMonth()+1).padStart(2,'0')+'-'+String(aed2.getDate()).padStart(2,'0');
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
        +'<span>Act. End: <b style="color:#1a2540;">'+(_fmDateK(actEndDate2)||'—')+'</b></span>'
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

    // Small blue activity-name label shown at the top of each panel body —
    // same visual pattern the Cost Dashboard's panels already use (e.g.
    // _renderResourceCost) — instead of in the popup's own header.
    function _gkpPanelNameLabel(actName) {
      return '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + _shK(actName || '', 40) + '</div>';
    }

    function _renderKpi(k, actId) {
      // k.activity_name (set by _buildKpi server-side) is the reliable
      // source — it was already present in the KPI payload before the
      // separate _kpNameCache/outer-JSON activity_name field was added,
      // so read it directly instead of depending on that second field
      // round-tripping correctly.
      var nm = (k && k.activity_name) || (actId != null ? _kpNameCache[actId] : null);
      document.getElementById('gkp-title').textContent = 'Manage Schedule';
      _renderKpuCapacity(k);
      _renderKpuCycleTime(k);
      _renderKpuProductivity(k);
      _renderKpuTargetProd(k);
      _renderKpuDuration(k);
      _renderKpuResourceCapacity(k);
      // Label injected AFTER each panel's own render — every _renderKpu*
      // above sets innerHTML directly (gauges are drawn as full inline
      // SVG), which would wipe out a label set beforehand.
      ['gkp-g3','gkp-tp','gkp-dur','gkp-res','gkp-g5','gkp-g4'].forEach(function(id){
        var el = document.getElementById(id);
        if (el) el.insertAdjacentHTML('afterbegin', _gkpPanelNameLabel(nm));
      });
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
      delete pop.dataset.portraitFitted;
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
        _renderKpi(_kpCache[actId], actId);
        var pop = document.getElementById('gkp-popup');
        pop.classList.add('gkp-visible');
        _raiseKpiPopup(pop);
        if (typeof window.popupFitPortrait === 'function') { window.popupFitPortrait('gkp-popup'); }
        positionFn();
        return;
      }
      if (_kpXhr) _kpXhr.abort();
      document.getElementById('gkp-title').textContent = 'Manage Schedule';
      ['gkp-g5','gkp-g4','gkp-g3','gkp-tp','gkp-dur','gkp-res'].forEach(function(id){
        var el=document.getElementById(id); if(el) el.innerHTML='';
      });
      var pop = document.getElementById('gkp-popup');
      pop.classList.add('gkp-visible');
      _raiseKpiPopup(pop);
      if (typeof window.popupFitPortrait === 'function') { window.popupFitPortrait('gkp-popup'); }
      positionFn();
      _kpXhr = $.ajax({
        type: 'POST', url: _base + '/performancedashboardkpi',
        data: { actid: actId }, dataType: 'json',
        success: function(d) {
          _kpXhr = null;
          if (!d || !d.kpi) return;
          _kpCache[actId] = d.kpi;
          _kpNameCache[actId] = d.activity_name || '';
          _renderKpi(d.kpi, actId);
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
    // Finds the dependency-line element (if any) at the given click
    // coordinates — a plain e.target.closest() isn't reliable here
    // because the "planned" ghost bar overlay (.gtaskbarcontainer.gplan,
    // jsgantt.css, z-index:10000) visually covers the same row/bar area
    // a line passes through; when that overlay is what's actually under
    // the cursor, e.target IS the overlay and .closest() walking its
    // ancestors can never reach the line (a sibling, not an ancestor).
    // elementsFromPoint returns the FULL stack at the click point,
    // topmost first, so the line is found regardless of what else is
    // painted on top of it. Shared by both the KPI-popup exclusion below
    // and the line-click-opens-relationship-editor handler further down.
    function _ganttDepLineAt(clientX, clientY){
      var stack = document.elementsFromPoint(clientX, clientY);
      for (var i = 0; i < stack.length; i++) {
        var el = stack[i];
        if (el.classList && Array.prototype.some.call(el.classList, function(c){ return c.indexOf('gDepId') === 0; })) {
          return el;
        }
      }
      return null;
    }

    (function(){
      var _gc = document.getElementById('gantt-container');
      if (!_gc) return;
      _gc.addEventListener('click', function(e) {
        // While the Add Relationship form's Gantt-click picker is active
        // (window._relPickFromGantt, overrelay-relations.php — a no-op
        // when that form isn't open), a bar click should feed the
        // picker, not open the KPI popup. This handler registers BEFORE
        // the picker's own interception further down the file, so it
        // needs its own bail-out here rather than relying on that one.
        if (typeof window._relPickFromGantt === 'function') {
          var _pickCheckBar = e.target.closest('.gtaskbarcontainer');
          var _pickCheckId = _pickCheckBar ? _getActIdFromBarDiv(_pickCheckBar) : null;
          if (_pickCheckId && typeof window._relPickIsActive === 'function' && window._relPickIsActive()) return;
        }
        // A dependency line/arrow can visually sit on top of a bar —
        // let the line-click handler (below, same #gantt-container
        // capture-phase listener) win in that case rather than opening
        // the KPI popup underneath it.
        if (_ganttDepLineAt(e.clientX, e.clientY)) return;
        var barEl = e.target.closest('.gtaskbarcontainer');
        if (!barEl) return;
        var actId = _getActIdFromBarDiv(barEl);
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
            + '<span><b>Actual Start:</b> <span class="' + _dateCls(db.astart, db.start) + '">' + _fmtNameDate(db.astart) + '</span></span>'
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
      function _anchor(){ var r=win.getBoundingClientRect(); win.style.left=r.left+'px'; win.style.top=r.top+'px'; win.style.width=r.width+'px'; win.style.height=r.height+'px'; win.dataset.userSized='1'; return r; }
      bindDragTouch(hdr, 'mousedown', function(e){ if(e.target.id==='gkp-close') return; var r=_anchor(); _action='drag'; _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; e.preventDefault(); });
      document.querySelectorAll('.gkp-rs').forEach(function(el){ bindDragTouch(el, 'mousedown', function(e){ var r=_anchor(); _action=el.getAttribute('data-dir'); _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height; e.preventDefault(); e.stopPropagation(); }); });
      bindDragTouch(document, 'mousemove', function(e){ if(!_action) return; var dx=e.clientX-_sx, dy=e.clientY-_sy; if(_action==='drag'){ win.style.left=Math.max(0,_ox+dx)+'px'; win.style.top=Math.max(0,_oy+dy)+'px'; } else { var l=_ox,t=_oy,w=_ow,h=_oh; if(_action.indexOf('e')>-1){w=Math.max(MIN_W,_ow+dx);} if(_action.indexOf('s')>-1){h=Math.max(MIN_H,_oh+dy);} if(_action.indexOf('w')>-1){var nw=Math.max(MIN_W,_ow-dx);l=_ox+(_ow-nw);w=nw;} if(_action.indexOf('n')>-1){var nh=Math.max(MIN_H,_oh-dy);t=_oy+(_oh-nh);h=nh;} win.style.left=l+'px'; win.style.top=t+'px'; win.style.width=w+'px'; win.style.height=h+'px'; } });
      bindDragTouch(document, 'mouseup', function(){ _action=null; });
    })();

    // Old "Bar click -> WBS Quick Entry" handler removed — the old
    // qe-modal form no longer exists (replaced by the Add IOWs popup +
    // Activity Picker). Unbind any stale copy a previous page load may
    // have registered, in case reloadGantt() ran before this deploy.
    $(document).off('click.qebar');

    // IOW row click — ANY IOW row (bare, or already has one or more
    // activities scheduled under it) opens the Activity Picker popup so
    // the user can add another activity from the Activity Library under
    // this IOW. Bound directly (capture phase, whole container) instead
    // of jQuery delegation on a specific selector — this library renders
    // the B./A. Duration cells as plain <td>s that a delegated selector
    // on tr.gname/tr[id*="child_"] was unreliably missing depending on
    // exactly which descendant received the click; resolving the row via
    // e.target.closest('tr') on every click in the container is more
    // robust against that. window._ganttWbsBareRowWgId (despite the
    // name) is populated for every WBS-tier row, not just bare ones.
    //
    // The IOW's own timeline BAR (once it has an activity scheduled) is
    // NOT inside this <tr> at all — JSGantt renders the chart/bar area
    // in an entirely separate DOM tree from the name/duration list rows
    // (see .gtaskbarcontainer, id "<divId>bardiv_<hashedId>" — same
    // hashedId as the row's "..child_<hashedId>"). Resolved separately
    // below so clicking the bar itself also opens the picker.
    (function(){
      var gc = document.getElementById('gantt-container');
      if (!gc) return;
      if (gc._iowRowClickBound) return; // avoid stacking a duplicate listener on reloadGantt()
      gc._iowRowClickBound = true;
      gc.addEventListener('click', function(e){
        // Pick Precedent/Dependent for the Add Relationship form by
        // clicking activities on the Gantt directly, instead of using
        // its IOW/Activity dropdowns — see window._relPickFromGantt
        // (overrelay-relations.php), which only actually does anything
        // while that form is open, so this is a no-op the rest of the
        // time and every other click behavior below stays untouched.
        // Takes priority over the line-click and row-click handlers
        // below: while picking is active, ANY activity click (bar or
        // row) should feed the form, not open the KPI popup or jump to
        // an existing relationship's edit view.
        if (typeof window._relPickFromGantt === 'function') {
          var pickBarEl = e.target.closest('.gtaskbarcontainer');
          var pickRowEl = pickBarEl ? null : e.target.closest('[id^="gantt-containerchildrow_"]');
          var pickActId = pickBarEl ? _getActIdFromBarDiv(pickBarEl) : (pickRowEl ? _getActIdFromRow(pickRowEl) : null);
          if (pickActId && window._relPickFromGantt(pickActId)) return;
        }

        // Dependency line/arrow click — jump straight into editing that
        // one relationship, instead of opening the plain unscoped list
        // (which is what clicking the project row/bar does, below).
        // JSGantt stamps every line segment AND its arrowhead with
        // gDepId<hash>/gDepNextId<hash> classes (see DrawDependencies in
        // jsgantt.js) — <hash> is TaskItem.getID()'s one-way hash of the
        // pId string, NOT the real activity id (jsgantt.js also stamps
        // gDepDataId/gDepNextDataId from each TaskItem's pDataObject,
        // but this app's TaskItem constructor calls never pass that
        // argument, so those classes never actually appear — do not use
        // them). The hash is resolved back to the real scheduleactivities
        // id via window._ganttActRealId, the same hash→real-id map built
        // alongside _actStatus when each TaskItem is created above.
        // See _ganttDepLineAt above for why elementsFromPoint is used
        // instead of e.target.closest().
        var depEl = _ganttDepLineAt(e.clientX, e.clientY);
        if (depEl) {
          var precMatch = depEl.className.match(/(?:^|\s)gDepId(\S+)/);
          var nextMatch = depEl.className.match(/gDepNextId(\S+)/);
          var idMap = window._ganttActRealId || {};
          if (precMatch && nextMatch && idMap[precMatch[1]] && idMap[nextMatch[1]]) {
            var precAct = idMap[precMatch[1]];
            var depAct  = idMap[nextMatch[1]];
            if (typeof window.openProjectRelationsList === 'function') {
              window.openProjectRelationsList({ precAct: precAct, depAct: depAct });
            }
            return;
          }
        }

        // Click anywhere in a leaf ACTIVITY's row (the name/duration
        // columns — NOT its bar, which is already claimed by the KPI
        // popup's own click handler above) — open the Relationships
        // popup jumped to whichever relationship(s) involve this
        // activity, on either side (precedent or dependent). Doesn't
        // require hitting the thin connector line itself; the whole row
        // is the target. _jumpToEdit (overrelay-relations.php) silently
        // no-ops if this activity has no relationships at all.
        //
        // IMPORTANT: "[id^=gantt-containerchildrow_]" is the CHART-side
        // row (jsgantt.js's childrow_ prefix, drawChartHead/drawCharBody)
        // — it's the row that physically CONTAINS the bar, not a
        // separate name-list row. Without excluding .gtaskbarcontainer
        // here too, clicking the bar matches this same row and opens
        // the Relationships popup at the same time as the KPI popup.
        var actRow = e.target.closest('.gtaskbarcontainer')
          ? null
          : e.target.closest('[id^="gantt-containerchildrow_"]');
        if (actRow) {
          var rowActId = _getActIdFromRow(actRow);
          if (rowActId) {
            if (typeof window.openProjectRelationsList === 'function') {
              window.openProjectRelationsList({ actId: rowActId });
            }
            return;
          }
        }

        var tid = null;

        var barEl = e.target.closest('.gtaskbarcontainer');
        if (barEl && barEl.id && barEl.id.indexOf('bardiv_') !== -1) {
          tid = barEl.id.slice(barEl.id.indexOf('bardiv_') + 'bardiv_'.length);
        } else {
          var row = e.target.closest('tr');
          if (row && row.id && row.id.indexOf('child_') !== -1 && row.id.indexOf('childrow_') === -1) {
            tid = row.id.slice(row.id.indexOf('child_') + 'child_'.length);
          }
        }
        if (tid === null) return;

        // Project-tier row/bar — opens the Relationships List popup
        // (overrelay-relations.php), scoped to this project via its own
        // #gantt-win-open[data-projectid] lookup. Checked before the
        // WBS-tier map below since the project row is never in that map.
        if (String(tid) === String(window._ganttProjectRowHid)) {
          if (typeof window.openProjectRelationsList === 'function') window.openProjectRelationsList();
          return;
        }

        var wgMap = window._ganttWbsBareRowWgId || {};
        var info = wgMap[tid];
        if (!info) return; // not an IOW Group row/bar — ignore (e.g. a leaf activity row/bar)

        // Resolve the IOW Group's own name from its name row.
        var iowName = '';
        document.querySelectorAll('#gantt-container tr[id*="child_"]').forEach(function(r){
          if (iowName) return; // already found
          var idx = r.id.indexOf('child_');
          if (idx === -1 || r.id.indexOf('childrow_') !== -1) return;
          if (r.id.slice(idx + 'child_'.length) !== tid) return; // exact match only, not a substring
          var nc = r.querySelector('.gtaskname');
          if (nc && nc.textContent.trim()) iowName = nc.textContent.trim();
        });

        if (typeof window.openEditActivitiesModal === 'function') window.openEditActivitiesModal(iowName, info.iowGroupId);
      }, true);
    })();
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
  bindDragTouch(hdr, 'mousedown', function(e){
    var r=_anchor(); _action='drag';
    _sx=e.clientX;_sy=e.clientY;_ox=r.left;_oy=r.top; e.preventDefault();
  });
  document.querySelectorAll('.gf-rs').forEach(function(el){
    bindDragTouch(el, 'mousedown', function(e){
      var r=_anchor(); _action=el.getAttribute('data-dir');
      _sx=e.clientX;_sy=e.clientY;_ox=r.left;_oy=r.top;_ow=r.width;_oh=r.height;
      e.preventDefault(); e.stopPropagation();
    });
  });
  bindDragTouch(document, 'mousemove', function(e){
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
  bindDragTouch(document, 'mouseup', function(){_action=null;});
})();
<?php endif; ?>

</script>
