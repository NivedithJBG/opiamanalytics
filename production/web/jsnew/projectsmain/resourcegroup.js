$(function(){

    // ------ List / Search ------
    $('#listresgroup').on('click', function(){
        $('.add-resource-group-form').hide();
        $('.edit-resource-group-form').hide();
        $('#reslib-group .search-and-actions-wrpr').show();
        $('#resgrouplistsection').show();
        $.ajax({
            type: 'POST',
            url: '../resourcegroup/search',
            dataType: 'json',
            data: { resgroupname: $('#searchresgroupname').val() },
            beforeSend: function(){ $('.preloader-group').show(); },
            success: function(data){
                $('.preloader-group').hide();
                if(data.error == 'No'){
                    $('#resgrouplistsection').html(data.resgroup);
                } else {
                    alert(data.errortext);
                }
            }
        });
    });

    $('#resgroupsearch').on('click', function(){
        $('#listresgroup').trigger('click');
    });

    // ------ Add form ------
    $('#addresgroup').on('click', function(){
        $('.edit-resource-group-form').hide();
        $('#reslib-group .search-and-actions-wrpr').hide();
        $('#resgrouplistsection').hide();
        $('#addresgroupform')[0].reset();
        $('.error').hide();
        $('.add-resource-group-form').slideDown('fast');
    });

    $('#cancelresgroup').on('click', function(){
        $('.add-resource-group-form').slideUp('fast');
        $('#reslib-group .search-and-actions-wrpr').show();
        $('#resgrouplistsection').show();
        $('#listresgroup').trigger('click');
    });

    $('#saveresgroup').on('click', function(){
        var name = $('#resgroupname1').val().trim();
        $('.error').hide();
        if(name === ''){
            $('#resgroupname1').next('.error').text('Enter Resource Group Name').show();
            return;
        }
        $.ajax({
            type: 'POST',
            url: '../resourcegroup/create',
            dataType: 'json',
            data: { resourcegroup: name, restypeid: $('#addresgrouptype').val() },
            beforeSend: function(){ $('#saveresgroup').attr('disabled', true); },
            success: function(data){
                $('#saveresgroup').attr('disabled', false);
                if(data.error == 'No'){
                    $('.add-resource-group-form').slideUp('fast');
                    $('#addresgroupform')[0].reset();
                    $('#reslib-group .search-and-actions-wrpr').show();
                    $('#resgrouplistsection').show();
                    $('#listresgroup').trigger('click');
                } else {
                    alert(data.errortext);
                }
            }
        });
    });

    // ------ Edit form (handler moved to global openEditResGroup) ------

    $('#cancelresgroups').on('click', function(){
        $('.edit-resource-group-form').hide();
        $('#reslib-group .search-and-actions-wrpr').show();
        $('#resgrouplistsection').show();
        $('#listresgroup').trigger('click');
    });

    $('#saveresgroupbutton').on('click', function(){
        var id = $('#saveresgroupval').val();
        var name = $('#resgroupnames').val().trim();
        if(name === ''){
            $('#resgroupnames').next('.error').text('Enter Resource Group Name').show();
            return;
        }
        $.ajax({
            type: 'POST',
            url: '../resourcegroup/update',
            dataType: 'json',
            data: { resgrpid: id, name: name, restypeid: $('#editresgrouptype').val() },
            beforeSend: function(){ $('#saveresgroupbutton').attr('disabled', true); },
            success: function(data){
                $('#saveresgroupbutton').attr('disabled', false);
                if(data.error == 'No'){
                    $('.edit-resource-group-form').hide();
                    $('#reslib-group .search-and-actions-wrpr').show();
                    $('#resgrouplistsection').show();
                    $('#listresgroup').trigger('click');
                } else {
                    alert(data.errortext);
                }
            }
        });
    });

    // ------ Delete ------
    $(document).on('click', '.deletresourcegroupbutton', function(e){
        e.stopPropagation();
        var id = $(this).closest('.deletresourcegroupbutton').val();
        if(confirm('Are you sure you want to delete this Resource Group?')){
            $.ajax({
                type: 'POST',
                url: '../resourcegroup/deleteitem',
                dataType: 'json',
                data: { restypeid: id },
                success: function(data){
                    if(data.error == 'No'){
                        $('#resourcegrouprow' + data.Id).remove();
                    } else {
                        alert(data.errortext);
                    }
                }
            });
        }
    });

});

function openEditResGroup(btn) {
    var $btn   = $(btn);
    var id     = $btn.data('id');
    var name   = $btn.data('name');
    var typeId = $btn.data('typeid');

    $('#resgroupnames').val(name);
    $('#saveresgroupval').val(id);
    $('#editresgrouptype').val(typeId);

    $('.add-resource-group-form').hide();
    $('#reslib-group .search-and-actions-wrpr').hide();
    $('#resgrouplistsection').hide();
    $('.edit-resource-group-form').show();
}

function makeSortableGroup(){
    $('#resgrouplistsection').sortable({
        items: '.resgroupsort',
        update: function(event, ui){
            var updatedrows = [];
            $(this).find('.resgroupsort').each(function(i){
                updatedrows.push({ rowid: $(this).data('id'), rowindex: i });
            });
            $.ajax({
                type: 'POST',
                url: '../resourcegroup/updatesort',
                data: { datavalue: updatedrows },
                dataType: 'json'
            });
        }
    }).disableSelection();
}
