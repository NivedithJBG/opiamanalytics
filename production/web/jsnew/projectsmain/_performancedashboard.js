/* Performance Dashboard -- popup modal */
(function(){
'use strict';

var _ch = {};
var _loaded = false;
var _groups    = [];
var _iow_items = [];
var _all       = [];
var _cdCostMap = {};

var _months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
function fmtDate(d){
    if (!d || d === '0000-00-00') return '--';
    var p = d.split('-');
    return p[2] + ' ' + (_months[parseInt(p[1],10)-1]||'') + ' ' + p[0];
}

$(document).on('mouseenter', '#pd-modal .blbl[data-tip], #cd-modal .blbl[data-tip]', function(e){
    var $t = $('#pd-tip');
    if (!$t.length) $t = $('<div id="pd-tip"></div>').appendTo('body');
    $t.text($(this).attr('data-tip')).css({display:'block',left:e.clientX+14,top:e.clientY+14});
}).on('mousemove', '#pd-modal .blbl[data-tip], #cd-modal .blbl[data-tip]', function(e){
    var $t = $('#pd-tip');
    var x = e.clientX + 14, y = e.clientY + 14;
    if (x + $t.outerWidth() + 10 > window.innerWidth) x = e.clientX - $t.outerWidth() - 6;
    $t.css({left:x, top:y});
}).on('mouseleave', '#pd-modal .blbl[data-tip], #cd-modal .blbl[data-tip]', function(){
    $('#pd-tip').hide();
});

$(document).on('click', '.perf-dashboard-btn', function(e){
    e.preventDefault();
    $('#pd-modal, #pd-bk').addClass('pd-open');
    if (!_loaded) { loadAll(); _loaded = true; }
});
$(document).on('click', '#pd-close, #pd-bk', function(){
    $('#pd-modal, #pd-bk').removeClass('pd-open');
    $('#pd-tip').hide();
});

// Cost Dashboard
var _cdLoaded = false;
var _cdProjectName = '';

var _resTypeColours = {
    'materials':         '#3E4A5C',
    'purchased inputs':  '#9E9E9E',
    'consumables':       '#80CBC4',
    'tools and tackles': '#607D8B',
    'sub contractors':   '#78909C',
    'sub contractor':    '#78909C'
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
        var done1 = false, done2 = false;
        var tryRender = function(){ if (done1 && done2) renderCdBars(); };

        if (_loaded) {
            done1 = true;
        } else {
            $.ajax({ type:'POST', url:'../projectsmain/performancedashboard', dataType:'json',
                success: function(d){
                    if (d) {
                        if (!_groups.length)    _groups    = d.iow_groups  || [];
                        if (!_iow_items.length) _iow_items = d.iow_items   || [];
                        if (!_all.length)       _all       = d.activities  || [];
                        if (!_cdProjectName)    _cdProjectName = d.project_name || '';
                    }
                },
                complete: function(){ done1 = true; tryRender(); }
            });
        }

        $.ajax({ type:'POST', url:'../projectsmain/costdashboardbatch', dataType:'json',
            success: function(d){
                if (d && d.data) _cdCostMap = d.data;
            },
            complete: function(){ done2 = true; tryRender(); }
        });

        if (done1) tryRender();
    }
});
$(document).on('click', '#cd-close, #cd-bk', function(){
    $('#cd-modal, #cd-bk').removeClass('cd-open');
});
$(document).on('click', '#cd-c4 .brow[data-aid]', function(){
    loadCdActivityData($(this).data('aid'));
});

function renderCdBars(){
    renderCdProjectBar();
    renderCdGroupBars();
    renderCdIowBars();
    renderCdActivityBars(_all || []);
    // Auto-load metrics for first activity of first IOW
    var firstIow = _iow_items && _iow_items[0];
    if (firstIow) {
        var firstAct = (_all || []).find(function(a){ return String(a.scheduleitem_id) === String(firstIow.id); });
        if (firstAct) {
            // Highlight the first IOW row
            setTimeout(function(){
                var iowEl = document.getElementById('cd-c3');
                if (iowEl) {
                    var firstRow = iowEl.querySelector('.cd-iow-row');
                    if (firstRow) firstRow.style.background = '#dbeafe';
                }
                // Highlight the first activity row
                var actEl = document.getElementById('cd-c4');
                if (actEl) {
                    var firstActRow = actEl.querySelector('.brow[data-aid="' + firstAct.id + '"]');
                    if (firstActRow) firstActRow.classList.add('brow-active');
                }
                loadCdActivityData(firstAct.id);
            }, 0);
        }
    }
}

function renderCdActivityBars(acts){
    var el = document.getElementById('cd-c4');
    if (!el) return;
    if (!acts.length) {
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No activities</div>';
        return;
    }
    // Max value = highest actual cost (may exceed est) or est, whichever bigger
    var maxVal = 0;
    acts.forEach(function(a){
        var c = _cdCostMap[String(a.id)] || {};
        var est  = +c.est  || 0;
        var acoa = +c.acoa || 0;
        var top  = acoa > est ? acoa : est;
        if (top > maxVal) maxVal = top;
    });
    var html = '';
    acts.forEach(function(a){
        var c    = _cdCostMap[String(a.id)] || {};
        var est  = +c.est  || 0;
        var acoa = +c.acoa || 0;
        var hasAct = acoa > 0;
        var diff = acoa - est;
        var over = diff > 0;

        // Est bar width as % of bar track
        var estPct  = maxVal > 0 ? Math.max(2, est  / maxVal * 96) : 2;

        var barHtml;
        if (!hasAct || diff === 0) {
            // Only est bar in slate
            barHtml = '<div style="position:absolute;left:0;top:0;width:' + estPct.toFixed(1) + '%;height:11px;background:#64748b;border-radius:2px;min-width:4px"></div>';
        } else if (over) {
            // Slate up to est width, orange extension for diff
            var actPct  = maxVal > 0 ? Math.max(2, acoa / maxVal * 96) : 2;
            var diffPct = actPct - estPct;
            barHtml = '<div style="position:absolute;left:0;top:0;width:' + estPct.toFixed(1) + '%;height:11px;background:#64748b;border-radius:2px 0 0 2px;min-width:4px"></div>'
                    + '<div style="position:absolute;left:' + estPct.toFixed(1) + '%;top:0;width:' + Math.max(0,diffPct).toFixed(1) + '%;height:11px;background:#e8820c;border-radius:0 2px 2px 0"></div>';
        } else {
            // Slate up to acoa, teal from acoa to est (saving shown left-to-right)
            var actPct  = maxVal > 0 ? Math.max(2, acoa / maxVal * 96) : 2;
            var savePct = estPct - actPct;
            barHtml = '<div style="position:absolute;left:0;top:0;width:' + actPct.toFixed(1) + '%;height:11px;background:#64748b;border-radius:2px 0 0 2px;min-width:4px"></div>'
                    + '<div style="position:absolute;left:' + actPct.toFixed(1) + '%;top:0;width:' + Math.max(0,savePct).toFixed(1) + '%;height:11px;background:#1b9e8e;border-radius:0 2px 2px 0"></div>';
        }

        var diffLabel = hasAct
            ? (over ? 'Cost Overrun: +' + fmtCost(Math.abs(diff)) : 'Cost Saving: ' + fmtCost(Math.abs(diff)))
            : 'No actual data';
        var tipStr = 'Estimated Cost of Activity: ' + fmtCost(est)
            + '|Actual Cost of Activity: ' + (hasAct ? fmtCost(acoa) : '-')
            + '|Difference in Cost: ' + diffLabel;
        html += '<div class="brow cd-bar-tip" data-aid="' + a.id + '" style="cursor:pointer;padding:3px 6px 4px;display:flex;align-items:center;gap:6px;width:100%;box-sizing:border-box;position:relative;border-top:1px solid #cbd5e1">'
            + '<div style="font-size:12px;color:#111;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:45%;flex-shrink:0">' + sh(a.name||'',40) + '</div>'
            + '<div style="flex:1;height:11px;position:relative">' + barHtml + '</div>'
            + '</div>';
    });
    el.innerHTML = html;

}
function renderCdIowBars(){
    var el = document.getElementById('cd-c3');
    if (!el) return;
    if (!_iow_items.length || !_all.length) {
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No IOW data</div>';
        return;
    }
    // Sum estimated and actual cost of all activities per IOW item
    // If activity has no actual yet, use estimated as proxy
    var iowEst     = {};
    var iowAct     = {};
    var iowHasReal = {}; // true if at least one activity has real actual
    _all.forEach(function(a){
        var sid  = String(a.scheduleitem_id);
        var c    = _cdCostMap[String(a.id)] || {};
        var est  = +c.est  || 0;
        var acoa = +c.acoa || 0;
        iowEst[sid] = (iowEst[sid] || 0) + est;
        iowAct[sid] = (iowAct[sid] || 0) + (acoa > 0 ? acoa : est);
        if (acoa > 0) iowHasReal[sid] = true;
    });
    var maxVal = 0;
    _iow_items.forEach(function(iow){
        var sid  = String(iow.id);
        var top  = Math.max(iowEst[sid] || 0, iowAct[sid] || 0);
        if (top > maxVal) maxVal = top;
    });
    var html = '';
    _iow_items.forEach(function(iow){
        var sid      = String(iow.id);
        var est      = iowEst[sid]     || 0;
        var acoa     = iowAct[sid]     || 0;
        var hasReal  = iowHasReal[sid] || false;
        var diff     = acoa - est;
        var over     = diff > 0;
        var estPct   = maxVal > 0 ? Math.max(2, est / maxVal * 96) : 2;

        var barHtml;
        if (!hasReal || diff === 0) {
            barHtml = '<div style="position:absolute;left:0;top:0;width:' + estPct.toFixed(1) + '%;height:11px;background:#64748b;border-radius:2px;min-width:4px"></div>';
        } else if (over) {
            var actPct  = maxVal > 0 ? Math.max(2, acoa / maxVal * 96) : 2;
            var diffPct = actPct - estPct;
            barHtml = '<div style="position:absolute;left:0;top:0;width:' + estPct.toFixed(1) + '%;height:11px;background:#64748b;border-radius:2px 0 0 2px;min-width:4px"></div>'
                    + '<div style="position:absolute;left:' + estPct.toFixed(1) + '%;top:0;width:' + Math.max(0,diffPct).toFixed(1) + '%;height:11px;background:#e8820c;border-radius:0 2px 2px 0"></div>';
        } else {
            var actPct  = maxVal > 0 ? Math.max(2, acoa / maxVal * 96) : 2;
            var savePct = estPct - actPct;
            barHtml = '<div style="position:absolute;left:0;top:0;width:' + actPct.toFixed(1) + '%;height:11px;background:#64748b;border-radius:2px 0 0 2px;min-width:4px"></div>'
                    + '<div style="position:absolute;left:' + actPct.toFixed(1) + '%;top:0;width:' + Math.max(0,savePct).toFixed(1) + '%;height:11px;background:#1b9e8e;border-radius:0 2px 2px 0"></div>';
        }

        var diffLabel = hasReal
            ? (over ? 'Cost Overrun: +' + fmtCost(Math.abs(diff)) : 'Cost Saving: ' + fmtCost(Math.abs(diff)))
            : 'Actual not yet available (showing estimate)';
        var tipStr = 'Estimated IOW Cost: ' + fmtCost(est)
            + '|Actual IOW Cost: ' + (hasReal ? fmtCost(acoa) : 'Using estimate as proxy')
            + '|Difference in Cost: ' + diffLabel;

        html += '<div class="brow cd-iow-row" data-iowid="' + iow.id + '" style="padding:3px 6px 4px;display:flex;align-items:center;gap:6px;width:100%;box-sizing:border-box;cursor:pointer;border-top:1px solid #cbd5e1">'
            + '<div style="font-size:12px;color:#111;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:45%;flex-shrink:0">' + sh(iow.name||'',40) + '</div>'
            + '<div style="flex:1;height:11px;position:relative">' + barHtml + '</div>'
            + '</div>';
    });
    el.innerHTML = html;

    // Wire IOW clicks to filter cd-c4 activities
    el.querySelectorAll('.cd-iow-row').forEach(function(row){
        row.addEventListener('click', function(){
            var iowId = String(row.getAttribute('data-iowid'));
            el.querySelectorAll('.cd-iow-row').forEach(function(r){ r.style.background = ''; });
            row.style.background = '#dbeafe';
            var filtered = (_all || []).filter(function(a){ return String(a.scheduleitem_id) === iowId; });
            renderCdActivityBars(filtered);
        });
    });
}

