<?php
$this->title = 'Progress Report';
?>
<style>
:root{
  --bg:#f4f5f7;--card:#ffffff;--card2:#eef0f3;--border:#d1d5db;
  --text:#1e293b;--muted:#64748b;--label:#94a3b8;
  --green:#16a34a;--red:#dc2626;--accent:#1e3a5f;--amber:#d97706;
  --slate:#334155;--slate2:#475569;--slate-text:#f1f5f9;
}
*{box-sizing:border-box;-webkit-tap-highlight-color:transparent}
body{background:var(--bg)}

#mob-app{display:flex;flex-direction:column;height:100vh;overflow:hidden;background:var(--bg)}

/* ── Top bar ── */
#mob-topbar{
  background:var(--slate);
  padding:env(safe-area-inset-top,0px) 16px 12px;
  padding-top:calc(env(safe-area-inset-top,0px) + 14px);
  flex-shrink:0;
  display:flex;align-items:center;gap:12px;
  border-bottom:1px solid rgba(0,0,0,.15);
}
#mob-topbar .mob-back{background:none;border:none;color:var(--slate-text);font-size:22px;line-height:1;cursor:pointer;padding:4px;display:none}
#mob-topbar .mob-title{flex:1;color:var(--slate-text);font-size:17px;font-weight:700;letter-spacing:.2px}
#mob-topbar .mob-proj{font-size:11px;color:rgba(241,245,249,.65);margin-top:3px}
#mob-date-bar{background:var(--slate);padding:0 16px 12px;flex-shrink:0;display:flex;align-items:center;gap:10px;border-bottom:1px solid rgba(0,0,0,.15)}
#mob-date-bar label{color:rgba(241,245,249,.65);font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
#mob-report-date{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:var(--slate-text);border-radius:8px;padding:7px 12px;font-size:13px;font-family:'Inter',sans-serif;flex:1}
#mob-report-date::-webkit-calendar-picker-indicator{filter:invert(1);opacity:.7}

/* ── Tab strip ── */
#mob-tabs{background:var(--slate);display:flex;flex-shrink:0;border-bottom:1px solid rgba(0,0,0,.15)}
.mob-tab{flex:1;background:none;border:none;padding:11px 8px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:rgba(241,245,249,.55);cursor:pointer;border-bottom:3px solid transparent}
.mob-tab.active{color:var(--slate-text);border-bottom-color:var(--accent)}

/* ── Scrollable content ── */
#mob-content-progress,#mob-content-mbook{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding:12px 12px 80px;background:var(--bg)}
#mob-content-mbook{display:none}

/* ── IOW group header ── */
.mob-iow-hdr{
  background:var(--slate);color:var(--slate-text);
  font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;
  padding:7px 14px;border-radius:8px;margin:14px 0 6px;
}
.mob-iow-hdr:first-child{margin-top:0}

/* ── Activity card ── */
.mob-act-card{
  background:var(--card);border-radius:12px;
  border:1px solid var(--border);
  margin-bottom:8px;
  box-shadow:0 1px 3px rgba(0,0,0,.06);
}
.mob-act-card.completed{border-left:4px solid var(--green)}
.mob-act-card.active{border-left:4px solid var(--accent)}

.mob-act-top{padding:13px 14px;display:flex;align-items:flex-start;gap:10px;cursor:pointer}
.mob-act-name{flex:1;font-size:14px;font-weight:600;color:var(--text);line-height:1.35}
.mob-act-meta{display:flex;gap:12px;margin-top:5px;flex-wrap:wrap}
.mob-act-meta span{font-size:11px;color:var(--muted)}
.mob-act-meta strong{color:var(--text)}
.mob-act-badge{flex-shrink:0;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;align-self:flex-start;margin-top:2px}
.mob-act-badge.done{background:rgba(22,163,74,.1);color:var(--green)}
.mob-act-badge.active{background:rgba(30,58,95,.1);color:var(--accent)}

