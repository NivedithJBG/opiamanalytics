$(document).on("click", ".prjct-tab",function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#addwindow').hide();
    $('#editwindow').hide();
    $('.search-and-actions-wrpr').show();
    $('#projectitems').show();
    $('#listproject').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});

$(function(){

    // project section function
    // list project click
  
    $('#listproject').click(function(){ 
        $('#projectaddsection').slideUp('slow');// slide down the project listing div
        $('#projectlistsection').slideDown('slow');// slide down the project listing div
        $('#listproject').removeClass('btn-danger').addClass('btn-success');
        $('#addproject').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../projects/inactivesearch',
            beforeSend : function(){
                $('#projectsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectname:''},
            success: function(data){
                if(data.error=='No')
                {
                    $('#projectitems').html(data.result);
                    $('#projecttable').show();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#projectsearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });

    });
    // list project click  \
    // add project click
    $('#addproject').click(function(){
        $('#projectlistsection').slideUp('slow');// slide down the project listing div
        $('#projectaddsection').slideDown('slow');// slide down the project listing div
        $('#addproject').removeClass('btn-danger').addClass('btn-success');
        $('#listproject').removeClass('btn-success').addClass('btn-danger');

    });
    // add project click
    // save project click
    $('#saveproject').click(function(){

        var error=0;
        $('.error').hide();
        if($('#projectnameover').val()=='')
        { 
            $("#projectnameover").next("span").html('Enter Project Name').show('slow');
            error=1;
        }
        if($('#startdate').val()=='')
        {
            $("#startdate").next("span").html('Select Start Date').show('slow');
            error=1;
        }
        if($('#enddate').val()=='')
        {
            $("#enddate").next("span").html('Select End Date').show('slow');
            error=1;
            /*alert('ed');*/
        }
        if($('#duration').val()=='')
        {
            $("#duration").next("span").html('Enter Duration').show('slow');
            error=1;
        }

        if($('#projectvalueover').val()=='')
        {
            $("#projectvalueover").next("span").html('Enter Project Value').show('slow');
            error=1;
        }

        if($('#clientname').val()=='')
        {
            $("#clientname").next("span").html('Enter Client Name').show('slow');
            error=1;
        }
        if($('#wrkhrs').val()=='')
        {
            $("#wrkhrs").next("span").html('Enter Work hours').show('slow');
            error=1;
        }
        /*if (!$('input[name=account]:checked').val() ) {
         $(".account").next("span").html('Select Cash or bank').show('slow');
         error=1;
         }*/
        if(ProjectNameExists($('#projectnameover').val())=='Yes')
        {
            $("#projectnameover").next("span").html('Project Name Exists').show('slow');
            error=1;
        }
        /*if(AccountName($('#cashaccountname').val())=='Yes')
         {
         $('#cashaccountname').next("span").html('Account Name Exists').show('slow');
         error=1;
         }
         if(AccountName($('#bankaccountname').val())=='Yes')
         {
         $('#bankaccountname').next("span").html('Account Name Exists').show('slow');
         error=1;
         }*/

        
        projectValue = $('#projectvalueover').val().replace(/,/g, '');
        if(error==0){
            $.ajax({
                type:'POST',
                url:'../projects/create',
                beforeSend:function(){
                    $('#saveproject').attr("disabled", true);
                },
                dataType:'json',
                data: {projectname:$('#projectnameover').val(),projectvalue:projectValue,clientname:$('#clientname').val(),duration:$('#duration').val(),enddate:$('#enddate').val(),startdate:$('#startdate').val(),wrkhrss:$('#wrkhrs').val(),cashaccount:$('#cashaccount').val(),bankaccount:$('#bankaccount').val()},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#addprojectform')[0].reset();
                        $("#addwindow").hide();
                        $(".content-action-wrpr").show();
                        $(".project-fav-cards-cntnr").show();
                        $('#saveproject').attr("disabled", false);
                        $('.projectTitle').html('Projects - '+data.Name);
                        $('#selected-projctid').html('<h4 class="panel-title" id="projtitle"><a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapseprojindex"><span class="icon-note1"></span>Projects - '+data.Name+'</a></h4>');
                        $('#canceladding').trigger('click');

                        /*$('#projectaddsection').slideUp('slow');// slide down the project listing div
                         $('#projectlistsection').slideDown('slow');// slide down the project listing div
                         $('#listproject').removeClass('btn-danger').addClass('btn-success');
                         $('#addproject').removeClass('btn-success').addClass('btn-danger');
                         $('#addprojectform')[0].reset();
                         $('#addproject').trigger('click');
                         $('#projectsearch').trigger('click')

                         $('#resourcevalueadd').toggle('slow');
                         $('#searchdiv').toggle('slow');
                         $('#resourcetable').toggle('slow');*/
                        /*window.location = '../projects/'+data.Id;*/
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#saveproject').attr("disabled", false);
                }
            });
        }



    });

$(document).on('click','.editprojectbutton',function(){
    $('#projectFormTitle').html('Edit Project');
    var idval=$(this).data('id');
    $('#savess').val(idval);
    var error=0;
    $('.error').hide();
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../projects/updatesave',
            beforeSend : function(){
               
            },
            dataType: "json",
            data: {projectid:idval},
            success: function(data){
                 // alert(data.durat);
                    $("#addwindow").hide();
                    $("#editwindow").show();
                    $('#durationn').val(data.durat);
                    $('#projectnames').val(data.prjname);
                    $('#projectvalues').val(data.prvalue);
                    $('#clientnames').val(data.clientnamesss);
                    $('#wrkhrss').val(data.wrkhrss);
                    $('#startdatee').val(data.strtdate);
                    $('#enddatee').val(data.endddate);
                    $('#editcashaccount').html(data.cashrow);
                    $('#editbankaccount').html(data.bankrow);
                    

                   

                }
            });
        }
});


    $(document).on('click','#saveeditproject',function(){
    var idval=$('#savess').val();
     
    var error=0;
    $('.error').hide();
    
        if(error==0){
        $.ajax({
            type: 'POST',
            url: '../projects/edit',
            beforeSend : function(){
                //$('#saveeditproject'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {projectid:idval,projectname:$('#projectnames').val(),startdate:$('#startdatee').val(),enddates:$('#enddatee').val(),duration:$('#durationn').val(),projectvalue:$('#projectvalues').val(),clientname:$('#clientnames').val(),wrkhrs:$('#wrkhrss').val(),cashaccount:$('#editcashaccount').val(),bankaccount:$('#editbankaccount').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editprojectform')[0].reset();
                    $("#editwindow").hide();
                    $(".search-and-actions-wrpr").show();
                        $(".project-master-cntnt-wrpr").show();
                    //$('.project-name'+data.Id).text($('#editproject'+data.Id).val()).show();
                    $('#selected-projctid').html('<h4 class="panel-title" id="projtitle"><a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapseprojindex"><span class="icon-note1"></span>Projects - '+data.Name+'</a></h4>');
                    $('#canceladding').trigger('click');
                    $('#listproject').trigger('click');
                    //$('#editprojectbutton'+data.Id).show();




                }

            }
            });
        }
   
});

    $('#listprojectInactive').click(function(){
        $('#projectaddsection').slideUp('slow');// slide down the project listing div
        $('#projectlistsection').slideDown('slow');// slide down the project listing div
        $('#listproject').removeClass('btn-danger').addClass('btn-success');
        $('#addproject').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../projects/search',
            beforeSend : function(){
                $('#projectsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectname:''},
            success: function(data){
                if(data.error=='No')
                {
                    $('#projectitems').html(data.result);
                    $('#projecttable').show();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#projectsearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });

    });

});
$(document).on('click','#editproject',function(){
    var error=0;
    $('.error').hide();
    if($('#projectname').val()=='')
    {
        $('#projectname').next("span").html('Enter Project Name').show('slow');
        error=1;
    }



    if(error==1)
    {
        return false;
    }
    else
    {
        return true;
    }
});
$(document).on( "click", ".remove_account", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Account?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../../projects/deleteaccount',
            beforeSend : function(){
                $('#remove_account'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {accountid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#bankaccount'+data.Id).remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#remove_account'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
$(document).on( "click", ".editprojectbutton", function(){
    
 var idval=$(this).val()
 $('#editwindow'+idval).show();
 $(".search-and-actions-wrpr").hide();
 $(".project-master-cntnt-wrpr").hide(); 
 });
// edit resource button click function
/*$(document).on( "click", ".editprojectbutton", function(){
 var idval=$(this).val()
 $('#editproject'+idval).show();
 $('#saveprojectbutton'+idval).show();
 $('#projecttext'+idval).hide();
 $('#editprojectbutton'+idval).hide();
 } );*/

// save edited resources function
/*$(document).on( "click", ".saveprojectbutton", function(){
 var idval=$(this).val()
 var error=0;
 $('.error').hide();
 if($('#editproject'+idval).val()=='')
 {
 $('#editproject'+idval).next("span").html('Enter Name').show('slow');
 error=1;
 }
 if(error==0){
 $.ajax({
 type: 'POST',
 url: '../projects/update',
 beforeSend : function(){
 $('#saveprojectbutton'+idval).attr("disabled", true);
 },
 dataType: "json",
 data: {projectid:idval,name:$('#editproject'+idval).val()},
 success: function(data){
 if(data.error=='No')
 {
 $('#editproject'+data.Id).hide();
 $('#saveprojectbutton'+data.Id).hide();
 $('#projecttext'+data.Id).text($('#editproject'+data.Id).val()).show();
 $('#editprojectbutton'+data.Id).show();

 }
 else
 {
 alert(data.errortext);
 }

 $('#saveprojectbutton'+data.Id).attr("disabled", false);
 }
 });
 }

 } );*/


$(document).on( "click", ".deleteprojectbutton", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this project?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../projects/checkaccount',
            /*beforeSend : function(){
             $('#deleteprojectbutton'+idval).attr("disabled", true);
             },*/
            //dataType: "json",
            data: {projectid:idval},
            success: function(data){
                if(data=='No')
                {
                    $.ajax({
                        type: 'POST',
                        url: '../projects/deleteproject',
                        beforeSend : function(){
                            $('#deleteprojectbutton'+idval).attr("disabled", true);
                        },
                        dataType: "json",
                        data: {projectid:idval},
                        success: function(data){
                            if(data.error=='No')
                            {
                                $('#projectrow'+data.Id).remove();
                            }
                            else
                            {
                                alert(data.errortext);
                            }

                            $('#deleteprojectbutton'+data.Id).attr("disabled", false);
                        }
                    });
                }
                else
                {
                    alert('Cannot Delete this project.Cash or bank account is linked with this project');
                }

            }
        });

    } else {
        return false;
    }

});
$(document).on( "click", ".activate", function(){
    var idval=$(this).data("id");
    var r = confirm("Are you sure you want to activate this project?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../projects/activateproject',
            data: {projectid:idval},
            beforeSend : function(){

            },
            dataType: "json",
            data: {projectid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#deactivebutton'+data.Id).show();
                    $('#activebutton'+data.Id).hide();

                }
                else
                {

                }


            }
        });

    } else {
        return false;
    }

});
$(document).on( "click", ".deactivate", function(){
    var idval=$(this).data("id");
    var r = confirm("Are you sure you want to deactivate this project?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../projects/deactivateproject',
            data: {projectid:idval},
            beforeSend : function(){

            },
            dataType: "json",
            data: {projectid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#activebutton'+data.Id).show();
                    $('#deactivebutton'+data.Id).hide();
                    $('#favourites'+idval).removeClass('added-to-fav');
                    $('#favourites'+idval).data('value',0);

                }
                else
                {

                }


            }
        });

    } else {
        return false;
    }

});
function ProjectNameExists(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/checkname',
        async:false,
        data: {name:name},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}
