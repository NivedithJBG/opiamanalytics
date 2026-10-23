/* Performance Dashboard -- popup modal */
(function(){
'use strict';

if(typeof window.bindDragTouch !== 'function'){
  // Standalone fallback in case this script loads on a page without
  // layouts/main.php, where the shared shim normally comes from. Without it,
  // drag here only ever bound mousedown, so dragging never worked on touch
  // devices (iPad).
  (function(){
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
  })();
}

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
    if (typeof window.popupBringToFront === 'function') window.popupBringToFront('pd-modal');
    if (typeof window.popupCascade === 'function') window.popupCascade('pd-modal');
    if (typeof window.popupFitPortrait === 'function') window.popupFitPortrait('pd-modal');
    if (!_loaded) { loadAll(); _loaded = true; }
});
// Only the X button closes this — clicking elsewhere (or another dashboard
// button) must never dismiss an already-open dashboard.
$(document).on('click', '#pd-close', function(){
    $('#pd-modal, #pd-bk').removeClass('pd-open');
    $('#pd-tip').hide();
    delete document.getElementById('pd-modal').dataset.portraitFitted;
});
// Bring to front on click anywhere inside, same as the other floaters.
$(document).on('mousedown touchstart', '#pd-modal', function(){
    if (typeof window.popupBringToFront === 'function') window.popupBringToFront('pd-modal');
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
    if (typeof window.popupCascade === 'function') window.popupCascade('cd-modal');
    if (typeof window.popupFitPortrait === 'function') window.popupFitPortrait('cd-modal');
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
// Only the X button closes this — clicking elsewhere (or another dashboard
// button) must never dismiss an already-open dashboard.
$(document).on('click', '#cd-close', function(){
    $('#cd-modal, #cd-bk').removeClass('cd-open');
    delete document.getElementById('cd-modal').dataset.portraitFitted;
});
// Bring to front on click anywhere inside, same as the other floaters.
$(document).on('mousedown touchstart', '#cd-modal', function(){
    if (typeof window.popupBringToFront === 'function') window.popupBringToFront('cd-modal');
});
$(document).on('click', '#cd-c4 .brow[data-aid]', function(){
    loadCdActivityData($(this).data('aid'));
});

// Cashflow money-value formatter: values under 1 Crore (1,00,00,000,
// i.e. 99 Lakhs or less) display in Lakhs; values at or above 1 Crore
// display in Crores. Callers pass Math.max(target, actual) in so BOTH
// figures on a dial always share the same unit — the unit is decided
// once, by whichever of the two is larger, never independently per
// value (that independent-conversion path was the actual bug: it let
// Target and Actual land in different units and look mismatched).
// Input is raw rupees; returns {value, unit} for display, and always
// positive magnitudes (dial panels treat everything as absolute value,
// per earlier explicit request — negative underlying balances should
// not display a minus sign).
function _cfFmtMoney(rupees){
    var v = Math.abs(+rupees || 0);
    if (v >= 10000000) {
        return { value: v / 10000000, unit: 'Crores' };
    }
    return { value: v / 100000, unit: 'Lakhs' };
}

// Converts a manually-typed editable target (T.WIP, W.Capital, Contract
// Income target, etc.) into raw rupees. ALWAYS reads the typed number as
// Lakhs (e.g. entering "110" means 110 Lakhs, i.e. 1.1 Crores) — no
// magnitude-based guessing on the input side. This is what actually
// caused the mismatch bug: the old rule read "110" as 110 CRORES
// (>=100 triggered the Crores branch) while Actual displayed in
// Crores too but at a tiny fraction, making Target look 1000x larger
// than intended. _cfFmtMoney's own Lakhs/Crores DISPLAY switch (above)
// is unaffected by this — it still auto-picks Crores once the value is
// large enough, it just no longer double-guesses on entry.
function _cfEnteredToRupees(n){
    var v = +n || 0;
    return v * 100000;
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
// Three separate Cashflow windows, side by side, opened together by the Cashflow
// button. Each has its own title bar and close button. The panels are moved from the
// old single window into them; their IDs are kept, so the drawing code is unchanged.
var _cfSegWins = [
    // Same segment widths as the single window at medium size: 960px in total,
    // Value Drivers 198px wide, the others 182px per panel column, with 26px gaps between the segments.
    { id: 'cf-win-value', title: 'Value Drivers',       cols: 1, head: '#1a0a2e', left: 'calc(50% - 580px)', width: '198px', panels: [1], center: true },
    { id: 'cf-win-c2c',   title: 'Cash-to-Cash Cycle',  cols: 3, head: '#1a0a2e', left: 'calc(50% - 356px)', width: '546px', panels: [3, 4, 5], center: true },
    { id: 'cf-win-roi',   title: 'Profit - ROI - Free Cash',   cols: 2, head: '#1a0a2e', left: 'calc(50% + 216px)', width: '364px', panels: [6, 7], center: true }
];
// Moves all three Cashflow windows by the same amount (used by the shared header).
function cfMoveSegmentWindows(dx, dy){
    _cfSegWins.forEach(function(w){
        var el = document.getElementById(w.id);
        if (!el) return;
        el.style.left = (el.offsetLeft + dx) + 'px';
        el.style.top  = (el.offsetTop + dy) + 'px';
    });
}
// Fits the three Cashflow windows to the iPad (or any) screen, in both orientations.
// The group is 962px wide; it is scaled down only when the screen is narrower than that
// (13" iPad portrait 1024px, landscape 1366px). Scaling uses transform, so the window
// positions stay in screen pixels and the jQuery UI drag and touch drag keep working.
function cfFitLayout(){
    var GROUP_W = 1160;   // Value Drivers 198 + 26 + C2C 546 + 26 + ROI 364
    var vw = window.innerWidth, vh = window.innerHeight;
    var portrait = vh > vw;
    // Portrait (iPad 1024 wide): use nearly the full width, and keep the windows
    // in the same width-to-height proportion as landscape (about 0.61), so the
    // panels look the same instead of tall and narrow.
    // Touch screens (iPad) in landscape: scale up to the screen width (up to 1.15x)
    // and keep the windows a little flatter, so the panels are not tall and narrow.
    var touch = (navigator.maxTouchPoints || 0) > 1;
    var maxS = (touch && !portrait) ? 1.15 : 1;
    var s = Math.min(maxS, (vw - (portrait ? 12 : 24)) / GROUP_W);
    var top = Math.max(64, Math.round(vh * 0.10));          // windows start below the toolbar
    var winH = Math.min(vh * (touch && !portrait ? 0.74 : 0.68), vh - top - 16, GROUP_W * s * (portrait ? 0.61 : (touch ? 0.56 : 0.72)));
    var groupLeft = (vw - GROUP_W * s) / 2;
    var hdr = document.getElementById('cf-shared-hdr');
    if (hdr) {
        hdr.style.transformOrigin = '0 0';
        hdr.style.transform = 'scale(' + s + ')';
        hdr.style.width = GROUP_W + 'px';
        hdr.style.left = groupLeft + 'px';
        hdr.style.top = (top - 44 * s) + 'px';
    }
    // Each window's offset from the start of the group (same as its calc(50% ...) left).
    var offsets = { 'cf-win-value': 0, 'cf-win-c2c': 224, 'cf-win-roi': 796 };
    _cfSegWins.forEach(function(w){
        var el = document.getElementById(w.id);
        if (!el) return;
        el.style.transformOrigin = '0 0';
        el.style.transform = 'scale(' + s + ')';
        el.style.left = (groupLeft + offsets[w.id] * s) + 'px';
        el.style.top = top + 'px';
        el.style.height = (winH / s) + 'px';
    });
}
$(window).on('resize orientationchange', function(){
    if ($('#cf-win-value').is(':visible')) cfFitLayout();
});
// Larger touch targets for the close buttons and title bars on touch screens (iPad).
$('head').append('<style>@media (pointer:coarse){#cf-shared-close,.cf-seg-close{min-width:36px;min-height:36px;font-size:22px !important;}.cf-seg-hdr{padding:10px 12px !important;}}</style>');
function cfShowSegmentWindows(){
    _cfSegWins.forEach(function(w){ $('#' + w.id).css('display', 'flex'); });
    $('#cf-shared-hdr').css('display', 'flex');
    cfFitLayout();
}
function cfHideSegmentWindows(){
    _cfSegWins.forEach(function(w){ $('#' + w.id).css('display', 'none'); });
    $('#cf-shared-hdr').css('display', 'none');
}
function cfBuildSegmentWindows(){
    if (document.getElementById('cf-win-value')) return;
    // Shared header above the three windows: "Project Management" and the project name.
    // Dragging it (or touching it) moves all three windows together; its × closes all.
    $('body').append(
        '<div id="cf-shared-hdr" style="display:none;position:fixed;left:calc(50% - 580px);top:calc(12vh - 44px);width:1160px;height:30px;box-sizing:border-box;background:#081142;color:#fff;padding:0 12px;align-items:center;justify-content:space-between;cursor:move;user-select:none;touch-action:none;z-index:100200;font-family:Nunito,Arial,sans-serif;">'
        + '<span><span style="font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;">Project Management</span>'
        + '<span id="cf-shared-name" style="font-size:12px;margin-left:12px;opacity:.9;"></span></span>'
        + '<button type="button" id="cf-shared-close" style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;line-height:1;padding:0 3px;">&times;</button>'
        + '</div>'
    );
    var $sh = $('#cf-shared-hdr');
    $sh.draggable({
        containment: 'window', scroll: false,
        start: function(e, ui){ $sh.data('lx', ui.position.left); $sh.data('ly', ui.position.top); },
        drag: function(e, ui){
            cfMoveSegmentWindows(ui.position.left - $sh.data('lx'), ui.position.top - $sh.data('ly'));
            $sh.data('lx', ui.position.left); $sh.data('ly', ui.position.top);
        }
    });
    var panels = $('#cf-grid > .panel').toArray();
    _cfSegWins.forEach(function(w){
        $('body').append(
            '<div id="' + w.id + '" class="cf-seg-win" style="display:none;position:fixed;top:12vh;left:' + w.left + ';width:' + w.width + ';height:68vh;color:#fff;background:#f0f2f7;border-radius:6px;box-shadow:0 8px 32px rgba(0,0,0,.7);flex-direction:column;overflow:hidden;z-index:100200;">'
            + '<div class="cf-seg-hdr" style="flex:none;display:flex;align-items:center;justify-content:space-between;background:' + w.head + ';color:#fff;padding:6px 10px;cursor:move;user-select:none;font-family:Nunito,Arial,sans-serif;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.02em;">'
            + '<span style="' + (w.center ? 'flex:1;text-align:center;' : '') + '">' + w.title + '</span>'
            + '<button type="button" class="cf-seg-close" data-win="' + w.id + '" style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;line-height:1;padding:0 3px;">&times;</button>'
            + '</div>'
            + '<div class="cf-seg-body" style="flex:1;min-height:0;box-sizing:border-box;padding-top:8px;display:grid;grid-template-columns:repeat(' + w.cols + ',1fr);grid-template-rows:repeat(3,1fr);"></div>'
            + '</div>'
        );
    });
    // Move each panel into its window, renumbering its column from 1.
    panels.forEach(function(p){
        var col = parseInt(p.style.gridColumnStart, 10);
        var row = parseInt(p.style.gridRowStart, 10);
        var w = _cfSegWins.filter(function(x){ return x.panels.indexOf(col) !== -1; })[0];
        if (!w) return;
        p.style.gridColumn = String(w.panels.indexOf(col) + 1);
        p.style.gridRow = String(row);
        $('#' + w.id + ' .cf-seg-body').append(p);
    });
    // The C2C legend (T / A days) sits in the Cash-to-Cash window header.
    var legend = document.getElementById('cf-c2c-legend');
    // The C2C figures now sit in the Cash-to-Cash dial, not in the window header.
    _cfSegWins.forEach(function(w){
        var $win = $('#' + w.id);
        $win.draggable({ handle: '.cf-seg-hdr', containment: 'window', scroll: false });
    });
}
// The Cashflow button opens the three windows, or closes them if they are open.
$(document).on('click', '.cashflow-dashboard-btn', function(e){
    e.preventDefault();
    if ($('#cf-win-value').is(':visible')) { cfHideSegmentWindows(); return; }
    cfBuildSegmentWindows();
    cfShowSegmentWindows();
    if (!_cfLoading) { renderCfBars(); }
});
// Touch dragging for the Cashflow windows (jQuery UI drag responds to the mouse only).
// Dragging the title bar moves that window; the page does not scroll while dragging.
$(document).on('touchstart', '.cf-seg-hdr', function(e){
    var oe = e.originalEvent;
    if (!oe || !oe.touches || oe.touches.length !== 1) return;
    var win = $(this).closest('.cf-seg-win');
    var t0 = oe.touches[0];
    var startX = t0.clientX, startY = t0.clientY;
    var startLeft = win[0].offsetLeft, startTop = win[0].offsetTop;
    function onMove(ev){
        if (!ev.touches || ev.touches.length !== 1) return;
        var t = ev.touches[0];
        var nl = startLeft + (t.clientX - startX);
        var nt = startTop + (t.clientY - startY);
        nl = Math.max(0, Math.min(window.innerWidth - win.outerWidth(), nl));
        nt = Math.max(0, Math.min(window.innerHeight - 40, nt));
        win.css({ left: nl + 'px', top: nt + 'px' });
        ev.preventDefault();
    }
    function onEnd(){
        document.removeEventListener('touchmove', onMove);
        document.removeEventListener('touchend', onEnd);
        document.removeEventListener('touchcancel', onEnd);
    }
    document.addEventListener('touchmove', onMove, { passive: false });
    document.addEventListener('touchend', onEnd);
    document.addEventListener('touchcancel', onEnd);
});
// The shared header's × closes all three windows.
$(document).on('click', '#cf-shared-close', function(){ cfHideSegmentWindows(); });
// Touch dragging of the shared header moves all three windows together.
$(document).on('touchstart', '#cf-shared-hdr', function(e){
    var oe = e.originalEvent;
    if (!oe || !oe.touches || oe.touches.length !== 1) return;
    var hdr = this;
    var lx = oe.touches[0].clientX, ly = oe.touches[0].clientY;
    function onMove(ev){
        if (!ev.touches || ev.touches.length !== 1) return;
        var t = ev.touches[0];
        var dx = t.clientX - lx, dy = t.clientY - ly;
        lx = t.clientX; ly = t.clientY;
        cfMoveSegmentWindows(dx, dy);
        hdr.style.left = (hdr.offsetLeft + dx) + 'px';
        hdr.style.top  = (hdr.offsetTop + dy) + 'px';
        ev.preventDefault();
    }
    function onEnd(){
        document.removeEventListener('touchmove', onMove);
        document.removeEventListener('touchend', onEnd);
        document.removeEventListener('touchcancel', onEnd);
    }
    document.addEventListener('touchmove', onMove, { passive: false });
    document.addEventListener('touchend', onEnd);
    document.addEventListener('touchcancel', onEnd);
});
// Each window closes only itself.
$(document).on('click', '.cf-seg-close', function(){
    $('#' + $(this).data('win')).css('display', 'none');
});
// Only the X button closes this — clicking elsewhere (or another dashboard
// button) must never dismiss an already-open dashboard.
$(document).on('click', '#cf-close', function(){
    $('#cf-modal, #cf-bk').removeClass('cf-open');
    delete document.getElementById('cf-modal').dataset.portraitFitted;
});
// Bring to front on click anywhere inside, same as the other floaters.
$(document).on('mousedown touchstart', '#cf-modal', function(){
    if (typeof window.popupBringToFront === 'function') window.popupBringToFront('cf-modal');
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

// Project Duration dial: the planned duration sits at the centre (yellow marker at
// the top), the needle shows the actual (forecast) duration. Actual above target is
// orange-red, at or below target blue-green, as on the other dials.
function _cfDurationDialSvg(target, actual, centerLabel, tipLabel){
    var maxV = target > 0 ? target * 2 : 100;
    var f = Math.max(0, Math.min(1, actual / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    var targetF = 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6"/>';
    var gapCol = '#d9480f';
    var targetPath = arc(0, targetF, '#2ba899');
    var gapPath = arc(targetF, 1, gapCol);
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var svg = '<svg width="153" height="82" viewBox="-10 -20 230 110" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arc(0, 1, '#3a4a5f') + targetPath + gapPath + targetLine
        + (tipLabel ? (function(){
            // Label just outside the needle tip, kept inside the drawing area
            // (viewBox x -10..220): near the ends it is pushed in and lifted
            // above the arc so a long label such as "-376 d" is not cut off.
            var c = Math.cos(na), anchor = c < -0.3 ? 'end' : c > 0.3 ? 'start' : 'middle';
            var lx = cx + (r + 18) * c, ly = cy - (r + 18) * Math.sin(na) + 4;
            var tw = String(tipLabel).length * 9.5;
            if (anchor === 'end' && lx - tw < -8) { lx = -8 + tw; ly = Math.min(ly, cy - r + 10); }
            if (anchor === 'start' && lx + tw > 218) { lx = 218 - tw; ly = Math.min(ly, cy - r + 10); }
            return '<text x="' + lx.toFixed(1) + '" y="' + ly.toFixed(1) + '" text-anchor="' + anchor + '" font-size="20" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + tipLabel + '</text>';
          })() : '')
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 22) + '" text-anchor="middle" font-size="22" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + centerLabel + '</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

// Project Duration as a horizontal bar: the full bar length is the project
// duration (target, yellow marker at its end). The actual fills it green-blue;
// beyond the target the scale grows to the actual and the excess is red-orange.
function _cfDurationBarHtml(target, actual, targetLabel, actualLabel, tipLabel, fmt){
    // fmt formats a value with its unit (days by default; the Project Cost bar passes Lakhs).
    fmt = fmt || function(v){ return v + ' d'; };
    var scale = Math.max(target, actual, 1);
    var tPct = target / scale * 100, aPct = actual / scale * 100;
    var over = actual > target;
    var tipCol = over ? '#d9480f' : '#2ba899';
    // Purple-black bar; only the difference between actual and target is
    // coloured: red-orange beyond the target, green-blue when short of it.
    var gapL = Math.min(aPct, tPct), gapW = Math.abs(aPct - tPct);
    var bar = '<div style="position:relative;width:100%;height:10px;background:#1a0a2e;border-radius:2px;">'
        + (gapW > 0 ? '<div style="position:absolute;left:' + gapL.toFixed(2) + '%;top:0;bottom:0;width:' + gapW.toFixed(2) + '%;background:' + tipCol + ';"></div>' : '')
        + '<div style="position:absolute;left:calc(' + tPct.toFixed(2) + '% - 2px);top:-4px;bottom:-4px;width:4px;background:#ffff00;border:1px solid #000;box-sizing:border-box;"></div>'
        + '</div>';
    return '<div style="width:100%;padding:0 10px;box-sizing:border-box;">'
        + '<div style="display:flex;justify-content:space-between;align-items:baseline;font-family:Barlow Condensed,Arial;line-height:22px;margin-bottom:4px;">'
        + '<span style="font-size:20px;font-weight:700;color:#000;">' + fmt(actual) + '</span>'
        + '<span style="font-size:20px;font-weight:700;color:' + tipCol + ';">' + tipLabel + '</span></div>'
        + bar
        + '<div style="display:flex;justify-content:space-between;font-size:11px;color:#5a5a5a;font-family:Barlow Condensed,Arial;margin-top:3px;"><span>0</span><span>' + fmt(scale) + '</span></div>'
        + '</div>'
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:4px 8px 0;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:#1a2540;font-weight:700;">' + actualLabel + '</span>'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>' + targetLabel + '</span>'
        + '</div>';
}

// Project Resources bar (values in lakhs): full length = estimated value of the
// project's allocated resources (yellow marker); lighter segment = purchased,
// darker segment = used, so the gap between them is the stock at site. Purchase
// beyond the estimate shows red-orange.
function _cfResourceBarHtml(est, purch, used, stock){
    var lk = function(v){ return (+v || 0).toFixed(2) + ' L'; };
    var scale = Math.max(est, purch, used, 0.0001);
    var pc = function(v){ return Math.max(0, Math.min(100, v / scale * 100)).toFixed(2); };
    var over = purch > est;
    var bar = '<div style="position:relative;width:100%;height:10px;background:#1a0a2e;border-radius:2px;">'
        + '<div title="Purchased: ' + lk(purch) + '" style="position:absolute;left:0;top:0;bottom:0;width:' + pc(Math.min(purch, est)) + '%;background:#8fd3c8;"></div>'
        + (over ? '<div style="position:absolute;left:' + pc(est) + '%;top:0;bottom:0;width:' + (pc(purch) - pc(est)).toFixed(2) + '%;background:#d9480f;"></div>' : '')
        + '<div title="Used: ' + lk(used) + '" style="position:absolute;left:0;top:0;bottom:0;width:' + pc(used) + '%;background:#2ba899;"></div>'
        + '<div style="position:absolute;left:calc(' + pc(est) + '% - 2px);top:-4px;bottom:-4px;width:4px;background:#ffff00;border:1px solid #000;box-sizing:border-box;"></div>'
        + '</div>';
    var sw = function(c){ return '<span style="display:inline-block;width:10px;height:6px;background:' + c + ';border-radius:1px;margin-right:3px;vertical-align:middle;"></span>'; };
    return '<div style="width:100%;padding:0 10px;box-sizing:border-box;font-family:Barlow Condensed,Arial;">'
        + '<div style="display:flex;justify-content:space-between;align-items:baseline;line-height:22px;margin-bottom:4px;">'
        + '<span style="font-size:20px;font-weight:700;color:#000;" title="Used">' + lk(used) + '</span>'
        + '<span style="font-size:13px;font-weight:700;color:#1a2540;" title="Stock at site = Purchased - Used">Stock ' + lk(stock) + '</span></div>'
        + bar
        + '<div style="display:flex;justify-content:space-between;font-size:11px;color:#5a5a5a;margin-top:3px;"><span>0</span><span>' + lk(scale) + '</span></div>'
        + '</div>'
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:4px 8px 0;width:100%;box-sizing:border-box;font-size:12px;color:#111;">'
        + '<span>' + sw('#8fd3c8') + 'Purch: ' + lk(purch) + '</span>'
        + '<span>' + sw('#2ba899') + 'Used: ' + lk(used) + '</span>'
        + '<span>' + sw('#ffff00') + 'Est: ' + lk(est) + '</span>'
        + '</div>';
}

// Dial plus its labels row, used by all three VALUE DRIVERS panels.
function _cfValueDialHtml(target, actual, centerLabel, targetLabel, actualLabel, tipLabel){
    return _cfDurationDialSvg(target, actual, centerLabel, tipLabel)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:#1a2540;font-weight:700;">' + actualLabel + '</span>'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>' + targetLabel + '</span>'
        + '</div>';
}

// VALUE DRIVERS (left segment of the Cashflow dashboard): Project Duration, Project
// Cost and Procurement. All three are dials.
function renderCfValueDrivers(){
    var gen = _cfLoadGen;
    var durEl  = document.getElementById('cf-value-duration');
    var costEl = document.getElementById('cf-value-cost');
    var procEl = document.getElementById('cf-value-procurement');
    if (procEl) {
        procEl.style.display = 'flex';
        procEl.style.flexDirection = 'column';
        procEl.style.justifyContent = 'center';
        procEl.style.flex = '1 1 auto';
        procEl.innerHTML = _cfResourceBarHtml(0, 0, 0, 0);
        _cfQueueAjax({
            type: 'POST', url: '../projectsmain/cfresourcesvalue', dataType: 'json',
            success: function(d){
                if (gen !== _cfLoadGen || !d || d.error !== 'No') return;
                procEl.innerHTML = _cfResourceBarHtml(+d.estimated / 100000, +d.purchased / 100000, +d.used / 100000, +d.stock / 100000);
            }
        });
    }
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfprojectduration', dataType: 'json',
        success: function(d){
            if (gen !== _cfLoadGen || !d || d.error !== 'No' || !durEl) return;
            $('#cf-shared-name').text(d.project_name || '');
            var t = +d.budgeted || 0, a = +d.actual || 0;
            durEl.style.display = 'flex';
            durEl.style.flexDirection = 'column';
            durEl.style.justifyContent = 'center';
            durEl.style.flex = '1 1 auto';   // fill the panel so the bar sits in its vertical centre
            durEl.innerHTML = _cfDurationBarHtml(t, a, 'Target: ' + t + ' d', 'Actual: ' + a + ' d', (a - t >= 0 ? '+' : '') + (a - t) + ' d');
        }
    });
    // Queued right after Project Duration (it takes about 0.1 s on the server;
    // what made it slow was waiting behind every other panel's requests).
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfprojectcost', dataType: 'json',
        success: function(d){
            if (gen !== _cfLoadGen || !d || d.error !== 'No' || !costEl) return;
            // Target = estimated cost; actual = sum of the activities' Actual Cost
            // in the Project Estimate window. Both in lakhs.
            var est = (+d.estimated || 0) / 100000;
            var act = (+d.actual || 0) / 100000;
            costEl.style.display = 'flex';
            costEl.style.flexDirection = 'column';
            costEl.style.justifyContent = 'center';
            costEl.style.flex = '1 1 auto';
            // Same bar as Project Duration: full length = estimate (Project Estimate
            // window); actual = Cost Dashboard actual. Over the estimate red-orange,
            // under it green-blue.
            var lk = function(v){ return (+v).toFixed(2) + ' L'; };
            costEl.innerHTML = _cfDurationBarHtml(est, act, 'Target: ' + lk(est), 'Actual: ' + lk(act) + (d.has_actual ? '' : ' (estimate)'), (act - est >= 0 ? '+' : '') + lk(act - est), lk);
        }
    });
}

// Project Cost panel popup: the activities in the current billing cycle and each
// one's cost in that cycle (same overlap formula as the WIP cycle bars, from
// cfwipcycles). The total equals the current cycle's bar in the WIP popup.
function _cfShowCostCyclePopup(d){
    $('#cf-costcycle-win').remove();
    var cyc = (d.cycles || [])[(d.current_cycle || 1) - 1] || {};
    var items = d.current_items || [];
    function n2(v){ return (+v || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function esc(s){ return $('<div>').text(s == null ? '' : String(s)).html(); }
    // Grouped by IOW group (items arrive in the project's group order), each with a subtotal.
    var total = 0, rows = '', grp = null, sub = 0, no = 0;
    function closeGroup(){
        if (grp === null) return;
        rows += '<tr><td colspan="6" style="text-align:right;font-weight:700;color:#081142;">Subtotal &mdash; ' + esc(grp) + '</td><td style="font-weight:700;color:#081142;">' + n2(sub) + '</td><td></td><td></td><td></td></tr>';
    }
    // Group sections kept for the Activities popup (group heading click).
    window._cfCostGroups = [];
    items.forEach(function(it){
        if (it.group !== grp) {
            closeGroup();
            grp = it.group; sub = 0;
            window._cfCostGroups.push({ iow_name: grp, items: [] });
            rows += '<tr class="cf-cc-grp" data-grp="' + (window._cfCostGroups.length - 1) + '" style="cursor:pointer;" title="Show the activities and their resource allocation"><td colspan="10" style="text-align:left;font-weight:700;background:#e8ecf3;color:#081142;">' + esc(grp) + '</td></tr>';
        }
        window._cfCostGroups[window._cfCostGroups.length - 1].items.push({
            wan_id: it.wan_id, sched_id: it.sched_id, lib_activity_id: it.lib_activity_id,
            activity_name: it.name, unit: it.unit
        });
        no++;
        sub += +it.cost || 0;
        total += +it.cost || 0;
        rows += '<tr class="cf-cc-row" data-schedid="' + (+it.sched_id || 0) + '" style="cursor:pointer;" title="Open the Cost Dashboard"><td>' + no + '</td><td style="text-align:left;">' + esc(it.name) + '</td><td>' + esc(it.unit) + '</td>'
            + '<td>' + n2(it.quantity) + '</td><td>' + n2(it.remaining) + '</td><td>' + n2(it.rate) + '</td>'
            + '<td style="font-weight:700;">' + n2(it.cost) + '</td><td>-</td><td>-</td><td>-</td></tr>';
    });
    closeGroup();
    if (!items.length) rows = '<tr><td colspan="10" style="text-align:center;color:#666;padding:12px;">' + ((d.cycles || []).length ? 'No activities in the current billing cycle.' : 'Set Project Duration and Billing Cycle to see the cycle.') + '</td></tr>';
    var th = 'style="background:#4a4f57;color:#fff;padding:6px 8px;font-weight:700;white-space:nowrap;"';
    var html = '<div id="cf-costcycle-win" style="position:fixed;z-index:100300;left:50%;top:14vh;transform:translateX(-50%);width:990px;max-width:96vw;max-height:72vh;display:flex;flex-direction:column;background:#fff;border-radius:6px;box-shadow:0 8px 32px rgba(0,0,0,.45);font-family:Nunito,Arial,sans-serif;font-size:12px;color:#111;">'
        + '<div id="cf-costcycle-hdr" style="flex:none;display:flex;justify-content:space-between;align-items:center;background:#081142;color:#fff;padding:8px 12px;border-radius:6px 6px 0 0;cursor:move;user-select:none;touch-action:none;">'
        // Same label and value as this cycle's bar in the WIP popup (month, lakhs).
        + '<span style="font-weight:700;">PROJECT COST &middot; Cycle ' + (d.current_cycle || '') + (cyc.end_label ? ' (' + esc(cyc.end_label) + ')' : '')
        + ' &middot; ' + ((+cyc.target || 0) / 100000).toFixed(2) + ' Lakhs' + (cyc.end_date ? ' &middot; ends ' + esc(cyc.end_date) : '') + '</span>'
        + '<button type="button" id="cf-costcycle-close" style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;line-height:1;">&times;</button></div>'
        + '<div style="flex:1;min-height:0;overflow:auto;padding:8px 10px;">'
        + '<table style="width:100%;border-collapse:collapse;text-align:right;">'
        + '<thead><tr><th ' + th + '>#</th><th style="background:#4a4f57;color:#fff;padding:6px 8px;font-weight:700;text-align:left;">Activity</th><th ' + th + '>Unit</th><th ' + th + ' title="Estimated quantity">E. Quantity</th><th ' + th + ' title="Quantity in this cycle minus quantity already executed (progress report)">R. Cycle Qnty</th><th ' + th + '>Rate</th><th ' + th + '>Cost in cycle</th><th ' + th + ' title="Quoted Rate">Qtd Rate</th><th ' + th + '>Revenue/Cycle</th><th ' + th + '>Margin</th></tr></thead>'
        + '<tbody>' + rows + '</tbody>'
        + '<tfoot><tr><td colspan="6" style="padding:6px 8px;font-weight:700;border-top:2px solid #4a4f57;">Total</td><td style="padding:6px 8px;font-weight:700;border-top:2px solid #4a4f57;">' + n2(total) + '</td><td style="border-top:2px solid #4a4f57;"></td><td style="border-top:2px solid #4a4f57;"></td><td style="border-top:2px solid #4a4f57;"></td></tr></tfoot>'
        + '</table></div>'
        + '<div style="flex:none;padding:4px 12px 8px;color:#5a6e8c;font-size:11px;">Cost in cycle = Quantity &times; Rate &times; (activity days in this cycle &divide; activity duration). R. Cycle Qnty = quantity in this cycle (to the cycle end) &minus; quantity already executed (progress report). Unfinished activities carry forward to the next cycle.</div>'
        + '</div>';
    $('body').append(html);
    $('#cf-costcycle-win td').css({ padding: '5px 8px', borderBottom: '1px solid #b8c2d3', borderRight: '1px solid #b8c2d3' });
    $('#cf-costcycle-win th').css({ borderRight: '1px solid #6b7280' });
    $('#cf-costcycle-win tr > :last-child').css({ borderRight: 'none' });
    // Drag by the header (mouse and touch).
    var win = document.getElementById('cf-costcycle-win'), drag = null;
    function start(x, y){ var r = win.getBoundingClientRect(); win.style.transform = 'none'; win.style.left = r.left + 'px'; win.style.top = r.top + 'px'; drag = { sx: x, sy: y, ox: r.left, oy: r.top }; }
    function move(x, y){ if (!drag) return; win.style.left = Math.max(0, drag.ox + x - drag.sx) + 'px'; win.style.top = Math.max(0, drag.oy + y - drag.sy) + 'px'; }
    $('#cf-costcycle-hdr').on('mousedown', function(e){ if ($(e.target).closest('button').length) return; start(e.clientX, e.clientY); e.preventDefault(); });
    $(document).on('mousemove.cfcost', function(e){ move(e.clientX, e.clientY); }).on('mouseup.cfcost', function(){ drag = null; });
    document.getElementById('cf-costcycle-hdr').addEventListener('touchstart', function(e){ if (e.touches.length !== 1 || $(e.target).closest('button').length) return; start(e.touches[0].clientX, e.touches[0].clientY); }, { passive: true });
    document.addEventListener('touchmove', function(e){ if (drag && e.touches.length === 1) { move(e.touches[0].clientX, e.touches[0].clientY); e.preventDefault(); } }, { passive: false });
    document.addEventListener('touchend', function(){ drag = null; });
}
// Clicking a group heading opens the same Activities popup as the Project
// Estimate window, with its Resource Allocation buttons.
$(document).on('click', '.cf-cc-grp', function(){
    var sec = (window._cfCostGroups || [])[+$(this).data('grp')];
    if (sec && typeof window.openEriowPopup === 'function') window.openEriowPopup(sec);
});
// Clicking an activity opens the same Cost Dashboard as a row click in the
// Project Estimate window (loads the Gantt functions silently if needed).
$(document).on('click', '.cf-cc-row', function(){
    var schedId = +$(this).data('schedid');
    if (!schedId) return;
    var anchorEl = this;
    function open(){
        if (typeof window.showCostDashboardPopup !== 'function') return;
        window.showCostDashboardPopup(schedId, anchorEl, false);
        // Keep it above the Cashflow windows and this popup.
        setTimeout(function(){ $('#gcm-popup').css('z-index', 100500); }, 0);
    }
    if (typeof window.showCostDashboardPopup === 'function') open();
    else if (typeof window.ensureGanttLoaded === 'function') window.ensureGanttLoaded(open);
});
// The Cost Dashboard re-raises itself through the app's stacking order when it is
// clicked or dragged, which is below the Cashflow windows. While a Cashflow
// window is open, put it back on top after each press.
$(document).on('mousedown touchstart', '#gcm-popup', function(){
    if (!$('#cf-win-value').is(':visible') && !$('#cf-costcycle-win').length) return;
    setTimeout(function(){ $('#gcm-popup').css('z-index', 100500); }, 0);
});
$(document).on('click', '#cf-costcycle-close', function(){ $('#cf-costcycle-win').remove(); $(document).off('.cfcost'); });
$(document).on('click', '#cf-value-cost', function(){
    // Uses the cycle data already loaded for the WIP dial, so it opens at once;
    // fetches only if the dashboard has not loaded it yet.
    if (window._cfWipCyclesData) { _cfShowCostCyclePopup(window._cfWipCyclesData); return; }
    $.ajax({ type: 'POST', url: '../projectsmain/cfwipcycles', dataType: 'json',
        success: function(d){ if (d && d.error === 'No') { window._cfWipCyclesData = d; _cfShowCostCyclePopup(d); } } });
});

// Project Resources panel popup: the resources of the current cycle's activities
// (same activity list as the Project Cost popup), one row per resource, grouped
// by resource type with subtotals (from cfcycleresources).
function _cfShowResourcesPopup(d){
    $('#cf-rescycle-win').remove();
    var rows = '', grp = null, sub = 0, total = 0, no = 0, res = d.resources || [], cyc = d.cycle || {};
    function n2(v){ return (+v || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function opt(v){ return (v === null || v === undefined || v === '') ? '-' : String(v); }
    function esc(s){ return $('<div>').text(s == null ? '' : String(s)).html(); }
    var NC = 13;
    function closeGroup(){
        if (grp === null) return;
        rows += '<tr><td colspan="6" style="text-align:right;font-weight:700;color:#081142;">Subtotal &mdash; ' + esc(grp) + '</td><td style="font-weight:700;color:#081142;">' + n2(sub) + '</td><td colspan="' + (NC - 7) + '"></td></tr>';
    }
    res.forEach(function(r){
        if (r.type !== grp) {
            closeGroup();
            grp = r.type; sub = 0;
            rows += '<tr><td colspan="' + NC + '" style="text-align:left;font-weight:700;background:#e8ecf3;color:#081142;">' + esc(grp) + '</td></tr>';
        }
        no++; sub += +r.cost || 0; total += +r.cost || 0;
        window._cfResRows = window._cfResRows || [];
        if (no === 1) window._cfResRows = [];
        window._cfResRows.push(r);
        rows += '<tr><td>' + no + '</td><td style="text-align:left;"><div style="display:flex;align-items:center;justify-content:space-between;gap:8px;"><span>' + esc(r.name) + '</span>'
            + '<button type="button" class="cf-res-order" data-i="' + (no - 1) + '" style="flex:none;background:#3d6a8f;color:#fff;border:none;border-radius:12px;padding:2px 12px;font-size:11px;font-weight:700;cursor:pointer;">Order</button></div></td><td>' + esc(r.unit) + '</td>'
            + '<td>' + n2(r.est_qty) + '</td><td>' + n2(r.cycle_qty) + '</td><td>' + n2(r.rate) + '</td>'
            + '<td style="font-weight:700;">' + n2(r.cost) + '</td>'
            + '<td>' + (r.lot_size === null ? '-' : n2(r.lot_size)) + '</td><td>' + esc(opt(r.lead_time)) + '</td><td>' + esc(opt(r.credit_period)) + '</td>'
            + '<td>' + n2(r.purchased) + '</td><td>' + n2(r.used) + '</td><td>' + n2(r.remaining) + '</td></tr>';
    });
    closeGroup();
    if (!res.length) rows = '<tr><td colspan="' + NC + '" style="text-align:center;color:#666;padding:12px;">No resources allocated to the activities of this cycle.</td></tr>';
    var th = 'style="background:#4a4f57;color:#fff;padding:6px 8px;font-weight:700;white-space:nowrap;"';
    function h(t, tip){ return '<th ' + th + (tip ? ' title="' + tip + '"' : '') + '>' + t + '</th>'; }
    var html = '<div id="cf-rescycle-win" style="position:fixed;z-index:100300;left:50%;top:12vh;transform:translateX(-50%);width:1180px;max-width:96vw;max-height:76vh;display:flex;flex-direction:column;background:#fff;border-radius:6px;box-shadow:0 8px 32px rgba(0,0,0,.45);font-family:Nunito,Arial,sans-serif;font-size:12px;color:#111;">'
        + '<div id="cf-rescycle-hdr" style="flex:none;display:flex;justify-content:space-between;align-items:center;background:#081142;color:#fff;padding:8px 12px;border-radius:6px 6px 0 0;cursor:move;user-select:none;touch-action:none;">'
        + '<span style="font-weight:700;">PROJECT RESOURCES &middot; Cycle ' + (d.current_cycle || '') + (cyc.end_label ? ' (' + esc(cyc.end_label) + ')' : '')
        + ' &middot; ' + (total / 100000).toFixed(2) + ' Lakhs' + (cyc.end_date ? ' &middot; ends ' + esc(cyc.end_date) : '') + '</span>'
        + '<button type="button" id="cf-rescycle-close" style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;line-height:1;">&times;</button></div>'
        + '<div style="flex:1;min-height:0;overflow:auto;padding:8px 10px;">'
        + '<table style="width:100%;border-collapse:collapse;text-align:right;">'
        + '<thead><tr>' + h('#') + '<th style="background:#4a4f57;color:#fff;padding:6px 8px;font-weight:700;text-align:left;">Resource</th>' + h('Unit')
        + h('E. Quantity', 'Estimated quantity for the activities: consumption per unit x activity quantity')
        + h('Cycle Qnty', 'Quantity required in this cycle: E. Quantity x activity days in the cycle / activity duration')
        + h('Rate') + h('Cost in cycle', 'Cycle Qnty x Rate')
        + h('Lot Size') + h('Lead Time') + h('Credit Period')
        + h('Purchased', 'GRN quantity received for the project') + h('Used', 'Average consumption x quantity executed') + h('Remaining', 'Purchased - Used')
        + '</tr></thead>'
        + '<tbody>' + rows + '</tbody>'
        + '<tfoot><tr><td colspan="6" style="padding:6px 8px;font-weight:700;border-top:2px solid #4a4f57;">Total</td><td style="padding:6px 8px;font-weight:700;border-top:2px solid #4a4f57;">' + n2(total) + '</td><td colspan="' + (NC - 7) + '" style="border-top:2px solid #4a4f57;"></td></tr></tfoot>'
        + '</table></div>'
        + '<div style="flex:none;padding:4px 12px 8px;color:#5a6e8c;font-size:11px;">Cost in cycle = Cycle Qnty &times; Rate. Its total equals the Project Cost popup total when the activity rates match their resource allocation. Remaining = Purchased &minus; Used.</div>'
        + '</div>';
    $('body').append(html);
    $('#cf-rescycle-win td').css({ padding: '5px 8px', borderBottom: '1px solid #b8c2d3', borderRight: '1px solid #b8c2d3', whiteSpace: 'nowrap' });
    $('#cf-rescycle-win th').css({ borderRight: '1px solid #6b7280' });
    $('#cf-rescycle-win tr > :last-child').css({ borderRight: 'none' });
    var win = document.getElementById('cf-rescycle-win'), drag = null;
    function start(x, y){ var r = win.getBoundingClientRect(); win.style.transform = 'none'; win.style.left = r.left + 'px'; win.style.top = r.top + 'px'; drag = { sx: x, sy: y, ox: r.left, oy: r.top }; }
    function move(x, y){ if (!drag) return; win.style.left = Math.max(0, drag.ox + x - drag.sx) + 'px'; win.style.top = Math.max(0, drag.oy + y - drag.sy) + 'px'; }
    $('#cf-rescycle-hdr').on('mousedown', function(e){ if ($(e.target).closest('button').length) return; start(e.clientX, e.clientY); e.preventDefault(); });
    $(document).on('mousemove.cfres', function(e){ move(e.clientX, e.clientY); }).on('mouseup.cfres', function(){ drag = null; });
    document.getElementById('cf-rescycle-hdr').addEventListener('touchstart', function(e){ if (e.touches.length !== 1 || $(e.target).closest('button').length) return; start(e.touches[0].clientX, e.touches[0].clientY); }, { passive: true });
    document.addEventListener('touchmove', function(e){ if (drag && e.touches.length === 1) { move(e.touches[0].clientX, e.touches[0].clientY); e.preventDefault(); } }, { passive: false });
    document.addEventListener('touchend', function(){ drag = null; });
}
// Order button in the Project Resources popup. Materials, Consumables and
// Purchased Inputs: the Raise Purchase Orders popup (same fields and endpoint as
// Order Management > Purchase Orders), one PO for that resource — quantity = this
// cycle's quantity, rate = estimate rate, until the popup gets its own fields.
// Other resource types: a placeholder popup for now.
var CF_PO_TYPES = ['Materials', 'Consumables', 'Purchased Inputs'];
function _cfOrderPopupShell(id, title, body, foot){
    $('#' + id).remove();
    $('body').append('<div id="' + id + '" style="position:fixed;z-index:100700;width:460px;max-width:94vw;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border-radius:7px;box-shadow:0 6px 28px rgba(0,0,0,.35);overflow:hidden;font-family:Nunito,Arial,sans-serif;">'
        + '<div class="cf-ord-hdr" style="display:flex;justify-content:space-between;align-items:center;background:#072c47;color:#fff;padding:12px 18px;font-size:14px;font-weight:700;cursor:move;user-select:none;touch-action:none;"><span>' + title + '</span><span class="cf-ord-close" style="cursor:pointer;font-size:18px;opacity:.85;">&times;</span></div>'
        + '<div style="padding:16px 18px;max-height:70vh;overflow:auto;">' + body + '</div>'
        + (foot ? '<div style="padding:12px 18px;border-top:1px solid #e6eaf0;text-align:right;">' + foot + '</div>' : '')
        + '</div>');
    var win = document.getElementById(id), drag = null, hdr = win.querySelector('.cf-ord-hdr');
    function start(x, y){ var r = win.getBoundingClientRect(); win.style.transform = 'none'; win.style.left = r.left + 'px'; win.style.top = r.top + 'px'; drag = { sx: x, sy: y, ox: r.left, oy: r.top }; }
    hdr.addEventListener('mousedown', function(e){ if ($(e.target).closest('.cf-ord-close').length) return; start(e.clientX, e.clientY); e.preventDefault(); });
    hdr.addEventListener('touchstart', function(e){ if (e.touches.length === 1 && !$(e.target).closest('.cf-ord-close').length) start(e.touches[0].clientX, e.touches[0].clientY); }, { passive: true });
    $(document).off('.cford').on('mousemove.cford', function(e){ if (drag) { win.style.left = Math.max(0, drag.ox + e.clientX - drag.sx) + 'px'; win.style.top = Math.max(0, drag.oy + e.clientY - drag.sy) + 'px'; } })
        .on('mouseup.cford touchend.cford', function(){ drag = null; })
        .on('touchmove.cford', function(e){ var t = e.originalEvent.touches; if (drag && t && t.length === 1) { win.style.left = Math.max(0, drag.ox + t[0].clientX - drag.sx) + 'px'; win.style.top = Math.max(0, drag.oy + t[0].clientY - drag.sy) + 'px'; } });
    $(win).on('click', '.cf-ord-close, .cf-ord-cancel', function(){ $(win).remove(); $(document).off('.cford'); });
    return win;
}
function _cfTermRow(v){
    return '<div class="cf-po-term-row" style="display:flex;align-items:center;gap:6px;margin-bottom:6px;"><input type="text" class="cf-po-term" value="' + $('<div>').text(v || '').html() + '" placeholder="Enter term..." style="flex:1;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">'
        + '<button type="button" class="cf-po-term-del" style="background:#c0392b;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:14px;line-height:1;cursor:pointer;">&times;</button></div>';
}
function _cfShowRaisePo(r){
    var lbl = 'style="display:block;font-size:12px;font-weight:700;color:#465365;margin:0 0 5px;"', inp = 'width:100%;box-sizing:border-box;height:36px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 10px;font-size:13px;color:#333;';
    var body = '<div style="font-size:12px;color:#5a6e8c;margin-bottom:12px;">' + $('<div>').text(r.name).html() + ' &middot; ' + (+r.cycle_qty || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 }) + ' ' + $('<div>').text(r.unit || '').html() + ' @ ' + (+r.rate || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 }) + '</div>'
        + '<div style="margin-bottom:14px;"><label ' + lbl + '>Vendor</label><select id="cf-po-vendor" style="' + inp + '"><option value="">Loading...</option></select></div>'
        + '<div style="margin-bottom:14px;"><label ' + lbl + '>Ship To</label><textarea id="cf-po-shipto" rows="3" style="' + inp + 'height:auto;padding:8px 10px;resize:vertical;"></textarea></div>'
        + '<div style="margin-bottom:14px;"><label ' + lbl + '>Credit Period</label><input type="number" id="cf-po-credit" min="0" step="1" placeholder="e.g. 30 days" style="' + inp + '"></div>'
        + '<div style="margin-bottom:14px;"><label ' + lbl + '>Lead Time</label><input type="text" id="cf-po-lead" placeholder="e.g. 7 days" style="' + inp + '"></div>'
        + '<div><label ' + lbl + '>Terms</label><div id="cf-po-terms">' + _cfTermRow('') + '</div>'
        + '<button type="button" id="cf-po-term-add" style="margin-top:4px;background:none;border:1px dashed #c5ccd4;border-radius:4px;padding:3px 12px;font-size:11px;color:#465365;cursor:pointer;">+ Add Term</button></div>';
    var foot = '<button type="button" class="cf-ord-cancel" style="background:#f0f4f8;border:1px solid #c5ccd4;border-radius:20px;padding:6px 20px;font-size:13px;color:#465365;cursor:pointer;margin-right:8px;">Cancel</button>'
        + '<button type="button" id="cf-po-save" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:6px 24px;font-size:13px;cursor:pointer;">Raise PO</button>';
    var win = _cfOrderPopupShell('cf-po-popup', 'Raise Purchase Orders', body, foot);
    $('#cf-po-shipto').val($('#cf-shared-name').text() || '');
    $.when(
        $.ajax({ type: 'POST', url: '../procurement/getvendors', dataType: 'json', data: { context: 'po' } }),
        $.ajax({ type: 'POST', url: '../procurement/getposettings', dataType: 'json', data: { resource_id: r.resource_id } })
    ).done(function(vr, sr){
        var v = vr[0] || {}, s = sr[0] || {};
        var opts = '<option value="">&mdash; Select Vendor &mdash;</option>';
        (v.vendors || []).forEach(function(x){ opts += '<option value="' + x.Vendor_Id + '">' + $('<div>').text(x.Name).html() + '</option>'; });
        $('#cf-po-vendor').html(opts);
        if (s.error === 'No' && s.data) {
            var d = s.data;
            if (d.vendor_id) $('#cf-po-vendor').val(d.vendor_id);
            if (d.credit_period) $('#cf-po-credit').val(d.credit_period);
            if (d.lead_time) $('#cf-po-lead').val(d.lead_time);
            if (d.ship_to && d.ship_to.trim()) $('#cf-po-shipto').val(d.ship_to.trim());
            try { var t = JSON.parse(d.terms || '[]'); if (t && t.length) $('#cf-po-terms').html(t.map(_cfTermRow).join('')); } catch (e) {}
        }
    });
    $(win).on('click', '#cf-po-term-add', function(){ $('#cf-po-terms').append(_cfTermRow('')); })
        .on('click', '.cf-po-term-del', function(){ if ($('.cf-po-term-row').length > 1) $(this).closest('.cf-po-term-row').remove(); else $(this).siblings('input').val(''); })
        .on('click', '#cf-po-save', function(){
            if (!String(r.task_ids || '').trim()) { alert('Please map ' + r.name + ' to a task in the Resource Allocation page before raising a Purchase Order.'); return; }
            if (!$('#cf-po-vendor').val()) { alert('Please select a vendor.'); return; }
            if (!(+r.cycle_qty > 0) || !(+r.rate > 0)) { alert('Quantity and rate are needed to raise the PO.'); return; }
            var terms = []; $('.cf-po-term').each(function(){ var x = $(this).val().trim(); if (x) terms.push(x); });
            var $b = $(this).prop('disabled', true).text('Processing...');
            $.ajax({ type: 'POST', url: '../procurement/raisepobulk', dataType: 'json',
                data: {
                    vendor_id: $('#cf-po-vendor').val(), ship_to: $('#cf-po-shipto').val().trim(),
                    credit_period: $('#cf-po-credit').val().trim(), lead_time: $('#cf-po-lead').val().trim(),
                    terms: JSON.stringify(terms),
                    resources: JSON.stringify([{ resource_id: r.resource_id, allocation_id: r.allocation_id || '', activity_id: r.activity_id || '',
                        resource_name: r.name, unit: r.unit || '', qty: Math.round((+r.cycle_qty || 0) * 1000) / 1000, rate: +r.rate || 0 }])
                },
                success: function(d){
                    $b.prop('disabled', false).text('Raise PO');
                    if (d && d.error === 'No') { $(win).remove(); $(document).off('.cford'); alert('Purchase Order raised for ' + r.name + '.'); }
                    else alert((d && (d.errortext || d.message)) || 'Could not raise the Purchase Order.');
                },
                error: function(){ $b.prop('disabled', false).text('Raise PO'); alert('Could not raise the Purchase Order.'); }
            });
        });
}
$(document).on('click', '.cf-res-order', function(e){
    e.stopPropagation();
    var r = (window._cfResRows || [])[+$(this).data('i')];
    if (!r) return;
    if (CF_PO_TYPES.indexOf(r.type) !== -1) { _cfShowRaisePo(r); return; }
    _cfOrderPopupShell('cf-po-popup', 'Order &mdash; ' + $('<div>').text(r.type).html(),
        '<div style="font-size:13px;color:#333;">' + $('<div>').text(r.name).html() + '</div><div style="font-size:12px;color:#8a97ad;margin-top:8px;">The order screen for ' + $('<div>').text(r.type).html() + ' is still to be defined.</div>',
        '<button type="button" class="cf-ord-cancel" style="background:#f0f4f8;border:1px solid #c5ccd4;border-radius:20px;padding:6px 20px;font-size:13px;color:#465365;cursor:pointer;">Close</button>');
});
$(document).on('click', '#cf-rescycle-close', function(){ $('#cf-rescycle-win').remove(); $(document).off('.cfres'); });
$(document).on('click', '#cf-value-procurement', function(){
    $.ajax({ type: 'POST', url: '../projectsmain/cfcycleresources', dataType: 'json',
        success: function(d){ if (d && d.error === 'No') _cfShowResourcesPopup(d); } });
});

// Clicking the Project Duration panel opens the Gantt window exactly as the
// Gantt Chart icon on the top toolbar does (same element, same click handler).
$(function(){
    $(document).on('click', '#cf-value-duration', function(){
        var btn = document.getElementById('gantt-win-open');
        if (typeof window.openGanttWin === 'function') window.openGanttWin('current');
        else if (btn) btn.click();
    });
});

// Cash-to-Cash Cycle dial (third stack, top): target = the c2c_cycle target
// (yellow band at the centre), actual = average cycle days (see actionCfactualc2c).
function renderCfC2cDial(){
    var gen = _cfLoadGen;
    var el = document.getElementById('cf-c2cdial');
    if (!el) return;
    var t = 0, a = 0;
    function draw(){
        el.style.display = 'flex';
        el.style.flexDirection = 'column';
        el.style.justifyContent = 'center';
        el.innerHTML = _cfValueDialHtml(t, a, a.toFixed(1) + ' d', 'Target: ' + t.toFixed(1) + ' d', 'Actual: ' + a.toFixed(1) + ' d');
    }
    draw();
    _cfFetchTarget('c2c_cycle', function(d){
        if (gen === _cfLoadGen) { t = +d.target || 0; draw(); }
    });
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfactualc2c', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') { a = +d.total || 0; draw(); }
        }
    });
}

// Current Assets, Working Capital and Capital efficiency: formulas to follow, so they show a
// placeholder dial until then (same treatment as Project Resources).
function renderCfPlaceholderDials(){
    // Current Assets: actual from cfcurrentassetstotal (no target yet).
    var caEl = document.getElementById('cf-curassets');
    if (caEl) {
        var caGen = _cfLoadGen;
        _cfQueueAjax({
            type: 'POST', url: '../projectsmain/cfcurrentassetstotal', dataType: 'json',
            success: function(d){
                if (caGen !== _cfLoadGen || !d || d.error !== 'No') return;
                var v = (+d.total || 0) / 100000;
                caEl.style.display = 'flex';
                caEl.style.flexDirection = 'column';
                caEl.style.justifyContent = 'center';
                // Shown as a large figure in lakhs instead of a dial.
                caEl.style.alignItems = 'center'; caEl.style.flex = '1'; caEl.style.height = '100%'; caEl.style.width = '100%'; caEl.style.cursor = 'pointer'; caEl.title = 'Click to see account breakdown';
                caEl.innerHTML = '<div style="text-align:center;font-family:Barlow Condensed,Arial;line-height:1;">'
                    + '<div style="font-size:42px;font-weight:700;color:#2e7d32;">' + v.toFixed(2) + '</div>'
                    + '<div style="font-size:14px;font-weight:600;color:#5a6e8c;margin-top:6px;letter-spacing:.04em;">LAKHS</div></div>';
            }
        });
    }
    // Working Capital = Current Assets - Current Liabilities, as a large figure
    // (green when positive, red when negative).
    var wcEl = document.getElementById('cf-workcap');
    if (wcEl) {
        var wcGen = _cfLoadGen, wcCA = null, wcCL = null;
        var wcDraw = function(){
            if (wcCA === null || wcCL === null) return;
            var v = (wcCA - wcCL) / 100000;
            wcEl.style.display = 'flex'; wcEl.style.flexDirection = 'column'; wcEl.style.justifyContent = 'center';
            wcEl.style.alignItems = 'center'; wcEl.style.flex = '1'; wcEl.style.height = '100%'; wcEl.style.width = '100%';
            wcEl.innerHTML = '<div style="text-align:center;font-family:Barlow Condensed,Arial;line-height:1;">'
                + '<div style="font-size:42px;font-weight:700;color:' + (v < 0 ? '#c62828' : '#2e7d32') + ';">' + v.toFixed(2) + '</div>'
                + '<div style="font-size:14px;font-weight:600;color:#5a6e8c;margin-top:6px;letter-spacing:.04em;">LAKHS</div></div>';
        };
        _cfQueueAjax({ type: 'POST', url: '../projectsmain/cfcurrentassetstotal', dataType: 'json',
            success: function(d){ if (wcGen === _cfLoadGen && d && d.error === 'No') { wcCA = +d.total || 0; wcDraw(); } } });
        _cfQueueAjax({ type: 'POST', url: '../projectsmain/cfcurrentliabilities', dataType: 'json',
            success: function(d){ if (wcGen === _cfLoadGen && d && d.error === 'No') { wcCL = +d.total || 0; wcDraw(); } } });
    }
    ['cf-wceff'].forEach(function(id){
        var el = document.getElementById(id);
        if (!el) return;
        el.style.display = 'flex';
        el.style.flexDirection = 'column';
        el.style.justifyContent = 'center';
        el.innerHTML = _cfValueDialHtml(0, 0, '-', 'Target: -', 'Actual: -');
    });
}

function renderCfBars(){
    _cfLoadGen++;
    _cfLoading = true;
    // _cfFetchTarget retries once after an 800ms delay on failure, so a
    // full load cycle can legitimately take a bit over 800ms; 2.5s gives
    // every panel's fetches (including a retry) comfortable room to
    // settle before the next click is allowed to start a new load.
    setTimeout(function(){ _cfLoading = false; }, 2500);
    // Value Drivers first, so Project Duration is not stuck behind every other request.
    renderCfValueDrivers();
    _cfDials.forEach(function(d){ renderCfDial(d.elId, d.metricKey, d.unit, d.unitFontSize, d.reverseColors); });
    renderCfBankDial();
    renderCfWipDial();
    renderCfBillsReceivableDial();
    renderCfCurrentAssetsDial();
    renderCfCurrentLiabilitiesDial();
    renderCfCurrLiabDial2();
    renderCfC2cLegend();
    renderCfC2cDial();
    renderCfPlaceholderDials();
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

function _cfInvestDialSvg(target, actual, aboveGood){
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
    // Base dial arc: uniform dark blue-grey
    var arcPath = arc(0, 1, '#3a4a5f');
    var targetF = 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    // Gap arc: colored based on performance. For Investment (spending):
    // - Orange-red if actual > target (overspent - bad)
    // - Blue-green if actual < target (under budget - good)
    // Total Investment (aboveGood): above the target green-blue, below it red-orange.
    var gapCol = aboveGood ? (actual >= targetCr ? '#2ba899' : '#d84a30') : (actual > targetCr ? '#d84a30' : '#2ba899');
    var gapPath = (targetCr > 0 && (actual > 0 || aboveGood)) ? (f < targetF ? arc(f, targetF, gapCol) : arc(targetF, f, gapCol)) : '';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    // Target is typed directly in Lakhs (the input box's fixed
    // convention -- see _cfEnteredToRupees) and Actual is shown in
    // that SAME fixed Lakhs unit, never auto-converted to Crores by
    // magnitude, so the two numbers always stay comparable and the
    // unit the user chose by using this box is never second-guessed.
    var fmt = { unit: 'Lakhs' };
    var divisor = 100;
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + gapPath
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
    // Target is typed directly in Lakhs (the input box's fixed
    // convention -- see _cfEnteredToRupees) and Actual is shown in
    // that SAME fixed Lakhs unit, never auto-converted to Crores by
    // magnitude, so the two numbers always stay comparable and the
    // unit the user chose by using this box is never second-guessed.
    var fmt = { unit: 'Lakhs' };
    var divisor = 100;
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
    // Target is typed directly in Lakhs (the input box's fixed
    // convention -- see _cfEnteredToRupees) and Actual is shown in
    // that SAME fixed Lakhs unit, never auto-converted to Crores by
    // magnitude, so the two numbers always stay comparable and the
    // unit the user chose by using this box is never second-guessed.
    var fmt = { unit: 'Lakhs' };
    var divisor = 100;
    var maxV = targetCr > 0 ? targetCr * 2 : 1;
    // Current cycle: needle = actual, yellow band = target; above target green-blue, below red-orange.
    var needleCol = s.actual >= targetCr ? '#2ba899' : '#d84a30';
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfInvestDialSvg(s.target, s.actual, true)
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
    // Target (Lakhs) = Advances and Deposits target + current cycle's WIP target.
    var advT = null, wipT = null;
    function setTarget(){
        if (gen !== _cfLoadGen || advT === null || wipT === null) return;
        _cfNetInvState.target = advT + wipT / 100000;
        _cfDrawNetInvDial();
    }
    _cfFetchTarget('investments_target', function(d){ advT = +d.target || 0; setTarget(); });
    if (window._cfWipCyclesData) { wipT = +window._cfWipCyclesData.current_target || 0; setTarget(); }
    else _cfQueueAjax({ type: 'POST', url: '../projectsmain/cfwipcycles', dataType: 'json',
        success: function(d){ wipT = (d && d.error === 'No') ? (+d.current_target || 0) : 0; setTarget(); },
        error: function(){ wipT = 0; setTarget(); } });
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
        + '<span style="float:right;font-weight:700;color:#251840;">Current: ' + _cfIndianComma(s.average / (r.unit === 'Crores' ? 10000000 : 100000)) + ' ' + r.unit + '</span>'
    );
    $('#cf-netinv-breakdown-chart').html(r.body);
}

// Total Investment popup — same window and chart design as the Work In Progress
// popup (_cfShowWipBreakdown / _cfWipCycleBarsHtml). Per billing cycle, two
// attached target bars: Advances and Deposits target and the cycle's WIP target.
// The actual Total Investment (Advances + WIP at the cycle end, today for the
// current cycle) is marked across the pair: green-blue at or above the
// combined target, red-orange below it. Cycles still to come show targets only.
function _cfNetInvCycleBarsHtml(cyc){
    if (!cyc.length) return '<div style="padding:8px 10px;font-size:12px;color:#5a6e8c;">Set Project Duration and Billing Cycle to see the cycle targets.</div>';
    // One bar per billing cycle, the current one and those still to come: the
    // target Total Investment (WIP target + Advances and Deposits target).
    var ci = -1;
    cyc.forEach(function(c, i){ if (c.current && ci < 0) ci = i; });
    if (ci > 0) cyc = cyc.slice(ci);
    // The actual Total Investment (Advances + WIP, today for the current cycle) is
    // overlaid as a narrower bar: green-blue at or above the target, red-orange below.
    var tot = function(c){ return (+c.wip || 0) + (+c.adv || 0); };
    var has = function(c){ return c.actual !== null && c.actual !== undefined; };
    var GB = '#2ba899', RO = '#d84a30';
    var maxV = 0;
    cyc.forEach(function(c){ maxV = Math.max(maxV, tot(c), has(c) ? +c.actual || 0 : 0); });
    var W = 600, H = 300, base = 262, top = 40, pad = 20;
    var slot = (W - 2 * pad) / cyc.length;
    var barW = Math.min(slot * 0.5, 90), ovW = barW * 0.55;
    var hOf = function(v){ return maxV > 0 ? Math.max(0, Math.round((v / maxV) * (base - top))) : 0; };
    var L = function(v){ return (v / 100000).toFixed(1); };
    var mon = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var lbl = function(c){ var m = /^(\d{2})(\d{2})-(\d{2})/.exec(c.end_date || ''); return m ? mon[+m[3] - 1] + ' ' + m[2] : (c.end_label || c.name); };
    var bars = '';
    cyc.forEach(function(c, i){
        var v = tot(c), h = hOf(v), cx = pad + (i + 0.5) * slot, cur = !!c.current;
        bars += '<rect x="' + (cx - barW / 2).toFixed(1) + '" y="' + (base - h) + '" width="' + barW.toFixed(1) + '" height="' + h + '" fill="#3a4a5f"><title>Target Total Investment ' + L(v) + ' L (WIP ' + L(+c.wip || 0) + ' + Advances ' + L(+c.adv || 0) + ')</title></rect>';
        var topY = base - h;
        if (has(c)) {
            var a = +c.actual || 0, hA = hOf(a);
            bars += '<rect x="' + (cx - ovW / 2).toFixed(1) + '" y="' + (base - hA) + '" width="' + ovW.toFixed(1) + '" height="' + hA + '" fill="' + (a >= v ? GB : RO) + '"><title>Actual Total Investment ' + L(a) + ' L (WIP ' + L(+c.actual_wip || 0) + ' + Advances ' + L(+c.actual_adv || 0) + ')</title></rect>';
            // Actual value at the top of the overlay bar: white inside the target bar, black above it.
            var inside = (base - hA) - topY > 18;
            bars += '<text x="' + cx.toFixed(1) + '" y="' + (base - hA - 5) + '" text-anchor="middle" font-size="12" font-weight="700" fill="' + (inside ? '#fff' : '#000') + '">' + L(a) + '</text>';
            if (!inside) topY = Math.min(topY, base - hA - 18);
        }
        bars += '<text x="' + cx.toFixed(1) + '" y="' + (topY - 6) + '" text-anchor="middle" font-size="11" fill="#555">' + L(v) + '</text>'
            + '<text x="' + cx.toFixed(1) + '" y="' + (base + 22) + '" text-anchor="middle" font-size="13" fill="#111"' + (cur ? ' font-weight="700"' : '') + '>' + lbl(c) + '</text>';
    });
    return '<svg width="100%" height="auto" viewBox="0 0 ' + W + ' ' + H + '" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" style="display:block;">'
        + '<line x1="0" y1="' + base + '" x2="' + W + '" y2="' + base + '" stroke="#999"/>' + bars + '</svg>';
}
function _cfShowNetInvCycles(cycles){
    $('#cf-netinv-breakdown-win').remove();
    window._cfNetInvCycles = cycles;
    var winW = 780, winH = 440, MIN_W = 480, MIN_H = 260;
    var left = Math.max(10, (window.innerWidth - winW) / 2);
    var topP = Math.max(10, (window.innerHeight - winH) / 2);
    var rsHtml = '';
    ['n','s','e','w','ne','nw','se','sw'].forEach(function(d){
        rsHtml += '<div class="cf-ni-rs" data-dir="' + d + '" style="position:absolute;';
        if (d === 'n') rsHtml += 'top:-4px;left:10px;right:10px;height:10px;cursor:n-resize;';
        else if (d === 's') rsHtml += 'bottom:-4px;left:10px;right:10px;height:10px;cursor:s-resize;';
        else if (d === 'e') rsHtml += 'right:-4px;top:10px;bottom:10px;width:10px;cursor:e-resize;';
        else if (d === 'w') rsHtml += 'left:-4px;top:10px;bottom:10px;width:10px;cursor:w-resize;';
        else if (d === 'ne') rsHtml += 'right:-4px;top:-4px;width:14px;height:14px;cursor:ne-resize;';
        else if (d === 'nw') rsHtml += 'left:-4px;top:-4px;width:14px;height:14px;cursor:nw-resize;';
        else if (d === 'se') rsHtml += 'right:-4px;bottom:-4px;width:14px;height:14px;cursor:se-resize;';
        else rsHtml += 'left:-4px;bottom:-4px;width:14px;height:14px;cursor:sw-resize;';
        rsHtml += 'z-index:20;background:transparent;"></div>';
    });
    // Right side: actual Total Investment today (current cycle) and the average of
    // the actual Total Investment of every cycle reached so far.
    var reached = cycles.filter(function(c){ return c.actual !== null && c.actual !== undefined; });
    var curC = cycles.filter(function(c){ return c.current; })[0];
    var curAct = curC && curC.actual !== null && curC.actual !== undefined ? +curC.actual : (reached.length ? +reached[reached.length - 1].actual : null);
    var avgAct = reached.length ? reached.reduce(function(s, c){ return s + (+c.actual || 0); }, 0) / reached.length : null;
    function statBox(title, v, note){
        return '<div style="border:1px solid #dde2e9;border-radius:6px;padding:8px 10px;text-align:center;">'
            + '<div style="font-size:12px;font-weight:700;color:#081142;text-transform:uppercase;letter-spacing:.3px;">' + title + '</div>'
            + '<div style="font-size:22px;font-weight:700;color:#000;line-height:1.2;margin-top:4px;">' + (v === null ? '-' : (v / 100000).toFixed(2) + ' L') + '</div>'
            + '<div style="font-size:11px;color:#5a6e8c;">' + note + '</div></div>';
    }
    var html = '<div id="cf-netinv-breakdown-win" style="position:fixed;left:' + left + 'px;top:' + topP + 'px;width:' + winW + 'px;height:' + winH + 'px;min-width:' + MIN_W + 'px;min-height:' + MIN_H + 'px;background:#fff;border-radius:6px;box-shadow:0 8px 30px rgba(0,0,0,.35);z-index:200600;display:flex;flex-direction:column;overflow:hidden;">'
        + rsHtml
        + '<div id="cf-ni-hdr" style="flex:none;display:flex;justify-content:space-between;align-items:center;padding:10px 16px;background:#081142;cursor:move;user-select:none;">'
        + '<span style="font-family:Barlow Condensed,Arial;font-size:15px;font-weight:700;color:#fff;">Total Investment — Current and Projected</span>'
        + '<button id="cf-netinv-breakdown-close" style="background:none;border:none;font-size:20px;cursor:pointer;color:#fff;line-height:1;">&times;</button>'
        + '</div>'
        + '<div style="flex:none;display:flex;flex-wrap:wrap;align-items:center;gap:4px 14px;padding:8px 16px 0;font-family:Barlow Condensed,Arial;font-size:12px;color:#111;">'
        + '<span><span style="display:inline-block;width:14px;height:10px;background:#3a4a5f;margin-right:5px;vertical-align:middle;"></span>Target</span>'
        + '<span><span style="display:inline-block;width:14px;height:10px;background:#2ba899;margin-right:5px;vertical-align:middle;"></span>Actual at or above target</span>'
        + '<span><span style="display:inline-block;width:14px;height:10px;background:#d84a30;margin-right:5px;vertical-align:middle;"></span>Actual below target</span>'
        + '</div>'
        + '<div style="flex:1;min-width:0;min-height:0;display:flex;gap:12px;overflow:hidden;padding:10px 16px 16px;box-sizing:border-box;">'
        + '<div id="cf-ni-chart" style="flex:1;min-width:0;overflow-y:auto;">' + _cfNetInvCycleBarsHtml(cycles) + '</div>'
        + '<div style="flex:none;width:130px;display:flex;flex-direction:column;justify-content:center;gap:14px;font-family:Barlow Condensed,Arial;">'
        + statBox('Total Investment', curAct, 'actual, today')
        + statBox('Average Investment', avgAct, 'actual, per cycle to date')
        + '</div>'
        + '</div></div>';
    $('body').append(html);
    var win = document.getElementById('cf-netinv-breakdown-win'), hdr = document.getElementById('cf-ni-hdr');
    var act = null, sx = 0, sy = 0, ox = 0, oy = 0, ow = 0, oh = 0;
    function onDown(e, dir){ var r = win.getBoundingClientRect(); act = dir; sx = e.clientX; sy = e.clientY; ox = r.left; oy = r.top; ow = r.width; oh = r.height; e.preventDefault(); if (dir !== 'drag') e.stopPropagation(); }
    function onMove(e){
        if (!act) return;
        e.preventDefault();
        var dx = e.clientX - sx, dy = e.clientY - sy;
        if (act === 'drag') {
            win.style.left = Math.max(0, Math.min(ox + dx, window.innerWidth - win.offsetWidth)) + 'px';
            win.style.top = Math.max(0, Math.min(oy + dy, window.innerHeight - win.offsetHeight)) + 'px';
        } else {
            var l = ox, t = oy, w = ow, h = oh;
            if (act.indexOf('e') > -1) w = Math.max(MIN_W, ow + dx);
            if (act.indexOf('s') > -1) h = Math.max(MIN_H, oh + dy);
            if (act.indexOf('w') > -1) { var nw = Math.max(MIN_W, ow - dx); l = ox + (ow - nw); w = nw; }
            if (act.indexOf('n') > -1) { var nh = Math.max(MIN_H, oh - dy); t = oy + (oh - nh); h = nh; }
            win.style.left = l + 'px'; win.style.top = t + 'px'; win.style.width = w + 'px'; win.style.height = h + 'px';
        }
    }
    function onUp(){ act = null; }
    bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-netinv-breakdown-close') onDown(e, 'drag'); });
    $(win).find('.cf-ni-rs').each(function(){ var dir = $(this).data('dir'); bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); }); });
    bindDragTouch(document, 'mousemove', onMove, { passive: false });
    bindDragTouch(document, 'mouseup', onUp);
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
        bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-netinv-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-netinv-rs').each(function(){
            var dir = $(this).data('dir');
            bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        bindDragTouch(document, 'mousemove', onMove);
        bindDragTouch(document, 'mouseup', onUp);
    }
}
$(document).on('click', '#cf-netinvestment', function(e){
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfnetinvestmentbreakdown', dataType: 'json',
        success: function(d){
            if (d && d.error === 'No') { _cfShowNetInvCycles(d.cycles || []); }
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
            t.style.cssText = 'display:none;position:fixed;z-index:200700;background:#fff;border:1px solid #b0bec5;border-radius:4px;box-shadow:0 4px 16px rgba(0,0,0,.25);font-family:Barlow Condensed,Arial;max-width:900px;min-width:420px;max-height:80vh;overflow-y:auto;';
            document.body.appendChild(t);
        }
        return t;
    }
    $(document).on('click', '#cf-breakdown-tip-close', function(){
        var tip = document.getElementById('cf-breakdown-tip');
        if (tip) tip.style.display = 'none';
    });
    $(document).on('click', '.cf-breakdown-bar-col', function(e){
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
        var rowsHtml = '', netTotal = 0;
        for (var i = 0; i < acct.members.length; i++) {
            netTotal += (+acct.members[i].amount || 0);
            rowsHtml += '<tr><td style="padding:6px 20px 6px 10px;font-size:15px;color:#333;white-space:nowrap;border:1px solid #d5dae2;">' + $('<div>').text(acct.members[i].name || '').html() + '</td>'
                + '<td style="padding:6px 10px;font-size:15px;font-weight:700;color:#251840;text-align:right;border:1px solid #d5dae2;">' + _cfIndianComma(acct.members[i].amount) + '</td></tr>';
        }
        rowsHtml += '<tr><td style="padding:6px 20px 6px 10px;font-size:15px;font-weight:700;color:#251840;white-space:nowrap;border:1px solid #d5dae2;background:#f4f6fa;">Net</td>'
            + '<td style="padding:6px 10px;font-size:15px;font-weight:700;color:#251840;text-align:right;border:1px solid #d5dae2;background:#f4f6fa;">' + _cfIndianComma(netTotal) + '</td></tr>';
        tip.innerHTML = '<div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-bottom:1px solid #e0e4ea;">'
            + '<span style="font-size:16px;font-weight:700;color:#251840;">' + $('<div>').text(acct.name || '').html() + '</span>'
            + '<button id="cf-breakdown-tip-close" style="background:none;border:none;font-size:20px;cursor:pointer;color:#5a6e8c;line-height:1;">&times;</button>'
            + '</div>'
            + '<div style="padding:10px 16px 14px;">'
            + '<table style="border-collapse:collapse;width:100%;">'
            + '<thead><tr><th style="padding:6px 20px 6px 10px;font-size:14px;color:#5a6e8c;text-align:left;border:1px solid #d5dae2;background:#f4f6fa;">Account Head</th><th style="padding:6px 10px;font-size:14px;color:#5a6e8c;text-align:right;border:1px solid #d5dae2;background:#f4f6fa;">Amount</th></tr></thead>'
            + '<tbody>' + rowsHtml + '</tbody></table>'
            + '</div>';
        var rect = this.getBoundingClientRect();
        tip.style.display = 'block';
        var tw = tip.offsetWidth || 200, th = tip.offsetHeight || 60;
        var left = Math.min(rect.left, window.innerWidth - tw - 8);
        // Prefer placing above the bar; if it doesn't fully fit above OR
        // below, pick whichever side has more room so a tall table (many
        // transaction rows) isn't blindly pinned off-screen either way.
        var spaceAbove = rect.top - 8, spaceBelow = window.innerHeight - rect.bottom - 8;
        var top;
        if (th <= spaceAbove || spaceAbove >= spaceBelow) {
            top = rect.top - th - 8;
        } else {
            top = rect.bottom + 8;
        }
        top = Math.max(4, Math.min(top, window.innerHeight - th - 4));
        tip.style.left = Math.max(4, left) + 'px';
        tip.style.top = top + 'px';
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
        bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-invest-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-invest-rs').each(function(){
            var dir = $(this).data('dir');
            bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        bindDragTouch(document, 'mousemove', onMove);
        bindDragTouch(document, 'mouseup', onUp);
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

// Vertical bar chart replacing the old dial — a single bar for the
// Actual value, its value labeled on top, Y-axis gridlines with amount
// labels, X-axis labeled with the panel name. No target reference here
// (dropped along with the dial's yellow marker) per explicit request.
// Single flat dark-grey dial — needle only, no target split (target is
// pinned at the dial's zero/left end, so there's nothing to mark; the
// needle alone shows where the value sits against the auto-scaled max).
// Value = Current Liabilities + Total Investment combined, per explicit
// request. Clicking the panel opens the 2-bar comparison as a popup
// (see _cfCurrLiabBarSvg/_cfShowCurrLiabCombinedTip below) rather than
// showing that breakdown inline on the dial itself.
function _cfCurrLiabDialSvg(sumVal){
    var maxV = sumVal > 0 ? sumVal * 1.5 : 1;
    var f = Math.max(0, Math.min(1, sumVal / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    var p1 = ptF(0), p2 = ptF(1);
    var arcPath = '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="#3a4a5f" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var fmt = _cfFmtMoney(Math.abs(sumVal) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 30) + '" text-anchor="middle" font-size="24" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + (sumVal * divisor).toFixed(2) + '</text>'
        + '<text x="' + cx + '" y="' + (cy - 14) + '" text-anchor="middle" font-size="14" font-weight="700" fill="#5a5a5a" font-family="Barlow Condensed,Arial">' + fmt.unit + '</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

// Two side-by-side bars sharing one Y-axis/scale: Current Liabilities
// and Total Investment (the latter is a read-only echo of that panel's
// own value, fetched independently — see renderCfCurrentLiabilitiesDial
// — the Total Investment panel/dial itself is untouched). Now used as
// the click-triggered popup's content, not the panel's own display.
function _cfCurrLiabBarSvg(actual, netInvActual){
    var scaleBasis = Math.max(Math.abs(actual), Math.abs(netInvActual));
    var fmt = _cfFmtMoney(scaleBasis * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    var dispVal = actual * divisor;
    var dispNetInv = netInvActual * divisor;

    var w = 320, h = 220;
    // padL trimmed to just enough for the Y-axis value labels (the axis
    // line itself sits at the panel's extreme left edge, not indented in
    // toward the middle) — padB carries separate room for the axis line
    // AND each bar's caption stacked below it.
    var padL = 34, padR = 12, padT = 26, padB = 42;
    var plotW = w - padL - padR, plotH = h - padT - padB;
    var scaleMax = Math.max(Math.abs(dispVal), Math.abs(dispNetInv)) > 0
        ? Math.max(Math.abs(dispVal), Math.abs(dispNetInv)) * 1.15 : 1;

    var gridHtml = '', gridLines = 4;
    for (var g = 0; g <= gridLines; g++) {
        var gv = scaleMax * g / gridLines;
        var gy = (padT + plotH - (plotH * g / gridLines)).toFixed(1);
        gridHtml += '<line x1="' + padL + '" y1="' + gy + '" x2="' + (w - padR) + '" y2="' + gy + '" stroke="#e8ecf2" stroke-width="1"/>'
            + '<text x="' + (padL - 4) + '" y="' + (parseFloat(gy) + 4) + '" text-anchor="end" font-size="11" fill="#8a94a6" font-family="Barlow Condensed,Arial">' + gv.toFixed(1) + '</text>';
    }

    // Two slim columns, each centered in its own half of the plot width.
    // Unit is appended inline on the value label (not a separate caption
    // row) per earlier explicit request to drop the standalone unit line.
    // Each bar carries data-cflbar so a delegated click handler can open
    // that metric's own existing account-breakdown popup.
    function bar(val, colX, col, label, key){
        var barW = plotW * 0.22;
        var barX = colX - barW / 2;
        var barH = scaleMax > 0 ? (Math.abs(val) / scaleMax) * plotH : 0;
        var barY = padT + plotH - barH;
        return '<rect data-cflbar="' + key + '" x="' + barX.toFixed(1) + '" y="' + barY.toFixed(1) + '" width="' + barW.toFixed(1) + '" height="' + barH.toFixed(1) + '" fill="' + col + '" style="cursor:pointer;"/>'
            + '<text x="' + colX.toFixed(1) + '" y="' + (barY - 10).toFixed(1) + '" text-anchor="middle" font-size="15" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + val.toFixed(2) + ' ' + fmt.unit + '</text>'
            + '<text data-cflbar="' + key + '" x="' + colX.toFixed(1) + '" y="' + (h - 10) + '" text-anchor="middle" font-size="12" fill="#5a6e8c" font-family="Barlow Condensed,Arial" style="cursor:pointer;">' + label + '</text>';
    }
    var col1X = padL + plotW * 0.28, col2X = padL + plotW * 0.72;
    var barsHtml = bar(dispVal, col1X, '#3d7a6e', 'Current Liabilities', 'cl')
        + bar(dispNetInv, col2X, '#0d3f7a', 'Total Investment', 'ti');

    var axisY = (padT + plotH).toFixed(1);
    // preserveAspectRatio="none": this panel should fill the entire
    // available space (per explicit request), not letterbox to preserve
    // the viewBox's fixed aspect ratio — "meet" was leaving visible empty
    // margins around the chart whenever the panel's actual aspect ratio
    // didn't match 320:220.
    var svg = '<svg width="100%" height="100%" viewBox="0 0 ' + w + ' ' + h + '" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="display:block;flex:1;min-height:0;">'
        + gridHtml
        + '<line x1="' + padL + '" y1="' + padT + '" x2="' + padL + '" y2="' + axisY + '" stroke="#b7c0cc" stroke-width="1"/>'
        + '<line x1="' + padL + '" y1="' + axisY + '" x2="' + (w - padR) + '" y2="' + axisY + '" stroke="#b7c0cc" stroke-width="1"/>'
        + barsHtml
        + '</svg>';
    return '<div style="flex:1;min-height:0;width:100%;height:100%;display:flex;flex-direction:column;align-items:stretch;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawCurrLiabDial(){
    var el = document.getElementById('cf-currentliabilities');
    if (!el) return;
    el.style.justifyContent = 'center';
    el.style.cursor = 'pointer';
    el.title = 'Click to compare with Total Investment';
    var s = _cfCurrLiabState;
    var sumVal = s.actual + s.netInvActual;
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfCurrLiabDialSvg(sumVal)
        + '<div style="display:flex;justify-content:center;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:12px;color:#5a5a5a;">Current Liabilities + Total Investment</span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
    // Re-draw the tooltip in place if it's already open, so it stays in
    // sync as fresh data comes in (billsRecvTarget/wipTarget/actual/
    // netInvActual each arrive from separate AJAX calls at different
    // times — see renderCfCurrentLiabilitiesDial).
    if (document.getElementById('cf-currliab-tip-win')) { _cfRedrawCurrLiabTip(); }
}

// Popup shown when the Current Liabilities dial is clicked: the 2-bar
// Current-Liabilities-vs-Total-Investment comparison (previously the
// panel's own permanent display, moved here per explicit request).
// Clicking either bar inside it opens that metric's own existing
// account-breakdown popup (_cfShowCurrLiabBreakdown / _cfShowNetInvBreakdown).
function _cfRedrawCurrLiabTip(){
    var s = _cfCurrLiabState;
    var el = document.getElementById('cf-currliab-tip-chart');
    if (!el) return;
    el.innerHTML = _cfCurrLiabBarSvg(s.actual, s.netInvActual);
}

function _cfShowCurrLiabTip(){
    if (document.getElementById('cf-currliab-tip-win')) return;
    var winW = 360, winH = 280;
    var anchorEl = document.getElementById('cf-currentliabilities');
    var rect = anchorEl ? anchorEl.getBoundingClientRect() : null;
    var left = rect ? Math.min(rect.left, window.innerWidth - winW - 10) : (window.innerWidth - winW) / 2;
    var top = rect ? Math.min(rect.bottom + 6, window.innerHeight - winH - 10) : (window.innerHeight - winH) / 2;
    if (left < 10) left = 10;
    if (top < 10) top = 10;
    var html = '<div id="cf-currliab-tip-win" style="position:fixed;left:' + left + 'px;top:' + top + 'px;width:' + winW + 'px;height:' + winH + 'px;background:#fff;border-radius:6px;box-shadow:0 8px 30px rgba(0,0,0,.35);z-index:200600;display:flex;flex-direction:column;overflow:hidden;">'
        + '<div id="cf-currliab-tip-hdr" style="flex:none;display:flex;justify-content:space-between;align-items:center;padding:8px 14px;background:#251840;cursor:move;user-select:none;">'
        + '<span style="font-family:Barlow Condensed,Arial;font-size:14px;font-weight:700;color:#fff;">Current Liabilities vs Total Investment</span>'
        + '<button id="cf-currliab-tip-close" style="background:none;border:none;font-size:18px;cursor:pointer;color:#fff;line-height:1;">&times;</button>'
        + '</div>'
        + '<div id="cf-currliab-tip-chart" style="flex:1;min-height:0;display:flex;padding:6px 10px;"></div>'
        + '</div>';
    $('body').append(html);
    _cfRedrawCurrLiabTip();

    var win = document.getElementById('cf-currliab-tip-win');
    var hdr = document.getElementById('cf-currliab-tip-hdr');
    var bdt = (typeof window.bindDragTouch === 'function') ? window.bindDragTouch : function(elm, ev, fn, opts){ elm.addEventListener(ev, fn, opts); };
    var _action = null, _sx = 0, _sy = 0, _ox = 0, _oy = 0;
    function _anchor(){ var r = win.getBoundingClientRect(); win.style.left = r.left + 'px'; win.style.top = r.top + 'px'; return r; }
    bdt(hdr, 'mousedown', function(e){
        if (e.target.id === 'cf-currliab-tip-close') return;
        var r = _anchor();
        _action = true; _sx = e.clientX; _sy = e.clientY; _ox = r.left; _oy = r.top;
        e.preventDefault();
    });
    bdt(document, 'mousemove', function(e){
        if (!_action) return;
        win.style.left = Math.max(0, _ox + (e.clientX - _sx)) + 'px';
        win.style.top = Math.max(0, _oy + (e.clientY - _sy)) + 'px';
    });
    bdt(document, 'mouseup', function(){ _action = null; });
}
$(document).on('click', '#cf-currliab-tip-close', function(){ $('#cf-currliab-tip-win').remove(); });
// Bar/label click inside the tooltip opens that metric's own existing
// account-breakdown popup — same endpoints the Current Liabilities and
// Total Investment panels themselves already use when clicked directly.
$(document).on('click', '#cf-currliab-tip-chart [data-cflbar="cl"]', function(e){
    e.stopPropagation();
    $.ajax({
        type: 'POST', url: '../projectsmain/cfcurrentliabilitiesbreakdown', dataType: 'json',
        success: function(d){ if (d && d.error === 'No') { _cfShowCurrLiabBreakdown(d.accounts || []); } }
    });
});
$(document).on('click', '#cf-currliab-tip-chart [data-cflbar="ti"]', function(e){
    e.stopPropagation();
    $.ajax({
        type: 'POST', url: '../projectsmain/cfnetinvestmentbreakdown', dataType: 'json',
        success: function(d){ if (d && d.error === 'No') { _cfShowNetInvCycles(d.cycles || []); } }
    });
});

function renderCfCurrentLiabilitiesDial(){
    var gen = _cfLoadGen;
    _cfCurrLiabState = { billsRecvTarget: 0, wipTarget: 0, actual: 0, netInvActual: 0 };
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
    // Total Investment's own value, shown as a second bar alongside
    // Current Liabilities per explicit request — fetched independently
    // here (same endpoint the Total Investment panel itself uses) rather
    // than reading _cfNetInvState directly, since that panel's own AJAX
    // may not have resolved yet by the time this one renders.
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfnetinvestment', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfCurrLiabState.netInvActual = (+d.total || 0) / 10000000;
                _cfDrawCurrLiabDial();
            }
        }
    });
}

// Standalone Current Liabilities dial — occupies #cf-bank, the grid slot
// that used to be the Cash-to-Cash Cycle panel (that metric moved to two
// small squares on the group sub-heading instead, see
// renderCfC2cLegend). Single flat colour, no target/needle split, since
// this metric has no editable target of its own — same treatment as the
// old combined dial's underlying value, just shown alone here instead of
// summed with Total Investment.
var _cfCurrLiabState2 = { actual: 0 };

function _cfCurrLiabDialSvg2(actual){
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    var p1 = ptF(0), p2 = ptF(1);
    var arcPath = '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="#3a4a5f" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    var maxV = actual > 0 ? actual * 1.5 : 1;
    var f = Math.max(0, Math.min(1, actual / maxV));
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var fmt = _cfFmtMoney(Math.abs(actual) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 30) + '" text-anchor="middle" font-size="24" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + (actual * divisor).toFixed(2) + '</text>'
        + '<text x="' + cx + '" y="' + (cy - 14) + '" text-anchor="middle" font-size="14" font-weight="700" fill="#5a5a5a" font-family="Barlow Condensed,Arial">' + fmt.unit + '</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawCurrLiabDial2(){
    var el = document.getElementById('cf-bank');
    if (!el) return;
    el.style.justifyContent = 'center';
    el.style.cursor = 'pointer';
    el.title = 'Click to see account breakdown';
    var s = _cfCurrLiabState2;
    // Shown as a large figure in lakhs (like Current Assets), in black.
    el.style.display = 'flex'; el.style.flexDirection = 'column'; el.style.alignItems = 'center';
    el.style.flex = '1'; el.style.height = '100%'; el.style.width = '100%';
    el.innerHTML = '<div style="text-align:center;font-family:Barlow Condensed,Arial;line-height:1;">'
        + '<div style="font-size:42px;font-weight:700;color:#000;">' + (Math.abs(s.actual) * 100).toFixed(2) + '</div>'
        + '<div style="font-size:14px;font-weight:600;color:#5a6e8c;margin-top:6px;letter-spacing:.04em;">LAKHS</div></div>';
    return;
    var fmt = _cfFmtMoney(Math.abs(s.actual) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfCurrLiabDialSvg2(s.actual)
        + '<div style="display:flex;justify-content:center;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:#111;">Actual: <span style="font-size:13px;color:#4a4f57;font-weight:700;">' + (s.actual * divisor).toFixed(2) + '</span></span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfCurrLiabDial2(){
    var gen = _cfLoadGen;
    _cfCurrLiabState2 = { actual: 0 };
    _cfDrawCurrLiabDial2();
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfcurrentliabilities', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfCurrLiabState2.actual = (+d.total || 0) / 10000000;
                _cfDrawCurrLiabDial2();
            }
        }
    });
}

