$(document).on( "click", "#view_workgroups", function(){
    /*$('.acco-three input[type=radio]').trigger('click');
    var id= $(this).attr('data-id');
    $('#dispprojectname').html(getProjectname(id));
    $('#workgroup_name').html('<span class="icon-chart6"></span>WBS');
    $('#dispprojectname_schedule').html(getProjectname(id));
    $('#wbs_schedule_block').hide();
    $('#selectedProjectId').val(id);
    $('#wbs-header').show();
    $('#projectnameWBS').show();

    $('.panel-group').removeClass('acco-one-active');
    $('.panel-group').removeClass('acco-two-active');
    $('.panel-group').addClass('acco-three-active');
    $('.panel-group').removeClass('acco-four-active');
    $('.panel-group').removeClass('acco-five-active');*/

    $('#wbs_estimate_block').show();
    $('#project-activities-Body').hide();
    $('#listworkgroup').trigger('click');
    $('#cancelworkgroup').trigger('click');
     
});

$(function(){
    $('#listworkgroup').click(function(){
        //var structureid = $('#estimatestructure').val();
        $.ajax({
            type: 'POST',
            url: '../workgroups1/search',
            beforeSend : function(){
                $('#listworkgroup').attr("disabled", true);
                $('#Promain-preloader-Listwbs').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val(),workgroupname:''},
            success: function(data){
                if(data.error=='No')
                {
                    $('#projectnameWBS').show();
                    $('#dispprojectname').html(data.projectName);
                    $('#listworkgroup-data').html(data.result);
                    $('#selectedProjectId').val(data.projectID);
                    $('#accordionprojindex').removeClass('acco-one-active');
                    $('#accordionprojindex').removeClass('acco-two-active');
                    $('#accordionprojindex').addClass('acco-three-active');
                    $('#accordionprojindex').removeClass('acco-four-active');
                    $('#accordionprojindex').removeClass('acco-five-active');
                }
                else
                {
                    alert(data.errortext);
                }

                $('#listworkgroup').attr("disabled", false);
                $('#Promain-preloader-Listwbs').hide();
            }
        });

    });
    
    $(document).on('click','.editworkgroupbutton',function(){
        var idval=$(this).attr('data-v');
        $('#workgroupname'+idval).hide();
        $('#worktypename'+idval).hide();
        $('#workgroupunit'+idval).hide();
        $('#workgroupquantity'+idval).hide();
        $('#editworkgroupbutton'+idval).hide();
        $('#editworkgroupname'+idval).show();
        $('#editworkgroupunit'+idval).show();
        $('#editworkgroupquantity'+idval).show();
        $('#editworktype'+idval).show();
        //$('#editworkgroupname'+idval).focus();
        $('#saveworkgroupbutton'+idval).show();
    });
    $(document).on('click','.saveworkgroupbutton',function(){
        var idval=$(this).attr('data-v');
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
        if($('#editworkgroupunit'+idval).val()=='')
        {
            $("#editworkgroupunit"+idval).next("span").html('Enter Unit').show('slow');
            error=1;
        }
        if($('#editworkgroupquantity'+idval).val()=='')
        {
            $("#editworkgroupquantity"+idval).next("span").html('Enter Quantity').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../workgroups1/update',
                beforeSend : function(){
                    $('#saveworkgroupbutton'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {workid:idval,name:$('#editworkgroupname'+idval).val(),worktype:$('#editworktype'+idval).val(),unit: $('#editworkgroupunit'+idval).val(),quantity: $('#editworkgroupquantity'+idval).val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#workgroupname'+data.Id).show();
                        $('#worktypename'+data.Id).show();
                        $('#workgroupunit'+data.Id).show();
                        $('#workgroupquantity'+data.Id).show();
                        $('#editworkgroupbutton'+data.Id).show();
                        $('#editworkgroupname'+data.Id).hide();
                        $('#editworktype'+data.Id).hide();
                        $('#editworkgroupquantity'+data.Id).hide();
                        $('#editworkgroupunit'+data.Id).hide();
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
        var workid=$(this).attr('data-v');

        $.ajax({
            type: 'POST',
            url: '../workgroups1/checkdeleteworkgroup',
            beforeSend : function(){
                $('#deleteworkgroupbutton'+workid).attr("disabled", true);
            },
            dataType: "json",
            data: {workid:workid},
            success: function(data){
                if(data.error=='No')
                {
                        var r = confirm("Are you sure you want to delete this Work Group?");
                        if (r == true) {
                            $.ajax({
                                type: 'POST',
                                url: '../workgroups1/deleteworkgroup',
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
                }
                else
                {
                
                    alert(data.errortext);
                }

                $('#deleteworkgroupbutton'+data.Id).attr("disabled", false);
            }
        });

        

        


    });

    $('#saveworkgroup').click(function(){
        var error=0;
        //var estimateid=$('#estimatestructure').val();
        if($('#workgroupname').val()=='')
        {
            $("#workgroupname").next("span").html('Enter IOW Name').show('slow');
            error=1;
        }
       /* if($('#workgroupname').val()!='' && WorkGroupNameExists($('#workgroupname').val(),$('#selectedProjectId').val())=='Yes')
        {
            $('#workgroupname').next("span").html('Work Group Name Exists').show('slow')
            error=1;
        } */
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
        if($('#workgroupunit').val()=='')
        {
            $("#workgroupunit").next("span").html('Enter Unit').show('slow');
            error=1;
        }
        if($('#workgroupquantity').val()=='')
        {
            $("#workgroupquantity").next("span").html('Enter Quantity').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../workgroups1/create',
                beforeSend : function(){
                    $('#saveworkgroup').attr("disabled", true);
                },
                dataType: "json",
                data: {Project_Id:$('#selectedProjectId').val(),workgroupname:$('#workgroupname').val(),worktype:$('#worktype').val(),worktypegroup:$('#worktypegroups').val(),unit:$('#workgroupunit').val(),quantity:$('#workgroupquantity').val()},
                success: function(data){

                    if(data.error=='No')
                    {
                        $('#addworkgroupform')[0].reset();
                        $('#cancelworkgroup').trigger('click');
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

    $('#cancelworkgroup').click(function(){
        $('#addworkgroupform')[0].reset();
        $(this).parents('.tab').removeClass('add-form-active');
        $('#listworkgroup').trigger('click');
        $('.content-action-wrpr').show();
    });

});

function getProjectname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projectsmain/getname',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}
$(function() {
$( "#listworkgroup-data" ).sortable({
        placeholder: "ui-state-highlight",
        helper:'clone',
        
        update:function( event, ui ) {
            //alert($(this).index());

            var updatedrows=[];
            $('.listdats').each(function() {
                var rowid=$(this).attr('data-id');
                //alert(rowid);
                var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    rowindex:rowindex
                })
            });
            $.ajax({
                type: 'POST',
                url: '../workgroups1/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection();
});


