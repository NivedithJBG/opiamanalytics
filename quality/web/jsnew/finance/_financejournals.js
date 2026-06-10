
$(document).on( "click", "#finjouradio", function(){  

    
    $('#JcancelB').trigger('click');
    
        
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
   }
   if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
       
   }

    $('#jresourcetypesearch').trigger('click');

  
  // $(this).parents('.panel-group').addClass('acco-four-active');
   $(this).parents('.panel-group').removeClass('acco-one-active');
   //$(this).parents('.panel-group').removeClass('acco-two-active');
   //$(this).parents('.panel-group').removeClass('acco-three-active');
  // $(this).parents('.panel-group').removeClass('acco-five-active');
   //$(this).parents('.panel-group').removeClass('acco-six-active');


});

$(document).on( "click", ".finjournladd", function(){  

    $.ajax({
        type:"post",
        dataType: "json",
        url:'../financerequests/finjornldata',
        data:{},
        success: function(data){
            if(data.error=='No'){
                $('#journalform').html(data.result);
            }else{
                alert(data.errortext);
            }
        }
    });

});  
    $(document).on('change','.debitamount',function(){
       

            var amnt = 0;
            $('.debitamount').each(function() {

                // new code start - dec 21-2022

                var amtvalue = parseFloat($('.creditamount').val());
                if(isNaN(amtvalue)) {
                  amtvalue = 0;
                  //alert (value);
                }

                // new code end - dec 21-2022
                
            var debt_amnt = $(this).val();
            if(debt_amnt){

                // old code start 
            amnt = parseFloat(amnt) + parseFloat(debt_amnt);

            // old code end 


            // new code start - dec 21-2022
          //amnt = amtvalue;
            // new code end - dec 21-2022

        }
           
            $('.creditamount').val(amnt.toFixed(2));
          
            
            
        }); 
        
        


    });
     
      $(document).on('change','.creditamount',function(){
       

            var amntt = 0;
            $('.creditamount').each(function() {


            var credt_amnt = $(this).val();
            if(credt_amnt){
            amntt = parseFloat(amntt) + parseFloat(credt_amnt);
        }   
        $('.debitamount').val(amntt.toFixed(2));
            
            
            
        }); 
        
        


    });
    

$(document).on('click','#jresourcetypesearch',function(){

    var jsearch = $('#jsearchrestypename').val();
    //alert(jsearch);
    $.ajax({
        type: 'POST',
        url: '../financerequests/journalsearch',
        beforeSend : function(){
            $('#fin-preloader-jtab').show();
        },
        dataType: "json",
        data: {jsearch: jsearch,},
        success: function(data){
            if(data.error=='No')
            {
                //$('#fin-preloader-jtab').hide();
                $('#j-body').html(data.result);
                //$('#journeltable').show();
            }
            else
            {
                alert(data.errortext);
            }
            $('#fin-preloader-jtab').hide();
        }
    });
});


$(document).on('focus','#datepicker',function(){
    $(this).datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true
    });
});

