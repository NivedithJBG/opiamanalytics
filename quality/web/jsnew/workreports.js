/**
 * Created by SolmindsDelli5 on 03-08-2016.
 */
$(document).on("click", "#workreport", function () {

    if (!$(this).next().is(':hidden')) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if ($(this).next().is(':hidden')) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    //$('#listrequest').trigger('click');
    $('#listworkreports').trigger('click');
});
$(function () {
    $('#listworkreports').click(function () {

        $('#worklistsection').slideDown('slow');// slide down the project listing div
        $('#listrequest').removeClass('btn-danger').addClass('btn-success');
        $('#addrequest').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Operations/workreportsearch',
            beforeSend: function () {
                $('.preloader').show();
            },
            dataType: "json",
            data: {name: $('#searchrequest').val()},
            success: function (data) {
                if (data.error == 'No') {
                    $('#Workitems').html(data.result);
                    $('#worktable').show();
                }
                else {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#worksearch').click(function () {
        $('#listworkreports').trigger('click');
    });
});
$(document).on("change", "#project", function () {
    //var itemid = $(this).attr('data-id');
    var resourcegroup = $(this).val();
    var projectid = $('#project').val();
    var requestid = $(this).attr('data-id');
    $.ajax({
        type: 'POST',
        url: '../Operations/GetSubcontractorsname',
        dataType: "json",
        data: {projectid: projectid},
        success: function (data) {
            if (data.error == 'No') {
                var option = data.result;
                $("#vendor").empty().append(option);

            }
            else {
                alert(data.errortext);
            }

        }
    });
});
$(document).on("change", "#vendor", function () {
    //var itemid = $(this).attr('data-id');
    var resourcegroup = $(this).val();
    var projectid = $('#project').val();
    var vendoridid = $('#vendor').val();
    var requestid = $(this).attr('data-id');
    $.ajax({
        type: 'POST',
        url: '../Operations/GetWOName',
        dataType: "json",
        data: {projectid: projectid, vendoridid: vendoridid},
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
    //var itemid = $(this).attr('data-id');
    var resourcegroup = $(this).val();

    var poid = $('#PO_No').val();
    var requestid = $(this).attr('data-id');
    $.ajax({
        type: 'POST',
        url: '../Operations/GetItemname',
        dataType: "json",
        data: {poid: poid},
        success: function (data) {
            if (data.error == 'No') {
                var option = data.result;
                $("#iowid0").empty().append(option);

            }
            else {
                alert(data.errortext);
            }

        }
    });
});
$(document).on("change", "#iowid0", function () {
    //var itemid = $(this).attr('data-id');
    var resourcegroup = $(this).val();

    var poid = $('#PO_No').val();

    var resourceid = $('#iowid0').val();
    var requestid = $(this).attr('data-id');
    $.ajax({
        type: 'POST',
        url: '../Operations/GetItemDetails',
        dataType: "json",
        data: {poid: poid, resourceid: resourceid},
        success: function (data) {
            if (data.error == 'No') {
                $('#Unit0').val(data.unit);
                $('#Rate0').val(data.rate);
                $("#Rate0").prop("readonly", true);
                $('#Unit0').trigger('blur');
                $('#Rate0').trigger('blur');

                $('#Quantity0').attr("placeholder", "Max :" + data.max);
                $('#Quantity0').attr("max", data.max);

            }
            else {
                alert(data.errortext);
            }

        }
    });
});
$(document).on( "blur","#Quantity0", function(){
    var rate=$('#Quantity0').val();
    var quantity=$('#Rate0').val();
    $('#Amount0').val(rate*quantity);

});
$(document).on( "blur","#Rate0", function(){
    var rate=$('#Quantity0').val();
    var quantity=$('#Rate0').val();
    $('#Amount0').val(rate*quantity);

});
$(document).on('click','.deleteworkreportbutton',function(){
    var reqid=$(this).val();
    var r = confirm("Are you sure you want to delete this Request ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../Operations/Deleteworkreport/',
            beforeSend : function(){
                $('#deletefundrqstbutton'+reqid).attr("disabled", true);
            },
            dataType: "json",
            data: {reqid:reqid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#requestrow'+data.Id).remove();
                    $('#listrequest').trigger('click');
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deletefundrqstbutton'+data.Id).attr("disabled", false);
            }
        });
    }
    else {
        return false;
    }
});