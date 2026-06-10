$(document).on( "click", ".viewjobcard", function(){
    $('#rproject').removeClass('active').next().slideUp();
    $('#jobcard').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#jobcardprojectid').val(id);
    $('#selectedProjectId').val(id);
    $('#jobcardprojname').html(getProjectname(id));
    $('#listjobcard').trigger('click') ;
});
$(function(){
    $('#listjobcard').click(function(){
        $('#jobcardaddsection').slideUp('slow');
        $('#jobcardlistsection').slideDown('slow');
        $('#listjobcard').removeClass('btn-danger').addClass('btn-success');
        $('#addjobcard').removeClass('btn-success').addClass('btn-danger');
        $('#listappjobcard').removeClass('btn-success').addClass('btn-danger');
        $('#listcompjobcard').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Jobcard/Jobcardsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#jobcarditems').html(data.result);
                    $('#jobcardtable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#listappjobcard').click(function(){
        $('#jobcardaddsection').slideUp('slow');
        $('#jobcardlistsection').slideDown('slow');
        $('#listappjobcard').removeClass('btn-danger').addClass('btn-success');
        $('#listjobcard').removeClass('btn-success').addClass('btn-danger');
        $('#addjobcard').removeClass('btn-success').addClass('btn-danger');
        $('#listcompjobcard').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Jobcard/ApprovedJobcards',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#jobcarditems').html(data.result);
                    $('#jobcardtable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#listcompjobcard').click(function(){
        $('#jobcardaddsection').slideUp('slow');
        $('#jobcardlistsection').slideDown('slow');
        $('#listcompjobcard').removeClass('btn-danger').addClass('btn-success');
        $('#listjobcard').removeClass('btn-success').addClass('btn-danger');
        $('#addjobcard').removeClass('btn-success').addClass('btn-danger');
        $('#listappjobcard').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Jobcard/CompletedJobcards',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#jobcarditems').html(data.result);
                    $('#jobcardtable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#addjobcard').click(function(){
        $('#jobcardlistsection').slideUp('slow');
        $('#jobcardaddsection').slideDown('slow');
        $('#addjobcard').removeClass('btn-danger').addClass('btn-success');
        $('#listjobcard').removeClass('btn-success').addClass('btn-danger');
        $('#listappjobcard').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            //url: '../Jobcard/getstructure',
            url: '../Jobcard/getiow',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#jobcardstructurelist').html(data.result);
                    $('#jobcardiowlist').html(data.result);
                }
                $('.preloader').hide();
            }
        });
    });
});
   $(document).on( "change","#jobcardstructurelist", function(){
        var structureid=$(this).val();
        $.ajax({
            type: 'POST',
            url: '../Jobcard/getiow',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val(), structureid:structureid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#jobcardiowlist').html(data.result);
                }
                $('.preloader').hide();
            }
        });
    });
