/**
 * Created by SolmindsDelli5 on 13-12-2017.
 */
$(document).on( "click", ".viewleaseorders", function(){
    $('#rproject').removeClass('active').next().slideUp();
    $('#leaseorders').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#selectedProjectId').val(id);
    $('#leaseorderprojname').html(getProjectname(id));
    $('#leaseordersearch').trigger('click') ;
});

$(function() {
    $('#leaseordersearch').click(function () {
        $('#leaseorderslist').slideDown('slow');
        $('#leaseorderhistorysection').slideUp('slow');
        $('#leaseordersearch').removeClass('btn-danger').addClass('btn-success');
        $('#leaseorderhistory').removeClass('btn-danger').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../projects/LeaseOrders',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#leaseorderitems').html(data.result);
                    $('#leaseorderstable').show();
                    $(".history-bill-list data-content-list").css("display", "none");
                }
                $('.preloader').hide();
            }
        });
    });
    $('#leaseorderhistory').click(function () {
        $('#leaseorderslist').slideUp('slow');
        $('#leaseorderhistorysection').slideDown('slow');
        $('#leaseordersearch').removeClass('btn-danger').addClass('btn-danger');
        $('#leaseorderhistory').removeClass('btn-danger').addClass('btn-success');
        $.ajax({
            type: 'POST',
            url: '../projects/Leaseorderhistory',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#leaseorderhistoryitems').html(data.result);
                    $('#leaseorderhistorytable').show();
                }
                $('.preloader').hide();
            }
        });
    });
});
$(document).on('click','.deleteleasebill',function(){
    var billid=$(this).val();
    var r = confirm("Are you sure you want to delete this Bill ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects/Deleteworkorderbill/',
            beforeSend : function(){
                $('#deleteleasebill'+billid).attr("disabled", true);
            },
            dataType: "json",
            data: {billid:billid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#leaseorderbillrow'+billid).remove();
                    //$('#listappjobcard').trigger('click');
                }
                $('#deleteleasebill'+billid).attr("disabled", false);
            }
        });
    }
    else {
        return false;
    }
});