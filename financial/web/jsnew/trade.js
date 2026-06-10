$(document).on('click','#Trade',function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listtrade').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});
$(function() {
    // project section function
    // list project click
    $('#listtrade').click(function () {
        $('#tradeaddsection').slideUp('slow');// slide down the project listing div
        $('#tradelistsection').slideDown('slow');// slide down the project listing div
        $('#listtrade').removeClass('btn-danger').addClass('btn-success');
        $('#addtrade').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../trade/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {tradename:$('#searchtrade').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#tradeitems').html(data.result);
                    $('#tradetable').show();
                }

                $('.preloader').hide();
            }
        });

    });
    $('#tradesearch').click(function(){
        $('#listtrade').trigger('click')
    });
    $('#addtrade').click(function(){
        $('#tradelistsection').slideUp('slow');// slide down the project listing div
        $('#tradeaddsection').slideDown('slow');// slide down the project listing div
        $('#addtrade').removeClass('btn-danger').addClass('btn-success');
        $('#listtrade').removeClass('btn-success').addClass('btn-danger');
    });
    $('#savetrade').click(function(){
        var error=0;
        $('.error').hide();
        if($('#resource').val()=='none')
        {
            $("#resource").next("span").html('Select Resource').show('slow');
            error=1;
        }
        var name=$('#resource').val();
        var restype=$('#resourcetype').val();
        var rateperday=$('#rateperday').val();
        var otperhour=$('#otperhour').val();

        if(error==0){
            $.ajax({
                type:'POST',
                url:'../trade/create',
                beforeSend:function(){
                    $('#savetrade').attr("disabled", true);
                },
                dataType:'json',
                data: {name:name,restype:restype,rateperday:rateperday,otperhour:otperhour},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#tradeform')[0].reset();
                        $('#listtrade').trigger('click');
                        $('#savetrade').attr("disabled", false);
                    }
                }
            });
        }
    });
});
$(document).on( "change","#resourcetype", function(){
    var restype=$('#resourcetype').val();
    $.ajax({
        type: 'POST',
        url: '../trade/Getresources',
        data: {restype:restype},
        dataType: "json",
        success: function(data){
            $('#resource').html(data.result);
        }
    });
});
$(document).on( "change",".editresourcetype", function(){
    var id=$(this).attr('data-id');
    var restype=$('#editresourcetype'+id).val();
    $.ajax({
        type: 'POST',
        url: '../trade/Getresources',
        data: {restype:restype},
        dataType: "json",
        success: function(data){
            $('#editresource'+id).html(data.result);
        }
    });
});
$(document).on( "click", ".edittradebutton", function(){
    var idval=$(this).val()
    $('#editresourcetype'+idval).show();
    $('#editresource'+idval).show();
    $('#edittraderate'+idval).show();
    $('#edittradeot'+idval).show();
    $('#savetradebutton'+idval).show();
    $('#restypetext'+idval).hide();
    $('#resourcetext'+idval).hide();
    $('#ratetext'+idval).hide();
    $('#ottext'+idval).hide();
    $('#edittradebutton'+idval).hide();
} );
$(document).on( "click", ".savetradebutton", function(){
    var idval=$(this).val();
    var restype=$('#editresourcetype'+idval).val();
    var resource=$('#editresource'+idval).val();
    var rate=$('#edittraderate'+idval).val();
    var ot=$('#edittradeot'+idval).val();
    var error=0;
    $('.error').hide();
    if($('#editresourcetype'+idval).val()=='none')
    {
        $('#editresourcetype'+idval).next("span").html('Select Resource Type').show('slow');
        error=1;
    }
    if($('#editresource'+idval).val()=='none')
    {
        $('#editresource'+idval).next("span").html('Select Resource').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../trade/update',
            beforeSend : function(){
                $('#savetradebutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {tradeid:idval,restype:restype,resource:resource,rate:rate,ot:ot},
            success: function(data){

                if(data.error=='No')
                {
                    $('#editresourcetype'+data.Id).hide();
                    $('#editresource'+data.Id).hide();
                    $('#edittraderate'+data.Id).hide();
                    $('#edittradeot'+data.Id).hide();
                    $('#savetradebutton'+data.Id).hide();
                    $('#restypetext'+data.Id).text(data.restype).show();
                    $('#resourcetext'+data.Id).text(data.Name).show();
                    $('#ratetext'+data.Id).text($('#edittraderate'+data.Id).val()).show();
                    $('#ottext'+data.Id).text($('#edittradeot'+data.Id).val()).show();
                    $('#edittradebutton'+data.Id).show();
                }
                $('#savetradebutton'+data.Id).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deletetradebutton", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Trade ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../trade/Delete',
            beforeSend : function(){
                $('#deletetradebutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {tradeid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#traderow'+data.Id).remove();
                    $('#listtrade').trigger('click');
                }
                $('#deletetradebutton'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
