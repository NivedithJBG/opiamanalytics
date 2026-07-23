<?php
$this->title = 'Site Office';
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

/* ── Shell ── */
#so-app{display:flex;flex-direction:column;height:100vh;overflow:hidden;background:var(--bg)}

/* ── Topbar ── */
#so-topbar{
  background:var(--slate);
  padding:env(safe-area-inset-top,0px) 16px 12px;
  padding-top:calc(env(safe-area-inset-top,0px) + 14px);
  flex-shrink:0;display:flex;align-items:center;gap:12px;
  border-bottom:1px solid rgba(0,0,0,.15);
}
#so-back-btn{background:none;border:none;color:var(--slate-text);font-size:22px;cursor:pointer;padding:4px;display:none;line-height:1}
#so-topbar .so-title{flex:1;color:var(--slate-text);font-size:17px;font-weight:700;letter-spacing:.2px}
#so-topbar .so-proj{font-size:11px;color:rgba(241,245,249,.65);margin-top:3px}

/* ── Scrollable content ── */
#so-content{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding:24px 16px 90px;background:var(--bg)}

/* ── Home oval buttons ── */
.so-home-list{display:flex;flex-direction:column;gap:14px;margin-top:12px}
.so-oval-btn{
  display:flex;align-items:center;gap:16px;
  background:var(--card);border:1px solid var(--border);
  border-radius:50px;padding:18px 24px;
  cursor:pointer;transition:transform .15s,box-shadow .15s;
  box-shadow:0 2px 6px rgba(0,0,0,.07);
}
.so-oval-btn:active{transform:scale(.98);box-shadow:none}
.so-oval-icon{font-size:26px;line-height:1;flex-shrink:0}
.so-oval-label{flex:1}
.so-oval-label strong{display:block;font-size:15px;font-weight:700;color:var(--text);letter-spacing:.2px}
.so-oval-label span{font-size:11px;color:var(--muted);margin-top:2px;display:block}
.so-oval-arrow{font-size:16px;color:var(--label)}

/* ── Section header ── */
.so-section-hdr{
  background:var(--slate);color:var(--slate-text);
  font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;
  padding:7px 14px;border-radius:8px;margin:14px 0 6px;
}
.so-section-hdr:first-child{margin-top:0}

