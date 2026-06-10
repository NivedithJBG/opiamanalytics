$(function(){

    // ------ List / Search ------
    $('#listrestype').on('click', function(){
        $('.add-resource-type-form').hide();
        $('.edit-resource-type-form').hide();
        $('#reslib-type .search-and-actions-wrpr').show();
        $('#restypelistsection').show();
        $.ajax({
            type: 'POST',
            url: '../resourcetype/search',
            dataType: 'json',
            data: { restypename: $('#searchrestypename').val() },
            beforeSend: function(){ $('.preloader').show(); },
            success: function(data){
                $('.preloader').hide();
                if(data.error == 'No'){
                    $('#restypelistsection').html(data.restype);
                    makeSortable();
                } else {
                    alert(data.errortext);
                }
            }
        });
    });

    $('#resourcetypesearch').on('click', function(){
        $('#listrestype').trigger('click');
    });

    // ------ Add form ------
    $('#addrestype').on('click', function(){
        $('.edit-resource-type-form').hide();
        $('#reslib-type .search-and-actions-wrpr').hide();
        $('#restypelistsection').hide();
        $('#addrestypeform')[0].reset();
        $('.error').hide();
        $('.add-resource-type-form').slideDown('fast');
    });

    $('#cancelrestype').on('click', function(){
        $('.add-resource-type-form').slideUp('fast');
        $('#reslib-type .search-and-actions-wrpr').show();
        $('#restypelistsection').show();
        $('#listrestype').trigger('click');
    });

    $('#saverestype').on('click', function(){
        var name = $('#restypename1').val().trim();
        $('.error').hide();
        if(name === ''){
            $('#restypename1').next('.error').text('Enter Resource Type Name').show();
            return;
        }
        $.ajax({
            type: 'POST',
            url: '../resourcetype/create',
            dataType: 'json',
            data: { restypename: name, addacntgrp: 1 },
            beforeSend: function(){ $('#saverestype').attr('disabled', true); },
            success: function(data){
                $('#saverestype').attr('disabled', false);
                if(data.error == 'No'){
                    $('.add-resource-type-form').slideUp('fast');
                    $('#addrestypeform')[0].reset();
                    $('#reslib-type .search-and-actions-wrpr').show();
                    $('#restypelistsection').show();
                    $('#listrestype').trigger('click');
                } else {
                    alert(data.errortext);
                }
            }
        });
    });

    // ------ Edit form (handler moved to global openEditResType) ------

    $('#cancelrestypes').on('click', function(){
        $('.edit-resource-type-form').hide();
        $('#reslib-type .search-and-actions-wrpr').show();
        $('#restypelistsection').show();
        $('#listrestype').trigger('click');
    });

    $('#saveresourcetypebutton').on('click', function(){
        var id = $('#saveresourcetypeval').val();
        var error = 0;
        if($('#restypenames').val() == ''){
            $('#restypenames').next('.error').html('Enter Resource Type Name').show();
            error = 1;
        }
        if(error == 0){
            $.ajax({
                type: 'POST',
                url: '../resourcetype/update',
                dataType: 'json',
                data: {
                    restypeid: id,
                    name: $('#restypenames').val(),
                    accntgrp: 1
                },
                beforeSend: function(){ $('#saveresourcetypebutton').attr('disabled', true); },
                success: function(data){
                    $('#saveresourcetypebutton').attr('disabled', false);
                    if(data.error == 'No'){
                        $('.edit-resource-type-form').hide();
                        $('#reslib-type .search-and-actions-wrpr').show();
                        $('#restypelistsection').show();
                        $('#listrestype').trigger('click');
                    } else {
                        alert(data.errortext);
                    }
                }
            });
        }
    });

    // ------ Delete ------
    $(document).on('click', '.deletresourcetypebutton', function(e){
        e.stopPropagation();
        var id = $(this).closest('.deletresourcetypebutton').data('id') || $(this).val();
        if(confirm('Are you sure you want to delete this Resource Type?')){
            $.ajax({
                type: 'POST',
                url: '../resourcetype/deleteitem',
                dataType: 'json',
                data: { restypeid: id },
                success: function(data){
                    if(data.error == 'No'){
                        $('#resourcetyperow' + data.Id).remove();
                    } else {
                        alert(data.errortext);
                    }
                }
            });
        }
    });

    // ------ Resource Groups child button → switch to Groups tab ------
    $(document).on('click', '.childresourcegroups', function(){
        var resTypeId = $(this).val();
        $('#reslibResTypeFilter').val(resTypeId);
        $('#reslib-group').collapse('show');
    });

});

function openEditResType(btn) {
    var id     = btn.getAttribute('data-id');
    var $tr    = jQuery(btn).closest('tr');
    var nameEl = document.getElementById('resordtypenames' + id);
    var name   = nameEl ? nameEl.value : '';

    // Turn the name cell into an inline input
    var $nameTd = $tr.find('td').eq(1);
    $nameTd.html('');
    var inp = document.createElement('input');
    inp.type      = 'text';
    inp.id        = 'restype-inline-' + id;
    inp.className = 'form-control input-sm';
    inp.style.cssText = 'height:26px;padding:2px 6px;margin:0;';
    inp.value = name;
    $nameTd[0].appendChild(inp);
    inp.focus();
    inp.select();

    // Swap action buttons to Save + Cancel
    $tr.find('td').last().html(
        '<button type="button" onclick="saveEditResType(' + id + ')" class="btn btn-success btn-xs" '
        + 'style="border-radius:50%;width:28px;height:28px;padding:0;margin-right:4px;background-color:#5cb85c!important;border-color:#4cae4c!important;color:#fff!important;">'
        + '<span class="icon-check"></span></button>'
        + '<button type="button" onclick="cancelEditResType()" class="btn btn-default btn-xs" '
        + 'style="border-radius:50%;width:28px;height:28px;padding:0;">'
        + '<span class="icon-close"></span></button>'
    );
}

function saveEditResType(id) {
    var inp     = document.getElementById('restype-inline-' + id);
    var newName = inp ? inp.value.trim() : '';
    if (!newName) { alert('Resource Type Name cannot be empty.'); return; }
    jQuery.ajax({
        type: 'POST',
        url: '../resourcetype/update',
        dataType: 'json',
        data: { restypeid: id, name: newName, accntgrp: 1 },
        success: function(data) {
            if (data.error == 'No') {
                document.getElementById('listrestype').click();
            } else {
                alert(data.errortext || 'Cannot update. Please try again.');
            }
        }
    });
}

function cancelEditResType() {
    document.getElementById('listrestype').click();
}

// Drag-to-reorder
function makeSortable(){
    $('#restypelistsection').sortable({
        items: '.restypesort',
        update: function(event, ui){
            var updatedrows = [];
            $(this).find('.restypesort').each(function(i){
                updatedrows.push({ rowid: $(this).data('id'), rowindex: i });
            });
            $.ajax({
                type: 'POST',
                url: '../resourcetype/updatesort',
                data: { datavalue: updatedrows },
                dataType: 'json'
            });
        }
    }).disableSelection();
}
