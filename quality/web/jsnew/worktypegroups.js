/**
 * Created by SolmindsDelli5 on 13-08-2018.
 */
$(document).on( "click", "#worktypegroups", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#listworktypegroups').trigger('click');

});
$(function(){

    $('#listworktypegroups').click(function () {

        $('#worktypegroupsadd').slideUp('slow');// slide down the project listing div

        $('#worktypegroupslistsection').slideDown('slow');// slide down the project listing div

        $('#listworktypegroups').removeClass('btn-danger').addClass('btn-success');

        $('#addworktypegroups').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../engineering/Listworktypegroups',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {name:$('#searchworktypegroups').val()},

            success: function(data){

                if(data.error=='No')
                {
                    $('#worktypegroupsitems').html(data.result);

                    $('#worktypegroupstable').show();
                }
                $('.preloader').hide();
            }
        });

    });

    $('#worktypegroupssearch').click(function(){

        $('#listworktypegroups').trigger('click')

    });

    $('#addworktypegroups').click(function(){

        $('#worktypegroupslistsection').slideUp('slow');// slide down the project listing div

        $('#worktypegroupsadd').slideDown('slow');// slide down the project listing div

        $('#addworktypegroups').removeClass('btn-danger').addClass('btn-success');

        $('#listworktypegroups').removeClass('btn-success').addClass('btn-danger');

    });

    $('#saveworktypegroups').click(function(){

        var error=0;

        $('.error').hide();

        if($('#worktypegroupname').val()=='')

        {

            $("#worktypegroupname").next("span").html('Enter Project Type Name').show('slow');

            error=1;

        }

        if(error==0){

            $.ajax({

                type:'POST',

                url:'../engineering/Addworktypegroup',

                beforeSend:function(){

                    $('#saveworktypegroups').attr("disabled", true);

                },

                dataType:'json',

                data: {worktypegroup:$('#worktypegroupname').val()},

                success:function(data){

                    if(data.error=='No')

                    {

                        $('#worktypegroupsform')[0].reset();

                        $('#listworktypegroups').trigger('click');

                        $('#saveworktypegroups').attr("disabled", false);

                    }

                }

            });

        }

    });

    $( "#worktypegroupsitems" ).sortable({
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
                url: '../engineering/updateworktypegroupssort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){
                    $('#listworktypegroups').trigger('click');
                }
            });
        }

    }).disableSelection()

});

$(document).on( "click", ".editworktypegroupsbutton", function(){

    var idval=$(this).val();

    $('#editworktypegroupsname'+idval).show();

    $('#saveworktypegroupsbutton'+idval).show();

    $('#groupsnametext'+idval).hide();

    $('#editworktypegroupsbutton'+idval).hide();

});

$(document).on( "click", ".saveworktypegroupsbutton", function(){

    var idval=$(this).val();

    var name=$('#editworktypegroupsname'+idval).val();

    var error=0;

    $('.error').hide();

    if($('#editworktypegroupsname'+idval).val()=='')

    {

        $('#editworktypegroupsname'+idval).next("span").html('Enter Workgroup Name').show('slow');

        error=1;

    }

    if(error==0){

        $.ajax({

            type: 'POST',

            url: '../engineering/Updateworktypegroups',

            beforeSend : function(){

                $('#saveworktypegroupsbutton'+idval).attr("disabled", true);

            },

            dataType: "json",

            data: {worktypegroupid:idval,name:name},

            success: function(data){

                if(data.error=='No')

                {

                    $('#editworktypegroupsname'+idval).hide();

                    $('#saveworktypegroupsbutton'+idval).hide();

                    $('#groupsnametext'+idval).text($('#editworktypegroupsname'+idval).val()).show();

                    $('#editworktypegroupsbutton'+idval).show();

                }

                $('#saveworktypegroupsbutton'+idval).attr("disabled", false);

            }

        });

    }

});

$(document).on('click','.deleteworktypegroupsbutton',function(){
    var worktypegroupsid=$(this).val();
    var r = confirm("Are you sure you want to delete this Workgroup?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../engineering/deleteworktypegroups',
            beforeSend : function(){
                $('#deleteworktypegroupsbutton'+worktypegroupsid).attr("disabled", true);
            },
            dataType: "json",
            data: {worktypegroupid:worktypegroupsid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#worktypegroupsrow'+worktypegroupsid).remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deleteworktypegroupsbutton'+worktypegroupsid).attr("disabled", false);
            }
        });
    }
});
