/* Performance Dashboard — popup modal */
(function(){
'use strict';

var _ch = {};
var _loaded = false;
var _groups    = [];   // iow_groups rows
var _iow_items = [];   // wbsscheduleitems rows with group_id
var _all       = [];   // all scheduleactivities
var _actExactCost = {}; // cache: actId => {est, acoa, ewd, awd} from exact per-resource data

// ── Date formatter ────────────────────────────────────────────────────────────
var _months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
function fmtDate(d){
    if (!d || d === '0000-00-00') return '—';
    var p = d.split('-');
    return p[2] + ' ' + (_months[parseInt(p[1],10)-1]||'') + ' ' + p[0];
}

// ── Floating tooltip ──────────────────────────────────────────────────────────
$(document).on('mouseenter', '#pd-modal [data-tip]', function(e){
    var $t = $('#pd-tip');
    if (!$t.length) $t = $('<div id="pd-tip"></div>').appendTo('body');
    $t.text($(this).attr('data-tip')).css({display:'block',left:e.clientX+14,top:e.clientY+14});
}).on('mousemove', '#pd-modal [data-tip]', function(e){
    var $t = $('#pd-tip');
    var x = e.clientX + 14, y = e.clientY + 14;
    if (x + $t.outerWidth() + 10 > window.innerWidth) x = e.clientX - $t.outerWidth() - 6;
    $t.css({left:x, top:y});
}).on('mouseleave', '#pd-modal [data-tip]', function(){
    $('#pd-tip').hide();
});

// ── Open / Close ──────────────────────────────────────────────────────────────
$(document).on('click', '.perf-dashboard-btn', function(e){
    e.preventDefault();
    $('#pd-modal, #pd-bk').addClass('pd-open');
    if (!_loaded) { loadAll(); _loaded = true; }
});
$(document).on('click', '#pd-close, #pd-bk', function(){
    $('#pd-modal, #pd-bk').removeClass('pd-open');
});

// ── Cost Dashboard ────────────────────────────────────────────────────────────
var _cdLoaded = false;
var _cdProjectName = '';
var _cdTotalCost = 0;

// Resource type colour map (name → bar colour)
var _resTypeColours = {
    'materials':         '#3E4A5C',   // Dark greyed navy
    'purchased inputs':  '#9E9E9E',   // Grey
    'consumables':       '#80CBC4',   // Grey Green (teal)
    'tools and tackles': '#607D8B',   // Blue Grey
    'sub contractors':   '#455A64',   // Slate
    'sub contractor':    '#455A64'    // Slate (singular)
};
function _resTypeCol(name, fallback){
    var k = (name || '').toLowerCase().trim();
    return _resTypeColours[k] || fallback || '#78909C';
}

$(document).on('click', '.cost-dashboard-btn', function(e){
    e.preventDefault();
    $('#cd-modal, #cd-bk').addClass('cd-open');
    if (!_cdLoaded) {
        _cdLoaded = true;
        if (_loaded) {
            renderCdBars();
        } else {
            $.ajax({
                type:'POST', url:'../projectsmain/performancedashboard', dataType:'json',
                success: function(d){
                    if (!d || d.error === undefined) return;
                    if (!_groups.length)    _groups    = d.iow_groups  || [];
                    if (!_iow_items.length) _iow_items = d.iow_items   || [];
                    if (!_all.length)       _all       = d.activities  || [];
                    if (!_cdProjectName)    _cdProjectName = d.project_name || '';
                    renderCdBars();
                }
            });
        }
    }
});
$(document).on('click', '#cd-close, #cd-bk', function(){
    $('#cd-modal, #cd-bk').removeClass('cd-open');
});

function renderCdBars(){
    // IOW costs = sum of activity costs under each IOW
    var iowCostMap = {}, iowAcoaMap = {}, iowWorkDoneMap = {}, iowAwdMap = {};
    _iow_items.forEach(function(iow){
        var sid  = String(iow.id);
        var acts = _all.filter(function(a){ return String(a.scheduleitem_id) === sid; });
        iowCostMap[sid]     = acts.reduce(function(s, a){ return s + (+a.activity_cost || 0); }, 0);
        iowWorkDoneMap[sid] = acts.reduce(function(s, a){
            var sc = +a.quantity || 1;
            return s + ((+a.activity_cost || 0) * (+a.cumulated_qty || 0) / sc);
        }, 0);
        iowAwdMap[sid]  = acts.reduce(function(s, a){ return s + (+a.actual_work_done || +a.actual_cost || 0); }, 0);
        iowAcoaMap[sid] = acts.reduce(function(s, a){
            var cq = +a.cumulated_qty || 0, sq = +a.quantity || 1;
            var awd = +a.actual_work_done || +a.actual_cost || 0;
            return s + (cq > 0 ? awd * sq / cq : 0);
        }, 0);
    });

    // Group costs = sum of IOW costs under each group
    var groupItems = _groups.map(function(g){
        var iows = _iow_items.filter(function(i){ return String(i.group_id) === String(g.id); });
        return {
            name:          g.name,
            cost:          iows.reduce(function(s, i){ return s + (iowCostMap[String(i.id)]     || 0); }, 0),
            acoa:          iows.reduce(function(s, i){ return s + (iowAcoaMap[String(i.id)]     || 0); }, 0),
            est_work_done: iows.reduce(function(s, i){ return s + (iowWorkDoneMap[String(i.id)] || 0); }, 0),
            awd:           iows.reduce(function(s, i){ return s + (iowAwdMap[String(i.id)]      || 0); }, 0),
            id:            g.id
        };
    });

    // Project Cost = sum of group costs
    var totalCost     = groupItems.reduce(function(s, g){ return s + g.cost; }, 0);
    var totalAcoa     = groupItems.reduce(function(s, g){ return s + g.acoa; }, 0);
    var totalWorkDone = groupItems.reduce(function(s, g){ return s + g.est_work_done; }, 0);
    var totalAwd      = groupItems.reduce(function(s, g){ return s + g.awd; }, 0);
    var totalGrnOnly  = _all.reduce(function(s, a){ return s + (+a.actual_cost || 0); }, 0);

    renderCostBars('cd-c2', [{name: _cdProjectName || 'Project', cost: totalCost, acoa: totalAcoa, est_work_done: totalWorkDone, awd: totalAwd, id: 0}], null);
    var c2el = document.getElementById('cd-c2');
    if (c2el) {
        c2el.style.display        = 'flex';
        c2el.style.flexDirection  = 'column';
        c2el.style.justifyContent = 'flex-start';
        c2el.style.paddingTop     = '14px';

        var brow = c2el.querySelector('.brow');
        if (brow) brow.style.marginTop = '0';

        // Append 4 legend rows
        function inrFmt(v){
            v = Math.round(+v); if (!v) return '0';
            var s = v.toString(), r = s.slice(-3), rem = s.slice(0, -3);
            while (rem.length > 2){ r = rem.slice(-2) + ',' + r; rem = rem.slice(0, -2); }
            return (rem.length ? rem + ',' : '') + r;
        }
        function legendHtml(col, label, val){
            return '<div style="display:flex;align-items:baseline;padding:2px 3px;gap:4px;">'
                + '<span style="display:inline-block;width:9px;height:9px;border-radius:2px;background:'+col+';flex-shrink:0;margin-bottom:1px;"></span>'
                + '<span style="font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#445;flex:1;white-space:nowrap;">'+label+'</span>'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:10px;font-weight:700;color:#1a2540;white-space:nowrap;">&#8377;'+inrFmt(val)+'</span>'
                + '</div>';
        }
        var leg = document.createElement('div');
        leg.style.cssText = 'margin-top:12px;padding-left:70px;display:flex;gap:6px;';
        var leftCol  = '<div style="flex:1;min-width:0;">'
                     + legendHtml('#4A5568', 'Estimated Cost of Project',    totalCost)
                     + legendHtml('#40C4FF', 'Actual Cost of Project',       totalAcoa)
                     + '</div>';
        var rightCol = '<div style="flex:1;min-width:0;border-left:1px solid #e0e4ec;padding-left:4px;">'
                     + legendHtml('#0D47A1', 'Estimated Cost of Work Done',  totalWorkDone)
                     + legendHtml('#455A64', 'Actual Cost of Work Done',     totalAwd)
                     + '</div>';
        leg.innerHTML = leftCol + rightCol;
        c2el.appendChild(leg);
    }
    renderSimpleCostBars('cd-c1', groupItems, filterByGroupCd);
    if (_groups.length) filterByGroupCd(_groups[0].id);
}

function filterByGroupCd(groupId){
    var gid = String(groupId);
    var filtered = _iow_items.filter(function(i){ return String(i.group_id) === gid; });
    if (!filtered.length) filtered = _iow_items;

    // IOW Cost — sum activity costs per IOW
    var iowItems = filtered.map(function(iow){
        var sid  = String(iow.id);
        var acts = _all.filter(function(a){ return String(a.scheduleitem_id) === sid; });
        var cost     = acts.reduce(function(s, a){ return s + (+a.activity_cost || 0); }, 0);
        var workDone = acts.reduce(function(s, a){
            var sc = +a.quantity || 1;
            return s + ((+a.activity_cost || 0) * (+a.cumulated_qty || 0) / sc);
        }, 0);
        var awd  = acts.reduce(function(s, a){ return s + (+a.actual_work_done || +a.actual_cost || 0); }, 0);
        var acoa = acts.reduce(function(s, a){
            var cq = +a.cumulated_qty || 0, sq = +a.quantity || 1;
            return s + (cq > 0 ? (+a.actual_work_done || +a.actual_cost || 0) * sq / cq : 0);
        }, 0);
        return {name: iow.name, cost: cost, acoa: acoa, est_work_done: workDone, awd: awd, id: iow.id};
    });
    renderSimpleCostBars('cd-c3', iowItems, filterByIowCd, true, function(iow){
        return _all.filter(function(a){ return String(a.scheduleitem_id) === String(iow.id); });
    });

    $('#cd-c1 .brow').removeClass('brow-active');
    $('#cd-c1 .brow[data-aid="' + groupId + '"]').addClass('brow-active');
    var firstId = filtered.length ? filtered[0].id : null;
    if (firstId) filterByIowCd(firstId);
}

function filterByIowCd(iowId){
    var sid = String(iowId);
    $('#cd-c3 .brow').removeClass('brow-active');
    $('#cd-c3 .brow[data-aid="' + iowId + '"]').addClass('brow-active');
    var filtered  = _all.filter(function(a){ return String(a.scheduleitem_id) === sid; });
    var fOngoing  = filtered.filter(function(a){ return parseInt(a.pr_report_count, 10) > 0; });
    var fUpcoming = filtered.filter(function(a){ return !(parseInt(a.pr_report_count, 10) > 0); });
    fUpcoming.sort(function(a, b){ return (a.start_date || '').localeCompare(b.start_date || ''); });
    var actList = fOngoing.concat(fUpcoming);
    renderActivityCostBars('cd-c4', toCostBarItems(actList), loadCdActivityData);
    var firstAct = fOngoing.length ? fOngoing[0] : (fUpcoming.length ? fUpcoming[0] : null);
    if (firstAct) {
        loadCdActivityData(firstAct.id);
    } else {
        var el = document.getElementById('cd-c6');
        if (el) el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">Select an activity</div>';
    }
    // Update bars only (not panels) for remaining activities
    actList.forEach(function(a, idx){
        if (a.id == (firstAct ? firstAct.id : null)) return;
        setTimeout(function(){ updateActivityBar(a.id); }, 400 + idx * 250);
    });
}

function updateActivityBar(actId){
    $.ajax({
        type:'POST', url:'../projectsmain/costdashboardactivity',
        data:{actid: actId}, dataType:'json',
        success: function(d){
            var items = d.items || [];
            var lastQty = +d.last_report_qty || 0;
            var estActQty = +d.activity_qty || 0;
            var schedQty  = +d.schedule_qty || 0;
            var unitCost = 0;
            items.forEach(function(r){ unitCost += (+r.res_qty||0)*(+r.rate||0); });
            var est  = unitCost * estActQty;
            var ewd  = 0;
            if (lastQty > 0) { items.forEach(function(r){ ewd += (+r.rate||0)*(+r.planned_consumption||0); }); ewd *= lastQty; }
            var awd = 0, hasA = false;
            items.forEach(function(r){ if(r.actual_unit_cost!=null&&r.actual_consumption!=null){ awd+=(+r.actual_unit_cost)*(+r.actual_consumption); hasA=true; } });
            var auCost = (hasA && lastQty > 0) ? awd/lastQty : null;
            var acoa = auCost !== null ? auCost * schedQty : 0;
            _actExactCost[actId] = {est:est, acoa:acoa, ewd:ewd, awd:hasA?awd:0};
            var brow = document.querySelector('#cd-c4 .brow[data-aid="'+actId+'"]');
            if (brow && est > 0) {
                var acoaPct = Math.min(acoa/est*100,100).toFixed(1);
                var ewdPct  = Math.min(ewd /est*100,100).toFixed(1);
                var awdPct  = Math.min(awd /est*100,100).toFixed(1);
                var trk = brow.querySelector('.btrk');
                if (trk) { trk.style.overflow='visible'; trk.innerHTML = '<div class="bs" style="width:100%;background:#555f6e;position:relative;overflow:visible;">'
                    + '<div style="position:absolute;top:0;left:0;height:100%;width:100%;background:#555f6e;border-radius:2px;"></div>'
                    + (acoa>0?'<div style="position:absolute;top:0;left:0;height:100%;width:'+acoaPct+'%;background:#78909C;opacity:0.9;border-radius:2px 0 0 2px;"></div>':'')
                    + (acoa>est?'<div style="position:absolute;top:0;left:100%;height:100%;width:'+((acoa-est)/est*100).toFixed(1)+'%;background:#FF7043;border-radius:0 2px 2px 0;"></div>':'')
                    + (ewd >0?'<div style="position:absolute;top:0;left:0;height:100%;width:'+ewdPct +'%;background:#0D47A1;border-radius:2px 0 0 2px;"></div>':'')
                    + (awd >0?'<div style="position:absolute;top:0;left:0;height:100%;width:'+awdPct +'%;background:#455A64;opacity:0.9;border-radius:2px 0 0 2px;"></div>':'')
                    + (awd>ewd?'<div style="position:absolute;top:0;left:'+ewdPct+'%;height:100%;width:'+((awd-ewd)/est*100).toFixed(1)+'%;background:#ef5350;opacity:0.9;"></div>':'')
                    + '</div>'; }
            }
        }
    });
}

function loadCdActivityData(actId){
    $('#cd-c4 .brow, #cd-c5 .brow').removeClass('brow-active');
    $('#cd-c4 .brow[data-aid="' + actId + '"], #cd-c5 .brow[data-aid="' + actId + '"]').addClass('brow-active');
    var el = document.getElementById('cd-c6');
    if (el) el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">Loading…</div>';
    $.ajax({
        type: 'POST',
        url: '../projectsmain/costdashboardactivity',
        data: {actid: actId},
        dataType: 'json',
        success: function(d){
            renderCdUnitCostOfResource(d.items || [], d.activity_name || '');
            renderCdResourceConsumption(d.items || [], d.activity_name || '', +d.last_report_qty || 0, d.unit || '');
            renderCdResourceCost(d.items || [], d.activity_name || '');
            renderCdUnitCostOfActivity(d.items || [], d.activity_name || '', d.unit || '', +d.last_report_qty || 0);
            renderCdCostOfActivity(d.items || [], d.activity_name || '', +d.last_report_qty || 0, +d.activity_qty || 0, +d.schedule_qty || 0, d.unit || '');
            renderCdCostOnCompletion(d.items || [], d.activity_name || '', +d.activity_qty || 0);
            renderCdValueOfWorkDone(d);

            // Cache exact cost values for this activity and update its bar in cd-c4
            (function(){
                var items   = d.items || [];
                var lastQty = +d.last_report_qty || 0;
                var estActQty = +d.activity_qty  || 0;
                var schedQty  = +d.schedule_qty  || 0;
                var unitCost  = 0;
                items.forEach(function(r){ unitCost += (+r.res_qty || 0) * (+r.rate || 0); });
                var est = unitCost * estActQty;
                var ewd = 0;
                if (lastQty > 0) {
                    items.forEach(function(r){ ewd += (+r.rate || 0) * (+r.planned_consumption || 0); });
                    ewd *= lastQty;
                }
                var awd = 0, hasA = false;
                items.forEach(function(r){
                    if (r.actual_unit_cost != null && r.actual_consumption != null){
                        awd += (+r.actual_unit_cost) * (+r.actual_consumption); hasA = true;
                    }
                });
                var auCost = (hasA && lastQty > 0) ? awd / lastQty : null;
                var acoa   = auCost !== null ? auCost * schedQty : 0;
                _actExactCost[actId] = {est: est, acoa: acoa, ewd: ewd, awd: hasA ? awd : 0};
                // Update bar in cd-c4
                var brow = document.querySelector('#cd-c4 .brow[data-aid="'+actId+'"]');
                if (brow && est > 0) {
                    var acoaPct = Math.min(acoa / est * 100, 100).toFixed(1);
                    var ewdPct  = Math.min(ewd  / est * 100, 100).toFixed(1);
                    var awdPct  = Math.min(awd  / est * 100, 100).toFixed(1);
                    var trk = brow.querySelector('.btrk');
                    if (trk) { trk.style.overflow = 'visible'; trk.innerHTML = '<div class="bs" style="width:100%;background:#555f6e;position:relative;overflow:visible;">'
                        + '<div style="position:absolute;top:0;left:0;height:100%;width:100%;background:#555f6e;border-radius:2px;"></div>'
                        + (acoa > 0 ? '<div style="position:absolute;top:0;left:0;height:100%;width:'+acoaPct+'%;background:#78909C;opacity:0.9;border-radius:2px 0 0 2px;"></div>' : '')
                        + (acoa > est ? '<div style="position:absolute;top:0;left:100%;height:100%;width:'+((acoa-est)/est*100).toFixed(1)+'%;background:#FF7043;border-radius:0 2px 2px 0;"></div>' : '')
                        + (ewd  > 0 ? '<div style="position:absolute;top:0;left:0;height:100%;width:'+ewdPct +'%;background:#0D47A1;border-radius:2px 0 0 2px;"></div>' : '')
                        + (awd  > 0 ? '<div style="position:absolute;top:0;left:0;height:100%;width:'+awdPct +'%;background:#455A64;opacity:0.9;border-radius:2px 0 0 2px;"></div>' : '')
                        + (awd > ewd ? '<div style="position:absolute;top:0;left:'+ewdPct+'%;height:100%;width:'+((awd-ewd)/est*100).toFixed(1)+'%;background:#ef5350;opacity:0.9;"></div>' : '')
                        + '</div>'; }
                }
            })();
        },
        error: function(){
            var el2 = document.getElementById('cd-c6');
            if (el2) el2.innerHTML = '<div style="text-align:center;font-size:12px;color:#e53935;padding:18px 0">Error loading data</div>';
        }
    });
}

function renderCdUnitCostOfResource(items, actName){
    var el = document.getElementById('cd-c6');
    if (!el) return;
    var colPalette = ['#90caf9','#ce93d8','#80cbc4','#ffcc80','#ef9a9a','#a5d6a7','#fff176','#f48fb1','#bcaaa4','#80deea'];

    function fmR(v){ return (+v).toFixed(2); }
    function fmRK(v){ return v>=1000000?(v/1000000).toFixed(1)+'M':v>=1000?(v/1000).toFixed(1)+'K':(+v).toFixed(0); }

    if (!items.length){
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No estimate data</div>';
        return;
    }

    // Build flat resource list with planned (rate) and actual (actual_unit_cost)
    var resources = items.map(function(r){
        var hasAct = r.actual_unit_cost !== null && r.actual_unit_cost !== undefined;
        return {
            name:    r.name || '',
            planned: +r.rate || 0,
            actual:  hasAct ? +r.actual_unit_cost : null,
            unit:    r.unit || '',
            col:     _resTypeCol(r.type_name, colPalette[0])
        };
    });

    // Scale: max of all planned and actual values
    var maxVal = 0;
    resources.forEach(function(r){
        if (r.planned > maxVal) maxVal = r.planned;
        if (r.actual !== null && r.actual > maxVal) maxVal = r.actual;
    });
    if (!maxVal) maxVal = 1;

    // Horizontal bars: name left, planned bar, actual indicator, value right
    var rowsHtml = '';
    resources.forEach(function(r, ri){
        var col     = r.col;
        var planned = r.planned;
        var actual  = r.actual;
        var plW     = (planned / maxVal * 100).toFixed(1);

        var uSfx    = r.unit ? ' / ' + r.unit : '';
        var barHtml = '';
        if (actual === null) {
            // No actual — plain planned bar
            barHtml = '<div style="width:' + plW + '%;height:100%;background:' + col + ';border-radius:0 3px 3px 0;'
                + 'display:flex;align-items:center;padding-left:5px;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:10px;font-weight:700;color:#fff;white-space:nowrap;">&#8377;' + fmRK(planned) + uSfx + '</span>'
                + '</div>';
        } else if (actual > planned) {
            // Over planned — planned + red excess
            var exW = ((actual - planned) / maxVal * 100).toFixed(1);
            barHtml = '<div style="width:' + plW + '%;height:100%;background:' + col + ';display:flex;align-items:center;padding-left:5px;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:10px;font-weight:700;color:#fff;white-space:nowrap;">&#8377;' + fmRK(planned) + uSfx + '</span>'
                + '</div>'
                + '<div style="width:' + exW + '%;height:100%;background:#ef5350;border-radius:0 3px 3px 0;display:flex;align-items:center;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:10px;font-weight:700;color:#fff;white-space:nowrap;padding-left:3px;">+&#8377;' + fmRK(actual-planned) + uSfx + '</span>'
                + '</div>';
        } else {
            // Under planned — actual bar + green saving
            var acW  = (actual / maxVal * 100).toFixed(1);
            var savW = ((planned - actual) / maxVal * 100).toFixed(1);
            barHtml = '<div style="width:' + acW + '%;height:100%;background:' + col + ';display:flex;align-items:center;padding-left:5px;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:10px;font-weight:700;color:#fff;white-space:nowrap;">&#8377;' + fmRK(actual) + uSfx + '</span>'
                + '</div>'
                + '<div style="width:' + savW + '%;height:100%;background:#66bb6a;border-radius:0 3px 3px 0;"></div>';
        }

        rowsHtml += '<div data-res-idx="'+ri+'" style="display:flex;align-items:center;margin-bottom:5px;cursor:pointer;">'
            + '<div style="width:130px;min-width:130px;font-family:\'Barlow Condensed\',sans-serif;font-size:12px;font-weight:700;color:#000;'
            + 'white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding-right:8px;text-align:right;">' + r.name + '</div>'
            + '<div style="flex:1;height:18px;background:#e8edf3;border-radius:3px;overflow:hidden;display:flex;">' + barHtml + '</div>'
            + '</div>';
    });

    el.innerHTML = '<div style="flex:1;min-height:0;display:flex;flex-direction:column;justify-content:center;padding:6px 4px;overflow-y:auto;">'
        + rowsHtml
        + (actName ? '<div class="resfoot" style="margin-top:4px;">' + sh(actName, 32) + '</div>' : '')
        + '</div>';

    var tipEl6 = document.getElementById('uc-res-tip');
    if (!tipEl6){
        tipEl6 = document.createElement('div');
        tipEl6.id = 'uc-res-tip';
        tipEl6.style.cssText = 'position:fixed;z-index:9999;display:none;pointer-events:none;'
            + 'background:#0d1a2e;border-radius:8px;box-shadow:0 8px 28px rgba(0,0,0,0.45);padding:12px 18px;min-width:280px;';
        document.body.appendChild(tipEl6);
    }
    el.querySelectorAll('[data-res-idx]').forEach(function(row){
        row.addEventListener('mouseenter', function(){
            var r    = resources[+row.getAttribute('data-res-idx')];
            var diff = r.actual !== null ? r.actual - r.planned : null;
            var dCol = diff === null ? '' : (diff > 0 ? '#ef9a9a' : '#a5d6a7');
            var dTxt = diff === null ? '' : (diff > 0 ? '+' : '') + '&#8377;' + fmR(Math.abs(diff));
            tipEl6.innerHTML = '<div style="display:flex;align-items:center;gap:7px;margin-bottom:8px;">'
                + '<span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:'+r.col+';flex-shrink:0;"></span>'
                + '<span style="font-family:\'Barlow Condensed\',sans-serif;font-size:13px;color:#fff;font-weight:700;">'+r.name+'</span>'
                + '</div>'
                + '<table style="width:100%;border-collapse:collapse;">'
                + '<tr><td style="padding:3px 14px 3px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#fff;">Planned</td>'
                + '<td style="padding:3px 0;font-family:\'Nunito\',sans-serif;font-size:11px;color:#fff;font-weight:700;text-align:right;white-space:nowrap;">&#8377;'+fmR(r.planned)+'</td></tr>'
                + '<tr><td style="padding:3px 14px 3px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#fff;">Actual</td>'
                + '<td style="padding:3px 0;font-family:\'Nunito\',sans-serif;font-size:11px;font-weight:700;color:'+(r.actual !== null ? '#fff' : '#5a6e8c')+';text-align:right;white-space:nowrap;">'+(r.actual !== null ? '&#8377;'+fmR(r.actual) : '—')+'</td></tr>'
                + (diff !== null ? '<tr><td style="padding:3px 14px 3px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#fff;">Diff</td>'
                + '<td style="padding:3px 0;font-family:\'Nunito\',sans-serif;font-size:11px;font-weight:700;color:'+dCol+';text-align:right;white-space:nowrap;">'+dTxt+'</td></tr>' : '')
                + '</table>';
            var rect = row.getBoundingClientRect();
            var tipW = 280;
            tipEl6.style.width = tipW + 'px';
            var left = rect.left + rect.width / 2 - tipW / 2;
            left = Math.max(4, Math.min(left, window.innerWidth - tipW - 4));
            tipEl6.style.left = left + 'px';
            tipEl6.style.top  = (rect.top - 8) + 'px';
            tipEl6.style.transform = 'translateY(-100%)';
            tipEl6.style.display = 'block';
        });
        row.addEventListener('mouseleave', function(){ tipEl6.style.display = 'none'; });
    });
}

function renderCdValueOfWorkDone(d){
    var el = document.getElementById('cd-g4');
    if (!el) return;
    var tq  = +d.schedule_qty    || 0;
    var aq  = +d.last_report_qty || 0;
    var u   = shu(d.unit);
    var an  = sh(d.activity_name || '', 38);
    var pct = tq > 0 ? Math.round(aq / tq * 100) : 0;
    var f   = tq > 0 ? Math.max(0, Math.min(1, aq / tq)) : 0;
    var cx=105, cy=92, r=76, sw=14;

    function ptF(frac){ var a=Math.PI*(1-frac); return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)]; }
    function arc(f1,f2,col,cap){
        if(f2<=f1) return ''; cap=cap||'butt';
        var p1=ptF(f1),p2=ptF(f2);
        if((f2-f1)>=1){ var pm=ptF(0.5);
            return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
        }
        return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
    }

    var nr=r-15, na=Math.PI*(1-f);
    var nx=(cx+nr*Math.cos(na)).toFixed(1), ny=(cy-nr*Math.sin(na)).toFixed(1);

    var svg='<svg width="100%" height="100%" viewBox="0 0 210 134" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet" style="display:block;width:100%;height:auto;">'
        +arc(0,1,'#90A4AE')
        +(f>0?arc(0,f,'#00838f','butt'):'')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#90A4AE"/>'
        +'<text x="'+(cx-r)+'" y="'+(cy+15)+'" text-anchor="middle" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Start</text>'
        +'<text x="'+(cx+r)+'" y="'+(cy+15)+'" text-anchor="middle" font-size="13" fill="#111" font-family="Barlow Condensed,Arial">Complete '+fm(tq)+(u?' '+u:'')+'</text>'
        +'<text x="'+cx+'" y="'+(cy-20)+'" text-anchor="middle" font-size="18" font-weight="700" fill="#111" font-family="Barlow Condensed,Arial">'+fm(aq)+(u?' '+u:'')+' | '+pct+'%</text>'
        +'<text x="'+cx+'" y="'+(cy-5)+'" text-anchor="middle" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">Achieved</text>'
        +(an?'<text x="'+cx+'" y="128" text-anchor="middle" font-size="13" fill="#111" font-family="Barlow Condensed,Arial">'+an+(u?' / '+u:'')+'</text>':'')
        +'</svg>';
    el.innerHTML = svg;
}

