$(document).on( "click", "#electlogbook", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#electloglistsection').slideUp('slow');
    $('#electlogbooklist').slideDown('slow');
    $('#projelectlogbook').show();
    $('#listelectlogbook').removeClass('btn-danger').addClass('btn-success');
    $('#listelectlog').removeClass('btn-danger').addClass('btn-danger');
});
$(function(){
    $('#projelectlogbook').change(function(){
        var error=0;
        $('.error').hide();
        var projectid = $('#projelectlogbook').val();
        var logdate=$('#electlogdate0').val();
        
        if(projectid==''){
           // $('#projlogbook').next("span").html('Select Project').show().delay(3000).fadeOut();
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
    });
    $('#projlistlog').change(function(){
        $('#listlog').trigger('click');
    });
    $('#electlogdate0').change(function(){
        var projectid = $('#projlogbook').val();
        var logdate=$(this).val();
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

                    }

                    $('.preloader').hide();

                }

            });
    });
    $('#listlog').click(function(){
        $('#desporderslist').slideUp('slow');
        $('#logaddsection').slideUp('slow');
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
                        $('#projlogbook').val(data.projectid)
                        $('#powerbookmsg').show();
                        setTimeout(function() {
                            $('#powerbookmsg').hide();
                        }, 2000); 
                        $('#electlogbookreport').attr("disabled", false);
                    }
                }
            });
        }
});