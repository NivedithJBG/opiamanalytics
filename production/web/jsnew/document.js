/**
 * Created by SolmindsDelli5 on 12/12/2016.
 */
$(document).on( "click", "#documents", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listdoc').trigger('click');
});
$(function(){
    $('#listdoc').click(function(){
        $('#doclistsection').slideDown('slow');// slide down the project listing div
        $('#templatelist').slideUp('slow');// slide down the project listing div
        $('#listdoc').removeClass('btn-danger').addClass('btn-success');
        $('#listtemp').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/Docsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {document:$('#searchdocument').val(),project:$('#selectedProjectId').val(),function:$('#function').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#docitems').html(data.result);
                    $('#doctable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#documentsearch').click(function(){
        $('#listdoc').trigger('click')
    });
    $('#listtemp').click(function(){
        $('#templatelist').slideDown('slow');// slide down the project listing div
        $('#doclistsection').slideUp('slow');// slide down the project listing div
        $('#listtemp').removeClass('btn-danger').addClass('btn-success');
        $('#listdoc').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/Templateearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {name:$('#searchtemp').val(),department:$('#tempfunction').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#tempitems').html(data.result);
                    $('#temptable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#tempsearch').click(function(){
        $('#listtemp').trigger('click')
    });
    $('#listuploaddoc').click(function(){
        $('#uploaddoclist').slideDown('slow');// slide down the project listing div
        $('#doclistsection').slideUp('slow');// slide down the project listing div
        $('#listuploaddoc').removeClass('btn-danger').addClass('btn-success');
        $('#listdoc').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/UploadedDocs',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {document:$('#searchuploaddocument').val(),project:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#uploaddocitems').html(data.result);
                    $('#uploaddoctable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#uploaddocumentsearch').click(function(){
        $('#listuploaddoc').trigger('click')
    });

    $( "#uploaddocitems" ).sortable({
        items: '.no',
        update:function( event, ui ) {
            //alert($(this).index());
            var updatedrows=[];
            $(this).closest('table').find('tbody tr').each(function (i) {
                var rowid=$(this).attr('data-id');
                var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    rowindex:rowindex
                })
            });
            $.ajax({
                type: 'POST',
                url: '../DoccumentManager/sortorder',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection()
});

$(document).on( "click", ".editdocument", function(){
    var idval=$(this).val();
    $('#editdoctype'+idval).show();
    $('#edittittle'+idval).show();
    $('#savedocument'+idval).show();
    $('#doctypetext'+idval).hide();
    $('#tittletext'+idval).hide();
    $('#editdocument'+idval).hide();
} );
$(document).on( "click", ".savedocument", function(){
    var idval=$(this).val();
    var name=$('#edittittle'+idval).val();
    var doctype=$('#editdoctype'+idval).val();
    var error=0;
    $('.error').hide();
    if($('#edittittle'+idval).val()=='')
    {
        $('#edittittle'+idval).next("span").html('Enter Subject').show('slow');
        error=1;
    }
    if($('#editdoctype'+idval).val()=='')
    {
        $('#editdoctype'+idval).next("span").html('Select Document Type').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../doccumentManager/Uploaddocupdate',
            beforeSend : function(){
                $('#savetittlebutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {updocid:idval,name:name,doctype:doctype},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editdoctype'+idval).hide();
                    $('#edittittle'+idval).hide();
                    $('#savedocument'+idval).hide();
                    $('#doctypetext'+idval).text(data.doctype).show();
                    $('#tittletext'+idval).text($('#edittittle'+idval).val()).show();
                    $('#editdocument'+idval).show();
                }
                $('#savedocument'+idval).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deletedoc", function(){
    var idval=$(this).val();
    var type=$(this).attr('data-id');
    var r = confirm("Are you sure you want to delete this Document ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/Deletedoc',
            beforeSend : function(){
                $('#deletedoc'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {docid:idval,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#docrow'+idval).remove();
                    $('#listdoc').trigger('click');
                }

                $('#deletedoc'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
$(document).on( "click", ".deletedrawing", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Document ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/Deleteuploaddoc',
            beforeSend : function(){
                $('#deletedrawing'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {drawingid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#drawingrow'+idval).remove();
                    $('#listuploaddoc').trigger('click');
                }

                $('#deletedrawing'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});

/*$(document).on( "click", ".assigndocument", function(){
    var idval=$(this).val();
    $('#function'+idval).show();
    $('#folder'+idval).show();
    $('#savefunction'+idval).show();
    $('#functiontext'+idval).hide();
    $('#foldtext'+idval).hide();
    $('#assigndocument'+idval).hide();
});*/

$(document).on( "click", ".assigndocument", function(){
    var idval=$(this).val();
    var type=$(this).attr('data-id');
    var error=0;
    $('.error').hide();
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../doccumentManager/AssignDoc',
            beforeSend : function(){
                $('#assigndocument'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {docid:idval,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#listdoc').trigger('click');
                }
                $('#assigndocument'+idval).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".documentdespatch", function(){
    var idval=$(this).val();
    var type=$(this).attr('data-id');
    var error=0;
    $('.error').hide();
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../doccumentManager/DocumentDesp',
            beforeSend : function(){
                $('#documentdespatch'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {docid:idval,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#listdoc').trigger('click');
                }
                $('#documentdespatch'+idval).attr("disabled", false);
            }
        });
    }

});
