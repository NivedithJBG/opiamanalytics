/**
 * Created by SolmindsDelli5 on 24-05-2016.
 */
$(document).on( "click", "#receipt", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listreceipt').trigger('click');
});
$(function(){
    //test();
    $('#listreceipt').click(function(){
        //$('#productaddsection').slideUp('slow');// slide down the project listing div
        $('#receiptlistsection').slideDown('slow');// slide down the project listing div
        $('#listreceipt').removeClass('btn-danger').addClass('btn-success');
        $('#addreceipt').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/purchasesearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {name:$('#searchreceipt').val(),project:$('#searchreceipt').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#receiptitems').html(data.result);
                    $('#receipttable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });


    $('#receiptsearch').click(function(){
        $('#listreceipt').trigger('click');
    });

    $('.savepurchase').click(function(){
        var error=0;
        $('.error').hide();
        if($('#Amount').val()=='')
        {
            $("#Amount").next("span").html('Enter Amount').show('slow');
            error=1;
        }
        if(NameExists($('#PO_No').val())=='Yes')
        {
            $("#PO_No").next("span").html('Resource Exists').show('slow');
            error=1;
        }
        if($('#Address').val()=='')
        {
            $("#Address").next("span").html('Enter Address').show('slow');
            error=1;
        }
        if($('#Purpose0').val()=='')
        {
            $("#Purpose0").next("span").html('Enter Tax').show('slow');
            error=1;
        }
        if($('#contact').val()=='')
        {
            $("#contact").next("span").html('Enter Contact Details').show('slow');
            error=1;
        }
        if($('#delivery').val()=='')
        {
            $("#delivery").next("span").html('Enter Delivery Details').show('slow');
            error=1;
        }
        if($('#leadtime').val()=='')
        {
            $("#leadtime").next("span").html('Enter Lead Time Details').show('slow');
            error=1;
        }
        if($('#Payment').val()=='')
        {
            $("#Payment").next("span").html('Enter Payment Details').show('slow');
            error=1;
        }
        if($('#LetterBody').val()=='')
        {
            $("#LetterBody").next("span").html('Enter Letter Body ').show('slow');
            error=1;
        }
        if($('#Terms').val()=='')
        {
            $("#Terms").next("span").html('Enter Terms and Conditions ').show('slow');
            error=1;
        }
        if($('#project').val()=='0')
        {
            $("#project").next("span").html('Select Project').show('slow');
            error=1;
        }
        if($('#place').val()=='0')
        {
            $("#place").next("span").html('Select Place').show('slow');
            error=1;
        }
        $('.details').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#Details"+id).next("span").html('Enter Details').show('slow');
                error=1;
            }
        });

        if(error==0)
        {
            return true;
        }
        else
        {
            return false;
        }

    });


    $('#receiptapprove').click(function(){
        var error=0;
        if($('#receiptstatus').val()=='0')
        {
            $("#receiptstatus").next("span").html('Select Status').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/Receiptapprove',
                dataType: "json"
            });
        }
        else
        {
            return false;
        }

    });
    $(document).on('click','.deletefundrecptbutton',function(){
        var recptid=$(this).val();
        var r = confirm("Are you sure you want to delete this Receipt ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../DoccumentManager/DeletePurchase/',
                beforeSend : function(){
                    $('#deletefundrecptbutton'+recptid).attr("disabled", true);
                },
                dataType: "json",
                data: {recptid:recptid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#receiptrow'+data.Id).remove();
                        $('#listreceipt').trigger('click');
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deletefundrecptbutton'+data.Id).attr("disabled", false);
                }
            });
        }
        else {
            return false;
        }
    });
});
$(document).on("change", ".resourcetype", function () {
    //var itemid = $(this).attr('data-id');
    var resourcetype = $(this).val();
    var requestid=$(this).attr('data-id');
    $.ajax({
        type:'POST',
        url:'../Resources/getresources',
        dataType:"json",
        data:{id:resourcetype},
        success:function (data) {
            if (data.error == 'No') {
                var option = data.result;
                $("#iowid"+requestid).empty().append(option);

            }
            else {
                alert(data.errortext);
            }

        }
    });
});
$(document).on("blur", ".billqty", function () {
    var itemid = $(this).attr('data-id') * 1;
    var rate = $('#Rate'+itemid).val() * 1;
    var tax = $('#Tax'+itemid).val() * 1;
    var unit = $(this).val() * 1;
    total=unit*(rate+(rate*tax/100));
    $('#totalrate'+itemid).text(total.toFixed(2));
    $("#totalamount"+itemid).val(total);

    var totalhere = 0;
    $('.itemtotal').each(function () {
        totalhere = (totalhere)*1 + ($(this).val()* 1)

    });

    $('#totalamt').text(totalhere.toFixed(2));
    $("#totalitem").val(totalhere);
});
$(document).on("blur", ".purchasetax", function () {

    var itemid = $(this).attr('data-id') * 1;
    var rate = $('#Rate'+itemid).val() * 1;
    var unit = $('#Quantity'+itemid).val() * 1;
    var tax = $(this).val() * 1;
    total=unit*(rate+(rate*tax/100));
    $('#totalrate'+itemid).text(total.toFixed(2));
    $("#totalamount"+itemid).val(total);

    var totalhere = 0;
    $('.itemtotal').each(function () {
        totalhere = (totalhere)*1 + ($(this).val()* 1)

    });

    $('#totalamt').text(totalhere.toFixed(2));
    $("#totalitem").val(totalhere);
});
$(document).on("blur", ".billrate", function () {
    var itemid = $(this).attr('data-id') * 1;
    var unit = $('#Quantity'+itemid).val() * 1;
    var rate = $(this).val() * 1;
    var tax = $('#Tax'+itemid).val() * 1;
    total=unit*(rate+(rate*tax/100));

    $('#totalrate'+itemid).text(total.toFixed(2));
    $("#totalamount"+itemid).val(total);
    var totalhere = 0;
    $('.itemtotal').each(function () {
        totalhere = (totalhere)*1 +($(this).val()* 1)


    });

    $('#totalamt').text(totalhere.toFixed(2));
    $("#totalitem").val(totalhere);

});
$(document).on('click','#printvoucher',function(){
    $('#cancelrequest').trigger('click');
});
$(document).on("change", ".groupid", function () {
    //var itemid = $(this).attr('data-id');
    var resourcegroup = $(this).val();
    var projectid = $('#project').val();
    var requestid=$(this).attr('data-id');
    $.ajax({
        type:'POST',
        url:'../../DoccumentManager/GetItems',
        dataType:"json",
        data:{resourcegroupid:resourcegroup,projectid:projectid},
        success:function (data) {
            if (data.error == 'No') {
                var option = data.result;
                $("#itemid"+requestid).empty().append(option);

            }
            else {
                alert(data.errortext);
            }

        }
    });
});
$(document).on("change", ".itemid", function () {
    //var itemid = $(this).attr('data-id');
    var itemid = $(this).val();
    var projectid = $('#project').val();
    var requestid=$(this).attr('data-id');
    $.ajax({
        type:'POST',
        url:'../../DoccumentManager/GetResourcesname',
        dataType:"json",
        data:{itemid:itemid},
        success:function (data) {
            if (data.error == 'No') {
                var option = data.result;
                $("#iowid"+requestid).empty().append(option);

            }
            else {
                alert(data.errortext);
            }

        }
    });
});
$(document).on("change", ".Resource", function () {
    //var itemid = $(this).attr('data-id');
    var itemid = $(this).val();
    var projectid = $('#project').val();
    var requestid=$(this).attr('data-id');
    $.ajax({
        type:'POST',
        url:'../../DoccumentManager/GetResourcesdetails',
        dataType:"json",
        data:{itemid:itemid},
        success:function (data) {
            if (data.error == 'No') {
                var Unit = data.Unit;
                $('#Unit'+requestid).val(data.Unit);

                var Price = data.Price;
                $('#Rate'+requestid).val(data.Price);
                $('.billrate').trigger("blur");

                /*var total = data.total;
                 $('#totalrate'+requestid).html(data.total);*/
                var total = data.Quantity;
                $('#Quantity'+requestid).val(data.Quantity);
                $('.billqty').trigger("blur");


            }
            else {
                alert(data.errortext);
            }

        }
    });
});
$(document).on("change", "#vendor", function () {
    //var itemid = $(this).attr('data-id');
    var vendor = $(this).val();
    var project = $("#project").val();
    var requestid=$(this).attr('data-id');
    $.ajax({
        type:'POST',
        url:'../../DoccumentManager/Vendorserach',
        dataType:"json",
        data:{vendor:vendor},
        success:function (data) {
            if (data.error == 'No') {
                var option = data.result;
                $('#Address').html(data.result);
            }
            else {
                alert(data.errortext);
            }

        }
    });
});
$(document).on("click", "#approverequest", function () {
    var error=0;

    if($('#aprrovedname').val()=='')
    {
        $("#aprrovedname").next("span").html('Enter Name').show('slow');
        error=1;
    }
    if($('#Designation').val()=='')
    {
        $("#Designation").next("span").html('Enter Designation').show('slow');
        error=1;
    }

    if(error==0)
    {
        return true;
    }
    else
    {
        return false;
    }
});
$(document).on("blur", "#tax", function () {

    var totalrate = 0;
    $('.itemtotal').each(function () {
        totalrate = totalrate + ($(this).val() * 1)
    });

    var taxrate = $(this).val() * 1;
    var taxamount=totalrate * taxrate / 100;

    var amountpayable = (totalrate + (totalrate * taxrate / 100))
    $('#totalamt').text(amountpayable.toFixed(2));
    $('#totalitem').val(amountpayable.toFixed(2));
    /* $('#taxamount').text(taxamount.toFixed(2));*/
});

$(document).on('change','#function',function(){
    var department=$('#function').val();
    $.ajax({
        type: 'POST',
        url: '../../doccumentManager/Getfolders',
        dataType: "json",
        data: {department:department},
        success: function(data){
            if(data.error=='No')
            {
                $('#folder').html(data.result);
            }
        }
    });
});
function NameExists(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../../DoccumentManager/checkname',
        async:false,
        data: {name:name,vendor:$('#vendor').val()},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}
