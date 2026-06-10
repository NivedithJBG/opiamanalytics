$(function(){

    // ------ List / Search ------
    $('#listvtypes').on('click', function(){
        $('.add-vtype-form').hide();
        $('#vendorlib-type .search-and-actions-wrpr').show();
        $('#vtypelistsection').show();
        $.ajax({
            type: 'POST',
            url: '../vendorlibrary/search',
            dataType: 'json',
            data: { vtypename: $('#searchvtypename').val() },
            beforeSend: function(){ $('.preloader-vtype').show(); },
            success: function(data){
                $('.preloader-vtype').hide();
                if(data.error == 'No'){
                    $('#vtypelistsection').html(data.result);
                } else {
                    alert(data.errortext);
                }
            }
        });
    });

    $(document).on('click', '#vtypesearch', function(){
        $('#listvtypes').trigger('click');
    });

    // ------ Add form ------
    $(document).on('click', '#addvtype', function(){
        $('#vendorlib-type .search-and-actions-wrpr').hide();
        $('#vtypelistsection').hide();
        $('#vtypename1').val('');
        $('.error').hide();
        $('.add-vtype-form').slideDown('fast');
        $('#vtypename1').focus();
    });

    $(document).on('click', '#cancelvtype', function(){
        $('.add-vtype-form').slideUp('fast');
        $('#vendorlib-type .search-and-actions-wrpr').show();
        $('#vtypelistsection').show();
        $('#listvtypes').trigger('click');
    });

    $(document).on('click', '#savevtype', function(){
        var name = $('#vtypename1').val().trim();
        $('.error').hide();
        if(name === ''){
            $('#vtypename1').next('.error').text('Enter Vendor Type Name').show();
            return;
        }
        $.ajax({
            type: 'POST',
            url: '../vendorlibrary/create',
            dataType: 'json',
            data: { vtypename: name },
            beforeSend: function(){ $('#savevtype').attr('disabled', true); },
            success: function(data){
                $('#savevtype').attr('disabled', false);
                if(data.error == 'No'){
                    $('.add-vtype-form').slideUp('fast');
                    $('#vtypename1').val('');
                    $('#vendorlib-type .search-and-actions-wrpr').show();
                    $('#vtypelistsection').show();
                    $('#listvtypes').trigger('click');
                } else {
                    alert(data.errortext);
                }
            }
        });
    });

    // ------ Delete ------
    $(document).on('click', '.delvtypebutton', function(e){
        e.stopPropagation();
        var id = $(this).data('id');
        if(confirm('Are you sure you want to delete this Vendor Type?')){
            $.ajax({
                type: 'POST',
                url: '../vendorlibrary/deleteitem',
                dataType: 'json',
                data: { vtypeid: id },
                success: function(data){
                    if(data.error == 'No'){
                        $('#vtrow' + data.Id).remove();
                    } else {
                        alert(data.errortext);
                    }
                }
            });
        }
    });

});

function openEditVendorType(btn) {
    var id     = btn.getAttribute('data-id');
    var $tr    = jQuery(btn).closest('tr');
    var nameEl = document.getElementById('vtname' + id);
    var name   = nameEl ? nameEl.value : '';

    var $nameTd = $tr.find('td').eq(1);
    $nameTd.html('');
    var inp = document.createElement('input');
    inp.type      = 'text';
    inp.id        = 'vtype-inline-' + id;
    inp.className = 'form-control input-sm';
    inp.style.cssText = 'height:26px;padding:2px 6px;margin:0;';
    inp.value = name;
    $nameTd[0].appendChild(inp);
    inp.focus();
    inp.select();

    $tr.find('td').last().html(
        '<button type="button" onclick="saveEditVendorType(' + id + ')" class="btn btn-success btn-xs" '
        + 'style="border-radius:50%;width:28px;height:28px;padding:0;margin-right:4px;background-color:#5cb85c!important;border-color:#4cae4c!important;color:#fff!important;">'
        + '<span class="icon-check"></span></button>'
        + '<button type="button" onclick="cancelEditVendorType()" class="btn btn-default btn-xs" '
        + 'style="border-radius:50%;width:28px;height:28px;padding:0;">'
        + '<span class="icon-close"></span></button>'
    );
}

function saveEditVendorType(id) {
    var inp     = document.getElementById('vtype-inline-' + id);
    var newName = inp ? inp.value.trim() : '';
    if (!newName) { alert('Vendor Type Name cannot be empty.'); return; }
    jQuery.ajax({
        type: 'POST',
        url: '../vendorlibrary/update',
        dataType: 'json',
        data: { vtypeid: id, name: newName },
        success: function(data) {
            if (data.error == 'No') {
                document.getElementById('listvtypes').click();
            } else {
                alert(data.errortext || 'Cannot update. Please try again.');
            }
        }
    });
}

function cancelEditVendorType() {
    document.getElementById('listvtypes').click();
}
