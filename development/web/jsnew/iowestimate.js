/**
 * Created by SolmindsDelli5 on 30-04-2018.
 */
$(document).on( "click", ".viewiowestimate", function(){

    $('#workgroup').removeClass('active').next().slideUp();

    $('#iowestimate').addClass('active').next('.acc_container').slideDown();

    var id= $(this).val();
    var projid= $(this).attr('data-id');

    $('#iowestimateProject_Id').val(projid);
    $('#iowestimatewbsid').val(id);

    $('#listiowestimateitems').trigger('click') ;
});
$(function(){
    $('#listiowestimateitems').click(function(){

        $.ajax({
            type: 'POST',
            url: '../projects/Listiowestimateitems',
            beforeSend : function(){
                $('.preloaderitems').show();
            },
            dataType: "json",
            data: {wbsid:$('#iowestimatewbsid').val(),projectid:$('#iowestimateProject_Id').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowaddedproducts').html(data.result);
                    $('#iowestimatetable').show();
                    $("span#wbsname h4").html(data.wbsname);
                }

                $('.preloaderitems').hide();
            }
        });
    });
});

$(document).on( "click","#saveiowproduct", function(){
    $.ajax({
        type: 'POST',
        url: '../projects/Pricingestimate',
        beforeSend : function(){
            $('#saveiowproduct').attr("disabled", true);
        },
        dataType: "json",
        data: $('#iowestimateform').serialize(),
        success: function(data){
            if(data.error=='No')
            {
                alert('Saved');
                $('#listiowestimateitems').trigger('click') ;
            }

            $('#saveiowproduct').attr("disabled", false);
        }
    });
});