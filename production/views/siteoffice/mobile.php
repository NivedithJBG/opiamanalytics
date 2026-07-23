<?php
$this->title = 'Site Office';
?>
<style>
:root{
  --navy:#0c2461;--slate:#475569;--slate2:#334155;
  --teal:#00838f;--green:#27ae60;--red:#e74c3c;--orange:#e67e22;
  --amber:#f39c12;--border:#d0d4e0;--text:#1a2540;--muted:#6b7a99;
  --grey:#f0f2f8;--white:#fff;
}
*{box-sizing:border-box;-webkit-tap-highlight-color:transparent}

/* ── Shell ── */
#so-app{display:flex;flex-direction:column;height:100vh;overflow:hidden}

/* ── Topbar ── */
#so-topbar{
  background:linear-gradient(135deg,var(--slate) 0%,var(--slate2) 100%);
  padding:env(safe-area-inset-top,0px) 16px 12px;
  padding-top:calc(env(safe-area-inset-top,0px) + 12px);
  flex-shrink:0;display:flex;align-items:center;gap:12px;
}
#so-back-btn{background:none;border:none;color:#fff;font-size:22px;cursor:pointer;padding:4px;display:none;line-height:1}
#so-topbar .so-title{flex:1;color:#fff;font-size:16px;font-weight:700;letter-spacing:.3px}
#so-topbar .so-proj{font-size:11px;color:rgba(255,255,255,.65);margin-top:2px}

/* ── Scrollable content ── */
#so-content{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding:16px 12px 90px}

