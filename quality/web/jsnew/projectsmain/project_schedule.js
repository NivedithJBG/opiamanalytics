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
    var scheduleitem_id=$('#listworkgroup-Schedul-datas').val();
    var prjcid=$('#prjid').val();
    //alert(scheduleitem_id);
    if(scheduleitem_id==0){
        var links="allganttchart?id="+prjcid+"";   
    //alert (links);
    window.location.replace(links);
    }
    else{
    var links="newganttchartshow?id="+scheduleitem_id+"";   
    //alert (links);
    window.location.replace(links);
}

});
