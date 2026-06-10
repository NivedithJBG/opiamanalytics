/**
 * Created by SolmindsDelli5 on 05-02-2019.
 */
$(document).on( "click", "#enggtask", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listenggtask').trigger('click');
});

$(function(){
    $('#listenggtask').click(function(){
        $('#enggtasklistsection').slideDown('slow');// slide down the project listing div
        $('#completedenggtasklist').slideUp('slow');// slide down the project listing div
        $('#listenggtask').removeClass('btn-danger').addClass('btn-success');
        $('#completedenggtask').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Task/Searchtask',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {task:$('#searchenggtask').val(),mode:'engineering'},
            success: function(data){
                if(data.error=='no')
                {
                    $('#enggtaskitems').html(data.result);
                    $('#enggtasktable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#enggtasksearch').click(function(){
        $('#listenggtask').trigger('click');
    });
    $('#completedenggtask').click(function(){
        $('#enggtasklistsection').slideUp('slow');// slide down the project listing div
        $('#completedenggtasklist').slideDown('slow');// slide down the project listing div
        $('#completedenggtask').removeClass('btn-danger').addClass('btn-success');
        $('#listenggtask').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Task/Searchcompleted',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {task:$('#taskenggtext').val(),mode:'engineering'},
            success: function(data){
                if(data.error=='no')
                {
                    $('#completedenggtaskitems').html(data.result);
                    $('#completedenggtasktable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#completedenggtasksearch').click(function(){
        $('#completedenggtask').trigger('click');
    });

    $( "#enggtaskitems" ).sortable({
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
                    $('#listenggtask').trigger('click');
                }

                $('#deletetask'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
