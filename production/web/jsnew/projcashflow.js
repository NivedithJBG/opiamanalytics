/**
 * Created by SolmindsDelli5 on 12-04-2017.
 */
$(document).on( "click", "#procashflow", function(){
    //$('.acc_container').slideUp();
    $('#financereports').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#projcashflow').addClass('active').next('.acc_container').slideDown();
    $('#projcashflow').show();
    //$('#projectexp').hide();
});
$(function(){
    $('#projectcashflowstmt').click(function(){
        var error=0;
        $('.error').hide();
        if($('#projectliststmt').val()=='none')
        {
            $("#projectliststmt").next("span").html('Select Project').show('slow');
            error=1;
        }
        if (error==0)
        {
            $('#projcashflowliststmt').slideDown('slow');
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/ProjectCashflow',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {projectid:$('#projectliststmt').val(),quarter:$('#projectquarter').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#projectinfo').html(data.project + ' Cash Flow');
                        $('#projcashflowstmtitems').html(data.result);
                        $('#projcashflowtable').show();
                    }
                    $('.preloader').hide();
                }
            });
        }

    });
});
