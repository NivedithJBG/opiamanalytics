$(document).on( "click", "#Outwards", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listoutwards').trigger('click');
});
$(function(){
    $('#listoutwards').click(function(){
        $('#Outwardlistsection').slideDown('slow');// slide down the project listing div
        $('#listoutwards').removeClass('btn-danger').addClass('btn-success');
        $('#addoutward').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../despatchoutward/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {name:$('#searchoutwards').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#outwardsitems').html(data.result);
                    $('#outwardstable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#outwardsearch').click(function(){
        $('#listoutwards').trigger('click');
    });
    $(document).on('click','.deleteoutwardbutton',function(){
        var outwardid=$(this).val();
        var r = confirm("Are you sure you want to delete this Outward ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../despatchoutward/Delete/',
                beforeSend : function(){
                    $('#deleteoutwardbutton'+outwardid).attr("disabled", true);
                },
                dataType: "json",
                data: {outwardid:outwardid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#outwardrow'+data.Id).remove();
                        $('#listoutwards').trigger('click');
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deleteoutwardbutton'+data.Id).attr("disabled", false);
                }
            });
        }
        else {
            return false;
        }
    });
});