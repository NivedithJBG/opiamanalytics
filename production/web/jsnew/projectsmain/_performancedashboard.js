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
    if (typeof window.popupBringToFront === 'function') window.popupBringToFront('cd-modal');
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

// Cashflow money-value formatter: values under 1 Crore (1,00,00,000)
// display in Lakhs, values at or above 1 Crore display in Crores — the
// unit label switches automatically with the value. Input is raw
// rupees; returns {value, unit} for display, and always positive
// magnitudes (dial panels treat everything as absolute value, per
// explicit request — negative underlying balances should not flip
// which unit is shown or display a minus sign).
function _cfFmtMoney(rupees){
    var v = Math.abs(+rupees || 0);
    if (v >= 10000000) {
        return { value: v / 10000000, unit: 'Crores' };
    }
    return { value: v / 100000, unit: 'Lakhs' };
}

// Converts a manually-typed editable target (T.WIP, W.Capital, Contract
// Income target, etc.) into raw rupees, using the SAME magnitude rule as
// _cfFmtMoney: the number the user typed is itself read as Lakhs when
// under 100, Crores when 100 or more — e.g. entering "19" means 19
// Lakhs, not 19 Crores. This replaces the earlier "targets are always
// entered directly in Crores" convention.
function _cfEnteredToRupees(n){
    var v = +n || 0;
    return Math.abs(v) < 100 ? v * 100000 : v * 10000000;
}

// Opening the Cashflow Dashboard fires ~16-20 AJAX requests at once (one
// or two per panel: a target fetch plus a totals fetch). The session
// backend here (Yii CacheSession over FileCache — see config/web.php)
// has no per-request locking to serialize concurrent reads/writes of
// the login session the way PHP's native file session handler does —
// under a burst of ~20 simultaneous requests all reading the same
// session, a request can occasionally come back with an empty/stale
// identity, which surfaced as panels flashing to zero, targets
// disappearing, or the user appearing logged out, all without any
// error being logged server-side (the failure happens at the session
// layer, before Yii's own error handler runs). Routing every Cashflow
// AJAX call through this small concurrency-limited queue (max 3 in
// flight at once) keeps requests from piling up against the session
// simultaneously, without adding any noticeable delay to the dashboard
// opening (~20 quick requests at 3-wide still finish in well under a
// second on a normal connection).
var _cfAjaxQueue = [];
var _cfAjaxInFlight = 0;
// FileCache (the session backend here) has no locking at all — even 3
// concurrent writers hitting the SAME session's cache file at once was
// still corrupting/losing the session (observed live: an entire
// dashboard-open burst coming back 403 "Login Required" end to end,
// immediately after a fresh login). Fully serial (1 at a time) is the
// only concurrency level that's actually safe against this backend.
var _CF_AJAX_MAX_CONCURRENT = 1;
function _cfQueueAjax(opts){
    _cfAjaxQueue.push(opts);
    _cfDrainAjaxQueue();
}
function _cfDrainAjaxQueue(){
    while (_cfAjaxInFlight < _CF_AJAX_MAX_CONCURRENT && _cfAjaxQueue.length) {
        var opts = _cfAjaxQueue.shift();
        _cfAjaxInFlight++;
        var userComplete = opts.complete;
        opts.complete = function(){
            _cfAjaxInFlight--;
            if (typeof userComplete === 'function') userComplete.apply(this, arguments);
            _cfDrainAjaxQueue();
        };
        $.ajax(opts);
    }
}

// Shared target fetch with a single retry: none of the Cashflow panels'
// AJAX calls had error handling, so a transient network/session hiccup
// on cfgettarget silently left a panel's target at its just-reset 0
// value with no retry and no visual difference from a genuine 0 target
// — this is what made targets appear to randomly "vanish" on some
// refreshes. onOk is only invoked on an actual successful response
// (retried once on failure or a non-'No' error before giving up).
function _cfFetchTarget(metricKey, onOk){
    function attempt(retriesLeft){
        _cfQueueAjax({
            type: 'POST', url: '../projectsmain/cfgettarget', dataType: 'json',
            data: { metric_key: metricKey },
            success: function(d){
                if (d && d.error === 'No') { onOk(d); }
                else if (retriesLeft > 0) { setTimeout(function(){ attempt(retriesLeft - 1); }, 800); }
            },
            error: function(){
                if (retriesLeft > 0) { setTimeout(function(){ attempt(retriesLeft - 1); }, 800); }
            }
        });
    }
    attempt(1);
}

// ── Cashflow Dashboard ──────────────────────────────────────────────────
// Placeholder shell: "Bank" panel renders a grey bar with no real data yet.
// No backend call — wire up a real data source when one exists.
//
// renderCfBars() resets every panel's state to zero/blank before firing
// its fetches, so calling it again while a PREVIOUS call's fetches are
// still in flight piles a fresh batch of ~15 concurrent AJAX requests on
// top of the last one — flashing every dial back to zero and letting a
// still-loading generation's callbacks race each other, which is what
// produced the "vanishing / wrong / zero / needle in a strange
// position" symptoms. Guarding on "was the modal already open" (a CSS
// class) turned out to be unreliable — if the class was ever left on
// (e.g. navigating away without using the explicit close button) the
// dashboard would silently stop refreshing altogether and just keep
// showing whatever was last (possibly incompletely) rendered. Guard on
// an explicit in-flight flag instead: every click always requests a
// fresh load, but a click that arrives while a load is still in
// progress is ignored rather than piling up another concurrent batch.
var _cfLoading = false;
$(document).on('click', '.cashflow-dashboard-btn', function(e){
    e.preventDefault();
    $('#cf-modal, #cf-bk').addClass('cf-open');
    if (typeof window.popupBringToFront === 'function') window.popupBringToFront('cf-modal');
    if (!_cfLoading) { renderCfBars(); }
});
$(document).on('click', '#cf-close, #cf-bk', function(){
    $('#cf-modal, #cf-bk').removeClass('cf-open');
});

// Bumped on every renderCfBars() call (i.e. every time the Cashflow
// Dashboard is opened). Each panel's AJAX success callback captures the
// generation it was fired under and checks it's still current before
// writing into shared state — otherwise a slow response from a PREVIOUS
// open (e.g. rapid close/reopen) can land after a newer, faster
// response and overwrite a correctly-fetched target back to stale/zero
// data, which is what made targets appear to randomly vanish on
// refresh.
var _cfLoadGen = 0;

function renderCfBars(){
    _cfLoadGen++;
    _cfLoading = true;
    // _cfFetchTarget retries once after an 800ms delay on failure, so a
    // full load cycle can legitimately take a bit over 800ms; 2.5s gives
    // every panel's fetches (including a retry) comfortable room to
    // settle before the next click is allowed to start a new load.
    setTimeout(function(){ _cfLoading = false; }, 2500);
    _cfDials.forEach(function(d){ renderCfDial(d.elId, d.metricKey, d.unit, d.unitFontSize, d.reverseColors); });
    renderCfBankDial();
    renderCfWipDial();
    renderCfBillsReceivableDial();
    renderCfCurrentAssetsDial();
    renderCfCurrentLiabilitiesDial();
    renderCfProjExpDial();
    renderCfNetInvDial();
    renderCfContribMarginDial();
    renderCfFreeCashDial();
    renderCfRoiDial();
}

// Investments panel — single flat dial with an editable target (yellow
// marker at the dial's center, half of the full 2x scale — same
// treatment as the Bank panel's Allocated marker), plus an orange
// overlay arc (0 to Actual, on the same scale) showing the Current
// Assets figure's progress along the dial — same overlay treatment as
// the Bank panel's orange Availed arc. Needle position also represents
// the actual value: sum of DEBIT current balances of every account head
// under the Current Assets subgroup, for the selected project — see
// actionCfcurrentassets. The actual value itself is shown as a number
// in the dial's center (with the Lakhs/Crores unit beneath it). Target
// is saved to cf_dashboard_targets under metric_key "investments_target"
// via cfsavetarget/cfgettarget.
var _cfInvestState = { target: 0, actual: 0, editing: false };

function _cfInvestDialSvg(target, actual){
    var targetCr = _cfEnteredToRupees(target) / 10000000;
    var maxV = targetCr > 0 ? targetCr * 2 : 1;
    var f = Math.max(0, Math.min(1, actual / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    // Up to target (first half of the 0-2x scale) = dark blue; beyond
    // target (second half) = orange. Target always sits at the dial's
    // midpoint (see targetF below), so this split is fixed, not
    // actual-dependent.
    var arcPath = arc(0, 0.5, '#0d3f7a') + arc(0.5, 1, '#b5540a');
    var targetF = 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var fmt = _cfFmtMoney(Math.max(Math.abs(targetCr), Math.abs(actual)) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + targetLine
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 30) + '" text-anchor="middle" font-size="24" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + (actual * divisor).toFixed(2) + '</text>'
        + '<text x="' + cx + '" y="' + (cy - 14) + '" text-anchor="middle" font-size="14" font-weight="700" fill="#5a5a5a" font-family="Barlow Condensed,Arial">' + fmt.unit + '</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawInvestDial(){
    var el = document.getElementById('cf-currentassets');
    if (!el) return;
    el.style.justifyContent = 'center';
    el.style.cursor = 'pointer';
    el.title = 'Click to see account breakdown';
    var s = _cfInvestState;
    var targetCr = _cfEnteredToRupees(s.target) / 10000000;
    var fmt = _cfFmtMoney(Math.max(Math.abs(s.actual), Math.abs(targetCr)) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    var targetCell = s.editing
        ? '<input class="cf-invest-input" data-field="target" type="number" step="any" value="' + s.target + '" style="width:54px;font-size:13px;padding:0 3px;border:1px solid #b0bec5;border-radius:2px;color:#5a5a5a;font-weight:700;">'
        : '<span style="font-size:13px;color:#5a5a5a;font-weight:700;">' + (targetCr * divisor).toFixed(2) + '</span>';
    // Actual legend colour always matches whichever arc half the needle
    // is currently pointing into (dark blue up to target, orange beyond).
    var maxV = targetCr > 0 ? targetCr * 2 : 1;
    var needleCol = (s.actual / maxV) <= 0.5 ? '#0d3f7a' : '#b5540a';
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfInvestDialSvg(s.target, s.actual)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:#111;">Actual: <span style="font-size:13px;color:' + needleCol + ';font-weight:700;">' + (s.actual * divisor).toFixed(2) + '</span></span>'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>Target: ' + targetCell + ' <button class="cf-invest-pencil" title="Edit Target" style="background:none;border:none;cursor:pointer;padding:0 2px;font-size:12px;">&#9998;</button></span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfCurrentAssetsDial(){
    var gen = _cfLoadGen;
    _cfInvestState = { target: 0, actual: 0, editing: false };
    _cfDrawInvestDial();
    _cfFetchTarget('investments_target', function(d){
        if (gen === _cfLoadGen) { _cfInvestState.target = +d.target || 0; _cfDrawInvestDial(); }
    });
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfcurrentassets', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                // Backend returns the total in raw rupees; state keeps it
                // as a Crores-scale float, same as every other panel.
                _cfInvestState.actual = (+d.total || 0) / 10000000;
                _cfDrawInvestDial();
            }
        }
    });
}

// Net Investment panel — single flat dial, same visual treatment as the
// Investments dial (_cfInvestDialSvg is generic and reused as-is), but
// with NO editable target of its own: it reuses the Bank-Working
// Capital target (metric_key "bank_wcapital") since Net Investment and
// Working Capital represent the same capital commitment viewed from
// different angles (see actionCfnetinvestment's comment). Actual =
// Investments + current open-cycle Work In Progress + the cumulative
// WIP cost already closed out against past Bills Receivable events —
// i.e. every rupee ever put into the project's cost base that hasn't
// yet come back as realized Contract Income, regardless of which panel
// it's currently displayed under.
var _cfNetInvState = { target: 0, actual: 0 };

function _cfDrawNetInvDial(){
    var el = document.getElementById('cf-netinvestment');
    if (!el) return;
    el.style.justifyContent = 'center';
    el.style.cursor = 'pointer';
    el.title = 'Click to see per-cycle breakdown';
    var s = _cfNetInvState;
    var targetCr = _cfEnteredToRupees(s.target) / 10000000;
    var fmt = _cfFmtMoney(Math.max(Math.abs(s.actual), Math.abs(targetCr)) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    var maxV = targetCr > 0 ? targetCr * 2 : 1;
    var needleCol = (s.actual / maxV) <= 0.5 ? '#0d3f7a' : '#b5540a';
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfInvestDialSvg(s.target, s.actual)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:#111;">Actual: <span style="font-size:13px;color:' + needleCol + ';font-weight:700;">' + (s.actual * divisor).toFixed(2) + '</span></span>'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>Target: <span style="font-size:13px;color:#5a5a5a;font-weight:700;">' + (targetCr * divisor).toFixed(2) + '</span></span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfNetInvDial(){
    var gen = _cfLoadGen;
    _cfNetInvState = { target: 0, actual: 0 };
    _cfDrawNetInvDial();
    _cfFetchTarget('bank_wcapital', function(d){
        if (gen === _cfLoadGen) { _cfNetInvState.target = +d.target || 0; _cfDrawNetInvDial(); }
    });
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfnetinvestment', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfNetInvState.actual = (+d.total || 0) / 10000000;
                _cfDrawNetInvDial();
            }
        }
    });
}

// Total Investment breakdown popup — same draggable/resizable vertical
// bar chart window as the other breakdown popups (see
// _cfInvestBreakdownChartHtml/_cfShowInvestBreakdown above), one bar per
// WIP accrual cycle: each closed cycle's CLOSING Total Investment value,
// plus the current open cycle valued as of today (see
// actionCfnetinvestmentbreakdown). X-axis labels are the month/year each
// cycle closed ("Current" for the open cycle) rather than account names.
// Also shows the average of all cycle values alongside the Total.
function _cfRedrawNetInvBreakdown(){
    var s = window._cfNetInvBreakdownState;
    if (!s) return;
    var chartH = Math.max(120, $('#cf-netinv-breakdown-win').height() - 130);
    var r = _cfInvestBreakdownChartHtml(s.accounts, chartH);
    $('#cf-netinv-breakdown-unit').html(
        '<span style="font-weight:700;color:#251840;">Amount in ' + r.unit + '</span>'
        + '<span style="float:right;font-weight:700;color:#251840;">Average: ' + _cfIndianComma(s.average / (r.unit === 'Crores' ? 10000000 : 100000)) + ' ' + r.unit + '</span>'
    );
    $('#cf-netinv-breakdown-chart').html(r.body);
}

