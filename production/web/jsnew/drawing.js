/**
 * Created by SolmindsDelli5 on 12/14/2016.
 */
$(function(){
    $('#listdrawing').click(function(){
        $.ajax({
            type: 'POST',
            url: '../ProjectPricing/Drawingssearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#drawingitems').html(data.result);
                    $('#drawingtable').show();
                }
                $('.preloader').hide();
            }
        });
    });
});
$(document).on('change','#projectid',function(){
    var department=$('#department').val();
    var projectid=$('#projectid').val();
    $.ajax({
        type: 'POST',
        url: '../doccumentManager/Getfolders',
        dataType: "json",
        data: {department:department,projectid:projectid},
        success: function(data){
            if(data.error=='No')
            {
                $('#folder').html(data.result);
            }
        }
    });
});

$(document).on('change','#department',function(){
    var department=$('#department').val();
    var projectid=$('#projectid').val();
    $.ajax({
        type: 'POST',
        url: '../doccumentManager/Getfolders',
        dataType: "json",
        data: {department:department,projectid:projectid},
        success: function(data){
            if(data.error=='No')
            {
                $('#folder').html(data.result);
            }
        }
    });
});
$(document).on('click','#adddrawing',function(){
    var error=0;
    $('.error').hide();
    if($('#tittle').val()=='')
    {
        $('#tittle').next("span").html('Enter Title').show('slow');
        error=1;
    }
    if($('#folder').val()=='none')
    {
        $('#folder').next("span").html('Select Folder').show('slow');
        error=1;
    }
    if($('#department').val()=='none')
    {
        $('#department').next("span").html('Select Function').show('slow');
        error=1;
    }

    if(error==0){
        return true;
    }
    else{
        //alert("You have to enter all values for reporting");
        return  false;
    }
});
$(document).on( "click", ".deletedrawing", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Drawing ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../ProjectPricing/Deletedrawing',
            beforeSend : function(){
                $('#deletedrawing'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {drawingid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#drawingrow'+idval).remove();
                    $('#listdrawing').trigger('click');
                }

                $('#deletedrawing'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
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