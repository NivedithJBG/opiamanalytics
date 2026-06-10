$(function(){ 
    $('#closefundreqappr11').click(function(){
        //alert('sa')
        if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

            //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

        }
    });
});

$(document).on('click','#fundreqradio',function(){

    $('.panel-default').removeClass('active');

    $('.FinanceVerifi-tab').addClass('active');

    $('.account-heads-table-wrpr').hide();

    $.ajax({
        type:"post",
        dataType: "json",
        url:'../financerequests/fundreqdata',
        beforeSend : function(){
                $('.preloader').show();
            },

        data:{},
        success: function(data){
            if(data.error=='No'){
                $('#fin-header').html(data.result);
            }else{
                alert(data.errortext);
            }
              $('.preloader').hide();
        }
    }); 
    

});

$(document).on('click','#addcashbankvouchr',function(){
    $('.addcashbankvouchr').hide();
    $('#displayaddrows').show();
    $('#fin-Approval-listing').hide();

});

$(document).on('click','.CancelVouch',function(){

    $("form").submit(function(e){
        e.preventDefault();
    });
    $('.addcashbankvouchr').show();
    $('#displayaddrows').hide();
    $('#fin-Approval-listing').show();

});

$(document).on('click','.editfinreq',function(){
    var id = $(this).attr("data-id");

    if ($('#ApproveMyfund-'+id).hasClass("active")) 
    {

        $('#ApproveMyfund-'+id).trigger('click');

    }

    $('#editfinreq'+id).hide();
    $('#findatespan'+id).hide();
    $('#finvrnospan'+id).hide();
    $('#finpurpspan'+id).hide();
    $('#finacctheadspan'+id).hide();
    $('#finamntpan'+id).hide();

    $('#savefinreq'+id).show();
    $('#editfindate-'+id).show();
    $('#editfinvrno-'+id).show();
    $('#editfinpurp-'+id).show();
    $('#finaccthead-'+id).show();
    $('#editfinamnt-'+id).show();

});


$(document).on('click','.savefinreq',function(){

    var id = $(this).attr("data-id");

    var findate = $('#editfindate-'+id).val();
    var finvrno = $('#editfinvrno-'+id).val();
    var finpurp = $('#editfinpurp-'+id).val();
    var finaccthead = $('#finaccthead-'+id).val();
    var str = $('#editfinamnt-'+id).val();
    var finamnt = str.replace(',','');
    var amnt = parseInt(finamnt)
    
    

    $.ajax({
        type:"post",
        dataType: "json",
        url:'../financerequests/updatefundrequest',
        data:{id: id,findate:findate,finvrno:finvrno,finpurp:finpurp,finaccthead:finaccthead,finamnt:finamnt},
        success: function(data){
            if(data.error=='No'){

                $('#savefinreq'+id).hide();
                $('#editfindate-'+id).hide();
                $('#editfinvrno-'+id).hide();
                $('#editfinpurp-'+id).hide();
                $('#finaccthead-'+id).hide();
                $('#editfinamnt-'+id).hide();

                $('#editfinreq'+id).show();
                $('#findatespan'+id).show();
                $('#finvrnospan'+id).show();
                $('#finpurpspan'+id).show();
                $('#finacctheadspan'+id).show();
                $('#finamntpan'+id).show();
                $('#findatespan'+id).html(data.date);
                $('#finvrnospan'+id).html(finvrno);
                $('#editfinpurp-'+id).html(finpurp);
                $('#finpurpspan'+id).html(finpurp);
                $('#finacctheadspan'+id).html(data.accheadname);
                $('#finamntpan'+id).html(amnt.toFixed(2));

                if (!$('.ApproveMyfund').hasClass("active")) 
                {
                    var newtots=0;
                    $('.ApproveMyfund').each(function() {
                       
                        var finreqsID = $(this).attr("data-id");
                           
                        var myStrings = $('#finamntpan'+finreqsID).html();
                        var nwtot = myStrings.replace(',','');
                        newtots = parseFloat(newtots) + parseFloat(nwtot);
                        
                    });

                    $('#apptot-'+data.accountid).html(newtots.toFixed(2));

                }
                
            }else{
                alert(data.errortext);
            }
        }
    });


});


