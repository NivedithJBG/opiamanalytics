<?php
$this->title = 'Goods Received Note';
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

#mob-content{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding:12px 12px 24px;background:var(--bg)}

.mob-type-hdr{
  background:var(--slate);color:var(--slate-text);
  font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;
  padding:7px 14px;border-radius:8px;margin:14px 0 6px;
}
.mob-type-hdr:first-child{margin-top:0}

.mob-po-card{
  background:var(--card);border-radius:12px;border:1px solid var(--border);
  margin-bottom:8px;box-shadow:0 1px 3px rgba(0,0,0,.06);
  padding:13px 14px;display:flex;align-items:center;gap:10px;
}
.mob-po-card.done{opacity:.6}
.mob-po-info{flex:1;min-width:0}
.mob-po-number{font-size:14px;font-weight:600;color:var(--text)}
.mob-po-meta{display:flex;gap:12px;margin-top:4px;flex-wrap:wrap}
.mob-po-meta span{font-size:11px;color:var(--muted)}
.mob-po-meta strong{color:var(--text)}
.mob-po-badge{flex-shrink:0;font-size:11px;font-weight:700;padding:5px 10px;border-radius:8px;white-space:nowrap}
.mob-po-badge.pending{background:#eef2f8;color:var(--accent)}
.mob-po-badge.done{background:#e9f7ef;color:var(--green)}

#mob-loading{text-align:center;padding:50px 20px;color:var(--muted);font-size:14px}
.mob-spinner{width:36px;height:36px;border:3px solid var(--border);border-top-color:var(--accent);border-radius:50%;animation:spin .8s linear infinite;margin:0 auto 14px}
@keyframes spin{to{transform:rotate(360deg)}}

#mob-toast{
  position:fixed;bottom:calc(env(safe-area-inset-bottom,0px) + 20px);left:50%;transform:translateX(-50%);
  background:var(--slate);color:var(--slate-text);padding:10px 22px;border-radius:24px;
  font-size:13px;font-weight:600;white-space:nowrap;
  opacity:0;transition:opacity .25s;pointer-events:none;z-index:999;
  box-shadow:0 4px 12px rgba(0,0,0,.2);
}
#mob-toast.show{opacity:1}

/* ── Entry pane (item list for a selected PO) ── */
#mob-entry-pane{display:none;flex:1;flex-direction:column;overflow:hidden}
#mob-entry-pane.show{display:flex}
#mob-list-pane{display:flex;flex:1;flex-direction:column;overflow:hidden}
#mob-list-pane.hide{display:none}

.mob-entry-hdr{
  background:#fff;border-bottom:1px solid var(--border);padding:12px 14px;flex-shrink:0;
}
.mob-entry-hdr .mob-back-link{background:none;border:none;color:var(--accent);font-size:13px;font-weight:600;padding:0 0 8px;display:flex;align-items:center;gap:4px}
.mob-entry-chip-row{display:flex;gap:16px;flex-wrap:wrap;font-size:12px;color:var(--muted)}
.mob-entry-chip-row strong{color:var(--text)}

.mob-form-row2{display:grid;grid-template-columns:1fr 1fr;gap:10px;padding:12px 14px 0}
.mob-form-field label{font-size:10px;font-weight:700;color:var(--slate2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;display:block}
.mob-form-field input,.mob-form-field textarea{width:100%;padding:9px 10px;font-size:14px;border:1.5px solid var(--border);border-radius:8px;font-family:'Inter',sans-serif}

#mob-entry-body{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding:12px 14px 100px}

.mob-item-card{
  background:var(--card);border-radius:12px;border:1px solid var(--border);
  margin-bottom:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);padding:13px 14px;
}
.mob-item-name{font-size:14px;font-weight:600;color:var(--text)}
.mob-item-meta{display:flex;gap:12px;margin-top:4px;margin-bottom:10px;flex-wrap:wrap}
.mob-item-meta span{font-size:11px;color:var(--muted)}
.mob-item-meta strong{color:var(--text)}
.mob-item-meta .mob-item-remaining{color:var(--amber);font-weight:700}
.mob-item-remaining.zero{color:var(--red)}

#mob-submit-bar{
  position:fixed;left:0;right:0;bottom:0;
  background:#fff;border-top:1px solid var(--border);
  padding:10px 14px calc(env(safe-area-inset-bottom,0px) + 10px);
  display:none;gap:8px;z-index:90;
  box-shadow:0 -2px 8px rgba(0,0,0,.06);
}
#mob-submit-bar.show{display:flex}
#mob-submit-bar button{flex:1;padding:12px;border-radius:10px;font-size:14px;font-weight:600;border:none}
#mob-grn-submit-btn{background:var(--accent);color:#fff}
</style>