/* ── Report form (expands inside card, slightly darker) ── */
.mob-act-form{display:none;padding:0 14px 14px;border-top:1px solid var(--border);background:var(--card2)}
.mob-act-form.open{display:block}
.mob-form-row{display:grid;grid-template-columns:1fr 1fr;column-gap:16px;row-gap:10px;margin-top:10px}
.mob-form-row-date{grid-template-columns:138px 138px;column-gap:28px}
.mob-form-field{display:flex;flex-direction:column;min-width:0}
.mob-form-field.full{grid-column:1/-1}
.mob-form-field label{font-size:10px;font-weight:700;color:var(--slate2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px}
.mob-form-field input,.mob-form-field select{
  padding:10px 12px;font-size:14px;color:var(--text);
  border:1.5px solid var(--border);border-radius:8px;
  background:#fff;font-family:'Inter',sans-serif;
  outline:none;width:100%;min-width:0;box-sizing:border-box;transition:border-color .15s;
}
.mob-form-field input:focus,.mob-form-field select:focus{border-color:var(--accent)}
.mob-form-field input[readonly]{background:var(--bg);color:var(--muted)}
.mob-form-field input.sd-locked{background:var(--bg);color:var(--muted);pointer-events:none;border-color:var(--border)}
.mob-cum-info{font-size:11px;color:var(--muted);margin-top:8px;padding:8px 12px;background:#fff;border-radius:8px;border:1px solid var(--border)}
.mob-cum-info strong{color:var(--text)}

.mob-form-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px}
.mob-btn{flex:1;min-width:100px;padding:12px;border:none;border-radius:10px;font-size:14px;font-weight:600;font-family:'Inter',sans-serif;cursor:pointer;transition:opacity .15s}
.mob-btn:active{opacity:.8}
.mob-btn-report{background:#166534;color:#fff}
.mob-btn-cancel{background:#ea580c;color:#fff}
.mob-btn-complete{background:var(--green);color:#fff;font-size:12px}
.mob-btn-resource{background:var(--accent);color:#fff;flex-basis:100%}

/* ── Loading / empty ── */
#mob-loading{text-align:center;padding:50px 20px;color:var(--muted);font-size:14px}
.mob-spinner{width:36px;height:36px;border:3px solid var(--border);border-top-color:var(--accent);border-radius:50%;animation:spin .8s linear infinite;margin:0 auto 14px}
@keyframes spin{to{transform:rotate(360deg)}}

/* ── Toast ── */
#mob-toast{
  position:fixed;bottom:calc(env(safe-area-inset-bottom,0px) + 20px);left:50%;transform:translateX(-50%);
  background:var(--slate);color:var(--slate-text);padding:10px 22px;border-radius:24px;
  font-size:13px;font-weight:600;white-space:nowrap;
  opacity:0;transition:opacity .25s;pointer-events:none;z-index:999;
  box-shadow:0 4px 12px rgba(0,0,0,.2);
}
#mob-toast.show{opacity:1}

/* ── Custom date picker ── */
.cdp-wrap{position:relative}
.cdp-display{
  padding:10px 12px;font-size:14px;color:var(--text);
  border:1.5px solid var(--border);border-radius:8px;
  background:#fff;font-family:'Inter',sans-serif;
  width:100%;box-sizing:border-box;cursor:pointer;
  display:flex;align-items:center;justify-content:space-between;
}
.cdp-display span{pointer-events:none}
.cdp-panel{
  position:fixed;z-index:9999;
  background:#fff;border:1.5px solid var(--border);border-radius:10px;
  box-shadow:0 6px 20px rgba(0,0,0,.14);padding:10px;width:250px;display:none;
}
.cdp-panel.open{display:block}
.cdp-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.cdp-head button{background:none;border:none;font-size:17px;cursor:pointer;color:var(--accent);padding:2px 6px;line-height:1}
.cdp-head span{font-size:13px;font-weight:700;color:var(--text)}
.cdp-days-hdr{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;margin-bottom:4px}
.cdp-days-hdr span{font-size:9px;font-weight:700;color:var(--muted);text-align:center;text-transform:uppercase}
.cdp-days{display:grid;grid-template-columns:repeat(7,1fr);gap:2px}
.cdp-day{
  aspect-ratio:1;display:flex;align-items:center;justify-content:center;
  font-size:12px;border-radius:6px;cursor:pointer;color:var(--text);
}
.cdp-day:hover{background:var(--bg)}
.cdp-day.selected{background:var(--accent);color:#fff;font-weight:700}
.cdp-day.empty{cursor:default}

/* ── Edit mode banner ── */
.mob-edit-banner{
  background:rgba(217,119,6,.12);border:1px solid var(--amber);
  border-radius:8px;padding:7px 12px;margin-top:10px;
  font-size:11px;font-weight:600;color:var(--amber);display:none;
}
.mob-edit-banner.show{display:block}

/* ── Resource sheet (slide-up, full-screen) ── */
#res-sheet-bk{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1200}
#res-sheet-bk.show{display:block}
#res-sheet{
  display:flex;flex-direction:column;
  position:fixed;left:0;right:0;bottom:0;
  height:88vh;max-height:88vh;
  background:#fff;border-radius:16px 16px 0 0;
  box-shadow:0 -4px 24px rgba(0,0,0,.25);
  z-index:1201;
  transform:translateY(100%);transition:transform .25s ease;
}
#res-sheet.show{transform:translateY(0)}
#res-sheet-hdr{
  flex-shrink:0;padding:14px 16px 12px;border-bottom:1px solid var(--border);
  display:flex;align-items:flex-start;justify-content:space-between;gap:10px;
}
#res-sheet-hdr .rs-titles{min-width:0}
#res-sheet-hdr .rs-title{font-size:15px;font-weight:700;color:var(--text)}
#res-sheet-hdr .rs-sub{font-size:12px;color:var(--muted);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
#res-sheet-close{background:none;border:none;font-size:24px;line-height:1;color:var(--muted);cursor:pointer;padding:0 4px;flex-shrink:0}
#res-sheet-body{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding:8px 12px}
#res-sheet-colhdr{
  display:flex;align-items:center;gap:10px;
  padding:0 4px 8px;position:sticky;top:0;background:#fff;z-index:1;
  font-size:11px;font-weight:700;color:var(--slate2);
  text-transform:uppercase;letter-spacing:.5px;
  border-bottom:1.5px solid var(--border);
}
.res-row{
  display:flex;align-items:center;gap:10px;
  padding:12px 4px;border-bottom:1px solid var(--card2);
}
.res-row-info{flex:1;min-width:0}
.res-row-type{font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px}
.res-row-name{font-size:14px;font-weight:600;color:var(--text);margin-top:2px}
.res-row-unit{font-size:11px;color:var(--muted)}
.res-row-qty{width:90px;flex-shrink:0}
.res-row-qty input{
  width:100%;padding:9px 10px;font-size:14px;text-align:right;
  border:1.5px solid var(--border);border-radius:8px;font-family:'Inter',sans-serif;
  box-sizing:border-box;outline:none;
}
.res-row-qty input:focus{border-color:var(--accent)}
#res-sheet-empty{text-align:center;color:var(--muted);font-size:13px;padding:40px 20px}
#res-sheet-actions{
  flex-shrink:0;display:flex;gap:8px;padding:12px 16px;
  padding-bottom:calc(env(safe-area-inset-bottom,0px) + 12px);
  border-top:1px solid var(--border);
}

/* ── Bottom nav ── */
#mob-bottom-nav{
  position:fixed;bottom:0;left:0;right:0;
  background:var(--slate);border-top:1px solid rgba(0,0,0,.15);
  padding:8px 0 calc(env(safe-area-inset-bottom,0px) + 6px);
  display:flex;justify-content:space-around;z-index:100;flex-shrink:0;
}
.mob-nav-item{display:flex;flex-direction:column;align-items:center;gap:2px;font-size:9px;font-weight:600;color:rgba(241,245,249,.55);text-transform:uppercase;letter-spacing:.4px;cursor:pointer;padding:0 12px;text-decoration:none}
.mob-nav-item.active{color:var(--slate-text)}
.mob-nav-icon{font-size:20px;line-height:1}
</style>

