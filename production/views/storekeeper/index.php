<style>
.sk-page-wrap {
    background: #fff;
    border-radius: 6px;
    margin: 30px 20px 20px;
    padding: 0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.10);
    min-height: 400px;
    overflow: hidden;
}
.sk-header {
    background: #072c47;
    padding: 6px 28px;
    color: #fff;
    font-size: 17px;
    font-weight: 400;
    letter-spacing: 0.5px;
}
.sk-tab-row {
    padding: 28px 24px 22px;
    text-align: center;
    border-bottom: 1px solid #e8e8e8;
    background: #f8fafc;
}
.sk-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border: 1.5px solid #c5ccd4;
    border-radius: 50px;
    padding: 6px 28px 6px 12px;
    margin: 0 6px;
    background: #c8ccd2;
    color: #333d4d;
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.4px;
    min-width: 180px;
    white-space: nowrap;
    cursor: pointer;
    transition: background 0.2s, color 0.2s, border-color 0.2s;
    text-decoration: none;
}
.sk-tab-btn:hover {
    background: #b6bcc4;
    text-decoration: none;
    color: #465365;
}
.sk-tab-btn.active {
    background: #2c4a4a;
    border-color: #2c4a4a;
    color: #fff;
}
.sk-tab-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(44,74,74,0.16);
    font-size: 12px;
    flex-shrink: 0;
}
.sk-tab-btn.active .sk-tab-icon {
    background: rgba(255,255,255,0.18);
    color: #fff;
}
.sk-tab-content {
    display: none;
    padding: 0;
}
.sk-tab-content.active {
    display: block;
}
.sk-toolbar {
    padding: 10px 16px;
    border-bottom: 1px solid #e0e4e8;
    background: #f8fafc;
    text-align: right;
}
.sk-search-bar {
    padding: 10px 16px;
    border-bottom: 1px solid #e0e4e8;
    background: #f4f7fa;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.sk-search-bar label {
    font-size: 12px;
    font-weight: 600;
    color: #465365;
    margin: 0;
    white-space: nowrap;
}
.sk-search-bar select,
.sk-search-bar input[type="text"] {
    height: 30px;
    font-size: 12px;
    border: 1px solid #c5ccd4;
    border-radius: 4px;
    padding: 2px 8px;
    color: #333;
    background: #fff;
    outline: none;
}
.sk-search-bar select { min-width: 160px; }
.sk-search-bar input[type="text"] { min-width: 200px; }
.sk-even { background: #f9f9f9; }
.sk-table td.num { text-align: right; }
.sk-table thead tr { background: #072c47; color: #fff; }
.sk-table thead th { border-color: #05233a; white-space: nowrap; }
.sk-table td, .sk-table th { border-color: #b8b8b8 !important; }
.procu-even { background: #f9f9f9; }
.procu-table td.num { text-align: right; }
.procu-table thead tr { background: #072c47; color: #fff; }
.procu-table thead th { border-color: #05233a; white-space: nowrap; }
.procu-table td, .procu-table th { border-color: #d0d0d0 !important; }
.sk-cb {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: #072c47;
}
.sk-cb-all {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: #072c47;
}
.sk-row-selected td {
    background: #e8f0f7 !important;
}
.sk-loader {
    text-align: center;
    padding: 30px 0;
    color: #888;
    font-size: 13px;
}
.sk-empty {
    text-align: center;
    color: #aaa;
    font-size: 13px;
    padding: 40px 0;
}
.sk-tab-win {
    display: none;
    position: fixed;
    top: 90px; left: 60px;
    width: 980px; height: calc(100vh - 140px);
    min-width: 480px; min-height: 320px;
    background: #fff; border-radius: 6px;
    border: 1px solid #d0d0d0;
    box-shadow: 0 4px 16px rgba(0,0,0,.18);
    flex-direction: column; overflow: hidden;
    z-index: 999999;
}
.sk-tab-win.pw2-open { display: flex; }
.sk-tab-win-hdr {
    background: #2c4a4a; color: #fff; padding: 10px 16px;
    display: flex; align-items: center; justify-content: space-between;
    cursor: move; user-select: none; flex-shrink: 0;
}
.sk-tab-win-title { font-size: 14px; font-weight: 600; letter-spacing: 0.5px; }
.sk-tab-win-hdr-btns { display: flex; align-items: center; gap: 8px; }
.sk-tab-win-hdr-btns button {
    background: none; border: none; color: #fff; font-size: 16px;
    cursor: pointer; line-height: 1; padding: 0 4px; opacity: .85;
}
.sk-tab-win-hdr-btns button:hover { opacity: 1; }
.sk-tab-win-body { flex: 1; min-height: 0; overflow: auto; }
.sk-tw-rs { position: absolute; z-index: 10; background: transparent; }
.sk-tw-rs-e  { right:0;  top:6px;  bottom:6px; width:6px;  cursor:e-resize; }
.sk-tw-rs-w  { left:0;   top:6px;  bottom:6px; width:6px;  cursor:w-resize; }
.sk-tw-rs-s  { bottom:0; left:6px; right:6px;  height:6px; cursor:s-resize; }
.sk-tw-rs-n  { top:0;    left:6px; right:6px;  height:6px; cursor:n-resize; }
.sk-tw-rs-se { right:0;  bottom:0; width:14px; height:14px; cursor:se-resize; }
.sk-tw-rs-sw { left:0;   bottom:0; width:14px; height:14px; cursor:sw-resize; }
.sk-tw-rs-ne { right:0;  top:0;    width:14px; height:14px; cursor:ne-resize; }
.sk-tw-rs-nw { left:0;   top:0;    width:14px; height:14px; cursor:nw-resize; }
</style>

<div class="sk-page-wrap">

    <div id="sk-page-header" class="sk-header" style="display:none;">Indents</div>

    <div class="sk-tab-row">
        <a href="javascript:;" class="sk-tab-btn" data-tab="indents">
            <span class="sk-tab-icon"><span class="icon-list"></span></span>
            Indents
        </a>
        <a href="javascript:;" class="sk-tab-btn" data-tab="issued">
            <span class="sk-tab-icon"><span class="icon-checkmark"></span></span>
            Goods Received Notes
        </a>
        <a href="javascript:;" class="sk-tab-btn" data-tab="mbwo">
            <span class="sk-tab-icon"><span class="icon-book"></span></span>
            Measurement Book
        </a>
        <a href="javascript:;" class="sk-tab-btn" data-tab="reportprogress">
            <span class="sk-tab-icon"><span class="icon-chart3"></span></span>
            Report Progress
        </a>
    </div>

    <div id="sk-tab-indents" class="sk-tab-content active">
        <div id="sk-filter-bar" style="padding:14px 20px 14px;display:flex;gap:16px;align-items:flex-end;border-bottom:1px solid #e0e4e8;">
            <div>
                <div style="font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Resource Type</div>
                <select id="sk-filter-restype" class="form-control" style="width:180px;height:30px;font-size:12px;padding:2px 8px;">
                    <option value="">-- All --</option>
                </select>
            </div>
            <div>
                <div style="font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Resource</div>
                <div style="position:relative;">
                    <input type="text" id="sk-filter-resname" class="form-control" placeholder="Search..." style="width:200px;height:30px;font-size:12px;padding:2px 28px 2px 8px;">
                    <span class="icon-search5" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);color:#aaa;font-size:12px;pointer-events:none;"></span>
                </div>
            </div>
            <div id="sk-indent-action-bar" style="margin-left:auto;display:flex;gap:8px;align-items:flex-end;">
                <button type="button" id="sk-issued-indent-btn" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:7px 24px;font-size:13px;font-weight:600;cursor:pointer;">Issued Indents</button>
                <button type="button" id="sk-raise-indent-btn" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:7px 24px;font-size:13px;font-weight:600;cursor:pointer;">Raise Indent</button>
            </div>
        </div>
        <div id="sk-loader" class="sk-loader">Loading...</div>
        <div id="sk-body"></div>
    </div>

    <!-- Indent Task Selection Modal -->
    <div id="sk-indent-task-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;overflow-y:auto;">
        <div style="background:#fff;margin:60px auto;max-width:600px;border-radius:6px;padding:26px 30px;position:relative;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
            <button id="sk-indent-task-modal-close" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:20px;color:#888;cursor:pointer;line-height:1;" title="Close">&times;</button>
            <h4 id="sk-indent-task-modal-title" style="margin:0 0 18px;font-size:15px;font-weight:700;color:#333;padding-right:30px;"></h4>
            <div id="sk-indent-task-modal-body"></div>
        </div>
    </div>

    <!-- Raise Indent Popup -->
    <div id="sk-raise-popup" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:10000;overflow-y:auto;">
        <div style="background:#fff;margin:50px auto;max-width:820px;border-radius:6px;padding:26px 30px;position:relative;box-shadow:0 8px 32px rgba(0,0,0,0.2);">
            <button id="sk-raise-popup-close" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:20px;color:#888;cursor:pointer;">&times;</button>
            <h4 style="margin:0 0 18px;font-size:15px;font-weight:700;color:#333;">Raise Indent</h4>
            <div id="sk-raise-popup-body"></div>
            <div style="text-align:right;margin-top:16px;">
                <button type="button" id="sk-raise-popup-cancel" style="background:#888;color:#fff;border:none;border-radius:20px;padding:6px 22px;font-size:13px;font-weight:600;cursor:pointer;margin-right:10px;">Cancel</button>
                <button type="button" id="sk-raise-popup-submit" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:6px 26px;font-size:13px;font-weight:600;cursor:pointer;">Submit</button>
            </div>
        </div>
    </div>

    <div id="sk-tab-issued" class="sk-tab-content">
        <div id="sk-inv-filter-bar" style="padding:12px 20px;border-bottom:1px solid #e0e4e8;background:#f4f7fa;display:flex;align-items:center;gap:8px;">
            <span class="icon-search5" style="font-size:14px;color:#465365;"></span>
            <label style="font-size:12px;font-weight:600;color:#465365;margin:0;white-space:nowrap;">Vendor</label>
            <select id="sk-inv-filter-vendor" class="form-control" style="width:220px;height:30px;font-size:12px;padding:2px 8px;">
                <option value="">-- All --</option>
            </select>
            <div style="margin-left:auto;">
                <button type="button" id="sk-issued-grn-btn" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;">Issued GRN's</button>
            </div>
        </div>
        <div id="sk-issued-loader" class="sk-loader" style="display:none;">Loading...</div>
        <div id="sk-issued-body" style="padding:14px 20px 20px;"></div>
    </div>
    <div id="sk-tab-mbwo" class="sk-tab-content">
        <div id="sk-mbwo-filter-bar" style="padding:12px 20px;border-bottom:1px solid #e0e4e8;background:#f4f7fa;display:flex;align-items:center;gap:8px;">
            <span class="icon-search5" style="font-size:14px;color:#465365;"></span>
            <label style="font-size:12px;font-weight:600;color:#465365;margin:0;white-space:nowrap;">Vendor</label>
            <select id="sk-mbwo-filter-vendor" class="form-control" style="width:220px;height:30px;font-size:12px;padding:2px 8px;">
                <option value="">-- All --</option>
            </select>
            <div style="margin-left:auto;">
                <button type="button" id="sk-issued-mb-btn" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:7px 24px;font-size:13px;font-weight:600;cursor:pointer;">Issued M.Books</button>
            </div>
        </div>
        <div id="sk-mbwo-loader" class="sk-loader" style="display:none;">Loading...</div>
        <div id="sk-mbwo-body" style="padding:14px 20px 20px;"></div>
    </div>
    <div id="sk-tab-reportprogress" class="sk-tab-content">
        <div id="sk-reportprogress-loader" class="sk-loader">Loading...</div>
        <div id="sk-reportprogress-body"></div>
    </div>

</div>

<script>
(function(){
    var _skAllRows          = [];
    var _allInvRows         = [];
    var _allMbRows          = [];
    var _allIssuedIndentRows = [];

    function loadIndents() {
        $('#sk-loader').show();
        $('#sk-body').html('');
        $.ajax({
            type: 'POST',
            url: '../storekeeper/indents',
            dataType: 'json',
            success: function(data) {
                $('#sk-loader').hide();
                if (data.error !== 'No') {
                    $('#sk-body').html('<p class="sk-empty">' + (data.errortext || 'Error loading data.') + '</p>');
                    return;
                }
                _skAllRows = data.rows || [];
                populateSkResTypeFilter();
                applySkFilters();
            },
            error: function() {
                $('#sk-loader').hide();
                $('#sk-body').html('<p class="sk-empty">Failed to load data. Please try again.</p>');
            }
        });
    }

    function populateSkResTypeFilter() {
        var types = {};
        $.each(_skAllRows, function(i, r) { if (r.resource_type) types[r.resource_type] = true; });
        var current = $('#sk-filter-restype').val();
        var opts = '<option value="">-- All --</option>';
        $.each(Object.keys(types).sort(), function(i, t) {
            opts += '<option value="' + t + '"' + (t === current ? ' selected' : '') + '>' + t + '</option>';
        });
        $('#sk-filter-restype').html(opts);
    }

    function applySkFilters() {
        var typeFilter = $('#sk-filter-restype').val();
        var nameFilter = $('#sk-filter-resname').val().trim().toLowerCase();
        var rows = _skAllRows.filter(function(r) {
            if (typeFilter && r.resource_type !== typeFilter) return false;
            if (nameFilter && (r.resource_name || '').toLowerCase().indexOf(nameFilter) === -1) return false;
            return true;
        });
        renderSkTable(rows);
    }

    function renderSkTable(rows) {
        if (!_skAllRows.length) {
            $('#sk-body').html('<p class="sk-empty">No allocated resources found for this project.</p>');
            return;
        }
        if (!rows.length) {
            $('#sk-body').html('<p class="sk-empty">No matching resources found.</p>');
            return;
        }
        var html = '<table class="table table-bordered sk-table">'
            + '<thead><tr>'
            + '<th>#</th>'
            + '<th>Resource</th>'
            + '<th>Unit</th>'
            + '<th style="text-align:right;">Purchased Qty</th>'
            + '<th style="text-align:right;">Consumed Qty</th>'
            + '<th style="text-align:center;width:110px;">Task</th>'
            + '<th style="text-align:center;width:60px;">Select</th>'
            + '</tr></thead><tbody>';
        var rowNum = 0;
        var currentType = null;
        $.each(rows, function(i, r) {
            if (r.resource_type !== currentType) {
                currentType = r.resource_type;
                html += '<tr><td colspan="7" style="border:none;padding:0;height:7px;background:#fff;"></td></tr>'
                    + '<tr style="background:#c8c8c8;">'
                    + '<td colspan="7" style="padding:6px 10px;font-weight:700;font-size:11px;color:#000;border:1px solid #bbb;letter-spacing:0.3px;">'
                    + currentType + '</td></tr>';
            }
            rowNum++;
            var purchasedQty  = parseFloat(r.purchased_quantity || 0);
            var consumedQty   = parseFloat(r.consumed_quantity  || 0);
            var stockQty      = parseFloat(r.stock              || 0);
            var reorderLevel  = parseFloat(r.reorder_level      || 0);
            var belowReorder  = reorderLevel > 0 && stockQty <= reorderLevel;
            var rowBg         = belowReorder ? 'background:#fff0f0;' : '';
            var reorderBadge  = belowReorder
                ? ' <span style="background:#e74c3c;color:#fff;font-size:9px;border-radius:3px;padding:1px 5px;vertical-align:middle;">Reorder</span>'
                : '';
            html += '<tr class="' + (rowNum % 2 === 0 ? 'sk-even' : '') + '" style="' + rowBg + '" data-id="' + r.resource_id + '">'
                + '<td>' + rowNum + '</td>'
                + '<td>' + r.resource_name + reorderBadge + '</td>'
                + '<td>' + (r.unit || '') + '</td>'
                + '<td class="num" style="background:#e6e6e6;color:#333;">' + purchasedQty.toLocaleString(undefined, {maximumFractionDigits:2}) + '</td>'
                + '<td class="num" style="background:#e6e6e6;color:#333;">' + consumedQty.toLocaleString(undefined, {maximumFractionDigits:2}) + '</td>'
                + '<td style="text-align:center;padding:4px 6px;">'
                + '<button type="button" class="sk-indent-task-btn" data-id="' + r.resource_id + '" data-resource-name="' + String(r.resource_name || '').replace(/"/g, '&quot;') + '" data-task-id="" data-task-name="" data-activity-name="" style="background:#072c47;color:#fff;border:none;border-radius:12px;padding:5px 12px;font-size:12px;font-weight:400;cursor:pointer;min-width:80px;">Tasks</button>'
                + '</td>'
                + '<td style="text-align:center;padding:4px 8px;">'
                + (r.estimate_reached == 1
                    ? '<span style="font-size:10px;font-weight:700;color:#c0392b;white-space:nowrap;">Est. Reached</span>'
                    : '<input type="checkbox" class="sk-cb" value="' + r.resource_id + '" style="width:16px;height:16px;cursor:pointer;accent-color:#072c47;">')
                + '</td>'
                + '</tr>';
        });
        html += '</tbody></table>';
        $('#sk-body').html(html);
    }

    $(document).on('change', '#sk-filter-restype', applySkFilters);
    $(document).on('input', '#sk-filter-resname', function(){
        clearTimeout(window._skFilterTimer);
        window._skFilterTimer = setTimeout(applySkFilters, 250);
    });

    function populateInvVendorFilter() {
        var vendors = {};
        $.each(_allInvRows, function(i, r) { if (r.vendor_name) vendors[r.vendor_name] = true; });
        var current = $('#sk-inv-filter-vendor').val();
        var opts = '<option value="">-- All --</option>';
        $.each(Object.keys(vendors).sort(), function(i, v) {
            opts += '<option value="' + v + '"' + (v === current ? ' selected' : '') + '>' + v + '</option>';
        });
        $('#sk-inv-filter-vendor').html(opts);
    }

    function applyInvFilters() {
        var vendor = $('#sk-inv-filter-vendor').val();
        var rows = _allInvRows.filter(function(r) {
            return !vendor || r.vendor_name === vendor;
        });
        renderInvoiceTable(rows);
    }

    function renderInvoiceTable(rows) {
        if (!_allInvRows.length) {
            $('#sk-issued-body').html('<p class="sk-empty">No orders found.</p>');
            return;
        }
        if (!rows.length) {
            $('#sk-issued-body').html('<p class="sk-empty">No matching orders found.</p>');
            return;
        }
        var btnStyle = 'border:none;border-radius:20px;padding:5px 14px;font-size:11px;font-weight:400;cursor:pointer;white-space:nowrap;';
        var thSub    = 'padding:6px 10px;font-weight:700;font-size:11px;color:#000;border:1px solid #bbb;letter-spacing:0.3px;';
        var html = '<table class="table table-bordered procu-table">'
            + '<thead><tr>'
            + '<th style="width:32px;">#</th>'
            + '<th>Vendor</th>'
            + '<th style="width:160px;">Order No.</th>'
            + '<th style="width:90px;">Date</th>'
            + '<th>Description</th>'
            + '<th style="text-align:center;white-space:nowrap;width:320px;">Actions</th>'
            + '</tr></thead><tbody>';
        var rowNum         = 0;
        var currentResType = null;
        $.each(rows, function(i, r) {
            var resType = r.resource_type_name || 'Other';
            if (resType !== currentResType) {
                currentResType = resType;
                html += '<tr style="background:#c8c8c8;">'
                    + '<td colspan="6" style="' + thSub + '">' + resType + '</td></tr>';
            }
            rowNum++;
            var cancelled = (r.cancelled == 1 || r.cancelled === '1');
            var rowStyle  = cancelled ? 'background:#fff0f0;' : '';
            var badge     = cancelled ? ' <span style="font-size:10px;font-weight:700;color:#c0392b;letter-spacing:0.4px;">[CANCELLED]</span>' : '';
            var actionsHtml = (resType !== 'Materials'
                ? '<button type="button" class="sk-viewpo-btn"'
                  + ' data-order-id="' + r.order_id + '"'
                  + ' data-ordernumber="' + r.ordernumber + '"'
                  + ' style="' + btnStyle + 'background:#465365;color:#fff;margin-right:6px;">'
                  + 'View PO</button>'
                : '')
                + (cancelled || r.grn_fully_received == 1
                    ? '<button disabled style="' + btnStyle + 'background:#e0e0e0;color:#aaa;cursor:not-allowed;">'
                      + (r.grn_fully_received == 1 ? 'GRN Issued' : 'Goods Received Note')
                      + '</button>'
                    : '<button type="button" class="sk-grn-btn"'
                      + ' data-order-id="' + r.order_id + '"'
                      + ' data-ordernumber="' + r.ordernumber + '"'
                      + ' data-vendor-name="' + $('<div>').text(r.vendor_name || '').html() + '"'
                      + ' style="' + btnStyle + 'background:#072c47;color:#fff;">'
                      + 'Goods Received Note</button>');
            html += '<tr class="' + (rowNum % 2 === 0 && !cancelled ? 'procu-even' : '') + '" style="' + rowStyle + '">'
                + '<td>' + rowNum + '</td>'
                + '<td>' + r.vendor_name + badge + '</td>'
                + '<td>' + r.ordernumber + '</td>'
                + '<td>' + (r.orderdate || '') + '</td>'
                + '<td style="color:#465365;">' + (r.first_item || '—') + '</td>'
                + '<td style="padding:3px 8px;text-align:center;">' + actionsHtml + '</td></tr>';
        });
        html += '</tbody></table>';
        $('#sk-issued-body').html(html);
    }

    function loadIssuedIndents() {
        _allIssuedIndentRows = [];
        $('#sk-loader').show();
        $('#sk-body').html('');
        $.ajax({
            type: 'POST', url: '../storekeeper/issuedindents', dataType: 'json',
            success: function(data) {
                $('#sk-loader').hide();
                if (data.error !== 'No') {
                    $('#sk-body').html('<p class="sk-empty">Failed to load data.</p>');
                    return;
                }
                _allIssuedIndentRows = data.rows || [];
                renderIssuedIndentsTable();
            },
            error: function() {
                $('#sk-loader').hide();
                $('#sk-body').html('<p class="sk-empty">Failed to load data. Please try again.</p>');
            }
        });
    }

    function renderIssuedIndentsTable() {
        var backBtn = '<div style="padding:10px 0 12px;text-align:right;">'
            + '<button type="button" id="sk-indent-back-btn" style="background:#465365;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;">&larr; Back to Indent Page</button>'
            + '</div>';
        if (!_allIssuedIndentRows.length) {
            $('#sk-body').html(backBtn + '<p class="sk-empty">No indents have been raised yet.</p>');
            return;
        }
        var html = backBtn
            + '<div style="background:#072c47;color:#fff;padding:10px 20px;font-size:14px;font-weight:600;letter-spacing:0.5px;">Issued Indents</div>'
            + '<div style="height:14px;"></div>'
            + '<table class="table table-bordered sk-table">'
            + '<thead><tr>'
            + '<th style="width:32px;">#</th>'
            + '<th>Resource</th>'
            + '<th style="width:140px;">Task</th>'
            + '<th style="width:70px;">Unit</th>'
            + '<th style="text-align:right;width:110px;">Stock at Site</th>'
            + '<th style="text-align:right;width:110px;">Reorder Qty</th>'
            + '<th style="width:120px;">Raised By</th>'
            + '<th style="width:130px;">Raised At</th>'
            + '</tr></thead><tbody>';
        var rowNum         = 0;
        var currentResType = null;
        $.each(_allIssuedIndentRows, function(i, r) {
            var resType = r.resource_type || 'Other';
            if (resType !== currentResType) {
                currentResType = resType;
                html += '<tr><td colspan="8" style="border:none;padding:0;height:7px;background:#fff;"></td></tr>'
                    + '<tr style="background:#c8c8c8;">'
                    + '<td colspan="8" style="padding:6px 10px;font-weight:700;font-size:11px;color:#000;border:1px solid #bbb;letter-spacing:0.3px;">'
                    + resType + '</td></tr>';
            }
            rowNum++;
            var raisedAt = r.raised_at ? r.raised_at.substring(0, 16) : '—';
            html += '<tr class="' + (rowNum % 2 === 0 ? 'sk-even' : '') + '">'
                + '<td>' + rowNum + '</td>'
                + '<td>' + r.resource_name + '</td>'
                + '<td style="font-size:12px;color:#465365;">' + (r.task_name || '—') + '</td>'
                + '<td>' + (r.unit || '') + '</td>'
                + '<td class="num">' + (parseFloat(r.stock_at_site || 0).toLocaleString(undefined, {maximumFractionDigits:2})) + '</td>'
                + '<td class="num">' + (parseFloat(r.reorder_quantity || 0).toLocaleString(undefined, {maximumFractionDigits:2})) + '</td>'
                + '<td style="font-size:12px;">' + (r.raised_by || '—') + '</td>'
                + '<td style="font-size:12px;color:#465365;">' + raisedAt + '</td>'
                + '</tr>';
        });
        html += '</tbody></table>';
        $('#sk-body').html(html);
    }

    function loadIssued() {
        _allInvRows = [];
        $('#sk-issued-loader').show();
        $('#sk-issued-body').html('');
        $.ajax({
            type: 'POST', url: '../storekeeper/issued', dataType: 'json',
            success: function(data) {
                $('#sk-issued-loader').hide();
                if (data.error !== 'No') {
                    $('#sk-issued-body').html('<p class="sk-empty">Failed to load data.</p>');
                    return;
                }
                _allInvRows = $.map(data.rows || [], function(r) { return $.extend({ type: 'PO' }, r); });
                _allInvRows.sort(function(a, b) {
                    var ta = a.resource_type_name || 'Other';
                    var tb = b.resource_type_name || 'Other';
                    return ta < tb ? -1 : ta > tb ? 1 : 0;
                });
                populateInvVendorFilter();
                applyInvFilters();
            },
            error: function() {
                $('#sk-issued-loader').hide();
                $('#sk-issued-body').html('<p class="sk-empty">Failed to load data. Please try again.</p>');
            }
        });
    }

    var _reportProgressUrl = '<?php echo Yii::$app->urlManager->createUrl("report/index"); ?>' + '?layout=false';

    function loadReportProgress() {
        $('#sk-reportprogress-loader').show();
        $('#sk-reportprogress-body').html('');
        $.ajax({
            url: _reportProgressUrl,
            success: function(html) {
                $('#sk-reportprogress-loader').hide();
                $('#sk-reportprogress-body').html(html);
            },
            error: function() {
                $('#sk-reportprogress-loader').hide();
                $('#sk-reportprogress-body').html('<p class="sk-empty">Failed to load Report Progress.</p>');
            }
        });
    }

    function loadMbWo() {
        _allMbRows = [];
        $('#sk-mbwo-loader').show();
        $('#sk-mbwo-body').html('');
        $.ajax({
            type: 'POST', url: '../storekeeper/issuedwo', dataType: 'json',
            success: function(data) {
                $('#sk-mbwo-loader').hide();
                if (data.error !== 'No') {
                    $('#sk-mbwo-body').html('<p class="sk-empty">Failed to load data.</p>');
                    return;
                }
                _allMbRows = $.map(data.rows || [], function(r) {
                    return {
                        type:               'WO',
                        order_id:           r.WO_Id,
                        ordernumber:        r.WO_Number,
                        orderdate:          r.Date_requested,
                        cancelled:          r.cancelled,
                        vendor_name:        r.vendor_name,
                        first_item:         r.activity_name || '',
                        resource_type_name: r.resource_type_name || 'Other'
                    };
                });
                _allMbRows.sort(function(a, b) {
                    var ta = a.resource_type_name || 'Other';
                    var tb = b.resource_type_name || 'Other';
                    return ta < tb ? -1 : ta > tb ? 1 : 0;
                });
                populateMbVendorFilter();
                applyMbFilters();
            },
            error: function() {
                $('#sk-mbwo-loader').hide();
                $('#sk-mbwo-body').html('<p class="sk-empty">Failed to load data. Please try again.</p>');
            }
        });
    }

    function populateMbVendorFilter() {
        var vendors = {};
        $.each(_allMbRows, function(i, r) { if (r.vendor_name) vendors[r.vendor_name] = true; });
        var current = $('#sk-mbwo-filter-vendor').val();
        var opts = '<option value="">-- All --</option>';
        $.each(Object.keys(vendors).sort(), function(i, v) {
            opts += '<option value="' + v + '"' + (v === current ? ' selected' : '') + '>' + v + '</option>';
        });
        $('#sk-mbwo-filter-vendor').html(opts);
    }

    function applyMbFilters() {
        var vendor = $('#sk-mbwo-filter-vendor').val();
        var rows = _allMbRows.filter(function(r) { return !vendor || r.vendor_name === vendor; });
        renderMbTable(rows);
    }

    function renderMbTable(rows) {
        if (!_allMbRows.length) {
            $('#sk-mbwo-body').html('<p class="sk-empty">No work orders found.</p>');
            return;
        }
        if (!rows.length) {
            $('#sk-mbwo-body').html('<p class="sk-empty">No matching work orders found.</p>');
            return;
        }
        var btnStyle = 'border:none;border-radius:20px;padding:5px 14px;font-size:11px;font-weight:400;cursor:pointer;white-space:nowrap;';
        var thSub    = 'padding:6px 10px;font-weight:700;font-size:11px;color:#000;border:1px solid #bbb;letter-spacing:0.3px;';
        var html = '<table class="table table-bordered procu-table">'
            + '<thead><tr>'
            + '<th style="width:32px;">#</th>'
            + '<th>Vendor</th>'
            + '<th style="width:160px;">WO No.</th>'
            + '<th style="width:90px;">Date</th>'
            + '<th>Activity</th>'
            + '<th style="text-align:center;white-space:nowrap;width:200px;">Actions</th>'
            + '</tr></thead><tbody>';
        var rowNum         = 0;
        var currentResType = null;
        $.each(rows, function(i, r) {
            var resType = r.resource_type_name || 'Other';
            if (resType !== currentResType) {
                currentResType = resType;
                html += '<tr style="background:#c8c8c8;">'
                    + '<td colspan="6" style="' + thSub + '">' + resType + '</td></tr>';
            }
            rowNum++;
            var cancelled = (r.cancelled == 1 || r.cancelled === '1');
            var rowStyle  = cancelled ? 'background:#fff0f0;' : '';
            var badge     = cancelled ? ' <span style="font-size:10px;font-weight:700;color:#c0392b;">[CANCELLED]</span>' : '';
            var actionBtn = cancelled
                ? '<button disabled style="' + btnStyle + 'background:#e0e0e0;color:#aaa;cursor:not-allowed;">Measurement Book</button>'
                : '<button type="button" class="sk-mb-btn"'
                  + ' data-wonumber="' + r.ordernumber + '"'
                  + ' data-vendor-name="' + $('<div>').text(r.vendor_name || '').html() + '"'
                  + ' style="' + btnStyle + 'background:#072c47;color:#fff;">Measurement Book</button>';
            html += '<tr class="' + (rowNum % 2 === 0 && !cancelled ? 'procu-even' : '') + '" style="' + rowStyle + '">'
                + '<td>' + rowNum + '</td>'
                + '<td>' + r.vendor_name + badge + '</td>'
                + '<td>' + r.ordernumber + '</td>'
                + '<td>' + (r.orderdate || '') + '</td>'
                + '<td style="color:#465365;">' + (r.first_item || '—') + '</td>'
                + '<td style="padding:3px 8px;text-align:center;">' + actionBtn + '</td></tr>';
        });
        html += '</tbody></table>';
        $('#sk-mbwo-body').html(html);
    }

    $(document).on('change', '#sk-mbwo-filter-vendor', applyMbFilters);

    function loadIssuedMbooks() {
        $('#sk-mbwo-loader').show();
        $('#sk-mbwo-body').html('');
        $.ajax({
            type: 'POST', url: '../storekeeper/issuedmbooks', dataType: 'json',
            success: function(data) {
                $('#sk-mbwo-loader').hide();
                if (data.error !== 'No') {
                    $('#sk-mbwo-body').html('<p class="sk-empty">Failed to load data.</p>');
                    return;
                }
                renderIssuedMbooksTable(data.rows || []);
            },
            error: function() {
                $('#sk-mbwo-loader').hide();
                $('#sk-mbwo-body').html('<p class="sk-empty">Failed to load data. Please try again.</p>');
            }
        });
    }

    function renderIssuedMbooksTable(rows) {
        var backBtn = '<div style="padding:10px 0 12px;text-align:right;">'
            + '<button type="button" id="sk-mb-back-btn" style="background:#465365;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;">&larr; Back to Work Orders</button>'
            + '</div>';
        if (!rows.length) {
            $('#sk-mbwo-body').html(backBtn + '<p class="sk-empty">No measurement books submitted yet.</p>');
            return;
        }
        var btnStyle = 'border:none;border-radius:20px;padding:5px 14px;font-size:11px;cursor:pointer;margin-right:4px;';
        var html = backBtn
            + '<div style="background:#072c47;color:#fff;padding:10px 20px;font-size:14px;font-weight:600;letter-spacing:0.5px;">Issued Measurement Books</div>'
            + '<div style="height:14px;"></div>'
            + '<table class="table table-bordered procu-table">'
            + '<thead><tr>'
            + '<th style="width:32px;">#</th>'
            + '<th style="width:150px;">MB No.</th>'
            + '<th style="width:100px;">MB Date</th>'
            + '<th style="width:160px;">WO No.</th>'
            + '<th>Vendor</th>'
            + '<th>Activity</th>'
            + '<th style="width:140px;">Created At</th>'
            + '<th style="text-align:center;width:220px;">Actions</th>'
            + '</tr></thead><tbody>';
        $.each(rows, function(i, r) {
            var createdAt  = r.created_at ? r.created_at.substring(0, 16) : '—';
            var cancelled  = (r.delete_status == 1 || r.delete_status === '1');
            var rowStyle   = cancelled ? 'background:#f5f5f5;color:#bbb;' : '';
            var viewBtn    = '<button type="button" class="sk-mb-view-btn" data-id="' + r.id + '" style="' + btnStyle + 'background:#072c47;color:#fff;">View M.Book</button>';
            html += '<tr class="' + ((i + 1) % 2 === 0 && !cancelled ? 'procu-even' : '') + '" style="' + rowStyle + '">'
                + '<td>' + (i + 1) + '</td>'
                + '<td style="font-weight:600;color:' + (cancelled ? '#bbb' : '#072c47') + ';">' + r.mb_number + '</td>'
                + '<td>' + (r.mb_date || '—') + '</td>'
                + '<td>' + r.wo_number + '</td>'
                + '<td>' + (r.vendor_name || '—') + '</td>'
                + '<td style="color:#465365;">' + (r.activity_name || '—') + '</td>'
                + '<td style="font-size:12px;color:#465365;">' + createdAt + '</td>'
                + '<td style="padding:3px 8px;text-align:center;">' + viewBtn + '</td>'
                + '</tr>';
        });
        html += '</tbody></table>';
        $('#sk-mbwo-body').html(html);
    }

    $(document).on('click', '#sk-issued-mb-btn', function() {
        $('#sk-mbwo-filter-bar').hide();
        loadIssuedMbooks();
    });

    $(document).on('click', '#sk-mb-back-btn', function() {
        $('#sk-mbwo-filter-bar').show();
        loadMbWo();
    });

    $(document).on('click', '.sk-mb-view-btn', function(){
        var id = $(this).data('id');
        $('#mb-body').html('<p style="color:#888;font-size:12px;padding:8px 0;">Loading...</p>');
        $('#mb-serial-number').text('—');
        $('#mb-vendor-name').text('—');
        $('#mb-wo-display').text('—');
        $('#mb-wo-number').text('');
        $('#mb-date-input').val('').prop('readonly', true).css({background:'#f4f7fa', color:'#333'});
        $('#mb-save-btn').hide();
        $('#mb-cancel-btn').text('Close');
        $('#mb-overlay, #mb-popup').show();
        $('#mb-popup').css('display', 'flex').data('viewmode', true);

        $.ajax({
            type: 'POST', url: '../storekeeper/viewmb',
            data: { id: id }, dataType: 'json',
            success: function(data) {
                if (data.error !== 'No') {
                    $('#mb-body').html('<p style="color:#c0392b;font-size:12px;">' + (data.errortext || 'Error loading.') + '</p>');
                    return;
                }
                $('#mb-serial-number').text(data.mb_number || '—');
                $('#mb-vendor-name').text(data.vendor_name || '—');
                $('#mb-wo-display').text(data.wo_number || '—');
                $('#mb-wo-number').text(data.wo_number || '');
                $('#mb-date-input').val(data.mb_date || '');

                var inpRo = 'type="number" step="any" readonly style="width:62px;height:24px;font-size:12px;display:inline-block;padding:2px 4px;background:#f4f7fa;border-color:#c5ccd4;color:#333;cursor:default;"';
                var html = '';
                $.each(data.entries || [], function(ai, act) {
                    html += '<div style="margin-bottom:18px;border:1px solid #d0d7df;border-radius:4px;overflow:hidden;">'
                        + '<div style="background:#072c47;color:#fff;padding:7px 12px;font-size:12px;font-weight:700;">' + (act.activity_name || '—') + '</div>'
                        + (function(){
                            var _wo  = parseFloat(act.wo_qty) || 0;
                            var _cum = parseFloat(act.cumulative_qty) || 0;
                            var _cur = act.qty != null ? +parseFloat(act.qty).toFixed(2) : 0;
                            var _bil = Math.max(0, _cum - _cur);
                            var _rem = Math.max(0, _wo - _cum);
                            var fmt  = function(n){ return +n.toFixed(2); };
                            var lbl  = function(t){ return '<span style="font-size:11px;font-weight:600;color:#555;white-space:nowrap;">'+t+'</span>'; };
                            var val  = function(v){ return '<span style="font-size:12px;color:#333;white-space:nowrap;">'+fmt(v)+'</span>'; };
                            var cell = function(label, content){ return '<div style="display:flex;flex-direction:column;align-items:center;flex:1;min-width:0;padding:4px 8px;">'+lbl(label)+'<div style="margin-top:2px;">'+content+'</div></div>'; };
                            return '<div style="padding:6px 0;background:#f4f7fa;display:flex;align-items:stretch;border-bottom:1px solid #d0d7df;width:100%;">'
                                + cell('Unit', '<input type="text" readonly style="width:80px;height:22px;font-size:12px;display:inline-block;padding:1px 6px;text-align:center;background:#f4f7fa;border-color:#c5ccd4;color:#333;cursor:default;" value="' + (act.unit || '') + '" placeholder="—">')
                                + '<div style="width:1px;background:#d0d7df;margin:4px 0;"></div>'
                                + cell('Billed Qty', val(_bil))
                                + '<div style="width:1px;background:#d0d7df;margin:4px 0;"></div>'
                                + cell('Current Qty', '<span style="font-size:13px;font-weight:700;color:#001033;white-space:nowrap;">' + _cur + '</span>')
                                + '<div style="width:1px;background:#d0d7df;margin:4px 0;"></div>'
                                + cell('Remaining Qty', val(_rem))
                                + '<div style="width:1px;background:#d0d7df;margin:4px 0;"></div>'
                                + cell('WO Qty', val(_wo))
                                + '</div>';
                        })();

                    if (act.tasks && act.tasks.length) {
                        html += '<table class="table table-bordered" style="margin:0;font-size:12px;table-layout:fixed;width:100%;">'
                            + '<thead><tr style="background:#dce3ea;">'
                            + '<th style="width:28px;padding:4px 5px;font-size:10px;white-space:nowrap;">#</th>'
                            + '<th style="padding:4px 6px;font-size:10px;white-space:nowrap;">Task</th>'
                            + '<th style="width:58px;padding:4px 5px;font-size:10px;white-space:nowrap;">Unit</th>'
                            + '<th style="width:80px;padding:4px 5px;font-size:10px;white-space:nowrap;text-align:right;background:#e8f0fb;">Rate</th>'
                            + '<th style="width:110px;padding:4px 5px;font-size:10px;white-space:nowrap;text-align:right;">Work Done</th>'
                            + '<th style="width:82px;padding:4px 5px;font-size:10px;white-space:nowrap;text-align:right;">Amount</th>'
                            + '<th style="width:90px;padding:4px 5px;font-size:10px;white-space:nowrap;text-align:right;">Qty Till Today</th>'
                            + '<th style="width:95px;padding:4px 5px;font-size:10px;white-space:nowrap;text-align:right;">Amt Till Today</th>'
                            + '</tr></thead><tbody>';
                        $.each(act.tasks, function(ti, task) {
                            var rate     = (task.rate != null && task.rate !== '') ? parseFloat(task.rate) : '';
                            var workDone = (task.work_done != null && task.work_done !== '') ? parseFloat(task.work_done) : '';
                            var amount   = (rate !== '' && rate > 0 && workDone !== '') ? (rate * workDone).toFixed(2) : '—';
                            var cumWd    = parseFloat(task.cum_work_done) || 0;
                            var cumAmt   = parseFloat(task.cum_amount)    || 0;
                            var roInp    = 'type="number" step="any" readonly style="height:24px;font-size:12px;text-align:right;margin-left:auto;border:1px solid #b8cce4;border-radius:3px;padding:2px 4px;cursor:default;"';
                            html += '<tr>'
                                + '<td style="padding:4px 6px;">' + (ti + 1) + '</td>'
                                + '<td style="padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="' + (task.task_name || '') + '">' + (task.task_name || '') + '</td>'
                                + '<td style="padding:4px 6px;">' + (task.unit || '') + '</td>'
                                + '<td style="padding:2px 4px;text-align:right;background:#e8f0fb;"><input ' + roInp + ' value="' + rate + '" style="width:70px;height:24px;font-size:12px;text-align:right;margin-left:auto;background:#e8f0fb;border-color:#b8cce4;color:#000;font-weight:600;cursor:default;"></td>'
                                + '<td style="padding:2px 4px;text-align:right;"><input ' + roInp + ' value="' + workDone + '" style="width:98px;height:24px;font-size:12px;text-align:right;margin-left:auto;background:#f4f7fa;border-color:#c5ccd4;color:#333;"></td>'
                                + '<td style="padding:4px 6px;text-align:right;">' + amount + '</td>'
                                + '<td style="padding:4px 6px;text-align:right;">' + (cumWd > 0 ? +cumWd.toFixed(2) : '—') + '</td>'
                                + '<td style="padding:4px 6px;text-align:right;">' + (cumAmt > 0 ? +cumAmt.toFixed(2) : '—') + '</td>'
                                + '</tr>';
                        });
                        html += '</tbody></table>';
                    } else {
                        html += '<p style="padding:8px 12px;font-size:12px;color:#888;margin:0;">No tasks.</p>';
                    }
                    html += '</div>';
                });
                $('#mb-body').html(html || '<p style="color:#888;font-size:12px;">No activities found.</p>');
            },
            error: function() {
                $('#mb-body').html('<p style="color:#c0392b;font-size:12px;">Failed to load measurement book.</p>');
            }
        });
    });

    $(document).on('click', '.sk-mb-cancel-btn, .sk-mb-recover-btn', function(){
        var $btn         = $(this);
        var isCancelling = $btn.hasClass('sk-mb-cancel-btn');
        var id           = $btn.data('id');
        var url          = isCancelling ? '../storekeeper/cancelmb' : '../storekeeper/recovermb';
        $btn.prop('disabled', true).text(isCancelling ? 'Cancelling...' : 'Recovering...');
        $.ajax({
            type: 'POST', url: url,
            data: { id: id }, dataType: 'json',
            success: function(data) {
                if (data.error !== 'No') {
                    $btn.prop('disabled', false).text(isCancelling ? 'Cancel' : 'Recover');
                    alert(data.errortext || 'Error.');
                    return;
                }
                var $row = $btn.closest('tr');
                if (isCancelling) {
                    $row.css({ background: '#f5f5f5', color: '#bbb' });
                    $btn.removeClass('sk-mb-cancel-btn').addClass('sk-mb-recover-btn')
                        .css('background', '#27ae60').text('Recover').prop('disabled', false);
                } else {
                    $row.css({ background: '', color: '' });
                    $btn.removeClass('sk-mb-recover-btn').addClass('sk-mb-cancel-btn')
                        .css('background', '#c0392b').text('Cancel').prop('disabled', false);
                }
            },
            error: function() {
                $btn.prop('disabled', false).text(isCancelling ? 'Cancel' : 'Recover');
                alert('Failed. Please try again.');
            }
        });
    });

    // Issued GRNs — load
    function loadIssuedGrns() {
        $('#sk-issued-loader').show();
        $('#sk-issued-body').html('');
        $.ajax({
            type: 'POST', url: '../storekeeper/issuedgrns', dataType: 'json',
            success: function(data) {
                $('#sk-issued-loader').hide();
                if (data.error !== 'No') {
                    $('#sk-issued-body').html('<p class="sk-empty">Failed to load data.</p>');
                    return;
                }
                renderIssuedGrnsTable(data.rows || []);
            },
            error: function() {
                $('#sk-issued-loader').hide();
                $('#sk-issued-body').html('<p class="sk-empty">Failed to load data. Please try again.</p>');
            }
        });
    }

    function renderIssuedGrnsTable(rows) {
        var backBtn = '<div style="padding:10px 0 12px;text-align:right;">'
            + '<button type="button" id="sk-grn-back-btn" style="background:#465365;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;">&larr; Back to GRN List</button>'
            + '</div>';
        if (!rows.length) {
            $('#sk-issued-body').html(backBtn + '<p class="sk-empty">No GRNs issued yet.</p>');
            return;
        }
        var btnStyle = 'border:none;border-radius:20px;padding:5px 14px;font-size:11px;cursor:pointer;margin-right:4px;';
        var html = backBtn
            + '<div style="background:#072c47;color:#fff;padding:10px 20px;font-size:14px;font-weight:600;letter-spacing:0.5px;">Issued Goods Received Notes</div>'
            + '<div style="height:14px;"></div>'
            + '<table class="table table-bordered procu-table">'
            + '<thead><tr>'
            + '<th style="width:32px;">#</th>'
            + '<th style="width:150px;">GRN No.</th>'
            + '<th style="width:100px;">GRN Date</th>'
            + '<th style="width:160px;">PO No.</th>'
            + '<th>Vendor</th>'
            + '<th style="text-align:center;width:220px;">Actions</th>'
            + '</tr></thead><tbody>';
        $.each(rows, function(i, r) {
            var cancelled  = (r.delete_status == 1 || r.delete_status === '1');
            var rowStyle   = cancelled ? 'background:#f5f5f5;color:#bbb;' : '';
            // Issued GRNs cannot be cancelled — receipt is final; shortfalls need a new PO
            var viewBtn    = '<button type="button" class="sk-grn-view-btn" data-grn-number="' + r.grn_number + '" style="' + btnStyle + 'background:#072c47;color:#fff;">View GRN</button>';
            html += '<tr class="' + ((i + 1) % 2 === 0 && !cancelled ? 'procu-even' : '') + '" style="' + rowStyle + '">'
                + '<td>' + (i + 1) + '</td>'
                + '<td style="font-weight:600;color:' + (cancelled ? '#bbb' : '#072c47') + ';">' + r.grn_number + '</td>'
                + '<td>' + (r.GRN_Date || '—') + '</td>'
                + '<td>' + (r.ordernumber || '—') + '</td>'
                + '<td>' + (r.vendor_name || '—') + '</td>'
                + '<td style="padding:3px 8px;text-align:center;">' + viewBtn + '</td>'
                + '</tr>';
        });
        html += '</tbody></table>';
        $('#sk-issued-body').html(html);
    }

    $(document).on('click', '#sk-issued-grn-btn', function() {
        $('#sk-inv-filter-bar').hide();
        loadIssuedGrns();
    });

    $(document).on('click', '#sk-grn-back-btn', function() {
        $('#sk-inv-filter-bar').show();
        loadIssued();
    });

    $(document).on('click', '.sk-grn-view-btn', function(){
        var grnNum = $(this).data('grn-number');
        $('#grn-items-body').html('<p style="color:#888;font-size:12px;padding:8px 0;">Loading...</p>');
        $('#grn-serial-number').text('—');
        $('#grn-vendor-name').text('—');
        $('#grn-po-display').text('—');
        $('#grn-po-number').text('—');
        $('#grn-receipt-date').val('').prop('readonly', true).css({ background: '#f4f7fa', color: '#333' });
        $('#grn-remarks').prop('readonly', true).css({ background: '#f4f7fa', color: '#333' });
        $('#grn-send-btn').hide();
        $('#grn-cancel').text('Close');
        $('#grn-overlay, #grn-popup').show();
        $('#grn-popup').data('viewmode', true);

        $.ajax({
            type: 'POST', url: '../storekeeper/viewgrn',
            data: { grn_number: grnNum }, dataType: 'json',
            success: function(data) {
                if (data.error !== 'No') {
                    $('#grn-items-body').html('<p style="color:#c0392b;font-size:12px;">' + (data.errortext || 'Error loading.') + '</p>');
                    return;
                }
                $('#grn-serial-number').text(data.grn_number || '—');
                $('#grn-vendor-name').text(data.vendor_name || '—');
                $('#grn-po-display').text(data.ordernumber || '—');
                $('#grn-po-number').text(data.ordernumber || '—');
                $('#grn-receipt-date').val(data.grn_date || '');
                $('#grn-remarks').val(data.remarks || '');

                if (!data.items || !data.items.length) {
                    $('#grn-items-body').html('<p style="color:#888;font-size:12px;">No items found.</p>');
                    return;
                }
                var html = '<table class="table table-bordered sk-table" style="margin-bottom:0;table-layout:fixed;">'
                    + '<colgroup>'
                    + '<col style="width:34px;"><col>'
                    + '<col style="width:58px;"><col style="width:95px;"><col style="width:110px;"><col style="width:88px;">'
                    + '<col style="width:100px;"><col style="width:100px;">'
                    + '</colgroup>'
                    + '<thead><tr>'
                    + '<th style="text-align:center;">#</th>'
                    + '<th>Item</th>'
                    + '<th style="text-align:center;font-size:11px;">Unit</th>'
                    + '<th style="text-align:right;font-size:11px;white-space:nowrap;">Received Qty</th>'
                    + '<th style="text-align:right;font-size:11px;">Rate</th>'
                    + '<th style="text-align:right;font-size:11px;">Amount</th>'
                    + '<th style="text-align:right;font-size:11px;white-space:nowrap;white-space:nowrap;">Rcvd Till Today</th>'
                    + '<th style="text-align:right;font-size:11px;white-space:nowrap;">Amt Till Today</th>'
                    + '</tr></thead><tbody>';
                $.each(data.items, function(i, item) {
                    var qty    = parseFloat(item.qty)  || 0;
                    // Legacy GRNs saved without a rate fall back to the PO rate
                    var rate   = parseFloat(item.rate) || parseFloat(item.po_rate) || 0;
                    var amount = (qty > 0 && rate > 0) ? (qty * rate).toFixed(2) : '—';
                    var rcvdTot = parseFloat(item.total_received) || 0;
                    var amtTot  = parseFloat(item.total_amount)  || 0;
                    html += '<tr>'
                        + '<td style="text-align:center;">' + (i + 1) + '</td>'
                        + '<td>' + (item.resource_name || '—') + '</td>'
                        + '<td style="text-align:center;font-size:12px;">' + (item.unit || '—') + '</td>'
                        + '<td style="text-align:right;font-size:12px;padding-right:6px;font-weight:600;color:#072c47;">' + qty + '</td>'
                        + '<td style="text-align:right;font-size:12px;padding-right:6px;">' + (rate > 0 ? rate.toFixed(2) : '—') + '</td>'
                        + '<td style="text-align:right;font-size:12px;padding-right:6px;">' + amount + '</td>'
                        + '<td style="text-align:right;font-size:12px;padding-right:6px;font-weight:600;background:#eef4fc;">' + rcvdTot.toFixed(2) + '</td>'
                        + '<td style="text-align:right;font-size:12px;padding-right:6px;background:#eef4fc;">' + (amtTot > 0 ? amtTot.toFixed(2) : '—') + '</td>'
                        + '</tr>';
                });
                html += '</tbody></table>';
                $('#grn-items-body').html(html);
            },
            error: function() {
                $('#grn-items-body').html('<p style="color:#c0392b;font-size:12px;">Failed to load GRN.</p>');
            }
        });
    });

    $(document).on('change', '#sk-inv-filter-vendor', applyInvFilters);

    // View WO popup — open
    $(document).on('click', '.sk-viewwo-btn', function(){
        var woNum = $(this).data('wonumber');
        $('#sk-wo-preview-number').text(woNum);
        $('#sk-wo-preview-content').html('<p style="color:#888;padding:20px;text-align:center;">Loading...</p>');
        $('#sk-wo-preview-panel').addClass('active');
        $('body').css('overflow-y', 'hidden');
        $.ajax({
            type: 'POST', url: '../procurement/viewwo',
            data: { wo_number: woNum }, dataType: 'json',
            success: function(data) {
                if (data.error !== 'No') {
                    $('#sk-wo-preview-content').html('<p style="color:#c0392b;padding:20px;">' + (data.errortext || 'Error loading WO.') + '</p>');
                    return;
                }
                $('#sk-wo-preview-content').html(data.html);
            },
            error: function() {
                $('#sk-wo-preview-content').html('<p style="color:#c0392b;padding:20px;">Failed to load work order.</p>');
            }
        });
    });

    $(document).on('click', '#sk-wo-preview-close', function(){
        $('#sk-wo-preview-panel').removeClass('active');
        $('body').css('overflow-y', '');
    });

    $(document).on('click', '#sk-wo-print-btn', function(){
        var content = $('#sk-wo-preview-content').html();
        var win = window.open('', '_blank', 'width=960,height=720');
        win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Work Order</title>'
            + '<style>body{margin:20px;font-family:Arial,sans-serif;}table{border-collapse:collapse;width:100%;}td,th{border:1px solid #ccc;padding:6px 8px;font-size:12px;}</style>'
            + '</head><body>' + content + '</body></html>');
        win.document.close();
        win.focus();
        setTimeout(function(){ win.print(); }, 400);
    });

    // View PO popup — open
    $(document).on('click', '.sk-viewpo-btn', function(){
        var orderId  = $(this).data('order-id');
        var orderNum = $(this).data('ordernumber');
        $('#sk-po-preview-number').text(orderNum);
        $('#sk-po-preview-content').html('<p style="color:#888;padding:20px;text-align:center;">Loading...</p>');
        $('#sk-po-preview-panel').addClass('active');
        $('body').css('overflow-y', 'hidden');
        $.ajax({
            type: 'POST', url: '../procurement/viewpo',
            data: { order_id: orderId }, dataType: 'json',
            success: function(data) {
                if (data.error !== 'No') {
                    $('#sk-po-preview-content').html('<p style="color:#c0392b;padding:20px;">' + (data.errortext || 'Error loading PO.') + '</p>');
                    return;
                }
                $('#sk-po-preview-content').html(data.html);
            },
            error: function() {
                $('#sk-po-preview-content').html('<p style="color:#c0392b;padding:20px;">Failed to load purchase order.</p>');
            }
        });
    });

    $(document).on('click', '#sk-po-preview-close', function(){
        $('#sk-po-preview-panel').removeClass('active');
        $('body').css('overflow-y', '');
    });

    $(document).on('click', '#sk-po-print-btn', function(){
        var content = $('#sk-po-preview-content').html();
        var win = window.open('', '_blank', 'width=960,height=720');
        win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Purchase Order</title>'
            + '<style>body{margin:20px;font-family:Arial,sans-serif;}table{border-collapse:collapse;width:100%;}td,th{border:1px solid #ccc;padding:6px 8px;font-size:12px;}</style>'
            + '</head><body>' + content + '</body></html>');
        win.document.close();
        win.focus();
        setTimeout(function(){ win.print(); }, 400);
    });

    // GRN popup — open
    $(document).on('click', '.sk-grn-btn', function(){
        var orderId    = $(this).data('order-id');
        var orderNum   = $(this).data('ordernumber');
        var vendorName = $(this).data('vendor-name') || '—';
        var today      = new Date().toISOString().slice(0, 10);
        $('#grn-order-id').val(orderId);
        $('#grn-po-number').text(orderNum);
        $('#grn-po-display').text(orderNum);
        $('#grn-serial-number').text('Loading...');
        $('#grn-vendor-name').text(vendorName);
        $('#grn-receipt-date').val(today);
        $('#grn-remarks').val('');
        $.ajax({
            type: 'POST', url: '../storekeeper/grnnext', dataType: 'json',
            success: function(d) {
                $('#grn-serial-number').text(d.error === 'No' ? d.grn_number : '—');
            },
            error: function() { $('#grn-serial-number').text('—'); }
        });
        $('#grn-items-body').html('<p style="color:#888;font-size:12px;padding:8px 0;">Loading items...</p>');
        $('#grn-overlay, #grn-popup').show();
        $.ajax({
            type: 'POST', url: '../storekeeper/grnitems',
            data: { order_id: orderId }, dataType: 'json',
            success: function(data) {
                if (data.error !== 'No' || !data.rows || !data.rows.length) {
                    $('#grn-items-body').html('<p style="color:#888;font-size:12px;">No items found for this PO.</p>');
                    return;
                }
                var html = '<table class="table table-bordered sk-table" style="margin-bottom:0;table-layout:fixed;">'
                    + '<colgroup>'
                    + '<col style="width:34px;"><col>'
                    + '<col style="width:58px;"><col style="width:95px;"><col style="width:110px;"><col style="width:88px;">'
                    + '<col style="width:100px;"><col style="width:100px;">'
                    + '</colgroup>'
                    + '<thead><tr>'
                    + '<th style="text-align:center;">#</th>'
                    + '<th>Item</th>'
                    + '<th style="text-align:center;font-size:11px;">Unit</th>'
                    + '<th style="text-align:right;font-size:11px;white-space:nowrap;">Received Qty</th>'
                    + '<th style="text-align:right;font-size:11px;">Rate</th>'
                    + '<th style="text-align:right;font-size:11px;">Amount</th>'
                    + '<th style="text-align:right;font-size:11px;white-space:nowrap;white-space:nowrap;">Rcvd Till Today</th>'
                    + '<th style="text-align:right;font-size:11px;white-space:nowrap;">Amt Till Today</th>'
                    + '</tr></thead><tbody>';
                $.each(data.rows, function(i, r) {
                    var ordered   = parseFloat(r.ordered_qty) || 0;
                    // One GRN per order, no balance carry-over: cap = this order's quantity
                    var remaining = ordered;
                    var poRate   = parseFloat(r.po_rate) || 0;
                    var rcvdTot  = parseFloat(r.total_received) || 0;
                    var amtTot   = parseFloat(r.total_amount)  || 0;
                    html += '<tr>'
                        + '<td style="text-align:center;">' + (i + 1) + '</td>'
                        + '<td>' + r.resource_name + '</td>'
                        + '<td style="text-align:center;font-size:12px;">' + (r.unit || '—') + '</td>'
                        + '<td style="padding:2px 4px;">'
                        + '<input type="number" min="0" max="' + remaining + '" step="any" class="form-control input-sm grn-qty-input"'
                        + ' data-resource-id="' + r.resource_id + '"'
                        + ' data-remaining="' + remaining + '"'
                        + ' placeholder="0"'
                        + ' style="width:100%;height:26px;padding:2px 6px;font-size:12px;text-align:right;">'
                        + '</td>'
                        + '<td style="padding:2px 4px;">'
                        + '<input type="number" min="0" step="any" class="form-control input-sm grn-rate-input"'
                        + ' data-po-rate="' + poRate + '"'
                        + ' value="' + (poRate > 0 ? poRate.toFixed(2) : '') + '"'
                        + ' placeholder="0.00"'
                        + ' style="width:100%;height:26px;padding:2px 6px;font-size:12px;text-align:right;">'
                        + '</td>'
                        + '<td style="text-align:right;font-size:12px;padding-right:6px;vertical-align:middle;">'
                        + '<span class="grn-amount-display">—</span>'
                        + '</td>'
                        + '<td style="text-align:right;font-size:12px;padding-right:6px;vertical-align:middle;font-weight:600;background:#eef4fc;">'
                        + rcvdTot.toFixed(2)
                        + '</td>'
                        + '<td style="text-align:right;font-size:12px;padding-right:6px;vertical-align:middle;background:#eef4fc;">'
                        + (amtTot > 0 ? amtTot.toFixed(2) : '—')
                        + '</td>'
                        + '</tr>';
                });
                html += '</tbody></table>';
                $('#grn-items-body').html(html);
                // Initialize Amount with the prefilled PO rate (updates live as qty is typed)
                $('#grn-items-body .grn-rate-input').trigger('input');
            }
        });
    });

    // MB — restrict qty inputs to 2 decimal places
    $(document).on('input', '.mb-qty, .mb-workdone', function(){
        var m = $(this).val().match(/^-?\d*(\.\d{0,2})?/);
        if (m) $(this).val(m[0]);
    });

    // MB — update Remaining Qty display as user types Current Qty
    $(document).on('input', '.mb-qty', function(){
        var ai  = $(this).data('ai');
        var wo  = parseFloat($(this).data('wo-qty')) || 0;
        var bil = parseFloat($(this).data('bil-qty')) || 0;
        var cur = parseFloat($(this).val()) || 0;
        var rem = +(Math.max(0, wo - bil - cur).toFixed(2));
        $('.mb-rem-display[data-ai="' + ai + '"]').text(rem);
        // Auto-update task work done for this activity
        $('.mb-workdone[data-ai="' + ai + '"]').each(function(){
            var tqpu = parseFloat($(this).data('tqpu')) || 0;
            if (tqpu > 0) $(this).val(+(cur * tqpu).toFixed(2)).trigger('input');
        });
    });

    // MB — clear validation highlight when user fills a field
    $(document).on('input', '.mb-unit, .mb-qty, .mb-workdone', function(){
        if ($(this).val().trim()) $(this).css({ background: '', borderColor: '' });
    });

    // MB — real-time work done limit check (must not exceed QNTY × task_qty/unit) + amount update
    $(document).on('input', '.mb-workdone', function(){
        var ai   = $(this).data('ai');
        var ti   = $(this).data('ti');
        var act  = _mbActivities[ai];
        var task = (act && act.tasks) ? act.tasks[ti] : null;
        if (!task) return;
        var currentQty = parseFloat($('.mb-qty[data-ai="' + ai + '"]').val()) || 0;
        var tqpu       = parseFloat(task.unit_qty || task.task_qty) || 0;
        var maxWd      = currentQty * tqpu;
        var wd         = parseFloat($(this).val()) || 0;
        if (tqpu > 0 && wd > maxWd) {
            $(this).css({ background: '#fde8e8', borderColor: '#c0392b' });
        } else {
            $(this).css({ background: '', borderColor: '' });
        }
        var rate   = parseFloat($('.mb-taskrate[data-ai="' + ai + '"][data-ti="' + ti + '"]').val()) || 0;
        var $amtEl = $('.mb-task-amount[data-ai="' + ai + '"][data-ti="' + ti + '"]');
        $amtEl.text(rate > 0 && wd > 0 ? (rate * wd).toFixed(2) : '—');
    });

    // GRN — auto-calculate Amount = Qty × Rate, warn if over remaining or over PO rate
    $(document).on('input', '.grn-qty-input, .grn-rate-input', function(){
        var $row        = $(this).closest('tr');
        var $qtyInput   = $row.find('.grn-qty-input');
        var $rateInput  = $row.find('.grn-rate-input');
        var qty         = parseFloat($qtyInput.val()) || 0;
        var rate        = parseFloat($rateInput.val()) || 0;
        var remaining   = parseFloat($qtyInput.data('remaining')) || 0;
        var poRate      = parseFloat($rateInput.data('po-rate')) || 0;
        // Over-limit warning on qty
        if (qty > remaining && remaining > 0) {
            $qtyInput.css({ background: '#fde8e8', borderColor: '#c0392b' });
        } else {
            $qtyInput.css({ background: '', borderColor: '' });
        }
        // Over-PO-rate warning
        if ($rateInput.val().trim() !== '') {
            if (poRate > 0 && rate > poRate) {
                $rateInput.css({ background: '#fde8e8', borderColor: '#c0392b' });
            } else {
                $rateInput.css({ background: '', borderColor: '' });
            }
        }
        $row.find('.grn-amount-display').text(qty > 0 || rate > 0 ? (qty * rate).toFixed(2) : '—');
    });

    // GRN popup — close
    $(document).on('click', '#grn-close, #grn-cancel, #grn-overlay', function(){
        $('#grn-overlay, #grn-popup').hide();
        if ($('#grn-popup').data('viewmode')) {
            $('#grn-send-btn').show();
            $('#grn-cancel').text('Cancel');
            $('#grn-receipt-date').prop('readonly', false).css({ background: '', color: '' });
            $('#grn-remarks').prop('readonly', false).css({ background: '', color: '' });
            $('#grn-popup').data('viewmode', false);
        }
    });

    // GRN popup — print
    $(document).on('click', '#grn-print-btn', function(){
        var w = window.open('', '_blank');
        w.document.write('<html><head><title>GRN Print</title>'
            + '<style>body{font-family:Arial,sans-serif;font-size:12px;padding:20px;}'
            + 'table{border-collapse:collapse;width:100%;}'
            + 'th,td{border:1px solid #999;padding:4px 8px;font-size:11px;}'
            + 'th{background:#dce3ea;}'
            + '.hdr{margin-bottom:12px;} .hdr b{display:inline-block;width:130px;}'
            + '</style></head><body>'
            + '<h3 style="margin:0 0 10px;">Goods Received Note</h3>'
            + '<div class="hdr"><b>GRN No.:</b> ' + $('#grn-serial-number').text() + '</div>'
            + '<div class="hdr"><b>Purchase Order:</b> ' + $('#grn-po-display').text() + '</div>'
            + '<div class="hdr"><b>Vendor:</b> ' + $('#grn-vendor-name').text() + '</div>'
            + '<div class="hdr"><b>Date:</b> ' + ($('#grn-receipt-date').val() || '—') + '</div>'
            + $('#grn-items-body').html()
            + '<div style="margin-top:14px;"><b>Remarks:</b> ' + ($('#grn-remarks').val() || '') + '</div>'
            + '</body></html>');
        w.document.close();
        w.print();
    });

    // GRN popup — send
    $(document).on('click', '#grn-send-btn', function(){
        var orderId = $('#grn-order-id').val();

        if (confirm('Press Print if you need a copy.\n\nOK → Print then Report\nCancel → Report without printing')) {
            $('#grn-print-btn').trigger('click');
        }

        // Validate date of receipt
        if (!$('#grn-receipt-date').val()) {
            alert('Please enter the Date of Receipt.');
            $('#grn-receipt-date').focus();
            return;
        }

        var items        = [];
        var overLimit    = false;
        var overRate     = false;
        var missingField = false;

        $('.grn-qty-input').each(function(){
            var $row      = $(this).closest('tr');
            var $rateInp  = $row.find('.grn-rate-input');
            var qtyVal    = $(this).val().trim();
            var rateVal   = $rateInp.val().trim();
            var qty       = parseFloat(qtyVal);
            var rate      = parseFloat(rateVal);
            var remaining = parseFloat($(this).data('remaining')) || 0;
            var poRate    = parseFloat($rateInp.data('po-rate')) || 0;
            var hasQty    = qtyVal !== '' && !isNaN(qty) && qty > 0;
            var hasRate   = rateVal !== '' && !isNaN(rate);

            if (!hasQty) { missingField = true; $(this).css({ background: '#fde8e8', borderColor: '#c0392b' }); }
            else $(this).css({ background: '', borderColor: '' });

            if (!hasRate) { missingField = true; $rateInp.css({ background: '#fde8e8', borderColor: '#c0392b' }); }
            else if (poRate > 0 && rate > poRate) { overRate = true; $rateInp.css({ background: '#fde8e8', borderColor: '#c0392b' }); }
            else $rateInp.css({ background: '', borderColor: '' });

            if (hasQty && hasRate) {
                if (qty > remaining) overLimit = true;
                items.push({ resource_id: $(this).data('resource-id'), qty: qty, rate: rate });
            }
        });

        if (missingField) {
            alert('Please fill in Qty Received and Rate for all items.');
            return;
        }
        if (overRate) {
            alert('Rate entered exceeds the Purchase Order rate for one or more items. Please check the highlighted fields.');
            return;
        }
        if (overLimit) {
            alert('Quantity entered exceeds the remaining balance for one or more items. Please check the highlighted fields.');
            return;
        }
        var $btn = $(this).prop('disabled', true).text('Reporting...');
        $.ajax({
            type: 'POST', url: '../storekeeper/savegrn',
            data: { order_id: orderId, items: JSON.stringify(items), remarks: $('#grn-remarks').val(), date_of_receipt: $('#grn-receipt-date').val(), grn_number: $('#grn-serial-number').text() },
            dataType: 'json',
            success: function(data) {
                $btn.prop('disabled', false).text('Report');
                if (data.error !== 'No') { alert(data.errortext || 'Error saving GRN.'); return; }
                $('#grn-overlay, #grn-popup').hide();
                alert('Goods Received Note submitted successfully.');
            },
            error: function() {
                $btn.prop('disabled', false).text('Report');
                alert('Failed to submit. Please try again.');
            }
        });
    });

    // Measurement Book popup
    var _mbActivities = [];

    $(document).on('click', '.sk-mb-btn', function(){
        var woNum      = $(this).data('wonumber');
        var vendorName = $(this).data('vendor-name') || '—';
        var today      = new Date().toISOString().slice(0, 10);
        _mbActivities  = [];
        $('#mb-wo-number').text(woNum);
        $('#mb-wo-display').text(woNum);
        $('#mb-vendor-name').text(vendorName);
        $('#mb-serial-number').text('Loading...');
        $('#mb-date-input').val('');
        $('#mb-popup').data('wonumber', woNum);
        $('#mb-body').html('<p style="color:#888;font-size:12px;padding:8px 0;">Loading...</p>');
        $('#mb-overlay, #mb-popup').show();
        $('#mb-popup').css('display', 'flex');
        $.ajax({
            type: 'POST', url: '../storekeeper/woactivities',
            data: { wo_number: woNum }, dataType: 'json',
            success: function(data) {
                if (data.error !== 'No') {
                    $('#mb-body').html('<p style="color:#c0392b;font-size:12px;">' + (data.errortext || 'Error loading work order.') + '</p>');
                    return;
                }
                if (data.has_draft) {
                    // Resume unsent draft
                    $('#mb-serial-number').text(data.mb_number || '—');
                    $('#mb-date-input').val(data.mb_date || '');
                    // (draft save removed)
                } else {
                    // Fresh M.Book — fetch next MB number
                    $.ajax({
                        type: 'POST', url: '../storekeeper/mbnext', dataType: 'json',
                        success: function(d) { $('#mb-serial-number').text(d.error === 'No' ? d.mb_number : '—'); },
                        error: function() { $('#mb-serial-number').text('—'); }
                    });
                }
                _mbActivities = data.activities || [];
                var inpStyle = 'type="number" step="any" style="width:62px;height:24px;font-size:12px;display:inline-block;padding:2px 4px;"';
                var html = '';
                $.each(_mbActivities, function(ai, act) {
                    html += '<div style="margin-bottom:18px;border:1px solid #d0d7df;border-radius:4px;overflow:hidden;">'
                        + '<div style="background:#072c47;color:#fff;padding:7px 12px;font-size:12px;font-weight:700;">'
                        + act.activity_name + '</div>'
                        + (function(){
                            var _lr  = parseFloat(act.last_reported_qty) || 0; // ceiling = last reported
                            var _bil = parseFloat(act.cumulative_qty) || 0;
                            var _cur = parseFloat(act.mb_qty) || 0;
                            var _rem = Math.max(0, _lr - _bil - _cur);
                            var fmt  = function(n){ return +n.toFixed(2); };
                            var lbl  = function(t){ return '<span style="font-size:11px;font-weight:600;color:#555;white-space:nowrap;">'+t+'</span>'; };
                            var val  = function(v){ return '<span style="font-size:12px;color:#333;white-space:nowrap;">'+fmt(v)+'</span>'; };
                            var cell = function(label, content){ return '<div style="display:flex;flex-direction:column;align-items:center;flex:1;min-width:0;padding:4px 8px;">'+lbl(label)+'<div style="margin-top:2px;">'+content+'</div></div>'; };
                            return '<div style="padding:6px 0;background:#f4f7fa;display:flex;align-items:stretch;border-bottom:1px solid #d0d7df;width:100%;">'
                                + cell('Unit', '<input type="text" readonly style="width:80px;height:22px;font-size:12px;display:inline-block;padding:1px 6px;text-align:center;background:#eef1f5;cursor:default;" class="form-control input-sm mb-unit" data-ai="' + ai + '" value="' + (act.unit || '') + '" placeholder="—">')
                                + '<div style="width:1px;background:#d0d7df;margin:4px 0;"></div>'
                                + cell('Billed Qty', val(_bil))
                                + '<div style="width:1px;background:#d0d7df;margin:4px 0;"></div>'
                                + cell('Current Qty', '<input type="number" step="any" min="0" max="' + Math.max(0, _lr - _bil).toFixed(3) + '" style="width:100px;height:22px;font-size:12px;font-weight:700;display:inline-block;padding:1px 6px;text-align:center;" class="form-control input-sm mb-qty" data-ai="' + ai + '" data-wo-qty="' + _lr + '" data-cum-qty="' + _bil + '" value="' + (_cur || '') + '" placeholder="—">')
                                + '<div style="width:1px;background:#d0d7df;margin:4px 0;"></div>'
                                + cell('Remaining Qty', '<span class="mb-rem-display" data-ai="' + ai + '" style="font-size:12px;color:#333;white-space:nowrap;">' + fmt(_rem) + '</span>')
                                + '<div style="width:1px;background:#d0d7df;margin:4px 0;"></div>'
                                + cell('Last Reported', val(_lr))
                                + '</div>';
                        })();
                    if (act.tasks && act.tasks.length) {
                        html += '<table class="table table-bordered" style="margin:0;font-size:12px;table-layout:fixed;width:100%;">'
                            + '<thead><tr style="background:#dce3ea;">'
                            + '<th style="width:28px;padding:4px 5px;font-size:10px;white-space:nowrap;">#</th>'
                            + '<th style="padding:4px 6px;font-size:10px;white-space:nowrap;">Task</th>'
                            + '<th style="width:58px;padding:4px 5px;font-size:10px;white-space:nowrap;">Unit</th>'
                            + '<th style="width:80px;padding:4px 5px;font-size:10px;white-space:nowrap;text-align:right;background:#e8f0fb;">Rate</th>'
                            + '<th style="width:110px;padding:4px 5px;font-size:10px;white-space:nowrap;text-align:right;">Work Done</th>'
                            + '<th style="width:82px;padding:4px 5px;font-size:10px;white-space:nowrap;text-align:right;">Amount</th>'
                            + '<th style="width:90px;padding:4px 5px;font-size:10px;white-space:nowrap;text-align:right;">Qty Till Today</th>'
                            + '<th style="width:95px;padding:4px 5px;font-size:10px;white-space:nowrap;text-align:right;">Amt Till Today</th>'
                            + '</tr></thead><tbody>';
                        $.each(act.tasks, function(ti, task) {
                            var taskRate = parseFloat(task.rate) || 0;
                            var cumWd    = parseFloat(task.cum_work_done) || 0;
                            var cumAmt   = parseFloat(task.cum_amount)    || 0;
                            html += '<tr>'
                                + '<td style="padding:4px 6px;">' + (ti + 1) + '</td>'
                                + '<td style="padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="' + task.task_name + '">' + task.task_name + '</td>'
                                + '<td style="padding:4px 6px;">' + (task.task_unit || '') + '</td>'
                                + '<td style="padding:2px 4px;text-align:right;background:#e8f0fb;">'
                                + '<input type="number" step="any" readonly class="form-control input-sm mb-taskrate"'
                                + ' data-ai="' + ai + '" data-ti="' + ti + '"'
                                + ' value="' + (taskRate || '') + '"'
                                + ' style="width:70px;height:24px;font-size:12px;text-align:right;margin-left:auto;background:#e8f0fb;border-color:#b8cce4;color:#000;font-weight:600;cursor:default;">'
                                + '</td>'
                                + '<td style="padding:2px 4px;text-align:right;">'
                                + (function(){
                                    var _tqpu  = parseFloat(task.unit_qty) || 0; // per-unit from wo_task_rates
                                    var _mbCur = parseFloat(act.mb_qty) || 0;
                                    var _wdDef = +(_mbCur * _tqpu).toFixed(2);
                                    return '<input type="number" step="any" class="form-control input-sm mb-workdone"'
                                        + ' data-ai="' + ai + '" data-ti="' + ti + '" data-tqpu="' + _tqpu + '"'
                                        + ' value="' + (_wdDef || '') + '" placeholder=""'
                                        + ' style="width:98px;height:24px;font-size:12px;text-align:right;margin-left:auto;">';
                                })()
                                + '</td>'
                                + '<td style="padding:4px 6px;text-align:right;font-size:12px;color:#000;" class="mb-task-amount" data-ai="' + ai + '" data-ti="' + ti + '">'
                                + (function(){
                                    var _tqpu  = parseFloat(task.unit_qty) || 0; // per-unit from wo_task_rates
                                    var _mbCur = parseFloat(act.mb_qty) || 0;
                                    var _wd    = _mbCur * _tqpu;
                                    return (taskRate > 0 && _wd > 0) ? (taskRate * _wd).toFixed(2) : '—';
                                })()
                                + '</td>'
                                + '<td style="padding:4px 6px;text-align:right;font-size:12px;background:#f0f4e8;">' + (cumWd > 0 ? +cumWd.toFixed(2) : '—') + '</td>'
                                + '<td style="padding:4px 6px;text-align:right;font-size:12px;background:#f0f4e8;">' + (cumAmt > 0 ? +cumAmt.toFixed(2) : '—') + '</td>'
                                + '</tr>';
                        });
                        html += '</tbody></table>';
                    } else {
                        html += '<p style="padding:8px 12px;font-size:12px;color:#888;margin:0;">No tasks.</p>';
                    }
                    html += '</div>';
                });
                $('#mb-body').html(html || '<p style="color:#888;font-size:12px;">No activities found.</p>');
            },
            error: function() {
                $('#mb-body').html('<p style="color:#c0392b;font-size:12px;">Failed to load. Please try again.</p>');
            }
        });
    });

    function closeMbPopup() {
        $('#mb-overlay').hide();
        $('#mb-popup').hide();
        if ($('#mb-popup').data('viewmode')) {
            $('#mb-save-btn').show();
            $('#mb-cancel-btn').text('Cancel');
            $('#mb-date-input').prop('readonly', false).css({background:'', color:''});
            $('#mb-popup').data('viewmode', false);
        }
        _mbActivities = [];
    }

    $(document).on('click', '#mb-close, #mb-cancel-btn, #mb-overlay', closeMbPopup);

    $(document).on('click', '#mb-print-btn', function(){
        var mbNumber = $('#mb-serial-number').text();
        var mbDate   = $('#mb-date-input').val();
        var woNum    = $('#mb-wo-number').text();
        var vendor   = $('#mb-vendor-name').text();
        var bodyHtml = $('#mb-body').html();
        var w = window.open('', '_blank');
        w.document.write(
            '<html><head><title>Measurement Book ' + mbNumber + '</title>'
            + '<style>body{font-family:Arial,sans-serif;font-size:12px;margin:20px;}'
            + 'table{border-collapse:collapse;width:100%;margin-bottom:12px;}'
            + 'th,td{border:1px solid #ccc;padding:5px 8px;}'
            + 'th{background:#dce3ea;}'
            + 'input{border:1px solid #ccc;padding:2px 4px;font-size:12px;}'
            + '.mb-rem-display,.mb-unit,.mb-qty{display:inline;}'
            + '.no-print{display:inline-block;margin-bottom:10px;}'
            + '@media print{.no-print{display:none;}}'
            + '</style></head><body>'
            + '<div class="no-print" style="margin-bottom:12px;">'
            + '<button onclick="window.print()" style="background:#072c47;color:#fff;border:none;border-radius:4px;padding:6px 18px;font-size:12px;cursor:pointer;margin-right:8px;">Print</button>'
            + '<button onclick="window.close()" style="background:#888;color:#fff;border:none;border-radius:4px;padding:6px 18px;font-size:12px;cursor:pointer;">Close</button>'
            + '</div>'
            + '<h3 style="margin:0 0 4px;">Measurement Book</h3>'
            + '<p style="margin:0 0 10px;font-size:12px;">MB No: <b>' + mbNumber + '</b> &nbsp; Date: <b>' + mbDate + '</b> &nbsp; WO: <b>' + woNum + '</b> &nbsp; Vendor: <b>' + vendor + '</b></p>'
            + bodyHtml
            + '</body></html>'
        );
        w.document.close();
        w.focus();
    });

    $(document).on('click', '#mb-save-btn', function(){
        var woNum = $('#mb-popup').data('wonumber');

        // Validate date first — before any dialogs
        if (!$('#mb-date-input').val()) {
            alert('Please enter the Date.');
            $('#mb-date-input').focus();
            return;
        }

        // Check WO qty limits and missing fields — skip activities left completely blank
        var overLimit = false, missingField = false;
        $.each(_mbActivities, function(ai, act) {
            var $qtyEl  = $('.mb-qty[data-ai="'  + ai + '"]');
            var $unitEl = $('.mb-unit[data-ai="' + ai + '"]');
            var qtyVal  = $qtyEl.val().trim();
            var unitVal = $unitEl.val().trim();

            // If both unit and qty are blank, this activity is skipped (optional)
            if (!qtyVal && !unitVal) return;

            // If one is filled but the other isn't, flag missing
            if (!unitVal) { $unitEl.css({ background: '#fde8e8', borderColor: '#c0392b' }); missingField = true; }
            else           { $unitEl.css({ background: '', borderColor: '' }); }
            if (!qtyVal)  { $qtyEl.css({ background: '#fde8e8', borderColor: '#c0392b', color: '#c0392b' }); missingField = true; }
            else           { $qtyEl.css({ background: '', borderColor: '', color: '' }); }

            var woQty   = parseFloat($qtyEl.data('wo-qty'))  || 0;
            var cumQty  = parseFloat($qtyEl.data('cum-qty')) || 0;
            var thisQty = parseFloat(qtyVal) || 0;

            // Check activity qty limit
            if (woQty > 0 && thisQty > (woQty - cumQty) + 0.001) {
                overLimit = true;
                $qtyEl.css({ background: '#fde8e8', borderColor: '#c0392b', color: '#c0392b' });
            }

            // Check task work done — only validate limit when activity qty is filled
            $.each(act.tasks || [], function(ti, task) {
                var $wd  = $('.mb-workdone[data-ai="' + ai + '"][data-ti="' + ti + '"]');
                var wdVal = $wd.val().trim();
                if (!wdVal) { $wd.css({ background: '#fde8e8', borderColor: '#c0392b' }); missingField = true; return; }
                $wd.css({ background: '', borderColor: '' });
                if (!qtyVal || thisQty <= 0) return; // can't check limit without activity qty
                var tqpu  = parseFloat(task.unit_qty || task.task_qty) || 0;
                var maxWd = thisQty * tqpu;
                var wd    = parseFloat(wdVal) || 0;
                if (tqpu > 0 && wd > maxWd + 0.001) {
                    overLimit = true;
                    $wd.css({ background: '#fde8e8', borderColor: '#c0392b' });
                }
            });
        });
        if (overLimit) {
            alert('One or more quantities exceed Work Order limits. Please check the highlighted fields.');
            return;
        }
        if (missingField) {
            alert('Please fill in Unit and Quantity for all activities, and Work Done for all tasks, before sending.');
            return;
        }
        // Ensure at least one activity has been filled in
        var anyFilled = false;
        $.each(_mbActivities, function(ai) {
            if ($('.mb-qty[data-ai="' + ai + '"]').val().trim()) { anyFilled = true; return false; }
        });
        if (!anyFilled) { alert('Please enter quantities for at least one activity.'); return; }

        var entries = [];
        $.each(_mbActivities, function(ai, act) {
            var qtyVal = $('.mb-qty[data-ai="' + ai + '"]').val().trim();
            if (!qtyVal) return; // skip blank activities
            var entry = {
                activity_id:   act.activity_id,
                activity_name: act.activity_name,
                unit:   $('.mb-unit[data-ai="' + ai + '"]').val() || null,
                qty:    qtyVal || null,
                tasks:  []
            };
            $.each(act.tasks || [], function(ti, task) {
                entry.tasks.push({
                    task_id:   task.task_id,
                    task_name: task.task_name,
                    unit:      task.task_unit,
                    rate:      parseFloat($('.mb-taskrate[data-ai="' + ai + '"][data-ti="' + ti + '"]').val()) || null,
                    work_done: $('.mb-workdone[data-ai="' + ai + '"][data-ti="' + ti + '"]').val() || null
                });
            });
            entries.push(entry);
        });
        var $btn = $(this).prop('disabled', true).text('Reporting...');
        $.ajax({
            type: 'POST', url: '../storekeeper/savemb',
            data: { wo_number: woNum, entries: JSON.stringify(entries), mb_number: $('#mb-serial-number').text(), mb_date: $('#mb-date-input').val() }, dataType: 'json',
            success: function(data) {
                $btn.prop('disabled', false).text('Report');
                if (data.error !== 'No') { alert(data.errortext || 'Error sending.'); return; }
                closeMbPopup();
                alert('Measurement Book submitted successfully.');
            },
            error: function() {
                $btn.prop('disabled', false).text('Report');
                alert('Failed to submit. Please try again.');
            }
        });
    });

    // Issued Indents button
    $(document).on('click', '#sk-issued-indent-btn', function(){
        $('#sk-filter-bar').hide();
        loadIssuedIndents();
    });

    $(document).on('click', '#sk-indent-back-btn', function(){
        $('#sk-filter-bar').show();
        loadIndents();
    });

    // ── Indent Task button ─────────────────────────────────────────────────
    $(document).on('click', '.sk-indent-task-btn', function(){
        var $btn = $(this);
        var rid  = $btn.data('id');
        var name = $btn.data('resource-name');
        $('#sk-indent-task-modal-title').text(name);
        $('#sk-indent-task-modal').data('btn', $btn);
        $('#sk-indent-task-modal-body').html('<p style="text-align:center;padding:30px;color:#888;">Loading...</p>');
        $('#sk-indent-task-modal').show();

        $.ajax({
            type: 'POST', url: '../storekeeper/getresourcetasks',
            data: { resource_id: rid }, dataType: 'json',
            success: function(data) {
                if (data.error !== 'No') {
                    $('#sk-indent-task-modal-body').html('<p style="text-align:center;padding:20px;color:#888;">' + (data.errortext || 'Error.') + '</p>');
                    return;
                }
                var tasks = data.tasks || [];
                if (!tasks.length) {
                    $('#sk-indent-task-modal-body').html('<p style="text-align:center;padding:20px;color:#888;">No tasks are mapped to this resource.</p>');
                    return;
                }
                var selId = String($btn.data('task-id') || '');
                var html  = '<table style="width:100%;border-collapse:collapse;font-size:13px;">'
                    + '<thead><tr style="background:#555;color:#fff;">'
                    + '<th style="padding:9px 12px;border:1px solid #444;width:36px;text-align:center;"></th>'
                    + '<th style="padding:9px 12px;border:1px solid #444;">Task</th>'
                    + '<th style="padding:9px 12px;border:1px solid #444;width:80px;text-align:center;">Unit</th>'
                    + '<th style="padding:9px 12px;border:1px solid #444;">Activity</th>'
                    + '</tr></thead><tbody>';
                $.each(tasks, function(i, t) {
                    var bg      = i % 2 === 1 ? 'background:#f7f7f7;' : 'background:#fff;';
                    var checked = (String(t.task_id) === selId) ? ' checked' : '';
                    html += '<tr style="' + bg + '" class="sk-indent-task-row" data-task-id="' + t.task_id + '" data-task-name="' + String(t.task_name || '').replace(/"/g, '&quot;') + '" data-activity-id="' + (t.activity_id || 0) + '" data-activity-name="' + String(t.activity_name || '').replace(/"/g, '&quot;') + '">'
                        + '<td style="padding:7px 12px;border:1px solid #e0e0e0;text-align:center;">'
                        +   '<input type="radio" name="sk-indent-task-radio" value="' + t.task_id + '"' + checked + ' style="width:14px;height:14px;cursor:pointer;accent-color:#072c47;">'
                        + '</td>'
                        + '<td style="padding:7px 12px;border:1px solid #e0e0e0;font-weight:600;">' + (t.task_name || '') + '</td>'
                        + '<td style="padding:7px 12px;border:1px solid #e0e0e0;text-align:center;color:#555;">' + (t.task_unit || '') + '</td>'
                        + '<td style="padding:7px 12px;border:1px solid #e0e0e0;color:#465365;font-size:12px;">' + (t.activity_name || '') + '</td>'
                        + '</tr>';
                });
                html += '</tbody></table>';
                html += '<div style="text-align:right;margin-top:16px;">'
                    + '<button type="button" id="sk-indent-task-cancel-btn" style="background:#888;color:#fff;border:none;border-radius:20px;padding:6px 22px;font-size:13px;font-weight:600;cursor:pointer;margin-right:10px;">Cancel</button>'
                    + '<button type="button" id="sk-indent-task-save-btn" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:6px 26px;font-size:13px;font-weight:600;cursor:pointer;">Select</button>'
                    + '</div>';
                $('#sk-indent-task-modal-body').html(html);
            },
            error: function() { $('#sk-indent-task-modal-body').html('<p style="text-align:center;padding:20px;color:#c0392b;">Failed to load tasks.</p>'); }
        });
    });

    $(document).on('click', '#sk-indent-task-modal-close, #sk-indent-task-cancel-btn', function(){
        $('#sk-indent-task-modal').hide();
    });

    $(document).on('click', '#sk-indent-task-save-btn', function(){
        var $radio = $('input[name="sk-indent-task-radio"]:checked');
        if (!$radio.length) { alert('Please select a task.'); return; }
        var $btn    = $('#sk-indent-task-modal').data('btn');
        var tid     = $radio.val();
        var tname   = $radio.closest('tr').data('task-name');
        var actid   = $radio.closest('tr').data('activity-id') || 0;
        var actname = $radio.closest('tr').data('activity-name') || '';
        $btn.data('task-id', tid).data('task-name', tname).data('activity-id', actid).data('activity-name', actname);
        $btn.closest('tr').find('td:eq(1) .sk-indent-task-sub').remove();
        $btn.closest('tr').find('td:eq(1)').append('<div class="sk-indent-task-sub" style="font-size:10px;color:#072c47;margin-top:2px;">&#9656; ' + tname + '</div>');
        $('#sk-indent-task-modal').hide();
    });

    // ── Raise Indent button — check progress then open popup ──────────────
    $(document).on('click', '#sk-raise-indent-btn', function(){
        if ($('.sk-cb:checked').length === 0) {
            alert('Please select at least one resource to raise an indent.');
            return;
        }
        var noTask = [];
        $('.sk-cb:checked').each(function(){
            var id  = $(this).val();
            var $tb = $('.sk-indent-task-btn[data-id="' + id + '"]');
            if (!$tb.data('task-id')) noTask.push($tb.data('resource-name') || id);
        });
        if (noTask.length) {
            alert('Please select a task for: ' + noTask.join(', '));
            return;
        }
        var checkItems = [];
        $('.sk-cb:checked').each(function(){
            var id  = $(this).val();
            var $tb = $('.sk-indent-task-btn[data-id="' + id + '"]');
            checkItems.push({ id: id, task_id: $tb.data('task-id') || '' });
        });
        var $raiseBtn = $(this).prop('disabled', true);
        $.ajax({
            type: 'POST', url: '../storekeeper/checkindentprogress',
            data: { items: JSON.stringify(checkItems) }, dataType: 'json',
            success: function(data) {
                $raiseBtn.prop('disabled', false);
                if (data.error !== 'No') { alert(data.errortext || 'Error checking progress.'); return; }
                if (data.blocked && data.blocked.length) {
                    alert('Please Report Progress as of Today for the following activit'
                        + (data.blocked.length > 1 ? 'ies' : 'y') + ':\n\n• '
                        + data.blocked.join('\n• '));
                    return;
                }
                // All activities have progress — build and open the popup
                var inp  = 'width:90px;height:26px;padding:2px 5px;text-align:right;font-size:12px;border:1px solid #ccc;border-radius:3px;';
                var html = '<table style="width:100%;border-collapse:collapse;font-size:13px;">'
                    + '<thead><tr style="background:#555;color:#fff;">'
                    + '<th style="padding:9px 12px;border:1px solid #444;">Resource</th>'
                    + '<th style="padding:9px 12px;border:1px solid #444;">Task</th>'
                    + '<th style="padding:9px 12px;border:1px solid #444;">Activity</th>'
                    + '<th style="padding:9px 12px;border:1px solid #444;width:110px;text-align:right;">Stock at Site</th>'
                    + '<th style="padding:9px 12px;border:1px solid #444;width:110px;text-align:right;">Re-order Qty</th>'
                    + '</tr></thead><tbody>';
                var idx = 0;
                $('.sk-cb:checked').each(function(){
                    var id  = $(this).val();
                    var $tb = $('.sk-indent-task-btn[data-id="' + id + '"]');
                    var bg  = idx++ % 2 === 1 ? 'background:#f7f7f7;' : 'background:#fff;';
                    var rowData    = _skAllRows.filter(function(x){ return String(x.resource_id) === String(id); })[0] || {};
                    var preReorder = parseFloat(rowData.reorder_quantity  || 0);
                    html += '<tr style="' + bg + '" data-resource-id="' + id + '">'
                        + '<td style="padding:7px 12px;border:1px solid #e0e0e0;font-weight:600;">' + ($tb.data('resource-name') || id) + '</td>'
                        + '<td style="padding:7px 12px;border:1px solid #e0e0e0;">' + ($tb.data('task-name') || '') + '</td>'
                        + '<td style="padding:7px 12px;border:1px solid #e0e0e0;color:#465365;font-size:12px;">' + ($tb.data('activity-name') || '') + '</td>'
                        + '<td style="padding:5px 8px;border:1px solid #e0e0e0;text-align:right;">'
                        +   '<input type="number" class="sk-popup-stock" data-id="' + id + '" min="0" step="any" placeholder="Enter physical stock" style="' + inp + '">'
                        + '</td>'
                        + '<td style="padding:5px 8px;border:1px solid #e0e0e0;text-align:right;">'
                        +   '<input type="number" class="sk-popup-reorder" data-id="' + id + '" min="0" step="any" value="' + preReorder + '" style="' + inp + '">'
                        + '</td>'
                        + '</tr>';
                });
                html += '</tbody></table>';
                $('#sk-raise-popup-body').html(html);
                $('#sk-raise-popup').show();
            },
            error: function() { $raiseBtn.prop('disabled', false); alert('Failed to check progress. Please try again.'); }
        });
    });

    $(document).on('click', '#sk-raise-popup-close, #sk-raise-popup-cancel', function(){
        $('#sk-raise-popup').hide();
    });

    $(document).on('click', '#sk-raise-popup-submit', function(){
        var selected = [];
        $('#sk-raise-popup-body tr[data-resource-id]').each(function(){
            var id  = $(this).data('resource-id').toString();
            var $tb = $('.sk-indent-task-btn[data-id="' + id + '"]');
            selected.push({
                id:          id,
                task_id:     $tb.data('task-id')    || '',
                task_name:   $tb.data('task-name')  || '',
                activity_id: $tb.data('activity-id') || 0,
                stock:       parseFloat($('.sk-popup-stock[data-id="'   + id + '"]').val()) || 0,
                reorder:     parseFloat($('.sk-popup-reorder[data-id="' + id + '"]').val()) || 0
            });
        });
        var $btn = $(this).prop('disabled', true).text('Saving...');
        $.ajax({
            type: 'POST',
            url: '../storekeeper/raiseindent',
            data: { items: JSON.stringify(selected) },
            dataType: 'json',
            success: function(data) {
                $btn.prop('disabled', false).text('Submit');
                if (data.error !== 'No') {
                    alert(data.errortext || 'Error raising indent.');
                    return;
                }
                $('#sk-raise-popup').hide();
                alert('Indent raised successfully.');
                loadIndents();
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Submit');
                alert('Failed. Server: ' + xhr.responseText.substring(0, 200));
            }
        });
    });

    // Individual checkbox — highlight selected row
    $(document).on('change', '.sk-cb', function(){
        $(this).closest('tr').toggleClass('sk-row-selected', $(this).is(':checked'));
    });

    var _skTabTitles = { indents: 'Indents', issued: 'Goods Received Notes', mbwo: 'Measurement Book', reportprogress: 'Report Progress' };
    var _skTabWins = {};
    var _skTabLoaded = {};
    var _skTabIds = ['indents', 'issued', 'mbwo', 'reportprogress'];
    var _skWinZ = 999999;
    var _skSeqState = { n: 0 };

    function _skNextZ(){
        var base = (typeof window.popupSubZBase === 'function') ? window.popupSubZBase() : 999999;
        if (base >= _skWinZ) _skWinZ = base;
        return ++_skWinZ;
    }
    function _skOpenSeq(){
        var anyOpen = _skTabIds.some(function(t){
            var w = _skTabWins[t];
            return w && w.classList.contains('pw2-open');
        });
        if(!anyOpen){ _skSeqState.n = 0; return 0; }
        _skSeqState.n = (_skSeqState.n % 6) + 1;
        return _skSeqState.n;
    }

    function _skBuildWin(tab){
        var content = document.getElementById('sk-tab-' + tab);
        if(!content) return null;
        content.classList.remove('sk-tab-content');
        content.classList.add('active');
        content.style.display = 'block';

        var win = document.createElement('div');
        win.className = 'sk-tab-win';
        win.id = 'sk-win-' + tab;

        var hdr = document.createElement('div');
        hdr.className = 'sk-tab-win-hdr';
        hdr.innerHTML =
            '<span class="sk-tab-win-title">' + _skTabTitles[tab] + '</span>' +
            '<div class="sk-tab-win-hdr-btns">' +
                '<button type="button" class="sk-tw-expand" title="Fullscreen">&#x26F6;</button>' +
                '<button type="button" class="sk-tw-close" title="Close">&times;</button>' +
            '</div>';

        var body = document.createElement('div');
        body.className = 'sk-tab-win-body';
        body.appendChild(content);

        ['n','s','e','w','ne','nw','se','sw'].forEach(function(dir){
            var rs = document.createElement('div');
            rs.className = 'sk-tw-rs sk-tw-rs-' + dir;
            rs.setAttribute('data-dir', dir);
            win.appendChild(rs);
        });
        win.appendChild(hdr);
        win.appendChild(body);
        document.body.appendChild(win);

        var MIN_W = 480, MIN_H = 320, _action = null, _sx=0,_sy=0,_ox=0,_oy=0,_ow=0,_oh=0;
        function anchor(){
            var r = win.getBoundingClientRect();
            win.style.left = r.left + 'px'; win.style.top = r.top + 'px';
            win.style.width = r.width + 'px'; win.style.height = r.height + 'px';
            return r;
        }
        var bdt = (typeof window.bindDragTouch === 'function') ? window.bindDragTouch : function(el, ev, fn, opts){ el.addEventListener(ev, fn, opts); };
        bdt(hdr, 'mousedown', function(e){
            if(e.target.closest('.sk-tab-win-hdr-btns')) return;
            var r = anchor(); _action = 'drag';
            _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top;
            e.preventDefault();
        });
        win.querySelectorAll('.sk-tw-rs').forEach(function(el){
            bdt(el, 'mousedown', function(e){
                var r = anchor(); _action = el.getAttribute('data-dir');
                _sx=e.clientX; _sy=e.clientY; _ox=r.left; _oy=r.top; _ow=r.width; _oh=r.height;
                e.preventDefault(); e.stopPropagation();
            });
        });
        bdt(document, 'mousemove', function(e){
            if(_action == null || !_skTabWins[tab]) return;
            if(getComputedStyle(win).display === 'none') return;
            e.preventDefault();
            var dx=e.clientX-_sx, dy=e.clientY-_sy;
            if(_action === 'drag'){
                win.style.left = Math.max(0, _ox+dx) + 'px';
                win.style.top  = Math.max(0, _oy+dy) + 'px';
            } else {
                var l=_ox,t=_oy,w=_ow,h=_oh;
                if(_action.indexOf('e')>-1){ w=Math.max(MIN_W,_ow+dx); }
                if(_action.indexOf('s')>-1){ h=Math.max(MIN_H,_oh+dy); }
                if(_action.indexOf('w')>-1){ var nw=Math.max(MIN_W,_ow-dx); l=_ox+(_ow-nw); w=nw; }
                if(_action.indexOf('n')>-1){ var nh=Math.max(MIN_H,_oh-dy); t=_oy+(_oh-nh); h=nh; }
                win.style.left=l+'px'; win.style.top=t+'px';
                win.style.width=w+'px'; win.style.height=h+'px';
            }
        }, { passive: false });
        bdt(document, 'mouseup', function(){ _action = null; });

        function raise(){ win.style.setProperty('z-index', _skNextZ(), 'important'); }
        bdt(win, 'mousedown', raise, true);

        hdr.querySelector('.sk-tw-close').addEventListener('click', function(){
            win.classList.remove('pw2-open');
            var btn = document.querySelector('.sk-tab-btn[data-tab="' + tab + '"]');
            if(btn) btn.classList.remove('active');
        });
        var _saved = null;
        hdr.querySelector('.sk-tw-expand').addEventListener('click', function(){
            if(_saved){
                win.style.left=_saved.left; win.style.top=_saved.top;
                win.style.width=_saved.width; win.style.height=_saved.height;
                _saved = null; this.innerHTML = '&#x26F6;'; this.title = 'Fullscreen';
            } else {
                anchor();
                _saved = { left: win.style.left, top: win.style.top, width: win.style.width, height: win.style.height };
                win.style.left = '0'; win.style.top = '0';
                win.style.width = '100vw'; win.style.height = '100vh';
                this.innerHTML = '&#x2716;'; this.title = 'Restore';
            }
        });

        win.__raise = raise;
        win.__anchor = anchor;
        return win;
    }

    function _skOpenTabWin(tab){
        var win = _skTabWins[tab] || (_skTabWins[tab] = _skBuildWin(tab));
        if(!win) return;

        if(!win.classList.contains('pw2-open')){
            var parentWin = document.getElementById('storeoffice-win');
            var base = parentWin ? parentWin.getBoundingClientRect() : { top: 90, left: (window.innerWidth - 980) / 2 };
            var step = _skOpenSeq() * 30;
            var top  = Math.min(window.innerHeight - 160, base.top + 40 + step);
            var left = Math.min(window.innerWidth  - 500, Math.max(10, base.left + 30 + step));
            win.style.left = left + 'px';
            win.style.top  = top + 'px';
        }
        win.classList.add('pw2-open');
        win.__raise();
        var btn = document.querySelector('.sk-tab-btn[data-tab="' + tab + '"]');
        if(btn) btn.classList.add('active');
        document.getElementById('sk-page-header').textContent = _skTabTitles[tab] || '';

        if(!_skTabLoaded[tab]){
            _skTabLoaded[tab] = true;
            if(tab === 'indents') { $('#sk-filter-bar').show(); loadIndents(); }
            else if(tab === 'issued') loadIssued();
            else if(tab === 'mbwo') { $('#sk-mbwo-filter-vendor').closest('div').show(); loadMbWo(); }
            else if(tab === 'reportprogress') loadReportProgress();
        }
    }

    document.querySelectorAll('.sk-tab-btn').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            var tab = btn.getAttribute('data-tab');
            if (!tab) return;
            _skOpenTabWin(tab);
        });
    });
})();
</script>

<!-- PO Preview Panel -->
<style>
#sk-po-preview-panel {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9990;
    background: #f0f4f8;
    overflow-y: auto;
    flex-direction: column;
}
#sk-po-preview-panel.active { display: flex; }
</style>
<div id="sk-po-preview-panel">
    <div style="background:#072c47;color:#fff;padding:10px 20px;font-size:15px;font-weight:600;letter-spacing:0.5px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <span>PURCHASE ORDER &mdash; <span id="sk-po-preview-number" style="font-weight:400;font-size:13px;"></span></span>
        <span id="sk-po-preview-close" style="cursor:pointer;font-size:22px;line-height:1;opacity:0.8;">&times;</span>
    </div>
    <div style="padding:16px 20px;text-align:right;flex-shrink:0;">
        <button type="button" id="sk-po-print-btn" style="background:#465365;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;"><span class="icon-printer"></span> Print</button>
    </div>
    <div style="padding:0 20px 20px;flex:1;">
        <div id="sk-po-preview-content"></div>
    </div>
</div>

<!-- WO Preview Panel -->
<style>
#sk-wo-preview-panel {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9990;
    background: #f0f4f8;
    overflow-y: auto;
    flex-direction: column;
}
#sk-wo-preview-panel.active { display: flex; }
</style>
<div id="sk-wo-preview-panel">
    <div style="background:#072c47;color:#fff;padding:10px 20px;font-size:15px;font-weight:600;letter-spacing:0.5px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <span>WORK ORDER &mdash; <span id="sk-wo-preview-number" style="font-weight:400;font-size:13px;"></span></span>
        <span id="sk-wo-preview-close" style="cursor:pointer;font-size:22px;line-height:1;opacity:0.8;">&times;</span>
    </div>
    <div style="padding:16px 20px;text-align:right;flex-shrink:0;">
        <button type="button" id="sk-wo-print-btn" style="background:#465365;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;"><span class="icon-printer"></span> Print</button>
    </div>
    <div style="padding:0 20px 20px;flex:1;">
        <div id="sk-wo-preview-content"></div>
    </div>
