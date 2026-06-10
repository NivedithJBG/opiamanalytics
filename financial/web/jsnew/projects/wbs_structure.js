$(document).on( "click", ".viewwbsname", function(){
    //$('.acc_container').slideUp();
    $('#project').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#wbsstructered').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#selectedProjectId').val(id);
    $('#listwbsname').trigger('click');
});


$(document).on('click','.editstructurebutton',function(){
    var idval=$(this).val();
    $('#wbsstructurename'+idval).hide();
    $('#editstructurebutton'+idval).hide();
    $('#editwbsstructurename'+idval).show();
    $('#savestructurebutton'+idval).show();
});

$(document).on('click','.savestructurebutton',function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editwbsstructurename'+idval).val()=='')
    {
        $('#editwbsstructurename'+idval).next("span").html('Enter Wbs Structure Name').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../projects1/updatewbsstructurename',
            beforeSend : function(){
                $('#savestructurebutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {structureid:idval,Structurename:$('#editwbsstructurename'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#wbsstructurename'+data.Id).show();
                    $('#editstructurebutton'+data.Id).show();
                    $('#editwbsstructurename'+data.Id).hide();
                    $('#savestructurebutton'+data.Id).hide();
                    $('#editwbsstructurename'+data.Id).text(data.StructureName);
                    $('#listwbsname').trigger('click');

                }
                else
                {
                    //alert(data.errortext);
                }

                $('#savewbsscheduleitembutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.deletestructurebutton',function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Structure Name?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects1/deletestructurename',
            beforeSend : function(){
                $('#deletestructurebutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {structureid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workgrouprow'+data.Id).remove();
                }
                else
                {
                   // alert(data.errortext);
                }

                $('#deletestructurebutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

$(function(){
     $('#listwbsname').click(function(){
        $('#wbslistsection').slideDown('slow');// slide down the project listing div
        $('#listwbsname').removeClass('btn-danger').addClass('btn-success');
        $.ajax({
            type: 'POST',
            url: '../projects1/wbsstructurelist',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#wbsitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
	
});