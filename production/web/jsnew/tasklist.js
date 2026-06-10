
$(function(){


    //    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
    //         $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //         //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    //     }
    //     if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
    //         $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //         $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    //     }
    //     $('#listrestype').trigger('click'); 
    // CONSTRUCTION section function
    // list Tasks in Construction
   
   
    $('#listtask').click(function(){
        $('#taskaddsection').slideUp('slow');// slide down the project listing div
        $('#tasklistsection').slideDown('slow');// slide down the project listing div
        $('#listtask').removeClass('btn-danger').addClass('btn-success');
        $('#addtask').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Construction/listtask',
            beforeSend : function(){
                $('#projectsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {id:$('#Construction_Id').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#taskitems').html(data.result);
                    $('#tasktable').show();
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
     $('#listtask').trigger('click');
    // list project click  \
    // add project click
    $('#addtask').click(function(){
        $('#tasklistsection').slideUp('slow');// slide down the project listing div
        $('#taskaddsection').slideDown('slow');// slide down the project listing div
        $('#addtask').removeClass('btn-danger').addClass('btn-success');
        $('#listtask').removeClass('btn-success').addClass('btn-danger');

    });
    // add project click
    // save project click
    $('#savetask').click(function(){

        var error=0;
        $('.error').hide();
        if($('#taskname').val()=='')
        {
            $("#taskname").next("span").html('Enter Task Name').show('slow');
            error=1;
        }
       
        if(error==0){

            $.ajax({
                type:'POST',
                url:'../construction/taskcreate',
                beforeSend:function(){
                    $('#savetask').attr("disabled", true);
                },
                dataType:'json',
                data: {taskname:$('#taskname').val(),activityid:$('#Construction_Id').val()},
                success:function(data){
                  
                        $('#addtaskform')[0].reset();
                        $('#listtask').trigger('click');
                        $('#savetask').attr("disabled", false);
                        
                    // savetask').attr("disabled", false);
                    
                }
            
                       

          });
        }

    // save project click
    //project function ends here


});

///////////////////////////////////////////////////////////////////////////////
// Project Setup section
    $('#listtask_prosetup').click(function(){
        $('#taskaddsection').slideUp('slow');// slide down the project listing div
        $('#tasklistsection').slideDown('slow');// slide down the project listing div
        $('#listtask_prosetup').removeClass('btn-danger').addClass('btn-success');
        $('#addtask_prosetup').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../ProjectSetup/listtask',
            beforeSend : function(){
                $('#projectsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {id:$('#PS_Id').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#taskitems').html(data.result);
                    $('#tasktable').show();
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
    $('#listtask_prosetup').trigger('click');
    // list project click  \
    // add project click
    $('#addtask_prosetup').click(function(){
        $('#tasklistsection').slideUp('slow');// slide down the project listing div
        $('#taskaddsection').slideDown('slow');// slide down the project listing div
        $('#addtask_prosetup').removeClass('btn-danger').addClass('btn-success');
        $('#listtask_prosetup').removeClass('btn-success').addClass('btn-danger');

    });
    // add project click
    // save project click
    $('#savetask_prosetup').click(function(){

        var error=0;
        $('.error').hide();
        if($('#taskname').val()=='')
        {
            $("#taskname").next("span").html('Enter Task Name').show('slow');
            error=1;
        }
       
        if(error==0){

            $.ajax({
                type:'POST',
                url:'../ProjectSetup/taskcreate',
                beforeSend:function(){
                    $('#savetask_prosetup').attr("disabled", true);
                },
                dataType:'json',
                data: {taskname:$('#taskname').val(),activityid:$('#PS_Id').val()},
                success:function(data){
                  
                        $('#addtaskform')[0].reset();
                        $('#listtask_prosetup').trigger('click');
                        $('#savetask_prosetup').attr("disabled", false);
                        
                    // savetask').attr("disabled", false);
                    
                }
            
                       

          });
        }

    // save project click
    //project function ends here


});





// $(document).on('click','#editproject',function(){
//     var error=0;
//     $('.error').hide();
//     if($('#tasktname').val()=='')
//     {
//         $('#taskname').next("span").html('Enter Project Name').show('slow');
//         error=1;
//     }

//     if(error==1)
//     {
//         return false;
//     }
//     else
//     {
//         return true;
//     }
// });
// $(document).on( "change",".cashaccountname", function(){
//     var cash=$('#cash').val();
//     var project=$('#projectid').val();
//     $.ajax({
//         type: 'POST',
//         url: '../../projects/checkaccounts',
//         dataType: "json",
//         data: {accountid:cash,projectid:project},
//         success: function(data){
//             if(data.error=='Yes')
//             {
//                 $('#cash').next("span").html('Account already linked').show('slow');
//             }
//             else
//             {
//                 $('#cash').next("span").hide();
//             }
//         }
//     });

// });
// $(document).on( "change",".bankaccountname", function(){
//     var bank=$('#bank').val();
//     var project=$('#projectid').val();
//     $.ajax({
//         type: 'POST',
//         url: '../../projects/checkaccounts',
//         dataType: "json",
//         data: {accountid:bank,projectid:project},
//         success: function(data){
//             if(data.error=='Yes')
//             {
//                 $('#bank').next("span").html('Account already linked').show('slow');
//             }
//             else
//             {
//                 $('#bank').next("span").hide();
//             }
//         }
//     });
// });
// $(document).on( "change","#cashaccount", function(){
//     var cash=$('#cashaccount').val();
//     $.ajax({
//         type: 'POST',
//         url: '../projects/checkaccounts',
//         dataType: "json",
//         data: {accountid:cash},
//         success: function(data){
//             if(data.error=='Yes')
//             {
//                 $('#cashaccount').next("span").html('Account already linked').show('slow');
//             }
//             else
//             {
//                 $('#cashaccount').next("span").hide();
//             }
//         }
//     });
// });
// $(document).on( "change",".bankaccount", function(){
//     var bank=$('#bankaccount').val();
//     $.ajax({
//         type: 'POST',
//         url: '../projects/checkaccounts',
//         dataType: "json",
//         data: {accountid:bank},
//         success: function(data){
//             if(data.error=='Yes')
//             {
//                 $('#bankaccount').next("span").html('Account already linked').show('slow');
//             }
//             else
//             {
//                 $('#bankaccount').next("span").hide();
//             }
//         }
//     });
// });
// $(document).on( "click", ".remove_account", function(){
//     var idval=$(this).val();
//     var r = confirm("Are you sure you want to delete this Account?");
//     if (r == true) {

//         $.ajax({
//             type: 'POST',
//             url: '../../projects/deleteaccount',
//             beforeSend : function(){
//                 $('#remove_account'+idval).attr("disabled", true);
//             },
//             dataType: "json",
//             data: {accountid:idval},
//             success: function(data){
//                 if(data.error=='No')
//                 {
//                     $('#bankaccount'+data.Id).remove();
//                 }
//                 else
//                 {
//                     alert(data.errortext);
//                 }

//                 $('#remove_account'+data.Id).attr("disabled", false);
//             }
//         });

//     } else {
//         return false;
//     }

// });



$(document).on( "click", ".deletetask_prosetup", function(){
    var idval=$(this).val();
    // console.log(idval);
    var r = confirm("Are you sure you want to delete this task?");
    if (r == true) {


                    $.ajax({
                        type: 'POST',
                        url: '../ProjectSetup/deletetask',
                        beforeSend : function(){
                            $('#deletetask_prosetup'+idval).attr("disabled", true);
                        },
                        dataType: "json",
                        data: {taskid:idval},
                        success: function(data){
                            if(data.error=='No')
                            {
                                // console.log("success");
                                $('#taskrow'+data.Id).remove();
                            }
                            else
                            {
                                alert(data.errortext);
                            }

                            $('#deletetask_prosetup'+data.Id).attr("disabled", false);
                        }
                    });
              

    } else {
        return false;
    }

});

$(document).on( "click", ".edittask_button", function(){
    var idval=$(this).val()
    $('#edittask'+idval).show();
    $('#savetaskbutton'+idval).show();
    $('#tasktext'+idval).hide();
    $('#edittask_button'+idval).hide(); 

} );
// save edited task from projectsetup function
$(document).on( "click", ".savetaskbutton", function(){
    var idval=$(this).val();
    // console.log($('#edittask'+idval).val());
    var error=0;
    $('.error').hide();
    if($('#edittask'+idval).val()=='')
    {
        $('#edittask'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
        
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../ProjectSetup/updatetask',
            beforeSend : function(){
                $('#savetaskbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {taskid:idval,name:$('#edittask'+idval).val()},
            success: function(data){
                if(data.error='No')
                {
                    // console.log(data);

                    $('#edittask'+data.Id).hide();
                    $('#savetaskbutton'+data.Id).hide();
                    $('#tasktext'+data.Id).text($('#edittask'+data.Id).val()).show();
                    $('#edittask_button'+data.Id).show();
                   
                }
                else
                {
                    alert(data.errortext);
                }

                $('#savetaskbutton'+data.Id).attr("disabled", false);
            }
        });
    }

} );

});