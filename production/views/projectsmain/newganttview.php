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

/* ── Gantt Cost Modal ──────────────────────────────────────────────────────── */
#gcm-bk {
  display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 10000;
}
#gcm-bk.gcm-open { display: block; }
#gcm-modal {
  display: none; position: fixed; z-index: 10001;
  top: 50%; left: 50%; transform: translate(-50%,-50%);
  width: 92vw; max-width: 1100px; height: 86vh;
  background: #f0f3fa; border-radius: 10px;
  box-shadow: 0 8px 40px rgba(0,0,0,0.28);
  flex-direction: column; overflow: hidden;
}
#gcm-modal.gcm-open { display: flex; }
#gcm-header {
  background: #1a2540; color: #fff; padding: 10px 16px;
  display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
}
#gcm-title { font-size: 13px; font-weight: 600; font-family: 'Barlow Condensed', sans-serif; }
#gcm-close {
  background: none; border: none; color: #fff; font-size: 18px; cursor: pointer; padding: 0 4px; line-height: 1;
}
#gcm-loading {
  text-align: center; padding: 40px; font-size: 13px; color: #5a6e8c;
}
#gcm-body {
  flex: 1; min-height: 0; display: flex; flex-direction: column; padding: 10px; gap: 8px; overflow: hidden;
}
/* Top row: 3 panels side by side */
#gcm-row1 {
  display: flex; gap: 8px; flex: 1; min-height: 0;
}
/* Bottom row: 3 panels side by side */
#gcm-row2 {
  display: flex; gap: 8px; flex: 1; min-height: 0;
}
.gcm-panel {
  flex: 1; background: #fff; border-radius: 6px; border: 1px solid #dde3ef;
  display: flex; flex-direction: column; overflow: hidden; min-width: 0;
}
.gcm-panel-title {
  font-size: 10px; font-weight: 700; color: #1a2540; text-transform: uppercase;
  letter-spacing: 0.04em; padding: 5px 8px; border-bottom: 1px solid #e8efff; flex-shrink: 0;
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

<!-- Gantt Cost Modal -->
<div id="gcm-bk"></div>
<div id="gcm-modal">
  <div id="gcm-header">
    <span id="gcm-title">Cost Dashboard</span>
    <button id="gcm-close" title="Close">&times;</button>
  </div>
  <div id="gcm-body">
    <div id="gcm-loading">Loading&hellip;</div>
    <div id="gcm-row1" style="display:none">
      <div class="gcm-panel">
        <div class="gcm-panel-title">Unit Cost of Activity</div>
        <div class="gcm-panel-body" id="gm-cd-g5" style="align-items:center;justify-content:center"></div>
      </div>
      <div class="gcm-panel">
        <div class="gcm-panel-title">Work Done</div>
        <div class="gcm-panel-body" id="gm-cd-g4" style="align-items:center;justify-content:center"></div>
      </div>
      <div class="gcm-panel">
        <div class="gcm-panel-title">Cost of Activity</div>
        <div class="gcm-panel-body" id="gm-cd-g2"></div>
      </div>
    </div>
    <div id="gcm-row2" style="display:none">
      <div class="gcm-panel">
        <div class="gcm-panel-title">Unit Cost of Resources</div>
        <div class="gcm-panel-body" id="gm-cd-c6"></div>
      </div>
      <div class="gcm-panel">
        <div class="gcm-panel-title">Consumption of Resources</div>
        <div class="gcm-panel-body" id="gm-cd-c7"></div>
      </div>
      <div class="gcm-panel">
        <div class="gcm-panel-title">Cost of Resources</div>
        <div class="gcm-panel-body" id="gm-cd-rcost"></div>
      </div>
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
    g.setUseToolTip(1);
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

    // Inject Cost header after A.Duration (gres)
    if (_resHdr && !document.getElementById('gcost-hdr-cell')) {
      _resHdr.parentNode.insertBefore(_makeHdr('gcost-hdr-cell', 'Cost', 50), _resHdr.nextSibling);
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

      // Inject Cost cell after A.Duration (gres) — all rows
      var _resTd = _row.querySelector('td.gres');
      if (_resTd && !_row.querySelector('td.gcol-cost')) {
        var _costTd = document.createElement('td');
        _costTd.className = 'gcol-cost';
        _costTd.style.cssText = 'width:50px;min-width:50px;text-align:center;padding:0;vertical-align:middle;';
        var _costIcon = document.createElement('span');
        _costIcon.style.cssText = 'display:inline-block;width:8px;height:8px;background:#546e7a;border-radius:2px;cursor:pointer;';
        _costIcon.title = 'Cost';
        // Store raw activity DB id so click handler can POST it
        if (_db) _costIcon.setAttribute('data-actid', _db.rawId);
        _costTd.appendChild(_costIcon);
        _resTd.parentNode.insertBefore(_costTd, _resTd.nextSibling);
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

  // ── Gantt Cost Modal — wired once at page level (not per-draw) ─────────────
  (function() {
    if (document.getElementById('gcm-modal').__gcmWired) return;
    document.getElementById('gcm-modal').__gcmWired = true;

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

      var barCols = '', estValRow = '', actValRow = '', lblRow = '', legendRows = '';
      items.forEach(function(r, idx) {
        var est = +r.rate || 0;
        var hasActual = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
        var act = hasActual ? +r.actual_unit_cost : est;
        var unit = r.unit ? ' /' + _shu(r.unit) : '';
        var col = palette[idx % palette.length];
        var diff = act - est;
        var actCol = diff > 0 ? '#e8820c' : (diff < 0 ? '#1b9e8e' : col);
        var estH = (est / maxVal * 100).toFixed(1);
        var actH = (act / maxVal * 100).toFixed(1);

        // Two vertical bars side by side per resource (Est + Act)
        barCols +=
          '<div style="flex:1;display:flex;gap:2px;justify-content:center;align-items:flex-end;height:100%;padding:0 4px">'
          + '<div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;height:100%">'
          +   '<div style="width:100%;height:' + estH + '%;background:' + col + ';border-radius:2px 2px 0 0;opacity:0.55"></div>'
          + '</div>'
          + '<div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;height:100%">'
          +   '<div style="width:100%;height:' + actH + '%;background:' + actCol + ';border-radius:2px 2px 0 0"></div>'
          + '</div>'
          + '</div>';

        // Values above bars
        estValRow += '<div style="flex:1;text-align:center;font-size:8px;color:#4a5568;font-weight:700;padding-bottom:1px">' + _fmtCost(est) + '</div>';
        actValRow += '<div style="flex:1;text-align:center;font-size:8px;color:' + actCol + ';font-weight:700;padding-bottom:1px">' + _fmtCost(act) + '</div>';

        // Index number below bars
        lblRow += '<div style="flex:1;text-align:center;font-size:9px;color:#1a2540;font-weight:700;padding-top:2px">' + (idx + 1) + '</div>';

        // Legend row: number + colour swatch + name + Est/Act values
        legendRows +=
          '<div style="display:flex;align-items:baseline;gap:5px;padding:2px 6px;border-bottom:1px solid #f0f3fa;font-size:10px">'
          + '<span style="font-weight:700;color:#1a2540;flex-shrink:0;min-width:12px">' + (idx + 1) + '.</span>'
          + '<span style="display:inline-block;width:8px;height:8px;border-radius:2px;background:' + col + ';flex-shrink:0;margin-bottom:1px"></span>'
          + '<span style="flex:1;color:#1a2540;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + (r.name||'') + '">' + (r.name||'') + '</span>'
          + '<span style="flex-shrink:0;color:#4a5568;margin-left:6px">Est&nbsp;<b style="color:#000">' + _fmtCost(est) + unit + '</b></span>'
          + '<span style="flex-shrink:0;color:#4a5568;margin-left:6px">Act&nbsp;<b style="color:' + actCol + '">' + _fmtCost(act) + unit + '</b></span>'
          + '</div>';
      });

      // Chart key
      var key = '<div style="display:flex;gap:12px;padding:3px 6px;flex-shrink:0">'
        + '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#4a5568;border-radius:1px;margin-right:3px;vertical-align:middle;opacity:0.55"></span>Estimated</span>'
        + '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#4a5568;border-radius:1px;margin-right:3px;vertical-align:middle"></span>Actual</span>'
        + '</div>';

      el.innerHTML =
        '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0">' + _sh(actName||'', 40) + '</div>'
        + '<div style="display:flex;flex-direction:column;flex:1;min-height:0;overflow:hidden">'
        +   '<div style="display:flex;gap:2px;padding:2px 6px 0;flex-shrink:0">' + estValRow + '</div>'
        +   '<div style="display:flex;gap:2px;padding:0 6px;flex-shrink:0">' + actValRow + '</div>'
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
        var act = +r.actual_consumption  || 0;
        if (Math.max(est, act) > maxVal) maxVal = Math.max(est, act);
      });
      if (maxVal === 0) maxVal = 1;
      var rows = '';
      items.forEach(function(r) {
        var est = +r.planned_consumption || 0;
        var typeId = +r.type_id;
        var isMaterial = [2, 6, 7, 8].indexOf(typeId) >= 0;
        var isSC = typeId === 4;
        var hasActual = (isMaterial && r.indent_raised) || (isSC && r.has_mb);
        var act = hasActual ? (+r.actual_consumption || 0) : est;
        var unit = r.unit ? _shu(r.unit) : (r.task_unit ? _shu(r.task_unit) : '');
        var diff = act - est;
        var actCol = diff > 0 ? '#e8820c' : (diff < 0 ? '#1b9e8e' : '#4a5568');
        var estPct = (est / maxVal * 100).toFixed(1);
        var barHtml;
        if (diff > 0) {
          var diffPct = (diff / maxVal * 100).toFixed(1);
          barHtml = '<div style="display:flex;height:10px;border-radius:3px;overflow:hidden;width:100%"><div style="width:' + estPct + '%;background:#4a5568;flex-shrink:0"></div><div style="width:' + diffPct + '%;background:#e8820c;flex-shrink:0"></div></div>';
        } else if (diff < 0) {
          var actPct2 = (act / maxVal * 100).toFixed(1);
          var gapPct2 = (Math.abs(diff) / maxVal * 100).toFixed(1);
          barHtml = '<div style="display:flex;height:10px;border-radius:3px;overflow:hidden;width:100%"><div style="width:' + actPct2 + '%;background:#4a5568;flex-shrink:0"></div><div style="width:' + gapPct2 + '%;background:#1b9e8e;flex-shrink:0"></div></div>';
        } else {
          barHtml = '<div style="display:flex;height:10px;border-radius:3px;overflow:hidden;width:100%"><div style="width:' + estPct + '%;background:#4a5568;flex-shrink:0"></div></div>';
        }
        rows += '<div style="padding:3px 6px;border-bottom:1px solid #f0f3fa">'
          + '<div style="display:grid;grid-template-columns:1fr 20px 56px 20px 56px;align-items:baseline;margin-bottom:2px">'
          +   '<div style="font-size:11px;font-weight:600;color:#1a2540;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0" title="' + (r.name||'') + '">' + (r.name||'') + '</div>'
          +   '<span style="font-size:9px;color:#888;text-align:right;padding-right:2px">Est</span>'
          +   '<span style="font-size:11px;color:#000;font-weight:700;text-align:right">' + _fm(est) + '<span style="font-size:9px;color:#888;font-weight:400"> ' + unit + '</span></span>'
          +   '<span style="font-size:9px;color:#888;text-align:right;padding-right:2px">Act</span>'
          +   '<span style="font-size:11px;color:' + actCol + ';font-weight:700;text-align:right">' + _fm(act) + '<span style="font-size:9px;color:#888;font-weight:400"> ' + unit + '</span></span>'
          + '</div>' + barHtml + '</div>';
      });
      el.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0">' + _sh(actName||'', 40) + '</div>'
        + '<div style="overflow-y:auto;flex:1;min-height:0">' + rows + '</div>';
    }

    // 3. Cost of Resources → gm-cd-rcost
    function _renderResourceCost(items, actName) {
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
      var estRow = '', actRow = '', bars = '', lblRow = '';
      labels.forEach(function(k) {
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
        lblRow += '<div style="flex:1;text-align:center;font-size:9px;color:#1a2540;font-weight:600;padding-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + k + '">' + _sh(k, 10) + '</div>';
      });
      el.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0">' + _sh(actName||'', 40) + '</div>'
        + '<div style="display:flex;gap:2px;padding:2px 6px 0;flex-shrink:0">' + estRow + '</div>'
        + '<div style="display:flex;gap:2px;padding:0 6px;flex-shrink:0">' + actRow + '</div>'
        + '<div style="display:flex;gap:2px;flex:1;min-height:0;padding:0 6px;align-items:flex-end;border-bottom:1px solid #c8d0e0">' + bars + '</div>'
        + '<div style="display:flex;gap:2px;padding:2px 6px;flex-shrink:0">' + lblRow + '</div>'
        + '<div style="display:flex;gap:12px;padding:4px 6px;flex-shrink:0;flex-wrap:wrap">'
        +   '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#4a5568;border-radius:1px;margin-right:3px;vertical-align:middle"></span>Estimated</span>'
        +   '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#e8820c;border-radius:1px;margin-right:3px;vertical-align:middle"></span>Actual (over)</span>'
        +   '<span style="font-size:9px;color:#666"><span style="display:inline-block;width:10px;height:8px;background:#1b9e8e;border-radius:1px;margin-right:3px;vertical-align:middle"></span>Actual (under)</span>'
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
      _gauge('gm-cd-g5', actTotal, maxVal, 'cost', 0.5, 'Est', _fmtCost(estTotal) + unitLbl, 'Act', _fmtCost(actTotal) + unitLbl);
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
        +'<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Done <tspan font-weight="700">'+_fm(lastQty)+(unit?' '+unit:'')+'</tspan></text>'
        +'<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Sched <tspan font-weight="700">'+_fm(schedQty)+(unit?' '+unit:'')+'</tspan></text>'
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
      var diffLabel = hasActual && diff !== 0 ? (over?'+':'-') + _fmtCost(Math.abs(diff)) + ' ' + (over?'over':'saving') : '';
      var diffCol = over ? '#e8820c' : '#1b9e8e';
      var workDone = +d.last_report_qty || 0;
      var estCostWD = estUCTotal * workDone;
      var actCostWD = actUCTotal * workDone;
      var diffWD = actCostWD - estCostWD;
      var overWD = diffWD > 0;
      var diffWDLabel = diffWD !== 0 ? (overWD?'+':'-') + _fmtCost(Math.abs(diffWD)) + ' ' + (overWD?'over':'saving') : '';
      var valRow = '<div style="display:flex;justify-content:space-between;align-items:baseline;font-family:\'Barlow Condensed\',sans-serif;font-size:15px;font-weight:700;color:#111;margin-bottom:6px">'
        +'<span>Est: ' + _fmtCost(estCost) + (hasActual ? '&nbsp;&nbsp;&nbsp;Act: <span style="color:'+(over?'#e8820c':'#1b9e8e')+'">' + _fmtCost(actCost) + '</span>' : '') + '</span>'
        +(diffLabel ? '<span style="color:'+diffCol+'">'+diffLabel+'</span>' : '')
        +'</div>';
      var wdRow = '<div style="font-family:\'Barlow Condensed\',sans-serif;font-size:13px;font-weight:600;color:#444;margin-top:8px;line-height:1.7">'
        +'<div>Estimated Cost of Work Done &nbsp;<span style="color:#111">' + _fmtCost(estCostWD) + '</span></div>'
        +'<div>Actual Cost of Work Done &nbsp;<span style="color:'+(overWD?'#e8820c':'#1b9e8e')+'">' + _fmtCost(actCostWD) + '</span></div>'
        +'<div style="color:'+(overWD?'#e8820c':'#1b9e8e')+'">Difference &nbsp;'+(diffWDLabel||_fmtCost(0))+'</div>'
        +'</div>';
      el.style.overflow = 'auto';
      el.innerHTML = '<div style="padding:4px 6px 8px;width:100%;box-sizing:border-box">'
        +'<div style="font-size:10px;color:#3461b8;font-weight:600;padding-bottom:4px;border-bottom:1px solid #e8efff;margin-bottom:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">'+_sh(actName,40)+'</div>'
        + valRow + barHtml + wdRow
        +'</div>';
    }

    // ── Open modal on Cost icon click ─────────────────────────────────────────
    $(document).on('click', '#gantt-container .gcol-cost span[data-actid]', function() {
      var actId = $(this).data('actid');
      if (!actId) return;
      $('#gcm-loading').show();
      $('#gcm-row1, #gcm-row2').hide();
      $('#gcm-title').text('Cost Dashboard — Loading…');
      $('#gcm-bk, #gcm-modal').addClass('gcm-open');

      $.ajax({
        type: 'POST',
        url: '../projectsmain/costdashboardactivity',
        data: { actid: actId },
        dataType: 'json',
        success: function(d) {
          $('#gcm-loading').hide();
          if (!d || d.error) {
            $('#gcm-loading').text(d && d.error ? d.error : 'Error loading data').show();
            return;
          }
          $('#gcm-title').text('Cost Dashboard — ' + (d.activity_name || ''));
          // Clear previous content
          ['gm-cd-c6','gm-cd-c7','gm-cd-rcost','gm-cd-g5','gm-cd-g4','gm-cd-g2'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.innerHTML = '';
          });
          _renderUnitCostOfResource(d.items, d.activity_name);
          _renderResourceConsumption(d.items, d.activity_name, d.last_report_qty, d.unit);
          _renderResourceCost(d.items, d.activity_name);
          _renderUnitCostOfActivity(d.items, d.activity_name, d.unit, d.schedule_qty);
          _renderValueOfWorkDone(d);
          _renderCostOfActivity(d);
          $('#gcm-row1, #gcm-row2').show();
        },
        error: function() {
          $('#gcm-loading').text('Failed to load cost data.').show();
        }
      });
    });

    // ── Close modal ────────────────────────────────────────────────────────────
    $('#gcm-close, #gcm-bk').on('click', function(e) {
      if (e.target !== this) return;
      $('#gcm-bk, #gcm-modal').removeClass('gcm-open');
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
