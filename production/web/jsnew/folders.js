/**
 * Created by SolmindsDelli5 on 1/27/2017.
 */
$(document).on('click','#folders',function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listfolders').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});
$(function(){
    $('#listfolders').click(function () {
        $('#folderadd').slideUp('slow');// slide down the project listing div
        $('#folderlistsection').slideDown('slow');// slide down the project listing div
        $('#listfolders').removeClass('btn-danger').addClass('btn-success');
        $('#addfolder').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../doccumentManager/Listfolder',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {depid:$('#folderslist').val(),name:$('#searchfolder').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#folderitems').html(data.result);
                    $('#foldertable').show();
                }

                $('.preloader').hide();
            }
        });

    });
    $('#foldersearch').click(function(){
        $('#listfolders').trigger('click')
    });
    $('#addfolder').click(function(){
        $('#folderlistsection').slideUp('slow');// slide down the project listing div
        $('#folderadd').slideDown('slow');// slide down the project listing div
        $('#addfolder').removeClass('btn-danger').addClass('btn-success');
        $('#listfolders').removeClass('btn-success').addClass('btn-danger');
    });
    $('#savefolder').click(function(){
        var error=0;
        $('.error').hide();
        var patt = /-/;
        var prefix=patt.test($('#prefixname').val());
        if($('#function').val()=='none')
        {
            $("#function").next("span").html('Select Function').show('slow');
            error=1;
        }
        if($('#foldername').val()=='')
        {
            $("#foldername").next("span").html('Enter Folder Name').show('slow');
            error=1;
        }
        if($('#prefixname').val()=='')
        {
            $("#prefixname").next("span").html('Enter Prefix').show('slow');
            error=1;
        }
        if (prefix) {
            $("#prefixname").next("span").html(' hyphen (-) is not allowed.').show('slow');
            error=1;
        }
        var folder=$('#function').val();
        var project=$('#folderproject').val();
        var name=$('#foldername').val();
        var prefixname=$('#prefixname').val();

        if(error==0){
            $.ajax({
                type:'POST',
                url:'../doccumentManager/Addfolder',
                beforeSend:function(){
                    $('#savefolder').attr("disabled", true);
                },
                dataType:'json',
                data: {folder:folder,project:project,name:name,prefixname:prefixname},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#folderform')[0].reset();
                        $('#listfolders').trigger('click');
                        $('#savefolder').attr("disabled", false);
                    }
                }
            });
        }
    });
});
$(document).on( "click", ".editfolderbutton", function(){
    var idval=$(this).val();
    $('#editfoldername'+idval).show();
    $('#editprefixname'+idval).show();
    $('#editdepname'+idval).show();
    $('#editfoldprojname'+idval).show();
    $('#savefolderbutton'+idval).show();
    $('#foldertext'+idval).hide();
    $('#functiontext'+idval).hide();
    $('#foldprojecttext'+idval).hide();
    $('#prefixtext'+idval).hide();
    $('#editfolderbutton'+idval).hide();
} );
$(document).on( "click", ".savefolderbutton", function(){
    var idval=$(this).val();
    var projectid=$('#editfoldprojname'+idval).val();
    var name=$('#editfoldername'+idval).val();
    var prefixname=$('#editprefixname'+idval).val();
    var dept=$('#editdepname'+idval).val();
    var error=0;
    $('.error').hide();
    var patt = /-/;
    var prefix=patt.test(prefixname);
    if($('#editfoldername'+idval).val()=='')
    {
        $('#editfoldername'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if($('#editprefixname'+idval).val()=='')
    {
        $('#editprefixname'+idval).next("span").html('Enter Prefix').show('slow');
        error=1;
    }
    if (prefix){
        $('#editprefixname'+idval).next("span").html(' hyphen (-) is not allowed.').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../doccumentManager/Folderupdate',
            beforeSend : function(){
                $('#savefolderbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {folderid:idval,projectid:projectid,dept:dept,name:name,prefix:prefixname},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editdepname'+idval).hide();
                    $('#editfoldprojname'+idval).hide();
                    $('#editfoldername'+idval).hide();
                    $('#editprefixname'+idval).hide();
                    $('#savefolderbutton'+idval).hide();
                    $('#functiontext'+idval).text(data.result).show();
                    $('#foldprojecttext'+idval).text(data.projectname).show();
                    $('#foldertext'+idval).text($('#editfoldername'+idval).val()).show();
                    $('#prefixtext'+idval).text($('#editprefixname'+idval).val()).show();
                    $('#editfolderbutton'+idval).show();
                }
                $('#savefolderbutton'+idval).attr("disabled", false);
            }
        });
    }
});
$(document).on( "click", ".deletefolder", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this folder ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../doccumentManager/Deletefolder',
            beforeSend : function(){
                $('#deletefolder'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {folderid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#folderrow'+idval).remove();
                    $('#listfolders').trigger('click');
                }
                $('#deletefolder'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
