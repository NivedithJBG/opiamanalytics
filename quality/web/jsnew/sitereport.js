$(document).on( "click", ".viewsitereport", function(){
    //$('.acc_container').slideUp();
    $('#rproject').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#sitereport').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#projid').val(id);
    $('#siteprojectname').html(getProjectname(id));
    $('#selectedProjectId').val(id);
    //$('#activity').val('none');
    //$('#processlistsection').show();
    $('#reportactivities').hide();
    $('#editreportactivities').hide();
    $('#sitereport').trigger('click') ;
});
$(function(){
    $('#sitereport').click(function(){
        $.ajax({
            type: 'POST',
            url: '../projects/Listactivities',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#activitylistsection').show();
                    $('#reportactivities').hide();
                    $('#editreportactivities').hide();
                    $('#activitylistitems').html(data.result);
                    $('#activitylisttable').show();
                }

                $('.preloader').hide();
            }
        });
    });

    /*$('#sitereport').click(function(){
        $('#processlistsection').show();
        $('#reportactivities').hide();
        $.ajax({
            type: 'POST',
            url: '../projects/process',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#processlist').html(data.result);
                }

                $('.preloader').hide();
            }
        });
    });*/

});
/*$(document).on('change','#process',function(){
    var process=$(this).val();
    var projectid=$('#selectedProjectId').val();
    $.ajax({
        type: 'POST',
        url: '../projects/Getactivity',
        data: {process:process,projectid:projectid},
        dataType: "json",
        success: function(data){
            $('#activity').html(data.result);
        }
    });
});*/
$(document).on('change','#process',function(){
    var process=$(this).val();
    var projectid=$('#selectedProjectId').val();
    $.ajax({
        type: 'POST',
        url: '../DoccumentManager/Getactivity',
        data: {projectid:$('#selectedProjectId').val(),processid:process},
        dataType: "json",
        success: function(data){
            $('#activity').html(data.result);
        }
    });
});
$(document).on('click','.report',function(){
    var error=0;
    $('.error').hide();
    if($('#process').val()=='none')
    {
        $('#process').next("span").html('Select Process').show('slow');
        error=1;
    }

    if(error==0)
    {
        //$('#processlistsection').hide();
        $('#activitylistsection').hide();
        $('#reportactivities').show();
        $('#editreportactivities').hide();
        //var projestid=$('#activity').val();
        var projestid=$(this).val();
        var projectid=$('#selectedProjectId').val();
        var owner=$('#owner').val();
        $.ajax({
            type: 'POST',
            url: '../projects/Getreportform',
            beforeSend : function(){
                $('.preloader').show();
            },
            data: {projestid:projestid,projectid:projectid,owner:owner},
            dataType: "json",
            success: function(data){
                $('#activityid').val(data.activityid);
                $('#resgroupid').val(data.resgroupid);
                $("span#activity h5").html(data.prodtname);
                $("span#process h5").html(data.process);
                $("span#unit h5").html(data.activityunit);
                $("span#cycleunit h5").html(data.prodtunit);
                $("span#compcycles h5").html(data.cyclescompleted);
                $("span#cumquantity h5").html(data.totalactqty);
                $('#activityunit').val(data.activityunit);
                $('#cyclenumoptions').html(data.list);
                $('#uptodateqty').val(data.qtytotal);
                $('#qtyupto').val(data.qtytotal);
                $('#qtyproduced').val(data.sitereportact);
                $('#reporttable').show();
                $('#reportitems').html(data.result);
                $('#draftdatediv').html(data.reportdatesdiv);
                $('.preloader').hide();
            }
        });
    }

});

$(document).on('click','.edit',function(){
    $('#activitylistsection').hide();
    $('#reportactivities').hide();
    $('#editreportactivities').show();
    //var projestid=$('#activity').val();
    var projestid=$(this).val();
    var projectid=$('#selectedProjectId').val();
    $.ajax({
        type: 'POST',
        url: '../projects/Editreportform',
        beforeSend : function(){
            $('.preloader').show();
        },
        data: {projestid:projestid,projectid:projectid},
        dataType: "json",
        success: function(data){
            $('#editactivityid').val(data.activityid);
            $('#editresgroupid').val(data.resgroupid);
            $("span#editactivity h5").html(data.prodtname);
            $("span#editprocess h5").html(data.process);
            $("span#editunit h5").html(data.prodtunit);
            $("span#editcycleunit h5").html(data.prodtunit);
            $('#editactivityunit').val(data.prodtunit);
            $('#editcyclenumoptions').html(data.list);
            $('#edituptodateqty').val(data.qtytotal);
            $('#editqtyupto').val(data.qtytotal);
            $('#editqtyproduced').val(data.sitereportact);
            $('#editreporttable').show();
            $('#editreportitems').html(data.result);
            $('#reportdatediv').html(data.reportdatesdiv);
            $('.preloader').hide();
        }
    });
});

