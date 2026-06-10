/**
 * Created by SolmindsDelli5 on 30-08-2019.
 */
$(document).on( "click", "#logbook", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }

    //$('#listdespatchorders').trigger('click');
    
    $('#logaddsection').slideUp('slow');
    $('#loglistsection').slideUp('slow');
    $('#electlogbooklist').slideUp('slow');
    $('#desporderslist').slideDown('slow');
    $('#projlogbook').show();
    $('#listdespatchorders').removeClass('btn-danger').addClass('btn-success');
    $('#listlog').removeClass('btn-danger').addClass('btn-danger');
});
$(function(){
    $('#listdespatchorders').click(function(){
        $('#projlogbook').show();
        $('#resouregrp').show();
        var resgrp = $('#resouregrp').val();
        //console.log(resgrp);
        if(resgrp=='102')
        {
            console.log('engine driven')
            $('#logaddsection').slideUp('slow');
            $('#loglistsection').slideUp('slow');
            $('#electlogbooklist').slideUp('slow');
            $('#desporderslist').slideDown('slow');
            $('#projlogbook').show();
            $('#listdespatchorders').removeClass('btn-danger').addClass('btn-success');
            $('#listlog').removeClass('btn-danger').addClass('btn-danger');
        
            var error=0;
            $('.error').hide();
            
            var projectid = $('#projlogbook').val();
            if(resgrp==''){
                //$('#resouregrp').next("span").html('Select Resource Group').show().delay(3000).fadeOut();
                error=1;
            }
            
            if(projectid==''){
               // $('#projlogbook').next("span").html('Select Project').show().delay(3000).fadeOut();
                error=1;
            }
        
            if(error==0){
                $.ajax({

                    type: 'POST',

                    url: '../projects/DespatchOrders',

                    beforeSend : function(){

                        $('.preloader').show();

                    },

                    dataType: "json",

                    data: {projectid:projectid,resgrp:resgrp},

                    success: function(data){

                        if(data.error=='No')

                        {

                            $('#desporderitems').html(data.result);

                            $('#desporderstable').show();

                        }

                        $('.preloader').hide();

                    }

                });
            }
        }
        else if (resgrp=='154') {
            console.log('motor driven')
            $('#logaddsection').slideUp('slow');
            $('#loglistsection').slideUp('slow');
            $('#desporderslist').slideUp('slow');
            $('#electlogbooklist').slideDown('slow');
            $('#projlogbook').show();
            $('#listdespatchorders').removeClass('btn-danger').addClass('btn-success');
            $('#listlog').removeClass('btn-danger').addClass('btn-danger');
            var error=0;
            $('.error').hide();
            var projectid = $('#projlogbook').val();
            $('#projelectlogbook').val(projectid);
            var logdate=$('#electlogdate0').val();
            
            if(projectid==''){
               // $('#projlogbook').next("span").html('Select Project').show().delay(3000).fadeOut();
                error=1;
            }
            if(resgrp==''){
                //$('#resouregrp').next("span").html('Select Resource Group').show().delay(3000).fadeOut();
                error=1;
            }
            if(error==0){
                $.ajax({

                    type: 'POST',

                    url: '../projects/EquipmentOrders',

                    beforeSend : function(){

                        $('.preloader').show();

                    },

                    dataType: "json",

                    data: {projectid:projectid,logdate:logdate},

                    success: function(data){

                        if(data.error=='No')

                        {

                            $('#electlogbookitems').html(data.result);

                            $('#electlogbooktable').show();
                            $('#electcurrentcons').val(data.currentcons);
                            $('#electeqcumcons').html(data.cumcons);
                            $('#electeqcumamount').html(data.cumamount);
                            $('#electeqamount').html(data.rate);
                            $('#electeqamountval').val(data.rate)

                        }

                        $('.preloader').hide();

                    }

                });
            }
        }
    });
    $('#projlogbook').change(function(){
        $('#listdespatchorders').trigger('click');
    });
    $('#resouregrp').change(function(){
        var resgrp = $('#resouregrp').val();
        /*if(resgrp==102){
           $('#resgrpchnge').html('No of Trips');
        }
        else{
           $('#resgrpchnge').html('No of Hours');
        }*/
        $('#listdespatchorders').trigger('click');
    });
    $('#projlistlog').change(function(){
        $('#listlog').trigger('click');
    });
    $('#listlog').click(function(){
        $('#projlogbook').hide();
        $('#resouregrp').hide();
        $('#desporderslist').slideUp('slow');
        $('#logaddsection').slideUp('slow');
        $('#electlogbooklist').slideUp('slow');
        $('#loglistsection').slideDown('slow');

        $('#listlog').removeClass('btn-danger').addClass('btn-success');

        $('#listdespatchorders').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../report/Logsearch',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:$('#projlistlog').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#logitems').html(data.result);

                    $('#logtable').show();
                    $('.preloader').hide();

                }

            }

        });

    });
});

$(document).on('click','#addlog',function(){
    $('#desporderslist').slideUp('slow');
    $('#loglistsection').slideUp('slow');
    $('#electlogbooklist').slideUp('slow');
    $('#logaddsection').slideDown('slow');
    $('#projlogbook').hide();
    $('#resouregrp').hide();
    //var order_id=$(this).val();
    var activityid=$(this).val();
    var resourceid=$(this).attr("data-id");
    $.ajax({

        type: 'POST',

        url: '../projects/LogProcess',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        //data: {projectid:$('#projlogbook').val(),resourceid:resourceid,order_id:order_id},
        data: {projectid:$('#projlogbook').val(),resourceid:resourceid,activityid:activityid},
        
        success: function(data){

            if(data.error=='No')

            {
                $('#logbookitems').html(data.resourcerows);
                $('#logbookreporttable').show();
                $('.preloader').hide();
            }
        }
    });

});
$(document).on( "change",".unit", function(){
    var id=$(this).attr('data-id');

    var unit=$('#unit'+id).val();

    if (unit==1)
    {
        $('#reading'+id).prop('readonly', false);
        //$('#starttime'+id).prop('readonly', false);
        //$('#endtime'+id).prop('readonly', false);
        $('#starttime'+id).prop('type', 'time');
        $('#endtime'+id).prop('type', 'time');
        $('#trips'+id).prop('readonly', true);
    }
    else if (unit==2){
        $('#reading'+id).prop('readonly', false);
        //$('#starttime'+id).prop('readonly', false);
        //$('#endtime'+id).prop('readonly', false);
        $('#starttime'+id).prop('type', 'text');
        $('#endtime'+id).prop('type', 'text');
        $('#trips'+id).prop('readonly', true);
    }
    else {
        $('#reading'+id).prop('readonly', true);
        //$('#starttime'+id).prop('readonly', true);
        //$('#endtime'+id).prop('readonly', true);
        $('#trips'+id).prop('readonly', false);
    }

});
$(document).on( "blur",".diesel", function(){

    var totalhours=0;

    $('.diesel').each(function(){

        totalhours=totalhours+$(this).val()*1;

    });

    $('#totaldiesel').html(totalhours);

});
$(document).on('click','#logbookreport',function(){
    var error=0;

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

    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../projects/Reportlog',
            beforeSend : function(){
                $('#logbookreport').attr("disabled", true);

            },
            dataType: "json",
            data: $( "#logbookform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#logbookform')[0].reset();
                    //window.location.href = url;
                    $('#listlog').trigger('click') ;
                    $('#logbookreport').attr("disabled", false);
                }


            }
        });
    }
    else{
        alert("You have to enter all the values for reporting");
        return  false;
    }
});

$(document).on('click','.deletelogbutton',function(){
    var logid=$(this).val();
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

