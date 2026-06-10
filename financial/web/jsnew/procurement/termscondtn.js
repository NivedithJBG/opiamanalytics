$(document).on( "click", ".termscond-tab", function(){ 
	
	if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#listtermscndtn').trigger();

    //$('#termscondtnsection').slideDown('slow');

    //$('#termscondtnaddsection').hide();

    $(this).parent('.panel-group').removeClass('acco-one-active');
    $(this).parent('.panel-group').removeClass('acco-two-active');
    $(this).parent('.panel-group').removeClass('acco-three-active');
    $(this).parent('.panel-group').addClass('acco-four-active');
    $(this).parent('.panel-group').removeClass('acco-five-active');
    $(this).parent('.panel-group').removeClass('acco-six-active');
			
});


$(function(){

    $('#listtermscndtn').click(function(){
        //$('.termscond-tab').removeClass('addResourceForm-active');

        $('#termscondtnsection').slideDown('slow');// slide down the project listing div

    });


    $('.addtermscndtn').click(function(){
        

        //$('.termscond-tab').removeClass('addResourceForm-active');

        $('#termscondtnaddsection').show();

        $('#termscondtnsection').hide();

        $('#addtermscndtn').hide();

        //$('#termscondtnaddsection').show('slide', {direction: 'right'}, 1000);

        $.ajax({

            type: 'POST',

            url: '../termscondtns/addterms',

            dataType: "json",

            data: {add:0},

            success: function(data){

                if(data.error=='No')

                {

                    $('#termscondtnaddsection').html(data.result);

                }

                else

                {

                    alert(data.errortext);

                }

            }

        });

    });


});

$(document).on( "click", ".edittermstypebutton", function(){

    var termid = $(this).val();

    $('#termscondtneditsection').html('');

    $('#termscondtnsection').hide();

    $('#addtermscndtn').hide();

    $.ajax({

        type: 'POST',

        url: '../termscondtns/editterms',

        dataType: "json",

        data: {termid:termid},

        success: function(data){

            if(data.error=='No')

            {
                $('#termscondtneditsection').show();

                $('#termscondtneditsection').html(data.result);

            }

            else

            {

                alert(data.errortext);

            }

        }

    });


});