function renderCdUnitCostOfActivity(items, actName, actUnit, lastQty){
    var el = document.getElementById('cd-g5');
    if (!el) return;

    // Planned = SUM(rate × planned_consumption) / lastQty
    // Actual  = SUM(actual_unit_cost × actual_consumption) / lastQty
    var plannedCost = 0, actualCost = 0, hasActual = false;
    items.forEach(function(r){
        plannedCost += (+r.rate || 0) * (+r.planned_consumption || 0);
        if (r.actual_unit_cost != null && r.actual_consumption != null) {
            actualCost  += (+r.actual_unit_cost) * (+r.actual_consumption);
            hasActual    = true;
        }
    });

    if (!plannedCost){
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No estimate data</div>';
        return;
    }

    // When no progress reported, use plannedCost as unit cost (qty treated as 1)
    var plannedUnitCost = (lastQty > 0) ? plannedCost / lastQty : plannedCost;
    // When no actual data or no progress yet, actual = planned
    var actualUnitCost  = (hasActual && lastQty > 0) ? actualCost / lastQty : plannedUnitCost;

    var maxVal = plannedUnitCost * 2;
    var f      = actualUnitCost !== null ? Math.max(0, Math.min(1, actualUnitCost / maxVal)) : 0;
    var an     = sh(actName || '', 38);
    var cx=105, cy=92, r=76, sw=14;

    function ptF(frac){ var a=Math.PI*(1-frac); return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)]; }
    function arc(f1,f2,col,cap){
        if(f2<=f1) return ''; cap=cap||'butt';
        var p1=ptF(f1), p2=ptF(f2);
        if((f2-f1)>=1){ var pm=ptF(0.5);
            return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
        }
        return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
    }

    var nr=r-15, na=Math.PI*(1-f);
    var nx=(cx+nr*Math.cos(na)).toFixed(1), ny=(cy-nr*Math.sin(na)).toFixed(1);

    function fmCost(v){ return v>=1000000?(v/1000000).toFixed(1)+'M':v>=1000?(v/1000).toFixed(1)+'K':v.toFixed(0); }

    var svg='<svg width="100%" height="100%" viewBox="0 0 210 134" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet" style="display:block;width:100%;height:auto;">'
        +arc(0, 0.5, '#00838f')
        +arc(0.5, 1,  '#ef5350')
        +(actualUnitCost !== null ? '<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>' : '')
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#dce3ef"/>'
        +'<text x="'+cx+'" y="'+(cy-10)+'" text-anchor="middle" font-size="15" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+(actualUnitCost !== null ? '&#8377; '+fmCost(actualUnitCost) : '—')+(actUnit?' / '+actUnit:'')+'</text>'
        +'<text x="55" y="114" text-anchor="middle" font-size="11" fill="#111" font-family="Barlow Condensed,Arial">Planned <tspan font-weight="700">&#8377; '+fmCost(plannedUnitCost)+'</tspan></text>'
        +'<text x="155" y="114" text-anchor="middle" font-size="11" fill="#111" font-family="Barlow Condensed,Arial">Actual <tspan font-weight="700">'+(actualUnitCost !== null ? '&#8377; '+fmCost(actualUnitCost) : '—')+'</tspan></text>'
        +(an?'<text x="'+cx+'" y="128" text-anchor="middle" font-size="11" fill="#5a6e8c" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +'</svg>';

    var colPalette = ['#90caf9','#ce93d8','#80cbc4','#ffcc80','#ef9a9a','#a5d6a7','#fff176','#f48fb1','#bcaaa4','#80deea'];
    var tipPalette = ['#42a5f5','#ab47bc','#26a69a','#ffa726','#ef5350','#66bb6a','#ffee58','#ec407a','#8d6e63','#26c6da'];
    function fmR(v){ return v>=1000000?(v/1000000).toFixed(1)+'M':v>=1000?(v/1000).toFixed(1)+'K':(+v).toFixed(0); }

    // Group by type for the chip tooltip
    var typeMap = {};
    items.forEach(function(r){
        var tid = r.type_id || '0';
        if (!typeMap[tid]) typeMap[tid] = { name: r.type_name || 'Other', amount: 0, resources: [] };
        var rAmt = (+r.rate || 0) * (+r.planned_consumption || 0);
        typeMap[tid].amount += rAmt;
        typeMap[tid].resources.push({ name: r.name || '', amount: rAmt, unit: r.unit || '' });
    });
    var types = Object.keys(typeMap).map(function(k){ return typeMap[k]; });
    types.sort(function(a,b){ return b.amount - a.amount; });

    el.style.position = 'relative';
    el.innerHTML = svg
        + '<div id="cd-g5-chip" style="position:absolute;top:5px;right:6px;'
        + 'background:#1a2a3a;color:#90caf9;border:1px solid #3a5a8a;border-radius:10px;'
        + 'font-size:10px;font-family:\'Barlow Condensed\',sans-serif;letter-spacing:.4px;'
        + 'padding:2px 9px;cursor:pointer;user-select:none;">&#9776; Breakdown</div>';

    // Chip tooltip — shared
    var chipTip = document.getElementById('uc-act-tip');
    if (!chipTip){
        chipTip = document.createElement('div');
        chipTip.id = 'uc-act-tip';
        chipTip.style.cssText = 'position:fixed;z-index:9999;display:none;pointer-events:none;'
            + 'background:#0d1a2e;border:1px solid #2a4a7a;border-radius:8px;'
            + 'box-shadow:0 8px 28px rgba(0,0,0,0.5);padding:12px 14px 10px;';
        document.body.appendChild(chipTip);
    }

    // Secondary resource tooltip — shared
    var resTip = document.getElementById('uc-act-res-tip');
    if (!resTip){
        resTip = document.createElement('div');
        resTip.id = 'uc-act-res-tip';
        resTip.style.cssText = 'position:fixed;z-index:10000;display:none;pointer-events:none;'
            + 'background:#0d1a2e;border:1px solid #2a4a7a;border-radius:8px;'
            + 'box-shadow:0 8px 28px rgba(0,0,0,0.5);padding:12px 14px 10px;';
        document.body.appendChild(resTip);
    }

    var chip = document.getElementById('cd-g5-chip');
    chip.addEventListener('mouseenter', function(){
        // Flatten all resources across types, unique colour per resource, sort type-first then amount-desc
        var allRes = [];
        var maxAmt = 0;
        var resIdx = 0;
        types.forEach(function(t){
            t.resources.slice().sort(function(a,b){ return b.amount - a.amount; }).forEach(function(r){
                if (r.amount > maxAmt) maxAmt = r.amount;
                allRes.push({ name: r.name, amount: r.amount, unit: r.unit, typeName: t.name, col: colPalette[resIdx++ % colPalette.length] });
            });
        });

        // Gridlines
        var tgHtml = '';
        [75,50,25].forEach(function(g){
            tgHtml += '<div style="position:absolute;left:0;right:0;bottom:' + g + '%;'
                + 'border-top:1px dashed rgba(100,130,170,0.55);pointer-events:none;"></div>';
        });
        tgHtml += '<div style="position:absolute;left:0;right:0;top:0;border-top:1px solid rgba(100,130,170,0.7);pointer-events:none;"></div>';
        tgHtml += '<div style="position:absolute;left:0;right:0;bottom:0;border-top:1px solid rgba(100,130,170,0.7);pointer-events:none;"></div>';

        // Y-axis scale — actual amounts
        var tsHtml = '';
        [100,75,50,25,0].forEach(function(g){
            tsHtml += '<div style="position:absolute;right:2px;bottom:calc(' + g + '% - 5px);'
                + 'font-family:\'Nunito\',sans-serif;font-size:9px;color:#7aa8d0;line-height:1;white-space:nowrap;">'
                + fmR(maxAmt * g / 100) + '</div>';
        });

        var tbHtml = '', tblRows = '';
        allRes.forEach(function(r, ri){
            var pct = maxAmt > 0 ? r.amount / maxAmt * 100 : 0;
            var sp  = Math.max(100 - pct, 0).toFixed(2);
            var bp  = Math.max(pct, 0.5).toFixed(2);
            // Clean bar — no text on skin
            tbHtml += '<div style="flex:1;min-width:0;display:flex;flex-direction:column;align-items:center;padding:0 4px;">'
                + '<div style="flex:' + sp + ' 1 0;min-height:0;"></div>'
                + '<div style="flex:' + bp + ' 1 0;width:40%;min-height:0;background:' + r.col + ';border-radius:2px 2px 0 0;"></div>'
                + '</div>';
            // Table row: zebra stripe, swatch, name, amount
            var rowBg = ri % 2 === 0 ? '#162035' : '#0d1a2e';
            tblRows += '<tr style="background:' + rowBg + ';">'
                + '<td style="padding:4px 6px;width:14px;">'
                + '<span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:' + r.col + ';"></span>'
                + '</td>'
                + '<td style="padding:4px 8px 4px 2px;font-family:\'Barlow Condensed\',sans-serif;font-size:12px;color:#c8ddf0;">' + r.name + '</td>'
                + '<td style="padding:4px 8px;font-family:\'Nunito\',sans-serif;font-size:12px;font-weight:700;color:#fff;text-align:right;white-space:nowrap;">'
                + r.amount.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) + (r.unit ? ' /'+r.unit : '')
                + '</td>'
                + '</tr>';
        });

        var tipW = Math.max(360, allRes.length * 56);
        chipTip.style.width = tipW + 'px';
        chipTip.innerHTML = '<div style="font-family:\'Barlow Condensed\',sans-serif;font-size:13px;'
            + 'color:#90caf9;font-weight:700;letter-spacing:.4px;margin-bottom:8px;">Resource Amounts</div>'
            + '<div style="display:flex;height:240px;">'
            + '<div style="width:32px;position:relative;flex-shrink:0;">' + tsHtml + '</div>'
            + '<div style="flex:1;position:relative;min-width:0;">'
            + tgHtml
            + '<div style="position:absolute;inset:0;display:flex;align-items:stretch;padding:0 2px;">' + tbHtml + '</div>'
            + '</div>'
            + '</div>'
            + '<div style="margin-top:10px;border-top:1px solid #1e3a5a;">'
            + '<table style="width:100%;border-collapse:collapse;">'
            + '<thead><tr style="background:#162035;">'
            + '<th style="padding:4px 6px;width:14px;"></th>'
            + '<th style="padding:4px 8px 4px 2px;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#7aa8d0;font-weight:600;text-align:left;letter-spacing:.3px;">Resource</th>'
            + '<th style="padding:4px 8px;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#7aa8d0;font-weight:600;text-align:right;letter-spacing:.3px;">Amount</th>'
            + '</tr></thead>'
            + '<tbody>' + tblRows + '</tbody>'
            + '</table>'
            + '</div>';

        var rect = chip.getBoundingClientRect();
        var left = rect.right - tipW;
        left = Math.max(4, Math.min(left, window.innerWidth - tipW - 4));
        chipTip.style.left = left + 'px';
        chipTip.style.top  = (rect.bottom + 4) + 'px';
        chipTip.style.transform = '';
        chipTip.style.display = 'block';
    });
    chip.addEventListener('mouseleave', function(){ chipTip.style.display = 'none'; });
}