//}); 
$(document).on( "change","#jobcardiowlist", function(){
    var iow=$(this).val();
    var projectid=$('#selectedProjectId').val();
    $.ajax({
        type: 'POST',
        url: '../Jobcard/Getactivity',
        dataType: "json",
        data: {iow:iow,projectid:projectid},
        success: function(data){
            if(data.error=='No')
            {
                $('#jobcardactivity').html(data.result);
                $('#estqty').val(data.quantity);
            }
        }
    });
});
/*$(document).on( "change","#jobcardprocesslist", function(){
    var process=$(this).val();
    var projectid=$('#selectedProjectId').val();
    var wbsid=$('#jobcardiowlist').val();
    $.ajax({
        type: 'POST',
        url: '../Jobcard/Getactivity',
        dataType: "json",
        data: {process:process,projectid:projectid,wbsid:wbsid},
        success: function(data){
            if(data.error=='No')
            {
                $('#jobcardactivity').html(data.result);
                $('#estqty').val(data.quantity);
            }
        }
    });
});*/
    $(document).on( "change","#jobcardactivity", function(){
        var activityid=$(this).val();
        var process=$('#jobcardprocesslist').val();
        $.ajax({
            type: 'POST',
            url: '../Jobcard/Getresources',
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
        url: '../Jobcard/ActivityReport',
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
    var activityid=$('#jobcardactivity').val();
    var iowid=$('#jobcardiowlist').val();
    var projectid=$('#jobcardprojectid').val();
    //var qty=$('#actquantity').html();
    var qty=$('#actqty').val();
    $.ajax({
        type: 'POST',
        url: '../Jobcard/Resdetails',
        dataType: "json",
        data: {resid:resid,activityid:activityid,projectid:projectid,iowid:iowid},
        success: function(data){
            if(data.error=='No')
            {
                var resqty=data.resqty * qty;
                var stockqty=data.recresqty - data.issuedqty;
                $('#Unit'+jobid).val(data.result);
                $('#EstQuantity'+jobid).html(resqty.toFixed(2));
                $('#EstQty'+jobid).val(resqty.toFixed(2));
                $('#PropQuantity'+jobid).val('');
                $('#exstresqty'+jobid).html(data.exstresqty);
                $('#recresqty'+jobid).html(data.recresqty);
                $('#issuedresqty'+jobid).html(data.issuedqty);
                $('#stockresqty'+jobid).html(stockqty);
                $('#remquantity'+jobid).html('');
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
        url: '../Jobcard/ActivityReport',
        dataType: "json",
        data: {activity:activityid,resource:resource},
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
            url: '../Jobcard/ReportJobcard',
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
                    $('#listjobcard').trigger('click') ;
                }

                $('#jobcardreport').attr("disabled", false);
            }
        });
    }
    else{
        //alert("You have to enter all values for reporting");
        alert('Sum of Proposed quantity and Received quantity cannot be greater than estimated quantity');
        return  false;
    }
});
$(document).on('click','.deletejobcardbutton',function(){
    var jobid=$(this).val();
    var r = confirm("Are you sure you want to delete this Job Card ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../Jobcard/Delete/',
            beforeSend : function(){
                $('#deletejobcardbutton'+jobid).attr("disabled", true);
            },
            dataType: "json",
            data: {jobid:jobid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#jobcardrow'+jobid).remove();
                    $('#listjobcard').trigger('click');
                }

                $('#deletejobcardbutton'+jobid).attr("disabled", false);
            }
        });
    }
    else {
        return false;
    }
});
$(document).on('click','.deleteappjobcard',function(){
    var jobid=$(this).val();
    var r = confirm("Are you sure you want to delete this Job Card ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../Jobcard/Delete/',
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

$(document).on('click','.completed',function(){
    var jobid=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../Jobcard/Complete/',
        beforeSend : function(){
            $('#completed'+jobid).attr("disabled", true);
        },
        dataType: "json",
        data: {jobid:jobid},
        success: function(data){
            if(data.error=='No')
            {
                $('#listappjobcard').trigger('click');
            }
        }
    });
});

$(document).on('click','#resourcesearch',function(){
    var activityid=$('#activityid').val();
    var resname=$('#resourcename').val();
    var notinres=$('#notinres').val();
    $.ajax({
        type: 'POST',
        url: '../../Jobcard/ActivityResources/',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {activityid:activityid,resname:resname,notinres:notinres},
        success: function(data){
            if(data.error=='No')
            {
                $('#resourceitems').html(data.result);
                $('#resourcetable').show();
            }
            $('.preloader').hide();
        }
    });
});

$(document).on( "change",".ResQuantity", function(){
    var resid=$(this).attr('data-id');
    var propqty=Number($(this).val());
    var estqty=Number($('#EstresQty'+resid).val());
    var remqty=estqty - propqty;
    $('#remresquantity'+resid).html(remqty.toFixed(2));
});

$(document).on('click','.addresource',function(){
    var id=$(this).val();
    var groupid=$('#groupid').val();
    var quantity=$('#PropQuantity'+id).val();
    var resunit=$('#resunit'+id).val();
    var error=0;
    if($('#PropQuantity'+id).val()=='' || $('#PropQuantity'+id).val()==0)
    {
        $('#PropQuantity'+id).next("span").html('Enter Valid Quantity').show('slow');
        error=1;
    }
    if(!$.isNumeric($('#PropQuantity'+id).val()))
    {
        $('#PropQuantity'+id).next("span").html('Enter Valid Quantity').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../../Jobcard/UpdateJobcard/',
            beforeSend : function(){
                $('#addresource'+id).attr("disabled", true);
            },
            dataType: "json",
            data: {resourceid:id,groupid:groupid,quantity:quantity,resunit:resunit},
            success: function(data){
                if(data.error=='No')
                {
                    $('#addedresources').html(data.result);
                    $('#resourcerow'+id).remove();
                }
                $('#addresource'+id).attr("disabled", false);
            }
        });
    }

});