$(document).on('click','.ApproveMyfund',function(e){

   
    e.preventDefault();
    var finreqID = $(this).attr("data-id");
    var bankID = $('#ApprovefundBank-'+finreqID).attr("data-id");
    var notival = $('#selectbankapp-'+bankID+' #noti-bank').text();
    $('#ApproveMyfund-'+finreqID).addClass('apprrv');
    $('#ApproveMyfund-'+finreqID).addClass('nonapprrv');

    if(finreqID != ''){
        

        var openblnce = $('#bankrealopenbalance_'+bankID).val();

        var myString = $('#finamntpan'+finreqID).html();
        var finamount = myString.replace(',','');
        
        var totamountString = $('#TotalAmount_'+bankID).html();

        var totamount = totamountString.replace(',','');
          

        if ($('#ApproveMyfund-'+finreqID).hasClass("active")) 
        {

            var str = $('#apptot-'+bankID).html();
            var newtotss = str.replace(',','');
            var tt = 0;
            var nw=0;
            $('.nonapprrv').each(function() {
                   
                    var finreqsID = $(this).attr("data-id");

                    var myStrings = $('#finamntpan'+finreqsID).html();
                    var nwtot = myStrings.replace(',','');
                    tt = parseFloat(tt) + parseFloat(nwtot);
                    
                    nw = parseFloat(newtotss) - parseFloat(tt);
                    
            });
            
            $('#ApproveMyfund-'+finreqID).removeClass('apprrv');
            var newtotal = parseFloat(totamount) - parseFloat(finamount);
            $('#TotalAmount_'+bankID).html(newtotal.toFixed(2));
            $('#realtotalamt_'+bankID).val(newtotal.toFixed(2));
            $('#selectedreqsttotl-'+bankID).html(nw.toFixed(2));

            $('#ApproveMyfund-'+finreqID).removeClass("active");
            $('#ApproveMyfund-'+finreqID).addClass("innactive");

            $('#apprvestatus-'+finreqID).val("0");
        }
        else{
            
            $('#ApproveMyfund-'+finreqID).removeClass('nonapprrv');
            
            var newtots=0;
            $('.apprrv').each(function() {
               
                var finreqsID = $(this).attr("data-id");
                   
                var myStrings = $('#finamntpan'+finreqsID).html();
                var nwtot = myStrings.replace(',','');
                newtots = parseFloat(newtots) + parseFloat(nwtot);
                
        });

            var newtotal = parseFloat(finamount)+parseFloat(totamount);
            $('#TotalAmount_'+bankID).html(newtotal.toFixed(2));
            $('#realtotalamt_'+bankID).val(newtotal.toFixed(2));
            $('#selectedreqsttotl-'+bankID).html(newtots.toFixed(2));

            $('#ApproveMyfund-'+finreqID).removeClass("innactive");
            $('#ApproveMyfund-'+finreqID).addClass("active");

            $('#apprvestatus-'+finreqID).val("1");
        }

        if($('.ApproveMyfund').hasClass("active")){
            $('#apptot-'+bankID).hide();
            $('#selectedreqsttotl-'+bankID).show();

        }else{
            $('#apptot-'+bankID).show();
            $('#selectedreqsttotl-'+bankID).hide();
        }
        var openblnce = $('#bankrealopenbalance_'+bankID).val();

        var apprvdamt = $('#realtotalamt_'+bankID).val();

        var closeblnce1 = parseFloat(openblnce)+ parseFloat(apprvdamt);

        var closeblnce = Math.abs(closeblnce1);

        $('#ClosingBalance_'+bankID).html(closeblnce.toFixed(2));

    }

            $.ajax({
            type:"post",
            dataType: "json",
            url:'../financerequests/approvestatus',
            data:{finreqID:finreqID},
            success: function(data){
                if(data.error=='No'){

                    /*$('#ApproveMyfund-'+finreqID).addClass("active");*/
                }
            }
        });



    /*var r = confirm("Are you sure you want to approve this request ?");

    if(finreqID != '' && r == true){

        var finamount = $('#finamntpan'+finreqID).html();
        var totamount = $('#TotalAmount_'+bankID).html();
        var newtotal = parseFloat(finamount)+parseFloat(totamount);
        $('#TotalAmount_'+bankID).html(newtotal.toFixed(2));

        $.ajax({
            type:"post",
            dataType: "json",
            url:'../financerequests/approvemyfund',
            data:{finreqID: finreqID,status: 1},
            success: function(data){
                if(data.error=='No'){
                    //$('#fin-Approval-body').show();
                    //$('#fin-Approval-content').html(data.resultsearch);ApproveMyfundActive-
                    //alert("Not -----approved");
                }else if(data.error=='Yes'){
                    $('#funaprverow-'+finreqID).remove();
                    $('#selectbankapp-'+bankID+' #noti-bank').text(parseInt(notival)-1);
                    $('#ApproveMyfund-'+finreqID).removeClass("innactive");
                    $('#RejectMyfund-'+finreqID).addClass("innactive");
                    //alert("Approved");
                }
            }
        });

    }  */

});

$(document).on('click','.apprveselectedreqst',function(e){
    e.preventDefault();

   // var r = confirm("Are you sure you want to approve the selected requests ?");

    //if(r == true){

        $.ajax({
            type:"post",
            dataType: "json",
            url:'../financerequests/approvemyfund',
            data:$('#fundrequestacceptform').serialize(),
            success: function(data){
                if(data.error=='No'){

                    var bankID = data.bankid;

                    $('#acctheadid_'+bankID).trigger('click');
                    /*$('#funaprverow-'+finreqID).remove();
                    $('#selectbankapp-'+bankID+' #noti-bank').text(parseInt(notival)-1);
                    $('#ApproveMyfund-'+finreqID).removeClass("innactive");
                    $('#RejectMyfund-'+finreqID).addClass("innactive");*/
                    //alert("Approved");
                }
            }
        });

    //} 

});

