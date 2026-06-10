/**
 * Created by SolmindsDelli5 on 12/14/2016.
 */
$(function(){
    $('#listgcc').click(function(){
        $.ajax({
            type: 'POST',
            url: '../ProjectPricing/Gccsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#gccitems').html(data.result);
                    $('#gcctable').show();
                }
                $('.preloader').hide();
            }
        });
    });
});
$(document).on('click','#addgcc',function(){
    var error=0;
    $('.error').hide();
    if($('#tittle').val()=='')
    {
        $('#tittle').next("span").html('Enter Tittle').show('slow');
        error=1;
    }

    if(error==0){
        return true;
    }
    else{
        //alert("You have to enter all values for reporting");
        return  false;
    }
});

$(document).on( "click", ".deletegcc", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this GCC ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../ProjectPricing/Deletegcc',
            beforeSend : function(){
                $('#deletegcc'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {gccid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#gccrow'+idval).remove();
                    $('#listgcc').trigger('click');
                }

                $('#deletegcc'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
function getProjectname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/Getname',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}