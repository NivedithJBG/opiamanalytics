/**
 * Created by SolmindsDelli5 on 04-01-2018.
 */
$(document).on('click','#enggproject',function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listenggproject').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});

$(function(){
    $('#listenggproject').click(function(){
        $('#enggprojectaddsection').slideUp('slow');// slide down the project listing div
        $('#enggprojectlistsection').slideDown('slow');// slide down the project listing div
        $('#listenggproject').removeClass('btn-danger').addClass('btn-success');
        $('#enggaddproject').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../engineering/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectname:''},
            success: function(data){
                if(data.error=='No')
                {
                    $('#enggprojectitems').html(data.result);
                    $('#enggprojecttable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });
    $('#enggaddproject').click(function(){
        $('#enggprojectlistsection').slideUp('slow');// slide down the project listing div
        $('#enggprojectaddsection').slideDown('slow');// slide down the project listing div
        $('#enggaddproject').removeClass('btn-danger').addClass('btn-success');
        $('#listenggproject').removeClass('btn-success').addClass('btn-danger');
    });
    $('#enggsaveproject').click(function(){
        var error=0;
        $('.error').hide();
        if($('#enggprojectname').val()=='')
        {
            $("#enggprojectname").next("span").html('Enter Project Name').show('slow');
            error=1;
        }
        if($('#enggcashaccount').val()=='0')
        {
            $("#enggcashaccount").next("span").html('Select Cash Account').show('slow');
            error=1;
        }
        if($('#enggbankaccount').val()=='0')
        {
            $("#enggbankaccount").next("span").html('Select Bank Account').show('slow');
            error=1;
        }

        if(ProjectNameExists($('#enggprojectname').val())=='Yes')
        {
            $("#enggprojectname").next("span").html('Project Name Exists').show('slow');
            error=1;
        }

        if(error==0){
            $.ajax({
                type:'POST',
                url:'../engineering/create',
                beforeSend:function(){
                    $('#enggsaveproject').attr("disabled", true);
                },
                dataType:'json',
                data: {projectname:$('#enggprojectname').val(),cashaccount:$('#enggcashaccount').val(),bankaccount:$('#enggbankaccount').val()},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#enggaddprojectform')[0].reset();
                        $('#listenggproject').trigger('click');
                        $('#enggsaveproject').attr("disabled", false);
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#enggsaveproject').attr("disabled", false);
                }
            });
        }



    });
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