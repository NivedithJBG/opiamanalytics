/**
 * Created by SolmindsDelli5 on 08-05-2018.
 */
$(document).on( "click", ".listiowschactivity", function(){
    $('#iowschedule').removeClass('active').next().slideUp();
    $('#iowschactivity').addClass('active').next('.acc_container').slideDown();
    var id=$(this).val();
    // alert(id);
    var parents= getiowschParentnames(id);
    // alert(parent.Workgroup_Id);
    $('#iowschprojectnamedisp').html(parents.Project);
    $('#iowschworkgroupnamedisp').html(parents.Workgroup);
    $('#selectedProjectId').val(parents.Project_Id);
    $('#selectedWorkgrouId').val(id);
    $('#iowschwbsid').val(id);
    $('#ProIdiowsch').val(parents.Project_Id);
    $('#listiowsch').trigger('click') ;
});

$(function(){
    $('#listiowsch').click(function(){
        $('#iowschaddsection').slideUp('slow');// slide down the project listing div
        $('#iowschlistsection').slideDown('slow');// slide down the project listing div
        $('#listiowsch').removeClass('btn-danger').addClass('btn-success');
        $('#addiowsch').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../activity/ListiowschActivities',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {Workgroup_Id:$('#iowschwbsid').val(),proid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowschitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });
    $('#addiowsch').click(function(){
        $('#iowschlistsection').slideUp('slow');// slide down the project listing div
        $('#iowschaddsection').slideDown('slow');// slide down the project listing div
        $('#addiowsch').removeClass('btn-danger').addClass('btn-success');
        $('#listiowsch').removeClass('btn-success').addClass('btn-danger');

        $.ajax({
            type: 'POST',
            url: '../activity/AddiowschActivities',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {Workgroup_Id:$('#selectedWorkgrouId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#deletediowschactivity').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });
    $( "#iowschitems" ).sortable({
        items: '.no',
        update:function( event, ui ) {
            //alert($(this).index());
            var updatedrows=[];
            $(this).closest('table').find('tbody tr').each(function (i) {
                var rowid=$(this).attr('data-id');
                var type=$(this).attr('data-type');
                var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    type: type,
                    rowindex:rowindex
                })
            });
            $.ajax({
                type: 'POST',
                url: '../activity/updateiowschsort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection();
});
$(document).on('click','.deleteiowschactivity',function(){
    var actid=$(this).val();
    $('#iowschactivitiesrow'+actid).remove();
});
$(document).on('click','.saveiowschactivitybutton',function(){
    $.ajax({
        type: 'POST',
        url: '../activity/saveiowschactivities',
        beforeSend : function(){
            $('.saveiowschactivitybutton').attr("disabled", true);
        },
        dataType: "json",
        data: $('#iowschactivitysaveform').serialize(),
        success: function(data){
            if(data.error=='No')
            {
                $('#listiowsch').trigger('click');
                $('.saveiowschactivitybutton').attr("disabled", false);
                $('.iowschsuccmsg').html("Activities saved successfully").fadeIn('slow');
                $('.iowschsuccmsg').delay(5000).fadeOut('slow');
            }
        }
    });

});

$(document).on('click','.editiowschactivitybutton',function(){
    var id=$(this).val();
    $('#editiowschactivityname'+id).show();
    $('#editiowschactivityunit'+id).show();
    $('#saveiowschactivitybtn'+id).show();
    $('#iowschactivityname'+id).hide();
    $('#iowschactivityunit'+id).hide();
    $('#editiowschactivitybutton'+id).hide();
    $("input#iowschcheckestimate"+id).removeAttr("disabled");
});

$(document).on('click','.saveiowschactivitybtn',function(){
    var id =$(this).val();
    var name= $('#editiowschactivityname'+id).val();
    var unit= $('#editiowschactivityunit'+id).val();

    var error=0;
    $('.error').hide();
    if($('#editiowschactivityname'+id).val()=='')
    {
        $('#editiowschactivityname'+id).next("span").html('Enter Activity Name').show('slow');
        error=1;
    }
    if($('#editiowschactivityunit'+id).val()=='')
    {
        $('#editiowschactivityunit'+id).next("span").html('Enter Activity Unit').show('slow');
        error=1;
    }
    if ($('#iowschcheckestimate'+id).is(':checked')){
        var estimate = 1;
    }
    else {
        var estimate = 0;
    }

    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../activity/UpdateiowschActivity',
            beforeSend : function(){
                $('#saveiowschactivitybtn').attr("disabled", true);
            },
            dataType: "json",
            data: {id:id,name:name,unit:unit,estimate:estimate},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editiowschactivityname'+data.Id).hide();
                    $('#editiowschactivityunit'+data.Id).hide();
                    $('#saveiowschactivitybtn'+data.Id).hide();
                    $('#iowschactivityname'+data.Id).text(data.Name).show();
                    $('#iowschactivityunit'+data.Id).text(data.Unit).show();
                    $('#editiowschactivitybutton'+data.Id).show();
                    $("input#iowschcheckestimate"+id).attr("disabled", true);
                }
                $('#saveiowschactivitybtn').attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.iowschactdelbtn',function(){
    var id=$(this).val();
    var actid=$(this).attr('data-id');
    var r = confirm("Are you sure you want to delete this Activity?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../activity/deleteiowschactivity',
            beforeSend : function(){
                $('#iowschactdelbtn'+id).attr("disabled", true);
            },
            dataType: "json",
            data: {actid:id},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowschactivitiesrow'+id).remove();
                }
                else
                {
                    alert(data.errortext);
                }
                $('#iowschactdelbtn'+id).attr("disabled", true);
            }
        });
    }

});

$(document).on('click','.addiowschactivity',function(){
    var actid=$(this).val();
    var processid=$(this).attr('data-id');
    var wbsid=$('#iowschwbsid').val();
    var activityunit=$('#addiowschactivunit'+actid).val();
    var projectid=$('#selectedProjectId').val();
    var type=$('#addiowschtype'+actid).val();
    var estimate=$('#addiowschestimate'+actid).val();
    /*if (type=='new'){
        var estimate=$('#addiowschestimate'+actid).val();
    }
    else {
        if ($('#addiowschcheckestimate'+actid).is(':checked')){
            var estimate = 1;
        }
        else {
            var estimate = 0;
        }
    }*/
    var error=0;
    if($('#iowschactivityname'+actid).val()!='' && IowschActivityNameExist($('#iowschactivityname'+actid).val(),wbsid)=='Yes')
    {
        $('#iowschactivityname'+actid).next("span").html('Activity Exists').show('slow');
        error=1;
    }
    var activityname=$('#iowschactivityname'+actid).val();
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../activity/addiowschactivity',
            beforeSend : function(){
                $('.addiowschactivity'+actid).attr("disabled", true);
            },
            dataType: "json",
            data: {actid:actid,processid:processid,wbsid:wbsid,activityunit:activityunit,estimate:estimate,projectid:projectid,type:type,activityname:activityname},
            success: function(data){
                if(data.error=='No')
                {
                    console.log("success request");
                    $('#addiowschactivityrow'+actid).remove();
                    // $('#listtasks').trigger('click');
                    // $('addprotaskbutton').attr("disabled", false);
                }
            }
        });
    }


});

function getiowschParentnames(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../activity/GetiowschParents',
        async:false,
        dataType: "json",
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}

function IowschActivityNameExist(name,wbsid)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../activity/CheckiowschActivityname',
        async:false,
        data: {name:name,wbsid:wbsid},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}