function _cfShowNetInvBreakdown(accounts, average){
    $('#cf-netinv-breakdown-win').remove();
    window._cfNetInvBreakdownState = { accounts: accounts, average: average };

    var winW = 640, winH = 380, MIN_W = 340, MIN_H = 240;
    var left = Math.max(10, (window.innerWidth - winW) / 2);
    var top = Math.max(10, (window.innerHeight - winH) / 2);

    var rsHandles = ['n','s','e','w','ne','nw','se','sw'];
    var rsHtml = '';
    for (var h = 0; h < rsHandles.length; h++) {
        rsHtml += '<div class="cf-netinv-rs" data-dir="' + rsHandles[h] + '" style="position:absolute;';
        var d = rsHandles[h];
        if (d === 'n') rsHtml += 'top:-4px;left:10px;right:10px;height:10px;cursor:n-resize;';
        else if (d === 's') rsHtml += 'bottom:-4px;left:10px;right:10px;height:10px;cursor:s-resize;';
        else if (d === 'e') rsHtml += 'right:-4px;top:10px;bottom:10px;width:10px;cursor:e-resize;';
        else if (d === 'w') rsHtml += 'left:-4px;top:10px;bottom:10px;width:10px;cursor:w-resize;';
        else if (d === 'ne') rsHtml += 'right:-4px;top:-4px;width:14px;height:14px;cursor:ne-resize;';
        else if (d === 'nw') rsHtml += 'left:-4px;top:-4px;width:14px;height:14px;cursor:nw-resize;';
        else if (d === 'se') rsHtml += 'right:-4px;bottom:-4px;width:14px;height:14px;cursor:se-resize;';
        else if (d === 'sw') rsHtml += 'left:-4px;bottom:-4px;width:14px;height:14px;cursor:sw-resize;';
        rsHtml += 'z-index:20;background:transparent;"></div>';
    }

    var html = '<div id="cf-netinv-breakdown-win" style="position:fixed;left:' + left + 'px;top:' + top + 'px;width:' + winW + 'px;height:' + winH + 'px;min-width:' + MIN_W + 'px;min-height:' + MIN_H + 'px;background:#fff;border-radius:6px;box-shadow:0 8px 30px rgba(0,0,0,.35);z-index:200600;display:flex;flex-direction:column;overflow:hidden;">'
        + rsHtml
        + '<div id="cf-netinv-breakdown-hdr" style="flex:none;display:flex;justify-content:space-between;align-items:center;padding:10px 16px;background:#251840;cursor:move;user-select:none;">'
        + '<span style="font-family:Barlow Condensed,Arial;font-size:15px;font-weight:700;color:#fff;">Total Investment — Per-Cycle Breakdown</span>'
        + '<button id="cf-netinv-breakdown-close" style="background:none;border:none;font-size:20px;cursor:pointer;color:#fff;line-height:1;">&times;</button>'
        + '</div>'
        + '<div id="cf-netinv-breakdown-unit" style="flex:none;font-family:Barlow Condensed,Arial;font-size:12px;color:#5a6e8c;padding:8px 16px 0;overflow:hidden;"></div>'
        + '<div style="flex:1;min-width:0;min-height:0;overflow-x:hidden;overflow-y:auto;padding:10px 16px 16px;box-sizing:border-box;">'
        + '<div id="cf-netinv-breakdown-chart" style="min-width:0;"></div>'
        + '</div>'
        + '</div>';
    $('body').append(html);
    _cfRedrawNetInvBreakdown();

    // Drag (header) + resize (8 handles) — same mechanics as the other
    // breakdown windows.
    var win = document.getElementById('cf-netinv-breakdown-win');
    var hdr = document.getElementById('cf-netinv-breakdown-hdr');
    var _action = null, _sx = 0, _sy = 0, _ox = 0, _oy = 0, _ow = 0, _oh = 0;

    function onDown(e, dir){
        var r = win.getBoundingClientRect();
        _action = dir; _sx = e.clientX; _sy = e.clientY;
        _ox = r.left; _oy = r.top; _ow = r.width; _oh = r.height;
        e.preventDefault();
        if (dir !== 'drag') e.stopPropagation();
    }
    function onMove(e){
        if (!_action) return;
        e.preventDefault();
        var dx = e.clientX - _sx, dy = e.clientY - _sy;
        if (_action === 'drag') {
            var x = Math.max(0, Math.min(_ox + dx, window.innerWidth - win.offsetWidth));
            var y = Math.max(0, Math.min(_oy + dy, window.innerHeight - win.offsetHeight));
            win.style.left = x + 'px'; win.style.top = y + 'px';
        } else {
            var l = _ox, t = _oy, w = _ow, h = _oh;
            if (_action.indexOf('e') > -1) { w = Math.max(MIN_W, _ow + dx); }
            if (_action.indexOf('s') > -1) { h = Math.max(MIN_H, _oh + dy); }
            if (_action.indexOf('w') > -1) { var nw = Math.max(MIN_W, _ow - dx); l = _ox + (_ow - nw); w = nw; }
            if (_action.indexOf('n') > -1) { var nh = Math.max(MIN_H, _oh - dy); t = _oy + (_oh - nh); h = nh; }
            win.style.left = l + 'px'; win.style.top = t + 'px';
            win.style.width = w + 'px'; win.style.height = h + 'px';
            _cfRedrawNetInvBreakdown();
        }
    }
    function onUp(){ _action = null; }

    if (typeof window.bindDragTouch === 'function') {
        window.bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-netinv-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-netinv-rs').each(function(){
            var dir = $(this).data('dir');
            window.bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        window.bindDragTouch(document, 'mousemove', onMove, { passive: false });
        window.bindDragTouch(document, 'mouseup', onUp);
    } else {
        hdr.addEventListener('mousedown', function(e){ if (e.target.id !== 'cf-netinv-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-netinv-rs').each(function(){
            var dir = $(this).data('dir');
            this.addEventListener('mousedown', function(e){ onDown(e, dir); });
        });
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    }
}
$(document).on('click', '#cf-netinvestment', function(e){
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfnetinvestmentbreakdown', dataType: 'json',
        success: function(d){
            if (d && d.error === 'No') { _cfShowNetInvBreakdown(d.accounts || [], +d.average || 0); }
        }
    });
});
$(document).on('click', '#cf-netinv-breakdown-close', function(){ $('#cf-netinv-breakdown-win').remove(); });

// Investments breakdown popup — clicking anywhere on the panel (except
// the pencil/target controls, which have their own click handling)
// fetches every account currently contributing to the Investments
// total (see actionCfcurrentassetsbreakdown, same pool/filter as
// actionCfcurrentassets) and shows them as a vertical bar chart, tallest
// bar first. A fixed-position overlay appended to <body> (not nested
// inside the draggable/resizable #cf-modal) so it isn't clipped by the
// modal's own bounds; closes on backdrop click or its own close button.
// Distinct colour per bar — cycled if there are more accounts than
// colours.
var _cfInvestBarColours = ['#0d3f7a', '#b5540a', '#2e9e44', '#7a2e8a', '#c62828', '#00838f', '#8a6d00', '#455a64'];

// Shortens a full account name to a short chart-axis label — takes the
// first 1-2 significant words, dropping generic trailing words like
// "and", "Construction", "Designers", etc. so long names like "Hebron
// Industries and Construction" become "Hebron Industries" rather than
// wrapping across several lines under a narrow bar.
function _cfShortLabel(name){
    var words = (name || '').replace(/\(.*?\)/g, '').trim().split(/\s+/);
    var stop = ['and', 'of', 'the', 'for'];
    var out = [];
    for (var i = 0; i < words.length && out.length < 2; i++) {
        if (stop.indexOf(words[i].toLowerCase()) === -1) out.push(words[i]);
    }
    return out.join(' ') || name;
}

// Renders just the chart markup (called both on first open and on
// every resize, since the bar heights/gridlines are pixel-computed
// against the current chart area size).
function _cfInvestBreakdownChartHtml(accounts, chartH, unitMode){
    // unitMode: 'money' (default) formats via _cfFmtMoney (Lakhs/Crores,
    // auto-scaled) — 'days' shows the raw day-count values as-is, no
    // unit conversion (used by the Cash-to-Cash Cycle breakdown, whose
    // bars are day-counts, not rupee amounts).
    unitMode = unitMode || 'money';
    var fmtVal = unitMode === 'days'
        ? function(v){ return { value: +v || 0, unit: 'Days' }; }
        : _cfFmtMoney;

    var maxAmt = 0, sumAmt = 0;
    for (var i = 0; i < accounts.length; i++) {
        if (accounts[i].amount > maxAmt) maxAmt = accounts[i].amount;
        sumAmt += accounts[i].amount;
    }
    if (maxAmt <= 0) maxAmt = 1;
    var fmtMax = fmtVal(maxAmt);
    var axisMax = fmtMax.value;
    var unit = fmtMax.unit;
    var totalDisp = unitMode === 'days' ? sumAmt.toFixed(2) : (sumAmt / (unit === 'Crores' ? 10000000 : 100000)).toFixed(2);
    var niceMax = axisMax <= 0 ? 1 : Math.ceil(axisMax * 1.05 * 100) / 100;
    var axisW = 34;

    // Exactly 5 evenly-spaced gridlines (0 to niceMax) — a normal chart
    // axis, not one line per account value (that produced 10+ crowded,
    // overlapping labels when accounts had many different amounts).
    // Drawn as a repeating background-image on the chart container
    // itself (not separate absolutely-positioned sibling divs) — a
    // background paints behind ALL of the container's content
    // unconditionally, so there's no risk of it being hidden behind a
    // flex item's own stacking context the way a positioned sibling
    // element could be.
    var stepCount = 5;
    var stepPx = chartH / stepCount;
    var gridBg = 'repeating-linear-gradient(to top, #aab2c0 0, #aab2c0 1px, transparent 1px, transparent ' + stepPx.toFixed(2) + 'px)';
    var gridLabelsHtml = '';
    for (var g = 0; g <= stepCount; g++) {
        var gv = Math.round((niceMax / stepCount) * g * 100) / 100;
        var gy = chartH - g * stepPx;
        gridLabelsHtml += '<div style="position:absolute;left:-' + (axisW + 4) + 'px;top:' + (gy - 7).toFixed(1) + 'px;width:' + axisW + 'px;text-align:right;font-size:10px;color:#8a94a3;font-family:Barlow Condensed,Arial;">' + gv.toFixed(2) + '</div>';
    }

    // Bar column width flexes to fill the available chart width instead
    // of a fixed px size — so the whole set of bars always fits without
    // needing a horizontal scrollbar, however many accounts there are.
    var barsHtml = '';
    for (var j = 0; j < accounts.length; j++) {
        var acct = accounts[j];
        var fmt = fmtVal(acct.amount);
        var barPx = Math.max(2, (fmt.value / niceMax) * chartH);
        var col = _cfInvestBarColours[j % _cfInvestBarColours.length];
        var hasMembers = acct.members && acct.members.length;
        barsHtml += '<div class="cf-breakdown-bar-col" data-idx="' + j + '" style="display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:' + chartH + 'px;flex:1 1 0;min-width:0;position:relative;' + (hasMembers ? 'cursor:pointer;' : '') + '">'
            + '<div style="font-size:11px;font-weight:700;color:#251840;margin-bottom:4px;white-space:nowrap;">' + fmt.value.toFixed(2) + '</div>'
            + '<div style="width:70%;max-width:34px;height:' + barPx.toFixed(1) + 'px;background:' + col + ';border-radius:2px 2px 0 0;"></div>'
            + '</div>';
    }
    var labelsHtml = '';
    for (var k = 0; k < accounts.length; k++) {
        labelsHtml += '<div style="flex:1 1 0;min-width:0;font-size:11px;color:#333;text-align:center;line-height:1.2;padding:0 3px;box-sizing:border-box;overflow:hidden;text-overflow:ellipsis;">' + _cfShortLabel(accounts[k].name) + '</div>';
    }

    // Always render the gridlines/axis frame, even with zero bars — an
    // empty chart still shows the reader the scale/labels; only the
    // bars themselves are conditional, plus a centered placeholder
    // message when there's nothing to plot yet.
    var emptyMsgHtml = accounts.length ? '' : '<div style="position:absolute;left:0;right:0;top:50%;transform:translateY(-50%);text-align:center;color:#8a94a3;font-size:12px;font-family:Barlow Condensed,Arial;">No data yet</div>';
    var body = '<div style="display:flex;margin-left:' + (axisW + 4) + 'px;min-width:0;box-sizing:border-box;">'
        + '<div class="cf-breakdown-chart-area" style="position:relative;flex:1;min-width:0;display:flex;align-items:flex-end;gap:6px;height:' + chartH + 'px;background-image:' + gridBg + ';">'
        + gridLabelsHtml
        + barsHtml
        + emptyMsgHtml
        + '</div>'
        + '</div>'
        // X-axis line + the account names as its value labels.
        + '<div style="margin-left:' + (axisW + 4) + 'px;border-top:2px solid #251840;"></div>'
        + '<div style="display:flex;margin-left:' + (axisW + 4) + 'px;min-width:0;box-sizing:border-box;gap:6px;margin-top:6px;">' + labelsHtml + '</div>';
    return { unit: unit, total: totalDisp, body: body };
}

// Per-bar hover tooltip for breakdown popups (WIP's group bars only —
// each bar's underlying `members` array, when present, lists the
// individual account heads that make up that group's total). Shared by
// both the Investments and WIP breakdown windows since they use the
// same bar markup; Investments bars simply have no `members` and so
// never trigger a tooltip. Positioned against the bar's own bounding
// rect (not the mouse cursor) so it can't lose the hover before being
// reached — same anchoring approach as #pd-res-tip elsewhere in this
// file.
// Formats a number with Indian digit grouping (comma after 3 digits from
// the decimal point, then every 2 digits thereafter — e.g. 1234567.89
// becomes "12,34,567.89"), 2 decimal places fixed.
function _cfIndianComma(n){
    n = +n || 0;
    var neg = n < 0; n = Math.abs(n);
    var parts = n.toFixed(2).split('.');
    var intPart = parts[0];
    var lastThree = intPart.length > 3 ? intPart.slice(-3) : intPart;
    var rest = intPart.length > 3 ? intPart.slice(0, -3) : '';
    if (rest) { lastThree = ',' + lastThree; }
    var withCommas = rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + lastThree;
    return (neg ? '-' : '') + withCommas + '.' + parts[1];
}

(function(){
    function _cfBreakdownTip(){
        var t = document.getElementById('cf-breakdown-tip');
        if (!t) {
            t = document.createElement('div');
            t.id = 'cf-breakdown-tip';
            t.style.cssText = 'display:none;position:fixed;z-index:200700;background:#fff;border:1px solid #b0bec5;border-radius:4px;box-shadow:0 4px 16px rgba(0,0,0,.25);padding:14px 16px;font-family:Barlow Condensed,Arial;max-width:900px;min-width:420px;';
            document.body.appendChild(t);
        }
        return t;
    }
    $(document).on('mouseenter', '.cf-breakdown-bar-col', function(e){
        var win = $(this).closest('[id$="-breakdown-win"]');
        var stateKeys = { 'cf-wip-breakdown-win': '_cfWipBreakdownState', 'cf-projexp-breakdown-win': '_cfProjExpBreakdownState', 'cf-invest-breakdown-win': '_cfInvestBreakdownState', 'cf-currliab-breakdown-win': '_cfCurrLiabBreakdownState', 'cf-netinv-breakdown-win': '_cfNetInvBreakdownState', 'cf-billsrecv-breakdown-win': '_cfBillsRecvBreakdownState', 'cf-c2c-breakdown-win': '_cfC2cBreakdownState', 'cf-ci-breakdown-win': '_cfContractIncomeBreakdownState' };
        var stateKey = stateKeys[win.attr('id')];
        if (!stateKey) return;
        var s = window[stateKey];
        if (!s) return;
        var idx = +this.dataset.idx;
        var acct = s.accounts[idx];
        if (!acct || !acct.members || !acct.members.length) return;
        var tip = _cfBreakdownTip();
        var rowsHtml = '';
        for (var i = 0; i < acct.members.length; i++) {
            rowsHtml += '<tr><td style="padding:6px 20px 6px 10px;font-size:15px;color:#333;white-space:nowrap;border:1px solid #d5dae2;">' + $('<div>').text(acct.members[i].name || '').html() + '</td>'
                + '<td style="padding:6px 10px;font-size:15px;font-weight:700;color:#251840;text-align:right;border:1px solid #d5dae2;">' + _cfIndianComma(acct.members[i].amount) + '</td></tr>';
        }
        tip.innerHTML = '<div style="font-size:16px;font-weight:700;color:#251840;margin-bottom:6px;border-bottom:1px solid #e0e4ea;padding-bottom:6px;">' + $('<div>').text(acct.name || '').html() + '</div>'
            + '<table style="border-collapse:collapse;width:100%;">'
            + '<thead><tr><th style="padding:6px 20px 6px 10px;font-size:14px;color:#5a6e8c;text-align:left;border:1px solid #d5dae2;background:#f4f6fa;">Account Head</th><th style="padding:6px 10px;font-size:14px;color:#5a6e8c;text-align:right;border:1px solid #d5dae2;background:#f4f6fa;">Amount</th></tr></thead>'
            + '<tbody>' + rowsHtml + '</tbody></table>';
        var rect = this.getBoundingClientRect();
        tip.style.display = 'block';
        var tw = tip.offsetWidth || 200, th = tip.offsetHeight || 60;
        var left = Math.min(rect.left, window.innerWidth - tw - 8);
        var top = rect.top - th - 8;
        if (top < 4) top = rect.bottom + 8;
        tip.style.left = Math.max(4, left) + 'px';
        tip.style.top = top + 'px';
    }).on('mouseleave', '.cf-breakdown-bar-col', function(){
        var tip = document.getElementById('cf-breakdown-tip');
        if (tip) tip.style.display = 'none';
    });
})();

function _cfRedrawInvestBreakdown(){
    var s = window._cfInvestBreakdownState;
    if (!s) return;
    var chartH = Math.max(120, $('#cf-invest-breakdown-win').height() - 130);
    var r = _cfInvestBreakdownChartHtml(s.accounts, chartH);
    $('#cf-invest-breakdown-unit').html(
        '<span style="font-weight:700;color:#251840;">Amount in ' + r.unit + '</span>'
        + '<span style="float:right;font-weight:700;color:#251840;">Total: ' + r.total + ' ' + r.unit + '</span>'
    );
    $('#cf-invest-breakdown-chart').html(r.body);
}

