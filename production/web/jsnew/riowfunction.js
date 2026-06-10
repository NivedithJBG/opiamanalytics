$(document).on( "click", ".rlistiow", function(){
    $('#rworkgroup').removeClass('active').next().slideUp();
    $('#ritemofwork').addClass('active').next('.acc_container').slideDown();
    var id=$(this).val();
    var parents= getParentnames(id)
    $('#selectedProjectId').val(id);
    $('#projectnamedisplay').html(parents.Project);
    $('#workgroupnamedisplay').html(parents.Workgroup);
    $('#selectedProjectId').val(parents.Project_Id);
    $('#selectedWorkgrouId').val(parents.Workgroup_Id);
    $('#ritemofwork').trigger('click') ;
});
$(document).on( "click", "#ritemofwork", function(){
    if($('#selectedWorkgrouId').val()!=''){
        $('.acc_trigger').removeClass('active').next().slideUp();
        $('#ritemofwork').addClass('active').next('.acc_container').slideDown();
        $('#scheduleactivities').hide();
        $('#iowlistsection').show('slow');// slide down the project listing div
        $.ajax({
            type: 'POST',
            url: '../itemofworks/IowSearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {Workgroup_Id:$('#selectedWorkgrouId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowitems').html(data.result);
                    $('#iowtable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    }

});
$(document).on('click','.reportiow',function(){
    $('#iowlistsection').hide();
    $('#scheduleactivities').show();
    var id=$(this).val();
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/Getreportfrom',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    $('#scheduleactivities').html(retval);
})

function getParentnames(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../itemofworks/GetParents',
        async:false,
        dataType: "json",
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}