$(function(){


   $(document).on('click','#savejournal',function(){ 


    //$('#savejournal').click(function(){

        var error=0;
        $('.error').hide();
        $('#Narration').each(function(){
            if($('#Narration').val()=='')
            {
                $("#Narration").next("span").html('Enter Narration').show('slow');
                error=1;
            }
        });
        if($('#creditaccount').val()=='0')
        {
            $("#creditaccount").next("span").html('Select Credit Account').show('slow');
            error=1;
        }
        if($('#jplace').val()=='0')
        {
            $("#jplace").next("span").html('Select Place').show('slow');
            error=1;
        }
        if($('#projectid').val()=='0')
        {
            $("#projectid").next("span").html('Select Project').show('slow');
            error=1;
        }
        var debitotal=0;
        var creditotal=0;
        var debtot=0;
        $('.debitamount').each(function(){
            var id=$(this).attr('data-id');
            debitotal=debitotal+$(this).val()*1;
            debtot=debitotal.toFixed(2);
            if($(this).val()=='')
            {
                $("#debitamount"+id).next("span").html('Enter Debit Amount').show('slow');
                error=1;
            }
        });
        
        $('.creditamount').each(function(){
            var id=$(this).attr('data-id');
            creditotal=creditotal+$(this).val()*1;
            credtotal = creditotal.toFixed(2);
            if($(this).val()=='')
            {
                $("#creditamount"+id).next("span").html('Enter Credit Amount').show('slow');
                error=1;
            }
        });
        
        
        if (debtot!=credtotal)
        {
            alert("Debit amount and credit amount must be same");
            error=1;
        }

        $('.debitaccount').each(function(){

            var id=$(this).attr('data-id');

            var creditaccount=$("#creditaccount").val();

            if($(this).val()==creditaccount)
            {

                $("#debitaccount"+id).next("span").html('Cannot select same account head as credit   ').show('slow');
                error=1;
            }
        });

        $('.debitaccount').each(function(){

            var id=$(this).attr('data-id');

            var debitaccount=$("#debitaccount"+id).val();

            if(debitaccount==0)
            {

                $("#debitaccount"+id).next("span").html('Select Debit Account').show('slow');
                error=1;
            }
        });

        $('.creditaccount').each(function(){

            var id=$(this).attr('data-id');

            var creditaccount=$("#creditaccount"+id).val();

            if(creditaccount==0)
            {

                $("#creditaccount"+id).next("span").html('Select Credit Account').show('slow');
                error=1;
            }
        });

        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../financerequests/journals',
                beforeSend : function(){
                    $('#savejournal').attr("disabled", true);
                },
                data:$('#journalform').serialize(),
                dataType:"json",
                success: function(data){
                    if(data.error=='Yes'){
                        $('#journalform')[0].reset();
                        $('#savejournal').css({"backgroundColor": "green", "color": "white","border-radius": "25px","border-color":"green"});
                        $('.journal-tab').removeClass('addJournalForm-active');
                        $('#jresourcetypesearch').trigger('click');
                       
                    }
                }
            });
        }
        else
        {
            return false;
        }

    });

    //$('#saveandcreate').click(function(){
    $(document).on('click','#saveandcreate',function(){
        var error=0;
        $('.error').hide();
        $('#Narration').each(function(){
            if($('#Narration').val()=='')
            {
                $("#Narration").next("span").html('Enter Narration').show('slow');
                error=1;
            }
        });
        if($('#creditaccount').val()=='0')
        {
            $("#creditaccount").next("span").html('Select Credit Account').show('slow');
            error=1;
        }
        if($('#jplace').val()=='0')
        {
            $("#jplace").next("span").html('Select Place').show('slow');
            error=1;
        }
        if($('#projectid').val()=='0')
        {
            $("#projectid").next("span").html('Select Project').show('slow');
            error=1;
        }
        var debitotal=0;
        var creditotal=0;
        $('.debitamount').each(function(){
            var id=$(this).attr('data-id');
            debitotal=debitotal+$(this).val()*1;
            if($(this).val()=='')
            {
                $("#debitamount"+id).next("span").html('Enter Debit Amount').show('slow');
                error=1;
            }
        });
        
        $('.creditamount').each(function(){
            var id=$(this).attr('data-id');
            creditotal=creditotal+$(this).val()*1;
            if($(this).val()=='')
            {
                $("#creditamount"+id).next("span").html('Enter Credit Amount').show('slow');
                error=1;
            }
        });
        if (debitotal!=creditotal)
        {
            alert("Debit amount and credit amount must be same");
            error=1;
        }

        $('.debitaccount').each(function(){

            var id=$(this).attr('data-id');

            var creditaccount=$("#creditaccount").val();

            if($(this).val()==creditaccount)
            {

                $("#debitaccount"+id).next("span").html('Cannot select same account head as credit   ').show('slow');
                error=1;
            }
        });

        $('.debitaccount').each(function(){

            var id=$(this).attr('data-id');

            var debitaccount=$("#debitaccount"+id).val();

            if(debitaccount==0)
            {

                $("#debitaccount"+id).next("span").html('Select Debit Account').show('slow');
                error=1;
            }
        });

        $('.creditaccount').each(function(){

            var id=$(this).attr('data-id');

            var creditaccount=$("#creditaccount"+id).val();

            if(creditaccount==0)
            {

                $("#creditaccount"+id).next("span").html('Select Credit Account').show('slow');
                error=1;
            }
        });

        if(error==0)
        {  
            $.ajax({
                type: 'POST',
                url: '../financerequests/journals',
                data:$('#journalform').serialize(),
                dataType:"json",
                success: function(data){
                    if(data.error=='Yes'){
                        $('#journalform')[0].reset(); 
                        $('.journal-tab').removeClass('addJournalForm-active');
                        $('#jresourcetypesearch').trigger('click');
                    }
                }
            });
        }
        else
        {
            return false;
        }

    });

    $(document).on('click','#JcancelB',function(){
        $('.journal-tab').removeClass('addJournalForm-active');
        $('#jresourcetypesearch').trigger('click');
    });