$(document).on('click','#reportres',function(){
    var error=0;
    var projectid=$('#selectedProjectId').val();
    if($('#qtyproduced').val()=='' || $('#qtyproduced').val()==0)
    {
        alert("Today's Work Done is not a valid quantity.");
        error=1;
    }
    var estqty=$('#estactqty').val() * 1;
    var cumquantity=$('#pactqty').val() * 1;
    var qtyproduced=$('#qtyproduced').val() * 1;
    var totalqty=cumquantity + qtyproduced;
    if (totalqty > estqty)
    {
        alert('Cannot report greater than Estimated quantity');
        error=1;
    }

    /*$('.resourceqty').each(function(){
        if($(this).val()=='' ||$(this).val()=='0')
        {
            error=1;
        }
    });*/
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../projects/Reportresource',
            beforeSend : function(){
                $('#reportres').attr("disabled", true);

            },
            dataType: "json",
            data: $( "#resourceform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#sitereport').trigger('click') ;
                }

                $('#reportres').attr("disabled", false);
            }
        });
    }
    else{
        //alert("You have to enter all values for reporting");
        return  false;
    }
});

$(document).on('click','#saveasdraft',function(){

    var projectid=$('#selectedProjectId').val();
    $.ajax({
        type: 'POST',
        url: '../projects/Draftresource',
        beforeSend : function(){
            $('#saveasdraft').attr("disabled", true);

        },
        dataType: "json",
        data: $( "#resourceform" ).serialize(),
        success: function(data){
            if(data.error=='No')
            {
                $('#sitereport').trigger('click') ;
            }

            $('#saveasdraft').attr("disabled", false);
        }
    });

});

$(document).on('change','#cyclenumoptions',function(){
    var cycle=$(this).val();
    var activityid=$('#jactivityid').val();
    $('#activitylistsection').hide();
    $('#editreportactivities').hide();
    $('#reportactivities').show();
    //var projestid=$('#activity').val();
    var projestid=activityid;
    var projectid=$('#selectedProjectId').val();
    var date=$('#datepicker0').val();
    $.ajax({
        type: 'POST',
        url: '../projects/Getreportform',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        data: {projestid:projestid,projectid:projectid,Cyclenumber:cycle,date:date},
        dataType: "json",
        success: function(data){
            $('#activityid').val(data.activityid);
            $('#resgroupid').val(data.resgroupid);
            $("span#activity h5").html(data.prodtname);
            $("span#unit h5").html(data.prodtunit);
            $("span#cycleunit h5").html(data.prodtunit);
            $('#activityunit').val(data.prodtunit);
            $('#cyclenumoptions').html(data.list);
            $('#uptodateqty').val(data.qtytotal);
            $('#qtyupto').val(data.qtytotal);
            $('#qtyproduced').val(data.sitereportact);
            $('#reporttable').show();
            $('#reportitems').html(data.result);
            //$('.preloader').hide();
        }
    });
});

$(document).on('change','#editcyclenumoptions',function(){
    var cycle=$(this).val();
    var activityid=$('#editjactivityid').val();
    $('#activitylistsection').hide();
    $('#reportactivities').hide();
    $('#editreportactivities').show();
    //var projestid=$('#activity').val();
    var projestid=activityid;
    var projectid=$('#selectedProjectId').val();
    var date=$('#editdatepicker').val();
    $.ajax({
        type: 'POST',
        url: '../projects/Editreportform',
        /*beforeSend : function(){
         $('.preloader').show();
         },*/
        data: {projestid:projestid,projectid:projectid,Cyclenumber:cycle,date:date},
        dataType: "json",
        success: function(data){
            $('#editactivityid').val(data.activityid);
            $('#editresgroupid').val(data.resgroupid);
            $("span#editactivity h5").html(data.prodtname);
            $("span#editunit h5").html(data.prodtunit);
            $("span#editcycleunit h5").html(data.prodtunit);
            $('#editactivityunit').val(data.prodtunit);
            $('#editcyclenumoptions').html(data.list);
            $('#edituptodateqty').val(data.qtytotal);
            $('#editqtyupto').val(data.qtytotal);
            $('#editqtyproduced').val(data.sitereportact);
            $('#editreporttable').show();
            $('#editreportitems').html(data.result);
            //$('.preloader').hide();
        }
    });
});

