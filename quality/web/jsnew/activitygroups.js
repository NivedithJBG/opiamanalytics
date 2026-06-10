$(document).on('click','#Activitygroup',function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listactivitygrp').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});
$(function(){
    $('#listactivitygrp').click(function(){
        $('#activitygrpaddsection').slideUp('slow');// slide down the project listing div
        $('#activitygrplistsection').slideDown('slow');// slide down the project listing div
        $('#listactivitygrp').removeClass('btn-danger').addClass('btn-success');
        $('#addactivitygrp').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../ActivityGroup/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {activitygrpname:$('#searchactivitygrps').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#activitygrpitems').html(data.result);
                    $('#activitygrptable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });
    $('#activitygrpsearch').click(function(){
        $('#listactivitygrp').trigger('click')
    });
    $('#addactivitygrp').click(function(){
        $('#activitygrplistsection').slideUp('slow');// slide down the project listing div
        $('#activitygrpaddsection').slideDown('slow');// slide down the project listing div
        $('#addactivitygrp').removeClass('btn-danger').addClass('btn-success');
        $('#listactivitygrp').removeClass('btn-success').addClass('btn-danger');
    });
    $('#saveactivitygrp').click(function(){
        var error=0;
        $('.error').hide();
        if($('#activitygrpname').val()=='')
        {
            $("#activitygrpname").next("span").html('Enter Activity Groups Name').show('slow');
            error=1;
        }
        if(error==0){
            $.ajax({
                type:'POST',
                url:'../ActivityGroup/create',
                beforeSend:function(){
                    $('#saveactivitygrp').attr("disabled", true);
                },
                dataType:'json',
                data: {activitygrpname:$('#activitygrpname').val()},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#activitygrpform')[0].reset();
                        $('#listactivitygrp').trigger('click');
                        $('#saveactivitygrp').attr("disabled", false);
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#saveactivitygrp').attr("disabled", false);
                }
            });
        }
    });
});
$(document).on( "click", ".editactgroupbutton", function(){
    var idval=$(this).val();
    $('#editactgroupname'+idval).show();
    $('#saveactgroupbutton'+idval).show();
    $('#actgrouptext'+idval).hide();
    $('#editactgroupbutton'+idval).hide();
});
$(document).on( "click", ".saveactgroupbutton", function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editactgroupname'+idval).val()=='')
    {
        $('#editactgroupname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../ActivityGroup/update',
            beforeSend : function(){
                $('#saveactgroupbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {actgrpsid:idval,name:$('#editactgroupname'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editactgroupname'+data.Id).hide();
                    $('#saveactgroupbutton'+data.Id).hide();
                    $('#actgrouptext'+data.Id).text($('#editactgroupname'+data.Id).val()).show();
                    $('#editactgroupbutton'+data.Id).show();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#saveactgroupbutton'+data.Id).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deleteactgroupbutton", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Activity group ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../ActivityGroup/DeleteItem',
            beforeSend : function(){
                $('#deleteactgroupbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {actgrpsid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#actgroupsrow'+data.Id).remove();
                    $('#listactivitygrp').trigger('click');
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deleteactgroupbutton'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});