<div id="mob-app">

  <!-- Top bar -->
  <div id="mob-topbar">
    <button class="mob-back" id="mob-back-btn">&#8592;</button>
    <div>
      <div class="mob-title">Progress &amp; M.Book</div>
      <div class="mob-proj"><?= htmlspecialchars($projectName) ?></div>
    </div>
  </div>

  <!-- Tab strip -->
  <div id="mob-tabs">
    <button type="button" class="mob-tab<?= $initialTab === 'progress' ? ' active' : '' ?>" data-tab="progress">Progress</button>
    <button type="button" class="mob-tab<?= $initialTab === 'mbook' ? ' active' : '' ?>" data-tab="mbook">M.Book</button>
  </div>

  <!-- Date bar (Progress tab only) -->
  <div id="mob-date-bar" style="<?= $initialTab === 'mbook' ? 'display:none' : '' ?>">
    <label>Report Date</label>
    <input type="date" id="mob-report-date" value="<?= date('Y-m-d') ?>">
  </div>

  <!-- Progress Report pane -->
  <div id="mob-content-progress" style="<?= $initialTab === 'mbook' ? 'display:none' : '' ?>">
    <div id="mob-loading">
      <div class="mob-spinner"></div>
      Loading activities…
    </div>
  </div>

  <!-- M.Book pane -->
  <div id="mob-content-mbook" style="<?= $initialTab === 'mbook' ? 'display:block' : '' ?>">
    <div id="mbk-loading" class="mob-mbk-loading">
      <div class="mob-spinner"></div>
      Loading work orders…
    </div>
  </div>

