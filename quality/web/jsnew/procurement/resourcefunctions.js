$(document).on( "click", ".childresourcegroups", function(){  

    $('#collapserestypes').removeClass('in');

    $('.resourcetype-tab').removeClass('active');

    $('.resources-tab').addClass('active');

    $('#collapseresource').addClass('in');

    $("#collapserestypes").attr("aria-expanded","false");

    $("#collapseresource").attr("aria-expanded","true");

    $('#collapseresource').css('height','');

    $('#collapseresource').show();

    $('#Resourcetype').removeClass('active').next().slideUp();
    $('#Resources').addClass('active').next().slideDown();
    $('#listresources').trigger('click');
    $('#res-tab-show').show();
   
   
});


$(document).on( "click", ".resources-tab", function(){  
		
    /*if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }*/

    var restypeid=$('#searcselectrestype').val();

    if(restypeid=='none'){
        //$('#listresources').trigger('click');
        $('#res-tab-show').hide();
    }
    else{
        $('#res-tab-show').show();
    }

	//$(this).parent('.panel-group').addClass('acco-three-active');
		
});  

$(document).on( "click", "#Resources", function(){
    $('#collapseresource').hide();
});

$(document).on('focus','#purchase_date',function(){
    $('#purchase_date').datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true
    });
});


$(document).on('click','#listresources',function(){ 


    $('.resources-tab').removeClass('addResourcesForm-active');        
    $('#resourceeaddsection').slideUp('slow');// slide down the project listing div
    $('#resourceeditsection').slideUp('slow');// slide down the project listing div
    $('#addvendorsection').slideUp('slow');// slide down the project listing div
    $('#resourceslistsections').slideDown('slow');// slide down the project listing div
    $('#listresources').removeClass('btn-danger').addClass('btn-success');
    $('#addresources').removeClass('btn-success').addClass('btn-danger');

    var idval=$(this).val();
    var restypename=$('#searcselectrestype').val();

    $('.search-and-actions-wrpr').show();

    if(restypename!='none')
    {
       document.getElementById('restyperes').value = restypename;

    }


    $.ajax({
        type: 'POST',
        url: '../resources/search',  
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {resourcetype:$('#searcselectrestype').val(),vendor:$('#searcselectvendor').val(),resgroup:$('#searcselectgroup').val(),resourcename:$('#searchresourcename').val(),location:$('#searchlocation').val(),brand:$('#searchbrand').val()},
        success: function(data){
            if(data.error=='No')
            { 

                $('#procresourceitems').html(data.result);
                $('#Resource-content1').show();
                $('#Resource-content2').show();
                $('#procresourcetable').show();
                $('#procrestypename').html(data.restypename);
                $('#procrestypeid').val(data.restypeid);
                $('.asset_reg_row').hide();
                $("#resgroup").removeAttr("disabled");
                $("#resourcename").removeAttr("readonly");
                $('#resourceGroup').html(data.resGroupDropDown);
                //$('#searchresourcename').val('');
                if(restypename==26){

                    $('#unitheads').html('Unit');
                    $('#rateheads').html('Depreciation');
                    $('.asset_reg_row').show();
                    $('#rsgrpshow').show();

                    $("#resgroup").attr("disabled", "disabled");
                    $("#resourcename").attr("readonly", "readonly");
                    //$("#resourcerate").prop("disabled", true);
                    $('.plantrate').show();

                }
                else if(restypename==24){

                    $('#rsgrpshow').show();
                    $("#resourcerate").prop("disabled", false);
                    $('#unitheads').html('Lease Unit');
                    $('#rateheads').html('Lease Rate');
                    $('.plantrate').show();

                }
                else{
                    $('#rsgrpshow').hide();
                    $("#resourcerate").prop("disabled", false);
                    $('.plantrate').show();
                }
               // $('#procresgrpname').html(data.resgroupname);
            }
            else
            {
                alert(data.errortext);
            }
            $('.preloader').hide();
        }
    });           	

});
// list project click  \
// add project click
    
$(document).on( "click", "#cancelres", function(){ 

    $('.resources-tab').removeClass('addResourcesForm-active');
    $('.content-action-wrpr').show();
});

$(document).on( "click", "#addresources", function(){ 

    //$('#addresourceform')[0].reset();
    $('.equipmentdetails').hide();
    $('#resgroup').val('none');
});
    

$(document).on( "click", "#canceladdvendor", function(){ 
       
    $('.resources-tab').removeClass('addVendor-at-ResourceTabForm-active');
    $('#resourceeaddsection').hide();
    $('#resourceslistsections').show();

});

 $(document).on( "click", "#canceleditres", function(){ 
    $('.resources-tab').removeClass('editResourcesForm-active');
    $('#resourceeaddsection').hide();
    $('#editresource').html('');
    $('#resourceslistsections').show();
    $('.search-and-actions-wrpr').show();


});


$(document).on("change", "#asset_register_item_id", function () {
    selectedType = $( "#asset_register_item_id option:selected" ).data('eq-type');
    $("#resgroup option[data-eq-type='" + selectedType + "']").prop("selected", true).trigger('change');
    $('#resourcename').val($( "#asset_register_item_id option:selected" ).text() + ' - ' +$('#id_number').val());
});

