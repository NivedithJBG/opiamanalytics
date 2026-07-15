<?php
/* newganttview.php — Clean Gantt chart view.
 * Works both as a full page (layout=true) and in the popup modal (layout=false, injected via $.ajax).
 * Data comes from actionGanttitems + actionGanttactivities (direct scheduleactivities query, no JOIN).
 * CPM coloring: gtaskpink = critical path, gtaskblue = normal.
 * Root task uses projectId as pID — never 0 (JSGantt reserves 0 as the virtual root sentinel).
 */
?>
<style>
#gantt-act-tooltip {
  display: none;
  position: fixed;
  z-index: 9999;
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 6px;
  padding: 8px 12px;
  font-size: 12px;
  color: #222;
  line-height: 1.8;
  box-shadow: 0 3px 10px rgba(0,0,0,0.15);
  pointer-events: none;
  white-space: nowrap;
}
#gantt-act-tooltip b { color: #1a2540; }

/* ── Gantt row highlight when cost modal is open ──────────────────────────── */
tr.gcm-row-highlight td { background: #fff8e1 !important; outline: 2px solid #e8820c; outline-offset: -1px; }

/* ── KPI Click Modal ─────────────────────────────────────────────────────── */
#gkm-bk { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:10000; }
#gkm-bk.gkm-open { display:block; }
#gkm-modal {
  display:none; position:fixed; z-index:10001;
  top:50%; left:50%; transform:translate(-50%,-50%);
  width:69vw; max-width:825px; height:65vh;
  background:#f0f3fa; border-radius:10px;
  box-shadow:0 8px 40px rgba(0,0,0,0.28);
  flex-direction:column; overflow:hidden;
}
#gkm-modal.gkm-open { display:flex; }
#gkm-header {
  background:#1a2540; color:#fff; padding:10px 16px;
  display:flex; align-items:center; justify-content:space-between; flex-shrink:0;
}
#gkm-title { font-size:13px; font-weight:600; font-family:'Barlow Condensed',sans-serif; }
#gkm-close { background:none; border:none; color:#fff; font-size:18px; cursor:pointer; padding:0 4px; line-height:1; }
#gkm-loading { text-align:center; padding:40px; font-size:13px; color:#5a6e8c; }
#gkm-body { flex:1; min-height:0; display:flex; flex-direction:column; padding:10px; gap:8px; overflow:hidden; }
#gkm-content { display:flex; flex-direction:column; gap:8px; flex:1; min-height:0; }
#gkm-row1 { display:flex; gap:8px; flex:1; min-height:0; }
#gkm-row2 { display:flex; gap:8px; flex:1; min-height:0; }
.gkm-panel {
  flex:1; background:#fff; border-radius:6px; border:1px solid #dde3ef;
  display:flex; flex-direction:column; overflow:hidden; min-width:0;
}
.gkm-panel-title {
  font-size:10px; font-weight:700; color:#e8efff; text-transform:uppercase;
  letter-spacing:0.04em; padding:5px 8px; border-bottom:1px solid #0d1f3c; flex-shrink:0;
  background:#1a2540;
}
.gkm-panel-body { flex:1; min-height:0; overflow:auto; display:flex; flex-direction:column; }

/* ── KPI Hover Popup ─────────────────────────────────────────────────────── */
#gkp-popup {
  display: none; position: fixed; z-index: 9998;
  width: 680px; background: #f0f3fa;
  border-radius: 10px; box-shadow: 0 8px 36px rgba(0,0,0,0.32);
  overflow: hidden; pointer-events: none;
}
#gkp-popup.gkp-visible { display: block; }
#gkp-hdr {
  background: #1a2540; color: #fff; padding: 7px 12px;
  font-size: 12px; font-weight: 600; font-family: 'Barlow Condensed', sans-serif;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
#gkp-body {
  display: flex; flex-direction: column; gap: 5px; padding: 6px;
}
#gkp-row1, #gkp-row2 { display: flex; gap: 5px; height: 175px; }
.gkp-panel {
  flex: 1; background: #fff; border-radius: 5px; border: 1px solid #dde3ef;
  display: flex; flex-direction: column; overflow: hidden; min-width: 0;
}
.gkp-panel-title {
  font-size: 9px; font-weight: 700; color: #e8efff; text-transform: uppercase;
  letter-spacing: 0.04em; padding: 4px 7px; flex-shrink: 0;
  background: #1a2540; border-bottom: 1px solid #0d1f3c;
}
.gkp-panel-body { flex: 1; min-height: 0; overflow: hidden; display: flex; flex-direction: column; }

/* ── Gantt Cost Hover Popup ─────────────────────────────────────────────────── */
#gcm-popup {
  display: none; position: fixed; z-index: 9998;
  width: 760px; background: #f0f3fa;
  border-radius: 10px; box-shadow: 0 8px 36px rgba(0,0,0,0.32);
  overflow: hidden; pointer-events: none;
}
#gcm-popup.gcm-visible { display: block; }
#gcm-pop-hdr {
  background: #1a2540; color: #fff; padding: 7px 12px;
  font-size: 12px; font-weight: 600; font-family: 'Barlow Condensed', sans-serif;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
#gcm-pop-body {
  display: flex; flex-direction: column; gap: 5px; padding: 6px;
}
#gcm-pop-row1, #gcm-pop-row2 { display: flex; gap: 5px; height: 205px; }
.gcm-pop-panel {
  flex: 1; background: #fff; border-radius: 5px; border: 1px solid #dde3ef;
  display: flex; flex-direction: column; overflow: hidden; min-width: 0;
}
.gcm-pop-panel-title {
  font-size: 9px; font-weight: 700; color: #e8efff; text-transform: uppercase;
  letter-spacing: 0.04em; padding: 4px 7px; flex-shrink: 0;
  background: #1a2540; border-bottom: 1px solid #0d1f3c;
}
.gcm-pop-panel-body { flex: 1; min-height: 0; overflow: auto; display: flex; flex-direction: column; }
#gcm-pop-loading { text-align: center; padding: 18px; font-size: 11px; color: #5a6e8c; }

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