$(document).on('click','.RejectMyfund',function(e){
    e.preventDefault();
    var finreqID = $(this).attr("data-id");
    var bankID = $('#RejectMyfund-'+finreqID).attr("data-id");
    var notival = $('#selectbankapp-'+bankID+' #noti-bank').text();
    var idss=$('#ApprovefundBank-'+finreqID).attr('data-id');
    var r = confirm("Are you sure you want to reject this request ?");
    if(finreqID != '' && r == true){

        $.ajax({
            type:"post",
            dataType: "json",
            url:'../financerequests/deleterequest',
            data:{finreqID: finreqID,status: 2},
            success: function(data){
                if(data.error=='No'){

                    if ($('#ApproveMyfund-'+finreqID).hasClass("active")) 
                    {

                        $('#ApproveMyfund-'+finreqID).trigger('click');

                    }
                    
                    $('#funaprverow-'+finreqID).remove();
                    $('#acctheadid_'+idss).trigger('click');
                    $('#selectbankapp-'+bankID+' #noti-bank').text(parseInt(notival)-1);
                    $('#RejectMyfund-'+finreqID).removeClass("innactive");
                    $('#ApproveMyfund-'+finreqID).addClass("innactive");
                    
                    //alert("Rejected");
                }else{

                }
            }
        });

    }    
});

$(document).on('click','.PauseMyfund',function(e){
    e.preventDefault();
    var finreqID = $(this).attr("data-id");
    var bankID = $('#RejectMyfund-'+finreqID).attr("data-id");
    var notival = $('#selectbankapp-'+bankID+' #noti-bank').text();
    //var r = confirm("Are you sure you want to hold this request ?");
    if(finreqID != ''){

        var status = $(this).attr("data-status");

        if(status==1){
            var holdstatus = null;
        }
        else{
            var holdstatus = 1;
        }

        $.ajax({
            type:"post",
            dataType: "json",
            url:'../financerequests/holdmyfund',
            data:{finreqID: finreqID,status: holdstatus},
            success: function(data){
                if(data.error=='No'){
                    //$('#fin-Approval-body').show();
                    //$('#fin-Approval-content').html(data.resultsearch);
                    //alert("Not -----denied");
                }else if(data.error=='Yes'){
                    $('#selectbankapp-'+bankID+' #noti-bank').text(parseInt(notival)-1);
                    if(holdstatus==1){
                        $('#PauseMyfund-'+finreqID).removeClass("innactive");
                        $('#PauseMyfund-'+finreqID).addClass("active");
                        $('#editfinreq'+finreqID).attr("disabled", true);
                        $('#ApproveMyfund-'+finreqID).attr("disabled", true);
                        $('#RejectMyfund-'+finreqID).attr("disabled", true);
                        $('#PauseMyfund-'+finreqID).attr("data-status", holdstatus);
                    }
                    else{
                        $('#PauseMyfund-'+finreqID).removeClass("active");
                        $('#PauseMyfund-'+finreqID).addClass("innactive");
                        $('#editfinreq'+finreqID).attr("disabled", false);
                        $('#ApproveMyfund-'+finreqID).attr("disabled", false);
                        $('#RejectMyfund-'+finreqID).attr("disabled", false);
                        $('#PauseMyfund-'+finreqID).attr("data-status", holdstatus);
                    }
                    
                    //alert("Rejected");
                }
            }
        });

    }    
});

