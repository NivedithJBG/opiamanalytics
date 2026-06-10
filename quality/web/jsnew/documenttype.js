/**
 * Created by SolmindsDelli5 on 1/2/2017.
 */
$(document).on('click','#documenttype',function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listdocumenttype').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});
$(function(){
    $('#listdocumenttype').click(function () {
        $('#documenttypeadd').slideUp('slow');// slide down the project listing div
        $('#documenttypelistsection').slideDown('slow');// slide down the project listing div
        $('#listdocumenttype').removeClass('btn-danger').addClass('btn-success');
        $('#adddocumenttype').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../doccumentManager/Listdocumenttype',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {name:$('#searchdocumenttype').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#documenttypeitems').html(data.result);
                    $('#documenttypetable').show();
                }

                $('.preloader').hide();
            }
        });

    });
    $('#documenttypesearch').click(function(){
        $('#listdocumenttype').trigger('click')
    });
    $('#adddocumenttype').click(function(){
        $('#documenttypelistsection').slideUp('slow');// slide down the project listing div
        $('#documenttypeadd').slideDown('slow');// slide down the project listing div
        $('#adddocumenttype').removeClass('btn-danger').addClass('btn-success');
        $('#listdocumenttype').removeClass('btn-success').addClass('btn-danger');
    });
    $('#savedocumenttype').click(function(){
        var error=0;
        $('.error').hide();
        if($('#documenttypename').val()=='')
        {
            $("#documenttypename").next("span").html('Enter Document Type').show('slow');
            error=1;
        }
        var doctype=$('#documenttypename').val();

        if(error==0){
            $.ajax({
                type:'POST',
                url:'../doccumentManager/AddDocumenttype',
                beforeSend:function(){
                    $('#savedocumenttype').attr("disabled", true);
                },
                dataType:'json',
                data: {doctype:doctype},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#documenttypeform')[0].reset();
                        $('#listdocumenttype').trigger('click');
                        $('#savedocumenttype').attr("disabled", false);
                    }
                }
            });
        }
    });
});

$(document).on( "click", ".editdocumenttypebutton", function(){
    var idval=$(this).val();
    $('#editdocumenttypename'+idval).show();
    $('#savedocumenttypebutton'+idval).show();
    $('#nametext'+idval).hide();
    $('#editdocumenttypebutton'+idval).hide();
} );
$(document).on( "click", ".savedocumenttypebutton", function(){
    var idval=$(this).val();
    var name=$('#editdocumenttypename'+idval).val();
    var error=0;
    $('.error').hide();
    if($('#editdocumenttypename'+idval).val()=='')
    {
        $('#editdocumenttypename'+idval).next("span").html('Enter Document Type').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../doccumentManager/Documenttypeupdate',
            beforeSend : function(){
                $('#savedocumenttypebutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {documenttypeid:idval,name:name},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editdocumenttypename'+idval).hide();
                    $('#savedocumenttypebutton'+idval).hide();
                    $('#nametext'+idval).text($('#editdocumenttypename'+idval).val()).show();
                    $('#editdocumenttypebutton'+idval).show();
                }
                $('#savedocumenttypebutton'+idval).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deletedocumenttypebutton", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Document Type ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../doccumentManager/Deletedocumenttype',
            beforeSend : function(){
                $('#deletedocumenttypebutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {documenttypeid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#documenttyperow'+data.Id).remove();
                    $('#listdocumenttype').trigger('click');
                }
                $('#deletedocumenttypebutton'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