// Cash-to-Cash Cycle: no dial/panel any more (see the _cfDials comment
// above) — Target and Actual now show as two small colored squares plus
// their values inline on the group sub-heading bar (#cf-c2c-legend).
// Target's square is always green (it's just the reference point, not a
// pass/fail state of its own); Actual's square is green when at/under
// Target (a shorter cycle is good) and red when it exceeds Target.
var _cfC2cLegendState = { target: 0, actual: 0 };

function _cfDrawC2cLegend(){
    var el = document.getElementById('cf-c2c-legend');
    if (!el) return;
    var s = _cfC2cLegendState;
    var actualCol = s.actual > s.target ? '#c62828' : '#2e7d32';
    function sq(col){ return '<span style="display:inline-block;width:9px;height:9px;background:' + col + ';border-radius:1px;vertical-align:middle;"></span>'; }
    el.innerHTML = sq('#2e7d32') + ' T ' + s.target.toFixed(1) + ' Days'
        + '&nbsp;&nbsp;' + sq(actualCol) + ' A ' + s.actual.toFixed(1) + ' Days';
}

function renderCfC2cLegend(){
    var gen = _cfLoadGen;
    _cfC2cLegendState = { target: 0, actual: 0 };
    _cfDrawC2cLegend();
    _cfFetchTarget('c2c_cycle', function(d){
        if (gen === _cfLoadGen) { _cfC2cLegendState.target = +d.target || 0; _cfDrawC2cLegend(); }
    });
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfactualc2c', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfC2cLegendState.actual = +d.total || 0;
                _cfDrawC2cLegend();
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

function _cfShowCurrLiabBreakdown(accounts, title){   // title: also used for the Current Assets list
    $('#cf-currliab-breakdown-win').remove();
    window._cfCurrLiabBreakdownState = { accounts: accounts };

    // Width grows with the number of accounts (about 95px per bar) so the names stay legible.
    var winW = Math.round(Math.max(720, Math.min(window.innerWidth - 40, accounts.length * 95 + 80)) * 2 / 3), winH = 440, MIN_W = 340, MIN_H = 240;   // reduced by a third
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
        + '<span style="font-family:Barlow Condensed,Arial;font-size:15px;font-weight:700;color:#fff;">' + (title || 'Current Liabilities — Account Breakdown') + '</span>'
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
        bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-currliab-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-currliab-rs').each(function(){
            var dir = $(this).data('dir');
            bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        bindDragTouch(document, 'mousemove', onMove);
        bindDragTouch(document, 'mouseup', onUp);
    }
}
$(document).on('click', '#cf-currentliabilities', function(e){
    _cfShowCurrLiabTip();
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
    // Base dial arc: uniform dark blue-grey
    var arcPath = arc(0, 1, '#3a4a5f');
    var targetF = 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    // Gap arc: colored based on performance (only render if target > 0 AND actual has meaningful value)
    var gapCol = actual > targetCr ? '#d84a30' : '#2ba899';
    var gapPath = (targetCr > 0 && actual > 0) ? (f < targetF ? arc(f, targetF, gapCol) : arc(targetF, f, gapCol)) : '';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    // Target is typed directly in Lakhs (the input box's fixed
    // convention -- see _cfEnteredToRupees) and Actual is shown in
    // that SAME fixed Lakhs unit, never auto-converted to Crores by
    // magnitude, so the two numbers always stay comparable and the
    // unit the user chose by using this box is never second-guessed.
    var fmt = { unit: 'Lakhs' };
    var divisor = 100;
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + gapPath
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
    // Target is typed directly in Lakhs (the input box's fixed
    // convention -- see _cfEnteredToRupees) and Actual is shown in
    // that SAME fixed Lakhs unit, never auto-converted to Crores by
    // magnitude, so the two numbers always stay comparable and the
    // unit the user chose by using this box is never second-guessed.
    var fmt = { unit: 'Lakhs' };
    var divisor = 100;
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
        bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-projexp-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-projexp-rs').each(function(){
            var dir = $(this).data('dir');
            bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        bindDragTouch(document, 'mousemove', onMove);
        bindDragTouch(document, 'mouseup', onUp);
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
var _cfWipState = { target: 0, currentWip: 0, editing: false, avgDays: null, cycles: [], currentCycle: 0, cycleTargetRupees: 0 };

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
    // Base dial arc: uniform dark blue-grey
    var arcPath = arc(0, 1, '#3a4a5f');
    // Gap arc: colored based on performance (only render if target > 0 AND currentWip has meaningful value)
    // WIP: orange-red if actual < target (work not completed), blue-green if actual > target (more done)
    var gapCol = currentWip < targetCr ? '#d84a30' : '#2ba899';
    var gapPath = (targetCr > 0 && currentWip > 0) ? (f < targetF ? arc(f, targetF, gapCol) : arc(targetF, f, gapCol)) : '';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    // Target is typed directly in Lakhs (the input box's fixed
    // convention -- see _cfEnteredToRupees) and Actual is shown in
    // that SAME fixed Lakhs unit, never auto-converted to Crores by
    // magnitude, so the two numbers always stay comparable and the
    // unit the user chose by using this box is never second-guessed.
    var fmt = { unit: 'Lakhs' };
    var divisor = 100;
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + gapPath
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
    el.title = 'Click to see cycle targets';
    var s = _cfWipState;
    // s.target is the raw number the user typed; reinterpret it via the
    // magnitude rule (< 100 = Lakhs, >= 100 = Crores) before comparing it
    // against currentWip, which is already a Crores-scale float.
    // Per-cycle target (Billing Cycle) when available, in rupees; otherwise
    // the manually typed target.
    var targetCr = (s.cycleTargetRupees > 0 ? s.cycleTargetRupees : _cfEnteredToRupees(s.target)) / 10000000;
    // Target is typed directly in Lakhs (the input box's fixed
    // convention -- see _cfEnteredToRupees) and Actual is shown in
    // that SAME fixed Lakhs unit, never auto-converted to Crores by
    // magnitude, so the two numbers always stay comparable and the
    // unit the user chose by using this box is never second-guessed.
    var fmt = { unit: 'Lakhs' };
    var divisor = 100; // state values are already in Crores; 1 Crore = 100 Lakhs
    var targetCell = s.editing
        ? '<input class="cf-wip-input" data-field="target" type="number" step="any" value="' + s.target + '" style="width:54px;font-size:13px;padding:0 3px;border:1px solid #b0bec5;border-radius:2px;color:#081142;font-weight:700;">'
        : '<span style="font-size:13px;color:#081142;font-weight:700;">' + (targetCr * divisor).toFixed(2) + '</span>';
    // Actual (C.WIP) legend colour always matches whichever arc half the
    // needle is currently pointing into (grey up to target, orange
    // beyond).
    var maxV = targetCr > 0 ? targetCr * 2 : 1;
    var needleCol = (s.currentWip / maxV) <= 0.5 ? '#e07b00' : '#71797e';
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfWipDialSvg(s.target, s.currentWip)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 6px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:12px;color:#111;">C.WIP: <span style="font-size:12px;color:' + needleCol + ';font-weight:700;">' + (s.currentWip * divisor).toFixed(2) + '</span></span>'
        + '<span style="font-size:12px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>T.WIP: ' + targetCell + ' <button class="cf-wip-pencil" title="Edit Target WIP" style="background:none;border:none;cursor:pointer;padding:0 2px;font-size:12px;">&#9998;</button></span>'
        + '</div>'
        + (s.avgDays !== null
            ? '<div style="width:100%;text-align:center;font-family:Barlow Condensed,Arial;padding:2px 6px 0;box-sizing:border-box;"><span style="font-size:12px;color:#111;" title="Length of the most recent WIP cycle (last Contract Income credit to the one before it, or project start for the first cycle). Average across all closed cycles so far: ' + (s.avgAllCycles !== null ? s.avgAllCycles.toFixed(1) : '—') + ' days.">Avg. Time in WIP: <span style="font-weight:700;color:#081142;">' + s.avgDays.toFixed(1) + ' days</span></span></div>'
            : '')
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

// Per-cycle target bars for the WIP breakdown popup: one vertical bar per
// payment cycle (Billing Cycle), each bar = that cycle's target in Lakhs,
// X axis = cycle end month. The current cycle's bar is highlighted.
function _cfWipCycleBarsHtml(){
    var s = _cfWipState;
    var cyc = s.cycles || [];
    if (!cyc.length) return '<div style="padding:8px 10px;font-size:12px;color:#5a6e8c;">Set Project Duration and Billing Cycle to see the cycle targets.</div>';
    var maxV = 0;
    cyc.forEach(function(c){ if (c.target > maxV) maxV = c.target; });
    // Drawn in a fixed 600 x 300 viewBox that scales to the popup width, so the
    // bars fill the window. Each cycle gets an equal slot, with the bar
    // occupying 60% of it, leaving clear space between bars.
    var W = 600, H = 300, base = 262, top = 40, pad = 20;
    var slot = (W - 2 * pad) / cyc.length;
    var barW = slot * 0.6;
    var bars = '';
    cyc.forEach(function(c, i){
        var h = maxV > 0 ? Math.round((c.target / maxV) * (base - top)) : 0;
        var x = pad + i * slot + (slot - barW) / 2;
        var col = (i + 1 === s.currentCycle) ? '#2ba899' : '#3a4a5f';
        bars += '<rect x="' + x.toFixed(1) + '" y="' + (base - h) + '" width="' + barW.toFixed(1) + '" height="' + h + '" fill="' + col + '"/>'
            + '<text x="' + (x + barW / 2).toFixed(1) + '" y="' + (base - h - 6) + '" text-anchor="middle" font-size="12" fill="#111">' + (c.target / 100000).toFixed(1) + '</text>'
            + '<text x="' + (x + barW / 2).toFixed(1) + '" y="' + (base + 18) + '" text-anchor="middle" font-size="12" fill="#333">' + c.end_label + '</text>';
    });
    return '<svg width="100%" height="auto" viewBox="0 0 ' + W + ' ' + H + '" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" style="display:block;">'
        + '<line x1="0" y1="' + base + '" x2="' + W + '" y2="' + base + '" stroke="#999"/>' + bars + '</svg>'
        + '<div style="font-size:12px;color:#5a6e8c;padding:4px 6px 0;">Cycle target (Lakhs) · highlighted = current cycle</div>';
}

function renderCfWipDial(){
    var gen = _cfLoadGen;
    _cfWipState = { target: 0, currentWip: 0, editing: false, avgDays: null, avgAllCycles: null, cycles: [], currentCycle: 0, cycleTargetRupees: 0 };
    _cfDrawWipDial();
    // Target for the dial = the bar of the current payment cycle (see
    // actionCfwipcycles). Replaces the manually typed WIP target.
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfwipcycles', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfWipState.cycles = d.cycles || [];
                _cfWipState.currentCycle = +d.current_cycle || 0;
                _cfWipState.cycleTargetRupees = +d.current_target || 0;
                window._cfWipCyclesData = d;   // reused by the Project Cost popup (opens instantly)
                _cfDrawWipDial();
            }
        }
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
    // Length of the most recent WIP cycle (not an average) — see
    // actionCfwipavgtime. avg_all_cycles is the true historical average,
    // shown only in the dial's hover tooltip as extra context.
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfwipavgtime', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfWipState.avgDays = +d.avg_days || 0;
                _cfWipState.avgAllCycles = +d.avg_all_cycles || 0;
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

function _cfContribMarginDialSvg(target, percent, valueLabel){
    var maxV = target > 0 ? Math.min(100, target * 2) : 100;
    var f = Math.max(0, Math.min(1, percent / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    // Base dial arc: uniform dark blue-grey
    var arcPath = arc(0, 1, '#3a4a5f');
    var targetF = maxV > 0 ? Math.max(0, Math.min(1, target / maxV)) : 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    // Gap arc: colored based on performance. Contribution Margin:
    // - Blue-green if percent > target (good - higher margin), orange-red if less
    var gapCol = percent < target ? '#d84a30' : '#2ba899';
    var gapPath = (target > 0) ? (f < targetF ? arc(f, targetF, gapCol) : arc(targetF, f, gapCol)) : '';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    // Percentage label sits just outside the needle's own tip, along the
    // same angle the needle points at — not centered in the dial anymore
    // (only valueLabel occupies the center now) — per explicit request.
    var labelR = r + sw / 2 + 14;
    var labelX = (cx + labelR * Math.cos(na)).toFixed(1), labelY = (cy - labelR * Math.sin(na)).toFixed(1);
    var labelAnchor = Math.cos(na) > 0.15 ? 'start' : (Math.cos(na) < -0.15 ? 'end' : 'middle');
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + gapPath
        + targetLine
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + labelX + '" y="' + labelY + '" text-anchor="' + labelAnchor + '" font-size="16" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + percent.toFixed(2) + '%</text>'
        + '<text x="' + cx + '" y="' + (cy - 20) + '" text-anchor="middle" font-size="24" font-weight="700" fill="#5a5a5a" font-family="Barlow Condensed,Arial">' + valueLabel + '</text>'
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
    var needleCol = (s.percent / maxV) <= 0.5 ? '#b5540a' : '#00838f';
    var valueLabel = (s.value / divisor).toFixed(2) + ' ' + fmt.unit;
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfContribMarginDialSvg(s.target, s.percent, valueLabel)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:' + needleCol + ';font-weight:700;">' + s.percent.toFixed(2) + '%</span>'
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

// Free Cash panel — needle/arc still driven by the percentage —
// (Free Cash value / Average Investment) * 100 — same as Contribution
// Margin's gauge mechanics, but the dial's CENTER now shows the actual
// rupee value (Contribution Margin - Total Investment - Current
// Liabilities — see actionCfreecash) instead of the percentage, per
// explicit request. Same rupee value is also shown to the left in
// Lakhs (<= 99 Lakhs) or Crores (> 99 Lakhs). Target is a user-editable
// PERCENTAGE, saved under metric_key "free_cash_target". Left-arc
// colour (deep purple, #4a2e7a) is distinct from Contribution Margin's
// teal, per explicit request, so the two dials aren't visually identical.
var _cfFreeCashState = { target: 0, value: 0, percent: 0, avgInvestment: 0, editing: false };

function _cfFreeCashDialSvg(target, percent, centerLabel){
    var maxV = target > 0 ? Math.min(100, target * 2) : 100;
    var f = Math.max(0, Math.min(1, percent / maxV));
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    // Base dial arc: uniform dark blue-grey
    var arcPath = arc(0, 1, '#3a4a5f');
    var targetF = maxV > 0 ? Math.max(0, Math.min(1, target / maxV)) : 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    // Gap arc: colored based on performance (only render if target > 0)
    var gapCol = percent > target ? '#d84a30' : '#2ba899';
    var gapPath = (target > 0) ? (f < targetF ? arc(f, targetF, gapCol) : arc(targetF, f, gapCol)) : '';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + gapPath
        + targetLine
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 22) + '" text-anchor="middle" font-size="22" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + centerLabel + '</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawFreeCashDial(){
    var el = document.getElementById('cf-expenses');
    if (!el) return;
    el.style.justifyContent = 'center';
    var s = _cfFreeCashState;
    // Target is still edited/stored as a PERCENTAGE (metric_key
    // "free_cash_target"), but displayed here as its rupee equivalent —
    // target% * Average Investment / 100 — formatted the same way as
    // the Actual value, per explicit request to show a figure, not %.
    var targetRupees = s.target / 100 * s.avgInvestment;
    var targetFmt = _cfFmtMoney(targetRupees);
    // Actual's displayed unit follows the Target's unit (so the two
    // never show in different units, e.g. Actual in Lakhs while Target
    // sits in Crores) — falling back to Actual's own auto-magnitude unit
    // only when there's no target set yet (targetRupees is 0, so
    // targetFmt carries no real signal to follow).
    var fmt = targetRupees !== 0 ? targetFmt : _cfFmtMoney(s.value);
    var divisor = fmt.unit === 'Crores' ? 10000000 : 100000;
    var targetDivisor = divisor;
    var targetCell = s.editing
        ? '<input class="cf-freecash-input" data-field="target" type="number" step="any" value="' + (targetRupees / targetDivisor).toFixed(2) + '" style="width:60px;font-size:13px;padding:0 3px;border:1px solid #b0bec5;border-radius:2px;color:#5a5a5a;font-weight:700;"> <span style="font-size:11px;color:#888;">' + fmt.unit + '</span>'
        : '<span style="font-size:13px;color:#5a5a5a;font-weight:700;">' + (targetRupees / targetDivisor).toFixed(2) + ' ' + fmt.unit + '</span>';
    var maxV = s.target > 0 ? Math.min(100, s.target * 2) : 100;
    var needleCol = (s.percent / maxV) <= 0.5 ? '#4a2e7a' : '#b5540a';
    var centerLabel = (s.value / divisor).toFixed(2) + ' ' + fmt.unit;
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfFreeCashDialSvg(s.target, s.percent, centerLabel)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:' + needleCol + ';font-weight:700;">' + (s.value / divisor).toFixed(2) + ' ' + fmt.unit + '</span>'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>Target: ' + targetCell + ' <button class="cf-freecash-pencil" title="Edit Target" style="background:none;border:none;cursor:pointer;padding:0 2px;font-size:12px;">&#9998;</button></span>'
        + '</div>'
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfFreeCashDial(){
    var gen = _cfLoadGen;
    _cfFreeCashState = { target: 0, value: 0, percent: 0, avgInvestment: 0, editing: false };
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
                _cfFreeCashState.avgInvestment = +d.average_investment || 0;
                _cfDrawFreeCashDial();
            }
        }
    });
}

$(document).on('click', '.cf-freecash-pencil', function(e){
    e.preventDefault(); e.stopPropagation();
    var s = _cfFreeCashState;
    if (s.editing) {
        // Input is entered as a rupee figure (Lakhs under 100, else
        // Crores — same convention as _cfEnteredToRupees), converted
        // back to the percentage that's actually stored server-side.
        var enteredRupees = _cfEnteredToRupees(+$('.cf-freecash-input[data-field="target"]').val() || 0);
        var pct = s.avgInvestment > 0 ? (enteredRupees / s.avgInvestment) * 100 : 0;
        s.target = pct;
        s.editing = false;
        _cfDrawFreeCashDial();
        _cfQueueAjax({
            type: 'POST', url: '../projectsmain/cfsavetarget', dataType: 'json',
            data: { metric_key: 'free_cash_target', target_value: pct },
            success: function(){ renderCfBars(); }
        });
    } else {
        s.editing = true;
        _cfDrawFreeCashDial();
    }
});

// ROI panel — a PERCENTAGE dial, same presentation pattern as
// Contribution Margin/Free Cash (Actual PERCENTAGE shown to the LEFT,
// Target editable on the right, percentage at center too). ROI =
// (Contribution Margin / Average Investment) * 100 (see actionCfroi).
// Target is a user-editable PERCENTAGE, saved under metric_key
// "roi_target". Arc is orange-red (#b5540a) on the left half, teal
// (#00838f) on the right half, per explicit request.
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
    // Base dial arc: uniform dark blue-grey
    var arcPath = arc(0, 1, '#3a4a5f');
    var targetF = maxV > 0 ? Math.max(0, Math.min(1, target / maxV)) : 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    // Gap arc: colored based on performance. ROI:
    // - Blue-green if percent > target (good - higher ROI), orange-red if less
    var gapCol = percent < target ? '#d84a30' : '#2ba899';
    var gapPath = (target > 0) ? (f < targetF ? arc(f, targetF, gapCol) : arc(targetF, f, gapCol)) : '';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + gapPath
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
    var targetCell = s.editing
        ? '<input class="cf-roi-input" data-field="target" type="number" step="any" value="' + s.target + '" style="width:54px;font-size:13px;padding:0 3px;border:1px solid #b0bec5;border-radius:2px;color:#5a5a5a;font-weight:700;">'
        : '<span style="font-size:13px;color:#5a5a5a;font-weight:700;">' + s.target.toFixed(2) + '%</span>';
    var maxV = s.target > 0 ? Math.min(100, s.target * 2) : 100;
    var needleCol = (s.percent / maxV) <= 0.5 ? '#b5540a' : '#00838f';
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfRoiDialSvg(s.target, s.percent)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:' + needleCol + ';font-weight:700;">' + s.percent.toFixed(2) + '%</span>'
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
    $('#cf-wip-breakdown-unit').html(
        '<span style="font-weight:700;color:#081142;">Target per billing cycle (Lakhs)</span>'
        + '<span style="float:right;font-weight:700;color:#081142;">Total: ' + (_cfWipState.cycles.reduce(function(sum, c){ return sum + (+c.target || 0); }, 0) / 100000).toFixed(2) + ' Lakhs</span>'
    );
    $('#cf-wip-breakdown-chart').html(_cfWipCycleBarsHtml());
}

