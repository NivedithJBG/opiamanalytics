
$(document).on( "click", ".estimateallocation", function(){

 $('.close-resource-list-btn1').trigger('click');
 $("#Estimate-allocate-body-one-head").hide();
 $("#Estimate-allocate-body-one").hide();
 $("#Estimate-allocate-body-two").show();

 var Process = $(this).data("process");
 var activityid = $(this).data("activity");
 var project_estimate_Id= $(this).data("proestimate");
 var Project_Id= $(this).data("project");

 $("#project_id").val(Project_Id);
 $('#EstActivity_Id').val(project_estimate_Id);
 $("#proces_id").val(Process);
 $("#activity_id").val(activityid);
        $.ajax({
            type: 'POST',
            url: '../estimateprojectmain/activityresources',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {Process:Process,activityid:activityid,project_estimate_Id:project_estimate_Id,Project_Id:Project_Id},
            success: function(data){
                if(data.error=='No')
                {

                	if(data.sno==0)
                	{
                		$(".estimationaddedallocation").hide();
                	}
                	else
                	{
                		$(".estimationaddedallocation").show();
                	}

                	$('#activitycost').html(data.acivitycost);
                    $('#activitycostnow').html(data.acivitycostnew);
                    $('#activityunitnow').html(data.activityunitnew);
                	$('.estimationaddedallocation').html(data.result);
                	$('.estimation-left-bar').html(data.datarows);
                	$('#activityunit').val(data.activityunit);
                	$('#activity_name').val(data.activityname);
                }

                $('.preloader').hide();
            }
        });

});

$(document).on( "click", ".updateallocateresourceunit", function(){
    var mid = $(this).attr('data-v');

    $.ajax({
          type: 'POST',
          url: '../projectsmain/receivepurchaseordercheck',
          dataType: "json",
          data: {estres:mid},
          success: function(data){
              if(data.receivedres!=0 && data.type!=1)
              {
                alert('Resource already received')
              }
              else{
                $("#updateallocateresourceunit"+mid).hide();
                $("#Estimateiowactivityunit"+mid).hide();
                $("#Estimateiowactivityqty"+mid).hide();
                $("#editEstimateiowactivityunit"+mid).show();
                $("#editEstimateiowactivityqty"+mid).show();
                $("#saveallocateresourceunit"+mid).show();
              }
          }
    });

    /*$("#updateallocateresourceunit"+mid).hide();
    $("#Estimateiowactivityunit"+mid).hide();
    $("#Estimateiowactivityqty"+mid).hide();
    $("#editEstimateiowactivityunit"+mid).show();
    $("#editEstimateiowactivityqty"+mid).show();
    $("#saveallocateresourceunit"+mid).show(); */ 
});

$(document).on( "blur",".editallocateresourceqty", function(){
    var itemid=$(this).attr('data-v');
    var error=0;
    $('.error').hide();
    if($('#editEstimateiowactivityqty'+itemid).val()==0)
    {
        $('#editEstimateiowactivityqty'+itemid).next("span").html('Enter Valid Quantity').show('slow');
        error=1;
    }
    var quantity=$(this).val()*1;
    var rate=$('#editEstimateiowactivityunit'+itemid).val()*1;
    var amount=rate*quantity;
    $('#editallocateresourceamount'+itemid).text(amount.toFixed(2));
    var totalrate=0;
    $('.resource-amount').each(function(){
        totalrate=totalrate+($(this).text()*1)
    });
    $('#productratetotal').html(totalrate.toFixed(2));
});

$(document).on( "blur",".editallocateresourceunit", function(){
    var itemid=$(this).attr('data-v');
    var error=0;
    $('.error').hide();
    if($('#editEstimateiowactivityqty'+itemid).val()==0)
    {
        $('#editEstimateiowactivityqty'+itemid).next("span").html('Enter Valid Quantity').show('slow');
        error=1;
    }
    var quantity=$('#editEstimateiowactivityqty'+itemid).val()*1;
    //var rate=$('#editEstimateiowactivityunit'+itemid).val();
    var str = $('#editEstimateiowactivityunit'+itemid).val();
    var rate = str.replace(/,/g, '');


    var amount=rate*quantity;
    $('#editallocateresourceamount'+itemid).html(amount.toFixed(2));
    var totalrate=0;
    $('.resource-amount').each(function(){
        totalrate=totalrate+($(this).text()*1)
    });
    $('#productratetotal').html(totalrate.toFixed(2));
});

