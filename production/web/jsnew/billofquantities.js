$(document).on( "click", ".billsquantity", function(){

    //$('.acc_container').slideUp();

    $('#project').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

    //$(this).toggleClass('active').next().slideDown();

    $('#billsofquantity').addClass('active').next('.acc_container').slideDown();

    var id= $(this).val();

    $('#dispprojectnamebills').html(getProjectname(id));

    $('#selectedProjectId').val(id);

    $('#billstable').hide();

    $('#billstypeinfo').hide();

    $('#billsinfo').hide();

    $('#print').hide();

    $('#billselectiontable').show();



});

$(document).on( "click", "#billsofquantity", function(){

    $('#billsofquantity').addClass('active').next('.acc_container').slideUp();

    $('#project').removeClass('active').next().slideDown(); //Remove all .acc_trigger classes and slide up the immediate next container

});



$(function(){

    $('#billofquantity').click(function(){

        $('#billselection').val(1);

        $('#billsquantitysearch').trigger('click') ;

    });

    $('#billofestimate').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');



        $.ajax({

            type: 'POST',

            url: '../Projects/Billsestimatesearch',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {name:$('#billsquantitysearchdata').val(),projectid:$('#selectedProjectId').val(),billtype:$('#billselection').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);

                    $('#print').html(data.print);

                    $('#billstable').show();

                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Project Estimates for '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });
    
    $('#activitycost').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');

        $('#billselectiontable').hide();
        $('#billstable').show();

        $.ajax({

            type: 'POST',

            url: '../Projects1/Activitycost',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:$('#selectedProjectId').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);

                    $('#billstable').show();

                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Project Value for '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });
    
    $('#activityactualcost').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');

        $('#billselectiontable').hide();
        $('#billstable').show();

        $.ajax({

            type: 'POST',

            url: '../Projects1/Activityactualcost',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:$('#selectedProjectId').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);

                    $('#billstable').show();

                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('IOW Actual Cost for '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#actvtyactualcost').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');

        $('#billselectiontable').hide();
        $('#billstable').show();

        $.ajax({

            type: 'POST',

            url: '../Projects1/ActivityResourcewisecost',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:$('#selectedProjectId').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);



                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Activity Actual Cost for '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#actvtybasedcost').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');

        $('#billselectiontable').hide();
        $('#billstable').show();

        $.ajax({

            type: 'POST',

            url: '../Projects1/Activitybasedcost',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:$('#selectedProjectId').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);



                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Activity Based Cost for '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#restype_actualcost').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');

        $('#billselectiontable').hide();
        $('#billstable').show();

        $.ajax({

            type: 'POST',

            url: '../Projects1/Resourcetypeavgrate',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:$('#selectedProjectId').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);



                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Resource Type Avg Rate for '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#activity_amount').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');

        $('#billselectiontable').hide();
        $('#billstable').show();

        $.ajax({

            type: 'POST',

            url: '../Projects1/Resourceactivityamount',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:$('#selectedProjectId').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);



                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Activity Amount '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#weekly_target').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');

        $('#billselectiontable').hide();
        $('#billstable').show();

        $.ajax({

            type: 'POST',

            url: '../Report/ScheduleWeeklytarget',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:$('#selectedProjectId').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);



                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Ongoing Activities '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#due_start').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');

        $('#billselectiontable').hide();
        $('#billstable').show();

        $.ajax({

            type: 'POST',

            url: '../Report/ScheduleDuestart',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:$('#selectedProjectId').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);



                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Activities due for this week '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#overdue_act').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');

        $('#billselectiontable').hide();
        $('#billstable').show();

        $.ajax({

            type: 'POST',

            url: '../Report/ScheduleOverdue',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:$('#selectedProjectId').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);



                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Overdue Activities '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#resource_group').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');

        $('#billselectiontable').hide();
        $('#billstable').show();

        $.ajax({

            type: 'POST',

            url: '../Projects1/Resourcegroupwise',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:$('#selectedProjectId').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);



                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Resource Group '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#estimateiow').click(function(){



        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');



        $.ajax({

            type: 'POST',

            url: '../Projects/Billsestimateiow',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {name:$('#billsquantitysearchdata').val(),projectid:$('#selectedProjectId').val(),billtype:$('#billselection').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);

                    $('#print').html(data.print);

                    $('#billstable').show();

                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Bill Of Materials for '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#billofmaterial').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');



        $.ajax({

            type: 'POST',

            url: '../Projects/Billsmaterialssearch',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {name:$('#billsquantitysearchdata').val(),projectid:$('#selectedProjectId').val(),billtype:$('#billselection').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);

                    $('#print').html(data.print);

                    $('#billstable').show();

                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Bill Of Materials for '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#billsquantitysearch').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');



        $.ajax({

            type: 'POST',

            url: '../Projects/Billsquantitysearch',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {name:$('#billsquantitysearchdata').val(),projectid:$('#selectedProjectId').val(),billtype:$('#billselection').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);

                    $('#print').html(data.print);

                    $('#billstable').show();

                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Bill Of Quantities for '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#billofquantityiow').click(function(){

        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');



        $.ajax({

            type: 'POST',

            url: '../Projects/Billsqtyiow',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:$('#selectedProjectId').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);

                    $('#print').html(data.print);

                    $('#billstable').show();

                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Bill Of Quantities - IOW for '+getProjectname(id));

                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();
                    $('.preloader').hide();

                }

                else

                {

                    alert(data.errortext);

                }



            }

        });

    });

	$('#procashflow').click(function(){



        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');



        $.ajax({

            type: 'POST',

            url: '../Projects/projectcashflow',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:$('#selectedProjectId').val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id=$('#selectedProjectId').val();

                    $('#billitems').html(data.result);

                    $('#print').html(data.print);

                    $('#billstable').show();

                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#billstypeinfo').html('Project Cash Flow for '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#ratevariance').click(function(){

        var projectid=$('#selectedProjectId').val();

        var url='../Projects/VariationReport/'+projectid;

        window.location.href = url;

    });

    $('#quantityvariance').click(function(){

        var projectid=$('#selectedProjectId').val();

        var url='../Projects/SiteReport/'+projectid;

        window.location.href = url;

    });

    $('#costvariance').click(function(){

        var projectid=$('#selectedProjectId').val();

        var url='../Projects/Costvariation/'+projectid;

        window.location.href = url;

    });

    $(document).on("change", "#vendor", function () {

        //var itemid = $(this).attr('data-id');



        $('#billsquantitysearch').trigger('click');



    });

    $('#Setupsearch').click(function(){

        $('#billsquantitysearch').trigger('click');

    });

    $('#resourcesearchsetup').click(function(){

        var toshow=$('#selecttype').val();

        var searchval=$('#searchname').val();

        $.ajax({

            type: 'POST',

            url: '../ProjectSetup/resourcesearch',

            beforeSend : function(){

                $('#resourcesearchsetup').attr("disabled", true);

                $('.preloader').show();

            },

            dataType: "json",

            data: {resourcetype:toshow,resourcename:searchval},

            success: function(data){

                if(data.error=='No')

                {

                    $('#resourceitems').html(data.result);

                    $('#resourcetable').show();



                }

                else

                {

                    alert(data.errortext);

                }



                $('#resourcesearchsetup').attr("disabled", false);

                $('.preloader').hide();

            }

        });

    });

    /**

    Added by karthik on 21-12-2016 for corporate office cash flow

    */

    $('#cocashflow').click(function(){



        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div

        $('#billsquantitysearch').removeClass('btn-danger').addClass('btn-success');

        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../Projects/corporatecashflow',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:'12'},

            success: function(data){

                if(data.error=='No')

                {

                    var id='12';

                    $('#billitems').html(data.result);

                    $('#print').html(data.print);

                    $('#billstable').show();

                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#quarterdiv').show();

                    $('#quarterselector').val(data.quarter);                    

                    $('#billstypeinfo').html('Cash Flow for '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });

    $('#quarterselector').change(function(){

        $.ajax({

            type: 'POST',

            url: '../Projects/corporatecashflow',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {projectid:'12',quarter:$(this).val()},

            success: function(data){

                if(data.error=='No')

                {

                    var id='12';

                    $('#billitems').html(data.result);

                    $('#print').html(data.print);

                    $('#billstable').show();

                    $('#print').show();

                    $('#billsinfo').show();

                    $('#dispprojectnamebills').hide();

                    $('#quarterdiv').show();

                    $('#quarterselector').val(data.quarter);                    

                    $('#billstypeinfo').html('Cash Flow for '+getProjectname(id));



                    $('#billstypeinfo').show();

                    $('#billselectiontable').hide();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });        

    })   

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