function _cfShowWipBreakdown(){
    $('#cf-wip-breakdown-win').remove();
    window._cfWipBreakdownState = { cycles: _cfWipState.cycles, currentCycle: _cfWipState.currentCycle };

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
        + '<span style="font-family:Barlow Condensed,Arial;font-size:15px;font-weight:700;color:#fff;">Work In Progress — Cycle Targets</span>'
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
        bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-wip-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-wip-rs').each(function(){
            var dir = $(this).data('dir');
            bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        bindDragTouch(document, 'mousemove', onMove);
        bindDragTouch(document, 'mouseup', onUp);
    }
}
// Work In Progress panel: the account heads making up the current WIP, as vertical
// bars of their actual cash and bank payments since the last bill (see
// actionCfwipaccounts). Replaces the earlier cycle-target popup.
$(document).on('click', '#cf-billsreceivable', function(e){
    if ($(e.target).closest('.cf-wip-pencil, .cf-wip-input').length) return;
    $.ajax({
        type: 'POST', url: '../projectsmain/cfwipaccounts', dataType: 'json',
        success: function(d){
            if (!d || d.error !== 'No') return;
            var t = 'Work In Progress — Payments' + (d.last_bill ? ' since the bill of ' + d.last_bill : '');
            if (Math.abs(+d.other_received || 0) >= 0.01) t += ' (less other receipts ' + ((+d.other_received) / 100000).toFixed(2) + ' L)';
            _cfShowCurrLiabBreakdown(d.accounts || [], t);
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
var _cfBillsRecvState = { target: 0, actual: 0, editing: false, avgDays: null };

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
    // Base dial arc: uniform dark blue-grey
    var arcPath = arc(0, 1, '#3a4a5f');
    var targetF = 0.5;
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>';
    // Gap arc: colored based on performance (only render if target > 0, inverted for bills receivable: less than target is underperformance)
    var gapCol = actual < targetCr ? '#d84a30' : '#2ba899';
    var gapPath = (targetCr > 0) ? (f < targetF ? arc(f, targetF, gapCol) : arc(targetF, f, gapCol)) : '';
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    // Target is typed directly in Lakhs (the input box's fixed
    // convention -- see _cfEnteredToRupees) and Actual is shown in
    // that SAME fixed Lakhs unit, never auto-converted to Crores by
    // magnitude, so the two numbers always stay comparable and the
    // unit the user chose by using this box is never second-guessed.
    var fmt = { unit: 'Lakhs' };
    var divisor = 100;
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + gapPath
        + targetLine
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 30) + '" text-anchor="middle" font-size="24" font-weight="700" fill="#000" font-family="Barlow Condensed,Arial">' + (actual * divisor).toFixed(2) + '</text>'
        + '<text x="' + cx + '" y="' + (cy - 14) + '" text-anchor="middle" font-size="14" font-weight="700" fill="#5a5a5a" font-family="Barlow Condensed,Arial">' + fmt.unit + '</text>'
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
    // Per-cycle target (WIP target x project value / total cost), in rupees;
    // otherwise the manually typed target.
    var targetCr = (s.cycleTargetRupees > 0 ? s.cycleTargetRupees : _cfEnteredToRupees(s.target)) / 10000000;
    // Target is typed directly in Lakhs (the input box's fixed
    // convention -- see _cfEnteredToRupees) and Actual is shown in
    // that SAME fixed Lakhs unit, never auto-converted to Crores by
    // magnitude, so the two numbers always stay comparable and the
    // unit the user chose by using this box is never second-guessed.
    var fmt = { unit: 'Lakhs' };
    var divisor = 100;
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
        + (s.avgDays !== null
            ? '<div style="width:100%;text-align:center;font-family:Barlow Condensed,Arial;padding:2px 6px 0;box-sizing:border-box;"><span style="font-size:12px;color:#111;" title="Days from the most recent bill to its payment (or to today if still unpaid). Average across all bills raised so far: ' + (s.avgAllCycles !== null ? s.avgAllCycles.toFixed(1) : '—') + ' days.">Avg. Collection Time: <span style="font-weight:700;color:#6b8e23;">' + s.avgDays.toFixed(1) + ' days</span></span></div>'
            : '')
        + '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>';
}

function renderCfBillsReceivableDial(){
    var gen = _cfLoadGen;
    _cfBillsRecvState = { target: 0, actual: 0, editing: false, avgDays: null, avgAllCycles: null, cycleTargetRupees: 0 };
    _cfDrawBillsRecvDial();
    // Dial target = current cycle's Bills Receivable target (see
    // actionCfbillsreceivablecycles). Replaces the manually typed target.
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfbillsreceivablecycles', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfBillsRecvState.cycleTargetRupees = +d.current_target || 0;
                _cfDrawBillsRecvDial();
            }
        }
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
    // Days from the most recent bill to its payment (not an average) —
    // see actionCfbillsreceivableavgtime. avg_all_cycles is the true
    // historical average, shown only in the dial's hover tooltip.
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfbillsreceivableavgtime', dataType: 'json',
        success: function(d){
            if (gen === _cfLoadGen && d && d.error === 'No') {
                _cfBillsRecvState.avgDays = +d.avg_days || 0;
                _cfBillsRecvState.avgAllCycles = +d.avg_all_cycles || 0;
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
// Per-cycle Bills Receivable bars: grey = target for the cycle, overlaid by the
// actual credited to Contract Income. A shortfall (actual below target) is shown
// in orange-red, an excess (actual above target) in blue-green.
function _cfBillsRecvBarsHtml(){
    var s = window._cfBillsRecvBreakdownState || {};
    var cyc = s.cycles || [];
    if (!cyc.length) return '<div style="padding:8px 10px;font-size:12px;color:#5a6e8c;">Set Project Duration, Billing Cycle, Project Value and an estimate to see the cycle targets.</div>';
    var maxV = 0;
    cyc.forEach(function(c){ maxV = Math.max(maxV, c.target, c.actual); });
    var W = 600, H = 300, base = 262, top = 40, pad = 20;
    var slot = (W - 2 * pad) / cyc.length;
    var barW = slot * 0.6;
    var scale = function(v){ return maxV > 0 ? (v / maxV) * (base - top) : 0; };
    var out = '';
    cyc.forEach(function(c, i){
        var x = pad + i * slot + (slot - barW) / 2;
        var tH = scale(c.target), aH = scale(c.actual);
        var lo = Math.min(tH, aH), hi = Math.max(tH, aH);
        var diffCol = (c.actual >= c.target) ? '#2ba899' : '#d84a30';
        out += '<rect x="' + x.toFixed(1) + '" y="' + (base - tH).toFixed(1) + '" width="' + barW.toFixed(1) + '" height="' + tH.toFixed(1) + '" fill="#d0d7e2"/>';
        out += '<rect x="' + x.toFixed(1) + '" y="' + (base - lo).toFixed(1) + '" width="' + barW.toFixed(1) + '" height="' + lo.toFixed(1) + '" fill="#3a4a5f"/>';
        if (hi > lo) {
            out += '<rect x="' + x.toFixed(1) + '" y="' + (base - hi).toFixed(1) + '" width="' + barW.toFixed(1) + '" height="' + (hi - lo).toFixed(1) + '" fill="' + diffCol + '"/>';
        }
        out += '<text x="' + (x + barW / 2).toFixed(1) + '" y="' + (base - Math.max(tH, aH) - 6).toFixed(1) + '" text-anchor="middle" font-size="12" fill="#111">' + (c.target / 100000).toFixed(1) + '</text>';
        out += '<text x="' + (x + barW / 2).toFixed(1) + '" y="' + (base + 18) + '" text-anchor="middle" font-size="12" fill="#333">' + c.end_label + '</text>';
    });
    return '<svg width="100%" height="auto" viewBox="0 0 ' + W + ' ' + H + '" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" style="display:block;">'
        + '<line x1="0" y1="' + base + '" x2="' + W + '" y2="' + base + '" stroke="#999"/>' + out + '</svg>'
        + '<div style="font-size:12px;color:#5a6e8c;padding:4px 6px 0;">Grey = target · dark = actual · orange-red = shortfall · blue-green = excess (Lakhs)</div>';
}

function _cfRedrawBillsRecvBreakdown(){
    var s = window._cfBillsRecvBreakdownState;
    if (!s) return;
    var tot = (s.cycles || []).reduce(function(sum, c){ return sum + (+c.actual || 0); }, 0);
    $('#cf-billsrecv-breakdown-unit').html(
        '<span style="font-weight:700;color:#251840;">Target per billing cycle (Lakhs)</span>'
        + '<span style="float:right;font-weight:700;color:#251840;">Total credited: ' + (tot / 100000).toFixed(2) + ' Lakhs</span>'
    );
    $('#cf-billsrecv-breakdown-chart').html(_cfBillsRecvBarsHtml());
}

function _cfShowBillsRecvBreakdown(){
    $('#cf-billsrecv-breakdown-win').remove();
    window._cfBillsRecvBreakdownState = { cycles: _cfBillsRecvState.cycles || [], currentCycle: _cfBillsRecvState.currentCycle || 0 };

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
        + '<span style="font-family:Barlow Condensed,Arial;font-size:15px;font-weight:700;color:#fff;">Bills Receivable — Cycle Targets</span>'
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
        bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-billsrecv-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-billsrecv-rs').each(function(){
            var dir = $(this).data('dir');
            bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        bindDragTouch(document, 'mousemove', onMove);
        bindDragTouch(document, 'mouseup', onUp);
    }
}
$(document).on('click', '#cf-contract-income', function(e){
    if ($(e.target).closest('.cf-billsrecv-pencil, .cf-billsrecv-input').length) return;
    _cfQueueAjax({
        type: 'POST', url: '../projectsmain/cfbillsreceivablecycles', dataType: 'json',
        success: function(d){
            if (d && d.error === 'No') {
                _cfBillsRecvState.cycles = d.cycles || [];
                _cfBillsRecvState.currentCycle = +d.current_cycle || 0;
                _cfShowBillsRecvBreakdown();
            }
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
    // Zero-centred scale: left tip = -Target, centre (yellow) = 0, right
    // tip = +Target. "availed" here is the signed bank balance in Crores.
    var span = wcapitalCr > 0 ? wcapitalCr : (Math.abs(availed) || 1);
    var f = Math.max(0, Math.min(1, 0.5 + availed / (2 * span)));
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
    // Base dial arc: uniform dark blue-grey
    var arcPath = arc(0, 1, '#3a4a5f');
    // Gap arc from 0 (centre) to the balance: green-blue when positive,
    // red-orange when negative (overdrawn).
    var gapCol = availed < 0 ? '#d84a30' : '#2ba899';
    var gapPath = f < targetF ? arc(f, targetF, gapCol) : arc(targetF, f, gapCol);
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    // Center shows the actual (Availed) value + unit, same treatment as
    // the Investments dial (value at cy-30, unit at cy-14, above the
    // needle pivot). Displayed unit follows Allocated (the target-like
    // figure)'s own magnitude, not whichever of the two is larger —
    // falls back to Availed's own magnitude only when Allocated is 0.
    var fmt = wcapitalCr !== 0 ? _cfFmtMoney(Math.abs(wcapitalCr) * 10000000) : _cfFmtMoney(Math.abs(availed) * 10000000);
    var divisor = fmt.unit === 'Crores' ? 1 : 100;
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + arcPath
        + gapPath
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
    el.style.cursor = 'pointer';
    el.title = 'Click to see peak working capital usage between client payments';
    var s = _cfBankState;
    // Allocated (W.Capital) is a user-entered target: the raw number
    // typed is reinterpreted via the magnitude rule (< 100 = Lakhs,
    // >= 100 = Crores), same as every other value. Actual (Availed) is
    // derived from a raw rupee backend figure and follows the same rule;
    // the displayed unit follows Allocated's own magnitude (falling back
    // to Availed's own magnitude only when Allocated is 0/unset), so the
    // two numbers stay comparable at a glance rather than each floating
    // independently between Lakhs and Crores.
    var wcapitalRupees = _cfEnteredToRupees(s.wcapital);
    var availedRupees = (+s.availed || 0) * 10000000;
    var fmt = wcapitalRupees !== 0 ? _cfFmtMoney(Math.abs(wcapitalRupees)) : _cfFmtMoney(Math.abs(availedRupees));
    var divisor = fmt.unit === 'Crores' ? 10000000 : 100000;
    var wcCell = s.editing
        ? '<input class="cf-bank-input" data-field="wcapital" type="number" step="any" value="' + s.wcapital + '" style="width:54px;font-size:13px;padding:0 3px;border:1px solid #b0bec5;border-radius:2px;color:#5a5a5a;font-weight:700;">'
        : '<span style="font-size:13px;color:#5a5a5a;font-weight:700;">' + (wcapitalRupees / divisor).toFixed(2) + '</span>';
    // Actual (Availed) legend colour/swatch always matches whichever arc
    // half the needle is currently pointing into (grey up to target,
    // orange beyond).
    var needleCol = availedRupees < 0 ? '#d84a30' : '#2ba899';
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfBankDialSvg(s.wcapital, s.availed)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:' + needleCol + ';border-radius:1px;margin-right:4px;vertical-align:middle;"></span>Actual: <span style="font-size:13px;color:' + needleCol + ';font-weight:700;">' + (availedRupees / divisor).toFixed(2) + '</span></span>'
        + '<span style="font-size:13px;color:#111;"><span style="display:inline-block;width:14px;height:5px;background:#ffff00;border-radius:1px;margin-right:4px;vertical-align:middle;"></span>Target: ' + wcCell + ' <button class="cf-bank-pencil" title="Edit Target" style="background:none;border:none;cursor:pointer;padding:0 2px;font-size:12px;">&#9998;</button></span>'
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
            if (gen === _cfLoadGen && d && d.error === 'No' && d.has_bank) {
                // Signed bank balance (debit - credit) in rupees -> Crores.
                _cfBankState.availed = (+d.balance || 0) / 10000000;
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

// Bank/Working Capital breakdown popup — a LINE chart (every other
// breakdown popup on this dashboard is a bar chart), one point per
// 45-day cycle from the project's planned start to its planned end,
// showing the PROJECTED net working-capital requirement (cumulative
// planned cost minus cumulative straight-lined expected income) at
// that point — see actionCfbankbalancebreakdown. X axis = the calendar
// date each cycle ends on. Y axis = amount in Lakhs/Crores, same
// magnitude rule as every other value on this dashboard. Values can go
// negative (a projected surplus rather than a funding gap) — the zero
// line is drawn wherever it falls inside the plot rather than always
// pinned to the bottom, and negative-side gridlines/labels are included.
function _cfLineChartHtml(points, chartH){
    points = points || [];
    var maxVal = 0, minVal = 0;
    for (var i = 0; i < points.length; i++) {
        if (points[i].amount > maxVal) maxVal = points[i].amount;
        if (points[i].amount < minVal) minVal = points[i].amount;
    }
    var magnitude = Math.max(Math.abs(maxVal), Math.abs(minVal));
    var fmt = _cfFmtMoney(magnitude);
    var divisor = fmt.unit === 'Crores' ? 10000000 : 100000;

    var padL = 64, padR = 16, padT = 16, padB = 34;
    var w = Math.max(360, points.length * 90);
    var h = Math.max(160, chartH);
    var plotW = w - padL - padR, plotH = h - padT - padB;
    var scaleMax = maxVal > 0 ? maxVal * 1.15 : 0;
    var scaleMin = minVal < 0 ? minVal * 1.15 : 0;
    var scaleRange = (scaleMax - scaleMin) || 1;

    function xAt(i){ return points.length > 1 ? padL + (plotW * i / (points.length - 1)) : padL + plotW / 2; }
    function yAt(v){ return padT + plotH - (plotH * (v - scaleMin) / scaleRange); }

    var gridHtml = '', gridLines = 4;
    for (var g = 0; g <= gridLines; g++) {
        var gv = scaleMin + (scaleRange * g / gridLines);
        var gy = yAt(gv).toFixed(1);
        gridHtml += '<line x1="' + padL + '" y1="' + gy + '" x2="' + (w - padR) + '" y2="' + gy + '" stroke="#e8ecf2" stroke-width="1"/>'
            + '<text x="' + (padL - 8) + '" y="' + (parseFloat(gy) + 4) + '" text-anchor="end" font-size="10" fill="#8a94a6" font-family="Barlow Condensed,Arial">' + (gv / divisor).toFixed(1) + '</text>';
    }
    // Zero line drawn distinctly (darker/dashed) when it falls strictly
    // inside the plot, i.e. whenever the series crosses from surplus to
    // shortfall or vice versa.
    var zeroHtml = '';
    if (scaleMin < 0 && scaleMax > 0) {
        var zy = yAt(0).toFixed(1);
        zeroHtml = '<line x1="' + padL + '" y1="' + zy + '" x2="' + (w - padR) + '" y2="' + zy + '" stroke="#8a94a6" stroke-width="1.2" stroke-dasharray="4,3"/>';
    }

    var pathD = '', dotsHtml = '', labelsHtml = '';
    for (var p = 0; p < points.length; p++) {
        var px = xAt(p), py = yAt(points[p].amount);
        pathD += (p === 0 ? 'M' : 'L') + px.toFixed(1) + ',' + py.toFixed(1) + ' ';
        var dotColor = points[p].amount < 0 ? '#2e7d32' : '#e07b00';
        dotsHtml += '<circle cx="' + px.toFixed(1) + '" cy="' + py.toFixed(1) + '" r="4" fill="' + dotColor + '" stroke="#fff" stroke-width="1.5"/>'
            + '<text x="' + px.toFixed(1) + '" y="' + (py - 10).toFixed(1) + '" text-anchor="middle" font-size="11" font-weight="700" fill="#333" font-family="Barlow Condensed,Arial">' + _cfIndianComma(points[p].amount / divisor) + '</text>';
        labelsHtml += '<text x="' + px.toFixed(1) + '" y="' + (h - 10) + '" text-anchor="middle" font-size="11" fill="#5a6e8c" font-family="Barlow Condensed,Arial">' + $('<div>').text(points[p].name).html() + '</text>';
    }

    var axisY = yAt(0).toFixed(1); /* x-axis sits at value 0, not always the bottom */
    var svg = '<svg width="' + w + '" height="' + h + '" viewBox="0 0 ' + w + ' ' + h + '" xmlns="http://www.w3.org/2000/svg" style="display:block;">'
        + gridHtml + zeroHtml
        + '<line x1="' + padL + '" y1="' + padT + '" x2="' + padL + '" y2="' + (h - padB) + '" stroke="#b7c0cc" stroke-width="1"/>'
        + '<line x1="' + padL + '" y1="' + axisY + '" x2="' + (w - padR) + '" y2="' + axisY + '" stroke="#b7c0cc" stroke-width="1"/>'
        + (points.length ? '<path d="' + pathD + '" fill="none" stroke="#e07b00" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>' : '')
        + dotsHtml + labelsHtml
        + '</svg>';

    return { unit: fmt.unit, body: '<div style="overflow-x:auto;">' + svg + '</div>' };
}

function _cfRedrawBankBreakdown(){
    var s = window._cfBankBreakdownState;
    if (!s) return;
    var chartH = Math.max(120, $('#cf-bank-breakdown-win').height() - 130);
    var r = _cfLineChartHtml(s.points, chartH);
    $('#cf-bank-breakdown-unit').html(
        '<span style="font-weight:700;color:#251840;">Projected Net Working Capital Requirement, in ' + r.unit + '</span>'
    );
    $('#cf-bank-breakdown-chart').html(r.body);
}

function _cfShowBankBreakdown(points){
    $('#cf-bank-breakdown-win').remove();
    window._cfBankBreakdownState = { points: points };

    var winW = 720, winH = 380, MIN_W = 380, MIN_H = 240;
    var left = Math.max(10, (window.innerWidth - winW) / 2);
    var top = Math.max(10, (window.innerHeight - winH) / 2);

    var rsHandles = ['n','s','e','w','ne','nw','se','sw'];
    var rsHtml = '';
    for (var h = 0; h < rsHandles.length; h++) {
        rsHtml += '<div class="cf-bank-bd-rs" data-dir="' + rsHandles[h] + '" style="position:absolute;';
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

    var html = '<div id="cf-bank-breakdown-win" style="position:fixed;left:' + left + 'px;top:' + top + 'px;width:' + winW + 'px;height:' + winH + 'px;min-width:' + MIN_W + 'px;min-height:' + MIN_H + 'px;background:#fff;border-radius:6px;box-shadow:0 8px 30px rgba(0,0,0,.35);z-index:200600;display:flex;flex-direction:column;overflow:hidden;">'
        + rsHtml
        + '<div id="cf-bank-breakdown-hdr" style="flex:none;display:flex;justify-content:space-between;align-items:center;padding:10px 16px;background:#5a5a5a;cursor:move;user-select:none;">'
        + '<span style="font-family:Barlow Condensed,Arial;font-size:15px;font-weight:700;color:#fff;">Working Capital — Projected Requirement per 45-Day Cycle</span>'
        + '<button id="cf-bank-breakdown-close" style="background:none;border:none;font-size:20px;cursor:pointer;color:#fff;line-height:1;">&times;</button>'
        + '</div>'
        + '<div id="cf-bank-breakdown-unit" style="flex:none;font-family:Barlow Condensed,Arial;font-size:12px;color:#5a6e8c;padding:8px 16px 0;overflow:hidden;"></div>'
        + '<div style="flex:1;min-width:0;min-height:0;overflow-x:hidden;overflow-y:auto;padding:10px 16px 16px;box-sizing:border-box;">'
        + '<div id="cf-bank-breakdown-chart" style="min-width:0;"></div>'
        + '</div>'
        + '</div>';
    $('body').append(html);
    _cfRedrawBankBreakdown();

    var win = document.getElementById('cf-bank-breakdown-win');
    var hdr = document.getElementById('cf-bank-breakdown-hdr');
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
            var l = _ox, t = _oy, w = _ow, hh = _oh;
            if (_action.indexOf('e') > -1) { w = Math.max(MIN_W, _ow + dx); }
            if (_action.indexOf('s') > -1) { hh = Math.max(MIN_H, _oh + dy); }
            if (_action.indexOf('w') > -1) { var nw = Math.max(MIN_W, _ow - dx); l = _ox + (_ow - nw); w = nw; }
            if (_action.indexOf('n') > -1) { var nh = Math.max(MIN_H, _oh - dy); t = _oy + (_oh - nh); hh = nh; }
            win.style.left = l + 'px'; win.style.top = t + 'px';
            win.style.width = w + 'px'; win.style.height = hh + 'px';
            _cfRedrawBankBreakdown();
        }
    }
    function onUp(){ _action = null; }

    if (typeof window.bindDragTouch === 'function') {
        window.bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-bank-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-bank-bd-rs').each(function(){
            window.bindDragTouch(this, 'mousedown', function(e){ onDown(e, this.getAttribute('data-dir')); });
        });
        window.bindDragTouch(document, 'mousemove', onMove);
        window.bindDragTouch(document, 'mouseup', onUp);
    }

    document.getElementById('cf-bank-breakdown-close').addEventListener('click', function(){
        win.remove();
    });
}

$(document).on('click', '#cf-bankdial', function(e){
    if (e.target.closest('.cf-bank-pencil') || e.target.closest('.cf-bank-input')) return;
    $.ajax({
        type: 'POST', url: '../projectsmain/cfbankbalancebreakdown', dataType: 'json',
        success: function(d){
            if (d && d.error === 'No') { _cfShowBankBreakdown(d.points || []); }
        }
    });
});

// Half blue-green / half dark orange dial, shared by every Cashflow panel
// that shows a target-vs-actual metric. Needle = actual value. Target is
// user-editable (pencil icon toggles an inline input; value saves to
// cf_dashboard_targets on the second pencil click, when edit mode is
// closed). Actual has no real data source yet — 0. Same size/geometry for
// every instance; only the element id, metric_key, and unit label differ.
// cf-bank (Cash-to-Cash Cycle) removed from this list — it no longer has
// its own dial/panel; Target/Actual now show as two small colored
// squares on the group sub-heading bar instead (see
// renderCfC2cLegend/_cfDrawC2cLegend below). cf-bank's element id is now
// reused for the new standalone Current Liabilities dial in the same
// grid slot (see renderCfCurrLiabDial2).
var _cfDials = [
    { elId: 'cf-contractincome',  metricKey: 'contract_income', unit: 'Crores', reverseColors: true }
];
var _cfDialState = {}; // elId -> { target, editing }

function _cfDialSvg(target, actual, unit, unitFontSize, reverseColors, actualDisp, solidColor){
    unitFontSize = unitFontSize || 16;
    var maxV = target > 0 ? target * 2 : 1;
    var f = Math.max(0, Math.min(1, actual / maxV));
    var targetF = 0.5;
    var cx = 105, cy = 82, r = 76, sw = 15;
    function ptF(fr){ var a = Math.PI * (1 - fr); return [(cx + r * Math.cos(a)).toFixed(1), (cy - r * Math.sin(a)).toFixed(1)]; }
    function arc(f1, f2, col){
        if (f2 <= f1) return '';
        var p1 = ptF(f1), p2 = ptF(f2);
        return '<path d="M' + p1[0] + ',' + p1[1] + ' A' + r + ',' + r + ' 0 0,1 ' + p2[0] + ',' + p2[1] + '" fill="none" stroke="' + col + '" stroke-width="' + sw + '" stroke-linecap="butt"/>';
    }
    // Dark blue-grey base dial color for all metrics (no metric-specific colors)
    var dialBaseColor = '#3a4a5f';
    // Gap color: orange-red if actual < target, blue-green if actual > target
    var gapColor = (actual > target) ? '#d84a30' : '#2ba899';
    // Render base dial as single solid color
    var dialSvg = arc(0, 1, dialBaseColor);
    // Render gap arc between needle and target (only render if target > 0)
    var gapSvg = '';
    if (actual !== target && target > 0) {
        var minF = Math.min(f, targetF);
        var maxF = Math.max(f, targetF);
        gapSvg = arc(minF, maxF, gapColor);
    }
    // Target marker: bright yellow band at the dial's fixed midpoint
    var ta = Math.PI * (1 - targetF);
    var tr1 = r - sw / 2 - 3, tr2 = r + sw / 2 + 3;
    var tx1 = (cx + tr1 * Math.cos(ta)).toFixed(1), ty1 = (cy - tr1 * Math.sin(ta)).toFixed(1);
    var tx2 = (cx + tr2 * Math.cos(ta)).toFixed(1), ty2 = (cy - tr2 * Math.sin(ta)).toFixed(1);
    var targetLine = solidColor ? '' : (
        '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#000" stroke-width="8" stroke-linecap="butt"/>'
        + '<line x1="' + tx1 + '" y1="' + ty1 + '" x2="' + tx2 + '" y2="' + ty2 + '" stroke="#ffff00" stroke-width="6" stroke-linecap="butt"/>'
    );
    var nr = r - 14, na = Math.PI * (1 - f);
    var nx = (cx + nr * Math.cos(na)).toFixed(1), ny = (cy - nr * Math.sin(na)).toFixed(1);
    var centerCol = '#000';
    var svg = '<svg width="140" height="82" viewBox="0 -10 210 108" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" style="display:block;">'
        + dialSvg
        + gapSvg
        + targetLine
        + '<line x1="' + cx + '" y1="' + cy + '" x2="' + nx + '" y2="' + ny + '" stroke="#333" stroke-width="5" stroke-linecap="round"/>'
        + '<circle cx="' + cx + '" cy="' + cy + '" r="6" fill="#555"/><circle cx="' + cx + '" cy="' + cy + '" r="2.5" fill="#dce3ef"/>'
        + '<text x="' + cx + '" y="' + (cy - 30) + '" text-anchor="middle" font-size="24" font-weight="700" fill="' + centerCol + '" font-family="Barlow Condensed,Arial">' + actualDisp + '</text>'
        + '<text x="' + cx + '" y="' + (cy - 14) + '" text-anchor="middle" font-size="14" font-weight="700" fill="#5a5a5a" font-family="Barlow Condensed,Arial">' + unit + '</text>'
        + '</svg>';
    return '<div style="flex:none;width:100%;display:flex;justify-content:center;margin-top:6px;">' + svg + '</div>';
}

function _cfDrawDial(elId, metricKey, unit, unitFontSize, reverseColors){
    var el = document.getElementById(elId);
    if (!el) return;
    el.style.justifyContent = 'center';
    if (elId === 'cf-contractincome') {
        el.style.cursor = 'pointer';
        el.title = 'Click to see per-cycle breakdown';
    }
    var state = _cfDialState[elId];
    var target = state.target, actual = state.actual || 0;
    // Target/Actual here are user-entered directly in the panel's
    // declared unit for non-money dials. For money-denominated dials (unit ===
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
        // The Target input box has no unit selector — every number typed
        // into it is always Lakhs, by fixed app-wide convention (see
        // _cfEnteredToRupees). So Lakhs is the unit the user chose by
        // using this box at all, and both Target and Actual stay in
        // Lakhs here always — never auto-converted to Crores by
        // magnitude, no matter how large the number gets.
        dispUnit = 'Lakhs';
        var divisor = 100; // targetCr/actual are Crores-scale; 1 Crore = 100 Lakhs
        targetDisp = targetCr * divisor;
        actualDisp = actual * divisor;
    }
    // Editing always types in Lakhs (the input box's fixed convention —
    // see _cfEnteredToRupees) even when the resolved display elsewhere
    // has switched to Crores, so the box is explicitly labeled "Lakhs"
    // while open — otherwise a bare "110" next to a dial now reading
    // "Crores" looks like a units mismatch instead of the same figure.
    var targetCell = state.editing
        ? '<input class="cf-dial-target-input" data-elid="' + elId + '" data-metric="' + metricKey + '" type="number" step="any" value="' + target + '" style="width:58px;font-size:15px;padding:0 3px;border:1px solid #b0bec5;border-radius:2px;"> <span style="font-size:11px;color:#888;">Lakhs</span>'
        : '<span style="font-size:15px;color:#000;font-weight:700;">' + targetDisp.toFixed(2) + '</span>';
    // Actual legend colour always matches whichever arc half the needle
    // is currently pointing into — same firstCol/secondCol split (and
    // reverseColors flag) used inside _cfDialSvg.
    var maxV = targetForSvg > 0 ? targetForSvg * 2 : 1;
    // Contract Income is a single solid colour (no orange/red half) —
    // per explicit request, since crossing target there is always good.
    var solidColor = (elId === 'cf-contractincome') ? '#00838f' : null;
    var firstCol = solidColor || (reverseColors ? '#b5540a' : '#00838f');
    var secondCol = solidColor || (reverseColors ? '#00838f' : '#b5540a');
    var needleCol = (actual / maxV) <= 0.5 ? firstCol : secondCol;
    // Center unit label (in the dial itself) is the single place the
    // Lakhs/Crores unit shows for money dials — Target/Actual below
    // display bare numbers with no unit suffix, to avoid repeating it.
    // The dial's own center now also shows the Actual value itself
    // (above the unit label), same two-line pattern used by the other
    // Cashflow dials (Bills Receivable, Free Cash, etc.).
    var actualCell = '<span style="font-size:15px;color:' + needleCol + ';font-weight:700;">' + actualDisp.toFixed(2) + '</span>';
    var actualBlock = '<span style="font-size:15px;color:#111;">Actual: ' + actualCell + '</span>';
    var targetBlock = '<span style="font-size:15px;color:#111;">Target: ' + targetCell + ' <button class="cf-dial-target-pencil" data-elid="' + elId + '" title="Edit target" style="background:none;border:none;cursor:pointer;padding:0 2px;font-size:12px;">&#9998;</button></span>';
    // Actual always shown on the left, Target on the right — same
    // convention as every other Cashflow dial.
    var legendRow = actualBlock + targetBlock;
    el.innerHTML = '<div style="width:100%;height:2px;background:#fff;border-top:1px solid #d8dde6;flex:none;"></div>'
        + _cfDialSvg(targetForSvg, actual, dispUnit, unitFontSize, reverseColors, actualDisp.toFixed(2), solidColor)
        + '<div style="display:flex;justify-content:space-between;align-items:center;font-family:Barlow Condensed,Arial;padding:0 8px;width:100%;box-sizing:border-box;">'
        + legendRow
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
        bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-c2c-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-c2c-rs').each(function(){
            var dir = $(this).data('dir');
            bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        bindDragTouch(document, 'mousemove', onMove);
        bindDragTouch(document, 'mouseup', onUp);
    }
}
// #cf-bank now hosts the standalone Current Liabilities dial (see
// renderCfCurrLiabDial2) — clicking it opens the same account-level
// breakdown popup the old #cf-currentliabilities dial used.
$(document).on('click', '#cf-bank', function(e){
    $.ajax({
        type: 'POST', url: '../projectsmain/cfcurrentliabilitiesbreakdown', dataType: 'json',
        success: function(d){
            if (d && d.error === 'No') { _cfShowCurrLiabBreakdown(d.accounts || []); }
        }
    });
});
// Current Assets popup: the same account list behind the Current Assets figure.
$(document).on('click', '#cf-curassets', function(){
    $.ajax({
        type: 'POST', url: '../projectsmain/cfcurrentassetsdiff', dataType: 'json',
        success: function(d){
            if (d && d.error === 'No') { _cfShowCurrLiabBreakdown(d.accounts || [], 'Current Assets = Advances and Deposits + the heads booked beyond it'); }
        }
    });
});
$(document).on('click', '#cf-c2c-breakdown-close', function(){ $('#cf-c2c-breakdown-win').remove(); });

// Contract Income breakdown popup — a PAIRED/grouped bar chart, two
// bars per Contract Income credit: LEFT bar = that cycle's own standalone
// billed amount (cycle_amount), RIGHT bar = the cumulative running total
// through that cycle (amount) — see actionCfcontractincomebreakdown.
// Both bars share one value axis/unit, scaled off the larger of the two
// series (the cumulative series, since it only grows). Labelled by the
// month/year each bill was credited.
function _cfContractIncomeGroupedChartHtml(accounts, chartH){
    var maxAmt = 0;
    for (var i = 0; i < accounts.length; i++) {
        if (accounts[i].amount > maxAmt) maxAmt = accounts[i].amount;
        if (accounts[i].cycle_amount > maxAmt) maxAmt = accounts[i].cycle_amount;
    }
    if (maxAmt <= 0) maxAmt = 1;
    var fmtMax = _cfFmtMoney(maxAmt);
    var axisMax = fmtMax.value;
    var unit = fmtMax.unit;
    var divisor = unit === 'Crores' ? 10000000 : 100000;
    var niceMax = axisMax <= 0 ? 1 : Math.ceil(axisMax * 1.05 * 100) / 100;
    var axisW = 34;

    var stepCount = 5;
    var stepPx = chartH / stepCount;
    var gridBg = 'repeating-linear-gradient(to top, #aab2c0 0, #aab2c0 1px, transparent 1px, transparent ' + stepPx.toFixed(2) + 'px)';
    var gridLabelsHtml = '';
    for (var g = 0; g <= stepCount; g++) {
        var gv = Math.round((niceMax / stepCount) * g * 100) / 100;
        var gy = chartH - g * stepPx;
        gridLabelsHtml += '<div style="position:absolute;left:-' + (axisW + 4) + 'px;top:' + (gy - 7).toFixed(1) + 'px;width:' + axisW + 'px;text-align:right;font-size:10px;color:#8a94a3;font-family:Barlow Condensed,Arial;">' + gv.toFixed(2) + '</div>';
    }

    var cycleCol = '#0d3f7a', cumCol = '#b5540a';
    var barsHtml = '';
    for (var j = 0; j < accounts.length; j++) {
        var acct = accounts[j];
        var cycleVal = (+acct.cycle_amount || 0) / divisor;
        var cumVal = (+acct.amount || 0) / divisor;
        var cyclePx = Math.max(2, (cycleVal / niceMax) * chartH);
        var cumPx = Math.max(2, (cumVal / niceMax) * chartH);
        barsHtml += '<div style="display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:' + chartH + 'px;flex:1 1 0;min-width:0;">'
            + '<div style="display:flex;align-items:flex-end;justify-content:center;width:100%;position:relative;height:' + Math.max(cyclePx, cumPx).toFixed(1) + 'px;">'
            + '<div style="position:absolute;bottom:' + (cyclePx + 2).toFixed(1) + 'px;right:22px;font-size:10px;font-weight:700;color:' + cycleCol + ';white-space:nowrap;">' + cycleVal.toFixed(2) + '</div>'
            + '<div style="width:22px;height:' + cyclePx.toFixed(1) + 'px;background:' + cycleCol + ';border-radius:2px 0 0 0;"></div>'
            + '<div style="position:absolute;bottom:' + (cumPx + 2).toFixed(1) + 'px;left:22px;font-size:10px;font-weight:700;color:' + cumCol + ';white-space:nowrap;">' + cumVal.toFixed(2) + '</div>'
            + '<div style="width:22px;height:' + cumPx.toFixed(1) + 'px;background:' + cumCol + ';border-radius:0 2px 0 0;"></div>'
            + '</div>'
            + '</div>';
    }
    var labelsHtml = '';
    for (var k = 0; k < accounts.length; k++) {
        labelsHtml += '<div style="flex:1 1 0;min-width:0;font-size:11px;color:#333;text-align:center;line-height:1.2;padding:0 3px;box-sizing:border-box;overflow:hidden;text-overflow:ellipsis;">' + accounts[k].name + '</div>';
    }

    var emptyMsgHtml = accounts.length ? '' : '<div style="position:absolute;left:0;right:0;top:50%;transform:translateY(-50%);text-align:center;color:#8a94a3;font-size:12px;font-family:Barlow Condensed,Arial;">No data yet</div>';
    var body = '<div style="display:flex;margin-left:' + (axisW + 4) + 'px;min-width:0;box-sizing:border-box;">'
        + '<div style="position:relative;flex:1;min-width:0;display:flex;align-items:flex-end;gap:6px;height:' + chartH + 'px;background-image:' + gridBg + ';">'
        + gridLabelsHtml
        + barsHtml
        + emptyMsgHtml
        + '</div>'
        + '</div>'
        + '<div style="margin-left:' + (axisW + 4) + 'px;border-top:2px solid #251840;"></div>'
        + '<div style="display:flex;margin-left:' + (axisW + 4) + 'px;min-width:0;box-sizing:border-box;gap:6px;margin-top:6px;">' + labelsHtml + '</div>'
        + '<div style="display:flex;gap:16px;justify-content:center;margin-top:10px;font-size:11px;font-family:Barlow Condensed,Arial;">'
        + '<span><span style="display:inline-block;width:10px;height:10px;background:' + cycleCol + ';border-radius:2px;vertical-align:middle;margin-right:4px;"></span>This bill</span>'
        + '<span><span style="display:inline-block;width:10px;height:10px;background:' + cumCol + ';border-radius:2px;vertical-align:middle;margin-right:4px;"></span>Cumulative</span>'
        + '</div>';
    return { unit: unit, body: body };
}

function _cfRedrawContractIncomeBreakdown(){
    var s = window._cfContractIncomeBreakdownState;
    if (!s) return;
    var chartH = Math.max(140, $('#cf-ci-breakdown-win').height() - 160);
    var r = _cfContractIncomeGroupedChartHtml(s.accounts, chartH);
    var cumFmt = _cfFmtMoney(s.cumulative);
    var cumDisp = _cfIndianComma(s.cumulative / (cumFmt.unit === 'Crores' ? 10000000 : 100000)) + ' ' + cumFmt.unit;
    $('#cf-ci-breakdown-unit').html(
        '<span style="font-weight:700;color:#251840;">Amount in ' + r.unit + '</span>'
        + '<span style="float:right;font-weight:700;color:#b5540a;">Cumulative: ' + cumDisp + '</span>'
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
        bindDragTouch(hdr, 'mousedown', function(e){ if (e.target.id !== 'cf-ci-breakdown-close') onDown(e, 'drag'); });
        $(win).find('.cf-ci-rs').each(function(){
            var dir = $(this).data('dir');
            bindDragTouch(this, 'mousedown', function(e){ onDown(e, dir); });
        });
        bindDragTouch(document, 'mousemove', onMove);
        bindDragTouch(document, 'mouseup', onUp);
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
        win.dataset.userSized = '1'; /* stop popupFitPortrait from overriding a size the user chose */
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
