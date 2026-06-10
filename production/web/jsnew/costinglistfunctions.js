$(document).on( "click", ".viewcosting", function(){
    //$('.acc_container').slideUp();
    $('#project').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#costingitems').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#selectedProjectId').val(id);
    $('#costingshowprojectname').html(getScheduleProjectname(id));
    $('#costingscheduleworkgrouplist').html(getCostingWorkgrouplist(id));
    $('#costinglistiow').show();
    $.ajax({
        type: 'POST',
        url: '../projects/costingiows',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {workgroupid:$('#costingworkgroups').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#costinglist').html(data.result);
            }
            else
            {
                alert(data.errortext);
            }
            $('.preloader').hide();
        }
    });
/*
    $('#dispprojectname').html(getProjectname(id));
    $('#WorkgroupProjectId').val(id);
    $('#listworkgroup').trigger('click') ;*/
});
$(document).on( "click", "#costingitems", function(){
    if($('#selectedProjectId').val()!='')
    {
        $('.acc_trigger').removeClass('active').next().slideUp();
        $('#costingitems').addClass('active').next('.acc_container').slideDown();
        var id= $('#selectedProjectId').val();
        $('#costingshowprojectname').html(getScheduleProjectname(id));
        $('#costingscheduleworkgrouplist').html(getCostingWorkgrouplist(id));
        $('#costinglistiow').show();
        $.ajax({
            type: 'POST',
            url: '../projects/costingiows',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {workgroupid:$('#costingworkgroups').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#costinglist').html(data.result);
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

$(document).on('change','#costingworkgroups',function(){
    $.ajax({
        type: 'POST',
        url: '../projects/costingiows',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {workgroupid:$(this).val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#costinglist').html(data.result);
            }
            else
            {
                alert(data.errortext);
            }
            $('.preloader').hide();
        }
    });
})
$(document).ready(function(){
    $('#costingworkgroups').change(function(){
        $.ajax({
            type: 'POST',
            url: '../projects/costingiows',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {workgroupid:$(this).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#costinglist').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    })

});

/*$(document).on('click','#saveschedule',function(){

    $.ajax({
        type: 'POST',
        url: '../projects/Schedulecreate',
        beforeSend : function(){
            $('#saveschedule').attr("disabled", true);

        },
        dataType: "json",
        data: $( "#scheduleform" ).serialize(),
        success: function(data){
            if(data.error=='No')
            {
                $('#scheduleactivities').hide();
                $('#schedulelistiow').show();
                $('#scheduleworkgroups').trigger('change');

            }
            else
            {
                alert(data.errortext);
            }
            $('#saveschedule').attr("disabled", false);
        }
    });
    return false;

});*/
$(document).on('click','#cancelschedule',function(){
    $('#scheduleactivities').hide();
    $('#schedulelistiow').show();

});
/*$(document).on('click','.schedulecreate',function(){
    $('#scheduleactivities').show();
    $('#schedulelistiow').hide();
    var id=$(this).val();
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/Getschedulefrom',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    $('#scheduleactivities').html(retval);
})*/


function getCostingWorkgrouplist(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/GetWorkgroupsCosting',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}
function getScheduleProjectname(id)
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