function _cfShowInvestBreakdown(accounts){
    $('#cf-invest-breakdown-win').remove();
    window._cfInvestBreakdownState = { accounts: accounts };

    var winW = 640, winH = 380, MIN_W = 340, MIN_H = 240;
    var left = Math.max(10, (window.innerWidth - winW) / 2);
    var top = Math.max(10, (window.innerHeight - winH) / 2);

    var rsHandles = ['n','s','e','w','ne','nw','se','sw'];
    var rsHtml = '';
    for (var h = 0; h < rsHandles.length; h++) {
        rsHtml += '<div class="cf-invest-rs" data-dir="' + rsHandles[h] + '" style="position:absolute;';
        var d = rsHandles[h];
        if (d === 'n') rsHtml += 'top:-4px;left:10px;right:10px;height:10px;cursor:n-resize;';
        else if (d === 's') rsHtml += 'bottom:-4px;left:10px;right:10px;height:10px;cursor:s-resize;';
        else if (d === 'e') rsHtml += 'right:-4px;top:10px;bottom:10px;width:10px;cursor:e-resize;';
        else if (d === 'w') rsHtml += 'left:-4px;top:10px;bottom:10px;width:10px;cursor:w-resize;';
        else if (d === 'ne') rsHtml += 'right:-4px;top:-4px;width:14px;height:14px;cursor:ne-resize;';
        else if (d === 'nw') rsHtml += 'left:-4px;top:-4px;width:14px;height:14px;cursor:nw-resize;';
        else if (d === 'se') rsHtml += 'right:-4px;bottom:-4px;width:14px;height:14px;cursor:se-resize;';
        else if (d === 'sw') rsHtml += 'left:-4px;bottom:-4px;width:14px;height:14px;cursor:sw-resize;';
        rsHtml += 'z-index:20;background:transparent;"></div>';
    }

    var html = '<div id="cf-invest-breakdown-win" style="position:fixed;left:' + left + 'px;top:' + top + 'px;width:' + winW + 'px;height:' + winH + 'px;min-width:' + MIN_W + 'px;min-height:' + MIN_H + 'px;background:#fff;border-radius:6px;box-shadow:0 8px 30px rgba(0,0,0,.35);z-index:200600;display:flex;flex-direction:column;overflow:hidden;">'
        + rsHtml
        + '<div id="cf-invest-breakdown-hdr" style="flex:none;display:flex;justify-content:space-between;align-items:center;padding:10px 16px;background:#251840;cursor:move;user-select:none;">'
        + '<span style="font-family:Barlow Condensed,Arial;font-size:15px;font-weight:700;color:#fff;">Investments — Account Breakdown</span>'
        + '<button id="cf-invest-breakdown-close" style="background:none;border:none;font-size:20px;cursor:pointer;color:#fff;line-height:1;">&times;</button>'
        + '</div>'
        + '<div id="cf-invest-breakdown-unit" style="flex:none;font-family:Barlow Condensed,Arial;font-size:12px;color:#5a6e8c;padding:8px 16px 0;overflow:hidden;"></div>'
        + '<div style="flex:1;min-width:0;min-height:0;overflow-x:hidden;overflow-y:auto;padding:10px 16px 16px;box-sizing:border-box;">'
        + '<div id="cf-invest-breakdown-chart" style="min-width:0;"></div>'
        + '</div>'
        + '</div>';
    $('body').append(html);
    _cfRedrawInvestBreakdown();

    // Drag (header) + resize (8 handles) — same mechanics as #cf-modal
    // and every other floating window in this app (see bindDragTouch).
    var win = document.getElementById('cf-invest-breakdown-win');
    var hdr = document.getElementById('cf-invest-breakdown-hdr');
    var _action = null, _sx = 0, _sy = 0, _ox = 0, _oy = 0, _ow = 0, _oh = 0;

    function onDown(e, dir){
        var r = win.getBoundingClientRect();
        _action = dir; _sx = e.clientX; _sy = e.clientY;
        _ox = r.left; _oy = r.top; _ow = r.width; _oh = r.height;
        e.preventDefault();
        if (dir !== 'drag') e.stopPropagation();
    }
    function onMove(e){
        if (!_action) return;
        e.preventDefault();
        var dx = e.clientX - _sx, dy = e.clientY - _sy;
        if (_action === 'drag') {
            var x = Math.max(0, Math.min(_ox + dx, window.innerWidth - win.offsetWidth));
            var y = Math.max(0, Math.min(_oy + dy, window.innerHeight - win.offsetHeight));
            win.style.left = x + 'px'; win.style.top = y + 'px';
        } else {
            var l = _ox, t = _oy, w = _ow, h = _oh;
            if (_action.indexOf('e') > -1) { w = Math.max(MIN_W, _ow + dx); }
            if (_action.indexOf('s') > -1) { h = Math.max(MIN_H, _oh + dy); }
            if (_action.indexOf('w') > -1) { var nw = Math.max(MIN_W, _ow - dx); l = _ox + (_ow - nw); w = nw; }
            if (_action.indexOf('n') > -1) { var nh = Math.max(MIN_H, _oh - dy); t = _oy + (_oh - nh); h = nh; }
            win.style.left = l + 'px'; win.style.top = t + 'px';
            win.style.width = w + 'px'; win.style.height = h + 'px';
            _cfRedrawInvestBreakdown();
        }
    }
    function onUp(){ _action = null; }

    if (typeof window.bindDragTouch === 'function') {
        window.bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-invest-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-invest-rs').each(function(){
            var dir = $(this).data('dir');
            window.bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        window.bindDragTouch(document, 'mousemove', onMove, { passive: false });
        window.bindDragTouch(document, 'mouseup', onUp);
    } else {
        hdr.addEventListener('mousedown', function(e){ if (e.target.id !== 'cf-invest-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-invest-rs').each(function(){
            var dir = $(this).data('dir');
            this.addEventListener('mousedown', function(e){ onDown(e, dir); });
        });
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    }
}
$(document).on('click', '#cf-currentassets', function(e){
    if ($(e.target).closest('.cf-invest-pencil, .cf-invest-input').length) return;
    $.ajax({
        type: 'POST', url: '../projectsmain/cfcurrentassetsbreakdown', dataType: 'json',
        success: function(d){
            if (d && d.error === 'No') { _cfShowInvestBreakdown(d.accounts || []); }
        }
    });
});
$(document).on('click', '#cf-invest-breakdown-close', function(){ $('#cf-invest-breakdown-win').remove(); });

$(document).on('click', '.cf-invest-pencil', function(e){
    e.preventDefault(); e.stopPropagation();
    var s = _cfInvestState;
    if (s.editing) {
        var val = +$('.cf-invest-input[data-field="target"]').val() || 0;
        s.target = val;
        s.editing = false;
        _cfDrawInvestDial();
        $.ajax({
            type: 'POST', url: '../projectsmain/cfsavetarget', dataType: 'json',
            data: { metric_key: 'investments_target', target_value: val },
            // Re-render every panel once the save confirms — other
            // panels (e.g. Current Liabilities) can depend on this
            // target and had no way to know it just changed, since each
            // panel only fetches its dependencies once, when the
            // dashboard opens.
            success: function(){ renderCfBars(); }
        });
    } else {
        s.editing = true;
        _cfDrawInvestDial();
    }
});
$(document).on('click', '.cf-invest-input', function(e){ e.stopPropagation(); });

// Current Liabilities panel — single flat teal dial (one colour, no
// target-split, since the target here isn't a user-entered threshold
// like the other panels — it's DERIVED: Target Bills Receivable minus
// Target Net WIP, both pulled from cf_dashboard_targets. Needle/actual
// is the sum of net balances of every account head under the Current
// Liabilities subgroup, scoped to the selected project's own
// transactions — see actionCfcurrentliabilities. If the derived target
// comes out negative (Net WIP's target set higher than Bills
// Receivable's), it's treated as 0 for the dial's scale, same as every
// other dial's target<=0 fallback.
var _cfCurrLiabState = { billsRecvTarget: 0, wipTarget: 0, actual: 0 };

function _cfCurrLiabDialSvg(targetCr, actual){
    var maxV = targetCr > 0 ? targetCr * 2 : 1;
    var f = Math.max(0, Math.min(1, actual / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    var arcPath = arc(0, 1, '#3d7a6e');
    var targetF = 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var fmt = _cfFmtMoney(Math.max(Math.abs(targetCr), Math.abs(actual)) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + targetLine
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 30) + '" text-anchor="middle" font-size="24" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + (actual * divisor).toFixed(2) + '</text>'
        + '<text x="' + cx + '" y="' + (cy - 14) + '" text-anchor="middle" font-size="14" font-weight="700" fill="#5a5a5a" font-family="Barlow Condensed,Arial">' + fmt.unit + '</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawCurrLiabDial(){
    var el = document.getElementById('cf-currentliabilities');
    if (!el) return;
    el.style.justifyContent = 'center';
    el.style.cursor = 'pointer';
    el.title = 'Click to see account breakdown';
    var s = _cfCurrLiabState;
    // Each target is entered independently (Lakhs if < 100, Crores if >=
    // 100), so both must be converted to raw rupees BEFORE subtracting —
    // subtracting the two raw entered numbers first (e.g. 150 meaning
    // 150 Crores minus 19 meaning 19 Lakhs) and only then reinterpreting
    // the difference via the same magnitude rule would silently corrupt
    // the result whenever the two targets sit on different sides of the
    // 100 threshold.
    var billsRecvRupees = _cfEnteredToRupees(s.billsRecvTarget);
    var wipRupees = _cfEnteredToRupees(s.wipTarget);
    var targetCr = Math.max(0, billsRecvRupees - wipRupees) / 10000000;
    var fmt = _cfFmtMoney(Math.max(Math.abs(s.actual), Math.abs(targetCr)) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfCurrLiabDialSvg(targetCr, s.actual)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:#111;">Actual: <span style="font-size:13px;color:#3d7a6e;font-weight:700;">' + (s.actual * divisor).toFixed(2) + '</span></span>'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>Target: <span style="font-size:13px;color:#3d7a6e;font-weight:700;">' + (targetCr * divisor).toFixed(2) + '</span></span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfCurrentLiabilitiesDial(){
    var gen = _cfLoadGen;
    _cfCurrLiabState = { billsRecvTarget: 0, wipTarget: 0, actual: 0 };
    _cfDrawCurrLiabDial();
    _cfFetchTarget('bills_receivable_target', function(d){
        if (gen === _cfLoadGen) { _cfCurrLiabState.billsRecvTarget = +d.target || 0; _cfDrawCurrLiabDial(); }
    });
    _cfFetchTarget('wip_target', function(d){
        if (gen === _cfLoadGen) { _cfCurrLiabState.wipTarget = +d.target || 0; _cfDrawCurrLiabDial(); }
    });
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfcurrentliabilities', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfCurrLiabState.actual = (+d.total || 0) / 10000000;
                _cfDrawCurrLiabDial();
            }
        }
    });
}

// Current Liabilities breakdown popup — same draggable/resizable
// vertical bar chart window as Investments (see
// _cfInvestBreakdownChartHtml/_cfShowInvestBreakdown above), one bar per
// contributing party/account (see actionCfcurrentliabilitiesbreakdown,
// same pool/filter as actionCfcurrentliabilities). No hover breakdown
// needed here — each bar is already a single account, same as
// Investments.
function _cfRedrawCurrLiabBreakdown(){
    var s = window._cfCurrLiabBreakdownState;
    if (!s) return;
    var chartH = Math.max(120, $('#cf-currliab-breakdown-win').height() - 130);
    var r = _cfInvestBreakdownChartHtml(s.accounts, chartH);
    $('#cf-currliab-breakdown-unit').html(
        '<span style="font-weight:700;color:#251840;">Amount in ' + r.unit + '</span>'
        + '<span style="float:right;font-weight:700;color:#251840;">Total: ' + r.total + ' ' + r.unit + '</span>'
    );
    $('#cf-currliab-breakdown-chart').html(r.body);
}

function _cfShowCurrLiabBreakdown(accounts){
    $('#cf-currliab-breakdown-win').remove();
    window._cfCurrLiabBreakdownState = { accounts: accounts };

    var winW = 640, winH = 380, MIN_W = 340, MIN_H = 240;
    var left = Math.max(10, (window.innerWidth - winW) / 2);
    var top = Math.max(10, (window.innerHeight - winH) / 2);

    var rsHandles = ['n','s','e','w','ne','nw','se','sw'];
    var rsHtml = '';
    for (var h = 0; h < rsHandles.length; h++) {
        rsHtml += '<div class="cf-currliab-rs" data-dir="' + rsHandles[h] + '" style="position:absolute;';
        var d = rsHandles[h];
        if (d === 'n') rsHtml += 'top:-4px;left:10px;right:10px;height:10px;cursor:n-resize;';
        else if (d === 's') rsHtml += 'bottom:-4px;left:10px;right:10px;height:10px;cursor:s-resize;';
        else if (d === 'e') rsHtml += 'right:-4px;top:10px;bottom:10px;width:10px;cursor:e-resize;';
        else if (d === 'w') rsHtml += 'left:-4px;top:10px;bottom:10px;width:10px;cursor:w-resize;';
        else if (d === 'ne') rsHtml += 'right:-4px;top:-4px;width:14px;height:14px;cursor:ne-resize;';
        else if (d === 'nw') rsHtml += 'left:-4px;top:-4px;width:14px;height:14px;cursor:nw-resize;';
        else if (d === 'se') rsHtml += 'right:-4px;bottom:-4px;width:14px;height:14px;cursor:se-resize;';
        else if (d === 'sw') rsHtml += 'left:-4px;bottom:-4px;width:14px;height:14px;cursor:sw-resize;';
        rsHtml += 'z-index:20;background:transparent;"></div>';
    }

    var html = '<div id="cf-currliab-breakdown-win" style="position:fixed;left:' + left + 'px;top:' + top + 'px;width:' + winW + 'px;height:' + winH + 'px;min-width:' + MIN_W + 'px;min-height:' + MIN_H + 'px;background:#fff;border-radius:6px;box-shadow:0 8px 30px rgba(0,0,0,.35);z-index:200600;display:flex;flex-direction:column;overflow:hidden;">'
        + rsHtml
        + '<div id="cf-currliab-breakdown-hdr" style="flex:none;display:flex;justify-content:space-between;align-items:center;padding:10px 16px;background:#251840;cursor:move;user-select:none;">'
        + '<span style="font-family:Barlow Condensed,Arial;font-size:15px;font-weight:700;color:#fff;">Current Liabilities — Account Breakdown</span>'
        + '<button id="cf-currliab-breakdown-close" style="background:none;border:none;font-size:20px;cursor:pointer;color:#fff;line-height:1;">&times;</button>'
        + '</div>'
        + '<div id="cf-currliab-breakdown-unit" style="flex:none;font-family:Barlow Condensed,Arial;font-size:12px;color:#5a6e8c;padding:8px 16px 0;overflow:hidden;"></div>'
        + '<div style="flex:1;min-width:0;min-height:0;overflow-x:hidden;overflow-y:auto;padding:10px 16px 16px;box-sizing:border-box;">'
        + '<div id="cf-currliab-breakdown-chart" style="min-width:0;"></div>'
        + '</div>'
        + '</div>';
    $('body').append(html);
    _cfRedrawCurrLiabBreakdown();

    // Drag (header) + resize (8 handles) — same mechanics as the
    // Investments breakdown window.
    var win = document.getElementById('cf-currliab-breakdown-win');
    var hdr = document.getElementById('cf-currliab-breakdown-hdr');
    var _action = null, _sx = 0, _sy = 0, _ox = 0, _oy = 0, _ow = 0, _oh = 0;

    function onDown(e, dir){
        var r = win.getBoundingClientRect();
        _action = dir; _sx = e.clientX; _sy = e.clientY;
        _ox = r.left; _oy = r.top; _ow = r.width; _oh = r.height;
        e.preventDefault();
        if (dir !== 'drag') e.stopPropagation();
    }
    function onMove(e){
        if (!_action) return;
        e.preventDefault();
        var dx = e.clientX - _sx, dy = e.clientY - _sy;
        if (_action === 'drag') {
            var x = Math.max(0, Math.min(_ox + dx, window.innerWidth - win.offsetWidth));
            var y = Math.max(0, Math.min(_oy + dy, window.innerHeight - win.offsetHeight));
            win.style.left = x + 'px'; win.style.top = y + 'px';
        } else {
            var l = _ox, t = _oy, w = _ow, h = _oh;
            if (_action.indexOf('e') > -1) { w = Math.max(MIN_W, _ow + dx); }
            if (_action.indexOf('s') > -1) { h = Math.max(MIN_H, _oh + dy); }
            if (_action.indexOf('w') > -1) { var nw = Math.max(MIN_W, _ow - dx); l = _ox + (_ow - nw); w = nw; }
            if (_action.indexOf('n') > -1) { var nh = Math.max(MIN_H, _oh - dy); t = _oy + (_oh - nh); h = nh; }
            win.style.left = l + 'px'; win.style.top = t + 'px';
            win.style.width = w + 'px'; win.style.height = h + 'px';
            _cfRedrawCurrLiabBreakdown();
        }
    }
    function onUp(){ _action = null; }

    if (typeof window.bindDragTouch === 'function') {
        window.bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-currliab-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-currliab-rs').each(function(){
            var dir = $(this).data('dir');
            window.bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        window.bindDragTouch(document, 'mousemove', onMove, { passive: false });
        window.bindDragTouch(document, 'mouseup', onUp);
    } else {
        hdr.addEventListener('mousedown', function(e){ if (e.target.id !== 'cf-currliab-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-currliab-rs').each(function(){
            var dir = $(this).data('dir');
            this.addEventListener('mousedown', function(e){ onDown(e, dir); });
        });
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    }
}
$(document).on('click', '#cf-currentliabilities', function(e){
    $.ajax({
        type: 'POST', url: '../projectsmain/cfcurrentliabilitiesbreakdown', dataType: 'json',
        success: function(d){
            if (d && d.error === 'No') { _cfShowCurrLiabBreakdown(d.accounts || []); }
        }
    });
});
$(document).on('click', '#cf-currliab-breakdown-close', function(){ $('#cf-currliab-breakdown-win').remove(); });

// Project Expenditure panel — single flat dial, deep purple up to
// target and deep orange beyond, no target-split colour logic needed
// since it's the same two-colour treatment throughout (unlike the
// dark-blue/orange dials, purple/orange doesn't reuse either half's
// colour for anything else). Target is NOT user-editable — it's
// DERIVED as 90% of the project's contract value
// (projects.project_value), same "auto-computed, no pencil" pattern as
// Current Liabilities. Needle/actual = the Finance module's Project
// Expenditure Total (see actionCfprojectexpenditure in
// FinancerequestsController, which mirrors actionProexpenditure's own
// grand-total calculation minus the Advances/Liabilities section).
var _cfProjExpState = { target: 0, actual: 0 };

function _cfProjExpDialSvg(targetCr, actual){
    var maxV = targetCr > 0 ? targetCr * 2 : 1;
    var f = Math.max(0, Math.min(1, actual / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    var arcPath = arc(0, 0.5, '#251840') + arc(0.5, 1, '#b5540a');
    var targetF = 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var fmt = _cfFmtMoney(Math.max(Math.abs(targetCr), Math.abs(actual)) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + targetLine
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 30) + '" text-anchor="middle" font-size="24" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + (actual * divisor).toFixed(2) + '</text>'
        + '<text x="' + cx + '" y="' + (cy - 14) + '" text-anchor="middle" font-size="14" font-weight="700" fill="#5a5a5a" font-family="Barlow Condensed,Arial">' + fmt.unit + '</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawProjExpDial(){
    var el = document.getElementById('cf-projectexpenditure');
    if (!el) return;
    el.style.justifyContent = 'center';
    el.style.cursor = 'pointer';
    el.title = 'Click to see group breakdown';
    var s = _cfProjExpState;
    // Backend already returns target/total in raw rupees; convert both
    // to the same Crores-scale float convention as every other panel.
    var targetCr = s.target / 10000000;
    var fmt = _cfFmtMoney(Math.max(Math.abs(s.actual), Math.abs(targetCr)) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    var maxV = targetCr > 0 ? targetCr * 2 : 1;
    var needleCol = (s.actual / maxV) <= 0.5 ? '#251840' : '#b5540a';
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfProjExpDialSvg(targetCr, s.actual)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:#111;">Actual: <span style="font-size:13px;color:' + needleCol + ';font-weight:700;">' + (s.actual * divisor).toFixed(2) + '</span></span>'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>Target: <span style="font-size:13px;color:#251840;font-weight:700;">' + (targetCr * divisor).toFixed(2) + '</span></span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfProjExpDial(){
    var gen = _cfLoadGen;
    _cfProjExpState = { target: 0, actual: 0 };
    _cfDrawProjExpDial();
    _cfQueueAjax({
        type: 'POST', url: '../financerequests/cfprojectexpenditure', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfProjExpState.target = +d.target || 0;
                _cfProjExpState.actual = (+d.total || 0) / 10000000;
                _cfDrawProjExpDial();
            }
        }
    });
}

// Project Expenditure breakdown popup — same draggable/resizable
// vertical bar chart window as Investments/WIP (see
// _cfInvestBreakdownChartHtml/_cfShowInvestBreakdown above), bucketed by
// Project Expenditure group (see actionCfprojectexpenditurebreakdown).
// Each bar's hover tooltip lists its individual contributing account
// heads, via the same shared .cf-breakdown-bar-col hover handler used
// by the WIP popup.
function _cfRedrawProjExpBreakdown(){
    var s = window._cfProjExpBreakdownState;
    if (!s) return;
    var chartH = Math.max(120, $('#cf-projexp-breakdown-win').height() - 130);
    var r = _cfInvestBreakdownChartHtml(s.accounts, chartH);
    $('#cf-projexp-breakdown-unit').html(
        '<span style="font-weight:700;color:#251840;">Amount in ' + r.unit + '</span>'
        + '<span style="float:right;font-weight:700;color:#251840;">Total: ' + r.total + ' ' + r.unit + '</span>'
    );
    $('#cf-projexp-breakdown-chart').html(r.body);
}

function _cfShowProjExpBreakdown(accounts){
    $('#cf-projexp-breakdown-win').remove();
    window._cfProjExpBreakdownState = { accounts: accounts };

    var winW = 640, winH = 380, MIN_W = 340, MIN_H = 240;
    var left = Math.max(10, (window.innerWidth - winW) / 2);
    var top = Math.max(10, (window.innerHeight - winH) / 2);

    var rsHandles = ['n','s','e','w','ne','nw','se','sw'];
    var rsHtml = '';
    for (var h = 0; h < rsHandles.length; h++) {
        rsHtml += '<div class="cf-projexp-rs" data-dir="' + rsHandles[h] + '" style="position:absolute;';
        var d = rsHandles[h];
        if (d === 'n') rsHtml += 'top:-4px;left:10px;right:10px;height:10px;cursor:n-resize;';
        else if (d === 's') rsHtml += 'bottom:-4px;left:10px;right:10px;height:10px;cursor:s-resize;';
        else if (d === 'e') rsHtml += 'right:-4px;top:10px;bottom:10px;width:10px;cursor:e-resize;';
        else if (d === 'w') rsHtml += 'left:-4px;top:10px;bottom:10px;width:10px;cursor:w-resize;';
        else if (d === 'ne') rsHtml += 'right:-4px;top:-4px;width:14px;height:14px;cursor:ne-resize;';
        else if (d === 'nw') rsHtml += 'left:-4px;top:-4px;width:14px;height:14px;cursor:nw-resize;';
        else if (d === 'se') rsHtml += 'right:-4px;bottom:-4px;width:14px;height:14px;cursor:se-resize;';
        else if (d === 'sw') rsHtml += 'left:-4px;bottom:-4px;width:14px;height:14px;cursor:sw-resize;';
        rsHtml += 'z-index:20;background:transparent;"></div>';
    }

    var html = '<div id="cf-projexp-breakdown-win" style="position:fixed;left:' + left + 'px;top:' + top + 'px;width:' + winW + 'px;height:' + winH + 'px;min-width:' + MIN_W + 'px;min-height:' + MIN_H + 'px;background:#fff;border-radius:6px;box-shadow:0 8px 30px rgba(0,0,0,.35);z-index:200600;display:flex;flex-direction:column;overflow:hidden;">'
        + rsHtml
        + '<div id="cf-projexp-breakdown-hdr" style="flex:none;display:flex;justify-content:space-between;align-items:center;padding:10px 16px;background:#251840;cursor:move;user-select:none;">'
        + '<span style="font-family:Barlow Condensed,Arial;font-size:15px;font-weight:700;color:#fff;">Project Expenditure — Group Breakdown</span>'
        + '<button id="cf-projexp-breakdown-close" style="background:none;border:none;font-size:20px;cursor:pointer;color:#fff;line-height:1;">&times;</button>'
        + '</div>'
        + '<div id="cf-projexp-breakdown-unit" style="flex:none;font-family:Barlow Condensed,Arial;font-size:12px;color:#5a6e8c;padding:8px 16px 0;overflow:hidden;"></div>'
        + '<div style="flex:1;min-width:0;min-height:0;overflow-x:hidden;overflow-y:auto;padding:10px 16px 16px;box-sizing:border-box;">'
        + '<div id="cf-projexp-breakdown-chart" style="min-width:0;"></div>'
        + '</div>'
        + '</div>';
    $('body').append(html);
    _cfRedrawProjExpBreakdown();

    // Drag (header) + resize (8 handles) — same mechanics as the
    // Investments/WIP breakdown windows.
    var win = document.getElementById('cf-projexp-breakdown-win');
    var hdr = document.getElementById('cf-projexp-breakdown-hdr');
    var _action = null, _sx = 0, _sy = 0, _ox = 0, _oy = 0, _ow = 0, _oh = 0;

    function onDown(e, dir){
        var r = win.getBoundingClientRect();
        _action = dir; _sx = e.clientX; _sy = e.clientY;
        _ox = r.left; _oy = r.top; _ow = r.width; _oh = r.height;
        e.preventDefault();
        if (dir !== 'drag') e.stopPropagation();
    }
    function onMove(e){
        if (!_action) return;
        e.preventDefault();
        var dx = e.clientX - _sx, dy = e.clientY - _sy;
        if (_action === 'drag') {
            var x = Math.max(0, Math.min(_ox + dx, window.innerWidth - win.offsetWidth));
            var y = Math.max(0, Math.min(_oy + dy, window.innerHeight - win.offsetHeight));
            win.style.left = x + 'px'; win.style.top = y + 'px';
        } else {
            var l = _ox, t = _oy, w = _ow, h = _oh;
            if (_action.indexOf('e') > -1) { w = Math.max(MIN_W, _ow + dx); }
            if (_action.indexOf('s') > -1) { h = Math.max(MIN_H, _oh + dy); }
            if (_action.indexOf('w') > -1) { var nw = Math.max(MIN_W, _ow - dx); l = _ox + (_ow - nw); w = nw; }
            if (_action.indexOf('n') > -1) { var nh = Math.max(MIN_H, _oh - dy); t = _oy + (_oh - nh); h = nh; }
            win.style.left = l + 'px'; win.style.top = t + 'px';
            win.style.width = w + 'px'; win.style.height = h + 'px';
            _cfRedrawProjExpBreakdown();
        }
    }
    function onUp(){ _action = null; }

    if (typeof window.bindDragTouch === 'function') {
        window.bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-projexp-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-projexp-rs').each(function(){
            var dir = $(this).data('dir');
            window.bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        window.bindDragTouch(document, 'mousemove', onMove, { passive: false });
        window.bindDragTouch(document, 'mouseup', onUp);
    } else {
        hdr.addEventListener('mousedown', function(e){ if (e.target.id !== 'cf-projexp-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-projexp-rs').each(function(){
            var dir = $(this).data('dir');
            this.addEventListener('mousedown', function(e){ onDown(e, dir); });
        });
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    }
}
$(document).on('click', '#cf-projectexpenditure', function(e){
    $.ajax({
        type: 'POST', url: '../financerequests/cfprojectexpenditurebreakdown', dataType: 'json',
        success: function(d){
            if (d && d.error === 'No') { _cfShowProjExpBreakdown(d.accounts || []); }
        }
    });
});
$(document).on('click', '#cf-projexp-breakdown-close', function(){ $('#cf-projexp-breakdown-win').remove(); });

// Average Work In Progress panel — same layout pattern as the Bank
// panel: flat dial, full scale = 2x Target (T.WIP), yellow bright
// marker line at the dial's center (the target threshold), needle =
// Current WIP (actual, no averaging), teal overlay arc (0 to C.WIP, on
// the same scale) showing Current WIP's progress along the dial — same
// overlay treatment as the Bank panel's orange Availed arc. Legend row
// shows two values left-to-right: C.WIP (Current WIP, static, left tip)
// — T.WIP (Target WIP, editable via pencil at the right tip, saved to
// cf_dashboard_targets under metric_key "wip_target" via the same
// cfsavetarget/cfgettarget endpoints the Bank panel uses). C.WIP = sum
// of Bank/Cash payment transactions for the selected project whose
// debited counterparty is an Expense-type account head
// (accounts_item.account_type = 8) — see actionCfwip.
var _cfWipState = { target: 0, currentWip: 0, editing: false };

function _cfWipDialSvg(target, currentWip){
    // target is the raw number the user typed (e.g. "19"); reinterpret it
    // via the same magnitude rule as every other value (< 100 = Lakhs,
    // >= 100 = Crores), then convert to the same Crores-scale float
    // currentWip is already in, so both share one axis.
    var targetCr = _cfEnteredToRupees(target) / 10000000;
    var maxV = targetCr > 0 ? targetCr * 2 : 1;
    var f = Math.max(0, Math.min(1, currentWip / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    // Target marker: bright yellow line at the dial's center (target =
    // half of the full 2x scale) — same treatment as the Bank panel's
    // Allocated marker.
    var targetF = 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var fmt = _cfFmtMoney(Math.max(Math.abs(targetCr), Math.abs(currentWip)) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arc(0, 0.5, '#71797e') + arc(0.5, 1, '#e07b00')
        + targetLine
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 30) + '" text-anchor="middle" font-size="24" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + (currentWip * divisor).toFixed(2) + '</text>'
        + '<text x="' + cx + '" y="' + (cy - 14) + '" text-anchor="middle" font-size="14" font-weight="700" fill="#5a5a5a" font-family="Barlow Condensed,Arial">' + fmt.unit + '</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawWipDial(){
    var el = document.getElementById('cf-billsreceivable');
    if (!el) return;
    el.style.justifyContent = 'center';
    el.style.cursor = 'pointer';
    el.title = 'Click to see group breakdown';
    var s = _cfWipState;
    // s.target is the raw number the user typed; reinterpret it via the
    // magnitude rule (< 100 = Lakhs, >= 100 = Crores) before comparing it
    // against currentWip, which is already a Crores-scale float.
    var targetCr = _cfEnteredToRupees(s.target) / 10000000;
    var fmt = _cfFmtMoney(Math.max(Math.abs(s.currentWip), Math.abs(targetCr)) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100; // state values are already in Crores; 1 Crore = 100 Lakhs
    var targetCell = s.editing
        ? '<input class="cf-wip-input" data-field="target" type="number" step="any" value="' + s.target + '" style="width:54px;font-size:13px;padding:0 3px;border:1px solid #b0bec5;border-radius:2px;color:#081142;font-weight:700;">'
        : '<span style="font-size:13px;color:#081142;font-weight:700;">' + (targetCr * divisor).toFixed(2) + '</span>';
    // Actual (C.WIP) legend colour always matches whichever arc half the
    // needle is currently pointing into (grey up to target, orange
    // beyond).
    var maxV = targetCr > 0 ? targetCr * 2 : 1;
    var needleCol = (s.currentWip / maxV) <= 0.5 ? '#71797e' : '#e07b00';
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfWipDialSvg(s.target, s.currentWip)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 6px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:12px;color:#111;">C.WIP: <span style="font-size:12px;color:' + needleCol + ';font-weight:700;">' + (s.currentWip * divisor).toFixed(2) + '</span></span>'
        + '<span style="font-size:12px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>T.WIP: ' + targetCell + ' <button class="cf-wip-pencil" title="Edit Target WIP" style="background:none;border:none;cursor:pointer;padding:0 2px;font-size:12px;">&#9998;</button></span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfWipDial(){
    var gen = _cfLoadGen;
    _cfWipState = { target: 0, currentWip: 0, editing: false };
    _cfDrawWipDial();
    _cfFetchTarget('wip_target', function(d){
        if (gen === _cfLoadGen) { _cfWipState.target = +d.target || 0; _cfDrawWipDial(); }
    });
    // C.WIP = sum of Bank/Cash payment transactions whose debited
    // counterparty is an Expense-type account head — see actionCfwip.
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfwip', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfWipState.currentWip = (+d.total || 0) / 10000000;
                _cfDrawWipDial();
            }
        }
    });
}

// Contribution Margin panel — a PERCENTAGE dial (unlike every other
// Cashflow dial, which shows a money value at its center). Center shows
// the margin percentage; the underlying rupee value (Contract Income -
// Project Expenditure — see actionCfcontributionmargin) is shown below
// the dial in Lakhs (<= 99 Lakhs) or Crores (> 99 Lakhs). Target is a
// user-editable PERCENTAGE (not a rupee amount, so _cfEnteredToRupees'
// Lakhs/Crores magnitude rule does NOT apply here), saved under
// metric_key "contribution_margin_target". Scale is 0-100%, needle at
// dial center (50%) represents the target's position only when target
// is exactly 50 — like every other dial here, the target marker sits at
// the dial's fixed midpoint and the SCALE (max = target*2) stretches or
// compresses around it, except percentage naturally caps at 100 so the
// max is clamped to 100 when target*2 would exceed it.
var _cfContribMarginState = { target: 0, value: 0, percent: 0, editing: false };

function _cfContribMarginDialSvg(target, percent){
    var maxV = target > 0 ? Math.min(100, target * 2) : 100;
    var f = Math.max(0, Math.min(1, percent / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    var arcPath = arc(0, 0.5, '#00838f') + arc(0.5, 1, '#b5540a');
    var targetF = maxV > 0 ? Math.max(0, Math.min(1, target / maxV)) : 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + targetLine
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 22) + '" text-anchor="middle" font-size="26" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + percent.toFixed(2) + '%</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawContribMarginDial(){
    var el = document.getElementById('cf-contribmargin');
    if (!el) return;
    el.style.justifyContent = 'center';
    var s = _cfContribMarginState;
    var fmt = _cfFmtMoney(s.value); // s.value is already raw rupees
    var divisor = fmt.unit === 'Crores' ? 10000000 : 100000;
    var targetCell = s.editing
        ? '<input class="cf-contribmargin-input" data-field="target" type="number" step="any" value="' + s.target + '" style="width:54px;font-size:13px;padding:0 3px;border:1px solid #b0bec5;border-radius:2px;color:#5a5a5a;font-weight:700;">'
        : '<span style="font-size:13px;color:#5a5a5a;font-weight:700;">' + s.target.toFixed(2) + '%</span>';
    var maxV = s.target > 0 ? Math.min(100, s.target * 2) : 100;
    var needleCol = (s.percent / maxV) <= 0.5 ? '#00838f' : '#b5540a';
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfContribMarginDialSvg(s.target, s.percent)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:' + needleCol + ';font-weight:700;">' + (s.value / divisor).toFixed(2) + ' ' + fmt.unit + '</span>'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>Target: ' + targetCell + ' <button class="cf-contribmargin-pencil" title="Edit Target" style="background:none;border:none;cursor:pointer;padding:0 2px;font-size:12px;">&#9998;</button></span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfContribMarginDial(){
    var gen = _cfLoadGen;
    _cfContribMarginState = { target: 0, value: 0, percent: 0, editing: false };
    _cfDrawContribMarginDial();
    _cfFetchTarget('contribution_margin_target', function(d){
        if (gen === _cfLoadGen) { _cfContribMarginState.target = +d.target || 0; _cfDrawContribMarginDial(); }
    });
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfcontributionmargin', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfContribMarginState.value = +d.value || 0;
                _cfContribMarginState.percent = +d.percent || 0;
                _cfDrawContribMarginDial();
            }
        }
    });
}

$(document).on('click', '.cf-contribmargin-pencil', function(e){
    e.preventDefault(); e.stopPropagation();
    var s = _cfContribMarginState;
    if (s.editing) {
        var val = +$('.cf-contribmargin-input[data-field="target"]').val() || 0;
        s.target = val;
        s.editing = false;
        _cfDrawContribMarginDial();
        _cfQueueAjax({
            type: 'POST', url: '../projectsmain/cfsavetarget', dataType: 'json',
            data: { metric_key: 'contribution_margin_target', target_value: val },
            success: function(){ renderCfBars(); }
        });
    } else {
        s.editing = true;
        _cfDrawContribMarginDial();
    }
});

// Free Cash panel — a PERCENTAGE dial, same presentation pattern as
// Contribution Margin (Actual shown to the LEFT of the dial, Target
// editable on the right). Center shows the Free Cash percentage —
// (Free Cash value / Average Investment) * 100 — the underlying rupee
// value (Contribution Margin - Total Investment - Current Liabilities —
// see actionCfreecash) is shown to the left in Lakhs (<= 99 Lakhs) or
// Crores (> 99 Lakhs). Target is a user-editable PERCENTAGE, saved
// under metric_key "free_cash_target". Left-arc colour (deep purple,
// #4a2e7a) is distinct from Contribution Margin's teal, per explicit
// request, so the two percentage dials aren't visually identical.
var _cfFreeCashState = { target: 0, value: 0, percent: 0, editing: false };

function _cfFreeCashDialSvg(target, percent){
    var maxV = target > 0 ? Math.min(100, target * 2) : 100;
    var f = Math.max(0, Math.min(1, percent / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    var arcPath = arc(0, 0.5, '#4a2e7a') + arc(0.5, 1, '#b5540a');
    var targetF = maxV > 0 ? Math.max(0, Math.min(1, target / maxV)) : 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + targetLine
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 22) + '" text-anchor="middle" font-size="26" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + percent.toFixed(2) + '%</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawFreeCashDial(){
    var el = document.getElementById('cf-expenses');
    if (!el) return;
    el.style.justifyContent = 'center';
    var s = _cfFreeCashState;
    var fmt = _cfFmtMoney(s.value); // s.value is already raw rupees
    var divisor = fmt.unit === 'Crores' ? 10000000 : 100000;
    var targetCell = s.editing
        ? '<input class="cf-freecash-input" data-field="target" type="number" step="any" value="' + s.target + '" style="width:54px;font-size:13px;padding:0 3px;border:1px solid #b0bec5;border-radius:2px;color:#5a5a5a;font-weight:700;">'
        : '<span style="font-size:13px;color:#5a5a5a;font-weight:700;">' + s.target.toFixed(2) + '%</span>';
    var maxV = s.target > 0 ? Math.min(100, s.target * 2) : 100;
    var needleCol = (s.percent / maxV) <= 0.5 ? '#4a2e7a' : '#b5540a';
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfFreeCashDialSvg(s.target, s.percent)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:' + needleCol + ';font-weight:700;">' + (s.value / divisor).toFixed(2) + ' ' + fmt.unit + '</span>'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>Target: ' + targetCell + ' <button class="cf-freecash-pencil" title="Edit Target" style="background:none;border:none;cursor:pointer;padding:0 2px;font-size:12px;">&#9998;</button></span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfFreeCashDial(){
    var gen = _cfLoadGen;
    _cfFreeCashState = { target: 0, value: 0, percent: 0, editing: false };
    _cfDrawFreeCashDial();
    _cfFetchTarget('free_cash_target', function(d){
        if (gen === _cfLoadGen) { _cfFreeCashState.target = +d.target || 0; _cfDrawFreeCashDial(); }
    });
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfreecash', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfFreeCashState.value = +d.value || 0;
                _cfFreeCashState.percent = +d.percent || 0;
                _cfDrawFreeCashDial();
            }
        }
    });
}

$(document).on('click', '.cf-freecash-pencil', function(e){
    e.preventDefault(); e.stopPropagation();
    var s = _cfFreeCashState;
    if (s.editing) {
        var val = +$('.cf-freecash-input[data-field="target"]').val() || 0;
        s.target = val;
        s.editing = false;
        _cfDrawFreeCashDial();
        _cfQueueAjax({
            type: 'POST', url: '../projectsmain/cfsavetarget', dataType: 'json',
            data: { metric_key: 'free_cash_target', target_value: val },
            success: function(){ renderCfBars(); }
        });
    } else {
        s.editing = true;
        _cfDrawFreeCashDial();
    }
});

// ROI panel — a PERCENTAGE dial, same presentation pattern as
// Contribution Margin/Free Cash (Actual shown to the LEFT, Target
// editable on the right, percentage at center). ROI = (Contribution
// Margin / Average Investment) * 100, so the Actual value shown here is
// Contribution Margin's own rupee value (see actionCfroi), not a
// separate ROI-specific rupee amount. Target is a user-editable
// PERCENTAGE, saved under metric_key "roi_target". Left-arc colour
// (steel blue, #2e5c8a) is distinct from Contribution Margin's teal and
// Free Cash's purple, so all three percentage dials stay visually
// distinguishable.
var _cfRoiState = { target: 0, value: 0, percent: 0, editing: false };

function _cfRoiDialSvg(target, percent){
    var maxV = target > 0 ? Math.min(100, target * 2) : 100;
    var f = Math.max(0, Math.min(1, percent / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    var arcPath = arc(0, 0.5, '#2e5c8a') + arc(0.5, 1, '#b5540a');
    var targetF = maxV > 0 ? Math.max(0, Math.min(1, target / maxV)) : 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + targetLine
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 22) + '" text-anchor="middle" font-size="26" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + percent.toFixed(2) + '%</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawRoiDial(){
    var el = document.getElementById('cf-roi');
    if (!el) return;
    el.style.justifyContent = 'center';
    var s = _cfRoiState;
    var fmt = _cfFmtMoney(s.value); // s.value is already raw rupees
    var divisor = fmt.unit === 'Crores' ? 10000000 : 100000;
    var targetCell = s.editing
        ? '<input class="cf-roi-input" data-field="target" type="number" step="any" value="' + s.target + '" style="width:54px;font-size:13px;padding:0 3px;border:1px solid #b0bec5;border-radius:2px;color:#5a5a5a;font-weight:700;">'
        : '<span style="font-size:13px;color:#5a5a5a;font-weight:700;">' + s.target.toFixed(2) + '%</span>';
    var maxV = s.target > 0 ? Math.min(100, s.target * 2) : 100;
    var needleCol = (s.percent / maxV) <= 0.5 ? '#2e5c8a' : '#b5540a';
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfRoiDialSvg(s.target, s.percent)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:' + needleCol + ';font-weight:700;">' + (s.value / divisor).toFixed(2) + ' ' + fmt.unit + '</span>'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>Target: ' + targetCell + ' <button class="cf-roi-pencil" title="Edit Target" style="background:none;border:none;cursor:pointer;padding:0 2px;font-size:12px;">&#9998;</button></span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfRoiDial(){
    var gen = _cfLoadGen;
    _cfRoiState = { target: 0, value: 0, percent: 0, editing: false };
    _cfDrawRoiDial();
    _cfFetchTarget('roi_target', function(d){
        if (gen === _cfLoadGen) { _cfRoiState.target = +d.target || 0; _cfDrawRoiDial(); }
    });
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfroi', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfRoiState.value = +d.value || 0;
                _cfRoiState.percent = +d.percent || 0;
                _cfDrawRoiDial();
            }
        }
    });
}

