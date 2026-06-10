$(document).on( "click", ".viewworkgroups", function(){
    //$('.acc_container').slideUp();
    $('#project').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#workgroup').addClass('active').next('.acc_container').slideDown();
    $('#workgroup_name').html('4. WBS');
    var id= $(this).val();
    $('#dispprojectname').html(getProjectname(id)); 
    $('#dispprojectname_schedule').html(getProjectname(id));
    $('#selectedProjectId').val(id);
    $('#wbs_estimate_block').show();
    $('#wbs_schedule_block').hide();
    $('#listworkgroup').trigger('click') ;
    //$('#listwbs_schedule').trigger('click');
});

$(document).on( "click", ".viewwbsSchedule", function(){
    //$('.acc_container').slideUp();
    $('#project').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#workgroup').addClass('active').next('.acc_container').slideDown();
    $('#workgroup_name').html('4. Schedule');
    var id= $(this).val();
    $('#dispprojectname').html(getProjectname(id)); 
    $('#dispprojectname_schedule').html(getProjectname(id));
    $('#selectedProjectId').val(id);
    //$('#listworkgroup').trigger('click') ;
    $('#wbs_estimate_block').hide();
    $('#wbs_schedule_block').show();
    $('#editoldstructure').val('');
    $('#saveoldstructure').val('');
    $('#editstructurebuttonschedule').val('');
    $('#savestructurebuttonschedule').val('');
    $('#deletestructurebuttonschedule').val('');
    $('#mode-edit').val('');
    $('#ganttchartshow').hide();
    $('#listwbs_schedule').trigger('click');
    
});

$(document).on( "click", "#workgroup", function(){
    if($('#selectedProjectId').val()!=''){
        //$('.acc_container').slideUp();
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown();
        $('#workgroup').addClass('active').next('.acc_container').slideDown();
        $('#dispprojectname').html(getProjectname($('#selectedProjectId').val()));
        $('#selectedProjectId').val($('#selectedProjectId').val());
        $('#listworkgroup').trigger('click') ;
    }
});

$(document).on( "click", "#iowstructurename", function(){
    var projectid = $('#selectedProjectId').val();
    var name = $('#wbsestimatename').val();
    var error=0;
    if($('#wbsestimatename').val()=='')
    {
        $("#wbsestimatename").next("span").html('Enter Wbs Estimate Structure Name').show().delay(3000).fadeOut();
        error=1;
    }
    if(error==0){
    $.ajax({
            type: 'POST',
            url: '../activity1/wbsestimatestructure',
            beforeSend : function(){
                $('#iowstructurename').attr("disabled", true);
            },
            dataType: "json",
            data: {projectid:projectid,name:name},
            success: function(data){
                if(data.error=='No')
                {
                 $('#listworkgroup').trigger('click');
                 $('#wbsestimatename').val('');
                 $('#schedulestructure').val(data.EstimateId);
                 $( ".schedulestructure" ).trigger( "click" );
                }
                else
                {
                    alert(data.errortext);
                }

                $('#iowstructurename').attr("disabled", false);
            }
        });
   }
});

$(document).on('click','.editstructurerelation',function(){
    var idval=$(this).val();
    // return;
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
    var relationtype = $('#editrelationrelationtype'+idval).val();
    //if(relationtype==1 || relationtype==3){
      $('#lag'+idval).hide();
      $('#editlag'+idval).show();
    /*}
    else{
      $('#lag'+idval).show();
      $('#editlag'+idval).hide();         
    }*/
               
});

$(document).on('change','.editrelationrelationtype',function(){
  var relationtype = $(this).val();
  var dataid= $(this).attr("data-id");
  //if(relationtype==1 || relationtype==3){
    $('#lag'+dataid).hide();
    $('#editlag'+dataid).show();
  /*}
  else{
    $('#lag'+dataid).html('');
    $('#editlag'+dataid).val('');
    $('#lag'+dataid).show();
    $('#editlag'+dataid).hide();
  }*/
});