$(document).on("change", "#id_number", function () {
    $('#resourcename').val($( "#asset_register_item_id option:selected" ).text() + ' - ' +$('#id_number').val());
});


$(document).on("change", "#resgroup", function () {

    var equipmentid=$('#resgroup').val();

    if(equipmentid!='none'){
        $('.equipmentdetails').show();

        if(equipmentid==102){
            $('#consumptionlabel').html('Diesel (L/H)');
            $('#ratelabel').html('Diesel Rate');
            /* $('#diesunit').show();

            $('#pownam').removeClass('col-md-6');
            $('#pownam').addClass('col-md-4');
            $('#dirate').removeClass('col-md-6');
            $('#dirate').addClass('col-md-4'); */
        }
        else{
            $('#consumptionlabel').html('Power (KWh)');
            $('#ratelabel').html('Power Rate');
            /* $('#diesunit').hide();
            $('#pownam').removeClass('col-md-4');
            $('#pownam').addClass('col-md-6');
            $('#dirate').removeClass('col-md-4');
            $('#dirate').addClass('col-md-6');
 */
        }
    }
    else{
        $('.equipmentdetails').hide();
    }

});    

$(document).on("change", "#editresgroup", function () {

    var equipmentid=$('#editresgroup').val();

    if(equipmentid!='none'){
        $('.equipmentdetails').show();

        if(equipmentid==102){
            $('#editconsumptionlabel').html('Diesel (L/H)');
            $('#editratelabel').html('Diesel Rate');
            /* $('#editfueleqt').removeClass('col-md-4');
            $('#editfueleqt').addClass('col-md-2');
            $('#editfuelunt').show(); */
        }
        else{
            $('#editconsumptionlabel').html('Power (KWh)');
            $('#editratelabel').html('Power Rate');
            /* $('#editfueleqt').removeClass('col-md-2');
            $('#editfueleqt').addClass('col-md-4');
            $('#editfuelunt').hide(); */
        }
    }
    else{
        $('.equipmentdetails').hide();
    }

}); 

$(document).on("change", "#equconsumption", function () {

    var equconsumption = $('#equconsumption').val();

    var equrate = $('#equrate').val();

    var equmaintenancecost = $('#equmaintenancecost').val();

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

    $('#machinerate').val(total);

});    

$(document).on("change", "#equrate", function () {

    var equconsumption = $('#equconsumption').val();

    var equrate = $('#equrate').val();

    var equmaintenancecost = $('#equmaintenancecost').val();

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

    $('#machinerate').val(total);

}); 

$(document).on("change", "#equmaintenancecost", function () {

    var equconsumption = $('#equconsumption').val();

    var equrate = $('#equrate').val();

    var equmaintenancecost = $('#equmaintenancecost').val();

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

    $('#machinerate').val(total);

}); 

$(document).on("change", "#editequconsumption", function () {

    var equconsumption = $('#editequconsumption').val();

    var equrate = $('#editequrate').val();

    var equmaintenancecost = $('#editequmaintenancecost').val();

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

    $('#editmachinerate').val(total);

});    

$(document).on("change", "#editequrate", function () {

    var equconsumption = $('#editequconsumption').val();

    var equrate = $('#editequrate').val();

    var equmaintenancecost = $('#editequmaintenancecost').val();

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

    $('#editmachinerate').val(total);

}); 

$(document).on("change", "#editequmaintenancecost", function () {

    var equconsumption = $('#editequconsumption').val();

    var equrate = $('#editequrate').val();

    var equmaintenancecost = $('#editequmaintenancecost').val();

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

    $('#editmachinerate').val(total);

}); 
    
$(document).on("change", ".accounthead", function () {   
    var accountheadid=$('#accounthead').val();

    var accounthd=$(this).val();
    $.ajax({
        type:'POST',
        url:'../resources/accounthead',
        dataType:'json',
        data: {acchead:accounthd},
        success:function(data){
            if(data.error=='No')
            {
                if(data.result!=''){
                    document.getElementById('accountsubgrp').value = data.result;
                }
                else{
                    document.getElementById('accountsubgrp').value = '';
                }
                $('#accountsubgrplist').html(data.datarow);

            }
        }
    });

});

$(document).on("change", "#vendor", function () {   
    var vendor = $('#vendor').val();

    if(vendor!=''){
        $.ajax({
            type:'POST',
            url:'../resources/vendordetails',
            dataType:'json',
            data: {vendor:vendor},
            success:function(data){
                if(data.error=='No')
                {
                    if(data.count!=0){

                        $('#vendorlocation').val(data.location);
                        $('#vendorphone').val(data.phone);
                        $('#vendoremail').val(data.email);
                        
                    }
                    else{
                        $('#vendorlocation').val('');
                        $('#vendorphone').val('');
                        $('#vendoremail').val('');
                    }

                }
            }
        });
    }
    else{
        $('#vendorlocation').val('');
        $('#vendorphone').val('');
        $('#vendoremail').val('');
    }

});

