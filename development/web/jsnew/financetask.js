/**
 * Created by SolmindsDelli5 on 17-02-2017.
 */
$(document).on( "click", "#task", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listtasks').trigger('click');
});

$(function(){
    $('#listtasks').click(function(){
        $('#tasklistsection').slideDown('slow');// slide down the project listing div
        $('#completedtasklist').slideUp('slow');// slide down the project listing div
        $('#listtasks').removeClass('btn-danger').addClass('btn-success');
        $('#completedtask').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Task/Searchtask',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {task:$('#searchtask').val(),mode:'finance'},
            success: function(data){
                if(data.error=='no')
                {
                    $('#taskitems').html(data.result);
                    $('#tasktable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#tasksearch').click(function(){
        $('#listtasks').trigger('click');
    });
    $('#completedtask').click(function(){
        $('#tasklistsection').slideUp('slow');// slide down the project listing div
        $('#completedtasklist').slideDown('slow');// slide down the project listing div
        $('#completedtask').removeClass('btn-danger').addClass('btn-success');
        $('#listtasks').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Task/Searchcompleted',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {task:$('#tasktext').val(),mode:'finance'},
            success: function(data){
                if(data.error=='no')
                {
                    $('#completedtaskitems').html(data.result);
                    $('#completedtasktable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#completedtasksearch').click(function(){
        $('#completedtask').trigger('click');
    });

    $( "#taskitems" ).sortable({
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
                    $('#listtasks').trigger('click');
                }

                $('#deletetask'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});