function renderCdCostOfActivity(items, actName, lastQty, estActQty, schedQty, actUnit){
    var el = document.getElementById('cd-g2');
    if (!el) return;

    var unitCost      = 0;
    items.forEach(function(r){ unitCost += (+r.res_qty || 0) * (+r.rate || 0); });
    var estimatedCost = unitCost * estActQty;

    // Estimated Cost of Work Done = Planned Unit Cost × lastQty
    var estWorkDone = 0;
    if (lastQty > 0) {
        var plannedUnitCostForBar = 0;
        items.forEach(function(r){ plannedUnitCostForBar += (+r.rate || 0) * (+r.planned_consumption || 0); });
        estWorkDone = plannedUnitCostForBar * lastQty;
    }

    // Actual Cost of Activity = Actual Unit Cost × Schedule Quantity
    var actualWorkDone = 0, hasActual = false;
    items.forEach(function(r){
        if (r.actual_unit_cost != null && r.actual_consumption != null) {
            actualWorkDone += (+r.actual_unit_cost) * (+r.actual_consumption);
            hasActual = true;
        }
    });
    var actualUnitCost       = (hasActual && lastQty > 0) ? actualWorkDone / lastQty : null;
    var actualCostOfActivity = (actualUnitCost !== null) ? actualUnitCost * schedQty : null;

    if (!estimatedCost){
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No estimate data</div>';
        return;
    }

    var actDiff = actualCostOfActivity !== null ? actualCostOfActivity - estimatedCost : null;
    var an      = sh(actName || '', 40);
    var unitLbl = actUnit ? ' / ' + actUnit : '';

    function fmC(v){ return '&#8377; ' + v.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}); }

    // Grey bar = estimated cost; overlays + red extensions where actuals exceed planned
    var ewdPct = estimatedCost > 0 ? Math.min(estWorkDone  / estimatedCost * 100, 100) : 0;
    var barHtml = '<div style="position:relative;width:100%;height:20px;border-radius:4px;background:#555f6e;margin-bottom:10px;overflow:visible;">';
    // Grey base
    barHtml += '<div style="position:absolute;top:0;left:0;height:100%;width:100%;background:#555f6e;border-radius:4px;"></div>';
    // Light grey = actual cost of activity
    if (actualCostOfActivity !== null) {
        var actW = Math.min(actualCostOfActivity / estimatedCost * 100, 100).toFixed(2);
        barHtml += '<div style="position:absolute;top:0;left:0;height:100%;width:'+actW+'%;background:#78909C;border-radius:4px 0 0 4px;opacity:0.9;"></div>';
        // Orange extension if actual cost of activity > estimated cost
        if (actualCostOfActivity > estimatedCost) {
            var redActW = ((actualCostOfActivity - estimatedCost) / estimatedCost * 100).toFixed(2);
            barHtml += '<div style="position:absolute;top:0;left:100%;height:100%;width:'+redActW+'%;background:#FF7043;border-radius:0 4px 4px 0;"></div>';
        }
    }
    // Dark navy = est cost of work done
    if (estWorkDone > 0) {
        barHtml += '<div style="position:absolute;top:0;left:0;height:100%;width:'+ewdPct.toFixed(2)+'%;background:#0D47A1;border-radius:4px 0 0 4px;"></div>';
    }
    // Yellow = actual cost of work done; red extension if actual work done > est work done
    if (hasActual && estimatedCost > 0) {
        if (actualWorkDone <= estWorkDone || estWorkDone === 0) {
            var aWdW = Math.min(actualWorkDone / estimatedCost * 100, 100).toFixed(2);
            barHtml += '<div style="position:absolute;top:0;left:0;height:100%;width:'+aWdW+'%;background:#455A64;border-radius:4px 0 0 4px;opacity:0.9;"></div>';
        } else {
            barHtml += '<div style="position:absolute;top:0;left:0;height:100%;width:'+ewdPct.toFixed(2)+'%;background:#455A64;border-radius:4px 0 0 4px;opacity:0.9;"></div>';
            var redWdW = ((actualWorkDone - estWorkDone) / estimatedCost * 100).toFixed(2);
            barHtml += '<div style="position:absolute;top:0;left:'+ewdPct.toFixed(2)+'%;height:100%;width:'+redWdW+'%;background:#ef5350;border-radius:0 4px 4px 0;opacity:0.9;"></div>';
        }
    }
    barHtml += '</div>';

    var actColour = actDiff === null ? '#1a2540' : (actDiff > 0 ? '#c62828' : '#2e7d32');

    el.style.position = 'relative';
    el.innerHTML = '<div style="flex:1;min-height:0;display:flex;flex-direction:column;justify-content:flex-start;padding:16px 20px 6px 12px;">'
        + barHtml
        + (function(){
            function lr(col, label, val, txtCol){
                return '<tr>'
                    + '<td style="padding:3px 6px 3px 0;width:12px;"><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:'+col+';"></span></td>'
                    + '<td style="padding:3px 8px 3px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#445;">'+label+'</td>'
                    + '<td style="padding:3px 0;font-family:\'Nunito\',sans-serif;font-size:11px;font-weight:700;color:'+(txtCol||'#1a2540')+';text-align:right;white-space:nowrap;">'+fmC(val)+'</td>'
                    + '</tr>';
            }
            return '<table style="width:100%;border-collapse:collapse;"><tbody>'
                + lr('#555f6e', 'Estimated Cost of Activity',  estimatedCost)
                + (actualCostOfActivity !== null ? lr('#78909C', 'Actual Cost of Activity', actualCostOfActivity, '#546E7A') : '')
                + (estWorkDone > 0            ? lr('#0D47A1', 'Est. Cost of Work Done',   estWorkDone,          '#0D47A1') : '')
                + (hasActual                  ? lr('#455A64', 'Actual Cost of Work Done',  actualWorkDone,       '#546E7A') : '')
                + '</tbody></table>';
          })()
        + (an ? '<div style="margin-top:auto;padding-top:6px;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#5a6e8c;text-align:center;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + an + unitLbl + '</div>' : '')
        + '</div>';
}

function renderCdCostOnCompletion(items, actName, estQty){
    var el = document.getElementById('cd-g3');
    if (!el) return;

    var unitCost = 0;
    items.forEach(function(r){ unitCost += (+r.res_qty || 0) * (+r.rate || 0); });
    var cost = unitCost * estQty;

    if (!cost){
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No estimate data</div>';
        return;
    }

    var maxVal = cost * 2;
    var actual = 0; // formula to be defined later
    var f      = Math.max(0, Math.min(1, actual / maxVal));
    var an     = sh(actName || '', 38);
    var cx=105, cy=92, r=76, sw=14;

    function ptF(frac){ var a=Math.PI*(1-frac); return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)]; }
    function arc(f1,f2,col,cap){
        if(f2<=f1) return ''; cap=cap||'butt';
        var p1=ptF(f1), p2=ptF(f2);
        if((f2-f1)>=1){ var pm=ptF(0.5);
            return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
        }
        return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
    }

    var nr=r-15, na=Math.PI*(1-f);
    var nx=(cx+nr*Math.cos(na)).toFixed(1), ny=(cy-nr*Math.sin(na)).toFixed(1);

    function fmCost(v){ return v>=1000000?(v/1000000).toFixed(1)+'M':v>=1000?(v/1000).toFixed(1)+'K':v.toFixed(0); }

    var svg='<svg width="100%" height="100%" viewBox="0 0 210 134" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet" style="display:block;width:100%;height:auto;">'
        +arc(0, 0.5, '#00838f')
        +arc(0.5, 1,  '#FF6D00')
        +(f>0?arc(0,f,'#1a3a6b','butt'):'')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#dce3ef"/>'
        +'<text x="'+cx+'" y="'+(cy-20)+'" text-anchor="middle" font-size="18" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+fmCost(cost)+'</text>'
        +'<text x="'+cx+'" y="'+(cy-5)+'" text-anchor="middle" font-size="11" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Est. Cost</text>'
        +'<text x="8" y="112" text-anchor="start" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">Actual <tspan font-weight="700">—</tspan></text>'
        +'<text x="202" y="112" text-anchor="end" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">CoC <tspan font-weight="700">'+fmCost(cost)+'</tspan></text>'
        +(an?'<text x="'+cx+'" y="128" text-anchor="middle" font-size="13" fill="#111" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +'</svg>';

    el.innerHTML = svg;
}

