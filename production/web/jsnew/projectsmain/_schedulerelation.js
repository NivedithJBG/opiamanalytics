$(document).on( "click", ".schedule_relation-tab", function(){
    $('#scheduleactpage').hide();
    $('#wbs_schedule_block').hide();
    $('#relation-tabscreen').show();
    $('.Schedule-tab').addClass('add-form-active');
    $('#search-show-relation').hide();
    $('#wbs_schedule_relation_new').trigger('click');
});

$(document).on( "click", ".act-relatn-tab", function(){
    //$('.act-relatn-tab').trigger('click');
    var id=$('#wbs_schedule_relation_newid').val();
    $('#scheduleitemnamedisplayRelation').html(getItemname(id));
    $('#wbs_schedule_relation_new').trigger('click');
});

/*$(document).on( "click", ".showrelatntab", function(){
    $('#search-show-relation').css('display', 'flex !important');
});*/
$(document).on( "click", ".cancel", function(){
    if ($('#relations-panel').length) {
        $('#relations-panel').slideUp(200);
    } else {
        $('.close-schedulerelatn').trigger('click');
    }
});

$(document).on( "click", ".close-schedulerelatn", function(){
    var a = $('.close-schedulerelatn').attr("data-id");
    if(a == 0){
        $('#relation-tabscreen').hide();
        $('#wbs_schedule_block').show();
    }
    else{
        $('#relation-tabscreen').hide();
        $('#scheduleactpage').show();
    }
});

