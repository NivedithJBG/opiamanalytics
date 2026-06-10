$(document).on( "click", "#liststocks", function(){
   if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
   }
   if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
       
   }
    $('.report-list-wrpr').show();
});



//stockstatement-material report
$(document).on( "click", "#viewstockstatement", function(){
    
    $('.drilldown2').addClass('report1');
    $('.drilldown2').removeClass('report2');
    $('.drilldown2').removeClass('report3');
    $('.drilldown2').removeClass('report4');
    $('#reporthead').hide();
    $('#stockstatements').show();
    
    
    $('#stocksearch').trigger('click');
  
   


});
$(document).on( "click", ".report1", function(){
     $('#stockdetails').hide();
    $('#stockstatements').show();
    $('#reporthead').hide();
    });
$(document).on( "click", ".mainlistback", function(){
    $('#stockdetails').hide();
    $('#stockstatements').hide();
    $('#reporthead').show();
    });
/*$(document).on( "click", ".drilldown2", function(){
    $('#stockdetails').hide();
    $('#stockstatements').show();
    $('#reporthead').hide();
});*/


$(document).on( "click", ".viewstockdetails", function(){

    var id=$(this).data('id');
    $.ajax({

            type: 'POST',

            url: '../procurement/details',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {id:id},

            success: function(data){

                if(data.error=='No')

                {
                    $('#stockstatements').hide();
                    $('#stockdetails').show();

                    $('#resourcename').html(data.resourcename);
                    $('#datarows').html(data.result);

                    $('#dataactrows').html(data.dataactrows);


                }

                $('.preloader').hide();

            }

        });

});

$(document).on( "click", "#stockstatement", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

});

$(function() {
    $('#stocksearch').click(function () {
        $('#reporthead').hide();
        $('#stockstatements').show();
    

        $.ajax({

            type: 'POST',

            url: '../procurement/stockresources',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {project:$('#stockproject').val(),item:$('#stockitem').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#stockresourceitems').html(data.result);
                    $('#stockitem').html(data.dataresitems);

                    $('#stockresourcetable').show();

                }

                $('.preloader').hide();

            }

        });

    });
});


//stockstatement-consumables report

$(document).on( "click", "#viewconsumables", function(){
    $('.drilldown2').removeClass('report1');
    $('.drilldown2').addClass('report2');
    $('.drilldown2').removeClass('report3');
    $('.drilldown2').removeClass('report4');
    $('#reporthead').hide();
    $('#stockstatementsconsumables').show();
    
    
    $('#stockconssearch').trigger('click');
  
   


});
$(function() {
    $('#stockconssearch').click(function () {

        $.ajax({

            type: 'POST',

            url: '../procurement/stockconsumables',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {project:$('#stockconsproject').val(),item:$('#stockconsitem').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#stockconsresourceitems').html(data.result);
                    $('#stockconsitem').html(data.dataresitems);

                    $('#stockconsresourcetable').show();

                }

                $('.preloader').hide();

            }

        });

    });

});
$(document).on( "click", ".mainlistback1", function(){
    //$('#stockdetails').hide();
    $('#stockstatementsconsumables').hide();
    $('#reporthead').show();
    });
$(document).on( "click", ".report2", function(){
     $('#stockdetails').hide();
    $('#stockstatementsconsumables').show();
    $('#reporthead').hide();
    });
$(document).on( "click", ".viewstockdetailsconsumables", function(){

    var id=$(this).data('id');
    $.ajax({

            type: 'POST',

            url: '../procurement/details',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {id:id},

            success: function(data){

                if(data.error=='No')

                {
                    $('#stockstatementsconsumables').hide();
                    $('#stockdetails').show();

                    $('#resourcename').html(data.resourcename);
                    $('#datarows').html(data.result);

                    $('#dataactrows').html(data.dataactrows);


                }

                $('.preloader').hide();

            }

        });

});


//stockstatement-purchase input report

$(document).on( "click", "#viewpurchasein", function(){
    $('.drilldown2').removeClass('report1');
    $('.drilldown2').removeClass('report2');
    $('.drilldown2').addClass('report3');
    $('.drilldown2').removeClass('report4');
    $('#reporthead').hide();
    $('#stockstatementspurchase').show();
    
    
    $('#stockpurchsearch').trigger('click');
  
   


});
$(function() {
    $('#stockpurchsearch').click(function () {

        $.ajax({

            type: 'POST',

            url: '../procurement/stockpurchasedinputs',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {project:$('#stockpurchproject').val(),item:$('#stockpurchitem').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#stockpurchresourceitems').html(data.result);
                    $('#stockpurchitem').html(data.dataresitems);

                    $('#stockpurchresourcetable').show();

                }

                $('.preloader').hide();

            }

        });

    });

});
$(document).on( "click", ".viewstockdetailspurchase", function(){

    var id=$(this).data('id');
    $.ajax({

            type: 'POST',

            url: '../procurement/details',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {id:id},

            success: function(data){

                if(data.error=='No')

                {
                    $('#stockstatementspurchase').hide();
                    $('#stockdetails').show();

                    $('#resourcename').html(data.resourcename);
                    $('#datarows').html(data.result);

                    $('#dataactrows').html(data.dataactrows);


                }

                $('.preloader').hide();

            }

        });

});
$(document).on( "click", ".mainlistback2", function(){
    //$('#stockdetails').hide();
    $('#stockstatementspurchase').hide();
    $('#reporthead').show();
    });
$(document).on( "click", ".report3", function(){
     $('#stockdetails').hide();
    $('#stockstatementspurchase').show();
    $('#reporthead').hide();
    });


//stockstatement-Tools and Tackles

$(document).on( "click", "#viewtoolsandtackles", function(){

    $('.drilldown2').removeClass('report1');
    $('.drilldown2').removeClass('report2');
    $('.drilldown2').removeClass('report3');
    $('.drilldown2').addClass('report4');
    $('#reporthead').hide();
    $('#stockstatementstool').show();
    
    
    $('#stocktoolssearch').trigger('click');
  
   


});
$(document).on( "click", ".mainlistback3", function(){
    //$('#stockdetails').hide();
    $('#stockstatementstool').hide();
    $('#reporthead').show();
    });
$(document).on( "click", ".report4", function(){
     $('#stockdetails').hide();
    $('#stockstatementstool').show();
    $('#reporthead').hide();
    });

$(function() {
    $('#stocktoolssearch').click(function () {

        $.ajax({

            type: 'POST',

            url: '../procurement/stocktoolsitems',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {project:$('#stocktoolproject').val(),item:$('#stocktoolitem').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#stocktoolresourceitems').html(data.result);
                    $('#stocktoolitem').html(data.dataresitems);

                    $('#stocktoolresourcetable').show();

                }

                $('.preloader').hide();

            }

        });

    });

});
$(document).on( "click", ".viewstockdetailtools", function(){

    var id=$(this).data('id');
    $.ajax({

            type: 'POST',

            url: '../procurement/details',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {id:id},

            success: function(data){

                if(data.error=='No')

                {
                    $('#stockstatementstool').hide();
                    $('#stockdetails').show();

                    $('#resourcename').html(data.resourcename);
                    $('#datarows').html(data.result);

                    $('#dataactrows').html(data.dataactrows);


                }

                $('.preloader').hide();

            }

        });

});