</div>

<!-- Toast -->
<div id="mob-toast"></div>

<!-- Calendar portal (fixed, outside all scroll/overflow containers) -->
<div class="cdp-panel" id="cdp-portal"></div>

<!-- Resource sheet (slide-up, full-screen) -->
<div id="res-sheet-bk"></div>
<div id="res-sheet">
  <div id="res-sheet-hdr">
    <div class="rs-titles">
      <div class="rs-title">Report Resources at Site</div>
      <div class="rs-sub" id="res-sheet-actname"></div>
    </div>
    <button id="res-sheet-close" type="button">&times;</button>
  </div>
  <div id="res-sheet-body">
    <div id="res-sheet-empty" style="display:none">Loading&hellip;</div>
    <div id="res-sheet-colhdr" style="display:none">
      <div class="res-row-info">Resources</div>
      <div class="res-row-qty">Nos</div>
    </div>
    <div id="res-sheet-rows"></div>
  </div>
  <div id="res-sheet-actions">
    <button class="mob-btn" id="res-sheet-cancel" type="button" style="background:#ea580c;color:#fff">Cancel</button>
    <button class="mob-btn" id="res-sheet-save" type="button" style="background:#166534;color:#fff">Save</button>
  </div>
</div>

<!-- Bottom nav -->
<div id="mob-bottom-nav">
  <a class="mob-nav-item active" href="<?= Yii::$app->urlManager->createUrl('report/mobile') ?>">
    <span class="mob-nav-icon">&#128203;</span>Report
  </a>
  <a class="mob-nav-item" href="<?= Yii::$app->urlManager->createUrl('projectsmain/index') ?>">
    <span class="mob-nav-icon">&#127968;</span>Home
  </a>
  <a class="mob-nav-item" href="<?= Yii::$app->urlManager->createUrl('site/logout') ?>" data-method="post">
    <span class="mob-nav-icon">&#128275;</span>Logout
  </a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?= $this->render('_mobile_mbook_pane') ?>

