/**
 * Created by SolmindsDelli5 on 31-07-2018.
 */

$(document).on( "click", ".viewOptasks", function(){

    $('#rproject').removeClass('active').next().slideUp();

    $('#opprojtasks').addClass('active').next('.acc_container').slideDown();

    var id= $(this).val();

    $('#selectedProjectId').val(id);

    $('#opprojtaskprojectname').html(getProjectname(id));

    $('#listoptasks').trigger('click');

});

$(function(){

    $('#listoptasks').click(function(){

        $('#optasklistsection').slideDown('slow');// slide down the project listing div

        $('#opcompletedtasklist').slideUp('slow');// slide down the project listing div

        $('#listoptasks').removeClass('btn-danger').addClass('btn-success');

        $('#opcompletedtask').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../Task/Searchtask',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {task:$('#searchoptask').val(),mode:'operations'},

            success: function(data){

                if(data.error=='no')

                {

                    $('#optaskitems').html(data.result);

                    $('#optasktable').show();

                }

                $('.preloader').hide();

            }

        });

    });

    $('#optasksearch').click(function(){

        $('#listoptasks').trigger('click');

    });

    $('#opcompletedtask').click(function(){

        $('#optasklistsection').slideUp('slow');// slide down the project listing div

        $('#opcompletedtasklist').slideDown('slow');// slide down the project listing div

        $('#opcompletedtask').removeClass('btn-danger').addClass('btn-success');

        $('#listoptasks').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../Task/Searchcompleted',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {task:$('#optasktext').val(),mode:'operations'},

            success: function(data){

                if(data.error=='no')

                {

                    $('#opcompletedtaskitems').html(data.result);

                    $('#opcompletedtasktable').show();

                }

                $('.preloader').hide();

            }

        });

    });

    $('#opcompletedtasksearch').click(function(){

        $('#opcompletedtask').trigger('click');

    });

    $( "#optaskitems" ).sortable({
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
                url: '../Task/updatetasksort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){
                    //$('#listtasks').trigger('click');
                }
            });
        }

    }).disableSelection()

});

$(document).on( "click", ".deletetask", function(){

    var idval=$(this).val();

    var r = confirm("Are you sure you want to delete this Task ?");

    if (r == true) {



        $.ajax({

            type: 'POST',

            url: '../Task/Deletetask',

            beforeSend : function(){

                $('#deletetask'+idval).attr("disabled", true);

            },

            dataType: "json",

            data: {taskid:idval},

            success: function(data){

                if(data.error=='No')

                {

                    $('#taskrow'+idval).remove();

                    $('#opcompletedtask').trigger('click');

                }



                $('#deletetask'+idval).attr("disabled", false);

            }

        });



    } else {

        return false;

    }



});
