$(document).on('click','#equipments',function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listequipments').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});
$(function() {
    // project section function
    // list project click
    $('#listequipments').click(function () {
        $('#equipmentaddsection').slideUp('slow');// slide down the project listing div
        $('#equipmentlistsection').slideDown('slow');// slide down the project listing div
        $('#listequipments').removeClass('btn-danger').addClass('btn-success');
        $('#addequipment').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../equipments/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {equipmentname:$('#searchequipment').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#equipmentitems').html(data.result);
                    $('#equipmenttable').show();
                }

                $('.preloader').hide();
            }
        });

    });
    $('#equipmentsearch').click(function(){
        $('#listequipments').trigger('click')
    });
    $('#addequipment').click(function(){
        $('#equipmentlistsection').slideUp('slow');// slide down the project listing div
        $('#equipmentaddsection').slideDown('slow');// slide down the project listing div
        $('#addequipment').removeClass('btn-danger').addClass('btn-success');
        $('#listequipments').removeClass('btn-success').addClass('btn-danger');
    });
    $('#saveequipment').click(function(){
        var error=0;
        $('.error').hide();
        if($('#equipmentname').val()=='')
        {
            $("#equipmentname").next("span").html('Enter Equipment Name').show('slow');
            error=1;
        }
        var name=$('#equipmentname').val();
        var unit=$('#equipmentunit').val();

        if(error==0){
            $.ajax({
                type:'POST',
                url:'../equipments/create',
                beforeSend:function(){
                    $('#saveequipment').attr("disabled", true);
                },
                dataType:'json',
                data: {name:name,unit:unit},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#equipmentform')[0].reset();
                        $('#listequipments').trigger('click');
                        $('#saveequipment').attr("disabled", false);
                    }
                }
            });
        }
    });
});
$(document).on( "click", ".editequipmentbutton", function(){
    var idval=$(this).val()
    $('#editequipmentname'+idval).show();
    $('#editequipmentunit'+idval).show();
    $('#saveequipmentbutton'+idval).show();
    $('#nametext'+idval).hide();
    $('#unittext'+idval).hide();
    $('#editequipmentbutton'+idval).hide();
} );
$(document).on( "click", ".saveequipmentbutton", function(){
    var idval=$(this).val();
    var name=$('#editequipmentname'+idval).val();
    var unit=$('#editequipmentunit'+idval).val();
    var error=0;
    $('.error').hide();
    if($('#editequipmentname'+idval).val()=='')
    {
        $('#editequipmentname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../equipments/update',
            beforeSend : function(){
                $('#saveequipmentbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {equipmentid:idval,name:name,unit:unit},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editequipmentname'+data.Id).hide();
                    $('#editequipmentunit'+data.Id).hide();
                    $('#saveequipmentbutton'+data.Id).hide();
                    $('#nametext'+data.Id).text($('#editequipmentname'+data.Id).val()).show();
                    $('#unittext'+data.Id).text($('#editequipmentunit'+data.Id).val()).show();
                    $('#editequipmentbutton'+data.Id).show();
                }
                $('#saveequipmentbutton'+data.Id).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deleteequipmentbutton", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Equipment ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../equipments/Delete',
            beforeSend : function(){
                $('#deleteequipmentbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {equipmentid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#equipmentrow'+data.Id).remove();
                    $('#listequipments').trigger('click');
                }
                $('#deleteequipmentbutton'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});