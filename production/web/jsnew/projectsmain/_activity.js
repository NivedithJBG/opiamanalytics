$(document).on( "click", ".listactivity", function(e){
    e.preventDefault();
    $('.close-activity-list-btn').trigger('click');
    $('#wbs_estimate_block').hide();
    $('#project-activities-Body').show();
    //$('#project-activities-Body-NOData').hide();
    var id=$(this).attr('data-v');//alert(id);
    var worktypeid=$(this).attr('data-id');
    var parents= getParentnames(id);
    $('#selectedProjectId').val(parents.Project_Id);
    $('#workgroupnamedisplay').html(parents.Workgroup);
    $('#selectedWorkgrouId').val(id);
    $('#selectedWorktypeId').val(worktypeid);
     //alert($('#selectedWorkgrouId').val());
    $('#IOWWorkgroupId2').val(id);
    $('#IOWorkgroupId').val(id);
    $('#ProId').val(parents.Project_Id);
    $('#listiow').trigger('click') ;
    $('.project-activities-list-wrpr').addClass('project-activities-add-form-active');
    $('.activity-results-wrpr').addClass('col-md-9');
    $('#projects-Activity-Search-Lists').html('<div class="row"><div class="col-md-12"><div class="text-center" style="padding-top:40px;color:#999;">Select an Activity Type to list activities</div></div></div>');
});

$(document).on( "click", ".close-prjctactvty", function(){

    $('#wbs_estimate_block').show();
    $('#project-activities-Body').hide();

});


