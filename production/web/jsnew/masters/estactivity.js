var taskRowCount = 1;

$(document).on( "click", ".prjct-estactvts", function(){
    $(".search-and-actions-wrpr").show();
    $('#Ar-allocate-body-two').hide();
    $('.add-activity-form').hide();
    $('.edit-activity-form').hide();
    $('#estactivityitems').show();
    $('#listestactivity').trigger('click');
});

$(document).on( "click", "#act-lib-actvty", function(){

    $('#collapsemasteract').hide();

});    

$(function() {
    // project section function
    // list project click
    $('#listestactivity').click(function () {

        $.ajax({
            type: 'POST',
            url: '../projects/listestactivity',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {estactivityname:$('#searchestactivityname').val(),estactivitytype:$('#searchestactivitytype').val(),estworktype:$('#searchestworktypelist').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#collapsemasteract').show();
                    $('#estactivityitems').html(data.result);
                    $('.estactivitytypename').html(data.estactypename);
                    $('#estactivitytable').show();
                    $('.content-action-wrpr').show();
                    
                    if(data.displaywt=='show'){
                      $('#searchestworktypediv').show();
                      $('#space').removeClass('col-md-5 col-sm-5');
                      $('#space').addClass('col-md-2 col-sm-2');
                      $('#estworktypedivs').show();
                      $('#estworktypedisplay').val(1);
                    }
                    else{
                        $('#searchestworktypediv').hide();
                        $('#space').removeClass('col-md-2 col-sm-2');
                        $('#space').addClass('col-md-5 col-sm-5');
                        $('#estworktypedivs').hide();
                        $('#estworktypedisplay').val(0);
                    }
                }

                if (data.error == 'No') {
                    $('#estactivityitems table tbody').sortable({
                        placeholder: 'ui-state-highlight',
                        helper: 'clone',
                        update: function(event, ui) {
                            var updatedrows = [];
                            $('#estactivityitems .esttypesort').each(function() {
                                updatedrows.push({ rowid: $(this).data('id'), rowindex: $(this).index(), rowtype: $(this).data('type') });
                            });
                            $.ajax({ type:'POST', url:'../projects/updateestactivitiessort', data:{datavalue:updatedrows}, dataType:'json', success:function(){} });
                        }
                    }).disableSelection();
                }
                $('.preloader').hide();
            }
        });

    });
    $('#estactivitysearch').click(function(){
      $('#listestactivity').trigger('click')
    });
    $(document).on("keyup", "#searchestactivityname", function(){
        $('#listestactivity').trigger('click')
    });
    $('#searchestworktypelist').change(function(){
        $('#listestactivity').trigger('click')
    });

    // Add new task row
    $(document).on('click', '.add-task-row', function(){
        taskRowCount++;
        var row = '<div class="row task-row" id="task-row-' + taskRowCount + '" style="margin-top:4px;">' +
            '<div class="col-md-1"></div>' +
            '<div class="col-md-4"><input type="text" class="form-control task-name" name="task_name[]" placeholder="Task Name"></div>' +
            '<div class="col-md-2"><input type="text" class="form-control task-unit" name="task_unit[]" placeholder="Unit"></div>' +
            '<div class="col-md-3"><input type="number" step="0.001" min="0" class="form-control task-productivity" name="task_productivity[]" placeholder="Productivity/Wh"></div>' +
            '<div class="col-md-1"><button type="button" class="remove-task-row" style="background:none;border:none;padding:6px 4px;"><span class="icon-minus" style="color:#d9534f;font-size:18px;"></span></button></div>' +
            '</div>';
        $('#task-rows-container').append(row);
    });

    $(document).on('click', '.remove-task-row', function(){
        $(this).closest('.task-row').remove();
    });

    $(document).on('click', '#saveestactivity', function(){
        var error=0;
        $('.error').hide();
        $('#estactivitytypeid').next('span').hide();
        var acttype = $('#estactivitytypeid').val();
        if (!acttype || acttype === '0') {
            $('#estactivitytypeid').next('span').html('Select activity type').show('slow');
            error = 1;
        }
        $('.estactivityname').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#estactivityname"+id).next("span").html('Enter Activity Name').show('slow');
                error=1;
            }
        });
        $('.estactivityunit').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#estactivityunit"+id).next("span").html('Enter Activity Unit').show('slow');
                error=1;
            }
        });
        if($('#estworktypedisplay').val()==1)
        {
            if($('#estworktypelist').val()=='0')
            {
                $("#estworktypelist").next("span").html('Select Work Type').show('slow');
                error=1;
            }
        }

        

        // if($('#estworktypelistses').val()=='0')
        // { alert("sadsa"); 
        //     if($('#estworktypelistses').val()=='0')
        //     {
        //         $("#estworktypelistss"+id).next("span").html('Select Project Type').show('slow');
        //         error=1;
        //     }
        // }
        // var el = $('.estworktypelistses');
        // if(!$(el).is("select")) {
        //     // the input field is not a select
        // }else{

            if(!$('#estworktypelistss1').is(':visible'))
            {
                

            }else{ 

        $('.estworktypelistses').each(function(){  
            var ids = $('#estworktypelistss1').val();
            if(ids==0)
            { 
                $("#estworktypelistss1").next("span").html('Select Project Type').show('slow');
                error=1;
            }
        });

      }


        //  if($('#estworktypedisplayy').val()==1)
        // {
        //     if($('#estworktypelist').val()=='0')
        //     {
        //         $("#estworktypelist").next("span").html('Select Work Type').show('slow');
        //         error=1;
        //     }
        // }
        
        // if($('#estactivityname').val()=='')
        // {
        //     $("#estactivityname").next("span").html('Enter Activity Name').show('slow');
        //     error=1;
        // }
        // if($('#estactivitytypelist').val()=='none')
        // {
        //     $("#estactivitytypelist").next("span").html('Select Activity Type').show('slow');
        //     error=1;
        // }
        //var name=$('#estactivityname').val();
        //var activitytype=$('#searchestactivitytype').val();
        //var worktype=$('#estworktypelist').val();

        if(error==0){
            var editingId = $('#editingActivityId').val();
            var url = editingId ? '../projects/updateactivitywithtasks' : '../projects/createactivity';

            var taskNames = [], taskUnits = [], taskProds = [];
            $('#task-rows-container .task-row').each(function(){
                taskNames.push($(this).find('.task-name').val());
                taskUnits.push($(this).find('.task-unit').val());
                taskProds.push($(this).find('.task-productivity').val());
            });
            var postData = {
                activity_id:          $('#editingActivityId').val(),
                activitytypeid:       $('#estactivitytypeid').val(),
                worktypeid:           $('#estworktypelistss1').val() || 0,
                working_hours:        $('#est_working_hours').val(),
                'estactivityname[]':  [$('#estactivityname1').val()],
                'estactivityunit[]':  [$('#estactivityunit1').val()],
                'task_name[]':        taskNames,
                'task_unit[]':        taskUnits,
                'task_productivity[]':taskProds
            };

            $.ajax({
                type:'POST',
                url: url,
                beforeSend:function(){
                    $('#saveestactivity').attr("disabled", true);
                },
                dataType:'json',
                data: postData,
                success:function(data){
                    if(data.error=='No')
                    {
                        $(".search-and-actions-wrpr").show();
                        $('#estactivityform')[0].reset();
                        $('#task-rows-container .task-row:not(#task-row-1)').remove();
                        taskRowCount = 1;
                        $(".add-activity-form").hide();
                        $("#estactivityitems").show();
                        $('#listestactivity').trigger('click');
                        $('#saveestactivity').attr("disabled", false);
                        $('.panel-default').removeClass('add-form-active');
                        $('#editingActivityId').val('');
                        $('#saveestactivity').html('<span class="icon-check"></span> Add Activity');
                    } else {
                        alert('Save failed: ' + (data.msg || 'Unknown error'));
                        $('#saveestactivity').attr("disabled", false);
                    }
                },
                error:function(xhr){
                    alert('Save failed. Please try again. (' + xhr.status + ')');
                    $('#saveestactivity').attr("disabled", false);
                }
            });
        }
    });
    
});