$(document).on( "click", "#saveterms", function(){

    var title = $('#termstitle').val();

    //var content = CKEDITOR.instances['termscontent'].getData();

    var error=0;

    $('.error').hide();

    if(title=='')

    {

        $('#termstitle').next("span").html('Enter Title').show('slow');

        error=1;

    }

    /*if(content=='')

    {

        $('.ck_validation').show('slow');

        error=1;

    }*/

    $('.termscontent').each(function(){
        if($('.termscontent').val()=='')
        {
            $(".termscontent").next("span").show('slow');
            error=1;
        }
    });

    if(error==0){  

        $.ajax({

            type: 'POST',

            url: '../termscondtns/create',

            beforeSend : function(){

                $('#saveterms').attr("disabled", true);

            },

            dataType: "json",

            data: $('#addtermsform').serialize(),

            success: function(data){

                if(data.error=='No')

                {

                    $( "#termscondtnsection" ).load(window.location.href + " #termscondtnsection" );

                    $('#termscondtnaddsection').hide();

                    $('#termscondtnsection').show();

                    $('#addtermscndtn').show();

                }

                else

                {

                    alert(data.errortext);

                }

                $('#saveterms').attr("disabled", false);

            }

        });

    }



});
$(document).on( "click", "#editsavetermss", function(){
   
    var termid = $('#termid').val();

    var title = $('#edittermstitle').val();
     var content = CKEDITOR.instances['edittermsscontent'].getData();

      $('.error').hide();

    

        $.ajax({

            type: 'POST',

            url: '../termscondtns/updateapprove',

            beforeSend : function(){

                $('#editsavetermss').attr("disabled", true);

            },

            dataType: "json",

            data: {termid:termid,title:title,content:content},

            success: function(data){

                if(data.error=='No')

                {

                    $( "#termscondtnsection" ).load(window.location.href + " #termscondtnsection" );

                    $('#termscondtneditsection').hide();

                    $('#termscondtnsection').show();

                    $('#addtermscndtn').show();

                }

                else

                {

                    alert(data.errortext);

                }

                $('#editsavetermss').attr("disabled", false);

            }

        });

  





});
$(document).on( "click", "#editsaveterms", function(){

    var termid = $('#termid').val();

    var title = $('#edittermstitle').val();

    //var content = CKEDITOR.instances['edittermscontent'].getData();

    var error=0;

    $('.error').hide();

    if(title=='')

    {

        $('#edittermstitle').next("span").html('Enter Title').show('slow');

        error=1;

    }

    /*if(content=='')

    {

        $('.ck_validation').show('slow');

        error=1;

    }*/

    $('.termscontent').each(function(){
        if($('.termscontent').val()=='')
        {
            $(".termscontent").next("span").show('slow');
            error=1;
        }
    });

    if(error==0){  

        $.ajax({

            type: 'POST',

            url: '../termscondtns/update',

            beforeSend : function(){

                $('#editsaveterms').attr("disabled", true);

            },

            dataType: "json",

            //data: {termid:termid,title:title,content:content},

            data: $('#edittermsform').serialize(),

            success: function(data){

                if(data.error=='No')

                {

                    $( "#termscondtnsection" ).load(window.location.href + " #termscondtnsection" );

                    $('#termscondtneditsection').hide();

                    $('#termscondtnsection').show();

                    $('#addtermscndtn').show();

                }

                else

                {

                    alert(data.errortext);

                }

                $('#editsaveterms').attr("disabled", false);

            }

        });

    }



});

$(document).on( "click", "#cancelterms", function(){

    $('#termscondtnaddsection').hide();

    $('#termscondtnsection').show();

    $('#addtermscndtn').show();

});

$(document).on( "click", ".termsaddmore", function(){

    var count = $(".mtremoveterms").length;

    var x = count + 1;
    var c = count + 2;

       $('.termscontentlist').append(
                        '<div class="col-md-12 termsrows'+x+'">'+
                        '<div class="row">'+
                        '<div class="col-md-1"></div>'+
                            '<div class="col-md-1" style="text-align: right;height: 67px;padding-top: 23px;">'+c+'</div>'+
                             
                             
                            '<div class="col-md-8">'+
                                '<div class="add-new-form-wrpr">'+
                                    '<div class="form-group">'+
                                        '<label></label>'+
                                            '<input type="text" class="form-control termscontent" placeholder="" name="termscontent[]">'+
                                            '<span class="ck_validation error" style="display: none;">Enter Content</span>'+
                                    '</div>'+
                                '</div></div>'  +                          
                            
                            '<div class="col-md-1 icon-groups">'+
                                '<a class="btn btn-primary icon-remove mtremoveterms" name="mtremoveterms" data-id="'+x+'" id="mtremoveterms" title="Remove" href="javascript:void(0)"></a>'+
                            '</div></div>'+
                            
                        '</div>');

});

$(document).on( "click", ".etermsaddmore", function(){

    var count = $(".emremoveterms").length;

    var x = count + 1;
    var c = count + 2;

       $('.edittermscontentlist').append(
                        '<div class="col-md-12 termsrows'+x+'">'+
                        '<div class="row">'+
                        '<div class="col-md-1"></div>'+
                            '<div class="col-md-1" style="text-align: right;height: 67px;padding-top: 23px;">'+c+'</div>'+
                             
                             
                            '<div class="col-md-8">'+
                                '<div class="add-new-form-wrpr">'+
                                    '<div class="form-group">'+
                                        '<label></label>'+
                                            '<input type="text" class="form-control termscontent" placeholder="" name="termscontent[]">'+
                                            '<span class="ck_validation error" style="display: none;">Enter Content</span>'+
                                    '</div>'+
                                '</div></div>'  +                          
                            
                            '<div class="col-md-1 icon-groups">'+
                                '<a class="btn btn-primary icon-remove emremoveterms" name="emremoveterms" data-id="'+x+'" id="emremoveterms" title="Remove" href="javascript:void(0)"></a>'+
                            '</div></div>'+
                            
                        '</div>');

});

