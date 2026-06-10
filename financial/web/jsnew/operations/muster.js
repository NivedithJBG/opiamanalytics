$(document).on( "click", "#Raise-Wage-Roll", function(){
    var id= $(this).val();

    $('#projid').val(id);

    //$('#projectname').html(getProjectname(id));//

    $('#selectedProjectId').val(id);

    $('#musterprojectid').val(id);

    $('#processlistsection').show();

    $('#reportactivities').hide();

    //$('#receivedirectwork').trigger('click') ;

    $("#directdis").css("display", "none");
    $("#atthis").css("display", "none");
    $("#listmuster").css("display", "none");
    

});

$(function(){



     $('#rptattendance').click(function(){ 

        $(".data-content-wagerolllist").css("display", "none");
        $.ajax({
            type: 'POST',
            url: '../projects/directworkordersrpt',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){

                if(data.error=='No')

                {
                    $('.receivedirectworkitemss').html(data.result);
                     $("#newone").css("display", "block");
                     $("#attendancedata").css("display", "none");
                      $("#attendreports").css("display", "none");
                     
                     
                    $(".raise-wage-roll-list-wrpr data-content-list").css("display", "block");
                   
                      $(".receivedirectworkitemss").css("display", "block");
                    $('#musteraddsection').hide();
                     $("#directdis").css("display", "block");
                     $("#listmuster").css("display", "none");
                     $("#atthis").css("display", "block");
                     $("#attnhis").css("display", "none");
                     $("#msttems").css("display", "none");
                      // $('#newone').show();

                    $('.drctworkorderitem').show();
                    $('#newone').show();
                    $("#msrll").css("display", "none");
                    $("#newone").css("display", "block");
                    $(".rtpdatazz").css("display", "block");


                     
                    // $("#newone").css("display", "none");
                     


                     
                }

                $('.preloader').hide();

            }

        });
    });

       $('#mustroll').click(function(){
        $.ajax({
            type: 'POST',
            url: '../projects/directworkordersmstroll',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){

                if(data.error=='No')

                {
                    $('.receivedirectworkitemss').html(data.result);
                      $("#newone").css("display", "block");
                       $("#attendreports").css("display", "none");
                       $("#attendancedata").css("display", "none");
                    $(".receivedirectworkitemss").css("display", "block");
                    $('#musteraddsection').hide();
                    $(".data-content-wagerolllist").css("display", "none");
                     $("#directdis").css("display", "block");
                      $("#listmuster").css("display", "block");
                       $("#atthis").css("display", "none");
                        $("#attnhis").css("display", "none");
                         $("#msttems").css("display", "none");
                        // $("#newone").css("display", "none");
                     
                    
                }

                $('.preloader').hide();

            }

        });
    });





    $('#receivedirectwork').click(function(){
        $.ajax({
            type: 'POST',
            url: '../projects/directworkorders',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){

                if(data.error=='No')

                {
                    $('#receivedirectworkitems').html(data.result);
                    $('#musteraddsection').hide();
                    
                }

                $('.preloader').hide();

            }

        });
    });
    $('#listmuster').click(function(){
        $.ajax({

            type: 'POST',

            url: '../projects/mustersearch',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            success: function(data){

                if(data.error=='No')

                {
                    $('.muster-list-wrpr').show();
                    $('#musteritems').html(data.result);
                    $('#dateinfodiv').html(data.dateinfo);

                    $('#mustertable').show();
                    $('.drctworkorderitem').hide();
                    $(".rtpdatazz").css("display", "none");
                    $("#attnhis").css("display", "none");
                    $("#msrll").css("display", "none");
                    $(".close-muster-btn").css("display", "block");
                      
                    
                    
                    

                }

                $('.preloader').hide();

            }

        });

    });

    $('#listattendance').click(function(){
        $('#receivedirectwork').hide();
        $('.muster-date-from-to').hide();
        $.ajax({

            type: 'POST',

            url: '../projects/attendancehistory',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            success: function(data){

                if(data.error=='No')

                {

                    $('#musteritems').html(data.result);
                    $('#dateinfodiv').html(data.dateinfo);

                    $('#mustertable').show();
                    $('.drctworkorderitem').hide();

                }

                $('.preloader').hide();

            }

        });

    });

    $(document).on('click','.singlelistattendance',function(){  
        $('#attendreports').hide();
        $('#receivedirectworkitems').hide(); 
        $('#raisattenditemsview').hide();
        $('.report-attend-form').hide();
        $('#attendancedata').show();
        $('#listattendance').hide(); 
        $('#listmuster').hide();
        $('.edit-form').hide(); 
        var orderid = $(this).attr('data-v');
        $.ajax({

            type: 'POST',

            url: '../projects/singleattendancehistory',

            beforeSend : function(){

                //$('.preloader').show();

            },

            dataType: "json",
            data: {orderid:orderid},
            success: function(data){

                if(data.error=='No')
                {  $('#attendreports').hide();
                    $('#raisattenditemsview').hide();
                    $('.report-attend-form').hide();
                    $('#attendancedata').html(data.result);
                    //$('.raise-wage-roll-list-wrpr').hide();
                    /*$('.muster-list-wrpr').show();
                    $('#musteritems').html(data.result);
                    $('#mustertable').show();
                    $('#listattendance').hide(); 
                    $('#listmuster').hide();*/
                }

                //$('.preloader').hide();

            }

        });

    });





    $(document).on('click','#atthis',function(){  
        $('#attendreports').hide();
        $('#receivedirectworkitems').hide(); 
        $('#raisattenditemsview').hide();
        $('.report-attend-form').hide();
        $('#attendancedata').show();
        $('#listattendance').hide(); 
        $('#listmuster').hide();
        $('.edit-form').hide(); 
        //var orderid = $(this).attr('data-v');
        $.ajax({

            type: 'POST',

            url: '../projects/allattendancehistory',

            beforeSend : function(){

                //$('.preloader').show();

            },

            dataType: "json",
           // data: {orderid:orderid},
            success: function(data){

                if(data.error=='No')
                {  $('#attendreports').hide();
                    $('#raisattenditemsview').hide();
                    $('.report-attend-form').hide();
                    $('#attendancedataz').html(data.result);
                    $("#msttems").css("display", "none");
                    $("#attnhis").css("display", "block");
                    $(".close-muster-btn").css("display", "none");
                    $(".rtpdatazz").css("display", "none");
                    
                    
                    
                    
                    //$('.raise-wage-roll-list-wrpr').hide();
                    /*$('.muster-list-wrpr').show();
                    $('#musteritems').html(data.result);
                    $('#mustertable').show();
                    $('#listattendance').hide(); 
                    $('#listmuster').hide();*/
                }

                //$('.preloader').hide();

            }

        });

    });




    $(document).on('click','.close-muster-btn',function(){
        $('.drctworkorderitem').show();
        $('#newone').show();
        $("#msrll").css("display", "none");
        $("#newone").css("display", "block");
        $(".rtpdatazz").css("display", "block");
        $(".close-muster-btn").css("display", "none");
       
        
       
        

        
     //   $('#attendancedata').hide();
     //   $('#receivedirectworkitems').show();
    });

    $(document).on('click','#closeattendance',function(){ 
        $('#attendancedata').hide();
        $('#receivedirectworkitems').show();
        $('#listattendance').show(); 
        $('#listmuster').show();
    });


     $(document).on('click','#closeattendancez',function(){ 
        $('#attendancedata').hide();
        $('#receivedirectworkitems').show();
        $('#listattendance').show(); 
        $('#listmuster').show();
        $("#attnhis").css("display", "none");
        $(".rtpdatazz").css("display", "block");
        $("#listmuster").css("display", "none");
        
        
        
    });


    



    $(document).on('click','#closemusterroll',function(){ 

        $('.add-form').hide();
        $('.raise-wage-roll-list-wrpr').show();
      
    });


    $(document).on('click','#raisemuster',function(){ 
        var orderid=$(this).attr("data-v");
        var activityid=$(this).attr("data-id");
        var fromdate=$('#raisemustfromdate').val();
        var enddate=$('#raisemusttodate').val();
        if( fromdate !=null && enddate !=null){
            if(fromdate !='' && enddate !=''){
                $.ajax({
                    type: 'POST',
                    url: '../projects/raisemuster',
                    beforeSend : function(){
                        $('.preloader').show(); 
                    }, 
                    dataType: "json", 
                    data: {orderid:orderid,activityid:activityid,fromdate:fromdate,enddate:enddate,checkNOW:'date'},
                    success: function(data){  
                        if(data.error=='No'){
                            $('#raisemusteritems').html(data.resourcerows);
                        }
                        $('.preloader').hide();
                    }
                });
            }
        }else{
            $.ajax({
                type: 'POST',
                url: '../projects/raisemuster',
                beforeSend : function(){
                    $('.preloader').show(); 
                }, 
                dataType: "json", 
                data: {orderid:orderid,activityid:activityid,checkNOW:'dateNO'},
                success: function(data){  
                    if(data.error=='No'){
                        $('#raisemusteritems').html(data.resourcerows);
                    }
                    $('.preloader').hide();
                }
    
            });
        }
    });

    $(document).on('click','.mustercheck',function(){  
        var ischecked= $(this).is(':checked');
        if(ischecked)
        {
            

            var musderis = []; 
            var no = $(this).attr('data-no');
            var orderid = $(this).attr('data-odr');
            var actid = $(this).attr('data-act');
            var date = $(this).attr('data-date');
            $.ajax({
                type: 'POST',
                url: '../projects/getmusterdata',
            
                dataType: "json", 
                data: {orderid:orderid,actid:actid,date:date},
                success: function(data){  
                    if(data.error=='No'){
                
                        
                    }
            
                }
    
            });


        }
        else
        {
            
            var musderis = []; 
            var no = $(this).attr('data-no');
            var orderid = $(this).attr('data-odr');
            var actid = $(this).attr('data-act');
            var date = $(this).attr('data-date');
            $.ajax({
                type: 'POST',
                url: '../projects/removemusterdata',
            
                dataType: "json", 
                data: {orderid:orderid,actid:actid,date:date},
                success: function(data){  
                    if(data.error=='No'){
                
                        
                    }
            
                }
    
            });
        }
        
    }); 
  