function AccountName(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/checkaccountname',
        async:false,
        data: {name:name},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}

$(document).on( "click", ".cancel", function(){ //athira   alert("hhh");
    $("#addwindow").hide();
    $("#editwindow").hide();

    $(".search-and-actions-wrpr").show();
    $(".project-master-cntnt-wrpr").show();
    $('#listproject').trigger('click');
});



$(document).on( "click", ".addForm", function(){
    $('#projectFormTitle').html('Add Project');
    $('#addprojectform')[0].reset();
    $("#addwindow").show();
    $(".content-action-wrpr").hide();
    $(".project-fav-cards-cntnr").hide();
    $("#editwindow").hide();
    //$('.resourcetype-tab').addClass('addResourceForm-active');
    //$('.resourcetype-tab').removeClass('editResourceForm-active');
});


$(document).on( "click", ".canceladding", function(){
    $("#addwindow").hide();
    $('.search-and-actions-wrpr').hide();
    //$('.search-and-actions-wrpr').css('display', 'none !important'); 
    $('#projectitems').hide();
    // $('#projectitems').css('display', 'none !important');
    $(".content-action-wrpr").show();
    $("#projects-list-body").show();
    $('#listprojects').trigger('click');
});

$(document).on( "click", "#searchproject", function(){


   /*if($('#projectsearch').val()=='')
    {

        //$("#projectsearch").val('Enter Project Name').show('slow');
            error=1;
    }
    else

    {
        error=0;
    }*/

    error=0;

    if(error==0)
    {
        var projectname = $('#projectsearch').val();
        $.ajax({
            type: 'POST',
            url: '../projects/inactivesearch',
            beforeSend : function(){
                $('#projectsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectname:projectname},
            success: function(data){
                if(data.error=='No')
                {
                    $('#projectitems').html(data.result);
                    $('#projecttable').show();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#projectsearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });
    }
    });

$(document).on( "click", ".starred", function(){
    var project_id = $(this).data("id");
    var val = $(this).data("value");
       $.ajax({
        type: 'POST',
        url: '../projects/favourite',
        async:false,
        data: {project_id:project_id,val:val},
        success: function(data){
            if(val==1)
            {
                
                $('#favourites'+project_id).removeClass('added-to-fav');
                $('#favourites'+project_id).data('value',0);
                $('#favourites'+project_id).attr('title', 'Add to favourites');
            }
            else
            {
                 
                $('#favourites'+project_id).addClass('added-to-fav');
                $('#favourites'+project_id).data('value',1);
                $('#favourites'+project_id).attr('title', 'Remove from favourites');
            }
        }
    });
});
 /*$(document).on('focus','#startdate',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
            
        });
 });
 $(document).on('focus','#enddate',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
            
        });
 });*/

$(document).on('change','.editactivityenddate',function(){
    
    var startDate = $('#startdate').val();
    var endDate = $('#enddate').val();
    //alert(endDate)
    if(endDate != '' && startDate != '')
    {
        var startDate1 = Date.parse(startDate);
        var endDate1 = Date.parse(endDate);
        var timeDiff = endDate1 - startDate1;
        daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        console.log(daysDiff);
        $('#duration').val(daysDiff+1);
    }
    });

$(document).on('change','.editactivitystartdate',function(){
       var startDate = new Date($('#startdate').val());
       var duration = $('#duration').val();
       if(duration != '' && startDate != '')
        {
            var newdate = new Date(startDate).setDate(startDate.getDate() + (+duration) - 1);
            var endDate1 = new Date(newdate);
            var tempoMonth = (endDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (endDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var endDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
            
            $('#enddate').val(endDate);
        }
    });
$(document).on('change','.editenggactivityduration',function(){
       var startDate = new Date($('#startdate').val());
       var duration = $('#duration').val();
       if(duration != '' && startDate != '')
        {
            var newdate = new Date(startDate).setDate(startDate.getDate() + (+duration) - 1);
            var endDate1 = new Date(newdate);
            var tempoMonth = (endDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (endDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var endDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
            
            $('#enddate').val(endDate);
        }
    });
