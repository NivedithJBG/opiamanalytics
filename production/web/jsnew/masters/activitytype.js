$(document).on('click','.prjct-acttype',function(){ 
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('.add-activity-type-form').hide();
    $('.edit-activity-type-form').hide();
    $('.search-and-actions-wrpr').show();
    $('.content-action-wrpr').show();
    $('#activitytypeitems').show();
    $('#searchactivitytypename').val('');
    $('#listactivitytype').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});
$(function() {
    // project section function
    // list project click
    $('#listactivitytype').click(function () {
        $('#activitytypeaddsection').slideUp('slow');// slide down the project listing div
        $('#activitytypelistsection').slideDown('slow');// slide down the project listing div
        $('#listactivitytype').removeClass('btn-danger').addClass('btn-success');
        $('#addactivitytype').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../projects/listactivitytype',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {activitytypename:$('#searchactivitytypename').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#activitytypeitems').html(data.result);
                    $('#activitytypetable').show();
                    $('#activitytypeitems table tbody').sortable({
                        placeholder: 'ui-state-highlight',
                        helper: 'clone',
                        update: function(event, ui) {
                            var updatedrows = [];
                            $('#activitytypeitems .activitysort').each(function() {
                                updatedrows.push({ rowid: $(this).data('id'), rowindex: $(this).index() });
                            });
                            $.ajax({ type:'POST', url:'../projects/updateestactivitytypesort', data:{datavalue:updatedrows}, dataType:'json', success:function(){ } });
                        }
                    }).disableSelection();
                }

                $('.preloader').hide();
            }
        });

    });
    $('#activitytypesearch').click(function(){
        $('#listactivitytype').trigger('click')
    });
    $(document).on("keyup", "#searchactivitytypename", function(){
        $('#listactivitytype').trigger('click')
    });
    $('#addactivitytype').click(function(){
        $("#activitytypename").val('');
        $(".search-and-actions-wrpr").hide();
        $('#activitytypelistsection').slideUp('slow');// slide down the project listing div
        $('#activitytypeaddsection').slideDown('slow');// slide down the project listing div
        $('#addactivitytype').removeClass('btn-danger').addClass('btn-success');
        $('#listactivitytype').removeClass('btn-success').addClass('btn-danger');
    });
    $('#saveactivitytype').click(function(){ 
        var error=0;
        $('.error').hide();
        if($('#activitytypename').val()=='')
        {
            $("#activitytypename").next("span").html('Enter Activity Type').show('slow');
            error=1;
        }
        if($('#scheduletype').is(':checked'))
        {
            var schedule_type = 1;
        }
        else{
            var schedule_type = 0;
        }  
        var name=$('#activitytypename').val();

        if(error==0){
            $.ajax({
                type:'POST',
                url:'../projects/createactivitytype',
                beforeSend:function(){
                    $('#saveactivitytype').attr("disabled", true);
                },
                dataType:'json',
                data: {name:name,schedule_type:schedule_type},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#activitytypeform')[0].reset();
                        $("#activitytypeitems").show();
                        $(".search-and-actions-wrpr").show();
                        $('.content-action-wrpr').show();
                        $(".add-activity-type-form").hide();
                        $('#listactivitytype').trigger('click');
                        $('#saveactivitytype').attr("disabled", false);
                    }
                }
            });
        }
    });
    
});

$(document).on( "click", ".editactivitytypebutton", function(){
    var idval=$(this).val()
    $('#editactivitytype'+idval).show();
    $('#activitytypetext'+idval).hide();
    $('#editactivitytypebutton'+idval).hide();
    $('#saveactivitytypebutton'+idval).show();
} );

$(document).on( "click", ".saveactivitytypebutton", function(){
    var idval=$(this).val();
    var activitytype=$('#editactivitytype').val();
    var error=0;
    $('.error').hide();
    if($('#editactivitytype').val()=='')
    {
        
        $('#editactivitytype').next("span").html('Enter Activity Type').show('slow');
        error=1;
    }
    if($('#editscheduletype').is(':checked'))
    {
        var schedule_type = 1;
    }
    else{
        var schedule_type = 0;
    } 
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../projects/updateactivitytype',
            beforeSend : function(){
                $('#saveactivitytypebutton').attr("disabled", true);
            },
            dataType: "json",
            data: {activitytypeid:idval,activitytype:activitytype,schedule_type:schedule_type},
            success: function(data){

                if(data.error=='No')
                {
                        $('#activitytypeform')[0].reset();
                        $("#activitytypeitems").show();
                        $(".search-and-actions-wrpr").show();
                        $(".edit-activity-type-form").hide();
                        $('#listactivitytype').trigger('click');
                        $('#saveactivitytype').attr("disabled", false);
                }
                $('#saveactivitytypebutton'+data.Id).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deleteactivitytypebutton", function(){
    var idval=$(this).data("id");
    var r = confirm("Are you sure you want to delete this Activity Type ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../projects/deleteactivitytype',
            beforeSend : function(){
                $('#deleteactivitytypebutton').attr("disabled", true);
            },
            dataType: "json",
            data: {activitytypeid:idval},
            success: function(data){
                if(data.error=='No')
                {
                        $('#listactivitytype').trigger('click');
                }
                $('#deleteactivitytypebutton'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});

$(document).on('click','#addactivitytype',function(){
    $("#activitytypeitems").hide();
    $(".add-activity-type-form").show();
});
$(document).on('click','#cancelactivitytype',function(){
    $("#activitytypeitems").show();
    $(".add-activity-type-form").hide();
    $('.content-action-wrpr').show();
});

$(document).on( "click", ".editactivitytypebutton", function(){ 
    var idval =  $(this).data("id");
    var schval =  $(this).attr('data-sch'); 
    var acttype = $("#activitype"+idval).val();
    if(schval==1)
    {
        $('#editscheduletype').prop('checked', true);
    }else if(schval==0){
        $('#editscheduletype').prop('checked', false);
    }
    $(".saveactivitytypebutton").val(idval);
    $("#editactivitytype").val(acttype);

    $(".edit-activity-type-form").show();
    $("#activitytypeitems").hide();
});

$(document).on( "click", "#canceladdtype", function(){
    $(".add-activity-type-form").hide();
    $(".edit-activity-type-form").hide();
    $("#estworktypeitems").show();
    $(".search-and-actions-wrpr").show();
    $("#activitytypeitems").show();
    $('#listactivitytype').trigger('click');
});

