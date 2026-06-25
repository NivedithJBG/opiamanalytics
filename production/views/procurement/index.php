<style>
.procu-page-wrap {
    background: #fff;
    border-radius: 6px;
    margin: 30px 20px 20px;
    padding: 0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.10);
    min-height: 400px;
    overflow: hidden;
}
.procu-piano-tab {
    background: #072c47;
    padding: 6px 28px;
    color: #fff;
    font-size: 14px;
    font-weight: 400;
    letter-spacing: 0.5px;
}
.procu-tab-row {
    padding: 28px 24px 22px;
    text-align: center;
    border-bottom: 1px solid #e8e8e8;
    background: #f8fafc;
}
.procu-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border: 1.5px solid #c5ccd4;
    border-radius: 50px;
    padding: 6px 28px 6px 12px;
    margin: 0 6px;
    background: #fff;
    color: #465365;
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.4px;
    min-width: 180px;
    cursor: pointer;
    transition: background 0.2s, color 0.2s, border-color 0.2s;
    text-decoration: none;
}
.procu-tab-btn:hover {
    background: #f0f2f5;
    text-decoration: none;
    color: #465365;
}
.procu-tab-btn.active {
    background: #072c47;
    border-color: #072c47;
    color: #fff;
}
.procu-tab-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(7,44,71,0.10);
    font-size: 12px;
    flex-shrink: 0;
}
.procu-tab-btn.active .procu-tab-icon {
    background: rgba(255,255,255,0.18);
    color: #fff;
}
.procu-tab-content {
    display: none;
    padding: 0;
}
.procu-tab-content.active {
    display: block;
}
.procu-empty {
    text-align: center;
    color: #aaa;
    font-size: 13px;
    padding: 40px 0;
}
.procu-even { background: #f9f9f9; }
.procu-table td.num { text-align: right; }
.procu-table thead tr { background: #072c47; color: #fff; }
.procu-table thead th { border-color: #05233a; white-space: nowrap; }
.procu-table td, .procu-table th { border-color: #b8b8b8 !important; }
.procu-loader {
    text-align: center;
    padding: 30px 0;
    color: #888;
    font-size: 13px;
}
.po-param-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9998;
}
.po-param-popup {
    position: fixed;
    z-index: 9999;
    width: 260px;
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.22);
    border: 1px solid #c5ccd4;
    overflow: hidden;
}
.po-param-popup-header {
    background: #072c47;
    color: #fff;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.4px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.po-param-popup-header .po-param-close {
    cursor: pointer;
    font-size: 14px;
    line-height: 1;
    opacity: 0.8;
}
.po-param-popup-header .po-param-close:hover { opacity: 1; }
.po-param-popup-body {
    padding: 14px 16px;
}
.po-param-row {
    margin-bottom: 10px;
}
.po-param-row label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #465365;
    margin-bottom: 3px;
}
.po-param-row input {
    width: 100%;
    height: 28px;
    border: 1px solid #c5ccd4;
    border-radius: 4px;
    padding: 2px 8px;
    font-size: 12px;
    color: #333;
}
.po-param-row input:focus {
    outline: none;
    border-color: #072c47;
}
.po-param-popup-footer {
    padding: 8px 16px 12px;
    text-align: right;
    border-top: 1px solid #eee;
}
.po-stock-input::-webkit-inner-spin-button,
.po-stock-input::-webkit-outer-spin-button,
.po-reorder-input::-webkit-inner-spin-button,
.po-reorder-input::-webkit-outer-spin-button,
.po-rate-input::-webkit-inner-spin-button,
.po-rate-input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.po-stock-input,
.po-reorder-input,
.po-rate-input {
    -moz-appearance: textfield;
    text-align: right !important;
}
@media print {
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
    body * { visibility: hidden; }
    #po-preview-content,
    #po-preview-content * { visibility: visible; }
    #po-preview-content {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
    }
}
.po-preview-cntnr {
    position: fixed;
    background: #fff;
    top: 86px;
    bottom: 0;
    overflow: auto;
    left: 0;
    right: 0;
    z-index: -1;
    opacity: 0;
    transition: all .3s linear;
    -o-transition: all .3s linear;
    -moz-transition: all .3s linear;
    -webkit-transition: all .3s linear;
    transform: translateY(100px);
}
.po-preview-cntnr.active {
    z-index: 1000;
    opacity: 1;
    transform: translateY(0px);
}
</style>

<!-- Raise Purchase Orders (bulk) popup -->
<div id="po-bulk-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:9998;"></div>
<div id="po-bulk-popup" style="display:none;position:fixed;z-index:9999;width:460px;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border-radius:7px;box-shadow:0 6px 28px rgba(0,0,0,0.22);overflow:hidden;">
    <div style="background:#072c47;color:#fff;padding:10px 16px;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:space-between;">
        <span>Raise Purchase Orders</span>
        <span id="po-bulk-close" style="cursor:pointer;font-size:16px;opacity:0.8;">&times;</span>
    </div>
    <div style="padding:18px 20px;">

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Vendor</label>
            <select id="po-bulk-vendor" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
                <option value="">— Select Vendor —</option>
            </select>
        </div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Ship To</label>
            <textarea id="po-bulk-shipto" rows="3" placeholder="Enter delivery address..." style="width:100%;border:1px solid #c5ccd4;border-radius:4px;padding:6px 8px;font-size:12px;color:#333;resize:vertical;"></textarea>
        </div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Credit Period</label>
            <input type="number" id="po-bulk-credit-period" min="0" step="1" placeholder="e.g. 30 days" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
        </div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Lead Time</label>
            <input type="text" id="po-bulk-lead-time" placeholder="e.g. 7 days" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
        </div>

        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Terms</label>
            <div id="po-bulk-terms-list">
                <div class="po-bulk-term-row" style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                    <input type="text" class="po-bulk-term-input" placeholder="Enter term..." style="flex:1;height:30px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
                    <button type="button" class="po-bulk-term-remove" style="background:#c0392b;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:14px;line-height:1;cursor:pointer;display:none;">&times;</button>
                </div>
            </div>
            <button type="button" id="po-bulk-term-add-btn" style="margin-top:4px;background:none;border:1px dashed #c5ccd4;border-radius:4px;padding:3px 12px;font-size:11px;color:#465365;cursor:pointer;">+ Add Term</button>
        </div>

    </div>
    <div style="padding:10px 20px 16px;text-align:right;border-top:1px solid #eee;">
        <button type="button" id="po-bulk-cancel" style="background:#f0f4f8;border:1px solid #c5ccd4;border-radius:20px;padding:5px 18px;font-size:12px;color:#465365;cursor:pointer;margin-right:8px;">Cancel</button>
        <button type="button" id="po-bulk-save-btn" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:5px 22px;font-size:12px;cursor:pointer;">Raise PO</button>
    </div>
</div>

<!-- Raise PO popup -->
<div class="po-modal-overlay" id="po-raisepo-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:9998;"></div>
<div id="po-raisepo-popup" style="display:none;position:fixed;z-index:9999;width:440px;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border-radius:7px;box-shadow:0 6px 28px rgba(0,0,0,0.22);overflow:hidden;">
    <div style="background:#072c47;color:#fff;padding:10px 16px;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:space-between;">
        <span>Raise Purchase Order &mdash; <span id="po-raisepo-resname" style="font-weight:400;font-size:12px;"></span></span>
        <span id="po-raisepo-close" style="cursor:pointer;font-size:16px;opacity:0.8;">&times;</span>
    </div>
    <div style="padding:18px 20px;">
        <input type="hidden" id="po-raisepo-resid">

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Vendor</label>
            <select id="po-raisepo-vendor" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
                <option value="">— Select Vendor —</option>
            </select>
        </div>

        <div style="display:flex;gap:12px;margin-bottom:14px;">
            <div style="flex:1;">
                <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Unit</label>
                <input type="text" id="po-raisepo-unit" placeholder="e.g. kg, m, nos" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
            </div>
            <div style="flex:1;">
                <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Rate</label>
                <input type="number" id="po-raisepo-rate" placeholder="0.00" min="0" step="0.01" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Ship To</label>
            <textarea id="po-raisepo-shipto" rows="3" placeholder="Enter delivery address..." style="width:100%;border:1px solid #c5ccd4;border-radius:4px;padding:6px 8px;font-size:12px;color:#333;resize:vertical;"></textarea>
        </div>

        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Terms</label>
            <div id="po-raisepo-terms-list">
                <div class="po-term-row" style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                    <input type="text" class="po-term-input" placeholder="Enter term..." style="flex:1;height:30px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
                    <button type="button" class="po-term-remove" style="background:#c0392b;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:14px;line-height:1;cursor:pointer;display:none;">&times;</button>
                </div>
            </div>
            <button type="button" id="po-term-add-btn" style="background:#f0f4f8;border:1px solid #c5ccd4;border-radius:20px;padding:3px 14px;font-size:11px;color:#465365;cursor:pointer;margin-top:2px;">&#43; Add Term</button>
        </div>
    </div>
    <div style="padding:10px 20px 16px;text-align:right;border-top:1px solid #eee;">
        <button type="button" id="po-raisepo-cancel" style="background:#f0f4f8;border:1px solid #c5ccd4;border-radius:20px;padding:5px 18px;font-size:12px;color:#465365;cursor:pointer;margin-right:8px;">Cancel</button>
        <button type="button" id="po-raisepo-save-btn" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:5px 22px;font-size:12px;cursor:pointer;">Raise PO</button>
    </div>
</div>

<!-- Parameters popup -->
<div class="po-param-overlay" id="po-param-overlay"></div>
<div class="po-param-popup" id="po-param-popup" style="display:none;">
    <div class="po-param-popup-header">
        <span id="po-param-resource-name">Parameters</span>
        <span class="po-param-close" id="po-param-close">&times;</span>
    </div>
    <div class="po-param-popup-body">
        <input type="hidden" id="po-param-resource-id">
        <div class="po-param-row">
            <label>Re-Order Level</label>
            <div style="display:flex;align-items:center;gap:6px;">
                <input type="number" id="po-param-reorder-level" min="0" step="any" placeholder="Enter value" style="flex:1;min-width:0;">
                <span class="po-param-unit-lbl" style="font-size:11px;color:#888;white-space:nowrap;min-width:28px;"></span>
            </div>
        </div>
        <div class="po-param-row" style="margin-bottom:0;">
            <label>Lot Size</label>
            <div style="display:flex;align-items:center;gap:6px;">
                <input type="number" id="po-param-lot-size" min="0" step="any" placeholder="Enter value" style="flex:1;min-width:0;">
                <span class="po-param-unit-lbl" style="font-size:11px;color:#888;white-space:nowrap;min-width:28px;"></span>
            </div>
        </div>
    </div>
    <div class="po-param-popup-footer">
        <button type="button" id="po-param-save-btn" class="btn btn-sm" style="background:#072c47;color:#fff;border-radius:20px;padding:4px 18px;font-size:11px;">Save</button>
    </div>
</div>

<!-- Mail Sent Toast -->
<div id="po-mail-toast" style="display:none;position:fixed;top:80px;left:50%;transform:translateX(-50%);background:#2d7a4f;color:#fff;padding:10px 32px;border-radius:50px;font-size:13px;font-weight:600;letter-spacing:0.5px;box-shadow:0 4px 18px rgba(0,0,0,0.22);z-index:99999;white-space:nowrap;">&#10003; Mail Sent</div>

<!-- PO Preview Overlay -->
<input type="hidden" id="po-preview-order-id">
<input type="hidden" id="po-preview-vendor-email">
<input type="hidden" id="po-preview-ordernumber">

<!-- Email Compose Popup -->
<div id="po-compose-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:10000;"></div>
<div id="po-compose-popup" style="display:none;position:fixed;z-index:10001;width:500px;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border-radius:7px;box-shadow:0 6px 28px rgba(0,0,0,0.22);overflow:hidden;">
    <div style="background:#909090;color:#fff;padding:10px 16px;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:space-between;">
        <span>Email Purchase Order</span>
        <span id="po-compose-close" style="cursor:pointer;font-size:16px;opacity:0.8;">&times;</span>
    </div>
    <div style="padding:18px 20px;">
        <div style="margin-bottom:13px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">To</label>
            <input type="email" id="po-compose-to" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
        </div>
        <div style="margin-bottom:13px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Subject</label>
            <input type="text" id="po-compose-subject" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
        </div>
        <div style="margin-bottom:0;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Body</label>
            <textarea id="po-compose-body" rows="6" placeholder="Write your message here..." style="width:100%;border:1px solid #c5ccd4;border-radius:4px;padding:6px 8px;font-size:12px;color:#333;resize:vertical;"></textarea>
        </div>
    </div>
    <div style="padding:10px 20px 16px;text-align:right;border-top:1px solid #eee;">
        <button type="button" id="po-compose-cancel" style="background:#f0f4f8;border:1px solid #c5ccd4;border-radius:20px;padding:5px 18px;font-size:12px;color:#465365;cursor:pointer;margin-right:8px;">Cancel</button>
        <button type="button" id="po-compose-send-btn" style="background:#909090;color:#fff;border:none;border-radius:20px;padding:5px 22px;font-size:12px;font-weight:600;cursor:pointer;">Send with PDF Attachment</button>
    </div>