$(document).on("change", "#editVendor", function () {   
    var vendor = $('#editVendor').val();

    if(vendor!=''){
        $.ajax({
            type:'POST',
            url:'../resources/vendordetails',
            dataType:'json',
            data: {vendor:vendor},
            success:function(data){
                if(data.error=='No')
                {
                    if(data.count!=0){

                        $('#editresourceloc').val(data.location);
                        $('#editresourcephne').val(data.phone);
                        $('#editresourcemail').val(data.email);
                        
                    }
                    else{
                        $('#editresourceloc').val('');
                        $('#editresourcephne').val('');
                        $('#editresourcemail').val('');
                    }

                }
            }
        });
    }
    else{
        $('#editresourceloc').val('');
        $('#editresourcephne').val('');
        $('#editresourcemail').val('');
    }

});

$(document).on( "click", "#vendor", function(){  

    var searchvalue = $('#vendor').val();

    if(searchvalue!=''){
        $('#vendor').val('');
        $('#vendorlocation').val('');
        $('#vendorphone').val('');
        $('#vendoremail').val('');
    }

});

$(document).on("change", ".nwvendor", function () { 
    var resid = $('#saveresourceval').val(); 
    var vendor = $('#vendoradd'+resid).val();

    if(vendor!=''){
        $.ajax({
            type:'POST',
            url:'../resources/vendordetails',
            dataType:'json',
            data: {vendor:vendor},
            success:function(data){
                if(data.error=='No')
                {
                    if(data.count!=0){

                        $('#vendlocation'+resid).val(data.location);
                        $('#vendorph'+resid).val(data.phone);
                        $('#vendemail'+resid).val(data.email);
                        
                    }
                    else{
                        $('#vendlocation'+resid).val('');
                        $('#vendorph'+resid).val('');
                        $('#vendemail'+resid).val('');
                    }

                }
            }
        });
    }
    else{
        $('#vendlocation'+resid).val('');
        $('#vendorph'+resid).val('');
        $('#vendemail'+resid).val('');
    }

});

$(document).on( "click", ".nwvendor", function(){  

    var resid = $('#saveresourceval').val(); 
    var searchvalue = $('#vendoradd'+resid).val();

    if(searchvalue!=''){
        $('#vendoradd'+resid).val('');
        $('#vendlocation'+resid).val('');
        $('#vendorph'+resid).val('');
        $('#vendemail'+resid).val('');
    }

});

$(document).on( "click", "#accounthead", function(){  

    var searchvalue = $('#accounthead').val();

    if(searchvalue!=''){
        $('#accounthead').val('');
        $('#accountsubgrp').val('');
    }

});

$(document).on( "click", "#accountsubgrp", function(){  

    var searchvalue = $('#accountsubgrp').val();

    if(searchvalue!=''){
        $('#accountsubgrp').val('');
    }

});

$(document).on( "click", "#editaccounthead", function(){  

    var searchvalue = $('#editaccounthead').val();

    if(searchvalue!=''){
        $('#editaccounthead').val('');
        $('#editaccountsubgrp').val('');
    }

});

$(document).on( "click", "#editaccountsubgrp", function(){  

    var searchvalue = $('#editaccountsubgrp').val();

    if(searchvalue!=''){
        $('#editaccountsubgrp').val('');
    }

});

$(document).on("change", ".editaccounthead", function () {   
    var accountheadid=$('#editaccounthead').val();

    var accounthd=$(this).val();
    $.ajax({
        type:'POST',
        url:'../resources/accounthead',
        dataType:'json',
        data: {acchead:accounthd},
        success:function(data){
            if(data.error=='No')
            {
                if(data.result!=''){
                    document.getElementById('editaccountsubgrp').value = data.result;
                }
                else{
                    document.getElementById('editaccountsubgrp').value = '';
                }
                $('#accountsubgrplist').html(data.datarow);

            }
        }
    });

});


