$(document).on( "click", "#projtitle", function(){ 

    $('.acco-one').addClass('active');
    $('.acco-two').removeClass('active');
    $('.acco-three').removeClass('active');
    $('.acco-four').removeClass('active');

    $('.acco-five').removeClass('active');

    
        $('.search-and-actions-wrpr').hide();
        $('#projectitems').hide(); 
   
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
   }
   if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
       
   }
   $('#editwindow').hide();
   $('#listprojects').trigger('click');
});
$(document).on( "click", ".listprj", function(){
    $('#projectsearch').val('');
    $('#projectitems').show();
    $('.search-and-actions-wrpr').show(); 
    $('#addprjfrm').hide(); 
    $('#projects-list-body').hide();
    $('#listproject').trigger('click');
});

$(document).on( "click", "#selected-projctid", function(){
    $('#projectitems').hide();
    $('#addwindow').hide();
    $('.search-and-actions-wrpr').hide(); 
    $('#addprjfrm').show(); 
    $('#projects-list-body').show();
});

$(document).on( "click", ".closeprjct", function(){
    $('#addprjfrm').show();
    $('#projects-list-body').show();
    $('#projectitems').hide();
    $('.search-and-actions-wrpr').hide();
    $('#listprojects').trigger('click');
    });
$(function(){

// project section function
    // list project click
    $('#listprojects').click(function(){
        
        $.ajax({
            type: 'POST',
            url: '../projectsmain/search',
            beforeSend : function(){
                //$('#listproject').attr("disabled", true);
                $('#Promain-preloader-Listprojects').show();
            },
            dataType: "json",
            data: {projectname:''},
            success: function(data){
                if(data.error=='No')
                {
                    $('#projects-list-body').html(data.result);
                    //$('#projecttable').show();
                }
                else
                {
                    alert(data.errortext);
                }

                //$('#projectsearch').attr("disabled", false);
                $('#Promain-preloader-Listprojects').hide();
            }
        });

    });
    // list project click  





});

$(document).on("click",'.viewnotes',function(){

    var id=$(this).attr('data-id');

    var name=$(this).attr('data-name');
    $('#notesitems').html(name);
    $('#currentproj').val(id);
    $.ajax({
        type: 'POST',
        url: '../projectsmain/notes',
        beforeSend : function(){

            $('#Promain-preloader-Listprojects-model').show();
        },
        dataType: "json",
        data: {id:id},
        success: function(data){
            if(data.error=='No')
            {
                $('#notesshow').html(data.result);
                $('#notesprojectname').html(data.project);
            }
            else
            {
                alert(data.errortext);
            }


            $('#Promain-preloader-Listprojects-model').hide();
        }
    });
});
$(document).on("click",'#savenotes',function(){
    var notes=$('#notes').val();
    var currentproj=$('#currentproj').val();

    $.ajax({
        type: 'POST',
        url: '../projectsmain/savenotes',

        dataType: "json",
        data: {notes:notes,currentproj:currentproj}

    });
});
    
$(document).on( "click", ".view_estimate", function(){

    //$('.view_estimate').trigger('click');
    var id= $(this).data("id");
    $('#estimateProject_Id').val(id);

    $("span#projectname").html(getProjectname(id));

    $('#listestimateitems').trigger('click') ;
});

$(function(){
    $('#listestimateitems').click(function(){ 
        $.ajax({
            type: 'POST',
            url: '../projectsmain/listestimateitems',
            beforeSend : function(){
                $('.preloaderitems').show();
                $('.preloader').show();

            },
            dataType: "json",
            data: {projectid:$('#estimateProject_Id').val(),estimatestrucid:$('#estimatestructurelist').val()},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#estimatestructurelist').html(data.result);
                    $('#estimateProject_Id').val(data.projectid);
                    $("span#projectname").html(getProjectname(data.projectid));
                    $('#processwise').html(data.result);
                    $('#projectvalue').val(data.projectvalue);
                    $('#estimateitems').html(data.items);
                    $('#estimatetable').show();
                    //saveProduct(0);
                }

                $('.preloaderitems').hide();
                $('.preloader').hide();
            }
        });
    });
});

$(document).on( "click","#saveproduct", function(){
    var error = true;
    $(".quantity").each(function() {
        //if($(this).val() == '' || $(this).val() == 0)
        if($(this).val() != '' && parseInt($(this).val()) != 0)
            error=false;
    });

    if(error)
        alert("Please Enter a Valid Quantity");
    else
        saveProduct(1);
});

function saveProduct(refreshFlag){
    $.ajax({
        type: 'POST',
        url: '../projectsmain/pricingestimate',
        beforeSend : function(){
            $('#saveproduct').attr("disabled", true);
        },
        dataType: "json",
        data: $('#pricingestimateform').serialize(),
        success: function(data){
            if(data.error=='No')
            {
                if(refreshFlag){
                    $('.confirm-purchase').html('Saved Successfully!');
                    $(".confirm-purchase").show().delay(5000).fadeOut();
                    $('#listestimateitems').trigger('click') ;
                }
            }
            if(data.error=='yes')
            {
                //alert('Please verify the resource allocations for '+data.result);
                alert('Please verify the resource allocations for all activities to proceed.');
            }

            $('#saveproduct').attr("disabled", false);
        }
    });
}

$(document).on( "click",".removeiowitem", function(){   
    
    var actid=$(this).data("id");


    var r = confirm("Are you sure you want to delete this Activity ?");

    if (r == true) {
        $.ajax({

            type:'POST',

            url:'../projectsmain/removeitem',

            beforeSend:function(){

                $('#removeiowitem'+actid).attr("disabled",true);

            },

            dataType:"json",

            data:{actid:actid},

            success:function(data){

                if(data.error=='No')

                {
                    $('#listestimateitems').trigger('click') ;
                    //$('#tempprodrow'+actid).remove();
                    //$('#Estimateiowactivitiesrow'+actid).remove();

                }
                else
                {
                    alert(data.result);
                }

                $('#removeiowitem'+actid).attr("disabled",false);

            }

        });
    }

    
});

function getProjectname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/getname',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}

$(document).on("click",'.projselctid',function(event){

    if(event.target.classList.contains('holidayPopup'))
        event.stopPropagation();
    else if($(event.target).closest('.card-upload-area').length)
        return;
    else{
        var prjctid=$(this).attr("data-id");
        $.ajax({
            type: 'POST',
            url: '../projectsmain/userprojectmain',
            dataType:"json",
            data: {prjctid:prjctid},
            success: function(data){
                if(data.error=='No') 
                {
                    $('#selected-projctid').html(data.result);
                    location.reload();
                }
            }
        });
    }
});