$(document).on( "click", ".saveallocateresourceunit", function(){
    var error=0;
    $('.error').hide();
    var mid = $(this).attr('data-v');
    var dactivity = $(this).attr('data-activity');
   // var updatevalue = $("#editEstimateiowactivityunit"+mid).val();

    var str = $("#editEstimateiowactivityunit"+mid).val();
    var updatevalue = str.replace(/,/g, '');


    var qty = $("#editEstimateiowactivityqty"+mid).val();

    if($("#editEstimateiowactivityunit"+mid).val()== ''){
        $('#editEstimateiowactivityunit'+mid).next("span").html('Enter Rate').show('slow');

        error=1;
    }
    if($("#editEstimateiowactivityqty"+mid).val()==''){
        $('#editEstimateiowactivityqty'+mid).next("span").html('Enter Quantity').show('slow');

        error=1;
    }
    if(updatevalue != '' && error==0){
        $.ajax({
            type: 'POST',
            url: '../estimateprojectmain/updateestimatererate',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {PRID:mid,dactivity:dactivity,updatevalue:updatevalue,qty:qty},
            success: function(data){
                if(data.error=='No')
                {
                	$("#editEstimateiowactivityunit"+mid).hide();
                    $("#editEstimateiowactivityqty"+mid).hide();
                    $("#saveallocateresourceunit"+mid).hide();
                    $("#updateallocateresourceunit"+mid).show();
                    $("#Estimateiowactivityunit"+mid).html(updatevalue);
                    $("#Estimateiowactivityunit"+mid).show();
                    $("#Estimateiowactivityqty"+mid).html(qty);
                    $("#Estimateiowactivityqty"+mid).show();
                    $('#editallocateresourceamount'+mid).html(data.amounttot)
                }else if(data.error=='Yes')
                {
                    alert("Can not reduce quantity as it is Received")
                }

                $('.preloader').hide();
            }
        });

    }else{
        return true;
    }
});


