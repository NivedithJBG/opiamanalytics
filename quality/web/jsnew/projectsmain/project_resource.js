$(document).on( "click", ".projectresources", function(){
    $('#listrestypes').trigger('click') ;
});

$(function() {
    $('#listrestypes').click(function () {
        $('#reporthead').show();
    

        $.ajax({

            type: 'POST',

            url: '../projectsmain/resourcereport',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {},

            success: function(data){

                if(data.error=='No')

                {

                    $('#resourcedetail').html(data.result);
                    $("span#projectname").html(getProjectname(data.projectid));

                    $('#resourcestable').show();

                }

                $('.preloader').hide();

            }

        });

    });
});

$(document).on( "click", ".viewresourcedetails", function(){
    
    var name=$(this).data('id');
        $.ajax({
            type: 'POST',
            url: '../projectsmain/resourcereportdetails',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {resourcetype:name},
            success: function(data){
                if(data.error=='No')
                { 
                    
                    $('#reporthead').hide();
                    $('#resourcedetailsreport').show();
                    $('#resourcetype').html(data.restypename);
                    $('#resourcerows').html(data.result);
                }

                $('.preloader').hide();
            }
        });
    });

$(document).on( "click", ".drilldowns", function(){
    $('#reporthead').show();
     $('#resourcedetailsreport').hide();
    });