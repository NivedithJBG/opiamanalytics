<?php
$this->title = 'Raise Indent';
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

#mob-topbar{
  background:var(--slate);
  padding:env(safe-area-inset-top,0px) 16px 12px;
  padding-top:calc(env(safe-area-inset-top,0px) + 14px);
  flex-shrink:0;
  display:flex;align-items:center;gap:12px;
  border-bottom:1px solid rgba(0,0,0,.15);
}
#mob-topbar .mob-back{background:none;border:none;color:var(--slate-text);font-size:22px;line-height:1;cursor:pointer;padding:4px}
#mob-topbar .mob-title{flex:1;color:var(--slate-text);font-size:17px;font-weight:700;letter-spacing:.2px}
#mob-topbar .mob-proj{font-size:11px;color:rgba(241,245,249,.65);margin-top:3px}

#mob-search-bar{background:var(--slate);padding:0 16px 12px;flex-shrink:0;border-bottom:1px solid rgba(0,0,0,.15)}
#mob-search-bar input{width:100%;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:var(--slate-text);border-radius:8px;padding:8px 12px;font-size:13px;font-family:'Inter',sans-serif}
#mob-search-bar input::placeholder{color:rgba(241,245,249,.5)}

#mob-content{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding:12px 12px 80px;background:var(--bg)}

.mob-type-hdr{
  background:var(--slate);color:var(--slate-text);
  font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;
  padding:7px 14px;border-radius:8px;margin:14px 0 6px;
}
.mob-type-hdr:first-child{margin-top:0}

.mob-res-card{
  background:var(--card);border-radius:12px;border:1px solid var(--border);
  margin-bottom:8px;box-shadow:0 1px 3px rgba(0,0,0,.06);
  padding:13px 14px;display:flex;align-items:center;gap:10px;
}
.mob-res-card.selected{border-color:var(--accent);background:#eef2f8}
.mob-res-check{width:20px;height:20px;flex-shrink:0;accent-color:var(--accent)}
.mob-res-info{flex:1;min-width:0}
.mob-res-name{font-size:14px;font-weight:600;color:var(--text)}
.mob-res-meta{display:flex;gap:12px;margin-top:4px;flex-wrap:wrap}
.mob-res-meta span{font-size:11px;color:var(--muted)}
.mob-res-meta strong{color:var(--text)}
.mob-res-task-btn{flex-shrink:0;font-size:11px;font-weight:600;color:var(--accent);background:none;border:1px solid var(--accent);border-radius:8px;padding:6px 10px}
.mob-res-task-btn.set{background:var(--accent);color:#fff}

#mob-loading{text-align:center;padding:50px 20px;color:var(--muted);font-size:14px}
.mob-spinner{width:36px;height:36px;border:3px solid var(--border);border-top-color:var(--accent);border-radius:50%;animation:spin .8s linear infinite;margin:0 auto 14px}
@keyframes spin{to{transform:rotate(360deg)}}

#mob-toast{
  position:fixed;bottom:calc(env(safe-area-inset-bottom,0px) + 90px);left:50%;transform:translateX(-50%);
  background:var(--slate);color:var(--slate-text);padding:10px 22px;border-radius:24px;
  font-size:13px;font-weight:600;white-space:nowrap;
  opacity:0;transition:opacity .25s;pointer-events:none;z-index:999;
  box-shadow:0 4px 12px rgba(0,0,0,.2);
}
#mob-toast.show{opacity:1}

/* ── Bottom action bar (selection count + raise button) ── */
#mob-action-bar{
  position:fixed;left:0;right:0;bottom:0;
  background:#fff;border-top:1px solid var(--border);
  padding:10px 14px calc(env(safe-area-inset-bottom,0px) + 10px);
  display:none;align-items:center;gap:10px;z-index:90;
  box-shadow:0 -2px 8px rgba(0,0,0,.06);
}
#mob-action-bar.show{display:flex}
#mob-sel-count{font-size:12px;color:var(--muted);flex:1}
#mob-raise-btn{background:var(--accent);color:#fff;border:none;border-radius:10px;padding:11px 20px;font-size:14px;font-weight:600}

