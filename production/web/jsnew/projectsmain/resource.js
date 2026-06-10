$(function(){

    // ------ List / Search ------
    $('#listresource').on('click', function(){
        $('.add-resource-form').hide();
        $('.edit-resource-form').hide();
        $('#reslib-res .search-and-actions-wrpr').show();
        $('#resourcelistsection').show();
        $.ajax({
            type: 'POST',
            url: '../resources/search',
            dataType: 'json',
            data: { restypeid: $('#searchresourcetype').val() },
            beforeSend: function(){ $('.preloader-resource').show(); },
            success: function(data){
                $('.preloader-resource').hide();
                if(data.error == 'No'){
                    $('#resourcelistsection').html(data.resource);
                } else {
                    alert(data.errortext);
                }
            }
        });
    });

    $(document).on('change', '#searchresourcetype', function(){
        $('#listresource').trigger('click');
    });

    // ------ Cascading: Resource Type → Resource Group ------
    function loadGroupsForType(typeSelectId, groupSelectId){
        var typeId = $(typeSelectId).val();
        var $grp   = $(groupSelectId);
        $grp.html('<option value="0">-- Select Group --</option>');
        if(!typeId || typeId == '0') return;
        $.ajax({
            type: 'POST',
            url: '../resourcegroup/getbytype',
            dataType: 'json',
            data: { restypeid: typeId },
            success: function(data){
                if(data.error == 'No'){
                    $.each(data.groups, function(i, g){
                        $grp.append('<option value="' + g.Resource_group_Id + '">' + g.Resource_group_Name + '</option>');
                    });
                }
            }
        });
    }

    $(document).on('change', '#addresourcetype', function(){
        loadGroupsForType('#addresourcetype', '#addresourcegroup');
    });

    $(document).on('change', '#editresourcetype', function(){
        loadGroupsForType('#editresourcetype', '#editresourcegroup');
    });

    // ------ Add form ------
    $('#addresource').on('click', function(){
        $('.edit-resource-form').hide();
        $('#reslib-res .search-and-actions-wrpr').hide();
        $('#resourcelistsection').hide();
        $('#addresourceform')[0].reset();
        $('#resourceunit1').val('');
        $('#addresourcegroup').html('<option value="0">-- Select Group --</option>');
        $('.error').hide();
        $('.add-resource-form').slideDown('fast');
    });

    $('#cancelresource').on('click', function(){
        $('.add-resource-form').slideUp('fast');
        $('#reslib-res .search-and-actions-wrpr').show();
        $('#resourcelistsection').show();
        $('#listresource').trigger('click');
    });

    $('#saveresource').on('click', function(){
        var name = $('#resourcename1').val().trim();
        $('.error').hide();
        if(name === ''){
            $('#resourcename1').next('.error').text('Enter Resource Name').show();
            return;
        }
        $.ajax({
            type: 'POST',
            url: '../resources/create',
            dataType: 'json',
            data: {
                resourcename: name,
                unit:         $('#resourceunit1').val().trim(),
                restypeid:    $('#addresourcetype').val(),
                resgroupid:   $('#addresourcegroup').val()
            },
            beforeSend: function(){ $('#saveresource').attr('disabled', true); },
            success: function(data){
                $('#saveresource').attr('disabled', false);
                if(data.error == 'No'){
                    $('.add-resource-form').slideUp('fast');
                    $('#addresourceform')[0].reset();
                    $('#reslib-res .search-and-actions-wrpr').show();
                    $('#resourcelistsection').show();
                    $('#listresource').trigger('click');
                } else {
                    alert(data.errortext);
                }
            }
        });
    });

    // ------ Edit form (handler moved to global openEditResource) ------

    $('#cancelresources').on('click', function(){
        $('.edit-resource-form').hide();
        $('#reslib-res .search-and-actions-wrpr').show();
        $('#resourcelistsection').show();
        $('#listresource').trigger('click');
    });

    $('#saveresourcebutton').on('click', function(){
        var name = $('#editresourcename').val().trim();
        if(name === ''){
            $('#editresourcename').next('.error').text('Enter Resource Name').show();
            return;
        }
        $.ajax({
            type: 'POST',
            url: '../resources/update',
            dataType: 'json',
            data: {
                resourceid: $('#saveresourceval').val(),
                name:       name,
                unit:       $('#editresourceunit').val().trim(),
                restypeid:  $('#editresourcetype').val(),
                resgroupid: $('#editresourcegroup').val()
            },
            beforeSend: function(){ $('#saveresourcebutton').attr('disabled', true); },
            success: function(data){
                $('#saveresourcebutton').attr('disabled', false);
                if(data.error == 'No'){
                    $('.edit-resource-form').hide();
                    $('#reslib-res .search-and-actions-wrpr').show();
                    $('#resourcelistsection').show();
                    $('#listresource').trigger('click');
                } else {
                    alert(data.errortext);
                }
            }
        });
    });

    // ------ Delete ------
    $(document).on('click', '.deleteresourcebutton', function(e){
        e.stopPropagation();
        var id = $(this).closest('.deleteresourcebutton').val();
        if(confirm('Are you sure you want to delete this Resource?')){
            $.ajax({
                type: 'POST',
                url: '../resources/deleteitem',
                dataType: 'json',
                data: { resourceid: id },
                success: function(data){
                    if(data.error == 'No'){
                        $('#resourcerow' + data.Id).remove();
                    } else {
                        alert(data.errortext);
                    }
                }
            });
        }
    });

});

function openEditResource(btn) {
    var $btn    = $(btn);
    var id      = $btn.data('id');
    var name    = $btn.data('name');
    var unit    = $btn.data('unit') || '';
    var typeId  = $btn.data('typeid');
    var groupId = $btn.data('groupid');

    $('#editresourcename').val(name);
    $('#editresourceunit').val(unit);
    $('#saveresourceval').val(id);
    $('#editresourcetype').val(typeId);

    $('#editresourcegroup').html('<option value="0">-- Select Group --</option>');
    if(typeId && typeId != '0'){
        $.ajax({
            type: 'POST',
            url: '../resourcegroup/getbytype',
            dataType: 'json',
            data: { restypeid: typeId },
            success: function(data){
                if(data.error == 'No'){
                    $.each(data.groups, function(i, g){
                        $('#editresourcegroup').append('<option value="' + g.Resource_group_Id + '">' + g.Resource_group_Name + '</option>');
                    });
                    $('#editresourcegroup').val(groupId);
                }
            }
        });
    }

    $('.add-resource-form').hide();
    $('#reslib-res .search-and-actions-wrpr').hide();
    $('#resourcelistsection').hide();
    $('.edit-resource-form').show();
}

function makeSortableResource(){
    $('#resourcelistsection').sortable({
        items: '.resourcesort',
        update: function(event, ui){
            var updatedrows = [];
            $(this).find('.resourcesort').each(function(i){
                updatedrows.push({ rowid: $(this).data('id'), rowindex: i });
            });
            $.ajax({
                type: 'POST',
                url: '../resources/updatesort',
                data: { datavalue: updatedrows },
                dataType: 'json'
            });
        }
    }).disableSelection();
}
