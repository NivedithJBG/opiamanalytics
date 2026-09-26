<style>
.mob-mbk-loading{text-align:center;padding:50px 20px;color:var(--muted);font-size:14px}

/* ── WO list ── */
.mbk-wo-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:13px 14px;margin-bottom:8px;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.mbk-wo-num{font-size:14px;font-weight:700;color:var(--text)}
.mbk-wo-meta{font-size:12px;color:var(--muted);margin-top:4px}
.mbk-wo-badge{display:inline-block;margin-top:6px;padding:2px 9px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;background:rgba(217,119,6,.12);color:var(--amber)}

/* ── Entry form ── */
.mbk-back{background:none;border:none;color:var(--accent);font-size:13px;font-weight:600;cursor:pointer;padding:4px 0;margin-bottom:10px}
.mbk-mbinfo{display:flex;gap:10px;background:var(--card);border:1px solid var(--border);border-radius:10px;padding:10px 12px;margin-bottom:12px}
.mbk-mbinfo-field{flex:1;min-width:0}
.mbk-mbinfo-field label{display:block;font-size:10px;font-weight:700;color:var(--slate2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px}
.mbk-mbinfo-field input{width:100%;box-sizing:border-box;padding:6px 8px;font-size:13px;border:1.5px solid var(--border);border-radius:6px;font-family:'Inter',sans-serif}
.mbk-mbinfo-field input[readonly]{background:var(--bg);color:var(--muted)}

.mbk-act-card{background:var(--card);border-radius:12px;border:1px solid var(--border);margin-bottom:8px;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.mbk-act-top{padding:13px 14px;cursor:pointer}
.mbk-act-name{font-size:14px;font-weight:600;color:var(--text);line-height:1.35}
.mbk-act-meta{display:flex;gap:12px;margin-top:5px;flex-wrap:wrap}
.mbk-act-meta span{font-size:11px;color:var(--muted)}
.mbk-act-meta strong{color:var(--text)}
.mbk-act-body{display:none;padding:0 14px 14px;border-top:1px solid var(--border);background:var(--card2)}
.mbk-act-body.open{display:block}

.mbk-remaining{font-size:11px;color:var(--muted);margin-top:6px}
.mbk-remaining strong{color:var(--accent)}
.mbk-remaining.over strong{color:var(--red)}

.mbk-task-row{padding:10px 0;border-top:1px solid var(--border)}
.mbk-task-row:first-child{border-top:none}
.mbk-task-name{font-size:12px;font-weight:600;color:var(--text)}
.mbk-task-unit{font-size:10px;color:var(--muted);margin-left:4px}
.mbk-task-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:6px}
.mbk-task-field label{display:block;font-size:10px;font-weight:700;color:var(--slate2);text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px}
.mbk-task-field input{width:100%;box-sizing:border-box;padding:8px 10px;font-size:13px;border:1.5px solid var(--border);border-radius:8px;font-family:'Inter',sans-serif}
.mbk-task-field input[readonly]{background:var(--bg);color:var(--muted)}
.mbk-task-amount{font-size:11px;color:var(--muted);margin-top:6px}
.mbk-task-amount strong{color:var(--text)}

.mob-form-field label{font-size:10px;font-weight:700;color:var(--slate2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;display:block}

.mbk-actions{display:flex;gap:8px;margin-top:14px}
.mbk-btn{flex:1;padding:12px;border:none;border-radius:10px;font-size:14px;font-weight:600;font-family:'Inter',sans-serif;cursor:pointer;transition:opacity .15s}
.mbk-btn:active{opacity:.8}
.mbk-btn[disabled]{opacity:.5;pointer-events:none}
.mbk-btn-draft{background:#f0f4f8;color:var(--slate2);border:1.5px solid var(--border)!important}
.mbk-btn-submit{background:#166534;color:#fff}
</style>

<script>
(function(){
'use strict';

var _woActivities = [];   // current WO's activities+tasks, working state
var _woNumber = null;
var _woVendor = '';
var _mbNumber = '';
var _mbDate = '';
var _openActId = null;    // activity_id of the currently expanded card, or null

function escHtml(s){
  return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function mbkToast(msg){
  if (typeof toast === 'function') { toast(msg); return; }
  var t = document.getElementById('mob-toast');
  if (!t) { alert(msg); return; }
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(function(){ t.classList.remove('show'); }, 2000);
}

// ── WO list ──────────────────────────────────────────────────────────
window.loadMbWoList = function(){
  $('#mob-content-mbook').html('<div id="mbk-loading" class="mob-mbk-loading"><div class="mob-spinner"></div>Loading work orders…</div>');
  $.ajax({
    type:'POST', url:'../storekeeper/issuedwo', dataType:'json',
    success: function(r){
      if(r.error !== 'No' || !r.rows || !r.rows.length){
        $('#mob-content-mbook').html('<div class="mob-mbk-loading">No work orders found for this project.</div>');
        return;
      }
      renderWoList(r.rows);
    },
    error: function(){
      $('#mob-content-mbook').html('<div class="mob-mbk-loading">Failed to load. Please refresh.</div>');
    }
  });
};

function renderWoList(rows){
  var html = '';
  $.each(rows, function(i, wo){
    html += '<div class="mbk-wo-card" data-wo="'+escHtml(wo.WO_Number)+'">'
      + '<div class="mbk-wo-num">'+escHtml(wo.WO_Number)+'</div>'
      + '<div class="mbk-wo-meta">'+escHtml(wo.vendor_name||'—')+' &middot; '+escHtml(wo.activity_name||'')+'</div>'
      + (wo.cancelled == 1 ? '<span class="mbk-wo-badge">Cancelled</span>' : '')
      + '</div>';
  });
  $('#mob-content-mbook').html(html);
}

$(document).on('click', '.mbk-wo-card', function(){
  openWo($(this).data('wo'));
});

// ── Entry form ───────────────────────────────────────────────────────
function openWo(woNumber){
  _woNumber = woNumber;
  _openActId = null;
  $('#mob-content-mbook').html('<div class="mob-mbk-loading"><div class="mob-spinner"></div>Loading…</div>');
  $.ajax({
    type:'POST', url:'../storekeeper/woactivities', dataType:'json',
    data:{ wo_number: woNumber },
    success: function(r){
      if(r.error !== 'No'){
        mbkToast(r.errortext || 'Failed to load work order.');
        window.loadMbWoList();
        return;
      }
      _woActivities = r.activities || [];
      _woVendor = r.vendor_name || '';
      _mbDate = r.mb_date || todayYmd();
      if(r.has_draft && r.mb_number){
        _mbNumber = r.mb_number;
        renderEntryForm();
      } else {
        $.ajax({
          type:'POST', url:'../storekeeper/mbnext', dataType:'json',
          success: function(mr){
            _mbNumber = (mr.error === 'No') ? mr.mb_number : '';
            renderEntryForm();
          },
          error: function(){ _mbNumber = ''; renderEntryForm(); }
        });
      }
    },
    error: function(){
      mbkToast('Network error. Please try again.');
      window.loadMbWoList();
    }
  });
}

function todayYmd(){
  var d = new Date();
  var m = d.getMonth()+1, day = d.getDate();
  return d.getFullYear()+'-'+(m<10?'0':'')+m+'-'+(day<10?'0':'')+day;
}

function renderEntryForm(){
  var html = '<button type="button" class="mbk-back" id="mbk-back-btn">&#8592; Back to Work Orders</button>';

  html += '<div class="mbk-mbinfo">'
    + '<div class="mbk-mbinfo-field"><label>MB Number</label><input type="text" id="mbk-mbnum" value="'+escHtml(_mbNumber)+'" readonly></div>'
    + '<div class="mbk-mbinfo-field"><label>MB Date</label><input type="date" id="mbk-mbdate" value="'+escHtml(_mbDate)+'"></div>'
    + '</div>';

  $.each(_woActivities, function(i, act){
    var aid = act.activity_id;
    var remaining = Math.max(0, (parseFloat(act.last_reported_qty)||0) - (parseFloat(act.cumulative_qty)||0));
    var currentQty = act.mb_qty || 0;

    html += '<div class="mbk-act-card" data-aid="'+escHtml(aid)+'">';
    html += '<div class="mbk-act-top" data-aid="'+escHtml(aid)+'">';
    html += '<div class="mbk-act-name">'+escHtml(act.activity_name)+'</div>';
    html += '<div class="mbk-act-meta">';
    html += '<span>Last Reported: <strong>'+escHtml(String(act.last_reported_qty||0))+' '+escHtml(act.unit||'')+'</strong></span>';
    html += '<span>Billed so far: <strong>'+escHtml(String(act.cumulative_qty||0))+' '+escHtml(act.unit||'')+'</strong></span>';
    html += '</div></div>';

    html += '<div class="mbk-act-body" id="mbk-body-'+escHtml(aid)+'">';
    html += '<div class="mob-form-field"><label>Current Qty (this M.Book)</label>'
      + '<input type="number" class="mbk-qty-input" id="mbk-qty-'+escHtml(aid)+'" data-aid="'+escHtml(aid)+'" '
      + 'data-remaining="'+remaining+'" value="'+(currentQty||'')+'" placeholder="0.00" step="0.001" min="0" max="'+remaining+'" inputmode="decimal"></div>';
    html += '<div class="mbk-remaining" id="mbk-rem-'+escHtml(aid)+'">Remaining: <strong>'+remaining.toFixed(3)+' '+escHtml(act.unit||'')+'</strong></div>';

    $.each(act.tasks || [], function(ti, task){
      var tid = task.task_id;
      var rate = parseFloat(task.rate)||0;
      var unitQty = parseFloat(task.unit_qty)||0;
      var workDone = (task.work_done !== undefined && task.work_done !== null && task.work_done !== '')
        ? task.work_done
        : (currentQty ? (currentQty * unitQty) : 0);
      html += '<div class="mbk-task-row" data-aid="'+escHtml(aid)+'" data-tid="'+escHtml(tid)+'" data-rate="'+rate+'" data-unitqty="'+unitQty+'">';
      html += '<div class="mbk-task-name">'+escHtml(task.task_name)+'<span class="mbk-task-unit">'+escHtml(task.task_unit||'')+'</span></div>';
      html += '<div class="mbk-task-grid">';
      html += '<div class="mbk-task-field"><label>Rate</label><input type="text" value="'+rate.toFixed(2)+'" readonly></div>';
      html += '<div class="mbk-task-field"><label>Work Done</label><input type="number" class="mbk-wd-input" id="mbk-wd-'+escHtml(aid)+'-'+escHtml(tid)+'" value="'+(workDone||'')+'" step="0.001" min="0" inputmode="decimal"></div>';
      html += '</div>';
      html += '<div class="mbk-task-amount" id="mbk-amt-'+escHtml(aid)+'-'+escHtml(tid)+'">Amount: <strong>'+(rate * (parseFloat(workDone)||0)).toFixed(2)+'</strong></div>';
      html += '</div>';
    });

    html += '</div>'; // mbk-act-body
    html += '</div>'; // mbk-act-card
  });

  html += '<div class="mbk-actions">'
    + '<button type="button" class="mbk-btn mbk-btn-draft" id="mbk-draft-btn">Save Draft</button>'
    + '<button type="button" class="mbk-btn mbk-btn-submit" id="mbk-submit-btn">Submit</button>'
    + '</div>';

  $('#mob-content-mbook').html(html);
}

$(document).on('click', '#mbk-back-btn', function(){
  window.loadMbWoList();
});

$(document).on('click', '.mbk-act-top', function(){
  var aid = $(this).data('aid');
  var $body = $('#mbk-body-'+aid);
  if($body.hasClass('open')){
    $body.removeClass('open');
    _openActId = null;
  } else {
    $('.mbk-act-body.open').removeClass('open');
    $body.addClass('open');
    _openActId = aid;
  }
});

// Current Qty -> auto-fill Work Done per task (editable override still allowed)
$(document).on('input', '.mbk-qty-input', function(){
  var aid = $(this).data('aid');
  var remaining = parseFloat($(this).data('remaining')) || 0;
  var qty = parseFloat($(this).val()) || 0;
  var $rem = $('#mbk-rem-'+aid);
  var left = remaining - qty;
  $rem.toggleClass('over', left < 0);
  $rem.html('Remaining: <strong>'+left.toFixed(3)+'</strong>');

  $('.mbk-task-row[data-aid="'+aid+'"]').each(function(){
    var tid = $(this).data('tid');
    var unitQty = parseFloat($(this).data('unitqty')) || 0;
    var rate = parseFloat($(this).data('rate')) || 0;
    var workDone = qty * unitQty;
    $('#mbk-wd-'+aid+'-'+tid).val(workDone ? workDone.toFixed(3) : '');
    $('#mbk-amt-'+aid+'-'+tid).html('Amount: <strong>'+(rate*workDone).toFixed(2)+'</strong>');
  });
});

// Manual Work Done edit -> recompute its own Amount only
$(document).on('input', '.mbk-wd-input', function(){
  var $row = $(this).closest('.mbk-task-row');
  var aid = $row.data('aid');
  var tid = $row.data('tid');
  var rate = parseFloat($row.data('rate')) || 0;
  var wd = parseFloat($(this).val()) || 0;
  $('#mbk-amt-'+aid+'-'+tid).html('Amount: <strong>'+(rate*wd).toFixed(2)+'</strong>');
});

function buildEntries(){
  var entries = [];
  $.each(_woActivities, function(i, act){
    var aid = act.activity_id;
    var qty = parseFloat($('#mbk-qty-'+aid).val()) || 0;
    var tasks = [];
    $.each(act.tasks || [], function(ti, task){
      var tid = task.task_id;
      var wd = parseFloat($('#mbk-wd-'+aid+'-'+tid).val()) || 0;
      tasks.push({
        task_id: tid,
        task_name: task.task_name,
        unit: task.task_unit || '',
        rate: parseFloat(task.rate) || 0,
        work_done: wd
      });
    });
    entries.push({
      activity_id: aid,
      activity_name: act.activity_name,
      unit: act.unit || '',
      qty: qty,
      tasks: tasks
    });
  });
  return entries;
}

function submitMb(url, successMsg, btn){
  var $btn = $(btn);
  var origText = $btn.text();
  var mbDate = $('#mbk-mbdate').val();
  if(!mbDate){ mbkToast('Please enter the M.Book date'); return; }

  var entries = buildEntries();
  var overLimit = false;
  $.each(_woActivities, function(i, act){
    var $qty = $('#mbk-qty-'+act.activity_id);
    var remaining = parseFloat($qty.data('remaining')) || 0;
    var qty = parseFloat($qty.val()) || 0;
    if(qty > remaining + 0.001) overLimit = true;
  });
  if(overLimit){ mbkToast('One or more activities exceed the remaining reported quantity.'); return; }

  $('.mbk-btn').attr('disabled', true);
  $btn.text('Saving…');

  $.ajax({
    type:'POST', url: url, dataType:'json',
    data:{
      wo_number: _woNumber,
      entries: JSON.stringify(entries),
      mb_number: _mbNumber,
      mb_date: mbDate
    },
    success: function(r){
      $('.mbk-btn').attr('disabled', false);
      $btn.text(origText);
      if(r.error === 'No'){
        mbkToast(successMsg);
        if(url.indexOf('savemb') !== -1){
          window.loadMbWoList();
        }
      } else {
        mbkToast(r.errortext || 'Failed to save.');
      }
    },
    error: function(){
      $('.mbk-btn').attr('disabled', false);
      $btn.text(origText);
      mbkToast('Network error. Please try again.');
    }
  });
}

$(document).on('click', '#mbk-submit-btn', function(){
  submitMb('../storekeeper/savemb', 'M.Book submitted successfully!', this);
});

$(document).on('click', '#mbk-draft-btn', function(){
  submitMb('../storekeeper/savedraft', 'Draft saved.', this);
});

})();
</script>