/* ── Full-screen modal sheets (task picker / confirm) ── */
.mob-sheet{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:200;display:none;align-items:flex-end}
.mob-sheet.show{display:flex}
.mob-sheet-panel{background:#fff;border-radius:16px 16px 0 0;max-height:85vh;width:100%;display:flex;flex-direction:column}
.mob-sheet-hdr{padding:16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.mob-sheet-hdr h3{font-size:15px;font-weight:700;color:var(--text)}
.mob-sheet-close{background:none;border:none;font-size:20px;color:var(--muted)}
.mob-sheet-body{overflow-y:auto;padding:12px 16px;flex:1}
.mob-sheet-footer{padding:12px 16px calc(env(safe-area-inset-bottom,0px) + 12px);border-top:1px solid var(--border);display:flex;gap:8px}

.mob-task-row{display:flex;align-items:center;gap:10px;padding:11px 4px;border-bottom:1px solid var(--card2)}
.mob-task-row input{width:18px;height:18px;accent-color:var(--accent)}
.mob-task-name{font-size:13px;font-weight:600;color:var(--text)}
.mob-task-act{font-size:11px;color:var(--muted);margin-top:2px}

.mob-confirm-row{border:1px solid var(--border);border-radius:10px;padding:12px;margin-bottom:10px}
.mob-confirm-row .mob-res-name{margin-bottom:6px}
.mob-confirm-row .mob-task-act{margin-bottom:8px}
.mob-form-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.mob-form-field label{font-size:10px;font-weight:700;color:var(--slate2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;display:block}
.mob-form-field input{width:100%;padding:9px 10px;font-size:14px;border:1.5px solid var(--border);border-radius:8px;font-family:'Inter',sans-serif}
</style>

<div id="mob-app">

  <div id="mob-topbar">
    <button class="mob-back" onclick="history.back()">&#8592;</button>
    <div>
      <div class="mob-title">Raise Indent</div>
      <div class="mob-proj"><?= htmlspecialchars($projectName) ?></div>
    </div>
  </div>

  <div id="mob-search-bar">
    <input type="text" id="mob-search" placeholder="Search resource…">
  </div>

  <div id="mob-content">
    <div id="mob-loading"><div class="mob-spinner"></div>Loading resources…</div>
  </div>

  <div id="mob-action-bar">
    <span id="mob-sel-count">0 selected</span>
    <button id="mob-raise-btn">Raise Indent</button>
  </div>

</div>

<!-- Task picker sheet -->
<div class="mob-sheet" id="mob-task-sheet">
  <div class="mob-sheet-panel">
    <div class="mob-sheet-hdr"><h3 id="mob-task-sheet-title">Select Task</h3><button class="mob-sheet-close" data-close="mob-task-sheet">&times;</button></div>
    <div class="mob-sheet-body" id="mob-task-sheet-body"></div>
    <div class="mob-sheet-footer">
      <button class="mob-btn mob-btn-cancel" data-close="mob-task-sheet" style="flex:1;padding:11px;border-radius:10px;border:1px solid var(--border);background:#fff;font-weight:600;">Cancel</button>
      <button class="mob-btn mob-btn-report" id="mob-task-sheet-save" style="flex:1;padding:11px;border-radius:10px;border:none;background:var(--accent);color:#fff;font-weight:600;">Select</button>
    </div>
  </div>
</div>

<!-- Confirm sheet -->
<div class="mob-sheet" id="mob-confirm-sheet">
  <div class="mob-sheet-panel" style="max-height:90vh;">
    <div class="mob-sheet-hdr"><h3>Confirm Indent</h3><button class="mob-sheet-close" data-close="mob-confirm-sheet">&times;</button></div>
    <div class="mob-sheet-body" id="mob-confirm-sheet-body"></div>
    <div class="mob-sheet-footer">
      <button class="mob-btn mob-btn-cancel" data-close="mob-confirm-sheet" style="flex:1;padding:11px;border-radius:10px;border:1px solid var(--border);background:#fff;font-weight:600;">Cancel</button>
      <button class="mob-btn mob-btn-report" id="mob-confirm-submit" style="flex:1;padding:11px;border-radius:10px;border:none;background:var(--accent);color:#fff;font-weight:600;">Submit</button>
    </div>
  </div>
</div>

<div id="mob-toast"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function(){
'use strict';

var _allRows = [];
var _selected = {}; // resource_id -> {task_id, task_name, activity_id, activity_name}

function toast(msg, dur){
  var t = document.getElementById('mob-toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(function(){ t.classList.remove('show'); }, dur||2000);
}
function escHtml(s){
  return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function loadResources(){
  $.ajax({
    type:'POST', url:'../storekeeper/indents', dataType:'json',
    success:function(d){
      if(d.error !== 'No'){ $('#mob-content').html('<div id="mob-loading">'+escHtml(d.errortext||'Error loading data.')+'</div>'); return; }
      _allRows = d.rows || [];
      render();
    },
    error:function(){ $('#mob-content').html('<div id="mob-loading">Failed to load. Please try again.</div>'); }
  });
}

function render(){
  var q = ($('#mob-search').val()||'').toLowerCase();
  var rows = _allRows.filter(function(r){ return !q || (r.resource_name||'').toLowerCase().indexOf(q) !== -1; });

  if(!_allRows.length){ $('#mob-content').html('<div id="mob-loading">No allocated resources found for this project.</div>'); return; }
  if(!rows.length){ $('#mob-content').html('<div id="mob-loading">No matching resources.</div>'); return; }

  var html = '';
  var lastType = null;
  rows.forEach(function(r){
    if(r.resource_type !== lastType){
      html += '<div class="mob-type-hdr">'+escHtml(r.resource_type)+'</div>';
      lastType = r.resource_type;
    }
    var sel = _selected[r.resource_id];
    var checked = sel ? ' checked' : '';
    var cardSel = sel ? ' selected' : '';
    var taskLabel = sel && sel.task_id ? sel.task_name : 'Task';
    var taskBtnCls = sel && sel.task_id ? ' set' : '';

    html += '<div class="mob-res-card'+cardSel+'" data-id="'+r.resource_id+'">';
    html += '<input type="checkbox" class="mob-res-check" data-id="'+r.resource_id+'"'+checked+'>';
    html += '<div class="mob-res-info">';
    html += '<div class="mob-res-name">'+escHtml(r.resource_name)+'</div>';
    html += '<div class="mob-res-meta">';
    html += '<span>Unit: <strong>'+escHtml(r.unit)+'</strong></span>';
    html += '<span>Stock: <strong>'+escHtml(String(r.stock))+'</strong></span>';
    html += '</div></div>';
    html += '<button type="button" class="mob-res-task-btn'+taskBtnCls+'" data-id="'+r.resource_id+'" data-name="'+escHtml(r.resource_name)+'">'+escHtml(taskLabel)+'</button>';
    html += '</div>';
  });
  $('#mob-content').html(html);
  updateActionBar();
}

function updateActionBar(){
  var n = Object.keys(_selected).length;
  $('#mob-sel-count').text(n + (n===1?' selected':' selected'));
  $('#mob-action-bar').toggleClass('show', n>0);
}

$('#mob-search').on('input', render);

// Checkbox toggles selection
$(document).on('change','.mob-res-check', function(){
  var id = String($(this).data('id'));
  if(this.checked){
    var row = _allRows.filter(function(x){ return String(x.resource_id)===id; })[0] || {};
    _selected[id] = _selected[id] || { task_id:null, task_name:'', activity_id:0, activity_name:'', reorder: row.reorder_quantity||0 };
  } else {
    delete _selected[id];
  }
  render();
});

// Tapping the card (not the checkbox/task button) toggles the checkbox too
$(document).on('click','.mob-res-card', function(e){
  if($(e.target).is('.mob-res-check, .mob-res-task-btn')) return;
  $(this).find('.mob-res-check').trigger('click');
});

// Task picker
var _taskPickerFor = null;
$(document).on('click','.mob-res-task-btn', function(e){
  e.stopPropagation();
  var id = String($(this).data('id'));
  if(!_selected[id]){
    $('.mob-res-check[data-id="'+id+'"]').prop('checked', true).trigger('change');
  }
  _taskPickerFor = id;
  $('#mob-task-sheet-title').text($(this).data('name'));
  $('#mob-task-sheet-body').html('<p style="text-align:center;padding:30px;color:#888;">Loading…</p>');
  $('#mob-task-sheet').addClass('show');

  $.ajax({
    type:'POST', url:'../storekeeper/getresourcetasks', dataType:'json', data:{resource_id:id},
    success:function(d){
      if(d.error !== 'No'){ $('#mob-task-sheet-body').html('<p style="text-align:center;padding:20px;color:#888;">'+escHtml(d.errortext||'Error.')+'</p>'); return; }
      var tasks = d.tasks || [];
      if(!tasks.length){ $('#mob-task-sheet-body').html('<p style="text-align:center;padding:20px;color:#888;">No tasks are mapped to this resource.</p>'); return; }
      var selTid = _selected[id] ? String(_selected[id].task_id||'') : '';
      var html = '';
      tasks.forEach(function(t){
        var checked = String(t.task_id)===selTid ? ' checked' : '';
        html += '<label class="mob-task-row">';
        html += '<input type="radio" name="mob-task-radio" value="'+t.task_id+'" data-task-name="'+escHtml(t.task_name)+'" data-activity-id="'+(t.activity_id||0)+'" data-activity-name="'+escHtml(t.activity_name)+'"'+checked+'>';
        html += '<div><div class="mob-task-name">'+escHtml(t.task_name)+' <span style="color:var(--muted);font-weight:400;">('+escHtml(t.task_unit||'')+')</span></div>';
        html += '<div class="mob-task-act">'+escHtml(t.activity_name)+'</div></div>';
        html += '</label>';
      });
      $('#mob-task-sheet-body').html(html);
    },
    error:function(){ $('#mob-task-sheet-body').html('<p style="text-align:center;padding:20px;color:#c0392b;">Failed to load tasks.</p>'); }
  });
});

$(document).on('click','[data-close]', function(){
  $('#'+$(this).data('close')).removeClass('show');
});

$('#mob-task-sheet-save').on('click', function(){
  var $r = $('input[name="mob-task-radio"]:checked');
  if(!$r.length){ toast('Please select a task'); return; }
  var id = _taskPickerFor;
  _selected[id] = _selected[id] || {};
  _selected[id].task_id = $r.val();
  _selected[id].task_name = $r.data('task-name');
  _selected[id].activity_id = $r.data('activity-id') || 0;
  _selected[id].activity_name = $r.data('activity-name') || '';
  $('#mob-task-sheet').removeClass('show');
  render();
});

// Raise Indent — progress-gate check, then confirm sheet
$('#mob-raise-btn').on('click', function(){
  var ids = Object.keys(_selected);
  if(!ids.length){ toast('Select at least one resource'); return; }
  var missingTask = ids.filter(function(id){ return !_selected[id].task_id; });
  if(missingTask.length){
    var names = missingTask.map(function(id){ return (_allRows.filter(function(x){return String(x.resource_id)===id;})[0]||{}).resource_name || id; });
    toast('Select a task for: ' + names.join(', '), 3000);
    return;
  }

  var checkItems = ids.map(function(id){ return {id:id, task_id:_selected[id].task_id}; });
  var $btn = $(this).prop('disabled', true);
  $.ajax({
    type:'POST', url:'../storekeeper/checkindentprogress', dataType:'json',
    data:{items: JSON.stringify(checkItems)},
    success:function(d){
      $btn.prop('disabled', false);
      if(d.error !== 'No'){ toast(d.errortext || 'Error checking progress.'); return; }
      if(d.blocked && d.blocked.length){
        toast('Report today\'s progress first for: ' + d.blocked.join(', '), 4000);
        return;
      }
      openConfirmSheet(ids);
    },
    error:function(){ $btn.prop('disabled', false); toast('Failed to check progress.'); }
  });
});

function openConfirmSheet(ids){
  var html = '';
  ids.forEach(function(id){
    var row = _allRows.filter(function(x){ return String(x.resource_id)===id; })[0] || {};
    var sel = _selected[id];
    html += '<div class="mob-confirm-row" data-id="'+id+'">';
    html += '<div class="mob-res-name">'+escHtml(row.resource_name)+'</div>';
    html += '<div class="mob-task-act">'+escHtml(sel.task_name)+' &middot; '+escHtml(sel.activity_name)+'</div>';
    html += '<div class="mob-form-row">';
    html += '<div class="mob-form-field"><label>Stock at Site</label><input type="number" step="any" min="0" class="mob-confirm-stock" placeholder="0">'+'</div>';
    html += '<div class="mob-form-field"><label>Re-order Qty</label><input type="number" step="any" min="0" class="mob-confirm-reorder" value="'+(sel.reorder||0)+'"></div>';
    html += '</div></div>';
  });
  $('#mob-confirm-sheet-body').html(html);
  $('#mob-confirm-sheet').addClass('show');
}

$('#mob-confirm-submit').on('click', function(){
  var items = [];
  $('#mob-confirm-sheet-body .mob-confirm-row').each(function(){
    var id = String($(this).data('id'));
    var sel = _selected[id];
    items.push({
      id: id,
      task_id: sel.task_id,
      task_name: sel.task_name,
      activity_id: sel.activity_id,
      stock: parseFloat($(this).find('.mob-confirm-stock').val()) || 0,
      reorder: parseFloat($(this).find('.mob-confirm-reorder').val()) || 0
    });
  });
  var $btn = $(this).prop('disabled', true).text('Saving…');
  $.ajax({
    type:'POST', url:'../storekeeper/raiseindent', dataType:'json',
    data:{items: JSON.stringify(items)},
    success:function(d){
      $btn.prop('disabled', false).text('Submit');
      if(d.error !== 'No'){ toast(d.errortext || 'Error raising indent.'); return; }
      $('#mob-confirm-sheet').removeClass('show');
      toast('Indent raised successfully!');
      _selected = {};
      setTimeout(loadResources, 800);
    },
    error:function(){ $btn.prop('disabled', false).text('Submit'); toast('Failed. Please try again.'); }
  });
});

loadResources();

})();
</script>
