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
            '<div class="col-md-3"><input type="text" class="form-control task-name" name="task_name[]" placeholder="Task Name"></div>' +
            '<div class="col-md-2"><input type="text" class="form-control task-unit" name="task_unit[]" placeholder="Unit" oninput="this.value=this.value.replace(/[0-9]/g,\'\')"></div>' +
            '<div class="col-md-2"><input type="number" step="0.001" min="0" class="form-control task-qty" name="task_qty[]" placeholder="Qty/Unit"></div>' +
            '<div class="col-md-3"><input type="number" step="0.001" min="0" class="form-control task-productivity" name="task_productivity[]" placeholder="Productivity/day"></div>' +
            '<div class="col-md-1"><button type="button" class="remove-task-row" style="background:none;border:none;padding:6px 4px;"><span class="icon-minus" style="color:#d9534f;font-size:18px;"></span></button></div>' +
            '</div>';
        $('#task-rows-container').append(row);
    });

    $(document).on('click', '.remove-task-row', function(){
        $(this).closest('.task-row').remove();
    });

    $(document).on('click', '#saveestactivity', function(){
        var error=0;
        // Clear every field-level error message up front, INCLUDING the
        // ones with no .error class (Project Type/IOW Group/Activity Type/
        // Name/Unit all use a plain `<span style="color:red;">`, not
        // class="error") — .error alone never matched them, so a message
        // shown on one failed attempt was never cleared on a later
        // successful one; it just sat there permanently once triggered.
        $('.error').hide();
        $('#estworktypelistss1, #actIowGroupId, #estactivitytypeid, .estactivityname, .estactivityunit').each(function(){
            $(this).next('span').hide().html('');
        });

        // All fields on this form are required — Project Type, IOW Group
        // and Activity Type were previously optional (see removed comments
        // below), but per explicit direction every field must be filled
        // before an activity can be added, so those three are validated
        // the same way Activity Name/Unit already were.
        if(!$('#estworktypelistss1').val() || $('#estworktypelistss1').val() === '0'){
            $('#estworktypelistss1').next('span').html('Select Project Type').show('slow');
            error = 1;
        }
        if(!$('#actIowGroupId').val()){
            $('#actIowGroupId').next('span').html('Select IOW Group').show('slow');
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

        // Every task row currently on the form must be fully filled in
        // (Task Name, Unit, Qty/Unit, Productivity/day) — a half-filled
        // row (e.g. productivity left blank) silently saved as 0 before,
        // which is exactly the "productivity not saving" confusion from
        // earlier; now it's caught here instead.
        $('#task-rows-container .task-row').each(function(){
            var $row = $(this);
            var $name = $row.find('.task-name');
            var $unit = $row.find('.task-unit');
            var $qty = $row.find('.task-qty');
            var $prod = $row.find('.task-productivity');
            if($name.val() === '' || $unit.val() === '' || $qty.val() === '' || $prod.val() === ''){
                $row.find('.task-name, .task-unit, .task-qty, .task-productivity').css('border-color', '#d9534f');
                error = 1;
            } else {
                $row.find('.task-name, .task-unit, .task-qty, .task-productivity').css('border-color', '');
            }
        });
        if(error && $('#task-rows-container .task-row').length && !$('#task-rows-error').length){
            $('#task-rows-container').after('<div id="task-rows-error" style="color:red;font-size:12px;margin-top:4px;">Please fill in Task Name, Unit, Qty/Unit and Productivity/day for every task row (or remove the empty row).</div>');
        }
        if(!error){ $('#task-rows-error').remove(); }

        if(error==0){
            var editingId = $('#editingActivityId').val();
            var url = editingId ? '../projects/updateactivitywithtasks' : '../projects/createactivity';

            var taskIds = [], taskNames = [], taskUnits = [], taskQtys = [], taskProds = [];
            $('#task-rows-container .task-row').each(function(){
                taskIds.push($(this).find('.task-id').val() || 0);
                taskNames.push($(this).find('.task-name').val());
                taskUnits.push($(this).find('.task-unit').val());
                taskQtys.push($(this).find('.task-qty').val());
                taskProds.push($(this).find('.task-productivity').val());
            });
            var postData = {
                activity_id:          $('#editingActivityId').val(),
                activitytypeid:       $('#estactivitytypeid').val(),
                worktypeid:           $('#estworktypelistss1').val() || 0,
                iow_group_id:         $('#actIowGroupId').val() || 0,
                working_hours:        $('#est_working_hours').val(),
                'estactivityname[]':  [$('#estactivityname1').val()],
                'estactivityunit[]':  [$('#estactivityunit1').val()],
                'task_id[]':          taskIds,
                'task_name[]':        taskNames,
                'task_unit[]':        taskUnits,
                'task_qty[]':         taskQtys,
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
                        $('#saveestactivity').attr("disabled", false);
                        $(".search-and-actions-wrpr").show();
                        $('#listestactivity').trigger('click');
                        $('.panel-default').removeClass('add-form-active');

                        if(editingId){
                            // Editing an existing activity is a one-off
                            // action on a specific record — close as before.
                            closeAlAddActivityModal();
                        } else {
                            // Adding a brand-new activity: stay open so the
                            // user can keep adding more under the same
                            // Project Type / IOW Group without reselecting
                            // them each time. Only Activity Name, Unit and
                            // Tasks are cleared; Project Type, IOW Group,
                            // Activity Type and Working Hours are left as-is.
                            $('#estactivityname1, #estactivityunit1').val('');
                            $('#task-rows-container .task-row:not(#task-row-1)').remove();
                            $('#task-rows-container .task-row:first .task-name, #task-rows-container .task-row:first .task-unit, #task-rows-container .task-row:first .task-qty, #task-rows-container .task-row:first .task-productivity').val('');
                            taskRowCount = 1;
                            $('.error').hide();
                            if(!$('#al-add-activity-saved-msg').length){
                                $('#saveestactivity').after('<span id="al-add-activity-saved-msg" style="color:#197a3a;font-weight:700;margin-left:10px;">Added.</span>');
                            } else {
                                $('#al-add-activity-saved-msg').show();
                            }
                            setTimeout(function(){ $('#al-add-activity-saved-msg').fadeOut(400); }, 2000);
                            $('#estactivityname1').focus();
                        }
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
                else{
                    alert(data.errortext || "Activity already assigned to a project");
                }
                $('#deleteestactivitybutton'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
$(document).on('click', '.al-edit-activity-btn', function(){
    var idval = $(this).data('actid');
    $.ajax({
        type: 'POST',
        url: '../projects/getactivityforedit',
        dataType: 'json',
        data: { activity_id: idval },
        error: function(xhr){ alert('Could not load activity (' + xhr.status + ')'); },
        success: function(data){
            if(data.error !== 'No') return;
            var act = data.activity;
            $('#estactivityform .error').hide();
            /* show modal first, then prefill after Bootstrap resets have run */
            window._alEditMode = true;
            $('#alAddActivityPopup').css('display','block').addClass('in');
            $('body').addClass('modal-open');
            if(!$('.modal-backdrop').length) $('<div class="modal-backdrop fade in"></div>').appendTo('body');
            setTimeout(function(){
                $('#estworktypelistss1').val(act.work_type || 0);
                $('#estactivitytypeid').val(act.activity_type);
                $('#estactivityname1').val(act.activity_name);
                $('#estactivityunit1').val(act.activity_unit);
                $('#est_working_hours').val(act.working_hours);
                $('#editingActivityId').val(idval);
                $('#saveestactivity').html('<span class="icon-check"></span> Update Activity');
                $('#alAddActivityTitle').text('Edit Activity');
                /* load IOW group options then select the saved value */
                if(typeof alRefreshActIowGroupDropdown === 'function'){
                    alRefreshActIowGroupDropdown(act.iow_group_id || '');
                }

                /* rebuild task rows */
                $('#task-rows-container').empty();
                taskRowCount = 0;
                if(data.tasks && data.tasks.length > 0){
                    $.each(data.tasks, function(i, task){
                        taskRowCount++;
                        var btnHtml = taskRowCount === 1
                            ? '<button type="button" class="add-task-row" style="background:none;border:none;padding:6px 4px;"><span class="icon-add" style="color:#337ab7;font-size:18px;"></span></button>'
                            : '<button type="button" class="remove-task-row" style="background:none;border:none;padding:6px 4px;"><span class="icon-minus" style="color:#d9534f;font-size:18px;"></span></button>';
                        $('#task-rows-container').append(
                            '<div class="row task-row" id="task-row-'+taskRowCount+'" style="margin-top:4px;">' +
                            '<input type="hidden" class="task-id" name="task_id[]" value="'+(task.id||0)+'">' +
                            '<div class="col-md-1"></div>' +
                            '<div class="col-md-3"><input type="text" class="form-control task-name" name="task_name[]" placeholder="Task Name" value="'+$('<div/>').text(task.task_name).html()+'"></div>' +
                            '<div class="col-md-2"><input type="text" class="form-control task-unit" name="task_unit[]" placeholder="Unit" value="'+$('<div/>').text(task.task_unit||'').html()+'" oninput="this.value=this.value.replace(/[0-9]/g,\'\')"></div>' +
                            '<div class="col-md-2"><input type="number" step="0.001" min="0" class="form-control task-qty" name="task_qty[]" placeholder="Qty/Unit" value="'+parseFloat(task.task_qty||0).toFixed(3)+'"></div>' +
                            '<div class="col-md-3"><input type="number" step="0.001" min="0" class="form-control task-productivity" name="task_productivity[]" placeholder="Productivity/day" value="'+parseFloat(task.productivity||0).toFixed(3)+'"></div>' +
                            '<div class="col-md-1">'+btnHtml+'</div></div>'
                        );
                    });
                } else {
                    taskRowCount = 1;
                    $('#task-rows-container').append(
                        '<div class="row task-row" id="task-row-1" style="margin-top:4px;">' +
                        '<div class="col-md-1"></div>' +
                        '<div class="col-md-3"><input type="text" class="form-control task-name" name="task_name[]" placeholder="Task Name"></div>' +
                        '<div class="col-md-2"><input type="text" class="form-control task-unit" name="task_unit[]" placeholder="Unit" oninput="this.value=this.value.replace(/[0-9]/g,\'\')"></div>' +
                        '<div class="col-md-2"><input type="number" step="0.001" min="0" class="form-control task-qty" name="task_qty[]" placeholder="Qty/Unit"></div>' +
                        '<div class="col-md-3"><input type="number" step="0.001" min="0" class="form-control task-productivity" name="task_productivity[]" placeholder="Productivity/day"></div>' +
                        '<div class="col-md-1"><button type="button" class="add-task-row" style="background:none;border:none;padding:6px 4px;"><span class="icon-add" style="color:#337ab7;font-size:18px;"></span></button></div></div>'
                    );
                }
            }, 50);
        }
    });
});

/* close manually-opened modal — shared by the dismiss/cancel buttons and
   by the Update Activity save-success handler above (editingId branch),
   which previously toggled the OLD inline-panel's .add-activity-form/
   #estactivityitems elements instead of actually closing this Bootstrap
   modal — the modal stayed open showing its pre-save (now stale) input
   values, making a successful save look like it hadn't taken effect. */
function closeAlAddActivityModal(){
    $('#alAddActivityPopup').css('display','none').removeClass('in').attr('aria-hidden','true');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();

    // Reset the whole Add Activity form on close, including the context
    // fields (Project Type / IOW Group / Activity Type / Working Hours)
    // that are deliberately kept filled between saves while the popup
    // stays open — they must not carry over the next time it's opened.
    $('#estactivityform')[0].reset();
    $('#task-rows-container .task-row:not(#task-row-1)').remove();
    $('#task-rows-container .task-row:first .task-name, #task-rows-container .task-row:first .task-unit, #task-rows-container .task-row:first .task-qty, #task-rows-container .task-row:first .task-productivity').val('');
    taskRowCount = 1;
    $('#actIowGroupId').html('<option value="">Select IOW Group</option>');
    $('#editingActivityId').val('');
    $('#estactivitytypeid').val('0');
    $('#estworktypelistss1').val('0');
    $('#saveestactivity').html('<span class="icon-check"></span> Add Activity');
    $('.error').hide();
}
$(document).on('click', '[data-dismiss="modal"], .cancelactivity', closeAlAddActivityModal);

$(document).on( "click", "#addestactivity", function(){
    $(".estactivityname").val('');
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

/* alEditActivity is defined in _estactivity.php inline script */

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
                     $('.allocation-list-items-master').html(data.listadded);
                     $('.activityrate span').html(data.activitynameval);
                     $('#activityrate').html(data.activitynameval);
                     $('#activityratenow').html(data.activitynamerate);
                     $('#activityunt').html(data.actvtyunit);
                 }
                 $('.preloader').hide();
             }
         });
});

$(document).on( "click", ".resourcesearch", function(){
    var idval = $('#EstActivity_Id').val();
    var restypeid = $(this).data("id");
    $('.resourcesearch').removeClass('active');
    $(this).addClass('active');
        $.ajax({
             type: 'POST',
             url: '../projects/allocationsearch',
             beforeSend : function(){
                $('.preloader').show();
             },
             dataType: "json",
             data: {activity_id:idval, restypeid:restypeid, resgroup:$('#resourcegroup1').val()},
             success: function(data){
                 $('.preloader').hide();
                 $('#accordionmast').html(data.result);
             }
         });
});

$(document).on( "click", ".searchresoureskey", function(){
    var resgroup = $('#resourcegroup1').val();
    var activity_id = $('#EstActivity_Id').val();
});

$(document).on( "click", ".mplant_no_of_hrs", function(){
    var actid = $('#EstActivity_Id').val();
});

$(document).on("click", ".manpower-add-btn", function(){
    var resid = $(this).data('resid');
    var restype = $(this).data('restype');
    var resourcerate = $('#manpowerrate'+resid).val();
    var mannumber = $('#mannumber'+resid).val();
    var mandays = $('#mandays'+resid).val();
    var man_qty = $('#man_qty'+resid).val();
    $.ajax({
                type:'POST',
                url:'../projects/addactivityresource',
                beforeSend: function(){
                    $('.preloader').show();
                },
                dataType:'json',
                data:{EstActivity_Id:$('#EstActivity_Id').val(),resid:resid, resourcerate:resourcerate, mannumber:mannumber, mandays:mandays, man_qty:man_qty, restype:restype},
                success: function(data){
                    $('.preloader').hide();
                    $('.added-activity-list-wrpr').html(data.result);
                    $('#activityratenow').html('Rate : '+data.price);
                    $('.activityrate span').html(data.activityhead);
                    $('#activityrate').html(data.activityhead);
                    $('.added-item-allocation-save-btn').trigger('click');
                    //$('.close-resource-list-btn').trigger('click');
                }
    });
});

$(document).on("click", ".mplant-add-btn", function(){
    var resid = $(this).data('resid');
    var restype = $(this).data('restype');
    var type = $(this).data('type');
    if(type == 'mplant_no_of_hrs'){
        var hoursno = $('#hoursno'+resid).val();
        var fueltype = $('#fueltype'+resid).val();
        var fuel_vendor = $('#fuel_vendor'+resid).val();
        var fuelrate = $('#fuelrate'+resid).val();
        var fuelqty = $('#fuelqty'+resid).val();
        var repair = $('#repair'+resid).val();
        $.ajax({
                    type:'POST',
                    url:'../projects/addactivityresource',
                    beforeSend: function(){
                        $('.preloader').show();
                    },
                    dataType:'json',
                    data:{  EstActivity_Id:$('#EstActivity_Id').val(),
                            resid:resid,fueltype:fueltype,fuel_vendor:fuel_vendor,fuelrate:fuelrate,fuelqty:fuelqty,
                            hoursno:hoursno,repair:repair,restype:restype,type:type},
                    success: function(data){
                        $('.preloader').hide();
                        $('.added-activity-list-wrpr').html(data.result);
                        $('#activityratenow').html('Rate : '+data.price);
                        $('.activityrate span').html(data.activityhead);
                        $('#activityrate').html(data.activityhead);
                        $('.added-item-allocation-save-btn').trigger('click');
                        //$('.close-resource-list-btn').trigger('click');
                    }
        });
    }else{
        var res_qty = $('#res_qty'+resid).val();
        var resourcerate = $('#mplantrate'+resid).val();
        $.ajax({
                type:'POST',
                url:'../projects/addactivityresource',
                beforeSend: function(){
                    $('.preloader').show();
                },
                dataType:'json',
                data:{EstActivity_Id:$('#EstActivity_Id').val(), resid:resid, res_qty:res_qty, resourcerate:resourcerate, restype:restype, type:type},
                success: function(data){
                    $('.preloader').hide();
                    $('.added-activity-list-wrpr').html(data.result);
                    $('#activityratenow').html('Rate : '+data.price);
                    $('.activityrate span').html(data.activityhead);
                    $('#activityrate').html(data.activityhead);
                    $('.added-item-allocation-save-btn').trigger('click');
                    //$('.close-resource-list-btn').trigger('click');
                }
        });
    }
});

$(document).on("click", ".mlease-add-btn", function(){
    var resid = $(this).data('resid');
    var restype = $(this).data('restype');
    var type = $(this).data('type');
    if(type == 'mlno_of_hrs'){
        var hoursno = $('#lhoursno'+resid).val();
        var fueltype = $('#lfueltype'+resid).val();
        var fuelrate = $('#lfuelrate'+resid).val();
        var fuelqty = $('#lfuelqty'+resid).val();
        $.ajax({
                    type:'POST',
                    url:'../projects/addactivityresource',
                    beforeSend: function(){
                        $('.preloader').show();
                    },
                    dataType:'json',
                    data:{EstActivity_Id:$('#EstActivity_Id').val(),resid:resid,fueltype:fueltype,fuelrate:fuelrate,fuelqty:fuelqty,hoursno:hoursno,restype:restype,type:type},
                    success: function(data){
                        $('.preloader').hide();
                        $('.added-activity-list-wrpr').html(data.result);
                        $('#activityratenow').html('Rate : '+data.price);
                        $('.activityrate span').html(data.activityhead);
                        $('#activityrate').html(data.activityhead);
                        $('.added-item-allocation-save-btn').trigger('click');
                        //$('.close-resource-list-btn').trigger('click');
                    }
        });
    }else{
        var leaseperiod = $('#leaseperiod'+resid).val();
        var resourcerate = $('#mleaserate'+resid).val();
        $.ajax({
                type:'POST',
                url:'../projects/addactivityresource',
                beforeSend: function(){
                    $('.preloader').show();
                },
                dataType:'json',
                data:{EstActivity_Id:$('#EstActivity_Id').val(),resid:resid,leaseperiod:leaseperiod,resourcerate:resourcerate,restype:restype,type:type},
                success: function(data){
                    $('.preloader').hide();
                    $('.added-activity-list-wrpr').html(data.result);
                    $('#activityratenow').html('Rate : '+data.price);
                    $('.activityrate span').html(data.activityhead);
                    $('#activityrate').html(data.activityhead);
                    $('.added-item-allocation-save-btn').trigger('click');
                    //$('.close-resource-list-btn').trigger('click');
                }
        });
    }
});

$(document).on("click", ".resource-add-btn", function(){
    var resid = $(this).data('resid');
    var restype = $(this).data('restype');
    var type = $(this).data('type');
    var quantity = $('#quantity'+resid).val();
    var price = $('#srate'+resid).val();
    var sprice = $('#srate'+resid).val();
    $.ajax({
            type:'POST',
            url:'../projects/addactivityresource',
            beforeSend: function(){
                $('.preloader').show();
            },
            dataType:'json',
            data:{EstActivity_Id:$('#EstActivity_Id').val(),resid:resid,rate:price,srate:sprice,quantity:quantity,restype:restype,type:type},
            success: function(data){
                $('.preloader').hide();
                $('.added-activity-list-wrpr').html(data.result);
                $('#activityratenow').html('Rate : '+data.price);
                $('.activityrate span').html(data.activityhead);
                $('#activityrate').html(data.activityhead);
                $('.added-item-allocation-save-btn').trigger('click');
            }
    });
});

$(document).on( "click", ".removeresourceitem", function(){
    var resid = $(this).data("id");
    var r = confirm("Are you sure you want to remove this resource ?");
    if(r==true){
    $.ajax({
        type: 'POST',
        url:'../projects/deleteactivityresource',
        beforeSend : function(){
            $('#removeresourceitem'+resid).attr("disabled",true);
        },
        dataType: "json",
        data:{resid:resid,activityid:$('#EstActivity_Id').val()},
        success: function(data){
            $('#tempresrow'+resid).remove();
            $('#activityratenow').html('Rate : '+data.price);
            $('#removeresourceitem'+resid).attr("disabled",false);
            $('.activityrate span').html(data.activityhead);
            $('#activityrate').html(data.activityhead);
            //$('#resourceallocation').trigger('click');
            $('.close-resource-list-btn').trigger('click');
        }
    });
    }
});

$(document).on( "click", ".updatesallocateresourceunit", function(){
    var mid = $(this).attr('data-v');
    $("#updatesallocateresourceunit"+mid).hide();
    $("#savesallocateresourceunit"+mid).show();
    $("#editallocateresourceunit"+mid).show();
    $("#editallocateresourceqty"+mid).show();
    $("#savesallocateresourceunit"+mid).show();
});

$(document).on( "click", ".savesallocateresourceunit", function(){
    var mid = $(this).attr('data-v');
    var activityid = $(this).attr('data-activity');
    //var updatevalue = $("#editallocateresourceunit"+mid).val();
    var str = $("#editallocateresourceunit"+mid).val();
    var updatevalue = str.replace(/,/g, '');
    var updateqty = $("#editallocateresourceqty"+mid).val();
    $.ajax({
            type: 'POST',
            url: '../projects/updateallocaterrated',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data:{PRID:mid,updatevalue:updatevalue,updateqty:updateqty},
            success: function(data){
                	$("#editallocateresourceunit"+mid).hide();
                    $("#editallocateresourceqty"+mid).hide();
                    $("#savesallocateresourceunit"+mid).hide();
                    $("#updatesallocateresourceunit"+mid).show();
                    $("#allocateresourceqty"+mid).html(updateqty);
                    $("#allocateresourceunit"+mid).html(updatevalue);
                    $("#allocateres_amt"+mid).html(data.restotal);
                    $("#activityratenow").html('Rate: '+data.total);
                $('.preloader').hide();
            }
    });
});

/*$(document).on( "input", "#resourcegroup1", function(e){
    if(e.which===13){
        var resgroup = $('#resourcegroup1').val();
        var activity_id = $('#EstActivity_Id').val();
        $.ajax({
            type: 'POST',
            url: '../projects/allocationsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data:{resgroup:resgroup,activity_id:activity_id},
            success: function(data){
                $('.preloader').hide();
                $('#accordionmast').html(data.result);
            }
        });
    }
});*/

$(document).on( "keyup", "#resourcegroup1", function(e){
    var resgroup = $('#resourcegroup1').val();
    var activity_id = $('#EstActivity_Id').val();
    var restypeid = $('.resourcesearch.active').data('id') || '';
    $.ajax({
        type: 'POST',
        url: '../projects/allocationsearch',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data:{resgroup:resgroup,activity_id:activity_id,restypeid:restypeid},
        success: function(data){
            $('.preloader').hide();
            $('#accordionmast').html(data.result);
        }
    });
});

$(document).on( "click", ".searchresoureskey", function(){
    $('#resourcegroup1').trigger('keyup');
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