/* ── Cards ── */
.so-card{
  background:var(--card);border-radius:12px;border:1px solid var(--border);
  margin-bottom:8px;overflow:hidden;
  box-shadow:0 1px 3px rgba(0,0,0,.06);
}
.so-card.warn{border-left:4px solid var(--amber)}
.so-card.blocked{border-left:4px solid var(--red)}
.so-card.ok{border-left:4px solid var(--green)}
.so-card.selected{border-left:4px solid var(--accent);background:#e8ecf0}
.so-card-top{padding:13px 14px;display:flex;align-items:flex-start;gap:10px;cursor:pointer}
.so-card-name{flex:1;font-size:14px;font-weight:600;color:var(--text);line-height:1.3}
.so-card-meta{display:flex;gap:10px;margin-top:5px;flex-wrap:wrap}
.so-card-meta span{font-size:11px;color:var(--muted)}
.so-card-meta strong{color:var(--text)}
.so-badge{flex-shrink:0;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;align-self:flex-start;margin-top:2px}
.so-badge.est-reached{background:rgba(220,38,38,.1);color:var(--red)}
.so-badge.reorder{background:rgba(217,119,6,.1);color:var(--amber)}
.so-badge.ok{background:rgba(22,163,74,.1);color:var(--green)}
.so-badge.grn-done{background:rgba(220,38,38,.1);color:var(--red)}

/* ── Inline form (opens inside card, slightly darker) ── */
.so-form{display:none;padding:0 14px 14px;border-top:1px solid var(--border);background:var(--card2)}
.so-form.open{display:block}
.so-form-row{display:flex;gap:10px;margin-top:10px;flex-wrap:wrap}
.so-field{display:flex;flex-direction:column;flex:1;min-width:130px}
.so-field.full{flex:0 0 100%}
.so-field label{font-size:10px;font-weight:700;color:var(--slate2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px}
.so-field input,.so-field select{
  padding:10px 12px;font-size:14px;color:var(--text);
  border:1.5px solid var(--border);border-radius:8px;
  background:#fff;font-family:'Inter',sans-serif;
  outline:none;width:100%;transition:border-color .15s;
}
.so-field input:focus,.so-field select:focus{border-color:var(--accent)}
.so-field input[readonly]{background:var(--bg);color:var(--muted)}
.so-info-box{font-size:11px;color:var(--muted);margin-top:8px;padding:10px 12px;background:#fff;border-radius:8px;border:1px solid var(--border);line-height:1.6}
.so-info-box strong{color:var(--text)}

/* ── Form actions ── */
.so-actions{display:flex;gap:8px;margin-top:14px}
.so-btn{flex:1;padding:12px;border:none;border-radius:10px;font-size:14px;font-weight:600;font-family:'Inter',sans-serif;cursor:pointer;transition:opacity .15s}
.so-btn:active{opacity:.8}
.so-btn-primary{background:var(--accent);color:#fff}
.so-btn-success{background:var(--green);color:#fff}
.so-btn-cancel{background:#fff;color:var(--muted);border:1px solid var(--border)}
.so-btn-draft{background:var(--amber);color:#fff;font-size:12px}

/* ── Alert box ── */
.so-alert{
  background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.25);border-radius:8px;
  padding:12px 14px;margin:10px 0;font-size:12px;color:#b91c1c;line-height:1.5;
}
.so-alert strong{display:block;margin-bottom:4px;font-size:13px;color:var(--red)}

/* ── GRN items table ── */
.so-items-table{width:100%;border-collapse:collapse;margin-top:10px;font-size:12px}
.so-items-table th{background:var(--slate);color:var(--slate-text);padding:8px;text-align:left;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.5px}
.so-items-table td{padding:8px;border-bottom:1px solid var(--border);vertical-align:middle;color:var(--text)}
.so-items-table input{border:1.5px solid var(--border);border-radius:6px;padding:6px 8px;font-size:13px;width:100%;outline:none;background:#fff;color:var(--text)}
.so-items-table input:focus{border-color:var(--accent)}
.so-items-table input[readonly]{background:var(--bg);color:var(--muted)}

/* ── MB activities ── */
.so-mb-act{background:var(--card);border:1px solid var(--border);border-radius:10px;margin-bottom:14px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.06)}
.so-mb-act-hdr{background:var(--slate);padding:9px 14px;font-size:13px;font-weight:700;color:var(--slate-text)}

/* ── MB stats row (Unit | Billed | Current | Remaining | Last Reported) ── */
.mb-stats-row{display:flex;border-bottom:1px solid var(--border);background:var(--card2)}
.mb-sc{flex:1;padding:8px 6px;text-align:center;border-right:1px solid var(--border);min-width:0}
.mb-sc:last-child{border-right:none}
.mb-sc-lbl{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--slate2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mb-sc-val{font-size:13px;font-weight:700;color:var(--text);margin-top:3px;white-space:nowrap}
.mb-sc-input{flex:1.4}

/* ── Loading / empty ── */
.so-loading{text-align:center;padding:40px 20px;color:var(--muted);font-size:14px}
.so-spinner{width:32px;height:32px;border:3px solid var(--border);border-top-color:var(--accent);border-radius:50%;animation:spin .8s linear infinite;margin:0 auto 12px}
@keyframes spin{to{transform:rotate(360deg)}}

/* ── Toast ── */
#so-toast{
  position:fixed;bottom:calc(env(safe-area-inset-bottom,0px) + 80px);left:50%;transform:translateX(-50%);
  background:var(--slate);color:var(--slate-text);padding:10px 22px;border-radius:24px;
  font-size:13px;font-weight:600;white-space:nowrap;
  opacity:0;transition:opacity .25s;pointer-events:none;z-index:999;
  box-shadow:0 4px 12px rgba(0,0,0,.2);
}
#so-toast.show{opacity:1}

/* ── Bottom nav ── */
#so-bottom-nav{
  position:fixed;bottom:0;left:0;right:0;
  background:var(--slate);border-top:1px solid rgba(0,0,0,.15);
  padding:8px 0 calc(env(safe-area-inset-bottom,0px) + 6px);
  display:flex;justify-content:space-around;z-index:100;
}
.so-nav-item{display:flex;flex-direction:column;align-items:center;gap:2px;font-size:9px;font-weight:600;color:rgba(241,245,249,.55);text-transform:uppercase;letter-spacing:.4px;cursor:pointer;padding:0 10px;text-decoration:none}
.so-nav-item.active{color:var(--slate-text)}
.so-nav-icon{font-size:20px;line-height:1}
</style>

<div id="so-app">

  <!-- Topbar -->
  <div id="so-topbar">
    <button id="so-back-btn" onclick="soGoHome()">&#8592;</button>
    <div>
      <div class="so-title" id="so-screen-title">Site Office</div>
      <div class="so-proj"><?= htmlspecialchars($projectName) ?></div>
    </div>
  </div>

  <!-- Content -->
  <div id="so-content">

    <!-- HOME -->
    <div id="screen-home">
      <div style="font-size:12px;color:var(--muted);margin-bottom:18px;text-align:center;letter-spacing:.3px;text-transform:uppercase;font-weight:600;">Select a module</div>
      <div class="so-home-list">
        <div class="so-oval-btn" onclick="soOpenModule('indent')">
          <div class="so-oval-icon">&#128230;</div>
          <div class="so-oval-label">
            <strong>Indents</strong>
            <span>Raise material requisition</span>
          </div>
          <div class="so-oval-arrow">&#8250;</div>
        </div>
        <div class="so-oval-btn" onclick="soOpenModule('grn')">
          <div class="so-oval-icon">&#9989;</div>
          <div class="so-oval-label">
            <strong>Goods Received Note</strong>
            <span>Record incoming materials</span>
          </div>
          <div class="so-oval-arrow">&#8250;</div>
        </div>
        <div class="so-oval-btn" onclick="soOpenModule('mbook')">
          <div class="so-oval-icon">&#128214;</div>
          <div class="so-oval-label">
            <strong>Measurement Book</strong>
            <span>Bill contractor work</span>
          </div>
          <div class="so-oval-arrow">&#8250;</div>
        </div>
      </div>
    </div>

    <!-- INDENT MODULE -->
    <div id="screen-indent" style="display:none">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
        <div style="font-size:13px;color:var(--muted)">Select resources and raise indent</div>
        <button class="so-btn so-btn-primary" style="flex:none;padding:8px 16px;font-size:12px" onclick="soSubmitIndent()">Raise Indent</button>
      </div>
      <div id="indent-body"><div class="so-loading"><div class="so-spinner"></div>Loading resources…</div></div>
    </div>

    <!-- GRN MODULE -->
    <div id="screen-grn" style="display:none">
      <div style="font-size:13px;color:var(--muted);margin-bottom:10px">Select a Purchase Order to raise GRN</div>
      <div id="grn-body"><div class="so-loading"><div class="so-spinner"></div>Loading orders…</div></div>
    </div>

    <!-- MBOOK MODULE -->
    <div id="screen-mbook" style="display:none">
      <div style="font-size:13px;color:var(--muted);margin-bottom:10px">Select a Work Order to create Measurement Book</div>
      <div id="mbook-body"><div class="so-loading"><div class="so-spinner"></div>Loading work orders…</div></div>
    </div>

    <!-- GRN FORM -->
    <div id="screen-grn-form" style="display:none">
      <div id="grn-form-body"></div>
    </div>

    <!-- MBOOK FORM -->
    <div id="screen-mbook-form" style="display:none">
      <div id="mbook-form-body"></div>
    </div>

  </div>
</div>

<!-- Toast -->
<div id="so-toast"></div>

<!-- Bottom nav -->
<div id="so-bottom-nav">
  <a class="so-nav-item active" href="<?= Yii::$app->urlManager->createUrl('site-office/mobile') ?>">
    <span class="so-nav-icon">&#127963;</span>Site Office
  </a>
  <a class="so-nav-item" href="<?= Yii::$app->urlManager->createUrl('report/mobile') ?>">
    <span class="so-nav-icon">&#128203;</span>Progress
  </a>
  <a class="so-nav-item" href="<?= Yii::$app->urlManager->createUrl('site/logout') ?>" data-method="post">
    <span class="so-nav-icon">&#128275;</span>Logout
  </a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function(){
'use strict';

var _currentModule = 'home';
var _selectedIndents = {}; // resource_id -> {task_id, stock, reorder}
var _indentRows = [];
var _grnOrders = [];
var _mbOrders = [];
var _currentGrnOrder = null;
var _currentMbWo = null;

// ── Navigation ──────────────────────────────────────────────────────────────

window.soGoHome = function(){
  showScreen('home');
  document.getElementById('so-screen-title').textContent = 'Site Office';
  document.getElementById('so-back-btn').style.display = 'none';
  _currentModule = 'home';
};

window.soOpenModule = function(mod){
  _currentModule = mod;
  document.getElementById('so-back-btn').style.display = 'block';
  if(mod === 'indent'){
    document.getElementById('so-screen-title').textContent = 'Raise Indent';
    showScreen('indent');
    loadIndents();
  } else if(mod === 'grn'){
    document.getElementById('so-screen-title').textContent = 'Goods Received Note';
    showScreen('grn');
    loadGrnOrders();
  } else if(mod === 'mbook'){
    document.getElementById('so-screen-title').textContent = 'Measurement Book';
    showScreen('mbook');
    loadMbOrders();
  }
};

function showScreen(name){
  ['home','indent','grn','mbook','grn-form','mbook-form'].forEach(function(s){
    document.getElementById('screen-'+s).style.display = (s===name)?'block':'none';
  });
}

// ── Toast ────────────────────────────────────────────────────────────────────

function toast(msg, dur){
  var t = document.getElementById('so-toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(function(){ t.classList.remove('show'); }, dur||2500);
}

function escHtml(s){
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ════════════════════════════════════════════════════════════════════════════
// INDENT MODULE
// ════════════════════════════════════════════════════════════════════════════

function loadIndents(){
  $('#indent-body').html('<div class="so-loading"><div class="so-spinner"></div>Loading resources…</div>');
  _selectedIndents = {};
  $.ajax({
    type:'POST', url:'../storekeeper/indents', dataType:'json',
    success:function(d){
      if(d.error !== 'No'){ $('#indent-body').html('<div class="so-loading">'+(d.errortext||'No resources found.')+'</div>'); return; }
      _indentRows = d.rows || [];
      renderIndents();
    },
    error:function(){ $('#indent-body').html('<div class="so-loading">Failed to load. Please refresh.</div>'); }
  });
}

function renderIndents(){
  if(!_indentRows.length){
    $('#indent-body').html('<div class="so-loading">No allocated resources found for this project.</div>');
    return;
  }
  var html = '';
  var lastType = null;
  _indentRows.forEach(function(r){
    if(r.resource_type !== lastType){
      lastType = r.resource_type;
      html += '<div class="so-section-hdr">'+escHtml(r.resource_type)+'</div>';
    }
    var estReached = r.estimate_reached == 1;
    var stock = parseFloat(r.stock||0);
    var reorderLevel = parseFloat(r.reorder_level||0);
    var belowReorder = reorderLevel > 0 && stock <= reorderLevel;
    var cardClass = estReached ? 'blocked' : (belowReorder ? 'warn' : '');
    var badge = estReached
      ? '<span class="so-badge est-reached">Est. Reached</span>'
      : (belowReorder ? '<span class="so-badge reorder">Reorder</span>' : '');

    html += '<div class="so-card '+cardClass+'" id="ic-'+r.resource_id+'">';
    html += '<div class="so-card-top" onclick="soToggleIndentCard('+r.resource_id+','+escHtml(JSON.stringify(estReached))+')">';
    html += '<div><div class="so-card-name">'+escHtml(r.resource_name)+'</div>';
    html += '<div class="so-card-meta">';
    html += '<span>Unit: <strong>'+escHtml(r.unit||'—')+'</strong></span>';
    html += '<span>Purchased: <strong>'+parseFloat(r.purchased_quantity||0).toFixed(2)+'</strong></span>';
    html += '<span>Consumed: <strong>'+parseFloat(r.consumed_quantity||0).toFixed(2)+'</strong></span>';
    html += '<span>Stock: <strong>'+stock.toFixed(2)+'</strong></span>';
    html += '</div></div>';
    html += badge;
    html += '</div>'; // card-top

    if(!estReached){
      html += '<div class="so-form" id="if-'+r.resource_id+'">';
      html += '<div id="if-tasks-'+r.resource_id+'"><div class="so-loading" style="padding:20px 0">Loading tasks…</div></div>';
      html += '<div class="so-form-row">';
      html += '<div class="so-field"><label>Stock at Site</label><input type="number" id="i-stock-'+r.resource_id+'" value="'+stock.toFixed(2)+'" step="0.001" min="0"></div>';
      html += '<div class="so-field"><label>Reorder Qty</label><input type="number" id="i-reorder-'+r.resource_id+'" value="'+(r.lot_size||'').toString()+'" step="0.001" min="0" placeholder="Enter qty"></div>';
      html += '</div>';
      html += '<div class="so-actions">';
      html += '<button class="so-btn so-btn-cancel" onclick="soCloseIndentCard('+r.resource_id+')">Cancel</button>';
      html += '<button class="so-btn so-btn-primary" onclick="soSelectIndent('+r.resource_id+')">Select</button>';
      html += '</div>';
      html += '</div>'; // form
    }
    html += '</div>'; // card
  });
  $('#indent-body').html(html);
}

window.soToggleIndentCard = function(rid, estReached){
  if(estReached){ toast('Estimate quantity reached — indent cannot be raised for this resource.'); return; }
  var $form = $('#if-'+rid);
  if($form.hasClass('open')){
    $form.removeClass('open');
  } else {
    $('.so-form.open').removeClass('open');
    $form.addClass('open');
    loadIndentTasks(rid);
    setTimeout(function(){
      var el = document.getElementById('ic-'+rid);
      if(el) el.scrollIntoView({behavior:'smooth',block:'nearest'});
    },200);
  }
};

window.soCloseIndentCard = function(rid){
  $('#if-'+rid).removeClass('open');
};

function loadIndentTasks(rid){
  var $t = $('#if-tasks-'+rid);
  if($t.data('loaded')) return;
  $.ajax({
    type:'POST', url:'../storekeeper/getresourcetasks', dataType:'json',
    data:{resource_id: rid},
    success:function(d){
      $t.data('loaded',true);
      if(!d.tasks || !d.tasks.length){
        $t.html('<div class="so-info-box">No tasks linked to this resource. Please assign tasks from the desktop app first.</div>');
        return;
      }
      var h = '<div class="so-field full" style="margin-top:10px"><label>Linked Task</label><select id="i-task-'+rid+'">';
      d.tasks.forEach(function(tk){
        h += '<option value="'+escHtml(String(tk.task_id))+'">'+escHtml(tk.activity_name+' → '+tk.task_name)+'</option>';
      });
      h += '</select></div>';
      $t.html(h);
    },
    error:function(){ $t.html('<div class="so-info-box">Could not load tasks.</div>'); }
  });
}

window.soSelectIndent = function(rid){
  var taskEl = document.getElementById('i-task-'+rid);
  var taskId = taskEl ? taskEl.value : '';
  var stock = parseFloat(document.getElementById('i-stock-'+rid).value)||0;
  var reorder = parseFloat(document.getElementById('i-reorder-'+rid).value)||0;
  if(!taskId){ toast('Please wait for tasks to load and select one.'); return; }
  if(reorder <= 0){ toast('Please enter a reorder quantity.'); return; }
  _selectedIndents[rid] = {task_id: taskId, stock: stock, reorder: reorder};
  $('#ic-'+rid).addClass('selected');
  $('#if-'+rid).removeClass('open');
  toast('Resource selected ✓');
};

window.soSubmitIndent = function(){
  var items = [];
  Object.keys(_selectedIndents).forEach(function(rid){
    var s = _selectedIndents[rid];
    items.push({id: rid, task_id: s.task_id, stock: s.stock, reorder: s.reorder});
  });
  if(!items.length){ toast('Please select at least one resource.'); return; }

  // Check progress first
  $.ajax({
    type:'POST', url:'../storekeeper/checkindentprogress', dataType:'json',
    data:{items: JSON.stringify(items)},
    success:function(d){
      if(d.blocked && d.blocked.length){
        var msg = '<strong>⚠ Progress Not Reported Today</strong>Cannot raise indent. Please report progress for these activities first:<br>• '+d.blocked.join('<br>• ');
        $('#indent-body').prepend('<div class="so-alert">'+msg+'</div>');
        document.getElementById('so-content').scrollTop = 0;
        return;
      }
      doRaiseIndent(items);
    },
    error:function(){ toast('Could not verify progress. Please try again.'); }
  });
};

function doRaiseIndent(items){
  $.ajax({
    type:'POST', url:'../storekeeper/raiseindent', dataType:'json',
    data:{items: JSON.stringify(items)},
    success:function(d){
      if(d.error === 'No'){
        toast('Indent raised successfully!');
        _selectedIndents = {};
        setTimeout(loadIndents, 800);
      } else {
        toast(d.errortext || 'Error raising indent.');
      }
    },
    error:function(){ toast('Network error. Please try again.'); }
  });
}

// ════════════════════════════════════════════════════════════════════════════
// GRN MODULE
// ════════════════════════════════════════════════════════════════════════════

function loadGrnOrders(){
  $('#grn-body').html('<div class="so-loading"><div class="so-spinner"></div>Loading purchase orders…</div>');
  $.ajax({
    type:'POST', url:'../storekeeper/issued', dataType:'json',
    success:function(d){
      if(d.error !== 'No' || !d.rows || !d.rows.length){
        $('#grn-body').html('<div class="so-loading">No purchase orders found.</div>');
        return;
      }
      _grnOrders = d.rows;
      renderGrnOrders();
    },
    error:function(){
      $('#grn-body').html('<div class="so-loading">Failed to load purchase orders.</div>');
    }
  });
}

function renderGrnOrders(){
  var html = '';
  _grnOrders.forEach(function(o){
    var grnDone = o.grn_fully_received == 1;
    var cancelled = o.cancelled == 1;
    var cardClass = cancelled ? 'blocked' : (grnDone ? 'warn' : 'ok');
    var badge = cancelled
      ? '<span class="so-badge grn-done">Cancelled</span>'
      : (grnDone ? '<span class="so-badge reorder">GRN Done</span>' : '<span class="so-badge ok">Open</span>');
    html += '<div class="so-card '+cardClass+'" onclick="soOpenGrnForm('+o.order_id+','+(grnDone||cancelled)+')">';
    html += '<div class="so-card-top">';
    html += '<div><div class="so-card-name">'+escHtml(o.ordernumber||'—')+'</div>';
    html += '<div class="so-card-meta">';
    html += '<span>Vendor: <strong>'+escHtml(o.vendor_name||'—')+'</strong></span>';
    html += '<span>Date: <strong>'+escHtml(o.orderdate||'—')+'</strong></span>';
    if(o.first_item) html += '<span>Item: <strong>'+escHtml(o.first_item)+'</strong></span>';
    html += '</div></div>';
    html += badge;
    html += '</div></div>';
  });
  if(!html) html = '<div class="so-loading">No purchase orders found.</div>';
  $('#grn-body').html(html);
}

window.soOpenGrnForm = function(orderId, grnDone){
  if(grnDone){ toast('This order is closed or already received.'); return; }
  _currentGrnOrder = orderId;
  document.getElementById('so-screen-title').textContent = 'GRN';
  showScreen('grn-form');
  $('#grn-form-body').html('<div class="so-loading"><div class="so-spinner"></div>Loading order items…</div>');

  // Find the order meta from the already-loaded list
  var orderMeta = null;
  for(var i=0;i<_grnOrders.length;i++){
    if(_grnOrders[i].order_id == orderId){ orderMeta = _grnOrders[i]; break; }
  }

  $.when(
    $.ajax({type:'POST', url:'../storekeeper/grnitems', dataType:'json', data:{order_id: orderId}}),
    $.ajax({type:'POST', url:'../storekeeper/grnnext', dataType:'json'})
  ).done(function(itemsRes, nextRes){
    var items = itemsRes[0];
    var next  = nextRes[0];
    if(items.error !== 'No'){ $('#grn-form-body').html('<div class="so-loading">'+(items.errortext||'Error loading items.')+'</div>'); return; }
    // Inject vendor/PO from the list we already have
    if(orderMeta){
      items.vendor_name  = orderMeta.vendor_name;
      items.order_number = orderMeta.ordernumber;
    }
    renderGrnForm(items, next);
  }).fail(function(){
    $('#grn-form-body').html('<div class="so-loading">Failed to load order items.</div>');
  });
};

function renderGrnForm(data, next){
  var grnNum = next.grn_number || '—';
  var rows = data.rows || [];
  var html = '';

  html += '<div style="background:var(--slate);border-radius:10px;padding:10px 14px;margin-bottom:12px">';
  html += '<div style="display:flex;gap:20px">';
  html += '<div style="display:flex;flex-direction:column;gap:5px;white-space:nowrap">';
  html += '<div><span style="font-size:10px;color:rgba(241,245,249,.6)">GRN: </span><span style="font-size:13px;font-weight:700;color:var(--slate-text)">'+escHtml(grnNum)+'</span></div>';
  html += '<div><span style="font-size:10px;color:rgba(241,245,249,.6)">PO: </span><span style="font-size:12px;font-weight:600;color:var(--slate-text)">'+escHtml(data.order_number||'—')+'</span></div>';
  html += '</div>';
  html += '<div style="display:flex;flex-direction:column;gap:5px;flex:1;min-width:0">';
  html += '<div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><span style="font-size:10px;color:rgba(241,245,249,.6)">Vendor: </span><span style="font-size:12px;font-weight:600;color:var(--slate-text)">'+escHtml(data.vendor_name||'—')+'</span></div>';
  html += '<div><span style="font-size:10px;color:rgba(241,245,249,.6)">Date: </span><input type="date" id="grn-date" value="'+new Date().toISOString().split("T")[0]+'" style="background:transparent;border:none;color:var(--slate-text);font-size:12px;font-family:Inter,sans-serif;outline:none;padding:0;font-weight:600"></div>';
  html += '</div>';
  html += '</div>';
  html += '</div>';

  html += '<table class="so-items-table" style="margin-top:14px">';
  html += '<thead><tr><th>Resource</th><th>Unit</th><th>Ordered</th><th>Received</th></tr></thead><tbody>';
  rows.forEach(function(r,i){
    var orderedQty = parseFloat(r.ordered_qty||0);
    var received   = parseFloat(r.total_received||0);
    var remaining  = Math.max(0, orderedQty - received).toFixed(3);
    var rate       = parseFloat(r.rate||0);
    html += '<tr>';
    html += '<td style="font-size:12px;font-weight:600">'+escHtml(r.resource_name)+'</td>';
    html += '<td style="font-size:11px;color:var(--muted)">'+escHtml(r.unit||'—')+'</td>';
    html += '<td style="font-size:12px;text-align:right;color:var(--text);font-weight:600">'+orderedQty.toFixed(2)+'</td>';
    html += '<td><input type="number" id="grn-qty-'+r.resource_id+'" data-rid="'+r.resource_id+'" data-rate="'+rate+'" data-max="'+remaining+'" placeholder="0.00" step="0.001" min="0" max="'+remaining+'" class="grn-qty-input" style="min-width:90px"></td>';
    html += '</tr>';
    if(received > 0){
      html += '<tr><td colspan="4" style="font-size:10px;color:var(--text);font-weight:600;padding:2px 8px 6px">Previously received: '+received.toFixed(2)+' &nbsp;|&nbsp; Remaining: '+remaining+'</td></tr>';
    }
  });
  html += '</tbody></table>';

  html += '<div class="so-form-row">';
  html += '<div class="so-field full"><label>Remarks</label><input type="text" id="grn-remarks" placeholder="Optional remarks"></div>';
  html += '</div>';

  html += '<div class="so-actions" style="margin-top:16px">';
  html += '<button class="so-btn so-btn-cancel" onclick="soBackToGrn()">Cancel</button>';
  html += '<button class="so-btn so-btn-success" onclick="soSaveGrn(\''+escHtml(grnNum)+'\',\''+escHtml(JSON.stringify(rows).replace(/'/g,"\\\'"))+'\')">Submit GRN</button>';
  html += '</div>';

  $('#grn-form-body').html(html);
}

window.soBackToGrn = function(){
  document.getElementById('so-screen-title').textContent = 'Goods Received Note';
  showScreen('grn');
};

window.soSaveGrn = function(grnNum, rowsJson){
  var rows = JSON.parse(rowsJson);
  var date = document.getElementById('grn-date').value;
  var remarks = document.getElementById('grn-remarks').value;
  if(!date){ toast('Please select date of receipt.'); return; }

  var items = [];
  var hasQty = false;
  var error = '';
  rows.forEach(function(r){
    var qtyEl = document.getElementById('grn-qty-'+r.resource_id);
    if(!qtyEl) return;
    var qty = parseFloat(qtyEl.value)||0;
    var max = parseFloat(qtyEl.dataset.max)||0;
    if(qty > 0){
      if(qty > max + 0.001){
        error = 'Quantity for "'+r.resource_name+'" ('+qty+') exceeds remaining ordered quantity ('+max+').';
      }
      hasQty = true;
      items.push({resource_id: r.resource_id, qty: qty, rate: parseFloat(qtyEl.dataset.rate)||0});
    }
  });

  if(error){ toast(error); return; }
  if(!hasQty){ toast('Please enter received quantity for at least one item.'); return; }

  var $btn = $('#grn-form-body .so-btn-success');
  $btn.text('Saving…').attr('disabled',true);

  $.ajax({
    type:'POST', url:'../storekeeper/savegrn', dataType:'json',
    data:{
      order_id: _currentGrnOrder,
      items: JSON.stringify(items),
      remarks: remarks,
      date_of_receipt: date,
      grn_number: grnNum
    },
    success:function(d){
      $btn.text('Submit GRN').removeAttr('disabled');
      if(d.error === 'No'){
        toast('GRN saved successfully!');
        soBackToGrn();
        setTimeout(loadGrnOrders, 800);
      } else {
        toast(d.errortext || 'Error saving GRN.');
      }
    },
    error:function(){
      $btn.text('Submit GRN').removeAttr('disabled');
      toast('Network error. Please try again.');
    }
  });
};

// ════════════════════════════════════════════════════════════════════════════
// MEASUREMENT BOOK MODULE
// ════════════════════════════════════════════════════════════════════════════

function loadMbOrders(){
  $('#mbook-body').html('<div class="so-loading"><div class="so-spinner"></div>Loading work orders…</div>');
  $.ajax({
    type:'POST', url:'../storekeeper/issuedwo', dataType:'json',
    success:function(d){
      if(d.error !== 'No' || !d.rows || !d.rows.length){
        $('#mbook-body').html('<div class="so-loading">No work orders found.</div>');
        return;
      }
      _mbOrders = d.rows;
      renderMbOrders();
    },
    error:function(){ $('#mbook-body').html('<div class="so-loading">Failed to load work orders.</div>'); }
  });
}

function renderMbOrders(){
  var html = '';
  _mbOrders.forEach(function(o){
    var cancelled = o.cancelled == 1;
    var cardClass = cancelled ? 'blocked' : 'ok';
    var badge = cancelled
      ? '<span class="so-badge grn-done">Cancelled</span>'
      : '<span class="so-badge ok">Active</span>';
    html += '<div class="so-card '+cardClass+'" onclick="soOpenMbForm(\''+escHtml(o.WO_Number)+'\','+cancelled+')">';
    html += '<div class="so-card-top">';
    html += '<div><div class="so-card-name">'+escHtml(o.WO_Number)+'</div>';
    html += '<div class="so-card-meta">';
    html += '<span>Vendor: <strong>'+escHtml(o.vendor_name||'—')+'</strong></span>';
    html += '<span>Activity: <strong>'+escHtml(o.activity_name||'—')+'</strong></span>';
    html += '</div></div>';
    html += badge;
    html += '</div></div>';
  });
  if(!html) html = '<div class="so-loading">No work orders found.</div>';
  $('#mbook-body').html(html);
}

window.soOpenMbForm = function(woNumber, cancelled){
  if(cancelled){ toast('This work order has been cancelled.'); return; }
  _currentMbWo = woNumber;
  document.getElementById('so-screen-title').textContent = 'Measurement Book';
  showScreen('mbook-form');
  $('#mbook-form-body').html('<div class="so-loading"><div class="so-spinner"></div>Loading activities…</div>');

  $.when(
    $.ajax({type:'POST', url:'../storekeeper/woactivities', dataType:'json', data:{wo_number: woNumber}}),
    $.ajax({type:'POST', url:'../storekeeper/mbnext', dataType:'json'})
  ).done(function(actRes, nextRes){
    var acts = actRes[0];
    var next = nextRes[0];
    if(acts.error !== 'No'){ $('#mbook-form-body').html('<div class="so-loading">'+(acts.errortext||'Error loading activities.')+'</div>'); return; }
    renderMbForm(acts, next, woNumber);
  }).fail(function(){
    $('#mbook-form-body').html('<div class="so-loading">Failed to load work order activities.</div>');
  });
};

function renderMbForm(data, next, woNumber){
  var mbNum    = next.mb_number || '—';
  var acts     = data.activities || [];
  var hasDraft = data.has_draft;
  var vendor   = data.vendor_name || '—';

  var html = '';

  html += '<div style="background:var(--slate);border-radius:10px;padding:10px 14px;margin-bottom:12px;display:flex;gap:20px">';

  // Left column: MB label + WO label
  html += '<div style="display:flex;flex-direction:column;gap:5px;white-space:nowrap">';
  html += '<div><span style="font-size:10px;color:rgba(241,245,249,.6)">MB: </span><span style="font-size:13px;font-weight:700;color:var(--slate-text)">'+escHtml(mbNum)+'</span></div>';
  html += '<div><span style="font-size:10px;color:rgba(241,245,249,.6)">WO: </span><span style="font-size:12px;font-weight:600;color:var(--slate-text)">'+escHtml(woNumber)+'</span></div>';
  html += '</div>';

  // Right column: Contractor label + Date label — aligned under each other
  html += '<div style="display:flex;flex-direction:column;gap:5px;flex:1;min-width:0">';
  html += '<div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><span style="font-size:10px;color:rgba(241,245,249,.6)">Contractor: </span><span style="font-size:12px;font-weight:600;color:var(--slate-text)">'+escHtml(vendor)+'</span></div>';
  html += '<div><span style="font-size:10px;color:rgba(241,245,249,.6)">Date: </span><input type="date" id="mb-date" value="'+new Date().toISOString().split("T")[0]+'" style="background:transparent;border:none;color:var(--slate-text);font-size:12px;font-family:Inter,sans-serif;outline:none;padding:0;font-weight:600"></div>';
  html += '</div>';

  html += '</div>';
  if(hasDraft) html += '<div style="color:#fbbf24;font-weight:700;font-size:11px;margin-bottom:8px">⚠ Draft MB exists for this WO</div>';

  if(!acts.length){
    html += '<div class="so-loading" style="padding:20px 0">No activities found in this work order.</div>';
  } else {
    var _totalBilledTillToday = 0;
    acts.forEach(function(act, ai){
      var unit      = act.unit || '—';
      var cumBilled = parseFloat(act.cumulative_qty||0);
      var lastRep   = parseFloat(act.last_reported_qty||0);
      var maxQty    = Math.max(0, lastRep - cumBilled);
      var aid       = act.activity_id;

      html += '<div class="so-mb-act">';

      // ── Activity name bar ──
      html += '<div class="so-mb-act-hdr">'+escHtml(act.activity_name)+'</div>';

      // ── Stats row: Unit | Billed | Current Qty | Progress | R.Qnty ──
      html += '<div class="mb-stats-row">';

      html += '<div class="mb-sc"><div class="mb-sc-lbl">Unit</div>';
      html += '<div class="mb-sc-val">'+escHtml(unit)+'</div></div>';

      html += '<div class="mb-sc"><div class="mb-sc-lbl">Billed</div>';
      html += '<div class="mb-sc-val">'+cumBilled.toFixed(2)+'</div></div>';

      html += '<div class="mb-sc mb-sc-input"><div class="mb-sc-lbl">Current Qty</div>';
      html += '<input type="number" step="any" min="0" max="'+maxQty.toFixed(3)+'" ';
      html += 'id="mb-qty-'+aid+'" data-aid="'+aid+'" data-max="'+maxQty.toFixed(6)+'" ';
      html += 'class="mb-qty-input" placeholder="—" style="width:100%;text-align:center;font-size:14px;font-weight:700;padding:4px;border-radius:6px;border:1.5px solid var(--accent);background:#fff;color:var(--accent);outline:none;">';
      html += '</div>';

      html += '<div class="mb-sc"><div class="mb-sc-lbl">Progress</div>';
      html += '<div class="mb-sc-val">'+lastRep.toFixed(2)+'</div></div>';

      html += '<div class="mb-sc"><div class="mb-sc-lbl">R. Qnty</div>';
      html += '<div class="mb-sc-val mb-rem-display" id="mb-rem-'+aid+'" style="color:'+(maxQty<=0?'var(--red)':'var(--green)')+'">'+maxQty.toFixed(2)+'</div></div>';

      html += '</div>'; // mb-stats-row

      // ── Tasks ──
      if(act.tasks && act.tasks.length){
        var hiddenInputs = '';
        html += '<div style="padding:6px 10px 10px;background:var(--card2)">';

        act.tasks.forEach(function(tk, ti){
          var tqpu   = parseFloat(tk.unit_qty||tk.task_qty_per_unit||0);
          var tkRate = parseFloat(tk.rate||0);
          var tkUnit = escHtml(tk.task_unit||tk.unit||unit);
          var cumWd  = parseFloat(tk.cum_work_done||0);
          var cumAmt = parseFloat(tk.cum_amount||0);
          _totalBilledTillToday += cumAmt;

          html += '<div style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:8px 10px;margin-bottom:6px;box-shadow:0 1px 2px rgba(0,0,0,.04)">';

          // Row 1: Task name + Unit
          html += '<div style="display:flex;align-items:baseline;justify-content:space-between;gap:8px">';
          html += '<div style="font-size:13px;font-weight:600;color:var(--text);flex:1;line-height:1.3">'+escHtml(tk.task_name||'—')+'</div>';
          html += '<div style="font-size:11px;font-weight:600;color:var(--slate2);white-space:nowrap;background:var(--card2);padding:2px 7px;border-radius:10px">'+tkUnit+'</div>';
          html += '</div>';

          // Row 2: Work Done | Unit | Rate | Amount
          html += '<div style="display:flex;gap:0;margin-top:8px;border:1px solid var(--border);border-radius:7px;overflow:hidden">';

          html += '<div style="flex:1.2;padding:5px 7px;border-right:1px solid var(--border);text-align:center">';
          html += '<div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--slate2)">Work Done</div>';
          html += '<div style="font-size:15px;font-weight:700;color:var(--accent);margin-top:2px" id="mb-task-qty-'+aid+'-'+tk.task_id+'">—</div>';
          html += '</div>';

          html += '<div style="flex:.7;padding:5px 6px;border-right:1px solid var(--border);text-align:center">';
          html += '<div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--slate2)">Unit</div>';
          html += '<div style="font-size:12px;font-weight:600;color:var(--muted);margin-top:2px">'+tkUnit+'</div>';
          html += '</div>';

          html += '<div style="flex:.8;padding:5px 6px;border-right:1px solid var(--border);text-align:center">';
          html += '<div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--slate2)">Rate</div>';
          html += '<div style="font-size:12px;font-weight:600;color:var(--muted);margin-top:2px">'+tkRate.toFixed(2)+'</div>';
          html += '</div>';

          html += '<div style="flex:1.2;padding:5px 7px;text-align:center">';
          html += '<div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--slate2)">Amount</div>';
          html += '<div style="font-size:15px;font-weight:700;color:var(--green);margin-top:2px" id="mb-task-amt-'+aid+'-'+tk.task_id+'">—</div>';
          html += '</div>';

          html += '</div>'; // row 2

          // Past records — Qty Till Today & Amt Till Today inline
          if(cumWd > 0 || cumAmt > 0){
            html += '<div style="display:flex;gap:16px;margin-top:6px;padding:3px 8px;background:var(--bg);border-radius:6px;align-items:center">';
            html += '<span style="font-size:10px;color:var(--label)">Qty Till Today: <strong style="color:var(--muted)">'+cumWd.toFixed(2)+'</strong></span>';
            html += '<span style="font-size:10px;color:var(--label)">Amt Till Today: <strong style="color:var(--muted)">₹'+cumAmt.toFixed(2)+'</strong></span>';
            html += '</div>';
          }

          html += '</div>'; // task card

          hiddenInputs += '<input type="hidden" id="mb-task-'+aid+'-'+tk.task_id+'" data-aid="'+aid+'" data-tid="'+tk.task_id+'" data-tqpu="'+tqpu+'" data-rate="'+tkRate+'" class="mb-task-input" value="0">';
        });

        html += '</div>'; // padding wrapper
        html += '<div id="mb-task-inputs-'+aid+'">'+hiddenInputs+'</div>';
      } else {
        html += '<div style="padding:10px 14px;font-size:12px;color:var(--muted)">No tasks.</div>';
      }

      html += '</div>'; // so-mb-act
    });

    // ── Summary panel ──
    html += '<div id="mb-summary-panel" style="background:#e2e5e9;border:1px solid #d1d5db;border-radius:8px;padding:6px 14px;margin-top:6px">';
    html += '<div style="display:flex;justify-content:space-between;align-items:center">';
    html += '<div>';
    html += '<div style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--slate2)">Billed Till Today</div>';
    html += '<div style="font-size:12px;font-weight:700;color:var(--text)">₹<span id="mb-total-billed">'+_totalBilledTillToday.toFixed(2)+'</span></div>';
    html += '</div>';
    html += '<div style="width:1px;background:#d1d5db;align-self:stretch;margin:0 14px"></div>';
    html += '<div style="text-align:right">';
    html += '<div style="font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--slate2)">This Bill</div>';
    html += '<div style="font-size:12px;font-weight:700;color:var(--green)">₹<span id="mb-total-this-bill">0.00</span></div>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
  }

  html += '<div class="so-actions" style="margin-top:12px">';
  html += '<button class="so-btn so-btn-cancel" onclick="soBackToMb()">Cancel</button>';
  html += '<button class="so-btn so-btn-draft" onclick="soSaveMb(\''+escHtml(mbNum)+'\',\'draft\')">Save Draft</button>';
  html += '<button class="so-btn so-btn-success" onclick="soSaveMb(\''+escHtml(mbNum)+'\',\'final\')">Submit MB</button>';
  html += '</div>';

  $('#mbook-form-body').html(html);

  // Auto-fill Work Done + Amount when Current Qty is typed
  $(document).off('input.mbqty').on('input.mbqty','.mb-qty-input',function(){
    var aid = $(this).data('aid');
    var max = parseFloat($(this).data('max'))||0;
    var val = parseFloat($(this).val())||0;

    // Validate and colour border
    if(val > max + 0.001){
      $(this).css('border-color','var(--red)');
      toast('Cannot exceed remaining qty ('+max.toFixed(2)+')');
    } else {
      $(this).css('border-color', val > 0 ? 'var(--accent)' : 'var(--accent)');
    }

    // Update Remaining display
    var rem = Math.max(0, max - val);
    var $rem = $('#mb-rem-'+aid);
    $rem.text(rem.toFixed(2)).css('color', rem <= 0 ? 'var(--red)' : 'var(--green)');

    // Auto-fill each task: Work Done = val × tqpu, Amount = Work Done × rate
    $('[data-aid="'+aid+'"].mb-task-input').each(function(){
      var tqpu   = parseFloat($(this).data('tqpu'))||0;
      var tkRate = parseFloat($(this).data('rate'))||0;
      var tid    = $(this).data('tid');
      var wd     = (val > 0 && tqpu > 0) ? val * tqpu : 0;
      $(this).val(wd > 0 ? wd.toFixed(3) : '0');
      $('#mb-task-qty-'+aid+'-'+tid).text(wd > 0 ? wd.toFixed(2) : '—');
      $('#mb-task-amt-'+aid+'-'+tid).text((wd > 0 && tkRate > 0) ? (wd * tkRate).toFixed(2) : '—');
    });

    // Recalculate This Bill total across all activities
    var thisBill = 0;
    $('.mb-task-input').each(function(){
      thisBill += parseFloat($(this).val())||0 * (parseFloat($(this).data('rate'))||0);
    });
    // More accurate: sum from displayed amount cells
    var billTotal = 0;
    $('.mb-qty-input').each(function(){
      var a = $(this).data('aid');
      $('[data-aid="'+a+'"].mb-task-input').each(function(){
        var wd2 = parseFloat($(this).val())||0;
        var r2  = parseFloat($(this).data('rate'))||0;
        billTotal += wd2 * r2;
      });
    });
    $('#mb-total-this-bill').text(billTotal.toFixed(2));
  });
}

window.soBackToMb = function(){
  document.getElementById('so-screen-title').textContent = 'Measurement Book';
  showScreen('mbook');
};

window.soSaveMb = function(mbNum, mode){
  var date = document.getElementById('mb-date').value;
  if(!date){ toast('Please select MB date.'); return; }

  var entries = [];
  var error = '';

  $('.mb-qty-input').each(function(){
    var aid = this.id.replace('mb-qty-','');
    var qty = parseFloat($(this).val())||0;
    if(qty <= 0) return;
    var max = parseFloat($(this).data('max'))||0;
    if(qty > max + 0.001){
      error = 'MB quantity exceeds remaining reportable progress for this activity.';
      return false;
    }
    var tasks = [];
    $('[data-aid="'+aid+'"].mb-task-input').each(function(){
      var workDone = parseFloat($(this).val())||0;
      var tqpu     = parseFloat($(this).data('tqpu'))||0;
      // auto-fill if still blank
      if(!workDone && tqpu > 0) workDone = qty * tqpu;
      var maxWd    = qty * tqpu;
      if(tqpu > 0 && workDone > maxWd + 0.001){
        error = 'Task work done ('+workDone.toFixed(3)+') exceeds maximum ('+maxWd.toFixed(3)+') for this MB quantity.';
        return false;
      }
      if(workDone > 0) tasks.push({task_id: $(this).data('tid'), work_done: workDone});
    });
    if(error) return false;
    entries.push({activity_id: aid, qty: qty, tasks: tasks});
  });

  if(error){ toast(error); return; }
  if(!entries.length){ toast('Please enter MB quantity for at least one activity.'); return; }

  var url = mode === 'draft' ? '../storekeeper/savedraft' : '../storekeeper/savemb';
  var $btns = $('#mbook-form-body .so-btn-success, #mbook-form-body .so-btn-draft');
  $btns.attr('disabled',true);

  $.ajax({
    type:'POST', url:url, dataType:'json',
    data:{
      wo_number: _currentMbWo,
      entries: JSON.stringify(entries),
      mb_number: mbNum,
      mb_date: date
    },
    success:function(d){
      $btns.removeAttr('disabled');
      if(d.error === 'No'){
        toast(mode === 'draft' ? 'Draft saved!' : 'Measurement Book submitted!');
        soBackToMb();
        setTimeout(loadMbOrders, 800);
      } else {
        toast(d.errortext || 'Error saving MB.');
      }
    },
    error:function(){
      $btns.removeAttr('disabled');
      toast('Network error. Please try again.');
    }
  });
};

// ── Init ─────────────────────────────────────────────────────────────────────
soGoHome();

})();
</script>
