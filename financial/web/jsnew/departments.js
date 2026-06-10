/**
 * Created by SolmindsDelli5 on 12/26/2016.
 */
$(document).on('click','#departments',function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listdepartments').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});
$(function(){
    $('#listdepartments').click(function () {
        $('#departmentadd').slideUp('slow');// slide down the project listing div
        $('#departmentlistsection').slideDown('slow');// slide down the project listing div
        $('#listdepartments').removeClass('btn-danger').addClass('btn-success');
        $('#adddepartment').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../doccumentManager/ListDepartment',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {name:$('#searchdepartment').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#departmentitems').html(data.result);
                    $('#departmenttable').show();
                }

                $('.preloader').hide();
            }
        });

    });
    $('#departmentsearch').click(function(){
        $('#listdepartments').trigger('click')
    });
    $('#adddepartment').click(function(){
        $('#departmentlistsection').slideUp('slow');// slide down the project listing div
        $('#departmentadd').slideDown('slow');// slide down the project listing div
        $('#adddepartment').removeClass('btn-danger').addClass('btn-success');
        $('#listdepartments').removeClass('btn-success').addClass('btn-danger');
    });
    $('#savedepartment').click(function(){
        var error=0;
        $('.error').hide();
        if($('#departmentname').val()=='')
        {
            $("#departmentname").next("span").html('Enter Function Name').show('slow');
            error=1;
        }
        var name=$('#departmentname').val();

        if(error==0){
            $.ajax({
                type:'POST',
                url:'../doccumentManager/AddDepartment',
                beforeSend:function(){
                    $('#savedepartment').attr("disabled", true);
                },
                dataType:'json',
                data: {name:name},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#departmentform')[0].reset();
                        $('#listdepartments').trigger('click');
                        $('#savedepartment').attr("disabled", false);
                    }
                }
            });
        }
    });
});
$(document).on( "click", ".editdepartmentbutton", function(){
    var idval=$(this).val();
    $('#editdepartmentname'+idval).show();
    $('#savedepartmentbutton'+idval).show();
    $('#nametext'+idval).hide();
    $('#editdepartmentbutton'+idval).hide();
} );
$(document).on( "click", ".savedepartmentbutton", function(){
    var idval=$(this).val();
    var name=$('#editdepartmentname'+idval).val();
    var error=0;
    $('.error').hide();
    if($('#editdepartmentname'+idval).val()=='')
    {
        $('#editdepartmentname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../doccumentManager/Departmentupdate',
            beforeSend : function(){
                $('#savedepartmentbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {departmentid:idval,name:name},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editdepartmentname'+idval).hide();
                    $('#savedepartmentbutton'+idval).hide();
                    $('#nametext'+idval).text($('#editdepartmentname'+idval).val()).show();
                    $('#editdepartmentbutton'+idval).show();
                }
                $('#savedepartmentbutton'+idval).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deletefunction", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this function ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../doccumentManager/Deletedept',
            beforeSend : function(){
                $('#deletefunction'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {departmentid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#departmentrow'+data.Id).remove();
                    $('#listdepartments').trigger('click');
                }
                $('#deletefunction'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});

$(document).on('click','.childfolders',function(){

    var functionid=$(this).val();

    $('#folderslist').val(functionid);

    $('#folders').trigger('click');

});