$(function(){
    $('.wbs_item_button').click(function(){
        var id = $(this).attr('id');
        // alert(id);
        $('.wbs_item').hide();
        $('.wbs_item_button').attr("disabled", false);
        if($('#'+id+'_block').show()){
            $(this).attr("disabled", true);
            $('#list'+id).trigger('click');
        }

    });

    // workgroup section function
    // list workgroup click
    $('#listworkgroup').click(function(){
        $('#workgroupaddsection').slideUp('slow');// slide down the project listing div
        $('#workgrouplistsection').slideDown('slow');// slide down the project listing div
        $('#listworkgroup').removeClass('btn-danger').addClass('btn-success');
        $('#addworkgroup').removeClass('btn-success').addClass('btn-danger');
        //var structureid = $('#estimatestructure').val();
        $.ajax({
            type: 'POST',
            url: '../workgroups1/search',
            beforeSend : function(){
                $('#workgroupsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val(),workgroupname:''},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workgroupitems').html(data.result);
                    $('#estimatestructure').html(data.names);
                }
                else
                {
                    alert(data.errortext);
                }

                $('#workgroupsearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });

    });
    
    $('#listwbs_structure').click(function(){
    $('#listwbs_structure').attr("disabled", true);
    $('#wbs_structure_relation').attr("disabled", false);
    $('#wbsstructureitemlistsection').show();
    $('#wbsstructurerelation').hide();
      $.ajax({
        type: 'POST',
        url: '../projects1/searchstructureitem',
        beforeSend : function(){
            $('#workgroupsearch').attr("disabled", true);
            $('.preloader').show();
        },
        dataType: "json",
        data: {projectId:$('#selectedProjectId').val(),workgroupname:'',structureid:$('#mode-edit').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#wbsstructureitems').html(data.result);
            }
            else
            {
               // alert(data.errortext);
            }

            $('#workgroupsearch').attr("disabled", false);
            $('.preloader').hide();
        }
      });
    });
    
    // list workgroup click  \
    // add workgroup click
    $('#addworkgroup').click(function(){
        $('#workgrouplistsection').slideUp('slow');// slide down the project listing div
        $('#workgroupaddsection').slideDown('slow');// slide down the project listing div
        $('#addworkgroup').removeClass('btn-danger').addClass('btn-success');
        $('#listworkgroup').removeClass('btn-success').addClass('btn-danger');

    });
    // add workgroup click

    // save workgroup click
    //workgroup function ends here

    $('#saveworkgroup').click(function(){
        var error=0;
        //var estimateid=$('#estimatestructure').val();
        if($('#workgroupname').val()=='')
        {
            $("#workgroupname").next("span").html('Enter Work Group Name').show('slow');
            error=1;
        }
       /* if($('#workgroupname').val()!='' && WorkGroupNameExists($('#workgroupname').val(),$('#selectedProjectId').val())=='Yes')
        {
            $('#workgroupname').next("span").html('Work Group Name Exists').show('slow')
            error=1;
        } */
        if($('#worktypegroups').val()=='none')
        {
            $("#worktypegroups").next("span").html('Select Workgroup').show('slow');
            error=1;
        }
        if($('#worktype').val()=='none')
        {
            $("#worktype").next("span").html('Select Worktype').show('slow');
            error=1;
        }
        if($('#workgroupunit').val()=='')
        {
            $("#workgroupunit").next("span").html('Enter Unit').show('slow');
            error=1;
        }
        if($('#workgroupquantity').val()=='')
        {
            $("#workgroupquantity").next("span").html('Enter Quantity').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../workgroups1/create',
                beforeSend : function(){
                    $('#saveworkgroup').attr("disabled", true);
                },
                dataType: "json",
                data: {Project_Id:$('#selectedProjectId').val(),workgroupname:$('#workgroupname').val(),worktype:$('#worktype').val(),worktypegroup:$('#worktypegroups').val(),unit:$('#workgroupunit').val(),quantity:$('#workgroupquantity').val()},
                success: function(data){

                    if(data.error=='No')
                    {
                        $('#addworkgroupform')[0].reset();
                        $('#listworkgroup').trigger('click');

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
                        $("#workgroupname").next("span").html(data.errortext).show('slow');
                        $('#saveworkgroup').attr("disabled", false);
                    }
                    $('#saveworkgroup').attr("disabled", false);
                }
            });
        }
    });

    $( "#workgroupitems" ).sortable({
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
                url: '../workgroups1/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection();
    
    $( "#wbsstructureitems" ).sortable({
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
                url: '../workgroups1/updateitemlistsort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection();
    
    $( "#wbsscheduleitems" ).sortable({
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
                url: '../workgroups1/updateitemlistsort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection();

    // WBS Schedule

    $('#listwbs_schedule').click(function(){
        $('#scheduleitemaddsection').slideUp('slow');// slide down the project listing div
        $('#wbsactivityrelation').slideUp('slow');
        $('#wbsscheduleitemlistsection').slideDown('slow');// slide down the project listing div
        $('#listwbs_schedule').removeClass('btn-danger').addClass('btn-success');
        $('#addschedule_item').removeClass('btn-success').addClass('btn-danger');
        $('#wbs_schedule_relation').removeClass('btn-success').addClass('btn-danger');
        var structureid = $('#mode-edit').val();
        if(structureid!=''){
          var structureid = $('#mode-edit').val();
        }
        else{
          var structureid = 0;
        }
        $.ajax({
            type: 'POST',
            url: '../projects1/searchscheduleitem',
            beforeSend : function(){
                $('#workgroupsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val(),workgroupname:'',structureid:structureid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#wbsstructureitems').show();
                    $('#ganttchartshow').show();
                    $('#ganttchartshow').html(data.gantt);  
                    $('#wbsstructureitems').html(data.result);
                    $('#oldstructure').html(data.names);
                }
                else if(data.mode=='Edit')
                {
                    $('#wbsstructureitems').html(data.result);
                    $('#oldstructure').html(data.names);
                }
                else
                {
                    alert(data.errortext);
                }

                $('#workgroupsearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });

    });

    
    $('#wbs_schedule_relation').click(function(){
        //$( ".editoldstructure" ).trigger( "click" );
        $('#scheduleitemaddsection').slideUp('slow');// slide down the project listing div
        $('#wbsscheduleitemlistsection').slideUp('slow');
        $('#wbsactivityrelation').slideDown('slow');// slide down the project listing div
        $('#listwbs_schedule').removeClass('btn-success').addClass('btn-danger');
        
        $('#addschedule_item').removeClass('btn-success').addClass('btn-danger');
        $('#wbs_schedule_relation').removeClass('btn-danger').addClass('btn-success');
        var structureid = $('#mode-edit').val();
        if(structureid!='none'){
          var structureid = $('#mode-edit').val();
          //$( ".editoldstructure" ).trigger( "click" );
        }
        else{
          var structureid = 0;
        }
        $.ajax({
            type: 'POST',
            url: '../projects1/activityrelation',
            beforeSend : function(){
                $('#workgroupsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val(),workgroupname:'',structureid:structureid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#activity_relation_content').html(data.result);
                    $('#structure_relation_list').html(data.relationList);
                    $('#structure_relation_list').show();
                    $('#activity_relation_list').hide();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#workgroupsearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });

    });

    $('#addschedule_item').click(function(){
        $('#wbsscheduleitemlistsection').slideUp('slow');// slide down the project listing div
        $('#wbsactivityrelation').slideUp('slow');
        $('#scheduleitemaddsection').slideDown('slow');// slide down the project listing div
        $('#addschedule_item').removeClass('btn-danger').addClass('btn-success');
        $('#listwbs_schedule').removeClass('btn-success').addClass('btn-danger');
        $('#wbs_schedule_relation').removeClass('btn-success').addClass('btn-danger');

        $.ajax({
            type: 'POST',
            url: '../projects1/getschedulegroups',
            beforeSend : function(){
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val(),},
            success: function(data){
                if(data.error=='No')
                {
                    $('#groups').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }

            }
        });
    });

    $('#savescheduleitem').click(function(){
        var error=0;
        if($('#schedulegroup_name').val()=='')
        {
            $("#schedulegroup_name").next("span").html('Enter Schedule Group Name').show('slow');
            error=1;
        }
        if($('#scheduleitem_name').val()=='')
        {
            $('#scheduleitem_name').next("span").html('Enter Item Name').show('slow')
            error=1;
        }
       
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../projects1/addwbschedulegroup',
                beforeSend : function(){
                    $('#saveworkgroup').attr("disabled", true);
                },
                dataType: "json",
                //data: {Project_Id:$('#selectedProjectId').val(),schedulegroupname:$('#schedulegroup_name').val(), scheduleitemname:$('#scheduleitem_name').val(), structureid:$('#savescheduleitem').val()},
                data: {Project_Id:$('#selectedProjectId').val(),schedulegroupname:$('#schedulegroup_name').val(), scheduleitemname:$('#scheduleitem_name').val()},
                success: function(data){

                    if(data.error=='No')
                    {
                        $('#addscheduleitemform')[0].reset();
                        $('#listwbs_schedule').trigger('click');
                     
                    }
                    else
                    {
                        $("#schedulegroup_name").next("span").html(data.Message).show('slow');
                        $('#savescheduleitem').attr("disabled", false);
                    }
                    $('#savescheduleitem').attr("disabled", false);
                }
            });
        }
    });

    // 
});

$(document).on('click','.saveeditstructurerelation',function(){
    var idval=$(this).val();
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
    if(secondItem == '')
    {
        $('#relationdependentitem_error'+idval).html('Please select an Item').show();
        return;   
    }
    else
    {
        $('#relationdependentitem_error'+idval).hide();
    }
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
            url: '../projects1/updatestructurerelation',
            beforeSend : function(){
                $('#saveeditrelationbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {id:idval,firstItem: firstItem, firstActivity: firstActivity, secondItem: secondItem, secondActivity: secondActivity, relationType: relationType, projectId: $('#selectedProjectId').val(),lag:lag},
            success: function(data){
                if(data.error=='No')
                {
                    $('#structure_relation_list').show();
                    $('#structure_relation_list').html(data.relationList);
                    $('#wbsscheduleitems').hide();
                    $('#wbsstructureitems').show();
                    $('#activity_relation_list').hide();
                   // $('#oldstructure').attr("disabled", true);
                    $('#wbs_schedule_relation').trigger('click');
                }
                else if(data.error=='Dates')
                {
                    alert(data.errortext);
                    $('#saveeditrelationbutton'+idval).attr("disabled", false);
                }
                else
                {
                    //alert(data.errortext);
                }

                $('.save_relation').attr("disabled", false);
            }
        });
});

$(document).on( "click", ".structurescheduleactivity", function(){
    $('#workgroup').removeClass('active').next().slideUp();
    $('#wbs_scheduleactivity').addClass('active').next('.acc_container').slideDown();
    var id=$(this).val();
    $('#selectedProjectId').val(id);
    var scheduleitem = $(this).attr("data-id");
    $('#selectedScheduleItem').val(scheduleitem);
    $('#listscheduleact').trigger('click') ;
    $('#projectnamedisplay').html(getProjectname(id));
    $('#scheduleitemnamedisplay').html(getItemname(scheduleitem));
});

$(document).on('click','.deletestructurerelation',function(){
    var relationId=$(this).val();
    var r = confirm("Are you sure you want to delete this Relation?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects1/deletestructurerelation',
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

$(document).on('click','.editworkgroupbutton',function(){
    var idval=$(this).val();
    $('#workgroupname'+idval).hide();
    $('#worktypename'+idval).hide();
    $('#workgroupunit'+idval).hide();
    $('#workgroupquantity'+idval).hide();
    $('#editworkgroupbutton'+idval).hide();
    $('#editworkgroupname'+idval).show();
    $('#editworkgroupunit'+idval).show();
    $('#editworkgroupquantity'+idval).show();
    $('#editworktype'+idval).show();
    //$('#editworkgroupname'+idval).focus();
    $('#saveworkgroupbutton'+idval).show();
});

$(document).on('click','.editscheduleitemgroupbuttonq',function(){
    var idval=$(this).val();
    // return;
    $('#schedulegroupname'+idval).hide();
    $('#scheduleitemname'+idval).hide();
    $('#editscheduleitemgroupbutton'+idval).hide();
    $('#editschedulegroupname'+idval).show();
    $('#editscheduleitemname'+idval).show();
    //$('#editworkgroupname'+idval).focus();
    $('#savewbsscheduleitembutton'+idval).show();
});

$(document).on('click','.editrelation',function(){
    var idval=$(this).val();
    // return;
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
    
    var relationtype = $('#editrelationrelationtype'+idval).val();
    //if(relationtype==1 || relationtype==3){
      $('#lag'+idval).hide();
      $('#editlag'+idval).show();
    /*}
    else{
      $('#lag'+idval).show();
      $('#editlag'+idval).hide();         
    }*/
});

$(document).on('click','.saveeditrelation',function(){
    var idval=$(this).val();
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
            url: '../projects1/updaterelation',
            beforeSend : function(){
                $('#saveeditrelationbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {id:idval,firstItem: firstItem, firstActivity: firstActivity, secondItem: secondItem, secondActivity: secondActivity, relationType: relationType, projectId: $('#selectedProjectId').val(),lag:lag},
            success: function(data){
                if(data.error=='No')
                {
                    $('#activity_relation_list').html(data.relationList);
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

$(document).on('click','.saveworkgroupbutton',function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editworkgroupname'+idval).val()=='')
    {
        $('#editworkgroupname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if($('#editworktype'+idval).val()=='none')
    {
        $("#editworktype"+idval).next("span").html('Select Worktype').show('slow');
        error=1;
    }
    if($('#editworkgroupunit'+idval).val()=='')
    {
        $("#editworkgroupunit"+idval).next("span").html('Enter Unit').show('slow');
        error=1;
    }
    if($('#editworkgroupquantity'+idval).val()=='')
    {
        $("#editworkgroupquantity"+idval).next("span").html('Enter Quantity').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../workgroups1/update',
            beforeSend : function(){
                $('#saveworkgroupbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {workid:idval,name:$('#editworkgroupname'+idval).val(),worktype:$('#editworktype'+idval).val(),unit: $('#editworkgroupunit'+idval).val(),quantity: $('#editworkgroupquantity'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workgroupname'+data.Id).show();
                    $('#worktypename'+data.Id).show();
                    $('#workgroupunit'+data.Id).show();
                    $('#workgroupquantity'+data.Id).show();
                    $('#editworkgroupbutton'+data.Id).show();
                    $('#editworkgroupname'+data.Id).hide();
                    $('#editworktype'+data.Id).hide();
                    $('#editworkgroupquantity'+data.Id).hide();
                    $('#editworkgroupunit'+data.Id).hide();
                    $('#saveworkgroupbutton'+data.Id).hide();
                    $('#editworkgroupname'+data.Id).val(data.Name);
                    $('#workgroupname'+data.Id).text(data.Name);
                    $('#listworkgroup').trigger('click');

                }
                else
                {
                    alert(data.errortext);
                }

                $('#saveworkgroupbutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.savewbsscheduleitembutton',function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editscheduleitemname'+idval).val()=='')
    {
        $('#editscheduleitemname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if($('#editschedulegroupname'+idval).val()=='')
    {
        $("#editschedulegroupname"+idval).next("span").html('Enter Worktype Name').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../projects1/updatescheduleitem',
            beforeSend : function(){
                $('#savewbsscheduleitembutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {itemid:idval,Itemname:$('#editscheduleitemname'+idval).val(),scheduleGroup:$('#editschedulegroupname'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#schedulegroupname'+data.Id).show();
                    $('#scheduleitemname'+data.Id).show();
                    $('#editscheduleitemgroupbuttonq'+data.Id).show();
                    $('#editschedulegroupname'+data.Id).hide();
                    $('#editscheduleitemname'+data.Id).hide();
                    $('#savewbsscheduleitembutton'+data.Id).hide();
                    $('#editschedulegroupname'+data.Id).val(data.groupName);
                    $('#editscheduleitemname'+data.Id).text(data.itemName);
                    $('#listwbs_schedule').trigger('click');

                }
                else
                {
                    alert(data.errortext);
                }

                $('#savewbsscheduleitembutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.deleteworkgroupbutton',function(){ 
    var workid=$(this).val();
    var r = confirm("Are you sure you want to delete this Work Group?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../workgroups1/deleteworkgroup',
            beforeSend : function(){
                $('#deleteworkgroupbutton'+workid).attr("disabled", true);
            },
            dataType: "json",
            data: {workid:workid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workgrouprow'+data.Id).remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deleteworkgroupbutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.deleterelation',function(){
    var relationId=$(this).val();
    var r = confirm("Are you sure you want to delete this Relation?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects1/deleterelation',
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

$(document).on('click','.deletewbsscheduleitembutton',function(){
    var workid=$(this).val();
    var r = confirm("Are you sure you want to delete this Schedule Item?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects1/deletescheduleitem',
            beforeSend : function(){
                $('#deletewbsscheduleitembutton'+workid).attr("disabled", true);
            },
            dataType: "json",
            data: {itemId:workid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#scheduleitemrow'+data.Id).remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deletewbsscheduleitembutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.save_relation',function(){
    // var workid=$(this).val();
    var firstItem = $('#schedule_item_first').val();
    var firstActivity = $('#schedule_activity_first').val();
    var secondItem = $('#schedule_item_second').val();
    var secondActivity = $('#schedule_activity_second').val();
    var relationType = $('#relation_type').val();
    var lag = $('#lag').val();
    //validation
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
            url: '../projects1/saverelation',
            beforeSend : function(){
                // $(this).attr("disabled", true);
                $('.save_relation').attr("disabled", true);
            },
            dataType: "json",
            data: {lag:lag, firstItem: firstItem, firstActivity: firstActivity, secondItem: secondItem, secondActivity: secondActivity, relationType: relationType, projectId: $('#selectedProjectId').val(), structureid:$('#mode-edit').val() },
            success: function(data){
                if(data.error=='No')
                {
                    //$('#wbs_schedule_relation').trigger('click');
                    $('#structure_relation_list').html(data.relationList);
                    $('#wbs_schedule_relation').trigger('click');
                }
                else if(data.mode=='Edit')
                {
                    $('#structure_relation_list').html(data.relationList);
                }
                else
                {
                    alert(data.errortext);
                }

                $('.save_relation').attr("disabled", false);
            }
        });
    
});

$(document).on('change','#relation_type',function(){
    var lag = $('#relation_type').val();
    if(lag==1){
       $('#lag').show();
    }
    else{
      $('#lag').val(''); 
      $('#lag').hide(); 
    }
});

$(document).on('click','#savewbsgroupname',function(){
        var error=0;
        if($('#wbsname').val()=='')
        {
            $("#wbsname").next("span").html('Enter Wbs Structure Name').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../projects1/wbsstructure',
                beforeSend : function(){
                    $('#savewbsgroupname').attr("disabled", true);
                },
                dataType: "json",
                data: {wbsstructure:$('#wbsname').val(), projectid:$('#selectedProjectId').val()},
                success: function(data){

                    if(data.error=='No')
                    {
                     $('#listwbs_schedule').trigger('click');
                     $('#wbsname').val('');
                    }
                    else
                    {

                    }
                    $('#savewbsgroupname').attr("disabled", false);
                }
            });
        }
});

$(document).on('click','#duplicatewbs',function(){
    var oldstructureid = $('#duplicatewbs').val();
    //alert(oldstructureid)
        var error=0;
       /* if($('#wbsestimatename').val()=='')
        {
            $("#wbsestimatename").next("span").html('Enter Wbs Estimate Structure Name').show('slow');
            error=1;
        } */
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../projects1/duplicatewbsstructure',
                beforeSend : function(){
                    $('#duplicatewbs').attr("disabled", true);
                },
                dataType: "json",
                data: {wbsstructure:$('#wbsestimatename').val(), projectid:$('#selectedProjectId').val(), oldstructureid:oldstructureid},
                success: function(data){

                    if(data.error=='No')
                    {
                     $('#listworkgroup').trigger('click');
                     $('#wbsestimatename').val('');
                     $('#iowstructurename').hide();
                     $('#duplicatewbs').show();
                     $('#schedulestructure').val(data.Structureid);
                     $('#estimatestructure').val(data.Structureid);
                     //$( ".schedulestructure" ).trigger( "click" );
                    }
                    else
                    {

                    }
                    $('#duplicatewbs').attr("disabled", false);
                }
            });
        } 
});

$(document).on('change','#worktypegroups',function(){
    var workgp=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../workgroups1/getworktypes',
        dataType: "json",
        data: {workgp:workgp},
        success: function(data){
            if(data.error=='No')
            {
                $('#worktype').html(data.result);
            }
        }
    });
});

$(document).on('change','#oldstructure',function(){
  var structueid=$(this).val();
   if(structueid=='none'){
    $('#structure_relation_list').hide();
    $('#activity_relation_list').show();
    $('#savewbsgroupname').show();
   // $('#duplicatewbs').hide();
    $('#wbsstructureitems').hide();
    $('#wbsname').show();
    $('#wbsscheduleitems').show();
    $('#editstructurebuttonschedule').hide();
    $('#savestructurebuttonschedule').hide();
    $('#deletestructurebuttonschedule').hide();
    $('#savescheduleitem').val(structueid);
    $('#ganttchartshow').hide();
    $('#mode-edit').val(structueid);
    if($('#wbs_schedule_relation').hasClass("btn-success")){
        $('#editoldstructure').val(structueid);
        $( ".editoldstructure" ).trigger( "click" );
    }
    else{
        $( "#listwbs_schedule" ).trigger( "click" );
    }
   }
   else{
   $('#editoldstructure').val(structueid);
   $('#saveoldstructure').val(structueid);
   $('#editstructurebuttonschedule').val(structueid);
   $('#savestructurebuttonschedule').val(structueid);
   $('#deletestructurebuttonschedule').val(structueid);
       
   $('#editstructurebuttonschedule').show();
   $('#savestructurebuttonschedule').hide();
   $('#deletestructurebuttonschedule').show();
   //$('#duplicatewbs').val(structueid);
   //$('#duplicatewbs').hide();
   $('#savewbsgroupname').hide();
   $('#wbsname').hide();
   $('#mode-edit').val(structueid);
   $( ".editoldstructure" ).trigger( "click" );
   }
});

$(document).on('click','.editstructurebuttonschedule',function(){
    var idval=$(this).val();   
    $('#editstructurebuttonschedule').hide();
    $('#savestructurebuttonschedule').show();
    $('#oldstructure').hide();
    var structurename = $( "#oldstructure option:selected" ).text();
    $('#newwbsschedulename').show();
    $('#newwbsschedulename').val(structurename);
});

$(document).on('click','.savestructurebuttonschedule',function(){
    var idval=$(this).val();
    var error=0;
   // $('.error').hide();
    if($('#newwbsschedulename').val()=='')
    {
        $('#newwbsschedulename').next("span").html('Enter Wbs Structure Name').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../projects1/updatewbsstructurename',
            beforeSend : function(){
                $('#savestructurebuttonschedule').attr("disabled", true);
            },
            dataType: "json",
            data: {structureid:idval,Structurename:$('#newwbsschedulename').val()},
            success: function(data){
                if(data.error=='No')
                {
                     $('#editstructurebuttonschedule').show();
                     $('#savestructurebuttonschedule').hide();
                     $('#deletestructurebuttonschedule').show();
                     $('#newwbsschedulename').hide();
                     $('#oldstructure').show();
                     $( "#listwbs_schedule" ).trigger( "click" );

                }
                else
                {
                    //alert(data.errortext);
                }

                $('#savestructurebuttonschedule').attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.deletestructurebuttonschedule',function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Structure Name?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects1/deletestructurename',
            beforeSend : function(){
                $('#deletestructurebuttonschedule').attr("disabled", true);
            },
            dataType: "json",
            data: {structureid:idval},
            success: function(data){
                if(data.error=='No')
                {
                  $('#editstructurebuttonschedule').hide();
                  $('#deletestructurebuttonschedule').hide();
                  $( "#listwbs_schedule" ).trigger( "click" );
                }
                else
                {
                   // alert(data.errortext);
                }

                $('#deletestructurebuttonschedule').attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.editoldstructure',function(){
        var structureid = $('#editoldstructure').val();
    
          if(structureid!='' && structureid!='none'){

            $.ajax({
                type: 'POST',
                url: '../projects1/editoldstructure',
                beforeSend : function(){
                    //$('#oldstructure').attr("disabled", true);
                    //$('#editoldstructure').hide();
                    //$('#saveoldstructure').show();
                },
                dataType: "json",
                data: {structureid:structureid, projectid:$('#selectedProjectId').val()},
                success: function(data){

                    if(data.error=='No')
                    {
                     $('#ganttchartshow').show();
                     $('#ganttchartshow').html(data.gantt);
                     $('#wbsscheduleitems').hide();
                     $('#wbsstructureitems').html(data.result);
                     $('#activity_relation_content').html(data.dropdown);
                     $('#wbsstructureitems').show();
                     //$('#wbsname').hide();
                     $('#savewbsgroupname').hide();
                    // $('#duplicatewbs').hide();
                     $('#activity_relation_list').hide();
                     $('#structure_relation_list').html(data.relations);
                     $('#structure_relation_list').show();
                     $('#savescheduleitem').val(structureid);
                    }
                    else
                    {

                    }
                  //  $('#oldstructure').attr("disabled", true);
                }
            });
          }
    
          else{              
              $( "#wbs_schedule_relation" ).trigger( "click" );         
          }
});

$(document).on('click','.saveoldstructure',function(){
          var structureid = $('#saveoldstructure').val();
          if(structureid!=''){
            $.ajax({
                type: 'POST',
                url: '../projects1/saveoldstructure',
                beforeSend : function(){
                    $('#oldstructure').attr("disabled", true);
                },
                dataType: "json",
                data: {structureid:structureid},
                success: function(data){

                    if(data.error=='No')
                    {
                     $('#wbsscheduleitems').show();
                     $('#wbsstructureitems').hide();
                     $('#wbsname').show();
                     $('#savewbsgroupname').show();
                     $('#activity_relation_list').show();
                     $('#structure_relation_list').hide();
                     $('#editoldstructure').show();
                     $('#saveoldstructure').hide();
                    }
                    else
                    {

                    }
                    $('#oldstructure').attr("disabled", false);
                    $('#mode-edit').val('');
                    $('#editoldstructure').val('');
                    $('#saveoldstructure').val('');
                    $('#savescheduleitem').val('');
                }
            });
          }
});

$(document).on('click','.editstructureitemgroupbuttonq',function(){
    var idval=$(this).val();
    // return;
    $('#structuregroupname'+idval).hide();
    $('#structureitemname'+idval).hide();
    $('#editstructureitemgroupbutton'+idval).hide();
    $('#editstructuregroupname'+idval).show();
    $('#editstructureitemname'+idval).show();
    //$('#editworkgroupname'+idval).focus();
    $('#savewbsstructureitembutton'+idval).show();
});

$(document).on('click','.savewbsstructureitembutton',function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editstructureitemname'+idval).val()=='')
    {
        $('#editstructureitemname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if($('#editstructuregroupname'+idval).val()=='')
    {
        $("#editstructuregroupname"+idval).next("span").html('Enter Worktype Name').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../projects1/updatestructureitem',
            beforeSend : function(){
                $('#savewbsstructureitembutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {itemid:idval,Itemname:$('#editstructureitemname'+idval).val(),scheduleGroup:$('#editstructuregroupname'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#structuregroupname'+data.Id).show();
                    $('#structureitemname'+data.Id).show();
                    $('#editstructureitemgroupbutton'+data.Id).show();
                    $('#editstructuregroupname'+data.Id).hide();
                    $('#editstructureitemname'+data.Id).hide();
                    $('#savewbsstructureitembutton'+data.Id).hide();
                    $('#editstructuregroupname'+data.Id).val(data.groupName);
                    $('#editstructureitemname'+data.Id).text(data.itemName);
                    $('#listwbs_structure').trigger('click');
                    $('#wbsscheduleitems').hide();
                    $('#wbsstructureitems').show();
                    $('#activity_relation_list').show();
                    $('#structure_relation_list').hide();
                    $('#listwbs_schedule').trigger('click');
                   // $('#oldstructure').attr("disabled", true);
                }
                else
                {
                    //alert(data.errortext);
                }

                $('#savewbsstructureitembutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.deletewbsstructureitembutton',function(){
    var workid=$(this).val();
    var r = confirm("Are you sure you want to delete this Schedule Item?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects1/deletestructureitem',
            beforeSend : function(){
                $('#deletewbsstructureitembutton'+workid).attr("disabled", true);
            },
            dataType: "json",
            data: {itemId:workid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#structureitemrow'+data.Id).remove();
                }
                else
                {
                   // alert(data.errortext);
                }

                $('#deletewbsstructureitembutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

$(document).on('change','#schedule_item_first',function(){
    var scheduleItem=$(this).val();
    // if($('#schedule_item_second').val()=='' )
    //     $('#schedule_item_second').val(scheduleItem);
    $.ajax({
        type: 'POST',
        url: '../projects1/getscheduleactivity',
        dataType: "json",
        data: {scheduleItem: scheduleItem,projectid:$('#selectedProjectId').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#schedule_activity_first').html(data.result);
            }
        }
    });
});

$(document).on('change','#schedule_item_second',function(){
    var scheduleItem=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../projects1/getscheduleactivity',
        dataType: "json",
        data: {scheduleItem: scheduleItem,projectid:$('#selectedProjectId').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#schedule_activity_second').html(data.result);
            }
        }
    });
});

$(document).on('change','.editrelationprecedentitem',function(){
     var scheduleItem= $(this).val();
    var dataid= $(this).attr("data-id");
   // alert(scheduleItem)
    // if($('#schedule_item_second').val()=='' )
    //     $('#schedule_item_second').val(scheduleItem);
    $.ajax({
        type: 'POST',
        url: '../projects1/getscheduleactivity',
        dataType: "json",
        data: {scheduleItem: scheduleItem,projectid:$('#selectedProjectId').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#editrelationprecedentactivity'+dataid).html(data.result);
            }
        }
    });
});

$(document).on('change','.editrelationdependentitem',function(){
    var scheduleItem= $(this).val();
    var dataid= $(this).attr("data-id");
    //alert(scheduleItem)
    // if($('#schedule_item_second').val()=='' )
    //     $('#schedule_item_second').val(scheduleItem);
    $.ajax({
        type: 'POST',
        url: '../projects1/getscheduleactivity',
        dataType: "json",
        data: {scheduleItem: scheduleItem,projectid:$('#selectedProjectId').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#editrelationdependentactivity'+dataid).html(data.result);
            }
        }
    });
});

function getProjectname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/Getname',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}

function WorkGroupNameExists(name,project)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../workgroups1/checkname',
        async:false,
        data: {name:name,project:project},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}

$(document).on('change','#estimatestructure',function(){
  var structueid=$(this).val();
   if(structueid==''){
      $( "#listworkgroup" ).trigger( "click" );
      $('#schedulestructure').hide();
      $('.schedulesuccess').hide();
      $('#wbsestimatename').show();
      $('#iowstructurename').show();
      $('#duplicatewbs').hide();
      $('#editestimatestructure').hide();
      $('#deleteestimatestructure').hide();
      $('#schedulestructure').val(structueid);
   }
   else{
      $('.schedulesuccess').hide();
      $('#estimatestructure').val(structueid);
      $('#schedulestructure').val(structueid);
      $('#duplicatewbs').val(structueid);
      $( "#listworkgroup" ).trigger( "click" );
      //$('#schedulestructure').show();
      $('#wbsestimatename').hide();
      $('#iowstructurename').hide();
      $('#duplicatewbs').show();
      $('#editestimatestructure').show();
      $('#saveestimatestructure').hide();
      $('#deleteestimatestructure').show();
      $('#editestimatestructure').val(structueid);
      $('#saveestimatestructure').val(structueid);
      $('#deleteestimatestructure').val(structueid);
      //$( ".schedulestructure" ).trigger( "click" );
   }
});

$(document).on('click','.editestimatestructure',function(){
    $('#editestimatestructure').hide();
    $('#saveestimatestructure').show();
    $('#estimatestructure').hide();
    var structurename = $( "#estimatestructure option:selected" ).text();
    $('#newwbsestimatename').show();
    $('#newwbsestimatename').val(structurename);
});

$(document).on('click','.saveestimatestructure',function(){
  var structureid=$(this).val();
  var structurename = $('#newwbsestimatename').val();
    if(structurename!='')
    {
            $.ajax({
                type: 'POST',
                url: '../projects1/newestimatestructurename',
                beforeSend : function(){

                },
                dataType: "json",
                data: {structureid:structureid,projectid:$('#selectedProjectId').val(),structurename:structurename},
                success: function(data){

                    if(data.error=='No')
                    {
                     $('#editestimatestructure').show();
                     $('#saveestimatestructure').hide();
                     $('#deleteestimatestructure').show();
                     $('#newwbsestimatename').hide();
                     $('#estimatestructure').show();
                     $( "#listworkgroup" ).trigger( "click" );
                    }
                    else
                    {
                     
                    }
                  //  $('#oldstructure').attr("disabled", true);
                }
            });
   }
  
});

$(document).on('click','.deleteestimatestructure',function(){
  var structureid=$(this).val();
  var r = confirm("Are you sure you want to delete this Estimate Structure?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects1/deleteestimatestructurename',
            beforeSend : function(){
               
            },
            dataType: "json",
            data: {structureid:structureid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editestimatestructure').hide();
                    $('#deleteestimatestructure').hide();
                    $( "#listworkgroup" ).trigger( "click" );
                }
                else
                {
                   // alert(data.errortext);
                }

            }
        });
    }
  
});

$(document).on('click','.schedulestructure',function(){
          var structureid = $('#schedulestructure').val();
          if(structureid!=''){
            $.ajax({
                type: 'POST',
                url: '../projects1/estimatestructure',
                beforeSend : function(){

                },
                dataType: "json",
                data: {structureid:structureid,projectid:$('#selectedProjectId').val()},
                success: function(data){

                    if(data.error=='No')
                    {
                     $('#listworkgroup').trigger( "click" );
                     $('.schedulesuccess').hide();
                    }
                    else
                    {
                     $('.schedulesuccess').hide();
                    }
                  //  $('#oldstructure').attr("disabled", true);
                }
            });
          }
});
