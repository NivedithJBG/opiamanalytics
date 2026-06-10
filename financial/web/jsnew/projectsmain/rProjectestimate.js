$(document).on( "click", ".R-ProjectEstimate", function(){
    $('#listEstimateR').trigger('click') ;
});

$(function(){ 
    $('#listEstimateR').click(function(){
        $.ajax({
            type: 'POST',
            url: '../projectsmain/listestimateitemsreport',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#estimateProject_Id').val(),estimatestrucid:$('#estimatestructurelist').val()},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#estimatestructurelist').html(data.result);
                    $('#estimateProject_Id').val(data.projectid);
                    $("span#projectname").html(getProjectname(data.projectid));
                    $('#processwise').html(data.result);
                    $('#projectvalue').val(data.projectvalue);
                    $('#estimateitems1').html(data.items);
                    $('#estimatetable').show();
                }

                $('.preloader').hide();
            }
        });
    });

    $(document).on( "click", ".estimateallocationView", function(){
        var Process =$(this).data("process");
        var activityid =$(this).data("activity");
        var project_estimate_Id =$(this).data("proestimate");
        var Project_Id = $(this).data("project");
        var iowName = $(this).data("iowname");
        $.ajax({
            type: 'POST',
            url: '../estimateprojectmain/activityresourcesreport',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {Process:Process,activityid:activityid,project_estimate_Id:project_estimate_Id,Project_Id:Project_Id,iowName:iowName},
            success: function(data){
                if(data.error=='No')
                {
                    
                    $('#projectnameDetails').html(data.acivityhead);
                    $('#projectnameDetails2').html(data.acivityhead2);
                    $('#projectname').hide();
                    $('#estimatereports').hide();
                    $('.projectnameDetails').show();
                    $('#estimatedetails').show();
                    $('.drilldown2').show();
                    $('#estimateitemsview').html(data.result);
                   // $('#projectvalue').val(data.projectvalue);
                   // $('#estimateitems').html(data.items);
                    $('#estimateitemsview').show();
                }

                $('.preloader').hide();
            }
        });
    });
    $(document).on( "click", ".drilldown2", function(){
        $('#estimatereports').show();
        $('#estimatedetails').hide();
        $('.projectnameDetails').hide();
        $('#projectname').show();
        $('.drilldown2').hide();
    });

});

function getProjectname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/getname',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}