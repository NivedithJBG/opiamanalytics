/**
 * Created by SolmindsDelli5 on 09-05-2019.
 */
// $(document).on( "click", "#requestappr", function(){
//     if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
//         $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
//         //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
//     }
//     if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
//         $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
//         $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
//     }
//     $.ajax({
//         type: 'POST',
//         url: '../financerequests/fundsapproval',
//         beforeSend : function(){
//             $('.preloader').show();
//         },
//         dataType: "json",
//         //data: {name:$('#searchrequest').val()},
//         success: function(data){
//             if(data.error=='No')
//             {
//                 $('.preloader').hide();
//                 $('#fundapprtable').html(data.result);
//                 $('#fundapprbtns').show();
//                 //$('#requestappritems').html(data.result);
//                 //$('#requestapprtable').show();
//             }
//         }
//     });
// });



 $(document).on( "click", ".acco-one input[type=radio]", function(){  
        
        if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
        }

        if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
       }

        $.ajax({
        type: 'POST',
        url: '../financerequests/fundsapproval',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        //data: {name:$('#searchrequest').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('.preloader').hide();
                $('#fundapprtable').html(data.funddetails2);
                $('#funddetails').html(data.funddetails);  

                $('#fundapprbtns').show();
                $('#requestappritems').html(data.funddetails2);
                $('#requestapprtable').show();
            }
        }
    });
        
        // $('#listrestype').trigger('click')
            
            
            
        //     $(this).parent('.panel-group').addClass('acco-one-active');
        //     $(this).parent('.panel-group').removeClass('acco-two-active');
        //     $(this).parent('.panel-group').removeClass('acco-three-active');
        //     $(this).parent('.panel-group').removeClass('acco-four-active');
        //     $(this).parent('.panel-group').removeClass('acco-five-active');
        //     $(this).parent('.panel-group').removeClass('acco-six-active');
            
            
        });

















$(function(){
    $('#closefundreqappr').click(function(){
        //alert('sa')
        if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

            //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

        }
    });
});
$(document).on( "change",".appraccountshead", function(){
    var requestid=$(this).attr('data-id');
    var accountid=$(this).val();
    var amount=$('#apprfundamount'+requestid).val();
    var place=$('#place').val();
    $.ajax({
        type: 'POST',
        url: '../FinanceRequests/accounttds',
        dataType:"json",
        data:{accountid:accountid,amount:amount,place:place,requestid:requestid},
        success: function(data){
            if(data.error=='No')
            {
                $('#account_tds'+requestid).text(data.TDS).val();
                $('#tdsamount'+requestid).val(data.TDS);

                $('#ledgerbal'+requestid).text(data.ledgbal);
                $('#ledgbal'+requestid).val(data.Ledgerbal);

                //$('#sgstamount'+requestid).text((data.Tax)/2).val();
                //$('#cgstamount'+requestid).text((data.Tax)/2).val();

                //$('#sgstamountval'+requestid).val((data.Tax)/2);
                //$('#cgstamountval'+requestid).val((data.Tax)/2);


                //$('#igstamount'+requestid).text(data.IgstTax).val();
                //$('#igstamountval'+requestid).text(data.IgstTax).val();
                var gst_amount=($('#sgstamountval'+requestid).val()*1) *2;
                var igst_amount=$('#igstamountval'+requestid).val()*1;
                var netamount=data.Netamount + (gst_amount + igst_amount);
                $('#accountnet'+requestid).text(netamount.toFixed(2));
                $('#account_net'+requestid).val(netamount);
            }

        }
    });
    if (accountid==0){
        $("#approverequest").attr('disabled','disabled');
    }
    else {
        $("#approverequest").removeAttr('disabled');
    }

});

$(document).on('blur','.apprfundamount',function(){
    var id=$(this).attr('data-id');
    var req_amount=$(this).val()*1;
    var tds_amount=$('#tdsamount'+id).val()*1;
    var gst_amount=($('#sgstamountval'+id).val()*1) *2;
    var igst_amount=$('#igstamountval'+id).val()*1;
    var netamount=(req_amount - tds_amount) + (gst_amount + igst_amount);
    $('#account_net'+id).val(netamount);
    $('#accountnet'+id).text(netamount.toFixed(2));

});

$(document).on('blur','.tdsamount',function(){
    var id=$(this).attr('data-id');
    var req_amount=$('#apprfundamount'+id).val()*1;
    var tds_amount=$(this).val()*1;
    var gst_amount=($('#sgstamountval'+id).val()*1) *2;
    var igst_amount=$('#igstamountval'+id).val()*1;
    var netamount=(req_amount - tds_amount) + (gst_amount + igst_amount);
    $('#account_net'+id).val(netamount);
    $('#accountnet'+id).text(netamount.toFixed(2));

});

$(document).on('blur','.sgstamountval',function(){
    var id=$(this).attr('data-id');
    var amount=$(this).val()*1;
    if (amount!=0)
    {
        var req_amount=$('#apprfundamount'+id).val()*1;
        var tds_amount=$('#tdsamount'+id).val()*1;
        var netamount=(req_amount - tds_amount) + (amount + amount);
        $('#cgstamountval'+id).val(amount);
        $('#igstamountval'+id).val(0);
        $('#account_net'+id).val(netamount);
        $('#accountnet'+id).text(netamount.toFixed(2));
    }

});