$(document).on('click', '.cf-roi-pencil', function(e){
    e.preventDefault(); e.stopPropagation();
    var s = _cfRoiState;
    if (s.editing) {
        var val = +$('.cf-roi-input[data-field="target"]').val() || 0;
        s.target = val;
        s.editing = false;
        _cfDrawRoiDial();
        _cfQueueAjax({
            type: 'POST', url: '../projectsmain/cfsavetarget', dataType: 'json',
            data: { metric_key: 'roi_target', target_value: val },
            success: function(){ renderCfBars(); }
        });
    } else {
        s.editing = true;
        _cfDrawRoiDial();
    }
});

// Work In Progress breakdown popup — same draggable/resizable vertical
// bar chart window as the Investments breakdown (see
// _cfInvestBreakdownChartHtml/_cfShowInvestBreakdown above), but bucketed
// by Project Expenditure group instead of by individual account (see
// actionCfwipbreakdown). Reuses the same chart-rendering function since
// the {name, amount} row shape is identical.
function _cfRedrawWipBreakdown(){
    var s = window._cfWipBreakdownState;
    if (!s) return;
    var chartH = Math.max(120, $('#cf-wip-breakdown-win').height() - 130);
    var r = _cfInvestBreakdownChartHtml(s.accounts, chartH);
    $('#cf-wip-breakdown-unit').html(
        '<span style="font-weight:700;color:#081142;">Amount in ' + r.unit + '</span>'
        + '<span style="float:right;font-weight:700;color:#081142;">Total: ' + r.total + ' ' + r.unit + '</span>'
    );
    $('#cf-wip-breakdown-chart').html(r.body);
}