function renderCdResourceConsumption(items, actName, lastQty, actUnit){
    var el = document.getElementById('cd-c7');
    if (!el) return;

    function fmR(v){ return v >= 1000000 ? (v/1000000).toFixed(1)+'M' : v >= 1000 ? (v/1000).toFixed(1)+'K' : (+v).toFixed(2); }

    if (!items.length){
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No estimate data</div>';
        return;
    }

    // Flatten all resources from all types
    var resources = [];
    items.forEach(function(r){
        resources.push({
            name:      r.name || '',
            qty:       (r.planned_consumption != null) ? +r.planned_consumption : (+r.res_qty || 0),
            unit:      r.unit || '',
            task_unit: r.task_unit || '',
            type_id:   +r.type_id || 0,
            type_name: r.type_name || '',
            actual:    (r.actual_consumption  != null) ? +r.actual_consumption  : null
        });
    });
    if (!resources.length){
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No data</div>';
        return;
    }

    // Scale: max across all resource planned/actual quantities
    var maxQty = 0;
    resources.forEach(function(r){
        if (r.qty > maxQty) maxQty = r.qty;
        if (r.actual !== null && r.actual > maxQty) maxQty = r.actual;
    });
    if (!maxQty) maxQty = 1;

    function fmQ(v){ return v >= 1000 ? (v/1000).toFixed(1)+'K' : (+v).toFixed(2); }

    // Horizontal bars — name left, bar right
    var rowsHtml = '';
    resources.forEach(function(r, ri){
        var col     = _resTypeCol(r.type_name, '#90caf9');
        var planned = r.qty;
        var actual  = r.actual;
        var resUnit = r.type_id === 4 ? (r.task_unit || r.unit || '') : (r.unit || '');
        var unitSuffix = (resUnit ? ' ' + resUnit : '') + (actUnit ? ' / ' + actUnit : '');
        var barHtml = '';
        if (actual === null) {
            var plW = Math.max(planned / maxQty * 100, 0.5).toFixed(1);
            barHtml = '<div style="width:' + plW + '%;height:100%;background:' + col + ';border-radius:0 3px 3px 0;'
                + 'display:flex;align-items:center;padding-left:5px;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:10px;font-weight:700;color:#fff;white-space:nowrap;">' + fmQ(planned) + unitSuffix + '</span>'
                + '</div>';
        } else if (actual > planned) {
            var plW = Math.max(planned / maxQty * 100, 0.5).toFixed(1);
            var exW = Math.max((actual - planned) / maxQty * 100, 0.5).toFixed(1);
            barHtml = '<div style="width:' + plW + '%;height:100%;background:' + col + ';display:flex;align-items:center;padding-left:5px;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:10px;font-weight:700;color:#fff;white-space:nowrap;">' + fmQ(planned) + unitSuffix + '</span></div>'
                + '<div style="width:' + exW + '%;height:100%;background:#ef5350;border-radius:0 3px 3px 0;display:flex;align-items:center;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:10px;font-weight:700;color:#fff;white-space:nowrap;padding-left:3px;">+' + fmQ(actual-planned) + unitSuffix + '</span></div>';
        } else {
            var acW  = Math.max(actual / maxQty * 100, 0.5).toFixed(1);
            var savW = Math.max((planned - actual) / maxQty * 100, 0).toFixed(1);
            barHtml = '<div style="width:' + acW + '%;height:100%;background:' + col + ';display:flex;align-items:center;padding-left:5px;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:10px;font-weight:700;color:#fff;white-space:nowrap;">' + fmQ(actual) + unitSuffix + '</span></div>'
                + '<div style="width:' + savW + '%;height:100%;background:#66bb6a;border-radius:0 3px 3px 0;'
                + 'display:flex;align-items:center;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:10px;font-weight:700;color:#fff;white-space:nowrap;padding-left:3px;">-' + fmQ(planned-actual) + unitSuffix + '</span></div>';
        }
        rowsHtml += '<div data-res-idx="'+ri+'" style="display:flex;align-items:center;margin-bottom:5px;cursor:pointer;">'
            + '<div style="width:130px;min-width:130px;font-family:\'Barlow Condensed\',sans-serif;font-size:12px;font-weight:700;color:#000;'
            + 'white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding-right:8px;text-align:right;">' + r.name + '</div>'
            + '<div style="flex:1;height:18px;background:#e8edf3;border-radius:3px;overflow:hidden;display:flex;">' + barHtml + '</div>'
            + '</div>';
    });

    el.innerHTML = '<div style="flex:1;min-height:0;display:flex;flex-direction:column;justify-content:center;padding:6px 4px;overflow-y:auto;">'
        + rowsHtml
        + (actName ? '<div class="resfoot" style="margin-top:4px;">' + sh(actName, 32) + '</div>' : '')
        + '</div>';

    var tipEl7 = document.getElementById('rc-cons-tip');
    if (!tipEl7){
        tipEl7 = document.createElement('div');
        tipEl7.id = 'rc-cons-tip';
        tipEl7.style.cssText = 'position:fixed;z-index:9999;display:none;pointer-events:none;'
            + 'background:#0d1a2e;border-radius:8px;box-shadow:0 8px 28px rgba(0,0,0,0.45);padding:12px 18px;min-width:290px;';
        document.body.appendChild(tipEl7);
    }
    el.querySelectorAll('[data-res-idx]').forEach(function(row){
        row.addEventListener('mouseenter', function(){
            var r       = resources[+row.getAttribute('data-res-idx')];
            var col     = _resTypeCol(r.type_name, '#90caf9');
            var resUnit = r.type_id === 4 ? (r.task_unit || r.unit || '') : (r.unit || '');
            var uSfx    = (resUnit ? ' ' + resUnit : '') + (actUnit ? ' / ' + actUnit : '');
            var diff    = r.actual !== null ? r.actual - r.qty : null;
            var dCol    = diff === null ? '' : (diff > 0 ? '#ef9a9a' : '#a5d6a7');
            var dTxt    = diff === null ? '' : (diff > 0 ? '+' : '') + fmQ(Math.abs(diff)) + uSfx;
            tipEl7.innerHTML = '<div style="display:flex;align-items:center;gap:7px;margin-bottom:8px;">'
                + '<span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:'+col+';flex-shrink:0;"></span>'
                + '<span style="font-family:\'Barlow Condensed\',sans-serif;font-size:13px;color:#fff;font-weight:700;">'+r.name+'</span>'
                + '</div>'
                + '<table style="width:100%;border-collapse:collapse;">'
                + '<tr><td style="padding:3px 14px 3px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#fff;">Planned</td>'
                + '<td style="padding:3px 0;font-family:\'Nunito\',sans-serif;font-size:11px;color:#fff;font-weight:700;text-align:right;white-space:nowrap;">'+fmQ(r.qty)+uSfx+'</td></tr>'
                + '<tr><td style="padding:3px 14px 3px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#fff;">Actual</td>'
                + '<td style="padding:3px 0;font-family:\'Nunito\',sans-serif;font-size:11px;font-weight:700;color:'+(r.actual !== null ? '#fff' : '#5a6e8c')+';text-align:right;white-space:nowrap;">'+(r.actual !== null ? fmQ(r.actual)+uSfx : '—')+'</td></tr>'
                + (diff !== null ? '<tr><td style="padding:3px 14px 3px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#fff;">Diff</td>'
                + '<td style="padding:3px 0;font-family:\'Nunito\',sans-serif;font-size:11px;font-weight:700;color:'+dCol+';text-align:right;white-space:nowrap;">'+dTxt+'</td></tr>' : '')
                + '</table>';
            var rect = row.getBoundingClientRect();
            var tipW = 290;
            tipEl7.style.width = tipW + 'px';
            var left = rect.left + rect.width / 2 - tipW / 2;
            left = Math.max(4, Math.min(left, window.innerWidth - tipW - 4));
            tipEl7.style.left = left + 'px';
            tipEl7.style.top  = (rect.top - 8) + 'px';
            tipEl7.style.transform = 'translateY(-100%)';
            tipEl7.style.display = 'block';
        });
        row.addEventListener('mouseleave', function(){ tipEl7.style.display = 'none'; });
    });
}

// ── Current Project Cost panel (#cd-g1) ──────────────────────────────────────
function renderCdCurrentProjectCost(activities){
    var el = document.getElementById('cd-g1');
    if (!el) return;

    var estimatedProjectCost = 0;
    var estimatedCurrentCost = 0;
    var actualCurrentCost    = 0;

    activities.forEach(function(a){
        estimatedProjectCost += (+a.activity_cost || 0);
        var unitCost  = +a.unit_cost    || 0;
        var cumQty    = +a.cumulated_qty || 0;
        if (cumQty > 0 && unitCost > 0) estimatedCurrentCost += unitCost * cumQty;
        actualCurrentCost += (+a.actual_cost || 0);
    });

    if (!estimatedProjectCost){
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No estimate data</div>';
        return;
    }

    var pct  = Math.min(estimatedCurrentCost / estimatedProjectCost * 100, 100);
    var barW = pct.toFixed(2);

    function fmC(v){ return '&#8377; ' + v.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}); }

    function lgRow(swatch, label, value){
        return '<tr>'
            + '<td style="padding:4px 6px 4px 0;width:12px;"><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:' + swatch + ';"></span></td>'
            + '<td style="padding:4px 8px 4px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#445;">' + label + '</td>'
            + '<td style="padding:4px 0;font-family:\'Nunito\',sans-serif;font-size:11px;font-weight:700;color:#1a2540;text-align:right;white-space:nowrap;">' + fmC(value) + '</td>'
            + '</tr>';
    }

    el.innerHTML = '<div style="flex:1;min-height:0;display:flex;flex-direction:column;justify-content:flex-start;padding:16px 4px 6px;">'
        + '<div style="position:relative;width:100%;height:18px;border-radius:4px;overflow:hidden;background:#00838f;margin-bottom:10px;">'
        + '<div style="position:absolute;top:0;left:0;height:100%;width:' + barW + '%;background:#26c6da;border-radius:4px 0 0 4px;"></div>'
        + (pct > 6 ? '<span style="position:absolute;top:50%;left:' + (pct / 2) + '%;transform:translate(-50%,-50%);font-family:\'Nunito\',sans-serif;font-size:10px;font-weight:700;color:#fff;white-space:nowrap;">' + pct.toFixed(1) + '%</span>' : '')
        + '</div>'
        + '<table style="width:100%;border-collapse:collapse;"><tbody>'
        + lgRow('#00838f', 'Estimated Cost of Project',         estimatedProjectCost)
        + lgRow('#26c6da', 'Estimated Current Project Cost',    estimatedCurrentCost)
        + lgRow('#27ae60', 'Actual Current Cost of Project',    actualCurrentCost)
        + '</tbody></table>'
        + '</div>';
}

// ── Data fetch ────────────────────────────────────────────────────────────────
function loadAll(){
    $.ajax({
        type:'POST', url:'../projectsmain/performancedashboard', dataType:'json',
        success: function(d){
            if (!d || d.error === undefined) return;
            var name = d.project_name || 'Project';
            $('#pd-title').text(name + ' — Performance Dashboard');
            if (!_cdProjectName) _cdProjectName = name;

            _groups    = d.iow_groups  || [];
            _iow_items = d.iow_items   || [];
            _all       = d.activities  || [];

            // IOW Groups in pd-c1 — clicking a group loads its IOW items into pd-c3
            renderBars('pd-c1', _groups.map(function(r){
                return {name:r.name, scheduled:+r.scheduled||0, delay:+r.delay||0, id:r.id,
                        critical:groupIsCritical(r.id),
                        start_date:r.start_date||'', end_date:r.end_date||'',
                        actual_end_date:(r.actual_end_date&&r.actual_end_date!=='0000-00-00')?r.actual_end_date:'',
                        duration_days:+r.scheduled||0};
            }), filterByGroup);

            // Project-level duration bar
            renderProjectBar(
                document.getElementById('pd-c2'),
                +(d.project_bar&&d.project_bar.budgeted)||0,
                +(d.project_bar&&d.project_bar.actual)||0,
                name,
                (d.project_bar&&d.project_bar.b_end_date)||'',
                (d.project_bar&&d.project_bar.a_end_date)||''
            );

            // Default: first group → its IOW items → first IOW's activities
            if (_groups.length) filterByGroup(_groups[0].id, d.default_iow_id);

            if (d.kpi) doKpi(d.kpi);
        },
        error: function(x){ console.error('PerfDash error', x.responseText); }
    });
}

function loadKpi(actid){
    $.ajax({
        type:'POST', url:'../projectsmain/performancedashboardkpi',
        data:{actid:actid}, dataType:'json',
        success: function(d){ if (d && d.kpi) doKpi(d.kpi); }
    });
}

// ── Group click → show IOW items for that group in pd-c3 ──────────────────────
function filterByGroup(groupId, preselectIowId){
    var gid = String(groupId);
    var filtered = _iow_items.filter(function(i){ return String(i.group_id) === gid; });
    // Fallback: if iowGroupid linkage is missing in DB, show all IOW items
    if (!filtered.length) filtered = _iow_items;
    renderBars('pd-c3', filtered.map(function(r){
        return {name:r.name, scheduled:+r.scheduled||0, delay:+r.delay||0, id:r.id,
                critical:iowIsCritical(r.id),
                start_date:r.start_date||'', end_date:r.end_date||'',
                actual_end_date:(r.actual_end_date&&r.actual_end_date!=='0000-00-00')?r.actual_end_date:'',
                duration_days:+r.scheduled||0};
    }), filterByIow);
    $('#pd-c1 .brow').removeClass('brow-active');
    $('#pd-c1 .brow[data-aid="' + groupId + '"]').addClass('brow-active');
    var firstId = preselectIowId || (filtered.length ? filtered[0].id : null);
    if (firstId) filterByIow(firstId);
}

// ── IOW click → show ongoing / upcoming activities ────────────────────────────
function filterByIow(iowId){
    var sid = String(iowId);
    $('#pd-c3 .brow').removeClass('brow-active');
    $('#pd-c3 .brow[data-aid="' + iowId + '"]').addClass('brow-active');
    var filtered  = _all.filter(function(a){ return String(a.scheduleitem_id) === sid; });
    var fOngoing  = filtered.filter(function(a){
        return parseInt(a.pr_report_count, 10) > 0;
    });
    var fUpcoming = filtered.filter(function(a){
        return !(parseInt(a.pr_report_count, 10) > 0);
    });
    renderBars('pd-c4', toBarItems(fOngoing, false));
    renderBars('pd-c5', toBarItems(fUpcoming, true));
    if (filtered.length) loadKpi(filtered[0].id);
}

// ── Criticality propagation: activity → IOW → IOW group ──────────────────────
function isCriticalActivity(a){
    return a.critical_status === 'Yes' || a.critical_status === 1 || a.critical_status === '1';
}
function iowIsCritical(iowId){
    var sid = String(iowId);
    return _all.some(function(a){
        return String(a.scheduleitem_id) === sid && isCriticalActivity(a);
    });
}
function groupIsCritical(groupId){
    var gid = String(groupId);
    return _iow_items.some(function(i){
        return String(i.group_id) === gid && iowIsCritical(i.id);
    });
}

function toBarItems(acts, isUpcoming){
    var today = new Date().toISOString().slice(0, 10);
    return acts.map(function(r){
        var planned = parseFloat(r.old_duration) || 0;
        var sc, dl, startDelayDays = 0, projEndDate = '';

        if (isUpcoming) {
            // Upcoming: red bar extension = start delay days
            if (r.start_date && r.start_date < today) {
                startDelayDays = Math.round((new Date(today) - new Date(r.start_date)) / 86400000);
            }
            sc = planned;
            dl = startDelayDays;
            if (startDelayDays > 0 && r.end_date && r.end_date !== '0000-00-00') {
                var pe = new Date(r.end_date);
                pe.setDate(pe.getDate() + startDelayDays);
                projEndDate = pe.toISOString().slice(0, 10);
            }
        } else {
            // Ongoing: projected_duration is computed server-side from the canonical
            // start anchor (earlier of planned start and reported start)
            var projDur = +r.projected_duration || 0;
            if (projDur > planned && planned > 0) {
                sc = planned;
                dl = projDur - planned;
                if (r.spr_start_date && r.spr_start_date !== '0000-00-00') {
                    var pe2 = new Date(r.spr_start_date);
                    pe2.setDate(pe2.getDate() + Math.round(projDur) - 1);
                    projEndDate = pe2.toISOString().slice(0, 10);
                }
            } else {
                sc = projDur || planned;
                dl = 0;
            }
        }

        return {
            name:            r.name,
            scheduled:       sc,
            delay:           dl,
            critical:        (r.critical_status === 'Yes' || r.critical_status === 1),
            id:              r.id,
            startDelayed:    isUpcoming && startDelayDays > 0,
            startDelayDays:  startDelayDays,
            start_date:      r.start_date || '',
            end_date:        r.end_date   || '',
            actual_end_date: (r.actual_end_date && r.actual_end_date !== '0000-00-00') ? r.actual_end_date : '',
            proj_end_date:   projEndDate,
            duration_days:   parseFloat(r.old_duration) || parseFloat(r.duration) || sc || 0
        };
    });
}

function toCostBarItems(acts){
    return acts.map(function(r){
        var est      = +r.activity_cost    || 0;
        var unitCost = +r.unit_cost        || 0;  // SUM(res_qty × rate) per schedule unit
        var awd      = +r.actual_work_done || +r.actual_cost || 0;
        var cumQty   = +r.cumulated_qty    || 0;
        var schedQty = +r.quantity         || 1;
        var ewd      = unitCost * cumQty;                             // planned unit cost × lastQty
        var acoa     = (awd > 0 && cumQty > 0) ? (awd / cumQty) * schedQty : 0;
        return {
            name:               r.name,
            cost:               est,
            actual_cost_of_act: acoa,
            est_work_done:      ewd,
            actual_work_done:   awd,
            id:                 r.id
        };
    });
}

function fmtCost(v){
    if (!v) return '0';
    if (v >= 1e7) return (v/1e7).toFixed(1)+'Cr';
    if (v >= 1e5) return (v/1e5).toFixed(1)+'L';
    if (v >= 1e3) return (v/1e3).toFixed(1)+'K';
    return Math.round(v).toString();
}