/*    $(document).on('click','.mustercheck',function(){ 
        var musderis = []; //odrid
        
        var no = $(this).attr('data-no');
        var orderid = $(this).attr('data-odr');

            var actid = $(this).attr('data-act');
            var date = $(this).attr('data-date');

         
            $.ajax({
                type: 'POST',
                url: '../projects/getmusterdata',
               
                dataType: "json", 
                data: {orderid:orderid,actid:actid,date:date},
                success: function(data){  
                    if(data.error=='No'){
                   
                    }
                }
    
            });
    });*/


    $(document).on('click','#musterattenditems input',function(){
    

        var musodrid = [];   //muster id
        var musactid = []; //odrid
        var musdate = [];   //actid
        var muserid = [];
    
        $('#musterattenditems input:checked').each(function() {
         
    
            
            musodrid.push($(this).attr('data-odr'));
            musactid.push($(this).attr('data-act'));
            musdate.push($(this).attr('data-date'));
            muserid.push($(this).val());
           
            
        });
      
        $('[name="musodrid"]').attr({value: musodrid.join(', ')});
        $('[name="musactid"]').attr({value: musactid.join("','")});
        $('[name="musdate"]').attr({value: musdate.join("','" )});

        $('[name="muserid"]').attr({value: muserid.join(', ')});
       
       
    });

    $(document).on('click','.raisemustersingle',function(){  

        if($('.mustercheck').is(":checked")) {


            
           
            var actid="";
            var error=0;
            $("input:checkbox[name=mustercheckk]:checked").each(function () {  
                var activityid=$(this).attr("data-act");
                
                if(actid!='' && activityid!=actid){
                    error=1;

                   alert("Different activites is not allowed to generate");
 
                }

                  actid=activityid;
              
            //alert("Id: " + $(this).attr("id") + " Value: " + $(this).val());
            });

         if(error==0)
         {



            $('#listattendance').hide(); 
            $('#listmuster').hide();
            $('#attendreports').hide();
            $('.report-attend-form').hide();
            $('.edit-form').hide(); 

            var musterid=$('#muserid').val();
            var activityid=$('#musactid').val();
            var orderidd=$('#musodrid').val();
            var orderid=$('#ordersid').val(); //odr id array
            var musdate=$('#musdate').val();

        
            
                $.ajax({
                    type: 'POST',
                    url: '../projects/raisemuster',
                    beforeSend : function(){
                      //  $('.preloader').show(); 
                    }, 
                    dataType: "json", 
                    data: {orderid:orderid,activityid:activityid,musterid:musterid,musdate:musdate,orderidd:orderidd,checkNOW:'dateNO'},
                    success: function(data){  
                        if(data.error=='No'){
                        $('#raisemusteritemsview').hide();  //view screen
                        $('.raise-wage-roll-list-wrpr').hide();  //vendor wise list screeen -history
                            $('.muster-list-wrpr').hide();
                            $('#mustertable').hide();
                            $('#attendreports').hide();
                            $('.report-attend-form').hide();
                            $('.add-form').show();
                            $('#raisemusteritems').html(data.resourcerows);

                            
                        }
                       // $('.preloader').hide();
                    }
        
                });
            } } else {
                alert('Please select any report attendance before proceeding.');
                return false;
            }
    });

    $(document).on('click','#musterattendance',function(){ 
        var orderid=$('#Attendanceview').attr("data-odr");
        var activityid=$('#Attendanceview').attr("data-act");
        var fromdate=$('#raisemustfromdate').val();
        var enddate=$('#raisemusttodate').val();
        if( fromdate !=null && enddate !=null){
            if(fromdate !='' && enddate !=''){
                $.ajax({
                    type: 'POST',
                    url: '../projects/raisemuster',
                    beforeSend : function(){
                        $('.preloader').show(); 
                    }, 
                    dataType: "json", 
                    data: {orderid:orderid,activityid:activityid,fromdate:fromdate,enddate:enddate,checkNOW:'date'},
                    success: function(data){  
                        if(data.error=='No'){
                            $('#raisemusteritems').html(data.resourcerows);
                        }
                        $('.preloader').hide();
                    }
                });
            }
        }else{
            $.ajax({
                type: 'POST',
                url: '../projects/raisemuster',
                beforeSend : function(){
                    $('.preloader').show(); 
                }, 
                dataType: "json", 
                data: {orderid:orderid,activityid:activityid,checkNOW:'dateNO'},
                success: function(data){  
                    if(data.error=='No'){
                        $('#raisemusteritems').html(data.resourcerows);
                    }
                    $('.preloader').hide();
                }
    
            });
        }
    });

    $(document).on('change','.ratechange',function(){  

        var id = $(this).data('id');
        var leg = $('.ratechange').length;

        orderid = $('#ordeidshw'+id).val();
        resid = $('#ordeidshw'+id).data('value');

        var resrate = $('#raiseratess'+id).val();
        

        var totwage = 0;

        //$('.fulldata').each(function(){

           // var nid = $(this).data('id');

            var rrate = $('#raiseratess'+id).val();
            
            var nodays = $('#fulldata'+id).data('value'); 
            var otrate = $('#raiseotval'+id).val();
            var othrs = $('#fulldata'+id).data('othrs');   

            var wages=(nodays * rrate) + (othrs * otrate);
       
 

            $('#raisewages'+id).html(wages.toFixed(2));
            $('#raisewagesval'+id).val(wages)
            $('.ratevalall').val(resrate);
            $('#edtraiserateval'+id).val(resrate);
            $('#fulldata'+id).attr('data-rate',resrate);
            $('#raiseratess1'+id).val(resrate);


            $('.wagesval').each(function(){

                var wage = $(this).val();

                totwage = parseFloat(totwage) + parseFloat(wage);


            });$('#totw').html(totwage.toFixed(2))

            // $('.fulldata').each(function(){

            //     var nid = $(this).data('id');

            //     var srate = $('#fulldata'+nid).data('rate');alert(srate)
            
            //     var nodays = $(this).data('value');
            //     var otrate = $(this).data('otrate');
            //     var othrs = $(this).data('othrs');
    
            //     var wages=(nodays * srate) + (othrs * otrate);

            //     totwage = totwage + wages;

            //     $('#totw').html(totwage.toFixed(2))
            // });

           

        //});
        
        
      
        
         
        
        
/* 
        $.ajax({
            type: 'POST',
            url: '../projects/editresrate',
            beforeSend : function(){}, 
            dataType: "json", 
            data: {orderid:orderid,resid:resid,resrate:resrate},
            success: function(data){  
            }
        });
 */
        
            
        });
    $(document).on('click','#raisemustsearch',function(){
        //$('#raisemuster').trigger('click') ;

        var orderid=$('#raise_Order_ID').val();
        var activityid=$('#raisemusteractivity').val();
        var fromdate=$('#raisemustfromdate').val();
        var enddate=$('#raisemusttodate').val();
        if( fromdate !=null && enddate !=null){
            if(fromdate !='' && enddate !=''){
                $.ajax({
                    type: 'POST',
                    url: '../projects/raisemuster',
                    beforeSend : function(){
                        $('.preloader').show(); 
                    }, 
                    dataType: "json", 
                    data: {orderid:orderid,activityid:activityid,fromdate:fromdate,enddate:enddate,checkNOW:'date'},
                    success: function(data){  
                        if(data.error=='No'){
                            $('#raisemusteritems').html(data.resourcerows);
                        }
                        $('.preloader').hide();
                    }
                });
            }
        }else{
            $.ajax({
                type: 'POST',
                url: '../projects/raisemuster',
                beforeSend : function(){
                    $('.preloader').show(); 
                }, 
                dataType: "json", 
                data: {orderid:orderid,activityid:activityid,checkNOW:'dateNO'},
                success: function(data){  
                    if(data.error=='No'){
                        $('#raisemusteritems').html(data.resourcerows);
                    }
                    $('.preloader').hide();
                }
    
            });
        }
    });
    $(document).on('click','#raisemusterbtn',function(){
        var error=0;
        if(error==0){
            $.ajax({
                type: 'POST',
                url: '../projects/reportmusterroll',
                beforeSend : function(){
                    $('#raisemusterbtn').attr("disabled", true);
                },
                dataType: "json",
                data: $( "#raisemusterform" ).serialize(),
                success: function(data){
                    if(data.error=='No')
                    {
                        $('.add-form').hide();
                        $('.edit-form').hide();
                        $('.raise-wage-roll-list-wrpr').show();
                        $('#attendancedata').hide();
                        $('#receivedirectworkitems').show();

                        $('#listattendance').show(); 
                        $('#listmuster').show();

                       /* $('#raisemusterform')[0].reset();
                        $('#cancelmuster').trigger('click');
                        $('.cancel').trigger('click');
                        $('#listmuster').trigger('click') ;*/


                    }

                    $('#raisemusterbtn').attr("disabled", false);
                }
            });
        }
        else{
            alert("You have to enter all values for reporting");
            return  false;
        }
    });
    
    $(document).on('click','#attendcancel',function(){
        $('.report-attend-form').hide();
        $('#attendancedata').show();
        $("#attnhis").css("display", "block");
        $("#attendreports").css("display", "none");
        

        
      
    });
    $(document).on('click','#cancelmuster',function(){
        $('#raisemusteritems').empty();
      
    });
    $(document).on('click','.deletemusterroll',function(){
        var groupid=$(this).attr("data-v");
        var r = confirm("Are you sure you want to delete this Muster ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../jobcard/deletemusterroll/',
                beforeSend : function(){
                    $('#deletemusterroll'+groupid).attr("disabled", true);
                },
                dataType: "json",
                data: {groupid:groupid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#musterrow'+groupid).remove();
                    }
                    $('#deletemusterroll'+groupid).attr("disabled", false);
                }
            });
        }
        else {
            return false;
        }
    });

    $(document).on('click','.tab-wrapper .Musterview',function(){
        $(this).parents('.tab').addClass('edit-form-active');
        $(this).parents('.tab').removeClass('add-form-active');
        var groupid=$(this).attr("data-v");
            $.ajax({
                type: 'POST',
                url: '../projects/musterview/',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {groupid:groupid},
                success: function(data){
                    if(data.error=='No')
                    {   
                        $('.edit-form').show();
                        $('#raisemusteritemsview').show();
                        $('#raisemusteritemsview').html(data.result);
                        $('#dateinfodiv').html('');
                        $("#msttems").css("display", "none");
                        
                    }
                    $('.preloader').hide();
                }
            });
    });

    $(document).on('click','.tab-wrapper .Attendanceview',function(){
        $(this).parents('.tab').addClass('edit-form-active');
        $(this).parents('.tab').removeClass('add-form-active');

        var orderid=$(this).attr("data-odr"); 
        var activityid=$(this).attr("data-act");  
        var date=$(this).attr("data-date");
        var musterid=$(this).val(); 
        //$('.edit-form').hide();
            $.ajax({
                type: 'POST',
                url: '../projects/attendanceview/',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {orderid:orderid,activityid:activityid,musterid:musterid,date:date},
                success: function(data){
                    if(data.error=='No')
                    {   $('#attendreports').show();
                        $("#attendancedata").css("display", "none");
                        $('#raisattenditemsview').show();
                        $('#raisattenditemsview').html(data.result);
                        $('#dateinfodiv').html('');
                        $("#attnhis").css("display", "none");
                        
                    }
                    $('.preloader').hide();
                }
            });
    });


  $(document).on('click','#raisehistory',function(){ 
        

   //$('#history').click(function(){ alert ("hi");
        
        $.ajax({
            type: 'POST',
            url: '../projects/raisehistory',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#raisewagerollhistory').html(data.result);
                    $('#newtwo').show();
                }
                $('.preloader').hide();
            }
        });
    });














    $(document).on('focus','.datepicker',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });


    $(document).on('click','#back_buttons',function(){ 
    //$('#musteraddsection').slideUp('slow');
    $('#newone').show();
    $('#musteraddsection').hide();

    $('#listattendance').show(); 
    $('#listmuster').show();
    $("#listmuster").css("display", "none");
    
   // $('#receivedirectworkorders').slideDown('slow');
    
});