$(document).on( "click", ".termsaddmoreorder", function(){

    var count = $(".removeterms").length;

    var x = count + 1;
    var c = count + 2;

       $('.termscontentlist').append(
                        '<div class="col-md-12 termsrows'+x+'">'+
                        '<div class="row">'+
                            '<div class="col-md-1"><span>'+c+'</span></div>'+
                            '<div class="col-md-8">'+
                                '<div class="add-new-form-wrpr">'+
                                    '<div class="form-group">'+
                                        
                                            '<input type="text" class="form-control termscontent" placeholder="" name="termscontent[]">'+
                                            '<span class="ck_validation error" style="display: none;">Enter Content</span>'+
                                    '</div>'+
                                '</div>'  +                          
                            '</div>'+
                            '<div class="col-md-1 icon-groups" style="min-height:unset;">'+
                                '<a class="btn btn-primary icon-remove removeterms" name="removeterms" data-id="'+x+'" id="removeterms" title="Remove" href="javascript:void(0)"></a>'+
                            '</div>'+
                            '</div>'+
                        '</div>');

});

$(document).on( "click", ".termsaddmorepurchase", function(){

    var count = $(".removeterms").length;

    var x = count + 1;
    var c = count + 2;

       $('.termscontentlist').append(
                        '<div class="col-md-12 termsrows'+x+'">'+
                            '<div class="row">'+
                            '<div class="col-md-1"><span>'+c+'.</span></div>'+
                            '<div class="col-md-8">'+
                                '<div class="add-new-form-wrpr">'+
                                    '<div class="form-group">'+
                                        
                                            '<input type="text" class="form-control termscontent" placeholder="" name="termscontent[]">'+
                                            '<span class="ck_validation error" style="display: none;">Enter Content</span>'+
                                    '</div>'+
                                '</div>'  +                          
                            '</div>'+
                            '<div class="col-md-1 icon-groups" style="min-height:unset;">'+
                                '<a class="btn btn-primary icon-remove removeterms" name="removeterms" data-id="'+x+'" id="removeterms" title="Remove" href="javascript:void(0)"></a>'+
                            '</div></div>'+
                        '</div>');

});

$(document).on( "click", ".removeterms", function(){

    var rowID = $(this).attr("data-id");

    $('.termsrows'+rowID).remove();

});
$(document).on( "click", ".mtremoveterms", function(){

    var rowID = $(this).attr("data-id");

    $('.termsrows'+rowID).remove();

});
$(document).on( "click", ".emremoveterms", function(){

    var rowID = $(this).attr("data-id");

    $('.termsrows'+rowID).remove();

});

$(document).on( "click", "#editcancelterms", function(){

    $('#termscondtneditsection').hide();

    $('#termscondtnsection').show();

    $('#addtermscndtn').show();

});

$(document).on( "click", ".deletetermstypebutton", function(){

    var idval=$(this).val();

    var r = confirm("Are you sure you want to delete this terms & condition?");

    if (r == true) {



        $.ajax({

            type: 'POST',

            url: '../termscondtns/delete',

            beforeSend : function(){

                $('#deletetermstypebutton'+idval).attr("disabled", true);

            },

            dataType: "json",

            data: {termsid:idval},

            success: function(data){

                if(data.error=='No')

                {

                    $( "#termscondtnsection" ).load(window.location.href + " #termscondtnsection" );

                }

                else

                {

                    alert(data.errortext);

                }



                $('#deletetermstypebutton'+idval).attr("disabled", false);

            }

        });



    } else {

        return false;

    }



});  