function renderSimpleCostBars(containerId, items, onRowClick, showOverlay, getTooltipItems){
    var el = document.getElementById(containerId);
    if (!el) return;
    var maxVal = 0;
    items.forEach(function(r){ if ((r.cost||0) > maxVal) maxVal = r.cost||0; });
    if (!maxVal) maxVal = 1;
    var html = '';
    items.forEach(function(r){
        var est = r.cost || 0;
        var awd = showOverlay ? (r.awd || 0) : 0;
        var pct    = (est / maxVal * 100).toFixed(1);
        var awdPct = Math.min(awd / maxVal * 100, parseFloat(pct)).toFixed(1);
        var bar = est > 0
            ? '<div class="bs" style="width:'+pct+'%;background:#4A5568;position:relative;">'
              + (awd > 0 ? '<div style="position:absolute;top:0;left:0;height:100%;width:'+awdPct+'%;background:#78909C;border-radius:2px 0 0 2px;"></div>' : '')
              + '</div>'
            : '<div class="bs" style="width:2%;background:#ccc;"></div>';
        html += '<div class="brow" data-aid="'+r.id+'" style="cursor:pointer;display:flex;align-items:center;">'
              + '<div class="blbl" style="color:#000;" title="'+r.name+'">'+sh(r.name,30)+'</div>'
              + '<div class="btrk" style="flex:1;">'+bar+'</div>'
              + '<div style="font-size:11px;color:#000;font-weight:700;min-width:38px;text-align:right;padding-left:5px;white-space:nowrap;">'+fmtCost(est)+'</div>'
              + '</div>';
    });
    el.innerHTML = html;
    $(el).find('.brow[data-aid]').on('click', function(){
        if (onRowClick) onRowClick($(this).data('aid'));
    });

    // Tooltip
    if (!getTooltipItems) return;
    var tipEl = document.getElementById('iow-cost-tip');
    if (!tipEl){
        tipEl = document.createElement('div');
        tipEl.id = 'iow-cost-tip';
        tipEl.style.cssText = 'position:fixed;z-index:9999;display:none;pointer-events:none;'
            + 'background:#0d1a2e;border-radius:8px;box-shadow:0 8px 28px rgba(0,0,0,0.5);padding:10px 14px;';
        document.body.appendChild(tipEl);
    }
    el.querySelectorAll('.brow[data-aid]').forEach(function(row){
        var iowItem = items.filter(function(r){ return String(r.id) === String($(row).data('aid')); })[0];
        if (!iowItem) return;
        row.addEventListener('mouseenter', function(){
            var acts = getTooltipItems(iowItem);
            if (!acts || !acts.length){ tipEl.style.display='none'; return; }
            function fmFull(v){ return '&#8377;' + Math.round(+v).toLocaleString(); }
            var tipW = 560;
            tipEl.style.width = tipW+'px';
            var rect = row.getBoundingClientRect();
            var left = Math.max(4, Math.min(rect.left, window.innerWidth - tipW - 4));
            tipEl.style.left = left+'px';
            tipEl.style.top  = (rect.top - 8)+'px';
            tipEl.style.transform = 'translateY(-100%)';
            tipEl.innerHTML = '<div style="font-family:\'Barlow Condensed\',sans-serif;font-size:13px;color:#fff;font-weight:700;margin-bottom:6px;">'+sh(iowItem.name,30)+'</div>'
                + '<div style="font-family:\'Nunito\',sans-serif;font-size:11px;color:#8a9bb0;text-align:center;padding:8px;">Loading…</div>';
            tipEl.style.display = 'block';
            // Fetch exact data for each activity
            var requests = acts.map(function(a){
                return $.ajax({ type:'POST', url:'../projectsmain/costdashboardactivity', data:{actid: a.id}, dataType:'json' });
            });
            $.when.apply($, requests).then(function(){
                var results = requests.length === 1 ? [arguments[0]] : Array.prototype.slice.call(arguments).map(function(r){ return r[0]; });
                var rows = '';
                acts.forEach(function(a, i){
                    var d = results[i] || {};
                    var items = d.items || [];
                    var est2 = 0;
                    items.forEach(function(r){ est2 += (+r.res_qty||0)*(+r.rate||0); });
                    est2 *= (+d.activity_qty||0);
                    var awd2 = 0;
                    items.forEach(function(r){
                        if (r.actual_unit_cost != null && r.actual_consumption != null)
                            awd2 += (+r.actual_unit_cost)*(+r.actual_consumption);
                    });
                    var lastQty2  = +d.last_report_qty || 0;
                    var schedQty2 = +d.schedule_qty   || 0;
                    var acoa2 = (lastQty2 > 0) ? (awd2 / lastQty2) * schedQty2 : 0;
                    var ewd2  = 0;
                    if (lastQty2 > 0) {
                        items.forEach(function(r){ ewd2 += (+r.rate||0) * (+r.planned_consumption||0); });
                        ewd2 *= lastQty2;
                    }
                    rows += '<tr>'
                        + '<td style="padding:4px 8px 4px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#fff;white-space:nowrap;">'+sh(a.name,18)+'</td>'
                        + '<td style="padding:4px 8px 4px 0;font-family:\'Nunito\',sans-serif;font-size:10px;color:#fff;font-weight:700;text-align:right;white-space:nowrap;">'+fmFull(est2)+'</td>'
                        + '<td style="padding:4px 8px 4px 0;font-family:\'Nunito\',sans-serif;font-size:10px;color:#fff;font-weight:700;text-align:right;white-space:nowrap;">'+fmFull(acoa2)+'</td>'
                        + '<td style="padding:4px 8px 4px 0;font-family:\'Nunito\',sans-serif;font-size:10px;color:#fff;font-weight:700;text-align:right;white-space:nowrap;">'+fmFull(ewd2)+'</td>'
                        + '<td style="padding:4px 0;font-family:\'Nunito\',sans-serif;font-size:10px;color:#fff;font-weight:700;text-align:right;white-space:nowrap;">'+fmFull(awd2)+'</td>'
                        + '</tr>';
                });
                tipEl.innerHTML = '<div style="font-family:\'Barlow Condensed\',sans-serif;font-size:13px;color:#fff;font-weight:700;margin-bottom:8px;">'+sh(iowItem.name,30)+'</div>'
                    + '<table style="width:100%;border-collapse:collapse;">'
                    + '<thead><tr>'
                    + '<th style="padding:3px 10px 3px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:10px;color:#fff;font-weight:600;text-align:left;">Activity</th>'
                    + '<th style="padding:3px 8px 3px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:10px;color:#fff;font-weight:600;text-align:right;">Estimated</th>'
                    + '<th style="padding:3px 8px 3px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:10px;color:#fff;font-weight:600;text-align:right;">Actual Cost</th>'
                    + '<th style="padding:3px 8px 3px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:10px;color:#fff;font-weight:600;text-align:right;">Est.Work Done</th>'
                    + '<th style="padding:3px 0;font-family:\'Barlow Condensed\',sans-serif;font-size:10px;color:#fff;font-weight:600;text-align:right;">Act.Work Done</th>'
                    + '</tr></thead>'
                    + '<tbody style="border-top:1px solid rgba(100,130,170,0.3);">'+rows+'</tbody>'
                    + '</table>';
            });
        });
        row.addEventListener('mouseleave', function(){ tipEl.style.display='none'; });
    });
}

function renderCostBars(containerId, items, onRowClick){
    var el = document.getElementById(containerId);
    if (!el) return;
    var html = '';
    items.forEach(function(r){
        var est  = r.cost          || 0;
        var acoa = r.acoa          || 0;
        var ewd  = r.est_work_done || 0;
        var awd  = r.awd           || 0;
        var bar  = '';
        if (!est) {
            bar = '<div class="bs" style="width:2%;background:#ccc;"></div>';
        } else {
            var acoaPct = Math.min(acoa / est * 100, 100).toFixed(1);
            var ewdPct  = Math.min(ewd  / est * 100, 100).toFixed(1);
            var awdPct  = Math.min(awd  / est * 100, 100).toFixed(1);
            bar = '<div class="bs" style="width:100%;background:#4A5568;position:relative;">'
                + (acoa > 0 ? '<div style="position:absolute;top:0;left:0;height:100%;width:'+acoaPct+'%;background:#40C4FF;opacity:0.85;"></div>' : '')
                + (ewd  > 0 ? '<div style="position:absolute;top:0;left:0;height:100%;width:'+ewdPct +'%;background:#0D47A1;"></div>' : '')
                + (awd  > 0 ? '<div style="position:absolute;top:0;left:0;height:100%;width:'+awdPct +'%;background:#455A64;opacity:0.9;"></div>' : '')
                + '</div>';
        }
        html += '<div class="brow" data-aid="'+r.id+'" style="cursor:pointer;display:flex;align-items:center;">'
              + '<div class="blbl" style="color:#000;" title="'+r.name+'">'+sh(r.name,30)+'</div>'
              + '<div class="btrk" style="flex:1;">'+bar+'</div>'
              + '<div style="font-size:11px;color:#000;font-weight:700;min-width:38px;text-align:right;padding-left:5px;white-space:nowrap;">'+fmtCost(est)+'</div>'
              + '</div>';
    });
    el.innerHTML = html;
    $(el).find('.brow[data-aid]').on('click', function(){
        var id = $(this).data('aid');
        if (onRowClick) onRowClick(id);
    });
}

function renderActivityCostBars(containerId, items, onRowClick){
    var el = document.getElementById(containerId);
    if (!el) return;
    var html = '';
    items.forEach(function(r){
        var est  = r.cost                || 0;  // estimated cost of activity
        var acoa = r.actual_cost_of_act  || 0;  // actual cost of activity
        var ewd  = r.est_work_done       || 0;  // est cost of work done
        var awd  = r.actual_work_done    || 0;  // actual cost of work done
        var rowMax = Math.max(est, acoa, 1);

        // Same 4-layer bar as Cost of Activity panel (cd-g2), scale = est
        var bar = '';
        if (!est) {
            bar = '<div class="bs" style="width:2%;background:#ccc"></div>';
        } else {
            var acoaPct = Math.min(acoa / est * 100, 100).toFixed(1);
            var ewdPct  = Math.min(ewd  / est * 100, 100).toFixed(1);
            var awdPct  = Math.min(awd  / est * 100, 100).toFixed(1);
            bar = '<div class="bs" style="width:100%;background:#555f6e;position:relative;overflow:visible;">'
                + '<div style="position:absolute;top:0;left:0;height:100%;width:100%;background:#555f6e;border-radius:2px;"></div>'
                + (acoa > 0 ? '<div style="position:absolute;top:0;left:0;height:100%;width:'+acoaPct+'%;background:#78909C;opacity:0.9;border-radius:2px 0 0 2px;"></div>' : '')
                + (acoa > est ? '<div style="position:absolute;top:0;left:100%;height:100%;width:'+((acoa-est)/est*100).toFixed(1)+'%;background:#FF7043;border-radius:0 2px 2px 0;"></div>' : '')
                + (ewd  > 0 ? '<div style="position:absolute;top:0;left:0;height:100%;width:'+ewdPct +'%;background:#0D47A1;border-radius:2px 0 0 2px;"></div>' : '')
                + (awd  > 0 ? '<div style="position:absolute;top:0;left:0;height:100%;width:'+awdPct +'%;background:#455A64;opacity:0.9;border-radius:2px 0 0 2px;"></div>' : '')
                + (awd > ewd ? '<div style="position:absolute;top:0;left:'+ewdPct+'%;height:100%;width:'+((awd-ewd)/est*100).toFixed(1)+'%;background:#ef5350;opacity:0.9;"></div>' : '')
                + '</div>';
        }

        html += '<div class="brow" data-aid="'+r.id+'" style="cursor:pointer;display:flex;align-items:center;">'
              + '<div class="blbl" style="color:#000;" title="'+r.name+'">'+sh(r.name,30)+'</div>'
              + '<div class="btrk" style="flex:1;overflow:visible;">'+bar+'</div>'
              + '<div style="font-size:11px;color:#000;font-weight:700;min-width:38px;text-align:right;padding-left:5px;white-space:nowrap;">'+fmtCost(est)+'</div>'
              + '</div>';
    });
    el.innerHTML = html;
    $(el).find('.brow[data-aid]').on('click', function(){
        if (onRowClick) onRowClick($(this).data('aid'));
    });
}