</div>

<!-- Measurement Book Popup -->
<div id="mb-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:9998;"></div>
<div id="mb-popup" style="display:none;position:fixed;z-index:9999;width:780px;max-width:96vw;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border-radius:7px;box-shadow:0 6px 28px rgba(0,0,0,0.22);overflow:hidden;flex-direction:column;max-height:88vh;">
    <div style="background:#072c47;color:#fff;padding:10px 16px;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <span>Measurement Book &mdash; <span id="mb-wo-number" style="font-weight:400;font-size:12px;"></span></span>
        <span id="mb-close" style="cursor:pointer;font-size:18px;line-height:1;">&times;</span>
    </div>
    <div id="mb-meta" style="padding:12px 20px 10px;border-bottom:1px solid #e0e4ea;flex-shrink:0;">
        <div style="display:flex;gap:20px;">
            <div style="flex:1;display:flex;flex-direction:column;gap:8px;">
                <div>
                    <label style="font-size:11px;font-weight:600;color:#000;display:block;margin-bottom:2px;">MB No.</label>
                    <div id="mb-serial-number" style="font-size:13px;color:#000;padding:3px 0;border-bottom:1px solid #e0e0e0;">—</div>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;color:#000;display:block;margin-bottom:2px;">Sub Contractor</label>
                    <div id="mb-vendor-name" style="font-size:13px;color:#000;padding:3px 0;border-bottom:1px solid #e0e0e0;"></div>
                </div>
            </div>
            <div style="width:200px;display:flex;flex-direction:column;gap:8px;">
                <div>
                    <label style="font-size:11px;font-weight:600;color:#000;display:block;margin-bottom:2px;">Work Order No.</label>
                    <div id="mb-wo-display" style="font-size:13px;color:#000;padding:3px 0;border-bottom:1px solid #e0e0e0;"></div>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;color:#000;display:block;margin-bottom:2px;">Date</label>
                    <input type="date" id="mb-date-input" style="width:100%;border:1px solid #c5ccd4;border-radius:4px;padding:4px 8px;font-size:12px;box-sizing:border-box;">
                </div>
            </div>
        </div>
    </div>
    <div id="mb-body" style="padding:16px 20px;overflow-y:auto;flex:1;"></div>
    <div style="padding:10px 20px 14px;text-align:right;border-top:1px solid #eee;flex-shrink:0;">
        <button type="button" id="mb-cancel-btn" style="background:#c0392b;border:none;border-radius:20px;padding:5px 18px;font-size:12px;color:#fff;cursor:pointer;margin-right:8px;">Cancel</button>
        <button type="button" id="mb-print-btn" style="background:#6c757d;color:#fff;border:none;border-radius:20px;padding:5px 18px;font-size:12px;cursor:pointer;margin-right:8px;">Print</button>
        <button type="button" id="mb-save-btn" style="background:#001a6e;color:#fff;border:none;border-radius:20px;padding:5px 22px;font-size:12px;cursor:pointer;">Report</button>
    </div>
