<style>
.uperm-wrap { padding: 18px; }
.uperm-top-row { display:flex; justify-content:flex-end; margin-bottom:14px; }
.uperm-add-user-btn { background:#8e24aa; color:#fff; border:none; border-radius:18px; padding:7px 20px; font-size:12px; font-weight:600; cursor:pointer; }
.uperm-add-user-btn:hover { background:#6a1b7a; }

.uperm-select-row { display:flex; align-items:flex-end; gap:16px; flex-wrap:wrap; background:#f8fafc; border:1px solid #e0e6ee; border-radius:6px; padding:14px 16px; margin-bottom:18px; }
.uperm-select-field { display:flex; flex-direction:column; gap:4px; }
.uperm-select-field label { font-size:11px; font-weight:600; color:#465365; text-transform:uppercase; letter-spacing:.4px; }
.uperm-select-field select { height:32px; min-width:200px; border:1px solid #c5ccd4; border-radius:4px; padding:2px 8px; font-size:12px; }
.uperm-select-field input[readonly] { height:32px; min-width:160px; border:1px solid #e0e6ee; background:#eef0f3; border-radius:4px; padding:2px 8px; font-size:12px; color:#555; }

.uperm-icons-row { display:block; }
.uperm-icons-hdr { font-size:12px; font-weight:700; color:#465365; text-transform:uppercase; letter-spacing:.4px; margin-bottom:10px; }
.uperm-icons-sub { font-size:11px; color:#8a94a3; margin-bottom:12px; }
.uperm-icons-list { display:flex; flex-wrap:wrap; gap:16px; }
.uperm-icon-tile { position:relative; display:flex; flex-direction:column; align-items:center; gap:6px; width:78px; cursor:not-allowed; user-select:none; text-align:center; }
.uperm-icon-tile.uperm-enabled { cursor:pointer; }
.uperm-icon-tile.uperm-enabled:not(.checked) .uperm-icon-glyph { opacity:.4; filter:grayscale(55%); }
.uperm-icon-tile.uperm-enabled:not(.checked) .uperm-icon-tile-label { color:#aab2bd; }
.uperm-icon-glyph { width:40px; height:40px; border-radius:50%; color:#fff; display:flex; align-items:center; justify-content:center; font-size:18px; box-shadow:0 1px 3px rgba(0,0,0,.25); transition:opacity .15s, filter .15s; }
.uperm-icon-tile-label { font-size:11px; color:#465365; line-height:1.25; }
.uperm-icon-tile.checked .uperm-icon-tile-label { font-weight:600; }
.uperm-icon-tile-check { position:absolute; top:-3px; right:14px; width:16px; height:16px; cursor:inherit; }

.uperm-save-row { margin-top:18px; text-align:right; }
.uperm-save-btn { background:#166534; color:#fff; border:none; border-radius:18px; padding:8px 26px; font-size:13px; font-weight:600; cursor:pointer; }
.uperm-save-btn:hover { background:#0f4c26; }
.uperm-save-btn:disabled { background:#b7bfc9; cursor:not-allowed; }
.uperm-save-status { font-size:12px; color:#166534; margin-right:12px; }

/* ── Add User popup ── */
.uperm-adduser-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9998; }
.uperm-adduser-popup { display:none; position:fixed; z-index:9999; left:50%; top:50%; transform:translate(-50%,-50%);
  width:420px; max-width:92vw; background:#fff; border-radius:8px; box-shadow:0 10px 40px rgba(0,0,0,.3); overflow:hidden; }
.uperm-adduser-hdr { background:#3a3f47; color:#fff; padding:12px 18px; font-size:14px; font-weight:700; display:flex; justify-content:space-between; align-items:center; }
.uperm-adduser-close { background:none; border:none; color:#fff; font-size:18px; cursor:pointer; opacity:.85; }
.uperm-adduser-body { padding:18px; }
.uperm-adduser-field { margin-bottom:12px; }
.uperm-adduser-field label { display:block; font-size:11px; font-weight:600; color:#465365; margin-bottom:4px; }
.uperm-adduser-field input, .uperm-adduser-field select { width:100%; height:32px; border:1px solid #c5ccd4; border-radius:4px; padding:2px 8px; font-size:12px; box-sizing:border-box; }
.uperm-adduser-footer { padding:12px 18px 16px; text-align:right; border-top:1px solid #eee; }
.uperm-adduser-cancel { background:#f0f4f8; border:1px solid #c5ccd4; border-radius:16px; padding:6px 18px; font-size:12px; color:#465365; cursor:pointer; margin-right:8px; }
.uperm-adduser-save { background:#8e24aa; color:#fff; border:none; border-radius:16px; padding:6px 22px; font-size:12px; font-weight:600; cursor:pointer; }
</style>

<div class="uperm-wrap">

    <div class="uperm-top-row">
        <button type="button" class="uperm-add-user-btn" id="uperm-add-user-open">+ Add User</button>
    </div>

    <div class="uperm-select-row">
        <div class="uperm-select-field">
            <label>User</label>
            <select id="uperm-user-select">
                <option value="">-- Select User --</option>
            </select>
        </div>
        <div class="uperm-select-field">
            <label>Designation</label>
            <input type="text" id="uperm-designation" readonly value="">
        </div>
        <div class="uperm-select-field">
            <label>Project</label>
            <select id="uperm-project-select">
                <option value="">All Projects</option>
            </select>
        </div>
    </div>

    <div class="uperm-icons-row" id="uperm-icons-row">
        <div class="uperm-icons-hdr">Icons</div>
        <div class="uperm-icons-sub" id="uperm-icons-sub">Select a user above to grant or remove access &mdash; icons shown here are all available icons.</div>
        <div class="uperm-icons-list" id="uperm-icons-list"></div>
        <div class="uperm-save-row">
            <span class="uperm-save-status" id="uperm-save-status"></span>
            <button type="button" class="uperm-save-btn" id="uperm-save-btn" disabled>Save</button>
        </div>
    </div>

</div>

<!-- Add User popup -->
<div class="uperm-adduser-overlay" id="uperm-adduser-overlay"></div>
<div class="uperm-adduser-popup" id="uperm-adduser-popup">
    <div class="uperm-adduser-hdr">
        <span>Add User</span>
        <button type="button" class="uperm-adduser-close" id="uperm-adduser-close">&times;</button>
    </div>
    <div class="uperm-adduser-body">
        <div class="uperm-adduser-field"><label>Username</label><input type="text" id="uperm-nu-username"></div>
        <div class="uperm-adduser-field"><label>Password</label><input type="password" id="uperm-nu-password"></div>
        <div class="uperm-adduser-field"><label>Email (optional)</label><input type="email" id="uperm-nu-email"></div>
        <div class="uperm-adduser-field">
            <label>Designation</label>
            <select id="uperm-nu-role"><option value="">-- Select Designation --</option></select>
        </div>
    </div>
    <div class="uperm-adduser-footer">
        <button type="button" class="uperm-adduser-cancel" id="uperm-adduser-cancel">Cancel</button>
        <button type="button" class="uperm-adduser-save" id="uperm-adduser-save">Create User</button>
    </div>
</div>

<script>
(function(){
    var _icons = [];
    var _usersById = {};
    var _grantedIconKeys = [];
    var _userSelected = false;

    // Same per-icon colors as the real top-bar buttons in main.php's
    // .round-icons rules, so a tile here looks like its real counterpart.
    var ICON_COLORS = {
        'icon-chart3':      '#003580', // Gantt Chart
        'rel-nav-btn':      '#5c3d8f', // Activity Relationships
        'estreport-btn':    '#37475a', // Project Estimate
        'overNow4':         '#CC0000', // Activity Library
        'reslib-btn':       '#555555', // Resource Library
        'overNowVendorLib': '#795548', // Vendor Library
        'procurementlib-btn': '#00695C', // Procurement
        'storeofficelib-btn': '#2c4a4a', // Site Office
        'perf-dashboard-btn': '#2e7d32', // KPI
        'cost-dashboard-btn': '#7b1fa2', // Cost Dashboard
        'qe-btn':           '#00838f', // WBS
        'pdoc-btn':         '#1565C0', // Project Documents
        'prjcet-dashboard': '#455a64'  // Dashboard (no dedicated color in main.php, sensible default)
    };

    function escHtml(s){
        return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function init(){
        $.ajax({
            type: 'POST', url: '../userpermissions/initdata', dataType: 'json',
            success: function(data){
                if (data.error !== 'No') return;
                _icons = data.icons;
                renderIcons(); // show icons immediately, before any user is picked

                var uOpts = '<option value="">-- Select User --</option>';
                $.each(data.users, function(i, u){
                    _usersById[u.id] = u;
                    uOpts += '<option value="' + u.id + '">' + escHtml(u.username) + '</option>';
                });
                $('#uperm-user-select').html(uOpts);

                var rOpts = '<option value="">-- Select Designation --</option>';
                $.each(data.roles, function(i, r){
                    rOpts += '<option value="' + r.role_id + '">' + escHtml(r.role_name) + '</option>';
                });
                $('#uperm-nu-role').html(rOpts);
            }
        });
    }

    $(document).on('change', '#uperm-user-select', function(){
        var userId = $(this).val();
        $('#uperm-save-status').text('');
        if (!userId){
            _userSelected = false;
            _grantedIconKeys = [];
            $('#uperm-designation').val('');
            $('#uperm-project-select').html('<option value="">All Projects</option>');
            $('#uperm-icons-sub').text('Select a user above to grant or remove access — icons shown here are all available icons.');
            $('#uperm-save-btn').prop('disabled', true);
            renderIcons();
            return;
        }
        var u = _usersById[userId];
        _userSelected = true;
        $('#uperm-designation').val(u ? u.role_name : '');
        $('#uperm-icons-sub').text('Editing icon access for ' + (u ? u.username : 'this user') + '.');
        $('#uperm-save-btn').prop('disabled', false);

        $.ajax({
            type: 'POST', url: '../userpermissions/usercontext', dataType: 'json',
            data: { user_id: userId },
            success: function(data){
                if (data.error !== 'No') return;
                var pOpts = '<option value="">All Projects</option>';
                $.each(data.projects, function(i, p){
                    pOpts += '<option value="' + p.Project_Id + '">' + escHtml(p.Name) + '</option>';
                });
                $('#uperm-project-select').html(pOpts);
                loadIconAccess();
            }
        });
    });

    $(document).on('change', '#uperm-project-select', function(){
        if ($('#uperm-user-select').val()) loadIconAccess();
    });

    function loadIconAccess(){
        var userId = $('#uperm-user-select').val();
        var projectId = $('#uperm-project-select').val();
        if (!userId) return;

        $.ajax({
            type: 'POST', url: '../userpermissions/iconaccess', dataType: 'json',
            data: { user_id: userId, project_id: projectId },
            success: function(data){
                if (data.error !== 'No') return;
                _grantedIconKeys = data.granted_icon_keys;
                renderIcons();
            }
        });
    }

    function renderIcons(){
        var html = '';
        $.each(_icons, function(i, icon){
            var checked = _userSelected && _grantedIconKeys.indexOf(icon.icon_key) !== -1;
            var color = ICON_COLORS[icon.icon_key] || '#7a8494';
            html += '<label class="uperm-icon-tile' + (checked ? ' checked' : '') + (_userSelected ? ' uperm-enabled' : '') + '" data-icon-key="' + escHtml(icon.icon_key) + '">'
                + '<input type="checkbox" class="uperm-icon-tile-check" value="' + escHtml(icon.icon_key) + '"' + (checked ? ' checked' : '') + (_userSelected ? '' : ' disabled') + '>'
                + '<span class="uperm-icon-glyph" style="background:' + color + ';"><span class="' + escHtml(icon.icon_class) + '"></span></span>'
                + '<span class="uperm-icon-tile-label">' + escHtml(icon.label) + '</span>'
                + '</label>';
        });
        $('#uperm-icons-list').html(html);
    }

    $(document).on('change', '.uperm-icon-tile-check', function(){
        if (!_userSelected) return;
        $(this).closest('.uperm-icon-tile').toggleClass('checked', this.checked);
    });

    $(document).on('click', '#uperm-save-btn', function(){
        var userId = $('#uperm-user-select').val();
        if (!userId) return;
        var projectId = $('#uperm-project-select').val();
        var iconKeys = [];
        $('#uperm-icons-list input:checked').each(function(){ iconKeys.push($(this).val()); });

        $.ajax({
            type: 'POST', url: '../userpermissions/saveiconaccess', dataType: 'json',
            data: { user_id: userId, project_id: projectId, icon_keys: iconKeys },
            success: function(data){
                if (data.error !== 'No'){ alert(data.errortext || 'Failed to save.'); return; }
                $('#uperm-save-status').text('Saved.');
                setTimeout(function(){ $('#uperm-save-status').text(''); }, 2000);
            },
            error: function(){ alert('Failed to save. Please try again.'); }
        });
    });

    // Add User popup
    $(document).on('click', '#uperm-add-user-open', function(){
        $('#uperm-nu-username, #uperm-nu-password, #uperm-nu-email').val('');
        $('#uperm-nu-role').val('');
        $('#uperm-adduser-overlay, #uperm-adduser-popup').show();
    });
    $(document).on('click', '#uperm-adduser-close, #uperm-adduser-cancel, #uperm-adduser-overlay', function(){
        $('#uperm-adduser-overlay, #uperm-adduser-popup').hide();
    });
    $(document).on('click', '#uperm-adduser-save', function(){
        var username = $('#uperm-nu-username').val().trim();
        var password = $('#uperm-nu-password').val();
        var email = $('#uperm-nu-email').val().trim();
        var roleId = $('#uperm-nu-role').val();

        $.ajax({
            type: 'POST', url: '../userpermissions/adduser', dataType: 'json',
            data: { username: username, password: password, email: email, role_id: roleId },
            success: function(data){
                if (data.error !== 'No'){ alert(data.errortext || 'Failed to create user.'); return; }
                $('#uperm-adduser-overlay, #uperm-adduser-popup').hide();
                init();
            },
            error: function(){ alert('Failed to create user. Please try again.'); }
        });
    });

    init();
})();
</script>