$(document).on('click','#addmuster',function(){ 

    $('#receivedirectworkorders').slideUp('slow');
    $('#selectedprojct').hide();
    $('#musteraddsection').slideDown('slow');

    $('#listattendance').hide(); 
    $('#listmuster').hide();

    var orderid=$(this).val();
    var activityid=$(this).attr("data-id");
    
/*  var activityid=$('#musteractivity').val(); athira
  if(activityid!=0 || activityid!=undefined)
  {
    activityid =
  }*/
    var projectid = $(this).attr("data-value");
    //alert (projectid);

    var date=$('#date0').val();



    $.ajax({

        type: 'POST',

        url: '../projects/musterprocess',

        beforeSend : function(){

           // $('.preloader').show();

        },

        dataType: "json",

        data: {projectid:projectid,orderid:orderid,activityid:activityid,date:date},

        success: function(data){

            if(data.error=='No')

            {

                //$('#musterprocessdiv').html(data.result);

                $('#musterreportitems').html(data.resourcerows);
                $('#activityselect').html(data.activityseldata);  
                $('#wrkinghours').html(data.workinghours);
                $('#musterreporttable').show();

                $('#newone').hide();
                $('#activitydiv').html(data.prodtname);
                 $(".data-content-wagerolllist").css("display", "block");
                 $("#atthis").css("display", "none");
                 


                if(data.pendhoursts==0)
                {
                    $('.balhours').show();
                }
                else if(data.pendhoursts==1)
                {
                    $('.balhours').hide();
                }
                else
                {
                    $('.balhours').hide();
                }


            }

           // $('.preloader').hide();

        }

    });

});