<div id="mob-app">

  <div id="mob-topbar">
    <button class="mob-back" onclick="history.back()">&#8592;</button>
    <div>
      <div class="mob-title">Goods Received Note</div>
      <div class="mob-proj"><?= htmlspecialchars($projectName) ?></div>
    </div>
  </div>

  <div id="mob-list-pane">
    <div id="mob-search-bar">
      <input type="text" id="mob-search" placeholder="Search PO number or vendor…">
    </div>
    <div id="mob-content">
      <div id="mob-loading"><div class="mob-spinner"></div>Loading purchase orders…</div>
    </div>
  </div>

  <div id="mob-entry-pane">
    <div class="mob-entry-hdr">
      <button class="mob-back-link" id="mob-entry-back">&#8592; Back to Purchase Orders</button>
      <div class="mob-entry-chip-row">
        <span>PO: <strong id="mob-entry-po-number">—</strong></span>
        <span>Vendor: <strong id="mob-entry-vendor">—</strong></span>
        <span>GRN No: <strong id="mob-entry-grn-number">—</strong></span>
      </div>
    </div>
    <div class="mob-form-row2">
      <div class="mob-form-field"><label>Date of Receipt</label><input type="date" id="mob-grn-date"></div>
      <div class="mob-form-field"><label>Remarks</label><input type="text" id="mob-grn-remarks" placeholder="Optional"></div>
    </div>
    <div id="mob-entry-body"></div>
  </div>

  <div id="mob-submit-bar">
    <button id="mob-grn-submit-btn">Submit GRN</button>
  </div>

</div>

