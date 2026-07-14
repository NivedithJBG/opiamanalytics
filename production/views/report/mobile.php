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

/* ── Scrollable content ── */
#mob-content{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding:12px 12px 80px;background:var(--bg)}

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
  margin-bottom:8px;overflow:hidden;
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
.mob-cum-info{font-size:11px;color:var(--muted);margin-top:8px;padding:8px 12px;background:#fff;border-radius:8px;border:1px solid var(--border)}
.mob-cum-info strong{color:var(--text)}

.mob-form-actions{display:flex;gap:8px;margin-top:14px}
.mob-btn{flex:1;padding:12px;border:none;border-radius:10px;font-size:14px;font-weight:600;font-family:'Inter',sans-serif;cursor:pointer;transition:opacity .15s}
.mob-btn:active{opacity:.8}
.mob-btn-report{background:var(--accent);color:#fff}
.mob-btn-cancel{background:#fff;color:var(--muted);border:1px solid var(--border)}
.mob-btn-complete{background:var(--green);color:#fff;font-size:12px}

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

/* ── Edit mode banner ── */
.mob-edit-banner{
  background:rgba(217,119,6,.12);border:1px solid var(--amber);
  border-radius:8px;padding:7px 12px;margin-top:10px;
  font-size:11px;font-weight:600;color:var(--amber);display:none;
}
.mob-edit-banner.show{display:block}

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
      <div class="mob-title">Progress Report</div>
      <div class="mob-proj"><?= htmlspecialchars($projectName) ?></div>
    </div>
  </div>

  <!-- Date bar -->
  <div id="mob-date-bar">
    <label>Report Date</label>
    <input type="date" id="mob-report-date" value="<?= date('Y-m-d') ?>">
  </div>

  <!-- Scrollable activity list -->
  <div id="mob-content">
    <div id="mob-loading">
      <div class="mob-spinner"></div>
      Loading activities…
    </div>
  </div>

</div>

<!-- Toast -->
<div id="mob-toast"></div>

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
  var date = fmtDate(document.getElementById('mob-report-date').value);
  $('#mob-content').html('<div id="mob-loading"><div class="mob-spinner"></div>Loading…</div>');

  $.ajax({
    type:'POST', url:'../report/scheduleprogressactivities',
    dataType:'json', data:{dateselect: date},
    success: function(d){
      if(d.error !== 'No'){ $('#mob-content').html('<div id="mob-loading">No activities found.</div>'); return; }
      renderActivities(d);
    },
    error: function(){ $('#mob-content').html('<div id="mob-loading">Failed to load. Please refresh.</div>'); }
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
        $('#mob-content').html('<div id="mob-loading">No activities found for this project.</div>');
        return;
      }
      buildCards(r.activities);
    },
    error: function(){
      $('#mob-content').html('<div id="mob-loading">No activities found for this project.</div>');
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
    html += '<div class="mob-form-row mob-form-row-date">';
    html += '<div class="mob-form-field"><label>Activity Start Date</label><input type="date" id="fsd-'+act.id+'" value="'+escHtml(startVal)+'"></div>';
    html += '<div class="mob-form-field"><label>Report Date</label><input type="date" id="frd-'+act.id+'" value="'+document.getElementById('mob-report-date').value+'" readonly></div>';
    html += '</div>';
    html += '<div class="mob-form-row">';
    html += '<div class="mob-form-field"><label>Break Days</label><input type="number" id="fbd-'+act.id+'" placeholder="0" step="0.5" min="0" inputmode="decimal"></div>';
    html += '<div class="mob-form-field"><label>Unit</label><input type="text" value="'+escHtml(act.unit||'—')+'" readonly></div>';
    html += '</div>';
    html += '<div class="mob-form-row">';
    html += '<div class="mob-form-field"><label>Current Qty</label><input type="number" id="fqty-'+act.id+'" data-cum="'+escHtml(String(cumQty||0))+'" placeholder="0.00" step="0.001" inputmode="decimal" class="fqty-input"></div>';
    html += '<div class="mob-form-field"><label>Up-to-date Qty</label><input type="text" id="fupd-'+act.id+'" data-unit="'+escHtml(act.unit||'')+'" value="'+escHtml(String(cumQty||0))+' '+escHtml(act.unit||'')+'" readonly></div>';
    html += '</div>';

    html += '<div class="mob-form-actions">';
    html += '<button class="mob-btn mob-btn-cancel" data-id="'+act.id+'">Cancel</button>';
    html += '<button class="mob-btn mob-btn-report" data-id="'+act.id+'">Submit Report</button>';
    html += '</div>';
    html += '</div>'; // mob-act-form

    html += '</div>'; // mob-act-card
  });

  $('#mob-content').html(html);
}

function escHtml(s){
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Toggle form open/close on card tap
$(document).on('click','.mob-act-top', function(){
  var id = $(this).data('id');
  var $form = $('#form-'+id);
  if($form.hasClass('open')){
    $form.removeClass('open');
    _openActId = null;
  } else {
    $('.mob-act-form.open').removeClass('open');
    $form.addClass('open');
    _openActId = id;
    // sync report date
    var reportDate = document.getElementById('mob-report-date').value;
    $('#frd-'+id).val(reportDate);
    // reset edit state
    $('#flogid-'+id).val('');
    $('#fedit-'+id).removeClass('show');
    $('#fqty-'+id).val('');
    $('#fbd-'+id).val('');
    $('.mob-btn-report[data-id="'+id+'"]').text('Submit Report');

    // check if a report already exists for this date
    $.ajax({
      type:'POST', url:'../report/getreportbydate',
      dataType:'json',
      data:{ actid: id, report_date: reportDate },
      success: function(r){
        if(r.found){
          $('#flogid-'+id).val(r.log_id);
          $('#fqty-'+id).val(r.currentqty);
          // break_hour is stored in hours; convert back to days using workhours=8 default
          var breakDays = r.break_hour > 0 ? (r.break_hour / 8).toFixed(2) : '';
          $('#fbd-'+id).val(breakDays);
          if(r.start_date) $('#fsd-'+id).val(r.start_date);
          $('#fedit-'+id).addClass('show');
          $('.mob-btn-report[data-id="'+id+'"]').text('Update Report');
        }
      }
    });

    // scroll card into view
    setTimeout(function(){
      var card = document.querySelector('.mob-act-card[data-id="'+id+'"]');
      if(card) card.scrollIntoView({behavior:'smooth', block:'nearest'});
    },200);
  }
});

// Cancel
$(document).on('click','.mob-btn-cancel',function(){
  var id = $(this).data('id');
  $('#form-'+id).removeClass('open');
  _openActId = null;
});

// Submit / Update report
$(document).on('click','.mob-btn-report',function(){
  var id = $(this).data('id');
  var qty = parseFloat($('#fqty-'+id).val());
  var reportDate = $('#frd-'+id).val();
  var breakDays = parseFloat($('#fbd-'+id).val()) || 0;
  var startDate = $('#fsd-'+id).val();
  var logId = $('#flogid-'+id).val();
  var isEdit = logId !== '';

  if(isNaN(qty) || qty <= 0){ toast('Please enter a valid quantity'); return; }
  if(!startDate){ toast('Please enter Activity Start Date'); return; }

  var $btn = $(this);
  var origText = $btn.text();
  $btn.text('Saving…').attr('disabled',true);

  var url  = isEdit ? '../report/progressreportedit' : '../report/simplereportprogress';
  var data = {
    actid:      id,
    currentqnty: qty,
    reportdate: fmtDate(reportDate),
    break_days: breakDays,
    workhours:  8,
    start_date: fmtDate(startDate)
  };
  if(isEdit) data.log_id = logId;

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

// Load on start
loadActivities();

})();
</script>