$(document).on('click','#addmusterss',function(){ 

    $('#receivedirectworkorders').slideUp('slow');
    $('#selectedprojct').hide();
    $('#musteraddsection').slideDown('slow');
    
    var orderid=$('#musterorderid').val();
    //var orderid=$('#addmuster').val();
    var activityid=$('#musteractivity').val();
    
/*  var activityid=$('#musteractivity').val(); athira
  if(activityid!=0 || activityid!=undefined)
  {
    activityid =
  }*/
    var projectid = $('#musterprojectID').val();
    //alert (projectid);

    var date=$('#date0').val();



    $.ajax({

        type: 'POST',

        url: '../projects/musterprocess',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {projectid:projectid,orderid:orderid,activityid:activityid,date:date},

        success: function(data){

            if(data.error=='No')

            {

                //$('#musterprocessdiv').html(data.result);

                $('#musterreportitems').html(data.resourcerows);
                $('#activityselect').html(data.activityseldata);
                $('#musterreporttable').show();
                $('#wrkinghours').html(data.workinghours);
                $('#newone').hide();
                $('#activitydiv').html(data.prodtname);
                if(data.pendhoursts==0)
                {
                    $('.balhours').show();
                }
                else if(data.pendhoursts==1)
                {
                    $('.balhours').hide();
                }
                else
                {
                    $('.balhours').hide();
                }
            }

            $('.preloader').hide();

        }

    });

});