$(document).on( "click", ".estimate-addresource", function(){  

    var Product_Name = $('#activity_name').val();
    var Product_Unit = $("#activityunit").val();
    var Project_Id = $("#project_id").val();
    var project_estimate_Id = $("#EstActivity_Id").val();
    var activityid = $("#activity_id").val();
    var Process = $("#proces_id").val();
    var error=0;
    var resourceid = $(this).val();
    
    
    var resourcetypeid = $('#resourcegroupval').val();
    $('.error').hide();

    if(resourcetypeid==26){ 

        var type = document.querySelector('input[name="pequ-select'+resourceid+'"]:checked').value;



        if (type == 'plant_no_of_hrs') {
            var batchingunit = $('#estimate-batchingunit'+resourceid).val();
            var fueltype = $('#fueltype'+resourceid).val();
            var fuel_vendor = $('#fuel_vendor'+resourceid).val();
            var fuelrate = $('#estimate-fuelrate'+resourceid).val();
            var fuelqty = $('#estimate-fuelqty'+resourceid).val();
            var hoursno = $('#estimate-resourcetime'+resourceid).val();
            var repair = $('#estimate-repair'+resourceid).val();
            var leaseperiod = $('#estimate-leaseperiod'+resourceid).val();
            var task_ids = $('#task_id'+resourceid).val();
            var unit = $('#fulunt').html();

            if(fuelrate=='' || fuelqty=='' || hoursno=='' || fuel_vendor=='' || task_ids==''){
                error=1;
                alert('Enter the fields')
            }

            if(error == 0){
                $.ajax({
                    type: 'POST',
                    url: '../estimateprojectmain/activityresources',
                    beforeSend : function(){
                        $('.preloader').show();
                    },
                    dataType: "json",
                    data: {type:type,unit:unit,repair:repair,batchingunit:batchingunit,fueltype:fueltype, fuel_vendor: fuel_vendor, fuelrate:fuelrate,fuelqty:fuelqty,hoursno:hoursno,resourcetypeid:resourcetypeid,Product_Name:Product_Name,Product_Unit:Product_Unit,resourceid:resourceid,Process:Process,activityid:activityid,project_estimate_Id:project_estimate_Id,Project_Id:Project_Id,task_ids:task_ids},
                    success: function(data){
                        if(data.error=='No')
                        {
                            
                            $('#activitycost').html(data.acivitycost);
                            $('#activitycostnow').html(data.acivitycostnew);
                            $(".estimationaddedallocation").show();
                            $('#estimate-resourcequantity'+resourceid).val('');
                            $('#estimate-batchingunit'+resourceid).val('');
                            $('#fueltype'+resourceid).val('');
                            $('#estimate-fuelrate'+resourceid).val('');
                            $('#estimate-fuelqty'+resourceid).val('');
                            $('#estimate-resourcetime'+resourceid).val('');
                            $('#estimate-repair'+resourceid).val('');
                            $('#estimate-acamnt'+resourceid).val('');
                           // $('#activitycosthead span').html(data.acivityhead);
                            $('.estimationaddedallocation').html(data.result);
                            $('.estimation-left-bar').html(data.datarows);
                            $('#activityunit').val(data.activityunit);
                            //$(".view_estimate").trigger('click');
                            $('.close-resource-list-btn1').trigger('click');
                            $('#listestimateitems').trigger('click') ;
                            $('.allocate-resource-tabs').addClass('add-allocation-list-active');
                        }

                        $('.preloader').hide();
                    }
                });
            }    
        }
        else if (type == 'depreciation') {

            var batchingunit = $('#estimate-batchingunit'+resourceid).val();
            var fueltype = $('#fueltype'+resourceid).val();
            var fuelrate = $('#estimate-fuelrate'+resourceid).val();
            var fuelqty = $('#estimate-fuelqty'+resourceid).val();
            var hoursno = $('#estimate-resourcetime'+resourceid).val();
            var repair = $('#estimate-repair'+resourceid).val();
            var leaseperiod = $('#estimate-leaseperiod'+resourceid).val();
            var str = $('#estimate-plantrate'+resourceid).val();
            var resourcerate = str.replace(/,/g, '');
            var dep_qty = $('#estimate-dep-qty'+resourceid).val();
            var task_ids = $('#task_id'+resourceid).val();

            if(fuelrate=='' || fuelqty=='' || hoursno=='' || !dep_qty || task_ids==''){
                error=1;
                alert('Enter the fields')
            }
            if(error == 0){
                $.ajax({
                    type: 'POST',
                    url: '../estimateprojectmain/activityresources',
                    beforeSend : function(){
                        $('.preloader').show();
                    },
                    dataType: "json",
                    data: {type:type,leaseperiod:leaseperiod,resourcerate:resourcerate,repair:repair,batchingunit:batchingunit,fueltype:fueltype,fuelrate:fuelrate,fuelqty:fuelqty,hoursno:hoursno,resourcetypeid:resourcetypeid,Product_Name:Product_Name,Product_Unit:Product_Unit,resourceid:resourceid,Process:Process,activityid:activityid,project_estimate_Id:project_estimate_Id,Project_Id:Project_Id,dep_qty:dep_qty,task_ids:task_ids},
                    success: function(data){
                        if(data.error=='No')
                        {
                            
                            $('#activitycost').html(data.acivitycost);
                            $('#activitycostnow').html(data.acivitycostnew);
                            $(".estimationaddedallocation").show();
                            $('#estimate-resourcequantity'+resourceid).val('');
                            $('#estimate-batchingunit'+resourceid).val('');
                            $('#fueltype'+resourceid).val('');
                            $('#estimate-fuelrate'+resourceid).val('');
                            $('#estimate-fuelqty'+resourceid).val('');
                            $('#estimate-resourcetime'+resourceid).val('');
                            $('#estimate-repair'+resourceid).val('');
                            $('#estimate-acamnt'+resourceid).val('');
                           // $('#activitycosthead span').html(data.acivityhead);
                            $('.estimationaddedallocation').html(data.result);
                            $('.estimation-left-bar').html(data.datarows);
                            $('#activityunit').val(data.activityunit);
                            //$(".view_estimate").trigger('click');
                            $('.close-resource-list-btn1').trigger('click');
                            $('#listestimateitems').trigger('click') ;
                            $('.allocate-resource-tabs').addClass('add-allocation-list-active');
                        }

                        $('.preloader').hide();
                    }
                });
            }


        }
    //}

    }
    else if(resourcetypeid==24){


        var type = document.querySelector('input[name="equ-select'+resourceid+'"]:checked').value;


        if (type == 'no_of_hrs') {

            var batchingunit = $('#estimate-batchingunit'+resourceid).val();

            var fueltype = $('#fueltype'+resourceid).val();

            var fuelrate = $('#estimate-fuelrate'+resourceid).val();

            var fuelqty = $('#estimate-fuelqty'+resourceid).val();
            var fuel_vendor = $('#fuel_vendor'+resourceid).val();

            var hoursno = $('#estimate-resourcetime'+resourceid).val();

            var repair = $('#estimate-repair'+resourceid).val();

            var leaseperiod = $('#estimate-leaseperiod'+resourceid).val();

            if(fuelrate=='' || fuelqty=='' || hoursno=='' || fuel_vendor==''){

                error=1;

                alert('Enter the fields')

            }

            $.ajax({
                type: 'POST',
                url: '../estimateprojectmain/activityresources',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {type:type,repair:repair,batchingunit:batchingunit,fueltype:fueltype, fuel_vendor: fuel_vendor,fuelrate:fuelrate,fuelqty:fuelqty,hoursno:hoursno,resourcetypeid:resourcetypeid,Product_Name:Product_Name,Product_Unit:Product_Unit,resourceid:resourceid,Process:Process,activityid:activityid,project_estimate_Id:project_estimate_Id,Project_Id:Project_Id},
                success: function(data){
                    if(data.error=='No')
                    {
                        
                        $('#activitycost').html(data.acivitycost);
                        $('#activitycostnow').html(data.acivitycostnew);
                        $(".estimationaddedallocation").show();
                        $('#estimate-resourcequantity'+resourceid).val('');
                        $('#estimate-batchingunit'+resourceid).val('');
                        $('#fueltype'+resourceid).val('');
                        $('#estimate-fuelrate'+resourceid).val('');
                        $('#estimate-fuelqty'+resourceid).val('');
                        $('#estimate-resourcetime'+resourceid).val('');
                        $('#estimate-repair'+resourceid).val('');
                        $('#estimate-acamnt'+resourceid).val('');
                       // $('#activitycosthead span').html(data.acivityhead);
                        $('.estimationaddedallocation').html(data.result);
                        $('.estimation-left-bar').html(data.datarows);
                        $('#activityunit').val(data.activityunit);
                        //$(".view_estimate").trigger('click');
                        $('.close-resource-list-btn1').trigger('click');
                        $('#listestimateitems').trigger('click') ;
                        $('.allocate-resource-tabs').addClass('add-allocation-list-active');
                    }

                    $('.preloader').hide();
                }
            });
            
        }
        else if (type == 'lease_period') {

            var batchingunit = $('#estimate-batchingunit'+resourceid).val();

            var fueltype = $('#fueltype'+resourceid).val();

            var fuelrate = $('#estimate-fuelrate'+resourceid).val();

            var fuelqty = $('#estimate-fuelqty'+resourceid).val();

            var hoursno = $('#estimate-resourcetime'+resourceid).val();

            var repair = $('#estimate-repair'+resourceid).val();

            var leaseperiod = $('#estimate-leaseperiod'+resourceid).val();
            var task_ids = $('#task_id'+resourceid).val();

            var str = $('#estimate-specificrate'+resourceid).val();
            var resourcerate = str.replace(/,/g, '');

            if(fuelrate=='' || fuelqty=='' || hoursno=='' || task_ids==''){

                error=1;

                alert('Enter the fields')

            }

            $.ajax({
                type: 'POST',
                url: '../estimateprojectmain/activityresources',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {type:type,leaseperiod:leaseperiod,resourcerate:resourcerate,repair:repair,batchingunit:batchingunit,fueltype:fueltype,fuelrate:fuelrate,fuelqty:fuelqty,hoursno:hoursno,resourcetypeid:resourcetypeid,Product_Name:Product_Name,Product_Unit:Product_Unit,resourceid:resourceid,Process:Process,activityid:activityid,project_estimate_Id:project_estimate_Id,Project_Id:Project_Id,task_ids:task_ids},
                success: function(data){
                    if(data.error=='No')
                    {
                        
                        $('#activitycost').html(data.acivitycost);
                        $('#activitycostnow').html(data.acivitycostnew);
                        $(".estimationaddedallocation").show();
                        $('#estimate-resourcequantity'+resourceid).val('');
                        $('#estimate-batchingunit'+resourceid).val('');
                        $('#fueltype'+resourceid).val('');
                        $('#estimate-fuelrate'+resourceid).val('');
                        $('#estimate-fuelqty'+resourceid).val('');
                        $('#estimate-resourcetime'+resourceid).val('');
                        $('#estimate-repair'+resourceid).val('');
                        $('#estimate-acamnt'+resourceid).val('');
                       // $('#activitycosthead span').html(data.acivityhead);
                        $('.estimationaddedallocation').html(data.result);
                        $('.estimation-left-bar').html(data.datarows);
                        $('#activityunit').val(data.activityunit);
                        //$(".view_estimate").trigger('click');
                        $('.close-resource-list-btn1').trigger('click');
                        $('#listestimateitems').trigger('click') ;
                        $('.allocate-resource-tabs').addClass('add-allocation-list-active');
                    }

                    $('.preloader').hide();
                }
            });



        }








        
    }
    else if(resourcetypeid==33)
    { 
        var id = $(this).val();
        var act_qty = $(this).data('qty'); 
        if(act_qty == 0 || act_qty == null)
        {
            alert("Please enter activity quantity.");
            $('.btnclass'+id).prop('disabled',true);
        }else{

            var man_qty = $('#estimate-qty'+resourceid).val();
            var mannumber = $('#estimate-mannumbr'+resourceid).val();
            var nodays = $('#estimate-no-ofdays'+resourceid).val();
            var mandayss = $('#estimate-no-ofmdays'+resourceid).val();
            var str = $('#estimate-specificrate'+resourceid).val();
            var task_ids = $('#task_id'+resourceid).val();
            var resourcerate = str.replace(/,/g, '');
            var mandays = parseFloat(mandayss/act_qty).toFixed(2);
            if(str=='' || mandayss=='' || man_qty == '' || task_ids == '')
            {
                error=1;
                alert('Enter the fields')
            }
            if(error==0)
            {
                $.ajax({
                    type: 'POST',
                    url: '../estimateprojectmain/activityresources',
                    beforeSend : function(){
                        $('.preloader').show();
                    },
                    dataType: "json",
                    data: {nodays:nodays,mannumber:mannumber, mandays:mandays, man_qty:man_qty, act_qty:act_qty, resourcetypeid:resourcetypeid, Product_Name:Product_Name, Product_Unit:Product_Unit, resourceid:resourceid, resourcerate:resourcerate, resourceqty:resourceqty, Process:Process, activityid:activityid, project_estimate_Id:project_estimate_Id, Project_Id:Project_Id,task_ids:task_ids},
                    success: function(data){
                        if(data.error=='No')
                        {
                            
                            $('#activitycost').html(data.acivitycost);
                            $('#activitycostnow').html(data.acivitycostnew);
                            $(".estimationaddedallocation").show();
                            $('#estimate-resourcequantity'+resourceid).val('');
                            $('#estimate-mannumbr'+resourceid).val('');
                            $('#estimate-no-ofdays'+resourceid).val('');
                            $('#estimate-no-ofmdays'+resourceid).val('');
                           // $('#activitycosthead span').html(data.acivityhead);
                            $('.estimationaddedallocation').html(data.result);
                            $('.estimation-left-bar').html(data.datarows);
                            $('#activityunit').val(data.activityunit);
                            //$(".view_estimate").trigger('click');
                            $('.close-resource-list-btn1').trigger('click');
                            $('#listestimateitems').trigger('click') ;
                            $('.allocate-resource-tabs').addClass('add-allocation-list-active');
                        }

                        $('.preloader').hide();
                    }
                });
            }
        }

    }
    else{

        var resourceqty = $('#estimate-resourcequantity'+resourceid).val();
        var str = $('#estimate-specificrate'+resourceid).val();
        var resourcerate = str.replace(/,/g, '');
        /*var lead_time = $('#lead_time'+resourceid).val();
        var credit_period = $('#credit_period'+resourceid).val();*/

        if(!$.isNumeric($('#estimate-resourcequantity'+resourceid).val()))
        {
            $('#estimate-resourcequantity'+resourceid).next("span").html('Enter Valid Quantity').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../estimateprojectmain/activityresources',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {resourcetypeid:resourcetypeid,Product_Name:Product_Name,Product_Unit:Product_Unit,resourceid:resourceid,resourcerate:resourcerate,resourceqty:resourceqty, Process:Process,activityid:activityid,project_estimate_Id:project_estimate_Id,Project_Id:Project_Id/*, lead_time:lead_time, credit_period:credit_period*/},
                success: function(data){
                    if(data.error=='No')
                    {
                        
                        $('#activitycost').html(data.acivitycost);
                        $('#activitycostnow').html(data.acivitycostnew);
                        $(".estimationaddedallocation").show();
                        $('#estimate-resourcequantity'+resourceid).val('');
                       // $('#activitycosthead span').html(data.acivityhead);
                        $('.estimationaddedallocation').html(data.result);
                        $('.estimation-left-bar').html(data.datarows);
                        $('#activityunit').val(data.activityunit);
                        //$(".view_estimate").trigger('click');
                        $('.close-resource-list-btn1').trigger('click');
                        $('#listestimateitems').trigger('click') ;
                        $('.allocate-resource-tabs').addClass('add-allocation-list-active');
                    }

                    $('.preloader').hide();
                }
            });
        }

    }
    
    });

                    