$(document).on('change','#datepicker0',function(){
    var date=$(this).val();
    var cycle=$('#cyclenumoptions').val();
    var activityid=$('#jactivityid').val();
    $('#activitylistsection').hide();
    $('#reportactivities').show();
    $('#editreportactivities').hide();
    //var projestid=$('#activity').val();
    var projestid=activityid;
    var projectid=$('#selectedProjectId').val();
    $.ajax({
        type: 'POST',
        url: '../projects/Getreportform',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        data: {projestid:projestid,projectid:projectid,Cyclenumber:cycle,date:date},
        dataType: "json",
        success: function(data){
            $('#activityid').val(data.activityid);
            $('#resgroupid').val(data.resgroupid);
            $("span#activity h5").html(data.prodtname);
            $("span#unit h5").html(data.prodtunit);
            $("span#cycleunit h5").html(data.prodtunit);
            $('#activityunit').val(data.prodtunit);
            $('#cyclenumoptions').html(data.list);
            $('#uptodateqty').val(data.qtytotal);
            $('#qtyupto').val(data.qtytotal);
            $('#qtyproduced').val(data.sitereportact);
            $('#reporttable').show();
            $('#reportitems').html(data.result);
            //$('.preloader').hide();
        }
    });
});

$(document).on('change','#editdatepicker',function(){
    var date=$(this).val();
    var cycle=$('#editcyclenumoptions').val();
    var activityid=$('#editjactivityid').val();
    $('#activitylistsection').hide();
    $('#reportactivities').hide();
    $('#editreportactivities').show();
    //var projestid=$('#activity').val();
    var projestid=activityid;
    var projectid=$('#selectedProjectId').val();
    $.ajax({
        type: 'POST',
        url: '../projects/Editreportform',
        /*beforeSend : function(){
         $('.preloader').show();
         },*/
        data: {projestid:projestid,projectid:projectid,Cyclenumber:cycle,date:date},
        dataType: "json",
        success: function(data){
            $('#editactivityid').val(data.activityid);
            $('#editresgroupid').val(data.resgroupid);
            $("span#editactivity h5").html(data.prodtname);
            $("span#editunit h5").html(data.prodtunit);
            $("span#editcycleunit h5").html(data.prodtunit);
            $('#editactivityunit').val(data.prodtunit);
            $('#editcyclenumoptions').html(data.list);
            $('#edituptodateqty').val(data.qtytotal);
            $('#editqtyupto').val(data.qtytotal);
            $('#editqtyproduced').val(data.sitereportact);
            $('#editreporttable').show();
            $('#editreportitems').html(data.result);
            //$('.preloader').hide();
        }
    });
});

$(document).on('click','#savereportres',function(){
    var error=0;
    var projectid=$('#selectedProjectId').val();
    if($('#editqtyproduced').val()=='' || $('#editqtyproduced').val()==0)
    {
        error=1;
    }
    /*$('.resourceqty').each(function(){
     if($(this).val()=='' ||$(this).val()=='0')
     {
     error=1;
     }
     });*/
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../projects/SaveReportresource',
            beforeSend : function(){
                $('#savereportres').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#editresourceform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#sitereport').trigger('click') ;
                }

                $('#savereportres').attr("disabled", false);
            }
        });
    }
    else{
        alert("You have to enter all the values for reporting");
        return  false;
    }
});

$(document).on('click','#cancelreport',function(){
    $('#reportactivities').hide();
    $('#editreportactivities').hide();
    //$('#processlistsection').show();
    $('#activitylistsection').show();

});
$(document).on('click','.draftdate',function(){
    var tooltip=$(this).attr('data-tooltip');
    $('.tooltiptable').hide();
    $('#'+tooltip).fadeIn('fast');
    // alert('#'+tooltip);
});
$(document).on('mouseleave','.draftdate',function(){
    var tooltip=$(this).attr('data-tooltip');
    $('#'+tooltip).fadeOut('slow');
});
$(document).on('click','.reportdate',function(){
    var tooltip=$(this).attr('data-tooltip');
    $('.tooltiptable').hide();
    $('#'+tooltip).fadeIn('fast');
    // alert('#'+tooltip);
});
$(document).on('mouseleave','.reportdate',function(){
    var tooltip=$(this).attr('data-tooltip');
    $('#'+tooltip).fadeOut('slow');
});

$(document).on('click','.deleteactivity',function(){
    var actid=$(this).val();
    var r = confirm("Are you sure you want to delete this Activity ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../Jobcard/Deleteactivity/',
            beforeSend : function(){
                $('#deleteactivity'+actid).attr("disabled", true);
            },
            dataType: "json",
            data: {actid:actid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#activityreportrow'+actid).remove();
                }
                $('#deleteactivity'+actid).attr("disabled", false);
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