// ── Resource Cost panel (#cd-g3) ─────────────────────────────────────────────
function renderCdResourceCost(items, actName){
    var el = document.getElementById('cd-rcost');
    if (!el) return;

    function fmR(v){ return v>=1000000?(v/1000000).toFixed(1)+'M':v>=1000?(v/1000).toFixed(1)+'K':(+v).toFixed(2); }
    function fmRs(v){ return '&#8377;'+fmR(v); }

    if (!items.length){
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No estimate data</div>';
        return;
    }

    // Group by type: planned = SUM(rate × planned_consumption), actual = SUM(actual_unit_cost × actual_consumption)
    var typeMap = {}, typeOrder = [];
    items.forEach(function(r){
        var tid = r.type_id != null ? String(r.type_id) : '0';
        if (!typeMap[tid]) {
            typeMap[tid] = { name: r.type_name || 'Other', planned: 0, actual: 0, hasActual: false, resources: [] };
            typeOrder.push(tid);
        }
        var rPlanned = (+r.rate || 0) * (+r.planned_consumption || 0);
        var rActual  = (r.actual_unit_cost != null && r.actual_consumption != null)
            ? (+r.actual_unit_cost) * (+r.actual_consumption) : null;
        typeMap[tid].planned += rPlanned;
        if (rActual !== null) { typeMap[tid].actual += rActual; typeMap[tid].hasActual = true; }
        typeMap[tid].resources.push({ name: r.name || '', planned: rPlanned, actual: rActual });
    });

    var types = typeOrder.map(function(k){ return typeMap[k]; });
    var maxVal = 0;
    types.forEach(function(t){
        if (t.planned > maxVal) maxVal = t.planned;
        if (t.hasActual && t.actual > maxVal) maxVal = t.actual;
    });
    if (!maxVal){
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No estimate data</div>';
        return;
    }

    // Y-axis
    var scaleHtml = '';
    [100,75,50,25,0].forEach(function(g){
        scaleHtml += '<div style="position:absolute;right:2px;bottom:calc('+g+'% - 5px);font-family:\'Nunito\',sans-serif;font-size:8px;color:#8a9bb0;line-height:1;">'+fmR(maxVal*g/100)+'</div>';
    });
    var gridHtml = '';
    [75,50,25].forEach(function(g){
        gridHtml += '<div style="position:absolute;left:0;right:0;bottom:'+g+'%;border-top:1px dashed rgba(90,110,140,0.22);pointer-events:none;"></div>';
    });
    gridHtml += '<div style="position:absolute;left:0;right:0;top:0;border-top:1px solid rgba(90,110,140,0.3);pointer-events:none;"></div>';
    gridHtml += '<div style="position:absolute;left:0;right:0;bottom:0;border-top:1px solid rgba(90,110,140,0.35);pointer-events:none;"></div>';

    var barsHtml = '', labelsHtml = '';
    types.forEach(function(t, i){
        var col     = _resTypeCol(t.name, '#90caf9');
        var planned = t.planned;
        var actual  = t.hasActual ? t.actual : null;
        var barInner = '';

        if (actual === null) {
            var plPct = Math.max(planned / maxVal * 100, 0.5);
            var spPct = Math.max(100 - plPct, 0);
            barInner = '<div style="flex:'+spPct.toFixed(2)+' 1 0;min-height:0;"></div>'
                + '<div style="flex:'+plPct.toFixed(2)+' 1 0;width:80%;min-height:0;background:'+col+';border-radius:3px 3px 0 0;'
                + 'display:flex;align-items:flex-start;justify-content:center;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:9px;font-weight:700;color:#fff;padding:2px 1px;white-space:nowrap;">'+fmRs(planned)+'</span>'
                + '</div>';
        } else if (actual > planned) {
            var plPct = Math.max(planned / maxVal * 100, 0.5);
            var exPct = Math.max((actual - planned) / maxVal * 100, 0.5);
            var spPct = Math.max(100 - plPct - exPct, 0);
            barInner = '<div style="flex:'+spPct.toFixed(2)+' 1 0;min-height:0;"></div>'
                + '<div style="flex:'+exPct.toFixed(2)+' 1 0;width:80%;min-height:0;background:#ef5350;border-radius:3px 3px 0 0;'
                + 'display:flex;align-items:flex-start;justify-content:center;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:9px;font-weight:700;color:#fff;padding:2px 1px;white-space:nowrap;">+'+fmRs(actual-planned)+'</span>'
                + '</div>'
                + '<div style="flex:'+plPct.toFixed(2)+' 1 0;width:80%;min-height:0;background:'+col+';'
                + 'display:flex;align-items:flex-start;justify-content:center;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:9px;font-weight:700;color:#fff;padding:2px 1px;white-space:nowrap;">'+fmRs(planned)+'</span>'
                + '</div>';
        } else {
            var acPct  = Math.max(actual / maxVal * 100, 0.5);
            var savPct = Math.max((planned - actual) / maxVal * 100, 0);
            var spPct  = Math.max(100 - acPct - savPct, 0);
            barInner = '<div style="flex:'+spPct.toFixed(2)+' 1 0;min-height:0;"></div>'
                + '<div style="flex:'+savPct.toFixed(2)+' 1 0;width:80%;min-height:0;background:#66bb6a;border-radius:3px 3px 0 0;'
                + 'display:flex;align-items:flex-start;justify-content:center;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:9px;font-weight:700;color:#fff;padding:2px 1px;white-space:nowrap;">-'+fmRs(planned-actual)+'</span>'
                + '</div>'
                + '<div style="flex:'+acPct.toFixed(2)+' 1 0;width:80%;min-height:0;background:'+col+';'
                + 'display:flex;align-items:flex-start;justify-content:center;overflow:hidden;">'
                + '<span style="font-family:\'Nunito\',sans-serif;font-size:9px;font-weight:700;color:#fff;padding:2px 1px;white-space:nowrap;">'+fmRs(actual)+'</span>'
                + '</div>';
        }

        barsHtml += '<div data-rc-idx="'+i+'" style="flex:1;min-width:0;display:flex;flex-direction:column;align-items:center;padding:0 3px;cursor:pointer;">'
            + barInner + '</div>';

        labelsHtml += '<div style="flex:1;min-width:0;text-align:center;padding:2px 2px 0;">'
            + '<div style="font-family:\'Barlow Condensed\',sans-serif;font-size:11px;font-weight:700;color:#000;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">'+t.name+'</div>'
            + '</div>';
    });

    el.innerHTML = '<div style="flex:1;min-height:0;display:flex;flex-direction:column;">'
        + '<div style="flex:1;min-height:0;display:flex;">'
        + '<div style="width:28px;position:relative;flex-shrink:0;">'+scaleHtml+'</div>'
        + '<div style="flex:1;position:relative;min-width:0;">'+gridHtml
        + '<div style="position:absolute;inset:0;display:flex;align-items:stretch;padding:0 2px;">'+barsHtml+'</div>'
        + '</div></div>'
        + '<div style="display:flex;padding-left:28px;">'+labelsHtml+'</div>'
        + (actName ? '<div class="resfoot">'+sh(actName,32)+'</div>' : '')
        + '</div>';

    // Tooltip
    var tipEl = document.getElementById('rc-cost-tip');
    if (!tipEl){
        tipEl = document.createElement('div');
        tipEl.id = 'rc-cost-tip';
        tipEl.style.cssText = 'position:fixed;z-index:9999;display:none;pointer-events:none;'
            + 'background:#0d1a2e;border-radius:8px;box-shadow:0 8px 28px rgba(0,0,0,0.45);'
            + 'padding:10px 14px 10px;min-width:260px;';
        document.body.appendChild(tipEl);
    }

    el.querySelectorAll('[data-rc-idx]').forEach(function(barCol){
        barCol.addEventListener('mouseenter', function(){
            var t   = types[+barCol.getAttribute('data-rc-idx')];
            var col = _resTypeCol(t.name, '#90caf9');

            // Scale across all resources in this type
            var tipMax = 0;
            t.resources.forEach(function(r){
                if (r.planned > tipMax) tipMax = r.planned;
                if (r.actual !== null && r.actual > tipMax) tipMax = r.actual;
            });
            if (!tipMax) tipMax = 1;

            // Y-axis grid + scale labels
            var tgHtml = '', tsHtml = '';
            [75,50,25].forEach(function(g){
                tgHtml += '<div style="position:absolute;left:0;right:0;bottom:'+g+'%;border-top:1px dashed rgba(100,130,170,0.4);pointer-events:none;"></div>';
            });
            tgHtml += '<div style="position:absolute;left:0;right:0;top:0;border-top:1px solid rgba(100,130,170,0.5);pointer-events:none;"></div>';
            tgHtml += '<div style="position:absolute;left:0;right:0;bottom:0;border-top:1px solid rgba(100,130,170,0.5);pointer-events:none;"></div>';
            [100,75,50,25,0].forEach(function(g){
                tsHtml += '<div style="position:absolute;right:2px;bottom:calc('+g+'% - 5px);font-family:\'Nunito\',sans-serif;font-size:8px;color:#8a9bb0;line-height:1;white-space:nowrap;">'+fmR(tipMax*g/100)+'</div>';
            });

            // Vertical bar per resource — distinct colours
            var tipPalMap = {
                'materials':       ['#42A5F5','#26C6DA','#66BB6A','#AB47BC','#FFA726','#7E57C2','#26A69A','#5C6BC0','#9CCC65','#78909C'],
                'sub contractors': ['#546E7A','#607D8B','#455A64','#78909C','#37474F','#546E7A','#607D8B','#455A64','#78909C','#37474F'],
                'sub contractor':  ['#546E7A','#607D8B','#455A64','#78909C','#37474F','#546E7A','#607D8B','#455A64','#78909C','#37474F']
            };
            var tipPal = tipPalMap[t.name.toLowerCase().trim()] || ['#FFA726','#FFEE58','#9CCC65','#26C6DA','#42A5F5','#AB47BC','#7E57C2','#26A69A','#8D6E63','#78909C'];
            var tbHtml = '', lblHtml = '';
            t.resources.forEach(function(r, ri){
                var rCol    = tipPal[ri % tipPal.length];
                var planned = r.planned;
                var actual  = r.actual;
                var diff    = actual !== null ? actual - planned : null;
                var dCol    = diff === null ? '' : (diff > 0 ? '#ef5350' : '#66bb6a');
                var barInner = '';

                if (actual === null) {
                    var plPct = Math.max(planned / tipMax * 100, 0.5);
                    var spPct = Math.max(100 - plPct, 0);
                    barInner = '<div style="flex:'+spPct.toFixed(2)+' 1 0;min-height:0;"></div>'
                        + '<div style="flex:'+plPct.toFixed(2)+' 1 0;width:80%;min-height:0;background:'+rCol+';border-radius:3px 3px 0 0;overflow:hidden;"></div>';
                } else if (actual > planned) {
                    var plPct = Math.max(planned / tipMax * 100, 0.5);
                    var exPct = Math.max((actual - planned) / tipMax * 100, 0.5);
                    var spPct = Math.max(100 - plPct - exPct, 0);
                    barInner = '<div style="flex:'+spPct.toFixed(2)+' 1 0;min-height:0;"></div>'
                        + '<div style="flex:'+exPct.toFixed(2)+' 1 0;width:80%;min-height:0;background:#ef5350;border-radius:3px 3px 0 0;overflow:hidden;"></div>'
                        + '<div style="flex:'+plPct.toFixed(2)+' 1 0;width:80%;min-height:0;background:'+rCol+';overflow:hidden;"></div>';
                } else {
                    var acPct  = Math.max(actual / tipMax * 100, 0.5);
                    var savPct = Math.max((planned - actual) / tipMax * 100, 0);
                    var spPct  = Math.max(100 - acPct - savPct, 0);
                    barInner = '<div style="flex:'+spPct.toFixed(2)+' 1 0;min-height:0;"></div>'
                        + '<div style="flex:'+savPct.toFixed(2)+' 1 0;width:80%;min-height:0;background:#66bb6a;border-radius:3px 3px 0 0;overflow:hidden;"></div>'
                        + '<div style="flex:'+acPct.toFixed(2)+' 1 0;width:80%;min-height:0;background:'+rCol+';overflow:hidden;"></div>';
                }

                tbHtml += '<div style="flex:1;min-width:0;display:flex;flex-direction:column;align-items:center;padding:0 3px;">'
                    + barInner + '</div>';

                var fmFull = function(v){ return '&#8377;'+(+v).toFixed(2); };
                var valLine = 'Planned: '+fmFull(planned)
                    + ' &nbsp;&nbsp;Actual: '+(actual !== null ? fmFull(actual) : '—')
                    + (diff !== null ? ' &nbsp;&nbsp;Difference: '+(diff>0?'+':'')+fmFull(diff) : '');
                var dCol = diff === null ? '' : (diff > 0 ? '#ef9a9a' : '#a5d6a7');
                lblHtml += '<div style="padding:4px 2px 6px;border-bottom:1px solid rgba(100,130,170,0.2);margin-bottom:2px;">'
                    + '<div style="display:flex;align-items:center;gap:4px;margin-bottom:2px;">'
                    +   '<span style="display:inline-block;width:9px;height:9px;border-radius:2px;background:'+rCol+';flex-shrink:0;"></span>'
                    +   '<span style="font-family:\'Barlow Condensed\',sans-serif;font-size:10px;font-weight:700;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="'+r.name+'">'+sh(r.name,18)+'</span>'
                    + '</div>'
                    + '<div style="font-family:\'Nunito\',sans-serif;font-size:9px;color:#cfd8e3;padding-left:13px;">Planned: '+fmFull(planned)+'</div>'
                    + '<div style="font-family:\'Nunito\',sans-serif;font-size:9px;color:#fff;padding-left:13px;">Actual: '+(actual !== null ? fmFull(actual) : '—')+'</div>'
                    + (diff !== null ? '<div style="font-family:\'Nunito\',sans-serif;font-size:9px;font-weight:700;color:'+dCol+';padding-left:13px;">Diff: '+(diff>0?'+':'')+fmFull(diff)+'</div>' : '')
                    + '</div>';
            });

            var tipW = Math.max(360, t.resources.length * 72 + 40);
            tipEl.style.width = tipW + 'px';
            tipEl.innerHTML = '<div style="display:flex;align-items:center;gap:7px;margin-bottom:8px;">'
                + '<span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:'+col+';flex-shrink:0;"></span>'
                + '<span style="font-family:\'Barlow Condensed\',sans-serif;font-size:13px;color:#fff;font-weight:700;letter-spacing:.3px;">'+t.name+'</span>'
                + '</div>'
                + '<div style="display:flex;height:200px;">'
                + '<div style="width:30px;position:relative;flex-shrink:0;">'+tsHtml+'</div>'
                + '<div style="flex:1;position:relative;min-width:0;">'+tgHtml
                + '<div style="position:absolute;inset:0;display:flex;align-items:stretch;padding:0 2px;">'+tbHtml+'</div>'
                + '</div></div>'
                + '<div style="margin-top:8px;border-top:1px solid rgba(100,130,170,0.3);padding-top:4px;">'+lblHtml+'</div>';

            var rect = barCol.getBoundingClientRect();
            var left = rect.left + rect.width / 2 - tipW / 2;
            left = Math.max(4, Math.min(left, window.innerWidth - tipW - 4));
            tipEl.style.left = left + 'px';
            tipEl.style.top  = (rect.top - 8) + 'px';
            tipEl.style.transform = 'translateY(-100%)';
            tipEl.style.display = 'block';
        });
        barCol.addEventListener('mouseleave', function(){ tipEl.style.display = 'none'; });
    });
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function sh(str,n){ str=str||''; return str.length>n ? str.substring(0,n-1)+'…' : str; }
function fm(v){ v=+v||0; return Number.isInteger(v)?v:v.toFixed(1); }

// ── Unit shortener — abbreviate lengthy unit names for compact panels ─────────
var UNIT_ABBR = {
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
    'hours':'hrs','hour':'hr',
    'days':'d','day':'d',
    'man days':'MD','man-days':'MD','mandays':'MD',
    'lump sum':'LS','lumpsum':'LS',
    'percentage':'%','percent':'%'
};
function shu(u){
    u = (u || '').trim();
    if (!u) return '';
    var key = u.toLowerCase();
    if (UNIT_ABBR[key]) return UNIT_ABBR[key];
    // "No of Panels" / "Number of Panels" → "Panels"
    var stripped = u.replace(/^(?:no\.?s?|number)\s+of\s+/i, '');
    if (stripped !== u) return shu(stripped);
    return u.length > 8 ? sh(u, 8) : u;
}

function niceAxis(maxVal){
    if (!maxVal) return [0];
    var step = Math.pow(10, Math.floor(Math.log10(maxVal)));
    if (maxVal/step > 5) step *= 2;
    if (maxVal/step > 5) step *= 2.5;
    var ticks = [], t = 0;
    while (ticks.length < 6){
        ticks.push(Math.round(t));
        if (t >= maxVal) break;
        t += step;
    }
    return ticks;
}

// ── Project Duration bar (pd-c2) ──────────────────────────────────────────────
function fmDate(s){
    if (!s) return '';
    var d=new Date(s);
    if (isNaN(d)) return s;
    var mo=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return d.getDate()+' '+mo[d.getMonth()]+' '+d.getFullYear();
}
function renderProjectBar(el, budgeted, actual, label, bEndDate, aEndDate){
    if (!el) return;
    if (!budgeted){
        el.innerHTML='<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No data</div>';
        return;
    }
    var maxVal = Math.max(budgeted, actual, 1);
    var lbl='font-family:\'Barlow Condensed\',sans-serif;font-size:11px;font-weight:700;white-space:nowrap;overflow:hidden;display:flex;align-items:center;justify-content:center;padding:0 4px';
    var html='<div style="display:flex;flex-direction:column;justify-content:center;height:100%;padding:6px 10px;box-sizing:border-box">';
    html+='<div style="font-family:\'Barlow Condensed\',sans-serif;font-size:12px;font-weight:700;color:#1a2540;margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">'+label+'</div>';
    html+='<div style="display:flex;align-items:stretch;height:22px;border-radius:3px;overflow:hidden">';
    if (actual>0 && actual>budgeted){
        var bPct=(budgeted/maxVal*100).toFixed(1);
        var rPct=((actual-budgeted)/maxVal*100).toFixed(1);
        html+='<div style="width:'+bPct+'%;background:#00838f;min-width:3px;'+lbl+';color:#fff">'+budgeted+' d</div>';
        html+='<div style="width:'+rPct+'%;background:#e53935;min-width:3px;'+lbl+';color:#fff">+'+(actual-budgeted)+' d</div>';
    } else if (actual>0 && actual<budgeted){
        var aPct=(actual/maxVal*100).toFixed(1);
        var yPct=((budgeted-actual)/maxVal*100).toFixed(1);
        html+='<div style="width:'+aPct+'%;background:#00838f;min-width:3px;'+lbl+';color:#fff">'+actual+' d</div>';
        html+='<div style="width:'+yPct+'%;background:#f0c419;min-width:3px;'+lbl+';color:#1a2540">-'+(budgeted-actual)+' d</div>';
    } else {
        var boPct=(budgeted/maxVal*100).toFixed(1);
        html+='<div style="width:'+boPct+'%;background:#00838f;min-width:3px;'+lbl+';color:#fff">'+budgeted+' d</div>';
    }
    html+='</div>';
    // Dates + delay row
    var delay = actual>0 ? actual-budgeted : 0;
    html+='<div style="display:flex;justify-content:space-between;align-items:center;margin-top:5px;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;color:#5a6e8c">';
    html+='<span>Budget end: <b style="color:#1a2540">'+(fmDate(bEndDate)||'—')+'</b>';
    if (budgeted) html+=' &nbsp;<b style="color:#1a2540">'+budgeted+' d</b>';
    html+='</span>';
    if (actual>0){
        html+='<span>';
        if (delay>0) html+='<b style="color:#e53935">+'+delay+' d delayed</b> &nbsp;';
        else if (delay<0) html+='<b style="color:#27ae60">'+Math.abs(delay)+' d ahead</b> &nbsp;';
        html+='Projected: <b style="color:#1a2540">'+(fmDate(aEndDate)||'—')+'</b>';
        html+='</span>';
    }
    html+='</div>';
    html+='<div style="display:flex;gap:10px;margin-top:5px;font-family:\'Barlow Condensed\',sans-serif;font-size:10px;color:#5a6e8c">'
        +'<span><span style="display:inline-block;width:10px;height:8px;background:#00838f;margin-right:3px;border-radius:1px"></span>Budgeted</span>'
        +'<span><span style="display:inline-block;width:10px;height:8px;background:#e53935;margin-right:3px;border-radius:1px"></span>Overrun</span>'
        +'<span><span style="display:inline-block;width:10px;height:8px;background:#f0c419;margin-right:3px;border-radius:1px"></span>Ahead</span>'
        +'</div>';
    html+='</div>';
    el.innerHTML=html;
}

// ── CSS horizontal bar chart ──────────────────────────────────────────────────
// onRowClick: optional callback(id) — defaults to loadKpi
function renderBars(containerId, items, onRowClick){
    var el = document.getElementById(containerId);
    if (!el) return;
    items = items||[];

    var maxVal = 0;
    items.forEach(function(r){ maxVal = Math.max(maxVal, r.scheduled+r.delay); });
    if (!maxVal) maxVal = 1;

    var ticks = niceAxis(maxVal);

    var html = '<div class="leg">'
        +'<span><span class="ld" style="background:#607D8B"></span>Normal</span>'
        +'<span><span class="ld" style="background:#00838f"></span>Critical</span>'
        +'<span><span class="ld" style="background:#FF0000"></span>Delay</span>'
        +'</div>';

    items.forEach(function(r){
        var sc = r.scheduled, dl = r.delay;
        var scPct = (sc/maxVal*100).toFixed(1);
        var dlPct = (dl/maxVal*100).toFixed(1);
        var barCol = r.critical ? '#00838f' : '#607D8B';
        var rowCls = 'brow';
        var tipLines = [];
        if (r.start_date)    tipLines.push('Planned Start:  ' + fmtDate(r.start_date));
        if (r.end_date)      tipLines.push('Planned End:    ' + fmtDate(r.end_date));
        if (r.proj_end_date) tipLines.push('Projected End:  ' + fmtDate(r.proj_end_date));
        else                 tipLines.push('Actual End:     ' + (r.actual_end_date ? fmtDate(r.actual_end_date) : '—'));
        if (r.duration_days) tipLines.push('Planned Dur:    ' + r.duration_days + ' days');
        if (r.startDelayed)  tipLines.push('Start Delay:    ' + r.startDelayDays + ' days');
        else if (dl > 0)     tipLines.push('Delay:          ' + dl + ' days');
        var tipAttr = tipLines.length ? ' data-tip="' + tipLines.join('&#10;') + '"' : '';
        var dispVal = sc > 0 ? (String(sc) + (dl > 0 ? '<span style="color:#FF0000;margin-left:3px;">+' + dl + '</span>' : '')) : '';
        html += '<div class="'+rowCls+'"'+tipAttr+' '+(r.id?'data-aid="'+r.id+'" style="cursor:pointer;display:flex;align-items:center;"':'style="display:flex;align-items:center;"')+'>'
            +'<div class="blbl" style="color:#000;" title="'+r.name+'">'+sh(r.name,30)+'</div>'
            +'<div class="btrk" style="flex:1;">'
            +(sc>0?'<div class="bs" style="width:'+scPct+'%;background:'+barCol+'"></div>':'')
            +(dl>0?'<div class="bs" style="width:'+dlPct+'%;background:#FF0000"></div>':'')
            +'</div>'
            +'<div style="font-size:11px;color:#000;font-weight:700;min-width:38px;text-align:right;padding-left:5px;white-space:nowrap;">'+dispVal+'</div>'
            +'</div>';
    });

    html += '<div class="baxis">';
    ticks.forEach(function(t){ html += '<span>'+t+'</span>'; });
    html += '</div>';
    el.innerHTML = html;

    $(el).find('.brow[data-aid]').on('click', function(){
        var id = $(this).data('aid');
        if (onRowClick) onRowClick(id);
        else loadKpi(id);
    });
}

// ── Work Done — custom semicircular gauge with Start/Complete/Achieved ────────
function doWorkDone(k){
    var el = document.getElementById('pd-g1');
    if (!el) return;
    var tq  = +k.target_qty    || 0;
    var aq  = +k.actual_qty    || 0;
    var pct = +k.work_done_pct || 0;
    var u   = shu(k.unit);
    var an  = sh(k.activity_name || '', 38);
    var f   = tq > 0 ? Math.max(0, Math.min(1, aq / tq)) : 0;
    var cx=105, cy=92, r=76, sw=14;

    function ptF(frac){ var a=Math.PI*(1-frac); return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)]; }
    function arc(f1,f2,col,cap){
        if(f2<=f1) return ''; cap=cap||'butt';
        var p1=ptF(f1),p2=ptF(f2);
        if((f2-f1)>=1){ var pm=ptF(0.5);
            return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
        }
        return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
    }

    var nr=r-15, na=Math.PI*(1-f);
    var nx=(cx+nr*Math.cos(na)).toFixed(1), ny=(cy-nr*Math.sin(na)).toFixed(1);

    var svg='<svg width="210" height="130" viewBox="0 0 210 130" xmlns="http://www.w3.org/2000/svg">'
        +arc(0,1,'#a8d4f5')
        +(f>0?arc(0,f,'#0d1f6e','butt'):'')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#a8d4f5"/>'
        +'<text x="'+(cx-r)+'" y="'+(cy+15)+'" text-anchor="middle" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Start</text>'
        +'<text x="'+(cx+r)+'" y="'+(cy+15)+'" text-anchor="middle" font-size="13" fill="#111" font-family="Barlow Condensed,Arial">Complete '+fm(tq)+(u?' '+u:'')+'</text>'
        +'<text x="'+cx+'" y="'+(cy-26)+'" text-anchor="middle" font-size="18" font-weight="700" fill="#111" font-family="Barlow Condensed,Arial">'+fm(aq)+' '+u+' | '+pct+'%</text>'
        +'<text x="'+cx+'" y="'+(cy-12)+'" text-anchor="middle" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">Achieved</text>'
        +(an?'<text x="'+cx+'" y="128" text-anchor="middle" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +'</svg>';

    el.innerHTML = svg;
}