$(document).on('click','#saveresource',function(){   

        var error=0;
        var restype= $('#restyperes').val();
        $('.error').hide();

        if($('#asset_register_item_id').val() == '' && restype == 26)
        {
            $("#asset_register_item_id").next("span").html('Select Asset Register Item').show('slow');
            error=1;
        }

        /*if($('#purchase_date').val()=='' && restype == 26)
        {
            $("#purchase_date").next("span").html('Enter Date of Purchase').show('slow');
            error=1;
        }*/

        if($('#reg_number').val()=='' && restype == 26)
        {
            $("#reg_number").next("span").html('Enter Reg. Number').show('slow');
            error=1;
        }

        if($('#id_number').val()=='' && restype == 26)
        {
            $("#id_number").next("span").html('Enter ID Number').show('slow');
            error=1;
        }

        if($('#capacity').val()=='' && restype == 26)
        {
            $("#capacity").next("span").html('Enter Capacity').show('slow');
            error=1;
        }

        if($('#resourcename').val()=='')
        {
            $("#resourcename").next("span").html('Enter Resource Name').show('slow');
            error=1;
        }

        if($('#vendorlocation').val()=='')
        {
            $("#vendorlocation").next("span").html('Enter Vendor Location').show('slow');
            error=1;
        }

       if($('#resourceunit').val()=='')
        {
            $("#resourceunit").next("span").html('Enter unit').show('slow');
            error=1;
        }


       if($('#vendorphone').val()=='')
        {
            $("#vendorphone").next("span").html('Enter Vendor Phone').show('slow');
            error=1;
        }

        if($('#restyperes').val()=='none')
        {
            $("#restyperes").next("span").html('Select Resource Type').show('slow');
            error=1;
        }
        if($('#accounthead').val()=='')
        {
            $("#accountheadlist").next("span").html('Select Account Head').show('slow');
            error=1;
        } 

        if($('#resourcerate').val()=='')
        {
            $("#resourcerate").next("span").html('Enter rate').show('slow');
        }

        if($('#accountsubgrp').val()=='')
        {
            $("#accountsubgrplist").next("span").html('Select Account Subgroup').show('slow');
            error=1;
        }
        if($('#vendor').val()=='')
        {
            $("#vendorlist").next("span").html('Select Vendor').show('slow');
            error=1;
        }


         if($('#brand').val()=='')
        {
            $("#brandlist").next("span").html('Select Brand').show('slow');
            error=1;
        }

        var accntsubgrp = $("#accountsubgrp").val();

        var accntsubgrpobj = $("#accountsubgrplist").find("option[value='" + accntsubgrp + "']");

        if(accntsubgrpobj == null){
            $("#accountsubgrplist").next("span").html('Sub group not found in the list').show('slow');
            error=1;
        }

        if(ResourceNameExists($('#resourcename').val())=='Yes')
        {
            $("#resourcename").next("span").html('Resource Exists').show('slow'); 
            error=1;
        }

        var sch = $('input[name="schedules"]:checked').val();  
        if(sch == 1)
        {
            var schedules = 1;
        }
        else{
            var schedules = 0;
        }
/* 
        if(restype==26){
            var machinerate = $('#machinerate').val();
        }
        else{ */
            
            var str = $('#resourcerate').val();
            var machinerate = str.replace(/,/g, '');

        //}

        if(error==0){
            $.ajax({
                type:'POST',
                url:'../resources/create',
                beforeSend:function(){
                    $('#saveresource').attr("disabled", true);
                },
                dataType:'json',
                data: {
                        asset_register_item_id : $('#asset_register_item_id').val(),
                        purchase_date : $('#purchase_date').val(),
                        reg_number : $('#reg_number').val(),
                        id_number : $('#id_number').val(),
                        capacity : $('#capacity').val(),
                        resourcename : $('#resourcename').val(),
                        schedules : schedules,
                        restype : restype,
                        accountsubgrp : $('#accountsubgrp').val(),
                        accounthead : $('#accounthead').val(),
                        addresourcetypeaccount : $('#addresourcetypeaccount').val(),
                        vendor : $('#vendor').val(),
                        unit : $('#resourceunit').val(),
                        rate : machinerate,
                        vendorlocation : $('#vendorlocation').val(),
                        addProcess : $('#addProcess').val(),
                        resgroup : $('#resgroup').val(),
                        resourcequantity : $('#resourcequantity').val(),
                        vendorphone : $('#vendorphone').val(),
                        vendoremail : $('#vendoremail').val(),
                        brand : $('#brand').val(),
                        equconsumption : $('#equconsumption').val(),
                        equrate : $('#equrate').val(),
                        equmaintenancecost : $('#equmaintenancecost').val(),
                        machinerate : $('#machinerate').val(),
                        equuunit : $('#equuunit').val(),
                        resourceGroup : $('#resourceGroup').val(),
                        lead_time : $('#lead_time').val(),
                        credit_period : $('#credit_period').val()

                    },
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#saveresource').attr("disabled", false);
                        $('#searcselectrestype').val(restype); 
                        $('#addresourceform')[0].reset();
                        $('.cancel').trigger('click');
                        $('#listresources').trigger('click'); 
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#saveresource').attr("disabled", false);
                }
            });
        }



    });

    $(document).on("change", "#restyperes", function () {
        var restype=$(this).val();

        if(restype==26){ 
            $('#unitheads').html('Unit');
            $('#rateheads').html('Depreciation');

            $('#rsgrpshow').show();
            //$("#resourcerate").prop("disabled", true);
            $('.plantrate').show();
            //$('.equipmentdetails').show();
        }
        else if(restype==24){
            $('#unitheads').html('Lease Unit');
            $('#rateheads').html('Lease Rate');

            $('#rsgrpshow').show();
            //$("#resourcerate").prop("disabled", false);
            $('.plantrate').show();
            //$('.equipmentdetails').show();
        }
        else{
            $('#unitheads').html('Unit');
            $('#rateheads').html('Rate');
            $('#rsgrpshow').hide();
            $('#resgroup').val('none');
            $('.equipmentdetails').hide();
            //$("#resourcerate").prop("disabled", false);
            $('.plantrate').show();
        }
        /*$.ajax({
            type:'POST',
            url:'../resources/getresourcegroups',
            dataType:'json',
            data: {restype:restype},
            success:function(data){
                if(data.error=='No')
                {
                    $('#resgrpres').html(data.result);
                }
            }
        });*/
    });

    $(document).on("change", "#editrestyperes", function () {
        var restype=$(this).val();

        if(restype==26){
            $('#editrsgrpshow').show();
            //$("#editresourcerate").prop("disabled", true);
            //$('.equipmentdetails').show();
            $('#editunithd').html('Unit');
            $('#editratehd').html('Depreciation');
            $('.edtplant').show();
        }
        else if(restype==24){
            $('#editrsgrpshow').show();
            //$("#editresourcerate").prop("disabled", false);
            //$('.equipmentdetails').show();
            $('#editunithd').html('Lease Unit');
            $('#editratehd').html('Lease Rate');
            $('.edtplant').show();
        }
        else{
            $('#editrsgrpshow').hide();
            $('#editresgroup').val('none');
            $('.equipmentdetails').hide();
            //$("#editresourcerate").prop("disabled", false);
            $('#editunithd').html('Unit');
            $('#editratehd').html('Rate');
            $('.edtplant').show();
        }
    });

    $(document).on("change", ".editrestypelist", function () {
        var id=$(this).attr('data-id');
        var restype=$(this).val();
        $.ajax({
            type:'POST',
            url:'../resources/getresourcegroups',
            dataType:'json',
            data: {restype:restype},
            success:function(data){
                if(data.error=='No')
                {
                    $('#editgrouptypetext'+id).html(data.result);
                }
            }
        });
    });

    $(document).on("change", "#searcselectrestype", function () {
        var restype=$(this).val();
        $.ajax({
            type:'POST',
            url:'../resources/getresourcegroups',
            dataType:'json',
            data: {restype:restype},
            success:function(data){
                if(data.error=='No')
                {
                    $('#searcselectgroup').html(data.result);
                }
            }
        });
    });

    $(document).on("change", "#addresourcetype", function () {

        var valueSelected = this.val();
        

    });


    $( "#resourceitems" ).sortable({
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
                url: '../resources/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection()
    // save project click
    //project function ends here

$(document).on('click','#resourcesearch',function(){ 
    $('#listresources').trigger('click');
});
$(document).on("keyup", "#searchresourcename", function(){
        $('#listresources').trigger('click')
    });

/*$(document).on( "click", ".editresourcebutton", function(){
    var idval=$(this).val();
    $('.resources-tab').addClass('editResourcesForm-active');
    $('#addresources').removeClass('btn-danger').addClass('btn-success');
    $('#listresources').removeClass('btn-success').addClass('btn-danger');
    $.ajax({
        type: 'POST',
        url: '../resources/getresourcedetails',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {id:idval},
        success: function(data){
            if(data.error=='No')
            {

                $('#editresource').html(data.result);
               // $('.acco-three input[type=radio]').trigger('click');
                //$('#resourcetableedit').show();
            }
            else
            {
                alert(data.errortext);
            }
            $('.preloader').hide();
        }
    });
} );*/

$(document).on( "click", ".editresourcebuttons", function(){
    var idval=$(this).val();
    $('.resources-tab').addClass('editResourcesForm-active');
    $('#addresources').removeClass('btn-danger').addClass('btn-success');
    $('#listresources').removeClass('btn-success').addClass('btn-danger');
    $('.search-and-actions-wrpr').hide();
    $.ajax({
        type: 'POST',
        url: '../resources/editresource',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {id:idval},
        success: function(data){
            if(data.error=='No')
            {

                $('#editresource').html(data.result);
               // $('.acco-three input[type=radio]').trigger('click');
                //$('#resourcetableedit').show();
            }
            else
            {
                alert(data.errortext);
            }
            $('.preloader').hide();
        }
    });
} );

/*$(document).on( "click", ".editresourcebuttons", function(){
    var idval=$(this).val();
    $('#editresourcebuttons'+idval).hide();
    $('#resourcetabname'+idval).hide();
    $('#editresourcername'+idval).show();
    $('#saveresorbutton'+idval).show();

    $('#resourcetext'+idval).hide();
    $('#resourcevendtext'+idval).show();
    //$('#resourceratetext'+idval).hide();
    //$('#edit_vendorbutton'+idval).hide();
});*/

$(document).on( "click", ".addvendor", function(){ 
    var idval=$(this).val();
    
    $('.resources-tab').addClass('addVendor-at-ResourceTabForm-active');
    $('#addresources').removeClass('btn-danger').addClass('btn-success');
    $('#listresources').removeClass('btn-success').addClass('btn-danger');

    $('.search-and-actions-wrpr').hide();
     
    $.ajax({
        type: 'POST',
        url: '../resources/resourcedetails',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {id:idval},
        success: function(data){
            if(data.error=='No')
            {
               
                $('#resvendoradd').html(data.result);
                $('.resourcename').html(data.resourcename);
                 //$('.resources-tab').removeClass('addVendor-at-ResourceTabForm-active');
                $('#listresources').trigger('click');
                //$('#addvendortable').show();
            }
            else
            {
                alert(data.errortext);
            }
            $('.preloader').hide();
        }
    });
});


// save edited resources function
/*$(document).on( "click", "#saveresourcebutton", function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();

    if($('#editresourcename'+idval).val()=='')
    {
        $('#editresourcename'+idval).next("span").html('Enter Resource Name').show('slow');
        error=1;
    }

    if($('#editgrouptypetext'+idval).val()=='')
    {
        $('#editgrouptypetext'+idval).next("span").html('Select Resource Group').show('slow');
        error=1;
    }

    if($('#editresourcelocation'+idval).val()=='')
    {
        $('#editresourcelocation'+idval).next("span").html('Enter Location').show('slow');
        error=1;
    }
    if($('#editresourcetype'+idval).val()=='none')
    {
        $('#editresourcetype'+idval).next("span").html('Select Resource Type').show('slow');
        error=1;
    }
    if($('#editresourcevendor'+idval).val()=='none')
    {
        $('#editresourcevendor'+idval).next("span").html('Select Vendor').show('slow');
        error=1;
    }
    if($('#addresourcetypeaccount'+idval).val()=='none')
    {
        $('#addresourcetypeaccount'+idval).next("span").html('Select Resource Account').show('slow');
        error=1;
    }
    var resourcename=$('#editresourcename'+idval).val();
    var resourceunit=$('#resunit'+idval).val();
    var resquantity=$('#resquantity'+idval).val();

    if ($('#logcheck').is(':checked')){
        var logcheck = 1;
    }
    else {
        var logcheck = 0;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../resources/updates',
            beforeSend : function(){
                $('#saveresourcetypebutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {resourceid:idval,resourcename:resourcename,resourceunit:resourceunit,logcheck:logcheck,resquantity:resquantity},
            success: function(data){
                if(data.error=='No')
                {
                    $('#listresources').trigger('click');

                }
                else
                {
                    alert(data.errortext);
                }

                $('#saveresourcetypebutton'+data.Id).attr("disabled", false);
            }
        });
    }

} );*/

$(document).on( "click", "#saveresourcebutton", function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    var str =  $('#editresourcerate').val();
    var erate = str.replace(/,/g, '');
    $('#editresourcerate').val(erate);


    if($('#edit_asset_register_item_id').val() == '' && restype == 26)
    {
        $("#edit_asset_register_item_id").next("span").html('Select Asset Register Item').show('slow');
        error=1;
    }

    /*if($('#edit_purchase_date').val()=='' && restype == 26)
    {
        $("#edit_purchase_date").next("span").html('Enter Date of Purchase').show('slow');
        error=1;
    }*/

    if($('#edit_reg_number').val()=='' && restype == 26)
    {
        $("#edit_reg_number").next("span").html('Enter Reg. Number').show('slow');
        error=1;
    }

    if($('#edit_id_number').val()=='' && restype == 26)
    {
        $("#edit_id_number").next("span").html('Enter ID Number').show('slow');
        error=1;
    }

    if($('#edit_capacity').val()=='' && restype == 26)
    {
        $("#edit_capacity").next("span").html('Enter Capacity').show('slow');
        error=1;
    }


    if($('#editresourcename').val()=='')
    {
        $('#editresourcename').next("span").html('Enter Resource Name').show('slow');
        error=1;
    }

    if($('#editresourceunit').val()=='')
    {
        $('#editresourceunit').next("span").html('Enter Unit').show('slow');
        error=1;
    }

    if($('#editresourcerate').val()=='')
    {
        $('#editresourcerate').next("span").html('Enter Rate').show('slow');
        error=1;
    }

    var sch = $('input[name="editschedules"]:checked').val();  
    if(sch == 1)
    {
        var schedules = 1;
    }
    else{
        var schedules = 0;
    }

    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../resources/resourceupdate',
            dataType: "json",
            data: $('#editresourceform').serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('.resources-tab').removeClass('editResourcesForm-active');
                    $('#resourceeaddsection').hide();
                    $('#editresource').html('');
                    $('#resourceslistsections').show();
                    $('#listresources').trigger('click');

                }
                else
                {
                   
                    alert(data.errortext);
                }

            }
        });
    }

} );
//athira
/*$(document).on("click", "#addresources", function(){
    var resval = $('.resourcetype').val();  alert(resval);
});*/

