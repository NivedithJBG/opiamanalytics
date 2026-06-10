$(document).on('click','#electricaleqpmnts',function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listelecteqpmnts').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});
$(function() {
    // project section function
    // list project click
    $('#listelecteqpmnts').click(function () {
        $('#electeqpmntsaddsection').slideUp('slow');// slide down the project listing div
        $('#electeqpmntslistsection').slideDown('slow');// slide down the project listing div
        $('#listelecteqpmnts').removeClass('btn-danger').addClass('btn-success');
        $('#addelecteqpmnts').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../projects/listequipments',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {estactivityname:$('#searchestactivityname').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#electeqpmntsitems').html(data.result);
                    $('#electeqpmntstable').show();
                }

                $('.preloader').hide();
            }
        });

    });
    $('#electeqpmntssearch').click(function(){
        $('#listelecteqpmnts').trigger('click')
    });
    $('#addelecteqpmnts').click(function(){
        $('#electeqpmntslistsection').slideUp('slow');// slide down the project listing div
        $('#electeqpmntsaddsection').slideDown('slow');// slide down the project listing div
        $('#addelecteqpmnts').removeClass('btn-danger').addClass('btn-success');
        $('#listelecteqpmnts').removeClass('btn-success').addClass('btn-danger');
    });
    $('#saveelecteqpmnts').click(function(){
        var error=0;
        $('.error').hide();
        $('.electeqpmntsnamelist').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#electeqpmntsnamelist"+id).next("span").html('Select Equipment').show('slow');
                error=1;
            }
        });
        $('.electeqpmntcapacity').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#electeqpmntcapacity"+id).next("span").html('Enter Capacity').show('slow');
                error=1;
            }
        });
        $('.electeqpmntsunit').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#electeqpmntsunit"+id).next("span").html('Enter Units/Hour').show('slow');
                error=1;
            }
        });

        if(error==0){
            $.ajax({
                type:'POST',
                url:'../projects/equipments',
                beforeSend:function(){
                    $('#saveelecteqpmnts').attr("disabled", true);
                },
                dataType:'json',
                data: $( "#electeqpmntsform" ).serialize(),
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#electeqpmntsform')[0].reset();
                        $('#listelecteqpmnts').trigger('click');
                        $('#saveelecteqpmnts').attr("disabled", false);
                    }
                }
            });
        }
    });
    // $( "#estactivityitems" ).sortable({
    //     items: '.no',
    //     update:function( event, ui ) {
    //         //alert($(this).index());
    //         var updatedrows=[];
    //         $(this).closest('table').find('tbody tr').each(function (i) {
    //             var rowid=$(this).attr('data-id');
    //             var activitytype=$(this).attr('data-type');
    //             var rowindex=$(this).index();
    //             updatedrows.push({
    //                 rowid: rowid,
    //                 rowindex:rowindex,
    //                 rowtype:activitytype
    //             })
    //         });
    //         $.ajax({
    //             type: 'POST',
    //             url: '../projects/updateestactivitiessort',
    //             data: {datavalue:updatedrows},
    //             dataType: "json",
    //             success: function(data){
    //                 $('#listestactivity').trigger('click');
    //             }
    //         });
    //     }

    // }).disableSelection()
});

$(document).on( "click", ".editelecteqpmntsbutton", function(){
    var idval=$(this).val()
    $('#editelecteqpmntcapacity'+idval).show();
    $('#editelecteqpmntunits'+idval).show();
    $('#electeqpmntcapacitytext'+idval).hide();
    $('#electeqpmntunitstext'+idval).hide();
    $('#editelecteqpmntsbutton'+idval).hide();
    $('#saveelecteqpmntsbutton'+idval).show();
} );
$(document).on( "click", ".saveelecteqpmntsbutton", function(){
    var idval=$(this).val();
    var equipmentcapacity=$('#editelecteqpmntcapacity'+idval).val();
    var equipmentunit=$('#editelecteqpmntunits'+idval).val();
    var error=0;
    $('.error').hide();
    if($('#editelecteqpmntcapacity'+idval).val()=='')
    {
        $('#editelecteqpmntcapacity'+idval).next("span").html('Enter Capacity').show('slow');
        error=1;
    }
    if($('#editelecteqpmntunits'+idval).val()=='')
    {
        $('#editelecteqpmntunits'+idval).next("span").html('Enter Units').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../projects/updateequipment',
            beforeSend : function(){
                $('#saveelecteqpmntsbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {equipmentid:idval,equipmentcapacity:equipmentcapacity,equipmentunit:equipmentunit},
            success: function(data){

                if(data.error=='No')
                {
                    $('#editelecteqpmntcapacity'+data.Id).hide();
                    $('#editelecteqpmntunits'+data.Id).hide();
                    $('#electeqpmntcapacitytext'+data.Id).text(data.equipmentcapacity).show();
                    $('#electeqpmntunitstext'+data.Id).text(data.equipmentunit).show();
                    $('#editelecteqpmntsbutton'+data.Id).show();
                    $('#saveelecteqpmntsbutton'+idval).hide();
                }
                $('#saveelecteqpmntsbutton'+data.Id).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deleteelecteqpmntsbutton", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Equipment ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../projects/Deleteequipment',
            beforeSend : function(){
                $('#deleteelecteqpmntsbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {equipmentid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#invequipmentsrow'+data.Id).remove();
                    //$('#listworktype').trigger('click');
                }
                $('#deleteelecteqpmntsbutton'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});