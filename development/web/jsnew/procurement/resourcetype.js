$(document).on( "click", "#Resourcetype", function(){ 
	
	if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#listrestype').trigger('click');

	$(this).parent('.panel-group').addClass('acco-one-active');
	$(this).parent('.panel-group').removeClass('acco-two-active');
	$(this).parent('.panel-group').removeClass('acco-three-active');
	$(this).parent('.panel-group').removeClass('acco-four-active');
	$(this).parent('.panel-group').removeClass('acco-five-active');
	$(this).parent('.panel-group').removeClass('acco-six-active');
	
	
});


$(function(){

    // project section function

    // list project click

    $('#listrestype').click(function(){

        $('.search-and-actions-wrpr').show();

        $('.content-action-wrpr').show();

        $('.resourcetype-tab').removeClass('addResourceForm-active');
        $('#restypeaddsection').slideUp('slow');// slide down the project listing div

        $('#restypelistsection').slideDown('slow');// slide down the project listing div

        $('#listrestype').removeClass('btn-danger').addClass('btn-success');

        $('#addrestype').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../resourcetype/search',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {restypename:$('#searchrestypename').val()},

            success: function(data){

                if(data.error=='No')

                {

                   $('#restypelistsection').html(data.result);
                  // $('#editbutton').html(data.editsavebutton);

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });



    });

    $('#resourcetypesearch').click(function(){

        $('#listrestype').trigger('click')

    });
    $(document).on("keyup", "#searchrestypename", function(){
        $('#listrestype').trigger('click')
    });
    $('#cancelrestype').click(function(){

        $('.resourcetype-tab').removeClass('addResourceForm-active');
        $('#restypeaddsection').hide();
        $('#restypelistsection').show();
        $('.content-action-wrpr').show();

    });


     $('#cancelrestypes').click(function(){

        $('.resourcetype-tab').removeClass('editResourceForm-active');
        $('#restypeaddsection').hide();
        $('#restypelistsection').show();
        $('.search-and-actions-wrpr').show();
        $('.content-action-wrpr').show();

    });
    // list project click  \

    // add project click

    $('#addrestype').click(function(){

        $('#addrestypeform')[0].reset();

        $('#restypelistsection').slideUp('slow');// slide down the project listing div

        $('#restypeaddsection').slideDown('slow');// slide down the project listing div

        $('#addrestype').removeClass('btn-danger').addClass('btn-success');

        $('#listrestype').removeClass('btn-success').addClass('btn-danger');



    });

    // add project click

    // save project click

    $(document).on( "click", "#saverestype", function(){ 

        var error=0;

        $('.error').hide(); 
        if($('#restypenameshw').val()==''){
            
            $("#restypenameshw").next("span").html('Enter Resource Type Name').show('slow');
            error=1;
            /*var reName = $('#restypename1').val();*/
        }/*else{
            var reName = $('#restypename').val();
        } */

        if($('#addacntgrp').val()==''){
            
            $("#addacntgrp").next("span").html('Select Account Group').show('slow');
            error=1;
            /*var reName = $('#restypename1').val();*/
        }

        if($('#projecttype').val()==0){
            
            $("#projecttype").next("span").html('Select Order Type').show('slow');
            error=1;
        }
       /* if(reName=='')

        {

            $("#restypename").next("span").html('Enter Resource Type Name').show('slow');
            $("#restypename1").next("span").html('Enter Resource Type Name').show('slow');
            error=1;

        }*/

       /* if($('#rstpaccountsub').val()=='')

        {

            $("#rstpaccountsub").next("span").html('Select Account Sub Group').show('slow');

            error=1;

        }        
*/
        if(ResourceTypeNameExists($('#restypenameshw').val())=='Yes')

        {

            $("#restypenameshw").next("span").html('Resource Type Exists').show('slow');
            /*$("#restypename1").next("span").html('Resource Type Exists').show('slow');
*/
            error=1;

        }
        
        if(error==0){

            $.ajax({

                type:'POST',

                url:'../resourcetype/create',

                beforeSend:function(){

                    $('#saverestype').attr("disabled", true);

                },

                dataType:'json',

                //data: {restypename:reName,sourceunit:$('#unitsource').val(),ordertype:$('#projecttype').val()},
                data : $('#addrestypeform').serialize(),

                success:function(data){

                    if(data.error=='No')

                    {
                        $( "#restyperefresh" ).load(window.location.href + " #restyperefresh" );

                        $( "#resourceeaddsections" ).load(window.location.href + " #resourceeaddsections" );

                        $('#addrestypeform')[0].reset();

                        $('.search-and-actions-wrpr').show();

                        $('.content-action-wrpr').show();

                        $('#listrestype').trigger('click');

                        $('#saverestype').attr("disabled", false);

                        $('#searcselecttype').append($('<option>', {

                            value: data.Id,

                            text: data.Name

                        }));

                    }

                    else

                    {

                        alert(data.errortext);

                    }

                    $('#saverestype').attr("disabled", false);

                }

            });

        }


    });

    // save project click

    //project function ends here

    $( "#restypeitems" ).sortable({

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

                url: '../resourcetype/updatesort',

                data: {datavalue:updatedrows},

                dataType: "json",

                success: function(data){

                    if(data.error=='No')

                    {

                        $('#listrestype').trigger('click');

                    }



                }

            });

        }



    }).disableSelection()

});

