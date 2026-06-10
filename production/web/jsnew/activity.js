$(document).on( "click", ".listactivity", function(){
    $('#workgroup').removeClass('active').next().slideUp();
    $('#activity').addClass('active').next('.acc_container').slideDown();
    var id=$(this).val();
    var worktypeid=$(this).attr('data-id');
    // alert(id);
    var parents= getParentnames(id);

    $('#projectnamedisplay').html(parents.Project);
    $('#workgroupnamedisplay').html(parents.Workgroup);
    $('#selectedProjectId').val(parents.Project_Id);
    $('#selectedWorkgrouId').val(id);
    $('#selectedWorktypeId').val(worktypeid);
    // alert($('#selectedWorkgrouId').val());
    $('#IOWWorkgroupId2').val(id);
    $('#IOWorkgroupId').val(id);
    $('#ProId').val(parents.Project_Id);
    $('#listiow').trigger('click') ;
});

$(document).on( "click", "#itemofwork", function(){
    if($('#selectedWorkgrouId').val()!='')
    {
        $('.acc_trigger').removeClass('active').next().slideUp();
        $('#itemofwork').addClass('active').next('.acc_container').slideDown();
        // var parents= getParentnames($('#selectedWorkgrouId').val())
        // $('#projectnamedisplay').html(parents.Project);
        // $('#workgroupnamedisplay').html(parents.Workgroup);
        // $('#selectedProjectId').val(parents.Project_Id);
        // $('#selectedWorkgrouId').val(parents.Workgroup_Id);
        $('#listiow').trigger('click') ;
    }
});

