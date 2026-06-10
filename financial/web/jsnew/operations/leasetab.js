$(document).on( "click", "#viewleaseorders", function(){
    //$('#leaseorderbillsearch').trigger('click') ;
});

$(function() {

    $('#leaseorderbillsearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../projects/leaseorders',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#projectname-ILE').html(data.projectName);
                    $('#leaseorderitems').html(data.result);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#leaseorderhistory').click(function () {
        $.ajax({
            type: 'POST',
            url: '../projects/leaseorderhistory',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#leaseorderhistoryitems').html(data.result);
                }
                $('.preloader').hide();
            }
        });
    });
    $(document).on('click','.leaseordersAdd',function(){
        var leaseid=$(this).attr("data-v");
            $.ajax({
                type: 'POST',
                url: '../projects/receiveorder',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {orderid:leaseid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#leaseorderadd').html(data.result);
                    }
                    $('.preloader').hide();
                }
            });
    });

    $(document).on('click','.tab-wrapper .viewForm-lc',function(){
        $(this).parents('.tab').addClass('edit-form-active');
        $(this).parents('.tab').removeClass('add-form-active');
        var viewid=$(this).attr('data-v');
        $.ajax({
            type: 'POST',
            url: '../projects/viewworkorder/',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {orderid: viewid},
            success: function(data){
                if(data.error=='No')
                {
                    $('.preloader').hide();
                    $('#viewleaseorder').html(data.result);
                }
                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();
            }
        });
    });
    $(document).on('click','.deleteleasebill',function(){
        var billid=$(this).attr('data-v');
        var r = confirm("Are you sure you want to delete this Bill ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../projects/deleteworkorderbill/',
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

})