$(document).on( "click", "#clientbill", function(){

    $('#boq_list').trigger('click');
    $("#pgrsrpt").css("display", "none");

});

$(document).on( "click", "#boq_list", function(){
    $.ajax({
        type: 'POST',
        url: '../report/boqsearch',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                //$('#boqslno').html(data.result);
                $('#projectname-CB').html(data.projectName);
                $('#projectnameBoq').show();
                $('#boqlistsection').show();
                $('#boq-list-body').html(data.result);
                $("#pgrsrpt").css("display", "none");
                // $("#cntbill").css("display", "block");
                $("#cllist").css("display", "block");
                $("#billhis").css("display", "none");
                $("#rbll").css("display", "none");
                


            }
            $('.preloader').hide();
        }
    });
});
$(document).on( "click", "#bill_list", function(){
    $.ajax({
        type: 'POST',
        url: '../report/clientbillhistory',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                //$('#boqslno').html(data.result);
                $('#billitems').html(data.result);
                $("#cllist").css("display", "none");
                $("#billhis").css("display", "block");
                $("#rbll").css("display", "none");
                $('#vcbill').css("display", "none");
                


            }
            $('.preloader').hide();
        }
    });
});
$(document).on( "click", "#raise_bill", function(){

    $.ajax({

        type: 'POST',

        url: '../report/boqraisebill',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        success: function(data){

            if(data.error=='No')

            {
                $('#raisebillitems123').html(data.result);
                $("#billhis").css("display", "none");
                $("#cllist").css("display", "none");
                $("#rbll").css("display", "block");
               // $("#vcbill").css("display", "block");
                

            }

            $('.preloader').hide();

        }

    });
});

$(document).on('change','.current_qty',function(){

    var boqid = $(this).attr('data-id');
    var rate = $('#editratename'+boqid).val();
    var cqty=$(this).val();
    var camount=cqty * rate;
    $('#current_amt'+boqid).html(camount.toFixed(2));
    var qtyuptolast2 = $('#qty_uptolastbill'+boqid).html();
    var qtyuptolast1 = parseFloat(qtyuptolast2.replace(/,/g, ''));
    if(qtyuptolast2!=''){
        var qtyuptolast = qtyuptolast1;
    }
    else{
        var qtyuptolast = 0;
    }
    var totqty = parseFloat(qtyuptolast) + parseFloat(cqty);
    $('#tot_qty'+boqid).html(totqty.toFixed(2));
    var amtuptolast2 = $('#amt_uptolastbill'+boqid).html();
    var amtuptolast1 = parseFloat(amtuptolast2.replace(/,/g, ''));
    if(amtuptolast2!=''){
        var amtuptolast = amtuptolast1;
    }
    else{
        var amtuptolast = 0;
    }
    var totamt = parseFloat(amtuptolast) + parseFloat(camount);
    $('#tot_amt'+boqid).html(totamt.toFixed(2));
    //var totalqty=parseFloat(cqty) + parseFloat(uqty);

});

$(document).on('change','.clientbillno',function(){
    var billno = $('#clientbillno').val();
    if(billno!=''){
        $.ajax({
            type: 'POST',
            url: '../report/billnumbercheck',
            dataType: "json",
            data: {billno:billno},
            success: function(data){
                if(data.exist == 'Yes')
                {
                    $('#clientbillno').next("span").html(billno+' already exists').show('slow').delay(3000).fadeOut();
                    $('#clientbillno').val('');
                }
            }
        });
    }
}); 

$(document).on('click','#viewCientBills',function(){ 
    var billid=$(this).attr("data-v");
    $.ajax({
        type: 'POST',
        url: '../report/viewclientbill',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {billid:billid},
        success: function(data){
            if(data.error=='No')
            {
                //$('#boqslno').html(data.result);
                $('#View-Client-Bill').html(data.result);
                $("#vcbill").css("display", "block");
                $("#billhis").css("display", "none");
                

            }
            $('.preloader').hide();
        }
    });

});

$(document).on('click','#savebillnew',function(){

    var error=0;
    $('.error').hide();

    var billdate = $('#clientbilldate').val();
    var billno = $('#clientbillno').val();
    if(billdate == ''){
        $('#clientbilldate').next("span").html('Select Date').show('slow').delay(3000).fadeOut();
        error=1;
        $('html, body').animate({scrollTop:$('#clientbill').position().top}, 'slow');
    }
    if(billno == ''){
        $('#clientbillno').next("span").html('Enter BillNo').show('slow').delay(3000).fadeOut();
        error=1;
        $('html, body').animate({scrollTop:$('#clientbill').position().top}, 'slow');
    }

    if(error==0)
    { 
    
        $.ajax({
            type: 'POST',
            url: '../report/clientbillsaving',
            dataType: "json",
            async:false,
            data: $('#clientbillform').serialize(),
            success: function(data){
                if(data.error == 'No')
                {
                    $('.cancel').trigger('click');
                    $('#bill_list').trigger('click');
                }
            }
        });

    }

});

$(document).on( "click", "#editbill", function(){
    $('.editbill').hide();
    $('.saveeditbill').show();
    $('.curqty').hide();
    $('.edit_current_qty').show();
});

$(document).on('change','.edit_current_qty',function(){

    var billid = $(this).attr('data-id');
    var rate1 = $('#editrate'+billid).html(); 
    var rate = parseFloat(rate1.replace(/,/g, ''));
    var cqty=$(this).val();
    var currentqty = parseFloat(cqty);
    $('#curqty'+billid).html(currentqty.toFixed(2));
    var camount=cqty * rate;
    $('#edit_current_amt'+billid).html(camount.toFixed(2));
    var qtyuptolast2 = $('#editqty_uptolastbill'+billid).html();
    var qtyuptolast1 = parseFloat(qtyuptolast2.replace(/,/g, ''));
    if(qtyuptolast1!=''){
        var qtyuptolast = qtyuptolast1;
    }
    else{
        var qtyuptolast = 0;
    }
    var totqty = parseFloat(qtyuptolast) + parseFloat(cqty);
    $('#edittot_qty'+billid).html(totqty.toFixed(2));
    var amtuptolast2 = $('#editamt_uptolastbill'+billid).html();
    var amtuptolast1 = parseFloat(amtuptolast2.replace(/,/g, ''));
    if(amtuptolast1!=''){
        var amtuptolast = amtuptolast1;
    }
    else{
        var amtuptolast = 0;
    }
    var totamt = parseFloat(amtuptolast) + parseFloat(camount);
    $('#edittot_amt'+billid).html(totamt.toFixed(2));

    var currentsum = 0;
    $('.edit_current_amt').each(function() {
        var currentsum1 = $(this).html();
        currentsum += parseFloat(currentsum1.replace(/,/g, ''));
    });
    $('#billtotal').html(currentsum.toFixed(2));

    var grosssum = 0;
    $('.edittot_amt').each(function() {
        var grosssum1 = $(this).html();
        grosssum += parseFloat(grosssum1.replace(/,/g, ''));
    });
    $('#billgrosstotal').html(grosssum.toFixed(2));

});

$(document).on('click','#saveeditbill',function(){

    $.ajax({
        type: 'POST',
        url: '../report/clientbillupdate',
        dataType: "json",
        async:false,
        data: $('#client_billformedit').serialize(),
        success: function(data){
            if(data.error == 'No')
            {
                //$('#bill_list').trigger('click');
                // location.reload();          
                $('.saveeditbill').hide();
                $('.edit_current_qty').hide();
                $('.editbill').show();
                $('.curqty').show();
                
            }
        }
    });

});