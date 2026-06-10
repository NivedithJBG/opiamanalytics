
$(document).on( "click", "#acc-groups", function(){  
            
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listaccountgroups').trigger('click');
                        
    $(this).parent('.panel-group').addClass('acco-two-active');
            
});

$(function(){
    // project section function
    // list project click
    $('#listaccountgroups').click(function(){
         $('.accountgroups-tab').removeClass('addAccountGroupsForm-active'); 
        $('#acntgrpsaddsection').slideUp('slow');// slide down the project listing div
        $('#acntgrpslistsection').slideDown('slow');// slide down the project listing div
        $('#listaccountgroups').removeClass('btn-danger').addClass('btn-success');
        $('#addaccountgroups').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../accountsmaster/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {acntgrpsname:$('#searchaccountgroups').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#acntgrpsitems').html(data.result);
                   // $('#acntgrpstable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });
    $('#acntgrpssearch').click(function(){
        $('#listaccountgroups').trigger('click')
    });

    $(document).on("keyup", "#searchaccountgroups", function(){
        $('#listaccountgroups').trigger('click')
    });
    $('#addaccountgroups').click(function(){
        $('#acntgrpslistsection').slideUp('slow');// slide down the project listing div
        $('#acntgrpsaddsection').slideDown('slow');// slide down the project listing div
        $('#addaccountgroups').removeClass('btn-danger').addClass('btn-success');
        $('#listaccountgroups').removeClass('btn-success').addClass('btn-danger');

    });
    $('#saveacntgrps').click(function(){
        var error=0;
        $('.error').hide();
        if($('#accountgroupsname').val()=='')
        {
            $("#accountgroupsname").next("span").html('Enter Account Groups Name').show('slow');
            error=1;
        }
        if(error==0){
            $.ajax({
                type:'POST',
                url:'../accountsmaster/create',
                beforeSend:function(){
                    $('#saveacntgrps').attr("disabled", true);
                },
                dataType:'json',
                data: {acntgrpsname:$('#accountgroupsname').val()},
                success:function(data){
                    if(data.error=='No')
                    {
                        $( "#acntsubgrpsaddsection" ).load(window.location.href + " #acntsubgrpsaddsection" );
                        $('#acntgrpsform')[0].reset();
                        $('#listaccountgroups').trigger('click');
                        $('#saveacntgrps').attr("disabled", false);
                        $('#searchacntgrp').append($('<option>', {
                            value: data.Id,
                            text: data.Name
                        }));
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#saveacntgrps').attr("disabled", false);
                }
            });
        }
    });
});
$(document).on( "click", ".editacntgrpsbutton", function(){
    var idval=$(this).val()
    $('#editacntgrpsname'+idval).show();
    $('#saveacntgrpsbutton'+idval).show();
    $('#acntgrpstext'+idval).hide();
    $('#editacntgrpsbutton'+idval).hide();
} );
$(document).on( "click", ".saveacntgrpsbutton", function(){
    var idval=$(this).val()
    var error=0;
    $('.error').hide();
    if($('#editacntgrpsname'+idval).val()=='')
    {
        $('#editacntgrpsname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../accountsmaster/update',
            beforeSend : function(){
                $('#saveacntgrpsbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {acntgrpsid:idval,name:$('#editacntgrpsname'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editacntgrpsname'+data.Id).hide();
                    $('#saveacntgrpsbutton'+data.Id).hide();
                    $('#acntgrpstext'+data.Id).text($('#editacntgrpsname'+data.Id).val()).show();
                    $('#editacntgrpsbutton'+data.Id).show();
                    $("select#searchacntgrp option[value='"+data.Id+"']").remove();
                    $('#searchacntgrp').append($('<option>', {
                        value: data.Id,
                        text: data.Name
                    }));

                }
                else
                {
                    alert(data.errortext);
                }

                $('#saveacntgrpsbutton'+data.Id).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deletecntgrpsbutton", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Account group ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../accountsmaster/deleteitem',
            beforeSend : function(){
                $('#deletecntgrpsbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {acntgrpsid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#acntgrpsrow'+data.Id).remove();
                    $('#listaccountgroups').trigger('click');
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deletecntgrpsbutton'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
$(document).on('click','.childacntgrps',function(){
    var resid=$(this).val();
    $('#searchacntgrp').val(resid);
    $('#listacntsubgrps').trigger('click');

    $('#identifygrp').val('accoungrps');

    $('#collapseaccntgrp').removeClass('in');

    $('.accountgroups-tab').removeClass('active');

    $('.accountsubgroup-tab').addClass('active');

    $('#collapseaccntsubgrp').addClass('in');

    $("#collapseaccntgrp").attr("aria-expanded","false");

    $("#collapseaccntsubgrp").attr("aria-expanded","true");

    $('#collapseaccntsubgrp').css('height','');
});