/*$(document).on('change','#editresourcerate',function(){ 
    var chkval = $('input[name="editresourcerate"]:checked').val();  
    if(chkval==1)
    {
        alert("1");
    }
    else
    {
        alert("2");
    }
});*/

$(document).on("click", "#addresources", function(){
    var res_type = $("#procrestypeid").val(); 
    if(res_type==26)
    {
        $('.schedulediv').show();
    }
    else
    {
        $('.schedulediv').hide();
    }
});

$(document).on("click", ".editresourcebuttons", function(){ 
    var res_type = $("#procrestypeid").val();
   /* if(res_type==26)
    {
        $('.scheduleeditdiv').show();
    }
    else
    { 
        $("#schdshw").css({
          display: "none",
          visibility: "hidden"
        });
    }*/

    if(res_type==26)
    {
        $('.scheduleeditdiv').show();
    }
    else
    {
        $('.scheduleeditdiv').hide();
    }
});

$(document).on("change" , "#restyperes", function(){
    var res_type = $("#restyperes").val(); 
    if(res_type==26)
    {
        $('.schedulediv').show();
    }
    else
    {
        $('.schedulediv').hide();
    }
});

$(document).on("change" , "#editrestyperes", function(){
    var res_type = $("#restyperes").val(); 
    if(res_type==26)
    {
        $('.scheduleeditdiv').show();
    }
    else
    {
        $('.scheduleeditdiv').hide();
    }
});

