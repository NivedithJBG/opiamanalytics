// $(function(){
//     var type = window.location.hash.substr(1);
//     if(type=='Logistics')
//     {
//         $('.acc_trigger').removeClass('active').next().slideUp();
//         $('#Logistics').addClass('active').next('.acc_container').slideDown();
//         $('#logisticsaddsection').slideUp('slow');// slide down the project listing div
//         $('#logisticslistsection').slideDown('slow');// slide down the project listing div
//         $('#listlogistics').removeClass('btn-danger').addClass('btn-success');
//         $('#addlogistics').removeClass('btn-success').addClass('btn-danger');
//         $.ajax({
//             type: 'POST',
//             url: '../logistics/search',
//             beforeSend : function(){
//                 $('.preloader').show();
//             },
//             dataType: "json",
//             data: {logisticsname:$('#searchlogisticsname').val(),projectid:$('#selectedProjectId').val()},
//             success: function(data){
//                 if(data.error=='No')
//                 {
//                     $('#logisticsitems').html(data.result);
//                     $('#logisticstable').show();
//                 }
//                 else
//                 {
//                     alert(data.errortext);
//                 }
//                 $('.preloader').hide();
//             }
//         });
//     }
// });

$(document).on( "click", ".viewlogistics", function(){
    //$('.acc_container').slideUp();
    $('#project').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#Logistics').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#logdispprojectname').html(getProjectname(id));
    $('#selectedProjectId').val(id);
    $('#listlogistics').trigger('click');
});

$(document).on( "click", "#Logistics", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
        $('#listlogistics').trigger('click') ;
});
$(function(){
    $('#listlogistics').click(function(){
        $('#logisticsaddsection').slideUp('slow');// slide down the project listing div
        $('#logisticslistsection').slideDown('slow');// slide down the project listing div
        $('#listlogistics').removeClass('btn-danger').addClass('btn-success');
        $('#addlogistics').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../logistics/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {logisticsname:$('#searchlogisticsname').val(),projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#logisticsitems').html(data.result);
                    $('#logisticstable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });

    $('#logisticssearch').click(function(){
        $('#listlogistics').trigger('click');
    })
    /*$('#addlogistics').click(function(){
        $('#logisticslistsection').slideUp('slow');// slide down the project listing div
        $('#logisticsaddsection').slideDown('slow');// slide down the project listing div
        $('#addlogistics').removeClass('btn-danger').addClass('btn-success');
        $('#listlogistics').removeClass('btn-success').addClass('btn-danger');
        $('.error').hide();

    });*/
    $('#savelogistics').click(function(){
        var error=0;
        $('.error').hide();
        if($('#logisticsname').val()=='')
        {
            $("#logisticsname").next("span").html('Enter Logistics Name').show('slow');
            error=1;
        }
        if($('#logisticsname').val()!='' && LogisticsNameExists($('#logisticsname').val(),$('#selectedProjectId').val())=='Yes')
        {
            $('#logisticsname').next("span").html('Logistics Name Exists').show('slow')
            error=1;
        }
        if($('#logisticsunit').val()=='')
        {
            $("#logisticsunit").next("span").html('Enter Logistics Unit').show('slow');
            error=1;
        }
        if($('#logisticsqty').val()=='')
        {
            $("#logisticsqty").next("span").html('Enter Logistics Quantity').show('slow');
            error=1;
        }

        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../logistics/create',
                beforeSend : function(){
                    $('#savelogistics').attr("disabled", true);
                },
                dataType: "json",
                data: {Project_Id:$('#selectedProjectId').val(),logisticsname:$('#logisticsname').val(),logisticsunit:$('#logisticsunit').val(),logisticsqty:$('#logisticsqty').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#addlogisticsform')[0].reset();
                        $('#addlogistics').trigger('click');


                    }
                    else
                    {
                        $("#logisticsname").next("span").html(data.errortext).show('slow');
                        $('#savelogistics').attr("disabled", false);
                    }
                    $('#savelogistics').attr("disabled", false);
                }
            });
        }
    });
     $( "#logisticsitems" ).sortable({ 
        items: '.no',
        update:function( event, ui ) {
            //alert($(this).index());
            var updatedrows=[];
            $(this).closest('table').find('tbody tr').each(function (i) {
                var rowid=$(this).attr('data-id');
                var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    rowindex:rowindex
                })
            });
            $.ajax({
                type: 'POST',
                url: '../logistics/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){
                    if(data.error=='No')
                    {
                       $('#listlogistics').trigger('click');
                    }

                }
            });
        }

    }).disableSelection()
});
$(document).on('click','.editlogisticsbutton',function(){
    var idval=$(this).val();
    $('#logisticsname'+idval).hide();
    $('#logisticsunit'+idval).hide();
    $('#editlogisticsbut'+idval).hide();
    $('#editlogisticsname'+idval).show();
    $('#editlogisticsunit'+idval).show();
    $('#savelogisticsbutton'+idval).show();
});
$(document).on('click','.savelogisticsbutton',function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editlogisticsname'+idval).val()=='')
    {
        $('#editlogisticsname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if($('#editlogisticsunit'+idval).val()=='')
    {
        $('#editlogisticsunit'+idval).next("span").html('Enter Unit').show('slow');
        error=1;
    }
  
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../logistics/ActUpdate',
            beforeSend : function(){
                $('#savelogisticsbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {id:idval,name:$('#editlogisticsname'+idval).val(),unit:$('#editlogisticsunit'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#logisticsname'+data.Id).show();
                    $('#logisticsunit'+data.Id).show();
                    $('#logisticsqty'+data.Id).show();
                    $('#editlogisticsbut'+data.Id).show();
                    $('#editlogisticsname'+data.Id).hide();
                    $('#editlogisticsunit'+data.Id).hide();
                    $('#editlogisticsqty'+data.Id).hide();
                    $('#savelogisticsbutton'+data.Id).hide();
                    $('#editlogisticsname'+data.Id).val(data.Name);
                    $('#editlogisticsunit'+data.Id).val(data.Unit);
                    $('#editlogisticsqty'+data.Id).val(data.Qty);
                    $('#editlogisticsamount'+data.Id).val(data.Amount);
                    $('#logisticsname'+data.Id).text(data.Name);
                    $('#logisticsunit'+data.Id).text(data.Unit);
                    $('#logisticsqty'+data.Id).text(data.Qty);
                    $('#logisticsamount'+data.Id).text(data.Amount);

                }
                else
                {
                    alert(data.errortext);
                }

                $('#savelogisticsbutton'+data.Id).attr("disabled", false);
            }
        });
    }
});
$(document).on('click','.deletelogistics',function(){
    var id=$(this).val();
    var r = confirm("Are you sure you want to delete this Logistics ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../logistics/delete/'+id,
            async:false,
            dataType:"json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#listlogistics').trigger('click');
                }
            }
        });
    }
});

/*$(document).on('click','#addlogistics',function(){

    window.location.href="../logistics/create?projectid="+ $('#selectedProjectId').val() ;
});*/

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
function LogisticsNameExists(name,project)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../logistics/checkname',
        async:false,
        data: {name:name,project:project},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}
