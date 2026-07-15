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
                    aend:   aEndComputed || act.actual_end_date
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

    // ── Activity name tooltip ─────────────────────────────────────────────────
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
        var _row2 = document.getElementById('gantt-container' + 'child_' + _tid2);
        if (!_row2) continue;
        var _nameCell = _row2.querySelector('td.gtaskname');
        if (!_nameCell) continue;

        (function(db) {
          _nameCell.style.cursor = 'default';
          _nameCell.addEventListener('mouseenter', function(e) {
            _tip.innerHTML =
              '<b>Planned Start:</b> '  + _fmt(db.start)  + '<br>' +
              '<b>Actual Start:</b> '   + _fmt(db.astart) + '<br>' +
              '<b>Planned End:</b> '    + _fmt(db.end)    + '<br>' +
              '<b>Actual End:</b> '     + _fmt(db.aend);
            _tip.style.display = 'block';
            _tip.style.left = (e.clientX + 14) + 'px';
            _tip.style.top  = (e.clientY + 14) + 'px';
          });
          _nameCell.addEventListener('mousemove', function(e) {
            _tip.style.left = (e.clientX + 14) + 'px';
            _tip.style.top  = (e.clientY + 14) + 'px';
          });
          _nameCell.addEventListener('mouseleave', function() {
            _tip.style.display = 'none';
          });
        })(_db2);
      }
    })();

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
