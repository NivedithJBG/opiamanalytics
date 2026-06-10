$(function(){

    // ── List / Search ──────────────────────────────────────────────────────
    $('#listvendorslib').on('click', function(){
        var typeId = $('#search-restype').val();
        // Only load if a resource type is selected
        if (!typeId || typeId === '0') {
            $('#vendorliblistsection').html('');
            $('#vendor-list-body').html('<tr><td colspan="8" style="text-align:center;padding:30px;color:#aaa;font-size:13px;border:1px solid #eee;">Select a Resource Type to view vendors</td></tr>');
            return;
        }
        $('.add-vendor-form').hide();
        $('#vendor-search-bar').show();
        $('#vendorliblistsection').show();
        $.ajax({
            type: 'POST',
            url: '../vendorlibrary/vendors',
            dataType: 'json',
            data: {
                vendorname:  $('#searchvendorlibname').val(),
                res_type_id: typeId,
                res_id:      $('#search-resid').val()
            },
            beforeSend: function(){ $('.preloader-vendorlib').show(); },
            success: function(data){
                $('.preloader-vendorlib').hide();
                if(data.error === 'No'){
                    $('#vendor-list-body').html(data.result);
                } else {
                    alert(data.errortext);
                }
            }
        });
    });

    $('#vendorlibsearch').on('click', function(){
        $('#listvendorslib').trigger('click');
    });

    // ── Search Resource Type cascade ───────────────────────────────────────
    $(document).on('change', '#search-restype', function(){
        var typeId = $(this).val();
        var $res   = $('#search-resid');
        if (!typeId || typeId === '0') {
            $res.html('<option value="0">-- Resource --</option>').prop('disabled', true);
            $('#listvendorslib').trigger('click');
            return;
        }
        $res.html('<option value="0">Loading...</option>').prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: '../vendorlibrary/getresources',
            dataType: 'json',
            data: { resource_type_id: typeId },
            success: function(data){
                $res.prop('disabled', false);
                var opts = '<option value="0">-- Resource --</option>';
                if(data.error === 'No' && data.resources.length){
                    $.each(data.resources, function(i, r){
                        opts += '<option value="' + r.Resource_Id + '">' + r.Name + '</option>';
                    });
                }
                $res.html(opts);
                $('#listvendorslib').trigger('click');
            }
        });
    });

    $(document).on('change', '#search-resid', function(){
        $('#listvendorslib').trigger('click');
    });

    $(document).on('input', '#searchvendorlibname', function(){
        clearTimeout(window._vendorLibTimer);
        window._vendorLibTimer = setTimeout(function(){
            $('#listvendorslib').trigger('click');
        }, 300);
    });

    // ── Form Resource Type cascade ─────────────────────────────────────────
    $(document).on('change', '#vlf-restype', function(){
        var typeId = $(this).val();
        var $res   = $('#vlf-resid');
        $res.html('<option value="0">Loading...</option>').prop('disabled', true);
        if (!typeId || typeId === '0') {
            $res.html('<option value="0">-- Select Resource Type first --</option>').prop('disabled', false);
            return;
        }
        $.ajax({
            type: 'POST',
            url: '../vendorlibrary/getresources',
            dataType: 'json',
            data: { resource_type_id: typeId },
            success: function(data){
                $res.prop('disabled', false);
                if(data.error === 'No' && data.resources.length){
                    var opts = '<option value="0">-- Select Resource --</option>';
                    $.each(data.resources, function(i, r){
                        opts += '<option value="' + r.Resource_Id + '">' + r.Name + '</option>';
                    });
                    $res.html(opts);
                } else {
                    $res.html('<option value="0">No resources found</option>');
                }
            }
        });
    });

    // ── Add form ───────────────────────────────────────────────────────────
    $(document).on('click', '#addvendorlibbtn', function(){
        resetVendorForm();
        $('#vlf-save').text('Add Vendor');
        $('#vendorlib-heading').text('ADD Vendor');
        $('#vendor-search-bar').hide();
        $('#vendorliblistsection').hide();
        $('.add-vendor-form').slideDown('fast');
    });

    $(document).on('click', '#vlf-cancel', function(){
        $('.add-vendor-form').slideUp('fast');
        resetVendorForm();
        $('#vendorlib-heading').text('VENDOR');
        $('#vendor-search-bar').show();
        $('#listvendorslib').trigger('click');
    });

    $(document).on('click', '#vlf-save', function(){
        var name  = $('#vlf-name').val().trim();
        var phone = $('#vlf-phone').val().trim();
        var email = $('#vlf-email').val().trim();
        $('.error').hide();

        var valid = true;
        if(name === ''){
            $('#vlf-name').next('.error').text('Vendor name is required.').show();
            valid = false;
        }
        if(phone !== '' && !/^[0-9+\-\s\(\)]+$/.test(phone)){
            $('#vlf-phone-error').text('Phone must contain numbers only.').show();
            valid = false;
        }
        if(email !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
            $('#vlf-email-error').text('Enter a valid email address.').show();
            valid = false;
        }
        if(!valid) return;
        var id  = $('#vlf-id').val();
        var url = id ? '../vendorlibrary/updatevendor' : '../vendorlibrary/createvendor';

        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data: {
                vid:      id,
                vname:    name,
                vaddress: $('#vlf-address').val().trim(),
                vcontact: $('#vlf-contact').val().trim(),
                vphone:   $('#vlf-phone').val().trim(),
                vemail:   $('#vlf-email').val().trim(),
                vrestype: $('#vlf-restype').val(),
                vresid:   $('#vlf-resid').val()
            },
            beforeSend: function(){ $('#vlf-save').attr('disabled', true); },
            success: function(data){
                $('#vlf-save').attr('disabled', false);
                if(data.error === 'No'){
                    $('.add-vendor-form').slideUp('fast');
                    resetVendorForm();
                    $('#vendorlib-heading').text('VENDOR');
                    $('#vendor-search-bar').show();
                    $('#vendorliblistsection').html('');
                } else {
                    alert(data.errortext || 'Error saving vendor.');
                }
            }
        });
    });

    // ── Edit (delegated) ───────────────────────────────────────────────────
    $(document).on('click', '.editvendorbutton', function(){
        var id        = $(this).data('id');
        var resTypeId = $('#vdata-restype-' + id).text();
        var resId     = $('#vdata-resid-'   + id).text();

        resetVendorForm();
        $('#vlf-id').val(id);
        $('#vlf-name').val($('#vdata-name-'    + id).text());
        $('#vlf-address').val($('#vdata-addr-' + id).text());
        $('#vlf-contact').val($('#vdata-contact-' + id).text());
        $('#vlf-phone').val($('#vdata-phone-'  + id).text());
        $('#vlf-email').val($('#vdata-email-'  + id).text());
        $('#vlf-restype').val(resTypeId);

        if(resTypeId && resTypeId !== '0'){
            var $res = $('#vlf-resid');
            $res.html('<option value="0">Loading...</option>').prop('disabled', true);
            $.ajax({
                type: 'POST',
                url: '../vendorlibrary/getresources',
                dataType: 'json',
                data: { resource_type_id: resTypeId },
                success: function(data){
                    $res.prop('disabled', false);
                    if(data.error === 'No' && data.resources.length){
                        var opts = '<option value="0">-- Select Resource --</option>';
                        $.each(data.resources, function(i, r){
                            opts += '<option value="' + r.Resource_Id + '">' + r.Name + '</option>';
                        });
                        $res.html(opts).val(resId);
                    } else {
                        $res.html('<option value="0">No resources found</option>');
                    }
                }
            });
        }

        $('#vlf-save').text('Save Changes');
        $('#vendorlib-heading').text('ADD Vendor');
        $('#vendor-search-bar').hide();
        $('#vendorliblistsection').hide();
        $('.add-vendor-form').slideDown('fast');
    });

    // ── Delete (delegated) ─────────────────────────────────────────────────
    $(document).on('click', '.delvendorbutton', function(){
        var id = $(this).data('id');
        if(!confirm('Are you sure you want to delete this vendor?')) return;
        $.ajax({
            type: 'POST',
            url: '../vendorlibrary/deletevendor',
            dataType: 'json',
            data: { vid: id },
            success: function(data){
                if(data.error === 'No'){
                    $('#vendorrow' + data.Id).remove();
                } else {
                    alert(data.errortext || 'Error deleting vendor.');
                }
            }
        });
    });

    function resetVendorForm(){
        $('#vlf-id').val('');
        $('#vlf-name, #vlf-address, #vlf-contact, #vlf-phone, #vlf-email').val('');
        $('#vlf-restype').val('0');
        $('#vlf-resid').html('<option value="0">-- Select Resource Type first --</option>');
        $('.error').hide().text('');
    }

});