$(document).on('click','.ApproveMyjounarl',function(e){
    e.preventDefault();

     var journID = $(this).attr("data-id");
     $('#finjournalid').val(journID);

     if(journID!='')
     {
        if ($('#ApproveMyjounarl-'+journID).hasClass("active")) {

            $('#ApproveMyjounarl-'+journID).removeClass("active");
            $('#ApproveMyjounarl-'+journID).addClass("innactive");
        }else{
            $('#ApproveMyjounarl-'+journID).removeClass("innactive");
            $('#ApproveMyjounarl-'+journID).addClass("active");

        }
     }


});




    $(document).on('click','.apprveselectedjournal',function(e){
        e.preventDefault();
        var finreqID = $('#finjournalid').val();
        if(finreqID != ''){

            $.ajax({
                type:"post",
                dataType: "json",
                url:'../financerequests/journalapprove',
                data:{journalid: finreqID,journalstatus: 1},
                success: function(data){
                    if(data.error=='No'){
                        
                        
                        //$('#fin-Approval-body').show();
                        //$('#fin-Approval-content').html(data.resultsearch);ApproveMyfundActive-
                        //alert("Not -----approved");
                    }else if(data.error=='Yes'){
                        $('#jresourcetypesearch').trigger('click');
                        $('#ApproveMyjounarl-'+finreqID).removeClass("innactive");
                        $('#Rejectjounarl-'+finreqID).addClass("innactive");
                        //alert("Approved");
                    }
                }
            });

        }    
    });
    $(document).on('click','.Rejectjounarl',function(e){
        e.preventDefault();
        var finreqID = $(this).attr("data-id");
        if(finreqID != ''){

            if(confirm('Are you sure you want to reject this journal entry?'))
            {
                $.ajax({
                    type:"post",
                    dataType: "json",
                    url:'../financerequests/journalapprove',
                    data:{journalid: finreqID,journalstatus: 2},
                    success: function(data){
                        if(data.error=='No'){
                            //$('#fin-Approval-body').show();
                            //$('#fin-Approval-content').html(data.resultsearch);
                            //alert("Not -----denied");
                        }else if(data.error=='Yes'){
                            $('#Rejectjounarl-'+finreqID).removeClass("innactive");
                            $('#ApproveMyjounarl-'+finreqID).addClass("innactive");
                            $('#jresourcetypesearch').trigger('click');
                            //alert("Rejected");
                        }
                    }
                });

            }
            else
            {
               return false;
            }

        }    
    });

    $(document).on('click','.delete-journal',function(e){
        e.preventDefault();
        var finreqID = $(this).attr("data-id");
        if(finreqID != ''){

            if(confirm('Are you sure you want to delete this journal entry?'))
            {

                $.ajax({
                    type:"post",
                    dataType: "json",
                    url:'../financerequests/journalapprove',
                    data:{journalid: finreqID,journalstatus: 4},
                    success: function(data){
                        if(data.error=='No'){
                            
                        }else if(data.error=='Yes'){
                            //$('#delete-journal-'+finreqID).removeClass("innactive");
                            $('#delete-journal-'+finreqID).attr("disabled", true);
                            $('#jl-'+finreqID).remove();
                            $('#jresourcetypesearch').trigger('click');
                        }
                    }
                });

            }
            else
            {
               return false;
            }

        }    
    });

 });

$(document).on("change", '.creditaccount', function(){
    var accnt_id = $(this).val(); 
    if(accnt_id!=0)
    {
        $.ajax({
            type : "POST",
            dataType : "json",
            url : '../financerequests/scheduleditems',
            data : {accnt_id:accnt_id },
            success: function(data){
                if(data.error=='No')
                {
                    $('#shitem').html(data.result);
                }
            }

        });
    }
});

$(document).on('click', '#shitem', function(){
    $('.erorshitem').hide();
 var vall = $(this).val();  
 if(vall == null)
 {
    $('.erorshitem').show();
 }
});