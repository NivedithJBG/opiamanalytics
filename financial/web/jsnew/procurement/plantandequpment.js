$(document).on( "click", "#plantandequip", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
   }
   if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
       
   }

    $('.report-list-wrprs').show();
});

$(document).on( "click", "#viewengin", function(){
	
	$('#engindrivenreport').show();
	$('#reportshead').hide();
    $('#engindrivensearch').trigger('click');
});

$(document).on( "click", ".firstlistback", function(){
    $('#engindrivenreport').hide();
    $('#reportshead').show();
    });


$(function() {
    $('#engindrivensearch').click(function () {
        $('#reportshead').hide();
        $('#engindrivenreport').show();
    

        $.ajax({

            type: 'POST',

            url: '../procurement/enginequipmentcost',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {project:$('#projequipmentcost').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#equipmentcostitems').html(data.result);
                    $('#rsgroupname').html(data.resgname);
                    $('#equipmentcosttable').show();

                }

                $('.preloader').hide();

            }

        });

    });
});



$(document).on( "click", "#viewmotor", function(){
    
    $('#motordrivenreport').show();
    $('#reportshead').hide();
    $('#motordrivensearch').trigger('click');
});
$(document).on( "click", ".firstlistback1", function(){
    $('#motordrivenreport').hide();
    $('#reportshead').show();
    });

$(function() {
    $('#motordrivensearch').click(function () {
        $('#reportshead').hide();
        $('#motordrivenreport').show();
    

        $.ajax({

            type: 'POST',

            url: '../procurement/motorequipmentcost',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {project:$('#projequipmentcosts').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#motorequipmentcostitems').html(data.result);
                    $('#resgnames').html(data.resgnames);
                    $('#motorequipmentcosttable').show();

                }

                $('.preloader').hide();

            }

        });

    });
});