function doTargetProduction(k){
    var el = document.getElementById('pd-g2');
    if (!el) return;
    var tq  = +k.target_qty    || 0;   // Schedule Quantity = full arch/dial
    var aq  = +k.actual_qty    || 0;   // Last reported quantity = dark blue arc
    var dur = +k.b_duration    || 0;   // B. Duration (old_duration)
    var u   = shu(k.unit);
    var an  = sh(k.activity_name || '', 38);

    // Target to date = Elapsed days × (Schedule Qty / B. Duration)
    var asd = k.act_start_date     || '';
    var lrd = k.last_reported_date || '';
    var elapsedDays  = (asd && lrd) ? Math.max(0, (new Date(lrd) - new Date(asd)) / 86400000) : 0;
    var compTarget   = (dur > 0 && tq > 0) ? Math.min(tq, elapsedDays * (tq / dur)) : 0;
    var fActual = tq > 0 ? Math.max(0, Math.min(1, aq / tq)) : 0;
    var fTarget = tq > 0 ? Math.max(0, Math.min(1, compTarget / tq)) : 0;

    var cx=105, cy=92, r=76, sw=14;

    function ptF(frac){ var a=Math.PI*(1-frac); return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)]; }
    function arc(f1,f2,col,cap){
        if(f2<=f1) return ''; cap=cap||'butt';
        var p1=ptF(f1),p2=ptF(f2);
        if((f2-f1)>=1){ var pm=ptF(0.5);
            return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
        }
        return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
    }

    var nr=r-15, na=Math.PI*(1-fTarget);
    var nx=(cx+nr*Math.cos(na)).toFixed(1), ny=(cy-nr*Math.sin(na)).toFixed(1);

    var svg='<svg width="100%" height="100%" viewBox="0 0 210 134" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet" style="display:block;width:100%;height:auto;">'
        +arc(0,1,'#a8aeb8')
        +(fActual>0?arc(0,fActual,'#0d1f6e','butt'):'')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#a8aeb8"/>'
        +(tq>0?'<text x="105" y="74" text-anchor="middle" font-size="18" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+fm(tq)+(u?' '+u:'')+'</text>':'')
        +'<text x="8" y="112" text-anchor="start" font-size="13" fill="#111" font-family="Barlow Condensed,Arial">Actual <tspan font-weight="700">'+fm(aq)+(u?' '+u:'')+'</tspan></text>'
        +'<text x="202" y="112" text-anchor="end" font-size="13" fill="#111" font-family="Barlow Condensed,Arial">Target <tspan font-weight="700">'+fm(compTarget)+(u?' '+u:'')+'</tspan></text>'
        +(an?'<text x="105" y="128" text-anchor="middle" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +'</svg>';

    el.style.flexDirection='';
    el.style.alignItems='';
    el.innerHTML = svg;
}

// ── Productivity gauge ────────────────────────────────────────────────────────
function doProductivity(k) {
    var el = document.getElementById('pd-g3');
    if (!el) return;
    var tp  = +k.target_productivity || 0;
    var ap  = +k.actual_productivity || 0;
    var u   = shu(k.unit);
    var an  = sh(k.activity_name || '', 38);
    var maxVal = tp > 0 ? tp * 2 : 1;
    var f   = Math.max(0, Math.min(1, ap / maxVal));
    var cx=105, cy=92, r=76, sw=14;

    function ptF(frac){ var a=Math.PI*(1-frac); return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)]; }
    function arc(f1,f2,col){
        if(f2<=f1) return '';
        var p1=ptF(f1),p2=ptF(f2);
        return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="butt"/>';
    }

    var nr=r-15, na=Math.PI*(1-f);
    var nx=(cx+nr*Math.cos(na)).toFixed(1), ny=(cy-nr*Math.sin(na)).toFixed(1);

    var svg='<svg width="210" height="138" viewBox="0 0 210 138" xmlns="http://www.w3.org/2000/svg">'
        +arc(0, 0.5, '#E57373')
        +arc(0.5, 1,  '#81C784')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#dce3ef"/>'
        +'<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Actual <tspan font-weight="700">'+fm(ap)+(u?' '+u+'/d':'')+'</tspan></text>'
        +'<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Target <tspan font-weight="700">'+fm(tp)+(u?' '+u+'/d':'')+'</tspan></text>'
        +'<text x="'+cx+'" y="'+(cy-18)+'" text-anchor="middle" font-size="18" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+fm(ap)+(u?' '+u+'/d':'')+'</text>'
        +(an?'<text x="'+cx+'" y="135" text-anchor="middle" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +'</svg>';

    el.innerHTML = svg;

    // Inject "Tasks" chip into white body area (top-right of .gp)
    var old = el.querySelector('.pd-tasks-chip');
    if (old) old.remove();
    var chip = document.createElement('span');
    chip.className = 'pd-tasks-chip';
    chip.textContent = 'Tasks';
    el.appendChild(chip);
    chip.addEventListener('mouseenter', function() { pdShowTasksTip(k.tasks, chip); });
    chip.addEventListener('mouseleave', function() { pdHideTipSoon(); });
}

// ── Tasks tooltip helpers ─────────────────────────────────────────────────────
var _pdTipTimer = null;
function pdGetTip() {
    var tip = document.getElementById('pd-tasks-tip');
    if (!tip) {
        tip = document.createElement('div');
        tip.id = 'pd-tasks-tip';
        var modal = document.getElementById('pd-modal');
        (modal || document.body).appendChild(tip);
        tip.addEventListener('mouseenter', function() { clearTimeout(_pdTipTimer); });
        tip.addEventListener('mouseleave', function() { pdHideTipSoon(); });
    }
    return tip;
}
function pdShowTasksTip(items, anchor, mode) {
    clearTimeout(_pdTipTimer);
    items = items || [];
    var isDuration = mode === 'duration';
    var valKey = isDuration ? 'planned_duration' : 'val';
    var actKey = isDuration ? 'actual_duration'  : 'actual';
    var title  = isDuration ? 'Task Duration' : 'Task Productivity';
    var tgtLbl = isDuration ? 'Planned' : 'Target';
    var overCol  = isDuration ? '#ef5350' : '#66bb6a';
    var underCol = isDuration ? '#f0c419' : '#ef5350';
    var underBarCol = isDuration ? 'rgba(240,196,25,.3)' : 'rgba(239,83,80,.3)';
    var fmtNum = isDuration ? function(v){ return (+v||0).toFixed(4); } : fm;
    var tip = pdGetTip();
    var cols = ['#d4845a','#f0c419','#8fa3bc','#7c5cbf','#3461b8','#27afc4','#ec407a','#26a69a'];
    var bars = '', taskRows = '';
    var segPct = function(v, tot) { return tot > 0 ? (v / tot * 100).toFixed(1) + '%' : '0%'; };
    items.forEach(function(r, i) {
        var tgt = +(r[valKey]) || 0, act = +(r[actKey]) || 0;
        var col = cols[i % cols.length];
        var u = isDuration ? ' d' : (r.unit ? ' ' + shu(r.unit) : '');
        var isOver  = act > 0 && act > tgt;
        var isUnder = act > 0 && act < tgt;
        var actCol  = isOver ? overCol : (isUnder ? underCol : '#e8f0fc');
        if (isOver) {
            bars += '<div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;height:100%;">'
                + '<div style="height:' + segPct(act - tgt, act) + ';background:' + overCol + ';border-radius:3px 3px 0 0;min-height:3px;"></div>'
                + '<div style="height:' + segPct(tgt, act) + ';background:' + col + ';min-height:4px;"></div>'
                + '</div>';
        } else if (isUnder) {
            bars += '<div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;height:100%;">'
                + '<div style="height:' + segPct(tgt - act, tgt) + ';background:' + underBarCol + ';border-radius:3px 3px 0 0;min-height:3px;"></div>'
                + '<div style="height:' + segPct(act, tgt) + ';background:' + col + ';min-height:4px;"></div>'
                + '</div>';
        } else {
            bars += '<div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;height:100%;">'
                + '<div style="height:100%;background:' + col + ';border-radius:3px 3px 0 0;min-height:4px;"></div>'
                + '</div>';
        }
        taskRows += '<tr>'
            + '<td style="white-space:nowrap;">'
            +   '<span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:' + col + ';margin-right:10px;vertical-align:middle;"></span>'
            +   sh(r.name || '', 35)
            + '</td>'
            + '<td style="text-align:right;font-weight:700;color:#e8f0fc;white-space:nowrap;">' + fmtNum(tgt) + u + '</td>'
            + '<td style="text-align:right;font-weight:700;color:' + actCol + ';white-space:nowrap;">' + (act > 0 ? fmtNum(act) + u : '—') + '</td>'
            + '</tr>';
    });
    if (!items.length) {
        tip.innerHTML = '<div class="tip-title">' + title + '</div><div style="font-size:17px;color:#aaa;padding:20px 0;text-align:center">No task data</div>';
    } else {
        tip.innerHTML = '<div class="tip-title">' + title + '</div>'
            + '<div style="display:flex;gap:8px;align-items:flex-end;height:90px;margin-bottom:10px;padding-bottom:4px;border-bottom:1px solid rgba(255,255,255,.12);">' + bars + '</div>'
            + '<table>'
            +   '<thead><tr>'
            +     '<th style="text-align:left;">Task</th>'
            +     '<th style="text-align:right;">' + tgtLbl + '</th>'
            +     '<th style="text-align:right;">Actual</th>'
            +   '</tr></thead>'
            +   '<tbody>' + taskRows + '</tbody>'
            + '</table>';
    }
    var gp = anchor.closest ? anchor.closest('.gp') : null;
    var gpRect = gp ? gp.getBoundingClientRect() : anchor.getBoundingClientRect();
    var tipW = Math.round(gpRect.width * 0.82);
    tip.style.width = tipW + 'px';
    tip.style.display = 'block';
    var tipH = tip.offsetHeight;
    tip.style.left = Math.max(4, Math.round(gpRect.left) - tipW) + 'px';
    tip.style.top  = Math.max(4, Math.round(gpRect.top) - tipH - 8) + 'px';
}
function pdHideTipSoon() {
    _pdTipTimer = setTimeout(function() {
        var tip = document.getElementById('pd-tasks-tip');
        if (tip) tip.style.display = 'none';
    }, 150);
}

