$(document).on( "click", ".viewworkgroups", function(){
    //$('.acc_container').slideUp();
    $('#project').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#workgroup').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#dispprojectname').html(getProjectname(id));
    $('#selectedProjectId').val(id);
    $('#listworkgroup').trigger('click') ;
});
$(document).on( "click", "#workgroup", function(){
    if($('#selectedProjectId').val()!=''){
        //$('.acc_container').slideUp();
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown();
        $('#workgroup').addClass('active').next('.acc_container').slideDown();
        $('#dispprojectname').html(getProjectname($('#selectedProjectId').val()));
        $('#selectedProjectId').val($('#selectedProjectId').val());
        $('#listworkgroup').trigger('click') ;
    }
});

$(function(){

    // workgroup section function
    // list workgroup click
    $('#listworkgroup').click(function(){
        $('#workgroupaddsection').slideUp('slow');// slide down the project listing div
        $('#workgrouplistsection').slideDown('slow');// slide down the project listing div
        $('#listworkgroup').removeClass('btn-danger').addClass('btn-success');
        $('#addworkgroup').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../workgroups/search',
            beforeSend : function(){
                $('#workgroupsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val(),workgroupname:''},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workgroupitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }

                $('#workgroupsearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });

    });
    // list workgroup click  \
    // add workgroup click
    $('#addworkgroup').click(function(){
        $('#workgrouplistsection').slideUp('slow');// slide down the project listing div
        $('#workgroupaddsection').slideDown('slow');// slide down the project listing div
        $('#addworkgroup').removeClass('btn-danger').addClass('btn-success');
        $('#listworkgroup').removeClass('btn-success').addClass('btn-danger');

    });
    // add workgroup click

    // save workgroup click
    //workgroup function ends here

    $('#saveworkgroup').click(function(){
        var error=0;
        if($('#workgroupname').val()=='')
        {
            $("#workgroupname").next("span").html('Enter Work Group Name').show('slow');
            error=1;
        }
        if($('#workgroupname').val()!='' && WorkGroupNameExists($('#workgroupname').val(),$('#selectedProjectId').val())=='Yes')
        {
            $('#workgroupname').next("span").html('Work Group Name Exists').show('slow')
            error=1;
        }
        if($('#worktypegroups').val()=='none')
        {
            $("#worktypegroups").next("span").html('Select Workgroup').show('slow');
            error=1;
        }
        if($('#worktype').val()=='none')
        {
            $("#worktype").next("span").html('Select Worktype').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../workgroups/create',
                beforeSend : function(){
                    $('#saveworkgroup').attr("disabled", true);
                },
                dataType: "json",
                data: {Project_Id:$('#selectedProjectId').val(),workgroupname:$('#workgroupname').val(),worktype:$('#worktype').val(),worktypegroup:$('#worktypegroups').val()},
                success: function(data){

                    if(data.error=='No')
                    {
                        $('#addworkgroupform')[0].reset();
                        $('#listworkgroup').trigger('click');

                        /*$('#workgroupaddsection').slideUp('slow');// slide down the project listing div
                        $('#workgrouplistsection').slideDown('slow');// slide down the project listing div

                        $('#listworkgroup').removeClass('btn-danger').addClass('btn-success');
                        $('#addworkgroup').removeClass('btn-success').addClass('btn-danger');

                        $('#addworkgroup').trigger('click');
                        $('#workgroupsearch').trigger('click')
                        */
                    }
                    else
                    {
                        $("#workgroupname").next("span").html(data.errortext).show('slow');
                        $('#saveworkgroup').attr("disabled", false);
                    }
                    $('#saveworkgroup').attr("disabled", false);
                }
            });
        }
    });

    $( "#workgroupitems" ).sortable({
        items: '.no',
        update:function( event, ui ) {
            //alert($(this).index());
            var updatedrows=[];
            $(this).closest('table').find('tbody tr').each(function (i) {
                var rowid=$(this).attr('data-id');
                var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    rowindex:rowindex
                })
            });
            $.ajax({
                type: 'POST',
                url: '../workgroups/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection()
});


$(document).on('click','.editworkgroupbutton',function(){
    var idval=$(this).val();
    $('#workgroupname'+idval).hide();
    $('#worktypename'+idval).hide();
    $('#editworkgroupbutton'+idval).hide();
    $('#editworkgroupname'+idval).show();
    $('#editworktype'+idval).show();
    //$('#editworkgroupname'+idval).focus();
    $('#saveworkgroupbutton'+idval).show();
});


$(document).on('click','.saveworkgroupbutton',function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editworkgroupname'+idval).val()=='')
    {
        $('#editworkgroupname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if($('#editworktype'+idval).val()=='none')
    {
        $("#editworktype"+idval).next("span").html('Select Worktype').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../workgroups/update',
            beforeSend : function(){
                $('#saveworkgroupbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {workid:idval,name:$('#editworkgroupname'+idval).val(),worktype:$('#editworktype'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workgroupname'+data.Id).show();
                    $('#worktypename'+data.Id).show();
                    $('#editworkgroupbutton'+data.Id).show();
                    $('#editworkgroupname'+data.Id).hide();
                    $('#editworktype'+data.Id).hide();
                    $('#saveworkgroupbutton'+data.Id).hide();
                    $('#editworkgroupname'+data.Id).val(data.Name);
                    $('#workgroupname'+data.Id).text(data.Name);
                    $('#listworkgroup').trigger('click');

                }
                else
                {
                    alert(data.errortext);
                }

                $('#saveworkgroupbutton'+data.Id).attr("disabled", false);
            }
        });
    }
});



$(document).on('click','.deleteworkgroupbutton',function(){
    var workid=$(this).val();
    var r = confirm("Are you sure you want to delete thi Work Group?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../workgroups/deleteworkgroup',
            beforeSend : function(){
                $('#deleteworkgroupbutton'+workid).attr("disabled", true);
            },
            dataType: "json",
            data: {workid:workid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workgrouprow'+data.Id).remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deleteworkgroupbutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

$(document).on('change','#worktypegroups',function(){
    var workgp=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../workgroups/getworktypes',
        dataType: "json",
        data: {workgp:workgp},
        success: function(data){
            if(data.error=='No')
            {
                $('#worktype').html(data.result);
            }
        }
    });
});

function getProjectname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/Getname',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}

function WorkGroupNameExists(name,project)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../workgroups/checkname',
        async:false,
        data: {name:name,project:project},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}
