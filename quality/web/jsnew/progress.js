$(document).on( "click", ".viewprogress", function(){
    //$('.acc_container').slideUp();
    $('#rproject').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#progress').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#projid').val(id);
    $('#projectname').html(getProjectname(id)); 
    $('#selectedProjectId').val(id);
    $('#progressiow').val('none');
    $('#progresssearchdiv').show();
    $('#progreportdiv').hide();
    $('#progress').trigger('click') ;
});
$(function(){
    $('#progress').click(function(){
        //$('#reportactivities').hide();
        /*$.ajax({
            type: 'POST',
            url: '../projects/Getwbs',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#progresswbs').html(data.result);
                }

                $('.preloader').hide();
            }
        });*/
        $.ajax({
            type: 'POST',
            url: '../projects/ProgressActivities',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#progressactivitylist').show();
                    $('#progreportdiv').hide();
                    //$('#editreportactivities').hide();
                    $('#progressactivityitems').html(data.result);
                    $('#progressactivitytable').show();
                }

                $('.preloader').hide();
            }
        });
    });
});
$(document).on('change','#workgroup',function(){
    var workgroupid=$(this).val();
    var projectid=$('#selectedProjectId').val();
    $.ajax({
        type: 'POST',
        url: '../projects/Getiow',
        data: {workgroupid:workgroupid,projectid:projectid},
        dataType: "json",
        success: function(data){
            // console.log(data.result);
            // if(data.result=null){
            //   $('#progressiow').html('<option value="none">Select Activity</option>');
            // }else{
            $('#progressiow').html(data.result);
            // }
        }
    });
});
$(document).on('click','#reportiow',function(){
    var error=0;
    $('.error').hide();
    /*if($('#workgroup').val()=='none')
    {
        $('#workgroup').next("span").html('Select Workgroup').show('slow');
        error=1;
    }
     if($('#progressiow').val()=='none')
    {
        $('#progressiow').next("span").html('Select Activity').show('slow');
        error=1;
    }*/
    if(error==0)
    {
        $('#progressactivitylist').hide();
        $('#progreportdiv').show();
        var actid=$(this).val();
        var wbsactid=$(this).attr("data-id");
        var processid=$(this).attr("data-process");
        var wbsid=$(this).attr("data-wbs");
        var projectid2=$('#selectedProjectId').val();
        // console.log($('#progressiow').find(':selected').attr('data-id'));
        var retval;
        $.ajax({
            type: 'POST',
            url: '../projects/Getreportfrom',
            async:false,
            data: {id:actid,wbsactid:wbsactid,wbsid:wbsid,processid:processid,proid:projectid2},
            dataType: "json",
            success: function(data){
                retval=data.result;

                $("span#progactivity h5").html('Activity : ' + data.activityname);
                $("span#progactunit h5").html(data.activityunit);
                //$('#datepicker0').datepicker("refresh");
            }
        });
        $('#iowactivities').html(retval);
        $('#datepicker').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
        $('#startdate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
    }

});

$(document).on('blur','.eduration',function(){
    var id=$(this).attr('data-id');
    var total=0;
    $(".eduration").each(function(){
        total=total + $(this).val() * 1;
    });
    $('#totalcycletime').html(total);
    $('#cycletime').val(total);
});
$(document).on('click','#savedraft',function(){
    var error=0;
    var totalcytime=$('#cycletime').val();
    var totalhours=$('#hoursoptions').val();
    /*if (parseFloat(totalcytime) > parseFloat(totalhours)){
        error=1;
    }*/

    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../projects/Reportcreate',
            beforeSend : function(){
                $('#savedraft').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#scheduleform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#progressactivitylist').show();
                    $('#progreportdiv').hide();
                }
                else
                {
                    alert(data.errortext);
                }
                $('#savedraft').attr("disabled", false);
            }
        });
    }
    /*else {
        alert('Daily total cycle time should match with Hours per day');
        return false;
    }*/



});
$(document).on('click','.startbutton',function(){
    var reportid = $(this).val();
    $.ajax({
        type: 'POST',
        url: '../projects/Reportstart',
        dataType: "json",
        data: {repid:reportid},
        success: function(data){
            if(data.error=='No')
            {
                // alert(data.result);
                $('.enddiv').html(data.result);
                $('.enddiv').show();
                $('.startdiv').hide();
            }
            else
            {
                alert(data.errortext);
            }
            
        }
    });
    return false;

});
$(document).on('click','#report',function(){
    var error=0;
    /*$('.validation').each(function(){
        if($(this).val()=='' ||$(this).val()=='0')
        {
            error=1;
        }
    });*/
    var totalcytime=$('#cycletime').val();
    var totalhours=$('#hoursoptions').val();
    /*if (parseFloat(totalcytime) != parseFloat(totalhours)){
        error=1;
    }*/
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../projects/Reportiow',
            beforeSend : function(){
                $('#report').attr("disabled", true);

            },
            dataType: "json",
            data: $( "#scheduleform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#progressactivitylist').show();
                    $('#progreportdiv').hide();
                }
                if(data.error==1)
                {
                    alert("Duration of the estimated time and elapsed time is different by "+data.result+" days. Please fill the appropriate tasks so that the duration is matched")
                }

                $('#report').attr("disabled", false);
            }
        });
    }
    /*else{
        alert('Daily total cycle time should match with Hours per day');
        return  false;
    }*/
});
$(document).on('click','#cancelreport',function(){
    $('#progressactivitylist').show();
    $('#progreportdiv').hide();

});