/* ── Home tiles ── */
.so-home-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:8px}
.so-tile{
  background:#fff;border-radius:14px;padding:22px 16px;
  box-shadow:0 2px 8px rgba(0,0,0,.08);border:1px solid var(--border);
  display:flex;flex-direction:column;align-items:center;gap:10px;
  cursor:pointer;transition:transform .15s,box-shadow .15s;
}
.so-tile:active{transform:scale(.97);box-shadow:0 1px 4px rgba(0,0,0,.1)}
.so-tile-icon{font-size:36px;line-height:1}
.so-tile-label{font-size:13px;font-weight:700;color:var(--text);text-align:center;letter-spacing:.2px}
.so-tile-sub{font-size:10px;color:var(--muted);text-align:center}
.so-tile.indent .so-tile-icon{color:#2980b9}
.so-tile.grn .so-tile-icon{color:var(--green)}
.so-tile.mbook .so-tile-icon{color:var(--orange)}

/* ── Section header ── */
.so-section-hdr{
  background:var(--slate);color:#fff;
  font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;
  padding:7px 14px;border-radius:6px;margin:14px 0 6px;
}
.so-section-hdr:first-child{margin-top:0}

/* ── Cards ── */
.so-card{
  background:#fff;border-radius:10px;border:1px solid var(--border);
  margin-bottom:8px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.06);
}
.so-card.warn{border-left:4px solid var(--amber)}
.so-card.blocked{border-left:4px solid var(--red)}
.so-card.ok{border-left:4px solid var(--green)}
.so-card.selected{border-left:4px solid var(--teal);background:#f0fdfe}
.so-card-top{padding:12px 14px;display:flex;align-items:flex-start;gap:10px;cursor:pointer}
.so-card-name{flex:1;font-size:14px;font-weight:600;color:var(--text);line-height:1.3}
.so-card-meta{display:flex;gap:10px;margin-top:4px;flex-wrap:wrap}
.so-card-meta span{font-size:11px;color:var(--muted)}
.so-card-meta strong{color:var(--text)}
.so-badge{flex-shrink:0;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;align-self:flex-start;margin-top:2px}
.so-badge.est-reached{background:#fde8e8;color:var(--red)}
.so-badge.reorder{background:#fff3e0;color:var(--orange)}
.so-badge.ok{background:#e8f8ee;color:var(--green)}
.so-badge.grn-done{background:#fde8e8;color:var(--red)}

/* ── Inline form ── */
.so-form{display:none;padding:0 14px 14px;border-top:1px solid var(--border);background:#f7f8fc}
.so-form.open{display:block}
.so-form-row{display:flex;gap:10px;margin-top:10px;flex-wrap:wrap}
.so-field{display:flex;flex-direction:column;flex:1;min-width:130px}
.so-field.full{flex:0 0 100%}
.so-field label{font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
.so-field input,.so-field select{
  padding:9px 12px;font-size:14px;color:var(--text);
  border:1.5px solid var(--border);border-radius:7px;
  background:#fff;font-family:'Inter',sans-serif;
  outline:none;width:100%;transition:border-color .15s;
}
.so-field input:focus,.so-field select:focus{border-color:var(--teal)}
.so-field input[readonly]{background:#f3f4f8;color:var(--muted)}
.so-info-box{font-size:11px;color:var(--muted);margin-top:8px;padding:7px 10px;background:#f3f4f8;border-radius:5px;line-height:1.5}
.so-info-box strong{color:var(--text)}

/* ── Form actions ── */
.so-actions{display:flex;gap:8px;margin-top:12px}
.so-btn{flex:1;padding:11px;border:none;border-radius:8px;font-size:14px;font-weight:600;font-family:'Inter',sans-serif;cursor:pointer;transition:opacity .15s}
.so-btn:active{opacity:.8}
.so-btn-primary{background:var(--slate);color:#fff}
.so-btn-success{background:var(--green);color:#fff}
.so-btn-cancel{background:#e8eaf2;color:var(--muted)}
.so-btn-draft{background:var(--amber);color:#fff;font-size:12px}

/* ── Alert box ── */
.so-alert{
  background:#fde8e8;border:1px solid #f5c6cb;border-radius:8px;
  padding:12px 14px;margin:10px 0;font-size:12px;color:#721c24;line-height:1.5;
}
.so-alert strong{display:block;margin-bottom:4px;font-size:13px}

/* ── GRN items table ── */
.so-items-table{width:100%;border-collapse:collapse;margin-top:10px;font-size:12px}
.so-items-table th{background:var(--slate);color:#fff;padding:7px 8px;text-align:left;font-weight:600}
.so-items-table td{padding:8px;border-bottom:1px solid var(--border);vertical-align:middle}
.so-items-table input{border:1.5px solid var(--border);border-radius:5px;padding:6px 8px;font-size:13px;width:100%;outline:none}
.so-items-table input:focus{border-color:var(--teal)}
.so-items-table input[readonly]{background:#f3f4f8;color:var(--muted)}

/* ── MB activities ── */
.so-mb-act{background:#fff;border:1px solid var(--border);border-radius:8px;margin-bottom:10px;overflow:hidden}
.so-mb-act-hdr{background:#f0f2f8;padding:10px 14px;display:flex;justify-content:space-between;align-items:center}
.so-mb-act-name{font-size:13px;font-weight:600;color:var(--text)}
.so-mb-act-meta{font-size:11px;color:var(--muted);margin-top:2px}
.so-mb-act-body{padding:10px 14px}

/* ── Loading / empty ── */
.so-loading{text-align:center;padding:40px 20px;color:var(--muted);font-size:14px}
.so-spinner{width:32px;height:32px;border:3px solid #e0e4ef;border-top-color:var(--teal);border-radius:50%;animation:spin .8s linear infinite;margin:0 auto 12px}
@keyframes spin{to{transform:rotate(360deg)}}

/* ── Toast ── */
#so-toast{
  position:fixed;bottom:calc(env(safe-area-inset-bottom,0px) + 80px);left:50%;transform:translateX(-50%);
  background:#1a2540;color:#fff;padding:10px 22px;border-radius:24px;
  font-size:13px;font-weight:600;white-space:nowrap;
  opacity:0;transition:opacity .25s;pointer-events:none;z-index:999;
}
#so-toast.show{opacity:1}

/* ── Bottom nav ── */
#so-bottom-nav{
  position:fixed;bottom:0;left:0;right:0;
  background:#fff;border-top:1px solid var(--border);
  padding:8px 0 calc(env(safe-area-inset-bottom,0px) + 6px);
  display:flex;justify-content:space-around;z-index:100;
}
.so-nav-item{display:flex;flex-direction:column;align-items:center;gap:2px;font-size:9px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;cursor:pointer;padding:0 10px;text-decoration:none}
.so-nav-item.active{color:var(--slate)}
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
      <div style="font-size:13px;color:var(--muted);margin-bottom:14px;text-align:center;">Select a module to get started</div>
      <div class="so-home-grid">
        <div class="so-tile indent" onclick="soOpenModule('indent')">
          <div class="so-tile-icon">&#128230;</div>
          <div class="so-tile-label">Indents</div>
          <div class="so-tile-sub">Raise material requisition</div>
        </div>
        <div class="so-tile grn" onclick="soOpenModule('grn')">
          <div class="so-tile-icon">&#9989;</div>
          <div class="so-tile-label">GRN</div>
          <div class="so-tile-sub">Goods received note</div>
        </div>
        <div class="so-tile mbook" onclick="soOpenModule('mbook')">
          <div class="so-tile-icon">&#128214;</div>
          <div class="so-tile-label">M. Book</div>
          <div class="so-tile-sub">Measurement book</div>
        </div>
        <div class="so-tile" onclick="window.location='<?= Yii::$app->urlManager->createUrl('report/mobile') ?>'">
          <div class="so-tile-icon" style="color:var(--teal)">&#128203;</div>
          <div class="so-tile-label">Progress</div>
          <div class="so-tile-sub">Report site progress</div>
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
  <a class="so-nav-item active" href="<?= Yii::$app->urlManager->createUrl('siteoffice-mobile/mobile') ?>">
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
    type:'POST', url:'../storekeeper/issuedgrns', dataType:'json',
    success:function(d){
      // issuedgrns returns already-done GRNs; we need open POs
      // Load open POs from procurement
      loadOpenPOs();
    },
    error:function(){ loadOpenPOs(); }
  });
}

function loadOpenPOs(){
  $.ajax({
    type:'POST', url:'../procurement/openpurchaseorders', dataType:'json',
    success:function(d){
      if(d.error !== 'No' || !d.rows || !d.rows.length){
        $('#grn-body').html('<div class="so-loading">No open purchase orders found.</div>');
        return;
      }
      _grnOrders = d.rows;
      renderGrnOrders();
    },
    error:function(){
      // Fallback: show issued GRNs list for reference
      $('#grn-body').html('<div class="so-loading">No open purchase orders found.</div>');
    }
  });
}

function renderGrnOrders(){
  var html = '';
  _grnOrders.forEach(function(o){
    var grnDone = o.grn_raised == 1;
    var cardClass = grnDone ? 'blocked' : 'ok';
    var badge = grnDone
      ? '<span class="so-badge grn-done">GRN Done</span>'
      : '<span class="so-badge ok">Open</span>';
    html += '<div class="so-card '+cardClass+'" onclick="soOpenGrnForm('+o.order_id+','+grnDone+')">';
    html += '<div class="so-card-top">';
    html += '<div><div class="so-card-name">'+escHtml(o.order_number)+'</div>';
    html += '<div class="so-card-meta">';
    html += '<span>Vendor: <strong>'+escHtml(o.vendor_name||'—')+'</strong></span>';
    html += '<span>Date: <strong>'+escHtml(o.order_date||'—')+'</strong></span>';
    html += '</div></div>';
    html += badge;
    html += '</div></div>';
  });
  if(!html) html = '<div class="so-loading">No purchase orders found.</div>';
  $('#grn-body').html(html);
}

window.soOpenGrnForm = function(orderId, grnDone){
  if(grnDone){ toast('A GRN has already been raised against this order.'); return; }
  _currentGrnOrder = orderId;
  document.getElementById('so-screen-title').textContent = 'Raise GRN';
  showScreen('grn-form');
  $('#grn-form-body').html('<div class="so-loading"><div class="so-spinner"></div>Loading order items…</div>');

  $.when(
    $.ajax({type:'POST', url:'../storekeeper/grnitems', dataType:'json', data:{order_id: orderId}}),
    $.ajax({type:'POST', url:'../storekeeper/grnnext', dataType:'json'})
  ).done(function(itemsRes, nextRes){
    var items = itemsRes[0];
    var next  = nextRes[0];
    if(items.error !== 'No'){ $('#grn-form-body').html('<div class="so-loading">'+(items.errortext||'Error loading items.')+'</div>'); return; }
    renderGrnForm(items, next);
  }).fail(function(){
    $('#grn-form-body').html('<div class="so-loading">Failed to load order items.</div>');
  });
};

function renderGrnForm(data, next){
  var grnNum = next.grn_number || '—';
  var rows = data.rows || [];
  var html = '';

  html += '<div class="so-info-box" style="margin-bottom:12px">';
  html += '<strong>GRN Number: '+escHtml(grnNum)+'</strong><br>';
  html += 'Vendor: <strong>'+escHtml(data.vendor_name||'—')+'</strong><br>';
  html += 'Order No: <strong>'+escHtml(data.order_number||'—')+'</strong>';
  html += '</div>';

  html += '<div class="so-form-row">';
  html += '<div class="so-field"><label>Date of Receipt</label><input type="date" id="grn-date" value="'+new Date().toISOString().split("T")[0]+'"></div>';
  html += '</div>';

  html += '<table class="so-items-table" style="margin-top:14px">';
  html += '<thead><tr><th>Resource</th><th>Unit</th><th>Ordered</th><th>Received</th><th>Rate</th></tr></thead><tbody>';
  rows.forEach(function(r,i){
    var orderedQty = parseFloat(r.ordered_qty||0);
    var received   = parseFloat(r.total_received||0);
    var remaining  = Math.max(0, orderedQty - received).toFixed(3);
    var rate       = parseFloat(r.rate||0).toFixed(2);
    html += '<tr>';
    html += '<td style="font-size:12px;font-weight:600">'+escHtml(r.resource_name)+'</td>';
    html += '<td style="font-size:11px;color:var(--muted)">'+escHtml(r.unit||'—')+'</td>';
    html += '<td style="font-size:12px;text-align:right">'+orderedQty.toFixed(2)+'</td>';
    html += '<td><input type="number" id="grn-qty-'+r.resource_id+'" data-rid="'+r.resource_id+'" data-max="'+remaining+'" placeholder="0.00" step="0.001" min="0" max="'+remaining+'" class="grn-qty-input"></td>';
    html += '<td><input type="text" value="'+rate+'" readonly id="grn-rate-'+r.resource_id+'"></td>';
    html += '</tr>';
    if(received > 0){
      html += '<tr><td colspan="5" style="font-size:10px;color:var(--muted);padding:2px 8px 6px">Previously received: '+received.toFixed(2)+' | Remaining: '+remaining+'</td></tr>';
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
      items.push({resource_id: r.resource_id, qty: qty, rate: parseFloat(document.getElementById('grn-rate-'+r.resource_id).value)||0});
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

  html += '<div class="so-info-box" style="margin-bottom:12px">';
  html += '<strong>MB Number: '+escHtml(mbNum)+'</strong><br>';
  html += 'Work Order: <strong>'+escHtml(woNumber)+'</strong><br>';
  html += 'Vendor: <strong>'+escHtml(vendor)+'</strong>';
  if(hasDraft) html += '<br><span style="color:var(--amber);font-weight:700">⚠ A draft MB exists for this WO</span>';
  html += '</div>';

  html += '<div class="so-form-row">';
  html += '<div class="so-field"><label>MB Date</label><input type="date" id="mb-date" value="'+new Date().toISOString().split("T")[0]+'"></div>';
  html += '</div>';

  if(!acts.length){
    html += '<div class="so-loading" style="padding:20px 0">No activities found in this work order.</div>';
  } else {
    acts.forEach(function(act){
      var cumBilled   = parseFloat(act.cumulative_qty||0).toFixed(3);
      var lastRep     = parseFloat(act.last_reported_qty||0).toFixed(3);
      var remaining   = Math.max(0, parseFloat(lastRep) - parseFloat(cumBilled)).toFixed(3);

      html += '<div class="so-mb-act">';
      html += '<div class="so-mb-act-hdr">';
      html += '<div>';
      html += '<div class="so-mb-act-name">'+escHtml(act.activity_name)+'</div>';
      html += '<div class="so-mb-act-meta">Unit: '+escHtml(act.unit||'—')+' | Last Reported: <strong>'+lastRep+'</strong> | Cum. Billed: <strong>'+cumBilled+'</strong> | Remaining: <strong>'+remaining+'</strong></div>';
      html += '</div></div>';
      html += '<div class="so-mb-act-body">';
      html += '<div class="so-field"><label>MB Quantity</label>';
      html += '<input type="number" id="mb-qty-'+act.activity_id+'" data-lastRep="'+lastRep+'" data-cumBilled="'+cumBilled+'" data-max="'+remaining+'" placeholder="0.000" step="0.001" min="0" class="mb-qty-input"></div>';

      if(act.tasks && act.tasks.length){
        html += '<table class="so-items-table" style="margin-top:10px">';
        html += '<thead><tr><th>Task</th><th>Qty/Unit</th><th>Work Done</th></tr></thead><tbody>';
        act.tasks.forEach(function(tk){
          html += '<tr>';
          html += '<td style="font-size:12px">'+escHtml(tk.task_name)+'</td>';
          html += '<td style="font-size:11px;color:var(--muted);text-align:right">'+escHtml(String(tk.task_qty_per_unit||'—'))+'</td>';
          html += '<td><input type="number" id="mb-task-'+act.activity_id+'-'+tk.task_id+'" data-aid="'+act.activity_id+'" data-tid="'+tk.task_id+'" data-tqpu="'+(tk.task_qty_per_unit||0)+'" placeholder="0.00" step="0.001" min="0" class="mb-task-input"></td>';
          html += '</tr>';
        });
        html += '</tbody></table>';
      }
      html += '</div></div>'; // body + mb-act
    });
  }

  html += '<div class="so-actions" style="margin-top:16px">';
  html += '<button class="so-btn so-btn-cancel" onclick="soBackToMb()">Cancel</button>';
  html += '<button class="so-btn so-btn-draft" onclick="soSaveMb(\''+escHtml(mbNum)+'\',\'draft\')">Save Draft</button>';
  html += '<button class="so-btn so-btn-success" onclick="soSaveMb(\''+escHtml(mbNum)+'\',\'final\')">Submit MB</button>';
  html += '</div>';

  $('#mbook-form-body').html(html);

  // Validate MB qty on input
  $(document).off('input.mbqty').on('input.mbqty','.mb-qty-input',function(){
    var max = parseFloat($(this).data('max'))||0;
    var val = parseFloat($(this).val())||0;
    if(val > max + 0.001){
      $(this).css('border-color','var(--red)');
      toast('MB qty cannot exceed remaining progress ('+max+')');
    } else {
      $(this).css('border-color','');
    }
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
      var maxWd    = qty * tqpu;
      if(tqpu > 0 && workDone > maxWd + 0.001){
        error = 'Task work done exceeds maximum ('+maxWd.toFixed(4)+') for this MB quantity.';
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
