$(document).on( "click", "#raise-job-card", function(){
      $('#addjobcard').trigger('click');
    //$('#listappjobcard').trigger('click');
});

/*$(document).on( "click", ".history", function(){
      
       $('#history').trigger('click');
});*/
$(function() {
    $('.history').click(function(){
        $.ajax({
            type: 'POST',
            url: '../jobcard/approvedjobcards',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('.cancel').trigger('click');
                    $('#projectname-jobcard').html(data.projectName);
                    $('#jobcarditems').html(data.result);
                    $('#jobcardtable').show();
                    $('#back').show();

                }
                $('.preloader').hide();
            }
        });
    });
    $('#addjobcard').click(function(){
        $.ajax({
            type: 'POST',
            url: '../jobcard/getjobcards',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#createnew-jobcard').html(data.result);
                    $('#raisedate').html(data.raisejobdate);
                }
                $('.preloader').hide();
            }
        });
    });
    $(document).on('focus','#datejobcard',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });
    $(document).on('click','.tab-wrapper .viewForm',function(){
        $(this).parents('.tab').addClass('edit-form-active');
        $(this).parents('.tab').removeClass('add-form-active');
        var viewid=$(this).attr('data-v');
        $.ajax({
            type: 'POST',
            url: '../jobcard/jobcardview/',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {viewid: viewid},
            success: function(data){
                if(data.error=='No')
                {
                    $('.preloader').hide();
                    $('#view-jobcard').html(data.result);
                    $('#back').hide();
                }
                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();
            }
        });
    });

    $(document).on('click','.deleteappjobcard',function(){
        var jobid=$(this).attr('data-v');
        var r = confirm("Are you sure you want to delete this Job Card ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../jobcard/delete/',
                beforeSend : function(){
                    $('#deleteappjobcard'+jobid).attr("disabled", true);
                },
                dataType: "json",
                data: {jobid:jobid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#jobcardrow'+jobid).remove();
                        $('#listappjobcard').trigger('click');
                    }
                    else
    
                    {
    
                        alert(data.errortext);
    
                    }
    
                    $('#deleteappjobcard'+jobid).attr("disabled", false);
                }
            });
        }
        else {
            return false;
        }
    });
    $(document).on( "change","#jobcardiowlist", function(){
        var iow=$(this).val();
        $.ajax({
            type: 'POST',
            url: '../jobcard/getactivity',
            dataType: "json",
            data: {iow:iow},
            success: function(data){
                if(data.error=='No')
                {
                    $('#jobcardactivity').html(data.result);
                }
            }
        });
    });
    $(document).on( "change","#jobcardactivity", function(){
        var activityid=$(this).val();
        var process='0';
        $.ajax({
            type: 'POST',
            url: '../jobcard/getresources',
            dataType: "json",
            data: {activityid:activityid,process:process},
            success: function(data){
                if(data.error=='No')
                {
                    $('#JobcardResource1').html(data.result);
                    $('#actqty').val(data.actqty);
                    $('#actquantity').html(data.actqty);
                    //$('#actunit').html(data.actunit);
                    $('#actunit').val(data.actunit);

                    //$('#jobcards').val(data.jobcards);
                    $('#exstquantity').html(data.jobcards);
                    $('#propquantity').val('');
                    $('#execquantity').html('');
                    $('#remquantity').html('');
                    /*if (data.jobcards>0){
                        $('#jobcardserror').html('Job card already created against the activity.').show('slow');
                        setTimeout(function () {
                            $('#jobcardserror').hide('slow')
                        }, 5000);
                        $('#jobcardreport').attr("disabled", true);
                    }
                    else {
                        $('#jobcardreport').attr("disabled", false);
                    }*/
                }
            }
        });
    });
    $(document).on( "change","#propquantity", function(){
        var propqty=Number($(this).val());
        var estqty=Number($('#actqty').val());
        var activity=$('#jobcardactivity').val();
        var resgrp=$('#jobcardprocesslist').val();
        if (propqty > estqty){
            document.getElementById("propquantity").style.border = "1px solid #d8272d";
        }
        else {
            document.getElementById("propquantity").style.border = "1px solid #ccc";
        }
    
        $.ajax({
            type: 'POST',
            url: '../jobcard/activityreport',
            dataType: "json",
            data: {activity:activity,resgrp:resgrp},
            success: function(data){
                if(data.error=='No')
                {
                    $('#execquantity').html(data.actqty);
                    //var remqty=estqty - propqty - $('#exstquantity').html();
                    var remqty=estqty - propqty;
                    $('#remquantity').html(remqty);
                }
            }
        });
    });
    $(document).on( "change",".JobcardResource", function(){
        var jobid=$(this).attr('data-id');
        var resid=$(this).val();
        $('#res_id').val(resid);
        var activityid=$('#jobcardactivity').val();
        var iowid=$('#jobcardiowlist').val();
        var projectid=$('#jobcardprojectid').val();
        //var qty=$('#actquantity').html();
        var qty=$('#actqty').val();
        $.ajax({ 
            type: 'POST',
            url: '../jobcard/resdetails',
            dataType: "json",
            data: {resid:resid,activityid:activityid,projectid:projectid,iowid:iowid},
            success: function(data){
                if(data.error=='No')
                {
                    var resqty=data.resqty * qty;
                    var stockqty=data.recresqty - data.issuedqty;
                    $('#Unit'+jobid).val(data.result);
                    $('#editunitplant'+jobid).val(data.result);
                    $('#EstQuantity'+jobid).html(resqty.toFixed(2));
                    $('#EstQty'+jobid).val(resqty.toFixed(2));
                    $('#PropQuantity'+jobid).val('');
                    $('#exstresqty'+jobid).html(data.exstresqty);
                    $('#recresqty'+jobid).html(data.recresqty);
                    $('#issuedresqty'+jobid).html(data.issuedqty);
                    $('#stockresqty'+jobid).html(stockqty);
                    $('#remquantity'+jobid).html('');

                    if(data.restype == 26){
                        $('.editunitbuttons').show();

                    }
                }
            }
        });
    });

    $(document).on( "change",".Quantity", function(){
        var jobid=$(this).attr('data-id');
        var activityid=$('#jobcardactivity').val();
        var resource=$('#JobcardResource'+jobid).val();
        var propqty=Number($(this).val());
        var estqty=Number($('#EstQty'+jobid).val());
    
        if (propqty > estqty){
            document.getElementById("PropQuantity"+jobid).style.border = "1px solid #d8272d";
        }
        else {
            document.getElementById("PropQuantity"+jobid).style.border = "1px solid #ccc";
        }
        $.ajax({
            type: 'POST',
            url: '../jobcard/activityreport',
            dataType: "json",
            data: {activity:activityid,resourceo:resource},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#consquantity'+jobid).html(data.resqty);
                    //var remqty=estqty - propqty - $('#exstresqty'+jobid).html();
                    var remqty=estqty - propqty;
                    $('#remquantity'+jobid).html(remqty.toFixed(2));
                }
            }
        });
    });
    $(document).on('click','.editunitbuttons',function(){
        var jobid=$(this).attr('data-id');

        $('#Unit'+jobid).hide();
        $('.editunitbuttons').hide();
        $('.saveunitbuttons').show();
        $('#editunitplant'+jobid).show();

    });
    $(document).on('click','.saveunitbuttons',function(){
        var jobid=$(this).attr('data-id');
        var resids=$('#res_id').val();
        var unit=$('#editunitplant'+jobid).val();

        $('#Unit'+jobid).show();
        $('.editunitbuttons').show();
        $('.saveunitbuttons').hide();
        $('#editunitplant'+jobid).hide();


        $.ajax({
                type: 'POST',
                url: '../jobcard/resourceunitupdate',
                beforeSend : function(){
                },
                dataType: "json",
                data: {resid:resids,unit:unit},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#Unit'+jobid).val(data.resunit);
                        
                    }
                    
                }
            });


    });
    $(document).on('click','#jobcardreport',function(){
        var error=0;
        $('.error').hide();
       /* if($('#jobcardstructurelist').val()=='none')
        {
            $('#jobcardstructurelist').next("span").html('Select Structure').show('slow');
            error=1;
        }*/
        if($('#jobcardiowlist').val()=='none')
        {
            $('#jobcardiowlist').next("span").html('Select IOW').show('slow');
            error=1;
        }
        if($('#jobcardprocesslist').val()=='none')
        {
            $('#jobcardprocesslist').next("span").html('Select Process').show('slow');
            error=1;
        }
        if($('#jobcardactivity').val()=='none')
        {
            $('#jobcardactivity').next("span").html('Select Activity').show('slow');
            error=1;
        }
        $('.JobcardResource').each(function(){
            var id=$(this).attr('data-id');
            if($('#JobcardResource'+id).val()=='0')
            {
                $('#JobcardResource'+id).next("span").html('Select Resource').show('slow');
                error=1;
            }
        });
        $('.Quantity').each(function(){
            var id=$(this).attr('data-id');
            if(!$.isNumeric($('#PropQuantity'+id).val()))
            {
                $('#PropQuantity'+id).next("span").html('Quantity must be number').show('slow');
                error=1;
            }
            var acqty=$('#actqty').val();
            var resqty=$('#ResQuantity'+id).val();
            var estqty=acqty * resqty;
            var estimateqty=$('#EstQty'+id).val();
            var receivedqty=$('#recresqty'+id).html()*1;
            var propqty=$('#PropQuantity'+id).val()*1;
            var totalqty=propqty + receivedqty;
            //alert(totalqty)
            if(totalqty > estimateqty)
            {
                error=0;
    
            }
            /*if ($('#Quantity'+id).val() > estqty){
                $('#Quantity'+id).next("span").html('Quantity cannot be greater than the estimated quantity').show('slow');
                error=1;
            }*/
        });
    
        var jobcards=$('#jobcards').val();
        if (jobcards > 0){
            $('#jobcardserror').html('Job card already created against the activity.').show('slow');
            //error=1;
        }
        // var from = $('#startdate').val().split("-");
        // var startdate = new Date(from[2], from[1] - 1, from[0]);
        // var to = $('#enddate').val().split("-");
        // var enddate = new Date(to[2], to[1] - 1, to[0]);
    
        // if (startdate > enddate) {
        //     alert('End date cannot be less than Start date');
        //     error=1;
        // }
    
        if(error==0){
            $.ajax({
                type: 'POST',
                url: '../jobcard/reportjobcard',
                beforeSend : function(){
                    $('#jobcardreport').attr("disabled", true);
    
                },
                dataType: "json",
                data: $( "#jobcardform" ).serialize(),
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#jobcardform')[0].reset();
                        //window.location.href = url;
                        //$('#listjobcard').trigger('click') ;
                        $('.cancel').trigger('click');
                        //$('.tab').removeClass('edit-form-active');
                        $('#listappjobcard').trigger('click');
                        $('.jobhead').hide();
                    }
                    $('#jobcardreport').attr("disabled", false);
                }
            });
        }
        else{
            //alert("You have to enter all values for reporting");
            //alert('Sum of Proposed quantity and Received quantity cannot be greater than estimated quantity');
            return  false;
        }
    });

});

    $(document).on('click','#back',function(){

    $('#addjobcard').trigger('click');
    $('#back').hide();
    });