#gantt-toolbar {
  padding: 8px 0;
  margin-bottom: 6px;
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
#gantt-container {
  border: 2px solid #000;
  overflow-y: auto;
  overflow-x: hidden;
  width: 100%;
  height: calc(100vh - 220px);
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
  min-width: 260px !important;
  width: 260px !important;
  max-width: 260px !important;
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

<div class="container-fluid">
<div class="row">
<div class="col-md-12">

  <div id="gantt-act-tooltip"></div>
<div id="gantt-toolbar">
    <button class="btn-opiam" id="btn-manage-relations">Manage Relations</button>
    <button class="btn-opiam" id="btn-refresh-cpm">Refresh Critical Path</button>
    <button class="btn-opiam" id="btn-quick-entry" style="background:#00838f;" title="Quick Entry">&#9998; Quick Entry</button>
    <span id="gantt-status" style="font-size:12px;color:#666;margin-left:8px;"></span>
  </div>

  <div id="gantt-container">
    <div id="gantt-loading" style="padding:50px;text-align:center;">Loading Gantt chart&hellip;</div>
  </div>

  <div class="gantt-legend-row" style="margin-top:10px;">
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
</div>
</div>

<!-- Gantt Cost Hover Popup -->
<div id="gcm-popup">
  <div id="gcm-pop-hdr">Cost Dashboard</div>
  <div id="gcm-pop-body">
    <div id="gcm-pop-loading">Loading&hellip;</div>
    <div id="gcm-pop-row1" style="display:none">
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Unit Cost of Activity</div><div class="gcm-pop-panel-body" id="gm-cd-g5" style="align-items:center;justify-content:center"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Work Done</div><div class="gcm-pop-panel-body" id="gm-cd-g4" style="align-items:center;justify-content:center"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Cost of Activity</div><div class="gcm-pop-panel-body" id="gm-cd-g2"></div></div>
    </div>
    <div id="gcm-pop-row2" style="display:none">
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Unit Cost of Resources</div><div class="gcm-pop-panel-body" id="gm-cd-c6"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Consumption of Resources</div><div class="gcm-pop-panel-body" id="gm-cd-c7"></div></div>
      <div class="gcm-pop-panel"><div class="gcm-pop-panel-title">Cost of Resources</div><div class="gcm-pop-panel-body" id="gm-cd-rcost"></div></div>
    </div>
  </div>
</div>

<!-- KPI Click Modal -->
<div id="gkm-bk"></div>
<div id="gkm-modal">
  <div id="gkm-header">
    <span id="gkm-title">KPI Dashboard</span>
    <button id="gkm-close" title="Close">&times;</button>
  </div>
  <div id="gkm-body">
    <div id="gkm-loading">Loading&hellip;</div>
    <div id="gkm-content" style="display:none">
      <div id="gkm-row1">
        <div class="gkm-panel" style="flex:1"><div class="gkm-panel-title">Target Production</div><div class="gkm-panel-body" id="gkm-tp"></div></div>
        <div class="gkm-panel" style="flex:1.4"><div class="gkm-panel-title">Activity Duration</div><div class="gkm-panel-body" id="gkm-dur"></div></div>
      </div>
      <div id="gkm-row2">
        <div class="gkm-panel"><div class="gkm-panel-title">Capacity Utilisation</div><div class="gkm-panel-body" id="gkm-g5"></div></div>
        <div class="gkm-panel"><div class="gkm-panel-title">Cycle Time</div><div class="gkm-panel-body" id="gkm-g4"></div></div>
        <div class="gkm-panel"><div class="gkm-panel-title">Productivity</div><div class="gkm-panel-body" id="gkm-g3"></div></div>
      </div>
    </div>
  </div>
</div>

<!-- KPI Hover Popup -->
<div id="gkp-popup">
  <div id="gkp-hdr">KPI</div>
  <div id="gkp-body">
    <div id="gkp-row1">
      <div class="gkp-panel" style="flex:1"><div class="gkp-panel-title">Target Production</div><div class="gkp-panel-body" id="gkp-tp"></div></div>
      <div class="gkp-panel" style="flex:1.4"><div class="gkp-panel-title">Activity Duration</div><div class="gkp-panel-body" id="gkp-dur"></div></div>
    </div>
    <div id="gkp-row2">
      <div class="gkp-panel"><div class="gkp-panel-title">Capacity Utilisation</div><div class="gkp-panel-body" id="gkp-g5"></div></div>
      <div class="gkp-panel"><div class="gkp-panel-title">Cycle Time</div><div class="gkp-panel-body" id="gkp-g4"></div></div>
      <div class="gkp-panel"><div class="gkp-panel-title">Productivity</div><div class="gkp-panel-body" id="gkp-g3"></div></div>
    </div>
  </div>
</div>