$(document).on( "click", ".editestactivitybutton", function(){
    var idval=$(this).val()
    $('#editestactivity'+idval).show();
    $('#estactivitytext'+idval).hide();
    $('#editestactivitybutton'+idval).hide();
    $('#saveestactivitybutton'+idval).show();
} );

$(document).on( "click", ".close-prjctactactvty", function(){
    $(".resource-not-allocated").css("cssText", "display:none  !important;");
    $(".allocation-cntnt-wrpr").css("cssText", "display:none  !important;");
    //$('.resource-not-allocated').hide();
    $('#Ar-allocate-body-two').hide();
    $("#estactivityitems").show();
    $(".search-and-actions-wrpr").show();
    $('#estactivitysearch').trigger('click');
} );

$(document).on( "click", "#saveactivitybutton", function(){
    var idval=$(this).val();
    var activityname=$('#editactivity').val();
    var activityunit=$('#editactivityunit').val();
    var error=0;
    $('.error').hide();
    if($('#editestactivity'+idval).val()=='')
    {
        $('#editestactivity'+idval).next("span").html('Enter Activity Name').show('slow');
        error=1;
    }
    if($('#editactivity').val()=='')
    {
        $('#editactivity').next("span").html('Enter Activity Name').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../projects/updateactivity',
            beforeSend : function(){
                $('#saveestactivitybutton').attr("disabled", true);
            },
            dataType: "json",
            data: {activityid:idval,activityname:activityname,activityunit:activityunit},
            success: function(data){

                if(data.error=='No')
                {
                        $(".search-and-actions-wrpr").show();
                        $(".edit-activity-form").hide();
                        $("#estactivityitems").show();
                        $('#listestactivity').trigger('click');
                        $('#saveactivitybutton').attr("disabled", false);
                  
                }
                $('#saveestactivitybutton'+data.Id).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deleteestactivitybutton", function(){
    var idval=$(this).data("id");
    var r = confirm("Are you sure you want to delete this Activity ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../projects/deleteactivity',
            beforeSend : function(){
                $('#deleteestactivitybutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {activityid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $(".search-and-actions-wrpr").show();
                    $('#estactivityrow'+data.Id).remove();
                    //$('#listworktype').trigger('click');
                }
               /*  else{
                    alert("Activity already assigned to project")
                } */
                $('#deleteestactivitybutton'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
$(document).on( "click", "#addestactivity", function(){
    $(".estactivityname").val('');
    $(".search-and-actions-wrpr").hide();
    $(".estactivityunit").val('');
    $(".remove_field").trigger('click');
    $('#estactivitytypeid').val('0');
    $(".add-activity-form").show();
    $("#estactivityitems").hide();

});
$(document).on( "click", ".cancelactivity", function(){
     $(".add-activity-form").hide();
     $("#estactivityitems").show();
     $(".edit-activity-form").hide();
     $(".search-and-actions-wrpr").show();
     $('.remove_field').trigger('click');
     $('.content-action-wrpr').show();
     $('.panel-default').removeClass('add-form-active');
     $('#editingActivityId').val('');
     $('#estactivitytypeid').val('0');
     $('#saveestactivity').html('<span class="icon-check"></span> Add Activity');
});

$(document).on('click', '.editactivitybutton', function(){
    var idval = $(this).data('id');
    $.ajax({
        type: 'POST',
        url: '../projects/getactivityforedit',
        dataType: 'json',
        data: {activity_id: idval},
        error: function(xhr) {
            alert('Could not load activity (' + xhr.status + '). Please try again.');
        },
        success: function(data){
            if(data.error == 'No'){
                var act = data.activity;
                $('#estactivitytypeid').val(act.activity_type);
                $('#estactivityname1').val(act.activity_name);
                $('#estactivityunit1').val(act.activity_unit);
                $('#est_working_hours').val(act.working_hours);
                $('#task-rows-container').empty();
                taskRowCount = 0;
                if(data.tasks && data.tasks.length > 0){
                    $.each(data.tasks, function(i, task){
                        taskRowCount++;
                        var btnHtml = (taskRowCount === 1)
                            ? '<button type="button" class="add-task-row" style="background:none;border:none;padding:6px 4px;"><span class="icon-add" style="color:#337ab7;font-size:18px;"></span></button>'
                            : '<button type="button" class="remove-task-row" style="background:none;border:none;padding:6px 4px;"><span class="icon-minus" style="color:#d9534f;font-size:18px;"></span></button>';
                        var row = '<div class="row task-row" id="task-row-' + taskRowCount + '" style="margin-top:4px;">' +
                            '<div class="col-md-1"></div>' +
                            '<div class="col-md-4"><input type="text" class="form-control task-name" name="task_name[]" placeholder="Task Name" value="' + $('<div/>').text(task.task_name).html() + '"></div>' +
                            '<div class="col-md-2"><input type="text" class="form-control task-unit" name="task_unit[]" placeholder="Unit" value="' + $('<div/>').text(task.task_unit).html() + '"></div>' +
                            '<div class="col-md-3"><input type="number" step="0.001" min="0" class="form-control task-productivity" name="task_productivity[]" placeholder="Productivity/Wh" value="' + parseFloat(task.productivity).toFixed(3) + '"></div>' +
                            '<div class="col-md-1">' + btnHtml + '</div>' +
                            '</div>';
                        $('#task-rows-container').append(row);
                    });
                } else {
                    taskRowCount = 1;
                    $('#task-rows-container').append(
                        '<div class="row task-row" id="task-row-1" style="margin-top:4px;">' +
                        '<div class="col-md-1"></div>' +
                        '<div class="col-md-4"><input type="text" class="form-control task-name" name="task_name[]" placeholder="Task Name"></div>' +
                        '<div class="col-md-2"><input type="text" class="form-control task-unit" name="task_unit[]" placeholder="Unit"></div>' +
                        '<div class="col-md-3"><input type="number" step="0.001" min="0" class="form-control task-productivity" name="task_productivity[]" placeholder="Productivity/Wh"></div>' +
                        '<div class="col-md-1"><button type="button" class="add-task-row" style="background:none;border:none;padding:6px 4px;"><span class="icon-add" style="color:#337ab7;font-size:18px;"></span></button></div>' +
                        '</div>'
                    );
                }
                $('#editingActivityId').val(idval);
                $('#saveestactivity').html('<span class="icon-check"></span> Update Activity');
                $(".search-and-actions-wrpr").hide();
                $(".add-activity-form").show();
                $("#estactivityitems").hide();
            }
        }
    });
});

 $(document).on( "click", "#resourceallocation", function(){

     //$('.resourceallocationaccordian').trigger('click');

     $('.close-resource-list-btn').trigger('click');
     //
     $('#AR-allocate-body-one-head').hide();
     $('#estactivityitems').hide();
     $('#Ar-allocate-body-two').show();
     $('#add-activity-cbody').show();

     $('#estactivity').removeClass('edit-form-active')

     $('.resources-from-selected-item-wrpr').hide();
     var idval = $(this).data("id");
     $('#Estimate-allocate-body-two').html('');
    $('#schedule-allocate-body-two').html('');
     $('#EstActivity_Id').val(idval);
        $.ajax({
             type: 'POST',
             url: '../projects/allocationlist',
             beforeSend : function(){
                $('.preloader').show();
             },
             dataType: "json",
             data: {id:idval},
             success: function(data){
                 if(data.error=='No')
                 { 

                     $('.items-select-bar').html(data.result);
                     $('.allocation-list-items-master').html(data.listadded);
                     $('.activityrate span').html(data.activitynameval);
                     $('#activityrate').html(data.activitynameval);
                     $('#activityratenow').html(data.activitynamerate);
                     $('#activityunt').html(data.actvtyunit);
                     $('.resource-not-allocated').removeClass('resource-not-allocated');
                     $('.added-item-allocation-save-btn-wrpr').addClass('saving');
                     $('.saving').children('.button-label').text('Saving');
                     setTimeout(function(){
                         $('.resources-from-selected-item-wrpr').addClass('resource-saved');
                         $('.allocation-right-bar').addClass('resource-saved');
                         $('.ton-column').addClass('col-md-2');
                         $('.amount-column').addClass('col-md-3');
                     }, 400);
                     if(data.added==0)
                     {
                         $('.resource-saved').hide();
                         $('.resource-not-allocated-info').css('display','flex');
                         $('.allocation-cntnt-wrpr').addClass('resource-not-allocated');
                         $('.infodisplay').html('<div style="margin-top: 11px;" class="info-box"><span class="icon-info"></span> <span>Resources not added. Please add resources.  </span></div>');
                                                
                     }
                     else
                     {
                          $('.infodisplay').hide();
                    }

                 }

                 $('.preloader').hide();
             }
         });

 });

$(document).on( "click", ".added-item-allocation-save-btn", function(){
    var idval = $('#EstActivity_Id').val();
    ;
        $.ajax({
            type: 'POST',
            url: '../projects/allocationlist',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {id:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('.items-select-bar').html(data.result);
                    $('.added-alloc-items-wrpr').html(data.listadded);
                    $('.resource-not-allocated').removeClass('resource-not-allocated');
                    $('.added-item-allocation-save-btn-wrpr').addClass('saving');
                    $('.saving').children('.button-label').text('Saving');
                    setTimeout(function(){
                        $('.resources-from-selected-item-wrpr').addClass('resource-saved');
                        $('.allocation-right-bar').addClass('resource-saved');
                        $('.ton-column').addClass('col-md-2');
                        $('.amount-column').addClass('col-md-3');
                    }, 400);

                }

                $('.preloader').hide();
            }
        });

});

$(document).on( "click", "#addedlist", function(){
    var idval = $('#EstActivity_Id').val();
        $.ajax({
            type: 'POST',
            url: '../projects/allocationlist',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {id:idval},
            success: function(data){
                if(data.error=='No')
                {
                    if(data.added==0)
                    {
                       $('.allocation-list-items-cntnr').hide();
                        $('#alredyadded').show();
                        $('.infodisplay').show();
                    }
                    else
                    {
                        $('.infodisplay').hide();
                        $('#alredyadded').hide();
                        //$('.resource-not-allocated-info').css('display','flex');
                        //$('.icon-info3').css('margin-top','-230px');
                        //$('.info-text').css('margin-top','-100px');
                        $('.allocation-list-items-cntnr').show();
                    }
                    //$('.items-select-bar').html(data.result);
                    $('.allocation-list-items-cntnr').html(data.listadded);
                    //$('.resource-not-allocated').removeClass('resource-not-allocated');
                    //$('.added-item-allocation-save-btn-wrpr').addClass('saving');
                    //$('.saving').children('.button-label').text('Saving');
                   

                }

                $('.preloader').hide();
            }
        });

});

$(document).on( "click", ".resourcesearch", function(){
        var resorcetypeid=$(this).data("id");
        $('#resourcegroupval').val(resorcetypeid);
        var resgroup = $('#resourcegroup1').val();
        var activity_id = $('#EstActivity_Id').val();
        $('.preloader').show();
        console.log(activity_id);
        $.ajax({
            type: 'POST',
            url: '../projectsetup/resourcesearchbytyid',
            dataType: "json",
            data: {activity_id:activity_id,resourcetypeid:resorcetypeid,resgroup:resgroup},
            success: function(data){
                if(data.error=='No')
                {
                    $('.preloader').hide();
                    $('.allocation-items-master').html(data.result);
                    $('.activityid').html(data.activityname);
                    //$('#restypeid').val(resorcetypeid);
                    //$('#resourcegroupselection').html(data.group);
                    //$('#resourcetable').show();

                }
                else
                {
                    alert(data.errortext);
                }

                $('.preloader').hide();
            }
        });
    });

    //$(document).on( "change","input[type=radio][name=mpequ-select]", function(){
    $(document).on( "change",".mpequ-select", function(){
        var resid=$(this).data("resid"); 
        var restype = $(this).data("type"); 
        var type=this.value;

        $.ajax({
            type:'POST',
            url:'../resources/masterplantestdata',
            dataType:'json',
            data: {type:type,resid:resid,restype:restype},
            success:function(data){
                if(data.error=='No')
                {
                    $('.plantrow'+resid).html(data.datarows);
                }
            }
        });

    });

    $(document).on( "change","input[type=radio][name=mlequ-select]", function(){
        var resid=$(this).data("resid"); 
        var restype = $(this).data("type"); 
        var type=this.value;

        $.ajax({
            type:'POST',
            url:'../resources/masterestdata',
            dataType:'json',
            data: {type:type,resid:resid,restype:restype},
            success:function(data){
                if(data.error=='No')
                {
                    $('.mleasedrow'+resid).html(data.datarows);
                }
            }
        });

    });


$(document).on( "click", ".master-addresource", function(){
    var resid=$(this).val();
    var error=0;
    var actid = $('#EstActivity_Id').val();
    var restype = $('#mastalloc').val();

    $('.error').hide();
    /* if(!$.isNumeric($('#master-resourcequantity'+resid).val()))
    {
        $('#master-resourcequantity'+resid).next("span").html('Enter Valid Quantity').show('slow');
        error=1;
    }  */

    if(restype == 33){

        var mannumber = $('#estimate-mannumbr'+resid).val();
        var mandays = $('#estimate-no-ofdays'+resid).val();
        var man_qty = $('#estimate-qty'+resid).val();

        if(str=='' || mannumber=='' || mandays=='' || man_qty=='')
        {
            error=1;
            alert('Enter the fields')
        }

        if(error==0)
        {
            $('.preloader').show();
            var str=$('#estimate-specificrate'+resid).val();
            var resourcerate=str.replace(/,/g, '');
           // resourceqty = $('#master-resourcequantity'+resid).val();;

            $.ajax({
                type:'POST',
                url:'../projects/addactivityresource',
                beforeSend:function(){
                    $('#addresourcebutton'+resid).attr("disabled",true);
                    $('.preloaderitems').show();
                },
                dataType:"json",
                data:{EstActivity_Id:$('#EstActivity_Id').val(),resid:resid, resourcerate:resourcerate, mannumber:mannumber, mandays:mandays, man_qty:man_qty, restype:restype},
                success:function(data){
                    $('.allocation-cntnt-wrpr').addClass('resource-not-allocated');
                    $('.preloader').hide();
                    $('.infodisplay').hide();
                    $('.allocation-list-items-cntnr').show();
                    $('#addedresources').html(data.result);
                    $('#addresourcebutton'+resid).attr("disabled",false);
                    $('#master-resourcequantity'+resid).val('');
                    $('.preloaderitems').hide();
                    $('#resourcerow'+resid).remove();
                    $('#estactresourceratetotal').val(data.price);
                    $('#activityratenow').html('Rate : '+data.price);
                    $('#addedlist').trigger('click');
                    $('.activityrate span').html(data.activityhead);
                    $('#activityrate').html(data.activityhead);
                    $('#listestactivity').trigger('click');
                    //$('.res_actlibrary_'+actid).trigger('click');
                    //$('.close-resource-list-btn').trigger('click');
                    
                    //$('#amount').val(data.amount);
                }
            });

        }

    }
    else if(restype == 26)
    {
        
       
        var type = document.querySelector('input[name="mpequ-select'+resid+'"]:checked').value;

        if (type == 'mplant_no_of_hrs') {            

            var fueltype = $('#fueltype'+resid).val();
            var fuel_vendor = $('#fuel_vendor'+resid).val();
            var fuelrate = $('#mast-fuelrate'+resid).val();
            var fuelqty = $('#mast-fuelqty'+resid).val();
            var hoursno = $('#estimate-resourcetime'+resid).val();
            var repair = $('#mast-repair'+resid).val();

            if(fuelrate=='' || fuelqty=='' || hoursno==''){
                error=1;
                alert('Enter the fields')
            }

            if(error==0)
            {
                $('.preloader').show();
                $.ajax({
                    type:'POST',
                    url:'../projects/addactivityresource',
                    beforeSend:function(){
                        $('#addresourcebutton'+resid).attr("disabled",true);
                        $('.preloaderitems').show();
                    },
                    dataType:"json",
                    data:{  EstActivity_Id:$('#EstActivity_Id').val(),
                            resid:resid,
                            repair:repair,
                            fuelrate:fuelrate,
                            fuelqty:fuelqty,
                            hoursno:hoursno,
                            fueltype:fueltype, 
                            fuel_vendor: fuel_vendor,
                            restype:restype,
                            type:type
                        },
                    success:function(data){
                        $('.allocation-cntnt-wrpr').addClass('resource-not-allocated');
                        $('.preloader').hide();
                        $('.infodisplay').hide();
                        $('.allocation-list-items-cntnr').show();
                        $('#addedresources').html(data.result);
                        $('#addresourcebutton'+resid).attr("disabled",false);
                        $('#master-resourcequantity'+resid).val('');
                        $('.preloaderitems').hide();
                        $('#resourcerow'+resid).remove();
                        $('#estactresourceratetotal').val(data.price);
                        $('#activityratenow').html('Rate : '+data.price);
                        $('#addedlist').trigger('click');
                        $('.activityrate span').html(data.activityhead);
                        $('#activityrate').html(data.activityhead);
                        $('#listestactivity').trigger('click');
                        //$('.res_actlibrary_'+actid).trigger('click');
                        //$('.close-resource-list-btn').trigger('click');
                        
                        //$('#amount').val(data.amount);
                    }
                });
            }

        }else if (type == 'mdepreciation') {

            var str=$('#estimate-plantrate'+resid).val();
            var resourcerate=str.replace(/,/g, '');
            var dep_qty = $('#estimate-dep-qty'+resid).val();

            $.ajax({
                type:'POST',
                url:'../projects/addactivityresource',
                beforeSend:function(){
                    $('#addresourcebutton'+resid).attr("disabled",true);
                    $('.preloaderitems').show();
                },
                dataType:"json",
                data:{EstActivity_Id:$('#EstActivity_Id').val(), resid:resid, res_qty:dep_qty, resourcerate:resourcerate, restype:restype, type:type},
                success:function(data){
                    $('.allocation-cntnt-wrpr').addClass('resource-not-allocated');
                    $('.preloader').hide();
                    $('.infodisplay').hide();
                    $('.allocation-list-items-cntnr').show();
                    $('#addedresources').html(data.result);
                    $('#addresourcebutton'+resid).attr("disabled",false);
                    $('#master-resourcequantity'+resid).val('');
                    $('.preloaderitems').hide();
                    $('#resourcerow'+resid).remove();
                    $('#estactresourceratetotal').val(data.price);
                    $('#activityratenow').html('Rate : '+data.price);
                    $('#addedlist').trigger('click');
                    $('.activityrate span').html(data.activityhead);
                    $('#activityrate').html(data.activityhead);
                    $('#listestactivity').trigger('click');
                    //$('.res_actlibrary_'+actid).trigger('click');
                    //$('.close-resource-list-btn').trigger('click');
                    
                    //$('#amount').val(data.amount);
                }
            });




        }

    }else if(restype == 24)
    {
        var type = document.querySelector('input[name="mlequ-select"]:checked').value;

        if (type == 'mlno_of_hrs') {

            

            var fueltype = $('#fueltype'+resid).val();

            var fuelrate = $('#estimate-fuelrate'+resid).val();

            var fuelqty = $('#estimate-fuelqty'+resid).val();

            var hoursno = $('#estimate-resourcetime'+resid).val();

            

            if(fuelrate=='' || fuelqty=='' || hoursno==''){

                error=1;

                alert('Enter the fields')


                
            }


            if(error==0)
            {
                $('.preloader').show();
            
            

                $.ajax({
                    type:'POST',
                    url:'../projects/addactivityresource',
                    beforeSend:function(){
                        $('#addresourcebutton'+resid).attr("disabled",true);
                        $('.preloaderitems').show();
                    },
                    dataType:"json",
                    data:{EstActivity_Id:$('#EstActivity_Id').val(),resid:resid,fueltype:fueltype,fuelrate:fuelrate,fuelqty:fuelqty,hoursno:hoursno,restype:restype,type:type},
                    success:function(data){
                        $('.allocation-cntnt-wrpr').addClass('resource-not-allocated');
                        $('.preloader').hide();
                        $('.infodisplay').hide();
                        $('.allocation-list-items-cntnr').show();
                        $('#addedresources').html(data.result);
                        $('#addresourcebutton'+resid).attr("disabled",false);
                        $('#master-resourcequantity'+resid).val('');
                        $('.preloaderitems').hide();
                        $('#resourcerow'+resid).remove();
                        $('#estactresourceratetotal').val(data.price);
                        $('#activityratenow').html('Rate : '+data.price);
                        $('#addedlist').trigger('click');
                        $('.activityrate span').html(data.activityhead);
                        $('#activityrate').html(data.activityhead);
                        $('#listestactivity').trigger('click');
                        //$('.res_actlibrary_'+actid).trigger('click');
                        //$('.close-resource-list-btn').trigger('click');
                        
                        //$('#amount').val(data.amount);
                    }
                });

            }




         
        }else if (type == 'mlease_period') {

            var leaseperiod = $('#estimate-leaseperiod'+resid).val();

            var str = $('#estimate-specificrate'+resid).val();
            var resourcerate = str.replace(/,/g, '');

           


            $.ajax({
                type:'POST',
                url:'../projects/addactivityresource',
                beforeSend:function(){
                    $('#addresourcebutton'+resid).attr("disabled",true);
                    $('.preloaderitems').show();
                },
                dataType:"json",
                data:{EstActivity_Id:$('#EstActivity_Id').val(),resid:resid,leaseperiod:leaseperiod,resourcerate:resourcerate,restype:restype,type:type},
                success:function(data){
                    $('.allocation-cntnt-wrpr').addClass('resource-not-allocated');
                    $('.preloader').hide();
                    $('.infodisplay').hide();
                    $('.allocation-list-items-cntnr').show();
                    $('#addedresources').html(data.result);
                    $('#addresourcebutton'+resid).attr("disabled",false);
                    $('#master-resourcequantity'+resid).val('');
                    $('.preloaderitems').hide();
                    $('#resourcerow'+resid).remove();
                    $('#estactresourceratetotal').val(data.price);
                    $('#activityratenow').html('Rate : '+data.price);
                    $('#addedlist').trigger('click');
                    $('.activityrate span').html(data.activityhead);
                    $('#activityrate').html(data.activityhead);
                    $('#listestactivity').trigger('click');
                    //$('.res_actlibrary_'+actid).trigger('click');
                    //$('.close-resource-list-btn').trigger('click');
                    
                    //$('#amount').val(data.amount);
                }
            });


            



            
        }



    }else{

        if(error==0)
        {
        $('.preloader').show();
        var price=$('#master-resourceprice'+resid).val();
        var str=$('#estimate-specificrate'+resid).val();
        var sprice=str.replace(/,/g, '');
        var quantity=$('#estimate-resourcequantity'+resid).val();
        $.ajax({
            type:'POST',
            url:'../projects/addactivityresource',
            beforeSend:function(){
                $('#addresourcebutton'+resid).attr("disabled",true);
                $('.preloaderitems').show();
            },
            dataType:"json",
            data:{EstActivity_Id:$('#EstActivity_Id').val(),resid:resid,rate:price,srate:sprice,quantity:quantity,restype:restype,type:type},
            success:function(data){
                $('.allocation-cntnt-wrpr').addClass('resource-not-allocated');
                $('.preloader').hide();
                $('.infodisplay').hide();
                $('.allocation-list-items-cntnr').show();
                $('#addedresources').html(data.result);
                $('#addresourcebutton'+resid).attr("disabled",false);
                $('#master-resourcequantity'+resid).val('');
                $('.preloaderitems').hide();
                $('#resourcerow'+resid).remove();
                $('#estactresourceratetotal').val(data.price);
                $('#activityratenow').html('Rate : '+data.price);
                $('#addedlist').trigger('click');
                $('.activityrate span').html(data.activityhead);
                $('#activityrate').html(data.activityhead);
                $('#listestactivity').trigger('click');
                
            }
        });
    }

    }

    


    
});

$(document).on( "click", ".removeresourceitem", function(){
    var resid=$(this).data("id");
    $('.preloader').show();
    $.ajax({
        type:'POST',
        url:'../projects/deleteactivityresource',
        beforeSend:function(){
            $('#removeresourceitem'+resid).attr("disabled",true);
            $('.preloaderitems').show();
        },
        dataType:"json",
        data:{resid:resid,activityid:$('#EstActivity_Id').val()},
        success:function(data){
            $('.preloader').hide();
            $('#tempresrow'+resid).remove();
            $('.res-masterrow'+resid).remove();
            //$('#addedresources').html(data.result);
            $('#estactresourceratetotal').val(data.price);
            $('#activityratenow').html('Rate : '+data.price);
            //$('#amount').val(data.amount);
            $('#removeresourceitem'+resid).attr("disabled",false);
            $('.preloaderitems').hide();
            $('.activityrate span').html(data.activityhead);
            $('#activityrate').html(data.activityhead);
            $('#listestactivity').trigger('click');
            //$('#resourceallocation').trigger('click');
            $('.close-resource-list-btn').trigger('click');
        }
    });
});

$(document).on( "click", ".updatesallocateresourceunit", function(){
    var mid = $(this).attr('data-v');
    $("#updatesallocateresourceunit"+mid).hide();
    $("#allocateresourceunit"+mid).hide();
    $("#allocateresourceqty"+mid).hide();
    $("#editallocateresourceunit"+mid).show();
    $("#editallocateresourceqty"+mid).show();
    $("#savesallocateresourceunit"+mid).show();

});

$(document).on( "click", ".savesallocateresourceunit", function(){
    var mid = $(this).attr('data-v');
    var dactivity = $(this).attr('data-activity');
    //var updatevalue = $("#editallocateresourceunit"+mid).val();

    var str = $("#editallocateresourceunit"+mid).val();
    var updatevalue = str.replace(/,/g, '');


    var updateqty = $("#editallocateresourceqty"+mid).val();
    if(updatevalue != ''){
        $.ajax({
            type: 'POST',
            url: '../projects/updateallocaterrated',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {PRID:mid,dactivity:dactivity,updatevalue:updatevalue,updateqty:updateqty},
            success: function(data){
                if(data.error=='No')
                {
                	$("#editallocateresourceunit"+mid).hide();
                    $("#editallocateresourceqty"+mid).hide();
                    $("#savesallocateresourceunit"+mid).hide();
                    $("#updatesallocateresourceunit"+mid).show();
                    $("#allocateresourceunit"+mid).html(updatevalue);
                    $("#allocateresourceqty"+mid).html(updateqty);
                    $("#allocateresourceunit"+mid).show();
                    $("#allocateresourceqty"+mid).show();
                    $("#activityratenow").html('Rate: '+data.total);
                    $("#allocateres_amt"+mid).html(data.restotal);
                }

                $('.preloader').hide();
            }
        });

    }else{
        return true;
    }
});

/*$(document).on( "input", "#resourcegroup1", function(e){
        
        var resorcetypeid=$('#resourcegroupval').val();
        var resgroup = $('#resourcegroup1').val();
        $('.preloader').show();
        //console.log(resorcetypeid);
        $.ajax({
            type: 'POST',
            url: '../projectsetup/resourcesearchbytyid',
            dataType: "json",
            data: {resourcetypeid:resorcetypeid,resgroup:resgroup},
            success: function(data){
                if(data.error=='No')
                {
                    $('.preloader').hide();
                    $('.allocation-items-master').html(data.result);
                    //$('#restypeid').val(resorcetypeid);
                    //$('#resourcegroupselection').html(data.group);
                    //$('#resourcetable').show();

                }
                else
                {
                    alert(data.errortext);
                }

                $('.preloader').hide();
            }
        });
});*/

$(document).on( "click", "#actmaster-res", function(e){
        
        var resorcetypeid=$('#resourcegroupval').val();
        var resgroup = $('#resourcegroup1').val();
        $('.preloader').show();
        //console.log(resorcetypeid);
        $.ajax({
            type: 'POST',
            url: '../projectsetup/resourcesearchbytyid',
            dataType: "json",
            data: {resourcetypeid:resorcetypeid,resgroup:resgroup},
            success: function(data){
                if(data.error=='No')
                {
                    $('.preloader').hide();
                    $('.allocation-items-master').html(data.result);
                    //$('#restypeid').val(resorcetypeid);
                    //$('#resourcegroupselection').html(data.group);
                    //$('#resourcetable').show();

                }
                else
                {
                    alert(data.errortext);
                }

                $('.preloader').hide();
            }
        });
});


$(document).on( "click", ".close-popup", function(){

$('#listestactivity').trigger('click');

});

    function OnSearch () 
    {
        $('#resourcegroup1').trigger('keyup');
            
    }


$(document).on('click','.resource-list-btn', function(e){
    $('#Estimate-allocate-body-two').html('');
    $('#schedule-allocate-body-two').html('');

    $(".resource-not-allocated").css("cssText", "display:flex  !important;");
    $(".allocation-cntnt-wrpr").css("cssText", "display:flex  !important;");
    e.preventDefault();

    $('#resourcegroup1').val('');
    
    $('.allocate-resource-tabss').addClass('add-allocation-list-active');
    $('#addedlist').trigger('click');
    $('.allocation-cntnt-wrpr').addClass('resource-not-allocated');
    setTimeout(function() {
       //$("html, body").animate({ scrollTop: $('.allocate-resource-tab').offset().top }, 1000);
    }, 10);
});
                
$(document).on('click','.close-resource-list-btn', function(e){
    $(".resource-not-allocated").css("cssText", "display:none  !important;");
    e.preventDefault();   
    $('.allocate-resource-tabss').removeClass('add-allocation-list-active');
 });


$(document).on('change','.editallocateresourceqty', function(e){
    var mid = $(this).attr('data-v');
    var updatevalue = $("#editallocateresourceunit"+mid).val();
    var updateqty = $("#editallocateresourceqty"+mid).val();

    var amount = (updatevalue * updateqty).toFixed(2);

    $("#allocateres_amt"+mid).html(amount);
    
});

$(document).on('change','.editallocateresourceunit', function(e){
    var mid = $(this).attr('data-v');
    var updatevalue = $("#editallocateresourceunit"+mid).val();
    var updateqty = $("#editallocateresourceqty"+mid).val();

    var amount = (updatevalue * updateqty).toFixed(2);

    $("#allocateres_amt"+mid).html(amount);
    
});

$(document).on("change", "input[type=text][name=mastqty]", function () {

    var id = $(this).attr('data-id');
    
    var equconsumption = $('#mast-fuelqty'+id).val();

    var equrate = $('#mast-fuelrate'+id).val();

    var equmaintenancecost = $('#mast-repair'+id).val();

    if(equconsumption==''){

        equconsumption = 0;

    }

    if(equrate==''){

        equrate = 1;

    }

    if(equmaintenancecost==''){

        equmaintenancecost = 0;

    }

    var total1 = equconsumption * equrate;

    var total = (total1 + parseFloat(equmaintenancecost)).toFixed(2);

    $('#mast-machrate'+id).val(total);

}); 

$(document).on("change", "input[type=text][name=masteqrate]", function () {

    var id = $(this).attr('data-id');

    var equconsumption = $('#mast-fuelqty'+id).val();

    var equrate = $('#mast-fuelrate'+id).val();

    var equmaintenancecost = $('#mast-repair'+id).val();

    if(equconsumption==''){

        equconsumption = 0;

    }

    if(equrate==''){

        equrate = 1;

    }

    if(equmaintenancecost==''){

        equmaintenancecost = 0;

    }

    var total1 = equconsumption * equrate;

    var total = (total1 + parseFloat(equmaintenancecost)).toFixed(2);

    $('#mast-machrate'+id).val(total);

});

$(document).on("change", "input[type=text][name=mastrep]", function () {

    var id = $(this).attr('data-id');

    var equconsumption = $('#mast-fuelqty'+id).val();

    var equrate = $('#mast-fuelrate'+id).val();

    var equmaintenancecost = $('#mast-repair'+id).val();

    if(equconsumption==''){

        equconsumption = 0;

    }

    if(equrate==''){

        equrate = 1;

    }

    if(equmaintenancecost==''){

        equmaintenancecost = 0;

    }

    var total1 = equconsumption * equrate;

    var total = (total1 + parseFloat(equmaintenancecost)).toFixed(2);

    $('#mast-machrate'+id).val(total);

});




$(document).on("change", "select[name=mastful]", function () {

    var id = $(this).data('id');
    var val = $(this).val();

    $h1 = '';
    $h2 = '';
    if(val == 'Power')
    {
        $h1 = 'Power (KWh)';
        $h2 = 'Power Rate';
    }
    else{
        $h1 = 'Fuel (Ltr/Hr)';
        $h2 = 'Fuel Rate';
    }
    $('#rateh'+id).html($h2);
    $('#fueh'+id).html($h1);
    changeFuelVendor(id);
});

function changeFuelVendor(id){

    val = $('#fueltype'+id+' option:selected').val();
    //id = $('select[name=fuelstyp]').data('id');
    console.log(val);
    console.log(id);
    $('#fuel_vendor'+id).val('');
    if(val == 'Petrol'){
        $('.dieselVendor').hide();
        $('.petrolVendor').show();
    }
    else if(val == 'Diesel'){
        $('.petrolVendor').hide();
        $('.dieselVendor').show();
    }
    else{
        $('.petrolVendor').hide();
        $('.dieselVendor').hide();
    }
}

$(document).on("change", "select[type=text][name=fuelleastyp]", function () {

    var id = $(this).data('id');
    var val = $(this).val();

    $h1 = '';
    $h2 = '';

    if(val == 'Power')
    {
        $h1 = 'Power (KWh)';
        $h2 = 'Power Rate';
    }else{
        $h1 = 'Fuel (Ltr/Hr)';
        $h2 = 'Fuel Rate';

    }

    $('#lrateh'+id).html($h2);
    $('#lfueh'+id).html($h1);


});

$(document).on("change", "select[type=text][name=mastful]", function () {

    var id = $(this).data('id');
    var val = $(this).val();

    $h1 = '';
    $h2 = '';

    if(val == 'Power')
    {
        $h1 = 'Power (KWh)';
        $h2 = 'Power Rate';
    }else{
        $h1 = 'Fuel (Ltr/Hr)';
        $h2 = 'Fuel Rate';

    }

    $('#mastfrate'+id).html($h2);
    $('#mastfqt'+id).html($h1);


});

$(document).on("change", "select[type=text][name=mastlfue]", function () {

    var id = $(this).data('id');
    var val = $(this).val();

    $h1 = '';
    $h2 = '';

    if(val == 'Power')
    {
        $h1 = 'Power (KWh)';
        $h2 = 'Power Rate';
    }else{
        $h1 = 'Fuel (Ltr/Hr)';
        $h2 = 'Fuel Rate';

    }

    $('#mastlfrate'+id).html($h2);
    $('#mastlfqt'+id).html($h1);


});