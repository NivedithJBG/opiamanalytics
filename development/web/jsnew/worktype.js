/**
 * Created by SolmindsDelli5 on 09-01-2018.
 */

$(document).on( "click", ".enggworktypes", function(){

    $('#worktypegroups').removeClass('active').next().slideUp();

    $('#worktype').addClass('active').next('.acc_container').slideDown();

    var id= $(this).val();

    $('#worktypegroupid').val(id);

    //$('#enggworktypegroupname').html(getWorktypegroupname(id));

    $('#listworktype').trigger('click');

});

/*$(document).on( "click", "#worktype", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#listworktype').trigger('click');

});*/

$(function(){

    $('#listworktype').click(function () {

        $('#worktypeadd').slideUp('slow');// slide down the project listing div

        $('#worktypelistsection').slideDown('slow');// slide down the project listing div

        $('#listworktype').removeClass('btn-danger').addClass('btn-success');

        $('#addworktype').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../engineering/Listworktype',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {workgroupid:$('#worktypegroupid').val(),name:$('#searchworktype').val()},

            success: function(data){

                if(data.error=='No')
                {
                    $('#worktypeitems').html(data.result);
                    $('#projecttpe').html(data.worktypegroupname);

                    $('#worktypetable').show();
                }
                $('.preloader').hide();
            }
        });

    });

    $('#worktypesearch').click(function(){

        $('#listworktype').trigger('click')

    });

    $('#addworktype').click(function(){

        $('#worktypelistsection').slideUp('slow');// slide down the project listing div

        $('#worktypeadd').slideDown('slow');// slide down the project listing div

        $('#addworktype').removeClass('btn-danger').addClass('btn-success');

        $('#listworktype').removeClass('btn-success').addClass('btn-danger');

    });

    $('#saveworktype').click(function(){

        var error=0;

        $('.error').hide();

        if($('#worktypename').val()=='')

        {

            $("#worktypename").next("span").html('Enter IOW Type').show('slow');

            error=1;

        }

        if(error==0){

            $.ajax({

                type:'POST',

                url:'../engineering/Addworktype',

                beforeSend:function(){

                    $('#saveworktype').attr("disabled", true);

                },

                dataType:'json',

                data: {worktypegroup:$('#worktypegroupid').val(),worktype:$('#worktypename').val()},

                success:function(data){

                    if(data.error=='No')

                    {

                        $('#worktypeform')[0].reset();

                        $('#listworktype').trigger('click');

                        $('#saveworktype').attr("disabled", false);

                    }

                }

            });

        }

    });

    $( "#worktypeitems" ).sortable({
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
                url: '../engineering/updateworktypesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){
                    $('#listworktype').trigger('click');
                }
            });
        }

    }).disableSelection()

});

$(document).on( "click", ".editworktypebutton", function(){

    var idval=$(this).val();

    $('#editworktypegroup'+idval).show();
    $('#editworktypename'+idval).show();

    $('#saveworktypebutton'+idval).show();

    $('#worktypegrouptext'+idval).hide();
    $('#nametext'+idval).hide();

    $('#editworktypebutton'+idval).hide();

});

$(document).on( "click", ".saveworktypebutton", function(){

    var idval=$(this).val();

    var name=$('#editworktypename'+idval).val();
    var worktypegroup=$('#editworktypegroup'+idval).val();

    var error=0;

    $('.error').hide();

    if($('#editworktypename'+idval).val()=='')

    {

        $('#editworktypename'+idval).next("span").html('Enter Work Type').show('slow');

        error=1;

    }

    if(error==0){

        $.ajax({

            type: 'POST',

            url: '../engineering/Updateworktype',

            beforeSend : function(){

                $('#saveworktypebutton'+idval).attr("disabled", true);

            },

            dataType: "json",

            data: {worktypegroup:worktypegroup,worktypeid:idval,name:name},

            success: function(data){

                if(data.error=='No')

                {

                    //$('#editworktypegroup'+idval).hide();
                    $('#editworktypename'+idval).hide();

                    $('#saveworktypebutton'+idval).hide();

                    //$('#worktypegrouptext'+idval).text(data.workgroup).show();
                    $('#nametext'+idval).text($('#editworktypename'+idval).val()).show();

                    $('#editworktypebutton'+idval).show();

                }

                $('#saveworktypebutton'+idval).attr("disabled", false);

            }

        });

    }

});

$(document).on('click','.deleteworktypebutton',function(){
    var worktypeid=$(this).val();
    var r = confirm("Are you sure you want to delete this Work Type?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../engineering/deleteworktype',
            beforeSend : function(){
                $('#deleteworktypebutton'+worktypeid).attr("disabled", true);
            },
            dataType: "json",
            data: {worktypeid:worktypeid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#worktyperow'+worktypeid).remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deleteworktypebutton'+worktypeid).attr("disabled", false);
            }
        });
    }
});
function getWorktypegroupname(id)

{

    var retval;

    $.ajax({

        type: 'POST',

        url: '../engineering/Getworktypegroupname',

        async:false,

        data: {id:id},

        success: function(data){

            retval=data;

        }

    });

    return retval;

}