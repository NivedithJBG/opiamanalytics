$(document).on( "click", ".viewschedules", function(){
    //$('.acc_container').slideUp();
    $('#activity').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#scheduleitems').addClass('active').next('.acc_container').slideDown();
    var id = $('#selectedProjectId').val();
    var estimateid= $(this).val();
    var processid = $(this).attr('data-id');
    var workgrpid =  $('#selectedWorkgrouId').val();
    $('#workgroupid').val($('#selectedWorkgrouId').val());
    // console.log($('#workgroupid').val());
    // $('#selectedProjectId').val(id)
    // $('#showprojectname').html(getScheduleProjectname(id));
    $('#projeid').val(id);
    // alert($('#projeid').val());
    $('#scheduleworkgrouplist').html(getWorkgrouplist(id));
    $('#scheduleactivities').hide();
    $('#schedulelistiow').show();
    $.ajax({
        type: 'POST',
        url: '../projects/scheduleactivity',
        beforeSend : function(){ 
            $('.preloader').show();
        },
        dataType: "json",
        data: {workgroupid:workgrpid,estimateid:estimateid},
        success: function(data){
            if(data.error=='No')
            {
                $('#schedulelist').html(data.result);
                // $('.schedulecreate').val(id);

                scheduleclick(id,processid,workgrpid,estimateid);
               // $('.schedulecreate').trigger('click') ;
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

$(document).on( "click", ".schedulebtn", function(){
    $('#activity').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    $('#scheduleitems').addClass('active').next('.acc_container').slideDown();
    var projid = $('#selectedProjectId').val();
    var activityid= $(this).val();
    var processid = $(this).attr('data-type');
    var wbsactid = $(this).attr('data-id');
    var workgrpid =  $('#selectedWorkgrouId').val();
    $('#workgroupid').val(workgrpid);
    $('#projeid').val(projid);
    $('#showprojectname').html(getScheduleProjectname(projid));
    $('#showworkgroupname').html(getWorkgroupname(workgrpid));
    $('#scheduleactivities').show();
    $('#schedulelistiow').hide();
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/Getschedulefrom',
        async:false,
        data: {projectid:projid,processid:processid,wbsid:workgrpid,activityid:activityid,wbsactid:wbsactid},
        success: function(data){
            retval=data;
        }
    });
    $('#scheduleactivities').html(retval);
});

$(document).on( "click", "#scheduleitems", function(){
    if($('#selectedProjectId').val()!='')
    {
        $('.acc_trigger').removeClass('active').next().slideUp();
        $('#scheduleitems').addClass('active').next('.acc_container').slideDown();
        var id= $('#selectedProjectId').val();
        $('#showprojectname').html(getScheduleProjectname(id));
        $('#scheduleworkgrouplist').html(getWorkgrouplist(id));
        $('#scheduleactivities').hide();
        $('#schedulelistiow').show();
        $.ajax({
            type: 'POST',
            url: '../projects/scheduleactivity',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {workgroupid:$('#scheduleworkgroups').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#schedulelist').html(data.result);
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

$(document).on('change','#scheduleworkgroups',function(){
    $.ajax({
        type: 'POST',
        url: '../projects/scheduleactivity',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {workgroupid:$(this).val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#schedulelist').html(data.result);
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
    $('#scheduleworkgroups').change(function(){
        $.ajax({
            type: 'POST',
            url: '../projects/scheduleactivity',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {workgroupid:$(this).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#schedulelist').html(data.result);
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
$(document).on('click','.activity',function(){
    var rowid=$(this).val();
    var bduration= $( "#activityrow"+rowid+" td" ).find( ".bduration" ).val();
    var aduration=$( "#activityrow"+rowid+" td" ).find( ".eduration" ).val();
    // alert(aduration);
    if($(this).is(":checked")) {

       var cyclebud=($("#cyclebud").text() * 1) - bduration;
        $("#cyclebud").text(cyclebud);
        $("#budcycle").val(cyclebud);
       var cycleact=($("#cycleact").text() * 1) - aduration;
        $("#cycleact").text(cycleact.toFixed(2));
        $("#actcycle").val(cycleact.toFixed(2));
    }
    else
    {
        var cyclebud=($("#cyclebud").text() * 1) + (bduration*1);
        $("#cyclebud").text(cyclebud);
        $("#budcycle").val(cyclebud);
        var cycleact=($("#cycleact").text() * 1) + (aduration*1);
        $("#cycleact").text(cycleact);
        $("#actcycle").val(cycleact);

    }

});

$(document).on('blur','.bduration',function(){
    var identifier=$(this).attr('data-id');
    var cycleatotalb=0
    var cycleatotale=0
    $('#eduration'+identifier).val($(this).val() * 1);
    $(".bduration").each(function(){
        cycleatotalb=cycleatotalb+($(this).val()*1)
    });
    $(".eduration").each(function(){
        cycleatotale=cycleatotale+($(this).val()*1)
    });
    $('#cyclebud').text(cycleatotalb);
    $('#budcycle').val(cycleatotalb);
    $('#cycleact').text(cycleatotale);
    $('#actcycle').val(cycleatotale);
});

$(document).on('blur','.eduration',function(){
    var cycleatotal=0
    $(".eduration").each(function(){
        cycleatotal=cycleatotal+($(this).val()*1)
    });
    $('#cycleact').text(cycleatotal);
    $('#actcycle').text(cycleatotal);

});

$(document).on('click','#saveschedule',function(){

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
                //$('#scheduleworkgroups').trigger('change');

            }
            else
            {
                alert(data.errortext);
            }
            $('#saveschedule').attr("disabled", false);
        }
    });
    //return false;

});
$(document).on('click','#cancelschedule',function(){
    $('#scheduleactivities').hide();
    $('#schedulelistiow').show();

});
function scheduleclick(id,processid,workgrpid,estimateid){
    $('#scheduleactivities').show();
    $('#schedulelistiow').hide();

    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/Getschedulefrom',
        async:false,
        data: {projectid:id,processid:processid,wbsid:workgrpid,estimateid:estimateid},
        success: function(data){
            retval=data;
        }
    });
    $('#scheduleactivities').html(retval);
};
$(document).on('click','.schedulecreate',function(){
    $('#scheduleactivities').show();
    $('#schedulelistiow').hide(); 
    var estimateid=$(this).val();
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/Getschedulefrom',
        async:false,
        data: {projectid:$('#projeid').val(),processid:$(this).attr('data-id'),wbsid:$('#wbs').val(),estimateid:estimateid},
        success: function(data){
            retval=data;
        }
    });
    $('#scheduleactivities').html(retval);
})


function getWorkgrouplist(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/GetWorkgroups',
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
        url: '../projects1/Getname',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}

function getWorkgroupname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects1/Getwbsname',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}