$(function(){ 

        $(document).on('click','.add-project-activities-btn', function(e){
            e.preventDefault();
            $('.project-activities-list-wrpr').addClass('project-activities-add-form-active');
            $('.activity-results-wrpr').addClass('col-md-9');
            setTimeout(function() {
                $("html, body").animate({ scrollTop: $('.project-activities-tab').offset().bottom }, 1000);
                $(".added-activity-list-wrpr").animate({ scrollTop: $('.added-activity-list-wrpr').get(0).scrollHeight }, 1000);
            }, 10);

        });

        // list iow click
        $('#listiow').click(function(){

            $.ajax({
                type: 'POST',
                url: '../activity1/listactivities',
                beforeSend : function(){
                    $('#Promain-preloader-projectactivity').show();
                },
                dataType: "json",
                data: {Workgroup_Id:$('#IOWWorkgroupId2').val(),proid:$('#selectedProjectId').val(),worktypeid:$('#selectedWorktypeId').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#projects-Activity-Lists').html(data.result);
                        initActSortable();
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#Promain-preloader-projectactivity').hide();
                }
            });
    
        });
        // list iow click
        $(document).on('click','.editiowactivitybutton',function(){
            var id=$(this).attr('data-v');
            $('#editiowactivityname'+id).show();
            $('#editiowactivityunit'+id).show();
            $('#saveiowactivitybtn'+id).show();
            $('#boqslno'+id).show();
            $('#boq_slno'+id).hide();
            $('#iowactivityname'+id).hide();
            $('#iowactivityunit'+id).hide();
            $('#editiowactivitybutton'+id).hide();
            $("input#checkestimate"+id).removeAttr("disabled");
        });

        $(document).on('click','.saveiowactivitybtndrop',function(){ 
            var id =$(this).attr('data-v');
            var name= $('#editiowactivityname'+id).val();
            var unit= $('#editiowactivityunit'+id).val();
            var type= $(this).attr('data-type');
            var slno= $('#boqslno'+id).val();
        
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
                    url: '../activity1/updateiowactivity',
                    beforeSend : function(){
                        $('#saveiowactivitybtn'+id).attr("disabled", true);
                    },
                    dataType: "json",
                    data: {id:id,name:name,unit:unit,estimate:estimate,slno:slno},
                    success: function(data){
                        if(data.error=='No')
                        {
                            $('#editiowactivityname'+data.Id).hide();
                            $('#editiowactivityunit'+data.Id).hide();
                            $('#saveiowactivitybtn'+data.Id).hide();
                            $('#boqslno'+id).hide();
                            $('#iowactivityname'+data.Id).text(data.Name).show();
                            $('#iowactivityunit'+data.Id).text(data.Unit).show();
                            $('#boq_slno'+data.Id).text(data.Slno).show();
                            $('#editiowactivitybutton'+data.Id).show();
                            $("input#checkestimate"+id).attr("disabled", true);
                        }
                        $('#saveiowactivitybtn'+id).attr("disabled", false);
                    }
                });
            }
        });

        /*$(document).on('click','.iowactdelbtndrop',function(){  alert("lll")
            var id=$(this).attr('data-v');
            var actid=$(this).attr('data-id');
            var r = confirm("Are you sure you want to delete this Activity?");
            if (r == true) {
                $.ajax({
                    type: 'POST',
                    url: '../activity1/deleteiowactivity',
                    beforeSend : function(){
                        $('#iowactdelbtn'+id).attr("disabled", true);
                    },
                    dataType: "json",
                    data: {actid:id},
                    success: function(data){
                        if(data.error=='No')
                        {
                            $('#iowactivitiesrow'+id).remove();
                            $('#enggactsearch').trigger('click');
                        }
                        else
                        {
                            alert(data.errortext);
                            $('#iowactdelbtn'+id).attr("disabled", false);
                        }
                    }
                });
            }
        
        });*/

        $(document).on('click','.iowactdelbtndrop',function(){ 
            var id=$(this).attr('data-v');
            var actid=$(this).attr('data-id');
            var r = confirm("Are you sure you want to delete this Activity?");
            if (r == true) {
                $.ajax({
                    type: 'POST',
                    url: '../activity1/deleteiowactivity',
                    beforeSend : function(){
                        $('#iowactdelbtn'+id).attr("disabled", true);
                    },
                    dataType: "json",
                    data: {actid:id},
                    success: function(data){ 
                        if(data.error=='No')
                        { 
                            $('#iowactivitiesrow'+id).remove();
                            $('#enggactsearch').trigger('click');
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

        $(document).on('click','.wbsworktypelist',function(){
            var wbsworktlist=$(this).attr('data-v');
            $( '.wbsworktypelist' ).each(function() {
                $(this).removeClass('active');
            });
            $(this).addClass('active');
            $('#wbsworktypelist').val(wbsworktlist);
            $('#enggactsearch').trigger('click');

        });
        // .wbsactivitytypelist click handled via onclick="loadActivitiesByType()" in _activity.php
        $(document).on('click','.showsearch',function(){
            $(this).hide();
            $('#search-show').show();
            $('.close-activity-list-btn').show();
            //$('#enggactsearch').trigger('click');
        });

        $(document).on('click','.close-activity-list-btn',function(){
            $(this).hide();
            $('#search-show').hide();
            $('.showsearch').show();
            //$('#enggactsearch').trigger('click');
        });

        $('#enggactsearch').click(function(){

            //$('#addiow').trigger('click')
            $.ajax({
                type: 'POST',
                url: '../activity1/addiowactivities',
                /*beforeSend : function(){
                    $('#Promain-preloader-projectactivity-Search').show();
                },*/
                dataType: "json",
                data: {Workgroup_Id:$('#selectedWorkgrouId').val(),activitytypeid:$('#wbsactivitytypelist').val(),worktypeid:$('#wbsworktypelist').val(),type:'nosearch'},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#projects-Activity-Search-Lists').html(data.result);
                        //$('#worktypelistactivities').html(data.worktypes);
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    //$('#Promain-preloader-projectactivity-Search').hide();
                }
            });
    
        });


        function runActivitySearch() {
            var actvtyname = $('#searchenggactname').val();
            $('#new-act-panel').slideDown(200);
            if(actvtyname != '') {
                $('#new-act-results').html('<div style="padding:20px;text-align:center;color:#aaa;">Searching&#8230;</div>');
                $.ajax({
                    type: 'POST',
                    url: '../activity1/addiowactivities',
                    dataType: "json",
                    data: {Workgroup_Id:$('#selectedWorkgrouId').val(),activityname:actvtyname,activitytypeid:$('#wbsactivitytypelist').val(),type:'search'},
                    success: function(data){
                        if(data.error=='No')
                            $('#new-act-results').html(data.result);
                        else
                            alert(data.errortext);
                    }
                });
            } else {
                $('#new-act-results').html('<div style="text-align:center;padding:50px 20px;color:#c0c0c0;"><span style="font-size:32px;display:block;margin-bottom:8px;">&#9776;</span>Select a type from the left to list activities</div>');
            }
        }

        $('#enggactsearchNow').click(runActivitySearch);

        var actSearchTimer;
        $(document).on('input','#searchenggactname',function(){
            clearTimeout(actSearchTimer);
            actSearchTimer = setTimeout(runActivitySearch, 300);
        });

        $(document).on('click','.mapactivity',function(){
            var actid=$(this).attr('data-v');
            $('#addBOQMap').trigger('click') ;
            $('#boqactsearch').val(actid);
            $('#searchboqitem').val('');
        });
        $(document).on('click','#boqactsearch',function(){
            var slno=$('#searchboqitem').val();
            var actid=$(this).val();
            $.ajax({
                type: 'POST',
                url: '../activity1/boqsearch',
                dataType: "json",
                data: {slno:slno,actid:actid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#boqsearchlisting').html(data.result);
                    }
                }
            });
        
        });
        $(document).on('click','#addboqslno',function(){
            var boqid=$(this).attr('data-v');
            var actid=$(this).attr('data-id');
            $.ajax({
                type: 'POST',
                url: '../activity1/mapboqtoactivity',
                dataType: "json",
                data: {boqid:boqid,actid:actid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#boqactsearchCancel').trigger('click');
                        //$(this).parents('.tab').removeClass('add-form-active');
                    }
                }
            });
        
        });
        $('#boqactsearchCancel').click(function(){
            
            $(this).parents('.tab').removeClass('add-form-active');
            $('#listiow').trigger('click');
        });

        
$(document).on('click','.addiowactivitydrop',function(){
    var actid=$(this).attr('data-v');//alert(actid);
    //var processid=$(this).attr('data-id');
    var wbsid=$('#IOWWorkgroupId2').val();
    //var activitytypeid=$('#iowactivitytypeid'+actid).val();
    var activitytypeid=$('#wbsactivitytypelist').val();
    var worktypeid=$('#iowworktypeid'+actid).val();
    //var activityunit=$('#editiowactivityunit'+actid).val();
    var activityunit=$('#masteriowactivityunit'+actid).val();
    var projectid=$('#selectedProjectId').val();
    var amount=$('#iowactivityamount'+actid).val();
    var error=0;
    /*if($('#iowactivityname'+actid).val()!='' && ActivityNameExist($('#iowactivityname'+actid).val(),wbsid)=='Yes')
    {
        $('#iowactivityname'+actid).next("span").html('Activity Name Exists').show('slow');
        error=1;
    }*/
    if(!activitytypeid){
        alert("Please select Activity Type to add Activity!");
        error=1;
    }
    //var activityname=$('#iowactivityname'+actid).val();
    var activityname=$('#iowactivitynamespan'+actid).text();
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../activity1/addiowactivity',
            beforeSend : function(){
                $('.addiowactivity'+actid).attr("disabled", true);
            },
            dataType: "json",
            data: {actid:actid,wbsid:wbsid,worktypeid:worktypeid,activitytypeid:activitytypeid,activityunit:activityunit,projectid:projectid,activityname:activityname,amount:amount},
            success: function(data){
                if(data.error=='No')
                {
                    $('#addiowactivity'+actid).hide();
                    $('#addiowactivity'+actid).next("span").html('Added').show('slow');
                    console.log("success request");
                    //$('#addiowactivityrow'+actid).remove();
                    //$('.close-activity-list-btn').trigger('click') ;
                    //$('.showsearch').trigger('click');
                    $('#listiow').trigger('click') ;
                    // $('#listtasks').trigger('click');
                    // $('addprotaskbutton').attr("disabled", false);
                }
            }
        });
    }



});
        





});

function getParentnames(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../activity1/getparents',
        async:false,
        dataType: "json",
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}

function ActivityNameExist(name,wbsid)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../activity1/checkactivityname',
        async:false,
        data: {name:name,wbsid:wbsid},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}
function initActSortable() {
    if (!$('#act-list-tbody').length || !$.fn.sortable) return;
    $('#act-list-tbody').sortable({
        placeholder: 'ui-state-highlight',
        helper: 'clone',
        update: function(event, ui) {
            var updatedrows = [];
            $('.prjactivityss').each(function() {
                updatedrows.push({
                    rowid: $(this).attr('data-id'),
                    type:  $(this).attr('data-type'),
                    rowindex: $(this).index()
                });
            });
            $.ajax({
                type: 'POST',
                url: '../activity1/updatesort',
                data: {datavalue: updatedrows},
                dataType: 'json',
                success: function(data){}
            });
        }
    }).disableSelection();
}

$(function() {
});