<div id="mob-toast"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function(){
'use strict';

var _allPOs = [];
var _currentOrderId = null;
var _currentItems = [];

function toast(msg, dur){
  var t = document.getElementById('mob-toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(function(){ t.classList.remove('show'); }, dur||2000);
}
function escHtml(s){
  return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function fmtNum(n){
  n = parseFloat(n) || 0;
  return n.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});
}

function loadPOs(){
  $.ajax({
    type:'POST', url:'../storekeeper/issued', dataType:'json',
    success:function(d){
      if(d.error !== 'No'){ $('#mob-content').html('<div id="mob-loading">'+escHtml(d.errortext||'Error loading data.')+'</div>'); return; }
      _allPOs = d.rows || [];
      renderPOList();
    },
    error:function(){ $('#mob-content').html('<div id="mob-loading">Failed to load. Please try again.</div>'); }
  });
}

function renderPOList(){
  var q = ($('#mob-search').val()||'').toLowerCase();
  var rows = _allPOs.filter(function(r){
    if(!q) return true;
    return (r.ordernumber||'').toLowerCase().indexOf(q) !== -1
        || (r.vendor_name||'').toLowerCase().indexOf(q) !== -1;
  });

  if(!_allPOs.length){ $('#mob-content').html('<div id="mob-loading">No purchase orders found for this project.</div>'); return; }
  if(!rows.length){ $('#mob-content').html('<div id="mob-loading">No matching purchase orders.</div>'); return; }

  var html = '';
  var lastType = null;
  rows.forEach(function(r){
    var typeLabel = r.resource_type_name || 'Other';
    if(typeLabel !== lastType){
      html += '<div class="mob-type-hdr">'+escHtml(typeLabel)+'</div>';
      lastType = typeLabel;
    }
    var done = String(r.grn_fully_received) === '1';
    html += '<div class="mob-po-card'+(done?' done':'')+'" data-id="'+r.order_id+'" data-ordernumber="'+escHtml(r.ordernumber)+'" data-vendor="'+escHtml(r.vendor_name)+'">';
    html += '<div class="mob-po-info">';
    html += '<div class="mob-po-number">'+escHtml(r.ordernumber)+'</div>';
    html += '<div class="mob-po-meta">';
    html += '<span>Vendor: <strong>'+escHtml(r.vendor_name)+'</strong></span>';
    if(r.first_item) html += '<span>'+escHtml(r.first_item)+'</span>';
    html += '</div></div>';
    html += '<span class="mob-po-badge '+(done?'done':'pending')+'">'+(done?'Received':'Pending')+'</span>';
    html += '</div>';
  });
  $('#mob-content').html(html);
}

$('#mob-search').on('input', renderPOList);

$(document).on('click', '.mob-po-card', function(){
  if($(this).hasClass('done')){ toast('A GRN has already been issued against this order.', 3000); return; }
  var orderId = $(this).data('id');
  var poNumber = $(this).data('ordernumber');
  var vendor = $(this).data('vendor');
  openEntryPane(orderId, poNumber, vendor);
});

function openEntryPane(orderId, poNumber, vendor){
  _currentOrderId = orderId;
  $('#mob-entry-po-number').text(poNumber);
  $('#mob-entry-vendor').text(vendor);
  $('#mob-entry-grn-number').text('…');
  $('#mob-grn-date').val(new Date().toISOString().slice(0,10));
  $('#mob-grn-remarks').val('');
  $('#mob-entry-body').html('<div id="mob-loading"><div class="mob-spinner"></div>Loading items…</div>');
  $('#mob-list-pane').addClass('hide');
  $('#mob-entry-pane').addClass('show');
  $('#mob-submit-bar').addClass('show');

  $.ajax({
    type:'POST', url:'../storekeeper/grnnext', dataType:'json',
    success:function(d){ if(d.error === 'No') $('#mob-entry-grn-number').text(d.grn_number); }
  });

  $.ajax({
    type:'POST', url:'../storekeeper/grnitems', dataType:'json', data:{order_id: orderId},
    success:function(d){
      if(d.error !== 'No'){ $('#mob-entry-body').html('<div id="mob-loading">'+escHtml(d.errortext||'Error loading items.')+'</div>'); return; }
      _currentItems = d.rows || [];
      renderItems();
    },
    error:function(){ $('#mob-entry-body').html('<div id="mob-loading">Failed to load items.</div>'); }
  });
}

function closeEntryPane(){
  $('#mob-entry-pane').removeClass('show');
  $('#mob-list-pane').removeClass('hide');
  $('#mob-submit-bar').removeClass('show');
  _currentOrderId = null;
  _currentItems = [];
}
$('#mob-entry-back').on('click', closeEntryPane);

function renderItems(){
  if(!_currentItems.length){ $('#mob-entry-body').html('<div id="mob-loading">No items on this purchase order.</div>'); return; }

  var html = '';
  _currentItems.forEach(function(it){
    var ordered = parseFloat(it.ordered_qty) || 0;
    var received = parseFloat(it.total_received) || 0;
    var remaining = Math.max(0, ordered - received);
    html += '<div class="mob-item-card" data-resid="'+it.resource_id+'" data-remaining="'+remaining+'" data-porate="'+(it.po_rate||0)+'">';
    html += '<div class="mob-item-name">'+escHtml(it.resource_name)+'</div>';
    html += '<div class="mob-item-meta">';
    html += '<span>Unit: <strong>'+escHtml(it.unit)+'</strong></span>';
    html += '<span>Ordered: <strong>'+fmtNum(ordered)+'</strong></span>';
    html += '<span>Remaining: <strong class="mob-item-remaining'+(remaining<=0?' zero':'')+'">'+fmtNum(remaining)+'</strong></span>';
    html += '</div>';
    html += '<div class="mob-form-row2" style="padding:0;">';
    html += '<div class="mob-form-field"><label>Received Qty</label><input type="number" step="any" min="0" max="'+remaining+'" class="mob-grn-qty-input" placeholder="0"'+(remaining<=0?' disabled':'')+'></div>';
    html += '<div class="mob-form-field"><label>Rate</label><input type="number" step="any" min="0" class="mob-grn-rate-input" value="'+(it.po_rate||0)+'"'+(remaining<=0?' disabled':'')+'></div>';
    html += '</div>';
    html += '</div>';
  });
  $('#mob-entry-body').html(html);
}

$('#mob-grn-submit-btn').on('click', function(){
  if(!_currentOrderId){ return; }
  var date = $('#mob-grn-date').val();
  if(!date){ toast('Please enter the date of receipt'); return; }

  var items = [];
  var error = null;
  $('#mob-entry-body .mob-item-card').each(function(){
    var $card = $(this);
    var qty = parseFloat($card.find('.mob-grn-qty-input').val()) || 0;
    if(qty <= 0) return; // skip items with no quantity entered
    var rate = parseFloat($card.find('.mob-grn-rate-input').val());
    var remaining = parseFloat($card.data('remaining')) || 0;
    var poRate = parseFloat($card.data('porate')) || 0;
    if(isNaN(rate) || rate < 0){ error = 'Please enter a valid rate for '+$card.find('.mob-item-name').text(); return false; }
    if(qty > remaining){ error = 'Quantity exceeds remaining for '+$card.find('.mob-item-name').text(); return false; }
    if(poRate > 0 && rate > poRate){ error = 'Rate exceeds PO rate for '+$card.find('.mob-item-name').text(); return false; }
    items.push({ resource_id: $card.data('resid'), qty: qty, rate: rate });
  });

  if(error){ toast(error, 3500); return; }
  if(!items.length){ toast('Enter a received quantity for at least one item'); return; }

  var $btn = $(this).prop('disabled', true).text('Saving…');
  $.ajax({
    type:'POST', url:'../storekeeper/savegrn', dataType:'json',
    data:{
      order_id: _currentOrderId,
      items: JSON.stringify(items),
      remarks: $('#mob-grn-remarks').val(),
      date_of_receipt: date,
      grn_number: $('#mob-entry-grn-number').text()
    },
    success:function(d){
      $btn.prop('disabled', false).text('Submit GRN');
      if(d.error !== 'No'){ toast(d.errortext || 'Error saving GRN.', 3500); return; }
      toast('GRN saved successfully!');
      closeEntryPane();
      setTimeout(loadPOs, 800);
    },
    error:function(){ $btn.prop('disabled', false).text('Submit GRN'); toast('Failed. Please try again.'); }
  });
});

loadPOs();

})();
</script>
