$(document).on( "click", ".viewprogress", function(){
    //$('.acc_container').slideUp();
    $('#rproject').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#rworkgroup').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#dispprojectname').html(getProjectname(id));
    $('#selectedProjectId').val(id);
    $('#rworkgroup').trigger('click') ;
});
$(document).on( "click", "#rworkgroup", function(){
    if($('#selectedProjectId').val()!=''){
        //$('.acc_container').slideUp();
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown();
        $('#rworkgroup').addClass('active').next('.acc_container').slideDown();
        $('#workgrouplistsection').show('slow');
        //$('#dispprojectname').html(getProjectname($('#selectedProjectId').val()));
        //$('#selectedProjectId').val($('#selectedProjectId').val());
        $.ajax({
            type: 'POST',
            url: '../workgroups/WorkgroupsSearch',
            beforeSend : function(){
                $('#workgroupsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workgroupitems').html(data.result);
                    $('#workgrouptable').show();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#workgroupsearch').attr("disabled", false);
                $('.preloader').hide();
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