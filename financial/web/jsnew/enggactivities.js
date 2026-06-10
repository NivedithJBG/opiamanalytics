/**
 * Created by SolmindsDelli5 on 10-01-2018.
 */

$(document).on( "click", ".enggactivities", function(){

    $('#worktype').removeClass('active').next().slideUp();

    $('#enggactivities').addClass('active').next('.acc_container').slideDown();

    var id= $(this).val();

    $('#worktypeid').val(id);

    $('#enggworktypename').html(getWorktypename(id));

    $('#listenggactivities').trigger('click');
});

$(function(){
    $('#listenggactivities').click(function () {

        $('#enggactivitiesadd').slideUp('slow');// slide down the project listing div

        $('#enggactivitieslistsection').slideDown('slow');// slide down the project listing div

        $('#listenggactivities').removeClass('btn-danger').addClass('btn-success');

        $('#addenggactivities').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../engineering/ListActivities',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {worktypeid:$('#worktypeid').val()},

            success: function(data){

                if(data.error=='No')
                {
                    $('#enggactivitiesitems').html(data.result);

                    $('#enggactivitiestable').show();
                }
                $('.preloader').hide();
            }
        });

    });

    $('#addenggactivities').click(function(){

        $('#enggactivitieslistsection').slideUp('slow');// slide down the project listing div

        $('#enggactivitiesadd').slideDown('slow');// slide down the project listing div

        $('#addenggactivities').removeClass('btn-danger').addClass('btn-success');

        $('#listenggactivities').removeClass('btn-success').addClass('btn-danger');

    });

    $('#saveenggactivities').click(function(){

        var error=0;

        $('.error').hide();

        if($('#enggactivitiesname').val()=='')
        {
            $("#enggactivitiesname").next("span").html('Enter Activity Name').show('slow');
            error=1;
        }

        if($('#enggactivitiesunit').val()=='')
        {
            $("#enggactivitiesunit").next("span").html('Enter Activity Unit').show('slow');
            error=1;
        }

        if($('#enggprocess').val()=='none')
        {
            $("#enggprocess").next("span").html('Select Process').show('slow');
            error=1;
        }

        if ($('#estimate').is(':checked')){
            var estimate = 1;
        }
        else {
            var estimate = 0;
        }
        
        if ($('#schedule').is(':checked')){
            var schedule = 1;
        }
        else {
            var schedule = 0;
        }

        if(error==0){

            $.ajax({
                type:'POST',
                url:'../engineering/Addactivities',
                beforeSend:function(){
                    $('#saveenggactivities').attr("disabled", true);
                },
                dataType:'json',
                data: {worktype:$('#worktypeid').val(),activityname:$('#enggactivitiesname').val(),activityunit:$('#enggactivitiesunit').val(),activityprocess:$('#enggprocess').val(),estimate:estimate,schedule:schedule},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#enggactivitiesform')[0].reset();
                        $('#listenggactivities').trigger('click');
                        $('#saveenggactivities').attr("disabled", false);
                    }
                }
            });
        }
    });

    $( "#enggactivitiesitems" ).sortable({
        items: '.no',
        update:function( event, ui ) {
            //alert($(this).index());
            var updatedrows=[];
            $(this).closest('table').find('tbody tr').each(function (i) {
                var rowid=$(this).attr('data-id');
                var rowtype=$(this).attr('data-type');
                var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    rowindex:rowindex,
                    rowtype:rowtype
                })
            });

            $.ajax({
                type: 'POST',
                url: '../engineering/updatesctivitysort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){
                    $('#listworktype').trigger('click');
                }
            });
        }

    }).disableSelection()
});

$(document).on('click','.editenggactivitybutton',function(){
    var id=$(this).val();
    var type=$(this).attr('data-type');
    type = type.replace(/ +/g, "");
    $('#edit'+type+'activityname'+id).show();
    $('#edit'+type+'activityunit'+id).show();
    $('#edit'+type+'activityprocess'+id).show();
    //$('#editenggprocess'+id).show();
    $("input#estimate"+id).removeAttr("disabled");
    $("input#schedule"+id).removeAttr("disabled");
    $('#save'+type+'activitybutton'+id).show();
    $('#'+type+'activityname'+id).hide();
    $('#'+type+'activityunit'+id).hide();
    $('#'+type+'activityprocess'+id).hide();
    //$('#'+type+'process'+id).hide();
    $('#edit'+type+'activitybut'+id).hide();
});

$(document).on('click','.saveenggactivitybutton',function(){
    var id =$(this).val();
    var type =$(this).attr('data-type');
    type = type.replace(/ +/g, "");
    var name= $('#edit'+type+'activityname'+id).val();
    var unit= $('#edit'+type+'activityunit'+id).val();
    var process_id= $('#edit'+type+'activityprocess'+id).val();

    var error=0;
    $('.error').hide();
    if($('#edit'+type+'activityname'+id).val()=='')
    {
        $('#edit'+type+'activityname'+id).next("span").html('Enter Activity Name').show('slow');
        error=1;
    }
    if($('#edit'+type+'activityunit'+id).val()=='')
    {
        $('#edit'+type+'activityunit'+id).next("span").html('Enter Activity Unit').show('slow');
        error=1;
    }
    if($('#edit'+type+'activityprocess'+id).val()=='none')
    {
        $('#edit'+type+'activityprocess'+id).next("span").html('Select Process').show('slow');
        error=1;
    }

    if ($('#estimate'+id).is(':checked')){
        var estimate = 1;
    }
    else {
        var estimate = 0;
    }
    
    if ($('#schedule'+id).is(':checked')){
        var schedule = 1;
    }
    else {
        var schedule = 0;
    }

    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../engineering/ActivityUpdate',
            beforeSend : function(){
                $('#saveenggactivitybutton'+id).attr("disabled", true);
            },
            dataType: "json",
            data: {id:id,name:name,unit:unit,type:type,estimate:estimate,schedule:schedule,process_id:process_id},
            success: function(data){
                if(data.error=='No')
                {
                    $('#edit'+type+'activityname'+data.Id).hide();
                    $('#edit'+type+'activityunit'+data.Id).hide();
                    $('#edit'+type+'activityprocess'+data.Id).hide();
                    $('#save'+type+'activitybutton'+data.Id).hide();
                    $('#'+type+'activityname'+data.Id).text(data.Name).show();
                    $('#'+type+'activityunit'+data.Id).text(data.Unit).show();
                    $('#'+type+'activityprocess'+data.Id).text(data.Process).show();
                    $('#edit'+type+'activitybut'+data.Id).show();
                    $("input#estimate"+id).attr("disabled", true);
                    $("input#schedule"+id).attr("disabled", true);
                }
                $('#save'+type+'activitybutton'+id).attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.deleteenggactivity',function(){
    var id=$(this).val();
    var type =$(this).attr('data-type');
    var r = confirm("Are you sure you want to delete this Activity ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../engineering/Activitydelete/',
            async:false,
            dataType:"json",
            data: {id:id,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#listenggactivities').trigger('click');
                }
                else {
                    //alert('Cannot delete this activity as it is used in Pricing and Scheduling');
                    alert(data.message);
                }
            }
        });
    }
});

function getWorktypename(id)

{

    var retval;

    $.ajax({

        type: 'POST',

        url: '../engineering/Getname',

        async:false,

        data: {id:id},

        success: function(data){

            retval=data;

        }

    });

    return retval;

}