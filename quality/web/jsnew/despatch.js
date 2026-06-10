$(document).on( "click", "#Inwards", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listinwards').trigger('click');
});
$(function(){
    $('#listinwards').click(function(){
        $('#Inwardlistsection').slideDown('slow');// slide down the project listing div
        $('#listinwards').removeClass('btn-danger').addClass('btn-success');
        $('#addinward').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../despatchinward/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {name:$('#searchinwards').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#inwardsitems').html(data.result);
                    $('#inwardstable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#inwardsearch').click(function(){
        $('#listinwards').trigger('click');
    });
    $(document).on('click','.deleteinwardbutton',function(){
        var inwardid=$(this).val();
        var r = confirm("Are you sure you want to delete this Inward ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../despatchinward/Delete/',
                beforeSend : function(){
                    $('#deleteinwardbutton'+inwardid).attr("disabled", true);
                },
                dataType: "json",
                data: {inwardid:inwardid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#inwardrow'+data.Id).remove();
                        $('#listinwards').trigger('click');
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deleteinwardbutton'+data.Id).attr("disabled", false);
                }
            });
        }
        else {
            return false;
        }
    });
});