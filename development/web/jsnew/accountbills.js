/**
 * Created by SolmindsDelli5 on 28-03-2017.
 */
$(document).on( "click", ".viewaccountbills", function(){
    $('#projects').removeClass('active').next().slideUp();
    $('#accountbills').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#selectedProjectId').val(id);
    $('#billgenprojectname').html(getProjectname(id));
    $('#listaccountbills').trigger('click');
});

$(function(){
    $('#listaccountbills').click(function(){
        $.ajax({
            type: 'POST',
            url: '../ProjectPricing/Accountbills',
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#accntbillitems').html(data.result);
                }
            }
        });
    });
});