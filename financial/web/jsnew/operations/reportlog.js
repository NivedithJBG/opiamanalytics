$(document).on( "click", "#LogEquipmentUsage", function(){
    $('#LogEquipmenthead').trigger('click');
});
$(function() { 

    $('#LogEquipmenthead').click(function(){
        $.ajax({
            type: 'POST',
            url: '../report/logheadsearch',
            beforeSend : function(){
             //   $('.preloader').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#Log-Equipment-list').html(data.result);
                    $('#tab-listingz').html(data.loghead1);
                    //$('#Log-head-One').html(data.loghead1);
                    //$('#Log-head-Two').html(data.loghead2);
                    
                    //$('#close-log-list-btn').hide();
                    $(".close-log-list-btn").css("display", "none");
                    //$('#Issue-Material-Slips-head').show();
                }
                $('.preloader').hide();
            }
        });
    });



    $('#Log-head-One').click(function(){ 

       var projectid = $("#projlogbook").val();
       
       var resgrp = $(this).data("r");

        $.ajax({
                    type: 'POST',
                    url: '../projects/despatchorders',
                    beforeSend : function(){
                        $('.preloader').show();
                    },
                    dataType: "json",
                    data: {projectid:projectid,resgrp:resgrp},
                    success: function(data){
                        if(data.error=='No')
                        {
                            $('#Log-Equipment-list').html(data.result);
                            $('#Log-Equipment-list').css('display','block');
                        }
                        $('.preloader').hide();
                    }
            });

    });



    $('#Log-head-Two').click(function(){ 

       //var projectid = $("#projlogbook").val();
       
       var resgrp = $(this).data("r");
       var logdate=$('#electlogdate0').val(); 

        var projectid = $('#projlogbook').val();
            $('#projelectlogbook').val(projectid);

      // alert (logdate);

        $.ajax({
                    type: 'POST',
                    url: '../projects/equipmentorders',
                    beforeSend : function(){
                        $('.preloader').show();
                    },
                    dataType: "json",
                    data: {projectid:projectid,logdate:logdate},
                    success: function(data){
                        if(data.error=='No')
                        {
                            $('#Log-Equipment-list').html(data.result);
                            $('#Log-Equipment-list').css('display','block');
                        }
                        $('.preloader').hide();
                    }
            });

    });













    $('#listdespatchorders').click(function(){
        var resgrp = $('#resouregrp').val();
        //console.log(resgrp);
        if(resgrp=='102')
        {
            console.log('engine driven')
            var error=0;
            $('.error').hide();        
            var projectid = $('#projlogbook').val();
            if(resgrp==''){
                $('#resouregrp').next("span").html('Select Resource Group').show().delay(3000).fadeOut();
                error=1;
            }          
            if(projectid==''){
                $('#projlogbook').next("span").html('Select Project').show().delay(3000).fadeOut();
                error=1;
            }
            if(error==0){
                $.ajax({
                    type: 'POST',
                    url: '../projects/despatchorders',
                    beforeSend : function(){
                        $('.preloader').show();
                    },
                    dataType: "json",
                    data: {projectid:projectid,resgrp:resgrp},
                    success: function(data){
                        if(data.error=='No')
                        {
                            $('#Log-Equipment-list').html(data.result);
                            $('#Log-Equipment-list').css('display','block');
                        }
                        $('.preloader').hide();
                    }
                });
            }
        }
        else if (resgrp=='154') {
            console.log('motor driven')
            var error=0;
            $('.error').hide();
            var projectid = $('#projlogbook').val();
            $('#projelectlogbook').val(projectid);
            var logdate=$('#electlogdate0').val();        
            if(projectid==''){
               $('#projlogbook').next("span").html('Select Project').show().delay(3000).fadeOut();
                error=1;
            }
            if(resgrp==''){
                $('#resouregrp').next("span").html('Select Resource Group').show().delay(3000).fadeOut();
                error=1;
            }
            if(error==0){
                $.ajax({
                    type: 'POST',
                    url: '../projects/equipmentorders',
                    beforeSend : function(){
                        $('.preloader').show();
                    },
                    dataType: "json",
                    data: {projectid:projectid,logdate:logdate},
                    success: function(data){
                        if(data.error=='No')
                        {
                            $('#Log-Equipment-list').html(data.result);
                            $('#Log-Equipment-list').css('display','block');
                        }

                        $('.preloader').hide();

                    }

                });
            }
        }
    });
    $(document).on('change','#projlogbook',function(){
        $('#listdespatchorders').trigger('click');
    });

    $(document).on('click','#logclz',function(){
       $("#Log-Equipment-list-log").css("display", "none");
       $("#logclz").css("display", "none");
    });


    $(document).on('change','#resouregrp',function(){
        var resgrp = $('#resouregrp').val();
        /*if(resgrp==102){
           $('#resgrpchnge').html('No of Trips');
        }
        else{
           $('#resgrpchnge').html('No of Hours');
        }*/
        $('#listdespatchorders').trigger('click');
    });


//function for validating date is less the receieved date
    var recievedDate='';
    $(document).on('click','#addlog',function(){
        var activityid=$(this).attr("data-v");
        var resourceid=$(this).attr("data-id");
        

        $.ajax({
            type: 'POST',
            url: '../projects/logdatevalidate',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#projlogbook').val(),resourceid:resourceid,activityid:activityid},
            success: function(data){
                //console.log("recdate",data.recievedDate);
                recievedDate=data.recievedDate;
            }
        });


        $.ajax({
            type: 'POST',
            url: '../projects/logprocess',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {projectid:$('#projlogbook').val(),resourceid:resourceid,order_id:order_id},
            data: {projectid:$('#projlogbook').val(),resourceid:resourceid,activityid:activityid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#Log-Equipment-add-form').html(data.result);
                    $("#Log-Equipment-list").css("display", "none");
                    //$('#logbookitems').html(data.resourcerows);
                    //$('#logresource').html(data.resnames);
                    //$('#unitdata').html(data.units);
                    //$('#dieseldata').html(data.dieselissue);
                    //$('#logbookreporttable').html(data.resourcerows);
                    $('.preloader').hide();
                }
            }
        });
    
    });

    $(document).on('focus','.datepicker1',function(){
        console.log("recevedDate",recievedDate)
        $(this).datepicker({
            maxDate: new Date(),
            minDate: new Date(recievedDate),
            dateFormat: 'dd-mm-yy', 
            changeMonth: true,
            changeYear: true
        });
    });


    $(document).on( "change","#eqpunit", function(){
        var unit=$(this).val();   
        if (unit==1)
        {
            //$('#reading'+id).prop('readonly', false);
            $('.startreading').prop('readonly', false);
            $('.endreading').prop('readonly', false);
            //$('#starttime'+id).prop('type', 'time');
            //$('#endtime'+id).prop('type', 'time');
            $('.trips').prop('readonly', true);
        }
        else if (unit==2){
            //$('#reading'+id).prop('readonly', false);
            $('.startreading').prop('readonly', false);
            $('.endreading').prop('readonly', false);
            //$('#starttime'+id).prop('type', 'text');
            //$('#endtime'+id).prop('type', 'text');
            $('.trips').prop('readonly', true);
        }
        else {
            //$('#reading'+id).prop('readonly', true);
            $('.startreading').prop('readonly', true);
            $('.endreading').prop('readonly', true);
            $('.trips').prop('readonly', false);
        }
    
    });

    $(document).on('blur','.startreading',function(){
        var id=$(this).attr('data-id');
        var sreading=$(this).val() * 1;
        var ereading=$('#endreading'+id).val() * 1;
        var netreading= ereading - sreading;
        $('#nreading'+id).html(netreading);
        $('#nreadingval'+id).val(netreading);
        var totalhours=0;
        $('.nreadingval').each(function(){
            totalhours+=$(this).val()*1;
        });
        $('#nreadingtot').html(totalhours);
    });
    $(document).on('blur','.endreading',function(){
        var id=$(this).attr('data-id');
        var ereading=$(this).val() * 1;
        var sreading=$('#startreading'+id).val() * 1;
        var netreading= ereading - sreading;
        $('#nreading'+id).html(netreading);
        $('#nreadingval'+id).val(netreading);
        var totalhours=0;
        $('.nreadingval').each(function(){
            totalhours+=$(this).val()*1;
        });
        $('#nreadingtot').html(totalhours);
    })
    $(document).on('blur','.trips',function(){
        var totaltrips=0;
        $('.trips').each(function(){
            totaltrips+=$(this).val()*1;
        });
        $('#ntripstot').html(totaltrips);
    })
    $(document).on( "blur",".diesel", function(){
    
        var totalhours=0;
    
        $('.diesel').each(function(){
    
            totalhours=totalhours+$(this).val()*1;
    
        });
    
        $('#totaldiesel').html(totalhours);
    
    });

    $(document).on('click','#logbookreport',function(){
        var error=0;
        $('.error').hide();
        $('.unit').each(function(){
            var id=$(this).attr('data-id');
            if ($('#unit'+id).val()==3){
                if($('#trips'+id).val()==''){
                    error=1;
                }
            }
            else if ($('#unit'+id).val()==1 || $('#unit'+id).val()==2){
                if($('#reading'+id).val()==''){
                    error=1;
                }
            }
    
        });
        if($('#eqpunit').val()=='none')
        {
            $("#eqpunit").next("span").html('Select Unit').show('slow');
            error=1;
        }
        if($('#diesel').val()=='')
        {
            $("#diesel").next("span").html('Diesel Issued').show('slow');
            error=1;
        }
        if($('#eqpunit').val()==1 || $('#eqpunit').val()==2){

            // $('.startreading').each(function(){
            //     var id=$(this).attr('data-id');
            //     if($(this).val()=='')
            //     {
            //         $("#startreading"+id).next("span").html('Start Reading').show('slow');
            //         error=1;
            //     }
        
            // });
            // $('.endreading').each(function(){
            //     var id=$(this).attr('data-id');
            //     if($(this).val()=='')
            //     {
            //         $("#endreading"+id).next("span").html('End Reading').show('slow');
            //         error=1;
            //     }
        
            // });
            var val=0;
            $('.logactivity').each(function(){
                val=parseInt(val+1);
                var id=$(this).attr('data-id');
                if($(this).val()=='none')
                {
                   // $("#logbookactlist"+id).next("span").html('Select Activity').show('slow');
                    $("#logbookactlist"+val).next("span").html('Select Activity').show('slow');
                    error=1;
                }
        
            });

        }

        

        if($('#eqpunit').val()==3){

            $('.logactivity').each(function(){
                var id=$(this).attr('data-id');
                if($(this).val()=='none')
                {
                    $("#logbookactlist"+id).next("span").html('Select Activity').show('slow');
                    error=1;
                }
        
            });
            $('.trips').each(function(){
                var id=$(this).attr('data-id');
                if($(this).val()=='')
                {
                    $("#trips"+id).next("span").html('No of Trips').show('slow');
                    error=1;
                }
        
            });
        }

        if(error==0){
            $.ajax({
                type: 'POST',
                url: '../projects/reportlog',
                beforeSend : function(){
                    $('#logbookreport').attr("disabled", true);
    
                },
                dataType: "json",
                data: $( "#logbookform" ).serialize(),
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#logbookform')[0].reset();
                        $('#logbookreport').attr("disabled", false);
                        $('.cancel').trigger('click');
                        $('#listlog').trigger('click');
                    }
                }
            });
         }
         else{
             alert("You have to enter all values for reporting");
             return  false;
         }
    });
    $(document).on('blur','.eq_noofhrs',function(){
        var id=$(this).attr('data-id');
        var hours=$(this).val()*1;
        var units=$('#electequnits'+id).val()*1;
        var rate=$('#electeqamountval').val()*1;
        var amount=units*hours*rate;
        $('#equipmentamounttext'+id).html(amount.toFixed(2));
        $('#equipmentamountval'+id).val(amount);
    });
    $(document).on('click','#electlogbookreport',function(){
        var pelb=$('#projelectlogbook').val();
        $('#projelectlogbooks').val(pelb);
        var error=0;
            $('.error').hide();
            if($('#electlogdate0').val()=='')
            {
                $("#electlogdate0").next("span").html('Select Date').show('slow');
                error=1;
            }
            if($("#electcurrentcons").val()=='')
            {
                $("#electcurrentcons").next("span").html('Enter Current consumption').show('slow');
                error=1;
            }
            $('.eq_noofhrs').each(function(){
                var id=$(this).attr('data-id');
                if($(this).val()=='')
                {
                    //$("#eq_noofhrs"+id).next("span").html('Enter No of hours').show('slow');
                    //error=1;
                }
            });
            if(error==0){
                $.ajax({
                    type:'POST',
                    url:'../projects/reportpowerbook',
                    beforeSend:function(){
                        $('#electlogbookreport').attr("disabled", true);
                    },
                    dataType:'json',
                    data: $( "#electlogbookform" ).serialize(),
                    success:function(data){
                        if(data.error=='No')
                        {
                            $('#electlogbookform')[0].reset();
                            $('.equipmentamounttext').html('');
                            $('.equipmentamountval').val('');
                            //$('#listelecteqpmnts').trigger('click');
                            $('#projlogbook').val(data.projectid);
                            $('#electlogbookreport').hide();
                            $('#electlogbookreportsaved').show();
                            setTimeout(function() {
                                $('#electlogbookreportsaved').hide();
                                $('#electlogbookreport').show();
                            }, 3000); 
                            $('#electlogbookreport').attr("disabled", false);
                        }
                    }
                });
            }
    });

    $(document).on('change','#projlistlog',function(){
        $('#listlog').trigger('click');
    });
    $(document).on('click','#cancelLogView',function(){
        $('#Log-Equipment-list-log').show();
    });
    $('#listlog').click(function(){

          var projectid = $('#projlogbook').val();
            $('#projelectlogbook').val(projectid);


        $.ajax({

            type: 'POST',

            url: '../report/logsearch',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:projectid},

            success: function(data){

                if(data.error=='No')

                {

                    $('#Log-Equipment-list-log').html(data.result);
                    $("#Log-Equipment-list-log").css("display", "block");
                    $('.preloader').hide();
                    $("#Log-Equipment-list").css("display", "none");
                    $(".close-log-list-btn").show();
                    
                    

                }

            }

        });

    });

    $(document).on('click','.tab-wrapper .Logview',function(){
        $(this).parents('.tab').addClass('edit-form-active');
        $(this).parents('.tab').removeClass('add-form-active');
        $('#Log-Equipment-view-form').empty();
        $('#Log-Equipment-list-log').hide();
        var viewid=$(this).attr('data-v');
        var projectid=$(this).attr('data-h');
        $.ajax({
            type: 'POST',
            url: '../report/logview',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {orderid: viewid,projectid: projectid},
            success: function(data){
                if(data.error=='No')
                {
                    $('.preloader').hide();
                    $('#Log-Equipment-view-form').html(data.result);
                    $(".edit-form raise-bill-form").css("display", "block");
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });

    $(document).on('click','.tab-wrapper .Logedit',function(){
        $(this).parents('.tab').addClass('edit-form-active');
        $(this).parents('.tab').removeClass('add-form-active');
        $('#Log-Equipment-view-form').empty();
        $('#Log-Equipment-list-log').hide();
        var viewid=$(this).attr('data-v');
        var projectid=$(this).attr('data-h');
        $.ajax({
            type: 'POST',
            url: '../report/logedit',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {orderid: viewid,projectid: projectid},
            success: function(data){
                if(data.error=='No')
                {
                    $('.preloader').hide();
                    $('#Log-Equipment-view-form').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });

    $(document).on('click','#updateLog',function(){
        var error=0;
        $('.error').hide();
        $('.datess').each(function(){
            if($(this).val()=='')
            {
                error=1;
            }
        });
        $('.tripss').each(function(){
            if($(this).val()=='')
            {
                error=1;
            }
        });
        if(error==0){
        $.ajax({
            type: 'POST',
            url: '../report/logupdate',
            dataType: "json",
            data: $( "#Updatelogview" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#cancelLogView').trigger('click');
                    $('.cancel').trigger('click');
                    $('#listlog').trigger('click');
                }
            }
        });
        }
    });
    $(document).on('click','.deletelogbutton',function(){
        var logid=$(this).attr('data-v');
        var r = confirm("Are you sure you want to delete this Log?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../projects/deletelog',
                beforeSend : function(){
                    $('#deletelogbutton'+logid).attr("disabled", true);
                },
                dataType: "json",
                data: {logid:logid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#logrow'+logid).remove();
                        $('#deletelogbutton'+logid).attr("disabled", false);
                    }
    
                }
            });
        }
    });



});