$(document).on( "click", "#addvendor", function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    var resourcevendor=$('#vendoradd'+idval).val();
    
    var brandvendor=$('#brandadd'+idval).val();
    var vendorlocation=$('#vendlocation'+idval).val();

    var vendorphone=$('#vendorph'+idval).val();
    var vendoremail=$('#vendemail'+idval).val();

    var resrate=$('#resrate'+idval).val()*1;

    if(resourcevendor=='')
    {
        //$('#vendoradd'+idval).next("span").html('Select Vendor').show('slow');
        $('#vendoradd'+idval).next("span").html('Select Vendor').fadeIn().delay(3000).fadeOut();
        error=1;
    }

    if(brandvendor=='')
    {
        //$('#brandadd'+idval).next("span").html('Select Brand').show('slow');
        $('#brandadd'+idval).next("span").html('Select Brand').fadeIn().delay(3000).fadeOut();
        error=1;
    }

    if(vendorlocation=='')
    {
        //$('#vendlocation'+idval).next("span").html('Enter Location').show('slow');
        $('#vendlocation'+idval).next("span").html('Enter Location').fadeIn().delay(3000).fadeOut();
        error=1;
    }

    if(vendorphone=='')
    {
        //$('#vendorph'+idval).next("span").html('Enter Phone').show('slow');
        $('#vendorph'+idval).next("span").html('Enter Phone').fadeIn().delay(3000).fadeOut();
        error=1;
    }

    if(vendoremail=='')
    {
        //$('#vendemail'+idval).next("span").html('Enter Email').show('slow');
        $('#vendemail'+idval).next("span").html('Enter Email').fadeIn().delay(3000).fadeOut();
        error=1;
    }

    if(resrate=='')
    {
        //$('#resrate'+idval).next("span").html('Enter Rate').show('slow');
        $('#resrate'+idval).next("span").html('Enter Rate').fadeIn().delay(3000).fadeOut();
        error=1;
    }

    if(error==0){
    
        $.ajax({
            type: 'POST',
            url: '../resources/addvendor',
            beforeSend : function(){
                $('#addvendor'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {resourceid:idval,resourcevendor:resourcevendor,brand:brandvendor,vendorphone:vendorphone,vendoremail:vendoremail,vendorlocation:vendorlocation,resrate:resrate},
            success: function(data){
                if(data.error=='No')
                {
                      $('.resources-tab').removeClass('addVendor-at-ResourceTabForm-active');
                     
                    //$('#resvendoradd').hide();  
                    $('.search-and-actions-wrpr').show();
                    $('.form-title').hide();
                    $('#resourceslistsections').show();
                    $('#procresourceitems').show();
                    $('#procresourcetable').show();
                   // alert ("hi");

                    //$('#listresources').trigger('click')
                     $('#listresources').trigger('click'); 

                }
                else
                {
                    alert(data.errortext);
                }
                $('#addvendor'+data.Id).attr("disabled", false);
            }
        });

    }
    

});   

$(document).on( "click", ".saveresorbutton", function(){
    var idval=$(this).val();
    var mode=$(this).attr('data-mode');
    var name=$('#editresourcername'+idval).val();
    var oldname=$('#resourcername_old'+idval).val();
    $.ajax({
        type: 'POST',
        url: '../resources/update',
        beforeSend : function(){
            $('#saveresorbutton'+idval).attr("disabled", true);
        },
        dataType: "json",
        data: {resourceid:idval,resourcename:name,oldresourcename:oldname,mode:mode},
        success: function(data){
            if(data.error=='No')
            {
                //$('#resourcetext'+idval).html(data.Name).show(); 
                $('#resourcetabname'+idval).html(data.Name).show(); 
                $('#resourcername_old'+idval).val(data.Name); 
                 $('#editresourcername'+idval).val(data.Name);
                
                $('#editresourcebuttons'+idval).show();
                $('#editresourcername'+idval).hide();

                $('#saveresorbutton'+idval).hide();
                $('#saveresorbutton'+idval).attr("disabled", false);
            }
        }
    });
});


$(document).on( "click", ".editvendorsbutton", function(){
    var idval=$(this).val();
    
    $('#editvendorbutton'+idval).hide();
    $('#editresourcelocation'+idval).show();
    $('#editresourcevendor'+idval).show();
    $('#editresourcerate'+idval).show();
    $('#editresourceunits'+idval).show();
    //$('#editresourcelast'+idval).show();
    $('#editresourcevenbrand'+idval).show();
    $('#saveresvendorbutton'+idval).show();
    

    $('#resourceloctext'+idval).hide();
    $('#resourcevendtext'+idval).hide();
    $('#resourceratetext'+idval).hide();
    $('#resourceunitstext'+idval).hide();
    $('#resourcevenbrandtext'+idval).hide();
    //$('#lastupdated'+idval).hide();
    
});

$(document).on( "click", ".saveresvendorbutton", function(){
    var idval=$(this).val();
    var mode=$(this).attr('data-mode');
    var location=$('#editresourcelocation'+idval).val();
    var vendor=$('#editresourcevendor'+idval).val();
    var rate=$('#editresourcerate'+idval).val();
    var units=$('#editresourceunits'+idval).val();
    //var lstupdated=$('#editresourcelast'+idval).val();
    var brand=$('#editresourcevenbrand'+idval).val();
    $.ajax({
        type: 'POST',
        url: '../resources/updateresvendor',
        beforeSend : function(){
            $('#saveresvendorbutton'+idval).attr("disabled", true);
        },
        dataType: "json",
        data: {resourceid:idval,brand:brand,resourcerate:rate,resourcevendor:vendor,location:location,units:units,mode:mode},
        success: function(data){
            if(data.error=='No')
            {
                //$('#listresources').trigger('click');
                $('#resourceloctext'+idval).html(data.Location).show();
                $('#resourcevendtext'+idval).html(data.Vendor).show();
                $('#resourceratetext'+idval).html(data.Price).show();
                $('#resourceunitstext'+idval).html(data.Units).show();
                $('#lastupdated'+idval).html(data.Lastupdated).show();
                $('#resourcevenbrandtext'+idval).html(data.brandname).show();
                $('#editvendorbutton'+idval).show();

                $('#editresourcelocation'+idval).hide();
                $('#editresourcevendor'+idval).hide();
                $('#editresourcerate'+idval).hide();
                $('#editresourceunits'+idval).hide();
                $('#editresourcelast'+idval).hide();
                $('#editresourcevenbrand'+idval).hide();
                $('#saveresvendorbutton'+idval).hide();
                $('#saveresvendorbutton'+idval).attr("disabled", false);
            }
        }
    });
});
$(document).on( "click", ".deletresourcebutton", function(){
    var idval=$(this).val();
    var mode=$(this).attr('data-mode');
    if(mode=='master'){
        var r = confirm("Are you sure you want to delete this Resource?");

        if (r == true) {

            if(idval == 183)
            {
                alert("Cannot delete as it is Petrol");

            }else if(idval == 184)
            {
                alert("Cannot delete as it is Desel");

            }else{

                $.ajax({
                    type: 'POST',
                    url: '../resources/deleteresource',
                    beforeSend : function(){
                        $('#delet_resourcebutton'+idval).attr("disabled", true);
                    },
                    dataType: "json",
                    data: {resourceid:idval,mode:mode},
                    success: function(data){
                        if(data.error=='No')
                        {
                            if(mode=='master'){
                               // $('#resourcerow'+idval).remove();
                                $('#listresources').trigger('click');
                            }
                            else {
                                $('#resvendorrow'+idval).remove();
                            }
    
                        }
                        else
                        {
                            alert(data.errortext);
                        }
    
                        $('#delet_resourcebutton'+data.Id).attr("disabled", false);
                    }
                });

            }

            

        } else {
            return false;
        }
    }
    else {
        var s = confirm("Are you sure you want to delete this Vendor?");

        if (s == true) {

            $.ajax({
                type: 'POST',
                url: '../resources/deleteresource',
                beforeSend : function(){
                    $('#deletresourcebutton'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {resourceid:idval,mode:mode},
                success: function(data){
                    if(data.error=='No')
                    {
                        if(mode=='master'){
                           // $('#resourcerow'+idval).remove();
                            $('#listresources').trigger('click');
                        }
                        else {
                            $('#resvendorrow'+idval).remove();
                        }

                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deletresourcebutton'+data.Id).attr("disabled", false);
                }
            });

        } else {
            return false;
        }
    }

});
function ResourceNameExists(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../resources/checkname',
        async:false,
        data: {name:name,type:$('selectedResTypeId').val()},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}




$(document).on( "click", ".addResourceGroupForm", function(){
    $.ajax({
        type: 'POST',
        url: '../resources/resourcegrouplist',
        data: {resType:$('#procrestypeid').val()},
        dataType: "json",
        success: function(data){
            $("#resGroupListContainer").html(data.result);
        }
    });
});

$(document).on('click','#saveResGroup',function(){

        var error=0;
        $('.error').hide();
        if($('#resgroupname').val()=='')
        {
            $('#resgroupname').next("span").html('Enter Resource group name').show('slow');
            error=1;
        }

        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../resources/resgroupcreate',
                beforeSend : function(){
                    $('#saveResGroup').attr("disabled", true);
                },
                dataType: "json",
                data: {resgroupname:$('#resgroupname').val(), resType:$('#procrestypeid').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $.ajax({
                            type: 'POST',
                            url: '../resources/resourcegrouplist',
                            dataType: "json",
                            data: {resType:$('#procrestypeid').val()},
                            success: function(data){
                                $("#resGroupListContainer").html(data.result);
                            }
                        });
                        $('#resgroupname').val('');
                    }
                    else
                        $('#resgroupname').next("span").html(data.errortext).show('slow');
    
                    $('#saveResGroup').attr("disabled", false);
                }
            });
        }
    });


$(document).on('click','.editresgroup',function(){
        var idval=$(this).attr('data-v');
        $('#resgroupname'+idval).hide();
        $('#editresgroupname'+idval).show();
        $('#editresgroup'+idval).hide();
        $('#saveresgroupbutton'+idval).show();
    });


    $(document).on('click','.saveresgroupbutton',function(){
        var idval=$(this).attr('data-v');
        var error=0;
        $('.error').hide();
        if($('#editresgroupname'+idval).val()==''){
            $('#editresgroupname'+idval).next("span").html('Enter Name').show('slow');
            error=1;
        }

        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../resources/resgroupupdate',
                beforeSend : function(){
                    $('#saveresgroupbutton'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {groupid:idval,groupname:$('#editresgroupname'+idval).val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#editresgroup'+idval).show();
                        $('#resgroupname'+idval).show();

                        $('#editresgroupname'+idval).hide();
                        $('#saveresgroupbutton'+idval).hide();

                        $('#resgroupname'+idval).text($('#editresgroupname'+idval).val());    
                    }
                    else
                    {
                        alert(data.errortext);
                    }
    
                    $('#saveresgroupbutton'+idval).attr("disabled", false);
                }
            });
        }
    });