$(document).on( "click", "#deleterefresh", function(){
        var Project_Id = $("#project_id").val();
        var project_estimate_Id = $("#EstActivity_Id").val();
        var activityid = $("#activity_id").val();
        var Process = $("#proces_id").val();

        $.ajax({
            type: 'POST',
            url: '../estimateprojectmain/activityresources',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: { Process:Process,activityid:activityid,project_estimate_Id:project_estimate_Id,Project_Id:Project_Id},
            success: function(data){
                if(data.error=='No')
                {
                    $('#activitycost').html(data.acivitycost);
                    $('#activitycostnow').html(data.acivitycostnew);
                	$(".estimationaddedallocation").show();
	                //$('#activitycosthead span').html(data.acivityhead);
	                $('.estimationaddedallocation').html(data.result);
	                $('.estimation-left-bar').html(data.datarows);
	                $('#activityunit').val(data.activityunit);
                }

                $('.preloader').hide();
            }
        });

});


var resorcetypeid = '';

$(document).on( "click", ".resourcesearch-estimate", function(){
        resorcetypeid=$(this).data("id");
        $('#resourcegroupval').val(resorcetypeid);
        var resgroup = $('#resourcegroup').val();
        var activity_id = $('#EstActivity_Id').val();
        $('.preloader').show();

        $.ajax({
            type: 'POST',
            url: '../projectsetup/resourcesearchbytyid',
            dataType: "json",
            data: {activity_id:activity_id,resourcetypeid:resorcetypeid,resgroup:resgroup,mode:'estimate'},
            success: function(data){
                if(data.error=='No')
                {
                    $('.preloader').hide();
                    $('.allocation-items-estimate').html(data.result);
                    $('.activityid').html(data.activityname);
                    $("#no_of_hrs").trigger('click');
                    //$('#restypeid').val(resorcetypeid);
                    //$('#resourcegroupselection').html(data.group);
                    //$('#resourcetable').show();


                    if(resorcetypeid == 33){
                        $('.task_multiple_select').each(function(){
                            resid=$(this).data("resid");

                             mobiscroll.setOptions({
                                locale: mobiscroll.localeEn,// Specify language like: locale: mobiscroll.localePl or omit setting to use default
                                theme: 'ios', // Specify theme like: theme: 'ios' or omit setting to use default
                                    themeVariant: 'light'  // More info about themeVariant: https://docs.mobiscroll.com/5-23-2/select#opt-themeVariant
                            });
                            
                            $j('#task_id'+resid).mobiscroll().select({
                                inputElement: document.getElementById('task_id-select-input-'+resid) ,
                                onOpen: function (event, inst) {
                                    $('.mbsc-popup-content div').each(function() {
                                       if ($(this).html() == "TRIAL") {
                                          $(this).remove();
                                       }
                                    })
                                } 
                            });
                        });
                    }
                    else if(resorcetypeid == 26){

                        $('.eq_task_multiple_select').each(function(){
                            resid=$(this).data("resid");

                             mobiscroll.setOptions({
                                locale: mobiscroll.localeEn,// Specify language like: locale: mobiscroll.localePl or omit setting to use default
                                theme: 'ios', // Specify theme like: theme: 'ios' or omit setting to use default
                                    themeVariant: 'light'  // More info about themeVariant: https://docs.mobiscroll.com/5-23-2/select#opt-themeVariant
                            });
                            
                            $j('#task_id'+resid).mobiscroll().select({
                                inputElement: document.getElementById('task_id-select-input-'+resid) ,
                                onOpen: function (event, inst) {
                                    $('.mbsc-popup-content div').each(function() {
                                       if ($(this).html() == "TRIAL") {
                                          $(this).remove();
                                       }
                                    })
                                } 
                            });
                        });


                    }


                }
                else
                {
                    alert(data.errortext);
                }

                $('.preloader').hide();
            }
        });
    });

    $(document).on( "click", ".resourceAccordion", function(){
        resid = $(this).data("id");
        changeFuelVendor(resid);
    });

    $(document).on( "click",".rem-res-estimate", function(){
        var itemid=$(this).data("id");
        $.ajax({
            type: 'POST',
            url: '../projectsmain/receivepurchaseordercheck',
            dataType: "json",
            data: {estres:itemid},
            success: function(data){
                if(data.receivedres!=0)
                {
                    alert('Resource already received')
                }
                else{
                    var r = confirm("Are you sure you want to delete this Resource ?");
                    if (r == true) {
                        $.ajax({
                            type: 'POST',
                            url: '../estimateprojectmain/deleteprojres',
                            beforeSend : function(){
                                $('#removeresourceitem'+itemid).attr("disabled", true);
                            },
                            dataType: "json",
                            data: {projresid:itemid},
                            success: function(data){
                                if(data.error=='No')
                                {
                                    $("#deleterefresh").trigger('click');
                                    $('#tempresrow'+itemid).remove();
                                    $('#listestimateitems').trigger('click') ;
                                }
                                else {
                                    alert(data.errortext);
                                }

                                $('#removeresourceitem'+itemid).attr("disabled", false);
                            }
                        });
                    }
                }
            }
        });
        
    });
 
    //$(document).on( "change","input[type=radio][name=equ-select]", function(){
    //$('input[type=radio][name=equ-select]').change(function() {
    $(document).on( "change",".equ-select", function(){
        var resid=$(this).data("resid"); 
        var actid=$(this).data("actid"); 
        var type=this.value;
        /*if (type == 'no_of_hrs') {
            
        }
        else if (type == 'lease_period') {

        }*/

        $.ajax({
            type:'POST',
            url:'../resources/resestdata',
            dataType:'json',
            data: {type:type,resid:resid,activity_id:actid},
            success:function(data){
                if(data.error=='No')
                {
                    $('.leasedrow'+resid).html(data.datarows);
                    changeFuelVendor(resid);
                    
                    mobiscroll.setOptions({
                        locale: mobiscroll.localeEn,// Specify language like: locale: mobiscroll.localePl or omit setting to use default
                        theme: 'ios', // Specify theme like: theme: 'ios' or omit setting to use default
                            themeVariant: 'light'  // More info about themeVariant: https://docs.mobiscroll.com/5-23-2/select#opt-themeVariant
                    });
                    
                    $j('#task_id'+resid).mobiscroll().select({
                        inputElement: document.getElementById('task_id-select-input-'+resid) ,
                        onOpen: function (event, inst) {
                            $('.mbsc-popup-content div').each(function() {
                               if ($(this).html() == "TRIAL") {
                                  $(this).remove();
                               }
                            })
                        } 
                    });
                }
            }
        });
    });

    //$(document).on( "change","input[type=radio][name=pequ-select]", function(){
    $(document).on( "change",".pequ-select", function(){
        var resid=$(this).data("resid"); 
        var actid=$(this).data("actid"); 
        var type=this.value;

        $.ajax({
            type:'POST',
            url:'../resources/resplantestdata',
            dataType:'json',
            data: {type:type,resid:resid,activity_id:actid},
            success:function(data){
                if(data.error=='No')
                {
                    $('.plantrow'+resid).html(data.datarows);
                    changeFuelVendor(resid);

                    mobiscroll.setOptions({
                        locale: mobiscroll.localeEn,// Specify language like: locale: mobiscroll.localePl or omit setting to use default
                        theme: 'ios', // Specify theme like: theme: 'ios' or omit setting to use default
                            themeVariant: 'light'  // More info about themeVariant: https://docs.mobiscroll.com/5-23-2/select#opt-themeVariant
                    });
                    
                    $j('#task_id'+resid).mobiscroll().select({
                        inputElement: document.getElementById('task_id-select-input-'+resid) ,
                        onOpen: function (event, inst) {
                            $('.mbsc-popup-content div').each(function() {
                               if ($(this).html() == "TRIAL") {
                                  $(this).remove();
                               }
                            })
                        } 
                    });


                }
            }
        });

    });

    /*$(document).on( "input", ".resourcegroupsearch", function(e){
            
            var resorcetypeid=$('#resourcegroupval').val();
            var resgroup = $('#resourcegroup').val();
            $('.preloader').show();
            //console.log(resorcetypeid);
            $.ajax({
                type: 'POST',
                url: '../projectsetup/resourcesearchbytyid',
                dataType: "json",
                data: {resourcetypeid:resorcetypeid,resgroup:resgroup,mode:'estimate'},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('.preloader').hide();
                        $('.allocation-items-estimate').html(data.result);

                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('.preloader').hide();
                }
            });
    });*/

    $(document).on( "click", "#actestimate-res", function(e){
            
            var resorcetypeid=$('#resourcegroupval').val();
            var resgroup = $('#resourcegroup').val();
            var activity_id = $('#activity_id').val();
            $('.preloader').show();
            //console.log(resorcetypeid);
            $.ajax({
                type: 'POST',
                url: '../projectsetup/resourcesearchbytyid',
                dataType: "json",
                data: {resourcetypeid:resorcetypeid,resgroup:resgroup,activity_id:activity_id,mode:'estimate'},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('.preloader').hide();
                        $('.allocation-items-estimate').html(data.result);

                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('.preloader').hide();
                }
            });
    });

    $(document).on( "keyup", ".resourcegroupsearch", function(e){
            
            var resorcetypeid=$('#resourcegroupval').val();
            var resgroup = $('#resourcegroup').val();
            var activity_id = $('#activity_id').val();
            $('.preloader').show();
            //console.log(resorcetypeid);
            $.ajax({
                type: 'POST',
                url: '../projectsetup/resourcesearchbytyid',
                dataType: "json",
                data: {resourcetypeid:resorcetypeid,resgroup:resgroup,activity_id:activity_id,mode:'estimate'},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('.preloader').hide();
                        $('.allocation-items-estimate').html(data.result);

                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('.preloader').hide();
                }
            });
    });

