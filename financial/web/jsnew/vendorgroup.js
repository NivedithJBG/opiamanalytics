$(document).on('click','#Vendorgroup',function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#listvendorgroup').trigger('click');

    //return false; //Prevent the browser jump to the link anchor

});

$(function(){

    // project section function

    // list project click

    $('#listvendorgroup').click(function(){

        $('#vendorgroupaddsection').slideUp('slow');// slide down the project listing div

        $('#vendorgrouplistsection').slideDown('slow');// slide down the project listing div

        $('#listvendorgroup').removeClass('btn-danger').addClass('btn-success');

        $('#addvendorgroup').removeClass('btn-success').addClass('btn-danger');       
        

        $.ajax({

            type: 'POST',

            url: '../vendors/SearchVendorgroups',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {vendorgroupname:$('#vendorgroupnamesearch').val(),vendortype:$('#vendortypelist').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#vendorgroupitems').html(data.result);

                    $('#vendorgrouptable').show();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });



    });

    $('#vendorgroupsearching').click(function(){

        $('#listvendorgroup').trigger('click')

    });

    // list project click  \

    // add project click

    $('#addvendorgroup').click(function(){

        $('#vendorgrouplistsection').slideUp('slow');// slide down the project listing div

        $('#vendorgroupaddsection').slideDown('slow');// slide down the project listing div

        $('#addvendorgroup').removeClass('btn-danger').addClass('btn-success');

        $('#listvendorgroup').removeClass('btn-success').addClass('btn-danger');



    });

    // add project click

    // save project click

    $('#savevendorgroup').click(function(){

        var error=0;

        $('.error').hide();        
        

        if($('#vendorgroupname').val()=='')

        {

            $("#vendorgroupname").next("span").html('Enter Vendor Group Name').show('slow');

            error=1;

        }
        
        if($('#choosevendortype').val()=='none')

        {

            $("#choosevendortype").next("span").html('Select Vendor Type').show('slow');

            error=1;

        }

        if(error==0){

            $.ajax({

                type:'POST',

                url:'../vendors/CreateVendorgroup',

                beforeSend:function(){

                    $('#savevendorgroup').attr("disabled", true);

                },

                dataType:'json',

                data: {vendorgroupname:$('#vendorgroupname').val(),vendortype:$('#choosevendortype').val()},

                success:function(data){

                    if(data.error=='No')

                    {

                        $('#addvendorgroupform')[0].reset();

                        $('#listvendorgroup').trigger('click');

                        $('#savevendorgroup').attr("disabled", false);
                        
                         /*$('#searcselecttype').append($('<option>', {

                            value: data.Id,

                            text: data.Name

                        }));*/

                    }
                    
                    else

                    {

                        alert(data.errortext);

                    }

                }

            })

        }

    });

});

$(document).on( "click", ".editvendorgroupbutton", function(){

    var idval=$(this).val();
    
    $('#editvendortypetext'+idval).show();

    $('#editvendorgroupname'+idval).show();

    $('#savevendorgroupbutton'+idval).show();

    $('#vendorgrouptext'+idval).hide();
    
    $('#vendortypenametext'+idval).hide();

    $('#editvendorgroupbutton'+idval).hide();

} );



$(document).on( "click", ".savevendorgroupbutton", function(){

    var idval=$(this).val();

    var error=0;

    $('.error').hide();

    if($('#editvendorgroupname'+idval).val()=='')

    {

        $('#editvendorgroupname'+idval).next("span").html('Enter Name').show('slow');

        error=1;

    }

    if($('#editvendortypetext'+idval).val()=='')

    {

        $('#editvendortypetext'+idval).next("span").html('Enter Name').show('slow');

        error=1;

    }

    if(error==0){

        $.ajax({

            type: 'POST',

            url: '../vendors/Vendorgroupupdate',

            beforeSend : function(){

                $('#savevendorgroupbutton'+idval).attr("disabled", true);

            },

            dataType: "json",

            data: {vendorgroupid:idval,name:$('#editvendorgroupname'+idval).val(),vendortype:$('#editvendortypetext'+idval).val()},

            success: function(data){

                if(data.error=='No')

                {
                    $('#editvendortypetext'+idval).hide();

                    $('#editvendorgroupname'+idval).hide();

                    $('#savevendorgroupbutton'+idval).hide();

                    $('#vendorgrouptext'+idval).text($('#editvendorgroupname'+idval).val()).show();

                    $('#vendortypenametext'+idval).text(data.vendortype).show();

                    $('#editvendorgroupbutton'+idval).show();

                    $('#savevendorgroupbutton'+idval).attr("disabled", false);

                    $('#listvendorgroup').trigger('click');

                }
                
                else

                {

                    alert(data.errortext);

                }

            }

        })

    }



} );





$(document).on( "click", ".deletvendorgroupbutton", function(){

    var idval=$(this).val();

    var r = confirm("Are you sure you want to delete this Vendor Group?");

    if (r == true) {



        $.ajax({

            type: 'POST',

            url: '../vendors/DeleteVendorgroup',

            beforeSend : function(){

                $('#deletvendorgroupbutton'+idval).attr("disabled", true);

            },

            dataType: "json",

            data: {vendorgroupid:idval},

            success: function(data){

                if(data.error=='No')

                {

                    $('#vendorgrouprow'+idval).remove();
                    
                    //$("select#searcselecttype option[value='"+data.Id+"']").remove();

                    //$('#listvendortype').trigger('click');

                    $('#deletvendorgroupbutton'+idval).attr("disabled", false);

                }

            }

        });



    } else {

        return false;

    }



});

$(document).on('click','.childvendors',function(){

    var vendorgroupid=$(this).val();
    var vendortypeid=$(this).attr('data-id');

    $('#vendorvtypelist').val(vendortypeid);
    $('#vendorvgrouplist').val(vendorgroupid);

    $('#ProcVendor').trigger('click');

});