</div>
<!-- Cancellation Mail Popup -->
<div id="po-cancel-mail-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:10000;"></div>
<div id="po-cancel-mail-popup" style="display:none;position:fixed;z-index:10001;width:500px;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border-radius:7px;box-shadow:0 6px 28px rgba(0,0,0,0.22);overflow:hidden;">
    <div style="background:#c0392b;color:#fff;padding:10px 16px;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:space-between;">
        <span>Send Cancellation Notice</span>
        <span id="po-cancel-mail-close" style="cursor:pointer;font-size:16px;opacity:0.8;">&times;</span>
    </div>
    <div style="padding:18px 20px;">
        <input type="hidden" id="po-cancel-mail-orderid">
        <div style="margin-bottom:13px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">To</label>
            <input type="email" id="po-cancel-mail-to" readonly style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#555;background:#f8f8f8;">
        </div>
        <div style="margin-bottom:0;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Message</label>
            <textarea id="po-cancel-mail-body" rows="6" style="width:100%;border:1px solid #c5ccd4;border-radius:4px;padding:6px 8px;font-size:12px;color:#333;resize:vertical;"></textarea>
        </div>
    </div>
    <div style="padding:10px 20px 16px;text-align:right;border-top:1px solid #eee;">
        <button type="button" id="po-cancel-mail-cancel" style="background:#f0f4f8;border:1px solid #c5ccd4;border-radius:20px;padding:5px 18px;font-size:12px;color:#465365;cursor:pointer;margin-right:8px;">Close</button>
        <button type="button" id="po-cancel-mail-send" style="background:#c0392b;color:#fff;border:none;border-radius:20px;padding:5px 22px;font-size:12px;font-weight:600;cursor:pointer;">Send</button>
    </div>
</div>

<div id="po-preview-overlay" class="po-preview-cntnr">
    <div style="background:#072c47;color:#fff;padding:10px 20px;font-size:15px;font-weight:600;letter-spacing:0.5px;display:flex;align-items:center;justify-content:space-between;">
        <span>PURCHASE ORDER</span>
        <span id="po-preview-close" style="cursor:pointer;font-size:20px;line-height:1;opacity:0.8;">&times;</span>
    </div>
    <div style="padding:20px;">
        <div style="text-align:right;margin-bottom:16px;">
            <button type="button" id="po-email-btn" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;margin-right:8px;"><span class="icon-envelop"></span> Email to Vendor</button>
            <button type="button" id="po-print-btn" style="background:#465365;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;margin-right:8px;"><span class="icon-printer"></span> Print</button>
            <button type="button" id="po-close-btn" style="background:#888;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;">Close</button>
        </div>
        <div id="po-preview-content"></div>
    </div>
</div>

<div class="procu-page-wrap">

    <div class="procu-piano-tab">Order Management</div>

    <div class="procu-tab-row">
        <a href="javascript:;" class="procu-tab-btn active" data-tab="PO">
            <span class="procu-tab-icon"><span class="icon-cart"></span></span>
            Purchase Orders
        </a>
        <a href="javascript:;" class="procu-tab-btn" data-tab="WO">
            <span class="procu-tab-icon"><span class="icon-briefcase"></span></span>
            Work Orders
        </a>
        <a href="javascript:;" class="procu-tab-btn" data-tab="LO">
            <span class="procu-tab-icon"><span class="icon-key"></span></span>
            Lease Orders
        </a>
    </div>

    <div id="procu-tab-PO" class="procu-tab-content active">
        <div id="po-filter-bar" style="padding:14px 20px 0;display:flex;gap:16px;align-items:flex-end;">
            <div>
                <div style="font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Resource Type</div>
                <select id="po-filter-restype" class="form-control" style="width:180px;height:30px;font-size:12px;padding:2px 8px;">
                    <option value="">-- All --</option>
                </select>
            </div>
            <div>
                <div style="font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Resource</div>
                <div style="position:relative;">
                    <input type="text" id="po-filter-resname" class="form-control" placeholder="Search..." style="width:200px;height:30px;font-size:12px;padding:2px 28px 2px 8px;">
                    <span class="icon-search5" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);color:#aaa;font-size:12px;pointer-events:none;"></span>
                </div>
            </div>
        </div>
        <div id="po-loader" class="procu-loader">Loading...</div>
        <div id="po-body"></div>
    </div>
    <div id="procu-tab-WO" class="procu-tab-content">
        <div id="wo-filter-bar" style="padding:14px 20px 0;display:flex;gap:16px;align-items:flex-end;">
            <div>
                <div style="font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Activity Type</div>
                <select id="wo-filter-acttype" class="form-control" style="width:200px;height:30px;font-size:12px;padding:2px 8px;">
                    <option value="">-- All --</option>
                </select>
            </div>
            <div>
                <div style="font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Activity</div>
                <div style="position:relative;">
                    <input type="text" id="wo-filter-name" class="form-control" placeholder="Search..." style="width:200px;height:30px;font-size:12px;padding:2px 28px 2px 8px;">
                    <span class="icon-search5" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);color:#aaa;font-size:12px;pointer-events:none;"></span>
                </div>
            </div>
        </div>
        <div id="wo-loader" class="procu-loader">Loading...</div>
        <div id="wo-body"></div>
    </div>
    <div id="procu-tab-LO" class="procu-tab-content">
        <p class="procu-empty">No lease orders found.</p>
    </div>

</div>

<style>
.wo-rate-inp::-webkit-outer-spin-button,.wo-rate-inp::-webkit-inner-spin-button,
.wo-qty-inp::-webkit-outer-spin-button,.wo-qty-inp::-webkit-inner-spin-button{-webkit-appearance:none;margin:0;}
</style>
<!-- ── Raise Work Order popup ───────────────────────────────────────────── -->
<div id="wo-raise-overlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.35);z-index:9998;"></div>
<div id="wo-raise-popup" style="display:none;position:fixed;z-index:9999;width:480px;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border-radius:7px;box-shadow:0 6px 28px rgba(0,0,0,0.22);overflow:hidden;">
    <div style="background:#072c47;color:#fff;padding:10px 16px;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:space-between;">
        <span>Raise Work Order</span>
        <span id="wo-raise-close" style="cursor:pointer;font-size:16px;opacity:0.8;">&times;</span>
    </div>
    <div style="padding:18px 20px;max-height:70vh;overflow-y:auto;">
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Vendor</label>
            <select id="wo-raise-vendor" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
                <option value="">— Select Vendor —</option>
            </select>
        </div>
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Subject</label>
            <input type="text" id="wo-raise-subject" placeholder="Work Order subject..." style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
        </div>
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Scope of Work</label>
            <textarea id="wo-raise-scope" rows="4" placeholder="Describe the scope of work..." style="width:100%;border:1px solid #c5ccd4;border-radius:4px;padding:6px 8px;font-size:12px;color:#333;resize:vertical;"></textarea>
        </div>
        <div style="display:flex;gap:12px;margin-bottom:14px;">
            <div style="flex:1;">
                <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Start Date</label>
                <input type="date" id="wo-raise-startdate" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
            </div>
            <div style="flex:1;">
                <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Duration</label>
                <input type="text" id="wo-raise-leadtime" placeholder="e.g. 30 days" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
            </div>
            <div style="flex:1;">
                <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Payment Cycle</label>
                <select id="wo-raise-payment-cycle" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
                    <option value="">— Select —</option>
                    <option value="Daily">Daily</option>
                    <option value="Weekly">Weekly</option>
                    <option value="Fortnightly">Fortnightly</option>
                    <option value="Monthly">Monthly</option>
                </select>
            </div>
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Terms &amp; Conditions</label>
            <div id="wo-raise-terms-list">
                <div class="wo-term-row" style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                    <input type="text" class="wo-term-input" placeholder="Enter term..." style="flex:1;height:30px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
                    <button type="button" class="wo-term-remove" style="background:#c0392b;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:14px;line-height:1;cursor:pointer;display:none;">&times;</button>
                </div>
            </div>
            <button type="button" id="wo-term-add-btn" style="margin-top:4px;background:none;border:1px dashed #c5ccd4;border-radius:4px;padding:3px 12px;font-size:11px;color:#465365;cursor:pointer;">+ Add Term</button>
        </div>
    </div>
    <div style="padding:10px 20px 16px;text-align:right;border-top:1px solid #eee;">
        <button type="button" id="wo-raise-cancel" style="background:#f0f4f8;border:1px solid #c5ccd4;border-radius:20px;padding:5px 18px;font-size:12px;color:#465365;cursor:pointer;margin-right:8px;">Cancel</button>
        <button type="button" id="wo-raise-save-btn" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:5px 22px;font-size:12px;font-weight:600;cursor:pointer;">Raise WO</button>
    </div>
</div>

<!-- ── WO Preview overlay ────────────────────────────────────────────────── -->
<input type="hidden" id="wo-preview-order-id">
<input type="hidden" id="wo-preview-vendor-email">
<input type="hidden" id="wo-preview-ordernumber">
<div id="wo-preview-overlay" class="po-preview-cntnr">
    <div style="background:#072c47;color:#fff;padding:10px 20px;font-size:15px;font-weight:600;letter-spacing:0.5px;display:flex;align-items:center;justify-content:space-between;">
        <span>WORK ORDER</span>
        <span id="wo-preview-close" style="cursor:pointer;font-size:20px;line-height:1;opacity:0.8;">&times;</span>
    </div>
    <div style="padding:20px;">
        <div style="text-align:right;margin-bottom:16px;">
            <button type="button" id="wo-email-btn" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;margin-right:8px;"><span class="icon-envelop"></span> Email to Vendor</button>
            <button type="button" id="wo-print-btn" style="background:#465365;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;margin-right:8px;"><span class="icon-printer"></span> Print</button>
            <button type="button" id="wo-cancel-draft-btn" style="background:#c0392b;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;margin-right:8px;">Cancel</button>
            <button type="button" id="wo-close-btn" style="background:#888;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;display:none;">Close</button>
        </div>
        <div id="wo-preview-content"></div>
    </div>
</div>

<!-- ── WO Email Compose popup ────────────────────────────────────────────── -->
<div id="wo-compose-overlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:10000;"></div>
<div id="wo-compose-popup" style="display:none;position:fixed;z-index:10001;width:500px;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border-radius:7px;box-shadow:0 6px 28px rgba(0,0,0,0.22);overflow:hidden;">
    <div style="background:#909090;color:#fff;padding:10px 16px;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:space-between;">
        <span>Email Work Order</span>
        <span id="wo-compose-close" style="cursor:pointer;font-size:16px;opacity:0.8;">&times;</span>
    </div>
    <div style="padding:18px 20px;">
        <div style="margin-bottom:13px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">To</label>
            <input type="email" id="wo-compose-to" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
        </div>
        <div style="margin-bottom:13px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Subject</label>
            <input type="text" id="wo-compose-subject" style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Body</label>
            <textarea id="wo-compose-body" rows="6" placeholder="Write your message here..." style="width:100%;border:1px solid #c5ccd4;border-radius:4px;padding:6px 8px;font-size:12px;color:#333;resize:vertical;"></textarea>
        </div>
    </div>
    <div style="padding:10px 20px 16px;text-align:right;border-top:1px solid #eee;">
        <button type="button" id="wo-compose-cancel" style="background:#f0f4f8;border:1px solid #c5ccd4;border-radius:20px;padding:5px 18px;font-size:12px;color:#465365;cursor:pointer;margin-right:8px;">Cancel</button>
        <button type="button" id="wo-compose-send-btn" style="background:#909090;color:#fff;border:none;border-radius:20px;padding:5px 22px;font-size:12px;font-weight:600;cursor:pointer;">Send with PDF Attachment</button>
    </div>
</div>

<!-- ── WO Mail Sent Toast ─────────────────────────────────────────────────── -->
<div id="wo-mail-toast" style="display:none;position:fixed;top:80px;left:50%;transform:translateX(-50%);background:#2d7a4f;color:#fff;padding:10px 32px;border-radius:50px;font-size:13px;font-weight:600;letter-spacing:0.5px;box-shadow:0 4px 18px rgba(0,0,0,0.22);z-index:99999;white-space:nowrap;">&#10003; Work Order Mail Sent</div>

<!-- ── WO Cancellation Mail Popup ─────────────────────────────────────────── -->
<div id="wo-cancel-mail-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:10000;"></div>
<div id="wo-cancel-mail-popup" style="display:none;position:fixed;z-index:10001;width:500px;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border-radius:7px;box-shadow:0 6px 28px rgba(0,0,0,0.22);overflow:hidden;">
    <div style="background:#c0392b;color:#fff;padding:10px 16px;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:space-between;">
        <span>Send WO Cancellation Notice</span>
        <span id="wo-cancel-mail-close" style="cursor:pointer;font-size:16px;opacity:0.8;">&times;</span>
    </div>
    <div style="padding:18px 20px;">
        <input type="hidden" id="wo-cancel-mail-woid">
        <div style="margin-bottom:13px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">To</label>
            <input type="email" id="wo-cancel-mail-to" readonly style="width:100%;height:32px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#555;background:#f8f8f8;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#465365;margin-bottom:4px;">Message</label>
            <textarea id="wo-cancel-mail-body" rows="6" style="width:100%;border:1px solid #c5ccd4;border-radius:4px;padding:6px 8px;font-size:12px;color:#333;resize:vertical;"></textarea>
        </div>
    </div>
    <div style="padding:10px 20px 16px;text-align:right;border-top:1px solid #eee;">
        <button type="button" id="wo-cancel-mail-cancel" style="background:#f0f4f8;border:1px solid #c5ccd4;border-radius:20px;padding:5px 18px;font-size:12px;color:#465365;cursor:pointer;margin-right:8px;">Close</button>
        <button type="button" id="wo-cancel-mail-send" style="background:#c0392b;color:#fff;border:none;border-radius:20px;padding:5px 22px;font-size:12px;font-weight:600;cursor:pointer;">Send</button>
    </div>