$(function(){ 

    $(document).on('click','#wbs_schedule_relation_new',function(){
        //$('#ScheduleActivity-main-body').hide();
        //$('#ScheduleActivity-Relation-body').show();
        $.ajax({
            type: 'POST',
            url: '../projectsmain/activityrelation',
            beforeSend : function(){
                //$('#wbs_schedule_relation_new').attr("disabled", true);
                $('#Promain-preloader-ScheduleActivity-Relation').show();
            },
            dataType: "json",
            data:{ projectid:$('#selectedProjectId').val(),
                    workgroupname:'',
                    structureid:'',
                    Relactionename:$('#Relactionsval').val(),
                    scheduleid:$('#selectedScheduleItem').val(),
                    filter_schedule_item:$('#filter_schedule_item').val(),
                    filter_schedule_activity:$('#filter_schedule_activity').val()
                },
            success: function(data){
                if(data.error=='No')
                {
                    $('#ScheduleActivity-Relation-add-form').html(data.result);
                    //$('#wbsactivityrelation').show();
                    $('#schedule_item_first-new').val(data.selecteditemone);
                    $('#schedule_item_second-new').val(data.selecteditemtwo);
                    $('#scheduleactivityitems-Relation').html(data.relationList);
                    $('#tasklistt').hide();
                    //$('#structure_relation_list').show();
                    //$('#activity_relation_list').hide();
                    $('#filter_schedule_item').html(data.scheduleItemSelectBox);
                    $('#filter_schedule_activity').html(data.scheduleActivitySelectBox);

                }
                else
                {
                    alert(data.errortext);
                }
    
                //$('#wbs_schedule_relation_new').attr("disabled", false);
                $('#Promain-preloader-ScheduleActivity-Relation').hide();
            }
        });
    
    });

    $(document).on('click','#Relactionssearch',function(){
        $('#wbs_schedule_relation_new').trigger('click');
    });
    $(document).on('change','#filter_schedule_item',function(){
        $('#wbs_schedule_relation_new').trigger('click');
    });
    $(document).on('change','#filter_schedule_activity',function(){
        $('#wbs_schedule_relation_new').trigger('click');
    });

    $(document).on('click','.editrelation',function(){
        var idval=$(this).attr('data-v');
        $('#precedentitem'+idval).hide();
        $('#precedentactivity'+idval).hide();
        $('#dependentitem'+idval).hide();
        $('#dependentactivity'+idval).hide();
        $('#relationtype'+idval).hide();
        $('#editrelationbutton'+idval).hide();
        $('#editrelationprecedentitem'+idval).show();
        $('#editrelationprecedentactivity'+idval).show();
        $('#editrelationdependentitem'+idval).show();
        $('#editrelationdependentactivity'+idval).show();
        $('#editrelationrelationtype'+idval).show();
        $('#saveeditrelationbutton'+idval).show();
        $('#lag'+idval).hide();
        $('#editlag'+idval).show();
  
    });

    $(document).on('click','.saveeditrelation',function(){
        if ($(this).closest('#relations-panel').length) return; // gantt panel handled by newganttview.php
        var idval=$(this).attr('data-v');
        var firstItem = $('#editrelationprecedentitem'+idval).val();
        var firstActivity = $('#editrelationprecedentactivity'+idval).val();
        var secondItem = $('#editrelationdependentitem'+idval).val();
        var secondActivity = $('#editrelationdependentactivity'+idval).val();
        var relationType = $('#editrelationrelationtype'+idval).val();
        var lag = $('#editlag'+idval).val();
        //validation
        if(firstItem == '')
        {
            $('#relationprecedentitem_error'+idval).html('Please select an Item').show();
            return;   
        }
        else
        {
            $('#relationprecedentitem_error'+idval).hide();
        }
        if(firstActivity == '')
        {
            $('#relationprecedentactivity_error'+idval).html('Please select an Activity').show();
            return;   
        }
        else
        {
            $('#relationprecedentactivity_error'+idval).hide();
        }
        /*if(secondItem == '')
        {
            $('#relationdependentitem_error'+idval).html('Please select an Item').show();
            return;   
        }
        else
        {
            $('#relationdependentitem_error'+idval).hide();
        }*/
        if(secondActivity == '')
        {
            $('#relationdependentactivity_error'+idval).html('Please select an Activity').show();
            return;   
        }
        else
        {
            $('#relationdependentactivity_error'+idval).hide();
        }
        if(relationType == '')
        {
            $('#relationrelationtype_error'+idval).show();
            return;   
        }
        else
        {
            $('#relationrelationtype_error'+idval).hide();
        }
        
            $.ajax({
                type: 'POST',
                url: '../projectsmain/updaterelation',
                beforeSend : function(){
                    $('#saveeditrelationbutton'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {id:idval,firstItem: firstItem, firstActivity: firstActivity, secondItem: secondItem, secondActivity: secondActivity, relationType: relationType, projectId: $('#selectedProjectId').val(),lag:lag},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#scheduleactivityitems-Relation').html(data.relationList);
                        /*$('#precedentitem'+idval).show();
                        $('#precedentactivity'+idval).show();
                        $('#dependentitem'+idval).show();
                        $('#dependentactivity'+idval).show();
                        $('#relationtype'+idval).show();
                        $('#editrelationbutton'+idval).show();
                        $('#editrelationprecedentitem'+idval).hide();
                        $('#editrelationprecedentactivity'+idval).hide();
                        $('#editrelationdependentitem'+idval).hide();
                        $('#editrelationdependentactivity'+idval).hide();
                        $('#editrelationrelationtype'+idval).hide();
                        $('#saveeditrelationbutton'+idval).hide();*/
                        $('#wbs_schedule_relation_new').trigger('click');
                    }
                    else
                    {
                        alert(data.errortext);
                    }
    
                    $('.save_relation').attr("disabled", false);
                }
            });
    });

    $(document).on('click','.deleterelation',function(){
        // The Gantt page's relations panel has its own handler (newganttview.php) —
        // skip here so one click never fires two delete requests
        if ($(this).closest('#relations-panel').length) return;
        var relationId=$(this).attr('data-v');
        var r = confirm("Are you sure you want to delete this Relation?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../projectsmain/deleterelation',
                beforeSend : function(){
                    $('#deleterelationbutton'+relationId).attr("disabled", true);
                },
                dataType: "json",
                data: {relationId:relationId},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#relationrow'+data.relationId).remove();
                    }
                    else
                    {
                        alert(data.errortext);
                    }
    
                    $('#deleterelationbutton'+data.relationId).attr("disabled", false);
                }
            });
        }
    });
    $(document).on('click','.schedule_item_first',function(){
        var scheduleItem1=$(this).attr('data-v');
        $( '.schedule_item_first' ).each(function() {
            $(this).removeClass('active');
        });
        $(this).addClass('active');
        $('#schedule_item_first-new').val(scheduleItem1);
        $.ajax({
            type: 'POST',
            url: '../projectsmain/getscheduleactivityone',
            dataType: "json",
            data: {scheduleItem: scheduleItem1,projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#schedule_activity_first-data').html(data.result);
                }
            }
        });
    });
    $(document).on('click','.schedule_activity_first',function(){
        var scheduleactivityfirst=$(this).attr('data-v');
        $( '.schedule_activity_first' ).each(function() {
            $(this).removeClass('active');
        });
        $(this).addClass('active');
        $('#schedule_activity_first-new').val(scheduleactivityfirst);
    });

    $(document).on('click','.schedule_item_second',function(){
        var scheduleItem=$(this).attr('data-v');
        $( '.schedule_item_second' ).each(function() {
            $(this).removeClass('active');
        });
        $(this).addClass('active');
        $('#schedule_item_second-new').val(scheduleItem);
        $.ajax({
            type: 'POST',
            url: '../projectsmain/getscheduleactivitytwo',
            dataType: "json",
            data: {scheduleItem: scheduleItem,projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#schedule_activity_second-data').html(data.result);
                }
            }
        });
    });
    $(document).on('click','.schedule_activity_second',function(){
        var scheduleactivitysecond=$(this).attr('data-v');
        $( '.schedule_activity_second' ).each(function() {
            $(this).removeClass('active');
        });
        $(this).addClass('active');
        $('#schedule_activity_second-new').val(scheduleactivitysecond);
    });
    $(document).on('click','.relation_type',function(){
        $('#relation_type-new').val($(this).val());
        $('#lag').show(); // field is rendered visible; never hide it
    });

    $(document).on('click','.save_relation_new',function(){
        if ($(this).closest('#relations-panel').length) return; // gantt panel handled by newganttview.php
        // var workid=$(this).val();
        var firstItem = $('#schedule_item_first-new').val();
        var firstActivity = $('#schedule_activity_first-new').val();
        var secondItem = $('#schedule_item_second-new').val();
        var secondActivity = $('#schedule_activity_second-new').val();
        var relationType = $('#relation_type-new').val();
        var lag = $('#lag').val();

        if(firstItem == '')
        {
            $('#first_item_error').show();
            return;   
        }
        else
        {
            $('#first_item_error').hide();
        }
        if(firstActivity == '')
        {
            $('#first_activity_error').show();
            return;   
        }
        else
        {
            $('#first_activity_error').hide();
        }
        if(secondItem == '')
        {
            $('#second_item_error').show();
            return;   
        }
        else
        {
            $('#second_item_error').hide();
        }
        if(secondActivity == '')
        {
            $('#second_avtivity_error').show();
            return;   
        }
        else
        {
            $('#second_activity_error').hide();
        }
        if(relationType == '')
        {
            $('#relation_error').show();
            return;   
        }
        else
        {
            $('#relation_error').hide();
        }
        
            $.ajax({
                type: 'POST',
                url: '../projectsmain/saverelation',
                beforeSend : function(){
                    // $(this).attr("disabled", true);
                    $('.save_relation_new').attr("disabled", true);
                },
                dataType: "json",
                data: {lag:lag, firstItem: firstItem, firstActivity: firstActivity, secondItem: secondItem, secondActivity: secondActivity, relationType: relationType, projectId: $('#selectedProjectId').val(), structureid:$('#mode-edit').val() },
                success: function(data){
                    if(data.error=='No')
                    {
                        if ($('#relations-panel').length) {
                            $('#relations-content').find('.relations-list-wrap').html(data.relationList);
                        } else {
                            $('#scheduleactivityitems-Relation').html(data.relationList);
                            $('#wbs_schedule_relation_new').trigger('click');
                        }
                    }
                    else if(data.error=='Durerror')
                    {
                        alert(data.errortext);
                        if ($('#relations-panel').length) {
                            $('#relations-content').find('.relations-list-wrap').html(data.relationList);
                        } else {
                            $('#scheduleactivityitems-Relation').html(data.relationList);
                            $('#wbs_schedule_relation_new').trigger('click');
                        }
                    }
                    else if(data.mode=='Edit')
                    {
                        if ($('#relations-panel').length) {
                            $('#relations-content').find('.relations-list-wrap').html(data.relationList);
                        } else {
                            $('#scheduleactivityitems-Relation').html(data.relationList);
                        }
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('.save_relation_new').attr("disabled", false);
                }
            });
        
    });





});
$(function() {
$( "#scheduleactivityitems-Relation" ).sortable({
        placeholder: "ui-state-highlight",
        helper:'clone',
        
        update:function( event, ui ) {
            //alert($(this).index());

            var updatedrows=[];
            $('.relationlist').each(function() {
                var rowid=$(this).data("id");

                //alert(rowid);
                var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    rowindex:rowindex
                })
            });
            $.ajax({
                type: 'POST',
                url: '../projectsmain/updaterelationsort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection();
});


function getItemname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projectsmain/getitemname',
        async:false,
        dataType: "json",
        data: {id:id},
        success: function(data){
            retval=data.itemName;
        }
    });
    return retval;
}