function _cfShowWipBreakdown(accounts){
    $('#cf-wip-breakdown-win').remove();
    window._cfWipBreakdownState = { accounts: accounts };

    var winW = 640, winH = 380, MIN_W = 340, MIN_H = 240;
    var left = Math.max(10, (window.innerWidth - winW) / 2);
    var top = Math.max(10, (window.innerHeight - winH) / 2);

    var rsHandles = ['n','s','e','w','ne','nw','se','sw'];
    var rsHtml = '';
    for (var h = 0; h < rsHandles.length; h++) {
        rsHtml += '<div class="cf-wip-rs" data-dir="' + rsHandles[h] + '" style="position:absolute;';
        var d = rsHandles[h];
        if (d === 'n') rsHtml += 'top:-4px;left:10px;right:10px;height:10px;cursor:n-resize;';
        else if (d === 's') rsHtml += 'bottom:-4px;left:10px;right:10px;height:10px;cursor:s-resize;';
        else if (d === 'e') rsHtml += 'right:-4px;top:10px;bottom:10px;width:10px;cursor:e-resize;';
        else if (d === 'w') rsHtml += 'left:-4px;top:10px;bottom:10px;width:10px;cursor:w-resize;';
        else if (d === 'ne') rsHtml += 'right:-4px;top:-4px;width:14px;height:14px;cursor:ne-resize;';
        else if (d === 'nw') rsHtml += 'left:-4px;top:-4px;width:14px;height:14px;cursor:nw-resize;';
        else if (d === 'se') rsHtml += 'right:-4px;bottom:-4px;width:14px;height:14px;cursor:se-resize;';
        else if (d === 'sw') rsHtml += 'left:-4px;bottom:-4px;width:14px;height:14px;cursor:sw-resize;';
        rsHtml += 'z-index:20;background:transparent;"></div>';
    }

    var html = '<div id="cf-wip-breakdown-win" style="position:fixed;left:' + left + 'px;top:' + top + 'px;width:' + winW + 'px;height:' + winH + 'px;min-width:' + MIN_W + 'px;min-height:' + MIN_H + 'px;background:#fff;border-radius:6px;box-shadow:0 8px 30px rgba(0,0,0,.35);z-index:200600;display:flex;flex-direction:column;overflow:hidden;">'
        + rsHtml
        + '<div id="cf-wip-breakdown-hdr" style="flex:none;display:flex;justify-content:space-between;align-items:center;padding:10px 16px;background:#081142;cursor:move;user-select:none;">'
        + '<span style="font-family:Barlow Condensed,Arial;font-size:15px;font-weight:700;color:#fff;">Work In Progress — Group Breakdown</span>'
        + '<button id="cf-wip-breakdown-close" style="background:none;border:none;font-size:20px;cursor:pointer;color:#fff;line-height:1;">&times;</button>'
        + '</div>'
        + '<div id="cf-wip-breakdown-unit" style="flex:none;font-family:Barlow Condensed,Arial;font-size:12px;color:#5a6e8c;padding:8px 16px 0;overflow:hidden;"></div>'
        + '<div style="flex:1;min-width:0;min-height:0;overflow-x:hidden;overflow-y:auto;padding:10px 16px 16px;box-sizing:border-box;">'
        + '<div id="cf-wip-breakdown-chart" style="min-width:0;"></div>'
        + '</div>'
        + '</div>';
    $('body').append(html);
    _cfRedrawWipBreakdown();

    // Drag (header) + resize (8 handles) — same mechanics as the
    // Investments breakdown window (see _cfShowInvestBreakdown).
    var win = document.getElementById('cf-wip-breakdown-win');
    var hdr = document.getElementById('cf-wip-breakdown-hdr');
    var _action = null, _sx = 0, _sy = 0, _ox = 0, _oy = 0, _ow = 0, _oh = 0;

    function onDown(e, dir){
        var r = win.getBoundingClientRect();
        _action = dir; _sx = e.clientX; _sy = e.clientY;
        _ox = r.left; _oy = r.top; _ow = r.width; _oh = r.height;
        e.preventDefault();
        if (dir !== 'drag') e.stopPropagation();
    }
    function onMove(e){
        if (!_action) return;
        e.preventDefault();
        var dx = e.clientX - _sx, dy = e.clientY - _sy;
        if (_action === 'drag') {
            var x = Math.max(0, Math.min(_ox + dx, window.innerWidth - win.offsetWidth));
            var y = Math.max(0, Math.min(_oy + dy, window.innerHeight - win.offsetHeight));
            win.style.left = x + 'px'; win.style.top = y + 'px';
        } else {
            var l = _ox, t = _oy, w = _ow, h = _oh;
            if (_action.indexOf('e') > -1) { w = Math.max(MIN_W, _ow + dx); }
            if (_action.indexOf('s') > -1) { h = Math.max(MIN_H, _oh + dy); }
            if (_action.indexOf('w') > -1) { var nw = Math.max(MIN_W, _ow - dx); l = _ox + (_ow - nw); w = nw; }
            if (_action.indexOf('n') > -1) { var nh = Math.max(MIN_H, _oh - dy); t = _oy + (_oh - nh); h = nh; }
            win.style.left = l + 'px'; win.style.top = t + 'px';
            win.style.width = w + 'px'; win.style.height = h + 'px';
            _cfRedrawWipBreakdown();
        }
    }
    function onUp(){ _action = null; }

    if (typeof window.bindDragTouch === 'function') {
        window.bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-wip-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-wip-rs').each(function(){
            var dir = $(this).data('dir');
            window.bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        window.bindDragTouch(document, 'mousemove', onMove, { passive: false });
        window.bindDragTouch(document, 'mouseup', onUp);
    } else {
        hdr.addEventListener('mousedown', function(e){ if (e.target.id !== 'cf-wip-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-wip-rs').each(function(){
            var dir = $(this).data('dir');
            this.addEventListener('mousedown', function(e){ onDown(e, dir); });
        });
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    }
}
$(document).on('click', '#cf-billsreceivable', function(e){
    if ($(e.target).closest('.cf-wip-pencil, .cf-wip-input').length) return;
    $.ajax({
        type: 'POST', url: '../projectsmain/cfwipbreakdown', dataType: 'json',
        success: function(d){
            if (d && d.error === 'No') { _cfShowWipBreakdown(d.accounts || []); }
        }
    });
});
$(document).on('click', '#cf-wip-breakdown-close', function(){ $('#cf-wip-breakdown-win').remove(); });

$(document).on('click', '.cf-wip-pencil', function(e){
    e.preventDefault(); e.stopPropagation();
    var s = _cfWipState;
    if (s.editing) {
        var val = +$('.cf-wip-input[data-field="target"]').val() || 0;
        s.target = val;
        s.editing = false;
        _cfDrawWipDial();
        $.ajax({
            type: 'POST', url: '../projectsmain/cfsavetarget', dataType: 'json',
            data: { metric_key: 'wip_target', target_value: val },
            success: function(){ renderCfBars(); }
        });
    } else {
        s.editing = true;
        _cfDrawWipDial();
    }
});
$(document).on('click', '.cf-wip-input', function(e){ e.stopPropagation(); });

// Bills Receivable panel — single flat yellow-green dial with an
// editable target (yellow marker at the dial's center, half of the full
// 2x scale — same treatment as the Bank panel's Allocated marker).
// Needle position represents the actual value — placeholder at 0, no
// data source yet. Target is saved to cf_dashboard_targets under
// metric_key "bills_receivable_target" via cfsavetarget/cfgettarget.
var _cfBillsRecvState = { target: 0, actual: 0, editing: false };

function _cfBillsRecvDialSvg(target, actual){
    var targetCr = _cfEnteredToRupees(target) / 10000000;
    var maxV = targetCr > 0 ? targetCr * 2 : 1;
    var f = Math.max(0, Math.min(1, actual / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    // Up to target (first half) = deep orange; beyond target (second
    // half) = the panel's original yellow-green.
    var arcPath = arc(0, 0.5, '#b5540a') + arc(0.5, 1, '#6b8e23');
    var targetF = 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var centerUnit = _cfFmtMoney(Math.max(Math.abs(targetCr), Math.abs(actual)) * 10000000).unit;
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + targetLine
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy + 16) + '" text-anchor="middle" font-size="16" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + centerUnit + '</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawBillsRecvDial(){
    var el = document.getElementById('cf-contract-income');
    if (!el) return;
    el.style.justifyContent = 'center';
    el.style.cursor = 'pointer';
    el.title = 'Click to see per-cycle breakdown';
    var s = _cfBillsRecvState;
    var targetCr = _cfEnteredToRupees(s.target) / 10000000;
    var fmt = _cfFmtMoney(Math.max(Math.abs(s.actual), Math.abs(targetCr)) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    var targetCell = s.editing
        ? '<input class="cf-billsrecv-input" data-field="target" type="number" step="any" value="' + s.target + '" style="width:54px;font-size:13px;padding:0 3px;border:1px solid #b0bec5;border-radius:2px;color:#6b8e23;font-weight:700;">'
        : '<span style="font-size:13px;color:#6b8e23;font-weight:700;">' + (targetCr * divisor).toFixed(2) + '</span>';
    // Actual legend colour always matches whichever arc half the needle
    // is currently pointing into (deep orange up to target, olive-green
    // beyond).
    var maxV = targetCr > 0 ? targetCr * 2 : 1;
    var needleCol = (s.actual / maxV) <= 0.5 ? '#b5540a' : '#6b8e23';
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfBillsRecvDialSvg(s.target, s.actual)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:#111;">Actual: <span style="font-size:13px;color:' + needleCol + ';font-weight:700;">' + (s.actual * divisor).toFixed(2) + '</span></span>'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>Target: ' + targetCell + ' <button class="cf-billsrecv-pencil" title="Edit Target" style="background:none;border:none;cursor:pointer;padding:0 2px;font-size:12px;">&#9998;</button></span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfBillsReceivableDial(){
    var gen = _cfLoadGen;
    _cfBillsRecvState = { target: 0, actual: 0, editing: false };
    _cfDrawBillsRecvDial();
    _cfFetchTarget('bills_receivable_target', function(d){
        if (gen === _cfLoadGen) { _cfBillsRecvState.target = +d.target || 0; _cfDrawBillsRecvDial(); }
    });
    // Actual = sum of transactions where the debit side is a Party
    // account (Other Parties) and the credit side is Contract Income —
    // see actionCfbillsreceivable.
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfbillsreceivable', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfBillsRecvState.actual = (+d.total || 0) / 10000000;
                _cfDrawBillsRecvDial();
            }
        }
    });
}

// Bills Receivable breakdown popup — same draggable/resizable vertical
// bar chart window and per-cycle pattern as the Total Investment
// breakdown (see _cfShowNetInvBreakdown above): one bar per WIP accrual
// cycle, each CLOSED cycle valued at its own CLOSING Bills Receivable
// balance, the current open cycle valued as of today (see
// actionCfbillsreceivablebreakdown). Also shows the average across all
// cycles.
function _cfRedrawBillsRecvBreakdown(){
    var s = window._cfBillsRecvBreakdownState;
    if (!s) return;
    var chartH = Math.max(120, $('#cf-billsrecv-breakdown-win').height() - 130);
    var r = _cfInvestBreakdownChartHtml(s.accounts, chartH);
    $('#cf-billsrecv-breakdown-unit').html(
        '<span style="font-weight:700;color:#251840;">Amount in ' + r.unit + '</span>'
        + '<span style="float:right;font-weight:700;color:#251840;">Average: ' + _cfIndianComma(s.average / (r.unit === 'Crores' ? 10000000 : 100000)) + ' ' + r.unit + '</span>'
    );
    $('#cf-billsrecv-breakdown-chart').html(r.body);
}

function _cfShowBillsRecvBreakdown(accounts, average){
    $('#cf-billsrecv-breakdown-win').remove();
    window._cfBillsRecvBreakdownState = { accounts: accounts, average: average };

    var winW = 640, winH = 380, MIN_W = 340, MIN_H = 240;
    var left = Math.max(10, (window.innerWidth - winW) / 2);
    var top = Math.max(10, (window.innerHeight - winH) / 2);

    var rsHandles = ['n','s','e','w','ne','nw','se','sw'];
    var rsHtml = '';
    for (var h = 0; h < rsHandles.length; h++) {
        rsHtml += '<div class="cf-billsrecv-rs" data-dir="' + rsHandles[h] + '" style="position:absolute;';
        var d = rsHandles[h];
        if (d === 'n') rsHtml += 'top:-4px;left:10px;right:10px;height:10px;cursor:n-resize;';
        else if (d === 's') rsHtml += 'bottom:-4px;left:10px;right:10px;height:10px;cursor:s-resize;';
        else if (d === 'e') rsHtml += 'right:-4px;top:10px;bottom:10px;width:10px;cursor:e-resize;';
        else if (d === 'w') rsHtml += 'left:-4px;top:10px;bottom:10px;width:10px;cursor:w-resize;';
        else if (d === 'ne') rsHtml += 'right:-4px;top:-4px;width:14px;height:14px;cursor:ne-resize;';
        else if (d === 'nw') rsHtml += 'left:-4px;top:-4px;width:14px;height:14px;cursor:nw-resize;';
        else if (d === 'se') rsHtml += 'right:-4px;bottom:-4px;width:14px;height:14px;cursor:se-resize;';
        else if (d === 'sw') rsHtml += 'left:-4px;bottom:-4px;width:14px;height:14px;cursor:sw-resize;';
        rsHtml += 'z-index:20;background:transparent;"></div>';
    }

    var html = '<div id="cf-billsrecv-breakdown-win" style="position:fixed;left:' + left + 'px;top:' + top + 'px;width:' + winW + 'px;height:' + winH + 'px;min-width:' + MIN_W + 'px;min-height:' + MIN_H + 'px;background:#fff;border-radius:6px;box-shadow:0 8px 30px rgba(0,0,0,.35);z-index:200600;display:flex;flex-direction:column;overflow:hidden;">'
        + rsHtml
        + '<div id="cf-billsrecv-breakdown-hdr" style="flex:none;display:flex;justify-content:space-between;align-items:center;padding:10px 16px;background:#334455;cursor:move;user-select:none;">'
        + '<span style="font-family:Barlow Condensed,Arial;font-size:15px;font-weight:700;color:#fff;">Bills Receivable — Per-Cycle Breakdown</span>'
        + '<button id="cf-billsrecv-breakdown-close" style="background:none;border:none;font-size:20px;cursor:pointer;color:#fff;line-height:1;">&times;</button>'
        + '</div>'
        + '<div id="cf-billsrecv-breakdown-unit" style="flex:none;font-family:Barlow Condensed,Arial;font-size:12px;color:#5a6e8c;padding:8px 16px 0;overflow:hidden;"></div>'
        + '<div style="flex:1;min-width:0;min-height:0;overflow-x:hidden;overflow-y:auto;padding:10px 16px 16px;box-sizing:border-box;">'
        + '<div id="cf-billsrecv-breakdown-chart" style="min-width:0;"></div>'
        + '</div>'
        + '</div>';
    $('body').append(html);
    _cfRedrawBillsRecvBreakdown();

    // Drag (header) + resize (8 handles) — same mechanics as the other
    // breakdown windows.
    var win = document.getElementById('cf-billsrecv-breakdown-win');
    var hdr = document.getElementById('cf-billsrecv-breakdown-hdr');
    var _action = null, _sx = 0, _sy = 0, _ox = 0, _oy = 0, _ow = 0, _oh = 0;

    function onDown(e, dir){
        var r = win.getBoundingClientRect();
        _action = dir; _sx = e.clientX; _sy = e.clientY;
        _ox = r.left; _oy = r.top; _ow = r.width; _oh = r.height;
        e.preventDefault();
        if (dir !== 'drag') e.stopPropagation();
    }
    function onMove(e){
        if (!_action) return;
        e.preventDefault();
        var dx = e.clientX - _sx, dy = e.clientY - _sy;
        if (_action === 'drag') {
            var x = Math.max(0, Math.min(_ox + dx, window.innerWidth - win.offsetWidth));
            var y = Math.max(0, Math.min(_oy + dy, window.innerHeight - win.offsetHeight));
            win.style.left = x + 'px'; win.style.top = y + 'px';
        } else {
            var l = _ox, t = _oy, w = _ow, h = _oh;
            if (_action.indexOf('e') > -1) { w = Math.max(MIN_W, _ow + dx); }
            if (_action.indexOf('s') > -1) { h = Math.max(MIN_H, _oh + dy); }
            if (_action.indexOf('w') > -1) { var nw = Math.max(MIN_W, _ow - dx); l = _ox + (_ow - nw); w = nw; }
            if (_action.indexOf('n') > -1) { var nh = Math.max(MIN_H, _oh - dy); t = _oy + (_oh - nh); h = nh; }
            win.style.left = l + 'px'; win.style.top = t + 'px';
            win.style.width = w + 'px'; win.style.height = h + 'px';
            _cfRedrawBillsRecvBreakdown();
        }
    }
    function onUp(){ _action = null; }

    if (typeof window.bindDragTouch === 'function') {
        window.bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-billsrecv-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-billsrecv-rs').each(function(){
            var dir = $(this).data('dir');
            window.bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        window.bindDragTouch(document, 'mousemove', onMove, { passive: false });
        window.bindDragTouch(document, 'mouseup', onUp);
    } else {
        hdr.addEventListener('mousedown', function(e){ if (e.target.id !== 'cf-billsrecv-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-billsrecv-rs').each(function(){
            var dir = $(this).data('dir');
            this.addEventListener('mousedown', function(e){ onDown(e, dir); });
        });
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    }
}
$(document).on('click', '#cf-contract-income', function(e){
    if ($(e.target).closest('.cf-billsrecv-pencil, .cf-billsrecv-input').length) return;
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfbillsreceivablebreakdown', dataType: 'json',
        success: function(d){
            if (d && d.error === 'No') { _cfShowBillsRecvBreakdown(d.accounts || [], +d.average || 0); }
        }
    });
});
$(document).on('click', '#cf-billsrecv-breakdown-close', function(){ $('#cf-billsrecv-breakdown-win').remove(); });

$(document).on('click', '.cf-billsrecv-pencil', function(e){
    e.preventDefault(); e.stopPropagation();
    var s = _cfBillsRecvState;
    if (s.editing) {
        var val = +$('.cf-billsrecv-input[data-field="target"]').val() || 0;
        s.target = val;
        s.editing = false;
        _cfDrawBillsRecvDial();
        $.ajax({
            type: 'POST', url: '../projectsmain/cfsavetarget', dataType: 'json',
            data: { metric_key: 'bills_receivable_target', target_value: val },
            success: function(){ renderCfBars(); }
        });
    } else {
        s.editing = true;
        _cfDrawBillsRecvDial();
    }
});
$(document).on('click', '.cf-billsrecv-input', function(e){ e.stopPropagation(); });

// Bank panel — single flat dark-grey dial (no target/actual split, no
// editable input). Needle position represents "Availed"; left tip is
// labelled "Availed", right tip "Surplus". Placeholder at 0 — no data
// source yet.
// Bank panel: flat dark-grey full-scale dial (0 to W.Capital), with an
// orange overlay arc + needle showing Availed's progress along that scale.
// Left tip label = Availed (actual, static). Right tip label = W.Capital
// (the scale/target, editable via pencil — saved to cf_dashboard_targets
// under metric_key "bank_wcapital" via the same cfsavetarget/cfgettarget
// endpoints the other Cashflow dials use).
var _cfBankState = { wcapital: 0, availed: 0, editing: false };

function _cfBankDialSvg(wcapital, availed){
    // wcapital is the raw number the user typed; reinterpret it via the
    // magnitude rule (< 100 = Lakhs, >= 100 = Crores), then convert to
    // the same Crores-scale float availed is already in.
    var wcapitalCr = _cfEnteredToRupees(wcapital) / 10000000;
    // Full dial scale = 2x W.Capital, so the target (W.Capital) sits at
    // the dial's center (50%) -- matching the target-vs-actual dials
    // elsewhere on this Cashflow tab (see _cfDialSvg's maxV = target*2).
    var maxV = wcapitalCr > 0 ? wcapitalCr * 2 : 1;
    var f = Math.max(0, Math.min(1, availed / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    // Target marker: a bright yellow line across the arc's band at the
    // dial's center (target = W.Capital, half of the full 2x scale), so
    // crossing it visually flags Availed passing the target threshold.
    var targetF = 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    // Center shows the actual (Availed) value + unit, same treatment as
    // the Investments dial (value at cy-30, unit at cy-14, above the
    // needle pivot).
    var fmt = _cfFmtMoney(Math.max(Math.abs(wcapitalCr), Math.abs(availed)) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arc(0, 0.5, '#5a5a5a') + arc(0.5, 1, '#e07b00')
        + targetLine
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 30) + '" text-anchor="middle" font-size="24" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + (availed * divisor).toFixed(2) + '</text>'
        + '<text x="' + cx + '" y="' + (cy - 14) + '" text-anchor="middle" font-size="14" font-weight="700" fill="#5a5a5a" font-family="Barlow Condensed,Arial">' + fmt.unit + '</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawBankDial(){
    var el = document.getElementById('cf-bankdial');
    if (!el) return;
    el.style.justifyContent = 'center';
    var s = _cfBankState;
    // Allocated (W.Capital) is a user-entered target: the raw number
    // typed is reinterpreted via the magnitude rule (< 100 = Lakhs,
    // >= 100 = Crores), same as every other value. Actual (Availed) is
    // derived from a raw rupee backend figure and follows the same rule;
    // both are shown on the SAME unit as whichever is larger, so the two
    // numbers stay comparable at a glance rather than each floating
    // independently between Lakhs and Crores.
    var wcapitalRupees = _cfEnteredToRupees(s.wcapital);
    var availedRupees = (+s.availed || 0) * 10000000;
    var fmt = _cfFmtMoney(Math.max(Math.abs(wcapitalRupees), Math.abs(availedRupees)));
    var divisor = fmt.unit === 'Crores' ? 10000000 : 100000;
    var wcCell = s.editing
        ? '<input class="cf-bank-input" data-field="wcapital" type="number" step="any" value="' + s.wcapital + '" style="width:54px;font-size:13px;padding:0 3px;border:1px solid #b0bec5;border-radius:2px;color:#5a5a5a;font-weight:700;">'
        : '<span style="font-size:13px;color:#5a5a5a;font-weight:700;">' + (wcapitalRupees / divisor).toFixed(2) + '</span>';
    // Actual (Availed) legend colour/swatch always matches whichever arc
    // half the needle is currently pointing into (grey up to target,
    // orange beyond).
    var maxV = wcapitalRupees > 0 ? wcapitalRupees * 2 : 1;
    var needleCol = (availedRupees / maxV) <= 0.5 ? '#5a5a5a' : '#e07b00';
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfBankDialSvg(s.wcapital, s.availed)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>Allocated: ' + wcCell + ' <button class="cf-bank-pencil" title="Edit Allocated" style="background:none;border:none;cursor:pointer;padding:0 2px;font-size:12px;">&#9998;</button></span>'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:' + needleCol + ';border-radius:1px;margin-right:4px;vertical-align:middle;"></span>Actual: <span style="font-size:13px;color:' + needleCol + ';font-weight:700;">' + (availedRupees / divisor).toFixed(2) + '</span></span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfBankDial(){
    var gen = _cfLoadGen;
    _cfBankState = { wcapital: 0, availed: 0, editing: false };
    _cfDrawBankDial();
    _cfFetchTarget('bank_wcapital', function(d){
        if (gen === _cfLoadGen) { _cfBankState.wcapital = +d.target || 0; _cfDrawBankDial(); }
    });
    // Availed = the project's linked bank account's current balance, only
    // when that balance is a net CREDIT (money drawn against the account).
    // A DEBIT balance means nothing is availed — dial stays fully grey,
    // needle at the left tip (availed=0), matching the initial state above.
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfbankbalance', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No' && d.has_bank && d.balance_type === 'credit') {
                // Backend returns availed in raw rupees; W.Capital is
                // entered directly in Crores by the user, so convert here
                // to keep both values on the same scale for the dial.
                _cfBankState.availed = (+d.availed || 0) / 10000000;
                _cfDrawBankDial();
            }
        }
    });
}

