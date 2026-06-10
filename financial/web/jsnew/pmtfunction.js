$(document).on('click','#PMT',function(){
        if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
            //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
        }
        if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
            $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
        }
        $('#listpmt').trigger('click');
        return false; //Prevent the browser jump to the link anchor
});
$(function(){
    $('#listpmt').click(function(){
        $('#pmtaddsection').slideUp('slow');// slide down the project listing div
        $('#pmtlistsection').slideDown('slow');// slide down the project listing div
        $('#listpmt').removeClass('btn-danger').addClass('btn-success');
        $('#addpmt').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../materials/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {materialname:$('#searchpmtname').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#pmtitems').html(data.result);
                    $('#pmttable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });

    $('#pmtsearch').click(function(){
        $('#listpmt').trigger('click');
    })
});
$(document).on('click','.deletepmt',function(){
    var id=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../materials/delete/'+id,
        async:false,
        dataType:"json",
        success: function(data){
            if(data.error=='No')
            {
                $('#pmtsearch').trigger('click');
            }
        }
    });
});
