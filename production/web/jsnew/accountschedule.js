$(document).on('click','#Accountschedules',function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listacntschedules').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});

$(function(){ 
    $('#addacntgrpschedule').change(function(){
        var accountgroup=$(this).val();
        $.ajax({
            type: 'POST',
            url: '../accountschedule/getsubgroups',                        
            data: {accountgroup:accountgroup},
            success: function(data){
                $('#addacntsubgrpsch').html(data);                
            }
        });        
    });
    $('#searchschedacntgrp').change(function(){
        var accountgroup=$(this).val();
        $.ajax({
            type: 'POST',
            url: '../accountschedule/getsubgroups',                        
            data: {accountgroup:accountgroup},
            success: function(data){
                $('#searchacntsubgrp').html(data);                
            }
        });        
    }); 
    $('#groupid').change(function(){
        var accountgroup=$(this).val();
        $.ajax({
            type: 'POST',
            url: '../accountschedule/getsubgroups',                        
            data: {accountgroup:accountgroup},
            success: function(data){
                $('#subgroupid').html(data);                
            }
        });        
    });        
     
    $('#saveaccountschedule').click(function(){
            var error=0;
            $('.error').hide();
            if($('#addacntgrpschedule').val()=='none')
            {
                $("#addacntgrpschedule").next("span").html('Select Account Group').show('slow');
                error=1;
            }
            if($('#addacntsubgrpsch').val()=='')
            {
                $("#addacntsubgrpsch").next("span").html('Select Account Sub-Group').show('slow');
                error=1;
            } 
    
            if($('#accountschedulesname').val()=='')
            {
                $("#accountschedulesname").next("span").html('Enter Account Schedule Name').show('slow');
                error=1;
            }                
            /*if(ResourceTypeNameExists($('#restypename').val())=='Yes')
             {
             $("#restypename").next("span").html('Resource Type Exists').show('slow');
             error=1;
             }*/
            if(error==0){
                $.ajax({
                    type:'POST',
                    url:'../accountschedule/create',
                    beforeSend:function(){
                        $('#saveaccountschedule').attr("disabled", true);
                    },
                    dataType:'json',
                    data: {accountgroup:$('#addacntgrpschedule').val(),accountsubgroup:$('#addacntsubgrpsch').val(),accountschedule:$('#accountschedulesname').val()},
                    success:function(data){
                        if(data.error=='No')
                        {
                            $('#acntschedulesform')[0].reset();
                            $('#listacntschedules').trigger('click');
                            $('#saveaccountschedule').attr("disabled", false);
                            /*$('#accountgroups').append($('<option>', {
                             value: data.Id,
                             text: data.Name
                             }));*/
                        }
                        else
                        {
                            alert(data.errortext);
                        }
                        $('#saveaccountschedule').attr("disabled", false);
                    }
                });
            }
        });   
    $('#acntschedulessearch').click(function(){
        $('#listacntschedules').trigger('click')
    });           
    // project section function
    // list project click
    $('#listacntschedules').click(function(){
        $('#acntschedulesaddsection').slideUp('slow');// slide down the project listing div
        $('#acntscheduleslistsection').slideDown('slow');// slide down the project listing div
        $('#listacntschedules').removeClass('btn-danger').addClass('btn-success');
        $('#addacntschedules').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../accountschedule/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {acntgrp:$('#searchschedacntgrp').val(),acntsubgrp:$('#searchacntsubgrp').val(),acntschedulesname:$('#searchacntschedules').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#acntschedulesitems').html(data.result);
                    $('#acntschedulestable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });

    $('#addacntschedules').click(function(){
        $('#acntscheduleslistsection').slideUp('slow');// slide down the project listing div
        $('#acntschedulesaddsection').slideDown('slow');// slide down the project listing div
        $('#addacntschedules').removeClass('btn-danger').addClass('btn-success');
        $('#listacntschedules').removeClass('btn-success').addClass('btn-danger');

    });
    
    $( "#acntschedulesitems" ).sortable({
        items: '.no',
        update:function( event, ui ) {
            //alert($(this).index());
            var updatedrows=[];
            $(this).closest('table').find('tbody tr').each(function (i) {
                var rowid=$(this).attr('data-id');
                var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    rowindex:rowindex
                })
            });
            $.ajax({
                type: 'POST',
                url: '../accountschedule/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#listacntschedules').trigger('click');
                    }

                }
            });
        }

    }).disableSelection()
});
$(document).on( "click", ".editacntschedulesbutton", function(){
    var idval=$(this).val();
    $('#editacntschedulesname'+idval).show();
    $('#saveacntschedulesbutton'+idval).show();
    $('#editacntgrp'+idval).show();
    $('#acntschedulestext'+idval).hide();
    $('#acntgrpstext'+idval).hide();
    $('#editacntschedulesbutton'+idval).hide();
} );
$(document).on( "click", ".saveacntschedulesbutton", function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editacntschedulesname'+idval).val()=='')
    {
        $('#editacntschedulesname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }

    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../accountschedule/update',
            beforeSend : function(){
                $('#saveacntschedulesbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {acntschedulesid:idval,name:$('#editacntschedulesname'+idval).val(),acntgrp:$('#editacntgrp'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editacntschedulesname'+data.Id).hide();
                    $('#saveacntschedulesbutton'+data.Id).hide();
                    $('#editacntgrp'+data.Id).hide();
                    $('#acntschedulestext'+data.Id).text($('#editacntschedulesname'+data.Id).val()).show();
                    $('#acntgrpstext'+data.Id).text($('#editacntgrp'+idval+' option:selected').text()).show();
                    $('#editacntschedulesbutton'+data.Id).show();
                    $("select#accountgroups option[value='"+data.Id+"']").remove();
                    $('#accountgroups').append($('<option>', {
                     value: data.Id,
                     text: data.Name
                     }));

                }
                else
                {
                    alert(data.errortext);
                }

                $('#saveacntschedulesbutton'+data.Id).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deleteacntschedulesbutton", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Account Schedule ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../accountschedule/deleteitem',
            beforeSend : function(){
                $('#deleteacntschedulesbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {acntschedulesid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#acntschedulesrow'+data.Id).remove();
                    $('#listacntschedules').trigger('click');
                    //$("select#searcselecttype option[value='"+data.Id+"']").remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deleteacntschedulesbutton'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
$(document).on('click','.childaccounts',function(){
    var subgrpid=$(this).val();
    $('#accountsubgrp').val(subgrpid);
    $('#Accounts').trigger('click');
});