$(document).on('click', '.cf-bank-pencil', function(e){
    e.preventDefault(); e.stopPropagation();
    var s = _cfBankState;
    if (s.editing) {
        var val = +$('.cf-bank-input[data-field="wcapital"]').val() || 0;
        s.wcapital = val;
        s.editing = false;
        _cfDrawBankDial();
        $.ajax({
            type: 'POST', url: '../projectsmain/cfsavetarget', dataType: 'json',
            data: { metric_key: 'bank_wcapital', target_value: val },
            success: function(){ renderCfBars(); }
        });
    } else {
        s.editing = true;
        _cfDrawBankDial();
    }
});
$(document).on('click', '.cf-bank-input', function(e){ e.stopPropagation(); });

// Half blue-green / half dark orange dial, shared by every Cashflow panel
// that shows a target-vs-actual metric. Needle = actual value. Target is
// user-editable (pencil icon toggles an inline input; value saves to
// cf_dashboard_targets on the second pencil click, when edit mode is
// closed). Actual has no real data source yet — 0. Same size/geometry for
// every instance; only the element id, metric_key, and unit label differ.
var _cfDials = [
    { elId: 'cf-bank',            metricKey: 'c2c_cycle',       unit: 'Days'   },
    { elId: 'cf-contractincome',  metricKey: 'contract_income', unit: 'Crores', reverseColors: true }
];
var _cfDialState = {}; // elId -> { target, editing }

