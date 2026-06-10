$(document).on("click", ".prjcttype-tab",function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('.add-project-type-form').hide();
    $('.edit-project-type-form').hide();
    $('.search-and-actions-wrpr').show();
    $('.content-action-wrpr').show();
    $('#estworktypeitems').show();
    $('#searchestworktypename').val('');
    $('#listestworktype').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});
$(function() {
    // project section function
    // list project click
    $('#listestworktype').click(function () {
        $('#estworktypeaddsection').slideUp('slow');// slide down the project listing div
        $('#estworktypelistsection').slideDown('slow');// slide down the project listing div
        $('#listestworktype').removeClass('btn-danger').addClass('btn-success');
        $('#addestworktype').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../projects/listworktype',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {worktypename:$('#searchestworktypename').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#estworktypeitems').html(data.result);
                    $('#estworktypetable').show();
                }

                $('.preloader').hide();
            }
        });

    });
    $('#estworktypesearch').click(function(){
        $('#listestworktype').trigger('click')
    });
    $(document).on("keyup", "#searchestworktypename", function(){
        $('#listestworktype').trigger('click')
    });
    $('#addestworktype').click(function(){
        $("#estworktypename").val('');
        $(".add-project-type-form").show();
        $("#estworktypeitems, .search-and-actions-wrpr").hide();
        $('#estworktypelistsection').slideUp('slow');// slide down the project listing div
        $('#estworktypeaddsection').slideDown('slow');// slide down the project listing div
        $('#addestworktype').removeClass('btn-danger').addClass('btn-success');
        $('#listestworktype').removeClass('btn-success').addClass('btn-danger');
    });
    $('#saveestworktype').click(function(){
        var error=0;
        $('.error').hide();
        if($('#estworktypename').val()=='')
        {
            $("#estworktypename").next("span").html('Enter Project type').show('slow');
            error=1;
        }
        var name=$('#estworktypename').val();

        if(error==0){
            $.ajax({
                type:'POST',
                url:'../projects/createworktype',
                beforeSend:function(){
                    $('#saveestworktype').attr("disabled", true);
                },
                dataType:'json',
                data: {name:name},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#estworktypeform')[0].reset();
                        $(".add-project-type-form").hide();
                        $("#estworktypeitems").show();
                        $(".search-and-actions-wrpr").show();
                        $('.content-action-wrpr').show();
                        $('#listestworktype').trigger('click');
                        $('#saveestworktype').attr("disabled", false);
                    }
                }
            });
        }
    });
        
});

$(document).on( "click", ".editestworktypebutton", function(){
    var idval=$(this).val()
    $('#editestworktype'+idval).show();
    $('#estworktypetext'+idval).hide();
    $('#editestworktypebutton'+idval).hide();
    $('#saveestworktypebutton'+idval).show();
} );
$(document).on( "click", ".saveestworktypebutton", function(){
    var idval=$(this).val();
    var worktype=$('#editestworktype').val();
    var error=0;
    $('.error').hide();
    if($('#editestworktype').val()=='')
    {
        $('#editestworktype').next("span").html('Enter Project type').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../projects/updateworktype',
            beforeSend : function(){
                $('#saveestworktypebutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {worktypeid:idval,worktype:worktype},
            success: function(data){

                if(data.error=='No')
                {
                        $(".add-project-type-form").hide();
                        $(".edit-project-type-form").hide();
                        $("#estworktypeitems").show();
                        $(".search-and-actions-wrpr").show();
                        $('#listestworktype').trigger('click');

                }
                $('#saveestworktypebutton'+data.Id).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deleteestworktypebutton", function(){
    var idval=$(this).data("id");
    var r = confirm("Are you sure you want to delete this Project type ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../projects/deleteworktype',
            beforeSend : function(){
                $('#deleteestworktypebutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {worktypeid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#listestworktype').trigger('click');
                    //$('#estworktyperow'+data.Id).remove();
                    //$('#listworktype').trigger('click');
                }
                $('#deleteestworktypebutton'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});

$(document).on( "click", "#canceladdtype", function(){
    $(".add-project-type-form").hide();
    $(".edit-project-type-form").hide();
    $("#estworktypeitems").show();
    $(".search-and-actions-wrpr").show();
    $('#listestworktype').trigger('click');
    $('.content-action-wrpr').show();
});

$(document).on( "click", ".editForm", function(){
    var idval =  $(this).data("id");
    var protype = $("#protype"+idval).val();
    $(".saveestworktypebutton").val(idval);
    $("#editestworktype").val(protype);
    $(".edit-project-type-form").show();
    $("#estworktypeitems").hide();
    $(".search-and-actions-wrpr").hide();
});
$(function() {
$( "#estworktypeitems" ).sortable({
        placeholder: "ui-state-highlight",
        helper:'clone',
        
        update:function( event, ui ) {
            //alert($(this).index());

            var updatedrows=[];
            $('.projtypes').each(function() {
               var rowid=$(this).attr('data-id');
               var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    rowindex:rowindex
                })
            });
            $.ajax({
                type: 'POST',
                url: '../projects/updateprojecttypesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){
                    $('#estworktypesearch').trigger('click');
                }
            });
        }

    }).disableSelection();
});
 