<script language="javascript" src="<?= Yii::$app->request->baseUrl ?>/jsnew/projectsmain/jsgantt.js"></script>
<script type="text/javascript">
(function($) {
  var criticalid = [];
  var projectId  = <?= (int)$projectId ?>;
  var projectName = '<?= addslashes(isset($project) && $project ? $project->Name : 'Project') ?>';

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
      url: '../projectsmain/ganttlastactivity',
      dataType: 'json',
      async: false,
      data: { projectid: projectId },
      success: function(data) {
        if (!data.id) { if (callback) callback(); return; }
        $.ajax({
          type: 'POST',
          url: '../projectsmain/getcriticalpath',
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
  function loadGantt() {
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
      url: '../projectsmain/ganttitems',
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
            url: '../projectsmain/ganttactivities',
            dataType: 'json',
            async: false,
            data: { itemId: item.scheduleitem_id },
            success: (function(parentId) {
              return function(actData) {
                if (!actData.result) return;
                for (var j = 0; j < actData.result.length; j++) {
                  var act = actData.result[j];
                  if (!safeDate(act.actual_start_date) || !safeDate(act.actual_end_date)) continue;

                  var pId    = act.id + 'ABC' + act.id;
                  var pClass = ($.inArray(Number(act.id), criticalid) !== -1)
                               ? 'gtaskpink' : 'gtaskblue';

                  // A. Duration: computed from actual progress reports
                  var actDur = (act.actual_duration !== null && act.actual_duration !== undefined) ? act.actual_duration : '';
                  // A. Start: activity start date from schedule_progress_report (falls back to planned start when no progress logged)
                  var aStart = safeDate(act.spr_start_date);
                  // A. End: A. Start + A. Duration - 1 (not the last report date)
                  var aEndComputed = (aStart && act.actual_duration) ? addDays(aStart, act.actual_duration - 1) : null;

                  // No progress logged yet but the planned start has already passed: push
                  // the end date out by the days late, same "start delay" the dashboard's
                  // Upcoming bucket already shows, so the Gantt bar/overlay reflect it too.
                  var noProgress = (act.actual_duration === null || act.actual_duration === undefined);
                  if (noProgress && aStart && aStart < todayStr) {
                    var startDelayDays = Math.round((new Date(todayStr) - new Date(aStart)) / 86400000);
                    if (startDelayDays > 0) {
                      var extendedDur = (parseFloat(act.old_duration) || 1) + startDelayDays;
                      aEndComputed = addDays(aStart, extendedDur - 1);
                      actDur = extendedDur;
                    }
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
                    critical:    (pClass === 'gtaskpink')
                  };
                  // B. columns show schedule values; A. columns fall back to B. when no progress
                  _actCells[_ti.getID()] = {
                    dur:    act.old_duration,
                    start:  act.actual_start_date,
                    end:    act.actual_end_date,
                    actdur: actDur !== '' ? actDur : act.old_duration,
                    astart: aStart || act.actual_start_date,
                    aend:   aEndComputed || act.actual_end_date,
                    rawId:  act.id
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

    function _makeHdr(id, txt, width) {
      var td = document.createElement('td');
      td.id = id;
      td.className = 'gtaskheading';
      td.style.cssText = 'text-align:center;font-weight:600;font-size:11px;width:' + width + 'px;min-width:' + width + 'px;';
      td.textContent = txt;
      return td;
    }

    // Inject KPI header then Cost header after A.Duration (gres)
    if (_resHdr && !document.getElementById('gcost-hdr-cell')) {
      var _kpiHdrInserted = _resHdr.parentNode.insertBefore(_makeHdr('gkpi-hdr-cell', 'KPI', 50), _resHdr.nextSibling);
      _resHdr.parentNode.insertBefore(_makeHdr('gcost-hdr-cell', 'Cost', 50), _kpiHdrInserted.nextSibling);
    }

    // Apply progressive indentation + patch B. columns and A. columns with formatted values
    var _tasks = g.getList ? g.getList() : [];
    for (var _i = 0; _i < _tasks.length; _i++) {
      var _t = _tasks[_i];
      var _tid = _t.getID();
      var _row = document.getElementById('gantt-container' + 'child_' + _tid);
      if (!_row) continue;

      // Indentation + prepend status dot inside the activity name cell
      var _cell = _row.querySelector('td.gtaskname');
      if (_cell) {
        var _indent = [0, 4, 18, 32, 46][_t.getLevel()] || 4;
        _cell.style.paddingLeft = _indent + 'px';

        // Prepend dot for leaf activities only (not group rows)
        var _st = _actStatus[_tid];
        if (_st && !_row.querySelector('.act-dot-inline')) {
          var _dot = document.createElement('span');
          _dot.className = 'act-dot-inline';
          _dot.style.cssText = 'display:inline-block;width:9px;height:9px;border-radius:50%;margin-right:5px;vertical-align:middle;flex-shrink:0;';
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
          _cell.insertBefore(_dot, _cell.firstChild);
        }
      }

      // Leaf activity: patch B. Duration and A. Duration
      var _db = _actCells[_tid];
      if (_db) {
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

      // Inject KPI cell then Cost cell after A.Duration (gres) — all rows
      var _resTd = _row.querySelector('td.gres');
      if (_resTd && !_row.querySelector('td.gcol-kpi')) {
        // KPI cell
        var _kpiTd = document.createElement('td');
        _kpiTd.className = 'gcol-kpi';
        _kpiTd.style.cssText = 'width:50px;min-width:50px;text-align:center;padding:0;vertical-align:middle;';
        if (_db) {
          var _kpiIcon = document.createElement('span');
          _kpiIcon.style.cssText = 'display:inline-block;cursor:pointer;vertical-align:middle;';
          _kpiIcon.innerHTML = '<svg width="14" height="12" viewBox="0 0 14 12" xmlns="http://www.w3.org/2000/svg">'
            + '<rect x="0" y="7" width="3" height="5" rx="0.5" fill="#00838f"/>'
            + '<rect x="4" y="4" width="3" height="8" rx="0.5" fill="#00838f"/>'
            + '<rect x="8" y="1" width="3" height="11" rx="0.5" fill="#00838f"/>'
            + '<polyline points="1.5,6 5.5,3 9.5,0.5 13,0" fill="none" stroke="#004d55" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>'
            + '</svg>';
          _kpiIcon.setAttribute('data-kpiactid', _db.rawId);
          _kpiTd.appendChild(_kpiIcon);
        }
        _resTd.parentNode.insertBefore(_kpiTd, _resTd.nextSibling);
        // Cost cell (after KPI)
        var _costTd = document.createElement('td');
        _costTd.className = 'gcol-cost';
        _costTd.style.cssText = 'width:50px;min-width:50px;text-align:center;padding:0;vertical-align:middle;';
        var _costIcon = document.createElement('span');
        _costIcon.style.cssText = 'display:inline-block;width:8px;height:8px;background:#546e7a;border-radius:2px;cursor:pointer;';
        // Store raw activity DB id so click handler can POST it
        if (_db) _costIcon.setAttribute('data-actid', _db.rawId);
        _costTd.appendChild(_costIcon);
        _kpiTd.parentNode.insertBefore(_costTd, _kpiTd.nextSibling);
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

    // ── Activity name tooltip — must run AFTER cloneNode so listeners survive ──
    (function() {
      var _tip = document.getElementById('gantt-act-tooltip');
      if (!_tip) return;

      function _fmt(d) {
        if (!d || d === '0000-00-00') return '—';
        var p = d.split('-');
        return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : d;
      }

      var _tasks2 = g.getList ? g.getList() : [];
      for (var _ti2 = 0; _ti2 < _tasks2.length; _ti2++) {
        var _tid2 = _tasks2[_ti2].getID();
        var _db2  = _actCells[_tid2];
        if (!_db2) continue;
        // getElementById finds the cloned row (same ID, now in DOM)
        var _row2 = document.getElementById('gantt-container' + 'child_' + _tid2);
        if (!_row2) continue;
        var _nameCell = _row2.querySelector('td.gtaskname');
        if (!_nameCell) continue;

        (function(db, cell) {
          cell.addEventListener('mouseenter', function(e) {
            _tip.innerHTML =
              '<b>Planned Start:</b> ' + _fmt(db.start)  + '<br>' +
              '<b>Actual Start:</b> '  + _fmt(db.astart) + '<br>' +
              '<b>Planned End:</b> '   + _fmt(db.end)    + '<br>' +
              '<b>Actual End:</b> '    + _fmt(db.aend);
            _tip.style.display = 'block';
            _tip.style.left = (e.clientX + 14) + 'px';
            _tip.style.top  = (e.clientY + 14) + 'px';
          });
          cell.addEventListener('mousemove', function(e) {
            _tip.style.left = (e.clientX + 14) + 'px';
            _tip.style.top  = (e.clientY + 14) + 'px';
          });
          cell.addEventListener('mouseleave', function() {
            _tip.style.display = 'none';
          });
        })(_db2, _nameCell);
      }
    })();
  }

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
        : arc(0, 0.5, '#8B0000') + arc(0.5, 1, '#81C784');
      var nr = r - 15, na = Math.PI * (1 - f);
      var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
      var fillCol = (trackStyle === 'flat') ? '#0d1f6e' : (trackStyle === 'cost') ? '#1b9e8e' : '#1a3a6b';
      var dotCol  = (trackStyle === 'flat') ? '#a8d4f5' : '#dce3ef';
      var centreVal = (trackStyle === 'cost') ? _fmtCost(val) : _fm(val);
      var lblSvg = '';
      if (lbl1) lblSvg += '<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">' + lbl1 + ' <tspan font-weight="700">' + v1 + '</tspan></text>';
      if (lbl2) lblSvg += '<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">' + lbl2 + ' <tspan font-weight="700">' + v2 + '</tspan></text>';
      var svg = '<svg width="210" height="138" viewBox="0 0 210 138" xmlns="http://www.w3.org/2000/svg">'
        + trackSvg
        + (f > 0 ? arc(0, f, fillCol, (trackStyle === 'cost' ? 'butt' : 'round')) : '')
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="' + dotCol + '"/>'
        + '<text x="' + cx + '" y="' + (cy - 18) + '" text-anchor="middle" font-size="22" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">' + centreVal + '</text>'
        + lblSvg
        + '</svg>';
      el.innerHTML += svg;
    }

    // 1. Unit Cost of Resources → gm-cd-c6
    function _renderUnitCostOfResource(items, actName) {
      var el = document.getElementById('gm-cd-c6');
      if (!el) return;
      items = items || [];
      if (!items.length) { el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No resources allocated</div>'; return; }
      var maxVal = 0;
      items.forEach(function(r) {
        var est = +r.rate || 0;
        var act = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined) ? +r.actual_unit_cost : est;
        if (Math.max(est, act) > maxVal) maxVal = Math.max(est, act);
      });
      if (maxVal === 0) maxVal = 1;

      // Colour palette for resources (cycles if more than palette length)
      var palette = ['#3461b8','#00838f','#e8820c','#8e44ad','#27ae60','#c0392b','#2980b9','#d4845a'];

      var barCols = '', valRow = '', lblRow = '', legendRows = '';
      items.forEach(function(r, idx) {
        var est = +r.rate || 0;
        var hasActual = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
        var act = hasActual ? +r.actual_unit_cost : est;
        var unit = r.unit ? ' /' + _shu(r.unit) : '';
        var col = palette[idx % palette.length];
        var diff = act - est;
        var diffCol = diff > 0 ? '#e8820c' : '#1b9e8e';
        var valCol  = diff > 0 ? '#e8820c' : (diff < 0 ? '#1b9e8e' : col);

        // Single stacked bar: base = estimated (resource colour), overlay = difference
        // Over: [est base | orange extension on top]
        // Under: [act base | teal saving on top up to est level]
        // Even: just the base
        var estH  = (est / maxVal * 100).toFixed(1);
        var actH  = (act / maxVal * 100).toFixed(1);
        var barInner;
        if (diff > 0) {
          var diffH = ((diff) / maxVal * 100).toFixed(1);
          barInner =
            '<div style="width:100%;height:' + diffH + '%;background:#e8820c;border-radius:2px 2px 0 0;flex-shrink:0"></div>'
            + '<div style="width:100%;height:' + estH + '%;background:' + col + ';flex-shrink:0"></div>';
        } else if (diff < 0) {
          var saveH = (Math.abs(diff) / maxVal * 100).toFixed(1);
          barInner =
            '<div style="width:100%;height:' + saveH + '%;background:#1b9e8e;border-radius:2px 2px 0 0;flex-shrink:0"></div>'
            + '<div style="width:100%;height:' + actH + '%;background:' + col + ';flex-shrink:0"></div>';
        } else {
          barInner = '<div style="width:100%;height:' + estH + '%;background:' + col + ';border-radius:2px 2px 0 0;flex-shrink:0"></div>';
        }

        barCols +=
          '<div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;height:100%;padding:0 3px">'
          + barInner
          + '</div>';

        // Act value above bar (coloured by diff), Est value smaller below it
        valRow +=
          '<div style="flex:1;text-align:center;padding-bottom:1px">'
          + '<div style="font-size:8px;font-weight:700;color:' + valCol + '">' + _fmtCost(act) + '</div>'
          + '<div style="font-size:7px;color:#888">' + _fmtCost(est) + '</div>'
          + '</div>';

        // Index number below bars
        lblRow += '<div style="flex:1;text-align:center;font-size:9px;color:#1a2540;font-weight:700;padding-top:2px">' + (idx + 1) + '</div>';

        // Legend row
        legendRows +=
          '<div style="display:flex;align-items:baseline;gap:5px;padding:2px 6px;border-bottom:1px solid #f0f3fa;font-size:10px">'
          + '<span style="font-weight:700;color:#1a2540;flex-shrink:0;min-width:12px">' + (idx + 1) + '.</span>'
          + '<span style="display:inline-block;width:8px;height:8px;border-radius:2px;background:' + col + ';flex-shrink:0;margin-bottom:1px"></span>'
          + '<span style="flex:1;color:#1a2540;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + (r.name||'') + '">' + (r.name||'') + '</span>'
          + '<span style="flex-shrink:0;color:#4a5568;margin-left:6px">Est&nbsp;<b style="color:#000">' + _fmtFull(est) + unit + '</b></span>'
          + '<span style="flex-shrink:0;color:#4a5568;margin-left:6px">Act&nbsp;<b style="color:' + valCol + '">' + _fmtFull(act) + unit + '</b></span>'
          + '</div>';
      });

      // Chart key
      var key = '<div style="display:flex;gap:12px;padding:3px 6px;flex-shrink:0">'
        + '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#4a5568;border-radius:1px;margin-right:3px;vertical-align:middle"></span>Estimated</span>'
        + '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#e8820c;border-radius:1px;margin-right:3px;vertical-align:middle"></span>Over</span>'
        + '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#1b9e8e;border-radius:1px;margin-right:3px;vertical-align:middle"></span>Under</span>'
        + '</div>';

      el.innerHTML =
        '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0">' + _sh(actName||'', 40) + '</div>'
        + '<div style="display:flex;flex-direction:column;flex:1;min-height:0;overflow:hidden">'
        +   '<div style="display:flex;gap:2px;padding:2px 6px 0;flex-shrink:0">' + valRow + '</div>'
        +   '<div style="display:flex;gap:2px;flex:1;min-height:0;padding:0 6px;align-items:flex-end;border-bottom:1px solid #c8d0e0">' + barCols + '</div>'
        +   '<div style="display:flex;gap:2px;padding:2px 6px;flex-shrink:0">' + lblRow + '</div>'
        +   key
        +   '<div style="overflow-y:auto;flex-shrink:0;max-height:90px;border-top:1px solid #e8efff;margin-top:2px">' + legendRows + '</div>'
        + '</div>';
    }

    // 2. Consumption of Resources → gm-cd-c7
    function _renderResourceConsumption(items, actName, lastQty, actUnit) {
      var el = document.getElementById('gm-cd-c7');
      if (!el) return;
      items = items || [];
      if (!items.length) { el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No resources allocated</div>'; return; }
      var maxVal = 0;
      items.forEach(function(r) {
        var est = +r.planned_consumption || 0;
        var typeId = +r.type_id;
        var isMaterial = [2, 6, 7, 8].indexOf(typeId) >= 0;
        var isSC = typeId === 4;
        var hasActual = (isMaterial && r.indent_raised) || (isSC && r.has_mb);
        var act = hasActual ? (+r.actual_consumption || 0) : est;
        if (Math.max(est, act) > maxVal) maxVal = Math.max(est, act);
      });
      if (maxVal === 0) maxVal = 1;

      var palette = ['#3461b8','#00838f','#e8820c','#8e44ad','#27ae60','#c0392b','#2980b9','#d4845a'];
      var barCols = '', valRow = '', lblRow = '', legendRows = '';

      items.forEach(function(r, idx) {
        var est = +r.planned_consumption || 0;
        var typeId = +r.type_id;
        var isMaterial = [2, 6, 7, 8].indexOf(typeId) >= 0;
        var isSC = typeId === 4;
        var hasActual = (isMaterial && r.indent_raised) || (isSC && r.has_mb);
        var act = hasActual ? (+r.actual_consumption || 0) : est;
        var unit = r.unit ? _shu(r.unit) : (r.task_unit ? _shu(r.task_unit) : '');
        var col = palette[idx % palette.length];
        var diff = act - est;
        var valCol = diff > 0 ? '#e8820c' : (diff < 0 ? '#1b9e8e' : col);

        var estH  = (est / maxVal * 100).toFixed(1);
        var actH  = (act / maxVal * 100).toFixed(1);
        var barInner;
        if (diff > 0) {
          var diffH = (diff / maxVal * 100).toFixed(1);
          barInner =
            '<div style="width:100%;height:' + diffH + '%;background:#e8820c;border-radius:2px 2px 0 0;flex-shrink:0"></div>'
            + '<div style="width:100%;height:' + estH + '%;background:' + col + ';flex-shrink:0"></div>';
        } else if (diff < 0) {
          var saveH = (Math.abs(diff) / maxVal * 100).toFixed(1);
          barInner =
            '<div style="width:100%;height:' + saveH + '%;background:#1b9e8e;border-radius:2px 2px 0 0;flex-shrink:0"></div>'
            + '<div style="width:100%;height:' + actH + '%;background:' + col + ';flex-shrink:0"></div>';
        } else {
          barInner = '<div style="width:100%;height:' + estH + '%;background:' + col + ';border-radius:2px 2px 0 0;flex-shrink:0"></div>';
        }

        barCols +=
          '<div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;height:100%;padding:0 3px">'
          + barInner
          + '</div>';

        valRow +=
          '<div style="flex:1;text-align:center;padding-bottom:1px">'
          + '<div style="font-size:8px;font-weight:700;color:' + valCol + '">' + _fm(act) + (unit ? ' '+unit : '') + '</div>'
          + '<div style="font-size:7px;color:#888">' + _fm(est) + (unit ? ' '+unit : '') + '</div>'
          + '</div>';

        lblRow += '<div style="flex:1;text-align:center;font-size:9px;color:#1a2540;font-weight:700;padding-top:2px">' + (idx + 1) + '</div>';

        legendRows +=
          '<div style="display:flex;align-items:baseline;gap:5px;padding:2px 6px;border-bottom:1px solid #f0f3fa;font-size:10px">'
          + '<span style="font-weight:700;color:#1a2540;flex-shrink:0;min-width:12px">' + (idx + 1) + '.</span>'
          + '<span style="display:inline-block;width:8px;height:8px;border-radius:2px;background:' + col + ';flex-shrink:0;margin-bottom:1px"></span>'
          + '<span style="flex:1;color:#1a2540;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + (r.name||'') + '">' + (r.name||'') + '</span>'
          + '<span style="flex-shrink:0;color:#4a5568;margin-left:6px">Est&nbsp;<b style="color:#000">' + _fmtFull(est) + (unit ? ' '+unit : '') + '</b></span>'
          + '<span style="flex-shrink:0;color:#4a5568;margin-left:6px">Act&nbsp;<b style="color:' + valCol + '">' + _fmtFull(act) + (unit ? ' '+unit : '') + '</b></span>'
          + '</div>';
      });

      var key = '<div style="display:flex;gap:12px;padding:3px 6px;flex-shrink:0">'
        + '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#4a5568;border-radius:1px;margin-right:3px;vertical-align:middle"></span>Estimated</span>'
        + '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#e8820c;border-radius:1px;margin-right:3px;vertical-align:middle"></span>Over</span>'
        + '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#1b9e8e;border-radius:1px;margin-right:3px;vertical-align:middle"></span>Under</span>'
        + '</div>';

      el.innerHTML =
        '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0">' + _sh(actName||'', 40) + '</div>'
        + '<div style="display:flex;flex-direction:column;flex:1;min-height:0;overflow:hidden">'
        +   '<div style="display:flex;gap:2px;padding:2px 6px 0;flex-shrink:0">' + valRow + '</div>'
        +   '<div style="display:flex;gap:2px;flex:1;min-height:0;padding:0 6px;align-items:flex-end;border-bottom:1px solid #c8d0e0">' + barCols + '</div>'
        +   '<div style="display:flex;gap:2px;padding:2px 6px;flex-shrink:0">' + lblRow + '</div>'
        +   key
        +   '<div style="overflow-y:auto;flex-shrink:0;max-height:90px;border-top:1px solid #e8efff;margin-top:2px">' + legendRows + '</div>'
        + '</div>';
    }

    // 3. Cost of Resources → gm-cd-rcost
    function _renderResourceCost(items, actName, actUnit) {
      var el = document.getElementById('gm-cd-rcost');
      if (!el) return;
      items = items || [];
      if (!items.length) { el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No resources</div>'; return; }
      var groups = {};
      items.forEach(function(r) {
        var key = r.type_name || 'Other';
        if (!groups[key]) groups[key] = { est: 0, act: 0 };
        var estUC = +r.rate || 0;
        var actUC = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined) ? +r.actual_unit_cost : estUC;
        groups[key].est += estUC * (+r.planned_consumption || 0);
        groups[key].act += actUC * (+r.actual_consumption  || 0);
      });
      var labels = Object.keys(groups);
      if (!labels.length) { el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No data</div>'; return; }
      var maxVal = 0;
      labels.forEach(function(k) { maxVal = Math.max(maxVal, groups[k].est, groups[k].act); });
      if (maxVal === 0) maxVal = 1;
      var estRow = '', actRow = '', bars = '', lblRow = '', legendRows3 = '';
      labels.forEach(function(k, idx3) {
        var est = groups[k].est, act = groups[k].act;
        var diff = act - est;
        var actCol = diff > 0 ? '#e8820c' : (diff < 0 ? '#1b9e8e' : '#4a5568');
        var estPct = (est / maxVal * 100).toFixed(1);
        var actPct = (act / maxVal * 100).toFixed(1);
        estRow += '<div style="flex:1;text-align:center;font-size:9px;font-weight:700;color:#4a5568">' + _fmtCost(est) + '</div>';
        actRow += '<div style="flex:1;text-align:center;font-size:9px;font-weight:700;color:' + actCol + '">' + _fmtCost(act) + '</div>';
        var barInner;
        if (diff > 0) {
          var diffPct = (diff / maxVal * 100).toFixed(1);
          barInner = '<div style="width:100%;height:' + diffPct + '%;background:#e8820c;border-radius:2px 2px 0 0;flex-shrink:0"></div>'
                   + '<div style="width:100%;height:' + estPct + '%;background:#4a5568;flex-shrink:0"></div>';
        } else if (diff < 0) {
          var savePct = (Math.abs(diff) / maxVal * 100).toFixed(1);
          barInner = '<div style="width:100%;height:' + savePct + '%;background:#1b9e8e;border-radius:2px 2px 0 0;flex-shrink:0"></div>'
                   + '<div style="width:100%;height:' + actPct + '%;background:#4a5568;flex-shrink:0"></div>';
        } else {
          barInner = '<div style="width:100%;height:' + estPct + '%;background:#4a5568;border-radius:2px 2px 0 0;flex-shrink:0"></div>';
        }
        bars += '<div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;height:100%;padding:0 3px">' + barInner + '</div>';
        lblRow += '<div style="flex:1;text-align:center;font-size:9px;color:#1a2540;font-weight:600;padding-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + k + '">' + (idx3 + 1) + '</div>';
        legendRows3 +=
          '<div style="display:flex;align-items:baseline;gap:5px;padding:2px 6px;border-bottom:1px solid #f0f3fa;font-size:10px">'
          + '<span style="font-weight:700;color:#1a2540;flex-shrink:0;min-width:12px">' + (idx3 + 1) + '.</span>'
          + '<span style="display:inline-block;width:8px;height:8px;border-radius:2px;background:#4a5568;flex-shrink:0;margin-bottom:1px"></span>'
          + '<span style="flex:1;color:#1a2540;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + k + '">' + k + '</span>'
          + '<span style="flex-shrink:0;color:#4a5568;margin-left:6px">Est&nbsp;<b style="color:#000">' + _fmtFull(est) + (actUnit ? ' ' + actUnit : '') + '</b></span>'
          + '<span style="flex-shrink:0;color:#4a5568;margin-left:6px">Act&nbsp;<b style="color:' + actCol + '">' + _fmtFull(act) + (actUnit ? ' ' + actUnit : '') + '</b></span>'
          + '</div>';
      });
      el.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0">' + _sh(actName||'', 40) + '</div>'
        + '<div style="display:flex;flex-direction:column;flex:1;min-height:0;overflow:hidden">'
        +   '<div style="display:flex;gap:2px;padding:2px 6px 0;flex-shrink:0">' + estRow + '</div>'
        +   '<div style="display:flex;gap:2px;padding:0 6px;flex-shrink:0">' + actRow + '</div>'
        +   '<div style="display:flex;gap:2px;flex:1;min-height:0;padding:0 6px;align-items:flex-end;border-bottom:1px solid #c8d0e0">' + bars + '</div>'
        +   '<div style="display:flex;gap:2px;padding:2px 6px;flex-shrink:0">' + lblRow + '</div>'
        +   '<div style="display:flex;gap:12px;padding:3px 6px;flex-shrink:0;flex-wrap:wrap">'
        +     '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#4a5568;border-radius:1px;margin-right:3px;vertical-align:middle"></span>Estimated</span>'
        +     '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#e8820c;border-radius:1px;margin-right:3px;vertical-align:middle"></span>Over</span>'
        +     '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#1b9e8e;border-radius:1px;margin-right:3px;vertical-align:middle"></span>Under</span>'
        +   '</div>'
        +   '<div style="overflow-y:auto;flex-shrink:0;max-height:80px;border-top:1px solid #e8efff;margin-top:2px">' + legendRows3 + '</div>'
        + '</div>';
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
      var svg='<svg width="210" height="138" viewBox="0 0 210 138" xmlns="http://www.w3.org/2000/svg">'
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
        +'<div>Actual Cost of Work Done &nbsp;<span style="color:'+(overWD?'#e8820c':'#1b9e8e')+'">' + _fmtFull(actCostWD) + '</span></div>'
        +'<div style="color:'+(overWD?'#e8820c':'#1b9e8e')+'">Difference &nbsp;'+(diffWDLabel||_fmtFull(0))+'</div>'
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
      var pw = pop.offsetWidth || 620, ph = pop.offsetHeight || 280;
      var vw = window.innerWidth, vh = window.innerHeight;
      var rect = anchorEl ? anchorEl.getBoundingClientRect() : null;
      var left = rect ? Math.min(rect.right + 10, vw - pw - 8) : vw - pw - 8;
      if (left < 4) left = 4;
      var top = rect ? rect.top - 20 : vh / 2 - ph / 2;
      if (top + ph > vh - 8) top = vh - ph - 8;
      if (top < 4) top = 4;
      pop.style.left = left + 'px';
      pop.style.top  = top  + 'px';
    }

    function _hideCostPopup() {
      clearTimeout(_gcpTimer);
      if (_gcpXhr) { _gcpXhr.abort(); _gcpXhr = null; }
      var pop = document.getElementById('gcm-popup');
      if (pop) pop.classList.remove('gcm-visible');
    }

    function _showCostPopup(actId, anchorEl) {
      var pop = document.getElementById('gcm-popup');
      if (!pop) return;

      function _doRender(d) {
        document.getElementById('gcm-pop-hdr').textContent = 'Cost Dashboard — ' + (d.activity_name || '');
        ['gm-cd-c6','gm-cd-c7','gm-cd-rcost','gm-cd-g5','gm-cd-g4','gm-cd-g2'].forEach(function(id) {
          var el = document.getElementById(id); if (el) el.innerHTML = '';
        });
        document.getElementById('gcm-pop-loading').style.display = 'none';
        document.getElementById('gcm-pop-row1').style.display = 'flex';
        document.getElementById('gcm-pop-row2').style.display = 'flex';
        _renderUnitCostOfResource(d.items, d.activity_name);
        _renderResourceConsumption(d.items, d.activity_name, d.last_report_qty, d.unit);
        _renderResourceCost(d.items, d.activity_name, d.unit);
        _renderUnitCostOfActivity(d.items, d.activity_name, d.unit, d.schedule_qty);
        _renderValueOfWorkDone(d);
        _renderCostOfActivity(d);
        _positionCostPopup(anchorEl);
      }

      if (_gcpCache[actId]) {
        document.getElementById('gcm-pop-loading').style.display = 'none';
        pop.classList.add('gcm-visible');
        _positionCostPopup(anchorEl);
        _doRender(_gcpCache[actId]);
        return;
      }

      // Show loading state
      document.getElementById('gcm-pop-hdr').textContent = 'Cost Dashboard — Loading…';
      document.getElementById('gcm-pop-loading').style.display = 'block';
      document.getElementById('gcm-pop-row1').style.display = 'none';
      document.getElementById('gcm-pop-row2').style.display = 'none';
      pop.classList.add('gcm-visible');
      _positionCostPopup(anchorEl);

      if (_gcpXhr) _gcpXhr.abort();
      _gcpXhr = $.ajax({
        type: 'POST', url: '../projectsmain/costdashboardactivity',
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

    $(document).on('mouseenter', '#gantt-container .gcol-cost span[data-actid]', function() {
      var actId = $(this).data('actid');
      if (!actId) return;
      clearTimeout(_gcpTimer);
      var self = this;
      _gcpTimer = setTimeout(function() {
        _showCostPopup(actId, self);
      }, 200);
    });

    $(document).on('mouseleave', '#gantt-container .gcol-cost span[data-actid]', function() {
      _hideCostPopup();
    });

    // ── KPI icon click → open KPI modal (full detail view) ───────────────────
    var _gkmCache = {};
    $(document).on('click', '#gantt-container .gcol-kpi span[data-kpiactid]', function() {
      var actId = $(this).data('kpiactid');
      if (!actId) return;
      _hidePopup(); // close hover popup first
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
        type: 'POST', url: '../projectsmain/performancedashboardkpi',
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
    $('#gkm-close, #gkm-bk').on('click', function(e) {
      if (e.target !== this) return;
      $('#gkm-bk, #gkm-modal').removeClass('gkm-open');
    });
  })();

  // ---- Manage Relations panel -----------------------------------------------

  $('#btn-manage-relations').on('click', function() {
    var panel = $('#relations-panel');
    if (panel.is(':visible')) { panel.slideUp(200); return; }
    $('#relations-content').html('<em>Loading&hellip;</em>');
    panel.slideDown(200);
    $.ajax({
      type: 'POST',
      url: '../projectsmain/activityrelation',
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
  $('#btn-quick-entry').on('click', function() {
    if (typeof openQeModal === 'function') {
      openQeModal();
    } else {
      $('#qe-bk').addClass('qe-open');
      $('#qe-modal').addClass('qe-open');
    }
  });

  // ---- Click on any activity bar opens Quick Entry (temporary) -------------
  $(document).on('click', '#gantt-container .gtaskblue, #gantt-container .gtaskpink', function() {
    $('#qe-bk').addClass('qe-open');
    $('#qe-modal').addClass('qe-open');
    if (typeof window.openQeModal === 'function') window.openQeModal();
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
      type: 'POST', url: '../projectsmain/getscheduleactivityone', dataType: 'json',
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
      type: 'POST', url: '../projectsmain/getscheduleactivitytwo', dataType: 'json',
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
      type: 'POST', url: '../projectsmain/saverelation', dataType: 'json',
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
      type: 'POST', url: '../projectsmain/updaterelation', dataType: 'json',
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
      type: 'POST', url: '../projectsmain/deleterelation', dataType: 'json',
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
    }

    function _renderKpi(k) {
      document.getElementById('gkp-hdr').textContent = k.activity_name || 'KPI';
      _renderKpuCapacity(k);
      _renderKpuCycleTime(k);
      _renderKpuProductivity(k);
      _renderKpuTargetProd(k);
      _renderKpuDuration(k);
    }

    function _positionPopup(mouseX, mouseY) {
      var pop = document.getElementById('gkp-popup');
      var pw = pop.offsetWidth || 560, ph = pop.offsetHeight || 300;
      var vw = window.innerWidth, vh = window.innerHeight;
      // Always anchor to the Gantt (right) side — prefer right of cursor
      var left = mouseX + 18;
      if (left + pw > vw - 8) left = vw - pw - 8;
      if (left < 4) left = 4;
      var top = mouseY - 20;
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
      var top = rect ? rect.top - 20 : vh / 2 - ph / 2;
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
    }

    // Extract raw actid from the bar's hashed task ID via _actCells lookup
    function _getActIdFromBarDiv(barDiv) {
      var idAttr = barDiv.id || '';   // e.g. "gantt-containerbardiv_<hashed_tid>"
      var pfx = 'gantt-containerbardiv_';
      if (idAttr.indexOf(pfx) !== 0) return null;
      var tid = idAttr.slice(pfx.length);
      var db  = _actCells[tid];
      return (db && db.rawId) ? db.rawId : null;
    }

    // Generic KPI fetch + show function (used by bar hover and KPI icon hover)
    function _showKpiForAct(actId, positionFn) {
      if (_kpCache[actId]) {
        _renderKpi(_kpCache[actId]);
        var pop = document.getElementById('gkp-popup');
        pop.classList.add('gkp-visible');
        positionFn();
        return;
      }
      if (_kpXhr) _kpXhr.abort();
      document.getElementById('gkp-hdr').textContent = 'Loading KPI…';
      ['gkp-g5','gkp-g4','gkp-g3','gkp-tp','gkp-dur'].forEach(function(id){
        var el=document.getElementById(id); if(el) el.innerHTML='';
      });
      var pop = document.getElementById('gkp-popup');
      pop.classList.add('gkp-visible');
      positionFn();
      _kpXhr = $.ajax({
        type: 'POST', url: '../projectsmain/performancedashboardkpi',
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

    // Bar hover — KPI popup
    $(document).on('mouseover', '#gantt-container .gtaskbarcontainer:not(.gplan)', function(e) {
      var actId = _getActIdFromBarDiv(this);
      if (!actId) return;
      clearTimeout(_kpTimer);
      var mx = e.clientX, my = e.clientY;
      _kpTimer = setTimeout(function() {
        _showKpiForAct(actId, function(){ _positionPopup(mx, my); });
      }, 300);
    });

    $(document).on('mouseleave', '#gantt-container .gtaskbarcontainer:not(.gplan)', function() {
      _hidePopup();
    });

    // KPI column icon hover — same popup
    $(document).on('mouseenter', '#gantt-container .gcol-kpi span[data-kpiactid]', function() {
      var actId = $(this).data('kpiactid');
      if (!actId) return;
      clearTimeout(_kpTimer);
      var self = this;
      _kpTimer = setTimeout(function() {
        _showKpiForAct(actId, function(){ _positionPopupRight(self); });
      }, 200);
    });

    $(document).on('mouseleave', '#gantt-container .gcol-kpi span[data-kpiactid]', function() {
      _hidePopup();
    });

    // Also hide if mouse leaves the whole gantt container
    $(document).on('mouseleave', '#gantt-container', function() {
      _hidePopup();
    });
  })();

  function reloadRelationsPanel(notice, warningText) {
    $.ajax({
      type: 'POST', url: '../projectsmain/activityrelation', dataType: 'json',
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
</script>