function _cfDialSvg(target, actual, unit, unitFontSize, reverseColors){
    unitFontSize = unitFontSize || 16;
    var maxV = target > 0 ? target * 2 : 1;
    var f = Math.max(0, Math.min(1, actual / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var firstCol = reverseColors ? '#b5540a' : '#00838f';
    var secondCol = reverseColors ? '#00838f' : '#b5540a';
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arc(0, 0.5, firstCol) + arc(0.5, 1, secondCol)
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy + 16) + '" text-anchor="middle" font-size="' + unitFontSize + '" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + unit + '</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawDial(elId, metricKey, unit, unitFontSize, reverseColors){
    var el = document.getElementById(elId);
    if (!el) return;
    el.style.justifyContent = 'center';
    if (elId === 'cf-bank' || elId === 'cf-contractincome') {
        el.style.cursor = 'pointer';
        el.title = 'Click to see per-cycle breakdown';
    }
    var state = _cfDialState[elId];
    var target = state.target, actual = state.actual || 0;
    // Target/Actual here are user-entered directly in the panel's
    // declared unit for non-money dials (e.g. "Days" for cf-bank's C2C
    // Cycle, left untouched). For money-denominated dials (unit ===
    // 'Crores'), the raw number typed is reinterpreted via the magnitude
    // rule (< 100 = Lakhs, >= 100 = Crores), same as every other value —
    // and the dial's own scale math needs that same reinterpreted value,
    // not the raw typed number.
    var isMoney = unit === 'Crores';
    var dispUnit = unit;
    var targetForSvg = target;
    var targetDisp = target, actualDisp = actual;
    if (isMoney) {
        var targetCr = _cfEnteredToRupees(target) / 10000000;
        targetForSvg = targetCr;
        var fmt = _cfFmtMoney(Math.max(Math.abs(targetCr), Math.abs(actual)) * 10000000);
        dispUnit = fmt.unit;
        var divisor = fmt.unit === 'Crores' ? 1 : 100; // targetCr/actual are already Crores-scale; 1 Crore = 100 Lakhs
        targetDisp = targetCr * divisor;
        actualDisp = actual * divisor;
    }
    var targetCell = state.editing
        ? '<input class="cf-dial-target-input" data-elid="' + elId + '" data-metric="' + metricKey + '" type="number" step="any" value="' + target + '" style="width:58px;font-size:15px;padding:0 3px;border:1px solid #b0bec5;border-radius:2px;">'
        : '<span style="font-size:15px;color:#000;font-weight:700;">' + targetDisp.toFixed(2) + '</span>';
    // Actual legend colour always matches whichever arc half the needle
    // is currently pointing into — same firstCol/secondCol split (and
    // reverseColors flag) used inside _cfDialSvg.
    var maxV = targetForSvg > 0 ? targetForSvg * 2 : 1;
    var firstCol = reverseColors ? '#b5540a' : '#00838f';
    var secondCol = reverseColors ? '#00838f' : '#b5540a';
    var needleCol = (actual / maxV) <= 0.5 ? firstCol : secondCol;
    // Center unit label (in the dial itself) is the single place the
    // Lakhs/Crores unit shows for money dials — Target/Actual below
    // display bare numbers with no unit suffix, to avoid repeating it.
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfDialSvg(targetForSvg, actual, dispUnit, unitFontSize, reverseColors)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:15px;color:#111;">Target: ' + targetCell + ' <button class="cf-dial-target-pencil" data-elid="' + elId + '" title="Edit target" style="background:none;border:none;cursor:pointer;padding:0 2px;font-size:12px;">&#9998;</button></span>'
        + '<span style="font-size:15px;color:#111;">Actual: <span style="font-size:15px;color:' + needleCol + ';font-weight:700;">' + actualDisp.toFixed(2) + '</span></span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfDial(elId, metricKey, unit, unitFontSize, reverseColors){
    var gen = _cfLoadGen;
    _cfDialState[elId] = { target: 0, actual: 0, editing: false };
    _cfDrawDial(elId, metricKey, unit, unitFontSize, reverseColors);
    _cfFetchTarget(metricKey, function(d){
        if (gen === _cfLoadGen) {
            _cfDialState[elId].target = +d.target || 0;
            _cfDrawDial(elId, metricKey, unit, unitFontSize, reverseColors);
        }
    });
    // Cash-to-Cash Cycle panel (cf-bank) — Actual = average days per
    // cycle, where each cycle is the gap between consecutive Contract
    // Income credit transactions (first cycle measured from the
    // project's first Bank/Cash transaction) — see actionCfactualc2c.
    if (elId === 'cf-bank') {
        _cfQueueAjax({
            type: 'POST', url: '../projectsmain/cfactualc2c', dataType: 'json',
            success: function(d){
                if (gen === _cfLoadGen && d && d.error === 'No') {
                    _cfDialState[elId].actual = +d.total || 0;
                    _cfDrawDial(elId, metricKey, unit, unitFontSize, reverseColors);
                }
            }
        });
    }
    // Contract Income panel (cf-contractincome) — Actual = sum of
    // transactions where the debit side is a Bank account and the
    // credit side is a Party account (the cash receipt that clears a
    // receivable raised via Bills Receivable) — see
    // actionCfcontractincome.
    if (elId === 'cf-contractincome') {
        _cfQueueAjax({
            type: 'POST', url: '../projectsmain/cfcontractincome', dataType: 'json',
            success: function(d){
                if (gen === _cfLoadGen && d && d.error === 'No') {
                    _cfDialState[elId].actual = (+d.total || 0) / 10000000;
                    _cfDrawDial(elId, metricKey, unit, unitFontSize, reverseColors);
                }
            }
        });
    }
}

// Cash-to-Cash Cycle breakdown popup — same draggable/resizable vertical
// bar chart window as the other breakdown popups, but showing each
// COMPLETED cycle's own length in days (see
// actionCfactualc2cbreakdown/_cfActualC2cCycleGaps) rather than a rupee
// amount — uses _cfInvestBreakdownChartHtml's 'days' unitMode. No
// "Current" bar: a cycle's length is only defined once it closes with a
// Contract Income credit. Also shows the average across all cycles
// (same figure as the live dial's Actual value).
function _cfRedrawC2cBreakdown(){
    var s = window._cfC2cBreakdownState;
    if (!s) return;
    var chartH = Math.max(120, $('#cf-c2c-breakdown-win').height() - 130);
    var r = _cfInvestBreakdownChartHtml(s.accounts, chartH, 'days');
    $('#cf-c2c-breakdown-unit').html(
        '<span style="font-weight:700;color:#251840;">Days per Cycle</span>'
        + '<span style="float:right;font-weight:700;color:#251840;">Average: ' + _cfIndianComma(s.average) + ' Days</span>'
    );
    $('#cf-c2c-breakdown-chart').html(r.body);
}

function _cfShowC2cBreakdown(accounts, average){
    $('#cf-c2c-breakdown-win').remove();
    window._cfC2cBreakdownState = { accounts: accounts, average: average };

    var winW = 640, winH = 380, MIN_W = 340, MIN_H = 240;
    var left = Math.max(10, (window.innerWidth - winW) / 2);
    var top = Math.max(10, (window.innerHeight - winH) / 2);

    var rsHandles = ['n','s','e','w','ne','nw','se','sw'];
    var rsHtml = '';
    for (var h = 0; h < rsHandles.length; h++) {
        rsHtml += '<div class="cf-c2c-rs" data-dir="' + rsHandles[h] + '" style="position:absolute;';
        var d = rsHandles[h];
        if (d === 'n') rsHtml += 'top:-4px;left:10px;right:10px;height:10px;cursor:n-resize;';
        else if (d === 's') rsHtml += 'bottom:-4px;left:10px;right:10px;height:10px;cursor:s-resize;';
        else if (d === 'e') rsHtml += 'right:-4px;top:10px;bottom:10px;width:10px;cursor:e-resize;';
        else if (d === 'w') rsHtml += 'left:-4px;top:10px;bottom:10px;width:10px;cursor:w-resize;';
        else if (d === 'ne') rsHtml += 'right:-4px;top:-4px;width:14px;height:14px;cursor:ne-resize;';
        else if (d === 'nw') rsHtml += 'left:-4px;top:-4px;width:14px;height:14px;cursor:nw-resize;';
        else if (d === 'se') rsHtml += 'right:-4px;bottom:-4px;width:14px;height:14px;cursor:se-resize;';
        else if (d === 'sw') rsHtml += 'left:-4px;bottom:-4px;width:14px;height:14px;cursor:sw-resize;';
        rsHtml += 'z-index:20;background:transparent;"></div>';
    }

    var html = '<div id="cf-c2c-breakdown-win" style="position:fixed;left:' + left + 'px;top:' + top + 'px;width:' + winW + 'px;height:' + winH + 'px;min-width:' + MIN_W + 'px;min-height:' + MIN_H + 'px;background:#fff;border-radius:6px;box-shadow:0 8px 30px rgba(0,0,0,.35);z-index:200600;display:flex;flex-direction:column;overflow:hidden;">'
        + rsHtml
        + '<div id="cf-c2c-breakdown-hdr" style="flex:none;display:flex;justify-content:space-between;align-items:center;padding:10px 16px;background:#334455;cursor:move;user-select:none;">'
        + '<span style="font-family:Barlow Condensed,Arial;font-size:15px;font-weight:700;color:#fff;">Cash-to-Cash Cycle — Per-Cycle Breakdown</span>'
        + '<button id="cf-c2c-breakdown-close" style="background:none;border:none;font-size:20px;cursor:pointer;color:#fff;line-height:1;">&times;</button>'
        + '</div>'
        + '<div id="cf-c2c-breakdown-unit" style="flex:none;font-family:Barlow Condensed,Arial;font-size:12px;color:#5a6e8c;padding:8px 16px 0;overflow:hidden;"></div>'
        + '<div style="flex:1;min-width:0;min-height:0;overflow-x:hidden;overflow-y:auto;padding:10px 16px 16px;box-sizing:border-box;">'
        + '<div id="cf-c2c-breakdown-chart" style="min-width:0;"></div>'
        + '</div>'
        + '</div>';
    $('body').append(html);
    _cfRedrawC2cBreakdown();

    // Drag (header) + resize (8 handles) — same mechanics as the other
    // breakdown windows.
    var win = document.getElementById('cf-c2c-breakdown-win');
    var hdr = document.getElementById('cf-c2c-breakdown-hdr');
    var _action = null, _sx = 0, _sy = 0, _ox = 0, _oy = 0, _ow = 0, _oh = 0;

    function onDown(e, dir){
        var r = win.getBoundingClientRect();
        _action = dir; _sx = e.clientX; _sy = e.clientY;
        _ox = r.left; _oy = r.top; _ow = r.width; _oh = r.height;
        e.preventDefault();
        if (dir !== 'drag') e.stopPropagation();
    }
    function onMove(e){
        if (!_action) return;
        e.preventDefault();
        var dx = e.clientX - _sx, dy = e.clientY - _sy;
        if (_action === 'drag') {
            var x = Math.max(0, Math.min(_ox + dx, window.innerWidth - win.offsetWidth));
            var y = Math.max(0, Math.min(_oy + dy, window.innerHeight - win.offsetHeight));
            win.style.left = x + 'px'; win.style.top = y + 'px';
        } else {
            var l = _ox, t = _oy, w = _ow, h = _oh;
            if (_action.indexOf('e') > -1) { w = Math.max(MIN_W, _ow + dx); }
            if (_action.indexOf('s') > -1) { h = Math.max(MIN_H, _oh + dy); }
            if (_action.indexOf('w') > -1) { var nw = Math.max(MIN_W, _ow - dx); l = _ox + (_ow - nw); w = nw; }
            if (_action.indexOf('n') > -1) { var nh = Math.max(MIN_H, _oh - dy); t = _oy + (_oh - nh); h = nh; }
            win.style.left = l + 'px'; win.style.top = t + 'px';
            win.style.width = w + 'px'; win.style.height = h + 'px';
            _cfRedrawC2cBreakdown();
        }
    }
    function onUp(){ _action = null; }

    if (typeof window.bindDragTouch === 'function') {
        window.bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-c2c-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-c2c-rs').each(function(){
            var dir = $(this).data('dir');
            window.bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        window.bindDragTouch(document, 'mousemove', onMove, { passive: false });
        window.bindDragTouch(document, 'mouseup', onUp);
    } else {
        hdr.addEventListener('mousedown', function(e){ if (e.target.id !== 'cf-c2c-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-c2c-rs').each(function(){
            var dir = $(this).data('dir');
            this.addEventListener('mousedown', function(e){ onDown(e, dir); });
        });
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    }
}
$(document).on('click', '#cf-bank', function(e){
    if ($(e.target).closest('.cf-dial-target-pencil, .cf-dial-target-input').length) return;
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfactualc2cbreakdown', dataType: 'json',
        success: function(d){
            if (d && d.error === 'No') { _cfShowC2cBreakdown(d.accounts || [], +d.average || 0); }
        }
    });
});
$(document).on('click', '#cf-c2c-breakdown-close', function(){ $('#cf-c2c-breakdown-win').remove(); });

// Contract Income breakdown popup — same draggable/resizable vertical
// bar chart window as the other breakdown popups, one bar per Contract
// Income credit transaction (each cycle's own billed amount — see
// actionCfcontractincomebreakdown), labelled by the month/year it was
// credited. Shows both the average per cycle AND the cumulative
// (summed) total across every credit.
function _cfRedrawContractIncomeBreakdown(){
    var s = window._cfContractIncomeBreakdownState;
    if (!s) return;
    var chartH = Math.max(120, $('#cf-ci-breakdown-win').height() - 130);
    var r = _cfInvestBreakdownChartHtml(s.accounts, chartH);
    var cumFmt = _cfFmtMoney(s.cumulative);
    var cumDisp = _cfIndianComma(s.cumulative / (cumFmt.unit === 'Crores' ? 10000000 : 100000)) + ' ' + cumFmt.unit;
    $('#cf-ci-breakdown-unit').html(
        '<span style="font-weight:700;color:#251840;">Amount in ' + r.unit + '</span>'
        + '<span style="float:right;font-weight:700;color:#251840;">Average: ' + r.total + ' ' + r.unit + ' &nbsp;|&nbsp; Cumulative: ' + cumDisp + '</span>'
    );
    $('#cf-ci-breakdown-chart').html(r.body);
}

function _cfShowContractIncomeBreakdown(accounts, average, cumulative){
    $('#cf-ci-breakdown-win').remove();
    // _cfInvestBreakdownChartHtml's header line reads "Total:" from
    // r.total, which is the SUM of the bars shown — that's already the
    // cumulative total, not the average, so the average is passed
    // through separately here (currently unused by the chart itself,
    // shown instead via the Average/Cycle label built in
    // _cfRedrawContractIncomeBreakdown from s.average directly).
    window._cfContractIncomeBreakdownState = { accounts: accounts, average: average, cumulative: cumulative };

    var winW = 640, winH = 380, MIN_W = 340, MIN_H = 240;
    var left = Math.max(10, (window.innerWidth - winW) / 2);
    var top = Math.max(10, (window.innerHeight - winH) / 2);

    var rsHandles = ['n','s','e','w','ne','nw','se','sw'];
    var rsHtml = '';
    for (var h = 0; h < rsHandles.length; h++) {
        rsHtml += '<div class="cf-ci-rs" data-dir="' + rsHandles[h] + '" style="position:absolute;';
        var d = rsHandles[h];
        if (d === 'n') rsHtml += 'top:-4px;left:10px;right:10px;height:10px;cursor:n-resize;';
        else if (d === 's') rsHtml += 'bottom:-4px;left:10px;right:10px;height:10px;cursor:s-resize;';
        else if (d === 'e') rsHtml += 'right:-4px;top:10px;bottom:10px;width:10px;cursor:e-resize;';
        else if (d === 'w') rsHtml += 'left:-4px;top:10px;bottom:10px;width:10px;cursor:w-resize;';
        else if (d === 'ne') rsHtml += 'right:-4px;top:-4px;width:14px;height:14px;cursor:ne-resize;';
        else if (d === 'nw') rsHtml += 'left:-4px;top:-4px;width:14px;height:14px;cursor:nw-resize;';
        else if (d === 'se') rsHtml += 'right:-4px;bottom:-4px;width:14px;height:14px;cursor:se-resize;';
        else if (d === 'sw') rsHtml += 'left:-4px;bottom:-4px;width:14px;height:14px;cursor:sw-resize;';
        rsHtml += 'z-index:20;background:transparent;"></div>';
    }

    var html = '<div id="cf-ci-breakdown-win" style="position:fixed;left:' + left + 'px;top:' + top + 'px;width:' + winW + 'px;height:' + winH + 'px;min-width:' + MIN_W + 'px;min-height:' + MIN_H + 'px;background:#fff;border-radius:6px;box-shadow:0 8px 30px rgba(0,0,0,.35);z-index:200600;display:flex;flex-direction:column;overflow:hidden;">'
        + rsHtml
        + '<div id="cf-ci-breakdown-hdr" style="flex:none;display:flex;justify-content:space-between;align-items:center;padding:10px 16px;background:#334455;cursor:move;user-select:none;">'
        + '<span style="font-family:Barlow Condensed,Arial;font-size:15px;font-weight:700;color:#fff;">Contract Income — Per-Cycle Breakdown</span>'
        + '<button id="cf-ci-breakdown-close" style="background:none;border:none;font-size:20px;cursor:pointer;color:#fff;line-height:1;">&times;</button>'
        + '</div>'
        + '<div id="cf-ci-breakdown-unit" style="flex:none;font-family:Barlow Condensed,Arial;font-size:12px;color:#5a6e8c;padding:8px 16px 0;overflow:hidden;"></div>'
        + '<div style="flex:1;min-width:0;min-height:0;overflow-x:hidden;overflow-y:auto;padding:10px 16px 16px;box-sizing:border-box;">'
        + '<div id="cf-ci-breakdown-chart" style="min-width:0;"></div>'
        + '</div>'
        + '</div>';
    $('body').append(html);
    _cfRedrawContractIncomeBreakdown();

    // Drag (header) + resize (8 handles) — same mechanics as the other
    // breakdown windows.
    var win = document.getElementById('cf-ci-breakdown-win');
    var hdr = document.getElementById('cf-ci-breakdown-hdr');
    var _action = null, _sx = 0, _sy = 0, _ox = 0, _oy = 0, _ow = 0, _oh = 0;

    function onDown(e, dir){
        var r = win.getBoundingClientRect();
        _action = dir; _sx = e.clientX; _sy = e.clientY;
        _ox = r.left; _oy = r.top; _ow = r.width; _oh = r.height;
        e.preventDefault();
        if (dir !== 'drag') e.stopPropagation();
    }
    function onMove(e){
        if (!_action) return;
        e.preventDefault();
        var dx = e.clientX - _sx, dy = e.clientY - _sy;
        if (_action === 'drag') {
            var x = Math.max(0, Math.min(_ox + dx, window.innerWidth - win.offsetWidth));
            var y = Math.max(0, Math.min(_oy + dy, window.innerHeight - win.offsetHeight));
            win.style.left = x + 'px'; win.style.top = y + 'px';
        } else {
            var l = _ox, t = _oy, w = _ow, h = _oh;
            if (_action.indexOf('e') > -1) { w = Math.max(MIN_W, _ow + dx); }
            if (_action.indexOf('s') > -1) { h = Math.max(MIN_H, _oh + dy); }
            if (_action.indexOf('w') > -1) { var nw = Math.max(MIN_W, _ow - dx); l = _ox + (_ow - nw); w = nw; }
            if (_action.indexOf('n') > -1) { var nh = Math.max(MIN_H, _oh - dy); t = _oy + (_oh - nh); h = nh; }
            win.style.left = l + 'px'; win.style.top = t + 'px';
            win.style.width = w + 'px'; win.style.height = h + 'px';
            _cfRedrawContractIncomeBreakdown();
        }
    }
    function onUp(){ _action = null; }

    if (typeof window.bindDragTouch === 'function') {
        window.bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-ci-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-ci-rs').each(function(){
            var dir = $(this).data('dir');
            window.bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        window.bindDragTouch(document, 'mousemove', onMove, { passive: false });
        window.bindDragTouch(document, 'mouseup', onUp);
    } else {
        hdr.addEventListener('mousedown', function(e){ if (e.target.id !== 'cf-ci-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-ci-rs').each(function(){
            var dir = $(this).data('dir');
            this.addEventListener('mousedown', function(e){ onDown(e, dir); });
        });
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    }
}
$(document).on('click', '#cf-contractincome', function(e){
    if ($(e.target).closest('.cf-dial-target-pencil, .cf-dial-target-input').length) return;
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfcontractincomebreakdown', dataType: 'json',
        success: function(d){
            if (d && d.error === 'No') { _cfShowContractIncomeBreakdown(d.accounts || [], +d.average || 0, +d.cumulative || 0); }
        }
    });
});
$(document).on('click', '#cf-ci-breakdown-close', function(){ $('#cf-ci-breakdown-win').remove(); });

$(document).on('click', '.cf-dial-target-pencil', function(e){
    e.preventDefault(); e.stopPropagation();
    var elId = $(this).data('elid');
    var d = _cfDials.filter(function(x){ return x.elId === elId; })[0];
    if (!d) return;
    var state = _cfDialState[elId];
    if (state.editing) {
        var val = +$('.cf-dial-target-input[data-elid="' + elId + '"]').val() || 0;
        state.target = val;
        state.editing = false;
        _cfDrawDial(d.elId, d.metricKey, d.unit, d.unitFontSize, d.reverseColors);
        $.ajax({
            type: 'POST', url: '../projectsmain/cfsavetarget', dataType: 'json',
            data: { metric_key: d.metricKey, target_value: val },
            success: function(){ renderCfBars(); }
        });
    } else {
        state.editing = true;
        _cfDrawDial(d.elId, d.metricKey, d.unit, d.unitFontSize, d.reverseColors);
    }
});
$(document).on('click', '.cf-dial-target-input', function(e){ e.stopPropagation(); });

// ── Drag + Resize: Cashflow modal ────────────────────────────────────────
// Centered via transform on first open; anchored to fixed left/top pixel
// values on first drag/resize interaction so it behaves like every other
// floating window afterwards.
(function(){
    var win = document.getElementById('cf-modal');
    var hdr = document.getElementById('cf-hdr');
    if (!win || !hdr) return;
    var bdt = (typeof window.bindDragTouch === 'function') ? window.bindDragTouch : function(el, ev, fn, opts){ el.addEventListener(ev, fn, opts); };
    var MIN_W = 600, MIN_H = 400, _action = null, _sx = 0, _sy = 0, _ox = 0, _oy = 0, _ow = 0, _oh = 0;

    function _anchor(){
        var r = win.getBoundingClientRect();
        win.style.transform = 'none';
        win.style.left = r.left + 'px'; win.style.top = r.top + 'px';
        win.style.width = r.width + 'px'; win.style.height = r.height + 'px';
        return r;
    }

    bdt(hdr, 'mousedown', function(e){
        if (e.target.id === 'cf-close') return;
        var r = _anchor();
        _action = 'drag'; _sx = e.clientX; _sy = e.clientY; _ox = r.left; _oy = r.top;
        e.preventDefault();
    });
    win.querySelectorAll('.cf-rs').forEach(function(el){
        bdt(el, 'mousedown', function(e){
            var r = _anchor();
            _action = el.getAttribute('data-dir');
            _sx = e.clientX; _sy = e.clientY; _ox = r.left; _oy = r.top; _ow = r.width; _oh = r.height;
            e.preventDefault(); e.stopPropagation();
        });
    });
    bdt(document, 'mousemove', function(e){
        if (!_action) return;
        var dx = e.clientX - _sx, dy = e.clientY - _sy;
        if (_action === 'drag') {
            win.style.left = Math.max(0, _ox + dx) + 'px';
            win.style.top  = Math.max(0, _oy + dy) + 'px';
        } else {
            var l = _ox, t = _oy, w = _ow, h = _oh;
            if (_action.indexOf('e') > -1) { w = Math.max(MIN_W, _ow + dx); }
            if (_action.indexOf('s') > -1) { h = Math.max(MIN_H, _oh + dy); }
            if (_action.indexOf('w') > -1) { var nw = Math.max(MIN_W, _ow - dx); l = _ox + (_ow - nw); w = nw; }
            if (_action.indexOf('n') > -1) { var nh = Math.max(MIN_H, _oh - dy); t = _oy + (_oh - nh); h = nh; }
            win.style.left = l + 'px'; win.style.top = t + 'px';
            win.style.width = w + 'px'; win.style.height = h + 'px';
        }
    }, { passive: false });
    bdt(document, 'mouseup', function(){ _action = null; });
})();

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
        'Act', fmtCost(actUCA)+unitLbl,
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
        +(hasActual
          ? '<div>Actual Cost of Work Done &nbsp;<span style="color:'+(overWD?'#e8820c':'#1b9e8e')+'">'+fmtCost(actCostWD)+'</span></div>'
            +'<div style="color:'+(overWD?'#e8820c':'#1b9e8e')+'">Difference &nbsp;'+(diffWDLabel||fmtCost(0))+'</div>'
          : '<div>Actual Cost of Work Done &nbsp;<span style="color:#888;font-style:italic;font-weight:500">No Cost Reported</span></div>'
        )
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
        html += '<div style="width:'+((actual-budgeted)/effMax*100).toFixed(1)+'%;background:#E65100;min-width:3px;"></div>';
    } else if (actual > 0 && actual < budgeted){
        html += '<div style="width:'+(actual/effMax*100).toFixed(1)+'%;background:#00838f;min-width:3px;"></div>';
        html += '<div style="width:'+((budgeted-actual)/effMax*100).toFixed(1)+'%;background:#f0c419;min-width:3px;"></div>';
    } else if (startDelay > 0){
        html += '<div style="width:'+(budgeted/effMax*100).toFixed(1)+'%;background:#00838f;min-width:3px;"></div>';
        html += '<div style="width:'+(startDelay/effMax*100).toFixed(1)+'%;background:#E65100;min-width:3px;"></div>';
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
        elapsedProj = Math.max(0, Math.round((today3 - ps) / 86400000) + 1);
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
        +'<span><span class="ld" style="background:#E65100"></span>Delay</span>'
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
            + (dl > 0  ? '<span style="color:#E65100;margin-left:3px;">+' + fmt2(dl) + '</span>' : '')
            + (isSlack ? '<span style="color:#27ae60;margin-left:3px;">-' + fmt2(dlAbs) + '</span>' : '')) : '';
        html += '<div class="'+rowCls+'" '+(r.id?'data-aid="'+r.id+'" style="cursor:pointer;display:flex;align-items:center;"':'style="display:flex;align-items:center;"')+'>'
            +'<div class="blbl"'+tipAttr+' style="color:#000;" title="'+r.name+'">'+sh(r.name,30)+'</div>'
            +'<div class="btrk" style="flex:1;">'
            +(sc>0?'<div class="bs" style="width:'+scPct+'%;background:'+barCol+'"></div>':'')
            +(dl>0?'<div class="bs" style="width:'+dlPct+'%;background:#E65100"></div>':'')
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
    var elapsedDays  = (asd && lrd) ? Math.max(0, Math.round((new Date(lrd) - new Date(asd)) / 86400000) + 1) : 0;
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
        + (startDelay > 0 && !isOver && !isUnder ? ' &nbsp;<span style="color:#E65100;">+' + startDelay + ' d</span>' : '')
        + '</span>'
        + '</div>'
        + bar
        + (function(){
            // Projected actual end = today + ceil(remaining_qty / actual_productivity) - 1
            // (today counts as day 1 of remaining period)
            var actStartStr = k.reported_start_date || k.act_start_date || '';
            var tq2 = +k.target_qty || 0;
            var aq2 = +k.actual_qty || 0;
            var actProd = +k.actual_productivity || 0;
            var projActEndStr = '-';
            var projActEndCol = '#aaa';
            if (actStartStr && actStartStr !== '0000-00-00' && aq2 > 0 && tq2 > 0 && actProd > 0) {
                var remainingDays = Math.ceil(Math.max(0, tq2 - aq2) / actProd);
                var sd3 = new Date(); sd3.setHours(0,0,0,0);
                sd3.setDate(sd3.getDate() + remainingDays - 1); // today is day 1
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
                elapsedDays = Math.max(0, Math.round((today - startD) / 86400000) + 1);
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
    doResourceCapacity(k.resource_latest);
}

// ── Resource Capacity: latest reported qty per resource, one bar each
// (not grouped by type — two resources of the same type each get their own
// bar), labelled underneath by Resource Type. Hover a bar for a Resource
// Name / Quantity tooltip. ──
var _pdResData = [];
function doResourceCapacity(items){
    var el = document.getElementById('pd-g7'); if (!el) return;
    var cols = ['#3461b8','#d4845a','#27afc4','#7c5cbf','#f0c419','#8fa3bc','#ec407a','#26a69a'];
    items = items || [];
    _pdResData = items;
    if (!items.length){
        el.innerHTML = '<div style="text-align:center;font-size:12px;color:#5a6e8c;padding:18px 0">No data</div>';
        return;
    }
    var maxVal = 0;
    items.forEach(function(r){ maxVal = Math.max(maxVal, +r.qty || 0); });
    if (!maxVal) maxVal = 1;
    var gridLines = 4;
    var step = maxVal / gridLines;
    var chartH = 90;

    var axis = '<div style="display:flex;flex-direction:column;justify-content:space-between;height:' + chartH + 'px;padding-right:4px;text-align:right;font-size:9px;color:#8ab4d8;">';
    for (var g = gridLines; g >= 0; g--) axis += '<span>' + Math.round(step * g) + '</span>';
    axis += '</div>';

    var grids = '<div style="position:absolute;left:0;right:0;top:0;bottom:0;pointer-events:none;">';
    for (var g2 = 0; g2 <= gridLines; g2++) {
        grids += '<div style="position:absolute;left:0;right:0;top:' + ((gridLines - g2) / gridLines * 100).toFixed(1) + '%;border-top:1px solid #e8ecf3;"></div>';
    }
    grids += '</div>';

    var bars = '', labels = '';
    items.forEach(function(r, i){
        var qty = +r.qty || 0;
        var col = cols[i % cols.length];
        var pct = Math.max(3, (qty / maxVal * 100)).toFixed(1) + '%';
        bars   += '<div class="rescol pd-res-bar-col" data-idx="' + i + '" style="height:100%;cursor:default"><div class="resb" style="width:16px;height:' + pct + ';min-height:3px;background:' + col + ';border:1px solid rgba(0,0,0,.15);"></div></div>';
        labels += '<div class="reslbl" style="color:#111">' + sh(r.type_name || '', 24) + '</div>';
    });

    el.innerHTML = '<div style="display:flex;gap:0;">'
        + axis
        + '<div style="flex:1;position:relative;min-width:0;"><div class="resbars" style="height:' + chartH + 'px;">' + grids + bars + '</div></div>'
        + '</div>'
        + '<div class="reslabels" style="margin-left:26px;">' + labels + '</div>';
}

(function(){
    function _pdResTip(){
        var t = document.getElementById('pd-res-tip');
        if (!t) { t = document.createElement('div'); t.id = 'pd-res-tip'; t.className = 'pd-res-tip'; document.body.appendChild(t); }
        return t;
    }
    $(document).on('mouseenter', '.pd-res-bar-col', function(e){
        var idx = +this.dataset.idx;
        var r = _pdResData[idx];
        if (!r) return;
        var tip = _pdResTip();
        tip.innerHTML = '<table><thead><tr><th>Resource Name</th><th style="text-align:right;">Quantity</th></tr></thead>'
            + '<tbody><tr><td>' + $('<div>').text(r.name || '').html() + '</td>'
            + '<td style="text-align:right;font-weight:700;">' + (+r.qty || 0) + '</td></tr></tbody></table>';
        var rect = this.getBoundingClientRect();
        tip.style.display = 'block';
        var tw = tip.offsetWidth || 180, th = tip.offsetHeight || 60;
        var left = Math.min(rect.left, window.innerWidth - tw - 8);
        var top = rect.top - th - 6;
        if (top < 4) top = rect.bottom + 6;
        tip.style.left = Math.max(4, left) + 'px';
        tip.style.top = top + 'px';
    }).on('mouseleave', '.pd-res-bar-col', function(){
        var tip = document.getElementById('pd-res-tip');
        if (tip) tip.style.display = 'none';
    });
})();

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
