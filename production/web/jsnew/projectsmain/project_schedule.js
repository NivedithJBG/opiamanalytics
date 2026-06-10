$(document).on( "click", ".projectschedule", function(){

	$('#listschedule').trigger('click');

});

$(function() {
    $('#listschedule').click(function () {
        $('#reporsthead').show();
    

        $.ajax({

            type: 'POST',

            url: '../projectsmain/prjschedulesearch',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {item:$('#listworkgroup-Schedul-datas').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#listworkgroup-Schedul-datas').html(data.result);
                    //$("span#projectname").html(getProjectname(data.projectid));

                    //$('#resourcestable').show();

                }

                $('.preloader').hide();

            }

        });

    });
});
$(document).on( "click", "#ganttt", function(){
    var prjcid=$('#prjid').val();
    window.location.replace("newganttchart?id="+prjcid);
});