</div>

<!-- GRN Popup -->
<div id="grn-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:9998;"></div>
<div id="grn-popup" style="display:none;position:fixed;z-index:9999;width:980px;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border-radius:7px;box-shadow:0 6px 28px rgba(0,0,0,0.22);overflow:hidden;">
    <div style="background:#072c47;color:#fff;padding:10px 16px;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:space-between;">
        <span>Goods Received Note &mdash; <span id="grn-po-number" style="font-weight:400;font-size:12px;"></span></span>
        <span id="grn-close" style="cursor:pointer;font-size:18px;line-height:1;">&times;</span>
    </div>
    <div style="padding:16px 20px;max-height:80vh;overflow-y:auto;">
        <input type="hidden" id="grn-order-id">
        <div style="display:flex;gap:20px;margin-bottom:14px;">
            <div style="flex:1;display:flex;flex-direction:column;gap:10px;">
                <div>
                    <label style="font-size:11px;font-weight:600;color:#465365;display:block;margin-bottom:3px;">GRN No.</label>
                    <div id="grn-serial-number" style="font-size:13px;color:#072c47;padding:4px 0;border-bottom:1px solid #e0e0e0;">—</div>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;color:#465365;display:block;margin-bottom:3px;">Vendor</label>
                    <div id="grn-vendor-name" style="font-size:13px;color:#222;padding:4px 0;border-bottom:1px solid #e0e0e0;"></div>
                </div>
            </div>
            <div style="width:190px;display:flex;flex-direction:column;gap:10px;">
                <div>
                    <label style="font-size:11px;font-weight:600;color:#465365;display:block;margin-bottom:3px;">Purchase Order No.</label>
                    <div id="grn-po-display" style="font-size:13px;color:#222;padding:4px 0;border-bottom:1px solid #e0e0e0;"></div>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;color:#465365;display:block;margin-bottom:3px;">Date of Receipt</label>
                    <input type="date" id="grn-receipt-date" style="width:100%;border:1px solid #c5ccd4;border-radius:4px;padding:5px 8px;font-size:12px;box-sizing:border-box;">
                </div>
            </div>
        </div>
        <div id="grn-items-body"></div>
        <div style="margin-top:14px;">
            <label style="font-size:11px;font-weight:600;color:#465365;display:block;margin-bottom:4px;">Remarks</label>
            <textarea id="grn-remarks" rows="3" placeholder="Enter remarks..." style="width:100%;border:1px solid #c5ccd4;border-radius:4px;padding:6px 8px;font-size:12px;resize:vertical;box-sizing:border-box;"></textarea>
        </div>
    </div>
    <div style="padding:10px 20px 16px;text-align:right;border-top:1px solid #eee;">
        <button type="button" id="grn-cancel" style="background:#c0392b;border:1px solid #a93226;border-radius:20px;padding:5px 18px;font-size:12px;color:#fff;cursor:pointer;margin-right:8px;">Cancel</button>
        <button type="button" id="grn-print-btn" style="background:#6c757d;color:#fff;border:none;border-radius:20px;padding:5px 18px;font-size:12px;cursor:pointer;margin-right:8px;">Print</button>
        <button type="button" id="grn-send-btn" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:5px 22px;font-size:12px;cursor:pointer;">Report</button>
    </div>
</div>
