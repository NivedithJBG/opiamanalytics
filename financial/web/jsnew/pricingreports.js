$(document).on( "click", ".viewReports", function(){
    $('#projects').removeClass('active').next().slideUp();
    $('#pricingreports').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#selectedProjectId').val(id);

});
$(function(){
    $('#qoute').click(function(){
        var projectid=$('#selectedProjectId').val();
        var url='../ProjectPricing/QuoteAnalysis/'+projectid;
        window.location.href = url;
    });
});
