<?php
$this->title = 'Field Report';
?>
<style>
/* ── App shell ── */
:root{--navy:#0c2461;--teal:#00838f;--orange:#e67e22;--green:#27ae60;--red:#e74c3c;--grey:#f0f2f8;--border:#d0d4e0;--text:#1a2540;--muted:#6b7a99}
*{box-sizing:border-box}

#mob-app{display:flex;flex-direction:column;height:100vh;overflow:hidden}

/* ── Top bar ── */
#mob-topbar{
  background:linear-gradient(135deg,var(--navy) 0%,#1a3a8f 100%);
  padding:env(safe-area-inset-top,0px) 16px 12px;
  padding-top:calc(env(safe-area-inset-top,0px) + 12px);
  flex-shrink:0;
  display:flex;align-items:center;gap:12px;
}
#mob-topbar .mob-back{background:none;border:none;color:#fff;font-size:22px;line-height:1;cursor:pointer;padding:4px;display:none}
#mob-topbar .mob-title{flex:1;color:#fff;font-size:16px;font-weight:700;letter-spacing:.3px}
#mob-topbar .mob-proj{font-size:11px;color:rgba(255,255,255,.65);margin-top:2px}
#mob-date-bar{background:var(--navy);padding:0 16px 10px;flex-shrink:0;display:flex;align-items:center;gap:10px}
#mob-date-bar label{color:rgba(255,255,255,.7);font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
#mob-report-date{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);color:#fff;border-radius:6px;padding:6px 10px;font-size:13px;font-family:'Inter',sans-serif;flex:1}
#mob-report-date::-webkit-calendar-picker-indicator{filter:invert(1);opacity:.7}

/* ── Scrollable content ── */
#mob-content{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding:12px 12px 80px}

/* ── IOW group header ── */
.mob-iow-hdr{
  background:var(--navy);color:#fff;
  font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;
  padding:7px 14px;border-radius:6px;margin:14px 0 6px;
}
.mob-iow-hdr:first-child{margin-top:0}

/* ── Activity card ── */
.mob-act-card{
  background:#fff;border-radius:10px;
  border:1px solid var(--border);
  margin-bottom:8px;overflow:hidden;
  box-shadow:0 1px 4px rgba(0,0,0,.06);
}
.mob-act-card.completed{border-left:4px solid var(--green)}
.mob-act-card.active{border-left:4px solid var(--teal)}

.mob-act-top{padding:12px 14px;display:flex;align-items:flex-start;gap:10px;cursor:pointer}
.mob-act-name{flex:1;font-size:14px;font-weight:600;color:var(--text);line-height:1.3}
.mob-act-meta{display:flex;gap:14px;margin-top:4px;flex-wrap:wrap}
.mob-act-meta span{font-size:11px;color:var(--muted)}
.mob-act-meta strong{color:var(--text)}
.mob-act-badge{flex-shrink:0;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;align-self:flex-start;margin-top:2px}
.mob-act-badge.done{background:#e8f8ee;color:var(--green)}
.mob-act-badge.active{background:#e0f5f7;color:var(--teal)}

/* ── Report form (expands inside card) ── */
.mob-act-form{display:none;padding:0 14px 14px;border-top:1px solid var(--border);background:#fafbff}
.mob-act-form.open{display:block}
.mob-form-row{display:flex;gap:10px;margin-top:10px;flex-wrap:wrap}
.mob-form-field{display:flex;flex-direction:column;flex:1;min-width:120px}
.mob-form-field.full{flex:0 0 100%}
.mob-form-field label{font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
.mob-form-field input,.mob-form-field select{
  padding:9px 12px;font-size:14px;color:var(--text);
  border:1.5px solid var(--border);border-radius:7px;
  background:#fff;font-family:'Inter',sans-serif;
  outline:none;width:100%;transition:border-color .15s;
}
.mob-form-field input:focus,.mob-form-field select:focus{border-color:var(--teal)}
.mob-form-field input[readonly]{background:#f3f4f8;color:var(--muted)}
.mob-cum-info{font-size:11px;color:var(--muted);margin-top:6px;padding:6px 10px;background:#f3f4f8;border-radius:5px}
.mob-cum-info strong{color:var(--text)}

.mob-form-actions{display:flex;gap:8px;margin-top:12px}
.mob-btn{flex:1;padding:11px;border:none;border-radius:8px;font-size:14px;font-weight:600;font-family:'Inter',sans-serif;cursor:pointer;transition:opacity .15s}
.mob-btn:active{opacity:.8}
.mob-btn-report{background:var(--teal);color:#fff}
.mob-btn-cancel{background:#e8eaf2;color:var(--muted)}
.mob-btn-complete{background:var(--green);color:#fff;font-size:12px}

/* ── Loading / empty ── */
#mob-loading{text-align:center;padding:50px 20px;color:var(--muted);font-size:14px}
.mob-spinner{width:36px;height:36px;border:3px solid #e0e4ef;border-top-color:var(--teal);border-radius:50%;animation:spin .8s linear infinite;margin:0 auto 14px}
@keyframes spin{to{transform:rotate(360deg)}}

/* ── Toast ── */
#mob-toast{
  position:fixed;bottom:calc(env(safe-area-inset-bottom,0px) + 20px);left:50%;transform:translateX(-50%);
  background:#1a2540;color:#fff;padding:10px 22px;border-radius:24px;
  font-size:13px;font-weight:600;white-space:nowrap;
  opacity:0;transition:opacity .25s;pointer-events:none;z-index:999;
}
#mob-toast.show{opacity:1}

/* ── Bottom nav ── */
#mob-bottom-nav{
  position:fixed;bottom:0;left:0;right:0;
  background:#fff;border-top:1px solid var(--border);
  padding:8px 0 calc(env(safe-area-inset-bottom,0px) + 6px);
  display:flex;justify-content:space-around;z-index:100;flex-shrink:0;
}
.mob-nav-item{display:flex;flex-direction:column;align-items:center;gap:2px;font-size:9px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;cursor:pointer;padding:0 12px;text-decoration:none}
.mob-nav-item.active{color:var(--teal)}
.mob-nav-icon{font-size:20px;line-height:1}
</style>

<div id="mob-app">

  <!-- Top bar -->
  <div id="mob-topbar">
    <button class="mob-back" id="mob-back-btn">&#8592;</button>
    <div>
      <div class="mob-title">Field Report</div>
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
    html += '<span>Unit: <strong>'+escHtml(act.unit||'—')+'</strong></span>';
    html += '<span>B.Qty: <strong>'+escHtml(String(act.b_qty||'—'))+'</strong></span>';
    html += '<span>Reported: <strong>'+escHtml(String(cumQty))+'</strong></span>';
    html += '</div></div>';
    html += badge;
    html += '</div>'; // mob-act-top

    html += '<div class="mob-act-form" id="form-'+act.id+'">';
    html += '<div class="mob-cum-info">Cumulated qty: <strong>'+escHtml(String(cumQty))+'</strong> &nbsp;|&nbsp; Last reported: <strong>'+escHtml(String(lastDate))+'</strong></div>';
    html += '<div class="mob-form-row">';
    html += '<div class="mob-form-field"><label>Activity Start Date</label><input type="date" id="fsd-'+act.id+'" value="'+escHtml(startVal)+'"></div>';
    html += '<div class="mob-form-field"><label>Report Date</label><input type="date" id="frd-'+act.id+'" value="'+document.getElementById('mob-report-date').value+'" readonly></div>';
    html += '</div>';
    html += '<div class="mob-form-row">';
    html += '<div class="mob-form-field"><label>Qty This Report</label><input type="number" id="fqty-'+act.id+'" placeholder="0.00" step="0.001" inputmode="decimal"></div>';
    html += '<div class="mob-form-field"><label>Working Hours</label>';
    html += '<select id="fwh-'+act.id+'">';
    html += '<option value="8">8 hrs</option><option value="10">10 hrs</option><option value="12">12 hrs</option><option value="24">24 hrs</option>';
    html += '</select></div>';
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
    $('#frd-'+id).val(document.getElementById('mob-report-date').value);
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

// Submit report
$(document).on('click','.mob-btn-report',function(){
  var id = $(this).data('id');
  var qty = parseFloat($('#fqty-'+id).val());
  var startDate = $('#fsd-'+id).val();
  var reportDate = $('#frd-'+id).val();
  var wh = $('#fwh-'+id).val();

  if(!startDate){ toast('Please enter Activity Start Date'); return; }
  if(isNaN(qty) || qty <= 0){ toast('Please enter a valid quantity'); return; }
  if(reportDate < startDate){ toast('Report date cannot be before start date'); return; }

  var $btn = $(this);
  $btn.text('Saving…').attr('disabled',true);

  $.ajax({
    type:'POST', url:'../report/simplereportprogress',
    dataType:'json',
    data:{
      actid: id,
      currentqnty: qty,
      start_date: fmtDate(startDate),
      reportdate: fmtDate(reportDate),
      workhours: wh
    },
    success: function(r){
      $btn.text('Submit Report').removeAttr('disabled');
      if(r.error === 'No'){
        toast('Reported successfully!');
        $('#form-'+id).removeClass('open');
        setTimeout(loadActivities, 800);
      } else {
        toast('Error saving report. Please try again.');
      }
    },
    error: function(){
      $btn.text('Submit Report').removeAttr('disabled');
      toast('Network error. Please try again.');
    }
  });
});

// Date change reloads list
document.getElementById('mob-report-date').addEventListener('change', loadActivities);

// Load on start
loadActivities();

})();
</script>
