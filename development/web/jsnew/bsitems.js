/**
 * Created by SolmindsDelli5 on 19-03-2018.
 */
$(document).on('click','#bsitems',function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#listbsitems').trigger('click');

    //return false; //Prevent the browser jump to the link anchor

});
$(function() {

    // project section function

    // list project click

    $('#listbsitems').click(function () {

        $('#bsitemsaddsection').slideUp('slow');// slide down the project listing div

        $('#bsitemslistsection').slideDown('slow');// slide down the project listing div

        $('#listbsitems').removeClass('btn-danger').addClass('btn-success');

        $('#addbsitem').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../AccountsSub/searchbsitems',

            beforeSend: function () {

                $('.preloader').show();

            },

            dataType: "json",

            data: {acntsubgrp: $('#searchacntsubgrp').val(), bsitemname: $('#searchbsitems').val()},

            success: function (data) {

                if (data.error == 'No') {

                    $('#bsitemsitems').html(data.result);

                    $('#bsitemstable').show();

                }


                $('.preloader').hide();

            }

        });


    });

    $('#bsitemssearch').click(function () {

        $('#listbsitems').trigger('click')

    });

    $('#addbsitem').click(function () {

        $('#bsitemslistsection').slideUp('slow');// slide down the project listing div

        $('#bsitemsaddsection').slideDown('slow');// slide down the project listing div

        $('#addbsitem').removeClass('btn-danger').addClass('btn-success');

        $('#listbsitems').removeClass('btn-success').addClass('btn-danger');


    });

    $('#savebsitems').click(function () {

        var error = 0;

        $('.error').hide();

        if ($('#choosesubacntgrp').val() == 'none') {

            $("#choosesubacntgrp").next("span").html('Select Account Subgroup').show('slow');

            error = 1;

        }

        if ($('#bsitemname').val() == '') {

            $("#bsitemname").next("span").html('Enter Name').show('slow');

            error = 1;

        }

        if (error == 0) {

            $.ajax({

                type: 'POST',

                url: '../AccountsSub/createbsitem',

                beforeSend: function () {

                    $('#savebsitems').attr("disabled", true);

                },

                dataType: 'json',

                data: {
                    bsitemname: $('#bsitemname').val(),
                    acntsubgrp: $('#choosesubacntgrp').val(),
                },

                success: function (data) {

                    if (data.error == 'No') {

                        $('#bsitemname').val('');
                        //$('#bsitemsform')[0].reset();

                        $('#listbsitems').trigger('click');

                        $('#savebsitems').attr("disabled", false);

                    }

                    $('#savesubacntgrps').attr("disabled", false);

                }

            });

        }

    });
});
$(document).on( "click", ".editbsitembutton", function(){
    var idval=$(this).val();
    $('#editbsitemname'+idval).show();
    $('#editbsacntsubgrp'+idval).show();
    $('#savebsitembutton'+idval).show();
    $('#bsacntsubgrpstext'+idval).hide();
    $('#bsitemtext'+idval).hide();
    $('#editbsitembutton'+idval).hide();
});

$(document).on( "click", ".savebsitembutton", function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();

    if($('#editbsacntsubgrp'+idval).val()=='')
    {
        $('#editbsacntsubgrp'+idval).next("span").html('Select Account Subgroup').show('slow');
        error=1;
    }

    if($('#editbsitemname'+idval).val()=='')
    {
        $('#editbsitemname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../AccountsSub/updatebsitem',
            beforeSend : function(){
                $('#savebsitembutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {itemid:idval,acntsubgrp:$('#editbsacntsubgrp'+idval).val(),name:$('#editbsitemname'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editbsitemname'+idval).hide();
                    $('#editbsacntsubgrp'+idval).hide();
                    $('#savebsitembutton'+idval).hide();
                    $('#bsitemtext'+idval).text($('#editbsitemname'+idval).val()).show();
                    $('#bsacntsubgrpstext'+idval).text(data.acntsubgrp).show();
                    $('#editbsitembutton'+idval).show();

                }
                $('#savebsitembutton'+idval).attr("disabled", false);
            }
        });
    }

});

$(document).on( "click", ".deletebsitembutton", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this BS Item ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../AccountsSub/Deletebsitem',
            beforeSend : function(){
                $('#deletebsitembutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {bsitemid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#bsitemsrow'+idval).remove();
                    $('#listbsitems').trigger('click');
                }

                $('#deletebsitembutton'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