function renderCdProjectBar(){
    var el = document.getElementById('cd-c2');
    if (!el) return;

    // Sum est, acoa, estwd, actwd across all activities
    var totEst = 0, totAcoa = 0, totEstWD = 0, totActWD = 0, hasReal = false;
    (_all || []).forEach(function(a){
        var c    = _cdCostMap[String(a.id)] || {};
        var est  = +c.est   || 0;
        var acoa = +c.acoa  || 0;
        var estwd = +c.estwd || 0;
        var actwd = +c.actwd || 0;
        totEst   += est;
        totAcoa  += acoa > 0 ? acoa : est;
        totEstWD += estwd;
        totActWD += actwd;
        if (acoa > 0) hasReal = true;
    });

    var diff  = totAcoa - totEst;
    var over  = diff > 0;
    var pct   = 96; // project bar always full width (it's the only bar)
    var acoaPct = totEst > 0 ? Math.max(2, totAcoa / totEst * 96) : 96;

    var barHtml;
    if (!hasReal || diff === 0) {
        barHtml = '<div style="position:absolute;left:0;top:0;width:96%;height:13px;background:#64748b;border-radius:3px;min-width:4px"></div>';
    } else if (over) {
        var diffPct = acoaPct - pct;
        barHtml = '<div style="position:absolute;left:0;top:0;width:' + pct + '%;height:13px;background:#64748b;border-radius:3px 0 0 3px;min-width:4px"></div>'
                + '<div style="position:absolute;left:' + pct + '%;top:0;width:' + Math.max(0,diffPct).toFixed(1) + '%;height:13px;background:#e8820c;border-radius:0 3px 3px 0"></div>';
    } else {
        var savePct = pct - acoaPct;
        barHtml = '<div style="position:absolute;left:0;top:0;width:' + acoaPct.toFixed(1) + '%;height:13px;background:#64748b;border-radius:3px 0 0 3px;min-width:4px"></div>'
                + '<div style="position:absolute;left:' + acoaPct.toFixed(1) + '%;top:0;width:' + Math.max(0,savePct).toFixed(1) + '%;height:13px;background:#1b9e8e;border-radius:0 3px 3px 0"></div>';
    }

    var diffLabel = hasReal
        ? (over ? 'Cost Overrun: +' + fmtCost(Math.abs(diff)) : 'Cost Saving: ' + fmtCost(Math.abs(diff)))
        : '';

    var diffWD    = totActWD - totEstWD;
    var overWD    = diffWD > 0;
    var diffWDLabel = hasReal && diffWD !== 0
        ? (overWD ? 'Work Done Cost Overrun: +' : 'Work Done Cost Saving: ') + fmtCost(Math.abs(diffWD))
        : '';

    var projName = _cdProjectName || '';
    var tipStr = 'Estimated Cost of Project: ' + fmtCost(totEst)
        + '|Actual Cost of Project: ' + (hasReal ? fmtCost(totAcoa) : 'Using estimate')
        + '|Difference in Cost: ' + (diffLabel || 'No actual data');

    var html = ''
        + '<div style="padding:6px 8px 4px;box-sizing:border-box;display:flex;flex-direction:column;justify-content:flex-end;height:100%">'
        +   (projName ? '<div style="font-family:\'Barlow Condensed\',sans-serif;font-size:13px;font-weight:700;color:#1e293b;margin-bottom:5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + projName + '</div>' : '')
        +   '<div style="height:13px;position:relative;margin-bottom:10px;flex-shrink:0">' + barHtml + '</div>'
        +   '<div style="display:flex;gap:0;font-family:\'Barlow Condensed\',sans-serif;font-size:13px;font-weight:600;color:#444;line-height:1">'
        +     '<div style="flex:1;border-right:1px solid #cbd5e1;padding-right:10px">'
        +       '<div style="font-size:11px;color:#3461b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;padding-bottom:3px">Project Cost</div>'
        +       '<div style="display:flex;justify-content:space-between;border-top:1px solid #cbd5e1;padding:4px 0"><span>Estimated</span><span style="color:#111">' + fmtCost(totEst) + '</span></div>'
        +       '<div style="display:flex;justify-content:space-between;border-top:1px solid #cbd5e1;padding:4px 0"><span>Actual</span><span style="color:' + (hasReal ? (over?'#e8820c':'#1b9e8e') : '#111') + '">' + (hasReal ? fmtCost(totAcoa) : 'Using estimate') + '</span></div>'
        +       (diffLabel ? '<div style="display:flex;justify-content:space-between;border-top:1px solid #cbd5e1;padding:4px 0"><span>' + (over?'Overrun':'Saving') + '</span><span style="color:' + (over?'#e8820c':'#1b9e8e') + '">' + fmtCost(Math.abs(diff)) + '</span></div>' : '')
        +     '</div>'
        +     '<div style="flex:1;padding-left:10px">'
        +       '<div style="font-size:11px;color:#3461b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;padding-bottom:3px">Cost of Work Done</div>'
        +       '<div style="display:flex;justify-content:space-between;border-top:1px solid #cbd5e1;padding:4px 0"><span>Estimated</span><span style="color:#111">' + fmtCost(totEstWD) + '</span></div>'
        +       '<div style="display:flex;justify-content:space-between;border-top:1px solid #cbd5e1;padding:4px 0"><span>Actual</span><span style="color:' + (overWD?'#e8820c':'#1b9e8e') + '">' + fmtCost(totActWD) + '</span></div>'
        +       (diffWDLabel ? '<div style="display:flex;justify-content:space-between;border-top:1px solid #cbd5e1;padding:4px 0"><span>' + (overWD?'Overrun':'Saving') + '</span><span style="color:' + (overWD?'#e8820c':'#1b9e8e') + '">' + fmtCost(Math.abs(diffWD)) + '</span></div>' : '')
        +     '</div>'
        +   '</div>'
        + '</div>';

    el.innerHTML = html;
}

function renderCdGroupBars(){
    var el = document.getElementById('cd-c1');
    if (!el) return;
    if (!_groups.length || !_iow_items.length || !_all.length) {
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No group data</div>';
        return;
    }
    // Sum est and actual per IOW first; use est as proxy when no actual yet
    var iowEst = {}, iowAct = {}, iowReal = {};
    _all.forEach(function(a){
        var sid  = String(a.scheduleitem_id);
        var c    = _cdCostMap[String(a.id)] || {};
        var est  = +c.est  || 0;
        var acoa = +c.acoa || 0;
        iowEst[sid] = (iowEst[sid] || 0) + est;
        iowAct[sid] = (iowAct[sid] || 0) + (acoa > 0 ? acoa : est);
        if (acoa > 0) iowReal[sid] = true;
    });
    // Sum IOW totals per group
    var grpEst = {}, grpAct = {}, grpHasReal = {};
    _iow_items.forEach(function(iow){
        var gid = String(iow.group_id);
        var sid = String(iow.id);
        grpEst[gid] = (grpEst[gid] || 0) + (iowEst[sid] || 0);
        grpAct[gid] = (grpAct[gid] || 0) + (iowAct[sid] || 0);
        if (iowReal[sid]) grpHasReal[gid] = true;
    });
    var maxVal = 0;
    _groups.forEach(function(g){
        var top = Math.max(grpEst[String(g.id)] || 0, grpAct[String(g.id)] || 0);
        if (top > maxVal) maxVal = top;
    });
    var html = '';
    _groups.forEach(function(g){
        var gid     = String(g.id);
        var est     = grpEst[gid]     || 0;
        var acoa    = grpAct[gid]     || 0;
        var hasReal = grpHasReal[gid] || false;
        var diff    = acoa - est;
        var over    = diff > 0;
        var estPct  = maxVal > 0 ? Math.max(2, est / maxVal * 96) : 2;

        var barHtml;
        if (!hasReal || diff === 0) {
            barHtml = '<div style="position:absolute;left:0;top:0;width:' + estPct.toFixed(1) + '%;height:11px;background:#64748b;border-radius:2px;min-width:4px"></div>';
        } else if (over) {
            var actPct  = maxVal > 0 ? Math.max(2, acoa / maxVal * 96) : 2;
            var diffPct = actPct - estPct;
            barHtml = '<div style="position:absolute;left:0;top:0;width:' + estPct.toFixed(1) + '%;height:11px;background:#64748b;border-radius:2px 0 0 2px;min-width:4px"></div>'
                    + '<div style="position:absolute;left:' + estPct.toFixed(1) + '%;top:0;width:' + Math.max(0,diffPct).toFixed(1) + '%;height:11px;background:#e8820c;border-radius:0 2px 2px 0"></div>';
        } else {
            var actPct  = maxVal > 0 ? Math.max(2, acoa / maxVal * 96) : 2;
            var savePct = estPct - actPct;
            barHtml = '<div style="position:absolute;left:0;top:0;width:' + actPct.toFixed(1) + '%;height:11px;background:#64748b;border-radius:2px 0 0 2px;min-width:4px"></div>'
                    + '<div style="position:absolute;left:' + actPct.toFixed(1) + '%;top:0;width:' + Math.max(0,savePct).toFixed(1) + '%;height:11px;background:#1b9e8e;border-radius:0 2px 2px 0"></div>';
        }

        var diffLabel = hasReal
            ? (over ? 'Cost Overrun: +' + fmtCost(Math.abs(diff)) : 'Cost Saving: ' + fmtCost(Math.abs(diff)))
            : 'Actual not yet available (showing estimate)';
        var tipStr = 'Estimated Group Cost: ' + fmtCost(est)
            + '|Actual Group Cost: ' + (hasReal ? fmtCost(acoa) : 'Using estimate as proxy')
            + '|Difference in Cost: ' + diffLabel;

        html += '<div class="brow cd-grp-row" data-grpid="' + g.id + '" style="padding:3px 6px 4px;display:flex;align-items:center;gap:6px;width:100%;box-sizing:border-box;cursor:pointer;border-top:1px solid #cbd5e1">'
            + '<div style="font-size:12px;color:#111;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:45%;flex-shrink:0">' + sh(g.name||'',40) + '</div>'
            + '<div style="flex:1;height:11px;position:relative">' + barHtml + '</div>'
            + '</div>';
    });
    el.innerHTML = html;

    // Wire group clicks to filter IOW panel and activity panel
    el.querySelectorAll('.cd-grp-row').forEach(function(row){
        row.addEventListener('click', function(){
            var gid = String(row.getAttribute('data-grpid'));
            el.querySelectorAll('.cd-grp-row').forEach(function(r){ r.style.background = ''; });
            row.style.background = '#dbeafe';
            filterByGroupCd(gid);
        });
    });
}

function filterByGroupCd(groupId){
    var gid = String(groupId);
    var filteredIows = _iow_items.filter(function(i){ return String(i.group_id) === gid; });
    var iowIds = filteredIows.map(function(i){ return String(i.id); });
    var filteredActs = (_all || []).filter(function(a){ return iowIds.indexOf(String(a.scheduleitem_id)) !== -1; });
    renderCdActivityBars(filteredActs);
}
function filterByIowCd(iowId){ /* handled inline in renderCdIowBars */ }

function loadCdActivityData(actId){
    $('#cd-c4 .brow').removeClass('brow-active');
    $('#cd-c4 .brow[data-aid="' + actId + '"]').addClass('brow-active');
    var el = document.getElementById('cd-c6');
    if (el) el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">Loading...</div>';
    $.ajax({
        type: 'POST',
        url: '../projectsmain/costdashboardactivity',
        data: { actid: actId },
        dataType: 'json',
        success: function(d) {
            if (!d || d.error) {
                if (el) el.innerHTML = '<div style="text-align:center;font-size:12px;color:#c0392b;padding:18px 0">' + (d && d.error ? d.error : 'Error') + '</div>';
                return;
            }
            renderCdUnitCostOfResource(d.items, d.activity_name);
            renderCdResourceConsumption(d.items, d.activity_name, d.last_report_qty, d.unit);
            renderCdResourceCost(d.items, d.activity_name);
            renderCdUnitCostOfActivity(d.items, d.activity_name, d.unit, d.schedule_qty);
            renderCdValueOfWorkDone(d);
            renderCdCostOfActivity(d);
        },
        error: function() {
            if (el) el.innerHTML = '<div style="text-align:center;font-size:12px;color:#c0392b;padding:18px 0">Failed to load</div>';
        }
    });
}

function renderCdCurrentProjectCost(activities){ /* to be implemented */ }
function renderCdUnitCostOfResource(items, actName){
    var el = document.getElementById('cd-c6');
    if (!el) return;
    items = items || [];
    if (!items.length) {
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No resources allocated</div>';
        return;
    }
    var maxVal = 0;
    items.forEach(function(r) {
        var est = +r.rate || 0;
        var act = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined) ? +r.actual_unit_cost : est;
        if (Math.max(est, act) > maxVal) maxVal = Math.max(est, act);
    });
    if (maxVal === 0) maxVal = 1;
    var rows = '';
    items.forEach(function(r) {
        var est = +r.rate || 0;
        var hasActual = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
        var act = hasActual ? +r.actual_unit_cost : est;
        var unit = r.unit ? ' /' + shu(r.unit) : '';
        var src  = hasActual
            ? '<span style="font-size:9px;background:#e8f0fe;color:#3461b8;border-radius:3px;padding:1px 4px;margin-left:4px">' + (+r.type_id === 4 ? 'MB' : 'GRN') + '</span>'
            : '';
        // Bar geometry
        var barBase = (+r.type_id === 4) ? '#4a5568' : '#4a5568';
        var estPct = (est / maxVal * 100).toFixed(1);
        var diff   = act - est;
        var diffPct = (Math.abs(diff) / maxVal * 100).toFixed(1);
        var diffCol = diff > 0 ? '#e8820c' : '#1b9e8e';
        var barHtml;
        if (diff > 0) {
            barHtml = '<div style="display:flex;height:10px;border-radius:3px;overflow:hidden;width:100%">'
                + '<div style="width:' + estPct + '%;background:' + barBase + ';flex-shrink:0"></div>'
                + '<div style="width:' + diffPct + '%;background:' + diffCol + ';flex-shrink:0"></div>'
                + '</div>';
        } else if (diff < 0) {
            var actPct = (act / maxVal * 100).toFixed(1);
            var gapPct = (Math.abs(diff) / maxVal * 100).toFixed(1);
            barHtml = '<div style="display:flex;height:10px;border-radius:3px;overflow:hidden;width:100%">'
                + '<div style="width:' + actPct + '%;background:' + barBase + ';flex-shrink:0"></div>'
                + '<div style="width:' + gapPct + '%;background:' + diffCol + ';flex-shrink:0"></div>'
                + '</div>';
        } else {
            barHtml = '<div style="display:flex;height:10px;border-radius:3px;overflow:hidden;width:100%">'
                + '<div style="width:' + estPct + '%;background:' + barBase + ';flex-shrink:0"></div>'
                + '</div>';
        }
        var actCol = diff > 0 ? '#e8820c' : (diff < 0 ? '#1b9e8e' : '#4a5568');
        rows += '<div style="padding:3px 6px;border-bottom:1px solid #f0f3fa">'
            + '<div style="display:grid;grid-template-columns:1fr 20px 56px 20px 56px;align-items:baseline;margin-bottom:2px">'
            +   '<div style="font-size:11px;font-weight:600;color:#1a2540;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0" title="' + (r.name||'') + '">' + (r.name||'') + '</div>'
            +   '<span style="font-size:9px;color:#888;text-align:right;padding-right:2px">Est</span>'
            +   '<span style="font-size:11px;color:#000;font-weight:700;text-align:right">' + fmtCost(est) + '<span style="font-size:9px;color:#888;font-weight:400">' + unit + '</span></span>'
            +   '<span style="font-size:9px;color:#888;text-align:right;padding-right:2px">Act</span>'
            +   '<span style="font-size:11px;color:' + actCol + ';font-weight:700;text-align:right">' + fmtCost(act) + '<span style="font-size:9px;color:#888;font-weight:400">' + unit + '</span></span>'
            + '</div>'
            + barHtml
            + '</div>';
    });
    el.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0">'
        + sh(actName||'',40) + '</div>'
        + '<div style="overflow-y:auto;flex:1;min-height:0">' + rows + '</div>';
}
function renderCdValueOfWorkDone(d){
    var el = document.getElementById('cd-g4');
    if (!el) return;
    var schedQty = +d.schedule_qty || 0;
    var lastQty  = +d.last_report_qty || 0;
    var unit     = d.unit || '';
    var actName  = d.activity_name || '';

    var maxVal = schedQty > 0 ? schedQty : 1;
    var cx=105, cy=92, r=76, sw=14;

    function ptF(frac){ var a=Math.PI*(1-frac); return [(cx+r*Math.cos(a)).toFixed(1),(cy-r*Math.sin(a)).toFixed(1)]; }
    function arc(f1,f2,col,cap){
        if(f2<=f1) return '';
        cap=cap||'butt';
        var p1=ptF(f1), p2=ptF(f2);
        if((f2-f1)>=1){
            var pm=ptF(0.5);
            return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+pm[0]+','+pm[1]+
                   ' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
        }
        return '<path d="M'+p1[0]+','+p1[1]+' A'+r+','+r+' 0 0,1 '+p2[0]+','+p2[1]+'" fill="none" stroke="'+col+'" stroke-width="'+sw+'" stroke-linecap="'+cap+'"/>';
    }

    var f = Math.max(0, Math.min(1, lastQty / maxVal));
    var nr=r-15, na=Math.PI*(1-f);
    var nx=(cx+nr*Math.cos(na)).toFixed(1), ny=(cy-nr*Math.sin(na)).toFixed(1);
    var pct = schedQty > 0 ? (lastQty/schedQty*100).toFixed(1) : '0.0';

    var svg='<svg width="210" height="138" viewBox="0 0 210 138" xmlns="http://www.w3.org/2000/svg">'
        // full arc = schedule qty (slate)
        +arc(0,1,'#64748b')
        // progress overlay (grey)
        +(f>0?arc(0,f,'#94a3b8','butt'):'')
        // needle
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#dce3ef"/>'
        // centre: progress %
        +'<text x="'+cx+'" y="'+(cy-22)+'" text-anchor="middle" font-size="22" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+pct+'%</text>'
        // labels below: Done on left, Sched on right
        +'<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Done <tspan font-weight="700">'+fm(lastQty)+(unit?' '+unit:'')+'</tspan></text>'
        +'<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Sched <tspan font-weight="700">'+fm(schedQty)+(unit?' '+unit:'')+'</tspan></text>'
        +'</svg>';

    el.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">'+sh(actName,40)+'</div>'
        + svg;
}
function renderCdUnitCostOfActivity(items, actName, actUnit, schedQty){
    items = items || [];
    schedQty = +schedQty || 0;

    // Sum total estimated and actual cost across all resource types (same as renderCdResourceCost groups)
    var estTotal = 0, actTotal = 0, hasActual = false;
    items.forEach(function(r){
        var estUC = +r.rate || 0;
        var estCons = +r.planned_consumption || 0;
        var estContrib = estUC * estCons;
        estTotal += estContrib;
        var hasAct = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
        var actUC = hasAct ? +r.actual_unit_cost : estUC;
        var actCons = +r.actual_consumption || 0;
        var actContrib = actUC * actCons;
        actTotal += actContrib;
        if (hasAct) hasActual = true;
    });


    // Unit cost of activity = total resource cost (no division by schedule_qty)
    var estUCA = estTotal;
    var actUCA = actTotal;

    var maxVal = estUCA > 0 ? estUCA * 2 : 1;
    var unitLbl = actUnit ? ' /'+actUnit : '';
    var el5 = document.getElementById('cd-g5');
    if (el5) el5.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0;width:100%;box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">'+sh(actName||'',40)+'</div>';
    gauge('cd-g5', actUCA, maxVal, 'cost', 0.5,
        'Est', fmtCost(estUCA)+unitLbl,
        hasActual ? 'Act' : '', hasActual ? fmtCost(actUCA)+unitLbl : '',
        '');
}
function renderCdCostOfActivity(d){
    var el = document.getElementById('cd-g2');
    if (!el) return;
    var items    = d.items || [];
    var schedQty = +d.schedule_qty || 0;
    var actName  = d.activity_name || '';

    // Estimated unit cost of activity = sum of all resource type costs (same as UCA dial)
    var estUCTotal = 0, actUCTotal = 0, hasActual = false;
    items.forEach(function(r){
        var estUC  = +r.rate || 0;
        var estCons = +r.planned_consumption || 0;
        estUCTotal += estUC * estCons;
        var hasAct = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined);
        var actUC  = hasAct ? +r.actual_unit_cost : estUC;
        var actCons = +r.actual_consumption || 0;
        actUCTotal += actUC * actCons;
        if (hasAct) hasActual = true;
    });

    // Cost of activity = unit cost × schedule qty
    var estCost = estUCTotal * schedQty;
    var actCost = hasActual ? actUCTotal * schedQty : estCost;
    var diff = actCost - estCost;
    var over = diff > 0;

    // Bar: full width = estimated cost
    // over: slate base (est) + orange extension
    // under: slate bar up to actual + blue-green from right
    var barHtml;
    if (!hasActual || diff === 0) {
        barHtml = '<div style="width:100%;height:14px;background:#64748b;border-radius:3px"></div>';
    } else if (over) {
        var estPct  = (estCost / actCost * 100).toFixed(1);
        var diffPct = (diff    / actCost * 100).toFixed(1);
        barHtml = '<div style="display:flex;height:14px;border-radius:3px;overflow:hidden;width:100%">'
            +'<div style="width:'+estPct+'%;background:#64748b;flex-shrink:0"></div>'
            +'<div style="width:'+diffPct+'%;background:#e8820c;flex-shrink:0"></div>'
            +'</div>';
    } else {
        var actPct  = (actCost / estCost * 100).toFixed(1);
        var savePct = (Math.abs(diff) / estCost * 100).toFixed(1);
        barHtml = '<div style="display:flex;height:14px;border-radius:3px;overflow:hidden;width:100%">'
            +'<div style="width:'+actPct+'%;background:#64748b;flex-shrink:0"></div>'
            +'<div style="width:'+savePct+'%;background:#1b9e8e;flex-shrink:0"></div>'
            +'</div>';
    }

    var diffLabel = hasActual && diff !== 0
        ? (over ? '+' : '-') + fmtCost(Math.abs(diff)) + ' ' + (over ? 'over' : 'saving')
        : '';
    var diffCol = over ? '#e8820c' : '#1b9e8e';

    var workDone = +d.last_report_qty || 0;
    var estCostWD = estUCTotal * workDone;
    var actCostWD = actUCTotal * workDone;

    var valRow = '<div style="display:flex;justify-content:space-between;align-items:baseline;font-family:\'Barlow Condensed\',sans-serif;font-size:15px;font-weight:700;color:#111;margin-bottom:6px">'
        +'<span>Est: '+fmtCost(estCost)
        +(hasActual ? '&nbsp;&nbsp;&nbsp;Act: <span style="color:'+(over?'#e8820c':'#1b9e8e')+'">'+fmtCost(actCost)+'</span>' : '')
        +'</span>'
        +(diffLabel ? '<span style="color:'+diffCol+'">'+diffLabel+'</span>' : '')
        +'</div>';

    var diffWD = actCostWD - estCostWD;
    var overWD = diffWD > 0;
    var diffWDLabel = diffWD !== 0
        ? (overWD ? '+' : '-') + fmtCost(Math.abs(diffWD)) + ' ' + (overWD ? 'over' : 'saving')
        : '';

    var wdRow = '<div style="font-family:\'Barlow Condensed\',sans-serif;font-size:13px;font-weight:600;color:#444;margin-top:8px;line-height:1.7">'
        +'<div>Estimated Cost of Work Done &nbsp;<span style="color:#111">'+fmtCost(estCostWD)+'</span></div>'
        +'<div>Actual Cost of Work Done &nbsp;<span style="color:'+(overWD?'#e8820c':'#1b9e8e')+'">'+fmtCost(actCostWD)+'</span></div>'
        +'<div style="color:'+(overWD?'#e8820c':'#1b9e8e')+'">Difference &nbsp;'+(diffWDLabel||fmtCost(0))+'</div>'
        +'</div>';

    el.style.overflow = 'auto';
    el.style.display  = 'block';
    el.innerHTML =
        '<div style="padding:4px 6px 8px;width:100%;box-sizing:border-box">'
        +'<div style="font-size:10px;color:#3461b8;font-weight:600;padding-bottom:4px;border-bottom:1px solid #e8efff;margin-bottom:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">'+sh(actName,40)+'</div>'
        +  valRow
        +  barHtml
        +  wdRow
        +'</div>';
}
function renderCdCostOnCompletion(items, actName, estQty){ /* to be implemented */ }
function renderCdResourceConsumption(items, actName, lastQty, actUnit){
    var el = document.getElementById('cd-c7');
    if (!el) return;
    items = items || [];
    if (!items.length) {
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No resources allocated</div>';
        return;
    }
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
        var unit = r.unit ? shu(r.unit) : (r.task_unit ? shu(r.task_unit) : '');
        var diff = act - est;
        var actCol = diff > 0 ? '#e8820c' : (diff < 0 ? '#1b9e8e' : '#4a5568');
        var src = hasActual
            ? '<span style="font-size:9px;background:#e8f0fe;color:#3461b8;border-radius:3px;padding:1px 3px;margin-left:3px">' + (isSC ? 'MB' : 'GRN') + '</span>'
            : '';
        // Bar geometry
        var barBase = isSC ? '#4a5568' : '#4a5568';
        var estPct = (est / maxVal * 100).toFixed(1);
        var barHtml;
        if (diff > 0) {
            var diffPct = (diff / maxVal * 100).toFixed(1);
            barHtml = '<div style="display:flex;height:10px;border-radius:3px;overflow:hidden;width:100%">'
                + '<div style="width:' + estPct + '%;background:' + barBase + ';flex-shrink:0"></div>'
                + '<div style="width:' + diffPct + '%;background:#e8820c;flex-shrink:0"></div>'
                + '</div>';
        } else if (diff < 0) {
            var actPct = (act / maxVal * 100).toFixed(1);
            var gapPct = (Math.abs(diff) / maxVal * 100).toFixed(1);
            barHtml = '<div style="display:flex;height:10px;border-radius:3px;overflow:hidden;width:100%">'
                + '<div style="width:' + actPct + '%;background:' + barBase + ';flex-shrink:0"></div>'
                + '<div style="width:' + gapPct + '%;background:#1b9e8e;flex-shrink:0"></div>'
                + '</div>';
        } else {
            barHtml = '<div style="display:flex;height:10px;border-radius:3px;overflow:hidden;width:100%">'
                + '<div style="width:' + estPct + '%;background:' + barBase + ';flex-shrink:0"></div>'
                + '</div>';
        }
        rows += '<div style="padding:3px 6px;border-bottom:1px solid #f0f3fa">'
            + '<div style="display:grid;grid-template-columns:1fr 20px 56px 20px 56px;align-items:baseline;margin-bottom:2px">'
            +   '<div style="font-size:11px;font-weight:600;color:#1a2540;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0" title="' + (r.name||'') + '">' + (r.name||'') + '</div>'
            +   '<span style="font-size:9px;color:#888;text-align:right;padding-right:2px">Est</span>'
            +   '<span style="font-size:11px;color:#000;font-weight:700;text-align:right">' + fm(est) + '<span style="font-size:9px;color:#888;font-weight:400"> ' + unit + '</span></span>'
            +   '<span style="font-size:9px;color:#888;text-align:right;padding-right:2px">Act</span>'
            +   '<span style="font-size:11px;color:' + actCol + ';font-weight:700;text-align:right">' + fm(act) + '<span style="font-size:9px;color:#888;font-weight:400"> ' + unit + '</span></span>'
            + '</div>'
            + barHtml
            + '</div>';
    });
    el.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0">'
        + sh(actName||'', 40) + '</div>'
        + '<div style="overflow-y:auto;flex:1;min-height:0">' + rows + '</div>';
}
function renderCdResourceCost(items, actName){
    var el = document.getElementById('cd-rcost');
    if (!el) return;
    items = items || [];
    if (!items.length) {
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No resources</div>';
        return;
    }
    // Group by type_name, sum est and act costs
    var groups = {};
    items.forEach(function(r) {
        var key = r.type_name || 'Other';
        if (!groups[key]) groups[key] = { est: 0, act: 0 };
        var estUC = +r.rate || 0;
        var actUC = (r.actual_unit_cost !== null && r.actual_unit_cost !== undefined) ? +r.actual_unit_cost : estUC;
        var estCons = +r.planned_consumption || 0;
        var actCons = +r.actual_consumption  || 0;
        groups[key].est += estUC * estCons;
        groups[key].act += actUC * actCons;
    });
    var labels = Object.keys(groups);
    if (!labels.length) {
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No data</div>';
        return;
    }
    // Find max for scaling
    var maxVal = 0;
    labels.forEach(function(k) { maxVal = Math.max(maxVal, groups[k].est, groups[k].act); });
    if (maxVal === 0) maxVal = 1;
    // Build vertical bar columns
    var estRow = '', actRow = '', bars = '', lblRow = '';
    labels.forEach(function(k) {
        var est = groups[k].est;
        var act = groups[k].act;
        var diff = act - est;
        var actCol = diff > 0 ? '#e8820c' : (diff < 0 ? '#1b9e8e' : '#4a5568');
        var estPct = (est / maxVal * 100).toFixed(1);
        var actPct = (act / maxVal * 100).toFixed(1);
        // Top label: show actual value coloured, est in grey below it
        estRow += '<div style="flex:1;text-align:center;font-size:9px;font-weight:700;color:#4a5568">' + fmtCost(est) + '</div>';
        actRow += '<div style="flex:1;text-align:center;font-size:9px;font-weight:700;color:' + actCol + '">' + fmtCost(act) + '</div>';
        // Single bar: grey base = est, overlay = difference
        var barInner;
        if (diff > 0) {
            // actual > est: grey up to est, orange extension above
            var diffPct = (diff / maxVal * 100).toFixed(1);
            barInner = '<div style="width:100%;height:' + diffPct + '%;background:#e8820c;border-radius:2px 2px 0 0;flex-shrink:0"></div>'
                     + '<div style="width:100%;height:' + estPct + '%;background:#4a5568;flex-shrink:0"></div>';
        } else if (diff < 0) {
            // actual < est: blue-green top segment, grey remainder below
            var savePct  = (Math.abs(diff) / maxVal * 100).toFixed(1);
            barInner = '<div style="width:100%;height:' + savePct + '%;background:#1b9e8e;border-radius:2px 2px 0 0;flex-shrink:0"></div>'
                     + '<div style="width:100%;height:' + actPct + '%;background:#4a5568;flex-shrink:0"></div>';
        } else {
            barInner = '<div style="width:100%;height:' + estPct + '%;background:#4a5568;border-radius:2px 2px 0 0;flex-shrink:0"></div>';
        }
        bars += '<div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;height:100%;padding:0 3px">'
            + barInner
            + '</div>';
        lblRow += '<div style="flex:1;text-align:center;font-size:9px;color:#1a2540;font-weight:600;padding-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + k + '">' + sh(k, 10) + '</div>';
    });
    el.innerHTML = '<div style="font-size:10px;color:#3461b8;font-weight:600;padding:4px 6px 3px;border-bottom:1px solid #e8efff;flex-shrink:0">'
        + sh(actName||'', 40) + '</div>'
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
// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Data fetch ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
function loadAll(){
    $.ajax({
        type:'POST', url:'../projectsmain/performancedashboard', dataType:'json',
        success: function(d){
            if (!d || d.error === undefined) return;
            var name = d.project_name || 'Project';
            $('#pd-title').text(name + ' - Performance Dashboard');
            if (!_cdProjectName) _cdProjectName = name;

            _groups    = d.iow_groups  || [];
            _iow_items = d.iow_items   || [];
            _all       = d.activities  || [];

            // IOW Groups in pd-c1 ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â clicking a group loads its IOW items into pd-c3
            renderBars('pd-c1', _groups.map(function(r){
                return {name:r.name, scheduled:+r.scheduled||0, delay:+r.delay||0, id:r.id,
                        critical:groupIsCritical(r.id),
                        start_date:r.start_date||'', end_date:r.end_date||'',
                        actual_end_date:(r.proj_end_date&&r.proj_end_date!=='0000-00-00')?r.proj_end_date:((r.actual_end_date&&r.actual_end_date!=='0000-00-00')?r.actual_end_date:''),
                        duration_days:+r.scheduled||0};
            }), filterByGroup);

            // Project-level duration bar
            renderProjectBar(
                document.getElementById('pd-c2'),
                +(d.project_bar&&d.project_bar.budgeted)||0,
                +(d.project_bar&&d.project_bar.actual)||0,
                +(d.project_bar&&d.project_bar.delay)||0,
                name,
                (d.project_bar&&d.project_bar.b_start_date)||'',
                (d.project_bar&&d.project_bar.b_end_date)||'',
                (d.project_bar&&d.project_bar.proj_end_date)||(d.project_bar&&d.project_bar.a_end_date)||'',
                (d.project_bar&&d.project_bar.actual_start)||''
            );
            // Default: first group ÃƒÂ¢Ã¢â‚¬Â Ã¢â‚¬â„¢ its IOW items ÃƒÂ¢Ã¢â‚¬Â Ã¢â‚¬â„¢ first IOW's activities
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

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Group click ÃƒÂ¢Ã¢â‚¬Â Ã¢â‚¬â„¢ show IOW items for that group in pd-c3 ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
function filterByGroup(groupId, preselectIowId){
    var gid = String(groupId);
    var filtered = _iow_items.filter(function(i){ return String(i.group_id) === gid; });
    // Fallback: if iowGroupid linkage is missing in DB, show all IOW items
    if (!filtered.length) filtered = _iow_items;
    renderBars('pd-c3', filtered.map(function(r){
        return {name:r.name, scheduled:+r.scheduled||0, delay:+r.delay||0, id:r.id,
                critical:iowIsCritical(r.id),
                start_date:r.start_date||'', end_date:r.end_date||'',
                actual_end_date:(r.proj_end_date&&r.proj_end_date!=='0000-00-00')?r.proj_end_date:((r.actual_end_date&&r.actual_end_date!=='0000-00-00')?r.actual_end_date:''),
                duration_days:+r.scheduled||0};
    }), filterByIow);
    $('#pd-c1 .brow').removeClass('brow-active');
    $('#pd-c1 .brow[data-aid="' + groupId + '"]').addClass('brow-active');
    var firstId = preselectIowId || (filtered.length ? filtered[0].id : null);
    if (firstId) filterByIow(firstId);
}

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ IOW click ÃƒÂ¢Ã¢â‚¬Â Ã¢â‚¬â„¢ show ongoing / upcoming activities ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
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

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Criticality propagation: activity ÃƒÂ¢Ã¢â‚¬Â Ã¢â‚¬â„¢ IOW ÃƒÂ¢Ã¢â‚¬Â Ã¢â‚¬â„¢ IOW group ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
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
                // Overrun: planned bar + red extension
                sc = planned;
                dl = projDur - planned;
                if (r.spr_start_date && r.spr_start_date !== '0000-00-00') {
                    var pe2 = new Date(r.spr_start_date);
                    pe2.setDate(pe2.getDate() + Math.round(projDur) - 1);
                    projEndDate = pe2.toISOString().slice(0, 10);
                }
            } else if (projDur > 0 && projDur < planned) {
                // Under plan: projected bar + yellow slack
                sc = projDur;
                dl = -(planned - projDur); // negative signals slack (yellow)
            } else {
                sc = projDur || planned;
                dl = 0;
                // No overrun but check for start delay in ongoing
                if (!projEndDate && r.delay > 0 && r.end_date && r.end_date !== '0000-00-00') {
                    var pe3 = new Date(r.end_date);
                    pe3.setDate(pe3.getDate() + Math.round(r.delay));
                    projEndDate = pe3.toISOString().slice(0, 10);
                }
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


function fmtCost(v){
    if (!v) return '0.00';
    if (v >= 1e7) return (v/1e7).toFixed(2)+'Cr';
    if (v >= 1e5) return (v/1e5).toFixed(2)+'L';
    if (v >= 1e3) return (v/1e3).toFixed(2)+'K';
    return (+v).toFixed(2);
}
function sh(str,n){ str=str||''; return str.length>n ? str.substring(0,n-1)+'…' : str; }
function fm(v){ v=+v||0; return Number.isInteger(v)?v:v.toFixed(1); }

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Unit shortener ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â abbreviate lengthy unit names for compact panels ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
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
    // "No of Panels" / "Number of Panels" ÃƒÂ¢Ã¢â‚¬Â Ã¢â‚¬â„¢ "Panels"
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
function fmDate(s){
    if (!s) return '';
    var d=new Date(s);
    if (isNaN(d)) return s;
    var mo=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return d.getDate()+' '+mo[d.getMonth()]+' '+d.getFullYear();
}
function renderProjectBar(el, budgeted, actual, serverDelay, label, bStartDate, bEndDate, aEndDate, actualStart){
    if (!el) return;
    if (!budgeted){ el.innerHTML='<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No data</div>'; return; }
    var startDelay = serverDelay || 0;
    var effMax     = Math.max(budgeted, budgeted + startDelay, actual, 1);
    var actVal     = startDelay > 0 ? budgeted + startDelay : (actual > 0 ? actual : budgeted);
    var diffVal    = actVal - budgeted;
    var diffCol    = diffVal > 0 ? '#e53935' : diffVal < 0 ? '#27ae60' : '#1a2540';
    var diffStr    = diffVal > 0 ? '+'+diffVal+' d' : diffVal+' d';
    var F          = 'font-family:Barlow Condensed,sans-serif;';
    var html = '<div style="'+F+'display:flex;flex-direction:column;height:100%;padding:4px 10px;box-sizing:border-box;gap:3px;">';

    // Project name + durations on same line above bar
    html += '<div style="'+F+'display:flex;justify-content:space-between;align-items:baseline;font-size:11px;font-weight:700;margin-bottom:2px;">'
          + '<span style="color:#1a2540;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;padding-right:6px;">'+label+'</span>'
          + '<span style="white-space:nowrap;color:#1a2540;">'+budgeted+' d'
          + (diffVal > 0 ? ' &nbsp;<span style="color:#e53935;">+'+diffVal+' d</span>' : diffVal < 0 ? ' &nbsp;<span style="color:#27ae60;">'+diffVal+' d</span>' : '')
          + '</span>'
          + '</div>';

    // Bar - same height as before
    html += '<div style="display:flex;align-items:stretch;height:14px;border-radius:3px;overflow:hidden;">';
    if (actual > 0 && actual > budgeted){
        html += '<div style="width:'+(budgeted/effMax*100).toFixed(1)+'%;background:#00838f;min-width:3px;"></div>';
        html += '<div style="width:'+((actual-budgeted)/effMax*100).toFixed(1)+'%;background:#e53935;min-width:3px;"></div>';
    } else if (actual > 0 && actual < budgeted){
        html += '<div style="width:'+(actual/effMax*100).toFixed(1)+'%;background:#00838f;min-width:3px;"></div>';
        html += '<div style="width:'+((budgeted-actual)/effMax*100).toFixed(1)+'%;background:#f0c419;min-width:3px;"></div>';
    } else if (startDelay > 0){
        html += '<div style="width:'+(budgeted/effMax*100).toFixed(1)+'%;background:#00838f;min-width:3px;"></div>';
        html += '<div style="width:'+(startDelay/effMax*100).toFixed(1)+'%;background:#e53935;min-width:3px;"></div>';
    } else {
        html += '<div style="width:100%;background:#00838f;min-width:3px;"></div>';
    }
    html += '</div>';

    // Start / End dates — plan row + actual row, start on left / end on right
    var actEndDateD = aEndDate ? new Date(aEndDate) : null;
    if (actEndDateD) actEndDateD.setHours(0,0,0,0);
    var planEndDateD = bEndDate ? new Date(bEndDate) : null;
    if (planEndDateD) planEndDateD.setHours(0,0,0,0);
    var actEndCol = actEndDateD && planEndDateD
        ? (actEndDateD <= planEndDateD ? '#27ae60' : '#e53935')
        : '#aaa';
    html += '<div style="'+F+'font-size:9px;color:#5a6e8c;margin-top:2px;">';
    html += '<div style="display:flex;justify-content:space-between;align-items:center;">'
          + '<span>Plan Start: <b style="color:#1a2540;">'+(fmDate(bStartDate)||'-')+'</b></span>'
          + '<span>Plan End: <b style="color:#1a2540;">'+(fmDate(bEndDate)||'-')+'</b></span>'
          + '</div>';
    html += '<div style="display:flex;justify-content:space-between;align-items:center;">'
          + '<span>Act. Start: <b style="color:'+(actualStart?'#1a2540':'#e53935')+'">'+(actualStart?fmDate(actualStart):'Not Started')+'</b></span>'
          + '<span>Act. End: <b style="color:'+actEndCol+'">'+(aEndDate?fmDate(aEndDate):'-')+'</b></span>'
          + '</div>';
    html += '</div>';

    // Legend — Planned / Elapsed Days / Time to Complete / Difference
    var lgRow2c = function(col, lbl, val){
        return '<div style="display:flex;justify-content:space-between;align-items:center;padding:1px 0;">'
            + '<span style="font-size:10px;"><span style="display:inline-block;width:8px;height:8px;background:'+col+';margin-right:3px;border-radius:2px;vertical-align:middle;"></span>'+lbl+'</span>'
            + '<b style="color:#1a2540;font-size:13px;">'+val+'</b>'
            + '</div>';
    };

    // Elapsed = today - plan start date of first activity
    var elapsedProj = 0, elapsedStr = '-';
    if (bStartDate) {
        var today3 = new Date(); today3.setHours(0,0,0,0);
        var ps = new Date(bStartDate); ps.setHours(0,0,0,0);
        elapsedProj = Math.max(0, Math.round((today3 - ps) / 86400000));
        elapsedStr = elapsedProj + ' d';
    }

    // Time to complete = planned duration - elapsed days (so elapsed + ttc = planned)
    var ttcStr = '-';
    if (bStartDate) {
        var ttc = Math.max(0, budgeted - elapsedProj);
        ttcStr = ttc + ' d';
    }

    // Difference = projected actual end - planned end
    var projDiffStr = '-', projDiffCol = '#1a2540';
    if (actEndDateD && planEndDateD) {
        var dDiff = Math.round((actEndDateD - planEndDateD) / 86400000);
        projDiffStr = dDiff > 0 ? '+'+dDiff+' d (overrun)' : dDiff < 0 ? dDiff+' d (ahead)' : '0 d (on time)';
        projDiffCol = dDiff > 0 ? '#e53935' : dDiff < 0 ? '#27ae60' : '#1a2540';
    }

    html += '<div style="'+F+'font-size:10px;margin-top:4px;border-top:2px solid #d0d8e8;padding-top:3px;overflow:hidden;">'
          + lgRow2c('#00838f', 'Planned', budgeted+' d')
          + lgRow2c('#5c7bd9', 'Elapsed Days', elapsedStr)
          + lgRow2c('#e53935', 'Time to Complete', ttcStr)
          + '<div style="display:flex;justify-content:space-between;align-items:center;padding:1px 0;">'
          + '<span style="font-size:10px;"><span style="display:inline-block;width:8px;height:8px;background:#f0c419;margin-right:3px;border-radius:2px;vertical-align:middle;"></span>Difference</span>'
          + '<b style="color:'+projDiffCol+';font-size:13px;">'+projDiffStr+'</b>'
          + '</div>'
          + '</div>';

    html += '</div>';
    el.innerHTML = html;
}
// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ CSS horizontal bar chart ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
// onRowClick: optional callback(id) ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â defaults to loadKpi
function renderBars(containerId, items, onRowClick){
    var el = document.getElementById(containerId);
    if (!el) return;
    items = items||[];

    var maxVal = 0;
    items.forEach(function(r){ maxVal = Math.max(maxVal, r.scheduled + Math.abs(r.delay)); });
    if (!maxVal) maxVal = 1;

    var ticks = niceAxis(maxVal);

    var html = '<div class="leg">'
        +'<span><span class="ld" style="background:#37474F"></span>Normal</span>'
        +'<span><span class="ld" style="background:#00838f"></span>Critical</span>'
        +'<span><span class="ld" style="background:#FF0000"></span>Delay</span>'
        +'<span><span class="ld" style="background:#f0c419"></span>Saving</span>'
        +'</div>';

    items.forEach(function(r){
        var sc = r.scheduled, dl = r.delay;
        var scPct = (sc/maxVal*100).toFixed(1);
        var dlPct = (dl/maxVal*100).toFixed(1);
        var barCol = r.critical ? '#00838f' : '#37474F';
        var rowCls = 'brow';
        var tipLines = [];
        if (r.start_date)    tipLines.push('Planned Start:  ' + fmtDate(r.start_date));
        if (r.end_date)      tipLines.push('Planned End:    ' + fmtDate(r.end_date));
        if (r.proj_end_date) tipLines.push('Projected End:  ' + fmtDate(r.proj_end_date));
        else                 tipLines.push('Actual End:     ' + (r.actual_end_date ? fmtDate(r.actual_end_date) : 'ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â'));
        if (r.duration_days) tipLines.push('Planned Dur:    ' + r.duration_days + ' days');
        if (r.startDelayed)  tipLines.push('Start Delay:    ' + r.startDelayDays + ' days');
        else if (dl > 0)     tipLines.push('Delay:          ' + dl + ' days');
        else if (dl < 0)     tipLines.push('Saving:         ' + Math.abs(dl) + ' days');
        var tipAttr = tipLines.length ? ' data-tip="' + tipLines.join('&#10;') + '"' : '';
        var isSlack = dl < 0;
        var dlAbs   = Math.abs(dl);
        var dlAbsPct = (dlAbs/maxVal*100).toFixed(1);
        var fmt2 = function(v){ return Number.isInteger(v) ? v : parseFloat(v).toFixed(2); };
        var dispVal = sc > 0 ? (fmt2(sc)
            + (dl > 0  ? '<span style="color:#FF0000;margin-left:3px;">+' + fmt2(dl) + '</span>' : '')
            + (isSlack ? '<span style="color:#27ae60;margin-left:3px;">-' + fmt2(dlAbs) + '</span>' : '')) : '';
        html += '<div class="'+rowCls+'" '+(r.id?'data-aid="'+r.id+'" style="cursor:pointer;display:flex;align-items:center;"':'style="display:flex;align-items:center;"')+'>'
            +'<div class="blbl"'+tipAttr+' style="color:#000;" title="'+r.name+'">'+sh(r.name,30)+'</div>'
            +'<div class="btrk" style="flex:1;">'
            +(sc>0?'<div class="bs" style="width:'+scPct+'%;background:'+barCol+'"></div>':'')
            +(dl>0?'<div class="bs" style="width:'+dlPct+'%;background:#FF0000"></div>':'')
            +(isSlack?'<div class="bs" style="width:'+dlAbsPct+'%;background:#f0c419"></div>':'')
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

    var svg='<svg width="210" height="142" viewBox="0 -12 210 142" xmlns="http://www.w3.org/2000/svg">'
        +(an?'<text x="'+cx+'" y="-2" text-anchor="middle" font-size="10" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +arc(0,1,'#a8d4f5')
        +(f>0?arc(0,f,'#0d1f6e','butt'):'')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#a8d4f5"/>'
        +'<text x="'+(cx-r)+'" y="'+(cy+15)+'" text-anchor="middle" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Start</text>'
        +'<text x="'+(cx+r)+'" y="'+(cy+15)+'" text-anchor="middle" font-size="13" fill="#111" font-family="Barlow Condensed,Arial">Complete '+fm(tq)+(u?' '+u:'')+'</text>'
        +'<text x="'+cx+'" y="'+(cy-26)+'" text-anchor="middle" font-size="18" font-weight="700" fill="#111" font-family="Barlow Condensed,Arial">'+fm(aq)+' '+u+' | '+pct+'%</text>'
        +'<text x="'+cx+'" y="'+(cy-12)+'" text-anchor="middle" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">Achieved</text>'
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

    // Target to date = Elapsed days ÃƒÆ’Ã¢â‚¬â€ (Schedule Qty / B. Duration)
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

    var svg='<svg width="100%" height="100%" viewBox="0 -12 210 146" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet" style="display:block;width:100%;height:auto;">'
        +(an?'<text x="105" y="-2" text-anchor="middle" font-size="10" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +arc(0,1,'#a8aeb8')
        +(fActual>0?arc(0,fActual,'#0d1f6e','butt'):'')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#a8aeb8"/>'
        +(tq>0?'<text x="105" y="40" text-anchor="middle" font-size="10" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Target to Date</text>':'')
        +(tq>0?'<text x="105" y="53" text-anchor="middle" font-size="15" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+fm(compTarget)+(u?' '+u:'')+'</text>':'')
        +'<text x="105" y="66" text-anchor="middle" font-size="10" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Actual</text>'
        +'<text x="105" y="79" text-anchor="middle" font-size="15" font-weight="700" fill="'+(aq<compTarget?'#e53935':aq>compTarget?'#27ae60':'#1a2540')+'" font-family="Barlow Condensed,Arial">'+fm(aq)+(u?' '+u:'')+'</text>'
        +'<text x="8" y="112" text-anchor="start" font-size="11" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Actual</text>'
        +'<text x="8" y="123" text-anchor="start" font-size="12" font-weight="700" fill="'+(aq<compTarget?'#e53935':aq>compTarget?'#27ae60':'#1a2540')+'" font-family="Barlow Condensed,Arial">'+fm(aq)+(u?' '+u:'')+'</text>'
        +'<text x="202" y="112" text-anchor="end" font-size="11" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Schedule Qty</text>'
        +'<text x="202" y="123" text-anchor="end" font-size="12" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+fm(tq)+(u?' '+u:'')+'</text>'
        +'</svg>';

    el.style.flexDirection='';
    el.style.alignItems='';
    el.innerHTML = svg;
}

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Productivity gauge ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
function doProductivity(k) {
    var el = document.getElementById('pd-g3');
    if (!el) return;
    var tp  = +k.target_productivity || 0;
    var ap  = +k.actual_productivity > 0 ? +k.actual_productivity : tp; // when no progress, actual = target
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

    var svg='<svg width="210" height="150" viewBox="0 -12 210 150" xmlns="http://www.w3.org/2000/svg">'
        +(an?'<text x="'+cx+'" y="-2" text-anchor="middle" font-size="10" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +arc(0, 0.5, '#E65100')
        +arc(0.5, 1,  '#00695C')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#dce3ef"/>'
        +'<text x="105" y="40" text-anchor="middle" font-size="10" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Target</text>'
        +'<text x="105" y="53" text-anchor="middle" font-size="15" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+fm(tp)+(u?' '+u+'/d':'')+'</text>'
        +'<text x="105" y="66" text-anchor="middle" font-size="10" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Actual</text>'
        +'<text x="105" y="79" text-anchor="middle" font-size="15" font-weight="700" fill="'+(ap<tp?'#e53935':ap>tp?'#27ae60':'#1a2540')+'" font-family="Barlow Condensed,Arial">'+fm(ap)+(u?' '+u+'/d':'')+'</text>'
        +'<text x="8" y="112" text-anchor="start" font-size="11" fill="#111" font-family="Barlow Condensed,Arial">Low</text>'
        +'<text x="202" y="112" text-anchor="end" font-size="11" fill="#111" font-family="Barlow Condensed,Arial">High</text>'
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

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Tasks tooltip helpers ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
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
    var fmtNum = isDuration ? function(v){ return (+v||0).toFixed(2); } : fm;
    var tip = pdGetTip();
    var cols = ['#d4845a','#f0c419','#8fa3bc','#7c5cbf','#3461b8','#27afc4','#ec407a','#26a69a'];
    var taskRows = '';
    var chartH = 180; var gridLines = 5;
    var maxVal2 = 0;
    items.forEach(function(r) { maxVal2 = Math.max(maxVal2, +(r[valKey])||0, +(r[actKey])||0); });
    if (!maxVal2) maxVal2 = 1;
    var step2 = maxVal2 / gridLines;
    var yAxis = '<div style="display:flex;flex-direction:column;justify-content:space-between;height:'+chartH+'px;padding-right:6px;text-align:right;font-size:10px;color:#8ab4d8;">';
    for (var g=gridLines; g>=0; g--) yAxis += '<span>'+fm(step2*g)+'</span>';
    yAxis += '</div>';
    var grids2 = '<div style="position:absolute;left:0;right:0;top:0;bottom:0;pointer-events:none;">';
    for (var g2=0; g2<=gridLines; g2++) grids2 += '<div style="position:absolute;left:0;right:0;top:'+(((gridLines-g2)/gridLines)*100).toFixed(1)+'%;border-top:1px solid rgba(255,255,255,0.1);"></div>';
    grids2 += '</div>';
    var bars2 = '';
    items.forEach(function(r, i) {
        var tgt = +(r[valKey])||0, act = +(r[actKey])||0;
        var col = cols[i%cols.length];
        var u2 = isDuration ? ' d' : (r.unit ? ' '+shu(r.unit) : '');
        var isOver=act>0&&act>tgt, isUnder=act>0&&act<tgt;
        var actCol = isOver?overCol:(isUnder?underCol:'#e8f0fc');
        var tH = (tgt/maxVal2*100).toFixed(1)+'%', aH = (act/maxVal2*100).toFixed(1)+'%';
        bars2 += '<div style="flex:1;display:flex;gap:2px;align-items:flex-end;height:100%;padding:0 2px;">';
        bars2 += '<div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;height:100%;"><div style="height:'+tH+';background:'+col+';border-radius:3px 3px 0 0;min-height:3px;opacity:0.6;"></div></div>';
        if (act>0) bars2 += '<div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;height:100%;"><div style="height:'+aH+';background:'+(isOver?overCol:isUnder?underCol:col)+';border-radius:3px 3px 0 0;min-height:3px;"></div></div>';
        bars2 += '</div>';
        taskRows += '<tr><td style="white-space:nowrap;"><span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:'+col+';margin-right:8px;vertical-align:middle;"></span>'+sh(r.name||'',30)+'</td>'
            +'<td style="text-align:right;font-weight:700;color:#e8f0fc;white-space:nowrap;">'+fmtNum(tgt)+u2+'</td>'
            +'<td style="text-align:right;font-weight:700;color:'+actCol+';white-space:nowrap;">'+( act>0?fmtNum(act)+u2:'-')+'</td></tr>';
    });
    if (!items.length) {
        tip.innerHTML = '<div class="tip-title">'+title+'</div><div style="font-size:15px;color:#aaa;padding:20px 0;text-align:center">No task data</div>';
    } else {
        tip.innerHTML = '<div class="tip-title">'+title+'</div>'
            +'<div style="display:flex;gap:0;margin-bottom:10px;">'+yAxis
            +'<div style="flex:1;position:relative;"><div style="display:flex;gap:4px;align-items:flex-end;height:'+chartH+'px;background:rgba(255,255,255,0.03);border-left:1px solid rgba(255,255,255,0.15);border-bottom:1px solid rgba(255,255,255,0.15);">'+grids2+bars2+'</div></div></div>'
            +'<div style="font-size:10px;color:#8ab4d8;margin-bottom:6px;"><span style="margin-right:10px;"><span style="display:inline-block;width:16px;height:8px;background:rgba(255,255,255,0.4);border-radius:2px;margin-right:4px;vertical-align:middle;"></span>'+tgtLbl+'</span><span><span style="display:inline-block;width:16px;height:8px;background:#66bb6a;border-radius:2px;margin-right:4px;vertical-align:middle;"></span>Actual</span></div>'
            +'<table><thead><tr><th style="text-align:left;">Task</th><th style="text-align:right;">'+tgtLbl+'</th><th style="text-align:right;">Actual</th></tr></thead><tbody>'+taskRows+'</tbody></table>';
    }
    var gp = anchor.closest ? anchor.closest('.gp') : null;
    var gpRect = gp ? gp.getBoundingClientRect() : anchor.getBoundingClientRect();
    var tipW = Math.min(420, Math.max(320, Math.round(gpRect.width * 1.5)));
    tip.style.width = tipW + 'px';
    tip.style.display = 'block';
    var tipH2 = tip.offsetHeight;
    var tipLeft = Math.max(4, Math.round(gpRect.left) - tipW - 8);
    if (tipLeft < 4) tipLeft = Math.min(4, Math.round(gpRect.right) + 8);
    tip.style.left = tipLeft + 'px';
    tip.style.top  = Math.max(4, Math.round(gpRect.top) - tipH2 - 8) + 'px';
}
function pdHideTipSoon() {
    _pdTipTimer = setTimeout(function() {
        var tip = document.getElementById('pd-tasks-tip');
        if (tip) tip.style.display = 'none';
    }, 150);
}

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Cycle Time gauge ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
function doCycleTime(k) {
    var el = document.getElementById('pd-g4');
    if (!el) return;
    var tc  = +k.target_cycle_time || 0;
    var ac  = +k.actual_cycle_time > 0 ? +k.actual_cycle_time : tc; // when no progress, actual = target
    var an  = sh(k.activity_name || '', 38);
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

    var svg='<svg width="210" height="150" viewBox="0 -12 210 150" xmlns="http://www.w3.org/2000/svg">'
        +(an?'<text x="'+cx+'" y="-2" text-anchor="middle" font-size="10" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +arc(0, 0.5, '#00838f')
        +arc(0.5, 1,  '#FF6D00')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#dce3ef"/>'
        +'<text x="105" y="40" text-anchor="middle" font-size="10" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Target</text>'
        +'<text x="105" y="53" text-anchor="middle" font-size="15" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+fm(tc)+' Hrs</text>'
        +'<text x="105" y="66" text-anchor="middle" font-size="10" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Actual</text>'
        +'<text x="105" y="79" text-anchor="middle" font-size="15" font-weight="700" fill="'+(ac>tc?'#e53935':ac<tc?'#27ae60':'#1a2540')+'" font-family="Barlow Condensed,Arial">'+fm(ac)+' Hrs</text>'
        +'<text x="8" y="112" text-anchor="start" font-size="11" fill="#111" font-family="Barlow Condensed,Arial">Fast</text>'
        +'<text x="202" y="112" text-anchor="end" font-size="11" fill="#111" font-family="Barlow Condensed,Arial">Slow</text>'
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

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Capacity Utilisation gauge ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
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

    var svg='<svg width="210" height="150" viewBox="0 -12 210 150" xmlns="http://www.w3.org/2000/svg">'
        +(an?'<text x="'+cx+'" y="-2" text-anchor="middle" font-size="10" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +arc(0, 1, '#FFD700')
        +(f>0 ? arc(0, f, '#90EE90', 'butt') : '')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#FFD700"/>'
        +'<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Used <tspan font-weight="700">'+fm(used)+' h</tspan></text>'
        +'<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Max <tspan font-weight="700">'+fm(maxVal)+' h</tspan></text>'
        +'<text x="'+cx+'" y="'+(cy-18)+'" text-anchor="middle" font-size="22" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+(maxVal>0?((used/maxVal)*100).toFixed(1):0)+'%</text>'
        +'</svg>';

    el.innerHTML = svg;
}

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Activity Duration bar (pd-g6) ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
function doActivityDuration(k) {
    var el = document.getElementById('pd-g6');
    if (!el) return;
    var bDur       = +k.b_duration || +k.duration || 0;
    var aDur       = +k.projected_duration || 0;
    var elapsed    = +k.elapsed     || 0;
    var startDelay = +k.start_delay || 0;
    var wDone    = +(+k.work_done_pct || 0).toFixed(1);
    var wRemain  = Math.max(0, +(100 - wDone).toFixed(1));
    var baseCol  = k.critical ? '#00838f' : '#37474F';  // teal if critical, deep slate otherwise

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
    var bar = '<div style="position:relative;display:flex;align-items:stretch;height:14px;border-radius:3px;overflow:hidden;">';
    if (isOver) {
        bar += '<div style="width:' + (bDur/maxDur*100).toFixed(1) + '%;background:' + baseCol + ';min-width:3px;' + seg + '"></div>';
        bar += '<div style="width:' + ((aDur-bDur)/maxDur*100).toFixed(1) + '%;background:#e53935;min-width:3px;' + seg + '"></div>';
    } else if (isUnder) {
        bar += '<div style="width:' + (aDur/maxDur*100).toFixed(1) + '%;background:' + baseCol + ';min-width:3px;' + seg + '"></div>';
        bar += '<div style="width:' + ((bDur-aDur)/maxDur*100).toFixed(1) + '%;background:#f0c419;min-width:3px;' + seg + '"></div>';
    } else {
        bar += '<div style="width:100%;background:' + baseCol + ';' + seg + '"></div>';
    }
    bar += '</div>';

    var actName = k.activity_name || '';
    el.innerHTML =
        '<div style="display:flex;flex-direction:column;justify-content:flex-start;height:100%;padding:4px 10px;box-sizing:border-box;gap:2px;overflow:hidden;">'
        + '<div style="display:flex;justify-content:space-between;align-items:baseline;' + fam + 'font-size:10px;font-weight:700;margin-bottom:1px;">'
        + '<span style="color:#1a2540;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;padding-right:6px;">' + (actName||'') + '</span>'
        + '<span style="white-space:nowrap;color:#1a2540;">' + bDur + ' d'
        + (isOver ? ' &nbsp;<span style="color:#e53935;">+' + (aDur-bDur) + ' d</span>' : isUnder ? ' &nbsp;<span style="color:#27ae60;">-' + (bDur-aDur) + ' d</span>' : '')
        + (startDelay > 0 && !isOver && !isUnder ? ' &nbsp;<span style="color:#e53935;">+' + startDelay + ' d</span>' : '')
        + '</span>'
        + '</div>'
        + bar
        + (function(){
            // Projected actual end = actual_start + ceil((elapsed / work_done_qty) × schedule_qty) days
            var actStartStr = k.reported_start_date || k.act_start_date || '';
            var tq2 = +k.target_qty || 0;
            var aq2 = +k.actual_qty || 0;
            var elDays = +k.elapsed || 0;
            if (actStartStr && actStartStr !== '0000-00-00') {
                var today2 = new Date(); today2.setHours(0,0,0,0);
                var sd2 = new Date(actStartStr); sd2.setHours(0,0,0,0);
                elDays = Math.max(0, Math.round((today2 - sd2) / 86400000));
            }
            var projActEndStr = '-';
            var projActEndCol = '#aaa';
            if (actStartStr && actStartStr !== '0000-00-00' && aq2 > 0 && tq2 > 0) {
                var totalDays = Math.ceil((elDays / aq2) * tq2);
                var sd3 = new Date(actStartStr); sd3.setHours(0,0,0,0);
                sd3.setDate(sd3.getDate() + totalDays - 1); // start day counts as day 1
                var mm = sd3.getMonth()+1, dd2 = sd3.getDate(), yy = sd3.getFullYear();
                projActEndStr = (dd2<10?'0'+dd2:dd2) + '-' + (mm<10?'0'+mm:mm) + '-' + yy;
                // Green if finishing earlier than planned, red if later
                var planEndD = k.adj_end_date ? new Date(k.adj_end_date) : null;
                planEndD && planEndD.setHours(0,0,0,0);
                projActEndCol = planEndD
                    ? (sd3 <= planEndD ? '#27ae60' : '#e53935')
                    : '#1a2540';
            }
            return '<div style="' + fam + 'font-size:9px;color:#5a6e8c;margin-top:1px;">'
                + '<div style="display:flex;justify-content:space-between;align-items:center;">'
                + '<span>Plan Start: <b style="color:#1a2540;">' + (fmDate(k.adj_start_date)||'-') + '</b></span>'
                + '<span>Plan End: <b style="color:#1a2540;">' + (fmDate(k.adj_end_date)||'-') + '</b></span>'
                + '</div>'
                + '<div style="display:flex;justify-content:space-between;align-items:center;">'
                + '<span>Act. Start: <b style="color:' + (actStartStr ? '#1a2540' : '#e53935') + ';">' + (actStartStr ? fmDate(actStartStr) : 'Not Started') + '</b></span>'
                + '<span>Act. End: <b style="color:' + projActEndCol + ';">' + projActEndStr + '</b></span>'
                + '</div>'
                + '</div>';
        })()
        + (function(){
            var lgDiv  = '<div style="border-top:1px solid #d0d8e8;margin:2px 0;"></div>';
            var lgRow2 = function(col, lbl, val){
                return '<div style="display:flex;justify-content:space-between;padding:2px 0;">'
                    + '<span><span style="display:inline-block;width:9px;height:9px;background:' + col + ';margin-right:4px;border-radius:2px;vertical-align:middle;"></span>' + lbl + '</span>'
                    + '<b style="color:#1a2540;">' + val + '</b>'
                    + '</div>';
            };

            // 1. Elapsed days = today - activity start date
            var elapsedDays = elapsed; // already computed from act_start_date → last_report_date
            // For display use today if activity has started
            if (k.act_start_date && k.act_start_date !== '0000-00-00') {
                var today = new Date(); today.setHours(0,0,0,0);
                var startD = new Date(k.act_start_date); startD.setHours(0,0,0,0);
                elapsedDays = Math.max(0, Math.round((today - startD) / 86400000));
            }

            // 2. Time required to complete = (schedule_qty - work_done_qty) / actual_productivity
            var tq   = +k.target_qty       || 0;
            var aq   = +k.actual_qty       || 0;
            var prod = +k.actual_productivity || 0;
            var timeToComplete = 0;
            var timeToCompleteStr = '-';
            if (prod > 0) {
                timeToComplete    = Math.ceil(Math.max(0, tq - aq) / prod);
                timeToCompleteStr = timeToComplete + ' d';
            } else if (aq >= tq && tq > 0) {
                timeToCompleteStr = '0 d (Complete)';
            } else {
                timeToCompleteStr = 'N/A (no progress)';
            }

            // 3. Difference = (elapsed + time_to_complete) - planned_duration
            var totalProjected = elapsedDays + timeToComplete;
            var diffD   = (prod > 0) ? Math.round(totalProjected - bDur) : null;
            var diffStr = (diffD !== null)
                ? (diffD > 0 ? '+' + diffD + ' d (overrun)' : diffD < 0 ? diffD + ' d (ahead)' : '0 d (on time)')
                : '-';
            var diffCol = (diffD !== null) ? (diffD > 0 ? '#e53935' : diffD < 0 ? '#27ae60' : '#1a2540') : '#aaa';

            var lgRow2c = function(col, lbl, val){
                return '<div style="display:flex;justify-content:space-between;align-items:center;padding:1px 0;">'
                    + '<span style="font-size:10px;"><span style="display:inline-block;width:8px;height:8px;background:' + col + ';margin-right:3px;border-radius:2px;vertical-align:middle;"></span>' + lbl + '</span>'
                    + '<b style="color:#1a2540;font-size:13px;">' + val + '</b>'
                    + '</div>';
            };
            return '<div style="' + fam + 'font-size:10px;margin-top:4px;border-top:2px solid #d0d8e8;padding-top:3px;">'
                + lgRow2c(baseCol, 'Planned', bDur + ' d')
                + lgRow2c('#5c7bd9', 'Elapsed Days', elapsedDays + ' d')
                + lgRow2c('#e53935', 'Time to Complete', timeToCompleteStr)
                + '<div style="display:flex;justify-content:space-between;align-items:center;padding:1px 0;">'
                + '<span style="font-size:10px;"><span style="display:inline-block;width:8px;height:8px;background:#f0c419;margin-right:3px;border-radius:2px;vertical-align:middle;"></span>Difference</span>'
                + '<b style="color:' + diffCol + ';font-size:13px;">' + diffStr + '</b>'
                + '</div>'
                + '</div>';
        })()
        + '</div>';
}

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ KPI render ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
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

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ SVG Needle Gauge ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
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
        : (trackStyle==='cost')
        ? arc(0,0.5,'#00838f')+arc(0.5,1,'#FF6D00')
        : arc(0,0.5,'#8B0000')+arc(0.5,1,'#81C784');

    var tickSvg = '';
    if (trackStyle!=='cost' && targetFrac && targetFrac>0 && targetFrac<=1){
        var ta = Math.PI*(1-targetFrac);
        var tx1=(cx+(r-10)*Math.cos(ta)).toFixed(1), ty1=(cy-(r-10)*Math.sin(ta)).toFixed(1);
        var tx2=(cx+(r+10)*Math.cos(ta)).toFixed(1), ty2=(cy-(r+10)*Math.sin(ta)).toFixed(1);
        tickSvg='<line x1="'+tx1+'" y1="'+ty1+'" x2="'+tx2+'" y2="'+ty2+'" stroke="#c0392b" stroke-width="3.5"/>';
    }

    var nr=r-15, na=Math.PI*(1-f);
    var nx=(cx+nr*Math.cos(na)).toFixed(1), ny=(cy-nr*Math.sin(na)).toFixed(1);
    var fillCol = (trackStyle==='flat') ? '#0d1f6e' : (trackStyle==='cost') ? '#1b9e8e' : '#1a3a6b';
    var dotCol  = (trackStyle==='flat') ? '#a8d4f5' : '#dce3ef';

    var centreVal = (trackStyle==='cost') ? fmtCost(val) : fm(val);
    var lblSvg='';
    if (lbl1) lblSvg+='<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">'+lbl1+' <tspan font-weight="700">'+v1+'</tspan></text>';
    if (lbl2) lblSvg+='<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">'+lbl2+' <tspan font-weight="700">'+v2+'</tspan></text>';
    var anSvg = actName ? '<text x="'+cx+'" y="135" text-anchor="middle" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">'+actName+'</text>' : '';

    var svg='<svg width="210" height="138" viewBox="0 0 210 138" xmlns="http://www.w3.org/2000/svg">'
        +trackSvg
        +(f>0?arc(0,f,fillCol,(trackStyle==='cost'?'butt':'round')):'')
        +tickSvg
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="'+dotCol+'"/>'
        +'<text x="'+(cx-r)+'" y="'+(cy+15)+'" text-anchor="middle" font-size="12" fill="#5a6e8c" font-family="Barlow Condensed,Arial">0</text>'
        +(trackStyle!=='cost'?'<text x="'+(cx+r)+'" y="'+(cy+15)+'" text-anchor="middle" font-size="12" fill="#5a6e8c" font-family="Barlow Condensed,Arial">'+fm(maxVal)+'</text>':'')
        +'<text x="'+cx+'" y="'+(cy-18)+'" text-anchor="middle" font-size="22" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+centreVal+'</text>'
        +lblSvg+anSvg
        +'</svg>';

    el.innerHTML += svg;
}

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Resource Capacity ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â task quantities ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
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

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Cause of Delay ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
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

// ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Task Productivity ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
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