<script>
(function(){
'use strict';

var _openActId = null;

function toast(msg, dur){
  var t = document.getElementById('mob-toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(function(){ t.classList.remove('show'); }, dur||2000);
}

function fmtDate(ymd){
  // yyyy-mm-dd → dd-mm-yyyy for API
  var p = ymd.split('-');
  return p[2]+'-'+p[1]+'-'+p[0];
}

function loadActivities(){
  // #cdp-portal lives outside #mob-content and survives the reload below —
  // close it so it can't stay open floating over a stale position once the
  // card it was anchored to is gone.
  closeCdpPortal();
  var date = fmtDate(document.getElementById('mob-report-date').value);
  $('#mob-content-progress').html('<div id="mob-loading"><div class="mob-spinner"></div>Loading…</div>');

  $.ajax({
    type:'POST', url:'../report/scheduleprogressactivities',
    dataType:'json', data:{dateselect: date},
    success: function(d){
      if(d.error !== 'No'){ $('#mob-content-progress').html('<div id="mob-loading">No activities found.</div>'); return; }
      renderActivities(d);
    },
    error: function(){ $('#mob-content-progress').html('<div id="mob-loading">Failed to load. Please refresh.</div>'); }
  });
}

function renderActivities(d){
  // Parse the HTML returned by the server to extract activity data
  // We rebuild a mobile-native card list instead of rendering the desktop HTML
  $.ajax({
    type:'POST', url:'../report/mobileactivities',
    dataType:'json', data:{dateselect: fmtDate(document.getElementById('mob-report-date').value)},
    success: function(r){
      if(!r || !r.activities || !r.activities.length){
        $('#mob-content-progress').html('<div id="mob-loading">No activities found for this project.</div>');
        return;
      }
      buildCards(r.activities);
    },
    error: function(){
      $('#mob-content-progress').html('<div id="mob-loading">No activities found for this project.</div>');
    }
  });
}

function buildCards(activities){
  var html = '';
  var lastIow = null;

  activities.forEach(function(act){
    if(act.iow !== lastIow){
      html += '<div class="mob-iow-hdr">'+escHtml(act.iow)+'</div>';
      lastIow = act.iow;
    }
    var completed = act.completed == 1;
    var badge = completed
      ? '<span class="mob-act-badge done">Done</span>'
      : '<span class="mob-act-badge active">Active</span>';
    var cardClass = completed ? 'completed' : 'active';
    var cumQty = act.cum_qty ? act.cum_qty : '—';
    var lastDate = act.last_date ? act.last_date : '—';
    var startVal = act.start_date || '';

    html += '<div class="mob-act-card '+cardClass+'" data-id="'+act.id+'">';
    html += '<div class="mob-act-top" data-id="'+act.id+'">';
    html += '<div><div class="mob-act-name">'+escHtml(act.name)+'</div>';
    html += '<div class="mob-act-meta">';
    html += '<span>B.Qty: <strong>'+escHtml(String(act.b_qty||'—'))+' '+escHtml(act.unit||'')+'</strong></span>';
    html += '<span>Last Reported: <strong>'+escHtml(String(lastDate))+'</strong></span>';
    html += '<span>Last Reported Qty: <strong>'+escHtml(String(cumQty))+' '+escHtml(act.unit||'')+'</strong></span>';
    html += '</div></div>';
    html += badge;
    html += '</div>'; // mob-act-top

    html += '<div class="mob-act-form" id="form-'+act.id+'">';
    html += '<input type="hidden" id="flogid-'+act.id+'" value="">';
    html += '<div class="mob-edit-banner" id="fedit-'+act.id+'">✏️ Editing previously submitted report — changes will recalculate all totals.</div>';

    // ── Entry fields ──
    var topDate = document.getElementById('mob-report-date').value;
    html += '<div class="mob-form-row mob-form-row-date">';
    html += '<div class="mob-form-field"><label>Activity Start Date</label><input type="date" id="fsd-'+act.id+'" value="'+escHtml(startVal)+'"></div>';
    html += '<div class="mob-form-field"><label>Report Date</label>';
    html += '<div class="cdp-wrap" id="cdpwrap-'+act.id+'">';
    html += '<div class="cdp-display" id="cdpdisp-'+act.id+'">'+fmtDisplayDate(topDate)+'<span style="font-size:12px;color:var(--muted)">▾</span></div>';
    html += '<input type="hidden" id="frd-'+act.id+'" class="frd-input" value="'+topDate+'">';
    html += '</div></div>';
    html += '</div>';
    html += '<div class="mob-form-row">';
    html += '<div class="mob-form-field"><label>Break Hours</label><input type="number" id="fbd-'+act.id+'" placeholder="0" step="0.5" min="0" inputmode="decimal"></div>';
    html += '<div class="mob-form-field"><label>Unit</label><input type="text" value="'+escHtml(act.unit||'—')+'" readonly></div>';
    html += '</div>';
    html += '<div class="mob-form-row">';
    html += '<div class="mob-form-field"><label>Current Qty</label><input type="number" id="fqty-'+act.id+'" data-cum="'+escHtml(String(cumQty||0))+'" data-orig-cum="'+escHtml(String(cumQty||0))+'" placeholder="0.00" step="0.001" inputmode="decimal" class="fqty-input"></div>';
    html += '<div class="mob-form-field"><label>Up-to-date Qty</label><input type="text" id="fupd-'+act.id+'" data-unit="'+escHtml(act.unit||'')+'" value="'+escHtml(String(cumQty||0))+' '+escHtml(act.unit||'')+'" readonly></div>';
    html += '</div>';

    html += '<div class="mob-form-actions">';
    html += '<button class="mob-btn mob-btn-resource" data-id="'+act.id+'" data-name="'+escHtml(act.name)+'">Report Resource</button>';
    html += '<button class="mob-btn mob-btn-cancel" data-id="'+act.id+'">Cancel</button>';
    html += '<button class="mob-btn mob-btn-report" data-id="'+act.id+'">Submit Report</button>';
    html += '</div>';
    html += '</div>'; // mob-act-form

    html += '</div>'; // mob-act-card
  });

  $('#mob-content-progress').html(html);
}

function escHtml(s){
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

var MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];

function fmtDisplayDate(ymd){
  if(!ymd) return '—';
  var p = ymd.split('-');
  return p[2]+' '+MONTHS[parseInt(p[1],10)-1].slice(0,3)+' '+p[0];
}

var _cdpActiveId = null;
var cdpState = {};

function renderCdp(id){
  var hidden = document.getElementById('frd-'+id);
  var selected = hidden ? hidden.value : '';
  var state = cdpState[id] || (function(){
    var d = selected ? new Date(selected) : new Date();
    cdpState[id] = {year: d.getFullYear(), month: d.getMonth()};
    return cdpState[id];
  })();

  var y = state.year, m = state.month;
  var first = new Date(y, m, 1).getDay();
  var daysInMonth = new Date(y, m+1, 0).getDate();

  var h = '<div class="cdp-head">';
  h += '<button type="button" data-cdp-prev="'+id+'">&#8249;</button>';
  h += '<span>'+MONTHS[m]+' '+y+'</span>';
  h += '<button type="button" data-cdp-next="'+id+'">&#8250;</button>';
  h += '</div>';
  h += '<div class="cdp-days-hdr"><span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span></div>';
  h += '<div class="cdp-days">';
  for(var i=0;i<first;i++) h += '<div class="cdp-day empty"></div>';
  for(var d=1;d<=daysInMonth;d++){
    var ymd = y+'-'+(m+1<10?'0':'')+(m+1)+'-'+(d<10?'0':'')+d;
    var sel = (ymd === selected) ? ' selected' : '';
    h += '<div class="cdp-day'+sel+'" data-cdp-date="'+ymd+'" data-cdp-id="'+id+'">'+d+'</div>';
  }
  h += '</div>';
  var portal = document.getElementById('cdp-portal');
  portal.innerHTML = h;
  portal.dataset.cdpId = id;
}

function cdpOpen(id){
  var portal = document.getElementById('cdp-portal');
  var disp   = document.getElementById('cdpdisp-'+id);
  if(!disp) return;

  var isOpen = portal.classList.contains('open') && portal.dataset.cdpId == id;
  portal.classList.remove('open');
  if(isOpen){ _cdpActiveId = null; return; }

  _cdpActiveId = id;
  renderCdp(id);

  // Position below the display button; flip upward if not enough space below
  var rect     = disp.getBoundingClientRect();
  var panelH   = 270;
  var spaceBelow = window.innerHeight - rect.bottom;
  var top = spaceBelow >= panelH ? rect.bottom + 4 : rect.top - panelH - 4;
  var left = Math.min(rect.left, window.innerWidth - 260);
  portal.style.top  = top + 'px';
  portal.style.left = left + 'px';
  portal.classList.add('open');
}

$(document).on('click','.cdp-display',function(){
  var id = this.id.replace('cdpdisp-','');
  cdpOpen(id);
});

$(document).on('click','[data-cdp-prev]',function(e){
  e.stopPropagation();
  var id = $(this).data('cdp-prev');
  if(!cdpState[id]) cdpState[id] = {year:new Date().getFullYear(), month:new Date().getMonth()};
  cdpState[id].month--;
  if(cdpState[id].month < 0){ cdpState[id].month=11; cdpState[id].year--; }
  renderCdp(id);
});

$(document).on('click','[data-cdp-next]',function(e){
  e.stopPropagation();
  var id = $(this).data('cdp-next');
  if(!cdpState[id]) cdpState[id] = {year:new Date().getFullYear(), month:new Date().getMonth()};
  cdpState[id].month++;
  if(cdpState[id].month > 11){ cdpState[id].month=0; cdpState[id].year++; }
  renderCdp(id);
});

$(document).on('click','[data-cdp-date]',function(e){
  e.stopPropagation();
  var ymd = $(this).data('cdp-date');
  var id  = $(this).data('cdp-id');
  document.getElementById('frd-'+id).value = ymd;
  var disp = document.getElementById('cdpdisp-'+id);
  disp.innerHTML = fmtDisplayDate(ymd) + '<span style="font-size:12px;color:var(--muted)">▾</span>';
  document.getElementById('cdp-portal').classList.remove('open');
  _cdpActiveId = null;
  $('#frd-'+id).trigger('change');
});

// close portal when tapping outside
$(document).on('click',function(e){
  if(!$(e.target).closest('.cdp-wrap, #cdp-portal').length){
    document.getElementById('cdp-portal').classList.remove('open');
    _cdpActiveId = null;
  }
});

function checkExistingReport(id, reportDate) {
  var $qty  = $('#fqty-'+id);
  var $upd  = $('#fupd-'+id);
  var $fsd  = $('#fsd-'+id);
  var unit  = $upd.data('unit') || '';
  var cum   = parseFloat($qty.data('cum')) || 0;

  // reset to new-report state
  $('#fedit-'+id).removeClass('show');
  $qty.val('');
  $('#fbd-'+id).val('');
  $('.mob-btn-report[data-id="'+id+'"]').text('Submit Report');
  $upd.val(cum.toFixed(3) + (unit ? ' '+unit : ''));

  $.ajax({
    type:'POST', url:'../report/getreportbydate',
    dataType:'json',
    data:{ actid: id, report_date: reportDate },
    success: function(r){
      // Lock/unlock start date based on whether report date is after the current start date
      var currentStartDate = $fsd.val();
      if(currentStartDate && reportDate > currentStartDate){
        // report date is after start date — start date is correct, lock it
        $fsd.addClass('sd-locked').removeAttr('type').attr('type','text');
      } else {
        // report date is on or before start date — allow correction
        $fsd.removeClass('sd-locked').removeAttr('type').attr('type','date');
      }

      if(r.found){
        var editBase = Math.max(0, cum - r.currentqty);
        $qty.data('cum', editBase);
        $qty.val(r.currentqty);
        $qty.trigger('input');
        $('#fbd-'+id).val(r.break_hour > 0 ? r.break_hour : '');
        if(r.start_date) $fsd.val(r.start_date);
        $('#fedit-'+id).addClass('show');
        $('.mob-btn-report[data-id="'+id+'"]').text('Update Report');
      } else {
        $qty.data('cum', cum);
      }
    }
  });
}

function closeCdpPortal(){
  document.getElementById('cdp-portal').classList.remove('open');
  _cdpActiveId = null;
}

// Toggle form open/close on card tap
$(document).on('click','.mob-act-top', function(){
  // The calendar portal is a single shared element repositioned per-card
  // (see cdpOpen) — switching cards without closing it first leaves it
  // floating at its old position, appearing stuck and unresponsive over
  // whichever card is now open.
  closeCdpPortal();
  var id = $(this).data('id');
  var $form = $('#form-'+id);
  if($form.hasClass('open')){
    $form.removeClass('open');
    _openActId = null;
  } else {
    $('.mob-act-form.open').removeClass('open');
    $form.addClass('open');
    _openActId = id;
    var reportDate = document.getElementById('mob-report-date').value;
    $('#frd-'+id).val(reportDate);
    checkExistingReport(id, reportDate);
    // scroll card into view
    setTimeout(function(){
      var card = document.querySelector('.mob-act-card[data-id="'+id+'"]');
      if(card) card.scrollIntoView({behavior:'smooth', block:'nearest'});
    },200);
  }
});

// When user changes the date inside an open form, re-check for existing report
$(document).on('change','.frd-input', function(){
  var id = this.id.replace('frd-','');
  checkExistingReport(id, this.value);
});

// Cancel
$(document).on('click','.mob-btn-cancel',function(){
  closeCdpPortal();
  var id = $(this).data('id');
  $('#form-'+id).removeClass('open');
  _openActId = null;
});

// ── Report Resources at Site (slide-up sheet) ───────────────────────────
var _resSheetActId = null;

function resSheetOpen(id, name){
  _resSheetActId = id;
  $('#res-sheet-actname').text(name || '');
  $('#res-sheet-rows').empty();
  $('#res-sheet-colhdr').hide();
  $('#res-sheet-empty').text('Loading…').show();
  $('#res-sheet-bk, #res-sheet').addClass('show');

  var reportDate = document.getElementById('mob-report-date').value;
  $.ajax({
    type:'POST', url:'../report/resourceusagelist',
    dataType:'json', data:{ actid: id, date: fmtDate(reportDate) },
    success: function(resp){
      if(!resp || resp.error !== 'No' || !resp.resources || !resp.resources.length){
        $('#res-sheet-empty').text('No Direct Labour / Sub Contractor / Plant & Equipment resources allocated to this activity.').show();
        return;
      }
      $('#res-sheet-empty').hide();
      $('#res-sheet-colhdr').show();
      var html = '';
      resp.resources.forEach(function(r){
        html += '<div class="res-row" data-resource-id="'+r.resource_id+'" data-resource-type-id="'+(r.resource_type_id||'')+'" data-name="'+escHtml(r.name)+'">';
        html += '<div class="res-row-info">';
        html += '<div class="res-row-type">'+escHtml(r.type_name||'')+'</div>';
        html += '<div class="res-row-name">'+escHtml(r.name)+(r.unit ? ' <span class="res-row-unit">('+escHtml(r.unit)+')</span>' : '')+'</div>';
        html += '</div>';
        html += '<div class="res-row-qty"><input type="number" class="res-qty-input" min="0" step="any" inputmode="decimal" placeholder="0" value="'+(r.reported_qty > 0 ? r.reported_qty : '')+'"></div>';
        html += '</div>';
      });
      $('#res-sheet-rows').html(html);
    },
    error: function(){
      $('#res-sheet-empty').text('Could not load resources. Please try again.').show();
    }
  });
}

function resSheetClose(){
  $('#res-sheet-bk, #res-sheet').removeClass('show');
  _resSheetActId = null;
}

$(document).on('click','.mob-btn-resource',function(){
  resSheetOpen($(this).data('id'), $(this).data('name'));
});

$('#res-sheet-close, #res-sheet-cancel, #res-sheet-bk').on('click', resSheetClose);

$('#res-sheet-save').on('click', function(){
  if(!_resSheetActId) return;
  var id = _resSheetActId;
  var rows = [];
  $('#res-sheet-rows .res-row').each(function(){
    var $row = $(this);
    rows.push({
      resource_id: $row.data('resource-id'),
      resource_type_id: $row.data('resource-type-id'),
      name: $row.data('name'),
      qty: $row.find('.res-qty-input').val() || 0
    });
  });

  var $btn = $(this);
  var origText = $btn.text();
  $btn.text('Saving…').attr('disabled', true);

  var reportDate = document.getElementById('mob-report-date').value;
  $.ajax({
    type:'POST', url:'../report/saveresourceusage',
    dataType:'json', data:{ actid: id, date: fmtDate(reportDate), rows: rows },
    success: function(resp){
      $btn.text(origText).removeAttr('disabled');
      if(!resp || resp.error !== 'No'){ toast('Could not save resource usage.'); return; }
      toast('Resources saved!');
      resSheetClose();
    },
    error: function(){
      $btn.text(origText).removeAttr('disabled');
      toast('Network error. Please try again.');
    }
  });
});

// Submit / Update report
$(document).on('click','.mob-btn-report',function(){
  var id = $(this).data('id');
  var qty = parseFloat($('#fqty-'+id).val());
  var reportDate = $('#frd-'+id).val();
  var breakHours = parseFloat($('#fbd-'+id).val()) || 0;
  var startDate = $('#fsd-'+id).val();
  var isEdit = $('#fedit-'+id).hasClass('show');

  if(isNaN(qty) || qty <= 0){ toast('Please enter a valid quantity'); return; }
  if(!startDate){ toast('Please enter Activity Start Date'); return; }

  var $btn = $(this);
  var origText = $btn.text();
  $btn.text('Saving…').attr('disabled',true);

  var url  = isEdit ? '../report/progressreportedit' : '../report/simplereportprogress';
  var data = {
    actid:       id,
    currentqnty: qty,
    reportdate:  fmtDate(reportDate),
    break_hours: breakHours,
    start_date:  fmtDate(startDate)
  };

  $.ajax({
    type:'POST', url: url,
    dataType:'json', data: data,
    success: function(r){
      $btn.text(origText).removeAttr('disabled');
      if(r.error === 'No'){
        toast(isEdit ? 'Report updated successfully!' : 'Reported successfully!');
        $('#form-'+id).removeClass('open');
        setTimeout(loadActivities, 800);
      } else {
        toast('Error saving report. Please try again.');
      }
    },
    error: function(){
      $btn.text(origText).removeAttr('disabled');
      toast('Network error. Please try again.');
    }
  });
});

// Live update Up-to-date Qty as Current Qty is typed
$(document).on('input','.fqty-input',function(){
  var id = this.id.replace('fqty-','');
  var cum = parseFloat($(this).data('cum'))||0;
  var cur = parseFloat($(this).val())||0;
  var unit = $('#fupd-'+id).data('unit')||'';
  $('#fupd-'+id).val((cum + cur).toFixed(3) + (unit ? ' '+unit : ''));
});

// Date change reloads list
document.getElementById('mob-report-date').addEventListener('change', loadActivities);

// ── Tabs ──────────────────────────────────────────────────────────────
var _progressLoadedOnce = false;
var _mbookLoadedOnce = false;

function switchTab(tab){
  $('.mob-tab').removeClass('active');
  $('.mob-tab[data-tab="'+tab+'"]').addClass('active');
  if(tab === 'progress'){
    closeCdpPortal();
    $('#mob-date-bar').show();
    $('#mob-content-progress').show();
    $('#mob-content-mbook').hide();
    if(!_progressLoadedOnce){ loadActivities(); _progressLoadedOnce = true; }
  } else {
    $('#mob-date-bar').hide();
    $('#mob-content-progress').hide();
    $('#mob-content-mbook').show();
    if(!_mbookLoadedOnce && typeof loadMbWoList === 'function'){ loadMbWoList(); _mbookLoadedOnce = true; }
  }
  if(history.replaceState) history.replaceState(null, '', '?tab='+tab);
}

$(document).on('click', '.mob-tab', function(){
  switchTab($(this).data('tab'));
});

// Load on start — whichever tab the server marked active
var _startTab = <?= json_encode($initialTab) ?>;
if(_startTab === 'mbook'){
  if(typeof loadMbWoList === 'function'){ loadMbWoList(); _mbookLoadedOnce = true; }
} else {
  loadActivities();
  _progressLoadedOnce = true;
}

})();
</script>