function OnSearch () 
{
     $('#resourcegroup').trigger('keyup');
            
}

$(document).on('click','.resource-list-btn1', function(e){

    e.preventDefault();
    $('#resourcegroup').val('');
    $('.allocate-resource-tabs').addClass('add-allocation-list-active');
    /*setTimeout(function() {
       //$("html, body").animate({ scrollTop: $('.allocate-resource-tabs').offset().top }, 1000);
    }, 10);*/
});
                
$(document).on('click','.close-resource-list-btn1', function(e){
   e.preventDefault();
   $('.allocate-resource-tabs').removeClass('add-allocation-list-active');
 });

 $(document).on('click','.resource-back-button', function(e){
    e.preventDefault();
    $('#listestimateitems').trigger('click') ;
    $("#Estimate-allocate-body-one-head").show();
    $("#Estimate-allocate-body-one").show();
    $("#Estimate-allocate-body-two").hide();
    $('#accordionprojindex').removeClass('acco-one-active');
    $('#accordionprojindex').removeClass('acco-two-active');
    $('#accordionprojindex').removeClass('acco-three-active');
    $('#accordionprojindex').addClass('acco-four-active');
    $('#accordionprojindex').removeClass('acco-five-active');
  });


 function resExist(resid)
 {
    var retval;
    $.ajax({
        type: 'POST',
        url: '../estimateprojectmain/checkresexist',
        async:false,
        data: {resid:resid},
        success: function(data){
            retval=data;
        }
    });
    return retval;
 }

 $(document).on("change", "input[type=text][name=estequnt]", function () {

    var id = $(this).attr('data-id');
    
    var equconsumption = $('#estimate-fuelqty'+id).val();

    var equrate = $('#estimate-fuelrate'+id).val();

    var equmaintenancecost = $('#estimate-repair'+id).val();

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

    $('#estimate-machnrate'+id).val(total);

}); 
$(document).on("change", "input[type=text][name=esteqrate]", function () {

    var id = $(this).attr('data-id');

    var equconsumption = $('#estimate-fuelqty'+id).val();

    var equrate = $('#estimate-fuelrate'+id).val();

    var equmaintenancecost = $('#estimate-repair'+id).val();

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

    $('#estimate-machnrate'+id).val(total);

});

