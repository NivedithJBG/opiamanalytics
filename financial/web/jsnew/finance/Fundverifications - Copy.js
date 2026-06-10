$(function(){ 
    $('#closefundreqappr11').click(function(){
        //alert('sa')
        if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

            //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

        }
    });
});

$(document).on('click','.fundreqradio',function(){

    $.ajax({
        type:"post",
        dataType: "json",
        url:'../financerequests/fundreqdata',
        data:{},
        success: function(data){
            if(data.error=='No'){
                $('#fin-header').html(data.result);
            }else{
                alert(data.errortext);
            }
        }
    }); 

});


$(document).ready(function(){

    /* Accounthead click Scripts start */
   
    $(document).on('click','#selectbank',function(){
        //e.preventDefault();alert('ff');
        //$('#Financeapproval-tab').trigger('click');
        var idCheck = $(this).attr("data-id");
        var iidCheck = $('#selectbankname_'+idCheck).val();
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
                        $('#displayaddrows').html(data.result);
                        $('.add-fr-cntnr').slideDown( "slow" );
                    }else{
                        alert(data.errortext);
                    }
                }
            }); 
        }   
    });
    /* Accounthead-click Scripts end */

    var max_fields = 10; //maximum input boxes allowed
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
                                           ' <td><textarea class="form-control fundpurpose" name="fundpurpose[]" placeholder="Purpose" data-id="'+x+'-'+ idCheck +'" id="fundpurpose'+x+'-'+ idCheck +'"></textarea> ' +
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