$(document).on("click",'#saveiow',function(){
    var error=0;
    $('.error').hide();
    if($('#IOW_Name').val()=='')
    {
        $('#IOW_Name').next("span").html('Enter IOW Name').show('slow');
        error=1;
    }
    if($('#IOW_Name').val()!='' && IOWNameExist($('#IOW_Name').val(),$('#selectedWorkgrouId').val())=='Yes')
    {
        $('#IOW_Name').next("span").html('Work Group Name Exists').show('slow')
        error=1;
    }
    if($('#IOW_Unit').val()=='')
    {
        $('#IOW_Unit').next("span").html('Enter IOW Unit').show('slow');
        error=1;
    }
    if($('#IOW_Quantity').val()=='')
    {
        $('#IOW_Quantity').next("span").html('Enter IOW Quantity').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../activity/create',
            beforeSend : function(){
                $('#saveiow').attr("disabled", true);
            },
            dataType: "json",
            data: {Project_Id:$('#selectedProjectId').val(),Workgroup_Id:$('#selectedWorkgrouId').val(),IOW_Name:$('#IOW_Name').val(),IOW_Unit:$('#IOW_Unit').val(),IOW_Quantity:$('#IOW_Quantity').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#addiowform')[0].reset();
                    $('#listiow').trigger('click');

                    /*$('#workgroupaddsection').slideUp('slow');// slide down the project listing div
                     $('#workgrouplistsection').slideDown('slow');// slide down the project listing div

                     $('#listworkgroup').removeClass('btn-danger').addClass('btn-success');
                     $('#addworkgroup').removeClass('btn-success').addClass('btn-danger');

                     $('#addworkgroup').trigger('click');
                     $('#workgroupsearch').trigger('click')
                     */
                }
                else
                {
                    $('#saveiow').attr("disabled", false);
                }
                $('#saveiow').attr("disabled", false);
            }
        });
    }
});
$(function(){
    /*$( "#iowitems" ).sortable({

        deactivate:function(event, ui){
            //alert('test')
        },
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
                url: '../activity/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection()*/

    $( "#iowitems" ).sortable({
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
                url: '../activity/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection();
    // list iow click
    $('#listiow').click(function(){
        $('#iowaddsection').slideUp('slow');// slide down the project listing div
        $('#iowlistsection').slideDown('slow');// slide down the project listing div
        $('#listiow').removeClass('btn-danger').addClass('btn-success');
        $('#addiow').removeClass('btn-success').addClass('btn-danger');

        // console.log($('#selectedProjectId').val());
        $.ajax({
            type: 'POST',
            url: '../activity/ListActivities',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {Workgroup_Id:$('#IOWWorkgroupId2').val(),proid:$('#selectedProjectId').val(),worktypeid:$('#selectedWorktypeId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });
    // list iow click  \
    // add iow click
    $('#addiow').click(function(){
        $('#iowlistsection').slideUp('slow');// slide down the project listing div
        $('#iowaddsection').slideDown('slow');// slide down the project listing div
        $('#addiow').removeClass('btn-danger').addClass('btn-success');
        $('#listiow').removeClass('btn-success').addClass('btn-danger');

         $.ajax({
            type: 'POST',
            url: '../activity/AddIowActivities',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {Workgroup_Id:$('#selectedWorkgrouId').val(),worktypeid:$('#selectedWorktypeId').val(),activityname:$('#searchenggactname').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#deletedactivityitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });

    $('#enggactsearch').click(function(){

        $('#addiow').trigger('click')

    });
    // add iow click
    // save iow click
    //iow function ends here

});
/*$(document).on('click','.disabled',function(){
    alert('This Project has Items Linked in gant chart. Please delete link for this project to enable sorting.')
});*/
$(document).on('click','.editiowbutton',function(){
        var id=$(this).val()
        $('#editiowname'+id).show();
        $('#editiowunit'+id).show();
        $('#editquantity'+id).show();
        $('#saveiowbutton'+id).show();
        $('#iowtext'+id).hide();
        $('#iowunit'+id).hide();
        $('#iowquantity'+id).hide();
        $('#editiowbutton'+id).hide();
});
$(document).on('click','.saveiowbutton',function(){
    var id =$(this).val();
    var name= $('#editiowname'+id).val();
    var unit= $('#editiowunit'+id).val();
    var quantity= $('#editquantity'+id).val();


    var error=0;
    $('.error').hide();
    if($('#editiowname'+id).val()=='')
    {
        $('#editiowname'+id).next("span").html('Enter IOW Name').show('slow');
        error=1;
    }
    if($('#editiowunit'+id).val()=='')
    {
        $('#editiowunit'+id).next("span").html('Enter IOW Unit').show('slow');
        error=1;
    }
    if($('#editquantity'+id).val()=='')
    {
        $('#editquantity'+id).next("span").html('Enter IOW Quantity').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../activity/update',
            beforeSend : function(){
                $('#saveiow').attr("disabled", true);
            },
            dataType: "json",
            data: {id:id,name:name,unit:unit,quantity:quantity},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editiowname'+data.Id).hide();
                    $('#editiowunit'+data.Id).hide();
                    $('#editquantity'+data.Id).hide();
                    $('#saveiowbutton'+data.Id).hide();
                    $('#iowtext'+data.Id).text(data.Name).show();
                    $('#iowunit'+data.Id).text(data.Unit).show();
                    $('#iowquantity'+data.Id).text(data.Quantity).show();
                    $('#editiowbutton'+data.Id).show();
                }
                $('#saveiow').attr("disabled", false);
            }
        });
    }



});



$(document).on('click','.deleteproactivitybutton',function(){
    var actid=$(this).val();
    // var type=$('#process_id').val();
    
    console.log(actid);
    // console.log(type);
    $('#activityrow'+actid).remove();

});
$(document).on('click','.saveproactivitybutton',function(){
    // console.log(id);
   
   $.ajax({
            type: 'POST',
            url: '../activity/saveactivities', 
            beforeSend : function(){
                $('.saveproactivitybutton').attr("disabled", true);
            },
            dataType: "json",
            // Workgroup_Id:$('#selectedWorkgrouId').val()
            data: $('#proactivitysaveform').serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    console.log("success request");
                    $('#listiow').trigger('click');
                    $('.saveproactivitybutton').attr("disabled", false);
                    $('.succmsg').html("Activities saved successfully").fadeIn('slow');
                    $('.succmsg').delay(5000).fadeOut('slow');
                }
            }
        });
    console.log("successfully saved");

});
$(document).on('click','.adddeletedactivitybutton',function(){
    var estimateid=$(this).val();
    var processid=$(this).attr('data-id');
    var workgp_Id = $('#IOWWorkgroupId2').val();
    // var estimateid = $('#estimateid'+processid+activityid).val();
    // alert('#addactivityrow'+estimateid);
   
    $.ajax({
            type: 'POST',
            url: '../activity/savedeletedtasks',
            // beforeSend : function(){
            //     $('#addprotaskbutton').attr("disabled", true);
            // },
            dataType: "json",
            // Workgroup_Id:$('#selectedWorkgrouId').val()
            data: {processid:processid, WgId:workgp_Id,estimateid:estimateid },
            success: function(data){
                if(data.error=='No')
                {
                    console.log("success request");
                    // $('#listtasks').trigger('click');
                    // $('addprotaskbutton').attr("disabled", false);
                }
            }
        });
        
    // $('#taskrow1'+taskid).remove();
    $('#addactivityrow'+estimateid).remove();

});

$(document).on('click','.editiowactivitybutton',function(){
    var id=$(this).val();
    $('#editiowactivityname'+id).show();
    $('#editiowactivityunit'+id).show();
    $('#saveiowactivitybtn'+id).show();
    $('#iowactivityname'+id).hide();
    $('#iowactivityunit'+id).hide();
    $('#editiowactivitybutton'+id).hide();
    $("input#checkestimate"+id).removeAttr("disabled");
});

$(document).on('click','.saveiowactivitybtn',function(){
    var id =$(this).val();
    var name= $('#editiowactivityname'+id).val();
    var unit= $('#editiowactivityunit'+id).val();
    var type= $(this).attr('data-type');

    var error=0;
    $('.error').hide();
    if($('#editiowactivityname'+id).val()=='')
    {
        $('#editiowactivityname'+id).next("span").html('Enter Activity Name').show('slow');
        error=1;
    }
    if($('#editiowactivityunit'+id).val()=='')
    {
        $('#editiowactivityunit'+id).next("span").html('Enter Activity Unit').show('slow');
        error=1;
    }
    if (type=='new'){
        var estimate=$('#editiowactivityestimate'+id).val();
    }
    else {
        if ($('#checkestimate'+id).is(':checked')){
            var estimate = 1;
        }
        else {
            var estimate = 0;
        }
    }
    /*if ($('#checkestimate'+id).is(':checked')){
        var estimate = 1;
    }
    else {
        var estimate = 0;
    }*/

    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../activity/UpdateIowActivity',
            beforeSend : function(){
                $('#saveiowactivitybtn').attr("disabled", true);
            },
            dataType: "json",
            data: {id:id,name:name,unit:unit,estimate:estimate},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editiowactivityname'+data.Id).hide();
                    $('#editiowactivityunit'+data.Id).hide();
                    $('#saveiowactivitybtn'+data.Id).hide();
                    $('#iowactivityname'+data.Id).text(data.Name).show();
                    $('#iowactivityunit'+data.Id).text(data.Unit).show();
                    $('#editiowactivitybutton'+data.Id).show();
                    $("input#checkestimate"+id).attr("disabled", true);
                }
                $('#saveiowactivitybtn').attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.deleteiowactivity',function(){
    var actid=$(this).val();
    $('#iowactivitiesrow'+actid).remove();
});

$(document).on('click','.iowactdelbtn',function(){
    var id=$(this).val();
    var actid=$(this).attr('data-id');
    var r = confirm("Are you sure you want to delete this Activity?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../activity/deleteiowactivity',
            beforeSend : function(){
                $('#iowactdelbtn'+id).attr("disabled", true);
            },
            dataType: "json",
            data: {actid:id},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowactivitiesrow'+id).remove();
                }
                else
                {
                    alert(data.errortext);
                    $('#iowactdelbtn'+id).attr("disabled", false);
                }
            }
        });
    }

});

$(document).on('click','.saveiowactivitybutton',function(){
    $.ajax({
        type: 'POST',
        url: '../activity/saveiowactivities',
        beforeSend : function(){
            $('.saveproactivitybutton').attr("disabled", true);
        },
        dataType: "json",
        data: $('#iowactivitysaveform').serialize(),
        success: function(data){
            if(data.error=='No')
            {
                $('#listiow').trigger('click');
                $('.saveiowactivitybutton').attr("disabled", false);
                $('.succmsg').html("Activities saved successfully").fadeIn('slow');
                $('.succmsg').delay(5000).fadeOut('slow');
            }
        }
    });

});

$(document).on('click','.addiowactivity',function(){
    var actid=$(this).val();
    var processid=$(this).attr('data-id');
    var wbsid=$('#IOWWorkgroupId2').val();
    var worktypeid=$('#selectedWorktypeId').val();
    var activityunit=$('#activunit'+actid).val();
    var projectid=$('#selectedProjectId').val();
    var type=$('#type'+actid).val();
    if (type=='new'){
        var estimate=$('#estimate'+actid).val();
    }
    else {
        if ($('#checkestimate'+actid).is(':checked')){
            var estimate = 1;
        }
        else {
            var estimate = 0;
        }
    }
    var error=0;
    if($('#iowactivityname'+actid).val()!='' && ActivityNameExist($('#iowactivityname'+actid).val(),wbsid)=='Yes')
    {
        $('#iowactivityname'+actid).next("span").html('Activity Name Exists').show('slow');
        error=1;
    }
    var activityname=$('#iowactivityname'+actid).val();
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../activity/addiowactivity',
            beforeSend : function(){
                $('.addiowactivity'+actid).attr("disabled", true);
            },
            dataType: "json",
            data: {actid:actid,processid:processid,wbsid:wbsid,worktypeid:worktypeid,activityunit:activityunit,estimate:estimate,projectid:projectid,type:type,activityname:activityname},
            success: function(data){
                if(data.error=='No')
                {
                    console.log("success request");
                    $('#addiowactivityrow'+actid).remove();
                    // $('#listtasks').trigger('click');
                    // $('addprotaskbutton').attr("disabled", false);
                }
            }
        });
    }



});

function getParentnames(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../activity/GetParents',
        async:false,
        dataType: "json",
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}
function ActivityNameExist(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../activity/CheckActivityname',
        async:false,
        data: {name:name},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}