// edit resource button click function

$(document).on( "click", ".editresourtypebutton", function(){
    $('.search-and-actions-wrpr').hide();
    $('.content-action-wrpr').hide();
   // $('.resourcetype-tab').addClass('addResourceForm-active');
    $('.resourcetype-tab').addClass('editResourceForm-active');
    var idval=$(this).val();
    var ordertype= $('#resordtypeidd'+idval).val();
    var subgroup= $('#resordtypesubidd'+idval).val();
    var name= $('#resordtypenames'+idval).val();
    var accntgroup = $('#resaccntgroup'+idval).val();
    //var button=$('#saveresourcetypebutton'+idval).val();
    $('#rstpaccountsubgroup').val(subgroup);
    $('#projecttypes').val(ordertype);
    $('#restypenames_new').val(name);
    $('#saveresourcetypeval').val(idval);
    if(accntgroup!=''){
        $('#editacntgrp').val(accntgroup);
    } 
    $('#restypeaddsection').show();
    $('#restypelistsection').hide();

} );


// save edited resources function

$(document).on( "click", "#saveresourcetypebutton", function(){  

    var idval= $('#saveresourcetypeval').val(); 
    

    var error=0;

    $('.error').hide();

    if($('#editresourtypename'+idval).val()=='')

    {

        $('#editresourtypename'+idval).next("span").html('Enter Name').show('slow');

        error=1;

    }
     if($('#restypenames_new').val()==''){
        $('#restypenames_new').next("span").html('Enter Resource Type').show('slow');

        error=1;
     }

     if($('#editacntgrp').val()==''){
        $('#editacntgrp').next("span").html('Select Account Group').show('slow');

        error=1;
     }

    if(error==0){  

        $.ajax({

            type: 'POST',

            url: '../resourcetype/update',

            beforeSend : function(){

                $('#saveresourcetypebutton').attr("disabled", true);

            },

            dataType: "json",

            data: {restypeid:idval,name:$('#restypenames_new').val(),projtype:$('#projecttypes').val(),accntgrp:$('#editacntgrp').val()},

            success: function(data){

                if(data.error=='No')

                { 
                   // alert(data.test)
                    
                    $('#saveresourcetypebutton').attr("disabled", false);
                    $('.resourcetype-tab').removeClass('editResourceForm-active');
                    $('#restypeaddsection').hide();
                    $('#restypelistsection').show();
                    
                  $('#listrestype').trigger('click')
                

                }

                else

                {

                    alert(data.errortext);

                }
            

            }

        });

    }



} );


$(document).on( "click", ".deletresourcetypebutton", function(){

    var idval=$(this).val();

    var r = confirm("Are you sure you want to delete this Resource type?");

    if (r == true) {



        $.ajax({

            type: 'POST',

            url: '../resourcetype/deleteitem',

            beforeSend : function(){

                $('#deletresourcetypebutton'+idval).attr("disabled", true);

            },

            dataType: "json",

            data: {restypeid:idval},

            success: function(data){

                if(data.error=='No')

                {

                    $('#resourcetyperow'+data.Id).remove();

                    $("select#searcselecttype option[value='"+data.Id+"']").remove();

                }

                else

                {

                    alert(data.errortext);

                }



                $('#deletresourcetypebutton'+data.Id).attr("disabled", false);

            }

        });



    } else {

        return false;

    }



});

$(document).on('click','.childresourcegroups',function(){ 

    var restypeid=$(this).val();

    $('#searcselectrestype').val(restypeid);

    $('#restyperes').val(restypeid);

    $('.res-tab').trigger('click');

});

$(document).on('click','.viewproducts',function(){

    $('#Products').trigger('click');

});

function ResourceTypeNameExists(name)

{

    var retval;

    $.ajax({

        type: 'POST',

        url: '../resourcetype/checkname',

        async:false,

        data: {name:name},

        success: function(data){

            retval=data;

        }

    });

    return retval;

}
$(function() {
$( "#restypelistsection" ).sortable({
        placeholder: "ui-state-highlight",
        helper:'clone',
        
        update:function( event, ui ) {
            //alert($(this).index());

            var updatedrows=[];
            $('.restypesort').each(function() {
                 var rowid=$(this).attr('data-id');

                var rowindex=$(this).index();

                updatedrows.push({

                    rowid: rowid,

                    rowindex:rowindex

                })

            });
            $.ajax({
                type: 'POST',
                url: '../resourcetype/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection();
});