$(document).on('click','.removeresitem',function(){
    var jobcardid=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../../Jobcard/RemoveJobcard',
        beforeSend : function(){
            $('#removeresitem'+jobcardid).attr("disabled", true);
        },
        dataType: "json",
        data: {jobcardid:jobcardid,groupid:$('#groupid').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#jobcardrow'+jobcardid).remove();
                $('#notinres').val(data.notinres);
            }
            else {
                if (data.cartcount!=0){
                    alert('Please remove from cart and try again')
                }
                if (data.ordercount!=0){
                    alert('Please cancel the order and try again')
                }
            }
            $('#removeresitem'+jobcardid).attr("disabled", false);
        }
    });
});

/*$(document).on('change','.propqty',function(){
    var jobcardid=$(this).attr('data-id');
    var resourceid=$(this).attr('data-item');
    var newqty=$(this).val();
    var oldqty=$('#currentresqty'+jobcardid).val();
    var ordercount=0;
    if (parseFloat(newqty) < parseFloat(oldqty)){
        $.ajax({
            type: 'POST',
            url: '../../Jobcard/Checkorders',
            dataType: "json",
            data: {jobcardid:jobcardid,resourceid:resourceid},
            success: function(data){
                if(data.count!=0)
                {
                    //alert('Please cancel order')
                    ordercount ++;
                    $('#ordercount').val(ordercount);
                    //$(':input[type="submit"]').prop('disabled', true);
                }
                else {
                    $('#ordercount').val(0);
                }
            }
        });
    }

});*/

/*$(document).on('click','#updatejobcard',function(){
    var error=0;
    $('.error').hide();
    var ordercount=0;
    $('.propqty').each(function(){
        var jobcardid=$(this).attr('data-id');
        var resourceid=$(this).attr('data-item');
        var newqty=$(this).val();
        var oldqty=$('#currentresqty'+jobcardid).val();

        if (parseFloat(newqty) < parseFloat(oldqty)){
            $.ajax({
                type: 'POST',
                url: '../../Jobcard/Checkorders',
                dataType: "json",
                data: {jobcardid:jobcardid,resourceid:resourceid},
                async: false,
                success: function(data){
                    if(data.count!=0)
                    {
                        //alert('Please cancel order')
                        if (data.cartcount!=0){
                            $('#propqty'+jobcardid).next("span").html('Item added to cart. Please remove and try again.').show('slow');
                            error=1;
                        }
                        if (data.ordercount!=0){
                            $('#propqty'+jobcardid).next("span").html('Item associated with an order. Please cancel and try again.').show('slow');
                            error=1;
                        }
                        ordercount++;

                        //$(':input[type="submit"]').prop('disabled', true);
                    }
                }
            });
        }

    });
    $('#ordercount').val(ordercount);
    var order=$('#ordercount').val();
    if (order > 0){
        return false;
    }
    else {
        return true;
    }


});*/

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