$(document).on("change", "input[type=text][name=esteqmaint]", function () {

    var id = $(this).attr('data-id');

    var equconsumption = $('#estimate-fuelqty'+id).val();

    var equrate = $('#estimate-fuelrate'+id).val();

    var equmaintenancecost = $('#estimate-repair'+id).val();

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

    $('#estimate-machnrate'+id).val(total);

});


$(document).on("change", "select[name=fuelstyp]", function () {

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

//Lease Equipment
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
    changeFuelVendor(id);


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

$(document).on('keyup','.mannumbr',function(){
    var id = $(this).data('id');
    $('.btnclass'+id).prop('disabled',false);
    var manno = $('#estimate-mannumbr'+id).val();
    var nodays = $('#estimate-no-ofdays'+id).val();

    if(manno != '' && nodays != '')
    {
        var mandays = mandays = manno * nodays;
        $('#estimate-no-ofmdays'+id).val(mandays)
    }else{
        $('#estimate-no-ofmdays'+id).val(0)
    }
   

});

$(document).on('keyup','.ofdays',function(){
    var id = $(this).data('id');
    $('.btnclass'+id).prop('disabled',false);
    var manno = $('#estimate-mannumbr'+id).val();
    var nodays = $('#estimate-no-ofdays'+id).val();

    if(manno != '' && nodays != '')
    {
        var mandays = mandays = manno * nodays;
        $('#estimate-no-ofmdays'+id).val(mandays)
    }else{
        $('#estimate-no-ofmdays'+id).val(0)
    }
   

});

$(document).on('keyup','.quantity',function(){
 
    var act_qqty = $(this).val();
    var act_id = $(this).data('id');
    $('.actid'+act_id).data('qty').val(act_qqty);

});