$(document).on('change','#progresscycle',function(){
    var actid=$('#progactivityid').val();
    var wbsactid=$('#wbsactid').val();
    var processid=$('#processid').val();
    var wbsid=$('#wbsid').val();
    var date=$('#datepicker').val();
    var projectid2=$('#projid').val();
    var cyclenum=$(this).val();
    var startdate=$('#progressstartdate').val();
    // console.log($('#progressiow').find(':selected').attr('data-id'));
    var retval;
    $('#progressactivitylist').hide();
    $('#progreportdiv').show();
    $.ajax({
        type: 'POST',
        url: '../projects/Getreportfrom',
        async:false,
        data: {id:actid,wbsactid:wbsactid,wbsid:wbsid,processid:processid,proid:projectid2,cyclenum:cyclenum,date:date,startdate:startdate},
        dataType: "json",
        success: function(data){
            retval=data.result;
            $("span#progactivity h5").html('Activity : '+data.activityname);
            $("span#progactunit h5").html(data.activityunit);
            //$('#datepicker').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
        }
    });
    $('#iowactivities').html(retval);
    $('#datepicker').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
    $('#startdate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
});

$(document).on('change','#datepicker',function(){
    var actid=$('#progactivityid').val();
    var wbsactid=$('#wbsactid').val();
    var processid=$('#processid').val();
    var wbsid=$('#wbsid').val();
    var date=$(this).val();
    var projectid2=$('#projid').val();
    var cyclenum=$('#progresscycle').val();
    var startdate=$('#progressstartdate').val();

    // console.log($('#progressiow').find(':selected').attr('data-id'));
    var retval;
    $('#progressactivitylist').hide();
    $('#progreportdiv').show();
    $.ajax({
        type: 'POST',
        url: '../projects/Getreportfrom',
        async:false,
        data: {id:actid,wbsactid:wbsactid,wbsid:wbsid,processid:processid,proid:projectid2,cyclenum:cyclenum,date:date,startdate:startdate},
        dataType: "json",
        success: function(data){
            retval=data.result;
            $("span#progactivity h5").html('Activity : '+data.activityname);
            $("span#progactunit h5").html(data.activityunit);
            //$('#datepicker').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
        }
    });
    $('#iowactivities').html(retval);
    $('#datepicker').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
    $('#startdate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});
});
$(document).on('click','#noworkbtn',function(){
    if($(this).is(':checked')){
        $(".eduration").attr("disabled", true);
    }
    else {
        $(".eduration").attr("disabled", false);
    }
});
$(document).on('blur','.deduction',function(){
    var id=$(this).attr('data-id');
    var total=0;
    var deduction=$(this).val();
    var wages=$('#raisewagesval'+id).val();
    var netamount=wages - deduction;
    $('#netamount'+id).html(netamount);
    $(".netamount").each(function(){
        total=total + $(this).html() * 1;
    });
    $('#totalwages').html(total);
});

$(document).on('click','.hover',function(){
    var tooltip=$(this).attr('data-tooltip');
    $('.tooltiptable').hide();
    $('#'+tooltip).fadeIn('fast');
    // alert('#'+tooltip);
});
$(document).on('mouseleave','.hover',function(){
    var tooltip=$(this).attr('data-tooltip');
    $('#'+tooltip).fadeOut('slow');
});

$(document).on('click','.deletepractivity',function(){
    var actid=$(this).val();
    var r = confirm("Are you sure you want to delete this Activity ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../Jobcard/Deletepractivity/',
            beforeSend : function(){
                $('#deletepractivity'+actid).attr("disabled", true);
            },
            dataType: "json",
            data: {actid:actid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#progressreporrow'+actid).remove();
                }
                $('#deletepractivity'+actid).attr("disabled", false);
            }
        });
    }
    else {
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