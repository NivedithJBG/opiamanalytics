$(document).on('click','#project',function(){
        if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
            //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
        }
        if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
            $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
        }
        $('#listproject').trigger('click')
        return false; //Prevent the browser jump to the link anchor
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
        if($('#projectname').val()=='')
        {
            $("#projectname").next("span").html('Enter Project Name').show('slow');
            error=1;
        }
        if($('#cashaccount').val()=='0')
        {
            $("#cashaccount").next("span").html('Select Cash Account').show('slow');
            error=1;
        }
        if($('#bankaccount').val()=='0')
        {
            $("#bankaccount").next("span").html('Select Bank Account').show('slow');
            error=1;
        }
        /*if (!$('input[name=account]:checked').val() ) {
            $(".account").next("span").html('Select Cash or bank').show('slow');
            error=1;
        }*/
        if(ProjectNameExists($('#projectname').val())=='Yes')
        {
            $("#projectname").next("span").html('Project Name Exists').show('slow');
            error=1;
        }
        /*if(AccountName($('#cashaccountname').val())=='Yes')
        {
            $('#cashaccountname').next("span").html('Account Name Exists').show('slow');
            error=1;
        }
        if(AccountName($('#bankaccount').val())=='Yes')
        {
            $('#bankaccount').next("span").html('Account Already Linked').show('slow');
            error=1;
        }*/

        if(error==0){
            $.ajax({
                type:'POST',
                url:'../projects/create',
                beforeSend:function(){
                    $('#saveproject').attr("disabled", true);
                },
                dataType:'json',
                data: {projectname:$('#projectname').val(),cashaccount:$('#cashaccount').val(),bankaccount:$('#bankaccount').val()},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#addprojectform')[0].reset();
                        $('#listproject').trigger('click');
                        $('#saveproject').attr("disabled", false);
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

    // save project click
    //project function ends here

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
$(document).on( "change",".cashaccountname", function(){
    var cash=$('#cash').val();
    var project=$('#projectid').val();
    $.ajax({
        type: 'POST',
        url: '../../projects/checkaccounts',
        dataType: "json",
        data: {accountid:cash,projectid:project},
        success: function(data){
            if(data.error=='Yes')
            {
                $('#cash').next("span").html('Account already linked').show('slow');
            }
            else
            {
                $('#cash').next("span").hide();
            }
        }
    });

});
$(document).on( "change",".bankaccountname", function(){
    var bank=$('#bank').val();
    var project=$('#projectid').val();
    $.ajax({
        type: 'POST',
        url: '../../projects/checkaccounts',
        dataType: "json",
        data: {accountid:bank,projectid:project},
        success: function(data){
            if(data.error=='Yes')
            {
                $('#bank').next("span").html('Account already linked').show('slow');
            }
            else
            {
                $('#bank').next("span").hide();
            }
        }
    });
});
$(document).on( "change","#cashaccount", function(){
    var cash=$('#cashaccount').val();
    $.ajax({
        type: 'POST',
        url: '../projects/checkaccounts',
        dataType: "json",
        data: {accountid:cash},
        success: function(data){
            if(data.error=='Yes')
            {
                $('#cashaccount').next("span").html('Account already linked').show('slow');
            }
            else
            {
                $('#cashaccount').next("span").hide();
            }
        }
    });
});
$(document).on( "change",".bankaccount", function(){
    var bank=$('#bankaccount').val();
    $.ajax({
        type: 'POST',
        url: '../projects/checkaccounts',
        dataType: "json",
        data: {accountid:bank},
        success: function(data){
            if(data.error=='Yes')
            {
                $('#bankaccount').next("span").html('Account already linked').show('slow');
            }
            else
            {
                $('#bankaccount').next("span").hide();
            }
        }
    });
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
                    alert('Cannot delete this project. Cash or bank account is linked with this project');
                }

            }
        });

    } else {
        return false;
    }

});

/*$(document).on( "click", ".viewDocuments", function(){

    var projectid=$(this).val();

    $('#selectedProjectId').val(projectid);

    var url='../doccumentManager/index?project='+projectid+'#projdocuments';

    window.location.href = url;

});*/
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