/**
 * Created by SolmindsDelli5 on 12/12/2016.
 */
$(function(){
    $('#listpq').click(function(){
        $.ajax({
            type: 'POST',
            url: '../ProjectPricing/PQsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#pqitems').html(data.result);
                    $('#pqtable').show();
                }
                $('.preloader').hide();
            }
        });
    });
});

$(document).on('click','#addpq',function(){
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

$(document).on( "click", ".deletepq", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this PQ ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../ProjectPricing/DeletePQ',
            beforeSend : function(){
                $('#deletepq'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {pqid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#pqrow'+idval).remove();
                    $('#listpq').trigger('click');
                }

                $('#deletepq'+idval).attr("disabled", false);
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
