$('#approvebill').click(function () {
    var error = 0;
    $('.accounthead').each(function () {
        var id = $(this).attr('data-id');
        if ($(this).val() == '') {
            $("#accounthead" + id).next("span").html('Enter Purpose').show('slow');
            error = 1;
        }
    });
    if (error == 1) {
        return false;
    }
    else {
        return true;
    }
});

$(document).on("click", "#bills", function () {
    if (!$(this).next().is(':hidden')) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if ($(this).next().is(':hidden')) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    //$('#listapprovedbills').val('0');
    $('#listnonapprovedbills').trigger('click');
});
$(function () {
    //test();
    $('#listnonapprovedbills').click(function () {
        //$('#productaddsection').slideUp('slow');// slide down the project listing div
        $('#billslistsection').slideDown('slow');// slide down the project listing div
        $('#filters').show();
        $('#listnonapprovedbills').removeClass('btn-danger').addClass('btn-success');
        $('#addbills').removeClass('btn-success').addClass('btn-danger');
        $('#listapprovedbills').removeClass('btn-success').addClass('btn-danger');

        var type = $('#billtypelist').val();
        var approved = 0;

        $.ajax({
            type:'POST',
            url:'../FinanceRequests/billsearch',
            beforeSend:function () {
                $('.preloader').show();
            },
            dataType:"json",
            data: {vendor:$('#vendorlist').val(),place:$('#placebill').val(),type:type,approved:approved},
            success:function (data) {
                if (data.error == 'No') {
                    $('#billitems').html(data.result);
                    $('#billstable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#listapprovedbills').click(function () {
        //$('#productaddsection').slideUp('slow');// slide down the project listing div
        $('#billslistsection').slideDown('slow');// slide down the project listing div
        $('#filters').show();
        $('#listapprovedbills').removeClass('btn-danger').addClass('btn-success');
        $('#addbills').removeClass('btn-success').addClass('btn-danger');
        $('#listnonapprovedbills').removeClass('btn-success').addClass('btn-danger');

        var type = $('#billtypelist').val();
        var approved = 1;

        $.ajax({
            type:'POST',
            url:'../FinanceRequests/billsearch',
            beforeSend:function () {
                $('.preloader').show();
            },
            dataType:"json",
            data: {vendor:$('#vendorlist').val(),place:$('#placebill').val(),type:type,approved:approved},
            success:function (data) {
                if (data.error == 'No') {
                    $('#billitems').html(data.result);
                    $('#billstable').show();
                }
                $('.preloader').hide();
            }
        });
    });

    $('#savebill').click(function () {
        var error = 0;
        $('.error').hide();
        $('.billpurpose').each(function () {
            var id = $(this).attr('data-id');
            if ($(this).val() == '') {
                $("#Purpose" + id).next("span").html('Enter Purpose').show('slow');
                error = 1;
            }
        });
        $('.billrate').each(function () {
            var id = $(this).attr('data-id');
            if ($(this).val() == '') {
                $("#Rate" + id).next("span").html('Enter Rate').show('slow');
                error = 1;
            }
        });
        $('.billqty').each(function () {
            var id = $(this).attr('data-id');
            if ($(this).val() == '') {
                $("#Quantity" + id).next("span").html('Enter Quantity').show('slow');
                error = 1;
            }
        });
        $('.billunit').each(function () {
            var id = $(this).attr('data-id');
            if ($(this).val() == '') {
                $("#Unit" + id).next("span").html('Enter Unit').show('slow');
                error = 1;
            }
        });
        $('.billtax').each(function () {
            var id = $(this).attr('data-id');
            if ($(this).val() == '') {
                $("#Tax_Amount" + id).next("span").html('Enter Tax Amount').show('slow');
                error = 1;
            }
        });

        /*if( $('#tds').length )         // use this if you are using id to check
        {
            if ($('#tds').val() == '') {
                $("#tds").next("span").html('Enter TDS').show('slow');
                error = 1;
            }
        }*/

        /*if( $('#retention').length )         // use this if you are using id to check
        {
            if ($('#retention').val() == '') {
                $("#retention").next("span").html('Enter Retention').show('slow');
                error = 1;
            }
        }*/

        if( $('#otherdeduction').length )         // use this if you are using id to check
        {
            if ($('#otherdeduction').val() == '') {
                $("#otherdeduction").next("span").html('Enter Deductions').show('slow');
                error = 1;
            }
        }

        if( $('#tinno').length )         // use this if you are using id to check
        {
            if ($('#tinno').val() == '') {
                $("#tinno").next("span").html('Enter Values').show('slow');
                error = 1;
            }

        }
        if( $('#cstregno').length )         // use this if you are using id to check
        {
            if ($('#cstregno').val() == '') {
                $("#cstregno").next("span").html('Enter CST Reg no').show('slow');
                error = 1;
            }

        }
        if( $('#invoiceno').length )         // use this if you are using id to check
        {
            if ($('#invoiceno').val() == '') {
                $("#invoiceno").next("span").html('Enter Invoice no').show('slow');
                error = 1;
            }

        }
        /*if( $('#tax').length )         // use this if you are using id to check
        {

            if ($('#tax').val() == '') {
                $("#tax").next("span").html('Enter Tax').show('slow');
                error = 1;
            }

        }*/
        if ($('#billno').val() == '') {
            $("#billno").next("span").html('Enter Bill no').show('slow');
            error = 1;
        }

        if ($('#place').val() == '0') {
            $("#place").next("span").html('Select Place').show('slow');
            error = 1;
        }
        if ($('#projectid').val() == '0') {
            $("#projectid").next("span").html('Select Project').show('slow');
            error = 1;
        }
        if ($('#creditacnt').val() == '0') {
            $("#creditacnt").next("span").html('Select Party').show('slow');
            error = 1;
        }

        if (error == 0) {
            $.ajax({
                type:'POST',
                url:'../FinanceRequests/bills',
                dataType:"json"
            });
        }
        else {
            return false;
        }

    });
    $(document).on("change", "#billstatus", function () {
        var status = $('#billstatus').val();
        if (status == 1) {
            if ($('#accounthead').val() == '0') {
                $("#accounthead").next("span").html('Select Account head').show('slow');
                $('.error').show();
            }
            if ($('#accountheaddeduction').val() == '0') {
                $("#accountheaddeduction").next("span").html('Select Account head').show('slow');
                $('.error').show();
            }
        }
    });
    $('#saveandnewbill').click(function () {
        var error = 0;
        $('.error').hide();
        if ($('#Amount').val() == '') {
            $("#Amount").next("span").html('Enter Amount').show('slow');
            error = 1;
        }
        if ($('#Purpose').val() == '') {
            $("#Purpose").next("span").html('Enter Purpose').show('slow');
            error = 1;
        }
        if ($('#billno').val() == '') {
            $("#billno").next("span").html('Enter Bill no').show('slow');
            error = 1;
        }

        if (error == 0) {
            $.ajax({
                type:'POST',
                url:'../FinanceRequests/bills',
                dataType:"json"
            });
        }
        else {
            return false;
        }

    });

    $('#receiptapprove').click(function () {
        var error = 0;
        if ($('#receiptstatus').val() == '0') {
            $("#receiptstatus").next("span").html('Select Status').show('slow');
            error = 1;
        }
        if (error == 0) {
            $.ajax({
                type:'POST',
                url:'../FinanceRequests/Receiptapprove',
                dataType:"json"
            });
        }
        else {
            return false;
        }

    });
    $(document).on('click', '.deletebillsbutton', function () {
        var billid = $(this).val();
        var r = confirm("Are you sure you want to delete this Bill ?");
        if (r == true) {
            $.ajax({
                type:'POST',
                url:'../FinanceRequests/Billsdelete/',
                beforeSend:function () {
                    $('#deletebillsbutton' + billid).attr("disabled", true);
                },
                dataType:"json",
                data:{billid:billid},
                success:function (data) {
                    if (data.error == 'No') {
                        $('#billsrow' + data.Id).remove();
                        $('#listbills').trigger('click');
                    }
                    else {
                        alert(data.errortext);
                    }

                    $('#deletebillsbutton' + data.Id).attr("disabled", false);
                }
            });
        }
        else {
            return false;
        }
    });

    $(document).on("blur", ".billrate", function () {
        var itemid = $(this).attr('data-id');
        var rate = $(this).val() * 1;
        var prevquantity = $('#prev' + itemid).val() * 1;
        var quantity = $('#Quantity' + itemid).val() * 1;

        var prevquantityamount = $('#prevamount' + itemid).val() * 1;
        $('#totalquantity' + itemid).text((prevquantity + quantity).toFixed(2));
        var taxget = $('#Tax_Amount' + itemid).val() * 1;
        var tax=0;
        if(isNaN(taxget)){
            tax=0;
        }
        else{
            tax=taxget;
        }
        var amount = (rate * quantity)+tax;
        $('#netamount' + itemid).text(amount.toFixed(2));
        $('#Net_Amount' + itemid).val(amount.toFixed(2));
        $('#billamount' + itemid).text(amount.toFixed(2));
        $('#Amount' + itemid).val(amount.toFixed(2));
        $('#totalquantityamount' + itemid).text((prevquantityamount + amount).toFixed(2));
        $("#totalAmount" + itemid).val(prevquantityamount + amount);
        var totalrate = 0;
        $('.net-amount').each(function () {
            totalrate = totalrate + ($(this).text() * 1)
        });
        $('#billtotal').text(totalrate.toFixed(2));
        $('#biltot').val(totalrate.toFixed(2));
    });
    $(document).on("blur", ".billqty", function () {
        var itemid = $(this).attr('data-id');
        var quantity = $(this).val() * 1;

        var rate = $('#Rate' + itemid).val() * 1;
        var prevquantity = $('#prev' + itemid).val() * 1;
        var prevquantityamount = $('#prevamount' + itemid).val() * 1;
        $('#totalquantity' + itemid).text((prevquantity + quantity).toFixed(2));
        var taxget = $('#Tax_Amount' + itemid).val() * 1;
        var tax=0;
        if(isNaN(taxget)){
             tax=0;
        }
        else{
             tax=taxget;
        }
        var amount = (rate * quantity)+tax;
        $('#billamount' + itemid).text(amount.toFixed(2));
        $('#Amount' + itemid).val(amount.toFixed(2));

        $('#totalquantityamount' + itemid).text((prevquantityamount + amount).toFixed(2));
        $("#totalAmount" + itemid).val(prevquantityamount + amount);
        $('#netamount' + itemid).text(amount.toFixed(2));
        $('#Net_Amount' + itemid).val(amount.toFixed(2));
        var totalrate = 0;
        $('.net-amount').each(function () {
            totalrate = totalrate + ($(this).text() * 1)
        });
        $('#billtotal').text(totalrate.toFixed(2));
        $('#biltot').val(totalrate.toFixed(2));

    });
    $(document).on("blur", ".billtax", function () {
        var itemid = $(this).attr('data-id');
        var tax = $(this).val() * 1;
        var rate = $('#Rate' + itemid).val() * 1;
        var quantity = $('#Quantity' + itemid).val() * 1;
        var amount = (rate * quantity);
        var totalAmount = $('#prevamount' + itemid).val() * 1;
        var taxamount = amount + tax;
        $('#billamount' + itemid).text(taxamount.toFixed(2));
        $('#Amount' + itemid).val(taxamount.toFixed(2));
        $("#totalAmount" + itemid).val(totalAmount + amount + tax);
        $('#totalquantityamount' + itemid).text((totalAmount + amount + tax).toFixed(2));
        $('#netamount' + itemid).text(taxamount.toFixed(2));
        $('#Net_Amount' + itemid).val(taxamount.toFixed(2));
        var totalrate = 0;
        $('.net-amount').each(function () {
            totalrate = totalrate + ($(this).text() * 1)
        });
        $('#billtotal').text(totalrate.toFixed(2));
        $('#biltot').val(totalrate.toFixed(2));
    });

    $(document).on("change", ".resourcetype", function () {
        var itemid = $(this).attr('data-id');
        var restype = $(this).val();
        $.ajax({
            type:'POST',
            url:'../../FinanceRequests/resourcesearch',
            dataType:"json",
            data:{restype:restype},
            success:function (data) {
                if (data.error == 'No') {
                    var option = data.result;
                    $("#resitem" + itemid).empty().append(option);
                }
                else {
                    alert(data.errortext);
                }

            }
        });
    });
    $(document).on("change", "#place", function () {
        var resourcegroup = $(this).val();
        var projectid = $('#place').val();
        //var requestid = $(this).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/GetVendors',
            dataType: "json",
            data: {projectid: projectid},
            success: function (data) {
                if (data.error == 'No') {
                    var option = data.result;
                    $("#party").empty().append(option);

                }
                else {
                    alert(data.errortext);
                }

            }
        });
    });
    $(document).on("change", "#party", function () {
        var vendor = $(this).val();
        var projectid = $('#place').val();
        //var requestid = $(this).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/GetPurchaseOrders',
            dataType: "json",
            data: {projectid: projectid,vendor:vendor},
            success: function (data) {
                if (data.error == 'No') {
                    var option = data.result;
                    $("#PO_No").empty().append(option);

                }
                else {
                    alert(data.errortext);
                }

            }
        });
    });
    $(document).on("change", "#PO_No", function () {
        var poid = $(this).val();
        var projectid = $('#place').val();
        //var requestid = $(this).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/GetResourcename',
            dataType: "json",
            data: {poid: poid},
            success: function (data) {
                if (data.error == 'No') {
                    var option = data.result;
                    $("#PurchasePurpose0").empty().append(option);

                }
                else {
                    alert(data.errortext);
                }

            }
        });
    });
    $(document).on("change", ".purchasepurpose", function () {
        var resourceid = $(this).val();
        var itemid = $(this).attr('data-id');
        var projectid = $('#place').val();
        var poid = $('#PO_No').val();
        //var requestid = $(this).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/GetResourcedetails',
            dataType: "json",
            data: {poid: poid,resourceid: resourceid},
            success: function (data) {
                if (data.error == 'No') {
                    //$('#Unit0').val(data.unit);
                    $('#Rate'+ itemid).val(data.rate);
                    //$('#Quantity'+ itemid).attr("placeholder", "Max :" + data.max);
                    $('#Quantity'+ itemid).attr("max", data.max);

                }
                else {
                    alert(data.errortext);
                }

            }
        });
    });
    $(document).on("blur", ".billpurpose", function () {
        var itemid = $(this).attr('data-id');
        var itemname = $(this).val();
        var prjctid = $('#projectid').val();
        var party = $('#accountid').val();

        $.ajax({
            type:'POST',
            url:'../FinanceRequests/projectItemSearch',
            dataType:"json",

            data:{projectid:prjctid, item:itemname, party:party},
            success:function (data) {
                $('#prevquantity' + itemid).text(data.totquantity.toFixed(2));
                $("#prev" + itemid).val(data.totquantity);
                $('#prevquantityamount' + itemid).text(data.totamount.toFixed(2));
                $("#prevamount" + itemid).val(data.totamount);

            }
        });

        $('.net-amount').each(function () {
            totalrate = totalrate + ($(this).text() * 1)
        });
        $('#billtotal').text(totalrate.toFixed(2));
    });
    $(document).on("change", "#placeworkbill", function () {

        var projectid = $(this).val();
        //var projectid = $('#place').val();
        //var requestid = $(this).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/GetWorkordervendors',
            dataType: "json",
            data: {projectid: projectid},
            success: function (data) {
                if (data.error == 'No') {
                    var option = data.result;
                    $("#partyforwork").empty().append(option);

                }
                else {
                    alert(data.errortext);
                }

            }
        });
    });
    $(document).on("change", "#partyforwork", function () {
        var vendor = $(this).val();
        var projectid = $('#placeworkbill').val();
        //var requestid = $(this).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/GetWorkOrders',
            dataType: "json",
            data: {projectid: projectid,vendor:vendor},
            success: function (data) {
                if (data.error == 'No') {
                    var option = data.result;
                    $("#WO_No").empty().append(option);

                }
                else {
                    alert(data.errortext);
                }

            }
        });
    });
    $(document).on("change", "#WO_No", function () {
        var poid = $(this).val();
        var projectid = $('#place').val();
        //var requestid = $(this).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/GetItemname',
            dataType: "json",
            data: {poid: poid},
            success: function (data) {
                if (data.error == 'No') {
                    var option = data.result;
                    $("#PurchasePurpose0").empty().append(option);

                }
                else {
                    alert(data.errortext);
                }

            }
        });
    });
    $(document).on("change", ".workpurpose", function () {
        var resourceid = $(this).val();
        var itemid = $(this).attr('data-id');
        var projectid = $('#partyforwork').val();
        var poid = $('#WO_No').val();
        //var requestid = $(this).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/GetItemDetails',
            dataType: "json",
            data: {poid: poid,resourceid: resourceid},
            success: function (data) {
                if (data.error == 'No') {
                    $('#Unit'+itemid).val(data.unit);
                    $('#Rate'+ itemid).val(data.rate);
                    $('#tax').val(data.servicetax);
                    $('#tds').val(data.tds);
                    $("#prev" + itemid).val(data.billquantity);
                    var quantity=parseFloat(data.billquantity);
                    var prevamount=quantity*parseFloat(data.rate);

                    $('#prevquantity' + itemid).text((quantity).toFixed(2));

                    $('#prevquantity' + itemid).text(quantity.toFixed(2));
                    $("#prev" + itemid).val(quantity);
                    $('#prevquantityamount' + itemid).text((prevamount).toFixed(2));
                    $("#prevamount" + itemid).val((prevamount));

                    $('#retention').val(data.retention);
                    $('#vatid').val(data.vat);
                    $('#taxpercent').text(data.servicetax+'(%)');
                    $('#tdspercent').text(data.tds+'(%)');
                    $('#vatpercent').text(data.vat+'(%)');
                    $('#retpercent').text(data.retention+'(%)');
                    //$('#Quantity'+ itemid).attr("placeholder", "Max :" + data.max);
                    $('#Quantity'+ itemid).attr("max", data.max);

                }
                else {
                    alert(data.errortext);
                }

            }
        });
    });


});