$(document).on('blur','.igstamountval',function(){
    var id=$(this).attr('data-id');
    var amount=$(this).val()*1;
    if(amount!=0)
    {
        var req_amount=$('#apprfundamount'+id).val()*1;
        var tds_amount=$('#tdsamount'+id).val()*1;
        var netamount=(req_amount - tds_amount) + amount;
        $('#sgstamountval'+id).val(0);
        $('#cgstamountval'+id).val(0);
        $('#account_net'+id).val(netamount);
        $('#accountnet'+id).text(netamount.toFixed(2));
    }

});

$(document).on('change','.fdapracnt',function(){
    var account=$(this).val();
    var project=$(this).attr('data-project');
    var user=$(this).attr('data-id');
    $.ajax({
        type: 'POST',
        url: '../FinanceRequests/Fundopening',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        data:{accountid:account,projectid:project,userid:user},
        dataType:"json",
        success: function(data){
            if(data.error=='No'){
                //$('.preloader').hide();
                $('#requestappritems'+user).html(data.result);
                //$('#openingbalspan'+user).html(data.result.toFixed(2));
                //$('#closingbal'+user).html(data.result.toFixed(2));
                //$('#openingbal'+user).val(data.result.toFixed(2));
            }
        }
    });

});

 
 $(document).on('click','.fdapracnt',function(){ //alert ("hi");
    var account=$(this).val();
    var project=$(this).attr('data-project');
    var user=$(this).attr('data-id');
    $.ajax({
        type: 'POST',
        url: '../financerequests/fundopening',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        data:{accountid:account,projectid:project,userid:user},
        dataType:"json",
        success: function(data){
            if(data.error=='No'){
                //$('.preloader').hide();
                $('#requestappritems'+user).html(data.result);
                //$('#openingbalspan'+user).html(data.result.toFixed(2));
                //$('#closingbal'+user).html(data.result.toFixed(2));
                //$('#openingbal'+user).val(data.result.toFixed(2));
            }
        }
    });

});











$(document).on('change','.requeststatus',function(){
    var userid=$(this).attr('data-user');
    var totalamount=0;
    var purchasetotal=0;
    $('.requeststatus'+userid).each(function(){
        var id=$(this).attr('data-id');
        if($(this).val()==1){
            if($('#fundreqtype'+id).val()==0){
                totalamount+=$('#account_net'+id).val()*1;
            }
            else {
                purchasetotal+=$('#account_net'+id).val()*1;
            }
        }
    });
    var openbal=$('#openingbal'+userid).val()*1;
    var netamount=openbal - totalamount;
    var appramount=totalamount + purchasetotal;
    //alert(netamount)
    if (netamount<=0){
        var closingbal=Math.abs(netamount) + purchasetotal;
    }
    else {
        //var closingbal=openbal - totalamount;
        var closingbal=0;
    }
    //alert(closingbal)
    var closebal=openbal - appramount;
    $('#transferbal'+userid).val(closingbal);
    $('#appramount'+userid).html(appramount.toFixed(2));
    $('#closingbal'+userid).html(closebal.toFixed(2));

});

$(document).on('click','#saveasdraftrequest',function(){
    $.ajax({
        type: 'POST',
        url: '../FinanceRequests/Draftfundrequest',
        beforeSend : function(){
            $('#saveasdraftrequest').attr("disabled", true);
        },
        data:$('#fundreqapprform').serialize(),
        dataType:"json",
        success: function(data){
            if(data.result=='Yes'){
                $('#saveasdraftrequest').attr("disabled", false);
                $('#closefundreqappr').trigger('click') ;
            }
        }
    });

});

$(document).on('click','#approveadvrequest',function(){
    var error=0;
    $('.error').hide();
    $('.requeststatus').each(function(){
        var id=$(this).attr('data-id');
        if($('#rqststatus'+id).val()==1)
        {
            if($('#apprfundpurpose'+id).val()=='')
            {
                $("#apprfundpurpose"+id).next("span").html('Enter Purpose').show('slow');
                error=1;
            }
            if($('#apprpaytype'+id).val()=='none')
            {
                $("#apprpaytype"+id).next("span").html('Select Payment Type').show('slow');
                error=1;
            }
            if($('#apprfundpaymode'+id).val()=='none')
            {
                $("#apprfundpaymode"+id).next("span").html('Select Payment Mode').show('slow');
                error=1;
            }
            if($('#apprfundamount'+id).val()=='')
            {
                $("#apprfundamount"+id).next("span").html('Enter Amount').show('slow');
                error=1;
            }
            if($('#appraccountshead'+id).val()=='none')
            {
                $("#appraccountshead"+id).next("span").html('Select Accounthead').show('slow');
                error=1;
            }
        }
    });
    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/Approvefundrequest',
            beforeSend : function(){
                $('#approveadvrequest').attr("disabled", true);
            },
            data:$('#fundreqapprform').serialize(),
            dataType:"json",
            success: function(data){
                if(data.result=='Yes'){
                    $('#approveadvrequest').attr("disabled", false);
                    $('#closefundreqappr').trigger('click');
                }
            }
        });
    }

});
