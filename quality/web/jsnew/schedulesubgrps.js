$(document).on('click','#Subschedule',function(){
    if(!$(this).next().is(':hidden') ) {
        $('.acc_trigger').removeClass('active').next().slideUp();
    }
    if( $(this).next().is(':hidden') ) {
        $('.acc_trigger').removeClass('active').next().slideUp();
        $(this).toggleClass('active').next().slideDown();
    }
    $('#listsubschedule').trigger('click');
});
$(function(){
    $('#listsubschedule').click(function(){
        $('#subscheduleaddsection').slideUp('slow');// slide down the project listing div
        $('#subschedulesection').slideDown('slow');// slide down the project listing div
        $('#listsubschedule').removeClass('btn-danger').addClass('btn-success');
        $('#addsubschedule').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Subschedule/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {schedulegrp:$('#searchschedulegrp').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#subscheduleitems').html(data.result);
                    //$('#searchschedulegrp').append(data.group);
                    $('#subscheduletable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });
    $('#subschedulesearch').click(function(){
        $('#listsubschedule').trigger('click')
    });
    $('#addsubschedule').click(function(){
        $('#subschedulesection').slideUp('slow');// slide down the project listing div
        $('#subscheduleaddsection').slideDown('slow');// slide down the project listing div
        $.ajax({
            type: 'POST',
            url: '../Subschedule/schedulesearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#addschedulegrp').append(data.group);
                }
                else
                {
                    alert(data.errortext);
                }

            }
        });
        $('#addsubschedule').removeClass('btn-danger').addClass('btn-success');
        $('#listsubschedule').removeClass('btn-success').addClass('btn-danger');

    });
    $('#savesubschedule').click(function(){
        var error=0;
        $('.error').hide();
        if($('#addschedulegrp').val()=='none')
        {
            $("#addschedulegrp").next("span").html('Select Schedule Group').show('slow');
            error=1;
        }
        if($('#subschedulename').val()=='')
        {
            $("#subschedulename").next("span").html('Enter Sub Schedule Name').show('slow');
            error=1;
        }
        /*if(ResourceTypeNameExists($('#restypename').val())=='Yes')
         {
         $("#restypename").next("span").html('Resource Type Exists').show('slow');
         error=1;
         }*/
        if(error==0){
            $.ajax({
                type:'POST',
                url:'../Subschedule/create',
                beforeSend:function(){
                    $('#savesubschedule').attr("disabled", true);
                },
                dataType:'json',
                data: {subschedulename:$('#subschedulename').val(),schedulegrp:$('#addschedulegrp').val()},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#subscheduleform')[0].reset();
                        $('#searchschedulegrp').val(data.Accountgroup);
                        $('#listsubschedule').trigger('click');
                        $('#savesubschedule').attr("disabled", false);

                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#savesubschedule').attr("disabled", false);
                }
            });
        }
    });
    $(document).on( "click", ".editsubschedulebutton", function(){
        var idval=$(this).val();
        $('#editsubschedulename'+idval).show();
        $('#savesubschedulebutton'+idval).show();
        $('#editschedulegrp'+idval).show();
        $('#subscheduletext'+idval).hide();
        $('#schedulegrptext'+idval).hide();
        $('#editsubschedulebutton'+idval).hide();
    });
    $(document).on( "click", ".savesubschedulebutton", function(){
        var idval=$(this).val();
        var error=0;
        $('.error').hide();
        if($('#editsubschedulename'+idval).val()=='')
        {
            $('#editsubschedulename'+idval).next("span").html('Enter Name').show('slow');
            error=1;
        }

        if(error==0){
            $.ajax({
                type: 'POST',
                url: '../Subschedule/update',
                beforeSend : function(){
                    $('#savesubschedulebutton'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {subscheduleid:idval,name:$('#editsubschedulename'+idval).val(),schedulegrp:$('#editschedulegrp'+idval).val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#editsubschedulename'+data.Id).hide();
                        $('#savesubschedulebutton'+data.Id).hide();
                        $('#editschedulegrp'+data.Id).hide();
                        $('#subscheduletext'+data.Id).text($('#editsubschedulename'+data.Id).val()).show();
                        $('#schedulegrptext'+data.Id).text($('#editschedulegrp'+idval+' option:selected').text()).show();
                        $('#editsubschedulebutton'+data.Id).show();
                        /*$("select#accountgroups option[value='"+data.Id+"']").remove();
                        $('#accountgroups').append($('<option>', {
                            value: data.Id,
                            text: data.Name
                        }));*/

                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#savesubschedulebutton'+data.Id).attr("disabled", false);
                }
            });
        }

    });
    $(document).on( "click", ".deletesubschedulebutton", function(){
        var idval=$(this).val();
        var r = confirm("Are you sure you want to delete this Sub Schedule ?");
        if (r == true) {

            $.ajax({
                type: 'POST',
                url: '../Subschedule/DeleteItem',
                beforeSend : function(){
                    $('#deletesubschedulebutton'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {subscheduleid:idval},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#subschedulerow'+data.Id).remove();
                        $('#listsubschedule').trigger('click');
                        //$("select#searcselecttype option[value='"+data.Id+"']").remove();
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deletesubschedulebutton'+data.Id).attr("disabled", false);
                }
            });

        } else {
            return false;
        }

    });
});