$(document).ready(function(){

    /* Accounthead click Scripts start */
   
    $(document).on('click','#selectbank',function(){
        //e.preventDefault();alert('ff');
        //$('#Financeapproval-tab').trigger('click');
        var idCheck = $(this).attr("data-id");
        var iidCheck = $('#selectbankname_'+idCheck).val();
        $('.acctheadid').removeClass("active prev-tab");
        $('#acctheadid_'+idCheck).addClass('active prev-tab');
        if(idCheck){
            $.ajax({
                type:"post",
                dataType: "json",
                url:'../financerequests/getaddrows',
                data:{aheadid: $(this).attr("data-id"),aheadname: iidCheck},
                success: function(data){
                    if(data.error=='No'){
                        //$('#fin-tab-body').show();
                        //$('.add-fr-cntnr').slideDown( "slow" );
                        $('.account-heads-table-wrpr').show();
                        $('#cashbankvouchadd').html(data.addbutton);
                        $('#displayaddrows').html(data.result);
                        $('#fin-Approval-listing').html(data.finaprve);
                        $('.add-fr-cntnr').slideDown( "slow" );
                    }else{
                        alert(data.errortext);
                    }
                }
            }); 
        }   
    });

    $(document).on('click','.acctheadid',function(){

        //e.preventDefault();alert('ff');
        //$('#Financeapproval-tab').trigger('click');
        var idCheck = $(this).attr("data-id");
        var iidCheck = $('#selectbankname_'+idCheck).val();
        var value=$(this).attr("data-value");
        $('#acnttype').val(value);

       
        $('.acctheadid').removeClass("active prev-tab");
        $('#acctheadid_'+idCheck).addClass('active prev-tab');
        if(idCheck){
            $.ajax({ 
                type:"post",
                dataType: "json",
                url:'../financerequests/getaddrows',
                data:{aheadid: $(this).attr("data-id"),aheadname: iidCheck,acnttype:value},
                success: function(data){
                    if(data.error=='No'){
                        //$('#fin-tab-body').show();
                        //$('.add-fr-cntnr').slideDown( "slow" );
                        $('.account-heads-table-wrpr').show();
                        $('#cashbankvouchadd').html(data.addbutton);
                        $('#displayaddrows').html(data.result);
                        $('#fin-Approval-listing').html(data.finaprve);
                        $('#typevalue').val(value);
                        $('.add-fr-cntnr').slideDown( "slow" );
                        x=1;
                        if(value==1){
                            $('.cash-book-tab').css('pointer-events','');
                            $('.bank-book-tab').css('pointer-events','none');
                        }
                        else{
                            $('.cash-book-tab').css('pointer-events','none');
                            $('.bank-book-tab').css('pointer-events','');
                        }
                        
                    }else{
                        alert(data.errortext);
                    }
                }
            }); 
        }   
    });
    /* Accounthead-click Scripts end */

    /*var max_fields = 10; //maximum input boxes allowed
    var x = 1; //initlal text box count
    $(document).on('click','.add_field_button',function(e){
        e.preventDefault();
        var idCheck = $(this).attr("data-id");
        var iidCheck = $('#selectbankname_'+idCheck).val();
        if(idCheck){
            if(x < max_fields){
                //alert(x +'--'+ iidCheck );
                x++;
                $('#fundreqsaverow123').before('<tr  class="colspanned" id="newtr1'+x+'-'+ idCheck +'"> ' +
                                            '<td class="text-center"><span class="number">'+x+'</span></td>'+
                                            '<td><input class="form-control datepicker" name="Request_Date[]" id="datepicker'+x+'-'+ idCheck +'" value="'.date('d-m-Y').'"><span class="error"></span></td>'+
                                            '<td><input class="form-control" name="voucher_no[]" id="voucher_no'+x+'-'+ idCheck +'" value=""><span class="error"></span></td>'+
                                            '<td><textarea class="form-control fundpurpose" name="fundpurpose[]" placeholder="Purpose" data-id="'+x+'-'+ idCheck +'" id="fundpurpose'+x+'-'+ idCheck +'"></textarea> ' +
                                            ' <span class="error"></span></td> ' + 
                                            '<td>' +
                                                '<select class="form-control fundpaymode-addon" name="fundpaymode[]" id="fundpaymode'+x+'-'+ idCheck +'" data-id="'+x+'-'+ idCheck +'">' +
                                                '<option value="none">Select Payment Mode</option>' +
                                                '<option value="1">Cash</option>' +
                                                '<option value="2">Bank</option>' +
                                                '<option value="3">Contra</option>' +
                                                '</select>' +
                                                '<span class="error"></span>' +
                                            '</td>' +
                                            '<td>' +
                                                '<select class="form-control reqcredit_account" data-id="'+x+'-'+ idCheck +'" name="reqcredit_account[]" id="reqcredit_account'+x+'-'+ idCheck +'">' +
                                                    '<option value="none">Select AccountHead</option>' +
                                                '</select>' +
                                                '<span class="error"></span>' +
                                            '</td>' + 
                                            '<td>' +
                                                '<div class="icon-groups">' +
                                                    '<a class="btn btn-primary  icon-add add_field_button" id="add_field_button'+x+'-'+ idCheck +'" data-id="'+ idCheck +'"></a>'+ 
                                                    '<a class="btn btn-primary icon-remove remove_field" id="remove_field'+x+'-'+ idCheck +'" data-id="'+x+'-'+ idCheck +'"></a>' + 
                                                    '<input type="hidden" id="selectRecord'+x+'-'+ idCheck +'" value="0">'+
                                                    
                                                '</div>' +
                                            '</td>' +
                                        '</tr>'+
                                        '<tr id="newtr2'+x+'-'+ idCheck +'">'+
                                        '<td class="text-center"><span></span></td>'+ 
                                        '<td colspan="2">'+
                                        '<div class="tds-sgst-cgst-igst-wrpr">'+
                                        '<div class="form-groups">'+
                                        '<label>TDS</label>'+
                                        '<input class="form-control fundtdsamount" name="fundtdsamount[]" data-id="'+x+'-'+ idCheck +'" id="fundtdsamount'+x+'-'+ idCheck +'" value="" placeholder="TDS" />'+
                                        '<span class="error"></span>'+
                                        '</div>'+
                                        '<div class="form-groups">'+
                                        '<label>SGST</label>'+
                                        '<input class="form-control fundsgstamount" name="fundsgstamount[]" data-id="'+x+'-'+ idCheck +'" id="fundsgstamount'+x+'-'+ idCheck +'" value="" placeholder="0" />'+
                                        '<span class="error"></span>'+
                                        '</div>'+
                                        '<div class="form-groups">'+
                                        '<label>CGST</label>'+
                                        '<input class="form-control fundcgstamount" readonly name="fundcgstamount[]" data-id="'+x+'-'+ idCheck +'" id="fundcgstamount'+x+'-'+ idCheck +'" value="" placeholder="CGST" />'+
                                        '</div>'+
                                        '<div class="form-groups">'+
                                        '<label>IGST</label>'+
                                        '<input class="form-control fundigstamount" name="fundigstamount[]" data-id="'+x+'-'+ idCheck +'" id="fundigstamount'+x+'-'+ idCheck +'" value="" placeholder="IGST" />'+
                                        '<span class="error"></span>'+
                                        '</div>'+
                                        '</div> <td >'+
                                        '<div class="form-groups">'+
                                        '<label>Amount</label>'+
                                        '<input class="purpose-amount form-control" type="text" name="fundamount[]" data-id="'+x+'-'+ idCheck +'" id="fundamount'+x+'-'+ idCheck +'"  value="" />'+
                                        '<span class="error"></span></div></td>'+
                                        '<td colspan="2"><div class="form-groups">'+
                                        '<label>&nbsp</label>'+
                                        '<input type="hidden" id="fundreq_net'+x+'-'+ idCheck +'" name="fundreq_net[]" value="" />'+
                                        '<input type="hidden" id="funddebtbank'+x+'-'+ idCheck +'" name="funddebtbank[]" value="'+ idCheck +'" />'+
                                        '<span class="myrequest123" id="fundreqnet'+x+'-'+ idCheck +'"></span>'+
                                        '</div></td>'+
                                        '</tr>');
            }
            
        }   
    });*/

    var x = 1;

    $(document).on('click','.add_field_button',function(e){
        e.preventDefault();
        var idCheck = $(this).attr("data-id");
        //var rowno = $(this).attr("data-no");
        var iidCheck = $('#selectbankname_'+idCheck).val();
        x++;
        $.ajax({
            type: 'POST',
            url: '../financerequests/cashbankvoucherrow',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {id:idCheck,rowno:x,aheadname:iidCheck},
            success: function(data){
                if(data.error=='No')
                {
                    $('#fundreqsaverow123').before(data.result);
                    //$('#cashbooktable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    
    /* remove newly added Rows Scripts start */
    $(document).on('click','.remove_field',function(){
        var idRow = $(this).attr("data-id");
        var iit = $('#selectRecord'+idRow).val();
        var r = confirm("Are you sure you want to delete this Request ?");
        if (r == true) {
            if( iit != 0){
                $.ajax({
                    type: 'POST',
                    url: '../financerequests/deleterowss',
                    beforeSend : function(){
                        $('#remove_field'+idRow).attr("disabled", true);
                    },
                    dataType: "json",
                    data: {deleid:iit},
                    success: function(data){
                        if(data.error=='Yes')
                        {
                            $('#newtr1'+idRow).remove();
                            $('#newtr2'+idRow).remove();
                            x--;
                        }else if(data.error=='No') {
                           alert('error occure while delete data');
                        }
                    }
                });
            }
            $('#newtr1'+idRow).remove();
            $('#newtr2'+idRow).remove();
            x--;
        }
        else {
            return false;
        }
    });
    /* remove newly added Rows Scripts end */

    $(document).on('change','.project123',function(e){
        e.preventDefault();
        var idCheckdata = $(this).attr("data-id");
        var selectedProject = $(this).val();
        $('#userrequser'+idCheckdata).attr('disabled', 'disabled');
        $.ajax({
            type:"post",
            dataType: "json",
            url:'../financerequests/getprojectsnames',
            data:{selectedProject: $(this).val()},
            success: function(data){
                if(data.error=='No'){
                    $('#userrequser'+idCheckdata).removeAttr('disabled');
                    $('#userrequser'+idCheckdata).html(data.result);
                }else{
                    //alert(data.errortext);
                }
            }
        });    
    });

    $(document).on('click','.fundpaymode-addon',function(e){ 
        e.preventDefault();
        var idCheckdata = $(this).attr("data-id");
        $.ajax({
                  type:"post",
                  dataType: "json",
                  url:'../financerequests/getdebtsaccounts',
                  data:{selecteddebtsaccounts: idCheckdata },
                  success: function(data){
                      if(data.error=='No'){
                          $('#reqcredit_account'+idCheckdata).html(data.results);
                      }else{
                          alert(data.errortext);
                      }
                  }
              });
    });

    //$(document).on('click','#reqcredit_account',function(e){ alert('inside');
        //e.preventDefault();
        //var idCheckdata = $(this).attr("data-id");
        // $.ajax({
        //     type:"post",
        //     dataType: "json",
        //     url:'../financerequests/getprojectslists',
        //     data:{selectedProjectlist: idCheckdata },
        //     success: function(data){
        //         if(data.error=='No'){
        //             //$('#project'+idCheckdata).removeAttr('disabled');
        //             //$('#project'+idCheckdata).html(data.result);
        //         }else{
        //             //alert(data.errortext);
        //         }
        //     }
        // });  
        //  $.ajax({
        //      type:"post",
        //      dataType: "json",
        //      url:'../financerequests/getdebtsaccounts',
        //      data:{selecteddebtsaccounts: idCheckdata },
        //      success: function(data){
        //          if(data.error=='No'){
        //              //$('#project'+idCheckdata).removeAttr('disabled');
        //              alert(data.results);
        //              $('#reqcredit_account'+idCheckdata).html(data.results);
        //          }else{
        //              alert(data.errortext);
        //          }
        //      }
        //  });  
    //});

    /* Calculation Scripts start */

    //$( "li" ).each(function() {
        //$( this ).addClass( "foo" );
     // });
      //$(document).on('change','.myrequest123',function(){

        //alert('123');
      //});
       

    // $(document).on('blur','.purpose-amount',function(){
    //     var id=$(this).attr('data-id');
    //     var req_amount=$(this).val()*1;
    //     var tds_amount=$('#fundtdsamount'+id).val()*1;
    //     var gst_amount=($('#fundsgstamount'+id).val()*1) *2;
    //     var igst_amount=$('#fundigstamount'+id).val()*1;
    //     var netamount=(req_amount - tds_amount) + (gst_amount + igst_amount);
    //     $('#fundreq_net'+id).val(netamount);
    //     $('#fundreqnet'+id).text(netamount.toFixed(2));
    //     // var btotal = $('#bankrowtotal_'+(id.slice(2))).val();
    //     // if(btotal == 0){
    //     //     var vish = netamount + 0 ;
    //     //     var vimal =$('#bankopenbalance_'+(id.slice(2))).val();
    //     //     $('#TotalAmount_'+(id.slice(2))).text(vish);
    //     //     $('#ClosingBalance_'+(id.slice(2))).text(vimal - vish);
    //     //     $('#fundreq_net'+id).val(netamount);
    //     //     $('#bankrowtotal_'+(id.slice(2))).val(vish);

    //     // }else{
    //     //     var vish = parseInt(netamount) + parseInt(btotal);
    //     //     var vimal =$('#bankopenbalance_'+(id.slice(2))).val();
    //     //     $('#TotalAmount_'+(id.slice(2))).text(vish);
    //     //     $('#ClosingBalance_'+(id.slice(2))).text(vimal - vish);
    //     //     $('#fundreq_net'+id).val(netamount);
    //     //     $('#bankrowtotal_'+(id.slice(2))).val(vish);
    //     // }
    //     // //var vish = parseInt($('#fundreq_net'+id).val());
    //     // //var vimal =$('#bankopenbalance_'+(id.slice(2))).val();
    //     // //$('#TotalAmount_'+(id.slice(2))).text(vish);
    //     // //$('#ClosingBalance_'+(id.slice(2))).text(vimal - vish);
    //     $( ".myrequest123" ).each(function() {
    //         alert($(this).text());
    //     });
    
    // });

    $(document).on('blur','.purpose-amount',function(){
        //var btotal = 0;
        var netamount = 0;
 
        $( ".purpose-amount" ).each(function() {
            var id=$(this).attr('data-id');
            var req_amount=$(this).val()*1;
            var tds_amount=$('#fundtdsamount'+id).val()*1;
            var gst_amount=($('#fundsgstamount'+id).val()*1) *2;
            var igst_amount=$('#fundigstamount'+id).val()*1;
            var netamount1=(req_amount - tds_amount) + (gst_amount + igst_amount);
            netamount += netamount1;
            $('#fundreq_net'+id).val(netamount1);
            $('#fundreqnet'+id).text(netamount1.toFixed(2));
            
         });
 
        var id=$(this).attr('data-id');
        var btotal = $('#bankrowtotal_'+(id.slice(2))).val();
        if(btotal == 0){
            var vish = netamount + 0 ;
            var vimal =$('#bankopenbalance_'+(id.slice(2))).val();
            $('#TotalAmount_'+(id.slice(2))).text(vish);
            $('#ClosingBalance_'+(id.slice(2))).text(vimal - vish);
            //$('#fundreq_net'+id).val(netamount);
            //$('#bankrowtotal_'+(id.slice(2))).val(vish);
 
        }else{
             var vish = parseInt(netamount) + parseInt(btotal);
             var vimal =$('#bankopenbalance_'+(id.slice(2))).val();
             $('#TotalAmount_'+(id.slice(2))).text(vish);
             $('#ClosingBalance_'+(id.slice(2))).text(vimal - vish);
             //$('#fundreq_net'+id).val(netamount);
             //$('#bankrowtotal_'+(id.slice(2))).val(vish);
        }
    
    });
    
    $(document).on('blur','.fundtdsamount',function(){
        var id=$(this).attr('data-id');
        var req_amount=$('#fundamount'+id).val()*1;
        var tds_amount=$(this).val()*1;
        var gst_amount=($('#fundsgstamount'+id).val()*1) *2;
        var igst_amount=$('#fundigstamount'+id).val()*1;
        var netamount=(req_amount - tds_amount) + (gst_amount + igst_amount);
        $('#fundreq_net'+id).val(netamount);
        $('#fundreqnet'+id).text(netamount.toFixed(2));
    
    });
    
    $(document).on('blur','.fundsgstamount',function(){
        var id=$(this).attr('data-id');
        var amount=$(this).val()*1;
        if (amount!=0)
        {
            var req_amount=$('#fundamount'+id).val()*1;
            var tds_amount=$('#fundtdsamount'+id).val()*1;
            var netamount=(req_amount - tds_amount) + (amount + amount);
            $('#fundcgstamount'+id).val(amount);
            $('#fundigstamount'+id).val(0);
            //$('#fundigstamount'+id).attr('disabled', 'disabled');
            $('#fundreq_net'+id).val(netamount);
            $('#fundreqnet'+id).text(netamount.toFixed(2));
            // var btotal = $('#bankrowtotal_'+(id.slice(2))).val();
            // if(btotal == 0){
            //     var vish = netamount + 0 ;
            //     var vimal =$('#bankopenbalance_'+(id.slice(2))).val();
            //     $('#TotalAmount_'+(id.slice(2))).text(vish);
            //     $('#ClosingBalance_'+(id.slice(2))).text(vimal - vish);
            //     $('#fundreq_net'+id).val(netamount);
            //     $('#bankrowtotal_'+(id.slice(2))).val(vish);

            // }else{
            //     var vish = parseInt(netamount) + parseInt(btotal);
            //     var vimal =$('#bankopenbalance_'+(id.slice(2))).val();
            //     $('#TotalAmount_'+(id.slice(2))).text(vish);
            //     $('#ClosingBalance_'+(id.slice(2))).text(vimal - vish);
            //     $('#fundreq_net'+id).val(netamount);
            //     $('#bankrowtotal_'+(id.slice(2))).val(vish);
            // }
        }
    
    });
    
    $(document).on('blur','.fundigstamount',function(){
        var id=$(this).attr('data-id');
        var amount=$(this).val()*1;
        if(amount!=0)
        {   
            var req_amount=$('#fundamount'+id).val()*1;
            var tds_amount=$('#fundtdsamount'+id).val()*1;
            var netamount=(req_amount - tds_amount) + amount;
            $('#fundsgstamount'+id).val(0);
            $('#fundcgstamount'+id).val(0);
            $('#fundreq_net'+id).val(netamount);
            $('#fundreqnet'+id).text(netamount.toFixed(2));
        }
    
    });

    /* Calculation Scripts end */

    /* Add cash-bank voucher start */

    $(document).on('click','.SaveasVouch',function(e){
        e.preventDefault();
        var idSbutton = $(this).attr("data-id");
        var type=$('#typevalue').val();

        var error=0;
        $('.error').hide();
        $('.datepicker').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='')
            {
                $("#datepicker"+idCheck).next("span").html('Select Date').show('slow');
                error=1;
            }
        });

        $('.voucher_no').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='')
            {
                $("#voucher_no"+idCheck).next("span").html('Voucher No').show('slow');
                error=1;
            }
        });

        $('.fundpurpose').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='')
            {
                $("#fundpurpose"+idCheck).next("span").html('Enter Purpose').show('slow');
                error=1;
            }
        });

        $('.reqcredit_account').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='none')
            {
                $("#reqcredit_account"+idCheck).next("span").html('Select Accounthead').show('slow');
                error=1;
            }
        });

        $('.fundamount').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='')
            {
                $("#fundamount"+idCheck).next("span").html('Enter Amount').show('slow');
                error=1;
            }if(type==1){
            if($(this).val()>10000){
                $("#fundamount"+idCheck).next("span").html('Enter up to 10000 only').show('slow');
                error=1;
            }
        }
        });
        $('.vouchersprojects').each(function(){
            var idCheck = $(this).attr("data-id");


            if($(this).val() == 0)
            {

                $("#vouchersprojects"+idCheck).next("span").html('Select Project').show('slow');
                error=1;

            }

        });


        if (error == 0)
        {  
            $.ajax({
                type: 'POST',
                url: '../financerequests/savefundrequest',
                beforeSend : function(){
                    $('#SaveasVouch'+idSbutton).attr("disabled", true);
                },
                data:$('#fundrequestform').serialize(),
                dataType:"json",
                success: function(data){
                    if(data.result=='Yes'){
                        $('#SaveasVouch'+idSbutton).attr("disabled", false);
                        $('#CancelVouch').trigger('click');
                        $('#acctheadid_'+idSbutton).trigger('click');
                        /*$('#displayaddrows').html(data.datarows);
                        $('#fin-Approval-listing').html(data.finaprve);
                        $('.add-fr-cntnr').slideDown( "slow" );*/
                        //$('#closefundreq1').trigger('click');
                        //$('#closefundreqappr').trigger('click');
                        //$('#closefundreq1').trigger('click');
                        //$( "#account_heads_listing" ).load(window.location.href + " #account_heads_listing" );
                        //$('#fin-Veri-tab-content').slideUp( "slow" );
                    }
                }
            });
        }
    
    });

    /* Add cash-bank voucher Scripts end */

    /* Calculation Scripts end */

    /* Save as Draft Scripts start */
    $(document).on('click','.SaveasDraft',function(e){
        e.preventDefault();
        var idSbutton = $(this).attr("data-id");
        var error=0;
        $('.error').hide();
        $('.fundpurpose').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='')
            {
                $("#fundpurpose"+idCheck).next("span").html('Enter Purpose').show('slow');
                error=1;
            }
        });
        $('.purpose-amount').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='')
            {
                $("#fundamount"+idCheck).next("span").html('Enter Amount').show('slow');
                error=1;
            }
        });
        $('.fundpaymode').each(function(){
            var idCheck =$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#fundpaymode"+idCheck).next("span").html('Select Payment Mode').show('slow');
                error=1;
            }
        });
        $('.reqcredit_account').each(function(){
            var idCheck =$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#reqcredit_account"+idCheck).next("span").html('Select AccountHead').show('slow');
                error=1;
            }
        });
        $('.project').each(function(){
            var idCheck =$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#project"+idCheck).next("span").html('Select Project').show('slow');
                error=1;
            }
        });
        $('.userrequser').each(function(){
            var idCheck =$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#userrequser"+idCheck).next("span").html('Select Project').show('slow');
                error=1;
            }
        });

        if (error == 0)
        {  
            $.ajax({
                type: 'POST',
                url: '../financerequests/savefundrequest',
                beforeSend : function(){
                    $('#SaveasDraft'+idSbutton).attr("disabled", true);
                },
                data:$('#fundrequestform').serialize()+"&status=5",
                dataType:"json",
                success: function(data){
                    if(data.result=='Yes'){
                        $('#SaveasDraft'+idSbutton).attr("disabled", false);
                        //$('#closefundreq1').trigger('click');
                        //$('#closefundreqappr').trigger('click');
                        //$('#closefundreq1').trigger('click');
                        //$( "#account_heads_listing" ).load(window.location.href + " #account_heads_listing" );
                        $('#fin-Veri-tab-content').slideUp( "slow" );
                    }
                }
            });
        }
    
    });

    /*$(document).on("click",'.acctheadid',function(){
    var accountid=$(this).attr("data-id");
    var account_type=$(this).attr("data-value");
    $.ajax({
        type: 'POST',
        url: '../financerequests/usercashbankselect',
        dataType:"json",
        data: {accountid:accountid,account_type:account_type},
        success: function(data){
            if(data.error=='No')
            {

                //$('#selectedaccount').html(data.result);

            }
        }
    });
});*/

    /* Save as Draft  Scripts end */

    /* Send for approval Scripts start */
    $(document).on('click','.SendforApprove',function(e){
        e.preventDefault();
        var error=0;
        var idAbutton = $(this).attr("data-id");
        $('.error').hide();
        var idCheck = $(this).attr("data-id");
        $('.fundpurpose').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='')
            {
                $("#fundpurpose"+idCheck).next("span").html('Enter Purpose').show('slow');
                error=1;
            }
        });
        $('.purpose-amount').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='')
            {
                $("#fundamount"+idCheck).next("span").html('Enter Amount').show('slow');
                error=1;
            }
        });
        $('.fundpaymode').each(function(){
            var idCheck =$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#fundpaymode"+idCheck).next("span").html('Select Payment Mode').show('slow');
                error=1;
            }
        });
        $('.reqcredit_account').each(function(){
            var idCheck =$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#reqcredit_account"+idCheck).next("span").html('Select AccountHead').show('slow');
                error=1;
            }
        });
        $('.project').each(function(){
            var idCheck =$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#project"+idCheck).next("span").html('Select Project').show('slow');
                error=1;
            }
        });
        $('.userrequser').each(function(){
            var idCheck =$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#userrequser"+idCheck).next("span").html('Select Project').show('slow');
                error=1;
            }
        });
        //alert(error);
        if (error==0) 
        {
            $.ajax({
                type: 'POST',
                url: '../financerequests/savefundrequest',
               // beforeSend : function(){
                   // $('#SendforApprove'+idAbutton).attr("disabled", true);
                //},
                data:$('#fundrequestform').serialize()+"&status=0",
                dataType:"json",
                success: function(data){
                    if(data.result=='Yes'){
                        //$('#SendforApprove'+idAbutton).attr("disabled", false);                  
                        //$('.add-fr-cntnr').slideUp( "slow" );
                        $('#fin-Veri-tab-content').slideUp( "slow" );
                        $( "#Fin-app-tab-content" ).load(window.location.href + " #Fin-app-tab-content" );
                        $('.acco-one input[type=radio]').slideUp( "slow" );
                       
                    }
                }
            });
            $('#Financeapproval-tab').trigger('click');
        }
    
    });

    /* Send for approval  Scripts end */

});
