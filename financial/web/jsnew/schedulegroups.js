$(document).on('click','#Schedulegroups',function(){
    if(!$(this).next().is(':hidden') ) {
        $('.acc_trigger').removeClass('active').next().slideUp();
    }
    if( $(this).next().is(':hidden') ) {
        $('.acc_trigger').removeClass('active').next().slideUp();
        $(this).toggleClass('active').next().slideDown();
    }
    $('#listschedulegroups').trigger('click');
});
$(function(){
    $('#listschedulegroups').click(function(){
        $('#schedulegrpsadd').slideUp('slow');// slide down the project listing div
        $('#schedulegrplist').slideDown('slow');// slide down the project listing div
        $('#listschedulegroups').removeClass('btn-danger').addClass('btn-success');
        $('#addschedulegroups').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Schedule/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {acntgrpsname:$('#searchaccountgroups').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#schedulegrpitems').html(data.result);
                    $('#schedulegrptable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });
    /*$('#acntgrpssearch').click(function(){
        $('#listaccountgroups').trigger('click')
    });*/
    $('#addschedulegroups').click(function(){
        $('#schedulegrplist').slideUp('slow');// slide down the project listing div
        $('#schedulegrpsadd').slideDown('slow');// slide down the project listing div
        $('#addschedulegroups').removeClass('btn-danger').addClass('btn-success');
        $('#listschedulegroups').removeClass('btn-success').addClass('btn-danger');

    });
    $('#saveschedulegrps').click(function(){
        var error=0;
        $('.error').hide();
        if($('#accounthead').val()=='none')
        {
            $("#accounthead").next("span").html('Select Account Head').show('slow');
            error=1;
        }
        if($('#schedulegroupsname').val()=='')
        {
            $("#schedulegroupsname").next("span").html('Enter Schedule Groups Name').show('slow');
            error=1;
        }
        if(error==0){
            $.ajax({
                type:'POST',
                url:'../Schedule/create',
                beforeSend:function(){
                    $('#saveschedulegrps').attr("disabled", true);
                },
                dataType:'json',
                data: {name:$('#schedulegroupsname').val(),accounthead:$('#accounthead').val()},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#schedulegrpsform')[0].reset();
                        $('#listschedulegroups').trigger('click');
                        $('#saveschedulegrps').attr("disabled", false);
                        /*$('#searchacntgrp').append($('<option>', {
                            value: data.Id,
                            text: data.Name
                        }));*/
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#saveschedulegrps').attr("disabled", false);
                }
            });
        }
    });
    $(document).on( "click", ".editschedulegrpsbutton", function(){
        var idval=$(this).val();
        $('#editschedulegrpsname'+idval).show();
        $('#saveschedulegrpsbutton'+idval).show();
        $('#accountheadedit'+idval).show();
        $('#schedulegrpstext'+idval).hide();
        $('#editschedulegrpsbutton'+idval).hide();
        $('#scheduleaccnthead'+idval).hide();
        
    } );
    $(document).on( "click", ".saveschedulegrpsbutton", function(){
        var idval=$(this).val();
        var error=0;
        $('.error').hide();
        if($('#editschedulegrpsname'+idval).val()=='')
        {
            $('#editschedulegrpsname'+idval).next("span").html('Enter Name').show('slow');
            error=1;
        }
        if($('#accountheadedit'+idval).val()=='none')
        {
            $('#accountheadedit'+idval).next("span").html('Select Account Head').show('slow');
            error=1;
        }
        if(error==0){
            $.ajax({
                type: 'POST',
                url: '../Schedule/update',
                beforeSend : function(){
                    $('#saveschedulegrpsbutton'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {grpid:idval,name:$('#editschedulegrpsname'+idval).val(),accounthead:$('#accountheadedit'+idval).val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#editschedulegrpsname'+data.Id).hide();
                        $('#accountheadedit'+data.Id).hide();
                        $('#saveschedulegrpsbutton'+data.Id).hide();
                        $('#schedulegrpstext'+data.Id).text($('#editschedulegrpsname'+data.Id).val()).show();
                        $('#editschedulegrpsbutton'+data.Id).show();
                        $('#scheduleaccnthead'+data.Id).text(data.Accounthead).show();
                        /*$("select#searchacntgrp option[value='"+data.Id+"']").remove();
                        $('#searchacntgrp').append($('<option>', {
                            value: data.Id,
                            text: data.Name
                        }));*/

                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#saveschedulegrpsbutton'+data.Id).attr("disabled", false);
                }
            });
        }

    });
    $(document).on( "click", ".deleteschedulegrpsbutton", function(){
        var idval=$(this).val();
        var r = confirm("Are you sure you want to delete this Schedule group ?");
        if (r == true) {

            $.ajax({
                type: 'POST',
                url: '../Schedule/DeleteItem',
                beforeSend : function(){
                    $('#deleteschedulegrpsbutton'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {grpid:idval},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#schedulegrpsrow'+data.Id).remove();
                        $('#listschedulegroups').trigger('click');
                        //$("select#searcselecttype option[value='"+data.Id+"']").remove();
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deleteschedulegrpsbutton'+data.Id).attr("disabled", false);
                }
            });

        } else {
            return false;
        }

    });
    $(document).on('click','.childschedulegrps',function(){
        var grpid=$(this).val();
        $('#searchschedulegrp').val(grpid);
        $('#Subschedule').trigger('click');
    });
});