// ── Cycle Time gauge ──────────────────────────────────────────────────────────
function doCycleTime(k) {
    var el = document.getElementById('pd-g4');
    if (!el) return;
    var tc  = +k.target_cycle_time || 0;
    var ac  = +k.actual_cycle_time || 0;
    var an  = sh(k.activity_name || '', 28);
    var maxVal = tc > 0 ? tc * 2 : 1;
    var f   = Math.max(0, Math.min(1, ac / maxVal));
    var cx=105, cy=92, r=76, sw=14;

    function ptF(frac){ var a=Math.PI*(1-frac); return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)]; }
    function arc(f1,f2,col){
        if(f2<=f1) return '';
        var p1=ptF(f1),p2=ptF(f2);
        return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="butt"/>';
    }

    var nr=r-15, na=Math.PI*(1-f);
    var nx=(cx+nr*Math.cos(na)).toFixed(1), ny=(cy-nr*Math.sin(na)).toFixed(1);

    var svg='<svg width="210" height="138" viewBox="0 0 210 138" xmlns="http://www.w3.org/2000/svg">'
        +arc(0, 0.5, '#00838f')
        +arc(0.5, 1,  '#FF6D00')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#dce3ef"/>'
        +'<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Actual <tspan font-weight="700">'+fm(ac)+' d</tspan></text>'
        +'<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Target <tspan font-weight="700">'+fm(tc)+' d</tspan></text>'
        +'<text x="'+cx+'" y="'+(cy-18)+'" text-anchor="middle" font-size="18" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+fm(ac)+' Days</text>'
        +(an?'<text x="'+cx+'" y="135" text-anchor="middle" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +'</svg>';

    el.innerHTML = svg;

    // Inject "Task" chip into white body area (top-right of .gp)
    var old = el.querySelector('.pd-tasks-chip');
    if (old) old.remove();
    var chip = document.createElement('span');
    chip.className = 'pd-tasks-chip';
    chip.textContent = 'Task';
    el.appendChild(chip);
    chip.addEventListener('mouseenter', function() { pdShowTasksTip(k.tasks, chip, 'duration'); });
    chip.addEventListener('mouseleave', function() { pdHideTipSoon(); });
}

// ── Capacity Utilisation gauge ────────────────────────────────────────────────
function doCapacity(k) {
    var el = document.getElementById('pd-g5');
    if (!el) return;
    var maxVal = +k.cap_max  || 0;
    var used   = +k.cap_used || 0;
    var an     = sh(k.activity_name || '', 38);
    var f      = maxVal > 0 ? Math.max(0, Math.min(1, used / maxVal)) : 0;
    var cx=105, cy=92, r=76, sw=14;

    function ptF(frac){ var a=Math.PI*(1-frac); return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)]; }
    function arc(f1,f2,col,cap){
        if(f2<=f1) return ''; cap=cap||'butt';
        var p1=ptF(f1),p2=ptF(f2);
        if((f2-f1)>=1){ var pm=ptF(0.5);
            return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
        }
        return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
    }

    var nr=r-15, na=Math.PI*(1-f);
    var nx=(cx+nr*Math.cos(na)).toFixed(1), ny=(cy-nr*Math.sin(na)).toFixed(1);

    var svg='<svg width="210" height="138" viewBox="0 0 210 138" xmlns="http://www.w3.org/2000/svg">'
        +arc(0, 1, '#FFD700')
        +(f>0 ? arc(0, f, '#90EE90', 'butt') : '')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#FFD700"/>'
        +'<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Used <tspan font-weight="700">'+fm(used)+' h</tspan></text>'
        +'<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Max <tspan font-weight="700">'+fm(maxVal)+' h</tspan></text>'
        +'<text x="'+cx+'" y="'+(cy-18)+'" text-anchor="middle" font-size="22" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+(maxVal>0?((used/maxVal)*100).toFixed(1):0)+'%</text>'
        +(an?'<text x="'+cx+'" y="135" text-anchor="middle" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +'</svg>';

    el.innerHTML = svg;
}

// ── Activity Duration bar (pd-g6) ─────────────────────────────────────────────
function doActivityDuration(k) {
    var el = document.getElementById('pd-g6');
    if (!el) return;
    var bDur     = +k.b_duration || +k.duration || 0;   // old_duration, fall back to duration
    var aDur     = +k.projected_duration || 0;
    var elapsed  = +k.elapsed            || 0;
    var wDone    = +(+k.work_done_pct || 0).toFixed(1);
    var wRemain  = Math.max(0, +(100 - wDone).toFixed(1));
    var baseCol  = k.critical ? '#00838f' : '#607D8B';  // blue if critical, dark grey otherwise

    if (!bDur) {
        el.innerHTML = '<div style="font-size:11px;color:#aaa;text-align:center;padding-top:20px">No duration data</div>';
        return;
    }
    if (!aDur) aDur = bDur;

    var maxDur    = Math.max(bDur, aDur, 1);
    var remaining = Math.max(0, aDur - elapsed);
    var isOver    = aDur > bDur;
    var isUnder   = bDur > aDur;
    var seg = 'font-family:\'Barlow Condensed\',sans-serif;font-size:11px;font-weight:700;white-space:nowrap;overflow:hidden;display:flex;align-items:center;justify-content:center;padding:0 4px';
    var pct = function(v){ return (Math.min(Math.max(v, 0), maxDur) / maxDur * 100).toFixed(2) + '%'; };
    var fam = "font-family:'Barlow Condensed',sans-serif;";
    var row = function(l, v, col) {
        return '<div style="display:flex;justify-content:space-between;' + fam + 'font-size:12px;color:#334;padding:1px 4px">'
            + '<span>' + l + '</span>'
            + '<span style="font-weight:700;color:' + (col || '#1a2540') + '">' + v + '</span>'
            + '</div>';
    };
    var divider = '<div style="border-top:1px solid #d0d8e8;margin:3px 4px"></div>';

    // Single bar: base colour (grey or blue) + overrun (red) or slack (yellow), dark overlay for elapsed
    var bar = '<div style="position:relative;display:flex;align-items:stretch;height:22px;border-radius:3px;overflow:hidden;">';
    if (isOver) {
        bar += '<div style="width:' + (bDur/maxDur*100).toFixed(1) + '%;background:' + baseCol + ';min-width:3px;' + seg + '"></div>';
        bar += '<div style="width:' + ((aDur-bDur)/maxDur*100).toFixed(1) + '%;background:#e53935;min-width:3px;' + seg + '"></div>';
    } else if (isUnder) {
        bar += '<div style="width:' + (aDur/maxDur*100).toFixed(1) + '%;background:' + baseCol + ';min-width:3px;' + seg + '"></div>';
        bar += '<div style="width:' + ((bDur-aDur)/maxDur*100).toFixed(1) + '%;background:#f0c419;min-width:3px;' + seg + '"></div>';
    } else {
        bar += '<div style="width:100%;background:' + baseCol + ';' + seg + '"></div>';
    }
    bar += '<div style="position:absolute;left:0;top:0;bottom:0;width:' + pct(elapsed) + ';background:#0d3b8e;opacity:0.55;pointer-events:none"></div>';
    bar += '</div>';

    var actName = k.activity_name || '';
    el.innerHTML =
        '<div style="display:flex;flex-direction:column;justify-content:center;height:100%;padding:6px 10px;box-sizing:border-box">'
        + (actName ? '<div style="' + fam + 'font-size:12px;font-weight:700;color:#1a2540;margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + actName + '</div>' : '')
        + bar
        + '<div style="display:flex;justify-content:space-between;' + fam + 'font-size:11px;color:#5a6e8c;margin-top:3px">'
        + '<span>Planned: <b style="color:#1a2540">' + bDur + ' d</b></span>'
        + (aDur !== bDur ? '<span>Projected: <b style="color:' + (isOver ? '#e53935' : '#27ae60') + '">' + aDur + ' d</b></span>' : '')
        + '</div>'
        + divider
        + row('Elapsed',        elapsed   + ' days', '#0d3b8e')
        + row('Remaining',      remaining + ' days', '#546e7a')
        + divider
        + row('Work done',      wDone   + '%', '#27ae60')
        + row('Remaining work', wRemain + '%', '#e67e22')
        + '</div>';
}

// ── KPI render ────────────────────────────────────────────────────────────────
function doKpi(k){
    var u = k.unit||'', an = sh(k.activity_name||'',38);

    doActivityDuration(k);
    doWorkDone(k);
    doTargetProduction(k);
    doProductivity(k);
    doCycleTime(k);
    doCapacity(k);
    doTaskQty(k.tasks);
    doRes(k.tasks);
}

// ── SVG Needle Gauge ──────────────────────────────────────────────────────────
function gauge(gwId, val, maxVal, trackStyle, targetFrac, lbl1, v1, lbl2, v2, actName){
    var el = document.getElementById(gwId);
    if (!el) return;
    val=+val||0; maxVal=+maxVal||1;
    var f = Math.max(0, Math.min(1, val/maxVal));
    var cx=105, cy=92, r=76, sw=14;

    function ptF(frac){
        var a = Math.PI*(1-frac);
        return [(cx+r*Math.cos(a)).toFixed(1), (cy-r*Math.sin(a)).toFixed(1)];
    }
    function arc(f1,f2,col,cap){
        if (f2<=f1) return '';
        cap = cap||'butt';
        var p1=ptF(f1), p2=ptF(f2);
        if ((f2-f1)>=1){
            var pm=ptF(0.5);
            return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+
                   ' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+
                   '" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
        }
        return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+
               '" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
    }

    var trackSvg = (trackStyle==='flat')
        ? arc(0,1,'#a8d4f5')
        : arc(0,0.5,'#8B0000')+arc(0.5,1,'#81C784');

    var tickSvg = '';
    if (targetFrac && targetFrac>0 && targetFrac<=1){
        var ta = Math.PI*(1-targetFrac);
        var tx1=(cx+(r-10)*Math.cos(ta)).toFixed(1), ty1=(cy-(r-10)*Math.sin(ta)).toFixed(1);
        var tx2=(cx+(r+10)*Math.cos(ta)).toFixed(1), ty2=(cy-(r+10)*Math.sin(ta)).toFixed(1);
        tickSvg='<line x1="'+tx1+'" y1="'+ty1+'" x2="'+tx2+'" y2="'+ty2+'" stroke="#c0392b" stroke-width="3.5"/>';
    }

    var nr=r-15, na=Math.PI*(1-f);
    var nx=(cx+nr*Math.cos(na)).toFixed(1), ny=(cy-nr*Math.sin(na)).toFixed(1);
    var fillCol = (trackStyle==='flat') ? '#0d1f6e' : '#1a3a6b';
    var dotCol  = (trackStyle==='flat') ? '#a8d4f5' : '#dce3ef';

    var lblSvg='';
    if (lbl1) lblSvg+='<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">'+lbl1+' <tspan font-weight="700">'+v1+'</tspan></text>';
    if (lbl2) lblSvg+='<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">'+lbl2+' <tspan font-weight="700">'+v2+'</tspan></text>';
    var anSvg = actName ? '<text x="'+cx+'" y="135" text-anchor="middle" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">'+actName+'</text>' : '';

    var svg='<svg width="210" height="138" viewBox="0 0 210 138" xmlns="http://www.w3.org/2000/svg">'
        +trackSvg
        +(f>0?arc(0,f,fillCol,'round'):'')
        +tickSvg
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="'+dotCol+'"/>'
        +'<text x="'+(cx-r)+'" y="'+(cy+15)+'" text-anchor="middle" font-size="12" fill="#5a6e8c" font-family="Barlow Condensed,Arial">0</text>'
        +'<text x="'+(cx+r)+'" y="'+(cy+15)+'" text-anchor="middle" font-size="12" fill="#5a6e8c" font-family="Barlow Condensed,Arial">'+fm(maxVal)+'</text>'
        +'<text x="'+cx+'" y="'+(cy-18)+'" text-anchor="middle" font-size="22" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+fm(val)+'</text>'
        +lblSvg+anSvg
        +'</svg>';

    el.innerHTML = svg;
}

// ── Resource Capacity — task quantities ───────────────────────────────────────
function doTaskQty(items){
    var el=document.getElementById('pd-c6'); if(!el) return;
    var cols=['#d4845a','#f0c419','#8fa3bc','#7c5cbf','#3461b8','#27afc4','#ec407a','#26a69a'];
    items=items||[];
    if (!items.length){
        el.innerHTML='<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No data</div>';
        return;
    }
    var maxVal=0;
    items.forEach(function(r){ maxVal=Math.max(maxVal,+(r.qty)||0); });
    if (!maxVal) maxVal=1;

    var topRow='', bars='', labels='', vals='';
    items.forEach(function(r,i){
        var val=+(r.qty)||0;
        var u=r.unit?' '+shu(r.unit):'';
        var pct=(val/maxVal*100).toFixed(1)+'%';
        topRow+='<div style="flex:1;text-align:center;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;font-weight:700;color:#1a2540">'+fm(val)+u+'</div>';
        bars+='<div class="rescol" style="height:'+pct+'">'
            +'<div class="resb" style="height:100%;min-height:3px;background:'+cols[i%cols.length]+'"></div>'
            +'</div>';
        labels+='<div class="reslbl" style="color:#111">'+sh(r.name||'',24)+'</div>';
        vals+='<div class="reslbl" style="font-weight:700;color:#1a2540">'+fm(val)+u+'</div>';
    });
    el.innerHTML='<div style="display:flex;gap:4px;padding:0 0 2px;flex-shrink:0">'+topRow+'</div>'
        +'<div class="resbars">'+bars+'</div>'
        +'<div class="reslabels">'+labels+'</div>'
        +'<div class="reslabels">'+vals+'</div>'
        +'<div class="resfoot">Tasks</div>';
}

// ── Cause of Delay ────────────────────────────────────────────────────────────
function doCod(items){
    var cv=document.getElementById('pd-c6'); if(!cv) return;
    if (_ch['pd-c6']) { _ch['pd-c6'].destroy(); delete _ch['pd-c6']; }
    var cols=['#3461b8','#e74c3c','#2ecc71','#f39c12','#9b59b6','#1abc9c','#e67e22','#16a085'];
    var lb=[], dt=[];
    (items||[]).forEach(function(r){ lb.push(r.name); dt.push(+r.count||0); });
    if (!lb.length){ lb=['No data']; dt=[1]; cols=['#dde']; }
    _ch['pd-c6'] = new Chart(cv, {
        type:'doughnut',
        data:{labels:lb, datasets:[{data:dt, backgroundColor:cols.slice(0,lb.length), borderWidth:2, borderColor:'#fff'}]},
        options:{
            responsive:true, maintainAspectRatio:false, animation:{duration:300},
            plugins:{
                legend:{position:'right', labels:{font:{size:8}, boxWidth:8, padding:4}},
                tooltip:{callbacks:{label:function(c){
                    var t=c.dataset.data.reduce(function(a,b){return a+b},0);
                    return ' '+c.label+': '+c.parsed+' ('+Math.round(c.parsed/t*100)+'%)';
                }}}
            }
        }
    });
}

// ── Task Productivity ─────────────────────────────────────────────────────────
function doRes(items){
    var el=document.getElementById('pd-c7'); if(!el) return;
    var cols=['#d4845a','#f0c419','#8fa3bc','#7c5cbf','#3461b8','#27afc4','#ec407a','#26a69a'];
    items=items||[];
    if(!items.length){
        el.innerHTML='<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No data</div>';
        return;
    }
    var actRow='', bars='', labels='', tgts='';
    items.forEach(function(r,i){
        var tgt=+(r.val)||0, act=+(r.actual)||0;
        var col=cols[i%cols.length];
        var u=r.unit?' '+shu(r.unit):'';
        var segPct=function(v,tot){ return tot>0?(v/tot*100).toFixed(1)+'%':'0%'; };
        actRow+='<div style="flex:1;text-align:center;font-family:\'Barlow Condensed\',sans-serif;font-size:11px;font-weight:700;color:#1a2540">'+(act>0?fm(act)+u:'')+'</div>';
        if(act>0 && act>tgt){
            bars+='<div class="rescol" style="height:100%">'
                +'<div style="height:'+segPct(act-tgt,act)+';width:100%;min-height:2px;background:#43a047;border-radius:2px 2px 0 0"></div>'
                +'<div class="resb" style="height:'+segPct(tgt,act)+';min-height:3px;background:'+col+'"></div>'
                +'</div>';
        } else if(act>0 && act<tgt){
            bars+='<div class="rescol" style="height:100%">'
                +'<div style="height:'+segPct(tgt-act,tgt)+';width:100%;min-height:2px;background:#e53935;border-radius:2px 2px 0 0;opacity:0.55"></div>'
                +'<div class="resb" style="height:'+segPct(act,tgt)+';min-height:3px;background:'+col+'"></div>'
                +'</div>';
        } else {
            bars+='<div class="rescol" style="height:100%">'
                +'<div class="resb" style="height:100%;min-height:3px;background:'+col+'"></div>'
                +'</div>';
        }
        labels+='<div class="reslbl" style="color:#111">'+sh(r.name||'',24)+'</div>';
        tgts+='<div class="reslbl" style="font-weight:700;color:#1a2540">'+fm(tgt)+u+'</div>';
    });
    el.innerHTML='<div style="display:flex;gap:4px;padding:0 0 2px;flex-shrink:0">'+tgts+'</div>'
        +'<div class="resbars">'+bars+'</div>'
        +'<div class="reslabels">'+labels+'</div>'
        +'<div style="display:flex;gap:4px;padding:2px 0 0;flex-shrink:0">'+actRow+'</div>'
        +'<div class="resfoot">Tasks</div>';
}

})();