$(document).on('change','#date0',function(){
    var orderid=$('#musterorderid').val();
    var activityid=$('#musteractivity').val();
    var process=$('#process').val();
    var date=$(this).val();

    var projectid=$('#musterprojectID').val();

    $.ajax({

        type: 'POST',

        //url: '../report/MusterProcess',
        url: '../projects/datemusterprocess',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {projectid:projectid,orderid:orderid,activityid:activityid,process:process,date:date},

        success: function(data){

            if(data.error=='No')

            {

                //$('#musterprocessdiv').html(data.result);

                $('#musterreportitems').html(data.resourcerows);
                $('#wrkinghours').html(data.workinghours);
                $('#musterreporttable').show();

                if(data.pendhoursts==0)
                {
                    $('.balhours').show();
                }
                else if(data.pendhoursts==1)
                {
                    $('.balhours').hide();
                }
                else
                {
                    $('.balhours').hide();
                }



                

            }

            $('.preloader').hide();

        }

    });
});

$(document).on('change','#musteractivity',function(){
    var orderid=$('#musterorderid').val();
    var activityid=$(this).val();
    var process=$('#process').val();
    var date=$('#date0').val();

    var projectid=$('#musterprojectID').val();

    $.ajax({

        type: 'POST',

        //url: '../report/MusterProcess',
        url: '../projects/activitymusterprocess',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {projectid:projectid,orderid:orderid,activityid:activityid,process:process,date:date},

        success: function(data){

            if(data.error=='No')

            {

                //$('#musterprocessdiv').html(data.result);

                $('#musterreportitems').html(data.resourcerows);
                $('#wrkinghours').html(data.workinghours);
                $('#musterreporttable').show();

                if(data.pendhoursts==0)
                {
                    $('.balhours').show();
                }
                else if(data.pendhoursts==1)
                {
                    $('.balhours').hide();
                }
                else
                {
                    $('.balhours').hide();
                }

                

            }

            $('.preloader').hide();

        }

    });
});



