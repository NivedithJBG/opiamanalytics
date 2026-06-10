/**
 * Created by SolmindsDelli5 on 28-09-2017.
 */
$(document).on( "click", "#invoice", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    $('#invoicesearch').trigger('click') ;
});
$(function() {
    $('#invoicesearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/Invoicesearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#invoiceitems').html(data.result);
                    $('#invoicetable').show();
                }
                $('.preloader').hide();
            }
        });
    });
});

$(document).on('click','input',function(){
    var invoice = [];
    var vendor = [];
    var ordertype = [];
    var project = [];
    var orders = [];
    $('input:checked').each(function() {
        invoice.push($(this).val());
        vendor.push($(this).attr('data-vendor'));
        ordertype.push($(this).attr('data-type'));
        project.push($(this).attr('data-project'));
        orders.push($(this).attr('data-id'));
    });
    $('[name="crvendor"]').attr({value: vendor.join(', ')});
    $('[name="crinvoices"]').attr({value: invoice.join(', ')});
    $('[name="crordertype"]').attr({value: ordertype.join(', ')});
    $('[name="crproject"]').attr({value: project.join(', ')});
    $('[name="crorders"]').attr({value: orders.join(', ')});
});

$(document).on('click','.createjournal',function(){
    if($('.creditvendors').is(":checked")) {
        var selvendors=$('#crvendor').val();
        var selcrordertype=$('#crordertype').val();
        var selproject=$('#crproject').val();
        var arr = selvendors.split(',');
        var crordertype = selcrordertype.split(',');
        var project = selproject.split(',');
        var sorted_arr = arr.slice().sort(); // You can define the comparing function here.
        var crordertype_arr = crordertype.slice().sort(); // You can define the comparing function here.
        var project_arr = project.slice().sort(); // You can define the comparing function here.
        var count='';
        for (var i = 0; i < arr.length; i++) {
            if (parseInt(arr[i]) == parseInt(sorted_arr[i])) {
                count=0;
            }
            else {
                count++;
            }
        }
        var crordertypecount='';
        for (var i = 0; i < crordertype.length; i++) {
            if (parseInt(crordertype[i]) == parseInt(crordertype_arr[i])) {
                crordertypecount=0;
            }
            else {
                crordertypecount++;
            }
        }
        var projectcount='';
        for (var i = 0; i < project.length; i++) {
            if (parseInt(project[i]) == parseInt(project_arr[i])) {
                projectcount=0;
            }
            else {
                projectcount++;
            }
        }

        if(count!=0){
            alert('Please select only one vendor');
            return false;
        }
        if(crordertypecount!=0){
            alert('Please select only one order type');
            return false;
        }
        if(projectcount!=0){
            alert('Please select only one project');
            return false;
        }

    }
    else {
        alert('Please select any vendor before proceeding.');
        return false;
    }
});

$(document).on( "click", ".deleteinvoice", function(){
    var idval=$(this).val();
    var type=$(this).attr('data-type');
    var r = confirm("Are you sure you want to delete this Invoice ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/Deleteinvoice',
            beforeSend : function(){
                $('#deleteinvoice'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {invoiceid:idval,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#invoicerow'+idval).remove();
                }

                $('#deleteinvoice'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
