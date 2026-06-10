/**
 * Created by SolmindsDelli5 on 07-05-2018.
 */
$(document).on( "click", ".viewiowschedule", function(){
    $('#project').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#iowschedule').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#iowschprojectname').html(getProjectname(id));
    $('#selectedProjectId').val(id);
    $('#listiowschworkgroup').trigger('click') ;
});
$(document).on( "click", "#iowschedule", function(){
    if($('#selectedProjectId').val()!=''){
        //$('.acc_container').slideUp();
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown();
        $('#iowschedule').addClass('active').next('.acc_container').slideDown();
        $('#iowschprojectname').html(getProjectname($('#selectedProjectId').val()));
        //$('#selectedProjectId').val($('#selectedProjectId').val());
        $('#listiowschworkgroup').trigger('click') ;
    }
});
$(function(){
    $('#listiowschworkgroup').click(function(){
        $('#iowschworkgroupadd').slideUp('slow');// slide down the project listing div
        $('#iowschworkgrouplist').slideDown('slow');// slide down the project listing div
        $('#listiowschworkgroup').removeClass('btn-danger').addClass('btn-success');
        $('#addiowschworkgroup').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../workgroups/iowschsearch',
            beforeSend : function(){
                $('#iowschworkgroupsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val(),workgroupname:''},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowschworkgroupitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }

                $('#iowschworkgroupsearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });

    });
    $('#addiowschworkgroup').click(function(){
        $('#iowschworkgrouplist').slideUp('slow');// slide down the project listing div
        $('#iowschworkgroupadd').slideDown('slow');// slide down the project listing div
        $('#addiowschworkgroup').removeClass('btn-danger').addClass('btn-success');
        $('#listiowschworkgroup').removeClass('btn-success').addClass('btn-danger');

    });
    $('#saveiowschworkgroup').click(function(){
        var error=0;
        if($('#iowschworkgroupname').val()=='')
        {
            $("#iowschworkgroupname").next("span").html('Enter IOW Name').show('slow');
            error=1;
        }
        /*if($('#workgroupname').val()!='' && WorkGroupNameExists($('#workgroupname').val(),$('#selectedProjectId').val())=='Yes')
        {
            $('#workgroupname').next("span").html('Work Group Name Exists').show('slow')
            error=1;
        }*/

        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../workgroups/iowcreate',
                beforeSend : function(){
                    $('#saveiowschworkgroup').attr("disabled", true);
                },
                dataType: "json",
                data: {Project_Id:$('#selectedProjectId').val(),workgroupname:$('#iowschworkgroupname').val()},
                success: function(data){

                    if(data.error=='No')
                    {
                        $('#addiowschworkgroupform')[0].reset();
                        $('#listiowschworkgroup').trigger('click');
                    }
                    else
                    {
                        //$("#workgroupname").next("span").html(data.errortext).show('slow');
                        $('#saveiowschworkgroup').attr("disabled", false);
                    }
                    $('#saveiowschworkgroup').attr("disabled", false);
                }
            });
        }
    });
});
$(document).on('click','.editiowschwbsbutton',function(){
    var idval=$(this).val();
    $('#iowschwbsname'+idval).hide();
    $('#editiowschwbsbutton'+idval).hide();
    $('#editiowschwbsname'+idval).show();
    //$('#editworkgroupname'+idval).focus();
    $('#saveiowschwbsbutton'+idval).show();
});
$(document).on('click','.saveiowschwbsbutton',function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editiowschwbsname'+idval).val()=='')
    {
        $('#editiowschwbsname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }

    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../workgroups/iowschupdate',
            beforeSend : function(){
                $('#saveiowschwbsbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {workid:idval,name:$('#editiowschwbsname'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowschwbsname'+data.Id).show();
                    $('#editiowschwbsbutton'+data.Id).show();
                    $('#editiowschwbsname'+data.Id).hide();
                    $('#saveiowschwbsbutton'+data.Id).hide();
                    $('#editiowschwbsname'+data.Id).val(data.Name);
                    $('#iowschwbsname'+data.Id).text(data.Name);
                    $('#listiowschworkgroup').trigger('click');

                }
                else
                {
                    alert(data.errortext);
                }

                $('#saveiowschwbsbutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.deleteiowschwbsbutton',function(){
    var workid=$(this).val();
    var r = confirm("Are you sure you want to delete thi IOW?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../workgroups/deleteiowschwbs',
            beforeSend : function(){
                $('#deleteiowschwbsbutton'+workid).attr("disabled", true);
            },
            dataType: "json",
            data: {workid:workid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowschwbsrow'+data.Id).remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deleteiowschwbsbutton'+data.Id).attr("disabled", false);
            }
        });
    }
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