$(document).on('click','#Bank',function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listbank').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});
$(function(){

    $('#listbank').click(function(){
        $('#bankaddsection').slideUp('slow');// slide down the project listing div
        $('#banklistsection').slideDown('slow');// slide down the project listing div
        $('#listbank').removeClass('btn-danger').addClass('btn-success');
        $('#addbank').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Bank/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {accounts:$('#searchaccounts').val(),acntsubgrp:$('#accountgroups').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#bankitems').html(data.result);
                    $('#banktable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });
    $('#addbank').click(function(){
        $('#banklistsection').slideUp('slow');// slide down the project listing div
        $('#bankaddsection').slideDown('slow');// slide down the project listing div
        $('#addbank').removeClass('btn-danger').addClass('btn-success');
        $('#listbank').removeClass('btn-success').addClass('btn-danger');

    });
    $('#savebank').click(function(){
        var error=0;
        $('.error').hide();
        if($('#bankname').val()=='')
        {
            $("#bankname").next("span").html('Enter Bank Name').show('slow');
            error=1;
        }

        if(error==0){
            $.ajax({
                type:'POST',
                url:'../Bank/create',
                beforeSend:function(){
                    $('#savebank').attr("disabled", true);
                },
                dataType:'json',
                data: {bankname:$('#bankname').val()},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#bankform')[0].reset();
                        $('#listbank').trigger('click');
                        $('#savebank').attr("disabled", false);
                        /*$('#searcselecttype').append($('<option>', {
                         value: data.Id,
                         text: data.Name
                         }));*/
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#savebank').attr("disabled", false);
                }
            });
        }
    });
    $(document).on( "click", ".editbankbutton", function(){
        var idval=$(this).val();
        $('#editbankname'+idval).show();
        $('#savebankbutton'+idval).show();
        $('#banktext'+idval).hide();
        $('#editbankbutton'+idval).hide();
    });
    $(document).on( "click", ".savebankbutton", function(){
        var idval=$(this).val();
        var error=0;
        $('.error').hide();
        if($('#editbankname'+idval).val()=='')
        {
            $('#editbankname'+idval).next("span").html('Enter Name').show('slow');
            error=1;
        }
        if(error==0){
            $.ajax({
                type: 'POST',
                url: '../Bank/update',
                beforeSend : function(){
                    $('#savebankbutton'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {bankid:idval,bankname:$('#editbankname'+idval).val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#editbankname'+data.Id).hide();
                        $('#savebankbutton'+data.Id).hide();
                        $('#banktext'+data.Id).text($('#editbankname'+data.Id).val()).show();
                        $('#editbankbutton'+data.Id).show();
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#savebankbutton'+data.Id).attr("disabled", false);
                }
            });
        }

    });
    $(document).on( "click", ".deletebankbutton", function(){
        var idval=$(this).val();
        var r = confirm("Are you sure you want to delete this Bank ?");
        if (r == true) {

            $.ajax({
                type: 'POST',
                url: '../Bank/Delete',
                beforeSend : function(){
                    $('#deletebankbutton'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {bankid:idval},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#bankrow'+data.Id).remove();
                        $('#listbank').trigger('click');
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deletebankbutton'+data.Id).attr("disabled", false);
                }
            });

        } else {
            return false;
        }

    });
});