</div>

<!-- Work Orders Task Modal -->
<div id="wo-task-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;overflow-y:auto;">
    <div style="background:#fff;margin:40px auto;max-width:860px;border-radius:6px;padding:26px 30px;position:relative;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
        <button id="wo-task-modal-close" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:20px;color:#888;cursor:pointer;line-height:1;" title="Close">&times;</button>
        <h4 id="wo-task-modal-title" style="margin:0 0 18px;font-size:15px;font-weight:700;color:#333;padding-right:30px;"></h4>
        <div id="wo-task-modal-body"></div>
    </div>
</div>

<script>
(function(){
    var _poAllRows = [];

    function loadPurchaseOrders() {
        $('#po-loader').show();
        $('#po-body').html('');
        $.ajax({
            type: 'POST',
            url: '../procurement/purchaseorders',
            dataType: 'json',
            success: function(data) {
                $('#po-loader').hide();
                if (data.error !== 'No') {
                    $('#po-body').html('<p class="procu-empty">' + (data.errortext || 'Error loading data.') + '</p>');
                    return;
                }
                if (!data.rows || data.rows.length === 0) {
                    $('#po-body').html('<p class="procu-empty">No Materials or Consumables allocated in Estimate.</p>');
                    return;
                }
                _poAllRows = data.rows;
                window._poProjectName     = data.project_name     || '';
                window._poProjectLocation = data.project_location || '';
                populatePoResTypeFilter();
                applyPoFilters();
            },
            error: function() {
                $('#po-loader').hide();
                $('#po-body').html('<p class="procu-empty">Failed to load data. Please try again.</p>');
            }
        });
    }

    function populatePoResTypeFilter() {
        var types = {};
        $.each(_poAllRows, function(i, r) { if (r.resource_type) types[r.resource_type] = true; });
        var current = $('#po-filter-restype').val();
        var opts = '<option value="">-- Resource Type --</option>';
        $.each(Object.keys(types).sort(), function(i, t) {
            opts += '<option value="' + t + '"' + (t === current ? ' selected' : '') + '>' + t + '</option>';
        });
        $('#po-filter-restype').html(opts);
    }

    function applyPoFilters() {
        var typeFilter = $('#po-filter-restype').val();
        var nameFilter = $('#po-filter-resname').val().trim().toLowerCase();
        var rows = _poAllRows.filter(function(r) {
            if (typeFilter && r.resource_type !== typeFilter) return false;
            if (nameFilter && (r.resource_name || '').toLowerCase().indexOf(nameFilter) === -1) return false;
            return true;
        });
        renderPoTable(rows);
    }

    function renderPoTable(rows) {
        if (!rows.length) {
            $('#po-body').html('<p class="procu-empty">No matching resources found.</p>');
            return;
        }
        var projLabel = '';
        if (window._poProjectName) {
            var projText = window._poProjectName;
            if (window._poProjectLocation) projText += ' — ' + window._poProjectLocation;
            projLabel = '<div style="padding:10px 20px 0;font-size:12px;font-weight:600;color:#465365;letter-spacing:0.3px;">'
                + '<span style="background:#e8edf3;border-radius:4px;padding:3px 10px;">'
                + '<span style="color:#888;font-weight:400;">Project: </span>' + projText
                + '</span></div>';
        }
        var html = projLabel + '<div style="padding:10px 0 12px;text-align:right;">'
            + '<button type="button" id="po-issued-orders-btn" style="background:#465365;color:#fff;border:none;border-radius:20px;padding:7px 24px;font-size:13px;font-weight:600;cursor:pointer;margin-right:10px;">Issued Orders</button>'
            + '<button type="button" id="po-raise-all-btn" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:7px 24px;font-size:13px;font-weight:600;cursor:pointer;">Raise Purchase Orders</button>'
            + '</div>'
            + '<table class="table table-bordered procu-table">'
            + '<thead><tr>'
            + '<th>#</th>'
            + '<th>Resource</th>'
            + '<th>Unit</th>'
            + '<th style="text-align:right;">E.Qty</th>'
            + '<th style="text-align:right;">E.Value</th>'
            + '<th style="text-align:right;">P.Qty</th>'
            + '<th style="text-align:right;">P.Value</th>'
            + '<th style="text-align:right;">Stock at Site</th>'
            + '<th style="text-align:right;">Re-Order Qty</th>'
            + '<th style="text-align:right;">Rate</th>'
            + '<th style="text-align:right;">Amount</th>'
            + '<th style="text-align:center;"></th>'
            + '<th style="text-align:center;width:60px;">Select</th>'
            + '</tr></thead><tbody>';
        var rowNum = 0;
        var currentType = null;
        var totalEVal = 0;
        $.each(rows, function(i, r) {
            if (r.resource_type !== currentType) {
                currentType = r.resource_type;
                html += '<tr><td colspan="13" style="border:none;padding:0;height:7px;background:#fff;"></td></tr>'
                    + '<tr style="background:#c8c8c8;">'
                    + '<td colspan="13" style="padding:6px 10px;font-weight:700;font-size:11px;color:#000;border:1px solid #bbb;letter-spacing:0.3px;">'
                    + currentType
                    + '</td></tr>';
            }
            rowNum++;
            var rid    = r.resource_id;
            var rate   = parseFloat(r.rate) || 0;
            var eVal   = parseFloat(r.amount) || 0;
            totalEVal += eVal;
            var inp    = 'width:65px;height:24px;padding:1px 4px;text-align:right;font-size:11px;margin-left:auto;';
            var hasIndent  = (r.indent_stock  !== null && r.indent_stock  !== undefined && r.indent_stock  !== '')
                          || (r.indent_reorder !== null && r.indent_reorder !== undefined && r.indent_reorder !== '');
            var stockVal   = (hasIndent && r.indent_stock   != null) ? parseFloat(r.indent_stock).toFixed(2)   : '';
            var reorderVal = (hasIndent && r.indent_reorder != null) ? parseFloat(r.indent_reorder).toFixed(2) : '';
            var alertTd    = hasIndent ? 'background:#fff0f0;border:1px solid #e74c3c;' : '';
            var alertInp   = hasIndent ? inp + 'background:#fff0f0;border-color:#e74c3c;color:#c0392b;font-weight:700;' : inp;
            html += '<tr class="' + (rowNum % 2 === 0 ? 'procu-even' : '') + '">'
                + '<td>' + rowNum + '</td>'
                + '<td>' + r.resource_name
                    + (r.activity_name ? '<div style="font-size:10px;color:#7a8a9a;margin-top:1px;">' + r.activity_name + '</div>' : '')
                    + ((!r.task_ids || r.task_ids.toString().trim() === '') ? '<span style="background:#e74c3c;color:#fff;font-size:9px;border-radius:3px;padding:1px 5px;margin-top:2px;display:inline-block;">Not Mapped</span>' : '')
                    + '</td>'
                + '<td>' + (r.unit || '') + '</td>'
                + '<td class="num" style="background:#e6e6e6;color:#333;">' + parseFloat(r.estimated_quantity).toLocaleString(undefined, {maximumFractionDigits:2}) + '</td>'
                + '<td class="num" style="background:#e6e6e6;color:#333;">' + eVal.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>'
                + '<td class="num" style="background:#e6e6e6;color:#333;">' + parseFloat(r.purchased_quantity || 0).toLocaleString(undefined, {maximumFractionDigits:2}) + '</td>'
                + '<td class="num" style="background:#e6e6e6;color:#333;">' + parseFloat(r.purchased_value || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>'
                + '<td style="padding:2px 4px;text-align:right;' + alertTd + '">'
                + '<input type="text" inputmode="decimal" autocomplete="off" class="form-control input-sm po-stock-input" data-id="' + rid + '" placeholder="0" value="' + stockVal + '" readonly tabindex="-1" style="' + alertInp + (hasIndent ? '' : 'background:#f0f0f0;') + 'cursor:default;">'
                + '</td>'
                + '<td style="padding:2px 4px;text-align:right;' + alertTd + '">'
                + '<input type="text" inputmode="decimal" autocomplete="off" class="form-control input-sm po-reorder-input" data-id="' + rid + '" data-rate="' + rate + '" placeholder="0" value="' + reorderVal + '" style="' + alertInp + '">'
                + '</td>'
                + '<td style="padding:2px 4px;text-align:right;">'
                + '<input type="number" class="form-control input-sm po-rate-input" data-id="' + rid + '" min="0" step="any" placeholder="0.00" style="' + inp + '">'
                + '</td>'
                + '<td class="num po-amount-cell" data-id="' + rid + '">0.00</td>'
                + '<td style="white-space:nowrap;text-align:center;padding:2px 6px;">'
                + '<button type="button" class="btn btn-xs po-btn-params" data-id="' + rid + '" style="background:#465365;color:#fff;border-radius:20px;padding:6px 16px;margin:2px 2px;font-size:12px;">Parameters</button>'
                + '</td>'
                + '<td style="text-align:center;padding:4px 8px;">'
                + '<input type="checkbox" class="po-cb" value="' + rid + '" data-name="' + String(r.resource_name || '').replace(/"/g, '&quot;') + '" data-unit="' + String(r.unit || '').replace(/"/g, '&quot;') + '" data-allocation-id="' + (r.allocation_id || '') + '" data-task-ids="' + (r.task_ids || '') + '" style="width:16px;height:16px;cursor:pointer;accent-color:#072c47;">'
                + '</td>'
                + '</tr>';
        });
        html += '<tr style="background:#d0d8e8;font-weight:700;font-size:13px;" id="po-total-row">'
            + '<td colspan="4" style="text-align:right;padding:7px 10px;border:1px solid #b0b8c8;letter-spacing:0.3px;">Total</td>'
            + '<td class="num" style="background:#c4cfe0;color:#1a2540;border:1px solid #b0b8c8;">' + totalEVal.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>'
            + '<td colspan="5" style="border:1px solid #b0b8c8;"></td>'
            + '<td class="num" id="po-grand-total" style="background:#c4cfe0;color:#1a2540;border:1px solid #b0b8c8;">0.00</td>'
            + '<td colspan="2" style="border:1px solid #b0b8c8;"></td>'
            + '</tr>';
        html += '</tbody></table>';
        $('#po-body').html(html);
    }

    $(document).on('change', '#po-filter-restype', function() { applyPoFilters(); });
    $(document).on('input', '#po-filter-resname', function() {
        clearTimeout(window._poFilterTimer);
        window._poFilterTimer = setTimeout(applyPoFilters, 250);
    });

    function recalcAmount(rid) {
        var qty  = parseFloat($('.po-reorder-input[data-id="' + rid + '"]').val()) || 0;
        var rate = parseFloat($('.po-rate-input[data-id="'   + rid + '"]').val()) || 0;
        $('.po-amount-cell[data-id="' + rid + '"]').text((qty * rate).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}));
        var grandTotal = 0;
        $('.po-amount-cell').each(function(){ grandTotal += parseFloat($(this).text().replace(/,/g, '')) || 0; });
        $('#po-grand-total').text(grandTotal.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}));
    }
    $(document).on('input', '.po-reorder-input', function(){ recalcAmount($(this).data('id')); });
    $(document).on('blur',  '.po-reorder-input', function(){
        var v = parseFloat($(this).val());
        if (!isNaN(v)) $(this).val(v.toFixed(2));
    });
    $(document).on('input', '.po-rate-input',    function(){ recalcAmount($(this).data('id')); });


    // Raise PO popup
    function buildTermsRows(termsJson) {
        var terms = [];
        try { terms = JSON.parse(termsJson || '[]'); } catch(e) {}
        if (!terms.length) terms = [''];
        var html = '';
        $.each(terms, function(i, t) {
            html += '<div class="po-term-row" style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">'
                + '<input type="text" class="po-term-input" placeholder="Enter term..." style="flex:1;height:30px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;" value="' + $('<div>').text(t).html() + '">'
                + '<button type="button" class="po-term-remove" style="background:#c0392b;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:14px;line-height:1;cursor:pointer;' + (i === 0 ? 'display:none;' : '') + '">&times;</button>'
                + '</div>';
        });
        return html;
    }

    function openRaisePOPopup(btn) {
        var rid  = $(btn).data('id');
        var unit = $(btn).data('unit') || '';
        var name = $(btn).closest('tr').find('td:eq(1)').text();
        $('#po-raisepo-resid').val(rid);
        $('#po-raisepo-resname').text(name);
        $('#po-raisepo-unit').val(unit).prop('readonly', true);
        $('#po-raisepo-rate').val('');
        $('#po-raisepo-shipto').val('');
        $('#po-raisepo-vendor').val('');
        $('#po-raisepo-terms-list').html(buildTermsRows('[]'));
        $('#po-raisepo-overlay').show();
        $('#po-raisepo-popup').show();

        $('#po-raisepo-vendor').html('<option value="">Loading...</option>');

        // Load vendors for this resource, then load saved settings
        $.ajax({
            type: 'POST',
            url: '../procurement/getvendors',
            data: { resource_id: rid },
            dataType: 'json',
            success: function(data) {
                var opts = '<option value="">— Select Vendor —</option>';
                if (data.error === 'No') {
                    $.each(data.vendors, function(i, v) {
                        opts += '<option value="' + v.Vendor_Id + '">' + v.Name + '</option>';
                    });
                }
                $('#po-raisepo-vendor').html(opts);

                // Load saved settings and pre-select vendor
                $.ajax({
                    type: 'POST',
                    url: '../procurement/getposettings',
                    data: { resource_id: rid },
                    dataType: 'json',
                    success: function(data) {
                        if (data.error === 'No' && data.data) {
                            var d = data.data;
                            if (d.vendor_id) $('#po-raisepo-vendor').val(d.vendor_id);
                            $('#po-raisepo-rate').val(d.rate || '');
                            $('#po-raisepo-shipto').val(d.ship_to || '');
                            $('#po-raisepo-terms-list').html(buildTermsRows(d.terms || '[]'));
                        }
                    }
                });
            }
        });
    }

    function closeRaisePOPopup() {
        $('#po-raisepo-popup').hide();
        $('#po-raisepo-overlay').hide();
    }

    $(document).on('click', '.po-btn-raise', function(){ openRaisePOPopup(this); });
    $(document).on('click', '#po-raisepo-close, #po-raisepo-cancel, #po-raisepo-overlay', closeRaisePOPopup);

    // Select checkbox — highlight row
    $(document).on('change', '.po-cb', function(){
        $(this).closest('tr').toggleClass('sk-row-selected', $(this).is(':checked'));
    });


    // Raise Purchase Orders (bulk) button
    $(document).on('click', '#po-raise-all-btn', function(){
        if ($('.po-cb:checked').length === 0) {
            alert('Please select at least one resource to raise a Purchase Order.');
            return;
        }
        var unmapped = [];
        $('.po-cb:checked').each(function(){
            var taskIds = ($(this).data('task-ids') || '').toString().trim();
            if (!taskIds) unmapped.push($(this).data('name') || '');
        });
        if (unmapped.length > 0) {
            alert('Please map the following resource(s) to a task in the Resource Allocation page before raising a Purchase Order:\n• ' + unmapped.join('\n• '));
            return;
        }
        $('#po-bulk-credit-period, #po-bulk-lead-time').val('');
        var _shipTo = window._poProjectName || '';
        if (window._poProjectLocation) _shipTo += '\n' + window._poProjectLocation;
        $('#po-bulk-shipto').val(_shipTo.trim());
        $('#po-bulk-terms-list').html(
            '<div class="po-bulk-term-row" style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">'
            + '<input type="text" class="po-bulk-term-input" placeholder="Enter term..." style="flex:1;height:30px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">'
            + '<button type="button" class="po-bulk-term-remove" style="background:#c0392b;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:14px;line-height:1;cursor:pointer;display:none;">&times;</button>'
            + '</div>'
        );
        // Load vendors and pre-fill from previous PO settings
        var firstRid = $('.po-cb:checked').first().val();
        $('#po-bulk-vendor').html('<option value="">Loading...</option>');
        $.when(
            $.ajax({ type: 'POST', url: '../procurement/getvendors', dataType: 'json', data: { context: 'po' } }),
            $.ajax({ type: 'POST', url: '../procurement/getposettings', dataType: 'json', data: { resource_id: firstRid } })
        ).done(function(vendorResp, settingsResp) {
            var vData = vendorResp[0], sData = settingsResp[0];
            var opts = '<option value="">— Select Vendor —</option>';
            if (vData.error === 'No') {
                $.each(vData.vendors, function(i, v) {
                    opts += '<option value="' + v.Vendor_Id + '">' + v.Name + '</option>';
                });
            }
            $('#po-bulk-vendor').html(opts);
            // Pre-fill from previous PO settings if available
            if (sData.error === 'No' && sData.data) {
                var s = sData.data;
                if (s.vendor_id)     $('#po-bulk-vendor').val(s.vendor_id);
                if (s.credit_period) $('#po-bulk-credit-period').val(s.credit_period);
                if (s.lead_time)     $('#po-bulk-lead-time').val(s.lead_time);
                if (s.ship_to && s.ship_to.trim()) $('#po-bulk-shipto').val(s.ship_to.trim());
                if (s.terms) {
                    try {
                        var terms = JSON.parse(s.terms);
                        if (terms && terms.length) {
                            var tHtml = '';
                            $.each(terms, function(i, t) {
                                tHtml += '<div class="po-bulk-term-row" style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">'
                                    + '<input type="text" class="po-bulk-term-input" value="' + $('<div>').text(t).html() + '" placeholder="Enter term..." style="flex:1;height:30px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">'
                                    + '<button type="button" class="po-bulk-term-remove" style="background:#c0392b;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:14px;line-height:1;cursor:pointer;">&times;</button>'
                                    + '</div>';
                            });
                            $('#po-bulk-terms-list').html(tHtml);
                        }
                    } catch(e) {}
                }
            }
        });
        $('#po-bulk-overlay').show();
        $('#po-bulk-popup').show();
    });

    $(document).on('click', '#po-bulk-close, #po-bulk-cancel, #po-bulk-overlay', function(){
        $('#po-bulk-popup').hide();
        $('#po-bulk-overlay').hide();
    });

    // Raise PO — collect selected rows and submit
    $(document).on('click', '#po-bulk-save-btn', function(){
        if ($('.po-cb:checked').length === 0) {
            alert('Please select at least one resource to raise a Purchase Order.');
            return;
        }
        var invalidQty = [], invalidRate = [];
        $('.po-cb:checked').each(function(){
            var rid  = $(this).val();
            var name = $(this).data('name') || rid;
            var qty  = parseFloat($('.po-reorder-input[data-id="' + rid + '"]').val());
            var rate = parseFloat($('.po-rate-input[data-id="'   + rid + '"]').val());
            if (!qty  || qty  <= 0) invalidQty.push(name);
            if (!rate || rate <= 0) invalidRate.push(name);
        });
        if (invalidQty.length > 0) {
            alert('Re-order Quantity is required for:\n• ' + invalidQty.join('\n• '));
            return;
        }
        if (invalidRate.length > 0) {
            alert('Rate is required for:\n• ' + invalidRate.join('\n• '));
            return;
        }
        var resources = [];
        $('.po-cb:checked').each(function(){
            var rid  = $(this).val();
            var qty  = parseFloat($('.po-reorder-input[data-id="' + rid + '"]').val());
            var rate = parseFloat($('.po-rate-input[data-id="'   + rid + '"]').val());
            resources.push({
                resource_id:   rid,
                allocation_id: $(this).data('allocation-id') || '',
                resource_name: $(this).data('name') || '',
                unit:          $(this).data('unit') || '',
                qty:           qty,
                rate:          rate
            });
        });

        if (!$('#po-bulk-vendor').val()) {
            alert('Please select a vendor.');
            return;
        }

        var terms = [];
        $('.po-bulk-term-input').each(function(){ var v = $(this).val().trim(); if (v) terms.push(v); });

        var $btn = $(this).prop('disabled', true).text('Processing...');
        $.ajax({
            type: 'POST',
            url: '../procurement/raisepobulk',
            dataType: 'json',
            data: {
                vendor_id:     $('#po-bulk-vendor').val(),
                ship_to:       $('#po-bulk-shipto').val().trim(),
                credit_period: $('#po-bulk-credit-period').val().trim(),
                lead_time:     $('#po-bulk-lead-time').val().trim(),
                terms:         JSON.stringify(terms),
                resources:     JSON.stringify(resources)
            },
            success: function(data) {
                $btn.prop('disabled', false).text('Raise PO');
                if (data.error === 'No') {
                    $('#po-bulk-popup').hide();
                    $('#po-bulk-overlay').hide();
                    // Clear stock and reorder inputs for raised resources
                    $.each(resources, function(i, r) {
                        var rid = r.resource_id;
                        var $stockInp   = $('.po-stock-input[data-id="'   + rid + '"]');
                        var $reorderInp = $('.po-reorder-input[data-id="' + rid + '"]');
                        $stockInp.val('').css({background: '', 'border-color': '', color: '', 'font-weight': ''});
                        $reorderInp.val('').css({background: '', 'border-color': '', color: '', 'font-weight': ''});
                        $stockInp.closest('td').css({background: '', border: ''});
                        $reorderInp.closest('td').css({background: '', border: ''});
                        $('.po-cb[value="' + rid + '"]').prop('checked', false).closest('tr').removeClass('sk-row-selected');
                    });
                    $('#po-preview-order-id').val(data.order_id);
                    $('#po-preview-vendor-email').val(data.vendor_email || '');
                    $('#po-preview-ordernumber').val(data.ordernumber || '');
                    $('#po-preview-content').html(data.html);
                    $('#po-preview-overlay').addClass('active');
                    $('body').css('overflow-y', 'hidden');
                } else {
                    alert(data.errortext || 'Error creating purchase order.');
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('Raise PO');
                alert('Failed to create purchase order. Please try again.');
            }
        });
    });

    // Print PO — open fresh window so background colours always render
    $(document).on('click', '#po-print-btn', function(){
        var content = $('#po-preview-content').html();
        var win = window.open('', '_blank', 'width=960,height=720');
        win.document.write(
            '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Purchase Order</title>'
            + '<style>'
            + '* { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; box-sizing: border-box; }'
            + 'body { margin: 24px; padding: 0; font-family: Arial, sans-serif; }'
            + '@media print { body { margin: 0; } }'
            + '</style></head><body>'
            + content
            + '<script>window.onload = function(){ window.print(); window.close(); };<\/script>'
            + '</body></html>'
        );
        win.document.close();
    });

    // PO Preview overlay close
    $(document).on('click', '#po-preview-close, #po-close-btn', function(){
        $('#po-preview-overlay').removeClass('active');
        $('body').css('overflow-y', '');
    });

    // View PO from Issued Orders list
    $(document).on('click', '.po-io-view-btn', function(){
        var orderId = $(this).data('order-id');
        $('#po-preview-content').html('<p style="color:#888;padding:20px;text-align:center;">Loading...</p>');
        $('#po-preview-overlay').addClass('active');
        $('body').css('overflow-y', 'hidden');
        $.ajax({
            type: 'POST', url: '../procurement/viewpo',
            data: { order_id: orderId }, dataType: 'json',
            success: function(data) {
                if (data.error !== 'No') {
                    $('#po-preview-content').html('<p style="color:#c0392b;padding:20px;">'
                        + (data.errortext || 'Error loading PO.') + '</p>');
                    return;
                }
                $('#po-preview-content').html(data.html);
            },
            error: function() {
                $('#po-preview-content').html('<p style="color:#c0392b;padding:20px;">Failed to load purchase order.</p>');
            }
        });
    });

    // Email PO to vendor — open compose popup
    $(document).on('click', '#po-email-btn', function(){
        $('#po-compose-to').val($('#po-preview-vendor-email').val());
        $('#po-compose-subject').val('Purchase Order ' + $('#po-preview-ordernumber').val());
        $('#po-compose-body').val('');
        $('#po-compose-overlay').show();
        $('#po-compose-popup').show();
    });

    $(document).on('click', '#po-compose-close, #po-compose-cancel, #po-compose-overlay', function(){
        $('#po-compose-popup').hide();
        $('#po-compose-overlay').hide();
    });

    $(document).on('click', '#po-compose-send-btn', function(){
        var to = $('#po-compose-to').val().trim();
        if (!to) { alert('Please enter a recipient email address.'); return; }
        var $btn = $(this).prop('disabled', true).text('Sending...');
        $.ajax({
            type: 'POST',
            url: '../procurement/emailpo',
            dataType: 'json',
            data: {
                order_id: $('#po-preview-order-id').val(),
                to:       to,
                subject:  $('#po-compose-subject').val().trim(),
                body:     $('#po-compose-body').val().trim()
            },
            success: function(data) {
                $btn.prop('disabled', false).text('Send with PDF Attachment');
                if (data.error === 'No') {
                    $('#po-compose-popup').hide();
                    $('#po-compose-overlay').hide();
                    $('#po-mail-toast').stop(true, true).fadeIn(300).delay(2500).fadeOut(500);
                } else {
                    alert(data.errortext || 'Error sending email.');
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('Send with PDF Attachment');
                alert('Failed to send. Please try again.');
            }
        });
    });

    // Bulk terms add/remove
    $(document).on('click', '#po-bulk-term-add-btn', function(){
        var row = '<div class="po-bulk-term-row" style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">'
            + '<input type="text" class="po-bulk-term-input" placeholder="Enter term..." style="flex:1;height:30px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">'
            + '<button type="button" class="po-bulk-term-remove" style="background:#c0392b;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:14px;line-height:1;cursor:pointer;">&times;</button>'
            + '</div>';
        $('#po-bulk-terms-list').append(row);
    });

    $(document).on('click', '.po-bulk-term-remove', function(){
        $(this).closest('.po-bulk-term-row').remove();
    });

    // Add term row
    $(document).on('click', '#po-term-add-btn', function(){
        var row = '<div class="po-term-row" style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">'
            + '<input type="text" class="po-term-input" placeholder="Enter term..." style="flex:1;height:30px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">'
            + '<button type="button" class="po-term-remove" style="background:#c0392b;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:14px;line-height:1;cursor:pointer;">&times;</button>'
            + '</div>';
        $('#po-raisepo-terms-list').append(row);
    });

    // Remove term row
    $(document).on('click', '.po-term-remove', function(){
        $(this).closest('.po-term-row').remove();
    });

    // Raise PO save
    $(document).on('click', '#po-raisepo-save-btn', function(){
        var vendor = $('#po-raisepo-vendor').val();
        if (!vendor) { alert('Please select a vendor.'); return; }
        var terms = [];
        $('.po-term-input').each(function(){ if ($(this).val().trim()) terms.push($(this).val().trim()); });
        var $btn = $(this).prop('disabled', true).text('Saving...');
        $.ajax({
            type: 'POST',
            url: '../procurement/saveposettings',
            data: {
                resource_id: $('#po-raisepo-resid').val(),
                vendor_id:   vendor,
                unit:        $('#po-raisepo-unit').val().trim(),
                rate:        $('#po-raisepo-rate').val(),
                ship_to:     $('#po-raisepo-shipto').val(),
                terms:       JSON.stringify(terms)
            },
            dataType: 'json',
            success: function(data) {
                $btn.prop('disabled', false).text('Raise PO');
                if (data.error === 'No') closeRaisePOPopup();
                else alert(data.errortext || 'Error saving.');
            },
            error: function() {
                $btn.prop('disabled', false).text('Raise PO');
                alert('Failed to save. Please try again.');
            }
        });
    });

    // Parameters popup
    function openParamsPopup(btn) {
        var rid  = $(btn).data('id');
        var name = $(btn).closest('tr').find('td:eq(1)').text();
        var unit = $(btn).closest('tr').find('td:eq(2)').text().trim();
        $('#po-param-resource-id').val(rid);
        $('#po-param-resource-name').text(name);
        $('#po-param-reorder-level').val('');
        $('#po-param-lot-size').val('');
        $('.po-param-unit-lbl').text(unit);

        // Position popup near the button
        var offset = $(btn).offset();
        var popW = 260;
        var left = offset.left - popW - 8;
        if (left < 8) left = offset.left + $(btn).outerWidth() + 8;
        var top  = offset.top - 10;
        if (top + 240 > $(window).height()) top = $(window).height() - 250;
        $('#po-param-popup').css({ top: top, left: left }).show();
        $('#po-param-overlay').show();

        // Load existing values
        $.ajax({
            type: 'POST',
            url: '../procurement/getparameters',
            data: { resource_id: rid },
            dataType: 'json',
            success: function(data) {
                if (data.error === 'No' && data.data) {
                    $('#po-param-reorder-level').val(data.data.reorder_level || '');
                    $('#po-param-lot-size').val(data.data.lot_size || '');
                }
            }
        });
    }

    function closeParamsPopup() {
        $('#po-param-popup').hide();
        $('#po-param-overlay').hide();
    }

    $(document).on('click', '.po-btn-params', function(){ openParamsPopup(this); });
    $(document).on('click', '#po-param-close, #po-param-overlay', closeParamsPopup);

    $(document).on('click', '#po-param-save-btn', function(){
        var $btn = $(this).prop('disabled', true).text('Saving...');
        $.ajax({
            type: 'POST',
            url: '../procurement/saveparameters',
            data: {
                resource_id:   $('#po-param-resource-id').val(),
                reorder_level: $('#po-param-reorder-level').val(),
                lot_size:      $('#po-param-lot-size').val()
            },
            dataType: 'json',
            success: function(data) {
                $btn.prop('disabled', false).text('Save');
                if (data.error === 'No') closeParamsPopup();
                else alert(data.errortext || 'Error saving.');
            },
            error: function() {
                $btn.prop('disabled', false).text('Save');
                alert('Failed to save. Please try again.');
            }
        });
    });

    // ── Issued Orders (within Purchase Orders tab) ─────────────────────────
    function loadIssuedOrders() {
        $('#po-loader').show();
        $('#po-body').html('');
        $.ajax({
            type: 'POST', url: '../procurement/issuedorders', dataType: 'json',
            success: function(data) {
                $('#po-loader').hide();
                var backBtn = '<div style="padding:10px 0 12px;text-align:right;">'
                    + '<button type="button" id="po-io-back-btn" style="background:#465365;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;">&larr; Back to Purchase Orders</button>'
                    + '</div>';
                if (data.error !== 'No' || !data.rows || !data.rows.length) {
                    $('#po-body').html(backBtn + '<p class="procu-empty">No issued orders found.</p>');
                    return;
                }
                var html = backBtn
                    + '<div style="background:#072c47;color:#fff;padding:10px 20px;font-size:14px;font-weight:600;letter-spacing:0.5px;">Issued Orders</div>'
                    + '<div style="height:14px;"></div>'
                    + '<table class="table table-bordered procu-table">'
                    + '<thead><tr>'
                    + '<th style="width:32px;">#</th>'
                    + '<th>Vendor</th>'
                    + '<th style="width:170px;">PO Number</th>'
                    + '<th style="width:92px;">Date</th>'
                    + '<th style="text-align:center;width:320px;">Actions</th>'
                    + '</tr></thead><tbody>';
                var rowNum = 0;
                var btnStyle = 'border:none;border-radius:20px;padding:5px 14px;font-size:11px;cursor:pointer;margin-right:6px;';
                $.each(data.rows, function(i, r) {
                    rowNum++;
                    var cancelled = (r.delete_status == 1);
                    var rowStyle  = cancelled ? 'background:#f5f5f5;color:#bbb;' : '';
                    var viewBtn   = '<button type="button" class="po-io-view-btn"'
                          + ' data-order-id="' + r.order_id + '" data-ordernumber="' + r.ordernumber + '"'
                          + ' style="' + btnStyle + 'background:#072c47;color:#fff;">View PO</button>';
                    var actions   = cancelled
                        ? viewBtn + '<button type="button" class="po-io-recover-btn"'
                          + ' data-order-id="' + r.order_id + '" data-ordernumber="' + r.ordernumber + '"'
                          + ' style="' + btnStyle + 'background:#27ae60;color:#fff;">Recover</button>'
                        : viewBtn
                          + '<button type="button" class="po-io-cancel-btn"'
                          + ' data-order-id="' + r.order_id + '" data-ordernumber="' + r.ordernumber + '"'
                          + ' style="' + btnStyle + 'background:#c0392b;color:#fff;">Cancel</button>'
                          + '<button type="button" class="po-io-mail-btn"'
                          + ' data-order-id="' + r.order_id + '" data-ordernumber="' + r.ordernumber + '"'
                          + ' data-date="' + (r.orderdate || '') + '" data-email="' + (r.vendor_email || '') + '"'
                          + ' style="' + btnStyle + 'background:#465365;color:#fff;">Send a Mail</button>';
                    html += '<tr class="' + (rowNum % 2 === 0 ? 'procu-even' : '') + '" style="' + rowStyle + '" data-order-id="' + r.order_id + '">'
                        + '<td>' + rowNum + '</td>'
                        + '<td>' + r.vendor_name + '</td>'
                        + '<td>' + r.ordernumber + '</td>'
                        + '<td>' + (r.orderdate || '') + '</td>'
                        + '<td style="padding:3px 8px;text-align:center;">' + actions + '</td></tr>';
                });
                html += '</tbody></table>';
                $('#po-body').html(html);
            },
            error: function() {
                $('#po-loader').hide();
                $('#po-body').html('<p class="procu-empty">Failed to load. Please try again.</p>');
            }
        });
    }

    // Open Issued Orders within the PO tab
    $(document).on('click', '#po-issued-orders-btn', function(){ loadIssuedOrders(); });

    // Back button — return to PO list
    $(document).on('click', '#po-io-back-btn', function(){ loadPurchaseOrders(); });

    // Cancel / Recover PO toggle
    $(document).on('click', '.po-io-cancel-btn, .po-io-recover-btn', function(){
        var $btn      = $(this);
        var isCancelling = $btn.hasClass('po-io-cancel-btn');
        var orderId   = $btn.data('order-id');
        var url       = isCancelling ? '../procurement/cancelpo' : '../procurement/recoverpo';
        $btn.prop('disabled', true).text(isCancelling ? 'Cancelling...' : 'Recovering...');
        $.ajax({
            type: 'POST', url: url,
            data: { order_id: orderId }, dataType: 'json',
            success: function(data) {
                if (data.error !== 'No') {
                    $btn.prop('disabled', false).text(isCancelling ? 'Cancel' : 'Recover');
                    alert(data.errortext || 'Error.');
                    return;
                }
                var $row = $btn.closest('tr');
                if (isCancelling) {
                    $row.css({background: '#f5f5f5', color: '#bbb'});
                    $btn.removeClass('po-io-cancel-btn').addClass('po-io-recover-btn')
                        .css('background', '#27ae60').text('Recover').prop('disabled', false);
                } else {
                    $row.css({background: '', color: ''});
                    $btn.removeClass('po-io-recover-btn').addClass('po-io-cancel-btn')
                        .css('background', '#c0392b').text('Cancel').prop('disabled', false);
                }
            },
            error: function() {
                $btn.prop('disabled', false).text(isCancelling ? 'Cancel' : 'Recover');
                alert('Failed. Please try again.');
            }
        });
    });

    // Open cancellation mail popup
    $(document).on('click', '.po-io-mail-btn', function(){
        var orderId  = $(this).data('order-id');
        var orderNum = $(this).data('ordernumber');
        var date     = $(this).data('date');
        var email    = $(this).data('email');
        $('#po-cancel-mail-orderid').val(orderId);
        $('#po-cancel-mail-to').val(email);
        $('#po-cancel-mail-body').val('The Order No: ' + orderNum + ' Dated ' + date + ' hereby stands cancelled.');
        $('#po-cancel-mail-overlay, #po-cancel-mail-popup').show();
    });

    // Close cancellation mail popup
    $(document).on('click', '#po-cancel-mail-close, #po-cancel-mail-cancel, #po-cancel-mail-overlay', function(){
        $('#po-cancel-mail-overlay, #po-cancel-mail-popup').hide();
    });

    // Send cancellation mail
    $(document).on('click', '#po-cancel-mail-send', function(){
        var $btn = $(this).prop('disabled', true).text('Sending...');
        $.ajax({
            type: 'POST', url: '../procurement/sendcancellation',
            data: { order_id: $('#po-cancel-mail-orderid').val(), body: $('#po-cancel-mail-body').val() },
            dataType: 'json',
            success: function(data) {
                $btn.prop('disabled', false).text('Send');
                if (data.error !== 'No') { alert(data.errortext || 'Error sending mail.'); return; }
                var sentOrderId = $('#po-cancel-mail-orderid').val();
                $('#po-cancel-mail-overlay, #po-cancel-mail-popup').hide();
                $('.po-io-mail-btn[data-order-id="' + sentOrderId + '"]')
                    .prop('disabled', true).text('Mail Sent')
                    .css({background:'#e0e0e0', color:'#aaa', cursor:'not-allowed'});
            },
            error: function() { $btn.prop('disabled', false).text('Send'); alert('Failed to send. Please try again.'); }
        });
    });
    // ── End Issued Orders Tab ──────────────────────────────────────────────

    // ── Work Orders Tab ───────────────────────────────────────────────────────
    var _woAllRows    = [];
    var _woTasksCache = {}; // keyed by activity_id → { name, unit, qty, tasks:[{task_name,task_unit,approx_qty,rate,amount}] }

    function loadWorkOrders() {
        $('#wo-filter-bar').show();
        $('#wo-loader').show();
        $('#wo-body').html('');
        $.ajax({
            type: 'POST', url: '../procurement/workorders', dataType: 'json',
            success: function(data) {
                $('#wo-loader').hide();
                if (data.error !== 'No' || !data.rows || !data.rows.length) {
                    $('#wo-body').html('<p class="procu-empty">No activities found for this project.</p>');
                    return;
                }
                _woAllRows = data.rows;
                populateWoActTypeFilter();
                applyWoFilters();
            },
            error: function() {
                $('#wo-loader').hide();
                $('#wo-body').html('<p class="procu-empty">Failed to load. Please try again.</p>');
            }
        });
    }

    function populateWoActTypeFilter() {
        var types = {};
        $.each(_woAllRows, function(i, r) { if (r.activity_type) types[r.activity_type] = true; });
        var current = $('#wo-filter-acttype').val();
        var opts = '<option value="">-- All --</option>';
        $.each(Object.keys(types).sort(), function(i, t) {
            opts += '<option value="' + t + '"' + (t === current ? ' selected' : '') + '>' + t + '</option>';
        });
        $('#wo-filter-acttype').html(opts);
    }

    function applyWoFilters() {
        var typeFilter = $('#wo-filter-acttype').val();
        var nameFilter = $('#wo-filter-name').val().trim().toLowerCase();
        var rows = _woAllRows.filter(function(r) {
            if (typeFilter && r.activity_type !== typeFilter) return false;
            if (nameFilter && (r.activity_name || '').toLowerCase().indexOf(nameFilter) === -1) return false;
            return true;
        });
        renderWoTable(rows);
    }

    function renderWoTable(rows) {
        if (!rows.length) {
            $('#wo-body').html('<p class="procu-empty">No matching activities found.</p>');
            return;
        }
        var html = '<div style="padding:10px 0 12px;text-align:right;">'
            + '<button type="button" id="wo-issued-orders-btn" style="background:#465365;color:#fff;border:none;border-radius:20px;padding:7px 24px;font-size:13px;font-weight:600;cursor:pointer;margin-right:10px;">Issued Orders</button>'
            + '<button type="button" id="wo-raise-btn" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:7px 24px;font-size:13px;font-weight:600;cursor:pointer;">Raise Work Orders</button>'
            + '</div>'
            + '<table class="table table-bordered procu-table">'
            + '<thead><tr>'
            + '<th style="width:32px;">#</th>'
            + '<th style="width:95px;">Activity Type</th>'
            + '<th style="width:260px;">Activity</th>'
            + '<th style="width:70px;">Unit</th>'
            + '<th style="text-align:right;width:130px;">Estimated Qty</th>'
            + '<th style="text-align:right;width:130px;">Estimated Value</th>'
            + '<th style="text-align:right;width:130px;">Work Done Qty</th>'
            + '<th style="text-align:right;width:130px;">Work Done Value</th>'
            + '<th style="width:150px;text-align:center;"></th>'
            + '<th style="text-align:center;width:60px;">Select</th>'
            + '</tr></thead><tbody>';
        var rowNum = 0;
        var currentIow = null;
        var totalEstQty   = 0;
        var totalEstValue = 0;
        var totalWdQty    = 0;
        var totalWdValue  = 0;
        $.each(rows, function(i, r) {
            if (r.workgroup_name !== currentIow) {
                currentIow = r.workgroup_name;
                html += '<tr><td colspan="10" style="border:none;padding:0;height:7px;background:#fff;"></td></tr>'
                    + '<tr style="background:#e0e0e0;">'
                    + '<td colspan="10" style="padding:7px 12px;font-weight:700;font-size:12px;color:#333;border:1px solid #c8c8c8;letter-spacing:0.3px;">'
                    + (currentIow || 'Item of Work') + '</td></tr>';
                rowNum = 0;
            }
            rowNum++;
            var estQty   = parseFloat(r.estimated_quantity) || 0;
            var estValue = parseFloat(r.sc_estimated_value) || 0;
            var mbWdq    = parseFloat(r.mb_work_done_qty) || 0;
            var wdValue  = parseFloat(r.mb_work_done_value) || 0;
            totalEstQty   += estQty;
            totalEstValue += estValue;
            totalWdQty    += mbWdq;
            totalWdValue  += wdValue;
            html += '<tr class="' + (rowNum % 2 === 0 ? 'procu-even' : '') + '">'
                + '<td>' + rowNum + '</td>'
                + '<td style="font-size:11px;color:#555;">' + (r.activity_type || '') + '</td>'
                + '<td>' + r.activity_name + '</td>'
                + '<td>' + (r.unit || '') + '</td>'
                + '<td class="num" style="background:#e6e6e6;color:#333;">' + estQty.toLocaleString(undefined, {maximumFractionDigits:2}) + '</td>'
                + '<td class="num" style="background:#e6e6e6;color:#333;">'
                +   (estValue ? estValue.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) : '&mdash;')
                + '</td>'
                + '<td class="num" style="background:#e6f0e6;color:#333;">'
                +   (mbWdq ? mbWdq.toLocaleString(undefined, {maximumFractionDigits:2}) : '&mdash;')
                + '</td>'
                + '<td class="num" style="background:#e6f0e6;color:#333;">'
                +   (wdValue ? wdValue.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) : '&mdash;')
                + '</td>'
                + (function(){
                    var sid = r.schedule_activity_id;
                    if (sid) {
                        return '<td style="text-align:center;padding:4px 6px;">'
                            + '<button type="button" class="wo-task-btn" data-id="' + r.activity_id + '" data-schedule-id="' + sid + '" data-name="' + r.activity_name.replace(/"/g,'&quot;') + '" data-est-qty="' + estQty + '" style="background:#072c47;color:#fff;border:none;border-radius:12px;padding:5px 6px;font-size:12px;font-weight:400;cursor:pointer;letter-spacing:0.3px;min-width:124px;">Tasks</button>'
                            + '</td>';
                    } else {
                        return '<td style="text-align:center;padding:4px 6px;">'
                            + '<button type="button" disabled style="background:#aaa;color:#fff;border:none;border-radius:12px;padding:4px 0;font-size:11px;font-weight:400;cursor:not-allowed;letter-spacing:0.3px;width:110px;opacity:0.5;">Tasks</button>'
                            + '</td>';
                    }
                })()
                + '<td style="text-align:center;padding:4px 8px;">'
                +   '<input type="checkbox" class="wo-cb" value="' + r.activity_id + '" data-name="' + r.activity_name.replace(/"/g,'&quot;') + '" data-unit="' + (r.unit || '').replace(/"/g,'&quot;') + '" data-qty="' + estQty + '" style="width:16px;height:16px;cursor:pointer;accent-color:#072c47;">'
                + '</td>'
                + '</tr>';
        });
        html += '</tbody>'
            + '<tfoot><tr style="background:#d0d8e8;font-weight:700;font-size:13px;">'
            + '<td colspan="4" style="text-align:right;padding:7px 10px;border:1px solid #b0b8c8;letter-spacing:0.3px;">Total</td>'
            + '<td class="num" style="background:#c4cfe0;color:#1a2540;border:1px solid #b0b8c8;">' + totalEstQty.toLocaleString(undefined, {maximumFractionDigits:2}) + '</td>'
            + '<td class="num" style="background:#c4cfe0;color:#1a2540;border:1px solid #b0b8c8;">' + totalEstValue.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>'
            + '<td class="num" style="background:#d8ead8;color:#1a2540;border:1px solid #b0b8c8;">' + (totalWdQty ? totalWdQty.toLocaleString(undefined, {maximumFractionDigits:2}) : '&mdash;') + '</td>'
            + '<td class="num" style="background:#d8ead8;color:#1a2540;border:1px solid #b0b8c8;">' + (totalWdValue ? totalWdValue.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) : '&mdash;') + '</td>'
            + '<td colspan="2" style="border:1px solid #b0b8c8;"></td>'
            + '</tr></tfoot>'
            + '</table>';
        $('#wo-body').html(html);
    }

    function closeWoRaisePopup() {
        $('#wo-raise-popup').hide();
        $('#wo-raise-overlay').hide();
    }

    $(document).on('click', '#wo-raise-btn', function() {
        var selected = [];
        $('.wo-cb:checked').each(function() {
            selected.push({ id: $(this).val(), name: $(this).data('name'), unit: $(this).data('unit'), qty: parseFloat($(this).data('qty')) || 0 });
        });
        if (!selected.length) { alert('Please select at least one activity.'); return; }

        // Check sub-contractor resource mapping before opening popup
        var actIds = selected.map(function(s){ return s.id; });
        $.ajax({
            type: 'POST', url: '../procurement/checkworesources', dataType: 'json',
            data: { activity_ids: JSON.stringify(actIds) },
            success: function(chk) {
                if (chk.error !== 'No') { alert(chk.errortext || 'Please allocate a contractor for this task'); return; }

                // All good — reset fields and open popup
                $('#wo-raise-subject, #wo-raise-scope, #wo-raise-leadtime, #wo-raise-startdate').val('');
                $('#wo-raise-payment-cycle').val('');
                $('#wo-raise-terms-list').html('<div class="wo-term-row" style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">'
                    + '<input type="text" class="wo-term-input" placeholder="Enter term..." style="flex:1;height:30px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">'
                    + '<button type="button" class="wo-term-remove" style="background:#c0392b;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:14px;line-height:1;cursor:pointer;display:none;">&times;</button></div>');

                $('#wo-raise-vendor').html('<option value="">Loading...</option>');
                $.ajax({ type:'POST', url:'../procurement/getvendors', dataType:'json', data: { context: 'wo' },
                    success: function(data) {
                        var opts = '<option value="">— Select Vendor —</option>';
                        if (data.error === 'No') $.each(data.vendors, function(i,v){ opts += '<option value="'+v.Vendor_Id+'">'+v.Name+'</option>'; });
                        $('#wo-raise-vendor').html(opts);
                    }
                });

                $('#wo-raise-overlay').show();
                $('#wo-raise-popup').show();
            },
            error: function() { alert('Validation failed. Please try again.'); }
        });
    });

    $(document).on('click', '#wo-raise-close, #wo-raise-cancel, #wo-raise-overlay', function(e){
        if (e.target === this) closeWoRaisePopup();
        if ($(this).is('#wo-raise-close, #wo-raise-cancel')) closeWoRaisePopup();
    });

    // Terms add/remove
    $(document).on('click', '#wo-term-add-btn', function(){
        $('#wo-raise-terms-list').append('<div class="wo-term-row" style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">'
            + '<input type="text" class="wo-term-input" placeholder="Enter term..." style="flex:1;height:30px;border:1px solid #c5ccd4;border-radius:4px;padding:2px 8px;font-size:12px;color:#333;">'
            + '<button type="button" class="wo-term-remove" style="background:#c0392b;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:14px;line-height:1;cursor:pointer;">&times;</button></div>');
    });
    $(document).on('click', '.wo-term-remove', function(){ $(this).closest('.wo-term-row').remove(); });

    // Raise WO — save
    $(document).on('click', '#wo-raise-save-btn', function(){
        if (!$('#wo-raise-vendor').val()) { alert('Please select a vendor.'); return; }

        var activities = [];
        $('.wo-cb:checked').each(function() {
            var actId  = $(this).val();
            var cache  = _woTasksCache[actId] || {};
            activities.push({
                activity_id  : actId,
                activity_name: $(this).data('name') || cache.name || '',
                unit         : $(this).data('unit') || cache.unit || '',
                qty          : parseFloat($(this).data('qty')) || 0,
                tasks        : cache.tasks || []
            });
        });
        if (!activities.length) { alert('Please select at least one activity.'); return; }

        var terms = [];
        $('.wo-term-input').each(function(){ var v=$(this).val().trim(); if(v) terms.push(v); });

        var $btn = $(this).prop('disabled', true).text('Processing...');
        $.ajax({
            type: 'POST', url: '../procurement/raiseworkorder', dataType: 'json',
            data: {
                vendor_id    : $('#wo-raise-vendor').val(),
                subject      : $('#wo-raise-subject').val().trim(),
                scope        : $('#wo-raise-scope').val().trim(),
                lead_time      : $('#wo-raise-leadtime').val().trim(),
                start_date     : $('#wo-raise-startdate').val().trim(),
                payment_cycle  : $('#wo-raise-payment-cycle').val(),
                terms        : JSON.stringify(terms),
                activities   : JSON.stringify(activities)
            },
            success: function(data) {
                $btn.prop('disabled', false).text('Raise WO');
                if (data.error !== 'No') { alert(data.errortext || 'Error creating work order.'); return; }
                closeWoRaisePopup();
                // Uncheck selected rows
                $('.wo-cb:checked').each(function(){
                    $(this).prop('checked', false).closest('tr').removeClass('sk-row-selected');
                });
                $('#wo-preview-order-id').val(data.order_id);
                $('#wo-preview-vendor-email').val(data.vendor_email || '');
                $('#wo-preview-ordernumber').val(data.ordernumber || '');
                $('#wo-preview-content').html(data.html);
                $('#wo-preview-overlay').addClass('active');
                $('body').css('overflow-y', 'hidden');
            },
            error: function() {
                $btn.prop('disabled', false).text('Raise WO');
                alert('Failed to create work order. Please try again.');
            }
        });
    });

    // WO Preview — shared discard-and-close helper
    function woDiscardAndClose() {
        var orderId = $('#wo-preview-order-id').val();
        if (orderId) {
            $.post('../procurement/discardworkorder', { order_id: orderId });
        }
        $('#wo-preview-overlay').removeClass('active');
        $('body').css('overflow-y', '');
        $('#wo-close-btn').hide();
        $('#wo-cancel-draft-btn').show();
    }
    $(document).on('click', '#wo-preview-close, #wo-cancel-draft-btn', woDiscardAndClose);

    // WO Print — issue the draft first, then open print window
    $(document).on('click', '#wo-print-btn', function(){
        var orderId = $('#wo-preview-order-id').val();
        var $btn    = $(this).prop('disabled', true);
        var html    = $('#wo-preview-content').html();
        $.ajax({
            type:'POST', url:'../procurement/issueworkorder', dataType:'json',
            data:{ order_id: orderId },
            success: function() {
                $btn.prop('disabled', false);
                var win = window.open('', '_blank', 'width=960,height=720');
                win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Work Order</title>'
                    + '<style>*{-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;box-sizing:border-box;}'
                    + 'body{margin:24px;padding:0;font-family:Arial,sans-serif;}'
                    + '@media print{body{margin:0;}}</style></head><body>'
                    + html
                    + '<script>window.onload=function(){window.print();window.close();};<\/script></body></html>');
                win.document.close();
            },
            error: function() {
                $btn.prop('disabled', false);
                alert('Failed to issue work order. Please try again.');
            }
        });
    });

    // WO Email — open compose
    $(document).on('click', '#wo-email-btn', function(){
        $('#wo-compose-to').val($('#wo-preview-vendor-email').val());
        $('#wo-compose-subject').val('Work Order ' + $('#wo-preview-ordernumber').val());
        $('#wo-compose-body').val('');
        $('#wo-compose-overlay').show();
        $('#wo-compose-popup').show();
    });
    $(document).on('click', '#wo-compose-close, #wo-compose-cancel', function(){
        $('#wo-compose-popup').hide();
        $('#wo-compose-overlay').hide();
    });
    $(document).on('click', '#wo-compose-overlay', function(e){
        if (e.target === this) { $('#wo-compose-popup').hide(); $(this).hide(); }
    });
    $(document).on('click', '#wo-compose-send-btn', function(){
        var to = $('#wo-compose-to').val().trim();
        if (!to) { alert('Please enter a recipient email address.'); return; }
        var $btn = $(this).prop('disabled', true).text('Sending...');
        $.ajax({
            type:'POST', url:'../procurement/emailwo', dataType:'json',
            data: { order_id:$('#wo-preview-order-id').val(), to:to,
                    subject:$('#wo-compose-subject').val().trim(), body:$('#wo-compose-body').val().trim() },
            success: function(data) {
                $btn.prop('disabled', false).text('Send with PDF Attachment');
                if (data.error === 'No') {
                    $('#wo-compose-popup').hide();
                    $('#wo-compose-overlay').hide();
                    $('#wo-mail-toast').stop(true,true).fadeIn(300).delay(2500).fadeOut(500);
                } else { alert(data.errortext || 'Error sending email.'); }
            },
            error: function() { $btn.prop('disabled', false).text('Send with PDF Attachment'); alert('Failed to send. Please try again.'); }
        });
    });

    // ── WO Issued Orders ───────────────────────────────────────────────────────
    function loadIssuedWorkOrders() {
        $('#wo-loader').show();
        $('#wo-body').html('');
        $.ajax({
            type: 'POST', url: '../procurement/issuedworkorders', dataType: 'json',
            success: function(data) {
                $('#wo-loader').hide();
                var backBtn = '<div style="padding:10px 0 12px;text-align:right;">'
                    + '<button type="button" id="wo-io-back-btn" style="background:#465365;color:#fff;border:none;border-radius:20px;padding:6px 20px;font-size:12px;font-weight:600;cursor:pointer;">&larr; Back to Work Orders</button>'
                    + '</div>';
                if (data.error !== 'No' || !data.rows || !data.rows.length) {
                    $('#wo-body').html(backBtn + '<p class="procu-empty">No issued work orders found.</p>');
                    return;
                }
                var html = backBtn
                    + '<div style="background:#072c47;color:#fff;padding:10px 20px;font-size:14px;font-weight:600;letter-spacing:0.5px;">Issued Work Orders</div>'
                    + '<div style="height:14px;"></div>'
                    + '<table class="table table-bordered procu-table">'
                    + '<thead><tr>'
                    + '<th style="width:32px;">#</th>'
                    + '<th>Vendor</th>'
                    + '<th style="width:180px;">WO Number</th>'
                    + '<th style="width:92px;">Date</th>'
                    + '<th style="text-align:center;width:320px;">Actions</th>'
                    + '</tr></thead><tbody>';
                var rowNum = 0;
                var woBtnStyle = 'border:none;border-radius:20px;padding:5px 14px;font-size:11px;cursor:pointer;margin-right:6px;';
                $.each(data.rows, function(i, r) {
                    rowNum++;
                    var cancelled = (r.WO_Status == 1);
                    var rowStyle  = cancelled ? 'background:#f5f5f5;color:#bbb;' : '';
                    var viewBtn   = '<button type="button" class="wo-io-view-btn"'
                          + ' data-wo-id="' + r.WO_Id + '" data-wonumber="' + r.WO_Number + '"'
                          + ' style="' + woBtnStyle + 'background:#072c47;color:#fff;">View WO</button>';
                    var actions   = cancelled
                        ? viewBtn + '<button type="button" class="wo-io-recover-btn"'
                          + ' data-wo-id="' + r.WO_Id + '" data-wonumber="' + r.WO_Number + '"'
                          + ' style="' + woBtnStyle + 'background:#27ae60;color:#fff;">Recover</button>'
                        : viewBtn
                          + '<button type="button" class="wo-io-cancel-btn"'
                          + ' data-wo-id="' + r.WO_Id + '" data-wonumber="' + r.WO_Number + '"'
                          + ' style="' + woBtnStyle + 'background:#c0392b;color:#fff;">Cancel</button>'
                          + '<button type="button" class="wo-io-mail-btn"'
                          + ' data-wo-id="' + r.WO_Id + '" data-wonumber="' + r.WO_Number + '"'
                          + ' data-date="' + (r.Date_requested || '') + '" data-email="' + (r.vendor_email || '') + '"'
                          + ' style="' + woBtnStyle + 'background:#465365;color:#fff;">Send a Mail</button>';
                    html += '<tr class="' + (rowNum % 2 === 0 ? 'procu-even' : '') + '" style="' + rowStyle + '" data-wo-id="' + r.WO_Id + '">'
                        + '<td>' + rowNum + '</td>'
                        + '<td>' + r.vendor_name + '</td>'
                        + '<td>' + r.WO_Number + '</td>'
                        + '<td>' + (r.Date_requested || '') + '</td>'
                        + '<td style="padding:3px 8px;text-align:center;">' + actions + '</td></tr>';
                });
                html += '</tbody></table>';
                $('#wo-body').html(html);
            },
            error: function() {
                $('#wo-loader').hide();
                $('#wo-body').html('<p class="procu-empty">Failed to load. Please try again.</p>');
            }
        });
    }

    $(document).on('click', '#wo-issued-orders-btn', function(){
        $('#wo-filter-bar').hide();
        loadIssuedWorkOrders();
    });

    $(document).on('click', '#wo-io-back-btn', function(){
        $('#wo-filter-bar').show();
        loadWorkOrders();
    });

    // Cancel / Recover WO toggle
    $(document).on('click', '.wo-io-cancel-btn, .wo-io-recover-btn', function(){
        var $btn         = $(this);
        var isCancelling = $btn.hasClass('wo-io-cancel-btn');
        var woId         = $btn.data('wo-id');
        var url          = isCancelling ? '../procurement/cancelwo' : '../procurement/recoverwo';
        $btn.prop('disabled', true).text(isCancelling ? 'Cancelling...' : 'Recovering...');
        $.ajax({
            type: 'POST', url: url,
            data: { wo_id: woId }, dataType: 'json',
            success: function(data) {
                if (data.error !== 'No') {
                    $btn.prop('disabled', false).text(isCancelling ? 'Cancel' : 'Recover');
                    alert(data.errortext || 'Error.');
                    return;
                }
                var $row = $btn.closest('tr');
                if (isCancelling) {
                    $row.css({background: '#f5f5f5', color: '#bbb'});
                    $btn.removeClass('wo-io-cancel-btn').addClass('wo-io-recover-btn')
                        .css('background', '#27ae60').text('Recover').prop('disabled', false);
                } else {
                    $row.css({background: '', color: ''});
                    $btn.removeClass('wo-io-recover-btn').addClass('wo-io-cancel-btn')
                        .css('background', '#c0392b').text('Cancel').prop('disabled', false);
                }
            },
            error: function() {
                $btn.prop('disabled', false).text(isCancelling ? 'Cancel' : 'Recover');
                alert('Failed. Please try again.');
            }
        });
    });

    // View WO from Issued Orders list
    $(document).on('click', '.wo-io-view-btn', function(){
        var woNum = $(this).data('wonumber');
        $('#wo-preview-order-id').val('');
        $('#wo-cancel-draft-btn').hide();
        $('#wo-close-btn').show();
        $('#wo-preview-content').html('<p style="color:#888;padding:20px;text-align:center;">Loading...</p>');
        $('#wo-preview-overlay').addClass('active');
        $('body').css('overflow-y', 'hidden');
        $.ajax({
            type: 'POST', url: '../procurement/viewwo',
            data: { wo_number: woNum }, dataType: 'json',
            success: function(data) {
                if (data.error !== 'No') {
                    $('#wo-preview-content').html('<p style="color:#c0392b;padding:20px;">'
                        + (data.errortext || 'Error loading WO.') + '</p>');
                    return;
                }
                $('#wo-preview-content').html(data.html);
            },
            error: function() {
                $('#wo-preview-content').html('<p style="color:#c0392b;padding:20px;">Failed to load work order.</p>');
            }
        });
    });

    // Close WO overlay (issued-view mode)
    $(document).on('click', '#wo-close-btn', function(){
        $('#wo-preview-overlay').removeClass('active');
        $('body').css('overflow-y', '');
        $('#wo-close-btn').hide();
        $('#wo-cancel-draft-btn').show();
    });

    // Open WO cancellation mail popup
    $(document).on('click', '.wo-io-mail-btn', function(){
        var woId  = $(this).data('wo-id');
        var woNum = $(this).data('wonumber');
        var date  = $(this).data('date');
        var email = $(this).data('email');
        $('#wo-cancel-mail-woid').val(woId);
        $('#wo-cancel-mail-to').val(email);
        $('#wo-cancel-mail-body').val('The Work Order No: ' + woNum + ' Dated ' + date + ' hereby stands cancelled.');
        $('#wo-cancel-mail-overlay, #wo-cancel-mail-popup').show();
    });

    $(document).on('click', '#wo-cancel-mail-close, #wo-cancel-mail-cancel', function(){
        $('#wo-cancel-mail-popup, #wo-cancel-mail-overlay').hide();
    });
    $(document).on('click', '#wo-cancel-mail-overlay', function(e){
        if (e.target === this) { $('#wo-cancel-mail-popup').hide(); $(this).hide(); }
    });

    $(document).on('click', '#wo-cancel-mail-send', function(){
        var to = $('#wo-cancel-mail-to').val().trim();
        if (!to) { alert('No vendor email on file.'); return; }
        var $btn = $(this).prop('disabled', true).text('Sending...');
        $.ajax({
            type: 'POST', url: '../procurement/sendwocancellation', dataType: 'json',
            data: { wo_id: $('#wo-cancel-mail-woid').val(), body: $('#wo-cancel-mail-body').val().trim() },
            success: function(data) {
                $btn.prop('disabled', false).text('Send');
                if (data.error === 'No') {
                    var sentWoId = $('#wo-cancel-mail-woid').val();
                    $('#wo-cancel-mail-popup, #wo-cancel-mail-overlay').hide();
                    $('.wo-io-mail-btn[data-wo-id="' + sentWoId + '"]')
                        .prop('disabled', true).text('Mail Sent')
                        .css({background:'#e0e0e0', color:'#aaa', cursor:'not-allowed'});
                    $('#wo-mail-toast').stop(true,true).fadeIn(300).delay(2500).fadeOut(500);
                } else { alert(data.errortext || 'Error sending email.'); }
            },
            error: function() { $btn.prop('disabled', false).text('Send'); alert('Failed to send. Please try again.'); }
        });
    });

    function woSyncCache(activityId, activityName, actUnit, actQty) {
        var tasks = [];
        var curActQty = parseFloat($('#wo-activity-qty-inp').val()) || actQty;
        $('#wo-task-modal-body .wo-task-row').each(function() {
            if (!$(this).find('.wo-sc-select').is(':checked')) return;
            var $tr      = $(this);
            var totalQty = parseFloat($tr.find('.wo-task-qty-inp').val()) || 0;
            var rate     = parseFloat($tr.find('.wo-rate-inp').val()) || 0;
            tasks.push({
                task_id   : $tr.data('task-id') || 0,
                task_name : $tr.find('.wo-td-name span').text(),
                task_unit : $tr.find('.wo-td-unit span').text(),
                task_qty  : totalQty,
                approx_qty: totalQty,
                rate      : rate,
                amount    : rate * totalQty
            });
        });
        _woTasksCache[activityId] = { name: activityName, unit: actUnit, qty: curActQty, tasks: tasks };
    }

    $(document).on('click', '#wo-task-save-btn', function() {
        var $btn       = $(this).prop('disabled', true).text('Saving...');
        var activityId = $(this).data('activity-id');
        var tasks      = [];
        $('#wo-task-modal-body .wo-task-row').each(function() {
            var $tr      = $(this);
            var unitQty  = parseFloat($tr.find('.wo-task-qty-inp').data('unit-qty')) || 0;
            var totalQty = parseFloat($tr.find('.wo-task-qty-inp').val()) || 0;
            tasks.push({
                task_id  : $tr.data('task-id') || 0,
                rate     : parseFloat($tr.find('.wo-rate-inp').val()) || 0,
                task_qty : unitQty,
                total_qty: totalQty,
                selected : $tr.find('.wo-sc-select').is(':checked') ? 1 : 0
            });
        });
        // All selected tasks must have rate > 0
        var missingRate = tasks.some(function(t){ return t.selected && t.rate <= 0; });
        var anySelected = tasks.some(function(t){ return t.selected; });
        if (!anySelected) {
            alert('Please select at least one task.');
            $btn.prop('disabled', false).text('Save');
            return;
        }
        if (missingRate) {
            alert('Please enter rates for all selected tasks before saving.');
            $btn.prop('disabled', false).text('Save');
            return;
        }

        var actQtyVal = parseFloat($('#wo-activity-qty-inp').val());
        $.ajax({
            type: 'POST', url: '../procurement/savewotaskrates', dataType: 'json',
            data: { activity_id: activityId, activity_qty: isNaN(actQtyVal) ? '' : actQtyVal, tasks: JSON.stringify(tasks) },
            success: function(data) {
                $btn.prop('disabled', false).text('Save');
                if (data.error === 'No') {
                    var $m = $('#wo-task-modal');
                    woSyncCache($m.data('activity-id'), $m.data('activity-name'), $m.data('activity-unit'), $m.data('activity-qty'));
                    $m.hide();
                } else {
                    alert(data.errortext || 'Error saving.');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Save');
                alert('Save failed (' + xhr.status + '): ' + xhr.responseText.substring(0, 200));
            }
        });
    });

    $(document).on('click', '.wo-task-btn', function() {
        var schedId    = $(this).data('schedule-id');
        var name       = $(this).data('name');
        var actQty     = parseFloat($(this).data('est-qty')) || 0;
        var activityId = $(this).data('id');
        var actUnit    = $(this).closest('tr').find('td:eq(3)').text().trim();
        if (!schedId) return;
        $('#wo-task-modal').data('activity-id', activityId).data('activity-name', name).data('activity-unit', actUnit).data('activity-qty', actQty);
        $('#wo-task-modal-title').text(name);
        $('#wo-task-modal-body').html('<p style="text-align:center;padding:30px;color:#888;">Loading...</p>');
        $('#wo-task-modal').show();
        $.ajax({
            type: 'POST',
            url: '../procurement/wotasks',
            dataType: 'json',
            data: { schedule_activity_id: schedId },
            success: function(data) {
                if (data.error !== 'No') {
                    $('#wo-task-modal-body').html('<p style="text-align:center;padding:20px;color:#888;">No tasks found for this activity.</p>');
                    return;
                }
                if (!data.rows || !data.rows.length) {
                    $('#wo-task-modal-body').html('<p style="text-align:center;padding:20px;color:#888;">No tasks have been defined for this activity.</p>');
                    return;
                }
                var inp  = 'width:80px;height:26px;padding:2px 5px;text-align:right;font-size:12px;border:1px solid #ccc;border-radius:3px;-moz-appearance:textfield;-webkit-appearance:none;appearance:none;';
                var actQtyBk = data.activity_qty || 0;
                var html = '<div style="margin-bottom:10px;font-size:13px;display:flex;align-items:center;gap:12px;">'
                    + '<label style="font-weight:600;color:#444;">Activity Qty:</label>'
                    + '<input type="number" id="wo-activity-qty-inp" value="' + actQtyBk + '" min="0" step="any" style="' + inp + 'width:100px;">'
                    + '<span style="color:#888;font-size:12px;">(' + (data.unit || '') + ')</span>'
                    + '</div>'
                    + '<table style="width:100%;border-collapse:collapse;font-size:13px;">'
                    + '<thead><tr style="background:#555;color:#fff;">'
                    + '<th style="padding:9px 12px;border:1px solid #444;width:36px;text-align:center;"><input type="checkbox" id="wo-sc-select-all" checked style="width:14px;height:14px;cursor:pointer;" title="Select all"></th>'
                    + '<th style="padding:9px 12px;border:1px solid #444;">Task</th>'
                    + '<th style="padding:9px 12px;border:1px solid #444;width:70px;text-align:center;">Unit</th>'
                    + '<th style="padding:9px 12px;border:1px solid #444;width:120px;text-align:right;">Quantity</th>'
                    + '<th style="padding:9px 12px;border:1px solid #444;width:110px;text-align:right;">Rate</th>'
                    + '<th style="padding:9px 12px;border:1px solid #444;width:110px;text-align:right;">Amount</th>'
                    + '</tr></thead><tbody>';
                $.each(data.rows, function(i, r) {
                    var bg        = i % 2 === 1 ? 'background:#f7f7f7;' : 'background:#fff;';
                    var unitQty   = r.task_unit_qty != null ? parseFloat(r.task_unit_qty) : 0;
                    var savedRate = r.saved_rate != null ? parseFloat(r.saved_rate) : 0;
                    var totalQty  = unitQty * actQtyBk;
                    var initAmt   = savedRate * totalQty;
                    html += '<tr style="' + bg + '" class="wo-task-row" data-task-id="' + (r.task_id || '') + '">'
                        + '<td style="padding:7px 12px;border:1px solid #e0e0e0;text-align:center;">'
                        +   '<input type="checkbox" class="wo-sc-select" ' + (r.selected !== 0 ? 'checked' : '') + ' style="width:14px;height:14px;cursor:pointer;">'
                        + '</td>'
                        + '<td style="padding:5px 8px;border:1px solid #e0e0e0;" class="wo-td-name">'
                        +   '<span>' + (r.task_name || '') + '</span>'
                        + '</td>'
                        + '<td style="padding:5px 8px;border:1px solid #e0e0e0;text-align:center;" class="wo-td-unit">'
                        +   '<span>' + (r.task_unit || '') + '</span>'
                        + '</td>'
                        + '<td style="padding:5px 8px;border:1px solid #e0e0e0;text-align:right;">'
                        +   '<input type="number" class="wo-task-qty-inp" data-unit-qty="' + unitQty + '" value="' + totalQty.toFixed(3) + '" readonly style="width:80px;height:26px;padding:2px 5px;text-align:right;font-size:12px;border:1px solid #ccc;border-radius:3px;background:#f0f0f0;color:#555;cursor:not-allowed;">'
                        + '</td>'
                        + '<td style="padding:5px 8px;border:1px solid #e0e0e0;text-align:right;" class="wo-td-rate">'
                        +   '<input type="number" class="wo-rate-inp" data-approx="' + totalQty + '" min="0" step="any" placeholder="0.00" value="' + (savedRate || '') + '" style="' + inp + '">'
                        + '</td>'
                        + '<td style="padding:7px 12px;border:1px solid #e0e0e0;text-align:right;font-weight:600;" class="wo-task-amount">' + (initAmt ? initAmt.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) : '0.00') + '</td>'
                        + '</tr>';
                });
                html += '<tr style="background:#f0f0f0;font-weight:700;">'
                    + '<td colspan="5" style="padding:7px 12px;border:1px solid #ddd;text-align:right;">Total (selected)</td>'
                    + '<td style="padding:7px 12px;border:1px solid #ddd;text-align:right;" id="wo-task-grand-total">0.00</td>'
                    + '</tr>';
                html += '</tbody></table>';
                html += '<div style="text-align:right;margin-top:14px;">'
                    + '<button type="button" id="wo-task-cancel-btn" style="background:#888;color:#fff;border:none;border-radius:20px;padding:6px 22px;font-size:13px;font-weight:600;cursor:pointer;margin-right:10px;">Cancel</button>'
                    + '<button type="button" id="wo-task-save-btn" data-activity-id="' + activityId + '" style="background:#072c47;color:#fff;border:none;border-radius:20px;padding:6px 26px;font-size:13px;font-weight:600;cursor:pointer;">Save</button>'
                    + '</div>';
                $('#wo-task-modal-body').html(html);
                woRecalcTotal();
                woSyncCache(activityId, name, actUnit, actQty);
            },
            error: function() {
                $('#wo-task-modal-body').html('<p style="text-align:center;padding:20px;color:#c00;">Failed to load tasks. Please try again.</p>');
            }
        });
    });

    function woRecalcTotal() {
        var total = 0;
        $('#wo-task-modal-body .wo-task-row').each(function() {
            if ($(this).find('.wo-sc-select').is(':checked')) {
                total += parseFloat($(this).find('.wo-task-amount').text().replace(/,/g,'')) || 0;
            }
        });
        $('#wo-task-grand-total').text(total.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}));
    }

    $(document).on('input', '.wo-rate-inp', function() {
        var $tr    = $(this).closest('tr');
        var rate   = parseFloat($(this).val()) || 0;
        var approx = parseFloat($(this).data('approx')) || 0;
        $tr.find('.wo-task-amount').text((rate * approx).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}));
        woRecalcTotal();
    });

    $(document).on('input', '#wo-activity-qty-inp', function() {
        var newActQty = parseFloat($(this).val()) || 0;
        $('#wo-task-modal-body .wo-task-row').each(function() {
            var $tr     = $(this);
            var unitQty = parseFloat($tr.find('.wo-task-qty-inp').data('unit-qty')) || 0;
            var newTotal = +(unitQty * newActQty).toFixed(3);
            var $qtyInp = $tr.find('.wo-task-qty-inp');
            $qtyInp.val(newTotal);
            $tr.find('.wo-rate-inp').data('approx', newTotal);
            var rate = parseFloat($tr.find('.wo-rate-inp').val()) || 0;
            $tr.find('.wo-task-amount').text((rate * newTotal).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}));
        });
        woRecalcTotal();
    });

    $(document).on('input', '.wo-task-qty-inp', function() {
        var $tr      = $(this).closest('tr');
        var totalQty = parseFloat($(this).val()) || 0;
        $tr.find('.wo-rate-inp').data('approx', totalQty);
        var rate = parseFloat($tr.find('.wo-rate-inp').val()) || 0;
        $tr.find('.wo-task-amount').text((rate * totalQty).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}));
        woRecalcTotal();
    });


    $(document).on('input', '.wo-qty-inp', function() {
        var $tr     = $(this).closest('tr');
        var actQty  = parseFloat($(this).data('act-qty')) || 0;
        var taskQty = parseFloat($(this).val()) || 0;
        var approx  = actQty * taskQty;
        $tr.find('.wo-approx-qty').text(approx ? approx.toLocaleString(undefined,{maximumFractionDigits:3}) : '-');
        $tr.find('.wo-rate-inp').data('approx', approx);
        var rate = parseFloat($tr.find('.wo-rate-inp').val()) || 0;
        $tr.find('.wo-task-amount').text((rate * approx).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}));
        woRecalcTotal();
    });

    $(document).on('click', '.wo-row-edit', function() {
        var $btn    = $(this);
        var $tr     = $btn.closest('tr');
        var editing = $btn.text() === 'Save';
        if (!editing) {
            $tr.find('.wo-rate-view').hide();
            $tr.find('.wo-rate-inp').show().focus();
            $btn.text('Save').css('background','#27ae60');
        } else {
            var newRate = parseFloat($tr.find('.wo-rate-inp').val()) || 0;
            var approx  = parseFloat($tr.find('.wo-rate-inp').data('approx')) || 0;
            var newAmt  = newRate * approx;
            $tr.find('.wo-rate-view').text(newRate ? newRate.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) : '—').show();
            $tr.find('.wo-rate-inp').hide();
            $tr.find('.wo-task-amount').text(newAmt ? newAmt.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) : '0.00');
            $btn.text('Edit').css('background','#465365');
            woRecalcTotal();
            var $m = $('#wo-task-modal');
            woSyncCache($m.data('activity-id'), $m.data('activity-name'), $m.data('activity-unit'), $m.data('activity-qty'));
        }
    });

    $(document).on('change', '.wo-sc-select', function() {
        woRecalcTotal();
        var $m = $('#wo-task-modal');
        woSyncCache($m.data('activity-id'), $m.data('activity-name'), $m.data('activity-unit'), $m.data('activity-qty'));
    });

    $(document).on('change', '#wo-sc-select-all', function() {
        var checked = $(this).is(':checked');
        $('#wo-task-modal-body .wo-sc-select').prop('checked', checked);
        woRecalcTotal();
        var $m = $('#wo-task-modal');
        woSyncCache($m.data('activity-id'), $m.data('activity-name'), $m.data('activity-unit'), $m.data('activity-qty'));
    });

    $(document).on('click', '#wo-task-cancel-btn', function() {
        var $m = $('#wo-task-modal');
        $m.hide();
    });

    $('#wo-task-modal-close').on('click', function() {
        var $m = $('#wo-task-modal');
        woSyncCache($m.data('activity-id'), $m.data('activity-name'), $m.data('activity-unit'), $m.data('activity-qty'));
        $m.hide();
    });
    $('#wo-task-modal').on('click', function(e) {
        if (e.target !== this) return;
        var $m = $(this);
        woSyncCache($m.data('activity-id'), $m.data('activity-name'), $m.data('activity-unit'), $m.data('activity-qty'));
        $m.hide();
    });

    $(document).on('change', '.wo-cb', function(){
        $(this).closest('tr').toggleClass('sk-row-selected', $(this).is(':checked'));
    });

    $(document).on('change', '#wo-filter-acttype', function() { applyWoFilters(); });
    $(document).on('input', '#wo-filter-name', function() {
        clearTimeout(window._woFilterTimer);
        window._woFilterTimer = setTimeout(applyWoFilters, 250);
    });

    $(document).on('change', '.wo-wdq-inp', function() {
        var $inp = $(this);
        $.post('../procurement/savewdq', {
            activity_id  : $inp.data('activity-id'),
            work_done_qty: parseFloat($inp.val()) || 0
        });
    });

    // ── End Work Orders Tab ───────────────────────────────────────────────────


    document.querySelectorAll('.procu-tab-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            var tab = btn.getAttribute('data-tab');
            if (!tab) return; // not a tab button (e.g. Resource Library)
            document.querySelectorAll('.procu-tab-btn').forEach(function(b){ b.classList.remove('active'); });
            document.querySelectorAll('.procu-tab-content').forEach(function(c){ c.classList.remove('active'); });
            btn.classList.add('active');
            document.getElementById('procu-tab-' + tab).classList.add('active');
            if (tab === 'WO') loadWorkOrders();
            else if (tab === 'PO') loadPurchaseOrders();
        });
    });


    // Auto-load Purchase Orders on page ready
    $(document).ready(function(){ loadPurchaseOrders(); });

    // Reload PO data if page is restored from browser bfcache (back/forward navigation)
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) loadPurchaseOrders();
    });
})();
</script>
