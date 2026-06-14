/* Performance Dashboard — popup modal */
(function(){
'use strict';

var _ch = {};
var _loaded = false;
var _groups    = [];   // iow_groups rows
var _iow_items = [];   // wbsscheduleitems rows with group_id
var _all       = [];   // all scheduleactivities

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
    var iowCostMap = {};
    _iow_items.forEach(function(iow){
        var sid = String(iow.id);
        iowCostMap[sid] = _all
            .filter(function(a){ return String(a.scheduleitem_id) === sid; })
            .reduce(function(s, a){ return s + (+a.activity_cost || 0); }, 0);
    });

    // Group costs = sum of IOW costs under each group
    var groupItems = _groups.map(function(g){
        var cost = _iow_items
            .filter(function(i){ return String(i.group_id) === String(g.id); })
            .reduce(function(s, i){ return s + (iowCostMap[String(i.id)] || 0); }, 0);
        return {name: g.name, cost: cost, id: g.id};
    });

    // Project Cost on Completion = sum of group costs
    var totalCost = groupItems.reduce(function(s, g){ return s + g.cost; }, 0);

    renderCostBars('cd-c2', [{name: _cdProjectName || 'Project', cost: totalCost, id: 0}], null);
    renderCostBars('cd-c1', groupItems, filterByGroupCd);
    if (_groups.length) filterByGroupCd(_groups[0].id);
}

function filterByGroupCd(groupId){
    var gid = String(groupId);
    var filtered = _iow_items.filter(function(i){ return String(i.group_id) === gid; });
    if (!filtered.length) filtered = _iow_items;

    // IOW Cost — sum activity costs per IOW
    var iowItems = filtered.map(function(iow){
        var sid = String(iow.id);
        var cost = _all
            .filter(function(a){ return String(a.scheduleitem_id) === sid; })
            .reduce(function(s, a){ return s + (+a.activity_cost || 0); }, 0);
        return {name: iow.name, cost: cost, id: iow.id};
    });
    renderCostBars('cd-c3', iowItems, filterByIowCd);

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
    renderActivityCostBars('cd-c4', toCostBarItems(fOngoing.concat(fUpcoming)), loadCdActivityData);
    var firstAct = fOngoing.length ? fOngoing[0] : (fUpcoming.length ? fUpcoming[0] : null);
    if (firstAct) loadCdActivityData(firstAct.id);
    else {
        var el = document.getElementById('cd-c6');
        if (el) el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">Select an activity</div>';
    }
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
            renderCdResourceConsumption(d.items || [], d.activity_name || '', +d.last_report_qty || 0);
            renderCdUnitCostOfActivity(d.items || [], d.activity_name || '');
            renderCdCostOfActivity(d.items || [], d.activity_name || '', +d.last_report_qty || 0);
            renderCdCostOnCompletion(d.items || [], d.activity_name || '', +d.activity_qty || 0);
            renderCdValueOfWorkDone(d);
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
    var palette = ['#90caf9','#b0bec5'];
    if (!items.length){
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No estimate data</div>';
        return;
    }

    function fmR(v){ return v >= 1000000 ? (v/1000000).toFixed(1)+'M' : v >= 1000 ? (v/1000).toFixed(1)+'K' : (+v).toFixed(0); }

    var maxVal = 0;
    items.forEach(function(r){
        var est = +r.rate || 0;
        var act = (r.actual_unit_cost != null) ? +r.actual_unit_cost : 0;
        maxVal = Math.max(maxVal, est, act);
    });
    if (!maxVal) maxVal = 1;

    var bars = '', labels = '';
    items.forEach(function(r, i){
        var est  = +r.rate || 0;
        var act  = (r.actual_unit_cost != null) ? +r.actual_unit_cost : null;
        var col  = palette[i % palette.length];
        var unit = r.unit ? '/' + shu(r.unit) : '';
        var isMaterial = act !== null;

        var barHtml = '';
        if (!isMaterial) {
            // Non-material or no GRN data — single plain bar
            var pct = Math.max(est / maxVal * 100, 12).toFixed(1) + '%';
            barHtml = '<div class="resb" style="height:'+pct+';min-height:30px;background:'+col+';'
                    + 'display:flex;align-items:center;justify-content:center;overflow:hidden;">'
                    + '<span style="font-family:\'Nunito\',sans-serif;font-size:13px;color:#111;white-space:nowrap;">'+fmR(est)+(unit?'<span style="font-size:10px;color:rgba(0,0,0,.7)">'+unit+'</span>':'')+'</span>'
                    + '</div>';
        } else if (act > est) {
            // Overrun: base bar (est) + red extension (act - est) on top
            var basePct = Math.max(est / maxVal * 100, 8).toFixed(1) + '%';
            var extPct  = ((act - est) / maxVal * 100).toFixed(1) + '%';
            barHtml = '<div class="resb" style="height:'+extPct+';background:#e53935;'
                    + 'display:flex;align-items:center;justify-content:center;overflow:hidden;">'
                    + '<span style="font-family:\'Nunito\',sans-serif;font-size:11px;color:#fff;white-space:nowrap;">+'+fmR(act - est)+'</span>'
                    + '</div>'
                    + '<div class="resb" style="height:'+basePct+';min-height:20px;background:'+col+';'
                    + 'display:flex;align-items:center;justify-content:center;overflow:hidden;">'
                    + '<span style="font-family:\'Nunito\',sans-serif;font-size:13px;color:#111;white-space:nowrap;">'+fmR(act)+(unit?'<span style="font-size:10px;color:rgba(0,0,0,.7)">'+unit+'</span>':'')+'</span>'
                    + '</div>';
        } else {
            // Saving: actual bar + yellow top segment up to estimated level
            var actPct  = Math.max(act / maxVal * 100, 8).toFixed(1) + '%';
            var savePct = ((est - act) / maxVal * 100).toFixed(1) + '%';
            barHtml = '<div class="resb" style="height:'+savePct+';background:#fdd835;'
                    + 'display:flex;align-items:center;justify-content:center;overflow:hidden;">'
                    + '<span style="font-family:\'Nunito\',sans-serif;font-size:11px;color:#333;white-space:nowrap;">-'+fmR(est - act)+'</span>'
                    + '</div>'
                    + '<div class="resb" style="height:'+actPct+';min-height:20px;background:'+col+';'
                    + 'display:flex;align-items:center;justify-content:center;overflow:hidden;">'
                    + '<span style="font-family:\'Nunito\',sans-serif;font-size:13px;color:#111;white-space:nowrap;">'+fmR(act)+(unit?'<span style="font-size:10px;color:rgba(0,0,0,.7)">'+unit+'</span>':'')+'</span>'
                    + '</div>';
        }

        bars += '<div class="rescol" style="height:100%;display:flex;flex-direction:column;justify-content:flex-end;">'
              + barHtml
              + '</div>';

        var typ = r.type_name || '';
        var nm  = r.name || '';
        labels += '<div class="reslbl" style="color:#111;line-height:1.3;">'
                + '<span style="font-size:11px;color:#1a2a3a;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + sh(typ, 12) + '</span>'
                + '<span style="font-size:10px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + sh(nm, 12) + '</span>'
                + '</div>';
    });

    el.innerHTML = '<div class="resbars">' + bars + '</div>'
        + '<div class="reslabels">' + labels + '</div>'
        + (actName ? '<div class="resfoot">' + sh(actName, 32) + '</div>' : '');
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
        +arc(0,1,'#a8d4f5')
        +(f>0?arc(0,f,'#0d1f6e','butt'):'')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#a8d4f5"/>'
        +'<text x="'+(cx-r)+'" y="'+(cy+15)+'" text-anchor="middle" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Start</text>'
        +'<text x="'+(cx+r)+'" y="'+(cy+15)+'" text-anchor="middle" font-size="13" fill="#111" font-family="Barlow Condensed,Arial">Complete '+fm(tq)+(u?' '+u:'')+'</text>'
        +'<text x="'+cx+'" y="'+(cy-20)+'" text-anchor="middle" font-size="18" font-weight="700" fill="#111" font-family="Barlow Condensed,Arial">'+fm(aq)+(u?' '+u:'')+' | '+pct+'%</text>'
        +'<text x="'+cx+'" y="'+(cy-5)+'" text-anchor="middle" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">Achieved</text>'
        +(an?'<text x="'+cx+'" y="128" text-anchor="middle" font-size="13" fill="#111" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +'</svg>';
    el.innerHTML = svg;
}

function renderCdUnitCostOfActivity(items, actName){
    var el = document.getElementById('cd-g5');
    if (!el) return;

    var unitCost = 0;
    items.forEach(function(r){ unitCost += (+r.res_qty || 0) * (+r.rate || 0); });

    if (!unitCost){
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No estimate data</div>';
        return;
    }

    var maxVal = unitCost * 2;
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
        +arc(0, 0.5, '#81C784')
        +arc(0.5, 1,  '#E57373')
        +(f>0?arc(0,f,'#1a3a6b','butt'):'')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#dce3ef"/>'
        +'<text x="'+cx+'" y="'+(cy-20)+'" text-anchor="middle" font-size="18" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+fmCost(unitCost)+'</text>'
        +'<text x="'+cx+'" y="'+(cy-5)+'" text-anchor="middle" font-size="11" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Rate</text>'
        +'<text x="8" y="112" text-anchor="start" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">Actual <tspan font-weight="700">—</tspan></text>'
        +'<text x="202" y="112" text-anchor="end" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">Rate <tspan font-weight="700">'+fmCost(unitCost)+'</tspan></text>'
        +(an?'<text x="'+cx+'" y="128" text-anchor="middle" font-size="13" fill="#111" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +'</svg>';

    el.innerHTML = svg;
}

function renderCdCostOfActivity(items, actName, workDone){
    var el = document.getElementById('cd-g2');
    if (!el) return;

    var unitCost = 0;
    items.forEach(function(r){ unitCost += (+r.res_qty || 0) * (+r.rate || 0); });
    var cost = unitCost * workDone;

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
        +arc(0, 0.5, '#81C784')
        +arc(0.5, 1,  '#E57373')
        +(f>0?arc(0,f,'#1a3a6b','butt'):'')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#dce3ef"/>'
        +'<text x="'+cx+'" y="'+(cy-20)+'" text-anchor="middle" font-size="18" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+fmCost(cost)+'</text>'
        +'<text x="'+cx+'" y="'+(cy-5)+'" text-anchor="middle" font-size="11" fill="#5a6e8c" font-family="Barlow Condensed,Arial">Cost</text>'
        +'<text x="8" y="112" text-anchor="start" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">Actual <tspan font-weight="700">—</tspan></text>'
        +'<text x="202" y="112" text-anchor="end" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">WD Cost <tspan font-weight="700">'+fmCost(cost)+'</tspan></text>'
        +(an?'<text x="'+cx+'" y="128" text-anchor="middle" font-size="13" fill="#111" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +'</svg>';

    el.innerHTML = svg;
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
        +arc(0, 0.5, '#81C784')
        +arc(0.5, 1,  '#E57373')
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

function renderCdResourceConsumption(items, actName, lastQty){
    var el = document.getElementById('cd-c7');
    if (!el) return;
    var palette = ['#a5d6a7','#80cbc4'];
    if (!items.length){
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No estimate data</div>';
        return;
    }
    var maxVal = 0;
    items.forEach(function(r){ maxVal = Math.max(maxVal, +r.res_qty || 0); });
    if (!maxVal) maxVal = 1;
    var bars = '', labels = '';
    items.forEach(function(r, i){
        var val  = +r.res_qty || 0;
        var barPct = Math.max(val / maxVal * 100, 12).toFixed(1) + '%';
        var col  = palette[i % palette.length];
        var disp = val >= 1000000 ? (val / 1000000).toFixed(1) + 'M'
                 : val >= 1000    ? (val / 1000).toFixed(1) + 'K'
                 : val % 1 === 0  ? val.toFixed(0)
                 : val.toFixed(2);
        var unit = r.unit ? shu(r.unit) : '';
        bars += '<div class="rescol" style="height:100%;display:flex;flex-direction:column;justify-content:flex-end;">'
              + '<div class="resb" style="height:' + barPct + ';min-height:30px;background:' + col + ';'
              +   'display:flex;flex-direction:column;align-items:center;justify-content:center;overflow:hidden;">'
              + '<span style="font-family:\'Nunito\',sans-serif;font-size:13px;font-weight:400;'
              +   'color:#111;white-space:nowrap;line-height:1.2;">' + disp + '</span>'
              + (unit ? '<span style="font-family:\'Nunito\',sans-serif;font-size:10px;font-weight:400;'
              +   'color:rgba(0,0,0,.7);white-space:nowrap;">' + unit + '</span>' : '')
              + '</div>'
              + '</div>';
        var typ = r.type_name || '';
        var nm  = r.name || '';
        labels += '<div class="reslbl" style="color:#111;line-height:1.3;">'
                + '<span style="font-size:11px;color:#1a2a3a;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + sh(typ, 12) + '</span>'
                + '<span style="font-size:10px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + sh(nm, 12) + '</span>'
                + '</div>';
    });
    el.innerHTML = '<div class="resbars">' + bars + '</div>'
        + '<div class="reslabels">' + labels + '</div>'
        + (actName ? '<div class="resfoot">' + sh(actName, 32) + '</div>' : '');
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
            // Ongoing: new formula — use planned start as base if activity started late
            if (r.spr_start_date && r.spr_start_date !== '0000-00-00'
                && r.last_report_date && +r.cumulated_qty > 0 && +r.quantity > 0) {
                var baseDate = (r.start_date && r.start_date < r.spr_start_date)
                               ? r.start_date : r.spr_start_date;
                var elapsed = Math.max(1, (new Date(r.last_report_date) - new Date(baseDate)) / 86400000);
                var projDur = Math.round(elapsed / +r.cumulated_qty * +r.quantity);
                if (projDur > planned && planned > 0) {
                    sc = planned;
                    dl = projDur - planned;
                } else {
                    sc = projDur || planned;
                    dl = 0;
                }
            } else {
                sc = planned;
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
        return {
            name:        r.name,
            cost:        +r.activity_cost || 0,
            actual_cost: +r.actual_cost   || 0,
            id:          r.id
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

function renderCostBars(containerId, items, onRowClick){
    var el = document.getElementById(containerId);
    if (!el) return;
    var maxVal = 0;
    items.forEach(function(r){ maxVal = Math.max(maxVal, r.cost); });
    if (!maxVal) maxVal = 1;
    var html = '';
    items.forEach(function(r){
        var pct = (r.cost / maxVal * 100).toFixed(1);
        var col = '#001f5b';
        var disp = fmtCost(r.cost);
        html += '<div class="brow" data-aid="'+r.id+'" style="cursor:pointer">'
              + '<div class="blbl" title="'+r.name+'">'+sh(r.name,30)+'</div>'
              + '<div class="btrk">'
              + (r.cost > 0
                    ? '<div class="bs" style="width:'+pct+'%;background:'+col+';color:#fff;font-size:13px;">'+disp+'</div>'
                    : '<div class="bs" style="width:2%;background:#ccc">—</div>')
              + '</div></div>';
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
        var est = r.cost || 0, act = r.actual_cost || 0;
        var rowMax = Math.max(est, act, 1);
        var bar = '';
        if (act === 0) {
            bar = est > 0
                ? '<div class="bs" style="width:100%;background:#001f5b;color:#fff;font-size:13px;">'+fmtCost(est)+'</div>'
                : '<div class="bs" style="width:2%;background:#ccc">—</div>';
        } else if (act > est) {
            var estPct  = (est / rowMax * 100).toFixed(1);
            var overPct = ((act - est) / rowMax * 100).toFixed(1);
            bar = '<div class="bs" style="width:'+estPct+'%;background:#001f5b;color:#fff;font-size:13px;">'+fmtCost(est)+'</div>'
                + '<div class="bs" style="width:'+overPct+'%;background:#c62828;color:#fff;font-size:13px;">+'+fmtCost(act-est)+'</div>';
        } else {
            var actPct  = (act  / rowMax * 100).toFixed(1);
            var savePct = ((est - act) / rowMax * 100).toFixed(1);
            bar = '<div class="bs" style="width:'+actPct+'%;background:#001f5b;color:#fff;font-size:13px;">'+fmtCost(act)+'</div>'
                + '<div class="bs" style="width:'+savePct+'%;background:#2e7d32;color:#fff;font-size:13px;">-'+fmtCost(est-act)+'</div>';
        }
        html += '<div class="brow" data-aid="'+r.id+'" style="cursor:pointer">'
              + '<div class="blbl" title="'+r.name+'">'+sh(r.name,30)+'</div>'
              + '<div class="btrk">'+bar+'</div></div>';
    });
    el.innerHTML = html;
    $(el).find('.brow[data-aid]').on('click', function(){
        if (onRowClick) onRowClick($(this).data('aid'));
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
        html+='<div style="width:'+bPct+'%;background:#1a6fbf;min-width:3px;'+lbl+';color:#fff">'+budgeted+' d</div>';
        html+='<div style="width:'+rPct+'%;background:#e53935;min-width:3px;'+lbl+';color:#fff">+'+(actual-budgeted)+' d</div>';
    } else if (actual>0 && actual<budgeted){
        var aPct=(actual/maxVal*100).toFixed(1);
        var yPct=((budgeted-actual)/maxVal*100).toFixed(1);
        html+='<div style="width:'+aPct+'%;background:#1a6fbf;min-width:3px;'+lbl+';color:#fff">'+actual+' d</div>';
        html+='<div style="width:'+yPct+'%;background:#f0c419;min-width:3px;'+lbl+';color:#1a2540">-'+(budgeted-actual)+' d</div>';
    } else {
        var boPct=(budgeted/maxVal*100).toFixed(1);
        html+='<div style="width:'+boPct+'%;background:#1a6fbf;min-width:3px;'+lbl+';color:#fff">'+budgeted+' d</div>';
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
        +'<span><span style="display:inline-block;width:10px;height:8px;background:#1a6fbf;margin-right:3px;border-radius:1px"></span>Budgeted</span>'
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
        +'<span><span class="ld" style="background:#555555"></span>Normal</span>'
        +'<span><span class="ld" style="background:#1a6fbf"></span>Critical</span>'
        +'<span><span class="ld" style="background:#FF0000"></span>Delay</span>'
        +'</div>';

    items.forEach(function(r){
        var sc = r.scheduled, dl = r.delay;
        var scPct = (sc/maxVal*100).toFixed(1);
        var dlPct = (dl/maxVal*100).toFixed(1);
        var barCol = r.critical ? '#1a6fbf' : '#555555';
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
        html += '<div class="'+rowCls+'"'+tipAttr+' '+(r.id?'data-aid="'+r.id+'" style="cursor:pointer"':'')+'>'
            +'<div class="blbl" title="'+r.name+'">'+sh(r.name,30)+'</div>'
            +'<div class="btrk">'
            +(sc>0?'<div class="bs" style="width:'+scPct+'%;background:'+barCol+'">'+sc+'</div>':'')
            +(dl>0?'<div class="bs" style="width:'+dlPct+'%;background:#FF0000">'+dl+'</div>':'')
            +'</div>'
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
function pdShowTasksTip(items, anchor) {
    clearTimeout(_pdTipTimer);
    items = items || [];
    var tip = pdGetTip();
    var cols = ['#d4845a','#f0c419','#8fa3bc','#7c5cbf','#3461b8','#27afc4','#ec407a','#26a69a'];
    var bars = '', taskRows = '';
    var segPct = function(v, tot) { return tot > 0 ? (v / tot * 100).toFixed(1) + '%' : '0%'; };
    items.forEach(function(r, i) {
        var tgt = +(r.val) || 0, act = +(r.actual) || 0;
        var col = cols[i % cols.length];
        var u = r.unit ? ' ' + shu(r.unit) : '';
        var isOver  = act > 0 && act > tgt;
        var isUnder = act > 0 && act < tgt;
        var actCol  = isOver ? '#66bb6a' : (isUnder ? '#ef5350' : '#e8f0fc');
        if (isOver) {
            bars += '<div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;height:100%;">'
                + '<div style="height:' + segPct(act - tgt, act) + ';background:#66bb6a;border-radius:3px 3px 0 0;min-height:3px;"></div>'
                + '<div style="height:' + segPct(tgt, act) + ';background:' + col + ';min-height:4px;"></div>'
                + '</div>';
        } else if (isUnder) {
            bars += '<div style="flex:1;display:flex;flex-direction:column;justify-content:flex-end;height:100%;">'
                + '<div style="height:' + segPct(tgt - act, tgt) + ';background:rgba(239,83,80,.3);border-radius:3px 3px 0 0;min-height:3px;"></div>'
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
            + '<td style="text-align:right;font-weight:700;color:#e8f0fc;white-space:nowrap;">' + fm(tgt) + u + '</td>'
            + '<td style="text-align:right;font-weight:700;color:' + actCol + ';white-space:nowrap;">' + (act > 0 ? fm(act) + u : '—') + '</td>'
            + '</tr>';
    });
    if (!items.length) {
        tip.innerHTML = '<div class="tip-title">Task Productivity</div><div style="font-size:17px;color:#aaa;padding:20px 0;text-align:center">No task data</div>';
    } else {
        tip.innerHTML = '<div class="tip-title">Task Productivity</div>'
            + '<div style="display:flex;gap:8px;align-items:flex-end;height:140px;margin-bottom:10px;padding-bottom:4px;border-bottom:1px solid rgba(255,255,255,.12);">' + bars + '</div>'
            + '<table>'
            +   '<thead><tr>'
            +     '<th style="text-align:left;">Task</th>'
            +     '<th style="text-align:right;">Target</th>'
            +     '<th style="text-align:right;">Actual</th>'
            +   '</tr></thead>'
            +   '<tbody>' + taskRows + '</tbody>'
            + '</table>';
    }
    var gp = anchor.closest ? anchor.closest('.gp') : null;
    var gpRect = gp ? gp.getBoundingClientRect() : anchor.getBoundingClientRect();
    var tipW = Math.round(gpRect.width * 0.95);
    var centerX = gpRect.left + gpRect.width / 2;
    var left = Math.max(4, Math.min(centerX - tipW / 2, window.innerWidth - tipW - 4));
    tip.style.width = tipW + 'px';
    tip.style.display = 'block';
    var tipH = tip.offsetHeight;
    tip.style.left = left + 'px';
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
        +arc(0, 0.5, '#81C784')
        +arc(0.5, 1,  '#E57373')
        +'<line x1="'+cx+'" y1="'+cy+'" x2="'+nx+'" y2="'+ny+'" stroke="#333" stroke-width="3" stroke-linecap="round"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="6" fill="#555"/>'
        +'<circle cx="'+cx+'" cy="'+cy+'" r="2.5" fill="#dce3ef"/>'
        +'<text x="10" y="122" text-anchor="start" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Actual <tspan font-weight="700">'+fm(ac)+' d</tspan></text>'
        +'<text x="200" y="122" text-anchor="end" font-size="14" fill="#111" font-family="Barlow Condensed,Arial">Target <tspan font-weight="700">'+fm(tc)+' d</tspan></text>'
        +'<text x="'+cx+'" y="'+(cy-18)+'" text-anchor="middle" font-size="18" font-weight="700" fill="#1a2540" font-family="Barlow Condensed,Arial">'+fm(ac)+' Days</text>'
        +(an?'<text x="'+cx+'" y="135" text-anchor="middle" font-size="12" fill="#111" font-family="Barlow Condensed,Arial">'+an+'</text>':'')
        +'</svg>';

    el.innerHTML = svg;
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

// ── KPI render ────────────────────────────────────────────────────────────────
function doKpi(k){
    var u = k.unit||'', an = sh(k.activity_name||'',38);

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