$(document).on('click','#musterreport',function(){ 
        var error=0;
        //var url="<?php echo Yii::$app->request->baseUrl; ?>/projects/report"; 


        /*if($('#musteractivity').val()=='none')
        {
            error=1;
        }*/

        $('.dwoworker').each(function(){
             if($(this).val()=='')
             {
                error=1;
             }
         });
      /*  $('.dwoworkedhrs').each(function(){
            //alert($(this).val())

            if($(this).val()=='')
            {
                error=1;
            }

        });*/
        var numdays=$('#noofdays').val();
        var repdays=$('#repdays').val();
        if (parseInt(repdays) == parseInt(numdays)){
            //alert('Cannot report more than the order days');
            //error=1;
        }


         //var count=0;
       //  $('.dwoworkedhrs').each(function(){
       //      var id=$(this).attr('data-id');
       //      var workinghrs=$('#workinghrs'+id).val()*1;  

       //    //$('.overtime').each(function(){
       //      var id=$(this).attr('data-id');
       //      var totaldays=$('#overtime'+id).val()*1;  
           
       //      //alert (totaldays);


       //      //var totaldays= workinghrs + totaldays;
       //      var totaldays=  totaldays;

       //      var final= totaldays + totaldays;
       // // });

       //      var id=$(this).attr('data-id');
       //      var tt=$('#totaldays'+id).val()*1;  

       //     // alert(tt);
       //      if(final == tt)
       //      {

       //          //error==0
       //         //  $('#workedhrs'+id).next('span').html('Hours worked not be greater than '+ workinghrs).show('slow');
       //         // count++;
       //      }
       //      else if(final <= tt){
       //         // 
       //      }else {
       //          error=1;
       //          alert ("Total Working hours not more than " + tt); 

       //      }


     
       //     //alert (final);
       //  });
      

      

        var actid = $('#musteractivity').val();
        if(actid != 0)
        {

           
            /*var editval = $('.editreport').val();
            if(editval==1)
            {
                
                    $('.error').hide();
                    var count=0;
                    var tradeid=$('.dwoworkedhrsedit').attr('data-tr');
                    $('.dwoworkedhrsedit'+tradeid).each(function(){
                        var id=$('.dwoworkedhrsedit').attr('data-id');
                        var pendhours=$(this).attr('data-val');
                         
                        if($(this).val() > pendhours)
                        {
                            $('#workedhrs'+id).next('span').html('Hours worked not be greater than '+ pendhours).show('slow');
                           count++;
                           error=2;
                        }
                        else
                        {
                            error=0;
                        }

                    });
                    if (count==0)
                    {
                        $('#musterreport').attr("disabled", false);
                    }
                    else {
                        $('#musterreport').attr("disabled", true);
                    }

                
            }
            else if(editval==0)
            {
                error=0;
            }*/

            if(error==0){
                $.ajax({
                    type: 'POST',
                    url: '../projects/reportmuster',
                    beforeSend : function(){
                        $('#musterreport').attr("disabled", true);

                    },
                    dataType: "json",
                    data: $( "#musterform" ).serialize(),
                    success: function(data){
                        if(data.error=='No')
                        {
                            //$('#musterform')[0].reset();
                            $(".rprtattndstyle").show().delay(5000).fadeOut();
                            $('#addmusterss').trigger('click') ;
                          //  $('#musterreport').trigger('click') ;

                            //window.location.href = url;
                            //$('#receivedirectwork').trigger('click') ;
                            //$('#projectiddetails').html(data.project_id);
                            $('#selectedprojct').val(data.project_id);

                            //$('#newone').show();
                          
                        }

                        $('#musterreport').attr("disabled", false);
                    }
                });
            }
            else{
                alert("You have to enter all Employee Name");
                return  false;
            }
        }
        else
        {
            alert("Please select any Activity Name.");
                return  false;
        }
    })


      $(document).on('keyup','.dwoworkedhrs',function(){ 
        $('.error').hide();
        var count=0;
        $('.dwoworkedhrs').each(function(){
            var id=$(this).attr('data-id');
            var workinghrs=$('#workinghrs'+id).val()*1;  
            if($(this).val() > workinghrs)
            {
                $('#workedhrs'+id).next('span').html('Hours worked not be greater than '+ workinghrs).show('slow');
               count++;
            }

        });
        if (count==0)
        {
            $('#musterreport').attr("disabled", false);
        }
        else {
            $('#musterreport').attr("disabled", true);
        }

    });

    /*
    //pending hour calculation
      $(document).on('click','.musterreport',function(){ 
        $('.error').hide();
        var count=0;
        var tradeid=$('.dwoworkedhrsedit').attr('data-tr');
        $('.dwoworkedhrsedit'+tradeid).each(function(){
            var id=$(this).attr('data-id');
            var pendhours=$(this).attr('data-val');
             
            if($(this).val() > pendhours)
            {
                $('#workedhrs'+id).next('span').html('Hours worked not be greater than '+ pendhours).show('slow');
               count++;
            }

        });
        if (count==0)
        {
            $('#musterreport').attr("disabled", false);
        }
        else {
            $('#musterreport').attr("disabled", true);
        }

    });*/

    $(document).on('change','.otratechange',function(){ 

        var id = $(this).data('id'); 

        var orderid = $('#ordeidshw'+id).val();

        var resid = $('#ordeidshw').data('value');

        var otrate = $(this).val();

        var nodays = $('#edraiseworkedhrs'+id).val();

        var othourstot = $('#edraiseovertime'+id).val();


        $.ajax({

            type: 'POST',
                url: '../projects/saveotrate',
                beforeSend : function(){
                    $('#musterreport').attr("disabled", true);

                },
                dataType: "json",
                data: {orderid:orderid,resid:resid,otrate:otrate,nodays:nodays,othourstot:othourstot},
                success: function(data){
                    if(data.error == 'No')
                    {
                        var nodays = $('#edraiseworkedhrs'+id).val();
                        var rate = $('#raiseratess'+id).val();
                        var othourstot =  $('#edraiseovertime'+id).val();



                     //   $('#raiseot'+id).val(otrate);
                        var otrates = $('#raiseot'+id).val(); 
                        $('#raiseotval'+id).val(otrates); 
                        
                        var twages = parseFloat(nodays*rate) + parseFloat(othourstot*otrates);

                        $('#raisewages'+id).html(twages);
                        $('#raisewagesval'+id).val(twages);

                        var tot = 0;
                        $('.wagesval').each(function(){

                            var val = $(this).val();//alert(val)

                            tot = parseFloat(tot) + parseFloat(val);

                        });

                        $('#totw').html(tot.toFixed(2));

                    }
                   
                }

        });
    });





});